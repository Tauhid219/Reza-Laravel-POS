<?php

use App\Models\CashRegister;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Unit;
use App\Models\User;

test('authenticated user can view POS terminal and search products', function () {
    $user = User::where('email', 'admin@gmail.com')->first() ?? User::factory()->create();

    $response = $this->actingAs($user)->get(route('pos.index'));
    $response->assertStatus(200);
    $response->assertSee('POS Terminal');
    $response->assertSee('Point of Sale (POS)');

    $searchResponse = $this->actingAs($user)->getJson(route('pos.search', ['query' => 'Oil']));
    $searchResponse->assertStatus(200);
});

test('pos checkout successfully creates order, deducts stock, and records payment', function () {
    $user = User::where('email', 'admin@gmail.com')->first() ?? User::factory()->create();

    $unit = Unit::first() ?? Unit::create(['name' => 'Piece', 'short_name' => 'pc']);
    $category = Category::first() ?? Category::create(['name' => 'General', 'slug' => 'general']);

    $product = Product::create([
        'category_id' => $category->id,
        'unit_id' => $unit->id,
        'name' => 'POS Test Biscuits',
        'code' => 'PTEST'.rand(1000, 9999),
        'cost_price' => 40.00,
        'selling_price' => 50.00,
        'stock_quantity' => 100,
        'status' => true,
    ]);

    $customer = Customer::first() ?? Customer::create([
        'name' => 'Walk-in Customer',
        'is_walk_in' => true,
    ]);

    // Open a register for the user
    $register = CashRegister::create([
        'user_id' => $user->id,
        'opened_at' => now(),
        'cash_in_hand' => 1000.00,
        'status' => 'open',
    ]);

    $checkoutPayload = [
        'customer_id' => $customer->id,
        'items' => [
            [
                'product_id' => $product->id,
                'quantity' => 2,
                'price' => 50.00,
                'discount' => 0,
            ]
        ],
        'subtotal' => 100.00,
        'discount_type' => 'fixed',
        'discount_amount' => 0,
        'tax_percentage' => 0,
        'tax_amount' => 0,
        'total_amount' => 100.00,
        'paid_amount' => 100.00,
        'payment_type' => 'cash',
        'note' => 'Test checkout flow',
    ];

    $response = $this->actingAs($user)->postJson(route('pos.checkout'), $checkoutPayload);

    $response->assertStatus(201);
    $response->assertJson([
        'success' => true,
        'total_amount' => 100,
        'paid_amount' => 100,
        'due_amount' => 0,
    ]);

    // Verify stock deducted: 100 - 2 = 98
    $product->refresh();
    expect((float) $product->stock_quantity)->toEqual(98.00);

    // Verify order exists in DB
    $order = Order::where('customer_id', $customer->id)->latest()->first();
    expect($order)->not->toBeNull();
    expect((float) $order->total_amount)->toEqual(100.00);
    expect((float) $order->total_cost)->toEqual(80.00); // 40 * 2
    expect($order->payment_status)->toEqual('paid');
    expect($order->items()->count())->toEqual(1);
    expect($order->payments()->count())->toEqual(1);

    // Verify Cash Register cash sales incremented
    $register->refresh();
    expect((float) $register->cash_sales)->toEqual(100.00);
});

test('pos credit/partial checkout updates customer due balance', function () {
    $user = User::where('email', 'admin@gmail.com')->first() ?? User::factory()->create();

    $unit = Unit::first() ?? Unit::create(['name' => 'Piece', 'short_name' => 'pc']);
    $category = Category::first() ?? Category::create(['name' => 'General', 'slug' => 'general']);

    $product = Product::create([
        'category_id' => $category->id,
        'unit_id' => $unit->id,
        'name' => 'Credit Test Item',
        'code' => 'CTEST'.rand(1000, 9999),
        'cost_price' => 80.00,
        'selling_price' => 100.00,
        'stock_quantity' => 50,
        'status' => true,
    ]);

    $creditCustomer = Customer::create([
        'name' => 'Credit Test Customer',
        'phone' => '01511223344',
        'total_due' => 0,
        'is_walk_in' => false,
    ]);

    $checkoutPayload = [
        'customer_id' => $creditCustomer->id,
        'items' => [
            [
                'product_id' => $product->id,
                'quantity' => 1,
                'price' => 100.00,
                'discount' => 0,
            ]
        ],
        'subtotal' => 100.00,
        'total_amount' => 100.00,
        'paid_amount' => 40.00, // 60 due
        'payment_type' => 'credit',
        'note' => 'Partial credit sale',
    ];

    $response = $this->actingAs($user)->postJson(route('pos.checkout'), $checkoutPayload);

    $response->assertStatus(201);
    $response->assertJson([
        'success' => true,
        'due_amount' => 60,
    ]);

    $creditCustomer->refresh();
    expect((float) $creditCustomer->total_due)->toEqual(60.00);
});

test('authenticated user can view order list, show, thermal receipt, and a4 invoice', function () {
    $user = User::where('email', 'admin@gmail.com')->first() ?? User::factory()->create();

    $order = Order::latest()->first();

    // 1. Orders List
    $indexResponse = $this->actingAs($user)->get(route('orders.index'));
    $indexResponse->assertStatus(200);
    $indexResponse->assertSee('Sales History');

    // 2. Due Orders List
    $dueResponse = $this->actingAs($user)->get(route('orders.due'));
    $dueResponse->assertStatus(200);
    $dueResponse->assertSee('Outstanding Due');

    if ($order) {
        // 3. Order Details
        $showResponse = $this->actingAs($user)->get(route('orders.show', $order));
        $showResponse->assertStatus(200);
        $showResponse->assertSee($order->invoice_no);

        // 4. Thermal Receipt
        $receiptResponse = $this->actingAs($user)->get(route('orders.receipt', $order));
        $receiptResponse->assertStatus(200);
        $receiptResponse->assertSee($order->invoice_no);
        $receiptResponse->assertSee('REZA POS & SUPER STORE');

        // 5. A4 Invoice
        $invoiceResponse = $this->actingAs($user)->get(route('orders.invoice', $order));
        $invoiceResponse->assertStatus(200);
        $invoiceResponse->assertSee($order->invoice_no);
        $invoiceResponse->assertSee('TAX INVOICE', false);
    }
});

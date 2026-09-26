<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;

test('authenticated user can view suppliers page and create supplier', function () {
    $user = User::where('email', 'admin@gmail.com')->first() ?? User::factory()->create();

    $response = $this->actingAs($user)->get(route('suppliers.index'));
    $response->assertStatus(200);
    $response->assertSee('Manage Suppliers');

    $postResponse = $this->actingAs($user)->post(route('suppliers.store'), [
        'name' => 'City Wholesale Co.',
        'company_name' => 'City Group',
        'phone' => '01511223399',
        'email' => 'city@example.com',
        'address' => 'Kawran Bazar, Dhaka',
    ]);

    $postResponse->assertRedirect(route('suppliers.index'));
    $this->assertDatabaseHas('suppliers', ['phone' => '01511223399']);
});

test('authenticated user can create stock purchase and stock quantity increments', function () {
    $user = User::where('email', 'admin@gmail.com')->first() ?? User::factory()->create();
    $supplier = Supplier::firstOrCreate(['phone' => '01700998877'], ['name' => 'Test Supplier']);
    $unit = Unit::firstOrCreate(['name' => 'Piece'], ['short_name' => 'pc']);
    $category = Category::firstOrCreate(['slug' => 'test-cat'], ['name' => 'Test Cat']);

    $product = Product::firstOrCreate(
        ['code' => 'PUR-TEST-PROD-1'],
        [
            'name' => 'Procurement Test Item',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'cost_price' => 100,
            'selling_price' => 150,
            'stock_quantity' => 10,
            'alert_quantity' => 5,
        ]
    );

    $initialStock = (float) $product->stock_quantity;
    $purchaseQty = 25.00;
    $newCostPrice = 95.00;

    $purchaseData = [
        'supplier_id' => $supplier->id,
        'purchase_date' => date('Y-m-d'),
        'paid_amount' => 1000.00,
        'notes' => 'Test stock in invoice',
        'items' => [
            [
                'product_id' => $product->id,
                'quantity' => $purchaseQty,
                'cost_price' => $newCostPrice,
            ],
        ],
    ];

    $response = $this->actingAs($user)->post(route('purchases.store'), $purchaseData);

    $purchase = Purchase::latest()->first();
    $response->assertRedirect(route('purchases.show', $purchase));

    // Verify stock incremented
    $product->refresh();
    expect((float) $product->stock_quantity)->toEqual($initialStock + $purchaseQty);
    expect((float) $product->cost_price)->toEqual($newCostPrice);

    // Verify financial calculation
    $expectedTotal = $purchaseQty * $newCostPrice; // 2375.00
    expect((float) $purchase->total_amount)->toEqual($expectedTotal);
    expect((float) $purchase->paid_amount)->toEqual(1000.00);
    expect((float) $purchase->due_amount)->toEqual($expectedTotal - 1000.00);
    expect($purchase->payment_status)->toBe('partial');
});

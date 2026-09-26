<?php

use App\Models\CashRegister;
use App\Models\Customer;
use App\Models\User;

test('authenticated user can view customers and create customer', function () {
    $user = User::where('email', 'admin@gmail.com')->first() ?? User::factory()->create();

    $response = $this->actingAs($user)->get(route('customers.index'));
    $response->assertStatus(200);
    $response->assertSee('Customers Directory');

    // Create via Web
    $postResponse = $this->actingAs($user)->post(route('customers.store'), [
        'name' => 'Kawsar Mahmud',
        'phone' => '01677889900',
        'email' => 'kawsar@example.com',
        'address' => 'Mirpur 10, Dhaka',
    ]);

    $postResponse->assertRedirect(route('customers.index'));
    $this->assertDatabaseHas('customers', ['phone' => '01677889900']);

    // Create via AJAX (POS modal)
    $ajaxResponse = $this->actingAs($user)->postJson(route('customers.store'), [
        'name' => 'Tariqul Islam',
        'phone' => '01799887766',
    ]);

    $ajaxResponse->assertStatus(201);
    $ajaxResponse->assertJson(['success' => true]);
    $this->assertDatabaseHas('customers', ['phone' => '01799887766']);
});

test('authenticated user can view customer ledger and collect due payment', function () {
    $user = User::where('email', 'admin@gmail.com')->first() ?? User::factory()->create();

    $customer = Customer::firstOrCreate(
        ['phone' => '01855667788'],
        ['name' => 'Due Test Customer', 'total_due' => 500.00]
    );

    $customer->update(['total_due' => 500.00]);

    $ledgerResponse = $this->actingAs($user)->get(route('customers.ledger'));
    $ledgerResponse->assertStatus(200);
    $ledgerResponse->assertSee('Customer Due Ledger');

    // Collect 200 due payment
    $collectResponse = $this->actingAs($user)->post(route('customers.collect_due', $customer), [
        'amount' => 200.00,
        'payment_method' => 'Cash',
        'note' => 'Partial cash collection',
    ]);

    $collectResponse->assertSessionHas('success');
    $customer->refresh();
    expect((float) $customer->total_due)->toEqual(300.00);
});

test('cashier can open and close cash register shift with variance audit', function () {
    $cashier = User::where('email', 'cashier@gmail.com')->first() ?? User::factory()->create();

    // Close any previous open registers for clean test state
    CashRegister::where('user_id', $cashier->id)->where('status', 'open')->update(['status' => 'closed']);

    // 1. Open register
    $openResponse = $this->actingAs($cashier)->post(route('cash_register.open'), [
        'cash_in_hand' => 1500.00,
        'note' => 'Morning shift counter 2',
    ]);

    $openResponse->assertRedirect(route('cash_register.index'));
    $register = CashRegister::where('user_id', $cashier->id)->where('status', 'open')->latest()->first();
    expect($register)->not->toBeNull();
    expect((float) $register->cash_in_hand)->toEqual(1500.00);

    // 2. Close register
    // Expected cash: 1500 + 0 sales = 1500. Cashier submits 1550 (Surplus of +50)
    $closeResponse = $this->actingAs($cashier)->post(route('cash_register.close', $register), [
        'total_cash_submitted' => 1550.00,
        'note' => 'Counted correctly, extra 50 tips',
    ]);

    $closeResponse->assertRedirect(route('cash_register.history'));
    $register->refresh();
    expect($register->status)->toBe('closed');
    expect((float) $register->total_cash_submitted)->toEqual(1550.00);
    expect((float) $register->difference)->toEqual(50.00);
});

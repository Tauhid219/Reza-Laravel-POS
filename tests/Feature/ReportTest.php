<?php

use App\Models\Customer;
use App\Models\Product;
use App\Models\User;

test('authenticated user can view sales report and filter by period', function () {
    $user = User::where('email', 'admin@gmail.com')->first() ?? User::factory()->create();

    $response = $this->actingAs($user)->get(route('reports.sales'));
    $response->assertStatus(200);
    $response->assertSee('Sales Analytics &amp; Revenue Report', false);
    $response->assertSee('Gross Revenue');

    // Preset filter query test
    $presetResponse = $this->actingAs($user)->get(route('reports.sales', ['date_range' => 'today']));
    $presetResponse->assertStatus(200);

    // Custom date range test
    $customResponse = $this->actingAs($user)->get(route('reports.sales', [
        'date_range' => 'custom',
        'start_date' => now()->subDays(5)->format('Y-m-d'),
        'end_date' => now()->format('Y-m-d'),
    ]));
    $customResponse->assertStatus(200);
});

test('authenticated user can view profit and loss statement', function () {
    $user = User::where('email', 'admin@gmail.com')->first() ?? User::factory()->create();

    $response = $this->actingAs($user)->get(route('reports.profit_loss'));
    $response->assertStatus(200);
    $response->assertSee('Profit &amp; Loss Analytics', false);
    $response->assertSee('COGS (Goods Cost)');
    $response->assertSee('Category-wise Profit Contribution');
});

test('authenticated user can view stock valuation report and low stock filters', function () {
    $user = User::where('email', 'admin@gmail.com')->first() ?? User::factory()->create();

    $response = $this->actingAs($user)->get(route('reports.stock'));
    $response->assertStatus(200);
    $response->assertSee('Stock &amp; Inventory Valuation', false);
    $response->assertSee('Valuation at Cost');
    $response->assertSee('Valuation at Retail');

    // Low stock filter
    $lowStockResponse = $this->actingAs($user)->get(route('reports.stock', ['stock_status' => 'low_stock']));
    $lowStockResponse->assertStatus(200);
});

test('authenticated user can view customer due receivables report', function () {
    $user = User::where('email', 'admin@gmail.com')->first() ?? User::factory()->create();

    $response = $this->actingAs($user)->get(route('reports.customer_due'));
    $response->assertStatus(200);
    $response->assertSee('Customer Due &amp; Receivables Report', false);
    $response->assertSee('Total Market Receivables (Due)');
});

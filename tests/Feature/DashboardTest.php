<?php

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Unit;
use App\Models\User;

test('authenticated user can view dashboard with live metrics', function () {
    $user = User::where('email', 'admin@gmail.com')->first() ?? User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee('POS Dashboard');
    $response->assertSee("Today's Sales", false);
    $response->assertSee('Low Stock Alert');
});

test('models relationships work correctly', function () {
    $unit = Unit::firstOrCreate(['name' => 'Test Unit', 'short_name' => 'tu']);
    $category = Category::firstOrCreate(['name' => 'Test Cat', 'slug' => 'test-cat']);

    $product = Product::firstOrCreate(
        ['code' => 'TEST12345'],
        [
            'name' => 'Test Product',
            'unit_id' => $unit->id,
            'category_id' => $category->id,
            'cost_price' => 50,
            'selling_price' => 70,
            'stock_quantity' => 2,
            'alert_quantity' => 5,
        ]
    );

    expect($product->category->name)->toBe('Test Cat');
    expect($product->unit->short_name)->toBe('tu');
    expect($product->isLowStock())->toBeTrue();
});

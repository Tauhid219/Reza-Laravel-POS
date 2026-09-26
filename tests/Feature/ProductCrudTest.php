<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Models\User;

test('authenticated user can view products list', function () {
    $user = User::where('email', 'admin@gmail.com')->first() ?? User::factory()->create();

    $response = $this->actingAs($user)->get(route('products.index'));

    $response->assertStatus(200);
    $response->assertSee('Products Catalog');
});

test('authenticated user can create a product', function () {
    $user = User::where('email', 'admin@gmail.com')->first() ?? User::factory()->create();
    $category = Category::firstOrCreate(['slug' => 'test-cat'], ['name' => 'Test Category']);
    $unit = Unit::firstOrCreate(['name' => 'Piece'], ['short_name' => 'pc']);

    $data = [
        'name' => 'Brand New Chips',
        'code' => '9998887771',
        'category_id' => $category->id,
        'unit_id' => $unit->id,
        'cost_price' => 20,
        'selling_price' => 30,
        'stock_quantity' => 50,
        'alert_quantity' => 10,
        'status' => 1,
    ];

    $response = $this->actingAs($user)->post(route('products.store'), $data);

    $response->assertRedirect(route('products.index'));
    $this->assertDatabaseHas('products', [
        'code' => '9998887771',
        'name' => 'Brand New Chips',
    ]);
});

test('authenticated user can view and print barcode page', function () {
    $user = User::where('email', 'admin@gmail.com')->first() ?? User::factory()->create();

    $response = $this->actingAs($user)->get(route('products.barcode'));

    $response->assertStatus(200);
    $response->assertSee('Barcode Label Preview', false);
    $response->assertSee('Reza POS');
});

test('authenticated user can manage categories', function () {
    $user = User::where('email', 'admin@gmail.com')->first() ?? User::factory()->create();

    $response = $this->actingAs($user)->post(route('categories.store'), [
        'name' => 'Frozen Foods',
        'description' => 'Frozen fish and meat',
    ]);

    $response->assertRedirect(route('categories.index'));
    $this->assertDatabaseHas('categories', ['name' => 'Frozen Foods']);
});

test('authenticated user can manage units', function () {
    $user = User::where('email', 'admin@gmail.com')->first() ?? User::factory()->create();

    $response = $this->actingAs($user)->post(route('units.store'), [
        'name' => 'Dozen',
        'short_name' => 'dz',
    ]);

    $response->assertRedirect(route('units.index'));
    $this->assertDatabaseHas('units', ['name' => 'Dozen', 'short_name' => 'dz']);
});

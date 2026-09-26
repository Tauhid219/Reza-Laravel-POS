<?php

use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('admin can view settings page and update store profile and pos configs', function () {
    $admin = User::where('email', 'admin@gmail.com')->first() ?? User::factory()->create();
    if (!$admin->hasRole('Admin')) {
        $admin->assignRole('Admin');
    }

    $response = $this->actingAs($admin)->get(route('settings.index'));
    $response->assertStatus(200);
    $response->assertSee('System &amp; Store Settings', false);
    $response->assertSee('Store Profile');

    // Update Settings
    $postResponse = $this->actingAs($admin)->post(route('settings.update'), [
        'company_name' => 'Metro Superstore & POS',
        'company_phone' => '+880 1911-223344',
        'company_email' => 'sales@metropos.com',
        'company_address' => 'Gulshan 2, Dhaka, Bangladesh',
        'vat_number' => '99887766-0101',
        'currency_symbol' => '৳',
        'currency_code' => 'BDT',
        'default_tax_rate' => 5.0,
        'invoice_prefix' => 'METRO-',
        'receipt_footer' => 'Happy shopping! Visit us again soon.',
        'invoice_terms' => 'Standard warranty applies on all electronics.',
    ]);

    $postResponse->assertRedirect(route('settings.index'));
    $postResponse->assertSessionHas('success');

    expect(Setting::get('company_name'))->toEqual('Metro Superstore & POS');
    expect(Setting::get('company_phone'))->toEqual('+880 1911-223344');
    expect(Setting::get('vat_number'))->toEqual('99887766-0101');
    expect(Setting::get('receipt_footer'))->toEqual('Happy shopping! Visit us again soon.');
});

test('non-admin cashier cannot access settings or user management', function () {
    $cashier = User::where('email', 'cashier@gmail.com')->first() ?? User::factory()->create();
    if (!$cashier->hasRole('Cashier')) {
        $cashier->assignRole('Cashier');
    }

    $settingsResponse = $this->actingAs($cashier)->get(route('settings.index'));
    $settingsResponse->assertStatus(403);

    $usersResponse = $this->actingAs($cashier)->get(route('users.index'));
    $usersResponse->assertStatus(403);
});

test('admin can manage staff users and assign roles', function () {
    $admin = User::where('email', 'admin@gmail.com')->first() ?? User::factory()->create();
    if (!$admin->hasRole('Admin')) {
        $admin->assignRole('Admin');
    }

    // 1. View Users List
    $indexResponse = $this->actingAs($admin)->get(route('users.index'));
    $indexResponse->assertStatus(200);
    $indexResponse->assertSee('Staff &amp; User Management', false);

    // 2. View Create Form
    $createResponse = $this->actingAs($admin)->get(route('users.create'));
    $createResponse->assertStatus(200);

    // 3. Store New Staff (Cashier)
    $testEmail = 'staff'.rand(1000, 9999).'@example.com';
    $storeResponse = $this->actingAs($admin)->post(route('users.store'), [
        'name' => 'Fahim Hasan',
        'username' => 'fahim'.rand(100, 999),
        'email' => $testEmail,
        'phone' => '017'.rand(10000000, 99999999),
        'role' => 'Cashier',
        'status' => 'active',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $storeResponse->assertRedirect(route('users.index'));
    $storeResponse->assertSessionHas('success');

    $newStaff = User::where('email', $testEmail)->first();
    expect($newStaff)->not->toBeNull();
    expect($newStaff->hasRole('Cashier'))->toBeTrue();

    // 4. Update Staff
    $updateResponse = $this->actingAs($admin)->put(route('users.update', $newStaff), [
        'name' => 'Fahim Hasan Updated',
        'email' => $testEmail,
        'role' => 'Manager',
        'status' => 'active',
    ]);

    $updateResponse->assertRedirect(route('users.index'));
    $newStaff->refresh();
    expect($newStaff->name)->toEqual('Fahim Hasan Updated');
    expect($newStaff->hasRole('Manager'))->toBeTrue();

    // 5. Delete Staff
    $deleteResponse = $this->actingAs($admin)->delete(route('users.destroy', $newStaff));
    $deleteResponse->assertRedirect(route('users.index'));
    $this->assertDatabaseMissing('users', ['id' => $newStaff->id]);
});

test('admin cannot delete own account', function () {
    $admin = User::where('email', 'admin@gmail.com')->first();

    $response = $this->actingAs($admin)->delete(route('users.destroy', $admin));
    $response->assertSessionHas('error');
    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});

test('admin can view roles and permissions index', function () {
    $admin = User::where('email', 'admin@gmail.com')->first();

    $response = $this->actingAs($admin)->get(route('roles.index'));
    $response->assertStatus(200);
    $response->assertSee('Roles &amp; Permissions', false);
    $response->assertSee('Admin');
    $response->assertSee('Manager');
    $response->assertSee('Cashier');
});

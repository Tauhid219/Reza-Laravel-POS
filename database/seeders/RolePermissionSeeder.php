<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions List
        $permissions = [
            // Dashboard
            'dashboard.view',

            // POS & Orders
            'pos.access',
            'order.view',
            'order.create',
            'order.delete',
            'order.due_payment',

            // Inventory & Products
            'product.view',
            'product.create',
            'product.edit',
            'product.delete',
            'category.manage',
            'unit.manage',
            'stock.adjust',

            // Procurement & Suppliers
            'purchase.view',
            'purchase.create',
            'purchase.edit',
            'purchase.delete',
            'supplier.manage',

            // Customers
            'customer.view',
            'customer.create',
            'customer.edit',
            'customer.delete',
            'customer.ledger',

            // Cash Register / Shifts
            'cash_register.access',
            'cash_register.open',
            'cash_register.close',
            'cash_register.view_history',

            // Reports
            'report.sales',
            'report.purchases',
            'report.profit_loss',
            'report.stock',

            // Administration
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
            'role.manage',
            'setting.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $managerRole = Role::firstOrCreate(['name' => 'Manager']);
        $cashierRole = Role::firstOrCreate(['name' => 'Cashier']);

        // Assign all permissions to Admin
        $adminRole->syncPermissions(Permission::all());

        // Assign permissions to Manager
        $managerRole->syncPermissions([
            'dashboard.view',
            'pos.access',
            'order.view',
            'order.create',
            'order.due_payment',
            'product.view',
            'product.create',
            'product.edit',
            'category.manage',
            'unit.manage',
            'stock.adjust',
            'purchase.view',
            'purchase.create',
            'purchase.edit',
            'supplier.manage',
            'customer.view',
            'customer.create',
            'customer.edit',
            'customer.ledger',
            'cash_register.access',
            'cash_register.open',
            'cash_register.close',
            'cash_register.view_history',
            'report.sales',
            'report.purchases',
            'report.profit_loss',
            'report.stock',
        ]);

        // Assign permissions to Cashier
        $cashierRole->syncPermissions([
            'dashboard.view',
            'pos.access',
            'order.view',
            'order.create',
            'customer.view',
            'customer.create',
            'cash_register.access',
            'cash_register.open',
            'cash_register.close',
        ]);

        // Create Seed Users
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'username' => 'admin',
                'phone' => '01700000001',
                'password' => Hash::make('password'),
                'status' => true,
                'email_verified_at' => now(),
            ]
        );
        $adminUser->syncRoles([$adminRole]);

        $managerUser = User::firstOrCreate(
            ['email' => 'manager@gmail.com'],
            [
                'name' => 'Store Manager',
                'username' => 'manager',
                'phone' => '01700000002',
                'password' => Hash::make('password'),
                'status' => true,
                'email_verified_at' => now(),
            ]
        );
        $managerUser->syncRoles([$managerRole]);

        $cashierUser = User::firstOrCreate(
            ['email' => 'cashier@gmail.com'],
            [
                'name' => 'Store Cashier',
                'username' => 'cashier',
                'phone' => '01700000003',
                'password' => Hash::make('password'),
                'status' => true,
                'email_verified_at' => now(),
            ]
        );
        $cashierUser->syncRoles([$cashierRole]);
    }
}

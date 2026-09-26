<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Units
        $unitsData = [
            ['name' => 'Piece', 'short_name' => 'pc'],
            ['name' => 'Kilogram', 'short_name' => 'kg'],
            ['name' => 'Gram', 'short_name' => 'g'],
            ['name' => 'Box', 'short_name' => 'box'],
            ['name' => 'Packet', 'short_name' => 'pkt'],
            ['name' => 'Liter', 'short_name' => 'ltr'],
        ];

        $unitModels = [];
        foreach ($unitsData as $u) {
            $unitModels[$u['short_name']] = Unit::firstOrCreate(['name' => $u['name']], $u);
        }

        // 2. Categories
        $categoriesData = [
            'Beverages' => 'Soft drinks, juices, mineral water and energy drinks',
            'Bakery & Bread' => 'Fresh bread, cakes, buns, and cookies',
            'Snacks & Confectionery' => 'Chips, chocolates, wafers, and candy',
            'Dairy & Eggs' => 'Milk, butter, cheese, and farm eggs',
            'Groceries & Spices' => 'Rice, lentils, cooking oil, salt, and spices',
            'Personal Care & Hygiene' => 'Soaps, shampoos, toothpastes, and handwash',
        ];

        $categoryModels = [];
        foreach ($categoriesData as $name => $desc) {
            $categoryModels[$name] = Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'description' => $desc, 'status' => true]
            );
        }

        // 3. Suppliers
        $suppliersData = [
            [
                'name' => 'Pran Consumer Products Ltd',
                'company_name' => 'PRAN-RFL Group',
                'email' => 'sales@pran.com',
                'phone' => '01811223344',
                'address' => 'PRAN-RFL Centre, Middle Badda, Dhaka',
            ],
            [
                'name' => 'Akij Food & Beverage Ltd',
                'company_name' => 'Akij Group',
                'email' => 'orders@akij.net',
                'phone' => '01711223344',
                'address' => 'Akij House, Tejgaon, Dhaka',
            ],
            [
                'name' => 'Square Toiletries & Consumer Goods',
                'company_name' => 'Square Group',
                'email' => 'info@square.com',
                'phone' => '01911223344',
                'address' => 'Square Centre, Mohakhali C/A, Dhaka',
            ],
            [
                'name' => 'Meghna Group of Industries',
                'company_name' => 'MGI Fresh Products',
                'email' => 'distribution@mgi.org',
                'phone' => '01611223344',
                'address' => 'Fresh Villa, Gulshan-1, Dhaka',
            ],
        ];

        foreach ($suppliersData as $s) {
            Supplier::firstOrCreate(['phone' => $s['phone']], $s);
        }

        // 4. Customers (including Default Walk-in Customer)
        Customer::firstOrCreate(
            ['is_walk_in' => true],
            [
                'name' => 'Walk-in Customer',
                'email' => 'walkin@rezapos.com',
                'phone' => '00000000000',
                'address' => 'Counter / Retail Store',
                'total_due' => 0.00,
                'status' => true,
            ]
        );

        $customersData = [
            [
                'name' => 'Md. Farhan Ahmed',
                'email' => 'farhan.ahmed@example.com',
                'phone' => '01712345678',
                'address' => 'House 14, Road 5, Dhanmondi, Dhaka',
                'total_due' => 0.00,
            ],
            [
                'name' => 'Rashidul Hasan',
                'email' => 'rashidul@example.com',
                'phone' => '01812345678',
                'address' => 'Sector 7, Uttara, Dhaka',
                'total_due' => 450.00,
            ],
            [
                'name' => 'Nusrat Jahan',
                'email' => 'nusrat.j@example.com',
                'phone' => '01912345678',
                'address' => 'Block C, Banani, Dhaka',
                'total_due' => 0.00,
            ],
        ];

        foreach ($customersData as $c) {
            Customer::firstOrCreate(['phone' => $c['phone']], $c);
        }

        // 5. Products
        $productsData = [
            [
                'name' => 'Coca-Cola Can 250ml',
                'code' => '8941100101',
                'category_id' => $categoryModels['Beverages']->id,
                'unit_id' => $unitModels['pc']->id,
                'cost_price' => 38.00,
                'selling_price' => 45.00,
                'stock_quantity' => 120.00,
                'alert_quantity' => 15.00,
                'description' => 'Chilled 250ml carbonated beverage can',
            ],
            [
                'name' => 'Mojo Cola 500ml',
                'code' => '8941100102',
                'category_id' => $categoryModels['Beverages']->id,
                'unit_id' => $unitModels['pc']->id,
                'cost_price' => 28.00,
                'selling_price' => 35.00,
                'stock_quantity' => 85.00,
                'alert_quantity' => 12.00,
                'description' => 'Akij Mojo cola 500ml bottle',
            ],
            [
                'name' => 'Pran Frooto Mango Juice 250ml',
                'code' => '8941100103',
                'category_id' => $categoryModels['Beverages']->id,
                'unit_id' => $unitModels['pc']->id,
                'cost_price' => 22.00,
                'selling_price' => 30.00,
                'stock_quantity' => 60.00,
                'alert_quantity' => 10.00,
                'description' => 'Real mango juice drink',
            ],
            [
                'name' => 'All Time Special Milk Bread 400g',
                'code' => '8941100201',
                'category_id' => $categoryModels['Bakery & Bread']->id,
                'unit_id' => $unitModels['pkt']->id,
                'cost_price' => 62.00,
                'selling_price' => 75.00,
                'stock_quantity' => 35.00,
                'alert_quantity' => 8.00,
                'description' => 'Fresh sliced milk bread',
            ],
            [
                'name' => 'Sunlight Butter Bun 60g',
                'code' => '8941100202',
                'category_id' => $categoryModels['Bakery & Bread']->id,
                'unit_id' => $unitModels['pc']->id,
                'cost_price' => 12.00,
                'selling_price' => 15.00,
                'stock_quantity' => 4.00, // Low stock trigger test!
                'alert_quantity' => 10.00,
                'description' => 'Sweet butter bun',
            ],
            [
                'name' => 'Lays American Style Cream & Onion 50g',
                'code' => '8941100301',
                'category_id' => $categoryModels['Snacks & Confectionery']->id,
                'unit_id' => $unitModels['pkt']->id,
                'cost_price' => 32.00,
                'selling_price' => 40.00,
                'stock_quantity' => 95.00,
                'alert_quantity' => 20.00,
                'description' => 'Potato chips cream & onion flavor',
            ],
            [
                'name' => 'Aarong Dairy Pure Ghee 400g',
                'code' => '8941100401',
                'category_id' => $categoryModels['Dairy & Eggs']->id,
                'unit_id' => $unitModels['pc']->id,
                'cost_price' => 640.00,
                'selling_price' => 720.00,
                'stock_quantity' => 18.00,
                'alert_quantity' => 5.00,
                'description' => 'Traditional aromatic cow milk ghee',
            ],
            [
                'name' => 'Fresh Fortified Soybean Oil 1 Litre',
                'code' => '8941100501',
                'category_id' => $categoryModels['Groceries & Spices']->id,
                'unit_id' => $unitModels['ltr']->id,
                'cost_price' => 165.00,
                'selling_price' => 185.00,
                'stock_quantity' => 50.00,
                'alert_quantity' => 10.00,
                'description' => '100% pure refined soybean oil',
            ],
            [
                'name' => 'Dettol Original Bar Soap 100g',
                'code' => '8941100601',
                'category_id' => $categoryModels['Personal Care & Hygiene']->id,
                'unit_id' => $unitModels['pc']->id,
                'cost_price' => 55.00,
                'selling_price' => 68.00,
                'stock_quantity' => 70.00,
                'alert_quantity' => 15.00,
                'description' => 'Antibacterial protection body soap',
            ],
        ];

        foreach ($productsData as $p) {
            Product::firstOrCreate(['code' => $p['code']], $p);
        }
    }
}

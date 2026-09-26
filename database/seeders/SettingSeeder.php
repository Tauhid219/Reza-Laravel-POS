<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            'company_name' => 'REZA POS & SUPER STORE',
            'company_phone' => '+880 1700-000000',
            'company_email' => 'info@rezapos.com',
            'company_address' => 'House #42, Road #27, Dhanmondi, Dhaka - 1209, Bangladesh',
            'vat_number' => '001234567-0101',
            'currency_symbol' => '৳',
            'currency_code' => 'BDT',
            'default_tax_rate' => '0',
            'invoice_prefix' => 'INV-',
            'receipt_footer' => 'Thank you for shopping with us! Please come again.',
            'invoice_terms' => '1. Thank you for your business. 2. Goods once sold can be replaced within 7 days in undamaged original packaging. 3. Computer-generated invoice; valid without physical stamp.',
        ];

        foreach ($defaults as $key => $value) {
            Setting::set($key, $value);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General Settings
            ['key' => 'company_name', 'value' => 'My Library', 'type' => 'string', 'group' => 'general', 'description' => 'Company name', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'company_email', 'value' => 'info@library.ma', 'type' => 'string', 'group' => 'general', 'description' => 'Company email', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'company_phone', 'value' => '+212 5XX-XXXXXX', 'type' => 'string', 'group' => 'general', 'description' => 'Company phone', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'company_address', 'value' => 'Fes, Morocco', 'type' => 'string', 'group' => 'general', 'description' => 'Company address', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'currency', 'value' => 'MAD', 'type' => 'string', 'group' => 'general', 'description' => 'Default currency', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'timezone', 'value' => 'Africa/Casablanca', 'type' => 'string', 'group' => 'general', 'description' => 'System timezone', 'created_at' => now(), 'updated_at' => now()],
            
            // POS Settings
            ['key' => 'default_tax_rate', 'value' => '20.00', 'type' => 'number', 'group' => 'pos', 'description' => 'Default VAT percentage', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'low_stock_threshold', 'value' => '5', 'type' => 'number', 'group' => 'pos', 'description' => 'Low stock alert level', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'points_per_dirham', 'value' => '1', 'type' => 'number', 'group' => 'pos', 'description' => 'Loyalty points per MAD spent', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'points_value', 'value' => '0.10', 'type' => 'number', 'group' => 'pos', 'description' => 'Value of 1 point in MAD', 'created_at' => now(), 'updated_at' => now()],
            
            // Receipt Settings
            ['key' => 'receipt_header', 'value' => 'Thank you for your purchase!', 'type' => 'string', 'group' => 'receipt', 'description' => 'Receipt header text', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'receipt_footer', 'value' => 'Visit us again soon!', 'type' => 'string', 'group' => 'receipt', 'description' => 'Receipt footer text', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'show_company_logo', 'value' => '1', 'type' => 'boolean', 'group' => 'receipt', 'description' => 'Show logo on receipt', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'auto_print_receipt', 'value' => '1', 'type' => 'boolean', 'group' => 'receipt', 'description' => 'Auto print after sale', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('settings')->insert($settings);
    }
}

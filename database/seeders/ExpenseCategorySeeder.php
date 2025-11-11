<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Rent', 'name_ar' => 'إيجار', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Utilities', 'name_ar' => 'مرافق', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Salaries', 'name_ar' => 'رواتب', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Office Supplies', 'name_ar' => 'لوازم مكتبية', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Marketing', 'name_ar' => 'تسويق', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Maintenance', 'name_ar' => 'صيانة', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Transportation', 'name_ar' => 'نقل', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Other', 'name_ar' => 'أخرى', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('expense_categories')->insert($categories);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MembershipTierSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            [
                'name' => 'Regular',
                'name_ar' => 'عادي',
                'discount_percentage' => 0,
                'points_multiplier' => 1.00,
                'min_purchase_amount' => 0,
                'color' => '#6B7280',
                'benefits' => 'Standard benefits',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Silver',
                'name_ar' => 'فضي',
                'discount_percentage' => 5,
                'points_multiplier' => 1.50,
                'min_purchase_amount' => 1000,
                'color' => '#C0C0C0',
                'benefits' => '5% discount, 1.5x points',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gold',
                'name_ar' => 'ذهبي',
                'discount_percentage' => 10,
                'points_multiplier' => 2.00,
                'min_purchase_amount' => 5000,
                'color' => '#FFD700',
                'benefits' => '10% discount, 2x points, priority service',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('membership_tiers')->insert($tiers);
    }
}

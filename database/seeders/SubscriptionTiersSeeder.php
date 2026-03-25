<?php

namespace Database\Seeders;

use App\Models\SubscriptionTier;
use Illuminate\Database\Seeder;

class SubscriptionTiersSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            [
                'name' => 'silver',
                'display_name' => 'Silver',
                'description' => 'Paket dasar untuk UMKM yang baru memulai bisnis. Cocok untuk usaha dengan satu outlet.',
                'price' => 75000,
                'max_outlets' => 1,
                'trial_duration_days' => 30,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'gold',
                'display_name' => 'Gold',
                'description' => 'Paket lengkap dengan fitur lanjutan untuk bisnis yang sedang berkembang. Termasuk dashboard, laporan, dan manajemen karyawan.',
                'price' => 100000,
                'max_outlets' => 1,
                'trial_duration_days' => 30,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'platinum',
                'display_name' => 'Platinum',
                'description' => 'Paket premium dengan semua fitur termasuk multi-outlet, AI insights, dan fitur eksklusif lainnya.',
                'price' => 150000,
                'max_outlets' => null, // Unlimited
                'trial_duration_days' => 30,
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($tiers as $tier) {
            SubscriptionTier::updateOrCreate(
                ['name' => $tier['name']],
                $tier
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\SubscriptionSetting;
use Illuminate\Database\Seeder;

class SubscriptionSettingsSeeder extends Seeder
{
    public function run(): void
    {
        SubscriptionSetting::updateOrCreate(
            ['id' => 1],
            [
                'trial_duration_days' => 30,
                'grace_period_days' => 7,
                'enable_trial' => true,
                'require_trial_verification' => true,
                'auto_renew_default' => false,
            ]
        );
    }
}

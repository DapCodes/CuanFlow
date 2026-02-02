<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use App\Models\SubscriptionTier;
use Illuminate\Database\Seeder;

class SubscriptionPlansSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = SubscriptionTier::all();

        foreach ($tiers as $tier) {
            $basePrice = $tier->price;

            // Monthly - No discount
            SubscriptionPlan::updateOrCreate(
                ['tier_id' => $tier->id, 'duration_months' => 1],
                [
                    'price' => $basePrice,
                    'discount_percentage' => 0,
                    'is_active' => true,
                    'is_unlimited' => false,
                ]
            );

            // Quarterly - 10% discount
            SubscriptionPlan::updateOrCreate(
                ['tier_id' => $tier->id, 'duration_months' => 3],
                [
                    'price' => $basePrice * 3 * 0.90, // 10% off
                    'discount_percentage' => 10,
                    'is_active' => true,
                    'is_unlimited' => false,
                ]
            );

            // Semi-annual - 15% discount
            SubscriptionPlan::updateOrCreate(
                ['tier_id' => $tier->id, 'duration_months' => 6],
                [
                    'price' => $basePrice * 6 * 0.85, // 15% off
                    'discount_percentage' => 15,
                    'is_active' => true,
                    'is_unlimited' => false,
                ]
            );

            // Annual - 25% discount
            SubscriptionPlan::updateOrCreate(
                ['tier_id' => $tier->id, 'duration_months' => 12],
                [
                    'price' => $basePrice * 12 * 0.75, // 25% off
                    'discount_percentage' => 25,
                    'is_active' => true,
                    'is_unlimited' => false,
                ]
            );

            // Unlimited (lifetime) - for special users
            SubscriptionPlan::updateOrCreate(
                ['tier_id' => $tier->id, 'duration_months' => null, 'is_unlimited' => true],
                [
                    'price' => $basePrice * 24, // 2 years worth
                    'discount_percentage' => 0,
                    'is_active' => false, // Disabled by default
                    'is_unlimited' => true,
                ]
            );
        }
    }
}

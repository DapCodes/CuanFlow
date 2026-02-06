<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\SubscriptionTier;
use Illuminate\Database\Seeder;

class TierFeaturesSeeder extends Seeder
{
    public function run(): void
    {
        // Silver tier features (19 core features)
        $silverFeatures = [
            'pos',
            'sales_management',
            'discount_management',
            'finance_management',
            'other_income',
            'operational_costs',
            'balance_withdrawal',
            'payment_methods',
            'products_recipes',
            'raw_materials',
            'suppliers',
            'production',
            'stock_opname',
            'customer_management',
            'accounts_receivable',
            'table_management',
            'outlet_policies',
            'account_settings',
            'help_faq',
            // 'clara_ai'
        ];

        // Gold tier features (Silver + 6 additional = 25 total)
        $goldAdditionalFeatures = [
            'invoice_list',
            'task_management',
            'dashboard',
            'reports',
            'employee_management',
            'access_rights',
        ];

        // Platinum tier features (Gold + 7 premium = 32 total)
        $platinumAdditionalFeatures = [
            'multi_outlet',
            'landing_page',
            'testimonials',
            'stock_transfer',
            'ai_insights',
            'clara_ai',
            'reseller_app',
        ];

        // Get tiers
        $silverTier = SubscriptionTier::where('name', 'silver')->first();
        $goldTier = SubscriptionTier::where('name', 'gold')->first();
        $platinumTier = SubscriptionTier::where('name', 'platinum')->first();

        if (!$silverTier || !$goldTier || !$platinumTier) {
            $this->command->error('Please run SubscriptionTiersSeeder first!');
            return;
        }

        // Assign Silver features
        $this->assignFeaturesToTier($silverTier, $silverFeatures);

        // Assign Gold features (includes all Silver features)
        $goldFeatures = array_merge($silverFeatures, $goldAdditionalFeatures);
        $this->assignFeaturesToTier($goldTier, $goldFeatures);

        // Assign Platinum features (includes all Gold features)
        $platinumFeatures = array_merge($goldFeatures, $platinumAdditionalFeatures);
        $this->assignFeaturesToTier($platinumTier, $platinumFeatures);

        // Update features_list JSON on each tier for quick reference
        $this->updateFeaturesListJson($silverTier);
        $this->updateFeaturesListJson($goldTier);
        $this->updateFeaturesListJson($platinumTier);
    }

    private function assignFeaturesToTier(SubscriptionTier $tier, array $featureNames): void
    {
        $featureIds = Feature::whereIn('name', $featureNames)->pluck('id');
        $tier->features()->sync($featureIds);

        $this->command->info("Assigned " . count($featureIds) . " features to {$tier->display_name} tier");
    }

    private function updateFeaturesListJson(SubscriptionTier $tier): void
    {
        $features = $tier->features()->pluck('display_name', 'name')->toArray();
        $tier->update(['features_list' => $features]);
    }
}

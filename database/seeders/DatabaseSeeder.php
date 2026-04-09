<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            UnitSeeder::class,
            CategorySeeder::class,
            ExpenseCategorySeeder::class,
            SettingSeeder::class,
            FeaturesSeeder::class,
            SubscriptionTiersSeeder::class,
            TierFeaturesSeeder::class,
            SubscriptionPlansSeeder::class,
            SubscriptionSettingsSeeder::class,
            OutletSeeder::class,
            SupplierSeeder::class,
            RawMaterialSeeder::class,
            ProductWithRecipeSeeder::class,
            FaqSeeder::class,
            TableSeeder::class,
            PaymentMethodSeeder::class,
            OutletPaymentLinkSeeder::class,
            SaleSeeder::class,
            CustomerDummySeeder::class,
            ResellerApplicationSeeder::class,
            EmployeeSeeder::class,
            // DiscountSeeder::class,
            AdminLandingPageSeeder::class,
            // ProductStockSeeder::class,
            TermsAndConditionSeeder::class,
            ResellerTestSeeder::class,
            ColorPaletteSeeder::class,
        ]);

        // Buat Akun Admin
        $admin = \App\Models\User::updateOrCreate(
            ['email' => 'admin@cuanflow.com'],
            [
                'name' => 'Admin CuanFlow',
                'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
                'email_verified_at' => now(),
                'outlet_id' => null,
                'phone' => null,
                'avatar' => null,
                'is_active' => true,
            ]
        );

        $admin->assignRole('admin');
    }
}

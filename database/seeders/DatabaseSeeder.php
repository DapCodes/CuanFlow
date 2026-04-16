<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
            BlogSeeder::class,
            CareerSeeder::class,
        ]);

        // Buat Akun Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@cuanflow.com'],
            [
                'name' => 'Admin CuanFlow',
                'password' => Hash::make('12345678'),
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

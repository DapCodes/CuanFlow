<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Outlet;
use App\Models\Customer;
use App\Models\ResellerApplication;
use App\Models\UserSubscription;
use App\Models\SubscriptionTier;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ResellerTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create/Update User (Reseller Owner)
        $user = User::updateOrCreate(
            ['email' => 'd4pfft123@gmail.com'],
            [
                'name' => 'Test Reseller',
                'phone' => '081234567890',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        // Assign Role
        $role = Role::firstOrCreate(['name' => 'owner']);
        $user->assignRole($role);

        // 2. Create/Update Outlet for this user
        $outlet = Outlet::updateOrCreate(
            ['code' => 'OUT-RESELLER-01'],
            [
                'name' => 'Test Reseller Outlet',
                'address' => 'Jl. Reseller No. 123',
                'phone' => '081234567890',
                'email' => 'outletsikami@gmail.com',
                'owner_id' => $user->id,
                'is_active' => true,
                'accepts_reseller' => true,
            ]
        );

        // Update User's main outlet
        $user->update(['outlet_id' => $outlet->id]);

        // 3. Create/Update Customer Record
        $customer = Customer::updateOrCreate(
            ['email' => 'd4pfft123@gmail.com'],
            [
                'code' => 'CUST-RES-01',
                'name' => 'Test Reseller',
                'phone' => '081234567890',
                'address' => 'Jl. Reseller No. 123',
                'type' => 'reseller',
                'is_active' => true,
            ]
        );

        // 4. Create Approved Reseller Application to Outlet ID 1
        ResellerApplication::updateOrCreate(
            ['customer_id' => $customer->id, 'outlet_id' => 1],
            [
                'description' => 'I want to be a reseller of your products.',
                'status' => 'approved',
                'processed_by' => 1, // Assume admin with ID 1 exists
                'processed_at' => now(),
            ]
        );

        // 5. Add PLATINUM Subscription (1 Month)
        $platinumTier = SubscriptionTier::where('name', 'Platinum')->first();
        if ($platinumTier) {
            $platinumPlan = SubscriptionPlan::where('tier_id', $platinumTier->id)
                ->where('duration_months', 1)
                ->first();

            if ($platinumPlan) {
                UserSubscription::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'tier_id' => $platinumTier->id,
                        'plan_id' => $platinumPlan->id,
                        'status' => UserSubscription::STATUS_ACTIVE,
                        'started_at' => now(),
                        'expires_at' => now()->addMonth(),
                        'is_trial' => false,
                        'auto_renew' => true,
                    ]
                );
            }
        }
    }
}

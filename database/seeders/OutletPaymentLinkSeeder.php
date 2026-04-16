<?php

namespace Database\Seeders;

use App\Models\OutletPaymentLink;
use Illuminate\Database\Seeder;

class OutletPaymentLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $outlets = [1, 2, 3];
        $paymentMethods = [1, 2, 3];

        foreach ($outlets as $outletId) {
            foreach ($paymentMethods as $paymentMethodId) {
                OutletPaymentLink::create([
                    'outlet_id' => $outletId,
                    'payment_method_id' => $paymentMethodId,
                    'account_number' => '1234567890',
                    'account_name' => 'Outlet '.$outletId.' Account '.$paymentMethodId,
                    'notes' => 'Seeded payment link for testing',
                    'is_active' => true,
                ]);
            }
        }
    }
}

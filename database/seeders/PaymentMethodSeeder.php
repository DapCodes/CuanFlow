<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['name' => 'Bank BCA', 'code' => 'bca', 'icon' => 'assets/bank/bca.png'],
            ['name' => 'Bank BRI', 'code' => 'bri', 'icon' => 'assets/bank/bri.png'],
            ['name' => 'Bank BNI', 'code' => 'bni', 'icon' => 'assets/bank/bni.jpg'],
            ['name' => 'Bank Mandiri', 'code' => 'mandiri', 'icon' => 'assets/bank/mandiri.png'],
            ['name' => 'Bank Permata', 'code' => 'permata', 'icon' => 'assets/bank/permata-bank.jpg'],
            ['name' => 'Bank CIMB Niaga', 'code' => 'cimb', 'icon' => 'assets/bank/cimb-niaga.png'],
            ['name' => 'QRIS', 'code' => 'qris', 'icon' => 'assets/bank/qris.png'],
            ['name' => 'GoPay', 'code' => 'gopay', 'icon' => 'assets/bank/gopay.png'],
            ['name' => 'OVO', 'code' => 'ovo', 'icon' => 'assets/bank/ovo.jpg'],
            ['name' => 'DANA', 'code' => 'dana', 'icon' => 'assets/bank/dana.png'],
            ['name' => 'ShopeePay', 'code' => 'shopeepay', 'icon' => 'assets/bank/shopeepay.png'],
            ['name' => 'LinkAja', 'code' => 'linkaja', 'icon' => 'assets/bank/link-aja.png'],
        ];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(
                ['code' => $method['code']],
                array_merge($method, ['is_active' => true])
            );
        }
    }
}

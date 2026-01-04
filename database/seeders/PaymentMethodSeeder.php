<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['name' => 'Bank BCA', 'code' => 'bca', 'icon' => null],
            ['name' => 'Bank BRI', 'code' => 'bri', 'icon' => null],
            ['name' => 'Bank BNI', 'code' => 'bni', 'icon' => null],
            ['name' => 'Bank Mandiri', 'code' => 'mandiri', 'icon' => null],
            ['name' => 'Bank Permata', 'code' => 'permata', 'icon' => null],
            ['name' => 'Bank CIMB Niaga', 'code' => 'cimb', 'icon' => null],
            ['name' => 'QRIS', 'code' => 'qris', 'icon' => null],
            ['name' => 'GoPay', 'code' => 'gopay', 'icon' => null],
            ['name' => 'OVO', 'code' => 'ovo', 'icon' => null],
            ['name' => 'DANA', 'code' => 'dana', 'icon' => null],
            ['name' => 'ShopeePay', 'code' => 'shopeepay', 'icon' => null],
            ['name' => 'LinkAja', 'code' => 'linkaja', 'icon' => null],
        ];

        foreach ($methods as $method) {
            PaymentMethod::firstOrCreate(
                ['code' => $method['code']], 
                array_merge($method, ['is_active' => true])
            );
        }
    }
}
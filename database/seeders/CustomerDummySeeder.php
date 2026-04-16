<?php

namespace Database\Seeders;

use App\Models\Customer;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CustomerDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create('id_ID');

        for ($i = 0; $i < 100; $i++) {
            $type = $faker->randomElement(['regular', 'reseller', 'vip']);

            Customer::create([
                'code' => 'CUST-'.strtoupper(Str::random(8)),
                'name' => $faker->name,
                'phone' => $faker->unique()->phoneNumber,
                'email' => $faker->unique()->email,
                'address' => $faker->address,
                'type' => $type,
                'credit_limit' => $type == 'vip' ? 5000000 : ($type == 'reseller' ? 2000000 : 0),
                'total_debt' => 0,
                'points' => $faker->numberBetween(0, 5000),
                'is_active' => true,
            ]);
        }
    }
}

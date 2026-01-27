<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CustomerDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');

        for ($i = 0; $i < 100; $i++) {
            $type = $faker->randomElement(['regular', 'reseller', 'vip']);

            \App\Models\Customer::create([
                'code' => 'CUST-'.strtoupper(\Illuminate\Support\Str::random(8)),
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

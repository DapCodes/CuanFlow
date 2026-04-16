<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\ResellerApplication;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ResellerApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create('id_ID');

        $outletId = 1;

        // 1. Create Customers who will be APPROVED RESELLERS
        // validasi: customer dengan type reseller harus punya aplikasi approved
        for ($i = 0; $i < 5; $i++) {
            $customer = Customer::create([
                'code' => 'CUST-'.strtoupper(Str::random(8)),
                'name' => $faker->name,
                'phone' => $faker->unique()->phoneNumber,
                'email' => $faker->unique()->email,
                'address' => $faker->address,
                'type' => 'reseller', // Already reseller after approved
                'credit_limit' => 2000000,
                'total_debt' => 0,
                'points' => $faker->numberBetween(0, 5000),
                'is_active' => true,
            ]);

            ResellerApplication::create([
                'customer_id' => $customer->id,
                'outlet_id' => $outletId,
                'description' => $faker->paragraph,
                'document_path' => null,
                'status' => 'approved',
                'processed_by' => 1, // Admin/Owner ID
                'processed_at' => now(),
                'created_at' => $faker->dateTimeBetween('-2 month', '-1 month'),
            ]);
        }

        // 2. Create Customers who are PENDING/REJECTED (Type: regular)
        // validasi: customer type regular yg sedang apply (pending/rejected)
        for ($i = 0; $i < 10; $i++) {
            $customer = Customer::create([
                'code' => 'CUST-'.strtoupper(Str::random(8)),
                'name' => $faker->name,
                'phone' => $faker->unique()->phoneNumber,
                'email' => $faker->unique()->email,
                'address' => $faker->address,
                'type' => 'regular',
                'credit_limit' => 0,
                'total_debt' => 0,
                'points' => $faker->numberBetween(0, 1000),
                'is_active' => true,
            ]);

            $status = $faker->randomElement(['pending', 'rejected']);

            ResellerApplication::create([
                'customer_id' => $customer->id,
                'outlet_id' => $outletId,
                'description' => $faker->paragraph,
                'document_path' => null,
                'status' => $status,
                'processed_by' => $status === 'rejected' ? 1 : null,
                'processed_at' => $status === 'rejected' ? now() : null,
                'created_at' => $faker->dateTimeBetween('-1 month', 'now'),
            ]);
        }
    }
}

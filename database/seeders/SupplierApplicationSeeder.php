<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');

        // Ensure roles exist
        if (!\Spatie\Permission\Models\Role::where('name', 'supplier')->exists()) {
             \Spatie\Permission\Models\Role::create(['name' => 'supplier', 'guard_name' => 'web']);
        }
        if (!\Spatie\Permission\Models\Role::where('name', 'pelanggan')->exists()) {
             \Spatie\Permission\Models\Role::create(['name' => 'pelanggan', 'guard_name' => 'web']);
        }

        $outletId = 1;

        // 1. Create/Get Users who will be APPROVED SUPPLIERS
        // validasi: user dengan role supplier harus punya aplikasi approved
        for ($i = 0; $i < 5; $i++) {
            $user = \App\Models\User::factory()->create([
                'outlet_id' => $outletId,
            ]);
            $user->assignRole('supplier');
            
            \App\Models\SupplierApplication::create([
                'user_id' => $user->id,
                'outlet_id' => $outletId,
                'description' => $faker->paragraph,
                'document_path' => null, 
                'status' => 'approved',
                'processed_by' => 1, // Admin/Owner ID
                'processed_at' => now(),
                'created_at' => $faker->dateTimeBetween('-2 month', '-1 month'),
            ]);
        }

        // 2. Create/Get Users who are PENDING/REJECTED (Role: Pelanggan)
        // validasi: user role pelanggan yg sedang apply (pending/rejected)
        for ($i = 0; $i < 10; $i++) {
            $user = \App\Models\User::factory()->create([
                'outlet_id' => $outletId,
            ]);
            $user->assignRole('pelanggan');

            $status = $faker->randomElement(['pending', 'rejected']);
            
            \App\Models\SupplierApplication::create([
                'user_id' => $user->id,
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

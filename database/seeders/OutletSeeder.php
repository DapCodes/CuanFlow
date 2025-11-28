<?php

namespace Database\Seeders;

use App\Models\Outlet;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role; // Import class Role dari Spatie

class OutletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Membuat Outlet
        $outletsData = [
            [
                'code' => 'TKY',
                'name' => 'Takoyaki D&D',
                'address' => 'Jl. Merdeka No. 12, Jakarta Pusat',
                'latitude' => -6.1754,
                'longtitude' => 106.8270,
                'phone' => '081122334455',
                'email' => 'takoyaki.pusat@outlet.com',
                'is_active' => true,
            ],
            // [
            //     'code' => 'CMB',
            //     'name' => 'Outlet Cimol Bojot',
            //     'address' => 'Jl. Asia Afrika No. 45, Bandung Selatan',
            //     'latitude' => -6.9217,
            //     'longtitude' => 107.6100,
            //     'phone' => '082233445566',
            //     'email' => 'cimol.bojot@outlet.com',
            //     'is_active' => true,
            // ],
            // [
            //     'code' => 'JSK',
            //     'name' => 'Outlet Jasuke Baper',
            //     'address' => 'Jl. Dharmawangsa No. 78, Surabaya Timur',
            //     'latitude' => -7.2755,
            //     'longtitude' => 112.7533,
            //     'phone' => '083344556677',
            //     'email' => 'jasuke.baper@outlet.com',
            //     'is_active' => true,
            // ],
        ];

        $outlets = [];
        foreach ($outletsData as $data) {
            $outlets[] = Outlet::create($data);
        }

        // Ambil outlet pertama (Takoyaki) untuk dihubungkan sebagai outlet default user
        $firstOutlet = $outlets[0] ?? null;

        // 2. Membuat User dan Role
        if ($firstOutlet) {
            // 2a. Membuat Role 'owner' jika belum ada
            $ownerRole = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);

            $user = User::create([
                'outlet_id' => $firstOutlet->id, // Outlet yang sedang aktif/default
                'name' => 'Daffa Ramadhan',
                'email' => 'daffaramadhan929@gmail.com',
                'password' => Hash::make('12345678'),
                'phone' => '080011223344',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            $user2 = User::create([
                'outlet_id' => null, // Outlet yang sedang aktif/default
                'name' => 'Rio Oktora',
                'email' => 'rioktor@gmail.com',
                'password' => Hash::make('12345678'),
                'phone' => '080011223344',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // 2b. Menetapkan role 'owner' kepada user
            $user->assignRole($ownerRole);

            $user2->assignRole($ownerRole);

            // 3. Menetapkan owner_id pada semua outlet yang baru dibuat
            // Semua outlet ini dimiliki oleh Daffa Ramadhan
            foreach ($outlets as $outlet) {
                // Di sini kita menggunakan save() karena model $outlet sudah ada dalam memori
                $outlet->owner_id = $user->id;
                $outlet->save();
            }
        }
    }
}
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            [
                'name' => 'Daffa Ramadhan Kasir 1',
                'email' => 'daffaramadgan929@gmail.com',
                'role' => 'kasir',
            ],
            [
                'name' => 'Atsyle Kasir 2',
                'email' => 'atsyyle@gmail.com',
                'role' => 'kasir',
            ],
            [
                'name' => 'Evostok Supervisor',
                'email' => 'evostokdalang7@gmail.com',
                'role' => 'supervisor',
            ],
            [
                'name' => 'Spotify Inventaris 1',
                'email' => 'sptify415@gmail.com',
                'role' => 'inventaris',
            ],
            [
                'name' => 'D4pfft Inventaris 2',
                'email' => 'd4pfft123@gmail.com',
                'role' => 'inventaris',
            ],
            [
                'name' => 'Atrandha Produksi 1',
                'email' => 'atrandhaeffu@gmail.com',
                'role' => 'produksi',
            ],
            [
                'name' => 'Claude Produksi 2',
                'email' => 'claudedaffaramadhan@gmail.com',
                'role' => 'produksi',
            ],
        ];

        foreach ($employees as $employeeData) {
            $user = User::updateOrCreate(
                ['email' => $employeeData['email']],
                [
                    'name' => $employeeData['name'],
                    'password' => Hash::make('12345678'), // Default password
                    'outlet_id' => 1,
                    'email_verified_at' => Carbon::now(),
                    'is_active' => true,
                ]
            );

            // Assign role
            $user->syncRoles($employeeData['role']);
        }
    }
}

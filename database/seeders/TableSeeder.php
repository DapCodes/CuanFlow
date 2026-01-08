<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $outletId = 1;
        $tables = [];

        // Area Utama (Indoor) - 15 Meja
        for ($i = 1; $i <= 15; $i++) {
            $num = str_pad($i, 2, '0', STR_PAD_LEFT);
            $tables[] = [
                'outlet_id' => $outletId,
                'table_number' => $num,
                'code' => 'TBL-IN-' . $num,
                'name' => 'Meja Indoor ' . $num,
                'capacity' => ($i <= 10) ? 4 : 2, // 10 meja kapasitas 4, 5 meja kapasitas 2
                'location' => 'Lantai 1 - Area Utama',
                'status' => 'available',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Teras (Outdoor) - 10 Meja
        for ($i = 16; $i <= 25; $i++) {
            $num = str_pad($i, 2, '0', STR_PAD_LEFT);
            $tables[] = [
                'outlet_id' => $outletId,
                'table_number' => $num,
                'code' => 'TBL-OUT-' . $num,
                'name' => 'Meja Outdoor ' . $num,
                'capacity' => ($i <= 20) ? 2 : 4,
                'location' => 'Teras Depan',
                'status' => 'available',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // VIP Room (Lantai 2) - 10 Meja
        for ($i = 26; $i <= 35; $i++) {
            $num = str_pad($i, 2, '0', STR_PAD_LEFT);
            $tables[] = [
                'outlet_id' => $outletId,
                'table_number' => $num,
                'code' => 'TBL-VIP-' . $num,
                'name' => 'Meja VIP ' . $num,
                'capacity' => ($i <= 30) ? 6 : 8, // Kapasitas lebih besar untuk VIP
                'location' => 'Lantai 2 - VIP Room',
                'status' => 'available',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert using chunk for efficiency
        foreach (array_chunk($tables, 10) as $chunk) {
            Table::insert($chunk);
        }
    }
}

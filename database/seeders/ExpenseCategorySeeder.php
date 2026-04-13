<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $cats = [
            // ======================
            // PEMASUKAN (+)
            // ======================
            [
                'code' => '+SALE',
                'name' => 'Penjualan',
                'description' => 'Pendapatan dari penjualan produk',
            ],
            [
                'code' => '+ONLINE',
                'name' => 'Penjualan Online',
                'description' => 'Pendapatan dari marketplace / online',
            ],
            [
                'code' => '+RESELL',
                'name' => 'Penjualan Reseller',
                'description' => 'Pendapatan dari reseller',
            ],
            [
                'code' => '+OTHER_INC',
                'name' => 'Pemasukan Lainnya',
                'description' => 'Pendapatan di luar penjualan utama',
            ],
            [
                'code' => '+LATE_FEE',
                'name' => 'Denda / Jatuh Tempo',
                'description' => 'Pendapatan dari denda keterlambatan piutang',
            ],

            // ======================
            // PENGELUARAN (-)
            // ======================
            [
                'code' => '-UTIL',
                'name' => 'Utilitas',
                'description' => 'Listrik, air, gas',
            ],
            [
                'code' => '-RENT',
                'name' => 'Sewa',
                'description' => 'Sewa tempat / gedung',
            ],
            [
                'code' => '-SAL',
                'name' => 'Gaji',
                'description' => 'Gaji karyawan',
            ],
            [
                'code' => '-TRANS',
                'name' => 'Transportasi',
                'description' => 'Ongkir, bensin, parkir',
            ],
            [
                'code' => '-STOCK',
                'name' => 'Pembelian Stok',
                'description' => 'Pembelian bahan baku / barang dagang',
            ],
            [
                'code' => '-MAINT',
                'name' => 'Perawatan',
                'description' => 'Perbaikan & maintenance',
            ],
            [
                'code' => '-MARK',
                'name' => 'Marketing',
                'description' => 'Iklan & promosi',
            ],
            [
                'code' => '-FEE',
                'name' => 'Biaya Admin',
                'description' => 'Biaya admin bank / platform',
            ],
            [
                'code' => '-OTHER_EXP',
                'name' => 'Pengeluaran Lainnya',
                'description' => 'Pengeluaran di luar kategori utama',
            ],
        ];
        foreach ($cats as $c) {
            ExpenseCategory::create($c);
        }
    }
}

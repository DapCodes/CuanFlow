<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier; // Pastikan model Supplier sudah ada

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            // Supplier Bahan Pangan Utama
            [
                'code' => 'SP001', 
                'name' => 'PT. Sinar Pangan Jaya', 
                'contact_person' => 'Budi Santoso', 
                'phone' => '021-8889910', 
                'email' => 'budi.spg@example.com',
                'address' => 'Jl. Industri Raya No. 10, Jakarta Timur',
                'notes' => 'Supplier utama terigu dan gula. Pembayaran tempo 30 hari.',
                'is_active' => true,
            ],
            [
                'code' => 'SP002', 
                'name' => 'Dairy Segar Makmur', 
                'contact_person' => 'Rina Wijaya', 
                'phone' => '0812-3456-7890', 
                'email' => 'rina.dairy@example.com',
                'address' => 'Komp. Pergudangan Daan Mogot Blok C5, Jakarta Barat',
                'notes' => 'Fokus pada produk susu, keju, dan mentega. Harus dikirim menggunakan mobil pendingin.',
                'is_active' => true,
            ],
            // Supplier Telur dan Minyak
            [
                'code' => 'SP003', 
                'name' => 'Toko Telur Mandiri', 
                'contact_person' => 'Agus Dharmawan', 
                'phone' => '022-5554433', 
                'email' => 'agus.telur@example.com',
                'address' => 'Jl. Peternakan Unggul No. 12, Bandung',
                'notes' => 'Menyediakan telur ayam grade A dan B. Pengiriman setiap Senin dan Kamis.',
                'is_active' => true,
            ],
            [
                'code' => 'SP004', 
                'name' => 'PT. Minyak Nabati Sehat', 
                'contact_person' => 'Santi Devi', 
                'phone' => '0878-1122-3344', 
                'email' => 'santi.mns@example.com',
                'address' => 'Kawasan Industri KM 18, Bekasi',
                'notes' => 'Supplier minyak goreng dan lemak nabati.',
                'is_active' => true,
            ],
            // Supplier Bumbu dan Perasa
            [
                'code' => 'SP005', 
                'name' => 'Bumbu Aromatik Nusantara', 
                'contact_person' => 'Joko Susilo', 
                'phone' => '021-7776655', 
                'email' => 'joko.bumbu@example.com',
                'address' => 'Jl. Rempah Sari No. 5, Bogor',
                'notes' => 'Supplier ekstrak, perasa, dan bumbu kering. Harga cukup tinggi tapi kualitas premium.',
                'is_active' => true,
            ],
            [
                'code' => 'SP006', 
                'name' => 'UD. Garam Bersih', 
                'contact_person' => 'Maria Lestari', 
                'phone' => '0856-9988-7766', 
                'email' => 'maria.garam@example.com',
                'address' => 'Jl. Pantai Indah No. 22, Cirebon',
                'notes' => 'Khusus garam industri dan bahan pengawet sederhana.',
                'is_active' => true,
            ],
            // Supplier Kemasan
            [
                'code' => 'SP007', 
                'name' => 'Indah Packaging Solution', 
                'contact_person' => 'Taufik Hidayat', 
                'phone' => '021-1122-3344', 
                'email' => 'taufik.ips@example.com',
                'address' => 'Jl. Percetakan No. 45, Tangerang',
                'notes' => 'Menyediakan paper cup, tas kertas, dan kemasan plastik custom.',
                'is_active' => true,
            ],
            // Supplier Bahan Tambahan
            [
                'code' => 'SP008', 
                'name' => 'Teknologi Baking Cepat', 
                'contact_person' => 'Dewi Utami', 
                'phone' => '0811-0001-2223', 
                'email' => 'dewi.tbc@example.com',
                'address' => 'Ruko Sentra Bisnis Blok K9, Jakarta Selatan',
                'notes' => 'Supplier ragi, baking powder, dan bahan tambahan lainnya.',
                'is_active' => true,
            ],
            // Contoh Supplier Tidak Aktif
            [
                'code' => 'SP009', 
                'name' => 'Supplier Bangkrut Lama', 
                'contact_person' => 'Adam Malik', 
                'phone' => '021-9990001', 
                'email' => 'adam.malik@example.com',
                'address' => 'Jl. Kenangan No. 1, Jakarta',
                'notes' => 'Sudah tidak beroperasi sejak tahun lalu.',
                'is_active' => false, // Tidak Aktif
            ],
        ];

        foreach ($suppliers as $data) {
            Supplier::create(array_merge($data, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
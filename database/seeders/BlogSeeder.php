<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['Manajemen Bisnis', 'Bisnis Digital', 'Keuangan', 'Inovasi UMKM', 'Kepemimpinan', 'Pemasaran'];

        $articles = [
            [
                'title' => 'Mengapa Transformasi Digital Mutlak Dibutuhkan UMKM Era Kini',
                'content' => '<p>Perkembangan teknologi memicu pergeseran perilaku konsumen. Apabila bisnis Anda masih bertahan pada pencatatan manual, Anda telah melewatkan peluang efisiensi besar...</p><p>Penggunaan ekosistem seperti Flow memungkinkan tracking stok serta analisa pasar yang holistik.</p>',
                'category' => 'Bisnis Digital',
            ],
            [
                'title' => '5 Tips Manajemen Arus Kas Bebas Stres untuk Pengusaha Kuliner',
                'content' => '<p>Uang tunai adalah darah bagi setiap bisnis. Tanpa likuiditas yang cukup, ekspansi hanya sekadar janji. Menghitung pengeluaran dan pemasukan harian kini dapat diotomatiskan. Kunci utamanya adalah mengontrol pos piutang dan hutang dengan tenggat yang akurat.</p>',
                'category' => 'Keuangan',
            ],
            [
                'title' => 'Membangun Loyalitas Pelanggan Melalui Pendekatan Berbasis Data',
                'content' => '<p>Data pelanggan bukanlah sekadar angka, melainkan wawasan tentang referensi perilaku. Mengetahui menu apa yang sering dibeli pada hari minggu dapat meningkatkan konversi promosi mingguan hingga 40%.</p>',
                'category' => 'Manajemen Bisnis',
            ],
            [
                'title' => 'Strategi Omnichannel: Mengintegrasikan Penjualan Online dan Offline',
                'content' => '<p>Konsumen modern cenderung melakukan riset secara online namun bertransaksi di outlet secara offline, begitupun sebaliknya. Memadukan pengalaman ini secara kohesif adalah kunci pertumbuhan 10x lipat.</p>',
                'category' => 'Bisnis Digital',
            ],
            [
                'title' => 'Manajemen Konflik dalam Tim Multi-Generasi di Tempat Kerja',
                'content' => '<p>Adanya pergeseran antara Gen Z dan Millennial pada satu tim kerja menghasilkan dinamika baru. Pemimpin dituntut untuk lebih mendengar opini dan memfasilitasi komunikasi agar terjadi inovasi organik.</p>',
                'category' => 'Kepemimpinan',
            ],
            [
                'title' => 'Optimalisasi Pengelolaan Stok Bahan Baku Agar Bebas Rugi',
                'content' => '<p>Bahan makanan yang terbuang berarti membuang peluang profit. Dengan fitur kalkulasi stok dinamis, sisa bahan baku dapat digunakan secara efektif mengikuti algoritma resep digital.</p>',
                'category' => 'Manajemen Bisnis',
            ],
            [
                'title' => 'Trik Meracik Promosi Diskon Tanpa Mematikan Brand Value',
                'content' => '<p>Terlalu sering memberikan diskon membuat pelanggan menunda pembelian di harga normal. Solusinya? Terapkan strategi bundling atau paket loyalitas yang mempertahankan margin namun terlihat menguntungkan.</p>',
                'category' => 'Pemasaran',
            ],
            [
                'title' => 'Prediksi Tren Layanan Pesan Antar Konsumen Tahun Depan',
                'content' => '<p>Dengan gaya hidup pragmatis yang tinggi, preferensi pembelian makanan sedang mengarah kepada transparansi kualitas bahan serta efisiensi waktu antrean lokal.</p>',
                'category' => 'Inovasi UMKM',
            ],
            [
                'title' => 'Merekrut Talenta Tepat di Awal Tahap Startup Anda',
                'content' => '<p>Kesalahan merekrut bisa jadi fatal. Temukan nilai (core values) yang diyakini perusahaan Anda lalu saringlah kandidat berdasarkan kecocokan visi sebelum menguji kompetensi operasional mereka.</p>',
                'category' => 'Manajemen Bisnis',
            ],
            [
                'title' => 'Bagaimana Aplikasi Kasir Mengubah Lanskap Kompetisi Retail',
                'content' => '<p>Dulu, alat kasir hanya menghitung angka. Sekarang, POS yang berbasis komputasi cerdas sanggup berperan sebagai konsultan bisnis personal Anda lewat analisis matrik profit real-time.</p>',
                'category' => 'Bisnis Digital',
            ],
            [
                'title' => 'Pengertian Analisis Break-Even Point (BEP) Lengkap Bagi Pemula',
                'content' => '<p>Kapan modal usaha Anda akan kembali? Pahami formula perhitungan BEP untuk segera merealisasikan langkah-langkah pivot jika indikasi traksi pasar melambat.</p>',
                'category' => 'Keuangan',
            ],
            [
                'title' => 'Pentingnya User Experience (UX) Untuk Kesuksesan Aplikasi Toko',
                'content' => '<p>Secanggih apa pun fiturnya, apabila antarmuka menyulitkan kasir saat keadaan mengantre panjang, produk akan ditinggalkan. Kesederhanaan navigasi dan konsistensi warna adalah jaminan retensi operasi harian.</p>',
                'category' => 'Inovasi UMKM',
            ],
            [
                'title' => 'Cara Tepat Follow Up Pelanggan yang Memiliki Tunggakan Hutang',
                'content' => '<p>Pihak manapun tidak menyukai menagih hutang karena merasa tidak enak. Memakai fitur tagihan terjadwal via WhatsApp yang dihasilkan secara sistem membuat proses ini bebas friksi personal.</p>',
                'category' => 'Manajemen Bisnis',
            ],
            [
                'title' => 'Ekspansi Bisnis: Indikasi Tepat Kapan Membuka Outlet Cabang Baru',
                'content' => '<p>Arus permintaan (demand) harian jauh di atas kapasitas tampung? Inilah sinyal utama Anda wajib melakukan duplikasi kesuksesan. Riset geografis menjadi pilar selanjutnya.</p>',
                'category' => 'Pemasaran',
            ],
            [
                'title' => 'Mengapa Automasi Bisnis Sekarang Bukan Lagi Sekadar Pilihan',
                'content' => '<p>Fokuslah pada ekspansi. Serahkan perekapan data, pengaturan jam lembur pegawai, hingga laporan laba/rugi kepada ekosistem digital agar Anda bisa menikmati kebebasan waktu yang nyata (time freedom).</p>',
                'category' => 'Bisnis Digital',
            ],
        ];

        foreach ($articles as $index => $article) {
            Blog::create([
                'title' => $article['title'],
                'slug' => Str::slug($article['title']).'-'.uniqid(),
                'content' => $article['content'],
                'category' => $article['category'],
                'is_published' => true,
                'views' => rand(120, 4800),
                'created_at' => now()->subDays(rand(1, 60)),
                'updated_at' => now()->subDays(rand(1, 60)),
            ]);
        }
    }
}

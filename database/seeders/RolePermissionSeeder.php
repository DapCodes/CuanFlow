<?php

namespace Database\Seeders;

use App\Models\PermissionCategory;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Hapus data lama
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();
        DB::table('permission_categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ===========================
        // BUAT KATEGORI PERMISSION
        // ===========================
        $categories = [
            [
                'name' => 'Dashboard',
                'slug' => 'dashboard',
                'description' => 'Akses ke halaman dashboard dan analitik',
                'icon' => 'fa-solid fa-home',
                'color' => '#10b981',
                'order' => 1,
            ],
            [
                'name' => 'Point of Sale',
                'slug' => 'pos',
                'description' => 'Akses ke sistem kasir dan transaksi',
                'icon' => 'fa-solid fa-cash-register',
                'color' => '#f97316',
                'order' => 2,
            ],
            [
                'name' => 'Penjualan',
                'slug' => 'penjualan',
                'description' => 'Kelola data penjualan dan riwayat transaksi',
                'icon' => 'fa-solid fa-cart-shopping',
                'color' => '#ec4899',
                'order' => 3,
            ],
            [
                'name' => 'Diskon',
                'slug' => 'diskon',
                'description' => 'Kelola promo dan potongan harga',
                'icon' => 'fa-solid fa-tags',
                'color' => '#ef4444',
                'order' => 4,
            ],
            [
                'name' => 'Keuangan',
                'slug' => 'keuangan',
                'description' => 'Kelola pemasukan dan pengeluaran',
                'icon' => 'fa-solid fa-wallet',
                'color' => '#a855f7',
                'order' => 5,
            ],
            [
                'name' => 'Metode Pembayaran',
                'slug' => 'metode-pembayaran',
                'description' => 'Atur metode pembayaran outlet',
                'icon' => 'fa-solid fa-qrcode',
                'color' => '#ec4899',
                'order' => 6,
            ],
            [
                'name' => 'Statistik',
                'slug' => 'statistik',
                'description' => 'Lihat grafik dan analisis bisnis',
                'icon' => 'fa-solid fa-chart-line',
                'color' => '#3b82f6',
                'order' => 7,
            ],
            [
                'name' => 'Laporan',
                'slug' => 'laporan',
                'description' => 'Akses dan ekspor laporan bisnis',
                'icon' => 'fa-solid fa-file-invoice',
                'color' => '#6366f1',
                'order' => 8,
            ],
            [
                'name' => 'Produk & Resep',
                'slug' => 'produk',
                'description' => 'Kelola produk, resep, dan HPP',
                'icon' => 'fa-solid fa-utensils',
                'color' => '#84cc16',
                'order' => 9,
            ],
            [
                'name' => 'Bahan Baku',
                'slug' => 'bahan-baku',
                'description' => 'Kelola stok bahan baku',
                'icon' => 'fa-solid fa-boxes-stacked',
                'color' => '#f97316',
                'order' => 10,
            ],
            [
                'name' => 'Supplier',
                'slug' => 'supplier',
                'description' => 'Kelola data pemasok',
                'icon' => 'fa-solid fa-truck-field',
                'color' => '#f59e0b',
                'order' => 11,
            ],
            [
                'name' => 'Produksi',
                'slug' => 'produksi',
                'description' => 'Kelola proses produksi',
                'icon' => 'fa-solid fa-flask',
                'color' => '#3b82f6',
                'order' => 12,
            ],
            [
                'name' => 'Stock Opname',
                'slug' => 'stock-opname',
                'description' => 'Kelola pengecekan stok',
                'icon' => 'fa-solid fa-boxes-packing',
                'color' => '#22c55e',
                'order' => 13,
            ],
            [
                'name' => 'Outlet',
                'slug' => 'outlet',
                'description' => 'Kelola informasi outlet',
                'icon' => 'fa-solid fa-store',
                'color' => '#eab308',
                'order' => 14,
            ],
            [
                'name' => 'Landing Page',
                'slug' => 'landing-page',
                'description' => 'Kelola halaman promosi outlet',
                'icon' => 'fa-solid fa-rocket',
                'color' => '#a855f7',
                'order' => 15,
            ],
            [
                'name' => 'Testimoni',
                'slug' => 'testimoni',
                'description' => 'Kelola ulasan pelanggan',
                'icon' => 'fa-solid fa-quote-left',
                'color' => '#06b6d4',
                'order' => 16,
            ],
            [
                'name' => 'Pegawai',
                'slug' => 'pegawai',
                'description' => 'Kelola data karyawan',
                'icon' => 'fa-solid fa-users',
                'color' => '#14b8a6',
                'order' => 17,
            ],
            [
                'name' => 'Pelanggan & Piutang',
                'slug' => 'pelanggan-piutang',
                'description' => 'Kelola pelanggan dan hutang',
                'icon' => 'fa-solid fa-address-book',
                'color' => '#14b8a6',
                'order' => 18,
            ],
            [
                'name' => 'Meja',
                'slug' => 'meja',
                'description' => 'Kelola sistem meja',
                'icon' => 'fa-solid fa-chair',
                'color' => '#f59e0b',
                'order' => 19,
            ],
            [
                'name' => 'AI Insights',
                'slug' => 'ai-insights',
                'description' => 'Akses saran AI dari data bisnis',
                'icon' => 'fa-solid fa-lightbulb',
                'color' => '#8b5cf6',
                'order' => 20,
            ],
            [
                'name' => 'Clara AI',
                'slug' => 'clara-ai',
                'description' => 'Akses asisten AI',
                'icon' => 'fa-solid fa-robot',
                'color' => '#6366f1',
                'order' => 21,
            ],
            [
                'name' => 'Kebijakan Outlet',
                'slug' => 'kebijakan-outlet',
                'description' => 'Kelola SOP dan kebijakan',
                'icon' => 'fa-solid fa-clipboard-list',
                'color' => '#64748b',
                'order' => 22,
            ],
            [
                'name' => 'FAQ & Bantuan',
                'slug' => 'faq',
                'description' => 'Kelola panduan dan FAQ',
                'icon' => 'fa-solid fa-circle-question',
                'color' => '#14b8a6',
                'order' => 23,
            ],
            [
                'name' => 'Pengaturan Akun',
                'slug' => 'profil',
                'description' => 'Kelola profil pengguna',
                'icon' => 'fa-solid fa-user-gear',
                'color' => '#64748b',
                'order' => 24,
            ],
            [
                'name' => 'Struk',
                'slug' => 'struk',
                'description' => 'Kelola struk dan bukti pembayaran',
                'icon' => 'fa-solid fa-receipt',
                'color' => '#78716c',
                'order' => 25,
            ],
            [
                'name' => 'Role & Permission',
                'slug' => 'role-permission',
                'description' => 'Kelola hak akses pengguna',
                'icon' => 'fa-solid fa-shield-halved',
                'color' => '#dc2626',
                'order' => 26,
            ],
        ];

        $categoryIds = [];
        foreach ($categories as $category) {
            $created = PermissionCategory::create($category);
            $categoryIds[$category['slug']] = $created->id;
        }

        // ===========================
        // BUAT PERMISSIONS DENGAN KATEGORI
        // ===========================
        $permissions = [
            // Dashboard
            ['name' => 'lihat dashboard', 'category' => 'dashboard', 'description' => 'Melihat halaman utama dashboard'],
            ['name' => 'lihat analitik', 'category' => 'dashboard', 'description' => 'Melihat data analitik bisnis'],

            // Point of Sale (POS)
            ['name' => 'akses pos', 'category' => 'pos', 'description' => 'Mengakses halaman kasir'],
            ['name' => 'buka kasir', 'category' => 'pos', 'description' => 'Membuka shift kasir'],
            ['name' => 'tutup kasir', 'category' => 'pos', 'description' => 'Menutup shift kasir'],
            ['name' => 'lihat riwayat kasir', 'category' => 'pos', 'description' => 'Melihat riwayat shift kasir'],
            ['name' => 'atur saldo awal kasir', 'category' => 'pos', 'description' => 'Mengatur modal awal kasir'],
            ['name' => 'proses pembayaran tunai', 'category' => 'pos', 'description' => 'Memproses pembayaran cash'],
            ['name' => 'proses pembayaran transfer', 'category' => 'pos', 'description' => 'Memproses pembayaran transfer'],
            ['name' => 'proses pembayaran digital', 'category' => 'pos', 'description' => 'Memproses pembayaran Midtrans/QRIS'],
            ['name' => 'terapkan diskon pos', 'category' => 'pos', 'description' => 'Menerapkan diskon saat transaksi'],
            ['name' => 'batalkan transaksi', 'category' => 'pos', 'description' => 'Membatalkan transaksi yang sedang berjalan'],
            ['name' => 'cetak ulang struk', 'category' => 'pos', 'description' => 'Mencetak ulang struk penjualan'],
            ['name' => 'atur tampilan produk pos', 'category' => 'pos', 'description' => 'Mengatur visibilitas produk di POS'],
            ['name' => 'pilih meja pos', 'category' => 'pos', 'description' => 'Memilih meja saat transaksi'],
            ['name' => 'pilih pelanggan pos', 'category' => 'pos', 'description' => 'Memilih pelanggan saat transaksi'],

            // Penjualan
            ['name' => 'lihat penjualan', 'category' => 'penjualan', 'description' => 'Melihat daftar penjualan'],
            ['name' => 'lihat semua penjualan', 'category' => 'penjualan', 'description' => 'Melihat penjualan semua kasir'],
            ['name' => 'lihat detail penjualan', 'category' => 'penjualan', 'description' => 'Melihat detail transaksi'],
            ['name' => 'lihat penjualan harian', 'category' => 'penjualan', 'description' => 'Melihat ringkasan penjualan hari ini'],
            ['name' => 'ekspor penjualan', 'category' => 'penjualan', 'description' => 'Mengekspor data penjualan'],
            ['name' => 'refund penjualan', 'category' => 'penjualan', 'description' => 'Melakukan refund transaksi'],
            ['name' => 'cetak struk penjualan', 'category' => 'penjualan', 'description' => 'Mencetak struk dari riwayat'],
            ['name' => 'unduh struk penjualan', 'category' => 'penjualan', 'description' => 'Mengunduh struk sebagai file'],

            // Diskon
            ['name' => 'lihat diskon', 'category' => 'diskon', 'description' => 'Melihat daftar diskon'],
            ['name' => 'buat diskon', 'category' => 'diskon', 'description' => 'Membuat diskon baru'],
            ['name' => 'edit diskon', 'category' => 'diskon', 'description' => 'Mengubah data diskon'],
            ['name' => 'hapus diskon', 'category' => 'diskon', 'description' => 'Menghapus diskon'],
            ['name' => 'aktifkan nonaktifkan diskon', 'category' => 'diskon', 'description' => 'Mengubah status aktif diskon'],
            ['name' => 'generate kode diskon', 'category' => 'diskon', 'description' => 'Membuat kode diskon otomatis'],

            // Keuangan
            ['name' => 'lihat keuangan', 'category' => 'keuangan', 'description' => 'Melihat halaman keuangan'],
            ['name' => 'buat pemasukan', 'category' => 'keuangan', 'description' => 'Menambah data pemasukan'],
            ['name' => 'edit pemasukan', 'category' => 'keuangan', 'description' => 'Mengubah data pemasukan'],
            ['name' => 'hapus pemasukan', 'category' => 'keuangan', 'description' => 'Menghapus data pemasukan'],
            ['name' => 'buat pengeluaran', 'category' => 'keuangan', 'description' => 'Menambah data pengeluaran'],
            ['name' => 'edit pengeluaran', 'category' => 'keuangan', 'description' => 'Mengubah data pengeluaran'],
            ['name' => 'hapus pengeluaran', 'category' => 'keuangan', 'description' => 'Menghapus data pengeluaran'],
            ['name' => 'validasi pendapatan', 'category' => 'keuangan', 'description' => 'Memvalidasi data pendapatan'],
            ['name' => 'lihat keuangan harian', 'category' => 'keuangan', 'description' => 'Melihat ringkasan keuangan hari ini'],
            ['name' => 'lihat grafik keuangan', 'category' => 'keuangan', 'description' => 'Melihat grafik pendapatan dan pengeluaran'],

            // Metode Pembayaran
            ['name' => 'lihat metode pembayaran', 'category' => 'metode-pembayaran', 'description' => 'Melihat daftar metode pembayaran'],
            ['name' => 'buat metode pembayaran', 'category' => 'metode-pembayaran', 'description' => 'Menambah metode pembayaran'],
            ['name' => 'edit metode pembayaran', 'category' => 'metode-pembayaran', 'description' => 'Mengubah metode pembayaran'],
            ['name' => 'hapus metode pembayaran', 'category' => 'metode-pembayaran', 'description' => 'Menghapus metode pembayaran'],
            ['name' => 'aktifkan nonaktifkan metode pembayaran', 'category' => 'metode-pembayaran', 'description' => 'Mengubah status metode pembayaran'],

            // Statistik
            ['name' => 'lihat statistik', 'category' => 'statistik', 'description' => 'Melihat halaman statistik'],
            ['name' => 'ekspor statistik', 'category' => 'statistik', 'description' => 'Mengekspor data statistik'],
            ['name' => 'lihat grafik penjualan', 'category' => 'statistik', 'description' => 'Melihat grafik penjualan'],
            ['name' => 'lihat grafik produk terlaris', 'category' => 'statistik', 'description' => 'Melihat produk paling laku'],
            ['name' => 'lihat grafik kategori', 'category' => 'statistik', 'description' => 'Melihat penjualan per kategori'],
            ['name' => 'lihat grafik per jam', 'category' => 'statistik', 'description' => 'Melihat penjualan per jam'],

            // Laporan
            ['name' => 'lihat laporan', 'category' => 'laporan', 'description' => 'Melihat halaman laporan'],
            ['name' => 'ekspor laporan pdf', 'category' => 'laporan', 'description' => 'Mengekspor laporan ke PDF'],
            ['name' => 'ekspor laporan excel', 'category' => 'laporan', 'description' => 'Mengekspor laporan ke Excel'],

            // Produk & Resep
            ['name' => 'lihat produk', 'category' => 'produk', 'description' => 'Melihat daftar produk'],
            ['name' => 'buat produk', 'category' => 'produk', 'description' => 'Menambah produk baru'],
            ['name' => 'edit produk', 'category' => 'produk', 'description' => 'Mengubah data produk'],
            ['name' => 'hapus produk', 'category' => 'produk', 'description' => 'Menghapus produk'],
            ['name' => 'lihat detail produk', 'category' => 'produk', 'description' => 'Melihat detail produk'],
            ['name' => 'aktifkan nonaktifkan produk', 'category' => 'produk', 'description' => 'Mengubah status produk'],
            ['name' => 'generate kode produk', 'category' => 'produk', 'description' => 'Membuat kode produk otomatis'],
            ['name' => 'generate barcode produk', 'category' => 'produk', 'description' => 'Membuat barcode produk'],
            ['name' => 'unduh barcode produk', 'category' => 'produk', 'description' => 'Mengunduh barcode produk'],
            ['name' => 'lihat analitik produk', 'category' => 'produk', 'description' => 'Melihat analitik penjualan produk'],
            ['name' => 'generate resep ai', 'category' => 'produk', 'description' => 'Membuat resep dengan bantuan AI'],

            // Bahan Baku
            ['name' => 'lihat bahan baku', 'category' => 'bahan-baku', 'description' => 'Melihat daftar bahan baku'],
            ['name' => 'buat bahan baku', 'category' => 'bahan-baku', 'description' => 'Menambah bahan baku baru'],
            ['name' => 'edit bahan baku', 'category' => 'bahan-baku', 'description' => 'Mengubah data bahan baku'],
            ['name' => 'hapus bahan baku', 'category' => 'bahan-baku', 'description' => 'Menghapus bahan baku'],
            ['name' => 'lihat detail bahan baku', 'category' => 'bahan-baku', 'description' => 'Melihat detail bahan baku'],
            ['name' => 'kelola stok bahan baku', 'category' => 'bahan-baku', 'description' => 'Mengatur stok bahan baku'],
            ['name' => 'update stok bahan baku', 'category' => 'bahan-baku', 'description' => 'Memperbarui jumlah stok'],
            ['name' => 'lihat riwayat stok bahan baku', 'category' => 'bahan-baku', 'description' => 'Melihat riwayat perubahan stok'],

            // Supplier
            ['name' => 'lihat supplier', 'category' => 'supplier', 'description' => 'Melihat daftar supplier'],
            ['name' => 'buat supplier', 'category' => 'supplier', 'description' => 'Menambah supplier baru'],
            ['name' => 'edit supplier', 'category' => 'supplier', 'description' => 'Mengubah data supplier'],
            ['name' => 'hapus supplier', 'category' => 'supplier', 'description' => 'Menghapus supplier'],
            ['name' => 'lihat detail supplier', 'category' => 'supplier', 'description' => 'Melihat detail supplier'],

            // Produksi
            ['name' => 'lihat produksi', 'category' => 'produksi', 'description' => 'Melihat daftar produksi'],
            ['name' => 'buat produksi', 'category' => 'produksi', 'description' => 'Membuat order produksi baru'],
            ['name' => 'mulai produksi', 'category' => 'produksi', 'description' => 'Memulai proses produksi'],
            ['name' => 'selesaikan produksi', 'category' => 'produksi', 'description' => 'Menyelesaikan proses produksi'],
            ['name' => 'batalkan produksi', 'category' => 'produksi', 'description' => 'Membatalkan order produksi'],
            ['name' => 'hapus produk kadaluarsa', 'category' => 'produksi', 'description' => 'Menghapus produk yang kadaluarsa'],
            ['name' => 'lihat stok produksi', 'category' => 'produksi', 'description' => 'Melihat stok hasil produksi'],
            ['name' => 'lihat detail resep', 'category' => 'produksi', 'description' => 'Melihat detail resep produksi'],

            // Stock Opname
            ['name' => 'lihat stock opname', 'category' => 'stock-opname', 'description' => 'Melihat daftar stock opname'],
            ['name' => 'buat stock opname', 'category' => 'stock-opname', 'description' => 'Membuat stock opname baru'],
            ['name' => 'edit stock opname', 'category' => 'stock-opname', 'description' => 'Mengubah data stock opname'],
            ['name' => 'finalisasi stock opname', 'category' => 'stock-opname', 'description' => 'Menyelesaikan stock opname'],
            ['name' => 'hapus stock opname', 'category' => 'stock-opname', 'description' => 'Menghapus stock opname'],

            // Outlet
            ['name' => 'lihat outlet', 'category' => 'outlet', 'description' => 'Melihat informasi outlet'],
            ['name' => 'buat outlet', 'category' => 'outlet', 'description' => 'Mendaftarkan outlet baru'],
            ['name' => 'edit outlet', 'category' => 'outlet', 'description' => 'Mengubah informasi outlet'],
            ['name' => 'hapus outlet', 'category' => 'outlet', 'description' => 'Menghapus outlet'],
            ['name' => 'lihat detail outlet', 'category' => 'outlet', 'description' => 'Melihat detail outlet'],
            ['name' => 'aktifkan nonaktifkan outlet', 'category' => 'outlet', 'description' => 'Mengubah status outlet'],
            ['name' => 'lihat semua outlet', 'category' => 'outlet', 'description' => 'Melihat daftar semua outlet'],
            ['name' => 'ganti outlet', 'category' => 'outlet', 'description' => 'Berpindah antar outlet'],

            // Landing Page
            ['name' => 'lihat landing page', 'category' => 'landing-page', 'description' => 'Melihat halaman landing page'],
            ['name' => 'edit landing page', 'category' => 'landing-page', 'description' => 'Mengubah landing page'],
            ['name' => 'aktifkan nonaktifkan landing page', 'category' => 'landing-page', 'description' => 'Mengubah status landing page'],
            ['name' => 'lihat analitik landing page', 'category' => 'landing-page', 'description' => 'Melihat statistik kunjungan'],

            // Testimoni
            ['name' => 'lihat testimoni', 'category' => 'testimoni', 'description' => 'Melihat daftar testimoni'],
            ['name' => 'hapus testimoni', 'category' => 'testimoni', 'description' => 'Menghapus testimoni'],
            ['name' => 'aktifkan nonaktifkan testimoni', 'category' => 'testimoni', 'description' => 'Mengubah status testimoni'],
            ['name' => 'buat testimoni publik', 'category' => 'testimoni', 'description' => 'Membuat testimoni sebagai pelanggan'],

            // Pegawai
            ['name' => 'lihat pegawai', 'category' => 'pegawai', 'description' => 'Melihat daftar pegawai'],
            ['name' => 'buat pegawai', 'category' => 'pegawai', 'description' => 'Menambah pegawai baru'],
            ['name' => 'edit pegawai', 'category' => 'pegawai', 'description' => 'Mengubah data pegawai'],
            ['name' => 'hapus pegawai', 'category' => 'pegawai', 'description' => 'Menghapus pegawai'],
            ['name' => 'lihat detail pegawai', 'category' => 'pegawai', 'description' => 'Melihat detail pegawai'],
            ['name' => 'aktifkan nonaktifkan pegawai', 'category' => 'pegawai', 'description' => 'Mengubah status pegawai'],
            ['name' => 'kirim ulang verifikasi pegawai', 'category' => 'pegawai', 'description' => 'Mengirim ulang email verifikasi'],

            // Pelanggan & Piutang
            ['name' => 'lihat pelanggan', 'category' => 'pelanggan-piutang', 'description' => 'Melihat daftar pelanggan'],
            ['name' => 'buat pelanggan', 'category' => 'pelanggan-piutang', 'description' => 'Menambah pelanggan baru'],
            ['name' => 'edit pelanggan', 'category' => 'pelanggan-piutang', 'description' => 'Mengubah data pelanggan'],
            ['name' => 'hapus pelanggan', 'category' => 'pelanggan-piutang', 'description' => 'Menghapus pelanggan'],
            ['name' => 'lihat piutang', 'category' => 'pelanggan-piutang', 'description' => 'Melihat daftar piutang'],
            ['name' => 'lihat detail piutang', 'category' => 'pelanggan-piutang', 'description' => 'Melihat detail piutang'],
            ['name' => 'bayar piutang', 'category' => 'pelanggan-piutang', 'description' => 'Memproses pembayaran piutang'],
            ['name' => 'cari pelanggan', 'category' => 'pelanggan-piutang', 'description' => 'Mencari data pelanggan'],
            ['name' => 'kelola piutang pelanggan', 'category' => 'pelanggan-piutang', 'description' => 'Mengelola piutang pelanggan'],

            // Meja
            ['name' => 'lihat meja', 'category' => 'meja', 'description' => 'Melihat daftar meja'],
            ['name' => 'buat meja', 'category' => 'meja', 'description' => 'Menambah meja baru'],
            ['name' => 'edit meja', 'category' => 'meja', 'description' => 'Mengubah data meja'],
            ['name' => 'hapus meja', 'category' => 'meja', 'description' => 'Menghapus meja'],
            ['name' => 'aktifkan nonaktifkan meja', 'category' => 'meja', 'description' => 'Mengubah status meja'],
            ['name' => 'quick toggle meja', 'category' => 'meja', 'description' => 'Mengubah status meja dengan cepat'],
            ['name' => 'generate kode meja', 'category' => 'meja', 'description' => 'Membuat kode meja otomatis'],
            ['name' => 'toggle sistem meja outlet', 'category' => 'meja', 'description' => 'Mengaktifkan/menonaktifkan sistem meja'],

            // AI Insights
            ['name' => 'lihat ai insights', 'category' => 'ai-insights', 'description' => 'Melihat daftar insight AI'],
            ['name' => 'lihat detail ai insight', 'category' => 'ai-insights', 'description' => 'Melihat detail insight'],
            ['name' => 'tandai ai insight dibaca', 'category' => 'ai-insights', 'description' => 'Menandai insight sudah dibaca'],
            ['name' => 'abaikan ai insight', 'category' => 'ai-insights', 'description' => 'Mengabaikan insight'],
            ['name' => 'tandai semua ai insight dibaca', 'category' => 'ai-insights', 'description' => 'Menandai semua sudah dibaca'],
            ['name' => 'lihat kalender ai insight', 'category' => 'ai-insights', 'description' => 'Melihat kalender insight'],

            // Clara AI
            ['name' => 'akses clara ai', 'category' => 'clara-ai', 'description' => 'Mengakses Clara AI'],
            ['name' => 'chat dengan clara ai', 'category' => 'clara-ai', 'description' => 'Berkomunikasi dengan Clara AI'],
            ['name' => 'sesi baru clara ai', 'category' => 'clara-ai', 'description' => 'Membuat sesi chat baru'],
            ['name' => 'hapus sesi clara ai', 'category' => 'clara-ai', 'description' => 'Menghapus sesi chat'],

            // Kebijakan Outlet
            ['name' => 'lihat kebijakan outlet', 'category' => 'kebijakan-outlet', 'description' => 'Melihat daftar SOP'],
            ['name' => 'buat kebijakan outlet', 'category' => 'kebijakan-outlet', 'description' => 'Menambah SOP baru'],
            ['name' => 'edit kebijakan outlet', 'category' => 'kebijakan-outlet', 'description' => 'Mengubah SOP'],
            ['name' => 'hapus kebijakan outlet', 'category' => 'kebijakan-outlet', 'description' => 'Menghapus SOP'],
            ['name' => 'lihat detail kebijakan outlet', 'category' => 'kebijakan-outlet', 'description' => 'Melihat detail SOP'],

            // FAQ & Bantuan
            ['name' => 'lihat faq', 'category' => 'faq', 'description' => 'Melihat daftar FAQ'],
            ['name' => 'buat faq', 'category' => 'faq', 'description' => 'Menambah FAQ baru'],
            ['name' => 'edit faq', 'category' => 'faq', 'description' => 'Mengubah FAQ'],
            ['name' => 'hapus faq', 'category' => 'faq', 'description' => 'Menghapus FAQ'],
            ['name' => 'lihat detail faq', 'category' => 'faq', 'description' => 'Melihat detail FAQ'],
            ['name' => 'aktifkan nonaktifkan faq', 'category' => 'faq', 'description' => 'Mengubah status FAQ'],
            ['name' => 'tandai faq membantu', 'category' => 'faq', 'description' => 'Menandai FAQ berguna'],
            ['name' => 'tandai faq tidak membantu', 'category' => 'faq', 'description' => 'Menandai FAQ tidak berguna'],

            // Pengaturan Akun
            ['name' => 'edit profil', 'category' => 'profil', 'description' => 'Mengubah profil akun'],
            ['name' => 'update profil', 'category' => 'profil', 'description' => 'Menyimpan perubahan profil'],
            ['name' => 'hapus akun', 'category' => 'profil', 'description' => 'Menghapus akun sendiri'],

            // Struk
            ['name' => 'cetak struk', 'category' => 'struk', 'description' => 'Mencetak struk'],
            ['name' => 'unduh struk', 'category' => 'struk', 'description' => 'Mengunduh struk'],
            ['name' => 'preview struk', 'category' => 'struk', 'description' => 'Melihat preview struk'],
            ['name' => 'lihat struk publik', 'category' => 'struk', 'description' => 'Melihat struk publik'],

            // Role & Permission
            ['name' => 'lihat roles', 'category' => 'role-permission', 'description' => 'Melihat daftar role'],
            ['name' => 'buat roles', 'category' => 'role-permission', 'description' => 'Membuat role baru'],
            ['name' => 'edit roles', 'category' => 'role-permission', 'description' => 'Mengubah role'],
            ['name' => 'hapus roles', 'category' => 'role-permission', 'description' => 'Menghapus role'],
            ['name' => 'lihat permissions', 'category' => 'role-permission', 'description' => 'Melihat daftar permission'],
            ['name' => 'kelola permissions', 'category' => 'role-permission', 'description' => 'Mengelola permission'],
        ];

        foreach ($permissions as $perm) {
            Permission::create([
                'name' => $perm['name'],
                'guard_name' => 'web',
                'permission_category_id' => $categoryIds[$perm['category']] ?? null,
                'description' => $perm['description'],
            ]);
        }

        // ===========================
        // BUAT ROLES
        // ===========================

        // ADMIN - Full Access
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        // OWNER - Full Access (Pemilik Usaha)
        $ownerRole = Role::create(['name' => 'owner', 'guard_name' => 'web']);
        $ownerRole->syncPermissions(Permission::all());

        // SUPERVISOR - Pengawas Operasional
        $supervisorRole = Role::create(['name' => 'supervisor', 'guard_name' => 'web']);
        $supervisorRole->syncPermissions([
            // Dashboard
            'lihat dashboard', 'lihat analitik',
            // POS
            'akses pos', 'buka kasir', 'tutup kasir', 'lihat riwayat kasir',
            'atur saldo awal kasir', 'proses pembayaran tunai', 'proses pembayaran transfer',
            'proses pembayaran digital', 'terapkan diskon pos', 'batalkan transaksi',
            'cetak ulang struk', 'atur tampilan produk pos', 'pilih meja pos', 'pilih pelanggan pos',
            // Penjualan
            'lihat penjualan', 'lihat semua penjualan', 'lihat detail penjualan',
            'lihat penjualan harian', 'ekspor penjualan', 'refund penjualan',
            'cetak struk penjualan', 'unduh struk penjualan',
            // Diskon
            'lihat diskon', 'buat diskon', 'edit diskon', 'aktifkan nonaktifkan diskon', 'generate kode diskon',
            // Keuangan
            'lihat keuangan', 'buat pemasukan', 'edit pemasukan', 'buat pengeluaran', 'edit pengeluaran',
            'validasi pendapatan', 'lihat keuangan harian', 'lihat grafik keuangan',
            // Statistik
            'lihat statistik', 'ekspor statistik', 'lihat grafik penjualan',
            'lihat grafik produk terlaris', 'lihat grafik kategori', 'lihat grafik per jam',
            // Laporan
            'lihat laporan', 'ekspor laporan pdf', 'ekspor laporan excel',
            // Produk
            'lihat produk', 'buat produk', 'edit produk', 'lihat detail produk',
            'aktifkan nonaktifkan produk', 'generate kode produk', 'generate barcode produk',
            'unduh barcode produk', 'lihat analitik produk',
            // Bahan Baku
            'lihat bahan baku', 'buat bahan baku', 'edit bahan baku', 'lihat detail bahan baku',
            'kelola stok bahan baku', 'update stok bahan baku', 'lihat riwayat stok bahan baku',
            // Supplier
            'lihat supplier', 'buat supplier', 'edit supplier', 'lihat detail supplier',
            // Produksi
            'lihat produksi', 'buat produksi', 'mulai produksi', 'selesaikan produksi',
            'batalkan produksi', 'hapus produk kadaluarsa', 'lihat stok produksi', 'lihat detail resep',
            // Stock Opname
            'lihat stock opname', 'buat stock opname', 'edit stock opname', 'finalisasi stock opname',
            // Meja
            'lihat meja', 'buat meja', 'edit meja', 'aktifkan nonaktifkan meja', 'quick toggle meja', 'generate kode meja',
            // Pegawai
            'lihat pegawai', 'lihat detail pegawai',
            // Pelanggan & Piutang
            'lihat pelanggan', 'buat pelanggan', 'edit pelanggan', 'lihat piutang',
            'lihat detail piutang', 'bayar piutang', 'cari pelanggan', 'kelola piutang pelanggan',
            // AI
            'lihat ai insights', 'lihat detail ai insight', 'tandai ai insight dibaca',
            'abaikan ai insight', 'tandai semua ai insight dibaca', 'lihat kalender ai insight',
            'akses clara ai', 'chat dengan clara ai', 'sesi baru clara ai', 'hapus sesi clara ai',
            // Kebijakan
            'lihat kebijakan outlet', 'lihat detail kebijakan outlet',
            // FAQ
            'lihat faq', 'lihat detail faq', 'tandai faq membantu', 'tandai faq tidak membantu',
            // Profil
            'edit profil', 'update profil',
            // Struk
            'cetak struk', 'unduh struk', 'preview struk',
        ]);

        // KASIR
        $kasirRole = Role::create(['name' => 'kasir', 'guard_name' => 'web']);
        $kasirRole->syncPermissions([
            'lihat dashboard',
            // POS
            'akses pos', 'buka kasir', 'tutup kasir', 'lihat riwayat kasir',
            'atur saldo awal kasir', 'proses pembayaran tunai', 'proses pembayaran transfer',
            'proses pembayaran digital', 'terapkan diskon pos', 'cetak ulang struk',
            'pilih meja pos', 'pilih pelanggan pos',
            // Penjualan
            'lihat penjualan', 'lihat detail penjualan', 'lihat penjualan harian',
            'cetak struk penjualan', 'unduh struk penjualan',
            // Diskon
            'lihat diskon',
            // Produk
            'lihat produk', 'lihat detail produk',
            // Meja
            'lihat meja', 'quick toggle meja',
            // Pelanggan
            'lihat pelanggan', 'buat pelanggan', 'edit pelanggan', 'cari pelanggan',
            // Piutang
            'lihat piutang', 'lihat detail piutang', 'bayar piutang',
            // Kebijakan
            'lihat kebijakan outlet', 'lihat detail kebijakan outlet',
            // FAQ
            'lihat faq', 'lihat detail faq', 'tandai faq membantu', 'tandai faq tidak membantu',
            // Profil
            'edit profil', 'update profil',
            // Struk
            'cetak struk', 'unduh struk', 'preview struk',
        ]);

        // INVENTARIS (Gudang / Stock)
        $inventarisRole = Role::create(['name' => 'inventaris', 'guard_name' => 'web']);
        $inventarisRole->syncPermissions([
            'lihat dashboard',
            // Produk
            'lihat produk', 'buat produk', 'edit produk', 'lihat detail produk',
            'aktifkan nonaktifkan produk', 'generate kode produk', 'generate barcode produk', 'unduh barcode produk',
            // Bahan Baku
            'lihat bahan baku', 'buat bahan baku', 'edit bahan baku', 'hapus bahan baku',
            'lihat detail bahan baku', 'kelola stok bahan baku', 'update stok bahan baku', 'lihat riwayat stok bahan baku',
            // Supplier
            'lihat supplier', 'buat supplier', 'edit supplier', 'hapus supplier', 'lihat detail supplier',
            // Stock Opname
            'lihat stock opname', 'buat stock opname', 'edit stock opname', 'finalisasi stock opname', 'hapus stock opname',
            // Produksi
            'lihat produksi', 'lihat stok produksi', 'lihat detail resep',
            // Kebijakan
            'lihat kebijakan outlet', 'lihat detail kebijakan outlet',
            // FAQ
            'lihat faq', 'lihat detail faq', 'tandai faq membantu', 'tandai faq tidak membantu',
            // Profil
            'edit profil', 'update profil',
        ]);

        // PRODUKSI
        $produksiRole = Role::create(['name' => 'produksi', 'guard_name' => 'web']);
        $produksiRole->syncPermissions([
            'lihat dashboard',
            // Produk
            'lihat produk', 'lihat detail produk',
            // Bahan Baku
            'lihat bahan baku', 'lihat detail bahan baku', 'kelola stok bahan baku',
            'update stok bahan baku', 'lihat riwayat stok bahan baku',
            // Produksi
            'lihat produksi', 'buat produksi', 'mulai produksi', 'selesaikan produksi',
            'batalkan produksi', 'hapus produk kadaluarsa', 'lihat stok produksi', 'lihat detail resep',
            // Stock Opname
            'lihat stock opname',
            // Kebijakan
            'lihat kebijakan outlet', 'lihat detail kebijakan outlet',
            // FAQ
            'lihat faq', 'lihat detail faq', 'tandai faq membantu', 'tandai faq tidak membantu',
            // Profil
            'edit profil', 'update profil',
        ]);

        // PELANGGAN (Customer)
        $pelangganRole = Role::create(['name' => 'pelanggan', 'guard_name' => 'web']);
        $pelangganRole->syncPermissions([
            'lihat dashboard',
            // Struk
            'lihat struk publik',
            // Testimoni
            'buat testimoni publik',
            // FAQ
            'lihat faq', 'lihat detail faq', 'tandai faq membantu', 'tandai faq tidak membantu',
            // Profil
            'edit profil', 'update profil',
        ]);

        // Clear cache lagi
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}

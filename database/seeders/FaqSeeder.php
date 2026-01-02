<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Outlet;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil outlet pertama atau buat dummy
        $outlet = Outlet::first();
        
        if (!$outlet) {
            $this->command->warn('Tidak ada outlet. Seeder FAQ dibatalkan.');
            return;
        }

        $faqs = [
            // ========== GENERAL (Umum) ==========
            [
                'question' => 'Apa itu CuanFlow dan apa fungsinya?',
                'answer' => 'CuanFlow adalah sistem manajemen bisnis all-in-one yang membantu Anda mengelola kasir (POS), inventori, keuangan, laporan penjualan, hingga analisis bisnis dengan AI. Cocok untuk UMKM, restoran, cafe, dan toko retail.',
                'type' => 'general',
                'priority' => 'high',
                'order' => 1,
            ],
            [
                'question' => 'Bagaimana cara memulai menggunakan CuanFlow?',
                'answer' => 'Setelah mendaftar dan login, Anda akan diminta mendaftarkan outlet terlebih dahulu. Setelah itu, Anda bisa langsung mengakses semua fitur seperti POS, produk, keuangan, dan laporan. Ikuti tour guide yang muncul untuk panduan lengkap.',
                'type' => 'general',
                'priority' => 'high',
                'order' => 2,
            ],
            [
                'question' => 'Apakah CuanFlow bisa digunakan offline?',
                'answer' => 'Saat ini CuanFlow memerlukan koneksi internet untuk sinkronisasi data real-time. Namun, beberapa fitur seperti pencatatan transaksi POS dapat dilakukan offline dan akan otomatis tersinkron saat koneksi internet tersedia.',
                'type' => 'general',
                'priority' => 'medium',
                'order' => 3,
            ],
            [
                'question' => 'Berapa jumlah outlet yang bisa saya kelola?',
                'answer' => 'Anda bisa mengelola satu outlet per akun pada paket gratis. Untuk mengelola lebih dari satu outlet atau cabang, Anda bisa upgrade ke paket premium yang mendukung multi-outlet management.',
                'type' => 'general',
                'priority' => 'low',
                'order' => 4,
            ],
            [
                'question' => 'Apakah data saya aman di CuanFlow?',
                'answer' => 'Ya, sangat aman! Kami menggunakan enkripsi SSL/TLS untuk semua transmisi data, backup otomatis setiap hari, dan server yang aman. Data Anda juga tidak akan pernah dibagikan ke pihak ketiga tanpa izin Anda.',
                'type' => 'general',
                'priority' => 'high',
                'order' => 5,
            ],

            // ========== POS (Point of Sale) ==========
            [
                'question' => 'Bagaimana cara membuka kasir (POS)?',
                'answer' => 'Klik menu "Point of Sale" di dashboard. Anda akan diminta memasukkan modal awal kasir. Setelah itu, kasir terbuka dan siap menerima transaksi. Jangan lupa tutup kasir di akhir shift untuk rekap otomatis.',
                'type' => 'pos',
                'priority' => 'high',
                'order' => 1,
            ],
            [
                'question' => 'Bagaimana cara mencatat penjualan di POS?',
                'answer' => 'Di halaman POS, pilih produk yang akan dijual dengan mengklik atau scan barcode. Masukkan jumlah, pilih metode pembayaran (tunai/non-tunai), lalu klik "Bayar". Struk otomatis tercetak atau bisa dikirim via WhatsApp.',
                'type' => 'pos',
                'priority' => 'high',
                'order' => 2,
            ],
            [
                'question' => 'Apakah POS mendukung pembayaran non-tunai?',
                'answer' => 'Ya! POS CuanFlow mendukung berbagai metode pembayaran: tunai, transfer bank, e-wallet (OVO, GoPay, DANA), kartu kredit/debit, dan QRIS. Anda bisa pilih metode pembayaran saat checkout.',
                'type' => 'pos',
                'priority' => 'medium',
                'order' => 3,
            ],
            [
                'question' => 'Bagaimana cara memberikan diskon di POS?',
                'answer' => 'Saat checkout, klik tombol "Diskon" dan pilih diskon yang sudah Anda buat sebelumnya di menu Diskon. Anda juga bisa memberikan diskon manual dengan memasukkan nominal atau persentase langsung.',
                'type' => 'pos',
                'priority' => 'medium',
                'order' => 4,
            ],
            [
                'question' => 'Bagaimana cara menutup kasir di akhir shift?',
                'answer' => 'Klik tombol "Tutup Kasir" di halaman POS. Sistem akan otomatis menghitung total penjualan, modal awal, dan saldo akhir. Anda bisa cetak laporan closing kasir untuk arsip.',
                'type' => 'pos',
                'priority' => 'high',
                'order' => 5,
            ],
            [
                'question' => 'Apakah bisa mencetak atau mengirim struk digital?',
                'answer' => 'Ya! Setelah transaksi, Anda bisa cetak struk thermal atau kirim struk digital via WhatsApp langsung ke nomor pelanggan. Format struk bisa dikustomisasi di pengaturan outlet.',
                'type' => 'pos',
                'priority' => 'medium',
                'order' => 6,
            ],

            // ========== PRODUCT (Produk & Stok) ==========
            [
                'question' => 'Bagaimana cara menambah produk baru?',
                'answer' => 'Masuk ke menu "Produk & Resep", klik "Tambah Produk". Isi nama produk, harga jual, kategori, dan foto produk. Jika ada resep/komposisi bahan, Anda bisa tambahkan di bagian "Resep" untuk hitung HPP otomatis.',
                'type' => 'product',
                'priority' => 'high',
                'order' => 1,
            ],
            [
                'question' => 'Apa itu HPP dan bagaimana cara menghitungnya?',
                'answer' => 'HPP (Harga Pokok Penjualan) adalah total biaya bahan baku untuk membuat satu produk. CuanFlow menghitung HPP otomatis berdasarkan resep yang Anda input. HPP membantu Anda menentukan harga jual yang menguntungkan.',
                'type' => 'product',
                'priority' => 'high',
                'order' => 2,
            ],
            [
                'question' => 'Bagaimana cara mengatur stok minimum produk?',
                'answer' => 'Saat menambah atau edit produk, ada kolom "Stok Minimum". Isi angka ambang batas stok. Ketika stok mencapai angka tersebut, sistem akan memberi notifikasi agar Anda segera restock.',
                'type' => 'product',
                'priority' => 'medium',
                'order' => 3,
            ],
            [
                'question' => 'Bagaimana cara mengelola bahan baku?',
                'answer' => 'Masuk ke menu "Bahan Baku", tambahkan nama bahan, satuan, harga per satuan, dan stok awal. Bahan baku ini nantinya digunakan untuk membuat resep produk dan otomatis ter-update saat ada produksi atau penjualan.',
                'type' => 'product',
                'priority' => 'high',
                'order' => 4,
            ],
            [
                'question' => 'Apakah ada fitur scan barcode untuk produk?',
                'answer' => 'Ya! CuanFlow mendukung barcode scanning. Anda bisa input barcode saat menambah produk, lalu gunakan scanner atau kamera HP untuk scan barcode saat transaksi di POS.',
                'type' => 'product',
                'priority' => 'medium',
                'order' => 5,
            ],
            [
                'question' => 'Bagaimana cara mencatat produksi produk?',
                'answer' => 'Masuk ke menu "Produksi", pilih produk yang akan diproduksi, masukkan jumlah produksi. Sistem otomatis mengurangi stok bahan baku sesuai resep dan menambah stok produk jadi.',
                'type' => 'product',
                'priority' => 'medium',
                'order' => 6,
            ],

            // ========== FINANCE (Keuangan) ==========
            [
                'question' => 'Bagaimana cara mencatat pemasukan dan pengeluaran?',
                'answer' => 'Masuk ke menu "Keuangan", pilih tab "Pemasukan" atau "Pengeluaran". Klik "Tambah", isi deskripsi, nominal, kategori, dan tanggal transaksi. Semua transaksi akan terekam dan bisa dilihat di laporan keuangan.',
                'type' => 'finance',
                'priority' => 'high',
                'order' => 1,
            ],
            [
                'question' => 'Apa perbedaan pemasukan operasional dan non-operasional?',
                'answer' => 'Pemasukan operasional berasal dari aktivitas bisnis utama (penjualan produk). Pemasukan non-operasional berasal dari aktivitas lain seperti investasi, bunga bank, atau penjualan aset.',
                'type' => 'finance',
                'priority' => 'medium',
                'order' => 2,
            ],
            [
                'question' => 'Bagaimana cara melacak piutang pelanggan?',
                'answer' => 'Masuk ke menu "Pelanggan & Piutang". Di sini Anda bisa catat penjualan kredit, lacak siapa yang punya hutang, berapa jumlahnya, dan kapan jatuh tempo. Ada reminder otomatis untuk piutang yang hampir jatuh tempo.',
                'type' => 'finance',
                'priority' => 'high',
                'order' => 3,
            ],
            [
                'question' => 'Apakah transaksi POS otomatis masuk ke catatan keuangan?',
                'answer' => 'Ya! Semua transaksi penjualan di POS otomatis tercatat sebagai pemasukan di modul keuangan. Anda tidak perlu input manual lagi. Data langsung terintegrasi dan bisa dilihat di laporan.',
                'type' => 'finance',
                'priority' => 'high',
                'order' => 4,
            ],
            [
                'question' => 'Bagaimana cara mengatur kategori transaksi?',
                'answer' => 'Di menu Keuangan, ada pengaturan untuk membuat kategori pemasukan dan pengeluaran custom. Misalnya: gaji karyawan, listrik, air, promosi, dll. Kategori membantu Anda analisis pengeluaran lebih detail.',
                'type' => 'finance',
                'priority' => 'low',
                'order' => 5,
            ],

            // ========== REPORT (Laporan) ==========
            [
                'question' => 'Laporan apa saja yang tersedia di CuanFlow?',
                'answer' => 'CuanFlow menyediakan: Laporan Penjualan, Laporan Keuangan (Laba/Rugi), Laporan Stok, Laporan Produk Terlaris, Laporan Kasir, Laporan Pelanggan, dan Laporan Custom berdasarkan periode yang Anda pilih.',
                'type' => 'report',
                'priority' => 'high',
                'order' => 1,
            ],
            [
                'question' => 'Bagaimana cara melihat laporan penjualan harian?',
                'answer' => 'Masuk ke menu "Laporan Keseluruhan", pilih "Laporan Penjualan", set periode "Hari Ini" atau pilih tanggal tertentu. Laporan akan menampilkan total penjualan, jumlah transaksi, produk terlaris, dan metode pembayaran.',
                'type' => 'report',
                'priority' => 'high',
                'order' => 2,
            ],
            [
                'question' => 'Apakah laporan bisa di-export ke Excel atau PDF?',
                'answer' => 'Ya! Semua laporan bisa di-export ke format Excel (.xlsx) dan PDF. Klik tombol "Export" di pojok kanan atas halaman laporan, pilih format yang diinginkan, dan file akan otomatis terunduh.',
                'type' => 'report',
                'priority' => 'medium',
                'order' => 3,
            ],
            [
                'question' => 'Bagaimana cara melihat laporan laba rugi?',
                'answer' => 'Masuk ke "Dashboard & Statistik" atau "Laporan Keseluruhan", pilih "Laporan Keuangan". Anda akan melihat total pemasukan, pengeluaran, laba kotor, laba bersih, dan persentase profit margin.',
                'type' => 'report',
                'priority' => 'high',
                'order' => 4,
            ],
            [
                'question' => 'Apakah bisa membandingkan performa bulan ini dengan bulan lalu?',
                'answer' => 'Ya! Di Dashboard & Statistik, ada fitur perbandingan periode. Anda bisa bandingkan penjualan, laba, transaksi bulan ini vs bulan lalu, atau custom periode lainnya. Ada visualisasi grafik yang memudahkan analisis.',
                'type' => 'report',
                'priority' => 'medium',
                'order' => 5,
            ],

            // ========== ACCOUNT (Akun & Pengaturan) ==========
            [
                'question' => 'Bagaimana cara mengubah password akun?',
                'answer' => 'Masuk ke menu "Pengaturan Akun", pilih tab "Keamanan". Masukkan password lama, password baru, dan konfirmasi password baru. Klik "Simpan" untuk mengupdate password Anda.',
                'type' => 'account',
                'priority' => 'medium',
                'order' => 1,
            ],
            [
                'question' => 'Bagaimana cara menambah karyawan/pegawai?',
                'answer' => 'Masuk ke menu "Pegawai & Hak Akses", klik "Tambah Pegawai". Isi nama, email, nomor HP, dan set role/jabatan. Anda bisa atur hak akses untuk setiap pegawai (apa saja menu yang boleh diakses).',
                'type' => 'account',
                'priority' => 'high',
                'order' => 2,
            ],
            [
                'question' => 'Apa itu role dan bagaimana cara mengaturnya?',
                'answer' => 'Role adalah tingkat akses pengguna. Ada 3 role utama: Owner (akses penuh), Manager (akses hampir penuh), dan Kasir (akses terbatas ke POS). Anda bisa custom role dan tentukan menu apa saja yang bisa diakses.',
                'type' => 'account',
                'priority' => 'medium',
                'order' => 3,
            ],
            [
                'question' => 'Bagaimana cara mengubah informasi outlet?',
                'answer' => 'Masuk ke "Informasi Outlet", klik "Edit Outlet". Anda bisa ubah nama outlet, alamat, nomor telepon, jam operasional, logo, dan informasi lainnya. Perubahan akan langsung terlihat di struk dan laporan.',
                'type' => 'account',
                'priority' => 'medium',
                'order' => 4,
            ],
            [
                'question' => 'Apakah bisa menghapus akun CuanFlow?',
                'answer' => 'Ya, Anda bisa request hapus akun dengan menghubungi tim support kami. Namun, perlu diingat bahwa semua data akan terhapus permanen dan tidak bisa dikembalikan. Pastikan backup data penting sebelum menghapus akun.',
                'type' => 'account',
                'priority' => 'low',
                'order' => 5,
            ],

            // ========== TAMBAHAN: AI & Insight ==========
            [
                'question' => 'Apa itu Clara AI dan bagaimana cara menggunakannya?',
                'answer' => 'Clara AI adalah asisten pintar yang bisa menjawab pertanyaan tentang bisnis Anda. Tanyakan apa saja seperti "Berapa total penjualan bulan ini?" atau "Produk apa yang paling laris?". Clara akan memberi jawaban berdasarkan data real Anda.',
                'type' => 'general',
                'priority' => 'high',
                'order' => 10,
            ],
            [
                'question' => 'Apa itu Insight dan bagaimana cara membacanya?',
                'answer' => 'Insight adalah saran otomatis dari AI berdasarkan analisis data bisnis Anda. Misalnya: produk yang stoknya menipis, tren penjualan menurun, atau rekomendasi produk yang perlu dipromosikan. Cek Insight secara berkala untuk optimasi bisnis.',
                'type' => 'general',
                'priority' => 'medium',
                'order' => 11,
            ],
            [
                'question' => 'Bagaimana cara mengakses Bantuan & FAQ?',
                'answer' => 'Klik menu "Bantuan & FAQ" di dashboard. Anda bisa cari pertanyaan dengan search bar atau pilih kategori tertentu. Semua panduan dan jawaban pertanyaan umum ada di sini.',
                'type' => 'general',
                'priority' => 'low',
                'order' => 12,
            ],
        ];

        foreach ($faqs as $faqData) {
            Faq::create([
                'outlet_id' => $outlet->id,
                'question' => $faqData['question'],
                'answer' => $faqData['answer'],
                'type' => $faqData['type'],
                'priority' => $faqData['priority'],
                'is_active' => true,
                'view_count' => rand(0, 50),
                'helpful_count' => rand(0, 30),
                'not_helpful_count' => rand(0, 5),
                'order' => $faqData['order'],
            ]);
        }

        $this->command->info('✅ FAQ Seeder berhasil! Total: ' . count($faqs) . ' FAQs');
    }
}
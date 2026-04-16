<?php

namespace Database\Seeders;

use App\Models\TermsAndCondition;
use Illuminate\Database\Seeder;

class TermsAndConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $content = '
            <h1>Syarat dan Ketentuan Penggunaan CuanFlow</h1>
            <p>Selamat datang di <strong>CuanFlow</strong>. Dengan mengakses dan menggunakan platform kami, Anda setuju untuk terikat oleh Syarat dan Ketentuan berikut. Harap baca dengan seksama sebelum menggunakan layanan kami.</p>

            <h2>1. Definisi Layanan</h2>
            <p>CuanFlow adalah platform manajemen bisnis berbasis cloud yang menyediakan fitur Point of Sale (POS), manajemen inventaris, laporan keuangan otomasi, dan analitik berbasis AI (Clara AI). Layanan ini dirancang untuk mempermudah operasional bisnis UMKM hingga perusahaan menengah.</p>

            <h2>2. Pendaftaran Akun dan Keamanan</h2>
            <ul>
                <li>Pengguna wajib memberikan informasi yang akurat dan lengkap saat proses pendaftaran.</li>
                <li>Anda bertanggung jawab penuh atas keamanan kata sandi dan aktivitas yang terjadi di bawah akun Anda.</li>
                <li>CuanFlow berhak menonaktifkan akun jika ditemukan adanya pelanggaran keamanan atau penggunaan yang mencurigakan.</li>
            </ul>

            <h2>3. Penggunaan Layanan (SaaS)</h2>
            <p>Anda diberikan hak non-eksklusif untuk menggunakan platform CuanFlow sesuai dengan paket langganan yang dipilih. Anda dilarang untuk:</p>
            <ul>
                <li>Menjual kembali, menyewakan, atau mendistribusikan layanan tanpa izin tertulis dari pihak CuanFlow.</li>
                <li>Melakukan reverse engineering atau mencoba mengakses kode sumber aplikasi.</li>
                <li>Menggunakan sistem untuk aktivitas ilegal yang melanggar hukum di Republik Indonesia.</li>
            </ul>

            <h2>4. Pembayaran dan Langganan</h2>
            <p>Beberapa fitur CuanFlow mungkin memerlukan biaya langganan bulanan atau tahunan. Seluruh pembayaran bersifat final dan tidak dapat dikembalikan (non-refundable), kecuali ditentukan lain oleh kebijakan promosi yang berlaku. Kegagalan pembayaran dapat mengakibatkan pembatasan akses ke fitur-fitur tertentu.</p>

            <h2>5. Kerahasiaan Data dan Privasi</h2>
            <p>CuanFlow sangat menjunjung tinggi privasi data Anda. Semua data transaksi, stok, dan pelanggan yang Anda masukkan ke dalam sistem adalah milik Anda sepenuhnya. Kami tidak akan membagikan data tersebut kepada pihak ketiga tanpa izin, kecuali diwajibkan oleh hukum.</p>

            <h2>6. Fitur AI (Clara AI) dan Analitik</h2>
            <p>Fitur AI kami memberikan saran berdasarkan data yang ada. Meskipun kami terus mengoptimalkan akurasi, CuanFlow tidak bertanggung jawab atas keputusan bisnis yang diambil berdasarkan saran dari sistem AI. Semua keputusan strategis tetap berada di tangan pemilik bisnis.</p>

            <h2>7. Batasan Tanggung Jawab</h2>
            <p>CuanFlow tidak bertanggung jawab atas kerugian finansial atau operasional yang disebabkan oleh gangguan teknis di luar kendali kami (seperti gangguan penyedia hosting atau koneksi internet pengguna). Kami berkomitmen untuk menjaga ketersediaan layanan (uptime) hingga 99.9%.</p>

            <h2>8. Perubahan Syarat dan Ketentuan</h2>
            <p>CuanFlow berhak memperbarui Syarat dan Ketentuan ini sewaktu-waktu. Perubahan tersebut akan berlaku segera setelah dipublikasikan di halaman ini. Penggunaan berkelanjutan Anda terhadap layanan setelah perubahan tersebut dianggap sebagai persetujuan Anda.</p>

            <p>Jika Anda memiliki pertanyaan mengenai Syarat dan Ketentuan ini, silakan hubungi tim dukungan kami melalui pusat bantuan di dalam aplikasi.</p>
        ';

        TermsAndCondition::truncate();
        TermsAndCondition::create([
            'content' => $content,
        ]);
    }
}

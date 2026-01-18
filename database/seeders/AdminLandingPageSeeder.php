<?php

namespace Database\Seeders;

use App\Models\AdminLandingPage;
use App\Models\AdminLandingSection;
use App\Models\AdminLandingSectionItem;
use App\Models\AdminLandingCta;
use Illuminate\Database\Seeder;

class AdminLandingPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main Flow landing page
        $landingPage = AdminLandingPage::create([
            'title' => 'Flow – All in One Business App',
            'tagline' => 'One ecosystem to run your business smarter',
            'slug' => 'flow',
            'primary_color' => '#658C58',
            'secondary_color' => '#31694E',
            'accent_color' => '#F0E491',
            'font_family' => 'Inter',
            'meta_title' => 'Flow – All in One Business App | Kelola Bisnis Lebih Mudah',
            'meta_description' => 'Flow adalah platform all-in-one untuk mengelola POS, inventori, keuangan, dan tim dalam satu aplikasi.',
            'is_active' => true,
        ]);

        // Update sections with content
        $sectionsData = [
            'hero' => [
                'title' => 'Kelola Bisnis Anda dengan Satu Platform',
                'subtitle' => 'Flow Business App',
                'description' => 'Dari penjualan hingga laporan keuangan, semua terintegrasi dalam satu ekosistem yang mudah digunakan. Tingkatkan produktivitas bisnis Anda hingga 10x lipat.',
                'is_active' => true,
                'order' => 1,
            ],
            'about' => [
                'title' => 'Mengapa Memilih Flow?',
                'subtitle' => 'Tentang Kami',
                'description' => "Flow dirancang khusus untuk pemilik bisnis yang ingin fokus pada pertumbuhan, bukan administrasi yang rumit.\n\nDengan antarmuka yang intuitif dan fitur yang powerful, Flow membantu ribuan bisnis di Indonesia mengelola operasional mereka dengan lebih efisien.",
                'is_active' => true,
                'order' => 2,
            ],
            'features' => [
                'title' => 'Semua yang Anda Butuhkan',
                'subtitle' => 'Fitur Unggulan',
                'description' => 'Fitur lengkap untuk mendukung setiap aspek bisnis Anda',
                'is_active' => true,
                'order' => 3,
            ],
            'benefits' => [
                'title' => 'Manfaat Menggunakan Flow',
                'subtitle' => 'Keuntungan',
                'description' => '',
                'is_active' => true,
                'order' => 4,
            ],
            'statistics' => [
                'title' => 'Dipercaya Ribuan Bisnis',
                'subtitle' => 'Di seluruh Indonesia',
                'is_active' => true,
                'order' => 5,
            ],
            'testimonial' => [
                'title' => 'Apa Kata Mereka?',
                'subtitle' => 'Testimonial',
                'description' => '',
                'is_active' => true,
                'order' => 6,
            ],
            'faq' => [
                'title' => 'Pertanyaan Umum',
                'subtitle' => 'FAQ',
                'description' => '',
                'is_active' => true,
                'order' => 7,
            ],
            'cta' => [
                'title' => '',
                'subtitle' => '',
                'is_active' => true,
                'order' => 8,
            ],
            'footer' => [
                'title' => '',
                'subtitle' => '',
                'description' => '© ' . date('Y') . ' Flow. All rights reserved.',
                'is_active' => true,
                'order' => 9,
            ],
        ];

        // Update each section
        foreach ($sectionsData as $key => $data) {
            $section = $landingPage->sections()->where('section_key', $key)->first();
            if ($section) {
                $section->update($data);
            }
        }

        // Add Features Items
        $featuresSection = $landingPage->getSection('features');
        if ($featuresSection) {
            $features = [
                [
                    'title' => 'Point of Sale (POS)',
                    'description' => 'Sistem kasir modern dengan interface yang mudah digunakan. Terima berbagai metode pembayaran dan cetak struk instan.',
                    'icon' => 'fas fa-cash-register',
                    'order' => 1,
                ],
                [
                    'title' => 'Manajemen Inventori',
                    'description' => 'Pantau stok real-time, atur minimum stok, dan dapatkan notifikasi otomatis saat stok menipis.',
                    'icon' => 'fas fa-boxes-stacked',
                    'order' => 2,
                ],
                [
                    'title' => 'Laporan Keuangan',
                    'description' => 'Laporan penjualan, laba rugi, dan arus kas yang mudah dipahami dan bisa diakses kapan saja.',
                    'icon' => 'fas fa-chart-line',
                    'order' => 3,
                ],
                [
                    'title' => 'Manajemen Tim',
                    'description' => 'Kelola jam kerja, izin, dan performa karyawan dalam satu dashboard terintegrasi.',
                    'icon' => 'fas fa-users',
                    'order' => 4,
                ],
                [
                    'title' => 'Multi Outlet',
                    'description' => 'Kelola beberapa cabang dari satu akun. Pantau performa setiap outlet secara real-time.',
                    'icon' => 'fas fa-store',
                    'order' => 5,
                ],
                [
                    'title' => 'Diskon & Promo',
                    'description' => 'Buat berbagai jenis diskon dan promo untuk meningkatkan penjualan dan loyalitas pelanggan.',
                    'icon' => 'fas fa-tags',
                    'order' => 6,
                ],
            ];

            foreach ($features as $feature) {
                $featuresSection->items()->create($feature);
            }
        }

        // Add Benefits Items
        $benefitsSection = $landingPage->getSection('benefits');
        if ($benefitsSection) {
            $benefits = [
                [
                    'title' => 'Hemat Waktu',
                    'description' => 'Otomatisasi proses bisnis yang memakan waktu, sehingga Anda bisa fokus pada hal yang lebih penting.',
                    'icon' => 'fas fa-clock',
                    'order' => 1,
                ],
                [
                    'title' => 'Akses Dimana Saja',
                    'description' => 'Pantau bisnis Anda dari mana saja menggunakan smartphone atau laptop Anda.',
                    'icon' => 'fas fa-globe',
                    'order' => 2,
                ],
                [
                    'title' => 'Data Aman',
                    'description' => 'Data bisnis Anda tersimpan aman di cloud dengan enkripsi tingkat enterprise.',
                    'icon' => 'fas fa-shield-halved',
                    'order' => 3,
                ],
                [
                    'title' => 'Support 24/7',
                    'description' => 'Tim support kami siap membantu Anda kapan saja melalui chat, email, atau telepon.',
                    'icon' => 'fas fa-headset',
                    'order' => 4,
                ],
            ];

            foreach ($benefits as $benefit) {
                $benefitsSection->items()->create($benefit);
            }
        }

        // Add Statistics Items
        $statsSection = $landingPage->getSection('statistics');
        if ($statsSection) {
            $stats = [
                ['title' => 'Pengguna Aktif', 'extra_data' => ['value' => '10000'], 'order' => 1],
                ['title' => 'Bisnis', 'extra_data' => ['value' => '500'], 'order' => 2],
                ['title' => 'Transaksi/Hari', 'extra_data' => ['value' => '50000'], 'order' => 3],
                ['title' => 'Uptime %', 'extra_data' => ['value' => '99'], 'order' => 4],
            ];

            foreach ($stats as $stat) {
                $statsSection->items()->create($stat);
            }
        }

        // Add Testimonial Items
        $testimonialSection = $landingPage->getSection('testimonial');
        if ($testimonialSection) {
            $testimonials = [
                [
                    'title' => 'Budi Santoso',
                    'description' => 'Flow benar-benar mengubah cara saya mengelola bisnis. Dulu saya menghabiskan waktu berjam-jam untuk rekap penjualan, sekarang semuanya otomatis!',
                    'extra_data' => ['rating' => 5, 'role' => 'Pemilik Warung Kopi'],
                    'order' => 1,
                ],
                [
                    'title' => 'Siti Rahayu',
                    'description' => 'Fitur multi outlet sangat membantu saya yang punya 3 cabang. Bisa pantau semuanya dari HP tanpa harus keliling.',
                    'extra_data' => ['rating' => 5, 'role' => 'Pemilik Toko Retail'],
                    'order' => 2,
                ],
                [
                    'title' => 'Ahmad Yusuf',
                    'description' => 'Support-nya luar biasa responsif. Pernah ada masalah di tengah malam dan tim Flow langsung bantu selesaikan.',
                    'extra_data' => ['rating' => 4, 'role' => 'Resto Owner'],
                    'order' => 3,
                ],
            ];

            foreach ($testimonials as $testimonial) {
                $testimonialSection->items()->create($testimonial);
            }
        }

        // Add FAQ Items
        $faqSection = $landingPage->getSection('faq');
        if ($faqSection) {
            $faqs = [
                [
                    'title' => 'Apakah Flow bisa digunakan offline?',
                    'description' => 'Ya, Flow memiliki mode offline yang memungkinkan Anda tetap melakukan transaksi meskipun tidak ada koneksi internet. Data akan otomatis disinkronkan saat koneksi kembali.',
                    'order' => 1,
                ],
                [
                    'title' => 'Berapa biaya berlangganan Flow?',
                    'description' => 'Flow menyediakan berbagai paket mulai dari gratis hingga enterprise. Anda bisa memilih paket sesuai kebutuhan bisnis Anda. Hubungi tim sales kami untuk informasi lebih lanjut.',
                    'order' => 2,
                ],
                [
                    'title' => 'Apakah data saya aman?',
                    'description' => 'Keamanan data adalah prioritas kami. Semua data dienkripsi dan disimpan di server yang aman dengan backup otomatis setiap hari.',
                    'order' => 3,
                ],
                [
                    'title' => 'Bagaimana cara migrasi dari sistem lama?',
                    'description' => 'Tim kami akan membantu proses migrasi data dari sistem lama Anda ke Flow secara gratis. Kami memastikan tidak ada data yang hilang dalam proses migrasi.',
                    'order' => 4,
                ],
                [
                    'title' => 'Apakah ada pelatihan penggunaan?',
                    'description' => 'Ya, kami menyediakan pelatihan gratis untuk semua pengguna baru. Anda juga bisa mengakses video tutorial dan dokumentasi lengkap di help center kami.',
                    'order' => 5,
                ],
            ];

            foreach ($faqs as $faq) {
                $faqSection->items()->create($faq);
            }
        }

        // Create CTA
        AdminLandingCta::create([
            'landing_page_id' => $landingPage->id,
            'headline' => 'Siap untuk Level Up Bisnis Anda?',
            'description' => 'Bergabung dengan ribuan pebisnis yang sudah merasakan kemudahan mengelola bisnis dengan Flow.',
            'button_text' => 'Coba Gratis Sekarang',
            'button_link' => route('register'),
            'button_color' => '#658C58',
            'secondary_button_text' => 'Jadwalkan Demo',
            'secondary_button_link' => '#',
        ]);

        $this->command->info('Admin Landing Page seeded successfully!');
    }
}

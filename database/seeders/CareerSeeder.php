<?php

namespace Database\Seeders;

use App\Models\Career;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CareerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $careers = [
            [
                'title' => 'Senior Fullstack Developer',
                'location' => 'Bandung, Indonesia / Full-Remote',
                'type' => 'Penuh Waktu',
                'salary_range' => 'Rp 12.000.000 - Rp 25.000.000',
                'description' => '<p>Kami mencari <strong>Senior Fullstack Developer</strong> yang berpengalaman untuk memimpin dan membangun infrastruktur backend dan pengembangan produk Flow Ecosystem secara komprehensif. Peran ini membutuhkan kemampuan teknis tinggi sekaligus ketangkasan dalam arsitektur sistem skala besar.</p>
                <p>Dalam peran ini, Anda akan secara aktif merancang struktur aplikasi yang robust dengan Laravel, mengatur manajemen antrean, websockets, dan berkoordinasi secara langsung dengan tim produk. Kami sangat mengedepankan kultur <em>ownership</em> - jika Anda melihat hal yang dapat ditingkatkan baik efisiensinya atau UX-nya, kami mendukung Anda untuk mengeksekusinya.</p>',
                'requirements' => '<ul>
                    <li>Minimal 4+ tahun pengalaman profesional dalam rekayasa perangkat lunak web.</li>
                    <li>Pengalaman matang dengan ekosistem Laravel (Queue, Jobs, Events, Eloquent Optimization) dan PHP 8+.</li>
                    <li>Keahlian kuat di sisi frontend menggunakan Vue.js, React, atau framework modern lain beserta Alpine.js / Tailwind CSS.</li>
                    <li>Pemahaman mendalam mengenai arsitektur database relasional (MySQL/PostgreSQL) dan caching layer (Redis).</li>
                    <li>Pemahaman mengenai arsitektur microservices dan integrasi API pihak ketiga (payment gateway seperti Midtrans, dll).</li>
                    <li>Memiliki semangat membimbing engineer junior dan menyukai tantangan penyelesaian algoritma asinkron.</li>
                </ul>',
                'is_active' => true,
                'deadline' => now()->addDays(30),
            ],
            [
                'title' => 'Growth & Digital Marketing Specialist',
                'location' => 'Jakarta, Indonesia / Hybrid',
                'type' => 'Penuh Waktu',
                'salary_range' => 'Rp 8.000.000 - Rp 15.000.000',
                'description' => '<p>Sebagai <strong>Growth & Digital Marketing Specialist</strong> di Flow Ecosystem, misi utama Anda adalah merancang dan mengeksekusi strategi kampanye untuk mengakuisisi pengguna UMKM di seluruh Indonesia agar mereka mengenal betapa revolusionernya platform CuanFlow.</p>
                <p>Anda akan berkolaborasi dengan tim desain kreatif untuk menyusun pitch produk, merumuskan copywriting, serta melakukan eksperimen taktis terkait A/B Testing, Facebook/Meta Ads, Google Ads, dan kampanye SEO. Pengambilan keputusan berbasis data yang sangat akurat adalah bagian utama dari kultur pemasaran kami.</p>',
                'requirements' => '<ul>
                    <li>Minimal 2-3 tahun rekam jejak yang terbukti (proven track record) dalam melakukan konversi melalui performance marketing.</li>
                    <li>Keahlian operasional mendalam terkait Facebook Business Manager, Google Ads, dan Google Analytics.</li>
                    <li>Pemahaman solid tentang fundamental funnel AARRR (Acquisition, Activation, Retention, Referral, Revenue).</li>
                    <li>Bakat mumpuni dalam merangkai copywriting persuasif yang menjiwai suara brand Flow.</li>
                    <li>Pola pikir yang kreatif namun bertolak ukur pada analisis Data (Data-driven mindset).</li>
                    <li>Memiliki pengalaman bekerja secara erat mengeksekusi strategi afiliasi pemasaran (nilai tambah).</li>
                </ul>',
                'is_active' => true,
                'deadline' => now()->addDays(45),
            ],
        ];

        foreach ($careers as $career) {
            $career['slug'] = Str::slug($career['title']).'-'.uniqid();
            Career::create($career);
        }
    }
}

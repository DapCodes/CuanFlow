@extends('layouts.landing')

@section('title', 'Tentang Kami — Flow Ecosystem')

@section('content')
<section class="page-hero">
    <div class="container" data-aos="fade-up">
        <span class="eyebrow" style="display: inline-flex; align-items: center; gap: 8px; background: var(--yellow); color: var(--dark-green); padding: 6px 14px; border-radius: 100px; font-size: 0.8rem; font-weight: 500; letter-spacing: 0.04em; text-transform: uppercase; margin-bottom: 24px;">
            MISI KAMI
        </span>
        <h1>Menjembatani Dunia <em>Bisnis & Konsumen</em></h1>
        <p style="color: var(--ink-2); font-size: 1.1rem; max-width: 600px; margin: 0 auto; font-weight: 300;">Kami percaya bahwa teknologi terbaik adalah yang memudahkan setiap langkah, baik bagi pengusaha maupun pelanggan.</p>
    </div>
</section>

<section class="py-120">
    <div class="container">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 md:gap-20 items-center">
            <div data-aos="fade-right">
                <h2 style="font-family: var(--serif); font-size: 2.5rem; margin-bottom: 24px;">Siapa Kami?</h2>
                <p style="color: var(--ink-2); margin-bottom: 20px; font-size: 1.05rem;">Flow adalah ekosistem digital revolusioner di Indonesia yang menggabungkan kekuatan manajemen operasional bisnis (CuanFlow) dengan pengalaman belanja konsumen yang menyenangkan (JajanFlow).</p>
                <p style="color: var(--ink-2); font-size: 1.05rem;">Lahir dari kebutuhan akan integrasi yang lebih baik, kami hadir untuk memastikan setiap transaksi bukan sekadar angka, tapi sebuah cerita sukses.</p>
            </div>
            <div data-aos="fade-left" style="background: var(--navy); border-radius: 32px; overflow: hidden; aspect-ratio: 16/10;">
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&q=80&w=800" alt="Team" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.8;">
            </div>
        </div>
    </div>
</section>

<section class="py-120" style="background: var(--white);">
    <div class="container">
        <div class="section-header">
            <h2>Nilai-Nilai Kami</h2>
            <p>Dasar dari setiap baris kode yang kami tulis dan setiap keputusan yang kami ambil.</p>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 32px;">
            <div data-aos="fade-up" style="padding: 40px; border-radius: 24px; background: var(--paper); border: 1px solid rgba(0,0,0,0.05);">
                <div style="width: 48px; height: 48px; background: var(--accent); color: white; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 24px;">
                    <i class="fas fa-heart"></i>
                </div>
                <h4 style="font-family: var(--serif); font-size: 1.3rem; margin-bottom: 12px;">Inovasi yang Peduli</h4>
                <p style="font-size: 0.95rem; color: var(--ink-2); font-weight: 300;">Kami menciptakan solusi bukan hanya karena keren, tapi karena benar-benar membantu masalah nyata pengusaha UMKM.</p>
            </div>
            <div data-aos="fade-up" data-aos-delay="100" style="padding: 40px; border-radius: 24px; background: var(--paper); border: 1px solid rgba(0,0,0,0.05);">
                <div style="width: 48px; height: 48px; background: var(--accent); color: white; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 24px;">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h4 style="font-family: var(--serif); font-size: 1.3rem; margin-bottom: 12px;">Integritas & Kepercayaan</h4>
                <p style="font-size: 0.95rem; color: var(--ink-2); font-weight: 300;">Data bisnis Anda adalah amanah bagi kami. Keamanan dan transparansi adalah prioritas utama dalam setiap sistem kami.</p>
            </div>
            <div data-aos="fade-up" data-aos-delay="200" style="padding: 40px; border-radius: 24px; background: var(--paper); border: 1px solid rgba(0,0,0,0.05);">
                <div style="width: 48px; height: 48px; background: var(--accent); color: white; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 24px;">
                    <i class="fas fa-rocket"></i>
                </div>
                <h4 style="font-family: var(--serif); font-size: 1.3rem; margin-bottom: 12px;">Tumbuh Bersama</h4>
                <p style="font-size: 0.95rem; color: var(--ink-2); font-weight: 300;">Kesuksesan kami diukur dari seberapa besar bisnis Anda berkembang menggunakan layanan yang kami sediakan.</p>
            </div>
        </div>
    </div>
</section>
@endsection

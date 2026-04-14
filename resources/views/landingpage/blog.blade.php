@extends('layouts.landing')

@section('title', 'Blog — Wawasan & Berita Flow Ecosystem')

@section('content')
<section class="page-hero">
    <div class="container" data-aos="fade-up">
        <span class="eyebrow" style="display: inline-flex; align-items: center; gap: 8px; background: var(--yellow); color: var(--dark-green); padding: 6px 14px; border-radius: 100px; font-size: 0.8rem; font-weight: 500; letter-spacing: 0.04em; text-transform: uppercase; margin-bottom: 24px;">
            INSIGHTS
        </span>
        <h1>Wawasan untuk <em>Bisnis Modern</em></h1>
        <p style="color: var(--ink-2); font-size: 1.1rem; max-width: 600px; margin: 0 auto; font-weight: 300;">Temukan tips, cerita sukses, dan pembaruan terbaru seputar ekosistem Flow.</p>
    </div>
</section>

<section class="py-120">
    <div class="container">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 40px;">
            <!-- Post 1 -->
            <article data-aos="fade-up" style="group cursor: pointer;">
                <div style="border-radius: 24px; overflow: hidden; aspect-ratio: 16/10; margin-bottom: 24px; background: var(--paper-2);">
                    <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=800" alt="Blog" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s;">
                </div>
                <div style="padding: 0 8px;">
                    <span style="font-size: 0.75rem; font-weight: 600; color: var(--accent); text-transform: uppercase; letter-spacing: 0.05em;">Bisnis & UMKM</span>
                    <h3 style="font-family: var(--serif); font-size: 1.5rem; margin: 12px 0 16px; line-height: 1.3;">5 Cara Mengoptimalkan Stok untuk Waralaba Anda</h3>
                    <p style="color: var(--ink-3); font-size: 0.9rem; margin-bottom: 20px; font-weight: 300;">Bagaimana manajemen bahan baku yang efisien dapat meningkatkan margin keuntungan hingga 20%...</p>
                    <a href="#" style="font-size: 0.85rem; font-weight: 600; color: var(--ink); text-decoration: none; display: flex; align-items: center; gap: 8px;">
                        Baca Selengkapnya <i class="fas fa-arrow-right" style="font-size: 10px;"></i>
                    </a>
                </div>
            </article>

            <!-- Post 2 -->
            <article data-aos="fade-up" data-aos-delay="100" style="group cursor: pointer;">
                <div style="border-radius: 24px; overflow: hidden; aspect-ratio: 16/10; margin-bottom: 24px; background: var(--paper-2);">
                    <img src="https://images.unsplash.com/photo-1556742044-3c52d6e88c62?auto=format&fit=crop&q=80&w=800" alt="Blog" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div style="padding: 0 8px;">
                    <span style="font-size: 0.75rem; font-weight: 600; color: var(--accent); text-transform: uppercase; letter-spacing: 0.05em;">Teknologi</span>
                    <h3 style="font-family: var(--serif); font-size: 1.5rem; margin: 12px 0 16px; line-height: 1.3;">Mengenal JajanFlow: Jembatan Baru ke Konsumen</h3>
                    <p style="color: var(--ink-3); font-size: 0.9rem; margin-bottom: 20px; font-weight: 300;">Pelajari bagaimana platform konsumer kami membantu outlet Anda ditemukan oleh ribuan pelanggan baru...</p>
                    <a href="#" style="font-size: 0.85rem; font-weight: 600; color: var(--ink); text-decoration: none; display: flex; align-items: center; gap: 8px;">
                        Baca Selengkapnya <i class="fas fa-arrow-right" style="font-size: 10px;"></i>
                    </a>
                </div>
            </article>

            <!-- Post 3 -->
            <article data-aos="fade-up" data-aos-delay="200" style="group cursor: pointer;">
                <div style="border-radius: 24px; overflow: hidden; aspect-ratio: 16/10; margin-bottom: 24px; background: var(--paper-2);">
                    <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&q=80&w=800" alt="Blog" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div style="padding: 0 8px;">
                    <span style="font-size: 0.75rem; font-weight: 600; color: var(--accent); text-transform: uppercase; letter-spacing: 0.05em;">Update Produk</span>
                    <h3 style="font-family: var(--serif); font-size: 1.5rem; margin: 12px 0 16px; line-height: 1.3;">Update Fitur: Integrasi AI untuk Laporan Keuangan</h3>
                    <p style="color: var(--ink-3); font-size: 0.9rem; margin-bottom: 20px; font-weight: 300;">Analisis data penjualan Anda kini lebih mudah dengan bantuan kecerdasan buatan dari Clara AI...</p>
                    <a href="#" style="font-size: 0.85rem; font-weight: 600; color: var(--ink); text-decoration: none; display: flex; align-items: center; gap: 8px;">
                        Baca Selengkapnya <i class="fas fa-arrow-right" style="font-size: 10px;"></i>
                    </a>
                </div>
            </article>
        </div>

        <div style="margin-top: 80px; text-align: center;">
             <button style="padding: 14px 40px; border-radius: 100px; border: 1.5px solid var(--ink); background: transparent; color: var(--ink); font-weight: 500; font-size: 0.9rem; cursor: pointer;">Tampilkan Lebih Banyak</button>
        </div>
    </div>
</section>
@endsection

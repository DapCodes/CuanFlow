@extends('layouts.landing')

@section('title', 'Karir — Gabung Bersama Flow Ecosystem')

@section('content')
<section class="page-hero">
    <div class="container" data-aos="fade-up">
        <span class="eyebrow" style="display: inline-flex; align-items: center; gap: 8px; background: var(--yellow); color: var(--dark-green); padding: 6px 14px; border-radius: 100px; font-size: 0.8rem; font-weight: 500; letter-spacing: 0.04em; text-transform: uppercase; margin-bottom: 24px;">
            JOIN THE TEAM
        </span>
        <h1>Bangun Masa Depan <em>Digital Indonesia</em></h1>
        <p style="color: var(--ink-2); font-size: 1.1rem; max-width: 600px; margin: 0 auto; font-weight: 300;">Kami sedang mencari talenta-talenta luar biasa yang berani bermimpi besar dan peduli pada kemajuan ekonomi mikro.</p>
    </div>
</section>

<section class="py-120">
    <div class="container">
        <div class="section-header">
            <h2>Posisi Terbuka</h2>
            <p>Jadilah bagian dari revolusi ekosistem bisnis digital kami.</p>
        </div>
        
        <div style="display: flex; flex-direction: column; gap: 16px; max-width: 800px; margin: 0 auto;">
            <!-- Job 1 -->
            <div data-aos="fade-up" style="background: var(--white); border-radius: 20px; padding: 32px; border: 1px solid rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; transition: transform 0.3s; cursor: pointer;">
                <div>
                    <h4 style="font-family: var(--serif); font-size: 1.2rem; margin-bottom: 8px; border-radius: 24px;">Senior Backend Developer</h4>
                    <p style="font-size: 0.85rem; color: var(--ink-3);"><i class="fas fa-map-marker-alt mr-2"></i> Jakarta / Remote • Penuh Waktu</p>
                </div>
                <a href="#" style="padding: 10px 20px; background: var(--paper); border-radius: 100px; text-decoration: none; font-size: 0.85rem; font-weight: 500; color: var(--accent);">Lamar Sekarang</a>
            </div>
            
            <!-- Job 2 -->
            <div data-aos="fade-up" data-aos-delay="100" style="background: var(--white); border-radius: 20px; padding: 32px; border: 1px solid rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; transition: transform 0.3s; cursor: pointer;">
                <div>
                    <h4 style="font-family: var(--serif); font-size: 1.2rem; margin-bottom: 8px;">Product Designer (UI/UX)</h4>
                    <p style="font-size: 0.85rem; color: var(--ink-3);"><i class="fas fa-map-marker-alt mr-2"></i> Bandung / Remote • Penuh Waktu</p>
                </div>
                <a href="#" style="padding: 10px 20px; background: var(--paper); border-radius: 100px; text-decoration: none; font-size: 0.85rem; font-weight: 500; color: var(--accent);">Lamar Sekarang</a>
            </div>

            <!-- Job 3 -->
            <div data-aos="fade-up" data-aos-delay="200" style="background: var(--white); border-radius: 20px; padding: 32px; border: 1px solid rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; transition: transform 0.3s; cursor: pointer;">
                <div>
                    <h4 style="font-family: var(--serif); font-size: 1.2rem; margin-bottom: 8px;">Growth Marketing Manager</h4>
                    <p style="font-size: 0.85rem; color: var(--ink-3);"><i class="fas fa-map-marker-alt mr-2"></i> Hybrid • Penuh Waktu</p>
                </div>
                <a href="#" style="padding: 10px 20px; background: var(--paper); border-radius: 100px; text-decoration: none; font-size: 0.85rem; font-weight: 500; color: var(--accent);">Lamar Sekarang</a>
            </div>
        </div>

        <div style="text-align: center; margin-top: 60px;">
            <p style="color: var(--ink-2); font-size: 0.95rem;">Tidak menemukan posisi yang cocok? <a href="mailto:careers@flow.com" style="color: var(--accent); font-weight: 500; text-decoration: none;">Kirim CV Anda ke sini &rarr;</a></p>
        </div>
    </div>
</section>

<section class="py-120" style="background: var(--navy); color: var(--white); border-radius: 60px 60px 0 0;">
    <div class="container">
        <div style="text-align: center; max-width: 700px; margin: 0 auto;">
            <h2 style="font-family: var(--serif); font-size: 2.8rem; margin-bottom: 24px; color: var(--white);">Mengapa Bergabung dengan Flow?</h2>
            <p style="color: rgba(255,255,255,0.6); margin-bottom: 48px;">Kami menawarkan lebih dari sekadar pekerjaan. Kami menawarkan kesempatan untuk berdampak nyata.</p>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px; text-align: center;">
            <div>
                <h3 style="color: var(--yellow); font-size: 1.5rem; margin-bottom: 15px;">Work-Life Integration</h3>
                <p style="font-size: 0.9rem; color: rgba(255,255,255,0.5);">Kebebasan untuk bekerja secara remote dan fleksibel demi hasil maksimal.</p>
            </div>
            <div>
                <h3 style="color: var(--yellow); font-size: 1.5rem; margin-bottom: 15px;">Ownership Culture</h3>
                <p style="font-size: 0.9rem; color: rgba(255,255,255,0.5);">Setiap ide dihargai dan setiap anggota memiliki andil dalam arah perusahaan.</p>
            </div>
            <div>
                <h3 style="color: var(--yellow); font-size: 1.5rem; margin-bottom: 15px;">Health & Wellness</h3>
                <p style="font-size: 0.9rem; color: rgba(255,255,255,0.5);">Asuransi kesehatan lengkap dan tunjangan kebugaran bulanan.</p>
            </div>
        </div>
    </div>
</section>
@endsection

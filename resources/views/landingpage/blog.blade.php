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
            @forelse($blogs as $index => $blog)
            <!-- Post {{ $index + 1 }} -->
            <article data-aos="fade-up" data-aos-delay="{{ $index * 100 }}" style="group cursor: pointer;">
                <div style="border-radius: 24px; overflow: hidden; aspect-ratio: 16/10; margin-bottom: 24px; background: var(--paper-2);">
                    <img src="{{ $blog->thumbnail_url }}" alt="{{ $blog->title }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s;">
                </div>
                <div style="padding: 0 8px;">
                    <span style="font-size: 0.75rem; font-weight: 600; color: var(--accent); text-transform: uppercase; letter-spacing: 0.05em;">{{ $blog->category ?? 'Berita' }}</span>
                    <h3 style="font-family: var(--serif); font-size: 1.5rem; margin: 12px 0 16px; line-height: 1.3;">{{ $blog->title }}</h3>
                    <p style="color: var(--ink-3); font-size: 0.9rem; margin-bottom: 20px; font-weight: 300;">{{ Str::limit(strip_tags($blog->content), 100) }}</p>
                    <a href="#" style="font-size: 0.85rem; font-weight: 600; color: var(--ink); text-decoration: none; display: flex; align-items: center; gap: 8px;">
                        Baca Selengkapnya <i class="fas fa-arrow-right" style="font-size: 10px;"></i>
                    </a>
                </div>
            </article>
            @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 0;">
                <p style="color: var(--ink-3); font-size: 1.1rem; font-weight: 300;">Belum ada artikel yang dipublikasikan saat ini. Mampir lagi nanti ya!</p>
            </div>
            @endforelse
        </div>

        <div style="margin-top: 80px; text-align: center;">
             <button style="padding: 14px 40px; border-radius: 100px; border: 1.5px solid var(--ink); background: transparent; color: var(--ink); font-weight: 500; font-size: 0.9rem; cursor: pointer;">Tampilkan Lebih Banyak</button>
        </div>
    </div>
</section>
@endsection

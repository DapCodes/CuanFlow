@extends('layouts.landing')

@section('title', 'Hubungi Kami — Flow Ecosystem')

@section('content')
<section class="page-hero">
    <div class="container" data-aos="fade-up">
        <span class="eyebrow" style="display: inline-flex; align-items: center; gap: 8px; background: var(--yellow); color: var(--dark-green); padding: 6px 14px; border-radius: 100px; font-size: 0.8rem; font-weight: 500; letter-spacing: 0.04em; text-transform: uppercase; margin-bottom: 24px;">
            SUPPORT
        </span>
        <h1>Ada Pertanyaan? <em>Kami Siap Membantu</em></h1>
        <p style="color: var(--ink-2); font-size: 1.1rem; max-width: 600px; margin: 0 auto; font-weight: 300;">Hubungi tim kami untuk konsultasi bisnis atau bantuan teknis seputar layanan Flow.</p>
    </div>
</section>

<section class="py-120">
    <div class="container">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-[1fr_1.2fr] gap-10 lg:gap-20">
            <div data-aos="fade-right">
                <h2 style="font-family: var(--serif); font-size: 2.5rem; margin-bottom: 32px;">Kontak Kami</h2>
                
                <div style="display: flex; flex-direction: column; gap: 40px;">
                    <div style="display: flex; gap: 20px;">
                        <div style="width: 52px; height: 52px; border-radius: 14px; background: var(--accent-light); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <div>
                            <h5 style="font-family: var(--serif); font-size: 1.15rem; margin-bottom: 5px;">Email Support</h5>
                            <p style="color: var(--ink-3); font-size: 0.95rem;">hello@flow.com</p>
                            <p style="color: var(--ink-3); font-size: 0.95rem;">support@flow.com</p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 20px;">
                        <div style="width: 52px; height: 52px; border-radius: 14px; background: var(--accent-light); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div>
                            <h5 style="font-family: var(--serif); font-size: 1.15rem; margin-bottom: 5px;">WhatsApp Business</h5>
                            <p style="color: var(--ink-3); font-size: 0.95rem;">+62 812 2104 9828</p>
                            <p style="color: var(--ink-3); font-size: 0.95rem;">Sangat responsif (09:00 - 21:00 WIB)</p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 20px;">
                        <div style="width: 52px; height: 52px; border-radius: 14px; background: var(--accent-light); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h5 style="font-family: var(--serif); font-size: 1.15rem; margin-bottom: 5px;">Kantor Pusat</h5>
                            <p style="color: var(--ink-3); font-size: 0.95rem;">Jl. Digital Tech Valley No. 12<br>Bandung, Jawa Barat, Indonesia</p>
                        </div>
                    </div>
                </div>
            </div>

            <div data-aos="fade-left" style="background: var(--white); border-radius: 32px; padding: 48px; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 40px 100px rgba(0,0,0,0.03);">
                <form action="#" method="POST" style="display: flex; flex-direction: column; gap: 24px;">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="font-weight: 500; font-size: 0.85rem; color: var(--ink-2);">Nama Lengkap</label>
                            <input type="text" placeholder="John Doe" style="padding: 14px 18px; border-radius: 12px; border: 1.5px solid var(--paper-2); background: var(--paper); outline: none; transition: border-color 0.3s;">
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="font-weight: 500; font-size: 0.85rem; color: var(--ink-2);">Email</label>
                            <input type="email" placeholder="john@example.com" style="padding: 14px 18px; border-radius: 12px; border: 1.5px solid var(--paper-2); background: var(--paper); outline: none;">
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <label style="font-weight: 500; font-size: 0.85rem; color: var(--ink-2);">Subjek</label>
                        <select style="padding: 14px 18px; border-radius: 12px; border: 1.5px solid var(--paper-2); background: var(--paper); outline: none;">
                            <option>Informasi Produk CuanFlow</option>
                            <option>Kerja Sama Bisnis</option>
                            <option>Bantuan Teknis</option>
                            <option>Lainnya</option>
                        </select>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <label style="font-weight: 500; font-size: 0.85rem; color: var(--ink-2);">Pesan Anda</label>
                        <textarea rows="5" placeholder="Tuliskan pesan Anda di sini..." style="padding: 14px 18px; border-radius: 12px; border: 1.5px solid var(--paper-2); background: var(--paper); outline: none; resize: none;"></textarea>
                    </div>
                    <button type="submit" style="margin-top: 10px; padding: 16px; border-radius: 100px; border: none; background: var(--ink); color: white; font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: background 0.3s;">Kirim Pesan Sekarang</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

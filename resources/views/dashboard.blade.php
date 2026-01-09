@extends('layouts.app')

@section('title', 'Menu - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@push('styles')
<style>
    /* =========================
    CUANFLOW SPOTLIGHT TOUR (no dependency)
    ========================= */
    .tour-overlay {
        position: fixed;
        inset: 0;
        z-index: 99999;
        display: none;                 /* ditampilkan via JS */
        pointer-events: auto;
        background: rgba(15,23,42,0.82);  /* slate-900/80 */
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);

        /* spotlight hole via CSS mask */
        --spot-x: 50vw;
        --spot-y: 50vh;
        --spot-r: 120px;
        -webkit-mask: radial-gradient(
            circle at var(--spot-x) var(--spot-y),
            transparent var(--spot-r),
            black calc(var(--spot-r) + 1px)
        );
                mask: radial-gradient(
            circle at var(--spot-x) var(--spot-y),
            transparent var(--spot-r),
            black calc(var(--spot-r) + 1px)
        );
    }

    /* Emerald ring around spotlight (pseudo ring) */
    .tour-overlay::after {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background:
        radial-gradient(circle at var(--spot-x) var(--spot-y),
            rgba(16,185,129,0) calc(var(--spot-r) - 3px),
            rgba(16,185,129,0.75) var(--spot-r),
            rgba(16,185,129,0) calc(var(--spot-r) + 3px)
        );
        animation: tourPulse 2s ease-in-out infinite;
    }
    @keyframes tourPulse {
    0%,100% { opacity: .9; }
    50%     { opacity: 1;   }
    }

    /* Popover bubble */
    .tour-pop {
        position: fixed;
        z-index: 100000;
        max-width: min(360px, 92vw);
        min-width: min(260px, 90vw);
        background: #fff;
        border-radius: 1rem;
        border: 1px solid rgba(0,0,0,0.06);
        box-shadow:
        0 20px 25px -5px rgba(0,0,0,.1),
        0 8px 10px -6px rgba(0,0,0,.08);
        padding: .9rem .95rem .8rem;
        color: #0f172a;
        transform-origin: top left;
        transition: transform .15s ease, opacity .15s ease;
    }
    .tour-pop[data-enter="1"] { transform: scale(1); opacity: 1; }
    .tour-pop[data-enter="0"] { transform: scale(.98); opacity: 0; }

    .tour-title {
        font-weight: 800;
        font-size: 1rem;
        margin: 0 0 .35rem 0;
    }
    .tour-desc {
        font-size: .9375rem;
        color: #475569;
        line-height: 1.6;
        max-height: 40vh;
        overflow-y: auto;
        word-break: break-word;
        overflow-wrap: anywhere;
        margin-bottom: .6rem;
    }
    .tour-footer {
        display: flex;
        align-items: center;
        gap: .5rem;
        padding-top: .5rem;
        border-top: 1px solid #f1f5f9;
    }
    .tour-progress {
        font-size: .8125rem;
        color: #64748b;
        margin-right: auto;
        white-space: nowrap;
    }
    .tour-btn {
        border: none;
        border-radius: .5rem;
        font-weight: 700;
        font-size: .875rem;
        padding: .6rem .9rem;
        cursor: pointer;
        transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
    }
    .tour-btn:active { transform: translateY(1px); }

    .tour-prev { background: #f1f5f9; color: #334155; }
    .tour-prev:hover { background: #e2e8f0; }

    .tour-next { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color:#fff; }
    .tour-next:hover { box-shadow: 0 6px 10px -2px rgba(16,185,129,.35); }

    .tour-close { background: #f8fafc; color:#64748b; }
    .tour-close:hover { background: #eef2f7; }

    /* helper notice */
    .tour-note {
        position: fixed;
        right: 1rem; top: 1rem;
        background: linear-gradient(90deg, #10b981, #059669);
        color:#fff; padding:.6rem .9rem;
        border-radius: .75rem;
        box-shadow: 0 10px 20px rgba(0,0,0,.12);
        z-index: 100001;
        display:none;
    }

    /* Mobile tweaks */
    @media (max-width: 640px) {
    .tour-pop { max-width: 94vw; min-width: 88vw; }
    .tour-desc { max-height: 48vh; font-size: .9rem; }
    .tour-footer { flex-direction: column; align-items: stretch; }
    .tour-btn { width: 100%; }
    .tour-progress { align-self: flex-end; }
    }
    /* =========================
       MODERN "SPOTLIGHT" TOUR
       using Driver.js
       ========================= */

    /* Dimmed backdrop + subtle blur for true spotlight feel */
    .driver-overlay {
        background: rgba(15, 23, 42, 0.85) !important; /* slate-900 */
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        transition: background 200ms ease;
    }

    /* Make the highlighted element pop with a soft emerald ring */
    .driver-active-element {
        border-radius: 14px !important;
        box-shadow:
            0 0 0 2px rgba(16, 185, 129, 0.65),
            0 0 0 10px rgba(16, 185, 129, 0.12) !important;
        transition: box-shadow .25s ease, transform .25s ease;
    }

    /* Custom popover theme: compact, responsive, non-overflowing */
    .driver-popover.cuanflow-popover {
        max-width: min(360px, 92vw);
        min-width: min(260px, 90vw);
        border-radius: 1rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        box-shadow:
            0 20px 25px -5px rgba(0,0,0,0.1),
            0 8px 10px -6px rgba(0,0,0,0.08);
        padding: 0.75rem 0.875rem 0.75rem;
        background: #ffffff;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    .driver-popover.cuanflow-popover .driver-popover-title {
        font-size: 1rem;
        font-weight: 800;
        color: #0f172a; /* slate-900 */
        margin-bottom: .375rem;
        line-height: 1.4;
    }

    .driver-popover.cuanflow-popover .driver-popover-description {
        font-size: 0.9375rem;
        color: #475569; /* slate-600 */
        line-height: 1.6;
        max-height: 38vh;          /* prevent overflow on small screens */
        overflow-y: auto;
        scrollbar-width: thin;
        -ms-overflow-style: -ms-autohiding-scrollbar;
        word-break: break-word;
        overflow-wrap: anywhere;
        margin-bottom: .5rem;
    }

    .driver-popover.cuanflow-popover .driver-popover-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        padding-top: .5rem;
        border-top: 1px solid #f1f5f9;
    }

    .driver-popover.cuanflow-popover .driver-popover-progress-text {
        font-size: .8125rem;
        color: #64748b; /* slate-500 */
        white-space: nowrap;
    }

    .driver-popover.cuanflow-popover .driver-popover-prev-btn,
    .driver-popover.cuanflow-popover .driver-popover-next-btn,
    .driver-popover.cuanflow-popover .driver-popover-close-btn {
        border-radius: .5rem;
        font-weight: 700;
        font-size: .875rem;
        text-shadow: none;
        box-shadow: none;
        padding: .6rem .9rem;
        border: none;
        cursor: pointer;
        transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
    }

    .driver-popover.cuanflow-popover .driver-popover-prev-btn {
        background: #f1f5f9;
        color: #334155;
    }
    .driver-popover.cuanflow-popover .driver-popover-prev-btn:hover {
        background: #e2e8f0;
        transform: translateY(-1px);
    }

    .driver-popover.cuanflow-popover .driver-popover-next-btn {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
    }
    .driver-popover.cuanflow-popover .driver-popover-next-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 10px -2px rgba(16,185,129,.35);
    }

    .driver-popover.cuanflow-popover .driver-popover-close-btn {
        background: #f8fafc;
        color: #64748b;
    }
    .driver-popover.cuanflow-popover .driver-popover-close-btn:hover {
        background: #eef2f7;
        transform: translateY(-1px);
    }

    /* Optional: popover arrow tinted to white */
    .driver-popover.cuanflow-popover .driver-popover-arrow {
        border-color: #ffffff;
    }

    /* "Lewati" (Skip) pill button injected via onPopoverRender */
    .cuanflow-skip {
        background: transparent;
        color: #0ea5e9; /* sky-500 */
        border: none;
        padding: .25rem .5rem;
        border-radius: .375rem;
        font-weight: 700;
        font-size: .875rem;
        cursor: pointer;
        transition: color .15s ease, background .15s ease;
        margin-right: auto; /* push progress + nav to the right */
    }
    .cuanflow-skip:hover { background: #f0f9ff; color: #0284c7; }

    /* ========== EXISTING STYLES (kept) ========== */

    /* Modal Animation */
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes modalSlideUp {
        from { opacity: 0; transform: translateY(20px) scale(0.95); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    @keyframes modalExit {
        from { opacity: 1; transform: scale(1); }
        to   { opacity: 0; transform: scale(0.95); }
    }
    .modal-backdrop { animation: fadeIn 0.3s ease-out; }
    .modal-content { animation: modalSlideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
    .modal-exit .modal-content { animation: modalExit 0.2s ease-in forwards; }

    /* Menu Card Animations */
    @keyframes menuCardEntry { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .menu-card { opacity: 0; animation: menuCardEntry 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border: 1px solid transparent; }
    .menu-card:hover { transform: none; background-color: #ffffff; box-shadow: none; border-color: transparent; }
    .menu-card .menu-icon { transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); }
    .menu-card:hover .menu-icon { transform: scale(1.05); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
    .menu-card:nth-child(1) { animation-delay: 0.05s; }
    .menu-card:nth-child(2) { animation-delay: 0.1s; }
    .menu-card:nth-child(3) { animation-delay: 0.15s; }
    .menu-card:nth-child(4) { animation-delay: 0.2s; }
    .menu-card:nth-child(5) { animation-delay: 0.25s; }
    .menu-card:nth-child(6) { animation-delay: 0.3s; }
    .menu-card:nth-child(7) { animation-delay: 0.35s; }
    .menu-card:nth-child(8) { animation-delay: 0.4s; }
    .menu-card:nth-child(9) { animation-delay: 0.45s; }
    .menu-card:nth-child(10) { animation-delay: 0.5s; }
    .menu-card:nth-child(11) { animation-delay: 0.55s; }
    .menu-card:nth-child(12) { animation-delay: 0.6s; }
    .menu-card:nth-child(13) { animation-delay: 0.65s; }
    .menu-card:nth-child(14) { animation-delay: 0.7s; }
    .menu-card:nth-child(15) { animation-delay: 0.75s; }
    .menu-card:nth-child(16) { animation-delay: 0.8s; }
    .menu-card:nth-child(17) { animation-delay: 0.85s; }
    .menu-card:nth-child(18) { animation-delay: 0.9s; }
    .menu-card:nth-child(19) { animation-delay: 0.95s; }
    .menu-card:nth-child(20) { animation-delay: 1s; }
    .menu-card:nth-child(21) { animation-delay: 1.05s; }
    .menu-card:nth-child(22) { animation-delay: 1.1s; }
    .menu-card:nth-child(23) { animation-delay: 1.15s; }


    .backdrop-blur-effect { backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); }

    /* Mobile tweaks for the popover */
    @media (max-width: 640px) {
        .driver-popover.cuanflow-popover {
            max-width: 94vw;
            min-width: 88vw;
            padding: 0.75rem;
        }
        .driver-popover.cuanflow-popover .driver-popover-title { font-size: .975rem; }
        .driver-popover.cuanflow-popover .driver-popover-description { font-size: .9rem; max-height: 45vh; }
        .driver-popover.cuanflow-popover .driver-popover-prev-btn,
        .driver-popover.cuanflow-popover .driver-popover-next-btn,
        .driver-popover.cuanflow-popover .driver-popover-close-btn { width: 100%; }
        .driver-popover.cuanflow-popover .driver-popover-footer { flex-direction: column; align-items: stretch; }
        .driver-popover.cuanflow-popover .driver-popover-progress-text { align-self: flex-end; }
    }

    .insights-modal-backdrop {
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        animation: fadeIn 0.4s ease-out;
    }

    .insights-modal-content {
        animation: modalSlideUp 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        max-height: 90vh;
        overflow-y: auto;
    }

    .insights-modal-exit .insights-modal-content {
        animation: modalExit 0.3s ease-in forwards;
    }

    .severity-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .severity-info {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1e40af;
    }

    .severity-warning {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
    }

    .severity-critical {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: .8; }
    }

    .insight-type-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .type-sales_trend { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .type-stock_prediction { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .type-product_recommendation { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
    .type-anomaly { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
    .type-general { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }

    .insight-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        min-height: 280px;
        display: flex;
        flex-direction: column;
    }

    .insight-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .carousel-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 50%;
        width: 3rem;
        height: 3rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        z-index: 10;
    }

    .carousel-btn:hover {
        background: #f9fafb;
        border-color: #10b981;
        transform: translateY(-50%) scale(1.1);
    }

    .carousel-btn.prev { left: -1rem; }
    .carousel-btn.next { right: -1rem; }

    .carousel-dots {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
        margin-top: 1rem;
    }

    .carousel-dot {
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 50%;
        background: #d1d5db;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .carousel-dot.active {
        background: #10b981;
        width: 1.5rem;
        border-radius: 0.25rem;
    }

    @media (max-width: 640px) {
        .insights-modal-content {
            max-width: 95vw;
            padding: 1rem;
        }
        .carousel-btn.prev { left: 0.5rem; }
        .carousel-btn.next { right: 0.5rem; }
    }
</style>

<style>
    /* Smooth Blur Animation untuk Welcome Modal */
    #welcomeTourModal {
        transition: opacity 0.4s ease-out, backdrop-filter 0.4s ease-out;
    }

    #welcomeTourModal.hidden {
        opacity: 0;
        pointer-events: none;
    }

    #welcomeTourModal:not(.hidden) {
        opacity: 1;
        pointer-events: auto;
    }

    /* Backdrop dengan smooth blur */
    #welcomeTourModal .backdrop-blur-effect {
        backdrop-filter: blur(0px);
        -webkit-backdrop-filter: blur(0px);
        transition: backdrop-filter 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    #welcomeTourModal:not(.hidden) .backdrop-blur-effect {
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }

    /* Modal content smooth entrance */
    #welcomeTourModal .modal-content {
        transform: scale(0.95) translateY(20px);
        opacity: 0;
        transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    #welcomeTourModal:not(.hidden) .modal-content {
        transform: scale(1) translateY(0);
        opacity: 1;
    }

    /* Exit animation */
    #welcomeTourModal.modal-exit .backdrop-blur-effect {
        backdrop-filter: blur(0px);
        -webkit-backdrop-filter: blur(0px);
    }

    #welcomeTourModal.modal-exit .modal-content {
        transform: scale(0.95) translateY(20px);
        opacity: 0;
    }
    
</style>
@endpush

@section('content')
<main class="flex-grow flex items-center justify-center py-8 px-4">
    <div class="w-full max-w-6xl">
<div class="flex justify-center p-4">
<div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-5 gap-6 max-w-8xl w-full">

    <!-- OPERASIONAL UTAMA (Prioritas Tertinggi) -->
<a href="{{ route('pos.index') }}"
   class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300 relative"
   data-step="1"
   data-title="Point of Sale"
   data-intro="<strong>Catat penjualan di kasir.</strong> Gunakan menu ini untuk memasukkan setiap transaksi dengan cepat dan rapi, sehingga antrian tidak menumpuk.">

    <div class="menu-icon relative w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-orange-500 to-red-400 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
        @if(isset($isPosOpen) && $isPosOpen)
            <span
                class="absolute top-1.5 right-1.5 sm:top-2 sm:right-2
                       h-2 w-2 sm:h-2.5 sm:w-2.5 rounded-full
                       bg-emerald-400 ring-2 ring-white/80 shadow-sm
                       transition-all duration-300
                       opacity-90 scale-95
                       group-hover:opacity-100 group-hover:scale-110"
                title="POS sedang buka"
                aria-label="POS sedang buka"
            ></span>
        @endif

        <i class="fa-solid fa-cash-register text-4xl sm:text-5xl text-white"></i>
    </div>

    <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
        Point of Sale
    </span>
</a>


    <a href="{{ route('sales.index') }}"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="2"
       data-title="Penjualan"
       data-intro="<strong>Kelola semua penjualan.</strong> Lihat riwayat penjualan untuk mengetahui produk mana yang paling laku dan perkembangan usaha Anda.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-pink-400 to-red-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-cart-shopping text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Penjualan
        </span>
    </a>

    <a href="{{ route('discounts.index') }}"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="3"
       data-title="Diskon"
       data-intro="<strong>Atur promo dan diskon.</strong> Buat potongan harga dengan cara sederhana agar pelanggan tertarik dan penjualan meningkat.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-red-400 to-pink-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-tags text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Diskon
        </span>
    </a>

    <a href="{{ route('finance.index') }}"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="4"
       data-title="Keuangan"
       data-intro="<strong>Catat uang masuk dan keluar.</strong> Menu ini membantu Anda melihat kondisi keuangan usaha sehingga lebih mudah mengontrol arus kas.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-purple-400 to-pink-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-wallet text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Keuangan
        </span>
    </a>

    <a href="{{ route('withdraw.index') }}"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="4"
       data-title="Penarikan Saldo"
       data-intro="<strong>Tarik saldo keuntungan Anda.</strong> Ajukan penarikan saldo hasil penjualan Anda ke rekening bank atau QRIS.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-teal-500 to-green-600 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-hand-holding-dollar text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Penarikan Saldo
        </span>
    </a>

    <a href="{{ route('outlet-payment-links.index') }}"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="4"
       data-title="Metode Pembayaran"
       data-intro="<strong>Atur metode pembayaran.</strong> Menu ini membantu Anda mengatur metode pembayaran yang tersedia.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-pink-400 to-pink-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-qrcode text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Metode Pembayaran
        </span>
    </a>

    <!-- MONITORING & ANALISIS -->
    <a href="{{ route('statistics.index') }}"
       class="menu-card nav-link group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="5"
       data-title="Dashboard & Statistik"
       data-intro="<strong>Lihat ringkasan usaha Anda.</strong> Tampilkan grafik dan angka penting seperti penjualan dan keuntungan dalam tampilan yang mudah dipahami.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-green-400 to-blue-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-chart-line text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Dashboard & Statistik
        </span>
    </a>


    <a href="{{ route('reports.index') }}"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="6"
       data-title="Laporan Keseluruhan"
       data-intro="<strong>Kumpulan laporan usaha.</strong> Akses dan unduh laporan harian, mingguan, dan bulanan untuk dicek atau dibagikan ke partner.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-file-invoice text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Laporan Keseluruhan
        </span>
    </a>

    <!-- MANAJEMEN PRODUK & INVENTORI -->
    <a href="{{ route('products-hpp.index') }}"
       class="menu-card nav-link group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="7"
       data-title="Produk & Resep"
       data-intro="<strong>Atur daftar menu dan resep.</strong> Hitung otomatis harga pokok penjualan (HPP) sehingga Anda bisa menentukan harga jual yang pas.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-yellow-400 to-green-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-utensils text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Produk & Resep
        </span>
    </a>

    <a href="{{ route('raw-materials.index') }}"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="8"
       data-title="Bahan Baku"
       data-intro="<strong>Data stok bahan baku.</strong> Cek dan perbarui stok agar dapur tidak kehabisan bahan saat produksi.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-orange-400 to-red-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-boxes-stacked text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Bahan Baku
        </span>
    </a>

    <a href="{{ route('raw-materials.suppliers') }}"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="9"
       data-title="Supplier"
       data-intro="<strong>Daftar pemasok bahan.</strong> Simpan nama, kontak, dan catatan supplier agar mudah dihubungi saat membutuhkan barang.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-amber-400 to-orange-600 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-truck-field text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Supplier
        </span>
    </a>

    <a href="{{ route('production.index') }}"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="10"
       data-title="Produksi"
       data-intro="<strong>Catat proses produksi.</strong> Pantau pemakaian bahan sampai menjadi produk jadi, supaya produksi lebih teratur dan terkontrol.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-blue-400 to-blue-700 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-flask text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Produksi
        </span>
    </a>

    <a href="{{ route('stock-opname.index') }}"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="11"
       data-title="Stock Opname"
       data-intro="<strong>Cek ulang stok barang.</strong> Cocokkan stok di sistem dengan stok di gudang untuk mengurangi selisih dan kerugian.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-green-400 to-green-700 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-boxes-packing text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Stock Opname
        </span>
    </a>

    <!-- PENGATURAN BISNIS -->
    <a href="{{ route('outlets.index') }}"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="12"
       data-title="Informasi Outlet"
       data-intro="<strong>Data toko/outlet Anda.</strong> Simpan alamat, jam buka, dan informasi penting lain agar data usaha selalu rapi.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-store text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Informasi Outlet
        </span>
    </a>

    <a href="{{ route('landing-pages.index') }}"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="12"
       data-title="Landing Page"
       data-intro="<strong>Bangun kehadiran digital Anda.</strong> Buat halaman promosi menarik untuk outlet Anda dengan mudah dan cepat.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-lg group-hover:shadow-purple-200 transition-all duration-300">
        <i class="fa-solid fa-rocket text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Landing Page
        </span>
    </a>

    <a href="{{ route('testimonials.index') }}"
        class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
        data-step="13"
        data-title="Testimoni Pelanggan"
        data-intro="<strong>Tingkatkan kepercayaan pelanggan.</strong> Kelola ulasan positif dari pelanggan Anda untuk ditampilkan di halaman promosi.">
            <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-lg group-hover:shadow-blue-200 transition-all duration-300">
                <i class="fa-solid fa-quote-left text-4xl sm:text-5xl text-white"></i>
            </div>
            <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
                Testimoni
            </span>
    </a>

    <a href="{{ route('employees.index') }}"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="13"
       data-title="Pegawai & Hak Akses"
       data-intro="<strong>Atur data karyawan.</strong> Tambah pegawai dan tentukan menu apa saja yang boleh mereka akses di sistem.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-teal-400 to-cyan-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-users text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Pegawai & Hak Akses
        </span>
    </a>

    <a href="{{ route('customer-debts.index') }}"
    class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
    data-step="14"
    data-title="Pelanggan & Piutang"
    data-intro="<strong>Kelola hubungan pelanggan.</strong> Pantau data pelanggan setia dan kelola catatan piutang atau tagihan yang belum terbayar secara terpusat.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-teal-400 to-cyan-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-address-book text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Pelanggan & Piutang
        </span>
    </a>

    <a href="{{ route('tables.index') }}"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="15"
       data-title="Kelola Meja"
       data-intro="<strong>Atur sistem meja outlet Anda.</strong> Kelola penomoran meja untuk kafe atau restoran, pantau status terisi atau tersedia secara real-time.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-chair text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Kelola Meja
        </span>
    </a>

    <!-- AI & INSIGHT -->
    <a href="{{ route('ai-insights.index') }}"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="15"
       data-title="Insight"
       data-intro="<strong>Saran dari data usaha.</strong> Lihat ringkasan dan saran sederhana berdasarkan data penjualan untuk membantu Anda mengambil keputusan.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-violet-400 to-purple-600 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-lightbulb text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Insight
        </span>
    </a>

    <a href="{{ route('clara-ai.index') }}"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="16"
       data-title="Clara AI"
       data-intro="<strong>Asisten pintar untuk usaha Anda.</strong> Tanyakan apa saja tentang penjualan, biaya, atau laporan, dan dapatkan jawaban dalam bahasa yang mudah dipahami.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <!-- <i class="fa-solid fa-robot text-4xl sm:text-5xl text-white"></i> -->
            <img src="{{ asset('assets/image/clara-ai.png') }}" class="p-2" alt="">
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Clara AI
        </span>
    </a>

    <!-- BANTUAN & PENGATURAN -->
    <a href="{{ route('outlet-policies.index') }}"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="17"
       data-title="Kebijakan Outlet"
       data-intro="<strong>Catatan aturan usaha.</strong> Simpan SOP dan kebijakan sederhana agar semua pegawai bekerja dengan cara yang sama.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-gray-400 to-gray-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-clipboard-list text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Kebijakan Outlet
        </span>
    </a>

    <a href="{{ route('profile.edit') }}"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="18"
       data-title="Pengaturan Akun"
       data-intro="<strong>Atur profil akun Anda.</strong> Ubah nama, kata sandi, dan kontak untuk menjaga keamanan dan kerapihan data.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-gray-500 to-gray-700 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-user-gear text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Pengaturan Akun
        </span>
    </a>

    <a href="{{ route('faqs.index') }}"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="19"
       data-title="Bantuan & FAQ"
       data-intro="<strong>Panduan penggunaan aplikasi.</strong> Baca langkah-langkah sederhana dan jawaban pertanyaan umum saat Anda membutuhkan bantuan.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-green-500 to-teal-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-circle-question text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Bantuan & FAQ
        </span>
    </a>

</div>

</div>
<!-- Modal Sapaan Selamat Datang (tambahkan sebelum modal noOutlet) -->
<div id="welcomeTourModal" class="hidden modal-backdrop fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-effect"></div>
    
    <div class="modal-content relative bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 p-8 text-center transform">
        <div class="mb-6">
            <!-- <div class="flex items-center justify-center mb-4 "> -->
                <!-- <img src="{{ asset('assets/image/full-logo.svg') }}" alt="Logo CuanFlow" style="width: 150px; height: 150px;">
            </div> -->
            <h2 class="text-2xl font-bold text-gray-900 mb-3">Selamat Datang di CuanFlow!</h2>
            <p class="text-gray-600 leading-relaxed mb-4">
                Outlet Anda sudah berhasil terdaftar!
            </p>
            <p class="text-sm text-gray-500">
                Ingin kami tunjukkan cara menggunakan menu-menu di CuanFlow?
            </p>
        </div>
        
        <div class="flex gap-3">
            <button id="skipTourBtn" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-xl transition-all duration-200">
                <i class="fa-solid fa-times mr-2"></i>
                Nanti Saja
            </button>
            <button id="startWelcomeTourBtn" class="flex-1 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                <i class="fa-solid fa-play mr-2"></i>
                Ya, Mulai!
            </button>
        </div>
    </div>
</div>
    </div>
</main>

<!-- Modal untuk user yang belum memiliki outlet -->
@if(auth()->check() && is_null(auth()->user()->outlet_id))
<div id="noOutletModal" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-effect"></div>
    
    <div class="modal-content relative bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 p-8 text-center transform">
        <div class="mb-6">
            <div class="mx-auto w-20 h-20 bg-gradient-to-br from-cuan-green to-cuan-olive rounded-full flex items-center justify-center mb-4">
                <i class="fa-solid fa-store text-4xl text-white"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-3">Outlet Belum Terdaftar</h2>
            <p class="text-gray-600 leading-relaxed">
                Anda belum mendaftarkan outlet. Daftarkan outlet Anda sekarang untuk menggunakan semua fitur yang tersedia di CuanFlow.
            </p>
        </div>
        
        <a href="{{ route('outlets.register.index') }}" id="registerOutletBtn" class="inline-block w-full bg-cuan-green hover:bg-cuan-dark text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
            <i class="fa-solid fa-plus-circle mr-2"></i>
            Daftarkan Outlet Sekarang
        </a>
    </div>
</div>
@endif
@endsection

@if(isset($unreadInsights) && $unreadInsights->isNotEmpty())
<div id="insightsModal" class="hidden insights-modal-backdrop fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-gray-900 bg-opacity-70"></div>
    
    <div class="insights-modal-content relative bg-white rounded-2xl shadow-2xl max-w-3xl w-full mx-4 p-6">
        <!-- Header -->
        <div class="flex items-start justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <img src="{{ asset('assets/image/clara-ai.png') }}" class="p-2" alt="">
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Insight Baru dari Clara AI</h2>
                    <p class="text-sm text-gray-500">{{ $unreadInsights->count() }} insight untuk Anda</p>
                </div>
            </div>
            <button onclick="closeInsightsModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fa-solid fa-times text-2xl"></i>
            </button>
        </div>

        <!-- Carousel Container -->
        <div class="relative mb-6">
            <div id="insightsCarousel" class="overflow-hidden">
                <div id="carouselTrack" class="flex transition-transform duration-300 ease-in-out">
                    @foreach($unreadInsights as $index => $insight)
                    <div class="carousel-slide w-full flex-shrink-0 px-2">
                        <div class="insight-card">
                            <!-- Header Card -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="insight-type-icon type-{{ $insight->type }}">
                                        <i class="fa-solid fa-{{ $insight->type === 'sales_trend' ? 'chart-line' : 
                                            ($insight->type === 'stock_prediction' ? 'boxes-stacked' : 
                                            ($insight->type === 'product_recommendation' ? 'star' : 
                                            ($insight->type === 'anomaly' ? 'exclamation-triangle' : 'lightbulb'))) }} text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-lg text-gray-900">{{ $insight->title }}</h3>
                                        <p class="text-xs text-gray-500">{{ $insight->insight_date->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <span class="severity-badge severity-{{ $insight->severity }}">
                                    @if($insight->severity === 'critical')
                                        <i class="fa-solid fa-circle-exclamation"></i>
                                    @elseif($insight->severity === 'warning')
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                    @else
                                        <i class="fa-solid fa-circle-info"></i>
                                    @endif
                                    {{ ucfirst($insight->severity) }}
                                </span>
                            </div>

                            <!-- Content -->
                            <div class="flex-grow mb-4">
                                <div class="text-gray-700 leading-relaxed prose prose-sm max-w-none" style="max-height: 200px; overflow-y: auto;">
                                    {!! nl2br(e($insight->content)) !!}
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2 pt-4 border-t border-gray-100">
                                <button onclick="markAsRead({{ $insight->id }})" 
                                        class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200">
                                    <i class="fa-solid fa-check mr-2"></i>
                                    Tandai Sudah Baca
                                </button>
                                <button onclick="dismissInsight({{ $insight->id }})" 
                                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg transition-all duration-200">
                                    <i class="fa-solid fa-eye-slash mr-2"></i>
                                    Dismiss
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Navigation Buttons -->
            @if($unreadInsights->count() > 1)
            <button onclick="prevSlide()" class="carousel-btn prev">
                <i class="fa-solid fa-chevron-left text-gray-600"></i>
            </button>
            <button onclick="nextSlide()" class="carousel-btn next">
                <i class="fa-solid fa-chevron-right text-gray-600"></i>
            </button>
            @endif
        </div>

        <!-- Dots Navigation -->
        @if($unreadInsights->count() > 1)
        <div class="carousel-dots">
            @foreach($unreadInsights as $index => $insight)
            <div class="carousel-dot {{ $index === 0 ? 'active' : '' }}" onclick="goToSlide({{ $index }})"></div>
            @endforeach
        </div>
        @endif

        <!-- Footer -->
        <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-100">
            <a href="{{ route('ai-insights.index') }}" class="text-emerald-600 hover:text-emerald-700 font-semibold text-sm">
                <i class="fa-solid fa-list mr-1"></i>
                Lihat Semua Insight
            </a>
            <button onclick="markAllAsRead()" class="text-gray-600 hover:text-gray-800 font-semibold text-sm">
                <i class="fa-solid fa-check-double mr-1"></i>
                Tandai Semua Sudah Baca
            </button>
        </div>
    </div>
</div>
@endif

{{-- GANTI SELURUH SCRIPT DI BAGIAN BAWAH DENGAN INI --}}

<script>
document.addEventListener('DOMContentLoaded', function() {
  /* ====== MODAL NO OUTLET LOGIC ====== */
  const modal = document.getElementById('noOutletModal');
  const registerBtn = document.getElementById('registerOutletBtn');
  if (modal && registerBtn) {
    modal.addEventListener('click', e => {
      if (e.target === modal) { e.preventDefault(); e.stopPropagation(); }
    });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') e.preventDefault(); });
    registerBtn.addEventListener('click', function(e){
      e.preventDefault();
      const url = this.getAttribute('href');
      modal.classList.add('modal-exit');
      setTimeout(() => {
        const globalLoader = document.getElementById('global-page-loader');
        if (globalLoader) globalLoader.classList.add('active');
        setTimeout(() => { window.location.href = url; }, 500);
      }, 300);
    });
  }

  /* ====== WELCOME TOUR MODAL LOGIC ====== */
  const welcomeModal = document.getElementById('welcomeTourModal');
  const startWelcomeTourBtn = document.getElementById('startWelcomeTourBtn');
  const skipTourBtn = document.getElementById('skipTourBtn');
  const WELCOME_KEY = 'cuanflow_show_welcome';

  const shouldShowWelcome = @json(session('show_welcome_tour', false)) || 
                           localStorage.getItem(WELCOME_KEY) === '1';

  if (shouldShowWelcome && welcomeModal && !modal) {
    if (@json(session('show_welcome_tour', false))) {
      localStorage.setItem(WELCOME_KEY, '1');
    }
    
    setTimeout(() => {
      welcomeModal.classList.remove('hidden');
      welcomeModal.offsetHeight;
    }, 600);
  }

  if (startWelcomeTourBtn) {
    startWelcomeTourBtn.addEventListener('click', function() {
      welcomeModal.classList.add('modal-exit');
      setTimeout(() => {
        welcomeModal.classList.add('hidden');
        welcomeModal.classList.remove('modal-exit');
        startTour({auto: true, fromWelcome: true});
      }, 500);
    });
  }

  if (skipTourBtn) {
    skipTourBtn.addEventListener('click', function() {
      localStorage.removeItem(WELCOME_KEY);
      welcomeModal.classList.add('modal-exit');
      setTimeout(() => {
        welcomeModal.classList.add('hidden');
        welcomeModal.classList.remove('modal-exit');
      }, 500);
    });
  }

  /* ====== TOUR CORE ====== */
  const overlay = document.createElement('div');
  overlay.className = 'tour-overlay';
  overlay.setAttribute('aria-hidden', 'true');

  const pop = document.createElement('div');
  pop.className = 'tour-pop';
  pop.setAttribute('role', 'dialog');
  pop.setAttribute('aria-live', 'polite');
  pop.dataset.enter = '0';

  const titleEl = document.createElement('div');
  titleEl.className = 'tour-title';

  const descEl = document.createElement('div');
  descEl.className = 'tour-desc';

  const footer = document.createElement('div');
  footer.className = 'tour-footer';

  const progressEl = document.createElement('div');
  progressEl.className = 'tour-progress';

  const prevBtn = document.createElement('button');
  prevBtn.className = 'tour-btn tour-prev';
  prevBtn.type = 'button';
  prevBtn.textContent = '← Kembali';

  const nextBtn = document.createElement('button');
  nextBtn.className = 'tour-btn tour-next';
  nextBtn.type = 'button';
  nextBtn.textContent = 'Lanjut →';

  const closeBtn = document.createElement('button');
  closeBtn.className = 'tour-btn tour-close';
  closeBtn.type = 'button';
  closeBtn.textContent = 'Tutup';

  footer.append(progressEl, prevBtn, nextBtn, closeBtn);
  pop.append(titleEl, descEl, footer);
  document.body.append(overlay, pop);

  const note = document.createElement('div');
  note.className = 'tour-note';
  note.innerHTML = '<strong>Tour Selesai!</strong> Anda siap menggunakan CuanFlow.';
  document.body.appendChild(note);

  let steps = [];
  let idx = 0;
  let autoMode = false;

  function collectSteps() {
    const items = Array.from(document.querySelectorAll('.menu-card'))
      .filter(el => el.hasAttribute('data-step'))
      .sort((a,b) => Number(a.dataset.step) - Number(b.dataset.step));

    steps = items.map((el,i) => ({
      el,
      title: el.dataset.title || ('Langkah ' + (i+1)),
      desc:  el.dataset.intro || '',
      side:  'bottom'
    }));
  }

  function clamp(n,min,max){ return Math.max(min, Math.min(max,n)); }

  function updateLayout() {
    const s = steps[idx];
    if (!s) return;

    s.el.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' });

    requestAnimationFrame(() => {
      const r = s.el.getBoundingClientRect();
      const cx = r.left + r.width/2;
      const cy = r.top  + r.height/2;
      const rad = Math.ceil(Math.hypot(r.width/2, r.height/2) + 18);

      overlay.style.setProperty('--spot-x', `${cx}px`);
      overlay.style.setProperty('--spot-y', `${cy}px`);
      overlay.style.setProperty('--spot-r', `${rad}px`);

      pop.style.visibility = 'hidden';
      pop.dataset.enter = '0';
      titleEl.textContent = s.title;
      descEl.innerHTML  = s.desc;
      progressEl.textContent = `${idx+1} dari ${steps.length}`;

      pop.style.left = '0px';
      pop.style.top  = '0px';
      pop.style.display = 'block';
      const pw = pop.offsetWidth;
      const ph = pop.offsetHeight;

      const gap = 14;
      let left = cx - pw/2;
      let top  = r.bottom + gap;

      if (top + ph > window.innerHeight - 8) {
        top = r.top - ph - gap;
      }
      if (top < 8) {
        top = clamp(cy - ph/2, 8, window.innerHeight - ph - 8);
        left = r.right + gap;
        if (left + pw > window.innerWidth - 8) {
          left = r.left - pw - gap;
        }
      }

      left = clamp(left, 8, window.innerWidth - pw - 8);
      top  = clamp(top,  8, window.innerHeight - ph - 8);

      pop.style.left = `${left}px`;
      pop.style.top  = `${top}px`;
      pop.style.visibility = 'visible';
      requestAnimationFrame(() => { pop.dataset.enter = '1'; });
    });
  }

  function showStep(i) {
    idx = i;
    if (idx < 0) idx = 0;
    if (idx > steps.length - 1) { endTour(); return; }

    overlay.style.display = 'block';
    pop.style.display = 'block';
    overlay.setAttribute('aria-hidden', 'false');

    prevBtn.disabled = (idx === 0);
    nextBtn.textContent = (idx === steps.length - 1) ? 'Selesai ✓' : 'Lanjut →';

    updateLayout();
  }

  function next() { showStep(idx + 1); }
  function prev() { showStep(idx - 1); }

  function endTour() {
    overlay.style.display = 'none';
    pop.style.display = 'none';
    overlay.setAttribute('aria-hidden', 'true');

    localStorage.removeItem(WELCOME_KEY);

    if (autoMode) {
      note.style.display = 'block';
      setTimeout(() => { note.style.display = 'none'; }, 3500);
    }
    autoMode = false;
    window.removeEventListener('resize', updateLayout);
    window.removeEventListener('scroll', updateLayout, true);
  }

  function startTour({auto=false, force=false, fromWelcome=false} = {}) {
    if (!force && !fromWelcome && document.getElementById('noOutletModal')) return;

    collectSteps();
    if (!steps.length) return;

    autoMode = !!auto;

    window.addEventListener('resize', updateLayout);
    window.addEventListener('scroll', updateLayout, true);

    overlay.onclick = next;
    prevBtn.onclick = prev;
    nextBtn.onclick = next;
    closeBtn.onclick = endTour;

    document.addEventListener('keydown', onKey, { passive: false });

    function onKey(e){
      if (overlay.style.display !== 'block') { document.removeEventListener('keydown', onKey); return; }
      if (e.key === 'Escape') { e.preventDefault(); endTour(); }
      else if (e.key === 'ArrowRight' || e.key === 'Enter') { e.preventDefault(); next(); }
      else if (e.key === 'ArrowLeft') { e.preventDefault(); prev(); }
    }

    showStep(0);
  }

  const startTourBtn = document.getElementById('startTourBtn');
  if (startTourBtn) startTourBtn.addEventListener('click', () => startTour());

  document.addEventListener('keydown', function(e) {
    const isCtrlOrMeta = e.ctrlKey || e.metaKey;
    if (isCtrlOrMeta && e.key.toLowerCase() === 'h') {
      e.preventDefault();
      localStorage.setItem(WELCOME_KEY, '1');
      if (welcomeModal) {
        welcomeModal.classList.remove('hidden');
      }
    }
  });
});
</script>

<script>
let currentSlide = 0;
let totalSlides = {{ isset($unreadInsights) ? $unreadInsights->count() : 0 }};

// Ambil CSRF langsung dari Blade
const CSRF_TOKEN = '{{ csrf_token() }}';

// Helper notification function
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-emerald-500' : 'bg-red-500'
    } text-white font-semibold transform transition-all duration-300`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(400px)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function updateCarousel() {
    const track = document.getElementById('carouselTrack');
    const dots  = document.querySelectorAll('.carousel-dot');
    
    if (track) {
        track.style.transform = `translateX(-${currentSlide * 100}%)`;
    }
    
    dots.forEach((dot, index) => {
        dot.classList.toggle('active', index === currentSlide);
    });
}

window.nextSlide = function() {
    if (currentSlide < totalSlides - 1) {
        currentSlide++;
        updateCarousel();
    }
};

window.prevSlide = function() {
    if (currentSlide > 0) {
        currentSlide--;
        updateCarousel();
    }
};

window.goToSlide = function(index) {
    currentSlide = index;
    updateCarousel();
};

window.closeInsightsModal = function() {
    const insightsModal = document.getElementById('insightsModal');
    if (insightsModal) {
        insightsModal.classList.add('insights-modal-exit');
        setTimeout(() => {
            insightsModal.classList.add('hidden');
            insightsModal.classList.remove('insights-modal-exit');
        }, 300);
    }
};

// MARK AS READ
window.markAsRead = function(insightId) {
    // URL pake named route (lebih aman kalau suatu saat prefix/prefix group berubah)
    const url = `{{ route('ai-insights.mark-read', ':id') }}`.replace(':id', insightId);

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: JSON.stringify({})
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            throw new Error(data.message || `HTTP ${response.status}`);
        }
        if (data.success) {
            const slides = document.querySelectorAll('.carousel-slide');
            const dots   = document.querySelectorAll('.carousel-dot');

            if (slides[currentSlide]) {
                slides[currentSlide].remove();
                if (dots[currentSlide]) dots[currentSlide].remove();
            }

            const remainingSlides = document.querySelectorAll('.carousel-slide');
            totalSlides = remainingSlides.length;

            if (totalSlides === 0) {
                closeInsightsModal();
                showNotification('Semua insight sudah dibaca!', 'success');
            } else {
                if (currentSlide >= totalSlides) {
                    currentSlide = totalSlides - 1;
                }
                updateCarousel();
            }
        } else {
            showNotification(data.message || 'Gagal menandai insight', 'error');
        }
    })
    .catch(error => {
        console.error('markAsRead error:', error);
        showNotification(error.message || 'Gagal menandai insight', 'error');
    });
};

// DISMISS
window.dismissInsight = function(insightId) {
    if (!confirm('Yakin ingin dismiss insight ini?')) return;

    const url = `{{ route('ai-insights.dismiss', ':id') }}`.replace(':id', insightId);

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: JSON.stringify({})
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            throw new Error(data.message || `HTTP ${response.status}`);
        }
        if (data.success) {
            // Setelah dismiss di server, treat sama seperti read di UI
            window.markAsRead(insightId);
        } else {
            showNotification(data.message || 'Gagal dismiss insight', 'error');
        }
    })
    .catch(error => {
        console.error('dismissInsight error:', error);
        showNotification(error.message || 'Gagal dismiss insight', 'error');
    });
};

// MARK ALL AS READ
window.markAllAsRead = function() {
    const url = `{{ route('ai-insights.mark-all-read') }}`;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: JSON.stringify({})
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            throw new Error(data.message || `HTTP ${response.status}`);
        }
        if (data.success) {
            closeInsightsModal();
            showNotification('Semua insight ditandai sudah baca!', 'success');
        } else {
            showNotification(data.message || 'Gagal menandai semua insight', 'error');
        }
    })
    .catch(error => {
        console.error('markAllAsRead error:', error);
        showNotification(error.message || 'Gagal menandai semua insight', 'error');
    });
};

document.addEventListener('DOMContentLoaded', function() {
    const insightsModal = document.getElementById('insightsModal');
    
    if (insightsModal && !document.getElementById('noOutletModal')) {
        setTimeout(() => {
            insightsModal.classList.remove('hidden');
        }, 800);
    }
    
    document.addEventListener('keydown', function(e) {
        if (!insightsModal || insightsModal.classList.contains('hidden')) return;
        
        if (e.key === 'ArrowRight') nextSlide();
        else if (e.key === 'ArrowLeft') prevSlide();
        else if (e.key === 'Escape') closeInsightsModal();
    });
});
</script>
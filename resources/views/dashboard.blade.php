@extends('layouts.app')

@section('title', 'Menu - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Menu</span>
</li>
@endsection

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
    <div class="w-full max-w-7xl">
        <div class="flex justify-center p-4">
<div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-5 gap-6 max-w-6xl w-full">

    <a href="#"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="1"
       data-title="Point of Sale"
       data-intro="<strong>Transaksi Sat-Set!</strong> Menu wajib untuk <strong>mencatat semua penjualan</strong> secara cepat dan akurat. Tingkatkan efisiensi kasir Anda dan hindari antrian panjang!">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-cash-register text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Point of Sale
        </span>
    </a>

    <a href="#"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="2"
       data-title="Dashboard & Statistik"
       data-intro="<strong>Monitor Performa Real-Time!</strong> Lihat ringkasan dan tren bisnis Anda dalam visual yang <strong>mudah dipahami</strong>. Siap mengambil <strong>keputusan strategis</strong> berdasarkan data terbaru.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-green-400 to-blue-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-chart-line text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Dashboard & Statistik
        </span>
    </a>

    <a href="#"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="3"
       data-title="Laporan Keseluruhan"
       data-intro="<strong>Koleksi Laporan Lengkap!</strong> Tempat untuk <strong>mengakses dan mengunduh</strong> semua laporan (harian, mingguan, bulanan). Lakukan analisis mendalam untuk <strong>strategi cuan</strong> bisnis Anda.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-file-invoice text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Laporan Keseluruhan
        </span>
    </a>

    <a href="#"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="4"
       data-title="Keuangan"
       data-intro="<strong>Kontrol Arus Kas Total!</strong> Catat semua transaksi <strong>masuk dan keluar</strong> uang. Pastikan <strong>kondisi keuangan</strong> bisnis Anda selalu sehat dan terkendali. Tidur pun nyenyak!">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-purple-400 to-pink-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-wallet text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Keuangan
        </span>
    </a>

    <a href="#"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="5"
       data-title="Produksi"
       data-intro="<strong>Pusat Efisiensi Produksi!</strong> Kelola alur kerja dari bahan mentah hingga produk akhir. <strong>Tingkatkan hasil produksi</strong> tanpa pusing dengan manajemen yang teratur.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-blue-400 to-blue-700 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-flask text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Produksi
        </span>
    </a>

    <a href="#"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="6"
       data-title="Stock Opname"
       data-intro="<strong>Detektif Stok Anti Rugi!</strong> Lakukan pencocokan <strong>stok fisik dengan data sistem</strong> secara berkala. Pastikan akurasi inventaris Anda <strong>100% *matching*</strong>.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-green-400 to-green-700 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-boxes-packing text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Stock Opname
        </span>
    </a>

    <a href="{{ route('raw-materials.index') }}"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="7"
       data-title="Bahan Baku & Supplier"
       data-intro="<strong>Logistik Anti Kehabisan!</strong> Kelola detail <strong>bahan baku dan kontak supplier</strong> andalan Anda. Dijamin, operasional dapur Anda <strong>tidak akan pernah *stuck*</strong> karena kehabisan stok.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-orange-400 to-red-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-boxes-stacked text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Bahan Baku & Supplier
        </span>
    </a>

    <a href="{{ route('products-hpp.index') }}"
       class="menu-card nav-link group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="8"
       data-title="Produk & Resep"
       data-intro="<strong>Dapur Rahasia Cuan!</strong> Atur daftar produk dan resep, plus <strong>hitung Harga Pokok Penjualan (HPP)</strong> yang akurat. Tentukan harga jual optimal agar <strong>pelanggan senang, Anda cuan</strong>!">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-yellow-400 to-green-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-utensils text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Produk & Resep
        </span>
    </a>

    <a href="{{ route('outlets.index') }}"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="9"
       data-title="Informasi Outlet"
       data-intro="<strong>Base Camp Bisnis Anda!</strong> Kelola semua <strong>data penting outlet</strong> (alamat, jam buka, dll.). Pastikan pelanggan selalu tahu di mana menemukan Anda!">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-store text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Informasi Outlet
        </span>
    </a>

    <a href="#"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="10"
       data-title="Pegawai & Hak Akses"
       data-intro="<strong>Kelola Tim Terbaik!</strong> Tambah data tim andalan dan atur <strong>siapa boleh mengakses apa</strong> di sistem. Kerja aman, kerjaan lancar terkendali.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-teal-400 to-cyan-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-users text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Pegawai & Hak Akses
        </span>
    </a>

    <a href="#"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="11"
       data-title="Penjualan & Diskon"
       data-intro="<strong>Mesin Gimmick Peningkat Omzet!</strong> Buat dan atur promo heboh, diskon menarik, dan strategi jualan yang membuat <strong>pelanggan balik lagi</strong>.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-pink-400 to-red-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-tags text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Penjualan & Diskon
        </span>
    </a>

    <a href="#"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="12"
       data-title="Kebijakan Outlet"
       data-intro="<strong>Kitab Suci SOP Bisnis!</strong> Tempat <strong>mendokumentasikan semua aturan</strong> dan standar operasional. Pastikan <strong>konsistensi</strong> dan kualitas layanan Anda terjamin.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-gray-400 to-gray-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-clipboard-list text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Kebijakan Outlet
        </span>
    </a>

    <a href="#"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="13"
       data-title="Pengaturan Akun"
       data-intro="<strong>Kontrol Akun Pribadi Anda!</strong> Atur profil, ganti <strong>password</strong>, update kontak, dan sesuaikan preferensi akun. Jaga keamanan data digital Anda!">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-gray-500 to-gray-700 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-user-gear text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Pengaturan Akun
        </span>
    </a>

    <a href="#"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="14"
       data-title="Bantuan & FAQ"
       data-intro="<strong>Pusat Solusi Cepat!</strong> Temukan jawaban atas pertanyaan umum dan <strong>panduan lengkap</strong> agar Anda semakin jago menggunakan sistem.">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-green-500 to-teal-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-circle-question text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            Bantuan & FAQ
        </span>
    </a>

    <a href="#"
       class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300"
       data-step="15"
       data-title="AI Assisten"
       data-intro="<strong>Partner Analisis Cerdas!</strong> Siap diajak diskusi tentang performa bisnis, analisis data, dan <strong>strategi cuan</strong>. Tanya apa saja, AI siap kasih <i>insight</i> tajam!">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-robot text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            AI Assisten
        </span>
    </a>

</div>
        </div>
<!-- Modal Sapaan Selamat Datang (tambahkan sebelum modal noOutlet) -->
<div id="welcomeTourModal" class="hidden modal-backdrop fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-effect"></div>
    
    <div class="modal-content relative bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 p-8 text-center transform">
        <div class="mb-6">
            <div class="flex items-center justify-center mb-4 ">
                <img src="{{ asset('assets/image/full-logo.svg') }}" alt="Logo CuanFlow" style="width: 150px; height: 150px;">
            </div>
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

// Cek dari session Laravel ATAU localStorage
const shouldShowWelcome = @json(session('show_welcome_tour', false)) || 
                         localStorage.getItem(WELCOME_KEY) === '1';

if (shouldShowWelcome && welcomeModal && !modal) {
  // Jika dari session, set ke localStorage juga untuk persistensi
  if (@json(session('show_welcome_tour', false))) {
    localStorage.setItem(WELCOME_KEY, '1');
  }
  
  // Tampilkan modal dengan smooth animation
  setTimeout(() => {
    welcomeModal.classList.remove('hidden');
    // Force reflow untuk smooth transition
    welcomeModal.offsetHeight;
  }, 600);
}

// Handler tombol "Ya, Mulai!"
if (startWelcomeTourBtn) {
  startWelcomeTourBtn.addEventListener('click', function() {
    welcomeModal.classList.add('modal-exit');
    setTimeout(() => {
      welcomeModal.classList.add('hidden');
      welcomeModal.classList.remove('modal-exit');
      startTour({auto: true, fromWelcome: true});
    }, 500); // Ubah jadi 500ms untuk smooth exit
  });
}

// Handler tombol "Nanti Saja"
if (skipTourBtn) {
  skipTourBtn.addEventListener('click', function() {
    localStorage.removeItem(WELCOME_KEY);
    welcomeModal.classList.add('modal-exit');
    setTimeout(() => {
      welcomeModal.classList.add('hidden');
      welcomeModal.classList.remove('modal-exit');
    }, 500); // Ubah jadi 500ms untuk smooth exit
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

    // Hapus localStorage setelah tour selesai
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

  // Tombol manual (kalau ada)
  const startTourBtn = document.getElementById('startTourBtn');
  if (startTourBtn) startTourBtn.addEventListener('click', () => startTour());

  // Hotkey testing: Ctrl/⌘ + H
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
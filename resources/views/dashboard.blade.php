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
       data-intro="Si Paling Sat Set! Menu andalan buat kamu yang mau ngegas transaksi secepat kilat. Catat semua penjualan dengan akurat, bye-bye antrian panjang!">
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
       data-intro="Jagoan Baca Pikiran Outlet! Lihat performa bisnismu kayak nonton film di bioskop - jelas & full colour! Pantau penjualan, tren, dan data-data penting lainnya buat ambil keputusan terbaik.">
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
       data-intro="Koleksi Lengkap Rahasia Sukses! Mau laporan harian, mingguan, atau bulanan? Semuanya ada di sini! Tinggal download, analisis, dan siap-siap cuan bertubi-tubi!">
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
       data-intro="Bodyguard Dompet Outlet! Atur arus kas, catat masuk-keluar uang, dan pastikan keuangan bisnismu selalu sehat. Cash flow aman, tidur pun nyenyak.">
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
       data-intro="Laboratorium Cuan! Dari bahan mentah jadi produk siap jual, semua proses diatur di sini. Efisienkan produksi, hasilkan lebih banyak tanpa pusing.">
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
       data-intro="Detektif Stok Sejati! Waktunya cocokkin stok fisik dengan data sistem! Pastikan semua barang matching. Stok akurat = anti rugi!">
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
       data-intro="Pusat Logistik Anti Kehabisan! Kelola semua bahan baku dan kontak supplier andalanmu. Dijamin, dapur nggak akan stuck karena kehabisan stok penting!">
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
       data-intro="Dapur Rahasia HPP Terbaik! Atur daftar produk kerenmu plus resep rahasia dengan hitungan HPP yang pas. Tentukan harga jual yang bikin pelanggan senang, kamu cuan!">
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
       data-intro="The Base Camp Bisnismu! Atur semua data penting outlet - alamat, jam buka, dan info lainnya. Pastikan pelanggan tahu di mana menemukan kamu!">
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
       data-intro="Kapten dan Kru Terbaikmu! Tambah tim andalanmu, lalu atur siapa boleh pegang apa. Sistem aman, kerjaan pun lancar terkendali.">
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
       data-intro="Mesin Gimmick Cuan! Bikin promo heboh, diskon menggiurkan, dan strategi jualan yang bikin pelanggan balik lagi. Gaspol omzet!">
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
       data-intro="Kitab Suci SOP! Tempat mendokumentasikan semua aturan main dan standar operasional. Konsistensi terjamin, semua berjalan sesuai rencana.">
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
       data-intro="My Self-Care Digital! Atur profile kamu sendiri. Ganti password, update kontak, dan sesuaikan preferensi akun. Keep your digital life fresh!">
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
       data-intro="Siap Sedia 24 Jam! Ada pertanyaan? Cari jawabannya di sini! Panduan lengkap dan tips-tips biar kamu makin jago pakai CuanFlow.">
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
       data-intro="Si Paling Bisa Diajak Ngobrol! Partner diskusi kamu tentang performa bisnis, analisa data, dan strategi cuan. Tanya apa aja, dia siap kasih insight cerdas!">
        <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
            <i class="fa-solid fa-robot text-4xl sm:text-5xl text-white"></i>
        </div>
        <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">
            AI Assisten
        </span>
    </a>

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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  /* ====== MODAL LOGIC (tetap seperti sebelumnya) ====== */
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

  /* ====== TOUR CORE (no dependency) ====== */
  const TOUR_KEY = 'cuanflow_menu_tour_done';

  // Create overlay + popover container once
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

  // helper “tour selesai” toast
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
      side:  'bottom' // simple heuristic; kita auto-posisikan
    }));
  }

  function clamp(n,min,max){ return Math.max(min, Math.min(max,n)); }

  function updateLayout() {
    const s = steps[idx];
    if (!s) return;

    // scroll element ke tengah viewport
    s.el.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' });

    // setelah scroll settle, posisikan spotlight
    requestAnimationFrame(() => {
      const r = s.el.getBoundingClientRect();
      const cx = r.left + r.width/2;
      const cy = r.top  + r.height/2;
      const rad = Math.ceil(Math.hypot(r.width/2, r.height/2) + 18); // sedikit padding

      overlay.style.setProperty('--spot-x', `${cx}px`);
      overlay.style.setProperty('--spot-y', `${cy}px`);
      overlay.style.setProperty('--spot-r', `${rad}px`);

      // posisikan popover: prefer bawah, fallback atas, lalu samping
      // ukur popover terlebih dulu (set invisible enter=0, lalu kalkulasi)
      pop.style.visibility = 'hidden';
      pop.dataset.enter = '0';
      titleEl.textContent = s.title;
      descEl.textContent  = s.desc;
      progressEl.textContent = `${idx+1} dari ${steps.length}`;

      // ukur
      pop.style.left = '0px';
      pop.style.top  = '0px';
      pop.style.display = 'block';
      const pw = pop.offsetWidth;
      const ph = pop.offsetHeight;

      const gap = 14; // jarak dari target
      let left = cx - pw/2;
      let top  = r.bottom + gap;

      // jika tidak muat bawah, coba atas
      if (top + ph > window.innerHeight - 8) {
        top = r.top - ph - gap;
      }
      // jika tetap tidak muat, geser ke samping kanan
      if (top < 8) {
        top = clamp(cy - ph/2, 8, window.innerHeight - ph - 8);
        left = r.right + gap;
        if (left + pw > window.innerWidth - 8) {
          // samping kiri
          left = r.left - pw - gap;
        }
      }

      // clamp ke viewport
      left = clamp(left, 8, window.innerWidth - pw - 8);
      top  = clamp(top,  8, window.innerHeight - ph - 8);

      pop.style.left = `${left}px`;
      pop.style.top  = `${top}px`;
      pop.style.visibility = 'visible';
      // anim show
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

    // state tombol
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

    if (autoMode) {
      localStorage.setItem(TOUR_KEY, '1');
      // toast
      note.style.display = 'block';
      setTimeout(() => { note.style.display = 'none'; }, 3500);
    }
    autoMode = false;
    window.removeEventListener('resize', updateLayout);
    window.removeEventListener('scroll', updateLayout, true);
  }

  function startTour({auto=false, force=false} = {}) {
    // jangan auto start jika masih ada modal outlet, kecuali force (testing)
    if (!force && document.getElementById('noOutletModal')) return;

    collectSteps();
    if (!steps.length) return;

    autoMode = !!auto;

    // events
    window.addEventListener('resize', updateLayout);
    window.addEventListener('scroll', updateLayout, true);

    overlay.onclick = next;        // klik dim area = Next
    prevBtn.onclick = prev;
    nextBtn.onclick = next;
    closeBtn.onclick = endTour;

    // keyboard
    document.addEventListener('keydown', onKey, { passive: false });

    function onKey(e){
      if (overlay.style.display !== 'block') { document.removeEventListener('keydown', onKey); return; }
      if (e.key === 'Escape') { e.preventDefault(); endTour(); }
      else if (e.key === 'ArrowRight' || e.key === 'Enter') { e.preventDefault(); next(); }
      else if (e.key === 'ArrowLeft') { e.preventDefault(); prev(); }
    }

    showStep(0);
  }

  // Auto start untuk first-time users
  if (!localStorage.getItem(TOUR_KEY)) {
    setTimeout(() => startTour({auto:true}), 900);
  }

  // Tombol manual (kalau ada)
  const startTourBtn = document.getElementById('startTourBtn');
  if (startTourBtn) startTourBtn.addEventListener('click', () => startTour());

  // ====== HOTKEY TESTING: Ctrl/⌘ + H untuk FORCE start ======
  document.addEventListener('keydown', function(e) {
    const isCtrlOrMeta = e.ctrlKey || e.metaKey;
    if (isCtrlOrMeta && e.key.toLowerCase() === 'h') {
      e.preventDefault();                   // cegah History
      localStorage.removeItem(TOUR_KEY);    // reset progress agar selalu dari awal
      startTour({auto:false, force:true});  // paksa tampil meski ada modal
    }
  });
});
</script>
@endpush

@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
    $user = auth()->user();
    if ($user && !$user->hasRole('owner') && !$user->hasRole('admin')) {
        if (!$user->canAccessFeature('employee_management')) {
            session(['employee_lock_reason' => 'no_subscription']);
            redirect()->route('employee.locked')->send();
            exit;
        }
    }
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

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
    display: none;         /* ditampilkan via JS */
    pointer-events: auto;
    background: rgba(15,23,42,0.82); /* slate-900/80 */
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
  50%   { opacity: 1;  }
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
    max-height: 38vh;     /* prevent overflow on small screens */
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
    to  { opacity: 1; transform: translateY(0) scale(1); }
  }
  @keyframes modalExit {
    from { opacity: 1; transform: scale(1); }
    to  { opacity: 0; transform: scale(0.95); }
  }
  .modal-backdrop { animation: fadeIn 0.3s ease-out; }
  .modal-content { animation: modalSlideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
  .modal-exit .modal-content { animation: modalExit 0.2s ease-in forwards; }

  /* Menu Card Animations */
  @keyframes menuCardEntry { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
  .menu-card { opacity: 0; animation: menuCardEntry 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; border: 1px solid transparent; }
  .menu-card:hover { background-color: transparent; border-color: transparent; }
  .menu-card .menu-icon { transition: transform 0.25s ease; }
  .menu-card:hover .menu-icon { transform: scale(1.05); }
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
  .menu-card:nth-child(24) { animation-delay: 1.2s; }
  .menu-card:nth-child(25) { animation-delay: 1.25s; }
  .menu-card:nth-child(26) { animation-delay: 1.3s; }
  .menu-card:nth-child(27) { animation-delay: 1.35s; }
  .menu-card:nth-child(28) { animation-delay: 1.4s; }
  .menu-card:nth-child(29) { animation-delay: 1.45s; }
  .menu-card:nth-child(30) { animation-delay: 1.5s; }
  .menu-card:nth-child(31) { animation-delay: 1.55s; }
  .menu-card:nth-child(32) { animation-delay: 1.6s; }
  .menu-card:nth-child(33) { animation-delay: 1.65s; }
  .menu-card:nth-child(34) { animation-delay: 1.7s; }
  .menu-card:nth-child(35) { animation-delay: 1.75s; }
  .menu-card:nth-child(36) { animation-delay: 1.8s; }
  .menu-card:nth-child(37) { animation-delay: 1.85s; }
  .menu-card:nth-child(38) { animation-delay: 1.9s; }
  .menu-card:nth-child(39) { animation-delay: 1.95s; }


  .backdrop-blur-effect { backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); }

  /* ========== EXISTING STYLES (kept) ========== */

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
    background: rgba(15, 23, 42, 0.42);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    z-index: 100;
    animation: fadeIn 0.4s ease-out;
  }

  .insights-modal-content {
    animation: modalSlideUp 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    max-height: 85vh;
    display: flex;
    flex-direction: column;
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
    width: 3.5rem;
    height: 3.5rem;
    flex-shrink: 0;
    border-radius: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    transition: all 0.3s ease;
  }

  @media (max-width: 640px) {
    .insight-type-icon {
      width: 3rem;
      height: 3rem;
      font-size: 1.5rem;
      border-radius: 1rem;
    }
  }

  .type-sales_trend { background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 8px 16px -4px rgba(16, 185, 129, 0.25); }
  .type-stock_prediction { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); box-shadow: 0 8px 16px -4px rgba(245, 158, 11, 0.25); }
  .type-product_recommendation { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); box-shadow: 0 8px 16px -4px rgba(139, 92, 246, 0.25); }
  .type-anomaly { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); box-shadow: 0 8px 16px -4px rgba(239, 68, 68, 0.25); }
  .type-general { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); box-shadow: 0 8px 16px -4px rgba(59, 130, 246, 0.25); }

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
      max-height: 75vh;
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
<div class="relative min-h-[calc(100vh-64px)] flex flex-col">
  @hasrole('owner')
  @include('subscription.modal')
  @endhasrole
  <div class="flex-grow flex items-center justify-center py-8 px-4">
  <div class="w-full max-w-6xl">

  {{-- Subscription Grace Period Warning --}}
  @if(auth()->user()->hasRole('owner') && auth()->user()->subscription && auth()->user()->subscription->isInGracePeriod())
  <div class="max-w-4xl mx-auto mb-6 px-4">
    <div class="bg-gradient-to-r from-amber-500/10 via-orange-500/10 to-red-500/10 border border-amber-200 rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row items-center gap-4 relative overflow-hidden group transition-all hover:shadow-lg">
      {{-- Animated Background Shine --}}
      <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-[shimmer_2s_infinite]"></div>
      
      <div class="flex-shrink-0 w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600 shadow-sm">
        <i class="fa-solid fa-triangle-exclamation text-2xl animate-pulse"></i>
      </div>
      
      <div class="flex-grow text-center sm:text-left">
        <h3 class="text-amber-900 font-bold text-base sm:text-lg mb-0.5">Masa Tenggang Berlangganan</h3>
        <p class="text-amber-800/80 text-sm leading-relaxed">
          Langganan Anda telah berakhir. Anda memiliki <span class="font-bold text-amber-900">{{ auth()->user()->subscription->grace_days_remaining }} hari lagi</span> sebelum akses fitur benar-benar dihentikan.
        </p>
      </div>
      
      <div class="flex-shrink-0 w-full sm:w-auto mt-2 sm:mt-0">
        <a href="{{ route('subscription.index') }}" class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl transition-all shadow-md hover:shadow-amber-200 transform hover:-translate-y-0.5 active:translate-y-0">
          Beli Paket Sekarang
          <i class="fa-solid fa-arrow-right ml-2 text-sm italic group-hover:translate-x-1 transition-transform"></i>
        </a>
      </div>
    </div>
  </div>
  @endif

  {{-- Search Bar Component --}}
  <div class="max-w-3xl mx-auto mb-8 px-4 relative z-30">
    <div class="relative group">
      <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
        <i class="fa-solid fa-magnifying-glass text-gray-400 group-focus-within:text-indigo-500 transition-colors duration-300"></i>
      </div>
      <input 
        type="text" 
        id="globalSearchInput"
        class="block w-full pl-11 pr-12 py-4 border-gray-200 rounded-2xl leading-5 bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 shadow-sm transition-all duration-300 transform focus:scale-[1.01]"
        placeholder="Cari menu, fitur, atau buat data baru (contoh: 'resep', 'keuangan')..."
        autocomplete="off"
      >
      <div id="searchLoader" class="absolute inset-y-0 right-0 pr-4 flex items-center opacity-0 transition-opacity duration-200">
         <i class="fa-solid fa-circle-notch fa-spin text-indigo-500"></i>
      </div>
      
      {{-- Kbd shortcut hint --}}
      <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none" id="searchShortcutHint">
        <span class="text-xs text-gray-400 border border-gray-200 rounded px-1.5 py-0.5 bg-gray-50">/</span>
      </div>

      {{-- Results Dropdown --}}
      <div 
        id="searchResults" 
        class="absolute left-0 w-full mt-2 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden hidden transition-all duration-200 origin-top z-50 text-left"
        style="max-height: 400px; overflow-y: auto;"
      >
        {{-- Injected by JS --}}
      </div>
    </div>
  </div>


<div class="db font-satoshi min-h-screen pb-20">
  {{-- Premium Fonts --}}
  <link href="https://api.fontshare.com/v2/css?f[]=satoshi@900,700,500,400&display=swap" rel="stylesheet">

  <style>
    .font-satoshi { font-family: 'Satoshi', sans-serif; }

    /* ══════════════════════════════════ */
    /* MINIMALIST SCROLLBAR               */
    /* ══════════════════════════════════ */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: rgba(0, 0, 0, 0.05); border-radius: 10px; transition: all 0.3s; }
    ::-webkit-scrollbar-thumb:hover { background: rgba(0, 0, 0, 0.2); }

    /* For Firefox */
    * { scrollbar-width: thin; scrollbar-color: rgba(0, 0, 0, 0.05) transparent; }
    
    /* ══════════════════════════════════ */
    /* DASHBOARD GRID                     */
    /* ══════════════════════════════════ */
    .section-label { padding: 32px 0 16px; font-size: 13px; font-weight: 800; color: #1e293b; letter-spacing: 0.1em; text-transform: uppercase; opacity: 0.5; }
    .main-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-bottom: 24px; }
    @media (max-width: 992px) { .main-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 640px) { .main-grid { grid-template-columns: 1fr; } }

    /* Category Card */
    .main-card { 
      border-radius: 24px; border: 1px solid rgba(0, 0, 0, 0.05); background: white;
      padding: 24px; cursor: pointer; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
      box-shadow: 0 4px 12px rgba(0,0,0,0.02); display: flex; flex-direction: column;
    }
    .main-card:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(0,0,0,0.06); border-color: #7c3aed20; }
    
    .card-icon { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 16px; }
    .card-icon.sell { background: #fee2e2; color: #ef4444; }
    .card-icon.money { background: #ede9fe; color: #7c3aed; }
    .card-icon.products { background: #ffedd5; color: #f97316; }
    .card-icon.ai { background: #f5f3ff; color: #8b5cf6; }
    .card-icon.more { background: #f1f5f9; color: #64748b; }
    
    .card-title { font-size: 17px; font-weight: 700; color: #0f172a; margin-bottom: 6px; }
    .card-desc { font-size: 13px; color: #64748b; line-height: 1.5; height: 3em; overflow: hidden; opacity: 0.8; }
    .card-meta { display: flex; align-items: center; justify-content: space-between; margin-top: auto; padding-top: 18px; border-top: 1px solid #f8fafc; }
    .card-arrow { width: 28px; height: 28px; border-radius: 50%; background: #f8fafc; display: flex; align-items: center; justify-content: center; color: #cbd5e1; font-size: 10px; transition: 0.3s; }
    .main-card:hover .card-arrow { background: #0f172a; color: white; transform: rotate(-45deg); }

    /* ══════════════════════════════════ */
    /* PREMIUM DRAWER                     */
    /* ══════════════════════════════════ */
    .folder-overlay { display: none; position: fixed; inset: 0; z-index: 1000; align-items: flex-end; justify-content: center; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(8px); }
    .folder-active { display: flex !important; }

    .drawer { 
      background: white; border-radius: 40px 40px 0 0; padding: 45px 35px; 
      width: 100%; max-width: 820px; max-height: 85vh; overflow-y: auto; position: relative;
      box-shadow: 0 -20px 60px rgba(0,0,0,0.1); border: 1px solid rgba(255,255,255,0.7);
    }
    @media (min-width: 640px) { .folder-overlay { align-items: center; padding: 20px; } .drawer { border-radius: 40px; } }
    .drawer-header { display: flex; align-items: center; gap: 24px; margin-bottom: 35px; border-bottom: 1px solid #f1f5f9; padding-bottom: 25px; }
    
    .drawer-items { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
    @media (max-width: 640px) { .drawer-items { grid-template-columns: repeat(2, 1fr); } }

    .ditem { 
      display: flex; flex-direction: column; background: #fcfcfd; border: 1px solid #f1f5f9; 
      border-radius: 28px; padding: 24px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      text-decoration: none !important;
    }
    .ditem:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.06); border-color: #7c3aed30; background: white; }
    .ditem-icon { width: 52px; height: 52px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 18px; }
    .ditem-label { font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
    .ditem-desc { font-size: 11px; color: #94a3b8; line-height: 1.5; }

    .close-btn { margin-left: auto; width: 44px; height: 44px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.3s; }
    .close-btn:hover { background: #ef4444; color: white; transform: rotate(90deg); }

    /* ══════════════════════════════════ */
    /* DYNAMIC FAB (LIGHT MODE)           */
    /* ══════════════════════════════════ */
    .fab { 
      position: fixed; bottom: 35px; right: 35px; 
      background: rgba(255, 255, 255, 0.9); 
      backdrop-filter: blur(10px);
      color: #0f172a; border-radius: 60px; height: 60px; min-width: 60px;
      display: flex; align-items: center; justify-content: center; padding: 0 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); 
      transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      z-index: 1000; overflow: hidden; white-space: nowrap; text-decoration: none !important;
    .fab i { font-size: 24px; color: #34d399; }

    @media (max-width: 640px) { .fab { bottom: 25px; right: 25px; height: 54px; min-width: 54px; } .fab-text { display: none; } }
  </style>

  <div class="max-w-6xl mx-auto px-6">
    <div class="section-label flex items-center gap-2">
       <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
       DAFTAR LAYANAN CUANFLOW
    </div>
    
    <div id="menuGrid" class="main-grid">
      @if(isset($categories) && $categories->count() > 0)
        @php
            $sortedCategories = $categories->sortBy(function($cat) {
                return $cat->slug === 'ai-tools' ? 7.5 : $cat->sort_order;
            });
        @endphp
        @foreach($sortedCategories as $category)
          @include('components.menu.category-group', [
            'category' => $category,
            'isPosOpen' => $isPosOpen ?? false,
            'isReseller' => $isReseller ?? false,
            'unreadStockCount' => $unreadStockCount ?? 0,
          ])
        @endforeach
      @endif
    </div>
    <div class="pb-24"></div>
  </div>

  {{-- New Dynamic FAB --}}
  <a href="{{ route('pos.index') }}" class="fab group">
    <i class="ph-bold ph-shopping-cart-simple"></i>
    <!-- <span class="fab-text">Transaksi Baru</span> -->
  </a>
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

<!-- Modal Pilihan Langganan (muncul setelah onboarding tour selesai untuk user baru) -->
@if(auth()->check() && auth()->user()->hasRole('owner') && !auth()->user()->subscriptions()->exists() && !is_null(auth()->user()->outlet_id))
<div id="subscriptionChoiceModal" class="hidden modal-backdrop fixed inset-0 z-50 flex items-center justify-center">
  <div class="absolute inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-effect"></div>
  
  <div class="modal-content relative bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 p-8 text-center transform">
    <div class="mb-6">
      <div class="mx-auto w-20 h-20 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center mb-4">
        <i class="fa-solid fa-rocket text-4xl text-white"></i>
      </div>
      <h2 class="text-2xl font-bold text-gray-900 mb-3">Siap Memulai Bisnis Anda?</h2>
      <p class="text-gray-600 leading-relaxed">
        Pilih cara Anda untuk mulai menggunakan CuanFlow dan kelola bisnis Anda dengan lebih mudah.
      </p>
    </div>
    
    <div class="space-y-3">
      <!-- Trial Option -->
      <a href="{{ route('subscription.trial-verification') }}" 
        onclick="localStorage.setItem('cuanflow_onboarding_completed', '1'); destroyOnboardingFlag();"
        class="block w-full bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-semibold py-4 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
        <i class="fa-solid fa-gift mr-2"></i>
        Coba Gratis {{ \App\Models\SubscriptionSetting::getTrialDays() }} Hari
        <p class="text-xs font-normal opacity-80 mt-1">Akses semua fitur tanpa biaya</p>
      </a>
      
      <!-- Buy Subscription Option -->
      <button id="showSubscriptionModalBtn" 
          class="block w-full bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-semibold py-4 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
        <i class="fa-solid fa-crown mr-2"></i>
        Beli Paket Langganan
        <p class="text-xs font-normal opacity-80 mt-1">Pilih paket sesuai kebutuhan Anda</p>
      </button>
      
      <!-- Re-explore Option -->
      <button id="reExploreTourBtn" 
          class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-xl transition-all duration-200">
        <i class="fa-solid fa-compass mr-2"></i>
        Jelajahi Lagi Fitur CuanFlow
      </button>
    </div>
    
    <p class="text-xs text-gray-400 mt-4">
      <i class="fa-solid fa-shield-check mr-1"></i>
      Data Anda aman bersama kami
    </p>
  </div>
</div>
@endif
</div>
</div>
</div>

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

@canAccessFeature('ai_insights')
@if(isset($unreadInsights) && $unreadInsights->isNotEmpty())
@can('lihat ai insights')
<div id="insightsModal" class="hidden insights-modal-backdrop fixed inset-0 flex items-center justify-center z-[100]">
  <div class="insights-modal-content relative bg-white rounded-3xl shadow-2xl max-w-xl w-full mx-4">
    <!-- Header -->
    <div class="p-6 md:p-8 bg-gradient-to-br from-gray-50 to-white border-b border-gray-100 rounded-t-3xl">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-cuan-green to-cuan-dark rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-cuan-green/20">
            <img src="{{ asset('assets/image/clara-ai.png') }}" class="w-10 h-10 md:w-12 md:h-12 object-contain" alt="">
          </div>
          <div>
            <h2 class="text-xl md:text-2xl font-black text-gray-900 leading-tight">Insight Baru dari Clara AI</h2>
            <p class="text-xs md:text-sm font-bold text-gray-500 uppercase tracking-widest mt-0.5">{{ $unreadInsights->count() }} insight untuk Anda</p>
          </div>
        </div>
        <button onclick="closeInsightsModal()" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-900">
          <i class="fa-solid fa-times text-xl"></i>
        </button>
      </div>
    </div>

    <div class="px-3 md:px-6 pb-6 overflow-y-auto overflow-x-hidden flex-grow">
      <!-- Carousel Container -->
      <div class="relative mt-2">
      <div id="insightsCarousel" class="overflow-hidden pb-2">
        <div id="carouselTrack" class="flex transition-transform duration-300 ease-in-out">
          @foreach($unreadInsights as $index => $insight)
          <div class="carousel-slide w-full flex-shrink-0 px-2 flex" data-insight-id="{{ $insight->id }}">
            <div class="border border-gray-100 rounded-[1.5rem] flex flex-col w-full bg-white shadow-sm p-0 mb-2">
              <div class="p-5 md:p-8 flex-grow flex flex-col">
                <!-- Header Card -->
                <div class="flex items-start justify-between mb-4 md:mb-6">
                  <div class="flex items-center gap-4">
                    <div class="insight-type-icon type-{{ $insight->type }} flex-shrink-0 !rounded-2xl">
                      <i class="fa-solid fa-{{ $insight->type === 'sales_trend' ? 'chart-line' : 
                        ($insight->type === 'stock_prediction' ? 'boxes-stacked' : 
                        ($insight->type === 'product_recommendation' ? 'star' : 
                        ($insight->type === 'anomaly' ? 'exclamation-triangle' : 'lightbulb'))) }} text-white"></i>
                    </div>
                    <div>
                      <h3 class="font-black text-base md:text-lg text-gray-900 leading-snug">{{ $insight->title }}</h3>
                      <p class="text-[10px] md:text-xs font-bold text-gray-400 uppercase tracking-wider">{{ $insight->insight_date->format('d M Y') }}</p>
                    </div>
                  </div>
                  <span class="severity-badge severity-{{ $insight->severity }} shadow-sm flex-shrink-0">
                    @if($insight->severity === 'critical')
                      <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                    @elseif($insight->severity === 'warning')
                      <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                    @else
                      <i class="fa-solid fa-circle-info text-[10px]"></i>
                    @endif
                    <span class="hidden sm:inline">{{ ucfirst($insight->severity) }}</span>
                  </span>
                </div>

                <!-- Content -->
                <div class="flex-grow mb-5 md:mb-6">
                  <div class="text-gray-600 leading-relaxed text-[13px] md:text-sm bg-gray-50/80 rounded-2xl p-4 md:p-5 border border-gray-100 italic" style="display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;">
                    {!! nl2br(e($insight->content)) !!}
                  </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-3 pt-5 md:pt-6 border-t border-gray-100 mt-auto">
                  <button onclick="markAsRead({{ $insight->id }})" 
                      class="flex-1 bg-cuan-green hover:bg-cuan-dark text-white font-black text-[10px] md:text-xs uppercase tracking-widest py-3.5 px-6 rounded-2xl transition-all duration-200 shadow-lg shadow-cuan-green/20 active:scale-95 flex items-center justify-center relative overflow-hidden group">
                    <span class="absolute inset-0 w-full h-full bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></span>
                    <i class="fa-solid fa-check mr-2"></i>
                    Selesai
                  </button>
                  <a href="{{ route('ai-insights.index') }}" 
                      class="flex-1 bg-gray-50 hover:bg-gray-100 text-gray-700 font-black text-[10px] md:text-xs uppercase tracking-widest py-3.5 px-6 rounded-2xl border border-gray-200 hover:border-gray-300 transition-all duration-200 text-center flex items-center justify-center active:scale-95">
                    <i class="fa-solid fa-arrow-right-long mr-2 text-gray-400 group-hover:text-gray-700 transition-colors pointer-events-none"></i>
                    Lihat Detail
                  </a>
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>

      <!-- Navigation Buttons -->
      @if($unreadInsights->count() > 1)
      <button onclick="prevSlide()" class="carousel-btn prev !left-0 md:!-left-4">
        <i class="fa-solid fa-chevron-left text-gray-600"></i>
      </button>
      <button onclick="nextSlide()" class="carousel-btn next !right-0 md:!-right-4">
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

    </div>
 
    <!-- Footer -->
    <div class="px-6 md:px-8 pb-6 md:pb-8">
      <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-6 border-t border-gray-100">
        <a href="{{ route('ai-insights.index') }}" class="inline-flex items-center text-cuan-green hover:text-cuan-dark font-black text-xs uppercase tracking-widest transition-colors">
          <i class="fa-solid fa-list mr-2 text-sm"></i>
          Lihat Semua Insight
        </a>
        <button onclick="markAllAsRead()" class="inline-flex items-center text-gray-500 hover:text-gray-900 font-black text-xs uppercase tracking-widest transition-colors">
          <i class="fa-solid fa-check-double mr-2 text-sm"></i>
          Tandai Semua Sudah Baca
        </button>
      </div>
    </div>
  </div>
</div>
@endcan
@endif
@endcanAccessFeature

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
 const ONBOARDING_COMPLETED_KEY = 'cuanflow_onboarding_completed';

 // Check if this is a new user (from middleware session flag)
 const isNewUser = @json(session('new_user_onboarding', false));
 const forceSubscriptionChoice = @json(session('force_subscription_choice', false));
 const hasOutlet = @json(auth()->user()->outlet_id !== null);
 const hasNoSubscription = @json(auth()->check() && !auth()->user()->subscriptions()->exists());
 
 const shouldShowWelcome = @json(session('show_welcome_tour', false)) || 
              localStorage.getItem(WELCOME_KEY) === '1';

 // For users with outlet but no subscription, or new users who haven't completed onboarding
 if (hasOutlet && !modal && !shouldShowWelcome) {
  const onboardingCompleted = localStorage.getItem(ONBOARDING_COMPLETED_KEY) === '1';
  
  // If no subscription, OR force flag is set, OR onboarding not completed for new user, show modal
  if (hasNoSubscription || forceSubscriptionChoice || (isNewUser && !onboardingCompleted)) {
   setTimeout(() => {
    showSubscriptionChoiceModal();
   }, 1000);
  }
 }

 if (shouldShowWelcome && welcomeModal && !modal) {
  if (@json(session('show_welcome_tour', false))) {
   localStorage.setItem(WELCOME_KEY, '1');
  }
  
  setTimeout(() => {
   welcomeModal.classList.remove('hidden');
   welcomeModal.offsetHeight;
  }, 1000); // Wait for page to fade-in fully
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
    // Show subscription choice modal for new users
    showSubscriptionChoiceModal();
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
   desc: el.dataset.intro || '',
   side: 'bottom'
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
   const cy = r.top + r.height/2;
   const rad = Math.ceil(Math.hypot(r.width/2, r.height/2) + 18);

   overlay.style.setProperty('--spot-x', `${cx}px`);
   overlay.style.setProperty('--spot-y', `${cy}px`);
   overlay.style.setProperty('--spot-r', `${rad}px`);

   pop.style.visibility = 'hidden';
   pop.dataset.enter = '0';
   titleEl.textContent = s.title;
   descEl.innerHTML = s.desc;
   progressEl.textContent = `${idx+1} dari ${steps.length}`;

   pop.style.left = '0px';
   pop.style.top = '0px';
   pop.style.display = 'block';
   const pw = pop.offsetWidth;
   const ph = pop.offsetHeight;

   const gap = 14;
   let left = cx - pw/2;
   let top = r.bottom + gap;

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
   top = clamp(top, 8, window.innerHeight - ph - 8);

   pop.style.left = `${left}px`;
   pop.style.top = `${top}px`;
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
   // Show subscription choice modal for new users after tour ends
   showSubscriptionChoiceModal();
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

 /* ====== SUBSCRIPTION CHOICE MODAL LOGIC ====== */
 const subscriptionChoiceModal = document.getElementById('subscriptionChoiceModal');
 const showSubscriptionModalBtn = document.getElementById('showSubscriptionModalBtn');
 const reExploreTourBtn = document.getElementById('reExploreTourBtn');

 function showSubscriptionChoiceModal() {
  if (subscriptionChoiceModal) {
   setTimeout(() => {
    subscriptionChoiceModal.classList.remove('hidden');
   }, 300);
  }
 }

 function hideSubscriptionChoiceModal() {
  if (subscriptionChoiceModal) {
   subscriptionChoiceModal.classList.add('modal-exit');
   setTimeout(() => {
    subscriptionChoiceModal.classList.add('hidden');
    subscriptionChoiceModal.classList.remove('modal-exit');
   }, 300);
  }
 }

// Helper to destroy onboarding flag (Expose to window for onclick usage)
window.destroyOnboardingFlag = function() {
  fetch('{{ route("subscription.destroy-onboarding-flag") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'X-Requested-With': 'XMLHttpRequest',
    }
  }); // Fire and forget
};

 // Show subscription packages modal
 if (showSubscriptionModalBtn) {
  showSubscriptionModalBtn.addEventListener('click', function() {
   hideSubscriptionChoiceModal();
   // Mark onboarding as completed
   localStorage.setItem('cuanflow_onboarding_completed', '1');
   // Destroy server session flag
   destroyOnboardingFlag();
   
   // Trigger showing subscription modal by setting session via AJAX
   fetch('{{ route("subscription.show-modal") }}', {
    method: 'POST',
    headers: {
     'Content-Type': 'application/json',
     'X-CSRF-TOKEN': '{{ csrf_token() }}',
     'X-Requested-With': 'XMLHttpRequest',
    }
   }).then(() => {
    window.location.reload();
   }).catch(() => {
    // Fallback: redirect to subscription page
    window.location.href = '{{ route("subscription.index") }}';
   });
  });
 }

 // Re-explore tour button
 if (reExploreTourBtn) {
  reExploreTourBtn.addEventListener('click', function() {
   hideSubscriptionChoiceModal();
   setTimeout(() => {
    startTour({auto: true, force: true, fromWelcome: true});
   }, 400);
  });
 }
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
  const dots = document.querySelectorAll('.carousel-dot');
  
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
    document.body.classList.remove('modal-open');
    document.body.classList.remove('insights-modal-open');
    setTimeout(() => {
      insightsModal.classList.add('hidden');
      insightsModal.classList.remove('insights-modal-exit');
    }, 300);
  }
}

window.openInsightsModal = function() {
  const insightsModal = document.getElementById('insightsModal');
  if (insightsModal) {
    insightsModal.classList.remove('hidden');
    document.body.classList.add('modal-open');
    document.body.classList.add('insights-modal-open');
  }
}

// Update the initial appearance logic if any, but since it's unread list based:
document.addEventListener('DOMContentLoaded', function() {
  const insightsModal = document.getElementById('insightsModal');
  if (insightsModal && !insightsModal.classList.contains('hidden')) {
    document.body.classList.add('modal-open');
    document.body.classList.add('insights-modal-open');
  }
});
;

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
      const dots  = document.querySelectorAll('.carousel-dot');

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

// helper: remove insight slide/dot from carousel UI without calling server
window.removeInsightFromCarousel = function(insightId) {
  const slides = document.querySelectorAll('.carousel-slide');
  const dots  = document.querySelectorAll('.carousel-dot');

  // Find slide index by data-id attribute (if present), fallback to currentSlide
  let index = -1;
  slides.forEach((s, i) => {
    if (s.dataset && s.dataset.insightId && String(s.dataset.insightId) === String(insightId)) {
      index = i;
    }
  });
  if (index === -1) index = currentSlide;

  if (slides[index]) {
    slides[index].remove();
    if (dots[index]) dots[index].remove();
  }

  const remainingSlides = document.querySelectorAll('.carousel-slide');
  totalSlides = remainingSlides.length;

  if (totalSlides === 0) {
    closeInsightsModal();
    showNotification('Insight berhasil di-dismiss', 'success');
  } else {
    if (currentSlide >= totalSlides) {
      currentSlide = totalSlides - 1;
    }
    updateCarousel();
    showNotification('Insight berhasil di-dismiss', 'success');
  }
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
      // Setelah dismiss di server, update UI langsung tanpa memanggil markAsRead
      window.removeInsightFromCarousel(insightId);
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
      window.openInsightsModal();
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
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('globalSearchInput');
  const searchResults = document.getElementById('searchResults');
  const searchLoader = document.getElementById('searchLoader');
  const shortcutHint = document.getElementById('searchShortcutHint');
  let debounceTimer;

  // Data Source
  const searchItems = [
    @canAccessFeature('pos')
    @can('akses pos')
    { label: 'Point of Sale (Kasir)', keywords: ['pos', 'kasir', 'transaksi', 'jual'], url: "{{ route('pos.index') }}", type: 'Menu' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('sales_management')
    @can('lihat penjualan')
    { label: 'Riwayat Penjualan', keywords: ['penjualan', 'sales', 'history', 'riwayat', 'laporan'], url: "{{ route('sales.index') }}", type: 'Menu' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('discount_management')
    @can('lihat diskon')
    { label: 'Daftar Diskon', keywords: ['diskon', 'promo', 'potongan'], url: "{{ route('discounts.index') }}", type: 'Menu' },
    { label: 'Buat Diskon Baru', keywords: ['buat', 'tambah', 'diskon', 'promo'], url: "{{ route('discounts.create') }}", type: 'Action' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('finance_management')
    @can('lihat keuangan')
    { label: 'Keuangan', keywords: ['keuangan', 'finance', 'laporan', 'uang'], url: "{{ route('finance.index') }}", type: 'Menu' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('other_income')
    @can('buat pemasukan')
    { label: 'Catat Pemasukan', keywords: ['pemasukan', 'income', 'tambah', 'uang masuk'], url: "{{ route('expenses.index', ['type' => 'income']) }}", type: 'Action' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('operational_costs')
    @can('buat pengeluaran')
    { label: 'Catat Pengeluaran', keywords: ['pengeluaran', 'expense', 'biaya', 'operasional', 'beli'], url: "{{ route('expenses.index', ['type' => 'expense']) }}", type: 'Action' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('balance_withdrawal')
    @can('buat penarikan')
    { label: 'Penarikan Saldo', keywords: ['tarik', 'saldo', 'withdraw', 'pencairan'], url: "{{ route('withdraw.index') }}", type: 'Menu' },
    { label: 'Ajukan Penarikan', keywords: ['buat', 'ajukan', 'tarik', 'saldo'], url: "{{ route('withdraw.create') }}", type: 'Action' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('payment_methods')
    @can('lihat metode pembayaran')
    { label: 'Metode Pembayaran', keywords: ['payment', 'metode', 'bayar', 'qris', 'bank'], url: "{{ route('outlet-payment-links.index') }}", type: 'Menu' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('task_management')
    @can('tasks.view')
    { label: 'Manajemen Tugas (Kanban)', keywords: ['tugas', 'task', 'kanban', 'kerja', 'project'], url: "{{ route('tasks.index') }}", type: 'Menu' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('dashboard')
    @can('lihat statistik')
    { label: 'Dashboard & Statistik', keywords: ['statistik', 'chart', 'grafik', 'analisis', 'dashboard'], url: "{{ route('statistics.index') }}", type: 'Menu' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('reports')
    @can('lihat laporan')
    { label: 'Laporan Keseluruhan', keywords: ['laporan', 'report', 'keuangan', 'pdf', 'excel'], url: "{{ route('reports.index') }}", type: 'Menu' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('track_transaction_location')
    @can('lihat peta transaksi')
    { label: 'Peta Transaksi', keywords: ['peta', 'rute', 'lokasi', 'kasir', 'transaksi', 'map'], url: "{{ route('sales-map.index') }}", type: 'Menu' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('products_recipes')
    @can('lihat produk')
    { label: 'Daftar Produk & Resep', keywords: ['produk', 'menu', 'makanan', 'minuman', 'resep', 'barang'], url: "{{ route('products-hpp.index') }}", type: 'Menu' },
    { label: 'Tambah Produk Baru', keywords: ['tambah', 'buat', 'produk', 'menu', 'resep'], url: "{{ route('products-hpp.create') }}", type: 'Action' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('raw_materials')
    @can('lihat bahan baku')
    { label: 'Stok Bahan Baku', keywords: ['bahan', 'baku', 'raw', 'material', 'stok', 'inventory'], url: "{{ route('raw-materials.index') }}", type: 'Menu' },
    { label: 'Tambah Bahan Baku', keywords: ['tambah', 'bahan', 'baku'], url: "{{ route('raw-materials.create') }}", type: 'Action' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('suppliers')
    @can('lihat supplier')
    { label: 'Daftar Pemasok (Supplier)', keywords: ['supplier', 'pemasok', 'vendor'], url: "{{ route('raw-materials.suppliers') }}", type: 'Menu' },
    { label: 'Tambah Supplier', keywords: ['tambah', 'supplier', 'pemasok'], url: "{{ route('raw-materials.suppliers.create') }}", type: 'Action' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('reseller_app')
    @can('lihat reseller applications')
    { label: 'Lamaran Reseller', keywords: ['reseller', 'mitra', 'lamaran', 'aplikasi'], url: "{{ route('reseller-applications.index') }}", type: 'Menu' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('production')
    @can('lihat produksi')
    { label: 'Daftar Produksi', keywords: ['produksi', 'production', 'olah', 'masak'], url: "{{ route('production.index') }}", type: 'Menu' },
    { label: 'Buat Produksi Baru', keywords: ['buat', 'tambah', 'produksi'], url: "{{ route('production.create') }}", type: 'Action' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('stock_opname')
    @can('lihat stock opname')
    { label: 'Stock Opname', keywords: ['stock', 'opname', 'so', 'cek', 'stok'], url: "{{ route('stock-opname.index') }}", type: 'Menu' },
    { label: 'Buat Stock Opname', keywords: ['buat', 'stock', 'opname'], url: "{{ route('stock-opname.create') }}", type: 'Action' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('stock_transfer')
    @can('lihat stock transfer')
    { label: 'Transfer Stok', keywords: ['transfer', 'kirim', 'stok', 'mutasi'], url: "{{ route('stock-transfers.index') }}", type: 'Menu' },
    { label: 'Buat Transfer Stok', keywords: ['buat', 'transfer', 'kirim'], url: "{{ route('stock-transfers.create') }}", type: 'Action' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('multi_outlet')
    @can('lihat outlet')
    { label: 'Informasi Outlet', keywords: ['outlet', 'toko', 'cabang', 'informasi', 'profil'], url: "{{ route('outlets.index') }}", type: 'Menu' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('landing_page')
    @can('lihat landing page')
    { label: 'Landing Page', keywords: ['landing', 'page', 'web', 'promosi', 'online'], url: "{{ route('landing-pages.index') }}", type: 'Menu' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('testimonials')
    @can('lihat testimoni')
    { label: 'Testimoni', keywords: ['testimoni', 'review', 'ulasan'], url: "{{ route('testimonials.index') }}", type: 'Menu' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('employee_management')
    @can('lihat pegawai')
    { label: 'Pegawai & Hak Akses', keywords: ['pegawai', 'employee', 'karyawan', 'staff', 'hrd', 'akses'], url: "{{ route('employees.index') }}", type: 'Menu' },
    { label: 'Tambah Pegawai', keywords: ['tambah', 'buat', 'pegawai', 'karyawan'], url: "{{ route('employees.create') }}", type: 'Action' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('customer_management')
    @can('lihat pelanggan')
    { label: 'Pelanggan & Piutang', keywords: ['pelanggan', 'customer', 'piutang', 'hutang', 'debt'], url: "{{ route('customer-debts.index') }}", type: 'Menu' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('table_management')
    @can('lihat meja')
    { label: 'Manajemen Meja', keywords: ['meja', 'table', 'nomor'], url: "{{ route('tables.index') }}", type: 'Menu' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('ai_insights')
    @can('lihat ai insights')
    { label: 'AI Insights', keywords: ['ai', 'insight', 'saran', 'analisis', 'cerdas'], url: "{{ route('ai-insights.index') }}", type: 'Menu' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('ai_insights')
    { label: 'Peta Cuan Lokasi', keywords: ['peta', 'cuan', 'lokasi', 'map', 'heatmap', 'peluang', 'opportunity'], url: "{{ route('opportunity-map.index') }}", type: 'Menu' },
    @endcanAccessFeature

    @canAccessFeature('clara_ai')
    @can('akses clara ai')
    { label: 'Clara AI Chat', keywords: ['clara', 'ai', 'chat', 'tanya', 'asisten', 'bot'], url: "{{ route('clara-ai.index') }}", type: 'Action' },
    { label: 'Video Prompt AI', keywords: ['video', 'prompt', 'ai', 'sinematik', 'runway', 'sora', 'pika'], url: "{{ route('clara-ai.video-prompt') }}", type: 'Menu' },
    { label: 'Script Generator AI', keywords: ['script', 'affiliate', 'tiktok', 'instagram', 'youtube', 'copywriting'], url: "{{ route('clara-ai.affiliate-script') }}", type: 'Menu' },
    { label: 'Image Prompt AI', keywords: ['image', 'gambar', 'iklan', 'ads', 'midjourney', 'dalle', 'sdxl'], url: "{{ route('clara-ai.ads-image-prompt') }}", type: 'Menu' },
    { label: 'Kalkulaba AI', keywords: ['kalkulaba', 'laba', 'untung', 'profit', 'hpp', 'cogs', 'pricing', 'resep', 'kalkulator'], url: "{{ route('clara-ai.kalkulaba') }}", type: 'Menu' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('outlet_policies')
    @can('lihat kebijakan outlet')
    { label: 'Kebijakan Outlet', keywords: ['kebijakan', 'policy', 'aturan', 'sop'], url: "{{ route('outlet-policies.index') }}", type: 'Menu' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('account_settings')
    @can('edit profil')
    { label: 'Pengaturan Akun', keywords: ['akun', 'profil', 'profile', 'password', 'sandi', 'setting'], url: "{{ route('profile.edit') }}", type: 'Menu' },
    @endcan
    @endcanAccessFeature

    @canAccessFeature('help_faq')
    @can('lihat faq')
    { label: 'Bantuan & FAQ', keywords: ['bantuan', 'faq', 'help', 'tanya'], url: "{{ route('faqs.index') }}", type: 'Menu' },
    @endcan
    @endcanAccessFeature

    { label: 'Notifikasi & Peringatan', keywords: ['notifikasi', 'pesan', 'info', 'alert', 'stock', 'peringatan', 'expired'], url: "{{ route('stock-notifications.index') }}", type: 'Menu' },

    @if(auth()->user()->hasRole('owner') && auth()->user()->subscription)
    { label: 'Kelola Langganan', keywords: ['langganan', 'subscribe', 'paket', 'premium', 'vip', 'billing', 'pembayaran'], url: "{{ route('subscription.manage') }}", type: 'Menu' },
    @endif
  ];

  // Focus shortcut
  document.addEventListener('keydown', function(e) {
    if (e.key === '/' && document.activeElement !== searchInput) {
      e.preventDefault();
      searchInput.focus();
    }
  });

  searchInput.addEventListener('input', function() {
    const query = this.value.trim().toLowerCase();
    
    // Toggle loader
    searchLoader.classList.remove('opacity-0');
    shortcutHint.classList.add('hidden');

    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
      performSearch(query);
      searchLoader.classList.add('opacity-0');
      if(!query) shortcutHint.classList.remove('hidden');
    }, 500);
  });
  
  // Hide on click outside
  document.addEventListener('click', function(e) {
    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
      searchResults.classList.add('hidden');
    }
  });
  
  searchInput.addEventListener('focus', function() {
    if (this.value.trim().length > 0) {
      searchResults.classList.remove('hidden');
    }
  });

  function performSearch(query) {
    if (!query) {
      searchResults.innerHTML = '';
      searchResults.classList.add('hidden');
      return;
    }

    // Filter
    const results = searchItems.filter(item => {
      return item.keywords.some(k => k.toLowerCase().includes(query)) || 
          item.label.toLowerCase().includes(query);
    });

    renderResults(results, query);
  }

  function renderResults(results, query) {
    searchResults.innerHTML = '';
    
    if (results.length === 0) {
      searchResults.innerHTML = `
        <div class="p-4 text-center text-gray-500 text-sm">
          <i class="fa-solid fa-magnifying-glass mb-2 text-gray-300 text-2xl block"></i>
          Tidak ada hasil untuk "${query}"
        </div>
      `;
      searchResults.classList.remove('hidden');
      return;
    }

    const ul = document.createElement('ul');
    ul.className = 'divide-y divide-gray-50';

    results.forEach(item => {
      const li = document.createElement('li');
      
      const icon = item.type === 'Action' 
        ? '<i class="fa-solid fa-plus-circle text-emerald-500 mr-3"></i>' 
        : '<i class="fa-solid fa-arrow-right text-gray-400 mr-3 group-hover:text-indigo-500 transition-colors"></i>';
      
      const badge = item.type === 'Action'
        ? '<span class="ml-auto text-[10px] font-bold tracking-wider text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full uppercase border border-emerald-100">Buat Baru</span>'
        : '<span class="ml-auto text-[10px] font-bold tracking-wider text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full uppercase border border-gray-200">Menu</span>';

      const regex = new RegExp(`(${query})`, 'gi');
      const highlightedLabel = item.label.replace(regex, '<span class="text-indigo-600 bg-indigo-50 font-bold">$1</span>');

      li.innerHTML = `
        <a href="${item.url}" class="flex items-center p-4 hover:bg-gray-50 transition-colors duration-150 group">
          ${icon}
          <span class="text-sm font-medium text-gray-700 group-hover:text-indigo-600">${highlightedLabel}</span>
          ${badge}
        </a>
      `;
      ul.appendChild(li);
    });

    searchResults.appendChild(ul);
    searchResults.classList.remove('hidden');
  }

  window.markStockAsRead = function(id) {
    fetch(`/stock-notifications/${id}/read`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const element = document.getElementById(`notification-${id}`);
        element.style.opacity = '0';
        element.style.transform = 'scale(0.95)';
        setTimeout(() => {
          element.remove();
          checkEmptyNotifications();
        }, 300);
      }
    });
  }

  window.markAllStockAsRead = function() {
    if (!confirm('Tandai semua pemberitahuan stok sebagai dibaca?')) return;

    fetch('/stock-notifications/read-all', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const container = document.getElementById('stockNotificationContainer');
        container.style.opacity = '0';
        container.style.transform = 'translateY(-10px)';
        setTimeout(() => {
          container.remove();
        }, 300);
      }
    });
  }

  function checkEmptyNotifications() {
    const container = document.getElementById('stockNotificationContainer');
    const items = container.querySelectorAll('.stock-item');
    if (items.length === 0) {
      container.style.opacity = '0';
      setTimeout(() => {
        container.remove();
      }, 300);
    } else {
      const badge = container.querySelector('span.bg-orange-100');
      if (badge) badge.textContent = items.length;
    }
  }
});
</script>
@push('scripts')
<script>
    // Sync localStorage to Cookie for Blade layout handling
    (function() {
        const storedLayout = localStorage.getItem('app_layout');
        const currentCookie = document.cookie.split('; ').find(row => row.startsWith('app_layout='))?.split('=')[1];
        
        if (storedLayout && storedLayout !== currentCookie) {
            document.cookie = 'app_layout=' + storedLayout + ';path=/;max-age=' + (60*60*24*365);
            window.location.reload();
        }
    })();
</script>
<script src="{{ asset('assets/js/menu-categories.js') }}"></script>
@endpush
<!DOCTYPE html>
<html lang="id">
@php
    // Global fallback for notifications if not passed from controller
    if (auth()->check()) {
        $stockNotificationService = app(\App\Services\StockNotificationService::class);
        $navStockNotifications = $navStockNotifications ?? $stockNotificationService->getLatestNotifications(auth()->user()->outlet_id, 5);
        $unreadStockCount = $unreadStockCount ?? $stockNotificationService->getUnreadCount(auth()->user()->outlet_id);
    } else {
        $navStockNotifications = collect();
        $unreadStockCount = 0;
    }
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CuanFlow')</title>
    
    <!-- Preload Critical Resources -->
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="preconnect" href="https://api.fontshare.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web@2.1.1"></script>

    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- favicon --}}
    <link rel="shortcut icon" href="{{ asset('assets/image/logo.svg') }}" type="image/x-icon">
    
    <!-- Satoshi Font - Optimized with font-display: swap -->
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@700,500,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    @php
        $activePalette = auth()->check() ? auth()->user()->getActivePalette() : \App\Models\ColorPalette::getDefault();

        // Generate a full Tailwind-like green scale from the palette's primary color.
        // Mixes the base hex with white (for lighter shades) or black (for darker shades).
        if (!function_exists('cuanMixHex')) {
            function cuanMixHex(string $hex, string $mixWith, float $weight): string {
                $hex     = ltrim($hex, '#');
                $mixWith = ltrim($mixWith, '#');
                $r1 = hexdec(substr($hex, 0, 2));  $g1 = hexdec(substr($hex, 2, 2));  $b1 = hexdec(substr($hex, 4, 2));
                $r2 = hexdec(substr($mixWith, 0, 2)); $g2 = hexdec(substr($mixWith, 2, 2)); $b2 = hexdec(substr($mixWith, 4, 2));
                $r = (int) round($r1 * (1 - $weight) + $r2 * $weight);
                $g = (int) round($g1 * (1 - $weight) + $g2 * $weight);
                $b = (int) round($b1 * (1 - $weight) + $b2 * $weight);
                return sprintf('#%02x%02x%02x', $r, $g, $b);
            }
        }

        $base = $activePalette->color_green;
        $greenScale = [
            50  => cuanMixHex($base, 'ffffff', 0.95),
            100 => cuanMixHex($base, 'ffffff', 0.87),
            200 => cuanMixHex($base, 'ffffff', 0.75),
            300 => cuanMixHex($base, 'ffffff', 0.60),
            400 => cuanMixHex($base, 'ffffff', 0.40),
            500 => $base,
            600 => cuanMixHex($base, '000000', 0.18),
            700 => cuanMixHex($base, '000000', 0.34),
            800 => cuanMixHex($base, '000000', 0.52),
            900 => cuanMixHex($base, '000000', 0.68),
            950 => cuanMixHex($base, '000000', 0.80),
        ];
    @endphp
    <script>
        // Active palette colors (server-side, initial render)
        window.__CUAN_PALETTE__ = {
            'cuan-yellow': '{{ $activePalette->color_yellow }}',
            'cuan-olive':  '{{ $activePalette->color_olive }}',
            'cuan-green':  '{{ $activePalette->color_green }}',
            'cuan-dark':   '{{ $activePalette->color_dark }}',
        };
        // Generated primary green scale (overrides Tailwind default green)
        window.__CUAN_GREEN_SCALE__ = {
            @foreach($greenScale as $shade => $hex)
            '{{ $shade }}': '{{ $hex }}',
            @endforeach
        };
        tailwind.config = {
            theme: {
                extend: {
                    colors: window.__CUAN_PALETTE__,
                },
                // Override Tailwind built-in green with user's palette primary
                colors: {
                    inherit: 'inherit', current: 'currentColor', transparent: 'transparent',
                    black: '#000', white: '#fff',
                    green: window.__CUAN_GREEN_SCALE__,
                    // Keep all standard Tailwind colors (partial re-declaration for slate/gray/etc)
                    slate:   { 50:'#f8fafc',100:'#f1f5f9',200:'#e2e8f0',300:'#cbd5e1',400:'#94a3b8',500:'#64748b',600:'#475569',700:'#334155',800:'#1e293b',900:'#0f172a',950:'#020617' },
                    gray:    { 50:'#f9fafb',100:'#f3f4f6',200:'#e5e7eb',300:'#d1d5db',400:'#9ca3af',500:'#6b7280',600:'#4b5563',700:'#374151',800:'#1f2937',900:'#111827',950:'#030712' },
                    zinc:    { 50:'#fafafa',100:'#f4f4f5',200:'#e4e4e7',300:'#d4d4d8',400:'#a1a1aa',500:'#71717a',600:'#52525b',700:'#3f3f46',800:'#27272a',900:'#18181b',950:'#09090b' },
                    neutral: { 50:'#fafafa',100:'#f5f5f5',200:'#e5e5e5',300:'#d4d4d4',400:'#a3a3a3',500:'#737373',600:'#525252',700:'#404040',800:'#262626',900:'#171717',950:'#0a0a0a' },
                    stone:   { 50:'#fafaf9',100:'#f5f5f4',200:'#e7e5e4',300:'#d6d3d1',400:'#a8a29e',500:'#78716c',600:'#57534e',700:'#44403c',800:'#292524',900:'#1c1917',950:'#0c0a09' },
                    red:     { 50:'#fef2f2',100:'#fee2e2',200:'#fecaca',300:'#fca5a5',400:'#f87171',500:'#ef4444',600:'#dc2626',700:'#b91c1c',800:'#991b1b',900:'#7f1d1d',950:'#450a0a' },
                    orange:  { 50:'#fff7ed',100:'#ffedd5',200:'#fed7aa',300:'#fdba74',400:'#fb923c',500:'#f97316',600:'#ea580c',700:'#c2410c',800:'#9a3412',900:'#7c2d12',950:'#431407' },
                    amber:   { 50:'#fffbeb',100:'#fef3c7',200:'#fde68a',300:'#fcd34d',400:'#fbbf24',500:'#f59e0b',600:'#d97706',700:'#b45309',800:'#92400e',900:'#78350f',950:'#451a03' },
                    yellow:  { 50:'#fefce8',100:'#fef9c3',200:'#fef08a',300:'#fde047',400:'#facc15',500:'#eab308',600:'#ca8a04',700:'#a16207',800:'#854d0e',900:'#713f12',950:'#422006' },
                    lime:    { 50:'#f7fee7',100:'#ecfccb',200:'#d9f99d',300:'#bef264',400:'#a3e635',500:'#84cc16',600:'#65a30d',700:'#4d7c0f',800:'#3f6212',900:'#365314',950:'#1a2e05' },
                    teal:    { 50:'#f0fdfa',100:'#ccfbf1',200:'#99f6e4',300:'#5eead4',400:'#2dd4bf',500:'#14b8a6',600:'#0d9488',700:'#0f766e',800:'#115e59',900:'#134e4a',950:'#042f2e' },
                    cyan:    { 50:'#ecfeff',100:'#cffafe',200:'#a5f3fc',300:'#67e8f9',400:'#22d3ee',500:'#06b6d4',600:'#0891b2',700:'#0e7490',800:'#155e75',900:'#164e63',950:'#083344' },
                    sky:     { 50:'#f0f9ff',100:'#e0f2fe',200:'#bae6fd',300:'#7dd3fc',400:'#38bdf8',500:'#0ea5e9',600:'#0284c7',700:'#0369a1',800:'#075985',900:'#0c4a6e',950:'#082f49' },
                    blue:    { 50:'#eff6ff',100:'#dbeafe',200:'#bfdbfe',300:'#93c5fd',400:'#60a5fa',500:'#3b82f6',600:'#2563eb',700:'#1d4ed8',800:'#1e40af',900:'#1e3a8a',950:'#172554' },
                    indigo:  { 50:'#eef2ff',100:'#e0e7ff',200:'#c7d2fe',300:'#a5b4fc',400:'#818cf8',500:'#6366f1',600:'#4f46e5',700:'#4338ca',800:'#3730a3',900:'#312e81',950:'#1e1b4b' },
                    violet:  { 50:'#f5f3ff',100:'#ede9fe',200:'#ddd6fe',300:'#c4b5fd',400:'#a78bfa',500:'#8b5cf6',600:'#7c3aed',700:'#6d28d9',800:'#5b21b6',900:'#4c1d95',950:'#2e1065' },
                    purple:  { 50:'#faf5ff',100:'#f3e8ff',200:'#e9d5ff',300:'#d8b4fe',400:'#c084fc',500:'#a855f7',600:'#9333ea',700:'#7e22ce',800:'#6b21a8',900:'#581c87',950:'#3b0764' },
                    fuchsia: { 50:'#fdf4ff',100:'#fae8ff',200:'#f5d0fe',300:'#f0abfc',400:'#e879f9',500:'#d946ef',600:'#c026d3',700:'#a21caf',800:'#86198f',900:'#701a75',950:'#4a044e' },
                    pink:    { 50:'#fdf2f8',100:'#fce7f3',200:'#fbcfe8',300:'#f9a8d4',400:'#f472b6',500:'#ec4899',600:'#db2777',700:'#be185d',800:'#9d174d',900:'#831843',950:'#500724' },
                    rose:    { 50:'#fff1f2',100:'#ffe4e6',200:'#fecdd3',300:'#fda4af',400:'#fb7185',500:'#f43f5e',600:'#e11d48',700:'#be123c',800:'#9f1239',900:'#881337',950:'#4c0519' },
                    emerald: { 50:'#ecfdf5',100:'#d1fae5',200:'#a7f3d0',300:'#6ee7b7',400:'#34d399',500:'#10b981',600:'#059669',700:'#047857',800:'#065f46',900:'#064e3b',950:'#022c22' },
                    // cuan-* custom colors (same as extend, but needed here since we're overriding colors entirely)
                    'cuan-yellow': '{{ $activePalette->color_yellow }}',
                    'cuan-olive':  '{{ $activePalette->color_olive }}',
                    'cuan-green':  '{{ $activePalette->color_green }}',
                    'cuan-dark':   '{{ $activePalette->color_dark }}',
                }
            }
        }
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
        body {
            font-family: 'Satoshi', sans-serif;
            background-color: #f9fafb;
        }

        /* ── Top Progress Bar (YouTube/GitHub style) ── */
        .global-page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            z-index: 99999;
            pointer-events: none;
            opacity: 0;
            background: transparent;
            transition: opacity 0.4s ease;
        }

        .global-page-loader .progress-bar {
            height: 100%;
            width: 0%;
            background: {{ $activePalette->color_green }};
            box-shadow: 0 0 10px {{ $activePalette->color_green }}80,
                        0 0 5px {{ $activePalette->color_green }}40;
            border-radius: 0 2px 2px 0;
            transform: translate3d(0, 0, 0);
            will-change: width, opacity;
            transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .global-page-loader .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(
                90deg,
                transparent 0%,
                rgba(255, 255, 255, 0.4) 50%,
                transparent 100%
            );
            animation: shimmer 1.5s ease-in-out infinite;
            transform: translate3d(-100%, 0, 0);
            will-change: transform;
        }

        .global-page-loader.active {
            opacity: 1;
        }

        @keyframes shimmer {
            0%   { transform: translate3d(-100%, 0, 0); }
            100% { transform: translate3d(200%, 0, 0); }
        }

        /* Content hidden until fully ready (Alpine + all scripts loaded) */
        #app-content-wrapper {
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        #app-content-wrapper.ready {
            opacity: 1;
        }

        /* Custom Scrollbar Styles */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 20px;
            transition: background 0.3s ease;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.15);
        }

        /* Firefox Support */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 0, 0, 0.05) transparent;
        }
    </style>
    
    @stack('styles')
</head>
<body class="antialiased bg-gray-50 relative overflow-x-hidden" 
      x-data="{ 
          get currentLayout() { return $store.app.layout },
          set currentLayout(val) { $store.app.layout = val },
          get sidebarCollapsed() { return $store.app.sidebarCollapsed },
          set sidebarCollapsed(val) { 
              $store.app.sidebarCollapsed = val;
              localStorage.setItem('sidebarCollapsed', val);
          },
          get isFullScreen() { return $store.app.isFullScreen },
          sidebarOpen: false
      }"
      @app-layout-changed.window="$store.app.setLayout($event.detail)"
      @toggle-fullscreen.window="$store.app.setFullScreen($event.detail)">
    @if(auth()->check() && auth()->user()->isOnTrial())
    <div class="fixed bottom-6 right-6 z-[9999] pointer-events-none opacity-40 select-none">
        <div class="bg-gray-900/10 backdrop-blur-sm border border-gray-900/20 px-4 py-2 rounded-full">
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-900/60 flex items-center gap-2">
                <i class="fa-solid fa-flask-vial animate-pulse text-emerald-600"></i>
                Dalam Uji Coba
            </span>
        </div>
    </div>
    @endif
    
    <!-- Top Progress Bar Loader (starts active to show immediate feedback) -->
    <div id="global-page-loader" class="global-page-loader active">
        <div class="progress-bar" id="progress-bar"></div>
    </div>
    <script>
        // Start the bar immediately so user sees progress while page loads
        (function() {
            var b = document.getElementById('progress-bar');
            if (b) {
                b.style.transition = 'width 2s cubic-bezier(0.4, 0, 0.2, 1)';
                b.style.width = '30%';
            }
        })();
    </script>

    <div id="app-content-wrapper" class="min-h-screen" :class="currentLayout === 'grid' ? 'flex flex-col' : 'block'">
        
        <!-- Sidebar Structure (For Sidebar layout mode) -->
        <template x-if="currentLayout === 'sidebar' && !isFullScreen">
            <div>
                <!-- Sidebar Overlay (Mobile) -->
                <div x-show="sidebarOpen" 
                    x-transition:enter="transition-opacity duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity duration-300"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    @click="sidebarOpen = false"
                    class="fixed inset-0 bg-gray-900/50 z-40 lg:hidden"
                    style="display: none;"></div>
                
                <!-- Aside Sidebar -->
                <aside class="fixed inset-y-0 left-0 z-50 bg-white border-r border-gray-100 transform lg:translate-x-0"
                    :class="{ 
                        'translate-x-0': sidebarOpen, 
                        '-translate-x-full': !sidebarOpen,
                        'w-64': !sidebarCollapsed,
                        'w-20': sidebarCollapsed 
                    }">
                    @include('layouts.sidebar-content')
                </aside>
            </div>
        </template>

        <div class="min-h-screen transition-all duration-300"
            :class="{ 
                'lg:ml-64': currentLayout === 'sidebar' && !sidebarCollapsed && !isFullScreen, 
                'lg:ml-20': currentLayout === 'sidebar' && sidebarCollapsed && !isFullScreen,
                'lg:ml-0': isFullScreen
            }">
            <!-- Navbar Grid -->
            <!-- Navbar Grid (For Grid mode) -->
            <nav id="main-navbar" x-cloak x-show="currentLayout === 'grid' && !isFullScreen" class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40" x-data="{ mobileOpen: false, notiOpen: false }">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16 items-center">
                        
                        <!-- Left: Logo / Outlet -->
                        <div class="flex items-center min-w-0 flex-1">
                            @if(auth()->check() && auth()->user()->outlet_id && auth()->user()->outlet)
                                @php
                                    $user = auth()->user();
                                    $hasMultiOutletFeature = app(\App\Services\FeatureAccessService::class)->checkAccess($user, 'multi_outlet')['can_access'];

                                    $userOutlets = $user->isOwner()
                                        ? $user->outletsOwned->where('is_active', true)->sortBy('name')
                                        : collect([$user->outlet])->filter(fn($o) => $o && $o->is_active);

                                    $hasMultipleOutlets = $userOutlets->count() > 1;
                                @endphp

                                <!-- Desktop outlet display -->
                                <div class="hidden sm:flex items-center">
                                    @if($hasMultipleOutlets)
                                        <!-- Multi Outlet Dropdown -->
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open"
                                                class="flex items-center space-x-3 hover:bg-gray-50 px-3 py-2 rounded-lg transition">
                                                @if(auth()->user()->outlet->logo)
                                                    <img src="{{ Storage::url(auth()->user()->outlet->logo) }}"
                                                        alt="{{ auth()->user()->outlet->name }}"
                                                        class="h-10 w-10 object-contain rounded-lg">
                                                @endif

                                                <div class="flex items-center space-x-2 min-w-0">
                                                    <span class="text-lg font-bold text-gray-900 truncate max-w-[220px]">
                                                        {{ auth()->user()->outlet->name }}
                                                    </span>
                                                    <i class="fa-solid fa-chevron-down text-xs text-gray-600"></i>
                                                </div>
                                            </button>

                                            <div x-show="open"
                                                @click.away="open = false"
                                                x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="transform opacity-100 scale-100"
                                                x-transition:leave-end="transform opacity-0 scale-95"
                                                class="absolute left-0 mt-2 w-72 bg-white rounded-lg shadow-lg py-2 border border-gray-200 z-50"
                                                style="display:none;">

                                                <div class="px-4 py-2 border-b border-gray-200">
                                                    <p class="text-xs text-gray-500 font-medium">Pilih Outlet</p>
                                                </div>

                                                @foreach($userOutlets as $outlet)
                                                    <form method="POST" action="{{ route('change.outlet') }}">
                                                        @csrf
                                                        <input type="hidden" name="outlet_id" value="{{ $outlet->id }}">

                                                        <button type="submit"
                                                            class="w-full text-left block px-4 py-3 hover:bg-cuan-yellow/20 transition
                                                            {{ auth()->user()->outlet_id == $outlet->id ? 'bg-cuan-yellow/30' : '' }}">
                                                            <div class="flex items-center space-x-3">
                                                                @if($outlet->logo)
                                                                    <img src="{{ Storage::url($outlet->logo) }}"
                                                                        alt="{{ $outlet->name }}"
                                                                        class="h-8 w-8 object-contain rounded">
                                                                @else
                                                                    <div
                                                                        class="h-8 w-8 rounded bg-gradient-to-br from-cuan-olive to-cuan-green flex items-center justify-center text-white font-semibold text-sm">
                                                                        {{ substr($outlet->name, 0, 1) }}
                                                                    </div>
                                                                @endif

                                                                <div class="flex-1 min-w-0">
                                                                    <p class="text-sm font-medium text-gray-900 truncate">
                                                                        {{ $outlet->name }}
                                                                    </p>
                                                                    <p class="text-xs text-gray-500 truncate">
                                                                        {{ $outlet->business_category }}
                                                                    </p>
                                                                </div>

                                                                @if(auth()->user()->outlet_id == $outlet->id)
                                                                    <i class="fa-solid fa-check text-cuan-green"></i>
                                                                @endif
                                                            </div>
                                                        </button>
                                                    </form>
                                                @endforeach

                                                @if($hasMultiOutletFeature)
                                                    <div class="px-2 py-1 mt-1 border-t border-gray-100">
                                                        <a href="{{ route('outlets.create') }}" 
                                                           class="flex items-center space-x-3 px-2 py-2 hover:bg-emerald-50 rounded-lg transition-colors group">
                                                            <div class="h-8 w-8 rounded bg-emerald-50 flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-all border border-emerald-100">
                                                                <i class="fa-solid fa-plus text-xs"></i>
                                                            </div>
                                                            <div class="flex-1 min-w-0">
                                                                <p class="text-sm font-bold text-emerald-600 truncate">Tambah Outlet</p>
                                                                <p class="text-[10px] text-gray-500 truncate">Buka cabang baru</p>
                                                            </div>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <!-- Single Outlet -->
                                        <a href="{{ route('dashboard') }}" class="nav-link flex items-center space-x-3">
                                            @if(auth()->user()->outlet->logo)
                                                <img src="{{ Storage::url(auth()->user()->outlet->logo) }}"
                                                    alt="{{ auth()->user()->outlet->name }}"
                                                    class="h-10 w-10 object-contain rounded-lg">
                                            @endif
                                            <span class="text-lg font-bold text-gray-900 truncate max-w-[240px]">
                                                {{ auth()->user()->outlet->name }}
                                            </span>
                                        </a>
                                    @endif
                                </div>

                                <!-- Mobile outlet display -->
                                <a href="{{ route('dashboard') }}" class="sm:hidden flex items-center space-x-2 min-w-0">
                                    @if(auth()->user()->outlet->logo)
                                        <img src="{{ Storage::url(auth()->user()->outlet->logo) }}"
                                            alt="{{ auth()->user()->outlet->name }}"
                                            class="h-9 w-9 object-contain rounded-lg">
                                    @endif
                                    <span class="text-base font-bold text-gray-900 truncate">
                                        {{ auth()->user()->outlet->name }}
                                    </span>
                                </a>

                            @else
                                <!-- No Outlet -->
                                <a href="{{ route('dashboard') }}" class="nav-link flex items-center space-x-3">
                                    <img src="{{ asset('assets/image/logo.svg') }}" alt="CuanFlow" class="h-10 w-auto">
                                    <span class="text-lg font-bold text-gray-900">CuanFlow</span>
                                </a>
                            @endif
                        </div>

                        <!-- Center: Notification (Desktop Grid) -->
                        <div class="hidden md:flex justify-center items-center flex-1">
                            <div class="relative" x-data="{ notiOpen: false }">
                                <button @click="notiOpen = !notiOpen" 
                                    class="p-2.5 text-gray-500 hover:text-cuan-dark hover:bg-cuan-green/5 rounded-xl transition-all duration-200 relative group active:scale-95">
                                    <i class="fa-regular fa-bell text-xl"></i>
                                    @if($unreadStockCount > 0)
                                    <span class="absolute top-2 right-2 flex h-2.5 w-2.5">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500 border-2 border-white"></span>
                                    </span>
                                    @endif
                                </button>

                                <!-- Dropdown Grid -->
                                <div x-show="notiOpen" 
                                    @click.away="notiOpen = false"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                    class="absolute left-1/2 -translate-x-1/2 mt-4 w-96 bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-gray-100 py-2 z-50 overflow-hidden ring-1 ring-black/5"
                                    style="display:none;">
                                    
                                    <div class="px-5 py-4 flex items-center justify-between border-b border-gray-50">
                                        <div>
                                            <h3 class="text-base font-bold text-gray-900">Notifikasi</h3>
                                            <p class="text-xs text-gray-500 font-medium mt-0.5">
                                                @if($unreadStockCount > 0)
                                                    Anda memiliki {{ $unreadStockCount }} peringatan stok
                                                @else
                                                    Tidak ada peringatan stok baru
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    <div class="max-h-[380px] overflow-y-auto custom-scrollbar p-2">
                                        @forelse($navStockNotifications as $noti)
                                        <a href="{{ route('stock-notifications.index') }}" class="block p-3 hover:bg-gray-50 rounded-2xl transition-all group mb-1 {{ $noti->is_read_by_me ? 'opacity-50' : '' }}">
                                            <div class="flex gap-4">
                                                <div class="h-10 w-10 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-all duration-300 shadow-sm border 
                                                    {{ in_array($noti->type, ['out_of_stock', 'expired']) ? 'bg-red-50 text-red-600 border-red-100' : 'bg-orange-50 text-orange-600 border-orange-100' }}">
                                                    <i class="fa-solid {{ in_array($noti->type, ['out_of_stock', 'expired']) ? 'fa-circle-xmark' : 'fa-triangle-exclamation' }} text-sm"></i>
                                                </div>
                                                <div class="flex-1 min-w-0 pt-0.5">
                                                    <div class="flex justify-between items-start mb-0.5">
                                                        <p class="text-sm font-bold text-gray-900 truncate">{{ $noti->title }}</p>
                                                        <span class="text-[10px] text-gray-400 font-medium">{{ $noti->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    <p class="text-xs text-gray-500 leading-relaxed line-clamp-1">
                                                        {{ $noti->message }}
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                        @empty
                                        <div class="py-12 text-center text-gray-400">
                                            <i class="fa-solid fa-check-circle text-2xl mb-2 opacity-20"></i>
                                            <p class="text-[11px]">Semua aman, Bos!</p>
                                        </div>
                                        @endforelse
                                    </div>

                                    <div class="p-3 border-t border-gray-50 bg-gray-50/50">
                                        <a href="{{ route('stock-notifications.index') }}" class="flex items-center justify-center w-full py-2.5 text-xs font-bold text-cuan-dark bg-white border border-gray-200 rounded-xl hover:bg-cuan-dark hover:text-white hover:border-cuan-dark transition-all duration-200 shadow-sm group">
                                            Laporan Stok Lengkap
                                            <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Desktop user dropdown -->
                        <div class="hidden sm:flex items-center space-x-4 flex-1 justify-end">
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center gap-2 hover:bg-gray-50 rounded-xl px-1 py-1 transition-all">
                                    <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-full object-cover ring-2 ring-emerald-50">
                                    <div class="hidden sm:block text-left mr-1">
                                        <span class="block text-xs font-bold text-gray-900 leading-none truncate max-w-[120px]">{{ auth()->user()->name }}</span>
                                        <span class="block text-[9px] font-bold text-emerald-600 uppercase tracking-wider mt-1">{{ auth()->user()->getRoleNames()->first() }}</span>
                                    </div>
                                    <i class="fas fa-chevron-down text-[10px] text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                                </button>

                                <div x-show="open" @click.away="open = false" 
                                     class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50 overflow-hidden"
                                     x-transition style="display:none;">
                                    <div class="px-4 py-3 border-b border-gray-100">
                                        <p class="text-xs font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                                        <p class="text-[10px] text-gray-500 truncate mt-0.5">{{ auth()->user()->email }}</p>
                                    </div>
                                    
                                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-xs text-gray-700 hover:bg-gray-50 transition-colors nav-link">
                                        <i class="fas fa-user-gear w-4 text-center text-gray-400"></i>
                                        <span>Pengaturan dan Akun</span>
                                    </a>

                                    <a href="{{ route('stock-notifications.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-xs text-gray-700 hover:bg-gray-50 transition-colors nav-link">
                                        <i class="fas fa-bell w-4 text-center text-gray-400"></i>
                                        <span>Notifikasi</span>
                                    </a>
                                    
                                    @hasrole('owner')
                                    <a href="{{ route('subscription.manage') }}" class="flex items-center gap-3 px-4 py-2.5 text-xs text-gray-700 hover:bg-gray-50 transition-colors nav-link">
                                        <i class="fas fa-crown w-4 text-center text-emerald-500"></i>
                                        <span>Langganan VIP</span>
                                    </a>
                                    @endhasrole

                                    <div class="border-t border-gray-100 mt-2 pt-2">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-xs text-red-600 hover:bg-red-50 transition-colors font-bold logout-btn">
                                                <i class="fas fa-sign-out-alt w-4 text-center"></i>
                                                <span>Keluar Aplikasi</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Mobile hamburger & Notification -->
                        <div class="sm:hidden flex items-center space-x-2">
                            <div class="relative" x-data="{ notiOpen: false }">
                                <button @click="notiOpen = !notiOpen" 
                                    class="p-2 text-gray-400 hover:text-gray-900 focus:outline-none relative active:bg-gray-100 rounded-lg transition-colors">
                                    <i class="fa-regular fa-bell text-xl"></i>
                                    @if($unreadStockCount > 0)
                                    <span class="absolute top-1.5 right-2 flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500 border-2 border-white"></span>
                                    </span>
                                    @endif
                                </button>

                                <!-- Dropdown Mobile -->
                                <div x-show="notiOpen" 
                                    @click.away="notiOpen = false"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95 origin-top-right"
                                    x-transition:enter-end="opacity-100 scale-100 origin-top-right"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 scale-100 origin-top-right"
                                    x-transition:leave-end="opacity-0 scale-95 origin-top-right"
                                    class="absolute right-0 mt-3 w-[85vw] max-w-[320px] bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50 overflow-hidden ring-1 ring-black/5"
                                    style="display:none;">
                                    
                                    <div class="px-4 py-3 flex items-center justify-between border-b border-gray-50">
                                        <h3 class="text-sm font-bold text-gray-900">Notifikasi ({{ $unreadStockCount }})</h3>
                                    </div>

                                    <div class="max-h-[350px] overflow-y-auto custom-scrollbar">
                                        @forelse($navStockNotifications as $noti)
                                        <a href="{{ route('stock-notifications.index') }}" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-50 group {{ $noti->is_read_by_me ? 'opacity-50' : '' }}">
                                            <div class="flex gap-3">
                                                <div class="h-9 w-9 rounded-xl flex items-center justify-center flex-shrink-0 border 
                                                    {{ in_array($noti->type, ['out_of_stock', 'expired']) ? 'bg-red-50 text-red-600 border-red-100' : 'bg-orange-50 text-orange-600 border-orange-100' }}">
                                                    <i class="fa-solid {{ in_array($noti->type, ['out_of_stock', 'expired']) ? 'fa-circle-xmark' : 'fa-triangle-exclamation' }} text-xs"></i>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="flex justify-between items-start">
                                                        <p class="text-xs font-bold text-gray-900 truncate">{{ $noti->title }}</p>
                                                        <span class="text-[10px] text-gray-400">{{ $noti->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    <p class="text-[11px] text-gray-500 mt-0.5 line-clamp-1">{{ $noti->message }}</p>
                                                </div>
                                            </div>
                                        </a>
                                        @empty
                                        <div class="py-8 text-center text-gray-400">
                                            <p class="text-[10px]">Semua aman, Bos!</p>
                                        </div>
                                        @endforelse
                                    </div>
                                    <div class="p-3 border-t border-gray-50 bg-gray-50/50">
                                        <a href="{{ route('stock-notifications.index') }}" class="block w-full text-center py-2 text-xs font-bold text-emerald-600 bg-white border border-gray-200 rounded-lg shadow-sm">
                                            Lihat Semua
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <button @click="mobileOpen = !mobileOpen"
                                class="inline-flex items-center justify-center p-2 rounded-lg hover:bg-gray-100 focus:outline-none"
                                aria-label="Open menu">
                                <svg class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                    <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" style="display:none;" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile panel Grid -->
                <div x-show="mobileOpen"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    class="sm:hidden border-t border-gray-200 bg-white"
                    style="display:none;"
                    @click.away="mobileOpen = false">

                    <div class="px-4 py-3 space-y-3">
                        <!-- Actions -->
                        <div class="pt-2 space-y-2">
                            <a href="{{ route('profile.edit') }}" class="nav-link flex items-center w-full px-3 py-2 rounded-lg hover:bg-emerald-50 text-gray-900">
                                <i class="fa-solid fa-gear mr-2 text-gray-400"></i>
                                Pengaturan dan Akun
                            </a>
                            <a href="{{ route('stock-notifications.index') }}" class="nav-link flex items-center w-full px-3 py-2 rounded-lg hover:bg-emerald-50 text-gray-900">
                                <i class="fa-solid fa-bell mr-2 text-gray-400"></i>
                                Notifikasi
                            </a>
                            @hasrole('owner')
                            <a href="{{ route('subscription.manage') }}" class="nav-link flex items-center w-full px-3 py-2 rounded-lg hover:bg-emerald-50 text-gray-900">
                                <i class="fa-solid fa-crown mr-2 text-emerald-500"></i>
                                Langganan VIP
                            </a>
                            @endhasrole
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="logout-btn flex items-center w-full px-3 py-2 rounded-lg hover:bg-red-50 text-red-600">
                                    <i class="fa-solid fa-right-from-bracket mr-2"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Sidebar Top Header (For Sidebar mode) -->
            <header x-cloak x-show="currentLayout === 'sidebar' && !isFullScreen" class="bg-white border-b border-gray-100 sticky top-0 z-40 h-16 flex items-center shadow-sm">
                <div class="flex items-center justify-between w-full px-4 lg:px-8">
                    <!-- Left: Toggle & Title -->
                    <div class="flex items-center gap-4">
                        <!-- Desktop Toggle -->
                        <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden lg:flex text-gray-500 hover:text-emerald-600 p-2 hover:bg-emerald-50 rounded-lg transition-colors">
                            <i class="fas" :class="sidebarCollapsed ? 'fa-indent' : 'fa-outdent'"></i>
                        </button>
                        <!-- Mobile Toggle -->
                        <button @click="sidebarOpen = true" class="lg:hidden text-gray-600 hover:text-gray-900">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h1 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] hidden sm:block">
                            @yield('title')
                        </h1>
                    </div>
                    
                    <!-- Right: Notifications & User -->
                    <div class="flex items-center gap-2 sm:gap-4">
                        <!-- Notifications Sidebar Top Header -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="p-2.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl relative transition-all">
                                <i class="fa-regular fa-bell text-lg"></i>
                                @if($unreadStockCount > 0)
                                    <span class="absolute top-2.5 right-2.5 flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500 border border-white"></span>
                                    </span>
                                @endif
                            </button>

                            <!-- Noti Dropdown Sidebar Header -->
                            <div x-show="open" @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                 class="absolute right-0 mt-3 w-96 bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-gray-100 py-2 z-50 overflow-hidden ring-1 ring-black/5"
                                 style="display:none;">
                                
                                <div class="px-5 py-4 flex items-center justify-between border-b border-gray-50">
                                    <div>
                                        <h3 class="text-base font-bold text-gray-900">Notifikasi</h3>
                                        <p class="text-xs text-gray-500 font-medium mt-0.5">
                                            @if($unreadStockCount > 0)
                                                Anda memiliki {{ $unreadStockCount }} peringatan stok
                                            @else
                                                Tidak ada peringatan stok baru
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="max-h-[380px] overflow-y-auto custom-scrollbar p-2">
                                    @forelse($navStockNotifications as $noti)
                                    <a href="{{ route('stock-notifications.index') }}" class="block p-3 hover:bg-gray-50 rounded-2xl transition-all group mb-1 {{ $noti->is_read_by_me ? 'opacity-50' : '' }}">
                                        <div class="flex gap-4">
                                            <div class="h-10 w-10 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-all duration-300 shadow-sm border 
                                                {{ in_array($noti->type, ['out_of_stock', 'expired']) ? 'bg-red-50 text-red-600 border-red-100' : 'bg-orange-50 text-orange-600 border-orange-100' }}">
                                                <i class="fa-solid {{ in_array($noti->type, ['out_of_stock', 'expired']) ? 'fa-circle-xmark' : 'fa-triangle-exclamation' }} text-sm"></i>
                                            </div>
                                            <div class="flex-1 min-w-0 pt-0.5">
                                                <div class="flex justify-between items-start mb-0.5">
                                                    <p class="text-sm font-bold text-gray-900 truncate">{{ $noti->title }}</p>
                                                    <span class="text-[10px] text-gray-400 font-medium">{{ $noti->created_at->diffForHumans() }}</span>
                                                </div>
                                                <p class="text-xs text-gray-500 leading-relaxed line-clamp-1">
                                                    {{ $noti->message }}
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                    @empty
                                    <div class="py-12 text-center text-gray-400">
                                        <i class="fa-solid fa-check-circle text-2xl mb-2 opacity-20"></i>
                                        <p class="text-[11px]">Semua aman, Bos!</p>
                                    </div>
                                    @endforelse
                                </div>

                                <div class="p-3 border-t border-gray-50 bg-gray-50/50">
                                    <a href="{{ route('stock-notifications.index') }}" class="flex items-center justify-center w-full py-2.5 text-xs font-bold text-cuan-dark bg-white border border-gray-200 rounded-xl hover:bg-cuan-dark hover:text-white hover:border-cuan-dark transition-all duration-200 shadow-sm group">
                                        Laporan Stok Lengkap
                                        <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- User Dropdown (Common UI in Grid & Sidebar) -->
                        <!-- The user requested it to be uniform, so I'll just keep the one I added in Chunk 2 -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 hover:bg-gray-50 rounded-xl px-1 py-1 transition-all">
                                <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-full object-cover ring-2 ring-emerald-50">
                                <div class="hidden sm:block text-left mr-1">
                                    <span class="block text-xs font-bold text-gray-900 leading-none truncate max-w-[120px]">{{ auth()->user()->name }}</span>
                                    <span class="block text-[9px] font-bold text-emerald-600 uppercase tracking-wider mt-1">{{ auth()->user()->getRoleNames()->first() }}</span>
                                </div>
                                <i class="fas fa-chevron-down text-[10px] text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <!-- Dropdown UI (Already handled in reactive way if we use Same Component, but for now I'll just hardcode it here for the Sidebar mode) -->
                            <div x-show="open" @click.away="open = false" 
                                 class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50 overflow-hidden"
                                 x-transition style="display:none;">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-xs font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-[10px] text-gray-500 truncate mt-0.5">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-xs text-gray-700 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-user-gear w-4 text-center"></i>
                                    <span>Pengaturan Akun</span>
                                </a>
                                <a href="{{ route('stock-notifications.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-xs text-gray-700 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-bell w-4 text-center"></i>
                                    <span>Notifikasi</span>
                                </a>
                                @hasrole('owner')
                                <a href="{{ route('subscription.manage') }}" class="flex items-center gap-3 px-4 py-2.5 text-xs text-gray-700 hover:bg-gray-100 transition-colors nav-link">
                                    <i class="fas fa-crown w-4 text-center text-emerald-500"></i>
                                    <span>Langganan VIP</span>
                                </a>
                                @endhasrole
                                <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100 mt-2">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-xs text-red-600 hover:bg-red-50 font-bold">
                                        <i class="fas fa-sign-out-alt w-4 text-center"></i>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Breadcrumbs -->
            @if(View::hasSection('breadcrumb'))
            <div x-show="!isFullScreen" class="bg-white border-b border-gray-100 shadow-sm">
                <div :class="currentLayout === 'sidebar' ? 'px-4 lg:px-8 py-3' : 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3'">
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-2 text-sm">
                            <li>
                                <a href="{{ route('dashboard') }}" class="nav-link text-gray-400 hover:text-emerald-600 flex items-center transition-colors">
                                    <i class="fa-solid fa-house text-xs"></i>
                                </a>
                            </li>
                            @yield('breadcrumb')
                        </ol>
                    </nav>
                </div>
            </div>
            @endif

            <!-- Main Page Content -->
            <main class="flex-grow">
                @yield('content')
            </main>
        </div>
    </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>


    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('app', {
                layout: localStorage.getItem('app_layout') || '{{ $_COOKIE["app_layout"] ?? "grid" }}',
                sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
                isFullScreen: false,
                setLayout(val) {
                    this.layout = val;
                    localStorage.setItem('app_layout', val);
                    document.cookie = 'app_layout=' + val + ';path=/;max-age=' + (60*60*24*365);
                },
                toggleSidebar() {
                    this.sidebarCollapsed = !this.sidebarCollapsed;
                    localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
                },
                setFullScreen(val) {
                    this.isFullScreen = val;
                }
            });
        });
    </script>
    <!-- Defer non-critical scripts -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // ── Top Progress Bar Handler (YouTube/GitHub style) ──
        (function() {
            const loader = document.getElementById('global-page-loader');
            const bar = document.getElementById('progress-bar');
            let isNavigating = false;
            let simulationTimer = null;

            // Start progress bar animation (simulates 0% → target%)
            function startProgress() {
                if (isNavigating) return;
                isNavigating = true;

                // Reset bar instantly
                bar.style.transition = 'none';
                bar.style.width = '0%';
                // Force reflow so the reset takes effect before animating
                void bar.offsetWidth;

                // Show bar and animate to ~85%
                loader.classList.add('active');
                bar.style.transition = 'width 2.5s cubic-bezier(0.4, 0, 0.2, 1)';
                bar.style.width = '85%';

                // After initial fast phase, slowly creep towards 95%
                simulationTimer = setTimeout(() => {
                    bar.style.transition = 'width 8s cubic-bezier(0.1, 0, 0.2, 1)';
                    bar.style.width = '95%';
                }, 2600);
            }

            // Complete progress bar (snap to 100%, then fade out)
            function completeProgress() {
                if (simulationTimer) clearTimeout(simulationTimer);

                bar.style.transition = 'width 0.3s ease-out';
                bar.style.width = '100%';

                setTimeout(() => {
                    loader.classList.remove('active');
                    // After fade-out, reset bar width for next navigation
                    setTimeout(() => {
                        bar.style.transition = 'none';
                        bar.style.width = '0%';
                        isNavigating = false;
                    }, 400);
                }, 350);
            }

            // Navigation helper – start bar, then navigate
            function navigate(url, e) {
                if (e) e.preventDefault();
                startProgress();
                // Navigate after a tiny delay so the bar is visible
                setTimeout(() => { window.location.href = url; }, 80);
            }

            // ── Event delegation ──
            document.addEventListener('click', function(e) {
                if (e.target.closest('.no-loader')) return;

                const link = e.target.closest('.nav-link');
                if (link) {
                    const url = link.getAttribute('href');
                    if (url && url !== '#' && !url.startsWith('javascript:')) {
                        navigate(url, e);
                    }
                }

                const logoutBtn = e.target.closest('.logout-btn');
                if (logoutBtn) {
                    e.preventDefault();
                    startProgress();
                    setTimeout(() => { logoutBtn.closest('form').submit(); }, 80);
                }
            });

            // Handle form submissions (outlet switching, etc.)
            document.addEventListener('submit', function(e) {
                const form = e.target;
                if (form.querySelector('input[name="outlet_id"]')) {
                    startProgress();
                }
            });

            // ── Page lifecycle ──
            window.addEventListener('load', function() {
                const contentWrapper = document.getElementById('app-content-wrapper');

                // Complete progress bar
                completeProgress();

                // Reveal content after Alpine.js is fully initialized
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        if (contentWrapper) contentWrapper.classList.add('ready');
                    });
                });
            });

            window.addEventListener('pageshow', function(e) {
                if (e.persisted) {
                    // Back/Forward cache: reset everything
                    if (simulationTimer) clearTimeout(simulationTimer);
                    loader.classList.remove('active');
                    bar.style.transition = 'none';
                    bar.style.width = '0%';
                    isNavigating = false;

                    const contentWrapper = document.getElementById('app-content-wrapper');
                    if (contentWrapper) contentWrapper.classList.add('ready');
                }
            });

            // On beforeunload, start the bar if not already running
            window.addEventListener('beforeunload', function() {
                if (!isNavigating) {
                    startProgress();
                }
            });
        })();
    </script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: @json(session('success')),
                    showConfirmButton: false,
                    timer: 3000,
                    iconColor: '#658C58',
                    customClass: {
                        popup: 'rounded-[2rem] border-none shadow-2xl',
                        title: 'font-black text-gray-900',
                        htmlContainer: 'text-sm font-medium text-gray-500'
                    }
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: @json(session('error')),
                    confirmButtonColor: '#ef4444',
                    customClass: {
                        popup: 'rounded-[2rem] border-none shadow-2xl',
                        title: 'font-black text-gray-900',
                        htmlContainer: 'text-sm font-medium text-gray-500'
                    }
                });
            @endif

            @if($errors->any())
                @php
                    $errorMsg = '';
                    foreach($errors->all() as $error) { $errorMsg .= '• ' . $error . '<br>'; }
                @endphp
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Input',
                    html: "{!! $errorMsg !!}",
                    confirmButtonColor: '#ef4444',
                    customClass: {
                        popup: 'rounded-[2rem] border-none shadow-2xl',
                        title: 'font-black text-gray-900',
                        htmlContainer: 'text-left text-sm font-medium text-gray-500'
                    }
                });
            @endif

            document.addEventListener('click', function(e) {
                const deleteBtn = e.target.closest('.confirm-delete');
                if (deleteBtn) {
                    e.preventDefault();
                    const form = deleteBtn.closest('form');
                    const name = deleteBtn.dataset.name || 'data ini';
                    Swal.fire({
                        title: 'Hapus Data?',
                        text: `Apakah Anda yakin ingin menghapus "${name}"?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#9ca3af',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal',
                        customClass: {
                            popup: 'rounded-[2rem] border-none shadow-2xl'
                        }
                    }).then((result) => { if (result.isConfirmed && form) form.submit(); });
                }

                const toggleBtn = e.target.closest('.confirm-toggle');
                if (toggleBtn) {
                    e.preventDefault();
                    const form = toggleBtn.closest('form');
                    const name = toggleBtn.dataset.name || 'ini';
                    const status = toggleBtn.dataset.status || 'ubah';
                    Swal.fire({
                        title: `${status.charAt(0).toUpperCase() + status.slice(1)}?`,
                        text: `Apakah Anda yakin ingin ${status} "${name}"?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#658C58',
                        cancelButtonColor: '#9ca3af',
                        confirmButtonText: 'Ya, Lanjutkan',
                        cancelButtonText: 'Batal',
                        customClass: {
                            popup: 'rounded-[2rem] border-none shadow-2xl'
                        }
                    }).then((result) => { if (result.isConfirmed && form) form.submit(); });
                }
            });
        });

        function markAllStockAsRead() {
            Swal.fire({
                title: 'Baca Semua?',
                text: 'Tandai semua pemberitahuan stok sebagai dibaca?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#658C58',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Ya, Tandai',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-[1.5rem] border-none shadow-xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/stock-notifications/read-all', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => { if (data.success) window.location.reload(); });
                }
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
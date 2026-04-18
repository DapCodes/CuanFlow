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
    <link rel="shortcut icon" href="{{ asset('assets/image/logo.svg') }}" type="image/x-icon">
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
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        ...window.__CUAN_PALETTE__,
                        primary: {
                            DEFAULT: '{{ $activePalette->color_green }}',
                            ...window.__CUAN_GREEN_SCALE__
                        },
                        green: window.__CUAN_GREEN_SCALE__,
                        emerald: window.__CUAN_GREEN_SCALE__,
                        lime: window.__CUAN_GREEN_SCALE__,
                    },
                },
            }
        }
    </script>
    
    <script>
        (function() {
            try {
                var theme = localStorage.getItem('theme_mode');
                if (theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (_) {}
        })();
    </script>

    <style>
        [x-cloak] { display: none !important; }
        body {
            font-family: 'Satoshi', sans-serif;
            background-color: #f9fafb;
        }

        /* Auto Dark Mode Global Overrides */
        html.dark body { background-color: #0f172a !important; color: #f8fafc !important; }
        html.dark .bg-white { background-color: #1e293b !important; border-color: #334155 !important; }
        html.dark .text-gray-900, html.dark .text-gray-800 { color: #f8fafc !important; }
        html.dark .text-gray-700, html.dark .text-gray-600 { color: #e2e8f0 !important; }
        html.dark .text-gray-500 { color: #94a3b8 !important; }
        html.dark .bg-gray-50, html.dark .bg-\[\#f9fafb\] { background-color: #0f172a !important; }
        html.dark .border-gray-200, html.dark .border-gray-100 { border-color: #334155 !important; }
        html.dark .border-b, html.dark .border-r, html.dark .border-l, html.dark .border-t, html.dark .border { border-color: #334155; }
        html.dark .bg-gray-100 { background-color: #334155 !important; }
        html.dark .hover\:bg-gray-50:hover { background-color: #334155 !important; }
        html.dark .hover\:bg-gray-100:hover { background-color: #475569 !important; }
        html.dark input, html.dark textarea, html.dark select { background-color: #0f172a !important; color: #f8fafc !important; border-color: #334155 !important; }
        html.dark .shadow-sm { box-shadow: none !important; }

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

        /* Fast Access Drawer Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.1);
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
            <nav id="main-navbar" x-cloak x-show="currentLayout === 'grid' && !isFullScreen" class="bg-white border-b border-gray-200 sticky top-0 z-40" x-data="{ mobileOpen: false, notiOpen: false }">
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
                                <div class="sm:hidden relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="flex items-center space-x-2 min-w-0 active:bg-gray-50 px-2 py-1 rounded-lg transition-colors">
                                        @if(auth()->user()->outlet->logo)
                                            <img src="{{ Storage::url(auth()->user()->outlet->logo) }}"
                                                alt="{{ auth()->user()->outlet->name }}"
                                                class="h-9 w-9 object-contain rounded-lg">
                                        @endif
                                        <div class="flex items-center space-x-1.5 min-w-0">
                                            <span class="text-base font-bold text-gray-900 truncate max-w-[150px]">
                                                {{ auth()->user()->outlet->name }}
                                            </span>
                                            @if($hasMultipleOutlets)
                                                <i class="fa-solid fa-chevron-down text-[10px] text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                                            @endif
                                        </div>
                                    </button>

                                    @if($hasMultipleOutlets)
                                        <div x-show="open"
                                            @click.away="open = false"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="fixed md:absolute inset-x-4 md:inset-x-auto md:left-0 top-[70px] md:top-auto md:mt-2 w-auto md:w-72 bg-white rounded-2xl shadow-xl py-2 border border-gray-100 z-50 ring-1 ring-black/5"
                                            style="display:none;">

                                            <div class="px-4 py-2 border-b border-gray-50">
                                                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Pilih Outlet</p>
                                            </div>

                                            <div class="max-h-[300px] overflow-y-auto custom-scrollbar">
                                                @foreach($userOutlets as $outlet)
                                                    <form method="POST" action="{{ route('change.outlet') }}">
                                                        @csrf
                                                        <input type="hidden" name="outlet_id" value="{{ $outlet->id }}">

                                                        <button type="submit"
                                                            class="w-full text-left block px-4 py-3 active:bg-cuan-yellow/10 transition
                                                            {{ auth()->user()->outlet_id == $outlet->id ? 'bg-cuan-yellow/5' : '' }}">
                                                            <div class="flex items-center space-x-3">
                                                                @if($outlet->logo)
                                                                    <img src="{{ Storage::url($outlet->logo) }}"
                                                                        alt="{{ $outlet->name }}"
                                                                        class="h-8 w-8 object-contain rounded-lg">
                                                                @else
                                                                    <div class="h-8 w-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 font-bold text-xs border border-gray-200">
                                                                        {{ substr($outlet->name, 0, 1) }}
                                                                    </div>
                                                                @endif

                                                                <div class="flex-1 min-w-0">
                                                                    <p class="text-sm font-bold text-gray-900 truncate">
                                                                        {{ $outlet->name }}
                                                                    </p>
                                                                    <p class="text-[10px] text-gray-500 truncate">
                                                                        {{ $outlet->business_category }}
                                                                    </p>
                                                                </div>

                                                                @if(auth()->user()->outlet_id == $outlet->id)
                                                                    <i class="fa-solid fa-circle-check text-cuan-green text-sm"></i>
                                                                @endif
                                                            </div>
                                                        </button>
                                                    </form>
                                                @endforeach
                                            </div>

                                            @if($hasMultiOutletFeature)
                                                <div class="px-2 py-2 mt-1 border-t border-gray-50 bg-gray-50/50">
                                                    <a href="{{ route('outlets.create') }}" 
                                                       class="flex items-center space-x-3 px-3 py-2.5 bg-white border border-gray-100 rounded-xl hover:bg-emerald-50 transition-all group shadow-sm">
                                                        <div class="h-8 w-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 border border-emerald-100 group-hover:bg-emerald-600 group-hover:text-white transition-all">
                                                            <i class="fa-solid fa-plus text-xs"></i>
                                                        </div>
                                                        <div class="flex-1 min-w-0 text-left">
                                                            <p class="text-[11px] font-black uppercase tracking-wider text-emerald-600">Tambah Outlet</p>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>

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
                                        <a href="{{ route('stock-notifications.index') }}" class="block p-3 hover:bg-gray-50 rounded-2xl transition-all group mb-1">
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
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="fixed md:absolute top-[70px] md:top-auto left-4 right-4 md:left-auto md:right-0 md:mt-3 w-auto md:w-96 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50 overflow-hidden ring-1 ring-black/5"
                                    style="display:none;">
                                    
                                    <div class="px-4 py-3 flex items-center justify-between border-b border-gray-50">
                                        <h3 class="text-sm font-bold text-gray-900">Notifikasi ({{ $unreadStockCount }})</h3>
                                    </div>

                                    <div class="max-h-[350px] overflow-y-auto custom-scrollbar">
                                        @forelse($navStockNotifications as $noti)
                                        <a href="{{ route('stock-notifications.index') }}" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-50 group">
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
                                 class="fixed md:absolute top-[70px] md:top-auto left-4 right-4 md:left-auto md:right-0 md:mt-3 w-auto md:w-96 bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-gray-100 py-2 z-50 overflow-hidden ring-1 ring-black/5"
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
                                    <a href="{{ route('stock-notifications.index') }}" class="block p-3 hover:bg-gray-50 rounded-2xl transition-all group mb-1">
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

            <!-- Breadcrumbs & Fast Access -->
            @php
                $user = auth()->user();
                $currentRoute = Route::currentRouteName();
                $currentCategoryItem = \App\Models\FeatureCategoryItem::where('route_name', $currentRoute)->first();
                $currentCategory = $currentCategoryItem ? $currentCategoryItem->category : null;
                
                // Calculate Context for Special Conditions
                $isPosOpen = $user->outlet_id 
                    ? \App\Models\CashRegister::where('outlet_id', $user->outlet_id)->where('user_id', $user->id)->where('status', 'open')->exists() 
                    : false;
                $isReseller = $user->email 
                    ? \App\Models\Customer::where('email', $user->email)->where('type', 'reseller')->exists() 
                    : false;

                $relatedFeatures = collect();
                if ($currentCategory) {
                    $relatedFeatures = $currentCategory->featureItems()->active()->get()->filter(function($item) use ($user, $isPosOpen, $isReseller) {
                        // 1. Subscription & Feature Access Check
                        if ($item->feature_key && !$user->canAccessFeature($item->feature_key)) {
                            return false;
                        }

                        // 2. Permission Check
                        if ($item->permission_key && !$user->hasAnyPermission((array)$item->permission_key)) {
                            return false;
                        }

                        // 3. Special Conditions
                        if ($item->special_condition) {
                            if ($item->special_condition === 'isReseller' && !$isReseller) return false;
                            if ($item->special_condition === 'isPosOpen' && !$isPosOpen) return false;
                            if ($item->special_condition === 'hasSubscription' && !($user->hasRole('admin') || $user->hasActiveSubscription())) return false;
                            if ($item->special_condition === 'outletInfo' && !$user->outlet_id) return false;
                        }

                        return true;
                    });
                }
            @endphp

            @if(View::hasSection('breadcrumb'))
            <div x-show="!isFullScreen" class="bg-white border-b border-gray-100 shadow-sm sticky top-[64px] z-30" x-data="{ fastAccessOpen: false }">
                <div :class="currentLayout === 'sidebar' ? 'px-4 lg:px-8 py-3' : 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3'">
                    <div class="flex items-center justify-between h-8">
                        <!-- Left side: Breadcrumb / Page Title -->
                        <div class="flex items-center min-w-0 flex-1 h-full">
                            @if($currentCategory)
                                <div class="flex items-center gap-2.5 h-full">
                                    <div class="hidden sm:flex items-center gap-2.5 group cursor-default">
                                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider group-hover:text-primary-500 transition-colors">{{ $currentCategory->name }}</span>
                                        <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i>
                                    </div>
                                    <span class="text-xs sm:text-sm font-black text-gray-900 truncate">{{ $currentCategoryItem->label }}</span>
                                </div>
                            @else
                                <nav class="flex h-full items-center" aria-label="Breadcrumb">
                                    <ol class="flex items-center space-x-2 text-sm">
                                        <li>
                                            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-primary-600 transition-colors">
                                                <i class="fa-solid fa-house text-xs"></i>
                                            </a>
                                        </li>
                                        @yield('breadcrumb')
                                    </ol>
                                </nav>
                            @endif
                        </div>

                        <!-- Right side: Fast Access Trigger -->
                        @if($currentCategory)
                        <div class="flex items-center h-full ml-4">
                            <button @click="fastAccessOpen = true" 
                                    class="inline-flex items-center justify-center gap-2 px-3 py-1.5 sm:px-4 sm:py-2 bg-primary-500 text-white rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-primary-600 transition-all active:scale-95 shadow-sm shadow-primary-200 group">
                                <i class="fa-solid fa-chevron-left text-[10px] group-hover:-translate-x-0.5 transition-transform"></i>
                                <span class="hidden sm:inline">Akses Cepat</span>
                            </button>
                        </div>
                        @else
                            <div class="h-8"></div>
                        @endif
                    </div>
                </div>

                <!-- Fast Access Side Menu (Drawer) -->
                @if($currentCategory)
                <template x-teleport="body">
                    <div x-show="fastAccessOpen" class="fixed inset-0 z-[100]" style="display: none;">
                        <!-- Backdrop -->
                        <div x-show="fastAccessOpen" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             @click="fastAccessOpen = false"
                             class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm"></div>
                        
                        <!-- Panel -->
                        <div x-show="fastAccessOpen"
                             x-transition:enter="transition ease-out duration-300 transform"
                             x-transition:enter-start="translate-x-full"
                             x-transition:enter-end="translate-x-0"
                             x-transition:leave="transition ease-in duration-200 transform"
                             x-transition:leave-start="translate-x-0"
                             x-transition:leave-end="translate-x-full"
                             class="absolute right-0 inset-y-0 w-full max-w-xs sm:max-w-sm bg-white shadow-2xl flex flex-col border-l border-gray-100">
                            
                            <!-- Header -->
                            <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-white dark:bg-slate-900 sticky top-0 z-10">
                                <div>
                                    <p class="text-[10px] text-primary-500 font-black uppercase tracking-[0.2em] mb-1">Pilihan Menu</p>
                                    <h3 class="text-lg font-black text-gray-900 leading-tight">{{ $currentCategory->name }}</h3>
                                </div>
                                <button @click="fastAccessOpen = false" class="h-10 w-10 flex items-center justify-center bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-500 rounded-full transition-all active:scale-90 border border-gray-100">
                                    <i class="fa-solid fa-xmark text-lg"></i>
                                </button>
                            </div>

                            <!-- List -->
                            <div class="flex-1 overflow-y-auto custom-scrollbar p-6 space-y-3">
                                @foreach($relatedFeatures as $item)
                                    @php
                                        $isActive = Route::is($item->route_name);
                                        $url = $item->resolveUrl();
                                    @endphp
                                    <a href="{{ $url }}" 
                                       class="flex items-center gap-4 p-4 rounded-xl border transition-all group relative overflow-hidden
                                              {{ $isActive 
                                                 ? 'bg-primary-50 border-primary-200 ring-2 ring-primary-500/10' 
                                                 : 'bg-white border-gray-100 hover:border-primary-200 hover:bg-primary-50/30' }}">
                                        
                                        <div class="h-10 w-10 rounded-lg flex items-center justify-center transition-all duration-300 flex-shrink-0
                                                    {{ $isActive ? 'bg-primary-600 text-white shadow-lg shadow-primary-200' : 'bg-gray-50 text-gray-400 group-hover:bg-primary-100 group-hover:text-primary-600 border border-gray-100 group-hover:border-primary-200' }}">
                                            <i class="{{ $item->icon_class }} text-lg"></i>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-black truncate transition-colors {{ $isActive ? 'text-primary-700' : 'text-gray-900 group-hover:text-primary-700' }}">
                                                {{ $item->label }}
                                            </p>
                                            <p class="text-[11px] text-gray-500 line-clamp-1 mt-0.5">{{ $item->description }}</p>
                                        </div>

                                        @if($isActive)
                                            <div class="h-2 w-2 rounded-full bg-primary-600"></div>
                                        @else
                                            <i class="fa-solid fa-chevron-right text-[10px] text-gray-300 group-hover:text-primary-400 group-hover:translate-x-1 transition-all"></i>
                                        @endif
                                    </a>
                                @endforeach
                            </div>

                            <!-- Footer -->
                            <div class="p-6 border-t border-gray-50 bg-gray-50/30">
                                <a href="{{ route('dashboard') }}" class="flex items-center justify-center gap-2 w-full py-3 px-4 bg-white border border-gray-200 text-gray-600 rounded-xl text-sm font-bold hover:bg-gray-50 transition-all shadow-sm group">
                                    <i class="fa-solid fa-house text-xs group-hover:-translate-y-0.5 transition-transform"></i>
                                    Beranda Utama
                                </a>
                            </div>
                        </div>
                    </div>
                </template>
                @endif
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
            document.addEventListener('DOMContentLoaded', function() {
                const contentWrapper = document.getElementById('app-content-wrapper');

                // Complete progress bar (smoothly)
                setTimeout(() => {
                    completeProgress();
                }, 200);

                // Reveal content as soon as DOM is ready
                requestAnimationFrame(() => {
                    if (contentWrapper) contentWrapper.classList.add('ready');
                });
            });

            // Fallback: ensure shown anyway on full load
            window.addEventListener('load', function() {
                const contentWrapper = document.getElementById('app-content-wrapper');
                completeProgress();
                if (contentWrapper) contentWrapper.classList.add('ready');
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
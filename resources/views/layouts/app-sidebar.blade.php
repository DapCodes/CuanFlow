<!DOCTYPE html>
<html lang="id">
@php
    // Global fallback for notifications if not passed from controller
    if (auth()->check()) {
        $stockNotificationService = app(\App\Services\StockNotificationService::class);
        $navStockNotifications = $navStockNotifications ?? $stockNotificationService->getLatestNotifications(auth()->user()->outlet_id, 5);
        $unreadStockCount = $unreadStockCount ?? $navStockNotifications->where('is_read_by_me', false)->count();
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
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- favicon --}}
    <link rel="shortcut icon" href="{{ asset('assets/image/logo.svg') }}" type="image/x-icon">
    
    <!-- Satoshi Font - Optimized with font-display: swap -->
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@700,500,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'cuan-yellow': '#F0E491',
                        'cuan-olive': '#BBC863',
                        'cuan-green': '#658C58',
                        'cuan-dark': '#31694E',
                    }
                }
            }
        }
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
        body {
            font-family: 'Satoshi', sans-serif;
        }

        /* Optimized Global Page Loader - Using transform and will-change for GPU acceleration */
        .global-page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, visibility 0.2s ease;
            will-change: opacity, visibility;
        }
        
        .global-page-loader.active {
            opacity: 1;
            visibility: visible;
        }
        
        /* Optimized Spinning Asterisk - GPU Accelerated */
        .global-loader-asterisk {
            width: 60px;
            height: 60px;
            animation: spin 1s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite;
            will-change: transform;
        }
        
        @keyframes spin {
            0% {
                transform: rotate(0deg) scale(1);
            }
            50% {
                transform: rotate(180deg) scale(1.15);
            }
            100% {
                transform: rotate(360deg) scale(1);
            }
        }
        
        /* Optimized Pulsing dots */
        .global-loader-dots {
            display: flex;
            gap: 6px;
            margin-top: 16px;
        }
        
        .global-loader-dot {
            width: 6px;
            height: 6px;
            background: #31694E;
            border-radius: 50%;
            animation: pulse 1.2s ease-in-out infinite;
            will-change: transform, opacity;
        }
        
        .global-loader-dot:nth-child(2) {
            animation-delay: 0.15s;
        }
        
        .global-loader-dot:nth-child(3) {
            animation-delay: 0.3s;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(0.8);
                opacity: 0.5;
            }
            50% {
                transform: scale(1.2);
                opacity: 1;
            }
        }
        
        /* Loading text */
        .global-loader-text {
            color: #31694E;
            font-size: 16px;
            font-weight: 600;
            margin-top: 12px;
            animation: fadeInOut 1.5s ease-in-out infinite;
        }
        
        @keyframes fadeInOut {
            0%, 100% {
                opacity: 0.5;
            }
            50% {
                opacity: 1;
            }
        }

        /* FOUC Prevention */
        #app-content-wrapper {
            opacity: 0;
            transition: opacity 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #app-content-wrapper.ready {
            opacity: 1;
        }
    </style>
    
    <style id="fouc-mask">
        #app-content-wrapper { display: none !important; }
        body { background-color: #ffffff !important; overflow: hidden !important; }
    </style>
    
    @stack('styles')
</head>
<body class="antialiased bg-gray-50 relative overflow-x-hidden" 
      x-data="{ 
          get currentLayout() { return $store.app.layout },
          set currentLayout(val) { $store.app.layout = val },
          get sidebarCollapsed() { return $store.app.sidebarCollapsed },
          set sidebarCollapsed(val) { $store.app.sidebarCollapsed = val },
          sidebarOpen: false
      }"
      @app-layout-changed.window="$store.app.setLayout($event.detail)">
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
    
    <!-- Optimized Global Page Loader -->
    <div id="global-page-loader" class="global-page-loader active">
        <svg class="global-loader-asterisk" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M40 10V70M10 40H70M20 20L60 60M60 20L20 60" stroke="#31694E" stroke-width="8" stroke-linecap="round"/>
        </svg>
        <div class="global-loader-dots">
            <div class="global-loader-dot"></div>
            <div class="global-loader-dot"></div>
            <div class="global-loader-dot"></div>
        </div>
        <p class="global-loader-text">Loading...</p>
    </div>

    <div id="app-content-wrapper" class="min-h-screen flex" :class="currentLayout === 'grid' ? 'flex-col' : ''">
        
        <!-- Sidebar Structure (For Sidebar layout mode) -->
        <template x-if="currentLayout === 'sidebar'">
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

        <div class="flex-1 flex flex-col min-h-screen"
            :class="{ 
                'lg:ml-64': currentLayout === 'sidebar' && !sidebarCollapsed, 
                'lg:ml-20': currentLayout === 'sidebar' && sidebarCollapsed 
            }">
            <!-- Navbar Grid -->
            <!-- Navbar Grid (For Grid mode) -->
            <nav id="main-navbar" x-show="currentLayout === 'grid'" class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40" x-data="{ mobileOpen: false, notiOpen: false }">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16 items-center">
                        
                        <!-- Left: Logo / Outlet -->
                        <div class="flex items-center min-w-0 flex-1">
                            @if(auth()->check() && auth()->user()->outlet_id && auth()->user()->outlet)
                                @php
                                    $userOutlets = auth()->user()->isOwner()
                                        ? auth()->user()->outletsOwned->where('is_active', true)->sortBy('name')
                                        : collect([auth()->user()->outlet])->filter(fn($o) => $o && $o->is_active);

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
            <header x-show="currentLayout === 'sidebar'" class="bg-white border-b border-gray-100 sticky top-0 z-40 h-16 flex items-center shadow-sm">
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
            <div class="bg-white border-b border-gray-100 shadow-sm">
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
                setLayout(val) {
                    this.layout = val;
                    localStorage.setItem('app_layout', val);
                    document.cookie = 'app_layout=' + val + ';path=/;max-age=' + (60*60*24*365);
                },
                toggleSidebar() {
                    this.sidebarCollapsed = !this.sidebarCollapsed;
                    localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
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
        // Optimized Page Loader Handler - Runs immediately
        (function() {
            const loader = document.getElementById('global-page-loader');
            let isNavigating = false;
            
            // Optimized navigation function - minimal delay
            function navigate(url, e) {
                if (e) e.preventDefault();
                if (isNavigating) return;
                
                isNavigating = true;
                loader.classList.add('active');
                
                // Navigate immediately after brief visual feedback (300ms)
                setTimeout(() => {
                    window.location.href = url;
                }, 300);
            }
            
            // Use event delegation for better performance
            document.addEventListener('click', function(e) {
                // Skip if clicked element or parent has no-loader class
                if (e.target.closest('.no-loader')) {
                    return;
                }
                
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
                    if (isNavigating) return;
                    
                    isNavigating = true;
                    loader.classList.add('active');
                    setTimeout(() => {
                        logoutBtn.closest('form').submit();
                    }, 300);
                }
            });
            
            // Handle form submissions for outlet switching
            document.addEventListener('submit', function(e) {
                const form = e.target;
                if (form.querySelector('input[name="outlet_id"]')) {
                    if (isNavigating) return;
                    
                    isNavigating = true;
                    loader.classList.add('active');
                }
            });
            
            // Handle back/forward navigation and initial load
            window.addEventListener('load', function() {
                const foucMask = document.getElementById('fouc-mask');
                const contentWrapper = document.getElementById('app-content-wrapper');
                
                // 1. Remove display:none constraint
                if (foucMask) foucMask.remove();
                
                // 2. Small delay to ensure styles are applied
                setTimeout(() => {
                    // 3. Hide Loader
                    loader.classList.remove('active');
                    
                    // 4. Fade In Content
                    if (contentWrapper) contentWrapper.classList.add('ready');
                    
                    // 5. Restore scroll
                    document.body.style.overflow = '';
                    isNavigating = false;
                }, 400); 
            });

            window.addEventListener('pageshow', function(e) {
                if (e.persisted) {
                    loader.classList.remove('active');
                    const contentWrapper = document.getElementById('app-content-wrapper');
                    if (contentWrapper) contentWrapper.classList.add('ready');
                    document.body.style.overflow = '';
                    isNavigating = false;
                }
            });
            
            // Prevent loader from staying visible if navigation is cancelled
            window.addEventListener('beforeunload', function() {
                if (!isNavigating) {
                    loader.classList.add('active');
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
                    text: "{{ session('success') }}",
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
                    text: "{{ session('error') }}",
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
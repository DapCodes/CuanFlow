<!DOCTYPE html>
<html lang="id">
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
<body class="antialiased bg-gray-50">
    
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

    <div id="app-content-wrapper" class="min-h-screen flex flex-col">
        <!-- Navbar -->
<nav id="main-navbar" class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40" x-data="{ mobileOpen: false, notiOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            
            <!-- Left: Logo / Outlet -->
            <div class="flex items-center min-w-0 flex-1">
                @if(auth()->check() && auth()->user()->outlet_id && auth()->user()->outlet)
                    @php
                        $userOutlets = auth()->user()->isOwner()
                            ? auth()->user()->outletsOwned->sortBy('name')
                            : collect([auth()->user()->outlet]);

                        $hasMultipleOutlets = $userOutlets->count() > 1;
                    @endphp

                    <!-- Desktop outlet display (same as before, but truncated nicely) -->
                    <div class="hidden sm:flex items-center">
                        @if($hasMultipleOutlets)
                            <!-- Multi Outlet Dropdown (Desktop) -->
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
                                    class="absolute left-0 mt-2 w-72 bg-white rounded-lg shadow-lg py-2 border border-gray-200"
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
                            <!-- Single Outlet (Desktop) -->
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

                    <!-- Mobile outlet display (compact) -->
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

            <!-- Center: Notification (Desktop) -->
            <div class="hidden md:flex justify-center items-center flex-1">
                <div class="relative">
                    <button @click="notiOpen = !notiOpen" 
                        class="p-2 text-gray-400 hover:text-cuan-dark transition-colors relative group">
                        <i class="fa-solid fa-bell text-xl"></i>
                        <span class="absolute top-1.5 right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white border-2 border-white">
                            3
                        </span>
                    </button>

                    <!-- Dropdown Desktop -->
                    <div x-show="notiOpen" 
                        @click.away="notiOpen = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        class="absolute left-1/2 -translate-x-1/2 mt-3 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50 overflow-hidden"
                        style="display:none;">
                        
                        <div class="px-4 py-3 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                            <h3 class="text-sm font-bold text-gray-900 leading-none">Notifikasi</h3>
                            <span class="text-[10px] font-bold bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full uppercase tracking-wider">Dummy</span>
                        </div>

                        <div class="max-h-[350px] overflow-y-auto custom-scrollbar">
                            <!-- Dummy Item 1 -->
                            <a href="#" class="block px-4 py-3.5 hover:bg-gray-50 transition-colors border-b border-gray-50 group">
                                <div class="flex gap-3">
                                    <div class="h-9 w-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-receipt text-sm"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-gray-900 mb-0.5">Pesanan Baru: #INV-2656</p>
                                        <p class="text-[11px] text-gray-500 line-clamp-2">Ada pesanan baru dari Pelanggan Umum yang perlu diproses segera.</p>
                                        <p class="text-[10px] text-blue-500 font-medium mt-1">2 menit yang lalu</p>
                                    </div>
                                </div>
                            </a>

                            <!-- Dummy Item 2 -->
                            <a href="#" class="block px-4 py-3.5 hover:bg-gray-50 transition-colors border-b border-gray-50 group">
                                <div class="flex gap-3">
                                    <div class="h-9 w-9 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-exclamation-triangle text-sm"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-gray-900 mb-0.5">Stok Menipis: Telur Ayam</p>
                                        <p class="text-[11px] text-gray-500 line-clamp-2">Persediaan stok barang Telur Ayam sudah mencapai batas minimum (0.5 kg).</p>
                                        <p class="text-[10px] text-blue-500 font-medium mt-1">1 jam yang lalu</p>
                                    </div>
                                </div>
                            </a>

                            <!-- Dummy Item 3 -->
                            <a href="#" class="block px-4 py-3.5 hover:bg-gray-50 transition-colors group">
                                <div class="flex gap-3">
                                    <div class="h-9 w-9 rounded-xl bg-green-50 text-green-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-check-circle text-sm"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-gray-900 mb-0.5">Laporan Harian Siap</p>
                                        <p class="text-[11px] text-gray-500 line-clamp-2">Laporan transaksi untuk tanggal 11 Feb sudah selesai digenerate.</p>
                                        <p class="text-[10px] text-blue-500 font-medium mt-1">3 jam yang lalu</p>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="p-2 bg-gray-50 mt-1">
                            <a href="#" class="block w-full text-center py-2 text-xs font-bold text-blue-600 hover:text-blue-700 hover:bg-white rounded-lg transition-all border border-transparent hover:border-blue-100">
                                Lihat Semua Notifikasi
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Desktop user dropdown -->
            <div class="hidden sm:flex items-center space-x-4 flex-1 justify-end">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                        <img src="{{ auth()->user()->avatar_url }}"
                            alt="{{ auth()->user()->name }}"
                            class="h-8 w-8 rounded-full object-cover">
                        <span class="text-sm font-medium text-gray-900 max-w-[180px] truncate">
                            {{ auth()->user()->name }}
                        </span>
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </button>

                    <div x-show="open"
                        @click.away="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-52 bg-white rounded-lg shadow-lg py-2 border border-gray-200"
                        style="display:none;">

                        @if(auth()->user()->outlet_id)
                            <div class="px-4 py-2 border-b border-gray-200">
                                <p class="text-xs text-gray-500">Outlet ({{ auth()->user()->getRoleNames()->first() }})</p>
                                <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->outlet->name }}</p>
                            </div>
                        @endif

                        <a href="{{ route('subscription.manage') }}"
                            class="nav-link block px-4 py-2 text-sm text-gray-900 hover:bg-cuan-yellow/20">
                            <i class="fa-solid fa-crown mr-2 text-cuan-green"></i>
                            Kelola Langganan
                        </a>

                        <a href="{{ route('profile.edit') }}"
                            class="nav-link block px-4 py-2 text-sm text-gray-900 hover:bg-cuan-yellow/20">
                            <i class="fa-solid fa-gear mr-2"></i>
                            Pengaturan Akun
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="logout-btn w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i class="fa-solid fa-right-from-bracket mr-2"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right: Mobile hamburger & Notification -->
            <div class="sm:hidden flex items-center space-x-3">
                <div class="relative">
                    <button @click="notiOpen = !notiOpen" 
                        class="p-2 text-gray-400 hover:text-gray-900 focus:outline-none relative">
                        <i class="fa-solid fa-bell text-xl"></i>
                        <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white border-2 border-white">
                            3
                        </span>
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
                        class="absolute right-0 mt-3 w-[280px] bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50 overflow-hidden origin-top-right"
                        style="display:none;">
                        
                        <div class="px-4 py-3 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                            <h3 class="text-sm font-bold text-gray-900 leading-none">Notifikasi</h3>
                            <span class="text-[10px] font-bold bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full uppercase tracking-wider">Dummy</span>
                        </div>

                        <div class="max-h-[300px] overflow-y-auto custom-scrollbar">
                            <a href="#" class="block px-4 py-3.5 hover:bg-gray-50 border-b border-gray-50">
                                <div class="flex gap-3">
                                    <div class="h-8 w-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-receipt text-xs"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-gray-900 truncate">Pesanan Baru: #INV-2656</p>
                                        <p class="text-[10px] text-gray-500 mt-0.5">Ada pesanan baru masuk.</p>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="block px-4 py-3.5 hover:bg-gray-50">
                                <div class="flex gap-3">
                                    <div class="h-8 w-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-exclamation-triangle text-xs"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-gray-900 truncate">Stok Menipis: Telur</p>
                                        <p class="text-[10px] text-gray-500 mt-0.5">Sisa stok sisa 0.5 kg.</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="p-2 border-t border-gray-50">
                            <a href="#" class="block w-full text-center py-2 text-xs font-bold text-blue-600">
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

    <!-- Mobile panel -->
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
            <!-- User info -->
            <div class="flex items-center space-x-3">
                <img src="{{ auth()->user()->avatar_url }}"
                    alt="{{ auth()->user()->name }}"
                    class="h-10 w-10 rounded-full object-cover">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                    @if(auth()->user()->outlet_id)
                        <p class="text-xs text-gray-500 truncate">
                            {{ auth()->user()->outlet->name }} • {{ auth()->user()->getRoleNames()->first() }}
                        </p>
                    @endif
                </div>
            </div>

            <!-- Mobile outlet switcher (only if multiple) -->
            @if(auth()->check() && auth()->user()->outlet_id && auth()->user()->outlet)
                @php
                    $userOutlets = auth()->user()->isOwner()
                        ? auth()->user()->outletsOwned->sortBy('name')
                        : collect([auth()->user()->outlet]);
                    $hasMultipleOutlets = $userOutlets->count() > 1;
                @endphp

                @if($hasMultipleOutlets)
                    <div class="pt-2 border-t border-gray-100">
                        <p class="text-xs font-medium text-gray-500 mb-2">Pilih Outlet</p>
                        <div class="space-y-2">
                            @foreach($userOutlets as $outlet)
                                <form method="POST" action="{{ route('change.outlet') }}">
                                    @csrf
                                    <input type="hidden" name="outlet_id" value="{{ $outlet->id }}">
                                    <button type="submit"
                                        class="w-full flex items-center space-x-3 px-3 py-2 rounded-lg border
                                            {{ auth()->user()->outlet_id == $outlet->id ? 'border-cuan-green bg-cuan-yellow/20' : 'border-gray-200 hover:bg-gray-50' }}">
                                        @if($outlet->logo)
                                            <img src="{{ Storage::url($outlet->logo) }}" alt="{{ $outlet->name }}"
                                                class="h-8 w-8 object-contain rounded">
                                        @else
                                            <div class="h-8 w-8 rounded bg-gradient-to-br from-cuan-olive to-cuan-green flex items-center justify-center text-white font-semibold text-sm">
                                                {{ substr($outlet->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0 text-left">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $outlet->name }}</p>
                                            <p class="text-xs text-gray-500 truncate">{{ $outlet->business_category }}</p>
                                        </div>
                                        @if(auth()->user()->outlet_id == $outlet->id)
                                            <i class="fa-solid fa-check text-cuan-green"></i>
                                        @endif
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

            <!-- Actions -->
            <div class="pt-2 border-t border-gray-100 space-y-2">
                <a href="{{ route('profile.edit') }}"
                    class="nav-link flex items-center w-full px-3 py-2 rounded-lg hover:bg-cuan-yellow/20 text-gray-900">
                    <i class="fa-solid fa-gear mr-2"></i>
                    Pengaturan Akun
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="logout-btn flex items-center w-full px-3 py-2 rounded-lg hover:bg-red-50 text-red-600">
                        <i class="fa-solid fa-right-from-bracket mr-2"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>


        <!-- Breadcrumbs -->
        @if(View::hasSection('breadcrumb'))
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-sm">
                        <li>
                            <a href="{{ route('dashboard') }}" class="nav-link text-gray-500 hover:text-gray-700 flex items-center">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                </svg>
                            </a>
                        </li>
                        @yield('breadcrumb')
                    </ol>
                </nav>
            </div>
        </div>
        @endif

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>

        <!-- Main Content -->
        <main class="flex-grow">
            @yield('content')
        </main>
    </div>

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
    
    @stack('scripts')
</body>
</html>
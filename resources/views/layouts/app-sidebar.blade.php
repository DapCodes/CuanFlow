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
    
    <!-- Satoshi Font -->
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@700,500,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
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
        body { font-family: 'Satoshi', sans-serif; }

        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }

        /* Loader Styles */
        .global-page-loader {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: #ffffff; display: flex; flex-direction: column;
            align-items: center; justify-content: center; z-index: 99999;
            opacity: 0; visibility: hidden; transition: opacity 0.2s ease, visibility 0.2s ease;
        }
        .global-page-loader.active { opacity: 1; visibility: visible; }
        #app-content-wrapper { opacity: 0; transition: opacity 0.6s ease; }
        #app-content-wrapper.ready { opacity: 1; }
    </style>
    
    @stack('styles')
</head>
<body class="antialiased bg-gray-50/50 text-gray-900" x-data="{ sidebarOpen: false, sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true' }" x-init="$watch('sidebarCollapsed', value => localStorage.setItem('sidebarCollapsed', value))">
    <!-- Loader -->
    <div id="global-page-loader" class="global-page-loader active">
        <svg  width="60" height="60" viewBox="0 0 80 80" fill="none" class="animate-spin">
            <path d="M40 10V70M10 40H70M20 20L60 60M60 20L20 60" stroke="#31694E" stroke-width="8" stroke-linecap="round"/>
        </svg>
        <p class="mt-4 text-cuan-dark font-bold">Loading...</p>
    </div>

    <div id="app-content-wrapper" class="min-h-screen flex">
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
        
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 bg-white border-r border-gray-100 transform lg:translate-x-0 overflow-x-hidden"
               :class="{ 
                   'translate-x-0': sidebarOpen, 
                   '-translate-x-full': !sidebarOpen,
                   'w-64': !sidebarCollapsed,
                   'w-20': sidebarCollapsed 
               }">
            @include('layouts.sidebar-content')
        </aside>
        
        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-h-screen"
             :class="{ 
                 'lg:ml-64': !sidebarCollapsed, 
                 'lg:ml-20': sidebarCollapsed 
             }">
            
            <!-- Navbar -->
            @inject('stockNotiService', 'App\Services\StockNotificationService')
            @php
                $navStockNotifications = auth()->check() && auth()->user()->outlet_id 
                    ? $stockNotiService->getLatestNotifications(auth()->user()->outlet_id, 15)
                    : collect();
                $navStockNotifications = $navStockNotifications->where('is_read_by_me', false)->values();
                $unreadStockCount = $navStockNotifications->count();
            @endphp
            
            <header class="bg-white border-b border-gray-100 sticky top-0 z-40 h-16 flex items-center">
                <div class="flex items-center justify-between w-full px-4 lg:px-8">
                    <!-- Left: Toggle & Title -->
                    <div class="flex items-center gap-4">
                        <!-- Desktop Toggle -->
                        <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden lg:flex text-gray-500 hover:text-emerald-600">
                            <i class="fas" :class="sidebarCollapsed ? 'fa-indent' : 'fa-outdent'"></i>
                        </button>
                        <!-- Mobile Toggle -->
                        <button @click="sidebarOpen = true" class="lg:hidden text-gray-600 hover:text-gray-900">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h1 class="text-xs font-bold text-gray-500 uppercase tracking-[0.2em] hidden sm:block">
                            @yield('title')
                        </h1>
                    </div>
                    
                    <!-- Right: Notifications & User -->
                    <div class="flex items-center gap-2 sm:gap-4">
                        <!-- Notifications -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl relative transition-all">
                                <i class="fa-regular fa-bell text-lg"></i>
                                @if($unreadStockCount > 0)
                                    <span class="absolute top-2.5 right-2.5 flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500 border border-white"></span>
                                    </span>
                                @endif
                            </button>

                            <!-- Noti Dropdown -->
                            <div x-show="open" @click.away="open = false" 
                                 class="absolute right-0 mt-3 w-[85vw] sm:w-96 bg-white rounded-2xl shadow-xl border border-gray-100 ring-1 ring-black/5 z-50 overflow-hidden"
                                 x-transition style="display:none;">
                                <div class="px-5 py-4 flex items-center justify-between border-b border-gray-50 bg-gray-50/30">
                                    <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest">Pemberitahuan</h3>
                                    @if($unreadStockCount > 0)
                                        <button onclick="markAllStockAsRead()" class="text-[10px] font-bold text-emerald-600 hover:underline">Tandai Dibaca</button>
                                    @endif
                                </div>
                                <div class="max-h-80 overflow-y-auto custom-scrollbar">
                                    @forelse($navStockNotifications as $noti)
                                        <div class="p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors">
                                            <p class="text-xs font-bold text-gray-900">{{ $noti->title }}</p>
                                            <p class="text-[11px] text-gray-500 mt-1">{{ $noti->message }}</p>
                                            <span class="text-[10px] text-gray-400 mt-2 block">{{ $noti->created_at->diffForHumans() }}</span>
                                        </div>
                                    @empty
                                        <div class="py-12 text-center text-gray-400">
                                            <i class="fa-solid fa-check-circle text-2xl mb-2 opacity-20"></i>
                                            <p class="text-[11px]">Semua aman, Bos!</p>
                                        </div>
                                    @endforelse
                                </div>
                                <a href="{{ route('stock-notifications.index') }}" class="block w-full py-3 text-center text-[10px] font-black uppercase tracking-widest text-emerald-600 bg-gray-50 hover:bg-emerald-50">Laporan Stok Lengkap</a>
                            </div>
                        </div>

                        <!-- User Profile -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 hover:bg-gray-50 rounded-xl px-1 py-1 transition-all">
                                <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-full object-cover ring-2 ring-emerald-50">
                                <div class="hidden sm:block text-left mr-1">
                                    <span class="block text-xs font-bold text-gray-900 leading-none truncate max-w-[120px]">{{ auth()->user()->name }}</span>
                                    <span class="block text-[9px] font-bold text-emerald-600 uppercase tracking-wider mt-1">{{ auth()->user()->getRoleNames()->first() }}</span>
                                </div>
                                <i class="fas fa-chevron-down text-[10px] text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>

                            <!-- Profile Dropdown -->
                            <div x-show="open" @click.away="open = false" 
                                 class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50"
                                 x-transition style="display:none;">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-xs font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-[10px] text-gray-500 truncate mt-0.5">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-xs text-gray-700 hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-user-gear w-4 text-center text-gray-400"></i>
                                    <span>Pengaturan Akun</span>
                                </a>
                                @hasrole('owner')
                                <a href="{{ route('subscription.manage') }}" class="flex items-center gap-3 px-4 py-2.5 text-xs text-gray-700 hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-crown w-4 text-center text-emerald-500"></i>
                                    <span>Langganan VIP</span>
                                </a>
                                @endhasrole
                                <div class="border-t border-gray-100 mt-2 pt-2">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-xs text-red-600 hover:bg-red-50 transition-colors font-bold">
                                            <i class="fas fa-sign-out-alt w-4 text-center"></i>
                                            <span>Keluar Aplikasi</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Breadcrumbs -->
            @if(View::hasSection('breadcrumb'))
            <nav class="flex py-3 px-8 bg-white/50 border-b border-gray-100" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-[10px] font-bold text-gray-400 tracking-widest uppercase">
                    <li><a href="{{ route('dashboard') }}" class="hover:text-emerald-600"><i class="fas fa-home"></i></a></li>
                    <i class="fas fa-chevron-right text-[7px] opacity-40"></i>
                    @yield('breadcrumb')
                </ol>
            </nav>
            @endif
            
            <!-- Page Content -->
            <main class="flex-1 p-6 lg:p-10 overflow-y-auto">
                <div class="max-w-[1600px] mx-auto">
                    @yield('content')
                </div>
            </main>
            
            <footer class="bg-white border-t border-gray-100 py-6 px-8">
                <p class="text-center text-[9px] text-gray-400 font-bold uppercase tracking-[0.3em]">
                    &copy; {{ date('Y') }} CuanFlow Ecosystem. All rights reserved.
                </p>
            </footer>
        </div>
    </div>
    
    <!-- Scripts -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        (function() {
            const loader = document.getElementById('global-page-loader');
            const wrapper = document.getElementById('app-content-wrapper');
            
            window.addEventListener('load', () => {
                setTimeout(() => {
                    loader.classList.remove('active');
                    wrapper.classList.add('ready');
                }, 400);
            });

            @if(session('success'))
                Swal.fire({
                    icon: 'success', title: 'Sukses!', text: "{{ session('success') }}",
                    showConfirmButton: false, timer: 3000, iconColor: '#658C58',
                    customClass: { popup: 'rounded-3xl border-none shadow-2xl' }
                });
            @endif
            @if(session('error'))
                Swal.fire({
                    icon: 'error', title: 'Error!', text: "{{ session('error') }}",
                    showConfirmButton: false, timer: 3000, iconColor: '#ef4444',
                    customClass: { popup: 'rounded-3xl border-none shadow-2xl' }
                });
            @endif
        })();
        
        function markAllStockAsRead() {
            fetch('{{ route('stock-notifications.read-all') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            }).then(() => window.location.reload());
        }
    </script>
    @stack('scripts')
</body>
</html>
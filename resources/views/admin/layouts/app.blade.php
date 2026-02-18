<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - CuanFlow</title>
    
    <!-- Preload -->
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="preconnect" href="https://api.fontshare.com">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/image/logo.svg') }}" type="image/x-icon">
    
    <!-- Fonts -->
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
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }
    </style>
    
    @stack('styles')
</head>
<body class="antialiased bg-gray-50/50 text-gray-900" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen flex">
        <!-- Sidebar Overlay (Mobile) -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-gray-900/50 z-40 lg:hidden"
             style="display: none;"></div>
        
        <!-- Sidebar -->
        @include('admin.partials.sidebar')
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-screen lg:ml-64">
            <!-- Navbar -->
            @include('admin.partials.navbar')
            
            <!-- Breadcrumbs -->
            @if(View::hasSection('breadcrumb'))
            <div class="bg-white border-b border-gray-100 flex-shrink-0">
                <div class="px-4 lg:px-6 py-3.5">
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-2 text-sm leading-none text-gray-400">
                            <li>
                                <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 transition-colors">
                                    <i class="fas fa-home text-[10px]"></i>
                                </a>
                            </li>
                            @yield('breadcrumb')
                        </ol>
                    </nav>
                </div>
            </div>
            @endif
            
            <!-- Page Content -->
            <main class="flex-1 p-4 lg:p-6 overflow-y-auto">
                <!-- Flash Messages -->
                @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl flex items-center gap-3">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
                @endif
                
                @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-800 rounded-2xl flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
                @endif

                @yield('content')
            </main>
            
            <!-- Footer -->
            <footer class="bg-white border-t border-gray-100 py-4 px-6 flex-shrink-0">
                <p class="text-center text-xs text-gray-400">
                    &copy; {{ date('Y') }} CuanFlow Admin. All rights reserved.
                </p>
            </footer>
        </div>
    </div>
    
    <!-- Vite -->
    @vite(['resources/js/app.js'])
    
    @stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'CuanFlow')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- favicon --}}
    <link rel="shortcut icon" href="{{ asset('assets/image/logo.svg') }}" type="image/x-icon">
    
    <!-- Satoshi Font -->
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@700,500,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
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
        body {
            font-family: 'Satoshi', sans-serif;
        }
    </style>
    
    @stack('styles')
</head>
<body class="antialiased bg-gray-50">
    <div class="min-h-screen flex flex-col">
        <!-- Navbar -->

<nav class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo / Outlet -->
                @if(auth()->check() && auth()->user()->outlet_id && auth()->user()->outlet)
                    @php
                        $userOutlets = auth()->user()->outlets ?? collect([auth()->user()->outlet]);
                        $hasMultipleOutlets = $userOutlets->count() > 1;
                    @endphp
                    
                    @if($hasMultipleOutlets)
                        <!-- Multi Outlet Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-3 hover:bg-gray-50 px-3 py-2 rounded-lg transition">
                                @if(auth()->user()->outlet->logo)
                                    <img src="{{ Storage::url(auth()->user()->outlet->logo) }}" 
                                        alt="{{ auth()->user()->outlet->name }}" 
                                        class="h-10 w-10 object-contain rounded-lg">
                                @endif
                                <div class="flex items-center space-x-2">
                                    <span class="text-xl font-bold text-gray-900">
                                        {{ auth()->user()->outlet->name }}
                                    </span>
                                    <i class="fa-solid fa-chevron-down text-xs text-gray-600"></i>
                                </div>
                            </button>

                            <!-- Outlet Dropdown Menu -->
                            <div x-show="open" 
                                @click.away="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute left-0 mt-2 w-64 bg-white rounded-lg shadow-lg py-2 border border-gray-200"
                                style="display: none;">
                                
                                <div class="px-4 py-2 border-b border-gray-200">
                                    <p class="text-xs text-gray-500 font-medium">Pilih Outlet</p>
                                </div>

                                @foreach($userOutlets as $outlet)
                                    <a href="{{ route('outlets.switch', $outlet->id) }}" 
                                        class="block px-4 py-3 hover:bg-cuan-yellow/20 transition {{ auth()->user()->outlet_id == $outlet->id ? 'bg-cuan-yellow/30' : '' }}">
                                        <div class="flex items-center space-x-3">
                                            @if($outlet->logo)
                                                <img src="{{ Storage::url($outlet->logo) }}" 
                                                    alt="{{ $outlet->name }}" 
                                                    class="h-8 w-8 object-contain rounded">
                                            @else
                                                <div class="h-8 w-8 rounded bg-gradient-to-br from-cuan-olive to-cuan-green flex items-center justify-center text-white font-semibold text-sm">
                                                    {{ substr($outlet->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900">{{ $outlet->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $outlet->business_category }}</p>
                                            </div>
                                            @if(auth()->user()->outlet_id == $outlet->id)
                                                <i class="fa-solid fa-check text-cuan-green"></i>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <!-- Single Outlet -->
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                            @if(auth()->user()->outlet->logo)
                                <img src="{{ Storage::url(auth()->user()->outlet->logo) }}" 
                                    alt="{{ auth()->user()->outlet->name }}" 
                                    class="h-10 w-10 object-contain rounded-lg">
                            @endif
                            <span class="text-xl font-bold text-gray-900">
                                {{ auth()->user()->outlet->name }}
                            </span>
                        </a>
                    @endif
                @else
                    <!-- CuanFlow Logo (No Outlet) -->
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                        <img src="{{ asset('assets/image/logo.svg') }}" 
                            alt="CuanFlow" 
                            class="h-10 w-auto">
                        <span class="text-xl font-bold text-gray-900">
                            CuanFlow
                        </span>
                    </a>
                @endif
            </div>

            <!-- Right Side -->
            <div class="flex items-center space-x-4">
                <!-- User Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                        @if(auth()->user()->avatar)
                            <img src="{{ Storage::url(auth()->user()->avatar) }}" 
                                alt="{{ auth()->user()->name }}" 
                                class="h-8 w-8 rounded-full object-cover">
                        @else
                            <div class="h-8 w-8 rounded-full bg-gradient-to-br from-cuan-olive to-cuan-green flex items-center justify-center text-white font-semibold">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        @endif
                        <span class="text-sm font-medium hidden sm:block text-gray-900">{{ auth()->user()->name }}</span>
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" 
                        @click.away="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 border border-gray-200"
                        style="display: none;">
                        
                        @if(auth()->user()->outlet_id)
                            <div class="px-4 py-2 border-b border-gray-200">
                                <p class="text-xs text-gray-500">Outlet ({{ auth()->user()->getRoleNames()->first() }}) </p>
                                <p class="text-sm font-medium text-gray-900">{{ auth()->user()->outlet->name }}</p>
                            </div>
                        @endif

                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-900 hover:bg-cuan-yellow/20">
                            <i class="fa-solid fa-user mr-2"></i>
                            Profile
                        </a>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i class="fa-solid fa-right-from-bracket mr-2"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
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
                            <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700 flex items-center">
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

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });

        // User dropdown toggle (Desktop)
        document.getElementById('user-menu-button').addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = document.getElementById('user-dropdown');
            dropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('user-dropdown');
            const button = document.getElementById('user-menu-button');
            
            if (!button.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
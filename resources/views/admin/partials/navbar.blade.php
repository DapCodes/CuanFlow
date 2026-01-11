<!-- Navbar -->
<header class="bg-white border-b border-gray-200 sticky top-0 z-30">
    <div class="flex items-center justify-between h-16 px-4 lg:px-6">
        <!-- Left: Mobile Menu Button & Search -->
        <div class="flex items-center gap-4">
            <!-- Mobile Menu Toggle -->
            <button @click="sidebarOpen = true" class="lg:hidden text-gray-600 hover:text-gray-900">
                <i class="fas fa-bars text-xl"></i>
            </button>
            
            <!-- Page Title -->
            <h1 class="text-lg font-semibold text-gray-900 hidden sm:block">
                @yield('page-title', 'Admin Dashboard')
            </h1>
        </div>
        
        <!-- Right: User Dropdown -->
        <div class="flex items-center gap-4">
            <!-- User Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="flex items-center gap-2.5 hover:bg-gray-50 rounded-xl px-2.5 py-1.5 transition-all duration-200">
                    <div class="relative">
                        <img src="{{ auth()->user()->avatar_url }}" 
                             alt="{{ auth()->user()->name }}" 
                             class="w-9 h-9 rounded-full object-cover ring-2 ring-emerald-50">
                        <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full"></div>
                    </div>
                    <div class="hidden sm:block text-left">
                        <span class="block text-sm font-semibold text-gray-900 leading-tight">{{ auth()->user()->name }}</span>
                        <span class="block text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Super Admin</span>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] text-gray-400 ml-1 transition-transform" :class="open ? 'rotate-180' : ''"></i>
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
                     class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2"
                     style="display: none;">
                    
                    <!-- User Info -->
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                    </div>
                    
                    <!-- Menu Items -->
                    <a href="{{ route('profile.edit') }}" 
                       class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-user-cog w-4 text-center text-gray-400"></i>
                        <span>Pengaturan Profil</span>
                    </a>
                    
                    <div class="border-t border-gray-100 mt-2 pt-2">
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt w-4 text-center"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Sidebar -->
<aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-100 transform transition-transform duration-300 ease-in-out lg:translate-x-0"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    
    <!-- Logo -->
    <div class="flex items-center justify-between h-20 px-6 mb-4">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group">
            <div class="p-2 bg-emerald-50 rounded-xl group-hover:bg-emerald-100 transition-colors">
                <img src="{{ asset('assets/image/logo.svg') }}" alt="CuanFlow" class="h-8 w-8">
            </div>
            <div>
                <span class="text-gray-900 font-bold text-lg tracking-tight">CuanFlow</span>
                <span class="block text-emerald-600 text-[10px] font-bold uppercase tracking-wider">Admin Panel</span>
            </div>
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-emerald-600 transition-colors">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>
    
    <!-- Navigation -->
    <nav class="px-4 custom-scrollbar overflow-y-auto" style="height: calc(100vh - 6rem);">
        <ul class="space-y-1.5">
            <!-- Section Label -->
            <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2 mt-4">Menu Utama</p>
            
            <!-- Dashboard -->
            <li>
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm shadow-emerald-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-home w-5 text-center text-lg {{ request()->routeIs('admin.dashboard') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Dashboard</span>
                </a>
            </li>

            <!-- Outlets -->
            <li>
                <a href="{{ route('admin.outlets.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.outlets.*') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm shadow-emerald-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-store w-5 text-center text-lg {{ request()->routeIs('admin.outlets.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Manajemen Outlet</span>
                </a>
            </li>
            
            <!-- Data Master (Collapsible) -->
            <li x-data="{ open: {{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') || request()->routeIs('admin.permission-categories.*') || request()->routeIs('admin.users.*') || request()->routeIs('admin.units.*') || request()->routeIs('admin.expense-categories.*') || request()->routeIs('admin.faqs.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.payment-methods.*') || request()->routeIs('admin.task-statuses.*') || request()->routeIs('admin.task-labels.*') || request()->routeIs('admin.testimonials.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" 
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 group text-gray-500 hover:bg-gray-50 hover:text-gray-900">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-database w-5 text-center text-lg text-gray-400 group-hover:text-emerald-500"></i>
                        <span class="text-sm">Data Master</span>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] transition-transform duration-300" :class="open ? 'rotate-180 text-emerald-600' : ''"></i>
                </button>
                
                <ul x-show="open" 
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    style="display: none;"
                    class="mt-1 ml-4 space-y-1 border-l border-gray-100 pl-4 py-2">
                    
                    @php
                        $masterItems = [
                            ['route' => 'admin.roles.index', 'icon' => 'fa-user-shield', 'label' => 'Roles', 'pattern' => 'admin.roles.*'],
                            ['route' => 'admin.permissions.index', 'icon' => 'fa-key', 'label' => 'Permissions', 'pattern' => 'admin.permissions.*'],
                            ['route' => 'admin.permission-categories.index', 'icon' => 'fa-layer-group', 'label' => 'Kat. Permission', 'pattern' => 'admin.permission-categories.*'],
                            ['route' => 'admin.users.index', 'icon' => 'fa-users', 'label' => 'Users', 'pattern' => 'admin.users.*'],
                            ['route' => 'admin.admins.index', 'icon' => 'fa-user-cog', 'label' => 'Admins', 'pattern' => 'admin.admins.*'],
                            ['type' => 'divider', 'label' => 'Bisnis'],
                            ['route' => 'admin.categories.index', 'icon' => 'fa-folder-open', 'label' => 'Kategori', 'pattern' => 'admin.categories.*'],
                            ['route' => 'admin.units.index', 'icon' => 'fa-ruler', 'label' => 'Units', 'pattern' => 'admin.units.*'],
                            ['route' => 'admin.payment-methods.index', 'icon' => 'fa-credit-card', 'label' => 'Metode Bayar', 'pattern' => 'admin.payment-methods.*'],
                            ['route' => 'admin.expense-categories.index', 'icon' => 'fa-tags', 'label' => 'Kat. Pengeluaran', 'pattern' => 'admin.expense-categories.*'],
                            ['type' => 'divider', 'label' => 'Konten'],
                            ['route' => 'admin.task-statuses.index', 'icon' => 'fa-list-check', 'label' => 'Status Tugas', 'pattern' => 'admin.task-statuses.*'],
                            ['route' => 'admin.task-labels.index', 'icon' => 'fa-tag', 'label' => 'Label Tugas', 'pattern' => 'admin.task-labels.*'],
                            ['route' => 'admin.testimonials.index', 'icon' => 'fa-quote-left', 'label' => 'Testimonial', 'pattern' => 'admin.testimonials.*'],
                            ['route' => 'admin.faqs.index', 'icon' => 'fa-question-circle', 'label' => 'FAQ', 'pattern' => 'admin.faqs.*'],
                        ];
                    @endphp

                    @foreach($masterItems as $item)
                        @if(isset($item['type']) && $item['type'] === 'divider')
                            <p class="text-[9px] font-bold text-gray-300 uppercase tracking-widest pt-3 pb-1 pl-2">{{ $item['label'] }}</p>
                        @else
                            <li>
                                <a href="{{ route($item['route']) }}" 
                                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs($item['pattern']) ? 'text-emerald-600 bg-emerald-50/50 font-semibold' : 'text-gray-500 hover:text-emerald-600 hover:bg-gray-50' }}">
                                    <i class="fas {{ $item['icon'] }} w-4 text-center {{ request()->routeIs($item['pattern']) ? 'text-emerald-500' : 'text-gray-300' }}"></i>
                                    <span>{{ $item['label'] }}</span>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </li>

            <!-- Section Label -->
            <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2 mt-6">Keuangan</p>

            <!-- Withdrawals -->
            <li>
                <a href="{{ route('admin.withdrawals.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.withdrawals.*') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm shadow-emerald-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-money-bill-transfer w-5 text-center text-lg {{ request()->routeIs('admin.withdrawals.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Penarikan</span>
                    @php
                        $pendingCount = \App\Models\Withdrawal::pending()->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold leading-none text-white bg-red-500 rounded-full">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>
            </li>
        </ul>
    </nav>
</aside>


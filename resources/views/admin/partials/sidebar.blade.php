<!-- Sidebar -->
<aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-cuan-dark to-cuan-green transform transition-transform duration-300 ease-in-out lg:translate-x-0"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    
    <!-- Logo -->
    <div class="flex items-center justify-between h-16 px-6 border-b border-white/10">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            <img src="{{ asset('assets/image/logo.svg') }}" alt="CuanFlow" class="h-10 w-10">
            <div>
                <span class="text-white font-bold text-lg">CuanFlow</span>
                <span class="block text-cuan-yellow text-xs font-medium">Admin Panel</span>
            </div>
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden text-white hover:text-cuan-yellow">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>
    
    <!-- Navigation -->
    <nav class="mt-6 px-4 custom-scrollbar overflow-y-auto" style="height: calc(100vh - 8rem);">
        <ul class="space-y-2">
            <!-- Dashboard -->
            <li>
                <a href="{{ route('admin.dashboard') }}" 
                   class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white/90 hover:text-white {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home w-5 text-center"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
            </li>

            <!-- Outlets -->
            <li>
                <a href="{{ route('admin.outlets.index') }}" 
                   class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white/90 hover:text-white {{ request()->routeIs('admin.outlets.*') ? 'active' : '' }}">
                    <i class="fas fa-store w-5 text-center"></i>
                    <span class="font-medium">Manajemen Outlet</span>
                </a>
            </li>
            
            <!-- Data Master (Collapsible) -->
            <li x-data="{ open: {{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') || request()->routeIs('admin.permission-categories.*') || request()->routeIs('admin.users.*') || request()->routeIs('admin.units.*') || request()->routeIs('admin.expense-categories.*') || request()->routeIs('admin.faqs.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.payment-methods.*') || request()->routeIs('admin.task-statuses.*') || request()->routeIs('admin.task-labels.*') || request()->routeIs('admin.testimonials.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" 
                        class="w-full sidebar-link flex items-center justify-between px-4 py-3 rounded-lg text-white/90 hover:text-white">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-database w-5 text-center"></i>
                        <span class="font-medium">Data Master</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                </button>
                
                <ul x-show="open" 
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="mt-2 ml-4 space-y-1 border-l-2 border-white/20 pl-4 overflow-y-auto max-h-[60vh] custom-scrollbar">
                    
                    <!-- Roles & Permissions Section -->
                    <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mt-2 mb-1 text-center">Akses & User</p>
                    <li>
                        <a href="{{ route('admin.roles.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-white/80 hover:text-white hover:bg-white/10 {{ request()->routeIs('admin.roles.*') ? 'bg-white/10 text-white font-semibold' : '' }}">
                            <i class="fas fa-user-shield w-4 text-center text-cuan-yellow"></i>
                            <span>Roles</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.permissions.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-white/80 hover:text-white hover:bg-white/10 {{ request()->routeIs('admin.permissions.*') ? 'bg-white/10 text-white font-semibold' : '' }}">
                            <i class="fas fa-key w-4 text-center text-cuan-yellow"></i>
                            <span>Permissions</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.permission-categories.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-white/80 hover:text-white hover:bg-white/10 {{ request()->routeIs('admin.permission-categories.*') ? 'bg-white/10 text-white font-semibold' : '' }}">
                            <i class="fas fa-layer-group w-4 text-center text-cuan-yellow"></i>
                            <span>Kat. Permission</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.users.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-white/80 hover:text-white hover:bg-white/10 {{ request()->routeIs('admin.users.*') ? 'bg-white/10 text-white font-semibold' : '' }}">
                            <i class="fas fa-users w-4 text-center text-cuan-yellow"></i>
                            <span>Users</span>
                        </a>
                    </li>

                    <!-- Business Master Section -->
                    <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mt-4 mb-1 text-center">Bisnis</p>
                    <li>
                        <a href="{{ route('admin.categories.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-white/80 hover:text-white hover:bg-white/10 {{ request()->routeIs('admin.categories.*') ? 'bg-white/10 text-white font-semibold' : '' }}">
                            <i class="fas fa-folder-open w-4 text-center text-cuan-yellow"></i>
                            <span>Kategori</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.units.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-white/80 hover:text-white hover:bg-white/10 {{ request()->routeIs('admin.units.*') ? 'bg-white/10 text-white font-semibold' : '' }}">
                            <i class="fas fa-ruler w-4 text-center text-cuan-yellow"></i>
                            <span>Units</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.payment-methods.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-white/80 hover:text-white hover:bg-white/10 {{ request()->routeIs('admin.payment-methods.*') ? 'bg-white/10 text-white font-semibold' : '' }}">
                            <i class="fas fa-credit-card w-4 text-center text-cuan-yellow"></i>
                            <span>Metode Bayar</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.expense-categories.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-white/80 hover:text-white hover:bg-white/10 {{ request()->routeIs('admin.expense-categories.*') ? 'bg-white/10 text-white font-semibold' : '' }}">
                            <i class="fas fa-tags w-4 text-center text-cuan-yellow"></i>
                            <span>Kat. Pengeluaran</span>
                        </a>
                    </li>

                    <!-- System & Content Section -->
                    <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mt-4 mb-1 text-center">Sistem & Konten</p>
                    <li>
                        <a href="{{ route('admin.task-statuses.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-white/80 hover:text-white hover:bg-white/10 {{ request()->routeIs('admin.task-statuses.*') ? 'bg-white/10 text-white font-semibold' : '' }}">
                            <i class="fas fa-list-check w-4 text-center text-cuan-yellow"></i>
                            <span>Status Tugas</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.task-labels.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-white/80 hover:text-white hover:bg-white/10 {{ request()->routeIs('admin.task-labels.*') ? 'bg-white/10 text-white font-semibold' : '' }}">
                            <i class="fas fa-tag w-4 text-center text-cuan-yellow"></i>
                            <span>Label Tugas</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.testimonials.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-white/80 hover:text-white hover:bg-white/10 {{ request()->routeIs('admin.testimonials.*') ? 'bg-white/10 text-white font-semibold' : '' }}">
                            <i class="fas fa-quote-left w-4 text-center text-cuan-yellow"></i>
                            <span>Testimonial</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.faqs.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-white/80 hover:text-white hover:bg-white/10 {{ request()->routeIs('admin.faqs.*') ? 'bg-white/10 text-white font-semibold' : '' }}">
                            <i class="fas fa-question-circle w-4 text-center text-cuan-yellow"></i>
                            <span>FAQ</span>
                        </a>
                    </li>
                </ul>
            </li>



            <!-- Withdrawals -->
            <li>
                <a href="{{ route('admin.withdrawals.index') }}" 
                   class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white/90 hover:text-white {{ request()->routeIs('admin.withdrawals.*') ? 'active' : '' }}">
                    <i class="fas fa-money-bill-transfer w-5 text-center"></i>
                    <span class="font-medium">Penarikan</span>
                    @php
                        $pendingCount = \App\Models\Withdrawal::pending()->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="ml-auto inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>
            </li>
        </ul>
    </nav>
</aside>

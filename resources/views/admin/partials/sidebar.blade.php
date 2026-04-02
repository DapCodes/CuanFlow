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
        <ul class="space-y-1.5 pb-6">

            {{-- ═══════════════════════════════════ --}}
            {{-- SECTION: MENU UTAMA --}}
            {{-- ═══════════════════════════════════ --}}
            <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2 mt-4">Menu Utama</p>

            <!-- Dashboard -->
            <li>
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm shadow-emerald-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-home w-5 text-center text-lg {{ request()->routeIs('admin.dashboard') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Dashboard</span>
                </a>
            </li>

            <!-- Outlet -->
            <li>
                <a href="{{ route('admin.outlets.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.outlets.*') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm shadow-emerald-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-store w-5 text-center text-lg {{ request()->routeIs('admin.outlets.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Manajemen Outlet</span>
                </a>
            </li>

            {{-- ═══════════════════════════════════ --}}
            {{-- SECTION: PENGGUNA & AKSES --}}
            {{-- ═══════════════════════════════════ --}}
            <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2 mt-6">Pengguna & Akses</p>

            <!-- Users -->
            <li>
                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.users.*') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm shadow-emerald-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-users w-5 text-center text-lg {{ request()->routeIs('admin.users.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Users</span>
                </a>
            </li>

            <!-- Admins -->
            <li>
                <a href="{{ route('admin.admins.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.admins.*') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm shadow-emerald-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-user-cog w-5 text-center text-lg {{ request()->routeIs('admin.admins.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Admins</span>
                </a>
            </li>

            <!-- Roles & Permissions (Collapsible) -->
            <li x-data="{ open: {{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') || request()->routeIs('admin.permission-categories.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') || request()->routeIs('admin.permission-categories.*') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm shadow-emerald-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-user-shield w-5 text-center text-lg {{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') || request()->routeIs('admin.permission-categories.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                        <span class="text-sm">Roles & Permissions</span>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] transition-transform duration-300" :class="open ? 'rotate-180 text-emerald-600' : ''"></i>
                </button>
                <ul x-show="open"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    style="display: none;"
                    class="mt-1 ml-4 space-y-1 border-l border-gray-100 pl-4 py-2">
                    <li>
                        <a href="{{ route('admin.roles.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.roles.*') ? 'text-emerald-600 bg-emerald-50/50 font-semibold' : 'text-gray-500 hover:text-emerald-600 hover:bg-gray-50' }}">
                            <i class="fas fa-user-shield w-4 text-center {{ request()->routeIs('admin.roles.*') ? 'text-emerald-500' : 'text-gray-300' }}"></i>
                            <span>Roles</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.permissions.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.permissions.*') ? 'text-emerald-600 bg-emerald-50/50 font-semibold' : 'text-gray-500 hover:text-emerald-600 hover:bg-gray-50' }}">
                            <i class="fas fa-key w-4 text-center {{ request()->routeIs('admin.permissions.*') ? 'text-emerald-500' : 'text-gray-300' }}"></i>
                            <span>Permissions</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.permission-categories.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.permission-categories.*') ? 'text-emerald-600 bg-emerald-50/50 font-semibold' : 'text-gray-500 hover:text-emerald-600 hover:bg-gray-50' }}">
                            <i class="fas fa-layer-group w-4 text-center {{ request()->routeIs('admin.permission-categories.*') ? 'text-emerald-500' : 'text-gray-300' }}"></i>
                            <span>Kat. Permission</span>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- ═══════════════════════════════════ --}}
            {{-- SECTION: DATA BISNIS --}}
            {{-- ═══════════════════════════════════ --}}
            <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2 mt-6">Data Bisnis</p>

            <!-- Kategori & Satuan (Collapsible) -->
            <li x-data="{ open: {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.units.*') || request()->routeIs('admin.payment-methods.*') || request()->routeIs('admin.expense-categories.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.units.*') || request()->routeIs('admin.payment-methods.*') || request()->routeIs('admin.expense-categories.*') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm shadow-emerald-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-folder-open w-5 text-center text-lg {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.units.*') || request()->routeIs('admin.payment-methods.*') || request()->routeIs('admin.expense-categories.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                        <span class="text-sm">Master Data</span>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] transition-transform duration-300" :class="open ? 'rotate-180 text-emerald-600' : ''"></i>
                </button>
                <ul x-show="open"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    style="display: none;"
                    class="mt-1 ml-4 space-y-1 border-l border-gray-100 pl-4 py-2">
                    <li>
                        <a href="{{ route('admin.categories.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.categories.*') ? 'text-emerald-600 bg-emerald-50/50 font-semibold' : 'text-gray-500 hover:text-emerald-600 hover:bg-gray-50' }}">
                            <i class="fas fa-folder-open w-4 text-center {{ request()->routeIs('admin.categories.*') ? 'text-emerald-500' : 'text-gray-300' }}"></i>
                            <span>Kategori</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.units.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.units.*') ? 'text-emerald-600 bg-emerald-50/50 font-semibold' : 'text-gray-500 hover:text-emerald-600 hover:bg-gray-50' }}">
                            <i class="fas fa-ruler w-4 text-center {{ request()->routeIs('admin.units.*') ? 'text-emerald-500' : 'text-gray-300' }}"></i>
                            <span>Units</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.payment-methods.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.payment-methods.*') ? 'text-emerald-600 bg-emerald-50/50 font-semibold' : 'text-gray-500 hover:text-emerald-600 hover:bg-gray-50' }}">
                            <i class="fas fa-credit-card w-4 text-center {{ request()->routeIs('admin.payment-methods.*') ? 'text-emerald-500' : 'text-gray-300' }}"></i>
                            <span>Metode Bayar</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.expense-categories.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.expense-categories.*') ? 'text-emerald-600 bg-emerald-50/50 font-semibold' : 'text-gray-500 hover:text-emerald-600 hover:bg-gray-50' }}">
                            <i class="fas fa-tags w-4 text-center {{ request()->routeIs('admin.expense-categories.*') ? 'text-emerald-500' : 'text-gray-300' }}"></i>
                            <span>Kat. Pengeluaran</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Feature Flags -->
            <li>
                <a href="{{ route('admin.features.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.features.*') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm shadow-emerald-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-toggle-on w-5 text-center text-lg {{ request()->routeIs('admin.features.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Feature Flags</span>
                </a>
            </li>

            {{-- ═══════════════════════════════════ --}}
            {{-- SECTION: KEUANGAN --}}
            {{-- ═══════════════════════════════════ --}}
            <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2 mt-6">Keuangan</p>

            <!-- Penarikan -->
            <li>
                @php $pendingCount = \App\Models\Withdrawal::pending()->count(); @endphp
                <a href="{{ route('admin.withdrawals.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.withdrawals.*') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm shadow-emerald-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-money-bill-transfer w-5 text-center text-lg {{ request()->routeIs('admin.withdrawals.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Penarikan</span>
                    @if($pendingCount > 0)
                        <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold leading-none text-white bg-red-500 rounded-full">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>

            {{-- ═══════════════════════════════════ --}}
            {{-- SECTION: LANGGANAN --}}
            {{-- ═══════════════════════════════════ --}}
            <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2 mt-6">Langganan</p>

            @php $pendingTrialCount = \App\Models\TrialVerificationRequest::pending()->count(); @endphp

            <!-- Paket Langganan (Collapsible) -->
            <li x-data="{ open: {{ request()->routeIs('admin.subscription-tiers.*') || request()->routeIs('admin.subscription-plans.*') || request()->routeIs('admin.subscription-features.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.subscription-tiers.*') || request()->routeIs('admin.subscription-plans.*') || request()->routeIs('admin.subscription-features.*') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm shadow-emerald-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-gem w-5 text-center text-lg {{ request()->routeIs('admin.subscription-tiers.*') || request()->routeIs('admin.subscription-plans.*') || request()->routeIs('admin.subscription-features.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                        <span class="text-sm">Paket & Tier</span>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] transition-transform duration-300" :class="open ? 'rotate-180 text-emerald-600' : ''"></i>
                </button>
                <ul x-show="open"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    style="display: none;"
                    class="mt-1 ml-4 space-y-1 border-l border-gray-100 pl-4 py-2">
                    <li>
                        <a href="{{ route('admin.subscription-tiers.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.subscription-tiers.*') ? 'text-emerald-600 bg-emerald-50/50 font-semibold' : 'text-gray-500 hover:text-emerald-600 hover:bg-gray-50' }}">
                            <i class="fas fa-layer-group w-4 text-center {{ request()->routeIs('admin.subscription-tiers.*') ? 'text-emerald-500' : 'text-gray-300' }}"></i>
                            <span>Tier Langganan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.subscription-plans.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.subscription-plans.*') ? 'text-emerald-600 bg-emerald-50/50 font-semibold' : 'text-gray-500 hover:text-emerald-600 hover:bg-gray-50' }}">
                            <i class="fas fa-calendar-alt w-4 text-center {{ request()->routeIs('admin.subscription-plans.*') ? 'text-emerald-500' : 'text-gray-300' }}"></i>
                            <span>Paket Langganan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.subscription-features.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.subscription-features.*') ? 'text-emerald-600 bg-emerald-50/50 font-semibold' : 'text-gray-500 hover:text-emerald-600 hover:bg-gray-50' }}">
                            <i class="fas fa-star w-4 text-center {{ request()->routeIs('admin.subscription-features.*') ? 'text-emerald-500' : 'text-gray-300' }}"></i>
                            <span>Fitur Tier</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Pelanggan & Transaksi (Collapsible) -->
            <li x-data="{ open: {{ request()->routeIs('admin.subscription-trial-requests.*') || request()->routeIs('admin.subscription-users.*') || request()->routeIs('admin.subscription-payments.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.subscription-trial-requests.*') || request()->routeIs('admin.subscription-users.*') || request()->routeIs('admin.subscription-payments.*') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm shadow-emerald-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-users-rectangle w-5 text-center text-lg {{ request()->routeIs('admin.subscription-trial-requests.*') || request()->routeIs('admin.subscription-users.*') || request()->routeIs('admin.subscription-payments.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                        <span class="text-sm">Pelanggan</span>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($pendingTrialCount > 0)
                            <span class="inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold leading-none text-white bg-red-500 rounded-full">{{ $pendingTrialCount }}</span>
                        @endif
                        <i class="fas fa-chevron-down text-[10px] transition-transform duration-300" :class="open ? 'rotate-180 text-emerald-600' : ''"></i>
                    </div>
                </button>
                <ul x-show="open"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    style="display: none;"
                    class="mt-1 ml-4 space-y-1 border-l border-gray-100 pl-4 py-2">
                    <li>
                        <a href="{{ route('admin.subscription-trial-requests.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.subscription-trial-requests.*') ? 'text-emerald-600 bg-emerald-50/50 font-semibold' : 'text-gray-500 hover:text-emerald-600 hover:bg-gray-50' }}">
                            <i class="fas fa-clock w-4 text-center {{ request()->routeIs('admin.subscription-trial-requests.*') ? 'text-emerald-500' : 'text-gray-300' }}"></i>
                            <span>Permintaan Trial</span>
                            @if($pendingTrialCount > 0)
                                <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold leading-none text-white bg-red-400 rounded-full">{{ $pendingTrialCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.subscription-users.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.subscription-users.*') ? 'text-emerald-600 bg-emerald-50/50 font-semibold' : 'text-gray-500 hover:text-emerald-600 hover:bg-gray-50' }}">
                            <i class="fas fa-users w-4 text-center {{ request()->routeIs('admin.subscription-users.*') ? 'text-emerald-500' : 'text-gray-300' }}"></i>
                            <span>Daftar Pelanggan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.subscription-payments.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.subscription-payments.*') ? 'text-emerald-600 bg-emerald-50/50 font-semibold' : 'text-gray-500 hover:text-emerald-600 hover:bg-gray-50' }}">
                            <i class="fas fa-receipt w-4 text-center {{ request()->routeIs('admin.subscription-payments.*') ? 'text-emerald-500' : 'text-gray-300' }}"></i>
                            <span>Transaksi</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Pengaturan Langganan -->
            <li>
                <a href="{{ route('admin.subscription-settings.edit') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.subscription-settings.*') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm shadow-emerald-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-sliders-h w-5 text-center text-lg {{ request()->routeIs('admin.subscription-settings.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Pengaturan Langganan</span>
                </a>
            </li>

            {{-- ═══════════════════════════════════ --}}
            {{-- SECTION: KONTEN --}}
            {{-- ═══════════════════════════════════ --}}
            <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2 mt-6">Konten</p>

            <!-- Iklan & Banner -->
            <li>
                <a href="{{ route('admin.advertisements.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.advertisements.*') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm shadow-emerald-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-bullhorn w-5 text-center text-lg {{ request()->routeIs('admin.advertisements.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Iklan & Banner</span>
                </a>
            </li>

            <!-- Landing Pages -->
            <li>
                <a href="{{ route('admin.landing-pages.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.landing-pages.*') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm shadow-emerald-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-globe w-5 text-center text-lg {{ request()->routeIs('admin.landing-pages.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Landing Pages</span>
                </a>
            </li>

            <!-- Konten Pendukung (Collapsible) -->
            <li x-data="{ open: {{ request()->routeIs('admin.task-statuses.*') || request()->routeIs('admin.task-labels.*') || request()->routeIs('admin.testimonials.*') || request()->routeIs('admin.faqs.*') || request()->routeIs('admin.terms.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.task-statuses.*') || request()->routeIs('admin.task-labels.*') || request()->routeIs('admin.testimonials.*') || request()->routeIs('admin.faqs.*') || request()->routeIs('admin.terms.*') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm shadow-emerald-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-file-lines w-5 text-center text-lg {{ request()->routeIs('admin.task-statuses.*') || request()->routeIs('admin.task-labels.*') || request()->routeIs('admin.testimonials.*') || request()->routeIs('admin.faqs.*') || request()->routeIs('admin.terms.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                        <span class="text-sm">Konten Pendukung</span>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] transition-transform duration-300" :class="open ? 'rotate-180 text-emerald-600' : ''"></i>
                </button>
                <ul x-show="open"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    style="display: none;"
                    class="mt-1 ml-4 space-y-1 border-l border-gray-100 pl-4 py-2">
                    <li>
                        <a href="{{ route('admin.task-statuses.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.task-statuses.*') ? 'text-emerald-600 bg-emerald-50/50 font-semibold' : 'text-gray-500 hover:text-emerald-600 hover:bg-gray-50' }}">
                            <i class="fas fa-list-check w-4 text-center {{ request()->routeIs('admin.task-statuses.*') ? 'text-emerald-500' : 'text-gray-300' }}"></i>
                            <span>Status Tugas</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.task-labels.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.task-labels.*') ? 'text-emerald-600 bg-emerald-50/50 font-semibold' : 'text-gray-500 hover:text-emerald-600 hover:bg-gray-50' }}">
                            <i class="fas fa-tag w-4 text-center {{ request()->routeIs('admin.task-labels.*') ? 'text-emerald-500' : 'text-gray-300' }}"></i>
                            <span>Label Tugas</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.testimonials.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.testimonials.*') ? 'text-emerald-600 bg-emerald-50/50 font-semibold' : 'text-gray-500 hover:text-emerald-600 hover:bg-gray-50' }}">
                            <i class="fas fa-quote-left w-4 text-center {{ request()->routeIs('admin.testimonials.*') ? 'text-emerald-500' : 'text-gray-300' }}"></i>
                            <span>Testimonial</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.faqs.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.faqs.*') ? 'text-emerald-600 bg-emerald-50/50 font-semibold' : 'text-gray-500 hover:text-emerald-600 hover:bg-gray-50' }}">
                            <i class="fas fa-question-circle w-4 text-center {{ request()->routeIs('admin.faqs.*') ? 'text-emerald-500' : 'text-gray-300' }}"></i>
                            <span>FAQ</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.terms.edit') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.terms.*') ? 'text-emerald-600 bg-emerald-50/50 font-semibold' : 'text-gray-500 hover:text-emerald-600 hover:bg-gray-50' }}">
                            <i class="fas fa-file-contract w-4 text-center {{ request()->routeIs('admin.terms.*') ? 'text-emerald-500' : 'text-gray-300' }}"></i>
                            <span>Syarat & Ketentuan</span>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- ═══════════════════════════════════ --}}
            {{-- SECTION: KEAMANAN & SISTEM --}}
            {{-- ═══════════════════════════════════ --}}
            <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2 mt-6">Keamanan & Sistem</p>

            <!-- Riwayat Login -->
            <li>
                <a href="{{ route('admin.security.login-histories.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.security.login-histories.*') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm shadow-emerald-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-clock-rotate-left w-5 text-center text-lg {{ request()->routeIs('admin.security.login-histories.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Riwayat Login</span>
                </a>
            </li>

            <!-- IP Terblokir -->
            <li>
                @php $bannedCount = \App\Models\BannedIp::count(); @endphp
                <a href="{{ route('admin.security.banned-ips.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.security.banned-ips.*') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm shadow-emerald-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-shield-halved w-5 text-center text-lg {{ request()->routeIs('admin.security.banned-ips.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">IP Terblokir</span>
                    @if($bannedCount > 0)
                        <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold leading-none text-white bg-red-500 rounded-full">{{ $bannedCount }}</span>
                    @endif
                </a>
            </li>

            <!-- Log & Monitoring (Collapsible) -->
            <li x-data="{ open: {{ request()->routeIs('admin.security.activity-logs.*') || request()->routeIs('admin.security.error-logs.*') || request()->routeIs('admin.security.queue.*') || request()->routeIs('admin.cpu-monitoring.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.security.activity-logs.*') || request()->routeIs('admin.security.error-logs.*') || request()->routeIs('admin.security.queue.*') || request()->routeIs('admin.cpu-monitoring.*') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm shadow-emerald-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-chart-line w-5 text-center text-lg {{ request()->routeIs('admin.security.activity-logs.*') || request()->routeIs('admin.security.error-logs.*') || request()->routeIs('admin.security.queue.*') || request()->routeIs('admin.cpu-monitoring.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                        <span class="text-sm">Log & Monitoring</span>
                    </div>
                    <div class="flex items-center gap-2">
                        @php $failedQueueCount = \Illuminate\Support\Facades\DB::table('failed_jobs')->count(); @endphp
                        @if($failedQueueCount > 0)
                            <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[9px] font-bold leading-none text-white bg-red-500 rounded-full">{{ $failedQueueCount }}</span>
                        @endif
                        <i class="fas fa-chevron-down text-[10px] transition-transform duration-300" :class="open ? 'rotate-180 text-emerald-600' : ''"></i>
                    </div>
                </button>
                <ul x-show="open"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    style="display: none;"
                    class="mt-1 ml-4 space-y-1 border-l border-gray-100 pl-4 py-2">
                    <li>
                        <a href="{{ route('admin.security.activity-logs.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.security.activity-logs.*') ? 'text-emerald-600 bg-emerald-50/50 font-semibold' : 'text-gray-500 hover:text-emerald-600 hover:bg-gray-50' }}">
                            <i class="fas fa-file-waveform w-4 text-center {{ request()->routeIs('admin.security.activity-logs.*') ? 'text-emerald-500' : 'text-gray-300' }}"></i>
                            <span>Activity Log</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.security.error-logs.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.security.error-logs.*') ? 'text-emerald-600 bg-emerald-50/50 font-semibold' : 'text-gray-500 hover:text-emerald-600 hover:bg-gray-50' }}">
                            <i class="fas fa-bug w-4 text-center {{ request()->routeIs('admin.security.error-logs.*') ? 'text-emerald-500' : 'text-gray-300' }}"></i>
                            <span>Error Log</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.security.queue.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.security.queue.*') ? 'text-emerald-600 bg-emerald-50/50 font-semibold' : 'text-gray-500 hover:text-emerald-600 hover:bg-gray-50' }}">
                            <i class="fas fa-network-wired w-4 text-center {{ request()->routeIs('admin.security.queue.*') ? 'text-emerald-500' : 'text-gray-300' }}"></i>
                            <span>Queue Monitor</span>
                            @if($failedQueueCount > 0)
                                <span class="ml-auto inline-flex items-center justify-center px-1.5 py-0.5 text-[9px] font-bold leading-none text-white bg-red-500 rounded-full">{{ $failedQueueCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.cpu-monitoring.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.cpu-monitoring.*') ? 'text-emerald-600 bg-emerald-50/50 font-semibold' : 'text-gray-500 hover:text-emerald-600 hover:bg-gray-50' }}">
                            <i class="fas fa-microchip w-4 text-center {{ request()->routeIs('admin.cpu-monitoring.*') ? 'text-emerald-500' : 'text-gray-300' }}"></i>
                            <span>CPU Monitoring</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Maintenance (Collapsible) -->
            <li x-data="{ open: {{ request()->routeIs('admin.maintenance.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.maintenance.*') ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm shadow-emerald-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-screwdriver-wrench w-5 text-center text-lg {{ request()->routeIs('admin.maintenance.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                        <span class="text-sm">Maintenance</span>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] transition-transform duration-300" :class="open ? 'rotate-180 text-emerald-600' : ''"></i>
                </button>
                <ul x-show="open"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    style="display: none;"
                    class="mt-1 ml-4 space-y-1 border-l border-gray-100 pl-4 py-2">
                    <li>
                        <a href="{{ route('admin.maintenance.index') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.maintenance.index') ? 'text-emerald-600 bg-emerald-50/50 font-semibold' : 'text-gray-500 hover:text-emerald-600 hover:bg-gray-50' }}">
                            <i class="fas fa-users-viewfinder w-4 text-center {{ request()->routeIs('admin.maintenance.index') ? 'text-emerald-500' : 'text-gray-300' }}"></i>
                            <span>Online Users</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.maintenance.broadcast') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.maintenance.broadcast') ? 'text-emerald-600 bg-emerald-50/50 font-semibold' : 'text-gray-500 hover:text-emerald-600 hover:bg-gray-50' }}">
                            <i class="fas fa-paper-plane w-4 text-center {{ request()->routeIs('admin.maintenance.broadcast') ? 'text-emerald-500' : 'text-gray-300' }}"></i>
                            <span>Kirim Pengumuman</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.maintenance.history') }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.maintenance.history') ? 'text-emerald-600 bg-emerald-50/50 font-semibold' : 'text-gray-500 hover:text-emerald-600 hover:bg-gray-50' }}">
                            <i class="fas fa-history w-4 text-center {{ request()->routeIs('admin.maintenance.history') ? 'text-emerald-500' : 'text-gray-300' }}"></i>
                            <span>Riwayat</span>
                        </a>
                    </li>
                </ul>
            </li>

        </ul>
    </nav>
</aside>
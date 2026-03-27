@php
    $user = auth()->user();
    $authServices = app(\App\Services\FeatureAccessService::class);
    $hasMultiOutlet = $authServices->checkAccess($user, 'multi_outlet')['can_access'];
    
    $outletUrl = route('outlets.index');
    if (!$hasMultiOutlet) {
        $singleOutlet = $user->hasRole('owner') ? $user->outletsOwned()->first() : $user->outlet;
        if ($singleOutlet) {
            $outletUrl = route('outlets.show', $singleOutlet->id);
        }
    }

    $isReseller = $user->email
        ? \App\Models\Customer::where('email', $user->email)->where('type', 'reseller')->exists()
        : false;

    $userOutlets = $user->isOwner()
        ? $user->outletsOwned->where('is_active', true)->sortBy('name')
        : collect([$user->outlet])->filter(fn($o) => $o && $o->is_active);
    $hasMultipleOutlets = $userOutlets->count() > 1;
@endphp

{{--
  Phosphor Icons (ph-light) — pastikan sudah ada di <head>:
  <script src="https://unpkg.com/@phosphor-icons/web@2.1.1"></script>
--}}

<div class="flex flex-col h-full bg-white">

    <!-- ── Header ── -->
    <div class="h-16 flex items-center mb-2 border-b border-gray-50 flex-shrink-0 relative"
         :class="sidebarCollapsed ? 'px-0 justify-center' : 'px-6'"
         x-data="{ switcherOpen: false }">

        @if($user->outlet_id && $user->outlet)
            <div class="flex items-center group cursor-pointer overflow-hidden"
                 :class="sidebarCollapsed ? 'justify-center w-12' : 'gap-3 w-full'"
                 @click="switcherOpen = !switcherOpen">
                <div class="flex-shrink-0 p-2 bg-emerald-50 rounded-xl group-hover:bg-emerald-100 transition-colors">
                    @if($user->outlet->logo)
                        <img src="{{ Storage::url($user->outlet->logo) }}" alt="{{ $user->outlet->name }}" class="h-6 w-6 object-contain">
                    @else
                        <div class="h-6 w-6 rounded bg-gradient-to-br from-cuan-olive to-cuan-green flex items-center justify-center text-white font-bold text-[10px]">
                            {{ substr($user->outlet->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="min-w-0 pr-4" x-show="!sidebarCollapsed">
                    <span class="block text-gray-900 font-bold text-sm truncate">{{ $user->outlet->name }}</span>
                    <div class="flex items-center gap-1">
                        <span class="block text-emerald-600 text-[9px] font-bold uppercase tracking-wider">User Panel</span>
                        @if($hasMultipleOutlets)
                            <i class="ph-light ph-caret-down text-[10px] text-gray-400"></i>
                        @endif
                    </div>
                </div>
            </div>

            @if($hasMultipleOutlets)
            <div x-show="switcherOpen"
                 @click.away="switcherOpen = false"
                 class="absolute bg-white rounded-xl shadow-2xl border border-gray-100 py-2 z-[9999] ring-1 ring-black/10 transition-all duration-100"
                 :class="sidebarCollapsed ? 'left-16 top-2 w-64' : 'left-3 right-3 top-14 w-[calc(100%-1.5rem)]'"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 style="display:none;">
                <div class="px-4 py-2 border-b border-gray-50">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Pilih Outlet</p>
                </div>
                <div class="max-h-60 overflow-y-auto custom-scrollbar">
                    @foreach($userOutlets as $outlet)
                        <form method="POST" action="{{ route('change.outlet') }}">
                            @csrf
                            <input type="hidden" name="outlet_id" value="{{ $outlet->id }}">
                            <button type="submit" class="w-full text-left px-4 py-3 hover:bg-emerald-50 transition-colors flex items-center gap-3 {{ $user->outlet_id == $outlet->id ? 'bg-emerald-50/50' : '' }}">
                                @if($outlet->logo)
                                    <img src="{{ Storage::url($outlet->logo) }}" class="h-6 w-6 object-contain rounded">
                                @else
                                    <div class="h-6 w-6 rounded bg-gray-100 flex items-center justify-center text-gray-400 text-[10px] font-bold">
                                        {{ substr($outlet->name, 0, 1) }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-gray-900 truncate">{{ $outlet->name }}</p>
                                    <p class="text-[9px] text-gray-500 truncate">{{ $outlet->business_category }}</p>
                                </div>
                                @if($user->outlet_id == $outlet->id)
                                    <i class="ph-light ph-check text-emerald-500 text-sm ml-auto"></i>
                                @endif
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
            @endif
        @else
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                <div class="p-2 bg-emerald-50 rounded-xl group-hover:bg-emerald-100 transition-colors">
                    <img src="{{ asset('assets/image/logo.svg') }}" alt="CuanFlow" class="h-8 w-8">
                </div>
                <div x-show="!sidebarCollapsed">
                    <span class="text-gray-900 font-bold text-base tracking-tight">CuanFlow</span>
                </div>
            </a>
        @endif
    </div>

    <!-- ── Navigation ── -->
    <nav class="flex-1 overflow-y-auto custom-scrollbar overflow-x-hidden py-4">
        <ul class="space-y-1 px-3">

            {{-- Dashboard --}}
            <li>
                <a href="{{ route('dashboard') }}"
                   title="Dashboard"
                   class="flex items-center rounded-xl group {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- House Simple: lebih ringan dari fa-house --}}
                    <i class="ph-light ph-house-simple w-5 text-center text-lg {{ request()->routeIs('dashboard') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Menu Utama</span>
                </a>
            </li>

            <!-- ══ OPERASIONAL UTAMA ══ -->
            <p x-show="!sidebarCollapsed" class="px-4 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] mb-2 mt-4 whitespace-nowrap">Operasional Utama</p>
            <div x-show="sidebarCollapsed" class="h-4"></div>

            @canAccessFeature('pos')
            @can('akses pos')
            <li>
                <a href="{{ route('pos.index') }}"
                   title="Point of Sale"
                   class="flex items-center rounded-xl group {{ request()->routeIs('pos.*', 'cash-register.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Storefront --}}
                    <i class="ph-light ph-storefront w-5 text-center text-lg {{ request()->routeIs('pos.*', 'cash-register.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Point of Sale</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @if($isReseller)
            @canAccessFeature('reseller_products')
            @can('lihat produk reseller')
            <li>
                <a href="{{ route('reseller-products.index') }}"
                   title="Produk Reseller"
                   class="flex items-center rounded-xl group {{ request()->routeIs('reseller-products.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Package --}}
                    <i class="ph-light ph-package w-5 text-center text-lg {{ request()->routeIs('reseller-products.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Produk Reseller</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature
            @endif

            @canAccessFeature('sales_management')
            @can('lihat penjualan')
            <li>
                <a href="{{ route('sales.index') }}"
                   title="Penjualan"
                   class="flex items-center rounded-xl group {{ request()->routeIs('sales.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Receipt --}}
                    <i class="ph-light ph-receipt w-5 text-center text-lg {{ request()->routeIs('sales.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Penjualan</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('discount_management')
            @can('lihat diskon')
            <li>
                <a href="{{ route('discounts.index') }}"
                   title="Diskon"
                   class="flex items-center rounded-xl group {{ request()->routeIs('discounts.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Ticket --}}
                    <i class="ph-light ph-ticket w-5 text-center text-lg {{ request()->routeIs('discounts.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Diskon</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('finance_management')
            @can('lihat keuangan')
            <li>
                <a href="{{ route('finance.index') }}"
                   title="Keuangan"
                   class="flex items-center rounded-xl group {{ request()->routeIs('finance.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Trend Up --}}
                    <i class="ph-light ph-trend-up w-5 text-center text-lg {{ request()->routeIs('finance.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Keuangan</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('other_income')
            @can('buat pemasukan')
            <li>
                <a href="{{ route('expenses.index', ['type' => 'income']) }}"
                   title="Pemasukan Lain"
                   class="flex items-center rounded-xl group {{ (request()->routeIs('expenses.*') && request('type') == 'income') || (request()->fullUrl() == route('expenses.index', ['type' => 'income'])) ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Coins --}}
                    <i class="ph-light ph-coins w-5 text-center text-lg {{ (request()->routeIs('expenses.*') && request('type') == 'income') || (request()->fullUrl() == route('expenses.index', ['type' => 'income'])) ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Pemasukan Lain</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('operational_costs')
            @can('buat pengeluaran')
            <li>
                <a href="{{ route('expenses.index', ['type' => 'expense']) }}"
                   title="Biaya Ops"
                   class="flex items-center rounded-xl group {{ (request()->routeIs('expenses.*') && request('type') == 'expense') || (request()->fullUrl() == route('expenses.index', ['type' => 'expense'])) ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Money Wavy --}}
                    <i class="ph-light ph-money-wavy w-5 text-center text-lg {{ (request()->routeIs('expenses.*') && request('type') == 'expense') || (request()->fullUrl() == route('expenses.index', ['type' => 'expense'])) ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Biaya Ops</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('balance_withdrawal')
            @can('buat penarikan')
            <li>
                <a href="{{ route('withdraw.index') }}"
                   title="Penarikan Saldo"
                   class="flex items-center rounded-xl group {{ request()->routeIs('withdraw.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Bank --}}
                    <i class="ph-light ph-bank w-5 text-center text-lg {{ request()->routeIs('withdraw.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Penarikan Saldo</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('invoice_list')
            @can('lihat invoice')
            <li>
                <a href="{{ route('invoices.index') }}"
                   title="Daftar Invoice"
                   class="flex items-center rounded-xl group {{ request()->routeIs('invoices.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Invoice --}}
                    <i class="ph-light ph-invoice w-5 text-center text-lg {{ request()->routeIs('invoices.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Daftar Invoice</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('payment_methods')
            @can('lihat metode pembayaran')
            <li>
                <a href="{{ route('outlet-payment-links.index') }}"
                   title="Metode Pembayaran"
                   class="flex items-center rounded-xl group {{ request()->routeIs('outlet-payment-links.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Scan --}}
                    <i class="ph-light ph-scan w-5 text-center text-lg {{ request()->routeIs('outlet-payment-links.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Metode Pembayaran</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('task_management')
            @can('tasks.view')
            <li>
                <a href="{{ route('tasks.index') }}"
                   title="Manajemen Tugas"
                   class="flex items-center rounded-xl group {{ request()->routeIs('tasks.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Clipboard Text --}}
                    <i class="ph-light ph-clipboard-text w-5 text-center text-lg {{ request()->routeIs('tasks.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Manajemen Tugas</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            <!-- ══ MONITORING ══ -->
            <p x-show="!sidebarCollapsed" class="px-4 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] mb-2 mt-6 whitespace-nowrap">Monitoring</p>
            <div x-show="sidebarCollapsed" class="h-4"></div>

            @canAccessFeature('dashboard')
            @can('lihat statistik')
            <li>
                <a href="{{ route('statistics.index') }}"
                   title="Dashboard & Statistik"
                   class="flex items-center rounded-xl group {{ request()->routeIs('statistics.index') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Chart Bar --}}
                    <i class="ph-light ph-chart-bar w-5 text-center text-lg {{ request()->routeIs('statistics.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Dashboard & Statistik</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('reports')
            @can('lihat laporan')
            <li>
                <a href="{{ route('reports.index') }}"
                   title="Laporan"
                   class="flex items-center rounded-xl group {{ request()->routeIs('reports.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- File Magnifying Glass --}}
                    <i class="ph-light ph-file-magnifying-glass w-5 text-center text-lg {{ request()->routeIs('reports.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Laporan</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            <!-- ══ PRODUK & STOK ══ -->
            <p x-show="!sidebarCollapsed" class="px-4 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] mb-2 mt-6 whitespace-nowrap">Produk & Stok</p>
            <div x-show="sidebarCollapsed" class="h-4"></div>

            @canAccessFeature('products_recipes')
            @can('lihat produk')
            <li>
                <a href="{{ route('products-hpp.index') }}"
                   title="Produk & Resep"
                   class="flex items-center rounded-xl group {{ request()->routeIs('products-hpp.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Bowl Steam --}}
                    <i class="ph-light ph-bowl-steam w-5 text-center text-lg {{ request()->routeIs('products-hpp.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Produk & Resep</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('raw_materials')
            @can('lihat bahan baku')
            <li>
                <a href="{{ route('raw-materials.index') }}"
                   title="Bahan Baku"
                   class="flex items-center rounded-xl group {{ (request()->routeIs('raw-materials.*') && !request()->routeIs('raw-materials.suppliers*')) ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Warehouse --}}
                    <i class="ph-light ph-warehouse w-5 text-center text-lg {{ (request()->routeIs('raw-materials.*') && !request()->routeIs('raw-materials.suppliers*')) ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Bahan Baku</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('suppliers')
            @can('lihat supplier')
            <li>
                <a href="{{ route('raw-materials.suppliers') }}"
                   title="Pemasok"
                   class="flex items-center rounded-xl group {{ request()->routeIs('raw-materials.suppliers*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Truck --}}
                    <i class="ph-light ph-truck w-5 text-center text-lg {{ request()->routeIs('raw-materials.suppliers*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Pemasok</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('reseller_app')
            @can('lihat reseller applications')
            <li>
                <a href="{{ route('reseller-applications.index') }}"
                   title="Lamaran Reseller"
                   class="flex items-center rounded-xl group {{ request()->routeIs('reseller-applications.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Users Three --}}
                    <i class="ph-light ph-users-three w-5 text-center text-lg {{ request()->routeIs('reseller-applications.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Lamaran Reseller</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('production')
            @can('lihat produksi')
            <li>
                <a href="{{ route('production.index') }}"
                   title="Produksi"
                   class="flex items-center rounded-xl group {{ request()->routeIs('production.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Factory --}}
                    <i class="ph-light ph-factory w-5 text-center text-lg {{ request()->routeIs('production.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Produksi</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('stock_opname')
            @can('lihat stock opname')
            <li>
                <a href="{{ route('stock-opname.index') }}"
                   title="Stock Opname"
                   class="flex items-center rounded-xl group {{ request()->routeIs('stock-opname.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Clipboard List --}}
                    <i class="ph-light ph-list-checks w-5 text-center text-lg {{ request()->routeIs('stock-opname.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Stock Opname</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('stock_transfer')
            @can('lihat stock transfer')
            <li>
                <a href="{{ route('stock-transfers.index') }}"
                   title="Transfer Stok"
                   class="flex items-center rounded-xl group {{ request()->routeIs('stock-transfers.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Arrows Left Right --}}
                    <i class="ph-light ph-arrows-left-right w-5 text-center text-lg {{ request()->routeIs('stock-transfers.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Transfer Stok</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            <!-- ══ ANALISIS AI ══ -->
            <p x-show="!sidebarCollapsed" class="px-4 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] mb-2 mt-6 whitespace-nowrap">Analisis AI</p>
            <div x-show="sidebarCollapsed" class="h-4"></div>

            @canAccessFeature('ai_insights')
            @can('lihat ai insights')
            <li>
                <a href="{{ route('ai-insights.index') }}"
                   title="Insight"
                   class="flex items-center rounded-xl group {{ request()->routeIs('ai-insights.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Sparkle --}}
                    <i class="ph-light ph-sparkle w-5 text-center text-lg {{ request()->routeIs('ai-insights.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Insight</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('clara_ai')
            @can('akses clara ai')
            <li>
                <a href="{{ route('clara-ai.index') }}"
                   title="Clara AI"
                   class="flex items-center rounded-xl group {{ request()->routeIs('clara-ai.index', 'clara-ai.chat', 'clara-ai.new-session') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Brain: AI/kecerdasan buatan --}}
                    <i class="ph-light ph-brain w-5 text-center text-lg {{ request()->routeIs('clara-ai.index', 'clara-ai.chat', 'clara-ai.new-session') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Clara AI</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('opportunity_map')
            @can('akses peta cuan')
            <li>
                <a href="{{ route('opportunity-map.index') }}"
                   title="Peta Cuan"
                   class="flex items-center rounded-xl group {{ request()->routeIs('opportunity-map.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Map Trifold --}}
                    <i class="ph-light ph-map-trifold w-5 text-center text-lg {{ request()->routeIs('opportunity-map.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Peta Cuan</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('clara_ai')
            @can('akses clara ai')
            @canAccessFeature('video_ai')
            @can('akses video ai')
            <li>
                <a href="{{ route('clara-ai.video-prompt') }}"
                   title="Video AI"
                   class="flex items-center rounded-xl group {{ request()->routeIs('clara-ai.video-prompt') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Video Camera --}}
                    <i class="ph-light ph-video-camera w-5 text-center text-lg {{ request()->routeIs('clara-ai.video-prompt') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Video AI</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('script_ai')
            @can('akses script ai')
            <li>
                <a href="{{ route('clara-ai.affiliate-script') }}"
                   title="Script AI"
                   class="flex items-center rounded-xl group {{ request()->routeIs('clara-ai.affiliate-script') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Microphone Stage --}}
                    <i class="ph-light ph-microphone-stage w-5 text-center text-lg {{ request()->routeIs('clara-ai.affiliate-script') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Script AI</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('image_ai')
            @can('akses image ai')
            <li>
                <a href="{{ route('clara-ai.ads-image-prompt') }}"
                   title="Image AI"
                   class="flex items-center rounded-xl group {{ request()->routeIs('clara-ai.ads-image-prompt') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Image Square --}}
                    <i class="ph-light ph-image-square w-5 text-center text-lg {{ request()->routeIs('clara-ai.ads-image-prompt') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Image AI</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('kalkulaba_ai')
            @can('akses kalkulaba ai')
            <li>
                <a href="{{ route('clara-ai.kalkulaba') }}"
                   title="Kalkulaba AI"
                   class="flex items-center rounded-xl group {{ request()->routeIs('clara-ai.kalkulaba') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Percent --}}
                    <i class="ph-light ph-percent w-5 text-center text-lg {{ request()->routeIs('clara-ai.kalkulaba') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Kalkulaba AI</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature
            @endcan
            @endcanAccessFeature

            <!-- ══ SISTEM & BANTUAN ══ -->
            <p x-show="!sidebarCollapsed" class="px-4 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] mb-2 mt-6 whitespace-nowrap">Sistem & Bantuan</p>
            <div x-show="sidebarCollapsed" class="h-4"></div>

            @can('lihat outlet')
            <li>
                <a href="{{ $outletUrl }}"
                   title="Outlet"
                   class="flex items-center rounded-xl group {{ request()->url() == $outletUrl || request()->routeIs('outlets.edit') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Buildings --}}
                    <i class="ph-light ph-buildings w-5 text-center text-lg {{ request()->url() == $outletUrl || request()->routeIs('outlets.edit') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Outlet</span>
                </a>
            </li>
            @endcan

            @canAccessFeature('landing_page')
            @can('lihat landing page')
            <li>
                <a href="{{ route('landing-pages.index') }}"
                   title="Landing Page"
                   class="flex items-center rounded-xl group {{ request()->routeIs('landing-pages.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Globe Simple --}}
                    <i class="ph-light ph-globe-simple w-5 text-center text-lg {{ request()->routeIs('landing-pages.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Landing Page</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('testimonials')
            @can('lihat testimoni')
            <li>
                <a href="{{ route('testimonials.index') }}"
                   title="Testimoni"
                   class="flex items-center rounded-xl group {{ request()->routeIs('testimonials.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Star Half --}}
                    <i class="ph-light ph-star-half w-5 text-center text-lg {{ request()->routeIs('testimonials.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Testimoni</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('employee_management')
            @can('lihat pegawai')
            <li>
                <a href="{{ route('employees.index') }}"
                   title="Pegawai"
                   class="flex items-center rounded-xl group {{ request()->routeIs('employees.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Identification Card --}}
                    <i class="ph-light ph-identification-card w-5 text-center text-lg {{ request()->routeIs('employees.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Pegawai</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('customer_management')
            @can('lihat pelanggan')
            <li>
                <a href="{{ route('customer-debts.index') }}"
                   title="Pelanggan"
                   class="flex items-center rounded-xl group {{ request()->routeIs('customer-debts.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Hand Coins --}}
                    <i class="ph-light ph-hand-coins w-5 text-center text-lg {{ request()->routeIs('customer-debts.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Pelanggan</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('table_management')
            @can('lihat meja')
            <li>
                <a href="{{ route('tables.index') }}"
                   title="Meja"
                   class="flex items-center rounded-xl group {{ request()->routeIs('tables.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Table --}}
                    <i class="ph-light ph-table w-5 text-center text-lg {{ request()->routeIs('tables.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Meja</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('outlet_policies')
            @can('lihat kebijakan outlet')
            <li>
                <a href="{{ route('outlet-policies.index') }}"
                   title="Kebijakan"
                   class="flex items-center rounded-xl group {{ request()->routeIs('outlet-policies.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Notepad --}}
                    <i class="ph-light ph-notepad w-5 text-center text-lg {{ request()->routeIs('outlet-policies.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Kebijakan</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            <li>
                <a href="{{ route('stock-notifications.index') }}"
                   title="Notifikasi"
                   class="flex items-center rounded-xl group {{ request()->routeIs('stock-notifications.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Bell Ringing --}}
                    <div class="relative w-5 flex justify-center">
                        <i class="ph-light ph-bell-ringing text-lg {{ request()->routeIs('stock-notifications.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                        @if(isset($unreadStockCount) && $unreadStockCount > 0)
                            <span class="absolute -top-1 -right-0.5 h-2.5 w-2.5 bg-red-500 border-2 border-white rounded-full"></span>
                        @endif
                    </div>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Notifikasi</span>
                    @if(isset($unreadStockCount) && $unreadStockCount > 0)
                        <span x-show="!sidebarCollapsed" class="ml-auto bg-red-100 text-red-600 px-2 py-0.5 rounded-full text-[10px] font-bold">
                            {{ $unreadStockCount }}
                        </span>
                    @endif
                </a>
            </li>

            <li>
                <a href="{{ route('profile.edit') }}"
                   title="Akun"
                   class="flex items-center rounded-xl group {{ request()->routeIs('profile.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Shield Check --}}
                    <i class="ph-light ph-shield-check w-5 text-center text-lg {{ request()->routeIs('profile.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Akun</span>
                </a>
            </li>

            @canAccessFeature('help_faq')
            @can('lihat faq')
            <li>
                <a href="{{ route('faqs.index') }}"
                   title="FAQ"
                   class="flex items-center rounded-xl group {{ request()->routeIs('faqs.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Lifebuoy --}}
                    <i class="ph-light ph-lifebuoy w-5 text-center text-lg {{ request()->routeIs('faqs.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">FAQ</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @if(auth()->user()->hasRole('owner') && auth()->user()->subscription)
            <li>
                <a href="{{ route('subscription.manage') }}"
                   title="Kelola Langganan"
                   class="flex items-center rounded-xl group {{ request()->routeIs('subscription.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    {{-- Diamond --}}
                    <i class="ph-light ph-diamond w-5 text-center text-lg {{ request()->routeIs('subscription.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Kelola Langganan</span>
                </a>
            </li>
            @endif

        </ul>
    </nav>

</div>
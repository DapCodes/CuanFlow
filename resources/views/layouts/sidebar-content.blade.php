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

    // Fetch user outlets for switcher
    $userOutlets = $user->isOwner()
        ? $user->outletsOwned->where('is_active', true)->sortBy('name')
        : collect([$user->outlet])->filter(fn($o) => $o && $o->is_active);
    $hasMultipleOutlets = $userOutlets->count() > 1;
@endphp

<div class="flex flex-col h-full bg-white">
    <!-- Sidebar Header (Logo/Outlet & Dropdown) -->
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
                            <i class="fa-solid fa-chevron-down text-[8px] text-gray-400"></i>
                        @endif
                    </div>
                </div>
            </div>

            @if($hasMultipleOutlets)
            <!-- Switcher Dropdown (Adjusted for collapse & z-index) -->
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
                                    <i class="fa-solid fa-check text-emerald-500 text-[10px] ml-auto"></i>
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

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto custom-scrollbar overflow-x-hidden py-4">
        <ul class="space-y-1 px-3">
            <!-- SEKSI OPERASIONAL -->
            <p x-show="!sidebarCollapsed" class="px-4 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] mb-2 mt-4 whitespace-nowrap">Operasional Utama</p>
            <div x-show="sidebarCollapsed" class="h-4"></div>

            @canAccessFeature('pos')
            @can('akses pos')
            <li>
                <a href="{{ route('pos.index') }}" 
                   title="Point of Sale"
                   class="flex items-center rounded-xl group {{ request()->routeIs('pos.index') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    <i class="fa-solid fa-cash-register w-5 text-center text-base {{ request()->routeIs('pos.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Point of Sale</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @if($isReseller)
            <li>
                <a href="{{ route('reseller-products.index') }}" 
                   title="Produk Reseller"
                   class="flex items-center rounded-xl group {{ request()->routeIs('reseller-products.index') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    <i class="fa-solid fa-boxes-packing w-5 text-center text-base {{ request()->routeIs('reseller-products.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Produk Reseller</span>
                </a>
            </li>
            @endif

            @canAccessFeature('sales_management')
            @can('lihat penjualan')
            <li>
                <a href="{{ route('sales.index') }}" 
                   title="Penjualan"
                   class="flex items-center rounded-xl group {{ request()->routeIs('sales.index') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    <i class="fa-solid fa-cart-shopping w-5 text-center text-base {{ request()->routeIs('sales.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
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
                   class="flex items-center rounded-xl group {{ request()->routeIs('discounts.index') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    <i class="fa-solid fa-tags w-5 text-center text-base {{ request()->routeIs('discounts.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
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
                   class="flex items-center rounded-xl group {{ request()->routeIs('finance.index') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    <i class="fa-solid fa-wallet w-5 text-center text-base {{ request()->routeIs('finance.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
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
                   class="flex items-center rounded-xl group {{ request()->fullUrl() == route('expenses.index', ['type' => 'income']) ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    <i class="fa-solid fa-arrow-trend-up w-5 text-center text-base {{ request()->fullUrl() == route('expenses.index', ['type' => 'income']) ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
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
                   class="flex items-center rounded-xl group {{ request()->fullUrl() == route('expenses.index', ['type' => 'expense']) ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    <i class="fa-solid fa-receipt w-5 text-center text-base {{ request()->fullUrl() == route('expenses.index', ['type' => 'expense']) ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
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
                    <i class="fa-solid fa-hand-holding-dollar w-5 text-center text-base {{ request()->routeIs('withdraw.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
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
                   class="flex items-center rounded-xl group {{ request()->routeIs('invoices.index') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    <i class="fa-solid fa-file-lines w-5 text-center text-base {{ request()->routeIs('invoices.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
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
                   class="flex items-center rounded-xl group {{ request()->routeIs('outlet-payment-links.index') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    <i class="fa-solid fa-qrcode w-5 text-center text-base {{ request()->routeIs('outlet-payment-links.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
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
                    <i class="fa-solid fa-list-check w-5 text-center text-base {{ request()->routeIs('tasks.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Manajemen Tugas</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            <!-- SEKSI MONITORING -->
            <p x-show="!sidebarCollapsed" class="px-4 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] mb-2 mt-6 whitespace-nowrap">Monitoring</p>
            <div x-show="sidebarCollapsed" class="h-4"></div>

            @canAccessFeature('dashboard')
            @can('lihat statistik')
            <li>
                <a href="{{ route('statistics.index') }}" 
                   title="Dashboard & Statistik"
                   class="flex items-center rounded-xl group {{ request()->routeIs('statistics.index') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    <i class="fa-solid fa-chart-line w-5 text-center text-base {{ request()->routeIs('statistics.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
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
                   class="flex items-center rounded-xl group {{ request()->routeIs('reports.index') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    <i class="fa-solid fa-file-invoice w-5 text-center text-base {{ request()->routeIs('reports.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Laporan</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            <!-- PRODUK & STOK -->
            <p x-show="!sidebarCollapsed" class="px-4 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] mb-2 mt-6 whitespace-nowrap">Produk & Stok</p>
            <div x-show="sidebarCollapsed" class="h-4"></div>

            @canAccessFeature('products_recipes')
            @can('lihat produk')
            <li>
                <a href="{{ route('products-hpp.index') }}" 
                   title="Produk & Resep"
                   class="flex items-center rounded-xl group {{ request()->routeIs('products-hpp.*') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    <i class="fa-solid fa-utensils w-5 text-center text-base {{ request()->routeIs('products-hpp.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
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
                   class="flex items-center rounded-xl group {{ request()->routeIs('raw-materials.index') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    <i class="fa-solid fa-boxes-stacked w-5 text-center text-base {{ request()->routeIs('raw-materials.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
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
                   class="flex items-center rounded-xl group {{ request()->routeIs('raw-materials.suppliers') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    <i class="fa-solid fa-truck-field w-5 text-center text-base {{ request()->routeIs('raw-materials.suppliers') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
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
                    <i class="fa-solid fa-handshake w-5 text-center text-base {{ request()->routeIs('reseller-applications.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
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
                   class="flex items-center rounded-xl group {{ request()->routeIs('production.index') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    <i class="fa-solid fa-flask w-5 text-center text-base {{ request()->routeIs('production.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
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
                   class="flex items-center rounded-xl group {{ request()->routeIs('stock-opname.index') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    <i class="fa-solid fa-boxes-packing w-5 text-center text-base {{ request()->routeIs('stock-opname.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
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
                    <i class="fa-solid fa-truck-fast w-5 text-center text-base {{ request()->routeIs('stock-transfers.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Transfer Stok</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            <!-- AI & ANALISIS -->
            <p x-show="!sidebarCollapsed" class="px-4 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] mb-2 mt-6 whitespace-nowrap">Analisis AI</p>
            <div x-show="sidebarCollapsed" class="h-4"></div>

            @canAccessFeature('ai_insights')
            @can('lihat ai insights')
            <li>
                <a href="{{ route('ai-insights.index') }}" 
                   title="Insight"
                   class="flex items-center rounded-xl group {{ request()->routeIs('ai-insights.index') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    <i class="fa-solid fa-lightbulb w-5 text-center text-base {{ request()->routeIs('ai-insights.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
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
                   class="flex items-center rounded-xl group {{ request()->routeIs('clara-ai.index') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    <i class="fa-solid fa-robot w-5 text-center text-base {{ request()->routeIs('clara-ai.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Clara AI</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('ai_insights')
            <li>
                <a href="{{ route('opportunity-map.index') }}" 
                   title="Peta Cuan"
                   class="flex items-center rounded-xl group {{ request()->routeIs('opportunity-map.index') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    <i class="fa-solid fa-map-location-dot w-5 text-center text-base {{ request()->routeIs('opportunity-map.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Peta Cuan</span>
                </a>
            </li>
            @endcanAccessFeature

            @canAccessFeature('clara_ai')
            @can('akses clara ai')
            <li>
                <a href="{{ route('clara-ai.video-prompt') }}" 
                   title="Video AI"
                   class="flex items-center rounded-xl group {{ request()->routeIs('clara-ai.video-prompt') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    <i class="fa-solid fa-film w-5 text-center text-base {{ request()->routeIs('clara-ai.video-prompt') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Video AI</span>
                </a>
            </li>
            <li>
                <a href="{{ route('clara-ai.affiliate-script') }}" 
                   title="Script AI"
                   class="flex items-center rounded-xl group {{ request()->routeIs('clara-ai.affiliate-script') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    <i class="fa-solid fa-scroll w-5 text-center text-base {{ request()->routeIs('clara-ai.affiliate-script') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Script AI</span>
                </a>
            </li>
            <li>
                <a href="{{ route('clara-ai.ads-image-prompt') }}" 
                   title="Image AI"
                   class="flex items-center rounded-xl group {{ request()->routeIs('clara-ai.ads-image-prompt') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    <i class="fa-solid fa-image w-5 text-center text-base {{ request()->routeIs('clara-ai.ads-image-prompt') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Image AI</span>
                </a>
            </li>
            <li>
                <a href="{{ route('clara-ai.kalkulaba') }}" 
                   title="Kalkulaba AI"
                   class="flex items-center rounded-xl group {{ request()->routeIs('clara-ai.kalkulaba') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    <i class="fa-solid fa-calculator w-5 text-center text-base {{ request()->routeIs('clara-ai.kalkulaba') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Kalkulaba AI</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            <!-- PENGATURAN -->
            <p x-show="!sidebarCollapsed" class="px-4 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] mb-2 mt-6 whitespace-nowrap">Sistem & Bantuan</p>
            <div x-show="sidebarCollapsed" class="h-4"></div>

            @can('lihat outlet')
            <li>
                <a href="{{ $outletUrl }}" 
                   title="Outlet"
                   class="flex items-center rounded-xl group {{ request()->url() == $outletUrl ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    <i class="fa-solid fa-store w-5 text-center text-base {{ request()->url() == $outletUrl ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
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
                    <i class="fa-solid fa-rocket w-5 text-center text-base {{ request()->routeIs('landing-pages.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
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
                    <i class="fa-solid fa-quote-left w-5 text-center text-base {{ request()->routeIs('testimonials.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
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
                    <i class="fa-solid fa-users w-5 text-center text-base {{ request()->routeIs('employees.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
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
                    <i class="fa-solid fa-address-book w-5 text-center text-base {{ request()->routeIs('customer-debts.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
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
                    <i class="fa-solid fa-chair w-5 text-center text-base {{ request()->routeIs('tables.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
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
                   class="flex items-center rounded-xl group {{ request()->routeIs('outlet-policies.index') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    <i class="fa-solid fa-clipboard-list w-5 text-center text-base {{ request()->routeIs('outlet-policies.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Kebijakan</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            <li>
                <a href="{{ route('profile.edit') }}" 
                   title="Akun"
                   class="flex items-center rounded-xl group {{ request()->routeIs('profile.edit') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    <i class="fa-solid fa-user-gear w-5 text-center text-base {{ request()->routeIs('profile.edit') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">Akun</span>
                </a>
            </li>

            @canAccessFeature('help_faq')
            @can('lihat faq')
            <li>
                <a href="{{ route('faqs.index') }}" 
                   title="FAQ"
                   class="flex items-center rounded-xl group {{ request()->routeIs('faqs.index') ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}"
                   :class="sidebarCollapsed ? 'justify-center p-2.5' : 'px-4 py-2.5 gap-3'">
                    <i class="fa-solid fa-circle-question w-5 text-center text-base {{ request()->routeIs('faqs.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span x-show="!sidebarCollapsed" class="text-sm whitespace-nowrap">FAQ</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature
        </ul>
    </nav>
</div>

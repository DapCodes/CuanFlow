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
@endphp

<div class="flex flex-col h-full bg-white">
    <!-- Sidebar Header (Logo/Outlet) -->
    <div class="h-20 flex items-center px-6 mb-2 border-b border-gray-50 flex-shrink-0">
        @if($user->outlet_id && $user->outlet)
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group overflow-hidden">
                <div class="flex-shrink-0 p-2 bg-emerald-50 rounded-xl group-hover:bg-emerald-100 transition-colors">
                    @if($user->outlet->logo)
                        <img src="{{ Storage::url($user->outlet->logo) }}" alt="{{ $user->outlet->name }}" class="h-8 w-8 object-contain">
                    @else
                        <div class="h-8 w-8 rounded bg-gradient-to-br from-cuan-olive to-cuan-green flex items-center justify-center text-white font-bold text-sm">
                            {{ substr($user->outlet->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="min-w-0">
                    <span class="block text-gray-900 font-bold text-sm truncate">{{ $user->outlet->name }}</span>
                    <span class="block text-emerald-600 text-[9px] font-bold uppercase tracking-wider">User Panel</span>
                </div>
            </a>
        @else
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                <div class="p-2 bg-emerald-50 rounded-xl group-hover:bg-emerald-100 transition-colors">
                    <img src="{{ asset('assets/image/logo.svg') }}" alt="CuanFlow" class="h-8 w-8">
                </div>
                <div>
                    <span class="text-gray-900 font-bold text-base tracking-tight">CuanFlow</span>
                </div>
            </a>
        @endif
        
        <button @click="sidebarOpen = false" class="lg:hidden ml-auto text-gray-400 hover:text-emerald-600">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-4 overflow-y-auto custom-scrollbar">
        <ul class="space-y-1">
            <!-- SEKSI OPERASIONAL -->
            <p class="px-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 mt-2">Operasional</p>
            
            @canAccessFeature('pos')
            @can('akses pos')
            <li>
                <a href="{{ route('pos.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all group {{ request()->routeIs('pos.index') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-cash-register w-5 text-center text-base {{ request()->routeIs('pos.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Point of Sale</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @if($isReseller)
            <li>
                <a href="{{ route('reseller-products.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all group {{ request()->routeIs('reseller-products.index') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-boxes-packing w-5 text-center text-base {{ request()->routeIs('reseller-products.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Produk Reseller</span>
                </a>
            </li>
            @endif

            @canAccessFeature('sales_management')
            @can('lihat penjualan')
            <li>
                <a href="{{ route('sales.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all group {{ request()->routeIs('sales.index') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-cart-shopping w-5 text-center text-base {{ request()->routeIs('sales.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Penjualan</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('discount_management')
            @can('lihat diskon')
            <li>
                <a href="{{ route('discounts.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all group {{ request()->routeIs('discounts.index') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-tags w-5 text-center text-base {{ request()->routeIs('discounts.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Diskon</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('finance_management')
            @can('lihat keuangan')
            <li>
                <a href="{{ route('finance.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all group {{ request()->routeIs('finance.index') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-wallet w-5 text-center text-base {{ request()->routeIs('finance.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Keuangan</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('task_management')
            @can('tasks.view')
            <li>
                <a href="{{ route('tasks.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all group {{ request()->routeIs('tasks.index') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-list-check w-5 text-center text-base {{ request()->routeIs('tasks.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Manajemen Tugas</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            <!-- SEKSI PRODUK & STOK -->
            <p class="px-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 mt-6">Produk & Stok</p>

            @canAccessFeature('products_recipes')
            @can('lihat produk')
            <li>
                <a href="{{ route('products-hpp.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all group {{ request()->routeIs('products-hpp.*') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-utensils w-5 text-center text-base {{ request()->routeIs('products-hpp.*') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Produk & Resep</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('raw_materials')
            @can('lihat bahan baku')
            <li>
                <a href="{{ route('raw-materials.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all group {{ request()->routeIs('raw-materials.index') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-boxes-stacked w-5 text-center text-base {{ request()->routeIs('raw-materials.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Bahan Baku</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('stock_opname')
            @can('lihat stock opname')
            <li>
                <a href="{{ route('stock-opname.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all group {{ request()->routeIs('stock-opname.index') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-boxes-packing w-5 text-center text-base {{ request()->routeIs('stock-opname.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Stock Opname</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            <!-- SEKSI ANALISIS & AI -->
            <p class="px-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 mt-6">Analisis & AI</p>

            @canAccessFeature('dashboard')
            @can('lihat statistik')
            <li>
                <a href="{{ route('statistics.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all group {{ request()->routeIs('statistics.index') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-chart-line w-5 text-center text-base {{ request()->routeIs('statistics.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Statistik</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('ai_insights')
            @can('lihat ai insights')
            <li>
                <a href="{{ route('ai-insights.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all group {{ request()->routeIs('ai-insights.index') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-lightbulb w-5 text-center text-base {{ request()->routeIs('ai-insights.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Insight</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('clara_ai')
            @can('akses clara ai')
            <li>
                <a href="{{ route('clara-ai.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all group {{ request()->routeIs('clara-ai.index') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="w-5 h-5 flex items-center justify-center">
                        <img src="{{ asset('assets/image/clara-ai.png') }}" class="w-4 h-4 object-contain {{ request()->routeIs('clara-ai.index') ? '' : 'grayscale opacity-60' }}">
                    </div>
                    <span class="text-sm">Clara AI</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            <!-- SEKSI BISNIS & SISTEM -->
            <p class="px-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 mt-6">Bisnis & Sistem</p>

            @can('lihat outlet')
            <li>
                <a href="{{ $outletUrl }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all group {{ request()->url() == $outletUrl ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-store w-5 text-center text-base {{ request()->url() == $outletUrl ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Informasi Outlet</span>
                </a>
            </li>
            @endcan

            @canAccessFeature('landing_page')
            @can('lihat landing page')
            <li>
                <a href="{{ route('landing-pages.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all group {{ request()->routeIs('landing-pages.index') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-rocket w-5 text-center text-base {{ request()->routeIs('landing-pages.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Landing Page</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('employee_management')
            @can('lihat pegawai')
            <li>
                <a href="{{ route('employees.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all group {{ request()->routeIs('employees.index') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-users w-5 text-center text-base {{ request()->routeIs('employees.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Pegawai</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('customer_management')
            @can('lihat pelanggan')
            <li>
                <a href="{{ route('customer-debts.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all group {{ request()->routeIs('customer-debts.index') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-address-book w-5 text-center text-base {{ request()->routeIs('customer-debts.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Pelanggan</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            @canAccessFeature('outlet_policies')
            @can('lihat kebijakan outlet')
            <li>
                <a href="{{ route('outlet-policies.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all group {{ request()->routeIs('outlet-policies.index') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-clipboard-list w-5 text-center text-base {{ request()->routeIs('outlet-policies.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Kebijakan Outlet</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature

            <li>
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all group {{ request()->routeIs('profile.edit') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-user-gear w-5 text-center text-base {{ request()->routeIs('profile.edit') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Pengaturan Akun</span>
                </a>
            </li>

            @canAccessFeature('help_faq')
            @can('lihat faq')
            <li>
                <a href="{{ route('faqs.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all group {{ request()->routeIs('faqs.index') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-circle-question w-5 text-center text-base {{ request()->routeIs('faqs.index') ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500' }}"></i>
                    <span class="text-sm">Bantuan & FAQ</span>
                </a>
            </li>
            @endcan
            @endcanAccessFeature
        </ul>
    </nav>
</div>

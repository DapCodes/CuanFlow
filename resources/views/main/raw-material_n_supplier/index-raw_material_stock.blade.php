@extends('layouts.app')

@section('title', 'Stok Bahan Baku - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Stok Bahan Baku</span>
</li>
@endsection

@push('styles')
<style>
    .animate-pulse-slow {
        animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: .7; }
    }
</style>
@endpush

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">
        
        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Stok & Inventaris
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Monitor ketersediaan bahan baku, produk siap jual, dan manajemen supplier Anda.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @can('lihat supplier')
                <a href="{{ route('raw-materials.suppliers') }}" 
                   class="inline-flex items-center gap-2 rounded-xl bg-white border border-gray-200 px-5 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all shadow-sm active:scale-95">
                    <span>Kelola Supplier</span>
                </a>
                @endcan
                @can('buat bahan baku')
                <a href="{{ $tab === 'instant_product' ? route('products-hpp.create') : route('raw-materials.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-5 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                    <span>Tambah {{ $tab === 'instant_product' ? 'Produk' : 'Bahan Baku' }}</span>
                </a>
                @endcan
            </div>
        </section>

        {{-- TAB NAVIGATION --}}
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button type="button" onclick="switchTab('raw_material')"
                   class="whitespace-nowrap py-4 px-1 border-b-2 font-black text-xs uppercase tracking-widest transition-all {{ $tab === 'raw_material' ? 'border-cuan-green text-cuan-green' : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300' }}">
                    Bahan Baku
                </button>
                <button type="button" onclick="switchTab('instant_product')"
                   class="whitespace-nowrap py-4 px-1 border-b-2 font-black text-xs uppercase tracking-widest transition-all {{ $tab === 'instant_product' ? 'border-cuan-green text-cuan-green' : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300' }}">
                    Produk Instant
                </button>
            </nav>
        </div>

        {{-- RINGKASAN STATISTIK --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total {{ $tab === 'instant_product' ? 'Produk' : 'Bahan' }}</p>
                <div class="flex items-center justify-between mt-2">
                    <p class="text-2xl font-black text-gray-900">{{ number_format($stats['total'], 0, ',', '.') }}</p>
                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg border border-emerald-100 uppercase">{{ $stats['active'] }} Aktif</span>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Peringatan Stok</p>
                <div class="flex items-center gap-4 mt-2">
                    <div>
                        <p class="text-2xl font-black text-red-600">{{ number_format($stats['out'], 0, ',', '.') }}</p>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Habis</p>
                    </div>
                    <div class="w-px h-8 bg-gray-100"></div>
                    <div>
                        <p class="text-2xl font-black text-orange-500">{{ number_format($stats['low'], 0, ',', '.') }}</p>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Menipis</p>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Kadaluarsa</p>
                <p class="mt-2 text-2xl font-black text-red-600 animate-pulse-slow">{{ number_format($stats['expired'], 0, ',', '.') }} <span class="text-xs text-gray-400 uppercase">Items</span></p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Segera Kadaluarsa</p>
                <p class="mt-2 text-2xl font-black text-yellow-600">{{ number_format($stats['expiring'], 0, ',', '.') }} <span class="text-xs text-gray-400 uppercase">Items</span></p>
            </div>
        </section>

        {{-- KONTEN UTAMA --}}
        <x-card-container>
            {{-- Toolbar: Search & Filter --}}
            <div class="px-6 py-5 border-b border-gray-100 bg-white grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-1 relative">
                    <input type="text" id="searchInput" value="{{ request('search') }}" placeholder="Cari nama atau kode..."
                           class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all font-bold shadow-sm">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                </div>

                <div class="flex flex-wrap md:col-span-3 gap-3">
                    <select id="categoryFilter"
                            class="flex-1 min-w-[150px] rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all bg-white font-bold shadow-sm">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>

                    <select id="stockFilter"
                            class="flex-1 min-w-[150px] rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all bg-white font-bold shadow-sm">
                        <option value="">Status Stok (Semua)</option>
                        <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Stok Menipis</option>
                        <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Stok Habis</option>
                        <option value="expired" {{ request('stock_status') == 'expired' ? 'selected' : '' }}>Kadaluarsa</option>
                        <option value="expiring" {{ request('stock_status') == 'expiring' ? 'selected' : '' }}>Segera Kadaluarsa</option>
                    </select>

                    <select id="supplierFilter" {{ $tab === 'instant_product' ? 'disabled' : '' }}
                            class="flex-1 min-w-[150px] rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all bg-white font-bold shadow-sm {{ $tab === 'instant_product' ? 'bg-gray-50 opacity-50 cursor-not-allowed' : '' }}">
                        <option value="">Semua Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Table Container for AJAX --}}
            <div id="material-table-container">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left">Kode</th>
                                <th class="px-6 py-4 text-left">{{ $tab === 'instant_product' ? 'Produk' : 'Bahan Baku' }}</th>
                                <th class="px-6 py-4 text-left">{{ $tab === 'instant_product' ? 'Kategori' : 'Supplier' }}</th>
                                <th class="px-6 py-4 text-left">Ketersediaan Stok</th>
                                <th class="px-6 py-4 text-left">Harga {{ $tab === 'instant_product' ? 'HPP' : 'Beli' }}</th>
                                <th class="px-6 py-4 text-left">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @if($tab === 'raw_material')
                                @forelse($rawMaterials as $material)
                                    @php
                                        $stock = $material->stocks->first();
                                        $currentStock = $stock ? $stock->quantity : 0;
                                        $isOutOfStock = $currentStock <= 0;
                                        $isLowStock = $currentStock <= $material->min_stock && !$isOutOfStock;
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors material-row {{ !$material->is_active ? 'opacity-60 bg-gray-50/30' : '' }}">
                                        
                                        <td class="px-6 py-5 whitespace-nowrap">
                                            <span class="text-[10px] font-black font-mono text-gray-500 bg-gray-100 px-2 py-1 rounded-lg border border-gray-100 uppercase tracking-tighter">
                                                {{ $material->code }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center border-2 border-white shadow-sm overflow-hidden flex-shrink-0">
                                                    @if($material->image)
                                                        <img src="{{ Storage::url($material->image) }}" class="w-full h-full object-cover">
                                                    @else
                                                        <i class="fas fa-cube text-gray-300"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-900 leading-tight">{{ $material->name }}</div>
                                                    @if($material->category)
                                                        <div class="text-[9px] font-black text-gray-400 mt-1 uppercase tracking-widest">{{ $material->category->name }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-5">
                                            @if($material->supplier)
                                                <div class="text-[11px] font-bold text-gray-900">{{ $material->supplier->name }}</div>
                                            @else
                                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest italic">Umum</span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-5">
                                            <div class="flex flex-col gap-1.5 min-w-[140px]">
                                                {{-- Safe Stock --}}
                                                <div class="flex items-center justify-between text-[11px] font-black">
                                                    <span class="text-gray-400 uppercase tracking-tighter">Aman</span>
                                                    <span class="text-emerald-600 px-2 py-0.5 rounded-lg bg-emerald-50 border border-emerald-100">
                                                        {{ number_format($material->total_valid_qty, 2) }} {{ $material->unit->abbreviation }}
                                                    </span>
                                                </div>

                                                {{-- Warnings --}}
                                                @if($material->total_expired_qty > 0)
                                                    <div class="flex items-center justify-between text-[10px] font-black text-red-600 bg-red-50 border border-red-100 px-2 py-1 rounded-lg animate-pulse-slow">
                                                        <span class="uppercase">Kadaluarsa</span>
                                                        <span>{{ number_format($material->total_expired_qty, 2) }}</span>
                                                    </div>
                                                @endif

                                                @if($material->total_expiring_qty > 0)
                                                    <div class="flex items-center justify-between text-[10px] font-black text-yellow-600 bg-yellow-50 border border-yellow-100 px-2 py-1 rounded-lg">
                                                        <span class="uppercase">Segera Kadal.</span>
                                                        <span>{{ number_format($material->total_expiring_qty, 2) }}</span>
                                                    </div>
                                                @endif

                                                @if($isOutOfStock)
                                                    <span class="w-full py-1 text-center bg-red-600 text-white text-[9px] font-black uppercase tracking-widest rounded-lg">Stok Habis</span>
                                                @elseif($isLowStock)
                                                    <span class="w-full py-1 text-center bg-orange-100 text-orange-600 text-[9px] font-black uppercase tracking-widest rounded-lg border border-orange-200">Stok Menipis</span>
                                                @endif
                                            </div>
                                        </td>

                                        <td class="px-6 py-5 text-[11px] font-black text-gray-900 whitespace-nowrap">
                                            Rp {{ number_format($material->purchase_price, 0, ',', '.') }}
                                        </td>

                                        <td class="px-6 py-5 whitespace-nowrap">
                                            @if($material->is_active)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-cuan-green/10 text-cuan-green border border-cuan-green/10">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-50 text-gray-400 border border-gray-200">
                                                    Nonaktif
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-5 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                @can('kelola stok bahan baku')
                                                <a href="{{ route('raw-materials.manage-stock', $material) }}"
                                                   class="w-9 h-9 flex items-center justify-center rounded-xl bg-orange-50 text-orange-500 hover:bg-orange-500 hover:text-white transition-all active:scale-95 border border-orange-100"
                                                   title="Kelola Stok">
                                                    <i class="fas fa-dolly text-xs"></i>
                                                </a>
                                                @endcan

                                                @can('lihat riwayat stok bahan baku')
                                                <a href="{{ route('raw-materials.stock-history', $material) }}"
                                                   class="w-9 h-9 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white transition-all active:scale-95 border border-blue-100"
                                                   title="Riwayat">
                                                    <i class="fas fa-history text-xs"></i>
                                                </a>
                                                @endcan
                                                
                                                @can('edit bahan baku')
                                                <a href="{{ route('raw-materials.edit', $material) }}"
                                                   class="w-9 h-9 flex items-center justify-center rounded-xl bg-cuan-green/10 text-cuan-green hover:bg-cuan-green hover:text-white transition-all active:scale-95 border border-cuan-green/10"
                                                   title="Edit">
                                                    <i class="fas fa-edit text-xs"></i>
                                                </a>
                                                @endcan

                                                @can('lihat detail bahan baku')
                                                <a href="{{ route('raw-materials.stock-show', $material) }}"
                                                   class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-500 hover:bg-gray-900 hover:text-white transition-all active:scale-95 border border-gray-200"
                                                   title="Detail">
                                                    <i class="fas fa-chart-bar text-xs"></i>
                                                </a>
                                                @endcan
                                                
                                                @can('hapus bahan baku')
                                                <button type="button" onclick="confirmDelete('{{ $material->id }}', '{{ $material->name }}')"
                                                        class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all active:scale-95 border border-red-100"
                                                        title="Hapus">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                                <form id="delete-form-{{ $material->id }}" action="{{ route('raw-materials.destroy', $material) }}" method="POST" class="hidden">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-20 text-center">
                                            <div class="w-16 h-16 bg-gray-50 border border-gray-100 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-200">
                                                <i class="fas fa-box-open text-2xl"></i>
                                            </div>
                                            <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">Belum Ada Bahan Baku</h3>
                                            <p class="text-[11px] text-gray-500 font-bold uppercase tracking-widest mt-2 max-w-xs mx-auto">Mulai tambahkan bahan baku untuk memonitor stok outlet Anda.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            @else
                                @forelse($products as $product)
                                    @php
                                        $stock = $product->stocks->first();
                                        $currentStock = $stock ? $stock->quantity : 0;
                                        $isOutOfStock = $currentStock <= 0;
                                        $isLowStock = $currentStock <= $product->min_stock && !$isOutOfStock;
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors product-row {{ !$product->is_active ? 'opacity-60 bg-gray-50/30' : '' }}">
                                        
                                        <td class="px-6 py-5 whitespace-nowrap">
                                            <span class="text-[10px] font-black font-mono text-gray-500 bg-gray-100 px-2 py-1 rounded-lg border border-gray-100 uppercase tracking-tighter">
                                                {{ $product->code }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center border-2 border-white shadow-sm overflow-hidden flex-shrink-0">
                                                    @if($product->image)
                                                        <img src="{{ Storage::url($product->image) }}" class="w-full h-full object-cover">
                                                    @else
                                                        <i class="fas fa-cube text-gray-300"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-900 leading-tight">{{ $product->name }}</div>
                                                    @if($product->unit)
                                                        <div class="text-[9px] font-black text-gray-400 mt-1 uppercase tracking-widest">{{ $product->unit->name }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-5">
                                            @if($product->category)
                                                <div class="text-[11px] font-bold text-gray-900">{{ $product->category->name }}</div>
                                            @else
                                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest italic">-</span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-5">
                                            <div class="flex flex-col gap-1.5 min-w-[140px]">
                                                <div class="flex items-center justify-between text-[11px] font-black">
                                                    <span class="text-gray-400 uppercase tracking-tighter">Total Stok</span>
                                                    <span class="{{ $isOutOfStock ? 'text-red-600' : 'text-emerald-600' }} px-2 py-0.5 rounded-lg bg-emerald-50 border border-emerald-100">
                                                        {{ number_format($currentStock, 2) }} {{ $product->unit->abbreviation ?? 'Unit' }}
                                                    </span>
                                                </div>

                                                @if($isOutOfStock)
                                                    <span class="w-full py-1 text-center bg-red-600 text-white text-[9px] font-black uppercase tracking-widest rounded-lg">Stok Habis</span>
                                                @elseif($isLowStock)
                                                    <span class="w-full py-1 text-center bg-orange-100 text-orange-600 text-[9px] font-black uppercase tracking-widest rounded-lg border border-orange-200">Stok Menipis</span>
                                                @endif
                                            </div>
                                        </td>

                                        <td class="px-6 py-5 text-[11px] font-black text-gray-900 whitespace-nowrap">
                                            Rp {{ number_format($product->hpp, 0, ',', '.') }}
                                        </td>

                                        <td class="px-6 py-5 whitespace-nowrap">
                                            @if($product->is_active)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-cuan-green/10 text-cuan-green border border-cuan-green/10">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-50 text-gray-400 border border-gray-200">
                                                    Nonaktif
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-5 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                @can('lihat stok produksi')
                                                <a href="{{ route('production.stock.show', $product->id) }}"
                                                   class="w-9 h-9 flex items-center justify-center rounded-xl bg-orange-50 text-orange-500 hover:bg-orange-500 hover:text-white transition-all active:scale-95 border border-orange-100"
                                                   title="Kelola Stok">
                                                    <i class="fas fa-dolly text-xs"></i>
                                                </a>
                                                @endcan
                                                
                                                @can('edit produk')
                                                <a href="{{ route('products-hpp.edit', $product->id) }}"
                                                   class="w-9 h-9 flex items-center justify-center rounded-xl bg-cuan-green/10 text-cuan-green hover:bg-cuan-green hover:text-white transition-all active:scale-95 border border-cuan-green/10"
                                                   title="Edit">
                                                    <i class="fas fa-edit text-xs"></i>
                                                </a>
                                                @endcan

                                                @can('lihat detail produk')
                                                <a href="{{ route('products-hpp.show', $product->id) }}"
                                                   class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-500 hover:bg-gray-900 hover:text-white transition-all active:scale-95 border border-gray-200"
                                                   title="Detail">
                                                    <i class="fas fa-chart-bar text-xs"></i>
                                                </a>
                                                @endcan
                                                
                                                @can('hapus produk')
                                                <button type="button" onclick="confirmDeleteProduct('{{ $product->id }}', '{{ $product->name }}')"
                                                        class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all active:scale-95 border border-red-100"
                                                        title="Hapus">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                                <form id="delete-form-product-{{ $product->id }}" action="{{ route('products-hpp.destroy', $product->id) }}" method="POST" class="hidden">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-20 text-center">
                                            <div class="w-16 h-16 bg-gray-50 border border-gray-100 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-200">
                                                <i class="fas fa-shopping-bag text-2xl"></i>
                                            </div>
                                            <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">Belum Ada Produk Instant</h3>
                                            <p class="text-[11px] text-gray-500 font-bold uppercase tracking-widest mt-2 max-w-xs mx-auto">Produk instant adalah produk yang tidak memerlukan resep dan memiliki stok sendiri.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @php $collection = ($tab === 'raw_material') ? $rawMaterials : $products; @endphp
                @if($collection->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $collection->appends(['tab' => $tab])->links() }}
                    </div>
                @endif
            </div>
        </x-card-container>

    </div>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    {{-- Session Notifications --}}
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 3000,
            iconColor: '#658C58',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black text-gray-900',
            }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: "{{ session('error') }}",
            confirmButtonColor: '#ef4444',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black text-gray-900',
            }
        });
    @endif

    {{-- AJAX Filter Logic --}}
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const stockFilter = document.getElementById('stockFilter');
    const supplierFilter = document.getElementById('supplierFilter');
    let timeout = null;

    function refreshTable() {
        const url = new URL(window.location.href);
        const search = searchInput.value;
        const category = categoryFilter.value;
        const stock = stockFilter.value;
        const supplier = supplierFilter ? supplierFilter.value : '';
        const currentTab = '{{ $tab }}';

        if (search) url.searchParams.set('search', search); else url.searchParams.delete('search');
        if (category) url.searchParams.set('category_id', category); else url.searchParams.delete('category_id');
        if (stock) url.searchParams.set('stock_status', stock); else url.searchParams.delete('stock_status');
        if (supplier) url.searchParams.set('supplier_id', supplier); else url.searchParams.delete('supplier_id');
        url.searchParams.set('tab', currentTab);

        const target = document.getElementById('material-table-container');
        target.style.opacity = '0.5';

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContent = doc.getElementById('material-table-container');
            if (newContent) {
                target.innerHTML = newContent.innerHTML;
                window.history.replaceState({}, '', url);
            }
        })
        .finally(() => { target.style.opacity = '1'; });
    }

    window.switchTab = function(tabId) {
        const url = new URL(window.location.href);
        url.searchParams.set('tab', tabId);
        url.searchParams.delete('page'); // Reset pagination on tab switch
        
        // We can either do a full page reload or an AJAX update. 
        // Let's do a full reload for simplicity to ensure all variables ($tab, $stats) are properly updated from the controller.
        window.location.href = url.toString();
    }

    [searchInput, categoryFilter, stockFilter, supplierFilter].forEach(el => {
        if (!el) return;
        el.addEventListener(el.id === 'searchInput' ? 'input' : 'change', () => {
            clearTimeout(timeout);
            timeout = setTimeout(refreshTable, el.id === 'searchInput' ? 500 : 0);
        });
    });

    {{-- Delete Confirmation --}}
    window.confirmDelete = function(id, name) {
        Swal.fire({
            title: 'Hapus Bahan Baku?',
            text: `Apakah Anda yakin ingin menghapus "${name}"? Tindakan ini permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-[2rem] border-none shadow-2xl',
                title: 'font-black text-gray-900',
                confirmButton: 'rounded-xl px-6 py-3 font-bold text-sm',
                cancelButton: 'rounded-xl px-6 py-3 font-bold text-sm'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    window.confirmDeleteProduct = function(id, name) {
        Swal.fire({
            title: 'Hapus Produk?',
            text: `Apakah Anda yakin ingin menghapus "${name}"? Tindakan ini permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-[2rem] border-none shadow-2xl',
                title: 'font-black text-gray-900',
                confirmButton: 'rounded-xl px-6 py-3 font-bold text-sm',
                cancelButton: 'rounded-xl px-6 py-3 font-bold text-sm'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-product-' + id).submit();
            }
        });
    }

    // Handle pagination clicks for AJAX
    document.addEventListener('click', function(e) {
        const link = e.target.closest('.pagination a');
        if (link && document.getElementById('material-table-container').contains(link)) {
            e.preventDefault();
            const url = new URL(link.href);
            const target = document.getElementById('material-table-container');
            target.style.opacity = '0.5';
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.getElementById('material-table-container');
                if (newContent) {
                    target.innerHTML = newContent.innerHTML;
                    window.history.pushState({}, '', url);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            })
            .finally(() => { target.style.opacity = '1'; });
        }
    });
});
</script>
@endpush
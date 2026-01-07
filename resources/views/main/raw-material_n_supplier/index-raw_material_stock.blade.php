@extends('layouts.app')

@section('title', 'Stok Bahan Baku - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Stok Bahan Baku</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">
        
        @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 flex items-start gap-3 text-sm">
            <i class="fas fa-check-circle mt-0.5 text-green-500"></i>
            <p class="text-green-800">{{ session('success') }}</p>
        </div>
        @endif

        @if(session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 flex items-start gap-3 text-sm">
            <i class="fas fa-exclamation-circle mt-0.5 text-red-500"></i>
            <p class="text-red-800">{{ session('error') }}</p>
        </div>
        @endif

        {{-- Header Section --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-500 border border-red-100">
                        <i class="fas fa-boxes text-sm"></i>
                    </span>
                    <span>Stok Bahan Baku</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola stok, monitor ketersediaan, dan atur supplier bahan baku Anda.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('raw-materials.suppliers') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-sm transition-all">
                    <i class="fas fa-truck mr-2"></i>
                    Kelola Supplier
                </a>
                <a href="{{ route('raw-materials.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-sm transition-all">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Tambah Bahan Baku
                </a>
            </div>
        </section>

        {{-- Stats Overview --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Bahan Baku</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $rawMaterials->total() }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100">
                        <i class="fas fa-box text-gray-400 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Stok Menipis</p>
                        <p class="mt-1 text-2xl font-bold text-yellow-600">
                             {{ $rawMaterials->filter(function($m) {
                                $stock = $m->stocks->first();
                                $currentStock = $stock ? $stock->quantity : 0;
                                return $currentStock <= $m->min_stock && $currentStock > 0;
                            })->count() }}
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-yellow-50 flex items-center justify-center border border-yellow-100">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Stok Habis</p>
                        <p class="mt-1 text-2xl font-bold text-red-600">
                            {{ $rawMaterials->filter(function($m) {
                                $stock = $m->stocks->first();
                                return ($stock ? $stock->quantity : 0) <= 0;
                            })->count() }}
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center border border-red-100">
                        <i class="fas fa-times-circle text-red-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Bahan Aktif</p>
                        <p class="mt-1 text-2xl font-bold text-emerald-600">{{ $rawMaterials->where('is_active', true)->count() }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100">
                        <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            {{-- Filter & Search --}}
            <div class="p-5 border-b border-gray-200 space-y-4">
                <form method="GET" action="{{ route('raw-materials.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    {{-- Search --}}
                    <div class="md:col-span-4 relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Cari nama, kode, atau barcode..." 
                               class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all shadow-sm">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    </div>
                    
                    {{-- Filters --}}
                    <div class="md:col-span-2">
                         <select name="category_id" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 shadow-sm text-gray-600">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <select name="stock_status" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 shadow-sm text-gray-600">
                            <option value="">Semua Stok</option>
                            <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Menipis</option>
                            <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Habis</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <select name="supplier_id" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 shadow-sm text-gray-600">
                            <option value="">Semua Supplier</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2 flex gap-2">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 shadow-sm transition-all">
                            <i class="fas fa-search mr-2"></i>Cari
                        </button>
                        <a href="{{ route('raw-materials.index') }}" class="inline-flex items-center justify-center px-3 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all" title="Reset Filter">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-200 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3 w-16">Kode</th>
                            <th class="px-6 py-3">Bahan Baku</th>
                            <th class="px-6 py-3">Supplier</th>
                            <th class="px-6 py-3">Stok Tersedia</th>
                            <th class="px-6 py-3">Harga Beli</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($rawMaterials as $material)
                        @php
                            $stock = $material->stocks->first();
                            $currentStock = $stock ? $stock->quantity : 0;
                            $isLowStock = $currentStock <= $material->min_stock;
                            $isOutOfStock = $currentStock <= 0;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap align-top">
                                <span class="text-xs font-mono font-semibold text-gray-600 bg-gray-100 px-2 py-1 rounded border border-gray-200">
                                    {{ $material->code }}
                                </span>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <div class="flex items-start gap-3">
                                    @if($material->image)
                                        <img src="{{ Storage::url($material->image) }}" alt="{{ $material->name }}" class="h-10 w-10 rounded-lg object-cover border border-gray-200">
                                    @else
                                        <div class="h-10 w-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-200 text-gray-400">
                                            <i class="fas fa-cube text-lg"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-medium text-gray-900 group-hover:text-red-600 transition-colors">{{ $material->name }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-1.5">
                                            @if($material->category)
                                            <span class="inline-flex items-center gap-1">
                                                <i class="fas fa-tag text-[10px]"></i> {{ $material->category->name }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top">
                                @if($material->supplier)
                                    <div class="text-gray-900">{{ $material->supplier->name }}</div>
                                    @if($material->supplier->phone)
                                        <div class="text-xs text-gray-500 mt-0.5"><i class="fas fa-phone text-[10px] mr-1"></i>{{ $material->supplier->phone }}</div>
                                    @endif
                                @else
                                    <span class="text-gray-400 italic">Umum</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 align-top">
                                <div class="flex items-center gap-2">
                                     <span class="font-semibold {{ $isOutOfStock ? 'text-red-600' : ($isLowStock ? 'text-yellow-600' : 'text-gray-900') }}">
                                        {{ number_format($currentStock, 2) }}
                                     </span>
                                     <span class="text-xs text-gray-500">{{ $material->unit->name }}</span>
                                </div>
                                @if($isOutOfStock)
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-medium bg-red-50 text-red-600 border border-red-100 mt-1">
                                        Habis
                                    </span>
                                @elseif($isLowStock)
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-medium bg-yellow-50 text-yellow-600 border border-yellow-100 mt-1">
                                        Menipis
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap align-top">
                                <div class="text-gray-900 font-medium">Rp {{ number_format($material->purchase_price, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap align-top">
                                @if($material->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Aktif
                                </span>
                                @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-50 text-gray-600 border border-gray-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1.5"></span> Nonaktif
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center align-top">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('raw-materials.manage-stock', $material) }}" 
                                       class="p-1.5 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                       title="Kelola Stok">
                                        <i class="fas fa-boxes"></i>
                                    </a>
                                    <a href="{{ route('raw-materials.stock-history', $material) }}" 
                                       class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                       title="Riwayat">
                                        <i class="fas fa-history"></i>
                                    </a>
                                    <a href="{{ route('raw-materials.edit', $material) }}" 
                                       class="p-1.5 text-gray-500 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-colors"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('raw-materials.show', $material) }}" 
                                        class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                                        title="Detail">
                                         <i class="fas fa-eye"></i>
                                     </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-box-open text-3xl text-gray-300"></i>
                                    </div>
                                    <h3 class="text-base font-semibold text-gray-900">Belum Ada Bahan Baku</h3>
                                    <p class="text-sm text-gray-500 mt-1 mb-4 max-w-sm">Mulai tambahkan bahan baku untuk memonitor stok dan produksi Anda.</p>
                                    <a href="{{ route('raw-materials.create') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 shadow-sm transition-all">
                                        <i class="fas fa-plus mr-2"></i> Tambah Baru
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($rawMaterials->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $rawMaterials->links() }}
            </div>
            @endif
        </section>

    </div>
</main>
@endsection
@extends('layouts.app')

@section('title', 'Detail Bahan Baku - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('raw-materials.index') }}" class="text-gray-500 hover:text-red-600 transition-colors">Bahan Baku</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Detail</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">
        
        @php
            $stock = $rawMaterial->stocks->first();
            $currentStock = $stock ? $stock->quantity : 0;
            $isLowStock = $currentStock <= $rawMaterial->min_stock;
            $isOutOfStock = $currentStock <= 0;
        @endphp

        {{-- Header Section --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                @if($rawMaterial->image)
                    <img src="{{ Storage::url($rawMaterial->image) }}" alt="{{ $rawMaterial->name }}" class="w-16 h-16 rounded-lg object-cover border border-gray-200 shadow-sm">
                @else
                    <div class="w-16 h-16 rounded-lg bg-red-50 flex items-center justify-center border border-red-100 text-red-400 shadow-sm">
                        <i class="fas fa-cube text-2xl"></i>
                    </div>
                @endif
                <div>
                    <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ $rawMaterial->name }}</h1>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono font-medium bg-gray-100 text-gray-600 border border-gray-200">
                            {{ $rawMaterial->code }}
                        </span>
                        @if($rawMaterial->barcode)
                            <span class="text-xs text-gray-400">|</span>
                            <span class="text-xs text-gray-500 font-mono"><i class="fas fa-barcode mr-1"></i>{{ $rawMaterial->barcode }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('raw-materials.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-sm transition-all">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
                @can('edit bahan baku')
                <a href="{{ route('raw-materials.edit', $rawMaterial) }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-sm transition-all">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
                @endcan
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Left Column: Information --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Stock Info Card --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                           <i class="fas fa-warehouse text-gray-400"></i> Status Stok
                        </h3>
                        @if($isOutOfStock)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Stok Habis
                            </span>
                        @elseif($isLowStock)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Stok Menipis
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Stok Aman
                            </span>
                        @endif
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div class="text-center p-4 rounded-lg bg-gray-50 border border-gray-100">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tersedia</p>
                                <p class="mt-2 text-3xl font-bold {{ $isOutOfStock ? 'text-red-600' : ($isLowStock ? 'text-yellow-600' : 'text-gray-900') }}">
                                    {{ number_format($currentStock, 2) }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">{{ $rawMaterial->unit->name }}</p>
                            </div>
                            <div class="text-center p-4 rounded-lg bg-white border border-gray-200">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Min. Stok</p>
                                <p class="mt-2 text-3xl font-bold text-gray-900">
                                    {{ number_format($rawMaterial->min_stock, 2) }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">{{ $rawMaterial->unit->name }}</p>
                            </div>
                            <div class="text-center p-4 rounded-lg bg-white border border-gray-200">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Masa Simpan</p>
                                <p class="mt-2 text-3xl font-bold text-gray-900">
                                    {{ $rawMaterial->shelf_life_days ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">Hari</p>
                            </div>
                        </div>

                         @if($currentStock > 0)
                            <div class="mt-6">
                                <div class="flex justify-between items-end mb-1">
                                    <span class="text-xs font-medium text-gray-500">Kapasitas Aman</span>
                                    @php
                                        // Simple calc: assume double min stock is "full enough" for visualization
                                        $percentage = ($currentStock / ($rawMaterial->min_stock * 2)) * 100;
                                        $percentage = min($percentage, 100);
                                    @endphp
                                    <span class="text-xs font-bold text-gray-700">{{ number_format($percentage, 0) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="h-2.5 rounded-full {{ $isOutOfStock ? 'bg-red-500' : ($isLowStock ? 'bg-yellow-500' : 'bg-emerald-500') }}" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Basic Info Card --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                           <i class="fas fa-info-circle text-gray-400"></i> Detail Informasi
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                             <div>
                                <label class="text-xs font-medium text-gray-500 uppercase block mb-1">Kategori</label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-sm font-medium bg-red-50 text-red-700">
                                    {{ $rawMaterial->category->name ?? '-' }}
                                </span>
                             </div>
                             <div>
                                <label class="text-xs font-medium text-gray-500 uppercase block mb-1">Status Sistem</label>
                                @if($rawMaterial->is_active)
                                    <span class="inline-flex items-center text-sm font-medium text-green-600">
                                        <i class="fas fa-check-circle mr-1.5"></i> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-sm font-medium text-gray-500">
                                        <i class="fas fa-ban mr-1.5"></i> Nonaktif
                                    </span>
                                @endif
                             </div>
                             <div class="md:col-span-2">
                                <label class="text-xs font-medium text-gray-500 uppercase block mb-1">Deskripsi</label>
                                <p class="text-sm text-gray-700 leading-relaxed">
                                    {{ $rawMaterial->description ?: 'Tidak ada deskripsi.' }}
                                </p>
                             </div>
                        </div>
                    </div>
                </div>

                {{-- Supplier Info --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                     <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                           <i class="fas fa-truck text-gray-400"></i> Supplier & Harga
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase block mb-1">Supplier Utama</label>
                                @if($rawMaterial->supplier)
                                    <p class="text-base font-medium text-gray-900">{{ $rawMaterial->supplier->name }}</p>
                                    @if($rawMaterial->supplier->phone)
                                        <p class="text-sm text-gray-500 mt-1"><i class="fas fa-phone mr-1"></i> {{ $rawMaterial->supplier->phone }}</p>
                                    @endif
                                @else
                                    <p class="text-sm text-gray-400 italic">Tidak ada data supplier</p>
                                @endif
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase block mb-1">Harga Beli Terakhir</label>
                                <p class="text-xl font-bold text-gray-900">Rp {{ number_format($rawMaterial->purchase_price, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500">per {{ $rawMaterial->unit->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

             {{-- Right Column: Actions --}}
             <div class="lg:col-span-1 space-y-6">
                 
                 {{-- Quick Actions --}}
                 <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="font-semibold text-gray-900">Aksi Cepat</h3>
                    </div>
                    <div class="p-4 space-y-3">
                        @can('kelola stok bahan baku')
                        <a href="{{ route('raw-materials.manage-stock', $rawMaterial) }}" class="flex items-center justify-between w-full px-4 py-3 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors group border border-red-100">
                            <span class="font-medium"><i class="fas fa-box-open mr-2"></i> Kelola Stok</span>
                            <i class="fas fa-chevron-right text-red-400 group-hover:text-red-600"></i>
                        </a>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('raw-materials.manage-stock', $rawMaterial) }}?type=add" class="flex flex-col items-center justify-center px-4 py-3 bg-white text-emerald-600 rounded-lg border border-gray-200 hover:border-emerald-500 hover:bg-emerald-50 transition-all">
                                <i class="fas fa-plus-circle text-lg mb-1"></i>
                                <span class="text-xs font-semibold">Tambah</span>
                            </a>
                            <a href="{{ route('raw-materials.manage-stock', $rawMaterial) }}?type=reduce" class="flex flex-col items-center justify-center px-4 py-3 bg-white text-orange-600 rounded-lg border border-gray-200 hover:border-orange-500 hover:bg-orange-50 transition-all">
                                <i class="fas fa-minus-circle text-lg mb-1"></i>
                                <span class="text-xs font-semibold">Kurang</span>
                            </a>
                        </div>
                        @endcan

                        @can('lihat riwayat stok bahan baku')
                        <a href="{{ route('raw-materials.stock-history', $rawMaterial) }}" class="flex items-center justify-between w-full px-4 py-3 bg-white text-gray-700 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors group">
                            <span class="font-medium"><i class="fas fa-history mr-2"></i> Riwayat Stok</span>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-gray-600"></i>
                        </a>
                        @endcan
                    </div>
                 </div>

                 {{-- Valuation --}}
                 <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                     <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="font-semibold text-gray-900">Valuasi Stok</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-baseline justify-between">
                            <span class="text-sm text-gray-500">Total Nilai</span>
                            <span class="text-lg font-bold text-gray-900">
                                Rp {{ number_format($currentStock * $rawMaterial->purchase_price, 0, ',', '.') }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 mt-2 text-right">Estimasi berdasarkan harga beli terakhir</p>
                    </div>
                 </div>

                 {{-- Delete Zone --}}
                 @can('hapus bahan baku')
                 <div class="bg-red-50 border border-red-100 rounded-xl shadow-sm p-6 text-center">
                    <h4 class="text-sm font-semibold text-red-900 mb-2">Hapus Bahan Baku</h4>
                    <p class="text-xs text-red-600 mb-4">Tindakan ini tidak dapat dibatalkan dan akan menghapus semua riwayat stok.</p>
                    <form action="{{ route('raw-materials.destroy', $rawMaterial) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus bahan baku ini? Data yang dihapus tidak dapat dikembalikan.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-white border border-red-200 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors">
                            <i class="fas fa-trash-alt mr-2"></i> Hapus Permanen
                        </button>
                    </form>
                 </div>
                 @endcan

             </div>

        </div>

    </div>
</main>
@endsection
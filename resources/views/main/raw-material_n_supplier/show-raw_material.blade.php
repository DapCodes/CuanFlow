@extends('layouts.app')

@section('title', 'Detail Bahan Baku - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('raw-materials.index') }}" class="text-gray-500 hover:text-gray-700">Stok Bahan Baku</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Detail Bahan Baku</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4">
    <div class="max-w-7xl mx-auto">
        
        @php
            $stock = $rawMaterial->stocks->first();
            $currentStock = $stock ? $stock->quantity : 0;
            $isLowStock = $currentStock <= $rawMaterial->min_stock;
            $isOutOfStock = $currentStock <= 0;
        @endphp

        <x-card-container>
            <!-- Header -->
            <div class="bg-gradient-to-br from-orange-50 to-red-50 p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        @if($rawMaterial->image)
                        <img src="{{ Storage::url($rawMaterial->image) }}" alt="{{ $rawMaterial->name }}" class="w-20 h-20 rounded-lg object-cover border-2 border-white shadow-lg">
                        @else
                        <div class="w-20 h-20 rounded-lg bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center shadow-lg">
                            <i class="fas fa-cube text-white text-3xl"></i>
                        </div>
                        @endif
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ $rawMaterial->name }}</h2>
                            <p class="text-sm text-gray-500 mt-1">
                                <span class="font-mono font-semibold bg-white px-2 py-1 rounded">{{ $rawMaterial->code }}</span>
                                @if($rawMaterial->barcode)
                                <span class="ml-2 font-mono text-gray-400">| {{ $rawMaterial->barcode }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('raw-materials.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-all duration-200 shadow-md">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali
                        </a>
                        <a href="{{ route('raw-materials.edit', $rawMaterial) }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-400 to-red-500 text-white rounded-lg font-semibold hover:from-orange-500 hover:to-red-600 transition-all duration-200 shadow-md">
                            <i class="fas fa-edit mr-2"></i>
                            Edit
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Left Column: Main Information -->
                    <div class="lg:col-span-2 space-y-6">
                        
                        <!-- Informasi Dasar -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-info-circle text-orange-500 mr-2"></i>
                                Informasi Dasar
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Nama Bahan</label>
                                    <p class="text-sm font-medium text-gray-900 mt-1">{{ $rawMaterial->name }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Kode</label>
                                    <p class="text-sm font-mono font-semibold text-gray-900 mt-1 bg-white px-2 py-1 rounded inline-block">{{ $rawMaterial->code }}</p>
                                </div>
                                @if($rawMaterial->barcode)
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Barcode</label>
                                    <p class="text-sm font-mono text-gray-900 mt-1">{{ $rawMaterial->barcode }}</p>
                                </div>
                                @endif
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Kategori</label>
                                    <p class="text-sm text-gray-900 mt-1">
                                        @if($rawMaterial->category)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                                            <i class="fas fa-tag mr-1"></i>
                                            {{ $rawMaterial->category->name }}
                                        </span>
                                        @else
                                        <span class="text-gray-400">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Satuan</label>
                                    <p class="text-sm font-medium text-gray-900 mt-1">{{ $rawMaterial->unit->name ?? '-' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Status</label>
                                    <p class="text-sm mt-1">
                                        @if($rawMaterial->is_active)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                            Aktif
                                        </span>
                                        @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                            <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                                            Nonaktif
                                        </span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            @if($rawMaterial->description)
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <label class="text-xs font-semibold text-gray-500 uppercase">Deskripsi</label>
                                <p class="text-sm text-gray-700 mt-2 leading-relaxed">{{ $rawMaterial->description }}</p>
                            </div>
                            @endif
                        </div>

                        <!-- Supplier & Harga -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-truck text-orange-500 mr-2"></i>
                                Supplier & Harga
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Supplier</label>
                                    @if($rawMaterial->supplier)
                                    <div class="mt-2 bg-white rounded-lg p-4 border border-gray-200">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">{{ $rawMaterial->supplier->name }}</p>
                                                @if($rawMaterial->supplier->contact_person)
                                                <p class="text-xs text-gray-500 mt-1">
                                                    <i class="fas fa-user mr-1"></i>
                                                    {{ $rawMaterial->supplier->contact_person }}
                                                </p>
                                                @endif
                                                @if($rawMaterial->supplier->phone)
                                                <p class="text-xs text-gray-500 mt-1">
                                                    <i class="fas fa-phone mr-1"></i>
                                                    {{ $rawMaterial->supplier->phone }}
                                                </p>
                                                @endif
                                                @if($rawMaterial->supplier->email)
                                                <p class="text-xs text-gray-500 mt-1">
                                                    <i class="fas fa-envelope mr-1"></i>
                                                    {{ $rawMaterial->supplier->email }}
                                                </p>
                                                @endif
                                            </div>
                                            @if($rawMaterial->supplier->is_active)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                Aktif
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    @else
                                    <p class="text-sm text-gray-400 mt-2">Tidak ada supplier terkait</p>
                                    @endif
                                </div>
                                
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Harga Beli</label>
                                    <p class="text-2xl font-bold text-gray-900 mt-1">
                                        Rp {{ number_format($rawMaterial->purchase_price, 0, ',', '.') }}
                                    </p>
                                    <p class="text-xs text-gray-500">per {{ $rawMaterial->unit->name ?? 'unit' }}</p>
                                </div>

                                @if($stock && $stock->avg_purchase_price > 0)
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Harga Rata-rata</label>
                                    <p class="text-2xl font-bold text-orange-600 mt-1">
                                        Rp {{ number_format($stock->avg_purchase_price, 0, ',', '.') }}
                                    </p>
                                    <p class="text-xs text-gray-500">per {{ $rawMaterial->unit->name ?? 'unit' }}</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Stok Information -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-warehouse text-orange-500 mr-2"></i>
                                Informasi Stok
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white rounded-lg p-4 border-2 border-gray-200">
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Stok Tersedia</label>
                                    <p class="text-3xl font-bold mt-2
                                        {{ $isOutOfStock ? 'text-red-600' : ($isLowStock ? 'text-yellow-600' : 'text-green-600') }}">
                                        {{ number_format($currentStock, 2) }}
                                    </p>
                                    <p class="text-sm text-gray-500 mt-1">{{ $rawMaterial->unit->name ?? 'unit' }}</p>
                                    
                                    @if($isOutOfStock)
                                    <div class="mt-3">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Stok Habis
                                        </span>
                                    </div>
                                    @elseif($isLowStock)
                                    <div class="mt-3">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Stok Menipis
                                        </span>
                                    </div>
                                    @else
                                    <div class="mt-3">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Stok Aman
                                        </span>
                                    </div>
                                    @endif
                                </div>

                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Minimum Stok</label>
                                    <p class="text-3xl font-bold text-gray-900 mt-2">
                                        {{ number_format($rawMaterial->min_stock, 2) }}
                                    </p>
                                    <p class="text-sm text-gray-500 mt-1">{{ $rawMaterial->unit->name ?? 'unit' }}</p>
                                </div>

                                @if($rawMaterial->shelf_life_days)
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Masa Simpan</label>
                                    <p class="text-3xl font-bold text-gray-900 mt-2">
                                        {{ $rawMaterial->shelf_life_days }}
                                    </p>
                                    <p class="text-sm text-gray-500 mt-1">hari</p>
                                </div>
                                @endif
                            </div>

                            @if($currentStock > 0)
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <label class="text-xs font-semibold text-gray-500 uppercase mb-3 block">Persentase Stok</label>
                                <div class="relative pt-1">
                                    @php
                                        $percentage = ($currentStock / ($rawMaterial->min_stock * 2)) * 100;
                                        $percentage = min($percentage, 100);
                                    @endphp
                                    <div class="flex mb-2 items-center justify-between">
                                        <div>
                                            <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full
                                                {{ $isOutOfStock ? 'text-red-600 bg-red-200' : ($isLowStock ? 'text-yellow-600 bg-yellow-200' : 'text-green-600 bg-green-200') }}">
                                                {{ number_format($percentage, 1) }}%
                                            </span>
                                        </div>
                                    </div>
                                    <div class="overflow-hidden h-3 text-xs flex rounded-full bg-gray-200">
                                        <div style="width: {{ $percentage }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center
                                            {{ $isOutOfStock ? 'bg-red-500' : ($isLowStock ? 'bg-yellow-500' : 'bg-green-500') }}
                                            transition-all duration-500">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                    </div>

                    <!-- Right Column: Quick Stats & Actions -->
                    <div class="space-y-6">
                        
                        <!-- Quick Actions -->
                        <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-lg p-6 border border-orange-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-bolt text-orange-500 mr-2"></i>
                                Aksi Cepat
                            </h3>
                            
                            <div class="space-y-3">
                                <a href="{{ route('raw-materials.edit', $rawMaterial) }}" class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-orange-400 to-red-500 text-white rounded-lg font-semibold hover:from-orange-500 hover:to-red-600 transition-all shadow-md">
                                    <i class="fas fa-edit mr-2"></i>
                                    Edit Bahan Baku
                                </a>
                                
                                <button class="w-full inline-flex items-center justify-center px-4 py-3 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-all border border-gray-300">
                                    <i class="fas fa-plus-circle mr-2"></i>
                                    Tambah Stok
                                </button>
                                
                                <button class="w-full inline-flex items-center justify-center px-4 py-3 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-all border border-gray-300">
                                    <i class="fas fa-minus-circle mr-2"></i>
                                    Kurangi Stok
                                </button>
                                
                                <button class="w-full inline-flex items-center justify-center px-4 py-3 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-all border border-gray-300">
                                    <i class="fas fa-history mr-2"></i>
                                    Riwayat Pergerakan
                                </button>
                            </div>
                        </div>

                        <!-- Value Stats -->
                        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">Nilai Stok</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="text-xs text-gray-500">Berdasarkan Harga Beli</label>
                                    <p class="text-xl font-bold text-gray-900 mt-1">
                                        Rp {{ number_format($currentStock * $rawMaterial->purchase_price, 0, ',', '.') }}
                                    </p>
                                </div>
                                
                                @if($stock && $stock->avg_purchase_price > 0)
                                <div class="pt-4 border-t border-gray-200">
                                    <label class="text-xs text-gray-500">Berdasarkan Harga Rata-rata</label>
                                    <p class="text-xl font-bold text-orange-600 mt-1">
                                        Rp {{ number_format($currentStock * $stock->avg_purchase_price, 0, ',', '.') }}
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Timestamps -->
                        <div class="bg-white rounded-lg p-6 border border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">Informasi Waktu</h3>
                            
                            <div class="space-y-3 text-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Dibuat</span>
                                    <span class="font-medium text-gray-900">{{ $rawMaterial->created_at->format('d M Y H:i') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Diperbarui</span>
                                    <span class="font-medium text-gray-900">{{ $rawMaterial->updated_at->format('d M Y H:i') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Action -->
                        <div class="bg-red-50 rounded-lg p-6 border border-red-200">
                            <h3 class="text-sm font-semibold text-red-900 mb-2 flex items-center">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Zona Berbahaya
                            </h3>
                            <p class="text-xs text-red-600 mb-4">Tindakan ini tidak dapat dibatalkan</p>
                            
                            <form action="{{ route('raw-materials.destroy', $rawMaterial) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Yakin ingin menghapus bahan baku {{ $rawMaterial->name }}? Tindakan ini tidak dapat dibatalkan!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-colors">
                                    <i class="fas fa-trash mr-2"></i>
                                    Hapus Bahan Baku
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

        </x-card-container>

    </div>
</main>
@endsection
@extends('layouts.app')

@section('title', 'Detail Supplier - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('raw-materials.suppliers') }}" class="text-gray-500 hover:text-orange-600 transition-colors">Supplier</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Detail Supplier</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4">
    <div class="max-w-7xl mx-auto">
        
        <x-card-container>
            <!-- Header -->
            <div class="bg-gradient-to-br from-orange-50 to-red-50 p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 rounded-lg bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center shadow-lg">
                            <i class="fas fa-truck text-white text-3xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ $supplier->name }}</h2>
                            <p class="text-sm text-gray-500 mt-1">
                                <span class="font-mono font-semibold bg-white px-2 py-1 rounded">{{ $supplier->code }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <a href="{{ route('raw-materials.suppliers') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-all duration-200 shadow-md">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali
                        </a>
                        <a href="{{ route('raw-materials.suppliers.edit', $supplier) }}" class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-orange-400 to-red-500 text-white rounded-lg font-semibold hover:from-orange-500 hover:to-red-600 transition-all duration-200 shadow-md">
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
                        
                        <!-- Informasi Kontak -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-address-card text-orange-500 mr-2"></i>
                                Informasi Kontak
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Nama Supplier</label>
                                    <p class="text-sm font-medium text-gray-900 mt-1">{{ $supplier->name }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Kode</label>
                                    <p class="text-sm font-mono font-semibold text-gray-900 mt-1 bg-white px-2 py-1 rounded inline-block">{{ $supplier->code }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Kontak Person</label>
                                    <p class="text-sm text-gray-900 mt-1">{{ $supplier->contact_person ?? '-' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Telepon</label>
                                    @if($supplier->phone)
                                    <p class="text-sm text-gray-900 mt-1 flex items-center">
                                        <i class="fas fa-phone-alt text-gray-400 mr-2"></i>
                                        {{ $supplier->phone }}
                                    </p>
                                    @else
                                    <p class="text-sm text-gray-400 mt-1">-</p>
                                    @endif
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Email</label>
                                    @if($supplier->email)
                                    <p class="text-sm text-gray-900 mt-1 flex items-center">
                                        <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                        {{ $supplier->email }}
                                    </p>
                                    @else
                                    <p class="text-sm text-gray-400 mt-1">-</p>
                                    @endif
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Status</label>
                                    <p class="text-sm mt-1">
                                        @if($supplier->is_active)
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
                        </div>

                        <!-- Alamat -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>
                                Alamat
                            </h3>
                            
                            <div>
                                @if($supplier->address)
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $supplier->address }}</p>
                                @else
                                <p class="text-sm text-gray-400">Alamat belum diisi</p>
                                @endif
                            </div>
                        </div>

                        <!-- Catatan -->
                        @if($supplier->notes)
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-sticky-note text-orange-500 mr-2"></i>
                                Catatan
                            </h3>
                            
                            <div>
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $supplier->notes }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Daftar Bahan Baku -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-boxes text-orange-500 mr-2"></i>
                                Daftar Bahan Baku ({{ $supplier->raw_materials_count }})
                            </h3>
                            
                            @if($supplier->rawMaterials->count() > 0)
                            <div class="space-y-3">
                                @foreach($supplier->rawMaterials as $material)
                                @php
                                    $stock = $material->stocks->first();
                                    $currentStock = $stock ? $stock->quantity : 0;
                                @endphp
                                <div class="bg-white rounded-lg p-4 border border-gray-200 hover:shadow-md transition-shadow">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3 flex-1">
                                            @if($material->image)
                                            <img src="{{ Storage::url($material->image) }}" alt="{{ $material->name }}" class="h-12 w-12 rounded-lg object-cover border border-gray-200">
                                            @else
                                            <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center">
                                                <i class="fas fa-cube text-white"></i>
                                            </div>
                                            @endif
                                            <div class="flex-1">
                                                <div class="text-sm font-semibold text-gray-900">{{ $material->name }}</div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    <span class="font-mono">{{ $material->code }}</span>
                                                    <span class="mx-2">•</span>
                                                    <span>Stok: {{ number_format($currentStock, 2) }} {{ $material->unit->name ?? '' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="{{ route('raw-materials.show', $material) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-boxes text-3xl text-gray-300"></i>
                                </div>
                                <p class="text-sm text-gray-500">Belum ada bahan baku dari supplier ini</p>
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
                                <a href="{{ route('raw-materials.suppliers.edit', $supplier) }}" class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-orange-400 to-red-500 text-white rounded-lg font-semibold hover:from-orange-500 hover:to-red-600 transition-all shadow-md">
                                    <i class="fas fa-edit mr-2"></i>
                                    Edit Supplier
                                </a>
                                
                                <a href="{{ route('raw-materials.create') }}?supplier_id={{ $supplier->id }}" class="w-full inline-flex items-center justify-center px-4 py-3 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-all border border-gray-300">
                                    <i class="fas fa-plus-circle mr-2"></i>
                                    Tambah Bahan Baku
                                </a>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">Statistik</h3>
                            
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs text-gray-500">Total Bahan Baku</p>
                                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $supplier->raw_materials_count }}</p>
                                    </div>
                                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-boxes text-orange-600 text-xl"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Timestamps -->
                        <div class="bg-white rounded-lg p-6 border border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">Informasi Waktu</h3>
                            
                            <div class="space-y-3 text-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Dibuat</span>
                                    <span class="font-medium text-gray-900">{{ $supplier->created_at->format('d M Y H:i') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Diperbarui</span>
                                    <span class="font-medium text-gray-900">{{ $supplier->updated_at->format('d M Y H:i') }}</span>
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
                            
                            <form action="{{ route('raw-materials.suppliers.destroy', $supplier) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Yakin ingin menghapus supplier {{ $supplier->name }}? Tindakan ini tidak dapat dibatalkan!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-colors">
                                    <i class="fas fa-trash mr-2"></i>
                                    Hapus Supplier
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
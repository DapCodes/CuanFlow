@extends('admin.layouts.app')

@section('title', 'Detail Kategori')
@section('page-title', 'Detail Kategori')

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <a href="{{ route('admin.categories.index') }}" class="text-gray-500 hover:text-gray-700">Kategori</a>
</li>
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <span class="text-gray-700">{{ $category->name }}</span>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-xl {{ $category->type == 'product' ? 'bg-purple-100' : 'bg-amber-100' }} flex items-center justify-center">
                <i class="{{ $category->icon ?? 'fas fa-folder' }} {{ $category->type == 'product' ? 'text-purple-600' : 'text-amber-600' }} text-2xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $category->name }}</h2>
                <p class="text-sm text-gray-500">Slug: {{ $category->slug }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.categories.edit', $category) }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-edit text-sm"></i>
                <span>Edit</span>
            </a>
            <a href="{{ route('admin.categories.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left text-sm"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>
    
    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Tipe</p>
            @if($category->type == 'product')
            <span class="px-2.5 py-1 text-sm font-medium bg-purple-100 text-purple-700 rounded-full">Produk</span>
            @else
            <span class="px-2.5 py-1 text-sm font-medium bg-amber-100 text-amber-700 rounded-full">Bahan Baku</span>
            @endif
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Status</p>
            @if($category->is_active)
            <span class="px-2.5 py-1 text-sm font-medium bg-green-100 text-green-700 rounded-full">Aktif</span>
            @else
            <span class="px-2.5 py-1 text-sm font-medium bg-red-100 text-red-700 rounded-full">Nonaktif</span>
            @endif
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Urutan</p>
            <p class="text-lg font-bold text-gray-900">{{ $category->sort_order }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Total Item</p>
            <p class="text-lg font-bold text-gray-900">
                @if($category->type == 'product')
                    {{ $category->products->count() }} produk
                @else
                    {{ $category->rawMaterials->count() }} bahan baku
                @endif
            </p>
        </div>
    </div>
    
    <!-- Description -->
    @if($category->description)
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-2">Deskripsi</h3>
        <p class="text-gray-600">{{ $category->description }}</p>
    </div>
    @endif
    
    <!-- Items List -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                @if($category->type == 'product')
                    Daftar Produk
                @else
                    Daftar Bahan Baku
                @endif
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">{{ $category->type == 'product' ? 'Harga' : 'Stok' }}</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @if($category->type == 'product')
                        @forelse($category->products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($product->image)
                                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-10 h-10 rounded-lg object-cover">
                                    @else
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                        <i class="fas fa-box text-gray-400"></i>
                                    </div>
                                    @endif
                                    <span class="font-medium text-gray-900">{{ $product->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-700">Rp {{ number_format($product->selling_price ?? 0, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($product->is_active)
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Aktif</span>
                                @else
                                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                Belum ada produk dalam kategori ini
                            </td>
                        </tr>
                        @endforelse
                    @else
                        @forelse($category->rawMaterials as $rawMaterial)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <span class="font-medium text-gray-900">{{ $rawMaterial->name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-700">{{ number_format($rawMaterial->stock ?? 0, 2) }} {{ $rawMaterial->unit->abbreviation ?? '' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($rawMaterial->is_active ?? true)
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Aktif</span>
                                @else
                                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                Belum ada bahan baku dalam kategori ini
                            </td>
                        </tr>
                        @endforelse
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Timestamps -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-500">Dibuat pada:</p>
                <p class="font-medium text-gray-900">{{ $category->created_at->format('d M Y, H:i') }}</p>
            </div>
            <div>
                <p class="text-gray-500">Terakhir diperbarui:</p>
                <p class="font-medium text-gray-900">{{ $category->updated_at->format('d M Y, H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

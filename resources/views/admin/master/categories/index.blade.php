@extends('admin.layouts.app')

@section('title', 'Kelola Kategori')
@section('page-title', 'Data Master - Kategori')

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <span class="text-gray-700">Kategori</span>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Kelola Kategori</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola kategori produk dan bahan baku</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-cuan-dark text-white font-semibold rounded-lg hover:bg-cuan-green transition-colors">
            <i class="fas fa-plus text-sm"></i>
            <span>Tambah Kategori</span>
        </a>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari kategori..."
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green">
            </div>
            <div>
                <select name="type" class="w-full sm:w-40 px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green">
                    <option value="">Semua Tipe</option>
                    <option value="product" {{ request('type') == 'product' ? 'selected' : '' }}>Produk</option>
                    <option value="raw_material" {{ request('type') == 'raw_material' ? 'selected' : '' }}>Bahan Baku</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2.5 bg-cuan-dark text-white font-semibold rounded-lg hover:bg-cuan-green transition-colors">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search') || request('type'))
                <a href="{{ route('admin.categories.index') }}" class="px-4 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
    
    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Kategori</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Tipe</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Urutan</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Digunakan</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($categories as $category)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg {{ $category->type == 'product' ? 'bg-purple-100' : 'bg-amber-100' }} flex items-center justify-center">
                                    <i class="{{ $category->icon ?? 'fas fa-folder' }} {{ $category->type == 'product' ? 'text-purple-600' : 'text-amber-600' }}"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $category->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $category->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($category->type == 'product')
                            <span class="px-2.5 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">Produk</span>
                            @else
                            <span class="px-2.5 py-1 text-xs font-medium bg-amber-100 text-amber-700 rounded-full">Bahan Baku</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm text-gray-600">{{ $category->sort_order }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm text-gray-600">
                                @if($category->type == 'product')
                                    {{ $category->products_count }} produk
                                @else
                                    {{ $category->raw_materials_count }} bahan baku
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('admin.categories.toggle-status', $category) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="focus:outline-none">
                                    @if($category->is_active)
                                    <span class="px-2.5 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full hover:bg-green-200 transition-colors cursor-pointer">Aktif</span>
                                    @else
                                    <span class="px-2.5 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full hover:bg-red-200 transition-colors cursor-pointer">Nonaktif</span>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.categories.edit', $category) }}" 
                                   class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @php
                                    $usageCount = $category->type == 'product' ? $category->products_count : $category->raw_materials_count;
                                @endphp
                                @if($usageCount == 0)
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @else
                                <span class="p-2 text-gray-300 cursor-not-allowed" title="Kategori sedang digunakan">
                                    <i class="fas fa-lock"></i>
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-folder-open text-4xl text-gray-300 mb-3"></i>
                            <p>Belum ada kategori</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($categories->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $categories->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@extends('admin.layouts.app')

@section('title', 'Kategori Produk')
@section('page-title', 'Manajemen Kategori')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Kategori</span>
</li>
@endsection

@section('content')
<div class="px-4 lg:px-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-layer-group text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight uppercase">Kelola Kategori</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium italic">Manajemen pengelompokan produk dan bahan baku</p>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.categories.create') }}" 
               class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 text-white text-sm font-black uppercase tracking-widest rounded-xl hover:bg-emerald-600 transition-all duration-300 shadow-md hover:shadow-emerald-200/50 active:scale-95">
                <i class="fas fa-plus text-[10px]"></i>
                <span>Tambah Kategori</span>
            </a>
        </div>
    </div>

    {{-- RINGKASAN STATISTIK --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Categories --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Total Kategori</p>
                    <p class="mt-1 text-2xl font-black text-gray-900">{{ number_format($stats['total_categories']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100">
                    <i class="fas fa-folder text-gray-400 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Product Categories --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Kategori Produk</p>
                    <p class="mt-1 text-2xl font-black text-purple-600">{{ number_format($stats['product_categories']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center border border-purple-100 shadow-sm shadow-purple-100/50">
                    <i class="fas fa-box text-purple-500 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Raw Material Categories --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Bahan Baku</p>
                    <p class="mt-1 text-2xl font-black text-amber-600">{{ number_format($stats['raw_material_categories']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center border border-amber-100 shadow-sm shadow-amber-100/50">
                    <i class="fas fa-mortar-pestle text-amber-500 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Active Categories --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Kategori Aktif</p>
                    <p class="mt-1 text-2xl font-black text-emerald-600">{{ number_format($stats['active_categories']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100 shadow-sm shadow-emerald-100/50">
                    <i class="fas fa-check-double text-emerald-500 text-lg"></i>
                </div>
            </div>
        </div>
    </section>

    {{-- KONTEN UTAMA: TOOLBAR + TABEL --}}
    <x-card-container class="!p-0 overflow-hidden border border-gray-200 shadow-sm bg-white rounded-xl">
        {{-- Toolbar: Search & Filter --}}
        <div class="border-b border-gray-200 px-4 md:px-6 py-5 bg-gray-50/50">
            <form action="{{ route('admin.categories.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:items-center md:justify-between gap-4">
                <div class="w-full md:max-w-xs">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2 block italic">Cari Nama Kategori</label>
                    <div class="relative group">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: Minuman Dingin..."
                               class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all duration-300">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-emerald-500 transition-colors text-xs"></i>
                    </div>
                </div>

                <div class="flex flex-wrap items-end gap-3 w-full md:w-auto">
                    <div class="w-full sm:w-44">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2 block italic">Tipe Kategori</label>
                        <select name="type" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition-all duration-300">
                            <option value="">Semua Tipe</option>
                            <option value="product" {{ request('type') == 'product' ? 'selected' : '' }}>Produk</option>
                            <option value="raw_material" {{ request('type') == 'raw_material' ? 'selected' : '' }}>Bahan Baku</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition-all shadow-md shadow-gray-200 active:scale-95 group">
                            <i class="fas fa-search group-hover:rotate-12 transition-transform"></i>
                        </button>
                        @if(request()->anyFilled(['search', 'type']))
                            <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white border border-gray-200 text-gray-400 hover:bg-gray-50 hover:text-red-500 transition-all shadow-sm active:scale-95" title="Reset">
                                <i class="fas fa-redo-alt text-sm"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left">Detail Kategori</th>
                        <th class="px-6 py-4 text-left">Tipe</th>
                        <th class="px-6 py-4 text-center">Urutan</th>
                        <th class="px-6 py-4 text-center">Penggunaan</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center font-black">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($categories as $category)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-11 h-11 rounded-xl flex items-center justify-center shadow-sm" 
                                     style="background-color: {{ $category->type == 'product' ? '#a855f715' : '#f59e0b15' }}; border: 1px solid {{ $category->type == 'product' ? '#a855f730' : '#f59e0b30' }};">
                                    <i class="{{ $category->icon ?? 'fas fa-layer-group' }} text-xs" style="color: {{ $category->type == 'product' ? '#a855f7' : '#f59e0b' }};"></i>
                                </div>
                                <div>
                                    <p class="font-black text-gray-900 leading-tight uppercase tracking-tight">{{ $category->name }}</p>
                                    @if($category->description)
                                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1 italic line-clamp-1">{{ $category->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            @if($category->type == 'product')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-purple-50 text-purple-600 border border-purple-100">Produk</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-amber-50 text-amber-600 border border-amber-100">Bahan Baku</span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="text-[11px] font-black text-gray-900 bg-gray-100 px-2 py-1 rounded-lg border border-gray-200">{{ $category->sort_order }}</span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-[11px] font-black text-gray-900">
                                    @if($category->type == 'product')
                                        {{ number_format($category->products_count) }}
                                    @else
                                        {{ number_format($category->raw_materials_count) }}
                                    @endif
                                </span>
                                <span class="text-[8px] font-black uppercase tracking-widest text-gray-400">Items</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <form action="{{ route('admin.categories.toggle-status', $category) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="focus:outline-none active:scale-95 transition-transform">
                                    @if($category->is_active)
                                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-green-50 text-green-600 border border-green-100 hover:bg-green-100 transition-colors">Aktif</span>
                                    @else
                                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-red-50 text-red-600 border border-red-100 hover:bg-red-100 transition-colors">Nonaktif</span>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.categories.edit', $category) }}" 
                                   class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-600 hover:text-white shadow-sm transition-all active:scale-95 border border-blue-100" 
                                   title="Edit Kategori">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                @php
                                    $usageCount = $category->type == 'product' ? $category->products_count : $category->raw_materials_count;
                                @endphp
                                @if($usageCount == 0)
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus kategori ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-600 hover:text-white shadow-sm transition-all active:scale-95 border border-red-100" 
                                                title="Hapus Kategori">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                @else
                                    <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-50 text-gray-300 border border-gray-100 cursor-not-allowed" title="Kategori sedang digunakan">
                                        <i class="fas fa-lock text-xs"></i>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gray-50 border border-dashed border-gray-200 rounded-full flex items-center justify-center mb-6">
                                    <i class="fas fa-folder-open text-gray-200 text-3xl"></i>
                                </div>
                                <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">Kategori Tidak Ditemukan</h3>
                                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-widest mt-2 max-w-xs mx-auto italic">
                                    {{ request('search') ? 'Coba sesuaikan kata kunci pencarian Anda.' : 'Belum ada data kategori tersedia.' }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($categories->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $categories->withQueryString()->links() }}
        </div>
        @endif
    </x-card-container>
</div>
@endsection


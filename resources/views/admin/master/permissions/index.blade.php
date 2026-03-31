@extends('admin.layouts.app')

@section('title', 'Manajemen Permission')
@section('page-title', 'Manajemen Permission')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Permissions</span>
</li>
@endsection

@section('content')
<div class="px-4 lg:px-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-key text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight uppercase">Kelola Permissions</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium italic">Pengaturan detail hak akses fitur di dalam sistem</p>
            </div>
        </div>
        @can('kelola permissions')
        <div class="flex-shrink-0">
            <a href="{{ route('admin.permissions.create') }}" 
               class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 text-white text-sm font-black uppercase tracking-widest rounded-xl hover:bg-emerald-600 transition-all duration-300 shadow-md hover:shadow-emerald-200/50 active:scale-95">
                <i class="fas fa-plus text-[10px]"></i>
                <span>Tambah Permission</span>
            </a>
        </div>
        @endcan
    </div>

    {{-- RINGKASAN STATISTIK --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Permissions --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Total Perms</p>
                    <p class="mt-1 text-2xl font-black text-gray-900">{{ number_format($stats['total_permissions']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100">
                    <i class="fas fa-key text-gray-400 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Total Categories --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Kategori</p>
                    <p class="mt-1 text-2xl font-black text-blue-600">{{ number_format($stats['total_categories']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center border border-blue-100 shadow-sm shadow-blue-100/50">
                    <i class="fas fa-folder-open text-blue-500 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Uncategorized --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Tanpa Kategori</p>
                    <p class="mt-1 text-2xl font-black text-amber-600">{{ number_format($stats['uncategorized']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center border border-amber-100 shadow-sm shadow-amber-100/50">
                    <i class="fas fa-question-circle text-amber-500 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Recent --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Baru (7 Hari)</p>
                    <p class="mt-1 text-2xl font-black text-emerald-600">{{ number_format($stats['recent']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100 shadow-sm shadow-emerald-100/50">
                    <i class="fas fa-clock text-emerald-500 text-lg"></i>
                </div>
            </div>
        </div>
    </section>

    {{-- KONTEN UTAMA: TOOLBAR + LIST --}}
    <div class="space-y-6">
        {{-- Toolbar: Search & Filter --}}
        <div class="bg-white border border-gray-200 rounded-2xl px-4 md:px-6 py-5 shadow-sm">
            <form action="{{ route('admin.permissions.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:items-center md:justify-between gap-4">
                <div class="w-full md:max-w-xs">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2 block italic">Cari Nama Permission / Deskripsi</label>
                    <div class="relative group">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: buat role..."
                               class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all duration-300">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-emerald-500 transition-colors text-xs"></i>
                    </div>
                </div>

                <div class="flex flex-wrap items-end gap-3 w-full md:w-auto">
                    <div class="w-full sm:w-56">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2 block italic">Filter Kategori</label>
                        <select name="category" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition-all duration-300">
                            <option value="">Semua Kategori</option>
                            @foreach(\App\Models\PermissionCategory::ordered()->get() as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition-all shadow-md shadow-gray-200 active:scale-95 group">
                            <i class="fas fa-search group-hover:rotate-12 transition-transform"></i>
                        </button>
                        @if(request()->anyFilled(['search', 'category']))
                            <a href="{{ route('admin.permissions.index') }}" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white border border-gray-200 text-gray-400 hover:bg-gray-50 hover:text-red-500 transition-all shadow-sm active:scale-95" title="Reset">
                                <i class="fas fa-redo-alt text-sm"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Categories and Permissions -->
        <div class="space-y-4">
            @forelse($permissionCategories as $category)
            @if($category->permissions->count() > 0)
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm" x-data="{ open: true }">
                <!-- Category Header -->
                <button @click="open = !open" 
                        class="w-full flex items-center justify-between px-6 py-5 bg-gray-50/50 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-sm" 
                             style="background-color: {{ $category->color }}15; border: 1px solid {{ $category->color }}30;">
                            <i class="{{ $category->icon }} text-lg" style="color: {{ $category->color }};"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-black text-gray-900 uppercase tracking-tight">{{ $category->name }}</p>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-0.5">{{ $category->permissions->count() }} PERMISSIONS AKTIF</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <i class="fas fa-chevron-down text-gray-300 transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                    </div>
                </button>
                
                <!-- Permissions List -->
                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="border-t border-gray-100 bg-white">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
                        @foreach($category->permissions as $permission)
                        <div class="group relative flex items-center justify-between p-4 bg-gray-50/50 rounded-xl border border-gray-100 hover:border-emerald-200 hover:bg-emerald-50/30 transition-all duration-300">
                            <div class="flex-1 min-w-0 pr-10">
                                <p class="font-black text-gray-900 uppercase tracking-tight text-[11px] leading-tight">{{ $permission->name }}</p>
                                @if($permission->description)
                                <p class="text-[10px] text-gray-500 mt-1 italic leading-tight">{{ $permission->description }}</p>
                                @endif
                            </div>
                            
                            @can('kelola permissions')
                            <div class="flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity absolute right-4 top-1/2 -translate-y-1/2">
                                <a href="{{ route('admin.permissions.edit', $permission) }}" 
                                   class="w-8 h-8 flex items-center justify-center rounded-lg bg-white shadow-sm text-blue-500 hover:bg-blue-500 hover:text-white transition-all active:scale-95 border border-blue-100/50"
                                   title="Edit">
                                    <i class="fas fa-edit text-[10px]"></i>
                                </a>
                                <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus permission ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-white shadow-sm text-red-500 hover:bg-red-500 hover:text-white transition-all active:scale-95 border border-red-100/50"
                                            title="Hapus">
                                        <i class="fas fa-trash text-[10px]"></i>
                                    </button>
                                </form>
                            </div>
                            @endcan
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            @empty
            <div class="bg-white border border-dashed border-gray-300 rounded-2xl py-20 px-6 text-center shadow-sm">
                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-search text-gray-200 text-4xl"></i>
                </div>
                <h3 class="text-lg font-black text-gray-900 uppercase tracking-widest">Tidak Ada Data</h3>
                <p class="text-sm text-gray-500 mt-2 font-medium italic">
                    {{ request('search') ? 'Tidak ada permission yang cocok dengan kata kunci "' . request('search') . '"' : 'Belum ada data permission tersedia.' }}
                </p>
                @if(request()->anyFilled(['search', 'category']))
                <a href="{{ route('admin.permissions.index') }}" class="inline-flex items-center gap-2 text-emerald-600 font-bold uppercase tracking-widest text-[11px] mt-6 hover:text-emerald-700">
                    <i class="fas fa-sync-alt"></i>
                    <span>Tampilkan Semua Data</span>
                </a>
                @endif
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection


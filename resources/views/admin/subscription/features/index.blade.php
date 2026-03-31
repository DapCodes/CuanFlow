@extends('admin.layouts.app')

@section('title', 'Fitur Langganan')
@section('page-title', 'Modul & Fitur')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Fitur Langganan</span>
</li>
@endsection

@section('content')
<div class="px-4 lg:px-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-list-check text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight uppercase">Fitur Langganan</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium italic">Kelola daftar modul dan fitur yang tersedia untuk paket tier</p>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.subscription-features.create') }}" 
               class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 text-white text-sm font-black uppercase tracking-widest rounded-xl hover:bg-emerald-600 transition-all duration-300 shadow-md hover:shadow-emerald-200/50 active:scale-95">
                <i class="fas fa-plus text-[10px]"></i>
                <span>Tambah Fitur</span>
            </a>
        </div>
    </div>

    {{-- RINGKASAN STATISTIK --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Features --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Total Fitur</p>
                    <p class="mt-1 text-2xl font-black text-gray-900">{{ number_format($stats['total_features']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100">
                    <i class="fas fa-cubes text-gray-400 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Active Features --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Fitur Aktif</p>
                    <p class="mt-1 text-2xl font-black text-emerald-600">{{ number_format($stats['active_features']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100 shadow-sm shadow-emerald-100/50">
                    <i class="fas fa-toggle-on text-emerald-500 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Inactive Features --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Fitur Nonaktif</p>
                    <p class="mt-1 text-2xl font-black text-amber-600">{{ number_format($stats['inactive_features']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center border border-amber-100 shadow-sm shadow-amber-100/50">
                    <i class="fas fa-toggle-off text-amber-500 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Categories --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Grup Kategori</p>
                    <p class="mt-1 text-2xl font-black text-blue-600">{{ number_format($stats['categories_count']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center border border-blue-100 shadow-sm shadow-blue-100/50">
                    <i class="fas fa-folder-tree text-blue-500 text-lg"></i>
                </div>
            </div>
        </div>
    </section>

    {{-- KONTEN UTAMA: TOOLBAR + TABEL --}}
    <x-card-container class="!p-0 overflow-hidden border border-gray-200 shadow-sm bg-white rounded-xl">
        {{-- Toolbar: Search --}}
        <div class="border-b border-gray-200 px-4 md:px-6 py-5 bg-gray-50/50">
            <form action="{{ route('admin.subscription-features.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:items-center md:justify-between gap-4">
                <div class="w-full md:max-w-xs">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2 block italic">Cari Fitur / Kategori</label>
                    <div class="relative group">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: Multi Outlet..."
                               class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all duration-300">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-emerald-500 transition-colors text-xs"></i>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition-all shadow-md shadow-gray-200 active:scale-95 group">
                        <i class="fas fa-search group-hover:rotate-12 transition-transform"></i>
                    </button>
                    @if(request()->anyFilled(['search']))
                        <a href="{{ route('admin.subscription-features.index') }}" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white border border-gray-200 text-gray-400 hover:bg-gray-50 hover:text-red-500 transition-all shadow-sm active:scale-95" title="Reset">
                            <i class="fas fa-redo-alt text-sm"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left">Representasi Fitur</th>
                        <th class="px-6 py-4 text-left">Grup Kategori</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Order</th>
                        <th class="px-6 py-4 text-center font-black">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($features as $feature)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-11 h-11 rounded-xl bg-gray-900 text-emerald-400 flex items-center justify-center shadow-lg border border-gray-800">
                                    <i class="{{ $feature->icon ?? 'fas fa-star' }} text-xs"></i>
                                </div>
                                <div>
                                    <p class="font-black text-gray-900 leading-tight uppercase tracking-tight">{{ $feature->display_name }}</p>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-emerald-500 mt-1 italic line-clamp-1 max-w-[200px]" title="{{ $feature->description }}">{{ $feature->description ?? $feature->name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-blue-50 text-blue-600 border border-blue-100">
                                {{ $feature->category ?? 'General' }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            @if($feature->is_active)
                                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-green-50 text-green-600 border border-green-100">Aktif</span>
                            @else
                                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-gray-50 text-gray-400 border border-gray-200">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-center font-black text-gray-900">
                            {{ $feature->sort_order }}
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.subscription-features.edit', ['feature' => $feature->id]) }}" 
                                   class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-600 hover:text-white shadow-sm transition-all active:scale-95 border border-blue-100" 
                                   title="Edit Fitur">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('admin.subscription-features.destroy', ['feature' => $feature->id]) }}" method="POST" 
                                      onsubmit="return confirm('Yakin ingin menghapus fitur ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-600 hover:text-white shadow-sm transition-all active:scale-95 border border-red-100" 
                                            title="Hapus Fitur">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gray-50 border border-dashed border-gray-200 rounded-full flex items-center justify-center mb-6 text-gray-200">
                                    <i class="fas fa-layer-group text-3xl"></i>
                                </div>
                                <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">Fitur Tidak Ditemukan</h3>
                                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-widest mt-2 max-w-xs mx-auto italic">
                                    {{ request('search') ? 'Coba sesuaikan kata kunci pencarian Anda.' : 'Belum ada data fitur langganan tersedia.' }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card-container>
</div>
@endsection

@extends('admin.layouts.app')

@section('title', 'Manajemen Outlet')
@section('page-title', 'Manajemen Outlet')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Manajemen Outlet</span>
</li>
@endsection

@section('content')
<div class="px-4 lg:px-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-store text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight uppercase">Manajemen Outlet</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium italic">Monitoring seluruh cabang outlet Anda</p>
            </div>
        </div>
    </div>

    {{-- RINGKASAN STATISTIK --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        {{-- Total Outlet --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Total Outlet</p>
                    <p class="mt-1 text-2xl font-black text-gray-900">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100 shadow-sm">
                    <i class="fas fa-store text-gray-400 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Outlet Aktif --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Aktif</p>
                    <p class="mt-1 text-2xl font-black text-green-600">{{ number_format($stats['active']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center border border-green-100 shadow-sm shadow-green-100/50">
                    <i class="fas fa-check-circle text-green-500 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Outlet Nonaktif --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Nonaktif</p>
                    <p class="mt-1 text-2xl font-black text-red-600">{{ number_format($stats['inactive']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center border border-red-100 shadow-sm shadow-red-100/50">
                    <i class="fas fa-times-circle text-red-500 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Total Transaksi --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Trans (Sls)</p>
                    <p class="mt-1 text-2xl font-black text-blue-600">{{ number_format($stats['total_sales']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center border border-blue-100 shadow-sm shadow-blue-100/50">
                    <i class="fas fa-shopping-cart text-blue-500 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Total Produk --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Produk</p>
                    <p class="mt-1 text-2xl font-black text-emerald-600">{{ number_format($stats['total_products']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100 shadow-sm shadow-emerald-100/50">
                    <i class="fas fa-box text-emerald-500 text-lg"></i>
                </div>
            </div>
        </div>
    </section>

    {{-- KONTEN UTAMA: TOOLBAR + TABEL --}}
    <x-card-container class="!p-0 overflow-hidden border border-gray-200 shadow-sm">
        {{-- Toolbar: Search & Filter --}}
        <div class="border-b border-gray-200 px-4 md:px-6 py-5 bg-gray-50/50">
            <form action="{{ route('admin.outlets.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:items-center md:justify-between gap-4">
                <div class="w-full md:max-w-xs">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2 block italic">Cari Outlet / Owner</label>
                    <div class="relative group">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: Outlet Bandung..."
                               class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all duration-300">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-emerald-500 transition-colors text-xs"></i>
                    </div>
                </div>

                <div class="flex flex-wrap items-end gap-3 w-full md:w-auto">
                    <div class="w-full sm:w-40">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2 block italic">Status Aktivasi</label>
                        <select name="status" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition-all duration-300">
                            <option value="">Semua</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition-all shadow-md shadow-gray-200 active:scale-95 group">
                            <i class="fas fa-search group-hover:rotate-12 transition-transform"></i>
                        </button>
                        @if(request()->anyFilled(['status', 'search']))
                            <a href="{{ route('admin.outlets.index') }}" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white border border-gray-200 text-gray-400 hover:bg-gray-50 hover:text-red-500 transition-all shadow-sm active:scale-95" title="Reset">
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
                        <th class="px-6 py-4 text-left">Outlet</th>
                        <th class="px-6 py-4 text-left">Owner</th>
                        <th class="px-6 py-4 text-center">Statistik</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center font-black">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($outlets as $outlet)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-11 h-11 rounded-xl bg-teal-50 flex items-center justify-center text-teal-600 border border-teal-100 shadow-sm">
                                    <i class="fas fa-store text-xs"></i>
                                </div>
                                <div>
                                    <p class="font-black text-gray-900 leading-tight uppercase tracking-tight">{{ $outlet->name }}</p>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1 italic">{{ Str::limit($outlet->address, 30) ?? 'No address' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-[11px] font-bold text-gray-900">{{ $outlet->owner->name ?? 'N/A' }}</div>
                            <div class="text-[10px] font-medium text-gray-400 italic">{{ $outlet->owner->email ?? '' }}</div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex justify-center gap-4">
                                <div class="text-center" title="Penjualan">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Sales</p>
                                    <p class="text-[11px] font-black text-gray-900">{{ number_format($outlet->sales_count) }}</p>
                                </div>
                                <div class="text-center" title="Produk">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Prod</p>
                                    <p class="text-[11px] font-black text-gray-900">{{ number_format($outlet->products_count) }}</p>
                                </div>
                                <div class="text-center" title="Karyawan">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Staff</p>
                                    <p class="text-[11px] font-black text-gray-900">{{ number_format($outlet->users_count) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <form action="{{ route('admin.outlets.toggle-status', $outlet) }}" method="POST">
                                @csrf
                                <button type="submit" class="focus:outline-none transition-transform active:scale-95 group">
                                    @if($outlet->is_active)
                                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-green-50 text-green-600 border border-green-100 group-hover:bg-green-600 group-hover:text-white transition-all">Aktif</span>
                                    @else
                                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-red-50 text-red-600 border border-red-100 group-hover:bg-red-600 group-hover:text-white transition-all">Nonaktif</span>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.outlets.show', $outlet) }}" 
                                   class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-600 hover:text-white shadow-sm transition-all active:scale-95 border border-blue-100" title="Informasi Lengkap">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gray-50 border border-dashed border-gray-200 rounded-full flex items-center justify-center mb-6">
                                    <i class="fas fa-store-slash text-gray-200 text-3xl"></i>
                                </div>
                                <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">Outlet Tidak Ditemukan</h3>
                                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-widest mt-2 max-w-xs mx-auto italic">
                                    {{ request()->anyFilled(['search', 'status']) ? 'Coba sesuaikan kata kunci atau filter pencarian Anda.' : 'Daftar outlet masih kosong.' }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($outlets->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $outlets->links() }}
        </div>
        @endif
    </x-card-container>
</div>
@endsection

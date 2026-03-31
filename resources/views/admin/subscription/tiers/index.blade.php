@extends('admin.layouts.app')

@section('title', 'Tiers Paket')
@section('page-title', 'Billing & Plan')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Subscription Tiers</span>
</li>
@endsection

@section('content')
<div class="px-4 lg:px-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-crown text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight uppercase">Paket Berlangganan</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium italic">Kelola tingkatan paket berlangganan (Tiers) dan batasan fitur</p>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.subscription-tiers.create') }}" 
               class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 text-white text-sm font-black uppercase tracking-widest rounded-xl hover:bg-emerald-600 transition-all duration-300 shadow-md hover:shadow-emerald-200/50 active:scale-95">
                <i class="fas fa-plus text-[10px]"></i>
                <span>Buat Paket</span>
            </a>
        </div>
    </div>

    {{-- RINGKASAN STATISTIK --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Tiers --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Total Paket</p>
                    <p class="mt-1 text-2xl font-black text-gray-900">{{ number_format($stats['total_tiers']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100">
                    <i class="fas fa-layer-group text-gray-400 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Active Tiers --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Paket Aktif</p>
                    <p class="mt-1 text-2xl font-black text-emerald-600">{{ number_format($stats['active_tiers']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100 shadow-sm shadow-emerald-100/50">
                    <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Inactive Tiers --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Paket Nonaktif</p>
                    <p class="mt-1 text-2xl font-black text-amber-600">{{ number_format($stats['inactive_tiers']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center border border-amber-100 shadow-sm shadow-amber-100/50">
                    <i class="fas fa-eye-slash text-amber-500 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Total Active Subscriptions --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Pelanggan Aktif</p>
                    <p class="mt-1 text-2xl font-black text-blue-600">{{ number_format($stats['total_subscriptions']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center border border-blue-100 shadow-sm shadow-blue-100/50">
                    <i class="fas fa-users text-blue-500 text-lg"></i>
                </div>
            </div>
        </div>
    </section>

    {{-- KONTEN UTAMA: TOOLBAR + GRID --}}
    <div class="space-y-6">
        {{-- Toolbar: Search --}}
        <div class="bg-white border border-gray-200 px-4 md:px-6 py-5 rounded-xl shadow-sm bg-gray-50/50">
            <form action="{{ route('admin.subscription-tiers.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:items-center md:justify-between gap-4">
                <div class="w-full md:max-w-xs">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2 block italic">Cari Nama / Deskripsi</label>
                    <div class="relative group">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: Premium..."
                               class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all duration-300">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-emerald-500 transition-colors text-xs"></i>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition-all shadow-md shadow-gray-200 active:scale-95 group">
                        <i class="fas fa-search group-hover:rotate-12 transition-transform"></i>
                    </button>
                    @if(request()->anyFilled(['search']))
                        <a href="{{ route('admin.subscription-tiers.index') }}" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white border border-gray-200 text-gray-400 hover:bg-gray-50 hover:text-red-500 transition-all shadow-sm active:scale-95" title="Reset">
                            <i class="fas fa-redo-alt text-sm"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        @if($tiers->count() > 0)
        <!-- Tiers Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($tiers as $tier)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-xl hover:shadow-emerald-100/50 transition-all duration-300 group">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-6">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center bg-gray-900 text-emerald-400 shadow-lg group-hover:scale-110 transition-transform duration-500">
                                <i class="fas fa-gem text-xl"></i>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <span class="px-3 py-1 text-[9px] font-black uppercase tracking-widest rounded-full {{ $tier->is_active ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-gray-50 text-gray-500 border border-gray-200' }}">
                                    {{ $tier->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Sort: {{ $tier->sort_order }}</span>
                            </div>
                        </div>
                        
                        <h3 class="text-xl font-black text-gray-900 mb-1 uppercase tracking-tight">{{ $tier->display_name }}</h3>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest italic mb-4">{{ $tier->name }}</p>
                        <p class="text-[11px] text-gray-500 line-clamp-3 h-12 leading-relaxed">{{ $tier->description ?? 'Tidak ada deskripsi.' }}</p>
                        
                        <div class="mt-6 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                            <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-1">Harga Langganan</p>
                            <div class="flex items-baseline gap-1">
                                <span class="text-2xl font-black text-gray-900">Rp {{ number_format($tier->price, 0, ',', '.') }}</span>
                                <span class="text-[10px] font-bold text-gray-400">/ bulan</span>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-between border-t border-dashed border-gray-100 pt-6">
                            <div class="flex flex-col">
                                <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-1">Limit Outlet</span>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-store text-[10px] text-emerald-500"></i>
                                    <span class="text-xs font-black text-gray-900">{{ $tier->max_outlets ? $tier->max_outlets . ' unit' : 'Unlimited' }}</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-1">Pelanggan</span>
                                <div class="flex items-center gap-2 text-emerald-600">
                                    <span class="text-xs font-black">{{ number_format($tier->subscriptions_count ?? 0) }}</span>
                                    <i class="fa-solid fa-users text-[10px]"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-gray-900 flex justify-center items-center">
                        <a href="{{ route('admin.subscription-tiers.edit', ['tier' => $tier->id]) }}" 
                           class="w-full text-center py-2 text-[10px] font-black uppercase tracking-widest text-emerald-400 hover:text-white transition-colors">
                            Edit Detail Paket
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        @else
        <div class="bg-white border border-dashed border-gray-200 rounded-3xl p-20 text-center shadow-sm">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-200">
                <i class="fas fa-crown text-3xl"></i>
            </div>
            <h3 class="text-lg font-black text-gray-900 uppercase tracking-widest">Tidak Ada Paket Ditemukan</h3>
            <p class="text-[11px] text-gray-500 font-bold uppercase tracking-widest mt-2 italic max-w-xs mx-auto">
                {{ request('search') ? 'Coba sesuaikan kata kunci pencarian Anda.' : 'Belum ada data paket berlangganan tersedia.' }}
            </p>
        </div>
        @endif
    </div>
</div>
@endsection

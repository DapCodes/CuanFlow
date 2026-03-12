@extends('layouts.app')

@section('title', 'Riwayat Stok - ' . $rawMaterial->name . ' - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('raw-materials.index') }}" class="text-gray-500 hover:text-cuan-green transition-colors">Bahan Baku</a>
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('raw-materials.show', $rawMaterial) }}" class="text-gray-500 hover:text-cuan-green transition-colors">{{ $rawMaterial->name }}</a>
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight whitespace-nowrap">Riwayat Stok</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">
        
        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-cuan-green/10 flex items-center justify-center text-cuan-green">
                        <i class="fas fa-history"></i>
                    </div>
                    <h1 class="text-xl md:text-2xl font-black text-gray-900 uppercase tracking-tight">
                        Aliran Persediaan
                    </h1>
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    Jejak mutasi masuk dan keluar untuk <span class="text-cuan-green font-bold">{{ $rawMaterial->name }}</span>
                </p>
            </div>
            <div class="flex items-center gap-3">
                 <a href="{{ route('raw-materials.show', $rawMaterial) }}" 
                   class="inline-flex items-center gap-2 rounded-xl bg-white border border-gray-200 px-5 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all shadow-sm active:scale-95">
                    <span>Kembali</span>
                </a>
                @can('kelola stok bahan baku')
                <a href="{{ route('raw-materials.manage-stock', $rawMaterial) }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-5 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                    <i class="fas fa-plus"></i>
                    <span>Catat Mutasi</span>
                </a>
                @endcan
            </div>
        </section>

        {{-- SUMMARY KECIL --}}
        <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center overflow-hidden flex-shrink-0">
                    @if($rawMaterial->image)
                        <img src="{{ Storage::url($rawMaterial->image) }}" class="w-full h-full object-cover">
                    @else
                        <i class="fas fa-cube text-gray-300"></i>
                    @endif
                </div>
                <div>
                    <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Status Bahan</h2>
                    <p class="text-sm font-black text-gray-900 tracking-tight leading-none">{{ $rawMaterial->name }}</p>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Stok Saat Ini</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <p class="text-2xl font-black text-gray-900 tracking-tight">{{ number_format($rawMaterial->stocks->first()->quantity ?? 0, 2) }}</p>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $rawMaterial->unit->abbreviation }}</p>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Mutasi</p>
                <p class="text-2xl font-black text-cuan-green mt-1 tracking-tight">{{ $movements->total() }} <span class="text-xs text-gray-400 uppercase">Input</span></p>
            </div>
        </section>

        {{-- LIST RIWAYAT --}}
        <div class="space-y-4">
            @forelse($movements as $movement)
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:border-cuan-green group transition-all relative overflow-hidden">
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $movement->type === 'in' ? 'bg-cuan-green' : 'bg-red-500' }}"></div>

                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pl-2">
                        <div class="flex items-center gap-6">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-lg {{ $movement->type === 'in' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }} flex-shrink-0 border {{ $movement->type === 'in' ? 'border-emerald-100' : 'border-red-100' }}">
                                <i class="fas {{ $movement->type === 'in' ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-3">
                                    <span class="text-[10px] font-black uppercase tracking-[0.2em] {{ $movement->type === 'in' ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ $movement->type === 'in' ? 'Input Stok' : 'Output Stok' }}
                                    </span>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $movement->created_at->format('d M Y • H:i') }}</span>
                                </div>
                                <h3 class="text-xl font-black mt-1 {{ $movement->type === 'in' ? 'text-emerald-700' : 'text-red-700' }} tracking-tighter">
                                    {{ $movement->type === 'in' ? '+' : '-' }}{{ number_format($movement->quantity, 2) }} 
                                    <span class="text-[10px] uppercase opacity-60 font-black ml-1">{{ $rawMaterial->unit->abbreviation }}</span>
                                </h3>
                                @if($movement->notes)
                                    <p class="mt-2 text-xs font-bold text-gray-500 italic">"{{ $movement->notes }}"</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-col md:items-end justify-center">
                            <div class="grid grid-cols-2 gap-8 mb-3">
                                <div class="text-right">
                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Saldo Awal</p>
                                    <p class="text-xs font-black text-gray-900 tracking-tight">{{ number_format($movement->quantity_before, 2) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Saldo Akhir</p>
                                    <p class="text-xs font-black text-gray-900 tracking-tight">{{ number_format($movement->quantity_after, 2) }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Oleh</span>
                                <span class="text-[10px] font-black text-gray-600 uppercase tracking-widest">{{ $movement->createdBy->name ?? 'SYSTEM' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white border border-gray-200 rounded-xl p-20 text-center flex flex-col items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center border border-gray-100 text-gray-300">
                        <i class="fas fa-history text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-[10px] font-black text-gray-900 uppercase tracking-[0.2em]">Data Kosong</h3>
                        <p class="text-xs font-bold text-gray-400 uppercase mt-2">Belum ada mutasi stok tercatat.</p>
                    </div>
                </div>
            @endforelse

            {{-- PAGINATION --}}
             @if($movements->hasPages())
                <div class="pt-8 mb-12">
                    {{ $movements->links() }}
                </div>
            @endif
        </div>
    </div>
</main>
@endsection
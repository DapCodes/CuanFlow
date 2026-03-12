@extends('layouts.app')

@section('title', 'Riwayat Stok - ' . $rawMaterial->name . ' - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('raw-materials.index') }}" class="text-gray-500 hover:text-cuan-green transition-colors">Bahan Baku</a>
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('raw-materials.show', $rawMaterial) }}" class="text-gray-500 hover:text-cuan-green transition-colors">{{ $rawMaterial->name }}</a>
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Riwayat Stok</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-5xl mx-auto space-y-6">
        
        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Lacak Aliran Stok
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Mutasi masuk dan keluar untuk <span class="text-cuan-green font-bold tracking-tight">{{ $rawMaterial->name }}</span>
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
                    <i class="fas fa-plus shadow-sm"></i>
                    <span>Catat Mutasi</span>
                </a>
                @endcan
            </div>
        </section>

        {{-- SUMMARY KECIL --}}
        <div class="bg-white border border-gray-200 rounded-[2rem] p-6 shadow-sm flex items-center gap-6">
            <div class="w-16 h-16 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center overflow-hidden flex-shrink-0">
                @if($rawMaterial->image)
                    <img src="{{ Storage::url($rawMaterial->image) }}" class="w-full h-full object-cover">
                @else
                    <i class="fas fa-cube text-gray-300 text-2xl"></i>
                @endif
            </div>
            <div>
                <h2 class="text-sm font-black text-gray-900 uppercase tracking-widest">{{ $rawMaterial->name }}</h2>
                <div class="flex items-center gap-4 mt-2">
                    <span class="text-[10px] font-black font-mono text-gray-400 bg-gray-100 px-2 py-1 rounded-lg border border-gray-100 uppercase tracking-tighter">{{ $rawMaterial->code }}</span>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Stok Sekarang:</span>
                        <span class="text-sm font-black text-gray-900 tracking-tight">{{ number_format($rawMaterial->stocks->first()->quantity ?? 0, 2) }} {{ $rawMaterial->unit->abbreviation }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- TIMELINE MODERN --}}
        <div class="space-y-4">
            @forelse($movements as $movement)
                <div class="bg-white border border-gray-100 rounded-[2rem] p-6 shadow-sm hover:shadow-md transition-all group overflow-hidden relative">
                    {{-- Decorative side bar --}}
                    <div class="absolute left-0 top-0 bottom-0 w-2 {{ $movement->type === 'in' ? 'bg-emerald-500' : 'bg-red-500' }} transition-all group-hover:w-3"></div>

                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pl-4">
                        <div class="flex items-center gap-6">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-xl {{ $movement->type === 'in' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }} flex-shrink-0 border {{ $movement->type === 'in' ? 'border-emerald-100' : 'border-red-100' }}">
                                <i class="fas {{ $movement->type === 'in' ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-3">
                                    <span class="text-[10px] font-black uppercase tracking-widest {{ $movement->type === 'in' ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ $movement->type === 'in' ? 'Stok Masuk' : 'Stok Keluar' }}
                                    </span>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $movement->created_at->format('d M Y • H:i') }}</span>
                                </div>
                                <h3 class="text-2xl font-black mt-1 {{ $movement->type === 'in' ? 'text-emerald-700' : 'text-red-700' }} tracking-tighter">
                                    {{ $movement->type === 'in' ? '+' : '-' }}{{ number_format($movement->quantity, 2) }} 
                                    <span class="text-xs uppercase opacity-60 font-black">{{ $rawMaterial->unit->abbreviation }}</span>
                                </h3>
                                @if($movement->notes)
                                    <p class="mt-2 text-xs font-bold text-gray-500 italic max-w-md">"{{ $movement->notes }}"</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-col md:items-end justify-center min-w-[180px]">
                            <div class="grid grid-cols-2 gap-4 w-full md:w-auto">
                                <div class="text-center md:text-right">
                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Saldo Awal</p>
                                    <p class="text-sm font-black text-gray-900">{{ number_format($movement->quantity_before, 2) }}</p>
                                </div>
                                <div class="text-center md:text-right">
                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Saldo Akhir</p>
                                    <p class="text-sm font-black text-gray-900">{{ number_format($movement->quantity_after, 2) }}</p>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded-xl border border-gray-100 md:ml-auto">
                                <i class="fas fa-user-circle text-gray-300 text-xs"></i>
                                <span class="text-[10px] font-black text-gray-600 uppercase tracking-widest">{{ $movement->createdBy->name ?? 'System' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white border border-gray-200 rounded-[2rem] p-16 shadow-inner text-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 border border-gray-100">
                        <i class="fas fa-history text-3xl text-gray-200"></i>
                    </div>
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Belum Ada Pergerakan</h3>
                    <p class="text-xs font-bold text-gray-400 mt-2 uppercase tracking-tight">Semua mutasi stok akan muncul di halaman ini.</p>
                </div>
            @endforelse

            {{-- PAGINATION --}}
             @if($movements->hasPages())
                <div class="pt-8 px-4">
                    {{ $movements->links() }}
                </div>
            @endif
        </div>
    </div>
</main>
@endsection
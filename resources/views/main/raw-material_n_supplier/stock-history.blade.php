@extends('layouts.app')

@section('title', 'Riwayat Stok - ' . $rawMaterial->name . ' - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('raw-materials.index') }}" class="text-gray-500 hover:text-red-600 transition-colors">Bahan Baku</a>
</li>
@can('lihat detail bahan baku')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('raw-materials.show', $rawMaterial) }}" class="text-gray-500 hover:text-red-600 transition-colors">{{ $rawMaterial->name }}</a>
</li>
@endcan
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Riwayat</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">
        
        {{-- Header Section --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                 <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-500 border border-red-100">
                        <i class="fas fa-history text-sm"></i>
                    </span>
                    <span>Riwayat Pergerakan Stok</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Lacak semua perubahan stok masuk dan keluar untuk <strong>{{ $rawMaterial->name }}</strong>.
                </p>
            </div>
            <div class="flex items-center gap-3">
                 @can('lihat detail bahan baku')
                 <a href="{{ route('raw-materials.show', $rawMaterial) }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-sm transition-all">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
                @endcan
                @can('kelola stok bahan baku')
                <a href="{{ route('raw-materials.manage-stock', $rawMaterial) }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-sm transition-all">
                    <i class="fas fa-box-open mr-2"></i>
                    Kelola Stok
                </a>
                @endcan
            </div>
        </section>

        {{-- Material Summary --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 flex items-center gap-4">
            @if($rawMaterial->image)
                <img src="{{ Storage::url($rawMaterial->image) }}" alt="{{ $rawMaterial->name }}" class="h-12 w-12 rounded-lg object-cover border border-gray-200">
            @else
                <div class="h-12 w-12 rounded-lg bg-gray-100 flex items-center justify-center border border-gray-200 text-gray-400">
                    <i class="fas fa-cube"></i>
                </div>
            @endif
            <div>
                <h2 class="font-semibold text-gray-900">{{ $rawMaterial->name }}</h2>
                <div class="flex items-center gap-3 text-sm text-gray-500 mt-0.5">
                    <span class="bg-gray-100 px-2 py-0.5 rounded text-xs font-mono">{{ $rawMaterial->code }}</span>
                    <span>Stok Saat Ini: <strong class="text-gray-900">{{ $rawMaterial->stocks->first()->quantity ?? 0 }} {{ $rawMaterial->unit->name }}</strong></span>
                </div>
            </div>
        </section>

        {{-- Timeline --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 relative">
             @if($movements->count() > 0)
                <div class="space-y-8 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-gray-200 before:to-transparent">
                    @foreach($movements as $movement)
                        <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                            
                            {{-- Icon --}}
                            <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 border-white shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 
                                {{ $movement->type === 'in' ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white' }}">
                                <i class="fas {{ $movement->type === 'in' ? 'fa-arrow-down' : 'fa-arrow-up' }} text-sm"></i>
                            </div>

                            {{-- Content Card --}}
                            <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-4 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wide
                                        {{ $movement->type === 'in' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                                        {{ $movement->type === 'in' ? 'Stok Masuk' : 'Stok Keluar' }}
                                    </span>
                                    <span class="text-xs text-gray-400 font-medium">
                                        {{ $movement->created_at->format('d M Y, H:i') }}
                                    </span>
                                </div>
                                
                                <div class="flex items-baseline gap-2 mb-3">
                                    <span class="text-2xl font-bold {{ $movement->type === 'in' ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ $movement->type === 'in' ? '+' : '-' }}{{ number_format($movement->quantity, 2) }}
                                    </span>
                                    <span class="text-sm text-gray-500 font-medium">{{ $rawMaterial->unit->name }}</span>
                                </div>

                                <div class="grid grid-cols-2 gap-2 text-xs text-gray-500 bg-gray-50 rounded-lg p-2.5 border border-gray-100 mb-3">
                                    <div>
                                        <span class="block text-[10px] uppercase text-gray-400 mb-0.5">Sebelum</span>
                                        <span class="font-semibold text-gray-700">{{ number_format($movement->quantity_before, 2) }}</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="block text-[10px] uppercase text-gray-400 mb-0.5">Sesudah</span>
                                        <span class="font-semibold text-gray-700">{{ number_format($movement->quantity_after, 2) }}</span>
                                    </div>
                                </div>

                                @if($movement->notes)
                                    <div class="text-sm text-gray-600 italic border-l-2 border-gray-200 pl-3 py-1">
                                        "{{ $movement->notes }}"
                                    </div>
                                @endif
                                
                                <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
                                    <span class="flex items-center gap-1.5">
                                        <i class="fas fa-user-circle"></i> {{ $movement->createdBy->name ?? 'System' }}
                                    </span>
                                    @if($movement->unit_price > 0)
                                    <span>
                                        @ Rp {{ number_format($movement->unit_price, 0, ',', '.') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                {{-- Pagination --}}
                @if($movements->hasPages())
                <div class="mt-8 pt-6 border-t border-gray-200">
                    {{ $movements->links() }}
                </div>
                @endif
             @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                        <i class="fas fa-history text-3xl text-gray-300"></i>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">Belum Ada Riwayat</h3>
                    <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">
                        Belum ada pergerakan stok tercatat untuk bahan baku ini.
                    </p>
                </div>
             @endif
        </section>

    </div>
</main>
@endsection
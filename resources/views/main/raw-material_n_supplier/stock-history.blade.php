@extends('layouts.app')

@section('title', 'Riwayat Pergerakan Stok - ' . $rawMaterial->name . ' - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('raw-materials.index') }}" class="text-gray-500 hover:text-orange-600 transition-colors">Stok Bahan Baku</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('raw-materials.show', $rawMaterial) }}" class="text-gray-500 hover:text-orange-600 transition-colors">Detail</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Riwayat Pergerakan</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4">
    <div class="max-w-7xl mx-auto">
        
        <x-card-container>
            <!-- Header -->
            <div class="bg-gradient-to-br from-orange-50 to-red-50 p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-history text-red-400 mr-3"></i>
                            Riwayat Pergerakan Stok
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">{{ $rawMaterial->name }}</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <a href="{{ route('raw-materials.show', $rawMaterial) }}" class="inline-flex items-center justify-center px-4 py-2 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition-all shadow-sm border border-gray-200">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali
                        </a>
                        <a href="{{ route('raw-materials.manage-stock', $rawMaterial) }}" class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-orange-400 to-red-500 text-white rounded-lg hover:from-orange-500 hover:to-red-600 transition-all shadow-sm">
                            <i class="fas fa-box-open mr-2"></i>
                            Kelola Stok
                        </a>
                    </div>
                </div>
            </div>

            <!-- Material Info -->
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-center gap-4">
                    @if($rawMaterial->image)
                    <img src="{{ Storage::url($rawMaterial->image) }}" alt="{{ $rawMaterial->name }}" class="h-16 w-16 rounded-lg object-cover border-2 border-gray-200 shadow-sm">
                    @else
                    <div class="h-16 w-16 rounded-lg bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center shadow-sm">
                        <i class="fas fa-cube text-white text-2xl"></i>
                    </div>
                    @endif
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900">{{ $rawMaterial->name }}</h3>
                        <div class="flex flex-wrap gap-3 mt-1">
                            <span class="text-sm text-gray-600">
                                <i class="fas fa-barcode mr-1 text-gray-400"></i>
                                <span class="font-mono font-semibold">{{ $rawMaterial->code }}</span>
                            </span>
                            <span class="text-sm text-gray-600">
                                <i class="fas fa-tag mr-1 text-gray-400"></i>
                                {{ $rawMaterial->category->name ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="p-6">
                @if($movements->count() > 0)
                <div class="space-y-6">
                    @foreach($movements as $movement)
                    <div class="relative pl-8 pb-6 border-l-2 
                        {{ $movement->type === 'in' ? 'border-green-300' : 'border-red-300' }}
                        {{ $loop->last ? 'border-l-0 pb-0' : '' }}">
                        
                        <!-- Icon Badge -->
                        <div class="absolute -left-4 top-0 w-8 h-8 rounded-full flex items-center justify-center shadow-md
                            {{ $movement->type === 'in' ? 'bg-green-500' : 'bg-red-500' }}">
                            <i class="fas {{ $movement->type === 'in' ? 'fa-arrow-down' : 'fa-arrow-up' }} text-white text-sm"></i>
                        </div>

                        <!-- Content -->
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:shadow-md transition-shadow">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                            {{ $movement->type === 'in' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $movement->type === 'in' ? 'MASUK' : 'KELUAR' }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $movement->created_at->format('d M Y, H:i') }}
                                        </span>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                                        <div>
                                            <span class="text-gray-500">Jumlah:</span>
                                            <span class="font-bold ml-1
                                                {{ $movement->type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $movement->type === 'in' ? '+' : '-' }}{{ number_format($movement->quantity, 2) }} {{ $rawMaterial->unit->name ?? '' }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Stok Sebelum:</span>
                                            <span class="font-semibold text-gray-900 ml-1">{{ number_format($movement->quantity_before, 2) }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Stok Sesudah:</span>
                                            <span class="font-semibold text-gray-900 ml-1">{{ number_format($movement->quantity_after, 2) }}</span>
                                        </div>
                                    </div>

                                    @if($movement->batch_number || $movement->expired_at)
                                    <div class="flex flex-wrap gap-3 mt-2 text-xs">
                                        @if($movement->batch_number)
                                        <span class="text-gray-600">
                                            <i class="fas fa-barcode mr-1"></i>
                                            Batch: <span class="font-mono font-semibold">{{ $movement->batch_number }}</span>
                                        </span>
                                        @endif
                                        @if($movement->expired_at)
                                        <span class="text-gray-600">
                                            <i class="fas fa-calendar-times mr-1"></i>
                                            Exp: <span class="font-semibold">{{ $movement->expired_at->format('d M Y') }}</span>
                                        </span>
                                        @endif
                                    </div>
                                    @endif

                                    @if($movement->notes)
                                    <div class="mt-2 p-2 bg-white rounded border border-gray-200">
                                        <p class="text-xs text-gray-600">
                                            <i class="fas fa-sticky-note mr-1 text-gray-400"></i>
                                            {{ $movement->notes }}
                                        </p>
                                    </div>
                                    @endif
                                </div>

                                <div class="flex items-center gap-3">
                                    @if($movement->createdBy)
                                    <div class="text-right text-xs text-gray-500">
                                        <i class="fas fa-user mr-1"></i>
                                        {{ $movement->createdBy->name }}
                                    </div>
                                    @endif
                                    
                                    @if($movement->unit_price > 0)
                                    <div class="text-right">
                                        <div class="text-xs text-gray-500">Harga Satuan</div>
                                        <div class="text-sm font-bold text-gray-900">
                                            Rp {{ number_format($movement->unit_price, 0, ',', '.') }}
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($movements->hasPages())
                <div class="mt-6 pt-6 border-t border-gray-200">
                    {{ $movements->links() }}
                </div>
                @endif

                @else
                <div class="text-center py-16">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-history text-5xl text-gray-300"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Riwayat</h3>
                    <p class="text-sm text-gray-500 mb-6">Belum ada pergerakan stok untuk bahan baku ini</p>
                    <a href="{{ route('raw-materials.manage-stock', $rawMaterial) }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-gradient-to-r from-orange-400 to-red-500 text-white rounded-lg font-semibold hover:from-orange-500 hover:to-red-600 transition-all shadow-md">
                        <i class="fas fa-box-open mr-2"></i>
                        Kelola Stok
                    </a>
                </div>
                @endif
            </div>

        </x-card-container>

    </div>
</main>
@endsection
@extends('layouts.app')

@section('title', 'Persiapan Produksi - ' . ($product->name ?? 'Detail'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('production.index') }}" class="text-gray-500 hover:text-gray-700 font-medium">Produksi</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Persiapan: {{ $product->name }}</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-6xl mx-auto space-y-6">
        
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                @if($product->image)
                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-20 h-20 rounded-xl object-cover border border-gray-200 shadow-sm">
                @else
                <div class="w-20 h-20 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-sm">
                    <i class="fas fa-utensils text-2xl"></i>
                </div>
                @endif
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
                    <div class="flex items-center gap-3 mt-1 text-sm text-gray-500">
                        <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded border border-blue-100 font-mono text-xs">{{ $saleItem->sale->invoice_number }}</span>
                        <span>&bull;</span>
                        <span class="font-medium">{{ $saleItem->quantity }} {{ $product->unit->name ?? 'Pcs' }}</span>
                    </div>
                </div>
            </div>

            <!-- Action Form -->
            <form action="{{ route('production.store') }}" method="POST" class="flex flex-col sm:flex-row items-end sm:items-center gap-3 w-full md:w-auto">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="sale_item_id" value="{{ $saleItem->id }}">
                
                <div class="w-full sm:w-32">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Jumlah Masak</label>
                    <div class="relative">
                        <input type="number" name="planned_quantity" value="{{ (int) $saleItem->quantity }}" 
                            class="w-full pl-3 pr-8 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-900">
                        <span class="absolute right-3 top-2 text-gray-400 text-xs font-medium">{{ $product->unit->name }}</span>
                    </div>
                </div>

                <div class="w-full sm:w-auto h-10 flex items-end"> <!-- Align with input -->
                    <button type="submit" class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-fire-alt"></i>
                        <span>Masak Sekarang</span>
                    </button>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Ingredients -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                        <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-carrot text-orange-500"></i>
                            Bahan Baku Diperlukan
                        </h2>
                        <span class="text-xs text-gray-500">Estimasi untuk {{ $saleItem->quantity }} {{ $product->unit->name }}</span>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-3">Bahan Baku</th>
                                    <th class="px-6 py-3 text-right">Per Unit</th>
                                    <th class="px-6 py-3 text-right">Total Butuh</th>
                                    <th class="px-6 py-3 text-right">Stok Tersedia</th>
                                    <th class="px-6 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($materials as $mat)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        {{ $mat['raw_material']->name }}
                                        <div class="text-xs text-gray-400 font-normal">{{ $mat['raw_material']->code }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right text-gray-600">
                                        {{ number_format($mat['required_per_recipe'], 2) }} {{ $mat['unit'] }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-semibold text-gray-800">
                                        {{ number_format($mat['required_total'], 2) }} {{ $mat['unit'] }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="{{ $mat['is_sufficient'] ? 'text-green-600' : 'text-red-600 font-bold' }}">
                                            {{ number_format($mat['available'], 2) }}
                                        </span>
                                        <span class="text-gray-400 text-xs ml-1">{{ $mat['unit'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($mat['is_sufficient'])
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-600">
                                            <i class="fas fa-check text-xs"></i>
                                        </span>
                                        @else
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-600 relative group cursor-help">
                                            <i class="fas fa-times text-xs"></i>
                                            <div class="absolute bottom-full mb-2 hidden group-hover:block w-32 bg-gray-900 text-white text-xs rounded py-1 px-2 z-10">
                                                Stok Kurang {{ number_format($mat['required_total'] - $mat['available'], 2) }}
                                            </div>
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">
                                        Tidak ada bahan baku yang terdaftar di resep.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Notes Section -->
                @if($saleItem->notes)
                <div class="bg-amber-50 rounded-xl border border-amber-200 p-5 flex gap-4">
                    <i class="fas fa-sticky-note text-amber-500 mt-1"></i>
                    <div>
                        <h3 class="font-bold text-amber-800 text-sm">Catatan Pesanan</h3>
                        <p class="text-amber-900 mt-1 text-sm">{{ $saleItem->notes }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column: Recipe Info -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden h-full">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                        <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-book-open text-blue-500"></i>
                            Instruksi Resep
                        </h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between text-sm pb-4 border-b border-gray-100">
                            <span class="text-gray-500">Estimasi Waktu</span>
                            <span class="font-medium text-gray-900"><i class="far fa-clock mr-1"></i> {{ $recipe->estimated_time_minutes ?? 15 }} Menit</span>
                        </div>
                        
                        <div>
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Cara Membuat</h3>
                            <div class="prose prose-sm text-gray-700">
                                @if($recipe->instructions)
                                    {!! nl2br(e($recipe->instructions)) !!}
                                @else
                                    <p class="text-gray-400 italic">Belum ada instruksi khusus.</p>
                                @endif
                            </div>
                        </div>

                        @if(Auth::user()->can('lihat hpp'))
                        <div class="pt-4 mt-4 border-t border-gray-100">
                            <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg border border-gray-200">
                                <span class="text-xs font-semibold text-gray-500">Estimasi HPP Total</span>
                                @php
                                    $estHpp = collect($materials)->sum(function($m) {
                                        return $m['required_total'] * ($m['raw_material']->purchase_price ?? 0);
                                    });
                                @endphp
                                <span class="text-sm font-bold text-gray-900">Rp {{ number_format($estHpp, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>
@endsection

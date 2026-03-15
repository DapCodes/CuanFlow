@extends('layouts.app')

@section('title', 'Persiapan Masak - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('production.index') }}" class="text-gray-500 hover:text-cuan-green transition-colors font-medium">Produksi</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Persiapan Masak</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-5xl mx-auto space-y-6">
        
        {{-- HEADER (Strictly matched employees/show/index) --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="flex items-center gap-6">
                 <div class="w-20 h-20 rounded-[2rem] bg-white border border-gray-100 flex items-center justify-center text-gray-400 shadow-xl shadow-gray-200/50">
                    <i class="fas fa-utensils text-3xl"></i>
                </div>
                <div>
                     <h1 class="text-2xl md:text-3xl font-black text-gray-900 tracking-tight">
                        {{ $saleItem->product->name }}
                    </h1>
                    <div class="flex items-center gap-3 mt-2">
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{ $saleItem->sale->invoice_number }}</span>
                        <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                        <span class="text-[10px] font-black uppercase tracking-widest text-cuan-green">Order Qty: {{ (int)$saleItem->quantity }} {{ $saleItem->product->unit->name }}</span>
                    </div>
                    
                    @if($saleItem->notes)
                    <div class="mt-4 p-4 bg-amber-50 border border-amber-100 rounded-2xl flex items-start gap-3">
                        <i class="fas fa-sticky-note text-amber-400 mt-1"></i>
                        <div class="flex-1">
                            <p class="text-[10px] font-black uppercase tracking-widest text-amber-600 mb-1">Catatan Pesanan:</p>
                            <p class="text-xs font-bold text-gray-700 leading-relaxed italic whitespace-pre-line">{{ $saleItem->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3">
                 <a href="{{ route('production.index') }}"
                   class="px-5 py-3 border border-gray-200 bg-white text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-50 transition-all active:scale-95 shadow-sm">
                    Kembali
                </a>
                <form action="{{ route('production.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $saleItem->product_id }}">
                    <input type="hidden" name="planned_quantity" value="{{ $saleItem->quantity }}">
                    <input type="hidden" name="sale_item_id" value="{{ $saleItem->id }}">
                    <button type="submit" class="px-5 py-3 bg-cuan-green text-white rounded-xl font-black text-sm hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                        Mulai Masak
                    </button>
                </form>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            {{-- LEFT: INGREDIENTS --}}
            <div class="lg:col-span-12">
                <x-card-container>
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Bahan Baku yang Diperlukan</h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Cek ketersediaan stok di dapur</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest border-b border-gray-100">
                                <tr>
                                    <th class="px-8 py-4 text-left">Nama Bahan</th>
                                    <th class="px-8 py-4 text-center">Takaran per Unit</th>
                                    <th class="px-8 py-4 text-right">Total Kebutuhan</th>
                                    <th class="px-8 py-4 text-right">Stok Dapur</th>
                                    <th class="px-8 py-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 bg-white">
                                @php
                                    $recipe = $saleItem->product->defaultRecipe;
                                    $multiplier = $recipe && $recipe->output_quantity > 0 ? ($saleItem->quantity / $recipe->output_quantity) : 0;
                                @endphp
                                @if($recipe)
                                    @forelse($recipe->items as $item)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-8 py-5">
                                            <div class="text-sm font-black text-gray-900 leading-none">{{ $item->rawMaterial->name }}</div>
                                            <div class="text-[9px] font-black uppercase text-gray-300 font-mono tracking-tighter mt-1.5">#{{ $item->rawMaterial->code }}</div>
                                        </td>
                                        <td class="px-8 py-5 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                            {{ number_format($item->quantity, 2) }} / {{ $recipe->output_quantity }}
                                        </td>
                                        <td class="px-8 py-5 text-right whitespace-nowrap">
                                            <span class="text-sm font-black text-gray-900">{{ number_format($item->quantity * $multiplier, 2) }}</span>
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">{{ $item->rawMaterial->unit->name }}</span>
                                        </td>
                                        @php
                                            $stockInOutlet = $item->rawMaterial->getStockQuantity(auth()->user()->outlet_id);
                                            $isSufficient = $stockInOutlet >= ($item->quantity * $multiplier);
                                        @endphp
                                        <td class="px-8 py-5 text-right whitespace-nowrap">
                                            <span class="text-sm font-black {{ $isSufficient ? 'text-gray-900' : 'text-red-500' }}">
                                                {{ number_format($stockInOutlet, 2) }}
                                            </span>
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">{{ $item->rawMaterial->unit->name }}</span>
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            @if($isSufficient)
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-cuan-green/10 text-cuan-green border border-cuan-green/20">
                                                    <i class="fas fa-check text-[10px]"></i>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-red-50 text-red-500 border border-red-100">
                                                    <i class="fas fa-times text-[10px]"></i>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="px-8 py-10 text-center text-gray-400 font-bold uppercase tracking-widest text-[9px]">Resep tidak memiliki item bahan baku.</td>
                                    </tr>
                                    @endforelse
                                @else
                                    <tr>
                                        <td colspan="5" class="px-8 py-10 text-center text-gray-400 font-bold uppercase tracking-widest text-[9px]">Produk ini tidak memiliki resep terdaftar.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </x-card-container>
            </div>

            {{-- BOTTOM: INSTRUCTIONS --}}
            <div class="lg:col-span-12 space-y-4">
                <div class="flex items-center justify-between">
                     <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Instruksi Pembuatan</h2>
                     <span class="px-3 py-1 bg-white border border-gray-200 text-[10px] font-black uppercase tracking-widest text-gray-400 rounded-lg shadow-sm">
                         SOP
                     </span>
                </div>
                
                <x-card-container class="p-8">
                     @if($recipe && $recipe->instructions)
                        <div class="prose prose-sm max-w-none text-gray-600 font-bold leading-relaxed">
                            {!! nl2br(e($recipe->instructions)) !!}
                        </div>
                     @else
                        <div class="py-10 flex flex-col items-center justify-center opacity-40">
                            <i class="fas fa-book-spells text-3xl text-gray-300 mb-2"></i>
                            <span class="text-[10px] font-black uppercase text-gray-400">Instruksi SOP belum ditambahkan.</span>
                        </div>
                     @endif
                </x-card-container>
            </div>
        </div>

    </div>
</main>
@endsection

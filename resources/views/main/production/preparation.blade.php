@extends('layouts.app')

@section('title', 'Persiapan Masak - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('production.index') }}" class="text-gray-500 hover:text-cuan-green transition-colors font-medium">Produksi</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-black tracking-tight">Eksplorasi Resep</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-5xl mx-auto space-y-8">
        
        {{-- HEADER --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-8">
            <div class="flex items-center gap-6">
                 <div class="w-14 h-14 rounded-2xl bg-white border border-gray-100 flex items-center justify-center text-gray-400 shadow-sm">
                    <i class="fas fa-hat-chef text-xl"></i>
                </div>
                <div>
                     <h1 class="text-xl md:text-2xl font-black text-gray-900 tracking-tight">
                        Persiapan: {{ $saleItem->product->name }}
                    </h1>
                    <div class="flex items-center gap-4 mt-1.5 font-bold uppercase tracking-widest text-[9px]">
                        <span class="text-gray-400">{{ $saleItem->sale->invoice_number }}</span>
                        <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                        <span class="text-cuan-green">Order Qty: {{ (int)$saleItem->quantity }} {{ $saleItem->product->unit->name }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <form action="{{ route('production.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $saleItem->product_id }}">
                    <input type="hidden" name="planned_quantity" value="{{ $saleItem->quantity }}">
                    <input type="hidden" name="sale_item_id" value="{{ $saleItem->id }}">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-cuan-green px-6 py-4 text-[11px] font-black uppercase tracking-widest text-white hover:bg-cuan-dark transition-all active:scale-95 shadow-lg shadow-cuan-green/20">
                        <i class="fas fa-fire-burner"></i>
                        Mulai Masak Sekarang
                    </button>
                </form>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            {{-- LEFT: INGREDIENTS --}}
            <div class="lg:col-span-12">
                <x-card-container>
                    <div class="px-8 py-6 border-b border-gray-100 bg-white">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest flex items-center gap-3">
                            <i class="fas fa-basket-shopping text-gray-300"></i>
                            Bahan Baku yang Diperlukan
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest border-b border-gray-100">
                                <tr>
                                    <th class="px-8 py-4 text-left">Nama Bahan</th>
                                    <th class="px-8 py-4 text-center">Takaran per Unit</th>
                                    <th class="px-8 py-4 text-center">Total Dibutuhkan</th>
                                    <th class="px-8 py-4 text-right">Stok Dapur</th>
                                    <th class="px-8 py-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 bg-white">
                                @php
                                    $recipe = $saleItem->product->defaultRecipe;
                                    $multiplier = $saleItem->quantity / $recipe->output_quantity;
                                @endphp
                                @foreach($recipe->materials as $mat)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-8 py-5">
                                        <div class="text-sm font-black text-gray-900">{{ $mat->rawMaterial->name }}</div>
                                        <div class="text-[9px] font-black uppercase text-gray-300 font-mono tracking-tighter mt-1">#{{ $mat->rawMaterial->code }}</div>
                                    </td>
                                    <td class="px-8 py-5 text-center text-[10px] font-bold text-gray-500 uppercase tracking-widest">
                                        {{ number_format($mat->amount, 2) }} / {{ $recipe->output_quantity }} {{ $saleItem->product->unit->name }}
                                    </td>
                                    <td class="px-8 py-5 text-center text-sm font-black text-gray-900">
                                        {{ number_format($mat->amount * $multiplier, 2) }} {{ $mat->rawMaterial->unit->name }}
                                    </td>
                                    @php
                                        $stockInOutlet = $mat->rawMaterial->outletStocks->where('outlet_id', auth()->user()->outlet_id)->sum('quantity');
                                        $isSufficient = $stockInOutlet >= ($mat->amount * $multiplier);
                                    @endphp
                                    <td class="px-8 py-5 text-right">
                                        <span class="text-sm font-black {{ $isSufficient ? 'text-cuan-green' : 'text-red-500' }}">
                                            {{ number_format($stockInOutlet, 2) }}
                                        </span>
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">{{ $mat->rawMaterial->unit->name }}</span>
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-8 py-6 bg-gray-50 border-t border-gray-100">
                        <p class="text-[9px] font-bold text-gray-400 italic">
                            * Stok bahan baku di atas adalah stok realtime di outlet Anda ({{ auth()->user()->outlet->name }}).
                        </p>
                    </div>
                </x-card-container>
            </div>

            {{-- BOTTOM: STEPS --}}
            <div class="lg:col-span-12 space-y-6">
                <div class="flex items-center justify-between mb-4">
                     <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">Langkah-langkah Pembuatan</h3>
                     <span class="inline-flex items-center px-3 py-1 bg-white border border-gray-200 text-[10px] font-black uppercase tracking-widest text-gray-400 rounded-xl shadow-sm">
                         {{ $recipe->steps->count() }} Tahap
                     </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($recipe->steps->sortBy('step_order') as $step)
                    <div class="p-8 bg-white border border-gray-100 rounded-[2.5rem] shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group">
                         <div class="flex items-center justify-between mb-6">
                             <div class="w-10 h-10 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-300 font-black text-xs group-hover:bg-cuan-green group-hover:text-white transition-all shadow-inner">
                                 {{ $step->step_order }}
                             </div>
                             <div class="text-[10px] font-black uppercase tracking-widest text-gray-300">Tahap {{ $step->step_order }}</div>
                         </div>
                         <h4 class="text-sm font-black text-gray-900 mb-2 leading-tight">{{ $step->instruction }}</h4>
                         <p class="text-xs font-bold text-gray-500 leading-relaxed italic opacity-80">Pastikan instruksi diikuti dengan presisi untuk menjaga standarisasi rasa.</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</main>
@endsection

@extends('layouts.app')

@section('title', 'Buat Transfer Stok - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('stock-transfers.index') }}" class="text-gray-600 hover:text-gray-900 font-medium">Transfer Stok</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Buat Transfer Baru</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Buat Transfer Stok
                </h1>
                <p class="mt-1 text-sm text-gray-500 font-medium capitalize">
                    Pindahkan stok bahan baku atau produk ke outlet lain.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('stock-transfers.index') }}" class="inline-flex items-center justify-center h-11 px-6 bg-white text-gray-700 border border-gray-200 rounded-xl text-sm font-black hover:bg-gray-50 transition-all active:scale-95 shadow-sm">
                    Kembali
                </a>
            </div>
        </section>

        {{-- FORM CARD --}}
        <form action="{{ route('stock-transfers.store') }}" method="POST" x-data="stockTransferForm()" class="space-y-6">
            @csrf

            {{-- Konfigurasi Dasar --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-visible">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/30 rounded-t-xl">
                    <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest">1. Konfigurasi Dasar</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    {{-- Custom Outlet Selection --}}
                    <div x-data="{ open: false, search: '' }">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Outlet Tujuan <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <button type="button" @click="open = !open" 
                                class="w-full h-[46px] px-4 rounded-xl border border-gray-200 text-sm font-bold bg-white text-left flex justify-between items-center focus:outline-none focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all">
                                <span x-text="selectedOutletId ? outletList.find(o => o.id == selectedOutletId)?.name : '-- Pilih Outlet --'"
                                    :class="selectedOutletId ? 'text-gray-900' : 'text-gray-400'"></span>
                                <i class="fas fa-chevron-down text-[10px] text-gray-300 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>

                            <div x-show="open" @click.away="open = false" 
                                class="absolute z-[100] w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-2xl overflow-hidden p-2 space-y-1"
                                style="display: none;"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0">
                                
                                <div class="px-2 py-1 mb-1">
                                    <input type="text" x-model="search" placeholder="Cari outlet..." 
                                        class="w-full px-3 py-2 text-xs border-none bg-gray-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green/20 placeholder:text-gray-300">
                                </div>

                                <div class="max-h-60 overflow-y-auto space-y-0.5 custom-scrollbar">
                                    <template x-for="o in outletList.filter(o => o.name.toLowerCase().includes(search.toLowerCase()))" :key="o.id">
                                        <div @click="selectedOutletId = o.id; open = false; search = ''" 
                                            class="flex items-center justify-between p-2.5 hover:bg-cuan-green/5 rounded-xl cursor-pointer transition-colors group"
                                            :class="selectedOutletId == o.id ? 'bg-cuan-green/5' : ''">
                                            <span class="text-xs font-black text-gray-800 capitalize" x-text="o.name"></span>
                                            <i class="fas fa-check text-cuan-green text-[10px]" x-show="selectedOutletId == o.id"></i>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <input type="hidden" name="to_outlet_id" :value="selectedOutletId" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Catatan (Opsional)</label>
                        <input type="text" name="notes" class="w-full h-[46px] px-4 rounded-xl border border-gray-200 text-sm font-bold text-gray-900 focus:outline-none focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all placeholder:text-gray-300 placeholder:font-medium" placeholder="Contoh: Stok tambahan weekend">
                    </div>
                </div>
            </div>

            {{-- Item Transfer --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-visible">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/30 rounded-t-xl">
                    <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest">2. Item Transfer</h3>
                    <button type="button" @click="addItem()" class="inline-flex items-center gap-2 px-4 py-2 bg-cuan-green/10 text-cuan-green rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-cuan-green/20 transition-all active:scale-95">
                        <i class="fas fa-plus"></i> Tambah Item
                    </button>
                </div>

                <div class="p-4 md:p-6 space-y-4">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end bg-gray-50/50 p-6 rounded-2xl border border-gray-100 relative group transition-all hover:border-cuan-green/30">
                            {{-- Remove Button --}}
                            <button type="button" @click="removeItem(index)" class="absolute -top-3 -right-3 w-8 h-8 bg-white text-red-500 rounded-full hover:bg-red-50 hover:text-red-600 flex items-center justify-center shadow-lg border border-red-50 opacity-0 group-hover:opacity-100 transition-all z-10" x-show="items.length > 1">
                                <i class="fas fa-times text-xs"></i>
                            </button>

                            {{-- Custom Type Selection --}}  
                            <div class="md:col-span-2" x-data="{ open: false }">
                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Tipe</label>
                                <div class="relative">
                                    <button type="button" @click="open = !open" 
                                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-[11px] font-bold bg-white text-left flex justify-between items-center focus:outline-none focus:ring-4 focus:ring-cuan-green/5 transition-all">
                                        <span x-text="item.type === 'product' ? 'Produk' : 'Bahan Baku'" class="text-gray-900"></span>
                                        <i class="fas fa-chevron-down text-[8px] text-gray-300"></i>
                                    </button>
                                    <div x-show="open" @click.away="open = false" 
                                        class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-2xl overflow-hidden p-1 space-y-0.5"
                                        style="display: none;"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 translate-y-2"
                                        x-transition:enter-end="opacity-100 translate-y-0">
                                        <div @click="item.type = 'product'; item.id = ''; item.selected_batches = []; open = false" 
                                            class="p-2.5 hover:bg-cuan-green/5 rounded-xl cursor-pointer text-[10px] font-black uppercase tracking-widest transition-colors"
                                            :class="item.type === 'product' ? 'text-cuan-green bg-cuan-green/5' : 'text-gray-600'">Produk</div>
                                        <div @click="item.type = 'raw_material'; item.id = ''; item.selected_batches = []; open = false" 
                                            class="p-2.5 hover:bg-cuan-green/5 rounded-xl cursor-pointer text-[10px] font-black uppercase tracking-widest transition-colors"
                                            :class="item.type === 'raw_material' ? 'text-cuan-green bg-cuan-green/5' : 'text-gray-600'">Bahan Baku</div>
                                    </div>
                                    <input type="hidden" :name="'items[' + index + '][type]'" :value="item.type">
                                </div>
                            </div>

                            {{-- Item Selection --}}
                            <div class="md:col-span-4" x-data="{ open: false, search: '' }">
                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Pilih Barang</label>
                                <div class="relative">
                                    <button type="button" @click="open = !open" 
                                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-[11px] font-bold bg-white text-left flex justify-between items-center focus:ring-4 focus:ring-cuan-green/5 transition-all">
                                        <span x-text="item.id ? (item.type === 'product' ? products.find(p => p.id == item.id)?.name : rawMaterials.find(rm => rm.id == item.id)?.name) : '-- Pilih --'"
                                            class="truncate text-gray-900"></span>
                                        <i class="fas fa-chevron-down text-[8px] text-gray-300"></i>
                                    </button>

                                    <div x-show="open" @click.away="open = false" 
                                        class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-2xl overflow-hidden p-2 space-y-1"
                                        style="display: none;"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 translate-y-2"
                                        x-transition:enter-end="opacity-100 translate-y-0">
                                        
                                        <div class="px-2 py-1 mb-1">
                                            <input type="text" x-model="search" placeholder="Cari barang..." 
                                                class="w-full px-3 py-2 text-[11px] border-none bg-gray-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green/20 placeholder:text-gray-300">
                                        </div>

                                        <div class="max-h-60 overflow-y-auto space-y-0.5 custom-scrollbar">
                                            <template x-for="p in (item.type === 'product' ? products : rawMaterials).filter(i => i.name.toLowerCase().includes(search.toLowerCase()))" :key="p.id">
                                                <div @click="item.id = p.id; item.selected_batches = []; open = false; search = ''" 
                                                    class="flex items-center justify-between p-2.5 hover:bg-cuan-green/5 rounded-xl cursor-pointer transition-colors group"
                                                    :class="item.id == p.id ? 'bg-cuan-green/5' : ''">
                                                    <div class="flex flex-col min-w-0">
                                                        <span class="text-[11px] font-black text-gray-800 capitalize" x-text="p.name"></span>
                                                        <span class="text-[9px] font-bold text-gray-400" x-text="'Tersedia: ' + p.stock + ' ' + (p.unit_name || '')"></span>
                                                    </div>
                                                    <i class="fas fa-check text-cuan-green text-[10px]" x-show="item.id == p.id"></i>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    <input type="hidden" :name="'items[' + index + '][id]'" :value="item.id" required>
                                </div>
                            </div>

                            {{-- Batch Selection --}}
                            <div class="md:col-span-4" x-data="{ open: false }">
                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Pilih Batch (Opsional)</label>
                                <div class="relative">
                                    <button type="button" @click="open = !open" 
                                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-[11px] font-bold bg-white text-left flex justify-between items-center focus:ring-4 focus:ring-cuan-green/5 transition-all">
                                        <span x-text="item.selected_batches && item.selected_batches.length > 0 
                                            ? item.selected_batches.length + ' Batch Terpilih' 
                                            : 'Otomatis (FIFO)'"
                                            class="truncate" :class="item.selected_batches.length > 0 ? 'text-gray-900' : 'text-gray-400'"></span>
                                        <i class="fas fa-chevron-down text-[8px] text-gray-300"></i>
                                    </button>

                                    <div x-show="open" @click.away="open = false" 
                                        class="absolute z-50 w-[300px] md:w-[400px] mt-2 bg-white border border-gray-100 rounded-2xl shadow-2xl overflow-hidden p-3 space-y-3"
                                        style="display: none;"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 translate-y-2"
                                        x-transition:enter-end="opacity-100 translate-y-0">
                                        
                                        <div class="flex justify-between items-center border-b border-gray-50 pb-2">
                                            <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">Pilih Batch Spesifik</span>
                                            <button type="button" @click="item.selected_batches = []" class="text-[9px] font-black uppercase tracking-widest text-cuan-green hover:underline">Reset</button>
                                        </div>

                                        <div class="max-h-56 overflow-y-auto space-y-1.5 custom-scrollbar">
                                            <template x-if="!item.id">
                                                <div class="py-10 text-center">
                                                    <p class="text-[10px] font-bold text-gray-400 italic">Silahkan pilih barang terlebih dahulu</p>
                                                </div>
                                            </template>
                                            
                                            <template x-if="item.id">
                                                <template x-for="b in (item.type === 'product' ? (products.find(p => p.id == item.id)?.batches || []) : (rawMaterials.find(rm => rm.id == item.id)?.batches || []))" :key="b.batch_number">
                                                    <label class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-xl cursor-pointer transition-all border border-transparent hover:border-gray-100"
                                                        :class="item.selected_batches.includes(b.batch_number) ? 'bg-cuan-green/5 border-cuan-green/10' : ''">
                                                        <input type="checkbox" :value="b.batch_number" x-model="item.selected_batches" class="w-4 h-4 text-cuan-green border-gray-200 rounded-lg focus:ring-cuan-green/20">
                                                        
                                                        <div class="flex-grow min-w-0">
                                                            <div class="flex items-center justify-between mb-1">
                                                                <span class="text-[11px] font-black text-gray-800" x-text="'#' + b.batch_number"></span>
                                                                <span class="text-[8px] px-2 py-0.5 rounded-full font-black uppercase tracking-widest" 
                                                                    :class="{
                                                                        'bg-red-50 text-red-500': b.status === 'Kadaluarsa',
                                                                        'bg-yellow-50 text-yellow-500': b.status === 'Akan Kadaluarsa',
                                                                        'bg-cuan-green/10 text-cuan-green': b.status === 'Aman'
                                                                    }" x-text="b.status"></span>
                                                            </div>
                                                            <div class="flex items-center gap-3 text-[9px] text-gray-400 font-bold">
                                                                <span>Stok: <span class="text-gray-900" x-text="b.qty"></span></span>
                                                                <span class="text-gray-200">|</span>
                                                                <span>EXP: <span :class="b.status === 'Kadaluarsa' ? 'text-red-500' : 'text-gray-900'" x-text="b.expired_at"></span></span>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </template>
                                            </template>
                                        </div>
                                    </div>
                                    <template x-for="batchNo in item.selected_batches" :key="batchNo">
                                        <input type="hidden" :name="'items[' + index + '][selected_batches][]'" :value="batchNo">
                                    </template>
                                </div>
                            </div>

                            {{-- Quantity --}}
                            <div class="md:col-span-2">
                                <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Jumlah</label>
                                <input type="number" step="any" x-model="item.quantity" :name="'items[' + index + '][quantity]'" required min="0.0001" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-[11px] font-black text-gray-900 focus:ring-4 focus:ring-cuan-green/5 focus:border-cuan-green transition-all" placeholder="0">
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Footer Actions --}}
            <div class="pt-4">
                <div class="flex flex-col md:flex-row md:justify-end gap-3">
                    <button type="submit" class="w-full md:w-auto h-12 px-10 bg-cuan-green text-white rounded-xl text-sm font-black hover:bg-cuan-dark transition-all active:scale-95 shadow-lg shadow-cuan-green/20">
                        Simpan Draft Transfer
                    </button>
                </div>
                <div class="mt-4 flex items-center justify-end gap-2 text-gray-400">
                    <i class="fas fa-info-circle text-[10px]"></i>
                    <p class="text-[10px] font-bold uppercase tracking-widest">Stok tidak akan berkurang sebelum diproses kirim.</p>
                </div>
            </div>

        </form>
    </div>
</main>

@push('scripts')
<script>
    function stockTransferForm() {
        return {
            selectedOutletId: '',
            outletList: {!! json_encode($outlets->map(fn($o) => ['id' => $o->id, 'name' => $o->name])) !!},
            items: [
                { type: 'raw_material', id: '', selected_batches: [], quantity: '' }
            ],
            products: {!! json_encode($products->map(function($p) use ($productBatches) { 
                $batches = collect($productBatches->get($p->id, []))->map(function($batch) {
                    $qty = $batch->actual_quantity - $batch->waste_quantity;
                    if ($qty <= 0) return null;
                    
                    $status = 'Aman';
                    if ($batch->expired_at) {
                        $days = now()->diffInDays($batch->expired_at, false);
                        if ($days < 0) $status = 'Kadaluarsa';
                        elseif ($days <= 7) $status = 'Akan Kadaluarsa';
                    }

                    return [
                        'batch_number' => $batch->batch_number,
                        'qty' => number_format($qty, 2),
                        'expired_at' => $batch->expired_at ? date('d M Y', strtotime($batch->expired_at)) : '-',
                        'status' => $status,
                    ];
                })->filter()->values();

                return [
                    'id' => $p->id, 
                    'name' => $p->name, 
                    'stock' => $p->getStockQuantity(auth()->user()->outlet_id),
                    'batches' => $batches
                ]; 
            })) !!},
            rawMaterials: {!! json_encode($rawMaterials->map(function($rm) { 
                $batches = collect($rm->purchaseItems)->map(function($batch) {
                    $qty = $batch->remaining_quantity;
                    if ($qty <= 0) return null;
                    
                    $status = 'Aman';
                    if ($batch->expired_at) {
                        $days = now()->diffInDays($batch->expired_at, false);
                        if ($days < 0) $status = 'Kadaluarsa';
                        elseif ($days <= 7) $status = 'Akan Kadaluarsa';
                    }

                    return [
                        'batch_number' => $batch->batch_number,
                        'qty' => number_format($qty, 2),
                        'expired_at' => $batch->expired_at ? date('d M Y', strtotime($batch->expired_at)) : '-',
                        'status' => $status,
                    ];
                })->filter()->values();

                return [
                    'id' => $rm->id, 
                    'name' => $rm->name, 
                    'stock' => $rm->getStockQuantity(auth()->user()->outlet_id), 
                    'unit_name' => optional($rm->unit)->name ?? '',
                    'batches' => $batches
                ]; 
            })) !!},
            
            addItem() {
                this.items.push({ type: 'raw_material', id: '', selected_batches: [], quantity: '' });
            },
            
            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            }
        }
    }
</script>
@endpush
@endsection
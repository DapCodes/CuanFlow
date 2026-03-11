@extends('layouts.app')

@section('title', 'Buat Transfer Stok - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('stock-transfers.index') }}" class="text-gray-500 hover:text-gray-700">Transfer Stok</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Buat Transfer Baru</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-cyan-50 text-cyan-600 border border-cyan-100">
                        <i class="fas fa-plus-circle text-sm"></i>
                    </span>
                    <span>Buat Transfer Stok</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Pindahkan stok bahan baku atau produk ke outlet lain dalam satu manajemen.
                </p>
            </div>
        </section>

        {{-- FORM CARD --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            @if ($errors->any())
                <div class="mb-4 mx-6 mt-6 p-4 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
                    <div class="flex items-center gap-2 mb-2 font-semibold">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Terjadi Kesalahan Input</span>
                    </div>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('stock-transfers.store') }}" method="POST" x-data="stockTransferForm()" class="px-4 md:px-6 py-6 space-y-8">
                @csrf

                {{-- Tujuan Transfer --}}
                <div>
                    <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-cyan-100 text-cyan-600 text-xs shadow-sm">1</span>
                            <span>Tujuan Transfer</span>
                        </h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Outlet Tujuan <span class="text-red-500">*</span></label>
                            <select name="to_outlet_id" required class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                                <option value="">-- Pilih Outlet --</option>
                                @foreach($outlets as $outlet)
                                    <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan (Opsional)</label>
                            <input type="text" name="notes" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400" placeholder="Contoh: Stok tambahan weekend">
                        </div>
                    </div>
                </div>

                {{-- Item Transfer --}}
                <div>
                    <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-cyan-100 text-cyan-600 text-xs shadow-sm">2</span>
                            <span>Item yang Ditransfer</span>
                        </h3>
                        <button type="button" @click="addItem()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-cyan-50 text-cyan-700 border border-cyan-100 rounded-lg text-xs font-semibold hover:bg-cyan-100 transition-colors">
                            <i class="fas fa-plus"></i> Tambah Item
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start bg-gray-50 p-4 rounded-lg border border-gray-200 relative group transition-all hover:shadow-sm hover:border-cyan-200">
                                {{-- Remove Button --}}
                                <button type="button" @click="removeItem(index)" class="absolute -top-2 -right-2 w-6 h-6 bg-red-100 text-red-500 rounded-full hover:bg-red-200 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity z-10" x-show="items.length > 1">
                                    <i class="fas fa-times text-xs"></i>
                                </button>

                                {{-- Type Select --}}  
                                <div class="col-span-12 md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Tipe</label>
                                    <select x-model="item.type" :name="'items[' + index + '][type]'" @change="item.batch_number = ''; item.id = ''" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 bg-white">
                                        <option value="product">Produk Jadi</option>
                                        <option value="raw_material">Bahan Baku</option>
                                    </select>
                                </div>

                                {{-- Item Select --}}
                                <div class="col-span-12 md:col-span-4" x-data="{ open: false, search: '' }">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Pilih Item</label>
                                    <div class="relative">
                                        {{-- Toggle Button --}}
                                        <button type="button" @click="open = !open" 
                                            class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white text-left flex justify-between items-center focus:ring-2 focus:ring-cyan-400 focus:outline-none"
                                            :class="item.id ? 'border-cyan-300 ring-1 ring-cyan-100' : ''">
                                            <span x-text="item.id ? (item.type === 'product' ? products.find(p => p.id == item.id)?.name : rawMaterials.find(rm => rm.id == item.id)?.name) : '-- Pilih --'"
                                                class="truncate text-gray-700"></span>
                                            <i class="fas fa-chevron-down text-[10px] text-gray-400"></i>
                                        </button>

                                        {{-- Dropdown List --}}
                                        <div x-show="open" @click.away="open = false" 
                                            class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden p-2 space-y-1 transition-all"
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 translate-y-2"
                                            x-transition:enter-end="opacity-100 translate-y-0">
                                            
                                            {{-- Search Input --}}
                                            <div class="px-2 py-1 mb-1">
                                                <input type="text" x-model="search" placeholder="Cari item..." 
                                                    class="w-full px-2 py-1.5 text-xs border border-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-cyan-400">
                                            </div>

                                            <div class="max-h-60 overflow-y-auto space-y-1 custom-scrollbar">
                                                <template x-for="p in (item.type === 'product' ? products : rawMaterials).filter(i => i.name.toLowerCase().includes(search.toLowerCase()))" :key="p.id">
                                                    <div @click="item.id = p.id; item.selected_batches = []; open = false; search = ''" 
                                                        class="flex items-center justify-between p-2 hover:bg-cyan-50 rounded-lg cursor-pointer transition-colors group"
                                                        :class="item.id == p.id ? 'bg-cyan-50 border-cyan-100' : ''">
                                                        <div class="flex flex-col min-w-0">
                                                            <span class="text-xs font-semibold text-gray-800" x-text="p.name"></span>
                                                            <span class="text-[10px] text-gray-400" x-text="'Stok: ' + p.stock + ' ' + (p.unit_name || '')"></span>
                                                        </div>
                                                        <i class="fas fa-check text-cyan-500 text-[10px]" x-show="item.id == p.id"></i>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        {{-- Hidden input for form --}}
                                        <input type="hidden" :name="'items[' + index + '][id]'" :value="item.id" required>
                                    </div>
                                </div>

                                {{-- Batch Selection --}}
                                <div class="col-span-12 md:col-span-4" x-data="{ open: false }">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Pilih Batch (Bisa > 1)</label>
                                    
                                    <div class="relative">
                                        {{-- Toggle Button --}}
                                        <button type="button" @click="open = !open" 
                                            class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm bg-white text-left flex justify-between items-center focus:ring-2 focus:ring-cyan-400 focus:outline-none"
                                            :class="item.selected_batches && item.selected_batches.length > 0 ? 'border-cyan-300 ring-1 ring-cyan-100' : ''">
                                            <span x-text="item.selected_batches && item.selected_batches.length > 0 
                                                ? item.selected_batches.length + ' Batch Terpilih' 
                                                : 'Pilih Otomatis (FIFO)'"
                                                class="truncate text-gray-700"></span>
                                            <i class="fas fa-chevron-down text-[10px] text-gray-400"></i>
                                        </button>

                                        {{-- Batch List Dropdown --}}
                                        <div x-show="open" @click.away="open = false" 
                                            class="absolute z-50 w-[350px] md:w-[450px] mt-2 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden p-2 space-y-1 transition-all"
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 translate-y-2"
                                            x-transition:enter-end="opacity-100 translate-y-0">
                                            
                                            <div class="px-2 py-1.5 border-b border-gray-100 mb-1 flex justify-between items-center">
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Daftar Batch Tersedia</span>
                                                <button type="button" @click="item.selected_batches = []" class="text-[10px] text-cyan-600 hover:text-cyan-700 font-bold">Reset</button>
                                            </div>

                                            <div class="max-h-60 overflow-y-auto space-y-1 custom-scrollbar">
                                                <template x-if="!item.id">
                                                    <div class="p-4 text-center text-xs text-gray-400 italic">Pilih item terlebih dahulu</div>
                                                </template>
                                                
                                                <template x-if="item.id">
                                                    <template x-for="b in (item.type === 'product' ? (products.find(p => p.id == item.id)?.batches || []) : (rawMaterials.find(rm => rm.id == item.id)?.batches || []))" :key="b.batch_number">
                                                        <label class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors border border-transparent hover:border-gray-100"
                                                            :class="item.selected_batches.includes(b.batch_number) ? 'bg-cyan-50/50 border-cyan-100' : ''">
                                                            <input type="checkbox" :value="b.batch_number" x-model="item.selected_batches" class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-cyan-500">
                                                            
                                                            <div class="flex-grow min-w-0">
                                                                <div class="flex items-center justify-between mb-0.5">
                                                                    <span class="text-xs font-bold text-gray-800" x-text="'#' + b.batch_number"></span>
                                                                    <span class="text-[10px] px-1.5 py-0.5 rounded-full font-bold uppercase" 
                                                                        :class="{
                                                                            'bg-red-100 text-red-600': b.status === 'Kadaluarsa',
                                                                            'bg-yellow-100 text-yellow-600': b.status === 'Akan Kadaluarsa',
                                                                            'bg-green-100 text-green-600': b.status === 'Aman'
                                                                        }" x-text="b.status"></span>
                                                                </div>
                                                                <div class="flex items-center gap-2 text-[10px] text-gray-500 font-medium">
                                                                    <span>Sisa: <span class="text-gray-900 font-bold" x-text="b.qty"></span></span>
                                                                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                                                    <span>Exp: <span class="text-gray-900" x-text="b.expired_at"></span></span>
                                                                </div>
                                                            </div>
                                                        </label>
                                                    </template>
                                                </template>
                                            </div>

                                            <template x-if="item.id && (item.type === 'product' ? (products.find(p => p.id == item.id)?.batches || []).length === 0 : (rawMaterials.find(rm => rm.id == item.id)?.batches || []).length === 0)">
                                                <div class="p-4 text-center text-xs text-gray-400 italic">Tidak ada data batch ditemukan</div>
                                            </template>
                                        </div>

                                        {{-- Hidden inputs for form submission --}}
                                        <template x-for="batchNo in item.selected_batches" :key="batchNo">
                                            <input type="hidden" :name="'items[' + index + '][selected_batches][]'" :value="batchNo">
                                        </template>
                                    </div>
                                    <p class="text-[10px] text-gray-400 mt-1">Jika memilih banyak batch, sistem akan membagi jumlah otomatis.</p>
                                </div>

                                {{-- Quantity Input --}}
                                <div class="col-span-12 md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Jumlah</label>
                                    <input type="number" step="any" x-model="item.quantity" :name="'items[' + index + '][quantity]'" required min="0.0001" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 placeholder:text-gray-300" placeholder="0">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-6 border-t border-gray-200">
                    <div class="flex flex-col md:flex-row md:justify-end gap-3">
                        <a href="{{ route('stock-transfers.index') }}" class="w-full md:w-auto inline-flex items-center justify-center px-6 py-2.5 border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-500 text-sm font-semibold text-white rounded-lg hover:from-cyan-600 hover:to-blue-600 shadow-md transition-all">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Draft Transfer
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 text-right mt-2">* Stok belum berkurang saat disimpan sebagai Draft.</p>
                </div>

            </form>
        </section>
    </div>
</main>

@push('scripts')
<script>
    function stockTransferForm() {
        return {
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
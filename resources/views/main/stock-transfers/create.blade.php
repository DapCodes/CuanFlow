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
                                <div class="col-span-12 md:col-span-3">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Tipe</label>
                                    <select x-model="item.type" :name="'items[' + index + '][type]'" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 bg-white">
                                        <option value="product">Produk Jadi</option>
                                        <option value="raw_material">Bahan Baku</option>
                                    </select>
                                </div>

                                {{-- Item Select --}}
                                <div class="col-span-12 md:col-span-6">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Pilih Item</label>
                                    <div class="relative">
                                        <select x-model="item.id" :name="'items[' + index + '][id]'" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 bg-white">
                                            <option value="">-- Pilih --</option>
                                            <template x-if="item.type === 'product'">
                                                <template x-for="p in products" :key="p.id">
                                                    <option :value="p.id" x-text="p.name + ' (Stok: ' + (p.stock || 0) + ')'"></option>
                                                </template>
                                            </template>
                                            <template x-if="item.type === 'raw_material'">
                                                <template x-for="rm in rawMaterials" :key="rm.id">
                                                    <option :value="rm.id" x-text="rm.name + ' (Stok: ' + (rm.stock || 0) + ' ' + (rm.unit_name || '') + ')'"></option>
                                                </template>
                                            </template>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                            <i class="fas fa-chevron-down text-xs"></i>
                                        </div>
                                    </div>
                                </div>

                                {{-- Quantity Input --}}
                                <div class="col-span-12 md:col-span-3">
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
                { type: 'raw_material', id: '', quantity: '' }
            ],
            products: {!! json_encode($products->map(function($p) { 
                return [
                    'id' => $p->id, 
                    'name' => $p->name, 
                    'stock' => $p->getStockQuantity(auth()->user()->outlet_id)
                ]; 
            })) !!},
            rawMaterials: {!! json_encode($rawMaterials->map(function($rm) { 
                return [
                    'id' => $rm->id, 
                    'name' => $rm->name, 
                    'stock' => $rm->getStockQuantity(auth()->user()->outlet_id), 
                    'unit_name' => optional($rm->unit)->name ?? ''
                ]; 
            })) !!},
            
            addItem() {
                this.items.push({ type: 'raw_material', id: '', quantity: '' });
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
@extends('layouts.app')

@section('title', 'Tambah Diskon - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-900 transition-colors">Dashboard</a>
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('discounts.index') }}" class="text-gray-400 hover:text-gray-900 transition-colors">Kelola Diskon</a>
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Tambah Diskon</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-gray-100 pb-6 mb-6">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900 leading-tight">
                    Tambah Diskon Baru
                </h1>
                <p class="mt-1 text-sm text-gray-500 font-medium">
                    Buat dan atur diskon dengan langkah yang sederhana.
                </p>
            </div>
        </section>

        {{-- FORM CARD UTAMA --}}
        <x-card-container class="p-0 overflow-hidden">
            @if ($errors->any())
                <div class="mb-4 p-3 rounded bg-red-50 border border-red-200 text-sm text-red-700">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('discounts.store') }}" method="POST" class="px-4 md:px-6 py-6 space-y-8" 
                  x-data="{ isVoucher: {{ old('is_voucher') ? 'true' : 'false' }} }">
                @csrf

                {{-- Informasi Dasar --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                            <span>Informasi Dasar</span>
                        </h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Kode Diskon --}}
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Kode Diskon <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <input type="text"
                                       name="code"
                                       id="code"
                                       value="{{ old('code') }}"
                                       class="flex-1 px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 @error('code') border-red-500 @enderror"
                                       placeholder="DISC-XXXXX"
                                       required>
                                <button type="button"
                                        id="generateCodeBtn"
                                        class="px-3 md:px-4 py-2.5 bg-gray-700 text-white rounded-lg text-sm hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    <i class="fas fa-sync-alt text-xs md:text-sm"></i>
                                </button>
                            </div>
                            @error('code')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nama Diskon --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Nama Diskon <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="{{ old('name') }}"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 @error('name') border-red-500 @enderror"
                                   placeholder="Contoh: Diskon Akhir Tahun"
                                   required>
                            @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Tipe Diskon --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                            <span>Tipe Diskon</span>
                            <span class="text-red-500">*</span>
                        </h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Percentage --}}
                        <label class="relative cursor-pointer">
                            <input type="radio"
                                   name="type"
                                   value="percentage"
                                   class="peer sr-only"
                                   {{ old('type') == 'percentage' || !old('type') ? 'checked' : '' }}>
                            <div
                                class="border border-gray-300 rounded-lg p-4 md:p-5 peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-red-400 transition-all">
                                <div class="flex flex-col items-center text-center gap-2">
                                    <div>
                                        <h4 class="font-semibold text-gray-900 text-sm">Persentase</h4>
                                        <p class="text-xs text-gray-600 mt-1">
                                            Diskon dalam bentuk persen (%) dari harga.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </label>

                        {{-- Fixed --}}
                        <label class="relative cursor-pointer">
                            <input type="radio"
                                   name="type"
                                   value="fixed"
                                   class="peer sr-only"
                                   {{ old('type') == 'fixed' ? 'checked' : '' }}>
                            <div
                                class="border border-gray-300 rounded-lg p-4 md:p-5 peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-red-400 transition-all">
                                <div class="flex flex-col items-center text-center gap-2">
                                    <div>
                                        <h4 class="font-semibold text-gray-900 text-sm">Fixed</h4>
                                        <p class="text-xs text-gray-600 mt-1">
                                            Potongan harga dengan nominal tetap (Rp).
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </label>

                        {{-- Buy X Get Y --}}
                        <label class="relative cursor-pointer">
                            <input type="radio"
                                   name="type"
                                   value="buy_x_get_y"
                                   class="peer sr-only"
                                   {{ old('type') == 'buy_x_get_y' ? 'checked' : '' }}>
                            <div
                                class="border border-gray-300 rounded-lg p-4 md:p-5 peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-red-400 transition-all">
                                <div class="flex flex-col items-center text-center gap-2">
                                    <div>
                                        <h4 class="font-semibold text-gray-900 text-sm">Buy X Get Y</h4>
                                        <p class="text-xs text-gray-600 mt-1">
                                            Beli sejumlah produk, gratis sejumlah produk.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Form Dinamis Berdasarkan Tipe --}}
                <div id="dynamicFields" class="space-y-8">
                    {{-- Percentage/Fixed Fields --}}
                    <div id="percentageFixedFields" style="display:none;">
                        <div class="flex items-center justify-between mb-4 border-t border-gray-100 pt-8 mt-4">
                            <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                <span>Detail Diskon</span>
                            </h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="value" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    <span id="valueLabel">Nilai Diskon</span> <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number"
                                           name="value"
                                           id="value"
                                           value="{{ old('value') }}"
                                           step="0.01"
                                           min="0"
                                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 @error('value') border-red-500 @enderror"
                                           placeholder="0">
                                    <span id="valueUnit"
                                          class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-500"></span>
                                </div>
                                @error('value')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="max_discount" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Maksimal Diskon <span class="text-gray-400">(Opsional)</span>
                                </label>
                                <div class="relative">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-500">Rp</span>
                                    <input type="number"
                                           name="max_discount"
                                           id="max_discount"
                                           value="{{ old('max_discount') }}"
                                           step="1000"
                                           min="0"
                                           class="w-full pl-9 pr-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400"
                                           placeholder="0">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ada batasan.</p>
                            </div>

                            <div>
                                <label for="min_purchase" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Minimal Pembelian
                                </label>
                                <div class="relative">
                                    <span
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-500">Rp</span>
                                    <input type="number"
                                           name="min_purchase"
                                           id="min_purchase"
                                           value="{{ old('min_purchase', 0) }}"
                                           step="1000"
                                           min="0"
                                           class="w-full pl-9 pr-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400"
                                           placeholder="0">
                                </div>
                            </div>

                            <div>
                                <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Pilih Produk <span class="text-red-500">*</span>
                                </label>
                                <select name="product_id"
                                        id="product_id"
                                        required
                                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 @error('product_id') border-red-500 @enderror">
                                    <option value="">Pilih Produk</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Buy X Get Y Fields --}}
                    <div id="buyXGetYFields" style="display:none;">
                        <div class="flex items-center justify-between mb-4 border-t border-gray-100 pt-8 mt-4">
                            <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                <span>Detail Promo</span>
                            </h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="buy_quantity" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Beli <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       name="buy_quantity"
                                       id="buy_quantity"
                                       value="{{ old('buy_quantity', 1) }}"
                                       min="1"
                                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 @error('buy_quantity') border-red-500 @enderror"
                                       placeholder="1">
                                @error('buy_quantity')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="get_quantity" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Gratis <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       name="get_quantity"
                                       id="get_quantity"
                                       value="{{ old('get_quantity', 1) }}"
                                       min="1"
                                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 @error('get_quantity') border-red-500 @enderror"
                                       placeholder="1">
                                @error('get_quantity')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="buy_product_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Produk <span class="text-red-500">*</span>
                                </label>
                                <select name="product_id"
                                        id="buy_product_id"
                                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 @error('product_id') border-red-500 @enderror">
                                    <option value="">Pilih Produk</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Preview Promo --}}
                        <div class="mt-4 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                            <p class="text-sm font-medium text-purple-900">
                                <i class="fas fa-info-circle mr-2"></i>
                                Preview: <span id="promoPreview">Beli 1 Gratis 1</span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Periode & Batasan --}}
                <div class="border-t border-gray-100 pt-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                            <span>Periode & Batasan</span>
                        </h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Tanggal Mulai <span class="text-gray-400">(Opsional)</span>
                            </label>
                            <input type="datetime-local"
                                   name="start_date"
                                   id="start_date"
                                   value="{{ old('start_date') }}"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400">
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Tanggal Berakhir <span class="text-gray-400">(Opsional)</span>
                            </label>
                            <input type="datetime-local"
                                   name="end_date"
                                   id="end_date"
                                   value="{{ old('end_date') }}"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 @error('end_date') border-red-500 @enderror">
                            @error('end_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="usage_limit" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Batas Penggunaan <span class="text-gray-400">(Opsional)</span>
                            </label>
                            <input type="number"
                                   name="usage_limit"
                                   id="usage_limit"
                                   value="{{ old('usage_limit') }}"
                                   min="1"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400"
                                   placeholder="Tidak terbatas">
                            <p class="mt-1 text-xs text-gray-500">Kosongkan untuk tidak ada batasan.</p>
                        </div>
                    </div>
                </div>

                {{-- Status Aktif --}}
                <div class="bg-gray-50/50 p-6 rounded-2xl border border-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        {{-- Toggle Voucher --}}
                        <div class="flex items-center justify-between p-1">
                            <div class="flex flex-col gap-0.5">
                                <span class="text-sm font-bold text-gray-900 leading-tight">Gunakan Voucher</span>
                                <span class="text-xs text-gray-500">Jadikan diskon sebagai kode kupon</span>
                            </div>
                            <label for="is_voucher" class="relative inline-flex items-center cursor-pointer group">
                                <input type="checkbox"
                                       name="is_voucher"
                                       id="is_voucher"
                                       value="1"
                                       {{ old('is_voucher') ? 'checked' : '' }}
                                       @change="isVoucher = $event.target.checked"
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600 group-hover:ring-4 group-hover:ring-indigo-50/50 transition-all"></div>
                            </label>
                        </div>

                        {{-- Toggle Public Voucher --}}
                        <div class="flex items-center justify-between p-1" x-show="isVoucher" x-cloak x-transition>
                            <div class="flex flex-col gap-0.5">
                                <span class="text-sm font-bold text-gray-700 leading-tight">Voucher Publik</span>
                                <span class="text-xs text-gray-400">Bisa diklaim semua pembeli</span>
                            </div>
                            <label for="is_public" class="relative inline-flex items-center cursor-pointer group">
                                <input type="checkbox"
                                       name="is_public"
                                       id="is_public"
                                       value="1"
                                       {{ old('is_public') ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500 group-hover:ring-4 group-hover:ring-emerald-50/50 transition-all"></div>
                            </label>
                        </div>

                        {{-- Toggle Active --}}
                        <div class="flex items-center justify-between p-1">
                            <div class="flex flex-col gap-0.5">
                                <span class="text-sm font-bold text-gray-900 leading-tight">Aktifkan Diskon</span>
                                <span class="text-xs text-gray-500">Munculkan diskon di outlet</span>
                            </div>
                            <label for="is_active" class="relative inline-flex items-center cursor-pointer group">
                                <input type="checkbox"
                                       name="is_active"
                                       id="is_active"
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-500 group-hover:ring-4 group-hover:ring-red-50/50 transition-all"></div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-6 border-t border-gray-100 bg-gray-50/50 -mx-4 -mb-6 px-4 py-5 md:px-6 md:py-6 rounded-b-3xl">
                    <div class="flex flex-col md:flex-row md:justify-end gap-3">
                        <a href="{{ route('discounts.index') }}"
                           class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3 border-2 border-gray-200 text-xs font-black uppercase tracking-widest text-gray-600 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all">
                            <span>Batal</span>
                        </a>
                        <button type="submit"
                                class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3 bg-red-600 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-red-700 shadow-lg shadow-red-600/20 active:scale-95 transition-all">
                            <span>Simpan Diskon</span>
                        </button>
                    </div>
                </div>
            </form>
        </x-card-container>
    </div>
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const percentageFixedFields = document.getElementById('percentageFixedFields');
    const buyXGetYFields = document.getElementById('buyXGetYFields');
    const valueLabel = document.getElementById('valueLabel');
    const valueUnit = document.getElementById('valueUnit');
    const valueInput = document.getElementById('value');
    const generateCodeBtn = document.getElementById('generateCodeBtn');
    const codeInput = document.getElementById('code');
    const buyQuantityInput = document.getElementById('buy_quantity');
    const getQuantityInput = document.getElementById('get_quantity');
    const promoPreview = document.getElementById('promoPreview');

    // Initialize
    updateFormFields();

    // Type change handler
    typeRadios.forEach(radio => {
        radio.addEventListener('change', updateFormFields);
    });

    function updateFormFields() {
        const selectedType = document.querySelector('input[name="type"]:checked').value;

        // Hide all dynamic fields and disable their inputs
        percentageFixedFields.style.display = 'none';
        toggleFields(percentageFixedFields, false);
        
        buyXGetYFields.style.display = 'none';
        toggleFields(buyXGetYFields, false);

        // Show relevant fields, update labels, and enable their inputs
        if (selectedType === 'percentage') {
            percentageFixedFields.style.display = 'block';
            toggleFields(percentageFixedFields, true);
            valueLabel.textContent = 'Persentase Diskon';
            valueUnit.textContent = '%';
            valueInput.placeholder = '0 - 100';
            valueInput.max = '100';
        } else if (selectedType === 'fixed') {
            percentageFixedFields.style.display = 'block';
            toggleFields(percentageFixedFields, true);
            valueLabel.textContent = 'Nominal Diskon';
            valueUnit.textContent = 'Rp';
            valueInput.placeholder = '0';
            valueInput.removeAttribute('max');
        } else if (selectedType === 'buy_x_get_y') {
            buyXGetYFields.style.display = 'block';
            toggleFields(buyXGetYFields, true);
            updatePromoPreview();
        }
    }

    function toggleFields(container, isEnabled) {
        const fields = container.querySelectorAll('input, select, textarea');
        fields.forEach(field => {
            field.disabled = !isEnabled;
        });
    }

    // Generate code
    generateCodeBtn.addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i>';

        fetch('{{ route("discounts.generate-code") }}')
            .then(response => response.json())
            .then(data => {
                codeInput.value = data.code;
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-sync-alt text-xs md:text-sm"></i>';
            })
            .catch(error => {
                console.error('Error:', error);
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-sync-alt text-xs md:text-sm"></i>';
                alert('Gagal generate kode');
            });
    });

    // Update promo preview
    function updatePromoPreview() {
        if (!buyQuantityInput || !getQuantityInput || !promoPreview) return;
        const buyQty = buyQuantityInput.value || 1;
        const getQty = getQuantityInput.value || 1;
        promoPreview.textContent = `Beli ${buyQty} Gratis ${getQty}`;
    }

    if (buyQuantityInput && getQuantityInput) {
        buyQuantityInput.addEventListener('input', updatePromoPreview);
        getQuantityInput.addEventListener('input', updatePromoPreview);
    }
});
</script>
@endpush
@endsection

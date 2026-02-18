@extends('layouts.app')

@section('title', 'Edit Diskon - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('discounts.index') }}" class="text-gray-500 hover:text-gray-700">Kelola Diskon</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Edit Diskon</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Alert error --}}
        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-exclamation-circle mt-0.5 text-red-500"></i>
                <p class="text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        {{-- HEADER (seragam dengan index/create) --}}
        <section
            class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-500 border border-red-100">
                        <i class="fas fa-edit text-sm"></i>
                    </span>
                    <span>Edit Diskon</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Perbarui detail diskon <span class="font-semibold">{{ $discount->name }}</span> dengan tampilan yang rapi dan mudah dipahami.
                </p>
            </div>
        </section>

        {{-- FORM CARD --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            <form action="{{ route('discounts.update', $discount->id) }}" method="POST"
                  class="px-4 md:px-6 py-6 space-y-8"
                  x-data="{ isVoucher: {{ old('is_voucher', $discount->is_voucher) ? 'true' : 'false' }} }">
                @csrf
                @method('PUT')

                {{-- Informasi Dasar --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900">
                            Informasi Dasar
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
                                       value="{{ old('code', $discount->code) }}"
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
                                   value="{{ old('name', $discount->name) }}"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 @error('name') border-red-500 @enderror"
                                   placeholder="Contoh: Diskon Akhir Tahun"
                                   required>
                            @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Tipe Diskon (card simple, tanpa icon besar) --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900">
                            Tipe Diskon <span class="text-red-500">*</span>
                        </h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Percentage --}}
                        <label class="relative cursor-pointer">
                            <input type="radio"
                                   name="type"
                                   value="percentage"
                                   class="peer sr-only"
                                   {{ old('type', $discount->type) == 'percentage' ? 'checked' : '' }}>
                            <div
                                class="border border-gray-300 rounded-lg p-4 md:p-5 peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-red-400 transition-all">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-semibold text-gray-900">Persentase</span>
                                    <p class="text-xs text-gray-600">
                                        Diskon dalam bentuk persen (%) dari harga.
                                    </p>
                                </div>
                            </div>
                        </label>

                        {{-- Fixed --}}
                        <label class="relative cursor-pointer">
                            <input type="radio"
                                   name="type"
                                   value="fixed"
                                   class="peer sr-only"
                                   {{ old('type', $discount->type) == 'fixed' ? 'checked' : '' }}>
                            <div
                                class="border border-gray-300 rounded-lg p-4 md:p-5 peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-red-400 transition-all">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-semibold text-gray-900">Fixed</span>
                                    <p class="text-xs text-gray-600">
                                        Potongan harga dengan nominal tetap (Rp).
                                    </p>
                                </div>
                            </div>
                        </label>

                        {{-- Buy X Get Y --}}
                        <label class="relative cursor-pointer">
                            <input type="radio"
                                   name="type"
                                   value="buy_x_get_y"
                                   class="peer sr-only"
                                   {{ old('type', $discount->type) == 'buy_x_get_y' ? 'checked' : '' }}>
                            <div
                                class="border border-gray-300 rounded-lg p-4 md:p-5 peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-red-400 transition-all">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-semibold text-gray-900">Buy X Get Y</span>
                                    <p class="text-xs text-gray-600">
                                        Beli sejumlah produk, gratis sejumlah produk.
                                    </p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Form Dinamis Berdasarkan Tipe --}}
                <div id="dynamicFields" class="space-y-8">
                    {{-- Percentage/Fixed Fields --}}
                    <div id="percentageFixedFields" style="display:none;">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base md:text-lg font-semibold text-gray-900">
                                Detail Diskon
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
                                           value="{{ old('value', $discount->value) }}"
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
                                           value="{{ old('max_discount', $discount->max_discount) }}"
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
                                           value="{{ old('min_purchase', $discount->min_purchase) }}"
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
                                        <option value="{{ $product->id }}" {{ old('product_id', $discount->product_id) == $product->id ? 'selected' : '' }}>
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
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base md:text-lg font-semibold text-gray-900">
                                Detail Promo
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
                                       value="{{ old('buy_quantity', $discount->buy_quantity) }}"
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
                                       value="{{ old('get_quantity', $discount->get_quantity) }}"
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
                                        <option value="{{ $product->id }}" {{ old('product_id', $discount->product_id) == $product->id ? 'selected' : '' }}>
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
                                Preview: <span id="promoPreview">Beli {{ $discount->buy_quantity ?? 1 }} Gratis {{ $discount->get_quantity ?? 1 }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Periode & Batasan --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900">
                            Periode & Batasan
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
                                   value="{{ old('start_date', $discount->start_date ? $discount->start_date->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400">
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Tanggal Berakhir <span class="text-gray-400">(Opsional)</span>
                            </label>
                            <input type="datetime-local"
                                   name="end_date"
                                   id="end_date"
                                   value="{{ old('end_date', $discount->end_date ? $discount->end_date->format('Y-m-d\TH:i') : '') }}"
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
                                   value="{{ old('usage_limit', $discount->usage_limit) }}"
                                   min="1"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400"
                                   placeholder="Tidak terbatas">
                            <p class="mt-1 text-xs text-gray-500">Kosongkan untuk tidak ada batasan.</p>
                        </div>
                    </div>
                </div>

                {{-- Status Aktif --}}
                <div>
                    <div class="flex items-center gap-6">
                        <div class="flex items-center">
                            <input type="checkbox"
                                   name="is_voucher"
                                   id="is_voucher"
                                   value="1"
                                   {{ old('is_voucher', $discount->is_voucher) ? 'checked' : '' }}
                                   @change="isVoucher = $event.target.checked"
                                   class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <label for="is_voucher" class="ml-3 text-sm font-medium text-gray-700">
                                Gunakan sebagai voucher / kupon
                            </label>
                        </div>

                        <div class="flex items-center" x-show="isVoucher" x-cloak>
                            <input type="checkbox"
                                   name="is_public"
                                   id="is_public"
                                   value="1"
                                   {{ old('is_public', $discount->is_public) ? 'checked' : '' }}
                                   class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                            <label for="is_public" class="ml-3 text-sm font-medium text-gray-700">
                                Voucher Publik (Bisa diklaim semua user)
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox"
                                   name="is_active"
                                   id="is_active"
                                   value="1"
                                   {{ old('is_active', $discount->is_active) ? 'checked' : '' }}
                                   class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <label for="is_active" class="ml-3 text-sm font-medium text-gray-700">
                                Aktifkan diskon
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Info penggunaan --}}
                @if($discount->used_count > 0)
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        Diskon ini telah digunakan <strong>{{ $discount->used_count }} kali</strong>.
                        @if($discount->usage_limit)
                            Sisa {{ max($discount->usage_limit - $discount->used_count, 0) }} kali lagi.
                        @endif
                    </div>
                @endif

                {{-- Action Buttons: mobile full width --}}
                <div class="pt-5 border-t border-gray-200">
                    <div class="flex flex-col md:flex-row md:justify-end gap-3">
                        <a href="{{ route('discounts.show', $discount->id) }}"
                           class="w-full md:w-auto inline-flex items-center justify-center px-4 md:px-6 py-2.5 border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-times mr-2 text-xs"></i>
                            <span>Batal</span>
                        </a>
                        <button type="submit"
                                class="w-full md:w-auto inline-flex items-center justify-center px-4 md:px-6 py-2.5 bg-red-500 text-sm font-semibold text-white rounded-lg hover:bg-red-600 shadow-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-1">
                            <i class="fas fa-save mr-2 text-xs"></i>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </div>
            </form>
        </section>
    </div>
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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

    // Init
    updateFormFields();

    // Handle change tipe
    typeRadios.forEach(radio => {
        radio.addEventListener('change', updateFormFields);
    });

    function updateFormFields() {
        const checked = document.querySelector('input[name="type"]:checked');
        if (!checked) return;

        const selectedType = checked.value;

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
        if (!confirm('Generate kode baru? Kode lama akan diganti.')) return;

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

    // Preview promo Buy X Get Y
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

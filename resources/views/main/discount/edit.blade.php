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
<main class="flex-grow py-8 px-4">
    <div class="max-w-7xl mx-auto">
        
        @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg" role="alert">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-red-500 mt-1 mr-3"></i>
                <div class="flex-1">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif

        <x-card-container>
            <div class="bg-gradient-to-br from-red-400 to-pink-500 p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-edit mr-3"></i>
                    Edit Diskon
                </h2>
                <p class="text-sm text-red-50 mt-1">Perbarui informasi diskon {{ $discount->name }}</p>
            </div>

            <form action="{{ route('discounts.update', $discount->id) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <!-- Informasi Dasar -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-red-500 mr-2"></i>
                        Informasi Dasar
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Kode Diskon -->
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                                Kode Diskon <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <input type="text" 
                                       name="code" 
                                       id="code" 
                                       value="{{ old('code', $discount->code) }}"
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('code') border-red-500 @enderror" 
                                       placeholder="DISC-XXXXX"
                                       required>
                                <button type="button" 
                                        id="generateCodeBtn"
                                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                            @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nama Diskon -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Diskon <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $discount->name) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('name') border-red-500 @enderror" 
                                   placeholder="Contoh: Diskon Akhir Tahun"
                                   required>
                            @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Tipe Diskon -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-layer-group text-red-500 mr-2"></i>
                        Tipe Diskon <span class="text-red-500">*</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Percentage -->
                        <label class="relative cursor-pointer">
                            <input type="radio" 
                                   name="type" 
                                   value="percentage" 
                                   class="peer sr-only" 
                                   {{ old('type', $discount->type) == 'percentage' ? 'checked' : '' }}>
                            <div class="border-2 border-gray-300 rounded-lg p-6 peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-red-300 transition-all">
                                <div class="flex flex-col items-center text-center">
                                    <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-percent text-white text-2xl"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-900 mb-1">Persentase</h4>
                                    <p class="text-xs text-gray-600">Diskon dalam bentuk persen (%) dari harga</p>
                                </div>
                            </div>
                        </label>

                        <!-- Fixed -->
                        <label class="relative cursor-pointer">
                            <input type="radio" 
                                   name="type" 
                                   value="fixed" 
                                   class="peer sr-only"
                                   {{ old('type', $discount->type) == 'fixed' ? 'checked' : '' }}>
                            <div class="border-2 border-gray-300 rounded-lg p-6 peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-red-300 transition-all">
                                <div class="flex flex-col items-center text-center">
                                    <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-money-bill text-white text-2xl"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-900 mb-1">Fixed</h4>
                                    <p class="text-xs text-gray-600">Potongan harga dengan nominal tetap (Rp)</p>
                                </div>
                            </div>
                        </label>

                        <!-- Buy X Get Y -->
                        <label class="relative cursor-pointer">
                            <input type="radio" 
                                   name="type" 
                                   value="buy_x_get_y" 
                                   class="peer sr-only"
                                   {{ old('type', $discount->type) == 'buy_x_get_y' ? 'checked' : '' }}>
                            <div class="border-2 border-gray-300 rounded-lg p-6 peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-red-300 transition-all">
                                <div class="flex flex-col items-center text-center">
                                    <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-gift text-white text-2xl"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-900 mb-1">Buy X Get Y</h4>
                                    <p class="text-xs text-gray-600">Beli sejumlah produk, gratis sejumlah produk</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Form Dinamis Berdasarkan Tipe -->
                <div id="dynamicFields">
                    <!-- Percentage/Fixed Fields -->
                    <div id="percentageFixedFields" class="mb-8" style="display: none;">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-dollar-sign text-red-500 mr-2"></i>
                            Detail Diskon
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="value" class="block text-sm font-medium text-gray-700 mb-2">
                                    <span id="valueLabel">Nilai Diskon</span> <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" 
                                           name="value" 
                                           id="value" 
                                           value="{{ old('value', $discount->value) }}"
                                           step="0.01"
                                           min="0"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('value') border-red-500 @enderror" 
                                           placeholder="0">
                                    <span id="valueUnit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500"></span>
                                </div>
                                @error('value')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="max_discount" class="block text-sm font-medium text-gray-700 mb-2">
                                    Maksimal Diskon <span class="text-gray-400">(Opsional)</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                                    <input type="number" 
                                           name="max_discount" 
                                           id="max_discount" 
                                           value="{{ old('max_discount', $discount->max_discount) }}"
                                           step="1000"
                                           min="0"
                                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                                           placeholder="0">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ada batasan</p>
                            </div>

                            <div>
                                <label for="min_purchase" class="block text-sm font-medium text-gray-700 mb-2">
                                    Minimal Pembelian
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                                    <input type="number" 
                                           name="min_purchase" 
                                           id="min_purchase" 
                                           value="{{ old('min_purchase', $discount->min_purchase) }}"
                                           step="1000"
                                           min="0"
                                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                                           placeholder="0">
                                </div>
                            </div>

                            <div>
                                <label for="product_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Produk Spesifik <span class="text-gray-400">(Opsional)</span>
                                </label>
                                <select name="product_id" 
                                        id="product_id" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                    <option value="">Semua Produk</option>
                                    @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id', $discount->product_id) == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kategori <span class="text-gray-400">(Opsional)</span>
                                </label>
                                <select name="category_id" 
                                        id="category_id" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                    <option value="">Semua Kategori</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $discount->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Buy X Get Y Fields -->
                    <div id="buyXGetYFields" class="mb-8" style="display: none;">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-gift text-red-500 mr-2"></i>
                            Detail Promo
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="buy_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                    Beli <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       name="buy_quantity" 
                                       id="buy_quantity" 
                                       value="{{ old('buy_quantity', $discount->buy_quantity) }}"
                                       min="1"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('buy_quantity') border-red-500 @enderror" 
                                       placeholder="1">
                                @error('buy_quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="get_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                    Gratis <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       name="get_quantity" 
                                       id="get_quantity" 
                                       value="{{ old('get_quantity', $discount->get_quantity) }}"
                                       min="1"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('get_quantity') border-red-500 @enderror" 
                                       placeholder="1">
                                @error('get_quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="buy_product_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Produk <span class="text-red-500">*</span>
                                </label>
                                <select name="product_id" 
                                        id="buy_product_id" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('product_id') border-red-500 @enderror">
                                    <option value="">Pilih Produk</option>
                                    @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id', $discount->product_id) == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Preview Promo -->
                        <div class="mt-4 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                            <p class="text-sm font-medium text-purple-900">
                                <i class="fas fa-info-circle mr-2"></i>
                                Preview: <span id="promoPreview">Beli {{ $discount->buy_quantity ?? 1 }} Gratis {{ $discount->get_quantity ?? 1 }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Periode & Batasan -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-calendar-alt text-red-500 mr-2"></i>
                        Periode & Batasan
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Mulai <span class="text-gray-400">(Opsional)</span>
                            </label>
                            <input type="datetime-local" 
                                   name="start_date" 
                                   id="start_date" 
                                   value="{{ old('start_date', $discount->start_date ? $discount->start_date->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Berakhir <span class="text-gray-400">(Opsional)</span>
                            </label>
                            <input type="datetime-local" 
                                   name="end_date" 
                                   id="end_date" 
                                   value="{{ old('end_date', $discount->end_date ? $discount->end_date->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('end_date') border-red-500 @enderror">
                            @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="usage_limit" class="block text-sm font-medium text-gray-700 mb-2">
                                Batas Penggunaan <span class="text-gray-400">(Opsional)</span>
                            </label>
                            <input type="number" 
                                   name="usage_limit" 
                                   id="usage_limit" 
                                   value="{{ old('usage_limit', $discount->usage_limit) }}"
                                   min="1"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" 
                                   placeholder="Tidak terbatas">
                            <p class="mt-1 text-xs text-gray-500">Kosongkan untuk tidak ada batasan</p>
                        </div>
                    </div>
                </div>

                <!-- Status Aktif -->
                <div class="mb-8">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               name="is_active" 
                               id="is_active" 
                               value="1"
                               {{ old('is_active', $discount->is_active) ? 'checked' : '' }}
                               class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <label for="is_active" class="ml-3 text-sm font-medium text-gray-700">
                            Aktifkan diskon
                        </label>
                    </div>
                </div>

                <!-- Usage Info -->
                @if($discount->used_count > 0)
                <div class="mb-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        Diskon ini telah digunakan <strong>{{ $discount->used_count }} kali</strong>.
                        @if($discount->usage_limit)
                            Sisa {{ $discount->usage_limit - $discount->used_count }} kali lagi.
                        @endif
                    </p>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('discounts.show', $discount->id) }}" 
                       class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-gradient-to-br from-red-400 to-pink-500 text-white rounded-lg hover:from-red-500 hover:to-pink-600 transition-colors font-semibold shadow-md">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Perubahan
                    </button>
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

        // Hide all dynamic fields
        percentageFixedFields.style.display = 'none';
        buyXGetYFields.style.display = 'none';

        // Show relevant fields and update labels
        if (selectedType === 'percentage') {
            percentageFixedFields.style.display = 'block';
            valueLabel.textContent = 'Persentase Diskon';
            valueUnit.textContent = '%';
            valueInput.placeholder = '0 - 100';
            valueInput.max = '100';
        } else if (selectedType === 'fixed') {
            percentageFixedFields.style.display = 'block';
            valueLabel.textContent = 'Nominal Diskon';
            valueUnit.textContent = 'Rp';
            valueInput.placeholder = '0';
            valueInput.removeAttribute('max');
        } else if (selectedType === 'buy_x_get_y') {
            buyXGetYFields.style.display = 'block';
            updatePromoPreview();
        }
    }

    // Generate code
    generateCodeBtn.addEventListener('click', function() {
        if (!confirm('Generate kode baru? Kode lama akan diganti.')) {
            return;
        }

        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        fetch('{{ route("discounts.generate-code") }}')
            .then(response => response.json())
            .then(data => {
                codeInput.value = data.code;
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-sync-alt"></i>';
            })
            .catch(error => {
                console.error('Error:', error);
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-sync-alt"></i>';
                alert('Gagal generate kode');
            });
    });

    // Update promo preview
    function updatePromoPreview() {
        const buyQty = buyQuantityInput.value || 1;
        const getQty = getQuantityInput.value || 1;
        promoPreview.textContent = `Beli ${buyQty} Gratis ${getQty}`;
    }

    if (buyQuantityInput && getQuantityInput) {
        buyQuantityInput.addEventListener('input', updatePromoPreview);
        getQuantityInput.addEventListener('input', updatePromoPreview);
    }

    // Sync product/category selection
    const productId = document.getElementById('product_id');
    const categoryId = document.getElementById('category_id');
    
    if (productId && categoryId) {
        productId.addEventListener('change', function() {
            if (this.value) {
                categoryId.value = '';
            }
        });

        categoryId.addEventListener('change', function() {
            if (this.value) {
                productId.value = '';
            }
        });
    }
});
</script>
@endpush
@endsection
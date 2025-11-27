@extends('layouts.app')

@section('title', 'Edit Produk & Resep - ' . $product->name)

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('products-hpp.index') }}" class="text-gray-500 hover:text-gray-700">Produk & Resep</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Edit Produk</span>
</li>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 46px;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0.625rem 1rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 30px;
        color: #374151;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px;
        right: 10px;
    }
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .select2-dropdown {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #3b82f6;
    }
</style>
@endpush

@section('content')
<main class="flex-grow py-8 px-4">
    <div class="max-w-7xl mx-auto">

        @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg" role="alert">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-red-500 mt-1 mr-3"></i>
                <div class="flex-1">
                    <h3 class="font-semibold text-red-800 mb-2">Terjadi kesalahan!</h3>
                    <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <x-card-container>
            {{-- HEADER + NAV SECTION (card utama tetap, hanya isi diatur ulang) --}}
            <div class="bg-gradient-to-r from-green-50 to-blue-50 border-b border-gray-200 px-6 py-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <h3 class="text-2xl font-semibold text-gray-900 flex items-center">
                            Edit Produk: <span class="ml-1">{{ $product->name }}</span>
                        </h3>
                        <p class="text-xs md:text-sm text-gray-500 mt-1">
                            Semua informasi produk, resep, bahan baku, biaya, dan harga dalam satu halaman.
                        </p>
                    </div>
                    <nav class="flex flex-wrap gap-2 text-xs md:text-sm">
                        <a href="#section-basic" class="px-3 py-1.5 rounded-full bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 flex items-center">
                            <i class="fas fa-info-circle mr-2 text-cuan-green"></i> Dasar
                        </a>
                        <a href="#section-recipe" class="px-3 py-1.5 rounded-full bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 flex items-center">
                            <i class="fas fa-book-open mr-2 text-cuan-green"></i> Resep
                        </a>
                        <a href="#section-bahan" class="px-3 py-1.5 rounded-full bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 flex items-center">
                            <i class="fas fa-shopping-basket mr-2 text-cuan-green"></i> Bahan
                        </a>
                        <a href="#section-biaya" class="px-3 py-1.5 rounded-full bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 flex items-center">
                            <i class="fas fa-coins mr-2 text-cuan-green"></i> Biaya & HPP
                        </a>
                        <a href="#section-pricing" class="px-3 py-1.5 rounded-full bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 flex items-center">
                            <i class="fas fa-tags mr-2 text-cuan-green"></i> Harga & Margin
                        </a>
                    </nav>
                </div>
            </div>

            <form action="{{ route('products-hpp.update', $product->id) }}" method="POST" enctype="multipart/form-data" id="productForm">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-10">

                    {{-- SECTION 1: INFORMASI DASAR --}}
                    <section id="section-basic" class="bg-white border border-gray-100 rounded-xl p-5 md:p-6 shadow-sm">
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <i class="fas fa-info-circle text-cuan-green mr-2"></i>
                                Informasi Dasar Produk
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">Perbarui informasi dasar tentang produk.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-barcode text-gray-400 mr-1"></i>
                                    Kode Produk <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="code" id="productCode" value="{{ old('code', $product->code) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="Contoh: PRD001" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-tag text-gray-400 mr-1"></i>
                                    Nama Produk <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name', $product->name) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="Masukkan nama produk" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-qrcode text-gray-400 mr-1"></i>
                                    Barcode
                                </label>
                                <input type="text" name="barcode" id="productBarcode" value="{{ old('barcode', $product->barcode) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="Opsional">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-folder text-gray-400 mr-1"></i>
                                    Kategori
                                </label>
                                <select name="category_id" class="select2-category w-full">
                                    <option value="">- Pilih Kategori -</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-ruler text-gray-400 mr-1"></i>
                                    Satuan <span class="text-red-500">*</span>
                                </label>
                                <select name="unit_id" class="select2-unit w-full" required>
                                    <option value="">- Pilih Satuan -</option>
                                    @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_id', $product->unit_id) == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-image text-gray-400 mr-1"></i>
                                    Foto Produk
                                </label>
                                <input type="file" name="image" id="imageInput" accept="image/*"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="text-xs text-gray-500 mt-1">Max 2MB (JPG, JPEG, PNG)</p>

                                <!-- Image Preview -->
                                <div id="imagePreview" class="mt-3 {{ $product->image ? '' : 'hidden' }}">
                                    <div class="relative inline-block">
                                        <img id="previewImg" src="{{ $product->image ? asset('storage/' . $product->image) : '' }}" alt="Preview" class="w-32 h-32 object-cover rounded-lg border-2 border-gray-300 shadow-sm">
                                        <button type="button" id="removeImage" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition-colors">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-align-left text-gray-400 mr-1"></i>
                                    Deskripsi
                                </label>
                                <textarea name="description" rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="Deskripsi produk (opsional)">{{ old('description', $product->description) }}</textarea>
                            </div>
                        </div>
                    </section>

                    {{-- SECTION 2: INFORMASI RESEP --}}
                    <section id="section-recipe" class="bg-white border border-gray-100 rounded-xl p-5 md:p-6 shadow-sm">
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <i class="fas fa-book-open text-cuan-green mr-2"></i>
                                Informasi Resep
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">Perbarui resep untuk produksi produk ini.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-signature text-gray-400 mr-1"></i>
                                    Nama Resep <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="recipe_name" value="{{ old('recipe_name', $product->defaultRecipe->name ?? '') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="Contoh: Resep Kue Original" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-layer-group text-gray-400 mr-1"></i>
                                    Jumlah Output <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.01" name="output_quantity" value="{{ old('output_quantity', $product->defaultRecipe->output_quantity ?? 1) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="1" required>
                                <p class="text-xs text-gray-500 mt-1">Berapa banyak produk yang dihasilkan dari resep ini.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-clock text-gray-400 mr-1"></i>
                                    Estimasi Waktu (menit)
                                </label>
                                <input type="number" name="estimated_time_minutes" value="{{ old('estimated_time_minutes', $product->defaultRecipe->estimated_time_minutes ?? '') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="30">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-list-ol text-gray-400 mr-1"></i>
                                    Instruksi Pembuatan
                                </label>
                                <textarea name="instructions" rows="6"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="1. Langkah pertama...&#10;2. Langkah kedua...&#10;3. Dan seterusnya...">{{ old('instructions', $product->defaultRecipe->instructions ?? '') }}</textarea>
                            </div>
                        </div>
                    </section>

                    {{-- SECTION 3: BAHAN BAKU --}}
                    <section id="section-bahan" class="bg-white border border-gray-100 rounded-xl p-5 md:p-6 shadow-sm">
                        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                    <i class="fas fa-shopping-basket text-cuan-green mr-2"></i>
                                    Bahan Baku yang Dibutuhkan
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">Perbarui bahan baku dan jumlah yang diperlukan.</p>
                            </div>
                            <button type="button" id="addRecipeItem" class="px-5 py-2.5 bg-cuan-green text-white rounded-lg hover:bg-cuan-olive transition-colors flex items-center text-sm font-medium">
                                <i class="fas fa-plus mr-2"></i>
                                Tambah Bahan
                            </button>
                        </div>

                        @php
                            $existingItems = old('recipe_items', $product->defaultRecipe->items ?? collect());
                            $itemCount = is_array($existingItems) ? count($existingItems) : $existingItems->count();
                        @endphp

                        <div id="recipeItemsContainer" class="space-y-4">
                            @if($itemCount > 0)
                                @foreach($existingItems as $index => $item)
                                    @php
                                        $rawMaterialId = is_array($item) ? $item['raw_material_id'] : $item->raw_material_id;
                                        $quantity = is_array($item) ? $item['quantity'] : $item->quantity;
                                        $notes = is_array($item) ? ($item['notes'] ?? '') : $item->notes;
                                        $rawMaterial = \App\Models\RawMaterial::find($rawMaterialId);
                                    @endphp
                                    <div class="recipe-item border border-gray-200 rounded-lg p-5 bg-gray-50">
                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                            <div class="md:col-span-5">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Bahan Baku <span class="text-red-500">*</span>
                                                </label>
                                                <select name="recipe_items[{{ $index }}][raw_material_id]" class="raw-material-select w-full" required>
                                                    <option value="">- Pilih Bahan -</option>
                                                    @foreach($rawMaterials as $rm)
                                                    <option value="{{ $rm->id }}"
                                                        data-price="{{ $rm->purchase_price }}"
                                                        data-unit="{{ $rm->unit->name ?? '' }}"
                                                        {{ $rawMaterialId == $rm->id ? 'selected' : '' }}>
                                                        {{ $rm->name }} ({{ $rm->unit->name ?? '' }}) - Rp {{ number_format($rm->purchase_price, 0, ',', '.') }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Jumlah <span class="text-red-500">*</span>
                                                </label>
                                                <input type="number" step="0.01" name="recipe_items[{{ $index }}][quantity]"
                                                    value="{{ $quantity }}"
                                                    class="quantity-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                    placeholder="0" required>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Satuan</label>
                                                <input type="text" class="unit-display w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 text-gray-600"
                                                    readonly placeholder="-" value="{{ $rawMaterial->unit->name ?? '' }}">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Biaya</label>
                                                <input type="text" class="cost-display w-full px-4 py-3 border border-gray-300 rounded-lg bg-blue-50 font-semibold text-blue-700"
                                                    readonly value="Rp {{ number_format(($rawMaterial->purchase_price ?? 0) * $quantity, 0, ',', '.') }}">
                                            </div>
                                            <div class="md:col-span-1 flex items-end">
                                                <button type="button" class="remove-item w-full px-4 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors" style="display: {{ $index == 0 && $itemCount == 1 ? 'none' : 'block' }};">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                <i class="fas fa-sticky-note text-gray-400 mr-1"></i>
                                                Catatan
                                            </label>
                                            <input type="text" name="recipe_items[{{ $index }}][notes]"
                                                value="{{ $notes }}"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                placeholder="Catatan tambahan (opsional)">
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                {{-- Default item jika tidak ada data --}}
                                <div class="recipe-item border border-gray-200 rounded-lg p-5 bg-gray-50">
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                        <div class="md:col-span-5">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Bahan Baku <span class="text-red-500">*</span>
                                            </label>
                                            <select name="recipe_items[0][raw_material_id]" class="raw-material-select w-full" required>
                                                <option value="">- Pilih Bahan -</option>
                                                @foreach($rawMaterials as $rm)
                                                <option value="{{ $rm->id }}" data-price="{{ $rm->purchase_price }}" data-unit="{{ $rm->unit->name ?? '' }}">
                                                    {{ $rm->name }} ({{ $rm->unit->name ?? '' }}) - Rp {{ number_format($rm->purchase_price, 0, ',', '.') }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Jumlah <span class="text-red-500">*</span>
                                            </label>
                                            <input type="number" step="0.01" name="recipe_items[0][quantity]"
                                                class="quantity-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                placeholder="0" required>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Satuan</label>
                                            <input type="text" class="unit-display w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 text-gray-600" readonly placeholder="-">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Biaya</label>
                                            <input type="text" class="cost-display w-full px-4 py-3 border border-gray-300 rounded-lg bg-blue-50 font-semibold text-blue-700" readonly value="Rp 0">
                                        </div>
                                        <div class="md:col-span-1 flex items-end">
                                            <button type="button" class="remove-item w-full px-4 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors" style="display: none;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-sticky-note text-gray-400 mr-1"></i>
                                            Catatan
                                        </label>
                                        <input type="text" name="recipe_items[0][notes]"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            placeholder="Catatan tambahan (opsional)">
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-5">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-semibold text-gray-700 flex items-center">
                                    <i class="fas fa-calculator text-cuan-green mr-2"></i>
                                    Total Biaya Bahan Baku:
                                </span>
                                <span id="totalMaterialCost" class="text-xl font-bold text-cuan-green">Rp 0</span>
                            </div>
                        </div>
                    </section>

                    {{-- SECTION 4: BIAYA TAMBAHAN & RINGKASAN HPP --}}
                    <section id="section-biaya" class="bg-white border border-gray-100 rounded-xl p-5 md:p-6 shadow-sm">
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <i class="fas fa-coins text-cuan-green mr-2"></i>
                                Biaya Tambahan & Ringkasan HPP
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">Biaya overhead seperti listrik, gas, packaging, dll.</p>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-cuan-green mt-0.5 mr-3"></i>
                                <p class="text-sm text-blue-800">
                                    Biaya tambahan mencakup semua pengeluaran diluar bahan baku yang diperlukan dalam proses produksi seperti listrik, gas, packaging, tenaga kerja, dan lain-lain.
                                </p>
                            </div>
                        </div>

                        @php
                            $latestHpp = $product->latestHppCalculation;
                            $additionalCostValue = old('additional_cost', $latestHpp->additional_cost ?? 0);
                        @endphp

                        <div class="grid md:grid-cols-3 gap-6 items-start">
                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-money-bill-wave text-gray-400 mr-1"></i>
                                    Biaya Tambahan (Rp)
                                </label>
                                <input type="number" step="0.01" name="additional_cost" id="additionalCostInput" value="{{ $additionalCostValue }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="0">
                                <p class="text-xs text-gray-500 mt-1">Masukkan 0 jika tidak ada biaya tambahan.</p>
                            </div>

                            <div class="md:col-span-2">
                                <div class="bg-gradient-to-br from-green-50 to-blue-50 border border-green-200 rounded-lg p-6">
                                    <h4 class="text-base font-bold text-gray-900 mb-4 flex items-center">
                                        <i class="fas fa-chart-line text-green-600 mr-2"></i>
                                        Ringkasan Perhitungan HPP
                                    </h4>
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center py-2">
                                            <span class="text-sm text-gray-700">Biaya Bahan Baku:</span>
                                            <span id="summaryMaterialCost" class="font-semibold text-gray-900">Rp 0</span>
                                        </div>
                                        <div class="flex justify-between items-center py-2">
                                            <span class="text-sm text-gray-700">Biaya Tambahan:</span>
                                            <span id="summaryAdditionalCost" class="font-semibold text-gray-900">Rp 0</span>
                                        </div>
                                        <div class="border-t border-gray-300 pt-3 flex justify-between items-center">
                                            <span class="font-bold text-gray-900">Total HPP:</span>
                                            <span id="summaryTotalHpp" class="text-xl font-bold text-green-600">Rp 0</span>
                                        </div>
                                        <div class="flex justify-between items-center py-2">
                                            <span class="text-sm text-gray-700">Output Quantity:</span>
                                            <span id="summaryOutputQty" class="font-semibold text-gray-900">1</span>
                                        </div>
                                        <div class="bg-white rounded-lg p-4 flex justify-between items-center shadow-sm">
                                            <span class="font-bold text-gray-900">HPP Per Unit:</span>
                                            <span id="summaryHppPerUnit" class="text-2xl font-bold text-cuan-green">Rp 0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- SECTION 5: HARGA JUAL, STOK & ANALISIS MARGIN --}}
                    <section id="section-pricing" class="bg-white border border-gray-100 rounded-xl p-5 md:p-6 shadow-sm">
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <i class="fas fa-tags text-cuan-green mr-2"></i>
                                Harga Jual & Stok
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">Perbarui harga jual dan pengaturan stok produk.</p>
                        </div>

                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-semibold text-gray-700 flex items-center">
                                    <i class="fas fa-dollar-sign text-green-600 mr-2"></i>
                                    HPP Per Unit:
                                </span>
                                <span id="finalHppPerUnit" class="text-xl font-bold text-green-600">Rp 0</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-hand-holding-usd text-gray-400 mr-1"></i>
                                    Harga Jual <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.01" name="selling_price" id="sellingPriceInput" value="{{ old('selling_price', $product->selling_price) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-tag text-gray-400 mr-1"></i>
                                    Harga Reseller
                                </label>
                                <input type="number" step="0.01" name="reseller_price" value="{{ old('reseller_price', $product->reseller_price) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="text-xs text-gray-500 mt-1">Harga untuk reseller (opsional).</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-percent text-gray-400 mr-1"></i>
                                    Harga Promo
                                </label>
                                <input type="number" step="0.01" name="promo_price" value="{{ old('promo_price', $product->promo_price) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="text-xs text-gray-500 mt-1">Harga saat promo (opsional).</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-boxes text-gray-400 mr-1"></i>
                                    Minimum Stok
                                </label>
                                <input type="number" step="0.01" name="min_stock" value="{{ old('min_stock', $product->min_stock) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="text-xs text-gray-500 mt-1">Alert jika stok di bawah angka ini.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-calendar-day text-gray-400 mr-1"></i>
                                    Masa Simpan (hari)
                                </label>
                                <input type="number" name="shelf_life_days" value="{{ old('shelf_life_days', $product->shelf_life_days) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="text-xs text-gray-500 mt-1">Berapa hari produk bisa disimpan.</p>
                            </div>
                        </div>

                        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-chart-line text-cuan-green mr-2"></i>
                                Analisis Margin
                            </h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">HPP Per Unit:</span>
                                    <span id="marginHpp" class="font-semibold text-gray-900">Rp 0</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">Harga Jual:</span>
                                    <span id="marginSellingPrice" class="font-semibold text-gray-900">Rp 0</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">Keuntungan:</span>
                                    <span id="marginProfit" class="font-semibold text-gray-900">Rp 0</span>
                                </div>
                                <div class="bg-white rounded-lg p-4 flex justify-between items-center border-2 border-blue-200">
                                    <span class="text-lg font-bold text-gray-800">
                                        <i class="fas fa-percentage text-cuan-green mr-2"></i>
                                        Margin:
                                    </span>
                                    <span id="marginPercent" class="text-2xl font-bold text-green-600">0%</span>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>

                {{-- FOOTER: BUTTON AKSI --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <a href="{{ route('products-hpp.index') }}"
                       class="inline-flex items-center justify-center px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                    <button type="submit"
                        class="inline-flex items-center justify-center px-6 py-2.5 bg-cuan-green text-white rounded-lg hover:bg-cuan-olive transition-all font-semibold shadow-md hover:shadow-lg text-sm">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </x-card-container>

    </div>
</main>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
let recipeItemIndex = {{ $itemCount }};
let totalMaterialCostValue = 0;

const imageInput = document.getElementById('imageInput');
const imagePreview = document.getElementById('imagePreview');
const previewImg = document.getElementById('previewImg');
const removeImageBtn = document.getElementById('removeImage');

if (imageInput) {
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];

        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file terlalu besar! Maksimal 2MB');
                imageInput.value = '';
                return;
            }

            if (!file.type.match('image.*')) {
                alert('File harus berupa gambar (JPG, JPEG, PNG)');
                imageInput.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });
}

if (removeImageBtn) {
    removeImageBtn.addEventListener('click', function() {
        imageInput.value = '';
        imagePreview.classList.add('hidden');
        previewImg.src = '';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Select2
    $('.select2-category').select2({
        theme: 'default',
        width: '100%',
        placeholder: '- Pilih Kategori -'
    });

    $('.select2-unit').select2({
        theme: 'default',
        width: '100%',
        placeholder: '- Pilih Satuan -'
    });

    $('.raw-material-select').select2({
        theme: 'default',
        width: '100%',
        placeholder: '- Pilih Bahan -'
    });

    // Hitung awal
    document.querySelectorAll('.recipe-item').forEach(item => {
        calculateItemCost(item);
    });
    calculateTotalMaterialCost();
    updateHppSummary();
    updateFinalPricing();

    // Tambah bahan
    document.getElementById('addRecipeItem').addEventListener('click', addRecipeItem);

    // Recalculate ketika bahan / quantity berubah
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('raw-material-select') || e.target.classList.contains('quantity-input')) {
            const item = e.target.closest('.recipe-item');
            if (item) {
                calculateItemCost(item);
                calculateTotalMaterialCost();
                updateHppSummary();
                updateFinalPricing();
            }
        }

        if (e.target.name === 'output_quantity') {
            updateHppSummary();
            updateFinalPricing();
        }
    });

    // Input biaya tambahan & harga jual
    document.getElementById('additionalCostInput').addEventListener('input', function() {
        updateHppSummary();
        updateFinalPricing();
    });

    document.getElementById('sellingPriceInput').addEventListener('input', calculateMargin);

    // Hapus bahan
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
            const item = e.target.closest('.recipe-item');
            if (item) {
                item.remove();
                calculateTotalMaterialCost();
                updateHppSummary();
                updateFinalPricing();
                updateRemoveButtons();
            }
        }
    });

    updateRemoveButtons();
});

function addRecipeItem() {
    const container = document.getElementById('recipeItemsContainer');
    const newItem = document.createElement('div');
    newItem.className = 'recipe-item bg-gray-50 p-5 rounded-lg mb-4 border border-gray-200';
    newItem.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-box text-gray-400 mr-1"></i>
                    Bahan Baku <span class="text-red-500">*</span>
                </label>
                <select name="recipe_items[${recipeItemIndex}][raw_material_id]" class="raw-material-select select2 w-full" required data-placeholder="Pilih Bahan Baku">
                    <option value=""></option>
                    @foreach($rawMaterials as $rm)
                    <option value="{{ $rm->id }}" data-price="{{ $rm->purchase_price }}" data-unit="{{ $rm->unit->name ?? '' }}">
                        {{ $rm->name }} ({{ $rm->unit->name ?? '' }}) - Rp {{ number_format($rm->purchase_price, 0, ',', '.') }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calculator text-gray-400 mr-1"></i>
                    Jumlah <span class="text-red-500">*</span>
                </label>
                <input type="number" step="0.01" name="recipe_items[${recipeItemIndex}][quantity]" class="quantity-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-balance-scale text-gray-400 mr-1"></i>
                    Satuan
                </label>
                <input type="text" class="unit-display w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100" readonly>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-money-bill-wave text-gray-400 mr-1"></i>
                    Biaya
                </label>
                <input type="text" class="cost-display w-full px-4 py-3 border border-gray-300 rounded-lg bg-blue-50 font-semibold text-cuan-green" readonly value="Rp 0">
            </div>
            <div class="md:col-span-1 flex items-end">
                <button type="button" class="remove-item w-full px-4 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="mt-3">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-sticky-note text-gray-400 mr-1"></i>
                Catatan
            </label>
            <input type="text" name="recipe_items[${recipeItemIndex}][notes]" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Catatan opsional untuk bahan ini">
        </div>
    `;

    container.appendChild(newItem);

    $(newItem).find('.select2').select2({
        theme: 'default',
        width: '100%',
        placeholder: 'Pilih Bahan Baku'
    });

    recipeItemIndex++;
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const items = document.querySelectorAll('.recipe-item');
    items.forEach((item) => {
        const removeBtn = item.querySelector('.remove-item');
        if (items.length > 1) {
            removeBtn.style.display = 'block';
        } else {
            removeBtn.style.display = 'none';
        }
    });
}

function calculateItemCost(item) {
    const select = item.querySelector('.raw-material-select');
    const quantityInput = item.querySelector('.quantity-input');
    const unitDisplay = item.querySelector('.unit-display');
    const costDisplay = item.querySelector('.cost-display');

    if (!select || !quantityInput) return;

    const selectedOption = select.options[select.selectedIndex] || {};
    const price = parseFloat(selectedOption.dataset?.price || 0);
    const unit = selectedOption.dataset?.unit || '';
    const quantity = parseFloat(quantityInput.value || 0);

    if (unitDisplay) {
        unitDisplay.value = unit;
    }

    const cost = price * quantity;
    if (costDisplay) {
        costDisplay.value = 'Rp ' + cost.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    }
}

function calculateTotalMaterialCost() {
    let total = 0;

    document.querySelectorAll('.recipe-item').forEach(item => {
        const select = item.querySelector('.raw-material-select');
        const quantityInput = item.querySelector('.quantity-input');

        if (!select || !quantityInput) return;

        const selectedOption = select.options[select.selectedIndex] || {};
        const price = parseFloat(selectedOption.dataset?.price || 0);
        const quantity = parseFloat(quantityInput.value || 0);

        total += (price * quantity);
    });

    totalMaterialCostValue = total;
    const totalEl = document.getElementById('totalMaterialCost');
    if (totalEl) {
        totalEl.textContent = 'Rp ' + total.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    }
}

function updateHppSummary() {
    const outputQtyInput = document.querySelector('input[name="output_quantity"]');
    const outputQty = parseFloat(outputQtyInput?.value || 1);
    const additionalCostInput = document.getElementById('additionalCostInput');
    const additionalCost = parseFloat(additionalCostInput?.value || 0);
    const totalHpp = totalMaterialCostValue + additionalCost;
    const hppPerUnit = outputQty > 0 ? (totalHpp / outputQty) : 0;

    const setText = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
    };

    setText('summaryMaterialCost', 'Rp ' + totalMaterialCostValue.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0}));
    setText('summaryAdditionalCost', 'Rp ' + additionalCost.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0}));
    setText('summaryTotalHpp', 'Rp ' + totalHpp.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0}));
    setText('summaryOutputQty', outputQty);
    setText('summaryHppPerUnit', 'Rp ' + hppPerUnit.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0}));

    // simpan global
    window.hppPerUnitValue = hppPerUnit;
}

function updateFinalPricing() {
    const outputQtyInput = document.querySelector('input[name="output_quantity"]');
    const outputQty = parseFloat(outputQtyInput?.value || 1);
    const additionalCostInput = document.getElementById('additionalCostInput');
    const additionalCost = parseFloat(additionalCostInput?.value || 0);
    const totalHpp = totalMaterialCostValue + additionalCost;
    const hppPerUnit = outputQty > 0 ? (totalHpp / outputQty) : 0;

    const finalHppEl = document.getElementById('finalHppPerUnit');
    const marginHppEl = document.getElementById('marginHpp');

    if (finalHppEl) {
        finalHppEl.textContent = 'Rp ' + hppPerUnit.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    }
    if (marginHppEl) {
        marginHppEl.textContent = 'Rp ' + hppPerUnit.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    }

    window.hppPerUnitValue = hppPerUnit;
    calculateMargin();
}

function calculateMargin() {
    const sellingPriceInput = document.getElementById('sellingPriceInput');
    if (!sellingPriceInput) return;

    const sellingPrice = parseFloat(sellingPriceInput.value || 0);
    const hpp = window.hppPerUnitValue || 0;
    const profit = sellingPrice - hpp;
    const marginPercent = hpp > 0 ? ((profit / hpp) * 100) : 0;

    const setText = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
    };

    setText('marginSellingPrice', 'Rp ' + sellingPrice.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0}));
    setText('marginProfit', 'Rp ' + profit.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0}));

    const marginEl = document.getElementById('marginPercent');
    if (marginEl) {
        marginEl.textContent = marginPercent.toFixed(1) + '%';

        if (marginPercent >= 30) {
            marginEl.className = 'text-2xl font-bold text-green-600';
        } else if (marginPercent >= 15) {
            marginEl.className = 'text-2xl font-bold text-yellow-600';
        } else {
            marginEl.className = 'text-2xl font-bold text-red-600';
        }
    }
}
</script>
@endpush

@endsection

@extends('layouts.app')

@section('title', 'Tambah Produk & Resep - CuanFlow')

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
    <span class="text-gray-900 font-medium">Tambah Produk</span>
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
            <!-- Progress Steps -->
            <div class="bg-gradient-to-r from-green-50 to-blue-50 p-6 border-b border-gray-200">
                <div class="flex justify-between items-center relative">
                    <!-- Progress Line -->
                    <div class="absolute top-5 left-0 right-0 h-0.5 bg-gray-200" style="z-index: 0;">
                        <div id="progressLine" class="h-full bg-cuan-green transition-all duration-300" style="width: 0%;"></div>
                    </div>

                    <!-- Step 1 -->
                    <div class="flex-1 text-center step-indicator active relative z-10" data-step="1">
                        <div class="w-10 h-10 bg-cuan-green rounded-full flex items-center justify-center mx-auto mb-2 shadow-md">
                            <i class="fas fa-info-circle text-white"></i>
                        </div>
                        {{-- <p class="text-xs font-medium text-gray-900">Info Dasar</p> --}}
                    </div>

                    <!-- Step 2 -->
                    <div class="flex-1 text-center step-indicator relative z-10" data-step="2">
                        <div class="w-10 h-10 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-book-open text-gray-400"></i>
                        </div>
                        {{-- <p class="text-xs font-medium text-gray-500">Info Resep</p> --}}
                    </div>

                    <!-- Step 3 -->
                    <div class="flex-1 text-center step-indicator relative z-10" data-step="3">
                        <div class="w-10 h-10 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-shopping-basket text-gray-400"></i>
                        </div>
                        {{-- <p class="text-xs font-medium text-gray-500">Bahan Baku</p> --}}
                    </div>

                    <!-- Step 4 -->
                    <div class="flex-1 text-center step-indicator relative z-10" data-step="4">
                        <div class="w-10 h-10 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-coins text-gray-400"></i>
                        </div>
                        {{-- <p class="text-xs font-medium text-gray-500">Biaya Tambahan</p> --}}
                    </div>

                    <!-- Step 5 -->
                    <div class="flex-1 text-center step-indicator relative z-10" data-step="5">
                        <div class="w-10 h-10 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-tags text-gray-400"></i>
                        </div>
                        {{-- <p class="text-xs font-medium text-gray-500">Harga & Stok</p> --}}
                    </div>
                </div>
            </div>

            <form action="{{ route('products-hpp.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
                @csrf

                <!-- Step 1: Basic Info -->
                <div class="step-content p-6" id="step1">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-info-circle text-cuan-green mr-2"></i>
                            Informasi Dasar Produk
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Masukkan informasi dasar tentang produk yang akan dibuat</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-barcode text-gray-400 mr-1"></i>
                                Kode Produk <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" name="code" id="productCode" value="{{ old('code') }}" 
                                    class="w-full px-4 py-3 pr-28 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                    placeholder="Contoh: PRD001" required>
                                <button type="button" id="generateCode" class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1.5 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition-colors">
                                    <i class="fas fa-magic mr-1"></i>
                                    Buat Kode
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-tag text-gray-400 mr-1"></i>
                                Nama Produk <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                placeholder="Masukkan nama produk" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-qrcode text-gray-400 mr-1"></i>
                                Barcode
                            </label>
                            <div class="relative">
                                <input type="text" name="barcode" id="productBarcode" value="{{ old('barcode') }}" 
                                    class="w-full px-4 py-3 pr-28 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                    placeholder="Opsional">
                                <button type="button" id="generateBarcode" class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1.5 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition-colors">
                                    <i class="fas fa-magic mr-1"></i>
                                    Buat Kode
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-folder text-gray-400 mr-1"></i>
                                Kategori
                            </label>
                            <select name="category_id" class="select2-category w-full">
                                <option value="">- Pilih Kategori -</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
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
                            <div id="imagePreview" class="mt-3 hidden">
                                <div class="relative inline-block">
                                    <img id="previewImg" src="" alt="Preview" class="w-32 h-32 object-cover rounded-lg border-2 border-gray-300 shadow-sm">
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
                                placeholder="Deskripsi produk (opsional)">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Recipe Info -->
                <div class="step-content p-6 hidden" id="step2">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-book-open text-cuan-green mr-2"></i>
                            Informasi Resep
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Tentukan resep untuk produksi produk ini</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-signature text-gray-400 mr-1"></i>
                                Nama Resep <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="recipe_name" value="{{ old('recipe_name') }}" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                placeholder="Contoh: Resep Kue Original" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-layer-group text-gray-400 mr-1"></i>
                                Jumlah Output <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="output_quantity" value="{{ old('output_quantity', 1) }}" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                placeholder="1" required>
                            <p class="text-xs text-gray-500 mt-1">Berapa banyak produk yang dihasilkan dari resep ini</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-clock text-gray-400 mr-1"></i>
                                Estimasi Waktu (menit)
                            </label>
                            <input type="number" name="estimated_time_minutes" value="{{ old('estimated_time_minutes') }}" 
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
                                placeholder="1. Langkah pertama...&#10;2. Langkah kedua...&#10;3. Dan seterusnya...">{{ old('instructions') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Recipe Items -->
                <div class="step-content p-6 hidden" id="step3">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-shopping-basket text-cuan-green mr-2"></i>
                            Bahan Baku yang Dibutuhkan
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Tambahkan bahan baku dan jumlah yang diperlukan untuk resep ini</p>
                    </div>
                    
                    <div id="recipeItemsContainer" class="space-y-4">
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
                    </div>

                    <button type="button" id="addRecipeItem" class="mt-4 px-5 py-2.5 bg-cuan-green text-white rounded-lg hover:bg-cuan-olive transition-colors flex items-center text-sm font-medium">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Bahan
                    </button>

                    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-5">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-semibold text-gray-700 flex items-center">
                                <i class="fas fa-calculator text-cuan-green mr-2"></i>
                                Total Biaya Bahan Baku:
                            </span>
                            <span id="totalMaterialCost" class="text-xl font-bold text-cuan-green">Rp 0</span>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Additional Costs -->
                <div class="step-content p-6 hidden" id="step4">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-coins text-cuan-green mr-2"></i>
                            Biaya Tambahan
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Tambahkan biaya overhead seperti listrik, gas, packaging, dll</p>
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-cuan-green mt-0.5 mr-3"></i>
                            <p class="text-sm text-blue-800">
                                Biaya tambahan mencakup semua pengeluaran diluar bahan baku yang diperlukan dalam proses produksi seperti listrik, gas, packaging, tenaga kerja, dan lain-lain.
                            </p>
                        </div>
                    </div>

                    <div class="max-w-md">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-money-bill-wave text-gray-400 mr-1"></i>
                            Biaya Tambahan (Rp)
                        </label>
                        <input type="number" step="0.01" name="additional_cost" id="additionalCostInput" value="{{ old('additional_cost', 0) }}" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                            placeholder="0">
                        <p class="text-xs text-gray-500 mt-1">Masukkan 0 jika tidak ada biaya tambahan</p>
                    </div>

                    <div class="mt-8 bg-gradient-to-br from-green-50 to-blue-50 border border-green-200 rounded-lg p-6">
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

                <!-- Step 5: Pricing & Stock -->
                <div class="step-content p-6 hidden" id="step5">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-tags text-cuan-green mr-2"></i>
                            Harga Jual & Stok
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Tentukan harga jual dan pengaturan stok produk</p>
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
                            <input type="number" step="0.01" name="selling_price" id="sellingPriceInput" value="{{ old('selling_price') }}" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-tag text-gray-400 mr-1"></i>
                                Harga Reseller
                            </label>
                            <input type="number" step="0.01" name="reseller_price" value="{{ old('reseller_price') }}" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Harga untuk reseller (opsional)</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-percent text-gray-400 mr-1"></i>
                                Harga Promo
                            </label>
                            <input type="number" step="0.01" name="promo_price" value="{{ old('promo_price') }}" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Harga saat promo (opsional)</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-boxes text-gray-400 mr-1"></i>
                                Minimum Stok
                            </label>
                            <input type="number" step="0.01" name="min_stock" value="{{ old('min_stock', 0) }}" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Alert jika stok di bawah angka ini</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-day text-gray-400 mr-1"></i>
                                Masa Simpan (hari)
                            </label>
                            <input type="number" name="shelf_life_days" value="{{ old('shelf_life_days') }}" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Berapa hari produk bisa disimpan</p>
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
                </div>

                <!-- Navigation Buttons -->
                <div class="px-8 py-6 bg-gray-50 border-t border-gray-200 flex justify-between">
                    <button type="button" id="prevBtn" class="px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-semibold flex items-center" style="display: none;">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Sebelumnya
                    </button>
                    <div></div>
                    <button type="button" id="nextBtn" class="px-6 py-3 bg-cuan-green text-white rounded-lg hover:bg-cuan-olive transition-all font-semibold flex items-center shadow-md hover:shadow-lg">
                        Selanjutnya
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                    <button type="submit" id="submitBtn" class="px-6 py-3 bg-cuan-green text-white rounded-lg hover:bg-green-700 transition-all font-semibold flex items-center shadow-md hover:shadow-lg" style="display: none;">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Produk
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
let currentStep = 1;
const totalSteps = 5;
let recipeItemIndex = 1;
let totalMaterialCostValue = 0;

const imageInput = document.getElementById('imageInput');
const imagePreview = document.getElementById('imagePreview');
const previewImg = document.getElementById('previewImg');
const removeImageBtn = document.getElementById('removeImage');

if (imageInput) {
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            // Validasi ukuran file (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file terlalu besar! Maksimal 2MB');
                imageInput.value = '';
                return;
            }
            
            // Validasi tipe file
            if (!file.type.match('image.*')) {
                alert('File harus berupa gambar (JPG, JPEG, PNG)');
                imageInput.value = '';
                return;
            }
            
            // Tampilkan preview
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
    
    // Initialize Select2 untuk raw material (item pertama)
    $('.raw-material-select').select2({
        theme: 'default',
        width: '100%',
        placeholder: '- Pilih Bahan -'
    });
    
    showStep(currentStep);
    
    // Next button
    document.getElementById('nextBtn').addEventListener('click', function() {
        if (validateStep(currentStep)) {
            if (currentStep === 3) {
                updateHppSummary();
            }
            if (currentStep === 4) {
                updateFinalPricing();
            }
            currentStep++;
            showStep(currentStep);
        }
    });
    
    // Previous button
    document.getElementById('prevBtn').addEventListener('click', function() {
        currentStep--;
        showStep(currentStep);
    });
    
    // Add recipe item
    document.getElementById('addRecipeItem').addEventListener('click', addRecipeItem);
    
    // Calculate material cost on change
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('raw-material-select') || e.target.classList.contains('quantity-input')) {
            calculateItemCost(e.target.closest('.recipe-item'));
            calculateTotalMaterialCost();
        }
    });
    
    // Update additional cost
    document.getElementById('additionalCostInput').addEventListener('input', updateHppSummary);
    
    // Update margin calculation
    document.getElementById('sellingPriceInput').addEventListener('input', calculateMargin);
    
    // Remove item handler
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
            const item = e.target.closest('.recipe-item');
            if (item) {
                item.remove();
                calculateTotalMaterialCost();
                updateRemoveButtons();
            }
        }
    });

// Generate Code Button
document.getElementById('generateCode').addEventListener('click', function() {
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Loading...';
    
    fetch('{{ route("products-hpp.generate-code") }}')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            document.getElementById('productCode').value = data.code;
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-magic mr-1"></i>Buat Kode';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal generate kode: ' + error.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-magic mr-1"></i>Buat Kode';
        });
});

// Generate Barcode Button
document.getElementById('generateBarcode').addEventListener('click', function() {
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Loading...';
    
    fetch('{{ route("products-hpp.generate-barcode") }}')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            document.getElementById('productBarcode').value = data.barcode;
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-magic mr-1"></i>Buat Kode';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal generate barcode: ' + error.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-magic mr-1"></i>Buat Kode';
        });
});
    
    // Initial calculation
    calculateItemCost(document.querySelector('.recipe-item'));
});

function showStep(step) {
    // Hide all steps
    document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
    
    // Show current step
    document.getElementById('step' + step).classList.remove('hidden');
    
    // Update progress indicators
    document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
        const stepNum = index + 1;
        const circle = indicator.querySelector('div');
        const icon = circle ? circle.querySelector('i') : null;
        
        if (!circle) return; // Skip jika elemen tidak ditemukan
        
        if (stepNum < step) {
            // Completed step
            circle.className = 'w-10 h-10 bg-cuan-green rounded-full flex items-center justify-center mx-auto mb-2 shadow-md';
            if (icon) {
                icon.className = 'fas fa-check text-white';
            }
        } else if (stepNum === step) {
            // Current step
            circle.className = 'w-10 h-10 bg-cuan-green rounded-full flex items-center justify-center mx-auto mb-2 shadow-md ring-4 ring-green-200';
            if (icon) {
                icon.className = icon.className; // Keep original icon
            }
        } else {
            // Future step
            circle.className = 'w-10 h-10 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center mx-auto mb-2';
            if (icon) {
                icon.className = icon.className.replace('text-white', 'text-gray-400');
            }
        }
    });
    
    // Update progress line
    const progressPercent = ((step - 1) / (totalSteps - 1)) * 100;
    document.getElementById('progressLine').style.width = progressPercent + '%';
    
    // Show/hide buttons
    document.getElementById('prevBtn').style.display = step > 1 ? 'flex' : 'none';
    document.getElementById('nextBtn').style.display = step < totalSteps ? 'flex' : 'none';
    document.getElementById('submitBtn').style.display = step === totalSteps ? 'flex' : 'none';
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}



function validateStep(step) {
    const currentStepEl = document.getElementById('step' + step);
    const requiredInputs = currentStepEl.querySelectorAll('[required]');
    
    for (let input of requiredInputs) {
        if (!input.value) {
            alert('Mohon lengkapi semua field yang wajib diisi (*)');
            input.focus();
            return false;
        }
    }
    
    if (step === 3) {
        const recipeItems = document.querySelectorAll('.recipe-item');
        if (recipeItems.length === 0) {
            alert('Minimal harus ada 1 bahan baku');
            return false;
        }
    }
    
    return true;
}

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
    
    // Initialize Select2 for new item
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
    items.forEach((item, index) => {
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
    
    const selectedOption = select.options[select.selectedIndex];
    const price = parseFloat(selectedOption.dataset.price || 0);
    const unit = selectedOption.dataset.unit || '';
    const quantity = parseFloat(quantityInput.value || 0);
    
    unitDisplay.value = unit;
    
    const cost = price * quantity;
    costDisplay.value = 'Rp ' + cost.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
}

function calculateTotalMaterialCost() {
    let total = 0;
    
    document.querySelectorAll('.recipe-item').forEach(item => {
        const select = item.querySelector('.raw-material-select');
        const quantityInput = item.querySelector('.quantity-input');
        
        const selectedOption = select.options[select.selectedIndex];
        const price = parseFloat(selectedOption.dataset.price || 0);
        const quantity = parseFloat(quantityInput.value || 0);
        
        total += (price * quantity);
    });
    
    totalMaterialCostValue = total;
    document.getElementById('totalMaterialCost').textContent = 'Rp ' + total.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
}

function updateHppSummary() {
    const outputQty = parseFloat(document.querySelector('input[name="output_quantity"]').value || 1);
    const additionalCost = parseFloat(document.getElementById('additionalCostInput').value || 0);
    const totalHpp = totalMaterialCostValue + additionalCost;
    const hppPerUnit = totalHpp / outputQty;
    
    document.getElementById('summaryMaterialCost').textContent = 'Rp ' + totalMaterialCostValue.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    document.getElementById('summaryAdditionalCost').textContent = 'Rp ' + additionalCost.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    document.getElementById('summaryTotalHpp').textContent = 'Rp ' + totalHpp.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    document.getElementById('summaryOutputQty').textContent = outputQty;
    document.getElementById('summaryHppPerUnit').textContent = 'Rp ' + hppPerUnit.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
}

function updateFinalPricing() {
    const outputQty = parseFloat(document.querySelector('input[name="output_quantity"]').value || 1);
    const additionalCost = parseFloat(document.getElementById('additionalCostInput').value || 0);
    const totalHpp = totalMaterialCostValue + additionalCost;
    const hppPerUnit = totalHpp / outputQty;
    
    document.getElementById('finalHppPerUnit').textContent = 'Rp ' + hppPerUnit.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    document.getElementById('marginHpp').textContent = 'Rp ' + hppPerUnit.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    
    // Store for margin calculation
    window.hppPerUnitValue = hppPerUnit;
    
    calculateMargin();
}

function calculateMargin() {
    const sellingPrice = parseFloat(document.getElementById('sellingPriceInput').value || 0);
    const hpp = window.hppPerUnitValue || 0;
    const profit = sellingPrice - hpp;
    const marginPercent = hpp > 0 ? ((profit / hpp) * 100) : 0;
    
    document.getElementById('marginSellingPrice').textContent = 'Rp ' + sellingPrice.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    document.getElementById('marginProfit').textContent = 'Rp ' + profit.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    
    const marginEl = document.getElementById('marginPercent');
    marginEl.textContent = marginPercent.toFixed(1) + '%';
    
    // Color coding for margin
    if (marginPercent >= 30) {
        marginEl.className = 'text-2xl font-bold text-green-600';
    } else if (marginPercent >= 15) {
        marginEl.className = 'text-2xl font-bold text-yellow-600';
    } else {
        marginEl.className = 'text-2xl font-bold text-red-600';
    }
}
</script>
@endpush

@endsection
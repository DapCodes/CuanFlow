@extends('layouts.app')

@section('title', 'Tambah Produk & Resep - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

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

    /* Modal Animation Styles */
    @keyframes modalFadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes modalSlideUp {
        from {
            opacity: 0;
            transform: scale(0.95) translateY(20px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    @keyframes iconBounce {
        0% {
            transform: scale(0);
        }
        50% {
            transform: scale(1.1);
        }
        100% {
            transform: scale(1);
        }
    }

    .modal-show .modal-overlay {
        animation: modalFadeIn 0.3s ease-out forwards;
    }

    .modal-show .modal-panel {
        animation: modalSlideUp 0.3s ease-out forwards;
    }

    .modal-show .modal-icon {
        animation: iconBounce 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55) 0.2s forwards;
    }

    /* Smooth hide animation */
    .modal-hide {
        pointer-events: none;
    }

    .modal-hide .modal-overlay {
        opacity: 0;
        transition: opacity 0.2s ease-in;
    }

    .modal-hide .modal-panel {
        opacity: 0;
        transform: scale(0.95);
        transition: all 0.2s ease-in;
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
                        <!-- Code -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-barcode text-gray-400 mr-1"></i>
                                Kode Produk <span class="text-red-500">*</span>
                            </label>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <input type="text" name="code" id="productCode" value="{{ old('code') }}" 
                                    class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                    placeholder="Contoh: PRD001" required>
                                <button type="button" id="generateCode" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors whitespace-nowrap">
                                    <i class="fas fa-magic mr-2"></i>
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

                        <!-- Barcode -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-qrcode text-gray-400 mr-1"></i>
                                Barcode
                            </label>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <input type="text" name="barcode" id="productBarcode" value="{{ old('barcode') }}" 
                                    class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                    placeholder="Opsional">
                                <button type="button" id="generateBarcode" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors whitespace-nowrap">
                                    <i class="fas fa-magic mr-2"></i>
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

                    <button type="button" id="addRecipeItem" class="mt-4 w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 bg-cuan-green text-white rounded-lg hover:bg-cuan-olive transition-colors text-sm font-medium">
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

                <!-- Step 6: Sales Target & Analysis -->
                <div class="step-content p-6 hidden" id="step6">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-chart-line text-cuan-green mr-2"></i>
                            Analisis Target Penjualan
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Tentukan target omzet dan lihat proyeksi penjualan berdasarkan data historis</p>
                    </div>

                    <!-- Toggle Enable Target -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-5 mb-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="enable_sales_target" value="1" id="enableSalesTarget" class="w-5 h-5 text-cuan-green rounded focus:ring-2 focus:ring-green-500">
                            <span class="ml-3 text-sm font-semibold text-gray-800">
                                <i class="fas fa-bullseye mr-2 text-cuan-green"></i>
                                Aktifkan Target Penjualan untuk Produk Ini
                            </span>
                        </label>
                        <p class="text-xs text-gray-600 ml-8 mt-1">Pantau pencapaian penjualan dan dapatkan insights bisnis yang lebih baik</p>
                    </div>

                    <div id="salesTargetContent" class="hidden">
                        <!-- Historical Sales Analysis -->
                        <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-xl p-6 mb-6 border border-purple-200">
                            <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-history text-purple-600 mr-2"></i>
                                Analisis Penjualan Historis (30 Hari Terakhir)
                            </h4>
                            
                            <div id="historicalDataLoading" class="text-center py-8">
                                <i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-2"></i>
                                <p class="text-gray-600">Memuat data penjualan...</p>
                            </div>

                            <div id="historicalDataContent" class="hidden">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <p class="text-xs text-gray-500 mb-1">Total Terjual</p>
                                        <p class="text-2xl font-bold text-purple-600" id="totalSold30Days">0 pcs</p>
                                    </div>
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <p class="text-xs text-gray-500 mb-1">Rata-rata/Hari</p>
                                        <p class="text-2xl font-bold text-blue-600" id="avgDailySales">0 pcs</p>
                                    </div>
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <p class="text-xs text-gray-500 mb-1">Hari Terbaik</p>
                                        <p class="text-2xl font-bold text-green-600" id="bestSalesDay">-</p>
                                    </div>
                                </div>

                                <!-- Charts Container -->
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <!-- Weekly Trend Chart -->
                                    <div class="bg-white rounded-lg p-5 shadow-sm">
                                        <h5 class="font-semibold text-gray-800 mb-3 flex items-center">
                                            <i class="fas fa-chart-bar text-blue-500 mr-2"></i>
                                            Tren Penjualan Mingguan
                                        </h5>
                                        <canvas id="weeklyTrendChart" height="250"></canvas>
                                    </div>

                                    <!-- Daily Pattern Chart -->
                                    <div class="bg-white rounded-lg p-5 shadow-sm">
                                        <h5 class="font-semibold text-gray-800 mb-3 flex items-center">
                                            <i class="fas fa-calendar-week text-purple-500 mr-2"></i>
                                            Pola Penjualan Harian
                                        </h5>
                                        <canvas id="dailyPatternChart" height="250"></canvas>
                                    </div>
                                </div>
                            </div>

                            <div id="noHistoricalData" class="hidden text-center py-8">
                                <i class="fas fa-inbox text-5xl text-gray-300 mb-3"></i>
                                <p class="text-gray-600 font-medium">Belum ada data penjualan historis</p>
                                <p class="text-sm text-gray-500 mt-1">Data akan tersedia setelah produk terjual</p>
                            </div>
                        </div>

                        <!-- Target Revenue Input -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-money-bill-wave text-gray-400 mr-1"></i>
                                    Target Omzet Per Bulan (Rp) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.01" name="monthly_target_revenue" id="monthlyTargetRevenue"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    placeholder="100000000">
                                <p class="text-xs text-gray-500 mt-1">Berapa target pendapatan per bulan dari produk ini?</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-calendar-alt text-gray-400 mr-1"></i>
                                    Tanggal Mulai Target
                                </label>
                                <input type="date" name="target_start_date" id="targetStartDate" value="{{ date('Y-m-d') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <!-- Sales Target Calculation Result -->
                        <div id="targetCalculationResult" class="hidden">
                            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-6 border-2 border-green-300 mb-6">
                                <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-calculator text-green-600 mr-2"></i>
                                    Perhitungan Target Penjualan
                                </h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <p class="text-xs text-gray-600 mb-1">Target Penjualan/Bulan</p>
                                        <p class="text-2xl font-bold text-green-600" id="monthlySalesTarget">0 pcs</p>
                                    </div>
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <p class="text-xs text-gray-600 mb-1">Target Penjualan/Hari</p>
                                        <p class="text-2xl font-bold text-blue-600" id="dailySalesTarget">0 pcs</p>
                                    </div>
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <p class="text-xs text-gray-600 mb-1">Revenue Harian Target</p>
                                        <p class="text-xl font-bold text-purple-600" id="dailyRevenueTarget">Rp 0</p>
                                    </div>
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <p class="text-xs text-gray-600 mb-1">Profit Bulanan Target</p>
                                        <p class="text-xl font-bold text-emerald-600" id="monthlyProfitTarget">Rp 0</p>
                                    </div>
                                </div>

                                <!-- Achievement Indicator -->
                                <div class="bg-white rounded-lg p-5 shadow-sm">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-semibold text-gray-700">Perbandingan dengan Performa Saat Ini</span>
                                        <span id="achievementPercent" class="text-lg font-bold text-gray-800">0%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                        <div id="achievementBar" class="h-full bg-gradient-to-r from-green-400 to-green-600 transition-all duration-500" style="width: 0%"></div>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-2" id="achievementNote">Berdasarkan rata-rata penjualan 30 hari terakhir</p>
                                </div>
                            </div>

                            <!-- Projection Chart -->
                            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200">
                                <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-chart-area text-indigo-600 mr-2"></i>
                                    Proyeksi Pencapaian Target
                                </h4>
                                <div style="position: relative; height: 250px; width: 100%;">
                                    <canvas id="projectionChart"></canvas>
                                </div>
                                
                                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                                        <p class="text-xs text-gray-600 mb-1">Scenario Optimis</p>
                                        <p class="text-xl font-bold text-blue-600" id="optimisticScenario">0 hari</p>
                                        <p class="text-xs text-gray-500">+20% dari rata-rata</p>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                                        <p class="text-xs text-gray-600 mb-1">Scenario Realistis</p>
                                        <p class="text-xl font-bold text-gray-600" id="realisticScenario">0 hari</p>
                                        <p class="text-xs text-gray-500">Sesuai rata-rata</p>
                                    </div>
                                    <div class="bg-red-50 rounded-lg p-4 text-center">
                                        <p class="text-xs text-gray-600 mb-1">Scenario Pesimis</p>
                                        <p class="text-xl font-bold text-red-600" id="pessimisticScenario">0 hari</p>
                                        <p class="text-xs text-gray-500">-20% dari rata-rata</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden inputs for form submission -->
                            <input type="hidden" name="daily_sales_target" id="hiddenDailySalesTarget">
                            <input type="hidden" name="monthly_sales_target" id="hiddenMonthlySalesTarget">
                            <input type="hidden" name="daily_revenue_target" id="hiddenDailyRevenueTarget">
                            <input type="hidden" name="sales_pattern" id="hiddenSalesPattern">
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="px-6 py-6 bg-gray-50 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3">
                        <button type="button" id="prevBtn" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-semibold" style="display: none;">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Sebelumnya
                        </button>
                        <div class="hidden sm:block"></div>
                        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                            <button type="button" id="nextBtn" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-cuan-green text-white rounded-lg hover:bg-cuan-olive transition-all font-semibold shadow-md hover:shadow-lg order-2 sm:order-1">
                                Selanjutnya
                                <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                            <button type="submit" id="submitBtn" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-cuan-green text-white rounded-lg hover:bg-green-700 transition-all font-semibold shadow-md hover:shadow-lg order-1 sm:order-2" style="display: none;">
                                <i class="fas fa-save mr-2"></i>
                                Simpan Produk
                            </button>
                        </div>
                    </div>
                </div>

            </form>

        </x-card-container>

    </div>
</main>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div id="draftModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity duration-300 ease-out modal-overlay" style="opacity: 0;"></div>
    
    <div class="flex min-h-screen items-center justify-center p-4 sm:p-6">
        <div class="relative transform overflow-hidden rounded-xl bg-white shadow-xl transition-all duration-300 ease-out w-full max-w-sm modal-panel" style="opacity: 0; transform: scale(0.95);">
            
            <div class="p-6 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-cuan-olive/20 text-cuan-dark mb-4 modal-icon" style="transform: scale(0);">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                </div>
                
                <h3 class="text-xl font-semibold text-gray-900 mb-2" id="modal-title">
                    Draft Ditemukan
                </h3>
                <p class="text-sm text-gray-600">
                    Anda memiliki draft yang belum selesai. Lanjutkan pengisian?
                </p>
                
                <div class="mt-4 inline-flex items-center px-3 py-1 rounded-full bg-cuan-yellow/50 border border-cuan-olive/50">
                    <svg class="w-3 h-3 text-cuan-dark mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs font-medium text-cuan-dark" id="draftTimestamp">Disimpan 10 menit lalu</span>
                </div>
            </div>
            
            <div class="px-6 pb-6 space-y-3">
                <button id="loadDraftBtn" class="w-full inline-flex items-center justify-center px-4 py-3 bg-cuan-dark hover:bg-cuan-green text-white font-medium rounded-lg transition-colors duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Lanjutkan Draft
                </button>
                
                <button id="discardDraftBtn" class="w-full inline-flex items-center justify-center px-4 py-3 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg border border-gray-300 transition-colors duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Mulai dari Awal
                </button>
            </div>
        </div>
    </div>
</div>

<div id="exitModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="exit-modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity duration-300 ease-out modal-overlay" style="opacity: 0;"></div>
    
    <div class="flex min-h-screen items-center justify-center p-4 sm:p-6">
        <div class="relative transform overflow-hidden rounded-xl bg-white shadow-xl transition-all duration-300 ease-out w-full max-w-sm modal-panel" style="opacity: 0; transform: scale(0.95);">
            
            <div class="p-6 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-cuan-yellow/40 text-cuan-olive mb-4 modal-icon" style="transform: scale(0);">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                
                <h3 class="text-xl font-semibold text-gray-900 mb-2" id="exit-modal-title">
                    Simpan Perubahan?
                </h3>
                <p class="text-sm text-gray-600">
                    Anda memiliki perubahan yang belum disimpan. Pilih tindakan yang ingin dilakukan.
                </p>
            </div>
            
            <div class="px-6 pb-6 space-y-3">
                <button id="saveDraftExitBtn" class="w-full inline-flex items-center justify-center px-4 py-3 bg-cuan-dark hover:bg-cuan-green text-white font-medium rounded-lg transition-colors duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Simpan Draft
                </button>
                
                <button id="discardExitBtn" class="w-full inline-flex items-center justify-center px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Buang Perubahan
                </button>
                
                <button id="cancelExitBtn" class="w-full inline-flex items-center justify-center px-4 py-3 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg border border-gray-300 transition-colors duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentStep = 1;
const totalSteps = 6;
let recipeItemIndex = 1;
let totalMaterialCostValue = 0;
let historicalSalesData = null;
let weeklyTrendChart = null;
let dailyPatternChart = null;
let projectionChart = null;

// Draft Management Variables
const STORAGE_KEY = 'cuanflow_product_create_form_v1';
const DRAFT_TIMESTAMP_KEY = 'cuanflow_product_draft_timestamp';
let isDraftLoaded = false;
let formHasChanges = false;
let isNavigatingAway = false;
let pendingNavigation = null;

const imageInput = document.getElementById('imageInput');
const imagePreview = document.getElementById('imagePreview');
const previewImg = document.getElementById('previewImg');
const removeImageBtn = document.getElementById('removeImage');

// Image handling code (sama seperti sebelumnya)
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

// ============================================
// DRAFT MANAGEMENT FUNCTIONS
// ============================================

function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

function checkForDraft() {
    const savedData = localStorage.getItem(STORAGE_KEY);
    const hasOldInput = "{{ old('code') }}" !== "";
    
    if (savedData && !hasOldInput) {
        showDraftModal();
    }
}

function showDraftModal() {
    const modal = document.getElementById('draftModal');
    modal.classList.remove('hidden', 'modal-hide');
    
    // Update timestamp if available
    const timestamp = localStorage.getItem(DRAFT_TIMESTAMP_KEY);
    if (timestamp) {
        const draftDate = new Date(timestamp);
        const now = new Date();
        const diffMinutes = Math.floor((now - draftDate) / 1000 / 60);
        
        let timeText;
        if (diffMinutes < 1) {
            timeText = 'Baru saja';
        } else if (diffMinutes < 60) {
            timeText = `${diffMinutes} menit lalu`;
        } else if (diffMinutes < 1440) {
            const hours = Math.floor(diffMinutes / 60);
            timeText = `${hours} jam lalu`;
        } else {
            const days = Math.floor(diffMinutes / 1440);
            timeText = `${days} hari lalu`;
        }
        
        document.getElementById('draftTimestamp').textContent = timeText;
    }
    
    // Trigger animation
    requestAnimationFrame(() => {
        modal.classList.add('modal-show');
    });
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

function hideDraftModal() {
    const modal = document.getElementById('draftModal');
    modal.classList.remove('modal-show');
    modal.classList.add('modal-hide');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('modal-hide');
        document.body.style.overflow = '';
    }, 200);
}

function showExitModal() {
    const modal = document.getElementById('exitModal');
    modal.classList.remove('hidden', 'modal-hide');
    
    requestAnimationFrame(() => {
        modal.classList.add('modal-show');
    });
    
    document.body.style.overflow = 'hidden';
}

function hideExitModal() {
    const modal = document.getElementById('exitModal');
    modal.classList.remove('modal-show');
    modal.classList.add('modal-hide');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('modal-hide');
        document.body.style.overflow = '';
    }, 200);
}

// Close modal when clicking outside
document.getElementById('draftModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        // Optional: allow closing by clicking outside
        // hideDraftModal();
    }
});

document.getElementById('exitModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        // Optional: allow closing by clicking outside
        // hideExitModal();
    }
});

// ESC key to close
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (!document.getElementById('draftModal').classList.contains('hidden')) {
            // hideDraftModal();
        }
        if (!document.getElementById('exitModal').classList.contains('hidden')) {
            hideExitModal();
        }
    }
});

function saveFormData() {
    const formData = {};
    const form = document.getElementById('productForm');
    
    // Save standard inputs
    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        if (input.name && !input.name.startsWith('recipe_items')) {
            if (input.type === 'checkbox' || input.type === 'radio') {
                formData[input.name] = input.checked;
            } else if (input.type !== 'file') {
                formData[input.name] = input.value;
            }
        }
    });

    // Save recipe items
    const recipeItems = [];
    document.querySelectorAll('.recipe-item').forEach((item, index) => {
        const rmSelect = item.querySelector('.raw-material-select');
        const qtyInput = item.querySelector('.quantity-input');
        const noteInput = item.querySelector('input[name*="[notes]"]');
        
        if (rmSelect && qtyInput) {
            recipeItems.push({
                raw_material_id: $(rmSelect).val(),
                quantity: qtyInput.value,
                notes: noteInput ? noteInput.value : ''
            });
        }
    });
    formData['recipe_items'] = recipeItems;
    formData['current_step'] = currentStep;
    
    localStorage.setItem(STORAGE_KEY, JSON.stringify(formData));
    localStorage.setItem(DRAFT_TIMESTAMP_KEY, new Date().toISOString());
    formHasChanges = true;
}

function loadFormData() {
    const savedData = localStorage.getItem(STORAGE_KEY);
    if (!savedData) return;

    try {
        const formData = JSON.parse(savedData);
        const form = document.getElementById('productForm');

        // Restore standard inputs
        Object.keys(formData).forEach(key => {
            if (key === 'recipe_items' || key === 'current_step') return;

            const input = form.querySelector(`[name="${key}"]`);
            if (input) {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    input.checked = formData[key];
                    input.dispatchEvent(new Event('change')); 
                } else if (input.tagName === 'SELECT') {
                    if ($(input).hasClass('select2-hidden-accessible')) {
                        $(input).val(formData[key]).trigger('change');
                    } else {
                        input.value = formData[key];
                    }
                } else {
                    input.value = formData[key];
                }
            }
        });

        // Restore recipe items
        if (formData.recipe_items && Array.isArray(formData.recipe_items)) {
            // Clear existing items first (except first one)
            const existingItems = document.querySelectorAll('.recipe-item');
            for (let i = existingItems.length - 1; i > 0; i--) {
                existingItems[i].remove();
            }
            recipeItemIndex = 1;

            // First item (index 0)
            if (formData.recipe_items.length > 0) {
                const firstItemData = formData.recipe_items[0];
                const firstItem = document.querySelector('.recipe-item');
                if (firstItem) {
                    const select = $(firstItem).find('.raw-material-select');
                    const qty = firstItem.querySelector('.quantity-input');
                    const note = firstItem.querySelector('input[name*="[notes]"]');

                    if (select) select.val(firstItemData.raw_material_id).trigger('change');
                    if (qty) qty.value = firstItemData.quantity;
                    if (note) note.value = firstItemData.notes;
                }
            }

            // Additional items
            for (let i = 1; i < formData.recipe_items.length; i++) {
                addRecipeItem();
                
                const items = document.querySelectorAll('.recipe-item');
                const newItem = items[items.length - 1];
                const itemData = formData.recipe_items[i];

                const select = $(newItem).find('.raw-material-select');
                const qty = newItem.querySelector('.quantity-input');
                const note = newItem.querySelector('input[name*="[notes]"]');

                if (select) select.val(itemData.raw_material_id).trigger('change');
                if (qty) qty.value = itemData.quantity;
                if (note) note.value = itemData.notes;
            }
            
            calculateTotalMaterialCost();
        }
        
        // Restore step
        if (formData.current_step) {
            currentStep = formData.current_step;
            showStep(currentStep);
        }

        // Trigger calculations
        if (typeof updateHppSummary === 'function') updateHppSummary();
        if (typeof updateFinalPricing === 'function') updateFinalPricing();

        isDraftLoaded = true;
        formHasChanges = false; // Reset after loading

    } catch (e) {
        console.error('Error loading form data', e);
    }
}

function clearDraft() {
    localStorage.removeItem(STORAGE_KEY);
    localStorage.removeItem(DRAFT_TIMESTAMP_KEY);
    formHasChanges = false;
}

// ============================================
// EVENT LISTENERS FOR DRAFT
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2
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
    
    // Check for draft on page load
    checkForDraft();
    
    // Draft Modal Handlers
    document.getElementById('loadDraftBtn').addEventListener('click', function() {
        loadFormData();
        hideDraftModal();
    });

    document.getElementById('discardDraftBtn').addEventListener('click', function() {
        clearDraft();
        hideDraftModal();
    });

    // Exit Modal Handlers
    document.getElementById('saveDraftExitBtn').addEventListener('click', function() {
        saveFormData();
        hideExitModal();
        if (pendingNavigation) {
            isNavigatingAway = true;
            window.location.href = pendingNavigation;
        }
    });

    document.getElementById('discardExitBtn').addEventListener('click', function() {
        clearDraft();
        hideExitModal();
        if (pendingNavigation) {
            isNavigatingAway = true;
            window.location.href = pendingNavigation;
        }
    });

    document.getElementById('cancelExitBtn').addEventListener('click', function() {
        pendingNavigation = null;
        hideExitModal();
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
            saveFormData(); // Auto-save on step change
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
                saveFormData(); // Auto-save after removing item
            }
        }
    });

    // Generate Code Button
    document.getElementById('generateCode').addEventListener('click', function() {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Loading...';
        
        fetch('{{ route("products-hpp.generate-code") }}')
            .then(response => response.json())
            .then(data => {
                if (data.error) throw new Error(data.error);
                document.getElementById('productCode').value = data.code;
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-magic mr-1"></i>Buat Kode';
                saveFormData();
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
            .then(response => response.json())
            .then(data => {
                if (data.error) throw new Error(data.error);
                document.getElementById('productBarcode').value = data.barcode;
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-magic mr-1"></i>Buat Kode';
                saveFormData();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal generate barcode: ' + error.message);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-magic mr-1"></i>Buat Kode';
            });
    });

    // Auto-save on input with debounce
    const debouncedSave = debounce(saveFormData, 1000);
    const form = document.getElementById('productForm');
    form.addEventListener('input', debouncedSave);
    form.addEventListener('change', saveFormData);
    
    // Select2 events
    $(document).on('select2:select select2:unselect', '.select2, .select2-category, .select2-unit, .raw-material-select', saveFormData);

    // Form submit - clear draft
    form.addEventListener('submit', function() {
        isNavigatingAway = true;
        clearDraft();
    });

    // Initial calculation
    calculateItemCost(document.querySelector('.recipe-item'));
});

// ============================================
// NAVIGATION GUARD (Beforeunload & Click)
// ============================================

// Prevent leaving page with unsaved changes
window.addEventListener('beforeunload', function(e) {
    if (formHasChanges && !isNavigatingAway) {
        e.preventDefault();
        e.returnValue = 'Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman?';
        return e.returnValue;
    }
});

// Intercept all navigation links
document.addEventListener('click', function(e) {
    const link = e.target.closest('a');
    
    if (link && link.href && !link.target && formHasChanges && !isNavigatingAway) {
        // Skip if it's anchor link or javascript:
        if (link.href.startsWith('#') || link.href.startsWith('javascript:')) return;
        
        e.preventDefault();
        pendingNavigation = link.href;
        showExitModal();
    }
});

// Intercept browser back/forward buttons
window.addEventListener('popstate', function(e) {
    if (formHasChanges && !isNavigatingAway) {
        e.preventDefault();
        history.pushState(null, '', window.location.href);
        pendingNavigation = document.referrer || '{{ route("products-hpp.index") }}';
        showExitModal();
    }
});

// Push initial state to enable popstate detection
history.pushState(null, '', window.location.href);

// Toggle Sales Target
document.getElementById('enableSalesTarget').addEventListener('change', function() {
    const content = document.getElementById('salesTargetContent');
    if (this.checked) {
        content.classList.remove('hidden');
        loadHistoricalData();
    } else {
        content.classList.add('hidden');
    }
    saveFormData();
});

// ... (Semua fungsi lainnya tetap sama seperti sebelumnya)
// showStep, validateStep, addRecipeItem, updateRemoveButtons, calculateItemCost, 
// calculateTotalMaterialCost, updateHppSummary, updateFinalPricing, calculateMargin,
// loadHistoricalData, dll.

// Saya skip fungsi-fungsi yang sama untuk menghemat space
// Pastikan semua fungsi dari kode asli tetap ada di sini

function showStep(step) {
    document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
    document.getElementById('step' + step).classList.remove('hidden');
    
    document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
        const stepNum = index + 1;
        const circle = indicator.querySelector('div');
        const icon = circle ? circle.querySelector('i') : null;
        
        if (!circle) return;
        
        if (stepNum < step) {
            circle.className = 'w-10 h-10 bg-cuan-green rounded-full flex items-center justify-center mx-auto mb-2 shadow-md';
            if (icon) icon.className = 'fas fa-check text-white';
        } else if (stepNum === step) {
            circle.className = 'w-10 h-10 bg-cuan-green rounded-full flex items-center justify-center mx-auto mb-2 shadow-md ring-4 ring-green-200';
        } else {
            circle.className = 'w-10 h-10 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center mx-auto mb-2';
            if (icon) icon.className = icon.className.replace('text-white', 'text-gray-400');
        }
    });
    
    const progressPercent = ((step - 1) / (totalSteps - 1)) * 100;
    document.getElementById('progressLine').style.width = progressPercent + '%';
    
    document.getElementById('prevBtn').style.display = step > 1 ? 'flex' : 'none';
    document.getElementById('nextBtn').style.display = step < totalSteps ? 'flex' : 'none';
    document.getElementById('submitBtn').style.display = step === totalSteps ? 'flex' : 'none';
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function validateStep(step) {
    const currentStepEl = document.getElementById('step' + step);
    if (!currentStepEl) return false;
    
    const requiredInputs = currentStepEl.querySelectorAll('[required]');
    
    for (let input of requiredInputs) {
        if (!input.value || input.value.trim() === '') {
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
    
    if (marginPercent >= 30) {
        marginEl.className = 'text-2xl font-bold text-green-600';
    } else if (marginPercent >= 15) {
        marginEl.className = 'text-2xl font-bold text-yellow-600';
    } else {
        marginEl.className = 'text-2xl font-bold text-red-600';
    }
}

function loadHistoricalData() {
    document.getElementById('historicalDataLoading').classList.remove('hidden');
    document.getElementById('historicalDataContent').classList.add('hidden');
    document.getElementById('noHistoricalData').classList.add('hidden');

    fetch('/products-hpp/sales-analytics?product_id=new')
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.total_sold_30days > 0) {
                historicalSalesData = data;
                displayHistoricalData(data);
            } else {
                showNoHistoricalData();
            }
        })
        .catch(error => {
            console.error('Error loading historical data:', error);
            showNoHistoricalData();
        });
}

function showNoHistoricalData() {
    document.getElementById('historicalDataLoading').classList.add('hidden');
    document.getElementById('noHistoricalData').classList.remove('hidden');
}

function displayHistoricalData(data) {
    document.getElementById('historicalDataLoading').classList.add('hidden');
    document.getElementById('historicalDataContent').classList.remove('hidden');

    document.getElementById('totalSold30Days').textContent = data.total_sold_30days + ' pcs';
    document.getElementById('avgDailySales').textContent = data.avg_daily_sales.toFixed(1) + ' pcs';
    document.getElementById('bestSalesDay').textContent = data.best_day;

    renderWeeklyTrendChart(data.weekly_trend);
    renderDailyPatternChart(data.daily_pattern);
}

document.getElementById('monthlyTargetRevenue').addEventListener('input', calculateSalesTarget);

function calculateSalesTarget() {
    const targetRevenue = parseFloat(document.getElementById('monthlyTargetRevenue').value || 0);
    const sellingPrice = parseFloat(document.getElementById('sellingPriceInput').value || 0);
    const hppPerUnit = window.hppPerUnitValue || 0;

    if (targetRevenue > 0 && sellingPrice > 0) {
        const monthlySalesTarget = Math.ceil(targetRevenue / sellingPrice);
        const dailySalesTarget = Math.ceil(monthlySalesTarget / 30);
        const dailyRevenueTarget = dailySalesTarget * sellingPrice;
        const profitPerUnit = sellingPrice - hppPerUnit;
        const monthlyProfitTarget = monthlySalesTarget * profitPerUnit;

        document.getElementById('monthlySalesTarget').textContent = monthlySalesTarget.toLocaleString('id-ID') + ' pcs';
        document.getElementById('dailySalesTarget').textContent = dailySalesTarget.toLocaleString('id-ID') + ' pcs';
        document.getElementById('dailyRevenueTarget').textContent = 'Rp ' + dailyRevenueTarget.toLocaleString('id-ID');
        document.getElementById('monthlyProfitTarget').textContent = 'Rp ' + monthlyProfitTarget.toLocaleString('id-ID');

        document.getElementById('hiddenMonthlySalesTarget').value = monthlySalesTarget;
        document.getElementById('hiddenDailySalesTarget').value = dailySalesTarget;
        document.getElementById('hiddenDailyRevenueTarget').value = dailyRevenueTarget;

        if (historicalSalesData) {
            const currentDailySales = historicalSalesData.avg_daily_sales;
            const achievementPercent = (currentDailySales / dailySalesTarget) * 100;
            
            document.getElementById('achievementPercent').textContent = achievementPercent.toFixed(1) + '%';
            document.getElementById('achievementBar').style.width = Math.min(achievementPercent, 100) + '%';
            
            if (achievementPercent >= 100) {
                document.getElementById('achievementNote').textContent = '✨ Target sudah tercapai dengan performa saat ini!';
            } else {
                const gap = dailySalesTarget - currentDailySales;
                document.getElementById('achievementNote').textContent = `Perlu peningkatan ${gap.toFixed(1)} pcs/hari untuk mencapai target`;
            }
        }

        renderProjectionChart(dailySalesTarget);

        document.getElementById('targetCalculationResult').classList.remove('hidden');
    } else if (targetRevenue > 0 && sellingPrice <= 0) {
        alert('Mohon isi "Harga Jual" di Step 5 terlebih dahulu untuk menghitung target penjualan.');
        document.getElementById('targetCalculationResult').classList.add('hidden');
    } else {
        document.getElementById('targetCalculationResult').classList.add('hidden');
    }
}

function renderWeeklyTrendChart(weeklyData) {
    const ctx = document.getElementById('weeklyTrendChart').getContext('2d');
    
    if (weeklyTrendChart) weeklyTrendChart.destroy();
    
    weeklyTrendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: weeklyData.map(w => w.week),
            datasets: [{
                label: 'Penjualan (pcs)',
                data: weeklyData.map(w => w.sales),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (context) => `${context.parsed.y} pcs`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });
}

function renderDailyPatternChart(dailyPattern) {
    const ctx = document.getElementById('dailyPatternChart').getContext('2d');
    
    if (dailyPatternChart) dailyPatternChart.destroy();
    
    const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    const indonesianDays = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
    
    dailyPatternChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: indonesianDays,
            datasets: [{
                label: 'Penjualan',
                data: days.map(day => dailyPattern[day] || 0),
                backgroundColor: 'rgba(147, 51, 234, 0.7)',
                borderColor: 'rgb(147, 51, 234)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });

    document.getElementById('hiddenSalesPattern').value = JSON.stringify(dailyPattern);
}

function renderProjectionChart(dailyTarget) {
    const ctx = document.getElementById('projectionChart');
    if (!ctx) return;
    
    const context = ctx.getContext('2d');
    
    if (projectionChart) projectionChart.destroy();

    const currentAvg = historicalSalesData ? historicalSalesData.avg_daily_sales : 0;
    
    const optimistic = currentAvg > 0 ? currentAvg * 1.2 : dailyTarget * 1.2;
    const realistic = currentAvg > 0 ? currentAvg : dailyTarget;
    const pessimistic = currentAvg > 0 ? currentAvg * 0.8 : dailyTarget * 0.8;

    const formatDays = (val) => {
        if (!isFinite(val) || val <= 0) return 'N/A';
        if (val > 180) return '> 6 bulan';
        if (val > 30) return Math.ceil(val / 30) + ' bulan';
        return Math.ceil(val) + ' hari';
    };

    const monthlyTargetQty = dailyTarget * 30;
    const daysToTargetOptimistic = optimistic > 0 ? Math.min(Math.ceil(monthlyTargetQty / optimistic), 180) : 180;
    const daysToTargetRealistic = realistic > 0 ? Math.min(Math.ceil(monthlyTargetQty / realistic), 180) : 180;
    const daysToTargetPessimistic = pessimistic > 0 ? Math.min(Math.ceil(monthlyTargetQty / pessimistic), 180) : 180;

    document.getElementById('optimisticScenario').textContent = formatDays(daysToTargetOptimistic);
    document.getElementById('realisticScenario').textContent = formatDays(daysToTargetRealistic);
    document.getElementById('pessimisticScenario').textContent = formatDays(daysToTargetPessimistic);

    const labels = [];
    const targetData = [];
    const optimisticData = [];
    const realisticData = [];
    const pessimisticData = [];

    const maxMonths = 6;
    const monthlyTarget = dailyTarget * 30;
    
    for (let month = 1; month <= maxMonths; month++) {
        labels.push('Bulan ' + month);
        targetData.push(monthlyTarget * month);
        optimisticData.push(optimistic * 30 * month);
        realisticData.push(realistic * 30 * month);
        pessimisticData.push(pessimistic * 30 * month);
    }

    projectionChart = new Chart(context, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Target',
                    data: targetData,
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    borderWidth: 3,
                    borderDash: [5, 5],
                    tension: 0
                },
                {
                    label: 'Optimis (+20%)',
                    data: optimisticData,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.05)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Realistis',
                    data: realisticData,
                    borderColor: 'rgb(107, 114, 128)',
                    backgroundColor: 'rgba(107, 114, 128, 0.05)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Pesimis (-20%)',
                    data: pessimisticData,
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.05)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + Math.round(context.parsed.y).toLocaleString('id-ID') + ' pcs';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return Math.round(value).toLocaleString('id-ID') + ' pcs';
                        }
                    }
                }
            }
        }
    });
}

</script>
@endpush

@endsection
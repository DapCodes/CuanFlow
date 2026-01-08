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
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- VALIDATION ERROR --}}
        @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 flex items-start gap-3 text-sm" role="alert">
                <i class="fas fa-exclamation-circle mt-0.5 text-red-500"></i>
                <div class="flex-1">
                    <p class="font-semibold text-red-800 mb-1">Ada data yang belum benar.</p>
                    <ul class="list-disc list-inside text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <p class="mt-2 text-xs text-red-600">
                        Periksa kembali isian yang bertanda <span class="font-semibold">*</span>.
                    </p>
                </div>
            </div>
        @endif

        {{-- HEADER HALAMAN (seragam dengan index) --}}
        <section
            class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-green-50 text-cuan-green border border-green-100">
                        <i class="fas fa-utensils text-sm"></i>
                    </span>
                    <span>Tambah Produk & Resep</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Isi data produk langkah demi langkah. Tenang, semua data masih bisa diubah setelah disimpan.
                </p>
            </div>
            <div class="flex flex-col items-start md:items-end gap-1 text-sm">
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-50 text-green-700 border border-green-100">
                    <i class="fas fa-list-ol mr-2 text-xs"></i>
                    Form bertahap: 6 langkah
                </span>
                <p class="text-xs text-gray-500">
                    Form ini otomatis menyimpan <span class="font-semibold">draft</span> supaya data tidak mudah hilang.
                </p>
            </div>
        </section>

        {{-- CARD FORM BERTAHAP --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

            {{-- Progress Steps (tetap pakai ID & class lama untuk JS) --}}
            <div class="bg-gradient-to-r from-green-50 to-blue-50 px-2 md:px-6 py-4 md:py-5 border-b border-gray-200">
                <div class="flex justify-between items-center relative max-w-3xl mx-auto">
                    {{-- Garis Progress --}}
                    <div class="absolute top-4 md:top-5 left-0 right-0 h-0.5 bg-gray-200" style="z-index: 0;">
                        <div id="progressLine" class="h-full bg-cuan-green transition-all duration-300" style="width: 0%;"></div>
                    </div>

                    {{-- Langkah 1 --}}
                    <div class="flex-1 text-center step-indicator active relative z-10 cursor-default" data-step="1" title="Info Produk">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-cuan-green rounded-full flex items-center justify-center mx-auto mb-2 shadow-md transition-all duration-300">
                            <i class="fas fa-info-circle text-white text-xs md:text-sm" data-default-icon="fas fa-info-circle"></i>
                        </div>
                        <p class="hidden md:block text-[11px] font-medium text-gray-900 transition-colors duration-300">Info Produk</p>
                    </div>

                    {{-- Langkah 2 --}}
                    <div class="flex-1 text-center step-indicator relative z-10 cursor-default" data-step="2" title="Resep">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center mx-auto mb-2 transition-all duration-300">
                            <i class="fas fa-book-open text-gray-400 text-xs md:text-sm" data-default-icon="fas fa-book-open"></i>
                        </div>
                        <p class="hidden md:block text-[11px] font-medium text-gray-600 transition-colors duration-300">Resep</p>
                    </div>

                    {{-- Langkah 3 --}}
                    <div class="flex-1 text-center step-indicator relative z-10 cursor-default" data-step="3" title="Bahan Baku">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center mx-auto mb-2 transition-all duration-300">
                            <i class="fas fa-shopping-basket text-gray-400 text-xs md:text-sm" data-default-icon="fas fa-shopping-basket"></i>
                        </div>
                        <p class="hidden md:block text-[11px] font-medium text-gray-600 transition-colors duration-300">Bahan Baku</p>
                    </div>

                    {{-- Langkah 4 --}}
                    <div class="flex-1 text-center step-indicator relative z-10 cursor-default" data-step="4" title="Biaya Lain">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center mx-auto mb-2 transition-all duration-300">
                            <i class="fas fa-coins text-gray-400 text-xs md:text-sm" data-default-icon="fas fa-coins"></i>
                        </div>
                        <p class="hidden md:block text-[11px] font-medium text-gray-600 transition-colors duration-300">Biaya Lain</p>
                    </div>

                    {{-- Langkah 5 --}}
                    <div class="flex-1 text-center step-indicator relative z-10 cursor-default" data-step="5" title="Harga & Stok">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center mx-auto mb-2 transition-all duration-300">
                            <i class="fas fa-tags text-gray-400 text-xs md:text-sm" data-default-icon="fas fa-tags"></i>
                        </div>
                        <p class="hidden md:block text-[11px] font-medium text-gray-600 transition-colors duration-300">Harga & Stok</p>
                    </div>

                    {{-- Langkah 6 --}}
                    <div class="flex-1 text-center step-indicator relative z-10 cursor-default" data-step="6" title="Target Jual">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center mx-auto mb-2 transition-all duration-300">
                            <i class="fas fa-chart-line text-gray-400 text-xs md:text-sm" data-default-icon="fas fa-chart-line"></i>
                        </div>
                        <p class="hidden md:block text-[11px] font-medium text-gray-600 transition-colors duration-300">Target Jual</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('products-hpp.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
                @csrf

                {{-- STEP 1: INFO DASAR --}}
                <div class="step-content px-4 md:px-6 py-6" id="step1">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <span>Info Produk</span>
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Masukkan nama, kode, dan keterangan singkat produk yang ingin dijual.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Kode Produk --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Kode Produk <span class="text-red-500">*</span>
                            </label>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <input type="text" name="code" id="productCode" value="{{ old('code') }}"
                                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                       placeholder="Contoh: PRD001" required>
                                <button type="button" id="generateCode"
                                        class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors whitespace-nowrap text-sm font-semibold">
                                    <i class="fas fa-magic mr-2"></i>
                                    Buat Otomatis
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Kode bebas, yang penting mudah dibaca tim Anda.
                            </p>
                        </div>

                        {{-- Nama Produk --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Produk <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                   placeholder="Contoh: Donat Cokelat Isi" required>
                        </div>

                        {{-- Barcode --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Barcode
                            </label>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <input type="text" name="barcode" id="productBarcode" value="{{ old('barcode') }}"
                                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                       placeholder="Opsional">
                                <button type="button" id="generateBarcode"
                                        class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors whitespace-nowrap text-sm font-semibold">
                                    <i class="fas fa-magic mr-2"></i>
                                    Buat Otomatis
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Jika belum pakai barcode, bisa dikosongkan dulu.
                            </p>
                        </div>

                        {{-- Kategori --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
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
                            <p class="mt-1 text-xs text-gray-500">
                                Contoh: Minuman, Snack, Kue, Lauk, dll.
                            </p>
                        </div>

                        {{-- Satuan --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
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
                            <p class="mt-1 text-xs text-gray-500">
                                Contoh: pcs, cup, box, bungkus, dll.
                            </p>
                        </div>

                        {{-- Foto Produk --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Foto Produk
                            </label>
                            <input type="file" name="image" id="imageInput" accept="image/*"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-xs text-gray-500 mt-1">Maksimal 2MB (JPG, JPEG, PNG). Foto membantu kasir lebih yakin.</p>

                            {{-- Preview Foto --}}
                            <div id="imagePreview" class="mt-3 hidden">
                                <div class="relative inline-block">
                                    <img id="previewImg" src="" alt="Preview"
                                         class="w-32 h-32 object-cover rounded-lg border-2 border-gray-300 shadow-sm">
                                    <button type="button" id="removeImage"
                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition-colors">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Deskripsi
                            </label>
                            <textarea name="description" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                      placeholder="Tuliskan deskripsi singkat produk (rasa, ukuran, keunggulan, dll)">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- STEP 2: RESEP --}}
                <div class="step-content px-4 md:px-6 py-6 hidden" id="step2">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <span>Resep Produk</span>
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Tuliskan nama resep, hasil produksi, dan cara membuat produk ini.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Resep <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="recipe_name" value="{{ old('recipe_name') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                   placeholder="Contoh: Resep Donat Original" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Jumlah Jadi <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="output_quantity" value="{{ old('output_quantity', 1) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                   placeholder="1" required>
                            <p class="text-xs text-gray-500 mt-1">
                                Contoh: 1 resep menghasilkan 20 pcs, isi 20.
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Perkiraan Waktu (menit)
                            </label>
                            <input type="number" name="estimated_time_minutes" value="{{ old('estimated_time_minutes') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                   placeholder="Contoh: 30">
                            <p class="text-xs text-gray-500 mt-1">
                                Boleh dikosongkan jika belum tahu pastinya.
                            </p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Cara Membuat (Langkah-langkah)
                            </label>
                            <textarea name="instructions" rows="6"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                      placeholder="1. Siapkan bahan...&#10;2. Campur dan aduk...&#10;3. Goreng / panggang...&#10;4. Sajikan...">{{ old('instructions') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- STEP 3: BAHAN BAKU --}}
                <div class="step-content px-4 md:px-6 py-6 hidden" id="step3">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <span>Bahan Baku</span>
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Tambahkan bahan baku yang dipakai untuk sekali resep. Bisa isi manual atau pakai bantuan AI.
                        </p>

                        {{-- Card AI --}}
                        <div class="mt-4 bg-gradient-to-r from-purple-50 to-blue-50 border-2 border-purple-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center">
                                        <i class="fas fa-magic text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 mb-1">Buat Resep dengan AI</h4>
                                    <p class="text-sm text-gray-600 mb-3">
                                        Sistem akan membantu menyusun daftar bahan dan takaran otomatis,
                                        berdasarkan <span class="font-semibold">nama produk</span> dan
                                        <span class="font-semibold">bahan yang sudah terdaftar</span> di outlet Anda.
                                    </p>
                                    <button type="button" id="generateRecipeAI"
                                            class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium text-sm">
                                        <i class="fas fa-sparkles mr-2"></i>
                                        Buat Resep Otomatis
                                    </button>
                                    <p class="mt-2 text-xs text-gray-500">
                                        Hasil AI tetap bisa Anda ubah lagi sesuai bahan di dapur.
                                    </p>
                                </div>
                            </div>
                        </div>
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
                                    <input type="text"
                                           class="unit-display w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 text-gray-600"
                                           readonly placeholder="-">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Biaya</label>
                                    <input type="text"
                                           class="cost-display w-full px-4 py-3 border border-gray-300 rounded-lg bg-blue-50 font-semibold text-blue-700"
                                           readonly value="Rp 0">
                                </div>
                                <div class="md:col-span-1 flex items-end">
                                    <button type="button"
                                            class="remove-item w-full px-4 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors"
                                            style="display: none;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Catatan
                                </label>
                                <input type="text" name="recipe_items[0][notes]"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                       placeholder="Catatan tambahan (opsional), misalnya merk tertentu, kualitas, dll">
                            </div>
                        </div>
                    </div>

                    <button type="button" id="addRecipeItem"
                            class="mt-4 w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 bg-cuan-green text-white rounded-lg hover:bg-cuan-olive transition-colors text-sm font-medium">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Bahan
                    </button>

                    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-5">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                            <span class="text-sm font-semibold text-gray-700 flex items-center">
                                Total biaya bahan baku untuk 1 resep:
                            </span>
                            <span id="totalMaterialCost" class="text-xl font-bold text-cuan-green">Rp 0</span>
                        </div>
                    </div>
                </div>

                {{-- STEP 4: BIAYA TAMBAHAN --}}
                <div class="step-content px-4 md:px-6 py-6 hidden" id="step4">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <span>Biaya Lain-lain</span>
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Masukkan biaya di luar bahan baku, misalnya listrik, gas, kemasan, dan tenaga kerja.
                        </p>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-info-circle text-cuan-green mt-0.5"></i>
                            <p class="text-sm text-blue-800">
                                Biaya tambahan ini membantu HPP lebih realistis. Kalau bingung, bisa isi perkiraan
                                per resep atau isi 0 dulu, nanti bisa disesuaikan lagi.
                            </p>
                        </div>
                    </div>

                    <div class="max-w-md">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Total Biaya Lain per Resep (Rp)
                        </label>
                        <input type="number" step="0.01" name="additional_cost" id="additionalCostInput"
                               value="{{ old('additional_cost', 0) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                               placeholder="0">
                        <p class="text-xs text-gray-500 mt-1">
                            Contoh: listrik, gas, kemasan, sewa dapur, dan lain-lain. Isi 0 jika belum mau dihitung.
                        </p>
                    </div>

                    <div class="mt-8 bg-gradient-to-br from-green-50 to-blue-50 border border-green-200 rounded-lg p-6">
                        <h4 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span>Ringkasan HPP (Harga Pokok Produksi)</span>
                        </h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm text-gray-700">Total biaya bahan baku:</span>
                                <span id="summaryMaterialCost" class="font-semibold text-gray-900">Rp 0</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm text-gray-700">Biaya lain-lain:</span>
                                <span id="summaryAdditionalCost" class="font-semibold text-gray-900">Rp 0</span>
                            </div>
                            <div class="border-t border-gray-300 pt-3 flex justify-between items-center">
                                <span class="font-bold text-gray-900">Total HPP per resep:</span>
                                <span id="summaryTotalHpp" class="text-xl font-bold text-green-600">Rp 0</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm text-gray-700">Hasil resep (jumlah jadi):</span>
                                <span id="summaryOutputQty" class="font-semibold text-gray-900">1</span>
                            </div>
                            <div class="bg-white rounded-lg p-4 flex justify-between items-center shadow-sm">
                                <span class="font-bold text-gray-900">HPP per 1 {{ $units->first()->name ?? 'unit' }}:</span>
                                <span id="summaryHppPerUnit" class="text-2xl font-bold text-cuan-green">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- STEP 5: HARGA & STOK --}}
                <div class="step-content px-4 md:px-6 py-6 hidden" id="step5">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <span>Harga Jual & Stok</span>
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Atur harga jual, harga reseller (jika ada), dan batas stok minimum.
                        </p>
                    </div>

                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                            <span class="text-sm font-semibold text-gray-700 flex items-center">
                                HPP per unit (perkiraan modal per pcs):
                            </span>
                            <span id="finalHppPerUnit" class="text-xl font-bold text-green-600">Rp 0</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Harga Jual <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="selling_price" id="sellingPriceInput"
                                   value="{{ old('selling_price') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Contoh: 10000" required>
                            <p class="mt-1 text-xs text-gray-500">
                                Sesuaikan dengan pasar dan margin yang diinginkan.
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Harga Reseller
                            </label>
                            <input type="number" step="0.01" name="reseller_price"
                                   value="{{ old('reseller_price') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Opsional, contoh: 9000">
                            <p class="text-xs text-gray-500 mt-1">Harga khusus untuk agen/reseller (boleh kosong).</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Harga Promo
                            </label>
                            <input type="number" step="0.01" name="promo_price"
                                   value="{{ old('promo_price') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Opsional, contoh: 8000">
                            <p class="text-xs text-gray-500 mt-1">Harga khusus saat promo (boleh kosong).</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Minimum Stok
                            </label>
                            <input type="number" step="0.01" name="min_stock"
                                   value="{{ old('min_stock', 0) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Contoh: 10">
                            <p class="text-xs text-gray-500 mt-1">
                                Sistem akan memberi tanda jika stok turun di bawah angka ini.
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Masa Simpan (hari)
                            </label>
                            <input type="number" name="shelf_life_days"
                                   value="{{ old('shelf_life_days') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Contoh: 3">
                            <p class="text-xs text-gray-500 mt-1">
                                Berapa lama produk aman dijual. Cocok untuk makanan/minuman segar.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <span>Perkiraan Laba per Unit</span>
                        </h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">HPP per unit (modal):</span>
                                <span id="marginHpp" class="font-semibold text-gray-900">Rp 0</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Harga jual:</span>
                                <span id="marginSellingPrice" class="font-semibold text-gray-900">Rp 0</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Laba per unit:</span>
                                <span id="marginProfit" class="font-semibold text-gray-900">Rp 0</span>
                            </div>
                            <div class="bg-white rounded-lg p-4 flex justify-between items-center border-2 border-blue-200">
                                <span class="text-lg font-bold text-gray-800">
                                    Margin (persentase laba):
                                </span>
                                <span id="marginPercent" class="text-2xl font-bold text-green-600">0%</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- STEP 6: TARGET PENJUALAN --}}
                <div class="step-content px-4 md:px-6 py-6 hidden" id="step6">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <span>Target Penjualan</span>
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Atur target omzet per bulan dan lihat kira-kira harus jual berapa pcs per hari.
                        </p>
                    </div>

                    {{-- Toggle --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-5 mb-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="enable_sales_target" value="1" id="enableSalesTarget"
                                   class="w-5 h-5 text-cuan-green rounded focus:ring-2 focus:ring-green-500">
                            <span class="ml-3 text-sm font-semibold text-gray-800">
                                Aktifkan target penjualan untuk produk ini
                            </span>
                        </label>
                        <p class="text-xs text-gray-600 ml-8 mt-1">
                            Fitur ini membantu melihat apakah penjualan sudah sejalan dengan target omset.
                        </p>
                    </div>

                    <div id="salesTargetContent" class="hidden">
                        {{-- Analisis historis --}}
                        <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-xl p-6 mb-6 border border-purple-200">
                            <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-history text-purple-600"></i>
                                <span>Ringkasan Penjualan 30 Hari Terakhir</span>
                            </h4>

                            <div id="historicalDataLoading" class="text-center py-8">
                                <i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-2"></i>
                                <p class="text-gray-600 text-sm">Sedang mengambil data penjualan...</p>
                            </div>

                            <div id="historicalDataContent" class="hidden">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <p class="text-xs text-gray-500 mb-1">Total terjual (30 hari)</p>
                                        <p class="text-2xl font-bold text-purple-600" id="totalSold30Days">0 pcs</p>
                                    </div>
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <p class="text-xs text-gray-500 mb-1">Rata-rata per hari</p>
                                        <p class="text-2xl font-bold text-blue-600" id="avgDailySales">0 pcs</p>
                                    </div>
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <p class="text-xs text-gray-500 mb-1">Hari penjualan terbaik</p>
                                        <p class="text-2xl font-bold text-green-600" id="bestSalesDay">-</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div class="bg-white rounded-lg p-5 shadow-sm">
                                        <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                            <i class="fas fa-chart-bar text-blue-500"></i>
                                            <span>Tren penjualan per minggu</span>
                                        </h5>
                                        <canvas id="weeklyTrendChart" height="250"></canvas>
                                    </div>

                                    <div class="bg-white rounded-lg p-5 shadow-sm">
                                        <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                            <i class="fas fa-calendar-week text-purple-500"></i>
                                            <span>Pola penjualan per hari</span>
                                        </h5>
                                        <canvas id="dailyPatternChart" height="250"></canvas>
                                    </div>
                                </div>
                            </div>

                            <div id="noHistoricalData" class="hidden text-center py-8">
                                <i class="fas fa-inbox text-5xl text-gray-300 mb-3"></i>
                                <p class="text-gray-600 font-medium">Belum ada data penjualan untuk produk ini.</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    Data akan muncul setelah produk ini mulai terjual di sistem.
                                </p>
                            </div>
                        </div>

                        {{-- Input target --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-money-bill-wave text-gray-400 mr-1"></i>
                                    Target omzet per bulan (Rp) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.01" name="monthly_target_revenue" id="monthlyTargetRevenue"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                       placeholder="Contoh: 5000000">
                                <p class="text-xs text-gray-500 mt-1">
                                    Contoh: ingin omzet Rp 5.000.000/bulan dari produk ini.
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-calendar-alt text-gray-400 mr-1"></i>
                                    Tanggal mulai hitung target
                                </label>
                                <input type="date" name="target_start_date" id="targetStartDate" value="{{ date('Y-m-d') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <p class="text-xs text-gray-500 mt-1">
                                    Biasanya diisi awal bulan berjalan.
                                </p>
                            </div>
                        </div>

                        {{-- Hasil perhitungan --}}
                        <div id="targetCalculationResult" class="hidden">
                            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-6 border-2 border-green-300 mb-6">
                                <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                                    <i class="fas fa-calculator text-green-600"></i>
                                    <span>Perhitungan Target Penjualan</span>
                                </h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <p class="text-xs text-gray-600 mb-1">Target jual per bulan</p>
                                        <p class="text-2xl font-bold text-green-600" id="monthlySalesTarget">0 pcs</p>
                                    </div>
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <p class="text-xs text-gray-600 mb-1">Target jual per hari</p>
                                        <p class="text-2xl font-bold text-blue-600" id="dailySalesTarget">0 pcs</p>
                                    </div>
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <p class="text-xs text-gray-600 mb-1">Target omzet harian</p>
                                        <p class="text-xl font-bold text-purple-600" id="dailyRevenueTarget">Rp 0</p>
                                    </div>
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <p class="text-xs text-gray-600 mb-1">Target laba per bulan</p>
                                        <p class="text-xl font-bold text-emerald-600" id="monthlyProfitTarget">Rp 0</p>
                                    </div>
                                </div>

                                <div class="bg-white rounded-lg p-5 shadow-sm">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-semibold text-gray-700">
                                            Perbandingan dengan penjualan saat ini
                                        </span>
                                        <span id="achievementPercent" class="text-lg font-bold text-gray-800">0%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                        <div id="achievementBar"
                                             class="h-full bg-gradient-to-r from-green-400 to-green-600 transition-all duration-500"
                                             style="width: 0%"></div>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-2" id="achievementNote">
                                        Berdasarkan rata-rata penjualan 30 hari terakhir.
                                    </p>
                                </div>
                            </div>

                            {{-- Grafik proyeksi --}}
                            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200">
                                <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                                    <i class="fas fa-chart-area text-indigo-600"></i>
                                    <span>Proyeksi Pencapaian Target</span>
                                </h4>
                                <div style="position: relative; height: 250px; width: 100%;">
                                    <canvas id="projectionChart"></canvas>
                                </div>

                                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                                        <p class="text-xs text-gray-600 mb-1">Skenario optimis</p>
                                        <p class="text-xl font-bold text-blue-600" id="optimisticScenario">0 hari</p>
                                        <p class="text-xs text-gray-500">Penjualan naik ±20%</p>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                                        <p class="text-xs text-gray-600 mb-1">Skenario realistis</p>
                                        <p class="text-xl font-bold text-gray-600" id="realisticScenario">0 hari</p>
                                        <p class="text-xs text-gray-500">Sesuai penjualan sekarang</p>
                                    </div>
                                    <div class="bg-red-50 rounded-lg p-4 text-center">
                                        <p class="text-xs text-gray-600 mb-1">Skenario pesimis</p>
                                        <p class="text-xl font-bold text-red-600" id="pessimisticScenario">0 hari</p>
                                        <p class="text-xs text-gray-500">Penjualan turun ±20%</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Hidden inputs --}}
                            <input type="hidden" name="daily_sales_target" id="hiddenDailySalesTarget">
                            <input type="hidden" name="monthly_sales_target" id="hiddenMonthlySalesTarget">
                            <input type="hidden" name="daily_revenue_target" id="hiddenDailyRevenueTarget">
                            <input type="hidden" name="sales_pattern" id="hiddenSalesPattern">
                        </div>
                    </div>
                </div>

                {{-- NAV BUTTONS --}}
                <div class="px-4 md:px-6 py-5 bg-gray-50 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3">
                        <button type="button" id="prevBtn"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-semibold"
                                style="display: none;">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Sebelumnya
                        </button>

                        <div class="hidden sm:block"></div>

                        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                            <button type="button" id="nextBtn"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-cuan-green text-white rounded-lg hover:bg-cuan-olive transition-all font-semibold shadow-md hover:shadow-lg order-2 sm:order-1">
                                Lanjut
                                <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                            <button type="submit" id="submitBtn"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-cuan-green text-white rounded-lg hover:bg-green-700 transition-all font-semibold shadow-md hover:shadow-lg order-1 sm:order-2"
                                    style="display: none;">
                                <i class="fas fa-save mr-2"></i>
                                Simpan Produk
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </section>
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
const USER_ID = {{ auth()->id() }};
const OUTLET_ID = {{ auth()->user()->outlet_id }};
const STORAGE_KEY = `cuanflow_product_create_form_u${USER_ID}_o${OUTLET_ID}_v1`;
const DRAFT_TIMESTAMP_KEY = `cuanflow_product_draft_timestamp_u${USER_ID}_o${OUTLET_ID}`;
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

        updateAvailableRawMaterials();

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

function updateAvailableRawMaterials() {
    // Ambil semua ID yang sudah dipilih
    const selectedIds = [];
    document.querySelectorAll('.raw-material-select').forEach(select => {
        const val = $(select).val();
        if (val) selectedIds.push(val);
    });

    // Update setiap select2
    document.querySelectorAll('.raw-material-select').forEach(select => {
        const currentVal = $(select).val();
        
        // Destroy dan reinit select2
        $(select).select2('destroy');
        
        // Disable options yang sudah dipilih di select lain
        $(select).find('option').each(function() {
            const optionVal = $(this).val();
            if (optionVal && selectedIds.includes(optionVal) && optionVal !== currentVal) {
                $(this).prop('disabled', true);
            } else {
                $(this).prop('disabled', false);
            }
        });
        
        // Reinit select2
        $(select).select2({
            theme: 'default',
            width: '100%',
            placeholder: '- Pilih Bahan -'
        });
    });
}

document.getElementById('generateRecipeAI').addEventListener('click', async function() {
    const btn = this;
    
    const productNameInput = document.querySelector('input[name="name"]');
    const outputQtyInput = document.querySelector('input[name="output_quantity"]');
    
    if (!productNameInput || !outputQtyInput) {
        alert('ERROR: Input tidak ditemukan');
        return;
    }
    
    const productName = productNameInput.value;
    const outputQty = outputQtyInput.value;

    if (!productName || !outputQty) {
        alert('Mohon isi Nama Produk (Step 1) dan Jumlah Output (Step 2) terlebih dahulu');
        return;
    }

    // Konfirmasi jika sudah ada recipe items
    const existingItems = document.querySelectorAll('.recipe-item').length;
    if (existingItems > 0) {
        const confirm = window.confirm(
            'Anda sudah memiliki ' + existingItems + ' bahan baku. ' +
            'Generate AI akan mengganti semua bahan yang ada. Lanjutkan?'
        );
        if (!confirm) return;
    }

    // Loading state
    btn.disabled = true;
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating...';

    try {
        const response = await fetch('{{ route("products-hpp.generate-recipe-ai") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({
                product_name: productName,
                output_quantity: parseFloat(outputQty),
            }),
        });

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.error || 'Unknown error');
        }

        const recipe = data.recipe;
        
        if (!recipe.ingredients || !Array.isArray(recipe.ingredients)) {
            throw new Error('Invalid recipe structure - missing ingredients array');
        }

        // ✅ FIX: Clear ALL existing items first
        const container = document.getElementById('recipeItemsContainer');
        if (!container) {
            throw new Error('Recipe items container not found');
        }
        
        container.innerHTML = ''; // Clear semua
        recipeItemIndex = 0; // Reset index

        // Show assumptions first
        if (recipe.assumptions) {
            showAIAssumptions(recipe.assumptions, recipe.missing_ingredients || []);
        }

        // ✅ FIX: Add ALL ingredients dengan loop
        recipe.ingredients.forEach((ingredient, index) => {
            console.log(`Processing ingredient ${index}:`, ingredient);
            
            // Buat item baru untuk SETIAP ingredient
            const newItem = document.createElement('div');
            newItem.className = 'recipe-item border border-gray-200 rounded-lg p-5 bg-gray-50';
            newItem.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Bahan Baku <span class="text-red-500">*</span>
                        </label>
                        <select name="recipe_items[${recipeItemIndex}][raw_material_id]" class="raw-material-select w-full" required>
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
                        <input type="number" step="0.01" name="recipe_items[${recipeItemIndex}][quantity]" 
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
                    <input type="text" name="recipe_items[${recipeItemIndex}][notes]" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                        placeholder="Catatan tambahan (opsional)">
                </div>
            `;
            
            container.appendChild(newItem);
            
            // Initialize Select2
            const select = $(newItem).find('.raw-material-select');
            select.select2({
                theme: 'default',
                width: '100%',
                placeholder: '- Pilih Bahan -'
            });
            
            // Set values
            select.val(ingredient.raw_material_id).trigger('change');
            
            const qtyInput = newItem.querySelector('.quantity-input');
            if (qtyInput) {
                qtyInput.value = ingredient.quantity;
            }
            
            const noteInput = newItem.querySelector('input[name*="[notes]"]');
            if (noteInput && ingredient.notes) {
                noteInput.value = ingredient.notes;
            }
            
            // Trigger calculation
            calculateItemCost(newItem);
            
            recipeItemIndex++;
        });

        // Update UI states
        updateRemoveButtons();
        updateAvailableRawMaterials();
        calculateTotalMaterialCost();
        saveFormData();

        // Success message
        showSuccessMessage(
            'Resep berhasil di-generate! ' +
            'Total ' + recipe.ingredients.length + ' bahan baku ditambahkan.'
        );

    } catch (error) {
        console.error('Error details:', error);
        alert('Gagal generate resep: ' + error.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    }
});

function showAIAssumptions(assumptions, missingIngredients) {
    let html = `
        <div class="fixed inset-0 z-50 overflow-y-auto" style="background: rgba(0,0,0,0.5);">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        Informasi Generate Resep
                    </h3>
                    
                    <div class="mb-4">
                        <h4 class="font-semibold mb-2">Asumsi AI:</h4>
                        <p class="text-sm text-gray-700 bg-blue-50 p-3 rounded">${assumptions}</p>
                    </div>
    `;

    if (missingIngredients && missingIngredients.length > 0) {
        html += `
            <div class="mb-4">
                <h4 class="font-semibold mb-2 text-orange-600">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Bahan yang Tidak Ditemukan:
                </h4>
                <ul class="text-sm space-y-2">
        `;
        missingIngredients.forEach(item => {
            html += `
                <li class="bg-orange-50 p-2 rounded">
                    <strong>${item.name}</strong>: ${item.reason}
                </li>
            `;
        });
        html += `</ul></div>`;
    }

    html += `
                    <button onclick="this.closest('.fixed').remove()" 
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Mengerti
                    </button>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', html);
}

function showSuccessMessage(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2';
    toast.innerHTML = `
        <i class="fas fa-check-circle"></i>
        <span>${message}</span>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
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
                updateAvailableRawMaterials();
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
    $(document).on('select2:select select2:unselect', '.raw-material-select', function() {
        calculateItemCost($(this).closest('.recipe-item')[0]);
        calculateTotalMaterialCost();
        updateAvailableRawMaterials(); // Tambahkan ini
        saveFormData();
    });

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
        const label = indicator.querySelector('p');
        
        if (!circle) return;

        // Base classes for circle (Responsive: w-8 h-8 on mobile, w-10 h-10 on desktop)
        const baseCircleClass = 'w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center mx-auto mb-2 transition-all duration-300';
        const defaultIcon = icon ? icon.getAttribute('data-default-icon') : '';
        
        if (stepNum < step) {
            // Completed Step
            circle.className = baseCircleClass + ' bg-cuan-green shadow-md';
            if (icon) icon.className = 'fas fa-check text-white text-xs md:text-sm';
            if (label) {
                label.className = 'hidden md:block text-[11px] font-medium text-cuan-green transition-colors duration-300';
            }
        } else if (stepNum === step) {
            // Active Step
            circle.className = baseCircleClass + ' bg-cuan-green shadow-md ring-4 ring-green-200 transform scale-110';
            if (icon && defaultIcon) icon.className = defaultIcon + ' text-white text-xs md:text-sm';
            if (label) {
                label.className = 'hidden md:block text-[11px] font-bold text-gray-900 transition-colors duration-300';
            }
        } else {
            // Inactive Step
            circle.className = baseCircleClass + ' bg-white border-2 border-gray-300';
            if (icon && defaultIcon) icon.className = defaultIcon + ' text-gray-400 text-xs md:text-sm';
            if (label) {
                label.className = 'hidden md:block text-[11px] font-medium text-gray-400 transition-colors duration-300';
            }
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
    
    if (!container) {
        console.error('Recipe items container not found!');
        return;
    }
    
    const newItem = document.createElement('div');
    newItem.className = 'recipe-item border border-gray-200 rounded-lg p-5 bg-gray-50';
    newItem.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Bahan Baku <span class="text-red-500">*</span>
                </label>
                <select name="recipe_items[${recipeItemIndex}][raw_material_id]" class="raw-material-select w-full" required>
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
                <input type="number" step="0.01" name="recipe_items[${recipeItemIndex}][quantity]" 
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
            <input type="text" name="recipe_items[${recipeItemIndex}][notes]" 
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                placeholder="Catatan tambahan (opsional)">
        </div>
    `;
    
    container.appendChild(newItem);
    
    // Initialize Select2 for the new item
    $(newItem).find('.raw-material-select').select2({
        theme: 'default',
        width: '100%',
        placeholder: '- Pilih Bahan -'
    });
    
    recipeItemIndex++;
    updateRemoveButtons();
    updateAvailableRawMaterials();
    
    console.log('Added new recipe item, index:', recipeItemIndex);
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
    if (!item) {
        console.error('calculateItemCost: item is null/undefined');
        return;
    }
    
    const select = item.querySelector('.raw-material-select');
    const quantityInput = item.querySelector('.quantity-input');
    const unitDisplay = item.querySelector('.unit-display');
    const costDisplay = item.querySelector('.cost-display');
    
    if (!select || !quantityInput || !unitDisplay || !costDisplay) {
        console.error('calculateItemCost: Missing required elements', {
            select: !!select,
            quantityInput: !!quantityInput,
            unitDisplay: !!unitDisplay,
            costDisplay: !!costDisplay
        });
        return;
    }
    
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
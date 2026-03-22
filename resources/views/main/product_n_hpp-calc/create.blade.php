@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Tambah Produk & Resep - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('products-hpp.index') }}" class="text-gray-500 hover:text-cuan-green transition-colors">Produk & Resep</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Tambah Produk</span>
</li>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Select2 Container Styling */
    .select2-container--default .select2-selection--single {
        height: 56px !important;
        border: 2px solid #f3f4f6 !important;
        border-radius: 1rem !important;
        padding: 0.75rem 1.25rem !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        background-color: #f9fafb !important;
        display: flex !important;
        align-items: center !important;
        outline: none !important;
    }

    /* Text inside selection */
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal !important;
        color: #111827 !important;
        font-weight: 700 !important;
        font-size: 0.875rem !important;
        padding-left: 0 !important;
        padding-right: 20px !important;
    }

    /* Arrow Styling */
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100% !important;
        right: 15px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #9ca3af transparent transparent transparent !important;
        border-width: 6px 5px 0 5px !important;
        margin-left: -5px !important;
        transition: transform 0.3s !important;
    }

    .select2-container--active .select2-selection--single .select2-selection__arrow b {
        transform: rotate(180deg) !important;
        border-color: #658C58 transparent transparent transparent !important;
    }

    /* Focus & Open States */
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #658C58 !important;
        background-color: white !important;
        box-shadow: 0 0 0 4px rgba(101, 140, 88, 0.1) !important;
    }

    /* Dropdown Styling */
    .select2-dropdown {
        border: 2px solid #f3f4f6 !important;
        border-radius: 1.25rem !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        margin-top: 8px !important;
        overflow: hidden !important;
        z-index: 9999 !important;
        padding: 5px !important;
        background: white !important;
    }

    .select2-results__option {
        padding: 12px 16px !important;
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        color: #374151 !important;
        border-radius: 0.75rem !important;
        margin-bottom: 2px !important;
        transition: all 0.2s !important;
    }

    .select2-results__option--highlighted[aria-selected] {
        background-color: #658C58 !important;
        color: white !important;
    }

    .select2-results__option[aria-selected="true"] {
        background-color: #f3f4f6 !important;
        color: #658C58 !important;
    }

    /* Search inside dropdown */
    .select2-search--dropdown {
        padding: 8px 8px 12px !important;
    }

    .select2-search--dropdown .select2-search__field {
        border: 2px solid #f3f4f6 !important;
        border-radius: 0.75rem !important;
        padding: 10px 14px !important;
        font-weight: 600 !important;
        font-size: 0.875rem !important;
        outline: none !important;
        transition: all 0.2s !important;
    }

    .select2-search--dropdown .select2-search__field:focus {
        border-color: #658C58 !important;
        background-color: #f9fafb !important;
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

        {{-- Validation errors & session flash handled by SweetAlert2 in scripts --}}

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Tambah Produk & Resep
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Isi data produk langkah demi langkah. Tenang, semua data masih bisa diubah setelah disimpan.
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('products-hpp.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all active:scale-95">
                    <span>Kembali</span>
                </a>
            </div>
        </section>

        {{-- CARD FORM BERTAHAP --}}
        <x-card-container>

            {{-- Progress Steps --}}
            <div class="bg-gray-50/50 px-2 md:px-6 py-4 md:py-5 border-b border-gray-100">
                <div class="flex justify-between items-center relative max-w-3xl mx-auto">
                    {{-- Garis Progress --}}
                    <div class="absolute top-4 md:top-5 left-0 right-0 h-0.5 bg-gray-200" style="z-index: 0;">
                        <div id="progressLine" class="h-full bg-cuan-green transition-all duration-300" style="width: 0%;"></div>
                    </div>

                    {{-- Langkah 1 --}}
                    <div class="flex-1 text-center step-indicator active relative z-10 cursor-default" data-step="1" title="Info Produk">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-cuan-green rounded-full flex items-center justify-center mx-auto mb-2 shadow-md transition-all duration-300">
                            <span class="text-white text-xs md:text-sm font-black" data-default-icon="step-number">1</span>
                        </div>
                        <p class="hidden md:block text-[11px] font-medium text-gray-900 transition-colors duration-300">Info Produk</p>
                    </div>

                    {{-- Langkah 2 --}}
                    <div class="flex-1 text-center step-indicator relative z-10 cursor-default" data-step="2" title="Resep">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center mx-auto mb-2 transition-all duration-300">
                            <span class="text-gray-400 text-xs md:text-sm font-black" data-default-icon="step-number">2</span>
                        </div>
                        <p class="hidden md:block text-[11px] font-medium text-gray-600 transition-colors duration-300">Resep</p>
                    </div>

                    {{-- Langkah 3 --}}
                    <div class="flex-1 text-center step-indicator relative z-10 cursor-default" data-step="3" title="Bahan Baku">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center mx-auto mb-2 transition-all duration-300">
                            <span class="text-gray-400 text-xs md:text-sm font-black" data-default-icon="step-number">3</span>
                        </div>
                        <p class="hidden md:block text-[11px] font-medium text-gray-600 transition-colors duration-300">Bahan Baku</p>
                    </div>

                    {{-- Langkah 4 --}}
                    <div class="flex-1 text-center step-indicator relative z-10 cursor-default" data-step="4" title="Biaya Lain">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center mx-auto mb-2 transition-all duration-300">
                            <span class="text-gray-400 text-xs md:text-sm font-black" data-default-icon="step-number">4</span>
                        </div>
                        <p class="hidden md:block text-[11px] font-medium text-gray-600 transition-colors duration-300">Biaya Lain</p>
                    </div>

                    {{-- Langkah 5 --}}
                    <div class="flex-1 text-center step-indicator relative z-10 cursor-default" data-step="5" title="Harga & Stok">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center mx-auto mb-2 transition-all duration-300">
                            <span class="text-gray-400 text-xs md:text-sm font-black" data-default-icon="step-number">5</span>
                        </div>
                        <p class="hidden md:block text-[11px] font-medium text-gray-600 transition-colors duration-300">Harga & Stok</p>
                    </div>

                    {{-- Langkah 6 --}}
                    <div class="flex-1 text-center step-indicator relative z-10 cursor-default" data-step="6" title="Target Jual">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center mx-auto mb-2 transition-all duration-300">
                            <span class="text-gray-400 text-xs md:text-sm font-black" data-default-icon="step-number">6</span>
                        </div>
                        <p class="hidden md:block text-[11px] font-medium text-gray-600 transition-colors duration-300">Target Jual</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('products-hpp.store') }}" method="POST" enctype="multipart/form-data" id="productForm" novalidate>
                @csrf

                {{-- STEP 1: INFO DASAR --}}
                <div class="step-content px-6 md:px-8 py-6" id="step1">
                    <div class="mb-6">
                        <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">
                            Info Produk
                        </h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">
                            Masukkan nama, kode, dan keterangan singkat produk yang ingin dijual.
                        </p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">
                            Jenis Produk <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Option 1: Produk Masak Langsung -->
                            <label class="relative flex flex-col p-4 bg-white border-2 border-gray-100 rounded-xl cursor-pointer hover:border-cuan-green hover:bg-green-50 transition-all group product-type-option" data-type="direct">
                                <input type="radio" name="product_type" value="direct" class="sr-only peer" {{ old('product_type', 'direct') == 'direct' ? 'checked' : '' }}>
                                <div class="flex items-center justify-between mb-2">
                                    <div class="w-10 h-10 bg-green-100 text-cuan-green rounded-full flex items-center justify-center group-hover:bg-cuan-green group-hover:text-white transition-colors peer-checked:bg-cuan-green peer-checked:text-white">
                                        <i class="fas fa-utensils"></i>
                                    </div>
                                    <div class="w-6 h-6 border-2 border-gray-300 rounded-full flex items-center justify-center peer-checked:border-cuan-green peer-checked:bg-cuan-green transition-all shadow-sm">
                                        <div class="w-2.5 h-2.5 bg-white rounded-full"></div>
                                    </div>
                                </div>
                                <span class="text-sm font-bold text-gray-900 group-hover:text-cuan-green transition-colors peer-checked:text-cuan-green">Produk Masak Langsung</span>
                                <p class="text-[10px] text-gray-500 mt-1">Masak saat pesanan datang, tanpa simpan stok.</p>
                                <div class="absolute inset-0 border-2 border-transparent peer-checked:border-cuan-green rounded-xl pointer-events-none"></div>
                            </label>

                            <!-- Option 2: Produk Stok Produksi -->
                            <label class="relative flex flex-col p-4 bg-white border-2 border-gray-100 rounded-xl cursor-pointer hover:border-cuan-green hover:bg-green-50 transition-all group product-type-option" data-type="stock">
                                <input type="radio" name="product_type" value="stock" class="sr-only peer" {{ old('product_type') == 'stock' ? 'checked' : '' }}>
                                <div class="flex items-center justify-between mb-2">
                                    <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors peer-checked:bg-blue-600 peer-checked:text-white">
                                        <i class="fas fa-boxes"></i>
                                    </div>
                                    <div class="w-6 h-6 border-2 border-gray-300 rounded-full flex items-center justify-center peer-checked:border-cuan-green peer-checked:bg-cuan-green transition-all shadow-sm">
                                        <div class="w-2.5 h-2.5 bg-white rounded-full"></div>
                                    </div>
                                </div>
                                <span class="text-sm font-bold text-gray-900 group-hover:text-cuan-green transition-colors peer-checked:text-cuan-green">Produk Stok Produksi</span>
                                <p class="text-[10px] text-gray-500 mt-1">Produksi massal dan disimpan sebagai stok jadi.</p>
                                <div class="absolute inset-0 border-2 border-transparent peer-checked:border-cuan-green rounded-xl pointer-events-none"></div>
                            </label>

                            <!-- Option 3: Produk Siap Jual -->
                            <label class="relative flex flex-col p-4 bg-white border-2 border-gray-100 rounded-xl cursor-pointer hover:border-cuan-green hover:bg-green-50 transition-all group product-type-option" data-type="ready">
                                <input type="radio" name="product_type" value="ready" class="sr-only peer" {{ old('product_type') == 'ready' ? 'checked' : '' }}>
                                <div class="flex items-center justify-between mb-2">
                                    <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition-colors peer-checked:bg-orange-600 peer-checked:text-white">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <div class="w-6 h-6 border-2 border-gray-300 rounded-full flex items-center justify-center peer-checked:border-cuan-green peer-checked:bg-cuan-green transition-all shadow-sm">
                                        <div class="w-2.5 h-2.5 bg-white rounded-full"></div>
                                    </div>
                                </div>
                                <span class="text-sm font-bold text-gray-900 group-hover:text-cuan-green transition-colors peer-checked:text-cuan-green">Produk Siap Jual</span>
                                <p class="text-[10px] text-gray-500 mt-1">Produk jadi dari supplier, tanpa perlu resep.</p>
                                <div class="absolute inset-0 border-2 border-transparent peer-checked:border-cuan-green rounded-xl pointer-events-none"></div>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Kode Produk --}}
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                Kode Produk <span class="text-red-500">*</span>
                            </label>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <input type="text" name="code" id="productCode" value="{{ old('code') }}"
                                       class="flex-1 px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all"
                                       placeholder="Contoh: PRD001" required>
                                @can('generate kode produk')
                                <button type="button" id="generateCode"
                                        class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-3 bg-cuan-green text-white rounded-xl hover:bg-cuan-dark transition-colors whitespace-nowrap text-sm font-bold">
                                    Buat Otomatis
                                </button>
                                @endcan
                            </div>
                            <p class="mt-2 text-[10px] text-gray-400 font-medium">
                                Kode bebas, yang penting mudah dibaca tim Anda.
                            </p>
                        </div>

                        {{-- Nama Produk --}}
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                Nama Produk <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all"
                                   placeholder="Contoh: Donat Cokelat Isi" required>
                        </div>

                        {{-- Barcode --}}
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                Barcode
                            </label>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <input type="text" name="barcode" id="productBarcode" value="{{ old('barcode') }}"
                                       class="flex-1 px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all"
                                       placeholder="Opsional">
                                <button type="button" id="startScan"
                                        class="w-full sm:w-auto px-4 py-3 bg-gray-800 text-white rounded-xl hover:bg-gray-900 transition-colors flex items-center justify-center font-bold text-sm"
                                        title="Scan Barcode">
                                    <i class="fas fa-qrcode mr-2 sm:mr-0"></i>
                                    <span class="sm:hidden">Scan</span>
                                </button>
                                @can('generate barcode produk')
                                <button type="button" id="generateBarcode"
                                        class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-3 bg-cuan-green text-white rounded-xl hover:bg-cuan-dark transition-colors whitespace-nowrap text-sm font-bold">
                                    Buat Otomatis
                                </button>
                                @endcan
                            </div>
                            <p class="mt-2 text-[10px] text-gray-400 font-medium">
                                Jika belum pakai barcode, bisa dikosongkan dulu.
                            </p>
                        </div>

                        {{-- Kategori --}}
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                Kategori
                            </label>
                            <select name="category_id" class="select2-init w-full">
                                <option value="">- Pilih Kategori -</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-[10px] text-gray-400 font-medium">
                                Contoh: Minuman, Snack, Kue, Lauk, dll.
                            </p>
                        </div>

                        {{-- Supplier --}}
                        <div id="supplierSelectContainer">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                Supplier
                            </label>
                            <select name="supplier_id" class="select2-init w-full">
                                <option value="">- Pilih Supplier -</option>
                                @foreach($suppliers ?? [] as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-[10px] text-gray-400 font-medium">
                                Opsional, untuk tracking asal produk instant.
                            </p>
                        </div>

                        {{-- Satuan --}}
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                Satuan <span class="text-red-500">*</span>
                            </label>
                            <select name="unit_id" class="select2-init w-full" required>
                                <option value="">- Pilih Satuan -</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-[10px] text-gray-400 font-medium">
                                Contoh: pcs, cup, box, bungkus, dll.
                            </p>
                        </div>

                        {{-- Foto Produk --}}
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                Foto Produk
                            </label>
                            <input type="file" name="image" id="imageInput" accept="image/*"
                                   class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-cuan-green/10 file:text-cuan-green hover:file:bg-cuan-green/20">
                            <p class="text-[10px] text-gray-400 font-medium mt-2">Maksimal 2MB (JPG, JPEG, PNG).</p>

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
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                Deskripsi
                            </label>
                            <textarea name="description" rows="3"
                                      class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all"
                                      placeholder="Tuliskan deskripsi singkat produk (rasa, ukuran, keunggulan, dll)">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- STEP 2: RESEP --}}
                <div class="step-content px-6 md:px-8 py-6 hidden" id="step2">
                    <div class="mb-6">
                        <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">
                            Resep Produk
                        </h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">
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

                        {{-- Toggle Produk Bisa Stok --}}
                        <div class="md:col-span-2">
                            <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                                        <i class="fas fa-boxes"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900">Produk Bisa Di-stok?</h4>
                                        <p class="text-xs text-gray-500">Aktifkan jika produk ini bisa diproduksi masal & disimpan (Contoh: minuman botol, sambal kemasan).</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_stock" value="1" class="sr-only peer" {{ old('is_stock') ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
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
                <div class="step-content px-6 md:px-8 py-6 hidden" id="step3">
                    <div class="mb-6">
                        <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">
                            Bahan Baku
                        </h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">
                            Tambahkan bahan baku yang dipakai untuk sekali resep.
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
                                    @can('generate resep ai')
                                    <button type="button" id="generateRecipeAI"
                                            class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium text-sm">
                                        <i class="fas fa-sparkles mr-2"></i>
                                        Buat Resep Otomatis
                                    </button>
                                    @endcan
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
                <div class="step-content px-6 md:px-8 py-6 hidden" id="step4">
                    <div class="mb-6">
                        <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">
                            Biaya Lain-lain
                        </h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">
                            Masukkan biaya di luar bahan baku, misalnya listrik, gas, kemasan, dan tenaga kerja.
                        </p>
                    </div>

                    <div class="bg-cuan-green/5 border border-cuan-green/10 rounded-2xl p-4 mb-6">
                        <p class="text-sm text-gray-600">
                            Biaya tambahan ini membantu HPP lebih realistis. Kalau bingung, bisa isi perkiraan
                            per resep atau isi 0 dulu, nanti bisa disesuaikan lagi.
                        </p>
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
                        <h4 class="text-base font-bold text-gray-900 mb-4">
                            Ringkasan HPP (Harga Pokok Produksi)
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
                <div class="step-content px-6 md:px-8 py-6 hidden" id="step5">
                    <div class="mb-6">
                        <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">
                            Harga Jual & Stok
                        </h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">
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
                        {{-- Input Stok Awal & HPP Manual (Hanya untuk Produk Siap Jual) --}}
                        <div id="readyToSellFields" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 hidden">
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-5 md:col-span-2 mb-4">
                                <h4 class="text-sm font-bold text-orange-800 mb-2">
                                    Khusus Produk Siap Jual
                                </h4>
                                <p class="text-xs text-orange-700">
                                    Karena produk ini tidak memiliki resep, harap masukkan modal (HPP) dan stok awal secara manual.
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    HPP (Modal) per Unit <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.01" name="manual_hpp" id="manualHppInput"
                                       value="{{ old('manual_hpp') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Contoh: 5000">
                                <p class="text-xs text-gray-500 mt-1">Harga beli dari supplier per unit.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Stok yang Tersedia Sekarang <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.01" name="initial_stock" id="initialStockInput"
                                       value="{{ old('initial_stock', 0) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Contoh: 50">
                                <p class="text-xs text-gray-500 mt-1">Jumlah stok fisik yang ada di outlet saat ini.</p>
                            </div>

                            {{-- Financial Tracking for Initial Stock --}}
                            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 mt-4 border-t border-gray-100">
                                <div class="md:col-span-2">
                                    <h5 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Informasi Pembelian Stok Awal</h5>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori Pengeluaran <span class="text-red-500">*</span></label>
                                    <select name="expense_category_id" class="select2-init w-full">
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach($expenseCategories ?? [] as $category)
                                            <option value="{{ $category->id }}" {{ old('expense_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Metode Bayar <span class="text-red-500">*</span></label>
                                    <select name="payment_method" class="select2-init w-full">
                                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                                        <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                        <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Kartu</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Batch / SN</label>
                                    <input type="text" name="batch_number" value="{{ old('batch_number') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Opsional">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kadaluarsa</label>
                                    <input type="date" name="expired_at" value="{{ old('expired_at') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>

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
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">
                            Perkiraan Laba per Unit
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
                <div class="step-content px-6 md:px-8 py-6 hidden" id="step6">
                    <div class="mb-6">
                        <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">
                            Target Penjualan
                        </h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">
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
                            <h4 class="text-lg font-bold text-gray-800 mb-4">
                                Ringkasan Penjualan 30 Hari Terakhir
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
                                        <h5 class="font-semibold text-gray-800 mb-3">
                                            Tren penjualan per minggu
                                        </h5>
                                        <canvas id="weeklyTrendChart" height="250"></canvas>
                                    </div>

                                    <div class="bg-white rounded-lg p-5 shadow-sm">
                                        <h5 class="font-semibold text-gray-800 mb-3">
                                            Pola penjualan per hari
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
                                <h4 class="text-lg font-bold text-gray-800 mb-4">
                                Perhitungan Target Penjualan
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
                                <h4 class="text-lg font-bold text-gray-800 mb-4">
                                    Proyeksi Pencapaian Target
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
                <div class="px-6 md:px-8 py-5 bg-gray-50/50 border-t border-gray-100">
                    <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3">
                        <button type="button" id="prevBtn"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 bg-white border border-gray-200 text-gray-600 rounded-2xl font-bold text-sm hover:bg-gray-50 transition-all active:scale-95"
                                style="display: none;">
                            Sebelumnya
                        </button>

                        <div class="hidden sm:block"></div>

                        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                            <button type="button" id="nextBtn"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 bg-cuan-green text-white rounded-2xl font-black text-sm hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95 order-2 sm:order-1">
                                Lanjut
                            </button>
                            <button type="submit" id="submitBtn"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 bg-cuan-green text-white rounded-2xl font-black text-sm hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95 order-1 sm:order-2"
                                    style="display: none;">
                                Simpan Produk
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </x-card-container>
    </div>

    {{-- Modal Scanner --}}
    <div id="scannerModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 hidden">
        <div class="bg-white rounded-lg p-5 w-full max-w-md mx-4 relative">
            <button type="button" id="closeScanner" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 z-10">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h3 class="text-lg font-bold mb-4 text-center">Scan Barcode</h3>
            <div id="reader" class="w-full bg-gray-100 rounded-lg overflow-hidden"></div>
            <p class="text-xs text-gray-500 mt-3 text-center">Arahkan kamera ke barcode produk</p>
        </div>
    </div>
</main>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
{{-- HTML5-QRCode Library --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
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
                Swal.fire({ icon: 'warning', title: 'File Terlalu Besar', text: 'Ukuran file maksimal 2MB.', confirmButtonColor: '#658C58', customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl font-bold text-sm' } });
                imageInput.value = '';
                return;
            }
            
            if (!file.type.match('image.*')) {
                Swal.fire({ icon: 'warning', title: 'Format Salah', text: 'File harus berupa gambar (JPG, JPEG, PNG).', confirmButtonColor: '#658C58', customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl font-bold text-sm' } });
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
        Swal.fire({ icon: 'error', title: 'Error', text: 'Input tidak ditemukan.', confirmButtonColor: '#658C58', customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl font-bold text-sm' } });
        return;
    }
    
    const productName = productNameInput.value;
    const outputQty = outputQtyInput.value;

    if (!productName || !outputQty) {
        Swal.fire({ icon: 'warning', title: 'Data Belum Lengkap', text: 'Mohon isi Nama Produk (Step 1) dan Jumlah Output (Step 2) terlebih dahulu.', confirmButtonColor: '#658C58', customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl font-bold text-sm' } });
        return;
    }

    // Konfirmasi jika sudah ada recipe items
    const existingItems = document.querySelectorAll('.recipe-item').length;
    if (existingItems > 0) {
        const result = await Swal.fire({
            icon: 'question',
            title: 'Ganti Bahan Baku?',
            text: 'Anda sudah memiliki ' + existingItems + ' bahan baku. Generate AI akan mengganti semua bahan yang ada.',
            showCancelButton: true,
            confirmButtonColor: '#658C58',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl font-bold text-sm', cancelButton: 'rounded-xl font-bold text-sm' }
        });
        if (!result.isConfirmed) return;
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
                category_name: $('.select2-category').find(':selected').text().trim(),
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
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: message,
        showConfirmButton: false,
        timer: 3000,
        iconColor: '#658C58',
        customClass: {
            popup: 'rounded-3xl border-none shadow-2xl',
            title: 'font-black text-gray-900',
            htmlContainer: 'text-sm font-medium text-gray-500'
        }
    });
}

// ============================================
// EVENT LISTENERS FOR DRAFT
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // ============================================
    // SWEETALERT2 SESSION & VALIDATION HANDLERS
    // ============================================
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 3000,
            iconColor: '#658C58',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black text-gray-900',
                htmlContainer: 'text-sm font-medium text-gray-500'
            }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: "{{ session('error') }}",
            confirmButtonColor: '#658C58',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black text-gray-900',
                htmlContainer: 'text-sm font-medium text-gray-500',
                confirmButton: 'rounded-xl px-6 py-3 font-bold text-sm'
            }
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Ada data yang belum benar',
            html: `<ul class="text-left text-sm space-y-1 mt-2">@foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach</ul><p class="text-xs text-gray-400 mt-3">Periksa kembali isian yang bertanda <strong>*</strong>.</p>`,
            confirmButtonColor: '#658C58',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black text-gray-900 text-lg',
                htmlContainer: 'text-sm font-medium text-gray-500',
                confirmButton: 'rounded-xl px-6 py-3 font-bold text-sm'
            }
        });
    @endif

    // Initialize Select2
    // Global Select2 initialization for standard selects
    $('.select2-init').select2({
        theme: 'default',
        width: '100%',
        minimumResultsForSearch: 10
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
    
    const isStockCheckbox = document.querySelector('input[name="is_stock"]');
    const isStockToggleContainer = isStockCheckbox ? isStockCheckbox.closest('.bg-blue-50') : null;
    window.currentProductType = $('input[name="product_type"]:checked').val() || 'direct';

    // Event listener for product type change
    $('input[name="product_type"]').on('change', function() {
        window.currentProductType = $(this).val();
        
        const readyToSellFields = document.getElementById('readyToSellFields');
        const hppSummarySection = document.querySelector('#step5 .bg-green-50'); // Final modal HPP summary
        const marginHppRow = document.querySelector('#marginHpp').closest('div'); // HPP row in results calculation
        
        // Auto-set is_stock checkbox and hide the toggle container in step 2
        if (currentProductType === 'direct') {
            if (isStockCheckbox) isStockCheckbox.checked = false;
            if (isStockToggleContainer) isStockToggleContainer.classList.add('hidden');
        } else {
            if (isStockCheckbox) isStockCheckbox.checked = true;
            if (currentProductType === 'stock') {
                if (isStockToggleContainer) isStockToggleContainer.classList.remove('hidden');
            } else {
                if (isStockToggleContainer) isStockToggleContainer.classList.add('hidden');
            }
        }

        if (currentProductType === 'ready') {
            if (readyToSellFields) readyToSellFields.classList.remove('hidden');
            if (hppSummarySection) hppSummarySection.classList.add('hidden');
            if (marginHppRow) marginHppRow.classList.add('hidden');
            if (document.getElementById('supplierSelectContainer')) document.getElementById('supplierSelectContainer').classList.remove('hidden');
            
            // Set required attributes
            document.getElementById('manualHppInput').setAttribute('required', 'required');
            document.getElementById('initialStockInput').setAttribute('required', 'required');
            document.querySelector('select[name="expense_category_id"]').setAttribute('required', 'required');
            document.querySelector('select[name="payment_method"]').setAttribute('required', 'required');
        } else {
            if (readyToSellFields) readyToSellFields.classList.add('hidden');
            if (hppSummarySection) hppSummarySection.classList.remove('hidden');
            if (marginHppRow) marginHppRow.classList.remove('hidden');
            if (document.getElementById('supplierSelectContainer')) document.getElementById('supplierSelectContainer').classList.add('hidden');
            
            // Remove required attributes
            if (document.getElementById('manualHppInput')) document.getElementById('manualHppInput').removeAttribute('required');
            if (document.getElementById('initialStockInput')) document.getElementById('initialStockInput').removeAttribute('required');
            if (document.querySelector('select[name="expense_category_id"]')) document.querySelector('select[name="expense_category_id"]').removeAttribute('required');
            if (document.querySelector('select[name="payment_method"]')) document.querySelector('select[name="payment_method"]').removeAttribute('required');
        }
        
        showStep(currentStep);
        saveFormData();
    });

    // Trigger initial state
    $('input[name="product_type"]:checked').trigger('change');

    showStep(currentStep);
    
    // Next button
    document.getElementById('nextBtn').addEventListener('click', function() {
        if (validateStep(currentStep)) {
            let nextStep = currentStep + 1;
            
            // Skip steps if ready to sell
            if (window.currentProductType === 'ready' && currentStep === 1) {
                nextStep = 5;
            }
            
            if (nextStep === 4) {
                updateHppSummary();
            }
            if (nextStep === 5) {
                updateFinalPricing();
            }
            
            currentStep = nextStep;
            showStep(currentStep);
            saveFormData(); // Auto-save on step change
        }
    });
    
    // Previous button
    document.getElementById('prevBtn').addEventListener('click', function() {
        let prevStep = currentStep - 1;
        
        // Skip steps if ready to sell
        if (window.currentProductType === 'ready' && currentStep === 5) {
            prevStep = 1;
        }
        
        currentStep = prevStep;
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
    
    const isReadyType = window.currentProductType === 'ready';
    
    document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
        const stepNum = parseInt(indicator.getAttribute('data-step'));
        const circle = indicator.querySelector('div');
        const stepEl = circle ? circle.querySelector('span, i') : null;
        const label = indicator.querySelector('p');
        const isSkipped = isReadyType && [2, 3, 4].includes(stepNum);
        
        if (!circle) return;

        if (isSkipped) {
            indicator.style.display = 'none';
        } else {
            indicator.style.display = 'block';
        }

        // Base classes for circle
        const baseCircleClass = 'w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center mx-auto mb-2 transition-all duration-300';
        
        if (stepNum < step) {
            // Completed Step - show checkmark
            circle.className = baseCircleClass + ' bg-cuan-green shadow-md';
            if (stepEl) {
                const checkIcon = document.createElement('i');
                checkIcon.className = 'fas fa-check text-white text-xs md:text-sm';
                stepEl.replaceWith(checkIcon);
            }
            if (label) {
                label.className = 'hidden md:block text-[11px] font-medium text-cuan-green transition-colors duration-300';
            }
        } else if (stepNum === step) {
            // Active Step - show step number
            circle.className = baseCircleClass + ' bg-cuan-green shadow-md ring-4 ring-green-200 transform scale-110';
            if (stepEl) {
                const numSpan = document.createElement('span');
                numSpan.className = 'text-white text-xs md:text-sm font-black';
                numSpan.setAttribute('data-default-icon', 'step-number');
                numSpan.textContent = stepNum;
                stepEl.replaceWith(numSpan);
            }
            if (label) {
                label.className = 'hidden md:block text-[11px] font-bold text-gray-900 transition-colors duration-300';
            }
        } else {
            // Inactive Step - show step number
            circle.className = baseCircleClass + ' bg-white border-2 border-gray-300';
            if (stepEl) {
                const numSpan = document.createElement('span');
                numSpan.className = 'text-gray-400 text-xs md:text-sm font-black';
                numSpan.setAttribute('data-default-icon', 'step-number');
                numSpan.textContent = stepNum;
                stepEl.replaceWith(numSpan);
            }
            if (label) {
                label.className = 'hidden md:block text-[11px] font-medium text-gray-400 transition-colors duration-300';
            }
        }
    });

    // Update progress line
    let progressPercent = ((step - 1) / (totalSteps - 1)) * 100;
    if (isReadyType) {
        // Map available steps to progress percentage
        const readySteps = [1, 5, 6];
        const stepIndex = readySteps.indexOf(step);
        if (stepIndex !== -1) {
            progressPercent = (stepIndex / (readySteps.length - 1)) * 100;
        }
    }
    
    // Completely hide skipped steps for "Ready" type instead of just lowering opacity
    document.querySelectorAll('.step-indicator').forEach((indicator) => {
        const stepNum = parseInt(indicator.getAttribute('data-step'));
        const isSkipped = isReadyType && [2, 3, 4].includes(stepNum);
        
        if (isSkipped) {
            indicator.style.display = 'none';
        } else {
            indicator.style.display = 'block';
        }
    });

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
        // Skip hidden required inputs
        if (input.closest('.hidden')) continue;
        
        if (!input.value || input.value.trim() === '') {
            Swal.fire({ icon: 'warning', title: 'Data Belum Lengkap', text: 'Mohon lengkapi semua field yang wajib diisi (*).', confirmButtonColor: '#658C58', customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl font-bold text-sm' } });
            input.focus();
            return false;
        }
    }
    
    if (step === 3 && currentProductType !== 'ready') {
        const recipeItems = document.querySelectorAll('.recipe-item');
        if (recipeItems.length === 0) {
            Swal.fire({ icon: 'warning', title: 'Data Belum Lengkap', text: 'Minimal harus ada 1 bahan baku.', confirmButtonColor: '#658C58', customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl font-bold text-sm' } });
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
    const manualHpp = parseFloat(document.getElementById('manualHppInput').value || 0);
    
    let hpp = window.hppPerUnitValue || 0;
    if (currentProductType === 'ready') {
        hpp = manualHpp;
        window.hppPerUnitValue = hpp; // Sync for target calculation
    }
    
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

    // SKIP FETCHING for new product (create page)
    showNoHistoricalData();
    return;

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
        Swal.fire({ icon: 'info', title: 'Harga Jual Belum Diisi', text: 'Mohon isi "Harga Jual" di Step 5 terlebih dahulu untuk menghitung target.', confirmButtonColor: '#658C58', customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl font-bold text-sm' } });
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

<script>
    // Barcode Scanner Logic
    let html5QrcodeScanner = null;

    function openScanner() {
        document.getElementById('scannerModal').classList.remove('hidden');
        
        // Initialize scanner if not already
        if (!html5QrcodeScanner) {
            html5QrcodeScanner = new Html5Qrcode("reader");
        }
        
        const config = { fps: 10, qrbox: { width: 250, height: 150 }, aspectRatio: 1.0 };
        
        // Prefer back camera
        html5QrcodeScanner.start({ facingMode: "environment" }, config, onScanSuccess, onScanFailure)
        .catch(err => {
            console.error("Error starting scanner", err);
            // alert('Gagal membuka kamera: ' + err); 
            // Better to log or show subtle error. Alert might be jarring if it just fails silently sometimes.
            // But since user complained, maybe they want to know.
            alert('Gagal membuka kamera: ' + err.message);
            closeScanner();
        });
    }

    function closeScanner() {
        document.getElementById('scannerModal').classList.add('hidden');
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop().then(() => {
                console.log("Scanner stopped");
            }).catch(err => {
                console.error("Failed to stop scanner", err);
            });
        }
    }

    function onScanSuccess(decodedText, decodedResult) {
        console.log(`Code matched = ${decodedText}`, decodedResult);
        
        const barcodeInput = document.getElementById('productBarcode');
        if (barcodeInput) {
            barcodeInput.value = decodedText;
            barcodeInput.dispatchEvent(new Event('input'));
            barcodeInput.dispatchEvent(new Event('change'));
        }
        
        closeScanner();
    }

    function onScanFailure(error) {
        // console.warn(`Code scan error = ${error}`);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const startScanBtn = document.getElementById('startScan');
        const closeScannerBtn = document.getElementById('closeScanner');
        const scannerModal = document.getElementById('scannerModal');

        if (startScanBtn) {
            startScanBtn.addEventListener('click', openScanner);
        }

        if (closeScannerBtn) {
            closeScannerBtn.addEventListener('click', closeScanner);
        }

        if (scannerModal) {
            scannerModal.addEventListener('click', function(e) {
                if (e.target === scannerModal) {
                    closeScanner();
                }
            });
        }
    });
</script>
@endpush

@endsection
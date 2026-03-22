@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Edit Produk & Resep - ' . $product->name)

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('products-hpp.index') }}" class="text-gray-400 hover:text-cuan-green transition-colors font-medium tracking-tight">Produk & Resep</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('products-hpp.show', $product->id) }}" class="text-gray-400 hover:text-cuan-green font-medium tracking-tight truncate max-w-[140px] md:max-w-xs">{{ $product->name }}</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Edit</span>
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
</style>
@endpush

@section('content')
<main class="flex-grow py-10 px-4 md:px-8">
    <div class="max-w-7xl mx-auto space-y-8">

        {{-- ALERT ERROR --}}
        @if($errors->any())
            <div class="mb-8 bg-red-50 border-2 border-red-100 p-6 rounded-[2rem] animate-pulse-subtle" role="alert">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-red-500 rounded-2xl flex items-center justify-center shrink-0 shadow-lg shadow-red-500/20">
                        <i class="fas fa-exclamation-triangle text-white"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-black text-red-900 uppercase tracking-widest text-sm mb-2">Peringatan Input</h3>
                        <ul class="space-y-1">
                            @foreach($errors->all() as $error)
                                <li class="text-sm text-red-700 font-medium">· {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Edit Produk & Resep
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Perbarui informasi resep, harga jual, dan strategi margin untuk memaksimalkan keuntungan produk Anda.
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('products-hpp.show', $product->id) }}" class="inline-flex items-center gap-2 rounded-xl bg-white border-2 border-gray-100 px-5 py-3 text-sm font-black text-gray-600 hover:bg-gray-50 transition-all active:scale-95">
                    <i class="fas fa-eye text-xs"></i> <span>Lihat Detail</span>
                </a>
            </div>
        </section>

        {{-- QUICK NAV --}}
        <nav class="flex flex-wrap gap-2 overflow-x-auto pb-2 scrollbar-hide">
            <a href="#section-basic" class="px-6 py-3 rounded-xl bg-white border-2 border-gray-50 text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-cuan-green hover:border-cuan-green/20 transition-all flex items-center shrink-0 shadow-sm">
                Data Dasar
            </a>
            <a href="#section-recipe" class="px-6 py-3 rounded-xl bg-white border-2 border-gray-50 text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-cuan-green hover:border-cuan-green/20 transition-all flex items-center shrink-0 shadow-sm">
                Resep & Produksi
            </a>
            <a href="#section-biaya" class="px-6 py-3 rounded-xl bg-white border-2 border-gray-50 text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-cuan-green hover:border-cuan-green/20 transition-all flex items-center shrink-0 shadow-sm">
                Biaya & Margin
            </a>
            <a href="#section-pricing" class="px-6 py-3 rounded-xl bg-white border-2 border-gray-50 text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-cuan-green hover:border-cuan-green/20 transition-all flex items-center shrink-0 shadow-sm">
                Price Strategy
            </a>
        </nav>

        <form action="{{ route('products-hpp.update', $product->id) }}" 
              method="POST" 
              enctype="multipart/form-data"
              id="productForm">
            @csrf
            @method('PUT')
            <div class="space-y-10">

                    {{-- SECTION 1: DATA PRODUK --}}
                    <x-card-container id="section-basic" class="scroll-mt-24">
                        <div class="p-8 border-b border-gray-50">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-cuan-green mb-2 block">Identity</span>
                            <h2 class="text-xl font-black text-gray-900 tracking-tight">Data <span class="text-gray-400">Dasar Produk</span></h2>
                        </div>

                        <div class="p-8 space-y-8">
                            @php
                                $currentType = 'direct';
                                if ($product->is_stock) {
                                    if ($product->defaultRecipe) {
                                        $currentType = 'stock';
                                    } else {
                                        $currentType = 'ready';
                                    }
                                }
                            @endphp

                            <div class="space-y-4">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Jenis / Tipe Unit Produk</label>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <!-- Option 1: Produk Masak Langsung -->
                                    <label class="group relative flex flex-col p-6 rounded-[2rem] border-2 transition-all cursor-pointer bg-gray-50/50 border-gray-100 hover:border-cuan-green/20 peer-checked:border-cuan-green {{ old('product_type', $currentType) == 'direct' ? 'border-cuan-green bg-cuan-green/[0.02]' : '' }} product-type-option" data-type="direct">
                                        <input type="radio" name="product_type" value="direct" class="sr-only" {{ old('product_type', $currentType) == 'direct' ? 'checked' : '' }}>
                                        <div class="flex justify-between items-start mb-4">
                                            <div class="w-12 h-12 rounded-2xl bg-white border border-gray-100 flex items-center justify-center text-gray-400 group-hover:text-cuan-green transition-colors {{ old('product_type', $currentType) == 'direct' ? 'text-cuan-green border-cuan-green/20 shadow-lg shadow-cuan-green/10' : '' }}">
                                                <i class="fas fa-utensils"></i>
                                            </div>
                                            <div class="radio-indicator w-6 h-6 rounded-full border-2 border-gray-200 flex items-center justify-center {{ old('product_type', $currentType) == 'direct' ? 'border-cuan-green bg-cuan-green' : '' }}">
                                                <div class="w-2 h-2 rounded-full bg-white"></div>
                                            </div>
                                        </div>
                                        <span class="font-black text-gray-900 tracking-tight mb-1">Masak Langsung</span>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wide leading-relaxed">Produk disajikan segar saat dipesan pembeli.</span>
                                    </label>

                                    <!-- Option 2: Produk Stok Produksi -->
                                    <label class="group relative flex flex-col p-6 rounded-[2rem] border-2 transition-all cursor-pointer bg-gray-50/50 border-gray-100 hover:border-cuan-green/20 product-type-option {{ old('product_type', $currentType) == 'stock' ? 'border-cuan-green bg-cuan-green/[0.02]' : '' }}" data-type="stock">
                                        <input type="radio" name="product_type" value="stock" class="sr-only" {{ old('product_type', $currentType) == 'stock' ? 'checked' : '' }}>
                                        <div class="flex justify-between items-start mb-4">
                                            <div class="w-12 h-12 rounded-2xl bg-white border border-gray-100 flex items-center justify-center text-gray-400 group-hover:text-cuan-green transition-colors {{ old('product_type', $currentType) == 'stock' ? 'text-cuan-green border-cuan-green/20 shadow-lg shadow-cuan-green/10' : '' }}">
                                                <i class="fas fa-boxes"></i>
                                            </div>
                                            <div class="radio-indicator w-6 h-6 rounded-full border-2 border-gray-200 flex items-center justify-center {{ old('product_type', $currentType) == 'stock' ? 'border-cuan-green bg-cuan-green' : '' }}">
                                                <div class="w-2 h-2 rounded-full bg-white"></div>
                                            </div>
                                        </div>
                                        <span class="font-black text-gray-900 tracking-tight mb-1">Stok Produksi</span>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wide leading-relaxed">Produksi massal dan disimpan sebagai stok jadi.</span>
                                    </label>

                                    <!-- Option 3: Produk Siap Jual -->
                                    <label class="group relative flex flex-col p-6 rounded-[2rem] border-2 transition-all cursor-pointer bg-gray-50/50 border-gray-100 hover:border-cuan-green/20 product-type-option {{ old('product_type', $currentType) == 'ready' ? 'border-cuan-green bg-cuan-green/[0.02]' : '' }}" data-type="ready">
                                        <input type="radio" name="product_type" value="ready" class="sr-only" {{ old('product_type', $currentType) == 'ready' ? 'checked' : '' }}>
                                        <div class="flex justify-between items-start mb-4">
                                            <div class="w-12 h-12 rounded-2xl bg-white border border-gray-100 flex items-center justify-center text-gray-400 group-hover:text-cuan-green transition-colors {{ old('product_type', $currentType) == 'ready' ? 'text-cuan-green border-cuan-green/20 shadow-lg shadow-cuan-green/10' : '' }}">
                                                <i class="fas fa-store"></i>
                                            </div>
                                            <div class="radio-indicator w-6 h-6 rounded-full border-2 border-gray-200 flex items-center justify-center {{ old('product_type', $currentType) == 'ready' ? 'border-cuan-green bg-cuan-green' : '' }}">
                                                <div class="w-2 h-2 rounded-full bg-white"></div>
                                            </div>
                                        </div>
                                        <span class="font-black text-gray-900 tracking-tight mb-1">Siap Jual</span>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wide leading-relaxed">Produk jadi dari supplier tanpa perlu resep.</span>
                                    </label>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-4">
                                    <label for="productCode" class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Kode Produk</label>
                                    <input type="text" name="code" id="productCode" value="{{ old('code', $product->code) }}" 
                                           class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-2 border-gray-100 focus:border-cuan-green focus:bg-white focus:ring-4 focus:ring-cuan-green/10 transition-all font-bold placeholder:text-gray-300"
                                           placeholder="Contoh: PRD001" required>
                                </div>

                                <div class="space-y-4">
                                    <label for="name" class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Nama Produk</label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" 
                                           class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-2 border-gray-100 focus:border-cuan-green focus:bg-white focus:ring-4 focus:ring-cuan-green/10 transition-all font-bold placeholder:text-gray-300"
                                           placeholder="Nama yang tampil di aplikasi / struk" required>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-4">
                                    <label for="productBarcode" class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Barcode</label>
                                    <div class="relative">
                                        <input type="text" name="barcode" id="productBarcode" value="{{ old('barcode', $product->barcode) }}" 
                                               class="w-full pl-6 pr-14 py-4 rounded-2xl bg-gray-50 border-2 border-gray-100 focus:border-cuan-green focus:bg-white focus:ring-4 focus:ring-cuan-green/10 transition-all font-bold placeholder:text-gray-300"
                                               placeholder="Kosongkan jika tidak ada">
                                        <button type="button" id="startScan" class="absolute right-4 top-1/2 -translate-y-1/2 w-8 h-8 rounded-lg bg-white border border-gray-100 flex items-center justify-center text-gray-400 group-hover:text-cuan-green transition-all shadow-sm active:scale-95">
                                            <i class="fas fa-barcode text-xs"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <label for="category_id" class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Kategori</label>
                                    <select name="category_id" class="select2-init w-full">
                                        <option value="">- Pilih Kategori -</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-4">
                                    <label for="supplier_id" class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Supplier</label>
                                    <select name="supplier_id" class="select2-init w-full">
                                        <option value="">- Pilih Supplier -</option>
                                        @foreach($suppliers ?? [] as $supplier)
                                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $product->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight italic">Opsional untuk barang jadi.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-4">
                                    <label for="unit_id" class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Satuan Jual (Unit)</label>
                                    <select name="unit_id" id="unit_id" class="select2-init w-full" required>
                                        <option value="">- Pilih Satuan -</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ old('unit_id', $product->unit_id) == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="space-y-4">
                                    <label for="imageInput" class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Foto Produk</label>
                                    <input type="file" name="image" id="imageInput" accept="image/*"
                                           class="w-full px-6 py-3 rounded-2xl bg-gray-50 border-2 border-gray-100 focus:border-cuan-green focus:bg-white transition-all font-bold text-sm
                                                  file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0
                                                  file:text-xs file:font-black file:uppercase file:tracking-widest file:bg-cuan-green file:text-white hover:file:bg-cuan-olive">
                                    
                                    {{-- Preview foto --}}
                                    <div id="imagePreview" class="mt-4 {{ $product->image ? '' : 'hidden' }}">
                                        <div class="relative inline-block group">
                                            <img id="previewImg"
                                                 src="{{ $product->image ? asset('storage/' . $product->image) : '' }}"
                                                 alt="Preview"
                                                 class="w-32 h-32 object-cover rounded-[2rem] border-4 border-white shadow-xl group-hover:scale-105 transition-transform duration-500">
                                            <button type="button" id="removeImage"
                                                    class="absolute -top-3 -right-3 bg-red-500 text-white rounded-full w-8 h-8
                                                           flex items-center justify-center hover:bg-red-600 transition-all shadow-lg active:scale-90">
                                                <i class="fas fa-times text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <label for="description" class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Deskripsi Produk (Opsional)</label>
                                <textarea name="description" id="description" rows="3" 
                                          class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-2 border-gray-100 focus:border-cuan-green focus:bg-white focus:ring-4 focus:ring-cuan-green/10 transition-all font-bold placeholder:text-gray-300 resize-none"
                                          placeholder="Tuliskan keterangan singkat, misalnya rasa, ukuran, atau catatan lain.">{{ old('description', $product->description) }}</textarea>
                            </div>
                        </div>
                    </x-card-container>

                    {{-- SECTION 2: RESEP PRODUK --}}
                    <x-card-container id="section-recipe" class="scroll-mt-24 section-recipe-container">
                        <div class="p-8 border-b border-gray-50">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-500 mb-2 block">Culinaries</span>
                            <h2 class="text-xl font-black text-gray-900 tracking-tight">Detail <span class="text-gray-400">Resep Produksi</span></h2>
                        </div>

                        <div class="p-8 space-y-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-4">
                                    <label for="recipe_name" class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Nama Resep</label>
                                    <input type="text" name="recipe_name" id="recipe_name" 
                                           value="{{ old('recipe_name', $product->defaultRecipe->name ?? '') }}" 
                                           class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-2 border-gray-100 focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all font-bold placeholder:text-gray-300"
                                           placeholder="Contoh: Resep Kue Original" required>
                                </div>

                                <div class="space-y-4">
                                    <label for="output_quantity" class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Hasil per 1 Resep</label>
                                    <div class="relative">
                                        <input type="number" step="0.01" name="output_quantity" id="output_quantity" 
                                               value="{{ old('output_quantity', $product->defaultRecipe->output_quantity ?? 1) }}" 
                                               class="w-full pl-6 pr-16 py-4 rounded-2xl bg-gray-50 border-2 border-gray-100 focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all font-bold placeholder:text-gray-300"
                                               placeholder="1" required>
                                        <span class="absolute right-6 top-1/2 -translate-y-1/2 text-xs font-black text-gray-300 uppercase select-none yield-unit-label">{{ $product->unit->abbreviation }}</span>
                                    </div>
                                    <p class="text-xs font-bold text-gray-400 tracking-wide uppercase italic">Jumlah produk jadi dari satu kali resep.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                                <div class="space-y-4">
                                    <label for="estimated_time_minutes" class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Estimasi Waktu Produksi</label>
                                    <div class="relative">
                                        <input type="number" name="estimated_time_minutes" id="estimated_time_minutes" 
                                               value="{{ old('estimated_time_minutes', $product->defaultRecipe->estimated_time_minutes ?? '') }}" 
                                               class="w-full pl-6 pr-16 py-4 rounded-2xl bg-gray-50 border-2 border-gray-100 focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all font-bold placeholder:text-gray-300"
                                               placeholder="30">
                                        <span class="absolute right-6 top-1/2 -translate-y-1/2 text-xs font-black text-gray-300 uppercase select-none">Menit</span>
                                    </div>
                                </div>

                                <div class="bg-blue-50/50 border-2 border-blue-100/50 rounded-[2rem] p-6 flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-white rounded-2xl shadow-sm flex items-center justify-center text-blue-500">
                                            <i class="fas fa-boxes"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-black text-gray-900 uppercase tracking-wider">Produk Bisa Di-stok?</h4>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">Aktifkan jika produk ini disimpan.</p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_stock" value="1" class="sr-only peer" {{ old('is_stock', $product->is_stock) ? 'checked' : '' }}>
                                        <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-500"></div>
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <label for="instructions" class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Langkah / Cara Membuat</label>
                                <textarea name="instructions" id="instructions" rows="6" 
                                          class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-2 border-gray-100 focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all font-bold placeholder:text-gray-300 resize-none"
                                          placeholder="1. Siapkan bahan...
2. Campur dan olah...
3. Panggang / masak...">{{ old('instructions', $product->defaultRecipe->instructions ?? '') }}</textarea>
                            </div>
                        </div>
                    </x-card-container>

                    {{-- SECTION 3: BAHAN BAKU --}}
                    {{-- SECTION 3: BAHAN BAKU --}}
                    <x-card-container id="section-bahan" class="scroll-mt-24">
                        <div class="p-8 border-b border-gray-50 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-cuan-green mb-2 block">Ingredients</span>
                                <h2 class="text-xl font-black text-gray-900 tracking-tight">Komposisi <span class="text-gray-400">Bahan Baku</span></h2>
                            </div>
                            <button type="button" id="addRecipeItem"
                                    class="inline-flex items-center justify-center px-6 py-3 bg-cuan-green text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-cuan-olive transition-all active:scale-95 shadow-lg shadow-cuan-green/20">
                                <i class="fas fa-plus mr-2"></i> Tambah Bahan
                            </button>
                        </div>

                        <div class="p-8 space-y-6">
                            @php
                                $existingItems = old('recipe_items', $product->defaultRecipe->items ?? collect());
                                $itemCount = is_array($existingItems) ? count($existingItems) : $existingItems->count();
                            @endphp

                            <div id="recipeItemsContainer" class="space-y-6">
                                @if($itemCount > 0)
                                    @foreach($existingItems as $index => $item)
                                        @php
                                            $rawMaterialId = is_array($item) ? $item['raw_material_id'] : $item->raw_material_id;
                                            $quantity = is_array($item) ? $item['quantity'] : $item->quantity;
                                            $notes = is_array($item) ? ($item['notes'] ?? '') : $item->notes;
                                            $rawMaterial = \App\Models\RawMaterial::find($rawMaterialId);
                                        @endphp
                                        <div class="recipe-item group bg-gray-50/50 border-2 border-gray-100/50 rounded-[2rem] p-8 transition-all hover:bg-white hover:border-cuan-green/20">
                                            <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                                                <div class="md:col-span-12 lg:col-span-5 space-y-4">
                                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Bahan Baku</label>
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

                                                <div class="md:col-span-6 lg:col-span-2 space-y-4">
                                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Jumlah</label>
                                                    <input type="number" step="0.01" name="recipe_items[{{ $index }}][quantity]" value="{{ $quantity }}"
                                                           class="quantity-input w-full px-6 py-4 rounded-2xl bg-white border-2 border-gray-100 focus:border-cuan-green focus:ring-4 focus:ring-cuan-green/10 transition-all font-bold placeholder:text-gray-300"
                                                           placeholder="0" required>
                                                </div>

                                                <div class="md:col-span-6 lg:col-span-2 space-y-4">
                                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Satuan</label>
                                                    <input type="text" class="unit-display w-full px-6 py-4 rounded-2xl bg-gray-100/50 border-2 border-gray-100 text-gray-500 font-bold text-sm"
                                                           readonly placeholder="-" value="{{ $rawMaterial->unit->name ?? '' }}">
                                                </div>

                                                <div class="md:col-span-6 lg:col-span-2 space-y-4">
                                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Estimasi Biaya</label>
                                                    <input type="text" class="cost-display w-full px-6 py-4 rounded-2xl bg-cuan-green/[0.03] border-2 border-cuan-green/10 text-cuan-green font-black text-sm"
                                                           readonly value="Rp {{ number_format(($rawMaterial->purchase_price ?? 0) * $quantity, 0, ',', '.') }}">
                                                </div>

                                                <div class="md:col-span-1 flex items-end">
                                                    <button type="button" class="remove-item w-14 h-14 bg-red-50 text-red-500 rounded-2xl hover:bg-red-500 hover:text-white transition-all active:scale-90 flex items-center justify-center shadow-sm"
                                                            style="display: {{ $index == 0 && $itemCount == 1 ? 'none' : 'flex' }};">
                                                        <i class="fas fa-trash-alt text-lg"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-6 space-y-4">
                                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Catatan Khusus (Opsional)</label>
                                                <input type="text" name="recipe_items[{{ $index }}][notes]" value="{{ $notes }}"
                                                       class="w-full px-6 py-4 rounded-2xl bg-white border-2 border-gray-100 focus:border-cuan-green focus:ring-4 focus:ring-cuan-green/10 transition-all font-bold text-sm placeholder:text-gray-300"
                                                       placeholder="Contoh: Takaran pas, jangan dikurangi...">
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="recipe-item group bg-gray-50/50 border-2 border-gray-100/50 rounded-[2rem] p-8 transition-all hover:bg-white hover:border-cuan-green/20">
                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                                            <div class="md:col-span-12 lg:col-span-5 space-y-4">
                                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Bahan Baku</label>
                                                <select name="recipe_items[0][raw_material_id]" class="raw-material-select w-full" required>
                                                    <option value="">- Pilih Bahan -</option>
                                                    @foreach($rawMaterials as $rm)
                                                        <option value="{{ $rm->id }}" data-price="{{ $rm->purchase_price }}" data-unit="{{ $rm->unit->name ?? '' }}">
                                                            {{ $rm->name }} ({{ $rm->unit->name ?? '' }}) - Rp {{ number_format($rm->purchase_price, 0, ',', '.') }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="md:col-span-6 lg:col-span-2 space-y-4">
                                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Jumlah</label>
                                                <input type="number" step="0.01" name="recipe_items[0][quantity]"
                                                       class="quantity-input w-full px-6 py-4 rounded-2xl bg-white border-2 border-gray-100 focus:border-cuan-green focus:ring-4 focus:ring-cuan-green/10 transition-all font-bold placeholder:text-gray-300"
                                                       placeholder="0" required>
                                            </div>
                                            <div class="md:col-span-6 lg:col-span-2 space-y-4">
                                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Satuan</label>
                                                <input type="text" class="unit-display w-full px-6 py-4 rounded-2xl bg-gray-100/50 border-2 border-gray-100 text-gray-500 font-bold text-sm"
                                                       readonly placeholder="-">
                                            </div>
                                            <div class="md:col-span-6 lg:col-span-2 space-y-4">
                                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Estimasi Biaya</label>
                                                <input type="text" class="cost-display w-full px-6 py-4 rounded-2xl bg-cuan-green/[0.03] border-2 border-cuan-green/10 text-cuan-green font-black text-sm"
                                                       readonly value="Rp 0">
                                            </div>
                                            <div class="md:col-span-1 flex items-end">
                                                <button type="button" class="remove-item w-14 h-14 bg-red-50 text-red-500 rounded-2xl hover:bg-red-500 hover:text-white transition-all active:scale-90 flex items-center justify-center shadow-sm" style="display: none;">
                                                    <i class="fas fa-trash-alt text-lg"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mt-6 space-y-4">
                                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Catatan Khusus (Opsional)</label>
                                            <input type="text" name="recipe_items[0][notes]"
                                                   class="w-full px-6 py-4 rounded-2xl bg-white border-2 border-gray-100 focus:border-cuan-green focus:ring-4 focus:ring-cuan-green/10 transition-all font-bold text-sm placeholder:text-gray-300"
                                                   placeholder="Contoh: Kualitas ekspektasi...">
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-8 bg-cuan-green/[0.02] border-2 border-cuan-green/10 rounded-[2rem] p-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-cuan-green shadow-sm border border-cuan-green/10">
                                        <i class="fas fa-calculator text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-1">Total Biaya Bahan Baku</h3>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">Akumulasi otomatis dari resep di atas.</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span id="totalMaterialCost" class="text-3xl font-black text-cuan-green tracking-tight">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </x-card-container>

                    {{-- SECTION 4: BIAYA LAINNYA & RINGKASAN HPP --}}
                    <x-card-container id="section-biaya" class="scroll-mt-24">
                        <div class="p-8 border-b border-gray-50 space-y-1">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-orange-500 mb-2 block">Overhead & Summary</span>
                            <h2 class="text-xl font-black text-gray-900 tracking-tight">Kalkulasi <span class="text-gray-400">Total HPP</span></h2>
                        </div>

                        <div class="p-8 space-y-8">
                            <div class="bg-gray-50/80 border-2 border-gray-100 rounded-[2rem] p-6 flex items-start gap-4">
                                <div class="w-10 h-10 bg-white rounded-2xl flex items-center justify-center text-gray-400 shadow-sm border border-gray-100 shrink-0">
                                    <i class="fas fa-info-circle text-xs"></i>
                                </div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide leading-relaxed">
                                    Biaya tambahan mencakup gas, listrik, kemasan porsi, gaji proporsional, atau biaya variabel lainnya per satu resep.
                                </p>
                            </div>

                            @php
                                $latestHpp = $product->latestHppCalculation;
                                $additionalCostValue = old('additional_cost', $latestHpp->additional_cost ?? 0);
                            @endphp

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                <div class="space-y-6">
                                    <div class="space-y-4">
                                        <label for="additionalCostInput" class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Biaya Operasional Tambahan (Rp)</label>
                                        <div class="relative">
                                            <input type="number" step="0.01" name="additional_cost" id="additionalCostInput" value="{{ $additionalCostValue }}"
                                                   class="w-full pl-6 pr-14 py-4 rounded-2xl bg-gray-50 border-2 border-gray-100 focus:border-cuan-green focus:bg-white focus:ring-4 focus:ring-cuan-green/10 transition-all font-black text-lg placeholder:text-gray-300"
                                                   placeholder="0">
                                            <div class="absolute right-6 top-1/2 -translate-y-1/2 text-xs font-black text-gray-300 uppercase select-none">IDR</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gradient-to-br from-cuan-green/10 to-transparent border-2 border-cuan-green/10 rounded-[2.5rem] p-8 space-y-6">
                                    <h3 class="text-xs font-black text-gray-900 uppercase tracking-[0.2em] mb-4">Ringkasan Kalkulasi HPP</h3>
                                    <div class="space-y-4">
                                        <div class="flex justify-between items-center group">
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Biaya Bahan Baku</span>
                                            <span id="summaryMaterialCost" class="text-sm font-black text-gray-700 tracking-tight">Rp 0</span>
                                        </div>
                                        <div class="flex justify-between items-center group">
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Biaya Tambahan</span>
                                            <span id="summaryAdditionalCost" class="text-sm font-black text-gray-700 tracking-tight">Rp 0</span>
                                        </div>
                                        <div class="h-px bg-gray-100"></div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-[10px] font-black text-gray-900 uppercase tracking-widest">Total HPP per Batch</span>
                                            <span id="summaryTotalHpp" class="text-xl font-black text-cuan-green tracking-tight">Rp 0</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Output per Batch</span>
                                            <span id="summaryOutputQty" class="text-sm font-black text-gray-700 tracking-tight">1</span>
                                        </div>
                                        
                                        <div class="mt-4 pt-4 border-t-2 border-cuan-green/10 flex justify-between items-center">
                                            <div class="space-y-1">
                                                <span class="text-[10px] font-black text-cuan-green uppercase tracking-widest block">HPP per Unit</span>
                                                <p class="text-[8px] font-bold text-gray-400 uppercase">Dasar perhitungan margin</p>
                                            </div>
                                            <span id="summaryHppPerUnit" class="text-3xl font-black text-cuan-green tracking-tighter">Rp 0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-card-container>

                    {{-- SECTION 5: HARGA JUAL & STOK --}}
                    <x-card-container id="section-pricing" class="scroll-mt-24">
                        <div class="p-8 border-b border-gray-50 space-y-1">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-cuan-green mb-2 block">Commercials</span>
                            <h2 class="text-xl font-black text-gray-900 tracking-tight">Penentuan <span class="text-gray-400">Harga & Margin</span></h2>
                        </div>

                        <div class="p-8 space-y-8">
                            {{-- Input HPP Manual (Hanya untuk Produk Siap Jual) --}}
                            <div id="readyToSellFields" class="bg-orange-50/50 border-2 border-orange-100 rounded-[2rem] p-8 hidden">
                                <div class="flex items-start gap-4 mb-6">
                                    <div class="w-10 h-10 bg-white rounded-2xl flex items-center justify-center text-orange-500 shadow-sm border border-orange-100 shrink-0">
                                        <i class="fas fa-layer-group text-xs"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-black text-orange-800 uppercase tracking-widest mb-1">Produk Siap Jual</h4>
                                        <p class="text-[10px] font-bold text-orange-600/70 uppercase">Masukkan modal beli per unit karena tanpa resep produksi.</p>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <label for="manualHppInput" class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">HPP (Modal) per Unit <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input type="number" step="0.01" name="manual_hpp" id="manualHppInput" value="{{ old('manual_hpp', $product->hpp) }}"
                                               class="w-full pl-6 pr-14 py-4 rounded-2xl bg-white border-2 border-orange-100 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 transition-all font-black text-lg placeholder:text-gray-300"
                                               placeholder="0">
                                        <div class="absolute right-6 top-1/2 -translate-y-1/2 text-xs font-black text-orange-300 uppercase select-none">IDR</div>
                                    </div>
                                </div>
                            </div>

                            {{-- HPP per unit highlight --}}
                            <div class="bg-cuan-green/[0.03] border-2 border-cuan-green/10 rounded-[2rem] p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-cuan-green border border-cuan-green/10 shadow-sm">
                                        <i class="fas fa-tag text-xs"></i>
                                    </div>
                                    <span class="text-xs font-black text-gray-500 uppercase tracking-widest">HPP Real-time per Unit</span>
                                </div>
                                <span id="finalHppPerUnit" class="text-2xl font-black text-cuan-green tracking-tighter">Rp 0</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                                {{-- Harga jual --}}
                                <div class="space-y-4">
                                    <label for="sellingPriceInput" class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Harga Jual Normal <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input type="number" step="0.01" name="selling_price" id="sellingPriceInput" value="{{ old('selling_price', $product->selling_price) }}"
                                               class="w-full pl-6 pr-14 py-4 rounded-2xl bg-gray-50 border-2 border-gray-100 focus:border-cuan-green focus:bg-white focus:ring-4 focus:ring-cuan-green/10 transition-all font-black text-lg placeholder:text-gray-300" required>
                                        <div class="absolute right-6 top-1/2 -translate-y-1/2 text-xs font-black text-gray-300 uppercase">IDR</div>
                                    </div>
                                </div>

                                {{-- Harga reseller --}}
                                <div class="space-y-4">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Harga Reseller</label>
                                    <div class="relative">
                                        <input type="number" step="0.01" name="reseller_price" value="{{ old('reseller_price', $product->reseller_price) }}"
                                               class="w-full pl-6 pr-14 py-4 rounded-2xl bg-gray-50 border-2 border-gray-100 focus:border-cuan-green focus:bg-white focus:ring-4 focus:ring-cuan-green/10 transition-all font-black text-lg placeholder:text-gray-300">
                                        <div class="absolute right-6 top-1/2 -translate-y-1/2 text-xs font-black text-gray-300 uppercase">IDR</div>
                                    </div>
                                </div>

                                {{-- Harga promo --}}
                                <div class="space-y-4">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Harga Promo</label>
                                    <div class="relative">
                                        <input type="number" step="0.01" name="promo_price" value="{{ old('promo_price', $product->promo_price) }}"
                                               class="w-full pl-6 pr-14 py-4 rounded-2xl bg-gray-50 border-2 border-gray-100 focus:border-cuan-green focus:bg-white focus:ring-4 focus:ring-cuan-green/10 transition-all font-black text-lg placeholder:text-gray-300">
                                        <div class="absolute right-6 top-1/2 -translate-y-1/2 text-xs font-black text-gray-300 uppercase">IDR</div>
                                    </div>
                                </div>

                                {{-- Stok minimum --}}
                                <div class="space-y-4">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Stok Minimum</label>
                                    <input type="number" step="0.01" name="min_stock" value="{{ old('min_stock', $product->min_stock) }}"
                                           class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-2 border-gray-100 focus:border-cuan-green focus:bg-white focus:ring-4 focus:ring-cuan-green/10 transition-all font-bold placeholder:text-gray-300">
                                </div>

                                {{-- Masa simpan --}}
                                <div class="space-y-4">
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Masa Simpan (Hari)</label>
                                    <input type="number" name="shelf_life_days" value="{{ old('shelf_life_days', $product->shelf_life_days) }}"
                                           class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-2 border-gray-100 focus:border-cuan-green focus:bg-white focus:ring-4 focus:ring-cuan-green/10 transition-all font-bold placeholder:text-gray-300">
                                </div>
                            </div>

                            {{-- Analisis margin --}}
                            <div class="bg-gray-900 border-2 border-gray-800 rounded-[2.5rem] p-8 space-y-8 relative overflow-hidden">
                                <div class="absolute top-0 right-0 p-8 opacity-10">
                                    <i class="fas fa-chart-line text-8xl text-white"></i>
                                </div>
                                <div class="relative z-10">
                                    <h3 class="text-xs font-black text-white uppercase tracking-[0.2em] mb-8">Analisis Margin & Keuntungan</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                        <div class="space-y-2">
                                            <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest block">HPP Satuan</span>
                                            <p id="marginHpp" class="text-xl font-black text-white tracking-tight">Rp 0</p>
                                        </div>
                                        <div class="space-y-2">
                                            <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest block">Harga Jual</span>
                                            <p id="marginSellingPrice" class="text-xl font-black text-white tracking-tight">Rp 0</p>
                                        </div>
                                        <div class="space-y-2">
                                            <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest block">Laba Kotor</span>
                                            <p id="marginProfit" class="text-xl font-black text-cuan-green tracking-tight">Rp 0</p>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-8 pt-8 border-t border-gray-800 flex items-center justify-between">
                                        <div class="space-y-1">
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Persentase Margin</span>
                                            <p class="text-[8px] font-bold text-gray-600 uppercase">Efisiensi keuntungan produk ini</p>
                                        </div>
                                        <span id="marginPercent" class="text-5xl font-black text-cuan-green tracking-tighter">0%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-card-container>

                </div>

                {{-- FOOTER: TOMBOL AKSI --}}
                <div class="px-8 py-8 bg-gray-50 border-t border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <a href="{{ route('products-hpp.index') }}"
                       class="inline-flex items-center justify-center px-8 py-4 border-2 border-gray-200 rounded-xl text-xs font-black text-gray-400 uppercase tracking-widest hover:bg-white hover:border-gray-300 hover:text-gray-600 transition-all active:scale-95">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center px-10 py-4 bg-cuan-green text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-cuan-olive transition-all active:scale-95 shadow-lg shadow-cuan-green/20">
                        <i class="fas fa-check-circle mr-2 text-base"></i> Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>

    {{-- Modal Scanner --}}
    <div id="scannerModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/90 backdrop-blur-sm hidden overflow-y-auto overflow-x-hidden transition-all duration-300">
        <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg mx-4 relative overflow-hidden border border-gray-100">
            <div class="p-8 border-b border-gray-50 text-center relative">
                <button type="button" id="closeScanner" class="absolute right-8 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center rounded-2xl bg-gray-50 text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all active:scale-90">
                    <i class="fas fa-times"></i>
                </button>
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-cuan-green mb-1 block">Smart Scan</span>
                <h3 class="text-xl font-black text-gray-900 tracking-tight">Scan <span class="text-gray-400">Barcode Produk</span></h3>
            </div>
            <div class="p-8">
                <div id="reader" class="w-full bg-gray-900 rounded-[2rem] overflow-hidden aspect-video shadow-inner"></div>
                <div class="mt-6 flex flex-col items-center gap-2">
                    <div class="w-12 h-1.5 bg-gray-100 rounded-full mb-2"></div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center leading-relaxed">Posisikan barcode di dalam area kamera untuk deteksi otomatis</p>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection


@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
{{-- HTML5-QRCode Library --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
let recipeItemIndex = {{ $itemCount }};
let totalMaterialCostValue = 0;

const imageInput = document.getElementById('imageInput');
const imagePreview = document.getElementById('imagePreview');
const previewImg = document.getElementById('previewImg');
const removeImageBtn = document.getElementById('removeImage');

// Product Type Switching Logic
$(document).ready(function() {
    const isStockCheckbox = document.querySelector('input[name="is_stock"]');
    const isStockToggleContainer = isStockCheckbox ? isStockCheckbox.closest('.bg-blue-50') : null;
    
    $('input[name="product_type"]').on('change', function() {
        const type = $(this).val();
        const recipeSection = document.getElementById('section-recipe');
        const bahanSection = document.getElementById('section-bahan');
        const biayaSection = document.getElementById('section-biaya');
        const readyToSellFields = document.getElementById('readyToSellFields');
        const finalHppSection = document.querySelector('#section-pricing .bg-green-50'); // Modal HPP highlight
        const marginHppRow = document.querySelector('#marginHpp').closest('div');
        
        // Navigation links
        const navLinks = document.querySelectorAll('nav a');

        // Auto-set is_stock
        if (type === 'direct') {
            if (isStockCheckbox) isStockCheckbox.checked = false;
            if (isStockToggleContainer) isStockToggleContainer.classList.add('hidden');
        } else {
            if (isStockCheckbox) isStockCheckbox.checked = true;
            if (type === 'stock') {
                if (isStockToggleContainer) isStockToggleContainer.classList.remove('hidden');
            } else {
                if (isStockToggleContainer) isStockToggleContainer.classList.add('hidden');
            }
        }

        if (type === 'ready') {
            if (recipeSection) recipeSection.classList.add('hidden');
            if (bahanSection) bahanSection.classList.add('hidden');
            if (biayaSection) biayaSection.classList.add('hidden');
            if (readyToSellFields) readyToSellFields.classList.remove('hidden');
            if (finalHppSection) finalHppSection.classList.add('hidden');
            if (marginHppRow) marginHppRow.classList.add('hidden');
            
            navLinks.forEach(link => {
                if (link.href.includes('recipe') || link.href.includes('bahan') || link.href.includes('biaya')) {
                    link.classList.add('hidden');
                }
            });

            document.getElementById('manualHppInput').setAttribute('required', 'required');
        } else {
            if (recipeSection) recipeSection.classList.remove('hidden');
            if (bahanSection) bahanSection.classList.remove('hidden');
            if (biayaSection) biayaSection.classList.remove('hidden');
            if (readyToSellFields) readyToSellFields.classList.add('hidden');
            if (finalHppSection) finalHppSection.classList.remove('hidden');
            if (marginHppRow) marginHppRow.classList.remove('hidden');

            navLinks.forEach(link => {
                link.classList.remove('hidden');
            });

            document.getElementById('manualHppInput').removeAttribute('required');
        }
        
        calculateMargin();
    });

    // Trigger initial state
    $('input[name="product_type"]:checked').trigger('change');
    
    // Manual HPP input listener
    $('#manualHppInput').on('input', calculateMargin);
});

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
    // Global Select2 initialization for standard selects
    $('.select2-init').select2({
        theme: 'default',
        width: '100%',
        minimumResultsForSearch: 10
    });

    // Ingredient selection with search
    $('.raw-material-select').select2({
        theme: 'default',
        width: '100%',
        placeholder: '- Pilih Bahan -',
        dropdownParent: $('#section-bahan')
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
    const manualHppInput = document.getElementById('manualHppInput');
    const manualHpp = manualHppInput ? parseFloat(manualHppInput.value || 0) : 0;
    const productType = $('input[name="product_type"]:checked').val();
    
    let hpp = window.hppPerUnitValue || 0;
    if (productType === 'ready') {
        hpp = manualHpp;
    }
    
    const profit = sellingPrice - hpp;
    const marginPercent = hpp > 0 ? ((profit / hpp) * 100) : 0;

    const setText = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
    };

    setText('marginSellingPrice', 'Rp ' + sellingPrice.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0}));
    setText('marginProfit', 'Rp ' + profit.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0}));
    setText('marginHpp', 'Rp ' + hpp.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0}));
    setText('summaryHppPerUnit', 'Rp ' + hpp.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0}));
    setText('finalHppPerUnit', 'Rp ' + hpp.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0}));

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

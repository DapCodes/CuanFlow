@extends('layouts.app')

@section('title', 'Tambah Bahan Baku - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('raw-materials.index') }}" class="text-gray-500 hover:text-gray-700">Stok Bahan Baku</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Tambah Bahan Baku</span>
</li>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Custom Select2 Styling to match Tailwind Inputs */
    .select2-container .select2-selection--single {
        height: 42px !important; /* Match standard Tailwind input height */
        border: 1px solid #d1d5db !important; /* Gray-300 */
        border-radius: 0.5rem !important; /* Rounded-lg */
        display: flex !important;
        align-items: center !important;
        padding-left: 0.5rem; /* Match input padding */
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal !important;
        color: #374151 !important; /* Gray-700 */
        padding-left: 0.5rem !important;
        padding-right: 2rem !important; /* Space for arrow */
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
        width: 30px !important;
        right: 1px !important;
    }

    /* Focus State */
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #f97316 !important; /* Orange-500 */
        box-shadow: 0 0 0 2px rgba(249, 115, 22, 0.2) !important; /* Ring-orange-500 with opacity */
        outline: none !important;
    }

    /* Dropdown Styling */
    .select2-dropdown {
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        overflow: hidden !important;
        z-index: 50 !important;
    }

    .select2-results__option {
        padding: 0.5rem 1rem !important;
        font-size: 0.875rem !important; /* Text-sm */
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #f97316 !important; /* Orange-500 */
        color: white !important;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #fff7ed !important; /* Orange-50 */
        color: #9a3412 !important; /* Orange-800 */
    }
</style>
@endpush

@section('content')
<main class="flex-grow py-8 px-4">
    <div class="max-w-7xl mx-auto">
        
        <x-card-container>
            <!-- Header -->
            <div class="bg-gradient-to-br from-orange-50 to-red-50 p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                            <!-- <i class="fas fa-plus-circle text-red-400 mr-3"></i> -->
                            Tambah Bahan Baku Baru
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Isi formulir di bawah untuk menambahkan bahan baku</p>
                    </div>
                    <a href="{{ route('raw-materials.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-all duration-200 shadow-md">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('raw-materials.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf

                <div class="space-y-6">
                    <!-- Informasi Dasar -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-info-circle text-orange-500 mr-2"></i>
                            Informasi Dasar
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Code -->
                            <div>
                                <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Kode Bahan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="code" 
                                       id="code" 
                                       value="{{ old('code') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('code') border-red-500 @enderror" 
                                       required>
                                @error('code')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Barcode -->
                            <div>
                                <label for="barcode" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Barcode
                                </label>
                                <input type="text" 
                                       name="barcode" 
                                       id="barcode" 
                                       value="{{ old('barcode') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('barcode') border-red-500 @enderror">
                                @error('barcode')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Name -->
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Nama Bahan Baku <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       value="{{ old('name') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('name') border-red-500 @enderror" 
                                       required>
                                @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Category -->
                            <div>
                                <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Kategori
                                </label>
                                <select name="category_id" 
                                        id="category_id" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 select2 @error('category_id') border-red-500 @enderror">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Unit -->
                            <div>
                                <label for="unit_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Satuan <span class="text-red-500">*</span>
                                </label>
                                <select name="unit_id" 
                                        id="unit_id" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 select2 @error('unit_id') border-red-500 @enderror" 
                                        required>
                                    <option value="">Pilih Satuan</option>
                                    @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('unit_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Supplier & Harga -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-shopping-cart text-orange-500 mr-2"></i>
                            Supplier & Harga
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Supplier -->
                            <div>
                                <label for="supplier_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Supplier
                                </label>
                                <select name="supplier_id" 
                                        id="supplier_id" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 select2 @error('supplier_id') border-red-500 @enderror">
                                    <option value="">Pilih Supplier</option>
                                    @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Purchase Price -->
                            <div>
                                <label for="purchase_price" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Harga Beli <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                                    <input type="number" 
                                           name="purchase_price" 
                                           id="purchase_price" 
                                           value="{{ old('purchase_price', 0) }}"
                                           step="0.01"
                                           class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('purchase_price') border-red-500 @enderror" 
                                           required>
                                </div>
                                @error('purchase_price')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Stok & Masa Simpan -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-warehouse text-orange-500 mr-2"></i>
                            Stok & Masa Simpan
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Min Stock -->
                            <div>
                                <label for="min_stock" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Minimum Stok <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       name="min_stock" 
                                       id="min_stock" 
                                       value="{{ old('min_stock', 0) }}"
                                       step="0.01"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('min_stock') border-red-500 @enderror" 
                                       required>
                                @error('min_stock')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Shelf Life -->
                            <div>
                                <label for="shelf_life_days" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Masa Simpan (Hari)
                                </label>
                                <input type="number" 
                                       name="shelf_life_days" 
                                       id="shelf_life_days" 
                                       value="{{ old('shelf_life_days') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('shelf_life_days') border-red-500 @enderror">
                                @error('shelf_life_days')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Gambar & Deskripsi -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-image text-orange-500 mr-2"></i>
                            Gambar & Deskripsi
                        </h3>
                        
                        <div class="space-y-6">
                            <!-- Image -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Gambar Bahan Baku
                                </label>
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0">
                                        <div id="image-preview" class="w-32 h-32 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center bg-gray-50 overflow-hidden">
                                            <i class="fas fa-image text-4xl text-gray-300"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <input type="file" 
                                               name="image" 
                                               id="image" 
                                               accept="image/*"
                                               class="hidden">
                                        <label for="image" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-upload mr-2"></i>
                                            Pilih Gambar
                                        </label>
                                        <button type="button" id="remove-image" class="ml-2 inline-flex items-center px-4 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors hidden">
                                            <i class="fas fa-trash mr-2"></i>
                                            Hapus
                                        </button>
                                        <p class="text-xs text-gray-500 mt-2">Format: JPG, PNG (Max: 2MB)</p>
                                        @error('image')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Deskripsi
                                </label>
                                <textarea name="description" 
                                          id="description" 
                                          rows="4"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                                @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-toggle-on text-orange-500 mr-2"></i>
                            Status
                        </h3>
                        
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="is_active" 
                                   id="is_active" 
                                   value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500">
                            <label for="is_active" class="ml-2 text-sm font-medium text-gray-900">
                                Aktifkan bahan baku ini
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('raw-materials.index') }}" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-orange-400 to-red-500 text-white rounded-lg font-semibold hover:from-orange-500 hover:to-red-600 transition-all shadow-md">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Bahan Baku
                    </button>
                </div>
            </form>
        </x-card-container>

    </div>
</main>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'default',
        width: '100%'
    });

    // Image Preview Handler
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    const removeImageBtn = document.getElementById('remove-image');
    let currentImageFile = null;

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file terlalu besar. Maksimal 2MB');
                imageInput.value = '';
                return;
            }

            // Validate file type
            if (!file.type.match('image.*')) {
                alert('File harus berupa gambar');
                imageInput.value = '';
                return;
            }

            currentImageFile = file;
            const reader = new FileReader();

            reader.onload = function(e) {
                imagePreview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                removeImageBtn.classList.remove('hidden');
            };

            reader.readAsDataURL(file);
        }
    });

    removeImageBtn.addEventListener('click', function() {
        imageInput.value = '';
        currentImageFile = null;
        imagePreview.innerHTML = '<i class="fas fa-image text-4xl text-gray-300"></i>';
        removeImageBtn.classList.add('hidden');
    });
});
</script>
@endpush
@endsection
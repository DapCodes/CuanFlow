@extends('layouts.app')

@section('title', 'Tambah Bahan Baku - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('raw-materials.index') }}" class="text-gray-500 hover:text-red-600 transition-colors">Bahan Baku</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Tambah Baru</span>
</li>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Custom Select2 Styling to match Tailwind Inputs */
    .select2-container .select2-selection--single {
        height: 42px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
        display: flex !important;
        align-items: center !important;
        padding-left: 0.5rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal !important;
        color: #374151 !important;
        padding-left: 0.5rem !important;
        padding-right: 2rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
        width: 30px !important;
        right: 1px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #ef4444 !important; /* Red-500 */
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2) !important;
        outline: none !important;
    }
    .select2-dropdown {
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        overflow: hidden !important;
        z-index: 50 !important;
    }
    .select2-results__option {
        padding: 0.5rem 1rem !important;
        font-size: 0.875rem !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #ef4444 !important;
        color: white !important;
    }
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #fef2f2 !important;
        color: #991b1b !important;
    }
</style>
@endpush

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">
        
        {{-- Header Section --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-500 border border-red-100">
                        <i class="fas fa-plus text-sm"></i>
                    </span>
                    <span>Tambah Bahan Baku</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Isi formulir lengkap untuk menambahkan bahan baku baru ke inventaris.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('raw-materials.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-sm transition-all">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </section>

        {{-- Form Container --}}
        <form action="{{ route('raw-materials.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Left Column: Main Form --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- Basic Info --}}
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-info-circle text-gray-400"></i> Informasi Dasar
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Kode Bahan <span class="text-red-500">*</span></label>
                                <input type="text" name="code" id="code" value="{{ old('code') }}" required
                                    class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm shadow-sm py-2.5">
                                @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="barcode" class="block text-sm font-medium text-gray-700 mb-1">Barcode</label>
                                <input type="text" name="barcode" id="barcode" value="{{ old('barcode') }}"
                                    class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm shadow-sm py-2.5">
                                @error('barcode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Bahan Baku <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                    class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm shadow-sm py-2.5">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                <select name="category_id" id="category_id" class="select2 w-full">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="unit_id" class="block text-sm font-medium text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                                <select name="unit_id" id="unit_id" class="select2 w-full" required>
                                    <option value="">Pilih Satuan</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                                @error('unit_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Supplier & Price --}}
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-tag text-gray-400"></i> Harga & Supplier
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                                <select name="supplier_id" id="supplier_id" class="select2 w-full">
                                    <option value="">Pilih Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                                @error('supplier_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-1">Harga Beli (Rp) <span class="text-red-500">*</span></label>
                                <div class="relative rounded-lg shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', 0) }}" required step="0.01"
                                        class="w-full rounded-lg border-gray-300 pl-10 focus:border-red-500 focus:ring-red-500 text-sm shadow-sm py-2.5">
                                </div>
                                @error('purchase_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Stock & Description --}}
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-clipboard-list text-gray-400"></i> Detail Tambahan
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                             <div>
                                <label for="min_stock" class="block text-sm font-medium text-gray-700 mb-1">Minimum Stok <span class="text-red-500">*</span></label>
                                <input type="number" name="min_stock" id="min_stock" value="{{ old('min_stock', 0) }}" required step="0.01"
                                    class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm shadow-sm py-2.5">
                                @error('min_stock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                             </div>
                             <div>
                                <label for="shelf_life_days" class="block text-sm font-medium text-gray-700 mb-1">Masa Simpan (Hari)</label>
                                <input type="number" name="shelf_life_days" id="shelf_life_days" value="{{ old('shelf_life_days') }}" placeholder="Contoh: 30"
                                    class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm shadow-sm py-2.5">
                                @error('shelf_life_days') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                             </div>
                             <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                <textarea name="description" id="description" rows="3"
                                    class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm shadow-sm">{{ old('description') }}</textarea>
                                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                             </div>
                        </div>
                    </div>

                </div>

                {{-- Right Column: Image & Status --}}
                <div class="lg:col-span-1 space-y-6">
                    
                    {{-- Status Card --}}
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                         <h3 class="text-sm font-semibold text-gray-900 mb-4 uppercase">Status</h3>
                         <div class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg bg-gray-50">
                             <div class="flex items-center h-5">
                                <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                    class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                             </div>
                             <div class="text-sm">
                                <label for="is_active" class="font-medium text-gray-700">Aktif</label>
                                <p class="text-xs text-gray-500">Bahan baku ini dapat digunakan dalam transaksi.</p>
                             </div>
                         </div>
                    </div>

                    {{-- Image Card --}}
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4 uppercase">Gambar Produk</h3>
                        <div class="space-y-4">
                            <div id="image-preview" class="w-full aspect-square bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center overflow-hidden">
                                <div class="text-center text-gray-400">
                                    <i class="fas fa-image text-3xl mb-2"></i>
                                    <p class="text-xs">Preview Gambar</p>
                                </div>
                            </div>
                            
                            <input type="file" name="image" id="image" accept="image/*" class="hidden">
                            <div class="flex flex-col gap-2">
                                <label for="image" class="w-full inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer shadow-sm transition-all text-center">
                                    <i class="fas fa-upload mr-2"></i> Pilih File
                                </label>
                                <button type="button" id="remove-image" class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-50 border border-transparent rounded-lg text-sm font-medium text-red-700 hover:bg-red-100 hidden transition-all">
                                    <i class="fas fa-trash mr-2"></i> Hapus
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 text-center">Format: JPG, PNG (Max. 2MB)</p>
                            @error('image') <p class="text-red-500 text-xs mt-1 text-center">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="pt-4">
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-red-600 border border-transparent rounded-lg text-sm font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-lg transition-all">
                            <i class="fas fa-save mr-2"></i> Simpan Bahan Baku
                        </button>
                    </div>

                </div>
            </div>
        </form>

    </div>
</main>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({
        theme: 'default',
        width: '100%'
    });

    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    const removeImageBtn = document.getElementById('remove-image');
    const defaultPreviewContent = `<div class="text-center text-gray-400"><i class="fas fa-image text-3xl mb-2"></i><p class="text-xs">Preview Gambar</p></div>`;

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file terlalu besar. Maksimal 2MB');
                this.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                removeImageBtn.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });

    removeImageBtn.addEventListener('click', function() {
        imageInput.value = '';
        imagePreview.innerHTML = defaultPreviewContent;
        this.classList.add('hidden');
    });
});
</script>
@endpush
@endsection
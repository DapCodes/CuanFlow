@extends('layouts.app')

@section('title', 'Edit Bahan Baku - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

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
    <span class="text-gray-900 font-medium">Edit Bahan Baku</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">
        
        {{-- Header Section --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-orange-50 text-orange-500 border border-orange-100">
                        <i class="fas fa-edit text-sm"></i>
                    </span>
                    <span>Edit Bahan Baku</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Perbarui informasi bahan baku: <span class="font-semibold">{{ $rawMaterial->name }}</span>
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('raw-materials.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 shadow-sm transition-all">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </section>

        {{-- Form Container --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            @if ($errors->any())
                <div class="p-4 bg-red-50 border-b border-red-100 rounded-t-xl">
                    <div class="flex items-center gap-2 text-red-700 font-medium mb-1">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Terdapat kesalahan pada input:</span>
                    </div>
                    <ul class="list-disc list-inside text-sm text-red-600 ml-6">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('raw-materials.update', $rawMaterial) }}" method="POST" enctype="multipart/form-data" class="px-4 md:px-6 py-6 space-y-8">
                @csrf
                @method('PUT')

                {{-- Informasi Dasar --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <span>Informasi Dasar</span>
                        </h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Code -->
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Kode Bahan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="code" 
                                   id="code" 
                                   value="{{ old('code', $rawMaterial->code) }}"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('code') border-red-500 @enderror" 
                                   required>
                            @error('code')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Barcode -->
                        <div>
                            <label for="barcode" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Barcode
                            </label>
                            <div class="flex gap-2">
                                <input type="text" 
                                       name="barcode" 
                                       id="barcode" 
                                       value="{{ old('barcode', $rawMaterial->barcode) }}"
                                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('barcode') border-red-500 @enderror">
                                <button type="button" id="startScan"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex-shrink-0"
                                        title="Scan Barcode">
                                    <i class="fas fa-qrcode"></i>
                                </button>
                            </div>
                            @error('barcode')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Name -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Nama Bahan Baku <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $rawMaterial->name) }}"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('name') border-red-500 @enderror" 
                                   required>
                            @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Kategori
                            </label>
                            <select name="category_id" 
                                    id="category_id" 
                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 select2 @error('category_id') border-red-500 @enderror">
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $rawMaterial->category_id) == $category->id ? 'selected' : '' }}>
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
                            <label for="unit_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Satuan <span class="text-red-500">*</span>
                            </label>
                            <select name="unit_id" 
                                    id="unit_id" 
                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 select2 @error('unit_id') border-red-500 @enderror" 
                                    required>
                                <option value="">Pilih Satuan</option>
                                @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id', $rawMaterial->unit_id) == $unit->id ? 'selected' : '' }}>
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

                {{-- Supplier & Harga --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <span>Supplier & Harga</span>
                        </h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Supplier -->
                        <div>
                            <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Supplier
                            </label>
                            <select name="supplier_id" 
                                    id="supplier_id" 
                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 select2 @error('supplier_id') border-red-500 @enderror">
                                <option value="">Pilih Supplier</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id', $rawMaterial->supplier_id) == $supplier->id ? 'selected' : '' }}>
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
                            <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Harga Beli <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                                <input type="number" 
                                       name="purchase_price" 
                                       id="purchase_price" 
                                       value="{{ old('purchase_price', $rawMaterial->purchase_price) }}"
                                       step="0.01"
                                       class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('purchase_price') border-red-500 @enderror" 
                                       required>
                            </div>
                            @error('purchase_price')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Stok & Masa Simpan --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <span>Stok & Detail</span>
                        </h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Min Stock -->
                        <div>
                            <label for="min_stock" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Minimum Stok <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   name="min_stock" 
                                   id="min_stock" 
                                   value="{{ old('min_stock', $rawMaterial->min_stock) }}"
                                   step="0.01"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('min_stock') border-red-500 @enderror" 
                                   required>
                            @error('min_stock')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Shelf Life -->
                        <div>
                            <label for="shelf_life_days" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Masa Simpan (Hari)
                            </label>
                            <input type="number" 
                                   name="shelf_life_days" 
                                   id="shelf_life_days" 
                                   value="{{ old('shelf_life_days', $rawMaterial->shelf_life_days) }}"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('shelf_life_days') border-red-500 @enderror">
                            @error('shelf_life_days')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Gambar & Deskripsi --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <span>Gambar & Lainnya</span>
                        </h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Image -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                Gambar Bahan Baku
                            </label>
                            <div class="flex flex-col sm:flex-row items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div id="image-preview" class="w-32 h-32 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center bg-gray-50 overflow-hidden">
                                        @if($rawMaterial->image)
                                        <img src="{{ Storage::url($rawMaterial->image) }}" class="w-full h-full object-cover" id="current-image">
                                        @else
                                        <i class="fas fa-image text-4xl text-gray-300"></i>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-1 w-full sm:w-auto">
                                    <input type="file" 
                                        name="image" 
                                        id="image" 
                                        accept="image/*"
                                        class="hidden">
                                    <div class="flex flex-col sm:flex-row gap-2">
                                        <label for="image" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors text-sm font-medium text-gray-700">
                                            <i class="fas fa-upload mr-2"></i>
                                            {{ $rawMaterial->image ? 'Ganti' : 'Pilih Gambar' }}
                                        </label>
                                        <button type="button" id="remove-image" class="inline-flex items-center justify-center px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors text-sm font-medium {{ $rawMaterial->image ? '' : 'hidden' }}">
                                            Hapus
                                        </button>
                                    </div>
                                    <input type="hidden" name="remove_image" id="remove-image-flag" value="0">
                                    <p class="text-xs text-gray-500 mt-2">Format: JPG, PNG (Max: 2MB)</p>
                                    @error('image')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Deskripsi
                            </label>
                            <textarea name="description" 
                                      id="description" 
                                      rows="4"
                                      class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('description') border-red-500 @enderror">{{ old('description', $rawMaterial->description) }}</textarea>
                            @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Status --}}
                <div class="pt-2">
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="is_active" 
                                   id="is_active" 
                                   value="1"
                                   {{ old('is_active', $rawMaterial->is_active) ? 'checked' : '' }}
                                   class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                            <label for="is_active" class="ml-2 text-sm font-medium text-gray-900">
                                Aktifkan bahan baku ini (Dapat digunakan dalam transaksi)
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-6 border-t border-gray-200">
                    <div class="flex flex-col md:flex-row md:justify-end gap-3">
                        <a href="{{ route('raw-materials.index') }}" 
                           class="w-full md:w-auto inline-flex items-center justify-center px-6 py-2.5 border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-times mr-2 text-xs"></i>
                            <span>Batal</span>
                        </a>
                        <button type="submit" 
                                class="w-full md:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-orange-500 text-sm font-semibold text-white rounded-lg hover:bg-orange-600 shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:ring-offset-1">
                            <i class="fas fa-save mr-2 text-xs"></i>
                            <span>Update Bahan Baku</span>
                        </button>
                    </div>
                </div>
            </form>
        </section>
    </div>

    {{-- Modal Scanner --}}
    <div id="scannerModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 hidden">
        <div class="bg-white rounded-lg p-5 w-full max-w-md mx-4 relative">
            <button type="button" id="closeScanner" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 z-10">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h3 class="text-lg font-bold mb-4 text-center">Scan Barcode</h3>
            <div id="reader" class="w-full bg-gray-100 rounded-lg overflow-hidden"></div>
            <p class="text-xs text-gray-500 mt-3 text-center">Arahkan kamera ke barcode</p>
        </div>
    </div>
</main>

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

@push('scripts')
{{-- HTML5-QRCode Library --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
    const removeImageFlag = document.getElementById('remove-image-flag');
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
                removeImageFlag.value = '0';
            };

            reader.readAsDataURL(file);
        }
    });

    removeImageBtn.addEventListener('click', function() {
        imageInput.value = '';
        currentImageFile = null;
        imagePreview.innerHTML = '<i class="fas fa-image text-3xl text-gray-300"></i>';
        removeImageBtn.classList.add('hidden');
        removeImageFlag.value = '1';
    });

    // Barcode Scanner Logic
    let html5QrcodeScanner = null;

    function openScanner() {
        document.getElementById('scannerModal').classList.remove('hidden');
        
        if (!html5QrcodeScanner) {
            html5QrcodeScanner = new Html5Qrcode("reader");
        }
        
        const config = { fps: 10, qrbox: { width: 250, height: 150 }, aspectRatio: 1.0 };
        
        // Prefer back camera
        html5QrcodeScanner.start({ facingMode: "environment" }, config, onScanSuccess, onScanFailure)
        .catch(err => {
            console.error("Error starting scanner", err);
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
        
        const barcodeInput = document.getElementById('barcode');
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
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

            <form action="{{ route('raw-materials.store') }}" method="POST" enctype="multipart/form-data" class="px-4 md:px-6 py-6 space-y-8">
                @csrf
                
                {{-- Detail Informasi --}}
                <div>
                     <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <span>Informasi Dasar</span>
                        </h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                         <!-- Code -->
                         <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-1.5">Kode Bahan <span class="text-red-500">*</span></label>
                            <input type="text" name="code" id="code" value="{{ old('code') }}" required
                                class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm shadow-sm py-2.5">
                            @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Barcode -->
                        <div>
                            <label for="barcode" class="block text-sm font-medium text-gray-700 mb-1.5">Barcode</label>
                            <div class="flex gap-2">
                                <input type="text" name="barcode" id="barcode" value="{{ old('barcode') }}"
                                    class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm shadow-sm py-2.5">
                                <button type="button" id="startScan"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex-shrink-0"
                                        title="Scan Barcode">
                                    <i class="fas fa-qrcode"></i>
                                </button>
                            </div>
                            @error('barcode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Name -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nama Bahan Baku <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm shadow-sm py-2.5">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1.5">Kategori</label>
                            <select name="category_id" id="category_id" class="select2 w-full">
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Unit -->
                        <div>
                            <label for="unit_id" class="block text-sm font-medium text-gray-700 mb-1.5">Satuan <span class="text-red-500">*</span></label>
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

                {{-- Harga & Supplier --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <span>Harga & Supplier</span>
                        </h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Supplier -->
                        <div>
                            <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1.5">Supplier</label>
                            <select name="supplier_id" id="supplier_id" class="select2 w-full">
                                <option value="">Pilih Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Purchase Price -->
                        <div>
                            <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-1.5">Harga Beli <span class="text-red-500">*</span></label>
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

                {{-- Stok & Detail --}}
                <div>
                     <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <span>Stok & Detail Lainnya</span>
                        </h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Min Stock -->
                        <div>
                            <label for="min_stock" class="block text-sm font-medium text-gray-700 mb-1.5">Minimum Stok <span class="text-red-500">*</span></label>
                            <input type="number" name="min_stock" id="min_stock" value="{{ old('min_stock', 0) }}" required step="0.01"
                                class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm shadow-sm py-2.5">
                            @error('min_stock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Shelf Life -->
                        <div>
                            <label for="shelf_life_days" class="block text-sm font-medium text-gray-700 mb-1.5">Masa Simpan (Hari)</label>
                            <input type="number" name="shelf_life_days" id="shelf_life_days" value="{{ old('shelf_life_days') }}" placeholder="Contoh: 30"
                                class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm shadow-sm py-2.5">
                            @error('shelf_life_days') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Gambar & Deskripsi --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <span>Gambar & Deskripsi</span>
                        </h3>
                    </div>
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                         <!-- Image -->
                         <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Gambar Produk</label>
                            <div class="flex flex-col sm:flex-row items-start gap-4">
                                <div class="flex-shrink-0">
                                     <div id="image-preview" class="w-24 h-24 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center overflow-hidden">
                                        <div class="text-center text-gray-400">
                                            <i class="fas fa-image text-2xl mb-1"></i>
                                            <p class="text-[10px]">Preview</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-1 w-full">
                                    <input type="file" name="image" id="image" accept="image/*" class="hidden">
                                    <div class="flex flex-col gap-2">
                                        <label for="image" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer shadow-sm transition-all">
                                            <i class="fas fa-upload mr-2"></i> Pilih File
                                        </label>
                                        <button type="button" id="remove-image" class="hidden inline-flex items-center justify-center px-4 py-2 bg-red-50 border border-transparent rounded-lg text-sm font-medium text-red-700 hover:bg-red-100 transition-all">
                                            <i class="fas fa-trash mr-2"></i> Hapus
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">Max. 2MB (JPG/PNG)</p>
                                    @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                            <textarea name="description" id="description" rows="4"
                                class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm shadow-sm">{{ old('description') }}</textarea>
                            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                     </div>
                </div>

                {{-- Status --}}
                <div class="pt-2">
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center">
                            <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <label for="is_active" class="ml-2 text-sm font-medium text-gray-900">
                                Aktifkan bahan baku ini (Dapat digunakan dalam transaksi)
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-6 border-t border-gray-200">
                    <div class="flex flex-col md:flex-row md:justify-end gap-3">
                         <a href="{{ route('raw-materials.index') }}" class="w-full md:w-auto inline-flex items-center justify-center px-6 py-2.5 border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-times mr-2 text-xs"></i>
                            <span>Batal</span>
                        </a>
                        <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-red-600 text-sm font-semibold text-white rounded-lg hover:bg-red-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-1">
                            <i class="fas fa-save mr-2 text-xs"></i>
                            <span>Simpan Bahan Baku</span>
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
        border-color: #ef4444 !important;
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

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
{{-- HTML5-QRCode Library --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({
        theme: 'default',
        width: '100%'
    });

    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    const removeImageBtn = document.getElementById('remove-image');
    const defaultPreviewContent = `<div class="text-center text-gray-400"><i class="fas fa-image text-2xl mb-1"></i><p class="text-[10px]">Preview</p></div>`;

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
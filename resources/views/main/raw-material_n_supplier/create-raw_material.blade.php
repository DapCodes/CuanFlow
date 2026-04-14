@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Tambah Bahan Baku - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('raw-materials.index') }}" class="text-gray-500 hover:text-cuan-green transition-colors">Bahan Baku</a>
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Tambah Baru</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">
        
        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Tambah Bahan Baku
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Lengkapi formulir inventaris untuk menambahkan bahan baku operasional baru.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('raw-materials.index') }}" 
                   class="inline-flex items-center gap-2 rounded-xl bg-white border border-gray-200 px-5 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all shadow-sm active:scale-95">
                    <span>Kembali</span>
                </a>
            </div>
        </section>

        {{-- FORM --}}
        <form action="{{ route('raw-materials.store') }}" method="POST" enctype="multipart/form-data" id="materialForm">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Kolom Kiri: Informasi Utama --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    <x-card-container title="Informasi Produk">
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="code" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Kode Bahan <span class="text-red-500">*</span></label>
                                    <input type="text" name="code" id="code" value="{{ old('code') }}" required
                                           class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all shadow-sm">
                                    @error('code') <p class="mt-1.5 text-[10px] text-red-500 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="barcode" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Barcode</label>
                                    <div class="flex gap-2">
                                        <input type="text" name="barcode" id="barcode" value="{{ old('barcode') }}"
                                               class="flex-1 bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all shadow-sm">
                                        <button type="button" id="startScan"
                                                class="w-12 h-12 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white transition-all border border-blue-100 active:scale-95 shadow-sm"
                                                title="Scan Barcode">
                                            <i class="fas fa-barcode"></i>
                                        </button>
                                    </div>
                                    @error('barcode') <p class="mt-1.5 text-[10px] text-red-500 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="name" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Nama Bahan Baku <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                           class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all shadow-sm">
                                    @error('name') <p class="mt-1.5 text-[10px] text-red-500 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="category_id" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Kategori</label>
                                    <select name="category_id" id="category_id" class="select2 w-full">
                                        <option value="">Pilih Kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id') <p class="mt-1.5 text-[10px] text-red-500 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="unit_id" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Satuan <span class="text-red-500">*</span></label>
                                    <select name="unit_id" id="unit_id" class="select2 w-full" required>
                                        <option value="">Pilih Satuan</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->abbreviation }})</option>
                                        @endforeach
                                    </select>
                                    @error('unit_id') <p class="mt-1.5 text-[10px] text-red-500 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </x-card-container>

                    <x-card-container title="Detail Stok & Harga">
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label for="purchase_price" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Harga Beli <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-gray-400">RP</span>
                                        <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', 0) }}" required step="0.01"
                                               class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all shadow-sm">
                                    </div>
                                    @error('purchase_price') <p class="mt-1.5 text-[10px] text-red-500 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="min_stock" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Minimum Stok <span class="text-red-500">*</span></label>
                                    <input type="number" name="min_stock" id="min_stock" value="{{ old('min_stock', 0) }}" required step="0.01"
                                           class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all shadow-sm">
                                    @error('min_stock') <p class="mt-1.5 text-[10px] text-red-500 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="shelf_life_days" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Masa Simpan (Hari)</label>
                                    <input type="number" name="shelf_life_days" id="shelf_life_days" value="{{ old('shelf_life_days') }}"
                                           class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 placeholder:text-gray-300 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all shadow-sm" placeholder="Opsional">
                                    @error('shelf_life_days') <p class="mt-1.5 text-[10px] text-red-500 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </x-card-container>

                    <x-card-container title="Deskripsi & Media">
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">Foto Bahan Baku</label>
                                    <div class="flex items-center gap-6">
                                        <div id="image-preview" class="w-24 h-24 rounded-2xl bg-gray-50 border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden transition-all">
                                            <i class="fas fa-image text-gray-300 text-2xl"></i>
                                        </div>
                                        <div class="flex-1 space-y-3">
                                            <input type="file" name="image" id="image" accept="image/*" class="hidden">
                                            <label for="image" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50 transition-all cursor-pointer shadow-sm">
                                                <i class="fas fa-upload"></i> Unggah Foto
                                            </label>
                                            <button type="button" id="remove-image" class="hidden text-[10px] font-black text-red-500 uppercase tracking-widest hover:underline block">Hapus Foto</button>
                                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">JPG, PNG, atau WEBP. Maks 2MB.</p>
                                        </div>
                                    </div>
                                    @error('image') <p class="mt-1.5 text-[10px] text-red-500 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="description" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Catatan Tambahan</label>
                                    <textarea name="description" id="description" rows="4"
                                              class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all shadow-sm">{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </x-card-container>
                </div>

                {{-- Kolom Kanan: Supplier & Actions --}}
                <div class="lg:col-span-1 space-y-6">
                    <x-card-container title="Relasi & Status">
                        <div class="p-6 space-y-6">
                            <div>
                                <label for="supplier_id" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Supplier Utama</label>
                                <select name="supplier_id" id="supplier_id" class="select2 w-full">
                                    <option value="">Tanpa Supplier (Umum)</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-black text-gray-900 uppercase tracking-widest">Status Aktif</p>
                                    <p class="text-[10px] font-bold text-gray-400 mt-1">Gunakan di produksi</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cuan-green"></div>
                                </label>
                            </div>

                            <div class="mt-8 space-y-3">
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-cuan-green py-4 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                                    <i class="fas fa-save shadow-sm"></i>
                                    <span>Simpan Bahan Baku</span>
                                </button>
                                <a href="{{ route('raw-materials.index') }}"
                                   class="w-full inline-flex items-center justify-center py-4 text-sm font-bold text-gray-500 hover:text-gray-900 transition-all">
                                    Batal
                                </a>
                            </div>
                        </div>
                    </x-card-container>
                </div>
            </div>
        </form>
    </div>

    {{-- Scanner Modal --}}
    <div id="scannerModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/90 backdrop-blur-md hidden px-4">
        <div class="bg-white rounded-[2.5rem] p-8 w-full max-w-md shadow-2xl relative border border-gray-100">
            <button type="button" id="closeScanner" class="absolute -top-4 -right-4 w-12 h-12 bg-white rounded-full shadow-xl flex items-center justify-center text-gray-400 hover:text-red-500 transition-all border border-gray-100 z-[110]">
                <i class="fas fa-times text-xl"></i>
            </button>
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-cuan-green/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-barcode text-cuan-green text-3xl"></i>
                </div>
                <h3 class="text-2xl font-black text-gray-900">Scan Barcode</h3>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mt-2 px-6">Dekatkan kamera ke barcode produk untuk memindai otomatis</p>
            </div>
            <div class="relative group">
                <div id="reader" class="w-full aspect-square bg-gray-100 rounded-[2rem] overflow-hidden border-2 border-gray-50 shadow-inner relative z-0"></div>
                <div class="absolute inset-0 pointer-events-none border-[3px] border-emerald-500/30 rounded-[2rem] z-10 animate-pulse"></div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    #reader video {
        object-fit: cover !important;
        width: 100% !important;
        height: 100% !important;
        border-radius: 1.8rem !important;
    }
    #reader canvas {
        display: none !important;
    }
    .select2-container--default .select2-selection--single {
        border-radius: 0.75rem !important;
        border: 1px solid #e5e7eb !important;
        height: 50px !important;
        padding: 10px 10px 10px 5px !important;
        font-weight: 700;
        font-size: 0.875rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 48px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;
        color: #111827 !important;
    }
    .select2-dropdown {
        border-radius: 1rem !important;
        border: 1px solid #e5e7eb !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        overflow: hidden !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
$(document).ready(function() {
    $('.select2').select2();

    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('image-preview');
    const removeImageBtn = document.getElementById('remove-image');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                imagePreview.classList.add('border-solid', 'border-white');
                removeImageBtn.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });

    removeImageBtn.addEventListener('click', function() {
        imageInput.value = '';
        imagePreview.innerHTML = `<i class="fas fa-image text-gray-300 text-2xl"></i>`;
        imagePreview.classList.remove('border-solid', 'border-white');
        this.classList.add('hidden');
    });

    {{-- SCANNER LOGIC --}}
    let html5QrcodeScanner = null;

    $('#startScan').on('click', function() {
        $('#scannerModal').removeClass('hidden');
        if (!html5QrcodeScanner) html5QrcodeScanner = new Html5Qrcode("reader");
        
        const config = { 
            fps: 15, 
            qrbox: function(viewfinderWidth, viewfinderHeight) {
                const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                const qrboxSize = Math.floor(minEdge * 0.7);
                return {
                    width: qrboxSize,
                    height: qrboxSize
                };
            },
            aspectRatio: 1.0
        };

        html5QrcodeScanner.start({ facingMode: "environment" }, config, 
            (decodedText) => {
                $('#barcode').val(decodedText);
                closeScanner();
                Swal.fire({ 
                    icon: 'success', 
                    title: 'Berhasil', 
                    text: 'Barcode ' + decodedText + ' berhasil terbaca', 
                    timer: 2000, 
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-3xl border-none shadow-2xl' }
                });
            }, 
            (errorMessage) => {}
        ).catch(err => {
            console.error(err);
            closeScanner();
            Swal.fire({ icon: 'error', title: 'Akses Gagal', text: 'Kamera tidak dapat diakses atau sedang digunakan.', customClass: { popup: 'rounded-3xl' } });
        });
    });

    async function closeScanner() {
        $('#scannerModal').addClass('hidden');
        if (html5QrcodeScanner) {
            try {
                if (html5QrcodeScanner.isScanning) {
                    await html5QrcodeScanner.stop();
                }
            } catch (err) {
                console.warn('Error stopping scanner:', err);
            }
        }
    }

    $('#closeScanner, #scannerModal').on('click', function(e) {
        if (e.target === this) closeScanner();
    });
});
</script>
@endpush
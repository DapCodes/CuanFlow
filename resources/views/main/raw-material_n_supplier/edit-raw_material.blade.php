@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Edit Bahan Baku - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('raw-materials.index') }}" class="text-gray-500 hover:text-cuan-green transition-colors">Bahan Baku</a>
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Edit Bahan Baku</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">
        
        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Edit Bahan Baku
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Memperbarui data inventaris: <span class="text-cuan-green font-bold tracking-tight">{{ $rawMaterial->name }}</span>
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
        <form action="{{ route('raw-materials.update', $rawMaterial) }}" method="POST" enctype="multipart/form-data" id="materialForm">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Kolom Kiri: Informasi Utama --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    <x-card-container title="Informasi Produk">
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="code" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Kode Bahan <span class="text-red-500">*</span></label>
                                    <input type="text" name="code" id="code" value="{{ old('code', $rawMaterial->code) }}" required
                                           class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all shadow-sm">
                                    @error('code') <p class="mt-1.5 text-[10px] text-red-500 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="barcode" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Barcode</label>
                                    <div class="flex gap-2">
                                        <input type="text" name="barcode" id="barcode" value="{{ old('barcode', $rawMaterial->barcode) }}"
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
                                    <input type="text" name="name" id="name" value="{{ old('name', $rawMaterial->name) }}" required
                                           class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all shadow-sm">
                                    @error('name') <p class="mt-1.5 text-[10px] text-red-500 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="category_id" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Kategori</label>
                                    <select name="category_id" id="category_id" class="select2 w-full">
                                        <option value="">Pilih Kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $rawMaterial->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id') <p class="mt-1.5 text-[10px] text-red-500 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="unit_id" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Satuan <span class="text-red-500">*</span></label>
                                    <select name="unit_id" id="unit_id" class="select2 w-full" required>
                                        <option value="">Pilih Satuan</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ old('unit_id', $rawMaterial->unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->abbreviation }})</option>
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
                                        <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', $rawMaterial->purchase_price) }}" required step="0.01"
                                               class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all shadow-sm">
                                    </div>
                                    @error('purchase_price') <p class="mt-1.5 text-[10px] text-red-500 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="min_stock" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Minimum Stok <span class="text-red-500">*</span></label>
                                    <input type="number" name="min_stock" id="min_stock" value="{{ old('min_stock', $rawMaterial->min_stock) }}" required step="0.01"
                                           class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all shadow-sm">
                                    @error('min_stock') <p class="mt-1.5 text-[10px] text-red-500 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="shelf_life_days" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Masa Simpan (Hari)</label>
                                    <input type="number" name="shelf_life_days" id="shelf_life_days" value="{{ old('shelf_life_days', $rawMaterial->shelf_life_days) }}"
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
                                            @if($rawMaterial->image)
                                                <img src="{{ Storage::url($rawMaterial->image) }}" class="w-full h-full object-cover">
                                            @else
                                                <i class="fas fa-image text-gray-300 text-2xl"></i>
                                            @endif
                                        </div>
                                        <div class="flex-1 space-y-3">
                                            <input type="file" name="image" id="image" accept="image/*" class="hidden">
                                            <label for="image" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50 transition-all cursor-pointer shadow-sm">
                                                <i class="fas fa-upload"></i> Ganti Foto
                                            </label>
                                            <button type="button" id="remove-image" class="{{ $rawMaterial->image ? '' : 'hidden' }} text-[10px] font-black text-red-500 uppercase tracking-widest hover:underline block">Hapus Foto</button>
                                            <input type="hidden" name="remove_image" id="remove-image-flag" value="0">
                                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">JPG, PNG, atau WEBP. Maks 2MB.</p>
                                        </div>
                                    </div>
                                    @error('image') <p class="mt-1.5 text-[10px] text-red-500 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="description" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Catatan Tambahan</label>
                                    <textarea name="description" id="description" rows="4"
                                              class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all shadow-sm">{{ old('description', $rawMaterial->description) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </x-card-container>

                    {{-- DANGER ZONE --}}
                    @can('hapus bahan baku')
                    <div class="p-8 rounded-[2rem] bg-red-50/50 border border-red-100 mt-12">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div>
                                <h3 class="text-lg font-black text-red-600 uppercase tracking-widest">Zona Bahaya</h3>
                                <p class="text-xs font-bold text-red-400 mt-1">Tindakan ini tidak dapat dibatalkan. Menghapus bahan baku akan berdampak pada riwayat produksi.</p>
                            </div>
                            <button type="button" onclick="confirmDeleteRawMaterial()"
                                    class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-white border border-red-200 text-sm font-black text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm active:scale-95 whitespace-nowrap">
                                <i class="fas fa-trash-alt mr-2"></i> Hapus Permanen
                            </button>
                        </div>
                        <form id="delete-raw-material-form" action="{{ route('raw-materials.destroy', $rawMaterial) }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                    @endcan
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
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $rawMaterial->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-black text-gray-900 uppercase tracking-widest">Status Aktif</p>
                                    <p class="text-[10px] font-bold text-gray-400 mt-1">Gunakan di produksi</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $rawMaterial->is_active) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cuan-green"></div>
                                </label>
                            </div>

                            <div class="mt-8 space-y-3">
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-cuan-green py-4 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                                    <i class="fas fa-save shadow-sm"></i>
                                    <span>Perbarui Data</span>
                                </button>
                                <a href="{{ route('raw-materials.index') }}"
                                   class="w-full inline-flex items-center justify-center py-4 text-sm font-bold text-gray-500 hover:text-gray-900 transition-all">
                                    Batalkan Perubahan
                                </a>
                            </div>
                        </div>
                    </x-card-container>

                    <x-card-container title="Statistik Terkait">
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Terakhir Diupdate</p>
                                <p class="text-xs font-bold text-gray-900">{{ $rawMaterial->updated_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex items-center justify-between">
                                <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Dibuat Pada</p>
                                <p class="text-xs font-bold text-gray-900">{{ $rawMaterial->created_at->format('d M Y') }}</p>
                            </div>
                             <a href="{{ route('raw-materials.stock-history', $rawMaterial) }}" class="w-full flex items-center justify-center gap-2 p-3 bg-gray-50 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-100 transition-all border border-gray-100">
                                <i class="fas fa-history"></i> Lihat Riwayat Stok
                             </a>
                        </div>
                    </x-card-container>
                </div>
            </div>
        </form>
    </div>

    {{-- Scanner Modal --}}
    <div id="scannerModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/80 backdrop-blur-sm hidden px-4">
        <div class="bg-white rounded-[2rem] p-8 w-full max-w-md shadow-2xl relative">
            <button type="button" id="closeScanner" class="absolute top-6 right-6 text-gray-400 hover:text-gray-900 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
            <div class="text-center mb-6">
                <i class="fas fa-barcode text-cuan-green text-3xl mb-3 block"></i>
                <h3 class="text-xl font-black text-gray-900">Scan Barcode</h3>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Arahkan kamera ke barcode produk</p>
            </div>
            <div id="reader" class="w-full aspect-square bg-gray-50 rounded-2xl overflow-hidden border-2 border-dashed border-gray-100 shadow-inner"></div>
        </div>
    </div>
</main>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
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
    const removeImageFlag = document.getElementById('remove-image-flag');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                imagePreview.classList.add('border-solid', 'border-white');
                removeImageBtn.classList.remove('hidden');
                removeImageFlag.value = '0';
            }
            reader.readAsDataURL(file);
        }
    });

    removeImageBtn.addEventListener('click', function() {
        imageInput.value = '';
        imagePreview.innerHTML = `<i class="fas fa-image text-gray-300 text-2xl"></i>`;
        imagePreview.classList.remove('border-solid', 'border-white');
        this.classList.add('hidden');
        removeImageFlag.value = '1';
    });

    {{-- SCANNER LOGIC --}}
    let html5QrcodeScanner = null;

    $('#startScan').on('click', function() {
        $('#scannerModal').removeClass('hidden');
        if (!html5QrcodeScanner) html5QrcodeScanner = new Html5Qrcode("reader");
        
        html5QrcodeScanner.start({ facingMode: "environment" }, { fps: 10, qrbox: 250 }, 
            (decodedText) => {
                $('#barcode').val(decodedText);
                closeScanner();
                Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Barcode terdeteksi!', timer: 1000, showConfirmButton: false });
            }, 
            (errorMessage) => {}
        ).catch(err => {
            alert('Gagal mengakses kamera: ' + err);
            closeScanner();
        });
    });

    function closeScanner() {
        $('#scannerModal').addClass('hidden');
        if (html5QrcodeScanner) html5QrcodeScanner.stop();
    }

    $('#closeScanner, #scannerModal').on('click', function(e) {
        if (e.target === this) closeScanner();
    });

    {{-- DELETE CONFIRMATION --}}
    window.confirmDeleteRawMaterial = function() {
        Swal.fire({
            title: 'Hapus Permanen?',
            text: "Data bahan baku dan riwayat terkait akan dihapus selamanya dari sistem.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus Sekarang',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-[2rem] border-none shadow-2xl',
                title: 'font-black text-gray-900',
                confirmButton: 'rounded-xl px-6 py-3 font-bold text-sm',
                cancelButton: 'rounded-xl px-6 py-3 font-bold text-sm'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-raw-material-form').submit();
            }
        });
    }
});
</script>
@endpush
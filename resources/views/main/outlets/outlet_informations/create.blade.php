@extends('layouts.app')

@section('title', 'Tambah Outlet - CuanFlow')

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('outlets.index') }}" class="text-gray-600 hover:text-gray-900 font-medium">Informasi Outlet</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Tambah Outlet</span>
</li>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map {
        height: 400px;
        width: 100%;
        border-radius: 1.5rem;
        z-index: 1;
    }
    .leaflet-container img.leaflet-tile {
        max-width: none !important;
        max-height: none !important;
    }
    .leaflet-container {
        font-family: inherit;
    }
    .logo-preview-container {
        position: relative;
        width: 100%;
        aspect-ratio: 1/1;
        border: 2px dashed #e5e7eb;
        border-radius: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: #fdfdfd;
        transition: all 0.3s ease;
    }
    .logo-preview-container:hover {
        border-color: #658C58;
        background: #f8faf7;
    }
    .logo-preview-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .logo-preview-placeholder {
        text-align: center;
        color: #9ca3af;
    }
    .remove-logo-btn {
        position: absolute;
        top: 12px;
        right: 12px;
        background: #ef4444;
        color: white;
        border: none;
        border-radius: 0.75rem;
        width: 32px;
        height: 32px;
        display: none;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        z-index: 10;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
    }
    .logo-preview-container:hover .remove-logo-btn {
        display: flex;
    }
    
    /* Toggle Switch Styles */
    .toggle-checkbox:checked {
        right: 0;
        border-color: #658C58;
    }
    .toggle-checkbox:checked + .toggle-label {
        background-color: #658C58;
    }
</style>
@endpush

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Tambah Outlet Baru
                </h1>
                <p class="mt-1 text-sm text-gray-500 font-medium">
                    Lengkapi informasi outlet untuk ditambahkan ke sistem.
                </p>
            </div>
            <a href="{{ route('outlets.index') }}" class="inline-flex items-center justify-center h-11 px-6 bg-white text-gray-700 border border-gray-200 rounded-xl text-sm font-black hover:bg-gray-50 transition-all active:scale-95 shadow-sm">
                Kembali
            </a>
        </section>

        {{-- FORM CARD --}}
        <x-card-container>
            <form action="{{ route('outlets.store') }}" method="POST" enctype="multipart/form-data" class="px-6 py-8 md:px-8 space-y-10">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                    <!-- Left Column - Logo & Status -->
                    <div class="lg:col-span-1 space-y-10">
                        <!-- Logo Upload -->
                        <div>
                            <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-4">
                                Logo Outlet
                            </h3>
                            <div class="space-y-4">
                                <div class="logo-preview-container mb-4" id="logoPreviewContainer">
                                    <button type="button" class="remove-logo-btn" id="removeLogo" title="Hapus Logo">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                    <div class="logo-preview-placeholder" id="logoPlaceholder">
                                        <i class="fas fa-cloud-upload-alt text-3xl mb-3 text-gray-300"></i>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Pilih Logo</p>
                                    </div>
                                    <img id="logoPreview" class="rounded-2xl" style="display: none;">
                                </div>

                                <input type="file" name="logo" id="logoInput" accept="image/*" class="hidden">
                                
                                <button type="button" 
                                        onclick="document.getElementById('logoInput').click()"
                                        class="w-full h-12 bg-cuan-green/10 text-cuan-green border border-cuan-green/10 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-cuan-green hover:text-white transition-all shadow-sm">
                                    Pilih Berkas
                                </button>
                                
                                @error('logo')
                                <p class="mt-2 text-[10px] font-bold text-red-500 uppercase tracking-tight">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Settings Toggle -->
                        <div>
                            <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-4">
                                Konfigurasi Outlet
                            </h3>
                            <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 ring-1 ring-gray-100 space-y-6">
                                <!-- Produksi Otomatis -->
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs font-black text-gray-900 uppercase tracking-tight">Produksi Otomatis</p>
                                        <p class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-tight">Status produksi langsung selesai</p>
                                    </div>
                                    <div class="relative">
                                        <input type="checkbox" 
                                               name="auto_production" 
                                               id="auto_production"
                                               value="1"
                                               {{ old('auto_production', false) ? 'checked' : '' }}
                                               class="sr-only toggle-checkbox">
                                        <label for="auto_production" class="block relative w-12 h-6 cursor-pointer">
                                            <div class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-200 transition-colors duration-300"></div>
                                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full shadow-sm transition-transform duration-300" id="auto_production_dot"></div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Sistem Meja -->
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs font-black text-gray-900 uppercase tracking-tight">Sistem Meja</p>
                                        <p class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-tight">Aktifkan pemesanan berbasis meja</p>
                                    </div>
                                    <div class="relative">
                                        <input type="checkbox" 
                                               name="has_table_system" 
                                               id="has_table_system"
                                               value="1"
                                               {{ old('has_table_system', false) ? 'checked' : '' }}
                                               class="sr-only toggle-checkbox">
                                        <label for="has_table_system" class="block relative w-12 h-6 cursor-pointer">
                                            <div class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-200 transition-colors duration-300"></div>
                                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full shadow-sm transition-transform duration-300" id="has_table_system_dot"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Form Fields -->
                    <div class="lg:col-span-2 space-y-10">
                        <!-- Informasi Dasar -->
                        <div>
                            <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                                <!-- <i class="fas fa-info-circle text-cuan-green text-sm"></i> -->
                                Informasi Dasar
                            </h3>
                            <div class="space-y-6">
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">
                                        Nama Outlet <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="name" 
                                           value="{{ old('name') }}"
                                           class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all font-bold text-gray-700 shadow-sm @error('name') border-red-500 @enderror"
                                           placeholder="Contoh: Outlet Utama Pusat"
                                           required>
                                    @error('name')
                                    <p class="mt-2 text-[10px] font-bold text-red-500 uppercase tracking-tight">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Kontak -->
                        <div>
                            <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                                <!-- <i class="fas fa-phone text-blue-500 text-sm"></i> -->
                                Kontak Detail
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">
                                        Nomor Telepon <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="phone" 
                                           value="{{ old('phone') }}"
                                           class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all font-bold text-gray-700 shadow-sm @error('phone') border-red-500 @enderror"
                                           placeholder="0812XXXXXXXX"
                                           required>
                                    @error('phone')
                                    <p class="mt-2 text-[10px] font-bold text-red-500 uppercase tracking-tight">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">
                                        Email <span class="text-gray-300 font-bold lowercase italic">(opsional)</span>
                                    </label>
                                    <input type="email" 
                                           name="email" 
                                           value="{{ old('email') }}"
                                           class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all font-bold text-gray-700 shadow-sm @error('email') border-red-500 @enderror"
                                           placeholder="outlet@bisnis.com">
                                    @error('email')
                                    <p class="mt-2 text-[10px] font-bold text-red-500 uppercase tracking-tight">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Lokasi -->
                        <div>
                            <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                                <!-- <i class="fas fa-map-marked-alt text-red-500 text-sm"></i> -->
                                Lokasi Geografis
                            </h3>
                            <div class="space-y-8">
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">
                                        Alamat Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="address" 
                                              id="address"
                                              rows="3"
                                              class="w-full bg-white border border-gray-200 rounded-2xl px-4 py-4 text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all font-bold text-gray-700 shadow-sm @error('address') border-red-500 @enderror"
                                              placeholder="Jl. Raya Bisnis No. 123, Kelurahan, Kecamatan..."
                                              required>{{ old('address') }}</textarea>
                                    @error('address')
                                    <p class="mt-2 text-[10px] font-bold text-red-500 uppercase tracking-tight">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-4">
                                        <label class="text-[10px] font-black text-gray-900 uppercase tracking-widest block">
                                            Pilih Titik Lokasi
                                        </label>
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest italic">Geser marker untuk akurasi</p>
                                    </div>
                                    <div id="map" class="border-4 border-white shadow-xl ring-1 ring-gray-100"></div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-gray-50/50 border border-gray-100 rounded-2xl">
                                    <div>
                                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Latitude</label>
                                        <input type="text" 
                                               name="latitude" 
                                               id="latitude"
                                               value="{{ old('latitude') }}"
                                               class="w-full bg-white border border-gray-100 rounded-xl px-4 py-2.5 text-xs text-gray-500 font-mono font-bold shadow-inner"
                                               readonly placeholder="-6.200000">
                                    </div>
                                    <div>
                                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Longitude</label>
                                        <input type="text" 
                                               name="longitude" 
                                               id="longitude"
                                               value="{{ old('longitude') }}"
                                               class="w-full bg-white border border-gray-100 rounded-xl px-4 py-2.5 text-xs text-gray-500 font-mono font-bold shadow-inner"
                                               readonly placeholder="106.816666">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Transfer Section -->
                @if($activeOutlet)
                <div class="pt-8 border-t border-gray-100">
                    <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                        Transfer Data
                    </h3>
                    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 ring-1 ring-gray-100 space-y-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-black text-gray-900 uppercase tracking-tight">Transfer Data dari {{ $activeOutlet->name }}</p>
                                <p class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-tight">Salin data produk, supplier, dan bahan baku ke outlet baru ini</p>
                            </div>
                            <div class="relative">
                                <input type="checkbox" 
                                       name="transfer_data" 
                                       id="transfer_data"
                                       value="1"
                                       class="sr-only toggle-checkbox">
                                <label for="transfer_data" class="block relative w-12 h-6 cursor-pointer">
                                    <div class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-200 transition-colors duration-300"></div>
                                    <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full shadow-sm transition-transform duration-300" id="transfer_data_dot"></div>
                                </label>
                            </div>
                        </div>

                        <div id="transfer_options" class="hidden space-y-8 pt-6 border-t border-gray-200/50">
                            <!-- Products Section -->
                            <div>
                                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center justify-between">
                                    <span>Produk & Resep</span>
                                    <button type="button" onclick="selectAll('product')" class="text-cuan-green hover:underline">Pilih Semua</button>
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($transferData['products'] as $product)
                                    <label class="product-item flex items-center p-3 bg-white border border-gray-100 rounded-xl cursor-pointer hover:border-cuan-green/30 transition-all shadow-sm">
                                        <input type="checkbox" 
                                               name="transfer_products[]" 
                                               value="{{ $product->id }}" 
                                               data-type="product"
                                               data-suppliers="{{ json_encode(array_filter([$product->supplier_id])) }}"
                                               data-raw-materials="{{ json_encode($product->defaultRecipe ? $product->defaultRecipe->items->pluck('raw_material_id')->toArray() : []) }}"
                                               class="w-4 h-4 text-cuan-green border-gray-200 rounded focus:ring-cuan-green/20 dependency-check">
                                        <div class="ml-3 overflow-hidden">
                                            <span class="block text-xs font-black text-gray-900 uppercase tracking-tight truncate">{{ $product->name }}</span>
                                            <span class="block text-[9px] font-bold text-gray-400 uppercase tracking-tight">{{ $product->code }}</span>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Suppliers Section -->
                            <div>
                                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center justify-between">
                                    <span>Supplier</span>
                                    <button type="button" onclick="selectAll('supplier')" class="text-cuan-green hover:underline">Pilih Semua</button>
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($transferData['suppliers'] as $supplier)
                                    <label class="flex items-center p-3 bg-white border border-gray-100 rounded-xl cursor-pointer hover:border-cuan-green/30 transition-all shadow-sm">
                                        <input type="checkbox" 
                                               name="transfer_suppliers[]" 
                                               value="{{ $supplier->id }}" 
                                               id="sup-{{ $supplier->id }}"
                                               data-type="supplier"
                                               class="w-4 h-4 text-cuan-green border-gray-200 rounded focus:ring-cuan-green/20">
                                        <div class="ml-3 overflow-hidden">
                                            <span class="block text-xs font-black text-gray-900 uppercase tracking-tight truncate">{{ $supplier->name }}</span>
                                            <span class="block text-[9px] font-bold text-gray-400 uppercase tracking-tight">{{ $supplier->code }}</span>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Raw Materials Section -->
                            <div>
                                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center justify-between">
                                    <span>Bahan Baku</span>
                                    <button type="button" onclick="selectAll('raw_material')" class="text-cuan-green hover:underline">Pilih Semua</button>
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($transferData['raw_materials'] as $material)
                                    <label class="flex items-center p-3 bg-white border border-gray-100 rounded-xl cursor-pointer hover:border-cuan-green/30 transition-all shadow-sm">
                                        <input type="checkbox" 
                                               name="transfer_raw_materials[]" 
                                               value="{{ $material->id }}" 
                                               id="rm-{{ $material->id }}"
                                               data-type="raw_material"
                                               class="w-4 h-4 text-cuan-green border-gray-200 rounded focus:ring-cuan-green/20">
                                        <div class="ml-3 overflow-hidden">
                                            <span class="block text-xs font-black text-gray-900 uppercase tracking-tight truncate">{{ $material->name }}</span>
                                            <span class="block text-[9px] font-bold text-gray-400 uppercase tracking-tight">{{ $material->code }}</span>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="pt-8 border-t border-gray-100">
                    <div class="flex flex-col md:flex-row md:justify-end gap-3">
                        <a href="{{ route('outlets.index') }}" 
                           class="w-full md:w-auto h-12 inline-flex items-center justify-center px-8 border border-gray-200 text-sm font-black text-gray-400 rounded-xl hover:bg-gray-50 transition-all active:scale-95 shadow-sm">
                            Batal
                        </a>
                        <button type="submit" 
                                class="w-full md:w-auto h-12 inline-flex items-center justify-center px-10 bg-cuan-green text-sm font-black text-white rounded-xl hover:bg-cuan-dark transition-all active:scale-95 shadow-lg shadow-cuan-green/20">
                            Simpan Outlet
                        </button>
                    </div>
                </div>
            </form>
        </x-card-container>
    </div>
</main>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Logo Preview Handler
    const logoInput = document.getElementById('logoInput');
    const logoPreview = document.getElementById('logoPreview');
    const logoPlaceholder = document.getElementById('logoPlaceholder');
    const removeLogo = document.getElementById('removeLogo');

    logoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({ icon: 'error', title: 'File Terlalu Besar', text: 'Maksimal 2MB', customClass: { popup: 'rounded-2xl' } });
                logoInput.value = ''; return;
            }
            if (!file.type.startsWith('image/')) {
                Swal.fire({ icon: 'error', title: 'Bukan Gambar', text: 'File harus berupa gambar', customClass: { popup: 'rounded-2xl' } });
                logoInput.value = ''; return;
            }
            const reader = new FileReader();
            reader.onload = e => { logoPreview.src = e.target.result; logoPreview.style.display = 'block'; logoPlaceholder.style.display = 'none'; };
            reader.readAsDataURL(file);
        }
    });

    removeLogo.addEventListener('click', e => { e.stopPropagation(); logoInput.value = ''; logoPreview.src = ''; logoPreview.style.display = 'none'; logoPlaceholder.style.display = 'block'; });

    // Toggle Handlers
    const autoProdCheckbox = document.getElementById('auto_production');
    const autoProdDot = document.getElementById('auto_production_dot');
    const tableSysCheckbox = document.getElementById('has_table_system');
    const tableSysDot = document.getElementById('has_table_system_dot');

    const updateToggle = (box, dot) => dot.style.transform = box.checked ? 'translateX(1.5rem)' : 'translateX(0)';
    
    autoProdCheckbox.addEventListener('change', () => updateToggle(autoProdCheckbox, autoProdDot));
    tableSysCheckbox.addEventListener('change', () => updateToggle(tableSysCheckbox, tableSysDot));
    
    updateToggle(autoProdCheckbox, autoProdDot);
    updateToggle(tableSysCheckbox, tableSysDot);

    // Transfer Data Handler
    const transferCheckbox = document.getElementById('transfer_data');
    const transferDot = document.getElementById('transfer_data_dot');
    const transferOptions = document.getElementById('transfer_options');

    if (transferCheckbox) {
        transferCheckbox.addEventListener('change', function() {
            updateToggle(this, transferDot);
            if (this.checked) {
                transferOptions.classList.remove('hidden');
            } else {
                transferOptions.classList.add('hidden');
            }
        });
        updateToggle(transferCheckbox, transferDot);
    }

    // Auto-check dependencies logic
    const dependencyChecks = document.querySelectorAll('.dependency-check');
    dependencyChecks.forEach(check => {
        check.addEventListener('change', function() {
            if (this.checked) {
                const suppliers = JSON.parse(this.dataset.suppliers || '[]');
                const rawMaterials = JSON.parse(this.dataset.rawMaterials || '[]');

                suppliers.forEach(id => {
                    const el = document.getElementById(`sup-${id}`);
                    if (el) el.checked = true;
                });

                rawMaterials.forEach(id => {
                    const el = document.getElementById(`rm-${id}`);
                    if (el) el.checked = true;
                });
            }
        });
    });

    window.selectAll = function(type) {
        document.querySelectorAll(`input[data-type="${type}"]`).forEach(el => {
            el.checked = true;
            // Trigger change event for product to check dependencies
            if (type === 'product') el.dispatchEvent(new Event('change'));
        });
    };

    // Map Handler
    const defaultLat = -6.2088;
    const defaultLng = 106.8456;
    const oldLat = "{{ old('latitude') }}";
    const oldLng = "{{ old('longitude') }}";
    
    const map = L.map('map', { zoomControl: false, attributionControl: false }).setView([defaultLat, defaultLng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    const customIcon = L.divIcon({
        className: 'custom-marker',
        html: `<div class="w-8 h-8 rounded-full bg-cuan-green border-4 border-white shadow-xl flex items-center justify-center text-white"><i class="fas fa-store text-xs"></i></div>`,
        iconSize: [32, 32],
        iconAnchor: [16, 16]
    });

    let marker = null;
    function updateMarker(lat, lng, fetchAddress = false) {
        if (marker) marker.setLatLng([lat, lng]);
        else {
            marker = L.marker([lat, lng], { icon: customIcon, draggable: true }).addTo(map);
            marker.on('dragend', () => updateMarker(marker.getLatLng().lat, marker.getLatLng().lng, true));
        }
        map.setView([lat, lng], 15);
        document.getElementById('latitude').value = lat.toFixed(6);
        document.getElementById('longitude').value = lng.toFixed(6);
        if (fetchAddress) {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(res => res.json()).then(data => { if (data?.display_name) document.getElementById('address').value = data.display_name; });
        }
    }

    map.on('click', e => updateMarker(e.latlng.lat, e.latlng.lng, true));
    if (navigator.geolocation && !oldLat) navigator.geolocation.getCurrentPosition(pos => updateMarker(pos.coords.latitude, pos.coords.longitude, true));
    if (oldLat && oldLng) updateMarker(parseFloat(oldLat), parseFloat(oldLng), false);

    const fixMapLayout = () => { if (map) map.invalidateSize(); if (marker) map.setView(marker.getLatLng()); };
    window.addEventListener('load', fixMapLayout);
    [100, 500, 1000].forEach(delay => setTimeout(fixMapLayout, delay));
});
</script>
@endpush
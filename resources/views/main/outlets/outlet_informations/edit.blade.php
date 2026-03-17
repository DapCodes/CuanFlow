@extends('layouts.app')

@section('title', 'Edit Outlet - CuanFlow')

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('outlets.index') }}" class="text-gray-600 hover:text-gray-900 font-medium">Informasi Outlet</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium">Edit {{ $outlet->name }}</span>
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

    .current-logo-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: #658C58;
        color: white;
        padding: 6px 12px;
        border-radius: 0.75rem;
        font-size: 9px;
        font-weight: 900;
        text-transform: uppercase;
        z-index: 10;
        box-shadow: 0 4px 12px rgba(101, 140, 88, 0.2);
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
                    Edit Outlet
                </h1>
                <p class="mt-1 text-sm text-gray-500 font-medium">
                    Perbarui informasi <span class="text-cuan-green font-black">{{ $outlet->name }}</span>.
                </p>
            </div>
            <a href="{{ route('outlets.index') }}" class="inline-flex items-center justify-center h-11 px-6 bg-white text-gray-700 border border-gray-200 rounded-xl text-sm font-black hover:bg-gray-50 transition-all active:scale-95 shadow-sm">
                Kembali
            </a>
        </section>

        {{-- FORM CARD --}}
        <x-card-container>
            <form action="{{ route('outlets.update', $outlet->id) }}" method="POST" enctype="multipart/form-data" class="px-6 py-8 md:px-8 space-y-10">
                @csrf
                @method('PUT')

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
                                    @if($outlet->logo)
                                    <span class="current-logo-badge">Logo Saat Ini</span>
                                    @endif
                                    <button type="button" class="remove-logo-btn" id="removeLogo" title="Hapus Logo">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                    @if($outlet->logo)
                                        <img id="logoPreview" src="{{ Storage::url($outlet->logo) }}" alt="Current Logo" class="rounded-2xl">
                                    @else
                                        <div class="logo-preview-placeholder" id="logoPlaceholder">
                                            <i class="fas fa-cloud-upload-alt text-3xl mb-3 text-gray-300"></i>
                                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Pilih Logo</p>
                                        </div>
                                        <img id="logoPreview" class="rounded-2xl" style="display: none;">
                                    @endif
                                </div>

                                <input type="file" name="logo" id="logoInput" accept="image/*" class="hidden">
                                
                                <div class="grid grid-cols-1 gap-2">
                                    <button type="button" 
                                            onclick="document.getElementById('logoInput').click()"
                                            class="w-full h-12 bg-cuan-green text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20">
                                        Ganti Berkas
                                    </button>
                                    
                                    @if($outlet->logo)
                                    <button type="button" id="resetLogo" class="w-full h-10 bg-gray-50 text-gray-400 border border-gray-100 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-gray-100 transition-all">
                                        Reset ke Semula
                                    </button>
                                    @endif
                                </div>
                                
                                @error('logo')
                                <p class="mt-2 text-[10px] font-bold text-red-500 uppercase tracking-tight">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Code Info -->
                        <div>
                            <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-4">
                                Kode Sistem
                            </h3>
                            <div class="bg-blue-50/50 rounded-2xl p-6 border border-blue-100 flex flex-col items-center text-center">
                                <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-2">Permanent ID</p>
                                <p class="text-3xl font-black text-blue-600 font-mono tracking-tighter">{{ $outlet->code }}</p>
                                <p class="mt-4 text-[9px] font-bold text-blue-300 uppercase tracking-widest italic">ID ini tidak dapat diubah</p>
                            </div>
                        </div>

                        <!-- Settings Toggle -->
                        <div>
                            <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-4">
                                Konfigurasi Outlet
                            </h3>
                            <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 ring-1 ring-gray-100 space-y-6">
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
                                               {{ old('auto_production', $outlet->auto_production) ? 'checked' : '' }}
                                               class="sr-only toggle-checkbox">
                                        <label for="auto_production" class="block relative w-12 h-6 cursor-pointer">
                                            <div class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-200 transition-colors duration-300"></div>
                                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full shadow-sm transition-transform duration-300" id="auto_production_dot"></div>
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
                                           value="{{ old('name', $outlet->name) }}"
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
                                           value="{{ old('phone', $outlet->phone) }}"
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
                                           value="{{ old('email', $outlet->email) }}"
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
                                              required>{{ old('address', $outlet->address) }}</textarea>
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
                                               value="{{ old('latitude', $outlet->latitude) }}"
                                               class="w-full bg-white border border-gray-100 rounded-xl px-4 py-2.5 text-xs text-gray-500 font-mono font-bold shadow-inner"
                                               readonly placeholder="-6.200000">
                                    </div>
                                    <div>
                                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Longitude</label>
                                        <input type="text" 
                                               name="longitude" 
                                               id="longitude"
                                               value="{{ old('longitude', $outlet->longitude) }}"
                                               class="w-full bg-white border border-gray-100 rounded-xl px-4 py-2.5 text-xs text-gray-500 font-mono font-bold shadow-inner"
                                               readonly placeholder="106.816666">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-8 border-t border-gray-100">
                    <div class="flex flex-col md:flex-row md:justify-end gap-3">
                        <a href="{{ route('outlets.index') }}" 
                           class="w-full md:w-auto h-12 inline-flex items-center justify-center px-8 border border-gray-200 text-sm font-black text-gray-400 rounded-xl hover:bg-gray-50 transition-all active:scale-95 shadow-sm">
                            Batal
                        </a>
                        <button type="submit" 
                                class="w-full md:w-auto h-12 inline-flex items-center justify-center px-10 bg-cuan-green text-sm font-black text-white rounded-xl hover:bg-cuan-dark transition-all active:scale-95 shadow-lg shadow-cuan-green/20">
                            Simpan Perubahan
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
    const originalLogoUrl = "{{ $outlet->logo ? Storage::url($outlet->logo) : '' }}";
    
    // Logo Preview Handler
    const logoInput = document.getElementById('logoInput');
    const logoPreview = document.getElementById('logoPreview');
    const logoPlaceholder = document.getElementById('logoPlaceholder');
    const removeLogo = document.getElementById('removeLogo');
    const resetLogo = document.getElementById('resetLogo');

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
            reader.onload = e => { 
                logoPreview.src = e.target.result; logoPreview.style.display = 'block'; 
                if (logoPlaceholder) logoPlaceholder.style.display = 'none';
                const badge = document.querySelector('.current-logo-badge');
                if (badge) badge.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    });

    removeLogo.addEventListener('click', e => { 
        e.stopPropagation(); logoInput.value = ''; logoPreview.src = ''; logoPreview.style.display = 'none'; 
        if (logoPlaceholder) logoPlaceholder.style.display = 'block';
        const badge = document.querySelector('.current-logo-badge');
        if (badge) badge.style.display = 'none';
    });

    if (resetLogo) {
        resetLogo.addEventListener('click', () => {
            logoInput.value = '';
            if (originalLogoUrl) {
                logoPreview.src = originalLogoUrl; logoPreview.style.display = 'block';
                if (logoPlaceholder) logoPlaceholder.style.display = 'none';
                const badge = document.querySelector('.current-logo-badge');
                if (badge) badge.style.display = 'block';
            }
        });
    }

    // Toggle Handler
    const autoProdCheckbox = document.getElementById('auto_production');
    const autoProdDot = document.getElementById('auto_production_dot');
    const updateToggle = (box, dot) => dot.style.transform = box.checked ? 'translateX(1.5rem)' : 'translateX(0)';
    autoProdCheckbox.addEventListener('change', () => updateToggle(autoProdCheckbox, autoProdDot));
    updateToggle(autoProdCheckbox, autoProdDot);

    // Map Handler
    const currentLat = {{ $outlet->latitude ?? -6.2088 }};
    const currentLng = {{ $outlet->longitude ?? 106.8456 }};
    
    const map = L.map('map', { zoomControl: false, attributionControl: false }).setView([currentLat, currentLng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    const customIcon = L.divIcon({
        className: 'custom-marker',
        html: `<div class="w-8 h-8 rounded-full bg-cuan-green border-4 border-white shadow-xl flex items-center justify-center text-white"><i class="fas fa-store text-xs"></i></div>`,
        iconSize: [32, 32],
        iconAnchor: [16, 16]
    });

    let marker = L.marker([currentLat, currentLng], { icon: customIcon, draggable: true }).addTo(map);

    function updateMarker(lat, lng, fetchAddress = false) {
        marker.setLatLng([lat, lng]);
        map.setView([lat, lng], 15);
        document.getElementById('latitude').value = lat.toFixed(6);
        document.getElementById('longitude').value = lng.toFixed(6);
        if (fetchAddress) {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(res => res.json()).then(data => { if (data?.display_name) document.getElementById('address').value = data.display_name; });
        }
    }

    marker.on('dragend', () => updateMarker(marker.getLatLng().lat, marker.getLatLng().lng, true));
    map.on('click', e => updateMarker(e.latlng.lat, e.latlng.lng, true));

    const fixMapLayout = () => { if (map) map.invalidateSize(); if (marker) map.setView(marker.getLatLng()); };
    window.addEventListener('load', fixMapLayout);
    [100, 500, 1000].forEach(delay => setTimeout(fixMapLayout, delay));
});
</script>
@endpush
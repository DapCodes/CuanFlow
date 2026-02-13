@extends('layouts.app')

@section('title', 'Edit Outlet - CuanFlow')

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('outlets.index') }}" class="text-gray-600 hover:text-gray-900">Informasi Outlet</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Edit {{ $outlet->name }}</span>
</li>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map {
        height: 400px;
        border-radius: 0.5rem;
        z-index: 1;
    }
    .logo-preview-container {
        position: relative;
        width: 200px;
        height: 200px;
        border: 2px dashed #d1d5db;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: #f9fafb;
        transition: all 0.3s ease;
    }
    .logo-preview-container:hover {
        border-color: #3b82f6;
        background: #eff6ff;
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
        top: 8px;
        right: 8px;
        background: #ef4444;
        color: white;
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: none;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        z-index: 10;
    }
    .logo-preview-container:hover .remove-logo-btn {
        display: flex;
    }
    .remove-logo-btn:hover {
        background: #dc2626;
        transform: scale(1.1);
    }
    
    /* Toggle Switch Styles */
    .toggle-checkbox:checked {
        right: 0;
        border-color: #f97316;
    }
    .toggle-checkbox:checked + .toggle-label {
        background-color: #f97316;
    }

    /* Current Logo Badge */
    .current-logo-badge {
        position: absolute;
        top: 8px;
        left: 8px;
        background: #3b82f6;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 600;
        z-index: 10;
    }
</style>
@endpush

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Alert error --}}
        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-exclamation-circle mt-0.5 text-red-500"></i>
                <p class="text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        {{-- HEADER --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-yellow-50 text-yellow-500 border border-yellow-100">
                        <i class="fas fa-edit text-sm"></i>
                    </span>
                    <span>Edit Outlet: {{ $outlet->name }}</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Perbarui informasi <span class="font-semibold">{{ $outlet->name }}</span> dengan mudah dan cepat.
                </p>
            </div>
            <a href="{{ route('outlets.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 transition-all shadow-sm">
                <i class="fas fa-arrow-left mr-2 text-xs"></i>
                Kembali
            </a>
        </section>

        {{-- FORM CARD --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            <form action="{{ route('outlets.update', $outlet->id) }}" method="POST" enctype="multipart/form-data" class="px-4 md:px-6 py-6 space-y-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column - Logo & Status -->
                    <div class="lg:col-span-1 space-y-8">
                        <!-- Logo Upload -->
                        <div>
                            <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-4">
                                Logo Outlet
                            </h3>
                            <div class="bg-gray-50 rounded-xl p-6 border-2 border-dashed border-gray-300 flex flex-col items-center">
                                <div class="logo-preview-container mb-4" id="logoPreviewContainer">
                                    @if($outlet->logo)
                                    <span class="current-logo-badge">Logo Saat Ini</span>
                                    @endif
                                    <button type="button" class="remove-logo-btn" id="removeLogo" title="Hapus Logo">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @if($outlet->logo)
                                    <img id="logoPreview" src="{{ Storage::url($outlet->logo) }}" alt="Current Logo" class="rounded-lg shadow-sm">
                                    @else
                                    <div class="logo-preview-placeholder" id="logoPlaceholder">
                                        <i class="fas fa-cloud-upload-alt text-5xl mb-2 text-gray-400"></i>
                                        <p class="text-sm font-medium text-gray-500">Upload Logo</p>
                                        <p class="text-xs text-gray-400 mt-1">JPG, PNG (Max 2MB)</p>
                                    </div>
                                    <img id="logoPreview" class="rounded-lg shadow-sm" style="display: none;">
                                    @endif
                                </div>

                                <input type="file" name="logo" id="logoInput" accept="image/*" class="hidden">
                                
                                <button type="button" 
                                        onclick="document.getElementById('logoInput').click()"
                                        class="w-full px-4 py-2.5 bg-yellow-500 text-white rounded-lg text-sm font-semibold hover:bg-yellow-600 transition-colors shadow-sm mb-2">
                                    <i class="fas fa-upload mr-2"></i>
                                    Ubah Logo
                                </button>

                                @if($outlet->logo)
                                <button type="button" id="resetLogo" class="w-full px-4 py-2 text-yellow-600 border border-yellow-200 bg-yellow-50 rounded-lg text-xs font-semibold hover:bg-yellow-100 transition-colors">
                                    <i class="fas fa-undo mr-1"></i>
                                    Reset ke Logo Lama
                                </button>
                                @endif
                                
                                @error('logo')
                                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Code Info -->
                        <div>
                            <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-4">
                                Kode Outlet
                            </h3>
                            <div class="bg-blue-50 rounded-xl p-5 border border-blue-100 text-center">
                                <p class="text-xs text-blue-500 font-medium mb-1 uppercase tracking-wider">Identifikasi Sistem</p>
                                <p class="text-2xl font-bold text-blue-700 font-mono tracking-widest">{{ $outlet->code }}</p>
                                <p class="mt-2 text-[10px] text-blue-400">
                                    <i class="fas fa-lock mr-1"></i> Kode ini bersifat permanen
                                </p>
                            </div>
                        </div>

                        <!-- Status Toggle -->
                        <div>
                            <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-4">
                                Pengaturan Outlet
                            </h3>
                            <div class="bg-gray-50 rounded-xl p-5 border border-gray-200 space-y-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Status Outlet</p>
                                        <p class="text-xs text-gray-500 mt-1">Dapat diakses sistem</p>
                                    </div>
                                    <div class="relative">
                                        <input type="checkbox" 
                                               name="is_active" 
                                               id="is_active"
                                               value="1"
                                               {{ old('is_active', $outlet->is_active) ? 'checked' : '' }}
                                               class="sr-only toggle-checkbox">
                                        <label for="is_active" class="block relative w-12 h-6 cursor-pointer">
                                            <div class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 transition-colors duration-200"></div>
                                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform duration-200" id="is_active_dot"></div>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between border-t border-gray-100 pt-6">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Produksi Otomatis</p>
                                        <p class="text-xs text-gray-500 mt-1">Status produksi otomatis selesai</p>
                                    </div>
                                    <div class="relative">
                                        <input type="checkbox" 
                                               name="auto_production" 
                                               id="auto_production"
                                               value="1"
                                               {{ old('auto_production', $outlet->auto_production) ? 'checked' : '' }}
                                               class="sr-only toggle-checkbox">
                                        <label for="auto_production" class="block relative w-12 h-6 cursor-pointer">
                                            <div class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 transition-colors duration-200"></div>
                                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform duration-200" id="auto_production_dot"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Form Fields -->
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Informasi Dasar -->
                        <div>
                            <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <i class="fas fa-info-circle text-blue-500 text-sm"></i>
                                Informasi Dasar
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                        Nama Outlet <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="name" 
                                           value="{{ old('name', $outlet->name) }}"
                                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 @error('name') border-red-500 @enderror"
                                           placeholder="Contoh: Outlet Cabang Utama"
                                           required>
                                    @error('name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Kontak -->
                        <div>
                            <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <i class="fas fa-address-book text-green-500 text-sm"></i>
                                Informasi Kontak
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                        Nomor Telepon <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="phone" 
                                           value="{{ old('phone', $outlet->phone) }}"
                                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 @error('phone') border-red-500 @enderror"
                                           placeholder="08123456789"
                                           required>
                                    @error('phone')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                        Email <span class="text-gray-400 font-normal">(Opsional)</span>
                                    </label>
                                    <input type="email" 
                                           name="email" 
                                           value="{{ old('email', $outlet->email) }}"
                                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 @error('email') border-red-500 @enderror"
                                           placeholder="outlet@example.com">
                                    @error('email')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Lokasi -->
                        <div>
                            <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <i class="fas fa-map-marked-alt text-red-500 text-sm"></i>
                                Lokasi GPS
                            </h3>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                        Alamat Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="address" 
                                              rows="3"
                                              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 @error('address') border-red-500 @enderror"
                                              placeholder="Jl. Contoh No. 123, Kota, Provinsi"
                                              required>{{ old('address', $outlet->address) }}</textarea>
                                    @error('address')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Pilih Lokasi di Peta
                                    </label>
                                    <div id="map" class="border border-gray-200 rounded-xl overflow-hidden shadow-inner"></div>
                                    <p class="mt-2 text-xs text-gray-500">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Klik pada peta atau geser marker untuk menentukan lokasi yang tepat.
                                    </p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                            Latitude
                                        </label>
                                        <input type="text" 
                                               name="latitude" 
                                               id="latitude"
                                               value="{{ old('latitude', $outlet->latitude) }}"
                                               class="w-full px-3 py-2.5 border border-gray-200 bg-gray-50 rounded-lg text-sm text-gray-600 font-mono"
                                               placeholder="-6.200000"
                                               readonly>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                            Longitude
                                        </label>
                                        <input type="text" 
                                               name="longitude" 
                                               id="longitude"
                                               value="{{ old('longitude', $outlet->longitude) }}"
                                               class="w-full px-3 py-2.5 border border-gray-200 bg-gray-50 rounded-lg text-sm text-gray-600 font-mono"
                                               placeholder="106.816666"
                                               readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons: mobile full width -->
                <div class="pt-5 border-t border-gray-200">
                    <div class="flex flex-col md:flex-row md:justify-end gap-3">
                        <a href="{{ route('outlets.index') }}" 
                           class="w-full md:w-auto inline-flex items-center justify-center px-6 py-2.5 border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-times mr-2 text-xs"></i>
                            <span>Batal</span>
                        </a>
                        <button type="submit" 
                                class="w-full md:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-yellow-500 text-sm font-semibold text-white rounded-lg hover:bg-yellow-600 shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-1">
                            <i class="fas fa-save mr-2 text-xs"></i>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </div>
            </form>
        </section>
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
            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file terlalu besar. Maksimal 2MB');
                logoInput.value = '';
                return;
            }

            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('File harus berupa gambar');
                logoInput.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                logoPreview.src = e.target.result;
                logoPreview.style.display = 'block';
                if (logoPlaceholder) logoPlaceholder.style.display = 'none';
                
                // Remove "current logo" badge
                const badge = document.querySelector('.current-logo-badge');
                if (badge) badge.remove();
            };
            reader.readAsDataURL(file);
        }
    });

    removeLogo.addEventListener('click', function(e) {
        e.stopPropagation();
        logoInput.value = '';
        logoPreview.src = '';
        logoPreview.style.display = 'none';
        if (logoPlaceholder) logoPlaceholder.style.display = 'block';
        
        // Remove badge
        const badge = document.querySelector('.current-logo-badge');
        if (badge) badge.remove();
    });

    // Reset to original logo
    if (resetLogo) {
        resetLogo.addEventListener('click', function() {
            logoInput.value = '';
            if (originalLogoUrl) {
                logoPreview.src = originalLogoUrl;
                logoPreview.style.display = 'block';
                if (logoPlaceholder) logoPlaceholder.style.display = 'none';
                
                // Add back the badge if not exists
                if (!document.querySelector('.current-logo-badge')) {
                    const badge = document.createElement('span');
                    badge.className = 'current-logo-badge';
                    badge.textContent = 'Logo Saat Ini';
                    document.getElementById('logoPreviewContainer').insertBefore(badge, document.getElementById('logoPreviewContainer').firstChild);
                }
            }
        });
    }

    // Toggle Switch Handler
    function updateToggle(checkbox, dot) {
        if (checkbox.checked) {
            dot.style.transform = 'translateX(1.5rem)';
        } else {
            dot.style.transform = 'translateX(0)';
        }
    }

    const isActiveCheckbox = document.getElementById('is_active');
    const isActiveDot = document.getElementById('is_active_dot');
    const autoProdCheckbox = document.getElementById('auto_production');
    const autoProdDot = document.getElementById('auto_production_dot');

    isActiveCheckbox.addEventListener('change', () => updateToggle(isActiveCheckbox, isActiveDot));
    autoProdCheckbox.addEventListener('change', () => updateToggle(autoProdCheckbox, autoProdDot));
    
    updateToggle(isActiveCheckbox, isActiveDot); // Initial state
    updateToggle(autoProdCheckbox, autoProdDot); // Initial state

    // Leaflet Map Handler
    const currentLat = {{ $outlet->latitude ?? -6.2088 }};
    const currentLng = {{ $outlet->longitude ?? 106.8456 }};
    
    // Initialize map with current location
    const map = L.map('map').setView([currentLat, currentLng], 15);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    // Custom marker icon
    const customIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    // Add marker at current location (draggable)
    let marker = L.marker([currentLat, currentLng], {
        icon: customIcon,
        draggable: true
    }).addTo(map);

    marker.bindPopup('<b>Lokasi Outlet</b><br>Geser marker untuk mengubah posisi').openPopup();

    // Update coordinates when marker is dragged
    marker.on('dragend', function(e) {
        const position = marker.getLatLng();
        document.getElementById('latitude').value = position.lat.toFixed(6);
        document.getElementById('longitude').value = position.lng.toFixed(6);
    });

    // Update marker position on map click
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        document.getElementById('latitude').value = e.latlng.lat.toFixed(6);
        document.getElementById('longitude').value = e.latlng.lng.toFixed(6);
        marker.openPopup();
    });

    // Function to update marker position programmatically
    function updateMarker(lat, lng) {
        marker.setLatLng([lat, lng]);
        map.setView([lat, lng], 15);
        document.getElementById('latitude').value = lat.toFixed(6);
        document.getElementById('longitude').value = lng.toFixed(6);
    }
});
</script>
@endpush
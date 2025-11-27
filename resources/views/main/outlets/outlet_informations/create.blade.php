@extends('layouts.app')

@section('title', 'Tambah Outlet - CuanFlow')

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
    <span class="text-gray-900 font-medium">Tambah Outlet</span>
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
        border-color: #10b981;
    }
    .toggle-checkbox:checked + .toggle-label {
        background-color: #10b981;
    }
</style>
@endpush

@section('content')
<main class="flex-grow py-8 px-4">
    <div class="max-w-7xl mx-auto">
        <x-card-container>
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-50 to-blue-50 p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                            Tambah Outlet Baru
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">Lengkapi informasi outlet yang akan ditambahkan</p>
                    </div>
                    <a href="{{ route('outlets.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg font-semibold hover:bg-gray-700 transition-all duration-200 shadow-md hover:shadow-lg">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('outlets.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column - Logo & Status -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Logo Upload -->
                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-image text-purple-500 mr-2"></i>
                                Logo Outlet
                            </h3>
                            
                            <div class="logo-preview-container mx-auto mb-4" id="logoPreviewContainer">
                                <button type="button" class="remove-logo-btn" id="removeLogo" title="Hapus Logo">
                                    <i class="fas fa-times"></i>
                                </button>
                                <div class="logo-preview-placeholder" id="logoPlaceholder">
                                    <i class="fas fa-cloud-upload-alt text-5xl mb-2"></i>
                                    <p class="text-sm font-medium">Upload Logo</p>
                                    <p class="text-xs mt-1">JPG, PNG (Max 2MB)</p>
                                </div>
                                <img id="logoPreview" style="display: none;">
                            </div>

                            <input type="file" 
                                   name="logo" 
                                   id="logoInput"
                                   accept="image/*"
                                   class="hidden">
                            
                            <button type="button" 
                                    onclick="document.getElementById('logoInput').click()"
                                    class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                                <i class="fas fa-upload mr-2"></i>
                                Pilih Logo
                            </button>
                            
                            @error('logo')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status Toggle -->
                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-toggle-on text-green-500 mr-2"></i>
                                Status Outlet
                            </h3>
                            
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Aktifkan Outlet</p>
                                    <p class="text-xs text-gray-500 mt-1">Outlet akan langsung aktif setelah dibuat</p>
                                </div>
                                <div class="relative">
                                    <input type="checkbox" 
                                           name="is_active" 
                                           id="is_active"
                                           value="1"
                                           {{ old('is_active', true) ? 'checked' : '' }}
                                           class="sr-only toggle-checkbox">
                                    <label for="is_active" class="block relative w-14 h-8 cursor-pointer">
                                        <div class="toggle-label block overflow-hidden h-8 rounded-full bg-gray-300 transition-colors duration-200"></div>
                                        <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform duration-200" id="toggleDot"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Form Fields -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Informasi Dasar -->
                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                Informasi Dasar
                            </h3>

                            <div class="space-y-4">
                                <!-- Nama Outlet -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-store mr-1"></i>
                                        Nama Outlet <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="name" 
                                           value="{{ old('name') }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                                           placeholder="Contoh: Outlet Cabang Utama"
                                           required>
                                    @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Kontak -->
                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-address-book text-green-500 mr-2"></i>
                                Informasi Kontak
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Phone -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-phone mr-1"></i>
                                        Nomor Telepon <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="phone" 
                                           value="{{ old('phone') }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror"
                                           placeholder="08123456789"
                                           required>
                                    @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-envelope mr-1"></i>
                                        Email
                                    </label>
                                    <input type="email" 
                                           name="email" 
                                           value="{{ old('email') }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                                           placeholder="outlet@example.com">
                                    @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Lokasi -->
                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-map-marked-alt text-red-500 mr-2"></i>
                                Lokasi
                            </h3>

                            <div class="space-y-4">
                                <!-- Address -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        Alamat Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="address" 
                                              rows="3"
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-500 @enderror"
                                              placeholder="Jl. Contoh No. 123, Kota, Provinsi"
                                              required>{{ old('address') }}</textarea>
                                    @error('address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Map -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-map mr-1"></i>
                                        Pilih Lokasi di Peta
                                    </label>
                                    <div id="map"></div>
                                    <p class="mt-2 text-xs text-gray-500">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Klik pada peta untuk menentukan lokasi outlet
                                    </p>
                                </div>

                                <!-- Coordinates -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            <i class="fas fa-globe mr-1"></i>
                                            Latitude
                                        </label>
                                        <input type="text" 
                                               name="latitude" 
                                               id="latitude"
                                               value="{{ old('latitude') }}"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('latitude') border-red-500 @enderror"
                                               placeholder="-6.200000"
                                               readonly>
                                        @error('latitude')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            <i class="fas fa-globe mr-1"></i>
                                            Longitude
                                        </label>
                                        <input type="text" 
                                               name="longtitude" 
                                               id="longitude"
                                               value="{{ old('longtitude') }}"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('longtitude') border-red-500 @enderror"
                                               placeholder="106.816666"
                                               readonly>
                                        @error('longtitude')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-6 mt-6 border-t border-gray-200">
                    <a href="{{ route('outlets.index') }}" 
                       class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-green-600 text-white rounded-lg font-semibold hover:bg-cuan-olive transition-colors shadow-md hover:shadow-lg">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Outlet
                    </button>
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
    const logoPreviewContainer = document.getElementById('logoPreviewContainer');

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
                logoPlaceholder.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    });

    removeLogo.addEventListener('click', function(e) {
        e.stopPropagation();
        logoInput.value = '';
        logoPreview.src = '';
        logoPreview.style.display = 'none';
        logoPlaceholder.style.display = 'block';
    });

    // Toggle Switch Handler
    const toggleCheckbox = document.getElementById('is_active');
    const toggleDot = document.getElementById('toggleDot');

    function updateToggle() {
        if (toggleCheckbox.checked) {
            toggleDot.style.transform = 'translateX(1.5rem)';
        } else {
            toggleDot.style.transform = 'translateX(0)';
        }
    }

    toggleCheckbox.addEventListener('change', updateToggle);
    updateToggle(); // Initial state

    // Leaflet Map Handler
    const defaultLat = -6.2088;
    const defaultLng = 106.8456;
    
    // Initialize map
    const map = L.map('map').setView([defaultLat, defaultLng], 13);

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

    let marker = null;

    // Function to update marker position
    function updateMarker(lat, lng) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], {icon: customIcon}).addTo(map);
            marker.bindPopup('<b>Lokasi Outlet</b><br>Geser marker untuk mengubah posisi').openPopup();
        }
        
        // Update input fields
        document.getElementById('latitude').value = lat.toFixed(6);
        document.getElementById('longitude').value = lng.toFixed(6);
    }

    // Map click event
    map.on('click', function(e) {
        updateMarker(e.latlng.lat, e.latlng.lng);
    });

    // Make marker draggable if exists
    map.on('click', function(e) {
        if (marker) {
            marker.setLatLng(e.latlng);
            document.getElementById('latitude').value = e.latlng.lat.toFixed(6);
            document.getElementById('longitude').value = e.latlng.lng.toFixed(6);
        } else {
            marker = L.marker(e.latlng, {
                icon: customIcon,
                draggable: true
            }).addTo(map);
            
            marker.bindPopup('<b>Lokasi Outlet</b><br>Geser marker untuk mengubah posisi').openPopup();
            
            marker.on('dragend', function(e) {
                const position = marker.getLatLng();
                document.getElementById('latitude').value = position.lat.toFixed(6);
                document.getElementById('longitude').value = position.lng.toFixed(6);
            });
            
            updateMarker(e.latlng.lat, e.latlng.lng);
        }
    });

    // Get user's current location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            map.setView([lat, lng], 15);
        });
    }

    // Set initial marker if old values exist
    const oldLat = document.getElementById('latitude').value;
    const oldLng = document.getElementById('longitude').value;
    if (oldLat && oldLng) {
        updateMarker(parseFloat(oldLat), parseFloat(oldLng));
        map.setView([parseFloat(oldLat), parseFloat(oldLng)], 15);
    }
});
</script>
@endpush
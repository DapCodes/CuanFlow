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
        border-color: #f97316;
    }
    .toggle-checkbox:checked + .toggle-label {
        background-color: #f97316;
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
                        <i class="fas fa-plus text-sm"></i>
                    </span>
                    <span>Tambah Outlet Baru</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Lengkapi informasi outlet yang akan ditambahkan ke sistem Anda.
                </p>
            </div>
            <a href="{{ route('outlets.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 transition-all shadow-sm">
                <i class="fas fa-arrow-left mr-2 text-xs"></i>
                Kembali
            </a>
        </section>

        {{-- FORM CARD --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            <form action="{{ route('outlets.store') }}" method="POST" enctype="multipart/form-data" class="px-4 md:px-6 py-6 space-y-8">
                @csrf

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
                                    <button type="button" class="remove-logo-btn" id="removeLogo" title="Hapus Logo">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <div class="logo-preview-placeholder" id="logoPlaceholder">
                                        <i class="fas fa-cloud-upload-alt text-5xl mb-2 text-gray-400"></i>
                                        <p class="text-sm font-medium text-gray-500">Upload Logo</p>
                                        <p class="text-xs text-gray-400 mt-1">JPG, PNG (Max 2MB)</p>
                                    </div>
                                    <img id="logoPreview" class="rounded-lg shadow-sm" style="display: none;">
                                </div>

                                <input type="file" name="logo" id="logoInput" accept="image/*" class="hidden">
                                
                                <button type="button" 
                                        onclick="document.getElementById('logoInput').click()"
                                        class="w-full px-4 py-2.5 bg-yellow-500 text-white rounded-lg text-sm font-semibold hover:bg-yellow-600 transition-colors shadow-sm">
                                    <i class="fas fa-upload mr-2"></i>
                                    Pilih Logo
                                </button>
                                
                                @error('logo')
                                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Status Toggle -->
                        <div>
                            <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-4">
                                Status Aktif
                            </h3>
                            <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Aktifkan Outlet</p>
                                        <p class="text-xs text-gray-500 mt-1">Outlet akan langsung aktif</p>
                                    </div>
                                    <div class="relative">
                                        <input type="checkbox" 
                                               name="is_active" 
                                               id="is_active"
                                               value="1"
                                               {{ old('is_active', true) ? 'checked' : '' }}
                                               class="sr-only toggle-checkbox">
                                        <label for="is_active" class="block relative w-12 h-6 cursor-pointer">
                                            <div class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 transition-colors duration-200"></div>
                                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform duration-200" id="toggleDot"></div>
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
                                           value="{{ old('name') }}"
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
                                           value="{{ old('phone') }}"
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
                                           value="{{ old('email') }}"
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
                                              id="address"
                                              rows="3"
                                              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 @error('address') border-red-500 @enderror"
                                              placeholder="Jl. Contoh No. 123, Kota, Provinsi"
                                              required>{{ old('address') }}</textarea>
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
                                               value="{{ old('latitude') }}"
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
                                               value="{{ old('longitude') }}"
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
                            <span>Simpan Outlet</span>
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
    function updateMarker(lat, lng, fetchNewAddress = false) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], {
                icon: customIcon,
                draggable: true
            }).addTo(map);
            marker.bindPopup('<b>Lokasi Outlet</b><br>Geser marker untuk mengubah posisi').openPopup();
            
            marker.on('dragend', function(e) {
                const position = marker.getLatLng();
                updateMarker(position.lat, position.lng, true);
            });
        }
        
        map.setView([lat, lng], 15);
        
        // Update input fields
        document.getElementById('latitude').value = lat.toFixed(6);
        document.getElementById('longitude').value = lng.toFixed(6);
        
        if (fetchNewAddress) {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.display_name) {
                        document.getElementById('address').value = data.display_name;
                    }
                })
                .catch(err => console.error("Error fetching address:", err));
        }
    }

    // Map click event
    map.on('click', function(e) {
        updateMarker(e.latlng.lat, e.latlng.lng, true);
    });

    // Get user's current location
    if (navigator.geolocation && !oldLat) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            updateMarker(lat, lng, true);
        });
    }

    // Set initial marker if old values exist
    if (oldLat && oldLng) {
        updateMarker(parseFloat(oldLat), parseFloat(oldLng), false);
    }

    setTimeout(() => {
        map.invalidateSize();
    }, 300);
});
</script>
@endpush
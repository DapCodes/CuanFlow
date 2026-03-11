@extends('layouts.app')

@section('title', 'Detail Outlet - ' . $outlet->name)

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
    <span class="text-gray-900 font-medium">{{ $outlet->name }}</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-xl bg-gray-100 border border-gray-200 flex items-center justify-center overflow-hidden shrink-0 shadow-sm transition-transform hover:scale-105">
                    @if($outlet->logo)
                        <img src="{{ Storage::url($outlet->logo) }}" alt="{{ $outlet->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center">
                            <i class="fas fa-store text-2xl text-white"></i>
                        </div>
                    @endif
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-xl md:text-2xl font-bold text-gray-900">{{ $outlet->name }}</h1>
                        @if($outlet->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1.5"></span>
                                Non-aktif
                            </span>
                        @endif
                    </div>
                    <div class="mt-1 flex items-center gap-3 text-sm text-gray-500">
                        <span class="font-mono bg-gray-100 px-2 py-0.5 rounded text-gray-600 text-xs tracking-wider border border-gray-200">{{ $outlet->code }}</span>
                        <span class="hidden md:inline text-gray-300">|</span>
                        <span><i class="fas fa-calendar-alt mr-1 text-gray-400"></i> Terdaftar {{ $outlet->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @php
                    $hasMultiOutlet = app(\App\Services\FeatureAccessService::class)->checkAccess(auth()->user(), 'multi_outlet')['can_access'];
                @endphp
                @if($hasMultiOutlet)
                <a href="{{ route('outlets.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 transition-all shadow-sm">
                    <i class="fas fa-arrow-left mr-2 text-xs"></i>
                    Kembali
                </a>
                @endif
                <a href="{{ route('outlets.edit', $outlet->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-yellow-500 text-white rounded-lg text-sm font-medium hover:bg-yellow-600 transition-all shadow-md">
                    <i class="fas fa-edit mr-2 text-xs"></i>
                    Edit Outlet
                </a>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informasi Kontak & Alamat -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-address-card text-blue-500"></i>
                            Informasi Kontak & Lokasi
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-6">
                                <div>
                                    <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-1.5">Nomor Telepon</p>
                                    <p class="text-sm text-gray-900 font-semibold p-3 bg-gray-50 rounded-lg border border-gray-100">
                                        <i class="fas fa-phone mr-2 text-green-500"></i>
                                        {{ $outlet->phone }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-1.5">Email</p>
                                    <p class="text-sm text-gray-900 font-semibold p-3 bg-gray-50 rounded-lg border border-gray-100">
                                        <i class="fas fa-envelope mr-2 text-blue-500"></i>
                                        {{ $outlet->email ?? '-' }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-1.5">Alamat Lengkap</p>
                                <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 min-h-[120px]">
                                    <p class="text-sm text-gray-900 font-medium leading-relaxed">
                                        {{ $outlet->address }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-8 border-t border-gray-100">
                            <div class="flex items-center justify-between mb-4">
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-widest">Lokasi GPS</p>
                                <a href="https://www.google.com/maps?q={{ $outlet->latitude }},{{ $outlet->longitude }}" 
                                   target="_blank"
                                   class="text-xs text-blue-600 hover:text-blue-700 font-semibold flex items-center gap-1">
                                    <i class="fas fa-external-link-alt"></i>
                                    Google Maps
                                </a>
                            </div>
                            <div id="map" class="border border-gray-200 rounded-xl shadow-inner bg-gray-100 overflow-hidden"></div>
                            <div class="mt-4 grid grid-cols-2 gap-4">
                                <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <p class="text-[10px] text-gray-400 uppercase font-bold mb-0.5">Latitude</p>
                                    <p class="text-xs font-mono text-gray-700">{{ $outlet->latitude }}</p>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <p class="text-[10px] text-gray-400 uppercase font-bold mb-0.5">Longitude</p>
                                    <p class="text-xs font-mono text-gray-700">{{ $outlet->longitude }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Sidebar -->
            <div class="space-y-6">
                <!-- Statistics Card -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden text-white bg-gradient-to-br from-yellow-500 via-orange-500 to-orange-600">
                    <div class="p-6">
                        <h3 class="font-bold text-white/90 flex items-center gap-2 mb-6 text-lg border-b border-white/10 pb-4">
                            <i class="fas fa-chart-pie"></i>
                            Ringkasan Outlet
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white/15 rounded-xl p-4 backdrop-blur-sm border border-white/20">
                                <p class="text-[10px] text-yellow-100 font-bold uppercase tracking-wider mb-1">Produk</p>
                                <p class="text-2xl font-bold">{{ number_format($stats['total_products']) }}</p>
                            </div>
                            <div class="bg-white/15 rounded-xl p-4 backdrop-blur-sm border border-white/20">
                                <p class="text-[10px] text-yellow-100 font-bold uppercase tracking-wider mb-1">Penjualan</p>
                                <p class="text-2xl font-bold">{{ number_format($stats['total_sales']) }}</p>
                            </div>
                            <div class="bg-white/15 rounded-xl p-4 backdrop-blur-sm border border-white/20">
                                <p class="text-[10px] text-yellow-100 font-bold uppercase tracking-wider mb-1">Bahan Baku</p>
                                <p class="text-2xl font-bold">{{ number_format($stats['total_raw_materials']) }}</p>
                            </div>
                            <div class="bg-white/15 rounded-xl p-4 backdrop-blur-sm border border-white/20">
                                <p class="text-[10px] text-yellow-100 font-bold uppercase tracking-wider mb-1">Karyawan</p>
                                <p class="text-2xl font-bold">{{ number_format($stats['total_employees']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Owner Info -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-900 flex items-center gap-2 text-sm">
                            <i class="fas fa-user-tie text-purple-500"></i>
                            Informasi Pemilik
                        </h3>
                    </div>
                    <div class="p-6 text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-full mx-auto mb-4 flex items-center justify-center border-4 border-purple-50 shadow-lg text-white text-2xl font-bold">
                            {{ strtoupper(substr($outlet->owner->name ?? 'U', 0, 1)) }}
                        </div>
                        <h4 class="font-bold text-gray-900 text-lg">{{ $outlet->owner->name ?? 'Tidak Ada Pemilik' }}</h4>
                        <p class="text-sm text-gray-500">{{ $outlet->owner->email ?? '-' }}</p>
                        <div class="mt-4 pt-4 border-t border-gray-50 flex justify-center gap-4">
                            <div class="text-center px-4">
                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest mb-1">Peran</p>
                                <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-[10px] font-bold rounded-full border border-purple-200">
                                    {{ strtoupper($outlet->owner->role ?? 'OWNER') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 space-y-4">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Aksi Pengelolaan</h3>
                    <div class="grid grid-cols-1 gap-2">
                        <form action="{{ route('outlets.toggle-status', $outlet->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-semibold transition-all {{ $outlet->is_active ? 'bg-red-50 text-red-700 hover:bg-red-100 border border-red-100' : 'bg-green-50 text-green-700 hover:bg-green-100 border border-green-100' }}">
                                <span class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-lg flex items-center justify-center {{ $outlet->is_active ? 'bg-red-100' : 'bg-green-100' }}">
                                        <i class="fas fa-power-off"></i>
                                    </span>
                                    {{ $outlet->is_active ? 'Nonaktifkan Outlet' : 'Aktifkan Outlet' }}
                                </span>
                                <i class="fas fa-chevron-right text-[10px] opacity-40"></i>
                            </button>
                        </form>
                    </div>
                    <p class="text-[10px] text-gray-500 text-center leading-relaxed">
                        <i class="fas fa-info-circle mr-1"></i>
                        Status non-aktif akan menghentikan akses operasional pada outlet ini untuk sementara.
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map {
        height: 300px;
        z-index: 1;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const lat = {{ $outlet->latitude ?? -6.2088 }};
    const lng = {{ $outlet->longitude ?? 106.8456 }};
    
    const map = L.map('map').setView([lat, lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const customIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    L.marker([lat, lng], {icon: customIcon}).addTo(map)
        .bindPopup('<b>{{ $outlet->name }}</b><br>{{ $outlet->address }}').openPopup();

    setTimeout(() => {
        map.invalidateSize();
    }, 300);
});
</script>
@endpush
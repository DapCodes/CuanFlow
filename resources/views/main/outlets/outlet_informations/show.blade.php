@extends('layouts.app')

@section('title', 'Detail Outlet - ' . $outlet->name)

@section('breadcrumb')
<li class="flex items-center text-gray-400 mx-2">/</li>
<li class="flex items-center">
    <a href="{{ route('outlets.index') }}" class="text-gray-600 hover:text-gray-900 font-medium tracking-tight">Informasi Outlet</a>
</li>
<li class="flex items-center text-gray-400 mx-2">/</li>
<li class="flex items-center">
    <span class="text-gray-900 font-medium tracking-tight">{{ $outlet->name }}</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-6">
                @if($outlet->logo)
                    <img src="{{ Storage::url($outlet->logo) }}" alt="{{ $outlet->name }}" class="w-16 h-16 rounded-2xl object-cover border-4 border-white shadow-lg">
                @else
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-cuan-green to-cuan-dark flex items-center justify-center border-4 border-white shadow-lg text-white text-xl font-black">
                        {{ substr($outlet->name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-2xl md:text-3xl font-black text-gray-900 uppercase tracking-tighter">{{ $outlet->name }}</h1>
                        @if($outlet->is_active)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-cuan-green/10 text-cuan-green border border-cuan-green/10">
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-100 text-gray-400 border border-gray-200">
                                Nonaktif
                            </span>
                        @endif
                    </div>
                    <div class="mt-1 flex items-center gap-3 text-xs font-bold text-gray-400 uppercase tracking-widest">
                        <span class="font-mono">{{ $outlet->code }}</span>
                        <span class="text-gray-300">•</span>
                        <span>Sejak {{ $outlet->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @php
                    $hasMultiOutlet = app(\App\Services\FeatureAccessService::class)->checkAccess(auth()->user(), 'multi_outlet')['can_access'];
                @endphp
                @if($hasMultiOutlet)
                <a href="{{ route('outlets.index') }}" class="inline-flex items-center justify-center h-11 px-6 bg-white text-gray-700 border border-gray-200 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-gray-50 transition-all active:scale-95 shadow-sm">
                    Kembali
                </a>
                @endif
                <a href="{{ route('outlets.edit', $outlet->id) }}" class="inline-flex items-center justify-center h-11 px-6 bg-cuan-green text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-cuan-dark transition-all active:scale-95 shadow-lg shadow-cuan-green/20">
                    Edit Outlet
                </a>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informasi Kontak & Alamat -->
                <x-card-container>
                    <div class="px-6 py-5 border-b border-gray-50 bg-gray-50/30">
                        <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-cuan-green text-sm"></i>
                            Informasi Kontak & Lokasi
                        </h3>
                    </div>
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <div class="space-y-8">
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Nomor Telepon</p>
                                    <div class="text-sm text-gray-900 font-black p-4 bg-gray-50 rounded-2xl border border-gray-100 flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-cuan-green/10 flex items-center justify-center text-cuan-green">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        {{ $outlet->phone }}
                                    </div>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Email Outlet</p>
                                    <div class="text-sm text-gray-900 font-black p-4 bg-gray-50 rounded-2xl border border-gray-100 flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        {{ $outlet->email ?? '-' }}
                                    </div>
                                </div>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Alamat Lengkap</p>
                                <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100 min-h-[148px]">
                                    <p class="text-[13px] text-gray-900 font-bold leading-relaxed">
                                        {{ $outlet->address }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 pt-10 border-t border-gray-100">
                            <div class="flex items-center justify-between mb-6">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Titik Koordinat GPS</p>
                                <a href="https://www.google.com/maps?q={{ $outlet->latitude }},{{ $outlet->longitude }}" 
                                   target="_blank"
                                   class="text-[10px] text-blue-500 hover:text-blue-600 font-black uppercase tracking-widest flex items-center gap-2">
                                    Lihat di Google Maps
                                    <i class="fas fa-external-link-alt text-[8px]"></i>
                                </a>
                            </div>
                            <div id="map" class="border-4 border-white rounded-3xl shadow-xl bg-gray-100 overflow-hidden ring-1 ring-gray-100"></div>
                            <div class="mt-6 grid grid-cols-2 gap-4">
                                <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                    <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-1">Latitude</p>
                                    <p class="text-xs font-mono font-bold text-gray-700 tracking-tight">{{ $outlet->latitude }}</p>
                                </div>
                                <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                    <p class="text-[9px] text-gray-400 uppercase font-black tracking-widest mb-1">Longitude</p>
                                    <p class="text-xs font-mono font-bold text-gray-700 tracking-tight">{{ $outlet->longitude }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-card-container>
            </div>

            <!-- Right Column: Sidebar -->
            <div class="space-y-6">
                <!-- Statistics Card -->
                <x-card-container class="bg-gradient-to-br from-cuan-green to-cuan-dark border-none shadow-xl shadow-cuan-green/20">
                    <div class="p-8">
                        <h3 class="text-xs font-black text-white/50 uppercase tracking-widest flex items-center gap-2 mb-8">
                            Ringkasan Performa
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white/10 rounded-2xl p-5 border border-white/10 backdrop-blur-sm">
                                <p class="text-[9px] text-white/40 font-black uppercase tracking-widest mb-1">Produk</p>
                                <p class="text-2xl font-black text-white">{{ number_format($stats['total_products']) }}</p>
                            </div>
                            <div class="bg-white/10 rounded-2xl p-5 border border-white/10 backdrop-blur-sm">
                                <p class="text-[9px] text-white/40 font-black uppercase tracking-widest mb-1">Penjualan</p>
                                <p class="text-2xl font-black text-white">{{ number_format($stats['total_sales']) }}</p>
                            </div>
                            <div class="bg-white/10 rounded-2xl p-5 border border-white/10 backdrop-blur-sm">
                                <p class="text-[9px] text-white/40 font-black uppercase tracking-widest mb-1">Bahan Baku</p>
                                <p class="text-2xl font-black text-white">{{ number_format($stats['total_raw_materials']) }}</p>
                            </div>
                            <div class="bg-white/10 rounded-2xl p-5 border border-white/10 backdrop-blur-sm">
                                <p class="text-[9px] text-white/40 font-black uppercase tracking-widest mb-1">Karyawan</p>
                                <p class="text-2xl font-black text-white">{{ number_format($stats['total_employees']) }}</p>
                            </div>
                        </div>
                    </div>
                </x-card-container>

                <!-- Owner Info -->
                <x-card-container>
                    <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/30">
                        <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-user-tie text-purple-500"></i>
                            Informasi Pemilik
                        </h3>
                    </div>
                    <div class="p-8 text-center">
                        <div class="w-20 h-20 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-2xl mx-auto mb-6 flex items-center justify-center border-4 border-white shadow-lg text-purple-600 text-2xl font-black">
                            {{ strtoupper(substr($outlet->owner->name ?? 'U', 0, 1)) }}
                        </div>
                        <h4 class="font-black text-gray-900 text-lg uppercase tracking-tight">{{ $outlet->owner->name ?? '-' }}</h4>
                        <p class="text-[11px] font-bold text-gray-400 mt-1">{{ $outlet->owner->email ?? '-' }}</p>
                        
                        <div class="mt-8 pt-6 border-t border-gray-50">
                            <span class="px-4 py-2 bg-purple-50 text-purple-600 text-[10px] font-black uppercase tracking-wider rounded-xl border border-purple-100">
                                {{ strtoupper($outlet->owner->role ?? 'OWNER') }}
                            </span>
                        </div>
                    </div>
                </x-card-container>

                <!-- Quick Actions -->
                <x-card-container>
                    <div class="p-6 space-y-4">
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Aksi Pengelolaan</h3>
                        <form action="{{ route('outlets.toggle-status', $outlet->id) }}" method="POST" class="confirm-toggle" data-name="{{ $outlet->name }}" data-status="{{ $outlet->is_active ? 'nonaktifkan' : 'aktifkan' }}">
                            @csrf
                            <button type="submit" class="w-full h-14 group flex items-center justify-between px-5 rounded-2xl text-xs font-black uppercase tracking-widest transition-all {{ $outlet->is_active ? 'bg-red-50 text-red-600 hover:bg-red-500 hover:text-white border border-red-100' : 'bg-cuan-green/10 text-cuan-green hover:bg-cuan-green hover:text-white border border-cuan-green/10' }}">
                                <span class="flex items-center gap-4">
                                    <i class="fas fa-power-off text-sm opacity-60"></i>
                                    {{ $outlet->is_active ? 'Nonaktifkan' : 'Aktifkan Detail' }}
                                </span>
                                <i class="fas fa-chevron-right text-[10px] opacity-20 transform group-hover:translate-x-1 transition-transform"></i>
                            </button>
                        </form>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest text-center leading-loose px-2">
                            <i class="fas fa-info-circle mr-1 text-cuan-green/40"></i>
                            Status non-aktif akan menghentikan akses operasional sistem.
                        </p>
                    </div>
                </x-card-container>
            </div>
        </div>
    </div>
</main>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map {
        height: 320px;
        width: 100%;
        z-index: 1;
    }
    .leaflet-container img.leaflet-tile {
        max-width: none !important;
        max-height: none !important;
    }
    .leaflet-container {
        font-family: inherit;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const lat = {{ $outlet->latitude ?? -6.2088 }};
    const lng = {{ $outlet->longitude ?? 106.8456 }};
    
    const map = L.map('map', {
        zoomControl: false,
        attributionControl: false
    }).setView([lat, lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    const customIcon = L.divIcon({
        className: 'custom-marker',
        html: `<div class="w-8 h-8 rounded-full bg-cuan-green border-4 border-white shadow-xl flex items-center justify-center text-white"><i class="fas fa-store text-xs"></i></div>`,
        iconSize: [32, 32],
        iconAnchor: [16, 16]
    });

    L.marker([lat, lng], {icon: customIcon}).addTo(map);

    // Comprehensive map resize fix
    const fixMapLayout = () => { if (map) map.invalidateSize(); };
    window.addEventListener('load', fixMapLayout);
    [100, 500, 1000].forEach(delay => setTimeout(fixMapLayout, delay));
});
</script>
@endpush
@extends('admin.layouts.app')

@section('title', 'Detail Outlet')
@section('page-title', 'Detail Outlet: ' . $outlet->name)

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <a href="{{ route('admin.outlets.index') }}" class="text-gray-500 hover:text-gray-700">Daftar Outlet</a>
</li>
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <span class="text-gray-700">Detail</span>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Map Card -->
    @if($outlet->latitude && $outlet->longitude)
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-map-marked-alt text-red-500"></i>
                Lokasi Outlet
            </h3>
            <a href="https://www.google.com/maps?q={{ $outlet->latitude }},{{ $outlet->longitude }}" target="_blank" class="text-xs text-blue-600 hover:text-blue-700 font-semibold flex items-center gap-1">
                <i class="fas fa-external-link-alt"></i> Google Maps
            </a>
        </div>
        <div class="p-0">
            <div id="map" style="height: 300px; width: 100%; z-index: 1;"></div>
        </div>
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex gap-6 text-[10px] font-mono text-gray-500">
            <span>Lat: {{ $outlet->latitude }}</span>
            <span>Lng: {{ $outlet->longitude }}</span>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
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
            const lat = {{ $outlet->latitude }};
            const lng = {{ $outlet->longitude }};
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
                .bindPopup('<b>{{ $outlet->name }}</b>').openPopup();

            const fixMapLayout = () => {
                map.invalidateSize();
                map.setView([lat, lng], 15);
            };

            [500, 1000, 2000].forEach(delay => setTimeout(fixMapLayout, delay));
            window.addEventListener('load', fixMapLayout);
            window.addEventListener('focus', fixMapLayout);
        });
    </script>
    @endpush
    @endif

    <!-- Header Card -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="px-6 py-8 flex flex-col md:flex-row gap-8 items-start">
            <div class="w-24 h-24 rounded-2xl bg-teal-100 flex items-center justify-center flex-shrink-0">
                @if($outlet->logo)
                    <img src="{{ Storage::url($outlet->logo) }}" alt="{{ $outlet->name }}" class="w-20 h-20 object-contain">
                @else
                    <i class="fas fa-store text-teal-600 text-4xl"></i>
                @endif
            </div>
            
            <div class="flex-1 space-y-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $outlet->name }}</h2>
                        <p class="text-gray-500 flex items-center gap-2 mt-1">
                            <i class="fas fa-barcode"></i>
                            <span class="font-mono">{{ $outlet->code }}</span>
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <form action="{{ route('admin.outlets.toggle-status', $outlet) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold transition-all {{ $outlet->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                <i class="fas {{ $outlet->is_active ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                <span>{{ $outlet->is_active ? 'Status: Aktif' : 'Status: Nonaktif' }}</span>
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Alamat</p>
                        <p class="text-sm text-gray-700">{{ $outlet->address ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Kontak</p>
                        <p class="text-sm text-gray-700">{{ $outlet->phone ?? '-' }} / {{ $outlet->email ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Owner</p>
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-cuan-dark text-white text-[10px] flex items-center justify-center">
                                {{ substr($outlet->owner->name ?? '?', 0, 1) }}
                            </div>
                            <p class="text-sm text-gray-700">{{ $outlet->owner->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <p class="text-gray-500 text-sm font-medium">Total Penjualan</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($outlet->sales()->count()) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <p class="text-gray-500 text-sm font-medium">Total Produk</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($outlet->products->count()) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <p class="text-gray-500 text-sm font-medium">Bahan Baku</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($outlet->rawMaterials->count()) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <p class="text-gray-500 text-sm font-medium">Total Staf</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($outlet->users->count()) }}</p>
        </div>
    </div>

    <!-- Details Tabs -->
    <div x-data="{ activeTab: 'staff' }" class="space-y-4">
        <div class="flex border-b border-gray-200 gap-6">
            <button @click="activeTab = 'staff'" :class="activeTab === 'staff' ? 'border-cuan-green text-cuan-green' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-4 px-2 border-b-2 font-semibold transition-all">
                Daftar Staf
            </button>
            <button @click="activeTab = 'products'" :class="activeTab === 'products' ? 'border-cuan-green text-cuan-green' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-4 px-2 border-b-2 font-semibold transition-all">
                Produk
            </button>
            <button @click="activeTab = 'raw_materials'" :class="activeTab === 'raw_materials' ? 'border-cuan-green text-cuan-green' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-4 px-2 border-b-2 font-semibold transition-all">
                Bahan Baku
            </button>
        </div>

        <!-- Staff Tab -->
        <div x-show="activeTab === 'staff'" class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Role</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm">
                    @forelse($outlet->users as $user)
                    <tr>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-4 text-center">
                            @foreach($user->roles as $role)
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-[10px] font-bold uppercase">{{ $role->name }}</span>
                            @endforeach
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">Belum ada staf</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Products Tab -->
        <div x-show="activeTab === 'products'" class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Harga Jual</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm">
                    @forelse($outlet->products as $product)
                    <tr>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $product->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $product->category->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-right">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">Belum ada produk</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Raw Materials Tab -->
        <div x-show="activeTab === 'raw_materials'" class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Bahan Baku</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Unit</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Harga Beli</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm">
                    @forelse($outlet->rawMaterials as $rm)
                    <tr>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $rm->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $rm->unit->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-right">Rp {{ number_format($rm->purchase_price, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">Belum ada bahan baku</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

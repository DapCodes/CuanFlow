@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Peta Transaksi - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Laporan</span>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Peta Transaksi</span>
</li>
@endsection

@push('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <!-- Leaflet MarkerCluster CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />

    <style>
        #sales-map {
            z-index: 10;
        }
        
        .kasir-item {
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }

        .kasir-item:hover, .kasir-item.active {
            background-color: #f3f4f6;
            border-left: 4px solid #10b981;
        }

        .kasir-item.active {
            background-color: #ecfdf5;
        }

        /* Marker cluster custom style to fit CuanFlow look */
        .marker-cluster-small, .marker-cluster-medium, .marker-cluster-large {
            background-color: rgba(16, 185, 129, 0.6); 
        }
        .marker-cluster-small div, .marker-cluster-medium div, .marker-cluster-large div {
            background-color: rgba(5, 150, 105, 0.8);
            color: white;
            font-weight: bold;
        }
        
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
@endpush

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        
        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="space-y-1">
                <h1 class="text-xl md:text-2xl font-black text-gray-900 leading-tight">
                    Peta Analisis Transaksi
                </h1>
                <p class="text-[10px] md:text-sm text-gray-500 font-medium leading-relaxed">
                    Pantau sebaran lokasi transaksi dan rute tim lapangan.
                </p>
            </div>
            
            <div class="w-full md:w-auto">
                <form id="filterForm" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full md:w-auto">
                    <div class="flex items-center gap-2 flex-grow">
                        <input type="date" id="start_date" name="start_date" value="{{ request('start_date', now()->format('Y-m-d')) }}" 
                               class="flex-1 min-w-0 bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs md:text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all font-bold text-gray-700 shadow-sm">
                        <span class="text-gray-400 font-black text-[9px] uppercase tracking-tighter shrink-0">S/D</span>
                        <input type="date" id="end_date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}"
                               class="flex-1 min-w-0 bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs md:text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all font-bold text-gray-700 shadow-sm">
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-cuan-green px-6 py-2.5 text-xs md:text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95 whitespace-nowrap">
                        <i class="ph-bold ph-funnel"></i>
                        <span>Filter</span>
                    </button>
                </form>
            </div>
        </section>

        <!-- Banner Info Premium (Responsive) -->
        <div class="group bg-white border border-gray-100 rounded-2xl p-3 md:p-4 flex gap-3 md:gap-4 shadow-sm hover:shadow-md transition-all">
            <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <i class="ph-fill ph-info text-emerald-600 text-xl md:text-2xl"></i>
            </div>
            <div>
                <h4 class="font-black text-gray-900 text-[10px] md:text-sm mb-0.5 md:mb-1 uppercase tracking-tight">Wawasan Geospasial</h4>
                <p class="text-gray-500 text-[9px] md:text-xs font-medium leading-relaxed">
                    Klik marker untuk detail. Pilih Karyawan di sidebar bawah untuk visualisasi Rute AI.
                </p>
            </div>
        </div>

        {{-- MAIN INTERFACE --}}
        <div class="grid grid-cols-12 gap-4 md:gap-6">
            
            <!-- Map (75%) -->
            <div class="col-span-12 lg:col-span-9 space-y-4">
                <div class="bg-white p-1.5 md:p-2 rounded-[1.5rem] md:rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden relative group">
                    <div id="sales-map" class="w-full h-[400px] md:h-[650px] rounded-[1rem] md:rounded-[1.5rem] z-10 border border-gray-50"></div>
                    
                    <!-- Floating Overlays -->
                    <div class="absolute top-4 right-4 md:top-6 md:right-6 z-[1000] flex flex-col gap-2">
                        <button id="reset-map-btn" 
                                class="bg-white/90 backdrop-blur-md p-2.5 md:p-3.5 rounded-xl md:rounded-2xl shadow-2xl border border-gray-200 text-gray-700 hover:text-emerald-600 hover:bg-white transition-all active:scale-95 group/btn"
                                title="Reset Tampilan Peta">
                            <i class="ph-bold ph-arrows-out text-base md:text-xl group-hover/btn:scale-110 transition-transform"></i>
                        </button>
                    </div>
                    
                    <div class="absolute bottom-4 left-4 md:bottom-6 md:left-6 z-[1000]">
                        <div id="map-status" class="bg-white/90 backdrop-blur-md px-3 py-2 md:px-5 md:py-2.5 rounded-xl md:rounded-2xl shadow-2xl border border-gray-200 text-[8px] md:text-[10px] font-black uppercase tracking-[0.1em] md:tracking-[0.15em] text-gray-600 flex items-center gap-2 md:gap-3">
                            <span class="relative flex h-2 w-2 md:h-2.5 md:w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 md:h-2.5 md:w-2.5 bg-emerald-500"></span>
                            </span>
                            <span class="hidden sm:inline">Sistem Peta Terhubung</span>
                            <span class="sm:hidden">Sistem Peta</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar (25%) -->
            <div class="col-span-12 lg:col-span-3">
                <div class="bg-white rounded-[1.5rem] md:rounded-[2rem] shadow-xl border border-gray-100 flex flex-col h-[450px] md:h-[666px] overflow-hidden">
                    <div class="p-4 md:p-6 border-b border-gray-50 bg-gray-50/30">
                        <h3 class="font-black text-gray-900 uppercase tracking-widest text-[10px] md:text-[11px] flex items-center gap-2">
                            <i class="ph-fill ph-users-three text-emerald-600 text-lg"></i>
                            Tim Lapangan
                        </h3>
                        <p class="text-[8px] md:text-[9px] text-gray-400 font-black mt-1 uppercase tracking-tighter">Berdasarkan Aktivitas</p>
                    </div>
                    
                    <div id="cashier-list" class="flex-grow overflow-y-auto p-3 md:p-4 space-y-2 md:space-y-3 custom-scrollbar">
                        {{-- AJAX Loaded --}}
                        <div class="flex flex-col items-center justify-center h-full text-gray-400 py-10 opacity-50">
                            <i class="ph ph-spinner animate-spin text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@push('scripts')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <!-- Leaflet MarkerCluster JS -->
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

    <script>
        let map;
        let markerCluster;
        let routeLayer;
        const statusEl = document.getElementById('map-status');
        const cashierListContainer = document.getElementById('cashier-list');
        const filterForm = document.getElementById('filterForm');
        const resetBtn = document.getElementById('reset-map-btn');

        // Formatter
        const idrFormatter = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 });

        // Outlet Fallback Data
        const outletLat = {{ auth()->user()->outlet->latitude ?? 'null' }};
        const outletLng = {{ auth()->user()->outlet->longitude ?? 'null' }};

        let userMarker;
        let outletMarker;

        // Initialize Map
        function initMap() {
            // Default: Indonesia Center
            const defaultLat = -2.5489;
            const defaultLng = 118.0149;
            const defaultZoom = 5;

            map = L.map('sales-map').setView([defaultLat, defaultLng], defaultZoom);
            
            // 1. Plot Outlet Marker if exists
            if (outletLat && outletLng) {
                const outletIcon = L.divIcon({
                    html: `<div class="flex items-center justify-center w-8 h-8 bg-blue-600 rounded-lg shadow-lg border-2 border-white">
                             <i class="ph-fill ph-storefront text-white text-lg"></i>
                           </div>`,
                    className: '',
                    iconSize: [32, 32],
                    iconAnchor: [16, 16]
                });
                outletMarker = L.marker([outletLat, outletLng], { icon: outletIcon })
                    .addTo(map)
                    .bindPopup('<div class="font-black text-[10px] uppercase tracking-widest text-gray-400 mb-1">Lokasi Outlet</div><div class="font-bold text-sm">{{ auth()->user()->outlet->name }}</div>');
            }

            // 2. Try to get user GPS & Plot User Marker
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const uLat = position.coords.latitude;
                        const uLng = position.coords.longitude;
                        
                        const userIcon = L.divIcon({
                            html: `<div class="relative flex items-center justify-center">
                                     <div class="absolute w-6 h-6 bg-blue-500 rounded-full animate-ping opacity-25"></div>
                                     <div class="relative w-4 h-4 bg-blue-600 border-2 border-white rounded-full shadow-md"></div>
                                   </div>`,
                            className: '',
                            iconSize: [24, 24],
                            iconAnchor: [12, 12]
                        });
                        
                        userMarker = L.marker([uLat, uLng], { icon: userIcon })
                            .addTo(map)
                            .bindPopup('<div class="font-black text-[10px] uppercase tracking-widest text-blue-400 mb-1">Posisi Anda</div><div class="font-bold text-sm text-blue-600">Lokasi Saat Ini</div>');

                        map.setView([uLat, uLng], 12);
                    }, 
                    function(error) {
                        if (outletLat && outletLng) {
                            map.setView([outletLat, outletLng], 13);
                        }
                    }
                );
            } else if (outletLat && outletLng) {
                map.setView([outletLat, outletLng], 13);
            }
            
            // Premium style: CartoDB Voyager or Positron (clean background to highlight data)
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                subdomains: 'abcd',
                maxZoom: 20
            }).addTo(map);

            markerCluster = L.markerClusterGroup({
                maxClusterRadius: 50,
                spiderfyOnMaxZoom: true,
                showCoverageOnHover: false,
                zoomToBoundsOnClick: true
            });
            
            map.addLayer(markerCluster);
        }

        // Fetch Data from Backend
        async function fetchMapData() {
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData).toString();
            
            statusEl.innerHTML = '<i class="ph ph-spinner animate-spin text-emerald-500 mr-2"></i> Sinkronisasi Data...';
            
            try {
                const response = await fetch(`{{ route('sales-map.data') }}?${params}`);
                const data = await response.json();
                
                if (data.success) {
                    renderMarkers(data.sales);
                    renderCashierList(data.cashiers);
                    statusEl.innerHTML = `<span class="relative flex h-2 w-2 mr-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span></span> ${data.sales.length} Transaksi Terdeteksi`;
                }
            } catch (error) {
                console.error('Error fetching data:', error);
                statusEl.innerHTML = '<i class="fas fa-exclamation-triangle text-red-500 mr-1"></i> Gagal Memuat Data';
            }
        }

        // Plot markers to map
        function renderMarkers(sales) {
            markerCluster.clearLayers();
            if (routeLayer) map.removeLayer(routeLayer);

            const bounds = L.latLngBounds();

            sales.forEach(sale => {
                const pos = [sale.latitude, sale.longitude];
                
                // Custom Pin HTML
                const htmlPin = `
                    <div class="relative flex items-center justify-center">
                        <div class="absolute w-8 h-8 bg-emerald-500/20 rounded-full animate-pulse"></div>
                        <div class="relative bg-white border-2 border-emerald-500 w-5 h-5 rounded-full shadow-lg flex items-center justify-center">
                            <div class="w-2 h-2 bg-emerald-600 rounded-full"></div>
                        </div>
                    </div>
                `;

                const icon = L.divIcon({
                    html: htmlPin,
                    className: '',
                    iconSize: [20, 20],
                    iconAnchor: [10, 10]
                });

                const marker = L.marker(pos, { icon: icon });
                
                const popupContent = `
                    <div class="p-1">
                        <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Invoice</div>
                        <div class="font-black text-gray-900 mb-2">${sale.invoice_number}</div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-[9px] font-black uppercase text-gray-400">Total</div>
                                <div class="text-xs font-bold text-emerald-600">${idrFormatter.format(sale.grand_total)}</div>
                            </div>
                            <div>
                                <div class="text-[9px] font-black uppercase text-gray-400">Kasir</div>
                                <div class="text-xs font-bold text-gray-700">${sale.cashier_name}</div>
                            </div>
                        </div>
                        <div class="mt-2 pt-2 border-t border-gray-100 text-[9px] text-gray-400 italic">
                            ${sale.created_at}
                        </div>
                    </div>
                `;

                marker.bindPopup(popupContent);
                markerCluster.addLayer(marker);
                bounds.extend(pos);
            });

            if (sales.length > 0) {
                map.fitBounds(bounds, { padding: [50, 50], maxZoom: 15 });
            }
        }

        // Populate sidebar
        function renderCashierList(cashiers) {
            cashierListContainer.innerHTML = '';
            
            if (cashiers.length === 0) {
                cashierListContainer.innerHTML = `
                    <div class="py-10 text-center opacity-40">
                        <i class="ph ph-user-minus text-3xl mb-2"></i>
                        <p class="text-[9px] font-black uppercase tracking-widest">Tidak Ada Aktivitas</p>
                    </div>
                `;
                return;
            }

            cashiers.forEach(cashier => {
                const item = document.createElement('div');
                item.className = 'kasir-item p-4 rounded-2xl border border-gray-100 bg-white shadow-sm flex items-center justify-between group active:scale-95 transition-all';
                item.dataset.id = cashier.id;
                
                item.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-black text-xs shadow-inner" style="background-color: ${cashier.color || '#10b981'}">
                            ${cashier.name.substring(0, 2).toUpperCase()}
                        </div>
                        <div>
                            <div class="text-xs font-black text-gray-900 truncate max-w-[120px]">${cashier.name}</div>
                            <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-0.5">${cashier.total_sales} Transaksi</div>
                        </div>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                        <i class="ph ph-caret-right font-bold"></i>
                    </div>
                `;
                
                item.onclick = () => selectCashier(cashier.id, item);
                cashierListContainer.appendChild(item);
            });
        }

        // Handle cashier selection and routing
        async function selectCashier(id, element) {
            // UI Toggle
            document.querySelectorAll('.kasir-item').forEach(el => el.classList.remove('active', 'border-emerald-500', 'bg-emerald-50/50'));
            element.classList.add('active', 'border-emerald-500', 'bg-emerald-50/50');

            // Fetch specific data for routing
            const formData = new FormData(filterForm);
            formData.append('cashier_id', id);
            const params = new URLSearchParams(formData).toString();
            
            try {
                const response = await fetch(`{{ route('sales-map.data') }}?${params}`);
                const data = await response.json();
                
                if (data.success && data.sales.length > 1) {
                    drawRoute(data.sales);
                } else {
                    if (routeLayer) map.removeLayer(routeLayer);
                    if (data.sales.length === 1) {
                        map.setView([data.sales[0].latitude, data.sales[0].longitude], 16);
                    }
                }
            } catch (error) {
                console.error('Error fetching route:', error);
            }
        }

        // OSRM Routing logic
        async function drawRoute(sales) {
            if (routeLayer) map.removeLayer(routeLayer);
            
            // Limit points for OSRM API (max 50 to avoid URL length issues/rate limits)
            const sampledSales = sales.length > 50 ? 
                                sales.filter((_, i) => i % Math.ceil(sales.length / 50) === 0) : 
                                sales;

            const coords = sampledSales.map(s => `${s.longitude},${s.latitude}`).join(';');
            const url = `https://router.project-osrm.org/route/v1/driving/${coords}?overview=full&geometries=geojson`;

            statusEl.innerHTML = '<i class="ph ph-path animate-pulse text-blue-500 mr-2"></i> Menghitung Rute AI...';

            try {
                const response = await fetch(url);
                const data = await response.json();
                
                if (data.code === 'Ok') {
                    const geometry = data.routes[0].geometry;
                    
                    routeLayer = L.geoJSON(geometry, {
                        style: {
                            color: '#3b82f6',
                            weight: 5,
                            opacity: 0.7,
                            dashArray: '10, 10',
                            lineCap: 'round'
                        }
                    }).addTo(map);

                    map.fitBounds(routeLayer.getBounds(), { padding: [50, 50] });
                    statusEl.innerHTML = '<i class="ph-fill ph-check-circle text-emerald-500 mr-2"></i> Rute Berhasil Dibuat';
                }
            } catch (error) {
                console.error('Routing error:', error);
                statusEl.innerHTML = '<i class="ph ph-warning text-amber-500 mr-2"></i> Rute Layanan Sedang Sibuk';
                
                // Fallback: simple polyline
                const simpleCoords = sales.map(s => [s.latitude, s.longitude]);
                routeLayer = L.polyline(simpleCoords, { color: '#94a3b8', weight: 3, dashArray: '5, 5' }).addTo(map);
            }
        }

        // Bootstrap
        document.addEventListener('DOMContentLoaded', () => {
            initMap();
            fetchMapData();
            
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                fetchMapData();
            });

            resetBtn.addEventListener('click', function() {
                // Clear selection
                document.querySelectorAll('.kasir-item').forEach(el => el.classList.remove('active', 'border-emerald-500', 'bg-emerald-50/50'));
                if (routeLayer) map.removeLayer(routeLayer);
                fetchMapData();
            });
        });
    </script>
@endpush
@endsection

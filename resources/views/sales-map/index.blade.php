@extends($preferredLayout ?? 'layouts.app')

@section('title', 'Peta Analisis Transaksi')

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
    </style>
@endpush

@section('content')
<div class="space-y-6 animate-fade-in-up">
    <!-- Header Page & Filter -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold leading-tight text-gray-900 flex items-center gap-2">
                <i class="ph-light ph-map-pin-line text-emerald-600 bg-emerald-50 p-2 rounded-xl"></i>
                Peta Analisis Transaksi
            </h2>
            <nav class="flex mt-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="ph ph-caret-right text-gray-400 text-sm mx-1"></i>
                            <span class="text-sm font-medium text-gray-500">Laporan</span>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="ph ph-caret-right text-gray-400 text-sm mx-1"></i>
                            <span class="text-sm font-bold text-gray-900" aria-current="page">Peta Transaksi</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
        
        <!-- Filter Date -->
        <div class="flex items-center gap-3">
            <form id="filterForm" class="flex gap-2">
                <input type="date" id="start_date" name="start_date" value="{{ request('start_date', now()->format('Y-m-d')) }}" 
                       class="bg-white border text-sm border-gray-300 text-gray-900 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5">
                <span class="self-center text-gray-500 text-sm">ke</span>
                <input type="date" id="end_date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}"
                       class="bg-white border text-sm border-gray-300 text-gray-900 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg text-sm px-4 py-2 opacity-90 transition-opacity whitespace-nowrap">
                    <i class="ph ph-funnel mr-1"></i> Filter
                </button>
            </form>
        </div>
    </div>

    <!-- Info Banner -->
    <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4 flex gap-4 shadow-sm items-start">
        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
            <i class="ph-fill ph-info text-emerald-600 text-xl"></i>
        </div>
        <div>
            <h4 class="font-bold text-emerald-900 mb-1">Rute dan Analisis Lokasi</h4>
            <p class="text-emerald-700 text-sm">
                Lihat sebaran transaksi penjualan dari outlet ini. Klik pada nama kasir di sebelah kanan untuk melihat rute perjalanan historis yang mereka tempuh untuk transaksi.
            </p>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-12 gap-6">
        
        <!-- Left: Map Container -->
        <div class="col-span-12 xl:col-span-9 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col h-[700px]">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="ph-fill ph-map-trifold text-emerald-500"></i> Peta Interaktif
                </h3>
                <span id="map-status" class="text-xs font-medium text-gray-500 bg-white px-2 py-1 rounded-md border border-gray-200 shadow-sm">
                    Memuat data...
                </span>
            </div>
            
            <div id="sales-map" class="w-full flex-1 relative">
                <!-- Map renders here -->
            </div>
        </div>

        <!-- Right: Cashiers Sidebar -->
        <div class="col-span-12 xl:col-span-3 bg-white rounded-2xl border border-gray-200 shadow-sm flex flex-col h-[700px]">
            <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="ph-fill ph-users text-emerald-500"></i> Karyawan
                </h3>
                <p class="text-xs text-gray-500 mt-1">Pilih kasir untuk melihat rute traksaksinya</p>
            </div>
            
            <div class="flex-1 overflow-y-auto p-2" id="cashier-list">
                <!-- Skeleton Loading -->
                <div class="animate-pulse flex space-x-4 p-3 mb-2">
                    <div class="rounded-full bg-slate-200 h-10 w-10"></div>
                    <div class="flex-1 space-y-3 py-1">
                        <div class="h-2 bg-slate-200 rounded"></div>
                        <div class="space-y-3">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="h-2 bg-slate-200 rounded col-span-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-3 border-t border-gray-100">
                <button id="reset-map-btn" class="w-full py-2 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors text-sm flex justify-center items-center gap-2 shadow-sm">
                    <i class="ph ph-arrows-clockwise"></i> Reset Tampilan Peta
                </button>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<!-- Leaflet MarkerCluster JS -->
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let map;
        let markerCluster;
        let routeLayer;
        let allSales = [];
        let allCashiers = [];
        let activeCashierId = null;
        
        const mapContainer = document.getElementById('sales-map');
        const cashierListContainer = document.getElementById('cashier-list');
        const filterForm = document.getElementById('filterForm');
        const statusEl = document.getElementById('map-status');
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
                    .bindPopup('<div class="font-bold text-sm">Lokasi Outlet: {{ auth()->user()->outlet->name }}</div>');
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
                            .bindPopup('<div class="font-bold text-sm text-blue-600">Lokasi Anda Sekarang</div>');

                        map.setView([uLat, uLng], 12);
                        statusEl.innerHTML += ' <span class="text-xs text-gray-400 font-normal">(GPS Terdeteksi)</span>';
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

        // Fetch Data
        async function fetchMapData() {
            try {
                const startDate = document.getElementById('start_date').value;
                const endDate = document.getElementById('end_date').value;
                
                statusEl.innerHTML = '<i class="fas fa-spinner fa-spin text-emerald-500 mr-1"></i> Memuat data...';
                
                const response = await fetch(`{{ route('sales-map.data') }}?start_date=${startDate}&end_date=${endDate}`, {
                    headers: { 'Accept': 'application/json' }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    allSales = data.sales;
                    allCashiers = data.cashiers;
                    
                    statusEl.innerHTML = `<span class="text-emerald-600"><i class="ph-fill ph-check-circle mr-1"></i> ${allSales.length} Transaksi Ditemukan</span>`;
                    
                    renderCashierList();
                    renderMapPoints();
                } else {
                    Swal.fire('Error', 'Gagal memuat data laporan', 'error');
                }
            } catch (error) {
                console.error("Error fetching map data:", error);
                statusEl.innerHTML = '<span class="text-red-500"><i class="ph-fill ph-warning-circle mr-1"></i> Gagal Memuat</span>';
                Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
            }
        }

        // Render Cashiers
        function renderCashierList() {
            if (allCashiers.length === 0) {
                cashierListContainer.innerHTML = `
                    <div class="h-full flex flex-col items-center justify-center text-gray-500 p-6 text-center">
                        <i class="ph-light ph-users-slash text-4xl mb-2 text-gray-300"></i>
                        <p class="text-sm">Tidak ada transaksi tercatat dengan koordinat untuk periode tanggal ini.</p>
                    </div>
                `;
                return;
            }

            let html = '';
            
            allCashiers.forEach(cashier => {
                const isActive = activeCashierId == cashier.id ? 'active' : '';
                const baseColor = cashier.color || '#10b981';
                
                // Get initials
                let initials = cashier.name.substring(0, 2).toUpperCase();
                
                html += `
                    <div class="kasir-item p-3 mb-2 rounded-xl flex items-center gap-3 border border-transparent ${isActive}" data-id="${cashier.id}">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-sm" style="background-color: ${baseColor}">
                            ${initials}
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <h4 class="font-bold text-gray-900 text-sm truncate">${cashier.name}</h4>
                            <p class="text-xs text-gray-500">${cashier.total_sales} Transaksi Valid</p>
                        </div>
                        <div class="w-8 flex justify-end">
                            <i class="ph ph-caret-right text-gray-400"></i>
                        </div>
                    </div>
                `;
            });

            cashierListContainer.innerHTML = html;

            // Bind events
            document.querySelectorAll('.kasir-item').forEach(item => {
                item.addEventListener('click', function() {
                    document.querySelectorAll('.kasir-item').forEach(el => el.classList.remove('active'));
                    this.classList.add('active');
                    
                    const cId = this.getAttribute('data-id');
                    activeCashierId = cId;
                    renderMapPoints(cId);
                });
            });
        }

        // Render Map Points & Route
        async function renderMapPoints(filterCashierId = null) {
            // Clear existing
            markerCluster.clearLayers();
            if (routeLayer) {
                map.removeLayer(routeLayer);
                routeLayer = null;
            }

            let salesToRender = allSales;
            if (filterCashierId) {
                salesToRender = allSales.filter(s => s.cashier_id == filterCashierId);
            }

            if (salesToRender.length === 0) return;

            const markers = [];
            const coordinates = [];

            // Add markers
            salesToRender.forEach(sale => {
                // Ensure valid coords
                if (sale.latitude && sale.longitude && !isNaN(sale.latitude) && !isNaN(sale.longitude)) {
                    // Create pin HTML
                    const htmlPin = `
                        <div style="background-color: white; border: 3px solid #10b981; width: 24px; height: 24px; border-radius: 50%; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);"></div>
                    `;
                    
                    const customIcon = L.divIcon({
                        html: htmlPin,
                        className: '',
                        iconSize: [24, 24],
                        iconAnchor: [12, 12]
                    });

                    const popupContent = `
                        <div class="min-w-[200px] font-sans">
                            <div class="bg-emerald-50 -mx-3 -mt-3 p-3 border-b border-emerald-100 rounded-t-lg">
                                <h3 class="font-bold text-gray-900 m-0 leading-tight">Sale: ${sale.invoice_number}</h3>
                                <p class="text-xs text-gray-600 m-0 mt-1"><i class="ph ph-clock mr-1"></i> ${new Date(sale.created_at).toLocaleString('id-ID')}</p>
                            </div>
                            <div class="py-3 space-y-2">
                                <div class="flex justify-between items-center bg-gray-50 p-2 rounded">
                                    <span class="text-xs text-gray-500">Total:</span>
                                    <span class="text-sm font-black text-gray-900">${idrFormatter.format(sale.grand_total)}</span>
                                </div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-gray-500">Kasir:</span>
                                    <span class="font-medium text-gray-800">${sale.cashier_name}</span>
                                </div>
                            </div>
                        </div>
                    `;

                    const marker = L.marker([sale.latitude, sale.longitude], { icon: customIcon });
                    marker.bindPopup(popupContent, { className: 'custom-popup rounded-lg' });
                    markers.push(marker);
                    
                    coordinates.push([sale.longitude, sale.latitude]); // Notice OSRM uses Long, Lat order
                }
            });

            if (markers.length > 0) {
                markerCluster.addLayers(markers);
                
                // Fit bounds map to show all markers
                const group = new L.featureGroup(markers);
                map.fitBounds(group.getBounds(), { padding: [50, 50] });
            }

            // Draw Route if a specific cashier is selected and there's more than 1 point
            if (filterCashierId && coordinates.length > 1) {
                // Ensure array doesn't exceed OSRM limits safely (approx 100 points per request without getting blocked for public server, but let's compress it just in case)
                let routeCoords = coordinates;
                
                // If there are too many transactions in a day, take a sampling of points to build a representative route
                if (routeCoords.length > 50) {
                    const sampled = [];
                    const step = Math.ceil(routeCoords.length / 50);
                    for(let i = 0; i < routeCoords.length; i += step) {
                        sampled.push(routeCoords[i]);
                    }
                    if(sampled[sampled.length-1] !== routeCoords[routeCoords.length-1]) {
                        sampled.push(routeCoords[routeCoords.length-1]);
                    }
                    routeCoords = sampled;
                }
                
                const coordString = routeCoords.map(c => `${c[0]},${c[1]}`).join(';');
                
                try {
                    statusEl.innerHTML = '<i class="fas fa-spinner fa-spin text-emerald-500 mr-1"></i> Mendapatkan rute...';
                    
                    const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${coordString}?overview=full&geometries=geojson`;
                    const osrmRes = await fetch(osrmUrl);
                    if (osrmRes.ok) {
                        const routeData = await osrmRes.json();
                        
                        if (routeData.code === 'Ok' && routeData.routes.length > 0) {
                            const geojsonRoute = routeData.routes[0].geometry;
                            
                            // Draw dashed lines
                            routeLayer = L.geoJSON(geojsonRoute, {
                                style: {
                                    color: '#f59e0b', // Amber color for route
                                    weight: 4,
                                    opacity: 0.8,
                                    dashArray: '10, 10', // Dashed line
                                    lineJoin: 'round'
                                }
                            }).addTo(map);
                            
                            statusEl.innerHTML = `<span class="text-emerald-600"><i class="ph-fill ph-check-circle mr-1"></i> Rute Ditampilkan</span>`;
                        } else {
                            statusEl.innerHTML = `<span class="text-orange-500"><i class="ph-fill ph-warning mr-1"></i> Gagal membangun rute utuh</span>`;
                        }
                    }
                } catch(e) {
                    console.error("OSRM Route Error", e);
                    statusEl.innerHTML = `<span class="text-orange-500"><i class="ph-fill ph-warning mr-1"></i> Gagal memuat rute jalan</span>`;
                    
                    // Fallback to simple straight polylines
                    const latLngs = routeCoords.map(c => [c[1], c[0]]);
                    routeLayer = L.polyline(latLngs, {
                        color: '#9ca3af',
                        weight: 2,
                        dashArray: '5, 5'
                    }).addTo(map);
                }
            }
        }

        // Setup Events
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            activeCashierId = null;
            fetchMapData();
        });

        resetBtn.addEventListener('click', function() {
            document.querySelectorAll('.kasir-item').forEach(el => el.classList.remove('active'));
            activeCashierId = null;
            renderMapPoints();
        });

        // Bootstrap Map
        initMap();
        fetchMapData(); // Load default
    });
</script>

<style>
/* Adjust mapping popup overrides */
.custom-popup .leaflet-popup-content-wrapper {
    background: transparent;
    box-shadow: none;
    padding: 0;
}
.custom-popup .leaflet-popup-content {
    margin: 0;
    border-radius: 0.75rem;
    background: white;
    box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    overflow: hidden;
}
.custom-popup .leaflet-popup-tip {
    background: white;
}
</style>
@endpush

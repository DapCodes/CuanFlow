@extends('layouts.app')

@section('title', 'Peta Peluang Bisnis - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Peta Peluang Bisnis</span>
</li>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #opportunity-map { z-index: 1; }
    .leaflet-popup-content-wrapper {
        border-radius: 1rem !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1) !important;
        border: 1px solid #e5e7eb !important;
        padding: 0 !important;
    }
    .leaflet-popup-content {
        margin: 0 !important;
        font-family: 'Satoshi', sans-serif !important;
    }
    .leaflet-popup-tip {
        box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;
    }
    .leaflet-control-zoom a {
        border-radius: 0.75rem !important;
        border: 1px solid #e5e7eb !important;
        color: #374151 !important;
        font-weight: 700 !important;
    }
    .leaflet-control-zoom {
        border: none !important;
        box-shadow: 0 1px 5px rgba(0,0,0,0.1) !important;
        border-radius: 0.75rem !important;
        overflow: hidden;
    }

    /* Custom scrollbar for filter panel */
    .filter-scrollbar::-webkit-scrollbar { width: 4px; }
    .filter-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 999px; }

    /* Pulse animation for loading dots */
    @keyframes mapPulse {
        0%, 100% { opacity: 0.4; transform: scale(0.8); }
        50% { opacity: 1; transform: scale(1.2); }
    }
    .map-loading-dot {
        animation: mapPulse 1.4s ease-in-out infinite;
    }
    .map-loading-dot:nth-child(2) { animation-delay: 0.2s; }
    .map-loading-dot:nth-child(3) { animation-delay: 0.4s; }
</style>
@endpush

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Peta Peluang Bisnis
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Analisis lokasi berbasis AI, temukan area terbaik untuk memulai bisnis baru.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button onclick="refreshData()" id="btnRefresh"
                    class="inline-flex items-center gap-2 rounded-xl bg-white border border-gray-200 px-5 py-3 text-sm font-black text-gray-700 hover:bg-gray-50 transition-all shadow-sm active:scale-95">
                    <i class="fas fa-sync-alt text-xs"></i>
                    <span>Perbarui Data</span>
                </button>
                <button onclick="toggleStats()" id="btnStats"
                    class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-5 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                    <i class="fas fa-chart-bar text-xs"></i>
                    <span>Lihat Statistik</span>
                </button>
            </div>
        </section>

        {{-- STATS CARDS (initially hidden, toggled) --}}
        <section id="statsSection" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 transition-all duration-300">
            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Titik Bisnis</p>
                <p class="mt-2 text-2xl font-black text-gray-900" id="statTotalPoints">—</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Area Potensi Tinggi</p>
                <p class="mt-2 text-2xl font-black text-cuan-green" id="statHighPotential">—</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Area Menengah</p>
                <p class="mt-2 text-2xl font-black text-amber-500" id="statMedium">—</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Rata-rata Skor</p>
                <p class="mt-2 text-2xl font-black text-blue-600" id="statAvgScore">—</p>
            </div>
        </section>

        {{-- FILTER + MAP CONTAINER --}}
        <x-card-container>
            {{-- Filter Bar --}}
            <div class="px-6 py-5 border-b border-gray-100 bg-white space-y-4 md:space-y-0 md:flex md:items-center md:gap-4">
                {{-- Category --}}
                <div class="flex-1 relative">
                    <select id="filterLabel"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all bg-white font-bold text-gray-700">
                        <option value="">Semua Klasifikasi</option>
                        <option value="High Potential">🟢 Potensi Tinggi</option>
                        <option value="Medium">🟡 Menengah</option>
                        <option value="Low">🔴 Rendah</option>
                    </select>
                </div>

                {{-- Min Score --}}
                <div class="flex items-center gap-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 whitespace-nowrap">Skor Min</label>
                    <input type="range" id="filterMinScore" min="0" max="100" value="0" step="5"
                        class="w-32 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-cuan-green">
                    <span id="minScoreValue" class="text-sm font-black text-gray-700 w-8 text-center">0</span>
                </div>

                {{-- View Toggle --}}
                <div class="flex rounded-xl overflow-hidden border border-gray-200">
                    <button id="viewCircles" onclick="setViewMode('circles')"
                        class="px-4 py-2.5 text-[10px] font-black uppercase tracking-widest bg-cuan-green text-white transition-all">
                        Lingkaran
                    </button>
                    <button id="viewHeat" onclick="setViewMode('heat')"
                        class="px-4 py-2.5 text-[10px] font-black uppercase tracking-widest bg-white text-gray-400 hover:bg-gray-50 transition-all">
                        Heatmap
                    </button>
                </div>
            </div>

            {{-- Map --}}
            <div class="relative">
                {{-- Loading Overlay --}}
                <div id="mapLoading" class="absolute inset-0 z-10 bg-white/80 backdrop-blur-sm flex flex-col items-center justify-center">
                    <div class="flex gap-2 mb-3">
                        <div class="w-3 h-3 bg-cuan-green rounded-full map-loading-dot"></div>
                        <div class="w-3 h-3 bg-cuan-green rounded-full map-loading-dot"></div>
                        <div class="w-3 h-3 bg-cuan-green rounded-full map-loading-dot"></div>
                    </div>
                    <p class="text-sm font-bold text-gray-500">Memuat data peta...</p>
                </div>

                {{-- Empty State Overlay --}}
                <div id="mapEmpty" class="hidden absolute inset-0 z-10 bg-white/80 backdrop-blur-sm flex flex-col items-center justify-center">
                    <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                        <i class="fas fa-map-marked-alt text-3xl text-gray-300"></i>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 mb-1">Data belum tersedia</h3>
                    <p class="text-sm text-gray-500 mb-4 max-w-sm text-center">
                        Silakan jalankan perintah pengambilan data untuk wilayah ini di konsol sistem.
                    </p>
                </div>

                <div id="opportunity-map" class="w-full h-[550px]"></div>
            </div>
        </x-card-container>

        {{-- LEGEND --}}
        <div class="fixed bottom-6 left-6 z-50">
            <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-4 space-y-3 min-w-[180px]">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Tingkat Peluang</p>
                <div class="space-y-2.5">
                    <div class="flex items-center gap-3">
                        <span class="w-4 h-4 rounded-full bg-emerald-400 border-2 border-emerald-200 shadow-sm shadow-emerald-100"></span>
                        <span class="text-xs font-bold text-gray-700">Potensi Tinggi</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-4 h-4 rounded-full bg-amber-400 border-2 border-amber-200 shadow-sm shadow-amber-100"></span>
                        <span class="text-xs font-bold text-gray-700">Menengah</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-4 h-4 rounded-full bg-red-400 border-2 border-red-200 shadow-sm shadow-red-100"></span>
                        <span class="text-xs font-bold text-gray-700">Rendah</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-4 h-4 rounded-full bg-gray-200 border-2 border-gray-100"></span>
                        <span class="text-xs font-bold text-gray-400">Tanpa Data</span>
                    </div>
                </div>
                <div class="pt-2 border-t border-gray-100">
                    <p class="text-[10px] text-gray-400" id="legendPointCount">0 titik dimuat</p>
                </div>
            </div>
        </div>

    </div>
</main>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ─── State ───
    let map, circleLayer, heatLayer;
    let currentData = [];
    let viewMode = 'circles'; // 'circles' | 'heat'

    // Outlet coordinates from backend (fallback to Jakarta)
    const outletLat = {{ auth()->user()->outlet->latitude ?? '-6.2088' }};
    const outletLng = {{ auth()->user()->outlet->longitude ?? '106.8456' }};
    const initialZoom = {{ auth()->user()->outlet->latitude ? 12 : 11 }};
    const radiusKm = 15;

    // ─── Init Map ───
    function initMap() {
        map = L.map('opportunity-map', {
            zoomControl: true,
            scrollWheelZoom: true,
            maxBounds: [[-11.0, 94.0], [6.0, 141.0]], // approximate Indonesian bounds
            maxBoundsViscosity: 1.0
        }).setView([outletLat, outletLng], initialZoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19,
        }).addTo(map);

        // Draw 15km radius boundary
        L.circle([outletLat, outletLng], {
            radius: radiusKm * 1000,
            color: '#3b82f6',
            weight: 2,
            dashArray: '5, 8',
            fillOpacity: 0.03,
            interactive: false
        }).addTo(map);

        // Outlet Marker
        L.circleMarker([outletLat, outletLng], {
            radius: 6,
            color: '#ffffff',
            fillColor: '#3b82f6',
            fillOpacity: 1,
            weight: 2
        }).bindPopup('<div class="font-bold text-center">Outlet Anda</div>').addTo(map);

        // Create layer groups
        circleLayer = L.layerGroup().addTo(map);

        // Load data
        loadData();
    }

    // ─── Load Data ───
    async function loadData() {
        showLoading(true);
        hideEmpty();

        const label = document.getElementById('filterLabel').value;
        const minScore = document.getElementById('filterMinScore').value;

        // Note: Always send min_score even if it's 0 to avoid cached empty results for the "no-min-score" key
        let url = `/api/v1/heatmap?limit=5000&lat=${outletLat}&lng=${outletLng}&radius=${radiusKm}&min_score=${minScore}&t=${Date.now()}`;
        if (label) url += `&label=${encodeURIComponent(label)}`;

        try {
            const response = await fetch(url);
            const json = await response.json();

            if (!json.success || !json.data || json.data.length === 0) {
                currentData = [];
                clearLayers(); // Add this line to remove ghost markers
                showEmpty();
                showLoading(false);
                return;
            }

            currentData = json.data;
            renderMap(currentData);
            showLoading(false);

            // Update legend count
            document.getElementById('legendPointCount').textContent = `${currentData.length.toLocaleString()} titik dimuat`;

        } catch (err) {
            console.error('Fetch error:', err);
            showLoading(false);
            showEmpty();
        }
    }

    // ─── Render Map ───
    function renderMap(data) {
        clearLayers();

        if (viewMode === 'circles') {
            renderCircles(data);
        } else {
            renderHeat(data);
        }

        // Auto-fit bounds if we have data (constrained to the 15km area)
        if (data.length > 0) {
            // We use the 15km bounding box (approx 0.135 deg) to keep the map centered on the search area
            const rOffset = 0.135; 
            map.fitBounds([
                [outletLat - rOffset, outletLng - rOffset], 
                [outletLat + rOffset, outletLng + rOffset]
            ], { padding: [20, 20], maxZoom: initialZoom });
        }
    }

    // ─── Render Circles ───
    function renderCircles(data) {
        data.forEach(point => {
            const { color, borderColor, glowColor } = getPointColors(point.score, point.label);
            const opacity = 0.35 + (point.score / 200);

            const circle = L.circle([point.lat, point.lng], {
                radius: 300,
                color: borderColor,
                fillColor: color,
                fillOpacity: Math.min(opacity, 0.7),
                weight: 1.5,
                opacity: 0.6,
            });

            circle.bindPopup(createPopupContent(point), {
                className: 'custom-popup',
                maxWidth: 280,
            });

            circleLayer.addLayer(circle);
        });
    }

    // ─── Render Heat ───
    function renderHeat(data) {
        if (heatLayer) {
            map.removeLayer(heatLayer);
        }

        const heatPoints = data.map(p => [p.lat, p.lng, p.score / 100]);

        heatLayer = L.heatLayer(heatPoints, {
            radius: 25,
            blur: 18,
            maxZoom: 17,
            max: 1.0,
            gradient: {
                0.0: '#3b82f6',
                0.3: '#ef4444',
                0.5: '#eab308',
                0.7: '#22c55e',
                1.0: '#16a34a',
            }
        }).addTo(map);
    }

    // ─── Popup Content ───
    function createPopupContent(point) {
        const { color, badgeClass } = getPointColors(point.score, point.label);
        const scoreBar = Math.min(point.score, 100);
        
        // Translate label for display
        let displayLabel = point.label;
        if (point.label === 'High Potential') displayLabel = 'Potensi Tinggi';
        else if (point.label === 'Medium') displayLabel = 'Menengah';
        else if (point.label === 'Low') displayLabel = 'Rendah';

        return `
            <div style="padding: 16px; min-width: 220px; font-family: 'Satoshi', sans-serif;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                    <span style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; color:#9ca3af;">
                        Analisis Area
                    </span>
                    <span style="display:inline-flex; align-items:center; padding:3px 10px; border-radius:999px; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; ${badgeClass}">
                        ${displayLabel}
                    </span>
                </div>

                <div style="margin-bottom:12px;">
                    <div style="display:flex; align-items:baseline; gap:4px;">
                        <span style="font-size:28px; font-weight:900; color:#111827;">${point.score.toFixed(1)}</span>
                        <span style="font-size:12px; font-weight:700; color:#9ca3af;">/100</span>
                    </div>
                    <div style="margin-top:6px; height:6px; background:#f3f4f6; border-radius:9999px; overflow:hidden;">
                        <div style="height:100%; width:${scoreBar}%; background:${color}; border-radius:9999px; transition:width 0.3s ease;"></div>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; padding-top:10px; border-top:1px solid #f3f4f6;">
                    <div>
                        <p style="font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; color:#9ca3af; margin:0;">Total Bisnis</p>
                        <p style="font-size:16px; font-weight:900; color:#111827; margin:2px 0 0;">${point.total_businesses}</p>
                    </div>
                    <div>
                        <p style="font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; color:#9ca3af; margin:0;">Kategori</p>
                        <p style="font-size:16px; font-weight:900; color:#111827; margin:2px 0 0;">${point.category_diversity}</p>
                    </div>
                    <div>
                        <p style="font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; color:#9ca3af; margin:0;">Permintaan</p>
                        <p style="font-size:16px; font-weight:900; color:#658C58; margin:2px 0 0;">${point.demand_score}</p>
                    </div>
                    <div>
                        <p style="font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; color:#9ca3af; margin:0;">Kompetisi</p>
                        <p style="font-size:16px; font-weight:900; color:#ef4444; margin:2px 0 0;">${point.competition_score}</p>
                    </div>
                </div>

                ${point.analysis ? `
                <div style="margin-top:10px; padding-top:10px; border-top:1px solid #f3f4f6;">
                    <p style="font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; color:#9ca3af; margin:0 0 4px;">Analisis AI</p>
                    <p style="font-size:12px; color:#6b7280; line-height:1.5; margin:0;">${point.analysis}</p>
                </div>
                ` : ''}
            </div>
        `;
    }

    // ─── Color Helpers ───
    function getPointColors(score, label) {
        if (score >= 60 || label === 'High Potential') {
            return {
                color: '#22c55e',
                borderColor: '#16a34a',
                glowColor: 'rgba(34,197,94,0.3)',
                badgeClass: 'background:#dcfce7; color:#16a34a; border:1px solid #bbf7d0;',
            };
        }
        if (score >= 30 || label === 'Medium') {
            return {
                color: '#eab308',
                borderColor: '#ca8a04',
                glowColor: 'rgba(234,179,8,0.3)',
                badgeClass: 'background:#fef9c3; color:#ca8a04; border:1px solid #fde68a;',
            };
        }
        return {
            color: '#ef4444',
            borderColor: '#dc2626',
            glowColor: 'rgba(239,68,68,0.3)',
            badgeClass: 'background:#fef2f2; color:#dc2626; border:1px solid #fecaca;',
        };
    }

    // ─── Utilities ───
    function clearLayers() {
        circleLayer.clearLayers();
        if (heatLayer) {
            map.removeLayer(heatLayer);
            heatLayer = null;
        }
    }

    function showLoading(show) {
        document.getElementById('mapLoading').classList.toggle('hidden', !show);
    }

    function showEmpty() {
        document.getElementById('mapEmpty').classList.remove('hidden');
    }

    function hideEmpty() {
        document.getElementById('mapEmpty').classList.add('hidden');
    }

    // ─── Exposed Functions ───
    window.refreshData = function() {
        const btn = document.getElementById('btnRefresh');
        btn.querySelector('i').classList.add('fa-spin');
        loadData().finally(() => {
            setTimeout(() => btn.querySelector('i').classList.remove('fa-spin'), 500);
        });
    };

    window.toggleStats = function() {
        const section = document.getElementById('statsSection');
        const isHidden = section.classList.contains('hidden');

        if (isHidden) {
            section.classList.remove('hidden');
            section.classList.add('grid');
            loadStats();
        } else {
            section.classList.add('hidden');
            section.classList.remove('grid');
        }
    };

    window.setViewMode = function(mode) {
        viewMode = mode;

        // Update button styles
        const btnCircles = document.getElementById('viewCircles');
        const btnHeat = document.getElementById('viewHeat');

        if (mode === 'circles') {
            btnCircles.className = 'px-4 py-2.5 text-[10px] font-black uppercase tracking-widest bg-cuan-green text-white transition-all';
            btnHeat.className = 'px-4 py-2.5 text-[10px] font-black uppercase tracking-widest bg-white text-gray-400 hover:bg-gray-50 transition-all';
        } else {
            btnHeat.className = 'px-4 py-2.5 text-[10px] font-black uppercase tracking-widest bg-cuan-green text-white transition-all';
            btnCircles.className = 'px-4 py-2.5 text-[10px] font-black uppercase tracking-widest bg-white text-gray-400 hover:bg-gray-50 transition-all';
        }

        if (currentData.length > 0) {
            renderMap(currentData);
        }
    };

    // ─── Stats Loader ───
    async function loadStats() {
        try {
            const response = await fetch(`/api/v1/heatmap/stats?lat=${outletLat}&lng=${outletLng}&radius=${radiusKm}&t=${Date.now()}`);
            const json = await response.json();

            if (json.success && json.data) {
                const d = json.data;
                document.getElementById('statTotalPoints').textContent = (d.total_business_points || 0).toLocaleString();
                document.getElementById('statHighPotential').textContent = (d.classifications?.high_potential || 0).toLocaleString();
                document.getElementById('statMedium').textContent = (d.classifications?.medium || 0).toLocaleString();
                document.getElementById('statAvgScore').textContent = (d.score_range?.avg || 0).toFixed(1);
            }
        } catch (err) {
            console.error('Stats error:', err);
        }
    }

    // ─── Filter Listeners ───
    document.getElementById('filterLabel').addEventListener('change', () => loadData());

    const minScoreSlider = document.getElementById('filterMinScore');
    const minScoreValue = document.getElementById('minScoreValue');
    let filterTimeout = null;

    minScoreSlider.addEventListener('input', function() {
        minScoreValue.textContent = this.value;
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(() => loadData(), 300);
    });

    // ─── Init ───
    initMap();

    // Fix partial tile loading / gray map bug
    const fixMapLayout = () => { 
        if (map) {
            map.invalidateSize();
            // Center map on outlet
            map.setView([outletLat, outletLng], initialZoom);
        }
    };
    
    // Multiple attempts to ensure layout is ready
    window.addEventListener('load', fixMapLayout);
    [100, 500, 1000, 2000].forEach(delay => setTimeout(fixMapLayout, delay));
});
</script>
@endpush

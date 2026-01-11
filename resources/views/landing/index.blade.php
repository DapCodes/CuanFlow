@extends('layouts.app')

@section('title', 'Kelola Landing Page - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Landing Page</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Alert / Notifikasi --}}
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-check-circle mt-0.5 text-green-500"></i>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-exclamation-circle mt-0.5 text-red-500"></i>
                <p class="text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        {{-- HEADER HALAMAN (Match Discount Page) --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-500 border border-blue-100">
                        <i class="fas fa-globe text-sm"></i>
                    </span>
                    <span>Landing Page Store</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola halaman profil website outlet Anda untuk menjangkau lebih banyak pelanggan secara online.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                @if($outlet && $outlet->landingPage)
                    <a href="{{ route('landing-pages.show', [$outlet->id, Str::slug($outlet->name)]) }}" target="_blank"
                       class="inline-flex items-center gap-2 rounded-lg bg-white border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                        <i class="fas fa-external-link-alt text-sm"></i>
                        <span>Lihat Live</span>
                    </a>
                    
                    @can('edit landing page')
                    <a href="{{ route('landing-pages.edit', $outlet->id) }}"
                       class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1">
                        <i class="fas fa-pen-paintbrush text-sm"></i>
                        <span>Edit Tampilan</span>
                    </a>
                    @endcan
                @endif
            </div>
        </section>

        {{-- ANALYTICS CHART SECTION --}}
        @if($outlet && $outlet->landingPage)
        @can('lihat analitik landing page')
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Traffic Pengunjung</h2>
                    <p class="text-sm text-gray-500">Statistik kunjungan halaman landing page Anda.</p>
                </div>
                
                <div class="flex items-center bg-gray-100 rounded-lg p-1">
                    <a href="{{ route('landing-pages.index') }}?period=7d" class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all {{ $period === '7d' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">7 Hari</a>
                    <a href="{{ route('landing-pages.index') }}?period=1m" class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all {{ $period === '1m' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">1 Bulan</a>
                    <a href="{{ route('landing-pages.index') }}?period=6m" class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all {{ $period === '6m' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">6 Bulan</a>
                    <a href="{{ route('landing-pages.index') }}?period=1y" class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all {{ $period === '1y' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">1 Tahun</a>
                </div>
            </div>

            <div class="relative h-72 w-full">
                <canvas id="trafficChart"></canvas>
            </div>
            
            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-xs text-blue-600 font-semibold uppercase tracking-wide">Total Kunjungan</p>
                    <h3 class="text-2xl font-bold text-blue-900 mt-1">{{ $totalVisits }}</h3>
                </div>
                <!-- Additional stats can be added here -->
            </div>
        </section>
        @endcan
        @endif

        {{-- KONTEN UTAMA --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            @if($outlet)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Outlet Info</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">URL Halaman</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Terakhir Update</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status Publik</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($outlet->logo)
                                            <img src="{{ Storage::url($outlet->logo) }}" class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                                                <i class="fas fa-store"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $outlet->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $outlet->address }}</div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <input type="text" readonly value="{{ route('landing-pages.show', [$outlet->id, Str::slug($outlet->name)]) }}" 
                                               class="text-xs text-gray-500 bg-gray-50 border border-gray-200 rounded px-2 py-1 w-48 truncate">
                                        <button onclick="navigator.clipboard.writeText('{{ route('landing-pages.show', [$outlet->id, Str::slug($outlet->name)]) }}')" 
                                                class="text-blue-600 hover:text-blue-800" title="Copy URL">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    {{ $outlet->landingPage->updated_at->diffForHumans() }}
                                </td>

                                <td class="px-6 py-4">
                                    @if($outlet->landingPage->is_active)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1.5"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @can('aktifkan nonaktifkan landing page')
                                    <form action="{{ route('landing-pages.toggle-status', $outlet->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium rounded-md border transition-colors
                                                {{ $outlet->landingPage->is_active 
                                                    ? 'border-red-200 text-red-600 hover:bg-red-50' 
                                                    : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50' }}">
                                            <i class="fas fa-power-off"></i>
                                            {{ $outlet->landingPage->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Preview Section (Mini) --}}
                <div class="p-6 bg-gray-50 border-t border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Preview Konten Saat Ini</h3>
                    <div class="aspect-w-16 aspect-h-9 w-full max-w-2xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden relative group">
                        <!-- Hero Preview -->
                        <div class="h-48 bg-cover bg-center flex items-center justify-center text-center px-4 relative"
                             style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('{{ $outlet->landingPage->hero_image ? Storage::url($outlet->landingPage->hero_image) : "https://images.unsplash.com/photo-1556740738-b6a63e27c4df?ixlib=rb-1.2.1" }}')">
                            <div class="text-white z-10">
                                <h2 class="text-2xl font-bold mb-1">{{ $outlet->landingPage->hero_title ?? 'Judul Hero' }}</h2>
                                <p class="text-sm opacity-90">{{ $outlet->landingPage->hero_subtitle ?? 'Sub Judul' }}</p>
                            </div>
                        </div>
                        
                        <!-- Overlay Button -->
                        <div class="absolute inset-0 bg-black/5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            @can('edit landing page')
                            <a href="{{ route('landing-pages.edit', $outlet->id) }}" class="bg-white text-gray-900 px-4 py-2 rounded-full font-bold shadow-lg transform scale-95 group-hover:scale-100 transition-transform">
                                <i class="fas fa-edit mr-2"></i> Edit Penuh
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>

            @else
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-store-slash text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Outlet Tidak Ditemukan</h3>
                    <p class="text-gray-500 mt-1">Anda belum terhubung dengan outlet manapun.</p>
                </div>
            @endif
        </section>
    </div>
</main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('trafficChart').getContext('2d');
        
        // Data injected from Controller
        const labels = @json($chartLabels);
        const data = @json($chartData);

        // Gradient Fill
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.2)');
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Kunjungan',
                    data: data,
                    borderColor: '#4F46E5',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#1F2937',
                        bodyColor: '#4B5563',
                        borderColor: '#E5E7EB',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        grid: { color: '#F3F4F6' },
                        ticks: { 
                            stepSize: 1,
                            precision: 0
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    });
</script>
@endpush

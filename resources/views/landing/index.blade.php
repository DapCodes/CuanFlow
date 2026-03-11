@extends('layouts.app')

@section('title', 'Landing Page - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Landing Page</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Landing Page Store
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola profil website outlet Anda untuk menjangkau lebih banyak pelanggan secara online.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @if($outlet && $outlet->landingPage)
                    <a href="{{ route('landing-pages.show', [$outlet->id, Str::slug($outlet->name)]) }}" target="_blank"
                       class="inline-flex items-center gap-2 rounded-xl bg-white border border-gray-200 px-5 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all active:scale-95 shadow-sm">
                        <span>Lihat Live</span>
                    </a>
                    
                    @can('edit landing page')
                    <a href="{{ route('landing-pages.edit', $outlet->id) }}" target="_blank"
                       class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-5 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                        <span>Edit Tampilan</span>
                    </a>
                    @endcan
                @endif
            </div>
        </section>

        {{-- ANALYTICS CHART SECTION --}}
        @if($outlet && $outlet->landingPage)
        @can('lihat analitik landing page')
        <x-card-container>
            <div class="px-6 py-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Traffic Pengunjung</h2>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Statistik kunjungan landing page</p>
                </div>
                
                <div class="flex items-center bg-gray-100/50 rounded-xl p-1 border border-gray-100">
                    <a href="{{ route('landing-pages.index') }}?period=7d" class="px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all {{ $period === '7d' ? 'bg-white shadow-sm text-cuan-green' : 'text-gray-400 hover:text-gray-600' }}">7 Hari</a>
                    <a href="{{ route('landing-pages.index') }}?period=1m" class="px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all {{ $period === '1m' ? 'bg-white shadow-sm text-cuan-green' : 'text-gray-400 hover:text-gray-600' }}">1 Bulan</a>
                    <a href="{{ route('landing-pages.index') }}?period=6m" class="px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all {{ $period === '6m' ? 'bg-white shadow-sm text-cuan-green' : 'text-gray-400 hover:text-gray-600' }}">6 Bulan</a>
                    <a href="{{ route('landing-pages.index') }}?period=1y" class="px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all {{ $period === '1y' ? 'bg-white shadow-sm text-cuan-green' : 'text-gray-400 hover:text-gray-600' }}">1 Tahun</a>
                </div>
            </div>

            <div class="p-6">
                <div class="relative h-72 w-full">
                    <canvas id="trafficChart"></canvas>
                </div>
                
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-gray-50/50 border border-gray-100 rounded-2xl p-5 transition-all hover:border-cuan-green/20">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Total Kunjungan</p>
                        <h3 class="text-2xl font-black text-gray-900 mt-2">{{ number_format($totalVisits, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </x-card-container>
        @endcan
        @endif

        {{-- KONTEN UTAMA --}}
        <x-card-container>
            @if($outlet)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left">Outlet Info</th>
                                <th class="px-6 py-4 text-left">URL Halaman</th>
                                <th class="px-6 py-4 text-left">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        @if($outlet->logo)
                                            <img src="{{ Storage::url($outlet->logo) }}" class="w-12 h-12 rounded-xl object-cover border-2 border-white shadow-sm">
                                        @else
                                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-cuan-green to-cuan-dark flex items-center justify-center border-2 border-white shadow-sm">
                                                <span class="text-white font-black text-xs">{{ strtoupper(substr($outlet->name, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-bold text-gray-900 leading-tight">{{ $outlet->name }}</div>
                                            <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">
                                                {{ $outlet->landingPage->updated_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-2 max-w-xs">
                                        <input type="text" readonly value="{{ route('landing-pages.show', [$outlet->id, Str::slug($outlet->name)]) }}" 
                                               class="text-[10px] font-bold text-gray-500 bg-gray-50 border border-gray-100 rounded-lg px-3 py-2 w-full truncate focus:ring-0 cursor-default">
                                        <button onclick="navigator.clipboard.writeText('{{ route('landing-pages.show', [$outlet->id, Str::slug($outlet->name)]) }}'); Swal.fire({title: 'Copied!', icon: 'success', toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, iconColor: '#658C58'})" 
                                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-400 hover:text-cuan-green hover:bg-cuan-green/10 transition-all active:scale-95" title="Copy URL">
                                            <i class="fas fa-copy text-xs"></i>
                                        </button>
                                    </div>
                                </td>

                                <td class="px-6 py-5 whitespace-nowrap">
                                    @if($outlet->landingPage->is_active)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-cuan-green/10 text-cuan-green border border-cuan-green/10">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-50 text-gray-400 border border-gray-200">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-5 whitespace-nowrap text-center text-sm font-medium">
                                    @can('aktifkan nonaktifkan landing page')
                                    <form action="{{ route('landing-pages.toggle-status', $outlet->id) }}" method="POST" class="inline confirm-toggle" 
                                          data-name="{{ $outlet->name }}" data-status="{{ $outlet->landingPage->is_active ? 'nonaktifkan' : 'aktifkan' }}">
                                        @csrf
                                        <button type="submit" 
                                                class="w-10 h-10 flex items-center justify-center rounded-xl transition-all active:scale-95
                                                {{ $outlet->landingPage->is_active ? 'bg-red-50 text-red-500 hover:bg-red-500 hover:text-white' : 'bg-cuan-green/10 text-cuan-green hover:bg-cuan-green hover:text-white' }}"
                                                title="{{ $outlet->landingPage->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="fas fa-power-off text-xs"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Preview Section --}}
                <div class="p-8 bg-gray-50/30 border-t border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Layout Preview</h3>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Template yang sedang digunakan</p>
                        </div>
                        <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-cuan-green/10 text-cuan-green border border-cuan-green/10">
                            Template {{ $outlet->landingPage->template_id ?? 1 }}
                        </span>
                    </div>

                    <div class="aspect-video w-full max-w-3xl mx-auto bg-white rounded-[2rem] shadow-2xl shadow-gray-200/50 border border-gray-100 overflow-hidden relative group transition-all hover:scale-[1.01]">
                        @php
                            $lp = $outlet->landingPage;
                            $heroImg = $lp->hero_image ? Storage::url($lp->hero_image) : "https://images.unsplash.com/photo-1556740738-b6a63e27c4df?ixlib=rb-1.2.1";
                            $templateId = $lp->template_id ?? 1;
                        @endphp

                        <div class="h-full relative overflow-hidden bg-gray-50">
                            @switch($templateId)
                                @case(1)
                                    <div class="absolute inset-0 bg-cover bg-center" style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.4)), url('{{ $heroImg }}')"></div>
                                    <div class="relative h-full flex flex-col items-center justify-center text-center px-10">
                                        @if($lp->tagline_text)
                                            <span class="text-[9px] font-black text-white/80 uppercase tracking-[0.3em] mb-3">{{ $lp->tagline_text }}</span>
                                        @endif
                                        <h2 class="text-2xl font-black text-white leading-tight mb-4">{{ $lp->hero_title ?? 'Your Title' }}</h2>
                                        <p class="text-xs text-white/90 max-w-md mb-6 line-clamp-2 leading-relaxed">{{ $lp->hero_subtitle ?? 'Your Subtitle' }}</p>
                                        <div class="px-8 py-3 bg-white text-gray-900 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-xl">{{ $lp->cta_button_text ?? 'Shop Now' }}</div>
                                    </div>
                                    @break

                                @case(2)
                                    <div class="flex h-full">
                                        <div class="w-1/2 bg-white flex flex-col justify-center p-10 space-y-4">
                                            @if($lp->tagline_text)
                                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ $lp->tagline_text }}</span>
                                            @endif
                                            <h2 class="text-2xl font-black text-gray-900 tracking-tighter leading-tight">{{ $lp->hero_title ?? 'Your Title' }}</h2>
                                            <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">{{ $lp->hero_subtitle ?? 'Your Subtitle' }}</p>
                                            <div class="pt-4">
                                                <div class="inline-block px-8 py-3 bg-gray-900 text-white text-[9px] font-black uppercase tracking-widest rounded-xl shadow-lg">{{ $lp->cta_button_text ?? 'Shop Now' }}</div>
                                            </div>
                                        </div>
                                        <div class="w-1/2 bg-cover bg-center" style="background-image: url('{{ $heroImg }}')"></div>
                                    </div>
                                    @break

                                @case(3)
                                    <div class="absolute inset-0 bg-gray-900">
                                        <div class="absolute inset-0 opacity-40 bg-cover bg-center" style="background-image: url('{{ $heroImg }}')"></div>
                                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/40 to-transparent"></div>
                                    </div>
                                    <div class="relative h-full flex items-center p-12">
                                        <div class="max-w-[70%] border-l-4 border-cuan-green pl-6">
                                            @if($lp->tagline_text)
                                                <span class="inline-block px-3 py-1 border border-cuan-green/50 text-cuan-green text-[8px] font-black uppercase mb-4 rounded-lg bg-cuan-green/5">{{ $lp->tagline_text }}</span>
                                            @endif
                                            <h2 class="text-3xl font-black text-white leading-none tracking-tighter mb-4">{{ $lp->hero_title ?? 'Your Title' }}</h2>
                                            <p class="text-xs text-gray-400 mb-6 line-clamp-2 italic leading-relaxed">{{ $lp->hero_subtitle ?? 'Your Subtitle' }}</p>
                                            <div class="inline-block px-8 py-3 bg-cuan-green text-white text-[9px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-cuan-green/20">{{ $lp->cta_button_text ?? 'Explore' }}</div>
                                        </div>
                                    </div>
                                    @break

                                @default
                                    <div class="flex items-center justify-center h-full bg-white">
                                        <p class="text-sm font-bold text-gray-400 tracking-widest uppercase">Preview Template {{ $templateId }}</p>
                                    </div>
                            @endswitch

                            <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-50 backdrop-blur-[2px]">
                                @can('edit landing page')
                                <a href="{{ route('landing-pages.edit', $outlet->id) }}" target="_blank" class="bg-white text-gray-900 px-8 py-4 rounded-2xl text-xs font-black uppercase tracking-[0.2em] shadow-2xl transform scale-90 group-hover:scale-100 transition-all hover:bg-cuan-green hover:text-white">
                                    Edit Tampilan
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>

            @else
                <div class="p-20 text-center">
                    <div class="w-20 h-20 bg-gray-50 border border-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-store-slash text-gray-200 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-black text-gray-900 uppercase tracking-widest">Outlet Tidak Ditemukan</h3>
                    <p class="text-sm text-gray-500 mt-2 max-w-sm mx-auto">Anda belum terhubung dengan outlet manapun untuk mengelola landing page.</p>
                </div>
            @endif
        </x-card-container>
    </div>
</main>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // CHART LOGIC
        const trafficCanvas = document.getElementById('trafficChart');
        if (trafficCanvas) {
            const ctx = trafficCanvas.getContext('2d');
            const labels = @json($chartLabels);
            const data = @json($chartData);

            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(101, 140, 88, 0.2)');
            gradient.addColorStop(1, 'rgba(101, 140, 88, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Kunjungan',
                        data: data,
                        borderColor: '#658C58',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#658C58',
                        pointBorderWidth: 2,
                        pointHoverRadius: 7,
                        pointHoverBackgroundColor: '#658C58',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 2
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
                            backgroundColor: 'white',
                            titleColor: '#111827',
                            titleFont: { weight: 'bold', family: 'Satoshi' },
                            bodyColor: '#374151',
                            bodyFont: { family: 'Satoshi' },
                            borderColor: '#E5E7EB',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: false,
                            cornerRadius: 12
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { 
                                font: { size: 10, weight: 'bold', family: 'Satoshi' },
                                color: '#9CA3AF'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            border: { display: false },
                            grid: { color: '#F3F4F6' },
                            ticks: { 
                                font: { size: 10, weight: 'bold', family: 'Satoshi' },
                                color: '#9CA3AF',
                                stepSize: 1,
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        }
    });
</script>
@endpush

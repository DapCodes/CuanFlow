@extends('admin.layouts.app')

@section('title', 'CPU Monitoring')

@section('breadcrumb')
<i class="fas fa-chevron-right text-gray-300 mx-2 text-xs"></i>
<span class="text-emerald-600 font-medium text-sm">CPU Monitoring</span>
@endsection

@section('content')
<div class="space-y-6" x-data="cpuMonitor()">
    <!-- Header with Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- CPU Card -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                    <i class="fas fa-microchip"></i>
                </div>
                <div class="w-2 h-2 rounded-full animate-pulse" :class="currentCpu < 50 ? 'bg-emerald-500' : (currentCpu < 80 ? 'bg-blue-500' : 'bg-red-500')"></div>
            </div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">CPU Usage</p>
            <div class="flex items-end gap-2 mt-1">
                <p class="text-2xl font-black text-gray-900"><span x-text="currentCpu">0</span>%</p>
                <span class="text-[10px] font-bold pb-1 lowercase" :class="currentCpu < 50 ? 'text-emerald-600' : (currentCpu < 80 ? 'text-blue-600' : 'text-red-600')" x-text="getStatusLabel(currentCpu)"></span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5 mt-3 overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500" :class="currentCpu < 50 ? 'bg-emerald-500' : (currentCpu < 80 ? 'bg-blue-500' : 'bg-red-500')" :style="'width: ' + currentCpu + '%'"></div>
            </div>
        </div>

        <!-- RAM Card -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
                    <i class="fas fa-memory"></i>
                </div>
            </div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">RAM Usage</p>
            <div class="flex items-end gap-2 mt-1">
                <p class="text-2xl font-black text-gray-900"><span x-text="ramUsage">0</span>%</p>
                <p class="text-[10px] text-gray-500 font-medium pb-1" x-text="ramDetails"></p>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5 mt-3 overflow-hidden">
                <div class="bg-purple-500 h-full rounded-full transition-all duration-500" :style="'width: ' + ramUsage + '%'"></div>
            </div>
        </div>

        <!-- Storage Card -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600">
                    <i class="fas fa-hdd"></i>
                </div>
            </div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Storage (Root)</p>
            <div class="flex items-end gap-2 mt-1">
                <p class="text-2xl font-black text-gray-900"><span x-text="diskUsage">0</span>%</p>
                <p class="text-[10px] text-gray-500 font-medium pb-1" x-text="diskDetails"></p>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5 mt-3 overflow-hidden">
                <div class="bg-orange-500 h-full rounded-full transition-all duration-500" :style="'width: ' + diskUsage + '%'"></div>
            </div>
        </div>

        <!-- Uptime Card -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center text-teal-600">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">System Uptime</p>
            <div class="mt-1">
                <p class="text-sm font-bold text-gray-900 truncate" x-text="uptime">N/A</p>
                <div class="flex items-center gap-1.5 mt-3">
                    <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-ping"></div>
                    <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">Server Live</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Section: Chart + Details -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- CPU Area Chart -->
        <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-emerald-600 text-sm"></i>
                    </div>
                    <h3 class="font-bold text-gray-900">CPU Load Trend</h3>
                </div>
                <div class="text-[10px] text-gray-400 font-bold bg-white px-3 py-1 rounded-full border border-gray-100 shadow-sm">
                    Live: <span x-text="lastUpdated">0</span>s ago
                </div>
            </div>
            <div class="p-6">
                <div id="cpuChart" class="w-full h-[350px]"></div>
            </div>
        </div>

        <!-- Server Environment Details -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden h-fit">
            <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/30">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-server text-blue-600 text-sm"></i>
                    </div>
                    <h3 class="font-bold text-gray-900">Environment Info</h3>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between text-sm py-2 border-b border-gray-50">
                    <span class="text-gray-500 font-medium">PHP Version</span>
                    <span class="text-gray-900 font-bold">{{ $stats['server']['php_version'] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm py-2 border-b border-gray-50">
                    <span class="text-gray-500 font-medium">Platform</span>
                    <span class="text-gray-900 font-bold capitalize">{{ $stats['server']['os'] }}</span>
                </div>
                <div class="flex flex-col py-2 border-b border-gray-50">
                    <span class="text-gray-500 font-medium text-xs mb-1">Database version</span>
                    <span class="text-gray-900 font-bold text-sm truncate" x-text="dbVersion">{{ $stats['server']['database'] }}</span>
                </div>
                <div class="flex flex-col py-2 border-b border-gray-50">
                    <span class="text-gray-500 font-medium text-xs mb-1">Server software</span>
                    <span class="text-gray-900 font-bold text-sm truncate">{{ $stats['server']['server_software'] }}</span>
                </div>
                
                <div class="mt-6">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Load Average</p>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 text-center">
                            <p class="text-[9px] font-bold text-gray-500 uppercase">1m</p>
                            <p class="text-sm font-black text-gray-900 mt-0.5" x-text="loadAvg[0]">0</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 text-center">
                            <p class="text-[9px] font-bold text-gray-500 uppercase">5m</p>
                            <p class="text-sm font-black text-gray-900 mt-0.5" x-text="loadAvg[1]">0</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 text-center">
                            <p class="text-[9px] font-bold text-gray-500 uppercase">15m</p>
                            <p class="text-sm font-black text-gray-900 mt-0.5" x-text="loadAvg[2]">0</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    function cpuMonitor() {
        return {
            currentCpu: {{ $stats['cpu']['percentage'] }},
            ramUsage: {{ $stats['memory']['percentage'] }},
            ramDetails: '{{ $stats['memory']['used'] }} / {{ $stats['memory']['total'] }} MB',
            diskUsage: {{ $stats['disk']['percentage'] }},
            diskDetails: '{{ $stats['disk']['used'] }} / {{ $stats['disk']['total'] }} GB',
            uptime: '{{ $stats['uptime'] }}',
            loadAvg: [{{ implode(',', $stats['cpu']['load_avg']) }}],
            dbVersion: '{{ $stats['server']['database'] }}',
            lastUpdated: 0,
            cpuHistory: [],
            timestamps: [],
            chart: null,
            timer: null,
            updateInterval: 5000, // Faster updates (5 seconds)

            init() {
                // Initialize history with empty data
                for (let i = 0; i < 20; i++) {
                    this.cpuHistory.push(this.currentCpu);
                    const time = new Date();
                    time.setSeconds(time.getSeconds() - (20 - i) * 5);
                    this.timestamps.push(time.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' }));
                }

                this.initChart();
                this.startMonitoring();
                
                setInterval(() => { this.lastUpdated++; }, 1000);
            },

            initChart() {
                const options = {
                    series: [{
                        name: 'CPU Load',
                        data: this.cpuHistory
                    }],
                    chart: {
                        type: 'area',
                        height: 350,
                        toolbar: { show: false },
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800,
                            animateGradually: { enabled: true, delay: 150 },
                            dynamicAnimation: { enabled: true, speed: 350 }
                        },
                        fontFamily: 'Satoshi, sans-serif'
                    },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 3, colors: ['#10b981'] },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.35,
                            opacityTo: 0.02,
                            stops: [0, 90, 100],
                            colorStops: [
                                { offset: 0, color: '#10b981', opacity: 0.4 },
                                { offset: 100, color: '#10b981', opacity: 0 }
                            ]
                        }
                    },
                    markers: { size: 0 },
                    xaxis: {
                        categories: this.timestamps,
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                        labels: { show: true, style: { colors: '#94a3b8', fontSize: '10px' } }
                    },
                    yaxis: {
                        min: 0, max: 100,
                        labels: { style: { colors: '#94a3b8', fontSize: '10px' }, formatter: (val) => val + '%' }
                    },
                    grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                    tooltip: { theme: 'light', x: { show: true }, y: { formatter: (val) => val + '%' } },
                };

                this.chart = new ApexCharts(document.querySelector("#cpuChart"), options);
                this.chart.render();
            },

            async fetchData() {
                try {
                    const response = await fetch('{{ route('admin.cpu-monitoring.api') }}');
                    const data = await response.json();
                    
                    this.currentCpu = data.cpu.percentage;
                    this.ramUsage = data.memory.percentage;
                    this.ramDetails = `${data.memory.used} / ${data.memory.total} MB`;
                    this.diskUsage = data.disk.percentage;
                    this.diskDetails = `${data.disk.used} / ${data.disk.total} GB`;
                    this.uptime = data.uptime;
                    this.loadAvg = data.cpu.load_avg;
                    this.lastUpdated = 0;

                    this.cpuHistory.push(this.currentCpu);
                    if (this.cpuHistory.length > 20) this.cpuHistory.shift();

                    this.timestamps.push(data.timestamp);
                    if (this.timestamps.length > 20) this.timestamps.shift();

                    this.chart.updateSeries([{ data: this.cpuHistory }]);
                    this.chart.updateOptions({ xaxis: { categories: this.timestamps } });

                } catch (error) {
                    console.error('Failed to fetch monitoring data:', error);
                }
            },

            getStatusLabel(p) {
                if (p < 50) return 'Normal';
                if (p < 85) return 'Moderate';
                return 'High Load';
            },

            startMonitoring() {
                setInterval(() => { this.fetchData(); }, this.updateInterval);
            }
        }
    }
</script>
@endpush

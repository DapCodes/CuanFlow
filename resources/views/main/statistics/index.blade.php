@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Dashboard & Statistik - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Dashboard & Statistik</span>
</li>
@endsection

@section('content')
<style>
    [x-cloak] { display: none !important; }
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<main class="flex-grow py-8 px-4 bg-gray-50" x-data="statisticsApp()" x-init="init()">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Dashboard & Statistik
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Analisis performa bisnis outlet <span class="font-semibold text-cuan-green">{{ auth()->user()->outlet->name ?? 'CuanFlow' }}</span>
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                {{-- Period Selector --}}
                <div class="flex items-center gap-2 overflow-x-auto hide-scrollbar">
                    <template x-for="(label, key) in periods" :key="key">
                        <button type="button" 
                            @click="setPeriod(key)"
                            :class="period === key ? 'bg-cuan-green text-white shadow-md shadow-cuan-green/20' : 'bg-gray-50 text-gray-600 hover:bg-gray-100 border border-gray-100'"
                            class="px-5 py-2.5 text-xs font-black uppercase tracking-widest rounded-xl transition-all whitespace-nowrap active:scale-95"
                            x-text="label">
                        </button>
                    </template>
                </div>
            </div>
        </section>

        {{-- FILTER PERIODE (BOX) --}}
        <section x-show="period === 'custom'" x-cloak 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5">
            <div class="flex flex-col sm:flex-row items-end sm:items-center gap-4">
                <div class="w-full sm:w-auto">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Dari Tanggal</label>
                    <input type="date" x-model="startDate" class="w-full text-sm border-gray-300 rounded-lg focus:ring-cuan-green/20 focus:border-cuan-green shadow-sm">
                </div>
                <div class="w-full sm:w-auto">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Sampai Tanggal</label>
                    <input type="date" x-model="endDate" class="w-full text-sm border-gray-300 rounded-lg focus:ring-cuan-green/20 focus:border-cuan-green shadow-sm">
                </div>
                <button @click="loadData()" class="w-full sm:w-auto px-6 py-3 bg-cuan-green text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-cuan-dark shadow-sm transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i class="fas fa-sync-alt" :class="loading ? 'animate-spin' : ''"></i>
                    <span>Terapkan Filter</span>
                </button>
            </div>
        </section>

        {{-- LOADING SPINNER --}}
        <div x-show="loading" x-cloak class="flex flex-col items-center justify-center py-20 bg-white border border-gray-200 rounded-xl shadow-sm">
            <div class="animate-spin rounded-full h-10 w-10 border-4 border-gray-100 border-t-cuan-green mb-4"></div>
            <p class="mt-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Sedang memuat data...</p>
        </div>

        {{-- MAIN CONTENT --}}
        <div x-show="!loading" x-cloak class="space-y-5">
            
            {{-- SUMMARY CARDS --}}
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Pendapatan</p>
                    <p class="mt-2 text-2xl font-black text-gray-900" x-text="formatRupiah(summaryData.total_revenue)"></p>
                    <div class="mt-2 flex items-center gap-1.5">
                        <span class="text-[10px] text-emerald-600 font-black uppercase tracking-widest" x-text="formatRupiah(summaryData.avg_revenue_per_day) + ' /hari'"></span>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Laba Bersih</p>
                    <p class="mt-2 text-2xl font-black" 
                       :class="summaryData.net_profit >= 0 ? 'text-cuan-green' : 'text-rose-600'" 
                       x-text="formatRupiah(summaryData.net_profit)"></p>
                    <div class="mt-2">
                        <span class="text-[10px] font-black uppercase tracking-widest" 
                              :class="summaryData.net_profit >= 0 ? 'text-cuan-green' : 'text-rose-600'"
                              x-text="summaryData.net_profit >= 0 ? 'Surplus Operasional' : 'Defisit Operasional'"></span>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Transaksi</p>
                    <p class="mt-2 text-2xl font-black text-gray-900" x-text="formatNumber(summaryData.total_transactions)"></p>
                    <div class="mt-2">
                        <span class="text-[10px] text-blue-600 font-black uppercase tracking-widest" x-text="summaryData.avg_transactions_per_day + ' Tx /hari'"></span>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Item Terjual</p>
                    <p class="mt-2 text-2xl font-black text-gray-900" x-text="formatNumber(summaryData.total_products_sold)"></p>
                    <div class="mt-2">
                        <span class="text-[10px] text-amber-600 font-black uppercase tracking-widest" x-text="summaryData.total_customers + ' Pelanggan'"></span>
                    </div>
                </div>
            </section>

            {{-- SUB-STATS --}}
            <section class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <div class="bg-white border border-gray-200 p-3 rounded-xl shadow-sm">
                    <p class="text-[9px] font-black text-gray-400 uppercase">Laba Kotor</p>
                    <p class="text-xs font-black text-blue-600" x-text="formatRupiah(summaryData.gross_profit)"></p>
                </div>
                <div class="bg-white border border-gray-200 p-3 rounded-xl shadow-sm">
                    <p class="text-[9px] font-black text-gray-400 uppercase">Pengeluaran</p>
                    <p class="text-xs font-black text-rose-500" x-text="formatRupiah(summaryData.total_expenses)"></p>
                </div>
                <div class="bg-white border border-gray-200 p-3 rounded-xl shadow-sm">
                    <p class="text-[9px] font-black text-gray-400 uppercase">Pend. Lain</p>
                    <p class="text-xs font-black text-emerald-600" x-text="formatRupiah(summaryData.extra_income)"></p>
                </div>
                <div class="bg-white border border-gray-200 p-3 rounded-xl shadow-sm">
                    <p class="text-[9px] font-black text-gray-400 uppercase">Piutang</p>
                    <p class="text-xs font-black text-amber-600" x-text="formatRupiah(summaryData.total_piutang)"></p>
                </div>
                <div class="bg-white border border-gray-200 p-3 rounded-xl shadow-sm">
                    <p class="text-[9px] font-black text-gray-400 uppercase">Diskon</p>
                    <p class="text-xs font-black text-orange-500" x-text="formatRupiah(summaryData.total_discounts)"></p>
                </div>
                <div class="bg-white border border-gray-200 p-3 rounded-xl shadow-sm">
                    <p class="text-[9px] font-black text-gray-400 uppercase">Pajak</p>
                    <p class="text-xs font-black text-gray-700" x-text="formatRupiah(summaryData.total_tax)"></p>
                </div>
            </section>

            {{-- CHARTS GRID --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                
                {{-- Sales Trend --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden lg:col-span-2">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">Tren Pendapatan Harian</h3>
                    </div>
                    <div class="p-6">
                        <div class="relative h-64">
                            <canvas x-ref="salesChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Payment Methods --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">Metode Pembayaran</h3>
                    </div>
                    <div class="p-6">
                        <div class="relative h-56">
                            <canvas x-ref="paymentChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Transaction Trend --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">Volume Transaksi</h3>
                    </div>
                    <div class="p-6">
                        <div class="relative h-56">
                            <canvas x-ref="transactionChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Top Products --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden lg:col-span-2">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">10 Produk Terlaris</h3>
                    </div>
                    <div class="p-6">
                        <div class="relative h-64">
                            <canvas x-ref="topProductsChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Categories --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">Kategori Produk</h3>
                    </div>
                    <div class="p-6">
                        <div class="relative h-56">
                            <canvas x-ref="categoryChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Discount Usage --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">Penggunaan Diskon</h3>
                    </div>
                    <div class="p-6">
                        <div class="relative h-56">
                            <canvas x-ref="discountChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Revenue vs Expense --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">Revenue vs Expense</h3>
                    </div>
                    <div class="p-6">
                        <div class="relative h-56">
                            <canvas x-ref="expenseChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Expense Category --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">Kategori Pengeluaran</h3>
                    </div>
                    <div class="p-6">
                        <div class="relative h-56">
                            <canvas x-ref="expenseCategoryChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Profit Trend --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden lg:col-span-2">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">Tren Profitabilitas</h3>
                    </div>
                    <div class="p-6">
                        <div class="relative h-64">
                            <canvas x-ref="profitChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Hourly Sales --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">Jam Sibuk</h3>
                    </div>
                    <div class="p-6">
                        <div class="relative h-56">
                            <canvas x-ref="hourlyChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Weekly Pattern --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">Pola Mingguan</h3>
                    </div>
                    <div class="p-6">
                        <div class="relative h-56">
                            <canvas x-ref="weeklyChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Cashier Performance --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">Performa Kasir</h3>
                    </div>
                    <div class="p-6">
                        <div class="relative h-56">
                            <canvas x-ref="cashierChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Top Customers --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">Pelanggan Loyal</h3>
                    </div>
                    <div class="p-6">
                        <div class="relative h-56">
                            <canvas x-ref="customerChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Stock Movement --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">Pergerakan Stok</h3>
                    </div>
                    <div class="p-6">
                        <div class="relative h-56">
                            <canvas x-ref="stockMovementChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Purchase Trend --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">Tren Pembelian</h3>
                    </div>
                    <div class="p-6">
                        <div class="relative h-56">
                            <canvas x-ref="purchaseChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABLES --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                {{-- Low Stock --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">Stok Menipis</h3>
                        <a href="{{ route('products-hpp.index') }}" class="text-[10px] font-black text-cuan-green uppercase hover:underline tracking-widest">Kelola</a>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($lowStockProducts as $product)
                            <div class="flex items-center justify-between p-4 hover:bg-gray-50 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center overflow-hidden">
                                        @if($product->image)
                                            <img src="{{ Storage::url($product->image) }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="fas fa-box text-gray-400 text-xs"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-gray-900">{{ $product->name }}</p>
                                        <p class="text-[10px] text-gray-400 font-black uppercase">Min: {{ $product->min_stock }}</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 text-xs font-black rounded-full {{ ($product->stocks->first()->quantity ?? 0) <= 0 ? 'bg-rose-100 text-rose-600' : 'bg-amber-100 text-amber-600' }}">
                                    {{ number_format($product->stocks->first()->quantity ?? 0) }}
                                </span>
                            </div>
                        @empty
                            <div class="py-12 text-center">
                                <i class="fas fa-check-double text-emerald-400 text-3xl mb-3 opacity-20"></i>
                                <p class="text-sm text-gray-400 font-black">Semua stok aman</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Sales --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">Transaksi Terakhir</h3>
                        <a href="{{ route('sales.index') }}" class="text-[10px] font-black text-cuan-green uppercase hover:underline tracking-widest">Semua</a>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($recentSales as $sale)
                            <div class="flex items-center justify-between p-4 hover:bg-gray-50 transition-all">
                                <div>
                                    <p class="text-sm font-black text-gray-900">{{ $sale->invoice_number }}</p>
                                    <p class="text-[10px] text-gray-400 font-black uppercase">
                                        {{ $sale->created_at->diffForHumans() }} • {{ $sale->cashier->name ?? 'Kasir' }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-black text-gray-900">{{ 'Rp ' . number_format($sale->grand_total, 0, ',', '.') }}</p>
                                    <p class="text-[9px] text-gray-400 font-black uppercase">{{ $sale->payment_method }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center text-gray-400">
                                <i class="fas fa-inbox text-3xl mb-3 opacity-20"></i>
                                <p class="text-sm font-black">Belum ada transaksi</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function statisticsApp() {
    return {
        loading: true,
        period: '{{ $period ?? "30" }}',
        startDate: '{{ $startDate ?? "" }}',
        endDate: '{{ $endDate ?? "" }}',
        charts: {},
        
        summaryData: {
            total_revenue: {{ $summaryData['total_revenue'] ?? 0 }},
            total_transactions: {{ $summaryData['total_transactions'] ?? 0 }},
            gross_profit: {{ $summaryData['gross_profit'] ?? 0 }},
            total_expenses: {{ $summaryData['total_expenses'] ?? 0 }},
            extra_income: {{ $summaryData['extra_income'] ?? 0 }},
            net_profit: {{ $summaryData['net_profit'] ?? 0 }},
            avg_transactions_per_day: {{ $summaryData['avg_transactions_per_day'] ?? 0 }},
            avg_revenue_per_day: {{ $summaryData['avg_revenue_per_day'] ?? 0 }},
            total_customers: {{ $summaryData['total_customers'] ?? 0 }},
            total_products_sold: {{ $summaryData['total_products_sold'] ?? 0 }},
            total_refunds: {{ $summaryData['total_refunds'] ?? 0 }},
            total_discounts: {{ $summaryData['total_discounts'] ?? 0 }},
            total_tax: {{ $summaryData['total_tax'] ?? 0 }},
            total_piutang: {{ $summaryData['total_piutang'] ?? 0 }},
            total_purchases: {{ $summaryData['total_purchases'] ?? 0 }},
        },

        periods: {
            'today': 'Hari Ini',
            '7': '7 Hari',
            '30': '30 Hari',
            'month': 'Bulan Ini',
            'year': 'Tahun Ini',
            'custom': 'Custom'
        },

        init() {
            // Set Chart.js defaults
            Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
            Chart.defaults.color = '#94a3b8';
            Chart.defaults.plugins.legend.display = false;
            Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(15, 23, 42, 0.95)';
            Chart.defaults.plugins.tooltip.padding = 12;
            Chart.defaults.plugins.tooltip.cornerRadius = 8;
            Chart.defaults.elements.bar.borderRadius = 6;
            Chart.defaults.elements.line.borderWidth = 2;
            Chart.defaults.elements.point.radius = 0;
            Chart.defaults.elements.point.hoverRadius = 5;
            
            this.loadData();
        },

        formatRupiah(value) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value || 0);
        },

        formatNumber(value) {
            return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 1 }).format(value || 0);
        },

        buildQueryParams() {
            const params = new URLSearchParams({ period: this.period });
            if (this.period === 'custom' && this.startDate && this.endDate) {
                params.set('start_date', this.startDate);
                params.set('end_date', this.endDate);
            }
            return params.toString();
        },

        async setPeriod(key) {
            this.period = key;
            if (key !== 'custom') {
                await this.loadData();
            }
        },

        async loadData() {
            this.loading = true;
            
            // Clear existing chart instances from our object
            this.charts = {};

            try {
                const query = this.buildQueryParams();
                
                // Fetch summary statistics
                const summaryRes = await fetch(`/statistics/summary?${query}`);
                if (summaryRes.ok) {
                    this.summaryData = await summaryRes.json();
                }

                // Fetch all chart data in parallel
                const endpoints = [
                    { key: 'sales', url: '/statistics/sales-chart' },
                    { key: 'payment', url: '/statistics/payment-method-chart' },
                    { key: 'transaction', url: '/statistics/transaction-chart' },
                    { key: 'topProducts', url: '/statistics/top-products-chart' },
                    { key: 'category', url: '/statistics/category-chart' },
                    { key: 'discount', url: '/statistics/discount-usage-chart' },
                    { key: 'expense', url: '/statistics/expense-chart' },
                    { key: 'expenseCategory', url: '/statistics/expense-category-chart' },
                    { key: 'profit', url: '/statistics/profit-chart' },
                    { key: 'hourly', url: '/statistics/hourly-chart' },
                    { key: 'weekly', url: '/statistics/weekly-chart' },
                    { key: 'cashier', url: '/statistics/cashier-performance-chart' },
                    { key: 'customer', url: '/statistics/top-customers-chart' },
                    { key: 'stockMovement', url: '/statistics/stock-movement-chart' },
                    { key: 'purchase', url: '/statistics/purchase-chart' }
                ];

                const chartDataResults = await Promise.all(
                    endpoints.map(e => fetch(`${e.url}?${query}`).then(r => r.json()))
                );

                this.loading = false;

                // Wait for Alpine to show the content div and render canvases
                await this.$nextTick();

                // Render each chart
                endpoints.forEach((e, i) => {
                    const data = chartDataResults[i];
                    if (e.key === 'topProducts' || e.key === 'cashier' || e.key === 'customer') {
                        this.renderChart(e.key, 'bar', data, 'y');
                    } else if (e.key === 'payment' || e.key === 'category' || e.key === 'expenseCategory') {
                        this.renderChart(e.key, 'doughnut', data);
                    } else if (e.key === 'hourly' || e.key === 'weekly') {
                        this.renderChart(e.key, 'bar', data);
                    } else if (e.key === 'expense' || e.key === 'stockMovement') {
                        this.renderChart(e.key, 'line', data, null, true);
                    } else {
                        this.renderChart(e.key, 'line', data);
                    }
                });

            } catch (error) {
                console.error('Error loading statistics:', error);
                this.loading = false;
            }
        },

        renderChart(name, type, data, indexAxis = null, showLegend = false) {
            const canvas = this.$refs[name + 'Chart'];
            if (!canvas) return;

            // Ensure any existing chart on this canvas is destroyed
            const existingChart = Chart.getChart(canvas);
            if (existingChart) {
                existingChart.destroy();
            }

            const ctx = canvas.getContext('2d');
            if (!ctx) return;

            // Check if data is empty or all zeros
            const hasData = data && data.labels && data.labels.length > 0 && 
                            data.datasets && data.datasets.some(ds => ds.data && ds.data.some(v => v > 0));

            const container = canvas.parentElement;
            
            // Remove any existing "No Data" overlay
            const oldOverlay = container.querySelector('.no-data-overlay');
            if (oldOverlay) oldOverlay.remove();

            if (!hasData) {
                canvas.style.display = 'none';
                const overlay = document.createElement('div');
                overlay.className = 'no-data-overlay absolute inset-0 flex flex-col items-center justify-center bg-gray-50/50 rounded-lg border border-dashed border-gray-200';
                overlay.innerHTML = `
                    <i class="fas fa-inbox text-gray-300 text-xl mb-2"></i>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-tight">Belum ada data</p>
                `;
                container.appendChild(overlay);
                return;
            }

            canvas.style.display = 'block';

            let options = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: showLegend }
                }
            };
            
            if (type === 'doughnut') {
                options.cutout = '70%';
                options.plugins.legend = {
                    display: true,
                    position: 'right',
                    labels: { boxWidth: 8, padding: 10, font: { size: 9, weight: 'bold' }, usePointStyle: true }
                };
            } else {
                options.scales = {
                    y: {
                        beginAtZero: true,
                        ticks: { font: { size: 9 } },
                        grid: { color: '#f1f5f9', borderDash: [4, 4] },
                        border: { display: false }
                    },
                    x: {
                        ticks: { font: { size: 9 } },
                        grid: { display: false }
                    }
                };
                if (indexAxis === 'y') {
                    options.indexAxis = 'y';
                }
            }

            if (type === 'line') {
                options.interaction = { intersect: false, mode: 'index' };
            }

            if (showLegend && type !== 'doughnut') {
                options.plugins.legend = {
                    display: true,
                    position: 'top',
                    labels: { boxWidth: 10, font: { size: 9, weight: 'bold' }, usePointStyle: true }
                };
            }

            try {
                this.charts[name] = new Chart(ctx, {
                    type: type,
                    data: {
                        labels: data.labels,
                        datasets: data.datasets
                    },
                    options: options
                });
            } catch (error) {
                console.error(`Error creating ${name} chart:`, error);
            }
        }
    }
}
</script>
@endpush

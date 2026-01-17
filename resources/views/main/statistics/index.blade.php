@extends('layouts.app')

@section('title', 'Dashboard & Statistik - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Dashboard & Statistik</span>
</li>
@endsection

@section('content')
<style>
    [x-cloak] { display: none !important; }
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<main class="flex-grow py-6 px-4 bg-gray-50" x-data="statisticsApp()" x-init="init()">
    <div class="max-w-7xl mx-auto space-y-5">

        {{-- HEADER & FILTERS --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100">
                            <i class="fas fa-chart-bar text-sm"></i>
                        </span>
                        <span>Dashboard & Statistik</span>
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Analisis performa bisnis outlet <span class="font-semibold text-indigo-600">{{ auth()->user()->outlet->name ?? 'CuanFlow' }}</span>
                    </p>
                </div>
                
                {{-- Period Selector --}}
                <div class="flex flex-wrap items-center gap-2">
                    <div class="flex items-center gap-1.5 bg-gray-50 p-1 rounded-lg border border-gray-200 overflow-x-auto hide-scrollbar">
                        <template x-for="(label, key) in periods" :key="key">
                            <button type="button" 
                                @click="setPeriod(key)"
                                :class="period === key ? 'bg-indigo-600 text-white shadow-sm font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'"
                                class="px-3 py-1.5 text-xs rounded-md transition-all whitespace-nowrap"
                                x-text="label">
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Custom Date Picker --}}
            <template x-if="period === 'custom'">
                <div x-transition class="border-t border-gray-100 mt-5 pt-5">
                    <div class="flex flex-wrap items-end gap-3">
                        <div class="w-full sm:w-44">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Dari</label>
                            <input type="date" x-model="startDate" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                        </div>
                        <div class="w-full sm:w-44">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Hingga</label>
                            <input type="date" x-model="endDate" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                        </div>
                        <button @click="loadData()" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 shadow-sm transition-all flex items-center gap-2">
                            <i class="fas fa-sync-alt" :class="loading ? 'animate-spin' : ''"></i> Terapkan
                        </button>
                    </div>
                </div>
            </template>
        </section>

        {{-- LOADING SPINNER --}}
        <div x-show="loading" x-cloak class="flex flex-col items-center justify-center py-20 bg-white border border-gray-200 rounded-xl shadow-sm">
            <div class="animate-spin rounded-full h-10 w-10 border-4 border-gray-100 border-t-indigo-600 mb-4"></div>
            <p class="text-gray-500 font-semibold text-sm">Menghitung statistik...</p>
        </div>

        {{-- MAIN CONTENT --}}
        <div x-show="!loading" x-cloak class="space-y-5">
            
            {{-- SUMMARY CARDS --}}
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pendapatan</p>
                            <p class="text-xl font-extrabold text-gray-900 mt-1" x-text="formatRupiah(summaryData.total_revenue)"></p>
                            <p class="text-[10px] text-emerald-600 font-bold mt-1">
                                <span x-text="formatRupiah(summaryData.avg_revenue_per_day)"></span>/hari
                            </p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Laba Bersih</p>
                            <p class="text-xl font-extrabold mt-1" 
                               :class="summaryData.net_profit >= 0 ? 'text-indigo-600' : 'text-rose-600'" 
                               x-text="formatRupiah(summaryData.net_profit)"></p>
                            <p class="text-[10px] font-bold mt-1" 
                               :class="summaryData.net_profit >= 0 ? 'text-indigo-600' : 'text-rose-600'"
                               x-text="summaryData.net_profit >= 0 ? 'Surplus' : 'Defisit'"></p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Transaksi</p>
                            <p class="text-xl font-extrabold text-gray-900 mt-1" x-text="formatNumber(summaryData.total_transactions)"></p>
                            <p class="text-[10px] text-blue-600 font-bold mt-1">
                                <span x-text="summaryData.avg_transactions_per_day"></span> tx/hari
                            </p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                            <i class="fas fa-receipt"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Item Terjual</p>
                            <p class="text-xl font-extrabold text-gray-900 mt-1" x-text="formatNumber(summaryData.total_products_sold)"></p>
                            <p class="text-[10px] text-amber-600 font-bold mt-1">
                                <span x-text="summaryData.total_customers"></span> pelanggan
                            </p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100">
                            <i class="fas fa-box"></i>
                        </div>
                    </div>
                </div>
            </section>

            {{-- SUB-STATS --}}
            <section class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <div class="bg-white border border-gray-200 p-3 rounded-xl shadow-sm">
                    <p class="text-[9px] font-bold text-gray-400 uppercase">Laba Kotor</p>
                    <p class="text-xs font-bold text-blue-600" x-text="formatRupiah(summaryData.gross_profit)"></p>
                </div>
                <div class="bg-white border border-gray-200 p-3 rounded-xl shadow-sm">
                    <p class="text-[9px] font-bold text-gray-400 uppercase">Pengeluaran</p>
                    <p class="text-xs font-bold text-rose-500" x-text="formatRupiah(summaryData.total_expenses)"></p>
                </div>
                <div class="bg-white border border-gray-200 p-3 rounded-xl shadow-sm">
                    <p class="text-[9px] font-bold text-gray-400 uppercase">Pend. Lain</p>
                    <p class="text-xs font-bold text-emerald-600" x-text="formatRupiah(summaryData.extra_income)"></p>
                </div>
                <div class="bg-white border border-gray-200 p-3 rounded-xl shadow-sm">
                    <p class="text-[9px] font-bold text-gray-400 uppercase">Piutang</p>
                    <p class="text-xs font-bold text-amber-600" x-text="formatRupiah(summaryData.total_piutang)"></p>
                </div>
                <div class="bg-white border border-gray-200 p-3 rounded-xl shadow-sm">
                    <p class="text-[9px] font-bold text-gray-400 uppercase">Diskon</p>
                    <p class="text-xs font-bold text-orange-500" x-text="formatRupiah(summaryData.total_discounts)"></p>
                </div>
                <div class="bg-white border border-gray-200 p-3 rounded-xl shadow-sm">
                    <p class="text-[9px] font-bold text-gray-400 uppercase">Pajak</p>
                    <p class="text-xs font-bold text-gray-700" x-text="formatRupiah(summaryData.total_tax)"></p>
                </div>
            </section>

            {{-- CHARTS GRID --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                
                {{-- Sales Trend --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 lg:col-span-2">
                    <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-chart-line text-indigo-500"></i> Tren Pendapatan Harian
                    </h3>
                    <div class="relative h-64">
                        <canvas x-ref="salesChart"></canvas>
                    </div>
                </div>

                {{-- Payment Methods --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                    <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-wallet text-indigo-500"></i> Metode Pembayaran
                    </h3>
                    <div class="relative h-56">
                        <canvas x-ref="paymentChart"></canvas>
                    </div>
                </div>

                {{-- Transaction Trend --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                    <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-receipt text-blue-500"></i> Volume Transaksi
                    </h3>
                    <div class="relative h-56">
                        <canvas x-ref="transactionChart"></canvas>
                    </div>
                </div>

                {{-- Top Products --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 lg:col-span-2">
                    <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-trophy text-amber-500"></i> 10 Produk Terlaris
                    </h3>
                    <div class="relative h-64">
                        <canvas x-ref="topProductsChart"></canvas>
                    </div>
                </div>

                {{-- Categories --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                    <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-tags text-indigo-500"></i> Kategori Produk
                    </h3>
                    <div class="relative h-56">
                        <canvas x-ref="categoryChart"></canvas>
                    </div>
                </div>

                {{-- Discount Usage --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                    <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-tag text-orange-500"></i> Penggunaan Diskon
                    </h3>
                    <div class="relative h-56">
                        <canvas x-ref="discountChart"></canvas>
                    </div>
                </div>

                {{-- Revenue vs Expense --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                    <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-balance-scale text-emerald-500"></i> Revenue vs Expense
                    </h3>
                    <div class="relative h-56">
                        <canvas x-ref="expenseChart"></canvas>
                    </div>
                </div>

                {{-- Expense Category --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                    <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-pie-chart text-rose-500"></i> Kategori Pengeluaran
                    </h3>
                    <div class="relative h-56">
                        <canvas x-ref="expenseCategoryChart"></canvas>
                    </div>
                </div>

                {{-- Profit Trend --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 lg:col-span-2">
                    <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-chart-area text-indigo-500"></i> Tren Profitabilitas
                    </h3>
                    <div class="relative h-64">
                        <canvas x-ref="profitChart"></canvas>
                    </div>
                </div>

                {{-- Hourly Sales --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                    <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-clock text-sky-500"></i> Jam Sibuk
                    </h3>
                    <div class="relative h-56">
                        <canvas x-ref="hourlyChart"></canvas>
                    </div>
                </div>

                {{-- Weekly Pattern --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                    <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-calendar-week text-violet-500"></i> Pola Mingguan
                    </h3>
                    <div class="relative h-56">
                        <canvas x-ref="weeklyChart"></canvas>
                    </div>
                </div>

                {{-- Cashier Performance --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                    <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-users text-indigo-500"></i> Performa Kasir
                    </h3>
                    <div class="relative h-56">
                        <canvas x-ref="cashierChart"></canvas>
                    </div>
                </div>

                {{-- Top Customers --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                    <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-crown text-amber-500"></i> Pelanggan Loyal
                    </h3>
                    <div class="relative h-56">
                        <canvas x-ref="customerChart"></canvas>
                    </div>
                </div>

                {{-- Stock Movement --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                    <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-exchange-alt text-emerald-500"></i> Pergerakan Stok
                    </h3>
                    <div class="relative h-56">
                        <canvas x-ref="stockMovementChart"></canvas>
                    </div>
                </div>

                {{-- Purchase Trend --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                    <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-truck text-violet-500"></i> Tren Pembelian
                    </h3>
                    <div class="relative h-56">
                        <canvas x-ref="purchaseChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- TABLES --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                {{-- Low Stock --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-box-open text-rose-500"></i> Stok Menipis
                        </h3>
                        <a href="{{ route('products-hpp.index') }}" class="text-[10px] font-bold text-indigo-600 uppercase hover:underline">Kelola</a>
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
                                        <p class="text-sm font-bold text-gray-900">{{ $product->name }}</p>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase">Min: {{ $product->min_stock }}</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 text-xs font-black rounded-full {{ ($product->stocks->first()->quantity ?? 0) <= 0 ? 'bg-rose-100 text-rose-600' : 'bg-amber-100 text-amber-600' }}">
                                    {{ number_format($product->stocks->first()->quantity ?? 0) }}
                                </span>
                            </div>
                        @empty
                            <div class="py-12 text-center">
                                <i class="fas fa-check-double text-emerald-400 text-3xl mb-3 opacity-20"></i>
                                <p class="text-sm text-gray-400 font-bold">Semua stok aman</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Sales --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-history text-indigo-500"></i> Transaksi Terakhir
                        </h3>
                        <a href="{{ route('sales.index') }}" class="text-[10px] font-bold text-indigo-600 uppercase hover:underline">Semua</a>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($recentSales as $sale)
                            <div class="flex items-center justify-between p-4 hover:bg-gray-50 transition-all">
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $sale->invoice_number }}</p>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase">
                                        {{ $sale->created_at->diffForHumans() }} • {{ $sale->cashier->name ?? 'Kasir' }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-black text-gray-900">{{ 'Rp ' . number_format($sale->grand_total, 0, ',', '.') }}</p>
                                    <p class="text-[9px] text-gray-400 font-bold uppercase">{{ $sale->payment_method }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center text-gray-400">
                                <i class="fas fa-inbox text-3xl mb-3 opacity-20"></i>
                                <p class="text-sm font-bold">Belum ada transaksi</p>
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
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">Belum ada data</p>
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

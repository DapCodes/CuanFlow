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
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-green-400 to-blue-500 text-white">
                        <i class="fas fa-chart-line text-sm"></i>
                    </span>
                    <span>Dashboard & Statistik</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Pantau performa bisnis Anda dengan visualisasi data yang lengkap dan akurat
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="inline-flex rounded-lg border border-gray-200 p-1 bg-gray-50">
                    <button type="button" data-period="today" class="period-btn px-3 py-1.5 text-xs font-medium rounded-md transition-all {{ $period == 'today' ? 'bg-white shadow text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                        Hari Ini
                    </button>
                    <button type="button" data-period="7" class="period-btn px-3 py-1.5 text-xs font-medium rounded-md transition-all {{ $period == '7' ? 'bg-white shadow text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                        7 Hari
                    </button>
                    <button type="button" data-period="30" class="period-btn px-3 py-1.5 text-xs font-medium rounded-md transition-all {{ $period == '30' ? 'bg-white shadow text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                        30 Hari
                    </button>
                    <button type="button" data-period="month" class="period-btn px-3 py-1.5 text-xs font-medium rounded-md transition-all {{ $period == 'month' ? 'bg-white shadow text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                        Bulan Ini
                    </button>
                </div>
                @can('ekspor statistik')
                <a href="{{ route('statistics.export', ['period' => $period]) }}" data-no-loader target="_blank" rel="noopener"
                   id="exportBtn"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-xs font-semibold rounded-lg shadow hover:from-green-600 hover:to-emerald-700 transition-all duration-200">
                    <i class="fas fa-file-excel"></i>
                    <span>Export Excel</span>
                </a>
                @endcan
            </div>
        </section>

        {{-- SUMMARY CARDS --}}
        <section class="grid grid-cols-2 md:grid-cols-4 gap-4">
            {{-- Total Revenue --}}
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Pendapatan</p>
                        <p id="cardRevenue" class="mt-1 text-xl md:text-2xl font-bold text-green-600">
                            Rp {{ number_format($summaryData['total_revenue'], 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            <span id="cardAvgRevenue">Rp {{ number_format($summaryData['avg_revenue_per_day'], 0, ',', '.') }}</span>/hari
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                        <i class="fas fa-coins text-green-600"></i>
                    </div>
                </div>
            </div>

            {{-- Total Transaksi --}}
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Transaksi</p>
                        <p id="cardTransactions" class="mt-1 text-xl md:text-2xl font-bold text-blue-600">
                            {{ number_format($summaryData['total_transactions']) }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            <span id="cardAvgTx">{{ $summaryData['avg_transactions_per_day'] }}</span> tx/hari
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-receipt text-blue-600"></i>
                    </div>
                </div>
            </div>

            {{-- Gross Profit --}}
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Laba Kotor</p>
                        <p id="cardProfit" class="mt-1 text-xl md:text-2xl font-bold text-purple-600">
                            Rp {{ number_format($summaryData['gross_profit'], 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            <span id="cardProductsSold">{{ number_format($summaryData['total_products_sold']) }}</span> produk terjual
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                        <i class="fas fa-chart-pie text-purple-600"></i>
                    </div>
                </div>
            </div>

            {{-- Net Profit --}}
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Laba Bersih</p>
                        <p id="cardNetProfit" class="mt-1 text-xl md:text-2xl font-bold {{ $summaryData['net_profit'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                            Rp {{ number_format($summaryData['net_profit'], 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            Pengeluaran: <span id="cardExpenses">Rp {{ number_format($summaryData['total_expenses'], 0, ',', '.') }}</span>
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <i class="fas fa-wallet text-emerald-600"></i>
                    </div>
                </div>
            </div>
        </section>

        {{-- CHARTS ROW 1 - Sales Trend & Payment Methods --}}
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- Sales Trend Chart --}}
            <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-chart-line text-green-500"></i>
                        Tren Penjualan
                    </h3>
                </div>
                <div class="p-5">
                    <canvas id="salesChart" height="280"></canvas>
                </div>
            </div>

            {{-- Payment Methods Chart --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-credit-card text-blue-500"></i>
                        Metode Pembayaran
                    </h3>
                </div>
                <div class="p-5 flex items-center justify-center">
                    <canvas id="paymentChart" height="240"></canvas>
                </div>
            </div>
        </section>

        {{-- CHARTS ROW 2 - Top Products & Categories --}}
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Top Products Chart --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-trophy text-yellow-500"></i>
                        Top 10 Produk Terlaris
                    </h3>
                </div>
                <div class="p-5">
                    <canvas id="topProductsChart" height="300"></canvas>
                </div>
            </div>

            {{-- Categories Chart --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-tags text-purple-500"></i>
                        Penjualan per Kategori
                    </h3>
                </div>
                <div class="p-5 flex items-center justify-center">
                    <canvas id="categoryChart" height="260"></canvas>
                </div>
            </div>
        </section>

        {{-- CHARTS ROW 3 - Hourly & Revenue vs Expense --}}
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Hourly Sales Chart --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-clock text-cyan-500"></i>
                        Penjualan per Jam (Peak Hours)
                    </h3>
                </div>
                <div class="p-5">
                    <canvas id="hourlyChart" height="260"></canvas>
                </div>
            </div>

            {{-- Revenue vs Expense Chart --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-balance-scale text-orange-500"></i>
                        Pendapatan vs Pengeluaran
                    </h3>
                </div>
                <div class="p-5">
                    <canvas id="expenseChart" height="260"></canvas>
                </div>
            </div>
        </section>

        {{-- BOTTOM ROW - Low Stock & Recent Sales --}}
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Low Stock Products --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                        Stok Rendah
                    </h3>
                    <a href="{{ route('products-hpp.index') }}" class="text-xs text-blue-600 hover:underline">
                        Lihat Semua
                    </a>
                </div>
                <div class="p-4">
                    @forelse($lowStockProducts as $product)
                        <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                            <div class="flex items-center gap-3">
                                @if($product->image)
                                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-10 h-10 rounded-lg object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                        <i class="fas fa-box text-gray-400"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500">Min: {{ $product->min_stock }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs font-bold rounded-full {{ $product->stocks->first()->quantity <= 0 ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ number_format($product->stocks->first()->quantity) }}
                            </span>
                        </div>
                    @empty
                        <div class="py-8 text-center text-gray-500">
                            <i class="fas fa-check-circle text-3xl mb-2 text-green-400"></i>
                            <p class="text-sm">Semua stok dalam kondisi aman</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Recent Sales --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-history text-blue-500"></i>
                        Transaksi Terbaru
                    </h3>
                    <a href="{{ route('sales.index') }}" class="text-xs text-blue-600 hover:underline">
                        Lihat Semua
                    </a>
                </div>
                <div class="p-4">
                    @forelse($recentSales as $sale)
                        <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $sale->invoice_number }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $sale->created_at->format('d M Y, H:i') }} • {{ $sale->cashier->name ?? '-' }}
                                </p>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">
                                Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                            </span>
                        </div>
                    @empty
                        <div class="py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-3xl mb-2 text-gray-300"></i>
                            <p class="text-sm">Belum ada transaksi</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

    </div>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPeriod = '{{ $period }}';
    let charts = {};

    // Format currency
    const rupiah = (n) => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(n);

    // Chart.js global defaults
    Chart.defaults.font.family = "'Satoshi', sans-serif";
    Chart.defaults.plugins.legend.display = false;

    // Initialize all charts
    async function initCharts() {
        await Promise.all([
            loadSalesChart(),
            loadPaymentChart(),
            loadTopProductsChart(),
            loadCategoryChart(),
            loadHourlyChart(),
            loadExpenseChart()
        ]);
    }

    // Sales Trend Chart
    async function loadSalesChart() {
        try {
            const res = await fetch(`/statistics/sales-chart?period=${currentPeriod}`);
            const data = await res.json();

            if (charts.sales) charts.sales.destroy();

            charts.sales = new Chart(document.getElementById('salesChart'), {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: data.datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: (ctx) => rupiah(ctx.raw)
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (v) => 'Rp ' + (v / 1000000).toFixed(1) + 'jt'
                            }
                        }
                    }
                }
            });
        } catch (e) {
            console.error('Sales chart error:', e);
        }
    }

    // Payment Methods Chart
    async function loadPaymentChart() {
        try {
            const res = await fetch(`/statistics/payment-method-chart?period=${currentPeriod}`);
            const data = await res.json();

            if (charts.payment) charts.payment.destroy();

            charts.payment = new Chart(document.getElementById('paymentChart'), {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: data.datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `${ctx.label}: ${rupiah(ctx.raw)}`
                            }
                        }
                    }
                }
            });
        } catch (e) {
            console.error('Payment chart error:', e);
        }
    }

    // Top Products Chart
    async function loadTopProductsChart() {
        try {
            const res = await fetch(`/statistics/top-products-chart?period=${currentPeriod}`);
            const data = await res.json();

            if (charts.topProducts) charts.topProducts.destroy();

            charts.topProducts = new Chart(document.getElementById('topProductsChart'), {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: data.datasets
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                afterLabel: (ctx) => `Revenue: ${rupiah(data.revenue[ctx.dataIndex])}`
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });
        } catch (e) {
            console.error('Top products chart error:', e);
        }
    }

    // Category Chart
    async function loadCategoryChart() {
        try {
            const res = await fetch(`/statistics/category-chart?period=${currentPeriod}`);
            const data = await res.json();

            if (charts.category) charts.category.destroy();

            charts.category = new Chart(document.getElementById('categoryChart'), {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: data.datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `${ctx.label}: ${rupiah(ctx.raw)}`
                            }
                        }
                    }
                }
            });
        } catch (e) {
            console.error('Category chart error:', e);
        }
    }

    // Hourly Chart
    async function loadHourlyChart() {
        try {
            const res = await fetch(`/statistics/hourly-chart?period=${currentPeriod}`);
            const data = await res.json();

            if (charts.hourly) charts.hourly.destroy();

            charts.hourly = new Chart(document.getElementById('hourlyChart'), {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: data.datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                afterLabel: (ctx) => `Revenue: ${rupiah(data.revenue[ctx.dataIndex])}`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        } catch (e) {
            console.error('Hourly chart error:', e);
        }
    }

    // Expense Chart
    async function loadExpenseChart() {
        try {
            const res = await fetch(`/statistics/expense-chart?period=${currentPeriod}`);
            const data = await res.json();

            if (charts.expense) charts.expense.destroy();

            charts.expense = new Chart(document.getElementById('expenseChart'), {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: data.datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `${ctx.dataset.label}: ${rupiah(ctx.raw)}`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (v) => 'Rp ' + (v / 1000000).toFixed(1) + 'jt'
                            }
                        }
                    }
                }
            });
        } catch (e) {
            console.error('Expense chart error:', e);
        }
    }

    // Period button handlers
    document.querySelectorAll('.period-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const newPeriod = this.dataset.period;
            if (newPeriod === currentPeriod) return;

            // Update button states
            document.querySelectorAll('.period-btn').forEach(b => {
                b.classList.remove('bg-white', 'shadow', 'text-gray-900');
                b.classList.add('text-gray-600');
            });
            this.classList.add('bg-white', 'shadow', 'text-gray-900');
            this.classList.remove('text-gray-600');

            currentPeriod = newPeriod;

            // Reload page with new period to update summary cards
            window.location.href = `/statistics?period=${currentPeriod}`;
        });
    });

    // Initialize charts on load
    initCharts();
});
</script>
@endpush

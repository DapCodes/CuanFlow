@extends('layouts.app')

@section('title', 'Laporan Bisnis - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Laporan Bisnis</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER & FILTERS --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-blue-400 to-indigo-500 text-white">
                            <i class="fas fa-file-alt text-sm"></i>
                        </span>
                        <span>Laporan Bisnis</span>
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Analisis lengkap performa bisnis Anda (Penjualan, Pengeluaran, Laba)
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('reports.export-excel', request()->all()) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <a href="{{ route('reports.export-pdf', request()->all()) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                </div>
            </div>

            <form action="{{ route('reports.index') }}" method="GET" id="filterForm" class="border-t border-gray-100 pt-4">
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-2 bg-gray-50 p-1 rounded-lg border border-gray-200">
                        @foreach(['today' => 'Hari Ini', 'yesterday' => 'Kemarin', '7_days' => '7 Hari', '30_days' => '30 Hari', 'this_month' => 'Bulan Ini', 'this_year' => 'Tahun Ini', 'custom' => 'Custom'] as $key => $label)
                            <button type="button" 
                                onclick="setPeriod('{{ $key }}')"
                                class="px-3 py-1.5 text-xs font-medium rounded-md transition-all {{ $period == $key ? 'bg-white shadow text-blue-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="period" id="periodInput" value="{{ $period }}">
                </div>

                <div id="customDateRange" class="mt-4 flex items-center gap-4 {{ $period == 'custom' ? '' : 'hidden' }}">
                    <div class="flex items-center gap-2">
                        <label class="text-sm text-gray-600">Dari:</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-sm text-gray-600">Sampai:</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </section>

        {{-- SUMMARY CARDS --}}
        <section class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Pendapatan</p>
                <p class="mt-1 text-xl md:text-2xl font-bold text-green-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Pengeluaran</p>
                <p class="mt-1 text-xl md:text-2xl font-bold text-red-600">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Laba Kotor</p>
                <p class="mt-1 text-xl md:text-2xl font-bold text-blue-600">Rp {{ number_format($grossProfit, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-1">Rev - HPP</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Laba Bersih</p>
                <p class="mt-1 text-xl md:text-2xl font-bold {{ $netProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                    Rp {{ number_format($netProfit, 0, ',', '.') }}
                </p>
                <p class="text-xs text-gray-400 mt-1">Gross - Exp</p>
            </div>
        </section>

        {{-- DETAILED SECTIONS --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- LEFT COLUMN: Sales & Expenses --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Sales Table --}}
                <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-900">Riwayat Penjualan Terbaru</h3>
                        <span class="text-xs text-gray-500">{{ $sales->count() }} Transaksi</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-600 font-medium border-b border-gray-100">
                                <tr>
                                    <th class="px-5 py-3">Tanggal</th>
                                    <th class="px-5 py-3">Invoice</th>
                                    <th class="px-5 py-3">Pelanggan</th>
                                    <th class="px-5 py-3 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($sales->take(10) as $sale)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-3 text-gray-600">{{ $sale->created_at->format('d M H:i') }}</td>
                                    <td class="px-5 py-3 font-medium text-gray-900">{{ $sale->invoice_number }}</td>
                                    <td class="px-5 py-3 text-gray-600">{{ $sale->customer->name ?? '-' }}</td>
                                    <td class="px-5 py-3 text-right font-medium text-gray-900">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-gray-500">Tidak ada data penjualan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($sales->count() > 10)
                    <div class="px-5 py-3 border-t border-gray-100 text-center">
                        <a href="{{ route('sales.index') }}" class="text-sm text-blue-600 hover:underline">Lihat Semua Penjualan</a>
                    </div>
                    @endif
                </section>

                {{-- Expenses Table --}}
                <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-900">Riwayat Pengeluaran Terbaru</h3>
                        <span class="text-xs text-gray-500">{{ $expenses->count() }} Item</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-600 font-medium border-b border-gray-100">
                                <tr>
                                    <th class="px-5 py-3">Tanggal</th>
                                    <th class="px-5 py-3">Kategori</th>
                                    <th class="px-5 py-3">Deskripsi</th>
                                    <th class="px-5 py-3 text-right">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($expenses->take(10) as $expense)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-3 text-gray-600">{{ \Carbon\Carbon::parse($expense->date)->format('d M Y') }}</td>
                                    <td class="px-5 py-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            {{ $expense->category ?? 'Umum' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-gray-600">{{ Str::limit($expense->description, 30) }}</td>
                                    <td class="px-5 py-3 text-right font-medium text-red-600">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-gray-500">Tidak ada data pengeluaran</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            {{-- RIGHT COLUMN: Top Products & Payment Methods --}}
            <div class="space-y-6">
                {{-- Top Products --}}
                <section class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                    <h3 class="font-semibold text-gray-900 mb-4">Produk Terlaris</h3>
                    <div class="space-y-4">
                        @forelse($topProducts as $index => $product)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 flex items-center justify-center rounded-full bg-yellow-100 text-yellow-700 text-xs font-bold">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $product->product_name }}</p>
                                    <p class="text-xs text-gray-500">{{ number_format($product->total_qty, 0, ',', '.') }} terjual</p>
                                </div>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</span>
                        </div>
                        @empty
                        <p class="text-sm text-gray-500 text-center py-4">Belum ada data</p>
                        @endforelse
                    </div>
                </section>

                {{-- Payment Methods --}}
                <section class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                    <h3 class="font-semibold text-gray-900 mb-4">Metode Pembayaran</h3>
                    <div class="space-y-3">
                        @foreach($paymentMethods as $method)
                        <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 border border-gray-100">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-wallet text-gray-400"></i>
                                <span class="text-sm font-medium text-gray-700">{{ ucfirst($method->payment_method) }}</span>
                            </div>
                            <span class="text-sm font-bold text-gray-900">{{ $method->total }} Tx</span>
                        </div>
                        @endforeach
                    </div>
                </section>
            </div>

        </div>

    </div>
</main>

<script>
    function setPeriod(period) {
        document.getElementById('periodInput').value = period;
        if (period === 'custom') {
            document.getElementById('customDateRange').classList.remove('hidden');
        } else {
            document.getElementById('customDateRange').classList.add('hidden');
            document.getElementById('filterForm').submit();
        }
    }
</script>
@endsection

@extends('layouts.app')

@section('title', 'Laporan Bisnis Komprehensif - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Laporan Bisnis</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50" x-data="{ activeTab: 'summary' }">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER & FILTERS --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 text-white">
                            <i class="fas fa-chart-pie text-sm"></i>
                        </span>
                        <span>Laporan Bisnis Komprehensif</span>
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Analisis menyeluruh: Penjualan, Stok, Bahan, dan Performa Karyawan
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button @click="$dispatch('open-modal', 'export-excel-modal')" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                    <button @click="$dispatch('open-modal', 'export-pdf-modal')" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
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

        {{-- TABS NAVIGATION --}}
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs">
                <button @click="activeTab = 'summary'" :class="activeTab === 'summary' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Ringkasan
                </button>
                <button @click="activeTab = 'sales'" :class="activeTab === 'sales' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Penjualan
                </button>
                <button @click="activeTab = 'stock'" :class="activeTab === 'stock' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Stok & Bahan
                </button>
                <button @click="activeTab = 'cashier'" :class="activeTab === 'cashier' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Performa Kasir
                </button>
                <button @click="activeTab = 'hourly'" :class="activeTab === 'hourly' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Analisis Waktu
                </button>
            </nav>
        </div>

        {{-- TAB CONTENTS --}}
        
        {{-- 1. SUMMARY TAB --}}
        <div x-show="activeTab === 'summary'" class="space-y-6">
            {{-- Summary Cards --}}
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

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
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

        {{-- 2. SALES TAB --}}
        <div x-show="activeTab === 'sales'" class="space-y-6" style="display: none;">
            <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-900">Riwayat Penjualan</h3>
                    <span class="text-xs text-gray-500">{{ $sales->count() }} Transaksi</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-600 font-medium border-b border-gray-100">
                            <tr>
                                <th class="px-5 py-3">Tanggal</th>
                                <th class="px-5 py-3">Invoice</th>
                                <th class="px-5 py-3">Kasir</th>
                                <th class="px-5 py-3">Pelanggan</th>
                                <th class="px-5 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($sales as $sale)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 text-gray-600">{{ $sale->created_at->format('d M Y H:i') }}</td>
                                <td class="px-5 py-3 font-medium text-gray-900">{{ $sale->invoice_number }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $sale->cashier->name ?? '-' }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $sale->customer->name ?? '-' }}</td>
                                <td class="px-5 py-3 text-right font-medium text-gray-900">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-gray-500">Tidak ada data penjualan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        {{-- 3. STOCK TAB --}}
        <div x-show="activeTab === 'stock'" class="space-y-6" style="display: none;">
            {{-- Product Stock --}}
            <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Stok Produk</h3>
                </div>
                <div class="overflow-x-auto max-h-96">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-600 font-medium border-b border-gray-100 sticky top-0">
                            <tr>
                                <th class="px-5 py-3">Produk</th>
                                <th class="px-5 py-3">Kategori</th>
                                <th class="px-5 py-3 text-right">Stok Saat Ini</th>
                                <th class="px-5 py-3 text-right">Min. Stok</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($productStocks as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 font-medium text-gray-900">{{ $product->name }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $product->category->name ?? '-' }}</td>
                                <td class="px-5 py-3 text-right font-bold {{ $product->stocks->sum('quantity') <= $product->min_stock ? 'text-red-600' : 'text-green-600' }}">
                                    {{ number_format($product->stocks->sum('quantity')) }} {{ $product->unit->name ?? '' }}
                                </td>
                                <td class="px-5 py-3 text-right text-gray-500">{{ number_format($product->min_stock) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- Ingredient Stock --}}
            <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Stok Bahan Baku</h3>
                </div>
                <div class="overflow-x-auto max-h-96">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-600 font-medium border-b border-gray-100 sticky top-0">
                            <tr>
                                <th class="px-5 py-3">Bahan</th>
                                <th class="px-5 py-3">Kategori</th>
                                <th class="px-5 py-3 text-right">Stok Saat Ini</th>
                                <th class="px-5 py-3 text-right">Min. Stok</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($ingredientStocks as $ingredient)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 font-medium text-gray-900">{{ $ingredient->name }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $ingredient->category->name ?? '-' }}</td>
                                <td class="px-5 py-3 text-right font-bold {{ $ingredient->stocks->sum('quantity') <= $ingredient->min_stock ? 'text-red-600' : 'text-green-600' }}">
                                    {{ number_format($ingredient->stocks->sum('quantity')) }} {{ $ingredient->unit->name ?? '' }}
                                </td>
                                <td class="px-5 py-3 text-right text-gray-500">{{ number_format($ingredient->min_stock) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- Stock Movements --}}
            <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Riwayat Perubahan Stok (Periode Ini)</h3>
                </div>
                <div class="overflow-x-auto max-h-96">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-600 font-medium border-b border-gray-100 sticky top-0">
                            <tr>
                                <th class="px-5 py-3">Waktu</th>
                                <th class="px-5 py-3">Item</th>
                                <th class="px-5 py-3">Tipe</th>
                                <th class="px-5 py-3 text-right">Jumlah</th>
                                <th class="px-5 py-3">Oleh</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($stockMovements as $movement)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 text-gray-600">{{ $movement->created_at->format('d M H:i') }}</td>
                                <td class="px-5 py-3 font-medium text-gray-900">
                                    {{ $movement->stockable->name ?? 'Unknown' }}
                                    <span class="text-xs text-gray-500 block">{{ class_basename($movement->stockable_type) }}</span>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ in_array($movement->type, ['in', 'production', 'return']) ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ ucfirst($movement->type) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right font-bold {{ in_array($movement->type, ['in', 'production', 'return']) ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $movement->quantity > 0 ? '+' : '' }}{{ number_format($movement->quantity) }}
                                </td>
                                <td class="px-5 py-3 text-gray-600">{{ $movement->createdBy->name ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-gray-500">Tidak ada pergerakan stok</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        {{-- 4. CASHIER TAB --}}
        <div x-show="activeTab === 'cashier'" class="space-y-6" style="display: none;">
            <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Performa Kasir</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-600 font-medium border-b border-gray-100">
                            <tr>
                                <th class="px-5 py-3">Nama Kasir</th>
                                <th class="px-5 py-3 text-center">Total Transaksi</th>
                                <th class="px-5 py-3 text-right">Total Pendapatan</th>
                                <th class="px-5 py-3 text-right">Rata-rata per Transaksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($cashierPerformance as $perf)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 font-medium text-gray-900 flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs">
                                        {{ substr($perf->cashier->name ?? 'U', 0, 1) }}
                                    </div>
                                    {{ $perf->cashier->name ?? 'Unknown' }}
                                </td>
                                <td class="px-5 py-3 text-center font-bold text-gray-900">{{ number_format($perf->total_transactions) }}</td>
                                <td class="px-5 py-3 text-right font-bold text-green-600">Rp {{ number_format($perf->total_revenue, 0, ',', '.') }}</td>
                                <td class="px-5 py-3 text-right text-gray-600">
                                    Rp {{ $perf->total_transactions > 0 ? number_format($perf->total_revenue / $perf->total_transactions, 0, ',', '.') : 0 }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-gray-500">Belum ada data performa kasir</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        {{-- 5. HOURLY TAB --}}
        <div x-show="activeTab === 'hourly'" class="space-y-6" style="display: none;">
            <section class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                <h3 class="font-semibold text-gray-900 mb-4">Analisis Penjualan per Jam (Peak Hours)</h3>
                <div class="relative h-80 w-full">
                    <canvas id="hourlyChartCanvas"></canvas>
                </div>
            </section>
        </div>

    </div>
</main>

{{-- MODAL EXPORT EXCEL --}}
<div x-data="{ open: false }" 
     @open-modal.window="if ($event.detail === 'export-excel-modal') open = true"
     x-show="open" 
     @click.away="open = false"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="open = false"></div>
        
        <div class="relative inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-green-100">
                        <i class="fas fa-file-excel text-green-600 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Export ke Excel</h3>
                        <p class="text-sm text-gray-500">Pilih sheet yang ingin di-export</p>
                    </div>
                </div>
                <button @click="open = false" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="{{ route('reports.export-excel') }}" method="GET" id="excelExportForm" target="_blank">
                <input type="hidden" name="period" value="{{ $period }}">
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">

                {{-- Date Confirmation --}}
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-blue-900">Periode Export</p>
                            <p class="text-sm text-blue-700 mt-1">
                                Anda akan export data dari tanggal 
                                <span class="font-bold">{{ $start->format('d F Y') }}</span> 
                                hingga 
                                <span class="font-bold">{{ $end->format('d F Y') }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Sheet Selection --}}
                <div class="space-y-3 mb-6">
                    <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors">
                        <input type="checkbox" name="sheets[]" value="summary" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-3 text-sm font-medium text-gray-900">Ringkasan Keuangan</span>
                    </label>
                    
                    <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors">
                        <input type="checkbox" name="sheets[]" value="sales" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-3 text-sm font-medium text-gray-900">Rincian Penjualan</span>
                    </label>
                    
                    <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors">
                        <input type="checkbox" name="sheets[]" value="expenses" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-3 text-sm font-medium text-gray-900">Rincian Pengeluaran</span>
                    </label>
                    
                    <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors">
                        <input type="checkbox" name="sheets[]" value="stock" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-3 text-sm font-medium text-gray-900">Laporan Stok</span>
                    </label>
                    
                    <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors">
                        <input type="checkbox" name="sheets[]" value="cashier" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-3 text-sm font-medium text-gray-900">Performa Kasir</span>
                    </label>
                    
                    <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors">
                        <input type="checkbox" name="sheets[]" value="hourly" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-3 text-sm font-medium text-gray-900">Analisis Waktu</span>
                    </label>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="open = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                        <i class="fas fa-download mr-2"></i>Export Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EXPORT PDF --}}
<div x-data="{ open: false }" 
     @open-modal.window="if ($event.detail === 'export-pdf-modal') open = true"
     x-show="open" 
     @click.away="open = false"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="open = false"></div>
        
        <div class="relative inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-red-100">
                        <i class="fas fa-file-pdf text-red-600 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Export ke PDF</h3>
                        <p class="text-sm text-gray-500">Pilih bagian yang ingin di-export</p>
                    </div>
                </div>
                <button @click="open = false" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="{{ route('reports.export-pdf') }}" method="GET" id="pdfExportForm" target="_blank">
                <input type="hidden" name="period" value="{{ $period }}">
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">

                {{-- Date Confirmation --}}
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-blue-900">Periode Export</p>
                            <p class="text-sm text-blue-700 mt-1">
                                Anda akan export data dari tanggal 
                                <span class="font-bold">{{ $start->format('d F Y') }}</span> 
                                hingga 
                                <span class="font-bold">{{ $end->format('d F Y') }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Section Selection --}}
                <div class="space-y-3 mb-6">
                    <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors">
                        <input type="checkbox" name="sections[]" value="summary" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-3 text-sm font-medium text-gray-900">Ringkasan Keuangan</span>
                    </label>
                    
                    <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors">
                        <input type="checkbox" name="sections[]" value="charts" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-3 text-sm font-medium text-gray-900">Grafik & Visualisasi</span>
                    </label>
                    
                    <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors">
                        <input type="checkbox" name="sections[]" value="sales" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-3 text-sm font-medium text-gray-900">Rincian Penjualan</span>
                    </label>
                    
                    <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors">
                        <input type="checkbox" name="sections[]" value="expenses" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-3 text-sm font-medium text-gray-900">Rincian Pengeluaran</span>
                    </label>
                    
                    <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors">
                        <input type="checkbox" name="sections[]" value="stock" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-3 text-sm font-medium text-gray-900">Laporan Stok</span>
                    </label>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="open = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                        <i class="fas fa-download mr-2"></i>Export PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hourly Chart Data
    const hourlyData = @json($hourlySales);
    
    // Prepare data for Chart.js
    const hours = Array.from({length: 24}, (_, i) => i); // 0-23
    const revenues = new Array(24).fill(0);
    const transactions = new Array(24).fill(0);

    hourlyData.forEach(item => {
        revenues[item.hour] = item.revenue;
        transactions[item.hour] = item.transactions;
    });

    const ctx = document.getElementById('hourlyChartCanvas').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: hours.map(h => `${h}:00`),
            datasets: [
                {
                    label: 'Pendapatan (Rp)',
                    data: revenues,
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1,
                    yAxisID: 'y',
                },
                {
                    label: 'Transaksi',
                    data: transactions,
                    type: 'line',
                    borderColor: 'rgb(239, 68, 68)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgb(239, 68, 68)',
                    yAxisID: 'y1',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: { display: true, text: 'Pendapatan' }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false,
                    },
                    title: { display: true, text: 'Jumlah Transaksi' }
                },
            }
        }
    });
});
</script>
@endpush
@endsection
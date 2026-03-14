@extends('layouts.app')

@section('title', 'Laporan Bisnis - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Laporan Bisnis</span>
</li>
@endsection

@section('content')
<style>
    [x-cloak] { display: none !important; }
</style>
<main class="flex-grow py-8 px-4 bg-gray-50" x-data="reportApp()" x-init="init()">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Laporan Bisnis
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Analisis menyeluruh: Penjualan, Stok, Bahan, dan Performa Karyawan
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @can('ekspor laporan excel')
                <button @click="openExportModal('excel')" 
                        class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-5 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                    <i class="fas fa-file-excel"></i>
                    <span>Export Excel</span>
                </button>
                @endcan
                @can('ekspor laporan pdf')
                <button @click="openExportModal('pdf')" 
                        class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-3 text-sm font-black text-white hover:bg-red-700 transition-all shadow-lg shadow-red-600/20 active:scale-95">
                    <i class="fas fa-file-pdf"></i>
                    <span>Export PDF</span>
                </button>
                @endcan
            </div>
        </section>

        {{-- FILTER PERIODE --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5">

            {{-- EXPORT MODAL --}}
            <div x-show="showExportModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showExportModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showExportModal = false"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="showExportModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full" :class="exportType === 'excel' ? 'bg-green-100' : 'bg-red-100'">
                                    <i class="fas" :class="exportType === 'excel' ? 'fa-file-excel text-green-600' : 'fa-file-pdf text-red-600'"></i>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        Export <span x-text="exportType === 'excel' ? 'Excel' : 'PDF'"></span>
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500 mb-4">
                                            Pilih data yang ingin Anda sertakan dalam laporan ini.
                                        </p>
                                        <div class="space-y-2">
                                            <template x-for="option in exportOptions" :key="option.key">
                                                <label class="flex items-center space-x-3 p-2 rounded hover:bg-gray-50 cursor-pointer border border-transparent hover:border-gray-200">
                                                    <input type="checkbox" :value="option.key" x-model="selectedExportOptions" class="h-4 w-4 text-cuan-green focus:ring-cuan-green/20 border-gray-300 rounded">
                                                    <span class="text-gray-700 text-sm font-medium" x-text="option.label"></span>
                                                </label>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                       <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" @click="processExport()" class="no-loader w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm" :class="exportType === 'excel' ? 'bg-green-600 hover:bg-green-700 focus:ring-green-500' : 'bg-red-600 hover:bg-red-700 focus:ring-red-500'">
                                <span x-show="!exportLoading">Download</span>
                                <span x-show="exportLoading">Memproses...</span>
                            </button>
                            <button type="button" @click="showExportModal = false" class="no-loader mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-4">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="flex-1 overflow-x-auto pb-2 sm:pb-0 hide-scrollbar">
                        <div class="flex items-center gap-2">
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
                </div>

                <div x-show="period === 'custom'" x-cloak 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="mt-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <div class="flex flex-col sm:flex-row items-end sm:items-center gap-4">
                        <div class="w-full sm:w-auto">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Dari Tanggal</label>
                            <input type="date" x-model="startDate" class="w-full text-sm border-gray-300 rounded-lg focus:ring-cuan-green/20 focus:border-blue-500 shadow-sm">
                        </div>
                        <div class="w-full sm:w-auto">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Sampai Tanggal</label>
                            <input type="date" x-model="endDate" class="w-full text-sm border-gray-300 rounded-lg focus:ring-cuan-green/20 focus:border-blue-500 shadow-sm">
                        </div>
                        <button @click="loadData()" class="w-full sm:w-auto px-6 py-3 bg-cuan-green text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-cuan-dark shadow-sm transition-all active:scale-95">
                            Terapkan Filter
                        </button>
                    </div>
                </div>
            </div>
        </section>

        {{-- LOADING STATE --}}
        <div x-show="loading" x-cloak class="flex items-center justify-center py-20">
            <div class="text-center">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-gray-200 border-t-cuan-green"></div>
                <p class="mt-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Sedang memuat data...</p>
            </div>
        </div>

        {{-- CONTENT --}}
        <div x-show="!loading" x-cloak>
            {{-- TABS NAVIGATION --}}
            <div class="mb-6 overflow-x-auto hide-scrollbar pb-1">
                <nav class="flex space-x-2" aria-label="Tabs">
                    <template x-for="tab in tabs" :key="tab.key">
                        <button @click="activeTab = tab.key" 
                            :class="activeTab === tab.key ? 'bg-white text-cuan-green shadow-sm ring-1 ring-gray-200' : 'text-gray-500 hover:text-gray-700 hover:bg-white/50'" 
                            class="whitespace-nowrap py-3 px-6 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all active:scale-95"
                            x-text="tab.label">
                        </button>
                    </template>
                </nav>
            </div>

            {{-- TAB CONTENTS --}}
            <div>
                {{-- 1. SUMMARY TAB --}}
                <div x-show="activeTab === 'summary'" class="space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    {{-- Summary Cards --}}
                    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Pendapatan</p>
                            <p class="mt-2 text-2xl font-black text-cuan-green" x-text="formatRupiah(data.totalRevenue)"></p>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Pengeluaran</p>
                            <p class="mt-2 text-2xl font-black text-red-600" x-text="formatRupiah(data.totalExpenses)"></p>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Laba Kotor</p>
                            <p class="mt-2 text-2xl font-black text-cuan-green" x-text="formatRupiah(data.grossProfit)"></p>
                            <p class="text-[9px] text-gray-400 mt-1 uppercase font-bold tracking-tighter">Pendapatan - HPP</p>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Laba Bersih</p>
                            <p class="mt-2 text-2xl font-black" 
                               :class="data.netProfit >= 0 ? 'text-cuan-green' : 'text-red-600'"
                               x-text="formatRupiah(data.netProfit)"></p>
                            <p class="text-[9px] text-gray-400 mt-1 uppercase font-bold tracking-tighter">Laba Kotor - Pengeluaran</p>
                        </div>
                    </section>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Top Products --}}
                        <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                                <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">Produk Terlaris</h3>
                                <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 bg-white border border-gray-100 px-2.5 py-1 rounded-lg">Top 5</span>
                            </div>
                            <div class="p-5 space-y-4">
                                <template x-if="data.topProducts && data.topProducts.length > 0">
                                    <template x-for="(product, index) in data.topProducts" :key="index">
                                        <div class="flex items-center justify-between group">
                                            <div class="flex items-center gap-3">
                                                <span class="w-6 h-6 flex items-center justify-center rounded-full bg-yellow-50 text-yellow-700 border border-yellow-100 text-xs font-bold" x-text="index + 1"></span>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900 group-hover:text-cuan-green transition-colors" x-text="product.product_name"></p>
                                                    <p class="text-xs text-gray-500" x-text="formatNumber(product.total_qty) + ' terjual'"></p>
                                                </div>
                                            </div>
                                            <span class="text-sm font-semibold text-gray-900" x-text="formatRupiah(product.total_revenue)"></span>
                                        </div>
                                    </template>
                                </template>
                                <template x-if="!data.topProducts || data.topProducts.length === 0">
                                    <div class="text-center py-8">
                                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-3">
                                            <i class="fas fa-box-open text-gray-400"></i>
                                        </div>
                                        <p class="text-sm text-gray-500">Belum ada data penjualan produk</p>
                                    </div>
                                </template>
                            </div>
                        </section>

                        {{-- Payment Methods --}}
                        <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                                <h3 class="font-semibold text-gray-900">Metode Pembayaran</h3>
                            </div>
                            <div class="p-5 space-y-3">
                                <template x-for="method in data.paymentMethods" :key="method.payment_method">
                                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 border border-gray-100 hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center border border-gray-200 shadow-sm">
                                                <i class="fas fa-wallet text-gray-400 text-xs"></i>
                                            </div>
                                            <span class="text-sm font-medium text-gray-700" x-text="method.payment_method.toUpperCase()"></span>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-bold text-gray-900" x-text="method.total + ' Tx'"></p>
                                            <p class="text-xs text-gray-500" x-text="formatRupiah(method.total_amount)"></p>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!data.paymentMethods || data.paymentMethods.length === 0">
                                    <div class="text-center py-8">
                                        <p class="text-sm text-gray-500">Belum ada data pembayaran</p>
                                    </div>
                                </template>
                            </div>
                        </section>
                    </div>
                </div>

                {{-- 2. SALES TAB --}}
                <div x-show="activeTab === 'sales'" class="space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                            <h3 class="font-semibold text-gray-900">Riwayat Penjualan</h3>
                            <span class="text-xs font-medium text-gray-500 bg-white border border-gray-200 px-2.5 py-1 rounded-full shadow-sm" x-text="(data.sales ? data.sales.length : 0) + ' Transaksi'"></span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-black uppercase tracking-widest border-b border-gray-100">
                                    <tr>
                                        <th class="px-6 py-4 text-left">Tanggal</th>
                                        <th class="px-6 py-4 text-left">Invoice</th>
                                        <th class="px-6 py-4 text-left">Kasir</th>
                                        <th class="px-6 py-4 text-left">Pelanggan</th>
                                        <th class="px-6 py-4 text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-if="data.sales && data.sales.length > 0">
                                        <template x-for="sale in data.sales" :key="sale.id">
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-3 text-gray-600 whitespace-nowrap" x-text="formatDate(sale.created_at)"></td>
                                                <td class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap" x-text="sale.invoice_number"></td>
                                                <td class="px-6 py-3 text-gray-600 whitespace-nowrap" x-text="sale.cashier ? sale.cashier.name : '-'"></td>
                                                <td class="px-6 py-3 text-gray-600 whitespace-nowrap" x-text="sale.customer ? sale.customer.name : '-'"></td>
                                                <td class="px-6 py-3 text-right font-medium text-gray-900 whitespace-nowrap" x-text="formatRupiah(sale.grand_total)"></td>
                                            </tr>
                                        </template>
                                    </template>
                                    <template x-if="!data.sales || data.sales.length === 0">
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                                <div class="flex flex-col items-center justify-center">
                                                    <i class="fas fa-inbox text-3xl mb-2 text-gray-300"></i>
                                                    <p>Tidak ada data penjualan</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>

                {{-- 3. FINANCE TAB --}}
                <div x-show="activeTab === 'finance'" class="space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    {{-- Financial Summary Cards --}}
                    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Pajak (PPN)</p>
                            <p class="mt-2 text-2xl font-black text-cuan-green" x-text="formatRupiah(data.totalTax)"></p>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Diskon</p>
                            <p class="mt-2 text-2xl font-black text-orange-600" x-text="formatRupiah(data.totalDiscount)"></p>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total HPP</p>
                            <p class="mt-2 text-2xl font-black text-gray-700" x-text="formatRupiah(data.totalCogs)"></p>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Margin Laba</p>
                            <p class="mt-2 text-2xl font-black" 
                               :class="data.totalRevenue > 0 && (data.grossProfit / data.totalRevenue * 100) >= 20 ? 'text-cuan-green' : 'text-yellow-600'"
                               x-text="data.totalRevenue > 0 ? ((data.grossProfit / data.totalRevenue) * 100).toFixed(1) + '%' : '0%'"></p>
                        </div>
                    </section>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Expenses by Category --}}
                        <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                                <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">Pengeluaran per Kategori</h3>
                            </div>
                            <div class="p-5 space-y-3">
                                <template x-for="expense in data.expensesByCategory" :key="expense.category">
                                    <div class="flex items-center justify-between p-3 rounded-lg bg-red-50 border border-red-100">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center border border-red-100">
                                                <i class="fas fa-receipt text-red-400 text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900" x-text="expense.category"></p>
                                                <p class="text-xs text-gray-500" x-text="expense.count + ' item'"></p>
                                            </div>
                                        </div>
                                        <span class="text-sm font-bold text-red-600" x-text="formatRupiah(expense.total)"></span>
                                    </div>
                                </template>
                                <template x-if="!data.expensesByCategory || data.expensesByCategory.length === 0">
                                    <div class="text-center py-8">
                                        <p class="text-sm text-gray-500">Belum ada data pengeluaran</p>
                                    </div>
                                </template>
                            </div>
                        </section>

                        {{-- Sales by Category --}}
                        <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                                <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">Penjualan per Kategori</h3>
                            </div>
                            <div class="p-5 space-y-3">
                                <template x-for="cat in data.salesByCategory" :key="cat.category_name">
                                    <div class="flex items-center justify-between p-3 rounded-lg bg-green-50 border border-green-100">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center border border-green-100">
                                                <i class="fas fa-tag text-green-400 text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900" x-text="cat.category_name"></p>
                                                <p class="text-xs text-gray-500" x-text="formatNumber(cat.total_qty) + ' unit'"></p>
                                            </div>
                                        </div>
                                        <span class="text-sm font-bold text-green-600" x-text="formatRupiah(cat.total_revenue)"></span>
                                    </div>
                                </template>
                                <template x-if="!data.salesByCategory || data.salesByCategory.length === 0">
                                    <div class="text-center py-8">
                                        <p class="text-sm text-gray-500">Belum ada data penjualan</p>
                                    </div>
                                </template>
                            </div>
                        </section>
                    </div>

                    {{-- Refund & Cancel Stats --}}
                    <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">Refund & Pembatalan</h3>
                        </div>
                        <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="text-center p-6 bg-red-50/30 rounded-xl border border-red-100">
                                <p class="text-2xl font-black text-red-600" x-text="data.refundStats.refund_count || 0"></p>
                                <p class="text-[9px] text-gray-400 mt-1 font-black uppercase tracking-widest">Transaksi Refund</p>
                            </div>
                            <div class="text-center p-6 bg-red-50/30 rounded-xl border border-red-100">
                                <p class="text-lg font-black text-red-600" x-text="formatRupiah(data.refundStats.refund_amount || 0)"></p>
                                <p class="text-[9px] text-gray-400 mt-1 font-black uppercase tracking-widest">Nilai Refund</p>
                            </div>
                            <div class="text-center p-6 bg-yellow-50/30 rounded-xl border border-yellow-100">
                                <p class="text-2xl font-black text-yellow-600" x-text="data.refundStats.cancel_count || 0"></p>
                                <p class="text-[9px] text-gray-400 mt-1 font-black uppercase tracking-widest">Transaksi Dibatalkan</p>
                            </div>
                        </div>
                    </section>

                    {{-- Purchases Summary --}}
                    <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">Ringkasan Pembelian Supplier</h3>
                        </div>
                        <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="text-center p-6 bg-cuan-green/10 text-cuan-green border-cuan-green/20">
                                <p class="text-lg font-black text-cuan-green" x-text="formatRupiah(data.totalPurchases)"></p>
                                <p class="text-[9px] text-gray-400 mt-1 font-black uppercase tracking-widest">Total Pembelian</p>
                            </div>
                            <div class="text-center p-6 bg-cuan-green/10 rounded-xl border border-cuan-green/20">
                                <p class="text-lg font-black text-cuan-green" x-text="formatRupiah(data.totalPurchasesPaid)"></p>
                                <p class="text-[9px] text-gray-400 mt-1 font-black uppercase tracking-widest">Sudah Dibayar</p>
                            </div>
                            <div class="text-center p-6 bg-red-50/30 rounded-xl border border-red-100">
                                <p class="text-lg font-black text-red-600" x-text="formatRupiah(data.totalPurchasesUnpaid)"></p>
                                <p class="text-[9px] text-gray-400 mt-1 font-black uppercase tracking-widest">Belum Lunas</p>
                            </div>
                        </div>
                    </section>
                </div>

                {{-- 4. CUSTOMER TAB --}}
                <div x-show="activeTab === 'customer'" class="space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    {{-- Customer Summary Cards --}}
                    <section class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Piutang</p>
                            <p class="mt-2 text-2xl font-black text-red-600" x-text="formatRupiah(data.totalPiutang)"></p>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Pelanggan Hutang</p>
                            <p class="mt-2 text-2xl font-black text-orange-600" x-text="data.customerDebts ? data.customerDebts.length : 0"></p>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Top Pelanggan</p>
                            <p class="mt-2 text-2xl font-black text-cuan-green" x-text="data.topCustomers ? data.topCustomers.length : 0"></p>
                        </div>
                    </section>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Top Customers --}}
                        <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                                <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">Pelanggan Terloyal</h3>
                            </div>
                            <div class="p-5 space-y-3">
                                <template x-if="data.topCustomers && data.topCustomers.length > 0">
                                    <template x-for="(cust, index) in data.topCustomers" :key="cust.customer_id">
                                        <div class="flex items-center justify-between p-3 rounded-lg bg-blue-50 border border-blue-100">
                                            <div class="flex items-center gap-3">
                                                <span class="w-6 h-6 flex items-center justify-center rounded-full bg-blue-600 text-white text-xs font-bold" x-text="index + 1"></span>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900" x-text="cust.customer ? cust.customer.name : 'Pelanggan'"></p>
                                                    <p class="text-xs text-gray-500" x-text="cust.total_transactions + ' transaksi'"></p>
                                                </div>
                                            </div>
                                            <span class="text-sm font-bold text-cuan-green" x-text="formatRupiah(cust.total_spent)"></span>
                                        </div>
                                    </template>
                                </template>
                                <template x-if="!data.topCustomers || data.topCustomers.length === 0">
                                    <div class="text-center py-8">
                                        <p class="text-sm text-gray-500">Belum ada data pelanggan</p>
                                    </div>
                                </template>
                            </div>
                        </section>

                        {{-- Customer Debts --}}
                        <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                                <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">Piutang Pelanggan</h3>
                            </div>
                            <div class="p-5 space-y-3 max-h-80 overflow-y-auto custom-scrollbar">
                                <template x-if="data.customerDebts && data.customerDebts.length > 0">
                                    <template x-for="debt in data.customerDebts" :key="debt.id">
                                        <div class="flex items-center justify-between p-3 rounded-lg bg-red-50 border border-red-100">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center border border-red-100">
                                                    <i class="fas fa-user-clock text-red-400 text-xs"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900" x-text="debt.customer ? debt.customer.name : 'Pelanggan'"></p>
                                                    <p class="text-xs text-gray-500" x-text="'Invoice: ' + (debt.invoice_number || '-')"></p>
                                                </div>
                                            </div>
                                            <span class="text-sm font-bold text-red-600" x-text="formatRupiah(debt.amount)"></span>
                                        </div>
                                    </template>
                                </template>
                                <template x-if="!data.customerDebts || data.customerDebts.length === 0">
                                    <div class="text-center py-8">
                                        <p class="text-sm text-gray-500">Tidak ada piutang</p>
                                    </div>
                                </template>
                            </div>
                        </section>
                    </div>
                </div>

                {{-- 5. STOCK TAB --}}
                <div x-show="activeTab === 'stock'" class="space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    {{-- Stock Value Summary --}}
                    <section class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <section class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Nilai Stok Produk</p>
                            <p class="mt-2 text-2xl font-black text-cuan-green" x-text="formatRupiah(data.productStockValue)"></p>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Nilai Stok Bahan Baku</p>
                            <p class="mt-2 text-2xl font-black text-cuan-green" x-text="formatRupiah(data.ingredientStockValue)"></p>
                        </div>
                    </section>

                    {{-- Product Stock --}}
                    <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">Stok Produk</h3>
                        </div>
                        <div class="overflow-x-auto max-h-96 custom-scrollbar">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-black uppercase tracking-widest border-b border-gray-100 sticky top-0">
                                    <tr>
                                        <th class="px-6 py-4 text-left">Produk</th>
                                        <th class="px-6 py-4 text-left">Kategori</th>
                                        <th class="px-6 py-4 text-right">Stok Saat Ini</th>
                                        <th class="px-6 py-4 text-right">Min. Stok</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="product in data.productStocks" :key="product.id">
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap" x-text="product.name"></td>
                                            <td class="px-6 py-3 text-gray-600 whitespace-nowrap" x-text="product.category ? product.category.name : '-'"></td>
                                            <td class="px-6 py-3 text-right font-bold whitespace-nowrap" 
                                                :class="product.current_stock <= product.min_stock ? 'text-red-600' : 'text-green-600'"
                                                x-text="formatNumber(product.current_stock) + ' ' + (product.unit ? product.unit.name : '')"></td>
                                            <td class="px-6 py-3 text-right text-gray-500 whitespace-nowrap" x-text="formatNumber(product.min_stock)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    {{-- Ingredient Stock --}}
                    <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">Stok Bahan Baku</h3>
                        </div>
                        <div class="overflow-x-auto max-h-96 custom-scrollbar">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-black uppercase tracking-widest border-b border-gray-100 sticky top-0">
                                    <tr>
                                        <th class="px-6 py-4 text-left">Bahan</th>
                                        <th class="px-6 py-4 text-left">Kategori</th>
                                        <th class="px-6 py-4 text-right">Stok Saat Ini</th>
                                        <th class="px-6 py-4 text-right">Min. Stok</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="ingredient in data.ingredientStocks" :key="ingredient.id">
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap" x-text="ingredient.name"></td>
                                            <td class="px-6 py-3 text-gray-600 whitespace-nowrap" x-text="ingredient.category ? ingredient.category.name : '-'"></td>
                                            <td class="px-6 py-3 text-right font-bold whitespace-nowrap" 
                                                :class="ingredient.current_stock <= ingredient.min_stock ? 'text-red-600' : 'text-green-600'"
                                                x-text="formatNumber(ingredient.current_stock) + ' ' + (ingredient.unit ? ingredient.unit.name : '')"></td>
                                            <td class="px-6 py-3 text-right text-gray-500 whitespace-nowrap" x-text="formatNumber(ingredient.min_stock)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    {{-- Stock Movements --}}
                    <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">Riwayat Perubahan Stok (Periode Ini)</h3>
                        </div>
                        <div class="overflow-x-auto max-h-96 custom-scrollbar">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-black uppercase tracking-widest border-b border-gray-100 sticky top-0">
                                    <tr>
                                        <th class="px-6 py-4 text-left">Waktu</th>
                                        <th class="px-6 py-4 text-left">Item</th>
                                        <th class="px-6 py-4 text-left">Tipe</th>
                                        <th class="px-6 py-4 text-right">Jumlah</th>
                                        <th class="px-6 py-4 text-left">Oleh</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-if="data.stockMovements && data.stockMovements.length > 0">
                                        <template x-for="movement in data.stockMovements" :key="movement.id">
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-3 text-gray-600 whitespace-nowrap" x-text="formatDateTime(movement.created_at)"></td>
                                                <td class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap">
                                                    <span x-text="movement.stockable ? movement.stockable.name : 'Unknown'"></span>
                                                    <span class="text-xs text-gray-500 block" x-text="movement.stockable_type"></span>
                                                </td>
                                                <td class="px-6 py-3 whitespace-nowrap">
                                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium"
                                                          :class="['in', 'production', 'return'].includes(movement.type) ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                                                          x-text="movement.type.toUpperCase()"></span>
                                                </td>
                                                <td class="px-6 py-3 text-right font-bold whitespace-nowrap"
                                                    :class="['in', 'production', 'return'].includes(movement.type) ? 'text-green-600' : 'text-red-600'"
                                                    x-text="(movement.quantity > 0 ? '+' : '') + formatNumber(movement.quantity)"></td>
                                                <td class="px-6 py-3 text-gray-600 whitespace-nowrap" x-text="movement.created_by ? movement.created_by.name : '-'"></td>
                                            </tr>
                                        </template>
                                    </template>
                                    <template x-if="!data.stockMovements || data.stockMovements.length === 0">
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                                <div class="flex flex-col items-center justify-center">
                                                    <i class="fas fa-history text-3xl mb-2 text-gray-300"></i>
                                                    <p>Tidak ada pergerakan stok</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>

                {{-- 4. CASHIER TAB --}}
                <div x-show="activeTab === 'cashier'" class="space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">Performa Kasir</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-black uppercase tracking-widest border-b border-gray-100">
                                    <tr>
                                        <th class="px-6 py-4 text-left">Nama Kasir</th>
                                        <th class="px-6 py-4 text-center">Total Transaksi</th>
                                        <th class="px-6 py-4 text-right">Total Pendapatan</th>
                                        <th class="px-6 py-4 text-right">Rata-rata per Transaksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-if="data.cashierPerformance && data.cashierPerformance.length > 0">
                                        <template x-for="perf in data.cashierPerformance" :key="perf.cashier_id">
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-3 font-medium text-gray-900 flex items-center gap-3 whitespace-nowrap">
                                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-cuan-green font-bold text-xs"
                                                         x-text="perf.cashier ? perf.cashier.name.charAt(0) : 'U'"></div>
                                                    <span x-text="perf.cashier ? perf.cashier.name : 'Unknown'"></span>
                                                </td>
                                                <td class="px-6 py-3 text-center font-bold text-gray-900 whitespace-nowrap" x-text="formatNumber(perf.total_transactions)"></td>
                                                <td class="px-6 py-3 text-right font-bold text-green-600 whitespace-nowrap" x-text="formatRupiah(perf.total_revenue)"></td>
                                                <td class="px-6 py-3 text-right text-gray-600 whitespace-nowrap" x-text="formatRupiah(perf.total_transactions > 0 ? perf.total_revenue / perf.total_transactions : 0)"></td>
                                            </tr>
                                        </template>
                                    </template>
                                    <template x-if="!data.cashierPerformance || data.cashierPerformance.length === 0">
                                        <tr>
                                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                                <div class="flex flex-col items-center justify-center">
                                                    <i class="fas fa-users-slash text-3xl mb-2 text-gray-300"></i>
                                                    <p>Belum ada data performa kasir</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>

                {{-- 5. HOURLY TAB --}}
                <div x-show="activeTab === 'hourly'" class="space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    <section class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900 mb-6 font-bold">Analisis Penjualan per Jam (Peak Hours)</h3>
                        <div class="relative h-80 w-full">
                            <canvas id="hourlyChartCanvas"></canvas>
                        </div>
                    </section>
                </div>

            </div>
        </div>
    </div>
</main>

@push('styles')
<style>
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function reportApp() {
    return {
        loading: false,
        activeTab: 'summary',
        period: '{{ $period ?? "today" }}',
        startDate: '{{ request("start_date") }}',
        endDate: '{{ request("end_date") }}',
        
        // Export Modal State
        showExportModal: false,
        exportType: 'excel', // 'excel' or 'pdf'
        exportLoading: false,
        selectedExportOptions: [],
        exportOptions: [
            {key: 'summary', label: 'Ringkasan Eksekutif'},
            {key: 'sales', label: 'Rincian Penjualan'},
            {key: 'finance', label: 'Detail Keuangan'},
            {key: 'customer', label: 'Laporan Pelanggan'},
            {key: 'stock', label: 'Laporan Stok & Bahan'},
            {key: 'cashier', label: 'Performa Kasir'},
            {key: 'hourly', label: 'Analisis Waktu (Peak Hours)'},
            {key: 'expenses', label: 'Pengeluaran Operasional'},
            {key: 'charts', label: 'Grafik & Visualisasi (PDF Only)'}
        ],

        data: {
            totalRevenue: {{ $totalRevenue ?? 0 }},
            totalSubtotal: {{ $totalSubtotal ?? 0 }},
            totalExpenses: {{ $totalExpenses ?? 0 }},
            totalCogs: {{ $totalCogs ?? 0 }},
            grossProfit: {{ $grossProfit ?? 0 }},
            netProfit: {{ $netProfit ?? 0 }},
            totalTax: {{ $totalTax ?? 0 }},
            totalDiscount: {{ $totalDiscount ?? 0 }},
            totalTransactions: {{ $totalTransactions ?? 0 }},
            totalPiutang: {{ $totalPiutang ?? 0 }},
            totalPurchases: {{ $totalPurchases ?? 0 }},
            totalPurchasesPaid: {{ $totalPurchasesPaid ?? 0 }},
            totalPurchasesUnpaid: {{ $totalPurchasesUnpaid ?? 0 }},
            productStockValue: {{ $productStockValue ?? 0 }},
            ingredientStockValue: {{ $ingredientStockValue ?? 0 }},
            topProducts: @json($topProducts ?? []),
            salesByCategory: @json($salesByCategory ?? []),
            paymentMethods: @json($paymentMethods ?? []),
            sales: @json($sales ?? []),
            productStocks: @json($productStocks ?? []),
            ingredientStocks: @json($ingredientStocks ?? []),
            stockMovements: @json($stockMovements ?? []),
            cashierPerformance: @json($cashierPerformance ?? []),
            hourlySales: @json($hourlySales ?? []),
            refundStats: @json($refundStats ?? ['refund_count' => 0, 'refund_amount' => 0, 'cancel_count' => 0]),
            topCustomers: @json($topCustomers ?? []),
            customerDebts: @json($customerDebts ?? []),
            purchases: @json($purchases ?? []),
            expenses: @json($expenses ?? []),
            expensesByCategory: @json($expensesByCategory ?? [])
        },
        periods: {
            'today': 'Hari Ini',
            'yesterday': 'Kemarin',
            '7_days': '7 Hari Terakhir',
            '30_days': '30 Hari Terakhir',
            'this_month': 'Bulan Ini',
            'this_year': 'Tahun Ini',
            'custom': 'Custom Tanggal'
        },
        tabs: [
            {key: 'summary', label: 'Ringkasan'},
            {key: 'sales', label: 'Penjualan'},
            {key: 'finance', label: 'Keuangan'},
            {key: 'customer', label: 'Pelanggan & Piutang'},
            {key: 'stock', label: 'Stok & Bahan'},
            {key: 'cashier', label: 'Performa Kasir'},
            {key: 'hourly', label: 'Analisis Waktu'}
        ],
        chart: null,

        init() {
            // Check URL for active tab
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            if (tabParam && this.tabs.some(t => t.key === tabParam)) {
                this.activeTab = tabParam;
            }

            this.initChart();
            
            // Watch for tab changes to reinit chart and update URL
            this.$watch('activeTab', (value) => {
                this.updateUrl('tab', value);
                if (value === 'hourly') {
                    setTimeout(() => this.initChart(), 100);
                }
            });
        },

        setPeriod(key) {
            this.period = key;
            this.updateUrl('period', key);
            
            // Clear custom dates if not custom
            if (key !== 'custom') {
                this.updateUrl('start_date', null);
                this.updateUrl('end_date', null);
                this.loadData();
            }
        },

        updateUrl(key, value) {
            const url = new URL(window.location.href);
            if (value) {
                url.searchParams.set(key, value);
            } else {
                url.searchParams.delete(key);
            }
            window.history.pushState({}, '', url);
        },

        async loadData() {
            this.loading = true;
            try {
                // Update URL for custom dates if applicable
                if (this.period === 'custom') {
                    this.updateUrl('start_date', this.startDate);
                    this.updateUrl('end_date', this.endDate);
                }

                const params = new URLSearchParams({
                    period: this.period,
                    start_date: this.startDate,
                    end_date: this.endDate
                });

                const response = await fetch(`{{ route('reports.ajax-data') }}?${params}`);
                const result = await response.json();
                
                this.data = result;
                
                if (this.activeTab === 'hourly') {
                    setTimeout(() => this.initChart(), 100);
                }
            } catch (error) {
                console.error('Error loading data:', error);
                alert('Gagal memuat data');
            } finally {
                this.loading = false;
            }
        },

        initChart() {
            const canvas = document.getElementById('hourlyChartCanvas');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            
            if (this.chart) {
                this.chart.destroy();
            }

            const hours = Array.from({length: 24}, (_, i) => i);
            const revenues = new Array(24).fill(0);
            const transactions = new Array(24).fill(0);

            this.data.hourlySales.forEach(item => {
                revenues[item.hour] = item.revenue;
                transactions[item.hour] = item.transactions;
            });

            this.chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: hours.map(h => `${String(h).padStart(2, '0')}:00`),
                    datasets: [
                        {
                            label: 'Pendapatan (Rp)',
                            data: revenues,
                            backgroundColor: 'rgba(0, 182, 155, 0.1)',
                            borderColor: '#00b69b',
                            borderWidth: 2,
                            yAxisID: 'y',
                            borderRadius: 6,
                        },
                        {
                            label: 'Transaksi',
                            data: transactions,
                            type: 'line',
                            borderColor: '#111827',
                            borderWidth: 2,
                            pointBackgroundColor: '#111827',
                            pointRadius: 3,
                            yAxisID: 'y1',
                            tension: 0.3
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
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: { display: true, text: 'Pendapatan' },
                            grid: {
                                borderDash: [2, 4]
                            }
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
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        },

        formatRupiah(value) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value || 0);
        },

        formatNumber(value) {
            return new Intl.NumberFormat('id-ID').format(value || 0);
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'});
        },

        formatDateTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit'});
        },

        openExportModal(type) {
            this.exportType = type;
            this.showExportModal = true;
            // Default select all options except charts for excel
            this.selectedExportOptions = this.exportOptions
                .filter(opt => type === 'pdf' || opt.key !== 'charts')
                .map(opt => opt.key);
        },

async processExport() {
    if (this.selectedExportOptions.length === 0) {
        alert('Pilih setidaknya satu data untuk diexport.');
        return;
    }

    this.exportLoading = true;
    
    try {
        const params = new URLSearchParams({
            period: this.period,
            start_date: this.startDate,
            end_date: this.endDate
        });

        // Add selected options
        if (this.exportType === 'excel') {
            this.selectedExportOptions.forEach(opt => params.append('sheets[]', opt));
        } else {
            this.selectedExportOptions.forEach(opt => params.append('sections[]', opt));
        }

        const url = this.exportType === 'excel' 
            ? `{{ route('reports.export-excel') }}?${params.toString()}`
            : `{{ route('reports.export-pdf') }}?${params.toString()}`;

        // Use fetch to download file without triggering navigation
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error('Download failed');
        }

        // Get the blob
        const blob = await response.blob();
        
        // Get filename from Content-Disposition header or use default
        const contentDisposition = response.headers.get('Content-Disposition');
        let filename = this.exportType === 'excel' ? 'laporan.xlsx' : 'laporan.pdf';
        
        if (contentDisposition) {
            const filenameMatch = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
            if (filenameMatch && filenameMatch[1]) {
                filename = filenameMatch[1].replace(/['"]/g, '');
            }
        }

        // Create download link
        const blobUrl = window.URL.createObjectURL(blob);
        const downloadLink = document.createElement('a');
        downloadLink.href = blobUrl;
        downloadLink.download = filename;
        downloadLink.style.display = 'none';
        
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
        
        // Clean up blob URL
        window.URL.revokeObjectURL(blobUrl);
        
        // Close modal and reset loading
        this.exportLoading = false;
        this.showExportModal = false;
        
    } catch (error) {
        console.error('Export error:', error);
        alert('Gagal mengunduh file. Silakan coba lagi.');
        this.exportLoading = false;
    }
}
    }
}
</script>
@endpush
@endsection
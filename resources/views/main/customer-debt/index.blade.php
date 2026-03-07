@extends('layouts.app')

@section('title', 'Pelanggan & Piutang - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Pelanggan & Piutang</span>
</li>
@endsection

@push('styles')
<style>
    .tab-btn {
        transition: all 0.2s ease;
    }
    .tab-btn.active {
        background: linear-gradient(135deg, #14b8a6 0%, #06b6d4 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(20, 184, 166, 0.35);
    }
    .tab-btn:not(.active):hover {
        background-color: #f0fdfa;
        color: #0d9488;
    }
    .debt-row.overdue {
        background-color: #fef2f2;
    }
    .debt-row.overdue:hover {
        background-color: #fee2e2;
    }
    .payment-method-card {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .payment-method-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }
    .payment-method-card.selected {
        border-color: #14b8a6;
        background-color: #f0fdfa;
        box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.2);
    }
</style>
@endpush

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Alert / Notifikasi --}}
        @if(session('success'))
            <div class="rounded-lg border border-teal-200 bg-teal-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-check-circle mt-0.5 text-teal-500"></i>
                <p class="text-teal-800">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-exclamation-circle mt-0.5 text-red-500"></i>
                <p class="text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        {{-- HEADER HALAMAN --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-teal-50 text-teal-500 border border-teal-100">
                        <i class="fas fa-address-book text-sm"></i>
                    </span>
                    <span>Pelanggan & Piutang</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola data pelanggan dan pantau tunggakan/piutang yang masih harus dibayar.
                </p>
            </div>
        </section>

        {{-- RINGKASAN STATISTIK --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Pelanggan</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($stats['total_customers']) }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-teal-50 flex items-center justify-center border border-teal-100">
                        <i class="fas fa-users text-teal-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Pelanggan Aktif</p>
                        <p class="mt-1 text-2xl font-semibold text-amber-600">{{ number_format($stats['active_customers']) }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center border border-amber-100">
                        <i class="fas fa-user-check text-amber-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Piutang</p>
                        <p class="mt-1 text-2xl font-semibold text-red-600">Rp {{ number_format($stats['total_debt'], 0, ',', '.') }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center border border-red-100">
                        <i class="fas fa-money-bill-wave text-red-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Terbayar Bulan Ini</p>
                        <p class="mt-1 text-2xl font-semibold text-emerald-600">Rp {{ number_format($stats['paid_this_month'], 0, ',', '.') }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100">
                        <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                    </div>
                </div>
            </div>
        </section>

        {{-- TAB NAVIGATION --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            <div class="border-b border-gray-200 px-4 md:px-6 py-4">
                <div class="flex gap-2">
                    @can('lihat pelanggan')
                    <button type="button" id="tabCustomer" onclick="switchTab('customer')"
                            class="tab-btn active px-4 py-2.5 rounded-lg font-semibold text-sm flex items-center gap-2">
                        <i class="fas fa-users"></i>
                        <span>Pelanggan</span>
                    </button>
                    @endcan
                    
                    @can('lihat piutang')
                    <button type="button" id="tabDebt" onclick="switchTab('debt')"
                            class="tab-btn px-4 py-2.5 rounded-lg font-semibold text-sm flex items-center gap-2 text-gray-600 border border-gray-200">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>Tunggakan</span>
                    </button>
                    @endcan

                    @can('lihat reseller applications')
                    <button type="button" id="tabSupplier" onclick="switchTab('supplier')"
                            class="tab-btn px-4 py-2.5 rounded-lg font-semibold text-sm flex items-center gap-2 text-gray-600 border border-gray-200">
                        <i class="fas fa-truck-loading"></i>
                        <span>Daftar Reseller</span>
                    </button>
                    @endcan
                </div>
            </div>

            {{-- TAB CONTENT: CUSTOMER --}}
            <div id="contentCustomer" class="p-4 md:p-6">
                {{-- Toolbar --}}
                <div class="flex flex-col md:flex-row md:items-end gap-4 mb-4">
                    <div class="flex-1 max-w-md">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Cari pelanggan</label>
                        <div class="relative">
                            <input type="text" id="searchCustomer" placeholder="Cari nama, kode, atau telepon..."
                                   class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        </div>
                    </div>
                    <div class="w-full sm:w-40">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Tipe</label>
                        <select id="filterCustomerType"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400">
                            <option value="">Semua Tipe</option>
                            <option value="regular">Regular</option>
                            <option value="reseller">Reseller</option>
                            <option value="vip">VIP</option>
                        </select>
                    </div>
                    <div class="w-full sm:w-40">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Status</label>
                        <select id="filterCustomerStatus"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400">
                            <option value="">Semua Status</option>
                            <option value="active">Aktif</option>
                            <option value="inactive">Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Kode</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Nama</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Telepon</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Tipe</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Transaksi</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Belanja</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="customerTableBody" class="divide-y divide-gray-100 bg-white">
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-spinner fa-spin text-2xl text-gray-300 mb-2"></i>
                                    <p>Memuat data...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div id="customerPagination" class="mt-4 flex items-center justify-between text-sm text-gray-500"></div>
            </div>

            {{-- TAB CONTENT: DEBT --}}
            <div id="contentDebt" class="p-4 md:p-6 hidden">
                {{-- Toolbar --}}
                <div class="flex flex-col md:flex-row md:items-end gap-4 mb-4">
                    <div class="flex-1 max-w-md">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Cari tunggakan</label>
                        <div class="relative">
                            <input type="text" id="searchDebt" placeholder="Cari invoice atau nama pelanggan..."
                                   class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        </div>
                    </div>
                    <div class="w-full sm:w-40">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Status</label>
                        <select id="filterDebtStatus"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400">
                            <option value="">Semua Status</option>
                            <option value="unpaid">Belum Bayar</option>
                            <option value="partial">Sebagian</option>
                        </select>
                    </div>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Invoice</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Pelanggan</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Dibayar</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Sisa</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Jatuh Tempo</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="debtTableBody" class="divide-y divide-gray-100 bg-white">
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-spinner fa-spin text-2xl text-gray-300 mb-2"></i>
                                    <p>Memuat data...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div id="debtPagination" class="mt-4 flex items-center justify-between text-sm text-gray-500"></div>
            </div>

            {{-- TAB CONTENT: SUPPLIER --}}
            <div id="contentSupplier" class="p-4 md:p-6 hidden">
                {{-- Toolbar --}}
                <div class="flex flex-col md:flex-row md:items-end gap-4 mb-4">
                    <div class="flex-1 max-w-md">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Cari supplier</label>
                        <div class="relative">
                            <input type="text" id="searchSupplier" placeholder="Cari nama atau telepon supplier..."
                                   class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        </div>
                    </div>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Supplier</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Kontak</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Tipe</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Diterima</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="supplierTableBody" class="divide-y divide-gray-100 bg-white">
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-spinner fa-spin text-2xl text-gray-300 mb-2"></i>
                                    <p>Memuat data...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div id="supplierPagination" class="mt-4 flex items-center justify-between text-sm text-gray-500"></div>
            </div>
        </section>
    </div>
</main>

{{-- PAYMENT MODAL --}}
<div id="paymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="backdrop-filter: blur(2px);">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gradient-to-r from-teal-500 to-cyan-500 rounded-t-2xl">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fas fa-money-bill-wave"></i>
                Pembayaran Utang
            </h3>
            <button onclick="closePaymentModal()" class="text-white/80 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-6">
            {{-- Debt Info --}}
            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-gray-500">Invoice</p>
                        <p id="modalInvoice" class="font-semibold text-gray-900">-</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Pelanggan</p>
                        <p id="modalCustomer" class="font-semibold text-gray-900">-</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Total Utang</p>
                        <p id="modalTotal" class="font-semibold text-gray-900">-</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Sisa Pembayaran</p>
                        <p id="modalRemaining" class="font-bold text-red-600 text-lg">-</p>
                    </div>
                </div>
            </div>

            {{-- Payment Amount --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Pembayaran</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                    <input type="number" id="paymentAmount" min="1"
                           class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-xl text-lg font-semibold focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400"
                           placeholder="0">
                </div>
                <div class="flex gap-2 mt-2">
                    <button type="button" onclick="setPaymentAmount(0.5)" class="px-3 py-1.5 text-xs font-semibold bg-teal-50 text-teal-600 rounded-lg hover:bg-teal-100">50%</button>
                    <button type="button" onclick="setPaymentAmount(1)" class="px-3 py-1.5 text-xs font-semibold bg-teal-50 text-teal-600 rounded-lg hover:bg-teal-100">Lunas</button>
                </div>
            </div>

            {{-- Payment Method --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Metode Pembayaran</label>
                <div class="grid grid-cols-3 gap-3">
                    <div onclick="selectPaymentMethod('cash')" class="payment-method-card selected border-2 border-gray-200 rounded-xl p-4 text-center" data-method="cash">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-400 to-green-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-money-bill-wave text-white text-lg"></i>
                        </div>
                        <p class="text-sm font-semibold text-gray-800">Tunai</p>
                    </div>
                    <div onclick="selectPaymentMethod('transfer')" class="payment-method-card border-2 border-gray-200 rounded-xl p-4 text-center" data-method="transfer">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-university text-white text-lg"></i>
                        </div>
                        <p class="text-sm font-semibold text-gray-800">Transfer</p>
                    </div>
                    <div onclick="selectPaymentMethod('qris')" class="payment-method-card border-2 border-gray-200 rounded-xl p-4 text-center" data-method="qris">
                        <div class="w-12 h-12 bg-gradient-to-br from-amber-400 to-yellow-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-qrcode text-white text-lg"></i>
                        </div>
                        <p class="text-sm font-semibold text-gray-800">QRIS</p>
                    </div>
                </div>
            </div>

            {{-- Transfer Options --}}
            <div id="transferOptions" class="mb-6 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-3">Pilih Rekening Tujuan:</label>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    @forelse($outletPaymentLinks as $link)
                        <div onclick="selectTransferMethod(this, '{{ $link->id }}', '{{ $link->paymentMethod->name }}', '{{ $link->account_number }}', '{{ $link->account_name }}', '{{ $link->qr_image ? Storage::url($link->qr_image) : '' }}')" 
                             class="transfer-method-card flex flex-col items-center justify-center p-3 border-2 border-gray-100 rounded-xl cursor-pointer hover:border-teal-200 hover:bg-teal-50 transition-all text-center group"
                             data-link-id="{{ $link->id }}">
                            <div class="w-10 h-10 flex items-center justify-center mb-1">
                                @if($link->paymentMethod->icon && Storage::disk('public')->exists($link->paymentMethod->icon))
                                    <img src="{{ Storage::url($link->paymentMethod->icon) }}" class="w-full h-full object-contain filter group-hover:drop-shadow-sm">
                                @else
                                    <div class="w-full h-full rounded-full bg-gray-100 flex items-center justify-center text-gray-400 group-hover:bg-white transition-colors">
                                        <i class="fas fa-university text-sm"></i>
                                    </div>
                                @endif
                            </div>
                            <p class="text-[11px] font-bold text-gray-700 leading-tight">{{ $link->paymentMethod->name }}</p>
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-4 text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                            <p class="text-xs">Belum ada metode transfer.</p>
                        </div>
                    @endforelse
                </div>
                
                <div id="selectedTransferDetail" class="hidden mb-4 p-3 bg-teal-50 border border-teal-100 rounded-xl animate-fadeIn">
                    <div class="flex flex-col items-center text-center">
                        <p class="text-[10px] text-teal-600 font-bold uppercase tracking-wider mb-1" id="transferMethodLabel">-</p>
                        
                        <div id="transferAccInfoSection">
                            <p class="text-sm font-mono font-bold text-gray-900 tracking-wider" id="transferAccNumber">-</p>
                            <p class="text-[10px] text-gray-500 font-medium" id="transferAccName">-</p>
                        </div>

                        <div id="transferQrSection" class="hidden mt-2">
                            <img id="transferQrImage" src="" class="w-40 h-40 object-contain rounded-lg border border-gray-200 bg-white p-2">
                            <p class="text-[9px] text-teal-600 font-medium mt-1">Scan QR untuk membayar</p>
                        </div>
                    </div>
                </div>

                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Referensi (Opsional)</label>
                <input type="text" id="referenceNumber" 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400"
                       placeholder="Masukkan nomor referensi transfer">
            </div>

            <input type="hidden" id="selectedOutletPaymentLinkId" value="">

            {{-- Notes --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                <textarea id="paymentNotes" rows="2"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400"
                          placeholder="Tambahkan catatan pembayaran"></textarea>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-gray-200 flex gap-3">
            <button onclick="closePaymentModal()" class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <button onclick="processPayment()" id="btnProcessPayment" class="flex-1 px-4 py-3 bg-gradient-to-r from-teal-500 to-cyan-500 text-white rounded-xl font-semibold hover:from-teal-600 hover:to-cyan-600 transition-all shadow-md">
                <i class="fas fa-check mr-2"></i>
                Bayar Sekarang
            </button>
        </div>
    </div>
</div>

{{-- HISTORY MODAL --}}
<div id="historyModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" style="backdrop-filter: blur(2px);">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
            <div>
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-history text-teal-500"></i>
                    Riwayat Transaksi
                </h3>
                <p id="historyCustomerName" class="text-sm text-gray-500 mt-1">-</p>
            </div>
            <button onclick="closeHistoryModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto p-6">
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">Invoice</th>
                            <th class="px-4 py-3 text-left font-medium">Tanggal</th>
                            <th class="px-4 py-3 text-center font-medium">Item</th>
                            <th class="px-4 py-3 text-left font-medium">Metode</th>
                            <th class="px-4 py-3 text-left font-medium">Total</th>
                            <th class="px-4 py-3 text-left font-medium">Tunggakan</th>
                            <th class="px-4 py-3 text-center font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody" class="divide-y divide-gray-100">
                        {{-- Data injected here --}}
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end">
            <button onclick="closeHistoryModal()" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- SUPPLIER DETAIL MODAL --}}
<div id="supplierDetailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center p-4 transition-all duration-300" style="backdrop-filter: blur(4px);">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all">
        {{-- Header with Gradient --}}
        <div class="relative h-32 bg-gradient-to-br from-teal-500 to-cyan-600">
            <button onclick="closeSupplierDetail()" class="absolute top-4 right-4 text-white/80 hover:text-white transition-colors bg-white/10 p-2 rounded-full backdrop-blur-md">
                <i class="fas fa-times"></i>
            </button>
            <div class="absolute -bottom-12 left-8">
                <div class="w-24 h-24 bg-white rounded-2xl shadow-lg flex items-center justify-center border-4 border-white overflow-hidden">
                    <span id="modalSupplierInitial" class="text-3xl font-bold text-teal-600 uppercase">S</span>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="pt-16 pb-8 px-8">
            <div class="mb-6">
                <h3 id="modalSupplierName" class="text-2xl font-bold text-gray-900 leading-tight">-</h3>
                <div class="flex items-center gap-2 mt-1">
                    <span id="modalSupplierCode" class="text-xs font-mono font-bold text-teal-600 bg-teal-50 px-2 py-0.5 rounded">-</span>
                    <span id="modalSupplierType" class="text-xs font-medium text-gray-500 uppercase tracking-wider">-</span>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                {{-- Contact Info --}}
                <div class="space-y-4">
                    <div class="flex items-start gap-4 p-3 rounded-2xl hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 bg-teal-50 rounded-xl flex items-center justify-center shrink-0">
                            <i class="fas fa-phone text-teal-500"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400">Telepon / WA</p>
                            <p id="modalSupplierPhone" class="text-sm font-semibold text-gray-900">-</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-3 rounded-2xl hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center shrink-0">
                            <i class="fas fa-envelope text-blue-500"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400">Email</p>
                            <p id="modalSupplierEmail" class="text-sm font-semibold text-gray-900">-</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-3 rounded-2xl hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center shrink-0">
                            <i class="fas fa-map-marker-alt text-amber-500"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400">Alamat</p>
                            <p id="modalSupplierAddress" class="text-sm font-semibold text-gray-900 leading-relaxed">-</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-3 rounded-2xl hover:bg-gray-50 transition-colors border border-dashed border-gray-200">
                        <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center shrink-0">
                            <i class="fas fa-info-circle text-gray-400"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400">Status Kontrak</p>
                            <p class="text-sm font-semibold text-emerald-600">Aktif sejak <span id="modalSupplierAcceptedAt">-</span></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CTA Button --}}
            <div class="mt-8">
                <a id="btnSupplierWa" href="#" target="_blank" class="flex items-center justify-center gap-3 w-full bg-[#25D366] hover:bg-[#20ba59] text-white py-4 rounded-2xl font-bold shadow-lg shadow-green-200 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <i class="fab fa-whatsapp text-xl"></i>
                    Hubungi via WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
// State
let currentTab = @can('lihat pelanggan') 'customer' @else 'debt' @endcan;
let currentDebt = null;
let selectedPaymentMethod = 'cash';
let customerPage = 1;
let debtPage = 1;
let supplierPage = 1;

// Debounce helper
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Format currency
function formatRupiah(amount) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
}

// Switch tabs
function switchTab(tab) {
    currentTab = tab;
    
    document.getElementById('tabCustomer').classList.toggle('active', tab === 'customer');
    document.getElementById('tabDebt').classList.toggle('active', tab === 'debt');
    document.getElementById('tabSupplier')?.classList.toggle('active', tab === 'supplier');

    document.getElementById('tabCustomer').classList.toggle('border', tab !== 'customer');
    document.getElementById('tabCustomer').classList.toggle('border-gray-200', tab !== 'customer');
    document.getElementById('tabCustomer').classList.toggle('text-gray-600', tab !== 'customer');
    
    document.getElementById('tabDebt').classList.toggle('border', tab !== 'debt');
    document.getElementById('tabDebt').classList.toggle('border-gray-200', tab !== 'debt');
    document.getElementById('tabDebt').classList.toggle('text-gray-600', tab !== 'debt');

    if (document.getElementById('tabSupplier')) {
        document.getElementById('tabSupplier').classList.toggle('border', tab !== 'supplier');
        document.getElementById('tabSupplier').classList.toggle('border-gray-200', tab !== 'supplier');
        document.getElementById('tabSupplier').classList.toggle('text-gray-600', tab !== 'supplier');
    }
    
    document.getElementById('contentCustomer').classList.toggle('hidden', tab !== 'customer');
    document.getElementById('contentDebt').classList.toggle('hidden', tab !== 'debt');
    document.getElementById('contentSupplier').classList.toggle('hidden', tab !== 'supplier');
    
    if (tab === 'customer') {
        loadCustomers();
    } else if (tab === 'supplier') {
        loadSuppliers();
    } else {
        loadDebts();
    }
}

// Load customers
function loadCustomers(page = 1) {
    customerPage = page;
    const search = document.getElementById('searchCustomer').value;
    const type = document.getElementById('filterCustomerType').value;
    const status = document.getElementById('filterCustomerStatus').value;
    
    const params = new URLSearchParams({ page, search, type, status });
    
    document.getElementById('customerTableBody').innerHTML = `
        <tr>
            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                <i class="fas fa-spinner fa-spin text-2xl text-gray-300 mb-2"></i>
                <p>Memuat data...</p>
            </td>
        </tr>
    `;
    
    fetch(`{{ route('customer-debts.customers') }}?${params}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                renderCustomerTable(data.customers, data.pagination);
            }
        })
        .catch(err => {
            console.error('Load customers error:', err);
            document.getElementById('customerTableBody').innerHTML = `
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-red-500">
                        <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                        <p>Gagal memuat data</p>
                    </td>
                </tr>
            `;
        });
}

// Render customer table
function renderCustomerTable(customers, pagination) {
    const tbody = document.getElementById('customerTableBody');
    
    if (customers.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="px-4 py-12 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                            <i class="fas fa-users text-2xl text-gray-300"></i>
                        </div>
                        <p class="text-gray-500 font-medium">Belum ada pelanggan</p>
                        <p class="text-gray-400 text-sm">Pelanggan akan muncul setelah ada transaksi</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = customers.map(c => `
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-4 py-3">
                <span class="inline-flex items-center rounded-md bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-800 font-mono">
                    ${c.code || '-'}
                </span>
            </td>
            <td class="px-4 py-3">
                <div class="font-semibold text-gray-900">${c.name}</div>
                ${c.email ? `<div class="text-xs text-gray-500">${c.email}</div>` : ''}
            </td>
            <td class="px-4 py-3 text-gray-600">${c.phone || '-'}</td>
            <td class="px-4 py-3">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ${getTypeBadgeClass(c.type)}">
                    ${getTypeLabel(c.type)}
                </span>
            </td>
            <td class="px-4 py-3">
                <span class="font-semibold text-gray-900">${c.sales_count || 0}</span>
                <span class="text-gray-400 text-xs">transaksi</span>
            </td>
            <td class="px-4 py-3 font-semibold text-gray-900">${formatRupiah(c.sales_sum_grand_total || 0)}</td>
            <td class="px-4 py-3">
                ${c.is_active 
                    ? '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>Aktif</span>'
                    : '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-600 border border-gray-200"><span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1.5"></span>Tidak Aktif</span>'
                }
            </td>
            <td class="px-4 py-3 text-center">
                <button onclick="openHistoryModal(${c.id})" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-teal-50 text-teal-600 hover:bg-teal-100 transition-colors text-xs font-semibold border border-teal-100">
                    <i class="fas fa-history"></i>
                    Riwayat
                </button>
            </td>
        </tr>
    `).join('');
    
    renderPagination('customerPagination', pagination, loadCustomers);
}

// Load debts
function loadDebts(page = 1) {
    debtPage = page;
    const search = document.getElementById('searchDebt').value;
    const status = document.getElementById('filterDebtStatus').value;
    
    const params = new URLSearchParams({ page, search, status });
    
    document.getElementById('debtTableBody').innerHTML = `
        <tr>
            <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                <i class="fas fa-spinner fa-spin text-2xl text-gray-300 mb-2"></i>
                <p>Memuat data...</p>
            </td>
        </tr>
    `;
    
    fetch(`{{ route('customer-debts.debts') }}?${params}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                renderDebtTable(data.debts, data.pagination);
            }
        })
        .catch(err => {
            console.error('Load debts error:', err);
            document.getElementById('debtTableBody').innerHTML = `
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center text-red-500">
                        <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                        <p>Gagal memuat data</p>
                    </td>
                </tr>
            `;
        });
}

// Render debt table
function renderDebtTable(debts, pagination) {
    const tbody = document.getElementById('debtTableBody');
    
    if (debts.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="px-4 py-12 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mb-3">
                            <i class="fas fa-check-circle text-2xl text-emerald-500"></i>
                        </div>
                        <p class="text-gray-700 font-medium">Tidak ada tunggakan</p>
                        <p class="text-gray-400 text-sm">Semua piutang sudah terbayar lunas</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = debts.map(d => `
        <tr class="debt-row ${d.is_overdue ? 'overdue' : ''} hover:bg-gray-50 transition-colors">
            <td class="px-4 py-3">
                <span class="inline-flex items-center rounded-md bg-teal-50 px-2.5 py-1 text-xs font-semibold text-teal-800 font-mono border border-teal-100">
                    ${d.invoice_number}
                </span>
            </td>
            <td class="px-4 py-3">
                <div class="font-semibold text-gray-900">${d.customer_name}</div>
                <div class="text-xs text-gray-500">${d.customer_phone}</div>
            </td>
            <td class="px-4 py-3 text-gray-600 text-sm">${d.created_at}</td>
            <td class="px-4 py-3 font-medium text-gray-900">${formatRupiah(d.amount)}</td>
            <td class="px-4 py-3 text-emerald-600 font-medium">${formatRupiah(d.paid_amount)}</td>
            <td class="px-4 py-3 font-bold text-red-600">${formatRupiah(d.remaining_amount)}</td>
            <td class="px-4 py-3">
                ${d.due_date 
                    ? `<span class="${d.is_overdue ? 'text-red-600 font-semibold' : 'text-gray-600'}">${d.due_date}${d.is_overdue ? ` <span class="text-xs">(${d.days_overdue} hari)</span>` : ''}</span>`
                    : '<span class="text-gray-400">-</span>'
                }
            </td>
            <td class="px-4 py-3">
                ${getDebtStatusBadge(d.status, d.is_overdue)}
            </td>
            <td class="px-4 py-3 text-center">
                @can('bayar piutang')
                <button onclick="openPaymentModal(${JSON.stringify(d).replace(/"/g, '&quot;')})"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gradient-to-r from-teal-500 to-cyan-500 text-white text-xs font-semibold hover:from-teal-600 hover:to-cyan-600 transition-all shadow-sm">
                    <i class="fas fa-credit-card"></i>
                    Bayar
                </button>
                @endcan
            </td>
        </tr>
    `).join('');
    
    renderPagination('debtPagination', pagination, loadDebts);
}

// Load suppliers
function loadSuppliers(page = 1) {
    supplierPage = page;
    const search = document.getElementById('searchSupplier').value;
    const params = new URLSearchParams({ page, search });
    
    document.getElementById('supplierTableBody').innerHTML = `
        <tr>
            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                <i class="fas fa-spinner fa-spin text-2xl text-gray-300 mb-2"></i>
                <p>Memuat data...</p>
            </td>
        </tr>
    `;
    
    fetch(`{{ route('customer-debts.suppliers') }}?${params}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                renderSupplierTable(data.suppliers, data.pagination);
            }
        })
        .catch(err => {
            console.error('Load suppliers error:', err);
            document.getElementById('supplierTableBody').innerHTML = `
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-red-500">
                        <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                        <p>Gagal memuat data</p>
                    </td>
                </tr>
            `;
        });
}

// Render supplier table
function renderSupplierTable(suppliers, pagination) {
    const tbody = document.getElementById('supplierTableBody');
    
    if (suppliers.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="px-4 py-12 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                            <i class="fas fa-truck-loading text-2xl text-gray-300"></i>
                        </div>
                        <p class="text-gray-500 font-medium">Belum ada supplier (reseller)</p>
                        <p class="text-gray-400 text-sm">Supplier muncul dari lamaran reseller yang diterima</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = suppliers.map(s => `
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-4 py-3">
                <div class="font-semibold text-gray-900">${s.name}</div>
                <div class="text-xs text-gray-500">${s.code || '-'}</div>
            </td>
            <td class="px-4 py-3">
                <div class="text-sm font-medium text-gray-900">${s.phone || '-'}</div>
                <div class="text-xs text-gray-500">${s.email || '-'}</div>
            </td>
             <td class="px-4 py-3">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ${getTypeBadgeClass(s.type)}">
                    ${getTypeLabel(s.type)}
                </span>
            </td>
            <td class="px-4 py-3 text-gray-600 text-sm">${s.accepted_at}</td>
            <td class="px-4 py-3 text-center">
                <div class="flex items-center justify-center gap-2">
                    <button onclick='openSupplierDetail(${JSON.stringify(s).replace(/'/g, "&#39;")})'
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-teal-50 text-teal-600 hover:bg-teal-100 transition-colors text-xs font-semibold border border-teal-100 shadow-sm" title="Detail Supplier">
                        <i class="fas fa-eye"></i>
                        Detail
                    </button>
                    <button onclick="cancelSupplierContract(${s.id}, '${s.name}')" 
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors text-xs font-semibold border border-red-100 shadow-sm" title="Batalkan Kontrak">
                        <i class="fas fa-times-circle"></i>
                        Putus Mitra
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
    
    renderPagination('supplierPagination', pagination, loadSuppliers);
}

function openSupplierDetail(s) {
    const modal = document.getElementById('supplierDetailModal');
    
    document.getElementById('modalSupplierInitial').textContent = s.name.charAt(0);
    document.getElementById('modalSupplierName').textContent = s.name;
    document.getElementById('modalSupplierCode').textContent = s.code || '-';
    document.getElementById('modalSupplierType').textContent = getTypeLabel(s.type);
    document.getElementById('modalSupplierPhone').textContent = s.phone || 'N/A';
    document.getElementById('modalSupplierEmail').textContent = s.email || 'N/A';
    document.getElementById('modalSupplierAddress').textContent = s.address || 'Alamat tidak tersedia';
    document.getElementById('modalSupplierAcceptedAt').textContent = s.accepted_at;
    
    const waBtn = document.getElementById('btnSupplierWa');
    if (s.whatsapp_url) {
        waBtn.href = s.whatsapp_url;
        waBtn.classList.remove('hidden');
    } else {
        waBtn.classList.add('hidden');
    }
    
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.querySelector('div').classList.remove('scale-95', 'opacity-0');
        modal.querySelector('div').classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeSupplierDetail() {
    const modal = document.getElementById('supplierDetailModal');
    modal.querySelector('div').classList.add('scale-95', 'opacity-0');
    modal.querySelector('div').classList.remove('scale-100', 'opacity-100');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

function cancelSupplierContract(id, name) {
    Swal.fire({
        title: 'Batalkan Kontrak?',
        text: `Apakah Anda yakin ingin menghentikan hubungan reseller dengan "${name}"? Status pelanggan akan kembali menjadi Regular.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Putuskan Mitra',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/customer-debts/${id}/cancel-contract`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Berhasil', data.message, 'success');
                    loadSuppliers(supplierPage);
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
            });
        }
    });
}

// Render pagination
function renderPagination(containerId, pagination, loadFunction) {
    const container = document.getElementById(containerId);
    
    if (pagination.last_page <= 1) {
        container.innerHTML = `<span>Menampilkan ${pagination.total} data</span>`;
        return;
    }
    
    let pagesHtml = '';
    for (let i = 1; i <= pagination.last_page; i++) {
        if (i === pagination.current_page) {
            pagesHtml += `<button class="px-3 py-1.5 bg-teal-500 text-white rounded-lg font-semibold">${i}</button>`;
        } else {
            pagesHtml += `<button onclick="${loadFunction.name}(${i})" class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">${i}</button>`;
        }
    }
    
    container.innerHTML = `
        <span>Menampilkan ${(pagination.current_page - 1) * pagination.per_page + 1} - ${Math.min(pagination.current_page * pagination.per_page, pagination.total)} dari ${pagination.total}</span>
        <div class="flex gap-1">${pagesHtml}</div>
    `;
}

// Helper functions
function getTypeBadgeClass(type) {
    switch (type) {
        case 'vip': return 'bg-amber-50 text-amber-700 border border-amber-100';
        case 'reseller': return 'bg-blue-50 text-blue-700 border border-blue-100';
        default: return 'bg-gray-50 text-gray-700 border border-gray-200';
    }
}

function getTypeLabel(type) {
    switch (type) {
        case 'vip': return 'VIP';
        case 'reseller': return 'Reseller';
        default: return 'Regular';
    }
}

function getDebtStatusBadge(status, isOverdue) {
    if (isOverdue) {
        return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-100"><span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5 animate-pulse"></span>Terlambat</span>';
    }
    switch (status) {
        case 'partial':
            return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-100"><span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>Sebagian</span>';
        default:
            return '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-100"><span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>Belum Bayar</span>';
    }
}

// Payment Modal
function openPaymentModal(debt) {
    currentDebt = debt;
    selectedPaymentMethod = 'cash';
    
    document.getElementById('modalInvoice').textContent = debt.invoice_number;
    document.getElementById('modalCustomer').textContent = debt.customer_name;
    document.getElementById('modalTotal').textContent = formatRupiah(debt.amount);
    document.getElementById('modalRemaining').textContent = formatRupiah(debt.remaining_amount);
    
    document.getElementById('paymentAmount').value = '';
    document.getElementById('paymentAmount').max = debt.remaining_amount;
    document.getElementById('referenceNumber').value = '';
    document.getElementById('paymentNotes').value = '';
    
    // Reset payment method selection
    document.querySelectorAll('.payment-method-card').forEach(card => {
        card.classList.remove('selected');
        if (card.dataset.method === 'cash') {
            card.classList.add('selected');
        }
    });
    document.getElementById('selectedOutletPaymentLinkId').value = '';
    document.getElementById('selectedTransferDetail').classList.add('hidden');
    document.querySelectorAll('.transfer-method-card').forEach(c => c.classList.remove('border-teal-400', 'bg-teal-50', 'ring-2', 'ring-teal-200'));

    document.getElementById('paymentModal').classList.remove('hidden');
}

function selectTransferMethod(element, linkId, methodName, accNumber, accName, qrImage) {
    // Reset other cards
    document.querySelectorAll('.transfer-method-card').forEach(c => {
        c.classList.remove('border-teal-400', 'bg-teal-50', 'ring-2', 'ring-teal-200');
        c.classList.add('border-gray-100');
    });

    // Select this card
    element.classList.remove('border-gray-100');
    element.classList.add('border-teal-400', 'bg-teal-50', 'ring-2', 'ring-teal-200');

    // Set value
    document.getElementById('selectedOutletPaymentLinkId').value = linkId;

    // Show details
    document.getElementById('transferMethodLabel').textContent = methodName;
    
    if (qrImage) {
        document.getElementById('transferQrImage').src = qrImage;
        document.getElementById('transferQrSection').classList.remove('hidden');
        document.getElementById('transferAccInfoSection').classList.add('hidden');
    } else {
        document.getElementById('transferAccNumber').textContent = accNumber || '-';
        document.getElementById('transferAccName').textContent = accName || '-';
        document.getElementById('transferQrSection').classList.add('hidden');
        document.getElementById('transferAccInfoSection').classList.remove('hidden');
    }
    
    document.getElementById('selectedTransferDetail').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
    currentDebt = null;
}

function selectPaymentMethod(method) {
    selectedPaymentMethod = method;
    
    document.querySelectorAll('.payment-method-card').forEach(card => {
        card.classList.toggle('selected', card.dataset.method === method);
    });
    
    document.getElementById('transferOptions').classList.toggle('hidden', method !== 'transfer');
}

function openHistoryModal(customerId) {
    const modal = document.getElementById('historyModal');
    const tbody = document.getElementById('historyTableBody');
    const nameEl = document.getElementById('historyCustomerName');
    
    modal.classList.remove('hidden');
    nameEl.textContent = 'Memuat data...';
    tbody.innerHTML = `
        <tr>
            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                <i class="fas fa-spinner fa-spin text-xl mb-2"></i>
                <p>Mengambil riwayat...</p>
            </td>
        </tr>
    `;
    
    fetch(`{{ url('customer-debts') }}/${customerId}/history`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                nameEl.textContent = `${data.customer.name} (${data.customer.code || '-'})`;
                
                 if (data.sales.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                <p>Belum ada riwayat transaksi di outlet ini</p>
                            </td>
                        </tr>
                    `;
                    return;
                }
                
                tbody.innerHTML = data.sales.map(s => `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-teal-600 font-semibold cursor-pointer hover:underline" onclick="window.location.href='{{ url('sales') }}/${s.id}'">${s.invoice_number}</td>
                        <td class="px-4 py-3 text-gray-600">${s.date}</td>
                        <td class="px-4 py-3 text-center text-gray-600">${s.items_count}</td>
                        <td class="px-4 py-3 capitalize text-gray-800">${s.payment_method}</td>
                        <td class="px-4 py-3 font-semibold text-gray-900">${formatRupiah(s.grand_total)}</td>
                        <td class="px-4 py-3 font-semibold ${s.remaining_debt > 0 ? 'text-red-500' : 'text-emerald-500'}">
                            ${s.remaining_debt > 0 ? formatRupiah(s.remaining_debt) : '-'}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${getStatusBadgeClass(s.status)}">
                                ${getStatusLabel(s.status)}
                            </span>
                        </td>
                    </tr>
                `).join('');
            }
        })
        .catch(err => {
            console.error(err);
             tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-red-500">
                        <p>Gagal memuat data</p>
                    </td>
                </tr>
            `;
        });
}

function closeHistoryModal() {
    document.getElementById('historyModal').classList.add('hidden');
}

function getStatusBadgeClass(status) {
    switch (status) {
        case 'completed': return 'bg-green-100 text-green-800';
        case 'debt': return 'bg-amber-100 text-amber-800';
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'canceled': return 'bg-gray-100 text-gray-800';
        case 'refunded': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

function getStatusLabel(status) {
    switch (status) {
        case 'completed': return 'Lunas / Selesai';
        case 'debt': return 'Belum Lunas';
        case 'pending': return 'Pending';
        case 'canceled': return 'Dibatalkan';
        case 'refunded': return 'Refund';
        default: return status;
    }
}


// Process payment
function processPayment() {
    if (!currentDebt) return;
    
    const amount = parseFloat(document.getElementById('paymentAmount').value) || 0;
    
    if (amount <= 0) {
        Swal.fire('Error', 'Masukkan jumlah pembayaran yang valid', 'error');
        return;
    }
    
    if (amount > currentDebt.remaining_amount) {
        Swal.fire('Error', 'Jumlah pembayaran melebihi sisa utang', 'error');
        return;
    }
    
    const btn = document.getElementById('btnProcessPayment');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
    
    if (selectedPaymentMethod === 'qris') {
        processMidtransPayment(amount);
    } else {
        processCashTransferPayment(amount);
    }
}

function processCashTransferPayment(amount) {
    const data = {
        amount: amount,
        payment_method: selectedPaymentMethod,
        reference_number: document.getElementById('referenceNumber').value,
        notes: document.getElementById('paymentNotes').value,
        outlet_payment_link_id: document.getElementById('selectedOutletPaymentLinkId').value,
    };
    
    fetch(`/customer-debts/${currentDebt.id}/pay`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify(data),
    })
    .then(r => r.json())
    .then(response => {
        const btn = document.getElementById('btnProcessPayment');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check mr-2"></i>Bayar Sekarang';
        
        if (response.success) {
            closePaymentModal();
            Swal.fire({
                icon: 'success',
                title: 'Pembayaran Berhasil',
                text: response.message,
                confirmButtonColor: '#14b8a6',
            }).then(() => {
                loadDebts(debtPage);
                // Reload page to update stats
                window.location.reload();
            });
        } else {
            Swal.fire('Error', response.message || 'Gagal memproses pembayaran', 'error');
        }
    })
    .catch(err => {
        console.error('Payment error:', err);
        const btn = document.getElementById('btnProcessPayment');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check mr-2"></i>Bayar Sekarang';
        Swal.fire('Error', 'Terjadi kesalahan saat memproses pembayaran', 'error');
    });
}

function processMidtransPayment(amount) {
    fetch(`/customer-debts/${currentDebt.id}/midtrans-token`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({ amount: amount }),
    })
    .then(r => r.json())
    .then(response => {
        const btn = document.getElementById('btnProcessPayment');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check mr-2"></i>Bayar Sekarang';
        
        if (response.success) {
            closePaymentModal();
            snap.pay(response.snap_token, {
                onSuccess: function(result) {
                    // Record the payment
                    fetch(`/customer-debts/${currentDebt.id}/pay`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            amount: amount,
                            payment_method: 'qris',
                            reference_number: result.transaction_id,
                            notes: 'Pembayaran via Midtrans QRIS',
                        }),
                    })
                    .then(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Pembayaran Berhasil',
                            text: 'Pembayaran QRIS berhasil dicatat',
                            confirmButtonColor: '#14b8a6',
                        }).then(() => {
                            window.location.reload();
                        });
                    });
                },
                onPending: function(result) {
                    Swal.fire('Menunggu', 'Menunggu konfirmasi pembayaran', 'info');
                },
                onError: function(result) {
                    Swal.fire('Error', 'Pembayaran gagal', 'error');
                },
                onClose: function() {
                    // User closed the popup
                }
            });
        } else {
            Swal.fire('Error', response.message || 'Gagal membuat token pembayaran', 'error');
        }
    })
    .catch(err => {
        console.error('Midtrans error:', err);
        const btn = document.getElementById('btnProcessPayment');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check mr-2"></i>Bayar Sekarang';
        Swal.fire('Error', 'Gagal terhubung ke payment gateway', 'error');
    });
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Load initial data
    loadCustomers();
    
    // Search with debounce
    const debouncedCustomerSearch = debounce(() => loadCustomers(1), 400);
    const debouncedDebtSearch = debounce(() => loadDebts(1), 400);
    const debouncedSupplierSearch = debounce(() => loadSuppliers(1), 400);
    
    document.getElementById('searchCustomer').addEventListener('input', debouncedCustomerSearch);
    document.getElementById('searchDebt').addEventListener('input', debouncedDebtSearch);
    document.getElementById('searchSupplier').addEventListener('input', debouncedSupplierSearch);
    
    // Filters
    document.getElementById('filterCustomerType').addEventListener('change', () => loadCustomers(1));
    document.getElementById('filterCustomerStatus').addEventListener('change', () => loadCustomers(1));
    document.getElementById('filterDebtStatus').addEventListener('change', () => loadDebts(1));
    
    // Close modal on backdrop click
    document.getElementById('paymentModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePaymentModal();
        }
    });

    document.getElementById('supplierDetailModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeSupplierDetail();
        }
    });
});
</script>
@endpush

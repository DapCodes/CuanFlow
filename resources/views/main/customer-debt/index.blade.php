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
                    <button type="button" id="tabCustomer" onclick="switchTab('customer')"
                            class="tab-btn active px-4 py-2.5 rounded-lg font-semibold text-sm flex items-center gap-2">
                        <i class="fas fa-users"></i>
                        <span>Pelanggan</span>
                    </button>
                    <button type="button" id="tabDebt" onclick="switchTab('debt')"
                            class="tab-btn px-4 py-2.5 rounded-lg font-semibold text-sm flex items-center gap-2 text-gray-600 border border-gray-200">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>Tunggakan</span>
                    </button>
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
                            </tr>
                        </thead>
                        <tbody id="customerTableBody" class="divide-y divide-gray-100 bg-white">
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
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

            {{-- Transfer Reference (hidden by default) --}}
            <div id="transferOptions" class="mb-6 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Referensi (Opsional)</label>
                <input type="text" id="referenceNumber" 
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400"
                       placeholder="Masukkan nomor referensi transfer">
            </div>

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
@endsection

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
// State
let currentTab = 'customer';
let currentDebt = null;
let selectedPaymentMethod = 'cash';
let customerPage = 1;
let debtPage = 1;

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
    document.getElementById('tabCustomer').classList.toggle('border', tab !== 'customer');
    document.getElementById('tabCustomer').classList.toggle('border-gray-200', tab !== 'customer');
    document.getElementById('tabCustomer').classList.toggle('text-gray-600', tab !== 'customer');
    document.getElementById('tabDebt').classList.toggle('border', tab !== 'debt');
    document.getElementById('tabDebt').classList.toggle('border-gray-200', tab !== 'debt');
    document.getElementById('tabDebt').classList.toggle('text-gray-600', tab !== 'debt');
    
    document.getElementById('contentCustomer').classList.toggle('hidden', tab !== 'customer');
    document.getElementById('contentDebt').classList.toggle('hidden', tab !== 'debt');
    
    if (tab === 'customer') {
        loadCustomers();
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
            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
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
                    <td colspan="7" class="px-4 py-8 text-center text-red-500">
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
                <td colspan="7" class="px-4 py-12 text-center">
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
                <button onclick="openPaymentModal(${JSON.stringify(d).replace(/"/g, '&quot;')})"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gradient-to-r from-teal-500 to-cyan-500 text-white text-xs font-semibold hover:from-teal-600 hover:to-cyan-600 transition-all shadow-sm">
                    <i class="fas fa-credit-card"></i>
                    Bayar
                </button>
            </td>
        </tr>
    `).join('');
    
    renderPagination('debtPagination', pagination, loadDebts);
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
    document.getElementById('transferOptions').classList.add('hidden');
    
    document.getElementById('paymentModal').classList.remove('hidden');
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

function setPaymentAmount(multiplier) {
    if (!currentDebt) return;
    const amount = Math.floor(currentDebt.remaining_amount * multiplier);
    document.getElementById('paymentAmount').value = amount;
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
    
    document.getElementById('searchCustomer').addEventListener('input', debouncedCustomerSearch);
    document.getElementById('searchDebt').addEventListener('input', debouncedDebtSearch);
    
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
});
</script>
@endpush

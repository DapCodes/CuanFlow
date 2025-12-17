@extends('layouts.app')

@section('title', 'Point of Sale - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@push('styles')
<style>
    /* ===================== GLOBAL & LAYOUT ===================== */
    html,
    body {
        height: 100%;
        overflow: hidden; /* No global scroll on desktop */
        color: #111827;
    }

    body {
        background-color: #f5f5f7;
    }

    main {
        height: calc(100vh - 65px); /* Sesuaikan dengan tinggi navbar */
        overflow: hidden;
    }

    .pos-container {
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        max-width: 100vw;
    }

    .breadcrumb-container {
        display: none;
    }

    .pos-main {
        flex: 1;
        display: grid;
        grid-template-columns: minmax(0, 1fr) 360px;
        gap: 0.75rem;
        padding: 0.75rem;
        overflow: hidden;
        min-height: 0;
    }

    /* ===================== PANELS ===================== */
    .products-panel,
    .order-panel {
        display: flex;
        flex-direction: column;
        background-color: #ffffff;
        border-radius: 10px;
        overflow: hidden;
        min-height: 0;
        border: 1px solid #e5e7eb;
        box-shadow:
            0 8px 16px rgba(15, 23, 42, 0.04),
            0 1px 3px rgba(15, 23, 42, 0.08);
    }

    .products-toolbar {
        padding: 0.75rem;
        border-bottom: 1px solid #e5e7eb;
        flex-shrink: 0;
        background-color: #f9fafb;
    }

    .products-content {
        flex: 1;
        overflow-y: auto;
        padding: 0.75rem;
        min-height: 0;
    }

    /* ===================== SEARCH & FILTER ===================== */
    .search-box {
        position: relative;
        flex: 1;
        max-width: 320px;
    }

    .search-input {
        width: 100%;
        padding: 0.55rem 0.9rem 0.55rem 2.3rem;
        font-size: 0.813rem;
        border-radius: 999px;
        border: 1px solid #d1d5db;
        background-color: #f9fafb;
        color: #111827;
        transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
    }

    .search-input::placeholder {
        color: #9ca3af;
    }

    .search-input:focus {
        outline: none;
        border-color: #f97316;
        background-color: #ffffff;
        box-shadow:
            0 0 0 1px rgba(249, 115, 22, 0.08),
            0 6px 10px rgba(15, 23, 42, 0.06);
    }

    .search-icon {
        position: absolute;
        left: 0.85rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.85rem;
        color: #9ca3af;
    }

    .filter-select {
        padding: 0.5rem 0.75rem;
        font-size: 0.813rem;
        min-width: 140px;
        border-radius: 999px;
        border: 1px solid #d1d5db;
        background-color: #ffffff;
        color: #111827;
        cursor: pointer;
        transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
        /* appearance: none; */
        /* -webkit-appearance: none; */
        -moz-appearance: none;
    }

    .filter-select:focus {
        outline: none;
        border-color: #f97316;
        box-shadow: 0 0 0 1px rgba(249, 115, 22, 0.12);
    }

    .category-tabs {
        margin-top: 0.5rem;
        display: flex;
        gap: 0.35rem;
        overflow-x: auto;
        padding-bottom: 0.1rem;
    }

    .category-tabs::-webkit-scrollbar {
        height: 4px;
    }

    .category-tab {
        padding: 0.35rem 0.75rem;
        font-size: 0.75rem;
        border-radius: 999px;
        border: 1px solid transparent;
        background-color: #f3f4f6;
        color: #4b5563;
        white-space: nowrap;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease;
    }

    .category-tab i {
        font-size: 0.8rem;
    }

    .category-tab:hover {
        background-color: #e5e7eb;
    }

    .category-tab.active {
        background-color: #fff7ed;
        border-color: #f97316;
        color: #9a3412;
    }

    /* ===================== PRODUCT GRID ===================== */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 0.75rem;
    }

    .product-card {
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.55rem;
        cursor: pointer;
        transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.12s ease, background-color 0.15s ease;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .product-card:hover {
        border-color: #f97316;
        box-shadow:
            0 10px 18px rgba(15, 23, 42, 0.08),
            0 1px 3px rgba(15, 23, 42, 0.08);
        transform: translateY(-1px);
        background-color: #fffdf9;
    }

    .product-image {
        width: 100%;
        height: 100px;
        object-fit: cover;
        border-radius: 6px;
        margin-bottom: 0.5rem;
        background-color: #e5e7eb;
    }

    .product-placeholder {
        width: 100%;
        height: 100px;
        background-color: #f3f4f6;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.5rem;
    }

    .product-placeholder i {
        color: #9ca3af;
        font-size: 1.5rem;
    }

    .product-name {
        font-size: 0.78rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.12rem;
        line-height: 1.2;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 1.8rem;
    }

    .product-price {
        font-size: 0.82rem;
        font-weight: 700;
        color: #111827;
        margin-top: auto;
    }

    .product-stock {
        font-size: 0.65rem;
        margin-top: 0.15rem;
    }

    /* Discount Badge */
    .discount-badge {
        position: absolute;
        top: 6px;
        right: 6px;
        z-index: 10;
    }

    .discount-badge > div {
        font-size: 0.62rem;
        padding: 0.18rem 0.45rem;
        border-radius: 999px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.15);
    }

    /* ===================== ORDER PANEL ===================== */
    .order-header {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #e5e7eb;
        flex-shrink: 0;
        background-color: #f9fafb;
    }

    .order-items {
        flex: 1;
        overflow-y: auto;
        padding: 0.75rem;
        min-height: 0;
        background-color: #ffffff;
    }

    .order-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.55rem 0.6rem;
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        transition: background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease, transform 0.12s ease;
    }

    .order-item:hover {
        background-color: #f9fafb;
        border-color: #d1d5db;
        box-shadow: 0 7px 12px rgba(15, 23, 42, 0.06);
        transform: translateY(-1px);
    }

    .order-item-info {
        flex: 1;
        min-width: 0;
    }

    .order-item-name {
        font-size: 0.813rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.1rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .order-item-price {
        font-size: 0.7rem;
        color: #6b7280;
    }

    .qty-controls {
        display: inline-flex;
        align-items: stretch;
        gap: 0;
        border: 1px solid #d1d5db;
        border-radius: 999px;
        overflow: hidden;
        background-color: #f9fafb;
    }

    .qty-btn {
        width: 23px;
        height: 23px;
        border: none;
        border-radius: 0;
        background: transparent;
        font-size: 0.8rem;
        color: #4b5563;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background-color 0.15s ease, color 0.15s ease;
    }

    .qty-btn:hover {
        background-color: #e5e7eb;
        color: #111827;
    }

    .qty-input {
        width: 34px;
        height: 23px;
        font-size: 0.75rem;
        border: none;
        border-left: 1px solid #d1d5db;
        border-right: 1px solid #d1d5db;
        text-align: center;
        background-color: #ffffff;
        color: #111827;
    }

    .qty-input:focus {
        outline: none;
    }

    .order-footer {
        padding: 0.8rem 1rem 0.9rem;
        border-top: 1px solid #e5e7eb;
        flex-shrink: 0;
        background-color: #f9fafb;
    }

    .summary-row {
        margin-bottom: 0.25rem;
        font-size: 0.813rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .summary-total {
        padding-top: 0.5rem;
        margin-top: 0.5rem;
        font-size: 1rem;
        font-weight: 700;
        display: flex;
        justify-content: space-between;
        align-items: baseline;
    }

    /* ===================== BUTTONS ===================== */
    .btn-primary,
    .btn-secondary {
        width: 100%;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        border: none;
        cursor: pointer;
        font-weight: 600;
        white-space: nowrap;
        transition: background-color 0.18s ease, color 0.18s ease, box-shadow 0.18s ease, transform 0.1s ease, border-color 0.18s ease;
    }

    .btn-primary {
        padding: 0.75rem;
        font-size: 0.875rem;
        background-color: #f97316;
        color: #ffffff;
        box-shadow:
            0 10px 18px rgba(249, 115, 22, 0.35),
            0 1px 3px rgba(15, 23, 42, 0.25);
    }

    .btn-primary:hover {
        background-color: #ea580c;
        transform: translateY(-1px);
        box-shadow:
            0 14px 24px rgba(234, 88, 12, 0.4),
            0 2px 4px rgba(15, 23, 42, 0.35);
    }

    .btn-primary:active {
        transform: translateY(0);
        box-shadow:
            0 7px 12px rgba(249, 115, 22, 0.35),
            0 1px 3px rgba(15, 23, 42, 0.35);
    }

    .btn-secondary {
        margin-top: 0.45rem;
        padding: 0.55rem 0.75rem;
        font-size: 0.813rem;
        background-color: #ffffff;
        color: #4b5563;
        border: 1px solid #e5e7eb;
        box-shadow: 0 3px 6px rgba(15, 23, 42, 0.06);
    }

    .btn-secondary:hover {
        background-color: #f3f4f6;
        transform: translateY(-1px);
    }

    /* ===================== EMPTY STATE ===================== */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1.5rem 1.25rem;
        background-color: #f9fafb;
        border-radius: 10px;
        border: 1px dashed #d1d5db;
        color: #6b7280;
        text-align: center;
        font-size: 0.875rem;
    }

    .empty-state i {
        font-size: 1.6rem;
        color: #9ca3af;
        margin-bottom: 0.5rem;
    }

    /* ===================== PAYMENT AREA ===================== */
    .payment-view {
        animation: fadeIn 0.15s ease-out;
    }

    .payment-methods {
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }

    .payment-method {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.7rem 0.85rem;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        background-color: #ffffff;
        cursor: pointer;
        transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.1s ease, background-color 0.15s ease;
    }

    .payment-method:hover {
        border-color: #f97316;
        box-shadow:
            0 10px 14px rgba(15, 23, 42, 0.08),
            0 1px 3px rgba(15, 23, 42, 0.08);
        transform: translateY(-1px);
        background-color: #fffaf3;
    }

    .payment-icon {
        width: 38px;
        height: 38px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        flex-shrink: 0;
        background-color: #f97316;
    }

    .payment-info {
        flex: 1;
    }

    .payment-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: #111827;
    }

    .payment-subtitle {
        font-size: 0.75rem;
        color: #6b7280;
    }

    /* ===================== CALCULATOR ===================== */
    .calc-btn {
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background-color: #ffffff;
        padding: 0.6rem 0.2rem;
        font-size: 0.95rem;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.15s ease, transform 0.1s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    }

    .calc-btn:hover {
        background-color: #f3f4f6;
        border-color: #d4d4d8;
        box-shadow: 0 6px 10px rgba(15, 23, 42, 0.08);
        transform: translateY(-1px);
    }

    .calc-history-item {
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        padding: 0.5rem 0.75rem;
        background-color: #ffffff;
        cursor: pointer;
        transition: background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease, transform 0.1s ease;
        font-size: 0.78rem;
    }

    .calc-history-item:hover {
        background-color: #f3f4f6;
        border-color: #d4d4d8;
        box-shadow: 0 6px 10px rgba(15, 23, 42, 0.08);
        transform: translateY(-1px);
    }

    .calc-history-expression {
        color: #6b7280;
    }

    .calc-history-result {
        font-weight: 600;
        color: #111827;
    }

    /* ===================== TOAST ===================== */
    #toastContainer > div {
        border-radius: 8px;
        box-shadow:
            0 14px 28px rgba(15, 23, 42, 0.35),
            0 0 0 1px rgba(15, 23, 42, 0.06);
    }

    /* ===================== SCROLLBAR ===================== */
    .custom-scrollbar {
        scrollbar-width: thin;
        scrollbar-color: #d1d5db transparent;
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
        height: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 999px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    /* ===================== ANIMATIONS ===================== */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(3px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ===================== RESPONSIVE ===================== */
    @media (max-width: 1024px) {
        html,
        body {
            overflow: auto; /* Allow scroll on tablet/mobile */
        }

        main {
            height: auto;
            overflow: visible;
        }

        .pos-container {
            height: auto;
            overflow: visible;
            padding-bottom: 2rem;
        }

        .pos-main {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            padding: 0.85rem;
            height: auto;
            overflow: visible;
        }

        .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        }
    }

    @media (max-width: 640px) {
        .pos-main {
            padding: 0.5rem;
        }

        .product-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .order-header,
        .order-footer {
            padding-inline: 0.75rem;
        }
    }

/* Modal Table Styles */
#salesTableBody tr {
    border-bottom: 1px solid #e5e7eb;
}

#salesTableBody tr:hover {
    background-color: #f9fafb;
}

.badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-success {
    background-color: #d1fae5;
    color: #065f46;
}

.badge-danger {
    background-color: #fee2e2;
    color: #991b1b;
}

@media (max-width: 768px) {
    #salesTodayModal .max-w-6xl {
        max-width: 100%;
        margin: 0;
        border-radius: 0;
        height: 100vh;
        max-height: 100vh;
    }
    
    #salesTodayModal table {
        font-size: 0.75rem;
    }
    
    #salesTodayModal th,
    #salesTodayModal td {
        padding: 0.5rem;
    }
}
</style>
@endpush 

@section('content')
<!-- Toast Container -->


<!-- Modal: Mulai Penjualan -->
<div id="startSalesModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 lg:p-8">
        <div class="text-center mb-5">
            <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-600 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-1">Selamat Datang!</h3>
            <p class="text-gray-600 text-sm">Mulai penjualan Anda hari ini</p>
        </div>
        <div class="bg-orange-50 rounded-xl p-3 mb-5">
            <p class="text-xs sm:text-sm text-gray-700 text-center">
                <i class="fas fa-info-circle text-orange-600 mr-1.5"></i>
                Sistem akan mencatat semua transaksi Anda hari ini
            </p>
        </div>
        <div class="flex gap-2.5">
            <button onclick="declineStartSales()" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                Tidak
            </button>
            <button onclick="openOpeningAmountModal()" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-lg text-sm font-semibold hover:from-orange-600 hover:to-red-700 transition-all shadow-md hover:shadow-lg">
                Ya, Mulai
            </button>
        </div>
    </div>
</div>

<!-- Modal: Payment Success -->
<div id="paymentSuccessModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 lg:p-8">
        <div class="flex justify-center mb-5">
            <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-xl font-bold text-gray-900 text-center mb-1.5">Pembayaran Berhasil!</h3>
        <p class="text-gray-600 text-sm text-center mb-5">Transaksi telah berhasil diproses</p>
        <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl p-4 mb-5">
            <div class="space-y-1.5 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">No. Invoice:</span>
                    <span class="font-bold text-gray-900" id="successInvoiceNumber">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Tanggal:</span>
                    <span class="text-gray-900 font-medium" id="successDate">-</span>
                </div>
                <div class="flex justify-between text-base font-bold border-t border-orange-200 pt-2 mt-2">
                    <span class="text-gray-700">Total:</span>
                    <span class="text-orange-600" id="successTotal">Rp 0</span>
                </div>
                <div class="flex justify-between text-sm" id="successChangeRow" style="display: none;">
                    <span class="text-gray-600">Kembalian:</span>
                    <span class="font-bold text-green-600" id="successChange">Rp 0</span>
                </div>
            </div>
        </div>
        <div class="space-y-2.5">
            <button onclick="printReceipt()" class="w-full flex items-center justify-center gap-2.5 px-4 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl text-sm font-semibold hover:from-orange-600 hover:to-orange-700 transition-all shadow-md hover:shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span>Cetak Struk</span>
            </button>
            <button onclick="downloadReceipt()" class="w-full flex items-center justify-center gap-2.5 px-4 py-3 bg-white border-2 border-orange-200 text-orange-600 rounded-xl text-sm font-semibold hover:bg-orange-50 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Download Struk (PDF)</span>
            </button>
            <button onclick="closePaymentSuccessModal()" class="w-full px-4 py-3 bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200 transition-all">
                Selesai
            </button>
        </div>
    </div>
</div>

<div class="pos-container">

    <!-- Main Content -->
    <div class="pos-main">
        <!-- Left Panel: Products -->
        <div class="products-panel">
            <div class="products-toolbar">
                <div class="flex gap-2">
                    <div class="search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input 
                            type="text" 
                            id="searchProduct" 
                            class="search-input" 
                            placeholder="Cari produk...">
                    </div>
                    <select id="filterCategory" class="filter-select">
                        <option value="">Semua Kategori</option>
                    </select>
                </div>
                <div class="category-tabs" id="categoryTabs">
                </div>
            </div>

            <div class="products-content custom-scrollbar">
                <!-- Browse Products View -->
                <div id="view-browse">
                    <div class="product-grid" id="productGrid">
                        @forelse($products as $product)
                        <div class="product-card"
                            data-product-id="{{ $product->id }}"
                            data-product-name="{{ $product->name }}"
                            data-product-code="{{ $product->code }}"
                            data-product-price="{{ $product->selling_price }}"
                            data-product-hpp="{{ $product->hpp }}"
                            data-category="{{ $product->category_id }}"
                            onclick="addProductToCart(this)">
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="product-image">
                            @else
                                <div class="product-placeholder">
                                    <i class="fas fa-utensils text-white text-2xl"></i>
                                </div>
                            @endif
                            <div class="product-name">{{ $product->name }}</div>
                            <div class="product-price">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</div>
                            @if($product->track_stock)
                                @php
                                    $stock = $product->stocks->where('outlet_id', auth()->user()->outlet_id)->first();
                                    $stockQty = $stock ? $stock->quantity : 0;
                                @endphp
                                <div class="product-stock {{ $stockQty > 0 ? 'text-green-600' : 'text-red-600' }}" data-product-id="{{ $product->id }}">
                                    Stok: <span class="stock-qty">{{ number_format($stockQty, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>
                        @empty
                        <div class="empty-state" style="grid-column: 1/-1;">
                            <i class="fas fa-box-open"></i>
                            <p>Belum ada produk tersedia</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Payment Selection View -->
                <div id="view-select" class="hidden payment-view">
                    <button onclick="backToBrowse()" class="flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-4 text-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </button>
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Pilih Metode Pembayaran</h3>
                    <div class="payment-methods">
                        <div class="payment-method" onclick="setUIState('cash')">
                            <div class="payment-icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div class="payment-info">
                                <div class="payment-title">Tunai</div>
                                <div class="payment-subtitle">Pembayaran cash</div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </div>
                        <div class="payment-method" onclick="setUIState('transfer')">
                            <div class="payment-icon">
                                <i class="fas fa-building-columns"></i>
                            </div>
                            <div class="payment-info">
                                <div class="payment-title">Transfer</div>
                                <div class="payment-subtitle">Transfer Bank / QR</div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </div>
                        <div class="payment-method" onclick="setUIState('midtrans')">
                            <div class="payment-icon">
                                <i class="fas fa-qrcode"></i>
                            </div>
                            <div class="payment-info">
                                <div class="payment-title">Midtrans</div>
                                <div class="payment-subtitle">QRIS, E-Wallet, VA</div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Cash Payment View -->
                <div id="view-cash" class="hidden payment-view">
                    <button onclick="setUIState('select')" class="flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-4 text-sm">
                        <i class="fas fa-arrow-left"></i> Kembali ke Metode
                    </button>
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Pembayaran Tunai</h3>
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4">
                        <p class="text-xs text-gray-600 mb-1">Total:</p>
                        <p class="text-2xl font-bold text-green-600" id="cashTotal">Rp 0</p>
                    </div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Uang:</label>
                    <input type="number" id="cashPaidAmount" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent text-base font-semibold mb-4 qty-input" placeholder="0" onkeyup="calculateChange()">
                    <div class="p-4 bg-gray-50 rounded-xl mb-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Kembalian:</span>
                            <span class="text-lg font-bold text-green-600" id="changeAmount">Rp 0</span>
                        </div>
                    </div>
                    <button onclick="processCashPayment()" class="btn-primary">
                        <i class="fas fa-check-circle"></i>
                        Proses Pembayaran
                    </button>
                </div>

                <!-- Transfer Payment View -->
                <div id="view-transfer" class="hidden payment-view">
                    <button onclick="setUIState('select')" class="flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-4 text-sm">
                        <i class="fas fa-arrow-left"></i> Kembali ke Metode
                    </button>
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Pembayaran Transfer</h3>
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
                        <p class="text-xs text-gray-600 mb-1">Total:</p>
                        <p class="text-2xl font-bold text-blue-600" id="transferTotal">Rp 0</p>
                    </div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Metode Transfer:</label>
                    <select id="transferMethod" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent mb-4 text-sm">
                        <option value="">-- Pilih --</option>
                        <option value="BCA">Bank BCA</option>
                        <option value="BRI">Bank BRI</option>
                        <option value="BNI">Bank BNI</option>
                        <option value="Mandiri">Bank Mandiri</option>
                        <option value="QRIS">QRIS</option>
                        <option value="GoPay">GoPay</option>
                        <option value="OVO">OVO</option>
                        <option value="DANA">DANA</option>
                    </select>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">No. Referensi (Opsional):</label>
                    <input type="text" id="transferReference" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent mb-4 text-sm" placeholder="Nomor referensi">
                    <button onclick="processTransferPayment()" class="btn-primary">
                        <i class="fas fa-check-circle"></i>
                        Konfirmasi Pembayaran
                    </button>
                </div>

                <!-- Midtrans Payment View -->
                <div id="view-midtrans" class="hidden payment-view">
                    <button onclick="setUIState('select')" class="flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-4 text-sm">
                        <i class="fas fa-arrow-left"></i> Kembali ke Metode
                    </button>
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Pembayaran via Midtrans</h3>
                    <div class="bg-purple-50 border border-purple-200 rounded-xl p-4 mb-4">
                        <p class="text-xs text-gray-600 mb-1">Total:</p>
                        <p class="text-2xl font-bold text-purple-600" id="midtransTotal">Rp 0</p>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Klik tombol di bawah untuk membuka Snap (QRIS / E-Wallet / VA).</p>
                    <button onclick="openMidtransPayment()" class="btn-primary">
                        <i class="fas fa-qrcode"></i>
                        Bayar via Midtrans
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Panel: Order Summary -->
        <div class="order-panel">
            <div class="order-header flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Ringkasan Pesanan</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Total Item: <span id="totalItems" class="font-semibold text-gray-900">0</span></p>
                </div>

                <div class="flex-shrink-0 relative">
                    <!-- Dropdown Button -->
                    <button onclick="togglePOSMenu()" class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-xs font-semibold hover:bg-gray-200 transition-all flex items-center gap-1.5">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div id="posDropdownMenu" class="hidden absolute right-0 top-full mt-2 bg-white rounded-lg shadow-xl border border-gray-200 z-50 min-w-[200px] overflow-hidden">
                        <!-- Buka/Tutup Toko -->
                        <button onclick="handleCloseCashRegister()" id="menuCloseCashRegister" class="hidden w-full px-4 py-2.5 text-left text-sm hover:bg-red-50 transition-colors flex items-center gap-2 text-red-600 font-medium border-b border-gray-100">
                            <i class="fas fa-sign-out-alt w-4"></i>
                            <span>Tutup Toko</span>
                        </button>
                        <button onclick="openOpeningAmountModal(); togglePOSMenu();" id="menuOpenCashRegister" class="hidden w-full px-4 py-2.5 text-left text-sm hover:bg-green-50 transition-colors flex items-center gap-2 text-green-600 font-medium border-b border-gray-100">
                            <i class="fas fa-door-open w-4"></i>
                            <span>Buka Toko</span>
                        </button>
                        
                        <!-- Penjualan Hari Ini -->
                        <button onclick="openSalesTodayModal(); togglePOSMenu();" class="w-full px-4 py-2.5 text-left text-sm hover:bg-indigo-50 transition-colors flex items-center gap-2 text-gray-700 border-b border-gray-100">
                            <i class="fas fa-chart-line w-4 text-indigo-600"></i>
                            <span>Penjualan Hari Ini</span>
                        </button>
                        
                        <!-- Kalkulator -->
                        <button onclick="openCalculator(); togglePOSMenu();" class="w-full px-4 py-2.5 text-left text-sm hover:bg-purple-50 transition-colors flex items-center gap-2 text-gray-700 0 border-b border-gray-100">
                            <i class="fas fa-calculator w-4 text-purple-600"></i>
                            <span>Kalkulator</span>
                        </button>

                        <button onclick="openProductSettingsModal(); togglePOSMenu();" class="w-full px-4 py-2.5 text-left text-sm hover:bg-orange-50 transition-colors flex items-center gap-2 text-gray-700 border-b border-gray-100">
                            <i class="fas fa-cog w-4 text-orange-600"></i>
                            <span>Atur Produk</span>
                        </button>

                         <!-- Penjualan Hari Ini -->
                        <a href="{{ route('dashboard') }}" class="block w-full px-4 py-2.5 text-left text-sm hover:bg-indigo-50 transition-colors flex items-center gap-2 text-gray-700 border-b border-gray-100">
                            <i class="fas fa-list w-4 text-indigo-600"></i>
                            <span>Menu</span>
                        </a>
                    </div>
                </div>

            </div>

            <div class="order-items custom-scrollbar">
                <div id="cartItemsPreview">
                    <div class="empty-state" id="emptyCartPreview">
                        <i class="fas fa-shopping-cart"></i>
                        <p>Keranjang kosong</p>
                    </div>
                </div>
            </div>

            <div class="order-footer">
                <div class="summary-row">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-semibold text-gray-900" id="summarySubtotal">Rp 0</span>
                </div>
                <div class="summary-row">
                    <span class="text-gray-600">Diskon:</span>
                    <span class="font-semibold text-red-600" id="summaryDiscount">- Rp 0</span>
                </div>
                <div class="summary-total">
                    <span class="text-gray-900">Total:</span>
                    <span class="text-gray-900" id="summaryGrandTotal">Rp 0</span>
                </div>

                <div id="actionsControls" class="mt-4">
                    <button id="btnBayar" onclick="showPaymentSelection()" class="btn-primary">
                        <i class="fas fa-credit-card"></i>
                        <span>Bayar Sekarang</span>
                    </button>
                    <button id="btnClearCart" onclick="clearCart()" class="btn-secondary text-red-600 hover:bg-red-50">
                        <i class="fas fa-trash-alt mr-2"></i>
                        Kosongkan Keranjang
                    </button>
                </div>

                <div id="actionsPayflowSummary" class="hidden mt-4">
                    <div class="w-full px-4 py-3 bg-orange-50 text-orange-700 rounded-xl text-sm font-semibold flex items-center justify-between">
                        <span id="payflowSummaryLabel">0 item · Rp 0</span>
                        <button class="text-xs underline hover:no-underline" onclick="backToBrowse()">Ubah Pesanan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Input Modal Awal -->
<div id="openingAmountModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 lg:p-8">
        <div class="text-center mb-5">
            <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-600 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-1">Modal Awal</h3>
            <p class="text-gray-600 text-sm">Masukkan jumlah uang modal awal untuk hari ini</p>
        </div>
        <div class="bg-orange-50 rounded-xl p-3 mb-5">
            <p class="text-xs sm:text-sm text-gray-700 text-center">
                <i class="fas fa-info-circle text-orange-600 mr-1.5"></i>
                Modal awal adalah uang tunai yang Anda bawa untuk memulai penjualan hari ini
            </p>
        </div>
        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Modal Awal (Rp):</label>
            <input 
                type="number" 
                id="openingAmountInput" 
                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent text-base font-semibold" 
                placeholder="0"
                min="0"
                step="1000"
                onkeypress="if(event.key==='Enter') submitOpeningAmount()">
            <p class="text-xs text-gray-500 mt-2">
                <i class="fas fa-lightbulb text-yellow-500 mr-1"></i>
                Contoh: Rp 100.000 untuk kembalian dan pengeluaran tak terduga
            </p>
        </div>
        <div class="flex gap-2.5">
            <button onclick="skipOpeningAmount()" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                Lewati (Rp 0)
            </button>
            <button onclick="submitOpeningAmount()" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-lg text-sm font-semibold hover:from-orange-600 hover:to-red-700 transition-all shadow-md hover:shadow-lg">
                Simpan & Mulai
            </button>
        </div>
    </div>
</div>

<!-- Modal: Kalkulator -->
<div id="calculatorModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 max-h-[90vh] overflow-hidden flex flex-col">
        <div class="flex items-center justify-between mb-4 flex-shrink-0">
            <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-calculator text-orange-600"></i>
                Kalkulator
            </h3>
            <button onclick="closeCalculator()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 flex-1 overflow-hidden">
            <!-- Calculator -->
            <div class="lg:col-span-3">
                <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl p-4 border border-orange-200">
                    <!-- Display -->
                    <div class="bg-white rounded-lg p-4 mb-4 shadow-inner">
                        <div class="text-right text-sm text-gray-500 h-6" id="calcExpression"></div>
                        <div class="text-right text-3xl font-bold text-gray-900 break-all" id="calcDisplay">0</div>
                    </div>
                    
                    <!-- Buttons -->
                    <div class="grid grid-cols-4 gap-2">
                        <button onclick="calcClear()" class="calc-btn bg-red-500 hover:bg-red-600 text-white col-span-2 font-semibold">C</button>
                        <button onclick="calcDelete()" class="calc-btn bg-orange-500 hover:bg-orange-600 text-white font-semibold">DEL</button>
                        <button onclick="calcOperator('/')" class="calc-btn bg-orange-500 hover:bg-orange-600 text-white text-xl">÷</button>
                        
                        <button onclick="calcNumber('7')" class="calc-btn">7</button>
                        <button onclick="calcNumber('8')" class="calc-btn">8</button>
                        <button onclick="calcNumber('9')" class="calc-btn">9</button>
                        <button onclick="calcOperator('*')" class="calc-btn bg-orange-500 hover:bg-orange-600 text-white text-xl">×</button>
                        
                        <button onclick="calcNumber('4')" class="calc-btn">4</button>
                        <button onclick="calcNumber('5')" class="calc-btn">5</button>
                        <button onclick="calcNumber('6')" class="calc-btn">6</button>
                        <button onclick="calcOperator('-')" class="calc-btn bg-orange-500 hover:bg-orange-600 text-white text-xl">−</button>
                        
                        <button onclick="calcNumber('1')" class="calc-btn">1</button>
                        <button onclick="calcNumber('2')" class="calc-btn">2</button>
                        <button onclick="calcNumber('3')" class="calc-btn">3</button>
                        <button onclick="calcOperator('+')" class="calc-btn bg-orange-500 hover:bg-orange-600 text-white text-xl">+</button>
                        
                        <button onclick="calcNumber('0')" class="calc-btn col-span-2">0</button>
                        <button onclick="calcDecimal()" class="calc-btn">.</button>
                        <button onclick="calcEquals()" class="calc-btn bg-orange-600 hover:bg-orange-700 text-white font-semibold text-xl">=</button>
                    </div>
                </div>
            </div>
            
            <!-- History -->
            <div class="lg:col-span-2 flex flex-col overflow-hidden">
                <div class="flex items-center justify-between mb-3 flex-shrink-0">
                    <h4 class="text-sm font-bold text-gray-700">Riwayat</h4>
                    <button onclick="calcClearHistory()" class="text-xs text-red-600 hover:text-red-700 font-medium">
                        <i class="fas fa-trash mr-1"></i>Hapus Semua
                    </button>
                </div>
                <div id="calcHistory" class="flex-1 overflow-y-auto space-y-2 custom-scrollbar">
                    <div class="text-center text-gray-400 text-sm py-8">
                        <i class="fas fa-history text-3xl mb-2 opacity-50"></i>
                        <p>Belum ada riwayat</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Atur Produk -->
<div id="productSettingsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 flex-shrink-0">
            <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-cog text-orange-600"></i>
                Pengaturan
            </h3>
            <button onclick="closeProductSettingsModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">
            <!-- Toggle: Sembunyikan Stok Habis -->
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
                <div class="flex-1">
                    <div class="font-semibold text-gray-900 mb-1">Sembunyikan Stok Habis</div>
                    <div class="text-sm text-gray-600">Produk dengan stok 0 tidak akan ditampilkan</div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="hideOutOfStock" class="sr-only peer" onchange="applyProductSettings()">
                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                </label>
            </div>

            <!-- Select: Urutkan Produk -->
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                <label class="block font-semibold text-gray-900 mb-3">Urutkan Produk</label>
                <select id="sortProducts" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm" onchange="applyProductSettings()">
                    <option value="default">Default (ID)</option>
                    <option value="name-asc">Nama (A-Z)</option>
                    <option value="name-desc">Nama (Z-A)</option>
                    <option value="price-asc">Harga Termurah</option>
                    <option value="price-desc">Harga Termahal</option>
                    <option value="discount">Produk Diskon</option>
                </select>
            </div>

            <!-- Toggle: Sembunyikan Beberapa Produk -->
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <div class="font-semibold text-gray-900">Sembunyikan Beberapa Produk</div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="enableProductHiding" class="sr-only peer" onchange="toggleProductListVisibility()">
                        <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                    </label>
                </div>
                
                <!-- Product List (Hidden by default) -->
                <div id="productVisibilityList" class="hidden mt-4 space-y-2 max-h-64 overflow-y-auto custom-scrollbar">
                    @foreach($products as $product)
                    <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200 hover:border-orange-300 transition-colors">
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-gray-900 truncate">{{ $product->name }}</div>
                            <div class="text-xs text-gray-500">{{ $product->code }}</div>
                            @if($product->track_stock)
                                @php
                                    $stock = $product->stocks->where('outlet_id', auth()->user()->outlet_id)->first();
                                    $stockQty = $stock ? $stock->quantity : 0;
                                @endphp
                                <div class="text-xs {{ $stockQty > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    Stok: {{ number_format($stockQty, 0, ',', '.') }}
                                </div>
                            @endif
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer ml-3">
                            <input 
                                type="checkbox" 
                                class="sr-only peer product-visibility-toggle" 
                                data-product-id="{{ $product->id }}"
                                {{ $product->is_active ? 'checked' : '' }}
                                onchange="toggleProductVisibility({{ $product->id }}, this.checked)">
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="p-6 border-t border-gray-200 flex gap-3 flex-shrink-0">
            <button onclick="resetProductSettings()" class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                <i class="fas fa-undo mr-2"></i>Reset
            </button>
            <button onclick="closeProductSettingsModal()" class="flex-1 px-4 py-3 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-xl font-semibold hover:from-orange-600 hover:to-red-700 transition-all shadow-md">
                <i class="fas fa-check mr-2"></i>Selesai
            </button>
        </div>
    </div>
</div>

<!-- Modal: Penjualan Hari Ini -->
<div id="salesTodayModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 flex-shrink-0">
            <div>
                <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-chart-line text-orange-600"></i>
                    Penjualan Hari Ini
                </h3>
                <p class="text-sm text-gray-600 mt-1" id="salesTodayDate">-</p>
            </div>
            <button onclick="closeSalesTodayModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-6 bg-gray-50 border-b border-gray-200 flex-shrink-0">
            <div class="bg-white rounded-lg p-4 border border-gray-200">
                <div class="text-xs text-gray-600 mb-1">Total Transaksi</div>
                <div class="text-xl font-bold text-gray-900" id="modalTotalTransactions">0</div>
            </div>
            <div class="bg-white rounded-lg p-4 border border-gray-200">
                <div class="text-xs text-gray-600 mb-1">Total Pendapatan</div>
                <div class="text-xl font-bold text-green-600" id="modalTotalRevenue">Rp 0</div>
            </div>
            <div class="bg-white rounded-lg p-4 border border-gray-200">
                <div class="text-xs text-gray-600 mb-1">Tunai</div>
                <div class="text-lg font-bold text-blue-600" id="modalCashTotal">Rp 0</div>
            </div>
            <div class="bg-white rounded-lg p-4 border border-gray-200">
                <div class="text-xs text-gray-600 mb-1">Non-Tunai</div>
                <div class="text-lg font-bold text-purple-600" id="modalNonCashTotal">Rp 0</div>
            </div>
        </div>
        
        <!-- Table Container -->
        <div class="flex-1 overflow-auto p-6 custom-scrollbar">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">No. Invoice</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Waktu</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Kasir</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Metode</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">Total Diskon</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">Total</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">Status</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="salesTableBody">
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                                <p>Memuat data...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="p-6 border-t border-gray-200 flex justify-end gap-3 flex-shrink-0">
            <a href="{{ route('sales.index') }}" class="px-6 py-3 bg-orange-500 text-white rounded-xl font-semibold hover:bg-orange-600 transition-colors flex items-center gap-2">
                <i class="fas fa-external-link-alt"></i>
                Lihat Semua Penjualan
            </a>
            <button onclick="closeSalesTodayModal()" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Modal: Detail Penjualan -->
<div id="saleDetailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 flex-shrink-0">
            <div>
                <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-receipt text-orange-600"></i>
                    Detail Penjualan
                </h3>
                <p class="text-sm text-gray-600 mt-1" id="detailInvoiceNumber">-</p>
            </div>
            <button onclick="closeSaleDetailModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
            <!-- Info Pelanggan & Kasir -->
            <div class="grid grid-cols-2 gap-4 mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100">
                <div>
                    <div class="text-xs text-gray-500 mb-1">Kasir</div>
                    <div class="font-semibold text-gray-900" id="detailCashier">-</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Pelanggan</div>
                    <div class="font-semibold text-gray-900" id="detailCustomer">-</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Waktu</div>
                    <div class="font-semibold text-gray-900" id="detailTime">-</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 mb-1">Metode Pembayaran</div>
                    <div class="font-semibold text-gray-900" id="detailPaymentMethod">-</div>
                </div>
            </div>

            <!-- Daftar Item -->
            <h4 class="font-bold text-gray-900 mb-3">Item Pembelian</h4>
            <div class="border rounded-xl overflow-hidden mb-6">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left text-gray-600">Produk</th>
                            <th class="px-4 py-2 text-center text-gray-600">Qty</th>
                            <th class="px-4 py-2 text-right text-gray-600">Harga</th>
                            <th class="px-4 py-2 text-right text-gray-600">Total</th>
                        </tr>
                    </thead>
                    <tbody id="detailItemsBody">
                        <!-- Items will be populated here -->
                    </tbody>
                </table>
            </div>

            <!-- Ringkasan Pembayaran -->
            <div class="space-y-2 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span>
                    <span id="detailSubtotal">Rp 0</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Pajak</span>
                    <span id="detailTax">Rp 0</span>
                </div>
                <div class="flex justify-between text-red-600">
                    <span>Diskon</span>
                    <span id="detailDiscount">-Rp 0</span>
                </div>
                <div class="flex justify-between text-lg font-bold text-gray-900 pt-2 border-t">
                    <span>Total Akhir</span>
                    <span id="detailGrandTotal">Rp 0</span>
                </div>
                <div class="flex justify-between text-gray-600 pt-2">
                    <span>Bayar</span>
                    <span id="detailPaid">Rp 0</span>
                </div>
                <div class="flex justify-between text-green-600 font-semibold">
                    <span>Kembalian</span>
                    <span id="detailChange">Rp 0</span>
                </div>
            </div>
        </div>

        <div class="p-6 border-t border-gray-200 flex justify-end gap-3 flex-shrink-0 bg-gray-50">
            <button onclick="closeSaleDetailModal()" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors shadow-sm">
                Tutup
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
// ==================== GLOBAL VARIABLES ====================
let UI_STATE = 'browse';
let cart = @json($cart ?? []);
let cartSummary = @json($cartSummary) || { subtotal: 0, total_discount: 0, tax: 0, grand_total: 0, total_items: 0 };
let categories = @json($categories ?? []);
let activeDiscountPlan = @json($activeDiscountPlan ?? null);
let availableDiscounts = [];
let calcCurrentValue = '0';
let calcPreviousValue = '';
let calcOperation = null;
let calcHistory = [];
let currentSaleId = null;

document.addEventListener('DOMContentLoaded', function() {
    loadCalcHistory();
    checkCashRegister();
    renderCart();
    setUIState('browse');
    loadProductSettings();
    if (productSettings.hideOutOfStock || productSettings.sortBy !== 'default' || productSettings.hiddenProducts.length > 0) {
        applyProductSettings();
    }
    
    renderCategoryTabs();
    renderCategoryDropdown();
    initCategoryHandlers();
    initClickOutsideHandler();
    loadAvailableDiscounts();
    initDiscountUI();
});

// ==================== CATEGORY FUNCTIONS ====================
function renderCategoryTabs() {
    const tabsContainer = document.getElementById('categoryTabs');
    if (!tabsContainer) return;
    
    let html = `<button class="category-tab active" data-category="">
        <i class="fas fa-th-large mr-1.5"></i>Semua
    </button>`;
    
    categories.forEach(cat => {
        const icon = cat.icon || 'fa-folder';
        html += `<button class="category-tab" data-category="${cat.id}">
            <i class="fas ${icon} mr-1.5"></i>${cat.name}
        </button>`;
    });
    
    tabsContainer.innerHTML = html;
}

function renderCategoryDropdown() {
    const dropdown = document.getElementById('filterCategory');
    if (!dropdown) return;
    
    let html = '<option value="">Semua Kategori</option>';
    categories.forEach(cat => {
        html += `<option value="${cat.id}">${cat.name}</option>`;
    });
    
    dropdown.innerHTML = html;
}

function initCategoryHandlers() {
    // Klik tab kategori
    document.addEventListener('click', function(e) {
        const tab = e.target.closest('.category-tab');
        if (!tab) return;
        
        document.querySelectorAll('.category-tab').forEach(t => {
            t.classList.remove('active');
        });
        tab.classList.add('active');
        
        const categoryId = tab.dataset.category;
        const searchTerm = document.getElementById('searchProduct')?.value.toLowerCase() || '';
        
        const dropdown = document.getElementById('filterCategory');
        if (dropdown) dropdown.value = categoryId;
        
        filterProducts(searchTerm, categoryId);
    });
    
    // Dropdown kategori
    const dropdown = document.getElementById('filterCategory');
    if (dropdown) {
        dropdown.addEventListener('change', function() {
            const categoryId = this.value;
            const searchTerm = document.getElementById('searchProduct')?.value.toLowerCase() || '';
            
            document.querySelectorAll('.category-tab').forEach(tab => {
                if (tab.dataset.category === categoryId) {
                    tab.classList.add('active');
                } else {
                    tab.classList.remove('active');
                }
            });
            
            filterProducts(searchTerm, categoryId);
        });
    }
    
    // Pencarian
    const searchInput = document.getElementById('searchProduct');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const categoryId = document.getElementById('filterCategory')?.value || '';
            filterProducts(searchTerm, categoryId);
        });
    }
}

function filterProducts(searchTerm, categoryId) {
    const productCards = document.querySelectorAll('.product-card');
    const productGrid = document.getElementById('productGrid');
    let visibleCount = 0;
    
    productCards.forEach(card => {
        const productName = (card.dataset.productName || '').toLowerCase();
        const productCode = (card.dataset.productCode || '').toLowerCase();
        const productCategory = card.dataset.category || '';
        
        const matchesSearch = !searchTerm || 
            productName.includes(searchTerm) || 
            productCode.includes(searchTerm);
        
        const matchesCategory = !categoryId || productCategory == categoryId;
        
        if (matchesSearch && matchesCategory) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    updateEmptyState(productGrid, visibleCount, searchTerm, categoryId);
}

function updateEmptyState(productGrid, visibleCount, searchTerm, categoryId) {
    const existingEmpty = productGrid.querySelector('.empty-state-filter');
    if (existingEmpty) existingEmpty.remove();
    
    if (visibleCount === 0) {
        const emptyDiv = document.createElement('div');
        emptyDiv.className = 'empty-state empty-state-filter';
        emptyDiv.style.gridColumn = '1/-1';
        
        let message = 'Produk tidak ditemukan';
        if (searchTerm && categoryId) {
            const categoryName = getCategoryName(categoryId);
            message = `Tidak ada produk "${searchTerm}" di kategori ${categoryName}`;
        } else if (searchTerm) {
            message = `Tidak ada produk dengan kata kunci "${searchTerm}"`;
        } else if (categoryId) {
            const categoryName = getCategoryName(categoryId);
            message = `Tidak ada produk di kategori ${categoryName}`;
        }
        
        emptyDiv.innerHTML = `
            <i class="fas fa-search"></i>
            <p>${message}</p>
            ${(searchTerm || categoryId) ? `
                <button onclick="clearFilters()" class="mt-3 px-4 py-2 bg-indigo-500 text-white rounded-lg text-sm hover:bg-indigo-600 transition-colors">
                    Hapus Filter
                </button>
            ` : ''}
        `;
        productGrid.appendChild(emptyDiv);
    }
}

function getCategoryName(categoryId) {
    const category = categories.find(cat => cat.id == categoryId);
    return category ? category.name : 'Kategori';
}

function clearFilters() {
    const searchInput = document.getElementById('searchProduct');
    if (searchInput) searchInput.value = '';
    
    const dropdown = document.getElementById('filterCategory');
    if (dropdown) dropdown.value = '';
    
    document.querySelectorAll('.category-tab').forEach(tab => {
        if (tab.dataset.category === '') {
            tab.classList.add('active');
        } else {
            tab.classList.remove('active');
        }
    });
    
    filterProducts('', '');
    showToast('info', 'Filter dihapus');
}

// ==================== DISCOUNT FUNCTIONS ====================
function loadAvailableDiscounts() {
    fetch("{{ route('pos.discounts.available') }}")
        .then(async (r) => {
            if (!r.ok) {
                throw new Error('HTTP ' + r.status);
            }
            return r.json();
        })
        .then((data) => {
            if (data.success) {
                // Filter hanya non-voucher untuk badge
                availableDiscounts = (data.discounts || []).filter(d => !d.is_voucher);
                renderDiscountBadges();
            }
        })
        .catch(err => {
            console.error('Failed to load discounts:', err);
        });
}


function renderDiscountBadges() {
    document.querySelectorAll('.product-card').forEach(card => {
        const productId = parseInt(card.dataset.productId);
        const categoryId = parseInt(card.dataset.category);
        
        const discounts = availableDiscounts.filter(d => {
            if (!d.can_apply) return false;
            if (d.product_id && d.product_id === productId) return true;
            if (d.category_id && d.category_id === categoryId) return true;
            if (!d.product_id && !d.category_id) return true;
            return false;
        });
                
        if (discounts.length > 0) {
            const discount = discounts[0];
            const badge = createDiscountBadge(discount);
            
            const existing = card.querySelector('.discount-badge');
            if (existing) existing.remove();
            
            card.appendChild(badge);
        }
    });
}

function createDiscountBadge(discount) {
    const badge = document.createElement('div');
    badge.className = 'discount-badge';
    
    let text = '';
    let color = 'bg-red-500';
    
    switch(discount.type) {
        case 'percentage':
            text = `-${formatNumber(discount.value)}%`;
            break;
        case 'fixed':
            text = `-Rp ${formatNumber(discount.value)}`;
            color = 'bg-orange-500';
            break;
        case 'buy_x_get_y':
            text = `Beli ${discount.buy_quantity} Gratis ${discount.get_quantity}`;
            color = 'bg-green-500';
            break;
    }
    
    badge.innerHTML = `
        <div class="${color} text-white px-2 py-1 rounded-md text-xs font-bold shadow-md">
            ${text}
        </div>
    `;
    
    return badge;
}

function applyDiscount(discountCode = null) {
    const payload = discountCode ? { discount_code: discountCode } : {};
    
    fetch('/pos/discounts/apply', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            activeDiscountPlan = data.discount_plan;
            cartSummary = data.cart_summary;
            
            renderCart();
            
            if (data.discount_plan.requires_free_item_selection) {
                showFreeItemSelectionModal(data.discount_plan);
            } else {
                showToast('success', `Diskon ${data.discount_plan.discount_name} diterapkan`);
            }
        } else {
            showToast('error', data.message);
        }
    })
    .catch(() => showToast('error', 'Gagal menerapkan diskon'));
}

// ====== FREE ITEM (BOGO) MODAL ======
function showFreeItemSelectionModal(discountPlan) {
    const modal = document.getElementById('freeItemModal') || createFreeItemModal();
    
    activeDiscountPlan = discountPlan;
    
    document.getElementById('freeItemQuota').textContent = discountPlan.free_item_quota;
    document.getElementById('freeItemsRemaining').textContent = discountPlan.free_item_quota;
    
    const candidatesList = document.getElementById('freeItemCandidates');
    candidatesList.innerHTML = '';
    
    discountPlan.free_item_candidates.forEach(candidate => {
        const item = document.createElement('div');
        item.className = 'free-item-candidate';
        item.innerHTML = `
            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-indigo-50 transition-colors">
                <div class="flex-1">
                    <div class="font-semibold text-gray-900">${candidate.product_name}</div>
                    <div class="text-sm text-gray-600">Rp ${formatNumber(candidate.unit_price)}</div>
                    <div class="text-xs text-gray-500">Maks. gratis: ${candidate.max_free_qty}</div>
                </div>
                <div class="qty-controls">
                    <button class="qty-btn" onclick="adjustFreeQty(${candidate.product_id}, -1)">−</button>
                    <input type="number" 
                           min="0" 
                           max="${candidate.max_free_qty}"
                           value="0"
                           class="qty-input"
                           id="freeQty_${candidate.product_id}"
                           onchange="updateFreeItemsRemaining()">
                    <button class="qty-btn" onclick="adjustFreeQty(${candidate.product_id}, 1)">+</button>
                </div>
            </div>
        `;
        candidatesList.appendChild(item);
    });
    
    modal.classList.remove('hidden');
}

function createFreeItemModal() {
    const modal = document.createElement('div');
    modal.id = 'freeItemModal';
    modal.className = 'hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 max-h-[90vh] overflow-hidden flex flex-col">
            <div class="flex items-center justify-between mb-4 flex-shrink-0">
                <h3 class="text-xl font-bold text-gray-900">
                    <i class="fas fa-gift text-orange-500 mr-2"></i>
                    Pilih Item Gratis
                </h3>
                <button onclick="closeFreeItemModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <div class="bg-orange-50 border border-orange-200 rounded-xl p-3 mb-4 flex-shrink-0">
                <p class="text-sm text-orange-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    Anda berhak mendapat <span id="freeItemQuota" class="font-bold">0</span> item gratis!
                    Sisa: <span id="freeItemsRemaining" class="font-bold">0</span>
                </p>
            </div>
            
            <div id="freeItemCandidates" class="flex-1 overflow-y-auto space-y-2 custom-scrollbar mb-4">
            </div>
            
            <div class="flex gap-3 flex-shrink-0">
                <button onclick="closeFreeItemModal()" class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button onclick="confirmFreeItems()" class="flex-1 px-4 py-3 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-xl font-semibold hover:from-orange-600 hover:to-red-700 transition-all shadow-md">
                    <i class="fas fa-check mr-2"></i>
                    Konfirmasi
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    return modal;
}

function adjustFreeQty(productId, delta) {
    const input = document.getElementById(`freeQty_${productId}`);
    if (!input) return;
    const max = parseInt(input.max);
    let newVal = parseInt(input.value || '0') + delta;
    newVal = Math.max(0, Math.min(max, newVal));
    input.value = newVal;
    updateFreeItemsRemaining();
}

function updateFreeItemsRemaining() {
    if (!activeDiscountPlan) return;
    const quota = activeDiscountPlan.free_item_quota;
    let used = 0;

    document.querySelectorAll('[id^="freeQty_"]').forEach(input => {
        used += parseInt(input.value || '0') || 0;
    });

    const remaining = Math.max(0, quota - used);
    document.getElementById('freeItemsRemaining').textContent = remaining;

    if (used > quota) {
        showToast('warning', 'Jumlah melebihi kuota gratis!');
    }
}

function confirmFreeItems() {
    const freeItems = [];
    document.querySelectorAll('[id^="freeQty_"]').forEach(input => {
        const qty = parseInt(input.value || '0') || 0;
        if (qty > 0) {
            const productId = input.id.replace('freeQty_', '');
            freeItems.push({
                product_id: parseInt(productId),
                quantity: qty
            });
        }
    });

    if (freeItems.length === 0) {
        showToast('warning', 'Pilih minimal 1 item gratis');
        return;
    }

    fetch('/pos/discounts/assign-free-items', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ free_items: freeItems })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            activeDiscountPlan = data.discount_plan;
            cartSummary = data.cart_summary;
            
            closeFreeItemModal();
            renderCart();
            
            showToast('success', 'Item gratis berhasil diterapkan!');
        } else {
            showToast('error', data.message);
        }
    })
    .catch(() => showToast('error', 'Gagal memproses item gratis'));
}

function closeFreeItemModal() {
    const modal = document.getElementById('freeItemModal');
    if (modal) modal.classList.add('hidden');
}

function clearDiscount() {
    if (!activeDiscountPlan) return;
    
    Swal.fire({
        title: 'Hapus Diskon?',
        text: "Diskon yang diterapkan akan dihapus",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        backdrop: 'rgba(0, 0, 0, 0.5)'
    }).then((result) => {
        if (result.isConfirmed) {

    fetch('/pos/discounts/clear', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            activeDiscountPlan = null;
            cartSummary = data.cart_summary;
            renderCart();
            showToast('success', 'Diskon dihapus');
        }
    })
        }
    });
}

function initDiscountUI() {
    const actions = document.getElementById('actionsControls');
    if (!actions || document.getElementById('btnDiscountCode')) return;
    const btn = document.createElement('button');
    btn.id = 'btnDiscountCode';
    btn.className = 'btn-secondary mb-2';
    btn.innerHTML = '<i class="fas fa-ticket-alt mr-2"></i>Gunakan Voucher';
    btn.onclick = showDiscountCodeModal;

    actions.insertBefore(btn, actions.firstChild);
}

function showDiscountCodeModal() {
    const modal = document.getElementById('discountCodeModal') || createDiscountCodeModal();
    modal.classList.remove('hidden');
    setTimeout(() => document.getElementById('discountCodeInput')?.focus(), 200);
}

function createDiscountCodeModal() {
    const modal = document.createElement('div');
    modal.id = 'discountCodeModal';
    modal.className = 'hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
            <div class="text-center mb-4">
                <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-600 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-ticket-alt text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Masukkan Kode Voucher</h3> <!-- ⬅️ Ubah -->
            </div>
            <div class="mb-4">
                <input type="text" 
                    id="discountCodeInput" 
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent text-center text-lg font-semibold uppercase"
                    placeholder="KODE VOUCHER" 
                    onkeypress="if(event.key==='Enter') submitDiscountCode()">
            </div>
            
            <div class="flex gap-3">
                <button onclick="closeDiscountCodeModal()" class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50">
                    Batal
                </button>
                <button onclick="submitDiscountCode()" class="flex-1 px-4 py-3 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-lg font-semibold hover:from-orange-600 hover:to-red-700">
                    Terapkan
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    return modal;
}

function submitDiscountCode() {
    const input = document.getElementById('discountCodeInput');
    const code = input?.value.trim().toUpperCase();
    if (!code) {
        showToast('warning', 'Masukkan kode voucher');
        return;
    }

    // Langsung fetch tanpa bikin fungsi baru
    fetch('/pos/discounts/apply', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ discount_code: code })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            activeDiscountPlan = data.discount_plan;
            cartSummary = data.cart_summary;
            
            renderCart();
            
            if (data.discount_plan.requires_free_item_selection) {
                showFreeItemSelectionModal(data.discount_plan);
            } else {
                showToast('success', `Voucher ${data.discount_plan.discount_name} diterapkan`);
            }
            
            closeDiscountCodeModal();
        } else {
            showToast('error', data.message);
        }
    })
    .catch(() => showToast('error', 'Gagal menerapkan voucher'));
}

function closeDiscountCodeModal() {
    const modal = document.getElementById('discountCodeModal');
    if (modal) {
        modal.classList.add('hidden');
        const input = document.getElementById('discountCodeInput');
        if (input) input.value = '';
    }
}

// ==================== DROPDOWN MENU FUNCTIONS ====================
function togglePOSMenu() {
    const menu = document.getElementById('posDropdownMenu');
    menu.classList.toggle('hidden');
}

function initClickOutsideHandler() {
    document.addEventListener('click', function(e) {
        const menu = document.getElementById('posDropdownMenu');
        if (!menu) return;
        const isInsideMenu = menu.contains(e.target);
        const btn = e.target.closest('button');
        const isToggleButton = btn && btn.onclick && String(btn.onclick).includes('togglePOSMenu');
        if (!isInsideMenu && !isToggleButton) {
            menu.classList.add('hidden');
        }
    });
}

// ==================== CALCULATOR FUNCTIONS ====================
function openCalculator() {
    document.getElementById('calculatorModal').classList.remove('hidden');
    updateCalcDisplay();
}

function closeCalculator() {
    document.getElementById('calculatorModal').classList.add('hidden');
}

function calcNumber(num) {
    if (calcCurrentValue === '0' || calcCurrentValue === 'Error') {
        calcCurrentValue = num;
    } else {
        calcCurrentValue += num;
    }
    updateCalcDisplay();
}

function calcOperator(op) {
    if (calcPreviousValue !== '' && calcOperation !== null) {
        calcEquals();
    }
    calcPreviousValue = calcCurrentValue;
    calcOperation = op;
    calcCurrentValue = '0';
    updateCalcDisplay();
}

function calcDecimal() {
    if (!calcCurrentValue.includes('.')) {
        calcCurrentValue += '.';
        updateCalcDisplay();
    }
}

function calcEquals() {
    if (calcPreviousValue === '' || calcOperation === null) return;
    const prev = parseFloat(calcPreviousValue);
    const current = parseFloat(calcCurrentValue);
    let result;

    switch(calcOperation) {
        case '+': result = prev + current; break;
        case '-': result = prev - current; break;
        case '*': result = prev * current; break;
        case '/': 
            if (current === 0) {
                calcCurrentValue = 'Error';
                updateCalcDisplay();
                calcPreviousValue = '';
                calcOperation = null;
                return;
            }
            result = prev / current; 
            break;
        default: return;
    }

    const expression = `${prev} ${calcOperation === '*' ? '×' : calcOperation === '/' ? '÷' : calcOperation} ${current}`;
    addToCalcHistory(expression, result);

    calcCurrentValue = result.toString();
    calcPreviousValue = '';
    calcOperation = null;
    updateCalcDisplay();
}

function calcClear() {
    calcCurrentValue = '0';
    calcPreviousValue = '';
    calcOperation = null;
    updateCalcDisplay();
}

function calcDelete() {
    if (calcCurrentValue.length > 1) {
        calcCurrentValue = calcCurrentValue.slice(0, -1);
    } else {
        calcCurrentValue = '0';
    }
    updateCalcDisplay();
}

function updateCalcDisplay() {
    document.getElementById('calcDisplay').textContent = calcCurrentValue;
    let expression = '';
    if (calcPreviousValue !== '') {
        const opSymbol = calcOperation === '*' ? '×' : calcOperation === '/' ? '÷' : calcOperation;
        expression = `${calcPreviousValue} ${opSymbol}`;
    }
    document.getElementById('calcExpression').textContent = expression;
}

function addToCalcHistory(expression, result) {
    const historyItem = {
        expression: expression,
        result: result,
        timestamp: new Date().toISOString()
    };
    calcHistory.unshift(historyItem);

    if (calcHistory.length > 50) {
        calcHistory = calcHistory.slice(0, 50);
    }

    saveCalcHistory();
    renderCalcHistory();
}

function saveCalcHistory() {
    localStorage.setItem('pos_calc_history', JSON.stringify(calcHistory));
}

function loadCalcHistory() {
    const saved = localStorage.getItem('pos_calc_history');
    if (saved) {
        try {
            calcHistory = JSON.parse(saved);
            renderCalcHistory();
        } catch (e) {
            calcHistory = [];
        }
    }
}

function renderCalcHistory() {
    const container = document.getElementById('calcHistory');
    if (calcHistory.length === 0) {
        container.innerHTML = `
            <div class="text-center text-gray-400 text-sm py-8">
                <i class="fas fa-history text-3xl mb-2 opacity-50"></i>
                <p>Belum ada riwayat</p>
            </div>`;
        return;
    }

    let html = '';
    calcHistory.forEach((item, index) => {
        html += `
            <div class="calc-history-item" onclick="calcUseHistory(${index})">
                <div class="calc-history-expression">${item.expression}</div>
                <div class="calc-history-result">= ${formatNumber(item.result)}</div>
            </div>`;
    });

    container.innerHTML = html;
}

function calcUseHistory(index) {
    const item = calcHistory[index];
    calcCurrentValue = item.result.toString();
    calcPreviousValue = '';
    calcOperation = null;
    updateCalcDisplay();
}

function calcClearHistory() {
    Swal.fire({
        title: 'Hapus Riwayat?',
        text: "Semua riwayat kalkulator akan dihapus",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        backdrop: 'rgba(0, 0, 0, 0.5)'
    }).then((result) => {
        if (result.isConfirmed) {
    calcHistory = [];
    saveCalcHistory();
    renderCalcHistory();
            calcHistory = [];
            saveCalcHistory();
            renderCalcHistory();
            showToast('success', 'Riwayat berhasil dihapus');
        }
    });
}

// ==================== CASH REGISTER FUNCTIONS ====================
function checkCashRegister() {
    fetch('{{ route("cash-register.check") }}')
        .then(r => r.json())
        .then(data => {
            if (data.is_open) {
                document.getElementById('menuCloseCashRegister').classList.remove('hidden');
                document.getElementById('menuOpenCashRegister').classList.add('hidden');
            } else if (data.has_unfinished) {
                const hasDeclined = sessionStorage.getItem('pos_declined_modal');
                if (hasDeclined === 'true') {
                    document.getElementById('menuOpenCashRegister').classList.remove('hidden');
                    document.getElementById('menuCloseCashRegister').classList.add('hidden');
                } else {
                    document.getElementById('startSalesModal').classList.remove('hidden');
                }
            } else {
                const hasDeclined = sessionStorage.getItem('pos_declined_modal');
                if (hasDeclined === 'true') {
                    document.getElementById('menuOpenCashRegister').classList.remove('hidden');
                    document.getElementById('menuCloseCashRegister').classList.add('hidden');
                } else {
                    document.getElementById('startSalesModal').classList.remove('hidden');
                }
            }
        });
}

function closeStartSalesModal() {
    document.getElementById('startSalesModal').classList.add('hidden');
}

function openOpeningAmountModal() {
    document.getElementById('openingAmountModal').classList.remove('hidden');
    setTimeout(() => {
        document.getElementById('openingAmountInput').focus();
    }, 200);
}

function closeOpeningAmountModal() {
    document.getElementById('openingAmountModal').classList.add('hidden');
    document.getElementById('openingAmountInput').value = '';
}

function submitOpeningAmount() {
    const amount = parseFloat(document.getElementById('openingAmountInput').value) || 0;
    if (amount < 0) {
        showToast('warning', 'Jumlah tidak boleh negatif');
        return;
    }

    fetch('{{ route("cash-register.start") }}', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}' 
        },
        body: JSON.stringify({ opening_amount: amount })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeOpeningAmountModal();
            closeStartSalesModal();
            
            if (data.is_continued) {
                showToast('success', 'Melanjutkan sesi penjualan sebelumnya');
            } else {
                showToast('success', `Toko dibuka dengan modal awal Rp ${formatNumber(amount)}`);
            }
            
            document.getElementById('menuCloseCashRegister').classList.remove('hidden');
            document.getElementById('menuOpenCashRegister').classList.add('hidden');
            sessionStorage.removeItem('pos_declined_modal');
        } else {
            showToast('error', data.message || 'Gagal memulai sesi');
        }
    })
    .catch(() => {
        showToast('error', 'Terjadi kesalahan saat memulai sesi');
    });
}

function skipOpeningAmount() {
    Swal.fire({
        title: 'Lewati Modal Awal?',
        text: "Modal awal akan diset Rp 0. Anda yakin?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Lewati',
        cancelButtonText: 'Batal',
        backdrop: 'rgba(0, 0, 0, 0.5)'
    }).then((result) => {
        if (result.isConfirmed) {
    fetch('{{ route("cash-register.start") }}', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}' 
        },
        body: JSON.stringify({ opening_amount: 0 })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeOpeningAmountModal();
            closeStartSalesModal();
            
            if (data.is_continued) {
                showToast('success', 'Melanjutkan sesi penjualan sebelumnya');
            } else {
                showToast('success', 'Toko dibuka dengan modal awal Rp 0');
            }
            
            document.getElementById('menuCloseCashRegister').classList.remove('hidden');
            document.getElementById('menuOpenCashRegister').classList.add('hidden');
            sessionStorage.removeItem('pos_declined_modal');
        } else {
            showToast('error', data.message || 'Gagal memulai sesi');
        }
    })
    .catch(() => {
        showToast('error', 'Terjadi kesalahan');
    });
        }
    });
}

function declineStartSales() {
    closeStartSalesModal();
    document.getElementById('menuOpenCashRegister').classList.remove('hidden');
    document.getElementById('menuCloseCashRegister').classList.add('hidden');

    sessionStorage.setItem('pos_declined_modal', 'true');

    showToast('info', 'Anda bisa buka toko kapan saja dengan klik tombol "Buka Toko"');
}

function handleCloseCashRegister() {
    fetch('{{ route("cash-register.check-sales") }}', {
        method: 'GET',
        headers: { 
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}' 
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const totalSales = parseFloat(data.total_sales || 0);
            if (totalSales <= 0) {
                closeCashRegisterSilent();
            } else {
                window.location.href = '{{ route("cash-register.close") }}';
            }
        } else {
            showToast('error', 'Gagal mengecek data penjualan');
        }
    })
    .catch(() => {
        showToast('error', 'Terjadi kesalahan saat tutup toko');
    });
}

function closeCashRegisterSilent() {
    fetch('{{ route("cash-register.close-silent") }}', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}' 
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('menuCloseCashRegister').classList.add('hidden');
            document.getElementById('menuOpenCashRegister').classList.remove('hidden');
            showToast('success', 'Toko ditutup. Tidak ada penjualan di sesi ini.');
        } else {
            showToast('error', data.message || 'Gagal menutup toko');
        }
    })
    .catch(() => {
        showToast('error', 'Gagal menutup toko');
    });
}

// ==================== UTILITY FUNCTIONS ====================
function showToast(type, message) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    Toast.fire({
        icon: type,
        title: message
    });
}

function formatNumber(num){
    num = Number(num||0);
    return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function formatDateTime(s){
    const d = new Date(s);
    const day=String(d.getDate()).padStart(2,'0');
    const month=String(d.getMonth()+1).padStart(2,'0');
    const year=d.getFullYear();
    const h=String(d.getHours()).padStart(2,'0');
    const m=String(d.getMinutes()).padStart(2,'0');
    return `${day}/${month}/${year} ${h}:${m}`;
}

// ==================== CART FUNCTIONS ====================
function addProductToCart(el) {
    const isStoreOpen = !document.getElementById('menuCloseCashRegister').classList.contains('hidden');
    if (!isStoreOpen) {
        showToast('warning', 'Buka toko terlebih dahulu untuk mulai transaksi!');
        return;
    }

    const productId = el.dataset.productId;
    fetch('{{ route("pos.cart.add") }}', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body: JSON.stringify({ product_id: productId, quantity: 1 })
    })
    .then(r=>r.json())
    .then(data=>{
        if(data.success){
            cart = data.cart; 
            cartSummary = data.cart_summary;
            if (data.discount_plan !== undefined) activeDiscountPlan = data.discount_plan;
            renderCart(); 
            showToast('success','Ditambahkan ke keranjang');
        } else { 
            showToast('error', data.message); 
        }
    })
    .catch(()=>showToast('error','Terjadi kesalahan'));
}

function updateCartQuantity(cartKey, newQty) {
    let qty = parseFloat(newQty);
    if (isNaN(qty) || qty < 0) qty = 0;
    fetch('{{ route("pos.cart.update") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json','X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ cart_key: cartKey, quantity: qty })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            cart = data.cart; 
            cartSummary = data.cart_summary;
            if (data.discount_plan !== undefined) activeDiscountPlan = data.discount_plan;
            renderCart();
        } else {
            showToast('error', data.message || 'Gagal update item');
        }
    })
    .catch(() => showToast('error', 'Gagal update item'));
}

function removeCartItem(cartKey) {
    fetch('{{ route("pos.cart.remove") }}', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json','X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ cart_key: cartKey })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            cart = data.cart;
            cartSummary = data.cart_summary;
            if (data.discount_plan !== undefined) activeDiscountPlan = data.discount_plan;
            renderCart();
            showToast('success', 'Item dihapus');
        } else {
            showToast('error', data.message || 'Gagal menghapus item');
        }
    })
    .catch(() => showToast('error', 'Gagal menghapus item'));
}

function clearCart() {
    Swal.fire({
        title: 'Kosongkan Keranjang?',
        text: "Semua item di keranjang akan dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Kosongkan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
    fetch('{{ route("pos.cart.clear") }}', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}
    })
    .then(r=>r.json())
    .then(data=>{
        if(data.success){
            cart = {};
            cartSummary = { subtotal:0,total_discount:0,tax:0,grand_total:0,total_items:0 };
            activeDiscountPlan = null; // ⬅️ TAMBAHKAN INI
            renderCart();
            setUIState('browse');
            showToast('success','Keranjang dikosongkan');
        }
    })
        }
    });
}

function renderCart() {
    const preview = document.getElementById('cartItemsPreview');
    const oldDiscountInfo = document.getElementById('discountInfo');
    if (oldDiscountInfo) oldDiscountInfo.remove();

    const totalItems = Object.values(cart || {}).reduce((s,i)=>s+Number(i.quantity||0),0);
    document.getElementById('totalItems').textContent = totalItems;

    const isPayflow = (UI_STATE !== 'browse');

    if (!cart || Object.keys(cart).length === 0) {
        // PERBAIKAN: Clear discount plan jika cart kosong
        activeDiscountPlan = null;
        
        preview.innerHTML = `
            <div class="empty-state" id="emptyCartPreview">
                <i class="fas fa-shopping-cart"></i>
                <p>Keranjang kosong</p>
            </div>`;
    } else {
        let html = '';
        for (const [key, item] of Object.entries(cart)) {
            const subtotal = Number(item.subtotal || (item.unit_price * item.quantity));
            const formattedPrice = formatNumber(item.unit_price);
            const formattedSubtotal = formatNumber(subtotal);

            html += `
            <div class="order-item">
                <div class="order-item-info">
                    <div class="order-item-name" title="${item.product_name}">${item.product_name}</div>
                    <div class="order-item-price">@ Rp ${formattedPrice} = <span class="font-semibold text-indigo-600">Rp ${formattedSubtotal}</span></div>
                </div>

                ${!isPayflow 
                    ? `
                    <div class="flex items-center gap-2">
                        <div class="qty-controls">
                            <button class="qty-btn" onclick="decrementQty('${key}')">−</button>
                            <input type="number" min="0" step="1" value="${Number(item.quantity)}"
                                    class="qty-input"
                                    onblur="onQtyBlur('${key}', this.value)"
                                    onkeydown="if(event.key==='Enter'){onQtyBlur('${key}', this.value)}">
                            <button class="qty-btn" onclick="incrementQty('${key}')">+</button>
                        </div>
                        <button class="text-red-500 hover:text-red-700" title="Hapus" onclick="removeCartItem('${key}')">
                            <i class="fas fa-trash-alt text-sm"></i>
                        </button>
                    </div>
                    `
                    : `
                    <div class="text-sm font-bold text-gray-700">x${Number(item.quantity)}</div>
                    `
                }
            </div>`;
        }
        preview.innerHTML = html;
    }

    // ====== INFO DISKON DI AREA KERANJANG ======
    if (activeDiscountPlan) {
        const discountInfo = document.createElement('div');
        discountInfo.id = 'discountInfo';
        
        if (activeDiscountPlan.discount_type === 'buy_x_get_y') {
            const freeItems = activeDiscountPlan.affected_items || [];
            const totalFreeQty = freeItems.reduce((sum, item) => sum + (item.free_qty || 0), 0);
            
            discountInfo.innerHTML = `
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-3 mb-3">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="text-xs text-green-600 font-semibold mb-1">
                                <i class="fas fa-gift mr-1"></i>Promo BOGO Aktif
                            </div>
                            <div class="text-sm font-bold text-green-900">${activeDiscountPlan.discount_name || 'Buy X Get Y'}</div>
                            <div class="text-xs text-green-700 mt-1">
                                ${totalFreeQty > 0 ? `#Dapat ${totalFreeQty} item gratis` : 'Pilih item gratis Anda'}
                            </div>
                        </div>
                        <button onclick="clearDiscount()" class="text-red-500 hover:text-red-700 px-2">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
        } else if ((activeDiscountPlan.total_discount || cartSummary.total_discount || 0) > 0) {
            const saved = activeDiscountPlan.total_discount || cartSummary.total_discount || 0;
            
            discountInfo.innerHTML = `
                <div class="bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 rounded-xl p-3 mb-3">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="text-xs text-purple-600 font-semibold mb-1">
                                <i class="fas fa-tag mr-1"></i>Diskon Aktif
                            </div>
                            <div class="text-sm font-bold text-purple-900">${activeDiscountPlan.discount_name || 'Diskon' }</div>
                            <div class="text-xs text-purple-700 mt-1">
                                Hemat Rp ${formatNumber(saved)}
                            </div>
                        </div>
                        <button onclick="clearDiscount()" class="text-red-500 hover:text-red-700 px-2">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
        }
        if (preview.firstChild) {
            preview.prepend(discountInfo);
        } else {
            preview.appendChild(discountInfo);
        }
    }

    // ====== RINGKASAN DI FOOTER ======
    document.getElementById('summarySubtotal').textContent = 'Rp ' + formatNumber(cartSummary.subtotal || 0);
    document.getElementById('summaryDiscount').textContent = '- Rp ' + formatNumber(cartSummary.total_discount || 0);
    document.getElementById('summaryGrandTotal').textContent = 'Rp ' + formatNumber(cartSummary.grand_total || 0);

    document.getElementById('cashTotal').textContent = 'Rp ' + formatNumber(cartSummary.grand_total || 0);
    document.getElementById('transferTotal').textContent = 'Rp ' + formatNumber(cartSummary.grand_total || 0);
    document.getElementById('midtransTotal').textContent = 'Rp ' + formatNumber(cartSummary.grand_total || 0);

    const payLabel = `${totalItems} item · Rp ${formatNumber(cartSummary.grand_total || 0)}`;
    const labelEl = document.getElementById('payflowSummaryLabel');
    if (labelEl) labelEl.textContent = payLabel;
}

function incrementQty(cartKey) {
    const item = cart[cartKey];
    if (!item) return;
    const newQty = Number(item.quantity) + 1;
    updateCartQuantity(cartKey, newQty);
}

function decrementQty(cartKey) {
    const item = cart[cartKey];
    if (!item) return;
    const newQty = Math.max(0, Number(item.quantity) - 1);
    updateCartQuantity(cartKey, newQty);
}

function onQtyBlur(cartKey, value) {
    let v = value === '' ? 0 : value;
    updateCartQuantity(cartKey, v);
}

// ==================== UI STATE FUNCTIONS ====================
function setUIState(state) {
    UI_STATE = state;
    const views = ['browse','select','cash','transfer','midtrans'];
    views.forEach(v => {
        const el = document.getElementById(`view-${v}`);
        if (el) el.classList.add('hidden');
    });
    const active = document.getElementById(`view-${state}`);
    if (active) active.classList.remove('hidden');

    updateRightActions();
    renderCart();

    if (state === 'cash') {
        setTimeout(()=>document.getElementById('cashPaidAmount').focus(), 120);
    }
}

function updateRightActions(){
    const isPayflow = (UI_STATE !== 'browse');
    const controls = document.getElementById('actionsControls');
    const badge = document.getElementById('actionsPayflowSummary');
    if (!controls || !badge) return;

    if (isPayflow) {
        controls.classList.add('hidden');
        badge.classList.remove('hidden');
    } else {
        controls.classList.remove('hidden');
        badge.classList.add('hidden');
    }
}

function backToBrowse(){
    setUIState('browse');
}

function showPaymentSelection() {
    if (!cart || Object.keys(cart).length === 0) {
        showToast('warning','Keranjang kosong');
        return;
    }
    setUIState('select');
}

// ==================== PAYMENT FUNCTIONS ====================
function calculateChange() {
    const paid = parseFloat(document.getElementById('cashPaidAmount').value) || 0;
    const change = paid - (cartSummary.grand_total || 0);
    document.getElementById('changeAmount').textContent = 'Rp ' + formatNumber(Math.max(0, change));
}

// ==================== PAYMENT FUNCTIONS ====================
function processCashPayment() {
    const paid = parseFloat(document.getElementById('cashPaidAmount').value) || 0;
    if (paid < (cartSummary.grand_total || 0)) {
        showToast('error','Jumlah kurang');
        return;
    }
    fetch('{{ route("payment.cash") }}', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body: JSON.stringify({ paid_amount: paid })
    })
    .then(r=>r.json())
    .then(async data=>{
        if (data.success) {
            if (data.sale && data.sale.items) { 
                updateProductStockFromSaleItems(data.sale.items); 
            }
            // PERBAIKAN: Update stock dengan free items untuk BOGO
            if (activeDiscountPlan && activeDiscountPlan.discount_type === 'buy_x_get_y') {
                updateProductStockWithFreeItems(activeDiscountPlan);
            }
            
            await fetch('{{ route("pos.cart.clear") }}', { 
                method:'POST', 
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'} 
            });
            
            // PERBAIKAN: Reset semua state
            cart = {}; 
            cartSummary = { subtotal:0,total_discount:0,tax:0,grand_total:0,total_items:0 };
            activeDiscountPlan = null; // ⬅️ TAMBAHKAN INI
            
            renderCart(); 
            setUIState('browse');
            openPaymentSuccessModal({
                sale_id: data.sale.id,
                invoice_number: data.sale.invoice_number,
                created_at: data.sale.created_at,
                grand_total: data.sale.grand_total,
                change_amount: data.change
            });
        } else { 
            showToast('error', data.message); 
        }
    })
    .catch(()=>showToast('error','Gagal proses pembayaran'));
}

function processTransferPayment() {
    const method = document.getElementById('transferMethod').value;
    const ref = document.getElementById('transferReference').value;
    if (!method) {
        showToast('warning','Pilih metode transfer');
        return;
    }
    fetch('{{ route("payment.transfer") }}', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body: JSON.stringify({ transfer_method: method, reference_number: ref })
    })
    .then(r=>r.json())
    .then(async data=>{
        if (data.success) {
            if (data.sale && data.sale.items) { 
                updateProductStockFromSaleItems(data.sale.items); 
            }
            // PERBAIKAN: Update stock dengan free items untuk BOGO
            if (activeDiscountPlan && activeDiscountPlan.discount_type === 'buy_x_get_y') {
                updateProductStockWithFreeItems(activeDiscountPlan);
            }
            
            await fetch('{{ route("pos.cart.clear") }}', { 
                method:'POST', 
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'} 
            });
            
            // PERBAIKAN: Reset semua state
            cart = {}; 
            cartSummary = { subtotal:0,total_discount:0,tax:0,grand_total:0,total_items:0 };
            activeDiscountPlan = null; // ⬅️ TAMBAHKAN INI
            
            renderCart(); 
            setUIState('browse');
            openPaymentSuccessModal({
                sale_id: data.sale.id,
                invoice_number: data.sale.invoice_number,
                created_at: data.sale.created_at,
                grand_total: data.sale.grand_total
            });
        } else { 
            showToast('error', data.message); 
        }
    })
    .catch(()=>showToast('error','Gagal proses pembayaran'));
}

function openMidtransPayment() {
    if (!cart || Object.keys(cart).length === 0) {
        showToast('warning','Keranjang kosong');
        return;
    }
    fetch('{{ route("payment.midtrans.token") }}', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}
    })
    .then(r=>r.json())
    .then(data=>{
        if (!data.success) { 
            showToast('error', data.message || 'Gagal membuat token Midtrans'); 
            return; 
        }

        snap.pay(data.snap_token, {
            onSuccess: function() {
                fetch('/api/sale/' + data.sale_id)
                    .then(res=>res.json())
                    .then(async saleData=>{
                        if (saleData.items) { 
                            updateProductStockFromSaleItems(saleData.items); 
                        }
                        // PERBAIKAN: Update stock dengan free items untuk BOGO
                        if (activeDiscountPlan && activeDiscountPlan.discount_type === 'buy_x_get_y') {
                            updateProductStockWithFreeItems(activeDiscountPlan);
                        }
                        
                        await fetch('{{ route("pos.cart.clear") }}', { 
                            method:'POST', 
                            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'} 
                        });
                        
                        // PERBAIKAN: Reset semua state
                        cart = {}; 
                        cartSummary = { subtotal:0,total_discount:0,tax:0,grand_total:0,total_items:0 };
                        activeDiscountPlan = null; // ⬅️ TAMBAHKAN INI
                        
                        renderCart(); 
                        setUIState('browse');
                        openPaymentSuccessModal({
                            sale_id: saleData.id,
                            invoice_number: saleData.invoice_number,
                            created_at: saleData.created_at,
                            grand_total: saleData.grand_total
                        });
                    })
                    .catch(()=>showToast('error','Pembayaran berhasil, tapi gagal mengambil data transaksi'));
            },
            onPending: function(){ showToast('info','Menunggu pembayaran'); },
            onError: function(){ showToast('error','Pembayaran via Midtrans gagal'); },
            onClose: function(){ showToast('info','Jendela pembayaran ditutup'); }
        });
    })
    .catch(()=>showToast('error','Gagal membuat token'));
}

function updateProductStockFromSaleItems(items){
    if (!items || !items.length) return;
    items.forEach(item=>{
        const wrap = document.querySelector(`.product-stock[data-product-id="${item.product_id}"]`);
        if (!wrap) return;
        const qtySpan = wrap.querySelector('.stock-qty');
        let currentQty = parseInt((qtySpan.textContent||'0').replace(/\./g,'')) || 0;
        let newQty = currentQty - item.quantity;
        if (newQty < 0) newQty = 0;
        qtySpan.textContent = formatNumber(newQty);
        wrap.classList.toggle('text-green-600', newQty>0);
        wrap.classList.toggle('text-red-600', newQty<=0);
    });
}

// PERBAIKAN: Fungsi baru untuk update stock dengan free items BOGO
function updateProductStockWithFreeItems(discountPlan) {
    if (!discountPlan || discountPlan.discount_type !== 'buy_x_get_y') return;
    if (!discountPlan.affected_items || !discountPlan.affected_items.length) return;
    
    discountPlan.affected_items.forEach(item => {
        const freeQty = item.free_qty || 0;
        if (freeQty <= 0) return;
        
        const wrap = document.querySelector(`.product-stock[data-product-id="${item.product_id}"]`);
        if (!wrap) return;
        
        const qtySpan = wrap.querySelector('.stock-qty');
        let currentQty = parseInt((qtySpan.textContent||'0').replace(/\./g,'')) || 0;
        let newQty = currentQty - freeQty;
        if (newQty < 0) newQty = 0;
        
        qtySpan.textContent = formatNumber(newQty);
        wrap.classList.toggle('text-green-600', newQty > 0);
        wrap.classList.toggle('text-red-600', newQty <= 0);
    });
}

function openPaymentSuccessModal(data){
    currentSaleId = data.sale_id || data.id;
    document.getElementById('successInvoiceNumber').textContent = data.invoice_number || '-';
    document.getElementById('successDate').textContent = formatDateTime(data.created_at || new Date());
    document.getElementById('successTotal').textContent = 'Rp ' + formatNumber(data.grand_total || 0);
    if (data.change_amount && data.change_amount > 0) {
        document.getElementById('successChangeRow').style.display = 'flex';
        document.getElementById('successChange').textContent = 'Rp ' + formatNumber(data.change_amount);
    } else {
        document.getElementById('successChangeRow').style.display = 'none';
    }
    document.getElementById('paymentSuccessModal').classList.remove('hidden');
}

function closePaymentSuccessModal(){
    document.getElementById('paymentSuccessModal').classList.add('hidden');
    currentSaleId=null;
    
    // PERBAIKAN: Pastikan discount info benar-benar hilang
    activeDiscountPlan = null;
    renderCart();
}

function printReceipt(){
    if(!currentSaleId){
        showToast('error','Sale ID tidak ditemukan');
        return;
    }
    window.open('/receipt/print/'+currentSaleId, '_blank');
}

function downloadReceipt(){
    if(!currentSaleId){
        showToast('error','Sale ID tidak ditemukan');
        return;
    }
    showToast('info','Memproses download...');
    const a=document.createElement('a');
    a.href='/receipt/download/'+currentSaleId;
    a.download='';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    setTimeout(()=>showToast('success','Struk berhasil didownload!'), 500);
}

// ==================== KEYBOARD SHORTCUTS ====================
document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') {
        const modal = document.getElementById('productSettingsModal');
        if (modal && !modal.classList.contains('hidden')) {
            closeProductSettingsModal();
        }
        const calcModal = document.getElementById('calculatorModal');
        if (calcModal && !calcModal.classList.contains('hidden')) {
            closeCalculator();
            return;
        }
        const successModal = document.getElementById('paymentSuccessModal');
        if (successModal && !successModal.classList.contains('hidden')) {
            closePaymentSuccessModal();
            return;
        }
        
        const openingModal = document.getElementById('openingAmountModal');
        if (openingModal && !openingModal.classList.contains('hidden')) {
            closeOpeningAmountModal();
            return;
        }
        
        if (UI_STATE === 'cash' || UI_STATE === 'transfer' || UI_STATE === 'midtrans') {
            setUIState('select');
        } else if (UI_STATE === 'select') {
            setUIState('browse');
        }
    }

    if (e.key === 'Enter' && UI_STATE === 'cash') { 
        const cashInput = document.getElementById('cashPaidAmount');
        if (document.activeElement === cashInput) {
            e.preventDefault(); 
            processCashPayment(); 
        }
    }

    if (e.key === 'Enter') {
        const openingInput = document.getElementById('openingAmountInput');
        if (document.activeElement === openingInput) {
            e.preventDefault();
            submitOpeningAmount();
        }
    }
});

// Keyboard untuk kalkulator
document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('calculatorModal');
    if (!modal || modal.classList.contains('hidden')) return;
    if (e.key >= '0' && e.key <= '9') {
        e.preventDefault();
        calcNumber(e.key);
    } else if (e.key === '.') {
        e.preventDefault();
        calcDecimal();
    } else if (e.key === '+' || e.key === '-' || e.key === '*' || e.key === '/') {
        e.preventDefault();
        calcOperator(e.key);
    } else if (e.key === 'Enter' || e.key === '=') {
        e.preventDefault();
        calcEquals();
    } else if (e.key === 'Backspace') {
        e.preventDefault();
        calcDelete();
    } else if (e.key === 'Delete' || e.key.toLowerCase() === 'c') {
        e.preventDefault();
        calcClear();
    }
});

// ==================== PRODUCT SETTINGS FUNCTIONS ====================
let productSettings = {
    hideOutOfStock: false,
    sortBy: 'default',
    hiddenProducts: []
};

function openProductSettingsModal() {
    document.getElementById('productSettingsModal').classList.remove('hidden');
    loadProductSettings();
}

function closeProductSettingsModal() {
    document.getElementById('productSettingsModal').classList.add('hidden');
}

function loadProductSettings() {
    const saved = localStorage.getItem('pos_product_settings');
    if (saved) {
        try {
            productSettings = JSON.parse(saved);
            document.getElementById('hideOutOfStock').checked = productSettings.hideOutOfStock;
            document.getElementById('sortProducts').value = productSettings.sortBy;
            
            if (productSettings.hiddenProducts && productSettings.hiddenProducts.length > 0) {
                document.getElementById('enableProductHiding').checked = true;
                toggleProductListVisibility();
                
                // Update toggle states
                productSettings.hiddenProducts.forEach(productId => {
                    const toggle = document.querySelector(`.product-visibility-toggle[data-product-id="${productId}"]`);
                    if (toggle) toggle.checked = false;
                });
            }
        } catch (e) {
            console.error('Failed to load product settings:', e);
        }
    }
}

function saveProductSettings() {
    localStorage.setItem('pos_product_settings', JSON.stringify(productSettings));
}

function toggleProductListVisibility() {
    const checkbox = document.getElementById('enableProductHiding');
    const list = document.getElementById('productVisibilityList');
    
    if (checkbox.checked) {
        list.classList.remove('hidden');
    } else {
        list.classList.add('hidden');
        // Reset all toggles to checked
        document.querySelectorAll('.product-visibility-toggle').forEach(toggle => {
            toggle.checked = true;
        });
        productSettings.hiddenProducts = [];
        saveProductSettings();
        applyProductSettings();
    }
}

function toggleProductVisibility(productId, isVisible) {
    if (isVisible) {
        // Remove from hidden list
        productSettings.hiddenProducts = productSettings.hiddenProducts.filter(id => id !== productId);
    } else {
        // Add to hidden list
        if (!productSettings.hiddenProducts.includes(productId)) {
            productSettings.hiddenProducts.push(productId);
        }
    }
    
    saveProductSettings();
    applyProductSettings();
}

function applyProductSettings() {
    // Save current settings
    productSettings.hideOutOfStock = document.getElementById('hideOutOfStock').checked;
    productSettings.sortBy = document.getElementById('sortProducts').value;
    saveProductSettings();
    
    const productGrid = document.getElementById('productGrid');
    const productCards = Array.from(document.querySelectorAll('.product-card'));
    
    // Apply filters
    productCards.forEach(card => {
        const productId = parseInt(card.dataset.productId);
        let shouldShow = true;
        
        // Check if manually hidden
        if (productSettings.hiddenProducts.includes(productId)) {
            shouldShow = false;
        }
        
        // Check out of stock filter
        if (shouldShow && productSettings.hideOutOfStock) {
            const stockEl = card.querySelector('.product-stock .stock-qty');
            if (stockEl) {
                const stockQty = parseInt(stockEl.textContent.replace(/\./g, '')) || 0;
                if (stockQty <= 0) {
                    shouldShow = false;
                }
            }
        }
        
        card.style.display = shouldShow ? '' : 'none';
    });
    
    // Apply sorting
    const visibleCards = productCards.filter(card => card.style.display !== 'none');
    
    visibleCards.sort((a, b) => {
        switch(productSettings.sortBy) {
            case 'name-asc':
                return a.dataset.productName.localeCompare(b.dataset.productName);
            case 'name-desc':
                return b.dataset.productName.localeCompare(a.dataset.productName);
            case 'price-asc':
                return parseFloat(a.dataset.productPrice) - parseFloat(b.dataset.productPrice);
            case 'price-desc':
                return parseFloat(b.dataset.productPrice) - parseFloat(a.dataset.productPrice);
            case 'discount':
                const aHasDiscount = a.querySelector('.discount-badge') ? 1 : 0;
                const bHasDiscount = b.querySelector('.discount-badge') ? 1 : 0;
                return bHasDiscount - aHasDiscount;
            default:
                return parseInt(a.dataset.productId) - parseInt(b.dataset.productId);
        }
    });
    
    // Re-append in sorted order
    visibleCards.forEach(card => productGrid.appendChild(card));
    
    // Update empty state
    const visibleCount = visibleCards.length;
    updateEmptyState(productGrid, visibleCount, '', '');
    
    showToast('success', 'Pengaturan produk diterapkan');
}

function resetProductSettings() {
    Swal.fire({
        title: 'Reset Pengaturan?',
        text: "Semua pengaturan produk akan dikembalikan ke default",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Reset',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            productSettings = {
                hideOutOfStock: false,
                sortBy: 'default',
                hiddenProducts: []
            };
            
            localStorage.removeItem('pos_product_settings');
            
            document.getElementById('hideOutOfStock').checked = false;
            document.getElementById('sortProducts').value = 'default';
            document.getElementById('enableProductHiding').checked = false;
            toggleProductListVisibility();
            
            // Reset all toggles
            document.querySelectorAll('.product-visibility-toggle').forEach(toggle => {
                toggle.checked = true;
            });
            
            // Show all products
            document.querySelectorAll('.product-card').forEach(card => {
                card.style.display = '';
            });
            
            // Reset order to default (by ID)
            const productGrid = document.getElementById('productGrid');
            const productCards = Array.from(document.querySelectorAll('.product-card'));
            productCards.sort((a, b) => parseInt(a.dataset.productId) - parseInt(b.dataset.productId));
            productCards.forEach(card => productGrid.appendChild(card));
            
            applyProductSettings();
            showToast('success', 'Pengaturan direset ke default');
        }
    });
}

// ==================== SALES TODAY MODAL FUNCTIONS ====================
function openSalesTodayModal() {
    document.getElementById('salesTodayModal').classList.remove('hidden');
    loadSalesToday();
}

function closeSalesTodayModal() {
    document.getElementById('salesTodayModal').classList.add('hidden');
}

function loadSalesToday() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('salesTodayDate').textContent = formatDate(today);
    
    fetch(`{{ route('sales.daily') }}?date=${today}`)
        .then(r => r.json())
        .then(data => {
            // Update summary cards
            document.getElementById('modalTotalTransactions').textContent = data.summary.transactions;
            document.getElementById('modalTotalRevenue').textContent = 'Rp ' + formatNumber(data.summary.revenue);
            document.getElementById('modalCashTotal').textContent = 'Rp ' + formatNumber(data.totals.cash);
            
            const nonCash = data.totals.qris + data.totals.transfer;
            document.getElementById('modalNonCashTotal').textContent = 'Rp ' + formatNumber(nonCash);
            
            // Render table
            const tbody = document.getElementById('salesTableBody');
            if (data.sales.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                            <p>Belum ada transaksi hari ini</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            let html = '';
            data.sales.forEach(sale => {
                const paymentBadge = getPaymentBadge(sale.payment_method);
                const statusBadge = sale.status === 'completed' 
                    ? '<span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Selesai</span>'
                    : '<span class="badge badge-danger"><i class="fas fa-undo mr-1"></i>Refund</span>';
                
                const refundButton = (sale.status === 'completed' && (sale.payment_method === 'cash' || sale.payment_method === 'transfer'))
                    ? `<button onclick="confirmRefund(${sale.id})" class="px-3 py-1.5 bg-red-500 text-white rounded-lg text-xs font-semibold hover:bg-red-600 transition-colors">
                        <i class="fas fa-undo mr-1"></i>Refund
                    </button>`
                    : '-';
                
                html += `
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs">${sale.invoice_number}</td>
                        <td class="px-4 py-3">${sale.time}</td>
                        <td class="px-4 py-3">${sale.cashier || '-'}</td>
                        <td class="px-4 py-3">${paymentBadge}</td>
                        <td class="px-4 py-3 text-right font-semibold text-red-600">Rp ${formatNumber(sale.total_discount)}</td>
                        <td class="px-4 py-3 text-right font-semibold">Rp ${formatNumber(sale.grand_total)}</td>
                        <td class="px-4 py-3 text-center">${statusBadge}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="showSaleDetail(${sale.id})" class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-xs font-semibold hover:bg-blue-100 transition-colors border border-blue-200">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </button>
                                ${refundButton}
                            </div>
                        </td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
        })
        .catch(() => {
            showToast('error', 'Gagal memuat data penjualan');
            document.getElementById('salesTableBody').innerHTML = `
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-red-500">
                        <i class="fas fa-exclamation-circle text-3xl mb-2"></i>
                        <p>Gagal memuat data</p>
                    </td>
                </tr>
            `;
        });
}

function getPaymentBadge(method) {
    const badges = {
        'cash': '<span class="badge" style="background-color: #dbeafe; color: #1e40af;"><i class="fas fa-money-bill-wave mr-1"></i>Tunai</span>',
        'transfer': '<span class="badge" style="background-color: #fef3c7; color: #92400e;"><i class="fas fa-building-columns mr-1"></i>Transfer</span>',
        'qris': '<span class="badge" style="background-color: #e9d5ff; color: #6b21a8;"><i class="fas fa-qrcode mr-1"></i>QRIS</span>'
    };
    return badges[method] || method;
}

function confirmRefund(saleId) {
    Swal.fire({
        title: 'Refund Transaksi?',
        text: "Stok akan dikembalikan dan status transaksi akan diubah menjadi 'Refunded'",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Refund',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            processRefund(saleId);
        }
    });
}

function processRefund(saleId) {
    fetch(`/sales/${saleId}/refund`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Refund berhasil diproses');
            loadSalesToday(); // Reload table
        } else {
            showToast('error', data.message || 'Refund gagal');
        }
    })
    .catch(() => {
        showToast('error', 'Terjadi kesalahan saat refund');
    });
}

function formatDate(dateStr) {
    const date = new Date(dateStr);
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('id-ID', options);
}

function showSaleDetail(saleId) {
    // Show loading state or just open modal with loader
    document.getElementById('saleDetailModal').classList.remove('hidden');
    
    // Reset content
    document.getElementById('detailItemsBody').innerHTML = '<tr><td colspan="4" class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Memuat...</td></tr>';
    
    fetch(`/api/sale/${saleId}`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('detailInvoiceNumber').textContent = data.invoice_number;
            document.getElementById('detailCashier').textContent = data.cashier_name;
            document.getElementById('detailCustomer').textContent = data.customer_name;
            document.getElementById('detailTime').textContent = data.created_at;
            document.getElementById('detailPaymentMethod').textContent = data.payment_method.toUpperCase();
            
            // Render Items
            let itemsHtml = '';
            data.items.forEach(item => {
                itemsHtml += `
                    <tr class="border-b last:border-0">
                        <td class="px-4 py-2 text-gray-800">${item.product_name}</td>
                        <td class="px-4 py-2 text-center text-gray-600">${item.quantity}</td>
                        <td class="px-4 py-2 text-right text-gray-600">Rp ${formatNumber(item.price)}</td>
                        <td class="px-4 py-2 text-right font-medium text-gray-900">Rp ${formatNumber(item.subtotal)}</td>
                    </tr>
                `;
            });
            document.getElementById('detailItemsBody').innerHTML = itemsHtml;
            
            // Render Totals
            document.getElementById('detailSubtotal').textContent = 'Rp ' + formatNumber(data.subtotal);
            document.getElementById('detailTax').textContent = 'Rp ' + formatNumber(data.tax);
            document.getElementById('detailDiscount').textContent = '-Rp ' + formatNumber(data.total_discount);
            document.getElementById('detailGrandTotal').textContent = 'Rp ' + formatNumber(data.grand_total);
            document.getElementById('detailPaid').textContent = 'Rp ' + formatNumber(data.paid_amount);
            document.getElementById('detailChange').textContent = 'Rp ' + formatNumber(data.change_amount);
        })
        .catch(err => {
            console.error(err);
            showToast('error', 'Gagal memuat detail transaksi');
            closeSaleDetailModal();
        });
}

function closeSaleDetailModal() {
    document.getElementById('saleDetailModal').classList.add('hidden');
}

</script>
@endpush

@extends('layouts.app')

@section('title', 'Point of Sale - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@push('styles')
<style>
    html, body { 
        height: 100%; 
        /* overflow: hidden; */
    }

    body {
        background-color: #f8f9fa;
    }

    .pos-container {
        height: 100vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    /* Header kompak */
    .pos-header {
        background: white;
        border-bottom: 1px solid #e5e7eb;
        padding: 0.75rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }

    /* Main content area */
    .pos-main {
        flex: 1;
        display: grid;
        grid-template-columns: 1fr 420px;
        gap: 1rem;
        padding: 1rem 1.5rem;
        overflow: hidden;
        min-height: 0;
    }

    /* Left panel - Products */
    .products-panel {
        display: flex;
        flex-direction: column;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        min-height: 0;
    }

    .products-toolbar {
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
        flex-shrink: 0;
    }

    .products-content {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
        min-height: 0;
    }

    /* Product Grid - Compact */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 0.75rem;
    }

    .product-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.75rem;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        flex-direction: column;
    }

    .product-card:hover {
        border-color: #6366f1;
        box-shadow: 0 2px 8px rgba(99, 102, 241, 0.1);
        transform: translateY(-2px);
    }

    .product-image {
        width: 100%;
        height: 80px;
        object-fit: cover;
        border-radius: 6px;
        margin-bottom: 0.5rem;
    }

    .product-placeholder {
        width: 100%;
        height: 80px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.5rem;
    }

    .product-name {
        font-size: 0.813rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.25rem;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-price {
        font-size: 0.875rem;
        font-weight: 700;
        color: #6366f1;
        margin-top: auto;
    }

    .product-stock {
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    /* Right panel - Order Summary */
    .order-panel {
        display: flex;
        flex-direction: column;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        min-height: 0;
    }

    .order-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e5e7eb;
        flex-shrink: 0;
    }

    .order-items {
        flex: 1;
        overflow-y: auto;
        padding: 1rem 1.25rem;
        min-height: 0;
    }

    .order-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: #f9fafb;
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }

    .order-item-info {
        flex: 1;
        min-width: 0;
    }

    .order-item-name {
        font-size: 0.875rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .order-item-price {
        font-size: 0.75rem;
        color: #6b7280;
    }

    .qty-controls {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 0.125rem;
    }

    .qty-btn {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        color: #6b7280;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .qty-btn:hover {
        background: #f3f4f6;
        color: #1f2937;
    }

    .qty-input {
        width: 40px;
        height: 24px;
        border: none;
        text-align: center;
        font-size: 0.813rem;
        font-weight: 600;
        color: #1f2937;
        background: transparent;
    }

    .qty-input:focus {
        outline: none;
    }

    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .qty-input[type=number] {
        -moz-appearance: textfield;
    }

    .order-footer {
        padding: 1rem 1.25rem;
        border-top: 1px solid #e5e7eb;
        flex-shrink: 0;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .summary-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 0.75rem;
        margin-top: 0.75rem;
        border-top: 2px solid #e5e7eb;
        font-size: 1.125rem;
        font-weight: 700;
    }

    /* Buttons */
    .btn-primary {
        width: 100%;
        padding: 0.875rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.938rem;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        width: 100%;
        padding: 0.75rem;
        background: white;
        color: #6b7280;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 0.5rem;
    }

    .btn-secondary:hover {
        background: #f9fafb;
        border-color: #d1d5db;
    }

    /* Search & Filter */
    .search-box {
        position: relative;
        flex: 1;
    }

    .search-input {
        width: 100%;
        padding: 0.625rem 0.875rem 0.625rem 2.5rem;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .search-input:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .search-icon {
        position: absolute;
        left: 0.875rem;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 0.875rem;
    }

    .filter-select {
        padding: 0.625rem 0.875rem;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 0.875rem;
        color: #374151;
        cursor: pointer;
        transition: all 0.2s;
        min-width: 140px;
    }

    .filter-select:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    /* Category Tabs */
    .category-tabs {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.75rem;
        flex-wrap: wrap;
    }

    .category-tab {
        padding: 0.5rem 1rem;
        background: #f3f4f6;
        border: none;
        border-radius: 6px;
        font-size: 0.813rem;
        font-weight: 500;
        color: #6b7280;
        cursor: pointer;
        transition: all 0.2s;
    }

    .category-tab.active {
        background: #6366f1;
        color: white;
    }

    .category-tab:hover:not(.active) {
        background: #e5e7eb;
        color: #374151;
    }

    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f3f4f6;
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }

    /* Payment Views */
    .payment-view {
        padding: 1.5rem;
        max-width: 400px;
        margin: 0 auto;
    }

    .payment-methods {
        display: grid;
        gap: 0.75rem;
    }

    .payment-method {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .payment-method:hover {
        border-color: #6366f1;
        background: #f9fafb;
    }

    .payment-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 1.25rem;
        color: white;
    }

    .payment-info {
        flex: 1;
    }

    .payment-title {
        font-size: 0.938rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.125rem;
    }

    .payment-subtitle {
        font-size: 0.75rem;
        color: #6b7280;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #9ca3af;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    /* Calculator Styles */
    .calc-btn {
        padding: 1rem;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .calc-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .calc-btn:active {
        transform: translateY(0);
    }

    .calc-history-item {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.75rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .calc-history-item:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
    }

    .calc-history-expression {
        font-size: 0.75rem;
        color: #6b7280;
        margin-bottom: 0.25rem;
    }

    .calc-history-result {
        font-size: 1rem;
        font-weight: 700;
        color: #1f2937;
    }

    /* Responsive Calculator */
    @media (max-width: 1024px) {
        #calculatorModal .max-w-2xl {
            max-width: 95%;
        }
        
        .calc-btn {
            padding: 0.875rem;
            font-size: 1rem;
        }
    }

    /* Responsive */
    @media (max-width: 1280px) {
        .pos-main {
            grid-template-columns: 1fr 380px;
        }
        
        .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        }
    }

    @media (max-width: 1024px) {
        .pos-main {
            grid-template-columns: 1fr;
            grid-template-rows: 1fr auto;
        }
        
        .order-panel {
            max-height: 400px;
        }
    }

    @media (max-width: 768px) {
        body {
            overflow: auto;
        }
        
        .pos-container {
            height: auto;
            min-height: 100vh;
        }
        
        .pos-main {
            display: flex;
            flex-direction: column;
            height: auto;
        }
        
        .products-panel,
        .order-panel {
            height: auto;
        }
        
        .product-grid {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        }
    }
</style>
@endpush

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Point of Sale</span>
</li>
@endsection

@section('content')
<!-- Toast Container -->
<div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

<!-- Modal: Mulai Penjualan -->
<div id="startSalesModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 lg:p-8">
        <div class="text-center mb-5">
            <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-1">Selamat Datang!</h3>
            <p class="text-gray-600 text-sm">Mulai penjualan Anda hari ini</p>
        </div>
        <div class="bg-indigo-50 rounded-xl p-3 mb-5">
            <p class="text-xs sm:text-sm text-gray-700 text-center">
                <i class="fas fa-info-circle text-indigo-600 mr-1.5"></i>
                Sistem akan mencatat semua transaksi Anda hari ini
            </p>
        </div>
        <div class="flex gap-2.5">
            <button onclick="declineStartSales()" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                Tidak
            </button>
            <button onclick="openOpeningAmountModal()" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg text-sm font-semibold hover:from-indigo-600 hover:to-purple-700 transition-all shadow-md hover:shadow-lg">
                Ya, Mulai
            </button>
        </div>
    </div>
</div>

<!-- Modal: Payment Success -->
<div id="paymentSuccessModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 lg:p-8">
        <div class="flex justify-center mb-5">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-xl font-bold text-gray-900 text-center mb-1.5">Pembayaran Berhasil!</h3>
        <p class="text-gray-600 text-sm text-center mb-5">Transaksi telah berhasil diproses</p>
        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-4 mb-5">
            <div class="space-y-1.5 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">No. Invoice:</span>
                    <span class="font-bold text-gray-900" id="successInvoiceNumber">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Tanggal:</span>
                    <span class="text-gray-900 font-medium" id="successDate">-</span>
                </div>
                <div class="flex justify-between text-base font-bold border-t border-indigo-200 pt-2 mt-2">
                    <span class="text-gray-700">Total:</span>
                    <span class="text-indigo-600" id="successTotal">Rp 0</span>
                </div>
                <div class="flex justify-between text-sm" id="successChangeRow" style="display: none;">
                    <span class="text-gray-600">Kembalian:</span>
                    <span class="font-bold text-green-600" id="successChange">Rp 0</span>
                </div>
            </div>
        </div>
        <div class="space-y-2.5">
            <button onclick="printReceipt()" class="w-full flex items-center justify-center gap-2.5 px-4 py-3 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl text-sm font-semibold hover:from-indigo-600 hover:to-indigo-700 transition-all shadow-md hover:shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span>Cetak Struk</span>
            </button>
            <button onclick="downloadReceipt()" class="w-full flex items-center justify-center gap-2.5 px-4 py-3 bg-white border-2 border-indigo-200 text-indigo-600 rounded-xl text-sm font-semibold hover:bg-indigo-50 transition-all">
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
                    <!-- <button class="category-tab active" data-category="">Semua</button>
                    <button class="category-tab" data-category="food">Makanan</button>
                    <button class="category-tab" data-category="drink">Minuman</button>
                    <button class="category-tab" data-category="snack">Snack</button> -->
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
                            <div class="payment-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div class="payment-info">
                                <div class="payment-title">Tunai</div>
                                <div class="payment-subtitle">Pembayaran cash</div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </div>
                        <div class="payment-method" onclick="setUIState('transfer')">
                            <div class="payment-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                                <i class="fas fa-building-columns"></i>
                            </div>
                            <div class="payment-info">
                                <div class="payment-title">Transfer</div>
                                <div class="payment-subtitle">Transfer Bank / QR</div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </div>
                        <div class="payment-method" onclick="setUIState('midtrans')">
                            <div class="payment-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
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
                    <button onclick="processTransferPayment()" class="btn-primary" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
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
                    <button onclick="openMidtransPayment()" class="btn-primary" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
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
                        <a href="{{ route('sales.index') }}" class="block w-full px-4 py-2.5 text-left text-sm hover:bg-indigo-50 transition-colors flex items-center gap-2 text-gray-700 border-b border-gray-100">
                            <i class="fas fa-chart-line w-4 text-indigo-600"></i>
                            <span>Penjualan Hari Ini</span>
                        </a>
                        
                        <!-- Kalkulator -->
                        <button onclick="openCalculator(); togglePOSMenu();" class="w-full px-4 py-2.5 text-left text-sm hover:bg-purple-50 transition-colors flex items-center gap-2 text-gray-700">
                            <i class="fas fa-calculator w-4 text-purple-600"></i>
                            <span>Kalkulator</span>
                        </button>
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
                    <span class="text-indigo-600" id="summaryGrandTotal">Rp 0</span>
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
                    <div class="w-full px-4 py-3 bg-indigo-50 text-indigo-700 rounded-xl text-sm font-semibold flex items-center justify-between">
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
            <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-1">Modal Awal</h3>
            <p class="text-gray-600 text-sm">Masukkan jumlah uang modal awal untuk hari ini</p>
        </div>
        <div class="bg-green-50 rounded-xl p-3 mb-5">
            <p class="text-xs sm:text-sm text-gray-700 text-center">
                <i class="fas fa-info-circle text-green-600 mr-1.5"></i>
                Modal awal adalah uang tunai yang Anda bawa untuk memulai penjualan hari ini
            </p>
        </div>
        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Modal Awal (Rp):</label>
            <input 
                type="number" 
                id="openingAmountInput" 
                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent text-base font-semibold" 
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
            <button onclick="submitOpeningAmount()" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg text-sm font-semibold hover:from-green-600 hover:to-emerald-700 transition-all shadow-md hover:shadow-lg">
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
                <i class="fas fa-calculator text-purple-600"></i>
                Kalkulator
            </h3>
            <button onclick="closeCalculator()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 flex-1 overflow-hidden">
            <!-- Calculator -->
            <div class="lg:col-span-3">
                <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl p-4 border border-purple-200">
                    <!-- Display -->
                    <div class="bg-white rounded-lg p-4 mb-4 shadow-inner">
                        <div class="text-right text-sm text-gray-500 h-6" id="calcExpression"></div>
                        <div class="text-right text-3xl font-bold text-gray-900 break-all" id="calcDisplay">0</div>
                    </div>
                    
                    <!-- Buttons -->
                    <div class="grid grid-cols-4 gap-2">
                        <button onclick="calcClear()" class="calc-btn bg-red-500 hover:bg-red-600 text-white col-span-2 font-semibold">C</button>
                        <button onclick="calcDelete()" class="calc-btn bg-orange-500 hover:bg-orange-600 text-white font-semibold">DEL</button>
                        <button onclick="calcOperator('/')" class="calc-btn bg-indigo-500 hover:bg-indigo-600 text-white text-xl">÷</button>
                        
                        <button onclick="calcNumber('7')" class="calc-btn">7</button>
                        <button onclick="calcNumber('8')" class="calc-btn">8</button>
                        <button onclick="calcNumber('9')" class="calc-btn">9</button>
                        <button onclick="calcOperator('*')" class="calc-btn bg-indigo-500 hover:bg-indigo-600 text-white text-xl">×</button>
                        
                        <button onclick="calcNumber('4')" class="calc-btn">4</button>
                        <button onclick="calcNumber('5')" class="calc-btn">5</button>
                        <button onclick="calcNumber('6')" class="calc-btn">6</button>
                        <button onclick="calcOperator('-')" class="calc-btn bg-indigo-500 hover:bg-indigo-600 text-white text-xl">−</button>
                        
                        <button onclick="calcNumber('1')" class="calc-btn">1</button>
                        <button onclick="calcNumber('2')" class="calc-btn">2</button>
                        <button onclick="calcNumber('3')" class="calc-btn">3</button>
                        <button onclick="calcOperator('+')" class="calc-btn bg-indigo-500 hover:bg-indigo-600 text-white text-xl">+</button>
                        
                        <button onclick="calcNumber('0')" class="calc-btn col-span-2">0</button>
                        <button onclick="calcDecimal()" class="calc-btn">.</button>
                        <button onclick="calcEquals()" class="calc-btn bg-green-500 hover:bg-green-600 text-white font-semibold text-xl">=</button>
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

@endsection

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
// ==================== GLOBAL VARIABLES ====================
let UI_STATE = 'browse';
let cart = @json($cart ?? []);
let cartSummary = { subtotal: 0, total_discount: 0, tax: 0, grand_total: 0 };
let currentSaleId = null;

// TAMBAHKAN INI
let categories = @json($categories ?? []);

// ==================== CALCULATOR VARIABLES ====================
let calcCurrentValue = '0';
let calcPreviousValue = '';
let calcOperation = null;
let calcHistory = [];

// ==================== DOM READY ====================
document.addEventListener('DOMContentLoaded', function() {
    loadCalcHistory();
    checkCashRegister();
    renderCart();
    setUIState('browse');
    
    // TAMBAHKAN INI
    renderCategoryTabs();
    renderCategoryDropdown();
    initCategoryHandlers();
    
    initClickOutsideHandler();
});

// ==================== CATEGORY FUNCTIONS ====================
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
    // Category tabs click handler
    document.addEventListener('click', function(e) {
        const tab = e.target.closest('.category-tab');
        if (!tab) return;
        
        // Update active state
        document.querySelectorAll('.category-tab').forEach(t => {
            t.classList.remove('active');
        });
        tab.classList.add('active');
        
        // Get category and filter
        const categoryId = tab.dataset.category;
        const searchTerm = document.getElementById('searchProduct')?.value.toLowerCase() || '';
        
        // Sync with dropdown
        const dropdown = document.getElementById('filterCategory');
        if (dropdown) dropdown.value = categoryId;
        
        // Apply filter
        filterProducts(searchTerm, categoryId);
    });
    
    // Category dropdown change handler
    const dropdown = document.getElementById('filterCategory');
    if (dropdown) {
        dropdown.addEventListener('change', function() {
            const categoryId = this.value;
            const searchTerm = document.getElementById('searchProduct')?.value.toLowerCase() || '';
            
            // Sync with tabs
            document.querySelectorAll('.category-tab').forEach(tab => {
                if (tab.dataset.category === categoryId) {
                    tab.classList.add('active');
                } else {
                    tab.classList.remove('active');
                }
            });
            
            // Apply filter
            filterProducts(searchTerm, categoryId);
        });
    }
    
    // Search input handler
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
        
        // Match search term (nama atau kode produk)
        const matchesSearch = !searchTerm || 
            productName.includes(searchTerm) || 
            productCode.includes(searchTerm);
        
        // Match category
        const matchesCategory = !categoryId || productCategory === categoryId;
        
        // Show/hide card
        if (matchesSearch && matchesCategory) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Handle empty state
    updateEmptyState(productGrid, visibleCount, searchTerm, categoryId);
}

function updateEmptyState(productGrid, visibleCount, searchTerm, categoryId) {
    // Remove existing empty state
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
                    <i class="fas fa-times mr-2"></i>Hapus Filter
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
    // Clear search
    const searchInput = document.getElementById('searchProduct');
    if (searchInput) searchInput.value = '';
    
    // Clear category
    const dropdown = document.getElementById('filterCategory');
    if (dropdown) dropdown.value = '';
    
    // Reset tabs
    document.querySelectorAll('.category-tab').forEach(tab => {
        if (tab.dataset.category === '') {
            tab.classList.add('active');
        } else {
            tab.classList.remove('active');
        }
    });
    
    // Show all products
    filterProducts('', '');
    
    showToast('info', 'Filter dihapus');
}

// ==================== UTILITY: Get Category Statistics ====================
function getCategoryStats() {
    const stats = {};
    
    document.querySelectorAll('.product-card').forEach(card => {
        const categoryId = card.dataset.category || 'uncategorized';
        stats[categoryId] = (stats[categoryId] || 0) + 1;
    });
    
    return stats;
}

// Optional: Show product count in tabs
function updateCategoryTabsWithCount() {
    const stats = getCategoryStats();
    
    document.querySelectorAll('.category-tab').forEach(tab => {
        const categoryId = tab.dataset.category;
        const count = categoryId === '' 
            ? Object.values(stats).reduce((a, b) => a + b, 0)
            : stats[categoryId] || 0;
        
        // Add count badge if you want
        const badge = tab.querySelector('.count-badge');
        if (badge) {
            badge.textContent = count;
        }
    });
}

// ==================== DROPDOWN MENU FUNCTIONS ====================
function togglePOSMenu() {
    const menu = document.getElementById('posDropdownMenu');
    menu.classList.toggle('hidden');
}

function initClickOutsideHandler() {
    document.addEventListener('click', function(e) {
        const menu = document.getElementById('posDropdownMenu');
        const button = e.target.closest('button');
        
        if (!menu.contains(e.target) && button?.onclick?.toString().indexOf('togglePOSMenu') === -1) {
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
    if (!confirm('Hapus semua riwayat kalkulator?')) return;
    
    calcHistory = [];
    saveCalcHistory();
    renderCalcHistory();
    showToast('success', 'Riwayat berhasil dihapus');
}

// ==================== CASH REGISTER FUNCTIONS ====================
function initCategoryTabs() {
    const tabs = document.querySelectorAll('.category-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });
}

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
    if (!confirm('Yakin ingin melewati input modal awal? Modal awal akan diset Rp 0')) {
        return;
    }
    
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
        headers: { 'Content-Type': 'application/json','X-CSRF-TOKEN': '{{ csrf_token() }}' }
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
        headers: { 'Content-Type': 'application/json','X-CSRF-TOKEN': '{{ csrf_token() }}' }
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
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    const colors = { 
        success:'bg-green-500', 
        error:'bg-red-500', 
        info:'bg-blue-500', 
        warning:'bg-orange-500' 
    };
    toast.className = `${colors[type]} text-white px-4 py-3 rounded-lg shadow-lg text-sm`;
    toast.textContent = message;
    container.appendChild(toast);
    setTimeout(()=>{ 
        toast.style.opacity='0'; 
        toast.style.transition='opacity 0.3s'; 
        setTimeout(()=>toast.remove(),300); 
    }, 2500);
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
    .then(r=>r.json()).then(data=>{
        if(data.success){
            cart = data.cart; 
            cartSummary = data.cart_summary;
            renderCart(); 
            showToast('success','Ditambahkan ke keranjang');
        } else { 
            showToast('error', data.message); 
        }
    }).catch(()=>showToast('error','Terjadi kesalahan'));
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
            renderCart();
            showToast('success', 'Item dihapus');
        } else {
            showToast('error', data.message || 'Gagal menghapus item');
        }
    })
    .catch(() => showToast('error', 'Gagal menghapus item'));
}

function clearCart() {
    if (!confirm('Kosongkan keranjang?')) return;
    fetch('{{ route("pos.cart.clear") }}', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}
    }).then(r=>r.json()).then(data=>{
        if(data.success){
            cart = {}; 
            cartSummary = { subtotal:0,total_discount:0,tax:0,grand_total:0 };
            renderCart(); 
            setUIState('browse'); 
            showToast('success','Keranjang dikosongkan');
        }
    }).catch(()=>showToast('error','Gagal'));
}

function renderCart() {
    const preview = document.getElementById('cartItemsPreview');
    const totalItems = Object.values(cart).reduce((s,i)=>s+Number(i.quantity||0),0);
    document.getElementById('totalItems').textContent = totalItems;

    const isPayflow = (UI_STATE !== 'browse');

    if (!cart || Object.keys(cart).length === 0) {
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
    views.forEach(v => document.getElementById(`view-${v}`).classList.add('hidden'));
    document.getElementById(`view-${state}`).classList.remove('hidden');

    updateRightActions();
    renderCart();

    if (state === 'cash') setTimeout(()=>document.getElementById('cashPaidAmount').focus(), 120);
}

function updateRightActions(){
    const isPayflow = (UI_STATE !== 'browse');
    const controls = document.getElementById('actionsControls');
    const badge = document.getElementById('actionsPayflowSummary');
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
    .then(r=>r.json()).then(async data=>{
        if (data.success) {
            if (data.sale && data.sale.items) { 
                updateProductStockFromSaleItems(data.sale.items); 
            }
            await fetch('{{ route("pos.cart.clear") }}', { 
                method:'POST', 
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'} 
            });
            cart = {}; 
            cartSummary = { subtotal:0,total_discount:0,tax:0,grand_total:0 };
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
    }).catch(()=>showToast('error','Gagal proses pembayaran'));
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
    .then(r=>r.json()).then(async data=>{
        if (data.success) {
            if (data.sale && data.sale.items) { 
                updateProductStockFromSaleItems(data.sale.items); 
            }
            await fetch('{{ route("pos.cart.clear") }}', { 
                method:'POST', 
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'} 
            });
            cart = {}; 
            cartSummary = { subtotal:0,total_discount:0,tax:0,grand_total:0 };
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
    }).catch(()=>showToast('error','Gagal proses pembayaran'));
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
    .then(r=>r.json()).then(data=>{
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
                        await fetch('{{ route("pos.cart.clear") }}', { 
                            method:'POST', 
                            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'} 
                        });
                        cart = {}; 
                        cartSummary = { subtotal:0,total_discount:0,tax:0,grand_total:0 };
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
    }).catch(()=>showToast('error','Gagal membuat token'));
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
    // ESC key handling
    if (e.key === 'Escape') {
        // Close calculator if open
        const calcModal = document.getElementById('calculatorModal');
        if (!calcModal.classList.contains('hidden')) {
            closeCalculator();
            return;
        }
        
        // Close payment success modal if open
        const successModal = document.getElementById('paymentSuccessModal');
        if (!successModal.classList.contains('hidden')) {
            closePaymentSuccessModal();
            return;
        }
        
        // Close opening amount modal if open
        const openingModal = document.getElementById('openingAmountModal');
        if (!openingModal.classList.contains('hidden')) {
            closeOpeningAmountModal();
            return;
        }
        
        // Navigate back in payment flow
        if (UI_STATE === 'cash' || UI_STATE === 'transfer' || UI_STATE === 'midtrans') {
            setUIState('select');
        } else if (UI_STATE === 'select') {
            setUIState('browse');
        }
    }
    
    // Enter key on cash payment
    if (e.key === 'Enter' && UI_STATE === 'cash') { 
        const cashInput = document.getElementById('cashPaidAmount');
        if (document.activeElement === cashInput) {
            e.preventDefault(); 
            processCashPayment(); 
        }
    }
    
    // Enter key on opening amount modal
    if (e.key === 'Enter') {
        const openingInput = document.getElementById('openingAmountInput');
        if (document.activeElement === openingInput) {
            e.preventDefault();
            submitOpeningAmount();
        }
    }
});

// Calculator keyboard support (handled in calculator section already, but adding to main keydown)
document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('calculatorModal');
    if (modal.classList.contains('hidden')) return;
    
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

// ==================== SEARCH & FILTER FUNCTIONS ====================
document.addEventListener('DOMContentLoaded', function() {
    // Search product
    const searchInput = document.getElementById('searchProduct');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            filterProducts(searchTerm, document.getElementById('filterCategory').value);
        });
    }
    
    // Filter category dropdown
    const filterCategory = document.getElementById('filterCategory');
    if (filterCategory) {
        filterCategory.addEventListener('change', function() {
            const searchTerm = document.getElementById('searchProduct').value.toLowerCase();
            filterProducts(searchTerm, this.value);
        });
    }
    
    // Category tabs
    const categoryTabs = document.querySelectorAll('.category-tab');
    categoryTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            categoryTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            const category = this.dataset.category;
            const searchTerm = document.getElementById('searchProduct').value.toLowerCase();
            filterProducts(searchTerm, category);
            
            // Sync with dropdown
            document.getElementById('filterCategory').value = category;
        });
    });
});

function filterProducts(searchTerm, category) {
    const productCards = document.querySelectorAll('.product-card');
    let visibleCount = 0;
    
    productCards.forEach(card => {
        const productName = card.dataset.productName ? card.dataset.productName.toLowerCase() : '';
        const productCode = card.dataset.productCode ? card.dataset.productCode.toLowerCase() : '';
        
        // Match search term
        const matchesSearch = !searchTerm || 
            productName.includes(searchTerm) || 
            productCode.includes(searchTerm);
        
        // Match category (you might need to add category data attribute to products)
        const matchesCategory = !category || card.dataset.category === category;
        
        if (matchesSearch && matchesCategory) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Show/hide empty state
    const productGrid = document.getElementById('productGrid');
    const emptyState = productGrid.querySelector('.empty-state');
    
    if (visibleCount === 0) {
        if (!emptyState) {
            const emptyDiv = document.createElement('div');
            emptyDiv.className = 'empty-state';
            emptyDiv.style.gridColumn = '1/-1';
            emptyDiv.innerHTML = `
                <i class="fas fa-search"></i>
                <p>Produk tidak ditemukan</p>
            `;
            productGrid.appendChild(emptyDiv);
        }
    } else {
        if (emptyState) {
            emptyState.remove();
        }
    }
}
</script>
@endpush
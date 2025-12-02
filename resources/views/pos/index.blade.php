@extends('layouts.app')

@section('title', 'Point of Sale - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Point of Sale</span>
</li>
@endsection

@push('styles')
<style>
    /* Custom scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    
    /* Product card hover */
    .product-card {
        transition: all 0.2s ease;
    }
    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    /* Subtle gradient background */
    .pos-gradient-bg {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.03) 0%, rgba(168, 85, 247, 0.05) 100%);
    }
</style>
@endpush

@section('content')
<div id="toastContainer" class="fixed top-20 right-4 z-50 space-y-2"></div>

<main class="flex-grow py-8 px-4">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header -->
        <!-- <div class="mb-6">
            <div class="bg-gradient-to-br from-indigo-100 to-purple-10 p-6 rounded-2xl border border-indigo-100">
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-cash-register text-indigo-600 mr-3"></i>
                    Point of Sale
                </h1>
                <p class="text-gray-600 mt-1">Proses transaksi penjualan dengan cepat dan mudah</p>
            </div>
        </div> -->
        
        <!-- Bento Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- LEFT SECTION: Products -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Search & Filter Bar -->
                <div class="bg-white rounded-2xl shadow-sm p-4">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1">
                            <input 
                                type="text" 
                                id="searchProduct" 
                                placeholder="Cari produk (nama atau kode)..."
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            >
                        </div>
                        <select 
                            id="filterCategory" 
                            class="px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        >
                            <option value="">Semua Kategori</option>
                        </select>
                    </div>
                </div>
                
                <!-- Products Grid -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Daftar Produk</h2>
                    
                    <div id="productGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 max-h-[600px] overflow-y-auto custom-scrollbar pr-2">
                        @forelse($products as $product)
                        <div class="product-card bg-white border border-gray-200 rounded-xl p-4 cursor-pointer" 
                             data-product-id="{{ $product->id }}"
                             data-product-name="{{ $product->name }}"
                             data-product-code="{{ $product->code }}"
                             data-product-price="{{ $product->selling_price }}"
                             data-product-hpp="{{ $product->hpp }}"
                             onclick="addProductToCart(this)">
                            
                            @if($product->image)
                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-24 object-cover rounded-lg mb-3">
                            @else
                            <div class="w-full h-24 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-lg mb-3 flex items-center justify-center">
                                <i class="fas fa-image text-3xl text-indigo-300"></i>
                            </div>
                            @endif
                            
                            <h3 class="font-semibold text-sm text-gray-900 mb-1 line-clamp-2">{{ $product->name }}</h3>
                            <p class="text-xs text-gray-500 mb-2">{{ $product->code }}</p>
                            
                            <div class="mt-auto">
                                <span class="text-sm font-bold text-gray-900">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                            </div>
                            
                            @if($product->track_stock)
                            @php
                                $stock = $product->stocks->where('outlet_id', auth()->user()->outlet_id)->first();
                                $stockQty = $stock ? $stock->quantity : 0;
                            @endphp
                            <p class="text-xs mt-2 stock-display {{ $stockQty > 0 ? 'text-green-600' : 'text-red-600' }}" data-product-id="{{ $product->id }}">
                                Stok: <span class="stock-qty">{{ number_format($stockQty, 0, ',', '.') }}</span>
                            </p>
                            @endif
                        </div>
                        @empty
                        <div class="col-span-full text-center py-12">
                            <i class="fas fa-box-open text-5xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Belum ada produk tersedia</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                
            </div>
            
            <!-- RIGHT SECTION: Cart & Payment -->
            <div class="space-y-6">
                
                <!-- Customer Selection -->
                <div class="bg-white rounded-2xl shadow-sm p-4" hidden>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pelanggan (Opsional)</label>
                    <select 
                        id="selectCustomer" 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        onchange="setCustomer(this.value)"
                    >
                        <option value="">Umum / Walk-in</option>
                        @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->phone }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Cart -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Keranjang</h2>
                        <button onclick="clearCart()" class="text-sm text-red-600 hover:text-red-700 font-medium">
                            <i class="fas fa-trash-alt mr-1"></i> Kosongkan
                        </button>
                    </div>
                    
                    <div id="cartItems" class="space-y-3 max-h-[300px] overflow-y-auto custom-scrollbar pr-2 mb-4">
                        <div class="text-center py-8 text-gray-400" id="emptyCartMessage">
                            <i class="fas fa-shopping-cart text-4xl mb-2"></i>
                            <p>Keranjang masih kosong</p>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium" id="cartSubtotal">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Diskon:</span>
                            <span class="font-medium text-red-600" id="cartDiscount">- Rp 0</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Pajak:</span>
                            <span class="font-medium" id="cartTax">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2 mt-2">
                            <span>Total:</span>
                            <span class="text-indigo-600" id="cartGrandTotal">Rp 0</span>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Methods -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Metode Pembayaran</h2>
                    
                    <div class="space-y-3">
                        <button 
                            onclick="openCashPaymentModal()" 
                            class="w-full flex items-center justify-between p-4 border-2 border-gray-200 rounded-xl hover:border-indigo-500 hover:bg-indigo-50 transition-all duration-200"
                        >
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-money-bill-wave text-white text-xl"></i>
                                </div>
                                <div class="text-left">
                                    <p class="font-semibold text-gray-900">Bayar Tunai</p>
                                    <p class="text-xs text-gray-500">Pembayaran cash langsung</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </button>
                        
                        <button 
                            onclick="openTransferPaymentModal()" 
                            class="w-full flex items-center justify-between p-4 border-2 border-gray-200 rounded-xl hover:border-indigo-500 hover:bg-indigo-50 transition-all duration-200"
                        >
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-building-columns text-white text-xl"></i>
                                </div>
                                <div class="text-left">
                                    <p class="font-semibold text-gray-900">Bayar Transfer</p>
                                    <p class="text-xs text-gray-500">Transfer via QR / Bank</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </button>
                        
                        <button 
                            onclick="openMidtransPayment()" 
                            class="w-full flex items-center justify-between p-4 border-2 border-gray-200 rounded-xl hover:border-indigo-500 hover:bg-indigo-50 transition-all duration-200"
                        >
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-qrcode text-white text-xl"></i>
                                </div>
                                <div class="text-left">
                                    <p class="font-semibold text-gray-900">Bayar via Midtrans</p>
                                    <p class="text-xs text-gray-500">QRIS, E-Wallet, VA</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </button>
                    </div>
                </div>
                
            </div>
            
        </div>
        
    </div>
</main>

<!-- Modal: Cash Payment -->
<div id="cashPaymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Pembayaran Tunai</h3>
        
        <div class="mb-6">
            <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 mb-4">
                <p class="text-sm text-gray-600 mb-1">Total yang harus dibayar:</p>
                <p class="text-2xl font-bold text-indigo-600" id="cashModalTotal">Rp 0</p>
            </div>
            
            <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Uang Diterima:</label>
            <input 
                type="number" 
                id="cashPaidAmount" 
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-lg font-semibold"
                placeholder="0"
                onkeyup="calculateChange()"
            >
            
            <div class="mt-4 p-4 bg-gray-50 rounded-xl">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Kembalian:</span>
                    <span class="text-2xl font-bold text-green-600" id="changeAmount">Rp 0</span>
                </div>
            </div>
        </div>
        
        <div class="flex gap-3">
            <button onclick="closeCashPaymentModal()" class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <button onclick="processCashPayment()" class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl font-semibold hover:from-green-600 hover:to-green-700 transition-all shadow-lg hover:shadow-xl">
                Proses Pembayaran
            </button>
        </div>
    </div>
</div>

<!-- Modal: Transfer Payment -->
<div id="transferPaymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Pembayaran Transfer</h3>
        
        <div class="mb-6">
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
                <p class="text-sm text-gray-600 mb-1">Total yang harus dibayar:</p>
                <p class="text-2xl font-bold text-blue-600" id="transferModalTotal">Rp 0</p>
            </div>
            
            <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Metode Transfer:</label>
            <select 
                id="transferMethod" 
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent mb-4"
            >
                <option value="">-- Pilih Metode --</option>
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
            <input 
                type="text" 
                id="transferReference" 
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Masukkan nomor referensi"
            >
            
            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                <p class="text-sm text-gray-700">
                    <i class="fas fa-info-circle text-yellow-600 mr-2"></i>
                    Pastikan pelanggan telah melakukan pembayaran via QR Code atau transfer bank yang tersedia di kasir.
                </p>
            </div>
        </div>
        
        <div class="flex gap-3">
            <button onclick="closeTransferPaymentModal()" class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <button onclick="processTransferPayment()" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-semibold hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg hover:shadow-xl">
                Konfirmasi Pembayaran
            </button>
        </div>
    </div>
</div>

<!-- Modal: Payment Success -->
<div id="paymentSuccessModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8 transform transition-all">
        <!-- Success Icon Animation -->
        <div class="flex justify-center mb-6">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>
        
        <!-- Success Message -->
        <h3 class="text-2xl font-bold text-gray-900 text-center mb-2">Pembayaran Berhasil!</h3>
        <p class="text-gray-600 text-center mb-6">Transaksi telah berhasil diproses</p>
        
        <!-- Transaction Details -->
        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-4 mb-6">
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">No. Invoice:</span>
                    <span class="font-bold text-gray-900" id="successInvoiceNumber">-</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Tanggal:</span>
                    <span class="font-medium text-gray-900" id="successDate">-</span>
                </div>
                <div class="flex justify-between text-lg font-bold border-t border-indigo-200 pt-2 mt-2">
                    <span class="text-gray-700">Total:</span>
                    <span class="text-indigo-600" id="successTotal">Rp 0</span>
                </div>
                <div class="flex justify-between text-sm" id="successChangeRow" style="display: none;">
                    <span class="text-gray-600">Kembalian:</span>
                    <span class="font-bold text-green-600" id="successChange">Rp 0</span>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="space-y-3">
            <!-- Print Receipt Button -->
            <button 
                onclick="printReceipt()" 
                class="w-full flex items-center justify-center gap-3 px-6 py-4 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl font-semibold hover:from-indigo-600 hover:to-indigo-700 transition-all shadow-lg hover:shadow-xl"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span>Cetak Struk</span>
            </button>
            
            <!-- Download Receipt Button -->
            <button 
                onclick="downloadReceipt()" 
                class="w-full flex items-center justify-center gap-3 px-6 py-4 bg-white border-2 border-indigo-200 text-indigo-600 rounded-xl font-semibold hover:bg-indigo-50 transition-all"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Download Struk (PDF)</span>
            </button>
            
            <!-- Close Button -->
            <button 
                onclick="closePaymentSuccessModal()" 
                class="w-full px-6 py-4 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-all"
            >
                Selesai
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>

<script>
let cart = {};
let cartSummary = {
    subtotal: 0,
    total_discount: 0,
    tax: 0,
    grand_total: 0
};
let currentSaleId = null;
let currentSaleItems = [];

// Simple Toast
function showToast(type, message) {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500',
        warning: 'bg-orange-500'
    };
    
    toast.className = `${colors[type]} text-white px-4 py-3 rounded-lg shadow-lg text-sm`;
    toast.textContent = message;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, 2500);
}

function addProductToCart(element) {
    const productId = element.dataset.productId;
    
    fetch('{{ route("pos.cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cart = data.cart;
            cartSummary = data.cart_summary;
            renderCart();
            showToast('success', 'Ditambahkan ke keranjang');
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        showToast('error', 'Terjadi kesalahan');
        console.error(error);
    });
}

function renderCart() {
    const cartItemsContainer = document.getElementById('cartItems');
    
    if (!cartItemsContainer) return;

    if (Object.keys(cart).length === 0) {
        cartItemsContainer.innerHTML = `
            <div class="text-center py-8 text-gray-400">
                <i class="fas fa-shopping-cart text-4xl mb-2"></i>
                <p>Keranjang masih kosong</p>
            </div>
        `;
    } else {
        let html = '';
        for (const [key, item] of Object.entries(cart)) {
            html += `
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm text-gray-900 truncate">${item.product_name}</p>
                        <p class="text-xs text-gray-500">${item.product_code}</p>
                        <p class="text-sm font-bold text-indigo-600 mt-1">Rp ${formatNumber(item.unit_price)}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="updateQuantity('${key}', ${item.quantity - 1})" 
                                class="w-8 h-8 flex items-center justify-center bg-white border border-gray-300 rounded-lg hover:bg-gray-100">
                            <i class="fas fa-minus text-xs"></i>
                        </button>
                        <input type="number" 
                               value="${item.quantity}" 
                               onchange="updateQuantity('${key}', this.value)"
                               class="w-16 text-center font-semibold border border-gray-300 rounded-lg py-1 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               min="1">
                        <button onclick="updateQuantity('${key}', ${item.quantity + 1})" 
                                class="w-8 h-8 flex items-center justify-center bg-white border border-gray-300 rounded-lg hover:bg-gray-100">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                    </div>
                    <button onclick="removeItem('${key}')" 
                            class="text-red-600 hover:text-red-700 p-2">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            `;
        }
        cartItemsContainer.innerHTML = html;
    }

    const subtotalEl = document.getElementById('cartSubtotal');
    const discountEl = document.getElementById('cartDiscount');
    const taxEl = document.getElementById('cartTax');
    const grandTotalEl = document.getElementById('cartGrandTotal');
    
    if (subtotalEl) subtotalEl.textContent = 'Rp ' + formatNumber(cartSummary.subtotal);
    if (discountEl) discountEl.textContent = '- Rp ' + formatNumber(cartSummary.total_discount);
    if (taxEl) taxEl.textContent = 'Rp ' + formatNumber(cartSummary.tax);
    if (grandTotalEl) grandTotalEl.textContent = 'Rp ' + formatNumber(cartSummary.grand_total);
}

function updateQuantity(cartKey, newQuantity) {
    newQuantity = parseInt(newQuantity);
    
    if (isNaN(newQuantity) || newQuantity < 0) {
        showToast('warning', 'Jumlah tidak valid');
        renderCart(); // refresh untuk reset input
        return;
    }
    
    if (newQuantity === 0) {
        removeItem(cartKey);
        return;
    }
    
    fetch('{{ route("pos.cart.update") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            cart_key: cartKey,
            quantity: newQuantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cart = data.cart;
            cartSummary = data.cart_summary;
            renderCart();
        } else {
            showToast('error', data.message);
            renderCart(); // refresh untuk reset input
        }
    })
    .catch(error => {
        showToast('error', 'Gagal update');
        renderCart(); // refresh untuk reset input
        console.error(error);
    });
}

function removeItem(cartKey) {
    if (!confirm('Hapus item?')) return;
    
    fetch('{{ route("pos.cart.remove") }}', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            cart_key: cartKey
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cart = data.cart;
            cartSummary = data.cart_summary;
            renderCart();
            showToast('success', 'Item dihapus');
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        showToast('error', 'Gagal menghapus');
        console.error(error);
    });
}

function clearCart() {
    if (!confirm('Kosongkan keranjang?')) return;
    
    fetch('{{ route("pos.cart.clear") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cart = {};
            cartSummary = { subtotal: 0, total_discount: 0, tax: 0, grand_total: 0 };
            renderCart();
            showToast('success', 'Keranjang dikosongkan');
        }
    })
    .catch(error => {
        showToast('error', 'Gagal');
        console.error(error);
    });
}

function setCustomer(customerId) {
    fetch('{{ route("pos.customer.set") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            customer_id: customerId || null
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && customerId) {
            showToast('info', 'Customer dipilih');
        }
    });
}

function openCashPaymentModal() {
    if (Object.keys(cart).length === 0) {
        showToast('warning', 'Keranjang kosong');
        return;
    }
    
    document.getElementById('cashModalTotal').textContent = 'Rp ' + formatNumber(cartSummary.grand_total);
    document.getElementById('cashPaidAmount').value = '';
    document.getElementById('changeAmount').textContent = 'Rp 0';
    document.getElementById('cashPaymentModal').classList.remove('hidden');
    
    setTimeout(() => document.getElementById('cashPaidAmount').focus(), 100);
}

function closeCashPaymentModal() {
    document.getElementById('cashPaymentModal').classList.add('hidden');
}

function calculateChange() {
    const paidAmount = parseFloat(document.getElementById('cashPaidAmount').value) || 0;
    const change = paidAmount - cartSummary.grand_total;
    document.getElementById('changeAmount').textContent = 'Rp ' + formatNumber(Math.max(0, change));
}

function processCashPayment() {
    const paidAmount = parseFloat(document.getElementById('cashPaidAmount').value) || 0;
    
    if (paidAmount < cartSummary.grand_total) {
        showToast('error', 'Jumlah kurang');
        return;
    }
    
    closeCashPaymentModal();
    
    fetch('{{ route("payment.cash") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            paid_amount: paidAmount
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update stok produk di UI
            if (data.sale && data.sale.items) {
                updateProductStockFromSaleItems(data.sale.items);
            }

            // Clear cart di backend (session) secara eksplisit
            return fetch('{{ route("pos.cart.clear") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(() => {
                // Reset cart di frontend SETELAH backend clear
                cart = {};
                cartSummary = { subtotal: 0, total_discount: 0, tax: 0, grand_total: 0 };
                renderCart();
                
                // Tampilkan modal success dengan data sale
                openPaymentSuccessModal({
                    sale_id: data.sale.id,
                    invoice_number: data.sale.invoice_number,
                    created_at: data.sale.created_at,
                    grand_total: data.sale.grand_total,
                    change_amount: data.change
                });
            });
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        showToast('error', 'Gagal proses pembayaran');
        console.error(error);
    });
}

function openTransferPaymentModal() {
    if (Object.keys(cart).length === 0) {
        showToast('warning', 'Keranjang kosong');
        return;
    }
    
    document.getElementById('transferModalTotal').textContent = 'Rp ' + formatNumber(cartSummary.grand_total);
    document.getElementById('transferMethod').value = '';
    document.getElementById('transferReference').value = '';
    document.getElementById('transferPaymentModal').classList.remove('hidden');
}

function closeTransferPaymentModal() {
    document.getElementById('transferPaymentModal').classList.add('hidden');
}

function processTransferPayment() {
    const transferMethod = document.getElementById('transferMethod').value;
    const referenceNumber = document.getElementById('transferReference').value;
    
    if (!transferMethod) {
        showToast('warning', 'Pilih metode transfer');
        return;
    }
    
    closeTransferPaymentModal();
    
    fetch('{{ route("payment.transfer") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            transfer_method: transferMethod,
            reference_number: referenceNumber
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update stok produk di UI
            if (data.sale && data.sale.items) {
                updateProductStockFromSaleItems(data.sale.items);
            }

            // Clear cart di backend (session) secara eksplisit
            return fetch('{{ route("pos.cart.clear") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(() => {
                // Reset cart di frontend SETELAH backend clear
                cart = {};
                cartSummary = { subtotal: 0, total_discount: 0, tax: 0, grand_total: 0 };
                renderCart();
                
                openPaymentSuccessModal({
                    sale_id: data.sale.id,
                    invoice_number: data.sale.invoice_number,
                    created_at: data.sale.created_at,
                    grand_total: data.sale.grand_total
                });
            });
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        showToast('error', 'Gagal proses pembayaran');
        console.error(error);
    });
}

function openMidtransPayment() {
    if (Object.keys(cart).length === 0) {
        showToast('warning', 'Keranjang kosong');
        return;
    }
    
    fetch('{{ route("payment.midtrans.token") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            showToast('error', data.message || 'Gagal membuat token Midtrans');
            return;
        }

        // Buka Snap Midtrans
        snap.pay(data.snap_token, {
            onSuccess: function(result) {
                // Setelah pembayaran sukses di Midtrans,
                // ambil data sale lengkap dari backend
                fetch('/api/sale/' + data.sale_id)
                    .then(res => res.json())
                    .then(saleData => {
                        // Kalau API /api/sale/{id} balikin items, update stok di UI
                        if (saleData.items) {
                            updateProductStockFromSaleItems(saleData.items);
                        }

                        // Clear cart di backend (session) secara eksplisit
                        return fetch('{{ route("pos.cart.clear") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(() => {
                            // Reset cart di frontend SETELAH backend clear
                            cart = {};
                            cartSummary = { subtotal: 0, total_discount: 0, tax: 0, grand_total: 0 };
                            renderCart();
                            
                            // Tampilkan modal success dengan data sale
                            openPaymentSuccessModal({
                                sale_id: saleData.id,
                                invoice_number: saleData.invoice_number,
                                created_at: saleData.created_at,
                                grand_total: saleData.grand_total
                            });
                        });
                    })
                    .catch(err => {
                        console.error(err);
                        showToast('error', 'Pembayaran berhasil, tapi gagal mengambil data transaksi');
                    });
            },
            onPending: function(result) {
                showToast('info', 'Menunggu pembayaran');
            },
            onError: function(result) {
                console.error(result);
                showToast('error', 'Pembayaran via Midtrans gagal');
            },
            onClose: function() {
                showToast('info', 'Jendela pembayaran ditutup sebelum selesai');
            }
        });
    })
    .catch(error => {
        console.error(error);
        showToast('error', 'Gagal membuat token');
    });
}

function formatNumber(num) {
    return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function openPaymentSuccessModal(saleData) {
    currentSaleId = saleData.sale_id || saleData.id;
    
    // Update konten modal
    document.getElementById('successInvoiceNumber').textContent = saleData.invoice_number || '-';
    document.getElementById('successDate').textContent = formatDateTime(saleData.created_at || new Date());
    document.getElementById('successTotal').textContent = 'Rp ' + formatNumber(saleData.grand_total || 0);
    
    // Tampilkan kembalian jika cash payment
    if (saleData.change_amount && saleData.change_amount > 0) {
        document.getElementById('successChangeRow').style.display = 'flex';
        document.getElementById('successChange').textContent = 'Rp ' + formatNumber(saleData.change_amount);
    } else {
        document.getElementById('successChangeRow').style.display = 'none';
    }
    
    // Tampilkan modal
    document.getElementById('paymentSuccessModal').classList.remove('hidden');
}

function closePaymentSuccessModal() {
    document.getElementById('paymentSuccessModal').classList.add('hidden');
    currentSaleId = null;
}

function printReceipt() {
    if (!currentSaleId) {
        showToast('error', 'Sale ID tidak ditemukan');
        return;
    }
    
    // Buka halaman print di tab baru
    window.open('/receipt/print/' + currentSaleId, '_blank');
}

function downloadReceipt() {
    if (!currentSaleId) {
        showToast('error', 'Sale ID tidak ditemukan');
        return;
    }
    
    // Tampilkan toast sebelum download
    showToast('info', 'Memproses download...');
    
    // Gunakan teknik download yang tidak memblokir UI
    const link = document.createElement('a');
    link.href = '/receipt/download/' + currentSaleId;
    link.download = ''; // Browser akan gunakan nama file dari server
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Toast sukses setelah trigger download
    setTimeout(() => {
        showToast('success', 'Struk berhasil didownload!');
    }, 500);
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    
    return `${day}/${month}/${year} ${hours}:${minutes}`;
}

function updateProductStockFromSaleItems(saleItems) {
    if (!saleItems || !saleItems.length) return;

    saleItems.forEach(item => {
        const wrapper = document.querySelector(`.stock-display[data-product-id="${item.product_id}"]`);
        if (!wrapper) return;

        const qtySpan = wrapper.querySelector('.stock-qty');
        let currentQty = parseInt(qtySpan.textContent) || 0;
        let newQty = currentQty - item.quantity;

        if (newQty < 0) newQty = 0;

        qtySpan.textContent = newQty;

        // ubah warna hijau/merah sesuai stok
        wrapper.classList.toggle('text-green-600', newQty > 0);
        wrapper.classList.toggle('text-red-600', newQty <= 0);
    });
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCashPaymentModal();
        closePaymentSuccessModal();
        closeTransferPaymentModal();
    }
    
    if (e.key === 'Enter' && !document.getElementById('cashPaymentModal').classList.contains('hidden')) {
        processCashPayment();
    }
});

// Load cart
document.addEventListener('DOMContentLoaded', function() {
    renderCart();
});
</script>
@endpush
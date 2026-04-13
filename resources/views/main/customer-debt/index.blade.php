@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Pelanggan & Piutang - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium">Pelanggan & Piutang</span>
</li>
@endsection

@push('styles')
<style>
    .tab-btn {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    /* Hide scrollbar for Chrome, Safari and Opera */
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    /* Hide scrollbar for IE, Edge and Firefox */
    .scrollbar-hide {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
    .tab-btn.active {
        background-color: #658C58;
        color: white;
        box-shadow: 0 4px 12px rgba(101, 140, 88, 0.25);
    }
    .tab-btn:not(.active):hover {
        background-color: rgba(101, 140, 88, 0.05);
        color: #658C58;
    }
    .debt-row.overdue {
        background-color: #fff1f2;
    }
    .debt-row.overdue:hover {
        background-color: #ffe4e6;
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
        border-color: #658C58;
        background-color: rgba(101, 140, 88, 0.05);
        box-shadow: 0 0 0 3px rgba(101, 140, 88, 0.1);
    }
</style>
@endpush

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-gray-900">
                    Pelanggan & Piutang
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola data pelanggan dan pantau tunggakan/piutang yang masih harus dibayar.
                </p>
            </div>
        </section>

        {{-- RINGKASAN STATISTIK --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-5 py-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Pelanggan</p>
                <p id="statTotalCustomers" class="mt-2 text-2xl font-black text-gray-900">{{ number_format($stats['total_customers'], 0, ',', '.') }}</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Reseller Aktif</p>
                <p id="statActiveResellers" class="mt-2 text-2xl font-black text-amber-600">{{ number_format($stats['active_resellers'], 0, ',', '.') }}</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Piutang</p>
                <p id="statTotalDebt" class="mt-2 text-2xl font-black text-red-600">Rp {{ number_format($stats['total_debt'], 0, ',', '.') }}</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Terbayar Bulan Ini</p>
                <p id="statPaidThisMonth" class="mt-2 text-2xl font-black text-cuan-green">{{ number_format($stats['paid_this_month'], 0, ',', '.') }}</p>
            </div>
        </section>

        {{-- TAB NAVIGATION --}}
        <x-card-container>
            <div class="border-b border-gray-100 px-6 py-4 bg-gray-50/50">
                <div class="flex gap-3 overflow-x-auto pb-2 -mb-2 scrollbar-hide snap-x">
                    @can('lihat pelanggan')
                    <button type="button" id="tabCustomer" onclick="switchTab('customer')"
                            class="tab-btn active px-6 py-3 rounded-xl font-bold text-sm flex items-center gap-2 whitespace-nowrap snap-start shrink-0">
                        <span>Pelanggan</span>
                    </button>
                    @endcan
                    
                    @can('lihat piutang')
                    <button type="button" id="tabDebt" onclick="switchTab('debt')"
                            class="tab-btn px-6 py-3 rounded-xl font-bold text-sm flex items-center gap-2 text-gray-500 border border-gray-200 bg-white whitespace-nowrap snap-start shrink-0">
                        <span>Tunggakan</span>
                    </button>
                    @endcan

                    @can('lihat reseller applications')
                    <button type="button" id="tabSupplier" onclick="switchTab('supplier')"
                            class="tab-btn px-6 py-3 rounded-xl font-bold text-sm flex items-center gap-2 text-gray-500 border border-gray-200 bg-white whitespace-nowrap snap-start shrink-0">
                        <span>Daftar Reseller</span>
                    </button>
                    @endcan
                </div>
            </div>

            {{-- TAB CONTENT: CUSTOMER --}}
            <div id="contentCustomer" class="tab-content">
                {{-- Toolbar --}}
                <div class="px-6 py-5 border-b border-gray-100 bg-white space-y-4 md:space-y-0 md:flex md:items-center md:gap-4">
                    <div class="flex-1">
                        <input type="text" id="searchCustomer" placeholder="Cari nama, kode, atau telepon..."
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all">
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <select id="filterCustomerType"
                                class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all">
                            <option value="">Semua Tipe</option>
                            <option value="regular">Regular</option>
                            <option value="reseller">Reseller</option>
                            <option value="vip">VIP</option>
                        </select>
                        <select id="filterCustomerStatus"
                                class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all">
                            <option value="">Semua Status</option>
                            <option value="active">Aktif</option>
                            <option value="inactive">Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left">Kode</th>
                                <th class="px-6 py-4 text-left">Pelanggan</th>
                                <th class="px-6 py-4 text-left">Telepon</th>
                                <th class="px-6 py-4 text-left">Tipe</th>
                                <th class="px-6 py-4 text-left">Transaksi</th>
                                <th class="px-6 py-4 text-left font-black">Total Belanja</th>
                                <th class="px-6 py-4 text-left">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="customerTableBody" class="divide-y divide-gray-50 bg-white">
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 rounded-full border-4 border-gray-100 border-t-cuan-green animate-spin mb-3"></div>
                                        <p class="font-bold">Memuat data...</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div id="customerPagination" class="px-6 py-4 border-t border-gray-50 bg-gray-50/30 flex items-center justify-between text-sm text-gray-500"></div>
            </div>

            {{-- TAB CONTENT: DEBT --}}
            <div id="contentDebt" class="tab-content hidden" style="display: none;">
                {{-- Toolbar --}}
                <div class="px-6 py-5 border-b border-gray-100 bg-white space-y-4 md:space-y-0 md:flex md:items-center md:gap-4">
                    <div class="flex-1">
                        <input type="text" id="searchDebt" placeholder="Cari invoice atau nama pelanggan..."
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all">
                    </div>
                    <div class="w-full md:w-48">
                        <select id="filterDebtStatus"
                                class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all">
                            <option value="">Semua Status</option>
                            <option value="unpaid">Belum Bayar</option>
                            <option value="partial">Sebagian</option>
                        </select>
                    </div>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left">Invoice</th>
                                <th class="px-6 py-4 text-left">Pelanggan</th>
                                <th class="px-6 py-4 text-left">Tanggal</th>
                                <th class="px-6 py-4 text-left font-black">Total</th>
                                <th class="px-6 py-4 text-left">Dibayar</th>
                                <th class="px-6 py-4 text-left">Sisa</th>
                                <th class="px-6 py-4 text-left">Jatuh Tempo</th>
                                <th class="px-6 py-4 text-left">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="debtTableBody" class="divide-y divide-gray-50 bg-white">
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 rounded-full border-4 border-gray-100 border-t-cuan-green animate-spin mb-3"></div>
                                        <p class="font-bold">Memuat data...</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div id="debtPagination" class="px-6 py-4 border-t border-gray-50 bg-gray-50/30 flex items-center justify-between text-sm text-gray-500"></div>
            </div>

            {{-- TAB CONTENT: SUPPLIER --}}
            <div id="contentSupplier" class="tab-content hidden" style="display: none;">
                {{-- Toolbar --}}
                <div class="px-6 py-5 border-b border-gray-100 bg-white">
                    <input type="text" id="searchSupplier" placeholder="Cari nama atau telepon supplier..."
                           class="w-full max-w-md px-4 py-2.5 rounded-xl border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all">
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left">Supplier</th>
                                <th class="px-6 py-4 text-left">Kontak</th>
                                <th class="px-6 py-4 text-left">Tipe</th>
                                <th class="px-6 py-4 text-left">Diterima</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="supplierTableBody" class="divide-y divide-gray-50 bg-white">
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 rounded-full border-4 border-gray-100 border-t-cuan-green animate-spin mb-3"></div>
                                        <p class="font-bold">Memuat data...</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div id="supplierPagination" class="px-6 py-4 border-t border-gray-50 bg-gray-50/30 flex items-center justify-between text-sm text-gray-500"></div>
            </div>
        </x-card-container>
    </div>
</main>

{{-- PAYMENT MODAL --}}
<div id="paymentModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 transition-all duration-300" style="display: none; backdrop-filter: blur(8px);">
    <div class="bg-white rounded-[2rem] shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto transform transition-all scale-100 opacity-100 border border-gray-100">
        {{-- Header --}}
        <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-black text-gray-900">
                    Pembayaran Utang
                </h3>
                <p class="text-xs text-gray-400 mt-1">Selesaikan kewajiban pembayaran pelanggan.</p>
            </div>
            <button onclick="closePaymentModal()" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-gray-100 transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-8">
            {{-- Debt Info --}}
            <div class="bg-gray-50 rounded-2xl p-6 mb-8 border border-gray-100/50">
                <div class="grid grid-cols-2 gap-y-6 gap-x-4 text-sm">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Invoice</p>
                        <p id="modalInvoice" class="font-bold text-gray-900 mt-1">-</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Pelanggan</p>
                        <p id="modalCustomer" class="font-bold text-gray-900 mt-1">-</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Utang</p>
                        <p id="modalTotal" class="font-bold text-gray-900 mt-1">-</p>
                    </div>
                    <div id="modalLateFeeRow" class="hidden">
                        <p class="text-[10px] font-black uppercase tracking-widest text-amber-500">Denda Keterlambatan</p>
                        <p id="modalLateFee" class="font-bold text-amber-600 mt-1">-</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-red-400">Sisa Tagihan Pokok</p>
                        <p id="modalRemaining" class="font-black text-red-600 text-lg mt-1">-</p>
                    </div>
                    <div id="modalTotalPlusFeeRow" class="hidden">
                         <p class="text-[10px] font-black uppercase tracking-widest text-red-600 font-black">Total Harus Bayar</p>
                        <p id="modalTotalPlusFee" class="font-black text-red-700 text-xl mt-1">-</p>
                    </div>
                </div>
            </div>

            {{-- Payment Amount --}}
            <div class="mb-8">
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Jumlah Pembayaran</label>
                <div class="relative">
                    <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                    <input type="number" id="paymentAmount" min="1"
                           class="w-full pl-12 pr-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-xl font-black text-gray-900 focus:outline-none focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all"
                           placeholder="0">
                </div>
                <div class="flex gap-3 mt-4">
                    <button type="button" onclick="setPaymentAmount(0.5)" class="flex-1 py-3 text-xs font-bold bg-gray-50 border border-gray-100 text-gray-600 rounded-xl hover:bg-gray-100 transition-all active:scale-95">50%</button>
                    <button type="button" onclick="setPaymentAmount(1)" class="flex-1 py-3 text-xs font-bold bg-cuan-green/10 text-cuan-green rounded-xl hover:bg-cuan-green/20 transition-all active:scale-95">Lunas</button>
                </div>
            </div>

            {{-- Payment Method --}}
            <div class="mb-8">
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4">Metode Pembayaran</label>
                <div class="grid grid-cols-3 gap-4">
                    <div onclick="selectPaymentMethod('cash')" class="payment-method-card selected border border-gray-200 rounded-2xl p-5 text-center group" data-method="cash">
                        <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-money-bill-wave text-cuan-green text-lg"></i>
                        </div>
                        <p class="text-xs font-bold text-gray-900 uppercase">Tunai</p>
                    </div>
                    <div onclick="selectPaymentMethod('transfer')" class="payment-method-card border border-gray-200 rounded-2xl p-5 text-center group" data-method="transfer">
                        <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-university text-blue-500 text-lg"></i>
                        </div>
                        <p class="text-xs font-bold text-gray-900 uppercase">Transfer</p>
                    </div>
                    <div @if(config('app.env') === 'production') onclick="Swal.fire({title: 'Informasi', text: 'Midtrans sedang dalam penanganan (maintenance). Silakan pilih metode pembayaran lain.', icon: 'info', customClass: {popup: 'rounded-3xl'}})" @else onclick="selectPaymentMethod('qris')" @endif class="payment-method-card border border-gray-200 rounded-2xl p-5 text-center group" data-method="qris">
                        <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-qrcode text-amber-500 text-lg"></i>
                        </div>
                        <p class="text-xs font-bold text-gray-900 uppercase">QRIS</p>
                    </div>
                </div>
            </div>

            {{-- Transfer Options --}}
            <div id="transferOptions" class="mb-8 hidden animate-slideDown">
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-4">Pilih Rekening Tujuan:</label>
                <div class="grid grid-cols-2 gap-3 mb-6">
                    @forelse($outletPaymentLinks as $link)
                        <div onclick="selectTransferMethod(this, '{{ $link->id }}', '{{ $link->paymentMethod->name }}', '{{ $link->account_number }}', '{{ $link->account_name }}', '{{ $link->qr_image ? Storage::url($link->qr_image) : '' }}')" 
                             class="transfer-method-card flex flex-col items-center justify-center p-4 border border-gray-100 rounded-2xl cursor-pointer hover:bg-gray-50 transition-all text-center group"
                             data-link-id="{{ $link->id }}">
                            <div class="w-10 h-10 flex items-center justify-center mb-2">
                                @if($link->paymentMethod->icon && Storage::disk('public')->exists($link->paymentMethod->icon))
                                    <img src="{{ Storage::url($link->paymentMethod->icon) }}" class="w-full h-full object-contain filter group-hover:grayscale-0 grayscale transition-all">
                                @else
                                    <div class="w-full h-full rounded-full bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-white transition-colors">
                                        <i class="fas fa-university text-sm"></i>
                                    </div>
                                @endif
                            </div>
                            <p class="text-[10px] font-black text-gray-900 leading-tight uppercase tracking-wider">{{ $link->paymentMethod->name }}</p>
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-6 text-gray-400 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                            <p class="text-xs">Belum ada metode transfer.</p>
                        </div>
                    @endforelse
                </div>
                
                <div id="selectedTransferDetail" class="hidden mb-6 p-5 bg-cuan-green/5 border border-cuan-green/10 rounded-2xl animate-fadeIn">
                    <div class="flex flex-col items-center text-center">
                        <p class="text-[10px] text-cuan-green font-black uppercase tracking-widest mb-3" id="transferMethodLabel">-</p>
                        
                        <div id="transferAccInfoSection">
                            <p class="text-lg font-black font-mono text-gray-900 tracking-wider mb-1" id="transferAccNumber">-</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest" id="transferAccName">-</p>
                        </div>

                        <div id="transferQrSection" class="hidden mt-2">
                            <img id="transferQrImage" src="" class="w-48 h-48 object-contain rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                            <p class="text-[10px] text-cuan-green font-bold uppercase tracking-widest mt-4">Scan QR untuk membayar</p>
                        </div>
                    </div>
                </div>

                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Nomor Referensi (Opsional)</label>
                <input type="text" id="referenceNumber" 
                       class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 focus:outline-none focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all"
                       placeholder="Masukkan nomor referensi transfer">
            </div>

            <input type="hidden" id="selectedOutletPaymentLinkId" value="">

            {{-- Notes --}}
            <div class="mb-8">
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Catatan (Opsional)</label>
                <textarea id="paymentNotes" rows="2"
                          class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 focus:outline-none focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all"
                          placeholder="Tambahkan catatan pembayaran"></textarea>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-8 py-6 border-t border-gray-100 flex gap-4 bg-gray-50/50">
            <button onclick="closePaymentModal()" class="flex-1 px-6 py-4 bg-white border border-gray-200 text-gray-600 rounded-2xl font-bold text-sm hover:bg-gray-50 transition-all active:scale-95">
                Batal
            </button>
            <button onclick="processPayment()" id="btnProcessPayment" class="flex-1 px-6 py-4 bg-cuan-green text-white rounded-2xl font-black text-sm hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                Bayar Sekarang
            </button>
        </div>
    </div>
</div>

{{-- HISTORY MODAL --}}
<div id="historyModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 transition-all duration-300" style="display: none; backdrop-filter: blur(8px);">
    <div class="bg-white rounded-[2rem] shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col border border-gray-100">
        {{-- Header --}}
        <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-black text-gray-900">
                    Riwayat Transaksi
                </h3>
                <p id="historyCustomerName" class="text-xs text-gray-400 mt-1 uppercase tracking-widest font-bold">-</p>
            </div>
            <button onclick="closeHistoryModal()" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-gray-100 transition-all">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto p-8">
            <div class="overflow-x-auto rounded-2xl border border-gray-100">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-black uppercase tracking-widest border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left">Invoice</th>
                            <th class="px-6 py-4 text-left">Tanggal</th>
                            <th class="px-6 py-4 text-center">Item</th>
                            <th class="px-6 py-4 text-left">Metode</th>
                            <th class="px-6 py-4 text-left font-black">Total</th>
                            <th class="px-6 py-4 text-left">Tunggakan</th>
                            <th class="px-6 py-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody" class="divide-y divide-gray-100 bg-white">
                        {{-- Data injected here --}}
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-8 py-6 border-t border-gray-100 bg-gray-50/50 flex justify-end">
            <button onclick="closeHistoryModal()" class="px-8 py-3 bg-white border border-gray-200 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-50 transition-all active:scale-95">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- SUPPLIER DETAIL MODAL --}}
<div id="supplierDetailModal" class="hidden fixed inset-0 bg-black/60 z-[60] flex items-center justify-center p-4 transition-all duration-500" style="display: none; backdrop-filter: blur(8px);">
    <div class="relative bg-white rounded-[2.5rem] shadow-2xl max-w-lg w-full max-h-[90vh] overflow-hidden flex flex-col transform transition-all border border-gray-100">
        
        {{-- Fixed Close Button --}}
        <button onclick="closeSupplierDetail()" class="absolute top-5 right-5 w-10 h-10 flex items-center justify-center text-white bg-white/10 hover:bg-white/20 transition-all rounded-full backdrop-blur-md z-[70] border border-white/20">
            <i class="fas fa-times"></i>
        </button>

        {{-- Header with Visual Decoration (Static) --}}
        <div class="relative h-32 bg-gray-900 shrink-0 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-cuan-green/40 to-cuan-dark/40 mix-blend-overlay"></div>
            <div class="absolute -bottom-10 left-10">
                <div class="w-24 h-24 bg-white rounded-3xl shadow-xl flex items-center justify-center border-4 border-white overflow-hidden">
                    <span id="modalSupplierInitial" class="text-3xl font-black text-cuan-green uppercase leading-none">-</span>
                </div>
            </div>
        </div>

        {{-- Scrollable Content --}}
        <div class="flex-1 overflow-y-auto scrollbar-hide">
            <div class="pt-20 pb-12 px-10">
                <div class="mb-8">
                    <h3 id="modalSupplierName" class="text-2xl font-black text-gray-900 leading-tight">-</h3>
                    <div class="flex items-center gap-3 mt-2">
                        <span id="modalSupplierCode" class="text-[10px] font-black font-mono text-cuan-green bg-cuan-green/10 px-3 py-1 rounded-full uppercase tracking-widest">-</span>
                        <span id="modalSupplierType" class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">-</span>
                    </div>
                </div>

                <div class="space-y-4">
                    {{-- Contact Info cards --}}
                    <div class="flex items-center gap-5 p-4 bg-gray-50 rounded-2xl group border border-transparent hover:border-gray-100 transition-all">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                            <i class="fas fa-phone text-emerald-500"></i>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Telepon / WhatsApp</p>
                            <p id="modalSupplierPhone" class="text-sm font-bold text-gray-900">-</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-5 p-4 bg-gray-50 rounded-2xl group border border-transparent hover:border-gray-100 transition-all">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                            <i class="fas fa-envelope text-blue-500"></i>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Email</p>
                            <p id="modalSupplierEmail" class="text-sm font-bold text-gray-900">-</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-5 p-4 bg-gray-50 rounded-2xl group border border-transparent hover:border-gray-100 transition-all">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform mt-1">
                            <i class="fas fa-map-marker-alt text-amber-500"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Alamat Lengkap</p>
                            <p id="modalSupplierAddress" class="text-sm font-bold text-gray-900 leading-relaxed">-</p>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl border border-dashed border-gray-200 flex items-center justify-between">
                        <div>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Mitra Sejak</p>
                            <p id="modalSupplierAcceptedAt" class="text-xs font-bold text-cuan-green uppercase">-</p>
                        </div>
                        <span class="px-4 py-1.5 bg-cuan-green/10 text-cuan-green rounded-full text-[10px] font-black uppercase tracking-widest">
                            Status Aktif
                        </span>
                    </div>
                </div>

                {{-- CTA --}}
                <div class="mt-10">
                    <a id="btnSupplierWa" href="#" target="_blank" class="flex items-center justify-center gap-3 w-full bg-[#1FAF38] hover:bg-[#199C31] text-white py-5 rounded-[1.5rem] font-black text-sm shadow-xl shadow-green-200 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <i class="fab fa-whatsapp text-xl"></i>
                        <span>Hubungi Reseller</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('modals')
{{-- PAYMENT SUCCESS MODAL --}}
<div id="paymentSuccessModal" class="hidden fixed inset-0 bg-black/60 z-[70] flex items-center justify-center p-4 transition-all duration-300" style="display: none; backdrop-filter: blur(12px);">
    <div class="bg-white rounded-[2.5rem] shadow-2xl max-w-md w-full p-10 text-center transform transition-all border border-gray-100">
        
        
        <h3 class="text-2xl font-black text-gray-900 mb-2">Pembayaran Berhasil!</h3>
        <p class="text-gray-400 text-sm mb-8">Terima kasih, pembayaran telah berhasil diproses dan dicatat ke dalam sistem.</p>
        
        <div class="bg-gray-50 rounded-2xl p-6 mb-8 border border-gray-100/50 space-y-4 text-sm">
            <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                <span class="font-bold text-gray-400 uppercase tracking-widest text-[9px]">Invoice</span>
                <span id="successInvoiceNumber" class="font-black text-gray-900 font-mono tracking-widest">-</span>
            </div>
            <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                <span class="font-bold text-gray-400 uppercase tracking-widest text-[9px]">Pelanggan</span>
                <span id="successCustomer" class="font-black text-gray-900">-</span>
            </div>
            <div class="flex justify-between items-center pb-3 border-b border-gray-100 text-cuan-green">
                <span class="font-bold uppercase tracking-widest text-[9px]">Jumlah Bayar</span>
                <span id="successTotal" class="font-black text-lg">-</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="font-bold text-gray-400 uppercase tracking-widest text-[9px]">Sisa Utang</span>
                <span id="successDebt" class="font-black text-red-500">-</span>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4">
            <button onclick="printReceipt()" class="flex flex-col items-center justify-center gap-2 px-4 py-5 bg-white border border-gray-200 text-gray-600 rounded-2xl font-bold text-xs hover:bg-gray-50 transition-all hover:scale-105 active:scale-95">
                <i class="fas fa-print text-xl text-gray-400"></i>
                Cetak Struk
            </button>
            <button onclick="handlePrintInvoice()" class="flex flex-col items-center justify-center gap-2 px-4 py-5 bg-white border border-gray-200 text-gray-600 rounded-2xl font-bold text-xs hover:bg-gray-50 transition-all hover:scale-105 active:scale-95">
                <i class="fas fa-file-invoice text-xl text-gray-400"></i>
                Cetak Invoice
            </button>
            <button onclick="downloadReceipt()" class="col-span-2 py-4 bg-cuan-green text-white rounded-2xl font-black text-sm hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95 flex items-center justify-center gap-3">
                <i class="fas fa-download"></i>
                Download PDF
            </button>
            <button onclick="closePaymentSuccessModal()" class="col-span-2 py-3 text-gray-400 font-bold text-xs hover:text-gray-600 transition-all uppercase tracking-widest">
                Kembali ke Daftar
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
    // Global SweetAlert2 notification handler
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: @json(session('success')),
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            iconColor: '#658C58',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black text-gray-900',
                htmlContainer: 'text-sm font-medium text-gray-500'
            }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: @json(session('error')),
            confirmButtonColor: '#ef4444',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black text-gray-900',
                htmlContainer: 'text-sm font-medium text-gray-500',
                confirmButton: 'rounded-xl px-8 py-3 font-bold text-sm'
            }
        });
    @endif

    let currentTab = 'customer';
    let customerPage = 1;
    let debtPage = 1;
    let supplierPage = 1;

    let currentDebt = null;
    let selectedPaymentMethod = 'cash';

    // URL Persistence Helpers
    function updateUrlParams(params) {
        const url = new URL(window.location);
        Object.keys(params).forEach(key => {
            if (params[key] === null || params[key] === '') {
                url.searchParams.delete(key);
            } else {
                url.searchParams.set(key, params[key]);
            }
        });
        window.history.pushState({}, '', url);
    }

    // Format currency
    function formatRupiah(amount) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
    }

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

    function switchTab(tab) {
        currentTab = tab;
        
        // Update URL
        const url = new URL(window.location);
        url.searchParams.set('tab', tab);
        window.history.pushState({}, '', url);

        // Update Buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
            btn.classList.add('text-gray-500', 'border-gray-200', 'bg-white');
        });
        const activeBtn = document.getElementById('tab' + tab.charAt(0).toUpperCase() + tab.slice(1));
        activeBtn.classList.add('active');
        activeBtn.classList.remove('text-gray-500', 'border-gray-200', 'bg-white');

        // Show Content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
            content.style.display = 'none';
        });
        
        const activeContent = document.getElementById('content' + tab.charAt(0).toUpperCase() + tab.slice(1));
        activeContent.classList.remove('hidden');
        activeContent.style.display = 'block';

        // Load Data
        if (tab === 'customer') loadCustomers();
        else if (tab === 'debt') loadDebts();
        else if (tab === 'supplier') loadSuppliers();
    }

function loadStats() {
    fetch('{{ url("customer-debts/stats") }}')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('statTotalCustomers').textContent = new Intl.NumberFormat('id-ID').format(data.stats.total_customers);
                document.getElementById('statActiveResellers').textContent = new Intl.NumberFormat('id-ID').format(data.stats.active_resellers);
                document.getElementById('statTotalDebt').textContent = formatRupiah(data.stats.total_debt);
                document.getElementById('statPaidThisMonth').textContent = formatRupiah(data.stats.paid_this_month);
            }
        })
        .catch(err => console.error('Load stats error:', err));
}

// Load customers
function loadCustomers(page = 1) {
    const urlParams = new URLSearchParams(window.location.search);
    const search = urlParams.get('search') || document.getElementById('searchCustomer').value;
    const type = urlParams.get('type') || document.getElementById('filterCustomerType').value;
    const status = urlParams.get('status') || document.getElementById('filterCustomerStatus').value;
    
    // Sync inputs if coming from URL
    if (urlParams.has('search')) document.getElementById('searchCustomer').value = search;
    if (urlParams.has('type')) document.getElementById('filterCustomerType').value = type;
    if (urlParams.has('status')) document.getElementById('filterCustomerStatus').value = status;
    
    customerPage = page;
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
                <td colspan="8" class="px-6 py-16 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4 transition-transform hover:scale-110">
                            <i class="fas fa-users text-2xl text-gray-200"></i>
                        </div>
                        <p class="text-gray-900 font-bold mb-1">Belum ada pelanggan</p>
                        <p class="text-gray-400 text-xs">Pelanggan akan muncul setelah ada transaksi dalam sistem.</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = customers.map(c => `
        <tr class="hover:bg-gray-50/50 transition-colors group">
            <td class="px-6 py-5">
                <span class="inline-flex items-center rounded-lg bg-gray-100 px-3 py-1.5 text-[10px] font-black text-gray-600 font-mono tracking-widest">
                    ${c.code || '-'}
                </span>
            </td>
            <td class="px-6 py-5">
                <div class="font-bold text-gray-900 group-hover:text-cuan-green transition-colors">${c.name}</div>
                ${c.email ? `<div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">${c.email}</div>` : ''}
            </td>
            <td class="px-6 py-5">
                <div class="flex items-center gap-2">
                    <span class="text-gray-600 font-medium">${c.phone || '-'}</span>
                </div>
            </td>
            <td class="px-6 py-5">
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest ${getTypeBadgeClass(c.type)}">
                    ${getTypeLabel(c.type)}
                </span>
            </td>
            <td class="px-6 py-5">
                <div class="flex flex-col">
                    <span class="font-bold text-gray-900">${c.sales_count || 0}</span>
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Transaksi</span>
                </div>
            </td>
            <td class="px-6 py-5">
                <div class="text-sm font-black text-gray-900">${formatRupiah(c.sales_sum_grand_total || 0)}</div>
            </td>
            <td class="px-6 py-5">
                ${c.is_active 
                    ? '<span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-50 text-emerald-700 border border-emerald-100/50">Aktif</span>'
                    : '<span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-100 text-gray-500">Nonaktif</span>'
                }
            </td>
            <td class="px-6 py-5 text-center">
                <button onclick="openHistoryModal(${c.id})" 
                        class="px-4 py-2 rounded-xl bg-cuan-green/10 text-cuan-green hover:bg-cuan-green hover:text-white transition-all text-[10px] font-black uppercase tracking-widest border border-cuan-green/10">
                    Riwayat
                </button>
            </td>
        </tr>
    `).join('');
    
    renderPagination('customerPagination', pagination, loadCustomers);
}

// Load debts
function loadDebts(page = 1) {
    const urlParams = new URLSearchParams(window.location.search);
    const search = urlParams.get('debt_search') || document.getElementById('searchDebt').value;
    const status = urlParams.get('debt_status') || document.getElementById('filterDebtStatus').value;
    
    // Sync inputs if coming from URL
    if (urlParams.has('debt_search')) document.getElementById('searchDebt').value = search;
    if (urlParams.has('debt_status')) document.getElementById('filterDebtStatus').value = status;
    
    debtPage = page;
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
                <td colspan="9" class="px-6 py-16 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mb-4 transition-transform hover:scale-110">
                            <i class="fas fa-check text-2xl text-emerald-500"></i>
                        </div>
                        <p class="text-gray-900 font-bold mb-1">Semua Terbayar Lunas</p>
                        <p class="text-gray-400 text-xs">Tidak ada pituang yang menunggak saat ini.</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = debts.map(d => `
        <tr class="debt-row ${d.is_overdue ? 'overdue' : ''} hover:bg-gray-50/50 transition-colors group">
            <td class="px-6 py-5">
                <span class="inline-flex items-center rounded-lg bg-cuan-green/10 px-3 py-1.5 text-[10px] font-black text-cuan-green font-mono tracking-widest border border-cuan-green/10">
                    ${d.invoice_number}
                </span>
            </td>
            <td class="px-6 py-5">
                <div class="font-bold text-gray-900">${d.customer_name}</div>
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">${d.customer_phone || '-'}</div>
            </td>
            <td class="px-6 py-5 text-gray-600 text-xs font-medium">${d.created_at}</td>
            <td class="px-6 py-5 font-black text-gray-900">
                <div class="flex flex-col">
                    <span>${formatRupiah(d.amount)}</span>
                    ${d.late_fee > 0 ? `<span class="text-[9px] text-amber-600 font-bold uppercase tracking-widest">+ ${formatRupiah(d.late_fee)} (Denda)</span>` : ''}
                </div>
            </td>
            <td class="px-6 py-5 text-cuan-green font-black">${formatRupiah(d.paid_amount)}</td>
            <td class="px-6 py-5 font-black text-red-600">
                <div class="flex flex-col">
                    <span>${formatRupiah(d.remaining_amount)}</span>
                    ${d.late_fee > 0 ? `<span class="text-[9px] text-red-400 font-black uppercase tracking-tighter">Total: ${formatRupiah(d.total_plus_fee)}</span>` : ''}
                </div>
            </td>
            <td class="px-6 py-5">
                ${d.due_date 
                    ? `<div class="flex flex-col"><span class="${d.is_overdue ? 'text-red-600 font-black' : 'text-gray-600 font-bold'} text-xs">${d.due_date}</span>${d.is_overdue ? `<span class="text-[9px] font-black uppercase text-red-400 tracking-tighter mt-0.5">lewat ${d.days_overdue} hari</span>` : ''}</div>`
                    : '<span class="text-gray-300">-</span>'
                }
            </td>
            <td class="px-6 py-5">
                ${getDebtStatusBadge(d.status, d.is_overdue)}
            </td>
            <td class="px-6 py-5 text-center">
                @can('bayar piutang')
                <button onclick="openPaymentModal(${JSON.stringify(d).replace(/"/g, '&quot;')})"
                        class="px-6 py-2.5 rounded-xl bg-cuan-green text-white text-[10px] font-black uppercase tracking-widest hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/10 active:scale-95">
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
    const urlParams = new URLSearchParams(window.location.search);
    const search = urlParams.get('supplier_search') || document.getElementById('searchSupplier').value;
    
    // Sync inputs if coming from URL
    if (urlParams.has('supplier_search')) document.getElementById('searchSupplier').value = search;
    
    supplierPage = page;
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
                <td colspan="5" class="px-6 py-16 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4 transition-transform hover:scale-110">
                            <i class="fas fa-truck-loading text-2xl text-gray-200"></i>
                        </div>
                        <p class="text-gray-900 font-bold mb-1">Belum ada Reseller</p>
                        <p class="text-gray-400 text-xs">Supplier (Reseller) muncul setelah aplikasi diterima.</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = suppliers.map(s => `
        <tr class="hover:bg-gray-50/50 transition-colors group">
            <td class="px-6 py-5">
                <div class="font-bold text-gray-900 group-hover:text-cuan-green transition-colors">${s.name}</div>
                <div class="text-[10px] font-black font-mono text-gray-400 uppercase tracking-widest mt-1">${s.code || '-'}</div>
            </td>
            <td class="px-6 py-5">
                <div class="text-sm font-bold text-gray-900">${s.phone || '-'}</div>
                <div class="text-[10px] font-black text-gray-400 tracking-widest mt-1 uppercase">${s.email || '-'}</div>
            </td>
             <td class="px-6 py-5">
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest ${getTypeBadgeClass(s.type)}">
                    ${getTypeLabel(s.type)}
                </span>
            </td>
            <td class="px-6 py-5 text-gray-400 text-xs font-bold uppercase tracking-widest">${s.accepted_at}</td>
            <td class="px-6 py-5 text-center">
                <div class="flex items-center justify-center gap-2">
                    <button onclick='openSupplierDetail(${JSON.stringify(s).replace(/'/g, "&#39;")})'
                            class="px-5 py-2.5 rounded-xl bg-cuan-green/10 text-cuan-green text-[10px] font-black uppercase tracking-widest hover:bg-cuan-green hover:text-white transition-all border border-cuan-green/10 shadow-sm" title="Detail Supplier">
                        Detail
                    </button>
                    <button onclick="cancelSupplierContract(${s.id}, '${s.name}')" 
                            class="px-5 py-2.5 rounded-xl bg-red-50 text-red-600 text-[10px] font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all border border-red-100 shadow-sm" title="Batalkan Kontrak">
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
    modal.style.display = 'flex';
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
        modal.style.display = 'none';
    }, 200);
}

function cancelSupplierContract(id, name) {
    Swal.fire({
        title: 'Batalkan Kontrak?',
        text: `Apakah Anda yakin ingin menghentikan hubungan reseller dengan "${name}"? Status pelanggan akan kembali menjadi Reguler.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Putuskan Mitra',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'rounded-[2rem] border-none shadow-2xl',
            title: 'font-black text-gray-900',
            htmlContainer: 'text-sm font-medium text-gray-500',
            confirmButton: 'rounded-xl px-6 py-3 font-bold text-sm',
            cancelButton: 'rounded-xl px-6 py-3 font-bold text-sm'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                customClass: {
                    popup: 'rounded-3xl border-none shadow-2xl'
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
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 2000,
                        iconColor: '#658C58',
                        customClass: {
                            popup: 'rounded-3xl border-none shadow-2xl'
                        }
                    });
                    loadSuppliers(supplierPage);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message,
                        confirmButtonColor: '#ef4444',
                        customClass: {
                            popup: 'rounded-3xl border-none shadow-2xl'
                        }
                    });
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
        container.innerHTML = `<span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total: ${pagination.total} Data</span>`;
        return;
    }
    
    let pagesHtml = '';
    const startPage = Math.max(1, pagination.current_page - 2);
    const endPage = Math.min(pagination.last_page, pagination.current_page + 2);

    for (let i = startPage; i <= endPage; i++) {
        if (i === pagination.current_page) {
            pagesHtml += `<button class="w-10 h-10 flex items-center justify-center bg-cuan-green text-white rounded-xl font-black shadow-lg shadow-cuan-green/20">${i}</button>`;
        } else {
            pagesHtml += `<button onclick="${loadFunction.name}(${i})" class="w-10 h-10 flex items-center justify-center bg-white border border-gray-100 text-gray-600 rounded-xl hover:bg-gray-50 transition-all font-bold">${i}</button>`;
        }
    }
    
    container.innerHTML = `
        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
            Halaman ${pagination.current_page} - ${pagination.last_page} (${pagination.total} Data)
        </span>
        <div class="flex gap-2">${pagesHtml}</div>
    `;
}

// Helper functions
function getTypeBadgeClass(type) {
    switch (type) {
        case 'vip': return 'bg-amber-50 text-amber-700 border border-amber-100/50';
        case 'reseller': return 'bg-blue-50 text-blue-700 border border-blue-100/50';
        default: return 'bg-gray-100 text-gray-400 border border-gray-200/50';
    }
}

function getTypeLabel(type) {
    switch (type) {
        case 'vip': return 'VIP';
        case 'reseller': return 'Reseller';
        default: return 'Reguler';
    }
}

function getDebtStatusBadge(status, isOverdue) {
    if (isOverdue) {
        return '<span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-red-50 text-red-700 border border-red-100/50">Terlambat</span>';
    }
    switch (status) {
        case 'partial':
            return '<span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-amber-50 text-amber-700 border border-amber-100/50">Sebagian</span>';
        default:
            return '<span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-red-50 text-red-700 border border-red-100/50">Belum Bayar</span>';
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
    
    const lateFeeRow = document.getElementById('modalLateFeeRow');
    const totalPlusFeeRow = document.getElementById('modalTotalPlusFeeRow');
    
    if (debt.late_fee > 0) {
        document.getElementById('modalLateFee').textContent = formatRupiah(debt.late_fee);
        document.getElementById('modalTotalPlusFee').textContent = formatRupiah(debt.total_plus_fee);
        lateFeeRow.classList.remove('hidden');
        totalPlusFeeRow.classList.remove('hidden');
    } else {
        lateFeeRow.classList.add('hidden');
        totalPlusFeeRow.classList.add('hidden');
    }

    document.getElementById('paymentAmount').value = '';
    document.getElementById('paymentAmount').max = debt.total_plus_fee;
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
    document.getElementById('paymentModal').style.display = 'flex';
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
    document.getElementById('paymentModal').style.display = 'none';
}

function setPaymentAmount(percent) {
    if (!currentDebt) return;
    const amount = Math.floor(currentDebt.total_plus_fee * percent);
    document.getElementById('paymentAmount').value = amount;
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
    modal.style.display = 'flex';
    nameEl.textContent = 'Memuat data...';
    tbody.innerHTML = `
        <tr>
            <td colspan="7" class="px-6 py-16 text-center">
                <div class="flex flex-col items-center">
                    <div class="w-12 h-12 rounded-full border-4 border-gray-100 border-t-cuan-green animate-spin mb-3"></div>
                    <p class="text-gray-400 font-bold">Mengambil riwayat...</p>
                </div>
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
                            <td colspan="7" class="px-6 py-16 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-history text-2xl text-gray-200"></i>
                                    </div>
                                    <p class="font-bold text-gray-900 mb-1">Belum ada riwayat</p>
                                    <p class="text-xs text-gray-400">Belum ada transaksi tercatat untuk pelanggan ini.</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    return;
                }
                
                tbody.innerHTML = data.sales.map(s => `
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center rounded-lg bg-cuan-green/10 px-3 py-1.5 text-[10px] font-black text-cuan-green font-mono tracking-widest border border-cuan-green/10 cursor-pointer" onclick="window.location.href='{{ url('sales') }}/${s.id}'">
                                ${s.invoice_number}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Tanggal</div>
                            <div class="text-sm font-bold text-gray-900">${s.date}</div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <div class="inline-flex flex-col items-center">
                                <span class="font-black text-gray-900">${s.items_count}</span>
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Item</span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">${s.payment_method}</span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm font-black text-gray-900">${formatRupiah(s.grand_total)}</div>
                        </td>
                        <td class="px-6 py-5">
                            ${s.remaining_debt > 0 
                                ? `<span class="text-xs font-black text-red-500">${formatRupiah(s.remaining_debt)}</span>`
                                : '<span class="text-gray-300">-</span>'
                            }
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest ${getStatusBadgeClass(s.status)}">
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
                    <td colspan="7" class="px-6 py-16 text-center text-red-500">
                        <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                        <p class="font-bold">Gagal memuat data</p>
                    </td>
                </tr>
            `;
        });
}

function closeHistoryModal() {
    document.getElementById('historyModal').classList.add('hidden');
    document.getElementById('historyModal').style.display = 'none';
}

function getStatusBadgeClass(status) {
    switch (status) {
        case 'completed': return 'bg-emerald-50 text-emerald-700 border border-emerald-100/50';
        case 'debt': return 'bg-red-50 text-red-700 border border-red-100/50';
        case 'pending': return 'bg-amber-50 text-amber-700 border border-amber-100/50';
        case 'canceled': return 'bg-gray-100 text-gray-500 border border-gray-200/50';
        case 'refunded': return 'bg-purple-50 text-purple-700 border border-purple-100/50';
        default: return 'bg-gray-50 text-gray-400 border border-gray-200/50';
    }
}

function getStatusLabel(status) {
    switch (status) {
        case 'completed': return 'Selesai';
        case 'debt': return 'Pituang';
        case 'pending': return 'Pending';
        case 'canceled': return 'Batal';
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
    
    if (amount > currentDebt.total_plus_fee) {
        Swal.fire('Error', 'Jumlah pembayaran melebihi total tagihan (termasuk denda)', 'error');
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
    
    // Show Loading Modal
    Swal.fire({
        title: 'Memproses...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    const btn = document.getElementById('btnProcessPayment');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';

    fetch(`/customer-debts/${currentDebt.id}/pay`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify(data),
    })
    .then(r => {
        if (!r.ok) throw new Error('Network response was not ok');
        return r.json();
    })
    .then(response => {
        Swal.close();
        btn.disabled = false;
        btn.innerHTML = 'Konfirmasi Pembayaran';
        
        if (response.success) {
            closePaymentModal();
            showPaymentSuccessModal(response.debt, amount);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: response.message || 'Gagal memproses pembayaran',
                confirmButtonColor: '#ef4444',
                customClass: {
                    popup: 'rounded-3xl border-none shadow-2xl'
                }
            });
        }
    })
    .catch(err => {
        Swal.close();
        console.error('Payment error:', err);
        const btn = document.getElementById('btnProcessPayment');
        btn.disabled = false;
        btn.innerHTML = 'Konfirmasi Pembayaran';
        Swal.fire({
            icon: 'error',
            title: 'Kesalahan Sistem',
            text: 'Terjadi kesalahan saat memproses pembayaran',
            confirmButtonColor: '#ef4444',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl'
            }
        });
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
        btn.innerHTML = 'Bayar via QRIS';
        
        if (response.success) {
            closePaymentModal();
            snap.pay(response.snap_token, {
                onSuccess: function(result) {
                    Swal.fire({
                        title: 'Memproses...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

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
                    .then(r => {
                        if (!r.ok) throw new Error('Response failed');
                        return r.json();
                    })
                    .then(payResp => {
                        Swal.close();
                        if (payResp.success) {
                            showPaymentSuccessModal(payResp.debt, amount);
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Pembayaran Berhasil',
                                text: 'Pembayaran QRIS berhasil, namun sistem gagal mencatat. Silakan hubungi admin.',
                                confirmButtonColor: '#658C58',
                                customClass: {
                                    popup: 'rounded-3xl border-none shadow-2xl'
                                }
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    })
                    .catch(err => {
                        Swal.close();
                        console.error('Final payment sync failed:', err);
                        Swal.fire({
                            icon: 'warning',
                            title: 'Pencatatan Gagal',
                            text: 'Pembayaran QRIS Anda berhasil, namun gagal terhubung ke sistem untuk pencatatan. Silakan refresh halaman atau hubungi Support.',
                            confirmButtonColor: '#658C58',
                            customClass: {
                                popup: 'rounded-3xl border-none shadow-2xl'
                            }
                        });
                    });
                },
                onPending: function(result) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Menunggu',
                        text: 'Silakan selesaikan pembayaran QRIS Anda.',
                        confirmButtonColor: '#658C58',
                        customClass: {
                            popup: 'rounded-3xl border-none shadow-2xl'
                        }
                    });
                },
                onError: function(result) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat memproses pembayaran.',
                        confirmButtonColor: '#ef4444',
                        customClass: {
                            popup: 'rounded-3xl border-none shadow-2xl'
                        }
                    });
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: response.message || 'Gagal membuat token pembayaran',
                confirmButtonColor: '#ef4444',
                customClass: {
                    popup: 'rounded-3xl border-none shadow-2xl'
                }
            });
        }
    })
    .catch(err => {
        console.error('Midtrans error:', err);
        const btn = document.getElementById('btnProcessPayment');
        btn.disabled = false;
        btn.innerHTML = 'Bayar via QRIS';
        Swal.fire({
            icon: 'error',
            title: 'Kesalahan Sistem',
            text: 'Gagal terhubung ke payment gateway',
            confirmButtonColor: '#ef4444',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl'
            }
        });
    });
}

// Success Modal Helpers
function showPaymentSuccessModal(debt, amountPaid) {
    const modal = document.getElementById('paymentSuccessModal');
    
    document.getElementById('successInvoiceNumber').textContent = currentDebt.invoice_number;
    document.getElementById('successCustomer').textContent = currentDebt.customer_name;
    document.getElementById('successTotal').textContent = formatRupiah(amountPaid);
    document.getElementById('successDebt').textContent = formatRupiah(debt.remaining_amount);
    
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    setTimeout(() => {
        modal.firstElementChild.classList.remove('scale-95', 'opacity-0');
        modal.firstElementChild.classList.add('scale-100', 'opacity-100');
    }, 10);
    
    // Set for printing functions
    modal.dataset.saleId = currentDebt.sale_id;
    
    // Refresh background data without full reload
    loadDebts(debtPage);
    loadStats();
}

function closePaymentSuccessModal() {
    const modal = document.getElementById('paymentSuccessModal');
    modal.firstElementChild.classList.add('scale-95', 'opacity-0');
    modal.firstElementChild.classList.remove('scale-100', 'opacity-100');
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }, 300);
}

function printReceipt() {
    const saleId = document.getElementById('paymentSuccessModal').dataset.saleId;
    if (saleId) {
        const printUrl = `{{ url('receipt/print') }}/${saleId}`;
        const win = window.open(printUrl, '_blank');
        if (win) {
            win.focus();
        } else {
            Swal.fire('Error', 'Gagal membuka tab cetak. Pastikan pop-up diizinkan.', 'error');
        }
    }
}

function downloadReceipt() {
    const saleId = document.getElementById('paymentSuccessModal').dataset.saleId;
    if (saleId) {
        window.location.href = `{{ url('receipt/download') }}/${saleId}`;
    }
}

function handlePrintInvoice() {
    const saleId = document.getElementById('paymentSuccessModal').dataset.saleId;
    if (saleId) {
        const printUrl = `{{ url('receipt/invoice') }}/${saleId}/print`;
        const win = window.open(printUrl, '_blank');
        if (win) {
            win.focus();
        } else {
            Swal.fire('Error', 'Gagal membuka tab cetak. Pastikan pop-up diizinkan.', 'error');
        }
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Load initial data from URL or default
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    
    if (tabParam && ['customer', 'debt', 'supplier'].includes(tabParam)) {
        switchTab(tabParam);
    } else {
        switchTab(currentTab);
    }
    
    loadStats();

    // Customer Search
    document.getElementById('searchCustomer').addEventListener('input', debounce(function(e) {
        updateUrlParams({ search: e.target.value });
        loadCustomers();
    }, 500));

    // Customer Type Filter
    document.getElementById('filterCustomerType').addEventListener('change', function(e) {
        updateUrlParams({ type: e.target.value });
        loadCustomers();
    });

    // Customer Status Filter
    document.getElementById('filterCustomerStatus').addEventListener('change', function(e) {
        updateUrlParams({ status: e.target.value });
        loadCustomers();
    });

    // Debt Search
    document.getElementById('searchDebt').addEventListener('input', debounce(function(e) {
        updateUrlParams({ debt_search: e.target.value });
        loadDebts();
    }, 500));

    // Debt Status Filter
    document.getElementById('filterDebtStatus').addEventListener('change', function(e) {
        updateUrlParams({ debt_status: e.target.value });
        loadDebts();
    });
    
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

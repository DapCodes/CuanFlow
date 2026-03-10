@extends('layouts.app')

@section('title', 'Produksi - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Produksi</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">
        
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-check-circle mt-0.5 text-green-500"></i>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-exclamation-circle mt-0.5 text-red-500"></i>
                <p class="text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-500 border border-blue-100">
                        <i class="fas fa-flask text-sm"></i>
                    </span>
                    <span>Stok Produk</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola produksi dan stok produk jadi
                </p>
            </div>
        </section>

        <!-- Tabs & Filter Navigation -->
        <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-gray-200 gap-4">
            <nav class="-mb-px flex space-x-8 overflow-x-auto no-scrollbar" aria-label="Tabs">
                <button type="button" 
                    id="tab-queue"
                    class="tab-btn active-tab border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2"
                    onclick="switchTab('queue')">
                    <i class="fas fa-clipboard-list"></i>
                    Antrian Pesanan
                    @php
                        $totalPending = $pendingSales->count();
                    @endphp
                    @if($totalPending > 0)
                    <span class="bg-red-100 text-red-600 py-0.5 px-2.5 rounded-full text-xs font-bold">{{ $totalPending }}</span>
                    @endif
                </button>
                <button type="button" 
                    id="tab-stock"
                    class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2"
                    onclick="switchTab('stock')">
                    <i class="fas fa-boxes"></i>
                    Stok & Inventaris
                </button>
            </nav>

            <div class="pb-3 md:pb-0 flex items-center justify-between md:justify-end gap-3 w-full md:w-auto">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider md:hidden">Urutkan:</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider hidden md:inline-block">Urutkan:</span>
                <div class="flex items-center p-1 bg-gray-100 rounded-lg border border-gray-200 flex-1 md:flex-none justify-center md:justify-start">
                    <input type="hidden" id="sort-mode" value="oldest">
                    <button type="button" onclick="setSortMode('oldest')" id="btn-sort-oldest" 
                        class="flex-1 md:flex-none px-4 py-1.5 text-xs font-bold rounded-md shadow-sm bg-white text-blue-600 transition-all border border-transparent text-center">
                        Terlama
                    </button>
                    <button type="button" onclick="setSortMode('newest')" id="btn-sort-newest" 
                        class="flex-1 md:flex-none px-4 py-1.5 text-xs font-bold rounded-md text-gray-500 hover:text-gray-700 transition-all border border-transparent text-center">
                        Terbaru
                    </button>
                </div>
            </div>
        </div>

        <!-- QUEUE SECTION (Cards) -->
        <section id="content-queue" class="tab-content block">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="production-grid">
                @forelse($pendingSales as $sale)
                <div class="production-card-wrapper" data-timestamp="{{ $sale->created_at->timestamp }}">
                    <div class="group bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col h-full relative" id="card-sale-{{ $sale->id }}">
                        <!-- Status Indicator (Subtle) -->
                        <div class="absolute top-0 right-0 w-16 h-16 pointer-events-none overflow-hidden">
                            <div class="absolute top-0 right-0 translate-x-8 -translate-y-8 rotate-45 bg-orange-500/10 w-full h-full border-b border-orange-500/20"></div>
                        </div>

                        <div class="p-5 flex-1 flex flex-col">
                            <div class="flex items-start justify-between gap-3 mb-5">
                                <div class="space-y-1">
                                    <h3 class="font-bold text-gray-900 text-lg tracking-tight">{{ $sale->invoice_number }}</h3>
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500 bg-gray-50 px-2 py-1 rounded-md border border-gray-100">
                                            <i class="far fa-clock text-orange-500"></i>
                                            {{ $sale->created_at->format('H:i') }}
                                        </span>
                                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-md border border-blue-100">
                                            <i class="fas fa-layer-group"></i>
                                            {{ $sale->items->count() }} Item
                                        </span>
                                    </div>
                                </div>
                                <div class="h-11 w-11 rounded-xl bg-gradient-to-br from-orange-400 to-orange-600 text-white flex items-center justify-center flex-shrink-0 shadow-lg shadow-orange-200 group-hover:rotate-12 transition-transform duration-300">
                                    <i class="fas fa-receipt text-lg"></i>
                                </div>
                            </div>

                            <div class="space-y-3 mb-6">
                                <div class="flex items-center gap-3 p-2 rounded-lg bg-gray-50/50 border border-transparent group-hover:border-gray-100 group-hover:bg-gray-50 transition-colors">
                                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-gray-400 border border-gray-100 shadow-sm">
                                        <i class="fas fa-user text-xs"></i>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700 truncate">{{ $sale->customer->name ?? 'Pelanggan Umum' }}</span>
                                </div>
                                
                                @if($sale->table)
                                <div class="flex items-center gap-3 p-2 rounded-lg bg-gray-50/50 border border-transparent group-hover:border-gray-100 group-hover:bg-gray-50 transition-colors">
                                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-gray-400 border border-gray-100 shadow-sm">
                                        <i class="fas fa-chair text-xs"></i>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700">Meja: {{ $sale->table->name }}</span>
                                </div>
                                @endif
                            </div>

                            <div class="mt-auto">
                                <button type="button" 
                                    onclick="openSaleModal('{{ $sale->id }}')"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-xl px-4 py-3 text-sm font-bold transition-all flex items-center justify-center gap-2 shadow-sm hover:shadow-blue-200 active:scale-95">
                                    <i class="fas fa-external-link-alt text-xs opacity-70"></i>
                                    Buka Pesanan
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for each sale -->
                    <div id="modal-sale-{{ $sale->id }}" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeSaleModal('{{ $sale->id }}')"></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            
                            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-white/20">
                                <!-- Modal Header -->
                                <div class="relative bg-white px-6 pt-5 pb-4 border-b border-gray-100">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-4">
                                            <div class="h-12 w-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center border border-orange-100 shadow-sm">
                                                <i class="fas fa-clipboard-list text-xl"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-extrabold text-gray-900 tracking-tight">{{ $sale->invoice_number }}</h3>
                                                <div class="flex items-center gap-2 mt-0.5">
                                                    <span class="text-xs font-semibold text-gray-500">{{ $sale->customer->name ?? 'Pelanggan Umum' }}</span>
                                                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                                    <span class="text-xs font-medium text-gray-400">{{ $sale->created_at->format('d M, H:i') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <button onclick="closeSaleModal('{{ $sale->id }}')" class="w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-900 transition-all">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Modal Body -->
                                <div class="p-6 bg-gray-50/30">
                                    <div class="space-y-4 max-h-[50vh] overflow-y-auto pr-2 custom-scrollbar">
                                        @php
                                            $saleItems = $sale->items->filter(function($item) {
                                                return $item->production_status === 'pending' && $item->product && !$item->product->is_stock;
                                            });
                                        @endphp
                                        @foreach($saleItems as $item)
                                        <div class="flex items-center justify-between p-5 bg-white border border-gray-200 rounded-2xl shadow-sm hover:border-blue-300 transition-colors">
                                            <div class="flex items-center gap-4 text-left">
                                                <div class="h-12 w-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 border border-blue-100">
                                                    <i class="fas fa-utensils text-lg"></i>
                                                </div>
                                                <div class="min-w-0">
                                                    <h4 class="text-sm font-bold text-gray-900 truncate pr-4">{{ $item->product->name }}</h4>
                                                    <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-gray-100 text-gray-700 border border-gray-200">
                                                            {{ (int)$item->quantity }} {{ $item->product->unit->name ?? 'Pcs' }}
                                                        </span>
                                                        @if($item->notes)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-orange-50 text-orange-600 border border-orange-100">
                                                            <i class="far fa-comment-dots mr-1"></i> {{ $item->notes }}
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="flex-shrink-0">
                                                @if($item->product->defaultRecipe)
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('production.preparation', $item->id) }}" class="bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-xl px-4 py-2.5 text-xs font-bold transition-all flex items-center gap-2 shadow-sm whitespace-nowrap">
                                                        <i class="fas fa-info-circle text-sm"></i>
                                                        Detail
                                                    </a>
                                                    <form action="{{ route('production.store') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                                        <input type="hidden" name="planned_quantity" value="{{ $item->quantity }}">
                                                        <input type="hidden" name="sale_item_id" value="{{ $item->id }}">
                                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white rounded-xl px-4 py-2.5 text-xs font-bold transition-all flex items-center gap-2 shadow-md hover:shadow-blue-200 active:scale-95 whitespace-nowrap">
                                                            <i class="fas fa-fire-alt text-sm"></i>
                                                            Masak
                                                        </button>
                                                    </form>
                                                </div>
                                                @else
                                                <div class="flex items-center gap-2 px-3 py-2 bg-red-50 text-red-600 rounded-lg border border-red-100">
                                                    <i class="fas fa-exclamation-triangle text-xs pr-1"></i>
                                                    <span class="text-[11px] font-bold uppercase tracking-wider">Tanpa Resep</span>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Modal Footer -->
                                <div class="bg-white px-6 py-5 border-t border-gray-100 flex flex-col items-center gap-3">
                                    @if($saleItems->count() > 1)
                                    <form action="{{ route('production.store-all') }}" method="POST" class="w-full">
                                        @csrf
                                        <input type="hidden" name="sale_id" value="{{ $sale->id }}">
                                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white rounded-xl px-4 py-3 text-sm font-bold transition-all flex items-center justify-center gap-2 shadow-md hover:shadow-green-200 active:scale-95">
                                            <i class="fas fa-check-double"></i>
                                            Masak Semua ({{ $saleItems->count() }} Item)
                                        </button>
                                    </form>
                                    @endif
                                    <div class="flex items-center justify-center gap-2 text-gray-400">
                                        <span class="flex h-2 w-2 relative">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                                        </span>
                                        <p class="text-[11px] font-medium tracking-wide first-letter:uppercase italic">Gunakan tombol "Masak" untuk setiap item yang ingin diproses sekarang.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div id="no-orders-empty-state" class="col-span-full py-20 text-center bg-white rounded-3xl border border-gray-200 border-dashed shadow-inner">
                    <div class="w-20 h-20 rounded-full bg-green-50 flex items-center justify-center mx-auto mb-5 border border-green-100">
                        <i class="fas fa-check-circle text-green-500 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Semua Pesanan Selesai!</h3>
                    <p class="text-gray-500 text-sm max-w-xs mx-auto">Tidak ada antrian pesanan yang perlu diproduksi saat ini. Santai sejenak.</p>
                </div>
                @endforelse
            </div>
        </section>

        <!-- STOCK SECTION (Table) -->
        <section id="content-stock" class="tab-content hidden bg-white border border-gray-200 rounded-xl shadow-sm">
            <div class="border-b border-gray-200 px-4 md:px-6 py-4 bg-gray-50">
                <div class="flex flex-col md:flex-row gap-3">
                    <div class="flex-1">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Cari produk stok</label>
                        <div class="relative">
                            <input type="text" id="searchProduct" placeholder="Cari berdasarkan nama..." 
                                class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        </div>
                    </div>
                    <div class="w-full sm:w-40 md:w-44">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Status Stok</label>
                        <select id="filterStock" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                            <option value="">Semua Stok</option>
                            <option value="low">Stok Menipis</option>
                            <option value="available">Stok Tersedia</option>
                            <option value="empty">Stok Kosong</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="hidden md:table-cell px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Kode</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Produk</th>
                            <th class="hidden lg:table-cell px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Stok</th>
                            <th class="hidden xl:table-cell px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Min. Stok</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white" id="productTableBody">
                        @forelse($stockProducts as $product)
                        <tr class="hover:bg-gray-50 transition-colors product-row" 
                            data-name="{{ strtolower($product['name']) }}"
                            data-stock-status="{{ $product['stock'] == 0 ? 'empty' : ($product['is_low_stock'] ? 'low' : 'available') }}">
                            <td class="hidden md:table-cell px-6 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center rounded-md bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-800 font-mono border border-gray-200">
                                    {{ $product['code'] }}
                                </span>
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    @if($product['image'])
                                    <img src="{{ Storage::url($product['image']) }}" alt="{{ $product['name'] }}" 
                                        class="h-10 w-10 rounded-lg object-cover border border-gray-200">
                                    @else
                                    <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center">
                                        <i class="fas fa-flask text-white text-sm"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $product['name'] }}</div>
                                        <div class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                                            <i class="fas fa-ruler-combined text-[10px]"></i>
                                            {{ $product['unit'] }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="hidden lg:table-cell px-6 py-3 whitespace-nowrap">
                                @if($product['category'])
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                    <i class="fas fa-tag mr-1 text-[10px]"></i>
                                    {{ $product['category'] }}
                                </span>
                                @else
                                <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap">
                                <div class="text-sm">
                                    <span class="font-semibold {{ $product['stock'] == 0 ? 'text-red-600' : ($product['is_low_stock'] ? 'text-yellow-600' : 'text-green-600') }}">
                                        {{ number_format($product['stock'], 2) }}
                                    </span>
                                    <span class="text-gray-500 ml-1">{{ $product['unit'] }}</span>
                                </div>
                            </td>
                            <td class="hidden xl:table-cell px-6 py-3 whitespace-nowrap">
                                <span class="text-sm text-gray-600">
                                    {{ number_format($product['min_stock'], 2) }} {{ $product['unit'] }}
                                </span>
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap">
                                <div class="flex flex-col gap-1.5">
                                    @if($product['stock'] == 0)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-100 w-fit">
                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>
                                        Kosong
                                    </span>
                                    @elseif($product['is_low_stock'])
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700 border border-yellow-100 w-fit">
                                        <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1.5"></span>
                                        Menipis
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-green-600 text-white border border-green-700 w-fit shadow-sm">
                                        <i class="fas fa-check-circle mr-1 text-[10px]"></i>
                                        {{ number_format($product['total_valid_qty'], 0) }} STOCK TERSEDIA
                                    </span>
                                    @endif

                                    @if($product['total_expired_qty'] > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-600 text-white border border-red-700 w-fit animate-pulse shadow-sm">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ number_format($product['total_expired_qty'], 0) }} STOCK KADALUARSA
                                    </span>
                                    @endif

                                    @if($product['total_expiring_qty'] > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-700 border border-orange-200 w-fit">
                                        <i class="fas fa-clock mr-1 text-[10px]"></i>
                                        {{ number_format($product['total_expiring_qty'], 0) }} STOCK SEGERA KADALUARSA
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    @can('lihat stok produksi')
                                    <a href="{{ route('production.stock.show', $product['id']) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-gray-200 bg-white text-gray-600 hover:bg-gray-50"
                                       title="Detail Stok">
                                        <i class="fas fa-chart-line text-xs"></i>
                                    </a>
                                    @endcan
                                    @if($product['has_recipe'])
                                    @can('buat produksi')
                                    <a href="{{ route('production.create', ['product_id' => $product['id']]) }}" 
                                       class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-xs font-semibold"
                                       title="Produksi">
                                        <i class="fas fa-plus-circle text-xs"></i>
                                        <span>Produksi</span>
                                    </a>
                                    @endcan
                                    @else
                                    <span class="text-xs text-gray-400 italic px-2">Tidak ada resep</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                        <i class="fas fa-box-open text-3xl text-gray-300"></i>
                                    </div>
                                    <h3 class="text-base font-semibold text-gray-900 mb-1">Belum Ada Produk Stok</h3>
                                    <p class="text-sm text-gray-500 mb-4 max-w-sm">
                                        Tambahkan produk yang dapat d-stok untuk memulai
                                    </p>
                                    @can('buat produk')
                                    <a href="{{ route('products-hpp.create') }}" 
                                       class="inline-flex items-center gap-2 rounded-lg bg-green-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-600">
                                        <i class="fas fa-plus-circle text-xs"></i>
                                        Tambah Produk
                                    </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        @if($recentProductions->count() > 0)
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-history text-blue-500"></i>
                    <span>Riwayat Produksi Terbaru</span>
                </h2>
            </div>

            <div class="p-6">
                <div class="space-y-3">
                    @foreach($recentProductions as $production)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors border border-gray-100">
                        <div class="flex items-center gap-4 flex-1 min-w-0">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-400 to-blue-700 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-flask text-white text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="text-sm font-semibold text-gray-900 truncate">{{ $production->product->name }}</h4>
                                    <span class="text-xs font-mono bg-gray-200 px-2 py-0.5 rounded border border-gray-300 flex-shrink-0">{{ $production->batch_number }}</span>
                                </div>
                                <div class="flex items-center gap-3 mt-1 flex-wrap">
                                    <p class="text-xs text-gray-600">
                                        <i class="fas fa-cubes mr-1"></i>
                                        {{ number_format($production->actual_quantity ?? $production->planned_quantity, 2) }} {{ $production->product->unit->name ?? '' }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        <i class="fas fa-user mr-1"></i>
                                        {{ $production->createdBy->name ?? '-' }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $production->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 ml-4 flex-shrink-0">
                            @php
                                $statusKey = $production->status;
                                if ($production->is_disposed) {
                                    $statusKey = 'disposed';
                                }
                                
                                $statusConfig = [
                                    'planned' => ['class' => 'bg-gray-50 text-gray-700 border-gray-200', 'icon' => 'fa-clock', 'text' => 'Direncanakan'],
                                    'in_progress' => ['class' => 'bg-blue-50 text-blue-700 border-blue-200', 'icon' => 'fa-spinner', 'text' => 'Proses'],
                                    'completed' => ['class' => 'bg-green-50 text-green-700 border-green-200', 'icon' => 'fa-check-circle', 'text' => 'Selesai'],
                                    'cancelled' => ['class' => 'bg-red-50 text-red-700 border-red-200', 'icon' => 'fa-times-circle', 'text' => 'Batal'],
                                    'disposed' => ['class' => 'bg-orange-50 text-orange-700 border-orange-200', 'icon' => 'fa-trash', 'text' => 'Terbuang'],
                                ];
                                $config = $statusConfig[$statusKey] ?? $statusConfig['planned'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $config['class'] }}">
                                <i class="fas {{ $config['icon'] }} mr-1.5"></i>
                                {{ $config['text'] }}
                            </span>
                            @can('lihat produksi')
                             <a href="{{ route('production.show', $production->id) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-gray-200 bg-white text-gray-600 hover:bg-gray-50"
                               title="Detail">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            @endcan
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

    </div>
</main>

@push('scripts')
<script>
    function updateURLParams(params) {
        const url = new URL(window.location);
        for (const [key, value] of Object.entries(params)) {
            if (value) {
                url.searchParams.set(key, value);
            } else {
                url.searchParams.delete(key);
            }
        }
        window.history.replaceState({}, '', url);
    }

    function switchTab(tabId) {
        // Update Buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active-tab', 'border-blue-500', 'text-blue-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        });
        
        const activeBtn = document.getElementById('tab-' + tabId);
        activeBtn.classList.remove('border-transparent', 'text-gray-500');
        activeBtn.classList.add('active-tab', 'border-blue-500', 'text-blue-600');

        // Update Content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        document.getElementById('content-' + tabId).classList.remove('hidden');

        updateURLParams({ tab: tabId });
    }

    function openSaleModal(saleId) {
        document.getElementById('modal-sale-' + saleId).classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeSaleModal(saleId) {
        document.getElementById('modal-sale-' + saleId).classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    document.addEventListener('submit', function(e) {
        const form = e.target;
        const isStore = form.action.includes('{{ route("production.store") }}');
        const isStoreAll = form.action.includes('{{ route("production.store-all") }}');

        if ((isStore || isStoreAll) && !form.dataset.validated) {
            e.preventDefault();

            const formData = new FormData(form);
            const data = {};
            formData.forEach((value, key) => data[key] = value);

            // Show loading
            Swal.fire({
                title: 'Memeriksa Bahan Baku...',
                allowOutsideClick: false,
                customClass: {
                    popup: 'rounded-[32px] border-none shadow-2xl',
                },
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('{{ route("production.check-materials") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(r => r.json())
            .then(res => {
                Swal.close();
                if (res.success && res.insufficient && res.insufficient.length > 0) {
                    let materialList = '<ul class="text-left space-y-2 mt-4">';
                    res.insufficient.forEach(m => {
                        materialList += `<li class="flex justify-between items-center text-sm border-b border-gray-100 pb-2">
                            <span class="font-medium text-gray-700">${m.name}</span>
                            <span class="text-red-500 font-bold">Kurang ${parseFloat(m.shortage).toFixed(2)} ${m.unit}</span>
                        </li>`;
                    });
                    materialList += '</ul>';

                    Swal.fire({
                        title: '<h3 class="text-xl font-extrabold text-gray-900 tracking-tight">Bahan Baku Tidak Mencukupi</h3>',
                        html: `
                            <div class="mt-4">
                                <p class="text-sm text-gray-500 mb-4 px-4">Maaf, stok beberapa bahan berikut tidak tersedia di dapur untuk pesanan ini:</p>
                                <div class="bg-gray-50 rounded-2xl border border-gray-100 p-4 max-h-60 overflow-y-auto custom-scrollbar">
                                    ${materialList}
                                </div>
                                <div class="mt-6 flex items-center gap-2 p-3 bg-blue-50 border border-blue-100 rounded-xl text-left">
                                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-blue-500 flex-shrink-0 shadow-sm border border-blue-100">
                                        <i class="fas fa-info-circle text-xs"></i>
                                    </div>
                                    <p class="text-[11px] text-blue-700 font-medium leading-tight">
                                        Pilih <b>Lanjutkan</b> jika Anda tetap ingin memproses dengan stok yang ada, atau <b>Batalkan</b> untuk me-refund transaksi ini.
                                    </p>
                                </div>
                            </div>
                        `,
                        icon: 'warning',
                        iconColor: '#f97316',
                        showCancelButton: true,
                        confirmButtonText: '<i class="fas fa-play mr-2"></i> Lanjutkan',
                        cancelButtonText: '<i class="fas fa-times-circle mr-2"></i> Batalkan Pesanan',
                        customClass: {
                            popup: 'rounded-[32px] border-none shadow-2xl',
                            confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white rounded-xl px-6 py-3 text-sm font-bold shadow-lg shadow-blue-200 border-none transition-all active:scale-95 mx-2',
                            cancelButton: 'bg-white border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-red-600 rounded-xl px-6 py-3 text-sm font-bold transition-all mx-2',
                            actions: 'mt-6 gap-2',
                        },
                        buttonsStyling: false,
                        reverseButtons: true,
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Lanjutkan: Add ignore_insufficient and submit
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'ignore_insufficient';
                            hiddenInput.value = '1';
                            form.appendChild(hiddenInput);
                            form.dataset.validated = 'true';
                            form.submit();
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            // Batalkan: Refund Sale
                            Swal.fire({
                                title: '<h3 class="text-xl font-extrabold text-gray-900 tracking-tight">Konfirmasi Refund</h3>',
                                text: 'Seluruh pesanan dalam transaksi ini akan dibatalkan dan uang akan dikembalikan (jika sudah bayar). Lanjutkan?',
                                icon: 'question',
                                iconColor: '#2563eb',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, Batalkan & Refund',
                                cancelButtonText: 'Kembali',
                                customClass: {
                                    popup: 'rounded-[32px] border-none shadow-2xl',
                                    confirmButton: 'bg-red-600 hover:bg-red-700 text-white rounded-xl px-6 py-3 text-sm font-bold shadow-lg shadow-red-200 border-none transition-all active:scale-95 mx-2',
                                    cancelButton: 'bg-gray-100 text-gray-600 hover:bg-gray-200 rounded-xl px-6 py-3 text-sm font-bold border-none transition-all mx-2',
                                },
                                buttonsStyling: false,
                                reverseButtons: true
                            }).then((refundRes) => {
                                if (refundRes.isConfirmed) {
                                    refundSale(data.sale_id || null, data.sale_item_id || null);
                                }
                            });
                        }
                    });
                } else {
                    form.dataset.validated = 'true';
                    form.submit();
                }
            })
            .catch(err => {
                Swal.close();
                console.error(err);
                showToast('error', 'Gagal memvalidasi bahan baku');
            });
        }
    });

    function refundSale(saleId, saleItemId) {
        Swal.fire({
            title: 'Memproses Refund...',
            allowOutsideClick: false,
            customClass: {
                popup: 'rounded-[32px] border-none shadow-2xl',
            },
            didOpen: () => { Swal.showLoading(); }
        });

        fetch('{{ route("production.refund-sale") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ sale_id: saleId, sale_item_id: saleItemId })
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                Swal.fire({
                    title: 'Berhasil',
                    text: res.message,
                    icon: 'success',
                    iconColor: '#22c55e',
                    confirmButtonText: 'Tutup',
                    customClass: {
                        popup: 'rounded-[32px] border-none shadow-2xl',
                        confirmButton: 'bg-green-600 hover:bg-green-700 text-white rounded-xl px-6 py-3 text-sm font-bold shadow-lg shadow-green-200 border-none transition-all active:scale-95 mx-2',
                    },
                    buttonsStyling: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Gagal',
                    text: res.message,
                    icon: 'error',
                    iconColor: '#ef4444',
                    confirmButtonText: 'Tutup',
                    customClass: {
                        popup: 'rounded-[32px] border-none shadow-2xl',
                        confirmButton: 'bg-red-600 hover:bg-red-700 text-white rounded-xl px-6 py-3 text-sm font-bold shadow-lg shadow-red-200 border-none transition-all active:scale-95 mx-2',
                    },
                    buttonsStyling: false
                });
            }
        })
        .catch(() => {
            Swal.fire({
                title: 'Error',
                text: 'Gagal memproses refund',
                icon: 'error',
                iconColor: '#ef4444',
                confirmButtonText: 'Tutup',
                customClass: {
                    popup: 'rounded-[32px] border-none shadow-2xl',
                    confirmButton: 'bg-red-600 hover:bg-red-700 text-white rounded-xl px-6 py-3 text-sm font-bold shadow-lg shadow-red-200 border-none transition-all active:scale-95 mx-2',
                },
                buttonsStyling: false
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        
        // Initial Tab State (Queue by default)
        const initialTab = urlParams.get('tab') || 'queue';
        switchTab(initialTab);
        
        // Initial Sort State
        const initialSort = urlParams.get('sort') || 'oldest';
        if (typeof setSortMode === 'function') {
            setSortMode(initialSort);
        }
        
        const searchInput = document.getElementById('searchProduct');
        const filterStock = document.getElementById('filterStock');
        
        if(searchInput && filterStock) {
            if (urlParams.has('search')) searchInput.value = urlParams.get('search');
            if (urlParams.has('stock')) filterStock.value = urlParams.get('stock');

            const productRows = document.querySelectorAll('.product-row');

            function filterProducts() {
                const searchTerm = (searchInput.value || '').toLowerCase();
                const stockFilter = filterStock.value;

                productRows.forEach(row => {
                    const productName = row.dataset.name || '';
                    const stockStatus = row.dataset.stockStatus || '';

                    const matchesSearch = !searchTerm || productName.includes(searchTerm);
                    const matchesFilter = !stockFilter || stockStatus === stockFilter;

                    row.style.display = (matchesSearch && matchesFilter) ? '' : 'none';
                });
                
                updateURLParams({ search: searchInput.value, stock: filterStock.value });
            }

            searchInput.addEventListener('input', filterProducts);
            filterStock.addEventListener('change', filterProducts);
            
            // Trigger initial filter
            filterProducts();
        }
    });
</script>

<!-- Pusher Realtime for Production Queue -->
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    (function() {
        const PUSHER_KEY = @json(config('broadcasting.connections.pusher.key'));
        const PUSHER_CLUSTER = @json(config('broadcasting.connections.pusher.options.cluster'));
        const OUTLET_ID = @json(auth()->user()->outlet_id);

        // Defined globally so it's accessible from the select's onchange
        // Defined globally so it's accessible
        // Defined globally so it's accessible
        window.setSortMode = function(mode) {
            const currentMode = document.getElementById('sort-mode');
            if(currentMode) currentMode.value = mode;

            // Update UI
            const btnOldest = document.getElementById('btn-sort-oldest');
            const btnNewest = document.getElementById('btn-sort-newest');

            if (mode === 'oldest') {
                btnOldest.className = 'flex-1 md:flex-none px-4 py-1.5 text-xs font-bold rounded-md shadow-sm bg-white text-blue-600 transition-all border border-transparent text-center';
                btnNewest.className = 'flex-1 md:flex-none px-4 py-1.5 text-xs font-bold rounded-md text-gray-500 hover:text-gray-700 transition-all border border-transparent text-center';
            } else {
                btnOldest.className = 'flex-1 md:flex-none px-4 py-1.5 text-xs font-bold rounded-md text-gray-500 hover:text-gray-700 transition-all border border-transparent text-center';
                btnNewest.className = 'flex-1 md:flex-none px-4 py-1.5 text-xs font-bold rounded-md shadow-sm bg-white text-blue-600 transition-all border border-transparent text-center';
            }

            if (typeof updateURLParams === 'function') {
                updateURLParams({ sort: mode });
            }

            sortProductionCards();
        };

        window.sortProductionCards = function() {
            const grid = document.getElementById('production-grid');
            if (!grid) return;
            
            const sortMode = document.getElementById('sort-mode').value;
            const wrappers = Array.from(grid.querySelectorAll('.production-card-wrapper'));

            if (wrappers.length === 0) return;

            wrappers.sort((a, b) => {
                const tsA = parseInt(a.dataset.timestamp) || 0;
                const tsB = parseInt(b.dataset.timestamp) || 0;
                return sortMode === 'newest' ? tsB - tsA : tsA - tsB;
            });

            // Re-append in correct order
            wrappers.forEach(w => grid.appendChild(w));
        };

        if (!PUSHER_KEY || PUSHER_KEY === 'your-app-key') {
            console.warn('[Production] Pusher key not configured. Realtime disabled.');
            return;
        }

        const pusher = new Pusher(PUSHER_KEY, {
            cluster: PUSHER_CLUSTER,
            forceTLS: true
        });

        const channel = pusher.subscribe('production.outlet.' + OUTLET_ID);

        // Helper: handle incoming order data
        window.handleIncomingOrder = function(order) {
            if (!order || !order.items || order.items.length === 0) return;

            // If card already exists, just return its ID for flashing
            if (document.getElementById('card-sale-' + order.sale_id)) {
                return order.sale_id;
            }

            // Remove empty state if present
            const emptyState = document.querySelector('#content-queue .col-span-full');
            if (emptyState) emptyState.remove();

            const grid = document.querySelector('#content-queue .grid');
            if (!grid) return;

            // Build items HTML
            let itemsHtml = '';
            order.items.forEach(function(item) {
                const notesHtml = item.notes 
                    ? `<div class="flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-100">
                           <i class="fas fa-comment-dots text-[10px]"></i>
                           <span class="text-[11px] font-medium">${escapeHtml(item.notes)}</span>
                       </div>` 
                    : '';

                const actionHtml = item.has_recipe
                    ? `<div class="flex items-center gap-2">
                           <a href="/production/preparation/${item.id}" class="bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-xl px-4 py-2.5 text-xs font-bold transition-all flex items-center gap-2 shadow-sm whitespace-nowrap">
                               <i class="fas fa-info-circle text-sm"></i> Detail
                           </a>
                           <form action="/production" method="POST">
                               <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]')?.content || ''}">
                               <input type="hidden" name="product_id" value="${item.product_id}">
                               <input type="hidden" name="planned_quantity" value="${item.quantity}">
                               <input type="hidden" name="sale_item_id" value="${item.id}">
                               <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white rounded-xl px-4 py-2.5 text-xs font-bold transition-all flex items-center gap-2 shadow-md hover:shadow-blue-200 active:scale-95 whitespace-nowrap">
                                   <i class="fas fa-fire-alt text-sm"></i> Masak
                               </button>
                           </form>
                       </div>`
                    : `<div class="flex items-center gap-2 px-3 py-2 bg-red-50 text-red-600 rounded-lg border border-red-100">
                           <i class="fas fa-exclamation-triangle text-xs pr-1"></i>
                           <span class="text-[11px] font-bold uppercase tracking-wider">Tanpa Resep</span>
                       </div>`;

                itemsHtml += `
                    <div class="flex items-center justify-between p-5 bg-white border border-gray-200 rounded-2xl shadow-sm hover:border-blue-300 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 border border-blue-100">
                                <i class="fas fa-utensils text-lg"></i>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-sm font-bold text-gray-900 truncate pr-4">${escapeHtml(item.product_name)}</h4>
                                <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-gray-100 text-gray-700 border border-gray-200">
                                        ${item.quantity} ${escapeHtml(item.unit)}
                                    </span>
                                    ${notesHtml}
                                </div>
                            </div>
                        </div>
                        <div class="flex-shrink-0">${actionHtml}</div>
                    </div>`;
            });

            const modalId = 'modal-sale-rt-' + order.sale_id;
            const tableHtml = order.table_name 
                ? `<div class="flex items-center gap-3 p-2 rounded-lg bg-gray-50/50 border border-transparent group-hover:border-gray-100 group-hover:bg-gray-50 transition-colors">
                       <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-gray-400 border border-gray-100 shadow-sm">
                           <i class="fas fa-chair text-xs"></i>
                       </div>
                       <span class="text-sm font-semibold text-gray-700">Meja: ${escapeHtml(order.table_name)}</span>
                   </div>` 
                : '';

            const showCookAll = order.items.length > 1;
            let cookAllHtml = '';
            
            if (showCookAll) {
                cookAllHtml = `
                    <form action="/production/store-all" method="POST" class="w-full">
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]')?.content || ''}">
                        <input type="hidden" name="sale_id" value="${order.sale_id}">
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white rounded-xl px-4 py-3 text-sm font-bold transition-all flex items-center justify-center gap-2 shadow-md hover:shadow-green-200 active:scale-95">
                            <i class="fas fa-check-double"></i>
                            Masak Semua (${order.items.length} Item)
                        </button>
                    </form>`;
            }

            const cardHtml = `
                <div class="production-card-wrapper" data-timestamp="${order.timestamp}">
                    <div class="group bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col h-full relative animate-fade-in" id="card-sale-${order.sale_id}">
                        <!-- Status Indicator (Subtle) -->
                        <div class="absolute top-0 right-0 w-16 h-16 pointer-events-none overflow-hidden">
                            <div class="absolute top-0 right-0 translate-x-8 -translate-y-8 rotate-45 bg-orange-500/10 w-full h-full border-b border-orange-500/20"></div>
                        </div>

                        <div class="p-5 flex-1 flex flex-col">
                            <div class="flex items-start justify-between gap-3 mb-5">
                                <div class="space-y-1">
                                    <h3 class="font-bold text-gray-900 text-lg tracking-tight">${escapeHtml(order.invoice_number)}</h3>
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500 bg-gray-50 px-2 py-1 rounded-md border border-gray-100">
                                            <i class="far fa-clock text-orange-500"></i> ${order.created_at}
                                        </span>
                                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-md border border-blue-100">
                                            <i class="fas fa-layer-group"></i> ${order.items_count} Item
                                        </span>
                                    </div>
                                </div>
                                <div class="h-11 w-11 rounded-xl bg-gradient-to-br from-orange-400 to-orange-600 text-white flex items-center justify-center flex-shrink-0 shadow-lg shadow-orange-200 group-hover:rotate-12 transition-transform duration-300">
                                    <i class="fas fa-receipt text-lg"></i>
                                </div>
                            </div>

                            <div class="space-y-3 mb-6">
                                <div class="flex items-center gap-3 p-2 rounded-lg bg-gray-50/50 border border-transparent group-hover:border-gray-100 group-hover:bg-gray-50 transition-colors">
                                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-gray-400 border border-gray-100 shadow-sm">
                                        <i class="fas fa-user text-xs"></i>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700 truncate">${escapeHtml(order.customer_name)}</span>
                                </div>
                                ${tableHtml}
                            </div>

                            <div class="mt-auto">
                                <button type="button" onclick="openSaleModal('rt-${order.sale_id}')"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-xl px-4 py-3 text-sm font-bold transition-all flex items-center justify-center gap-2 shadow-sm hover:shadow-blue-200 active:scale-95">
                                    <i class="fas fa-external-link-alt text-xs opacity-70"></i> Buka Pesanan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal -->
                <div id="modal-sale-rt-${order.sale_id}" class="fixed inset-0 z-50 overflow-y-auto hidden" role="dialog" aria-modal="true">
                    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeSaleModal('rt-${order.sale_id}')"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                        <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-white/20">
                            <div class="relative bg-white px-6 pt-5 pb-4 border-b border-gray-100">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="h-12 w-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center border border-orange-100 shadow-sm">
                                            <i class="fas fa-clipboard-list text-xl"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-extrabold text-gray-900 tracking-tight">${escapeHtml(order.invoice_number)}</h3>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-xs font-semibold text-gray-500">${escapeHtml(order.customer_name)}</span>
                                                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                                <span class="text-xs font-medium text-gray-400">${order.created_at}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <button onclick="closeSaleModal('rt-${order.sale_id}')" class="w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-900 transition-all">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="p-6 bg-gray-50/30">
                                <div class="space-y-4 max-h-[50vh] overflow-y-auto pr-2 custom-scrollbar">${itemsHtml}</div>
                            </div>
                            <div class="bg-white px-6 py-5 border-t border-gray-100 flex flex-col items-center gap-3">
                                ${cookAllHtml}
                                <div class="flex items-center justify-center gap-2 text-gray-400">
                                    <span class="flex h-2 w-2 relative">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                                    </span>
                                    <p class="text-[11px] font-medium tracking-wide first-letter:uppercase italic">Gunakan tombol "Masak" untuk setiap item yang ingin diproses sekarang.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;

            grid.insertAdjacentHTML('afterbegin', cardHtml);
            
            // Re-sort cards
            sortProductionCards();

            return order.sale_id;
        };

        const playNotificationSound = function() {
            try {
                const audio = new Audio('{{ asset("assets/sounds/ting.mp3") }}');
                audio.play().catch(e => console.warn('[Production] Audio play failed (autoplay policy):', e));
            } catch (e) {
                console.error('[Production] Sound error:', e);
            }
        };

        channel.bind('new-order', function(data) {
            console.log('[Production] New order received:', data);
            const saleId = handleIncomingOrder(data.orderData);
            
            // Flash the card
            if (saleId) {
                const card = document.getElementById('card-sale-' + saleId);
                if (card) {
                    card.style.animation = 'none';
                    card.offsetHeight; // force reflow
                    card.style.animation = 'fadeInUp 0.5s ease-out';
                }
            }

            playNotificationSound();
            showRealtimeToast('Pesanan Baru: ' + data.orderData.invoice_number);
        });

        channel.bind('kitchen-bell', function(data) {
            console.log('[Production] Kitchen bell received:', data);
            const saleId = handleIncomingOrder(data.orderData);

            // Flash the card with a special color or animation for bell
            if (saleId) {
                const card = document.getElementById('card-sale-' + saleId);
                if (card) {
                    card.classList.add('ring-4', 'ring-blue-500', 'ring-opacity-50');
                    card.style.animation = 'none';
                    card.offsetHeight; // force reflow
                    card.style.animation = 'flashBlue 1s ease-in-out infinite';
                    
                    setTimeout(() => {
                        card.style.animation = '';
                        card.classList.remove('ring-4', 'ring-blue-500', 'ring-opacity-50');
                    }, 5000);
                }
            }

            playNotificationSound();
            showRealtimeToast('Permintaan Produksi: ' + data.orderData.invoice_number, true);
        });

        channel.bind('order-refunded', function(data) {
            console.log('[Production] Order refunded received:', data);
            const saleId = data.orderData.sale_id;
            
            // Remove the card if it exists
            const card = document.getElementById('card-sale-' + saleId);
            if (card) {
                const wrapper = card.closest('.production-card-wrapper');
                if (wrapper) {
                    wrapper.style.transition = 'all 0.4s ease-out';
                    wrapper.style.opacity = '0';
                    wrapper.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        wrapper.remove();
                        // Show empty state if no cards left
                        const grid = document.getElementById('production-grid');
                        if (grid) {
                            const wrappers = grid.querySelectorAll('.production-card-wrapper');
                            if (wrappers.length === 0 && !document.getElementById('no-orders-empty-state')) {
                                grid.innerHTML = `
                <div id="no-orders-empty-state" class="col-span-full py-20 text-center bg-white rounded-3xl border border-gray-200 border-dashed shadow-inner">
                    <div class="w-20 h-20 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-5 border border-red-100">
                        <i class="fas fa-times-circle text-red-500 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Semua Pesanan Selesai atau Diretur!</h3>
                    <p class="text-gray-500 text-sm max-w-xs mx-auto">Tidak ada antrian pesanan yang perlu diproduksi saat ini. Santai sejenak.</p>
                </div>
                                `;
                            }
                        }
                    }, 400);
                }
            }
            playNotificationSound();
            showRealtimeToast('Pesanan Diretur: ' + data.orderData.invoice_number, true); // True to show bell icon / alternate style
        });

        // Helper: escape HTML
        function escapeHtml(text) {
            if (!text) return '';
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return String(text).replace(/[&<>"']/g, m => map[m]);
        }

        // Helper: toast notification
        function showRealtimeToast(message, isBell = false) {
            const toast = document.createElement('div');
            const bgColor = isBell ? 'bg-blue-600' : 'bg-green-600';
            const icon = isBell ? 'fa-bell' : 'fa-check-circle';
            
            toast.className = `fixed top-6 right-6 z-[9999] ${bgColor} text-white px-5 py-3 rounded-xl shadow-2xl text-sm font-bold flex items-center gap-3 animate-slide-in`;
            toast.innerHTML = `<i class="fas ${icon} ${isBell ? 'animate-bounce' : ''}"></i> <span>${escapeHtml(message)}</span>`;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.transition = 'opacity 0.5s, transform 0.5s';
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => toast.remove(), 500);
            }, 4000);
        }
    })();
</script>
<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(100%); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes flashBlue {
        0%, 100% { background-color: white; }
        50% { background-color: #eff6ff; border-color: #3b82f6; }
    }
    @keyframes shake {
        0%, 100% { transform: rotate(0deg); }
        20%, 60% { transform: rotate(-10deg); }
        40%, 80% { transform: rotate(10deg); }
    }
    .animate-shake { animation: shake 0.5s ease-in-out infinite; }
    .animate-slide-in { animation: slideIn 0.4s ease-out; }
    .animate-fade-in { animation: fadeInUp 0.5s ease-out; }
</style>
@endpush
@endsection
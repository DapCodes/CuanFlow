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

        <!-- Tabs Navigation -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
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
                    <span class="bg-red-100 text-red-600 py-0.5 px-2.5 rounded-full text-xs font-bold">{{ $totalPending }} PESANAN</span>
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
        </div>

        <!-- QUEUE SECTION (Cards) -->
        <section id="content-queue" class="tab-content block">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($pendingSales as $sale)
                <div class="group bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col h-full relative">
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
                                            <h3 class="text-xl font-extrabold text-gray-900 tracking-tight" id="modal-title">{{ $sale->invoice_number }}</h3>
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
                                    @foreach($sale->items as $item)
                                    <div class="flex items-center justify-between p-5 bg-white border border-gray-200 rounded-2xl shadow-sm hover:border-blue-300 transition-colors">
                                        <div class="flex items-center gap-4">
                                            <div class="h-12 w-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 border border-blue-100 group-hover:scale-110 transition-transform">
                                                <i class="fas fa-utensils text-lg"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="text-sm font-bold text-gray-900 truncate pr-4">{{ $item->product->name }}</h4>
                                                <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-gray-100 text-gray-700 border border-gray-200">
                                                        {{ number_format($item->quantity) }} {{ $item->product->unit->name ?? 'Pcs' }}
                                                    </span>
                                                    @if($item->notes)
                                                    <div class="flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-100">
                                                        <i class="fas fa-comment-dots text-[10px]"></i>
                                                        <span class="text-[11px] font-medium">{{ $item->notes }}</span>
                                                    </div>
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
                            <div class="bg-white px-6 py-5 border-t border-gray-100">
                                <div class="flex items-center justify-center gap-2 text-gray-400">
                                    <span class="flex h-2 w-2 relative">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                                    </span>
                                    <p class="text-[11px] font-medium tracking-wide first-letter:uppercase italic">
                                        Gunakan tombol "Masak" untuk setiap item yang ingin diproses sekarang.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-20 text-center bg-white rounded-3xl border border-gray-200 border-dashed shadow-inner">
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
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Kode</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Stok</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Min. Stok</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white" id="productTableBody">
                        @forelse($stockProducts as $product)
                        <tr class="hover:bg-gray-50 transition-colors product-row" 
                            data-name="{{ strtolower($product['name']) }}"
                            data-stock-status="{{ $product['stock'] == 0 ? 'empty' : ($product['is_low_stock'] ? 'low' : 'available') }}">
                            <td class="px-6 py-3 whitespace-nowrap">
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
                            <td class="px-6 py-3 whitespace-nowrap">
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
                            <td class="px-6 py-3 whitespace-nowrap">
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
    }

    function openSaleModal(saleId) {
        document.getElementById('modal-sale-' + saleId).classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeSaleModal(saleId) {
        document.getElementById('modal-sale-' + saleId).classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initial Tab State (Queue by default, or Stock if Queue is empty?)
        // Let's stick to Queue as default for KDS focus
        
        const searchInput = document.getElementById('searchProduct');
        const filterStock = document.getElementById('filterStock');
        // Note: multiple tables might need separate search logic if heavily used, 
        // but for now we only have search on the Stock table. 
        // The Queue section doesn't have a search bar yet (it was removed/not added).
        
        if(searchInput && filterStock) {
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
            }

            searchInput.addEventListener('input', filterProducts);
            filterStock.addEventListener('change', filterProducts);
        }
    });
</script>
@endpush
@endsection
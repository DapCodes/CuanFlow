@extends('layouts.app')

@section('title', 'Produksi - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Produksi</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">
        
        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Produksi & Stok
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola antrian pesanan dapur dan kontrol inventaris produk jadi.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @can('buat produk')
                <a href="{{ route('products-hpp.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all active:scale-95">
                    <span>Tambah Produk</span>
                </a>
                @endcan
            </div>
        </section>

        <!-- Tabs & Sort Navigation -->
        <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-gray-200 gap-4">
            <nav class="-mb-px flex space-x-8 overflow-x-auto no-scrollbar" aria-label="Tabs" id="productionTabs">
                <button type="button" 
                    id="tab-queue"
                    class="tab-btn active-tab border-cuan-green text-cuan-green whitespace-nowrap py-4 px-1 border-b-2 font-black text-xs uppercase tracking-widest flex items-center gap-2 transition-all"
                    onclick="switchTab('queue')">
                    Antrian Pesanan
                    @php
                        $totalPending = $pendingSales->count();
                    @endphp
                    @if($totalPending > 0)
                    <span class="bg-red-500 text-white py-0.5 px-2 rounded-lg text-[10px] font-black leading-none">{{ $totalPending }}</span>
                    @endif
                </button>
                <button type="button" 
                    id="tab-stock"
                    class="tab-btn border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-black text-xs uppercase tracking-widest flex items-center gap-2 transition-all"
                    onclick="switchTab('stock')">
                    Stok & Inventaris
                </button>
            </nav>

            <div class="pb-3 md:pb-0 flex items-center gap-3 w-full md:w-auto overflow-x-auto no-scrollbar">
                <div class="flex items-center p-1 bg-gray-100 rounded-xl border border-gray-200">
                    <input type="hidden" id="sort-mode" value="oldest">
                    <button type="button" onclick="setSortMode('oldest')" id="btn-sort-oldest" 
                        class="px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg shadow-sm bg-white text-cuan-green transition-all border border-transparent whitespace-nowrap">
                        Terlama
                    </button>
                    <button type="button" onclick="setSortMode('newest')" id="btn-sort-newest" 
                        class="px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg text-gray-400 hover:text-gray-600 transition-all border border-transparent whitespace-nowrap">
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
                    <div class="group bg-white border border-gray-200 rounded-[2rem] shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col h-full relative" id="card-sale-{{ $sale->id }}">
                        
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="flex items-start justify-between gap-3 mb-6">
                                <div class="space-y-1">
                                    <h3 class="font-black text-gray-900 text-lg tracking-tight">{{ $sale->invoice_number }}</h3>
                                    <div class="flex flex-wrap items-center gap-2 pt-1">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-amber-50 text-amber-600 border border-amber-100">
                                            {{ $sale->created_at->format('H:i') }}
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-blue-50 text-blue-600 border border-blue-100">
                                            {{ $sale->items->count() }} Item
                                        </span>
                                    </div>
                                </div>
                                <div class="h-10 w-10 rounded-2xl bg-gray-50 flex items-center justify-center flex-shrink-0 text-gray-400 group-hover:bg-cuan-green group-hover:text-white transition-all duration-300">
                                    <i class="fas fa-receipt text-sm"></i>
                                </div>
                            </div>

                            <div class="space-y-3 mb-8">
                                <div class="flex items-center gap-3 p-3 rounded-2xl bg-gray-50 border border-transparent group-hover:border-gray-100 group-hover:bg-white transition-all">
                                    <div class="w-8 h-8 rounded-xl bg-white flex items-center justify-center text-gray-400 border border-gray-100 shadow-sm">
                                        <i class="fas fa-user text-[10px]"></i>
                                    </div>
                                    <span class="text-xs font-bold text-gray-700 truncate capitalize">{{ $sale->customer->name ?? 'Pelanggan Umum' }}</span>
                                </div>
                                
                                @if($sale->table)
                                <div class="flex items-center gap-3 p-3 rounded-2xl bg-gray-50 border border-transparent group-hover:border-gray-100 group-hover:bg-white transition-all">
                                    <div class="w-8 h-8 rounded-xl bg-white flex items-center justify-center text-gray-400 border border-gray-100 shadow-sm">
                                        <i class="fas fa-chair text-[10px]"></i>
                                    </div>
                                    <span class="text-xs font-bold text-gray-700">Meja: {{ $sale->table->name }}</span>
                                </div>
                                @endif
                            </div>

                            <div class="mt-auto pt-4">
                                <button type="button" 
                                    onclick="openSaleModal('{{ $sale->id }}')"
                                    class="w-full bg-cuan-green hover:bg-cuan-dark text-white rounded-2xl px-5 py-4 text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                                    Buka Pesanan
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for each sale -->
                    <div id="modal-sale-{{ $sale->id }}" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeSaleModal('{{ $sale->id }}')"></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            
                            <div class="inline-block align-bottom bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-100">
                                <!-- Modal Header -->
                                <div class="relative px-8 pt-8 pb-6 border-b border-gray-50">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-xl font-black text-gray-900 tracking-tight">{{ $sale->invoice_number }}</h3>
                                            <div class="flex items-center gap-2 mt-2">
                                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{ $sale->customer->name ?? 'Pelanggan Umum' }}</span>
                                                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{ $sale->created_at->format('d M, H:i') }}</span>
                                            </div>
                                        </div>
                                        <button onclick="closeSaleModal('{{ $sale->id }}')" class="w-10 h-10 rounded-2xl bg-gray-50 hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-900 transition-all">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Modal Body -->
                                <div class="p-8">
                                    <div class="space-y-4 max-h-[50vh] overflow-y-auto pr-2 custom-scrollbar no-scrollbar">
                                        @php
                                            $saleItems = $sale->items->filter(function($item) {
                                                return $item->production_status === 'pending' && $item->product && !$item->product->is_stock;
                                            });
                                        @endphp
                                        @foreach($saleItems as $item)
                                        <div class="p-5 bg-gray-50 border border-gray-100 rounded-3xl hover:bg-white hover:border-cuan-green/20 hover:shadow-xl hover:shadow-gray-200/50 transition-all">
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                                <div class="flex items-center gap-4">
                                                    <div class="h-12 w-12 rounded-2xl bg-white flex items-center justify-center flex-shrink-0 shadow-sm border border-gray-100">
                                                        <i class="fas fa-utensils text-gray-400"></i>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <h4 class="text-sm font-black text-gray-900 truncate pr-4">{{ $item->product->name }}</h4>
                                                        <div class="flex flex-wrap items-center gap-2 mt-2">
                                                            <span class="px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest bg-white border border-gray-100 text-gray-600 shadow-sm">
                                                                {{ (int)$item->quantity }} {{ $item->product->unit->name ?? 'Pcs' }}
                                                            </span>
                                                            @if($item->notes)
                                                            <span class="px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest bg-amber-50 border border-amber-100 text-amber-600">
                                                                {{ $item->notes }}
                                                            </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="flex flex-wrap items-center gap-2 mt-2 sm:mt-0">
                                                    @if($item->product->defaultRecipe)
                                                    <a href="{{ route('production.preparation', $item->id) }}" 
                                                       class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-50 transition-all active:scale-95 shadow-sm">
                                                        Detail
                                                    </a>
                                                    <form action="{{ route('production.store') }}" method="POST" class="flex-1 sm:flex-none">
                                                        @csrf
                                                        <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                                        <input type="hidden" name="planned_quantity" value="{{ $item->quantity }}">
                                                        <input type="hidden" name="sale_item_id" value="{{ $item->id }}">
                                                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-cuan-green text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-cuan-dark transition-all active:scale-95 shadow-md shadow-cuan-green/20">
                                                            Masak
                                                        </button>
                                                    </form>
                                                    @else
                                                    <div class="w-full flex items-center justify-center px-4 py-2 bg-red-50 text-red-600 rounded-xl border border-red-100 text-[9px] font-black uppercase tracking-widest opacity-60">
                                                        Tanpa Resep
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Modal Footer -->
                                <div class="px-8 pb-8 flex flex-col items-center gap-4">
                                    @if($saleItems->count() > 1)
                                    <form action="{{ route('production.store-all') }}" method="POST" class="w-full">
                                        @csrf
                                        <input type="hidden" name="sale_id" value="{{ $sale->id }}">
                                        <button type="submit" class="w-full bg-cuan-green hover:bg-cuan-dark text-white rounded-[1.5rem] px-5 py-4 text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                                            Masak Semua ({{ $saleItems->count() }} Item)
                                        </button>
                                    </form>
                                    @endif
                                    <div class="flex items-center gap-2 text-gray-400">
                                        <p class="text-[9px] font-black uppercase tracking-widest leading-relaxed text-center opacity-60">Pilih "Masak" untuk memproses item pesanan secara efisien.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div id="no-orders-empty-state" class="col-span-full py-20 bg-white rounded-[3rem] border border-gray-100 shadow-sm text-center">
                    <div class="w-20 h-20 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-check-double text-gray-300 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-2 tracking-tight">Dapur Bersih!</h3>
                    <p class="text-xs font-medium text-gray-400 max-w-xs mx-auto uppercase tracking-widest">Tidak ada antrian pesanan saat ini.</p>
                </div>
                @endforelse
            </div>
        </section>

        <!-- STOCK SECTION (Table) -->
        <section id="content-stock" class="tab-content hidden space-y-6">
            <x-card-container>
                <div class="px-6 py-5 border-b border-gray-100 bg-white space-y-4 md:space-y-0 md:flex md:items-center md:gap-4">
                    <div class="flex-1 relative">
                        <input type="text" id="searchProduct" placeholder="Cari nama produk stok..." 
                            class="w-full pl-12 pr-4 py-3 rounded-2xl border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all font-bold">
                        <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 text-sm"></i>
                    </div>
                    <div class="flex gap-3">
                        <select id="filterStock" class="rounded-2xl border border-gray-300 px-5 py-3 text-xs font-black uppercase tracking-widest focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all bg-white min-w-[160px]">
                            <option value="">Semua Status</option>
                            <option value="low">Stok Menipis</option>
                            <option value="available">Tersedia</option>
                            <option value="empty">Kosong</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                            <tr>
                                <th class="hidden md:table-cell px-6 py-4 text-left">Kode</th>
                                <th class="px-6 py-4 text-left">Produk</th>
                                <th class="hidden lg:table-cell px-6 py-4 text-left text-center">Kategori</th>
                                <th class="px-6 py-4 text-right">Stok</th>
                                <th class="hidden xl:table-cell px-6 py-4 text-right">Min. Stok</th>
                                <th class="px-6 py-4 text-left">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white" id="productTableBody">
                            @forelse($stockProducts as $product)
                            <tr class="hover:bg-gray-50 transition-colors product-row" 
                                data-name="{{ strtolower($product['name']) }}"
                                data-stock-status="{{ $product['stock'] == 0 ? 'empty' : ($product['is_low_stock'] ? 'low' : 'available') }}">
                                
                                <td class="hidden md:table-cell px-6 py-5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-[10px] font-black bg-gray-100 text-gray-500 font-mono tracking-tighter">
                                        #{{ $product['code'] }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="flex items-center gap-4">
                                        @if($product['image'])
                                            <img src="{{ Storage::url($product['image']) }}" alt="{{ $product['name'] }}" 
                                                class="h-14 w-14 rounded-2xl object-cover border-2 border-white shadow-lg shadow-gray-200/50">
                                        @else
                                            <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-cuan-green to-cuan-dark flex items-center justify-center border-2 border-white shadow-lg shadow-cuan-green/20">
                                                <i class="fas fa-flask text-white text-sm"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-black text-gray-900 leading-tight">{{ $product['name'] }}</div>
                                            <div class="text-[9px] font-black uppercase tracking-widest text-gray-400 mt-1">
                                                {{ $product['unit'] }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="hidden lg:table-cell px-6 py-5 whitespace-nowrap text-center">
                                    @if($product['category'])
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-blue-50 text-blue-600 border border-blue-100">
                                        {{ $product['category'] }}
                                    </span>
                                    @else
                                    <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest">-</span>
                                    @endif
                                </td>

                                <td class="px-6 py-5 whitespace-nowrap text-right">
                                    <div class="text-sm">
                                        <span class="font-black {{ $product['stock'] == 0 ? 'text-red-600' : ($product['is_low_stock'] ? 'text-amber-600' : 'text-cuan-green') }}">
                                            {{ number_format($product['stock'], 2) }}
                                        </span>
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">{{ $product['unit'] }}</span>
                                    </div>
                                </td>

                                <td class="hidden xl:table-cell px-6 py-5 whitespace-nowrap text-right">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        {{ number_format($product['min_stock'], 2) }}
                                    </span>
                                </td>

                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="flex flex-col gap-1.5 align-middle">
                                        @if($product['stock'] == 0)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-red-50 text-red-600 border border-red-200 w-fit">
                                            Kosong
                                        </span>
                                        @elseif($product['is_low_stock'])
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-amber-50 text-amber-600 border border-amber-200 w-fit">
                                            Menipis
                                        </span>
                                        @else
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-cuan-green/10 text-cuan-green border border-cuan-green/20 w-fit">
                                            {{ number_format($product['total_valid_qty'], 0) }} Tersedia
                                        </span>
                                        @endif

                                        @if($product['total_expired_qty'] > 0)
                                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-[8px] font-black uppercase tracking-widest bg-red-600 text-white animate-pulse shadow-sm w-fit">
                                            {{ number_format($product['total_expired_qty'], 0) }} Kadaluarsa
                                        </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-5 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center gap-2">
                                        @can('lihat stok produksi')
                                        <a href="{{ route('production.stock.show', $product['id']) }}" 
                                           class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 hover:bg-gray-100 transition-all active:scale-95 shadow-sm"
                                           title="Detail Stok">
                                            <i class="fas fa-chart-line text-xs"></i>
                                        </a>
                                        @endcan
                                        
                                        @if($product['has_recipe'])
                                            @can('buat produksi')
                                            <a href="{{ route('production.create', ['product_id' => $product['id']]) }}" 
                                               class="px-4 py-2 bg-cuan-green/10 text-cuan-green hover:bg-cuan-green hover:text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all active:scale-95 shadow-sm">
                                                Produksi
                                            </a>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center text-gray-400 font-bold uppercase tracking-widest text-[10px]">
                                    Belum ada data stok produk.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card-container>
        </section>

        {{-- RIWAYAT TERBARU --}}
        @if($recentProductions->count() > 0)
        <section class="space-y-4">
            <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Riwayat Terbaru</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($recentProductions as $production)
                <div class="p-5 bg-white border border-gray-100 rounded-3xl hover:shadow-xl hover:shadow-gray-200/50 transition-all flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center flex-shrink-0 text-gray-400 border border-gray-100 shadow-sm">
                            <i class="fas fa-flask text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="text-sm font-black text-gray-900 truncate">{{ $production->product->name }}</h4>
                                <span class="px-2 py-0.5 rounded-lg text-[8px] font-black font-mono bg-gray-100 text-gray-500 border border-gray-200">{{ $production->batch_number }}</span>
                            </div>
                            <div class="flex items-center gap-3 mt-1.5 flex-wrap">
                                <span class="text-[9px] font-black uppercase tracking-widest text-cuan-green">
                                    {{ number_format($production->actual_quantity ?? $production->planned_quantity, 2) }} {{ $production->product->unit->name ?? '' }}
                                </span>
                                <span class="text-[9px] font-black uppercase tracking-widest text-gray-300">
                                    {{ $production->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @php
                            $statusKey = $production->status;
                            if ($production->is_disposed) {
                                $statusKey = 'disposed';
                            }
                            
                            $statusConfig = [
                                'planned' => ['class' => 'bg-gray-50 text-gray-400 border-gray-100', 'text' => 'Direncanakan'],
                                'in_progress' => ['class' => 'bg-blue-50 text-blue-600 border-blue-100', 'text' => 'Diproses'],
                                'completed' => ['class' => 'bg-cuan-green/10 text-cuan-green border-cuan-green/20', 'text' => 'Selesai'],
                                'cancelled' => ['class' => 'bg-red-50 text-red-500 border-red-100', 'text' => 'Batal'],
                                'disposed' => ['class' => 'bg-amber-50 text-amber-600 border-amber-100', 'text' => 'Dibuang'],
                            ];
                            $config = $statusConfig[$statusKey] ?? $statusConfig['planned'];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest border {{ $config['class'] }}">
                            {{ $config['text'] }}
                        </span>
                        @can('lihat produksi')
                         <a href="{{ route('production.show', $production->id) }}" 
                            class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 hover:bg-gray-100 transition-all active:scale-95 shadow-sm"
                            title="Detail">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                        @endcan
                    </div>
                </div>
                @endforeach
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
            btn.classList.remove('active-tab', 'border-cuan-green', 'text-cuan-green');
            btn.classList.add('border-transparent', 'text-gray-400');
        });
        
        const activeBtn = document.getElementById('tab-' + tabId);
        if (activeBtn) {
            activeBtn.classList.remove('border-transparent', 'text-gray-400');
            activeBtn.classList.add('active-tab', 'border-cuan-green', 'text-cuan-green');
        }

        // Update Content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        const activeContent = document.getElementById('content-' + tabId);
        if (activeContent) activeContent.classList.remove('hidden');

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

    // SweetAlert2 notification handler
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 3000,
                iconColor: '#658C58',
                customClass: {
                    popup: 'rounded-[3rem] border-none shadow-2xl',
                    title: 'font-black text-gray-900',
                    htmlContainer: 'text-sm font-medium text-gray-500'
                }
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: "{{ session('error') }}",
                confirmButtonColor: '#ef4444',
                customClass: {
                    popup: 'rounded-[3rem] border-none shadow-2xl',
                    title: 'font-black text-gray-900',
                    htmlContainer: 'text-sm font-medium text-gray-500'
                }
            });
        @endif
    });

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
                title: 'Validasi Stok...',
                text: 'Memeriksa ketersediaan bahan baku antar-ruang.',
                allowOutsideClick: false,
                customClass: {
                    popup: 'rounded-[3rem] border-none shadow-2xl',
                    title: 'font-black text-gray-900',
                    htmlContainer: 'text-sm font-medium text-gray-500'
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
                    let materialList = '<div class="space-y-3 mt-4 text-left">';
                    res.insufficient.forEach(m => {
                        materialList += `<div class="p-3 bg-red-50 border border-red-100 rounded-2xl flex justify-between items-center">
                            <span class="text-xs font-black uppercase text-red-700">${m.name}</span>
                            <span class="text-[10px] font-black text-red-500">-${parseFloat(m.shortage).toFixed(2)} ${m.unit}</span>
                        </div>`;
                    });
                    materialList += '</div>';

                    Swal.fire({
                        title: 'Stok Tidak Cukup',
                        html: `
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Beberapa bahan baku tidak tersedia di dapur.</p>
                            ${materialList}
                             <div class="mt-8 p-4 bg-gray-50 border border-gray-100 rounded-[1.5rem] text-left">
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest leading-relaxed">
                                    Pilih <b>Masak Tetap</b> untuk melanjutkan dengan stok yang ada, atau <b>Refund</b> untuk me-refund transaksi ini.
                                </p>
                            </div>
                        `,
                        icon: 'warning',
                        iconColor: '#f97316',
                        showCancelButton: true,
                        confirmButtonText: 'Masak Tetap',
                        cancelButtonText: 'Refund Pesanan',
                        customClass: {
                            popup: 'rounded-[3rem] border-none shadow-2xl',
                            confirmButton: 'rounded-xl px-6 py-3 font-black text-xs uppercase tracking-widest bg-cuan-green text-white hover:bg-cuan-dark transition-all mx-2',
                            cancelButton: 'rounded-xl px-6 py-3 font-black text-xs uppercase tracking-widest bg-red-50 text-red-600 border border-red-100 hover:bg-red-500 hover:text-white transition-all mx-2',
                            actions: 'mt-8',
                        },
                        buttonsStyling: false,
                        reverseButtons: true,
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'ignore_insufficient';
                            hiddenInput.value = '1';
                            form.appendChild(hiddenInput);
                            form.dataset.validated = 'true';
                            form.submit();
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            Swal.fire({
                                title: 'Konfirmasi Refund',
                                text: 'Pesanan akan dibatalkan otomatis dan stok bahan tidak akan dipotong bila Anda men-refund sekarang.',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, Refund',
                                cancelButtonText: 'Nanti',
                                customClass: {
                                    popup: 'rounded-[3rem] border-none shadow-2xl shadow-red-200/50',
                                    confirmButton: 'rounded-xl px-6 py-3 font-black text-xs uppercase tracking-widest bg-red-600 text-white hover:bg-red-700 transition-all mx-2',
                                    cancelButton: 'rounded-xl px-6 py-3 font-black text-xs uppercase tracking-widest bg-gray-50 border border-gray-100 text-gray-400 hover:bg-gray-100 transition-all mx-2',
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
                Swal.fire('Error', 'Gagal memvalidasi bahan baku', 'error');
            });
        }
    });

    function refundSale(saleId, saleItemId) {
        Swal.fire({
            title: 'Memproses Refund...',
            allowOutsideClick: false,
            customClass: { popup: 'rounded-[3rem] border-none shadow-2xl' },
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
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message,
                    confirmButtonText: 'Tutup',
                    customClass: {
                        popup: 'rounded-[3rem] border-none shadow-2xl',
                        confirmButton: 'rounded-xl px-6 py-3 font-black text-xs uppercase tracking-widest bg-cuan-green text-white hover:bg-cuan-dark transition-all'
                    },
                    buttonsStyling: false
                }).then(() => { location.reload(); });
            } else {
                Swal.fire('Gagal', res.message, 'error');
            }
        })
        .catch(() => {
            Swal.fire('Error', 'Gagal memproses refund', 'error');
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
            
            filterProducts();
        }
    });

    window.setSortMode = function(mode) {
        const currentMode = document.getElementById('sort-mode');
        if(currentMode) currentMode.value = mode;

        const btnOldest = document.getElementById('btn-sort-oldest');
        const btnNewest = document.getElementById('btn-sort-newest');

        if (mode === 'oldest') {
            btnOldest.className = 'px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg shadow-sm bg-white text-cuan-green transition-all border border-transparent whitespace-nowrap';
            btnNewest.className = 'px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg text-gray-400 hover:text-gray-600 transition-all border border-transparent whitespace-nowrap';
        } else {
            btnOldest.className = 'px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg text-gray-400 hover:text-gray-600 transition-all border border-transparent whitespace-nowrap';
            btnNewest.className = 'px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-lg shadow-sm bg-white text-cuan-green transition-all border border-transparent whitespace-nowrap';
        }

        updateURLParams({ sort: mode });
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

        wrappers.forEach(w => grid.appendChild(w));
    };
</script>

<!-- Pusher Realtime for Production Queue -->
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    (function() {
        const PUSHER_KEY = @json(config('broadcasting.connections.pusher.key'));
        const PUSHER_CLUSTER = @json(config('broadcasting.connections.pusher.options.cluster'));
        const OUTLET_ID = @json(auth()->user()->outlet_id);

        if (!PUSHER_KEY || PUSHER_KEY === 'your-app-key') {
            console.warn('[Production] Pusher key not configured. Realtime disabled.');
            return;
        }

        const pusher = new Pusher(PUSHER_KEY, {
            cluster: PUSHER_CLUSTER,
            forceTLS: true
        });

        const channel = pusher.subscribe('production.outlet.' + OUTLET_ID);

        window.handleIncomingOrder = function(order) {
            if (!order || !order.items || order.items.length === 0) return;

            if (document.getElementById('card-sale-' + order.sale_id)) {
                return order.sale_id;
            }

            const emptyState = document.querySelector('#content-queue #no-orders-empty-state');
            if (emptyState) emptyState.remove();

            const grid = document.getElementById('production-grid');
            if (!grid) return;

            // Simple HTML build for pusher
            let itemsHtml = '';
            order.items.forEach(function(item) {
                const notesHtml = item.notes 
                    ? `<span class="px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest bg-amber-50 border border-amber-100 text-amber-600">${escapeHtml(item.notes)}</span>` 
                    : '';

                const actionHtml = item.has_recipe
                    ? `<div class="flex items-center gap-2">
                           <a href="/production/preparation/${item.id}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-50 transition-all shadow-sm">Detail</a>
                           <form action="/production" method="POST">
                               <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]')?.content || ''}">
                               <input type="hidden" name="product_id" value="${item.product_id}">
                               <input type="hidden" name="planned_quantity" value="${item.quantity}">
                               <input type="hidden" name="sale_item_id" value="${item.id}">
                               <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-cuan-green text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-cuan-dark transition-all shadow-md shadow-cuan-green/20">Masak</button>
                           </form>
                       </div>`
                    : `<div class="w-full flex items-center justify-center px-4 py-2 bg-red-50 text-red-600 rounded-xl border border-red-100 text-[9px] font-black uppercase tracking-widest opacity-60">Tanpa Resep</div>`;

                itemsHtml += `
                    <div class="p-5 bg-gray-50 border border-gray-100 rounded-3xl hover:bg-white hover:border-cuan-green/20 hover:shadow-xl hover:shadow-gray-200/50 transition-all">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 rounded-2xl bg-white flex items-center justify-center flex-shrink-0 shadow-sm border border-gray-100">
                                    <i class="fas fa-utensils text-gray-400"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-sm font-black text-gray-900 truncate pr-4">${escapeHtml(item.product_name)}</h4>
                                    <div class="flex flex-wrap items-center gap-2 mt-2">
                                        <span class="px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest bg-white border border-gray-100 text-gray-600 shadow-sm">${item.quantity} ${escapeHtml(item.unit)}</span>
                                        ${notesHtml}
                                    </div>
                                </div>
                            </div>
                            <div class="flex-shrink-0">${actionHtml}</div>
                        </div>
                    </div>`;
            });

            const tableHtml = order.table_name 
                ? `<div class="flex items-center gap-3 p-3 rounded-2xl bg-gray-50 border border-transparent group-hover:border-gray-100 group-hover:bg-white transition-all">
                       <div class="w-8 h-8 rounded-xl bg-white flex items-center justify-center text-gray-400 border border-gray-100 shadow-sm">
                           <i class="fas fa-chair text-[10px]"></i>
                       </div>
                       <span class="text-xs font-bold text-gray-700">Meja: ${escapeHtml(order.table_name)}</span>
                   </div>` 
                : '';

            const cardHtml = `
                <div class="production-card-wrapper" data-timestamp="${order.timestamp}">
                    <div class="group bg-white border border-gray-200 rounded-[2rem] shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col h-full relative animate-fade-in" id="card-sale-${order.sale_id}">
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="flex items-start justify-between gap-3 mb-6">
                                <div class="space-y-1">
                                    <h3 class="font-black text-gray-900 text-lg tracking-tight">${escapeHtml(order.invoice_number)}</h3>
                                    <div class="flex flex-wrap items-center gap-2 pt-1">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-amber-50 text-amber-600 border border-amber-100">${order.created_at}</span>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-blue-50 text-blue-600 border border-blue-100">${order.items_count} Item</span>
                                    </div>
                                </div>
                                <div class="h-10 w-10 rounded-2xl bg-gray-50 flex items-center justify-center flex-shrink-0 text-gray-400 group-hover:bg-cuan-green group-hover:text-white transition-all duration-300">
                                    <i class="fas fa-receipt text-sm"></i>
                                </div>
                            </div>

                            <div class="space-y-3 mb-8">
                                <div class="flex items-center gap-3 p-3 rounded-2xl bg-gray-50 border border-transparent group-hover:border-gray-100 group-hover:bg-white transition-all">
                                    <div class="w-8 h-8 rounded-xl bg-white flex items-center justify-center text-gray-400 border border-gray-100 shadow-sm">
                                        <i class="fas fa-user text-[10px]"></i>
                                    </div>
                                    <span class="text-xs font-bold text-gray-700 truncate capitalize">${escapeHtml(order.customer_name)}</span>
                                </div>
                                ${tableHtml}
                            </div>

                            <div class="mt-auto pt-4">
                                <button type="button" onclick="openSaleModal('${order.sale_id}')"
                                    class="w-full bg-cuan-green hover:bg-cuan-dark text-white rounded-2xl px-5 py-4 text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                                    Buka Pesanan
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Realtime Modal Wrapper Placeholder -->
                    <div id="modal-sale-${order.sale_id}" class="fixed inset-0 z-50 overflow-y-auto hidden" role="dialog" aria-modal="true">
                        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeSaleModal('${order.sale_id}')"></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                            <div class="inline-block align-bottom bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-100">
                                <div class="relative px-8 pt-8 pb-6 border-b border-gray-50">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-xl font-black text-gray-900 tracking-tight">${escapeHtml(order.invoice_number)}</h3>
                                            <div class="flex items-center gap-2 mt-2">
                                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">${escapeHtml(order.customer_name)}</span>
                                                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">${order.created_at}</span>
                                            </div>
                                        </div>
                                        <button onclick="closeSaleModal('${order.sale_id}')" class="w-10 h-10 rounded-2xl bg-gray-50 hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-900 transition-all">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="p-8">
                                    <div class="space-y-4 max-h-[50vh] overflow-y-auto pr-2 custom-scrollbar no-scrollbar">${itemsHtml}</div>
                                </div>
                                <div class="px-8 pb-8 flex flex-col items-center gap-4">
                                     ${order.items.length > 1 ? `
                                        <form action="/production/store-all" method="POST" class="w-full">
                                            <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]')?.content || ''}">
                                            <input type="hidden" name="sale_id" value="${order.sale_id}">
                                            <button type="submit" class="w-full bg-cuan-green hover:bg-cuan-dark text-white rounded-[1.5rem] px-5 py-4 text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                                                Masak Semua (${order.items.length} Item)
                                            </button>
                                        </form>
                                     ` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;

            grid.insertAdjacentHTML('afterbegin', cardHtml);
            sortProductionCards();
            return order.sale_id;
        };

        const playNotificationSound = function() {
            try {
                const audio = new Audio('{{ asset("assets/sounds/ting.mp3") }}');
                audio.play().catch(e => console.warn('[Production] Audio play failed:', e));
            } catch (e) {}
        };

        const showRealtimeToast = function(message) {
             Swal.fire({
                title: 'Pesanan Baru',
                text: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                background: '#658C58',
                color: '#fff',
                icon: 'info',
                iconColor: '#fff',
                customClass: {
                    popup: 'rounded-2xl',
                    title: 'font-black',
                }
            });
        };

        channel.bind('new-order', function(data) {
            const saleId = handleIncomingOrder(data.orderData);
            playNotificationSound();
            showRealtimeToast('Invoise: ' + data.orderData.invoice_number);
        });

        channel.bind('kitchen-bell', function(data) {
            const saleId = handleIncomingOrder(data.orderData);
            if (saleId) {
                const card = document.getElementById('card-sale-' + saleId);
                if (card) {
                    card.classList.add('ring-4', 'ring-amber-400', 'ring-opacity-50');
                    setTimeout(() => card.classList.remove('ring-4', 'ring-amber-400', 'ring-opacity-50'), 10000);
                }
            }
            playNotificationSound();
            showRealtimeToast('Permintaan Produksi: ' + data.orderData.invoice_number);
        });

        channel.bind('order-refunded', function(data) {
            const saleId = data.orderData.sale_id;
            const card = document.getElementById('card-sale-' + saleId);
            if (card) {
                const wrapper = card.closest('.production-card-wrapper');
                if (wrapper) {
                    wrapper.style.transition = 'all 0.5s ease';
                    wrapper.style.opacity = '0';
                    wrapper.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        wrapper.remove();
                        const grid = document.getElementById('production-grid');
                        if (grid && grid.querySelectorAll('.production-card-wrapper').length === 0) {
                            grid.innerHTML = `<div id="no-orders-empty-state" class="col-span-full py-20 bg-white rounded-[3rem] border border-gray-100 shadow-sm text-center"><div class="w-20 h-20 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-6"><i class="fas fa-check-double text-gray-300 text-3xl"></i></div><h3 class="text-xl font-black text-gray-900 mb-2 tracking-tight">Dapur Bersih!</h3><p class="text-xs font-medium text-gray-400 max-w-xs mx-auto uppercase tracking-widest">Tidak ada antrian pesanan saat ini.</p></div>`;
                        }
                    }, 500);
                }
            }
        });

        function escapeHtml(text) {
            if (!text) return '';
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return String(text).replace(/[&<>"']/g, m => map[m]);
        }
    })();
</script>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fadeInUp 0.5s ease-out; }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endpush
@endsection
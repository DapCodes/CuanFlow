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

        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            <div class="border-b border-gray-200 px-4 md:px-6 py-4 bg-gray-50">
                <div class="flex flex-col md:flex-row gap-3">
                    <div class="flex-1">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Cari produk</label>
                        <div class="relative">
                            <input type="text" id="searchProduct" placeholder="Cari berdasarkan nama produk..." 
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
                        @forelse($products as $product)
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
                                @if($product['stock'] == 0)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-100">
                                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>
                                    Kosong
                                </span>
                                @elseif($product['is_low_stock'])
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700 border border-yellow-100">
                                    <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1.5"></span>
                                    Menipis
                                </span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-100">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                    Tersedia
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('production.stock.show', $product['id']) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-gray-200 bg-white text-gray-600 hover:bg-gray-50"
                                       title="Detail Stok">
                                        <i class="fas fa-chart-line text-xs"></i>
                                    </a>
                                    @if($product['has_recipe'])
                                    <a href="{{ route('production.create', ['product_id' => $product['id']]) }}" 
                                       class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-xs font-semibold"
                                       title="Produksi">
                                        <i class="fas fa-plus-circle text-xs"></i>
                                        <span>Produksi</span>
                                    </a>
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
                                    <h3 class="text-base font-semibold text-gray-900 mb-1">Belum Ada Produk</h3>
                                    <p class="text-sm text-gray-500 mb-4 max-w-sm">
                                        Tambahkan produk terlebih dahulu untuk memulai produksi
                                    </p>
                                    <a href="{{ route('products-hpp.create') }}" 
                                       class="inline-flex items-center gap-2 rounded-lg bg-green-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-600">
                                        <i class="fas fa-plus-circle text-xs"></i>
                                        Tambah Produk
                                    </a>
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
                                $statusConfig = [
                                    'planned' => ['class' => 'bg-gray-50 text-gray-700 border-gray-200', 'icon' => 'fa-clock', 'text' => 'Direncanakan'],
                                    'in_progress' => ['class' => 'bg-blue-50 text-blue-700 border-blue-200', 'icon' => 'fa-spinner', 'text' => 'Proses'],
                                    'completed' => ['class' => 'bg-green-50 text-green-700 border-green-200', 'icon' => 'fa-check-circle', 'text' => 'Selesai'],
                                    'cancelled' => ['class' => 'bg-red-50 text-red-700 border-red-200', 'icon' => 'fa-times-circle', 'text' => 'Batal'],
                                ];
                                $config = $statusConfig[$production->status] ?? $statusConfig['planned'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $config['class'] }}">
                                <i class="fas {{ $config['icon'] }} mr-1.5"></i>
                                {{ $config['text'] }}
                            </span>
                            <a href="{{ route('production.show', $production->id) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-gray-200 bg-white text-gray-600 hover:bg-gray-50"
                               title="Detail">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
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
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchProduct');
    const filterStock = document.getElementById('filterStock');
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
});
</script>
@endpush
@endsection
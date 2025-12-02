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
<main class="flex-grow py-8 px-4">
    <div class="max-w-7xl mx-auto">
        
        @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg" role="alert">
            <div class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                <div class="flex-1">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg" role="alert">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-red-500 mt-1 mr-3"></i>
                <div class="flex-1">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Products Stock Overview -->
        <x-card-container>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-industry text-blue-600 mr-3"></i>
                            Stok Produk
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">Kelola produksi dan stok produk jadi</p>
                    </div>
                </div>
            </div>

            <!-- Filter & Search -->
            <div class="p-6 bg-gray-50 border-b border-gray-200">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" id="searchProduct" placeholder="Cari produk..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                    <select id="filterStock" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Stok</option>
                        <option value="low">Stok Menipis</option>
                        <option value="available">Stok Tersedia</option>
                        <option value="empty">Stok Kosong</option>
                    </select>
                </div>
            </div>

            <!-- Products Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-barcode mr-1 text-gray-400"></i>
                                Kode
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-box mr-1 text-gray-400"></i>
                                Produk
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-folder mr-1 text-gray-400"></i>
                                Kategori
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-cubes mr-1 text-gray-400"></i>
                                Stok
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-exclamation-triangle mr-1 text-gray-400"></i>
                                Min. Stok
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-clipboard-check mr-1 text-gray-400"></i>
                                Status
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-cog mr-1 text-gray-400"></i>
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="productTableBody">
                        @forelse($products as $product)
                        <tr class="hover:bg-gray-50 transition-colors product-row" 
                            data-name="{{ strtolower($product['name']) }}"
                            data-stock-status="{{ $product['stock'] == 0 ? 'empty' : ($product['is_low_stock'] ? 'low' : 'available') }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono font-semibold text-gray-900 bg-gray-100 px-2 py-1 rounded">
                                    {{ $product['code'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($product['image'])
                                    <img src="{{ Storage::url($product['image']) }}" alt="{{ $product['name'] }}" class="h-12 w-12 rounded-lg object-cover mr-3 border-2 border-gray-200 shadow-sm">
                                    @else
                                    <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center mr-3 shadow-sm">
                                        <i class="fas fa-industry text-white text-lg"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $product['name'] }}</div>
                                        <div class="text-xs text-gray-500 flex items-center mt-1">
                                            <i class="fas fa-ruler-combined mr-1"></i>
                                            {{ $product['unit'] }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product['category'])
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-tag mr-1"></i>
                                    {{ $product['category'] }}
                                </span>
                                @else
                                <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <span class="font-bold {{ $product['stock'] == 0 ? 'text-red-600' : ($product['is_low_stock'] ? 'text-yellow-600' : 'text-green-600') }}">
                                        {{ number_format($product['stock'], 2) }}
                                    </span>
                                    <span class="text-gray-500 ml-1">{{ $product['unit'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-600">
                                    {{ number_format($product['min_stock'], 2) }} {{ $product['unit'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product['stock'] == 0)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                    Kosong
                                </span>
                                @elseif($product['is_low_stock'])
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                    <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                                    Menipis
                                </span>
                                @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                    Tersedia
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($product['has_recipe'])
                                <a href="{{ route('production.create', ['product_id' => $product['id']]) }}" 
                                   class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-br from-blue-400 to-blue-700 text-white rounded-lg hover:from-blue-500 hover:to-blue-800 transition-all text-sm font-medium shadow-sm"  
                                   title="Produksi">
                                    <i class="fas fa-plus-circle mr-2"></i>
                                    Produksi
                                </a>
                                @else
                                <span class="text-xs text-gray-400 italic">Tidak ada resep</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-box-open text-5xl text-gray-300"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Produk</h3>
                                    <p class="text-sm text-gray-500 mb-6">Tambahkan produk terlebih dahulu untuk memulai produksi</p>
                                    <a href="{{ route('products-hpp.create') }}" class="inline-flex items-center px-5 py-2.5 bg-green-600 text-white rounded-lg font-semibold hover:bg-cuan-olive transition-colors">
                                        <i class="fas fa-plus-circle mr-2"></i>
                                        Tambah Produk
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card-container>

        <!-- Recent Productions -->
        @if($recentProductions->count() > 0)
        <x-card-container class="mt-6">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-history text-blue-600 mr-3"></i>
                    Riwayat Produksi Terbaru
                </h3>
            </div>

            <div class="p-6">
                <div class="space-y-4">
                    @foreach($recentProductions as $production)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex items-center space-x-4 flex-1">
                            <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-400 to-blue-700 flex items-center justify-center shadow-sm">
                                <i class="fas fa-industry text-white text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <h4 class="text-sm font-bold text-gray-900">{{ $production->product->name }}</h4>
                                    <span class="text-xs font-mono bg-gray-200 px-2 py-1 rounded">{{ $production->batch_number }}</span>
                                </div>
                                <div class="flex items-center gap-4 mt-1">
                                    <p class="text-xs text-gray-600">
                                        <i class="fas fa-cubes mr-1"></i>
                                        {{ number_format($production->actual_quantity, 2) }} {{ $production->product->unit->name ?? '' }}
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
                        <div class="flex items-center gap-3">
                            @php
                                $statusConfig = [
                                    'planned' => ['class' => 'bg-gray-100 text-gray-700', 'icon' => 'fa-clock', 'text' => 'Direncanakan'],
                                    'in_progress' => ['class' => 'bg-blue-100 text-blue-700', 'icon' => 'fa-spinner', 'text' => 'Sedang Proses'],
                                    'completed' => ['class' => 'bg-green-100 text-green-700', 'icon' => 'fa-check-circle', 'text' => 'Selesai'],
                                    'cancelled' => ['class' => 'bg-red-100 text-red-700', 'icon' => 'fa-times-circle', 'text' => 'Dibatalkan'],
                                ];
                                $config = $statusConfig[$production->status] ?? $statusConfig['planned'];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $config['class'] }}">
                                <i class="fas {{ $config['icon'] }} mr-1"></i>
                                {{ $config['text'] }}
                            </span>
                            <a href="{{ route('production.show', $production->id) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors"  
                               title="Detail">
                                <i class="fas fa-eye text-sm"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </x-card-container>
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
        const searchTerm = searchInput.value.toLowerCase();
        const stockFilter = filterStock.value;

        productRows.forEach(row => {
            const productName = row.dataset.name;
            const stockStatus = row.dataset.stockStatus;

            const matchesSearch = productName.includes(searchTerm);
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
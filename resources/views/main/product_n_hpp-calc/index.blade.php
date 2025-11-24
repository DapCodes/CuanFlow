@extends('layouts.app')

@section('title', 'Produk & Resep - CuanFlow')

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Produk & Resep</span>
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

        <x-card-container>
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-50 to-blue-50 p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-cube text-cuan-green mr-3"></i>
                            Daftar Produk & Resep
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">Kelola produk dan resep dengan perhitungan HPP otomatis</p>
                    </div>
                    <a href="{{ route('products-hpp.create') }}" class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-cuan-olive transition-all duration-200 shadow-md hover:shadow-lg">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Tambah Produk
                    </a>
                </div>
            </div>

            <!-- Filter & Search (Optional - bisa ditambahkan nanti) -->
            <div class="p-6 bg-gray-50 border-b border-gray-200">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" placeholder="Cari produk..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                    <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Kategori</option>
                        <option value="makanan">Makanan</option>
                        <option value="minuman">Minuman</option>
                    </select>
                    <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
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
                                <i class="fas fa-calculator mr-1 text-gray-400"></i>
                                HPP
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-tag mr-1 text-gray-400"></i>
                                Harga Jual
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-percent mr-1 text-gray-400"></i>
                                Margin
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-toggle-on mr-1 text-gray-400"></i>
                                Status
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-cog mr-1 text-gray-400"></i>
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($products as $product)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono font-semibold text-gray-900 bg-gray-100 px-2 py-1 rounded">
                                    {{ $product->code }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($product->image)
                                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="h-12 w-12 rounded-lg object-cover mr-3 border-2 border-gray-200 shadow-sm">
                                    @else
                                    <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center mr-3 shadow-sm">
                                        <i class="fas fa-utensils text-white text-lg"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $product->name }}</div>
                                        <div class="text-xs text-gray-500 flex items-center mt-1">
                                            <i class="fas fa-ruler-combined mr-1"></i>
                                            {{ $product->unit->name ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->category)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-tag mr-1"></i>
                                    {{ $product->category->name }}
                                </span>
                                @else
                                <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">
                                    Rp {{ number_format($product->hpp, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-gray-500">per {{ $product->unit->name ?? 'unit' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-green-600">
                                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                </div>
                                @if($product->reseller_price)
                                <div class="text-xs text-gray-500">
                                    Reseller: Rp {{ number_format($product->reseller_price, 0, ',', '.') }}
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $margin = $product->margin_percent;
                                    if ($margin >= 30) {
                                        $bgClass = 'bg-green-100';
                                        $textClass = 'text-green-700';
                                        $icon = 'fa-arrow-up';
                                    } elseif ($margin >= 15) {
                                        $bgClass = 'bg-yellow-100';
                                        $textClass = 'text-yellow-700';
                                        $icon = 'fa-minus';
                                    } else {
                                        $bgClass = 'bg-red-100';
                                        $textClass = 'text-red-700';
                                        $icon = 'fa-arrow-down';
                                    }
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold {{ $bgClass }} {{ $textClass }}">
                                    <i class="fas {{ $icon }} mr-1"></i>
                                    {{ number_format($margin, 1) }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->is_active)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                    Aktif
                                </span>
                                @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                    <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                                    Nonaktif
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('products-hpp.show', $product->id) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors" 
                                       title="Detail">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="{{ route('products-hpp.edit', $product->id) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors" 
                                       title="Edit">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <form action="{{ route('products-hpp.destroy', $product->id) }}" 
                                          method="POST" 
                                          class="inline-block" 
                                          onsubmit="return confirm('Yakin ingin menghapus produk {{ $product->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition-colors" 
                                                title="Hapus">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-box-open text-5xl text-gray-300"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Produk</h3>
                                    <p class="text-sm text-gray-500 mb-6">Mulai dengan menambahkan produk pertama Anda</p>
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

            <!-- Pagination -->
            @if($products->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-sm text-gray-700">
                        Menampilkan 
                        <span class="font-semibold">{{ $products->firstItem() }}</span>
                        sampai
                        <span class="font-semibold">{{ $products->lastItem() }}</span>
                        dari
                        <span class="font-semibold">{{ $products->total() }}</span>
                        produk
                    </div>
                    <div>
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
            @endif

            <!-- Summary Statistics (Optional - tampilan statistik ringkas) -->
            <div class="bg-gradient-to-r from-green-50 to-blue-50 p-6 border-t border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold">Total Produk</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $products->total() }}</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-cube text-blue-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold">Produk Aktif</p>
                                <p class="text-2xl font-bold text-green-600 mt-1">{{ $products->where('is_active', true)->count() }}</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold">Rata-rata HPP</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">
                                    Rp {{ number_format($products->avg('hpp'), 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calculator text-yellow-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold">Avg Margin</p>
                                <p class="text-2xl font-bold text-cuan-green mt-1">
                                    {{ number_format($products->avg('margin_percent'), 1) }}%
                                </p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-line text-cuan-green text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-card-container>

    </div>
</main>
@endsection
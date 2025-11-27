@extends('layouts.app')

@section('title', 'Detail Produk - ' . $product->name)

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('products-hpp.index') }}" class="text-gray-500 hover:text-gray-700">Produk & Resep</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">{{ $product->name }}</span>
</li>
@endsection

@push('styles')
<style>
    .stat-card {
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<main class="flex-grow py-8 px-4">
    <div class="max-w-7xl mx-auto">
        <!-- Product Info Card -->
        <x-card-container class="mb-6">
            <div class="bg-gradient-to-r from-green-50 to-blue-50 p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                            Detail Produk & Resep
                        </h2>
                        <p class="text-gray-600 text-sm">Informasi produk dan resep, serta statistik HPP per unit</p>
                    </div>
                    <div class="flex gap-3 justify-center">
                        <a href="{{ route('products-hpp.edit', $product->id) }}" class="inline-flex items-center px-6 py-3 bg-yellow-500 text-white rounded-lg font-semibold hover:bg-yellow-600 transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-edit mr-2"></i>
                            Ubah
                        </a>
                        <a href="{{ route('products-hpp.index') }}" class="inline-flex items-center px-6 py-3 bg-slate-600 text-white rounded-lg font-semibold hover:bg-slate-700 transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Product Image -->
                    <div class="lg:col-span-1">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" 
                                alt="{{ $product->name }}" 
                                class="w-full h-64 object-cover rounded-lg border-2 border-gray-200 shadow-md">
                        @else
                            <div class="w-full h-64 bg-gray-100 rounded-lg border-2 border-gray-200 flex items-center justify-center">
                                <i class="fas fa-image text-6xl text-gray-300"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Product Details -->
                    <div class="lg:col-span-2">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $product->name }}</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Kode Produk</label>
                                <p class="text-base font-semibold text-gray-900">{{ $product->code }}</p>
                            </div>
                            
                            @if($product->barcode)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Barcode</label>
                                <p class="text-base font-semibold text-gray-900">{{ $product->barcode }}</p>
                            </div>
                            @endif
                            
                            <div>
                                <label class="text-sm font-medium text-gray-500">Kategori</label>
                                <p class="text-base font-semibold text-gray-900">
                                    {{ $product->category->name ?? '-' }}
                                </p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-500">Satuan</label>
                                <p class="text-base font-semibold text-gray-900">{{ $product->unit->name }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-500">HPP Per Unit</label>
                                <p class="text-xl font-bold text-green-600">
                                    Rp {{ number_format($product->hpp, 0, ',', '.') }}
                                </p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-500">Harga Jual</label>
                                <p class="text-xl font-bold text-blue-600">
                                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                </p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-500">Margin</label>
                                <p class="text-lg font-bold {{ $product->margin_percent >= 30 ? 'text-green-600' : ($product->margin_percent >= 15 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($product->margin_percent, 2, ',', '.') }}%
                                </p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-500">Status</label>
                                <p>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $product->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        @if($product->description)
                        <div class="mt-4">
                            <label class="text-sm font-medium text-gray-500">Deskripsi</label>
                            <p class="text-gray-700 mt-1">{{ $product->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </x-card-container>

        <!-- Sales Target Card -->
        @php
            $salesTarget = $product->activeSalesTarget;
            $hasTarget = $salesTarget && $salesTarget->is_active;
        @endphp

        @if($hasTarget)
        <x-card-container class="mb-6">
            <div class="bg-gradient-to-r from-purple-50 to-blue-50 p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-bullseye text-purple-600 mr-2"></i>
                        Target Penjualan Aktif
                    </h3>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                        <i class="fas fa-check-circle mr-1"></i>
                        Aktif
                    </span>
                </div>
            </div>

            <div class="p-6">
                <!-- Target Overview -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="stat-card bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5 border border-blue-200">
                        <p class="text-sm text-blue-700 mb-1">Target Omzet/Bulan</p>
                        <p class="text-2xl font-bold text-blue-900">
                            Rp {{ number_format($salesTarget->monthly_target_revenue, 0, ',', '.') }}
                        </p>
                    </div>
                    
                    <div class="stat-card bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5 border border-green-200">
                        <p class="text-sm text-green-700 mb-1">Target Penjualan/Bulan</p>
                        <p class="text-2xl font-bold text-green-900">
                            {{ number_format($salesTarget->monthly_sales_target, 0, ',', '.') }} pcs
                        </p>
                    </div>
                    
                    <div class="stat-card bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5 border border-purple-200">
                        <p class="text-sm text-purple-700 mb-1">Target Penjualan/Hari</p>
                        <p class="text-2xl font-bold text-purple-900">
                            {{ number_format($salesTarget->daily_sales_target, 0, ',', '.') }} pcs
                        </p>
                    </div>
                    
                    <div class="stat-card bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-5 border border-yellow-200">
                        <p class="text-sm text-yellow-700 mb-1">Target Profit/Bulan</p>
                        <p class="text-2xl font-bold text-yellow-900">
                            Rp {{ number_format($salesTarget->monthly_profit_target, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <!-- Performance Tracking -->
                @php
                    // Get actual sales data for current month
                    $currentMonthStart = now()->startOfMonth();
                    $currentMonthEnd = now()->endOfMonth();
                    
                    $actualSales = \App\Models\Sale::byOutlet(auth()->user()->outlet_id)
                        ->completed()
                        ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
                        ->whereHas('items', function($q) use ($product) {
                            $q->where('product_id', $product->id);
                        })
                        ->with(['items' => function($q) use ($product) {
                            $q->where('product_id', $product->id);
                        }])
                        ->get();
                    
                    $actualQuantity = $actualSales->sum(fn($s) => $s->items->sum('quantity'));
                    $actualRevenue = $actualSales->sum(fn($s) => $s->items->sum(fn($i) => $i->quantity * $i->price));
                    
                    $quantityAchievement = $salesTarget->monthly_sales_target > 0 
                        ? ($actualQuantity / $salesTarget->monthly_sales_target) * 100 
                        : 0;
                    
                    $revenueAchievement = $salesTarget->monthly_target_revenue > 0 
                        ? ($actualRevenue / $salesTarget->monthly_target_revenue) * 100 
                        : 0;
                    
                    $daysInMonth = now()->daysInMonth;
                    $daysPassed = now()->day;
                    $expectedProgress = ($daysPassed / $daysInMonth) * 100;
                @endphp

                <div class="bg-white rounded-xl border-2 border-gray-200 p-6 mb-6">
                    <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                        Pencapaian Bulan Ini ({{ now()->format('F Y') }})
                    </h4>

                    <!-- Sales Achievement -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-semibold text-gray-700">Pencapaian Penjualan</span>
                            <span class="text-lg font-bold {{ $quantityAchievement >= 100 ? 'text-green-600' : 'text-blue-600' }}">
                                {{ number_format($quantityAchievement, 1, ',', '.') }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-blue-400 to-blue-600 transition-all duration-500 flex items-center justify-end px-2" 
                                style="width: {{ min($quantityAchievement, 100) }}%">
                                @if($quantityAchievement >= 10)
                                    <span class="text-xs text-white font-semibold">{{ number_format($actualQuantity, 0, ',', '.') }} / {{ number_format($salesTarget->monthly_sales_target, 2, ',', '.') }} pcs</span>
                                @endif
                            </div>
                        </div>
                        @if($quantityAchievement < 100)
                            <p class="text-xs text-gray-600 mt-1">
                                Sisa target: {{ number_format($salesTarget->monthly_sales_target - $actualQuantity, 0, ',', '.') }} pcs
                            </p>
                        @endif
                    </div>

                    <!-- Revenue Achievement -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-semibold text-gray-700">Pencapaian Omzet</span>
                            <span class="text-lg font-bold {{ $revenueAchievement >= 100 ? 'text-green-600' : 'text-purple-600' }}">
                                {{ number_format($revenueAchievement, 1, ',', '.') }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-purple-400 to-purple-600 transition-all duration-500" 
                                style="width: {{ min($revenueAchievement, 100) }}%">
                            </div>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">
                            Rp {{ number_format($actualRevenue, 0, ',', '.') }} / Rp {{ number_format($salesTarget->monthly_target_revenue, 0, ',', '.') }}
                        </p>
                    </div>

                    <!-- Expected Progress -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-semibold text-gray-700">Progress yang Diharapkan</span>
                            <span class="text-sm font-bold text-gray-800">{{ number_format($expectedProgress, 1, ',', '.') }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-full bg-gray-400 rounded-full" style="width: {{ $expectedProgress }}%"></div>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">
                            Hari ke-{{ $daysPassed }} dari {{ $daysInMonth }} hari
                        </p>
                    </div>

                    <!-- Performance Status -->
                    <div class="mt-6 p-4 rounded-lg {{ $quantityAchievement >= $expectedProgress ? 'bg-green-50 border-2 border-green-200' : 'bg-yellow-50 border-2 border-yellow-200' }}">
                        @if($quantityAchievement >= 100)
                            <div class="flex items-start">
                                <i class="fas fa-trophy text-2xl text-green-600 mr-3"></i>
                                <div>
                                    <p class="font-bold text-green-800">🎉 Target Tercapai!</p>
                                    <p class="text-sm text-green-700 mt-1">Selamat! Target penjualan bulan ini sudah tercapai.</p>
                                </div>
                            </div>
                        @elseif($quantityAchievement >= $expectedProgress)
                            <div class="flex items-start">
                                <i class="fas fa-check-circle text-2xl text-green-600 mr-3"></i>
                                <div>
                                    <p class="font-bold text-green-800">✓ On Track</p>
                                    <p class="text-sm text-green-700 mt-1">Performa penjualan sesuai dengan target yang diharapkan.</p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle text-2xl text-yellow-600 mr-3"></i>
                                <div>
                                    <p class="font-bold text-yellow-800">⚠ Below Target</p>
                                    <p class="text-sm text-yellow-700 mt-1">
                                        Perlu peningkatan penjualan. Kekurangan: {{ number_format($expectedProgress - $quantityAchievement, 1, ',', '.') }}%
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sales Pattern -->
                @if($salesTarget->sales_pattern)
                <div class="bg-white rounded-xl border-2 border-gray-200 p-6">
                    <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-calendar-week text-indigo-600 mr-2"></i>
                        Pola Penjualan Harian
                    </h4>
                    <canvas id="salesPatternChart" height="80"></canvas>
                </div>
                @endif
            </div>
        </x-card-container>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recipe Card -->
            @if($product->defaultRecipe)
            <x-card-container>
                <div class="bg-gradient-to-r from-green-50 to-blue-50 p-6 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-book-open text-cuan-green mr-2"></i>
                        Resep: {{ $product->defaultRecipe->name }}
                    </h3>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Output Quantity</label>
                            <p class="text-lg font-bold text-gray-900">
                                {{ number_format($product->defaultRecipe->output_quantity, 0, ',', '.') }} {{ $product->unit->name }}
                            </p>
                        </div>
                        
                        @if($product->defaultRecipe->estimated_time_minutes)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Estimasi Waktu</label>
                            <p class="text-lg font-bold text-gray-900">
                                {{ $product->defaultRecipe->estimated_time_minutes }} menit
                            </p>
                        </div>
                        @endif
                    </div>

                    <!-- Recipe Items -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">Bahan Baku:</h4>
                        <div class="space-y-3">
                            @foreach($product->defaultRecipe->items as $item)
                            <div class="flex justify-between items-start p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">{{ $item->rawMaterial->name }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ number_format($item->quantity, 2, ',', '.') }} {{ $item->rawMaterial->unit->name ?? '' }}
                                    </p>
                                    @if($item->notes)
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-sticky-note mr-1"></i>{{ $item->notes }}
                                    </p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-500">Biaya:</p>
                                    <p class="font-bold text-cuan-green">
                                        Rp {{ number_format($item->quantity * $item->rawMaterial->purchase_price, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    @if($product->defaultRecipe->instructions)
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">Instruksi:</h4>
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                            <p class="text-gray-700 whitespace-pre-line text-sm">{{ $product->defaultRecipe->instructions }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </x-card-container>
            @endif

            <!-- HPP Calculation Card -->
            @if($product->latestHppCalculation)
            <x-card-container>
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-6 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-calculator text-purple-600 mr-2"></i>
                        Perhitungan HPP Terbaru
                    </h3>
                </div>

                <div class="p-6">
                    @php
                        $hpp = $product->latestHppCalculation;
                    @endphp

                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-gray-200">
                            <span class="text-gray-700">Biaya Bahan Baku:</span>
                            <span class="font-bold text-gray-900">
                                Rp {{ number_format($hpp->raw_material_cost, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center py-3 border-b border-gray-200">
                            <span class="text-gray-700">Biaya Tambahan:</span>
                            <span class="font-bold text-gray-900">
                                Rp {{ number_format($hpp->additional_cost, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center py-3 border-b-2 border-gray-300">
                            <span class="font-bold text-gray-900">Total HPP:</span>
                            <span class="text-xl font-bold text-purple-600">
                                Rp {{ number_format($hpp->total_hpp, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center py-3">
                            <span class="text-gray-700">Output Quantity:</span>
                            <span class="font-bold text-gray-900">{{ number_format($hpp->output_quantity, 0, ',', '.') }} pcs</span>
                        </div>

                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-5 border-2 border-green-300">
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-gray-900">HPP Per Unit:</span>
                                <span class="text-2xl font-bold text-green-600">
                                    Rp {{ number_format($hpp->hpp_per_unit, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <div class="text-xs text-gray-500 mt-4">
                            <i class="fas fa-clock mr-1"></i>
                            Dihitung: {{ $hpp->created_at->format('d M Y H:i') }}
                            @if($hpp->calculatedBy)
                            <br>Oleh: {{ $hpp->calculatedBy->name }}
                            @endif
                        </div>
                    </div>
                </div>
            </x-card-container>
            @endif
        </div>

        <!-- Pricing & Margin Analysis -->
        <x-card-container class="mt-6">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-chart-pie text-blue-600 mr-2"></i>
                    Analisis Harga & Margin
                </h3>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="stat-card bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5 border border-green-200">
                        <p class="text-sm text-green-700 mb-1">HPP Per Unit</p>
                        <p class="text-2xl font-bold text-green-900">
                            Rp {{ number_format($product->hpp, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="stat-card bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5 border border-blue-200">
                        <p class="text-sm text-blue-700 mb-1">Harga Jual</p>
                        <p class="text-2xl font-bold text-blue-900">
                            Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="stat-card bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5 border border-purple-200">
                        <p class="text-sm text-purple-700 mb-1">Profit Per Unit</p>
                        <p class="text-2xl font-bold text-purple-900">
                            Rp {{ number_format($product->selling_price - $product->hpp, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="stat-card bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-5 border border-yellow-200">
                        <p class="text-sm text-yellow-700 mb-1">Margin</p>
                        <p class="text-2xl font-bold text-yellow-900">
                            {{ number_format($product->margin_percent, 2, ',', '.') }}%
                        </p>
                    </div>
                </div>

                @if($product->reseller_price || $product->promo_price)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($product->reseller_price)
                    <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-5 border border-orange-200">
                        <p class="text-sm text-orange-700 mb-1">Harga Reseller</p>
                        <p class="text-xl font-bold text-orange-900">
                            Rp {{ number_format($product->reseller_price, 0, ',', '.') }}
                        </p>
                        @php
                            $resellerMargin = $product->hpp > 0 ? (($product->reseller_price - $product->hpp) / $product->hpp) * 100 : 0;
                        @endphp
                        <p class="text-sm text-orange-700 mt-1">Margin: {{ number_format($resellerMargin, 2, ',', '.') }}%</p>
                    </div>
                    @endif

                    @if($product->promo_price)
                    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-5 border border-red-200">
                        <p class="text-sm text-red-700 mb-1">Harga Promo</p>
                        <p class="text-xl font-bold text-red-900">
                            Rp {{ number_format($product->promo_price, 0, ',', '.') }}
                        </p>
                        @php
                            $promoMargin = $product->hpp > 0 ? (($product->promo_price - $product->hpp) / $product->hpp) * 100 : 0;
                        @endphp
                        <p class="text-sm text-red-700 mt-1">Margin: {{ number_format($promoMargin, 2, ',', '.') }}%</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </x-card-container>

        <!-- Additional Info -->
        <x-card-container class="mt-6">
            <div class="bg-gradient-to-r from-gray-50 to-slate-50 p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-info-circle text-gray-600 mr-2"></i>
                    Informasi Tambahan
                </h3>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-500 flex items-center mb-2">
                            <i class="fas fa-boxes text-gray-400 mr-2"></i>
                            Minimum Stok
                        </label>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($product->min_stock, 0, ',', '.') }} {{ $product->unit->name }}</p>
                        <p class="text-xs text-gray-500 mt-1">Alert jika stok di bawah nilai ini</p>
                    </div>

                    @if($product->shelf_life_days)
                    <div>
                        <label class="text-sm font-medium text-gray-500 flex items-center mb-2">
                            <i class="fas fa-calendar-day text-gray-400 mr-2"></i>
                            Masa Simpan
                        </label>
                        <p class="text-lg font-bold text-gray-900">{{ $product->shelf_life_days }} Hari</p>
                        <p class="text-xs text-gray-500 mt-1">Maksimal penyimpanan produk</p>
                    </div>
                    @endif

                    <div>
                        <label class="text-sm font-medium text-gray-500 flex items-center mb-2">
                            <i class="fas fa-clock text-gray-400 mr-2"></i>
                            Terakhir Diperbarui
                        </label>
                        <p class="text-lg font-bold text-gray-900">{{ $product->updated_at->format('d M Y') }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $product->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </x-card-container>

        <!-- HPP Calculation History -->
        @if($product->hppCalculations->count() > 1)
        <x-card-container class="mt-6">
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-history text-indigo-600 mr-2"></i>
                    Riwayat Perhitungan HPP
                </h3>
            </div>

            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Biaya Bahan Baku</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Biaya Tambahan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total HPP</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">HPP/Unit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dihitung Oleh</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($product->hppCalculations->take(5) as $calc)
                            <tr class="{{ $loop->first ? 'bg-green-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $calc->created_at->format('d M Y H:i') }}
                                    @if($loop->first)
                                        <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-semibold">Terbaru</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Rp {{ number_format($calc->raw_material_cost, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Rp {{ number_format($calc->additional_cost, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    Rp {{ number_format($calc->total_hpp, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">
                                    Rp {{ number_format($calc->hpp_per_unit, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $calc->calculatedBy->name ?? '-' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($product->hppCalculations->count() > 5)
                <div class="mt-4 text-center">
                    <p class="text-sm text-gray-500">Menampilkan 5 perhitungan terakhir dari {{ $product->hppCalculations->count() }} total perhitungan</p>
                </div>
                @endif
            </div>
        </x-card-container>
        @endif

    </div>
</main>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($hasTarget && $salesTarget->sales_pattern)
    // Render Sales Pattern Chart
    const ctx = document.getElementById('salesPatternChart');
    if (ctx) {
        const salesPattern = @json($salesTarget->sales_pattern);
        const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        const indonesianDays = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: indonesianDays,
                datasets: [{
                    label: 'Penjualan (pcs)',
                    data: days.map(day => salesPattern[day] || 0),
                    backgroundColor: 'rgba(99, 102, 241, 0.7)',
                    borderColor: 'rgb(99, 102, 241)',
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' pcs terjual';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
    @endif
});
</script>
@endpush

@endsection
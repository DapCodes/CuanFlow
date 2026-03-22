@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Detail Produk & Resep - ' . $product->name)

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('products-hpp.index') }}" class="text-gray-400 hover:text-cuan-green transition-colors font-medium tracking-tight">Produk & Resep</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight truncate max-w-[140px] md:max-w-xs">{{ $product->name }}</span>
</li>
@endsection

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px -8px rgba(101, 140, 88, 0.15);
    }
</style>
@endpush

@section('content')
<main class="flex-grow py-10 px-4 md:px-8">
    <div class="max-w-7xl mx-auto space-y-8">

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Detail Produk & Resep
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Informasi lengkap mengenai resep, HPP, harga jual, dan performa penjualan produk <span class="text-gray-900 font-bold">{{ $product->name }}</span>.
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                @can('edit produk')
                <a href="{{ route('products-hpp.edit', $product->id) }}" class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-5 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                    <i class="fas fa-edit text-xs"></i> <span>Edit Produk</span>
                </a>
                @endcan
                <a href="{{ route('products-hpp.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-white border-2 border-gray-100 px-5 py-3 text-sm font-black text-gray-600 hover:bg-gray-50 transition-all active:scale-95">
                    <i class="fas fa-arrow-left text-xs"></i> <span>Kembali</span>
                </a>
            </div>
        </section>

        <x-card-container>
            <div class="p-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                    {{-- FOTO PRODUK --}}
                    <div class="lg:col-span-4">
                        <div class="group relative">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}"
                                     alt="{{ $product->name }}"
                                     class="w-full aspect-square object-cover rounded-[2rem] shadow-2xl shadow-gray-200 group-hover:scale-[1.02] transition-transform duration-500">
                            @else
                                <div class="w-full aspect-square bg-gray-50 rounded-[2rem] border-2 border-dashed border-gray-200 flex flex-col items-center justify-center text-center p-8 transition-colors group-hover:bg-gray-100/50">
                                    <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-4">
                                        <i class="fas fa-image text-3xl text-gray-300"></i>
                                    </div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">No Image</p>
                                    <p class="text-[10px] text-gray-400 mt-2 font-medium">Tambah foto di halaman edit untuk visual yang lebih baik.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- DATA PRODUK --}}
                    <div class="lg:col-span-8 space-y-8">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-cuan-green mb-2 block">Informasi Produk</span>
                            <h2 class="text-2xl font-black text-gray-900 tracking-tight">Detail Dasar & <span class="text-gray-400">Klasifikasi</span></h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            <div class="space-y-1">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Kode Produk</p>
                                <p class="text-sm font-black text-gray-900 uppercase tracking-widest">{{ $product->code }}</p>
                            </div>

                            @if($product->barcode)
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Barcode</p>
                                    <p class="text-sm font-black text-gray-900 uppercase tracking-widest">{{ $product->barcode }}</p>
                                </div>
                            @endif

                            <div class="space-y-1">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Kategori</p>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-black text-gray-900 uppercase tracking-widest">{{ $product->category->name ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Satuan</p>
                                <p class="text-sm font-black text-gray-900 uppercase tracking-widest">{{ $product->unit->name }}</p>
                            </div>

                            <div class="space-y-1">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">HPP per Unit</p>
                                <p class="text-xl font-black text-cuan-green tracking-tight">
                                    Rp {{ number_format($product->hpp, 0, ',', '.') }}
                                </p>
                            </div>

                            <div class="space-y-1">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Harga Jual</p>
                                <p class="text-xl font-black text-gray-900 tracking-tight">
                                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                </p>
                            </div>

                            <div class="space-y-1">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Margin Keuntungan</p>
                                <div class="flex items-center gap-3">
                                    <span class="text-lg font-black tracking-tight {{ $product->margin_percent >= 30 ? 'text-cuan-green' : ($product->margin_percent >= 15 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ number_format($product->margin_percent, 2, ',', '.') }}%
                                    </span>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-2 py-0.5 bg-gray-50 rounded-lg">Target: >30%</span>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Status & Tipe</p>
                                <div class="flex flex-wrap gap-2">
                                    <span class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border-2 {{ $product->is_active ? 'bg-cuan-green/10 text-cuan-green border-cuan-green/10' : 'bg-gray-50 text-gray-400 border-gray-100' }}">
                                        {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                    @if($product->is_stock)
                                        <span class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border-2 bg-blue-50 text-blue-600 border-blue-50">
                                            Inventory
                                        </span>
                                    @else
                                        <span class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest border-2 bg-amber-50 text-amber-600 border-amber-50">
                                            Hand-made
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($product->description)
                            <div class="pt-4 border-t border-gray-50">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Deskripsi</p>
                                <p class="text-sm text-gray-600 font-medium leading-relaxed italic">
                                    "{{ $product->description }}"
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </x-card-container>

        @can('lihat analitik produk')
        @if($hasTarget)
            <x-card-container>
                <div class="p-8 border-b border-gray-50 bg-gradient-to-br from-indigo-50/30 to-blue-50/30">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-500 mb-2 block">Performance Tracking</span>
                            <h3 class="text-xl font-black text-gray-900 tracking-tight flex items-center gap-3">
                                <i class="fas fa-bullseye text-indigo-500 text-lg"></i>
                                Target Penjualan <span class="text-gray-400">Aktif</span>
                            </h3>
                        </div>
                        <span class="px-3 py-1.5 bg-cuan-green/10 text-cuan-green rounded-xl text-[10px] font-black uppercase tracking-widest">
                            Running
                        </span>
                    </div>
                </div>

                <div class="p-8">
                    {{-- RINGKASAN TARGET --}}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-10">
                        <div class="stat-card bg-white rounded-2xl p-6 border-2 border-gray-50 group">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 group-hover:text-indigo-500 transition-colors">Target Omzet</p>
                            <p class="text-xl font-black text-gray-900 tracking-tight">
                                Rp {{ number_format($salesTarget->monthly_target_revenue, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="stat-card bg-white rounded-2xl p-6 border-2 border-gray-50 group">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 group-hover:text-cuan-green transition-colors">Target Unit</p>
                            <p class="text-xl font-black text-gray-900 tracking-tight">
                                {{ number_format($salesTarget->monthly_sales_target, 0, ',', '.') }} <span class="text-gray-400 text-sm italic">{{ $product->unit->name }}</span>
                            </p>
                        </div>

                        <div class="stat-card bg-white rounded-2xl p-6 border-2 border-gray-50 group">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 group-hover:text-amber-500 transition-colors">Target/Hari</p>
                            <p class="text-xl font-black text-gray-900 tracking-tight">
                                {{ number_format($salesTarget->daily_sales_target, 0, ',', '.') }} <span class="text-gray-400 text-sm italic">pcs</span>
                            </p>
                        </div>

                        <div class="stat-card bg-white rounded-2xl p-6 border-2 border-gray-50 group">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 group-hover:text-pink-500 transition-colors">Target Profit</p>
                            <p class="text-xl font-black text-gray-900 tracking-tight">
                                Rp {{ number_format($salesTarget->monthly_profit_target, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    {{-- HITUNG PENCAPAIAN BULAN INI --}}
                    @php
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
                        $actualRevenue = $actualSales->sum(fn($s) => $s->items->sum(fn($i) => $i->quantity * $i->unit_price));

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

                    <div class="bg-gray-50/50 rounded-[2rem] p-8 border border-gray-100">
                        <h4 class="text-lg font-black text-gray-900 mb-8 flex items-center gap-3">
                            <i class="fas fa-chart-line text-cuan-green"></i>
                            Pencapaian <span class="text-gray-400">{{ now()->format('F Y') }}</span>
                        </h4>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                            {{-- Pencapaian Penjualan --}}
                            <div class="space-y-4">
                                <div class="flex justify-between items-end">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Penjualan (pcs)</span>
                                    <span class="text-2xl font-black {{ $quantityAchievement >= 100 ? 'text-cuan-green' : 'text-gray-900' }}">
                                        {{ number_format($quantityAchievement, 1, ',', '.') }}%
                                    </span>
                                </div>
                                <div class="w-full bg-white rounded-full h-8 p-1 border border-gray-100 overflow-hidden shadow-sm">
                                    <div class="h-full bg-gradient-to-r from-indigo-500 to-cuan-green rounded-full transition-all duration-1000 flex items-center justify-end px-3"
                                         style="width: {{ min($quantityAchievement, 100) }}%">
                                        @if($quantityAchievement >= 15)
                                            <span class="text-[10px] text-white font-black uppercase tracking-tight">
                                                {{ number_format($actualQuantity, 0, ',', '.') }} pcs
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @if($quantityAchievement < 100)
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">
                                        Sisa: {{ number_format(max($salesTarget->monthly_sales_target - $actualQuantity, 0), 0, ',', '.') }} pcs
                                    </p>
                                @endif
                            </div>

                            {{-- Pencapaian Omzet --}}
                            <div class="space-y-4">
                                <div class="flex justify-between items-end">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Omzet</span>
                                    <span class="text-2xl font-black {{ $revenueAchievement >= 100 ? 'text-cuan-green' : 'text-gray-900' }}">
                                        {{ number_format($revenueAchievement, 1, ',', '.') }}%
                                    </span>
                                </div>
                                <div class="w-full bg-white rounded-full h-8 p-1 border border-gray-100 overflow-hidden shadow-sm">
                                    <div class="h-full bg-gradient-to-r from-gray-900 to-indigo-600 rounded-full transition-all duration-1000"
                                         style="width: {{ min($revenueAchievement, 100) }}%"></div>
                                </div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">
                                    Rp {{ number_format($actualRevenue, 0, ',', '.') }} / Rp {{ number_format($salesTarget->monthly_target_revenue, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        {{-- Performa Status --}}
                        <div class="mt-10 p-6 rounded-3xl {{ $quantityAchievement >= $expectedProgress ? 'bg-cuan-green/5 border-2 border-cuan-green/10' : 'bg-red-50 border-2 border-red-100' }}">
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 {{ $quantityAchievement >= $expectedProgress ? 'bg-cuan-green text-white shadow-lg shadow-cuan-green/20' : 'bg-red-500 text-white shadow-lg shadow-red-500/20' }}">
                                    <i class="fas {{ $quantityAchievement >= $expectedProgress ? 'fa-rocket' : 'fa-exclamation-triangle' }} text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-gray-900 uppercase tracking-widest mb-1">
                                        {{ $quantityAchievement >= $expectedProgress ? 'ON TRACK' : 'NEEDS ATTENTION' }}
                                    </p>
                                    <p class="text-sm text-gray-500 font-medium">
                                        Progress hari: <span class="font-bold text-gray-900">{{ number_format($expectedProgress, 0) }}%</span>. 
                                        {{ $quantityAchievement >= $expectedProgress ? 'Performa penjualan masih sesuai target harian.' : 'Penjualan perlu ditingkatkan untuk mengejar target bulanan.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pola Penjualan Harian --}}
                    @if($salesTarget->sales_pattern)
                        <div class="mt-10">
                            <div class="mb-4">
                                <h4 class="text-lg font-black text-gray-900 tracking-tight">Pola <span class="text-gray-400">Penjualan Harian</span></h4>
                                <p class="text-xs text-gray-400 font-medium tracking-tight">Distribusi rata-rata penjualan berdasarkan hari.</p>
                            </div>
                            <div class="bg-white border-2 border-gray-50 rounded-[2rem] p-8">
                                <canvas id="salesPatternChart" height="80"></canvas>
                            </div>
                        </div>
                    @endif
                </div>
            </x-card-container>
        @endif
        @endcan

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- RESEP PRODUK --}}
            @if($product->defaultRecipe)
                <x-card-container>
                    <div class="p-8 border-b border-gray-50">
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-cuan-green mb-2 block">Production Recipe</span>
                        <h3 class="text-xl font-black text-gray-900 tracking-tight">
                            {{ $product->defaultRecipe->name }}
                        </h3>
                    </div>

                    <div class="p-8 space-y-8">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Hasil Produksi</p>
                                <p class="text-xl font-black text-gray-900 tracking-tight">
                                    {{ number_format($product->defaultRecipe->output_quantity, 0, ',', '.') }}
                                    <span class="text-gray-400 text-xs italic">{{ $product->unit->name }}</span>
                                </p>
                            </div>

                            @if($product->defaultRecipe->estimated_time_minutes)
                                <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Estimasi Waktu</p>
                                    <p class="text-xl font-black text-gray-900 tracking-tight">
                                        {{ $product->defaultRecipe->estimated_time_minutes }} <span class="text-gray-400 text-xs italic">Menit</span>
                                    </p>
                                </div>
                            @endif
                        </div>

                        {{-- BAHAN BAKU --}}
                        <div class="space-y-4">
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Bahan Baku Utama</h4>
                            <div class="space-y-3">
                                @foreach($product->defaultRecipe->items as $item)
                                    <div class="flex items-center justify-between p-4 bg-white border-2 border-gray-50 rounded-2xl group hover:border-cuan-green/20 transition-all">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-cuan-green/10 group-hover:text-cuan-green transition-colors">
                                                <i class="fas fa-flask text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="font-black text-gray-900 tracking-tight text-sm uppercase">{{ $item->rawMaterial->name }}</p>
                                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                                                    {{ number_format($item->quantity, 2, ',', '.') }} {{ $item->rawMaterial->unit->name ?? '' }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-black text-cuan-green tracking-tight">
                                                Rp {{ number_format($item->quantity * $item->rawMaterial->purchase_price, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- INSTRUKSI --}}
                        @if($product->defaultRecipe->instructions)
                            <div class="space-y-3">
                                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Langkah Pembuatan</h4>
                                <div class="bg-gray-50 rounded-2xl p-6 border-2 border-gray-100">
                                    <p class="text-sm text-gray-600 font-medium leading-relaxed whitespace-pre-line">
                                        {{ $product->defaultRecipe->instructions }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </x-card-container>
            @endif

            {{-- PERHITUNGAN HPP TERBARU --}}
            @if($product->latestHppCalculation)
                <x-card-container>
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-6 border-b border-gray-200 rounded-t-xl">
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 flex items-center gap-2">
                            Perhitungan HPP Terbaru
                        </h3>
                        <p class="text-xs md:text-sm text-gray-600 mt-1">
                            Ringkasan biaya produksi terakhir yang sudah disimpan untuk produk ini.
                        </p>
                    </div>

                    <div class="p-6">
                        @php
                            $hpp = $product->latestHppCalculation;
                        @endphp

                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-3 border-b border-gray-200">
                                <span class="text-sm text-gray-700">Biaya bahan baku (per resep):</span>
                                <span class="font-bold text-gray-900">
                                    Rp {{ number_format($hpp->raw_material_cost, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b border-gray-200">
                                <span class="text-sm text-gray-700">Biaya tambahan (per resep):</span>
                                <span class="font-bold text-gray-900">
                                    Rp {{ number_format($hpp->additional_cost, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b-2 border-gray-300">
                                <span class="font-bold text-gray-900">Total HPP (per resep):</span>
                                <span class="text-xl font-bold text-purple-600">
                                    Rp {{ number_format($hpp->total_hpp, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-3">
                                <span class="text-sm text-gray-700">Output produk per resep:</span>
                                <span class="font-bold text-gray-900">
                                    {{ number_format($hpp->output_quantity, 0, ',', '.') }} pcs
                                </span>
                            </div>

                            <div class="p-6 bg-cuan-green/10 rounded-[2rem] border-2 border-cuan-green/10">
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] font-black text-cuan-green uppercase tracking-widest">Final HPP / Unit</span>
                                    <span class="text-3xl font-black text-cuan-green tracking-tight">
                                        Rp {{ number_format($hpp->hpp_per_unit, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                            <i class="fas fa-history"></i>
                            Updated: {{ $hpp->created_at->format('d M Y') }} • By {{ $hpp->calculatedBy->name ?? 'System' }}
                        </div>
                    </div>
                </x-card-container>
            @endif
        </div>

        @can('lihat analitik produk')
        {{-- ANALISIS HARGA & MARGIN --}}
        <x-card-container>
            <div class="p-8 border-b border-gray-50">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-500 mb-2 block">Price Analysis</span>
                <h3 class="text-xl font-black text-gray-900 tracking-tight">Profitability <span class="text-gray-400">& Margins</span></h3>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="stat-card p-6 rounded-[2rem] bg-gray-50/50 border border-gray-100 flex flex-col justify-between">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">HPP Dasar</p>
                        <p class="text-xl font-black text-gray-900 tracking-tight">Rp {{ number_format($product->hpp, 0, ',', '.') }}</p>
                    </div>

                    <div class="stat-card p-6 rounded-[2rem] bg-gray-50/50 border border-gray-100 flex flex-col justify-between">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Harga Jual</p>
                        <p class="text-xl font-black text-gray-900 tracking-tight">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                    </div>

                    <div class="stat-card p-6 rounded-[2rem] bg-cuan-green/5 border border-cuan-green/10 flex flex-col justify-between">
                        <p class="text-[10px] font-black text-cuan-green uppercase tracking-widest mb-4">Gross Profit</p>
                        <p class="text-xl font-black text-cuan-green tracking-tight">Rp {{ number_format($product->selling_price - $product->hpp, 0, ',', '.') }}</p>
                    </div>

                    <div class="stat-card p-6 rounded-[2rem] bg-gray-900 flex flex-col justify-between shadow-xl shadow-gray-200">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Margin %</p>
                        <p class="text-xl font-black text-white tracking-tight">{{ number_format($product->margin_percent, 2, ',', '.') }}%</p>
                    </div>
                </div>

                @if($product->reseller_price || $product->promo_price)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($product->reseller_price)
                            <div class="p-6 rounded-2xl bg-amber-50/50 border border-amber-100 flex items-center justify-between">
                                <div>
                                    <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest">Reseller Price</p>
                                    <p class="text-lg font-black text-gray-900 tracking-tight">Rp {{ number_format($product->reseller_price, 0, ',', '.') }}</p>
                                </div>
                                @php $resellerMargin = $product->hpp > 0 ? (($product->reseller_price - $product->hpp) / $product->hpp) * 100 : 0; @endphp
                                <span class="px-3 py-1 bg-white rounded-lg text-[10px] font-black text-amber-600">{{ number_format($resellerMargin, 1) }}% Margin</span>
                            </div>
                        @endif

                        @if($product->promo_price)
                            <div class="p-6 rounded-2xl bg-red-50/50 border border-red-100 flex items-center justify-between">
                                <div>
                                    <p class="text-[10px] font-black text-red-600 uppercase tracking-widest">Promo Price</p>
                                    <p class="text-lg font-black text-gray-900 tracking-tight">Rp {{ number_format($product->promo_price, 0, ',', '.') }}</p>
                                </div>
                                @php $promoMargin = $product->hpp > 0 ? (($product->promo_price - $product->hpp) / $product->hpp) * 100 : 0; @endphp
                                <span class="px-3 py-1 bg-white rounded-lg text-[10px] font-black text-red-600">{{ number_format($promoMargin, 1) }}% Margin</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </x-card-container>
        @endcan

        {{-- INFORMASI TAMBAHAN --}}
        <x-card-container>
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <div class="group">
                        <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center mb-4 group-hover:bg-cuan-green/10 transition-colors">
                            <i class="fas fa-boxes text-gray-400 group-hover:text-cuan-green"></i>
                        </div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Stock Threshold</p>
                        <p class="text-lg font-black text-gray-900 tracking-tight">
                            {{ number_format($product->min_stock, 0, ',', '.') }} <span class="text-gray-400 text-xs italic">{{ $product->unit->name }}</span>
                        </p>
                    </div>

                    <div class="group">
                        <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center mb-4 group-hover:bg-blue-50 transition-colors">
                            <i class="fas fa-calendar-day text-gray-400 group-hover:text-blue-600"></i>
                        </div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Shelf Life</p>
                        <p class="text-lg font-black text-gray-900 tracking-tight">
                            {{ $product->shelf_life_days ?? '-' }} <span class="text-gray-400 text-xs italic">Hari</span>
                        </p>
                    </div>

                    <div class="group">
                        <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center mb-4 group-hover:bg-purple-50 transition-colors">
                            <i class="fas fa-fingerprint text-gray-400 group-hover:text-purple-600"></i>
                        </div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Last Modification</p>
                        <p class="text-lg font-black text-gray-900 tracking-tight">
                            {{ $product->updated_at->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </x-card-container>

        {{-- RIWAYAT PERHITUNGAN HPP --}}
        @if($product->hppCalculations->count() > 1)
            <x-card-container>
                <div class="p-8 border-b border-gray-50 bg-gray-50/10">
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-2 block">Version Control</span>
                    <h3 class="text-xl font-black text-gray-900 tracking-tight">Riwayat <span class="text-gray-400">Kalkulasi</span></h3>
                </div>

                <div class="p-0 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-8 py-4 text-left text-gray-400 text-[10px] font-black uppercase tracking-widest">Waktu Update</th>
                                    <th class="px-8 py-4 text-left text-gray-400 text-[10px] font-black uppercase tracking-widest">Material Cost</th>
                                    <th class="px-8 py-4 text-left text-gray-400 text-[10px] font-black uppercase tracking-widest">Operating Cost</th>
                                    <th class="px-8 py-4 text-left text-gray-400 text-[10px] font-black uppercase tracking-widest">Total HPP</th>
                                    <th class="px-8 py-4 text-left text-gray-400 text-[10px] font-black uppercase tracking-widest">Final/Unit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($product->hppCalculations->take(5) as $calc)
                                    <tr class="hover:bg-gray-50/30 transition-colors group">
                                        <td class="px-8 py-5">
                                            <div class="flex items-center gap-3">
                                                <span class="text-sm font-black text-gray-900 tracking-tight">
                                                    {{ $calc->created_at->format('d M Y') }}
                                                </span>
                                                @if($loop->first)
                                                    <span class="px-2 py-0.5 bg-cuan-green/10 text-cuan-green text-[9px] font-black uppercase tracking-[0.1em] rounded-md">Live</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 text-sm font-medium text-gray-500">
                                            Rp {{ number_format($calc->raw_material_cost, 0, ',', '.') }}
                                        </td>
                                        <td class="px-8 py-5 text-sm font-medium text-gray-500">
                                            Rp {{ number_format($calc->additional_cost, 0, ',', '.') }}
                                        </td>
                                        <td class="px-8 py-5 text-sm font-bold text-gray-900">
                                            Rp {{ number_format($calc->total_hpp, 0, ',', '.') }}
                                        </td>
                                        <td class="px-8 py-5">
                                            <span class="text-sm font-black text-cuan-green tracking-tight">
                                                Rp {{ number_format($calc->hpp_per_unit, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
    const ctx = document.getElementById('salesPatternChart');
    if (ctx) {
        const salesPattern = @json($salesTarget->sales_pattern);
        const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        const indonesianDays = ['SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB', 'MIN'];

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: indonesianDays,
                datasets: [{
                    label: 'Pcs',
                    data: days.map(day => salesPattern[day] || 0),
                    backgroundColor: 'rgba(101, 140, 88, 0.15)',
                    borderColor: '#658C58',
                    borderWidth: 2,
                    borderRadius: 12,
                    hoverBackgroundColor: '#658C58'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#111827',
                        padding: 12,
                        titleFont: { family: 'Inter', size: 10, weight: '900' },
                        bodyFont: { family: 'Inter', size: 12, weight: 'bold' },
                        cornerRadius: 12,
                        displayColors: false,
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
                        grid: { display: true, color: '#f3f4f6', drawBorder: false },
                        ticks: { font: { size: 10, weight: '600' }, color: '#9ca3af' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: '900' }, color: '#9ca3af' }
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

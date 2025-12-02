@extends('layouts.app')

@section('title', 'Detail Produksi #' . $production->batch_number)

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('production.index') }}" class="text-gray-500 hover:text-gray-700">Produksi</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">#{{ $production->batch_number }}</span>
</li>
@endsection

@section('content')

<main class="flex-grow py-8 px-4">
    <div class="max-w-7xl mx-auto">

        @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg" role="alert">
            <div class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg" role="alert">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-red-500 mt-1 mr-3"></i>
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        @if(session('insufficient_materials'))
        <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg" role="alert">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-yellow-500 mt-1 mr-3"></i>
                <div class="flex-1">
                    <h3 class="font-semibold text-yellow-800 mb-2">Stok Bahan Baku Tidak Mencukupi!</h3>
                    <ul class="list-disc list-inside text-sm text-yellow-700 space-y-1">
                        @foreach(session('insufficient_materials') as $material)
                        <li>
                            <strong>{{ $material['name'] }}</strong>: 
                            Dibutuhkan {{ number_format($material['required'], 2) }}, 
                            Tersedia {{ number_format($material['available'], 2) }}, 
                            Kurang {{ number_format($material['shortage'], 2) }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Production Status Card -->
                <x-card-container>
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                                    Produksi #{{ $production->batch_number }}
                                </h2>
                                <p class="text-sm text-gray-600 mt-1">Detail informasi produksi</p>
                            </div>
                            <div class="flex flex-col sm:flex-row flex-wrap gap-2 md:gap-3">
                                @if($production->status === 'planned')
                                    <form action="{{ route('production.start', $production->id) }}" method="POST" class="w-full sm:w-auto" onsubmit="return confirm('Apakah Anda yakin ingin memulai produksi ini? Stok bahan baku akan langsung dikurangi.')">
                                        @csrf
                                        <button type="submit" 
                                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-gradient-to-br from-blue-400 to-blue-700 text-white rounded-lg hover:from-blue-500 hover:to-blue-800 transition-all font-medium shadow-md hover:shadow-lg text-sm md:text-base">
                                            <i class="fas fa-play mr-2"></i>
                                            Mulai Produksi
                                        </button>
                                    </form>
                                    <button onclick="openCancelModal()" 
                                        class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium shadow-md hover:shadow-lg text-sm md:text-base">
                                        <i class="fas fa-times-circle mr-2"></i>
                                        Batalkan
                                    </button>
                                @endif

                                @if($production->status === 'in_progress')
                                    <button onclick="openCompleteModal()" 
                                        class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium shadow-md hover:shadow-lg text-sm md:text-base">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Selesaikan
                                    </button>
                                    <button onclick="openCancelModal()" 
                                        class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium shadow-md hover:shadow-lg text-sm md:text-base">
                                        <i class="fas fa-times-circle mr-2"></i>
                                        Batalkan
                                    </button>
                                @endif

                                <a href="{{ route('production.index') }}" 
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium shadow-md hover:shadow-lg text-sm md:text-base">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Kembali
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-xs text-gray-600 mb-1">Status</p>
                                @php
                                    $statusConfig = [
                                        'planned' => ['class' => 'bg-gray-100 text-gray-700 border-gray-300', 'icon' => 'fa-clock', 'text' => 'Direncanakan'],
                                        'in_progress' => ['class' => 'bg-blue-100 text-blue-700 border-blue-300', 'icon' => 'fa-spinner fa-spin', 'text' => 'Sedang Proses'],
                                        'completed' => ['class' => 'bg-green-100 text-green-700 border-green-300', 'icon' => 'fa-check-circle', 'text' => 'Selesai'],
                                        'cancelled' => ['class' => 'bg-red-100 text-red-700 border-red-300', 'icon' => 'fa-times-circle', 'text' => 'Dibatalkan'],
                                    ];
                                    $config = $statusConfig[$production->status] ?? $statusConfig['planned'];
                                @endphp
                                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold {{ $config['class'] }} border-2">
                                    <i class="fas {{ $config['icon'] }} mr-2"></i>
                                    {{ $config['text'] }}
                                </span>
                            </div>

                            <div>
                                <p class="text-xs text-gray-600 mb-1">Batch Number</p>
                                <p class="text-lg font-bold font-mono text-gray-900 bg-gray-100 px-3 py-2 rounded inline-block">
                                    {{ $production->batch_number }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-600 mb-1">Dibuat Oleh</p>
                                <p class="font-semibold text-gray-900">
                                    <i class="fas fa-user text-gray-400 mr-1"></i>
                                    {{ $production->createdBy->name ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $production->created_at->format('d M Y, H:i') }}</p>
                            </div>

                            @if($production->started_at)
                            <div>
                                <p class="text-xs text-gray-600 mb-1">Dimulai Pada</p>
                                <p class="font-semibold text-gray-900">
                                    <i class="fas fa-play-circle text-blue-500 mr-1"></i>
                                    {{ $production->started_at->format('d M Y, H:i') }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $production->started_at->diffForHumans() }}</p>
                            </div>
                            @endif

                            @if($production->completed_at)
                            <div>
                                <p class="text-xs text-gray-600 mb-1">Diselesaikan Oleh</p>
                                <p class="font-semibold text-gray-900">
                                    <i class="fas fa-user-check text-green-500 mr-1"></i>
                                    {{ $production->completedBy->name ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $production->completed_at->format('d M Y, H:i') }}</p>
                            </div>

                            @if($production->started_at)
                            <div>
                                <p class="text-xs text-gray-600 mb-1">Durasi Produksi</p>
                                <p class="font-semibold text-gray-900">
                                    <i class="fas fa-stopwatch text-purple-500 mr-1"></i>
                                    {{ $production->started_at->diffForHumans($production->completed_at, true) }}
                                </p>
                            </div>
                            @endif
                            @endif
                        </div>
                    </div>
                </x-card-container>

                <!-- Product & Recipe Info -->
                <x-card-container>
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-box text-blue-600 mr-2"></i>
                            Informasi Produk & Resep
                        </h2>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-xs text-gray-600 mb-2">Produk</p>
                                <div class="flex items-center gap-3 bg-green-50 p-4 rounded-lg border border-green-200">
                                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center shadow-sm">
                                        <i class="fas fa-cube text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $production->product->name }}</p>
                                        <p class="text-xs text-gray-600">{{ $production->product->code }}</p>
                                    </div>
                                </div>
                            </div>

                            @if($production->recipe)
                            <div>
                                <p class="text-xs text-gray-600 mb-2">Resep</p>
                                <div class="flex items-center gap-3 bg-blue-50 p-4 rounded-lg border border-blue-200">
                                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center shadow-sm">
                                        <i class="fas fa-file-alt text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $production->recipe->name }}</p>
                                        <p class="text-xs text-gray-600">Output: {{ $production->recipe->output_quantity }} {{ $production->product->unit->name ?? '' }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </x-card-container>

                <!-- Materials Used -->
                <x-card-container>
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-cubes text-blue-600 mr-2"></i>
                            Bahan Baku yang Digunakan
                        </h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Bahan Baku</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Qty Rencana</th>
                                    @if($production->status === 'completed')
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Qty Aktual</th>
                                    @endif
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Harga Satuan</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($production->items as $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center mr-3 shadow-sm">
                                                <i class="fas fa-box text-white"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">{{ $item->rawMaterial->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $item->rawMaterial->unit->name ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-sm font-semibold text-gray-900">
                                            {{ number_format($item->planned_quantity, 2) }}
                                        </span>
                                    </td>
                                    @if($production->status === 'completed')
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-sm font-semibold text-green-600">
                                            {{ number_format($item->actual_quantity ?? $item->planned_quantity, 2) }}
                                        </span>
                                    </td>
                                    @endif
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-sm text-gray-600">
                                            Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-sm font-bold text-gray-900">
                                            Rp {{ number_format($item->total_price, 0, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="{{ $production->status === 'completed' ? 4 : 3 }}" class="px-6 py-4 text-right font-bold text-gray-900">
                                        Total Biaya Bahan:
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-lg font-bold text-green-600">
                                            Rp {{ number_format($production->total_material_cost, 0, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </x-card-container>

                <!-- Notes -->
                @if($production->notes)
                <x-card-container>
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-sticky-note text-gray-600 mr-2"></i>
                            Catatan
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $production->notes }}</p>
                        </div>
                    </div>
                </x-card-container>
                @endif

            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                
                <!-- Production Summary -->
                <x-card-container>
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center">
                            <i class="fas fa-chart-pie text-blue-600 mr-2"></i>
                            Ringkasan Produksi
                        </h3>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-4 rounded-lg border-2 border-green-200">
                            <p class="text-xs text-green-700 mb-1">Jumlah Rencana</p>
                            <p class="text-2xl font-bold text-green-600">
                                {{ number_format($production->planned_quantity, 2) }}
                            </p>
                            <p class="text-xs text-green-600 mt-1">{{ $production->product->unit->name ?? '' }}</p>
                        </div>

                        @if($production->status === 'completed')
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-lg border-2 border-blue-200">
                            <p class="text-xs text-blue-700 mb-1">Jumlah Aktual</p>
                            <p class="text-2xl font-bold text-blue-600">
                                {{ number_format($production->actual_quantity, 2) }}
                            </p>
                            <p class="text-xs text-blue-600 mt-1">{{ $production->product->unit->name ?? '' }}</p>
                        </div>

                        @if($production->waste_quantity > 0)
                        <div class="bg-gradient-to-br from-red-50 to-pink-50 p-4 rounded-lg border-2 border-red-200">
                            <p class="text-xs text-red-700 mb-1">Waste/Sisa</p>
                            <p class="text-2xl font-bold text-red-600">
                                {{ number_format($production->waste_quantity, 2) }}
                            </p>
                            <p class="text-xs text-red-600 mt-1">{{ $production->product->unit->name ?? '' }}</p>
                        </div>
                        @endif

                        <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-4 rounded-lg border-2 border-purple-200">
                            <p class="text-xs text-purple-700 mb-1">Efisiensi Produksi</p>
                            <p class="text-2xl font-bold text-purple-600">
                                {{ $production->planned_quantity > 0 ? number_format(($production->actual_quantity / $production->planned_quantity) * 100, 1) : 0 }}%
                            </p>
                        </div>
                        @endif
                    </div>
                </x-card-container>

                <!-- Cost Summary -->
                <x-card-container>
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center">
                            <i class="fas fa-money-bill-wave text-blue-600 mr-2"></i>
                            Ringkasan Biaya
                        </h3>
                    </div>

                    <div class="p-6 space-y-3">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Biaya Bahan Baku</span>
                            <span class="font-bold text-gray-900">
                                Rp {{ number_format($production->total_material_cost, 0, ',', '.') }}
                            </span>
                        </div>

                        @if($production->total_additional_cost > 0)
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Biaya Tambahan</span>
                            <span class="font-bold text-gray-900">
                                Rp {{ number_format($production->total_additional_cost, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        <div class="flex justify-between items-center pt-3 bg-gradient-to-br from-green-50 to-emerald-50 p-4 rounded-lg border-2 border-green-200">
                            <span class="font-bold text-green-700">Total Biaya</span>
                            <span class="text-xl font-bold text-green-600">
                                Rp {{ number_format($production->total_cost, 0, ',', '.') }}
                            </span>
                        </div>

                        @if($production->status === 'completed' && $production->actual_quantity > 0)
                        <div class="flex justify-between items-center bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-lg border-2 border-blue-200">
                            <span class="font-bold text-blue-700">HPP per Unit</span>
                            <span class="text-xl font-bold text-blue-600">
                                Rp {{ number_format($production->total_cost / $production->actual_quantity, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif
                    </div>
                </x-card-container>

            </div>

        </div>

    </div>
</main>

<!-- Complete Production Modal -->
<div id="completeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-green-500 to-emerald-500 p-4 md:p-6 rounded-t-xl">
            <h3 class="text-lg md:text-xl font-bold text-white flex items-center">
                <i class="fas fa-check-circle mr-2 md:mr-3"></i>
                Selesaikan Produksi
            </h3>
        </div>
        
        <form action="{{ route('production.complete', $production->id) }}" method="POST">
            @csrf
            <div class="p-4 md:p-6 space-y-4">
                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-2 flex-shrink-0"></i>
                        <p class="text-blue-700">Jumlah yang masuk ke stok = Total Produksi - Waste</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Total Produksi <span class="text-red-500">*</span>
                    </label>
                    <input type="number" step="0.01" name="actual_quantity" 
                        value="{{ $production->planned_quantity }}"
                        class="w-full px-3 md:px-4 py-2 md:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm md:text-base" 
                        placeholder="0" required min="0">
                    <p class="text-xs text-gray-500 mt-1">Total produk yang dihasilkan (termasuk waste)</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah Waste/Sisa
                    </label>
                    <input type="number" step="0.01" name="waste_quantity" value="0"
                        class="w-full px-3 md:px-4 py-2 md:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm md:text-base" 
                        placeholder="0" min="0">
                    <p class="text-xs text-gray-500 mt-1">Produk rusak/tidak sesuai standar</p>
                </div>

                <!-- Calculation Preview -->
                <div id="calculationPreview" class="hidden bg-green-50 border border-green-200 rounded-lg p-3">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Produksi:</span>
                            <span class="font-semibold" id="previewTotal">0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Waste:</span>
                            <span class="font-semibold text-red-600" id="previewWaste">0</span>
                        </div>
                        <div class="flex justify-between border-t border-green-300 pt-2">
                            <span class="font-bold text-green-700">Masuk Stok:</span>
                            <span class="font-bold text-green-700" id="previewNet">0</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan Penyelesaian
                    </label>
                    <textarea name="notes" rows="3"
                        class="w-full px-3 md:px-4 py-2 md:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm md:text-base" 
                        placeholder="Catatan tambahan (opsional)"></textarea>
                </div>
            </div>

            <div class="sticky bottom-0 flex flex-col sm:flex-row gap-2 sm:gap-3 p-4 md:p-6 bg-gray-50 rounded-b-xl">
                <button type="button" onclick="closeCompleteModal()" 
                    class="w-full sm:flex-1 px-4 py-2 md:py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium text-sm md:text-base">
                    Batal
                </button>
                <button type="submit" 
                    class="w-full sm:flex-1 px-4 py-2 md:py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium text-sm md:text-base">
                    <i class="fas fa-check mr-2"></i>
                    Selesaikan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Cancel Production Modal -->
<div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="bg-gradient-to-r from-red-500 to-pink-500 p-4 md:p-6 rounded-t-xl">
            <h3 class="text-lg md:text-xl font-bold text-white flex items-center">
                <i class="fas fa-times-circle mr-2 md:mr-3"></i>
                Batalkan Produksi
            </h3>
        </div>
        
        <form action="{{ route('production.cancel', $production->id) }}" method="POST">
            @csrf
            <div class="p-4 md:p-6 space-y-4">
                <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-3">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5 mr-2 flex-shrink-0"></i>
                        <p class="text-sm text-yellow-800">
                            @if($production->status === 'in_progress')
                                Bahan baku akan dikembalikan ke stok.
                            @else
                                Rencana produksi akan dihapus.
                            @endif
                        </p>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-3 md:p-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Batch:</span>
                        <span class="font-semibold text-gray-900">#{{ $production->batch_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Produk:</span>
                        <span class="font-semibold text-gray-900">{{ $production->product->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Jumlah:</span>
                        <span class="font-semibold text-gray-900">
                            {{ number_format($production->planned_quantity, 2) }} {{ $production->product->unit->name ?? '' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 p-4 md:p-6 bg-gray-50 rounded-b-xl">
                <button type="button" onclick="closeCancelModal()" 
                    class="w-full sm:flex-1 px-4 py-2 md:py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium text-sm md:text-base">
                    Tidak
                </button>
                <button type="submit" 
                    class="w-full sm:flex-1 px-4 py-2 md:py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium text-sm md:text-base">
                    <i class="fas fa-times-circle mr-2"></i>
                    Ya, Batalkan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCompleteModal() {
        document.getElementById('completeModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeCompleteModal() {
        document.getElementById('completeModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function openCancelModal() {
        document.getElementById('cancelModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modals when clicking outside
    document.getElementById('completeModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeCompleteModal();
        }
    });

    document.getElementById('cancelModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeCancelModal();
        }
    });

    // Close modals with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCompleteModal();
            closeCancelModal();
        }
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('[role="alert"]');
        alerts.forEach(function(alert) {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        });
    }, 5000);

    // Form validation for complete production
    document.querySelector('#completeModal form')?.addEventListener('submit', function(e) {
        const actualQty = parseFloat(document.querySelector('input[name="actual_quantity"]').value);
        const wasteQty = parseFloat(document.querySelector('input[name="waste_quantity"]').value) || 0;
        
        if (actualQty < 0) {
            e.preventDefault();
            alert('Jumlah aktual produksi tidak boleh negatif!');
            return false;
        }
        
        if (wasteQty < 0) {
            e.preventDefault();
            alert('Jumlah waste tidak boleh negatif!');
            return false;
        }

        // Optional: Confirm if actual quantity is significantly different from planned
        const plannedQty = {{ $production->planned_quantity }};
        const difference = Math.abs(actualQty - plannedQty);
        const percentDiff = (difference / plannedQty) * 100;
        
        if (percentDiff > 20) {
            const confirmed = confirm(
                `Jumlah aktual (${actualQty}) berbeda ${percentDiff.toFixed(1)}% dari rencana (${plannedQty}). ` +
                'Apakah Anda yakin data sudah benar?'
            );
            if (!confirmed) {
                e.preventDefault();
                return false;
            }
        }
    });

    // Real-time calculation preview
    const actualInput = document.querySelector('input[name="actual_quantity"]');
    const wasteInput = document.querySelector('input[name="waste_quantity"]');
    const calculationPreview = document.getElementById('calculationPreview');
    const previewTotal = document.getElementById('previewTotal');
    const previewWaste = document.getElementById('previewWaste');
    const previewNet = document.getElementById('previewNet');
    const unit = '{{ $production->product->unit->name ?? "" }}';

    function updateCalculationPreview() {
        const actual = parseFloat(actualInput.value) || 0;
        const waste = parseFloat(wasteInput.value) || 0;
        const net = Math.max(0, actual - waste);
        
        if (actual > 0) {
            calculationPreview.classList.remove('hidden');
            previewTotal.textContent = actual.toFixed(2) + ' ' + unit;
            previewWaste.textContent = waste.toFixed(2) + ' ' + unit;
            previewNet.textContent = net.toFixed(2) + ' ' + unit;
            
            // Warning if waste is high
            const wastePercent = actual > 0 ? (waste / actual * 100) : 0;
            if (wastePercent > 20) {
                calculationPreview.classList.remove('bg-green-50', 'border-green-200');
                calculationPreview.classList.add('bg-red-50', 'border-red-200');
            } else {
                calculationPreview.classList.remove('bg-red-50', 'border-red-200');
                calculationPreview.classList.add('bg-green-50', 'border-green-200');
            }
        } else {
            calculationPreview.classList.add('hidden');
        }
        
        // Validate waste not exceeding actual
        if (waste > actual) {
            wasteInput.setCustomValidity('Waste tidak boleh melebihi total produksi');
        } else {
            wasteInput.setCustomValidity('');
        }
    }

    actualInput?.addEventListener('input', updateCalculationPreview);
    wasteInput?.addEventListener('input', updateCalculationPreview);

    // Initialize on modal open
    function openCompleteModal() {
        document.getElementById('completeModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        updateCalculationPreview();
    }

    // Print functionality (optional enhancement)
    function printProduction() {
        window.print();
    }

    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + P for print
        if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
            e.preventDefault();
            printProduction();
        }
        
        // Ctrl/Cmd + Enter to complete production (if in progress)
        @if($production->status === 'in_progress')
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            e.preventDefault();
            openCompleteModal();
        }
        @endif
    });
</script>

<style>
    @media print {
        /* Hide non-essential elements when printing */
        nav, .no-print, button, form button {
            display: none !important;
        }
        
        /* Adjust layout for print */
        .max-w-7xl {
            max-width: 100% !important;
        }
        
        .lg\:col-span-2 {
            grid-column: span 3 !important;
        }
        
        /* Ensure colors are visible in print */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>

@endsection
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
                    <span>Produksi #{{ $production->batch_number }}</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Detail informasi produksi dan status kadaluarsa
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                @if($production->status === 'planned')
                    @can('mulai produksi')
                    <form action="{{ route('production.start', $production->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memulai produksi ini?')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-600">
                            <i class="fas fa-play text-sm"></i>
                            <span>Mulai Produksi</span>
                        </button>
                    </form>
                    @endcan
                    @can('batalkan produksi')
                    <button onclick="openCancelModal()" class="inline-flex items-center gap-2 rounded-lg bg-red-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-600">
                        <i class="fas fa-times-circle text-sm"></i>
                        <span>Batalkan</span>
                    </button>
                    @endcan
                @endif

                @if($production->status === 'in_progress')
                    @can('selesaikan produksi')
                    <button onclick="openCompleteModal()" class="inline-flex items-center gap-2 rounded-lg bg-green-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-600">
                        <i class="fas fa-check-circle text-sm"></i>
                        <span>Selesaikan</span>
                    </button>
                    @endcan
                    @can('batalkan produksi')
                    <button onclick="openCancelModal()" class="inline-flex items-center gap-2 rounded-lg bg-red-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-600">
                        <i class="fas fa-times-circle text-sm"></i>
                        <span>Batalkan</span>
                    </button>
                    @endcan
                @endif

                <a href="{{ route('production.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-white border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-arrow-left text-sm"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2 space-y-6">
                
                <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-500"></i>
                            <span>Informasi Produksi</span>
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1">Status</p>
                                @php
                                    $statusConfig = [
                                        'planned' => ['class' => 'bg-gray-50 text-gray-700 border-gray-200', 'icon' => 'fa-clock', 'text' => 'Direncanakan'],
                                        'in_progress' => ['class' => 'bg-blue-50 text-blue-700 border-blue-200', 'icon' => 'fa-spinner', 'text' => 'Sedang Proses'],
                                        'completed' => ['class' => 'bg-green-50 text-green-700 border-green-200', 'icon' => 'fa-check-circle', 'text' => 'Selesai'],
                                        'cancelled' => ['class' => 'bg-red-50 text-red-700 border-red-200', 'icon' => 'fa-times-circle', 'text' => 'Dibatalkan'],
                                    ];
                                    $config = $statusConfig[$production->status] ?? $statusConfig['planned'];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold border {{ $config['class'] }}">
                                    <i class="fas {{ $config['icon'] }} mr-2"></i>
                                    {{ $config['text'] }}
                                </span>
                            </div>

                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1">Batch Number</p>
                                <p class="text-sm font-semibold font-mono text-gray-900 bg-gray-50 px-3 py-1.5 rounded-lg inline-block border border-gray-200">
                                    {{ $production->batch_number }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1">Produk</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $production->product->name }}</p>
                                <p class="text-xs text-gray-500">{{ $production->product->code }}</p>
                            </div>

                            @if($production->recipe)
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1">Resep</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $production->recipe->name }}</p>
                                <p class="text-xs text-gray-500">Output: {{ $production->recipe->output_quantity }} {{ $production->product->unit->name ?? '' }}</p>
                            </div>
                            @endif

                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1">Dibuat Oleh</p>
                                <p class="text-sm font-semibold text-gray-900">
                                    <i class="fas fa-user text-gray-400 mr-1"></i>
                                    {{ $production->createdBy->name ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $production->created_at->format('d M Y, H:i') }}</p>
                            </div>

                            @if($production->started_at)
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1">Dimulai Pada</p>
                                <p class="text-sm font-semibold text-gray-900">
                                    <i class="fas fa-play-circle text-blue-500 mr-1"></i>
                                    {{ $production->started_at->format('d M Y, H:i') }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $production->started_at->diffForHumans() }}</p>
                            </div>
                            @endif

                            @if($production->completed_at)
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1">Diselesaikan Oleh</p>
                                <p class="text-sm font-semibold text-gray-900">
                                    <i class="fas fa-user-check text-green-500 mr-1"></i>
                                    {{ $production->completedBy->name ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $production->completed_at->format('d M Y, H:i') }}</p>
                            </div>

                            @if($production->started_at)
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1">Durasi Produksi</p>
                                <p class="text-sm font-semibold text-gray-900">
                                    <i class="fas fa-stopwatch text-purple-500 mr-1"></i>
                                    {{ $production->started_at->diffForHumans($production->completed_at, true) }}
                                </p>
                            </div>
                            @endif
                            @endif

                            @if($production->expired_at)
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1">Tanggal Kadaluarsa</p>
                                <p class="text-sm font-semibold text-gray-900">
                                    <i class="fas fa-calendar-times text-red-500 mr-1"></i>
                                    {{ $production->expired_at->format('d M Y') }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    @if($production->expired_at->isPast())
                                        <span class="text-red-600 font-semibold">Sudah kadaluarsa</span>
                                    @else
                                        {{ $production->expired_at->diffForHumans() }}
                                    @endif
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                </section>

                <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-cubes text-blue-500"></i>
                            <span>Bahan Baku yang Digunakan</span>
                        </h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Bahan Baku</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Qty Rencana</th>
                                    @if($production->status === 'completed')
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Qty Aktual</th>
                                    @endif
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Harga Satuan</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach($production->items as $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center border border-orange-100">
                                                <i class="fas fa-box text-orange-500 text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">{{ $item->rawMaterial->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $item->rawMaterial->unit->name ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <span class="text-sm font-semibold text-gray-900">
                                            {{ number_format($item->planned_quantity, 2) }}
                                        </span>
                                    </td>
                                    @if($production->status === 'completed')
                                    <td class="px-6 py-3 text-right">
                                        <span class="text-sm font-semibold text-green-600">
                                            {{ number_format($item->actual_quantity ?? $item->planned_quantity, 2) }}
                                        </span>
                                    </td>
                                    @endif
                                    <td class="px-6 py-3 text-right">
                                        <span class="text-sm text-gray-600">
                                            Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <span class="text-sm font-semibold text-gray-900">
                                            Rp {{ number_format($item->total_price, 0, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 border-t border-gray-200">
                                <tr>
                                    <td colspan="{{ $production->status === 'completed' ? 4 : 3 }}" class="px-6 py-3 text-right text-sm font-semibold text-gray-900">
                                        Total Biaya Bahan:
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <span class="text-base font-bold text-green-600">
                                            Rp {{ number_format($production->total_material_cost, 0, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </section>

                @if($production->notes)
                <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-sticky-note text-blue-500"></i>
                            <span>Catatan</span>
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $production->notes }}</p>
                        </div>
                    </div>
                </section>
                @endif

                @if($production->product->shelf_life_days && ($stats['expired_count'] > 0 || $stats['expiring_count'] > 0 || $stats['valid_count'] > 0))
                <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-calendar-times text-blue-500"></i>
                            <span>Status Kadaluarsa Produk</span>
                        </h2>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs font-medium uppercase tracking-wide text-red-600">Kadaluarsa</p>
                                        <p class="mt-1 text-xl font-bold text-red-700">{{ $stats['expired_count'] }}</p>
                                        <p class="text-xs text-red-600 mt-0.5">{{ number_format($stats['expired_quantity'], 2) }} unit</p>
                                    </div>
                                    <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center border border-red-200">
                                        <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs font-medium uppercase tracking-wide text-yellow-600">Segera Kadaluarsa</p>
                                        <p class="mt-1 text-xl font-bold text-yellow-700">{{ $stats['expiring_count'] }}</p>
                                        <p class="text-xs text-yellow-600 mt-0.5">{{ number_format($stats['expiring_quantity'], 2) }} unit</p>
                                    </div>
                                    <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center border border-yellow-200">
                                        <i class="fas fa-clock text-yellow-600 text-lg"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs font-medium uppercase tracking-wide text-green-600">Masih Valid</p>
                                        <p class="mt-1 text-xl font-bold text-green-700">{{ $stats['valid_count'] }}</p>
                                        <p class="text-xs text-green-600 mt-0.5">{{ number_format($stats['valid_quantity'], 2) }} unit</p>
                                    </div>
                                    <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center border border-green-200">
                                        <i class="fas fa-check-circle text-green-600 text-lg"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(count($expiredStocks) > 0)
                        <div class="border border-red-200 rounded-lg overflow-hidden">
                            <div class="bg-red-50 px-4 py-3 border-b border-red-200 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    @can('hapus produk kadaluarsa')
                                    <input type="checkbox" id="selectAllExpired" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500" onchange="toggleSelectAll(this)">
                                    @endcan
                                    <h3 class="text-sm font-semibold text-red-800 flex items-center gap-2">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <span>Stok Kadaluarsa ({{ count($expiredStocks) }})</span>
                                    </h3>
                                </div>
                                @can('hapus produk kadaluarsa')
                                <button onclick="openRemoveExpiredModal()" class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-700">
                                    <i class="fas fa-trash"></i>
                                    <span>Hapus Baris Terpilih</span>
                                </button>
                                @endcan
                            </div>
                            <div class="divide-y divide-red-100">
                                @foreach($expiredStocks as $stock)
                                <div class="px-4 py-3 bg-white hover:bg-red-50 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3">
                                                @can('hapus produk kadaluarsa')
                                                <input type="checkbox" class="expired-checkbox w-4 h-4 text-red-600 border-gray-300 rounded" value="{{ $stock['batch_number'] }}">
                                                @endcan
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-900">Batch #{{ $stock['batch_number'] }}</p>
                                                    <div class="flex items-center gap-4 mt-1">
                                                        <p class="text-xs text-gray-600">
                                                            <i class="fas fa-cubes mr-1"></i>
                                                            {{ number_format($stock['quantity'], 2) }} {{ $production->product->unit->name ?? '' }}
                                                        </p>
                                                        <p class="text-xs text-gray-500">
                                                            <i class="fas fa-calendar mr-1"></i>
                                                            Produksi: {{ $stock['completed_at']->format('d M Y') }}
                                                        </p>
                                                        <p class="text-xs text-red-600 font-semibold">
                                                            <i class="fas fa-calendar-times mr-1"></i>
                                                            Kadaluarsa: {{ $stock['expired_at']->format('d M Y') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200">
                                            {{ $stock['expired_at']->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if(count($expiringStocks) > 0)
                        <div class="border border-yellow-200 rounded-lg overflow-hidden">
                            <div class="bg-yellow-50 px-4 py-3 border-b border-yellow-200">
                                <h3 class="text-sm font-semibold text-yellow-800 flex items-center gap-2">
                                    <i class="fas fa-clock"></i>
                                    <span>Segera Kadaluarsa ({{ count($expiringStocks) }})</span>
                                </h3>
                            </div>
                            <div class="divide-y divide-yellow-100">
                                @foreach($expiringStocks as $stock)
                                <div class="px-4 py-3 bg-white hover:bg-yellow-50 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-900">Batch #{{ $stock['batch_number'] }}</p>
                                            <div class="flex items-center gap-4 mt-1">
                                                <p class="text-xs text-gray-600">
                                                    <i class="fas fa-cubes mr-1"></i>
                                                    {{ number_format($stock['quantity'], 2) }} {{ $production->product->unit->name ?? '' }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    <i class="fas fa-calendar mr-1"></i>
                                                    Produksi: {{ $stock['completed_at']->format('d M Y') }}
                                                </p>
                                                <p class="text-xs text-yellow-600 font-semibold">
                                                    <i class="fas fa-calendar-times mr-1"></i>
                                                    Kadaluarsa: {{ $stock['expired_at']->format('d M Y') }}
                                                </p>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700 border border-yellow-200">
                                            {{ $stock['expired_at']->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if(count($validStocks) > 0)
                        <div class="border border-green-200 rounded-lg overflow-hidden">
                            <div class="bg-green-50 px-4 py-3 border-b border-green-200">
                                <h3 class="text-sm font-semibold text-green-800 flex items-center gap-2">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Stok Valid ({{ count($validStocks) }})</span>
                                </h3>
                            </div>
                            <div class="divide-y divide-green-100 max-h-64 overflow-y-auto">
                                @foreach($validStocks as $stock)
                                <div class="px-4 py-3 bg-white hover:bg-green-50 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-900">Batch #{{ $stock['batch_number'] }}</p>
                                            <div class="flex items-center gap-4 mt-1">
                                                <p class="text-xs text-gray-600">
                                                    <i class="fas fa-cubes mr-1"></i>
                                                    {{ number_format($stock['quantity'], 2) }} {{ $production->product->unit->name ?? '' }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    <i class="fas fa-calendar mr-1"></i>
                                                    Produksi: {{ $stock['completed_at']->format('d M Y') }}
                                                </p>
                                                <p class="text-xs text-green-600">
                                                    <i class="fas fa-calendar-check mr-1"></i>
                                                    Kadaluarsa: {{ $stock['expired_at']->format('d M Y') }}
                                                </p>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200">
                                            {{ $stock['expired_at']->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </section>
                @endif

            </div>

            <div class="space-y-6">
                
                <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-chart-pie text-blue-500"></i>
                            <span>Ringkasan Produksi</span>
                        </h3>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <p class="text-xs font-medium text-green-600 mb-1">Jumlah Rencana</p>
                            <p class="text-2xl font-bold text-green-700">
                                {{ number_format($production->planned_quantity, 2) }}
                            </p>
                            <p class="text-xs text-green-600 mt-1">{{ $production->product->unit->name ?? '' }}</p>
                        </div>

                        @if($production->status === 'completed')
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-xs font-medium text-blue-600 mb-1">Jumlah Aktual</p>
                            <p class="text-2xl font-bold text-blue-700">
                                {{ number_format($production->actual_quantity, 2) }}
                            </p>
                            <p class="text-xs text-blue-600 mt-1">{{ $production->product->unit->name ?? '' }}</p>
                        </div>

                        @if($production->waste_quantity > 0)
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <p class="text-xs font-medium text-red-600 mb-1">Waste/Sisa</p>
                            <p class="text-2xl font-bold text-red-700">
                                {{ number_format($production->waste_quantity, 2) }}
                            </p>
                            <p class="text-xs text-red-600 mt-1">{{ $production->product->unit->name ?? '' }}</p>
                        </div>
                        @endif

                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <p class="text-xs font-medium text-purple-600 mb-1">Efisiensi Produksi</p>
                            <p class="text-2xl font-bold text-purple-700">
                                {{ $production->planned_quantity > 0 ? number_format(($production->actual_quantity / $production->planned_quantity) * 100, 1) : 0 }}%
                            </p>
                        </div>
                        @endif
                    </div>
                </section>

                <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-money-bill-wave text-blue-500"></i>
                            <span>Ringkasan Biaya</span>
                        </h3>
                    </div>

                    <div class="p-6 space-y-3">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Biaya Bahan Baku</span>
                            <span class="text-sm font-semibold text-gray-900">
                                Rp {{ number_format($production->total_material_cost, 0, ',', '.') }}
                            </span>
                        </div>

                        @if($production->total_additional_cost > 0)
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Biaya Tambahan</span>
                            <span class="text-sm font-semibold text-gray-900">
                                Rp {{ number_format($production->total_additional_cost, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        <div class="flex justify-between items-center pt-3 bg-green-50 border border-green-200 p-4 rounded-lg">
                            <span class="text-sm font-semibold text-green-700">Total Biaya</span>
                            <span class="text-lg font-bold text-green-700">
                                Rp {{ number_format($production->total_cost, 0, ',', '.') }}
                            </span>
                        </div>

                        @if($production->status === 'completed' && $production->actual_quantity > 0)
                        <div class="flex justify-between items-center bg-blue-50 border border-blue-200 p-4 rounded-lg">
                            <span class="text-sm font-semibold text-blue-700">HPP per Unit</span>
                            <span class="text-lg font-bold text-blue-700">
                                Rp {{ number_format($production->total_cost / $production->actual_quantity, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif
                    </div>
                </section>

            </div>

        </div>

    </div>
</main>

<div id="completeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-green-500 p-4 md:p-6 rounded-t-xl">
            <h3 class="text-lg md:text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                <span>Selesaikan Produksi</span>
            </h3>
        </div>
        
        <form action="{{ route('production.complete', $production->id) }}" method="POST">
            @csrf
            <div class="p-4 md:p-6 space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5 flex-shrink-0"></i>
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

            <div class="sticky bottom-0 flex flex-col sm:flex-row gap-2 sm:gap-3 p-4 md:p-6 bg-gray-50 rounded-b-xl border-t border-gray-200">
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

<div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="bg-red-500 p-4 md:p-6 rounded-t-xl">
            <h3 class="text-lg md:text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-times-circle"></i>
                <span>Batalkan Produksi</span>
            </h3>
        </div>
        
        <form action="{{ route('production.cancel', $production->id) }}" method="POST">
            @csrf
            <div class="p-4 md:p-6 space-y-4">
                <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-3">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5 flex-shrink-0"></i>
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

            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 p-4 md:p-6 bg-gray-50 rounded-b-xl border-t border-gray-200">
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

<div id="removeExpiredModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="bg-red-500 p-4 md:p-6 rounded-t-xl">
            <h3 class="text-lg md:text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-trash"></i>
                <span>Hapus Stok Kadaluarsa</span>
            </h3>
        </div>
        
        <form action="{{ route('production.remove-expired', $production->id) }}" method="POST" id="removeExpiredForm">
            @csrf
            <div class="p-4 md:p-6 space-y-4">
                <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-3">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5 flex-shrink-0"></i>
                        <p class="text-sm text-yellow-800">
                            Stok yang dipilih akan dihapus dari sistem dan tidak dapat dikembalikan.
                        </p>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-3 md:p-4 text-sm">
                    <p class="text-gray-700">
                        <span class="font-semibold" id="selectedCount">0</span> batch dipilih untuk dihapus
                    </p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 p-4 md:p-6 bg-gray-50 rounded-b-xl border-t border-gray-200">
                <button type="button" onclick="closeRemoveExpiredModal()" 
                    class="w-full sm:flex-1 px-4 py-2 md:py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium text-sm md:text-base">
                    Batal
                </button>
                <button type="submit" 
                    class="w-full sm:flex-1 px-4 py-2 md:py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium text-sm md:text-base">
                    <i class="fas fa-trash mr-2"></i>
                    Hapus Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openCompleteModal() {
        document.getElementById('completeModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        updateCalculationPreview();
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

    function toggleSelectAll(checkbox) {
        document.querySelectorAll('.expired-checkbox').forEach(el => {
            el.checked = checkbox.checked;
        });
    }

    function openRemoveExpiredModal() {
        const checkboxes = document.querySelectorAll('.expired-checkbox:checked');
        if (checkboxes.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Opps...',
                text: 'Pilih minimal satu batch untuk dihapus',
            });
            return;
        }
        
        const form = document.getElementById('removeExpiredForm');
        form.querySelectorAll('input[name="batch_numbers[]"]').forEach(el => el.remove());
        
        checkboxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'batch_numbers[]';
            input.value = checkbox.value;
            form.appendChild(input);
        });
        
        document.getElementById('selectedCount').textContent = checkboxes.length;
        document.getElementById('removeExpiredModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeRemoveExpiredModal() {
        document.getElementById('removeExpiredModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    document.getElementById('completeModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeCompleteModal();
    });

    document.getElementById('cancelModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeCancelModal();
    });

    document.getElementById('removeExpiredModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeRemoveExpiredModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCompleteModal();
            closeCancelModal();
            closeRemoveExpiredModal();
        }
    });

    // setTimeout(function() {
    //     const alerts = document.querySelectorAll('[role="alert"], .border-green-200, .border-red-200');
    //     alerts.forEach(function(alert) {
    //         if (alert.classList.contains('border-green-200') || alert.classList.contains('border-red-200')) {
    //             alert.style.transition = 'opacity 0.5s ease-out';
    //             alert.style.opacity = '0';
    //             setTimeout(function() {
    //                 alert.remove();
    //             }, 500);
    //         }
    //     });
    // }, 5000);

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
        
        if (waste > actual) {
            wasteInput.setCustomValidity('Waste tidak boleh melebihi total produksi');
        } else {
            wasteInput.setCustomValidity('');
        }
    }

    actualInput?.addEventListener('input', updateCalculationPreview);
    wasteInput?.addEventListener('input', updateCalculationPreview);

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
</script>
@endpush
@endsection
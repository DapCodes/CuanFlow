@extends('layouts.app')

@section('title', 'Detail Stok - ' . $product->name)

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
    <span class="text-gray-900 font-medium">Detail Stok</span>
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
                        <i class="fas fa-chart-line text-sm"></i>
                    </span>
                    <span>Detail Stok - {{ $product->name }}</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Informasi lengkap stok produk dan status kadaluarsa
                </p>
            </div>
            <div class="flex items-center gap-3">
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
                            <span>Informasi Produk</span>
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1">Kode Produk</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $product->code }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1">Nama Produk</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $product->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1">Satuan</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $product->unit->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1">Total Stok</p>
                                <p class="text-lg font-bold text-green-600">{{ number_format($totalStock, 2) }} {{ $product->unit->name ?? '' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1">Minimum Stok</p>
                                <p class="text-sm font-semibold text-gray-900">{{ number_format($product->min_stock, 2) }} {{ $product->unit->name ?? '' }}</p>
                            </div>
                            @if($product->shelf_life_days)
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1">Masa Simpan</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $product->shelf_life_days }} hari</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </section>

                @if($product->shelf_life_days)
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
                                <h3 class="text-sm font-semibold text-red-800 flex items-center gap-2">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span>Stok Kadaluarsa ({{ count($expiredStocks) }})</span>
                                </h3>
                                @if(count($expiredStocks) > 0)
                                <button onclick="openRemoveExpiredModal()" class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-700">
                                    <i class="fas fa-trash"></i>
                                    <span>Hapus Semua</span>
                                </button>
                                @endif
                            </div>
                            <div class="divide-y divide-red-100">
                                @foreach($expiredStocks as $stock)
                                <div class="px-4 py-3 bg-white hover:bg-red-50 transition-colors">
                                    <div class="flex items-center justify-between flex-wrap gap-3">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <input type="checkbox" class="expired-checkbox w-4 h-4 text-red-600 border-gray-300 rounded flex-shrink-0" value="{{ $stock['batch_number'] }}">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-gray-900 truncate">Batch #{{ $stock['batch_number'] }}</p>
                                                <div class="flex items-center gap-3 mt-1 flex-wrap text-xs">
                                                    <span class="text-gray-600">
                                                        <i class="fas fa-cubes mr-1"></i>
                                                        {{ number_format($stock['quantity'], 2) }} {{ $product->unit->name ?? '' }}
                                                    </span>
                                                    <span class="text-gray-500">
                                                        <i class="fas fa-calendar mr-1"></i>
                                                        Produksi: {{ $stock['completed_at']->format('d M Y') }}
                                                    </span>
                                                    <span class="text-red-600 font-semibold">
                                                        <i class="fas fa-calendar-times mr-1"></i>
                                                        Kadaluarsa: {{ $stock['expired_at']->format('d M Y') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200 flex-shrink-0">
                                            {{ abs($stock['days_until_expiry']) }} hari lewat
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
                                    <div class="flex items-center justify-between flex-wrap gap-3">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 truncate">Batch #{{ $stock['batch_number'] }}</p>
                                            <div class="flex items-center gap-3 mt-1 flex-wrap text-xs">
                                                <span class="text-gray-600">
                                                    <i class="fas fa-cubes mr-1"></i>
                                                    {{ number_format($stock['quantity'], 2) }} {{ $product->unit->name ?? '' }}
                                                </span>
                                                <span class="text-gray-500">
                                                    <i class="fas fa-calendar mr-1"></i>
                                                    Produksi: {{ $stock['completed_at']->format('d M Y') }}
                                                </span>
                                                <span class="text-yellow-600 font-semibold">
                                                    <i class="fas fa-calendar-times mr-1"></i>
                                                    Kadaluarsa: {{ $stock['expired_at']->format('d M Y') }}
                                                </span>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700 border border-yellow-200 flex-shrink-0">
                                            {{ $stock['days_until_expiry'] }} hari lagi
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
                                    <div class="flex items-center justify-between flex-wrap gap-3">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 truncate">Batch #{{ $stock['batch_number'] }}</p>
                                            <div class="flex items-center gap-3 mt-1 flex-wrap text-xs">
                                                <span class="text-gray-600">
                                                    <i class="fas fa-cubes mr-1"></i>
                                                    {{ number_format($stock['quantity'], 2) }} {{ $product->unit->name ?? '' }}
                                                </span>
                                                <span class="text-gray-500">
                                                    <i class="fas fa-calendar mr-1"></i>
                                                    Produksi: {{ $stock['completed_at']->format('d M Y') }}
                                                </span>
                                                <span class="text-green-600">
                                                    <i class="fas fa-calendar-check mr-1"></i>
                                                    Kadaluarsa: {{ $stock['expired_at']->format('d M Y') }}
                                                </span>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200 flex-shrink-0">
                                            {{ $stock['days_until_expiry'] }} hari lagi
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if(count($expiredStocks) == 0 && count($expiringStocks) == 0 && count($validStocks) == 0)
                        <div class="text-center py-8">
                            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-box-open text-2xl text-gray-300"></i>
                            </div>
                            <p class="text-sm text-gray-500">Belum ada data stok produksi</p>
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
                            <span>Ringkasan Stok</span>
                        </h3>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-xs font-medium text-blue-600 mb-1">Total Stok Saat Ini</p>
                            <p class="text-2xl font-bold text-blue-700">
                                {{ number_format($totalStock, 2) }}
                            </p>
                            <p class="text-xs text-blue-600 mt-1">{{ $product->unit->name ?? '' }}</p>
                        </div>

                        @if($product->shelf_life_days)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <p class="text-xs font-medium text-green-600 mb-1">Stok Valid</p>
                            <p class="text-2xl font-bold text-green-700">
                                {{ number_format($stats['valid_quantity'], 2) }}
                            </p>
                            <p class="text-xs text-green-600 mt-1">{{ $stats['valid_count'] }} batch</p>
                        </div>

                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <p class="text-xs font-medium text-yellow-600 mb-1">Segera Kadaluarsa</p>
                            <p class="text-2xl font-bold text-yellow-700">
                                {{ number_format($stats['expiring_quantity'], 2) }}
                            </p>
                            <p class="text-xs text-yellow-600 mt-1">{{ $stats['expiring_count'] }} batch</p>
                        </div>

                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <p class="text-xs font-medium text-red-600 mb-1">Sudah Kadaluarsa</p>
                            <p class="text-2xl font-bold text-red-700">
                                {{ number_format($stats['expired_quantity'], 2) }}
                            </p>
                            <p class="text-xs text-red-600 mt-1">{{ $stats['expired_count'] }} batch</p>
                        </div>
                        @endif
                    </div>
                </section>

            </div>

        </div>

    </div>
</main>

<div id="removeExpiredModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="bg-red-500 p-4 md:p-6 rounded-t-xl">
            <h3 class="text-lg md:text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-trash"></i>
                <span>Hapus Stok Kadaluarsa</span>
            </h3>
        </div>
        
        <form action="{{ route('production.stock.remove-expired', $product->id) }}" method="POST" id="removeExpiredForm">
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
    function openRemoveExpiredModal() {
        const checkboxes = document.querySelectorAll('.expired-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('Pilih minimal satu batch untuk dihapus');
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

    document.getElementById('removeExpiredModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeRemoveExpiredModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeRemoveExpiredModal();
        }
    });

    // setTimeout(function() {
    //     const alerts = document.querySelectorAll('.border-green-200, .border-red-200');
    //     alerts.forEach(function(alert) {
    //         alert.style.transition = 'opacity 0.5s ease-out';
    //         alert.style.opacity = '0';
    //         setTimeout(function() {
    //             alert.remove();
    //         }, 500);
    //     });
    // }, 5000);
</script>
@endpush
@endsection
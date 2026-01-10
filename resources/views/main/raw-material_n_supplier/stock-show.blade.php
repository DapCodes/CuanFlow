@extends('layouts.app')

@section('title', 'Detail Stok - ' . $rawMaterial->name)

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('raw-materials.index') }}" class="text-gray-500 hover:text-red-600 transition-colors">Bahan Baku</a>
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
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-500 border border-red-100">
                        <i class="fas fa-chart-line text-sm"></i>
                    </span>
                    <span>Detail Stok - {{ $rawMaterial->name }}</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Informasi lengkap stok bahan baku dan status kadaluarsa per batch
                </p>
            </div>
            <div class="flex items-center gap-3">
                @can('kelola stok bahan baku')
                <a href="{{ route('raw-materials.manage-stock', $rawMaterial) }}" class="inline-flex items-center gap-2 rounded-lg bg-white border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all">
                    <i class="fas fa-boxes text-sm text-red-500"></i>
                    <span>Manage Transaksi</span>
                </a>
                @endcan
                <a href="{{ route('raw-materials.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-white border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all">
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
                            <i class="fas fa-info-circle text-red-500"></i>
                            <span>Informasi Bahan Baku</span>
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1 scale-90 origin-left uppercase tracking-wider">Kode</p>
                                <p class="text-sm font-semibold text-gray-900 font-mono">{{ $rawMaterial->code }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1 scale-90 origin-left uppercase tracking-wider">Nama</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $rawMaterial->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1 scale-90 origin-left uppercase tracking-wider">Satuan</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $rawMaterial->unit->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1 scale-90 origin-left uppercase tracking-wider">Total Stok</p>
                                <p class="text-lg font-bold text-emerald-600">{{ number_format($currentStock, 2) }} {{ $rawMaterial->unit->abbreviation ?? '' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1 scale-90 origin-left uppercase tracking-wider">Minimum Stok</p>
                                <p class="text-sm font-semibold text-gray-900">{{ number_format($rawMaterial->min_stock, 2) }} {{ $rawMaterial->unit->abbreviation ?? '' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1 scale-90 origin-left uppercase tracking-wider">Kategori</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $rawMaterial->category->name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-calendar-times text-red-500"></i>
                            <span>Status Kadaluarsa per Batch</span>
                        </h2>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wide text-red-600">Kadaluarsa</p>
                                        <p class="mt-1 text-xl font-bold text-red-700">{{ $stats['expired_count'] }}</p>
                                        <p class="text-xs text-red-600 mt-0.5 font-medium">{{ number_format($stats['expired_quantity'], 2) }} {{ $rawMaterial->unit->abbreviation }}</p>
                                    </div>
                                    <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center border border-red-200 shadow-sm">
                                        <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wide text-yellow-600">Segera Kadaluarsa</p>
                                        <p class="mt-1 text-xl font-bold text-yellow-700">{{ $stats['expiring_count'] }}</p>
                                        <p class="text-xs text-yellow-600 mt-0.5 font-medium">{{ number_format($stats['expiring_quantity'], 2) }} {{ $rawMaterial->unit->abbreviation }}</p>
                                    </div>
                                    <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center border border-yellow-200 shadow-sm">
                                        <i class="fas fa-clock text-yellow-600 text-lg"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-emerald-50 border border-emerald-200 rounded-lg px-4 py-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wide text-emerald-600">Masih Valid</p>
                                        <p class="mt-1 text-xl font-bold text-emerald-700">{{ $stats['valid_count'] }}</p>
                                        <p class="text-xs text-emerald-600 mt-0.5 font-medium">{{ number_format($stats['valid_quantity'], 2) }} {{ $rawMaterial->unit->abbreviation }}</p>
                                    </div>
                                    <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center border border-emerald-200 shadow-sm">
                                        <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Batch Lists --}}
                        <div class="space-y-6 mt-8">
                            {{-- Expired Section --}}
                            @if(count($expiredStocks) > 0)
                            <div class="border border-red-200 rounded-lg overflow-hidden shadow-sm">
                                <div class="bg-red-50 px-4 py-3 border-b border-red-200 flex items-center justify-between">
                                    <h3 class="text-sm font-bold text-red-800 flex items-center gap-2">
                                        @can('update stok bahan baku')
                                        <input type="checkbox" onchange="toggleSelectAll(this)" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500" title="Pilih Semua">
                                        @endcan
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <span>Stok Kadaluarsa ({{ count($expiredStocks) }})</span>
                                    </h3>
                                    @can('update stok bahan baku')
                                    <button onclick="openRemoveExpiredModal()" class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-red-700 shadow-sm transition-all">
                                        <i class="fas fa-trash"></i>
                                        <span>BUANG STOK TERPILIH</span>
                                    </button>
                                    @endcan
                                </div>
                                <div class="divide-y divide-red-100">
                                    @foreach($expiredStocks as $stock)
                                    <div class="px-4 py-3 bg-white hover:bg-red-50 transition-colors">
                                        <div class="flex items-center justify-between flex-wrap gap-3">
                                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                                @can('update stok bahan baku')
                                                <input type="checkbox" class="expired-checkbox w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500" value="{{ $stock['id'] }}">
                                                @endcan
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-bold text-gray-900 truncate">Batch #{{ $stock['batch_number'] }}</p>
                                                    <div class="flex items-center gap-3 mt-1 flex-wrap text-[10px]">
                                                        <span class="text-gray-600">
                                                            <i class="fas fa-cubes mr-1 text-gray-400"></i>
                                                            {{ number_format($stock['quantity'], 2) }} {{ $rawMaterial->unit->abbreviation }}
                                                        </span>
                                                        <span class="text-red-600 font-bold bg-red-50 px-1.5 py-0.5 rounded">
                                                            <i class="fas fa-calendar-times mr-1"></i>
                                                            KADALUARSA: {{ $stock['expired_at']->format('d M Y') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 border border-red-200">
                                                {{ $stock['expired_at']->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            {{-- Expiring Section --}}
                            @if(count($expiringStocks) > 0)
                            <div class="border border-yellow-200 rounded-lg overflow-hidden shadow-sm">
                                <div class="bg-yellow-50 px-4 py-3 border-b border-yellow-200">
                                    <h3 class="text-sm font-bold text-yellow-800 flex items-center gap-2">
                                        <i class="fas fa-clock"></i>
                                        <span>Segera Kadaluarsa ({{ count($expiringStocks) }})</span>
                                    </h3>
                                </div>
                                <div class="divide-y divide-yellow-100">
                                    @foreach($expiringStocks as $stock)
                                    <div class="px-4 py-3 bg-white hover:bg-yellow-50 transition-colors">
                                        <div class="flex items-center justify-between flex-wrap gap-3">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-bold text-gray-900 truncate">Batch #{{ $stock['batch_number'] }}</p>
                                                <div class="flex items-center gap-3 mt-1 flex-wrap text-[10px]">
                                                    <span class="text-gray-600">
                                                        <i class="fas fa-cubes mr-1 text-gray-400"></i>
                                                        {{ number_format($stock['quantity'], 2) }} {{ $rawMaterial->unit->abbreviation }}
                                                    </span>
                                                    <span class="text-yellow-600 font-bold bg-yellow-50 px-1.5 py-0.5 rounded">
                                                        <i class="fas fa-calendar-times mr-1"></i>
                                                        KADALUARSA: {{ $stock['expired_at']->format('d M Y') }}
                                                    </span>
                                                </div>
                                            </div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-700 border border-yellow-200">
                                                {{ $stock['days_until_expiry'] }} hari lagi
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            {{-- Valid Section --}}
                            @if(count($validStocks) > 0)
                            <div class="border border-emerald-200 rounded-lg overflow-hidden shadow-sm">
                                <div class="bg-emerald-50 px-4 py-3 border-b border-emerald-200">
                                    <h3 class="text-sm font-bold text-emerald-800 flex items-center gap-2">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Stok Valid / Aman ({{ count($validStocks) }})</span>
                                    </h3>
                                </div>
                                <div class="divide-y divide-emerald-100 max-h-96 overflow-y-auto">
                                    @foreach($validStocks as $stock)
                                    <div class="px-4 py-3 bg-white hover:bg-emerald-50 transition-colors">
                                        <div class="flex items-center justify-between flex-wrap gap-3">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-bold text-gray-900 truncate">Batch #{{ $stock['batch_number'] }}</p>
                                                <div class="flex items-center gap-3 mt-1 flex-wrap text-[10px]">
                                                    <span class="text-gray-600">
                                                        <i class="fas fa-cubes mr-1 text-gray-400"></i>
                                                        {{ number_format($stock['quantity'], 2) }} {{ $rawMaterial->unit->abbreviation }}
                                                    </span>
                                                    @if($stock['expired_at'])
                                                    <span class="text-emerald-600 font-bold bg-emerald-50 px-1.5 py-0.5 rounded">
                                                        <i class="fas fa-calendar-check mr-1"></i>
                                                        KADALUARSA: {{ $stock['expired_at']->format('d M Y') }}
                                                    </span>
                                                    @else
                                                    <span class="text-gray-500 font-bold bg-gray-50 px-1.5 py-0.5 rounded">
                                                        <i class="fas fa-infinity mr-1"></i>
                                                        TIDAK ADA KADALUARSA
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                                {{ $stock['expired_at'] ? $stock['days_until_expiry'] . ' hari lagi' : 'AMAN' }}
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if(count($expiredStocks) == 0 && count($expiringStocks) == 0 && count($validStocks) == 0)
                            <div class="text-center py-12 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-box-open text-2xl text-gray-300"></i>
                                </div>
                                <p class="text-sm text-gray-500 font-medium">Belum ada data stok bahan baku per batch</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </section>
            </div>

            <div class="space-y-6">
                
                <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="border-b border-gray-200 px-6 py-4 bg-gray-50/50">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-chart-pie text-red-500"></i>
                            <span>Ringkasan Stok</span>
                        </h3>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 shadow-sm">
                            <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider mb-1">Total Stok Tersedia</p>
                            <div class="flex items-baseline gap-2">
                                <p class="text-3xl font-black text-emerald-700">
                                    {{ number_format($currentStock, 2) }}
                                </p>
                                <p class="text-sm font-bold text-emerald-600">{{ $rawMaterial->unit->abbreviation ?? '' }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-3">
                            <div class="bg-white border border-gray-200 rounded-lg p-4 flex items-center justify-between shadow-sm">
                                <div>
                                    <p class="text-[10px] font-bold text-gray-500 uppercase">Stok Aman</p>
                                    <p class="text-lg font-bold text-emerald-600">{{ number_format($stats['valid_quantity'], 2) }}</p>
                                </div>
                                <div class="w-8 h-8 rounded bg-emerald-50 flex items-center justify-center text-emerald-600">
                                    <i class="fas fa-check-circle text-sm"></i>
                                </div>
                            </div>

                            <div class="bg-white border border-gray-200 rounded-lg p-4 flex items-center justify-between shadow-sm">
                                <div>
                                    <p class="text-[10px] font-bold text-gray-500 uppercase">Hampir Kadaluarsa</p>
                                    <p class="text-lg font-bold text-yellow-600">{{ number_format($stats['expiring_quantity'], 2) }}</p>
                                </div>
                                <div class="w-8 h-8 rounded bg-yellow-50 flex items-center justify-center text-yellow-600">
                                    <i class="fas fa-hourglass-half text-sm"></i>
                                </div>
                            </div>

                            <div class="bg-white border border-gray-200 rounded-lg p-4 flex items-center justify-between shadow-sm">
                                <div>
                                    <p class="text-[10px] font-bold text-gray-500 uppercase">Sudah Kadaluarsa</p>
                                    <p class="text-lg font-bold text-red-600">{{ number_format($stats['expired_quantity'], 2) }}</p>
                                </div>
                                <div class="w-8 h-8 rounded bg-red-50 flex items-center justify-center text-red-600">
                                    <i class="fas fa-calendar-times text-sm"></i>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">Quick Actions</h4>
                            <div class="grid grid-cols-1 gap-2">
                                @can('lihat riwayat stok bahan baku')
                                <a href="{{ route('raw-materials.stock-history', $rawMaterial) }}" class="flex items-center gap-3 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-lg transition-all border border-gray-200">
                                    <i class="fas fa-history text-gray-400"></i>
                                    Lihat Riwayat Stok
                                </a>
                                @endcan
                                @can('kelola stok bahan baku')
                                <a href="{{ route('raw-materials.manage-stock', $rawMaterial) }}" class="flex items-center gap-3 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-lg transition-all border border-gray-200">
                                    <i class="fas fa-plus-circle text-gray-400"></i>
                                    Update Persediaan
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</main>

{{-- Remove Expired Modal --}}
<div id="removeExpiredModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="bg-red-600 p-6 flex flex-col items-center">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-trash-alt text-3xl text-white"></i>
            </div>
            <h3 class="text-xl font-black text-white text-center">Buang Stok Kadaluarsa?</h3>
        </div>
        
        <form action="{{ route('raw-materials.remove-expired', $rawMaterial) }}" method="POST" id="removeExpiredForm">
            @csrf
            <div class="p-6 space-y-4">
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex gap-3">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mt-1"></i>
                    <p class="text-sm text-yellow-800 leading-relaxed font-medium">
                        Tindakan ini akan menghapus stok batch terpilih dari sistem secara permanen dan mencatatnya sebagai penyesuaian stok keluar.
                    </p>
                </div>

                <div class="bg-gray-50 rounded-xl p-4 text-center">
                    <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-1">Batch Terpilih</p>
                    <p class="text-3xl font-black text-gray-800">
                        <span id="selectedCountText">0</span>
                    </p>
                </div>
            </div>

            <div class="flex gap-3 p-6 pt-0">
                <button type="button" onclick="closeRemoveExpiredModal()" 
                    class="flex-1 px-4 py-3 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-all font-bold text-sm tracking-wide">
                    BATAL
                </button>
                <button type="submit" 
                    class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all font-bold text-sm tracking-wide shadow-lg shadow-red-200">
                    HAPUS SEKARANG
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleSelectAll(checkbox) {
        document.querySelectorAll('.expired-checkbox').forEach(cb => {
            cb.checked = checkbox.checked;
        });
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const count = document.querySelectorAll('.expired-checkbox:checked').length;
        const textElement = document.getElementById('selectedCountText');
        if(textElement) textElement.textContent = count;
    }

    // Add event listeners for checkboxes
    document.querySelectorAll('.expired-checkbox').forEach(cb => {
        cb.addEventListener('change', updateSelectedCount);
    });

    function openRemoveExpiredModal() {
        const checkboxes = document.querySelectorAll('.expired-checkbox:checked');
        if (checkboxes.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Opps!',
                text: 'Silakan pilih minimal satu batch untuk dibuang.',
                confirmButtonColor: '#EF4444'
            });
            return;
        }
        
        const form = document.getElementById('removeExpiredForm');
        // Clear existing hidden inputs
        form.querySelectorAll('input[name="batch_ids[]"]').forEach(el => el.remove());
        
        checkboxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'batch_ids[]';
            input.value = checkbox.value;
            form.appendChild(input);
        });
        
        updateSelectedCount();
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
</script>
@endpush
@endsection

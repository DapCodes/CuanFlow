@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Detail Stok - ' . $rawMaterial->name)

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('raw-materials.index') }}" class="text-gray-500 hover:text-cuan-green transition-colors">Bahan Baku</a>
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight whitespace-nowrap">Status Batch</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-cuan-green/10 flex items-center justify-center text-cuan-green">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <h1 class="text-xl md:text-2xl font-black text-gray-900 uppercase tracking-tight">
                        Detail Per Batch
                    </h1>
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    Monitor masa simpan dan lokasi batch untuk <span class="text-cuan-green font-bold">{{ $rawMaterial->name }}</span>
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                 <a href="{{ route('raw-materials.index') }}" 
                   class="inline-flex items-center gap-2 rounded-xl bg-white border border-gray-200 px-5 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all shadow-sm active:scale-95">
                    <span>Kembali</span>
                </a>
                @can('kelola stok bahan baku')
                <a href="{{ route('raw-materials.manage-stock', $rawMaterial) }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-5 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                    <i class="fas fa-boxes opacity-50"></i>
                    <span>Manage Mutasi</span>
                </a>
                @endcan
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2 space-y-6">

                {{-- STATISTIK BATCH --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Fisik</p>
                        <p class="text-2xl font-black text-gray-900 mt-2 tracking-tight">{{ number_format($currentStock, 2) }} <span class="text-[10px] font-bold text-gray-400">{{ $rawMaterial->unit->abbreviation }}</span></p>
                        <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between">
                            <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">Kode</span>
                            <span class="text-[10px] font-black text-gray-900 font-mono">{{ $rawMaterial->code }}</span>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Kapasitas Aman</p>
                        @php $safeBatchCount = count($validStocks); @endphp
                        <p class="text-2xl font-black text-cuan-green mt-2 tracking-tight">{{ $safeBatchCount }} <span class="text-[10px] font-bold text-gray-400">Batch</span></p>
                        <p class="mt-2 text-[10px] font-bold text-emerald-600/60 uppercase tracking-widest tracking-tighter">Status: Optimal</p>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Batas Minimum</p>
                        <p class="text-2xl font-black text-gray-900 mt-2 tracking-tight">{{ number_format($rawMaterial->min_stock, 2) }} <span class="text-[10px] font-bold text-gray-400">{{ $rawMaterial->unit->abbreviation }}</span></p>
                        <p class="mt-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Inventory Threshold</p>
                    </div>
                </div>

                {{-- BATCH MONITORING --}}
                <x-card-container title="Pemantauan Batas Kadaluarsa">
                    <div class="p-8 space-y-12">
                        
                        {{-- EXPIRED LIST --}}
                        @if(count($expiredStocks) > 0)
                        <div class="space-y-4">
                            <div class="flex items-center justify-between px-2">
                                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-red-500 flex items-center gap-3">
                                    <div class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></div>
                                    Varian Kadaluarsa
                                </h3>
                                @can('update stok bahan baku')
                                <button onclick="openRemoveExpiredModal()" class="text-[10px] font-black uppercase text-red-600 hover:text-red-700 transition-colors">Hapus Batch Terpilih</button>
                                @endcan
                            </div>
                            <div class="grid grid-cols-1 gap-3">
                                @foreach($expiredStocks as $stock)
                                <div class="p-5 rounded-xl bg-white border border-red-100 shadow-sm flex items-center justify-between group hover:border-red-400 transition-all">
                                    <div class="flex items-center gap-4">
                                        @can('update stok bahan baku')
                                        <input type="checkbox" class="expired-checkbox w-5 h-5 rounded border-red-200 text-red-600 focus:ring-red-500" value="{{ $stock['id'] }}">
                                        @endcan
                                        <div>
                                            <p class="text-sm font-black text-gray-900 tracking-tight leading-none uppercase">Batch #{{ $stock['batch_number'] ?: 'UNSET' }}</p>
                                            <div class="flex items-center gap-3 mt-2 text-[9px] font-bold uppercase tracking-widest">
                                                <span class="text-gray-400">{{ number_format($stock['quantity'], 2) }} {{ $rawMaterial->unit->abbreviation }}</span>
                                                <span class="text-red-500 px-2 py-0.5 rounded bg-red-50 border border-red-100">Kadal. {{ $stock['expired_at']->format('d M Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-black text-red-400 uppercase tracking-widest opacity-60 group-hover:opacity-100 transition-opacity">{{ $stock['expired_at']->diffForHumans() }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- EXPIRING LIST --}}
                        @if(count($expiringStocks) > 0)
                        <div class="space-y-4">
                            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-yellow-600 flex items-center gap-3 px-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-yellow-500"></div>
                                Mendekati Batas
                            </h3>
                            <div class="grid grid-cols-1 gap-3">
                                @foreach($expiringStocks as $stock)
                                <div class="p-5 rounded-xl bg-white border border-yellow-100 shadow-sm flex items-center justify-between hover:border-yellow-400 transition-all">
                                    <div>
                                        <p class="text-sm font-black text-gray-900 tracking-tight leading-none uppercase">Batch #{{ $stock['batch_number'] ?: 'UNSET' }}</p>
                                        <div class="flex items-center gap-3 mt-2 text-[9px] font-bold uppercase tracking-widest">
                                            <span class="text-gray-400">{{ number_format($stock['quantity'], 2) }} {{ $rawMaterial->unit->abbreviation }}</span>
                                            <span class="text-yellow-600 px-2 py-0.5 rounded bg-yellow-50 border border-yellow-100">Exp: {{ $stock['expired_at']->format('d M Y') }}</span>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-black text-yellow-600 uppercase tracking-widest">{{ $stock['days_until_expiry'] }} Hari Lagi</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- VALID LIST --}}
                        @if(count($validStocks) > 0)
                        <div class="space-y-4">
                            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-emerald-600 flex items-center gap-3 px-2">
                                <i class="fas fa-check-double text-[10px]"></i>
                                Batch Persediaan Aman
                            </h3>
                            <div class="grid grid-cols-1 gap-3 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                                @foreach($validStocks as $stock)
                                <div class="p-5 rounded-xl bg-white border border-gray-200 shadow-sm flex items-center justify-between hover:border-cuan-green hover:shadow-md transition-all">
                                    <div>
                                        <p class="text-sm font-black text-gray-900 tracking-tight leading-none uppercase">Batch #{{ $stock['batch_number'] ?: 'UMUM' }}</p>
                                        <div class="flex items-center gap-3 mt-2 text-[9px] font-bold uppercase tracking-widest text-gray-400">
                                            <span>Qty: {{ number_format($stock['quantity'], 2) }} {{ $rawMaterial->unit->abbreviation }}</span>
                                            @if($stock['expired_at'])
                                                <span>• Kadal. {{ $stock['expired_at']->format('d M Y') }}</span>
                                            @else
                                                <span class="italic text-gray-300">Tanpa Kadaluarsa</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($stock['expired_at'])
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $stock['days_until_expiry'] }} Hari</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if(count($expiredStocks) == 0 && count($expiringStocks) == 0 && count($validStocks) == 0)
                        <div class="py-20 text-center flex flex-col items-center gap-4">
                            <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center border border-gray-100 text-gray-200">
                                <i class="fas fa-box-open text-2xl"></i>
                            </div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Data Batch Persediaan Kosong</p>
                        </div>
                        @endif
                    </div>
                </x-card-container>
            </div>

            {{-- SIDEBAR STATS --}}
            <div class="lg:col-span-1 space-y-6">
                
                <section class="bg-gray-900 border border-gray-800 rounded-xl p-8 text-white shadow-2xl relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-[9px] font-black uppercase tracking-[0.2em] opacity-40">Valuasi Persediaan</p>
                        <p class="text-3xl font-black mt-3 tracking-tighter flex items-baseline gap-2">
                            <span class="text-[10px] opacity-40">RP</span>
                            {{ number_format($currentStock * $rawMaterial->purchase_price, 0, ',', '.') }}
                        </p>
                        <div class="mt-8 pt-8 border-t border-white/5 space-y-3">
                            <div class="flex justify-between text-[10px] font-black uppercase tracking-widest opacity-60">
                                <span>Unit</span>
                                <span>{{ $rawMaterial->unit->name }}</span>
                            </div>
                            <div class="flex justify-between text-[10px] font-black uppercase tracking-widest opacity-60">
                                <span>Kategori</span>
                                <span class="text-cuan-green">{{ $rawMaterial->category->name ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="absolute -right-4 -bottom-4 opacity-5 rotate-12">
                        <i class="fas fa-chart-pie text-8xl"></i>
                    </div>
                </section>

                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-6">Distribusi Batas Waktu</h3>
                    <div class="space-y-6">
                        @php $totalBatches = max(count($validStocks) + count($expiringStocks) + count($expiredStocks), 1); @endphp
                        
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest">
                                <span class="text-gray-700">Valid</span>
                                <span class="text-emerald-500">{{ number_format((count($validStocks) / $totalBatches) * 100, 0) }}%</span>
                            </div>
                            <div class="w-full bg-gray-50 rounded-full h-1.5 border border-gray-100 overflow-hidden">
                                <div class="bg-emerald-500 h-full rounded-full" style="width: {{ (count($validStocks) / $totalBatches) * 100 }}%"></div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest">
                                <span class="text-gray-700">Warning</span>
                                <span class="text-yellow-500">{{ number_format((count($expiringStocks) / $totalBatches) * 100, 0) }}%</span>
                            </div>
                            <div class="w-full bg-gray-50 rounded-full h-1.5 border border-gray-100 overflow-hidden">
                                <div class="bg-yellow-500 h-full rounded-full" style="width: {{ (count($expiringStocks) / $totalBatches) * 100 }}%"></div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest">
                                <span class="text-gray-700">Kritis</span>
                                <span class="text-red-500">{{ number_format((count($expiredStocks) / $totalBatches) * 100, 0) }}%</span>
                            </div>
                            <div class="w-full bg-gray-50 rounded-full h-1.5 border border-gray-100 overflow-hidden">
                                <div class="bg-red-500 h-full rounded-full" style="width: {{ (count($expiredStocks) / $totalBatches) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                @can('lihat riwayat stok bahan baku')
                <a href="{{ route('raw-materials.stock-history', $rawMaterial) }}" 
                   class="flex items-center justify-between w-full p-5 rounded-xl bg-white border border-gray-200 text-gray-500 hover:text-cuan-green hover:border-cuan-green transition-all shadow-sm group">
                    <span class="text-[10px] font-black uppercase tracking-widest">Riwayat Lengkap</span>
                    <i class="fas fa-history text-xs opacity-50 group-hover:opacity-100 transition-opacity"></i>
                </a>
                @endcan
            </div>
        </div>
    </div>
</main>

{{-- MODAL KONFIRMASI --}}
<div id="removeExpiredModal" class="hidden fixed inset-0 bg-gray-900/60 z-50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="p-8 text-center">
            <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-6 text-red-500 border border-red-100 shadow-sm">
                <i class="fas fa-trash-alt text-2xl"></i>
            </div>
            <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight leading-tight">Buang Stok Kadaluarsa?</h3>
            <p class="mt-3 text-xs font-bold text-gray-400 uppercase tracking-widest leading-relaxed">
                Penghapusan batch ke-<span id="selectedCountText" class="text-red-500">0</span> akan dicatat sebagai penyesuaian stok keluar permanen.
            </p>
        </div>
        
        <form action="{{ route('raw-materials.remove-expired', $rawMaterial) }}" method="POST" id="removeExpiredForm">
            @csrf
            <div class="p-8 pt-0 flex gap-3">
                <button type="button" onclick="closeRemoveExpiredModal()" 
                    class="flex-1 py-4 text-[10px] font-black uppercase text-gray-400 tracking-widest hover:bg-gray-50 rounded-xl transition-all">BATAL</button>
                <button type="submit" 
                    class="flex-1 py-4 bg-red-600 text-[10px] font-black uppercase text-white tracking-widest rounded-xl hover:bg-red-700 transition-all shadow-lg shadow-red-200 active:scale-95">BUANG DATA</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #F3F4F6; border-radius: 10px; }
</style>
@endpush

@push('scripts')
<script>
    function updateSelectedCount() {
        const count = document.querySelectorAll('.expired-checkbox:checked').length;
        const countText = document.getElementById('selectedCountText');
        if(countText) countText.textContent = count;
    }

    document.querySelectorAll('.expired-checkbox').forEach(cb => {
        cb.addEventListener('change', updateSelectedCount);
    });

    function openRemoveExpiredModal() {
        const checkboxes = document.querySelectorAll('.expired-checkbox:checked');
        if (checkboxes.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Ops!',
                text: 'Centang setidaknya satu batch kadaluarsa.',
                confirmButtonColor: '#111827',
                customClass: { popup: 'rounded-xl', title: 'font-black uppercase tracking-tight' }
            });
            return;
        }
        
        const form = document.getElementById('removeExpiredForm');
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
    }

    function closeRemoveExpiredModal() {
        document.getElementById('removeExpiredModal').classList.add('hidden');
    }

    document.getElementById('removeExpiredModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeRemoveExpiredModal();
    });
</script>
@endpush

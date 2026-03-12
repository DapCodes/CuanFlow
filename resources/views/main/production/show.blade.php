@extends('layouts.app')

@section('title', 'Detail Produksi - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('production.index') }}" class="text-gray-500 hover:text-cuan-green transition-colors font-medium">Produksi</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Detail Batch</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER (Strictly matched employees/show.blade.php) --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="flex items-center gap-6">
                @if($production->product->image)
                    <img src="{{ Storage::url($production->product->image) }}" alt="{{ $production->product->name }}"
                         class="w-20 h-20 rounded-[2rem] object-cover border-4 border-white shadow-xl shadow-gray-200/50">
                @else
                    <div class="w-20 h-20 rounded-[2rem] bg-gradient-to-br from-cuan-green to-cuan-dark flex items-center justify-center border-4 border-white shadow-xl shadow-cuan-green/20">
                         <span class="text-white font-black text-2xl">
                            {{ strtoupper(substr($production->product->name, 0, 2)) }}
                        </span>
                    </div>
                @endif
                <div>
                    <h1 class="text-2xl md:text-3xl font-black text-gray-900 tracking-tight">Batch #{{ $production->batch_number }}</h1>
                    <div class="flex items-center gap-3 mt-2">
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{ $production->product->name }}</span>
                        @php
                            $statusClasses = [
                                'planned' => 'bg-gray-100 text-gray-500 border-gray-200',
                                'in_progress' => 'bg-blue-50 text-blue-600 border-blue-100',
                                'completed' => 'bg-cuan-green/10 text-cuan-green border-cuan-green/20'
                            ];
                            $statusLabels = ['planned' => 'Direncanakan', 'in_progress' => 'Sedang Proses', 'completed' => 'Selesai'];
                            $currentStatus = $production->status;
                            if ($production->is_disposed) {
                                $currentStatus = 'disposed';
                                $statusClasses['disposed'] = 'bg-amber-50 text-amber-600 border-amber-100';
                                $statusLabels['disposed'] = 'Dibuang/Disposed';
                            }
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $statusClasses[$currentStatus] ?? 'bg-gray-100 text-gray-400 border-gray-200' }} border">
                            {{ $statusLabels[$currentStatus] ?? $production->status }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('production.index') }}"
                   class="px-5 py-3 border border-gray-200 bg-white text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-50 transition-all active:scale-95 shadow-sm">
                    Kembali
                </a>
                
                @if($production->status === 'planned')
                <form action="{{ route('production.start', $production->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-5 py-3 bg-cuan-green text-white rounded-xl font-black text-sm hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                        Mulai Masak
                    </button>
                </form>
                @elseif($production->status === 'in_progress')
                 <button type="button" onclick="openCompleteModal()" 
                    class="px-5 py-3 bg-cuan-green text-white rounded-xl font-black text-sm hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                    Selesaikan Produksi
                </button>
                @endif
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            {{-- KOLOM KIRI (Info Utama) --}}
            <div class="lg:col-span-8 space-y-6">
                
                {{-- Detail Produksi --}}
                <x-card-container>
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Informasi Produksi</h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Status dan detail kuantitas</p>
                    </div>
                    <div class="px-8 py-8 grid grid-cols-1 sm:grid-cols-2 gap-8">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center border border-gray-100">
                                <i class="fas fa-calendar-alt text-cuan-green text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Direncanakan</p>
                                <p class="text-sm font-bold text-gray-900">{{ $production->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center border border-gray-100">
                                <i class="fas fa-boxes text-blue-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Rencana Kuantitas</p>
                                <p class="text-sm font-bold text-gray-900">
                                    {{ number_format($production->planned_quantity, 2) }} 
                                    <span class="text-[10px] font-bold text-gray-400 uppercase ml-0.5">{{ $production->product->unit->name }}</span>
                                </p>
                            </div>
                        </div>

                         <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center border border-gray-100">
                                <i class="fas fa-user-edit text-amber-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Oleh</p>
                                <p class="text-sm font-bold text-gray-900">{{ $production->creator->name ?? '-' }}</p>
                            </div>
                        </div>

                        @if($production->status === 'completed')
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center border border-gray-100">
                                <i class="fas fa-check-circle text-cuan-green text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Hasil Produksi (Net)</p>
                                <p class="text-sm font-bold text-gray-900">
                                    {{ number_format($production->actual_quantity - $production->waste_quantity, 2) }} 
                                    <span class="text-[10px] font-bold text-gray-400 uppercase ml-0.5">{{ $production->product->unit->name }}</span>
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </x-card-container>

                {{-- Material Usage Table --}}
                <x-card-container>
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Bahan Baku Digunakan</h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Detail konsumsi inventaris dapur</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest border-b border-gray-100 text-center">
                                <tr>
                                    <th class="px-8 py-4 text-left">Bahan Baku</th>
                                    <th class="px-8 py-4 text-right">Qty Pokok</th>
                                    <th class="px-8 py-4 text-right">Harga Unit</th>
                                    <th class="px-8 py-4 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 bg-white">
                                @forelse($production->items as $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-8 py-5">
                                        <div class="text-sm font-black text-gray-900 leading-none">{{ $item->rawMaterial->name }}</div>
                                        <div class="text-[9px] font-black uppercase text-gray-300 font-mono tracking-tighter mt-1.5">UNIT: {{ $item->rawMaterial->unit->name }}</div>
                                    </td>
                                    <td class="px-8 py-5 text-right whitespace-nowrap">
                                        <span class="text-sm font-black text-gray-900">{{ number_format($item->actual_quantity ?? $item->planned_quantity, 2) }}</span>
                                    </td>
                                    <td class="px-8 py-5 text-right text-gray-500 font-bold">
                                        Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-8 py-5 text-right font-black text-gray-900">
                                        Rp {{ number_format($item->total_price, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-10 text-center text-gray-400 font-bold uppercase tracking-widest text-[9px]">Tanpa penggunaan bahan baku (Non-Recipe).</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-card-container>

                {{-- Status Selesai Detail --}}
                @if($production->status === 'completed')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-card-container>
                        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                            <h2 class="text-base font-black text-gray-900 uppercase tracking-widest text-red-500">Waste & Kerugian</h2>
                        </div>
                        <div class="px-8 py-8 space-y-6">
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Jumlah Terbuang (Waste)</span>
                                <span class="text-sm font-black text-red-600">{{ number_format($production->waste_quantity, 2) }} {{ $production->product->unit->name }}</span>
                            </div>
                            <div class="p-4 bg-red-50 border border-red-100 rounded-2xl">
                                <p class="text-[9px] font-black text-red-700 uppercase tracking-widest mb-1">Alasan / Catatan</p>
                                <p class="text-xs font-bold text-red-600 leading-relaxed italic">{{ $production->notes ?: 'Tidak ada catatan.' }}</p>
                            </div>
                        </div>
                    </x-card-container>

                    <x-card-container>
                         <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                            <h2 class="text-base font-black text-gray-900 uppercase tracking-widest text-blue-500">Masa Laku</h2>
                        </div>
                         <div class="px-8 py-8 space-y-6 text-center">
                             @if($production->expiry_date)
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tanggal Kadaluarsa</p>
                                <h3 class="text-xl font-black text-gray-900">{{ $production->expiry_date->format('d M Y') }}</h3>
                                @php $isExp = $production->expiry_date->isPast(); @endphp
                                <div class="mt-4 px-4 py-2 rounded-xl {{ $isExp ? 'bg-red-50 text-red-500' : 'bg-blue-50 text-blue-500' }} border border-transparent font-black text-[9px] uppercase tracking-widest inline-block">
                                   {{ $isExp ? 'KADALUARSA' : $production->expiry_date->diffForHumans() }}
                                </div>
                             @else
                                <div class="py-10 flex flex-col items-center justify-center opacity-40">
                                    <i class="fas fa-infinity text-3xl text-gray-300 mb-2"></i>
                                    <span class="text-[10px] font-black uppercase text-gray-400">Tanpa Batas Kadaluarsa</span>
                                </div>
                             @endif
                         </div>
                    </x-card-container>
                </div>
                @endif
            </div>

            {{-- KOLOM KANAN (Aksi & Summary) --}}
            <div class="lg:col-span-4 space-y-6">
                
                {{-- SUMMARY CARD (Replacing flashy one) --}}
                <x-card-container>
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Ringkasan Biaya</h2>
                    </div>
                    <div class="px-8 py-8 space-y-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                <span>Total Biaya Bahan</span>
                                <span class="text-gray-900">Rp {{ number_format($production->items->sum('total_price'), 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs font-black text-gray-900 uppercase tracking-widest pt-4 border-t border-gray-100">
                                <span>Estimasi HPP / Unit</span>
                                @php
                                    $netActual = $production->actual_quantity - $production->waste_quantity;
                                    $costPerUnit = $netActual > 0 ? ($production->items->sum('total_price') / $netActual) : 0;
                                @endphp
                                <span class="text-cuan-green text-lg tracking-tighter">Rp {{ number_format($costPerUnit, 2, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="p-5 bg-blue-50 border border-blue-100 rounded-3xl">
                            <p class="text-[9px] font-bold text-blue-600 leading-relaxed italic">
                                * HPP ini dihitung secara realtime berdasarkan penggunaan bahan baku aktual dibagi dengan hasil bersih produksi.
                            </p>
                        </div>
                    </div>
                </x-card-container>

                @if($production->status !== 'completed' && $production->status !== 'cancelled')
                <x-card-container>
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest text-amber-600">Admin Actions</h2>
                    </div>
                    <div class="px-8 py-8 flex flex-col gap-3">
                         <button type="button" onclick="confirmCancel()" class="w-full px-5 py-4 border border-red-200 text-red-500 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-red-50 transition-all active:scale-95 shadow-sm">
                            <i class="fas fa-times-circle mr-2"></i>Batalkan Batch
                        </button>
                    </div>
                </x-card-container>
                @endif
                
                @if($production->status === 'completed' && !$production->is_disposed)
                <x-card-container>
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest text-amber-600">Disposal</h2>
                    </div>
                    <div class="px-8 py-8">
                         @can('buang stok produksi')
                         <button type="button" onclick="openDisposeModal()" class="w-full px-5 py-4 bg-amber-50 text-amber-600 border border-amber-100 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-amber-100 transition-all active:scale-95">
                            <i class="fas fa-trash-alt mr-2"></i>Buang Sisa Stok
                        </button>
                        @endcan
                    </div>
                </x-card-container>
                @endif
            </div>

        </div>

    </div>
</main>

{{-- MODALS --}}
@if($production->status === 'in_progress')
<div id="modal-complete-production" class="fixed inset-0 z-50 overflow-y-auto hidden" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeCompleteModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-100 animate-fade-in">
            <form action="{{ route('production.complete', $production->id) }}" method="POST">
                @csrf
                <div class="px-10 pt-10 pb-6 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-black text-gray-900 tracking-tight">Selesaikan Produksi</h3>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-2">Input hasil akhir timbangan/jumlah aktual</p>
                    </div>
                    <button type="button" onclick="closeCompleteModal()" class="w-10 h-10 rounded-2xl bg-white hover:bg-gray-100 flex items-center justify-center text-gray-400">
                         <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                
                <div class="p-10 space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400">Jumlah Aktual (Bruto)</label>
                            <div class="relative">
                                <input type="number" name="actual_quantity" step="0.01" required value="{{ $production->planned_quantity }}"
                                    class="w-full px-6 py-5 bg-gray-50 border border-gray-200 rounded-3xl text-xl font-black text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all">
                                <span class="absolute right-6 top-1/2 -translate-y-1/2 text-[10px] font-black uppercase text-gray-400">{{ $production->product->unit->name }}</span>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400">Jumlah Waste (Rusak)</label>
                            <div class="relative">
                                <input type="number" name="waste_quantity" step="0.01" required value="0"
                                    class="w-full px-6 py-5 bg-gray-50 border border-gray-200 rounded-3xl text-xl font-black text-red-500 focus:ring-4 focus:ring-red-500/10 focus:border-red-500 transition-all">
                                <span class="absolute right-6 top-1/2 -translate-y-1/2 text-[10px] font-black uppercase text-gray-400">{{ $production->product->unit->name }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400">Catatan/Alasan Waste</label>
                        <textarea name="notes" rows="3" class="w-full px-6 py-5 bg-gray-50 border border-gray-200 rounded-3xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all" placeholder="Ada keterangan tambahan?"></textarea>
                    </div>
                </div>

                <div class="px-10 pb-10 flex gap-4">
                    <button type="submit" class="flex-1 bg-cuan-green hover:bg-cuan-dark text-white rounded-[1.5rem] px-6 py-5 text-sm font-black uppercase tracking-widest transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                        Konfirmasi & Selesai
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    function openCompleteModal() {
        document.getElementById('modal-complete-production').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeCompleteModal() {
        document.getElementById('modal-complete-production').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function confirmCancel() {
        Swal.fire({
            title: 'Batalkan Batch?',
            text: "Status akan menjadi dibatalkan dan rencana pemotongan stok bahan baku (jika ada) akan dihapus.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Tutup',
            reverseButtons: true,
            customClass: { popup: 'rounded-[1.5rem]', confirmButton: 'rounded-xl', cancelButton: 'rounded-xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                const f = document.createElement('form');
                f.method = 'POST'; f.action = '{{ route("production.cancel", $production->id) }}';
                const s = document.createElement('input'); 
                s.type = 'hidden'; s.name = '_token'; s.value = '{{ csrf_token() }}';
                f.appendChild(s); document.body.appendChild(f); f.submit();
            }
        });
    }

    function openDisposeModal() {
        @if($production->status === 'completed')
        Swal.fire({
            title: 'Dispose Sisa Stok?',
            html: `
                <div class="text-left mt-4 space-y-4">
                    <p class="text-xs font-bold text-gray-500 leading-relaxed uppercase tracking-widest">Aksi ini akan membuang sisa stok batch ini dari inventaris.</p>
                    <input type="number" id="swal-dispose-qty" class="swal2-input !rounded-[1.5rem] !bg-gray-50 !border-gray-200" placeholder="Kuantitas yang dibuang..." value="{{ $production->actual_quantity - $production->waste_quantity }}">
                    <textarea id="swal-dispose-reason" class="swal2-textarea !rounded-[1.5rem] !bg-gray-50 !border-gray-200" placeholder="Alasan pembuangan (Wajib)..."></textarea>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Konfirmasi Buang',
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#9ca3af',
            reverseButtons: true,
            customClass: { popup: 'rounded-[1.5rem]' },
            preConfirm: () => {
                const qty = document.getElementById('swal-dispose-qty').value;
                const reason = document.getElementById('swal-dispose-reason').value;
                if (!qty || qty <= 0 || !reason) { Swal.showValidationMessage('Mohon isi kuantitas dan alasan!'); return false; }
                return { qty, reason };
            }
        }).then((res) => {
            if (res.isConfirmed) {
                const f = document.createElement('form');
                f.method = 'POST'; f.action = '{{ url("production/dispose/" . $production->id) }}';
                f.innerHTML = `@csrf @method('PUT')
                    <input type="hidden" name="quantity" value="${res.value.qty}">
                    <input type="hidden" name="reason" value="${res.value.reason}">`;
                document.body.appendChild(f); f.submit();
            }
        });
        @endif
    }
</script>
<style>
     @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fadeInUp 0.4s ease-out; }
</style>
@endpush
@endsection
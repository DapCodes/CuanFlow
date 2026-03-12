@extends('layouts.app')

@section('title', 'Detail Produksi - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('production.index') }}" class="text-gray-500 hover:text-cuan-green transition-colors font-medium">Produksi</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-black tracking-tight">Detail Batch</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-5xl mx-auto space-y-8">
        
        {{-- HEADER --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="flex items-center gap-6">
                 <div class="hidden sm:flex w-14 h-14 rounded-2xl bg-white border border-gray-100 items-center justify-center text-gray-400 shadow-sm">
                    <i class="fas fa-boxes-stacked text-xl"></i>
                </div>
                <div>
                     <h1 class="text-xl md:text-2xl font-black text-gray-900 tracking-tight">
                        {{ $production->product->name }}
                        <span class="ml-2 px-3 py-1 bg-gray-100 text-gray-500 rounded-xl text-[10px] font-black uppercase tracking-widest border border-gray-200 align-middle">
                            {{ $production->batch_number }}
                        </span>
                    </h1>
                    <div class="flex items-center gap-4 mt-1.5">
                        <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">{{ $production->created_at->format('d M Y, H:i') }}</span>
                        <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                        <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">Oleh {{ $production->creator->name ?? 'Sistem' }}</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                @if($production->status == 'planned' || $production->status == 'in_progress')
                    @can('selesai produksi')
                    <button type="button" onclick="confirmComplete()"
                            class="inline-flex items-center gap-2 rounded-2xl bg-cuan-green px-6 py-4 text-[10px] font-black uppercase tracking-widest text-white hover:bg-cuan-dark transition-all active:scale-95 shadow-lg shadow-cuan-green/20">
                        Selesaikan Produksi
                    </button>
                    @endcan
                    @can('batal produksi')
                    <button type="button" onclick="confirmCancel()"
                            class="inline-flex items-center gap-2 rounded-2xl bg-white border border-red-100 px-6 py-4 text-[10px] font-black uppercase tracking-widest text-red-600 hover:bg-red-50 transition-all active:scale-95 shadow-sm">
                        Batalkan
                    </button>
                    @endcan
                @endif
            </div>
        </section>

        {{-- STATUS & INFO CARDS --}}
        <section class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <x-card-container class="p-6 col-span-1 md:col-span-1 flex flex-col justify-center items-center text-center space-y-4">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Status Saat Ini</p>
                @php
                    $statusConfig = [
                        'planned' => ['class' => 'bg-gray-100 text-gray-500 border-gray-200', 'text' => 'DIRENCANAKAN'],
                        'in_progress' => ['class' => 'bg-blue-50 text-blue-600 border-blue-200', 'text' => 'DIPROSES'],
                        'completed' => ['class' => 'bg-cuan-green/10 text-cuan-green border-cuan-green/20', 'text' => 'SELESAI'],
                        'cancelled' => ['class' => 'bg-red-50 text-red-600 border-red-200', 'text' => 'DIBATALKAN'],
                    ];
                    $config = $statusConfig[$production->status] ?? $statusConfig['planned'];
                    if ($production->is_disposed) {
                        $config = ['class' => 'bg-amber-50 text-amber-600 border-amber-200', 'text' => 'DIBUANG'];
                    }
                @endphp
                <span class="inline-flex items-center px-4 py-2 rounded-full text-xs font-black uppercase tracking-widest border-2 {{ $config['class'] }}">
                    {{ $config['text'] }}
                </span>
                <p class="text-[9px] font-bold text-gray-300 italic">Terakhir diupdate {{ $production->updated_at->diffForHumans() }}</p>
            </x-card-container>

            <x-card-container class="p-6 col-span-1 md:col-span-3">
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-8">
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Informasi Produk</p>
                        <h4 class="text-sm font-black text-gray-900 truncate">{{ $production->product->name }}</h4>
                        <p class="text-[10px] font-bold text-gray-400 uppercase mt-1">{{ $production->product->code }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Kuantitas Output</p>
                        <h4 class="text-sm font-black text-gray-900">
                             {{ number_format($production->actual_quantity ?? $production->planned_quantity, 2) }} 
                             <span class="text-[10px] text-gray-400 uppercase ml-1">{{ $production->product->unit->name }}</span>
                        </h4>
                        @if($production->planned_quantity != $production->actual_quantity && $production->actual_quantity)
                        <p class="text-[9px] font-bold text-amber-600 uppercase mt-1 italic">Target: {{ number_format($production->planned_quantity, 2) }}</p>
                        @endif
                    </div>
                    <div class="hidden lg:block">
                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Catatan Produksi</p>
                        <p class="text-[10px] font-bold text-gray-600 italic leading-relaxed">{{ $production->notes ?? '-- tidak ada catatan --' }}</p>
                    </div>
                </div>
            </x-card-container>
        </section>

        {{-- MAIN DETAILS --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- LEFT: BATCH DETAILS & MATERIALS --}}
            <div class="lg:col-span-2 space-y-8">
                 <x-card-container>
                    <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Kebutuhan Bahan Baku</h3>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[9px] font-black bg-gray-50 text-gray-400 border border-gray-100 uppercase tracking-widest">
                            {{ $production->materialUsages->count() }} Bahan
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest border-b border-gray-100">
                                <tr>
                                    <th class="px-8 py-4 text-left">Item</th>
                                    <th class="px-8 py-4 text-right">Kuantitas</th>
                                    @can('lihat hpp')
                                    <th class="px-8 py-4 text-right">Total Biaya</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 bg-white">
                                @forelse($production->materialUsages as $usage)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-8 py-5">
                                        <div class="text-sm font-black text-gray-900">{{ $usage->rawMaterial->name }}</div>
                                        <div class="text-[9px] font-black uppercase text-gray-300 font-mono tracking-tighter mt-1">#{{ $usage->rawMaterial->code }}</div>
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <span class="text-sm font-black text-gray-700">{{ number_format($usage->quantity, 2) }}</span>
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">{{ $usage->rawMaterial->unit->name }}</span>
                                    </td>
                                    @can('lihat hpp')
                                    <td class="px-8 py-5 text-right font-black text-gray-900">
                                        Rp {{ number_format($usage->total_cost, 0, ',', '.') }}
                                    </td>
                                    @endcan
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-8 py-10 text-center text-gray-400 font-bold uppercase tracking-widest text-[9px]">
                                        Tidak ada data bahan baku digunakan.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-card-container>

                @if($production->status == 'completed' && !$production->is_disposed)
                <x-card-container class="p-8 border-l-4 border-l-cuan-green space-y-6">
                    <div class="flex items-center justify-between">
                         <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Ringkasan Selesai</h3>
                         <span class="text-[10px] font-black uppercase tracking-widest text-cuan-green">{{ $production->updated_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Total Output</p>
                             <h4 class="text-lg font-black text-gray-900">
                                {{ number_format($production->actual_quantity, 2) }} 
                                <span class="text-xs text-gray-400 uppercase ml-1">{{ $production->product->unit->name }}</span>
                            </h4>
                        </div>
                         <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Kualitas Batch</p>
                            <span class="inline-flex items-center px-3 py-1 bg-cuan-green/10 text-cuan-green rounded-xl text-[10px] font-black uppercase tracking-widest border border-cuan-green/20">
                                TERVERIFIKASI
                            </span>
                        </div>
                    </div>
                </x-card-container>
                @endif
            </div>

            {{-- RIGHT: SUMMARY & ACTIONS --}}
            <div class="space-y-8">
                {{-- HP SUMMRY --}}
                @can('lihat hpp')
                <x-card-container class="bg-gray-900 text-white p-8 space-y-6 overflow-hidden relative">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-cuan-green/10 rounded-full blur-2xl"></div>
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-500 relative z-10">Ringkasan Biaya</h3>
                    <div class="space-y-4 relative z-10">
                        <div class="flex items-center justify-between border-b border-gray-800 pb-4">
                            <span class="text-xs font-bold text-gray-400">Total Biaya Bahan</span>
                            <span class="text-sm font-black">Rp {{ number_format($production->total_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-2">
                             <div class="space-y-1">
                                <span class="text-[9px] font-black uppercase tracking-widest text-gray-500">HPP / Unit</span>
                                <h4 class="text-xl font-black text-cuan-green">
                                    Rp {{ number_format($production->actual_quantity && $production->actual_quantity > 0 ? $production->total_cost / $production->actual_quantity : ($production->planned_quantity > 0 ? $production->total_cost / $production->planned_quantity : 0), 0, ',', '.') }}
                                </h4>
                             </div>
                             <div class="h-10 w-10 bg-gray-800 rounded-xl flex items-center justify-center text-gray-400">
                                <i class="fas fa-tag text-xs"></i>
                             </div>
                        </div>
                    </div>
                </x-card-container>
                @endcan

                {{-- EXPIRATION / DISPOSAL --}}
                @if($production->status == 'completed' && !$production->is_disposed)
                <x-card-container class="p-8 space-y-6 border-l-4 border-l-amber-400">
                     <div class="flex items-center justify-between">
                         <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900 leading-none">Kadaluarsa Batch</h3>
                         <div class="h-8 w-8 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500">
                             <i class="fas fa-calendar-times text-xs"></i>
                         </div>
                    </div>

                    @php
                        $isExpired = $production->expiry_date && $production->expiry_date->isPast();
                        $expiryDays = $production->expiry_date ? $production->expiry_date->diffInDays(now(), false) : null;
                    @endphp

                    @if($production->expiry_date)
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-gray-400">Tgl. Kadaluarsa</span>
                            <span class="text-xs font-black text-gray-900 uppercase tracking-widest">{{ $production->expiry_date->format('d M Y') }}</span>
                        </div>
                        @if($isExpired)
                        <div class="p-3 bg-red-50 text-red-600 rounded-xl border border-red-100 flex items-center justify-center gap-2 animate-pulse">
                            <i class="fas fa-exclamation-triangle text-xs"></i>
                            <span class="text-[9px] font-black uppercase tracking-widest">TELAH KADALUARSA</span>
                        </div>
                        @elseif($expiryDays !== null && $expiryDays <= 0 && $expiryDays > -3)
                        <div class="p-3 bg-amber-50 text-amber-600 rounded-xl border border-amber-100 flex items-center justify-center gap-2">
                            <i class="fas fa-history text-xs"></i>
                            <span class="text-[9px] font-black uppercase tracking-widest">Segera Kadaluarsa ({{ abs($expiryDays) }} hari)</span>
                        </div>
                        @endif
                    </div>
                    @else
                    <p class="text-[10px] font-bold text-gray-400 italic">Tanggal kadaluarsa belum diset.</p>
                    @endif

                    @can('buang stok produksi')
                    <button type="button" onclick="confirmDispose()"
                        class="w-full px-5 py-4 bg-amber-100 text-amber-700 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-amber-200 transition-all active:scale-95 border border-amber-200 shadow-sm">
                        Buang Sisa Stok
                    </button>
                    @endcan
                </x-card-container>
                @endif
            </div>
        </div>

    </div>
</main>

{{-- MODAL COMPLETION --}}
<div id="modal-complete" class="fixed inset-0 z-50 overflow-y-auto hidden" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal('complete')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-100">
            <form action="{{ route('production.complete', $production->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="px-10 pt-10 pb-6 border-b border-gray-50 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-black text-gray-900 tracking-tight">Selesaikan Batch</h3>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-2">Update hasil produksi nyata untuk batch ini</p>
                    </div>
                    <button type="button" onclick="closeModal('complete')" class="w-10 h-10 rounded-2xl bg-gray-50 hover:bg-gray-100 flex items-center justify-center text-gray-400">
                         <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                
                <div class="p-10 space-y-8">
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400">Kuantitas Aktual</label>
                            <div class="relative">
                                <input type="number" name="actual_quantity" step="0.01" value="{{ $production->planned_quantity }}" required
                                    class="w-full px-6 py-5 bg-gray-50 border border-gray-200 rounded-3xl text-xl font-black text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all">
                                <span class="absolute right-6 top-1/2 -translate-y-1/2 text-[10px] font-black uppercase text-gray-400">{{ $production->product->unit->name }}</span>
                            </div>
                             <p class="text-[9px] font-bold text-gray-400 italic mt-1">Estimasi awal: {{ $production->planned_quantity }} {{ $production->product->unit->name }}</p>
                        </div>
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400">Tgl. Kadaluarsa</label>
                            <input type="date" name="expiry_date" 
                                value="{{ $production->product->shelf_life_days ? now()->addDays($production->product->shelf_life_days)->format('Y-m-d') : '' }}"
                                class="w-full px-6 py-5 bg-gray-50 border border-gray-200 rounded-3xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all">
                             <p class="text-[9px] font-bold text-gray-400 mt-1 uppercase tracking-tighter">Shelf life: {{ $production->product->shelf_life_days ?? '--' }} hari</p>
                        </div>
                    </div>
                    
                    <div class="p-6 bg-blue-50 border border-blue-100 rounded-3xl flex gap-4">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                        <p class="text-[10px] font-medium text-blue-700 leading-relaxed uppercase tracking-widest">
                            Menyelesaikan batch akan menambah stok produk jadi dan melakukan pemotongan stok bahan baku secara final.
                        </p>
                    </div>
                </div>

                <div class="px-10 pb-10 flex gap-4">
                     <button type="submit" class="flex-1 bg-cuan-green hover:bg-cuan-dark text-white rounded-[1.5rem] px-6 py-5 text-sm font-black uppercase tracking-widest transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                        Konfirmasi Selesai
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL DISPOSE --}}
<div id="modal-dispose" class="fixed inset-0 z-50 overflow-y-auto hidden" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal('dispose')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-100">
            <form action="{{ route('production.dispose', $production->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="px-10 pt-10 pb-6 border-b border-gray-50 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-black text-gray-900 tracking-tight">Buang Stok</h3>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-2">Hapus sisa stok dari batch ini dari inventaris</p>
                    </div>
                    <button type="button" onclick="closeModal('dispose')" class="w-10 h-10 rounded-2xl bg-gray-50 hover:bg-gray-100 flex items-center justify-center text-gray-400">
                         <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                
                <div class="p-10 space-y-6">
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400">Jumlah di-Dispose</label>
                        <div class="relative">
                            <input type="number" name="quantity" step="0.01" required
                                class="w-full px-6 py-5 bg-gray-50 border border-gray-200 rounded-3xl text-xl font-black text-gray-900 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all">
                            <span class="absolute right-6 top-1/2 -translate-y-1/2 text-[10px] font-black uppercase text-gray-400">{{ $production->product->unit->name }}</span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400">Alasan Pembuangan</label>
                        <textarea name="reason" rows="3" required
                            class="w-full px-6 py-5 bg-gray-50 border border-gray-200 rounded-3xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all"
                            placeholder="Contoh: Melewati masa kadaluarsa, rusak, terjatuh..."></textarea>
                    </div>
                </div>

                <div class="px-10 pb-10 flex gap-4">
                    <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white rounded-[1.5rem] px-6 py-5 text-sm font-black uppercase tracking-widest transition-all shadow-lg shadow-amber-500/20 active:scale-95">
                        Konfirmasi Buang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmComplete() {
        document.getElementById('modal-complete').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function confirmDispose() {
         document.getElementById('modal-dispose').classList.remove('hidden');
         document.body.classList.add('overflow-hidden');
    }

    function closeModal(type) {
        document.getElementById('modal-' + type).classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function confirmCancel() {
        Swal.fire({
            title: 'Batalkan Batch?',
            text: 'Membatalkan batch akan mengembalikan rencana pemotongan stok bahan baku.',
            icon: 'warning',
            iconColor: '#ef4444',
            showCancelButton: true,
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Kembali',
            customClass: {
                popup: 'rounded-[3rem] border-none shadow-2xl',
                confirmButton: 'rounded-xl px-6 py-3 font-black text-xs uppercase tracking-widest bg-red-600 text-white hover:bg-red-700 transition-all mx-2',
                cancelButton: 'rounded-xl px-6 py-3 font-black text-xs uppercase tracking-widest bg-gray-50 border border-gray-100 text-gray-400 hover:bg-gray-100 transition-all mx-2',
            },
            buttonsStyling: false,
            reverseButtons: true
        }).then((res) => {
            if(res.isConfirmed) {
                const form = document.createElement('form');
                form.action = '{{ route("production.cancel", $production->id) }}';
                form.method = 'POST';
                form.innerHTML = `@csrf @method('PUT')`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 3000,
                iconColor: '#658C58',
                customClass: { popup: 'rounded-[3rem]' }
            });
        @endif
    });
</script>
@endpush
@endsection
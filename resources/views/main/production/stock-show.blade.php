@extends('layouts.app')

@section('title', 'Detail Stok Produk - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('production.index') }}" class="text-gray-500 hover:text-cuan-green transition-colors font-medium">Produksi</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Detail Stok Produk</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">
        
        {{-- HEADER (Strictly matched employees/show.blade.php) --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="flex items-center gap-6">
                 @if($product->image)
                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" 
                        class="w-20 h-20 rounded-[2rem] object-cover border-4 border-white shadow-xl shadow-gray-200/50">
                @else
                    <div class="w-20 h-20 rounded-[2rem] bg-gradient-to-br from-cuan-green to-cuan-dark flex items-center justify-center border-4 border-white shadow-xl shadow-cuan-green/20">
                         <span class="text-white font-black text-2xl">
                            {{ strtoupper(substr($product->name, 0, 2)) }}
                        </span>
                    </div>
                @endif
                <div>
                     <h1 class="text-2xl md:text-3xl font-black text-gray-900 tracking-tight">{{ $product->name }}</h1>
                    <div class="flex items-center gap-3 mt-2">
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">#{{ $product->code }}</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-cuan-green/10 text-cuan-green border border-cuan-green/20">
                            Stok: {{ number_format($outletStock, 2) }} {{ $product->unit->name }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                 <a href="{{ route('production.index') }}"
                   class="px-5 py-3 border border-gray-200 bg-white text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-50 transition-all active:scale-95 shadow-sm">
                    Kembali
                </a>
                @can('buat produksi')
                 <a href="{{ route('production.create', ['product_id' => $product->id]) }}" 
                    class="px-5 py-3 bg-cuan-green text-white rounded-xl font-black text-sm hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                    Produksi Baru
                </a>
                @endcan
            </div>
        </section>

        {{-- RINGKASAN STATISTIK (Simple style like employees index) --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Stok Tersedia</p>
                <p class="mt-2 text-2xl font-black text-gray-900">{{ number_format($outletStock, 2) }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm border-l-4 border-l-amber-500">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Segera Kadaluarsa</p>
                <p class="mt-2 text-2xl font-black text-amber-600">{{ number_format($nearExpiryStock ?? 0, 2) }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm border-l-4 border-l-red-500">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Kadaluarsa (Expired)</p>
                <p class="mt-2 text-2xl font-black text-red-600">{{ number_format($expiredStock, 2) }}</p>
            </div>
             <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Produksi Terakhir</p>
                 @php
                    $lastProd = $productions->where('status', 'completed')->first();
                @endphp
                <p class="mt-2 text-sm font-black text-gray-900">{{ $lastProd ? $lastProd->updated_at->format('d M Y') : '--' }}</p>
                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">{{ $lastProd ? $lastProd->updated_at->diffForHumans() : '-' }}</p>
            </div>
        </section>

        {{-- BATCH TABLES --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- AKTIF BATCH --}}
            <x-card-container>
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                     <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Batch Stok Aktif</h2>
                     <span class="px-3 py-1 bg-white border border-gray-200 text-[10px] font-black uppercase tracking-widest text-gray-400 rounded-lg shadow-sm">
                         {{ $productions->where('status', 'completed')->where('is_disposed', false)->count() }} Batch
                     </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                         <thead class="bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest border-b border-gray-100">
                            <tr>
                                <th class="px-8 py-4 text-left">Batch #</th>
                                <th class="px-8 py-4 text-right">Stok</th>
                                <th class="px-8 py-4 text-center">Kadaluarsa</th>
                                <th class="px-8 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($productions->where('status', 'completed')->where('is_disposed', false) as $prod)
                                @php
                                    $isExpired = $prod->expired_at && $prod->expired_at->isPast();
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors {{ $isExpired ? 'bg-red-50/20' : '' }}">
                                    <td class="px-8 py-5">
                                        <div class="text-sm font-black text-gray-900 leading-none">{{ $prod->batch_number }}</div>
                                        <div class="text-[9px] font-black uppercase text-gray-300 tracking-tighter mt-1">{{ $prod->updated_at->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-8 py-5 text-right whitespace-nowrap">
                                        <span class="text-sm font-black text-gray-900">{{ number_format($prod->actual_quantity, 2) }}</span>
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">{{ $product->unit->name }}</span>
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                         @if($prod->expired_at)
                                            <span class="text-[10px] font-black uppercase tracking-widest {{ $isExpired ? 'text-red-500 animate-pulse' : 'text-gray-500' }}">
                                                {{ $prod->expired_at->format('d M Y') }}
                                            </span>
                                         @else
                                            <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest italic line-through">NONE</span>
                                         @endif
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                         <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('production.show', $prod->id) }}" 
                                                class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 hover:bg-gray-100 transition-all active:scale-95 shadow-sm">
                                                <i class="fas fa-eye text-xs"></i>
                                            </a>
                                            @can('buang stok produksi')
                                             <button type="button" onclick="confirmDispose('{{ $prod->id }}', '{{ $prod->batch_number }}')"
                                                class="w-10 h-10 flex items-center justify-center rounded-xl bg-amber-50 text-amber-500 hover:bg-amber-100 transition-all active:scale-95 border border-amber-100">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                            @endcan
                                         </div>
                                    </td>
                                </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-8 py-10 text-center text-gray-400 font-bold uppercase tracking-widest text-[9px]">
                                    Belum ada batch stok aktif.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card-container>

             {{-- BATCH RIWAYAT --}}
            <x-card-container>
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                     <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Riwayat Keluar & Disposal</h2>
                     <span class="px-3 py-1 bg-white border border-gray-200 text-[10px] font-black uppercase tracking-widest text-gray-400 rounded-lg shadow-sm">
                         LOG
                     </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                         <thead class="bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest border-b border-gray-100">
                            <tr>
                                <th class="px-8 py-4 text-left">Batch #</th>
                                <th class="px-8 py-4 text-right">Kuantitas</th>
                                <th class="px-8 py-4 text-center">Status</th>
                                <th class="px-8 py-4 text-center">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($productions->whereIn('status', ['cancelled'])->union($productions->where('is_disposed', true))->sortByDesc('updated_at')->take(10) as $prod)
                                <tr class="hover:bg-gray-50 transition-colors opacity-80">
                                    <td class="px-8 py-5">
                                        <div class="text-[11px] font-black text-gray-700">{{ $prod->batch_number }}</div>
                                    </td>
                                    <td class="px-8 py-5 text-right whitespace-nowrap">
                                        <span class="text-xs font-black text-gray-500">{{ number_format($prod->actual_quantity ?? $prod->planned_quantity, 2) }}</span>
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">{{ $product->unit->name }}</span>
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                         @if($prod->is_disposed)
                                         <span class="inline-flex items-center px-2 py-1 bg-amber-50 text-amber-600 rounded-lg text-[8px] font-black uppercase tracking-widest border border-amber-100">DISPOSED</span>
                                         @else
                                         <span class="inline-flex items-center px-2 py-1 bg-red-50 text-red-500 rounded-lg text-[8px] font-black uppercase tracking-widest border border-red-100">CANCELLED</span>
                                         @endif
                                    </td>
                                    <td class="px-8 py-5 text-center whitespace-nowrap">
                                        <span class="text-[10px] font-bold text-gray-400">{{ $prod->updated_at->format('d M, H:i') }}</span>
                                    </td>
                                </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-8 py-10 text-center text-gray-400 font-bold uppercase tracking-widest text-[9px]">
                                    Belum ada data riwayat.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card-container>
        </div>

    </div>
</main>

@can('buang stok produksi')
<div id="modal-dispose-stock" class="fixed inset-0 z-50 overflow-y-auto hidden" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeDisposeModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-100 animate-fade-in">
            <form id="dispose-form-dynamic" method="POST">
                @csrf @method('PUT')
                <div class="px-10 pt-10 pb-6 border-b border-gray-50 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-black text-gray-900 tracking-tight">Dispose Batch <span id="dispose-batch-label"></span></h3>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-2">Hapus sisa stok dari inventaris secara resmi</p>
                    </div>
                    <button type="button" onclick="closeDisposeModal()" class="w-10 h-10 rounded-2xl bg-gray-50 hover:bg-gray-100 flex items-center justify-center text-gray-400">
                         <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                
                <div class="p-10 space-y-8">
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400">Kuantitas di-Dispose</label>
                        <div class="relative">
                            <input type="number" name="quantity" id="dispose-qty-input" step="0.01" required
                                class="w-full px-6 py-5 bg-gray-50 border border-gray-200 rounded-3xl text-xl font-black text-gray-900 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all">
                            <span class="absolute right-6 top-1/2 -translate-y-1/2 text-[10px] font-black uppercase text-gray-400">{{ $product->unit->name }}</span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400">Alasan Pembuangan</label>
                        <textarea name="reason" rows="3" required
                            class="w-full px-6 py-5 bg-gray-50 border border-gray-200 rounded-3xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all"
                            placeholder="Wajib diisi..."></textarea>
                    </div>
                    <div class="p-5 bg-red-50 border border-red-100 rounded-[1.5rem] flex items-center gap-4">
                        <i class="fas fa-exclamation-triangle text-red-500"></i>
                        <p class="text-[9px] font-black uppercase text-red-700 tracking-widest leading-relaxed">
                            Aksi ini permanen dan akan diverifikasi oleh auditor sistem.
                        </p>
                    </div>
                </div>

                <div class="px-10 pb-10 flex gap-4">
                    <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white rounded-[1.5rem] px-6 py-5 text-sm font-black uppercase tracking-widest transition-all shadow-lg shadow-amber-500/20 active:scale-95">
                        Konfirmasi Disposal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@push('scripts')
<script>
    function confirmDispose(id, batch) {
        document.getElementById('dispose-batch-label').textContent = `#${batch}`;
        document.getElementById('dispose-form-dynamic').action = `{{ url('production/dispose') }}/${id}`;
        document.getElementById('modal-dispose-stock').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeDisposeModal() {
        document.getElementById('modal-dispose-stock').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
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
                customClass: { popup: 'rounded-[1.5rem]' }
            });
        @endif
    });
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
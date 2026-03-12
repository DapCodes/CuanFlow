@extends('layouts.app')

@section('title', 'Detail Stok - ' . $rawMaterial->name)

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('raw-materials.index') }}" class="text-gray-500 hover:text-cuan-green transition-colors">Bahan Baku</a>
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Status Persediaan Detil</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900 leading-tight">
                    Batch & Kondisi Stok
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Rincian batch kadaluarsa untuk <span class="text-cuan-green font-bold tracking-tight">{{ $rawMaterial->name }}</span>
                </p>
            </div>
            <div class="flex items-center gap-3">
                 <a href="{{ route('raw-materials.index') }}" 
                   class="inline-flex items-center gap-2 rounded-xl bg-white border border-gray-200 px-5 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all shadow-sm active:scale-95">
                    <span>Kembali</span>
                </a>
                @can('kelola stok bahan baku')
                <a href="{{ route('raw-materials.manage-stock', $rawMaterial) }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-5 py-3 text-sm font-black text-white hover:bg-black transition-all shadow-lg active:scale-95">
                    <i class="fas fa-boxes shadow-sm opacity-50"></i>
                    <span>Manage Mutasi</span>
                </a>
                @endcan
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2 space-y-6">

                {{-- INFO PRODUK RINGKAS --}}
                <div class="bg-white border border-gray-100 rounded-[2rem] p-8 shadow-sm grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div class="space-y-1">
                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Kode Bahan</p>
                        <p class="text-sm font-black text-gray-900 font-mono tracking-tighter">{{ $rawMaterial->code }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Total Fisik</p>
                        <p class="text-sm font-black text-emerald-600 tracking-tight">{{ number_format($currentStock, 2) }} {{ $rawMaterial->unit->abbreviation }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Batas Minimum</p>
                        <p class="text-sm font-black text-gray-900 tracking-tight">{{ number_format($rawMaterial->min_stock, 2) }} {{ $rawMaterial->unit->abbreviation }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Kategori</p>
                        <p class="text-sm font-black text-cuan-green">{{ $rawMaterial->category->name ?? 'N/A' }}</p>
                    </div>
                </div>

                <x-card-container title="Pemantauan Batch Kadaluarsa">
                    <div class="p-8 space-y-8">
                        {{-- Stats Row --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div class="p-6 rounded-[2rem] bg-red-50/50 border border-red-100 group">
                                <p class="text-[9px] font-black text-red-400 uppercase tracking-widest">Kadaluarsa</p>
                                <p class="text-3xl font-black text-red-600 mt-2 tracking-tighter">{{ $stats['expired_count'] }} <span class="text-[10px] opacity-50 font-bold uppercase ml-1">Batch</span></p>
                                <p class="text-[10px] font-black text-red-400 mt-2 uppercase tracking-widest">{{ number_format($stats['expired_quantity'], 2) }} {{ $rawMaterial->unit->abbreviation }}</p>
                            </div>

                            <div class="p-6 rounded-[2rem] bg-yellow-50/50 border border-yellow-100 group">
                                <p class="text-[9px] font-black text-yellow-500 uppercase tracking-widest">Segera Exp.</p>
                                <p class="text-3xl font-black text-yellow-600 mt-2 tracking-tighter">{{ $stats['expiring_count'] }} <span class="text-[10px] opacity-50 font-bold uppercase ml-1">Batch</span></p>
                                <p class="text-[10px] font-black text-yellow-500 mt-2 uppercase tracking-widest">{{ number_format($stats['expiring_quantity'], 2) }} {{ $rawMaterial->unit->abbreviation }}</p>
                            </div>

                            <div class="p-6 rounded-[2rem] bg-emerald-50/50 border border-emerald-100 group">
                                <p class="text-[9px] font-black text-emerald-500 uppercase tracking-widest">Kondisi Aman</p>
                                <p class="text-3xl font-black text-emerald-600 mt-2 tracking-tighter">{{ $stats['valid_count'] }} <span class="text-[10px] opacity-50 font-bold uppercase ml-1">Batch</span></p>
                                <p class="text-[10px] font-black text-emerald-500 mt-2 uppercase tracking-widest">{{ number_format($stats['valid_quantity'], 2) }} {{ $rawMaterial->unit->abbreviation }}</p>
                            </div>
                        </div>

                        {{-- BATCH LISTS --}}
                        <div class="space-y-8 mt-12">
                            {{-- EXPIRED LIST --}}
                            @if(count($expiredStocks) > 0)
                            <div class="space-y-4">
                                <div class="flex items-center justify-between px-2">
                                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-red-500 flex items-center gap-3">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Varian Kadaluarsa
                                    </h3>
                                    @can('update stok bahan baku')
                                    <button onclick="openRemoveExpiredModal()" class="text-[10px] font-black uppercase text-red-600 hover:underline">Hapus Otomatis Terpilih</button>
                                    @endcan
                                </div>
                                <div class="grid grid-cols-1 gap-3">
                                    @foreach($expiredStocks as $stock)
                                    <div class="p-6 rounded-[1.5rem] bg-white border border-red-100 shadow-sm flex items-center justify-between group hover:border-red-300 transition-all">
                                        <div class="flex items-center gap-6">
                                            @can('update stok bahan baku')
                                            <input type="checkbox" class="expired-checkbox w-5 h-5 rounded-lg border-red-200 text-red-600 focus:ring-red-500 cursor-pointer" value="{{ $stock['id'] }}">
                                            @endcan
                                            <div>
                                                <p class="text-sm font-black text-gray-900 tracking-tight">Batch #{{ $stock['batch_number'] ?: 'N/A' }}</p>
                                                <div class="flex items-center gap-4 mt-1 text-[10px] font-bold uppercase tracking-widest text-gray-400">
                                                    <span>{{ number_format($stock['quantity'], 2) }} {{ $rawMaterial->unit->abbreviation }}</span>
                                                    <span class="text-red-500 bg-red-50 px-2 py-0.5 rounded-lg border border-red-100 shadow-sm">Kadal. {{ $stock['expired_at']->format('d M Y') }}</span>
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
                                    <i class="fas fa-hourglass-start"></i>
                                    Mendekati Kadaluarsa
                                </h3>
                                <div class="grid grid-cols-1 gap-3">
                                    @foreach($expiringStocks as $stock)
                                    <div class="p-6 rounded-[1.5rem] bg-white border border-yellow-100 shadow-sm flex items-center justify-between hover:border-yellow-300 transition-all">
                                        <div>
                                            <p class="text-sm font-black text-gray-900 tracking-tight">Batch #{{ $stock['batch_number'] ?: 'N/A' }}</p>
                                            <div class="flex items-center gap-4 mt-1 text-[10px] font-bold uppercase tracking-widest text-gray-400">
                                                <span>{{ number_format($stock['quantity'], 2) }} {{ $rawMaterial->unit->abbreviation }}</span>
                                                <span class="text-yellow-600 bg-yellow-50 px-2 py-0.5 rounded-lg border border-yellow-100 shadow-sm">Kadal. {{ $stock['expired_at']->format('d M Y') }}</span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-[10px] font-black text-yellow-500 uppercase tracking-widest bg-yellow-400/10 px-3 py-1.5 rounded-xl border border-yellow-100">{{ $stock['days_until_expiry'] }} Hari Lagi</span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            {{-- VALID LIST --}}
                            @if(count($validStocks) > 0)
                            <div class="space-y-4">
                                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-emerald-600 flex items-center gap-3 px-2">
                                    <i class="fas fa-check-double"></i>
                                    Batch Masih Valid
                                </h3>
                                <div class="grid grid-cols-1 gap-3 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                                    @foreach($validStocks as $stock)
                                    <div class="p-6 rounded-[1.5rem] bg-white border border-gray-50 shadow-sm flex items-center justify-between hover:shadow-md transition-all">
                                        <div>
                                            <p class="text-sm font-black text-gray-900 tracking-tight">Batch #{{ $stock['batch_number'] ?: 'N/A' }}</p>
                                            <div class="flex items-center gap-4 mt-1 text-[10px] font-bold uppercase tracking-widest text-gray-400">
                                                <span>{{ number_format($stock['quantity'], 2) }} {{ $rawMaterial->unit->abbreviation }}</span>
                                                @if($stock['expired_at'])
                                                    <span>Tgl. Kadal. {{ $stock['expired_at']->format('d M Y') }}</span>
                                                @else
                                                    <span class="text-gray-300 tracking-tighter opacity-70 italic">Tanpa Tgl Kadaluarsa</span>
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
                                <i class="fas fa-box-open text-5xl text-gray-100"></i>
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-300">Belum ada catatan inventaris per batch</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </x-card-container>
            </div>

            <div class="lg:col-span-1 space-y-6">
                <x-card-container title="Kalkulasi Valuasi">
                    <div class="p-8 space-y-6">
                        <div class="p-6 rounded-[1.5rem] bg-gray-900 text-white shadow-xl relative overflow-hidden">
                            <div class="relative z-10">
                                <p class="text-[9px] font-black uppercase tracking-widest opacity-40">Nilai Inventaris Fisik</p>
                                <p class="text-3xl font-black mt-3 tracking-tighter">
                                    <span class="text-[10px] opacity-40 uppercase mr-1">RP</span> {{ number_format($currentStock * $rawMaterial->purchase_price, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="absolute -right-4 -bottom-4 opacity-5 rotate-12 scale-150">
                                <i class="fas fa-coins text-7xl"></i>
                            </div>
                        </div>

                        <div class="space-y-4 pt-2">
                             @can('lihat riwayat stok bahan baku')
                            <a href="{{ route('raw-materials.stock-history', $rawMaterial) }}" 
                               class="flex items-center gap-4 w-full p-4 rounded-2xl bg-white border border-gray-100 text-gray-500 hover:border-blue-500 hover:text-blue-500 transition-all font-bold text-xs uppercase tracking-widest group shadow-sm">
                                <i class="fas fa-history text-lg opacity-40 group-hover:opacity-100 transition-opacity"></i>
                                <span>Aliran Stok</span>
                            </a>
                            @endcan
                            @can('kelola stok bahan baku')
                            <a href="{{ route('raw-materials.manage-stock', $rawMaterial) }}" 
                               class="flex items-center gap-4 w-full p-4 rounded-2xl bg-white border border-gray-100 text-gray-500 hover:border-cuan-green hover:text-cuan-green transition-all font-bold text-xs uppercase tracking-widest group shadow-sm">
                                <i class="fas fa-pencil-alt text-lg opacity-40 group-hover:opacity-100 transition-opacity"></i>
                                <span>Update Batch</span>
                            </a>
                            @endcan
                        </div>
                    </div>
                </x-card-container>

                <div class="p-8 rounded-[2rem] bg-white border border-gray-100 shadow-sm">
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-6">Distribusi Batch</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-700">Valid</span>
                            <span class="text-xs font-black text-gray-900 tracking-tight">{{ number_format($stats['valid_count']) }}</span>
                        </div>
                        <div class="w-full bg-gray-50 rounded-full h-1.5 border border-gray-100">
                             @php $totalBatches = max($stats['valid_count'] + $stats['expiring_count'] + $stats['expired_count'], 1); @endphp
                            <div class="bg-emerald-500 h-full rounded-full" style="width: {{ ($stats['valid_count'] / $totalBatches) * 100 }}%"></div>
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <span class="text-xs font-bold text-gray-700">Kadaluarsa</span>
                            <span class="text-xs font-black text-red-600 tracking-tight">{{ number_format($stats['expired_count']) }}</span>
                        </div>
                        <div class="w-full bg-gray-50 rounded-full h-1.5 border border-gray-100">
                            <div class="bg-red-500 h-full rounded-full" style="width: {{ ($stats['expired_count'] / $totalBatches) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

{{-- MODAL BUANG STOK --}}
<div id="removeExpiredModal" class="hidden fixed inset-0 bg-gray-900/80 z-[60] backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] shadow-2xl max-w-md w-full overflow-hidden scale-in">
        <div class="bg-red-500 p-10 flex flex-col items-center">
            <div class="w-20 h-20 bg-white/10 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-trash-alt text-4xl text-white"></i>
            </div>
            <h3 class="text-2xl font-black text-white text-center leading-tight">Buang Stok Kadaluarsa?</h3>
            <p class="text-white/60 text-[10px] font-black uppercase tracking-[0.2em] mt-4">Tindakan Tidak Dapat Dibatalkan</p>
        </div>
        
        <form action="{{ route('raw-materials.remove-expired', $rawMaterial) }}" method="POST" id="removeExpiredForm">
            @csrf
            <div class="p-10 space-y-6">
                <div class="bg-gray-50 rounded-2xl p-6 text-center border border-gray-100">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Total Batch Terpilih</p>
                    <p class="text-4xl font-black text-gray-900 tracking-tighter" id="selectedCountText">0</p>
                </div>
                <p class="text-xs font-bold text-gray-500 text-center leading-relaxed">Seluruh batch terpilih akan dicatat sebagai penyesuaian stok rusak/habis di riwayat keuangan & pergerakan barang.</p>
            </div>

            <div class="flex gap-4 p-10 pt-0">
                <button type="button" onclick="closeRemoveExpiredModal()" 
                    class="flex-1 py-4 text-[10px] font-black uppercase text-gray-400 tracking-widest hover:text-gray-900 transition-all">BATAL</button>
                <button type="submit" 
                    class="flex-1 py-4 bg-red-600 text-[10px] font-black uppercase text-white tracking-widest rounded-2xl hover:bg-black transition-all shadow-lg active:scale-95">KONFIRMASI</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; }
    .scale-in { animation: scaleIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    @keyframes scaleIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
</style>
@endpush

@push('scripts')
<script>
    function updateSelectedCount() {
        const count = document.querySelectorAll('.expired-checkbox:checked').length;
        document.getElementById('selectedCountText').textContent = count;
    }

    document.querySelectorAll('.expired-checkbox').forEach(cb => {
        cb.addEventListener('change', updateSelectedCount);
    });

    function openRemoveExpiredModal() {
        const checkboxes = document.querySelectorAll('.expired-checkbox:checked');
        if (checkboxes.length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'Pilih Batch',
                text: 'Centang minimal satu batch untuk dihapus.',
                confirmButtonColor: '#111827',
                customClass: { popup: 'rounded-[2rem] border-none shadow-2xl', title: 'font-black' }
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
        $('#removeExpiredModal').removeClass('hidden');
    }

    function closeRemoveExpiredModal() {
        $('#removeExpiredModal').addClass('hidden');
    }

    $('#removeExpiredModal').on('click', function(e) {
        if (e.target === this) closeRemoveExpiredModal();
    });
</script>
@endpush

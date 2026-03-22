@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Detail Opname ' . $stockOpname->opname_number . ' - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('stock-opname.index') }}" class="text-gray-600 hover:text-gray-900 font-medium">Stock Opname</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">{{ $stockOpname->opname_number }}</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">
            <div class="space-y-4">
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-xl md:text-2xl font-black text-gray-900">
                        {{ $stockOpname->opname_number }}
                    </h1>
                    @php
                        $statusClass = match($stockOpname->status) {
                            'completed' => 'bg-cuan-green/10 text-cuan-green border-cuan-green/10',
                            'in_progress' => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                            default => 'bg-gray-50 text-gray-400 border-gray-200'
                        };
                        $statusLabel = match($stockOpname->status) {
                            'completed' => 'Selesai',
                            'in_progress' => 'Proses',
                            default => 'Draft'
                        };
                    @endphp
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                </div>

                <div class="flex flex-wrap items-center gap-x-8 gap-y-3">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">Tanggal Buat</span>
                        <span class="text-[11px] font-bold text-gray-700 mt-0.5">{{ $stockOpname->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">Penanggung Jawab</span>
                        <span class="text-[11px] font-bold text-gray-700 mt-0.5 capitalize">{{ $stockOpname->createdBy->name ?? '-' }}</span>
                    </div>
                    @if($stockOpname->notes)
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">Catatan</span>
                        <span class="text-[11px] font-bold text-gray-700 mt-0.5">{{ $stockOpname->notes }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                @if($stockOpname->status == 'draft')
                    @can('edit stock opname')
                    <form action="{{ route('stock-opname.update', $stockOpname->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="start_opname" value="1">
                        <button type="submit" class="h-11 px-6 bg-cuan-green text-white rounded-xl text-sm font-black hover:bg-cuan-dark transition-all active:scale-95 shadow-lg shadow-cuan-green/20">
                            Mulai Proses
                        </button>
                    </form>
                    @endcan
                @elseif($stockOpname->status == 'in_progress')
                    @can('finalisasi stock opname')
                    <form id="finalizeForm" action="{{ route('stock-opname.finalize', $stockOpname->id) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="confirm-toggle h-11 px-6 bg-cuan-green text-white rounded-xl text-sm font-black hover:bg-cuan-dark transition-all active:scale-95 shadow-lg shadow-cuan-green/20"
                                data-title="Selesaikan Opname?"
                                data-text="Aksi ini akan menyesuaikan stok di sistem berdasarkan hasil opname fisik. Pastikan semua data sudah benar.">
                            Selesaikan Opname
                        </button>
                    </form>
                    @endcan
                @endif
                
                <a href="{{ route('stock-opname.index') }}"
                   class="inline-flex items-center justify-center h-11 px-6 bg-white text-gray-700 border border-gray-200 rounded-xl text-sm font-black hover:bg-gray-50 transition-all active:scale-95 shadow-sm">
                    Kembali
                </a>
            </div>
        </section>

        {{-- ITEMS TABLE --}}
        <x-card-container>
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest italic">Lembar Kerja Opname</h3>
                @if($stockOpname->status == 'completed')
                    @php
                        $diff = $stockOpname->getTotalDifference();
                    @endphp
                    <div class="flex gap-4">
                        <div class="flex flex-col items-end">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Surplus</span>
                            <span class="text-[11px] font-black text-cuan-green">+{{ number_format($diff['positive']) }}</span>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Defisit</span>
                            <span class="text-[11px] font-black text-red-500">-{{ number_format($diff['negative']) }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <form id="opnameForm" action="{{ route('stock-opname.update', $stockOpname->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left">Item</th>
                                <th class="px-6 py-4 text-right">Stok Sistem</th>
                                <th class="px-6 py-4 text-center w-40">Stok Fisik</th>
                                <th class="px-6 py-4 text-right">Selisih</th>
                                <th class="px-6 py-4 text-left">Catatan Khusus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($stockOpname->items as $index => $item)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="text-[11px] font-bold text-gray-900 capitalize">{{ $item->stockable->name ?? '-' }}</div>
                                        <div class="text-[9px] font-black uppercase tracking-widest text-gray-400 mt-0.5">SKU: {{ $item->stockable->code ?? '-' }}</div>
                                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-right">
                                        <div class="text-[11px] font-black text-gray-900">{{ number_format($item->system_quantity, 0) }}</div>
                                        <div class="text-[9px] font-black uppercase tracking-widest text-gray-400 mt-0.5 italic">Sistem</div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        @if($stockOpname->status == 'completed')
                                            <div class="text-center font-black text-gray-900 border-b-2 border-gray-100 pb-1">
                                                {{ $item->physical_quantity !== null ? number_format($item->physical_quantity, 0) : '-' }}
                                            </div>
                                        @else
                                            @if($stockOpname->status == 'draft')
                                                <div class="text-center text-[10px] font-black text-gray-300 uppercase tracking-widest p-2 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                                    Klik Mulai
                                                </div>
                                            @else
                                                <input type="number" step="any" min="0" 
                                                    name="items[{{ $index }}][physical_quantity]" 
                                                    value="{{ old('items.' . $index . '.physical_quantity', $item->physical_quantity) }}"
                                                    class="w-full text-center rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-black text-gray-900 focus:ring-4 focus:ring-cuan-green/5 focus:border-cuan-green transition-all bg-white" 
                                                    placeholder="0"
                                                    data-system-qty="{{ $item->system_quantity }}"
                                                    oninput="calculateDiff(this)">
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-right">
                                        @php
                                            $diff = $item->difference;
                                            $color = $diff > 0 ? 'text-cuan-green bg-cuan-green/10 border-cuan-green/10' : ($diff < 0 ? 'text-red-600 bg-red-50 border-red-100' : 'text-gray-400 bg-gray-50 border-gray-100');
                                            $display = $diff !== null ? ($diff > 0 ? "+".number_format($diff,0) : number_format($diff,0)) : '-';
                                        @endphp
                                        <span class="diff-display inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black border {{ $color }}">
                                            {{ $display }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5">
                                        @if($stockOpname->status == 'completed')
                                            <span class="text-[11px] font-bold text-gray-500 italic">{{ $item->notes ?? '-' }}</span>
                                        @else
                                            @if($stockOpname->status == 'draft')
                                                <div class="h-2 w-8 bg-gray-100 rounded"></div>
                                            @else
                                                <input type="text" 
                                                    name="items[{{ $index }}][notes]" 
                                                    value="{{ $item->notes }}"
                                                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-xs font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/5 focus:border-cuan-green transition-all bg-white"
                                                    placeholder="Tambahkan catatan...">
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($stockOpname->status == 'in_progress')
                <div class="bg-gray-50/50 px-6 py-5 border-t border-gray-100 flex justify-between items-center gap-4">
                    <p class="text-[11px] font-bold text-gray-500 max-w-sm">Perubahan akan disimpan sebagai draft sampai Anda mengklik Selesaikan Opname.</p>
                    <button type="submit" class="h-10 px-6 bg-white text-gray-700 border border-gray-200 rounded-xl text-[11px] font-black uppercase tracking-widest hover:bg-gray-50 transition-all active:scale-95 shadow-sm whitespace-nowrap">
                        Simpan Perubahan
                    </button>
                </div>
                @endif
            </form>
        </x-card-container>
    </div>
</main>

@push('scripts')
<script>
    function calculateDiff(input) {
        const row = input.closest('tr');
        const systemQty = parseFloat(input.dataset.systemQty);
        const physicalQty = parseFloat(input.value);
        const diffDisplay = row.querySelector('.diff-display');

        if (isNaN(physicalQty)) {
            diffDisplay.textContent = '-';
            diffDisplay.className = 'diff-display inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black border text-gray-400 bg-gray-50 border-gray-100';
            return;
        }

        const diff = physicalQty - systemQty;
        
        diffDisplay.textContent = (diff > 0 ? '+' : '') + diff;
        
        if (diff > 0) {
            diffDisplay.className = 'diff-display inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black border text-cuan-green bg-cuan-green/10 border-cuan-green/10';
        } else if (diff < 0) {
            diffDisplay.className = 'diff-display inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black border text-red-600 bg-red-50 border-red-100';
        } else {
            diffDisplay.className = 'diff-display inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black border text-gray-400 bg-gray-50 border-gray-100';
        }
    }
</script>
@endpush
@endsection

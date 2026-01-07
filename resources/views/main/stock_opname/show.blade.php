@extends('layouts.app')

@section('title', 'Detail Opname ' . $stockOpname->opname_number . ' - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('stock-opname.index') }}" class="text-gray-500 hover:text-gray-700">Stock Opname</a>
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">{{ $stockOpname->opname_number }}</span>
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

        {{-- HEADER HALAMAN (POLA SERAGAM) --}}
        <section
            class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                         <span
                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100">
                            <i class="fas fa-clipboard-list text-sm"></i>
                        </span>
                        <span>{{ $stockOpname->opname_number }}</span>
                    </h1>
                    @if($stockOpname->status == 'completed')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                            Selesai
                        </span>
                    @elseif($stockOpname->status == 'in_progress')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                            Sedang Proses
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                            Draft
                        </span>
                    @endif
                </div>
                <div class="mt-2 flex flex-wrap gap-x-6 gap-y-2 text-sm text-gray-500">
                    <div class="flex items-center gap-1.5">
                        <i class="fas fa-calendar-alt text-gray-400"></i>
                        <span>Dibuat: {{ $stockOpname->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <i class="fas fa-user text-gray-400"></i>
                        <span>Oleh: {{ $stockOpname->createdBy->name ?? '-' }}</span>
                    </div>
                    @if($stockOpname->notes)
                    <div class="flex items-center gap-1.5">
                        <i class="fas fa-sticky-note text-gray-400"></i>
                        <span>Note: {{ $stockOpname->notes }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                @if($stockOpname->status == 'draft')
                    <form action="{{ route('stock-opname.update', $stockOpname->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="start_opname" value="1">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1 shadow-sm transition-all">
                            <i class="fas fa-play text-sm"></i>
                            <span>Mulai Proses</span>
                        </button>
                    </form>
                @elseif($stockOpname->status == 'in_progress')
                    <button type="button" onclick="document.getElementById('finalizeForm').submit()" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1 shadow-sm transition-all">
                        <i class="fas fa-check-circle text-sm"></i>
                        <span>Selesaikan Opname</span>
                    </button>
                @endif
                
                <a href="{{ route('stock-opname.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-white border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 shadow-sm transition-all">
                    <i class="fas fa-arrow-left text-sm"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </section>

        {{-- ITEMS TABLE --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h3 class="font-semibold text-gray-900">Daftar Item</h3>
                @if($stockOpname->status != 'completed')
                    <button type="button" onclick="document.getElementById('opnameForm').submit()" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                        <i class="fas fa-save"></i> Simpan Draft
                    </button>
                @else
                    <div class="text-sm">
                        @php
                            $diff = $stockOpname->getTotalDifference();
                        @endphp
                        <span class="text-green-600 font-semibold mr-3">Surplus: +{{ number_format($diff['positive']) }}</span>
                        <span class="text-red-600 font-semibold">Defisit: -{{ number_format($diff['negative']) }}</span>
                    </div>
                @endif
            </div>

            <form id="opnameForm" action="{{ route('stock-opname.update', $stockOpname->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-500 uppercase font-semibold text-xs">
                            <tr>
                                <th class="px-6 py-3">Produk</th>
                                <th class="px-6 py-3 text-right">Stok Sistem</th>
                                <th class="px-6 py-3 text-right w-40">Stok Fisik</th>
                                <th class="px-6 py-3 text-right">Selisih</th>
                                <th class="px-6 py-3">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($stockOpname->items as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3">
                                        <div class="font-medium text-gray-900">{{ $item->stockable->name ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->stockable->code ?? '' }}</div>
                                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                    </td>
                                    <td class="px-6 py-3 text-right font-mono text-gray-600">
                                        {{ number_format($item->system_quantity, 0) }}
                                    </td>
                                    <td class="px-6 py-3">
                                        @if($stockOpname->status == 'completed')
                                            <div class="text-right font-bold text-gray-900">
                                                {{ $item->physical_quantity !== null ? number_format($item->physical_quantity, 0) : '-' }}
                                            </div>
                                        @else
                                            @if($stockOpname->status == 'draft')
                                            <div class="text-right text-gray-400 italic text-xs py-2">
                                                Mulai dulu
                                            </div>
                                            <!-- Hidden input to preserve structure if needed, or just omit -->
                                            @else
                                            <input type="number" step="any" min="0" 
                                                name="items[{{ $index }}][physical_quantity]" 
                                                value="{{ old('items.' . $index . '.physical_quantity', $item->physical_quantity) }}"
                                                class="w-full text-right rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm py-1.5 physical-qty-input"
                                                data-system-qty="{{ $item->system_quantity }}"
                                                oninput="calculateDiff(this)">
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-right font-bold">
                                        @php
                                            $diff = $item->difference;
                                            $color = $diff > 0 ? 'text-green-600' : ($diff < 0 ? 'text-red-600' : 'text-gray-400');
                                            $display = $diff !== null ? ($diff > 0 ? "+".number_format($diff,0) : number_format($diff,0)) : '-';
                                        @endphp
                                        <span class="diff-display {{ $color }}">
                                            {{ $display }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3">
                                        @if($stockOpname->status == 'completed')
                                            <span class="text-gray-500 italic">{{ $item->notes ?? '-' }}</span>
                                        @else
                                            @if($stockOpname->status == 'draft')
                                                <span class="text-gray-300 text-xs italic">-</span>
                                            @else
                                                <input type="text" 
                                                    name="items[{{ $index }}][notes]" 
                                                    value="{{ $item->notes }}"
                                                    class="w-full rounded-lg border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 sm:text-xs py-1.5"
                                                    placeholder="Ket.">
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($stockOpname->status != 'completed')
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end">
                    <button type="submit" class="btn bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2 px-4 rounded-lg shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
                @endif
            </form>

            <form id="finalizeForm" action="{{ route('stock-opname.finalize', $stockOpname->id) }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
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
            diffDisplay.className = 'diff-display text-gray-400';
            return;
        }

        const diff = physicalQty - systemQty;
        
        diffDisplay.textContent = (diff > 0 ? '+' : '') + diff; // Simple format for JS, backend handles real format
        
        if (diff > 0) {
            diffDisplay.className = 'diff-display text-green-600';
        } else if (diff < 0) {
            diffDisplay.className = 'diff-display text-red-600';
        } else {
            diffDisplay.className = 'diff-display text-gray-400';
        }
    }
</script>
@endpush
@endsection

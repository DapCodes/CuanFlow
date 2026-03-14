@extends('layouts.app')

@section('title', 'Ringkasan Invoice - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Ringkasan Invoice</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-8">

        {{-- HEADER HALAMAN & PENCARIAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900 leading-tight">
                    Ringkasan Invoice & Transaksi
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Pantau ringkasan penjualan, pemasukan, pengeluaran, dan piutang terbaru dalam satu halaman.
                </p>
            </div>
            <div class="w-full md:w-72">
                <form id="searchForm" class="relative group" action="{{ route('invoices.index') }}" method="GET" onsubmit="event.preventDefault(); triggerSearch();">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-cuan-green transition-colors">
                        <i class="fas fa-search text-sm w-4 h-4 flex items-center justify-center"></i>
                    </div>
                    <input type="text" name="search" id="searchInput" value="{{ $search ?? '' }}"
                           class="block w-full pl-11 pr-4 py-3 bg-white border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all shadow-sm" 
                           placeholder="Cari referensi atau pelanggan...">
                    @if($search)
                    <button type="button" onclick="clearSearch()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-red-500 transition-colors">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                    @endif
                </form>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            {{-- TABEL PENJUALAN --}}
            <x-card-container class="flex flex-col flex-grow relative" id="sales-container">
                <div id="sales-overlay" class="absolute inset-0 bg-white/70 backdrop-blur-sm z-10 flex items-center justify-center hidden rounded-[2rem]">
                    <i class="fas fa-spinner fa-spin text-cuan-green text-3xl"></i>
                </div>
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Penjualan Terbaru</h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Transaksi masuk</p>
                    </div>
                    <a href="{{ route('sales.index') }}" class="text-[10px] font-black uppercase tracking-widest text-cuan-green hover:text-cuan-dark transition-colors bg-cuan-green/10 px-3 py-1.5 rounded-lg">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto flex-grow">
                    <table class="w-full text-sm">
                        <thead class="bg-white border-b border-gray-100">
                            <tr>
                                <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Invoice</th>
                                <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Total</th>
                                <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                            @forelse($recentSales as $sale)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-8 py-4">
                                        <div class="font-bold text-gray-900 text-xs">{{ $sale->invoice_number }}</div>
                                        <div class="text-[10px] text-gray-400 font-medium uppercase tracking-widest mt-1">{{ $sale->created_at->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td class="px-8 py-4 font-black text-gray-900 text-xs">
                                        Rp {{ number_format($sale->grand_total, 0) }}
                                    </td>
                                    <td class="px-8 py-4 whitespace-nowrap">
                                        <button 
                                            onclick="openPrintModal('{{ $sale->id }}', 'sale', {
                                                name: '{{ $sale->customer->name ?? '' }}',
                                                phone: '{{ $sale->customer->phone ?? '' }}',
                                                address: '{{ $sale->customer->address ?? '' }}'
                                            })"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-gray-50 text-gray-500 hover:bg-cuan-green hover:text-white transition-all transform group-hover:scale-105" title="Cetak">
                                            <i class="fas fa-print text-xs"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-8 py-8 text-center">
                                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Belum ada data penjualan</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($recentSales->hasPages())
                    <div class="px-8 py-4 border-t border-gray-100 bg-gray-50/30 flex items-center justify-between">
                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                            Hal {{ $recentSales->currentPage() }} / {{ $recentSales->lastPage() }}
                        </div>
                        <div class="flex items-center gap-2">
                            @if ($recentSales->onFirstPage())
                                <span class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-100 text-gray-400 cursor-not-allowed">
                                    <i class="fas fa-chevron-left text-[10px]"></i>
                                </span>
                            @else
                                <a href="{{ $recentSales->previousPageUrl() }}" 
                                   class="ajax-pagination w-8 h-8 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-cuan-green transition-all shadow-sm active:scale-95"
                                   data-target="sales-container">
                                    <i class="fas fa-chevron-left text-[10px]"></i>
                                </a>
                            @endif

                            @if ($recentSales->hasMorePages())
                                <a href="{{ $recentSales->nextPageUrl() }}" 
                                   class="ajax-pagination w-8 h-8 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-cuan-green transition-all shadow-sm active:scale-95"
                                   data-target="sales-container">
                                    <i class="fas fa-chevron-right text-[10px]"></i>
                                </a>
                            @else
                                <span class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-100 text-gray-400 cursor-not-allowed">
                                    <i class="fas fa-chevron-right text-[10px]"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </x-card-container>

            {{-- TABEL PEMASUKAN --}}
            <x-card-container class="flex flex-col flex-grow relative" id="incomes-container">
                <div id="incomes-overlay" class="absolute inset-0 bg-white/70 backdrop-blur-sm z-10 flex items-center justify-center hidden rounded-[2rem]">
                    <i class="fas fa-spinner fa-spin text-cuan-green text-3xl"></i>
                </div>
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Pemasukan Lain</h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Non-penjualan</p>
                    </div>
                    <a href="{{ route('expenses.index', ['type' => 'income']) }}" class="text-[10px] font-black uppercase tracking-widest text-cuan-green hover:text-cuan-dark transition-colors bg-cuan-green/10 px-3 py-1.5 rounded-lg">Kelola</a>
                </div>
                <div class="overflow-x-auto flex-grow">
                    <table class="w-full text-sm">
                        <thead class="bg-white border-b border-gray-100">
                            <tr>
                                <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">No. Ref</th>
                                <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Jumlah</th>
                                <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                            @forelse($recentIncomes as $income)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-8 py-4">
                                        <div class="font-bold text-gray-900 text-xs">{{ $income->expense_number }}</div>
                                        <div class="text-[10px] text-gray-400 font-medium uppercase tracking-widest mt-1">{{ $income->expense_date->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-8 py-4 font-black text-cuan-green text-xs">
                                        Rp {{ number_format($income->amount, 0) }}
                                    </td>
                                    <td class="px-8 py-4 whitespace-nowrap">
                                        <a href="{{ route('invoices.expense.print', $income->id) }}" target="_blank"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-gray-50 text-gray-500 hover:bg-cuan-green hover:text-white transition-all transform group-hover:scale-105" title="Cetak">
                                            <i class="fas fa-print text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-8 py-8 text-center">
                                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Belum ada data pemasukan</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($recentIncomes->hasPages())
                    <div class="px-8 py-4 border-t border-gray-100 bg-gray-50/30 flex items-center justify-between">
                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                            Hal {{ $recentIncomes->currentPage() }} / {{ $recentIncomes->lastPage() }}
                        </div>
                        <div class="flex items-center gap-2">
                            @if ($recentIncomes->onFirstPage())
                                <span class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-100 text-gray-400 cursor-not-allowed">
                                    <i class="fas fa-chevron-left text-[10px]"></i>
                                </span>
                            @else
                                <a href="{{ $recentIncomes->previousPageUrl() }}" 
                                   class="ajax-pagination w-8 h-8 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-cuan-green transition-all shadow-sm active:scale-95"
                                   data-target="incomes-container">
                                    <i class="fas fa-chevron-left text-[10px]"></i>
                                </a>
                            @endif

                            @if ($recentIncomes->hasMorePages())
                                <a href="{{ $recentIncomes->nextPageUrl() }}" 
                                   class="ajax-pagination w-8 h-8 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-cuan-green transition-all shadow-sm active:scale-95"
                                   data-target="incomes-container">
                                    <i class="fas fa-chevron-right text-[10px]"></i>
                                </a>
                            @else
                                <span class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-100 text-gray-400 cursor-not-allowed">
                                    <i class="fas fa-chevron-right text-[10px]"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </x-card-container>

            {{-- TABEL PENGELUARAN --}}
            <x-card-container class="flex flex-col flex-grow relative" id="expenses-container">
                <div id="expenses-overlay" class="absolute inset-0 bg-white/70 backdrop-blur-sm z-10 flex items-center justify-center hidden rounded-[2rem]">
                    <i class="fas fa-spinner fa-spin text-cuan-green text-3xl"></i>
                </div>
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Pengeluaran</h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Biaya Operasional</p>
                    </div>
                    <a href="{{ route('expenses.index', ['type' => 'expense']) }}" class="text-[10px] font-black uppercase tracking-widest text-cuan-green hover:text-cuan-dark transition-colors bg-cuan-green/10 px-3 py-1.5 rounded-lg">Kelola</a>
                </div>
                <div class="overflow-x-auto flex-grow">
                    <table class="w-full text-sm">
                        <thead class="bg-white border-b border-gray-100">
                            <tr>
                                <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">No. Ref</th>
                                <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Jumlah</th>
                                <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                            @forelse($recentExpenses as $expense)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-8 py-4">
                                        <div class="font-bold text-gray-900 text-xs">{{ $expense->expense_number }}</div>
                                        <div class="text-[10px] text-gray-400 font-medium uppercase tracking-widest mt-1">{{ $expense->expense_date->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-8 py-4 font-black text-red-500 text-xs">
                                        Rp {{ number_format($expense->amount, 0) }}
                                    </td>
                                    <td class="px-8 py-4 whitespace-nowrap">
                                        <a href="{{ route('invoices.expense.print', $expense->id) }}" target="_blank"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-gray-50 text-gray-500 hover:bg-cuan-green hover:text-white transition-all transform group-hover:scale-105" title="Cetak">
                                            <i class="fas fa-print text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-8 py-8 text-center">
                                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Belum ada data pengeluaran</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($recentExpenses->hasPages())
                    <div class="px-8 py-4 border-t border-gray-100 bg-gray-50/30 flex items-center justify-between">
                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                            Hal {{ $recentExpenses->currentPage() }} / {{ $recentExpenses->lastPage() }}
                        </div>
                        <div class="flex items-center gap-2">
                            @if ($recentExpenses->onFirstPage())
                                <span class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-100 text-gray-400 cursor-not-allowed">
                                    <i class="fas fa-chevron-left text-[10px]"></i>
                                </span>
                            @else
                                <a href="{{ $recentExpenses->previousPageUrl() }}" 
                                   class="ajax-pagination w-8 h-8 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-cuan-green transition-all shadow-sm active:scale-95"
                                   data-target="expenses-container">
                                    <i class="fas fa-chevron-left text-[10px]"></i>
                                </a>
                            @endif

                            @if ($recentExpenses->hasMorePages())
                                <a href="{{ $recentExpenses->nextPageUrl() }}" 
                                   class="ajax-pagination w-8 h-8 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-cuan-green transition-all shadow-sm active:scale-95"
                                   data-target="expenses-container">
                                    <i class="fas fa-chevron-right text-[10px]"></i>
                                </a>
                            @else
                                <span class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-100 text-gray-400 cursor-not-allowed">
                                    <i class="fas fa-chevron-right text-[10px]"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </x-card-container>

            {{-- TABEL PIUTANG --}}
            <x-card-container class="flex flex-col flex-grow relative" id="debts-container">
                <div id="debts-overlay" class="absolute inset-0 bg-white/70 backdrop-blur-sm z-10 flex items-center justify-center hidden rounded-[2rem]">
                    <i class="fas fa-spinner fa-spin text-cuan-green text-3xl"></i>
                </div>
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Piutang Pelanggan</h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Pending/Sebagian</p>
                    </div>
                    <a href="{{ route('customer-debts.index') }}" class="text-[10px] font-black uppercase tracking-widest text-cuan-green hover:text-cuan-dark transition-colors bg-cuan-green/10 px-3 py-1.5 rounded-lg">Kelola</a>
                </div>
                <div class="overflow-x-auto flex-grow">
                    <table class="w-full text-sm">
                        <thead class="bg-white border-b border-gray-100">
                            <tr>
                                <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Pelanggan</th>
                                <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Sisa Piutang</th>
                                <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                            @forelse($recentDebts as $debt)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-8 py-4">
                                        <div class="font-bold text-gray-900 text-xs">{{ $debt->customer->name ?? 'Umum' }}</div>
                                        <div class="text-[10px] text-gray-400 font-medium uppercase tracking-widest mt-1">Tempo: {{ $debt->due_date ? $debt->due_date->format('d/m/Y') : '-' }}</div>
                                    </td>
                                    <td class="px-8 py-4 font-black text-gray-900 text-xs">
                                        Rp {{ number_format($debt->remaining_amount, 0) }}
                                    </td>
                                    <td class="px-8 py-4 whitespace-nowrap">
                                        <button 
                                            onclick="openPrintModal('{{ $debt->sale_id }}', 'sale', {
                                                name: '{{ $debt->customer->name ?? '' }}',
                                                phone: '{{ $debt->customer->phone ?? '' }}',
                                                address: '{{ $debt->customer->address ?? '' }}'
                                            })"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-gray-50 text-gray-500 hover:bg-cuan-green hover:text-white transition-all transform group-hover:scale-105" title="Cetak">
                                            <i class="fas fa-print text-xs"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-8 py-8 text-center">
                                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Tidak ada piutang aktif</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                    </table>
                </div>
                @if($recentDebts->hasPages())
                    <div class="px-8 py-4 border-t border-gray-100 bg-gray-50/30 flex items-center justify-between">
                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                            Hal {{ $recentDebts->currentPage() }} / {{ $recentDebts->lastPage() }}
                        </div>
                        <div class="flex items-center gap-2">
                            @if ($recentDebts->onFirstPage())
                                <span class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-100 text-gray-400 cursor-not-allowed">
                                    <i class="fas fa-chevron-left text-[10px]"></i>
                                </span>
                            @else
                                <a href="{{ $recentDebts->previousPageUrl() }}" 
                                   class="ajax-pagination w-8 h-8 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-cuan-green transition-all shadow-sm active:scale-95"
                                   data-target="debts-container">
                                    <i class="fas fa-chevron-left text-[10px]"></i>
                                </a>
                            @endif

                            @if ($recentDebts->hasMorePages())
                                <a href="{{ $recentDebts->nextPageUrl() }}" 
                                   class="ajax-pagination w-8 h-8 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-cuan-green transition-all shadow-sm active:scale-95"
                                   data-target="debts-container">
                                    <i class="fas fa-chevron-right text-[10px]"></i>
                                </a>
                            @else
                                <span class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-100 text-gray-400 cursor-not-allowed">
                                    <i class="fas fa-chevron-right text-[10px]"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </x-card-container>

        </div>
    </div>
</main>

{{-- Modal Customer Info --}}
<div id="printModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closePrintModal()"></div>
        
        <div class="inline-block bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg w-full relative z-10">
            <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">Cetak Invoice</h3>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Lengkapi data pelanggan</p>
                </div>
                <button type="button" onclick="closePrintModal()" class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-100 text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="p-8">
                <form id="printForm" target="_blank" class="space-y-6">
                    <div>
                        <label for="customer_name" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Nama Pelanggan</label>
                        <input type="text" name="customer_name" id="customer_name" class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all" placeholder="Masukkan nama pelanggan...">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="customer_phone" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">No. WhatsApp</label>
                            <input type="text" name="customer_phone" id="customer_phone" class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all" placeholder="08xxx...">
                        </div>
                        <div>
                            <label for="due_date" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Jatuh Tempo (Opsional)</label>
                            <input type="date" name="due_date" id="due_date" class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all">
                        </div>
                    </div>

                    <div>
                        <label for="customer_address" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Alamat</label>
                        <textarea name="customer_address" id="customer_address" rows="3" class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all" placeholder="Masukkan alamat lengkap..."></textarea>
                    </div>
                </form>
            </div>
            
            <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100 flex flex-col md:flex-row justify-end gap-3">
                <button type="button" onclick="closePrintModal()" class="px-8 py-4 bg-white border border-gray-200 text-gray-600 rounded-2xl font-bold text-sm hover:bg-gray-50 transition-all active:scale-95 text-center">
                    Batal
                </button>
                <button type="button" onclick="submitPrintForm()" class="px-8 py-4 bg-cuan-green text-white rounded-2xl font-black text-sm hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95 text-center">
                    Cetak Invoice
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const modal = document.getElementById('printModal');
    const printForm = document.getElementById('printForm');
    
    function openPrintModal(id, type, customer) {
        if (type === 'sale') {
            printForm.action = `/receipt/invoice/${id}/print`;
            document.getElementById('customer_name').value = customer.name || '';
            document.getElementById('customer_phone').value = customer.phone || '';
            document.getElementById('customer_address').value = customer.address || '';
            modal.classList.remove('hidden');
        } else {
            window.open(`/invoices/expense/${id}/print`, '_blank');
        }
    }

    function closePrintModal() {
        modal.classList.add('hidden');
        printForm.reset();
    }

    function submitPrintForm() {
        printForm.submit();
        setTimeout(closePrintModal, 100);
    }

    // Close on escape
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closePrintModal();
    });

    // --- AJAX NO REFRESH LOGIC ---
    let debounceTimer;
    const searchInput = document.getElementById('searchInput');

    function toggleOverlays(show, targetId = null) {
        const ids = targetId ? [targetId] : ['sales', 'incomes', 'expenses', 'debts'];
        ids.forEach(id => {
            const containerName = id.replace('-container', '');
            const overlay = document.getElementById(containerName + '-overlay');
            if(overlay) {
                if(show) overlay.classList.remove('hidden');
                else overlay.classList.add('hidden');
            }
        });
    }

    // Dynamic fetch and replace DOM
    async function fetchAndUpdate(url, targetId = null) {
        toggleOverlays(true, targetId);
        
        try {
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!response.ok) throw new Error('Network error');
            const html = await response.text();
            
            // Parse new DOM
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // If a single table target is clicked via pagination
            if (targetId) {
                const newContent = doc.getElementById(targetId);
                const oldContent = document.getElementById(targetId);
                if(newContent && oldContent) {
                    oldContent.innerHTML = newContent.innerHTML;
                }
            } else {
                // If it's a global search, update all 4 tables
                ['sales-container', 'incomes-container', 'expenses-container', 'debts-container'].forEach(id => {
                    const newContent = doc.getElementById(id);
                    const oldContent = document.getElementById(id);
                    if(newContent && oldContent) {
                        oldContent.innerHTML = newContent.innerHTML;
                    }
                });
                
                // Update URL history for global search
                window.history.pushState({}, '', url);
            }
        } catch (error) {
            console.error('Fetch error:', error);
            window.location.href = url; // Fallback
        } finally {
            toggleOverlays(false, targetId);
            bindPaginationLinks(); // Rebind events to new DOM elements
        }
    }

    function triggerSearch() {
        const query = searchInput.value;
        const url = new URL(window.location.href);
        if (query) {
            url.searchParams.set('search', query);
        } else {
            url.searchParams.delete('search');
        }
        
        // Reset all paginations to page 1 for search
        ['sales_page', 'income_page', 'expense_page', 'debt_page'].forEach(param => {
            url.searchParams.delete(param);
        });

        fetchAndUpdate(url.toString());
    }

    function clearSearch() {
        searchInput.value = '';
        triggerSearch();
        
        // remove clear button if exists
        const btn = searchInput.nextElementSibling;
        if(btn && btn.tagName === 'BUTTON') {
            btn.remove();
        }
    }

    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            triggerSearch();
        }, 500); // 500ms debounce
    });

    function bindPaginationLinks() {
        document.querySelectorAll('.ajax-pagination').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                const target = this.getAttribute('data-target');
                if (url) {
                    fetchAndUpdate(url, target);
                }
            });
        });
    }

    // Initial binding
    document.addEventListener('DOMContentLoaded', bindPaginationLinks);

</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'Ringkasan Invoice - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Ringkasan Invoice</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-8">

        {{-- HEADER HALAMAN --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-50 text-indigo-500 border border-indigo-100">
                        <i class="fas fa-file-invoice text-sm"></i>
                    </span>
                    <span>Ringkasan Invoice & Transaksi</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Pantau ringkasan penjualan, pemasukan, pengeluaran, dan piutang terbaru dalam satu halaman.
                </p>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            {{-- TABEL PENJUALAN --}}
            <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex flex-col">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h2 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-shopping-cart text-pink-500"></i>
                        Penjualan Terbaru
                    </h2>
                    <a href="{{ route('sales.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Riwayat Penjualan</a>
                </div>
                <div class="overflow-x-auto flex-grow">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Invoice</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($recentSales as $sale)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">{{ $sale->invoice_number }}</div>
                                        <div class="text-[10px] text-gray-400">{{ $sale->created_at->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-gray-900">
                                        Rp {{ number_format($sale->grand_total, 0) }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <button 
                                            onclick="openPrintModal('{{ $sale->id }}', 'sale', {
                                                name: '{{ $sale->customer->name ?? '' }}',
                                                phone: '{{ $sale->customer->phone ?? '' }}',
                                                address: '{{ $sale->customer->address ?? '' }}'
                                            })"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 border border-indigo-100 transition-colors text-xs font-semibold">
                                            <i class="fas fa-print"></i>
                                            Cetak
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-400">Belum ada data penjualan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($recentSales->hasPages())
                    <div class="px-4 py-2 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between">
                        <div class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider">
                            Hal {{ $recentSales->currentPage() }} / {{ $recentSales->lastPage() }}
                        </div>
                        <div class="flex items-center gap-1">
                            @if ($recentSales->onFirstPage())
                                <span class="w-7 h-7 flex items-center justify-center rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed">
                                    <i class="fas fa-chevron-left text-[10px]"></i>
                                </span>
                            @else
                                <a href="{{ $recentSales->appends(['income_page' => $recentIncomes->currentPage(), 'expense_page' => $recentExpenses->currentPage(), 'debt_page' => $recentDebts->currentPage()])->previousPageUrl() }}" 
                                   class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200 text-indigo-600 hover:bg-indigo-50 hover:border-indigo-200 transition-all shadow-sm">
                                    <i class="fas fa-chevron-left text-[10px]"></i>
                                </a>
                            @endif

                            @if ($recentSales->hasMorePages())
                                <a href="{{ $recentSales->appends(['income_page' => $recentIncomes->currentPage(), 'expense_page' => $recentExpenses->currentPage(), 'debt_page' => $recentDebts->currentPage()])->nextPageUrl() }}" 
                                   class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200 text-indigo-600 hover:bg-indigo-50 hover:border-indigo-200 transition-all shadow-sm">
                                    <i class="fas fa-chevron-right text-[10px]"></i>
                                </a>
                            @else
                                <span class="w-7 h-7 flex items-center justify-center rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed">
                                    <i class="fas fa-chevron-right text-[10px]"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </section>

            {{-- TABEL PEMASUKAN --}}
            <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex flex-col">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h2 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-arrow-trend-up text-emerald-500"></i>
                        Pemasukan Lain
                    </h2>
                    <a href="{{ route('expenses.index', ['type' => 'income']) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Manajemen Pemasukan</a>
                </div>
                <div class="overflow-x-auto flex-grow">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">No. Ref</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Jumlah</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($recentIncomes as $income)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">{{ $income->expense_number }}</div>
                                        <div class="text-[10px] text-gray-400">{{ $income->expense_date->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-emerald-600">
                                        Rp {{ number_format($income->amount, 0) }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <a href="{{ route('invoices.expense.print', $income->id) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 border border-emerald-100 transition-colors text-xs font-semibold">
                                            <i class="fas fa-print"></i>
                                            Cetak
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-400">Belum ada data pemasukan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($recentIncomes->hasPages())
                    <div class="px-4 py-2 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between">
                        <div class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider">
                            Hal {{ $recentIncomes->currentPage() }} / {{ $recentIncomes->lastPage() }}
                        </div>
                        <div class="flex items-center gap-1">
                            @if ($recentIncomes->onFirstPage())
                                <span class="w-7 h-7 flex items-center justify-center rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed">
                                    <i class="fas fa-chevron-left text-[10px]"></i>
                                </span>
                            @else
                                <a href="{{ $recentIncomes->appends(['sales_page' => $recentSales->currentPage(), 'expense_page' => $recentExpenses->currentPage(), 'debt_page' => $recentDebts->currentPage()])->previousPageUrl() }}" 
                                   class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200 text-emerald-600 hover:bg-emerald-50 hover:border-emerald-200 transition-all shadow-sm">
                                    <i class="fas fa-chevron-left text-[10px]"></i>
                                </a>
                            @endif

                            @if ($recentIncomes->hasMorePages())
                                <a href="{{ $recentIncomes->appends(['sales_page' => $recentSales->currentPage(), 'expense_page' => $recentExpenses->currentPage(), 'debt_page' => $recentDebts->currentPage()])->nextPageUrl() }}" 
                                   class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200 text-emerald-600 hover:bg-emerald-50 hover:border-emerald-200 transition-all shadow-sm">
                                    <i class="fas fa-chevron-right text-[10px]"></i>
                                </a>
                            @else
                                <span class="w-7 h-7 flex items-center justify-center rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed">
                                    <i class="fas fa-chevron-right text-[10px]"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </section>

            {{-- TABEL PENGELUARAN --}}
            <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex flex-col">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h2 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-receipt text-orange-500"></i>
                        Pengeluaran (Biaya Ops)
                    </h2>
                    <a href="{{ route('expenses.index', ['type' => 'expense']) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Manajemen Pengeluaran</a>
                </div>
                <div class="overflow-x-auto flex-grow">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">No. Ref</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Jumlah</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($recentExpenses as $expense)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">{{ $expense->expense_number }}</div>
                                        <div class="text-[10px] text-gray-400">{{ $expense->expense_date->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-red-600">
                                        Rp {{ number_format($expense->amount, 0) }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <a href="{{ route('invoices.expense.print', $expense->id) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-100 border border-orange-100 transition-colors text-xs font-semibold">
                                            <i class="fas fa-print"></i>
                                            Cetak
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-400">Belum ada data pengeluaran</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($recentExpenses->hasPages())
                    <div class="px-4 py-2 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between">
                        <div class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider">
                            Hal {{ $recentExpenses->currentPage() }} / {{ $recentExpenses->lastPage() }}
                        </div>
                        <div class="flex items-center gap-1">
                            @if ($recentExpenses->onFirstPage())
                                <span class="w-7 h-7 flex items-center justify-center rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed">
                                    <i class="fas fa-chevron-left text-[10px]"></i>
                                </span>
                            @else
                                <a href="{{ $recentExpenses->appends(['sales_page' => $recentSales->currentPage(), 'income_page' => $recentIncomes->currentPage(), 'debt_page' => $recentDebts->currentPage()])->previousPageUrl() }}" 
                                   class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200 text-orange-600 hover:bg-orange-50 hover:border-orange-200 transition-all shadow-sm">
                                    <i class="fas fa-chevron-left text-[10px]"></i>
                                </a>
                            @endif

                            @if ($recentExpenses->hasMorePages())
                                <a href="{{ $recentExpenses->appends(['sales_page' => $recentSales->currentPage(), 'income_page' => $recentIncomes->currentPage(), 'debt_page' => $recentDebts->currentPage()])->nextPageUrl() }}" 
                                   class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200 text-orange-600 hover:bg-orange-50 hover:border-orange-200 transition-all shadow-sm">
                                    <i class="fas fa-chevron-right text-[10px]"></i>
                                </a>
                            @else
                                <span class="w-7 h-7 flex items-center justify-center rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed">
                                    <i class="fas fa-chevron-right text-[10px]"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </section>

            {{-- TABEL PIUTANG --}}
            <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex flex-col">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h2 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-hand-holding-dollar text-purple-500"></i>
                        Piutang (Pending/Partial)
                    </h2>
                    <a href="{{ route('customer-debts.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Daftar Piutang</a>
                </div>
                <div class="overflow-x-auto flex-grow">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Pelanggan</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Sisa Piutang</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($recentDebts as $debt)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">{{ $debt->customer->name ?? 'Umum' }}</div>
                                        <div class="text-[10px] text-gray-400">Tempo: {{ $debt->due_date ? $debt->due_date->format('d/m/Y') : '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-gray-900">
                                        Rp {{ number_format($debt->remaining_amount, 0) }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <button 
                                            onclick="openPrintModal('{{ $debt->sale_id }}', 'sale', {
                                                name: '{{ $debt->customer->name ?? '' }}',
                                                phone: '{{ $debt->customer->phone ?? '' }}',
                                                address: '{{ $debt->customer->address ?? '' }}'
                                            })"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-purple-50 text-purple-600 hover:bg-purple-100 border border-purple-100 transition-colors text-xs font-semibold">
                                            <i class="fas fa-print"></i>
                                            Cetak
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-400">Tidak ada piutang aktif</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($recentDebts->hasPages())
                    <div class="px-4 py-2 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between">
                        <div class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider">
                            Hal {{ $recentDebts->currentPage() }} / {{ $recentDebts->lastPage() }}
                        </div>
                        <div class="flex items-center gap-1">
                            @if ($recentDebts->onFirstPage())
                                <span class="w-7 h-7 flex items-center justify-center rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed">
                                    <i class="fas fa-chevron-left text-[10px]"></i>
                                </span>
                            @else
                                <a href="{{ $recentDebts->appends(['sales_page' => $recentSales->currentPage(), 'income_page' => $recentIncomes->currentPage(), 'expense_page' => $recentExpenses->currentPage()])->previousPageUrl() }}" 
                                   class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200 text-purple-600 hover:bg-purple-50 hover:border-purple-200 transition-all shadow-sm">
                                    <i class="fas fa-chevron-left text-[10px]"></i>
                                </a>
                            @endif

                            @if ($recentDebts->hasMorePages())
                                <a href="{{ $recentDebts->appends(['sales_page' => $recentSales->currentPage(), 'income_page' => $recentIncomes->currentPage(), 'expense_page' => $recentExpenses->currentPage()])->nextPageUrl() }}" 
                                   class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200 text-purple-600 hover:bg-purple-50 hover:border-purple-200 transition-all shadow-sm">
                                    <i class="fas fa-chevron-right text-[10px]"></i>
                                </a>
                            @else
                                <span class="w-7 h-7 flex items-center justify-center rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed">
                                    <i class="fas fa-chevron-right text-[10px]"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </section>

        </div>
    </div>
</main>

{{-- Modal Customer Info --}}
<div id="printModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closePrintModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-user-edit text-indigo-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-semibold text-gray-900" id="modal-title">Lengkapi Data Pelanggan</h3>
                        <p class="text-sm text-gray-500 mt-1">Data ini akan ditampilkan pada invoice yang dicetak.</p>
                        
                        <form id="printForm" target="_blank" class="mt-4 space-y-4">
                            <div>
                                <label for="customer_name" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Nama Pelanggan</label>
                                <input type="text" name="customer_name" id="customer_name" class="block w-full px-4 py-3 rounded-xl border-gray-200 bg-gray-50 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="Masukkan nama pelanggan...">
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="customer_phone" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">No. WhatsApp</label>
                                    <input type="text" name="customer_phone" id="customer_phone" class="block w-full px-4 py-3 rounded-xl border-gray-200 bg-gray-50 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="08xxx...">
                                </div>
                                <div>
                                    <label for="due_date" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Jatuh Tempo (Opsional)</label>
                                    <input type="date" name="due_date" id="due_date" class="block w-full px-4 py-3 rounded-xl border-gray-200 bg-gray-50 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                                </div>
                            </div>

                            <div>
                                <label for="customer_address" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Alamat</label>
                                <textarea name="customer_address" id="customer_address" rows="2" class="block w-full px-4 py-3 rounded-xl border-gray-200 bg-gray-50 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="Masukkan alamat lengkap..."></textarea>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="submitPrintForm()" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                    Cetak Sekarang
                </button>
                <button type="button" onclick="closePrintModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                    Batal
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
</script>
@endpush
@endsection

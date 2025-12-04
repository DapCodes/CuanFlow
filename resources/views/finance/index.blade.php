@extends('layouts.app')

@section('title', 'Keuangan - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Keuangan</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4">
    <div class="max-w-7xl mx-auto space-y-6">
        
        @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
            <div class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        <!-- Header Section -->
        <x-card-container>
            <div class="bg-gradient-to-br from-purple-50 to-indigo-50 p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-chart-line text-purple-500 mr-3"></i>
                            Manajemen Keuangan
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Pantau dan kelola keuangan outlet Anda</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <a href="{{ route('finance.income.create') }}" class="inline-flex items-center justify-center px-5 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-500 transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Pemasukan
                        </a>
                        <a href="{{ route('finance.expense.create') }}" class="inline-flex items-center justify-center px-5 py-3 bg-white text-red-600 border border-red-200 rounded-lg font-semibold hover:bg-red-50 transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-minus-circle mr-2"></i>
                            Pengeluaran
                        </a>
                    </div>
                </div>
            </div>
        </x-card-container>

        <!-- Transaction Calendar & Table Section -->
        <x-card-container>
        <div class="p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">
            <i class="fas fa-calendar-alt text-purple-500 mr-2"></i>
            Transaksi Penjualan
            </h3>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Calendar Section -->
            <div class="lg:col-span-4">
                <div class="border border-gray-200 rounded-lg p-4 h-full">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-semibold text-gray-700">Pilih Tanggal</h4>
                    <button id="btnToday" class="text-xs text-purple-600 hover:text-purple-700 font-medium">
                    Hari Ini
                    </button>
                </div>

                <div id="calendar" class="fc-theme-standard rounded-xl shadow-sm border border-gray-200"></div>

                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="text-sm text-gray-600 mb-1">Tanggal Dipilih:</div>
                    <div id="selectedDateText" class="text-lg font-bold text-gray-900">
                    {{ \Carbon\Carbon::parse($selectedDate)->isoFormat('D MMMM Y') }}
                    </div>
                </div>
                </div>
            </div>

            <!-- Transaction Table -->
            <div class="lg:col-span-8">
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="overflow-x-auto" style="max-height: 500px; overflow-y: auto;">
                    <table class="w-full">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Invoice</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Waktu</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Kasir</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Metode</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Total</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Status</th>
                        </tr>
                    </thead>

                    <tbody id="salesTableBody" class="bg-white divide-y divide-gray-200">
                        @forelse($sales as $sale)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $sale->invoice_number }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $sale->created_at->format('H:i') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $sale->cashier->name }}</td>
                            <td class="px-4 py-3">
                            @if($sale->payment_method == 'cash')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-money-bill-wave mr-1"></i> Cash
                                </span>
                            @elseif($sale->payment_method == 'qris')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-qrcode mr-1"></i> QRIS
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                <i class="fas fa-exchange-alt mr-1"></i> Transfer
                                </span>
                            @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">
                            Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                                Selesai
                            </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2 block"></i>
                            <p>Tidak ada transaksi pada tanggal ini</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    </table>
                </div>
                </div>

                <!-- ⬇️ Kartu metode pembayaran dipindah ke bawah tabel, 1 baris & scroll -->
                <div class="mt-4 overflow-x-auto">
                <div id="paymentRow" class="flex gap-3 whitespace-nowrap pb-2">
                    <div class="inline-flex items-center justify-between min-w-[220px] p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-money-bill-wave text-blue-600 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">Cash</span>
                    </div>
                    <span id="cashTotalText" class="text-sm font-bold text-blue-600">Rp {{ number_format($cashTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="inline-flex items-center justify-between min-w-[220px] p-3 bg-green-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-qrcode text-green-600 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">QRIS</span>
                    </div>
                    <span id="qrisTotalText" class="text-sm font-bold text-green-600">Rp {{ number_format($qrisTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="inline-flex items-center justify-between min-w-[220px] p-3 bg-purple-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exchange-alt text-purple-600 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">Transfer</span>
                    </div>
                    <span id="transferTotalText" class="text-sm font-bold text-purple-600">Rp {{ number_format($transferTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="inline-flex items-center justify-between min-w-[240px] p-3 bg-gray-100 rounded-lg border-2 border-gray-300 gap-1">
                    <span class="text-sm font-bold text-gray-900">Total Pendapatan</span>
                    <span id="revenueTotalText" class="text-lg font-bold text-gray-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>
        </x-card-container>

        <!-- Daily Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg p-5 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Pendapatan</p>
                <p id="summaryRevenue" class="text-xl font-bold text-green-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                <p id="summaryDate1" class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-arrow-up text-green-600 text-xl"></i>
            </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-5 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Laba Kotor</p>
                <p id="summaryProfit" class="text-xl font-bold text-blue-600">Rp {{ number_format($dailyProfit, 0, ',', '.') }}</p>
                <p id="summaryDate2" class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-chart-line text-blue-600 text-xl"></i>
            </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-5 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Pengeluaran</p>
                <p id="summaryExpenses" class="text-xl font-bold text-red-600">Rp {{ number_format($dailyExpenses, 0, ',', '.') }}</p>
                <p id="summaryDate3" class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-arrow-down text-red-600 text-xl"></i>
            </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-5 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Laba Bersih</p>
                <p id="summaryNet" class="text-xl font-bold {{ $dailyNetIncome >= 0 ? 'text-purple-600' : 'text-red-600' }}">
                Rp {{ number_format($dailyNetIncome, 0, ',', '.') }}
                </p>
                <p id="summaryDate4" class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-wallet text-purple-600 text-xl"></i>
            </div>
            </div>
        </div>
        </div>


        <!-- Cash Register Report -->
        <x-card-container>
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">
                        <i class="fas fa-cash-register text-purple-500 mr-2"></i>
                        Laporan Kas Register
                    </h3>
                    <a href="#" class="text-sm text-purple-600 hover:text-purple-700 font-medium">
                        <i class="fas fa-plus-circle mr-1"></i>
                        Tambah Laporan
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Kasir</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Dibuka</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Ditutup</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Total Transaksi</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Total Penjualan</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Cash</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">QRIS</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Transfer</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($cashRegisters as $register)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $register->user->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $register->opened_at->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $register->closed_at ? $register->closed_at->format('d M Y H:i') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900">{{ $register->total_transactions }}</td>
                                <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">
                                    Rp {{ number_format($register->total_sales, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right text-blue-600">
                                    Rp {{ number_format($register->total_cash, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right text-green-600">
                                    Rp {{ number_format($register->total_qris, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right text-purple-600">
                                    Rp {{ number_format($register->total_transfer, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($register->status == 'open')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                                        Buka
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                        Tutup
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="px-4 py-12 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2 block"></i>
                                    <p>Belum ada laporan kas register</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($cashRegisters->hasPages())
                <div class="mt-4">
                    {{ $cashRegisters->links() }}
                </div>
                @endif
            </div>
        </x-card-container>

        <!-- Expenses Table -->
        <x-card-container>
            <div class="p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                    <h3 class="text-lg font-bold text-gray-900">
                        <i class="fas fa-receipt text-red-500 mr-2"></i>
                        Daftar Pengeluaran
                    </h3>
                    
                    <form method="GET" class="flex flex-col sm:flex-row gap-2">
                        <input type="hidden" name="date" value="{{ $selectedDate }}">
                        <select name="expense_period" id="expensePeriodSelect" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                            <option value="today" {{ $expensePeriod == 'today' ? 'selected' : '' }}>Hari Ini</option>
                            <option value="week" {{ $expensePeriod == 'week' ? 'selected' : '' }}>Minggu Ini</option>
                            <option value="month" {{ $expensePeriod == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                            <option value="year" {{ $expensePeriod == 'year' ? 'selected' : '' }}>Tahun Ini</option>
                            <option value="custom" {{ $expensePeriod == 'custom' ? 'selected' : '' }}>Periode Kustom</option>
                        </select>
                        <div id="expenseCustomDateRange" class="flex gap-2 {{ $expensePeriod == 'custom' ? '' : 'hidden' }}">
                            <input type="date" name="expense_start_date" value="{{ $expenseStartDate }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                            <input type="date" name="expense_end_date" value="{{ $expenseEndDate }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                        </div>
                        <button type="submit" class="px-5 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm font-medium">
                            <i class="fas fa-filter mr-1"></i>Filter
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Nomor</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Kategori</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Deskripsi</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Metode</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Jumlah</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($expenses as $expense)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $expense->expense_number }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $expense->category->name ?? 'Lainnya' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $expense->description }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 capitalize">{{ $expense->payment_method }}</td>
                                <td class="px-4 py-3 text-sm text-right font-semibold text-red-600">
                                    Rp {{ number_format($expense->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($expense->status == 'approved')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        Disetujui
                                    </span>
                                    @elseif($expense->status == 'pending')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        Ditolak
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2 block"></i>
                                    <p>Tidak ada data pengeluaran</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($expenses->hasPages())
                <div class="mt-4">
                    {{ $expenses->links() }}
                </div>
                @endif
            </div>
        </x-card-container>
    </div>
</main>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<style>
  /* Warna & border global */
  #calendar{
    --fc-border-color:#e5e7eb;
    --fc-neutral-bg-color:#fafafa;
    --fc-today-bg-color:#EEF2FF;
  }

  /* Toolbar - Layout kiri-tengah-kanan dengan background putih */
  .fc .fc-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0;
    padding: 1rem;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-bottom: none;
    border-radius: 0.75rem 0.75rem 0 0;
  }
  
  .fc .fc-toolbar-chunk {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  /* Title di tengah - uppercase dan bold */
  .fc .fc-toolbar-title {
    font-size: 0.875rem;
    font-weight: 700;
    color: #111827;
    text-align: center;
    min-width: 150px;
    text-transform: uppercase;
    letter-spacing: 0.025em;
  }

  /* Sembunyikan tombol today bawaan */
  .fc .fc-today-button {
    display: none !important;
  }

  /* Tombol navigasi - hanya icon chevron */
  .fc .fc-button {
    border-radius: 0.375rem;
    border: 1px solid #e5e7eb;
    background: #fff;
    color: #6b7280;
    box-shadow: none;
    padding: 0.375rem 0.5rem;
    font-size: 1rem;
    transition: all 0.2s;
    min-width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .fc .fc-button:hover {
    background: #f9fafb;
    border-color: #d1d5db;
    color: #111827;
  }
  
  .fc .fc-button-primary:not(:disabled).fc-button-active,
  .fc .fc-button-primary:focus {
    box-shadow: none;
    outline: none;
  }

  /* Grid & sel */
  .fc .fc-scrollgrid {
    border-radius: 0 0 0.75rem 0.75rem;
    overflow: hidden;
    border: 1px solid #e5e7eb;
  }
  
  .fc .fc-daygrid-day-number {
    font-weight: 600;
    padding: 0.5rem;
    font-size: 0.875rem;
  }
  
  .fc .fc-day-today {
    background: #3b83f6ad !important;
  }

  .fc .fc-day-today .fc-daygrid-day-number {
    color: #ffffff !important;
    font-weight: 700;
  }
  
  .fc .fc-daygrid-day:hover {
    background: #eff6ff;
    cursor: pointer;
  }
  
  .fc .fc-daygrid-day-frame {
    min-height: 56px;
  }

  /* Highlight tanggal terpilih */
  .fc .fc-daygrid-day.fc-selected {
    background: #dbeafe !important;
  }

  .fc .fc-daygrid-day.fc-selected .fc-daygrid-day-number {
    color: #1e40af;
    font-weight: 700;
  }

  /* Header hari - uppercase, abu-abu muda */
  .fc .fc-col-header-cell {
    padding: 0.75rem 0.25rem;
    font-weight: 600;
    font-size: 0.6875rem;
    text-transform: uppercase;
    color: #6b7280;
    background: #f9fafb;
    border-color: #e5e7eb;
    letter-spacing: 0.025em;
  }

  /* Responsive - Mobile */
  @media (max-width: 640px) {
    .fc .fc-toolbar {
      padding: 0.75rem;
    }

    .fc .fc-toolbar-title {
      font-size: 0.75rem;
      min-width: 120px;
    }
    
    .fc .fc-button {
      padding: 0.25rem 0.375rem;
      font-size: 0.875rem;
      min-width: 28px;
      height: 28px;
    }
    
    .fc .fc-daygrid-day-frame {
      min-height: 42px;
    }
    
    .fc .fc-daygrid-day-number {
      font-size: 0.75rem;
      padding: 0.375rem;
    }
    
    .fc .fc-col-header-cell {
      font-size: 0.625rem;
      padding: 0.5rem 0.125rem;
    }
  }

  /* Extra small screens */
  @media (max-width: 380px) {
    .fc .fc-toolbar-title {
      font-size: 0.6875rem;
      min-width: 100px;
    }
    
    .fc .fc-button {
      padding: 0.25rem;
      min-width: 24px;
      height: 24px;
    }
  }
</style>
@endpush


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
  const rupiah = (n) => new Intl.NumberFormat('id-ID', {style:'currency', currency:'IDR', maximumFractionDigits:0}).format(n);

  function methodBadge(method) {
    if (method === 'cash')    return `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"><i class="fas fa-money-bill-wave mr-1"></i>Cash</span>`;
    if (method === 'qris')    return `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-qrcode mr-1"></i>QRIS</span>`;
    return `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800"><i class="fas fa-exchange-alt mr-1"></i>Transfer</span>`;
  }

  function setLoading(isLoading) {
    const tbody = document.getElementById('salesTableBody');
    if (isLoading) {
      tbody.innerHTML = `
        <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500">
          <i class="fas fa-circle-notch fa-spin text-xl mr-2"></i> Memuat data...
        </td></tr>`;
    }
  }

  function refreshUI(payload) {
    const dateLabel = new Date(payload.selectedDate + 'T00:00:00');
    const dateText = dateLabel.toLocaleDateString('id-ID', { day:'numeric', month:'long', year:'numeric' });
    document.getElementById('selectedDateText').innerText = dateText;
    ['summaryDate1','summaryDate2','summaryDate3','summaryDate4'].forEach(id => {
      document.getElementById(id).innerText = dateLabel.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' });
    });

    // Tabel sales
    const tbody = document.getElementById('salesTableBody');
    if (!payload.sales.length) {
      tbody.innerHTML = `
        <tr>
          <td colspan="6" class="px-4 py-12 text-center text-gray-500">
            <i class="fas fa-inbox text-4xl mb-2 block"></i>
            <p>Tidak ada transaksi pada tanggal ini</p>
          </td>
        </tr>`;
    } else {
      tbody.innerHTML = payload.sales.map(s => `
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-3 text-sm font-medium text-gray-900">${s.invoice_number}</td>
          <td class="px-4 py-3 text-sm text-gray-600">${s.time}</td>
          <td class="px-4 py-3 text-sm text-gray-600">${s.cashier ?? '-'}</td>
          <td class="px-4 py-3">${methodBadge(s.payment_method)}</td>
          <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">${rupiah(s.grand_total)}</td>
          <td class="px-4 py-3 text-center">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
              <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span> Selesai
            </span>
          </td>
        </tr>`).join('');
    }

    // Kartu metode pembayaran (1 baris)
    document.getElementById('cashTotalText').innerText     = rupiah(payload.totals.cash);
    document.getElementById('qrisTotalText').innerText     = rupiah(payload.totals.qris);
    document.getElementById('transferTotalText').innerText = rupiah(payload.totals.transfer);
    document.getElementById('revenueTotalText').innerText  = rupiah(payload.totals.revenue);

    // Ringkasan
    document.getElementById('summaryRevenue').innerText  = rupiah(payload.totals.revenue);
    document.getElementById('summaryProfit').innerText   = rupiah(payload.summary.profit);
    document.getElementById('summaryExpenses').innerText = rupiah(payload.summary.expenses);
    const net = payload.summary.net;
    const netEl = document.getElementById('summaryNet');
    netEl.innerText = rupiah(net);
    netEl.classList.toggle('text-purple-600', net >= 0);
    netEl.classList.toggle('text-red-600', net < 0);
  }

  async function loadDaily(dateStr) {
    setLoading(true);
    try {
      const res = await fetch(`{{ route('finance.daily') }}?date=${dateStr}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      const json = await res.json();
      refreshUI(json);
    } catch (e) {
      const tbody = document.getElementById('salesTableBody');
      tbody.innerHTML = `<tr><td colspan="6" class="px-4 py-10 text-center text-red-500">
        Gagal memuat data.
      </td></tr>`;
      console.error(e);
    }
  }

  // Inisialisasi kalender
  document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialDate: '{{ $selectedDate }}',
      initialView: 'dayGridMonth',
      headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
      locale: 'id',
      firstDay: 1,           // mulai Senin
      dayMaxEventRows: true, // biar rapi
      selectable: true,
      dateClick: (info) => loadDaily(info.dateStr),
    });
    calendar.render();

    // Tombol "Hari Ini"
    document.getElementById('btnToday').addEventListener('click', () => {
      const today = new Date().toISOString().slice(0,10);
      calendar.today();
      loadDaily(today);
    });
  });
</script>

<script>
// Expense period filter
document.getElementById('expensePeriodSelect').addEventListener('change', function() {
    const customRange = document.getElementById('expenseCustomDateRange');
    customRange.classList.toggle('hidden', this.value !== 'custom');
});

// Date selector functions
function selectDate(date) {
    window.location.href = '{{ route("finance.index") }}?date=' + date;
}

function selectToday() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('dateSelector').value = today;
    selectDate(today);
}
</script>
@endpush
@endsection
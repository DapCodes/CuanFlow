@extends('layouts.app')

@section('title', 'Penjualan - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Penjualan</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">
        
        {{-- Alert / Notifikasi (diseragamkan gaya-nya dengan halaman diskon) --}}
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

        {{-- HEADER HALAMAN (diseragamkan dengan pola di discounts.index) --}}
        <section
            class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-500 border border-red-100">
                        <i class="fas fa-shopping-cart text-sm"></i>
                    </span>
                    <span>Manajemen Penjualan</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Pantau dan kelola transaksi penjualan harian dengan tampilan yang rapi dan konsisten.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                <a href="{{ route('withdraw.confirm-password') }}"
                   class="inline-flex items-center justify-center rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 transition-all">
                    <i class="fas fa-wallet mr-2"></i>
                    Tarik Saldo
                </a>
                <div class="text-right border-l pl-3 border-gray-200">
                    <p class="text-xs text-gray-500 font-medium mb-1">Tanggal terpilih</p>
                    <p class="text-sm font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($selectedDate)->isoFormat('D MMMM Y') }}
                    </p>
                </div>
            </div>
        </section>

        {{-- RINGKASAN STATISTIK HARIAN (layout mengikuti diskon, isi tetap seperti semula) --}}
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Penjualan</p>
                        <p id="summaryRevenue" class="mt-1 text-2xl font-semibold text-green-600">
                            Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                        </p>
                        <p id="summaryDate1" class="text-xs text-gray-400 mt-1">
                            {{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}
                        </p>
                    </div>
                    <div
                        class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center border border-green-200">
                        <i class="fas fa-shopping-cart text-green-600 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Transaksi</p>
                        <p id="summaryTransactions" class="mt-1 text-2xl font-semibold text-blue-600">
                            {{ $totalTransactions }}
                        </p>
                        <p id="summaryDate2" class="text-xs text-gray-400 mt-1">
                            {{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}
                        </p>
                    </div>
                    <div
                        class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center border border-blue-200">
                        <i class="fas fa-receipt text-blue-600 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Laba Kotor</p>
                        <p id="summaryProfit" class="mt-1 text-2xl font-semibold text-purple-600">
                            Rp {{ number_format($dailyProfit, 0, ',', '.') }}
                        </p>
                        <p id="summaryDate3" class="text-xs text-gray-400 mt-1">
                            {{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}
                        </p>
                    </div>
                    <div
                        class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center border border-purple-200">
                        <i class="fas fa-chart-line text-purple-600 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Refund</p>
                        <p id="summaryRefunds" class="mt-1 text-2xl font-semibold text-red-600">
                            Rp {{ number_format($totalRefunds, 0, ',', '.') }}
                        </p>
                        <p id="summaryDate4" class="text-xs text-gray-400 mt-1">
                            {{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}
                        </p>
                    </div>
                    <div
                        class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center border border-red-200">
                        <i class="fas fa-undo text-red-600 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm" hidden>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Diskon</p>
                        <p id="summaryDiscount" class="mt-1 text-2xl font-semibold text-orange-600">
                            Rp {{ number_format($dailyTotalDiscount ?? 0, 0, ',', '.') }}
                        </p>
                        <p id="summaryDate5" class="text-xs text-gray-400 mt-1">
                            {{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}
                        </p>
                    </div>
                    <div
                        class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center border border-orange-200">
                        <i class="fas fa-tags text-orange-600 text-lg"></i>
                    </div>
                </div>
            </div>
        </section>

        {{-- KONTEN UTAMA: KALENDER + TABEL TRANSAKSI (layout & padding seragam dengan diskon) --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            {{-- Header / Toolbar kecil --}}
            <div class="border-b border-gray-200 px-4 md:px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-500 border border-red-100">
                        <i class="fas fa-calendar-alt text-xs"></i>
                    </span>
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900 sm:text-base">
                            Transaksi Penjualan
                        </h2>
                        <p class="text-xs text-gray-500">
                            Pilih tanggal pada kalender untuk melihat transaksi penjualan.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" id="searchInvoice" placeholder="Cari invoice..." 
                               class="pl-8 pr-4 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 w-48">
                    </div>
                    <button id="btnToday"
                        class="inline-flex items-center justify-center rounded-lg bg-red-500 px-3 py-2 text-xs font-semibold text-white hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-1">
                        <i class="fas fa-calendar-day mr-1 text-[11px]"></i>
                        Hari Ini
                    </button>
                </div>
            </div>

            {{-- Grid Kalender + Tabel --}}
            <div class="px-4 md:px-6 py-5">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    {{-- Calendar Section --}}
                    <div class="lg:col-span-5">
                        <div class="border border-gray-200 rounded-xl bg-white">
                            <div class="px-4 pt-4 pb-2 flex items-center justify-between">
                                <h4 class="font-semibold text-gray-700 text-sm">Pilih Tanggal</h4>
                            </div>

                            <div id="calendar"
                                 class="fc-theme-standard rounded-xl border-t border-gray-200"></div>

                            <div class="mt-0 px-4 pb-4 pt-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                                <div class="text-xs text-gray-500 mb-1 font-medium">Tanggal Dipilih</div>
                                <div id="selectedDateText" class="text-base font-semibold text-gray-900">
                                    {{ \Carbon\Carbon::parse($selectedDate)->isoFormat('D MMMM Y') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Transaction Table --}}
                    <div class="lg:col-span-7">
                        <div
                            class="border border-gray-200 rounded-xl overflow-hidden bg-white mb-4 shadow-sm">
                            <div class="overflow-x-auto" style="max-height: 500px; overflow-y: auto;">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50 sticky top-0 border-b border-gray-200">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                                Invoice
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                                Waktu
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                                Kasir
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                                Metode
                                            </th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                                Diskon
                                            </th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                                Total
                                            </th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                                Status
                                            </th>
                                            @if(auth()->user()->can('lihat detail penjualan') || auth()->user()->can('refund penjualan'))
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide w-32">
                                                Aksi
                                            </th>
                                            @endif
                                        </tr>
                                    </thead>

                                    <tbody id="salesTableBody" class="bg-white divide-y divide-gray-100">
                                        @forelse($sales as $sale)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                                    {{ $sale->invoice_number }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-600">
                                                    {{ $sale->created_at->format('H:i') }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-600">
                                                    {{ $sale->cashier->name }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    @if($sale->payment_method == 'cash')
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            <i class="fas fa-money-bill-wave mr-1"></i> Cash
                                                        </span>
                                                    @elseif($sale->payment_method == 'qris')
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <i class="fas fa-qrcode mr-1"></i> QRIS
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                            <i class="fas fa-exchange-alt mr-1"></i> Transfer
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-sm text-right font-medium text-orange-600">
                                                    Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">
                                                    Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @if($sale->status == 'completed')
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                            <span
                                                                class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                                                            Selesai
                                                        </span>
                                                    @elseif($sale->status == 'refunded')
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                            <span
                                                                class="w-2 h-2 bg-red-500 rounded-full mr-1"></span>
                                                            Dikembalikan
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                                            <span
                                                                class="w-2 h-2 bg-yellow-500 rounded-full mr-1"></span>
                                                            {{ ucfirst($sale->status) }}
                                                        </span>
                                                    @endif
                                                </td>
                                                @if(auth()->user()->can('lihat detail penjualan') || auth()->user()->can('refund penjualan'))
                                                <td class="px-4 py-3 text-center">
                                                    <div class="flex items-center justify-center gap-2">
                                                        @can('lihat detail penjualan')
                                                        <a href="{{ route('sales.show', $sale->id) }}"
                                                           class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-md text-xs font-medium transition-colors">
                                                            <i class="fas fa-eye mr-1"></i>Detail
                                                        </a>
                                                        @endcan
                                                        @if($sale->status == 'completed' && in_array($sale->payment_method, ['cash', 'transfer']))
                                                            @can('refund penjualan')
                                                            <button
                                                                onclick="confirmRefund('{{ $sale->id }}', '{{ $sale->invoice_number }}', {{ $sale->grand_total }})"
                                                                class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-md text-xs font-medium transition-colors">
                                                                <i class="fas fa-undo mr-1"></i>
                                                                Refund
                                                            </button>
                                                            @endcan
                                                        @endif
                                                    </div>
                                                </td>
                                                @endif
                                            </tr>
                                        @empty
                                            <tr>
                                            <tr>
                                                <td colspan="8"
                                                    class="px-4 py-12 text-center text-gray-500">
                                                    <div class="flex flex-col items-center justify-center">
                                                        <i class="fas fa-inbox text-4xl mb-2 block text-gray-300"></i>
                                                        <p class="text-sm">Tidak ada transaksi pada tanggal ini</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Payment method cards (diseragamkan gaya card-nya) --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-4 gap-3">
                            <div
                                class="flex items-center justify-between px-3 py-3 bg-blue-50 rounded-xl border border-blue-100">
                                <div class="flex items-center">
                                    <i class="fas fa-money-bill-wave text-blue-600 mr-2"></i>
                                </div>
                                <span id="cashTotalText"
                                      class="text-sm font-bold text-blue-600">
                                    Rp {{ number_format($cashTotal, 0, ',', '.') }}
                                </span>
                            </div>
                            <div
                                class="flex items-center justify-between px-3 py-3 bg-green-50 rounded-xl border border-green-100">
                                <div class="flex items-center">
                                    <i class="fas fa-qrcode text-green-600 mr-2"></i>
                                </div>
                                <span id="qrisTotalText"
                                      class="text-sm font-bold text-green-600">
                                    Rp {{ number_format($qrisTotal, 0, ',', '.') }}
                                </span>
                            </div>
                            <div
                                class="flex items-center justify-between px-3 py-3 bg-purple-50 rounded-xl border border-purple-100">
                                <div class="flex items-center">
                                    <i class="fas fa-exchange-alt text-purple-600 mr-2"></i>
                                </div>
                                <span id="transferTotalText"
                                      class="text-sm font-bold text-purple-600">
                                    Rp {{ number_format($transferTotal, 0, ',', '.') }}
                                </span>
                            </div>
                            <div
                                class="flex items-center justify-between px-3 py-3 bg-gray-100 rounded-xl border-2 border-gray-300 gap-1">
                                <span class="text-sm font-bold text-gray-900">Total</span>
                                <span id="revenueTotalText"
                                      class="text-sm font-bold text-gray-900">
                                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>

{{-- Refund Confirmation Modal (kelas sedikit dirapikan, fungsi tetap sama) --}}
<div id="refundModal"
     class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 w-full max-w-md">
        <div class="border shadow-lg rounded-xl bg-white px-5 py-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                <i class="fas fa-undo text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 text-center mt-4">Konfirmasi Refund</h3>
            <div class="mt-4 text-sm text-gray-600">
                <p class="text-center">Apakah Anda yakin ingin melakukan refund untuk:</p>
                <div class="bg-gray-50 rounded-lg p-3 mt-3">
                    <p class="font-medium text-gray-900">
                        Invoice: <span id="refundInvoice"></span>
                    </p>
                    <p class="font-bold text-red-600 text-lg mt-1">
                        Total: <span id="refundAmount"></span>
                    </p>
                </div>
                <p class="text-center mt-3 text-red-600 font-medium">
                    Stok produk akan dikembalikan dan uang akan dikembalikan ke kasir.
                </p>
            </div>
            <div class="flex gap-3 mt-5">
                <button onclick="closeRefundModal()"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-medium">
                    Batal
                </button>
                <form id="refundForm" method="POST" class="flex-1">
                    @csrf
                    <button type="submit"
                            class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                        Ya, Refund
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<style>
  /* Calendar Variables */
  #calendar {
    --fc-border-color: #e5e7eb;
    --fc-neutral-bg-color: #fafafa;
    --fc-today-bg-color: #FEE2E2;
  }

  /* Toolbar */
  .fc .fc-toolbar {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin-bottom: 0;
    padding: 0.75rem 1rem;
    background: #ffffff;
    border-bottom: 1px solid #e5e7eb;
    border-radius: 0.75rem 0.75rem 0 0;
  }
  
  .fc .fc-toolbar-chunk {
    display: flex;
    align-items: center;
  }

  /* Center title with buttons on sides */
  .fc .fc-toolbar-chunk:nth-child(1) {
    order: 1;
  }
  
  .fc .fc-toolbar-chunk:nth-child(2) {
    order: 2;
    flex: 1;
    justify-content: center;
  }
  
  .fc .fc-toolbar-chunk:nth-child(3) {
    order: 3;
  }
  
  .fc .fc-toolbar-title {
    font-size: 0.875rem;
    font-weight: 700;
    color: #111827;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    white-space: nowrap;
  }

  .fc .fc-today-button {
    display: none !important;
  }

  /* Navigation Buttons */
  .fc .fc-button {
    border-radius: 0.375rem;
    border: 1px solid #e5e7eb;
    background: #fff;
    color: #6b7280;
    box-shadow: none;
    padding: 0.375rem 0.625rem;
    font-size: 0.875rem;
    transition: all 0.2s;
    min-width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .fc .fc-button:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
    color: #111827;
  }
  
  .fc .fc-button-primary:not(:disabled).fc-button-active,
  .fc .fc-button-primary:focus {
    box-shadow: none;
    outline: none;
  }

  /* Calendar Grid */
  .fc .fc-scrollgrid {
    border-radius: 0 0 0.75rem 0.75rem;
    overflow: hidden;
    border: 1px solid #e5e7eb;
  }

  /* Disable scrolling */
  .fc .fc-scroller {
    overflow: hidden !important;
  }

  .fc .fc-scroller-liquid-absolute {
    position: relative !important;
  }
  
  /* Day Numbers */
  .fc .fc-daygrid-day-number {
    font-weight: 600;
    padding: 0.375rem;
    font-size: 0.8125rem;
    text-align: center;
    width: 100%;
  }
  
  /* Today styling */
  .fc .fc-day-today {
    background: #fca5a5ad !important;
  }

  .fc .fc-day-today .fc-daygrid-day-number {
    color: #ffffff !important;
    font-weight: 700;
  }
  
  /* Hover effect */
  .fc .fc-daygrid-day:hover {
    background: #fee2e2;
    cursor: pointer;
  }
  
  /* Day cell height - compact */
  .fc .fc-daygrid-day-frame {
    min-height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* Selected day */
  .fc .fc-daygrid-day.fc-selected {
    background: #fecaca !important;
  }

  .fc .fc-daygrid-day.fc-selected .fc-daygrid-day-number {
    color: #991b1b;
    font-weight: 700;
  }

  /* Day headers */
  .fc .fc-col-header-cell {
    padding: 0.5rem 0.25rem;
    font-weight: 600;
    font-size: 0.6875rem;
    text-transform: uppercase;
    color: #6b7280;
    background: #f9fafb;
    border-color: #e5e7eb;
    letter-spacing: 0.025em;
  }

  /* Remove extra spacing */
  .fc .fc-daygrid-day-top {
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .fc .fc-daygrid-day-events {
    display: none;
  }

  /* Responsive Design */
  @media (max-width: 1024px) {
    .fc .fc-toolbar {
      padding: 0.625rem 0.75rem;
    }

    .fc .fc-toolbar-title {
      font-size: 0.8125rem;
    }

    .fc .fc-daygrid-day-frame {
      min-height: 40px;
    }

    .fc .fc-daygrid-day-number {
      font-size: 0.75rem;
      padding: 0.25rem;
    }
  }

  @media (max-width: 640px) {
    .fc .fc-toolbar {
      padding: 0.5rem 0.625rem;
      gap: 0.5rem;
    }

    .fc .fc-toolbar-title {
      font-size: 0.75rem;
    }
    
    .fc .fc-button {
      padding: 0.25rem 0.5rem;
      font-size: 0.75rem;
      min-width: 28px;
      height: 28px;
    }
    
    .fc .fc-daygrid-day-frame {
      min-height: 36px;
    }
    
    .fc .fc-daygrid-day-number {
      font-size: 0.6875rem;
      padding: 0.25rem;
    }
    
    .fc .fc-col-header-cell {
      font-size: 0.625rem;
      padding: 0.375rem 0.125rem;
    }
  }

  @media (max-width: 480px) {
    .fc .fc-toolbar-title {
      font-size: 0.6875rem;
    }

    .fc .fc-button {
      padding: 0.25rem 0.375rem;
      min-width: 24px;
      height: 24px;
      font-size: 0.6875rem;
    }

    .fc .fc-daygrid-day-frame {
      min-height: 32px;
    }

    .fc .fc-daygrid-day-number {
      font-size: 0.625rem;
      padding: 0.125rem;
    }
  }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
    window.permissions = {
        lihatDetail: @json(auth()->user()->can('lihat detail penjualan')),
        refundPenjualan: @json(auth()->user()->can('refund penjualan')),
    };
</script>
<script>
  const rupiah = (n) => new Intl.NumberFormat('id-ID', {style:'currency', currency:'IDR', maximumFractionDigits:0}).format(n);

  function methodBadge(method) {
    if (method === 'cash')    return `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"><i class="fas fa-money-bill-wave mr-1"></i>Cash</span>`;
    if (method === 'qris')    return `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-qrcode mr-1"></i>QRIS</span>`;
    return `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800"><i class="fas fa-exchange-alt mr-1"></i>Transfer</span>`;
  }

  function statusBadge(status) {
    if (status === 'completed') return `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800"><span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>Selesai</span>`;
    if (status === 'refunded') return `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800"><span class="w-2 h-2 bg-red-500 rounded-full mr-1"></span>Dikembalikan</span>`;
    return `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800"><span class="w-2 h-2 bg-yellow-500 rounded-full mr-1"></span>${status}</span>`;
  }

  function confirmRefund(saleId, invoiceNumber, amount) {
    document.getElementById('refundInvoice').innerText = invoiceNumber;
    document.getElementById('refundAmount').innerText = rupiah(amount);
    document.getElementById('refundForm').action = `/sales/${saleId}/refund`;
    document.getElementById('refundModal').classList.remove('hidden');
  }

  function closeRefundModal() {
    document.getElementById('refundModal').classList.add('hidden');
  }

  function setLoading(isLoading) {
    const tbody = document.getElementById('salesTableBody');
    if (isLoading) {
      tbody.innerHTML = `<tr><td colspan="8" class="px-4 py-10 text-center text-gray-500"><i class="fas fa-circle-notch fa-spin text-xl mr-2"></i> Memuat data...</td></tr>`;
    }
  }

  // Declare calendar variable in wider scope accessed by multiple functions
  let calendar;

  function refreshUI(payload) {
    // If dates mismatch, update calendar view
    if (payload.selectedDate !== currentSelectedDate) {
        currentSelectedDate = payload.selectedDate;
        if (calendar) {
            calendar.gotoDate(currentSelectedDate);
            calendar.select(currentSelectedDate);
        }
    }

    const dateLabel = new Date(payload.selectedDate + 'T00:00:00');
    const dateText = dateLabel.toLocaleDateString('id-ID', { day:'numeric', month:'long', year:'numeric' });
    document.getElementById('selectedDateText').innerText = dateText;
    
    ['summaryDate1','summaryDate2','summaryDate3','summaryDate4', 'summaryDate5'].forEach(id => {
      document.getElementById(id).innerText = dateLabel.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' });
    });

    const tbody = document.getElementById('salesTableBody');
    if (!payload.sales.length) {
      tbody.innerHTML = `<tr><td colspan="8" class="px-4 py-12 text-center text-gray-500"><i class="fas fa-inbox text-4xl mb-2 block"></i><p>Tidak ada transaksi pada tanggal ini</p></td></tr>`;
    } else {
      const showAksi = window.permissions.lihatDetail || window.permissions.refundPenjualan;
      tbody.innerHTML = payload.sales.map(s => {
        let actionColumn = '';
        if (showAksi) {
          actionColumn = `
            <td class="px-4 py-3 text-center">
              <div class="flex items-center justify-center gap-2">
                ${window.permissions.lihatDetail ? `
                <a href="/sales/${s.id}" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-md text-xs font-medium transition-colors">
                  <i class="fas fa-eye mr-1"></i>Detail
                </a>` : ''}
                ${(window.permissions.refundPenjualan && s.status === 'completed' && ['cash', 'transfer'].includes(s.payment_method))
                  ? `<button onclick="confirmRefund('${s.id}', '${s.invoice_number}', ${s.grand_total})" class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-md text-xs font-medium transition-colors"><i class="fas fa-undo mr-1"></i>Refund</button>`
                  : ''
                }
              </div>
            </td>`;
        }
        
        // Check for highlight
        const isHighlighted = payload.highlightId == s.id;
        const rowClass = isHighlighted ? 'bg-yellow-100 border-l-4 border-yellow-500' : 'hover:bg-gray-50';
        
        return `
        <tr id="row-${s.id}" class="${rowClass} transition-colors duration-1000">
          <td class="px-4 py-3 text-sm font-medium text-gray-900">${s.invoice_number}</td>
          <td class="px-4 py-3 text-sm text-gray-600">${s.time}</td>
          <td class="px-4 py-3 text-sm text-gray-600">${s.cashier ?? '-'}</td>
          <td class="px-4 py-3">${methodBadge(s.payment_method)}</td>
          <td class="px-4 py-3 text-sm text-right font-medium text-orange-600">${rupiah(s.total_discount)}</td>
          <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">${rupiah(s.grand_total)}</td>
          <td class="px-4 py-3 text-center">${statusBadge(s.status)}</td>
          ${actionColumn}
        </tr>`;
      }).join('');
      
      // Scroll to highlighted element if exists
      if (payload.highlightId) {
          const row = document.getElementById(`row-${payload.highlightId}`);
          if (row) {
              setTimeout(() => {
                  row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                  // Remove highlight after 2 seconds
                  setTimeout(() => {
                      row.classList.remove('bg-yellow-100', 'border-l-4', 'border-yellow-500');
                      row.classList.add('hover:bg-gray-50');
                  }, 3000);
              }, 100);
          }
      }
    }

    document.getElementById('cashTotalText').innerText = rupiah(payload.totals.cash);
    document.getElementById('qrisTotalText').innerText = rupiah(payload.totals.qris);
    document.getElementById('transferTotalText').innerText = rupiah(payload.totals.transfer);
    document.getElementById('revenueTotalText').innerText = rupiah(payload.totals.revenue);

    document.getElementById('summaryRevenue').innerText = rupiah(payload.summary.revenue);
    document.getElementById('summaryTransactions').innerText = payload.summary.transactions;
    document.getElementById('summaryProfit').innerText = rupiah(payload.summary.profit);
    document.getElementById('summaryRefunds').innerText = rupiah(payload.summary.refunds);
    document.getElementById('summaryDiscount').innerText = rupiah(payload.summary.discount);
  }

  let currentSelectedDate = '{{ $selectedDate }}';
  let searchTimeout = null;

  async function loadDaily(dateStr) {
    // Keep requested date but might change based on response
    currentSelectedDate = dateStr; 
    setLoading(true);
    
    // Get search value
    const search = document.getElementById('searchInvoice').value;
    
    try {
      const res = await fetch(`/sales/daily?date=${dateStr}&search=${search}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      const json = await res.json();
      refreshUI(json);
    } catch (e) {
      const tbody = document.getElementById('salesTableBody');
      tbody.innerHTML = `<tr><td colspan="8" class="px-4 py-10 text-center text-red-500">Gagal memuat data.</td></tr>`;
      console.error(e);
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
      initialDate: '{{ $selectedDate }}',
      initialView: 'dayGridMonth',
      headerToolbar: { 
        left: 'prev', 
        center: 'title', 
        right: 'next' 
      },
      locale: 'id',
      firstDay: 1,
      height: 'auto',
      contentHeight: 'auto',
      dayMaxEventRows: false,
      selectable: true,
      fixedWeekCount: false,
      dateClick: (info) => loadDaily(info.dateStr),
    });
    calendar.render();

    document.getElementById('btnToday').addEventListener('click', () => {
      const today = new Date().toISOString().slice(0,10);
      calendar.today();
      loadDaily(today);
    });

    // Search Handler
    document.getElementById('searchInvoice').addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadDaily(currentSelectedDate);
        }, 500);
    });

    // Handle Refund Form AJAX
    const refundForm = document.getElementById('refundForm');
    if(refundForm){
        refundForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Get button to show loading state
            const btn = this.querySelector('button[type="submit"]');
            const originalContent = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

            try {
                const action = this.action;
                const token = this.querySelector('input[name="_token"]').value;

                const res = await fetch(action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json();

                if (data.success) {
                    closeRefundModal();
                    
                    // Show success notification
                    const container = document.querySelector('main .max-w-7xl');
                    const alertHtml = `
                        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 flex items-start gap-3 text-sm animate-fadeIn">
                            <i class="fas fa-check-circle mt-0.5 text-green-500"></i>
                            <p class="text-green-800">${data.message}</p>
                        </div>
                    `;
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = alertHtml;
                    container.insertBefore(tempDiv.firstElementChild, container.firstChild);

                    // Refresh data
                    loadDaily(currentSelectedDate);

                    // Scroll to top
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    alert(data.message || 'Gagal melakukan refund');
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan saat memproses refund');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalContent;
            }
        });
    }
  });
</script>
@endpush
@endsection

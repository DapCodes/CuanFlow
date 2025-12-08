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

        @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-red-500 mt-1 mr-3"></i>
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        <!-- Header Section -->
        <x-card-container>
            <div class="bg-gradient-to-br from-pink-40 to-red-50 p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-shopping-cart text-red-500 mr-3"></i>
                            Manajemen Penjualan
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Pantau dan kelola transaksi penjualan</p>
                    </div>
                </div>
            </div>
        </x-card-container>

        <!-- Transaction Calendar & Table Section -->
        <x-card-container>
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-calendar-alt text-red-500 mr-2"></i>
                    Transaksi Penjualan
                </h3>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Calendar Section -->
                    <div class="lg:col-span-5">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-gray-700">Pilih Tanggal</h4>
                                <button id="btnToday" class="text-xs text-red-600 hover:text-red-700 font-medium">
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
                    <div class="lg:col-span-7">
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
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase w-32">Aksi</th>
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
                                                @if($sale->status == 'completed')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                                                        Selesai
                                                    </span>
                                                @elseif($sale->status == 'refunded')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                        <span class="w-2 h-2 bg-red-500 rounded-full mr-1"></span>
                                                        Dikembalikan
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                                        <span class="w-2 h-2 bg-yellow-500 rounded-full mr-1"></span>
                                                        {{ ucfirst($sale->status) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <a href="{{ route('sales.show', $sale->id) }}" 
                                                       class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-md text-xs font-medium transition-colors">
                                                        <i class="fas fa-eye mr-1"></i>Detail
                                                    </a>
                                                    @if($sale->status == 'completed' && in_array($sale->payment_method, ['cash', 'transfer']))
                                                        <button onclick="confirmRefund('{{ $sale->id }}', '{{ $sale->invoice_number }}', {{ $sale->grand_total }})" 
                                                                class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-md text-xs font-medium transition-colors">
                                                            <i class="fas fa-undo mr-1"></i>
                                                            Refund
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                                                <i class="fas fa-inbox text-4xl mb-2 block"></i>
                                                <p>Tidak ada transaksi pada tanggal ini</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Payment method cards -->
                        <div class="mt-4">
                            <div id="paymentRow" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-4 gap-3">
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-money-bill-wave text-blue-600 mr-2"></i>
                                    </div>
                                    <span id="cashTotalText" class="text-sm font-bold text-blue-600">Rp {{ number_format($cashTotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-qrcode text-green-600 mr-2"></i>
                                    </div>
                                    <span id="qrisTotalText" class="text-sm font-bold text-green-600">Rp {{ number_format($qrisTotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-exchange-alt text-purple-600 mr-2"></i>
                                    </div>
                                    <span id="transferTotalText" class="text-sm font-bold text-purple-600">Rp {{ number_format($transferTotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-gray-100 rounded-lg border-2 border-gray-300 gap-1">
                                    <span class="text-sm font-bold text-gray-900">Total</span>
                                    <span id="revenueTotalText" class="text-sm font-bold text-gray-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
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
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Total Penjualan</p>
                        <p id="summaryRevenue" class="text-xl font-bold text-green-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                        <p id="summaryDate1" class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg p-5 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Total Transaksi</p>
                        <p id="summaryTransactions" class="text-xl font-bold text-blue-600">{{ $totalTransactions }}</p>
                        <p id="summaryDate2" class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-receipt text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg p-5 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Laba Kotor</p>
                        <p id="summaryProfit" class="text-xl font-bold text-purple-600">Rp {{ number_format($dailyProfit, 0, ',', '.') }}</p>
                        <p id="summaryDate3" class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg p-5 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Refund</p>
                        <p id="summaryRefunds" class="text-xl font-bold text-red-600">Rp {{ number_format($totalRefunds, 0, ',', '.') }}</p>
                        <p id="summaryDate4" class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-undo text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Refund Confirmation Modal -->
<div id="refundModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                <i class="fas fa-undo text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 text-center mt-4">Konfirmasi Refund</h3>
            <div class="mt-4 text-sm text-gray-600">
                <p class="text-center">Apakah Anda yakin ingin melakukan refund untuk:</p>
                <div class="bg-gray-50 rounded-lg p-3 mt-3">
                    <p class="font-medium text-gray-900">Invoice: <span id="refundInvoice"></span></p>
                    <p class="font-bold text-red-600 text-lg mt-1">Total: <span id="refundAmount"></span></p>
                </div>
                <p class="text-center mt-3 text-red-600 font-medium">Stok produk akan dikembalikan dan uang akan dikembalikan ke kasir.</p>
            </div>
            <div class="flex gap-3 mt-5">
                <button onclick="closeRefundModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-medium">
                    Batal
                </button>
                <form id="refundForm" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
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
    border: 1px solid #e5e7eb;
    border-bottom: none;
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
      tbody.innerHTML = `<tr><td colspan="7" class="px-4 py-10 text-center text-gray-500"><i class="fas fa-circle-notch fa-spin text-xl mr-2"></i> Memuat data...</td></tr>`;
    }
  }

  function refreshUI(payload) {
    const dateLabel = new Date(payload.selectedDate + 'T00:00:00');
    const dateText = dateLabel.toLocaleDateString('id-ID', { day:'numeric', month:'long', year:'numeric' });
    document.getElementById('selectedDateText').innerText = dateText;
    
    ['summaryDate1','summaryDate2','summaryDate3','summaryDate4'].forEach(id => {
      document.getElementById(id).innerText = dateLabel.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' });
    });

    const tbody = document.getElementById('salesTableBody');
    if (!payload.sales.length) {
      tbody.innerHTML = `<tr><td colspan="7" class="px-4 py-12 text-center text-gray-500"><i class="fas fa-inbox text-4xl mb-2 block"></i><p>Tidak ada transaksi pada tanggal ini</p></td></tr>`;
    } else {
      tbody.innerHTML = payload.sales.map(s => `
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-3 text-sm font-medium text-gray-900">${s.invoice_number}</td>
          <td class="px-4 py-3 text-sm text-gray-600">${s.time}</td>
          <td class="px-4 py-3 text-sm text-gray-600">${s.cashier ?? '-'}</td>
          <td class="px-4 py-3">${methodBadge(s.payment_method)}</td>
          <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">${rupiah(s.grand_total)}</td>
          <td class="px-4 py-3 text-center">${statusBadge(s.status)}</td>
          <td class="px-4 py-3 text-center">
            <div class="flex items-center justify-center gap-2">
              <a href="/sales/${s.id}" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-md text-xs font-medium transition-colors">
                <i class="fas fa-eye mr-1"></i>Detail
              </a>
              ${s.status === 'completed' && ['cash', 'transfer'].includes(s.payment_method) 
                ? `<button onclick="confirmRefund('${s.id}', '${s.invoice_number}', ${s.grand_total})" class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-md text-xs font-medium transition-colors"><i class="fas fa-undo mr-1"></i>Refund</button>`
                : ''
              }
            </div>
          </td>
        </tr>`).join('');
    }

    document.getElementById('cashTotalText').innerText = rupiah(payload.totals.cash);
    document.getElementById('qrisTotalText').innerText = rupiah(payload.totals.qris);
    document.getElementById('transferTotalText').innerText = rupiah(payload.totals.transfer);
    document.getElementById('revenueTotalText').innerText = rupiah(payload.totals.revenue);

    document.getElementById('summaryRevenue').innerText = rupiah(payload.summary.revenue);
    document.getElementById('summaryTransactions').innerText = payload.summary.transactions;
    document.getElementById('summaryProfit').innerText = rupiah(payload.summary.profit);
    document.getElementById('summaryRefunds').innerText = rupiah(payload.summary.refunds);
  }

  async function loadDaily(dateStr) {
    setLoading(true);
    try {
      const res = await fetch(`/sales/daily?date=${dateStr}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      const json = await res.json();
      refreshUI(json);
    } catch (e) {
      const tbody = document.getElementById('salesTableBody');
      tbody.innerHTML = `<tr><td colspan="7" class="px-4 py-10 text-center text-red-500">Gagal memuat data.</td></tr>`;
      console.error(e);
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
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
  });
</script>
@endpush
@endsection
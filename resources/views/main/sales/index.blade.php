@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Penjualan - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Penjualan</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Manajemen Penjualan
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Pantau dan kelola transaksi penjualan harian outlet Anda.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="text-right">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Tanggal Dipilih</p>
                    <p id="headerDateText" class="text-sm font-black text-gray-900 mt-0.5">
                        {{ \Carbon\Carbon::parse($selectedDate)->isoFormat('D MMMM Y') }}
                    </p>
                </div>
                <a href="{{ route('withdraw.confirm-password') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-5 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                    <i class="fas fa-wallet text-xs"></i>
                    <span>Tarik Saldo</span>
                </a>
            </div>
        </section>

        {{-- RINGKASAN STATISTIK HARIAN --}}
        <section class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Penjualan</p>
                <p id="summaryRevenue" class="mt-2 text-2xl font-black text-cuan-green">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </p>
                <p id="summaryDate1" class="mt-1 text-[10px] font-bold text-gray-400">
                    {{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}
                </p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Transaksi</p>
                <p id="summaryTransactions" class="mt-2 text-2xl font-black text-blue-600">
                    {{ $totalTransactions }}
                </p>
                <p id="summaryDate2" class="mt-1 text-[10px] font-bold text-gray-400">
                    {{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}
                </p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Laba Kotor</p>
                <p id="summaryProfit" class="mt-2 text-2xl font-black text-purple-600">
                    Rp {{ number_format($dailyProfit, 0, ',', '.') }}
                </p>
                <p id="summaryDate3" class="mt-1 text-[10px] font-bold text-gray-400">
                    {{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}
                </p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Refund</p>
                <p id="summaryRefunds" class="mt-2 text-2xl font-black text-red-500">
                    Rp {{ number_format($totalRefunds, 0, ',', '.') }}
                </p>
                <p id="summaryDate4" class="mt-1 text-[10px] font-bold text-gray-400">
                    {{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}
                </p>
            </div>

            {{-- summary discount (tersembunyi, hanya untuk JS) --}}
            <p id="summaryDate5" class="hidden">{{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}</p>
            <p id="summaryDiscount" class="hidden">Rp {{ number_format($dailyTotalDiscount ?? 0, 0, ',', '.') }}</p>
        </section>

        {{-- KONTEN UTAMA: KALENDER + TABEL --}}
        <x-card-container>

            {{-- Toolbar --}}
            <div class="px-6 py-5 border-b border-gray-100 bg-white">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Transaksi Penjualan</h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">
                            Pilih tanggal pada kalender untuk melihat transaksi.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="relative">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                            <input type="text"
                                   id="searchInvoice"
                                   placeholder="Cari invoice..."
                                   class="pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all font-bold w-48">
                        </div>
                        <button id="btnToday"
                                class="inline-flex items-center gap-2 rounded-xl bg-cuan-green/10 text-cuan-green border border-cuan-green/20 px-4 py-2.5 text-sm font-black hover:bg-cuan-green hover:text-white transition-all active:scale-95">
                            <i class="fas fa-calendar-day text-xs"></i>
                            <span>Hari Ini</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Grid Kalender + Tabel --}}
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                    {{-- Kalender --}}
                    <div class="lg:col-span-5">
                        <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white">
                            <div id="calendar" class="fc-theme-standard"></div>
                            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Tanggal Dipilih</p>
                                <p id="selectedDateText" class="text-base font-black text-gray-900">
                                    {{ \Carbon\Carbon::parse($selectedDate)->isoFormat('D MMMM Y') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Tabel transaksi & ringkasan metode --}}
                    <div class="lg:col-span-7 flex flex-col gap-4">

                        {{-- Tabel --}}
                        <div class="border border-gray-200 rounded-2xl overflow-hidden">
                            <div class="overflow-x-auto" style="max-height: 440px; overflow-y: auto;">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100 sticky top-0">
                                        <tr>
                                            <th class="px-4 py-3 text-left">Invoice</th>
                                            <th class="px-4 py-3 text-left">Waktu</th>
                                            <th class="px-4 py-3 text-left">Kasir</th>
                                            <th class="px-4 py-3 text-left">Metode</th>
                                            <th class="px-4 py-3 text-right">Diskon</th>
                                            <th class="px-4 py-3 text-right">Total</th>
                                            <th class="px-4 py-3 text-center">Status</th>
                                            @if(auth()->user()->can('lihat detail penjualan') || auth()->user()->can('refund penjualan'))
                                            <th class="px-4 py-3 text-center">Aksi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody id="salesTableBody" class="bg-white divide-y divide-gray-100">
                                        @forelse($sales as $sale)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-4 py-4 font-bold text-gray-900 whitespace-nowrap">
                                                    {{ $sale->invoice_number }}
                                                </td>
                                                <td class="px-4 py-4 text-gray-500 whitespace-nowrap">
                                                    {{ $sale->created_at->format('H:i') }}
                                                </td>
                                                <td class="px-4 py-4 text-gray-700 whitespace-nowrap">
                                                    <div class="font-bold text-gray-900">{{ $sale->customer_name ?? ($sale->customer?->name ?? 'Umum') }}</div>
                                                    <div class="text-[10px] text-gray-400">Kasir: {{ $sale->cashier->name ?? '-' }}</div>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    @if($sale->payment_method == 'cash')
                                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-blue-50 text-blue-600 border border-blue-100">
                                                            Cash
                                                        </span>
                                                    @elseif($sale->payment_method == 'qris')
                                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-cuan-green/10 text-cuan-green border border-cuan-green/20">
                                                            QRIS
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-purple-50 text-purple-600 border border-purple-100">
                                                            Transfer
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-4 text-right font-bold text-orange-500 whitespace-nowrap">
                                                    Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}
                                                </td>
                                                <td class="px-4 py-4 text-right font-black text-gray-900 whitespace-nowrap">
                                                    Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                                                </td>
                                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                                    @if($sale->status == 'completed')
                                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-cuan-green/10 text-cuan-green border border-cuan-green/10">
                                                            Selesai
                                                        </span>
                                                    @elseif($sale->status == 'refunded')
                                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-red-50 text-red-500 border border-red-100">
                                                            Refund
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-yellow-50 text-yellow-600 border border-yellow-100">
                                                            {{ ucfirst($sale->status) }}
                                                        </span>
                                                    @endif
                                                </td>
                                                @if(auth()->user()->can('lihat detail penjualan') || auth()->user()->can('refund penjualan'))
                                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                                    <div class="inline-flex items-center gap-2">
                                                        @can('lihat detail penjualan')
                                                        <a href="{{ route('sales.show', $sale->id) }}"
                                                           class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 hover:bg-gray-100 transition-all active:scale-95"
                                                           title="Detail">
                                                            <i class="fas fa-eye text-xs"></i>
                                                        </a>
                                                        @endcan
                                                        @if($sale->status == 'completed' && in_array($sale->payment_method, ['cash', 'transfer']))
                                                            @can('refund penjualan')
                                                            <button
                                                                onclick="confirmRefund('{{ $sale->id }}', '{{ $sale->invoice_number }}', {{ $sale->grand_total }})"
                                                                class="w-8 h-8 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all active:scale-95"
                                                                title="Refund">
                                                                <i class="fas fa-undo text-xs"></i>
                                                            </button>
                                                            @endcan
                                                        @endif
                                                    </div>
                                                </td>
                                                @endif
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="px-4 py-16 text-center">
                                                    <div class="flex flex-col items-center">
                                                        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                                            <i class="fas fa-receipt text-2xl text-gray-300"></i>
                                                        </div>
                                                        <p class="text-sm font-bold text-gray-900">Tidak ada transaksi</p>
                                                        <p class="text-xs text-gray-400 mt-1">Pilih tanggal lain untuk melihat transaksi.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Ringkasan metode pembayaran --}}
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                            <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                                <p class="text-[10px] font-black uppercase tracking-widest text-blue-400 mb-1">Cash</p>
                                <p id="cashTotalText" class="text-sm font-black text-blue-700">
                                    Rp {{ number_format($cashTotal, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="bg-cuan-green/5 border border-cuan-green/20 rounded-xl px-4 py-3">
                                <p class="text-[10px] font-black uppercase tracking-widest text-cuan-green/70 mb-1">QRIS</p>
                                <p id="qrisTotalText" class="text-sm font-black text-cuan-green">
                                    Rp {{ number_format($qrisTotal, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="bg-purple-50 border border-purple-100 rounded-xl px-4 py-3">
                                <p class="text-[10px] font-black uppercase tracking-widest text-purple-400 mb-1">Transfer</p>
                                <p id="transferTotalText" class="text-sm font-black text-purple-700">
                                    Rp {{ number_format($transferTotal, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="bg-gray-900 border border-gray-700 rounded-xl px-4 py-3">
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Total</p>
                                <p id="revenueTotalText" class="text-sm font-black text-white">
                                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </x-card-container>

    </div>
</main>

{{-- SweetAlert2 Refund Confirmation (menggantikan modal HTML lama) --}}
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<style>
  /* Calendar Variables */
  #calendar {
    --fc-border-color: #e5e7eb;
    --fc-neutral-bg-color: #fafafa;
    --fc-today-bg-color: rgba(101, 140, 88, 0.12);
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
  }

  .fc .fc-toolbar-chunk {
    display: flex;
    align-items: center;
  }

  .fc .fc-toolbar-chunk:nth-child(1) { order: 1; }
  .fc .fc-toolbar-chunk:nth-child(2) { order: 2; flex: 1; justify-content: center; }
  .fc .fc-toolbar-chunk:nth-child(3) { order: 3; }

  .fc .fc-toolbar-title {
    font-size: 0.8125rem;
    font-weight: 900;
    color: #111827;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    white-space: nowrap;
  }

  .fc .fc-today-button { display: none !important; }

  /* Nav Buttons */
  .fc .fc-button {
    border-radius: 0.625rem;
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
    border-color: #658C58;
    color: #658C58;
  }

  .fc .fc-button-primary:not(:disabled).fc-button-active,
  .fc .fc-button-primary:focus {
    box-shadow: none;
    outline: none;
  }

  /* Calendar Grid */
  .fc .fc-scrollgrid { border: none; }

  /* Day Numbers */
  .fc .fc-daygrid-day-number {
    font-weight: 700;
    padding: 0.375rem;
    font-size: 0.8125rem;
    text-align: center;
    width: 100%;
    color: #374151;
  }

  /* Today */
  .fc .fc-day-today { background: rgba(101,140,88,0.10) !important; }
  .fc .fc-day-today .fc-daygrid-day-number { color: #658C58 !important; font-weight: 900; }

  /* Hover */
  .fc .fc-daygrid-day:hover { background: rgba(101,140,88,0.05); cursor: pointer; }

  /* Day cell height */
  .fc .fc-daygrid-day-frame { min-height: 45px; }

  /* Selected day */
  .fc .fc-daygrid-day.fc-selected { background: rgba(101,140,88,0.15) !important; }
  .fc .fc-daygrid-day.fc-selected .fc-daygrid-day-number { color: #3d6233; font-weight: 900; }

  /* Day headers */
  .fc .fc-col-header-cell {
    padding: 0.5rem 0.25rem;
    font-weight: 900;
    font-size: 0.625rem;
    text-transform: uppercase;
    color: #9ca3af;
    background: #f9fafb;
    border-color: #e5e7eb;
    letter-spacing: 0.1em;
  }

  .fc .fc-daygrid-day-top { display: flex; justify-content: center; align-items: center; }
  .fc .fc-daygrid-day-events { display: none; }

  /* Responsive */
  @media (max-width: 1024px) {
    .fc .fc-toolbar { padding: 0.625rem 0.75rem; }
    .fc .fc-toolbar-title { font-size: 0.75rem; }
    .fc .fc-daygrid-day-frame { min-height: 40px; }
    .fc .fc-daygrid-day-number { font-size: 0.75rem; padding: 0.25rem; }
  }
  @media (max-width: 640px) {
    .fc .fc-toolbar { padding: 0.5rem 0.625rem; gap: 0.5rem; }
    .fc .fc-toolbar-title { font-size: 0.6875rem; }
    .fc .fc-button { padding: 0.25rem 0.5rem; min-width: 28px; height: 28px; }
    .fc .fc-daygrid-day-frame { min-height: 36px; }
    .fc .fc-daygrid-day-number { font-size: 0.6875rem; padding: 0.25rem; }
    .fc .fc-col-header-cell { font-size: 0.5625rem; padding: 0.375rem 0.125rem; }
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
    if (method === 'cash')
      return `<span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-blue-50 text-blue-600 border border-blue-100">Cash</span>`;
    if (method === 'qris')
      return `<span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-green-50 text-green-600 border border-green-100" style="color:#658C58;background:rgba(101,140,88,.08);border-color:rgba(101,140,88,.2)">QRIS</span>`;
    return `<span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-purple-50 text-purple-600 border border-purple-100">Transfer</span>`;
  }

  function statusBadge(status) {
    if (status === 'completed')
      return `<span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest" style="background:rgba(101,140,88,.1);color:#658C58;border:1px solid rgba(101,140,88,.1)">Selesai</span>`;
    if (status === 'refunded')
      return `<span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-red-50 text-red-500 border border-red-100">Refund</span>`;
    return `<span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-yellow-50 text-yellow-600 border border-yellow-100">${status}</span>`;
  }

  // Refund menggunakan SweetAlert2 (tidak ada modal HTML)
  let pendingRefundId = null;
  let pendingRefundToken = null;

  function confirmRefund(saleId, invoiceNumber, amount) {
    Swal.fire({
        title: 'Konfirmasi Refund',
        html: `
            <div class="text-left space-y-3">
                <p class="text-sm text-gray-600 text-center">Apakah Anda yakin ingin melakukan refund untuk:</p>
                <div class="bg-gray-50 rounded-2xl p-4 mt-3 border border-gray-200">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Invoice</p>
                    <p class="font-black text-gray-900">${invoiceNumber}</p>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-3 mb-1">Total</p>
                    <p class="text-xl font-black text-red-600">${rupiah(amount)}</p>
                </div>
                <p class="text-xs text-center text-red-500 font-bold mt-2">
                    Stok produk akan dikembalikan dan uang akan dikembalikan ke kasir.
                </p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Refund',
        cancelButtonText: 'Batal',
        showLoaderOnConfirm: true,
        customClass: {
            popup: 'rounded-[2rem] border-none shadow-2xl',
            title: 'font-black text-gray-900',
            htmlContainer: 'text-sm font-medium text-gray-500',
            confirmButton: 'rounded-xl px-6 py-3 font-bold text-sm',
            cancelButton: 'rounded-xl px-6 py-3 font-bold text-sm'
        },
        preConfirm: () => {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                       || '{{ csrf_token() }}';
            return fetch(`/sales/${saleId}/refund`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            }).then(res => {
                if (!res.ok) throw new Error('Gagal melakukan refund.');
                return res.json();
            }).catch(err => {
                Swal.showValidationMessage(err.message || 'Terjadi kesalahan.');
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed && result.value?.success) {
            Swal.fire({
                icon: 'success',
                title: 'Refund Berhasil',
                text: result.value.message || 'Refund telah diproses.',
                showConfirmButton: false,
                timer: 2500,
                iconColor: '#658C58',
                customClass: {
                    popup: 'rounded-3xl border-none shadow-2xl',
                    title: 'font-black text-gray-900',
                }
            }).then(() => {
                loadDaily(currentSelectedDate);
            });
        } else if (result.isConfirmed && result.value && !result.value.success) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: result.value.message || 'Gagal melakukan refund.',
                confirmButtonColor: '#ef4444',
                customClass: { popup: 'rounded-3xl border-none shadow-2xl', title: 'font-black text-gray-900' }
            });
        }
    });
  }

  function setLoading(isLoading) {
    const tbody = document.getElementById('salesTableBody');
    if (isLoading) {
      tbody.innerHTML = `
        <tr>
          <td colspan="8" class="px-4 py-16 text-center">
            <div class="flex flex-col items-center gap-3">
              <i class="fas fa-circle-notch fa-spin text-2xl text-cuan-green"></i>
              <p class="text-sm font-bold text-gray-400">Memuat data...</p>
            </div>
          </td>
        </tr>`;
    }
  }

  let calendar;

  function refreshUI(payload) {
    if (payload.selectedDate !== currentSelectedDate) {
        currentSelectedDate = payload.selectedDate;
        if (calendar) {
            calendar.gotoDate(currentSelectedDate);
            calendar.select(currentSelectedDate);
        }
    }

    const dateLabel = new Date(payload.selectedDate + 'T00:00:00');
    const dateText = dateLabel.toLocaleDateString('id-ID', { day:'numeric', month:'long', year:'numeric' });
    const dateShort = dateLabel.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' });

    document.getElementById('selectedDateText').innerText = dateText;
    document.getElementById('headerDateText').innerText = dateText;
    ['summaryDate1','summaryDate2','summaryDate3','summaryDate4','summaryDate5'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.innerText = dateShort;
    });

    const tbody = document.getElementById('salesTableBody');
    if (!payload.sales.length) {
      tbody.innerHTML = `
        <tr>
          <td colspan="8" class="px-4 py-16 text-center">
            <div class="flex flex-col items-center">
              <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                <i class="fas fa-receipt text-2xl text-gray-300"></i>
              </div>
              <p class="text-sm font-bold text-gray-900">Tidak ada transaksi</p>
              <p class="text-xs text-gray-400 mt-1">Pilih tanggal lain untuk melihat transaksi.</p>
            </div>
          </td>
        </tr>`;
    } else {
      const showAksi = window.permissions.lihatDetail || window.permissions.refundPenjualan;
      tbody.innerHTML = payload.sales.map(s => {
        let actionColumn = '';
        if (showAksi) {
          actionColumn = `
            <td class="px-4 py-4 text-center whitespace-nowrap">
              <div class="inline-flex items-center gap-2">
                ${window.permissions.lihatDetail ? `
                <a href="/sales/${s.id}"
                   class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 hover:bg-gray-100 transition-all"
                   title="Detail">
                  <i class="fas fa-eye text-xs"></i>
                </a>` : ''}
                ${(window.permissions.refundPenjualan && s.status === 'completed' && ['cash','transfer'].includes(s.payment_method)) ? `
                <button onclick="confirmRefund('${s.id}', '${s.invoice_number}', ${s.grand_total})"
                        class="w-8 h-8 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all"
                        title="Refund">
                  <i class="fas fa-undo text-xs"></i>
                </button>` : ''}
              </div>
            </td>`;
        }

        const isHighlighted = payload.highlightId == s.id;
        const rowClass = isHighlighted ? 'bg-yellow-50 border-l-4 border-yellow-400' : 'hover:bg-gray-50';

        return `
        <tr id="row-${s.id}" class="${rowClass} transition-colors duration-500">
          <td class="px-4 py-4 font-bold text-gray-900 whitespace-nowrap">${s.invoice_number}</td>
          <td class="px-4 py-4 text-gray-500 whitespace-nowrap">${s.time}</td>
          <td class="px-4 py-4 text-gray-700 whitespace-nowrap">${s.cashier ?? '-'}</td>
          <td class="px-4 py-4 whitespace-nowrap">${methodBadge(s.payment_method)}</td>
          <td class="px-4 py-4 text-right font-bold text-orange-500 whitespace-nowrap">${rupiah(s.total_discount)}</td>
          <td class="px-4 py-4 text-right font-black text-gray-900 whitespace-nowrap">${rupiah(s.grand_total)}</td>
          <td class="px-4 py-4 text-center whitespace-nowrap">${statusBadge(s.status)}</td>
          ${actionColumn}
        </tr>`;
      }).join('');

      if (payload.highlightId) {
          const row = document.getElementById(`row-${payload.highlightId}`);
          if (row) {
              setTimeout(() => {
                  row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                  setTimeout(() => {
                      row.classList.remove('bg-yellow-50', 'border-l-4', 'border-yellow-400');
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
    const discEl = document.getElementById('summaryDiscount');
    if (discEl) discEl.innerText = rupiah(payload.summary.discount);
  }

  let currentSelectedDate = '{{ $selectedDate }}';
  let searchTimeout = null;

  async function loadDaily(dateStr) {
    currentSelectedDate = dateStr;
    setLoading(true);
    const search = document.getElementById('searchInvoice').value;
    try {
      const res = await fetch(`/sales/daily?date=${dateStr}&search=${search}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      const json = await res.json();
      refreshUI(json);
    } catch (e) {
      document.getElementById('salesTableBody').innerHTML =
        `<tr><td colspan="8" class="px-4 py-12 text-center text-sm text-red-500 font-bold">Gagal memuat data. Coba lagi.</td></tr>`;
      console.error(e);
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
      initialDate: '{{ $selectedDate }}',
      initialView: 'dayGridMonth',
      headerToolbar: { left: 'prev', center: 'title', right: 'next' },
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

    window.addEventListener('load', () => {
        setTimeout(() => calendar.updateSize(), 500);
    });

    document.getElementById('btnToday').addEventListener('click', () => {
      const today = new Date().toISOString().slice(0,10);
      calendar.today();
      loadDaily(today);
    });

    document.getElementById('searchInvoice').addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => loadDaily(currentSelectedDate), 500);
    });

    // Session Flash SweetAlert
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 3000,
            iconColor: '#658C58',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black text-gray-900',
                htmlContainer: 'text-sm font-medium text-gray-500'
            }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: "{{ session('error') }}",
            confirmButtonColor: '#ef4444',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black text-gray-900',
                htmlContainer: 'text-sm font-medium text-gray-500'
            }
        });
    @endif
  });
</script>
@endpush
@endsection

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keseluruhan</title>
    <style>
        @page { 
            /* Margin: Top Right Bottom Left */
            /* User request: Kiri 4, Kanan 4, Atas 3, Bawah 3 */
            margin: 3cm 4cm 3cm 4cm; 
            @bottom-right {
                content: "Hal. " counter(page);
                font-size: 8px;
                color: #9ca3af;
            }
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'DejaVu Sans', sans-serif; 
            font-size: 9px; 
            color: #1f2937; 
            line-height: 1.2;
        }
        
        /* COVER PAGE */
        .cover {
            height: 100%;
            min-height: 80vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            border: 2px solid #1e40af;
            border-radius: 15px;
            padding: 40px;
            margin-bottom: 0;
            page-break-after: always;
        }
        
        .cover-logo {
            width: 100px;
            height: 100px;
            margin-bottom: 30px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .cover-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .cover h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #1e40af;
        }
        
        .cover .outlet-name {
            font-size: 16px;
            margin-bottom: 35px;
            font-weight: 600;
            color: #4b5563;
        }
        
        .cover .period-box {
            background: #eff6ff;
            padding: 15px 35px;
            border-radius: 10px;
            border: 1px solid #bfdbfe;
            margin-bottom: 30px;
            display: inline-block;
        }
        
        .cover .period-box strong {
            display: block;
            font-size: 10px;
            margin-bottom: 6px;
            letter-spacing: 1px;
            color: #1e40af;
        }
        
        .cover .period-box .dates {
            font-size: 14px;
            font-weight: 700;
            color: #1f2937;
        }
        
        .cover .footer-info {
            margin-top: 50px;
            font-size: 9px;
            color: #9ca3af;
        }
        
        /* CONTENT PAGES */
        .section-header {
            background: #1e40af;
            color: white;
            padding: 6px 10px;
            margin: 0 0 10px 0;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .subsection-title {
            font-size: 9px;
            font-weight: 700;
            color: #1e40af;
            margin: 10px 0 5px 0;
            padding-bottom: 3px;
            border-bottom: 1px solid #bfdbfe;
        }
        
        /* SUMMARY GRID */
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }
        
        .summary-row {
            display: table-row;
        }
        
        .summary-cell {
            display: table-cell;
            padding: 4px 8px;
            border: 1px solid #e5e7eb;
            font-size: 8px;
        }
        
        .summary-cell:first-child {
            background: #f9fafb;
            font-weight: 600;
            width: 60%;
        }
        
        .summary-cell:last-child {
            background: white;
            text-align: right;
            font-weight: 700;
            width: 40%;
        }
        
        .summary-highlight {
            background: #1e40af !important;
            color: white !important;
        }
        
        .summary-success {
            background: #059669 !important;
            color: white !important;
        }
        
        .summary-danger {
            background: #dc2626 !important;
            color: white !important;
        }
        
        /* TABLES */
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 7px;
        }
        
        table.data thead {
            background: #1e40af;
            color: white;
        }
        
        table.data th {
            padding: 4px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            text-align: left;
        }
        
        table.data td {
            padding: 4px;
            border: 1px solid #e5e7eb;
        }
        
        table.data tbody tr:nth-child(even) {
            background: #f9fafb;
        }
        
        table.data tfoot {
            background: #1f2937;
            color: white;
            font-weight: 700;
        }
        
        table.data tfoot td {
            padding: 4px;
            border: none;
        }
        
        /* CHARTS */
        .chart-container {
            margin: 4px 0;
            padding: 6px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            background: #fafafa;
        }
        
        .chart-title {
            font-size: 8px;
            font-weight: 700;
            margin-bottom: 6px;
            text-align: center;
            color: #1e40af;
            text-transform: uppercase;
        }
        
        .bar-item {
            display: table;
            width: 100%;
            margin-bottom: 2px;
        }
        
        .bar-label {
            display: table-cell;
            width: 35%;
            font-size: 6px;
            font-weight: 600;
            vertical-align: middle;
            padding-right: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .bar-track {
            display: table-cell;
            width: 45%;
            vertical-align: middle;
            background: #e5e7eb;
            border-radius: 2px;
            height: 6px;
        }
        
        .bar-fill {
            height: 100%;
            background: #3b82f6;
            border-radius: 2px;
        }
        
        .bar-value {
            display: table-cell;
            width: 20%;
            text-align: right;
            font-size: 6px;
            font-weight: 700;
            vertical-align: middle;
            padding-left: 4px;
        }
        
        /* BADGES */
        .badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 6px;
            font-weight: 700;
            text-transform: uppercase;
        }
        
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        
        /* INFO BOX */
        .info-box {
            padding: 6px 8px;
            background: #eff6ff;
            border-left: 3px solid #3b82f6;
            border-radius: 3px;
            margin: 6px 0;
            font-size: 7px;
            color: #1e40af;
        }
        
        .info-box strong {
            font-weight: 700;
        }
        
        /* STOCK STATUS */
        .stock-warning { background: #fef3c7 !important; }
        .stock-critical { background: #fee2e2 !important; }
        
        /* UTILITIES */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-green { color: #059669; }
        .text-red { color: #dc2626; }
        .text-blue { color: #2563eb; }
        .font-bold { font-weight: 700; }
        
        .page-break { page-break-before: always; }
        
        /* TWO COLUMN LAYOUT */
        .two-col {
            display: table;
            width: 100%;
            margin-bottom: 8px;
            border-spacing: 8px 0;
            margin-left: -4px;
            width: calc(100% + 8px);
        }
        
        .col-50 {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
    </style>
</head>
<body>

{{-- COVER PAGE --}}
<div class="cover">
    <div class="cover-logo">
        @if(isset(auth()->user()->outlet->logo) && auth()->user()->outlet->logo && file_exists(public_path('storage/' . auth()->user()->outlet->logo)))
            <img src="{{ public_path('storage/' . auth()->user()->outlet->logo) }}" alt="Logo">
        @else
            <img src="{{ public_path('assets/image/logo.svg') }}" alt="Logo">
        @endif
    </div>
    
    <h1>Laporan Keseluruhan</h1>
    <div class="outlet-name">{{ auth()->user()->outlet->name ?? 'CuanFlow POS' }}</div>
    
    <div class="period-box">
        <strong>PERIODE LAPORAN</strong>
        <div class="dates">{{ $start->format('d F Y') }} - {{ $end->format('d F Y') }}</div>
    </div>
    
    <div class="footer-info">
        Dicetak pada {{ now()->format('d F Y, H:i') }} WIB
    </div>
</div>

{{-- EXECUTIVE SUMMARY --}}
@if(in_array('summary', $sections))
<div class="page-break">
    <div class="section-header">📊 RINGKASAN EKSEKUTIF</div>
    
    <div class="two-col">
        <div class="col-50">
            <div class="summary-grid">
                <div class="summary-row">
                    <div class="summary-cell">Total Pendapatan Kotor</div>
                    <div class="summary-cell text-green">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-cell">Jumlah Transaksi</div>
                    <div class="summary-cell">{{ number_format($totalTransactions) }} tx</div>
                </div>
                <div class="summary-row">
                    <div class="summary-cell">Rata-rata per Transaksi</div>
                    <div class="summary-cell">Rp {{ $totalTransactions > 0 ? number_format($totalRevenue / $totalTransactions, 0, ',', '.') : 0 }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-cell">Total HPP</div>
                    <div class="summary-cell">Rp {{ number_format($totalCogs, 0, ',', '.') }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-cell">Laba Kotor</div>
                    <div class="summary-cell text-blue">Rp {{ number_format($grossProfit, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        
        <div class="col-50">
            <div class="summary-grid">
                <div class="summary-row">
                    <div class="summary-cell">Total Pengeluaran</div>
                    <div class="summary-cell text-red">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-cell">Total Pajak (PPN)</div>
                    <div class="summary-cell">Rp {{ number_format($totalTax ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-cell">Total Diskon</div>
                    <div class="summary-cell text-red">Rp {{ number_format($totalDiscount ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-cell summary-highlight">LABA BERSIH</div>
                    <div class="summary-cell {{ $netProfit >= 0 ? 'summary-success' : 'summary-danger' }}">
                        Rp {{ number_format($netProfit, 0, ',', '.') }}
                    </div>
                </div>
                <div class="summary-row">
                    <div class="summary-cell">Margin Laba (%)</div>
                    <div class="summary-cell">{{ $totalRevenue > 0 ? number_format(($grossProfit / $totalRevenue) * 100, 1) : 0 }}%</div>
                </div>
            </div>
        </div>
    </div>
    
    @if($netProfit >= 0)
    <div class="info-box">
        <strong>✓ Bisnis Menguntungkan:</strong> Laba bersih positif menunjukkan bisnis menghasilkan keuntungan.
    </div>
    @else
    <div class="info-box" style="background: #fef3c7; border-left-color: #f59e0b; color: #92400e;">
        <strong>⚠ Perhatian:</strong> Laba bersih negatif, evaluasi pengeluaran diperlukan.
    </div>
    @endif
    
    {{-- Top Products Mini --}}
    @if(isset($topProducts) && count($topProducts) > 0)
    <div class="subsection-title">🏆 5 PRODUK TERLARIS</div>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 8%;">NO</th>
                <th style="width: 47%;">PRODUK</th>
                <th class="text-right" style="width: 20%;">TERJUAL</th>
                <th class="text-right" style="width: 25%;">PENDAPATAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topProducts->take(5) as $index => $product)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="font-bold">{{ $product->product_name }}</td>
                <td class="text-right">{{ number_format($product->total_qty) }} unit</td>
                <td class="text-right text-green font-bold">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    
    {{-- Payment Methods Mini --}}
    @if(isset($paymentMethods) && count($paymentMethods) > 0)
    <div class="subsection-title">💳 METODE PEMBAYARAN</div>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 30%;">METODE</th>
                <th class="text-center" style="width: 20%;">TRANSAKSI</th>
                <th class="text-right" style="width: 35%;">TOTAL</th>
                <th class="text-right" style="width: 15%;">%</th>
            </tr>
        </thead>
        <tbody>
            @foreach($paymentMethods as $method)
            <tr>
                <td><span class="badge badge-info">{{ strtoupper($method->payment_method) }}</span></td>
                <td class="text-center font-bold">{{ number_format($method->total) }}</td>
                <td class="text-right font-bold">Rp {{ number_format($method->total_amount, 0, ',', '.') }}</td>
                <td class="text-right">{{ $totalRevenue > 0 ? number_format(($method->total_amount / $totalRevenue) * 100, 1) : 0 }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endif

{{-- FINANCIAL DETAILS --}}
@if(in_array('finance', $sections ?? []))
<div class="page-break">
    <div class="section-header">💰 DETAIL KEUANGAN</div>
    
    {{-- Sales by Category --}}
    @if(isset($salesByCategory) && count($salesByCategory) > 0)
    <div class="subsection-title">📊 PENJUALAN PER KATEGORI</div>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 40%;">KATEGORI</th>
                <th class="text-right" style="width: 20%;">QTY</th>
                <th class="text-right" style="width: 25%;">PENDAPATAN</th>
                <th class="text-right" style="width: 15%;">%</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesByCategory as $cat)
            <tr>
                <td class="font-bold">{{ $cat['category_name'] }}</td>
                <td class="text-right">{{ number_format($cat['total_qty']) }} unit</td>
                <td class="text-right text-green font-bold">Rp {{ number_format($cat['total_revenue'], 0, ',', '.') }}</td>
                <td class="text-right">{{ $totalRevenue > 0 ? number_format(($cat['total_revenue'] / $totalRevenue) * 100, 1) : 0 }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    
    {{-- Two Column Layout for Stats --}}
    <div class="two-col">
        <div class="col-50">
            {{-- Refund Stats --}}
            @if(isset($refundStats))
            <div class="subsection-title">🔄 REFUND & PEMBATALAN</div>
            <div class="summary-grid">
                <div class="summary-row">
                    <div class="summary-cell">Transaksi Refund</div>
                    <div class="summary-cell text-red">{{ number_format($refundStats['refund_count'] ?? 0) }} tx</div>
                </div>
                <div class="summary-row">
                    <div class="summary-cell">Nilai Refund</div>
                    <div class="summary-cell text-red">Rp {{ number_format($refundStats['refund_amount'] ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-cell">Transaksi Batal</div>
                    <div class="summary-cell">{{ number_format($refundStats['cancel_count'] ?? 0) }} tx</div>
                </div>
            </div>
            @endif
        </div>
        
        <div class="col-50">
            {{-- Purchase Summary --}}
            @if(isset($totalPurchases))
            <div class="subsection-title">🛒 PEMBELIAN SUPPLIER</div>
            <div class="summary-grid">
                <div class="summary-row">
                    <div class="summary-cell">Total Pembelian</div>
                    <div class="summary-cell text-blue">Rp {{ number_format($totalPurchases ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-cell">Sudah Dibayar</div>
                    <div class="summary-cell text-green">Rp {{ number_format($totalPurchasesPaid ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-cell">Belum Lunas</div>
                    <div class="summary-cell text-red">Rp {{ number_format($totalPurchasesUnpaid ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

{{-- CUSTOMER REPORT --}}
@if(in_array('customer', $sections ?? []))
<div class="page-break">
    <div class="section-header">👥 LAPORAN PELANGGAN</div>
    
    <div class="summary-grid" style="width: 70%; margin-bottom: 12px;">
        <div class="summary-row">
            <div class="summary-cell">Total Piutang</div>
            <div class="summary-cell text-red">Rp {{ number_format($totalPiutang ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-cell">Pelanggan Berhutang</div>
            <div class="summary-cell">{{ isset($customerDebts) ? count($customerDebts) : 0 }} pelanggan</div>
        </div>
    </div>
    
    {{-- Top Customers --}}
    @if(isset($topCustomers) && count($topCustomers) > 0)
    <div class="subsection-title">🏆 PELANGGAN TERLOYAL</div>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 8%;">NO</th>
                <th style="width: 42%;">NAMA</th>
                <th class="text-right" style="width: 20%;">TRANSAKSI</th>
                <th class="text-right" style="width: 30%;">TOTAL BELANJA</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topCustomers as $index => $cust)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="font-bold">{{ $cust->customer->name ?? 'Pelanggan' }}</td>
                <td class="text-right">{{ number_format($cust->total_transactions) }}x</td>
                <td class="text-right text-green font-bold">Rp {{ number_format($cust->total_spent, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    
    {{-- Customer Debts --}}
    @if(isset($customerDebts) && count($customerDebts) > 0)
    <div class="subsection-title">📋 DAFTAR PIUTANG</div>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 45%;">PELANGGAN</th>
                <th style="width: 25%;">NO. INVOICE</th>
                <th class="text-right" style="width: 30%;">JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customerDebts as $debt)
            <tr>
                <td class="font-bold">{{ $debt->customer->name ?? 'Pelanggan' }}</td>
                <td>{{ $debt->invoice_number ?? '-' }}</td>
                <td class="text-right text-red font-bold">Rp {{ number_format($debt->amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-right">TOTAL PIUTANG</td>
                <td class="text-right">Rp {{ number_format($totalPiutang ?? 0, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
    @endif
</div>
@endif

{{-- SALES ANALYSIS WITH CHARTS --}}
@if(in_array('charts', $sections))
<div class="page-break">
    <div class="section-header">📈 ANALISIS PENJUALAN</div>
    
    <div class="two-col">
        <div class="col-50">
            <div class="chart-container">
                <div class="chart-title">🏆 TOP 10 PRODUK (QTY)</div>
                @php $maxQty = $topProducts->max('total_qty') ?? 1; @endphp
                @foreach($topProducts->take(10) as $index => $product)
                <div class="bar-item">
                    <div class="bar-label">{{ $index + 1 }}. {{ Str::limit($product->product_name, 15) }}</div>
                    <div class="bar-track">
                        <div class="bar-fill" style="width: {{ ($product->total_qty / $maxQty) * 100 }}%;"></div>
                    </div>
                    <div class="bar-value">{{ number_format($product->total_qty) }}</div>
                </div>
                @endforeach
            </div>
        </div>
        
        <div class="col-50">
            <div class="chart-container">
                <div class="chart-title">💰 PENDAPATAN TERTINGGI</div>
                @php $maxRev = $topProducts->max('total_revenue') ?? 1; @endphp
                @foreach($topProducts->sortByDesc('total_revenue')->take(10) as $index => $product)
                <div class="bar-item">
                    <div class="bar-label">{{ $index + 1 }}. {{ Str::limit($product->product_name, 15) }}</div>
                    <div class="bar-track">
                        <div class="bar-fill" style="width: {{ ($product->total_revenue / $maxRev) * 100 }}%; background: #10b981;"></div>
                    </div>
                    <div class="bar-value">{{ number_format($product->total_revenue / 1000, 0) }}K</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

{{-- HOURLY ANALYSIS --}}
@if(in_array('hourly', $sections) && $hourlySales->count() > 0)
<div class="page-break">
    <div class="section-header">⏰ ANALISIS WAKTU OPERASIONAL</div>
    
    <div class="chart-container">
        <div class="chart-title">JAM TERSIBUK (PEAK HOURS)</div>
        @php 
            $maxHour = $hourlySales->max('transactions') ?? 1; 
            $hourlySorted = $hourlySales->sortByDesc('transactions')->take(16); // Take 16 for even split
            $halfCount = ceil($hourlySorted->count() / 2);
            $leftColumn = $hourlySorted->slice(0, $halfCount);
            $rightColumn = $hourlySorted->slice($halfCount);
        @endphp
        
        <div class="two-col" style="margin-bottom: 0;">
            <div class="col-50">
                @foreach($leftColumn as $hour)
                <div class="bar-item">
                    <div class="bar-label">{{ sprintf('%02d:00-%02d:59', $hour->hour, $hour->hour) }}</div>
                    <div class="bar-track">
                        <div class="bar-fill" style="width: {{ ($hour->transactions / $maxHour) * 100 }}%; background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);"></div>
                    </div>
                    <div class="bar-value">{{ number_format($hour->transactions) }} tx</div>
                </div>
                @endforeach
            </div>
            <div class="col-50">
                @foreach($rightColumn as $hour)
                <div class="bar-item">
                    <div class="bar-label">{{ sprintf('%02d:00-%02d:59', $hour->hour, $hour->hour) }}</div>
                    <div class="bar-track">
                        <div class="bar-fill" style="width: {{ ($hour->transactions / $maxHour) * 100 }}%; background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);"></div>
                    </div>
                    <div class="bar-value">{{ number_format($hour->transactions) }} tx</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="info-box">
        <strong>💡 Insight:</strong> Optimalkan jadwal staff dan stok produk pada jam-jam tersibuk untuk meningkatkan efisiensi operasional.
    </div>
</div>
@endif

{{-- CASHIER PERFORMANCE --}}
@if(in_array('cashier', $sections))
<div class="page-break">
    <div class="section-header">👨‍💼 PERFORMA KASIR</div>
    
    <table class="data">
        <thead>
            <tr>
                <th style="width: 35%;">NAMA KASIR</th>
                <th class="text-center" style="width: 20%;">TRANSAKSI</th>
                <th class="text-right" style="width: 30%;">TOTAL PENJUALAN</th>
                <th class="text-right" style="width: 15%;">AVG/TX</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($cashierPerformance) && count($cashierPerformance) > 0)
                @foreach($cashierPerformance as $perf)
                <tr>
                    <td class="font-bold">{{ $perf->cashier->name ?? 'Unknown' }}</td>
                    <td class="text-center font-bold">{{ number_format($perf->total_transactions) }}</td>
                    <td class="text-right text-green font-bold">Rp {{ number_format($perf->total_revenue, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($perf->total_transactions > 0 ? $perf->total_revenue / $perf->total_transactions : 0, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4" class="text-center" style="padding: 15px;">Tidak ada data performa kasir</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
@endif

{{-- DETAILED SALES --}}
@if(in_array('sales', $sections))
<div class="page-break">
    <div class="section-header">🧾 RINCIAN TRANSAKSI PENJUALAN</div>
    
    @if($sales->count() > 100)
    <div class="info-box" style="background: #fef3c7; border-left-color: #f59e0b; color: #92400e;">
        <strong>ℹ Info:</strong> Menampilkan 100 transaksi pertama dari {{ $sales->count() }} total transaksi.
    </div>
    @endif
    
    <table class="data">
        <thead>
            <tr>
                <th style="width: 11%;">TANGGAL</th>
                <th style="width: 18%;">INVOICE</th>
                <th style="width: 26%;">PELANGGAN</th>
                <th style="width: 12%;">METODE</th>
                <th class="text-right" style="width: 20%;">TOTAL</th>
                <th class="text-center" style="width: 13%;">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales->take(100) as $sale)
            <tr>
                <td>{{ $sale->created_at->format('d/m/y H:i') }}</td>
                <td class="font-bold">{{ $sale->invoice_number }}</td>
                <td>{{ Str::limit($sale->customer->name ?? 'Umum', 20) }}</td>
                <td><span class="badge badge-info">{{ strtoupper($sale->payment_method) }}</span></td>
                <td class="text-right text-green font-bold">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                <td class="text-center"><span class="badge badge-success">✓</span></td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 20px;">Tidak ada data transaksi</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

{{-- EXPENSES --}}
@if(in_array('expenses', $sections) && $expenses->count() > 0)
<div class="page-break">
    <div class="section-header">💸 PENGELUARAN OPERASIONAL</div>
    
    <table class="data">
        <thead>
            <tr>
                <th style="width: 10%;">TANGGAL</th>
                <th style="width: 18%;">KATEGORI</th>
                <th style="width: 47%;">DESKRIPSI</th>
                <th class="text-right" style="width: 18%;">JUMLAH</th>
                <th class="text-center" style="width: 7%;">✓</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $expense)
            <tr>
                <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d/m/y') }}</td>
                <td><span class="badge badge-warning">{{ strtoupper($expense->category->name ?? 'LAIN') }}</span></td>
                <td>{{ $expense->description }}</td>
                <td class="text-right text-red font-bold">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                <td class="text-center">
                    @if($expense->status === 'approved')
                    <span class="badge badge-success">✓</span>
                    @else
                    <span class="badge badge-warning">⏳</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right">TOTAL PENGELUARAN</td>
                <td class="text-right">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>
@endif

{{-- STOCK REPORT --}}
@if(in_array('stock', $sections) && $productStocks && $ingredientStocks)
<div class="page-break">
    <div class="section-header">📦 LAPORAN PERSEDIAAN</div>
    
    <div class="subsection-title">STOK PRODUK</div>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 45%;">PRODUK</th>
                <th style="width: 20%;">KATEGORI</th>
                <th class="text-right" style="width: 15%;">STOK</th>
                <th class="text-right" style="width: 10%;">MIN</th>
                <th class="text-center" style="width: 10%;">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productStocks as $product)
            @php
                $currentStock = $product->stocks->sum('quantity');
                $isCritical = $currentStock <= $product->min_stock * 0.5;
                $isWarning = $currentStock <= $product->min_stock && !$isCritical;
            @endphp
            <tr class="{{ $isCritical ? 'stock-critical' : ($isWarning ? 'stock-warning' : '') }}">
                <td class="font-bold">{{ $product->name }}</td>
                <td>{{ $product->category->name ?? '-' }}</td>
                <td class="text-right font-bold">{{ number_format($currentStock) }} {{ $product->unit->name ?? '' }}</td>
                <td class="text-right">{{ number_format($product->min_stock) }}</td>
                <td class="text-center">
                    @if($isCritical)
                    <span class="badge badge-danger">⚠</span>
                    @elseif($isWarning)
                    <span class="badge badge-warning">!</span>
                    @else
                    <span class="badge badge-success">✓</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="subsection-title" style="margin-top: 15px;">STOK BAHAN BAKU</div>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 45%;">BAHAN</th>
                <th style="width: 20%;">KATEGORI</th>
                <th class="text-right" style="width: 15%;">STOK</th>
                <th class="text-right" style="width: 10%;">MIN</th>
                <th class="text-center" style="width: 10%;">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ingredientStocks as $ingredient)
            @php
                $currentStock = $ingredient->stocks->sum('quantity');
                $isCritical = $currentStock <= $ingredient->min_stock * 0.5;
                $isWarning = $currentStock <= $ingredient->min_stock && !$isCritical;
            @endphp
            <tr class="{{ $isCritical ? 'stock-critical' : ($isWarning ? 'stock-warning' : '') }}">
                <td class="font-bold">{{ $ingredient->name }}</td>
                <td>{{ $ingredient->category->name ?? '-' }}</td>
                <td class="text-right font-bold">{{ number_format($currentStock) }} {{ $ingredient->unit->name ?? '' }}</td>
                <td class="text-right">{{ number_format($ingredient->min_stock) }}</td>
                <td class="text-center">
                    @if($isCritical)
                    <span class="badge badge-danger">⚠</span>
                    @elseif($isWarning)
                    <span class="badge badge-warning">!</span>
                    @else
                    <span class="badge badge-success">✓</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="info-box" style="background: #fee2e2; border-left-color: #dc2626; color: #991b1b; margin-top: 8px;">
        <strong>⚠ Perhatian:</strong> Item dengan status merah/kuning memerlukan restock segera.
    </div>
</div>
@endif

</body>
</html>
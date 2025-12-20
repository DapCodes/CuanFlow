<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Bisnis</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #000;
            line-height: 1.5;
            orphans: 3;
            widows: 3;
        }
        
        .cover {
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            page-break-after: always;
        }
        
        .cover img {
            width: 100px;
            height: 100px;
            margin-bottom: 30px;
        }
        
        .cover h1 {
            font-size: 28px;
            margin-bottom: 10px;
            color: #000;
        }
        
        .cover .outlet {
            font-size: 16px;
            margin-bottom: 30px;
            color: #333;
        }
        
        .cover .info {
            font-size: 11px;
            color: #666;
            line-height: 1.8;
        }
        
        .page {
            padding: 25px 35px 50px 35px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .header {
            margin-bottom: 18px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
            page-break-after: avoid;
        }
        
        .header h2 {
            font-size: 15px;
            color: #000;
            page-break-after: avoid;
        }
        
        .summary {
            margin-bottom: 25px;
        }
        
        .summary table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .summary td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        
        .summary .label {
            width: 40%;
            font-weight: 600;
            background: #f5f5f5;
        }
        
        .summary .value {
            width: 60%;
            text-align: right;
            font-weight: 700;
        }
        
        .summary .positive {
            color: #059669;
        }
        
        .summary .negative {
            color: #dc2626;
        }
        
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table.data thead {
            background: #f5f5f5;
            display: table-header-group;
        }
        
        table.data th {
            padding: 8px 10px;
            border: 1px solid #ddd;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            text-align: left;
        }
        
        table.data td {
            padding: 8px 10px;
            border: 1px solid #ddd;
            font-size: 9px;
        }
        
        table.data tbody tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        
        table.data tr:nth-child(even) {
            background: #fafafa;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .chart {
            margin: 18px 0;
            padding: 15px;
            border: 1px solid #ddd;
            page-break-inside: avoid;
        }
        
        .chart-title {
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 12px;
            text-align: center;
        }
        
        .bar-item {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        
        .bar-label {
            display: table-cell;
            width: 35%;
            font-size: 9px;
            vertical-align: middle;
            padding-right: 8px;
        }
        
        .bar-track {
            display: table-cell;
            width: 45%;
            vertical-align: middle;
        }
        
        .bar-fill {
            height: 18px;
            background: #000;
        }
        
        .bar-value {
            display: table-cell;
            width: 20%;
            text-align: right;
            font-size: 9px;
            font-weight: 600;
            vertical-align: middle;
            padding-left: 8px;
        }
        
        .note {
            padding: 10px;
            background: #f9f9f9;
            border-left: 3px solid #666;
            font-size: 9px;
            margin: 12px 0;
            page-break-inside: avoid;
        }
        
        .stock-low {
            background: #fee2e2 !important;
        }
        
        .footer {
            position: fixed;
            bottom: 10px;
            left: 30px;
            right: 30px;
            text-align: center;
            font-size: 7px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
        
        @page {
            margin: 0;
        }
    </style>
</head>
<body>
    {{-- COVER --}}
    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
    <div class="cover">
        @if(isset(auth()->user()->outlet->logo) && auth()->user()->outlet->logo)
            <img src="{{ public_path('storage/' . auth()->user()->outlet->logo) }}" alt="Logo">
        @else
            <img src="{{ public_path('assets/image/logo.svg') }}" alt="Logo">
        @endif
        
        <h1>LAPORAN BISNIS</h1>
        <div class="outlet">{{ auth()->user()->outlet->name ?? 'CuanFlow POS' }}</div>
        <div class="info">
            <div><strong>Periode:</strong> {{ $start->format('d F Y') }} - {{ $end->format('d F Y') }}</div>
            <div><strong>Dicetak:</strong> {{ now()->format('d F Y, H:i') }} WIB</div>
        </div>
    </div>

    @if(in_array('summary', $sections) || in_array('charts', $sections))
    {{-- RINGKASAN & ANALISIS --}}
    <div class="page">
        @if(in_array('summary', $sections))
        <div class="header">
            <h2>RINGKASAN KEUANGAN</h2>
        </div>
        
        <div class="summary">
            <table>
                <tr>
                    <td class="label">Total Pendapatan</td>
                    <td class="value positive">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Total Pengeluaran</td>
                    <td class="value negative">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Laba Kotor</td>
                    <td class="value">Rp {{ number_format($grossProfit, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Laba Bersih</td>
                    <td class="value {{ $netProfit >= 0 ? 'positive' : 'negative' }}">Rp {{ number_format($netProfit, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Total Transaksi</td>
                    <td class="value">{{ number_format($totalTransactions) }}</td>
                </tr>
                <tr>
                    <td class="label">Rata-rata per Transaksi</td>
                    <td class="value">Rp {{ $totalTransactions > 0 ? number_format($totalRevenue / $totalTransactions, 0, ',', '.') : 0 }}</td>
                </tr>
            </table>
        </div>
        @endif

        @if(in_array('charts', $sections))
        <div class="header" style="margin-top: 30px;">
            <h2>ANALISIS PENJUALAN</h2>
        </div>
        
        <div class="chart">
            <div class="chart-title">TOP 5 PRODUK TERLARIS</div>
            @php $maxQty = $topProducts->max('total_qty') ?? 1; @endphp
            @foreach($topProducts->take(5) as $product)
            <div class="bar-item">
                <div class="bar-label">{{ Str::limit($product->product_name, 28) }}</div>
                <div class="bar-track">
                    <div class="bar-fill" style="width: {{ ($product->total_qty / $maxQty) * 100 }}%;"></div>
                </div>
                <div class="bar-value">{{ number_format($product->total_qty) }}</div>
            </div>
            @endforeach
        </div>

        <div class="chart">
            <div class="chart-title">PENDAPATAN PER PRODUK</div>
            @php $maxRev = $topProducts->max('total_revenue') ?? 1; @endphp
            @foreach($topProducts->sortByDesc('total_revenue')->take(5) as $product)
            <div class="bar-item">
                <div class="bar-label">{{ Str::limit($product->product_name, 28) }}</div>
                <div class="bar-track">
                    <div class="bar-fill" style="width: {{ ($product->total_revenue / $maxRev) * 100 }}%;"></div>
                </div>
                <div class="bar-value">Rp {{ number_format($product->total_revenue / 1000, 0) }}K</div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    @if(in_array('charts', $sections))
    {{-- ANALISIS LANJUTAN --}}
    <div class="page page-break">
        <div class="header">
            <h2>METODE PEMBAYARAN & JAM TERSIBUK</h2>
        </div>

        <table class="data">
            <thead>
                <tr>
                    <th>METODE PEMBAYARAN</th>
                    <th class="text-right">TRANSAKSI</th>
                    <th class="text-right">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($paymentMethods as $method)
                <tr>
                    <td>{{ strtoupper($method->payment_method) }}</td>
                    <td class="text-right">{{ number_format($method->total) }}</td>
                    <td class="text-right">Rp {{ number_format($method->total_amount, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($hourlySales->count() > 0)
        <div class="chart">
            <div class="chart-title">JAM TERSIBUK</div>
            @php $maxHour = $hourlySales->max('transactions') ?? 1; @endphp
            @foreach($hourlySales->sortByDesc('transactions')->take(8) as $hour)
            <div class="bar-item">
                <div class="bar-label">{{ sprintf('%02d:00', $hour->hour) }}</div>
                <div class="bar-track">
                    <div class="bar-fill" style="width: {{ ($hour->transactions / $maxHour) * 100 }}%; background: #666;"></div>
                </div>
                <div class="bar-value">{{ number_format($hour->transactions) }} tx</div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @endif
    @endif

    @if(in_array('sales', $sections))
    {{-- PENJUALAN --}}
    <div class="page page-break">
        <div class="header">
            <h2>RINCIAN PENJUALAN</h2>
        </div>
        
        @if($sales->count() > 50)
        <div class="note">Menampilkan 50 dari {{ $sales->count() }} transaksi</div>
        @endif

        <table class="data">
            <thead>
                <tr>
                    <th style="width: 12%;">TANGGAL</th>
                    <th style="width: 18%;">INVOICE</th>
                    <th style="width: 25%;">PELANGGAN</th>
                    <th style="width: 15%;">METODE</th>
                    <th class="text-right" style="width: 20%;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales->take(50) as $sale)
                <tr>
                    <td>{{ $sale->created_at->format('d/m/y H:i') }}</td>
                    <td>{{ $sale->invoice_number }}</td>
                    <td>{{ $sale->customer->name ?? 'Umum' }}</td>
                    <td>{{ strtoupper($sale->payment_method) }}</td>
                    <td class="text-right"><strong>Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</strong></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 20px;">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

    @if(in_array('expenses', $sections) && $expenses->count() > 0)
    {{-- PENGELUARAN --}}
    <div class="page page-break">
        <div class="header">
            <h2>RINCIAN PENGELUARAN</h2>
        </div>
        
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 12%;">TANGGAL</th>
                    <th style="width: 18%;">KATEGORI</th>
                    <th style="width: 50%;">DESKRIPSI</th>
                    <th class="text-right" style="width: 20%;">JUMLAH</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenses as $expense)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y') }}</td>
                    <td>{{ strtoupper($expense->category ?? 'LAIN-LAIN') }}</td>
                    <td>{{ $expense->description }}</td>
                    <td class="text-right"><strong>Rp {{ number_format($expense->amount, 0, ',', '.') }}</strong></td>
                </tr>
                @endforeach
                <tr style="background: #f5f5f5;">
                    <td colspan="3" class="text-right"><strong>TOTAL</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($totalExpenses, 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    @if(in_array('stock', $sections) && $productStocks && $ingredientStocks)
    {{-- STOK PRODUK --}}
    <div class="page page-break">
        <div class="header">
            <h2>STOK PRODUK</h2>
        </div>
        
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 45%;">PRODUK</th>
                    <th style="width: 20%;">KATEGORI</th>
                    <th class="text-right" style="width: 15%;">STOK</th>
                    <th class="text-right" style="width: 12%;">MIN</th>
                    <th class="text-center" style="width: 8%;">STATUS</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productStocks as $product)
                @php
                    $currentStock = $product->stocks->sum('quantity');
                    $isLow = $currentStock <= $product->min_stock;
                @endphp
                <tr class="{{ $isLow ? 'stock-low' : '' }}">
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                    <td class="text-right"><strong>{{ number_format($currentStock) }}</strong></td>
                    <td class="text-right">{{ number_format($product->min_stock) }}</td>
                    <td class="text-center">{{ $isLow ? '⚠' : '✓' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- STOK BAHAN --}}
    <div class="page page-break">
        <div class="header">
            <h2>STOK BAHAN BAKU</h2>
        </div>
        
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 45%;">BAHAN</th>
                    <th style="width: 20%;">KATEGORI</th>
                    <th class="text-right" style="width: 15%;">STOK</th>
                    <th class="text-right" style="width: 12%;">MIN</th>
                    <th class="text-center" style="width: 8%;">STATUS</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ingredientStocks as $ingredient)
                @php
                    $currentStock = $ingredient->stocks->sum('quantity');
                    $isLow = $currentStock <= $ingredient->min_stock;
                @endphp
                <tr class="{{ $isLow ? 'stock-low' : '' }}">
                    <td>{{ $ingredient->name }}</td>
                    <td>{{ $ingredient->category->name ?? '-' }}</td>
                    <td class="text-right"><strong>{{ number_format($currentStock) }}</strong></td>
                    <td class="text-right">{{ number_format($ingredient->min_stock) }}</td>
                    <td class="text-center">{{ $isLow ? '⚠' : '✓' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- FOOTER --}}
    <div class="footer">
        {{ auth()->user()->outlet->name ?? 'CuanFlow POS' }} | {{ now()->format('d F Y, H:i') }} WIB
    </div>
</body>
</html>
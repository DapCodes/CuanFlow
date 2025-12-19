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
            font-size: 11px;
            color: #1f2937;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding: 20px;
            background: linear-gradient(135deg, #2563EB 0%, #1e40af 100%);
            color: white;
            border-radius: 8px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .header p {
            margin: 3px 0;
            font-size: 11px;
            opacity: 0.95;
        }
        
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 12px;
            color: #111827;
            padding-bottom: 6px;
            border-bottom: 3px solid #3B82F6;
            display: flex;
            align-items: center;
        }
        
        .section-title::before {
            content: '';
            display: inline-block;
            width: 6px;
            height: 20px;
            background: #3B82F6;
            margin-right: 10px;
            border-radius: 2px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            background: white;
        }
        
        th, td {
            padding: 10px 12px;
            border: 1px solid #E5E7EB;
            text-align: left;
        }
        
        th {
            background: #F3F4F6;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #374151;
        }
        
        tr:nth-child(even) {
            background: #F9FAFB;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border-spacing: 10px;
        }
        
        .summary-row {
            display: table-row;
        }
        
        .summary-card {
            display: table-cell;
            background: #F9FAFB;
            padding: 15px;
            border: 2px solid #E5E7EB;
            border-radius: 8px;
            width: 33%;
        }
        
        .summary-label {
            font-size: 9px;
            color: #6B7280;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            margin-top: 5px;
        }
        
        .text-green { color: #10B981; }
        .text-red { color: #EF4444; }
        .text-blue { color: #3B82F6; }
        .text-orange { color: #F59E0B; }
        .text-purple { color: #8B5CF6; }
        
        .bg-green { background: #D1FAE5 !important; border-color: #10B981 !important; }
        .bg-red { background: #FEE2E2 !important; border-color: #EF4444 !important; }
        .bg-blue { background: #DBEAFE !important; border-color: #3B82F6 !important; }
        .bg-yellow { background: #FEF3C7 !important; border-color: #F59E0B !important; }
        
        .chart-container {
            margin: 20px 0;
            padding: 15px;
            background: white;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
        }
        
        .chart-title {
            font-size: 13px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .bar-chart {
            width: 100%;
        }
        
        .bar-item {
            margin-bottom: 10px;
            display: table;
            width: 100%;
        }
        
        .bar-label {
            display: table-cell;
            width: 30%;
            font-size: 10px;
            color: #4B5563;
            vertical-align: middle;
            padding-right: 10px;
        }
        
        .bar-wrapper {
            display: table-cell;
            width: 50%;
            vertical-align: middle;
        }
        
        .bar-fill {
            height: 24px;
            background: linear-gradient(90deg, #3B82F6, #2563EB);
            border-radius: 4px;
            position: relative;
        }
        
        .bar-value {
            display: table-cell;
            width: 20%;
            text-align: right;
            font-size: 10px;
            font-weight: bold;
            color: #1F2937;
            vertical-align: middle;
            padding-left: 10px;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            margin: 15px 0;
        }
        
        .stats-item {
            display: table-cell;
            text-align: center;
            padding: 12px;
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
        }
        
        .stats-item:first-child {
            border-radius: 6px 0 0 6px;
        }
        
        .stats-item:last-child {
            border-radius: 0 6px 6px 0;
        }
        
        .stats-label {
            font-size: 9px;
            color: #6B7280;
            text-transform: uppercase;
            font-weight: 600;
        }
        
        .stats-value {
            font-size: 16px;
            font-weight: bold;
            color: #1F2937;
            margin-top: 4px;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-green {
            background: #D1FAE5;
            color: #065F46;
        }
        
        .badge-red {
            background: #FEE2E2;
            color: #991B1B;
        }
        
        .badge-blue {
            background: #DBEAFE;
            color: #1E40AF;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #E5E7EB;
            text-align: center;
            font-size: 9px;
            color: #6B7280;
        }
        
        .highlight-box {
            background: #FEF3C7;
            border-left: 4px solid #F59E0B;
            padding: 12px;
            margin: 15px 0;
            border-radius: 4px;
        }
        
        .info-box {
            background: #DBEAFE;
            border-left: 4px solid #3B82F6;
            padding: 12px;
            margin: 15px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    {{-- HEADER --}}
    <div class="header">
        <h1>📊 LAPORAN BISNIS KOMPREHENSIF</h1>
        <p style="font-size: 13px; margin-top: 8px;">Periode: {{ $start->format('d F Y') }} - {{ $end->format('d F Y') }}</p>
        <p>Dicetak pada: {{ now()->format('d F Y H:i:s') }} WIB</p>
        <p style="margin-top: 5px; font-size: 10px;">{{ auth()->user()->outlet->name ?? 'CuanFlow POS' }}</p>
    </div>

    @if(in_array('summary', $sections))
    {{-- RINGKASAN KEUANGAN --}}
    <div class="section">
        <div class="section-title">Ringkasan Keuangan</div>
        
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-card bg-green">
                    <div class="summary-label">💰 Total Pendapatan</div>
                    <div class="summary-value text-green">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                </div>
                <div class="summary-card bg-red">
                    <div class="summary-label">💸 Total Pengeluaran</div>
                    <div class="summary-value text-red">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</div>
                </div>
                <div class="summary-card bg-blue">
                    <div class="summary-label">📈 Laba Kotor</div>
                    <div class="summary-value text-blue">Rp {{ number_format($grossProfit, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-card bg-yellow">
                    <div class="summary-label">🧾 Total Transaksi</div>
                    <div class="summary-value text-orange">{{ number_format($totalTransactions) }}</div>
                </div>
                <div class="summary-card {{ $netProfit >= 0 ? 'bg-green' : 'bg-red' }}">
                    <div class="summary-label">💎 Laba Bersih</div>
                    <div class="summary-value {{ $netProfit >= 0 ? 'text-green' : 'text-red' }}">
                        Rp {{ number_format($netProfit, 0, ',', '.') }}
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">📊 Rata-rata / Transaksi</div>
                    <div class="summary-value text-purple">
                        Rp {{ $totalTransactions > 0 ? number_format($totalRevenue / $totalTransactions, 0, ',', '.') : 0 }}
                    </div>
                </div>
            </div>
        </div>

        @if($netProfit >= 0)
        <div class="info-box">
            <strong>✅ Performa Positif!</strong> Bisnis Anda menghasilkan laba bersih sebesar Rp {{ number_format($netProfit, 0, ',', '.') }} pada periode ini.
        </div>
        @else
        <div class="highlight-box">
            <strong>⚠️ Perhatian!</strong> Bisnis mengalami kerugian sebesar Rp {{ number_format(abs($netProfit), 0, ',', '.') }}. Evaluasi pengeluaran dan strategi penjualan diperlukan.
        </div>
        @endif
    </div>
    @endif

    @if(in_array('charts', $sections))
    {{-- GRAFIK & VISUALISASI --}}
    <div class="section page-break">
        <div class="section-title">Grafik & Visualisasi Data</div>
        
        {{-- Top Products Chart --}}
        <div class="chart-container">
            <div class="chart-title">🏆 Top 5 Produk Terlaris</div>
            <div class="bar-chart">
                @php
                    $maxQty = $topProducts->max('total_qty') ?? 1;
                @endphp
                @foreach($topProducts->take(5) as $product)
                <div class="bar-item">
                    <div class="bar-label">{{ Str::limit($product->product_name, 20) }}</div>
                    <div class="bar-wrapper">
                        <div class="bar-fill" style="width: {{ ($product->total_qty / $maxQty) * 100 }}%;"></div>
                    </div>
                    <div class="bar-value">{{ number_format($product->total_qty) }}</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Payment Methods Chart --}}
        <div class="chart-container" style="margin-top: 20px;">
            <div class="chart-title">💳 Distribusi Metode Pembayaran</div>
            <div class="stats-grid">
                @foreach($paymentMethods as $method)
                <div class="stats-item">
                    <div class="stats-label">{{ ucfirst($method->payment_method) }}</div>
                    <div class="stats-value">{{ $method->total }}</div>
                    <div style="font-size: 9px; color: #6B7280; margin-top: 4px;">
                        Rp {{ number_format($method->total_amount, 0, ',', '.') }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Hourly Sales Chart --}}
        @if($hourlySales->count() > 0)
        <div class="chart-container" style="margin-top: 20px;">
            <div class="chart-title">⏰ Penjualan Per Jam (Peak Hours)</div>
            <div class="bar-chart">
                @php
                    $maxHourly = $hourlySales->max('revenue') ?? 1;
                    $peakHour = $hourlySales->sortByDesc('revenue')->first();
                @endphp
                @foreach($hourlySales->sortByDesc('revenue')->take(8) as $hour)
                <div class="bar-item">
                    <div class="bar-label">
                        {{ sprintf('%02d:00', $hour->hour) }}
                        @if($hour->hour == $peakHour->hour) ⭐ @endif
                    </div>
                    <div class="bar-wrapper">
                        <div class="bar-fill" style="width: {{ ($hour->revenue / $maxHourly) * 100 }}%; background: linear-gradient(90deg, #F59E0B, #D97706);"></div>
                    </div>
                    <div class="bar-value">{{ number_format($hour->transactions) }} tx</div>
                </div>
                @endforeach
            </div>
            <div class="highlight-box" style="margin-top: 15px;">
                <strong>⭐ Peak Hour:</strong> Jam tersibuk adalah pukul {{ sprintf('%02d:00', $peakHour->hour) }} dengan {{ $peakHour->transactions }} transaksi.
            </div>
        </div>
        @endif
    </div>
    @endif

    @if(in_array('sales', $sections))
    {{-- RINCIAN PENJUALAN --}}
    <div class="section page-break">
        <div class="section-title">Rincian Penjualan</div>
        
        <div class="info-box">
            📝 Menampilkan <strong>50 transaksi terbaru</strong> dari total {{ $sales->count() }} transaksi pada periode ini.
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 18%;">Invoice</th>
                    <th style="width: 22%;">Pelanggan</th>
                    <th style="width: 15%;">Metode</th>
                    <th class="text-right" style="width: 20%;">Total</th>
                    <th class="text-center" style="width: 10%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales->take(50) as $sale)
                <tr>
                    <td>{{ $sale->created_at->format('d/m/y H:i') }}</td>
                    <td style="font-weight: 600; color: #1F2937;">{{ $sale->invoice_number }}</td>
                    <td>{{ $sale->customer->name ?? 'Umum' }}</td>
                    <td>
                        <span class="badge badge-blue">{{ ucfirst($sale->payment_method) }}</span>
                    </td>
                    <td class="text-right" style="font-weight: 700; color: #059669;">
                        Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                    </td>
                    <td class="text-center">
                        <span class="badge badge-green">✓</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 30px; color: #6B7280;">
                        Tidak ada data penjualan pada periode ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($sales->count() > 50)
        <p style="text-align: center; font-style: italic; color: #6B7280; margin-top: 10px;">
            ... dan {{ $sales->count() - 50 }} transaksi lainnya.
        </p>
        @endif
    </div>
    @endif

    @if(in_array('expenses', $sections))
    {{-- RINCIAN PENGELUARAN --}}
    <div class="section page-break">
        <div class="section-title">Rincian Pengeluaran</div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 18%;">Kategori</th>
                    <th style="width: 48%;">Deskripsi</th>
                    <th class="text-right" style="width: 22%;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge badge-red">{{ $expense->category ?? 'Lain-lain' }}</span>
                    </td>
                    <td>{{ $expense->description }}</td>
                    <td class="text-right" style="font-weight: 700; color: #DC2626;">
                        Rp {{ number_format($expense->amount, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center" style="padding: 30px; color: #6B7280;">
                        Tidak ada data pengeluaran pada periode ini
                    </td>
                </tr>
                @endforelse
                @if($expenses->count() > 0)
                <tr style="background: #F3F4F6; font-weight: bold;">
                    <td colspan="3" class="text-right" style="padding: 12px;">TOTAL PENGELUARAN:</td>
                    <td class="text-right" style="color: #DC2626; font-size: 13px;">
                        Rp {{ number_format($totalExpenses, 0, ',', '.') }}
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    @endif

    @if(in_array('stock', $sections) && $productStocks && $ingredientStocks)
    {{-- LAPORAN STOK --}}
    <div class="section page-break">
        <div class="section-title">Laporan Stok Produk</div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 35%;">Nama Produk</th>
                    <th style="width: 20%;">Kategori</th>
                    <th class="text-right" style="width: 15%;">Stok</th>
                    <th class="text-center" style="width: 10%;">Satuan</th>
                    <th class="text-right" style="width: 12%;">Min. Stok</th>
                    <th class="text-center" style="width: 8%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productStocks as $product)
                @php
                    $currentStock = $product->stocks->sum('quantity');
                    $isLow = $currentStock <= $product->min_stock;
                @endphp
                <tr style="{{ $isLow ? 'background: #FEE2E2;' : '' }}">
                    <td style="font-weight: 600;">{{ $product->name }}</td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                    <td class="text-right" style="font-weight: 700; color: {{ $isLow ? '#DC2626' : '#059669' }};">
                        {{ number_format($currentStock) }}
                    </td>
                    <td class="text-center">{{ $product->unit->name ?? '-' }}</td>
                    <td class="text-right">{{ number_format($product->min_stock) }}</td>
                    <td class="text-center">
                        @if($isLow)
                            <span class="badge badge-red">⚠️ Rendah</span>
                        @else
                            <span class="badge badge-green">✓ Aman</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 30px;"></div>
        
        <div class="section-title">Laporan Stok Bahan Baku</div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 35%;">Nama Bahan</th>
                    <th style="width: 20%;">Kategori</th>
                    <th class="text-right" style="width: 15%;">Stok</th>
                    <th class="text-center" style="width: 10%;">Satuan</th>
                    <th class="text-right" style="width: 12%;">Min. Stok</th>
                    <th class="text-center" style="width: 8%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ingredientStocks as $ingredient)
                @php
                    $currentStock = $ingredient->stocks->sum('quantity');
                    $isLow = $currentStock <= $ingredient->min_stock;
                @endphp
                <tr style="{{ $isLow ? 'background: #FEE2E2;' : '' }}">
                    <td style="font-weight: 600;">{{ $ingredient->name }}</td>
                    <td>{{ $ingredient->category->name ?? '-' }}</td>
                    <td class="text-right" style="font-weight: 700; color: {{ $isLow ? '#DC2626' : '#059669' }};">
                        {{ number_format($currentStock) }}
                    </td>
                    <td class="text-center">{{ $ingredient->unit->name ?? '-' }}</td>
                    <td class="text-right">{{ number_format($ingredient->min_stock) }}</td>
                    <td class="text-center">
                        @if($isLow)
                            <span class="badge badge-red">⚠️ Rendah</span>
                        @else
                            <span class="badge badge-green">✓ Aman</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- FOOTER --}}
    <div class="footer">
        <p><strong>{{ auth()->user()->outlet->name ?? 'CuanFlow POS' }}</strong></p>
        <p style="margin-top: 5px;">Laporan ini digenerate secara otomatis oleh sistem</p>
        <p style="margin-top: 3px;">© {{ date('Y') }} All Rights Reserved</p>
    </div>
</body>
</html>
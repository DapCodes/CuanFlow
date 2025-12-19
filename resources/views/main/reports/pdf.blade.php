<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Bisnis</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #2563EB;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #111;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary-grid {
            width: 100%;
            margin-bottom: 20px;
        }
        .summary-card {
            background: #f8f9fa;
            padding: 10px;
            border: 1px solid #ddd;
            margin-bottom: 5px;
        }
        .summary-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }
        .summary-value {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        .text-green { color: #10B981; }
        .text-red { color: #EF4444; }
        .text-blue { color: #3B82F6; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Bisnis</h1>
        <p>Periode: {{ $start->format('d M Y') }} - {{ $end->format('d M Y') }}</p>
        <p>Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Ringkasan Keuangan</div>
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; width: 33%; padding: 5px;">
                    <div class="summary-card">
                        <div class="summary-label">Total Pendapatan</div>
                        <div class="summary-value text-green">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                    </div>
                </td>
                <td style="border: none; width: 33%; padding: 5px;">
                    <div class="summary-card">
                        <div class="summary-label">Total Pengeluaran</div>
                        <div class="summary-value text-red">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</div>
                    </div>
                </td>
                <td style="border: none; width: 33%; padding: 5px;">
                    <div class="summary-card">
                        <div class="summary-label">Laba Bersih</div>
                        <div class="summary-value text-blue">Rp {{ number_format($netProfit, 0, ',', '.') }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="border: none; width: 33%; padding: 5px;">
                    <div class="summary-card">
                        <div class="summary-label">Total Transaksi</div>
                        <div class="summary-value">{{ number_format($totalTransactions) }}</div>
                    </div>
                </td>
                <td style="border: none; width: 33%; padding: 5px;">
                    <div class="summary-card">
                        <div class="summary-label">Laba Kotor</div>
                        <div class="summary-value">Rp {{ number_format($grossProfit, 0, ',', '.') }}</div>
                    </div>
                </td>
                <td style="border: none; width: 33%; padding: 5px;"></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Rincian Penjualan (Top 50 Terbaru)</div>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>No. Invoice</th>
                    <th>Pelanggan</th>
                    <th>Metode</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales->take(50) as $sale)
                <tr>
                    <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $sale->invoice_number }}</td>
                    <td>{{ $sale->customer->name ?? 'Umum' }}</td>
                    <td>{{ ucfirst($sale->payment_method) }}</td>
                    <td class="text-right">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data penjualan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($sales->count() > 50)
            <p style="text-align: center; font-style: italic; color: #666;">... dan {{ $sales->count() - 50 }} transaksi lainnya.</p>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Rincian Pengeluaran</div>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Deskripsi</th>
                    <th class="text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}</td>
                    <td>{{ $expense->category ?? '-' }}</td>
                    <td>{{ $expense->description }}</td>
                    <td class="text-right">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data pengeluaran</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Bisnis - {{ auth()->user()->outlet->name ?? 'CuanFlow' }}</title>
    <style>
        @page {
            margin: 1.5cm;
            @bottom-right {
                content: "Hal. " counter(page);
                font-size: 8px;
                color: #94a3b8;
            }
        }
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .text-blue { color: #2563eb; }
        .text-green { color: #059669; }
        .text-red { color: #dc2626; }
        .w-full { width: 100%; }
        
        .table-layout {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }

        .header {
            margin-bottom: 25px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
        }
        
        .section-title {
            background-color: #f8fafc;
            padding: 8px 12px;
            font-size: 11px;
            font-weight: bold;
            color: #1e293b;
            border-left: 4px solid #2563eb;
            margin: 20px 0 10px 0;
            text-transform: uppercase;
        }

        .subsection-title {
            font-size: 10px;
            font-weight: bold;
            color: #475569;
            margin: 15px 0 8px 0;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .data-table th {
            background-color: #1e293b;
            color: white;
            text-align: left;
            padding: 8px;
            font-size: 9px;
        }
        
        .data-table td {
            padding: 7px 8px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }
        
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .summary-box {
            border: 1px solid #e2e8f0;
            padding: 10px;
            border-radius: 6px;
        }
        
        .summary-label {
            font-size: 8px;
            color: #64748b;
            margin-bottom: 2px;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .summary-value {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
        }

        .badge {
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-blue { background-color: #dbeafe; color: #1e40af; }
        .badge-green { background-color: #dcfce7; color: #166534; }
        .badge-red { background-color: #fee2e2; color: #991b1b; }
        .badge-yellow { background-color: #fef9c3; color: #854d0e; }

        .page-break { page-break-before: always; }

        #footer {
            position: fixed;
            bottom: -1cm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
        }
        
        .pagenum:before {
            content: counter(page);
        }
    </style>
</head>
<body>

    <div id="footer">
        {{ auth()->user()->outlet->name ?? 'CuanFlow' }} - Laporan Keseluruhan - Halaman <span class="pagenum"></span>
    </div>

    {{-- HEADER --}}
    <div class="header">
        <table class="table-layout">
            <tr>
                <td style="width: 70%;">
                    <div style="font-size: 22px; font-weight: bold; color: #2563eb;">LAPORAN BISNIS</div>
                    <div style="font-size: 14px; color: #475569; font-weight: bold;">
                        {{ auth()->user()->outlet->name ?? 'Outlet name' }}
                    </div>
                </td>
                <td style="width: 30%; text-align: right; vertical-align: middle;">
                    @php
                        $logoPath = null;
                        if(isset(auth()->user()->outlet->logo) && auth()->user()->outlet->logo) {
                            $path = public_path('storage/' . auth()->user()->outlet->logo);
                            if(file_exists($path)) $logoPath = $path;
                        }
                    @endphp
                    @if($logoPath)
                        <img src="{{ $logoPath }}" style="max-height: 45px; max-width: 140px;">
                    @else
                        <div style="font-size: 18px; font-weight: bold; color: #2563eb;">CUANFLOW</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- INFO --}}
    <table class="table-layout" style="margin-bottom: 20px;">
        <tr>
            <td style="width: 50%;">
                <div class="summary-label">Periode Laporan</div>
                <div style="font-size: 12px; font-weight: bold;">
                    {{ $start->format('d M Y') }} - {{ $end->format('d M Y') }}
                </div>
            </td>
            <td style="width: 50%; text-align: right;">
                <div class="summary-label">Dicetak Pada</div>
                <div style="font-size: 10px;">{{ now()->format('d/m/Y H:i') }} WIB</div>
            </td>
        </tr>
    </table>

    {{-- SUMMARY --}}
    @if(in_array('summary', $sections))
    <div class="section-title">Ringkasan Eksekutif</div>
    <table class="table-layout" style="margin-bottom: 15px;">
        <tr>
            <td style="width: 24%; padding-right: 1%;">
                <div class="summary-box">
                    <div class="summary-label">Total Penjualan</div>
                    <div class="summary-value text-blue">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width: 24%; padding-right: 1%;">
                <div class="summary-box">
                    <div class="summary-label">Total Pengeluaran</div>
                    <div class="summary-value text-red">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width: 24%; padding-right: 1%;">
                <div class="summary-box">
                    <div class="summary-label">Laba Kotor</div>
                    <div class="summary-value">Rp {{ number_format($grossProfit, 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width: 24%;">
                <div class="summary-box" style="background-color: {{ $netProfit >= 0 ? '#f0fdf4' : '#fef2f2' }}; border-color: {{ $netProfit >= 0 ? '#bbf7d0' : '#fecaca' }};">
                    <div class="summary-label">Laba Bersih</div>
                    <div class="summary-value" style="color: {{ $netProfit >= 0 ? '#15803d' : '#b91c1c' }};">
                        Rp {{ number_format($netProfit, 0, ',', '.') }}
                    </div>
                </div>
            </td>
        </tr>
    </table>
    @endif

    {{-- FINANCE DETAIL --}}
    @if(in_array('finance', $sections))
    <div class="section-title">Analisis Keuangan</div>
    <table class="table-layout">
        <tr>
            <td style="width: 48%; vertical-align: top;">
                <div class="subsection-title">Metrik Penjualan</div>
                <table class="w-full" style="font-size: 9px;">
                    <tr>
                        <td style="padding: 3px 0; border-bottom: 1px dotted #e2e8f0;">Jumlah Transaksi:</td>
                        <td class="text-right text-bold">{{ number_format($totalTransactions) }} tx</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 0; border-bottom: 1px dotted #e2e8f0;">Rata-rata Penjualan:</td>
                        <td class="text-right text-bold">Rp {{ $totalTransactions > 0 ? number_format($totalRevenue / $totalTransactions, 0, ',', '.') : 0 }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 0;">Estimasi Total HPP:</td>
                        <td class="text-right text-bold">Rp {{ number_format($totalCogs, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 4%;"></td>
            <td style="width: 48%; vertical-align: top;">
                <div class="subsection-title">Pajak & Diskon</div>
                <table class="w-full" style="font-size: 9px;">
                    <tr>
                        <td style="padding: 3px 0; border-bottom: 1px dotted #e2e8f0;">Total PPN:</td>
                        <td class="text-right text-bold">Rp {{ number_format($totalTax ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 0;">Total Diskon Diberikan:</td>
                        <td class="text-right text-bold text-red">- Rp {{ number_format($totalDiscount ?? 0, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="subsection-title">Penjualan per Kategori</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Kategori</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Total Pendapatan</th>
                <th class="text-right">Kontribusi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesByCategory as $cat)
            <tr>
                <td class="text-bold">{{ $cat['category_name'] }}</td>
                <td class="text-center">{{ number_format($cat['total_qty']) }}</td>
                <td class="text-right text-bold">Rp {{ number_format($cat['total_revenue'], 0, ',', '.') }}</td>
                <td class="text-right">{{ $totalRevenue > 0 ? number_format(($cat['total_revenue'] / $totalRevenue) * 100, 1) : 0 }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- SALES LIST --}}
    @if(in_array('sales', $sections) && count($sales) > 0)
    <div class="section-title">Rincian Transaksi Penjualan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Waktu</th>
                <th>Pelanggan</th>
                <th>Metode</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales->take(50) as $sale)
            <tr>
                <td class="text-bold">{{ $sale->invoice_number }}</td>
                <td>{{ $sale->created_at->format('d/m/y H:i') }}</td>
                <td>{{ \Illuminate\Support\Str::limit($sale->customer->name ?? 'Umum', 20) }}</td>
                <td><span class="badge badge-blue">{{ strtoupper($sale->payment_method) }}</span></td>
                <td class="text-right text-bold text-green">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- EXPENSES --}}
    @if(in_array('expenses', $sections) && count($expenses) > 0)
    <div class="page-break"></div>
    <div class="section-title">Pengeluaran Operasional</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Deskripsi</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses->take(50) as $expense)
            <tr>
                <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d/m/y') }}</td>
                <td><span class="badge badge-yellow">{{ strtoupper($expense->category->name ?? 'Lain') }}</span></td>
                <td>{{ $expense->description }}</td>
                <td class="text-right text-bold text-red">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f8fafc; border-top: 2px solid #e2e8f0;">
                <td colspan="3" class="text-right text-bold">TOTAL PENGELUARAN</td>
                <td class="text-right text-bold text-red">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
    @endif

    {{-- STOCK --}}
    @if(in_array('stock', $sections))
    <div class="section-title">Laporan Persediaan</div>
    <table class="table-layout">
        <tr>
            <td style="width: 49%; vertical-align: top;">
                <div class="subsection-title">Stok Produk</div>
                <table class="data-table" style="font-size: 8px;">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th class="text-right">Stok</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productStocks->sortBy('current_stock')->take(10) as $product)
                        <tr>
                            <td>{{ \Illuminate\Support\Str::limit($product->name, 22) }}</td>
                            <td class="text-right text-bold">{{ number_format($product->current_stock) }}</td>
                            <td class="text-center">
                                @if($product->current_stock <= $product->min_stock)
                                    <span class="badge badge-red">!</span>
                                @else
                                    <span class="badge badge-green">v</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
            <td style="width: 2%;"></td>
            <td style="width: 49%; vertical-align: top;">
                <div class="subsection-title">Stok Bahan Baku</div>
                <table class="data-table" style="font-size: 8px;">
                    <thead>
                        <tr>
                            <th>Bahan</th>
                            <th class="text-right">Stok</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ingredientStocks->sortBy('current_stock')->take(10) as $ingredient)
                        <tr>
                            <td>{{ \Illuminate\Support\Str::limit($ingredient->name, 22) }}</td>
                            <td class="text-right text-bold">{{ number_format($ingredient->current_stock) }}</td>
                            <td class="text-center">
                                @if($ingredient->current_stock <= $ingredient->min_stock)
                                    <span class="badge badge-red">!</span>
                                @else
                                    <span class="badge badge-green">v</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
    @endif

    {{-- CUSTOMER DEBT --}}
    @if(in_array('customer', $sections))
    <div class="section-title">Piutang Pelanggan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Nama Pelanggan</th>
                <th>No. Invoice</th>
                <th class="text-right">Sisa Piutang</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customerDebts->where('status', '!=', 'paid')->take(20) as $debt)
            <tr>
                <td class="text-bold">{{ $debt->customer->name ?? 'Pelanggan' }}</td>
                <td>{{ $debt->sale->invoice_number ?? '-' }}</td>
                <td class="text-right text-bold text-red">Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">Tidak ada piutang aktif</td>
            </tr>
            @endforelse
        </tbody>
        @if($totalPiutang > 0)
        <tfoot>
            <tr style="background-color: #f8fafc; border-top: 2px solid #e2e8f0;">
                <td colspan="2" class="text-right text-bold">TOTAL PIUTANG AKTIF</td>
                <td class="text-right text-bold text-red">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
    @endif

</body>
</html>
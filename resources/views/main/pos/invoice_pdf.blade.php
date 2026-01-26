<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>
        @if($type === 'sale')
            Invoice {{ $data->invoice_number }}
        @else
            {{ $data->type === 'income' ? 'Income' : 'Expense' }} {{ $data->expense_number }}
        @endif
    </title>
    <style>
        @page {
            margin: 0.8cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #1a1a1a;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        
        /* Header Section */
        .header {
            border-bottom: 3px solid #000;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .header-content {
            display: table;
            width: 100%;
        }
        .header-left {
            display: table-cell;
            vertical-align: middle;
            width: 65%;
        }
        .header-right {
            display: table-cell;
            vertical-align: middle;
            width: 35%;
            text-align: right;
        }
        .logo-container {
            max-width: 120px;
            max-height: 60px;
            margin-bottom: 8px;
            filter: grayscale(100%) contrast(1.2);
        }
        .logo-container img {
            max-width: 100%;
            max-height: 60px;
            display: block;
        }
        .business-name {
            font-size: 18pt;
            font-weight: bold;
            color: #000;
            margin: 0 0 4px 0;
            letter-spacing: 0.5px;
        }
        .business-info {
            font-size: 8pt;
            color: #4a4a4a;
            line-height: 1.4;
        }
        .invoice-title {
            font-size: 32pt;
            font-weight: bold;
            color: #d4d4d4;
            margin: 0;
            letter-spacing: 2px;
        }
        .invoice-subtitle {
            font-size: 7pt;
            color: #6b6b6b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 2px;
        }
        
        /* Info Section */
        .info-section {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .info-section td {
            vertical-align: top;
            padding: 8px 10px;
        }
        .info-left {
            width: 50%;
            background-color: #f5f5f5;
            border: 1px solid #d4d4d4;
        }
        .info-right {
            width: 50%;
            padding-left: 15px;
        }
        .info-label {
            font-size: 7pt;
            color: #6b6b6b;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }
        .info-content {
            font-size: 9pt;
            color: #1a1a1a;
            line-height: 1.4;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 3px 0;
            font-size: 9pt;
        }
        .info-table .label-col {
            color: #6b6b6b;
            width: 110px;
            font-size: 8pt;
        }
        .info-table .value-col {
            color: #1a1a1a;
            font-weight: 600;
        }
        .due-date-row {
            color: #000 !important;
            font-weight: bold;
        }
        
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9pt;
        }
        .items-table thead th {
            background-color: #2a2a2a;
            color: #fff;
            text-align: left;
            padding: 8px 6px;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #1a1a1a;
        }
        .items-table tbody td {
            padding: 7px 6px;
            border-bottom: 1px solid #d4d4d4;
            vertical-align: top;
        }
        .items-table tbody tr:nth-child(even) {
            background-color: #fafafa;
        }
        
        /* Summary Section */
        .summary-section {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-section td {
            vertical-align: top;
            padding: 0;
        }
        .summary-left {
            width: 52%;
            padding-right: 15px;
        }
        .summary-right {
            width: 48%;
        }
        
        /* Totals Table */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #f9f9f9;
            border: 1px solid #d4d4d4;
        }
        .totals-table tr {
            border-bottom: 1px solid #e5e5e5;
        }
        .totals-table tr:last-child {
            border-bottom: none;
        }
        .totals-table td {
            padding: 7px 10px;
            font-size: 9pt;
        }
        .totals-label {
            color: #4a4a4a;
            text-align: left;
            font-weight: 500;
        }
        .totals-value {
            text-align: right;
            font-weight: 600;
            color: #1a1a1a;
            width: 130px;
        }
        .grand-total-row {
            background-color: #2a2a2a;
            color: #fff;
            font-weight: bold;
            font-size: 11pt;
        }
        .grand-total-row td {
            padding: 10px;
            border-top: 2px solid #000;
        }
        
        /* Signature Section */
        .signature-section {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
        }
        .signature-box {
            text-align: center;
            width: 35%;
            padding: 10px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin: 50px auto 8px auto;
            width: 150px;
        }
        .signature-name {
            font-weight: bold;
            font-size: 9pt;
            color: #1a1a1a;
        }
        
        /* Footer */
        .footer {
            margin-top: 25px;
            padding-top: 12px;
            border-top: 1px solid #d4d4d4;
            text-align: center;
            font-size: 8pt;
            color: #6b6b6b;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div class="header-left">
                @if($data->outlet->logo)
                <div class="logo-container">
                    <img src="{{ asset('storage/' . $data->outlet->logo) }}" alt="{{ $data->outlet->name }}">
                </div>
                @endif
                <h1 class="business-name">{{ $data->outlet->name }}</h1>
                <div class="business-info">
                    {{ $data->outlet->address ?? 'Alamat tidak tersedia' }}<br>
                    Telp/WA: {{ $data->outlet->phone ?? '-' }}<br>
                    Email: {{ $data->outlet->email ?? '-' }}
                </div>
            </div>
            <div class="header-right">
                <h2 class="invoice-title">
                    @if($type === 'sale')
                        INVOICE
                    @else
                        {{ strtoupper($data->type) }}
                    @endif
                </h2>
                <div class="invoice-subtitle">Official Document</div>
            </div>
        </div>
    </div>

    <!-- Info Section -->
    <table class="info-section">
        <tr>
            <td class="info-left">
                <div class="info-label">
                    @if($type === 'sale')
                        Ditetapkan Untuk:
                    @else
                        Penanggung Jawab:
                    @endif
                </div>
                <div class="info-content">
                    @if($type === 'sale')
                        @if(isset($data->temp_customer_name) && $data->temp_customer_name)
                            <strong>{{ $data->temp_customer_name }}</strong><br>
                            {{ $data->temp_customer_phone ?? '-' }}<br>
                            {{ $data->temp_customer_address ?? '-' }}
                        @elseif($data->customer)
                            <strong>{{ $data->customer->name }}</strong><br>
                            {{ $data->customer->phone }}<br>
                            {{ $data->customer->address ?? '-' }}
                        @else
                            <em>Pelanggan Umum</em>
                        @endif
                    @else
                        <strong>{{ $data->creator->name ?? 'System' }}</strong><br>
                        Status: Approved oleh {{ $data->approvedBy->name ?? 'System' }}
                    @endif
                </div>
            </td>
            <td class="info-right">
                <table class="info-table">
                    <tr>
                        <td class="label-col">No. Dokumen</td>
                        <td class="value-col">{{ $type === 'sale' ? $data->invoice_number : $data->expense_number }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Tanggal</td>
                        <td class="value-col">
                            @if($type === 'sale')
                                {{ $data->created_at->format('d/m/Y H:i') }}
                            @else
                                {{ $data->expense_date->format('d/m/Y') }}
                            @endif
                        </td>
                    </tr>
                    @if($type === 'sale')
                    <tr>
                        <td class="label-col">Kasir</td>
                        <td class="value-col">{{ $data->cashier->name }}</td>
                    </tr>
                    @if($data->invoice_due_date)
                    <tr class="due-date-row">
                        <td class="label-col">Jatuh Tempo</td>
                        <td class="value-col">{{ $data->invoice_due_date->format('d/m/Y') }}</td>
                    </tr>
                    @endif
                    @else
                    <tr>
                        <td class="label-col">Kategori</td>
                        <td class="value-col">{{ $data->category->name ?? 'Lainnya' }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Metode Bayar</td>
                        <td class="value-col">{{ ucfirst($data->payment_method) }}</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    @if($type === 'sale')
    <!-- Items Table for Sale -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Deskripsi Produk</th>
                <th style="text-align: center; width: 60px;">Qty</th>
                <th style="text-align: right; width: 100px;">Unit</th>
                <th style="text-align: right; width: 110px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <div style="font-weight: 600;">{{ $item->product->name }}</div>
                    @if($item->notes && !is_array(json_decode($item->notes)))
                        <div style="font-size: 7pt; font-style: italic; color: #666;">Catatan: {{ $item->notes }}</div>
                    @endif
                </td>
                <td class="text-center">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="text-right"><strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <!-- Description for Expense/Income -->
    <div style="margin-bottom: 20px; padding: 15px; border: 1px solid #d4d4d4; background: #fafafa;">
        <div class="info-label">Deskripsi & Keterangan:</div>
        <div style="font-size: 10pt; margin-top: 5px;">
            {{ $data->description ?? 'Tidak ada deskripsi' }}
        </div>
        @if($data->reference_number)
            <div style="margin-top: 10px; font-size: 8pt; color: #666;">
                <strong>No. Referensi/Faktur:</strong> {{ $data->reference_number }}
            </div>
        @endif
        @if($data->notes)
            <div style="margin-top: 10px; font-size: 8pt; color: #666;">
                <strong>Catatan Internal:</strong> {{ $data->notes }}
            </div>
        @endif
    </div>
    @endif

    <!-- Summary Section -->
    <table class="summary-section">
        <tr>
            <td class="summary-left">
                @if($type === 'sale')
                    <div style="background-color: #f9f9f9; border: 1px solid #d4d4d4; padding: 10px; font-size: 8pt;">
                        <div class="info-label">Metode Pembayaran:</div>
                        <div style="font-weight: bold; margin-bottom: 5px;">
                            {{ str_replace('_', ' ', $data->payment_method) }}
                            @if($data->outletPaymentLink)
                                - {{ $data->outletPaymentLink->paymentMethod->name }}
                            @endif
                        </div>
                        @if(!empty($data->customer_notes))
                            <div style="border-top: 1px solid #ddd; padding-top: 5px; margin-top: 5px;">
                                <strong>Catatan Pelanggan:</strong> "{{ $data->customer_notes }}"
                            </div>
                        @endif
                    </div>
                @else
                    <div style="border: 2px solid #000; padding: 10px; text-align: center;">
                        <div style="font-size: 10pt; font-weight: bold; text-transform: uppercase;">
                            Status Dokumen: TERVALIDASI
                        </div>
                        <div style="font-size: 7pt; color: #666; margin-top: 5px;">
                            Dicetak pada {{ date('d/m/Y H:i') }}
                        </div>
                    </div>
                @endif
            </td>
            <td class="summary-right">
                <table class="totals-table">
                    @if($type === 'sale')
                        <tr>
                            <td class="totals-label">Subtotal</td>
                            <td class="totals-value">Rp {{ number_format($data->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @if($data->discount_amount > 0)
                        <tr>
                            <td class="totals-label">Diskon</td>
                            <td class="totals-value">- Rp {{ number_format($data->discount_amount, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        @if($data->tax_amount > 0)
                        <tr>
                            <td class="totals-label">Pajak ({{ number_format($data->tax_percent, 0) }}%)</td>
                            <td class="totals-value">Rp {{ number_format($data->tax_amount, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr class="grand-total-row">
                            <td>GRAND TOTAL</td>
                            <td class="text-right">Rp {{ number_format($data->grand_total, 0, ',', '.') }}</td>
                        </tr>
                    @else
                        <tr class="grand-total-row">
                            <td>TOTAL {{ strtoupper($data->type) }}</td>
                            <td class="text-right">Rp {{ number_format($data->amount, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <!-- Signature Section -->
    <table class="signature-section">
        <tr>
            <td class="signature-box">
                <div class="info-label">Petugas,</div>
                <div class="signature-line"></div>
                <div class="signature-name">{{ auth()->user()->name }}</div>
            </td>
            <td style="width: 30%;"></td>
            <td class="signature-box">
                <div class="info-label">
                    @if($type === 'sale') Pelanggan, @else Penyetuju, @endif
                </div>
                <div class="signature-line"></div>
                <div class="signature-name">
                    @if($type === 'sale')
                        @if(isset($data->temp_customer_name) && $data->temp_customer_name)
                            {{ $data->temp_customer_name }}
                        @elseif($data->customer)
                            {{ $data->customer->name }}
                        @else
                            ( ............................ )
                        @endif
                    @else
                        {{ $data->approvedBy->name ?? '( ............................ )' }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        © {{ date('Y') }} {{ $data->outlet->name }}. Dokumen ini dihasilkan secara sistematis oleh CuanFlow.
    </div>

</body>
</html>
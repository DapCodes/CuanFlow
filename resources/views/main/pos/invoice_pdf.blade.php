<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $sale->invoice_number }}</title>
    <style>
        @page {
            margin: 1cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            color: #333;
            line-height: 1.4;
        }
        .header {
            border-bottom: 2px solid #f97316;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
        }
        .header .business-name {
            font-size: 24pt;
            font-weight: bold;
            color: #f97316;
            margin: 0;
        }
        .header .business-info {
            font-size: 9pt;
            color: #666;
        }
        .invoice-title {
            text-align: right;
            font-size: 28pt;
            font-weight: bold;
            color: #e5e7eb;
            text-transform: uppercase;
            margin: 0;
        }
        .info-section {
            width: 100%;
            margin-bottom: 30px;
        }
        .info-section td {
            vertical-align: top;
            width: 50%;
        }
        .info-label {
            font-size: 9pt;
            color: #666;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .info-content {
            font-weight: bold;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .table th {
            background-color: #f97316;
            color: white;
            text-align: left;
            padding: 10px;
            font-size: 10pt;
        }
        .table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10pt;
        }
        .table tr:nth-child(even) {
            background-color: #fffaf8;
        }
        .totals-section {
            width: 100%;
        }
        .totals-section td {
            vertical-align: top;
        }
        .totals-table {
            width: 100%;
            margin-left: auto;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 5px 10px;
            text-align: right;
        }
        .totals-table .label {
            color: #666;
        }
        .totals-table .value {
            font-weight: bold;
            width: 120px;
        }
        .totals-table .grand-total {
            font-size: 14pt;
            color: #f97316;
            border-top: 2px solid #f97316;
            padding-top: 10px;
            margin-top: 10px;
        }
        .footer {
            margin-top: 50px;
            font-size: 9pt;
            color: #666;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        .debt-warning {
            color: #ef4444;
            font-weight: bold;
            border: 1px solid #fee2e2;
            background-color: #fef2f2;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 10pt;
        }
        .signature-section {
            margin-top: 40px;
            width: 100%;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
    <style>
        /* ... existing styles ... */
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            width: 100%;
        }

        /* Structured Notes Styles */
        .notes-container {
            margin-top: 15px;
        }
        .structured-box {
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
            font-size: 9pt;
        }
        .box-blue {
            background-color: #eef2ff;
            border: 1px solid #e0e7ff;
            color: #3730a3;
        }
        .box-gray {
            background-color: #f9fafb;
            border: 1px solid #f3f4f6;
            color: #374151;
        }
        .box-green {
            background-color: #ecfdf5;
            border: 1px solid #d1fae5;
            color: #065f46;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .badge-blue { background-color: #4f46e5; color: white; }
        .badge-gray { background-color: #6b7280; color: white; }
        
        .adjustment-item {
            display: table;
            width: 100%;
            margin-bottom: 4px;
        }
        .adjustment-item div { display: table-cell; }
        .adj-name { font-weight: bold; }
        .adj-price { text-align: right; }
        .line-through { text-decoration: line-through; color: #9ca3af; }
        
        .discount-item-row {
            border-bottom: 1px dashed #e5e7eb;
            padding: 5px 0;
        }
        .discount-item-row:last-child { border-bottom: none; }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <h1 class="business-name">{{ $sale->outlet->name }}</h1>
                    <div class="business-info">
                        {{ $sale->outlet->address ?? 'Alamat tidak tersedia' }}<br>
                        Telp/WA: {{ $sale->outlet->phone ?? '-' }}<br>
                        Email: {{ $sale->outlet->email ?? '-' }}
                    </div>
                </td>
                <td>
                    <h2 class="invoice-title">INVOICE</h2>
                </td>
            </tr>
        </table>
    </div>

    @php
        $rawNotes = $sale->notes ?? '';
        $decoded = null;
        if (is_string($rawNotes) && trim($rawNotes) !== '') {
            $decoded = json_decode($rawNotes, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $decoded = null;
            }
        }
        $plan = is_array($decoded) ? ($decoded['discount_plan'] ?? null) : null;
        $typeInfo = is_array($decoded) ? ($decoded['customer_type_info'] ?? null) : null;
    @endphp

    <table class="info-section">
        <tr>
            <td>
                <div class="info-label">Ditetapkan Untuk:</div>
                <div class="info-content">
                    @if(isset($sale->temp_customer_name) && $sale->temp_customer_name)
                        {{ $sale->temp_customer_name }}<br>
                        {{ $sale->temp_customer_phone ?? '-' }}<br>
                        {{ $sale->temp_customer_address ?? '-' }}
                    @elseif($sale->customer)
                        {{ $sale->customer->name }}<br>
                        {{ $sale->customer->phone }}<br>
                        {{ $sale->customer->address ?? '-' }}
                    @else
                        -
                    @endif
                </div>
            </td>
            <td style="text-align: right;">
                <table style="margin-left: auto;">
                    <tr>
                        <td style="text-align: left; padding-right: 20px;" class="info-label">No. Invoice</td>
                        <td style="text-align: right;" class="info-content">{{ $sale->invoice_number }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: left;" class="info-label">Tanggal Transaksi</td>
                        <td style="text-align: right;" class="info-content">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: left;" class="info-label">Kasir</td>
                        <td style="text-align: right;" class="info-content">{{ $sale->cashier->name }}</td>
                    </tr>
                    @if($sale->invoice_due_date)
                    <tr>
                        <td style="text-align: left; color: #ef4444;" class="info-label">Jatuh Tempo</td>
                        <td style="text-align: right; color: #ef4444;" class="info-content">{{ $sale->invoice_due_date->format('d/m/Y') }}</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    @if($sale->debt && $sale->debt->remaining_amount > 0)
    <div class="debt-warning">
        Pemberitahuan: Transaksi ini memiliki sisa piutang sebesar 
        <strong>Rp {{ number_format($sale->debt->remaining_amount, 0, ',', '.') }}</strong>
        @if($sale->debt->due_date)
            yang jatuh tempo pada <strong>{{ $sale->debt->due_date->format('d/m/Y') }}</strong>.
        @endif
    </div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Deskripsi Produk</th>
                <th style="text-align: center; width: 60px;">Jumlah</th>
                <th style="text-align: right; width: 100px;">Harga Satuan</th>
                <th style="text-align: right; width: 120px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <div style="font-weight: bold;">{{ $item->product->name }}</div>
                    @if($item->notes && !is_array(json_decode($item->notes)))
                        <div style="font-size: 8pt; color: #666;">Catatan: {{ $item->notes }}</div>
                    @endif
                </td>
                <td style="text-align: center;">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                <td style="text-align: right;">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td style="text-align: right;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-section">
        <tr>
            <td style="width: 55%;">
                <div class="info-label">Metode Pembayaran:</div>
                <div class="info-content" style="text-transform: uppercase; margin-bottom: 20px;">
                    {{ str_replace('_', ' ', $sale->payment_method) }}
                    @if($sale->outletPaymentLink)
                        ({{ $sale->outletPaymentLink->paymentMethod->name }})
                    @endif
                </div>
                
                <div class="notes-container">
                    <div class="info-label">Catatan & Promo:</div>
                    
                    @if($typeInfo)
                        <div class="structured-box box-blue">
                            <span class="badge badge-blue">{{ $typeInfo['label'] }}</span>
                            <div style="font-weight: bold; margin-bottom: 8px;">
                                Total Hemat: Rp {{ number_format($typeInfo['total_savings'] ?? 0, 0, ',', '.') }}
                            </div>
                            <div class="space-y-1">
                                @foreach($typeInfo['adjustments'] ?? [] as $adj)
                                    <div class="adjustment-item">
                                        <div class="adj-name">{{ $adj['qty'] }}x {{ $adj['product_name'] }}</div>
                                        <div class="adj-price">
                                            <span class="line-through">Rp {{ number_format($adj['original_price'], 0, ',', '.') }}</span>
                                            » <b>Rp {{ number_format($adj['applied_price'], 0, ',', '.') }}</b>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($plan)
                        <div class="structured-box box-gray">
                            @php
                                $appliedDiscounts = $plan['applied_discounts'] ?? [];
                            @endphp

                            @if(!empty($appliedDiscounts))
                                @foreach($appliedDiscounts as $applied)
                                    <div class="discount-item-row">
                                        <table style="width: 100%;">
                                            <tr>
                                                <td style="text-align: left;">
                                                    <span class="badge badge-gray" style="margin-bottom: 2px;">{{ $applied['type'] ?? 'PROMO' }}</span><br>
                                                    <b>{{ $applied['name'] ?? 'Diskon' }}</b>
                                                </td>
                                                <td style="text-align: right; vertical-align: middle;">
                                                    <b style="color: #ea580c;">- Rp {{ number_format((float)($applied['amount'] ?? 0), 0, ',', '.') }}</b>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                @endforeach
                            @endif

                            {{-- Free Items (BOGO) --}}
                            @if(($plan['free_item_quota'] ?? 0) > 0)
                                <div class="structured-box box-green" style="margin-top: 10px; margin-bottom: 0;">
                                    <div style="font-weight: bold; text-transform: uppercase; font-size: 8pt; margin-bottom: 5px;">Hadiah Gratis</div>
                                    @php
                                        $freeItems = [];
                                        foreach ($appliedDiscounts as $ad) {
                                            if (($ad['type'] ?? '') === 'buy_x_get_y' && !empty($ad['free_items'])) {
                                                $freeItems = array_merge($freeItems, $ad['free_items']);
                                            }
                                        }
                                    @endphp
                                    @foreach($freeItems as $fi)
                                        <div style="font-size: 9pt; color: #059669;">
                                            • {{ $fi['product_name'] ?? 'Item' }} <b>x{{ $fi['free_qty'] ?? 1 }}</b>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    @if(!$plan && !$typeInfo)
                        <div style="font-size: 9pt;">{{ $rawNotes ?: 'Tidak ada catatan.' }}</div>
                    @endif

                    @if(!empty($sale->customer_notes))
                        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #e5e7eb;">
                            <div class="info-label" style="font-size: 8pt;">Catatan Pelanggan:</div>
                            <div style="font-size: 9pt; font-style: italic;">"{{ $sale->customer_notes }}"</div>
                        </div>
                    @endif
                </div>
            </td>
            <td style="width: 45%;">
                <table class="totals-table">
                    <tr>
                        <td class="label">Subtotal</td>
                        <td class="value">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if($sale->discount_amount > 0)
                    <tr>
                        <td class="label">Diskon</td>
                        <td class="value">- Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    @if($sale->tax_amount > 0)
                    <tr>
                        <td class="label">Pajak ({{ number_format($sale->tax_percent, 0) }}%)</td>
                        <td class="value">Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label grand-total">Grand Total</td>
                        <td class="value grand-total">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label" style="padding-top: 10px;">Dibayar</td>
                        <td class="value" style="padding-top: 10px;">Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</td>
                    </tr>
                    @if($sale->change_amount > 0)
                    <tr>
                        <td class="label">Kembalian</td>
                        <td class="value">Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <table class="signature-section">
        <tr>
            <td class="signature-box">
                <div class="info-label">Hormat Kami,</div>
                <div class="signature-line"></div>
                <div style="font-weight: bold; margin-top: 5px;">{{ auth()->user()->name }}</div>
                <div style="font-size: 8pt; color: #666;">Admin / Kasir</div>
            </td>
            <td style="width: 100px;"></td>
            <td class="signature-box" style="margin-left: auto;">
                <div class="info-label">Pelanggan,</div>
                <div class="signature-line"></div>
                <div style="font-weight: bold; margin-top: 5px;">
                    @if(isset($sale->temp_customer_name) && $sale->temp_customer_name)
                        {{ $sale->temp_customer_name }}
                    @elseif($sale->customer)
                        {{ $sale->customer->name }}
                    @else
                        ( ............................ )
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Terima kasih telah berbelanja di {{ $sale->outlet->name }}.<br>
        Semoga hari Anda menyenangkan!
    </div>

</body>
</html>


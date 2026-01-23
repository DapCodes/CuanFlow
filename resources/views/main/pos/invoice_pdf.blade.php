<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $sale->invoice_number }}</title>
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
        
        /* Debt Warning */
        .debt-warning {
            background-color: #f0f0f0;
            border-left: 4px solid #000;
            padding: 8px 12px;
            margin-bottom: 15px;
            font-size: 9pt;
            color: #1a1a1a;
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
        .items-table tbody tr:hover {
            background-color: #f0f0f0;
        }
        .product-name {
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 2px;
        }
        .product-note {
            font-size: 7pt;
            color: #6b6b6b;
            font-style: italic;
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
        
        /* Notes Container */
        .notes-container {
            background-color: #f9f9f9;
            border: 1px solid #d4d4d4;
            padding: 10px;
            font-size: 8pt;
        }
        .notes-title {
            font-size: 7pt;
            color: #6b6b6b;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        .payment-method {
            background-color: #fff;
            border: 1px solid #d4d4d4;
            padding: 8px;
            margin-bottom: 10px;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Structured Notes */
        .structured-box {
            background-color: #fff;
            border: 1px solid #c0c0c0;
            padding: 8px;
            margin-bottom: 8px;
            font-size: 8pt;
        }
        .box-header {
            background-color: #2a2a2a;
            color: #fff;
            padding: 4px 6px;
            margin: -8px -8px 6px -8px;
            font-weight: bold;
            font-size: 7pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .savings-amount {
            font-size: 9pt;
            font-weight: bold;
            margin-bottom: 6px;
            color: #1a1a1a;
        }
        .adjustment-item {
            padding: 4px 0;
            border-bottom: 1px dotted #d4d4d4;
            display: table;
            width: 100%;
        }
        .adjustment-item:last-child {
            border-bottom: none;
        }
        .adj-name {
            display: table-cell;
            width: 60%;
            font-weight: 500;
        }
        .adj-price {
            display: table-cell;
            width: 40%;
            text-align: right;
        }
        .line-through {
            text-decoration: line-through;
            color: #999;
            font-size: 7pt;
        }
        
        .discount-item {
            padding: 6px 0;
            border-bottom: 1px dotted #d4d4d4;
        }
        .discount-item:last-child {
            border-bottom: none;
        }
        .discount-badge {
            background-color: #2a2a2a;
            color: #fff;
            padding: 2px 5px;
            font-size: 6pt;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
            margin-bottom: 3px;
        }
        .discount-name {
            font-weight: 600;
            font-size: 8pt;
            color: #1a1a1a;
        }
        .discount-amount {
            font-weight: bold;
            color: #1a1a1a;
        }
        
        .free-items-box {
            background-color: #fafafa;
            border: 1px dashed #999;
            padding: 6px;
            margin-top: 6px;
        }
        .free-items-title {
            font-weight: bold;
            font-size: 7pt;
            text-transform: uppercase;
            margin-bottom: 4px;
            color: #4a4a4a;
        }
        .free-item {
            font-size: 8pt;
            padding: 2px 0;
            color: #1a1a1a;
        }
        
        .customer-notes {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #d4d4d4;
            font-style: italic;
            color: #4a4a4a;
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
        .signature-role {
            font-size: 7pt;
            color: #6b6b6b;
            margin-top: 2px;
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
        
        /* Utilities */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div class="header-left">
                @if($sale->outlet->logo)
                <div class="logo-container">
                    <img src="{{ asset('storage/' . $sale->outlet->logo) }}" alt="{{ $sale->outlet->name }}">
                </div>
                @endif
                <h1 class="business-name">{{ $sale->outlet->name }}</h1>
                <div class="business-info">
                    {{ $sale->outlet->address ?? 'Alamat tidak tersedia' }}<br>
                    Telp/WA: {{ $sale->outlet->phone ?? '-' }}<br>
                    Email: {{ $sale->outlet->email ?? '-' }}
                </div>
            </div>
            <div class="header-right">
                <h2 class="invoice-title">INVOICE</h2>
                <div class="invoice-subtitle">Official Document</div>
            </div>
        </div>
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

    <!-- Info Section -->
    <table class="info-section">
        <tr>
            <td class="info-left">
                <div class="info-label">Ditetapkan Untuk:</div>
                <div class="info-content">
                    @if(isset($sale->temp_customer_name) && $sale->temp_customer_name)
                        <strong>{{ $sale->temp_customer_name }}</strong><br>
                        {{ $sale->temp_customer_phone ?? '-' }}<br>
                        {{ $sale->temp_customer_address ?? '-' }}
                    @elseif($sale->customer)
                        <strong>{{ $sale->customer->name }}</strong><br>
                        {{ $sale->customer->phone }}<br>
                        {{ $sale->customer->address ?? '-' }}
                    @else
                        <em>Pelanggan Umum</em>
                    @endif
                </div>
            </td>
            <td class="info-right">
                <table class="info-table">
                    <tr>
                        <td class="label-col">No. Invoice</td>
                        <td class="value-col">{{ $sale->invoice_number }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Tanggal Transaksi</td>
                        <td class="value-col">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Kasir</td>
                        <td class="value-col">{{ $sale->cashier->name }}</td>
                    </tr>
                    @if($sale->invoice_due_date)
                    <tr class="due-date-row">
                        <td class="label-col">Jatuh Tempo</td>
                        <td class="value-col">{{ $sale->invoice_due_date->format('d/m/Y') }}</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <!-- Debt Warning -->
    @if($sale->debt && $sale->debt->remaining_amount > 0)
    <div class="debt-warning">
        <strong>⚠ PEMBERITAHUAN PIUTANG:</strong> Transaksi ini memiliki sisa piutang sebesar 
        <strong>Rp {{ number_format($sale->debt->remaining_amount, 0, ',', '.') }}</strong>
        @if($sale->debt->due_date)
            yang jatuh tempo pada <strong>{{ $sale->debt->due_date->format('d/m/Y') }}</strong>
        @endif
    </div>
    @endif

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Deskripsi Produk</th>
                <th style="text-align: center; width: 60px;">Qty</th>
                <th style="text-align: right; width: 100px;">Harga Satuan</th>
                <th style="text-align: right; width: 110px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <div class="product-name">{{ $item->product->name }}</div>
                    @if($item->notes && !is_array(json_decode($item->notes)))
                        <div class="product-note">Catatan: {{ $item->notes }}</div>
                    @endif
                </td>
                <td class="text-center">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="text-right"><strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary Section -->
    <table class="summary-section">
        <tr>
            <td class="summary-left">
                <!-- Payment Method -->
                <div class="payment-method">
                    {{ str_replace('_', ' ', $sale->payment_method) }}
                    @if($sale->outletPaymentLink)
                        - {{ $sale->outletPaymentLink->paymentMethod->name }}
                    @endif
                </div>
                
                <!-- Notes Container -->
                <div class="notes-container">
                    <div class="notes-title">Catatan & Promosi</div>
                    
                    @if($typeInfo)
                        <div class="structured-box">
                            <div class="box-header">{{ $typeInfo['label'] }}</div>
                            <div class="savings-amount">
                                Total Hemat: Rp {{ number_format($typeInfo['total_savings'] ?? 0, 0, ',', '.') }}
                            </div>
                            @foreach($typeInfo['adjustments'] ?? [] as $adj)
                                <div class="adjustment-item">
                                    <div class="adj-name">{{ $adj['qty'] }}x {{ $adj['product_name'] }}</div>
                                    <div class="adj-price">
                                        <span class="line-through">Rp {{ number_format($adj['original_price'], 0, ',', '.') }}</span>
                                        → <strong>Rp {{ number_format($adj['applied_price'], 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($plan)
                        <div class="structured-box">
                            <div class="box-header">Diskon & Promo</div>
                            @php
                                $appliedDiscounts = $plan['applied_discounts'] ?? [];
                            @endphp

                            @if(!empty($appliedDiscounts))
                                @foreach($appliedDiscounts as $applied)
                                    <div class="discount-item">
                                        <span class="discount-badge">{{ $applied['type'] ?? 'PROMO' }}</span><br>
                                        <span class="discount-name">{{ $applied['name'] ?? 'Diskon' }}</span>
                                        <div class="discount-amount text-right">
                                            - Rp {{ number_format((float)($applied['amount'] ?? 0), 0, ',', '.') }}
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            @if(($plan['free_item_quota'] ?? 0) > 0)
                                <div class="free-items-box">
                                    <div class="free-items-title">🎁 Hadiah Gratis</div>
                                    @php
                                        $freeItems = [];
                                        foreach ($appliedDiscounts as $ad) {
                                            if (($ad['type'] ?? '') === 'buy_x_get_y' && !empty($ad['free_items'])) {
                                                $freeItems = array_merge($freeItems, $ad['free_items']);
                                            }
                                        }
                                    @endphp
                                    @foreach($freeItems as $fi)
                                        <div class="free-item">
                                            • {{ $fi['product_name'] ?? 'Item' }} <strong>x{{ $fi['free_qty'] ?? 1 }}</strong>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    @if(!$plan && !$typeInfo && $rawNotes)
                        <div style="font-size: 8pt; padding: 6px; background: #fff; border: 1px solid #d4d4d4;">
                            {{ $rawNotes }}
                        </div>
                    @endif

                    @if(!empty($sale->customer_notes))
                        <div class="customer-notes">
                            <strong style="font-size: 7pt; text-transform: uppercase; color: #6b6b6b;">Catatan Pelanggan:</strong><br>
                            "{{ $sale->customer_notes }}"
                        </div>
                    @endif
                </div>
            </td>
            <td class="summary-right">
                <table class="totals-table">
                    <tr>
                        <td class="totals-label">Subtotal</td>
                        <td class="totals-value">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if($sale->discount_amount > 0)
                    <tr>
                        <td class="totals-label">Diskon</td>
                        <td class="totals-value">- Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    @if($sale->tax_amount > 0)
                    <tr>
                        <td class="totals-label">Pajak ({{ number_format($sale->tax_percent, 0) }}%)</td>
                        <td class="totals-value">Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr class="grand-total-row">
                        <td>GRAND TOTAL</td>
                        <td class="text-right">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="background-color: #fff;">
                        <td class="totals-label" style="padding-top: 10px;">Dibayar</td>
                        <td class="totals-value" style="padding-top: 10px;">Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</td>
                    </tr>
                    @if($sale->change_amount > 0)
                    <tr style="background-color: #fff;">
                        <td class="totals-label">Kembalian</td>
                        <td class="totals-value">Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</td>
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
                <div class="info-label">Hormat Kami,</div>
                <div class="signature-line"></div>
                <div class="signature-name">{{ auth()->user()->name }}</div>
                <div class="signature-role">Admin / Kasir</div>
            </td>
            <td style="width: 30%;"></td>
            <td class="signature-box">
                <div class="info-label">Pelanggan,</div>
                <div class="signature-line"></div>
                <div class="signature-name">
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

    <!-- Footer -->
    <div class="footer">
        Terima kasih telah berbelanja di {{ $sale->outlet->name }}<br>
        Dokumen ini dicetak secara otomatis dan sah tanpa tanda tangan basah
    </div>

</body>
</html>
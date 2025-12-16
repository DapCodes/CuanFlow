<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk - {{ $sale->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            line-height: 1.4;
            color: #000;
            padding: 10px;
        }
        
        .receipt {
            width: 100%;
            max-width: 80mm;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 8px;
        }
        
        .outlet-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        
        .outlet-info {
            font-size: 9px;
            line-height: 1.3;
            margin-bottom: 2px;
        }
        
        .invoice-section {
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #000;
            font-size: 9px;
        }
        
        .invoice-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        
        .invoice-label {
            font-weight: bold;
        }
        
        .items-section {
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #000;
        }
        
        .items-header {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            margin-bottom: 5px;
            padding-bottom: 3px;
            border-bottom: 1px solid #000;
            font-size: 9px;
        }
        
        .item {
            margin-bottom: 6px;
            font-size: 9px;
        }
        
        .item-name {
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .item-details {
            display: flex;
            justify-content: space-between;
            color: #333;
        }
        
        .item-qty-price {
            text-align: left;
        }
        
        .item-subtotal {
            text-align: right;
            font-weight: bold;
        }
        
        .summary-section {
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px dashed #000;
            font-size: 10px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .summary-row.discount {
            color: #d32f2f;
        }
        
        .summary-row.total {
            font-size: 13px;
            font-weight: bold;
            margin-top: 5px;
            padding-top: 5px;
            border-top: 1px solid #000;
        }
        
        .payment-section {
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #000;
            font-size: 10px;
        }
        
        .payment-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .payment-method {
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .change-row {
            font-size: 11px;
            font-weight: bold;
        }
        
        .footer {
            text-align: center;
            font-size: 9px;
            margin-top: 10px;
        }
        
        .footer-message {
            margin-bottom: 5px;
            font-style: italic;
        }
        
        .barcode {
            text-align: center;
            margin: 10px 0;
        }
        
        .qr-placeholder {
            width: 80px;
            height: 80px;
            margin: 10px auto;
            border: 1px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
        }
        
        .brand {
            margin-top: 10px;
            font-size: 8px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <!-- Header -->
        <div class="header">
            @if($sale->outlet->logo)
            <div class="logo">
                <img src="{{ public_path('storage/' . $sale->outlet->logo) }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            @endif
            
            <div class="outlet-name">{{ $sale->outlet->name }}</div>
            
            @if($sale->outlet->address)
            <div class="outlet-info">{{ $sale->outlet->address }}</div>
            @endif
            
            @if($sale->outlet->phone)
            <div class="outlet-info">Telp: {{ $sale->outlet->phone }}</div>
            @endif
            
            @if($sale->outlet->email)
            <div class="outlet-info">{{ $sale->outlet->email }}</div>
            @endif
        </div>
        
        <!-- Invoice Info -->
        <div class="invoice-section">
            <div class="invoice-row">
                <span class="invoice-label">No Invoice:</span>
                <span>{{ $sale->invoice_number }}</span>
            </div>
            <div class="invoice-row">
                <span class="invoice-label">Tanggal:</span>
                <span>{{ $sale->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="invoice-row">
                <span class="invoice-label">Kasir:</span>
                <span>{{ $sale->cashier->name }}</span>
            </div>
            @if($sale->customer)
            <div class="invoice-row">
                <span class="invoice-label">Pelanggan:</span>
                <span>{{ $sale->customer->name }}</span>
            </div>
            @endif
        </div>
        
        <!-- Items -->
        <div class="items-section">
            <div class="items-header">
                <span>ITEM</span>
                <span>TOTAL</span>
            </div>
            
            @foreach($sale->items as $item)
            <div class="item">
                <div class="item-name">{{ $item->product_name }}</div>
                <div class="item-details">
                    <div class="item-qty-price">
                        {{ number_format($item->quantity, 0) }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                        @if($item->discount_amount > 0)
                        <br><span style="color: #d32f2f;">Diskon: -Rp {{ number_format($item->discount_amount, 0, ',', '.') }}</span>
                        @endif
                    </div>
                    <div class="item-subtotal">
                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Summary -->
        <div class="summary-section">
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</span>
            </div>
            
            @if($sale->discount_amount > 0)
            <div class="summary-row discount">
                <span>Diskon:</span>
                <span>- Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}</span>
            </div>
            @endif
            
            @if($sale->tax_amount > 0)
            <div class="summary-row">
                <span>Pajak ({{ $sale->tax_percent }}%):</span>
                <span>Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}</span>
            </div>
            @endif
            
            <div class="summary-row total">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</span>
            </div>
        </div>
        
        <!-- Payment -->
        <div class="payment-section">
            <div class="payment-row">
                <span>Metode Bayar:</span>
                <span class="payment-method">{{ strtoupper($sale->payment_method) }}</span>
            </div>
            
            @if($sale->payment_method === 'cash')
            <div class="payment-row">
                <span>Tunai:</span>
                <span>Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span>
            </div>
            <div class="payment-row change-row">
                <span>Kembalian:</span>
                <span>Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span>
            </div>
            @endif
            
            @if(in_array($sale->payment_method, ['qris', 'transfer']))
            <div class="payment-row">
                <span>Dibayar:</span>
                <span>Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span>
            </div>
            @if($sale->midtrans_transaction_id)
            <div class="payment-row" style="font-size: 8px;">
                <span>Ref:</span>
                <span>{{ $sale->midtrans_transaction_id }}</span>
            </div>
            @endif
            @endif
        </div>
        
@php
    use BaconQrCode\Renderer\ImageRenderer;
    use BaconQrCode\Renderer\Image\SvgImageBackEnd;
    use BaconQrCode\Renderer\RendererStyle\RendererStyle;
    use BaconQrCode\Writer;

    $url = route('receipts.show', $sale->invoice_number);

    $renderer = new ImageRenderer(
        new RendererStyle(140),
        new SvgImageBackEnd()
    );

    $writer = new Writer($renderer);
    $qrCodeSvg = $writer->writeString($url);
    $qrCodeBase64 = base64_encode($qrCodeSvg);
@endphp

<div class="barcode">
    <img src="data:image/svg+xml;base64,{{ $qrCodeBase64 }}" alt="QR Code" style="width: 140px; height: 140px;">
</div>



        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-message">Terima kasih atas kunjungan Anda!</div>
            <div class="footer-message">Barang yang sudah dibeli tidak dapat dikembalikan</div>
            <div class="brand">Powered by CuanFlow POS</div>
        </div>
    </div>
</body>
</html>
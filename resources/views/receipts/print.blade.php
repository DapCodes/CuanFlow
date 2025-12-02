<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Print Struk - {{ $sale->invoice_number }}</title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
            @page { margin: 0; size: 80mm auto; }
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            padding: 15px;
            width: 80mm;
            margin: 0 auto;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #4F46E5;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .print-button:hover {
            background: #4338CA;
        }
        
        .receipt {
            width: 100%;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
        }
        
        .logo {
            width: 70px;
            height: 70px;
            margin: 0 auto 10px;
        }
        
        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .outlet-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .outlet-info {
            font-size: 11px;
            line-height: 1.3;
            margin-bottom: 3px;
        }
        
        .invoice-section {
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #000;
            font-size: 11px;
        }
        
        .invoice-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .invoice-label {
            font-weight: bold;
        }
        
        .items-section {
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
        }
        
        .items-header {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            margin-bottom: 6px;
            padding-bottom: 4px;
            border-bottom: 1px solid #000;
            font-size: 11px;
        }
        
        .item {
            margin-bottom: 8px;
            font-size: 11px;
        }
        
        .item-name {
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .item-details {
            display: flex;
            justify-content: space-between;
        }
        
        .item-subtotal {
            text-align: right;
            font-weight: bold;
        }
        
        .summary-section {
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 2px dashed #000;
            font-size: 12px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }
        
        .summary-row.discount {
            color: #d32f2f;
        }
        
        .summary-row.total {
            font-size: 16px;
            font-weight: bold;
            margin-top: 6px;
            padding-top: 6px;
            border-top: 1px solid #000;
        }
        
        .payment-section {
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #000;
            font-size: 12px;
        }
        
        .payment-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }
        
        .payment-method {
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .change-row {
            font-size: 13px;
            font-weight: bold;
        }
        
        .footer {
            text-align: center;
            font-size: 11px;
            margin-top: 12px;
        }
        
        .footer-message {
            margin-bottom: 6px;
            font-style: italic;
        }
        
        .barcode {
            text-align: center;
            margin: 12px 0;
            font-family: 'Libre Barcode 39', cursive;
            font-size: 32px;
            letter-spacing: 2px;
        }
        
        .brand {
            margin-top: 12px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button no-print">
        <i class="fas fa-print"></i> Print Struk
    </button>
    
    <div class="receipt">
        <!-- Header -->
        <div class="header">
            @if($sale->outlet->logo)
            <div class="logo">
                <img src="{{ Storage::url($sale->outlet->logo) }}" alt="Logo">
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
            <div class="payment-row" style="font-size: 10px;">
                <span>Ref:</span>
                <span>{{ $sale->midtrans_transaction_id }}</span>
            </div>
            @endif
            @endif
        </div>
        
        <!-- Barcode -->
        <div class="barcode">
            {!! QrCode::size(100)->generate($sale->invoice_number) !!}
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-message">Terima kasih atas kunjungan Anda!</div>
            <div class="footer-message">Barang yang sudah dibeli tidak dapat dikembalikan</div>
            <div class="brand">Powered by CuanFlow POS</div>
        </div>
    </div>
    
    <script>
        // Auto print on load (optional)
        // window.onload = function() { window.print(); }
        
        // Close after print
        window.onafterprint = function() {
            // window.close();
        }
    </script>
</body>
</html>
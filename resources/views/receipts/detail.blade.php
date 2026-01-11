<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi - {{ $sale->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        .receipt-card {
            background: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
            gap: 0.5rem;
        }
        .star-rating input {
            display: none;
        }
        .star-rating label {
            cursor: pointer;
            font-size: 2rem;
            color: #d1d5db;
            transition: color 0.2s;
        }
        .star-rating input:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #fbbf24;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md receipt-card rounded-2xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-br from-orange-500 to-red-600 p-6 text-white text-center">
            @if(!empty($sale->outlet->logo))
                <img src="{{ asset('storage/' . $sale->outlet->logo) }}" alt="Logo Outlet" class="w-16 h-16 rounded-full mx-auto mb-4 object-cover bg-white shadow-md">
            @else
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                    <i class="fas fa-shopping-bag text-3xl"></i>
                </div>
            @endif
            <h1 class="text-xl font-bold mb-1">{{ $sale->outlet->name ?? 'CuanFlow Store' }}</h1>
            <p class="text-white/80 text-sm">{{ $sale->outlet->address ?? 'Alamat Outlet' }}</p>
        </div>

        <!-- Transaction Info -->
        <div class="p-6 border-b border-gray-100">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">No. Invoice</p>
                    <p class="font-mono font-medium text-gray-800">{{ $sale->invoice_number }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Tanggal</p>
                    <p class="font-medium text-gray-800">{{ $sale->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Kasir</p>
                    <p class="font-medium text-gray-800">{{ $sale->cashier->name ?? '-' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Pelanggan</p>
                    <p class="font-medium text-gray-800">{{ $sale->customer->name ?? 'Umum' }}</p>
                </div>
            </div>
        </div>

        <!-- Items -->
        <div class="p-6 bg-gray-50/50">
            <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-list-ul text-orange-500"></i> Detail Pesanan
            </h3>
            <div class="space-y-3">
                @foreach($sale->items as $item)
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">{{ $item->product->name ?? 'Item Terhapus' }}</p>
                        <p class="text-xs text-gray-500">{{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                    </div>
                    <p class="text-sm font-semibold text-gray-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Summary -->
        <div class="p-6 border-t border-gray-100">
            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($sale->discount_amount > 0)
                <div class="flex justify-between text-sm text-green-600">
                    <span>Diskon</span>
                    <span>- Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($sale->tax_amount > 0)
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Pajak ({{ $sale->tax_percent }}%)</span>
                    <span>Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}</span>
                </div>
                @endif
            </div>
            <div class="flex justify-between items-center pt-4 border-t border-dashed border-gray-200">
                <span class="font-bold text-gray-900">Total Bayar</span>
                <span class="text-xl font-bold text-orange-600">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</span>
            </div>
            
            <div class="mt-4 flex justify-between items-center text-sm bg-gray-50 p-3 rounded-lg">
                <span class="text-gray-600">Metode Pembayaran</span>
                <span class="font-semibold text-gray-900 uppercase flex items-center gap-2">
                    @if($sale->payment_method == 'cash')
                        <i class="fas fa-money-bill-wave text-green-500"></i>
                    @elseif($sale->payment_method == 'qris')
                        <i class="fas fa-qrcode text-purple-500"></i>
                    @else
                        <i class="fas fa-credit-card text-blue-500"></i>
                    @endif
                    {{ $sale->payment_method }}
                </span>
            </div>
        </div>

        <!-- Rating Section -->
        <div class="p-6 bg-gradient-to-b from-white to-orange-50 border-t border-gray-100 text-center">
            <h3 class="font-semibold text-gray-900 mb-2">Bagaimana pengalaman belanja Anda?</h3>
            <p class="text-xs text-gray-500 mb-4">Beri rating untuk layanan kami</p>
            
            <div class="star-rating mb-4">
                <input type="radio" id="star5" name="rating" value="5" /><label for="star5" title="Sempurna"><i class="fas fa-star"></i></label>
                <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="Sangat Baik"><i class="fas fa-star"></i></label>
                <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="Baik"><i class="fas fa-star"></i></label>
                <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="Cukup"><i class="fas fa-star"></i></label>
                <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="Buruk"><i class="fas fa-star"></i></label>
            </div>

            <div id="rating-feedback" class="hidden">
                <p class="text-sm text-orange-600 font-medium animate-bounce">Terima kasih atas penilaian Anda!</p>
            </div>
        </div>

        @auth
        <div class="p-4 flex gap-2 justify-center border-t border-gray-100">
            @can('cetak struk penjualan')
            <a href="{{ route('receipt.print', $sale->id) }}" target="_blank" class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-bold flex items-center gap-2">
                <i class="fas fa-print"></i> Cetak
            </a>
            @endcan
            @can('unduh struk penjualan')
            <a href="{{ route('receipt.download', $sale->id) }}" target="_blank" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-bold flex items-center gap-2">
                <i class="fas fa-download"></i> Unduh
            </a>
            @endcan
        </div>
        @endauth

        <!-- Footer -->
        <div class="bg-gray-50 p-4 text-center border-t border-gray-200">
            <p class="text-xs text-gray-400">Powered by CuanFlow</p>
        </div>
    </div>

    <script>
        document.querySelectorAll('input[name="rating"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const feedback = document.getElementById('rating-feedback');
                feedback.classList.remove('hidden');
                
                // Disable inputs after selection (optional, for visual effect)
                setTimeout(() => {
                    document.querySelectorAll('input[name="rating"]').forEach(r => r.disabled = true);
                }, 500);
            });
        });
    </script>
</body>
</html>

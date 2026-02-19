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
                <div class="flex justify-between items-start gap-4 p-3 bg-white rounded-xl border border-gray-100 shadow-sm">
                    <div class="flex-1">
                        <p class="text-sm font-bold text-gray-800">{{ $item->product->name ?? 'Item Terhapus' }}</p>
                        <p class="text-[11px] text-gray-500 font-medium">
                            {{ (float)$item->quantity }} {{ $item->product->unit->name ?? 'Unit' }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                        </p>
                        
                        {{-- Review Button --}}
                        @if($item->product_id && !$item->review)
                        <button type="button" 
                                onclick="openReviewModal('{{ $item->id }}', '{{ addslashes($item->product->name) }}', '{{ $item->product_id }}')"
                                class="mt-2 text-[10px] font-bold text-orange-600 hover:text-orange-700 flex items-center gap-1.5 transition-colors uppercase tracking-wider group">
                            <i class="fas fa-star group-hover:scale-110 transition-transform"></i>
                            Beri Ulasan
                        </button>
                        @elseif($item->review)
                        <div class="mt-2 flex items-center gap-1 text-[10px] text-emerald-600 font-bold uppercase tracking-wider">
                            <i class="fas fa-check-circle"></i>
                            Sudah Diulas ({{ $item->review->rating }} ★)
                        </div>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-extrabold text-gray-900 italic">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                    </div>
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
                
                @php
                    $notes = json_decode($sale->notes, true);
                    $typeInfo = $notes['customer_type_info'] ?? null;
                @endphp

                @if($typeInfo)
                    <div class="mt-2 p-2 bg-indigo-50 rounded-lg border border-indigo-100">
                        <div class="flex justify-between text-[11px] font-bold text-indigo-700 uppercase">
                            <span>PROMO {{ $typeInfo['label'] }}</span>
                            <span>- Rp {{ number_format($typeInfo['total_savings'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-[9px] text-indigo-500 mt-1 italic leading-tight">
                            * Produk sudah menggunakan harga khusus untuk kategori {{ $typeInfo['type'] }}.
                        </p>
                    </div>
                @endif

                @php
                    $notes = json_decode($sale->notes, true);
                    $plan = $notes['discount_plan'] ?? null;
                    $appliedDiscounts = $plan['applied_discounts'] ?? [];
                @endphp
                
                @if(!empty($appliedDiscounts) && is_array($appliedDiscounts))
                    @foreach($appliedDiscounts as $applied)
                        <div class="flex justify-between text-sm text-green-600">
                            <span>{{ $applied['name'] ?? 'Diskon' }}</span>
                            <span>- Rp {{ number_format((float)($applied['amount'] ?? 0), 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                @elseif($sale->discount_amount > 0)
                    <div class="flex justify-between text-sm text-green-600">
                        <span>Diskon</span>
                        <span>- Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif

                {{-- Gifts --}}
                @php
                    $freeItems = [];
                    if (!empty($appliedDiscounts)) {
                        foreach ($appliedDiscounts as $ad) {
                            if (($ad['type'] ?? '') === 'buy_x_get_y' && !empty($ad['free_items'])) {
                                $freeItems = array_merge($freeItems, $ad['free_items']);
                            }
                        }
                    }
                @endphp
                @if(!empty($freeItems))
                    <div class="mt-2 pt-2 border-t border-gray-100">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 text-center">Hadiah Gratis</p>
                        @foreach($freeItems as $fi)
                            <div class="flex justify-between text-xs text-emerald-600 italic">
                                <span>{{ $fi['product_name'] ?? 'Item' }}</span>
                                <span class="font-bold">x{{ $fi['free_qty'] ?? 1 }}</span>
                            </div>
                        @endforeach
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

        <!-- Product Review Modal -->
        <div id="productReviewModal" class="hidden fixed inset-0 bg-black/60 z-[100] flex items-center justify-center p-4 backdrop-blur-sm transition-all duration-300">
            <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full overflow-hidden transform transition-all scale-95 opacity-0 duration-300" id="modalContainer">
                <div class="bg-gradient-to-br from-orange-500 to-red-600 p-6 text-white text-center relative">
                    <button onclick="closeReviewModal()" class="absolute top-4 right-4 text-white/80 hover:text-white bg-white/10 p-2 rounded-full backdrop-blur-md transition-colors">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3 backdrop-blur-sm">
                        <i class="fas fa-star text-2xl animate-pulse"></i>
                    </div>
                    <h2 class="text-lg font-bold">Beri Ulasan</h2>
                    <p class="text-white/70 text-[11px] uppercase tracking-widest font-bold mt-1" id="reviewProductName"></p>
                </div>
                
                <form id="product-review-form" class="p-6">
                    @csrf
                    <input type="hidden" name="sale_item_id" id="modal_sale_item_id">
                    <input type="hidden" name="product_id" id="modal_product_id">
                    
                    <div class="mb-6">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest text-center mb-4">Rating Produk</p>
                        <div class="star-rating">
                            <input type="radio" id="p_star5" name="rating" value="5" /><label for="p_star5" title="Sempurna"><i class="fas fa-star"></i></label>
                            <input type="radio" id="p_star4" name="rating" value="4" /><label for="p_star4" title="Sangat Baik"><i class="fas fa-star"></i></label>
                            <input type="radio" id="p_star3" name="rating" value="3" /><label for="p_star3" title="Baik"><i class="fas fa-star"></i></label>
                            <input type="radio" id="p_star2" name="rating" value="2" /><label for="p_star2" title="Cukup"><i class="fas fa-star"></i></label>
                            <input type="radio" id="p_star1" name="rating" value="1" /><label for="p_star1" title="Buruk"><i class="fas fa-star"></i></label>
                        </div>
                    </div>

                    <div class="mb-6">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Kritik / Saran</p>
                        <textarea name="comment" rows="3" 
                                  class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-50 transition-all resize-none shadow-inner" 
                                  placeholder="Bagaimana kualitas produk ini?"></textarea>
                    </div>

                    <button type="submit" id="submitProductReview" class="w-full bg-gradient-to-r from-gray-900 to-gray-800 hover:from-black hover:to-gray-900 text-white font-extrabold py-4 rounded-2xl shadow-xl shadow-gray-200 transition-all active:scale-95 flex items-center justify-center gap-3">
                        <span>Kirim Ulasan</span>
                        <i class="fas fa-paper-plane text-xs"></i>
                    </button>
                    
                    <p class="mt-4 text-[9px] text-gray-400 text-center leading-relaxed italic">
                        Ulasan Anda membantu kami meningkatkan kualitas produk dan layanan yang kami berikan.
                    </p>
                </form>
            </div>
        </div>

        <!-- Receipt Testimonial -->
        <div class="p-6 bg-gradient-to-b from-white to-orange-50 border-t border-gray-100">
            <h3 class="font-semibold text-gray-900 mb-2 text-center">Bagaimana pengalaman belanja Anda?</h3>
            <p class="text-xs text-gray-500 mb-4 text-center">Beri rating dan ulasan untuk layanan kami</p>
            
            <form id="testimonial-form" action="{{ route('testimonials.store') }}" method="POST">
                @csrf
                <input type="hidden" name="outlet_id" value="{{ $sale->outlet_id }}">
                <input type="hidden" name="role" value="Pelanggan">
                
                <div class="star-rating mb-4 justify-center">
                    <input type="radio" id="star5" name="rating" value="5" /><label for="star5" title="Sempurna"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="Sangat Baik"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="Baik"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="Cukup"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="Buruk"><i class="fas fa-star"></i></label>
                </div>

                <div class="space-y-3 mb-4">
                    <div>
                        <input type="text" name="name" 
                               class="w-full px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all" 
                               placeholder="Nama Anda" 
                               value="{{ $sale->customer->name ?? '' }}" 
                               required>
                    </div>
                    <div>
                        <textarea name="content" rows="3" 
                                  class="w-full px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all resize-none" 
                                  placeholder="Ceritakan pengalaman Anda..." 
                                  required></textarea>
                    </div>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-orange-200 transition-all duration-200 flex items-center justify-center gap-2">
                    <span>Kirim Ulasan</span>
                    <i class="fas fa-paper-plane text-xs"></i>
                </button>
            </form>
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
        document.getElementById('testimonial-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
            // Basic validation
            const rating = form.querySelector('input[name="rating"]:checked');
            if (!rating) {
                alert('Mohon pilih rating bintang terlebih dahulu.');
                return;
            }

            // Disable button and show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Replace form with success message
                    const container = form.closest('div');
                    container.innerHTML = `
                        <div class="py-8 text-center animate-fade-in">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 text-green-500 shadow-sm">
                                <i class="fas fa-check text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Terima Kasih!</h3>
                            <p class="text-gray-600 text-sm mb-4">${data.message}</p>
                            <p class="text-xs text-gray-400">Masukan Anda sangat berarti bagi kemajuan kami.</p>
                        </div>
                    `;
                } else {
                    alert(data.message || 'Terjadi kesalahan. Mohon coba lagi.');
                    resetButton();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan koneksi.');
                resetButton();
            });

            function resetButton() {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
                submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        });

        function openReviewModal(itemId, productName, productId) {
            document.getElementById('modal_sale_item_id').value = itemId;
            document.getElementById('modal_product_id').value = productId;
            document.getElementById('reviewProductName').textContent = productName;
            
            const modal = document.getElementById('productReviewModal');
            const container = document.getElementById('modalContainer');
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                container.classList.remove('opacity-0', 'scale-95');
                container.classList.add('opacity-100', 'scale-100');
            }, 10);
        }

        function closeReviewModal() {
            const modal = document.getElementById('productReviewModal');
            const container = document.getElementById('modalContainer');
            
            container.classList.remove('opacity-100', 'scale-100');
            container.classList.add('opacity-0', 'scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                document.getElementById('product-review-form').reset();
            }, 300);
        }

        document.getElementById('product-review-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const submitBtn = document.getElementById('submitProductReview');
            const originalBtnContent = submitBtn.innerHTML;
            
            const rating = form.querySelector('input[name="rating"]:checked');
            if (!rating) {
                alert('Mohon pilih rating bintang untuk produk ini.');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> SEBENTAR...';
            
            const formData = new FormData(form);

            fetch('{{ route("product-reviews.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const container = document.getElementById('modalContainer');
                    container.innerHTML = `
                        <div class="p-10 text-center bg-white rounded-3xl">
                            <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-6 text-emerald-500 shadow-sm border border-emerald-100">
                                <i class="fas fa-check text-4xl"></i>
                            </div>
                            <h3 class="text-2xl font-black text-gray-900 mb-3">Selesai!</h3>
                            <p class="text-gray-500 text-sm font-medium mb-8">${data.message}</p>
                            <button onclick="window.location.reload()" class="w-full bg-gray-900 text-white font-bold py-4 rounded-2xl shadow-xl">
                                Tutup Berhasil
                            </button>
                        </div>
                    `;
                } else {
                    alert(data.message || 'Terjadi kesalahan.');
                    resetBtn();
                }
            })
            .catch(error => {
                alert('Kesalahan koneksi.');
                resetBtn();
            });

            function resetBtn() {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnContent;
            }
        });
    </script>
</body>
</html>

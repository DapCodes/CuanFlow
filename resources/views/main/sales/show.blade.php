@extends('layouts.app')

@section('title', 'Detail Penjualan ' . $sale->invoice_number)

@section('content')

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('sales.index') }}" class="text-gray-600 hover:text-indigo-600">Penjualan</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Detail Penjualan</span>
</li>
@endsection

<main class="flex-grow py-4 md:py-6 px-3 md:px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-4 md:space-y-6">

        <section
            class="bg-white border border-gray-200 rounded-xl shadow-sm px-5 md:px-6 py-4 md:py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-500 border border-red-100">
                        <i class="fas fa-receipt text-sm"></i>
                    </span>
                    <span>Detail Penjualan</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Rincian transaksi, pembayaran, dan item yang terjual.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[11px] text-gray-500">Invoice</span>
                <span class="text-xs font-mono bg-gray-100 text-gray-800 px-2.5 py-1 rounded-lg border border-gray-200">
                    {{ $sale->invoice_number }}
                </span>
            </div>
        </section>

        {{-- STATUS BAR (compact & jelas) --}}
        @php
            $isRefunded = ($sale->status ?? null) === 'refunded';
            $statusText = strtoupper($sale->status ?? '-');
            $payStatusText = strtoupper($sale->payment_status ?? '-');

            $statusClass = $isRefunded
                ? 'bg-red-50 text-red-700 border-red-200'
                : 'bg-green-50 text-green-700 border-green-200';

            $payStatusClass = in_array($sale->payment_status, ['paid'])
                ? 'bg-green-50 text-green-700 border-green-200'
                : (in_array($sale->payment_status, ['partial', 'pending'])
                    ? 'bg-yellow-50 text-yellow-700 border-yellow-200'
                    : 'bg-gray-50 text-gray-700 border-gray-200');
        @endphp

        <section class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                <p class="text-xs text-gray-500 font-medium">Status Transaksi</p>
                <span class="inline-flex mt-2 px-2.5 py-1 rounded-full text-xs font-bold border {{ $statusClass }}">
                    {{ $statusText }}
                </span>
                <p class="text-xs text-gray-500 mt-2">
                    {{ $isRefunded ? 'Transaksi ini sudah di-refund.' : 'Transaksi selesai.' }}
                </p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                <p class="text-xs text-gray-500 font-medium">Status Pembayaran</p>
                <span class="inline-flex mt-2 px-2.5 py-1 rounded-full text-xs font-bold border {{ $payStatusClass }}">
                    {{ $payStatusText }}
                </span>
                <p class="text-xs text-gray-500 mt-2">
                    Metode: <span class="font-semibold text-gray-800">{{ strtoupper($sale->payment_method) }}</span>
                </p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                <p class="text-xs text-gray-500 font-medium">Waktu Transaksi</p>
                <p class="mt-2 text-sm font-semibold text-gray-900">{{ $sale->created_at->format('d/m/Y H:i') }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    Kasir: <span class="font-semibold text-gray-800">{{ $sale->cashier->name ?? '-' }}</span>
                </p>
            </div>
        </section>

        {{-- GRID KONTEN UTAMA --}}
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            {{-- KIRI: Informasi umum + catatan --}}
            <div class="lg:col-span-1 space-y-4">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                    <h2 class="text-sm font-semibold text-gray-900 mb-3">Informasi</h2>

                    <dl class="text-sm space-y-2">
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-gray-500">Pelanggan</dt>
                            <dd class="font-semibold text-gray-900 truncate max-w-[60%]">
                                {{ $sale->customer->name ?? '-' }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-gray-500">Metode bayar</dt>
                            <dd class="font-semibold text-gray-900">{{ strtoupper($sale->payment_method) }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-gray-500">Subtotal</dt>
                            <dd class="font-semibold text-gray-900">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-gray-500">Diskon</dt>
                            <dd class="font-semibold text-orange-600">- Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-gray-500">Pajak (PPN)</dt>
                            <dd class="font-semibold text-gray-900">Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}</dd>
                        </div>

                        <div class="pt-2 mt-2 border-t border-gray-200 flex items-center justify-between gap-3">
                            <dt class="text-gray-700 font-semibold">Total</dt>
                            <dd class="text-base font-bold text-green-600">
                                Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                            </dd>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-gray-500">Bayar</dt>
                            <dd class="font-semibold text-gray-900">Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</dd>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-gray-500">Kembali</dt>
                            <dd class="font-semibold text-gray-900">Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                    <h2 class="text-sm font-semibold text-gray-900 mb-2">Catatan</h2>

                    @php
                        $rawNotes = $sale->notes ?? '';
                        $decoded = null;

                        if (is_string($rawNotes) && trim($rawNotes) !== '') {
                            $decoded = json_decode($rawNotes, true);
                            if (json_last_error() !== JSON_ERROR_NONE) {
                                $decoded = null;
                            }
                        }

                        // Ambil discount_plan kalau ada
                        $plan = is_array($decoded) ? ($decoded['discount_plan'] ?? null) : null;

                        $planName = $plan['discount_name'] ?? null;
                        $planType = $plan['discount_type'] ?? null;
                        $planTotal = $plan['total_discount'] ?? null;

                        $affected = $plan['affected_items'] ?? [];
                    @endphp

                    @if($plan)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 space-y-2">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">
                                        {{ $planName ?: 'Diskon' }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        Tipe: {{ $planType ? strtoupper($planType) : '-' }}
                                    </p>
                                </div>

                                <div class="text-right">
                                    <p class="text-xs text-gray-500">Total diskon</p>
                                    <p class="text-sm font-bold text-orange-600 whitespace-nowrap">
                                        Rp {{ number_format((float)($planTotal ?? 0), 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            @if(is_array($affected) && count($affected) > 0)
                                <div class="pt-2 border-t border-gray-200">
                                    <p class="text-xs font-semibold text-gray-700 mb-2">Item yang kena diskon</p>

                                    <div class="space-y-1">
                                        @foreach($affected as $ai)
                                            @php
                                                $productId = $ai['product_id'] ?? null;
                                                $discAmt  = (float)($ai['discount_amount'] ?? 0);

                                                // Cari nama produk dari items transaksi (kalau ketemu)
                                                $productName = null;
                                                if ($productId) {
                                                    $found = $sale->items->firstWhere('product_id', $productId);
                                                    $productName = $found->product_name ?? null;
                                                }
                                            @endphp

                                            <div class="flex items-center justify-between gap-3 text-sm">
                                                <div class="min-w-0">
                                                    <p class="text-gray-900 font-medium truncate">
                                                        {{ $productName ?: ('Produk ID: ' . ($productId ?? '-')) }}
                                                    </p>
                                                </div>
                                                <div class="font-semibold text-orange-600 whitespace-nowrap">
                                                    - Rp {{ number_format($discAmt, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if(($plan['requires_free_item_selection'] ?? false) || (($plan['free_item_quota'] ?? 0) > 0))
                                <div class="pt-2 border-t border-gray-200 text-xs text-gray-600">
                                    Bonus item: {{ (int)($plan['free_item_quota'] ?? 0) }} item
                                    @if(!empty($plan['free_item_candidates']) && is_array($plan['free_item_candidates']))
                                        (tersedia: {{ count($plan['free_item_candidates']) }} kandidat)
                                    @endif
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-sm text-gray-600">
                            {{ $rawNotes ?: 'Tidak ada catatan.' }}
                        </p>
                    @endif

                    @if(!empty($sale->customer_notes))
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <p class="text-xs text-gray-500 font-medium mb-1">Catatan pelanggan</p>
                            <p class="text-sm text-gray-600">{{ $sale->customer_notes }}</p>
                        </div>
                    @endif
                </div>

            </div>

            {{-- KANAN: Pembayaran + Item --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- Pembayaran --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <h2 class="text-sm font-semibold text-gray-900">Pembayaran</h2>
                        <span class="text-xs text-gray-500">
                            {{ $sale->payments->count() }} metode
                        </span>
                    </div>

                    @forelse($sale->payments as $pay)
                        <div class="py-2 border-b border-gray-100 last:border-b-0">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ strtoupper($pay->payment_method) }}
                                    </p>
                                    @if($pay->reference_number)
                                        <p class="text-xs text-gray-500 truncate">Ref: {{ $pay->reference_number }}</p>
                                    @endif
                                </div>
                                <div class="text-sm font-bold text-gray-900 whitespace-nowrap">
                                    Rp {{ number_format($pay->amount, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada data pembayaran.</p>
                    @endforelse
                </div>

                {{-- Item Penjualan --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-900">Item Penjualan</h2>
                            <p class="text-sm text-gray-500">Rincian barang yang terjual pada transaksi ini.</p>
                        </div>
                        <div class="text-xs text-gray-500">
                            Total item: <span class="font-semibold text-gray-900">{{ $sale->items->count() }}</span>
                        </div>
                    </div>

                    <div class="overflow-x-auto -mx-4 px-4">
                        <table class="w-full min-w-[860px] text-sm">
                            <thead class="bg-gray-50 text-[11px] uppercase text-gray-500">
                                <tr>
                                    <th class="px-3 py-2 text-left">Produk</th>
                                    <th class="px-3 py-2 text-right">Qty</th>
                                    <th class="px-3 py-2 text-right">Harga</th>
                                    <th class="px-3 py-2 text-right">Subtotal</th>
                                    <th class="px-3 py-2 text-right">HPP</th>
                                    <th class="px-3 py-2 text-right">Profit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($sale->items as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2">
                                            <div class="font-semibold text-gray-900 cursor-pointer hover:text-indigo-600 transition" 
                                                 onclick="showProductDetail({{ json_encode([
                                                     'name' => $item->product_name,
                                                     'price' => number_format($item->unit_price, 0, ',', '.'),
                                                     'description' => $item->product->description ?? 'Tidak ada deskripsi produk.',
                                                     'image' => $item->product->image ?? null ? Storage::url($item->product->image) : null,
                                                     'category' => $item->product->category->name ?? 'Umum',
                                                     'unit' => $item->product->unit->name ?? 'pcs'
                                                 ]) }})">
                                                {{ $item->product_name }}
                                                <i class="fas fa-external-link-alt text-[10px] ml-1 opacity-50"></i>
                                            </div>
                                            @if(isset($item->product->unit->name))
                                                <div class="text-xs text-gray-500">{{ $item->product->unit->name }}</div>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 text-right whitespace-nowrap">
                                            {{ number_format($item->quantity, 0, ',', '.') }}
                                            {{ $item->product->unit->name ?? '' }}
                                        </td>
                                        <td class="px-3 py-2 text-right whitespace-nowrap">
                                            Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-3 py-2 text-right whitespace-nowrap font-semibold text-gray-900">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </td>
                                        <td class="px-3 py-2 text-right whitespace-nowrap text-gray-700">
                                            Rp {{ number_format($item->hpp, 0, ',', '.') }}
                                        </td>
                                        <td class="px-3 py-2 text-right whitespace-nowrap font-semibold
                                            {{ ($item->profit ?? 0) < 0 ? 'text-red-600' : 'text-green-600' }}">
                                            Rp {{ number_format($item->profit, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Footer mini total --}}
                    <div class="mt-3 pt-3 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-center sm:text-left">
                        <p class="text-sm text-gray-600">
                            Total transaksi:
                            <span class="font-bold text-gray-900">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</span>
                        </p>

                        <div class="flex flex-wrap justify-center sm:justify-end gap-2">
                            @if($sale->status === 'completed')
                                @can('unduh struk penjualan')
                                <a href="{{ route('receipt.download', $sale->id) }}" target="_blank"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700 transition-all shadow-sm">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>Export PDF</span>
                                </a>
                                @endcan

                                @can('cetak struk penjualan')
                                <a href="{{ route('sales.print', $sale->id) }}" target="_blank"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg text-sm font-semibold hover:from-orange-600 hover:to-orange-700 transition-all shadow-md">
                                    <i class="fas fa-print"></i>
                                    <span>Cetak Struk</span>
                                </a>
                                @endcan
                            @endif

                            <a href="{{ url()->previous() }}"
                               class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </section>

    </div>
</main>
@endsection

@push('scripts')
<script>
    function showProductDetail(product) {
        let imageHtml = '';
        if (product.image) {
            imageHtml = `<img src="${product.image}" class="w-full h-48 object-cover rounded-xl mb-4 shadow-sm">`;
        } else {
            imageHtml = `<div class="w-full h-48 bg-gray-100 flex items-center justify-center rounded-xl mb-4 text-gray-300">
                            <i class="fas fa-box text-5xl"></i>
                         </div>`;
        }

        Swal.fire({
            title: `<div class="text-left"><span class="text-xs font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2 py-1 rounded-md mb-2 inline-block">${product.category}</span><br>${product.name}</div>`,
            html: `
                <div class="text-left">
                    ${imageHtml}
                    <div class="mb-4">
                        <p class="text-2xl font-black text-gray-900">
                            <span class="text-sm">Rp</span> ${product.price}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">Satuan: <b>${product.unit}</b></p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Deskripsi</p>
                        <p class="text-sm text-gray-600 leading-relaxed">${product.description}</p>
                    </div>
                </div>
            `,
            showConfirmButton: false,
            showCloseButton: true,
            width: '400px',
            padding: '1.5rem',
            customClass: {
                popup: 'rounded-3xl',
                title: 'text-left p-0 mb-4',
                htmlContainer: 'p-0 m-0'
            }
        });
    }
</script>
@endpush

@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Detail Penjualan ' . $sale->invoice_number . ' - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('sales.index') }}" class="text-gray-500 hover:text-cuan-green transition-colors">Penjualan</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Detail Penjualan</span>
</li>
@endsection

@section('content')

@php
    $isRefunded = ($sale->status ?? null) === 'refunded';
    $statusText = $isRefunded ? 'Refund' : ucfirst($sale->status ?? '-');
    $payStatusText = strtoupper($sale->payment_status ?? '-');

    $statusBg = $isRefunded
        ? 'bg-red-50 text-red-500 border-red-100'
        : 'bg-cuan-green/10 text-cuan-green border-cuan-green/10';

    $payStatusBg = in_array($sale->payment_status, ['paid'])
        ? 'bg-cuan-green/10 text-cuan-green border-cuan-green/10'
        : (in_array($sale->payment_status, ['partial','pending'])
            ? 'bg-yellow-50 text-yellow-600 border-yellow-100'
            : 'bg-gray-50 text-gray-400 border-gray-200');
@endphp

<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-xl md:text-2xl font-black text-gray-900">
                        Detail Penjualan
                    </h1>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $statusBg }}">
                        {{ $statusText }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    Rincian transaksi, pembayaran, dan item yang terjual.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Invoice</p>
                    <p class="font-mono font-black text-gray-900 bg-gray-100 px-2.5 py-1 rounded-lg mt-1 text-sm">
                        {{ $sale->invoice_number }}
                    </p>
                </div>
                <a href="{{ url()->previous() }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all active:scale-95">
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </section>

        {{-- STATUS CARDS --}}
        <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Status Transaksi</p>
                <span class="inline-flex items-center mt-3 px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $statusBg }}">
                    {{ $statusText }}
                </span>
                <p class="text-xs text-gray-400 mt-2 font-bold">
                    {{ $isRefunded ? 'Transaksi ini sudah di-refund.' : 'Transaksi selesai.' }}
                </p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Status Pembayaran</p>
                <span class="inline-flex items-center mt-3 px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $payStatusBg }}">
                    {{ $payStatusText }}
                </span>
                <p class="text-xs text-gray-400 mt-2 font-bold">
                    Metode: <span class="font-black text-gray-700">{{ strtoupper($sale->payment_method) }}</span>
                </p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Waktu Transaksi</p>
                <p class="mt-3 text-lg font-black text-gray-900">{{ $sale->created_at->format('H:i') }}</p>
                <p class="text-xs text-gray-400 mt-1 font-bold">
                    {{ $sale->created_at->format('d M Y') }} &middot;
                    Kasir: <span class="font-black text-gray-700">{{ $sale->cashier->name ?? '-' }}</span>
                </p>
            </div>
        </section>

        {{-- KONTEN UTAMA: kiri info, kanan item --}}
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- KIRI: Informasi + Catatan Diskon --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- Informasi Keuangan --}}
                <x-card-container>
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Informasi</h2>
                    </div>
                    <div class="px-6 py-6">
                        <dl class="space-y-3 text-sm">
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-[10px] font-black uppercase tracking-widest text-gray-400">Pelanggan</dt>
                                <dd class="font-bold text-gray-900 truncate max-w-[60%]">
                                    {{ $sale->customer_name ?? ($sale->customer?->name ?? 'Umum') }}
                                </dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-[10px] font-black uppercase tracking-widest text-gray-400">Metode Bayar</dt>
                                <dd class="font-bold text-gray-900">{{ strtoupper($sale->payment_method) }}</dd>
                            </div>

                            <div class="border-t border-gray-100 pt-3 space-y-3">
                                <div class="flex items-center justify-between gap-3">
                                    <dt class="text-[10px] font-black uppercase tracking-widest text-gray-400">Subtotal</dt>
                                    <dd class="font-bold text-gray-900">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</dd>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <dt class="text-[10px] font-black uppercase tracking-widest text-gray-400">Diskon</dt>
                                    <dd class="font-bold text-orange-500">- Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}</dd>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <dt class="text-[10px] font-black uppercase tracking-widest text-gray-400">Pajak (PPN)</dt>
                                    <dd class="font-bold text-gray-900">Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}</dd>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 pt-3">
                                <div class="flex items-center justify-between gap-3">
                                    <dt class="text-[10px] font-black uppercase tracking-widest text-gray-700">Total</dt>
                                    <dd class="text-lg font-black text-cuan-green">
                                        Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                                    </dd>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="flex items-center justify-between gap-3">
                                    <dt class="text-[10px] font-black uppercase tracking-widest text-gray-400">Bayar</dt>
                                    <dd class="font-bold text-gray-900">Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</dd>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <dt class="text-[10px] font-black uppercase tracking-widest text-gray-400">Kembalian</dt>
                                    <dd class="font-bold text-gray-900">Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</dd>
                                </div>
                            </div>
                        </dl>
                    </div>
                </x-card-container>

                {{-- Catatan & Diskon --}}
                <x-card-container>
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Catatan & Diskon</h2>
                    </div>
                    <div class="px-6 py-6">
                        @php
                            $rawNotes = $sale->notes ?? '';
                            $decoded = null;
                            if (is_string($rawNotes) && trim($rawNotes) !== '') {
                                $decoded = json_decode($rawNotes, true);
                                if (json_last_error() !== JSON_ERROR_NONE) $decoded = null;
                            }
                            $plan = is_array($decoded) ? ($decoded['discount_plan'] ?? null) : null;
                            $planName = $plan['discount_name'] ?? null;
                            $planType = $plan['discount_type'] ?? null;
                            $planTotal = $plan['total_discount'] ?? null;
                            $affected = $plan['affected_items'] ?? [];
                            $typeInfo = is_array($decoded) ? ($decoded['customer_type_info'] ?? null) : null;
                        @endphp

                        @if($typeInfo)
                            <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-4 mb-4">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="px-2.5 py-1 rounded-lg bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest">
                                        {{ $typeInfo['label'] }}
                                    </span>
                                    <span class="text-xs font-black text-indigo-700">
                                        Hemat: Rp {{ number_format($typeInfo['total_savings'] ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="space-y-2">
                                    @foreach($typeInfo['adjustments'] ?? [] as $adj)
                                        <div class="text-xs flex justify-between items-center text-indigo-900">
                                            <div class="truncate mr-3">
                                                <span class="font-black">{{ $adj['qty'] }}x</span> {{ $adj['product_name'] }}
                                            </div>
                                            <div class="text-right whitespace-nowrap flex items-center gap-1">
                                                <span class="text-gray-400 line-through text-[10px]">Rp {{ number_format($adj['original_price'], 0, ',', '.') }}</span>
                                                <i class="fas fa-arrow-right text-[8px] text-gray-300"></i>
                                                <span class="font-black">Rp {{ number_format($adj['applied_price'], 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($plan)
                            <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4 space-y-3">
                                @php $appliedDiscounts = $plan['applied_discounts'] ?? []; @endphp

                                @if(!empty($appliedDiscounts) && is_array($appliedDiscounts))
                                    <div class="space-y-3">
                                        @foreach($appliedDiscounts as $applied)
                                            <div class="flex items-start justify-between gap-3 pb-3 border-b border-gray-100 last:border-0 last:pb-0">
                                                <div class="min-w-0">
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-widest bg-indigo-100 text-indigo-700">
                                                            {{ strtoupper($applied['type'] ?? 'DISCOUNT') }}
                                                        </span>
                                                        <p class="text-sm font-black text-gray-900 truncate">
                                                            {{ $applied['name'] ?? 'Diskon' }}
                                                        </p>
                                                    </div>
                                                    @if(isset($applied['value']) && $applied['value'] > 0)
                                                        <p class="text-[10px] text-gray-400 font-bold mt-1">
                                                            Nilai: {{ $applied['type'] === 'percentage' ? $applied['value'].'%' : 'Rp '.number_format($applied['value'], 0, ',', '.') }}
                                                        </p>
                                                    @endif
                                                </div>
                                                <p class="text-sm font-black text-orange-500 whitespace-nowrap">
                                                    - Rp {{ number_format((float)($applied['amount'] ?? 0), 0, ',', '.') }}
                                                </p>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="text-sm font-black text-gray-900 truncate">{{ $planName ?: 'Diskon' }}</p>
                                            <p class="text-[10px] font-bold text-gray-400 mt-0.5">Tipe: {{ $planType ? strtoupper($planType) : '-' }}</p>
                                        </div>
                                        <p class="text-sm font-black text-orange-500 whitespace-nowrap">
                                            Rp {{ number_format((float)($planTotal ?? 0), 0, ',', '.') }}
                                        </p>
                                    </div>
                                @endif

                                @if(is_array($affected) && count($affected) > 0)
                                    <div class="pt-3 border-t border-gray-100">
                                        <button type="button"
                                                onclick="this.nextElementSibling.classList.toggle('hidden')"
                                                class="w-full flex items-center justify-between text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-cuan-green transition-colors">
                                            <span>Rincian per produk</span>
                                            <i class="fas fa-chevron-down text-xs"></i>
                                        </button>
                                        <div class="mt-2 space-y-1 hidden">
                                            @foreach($affected as $ai)
                                                @php
                                                    $productId = $ai['product_id'] ?? null;
                                                    $discAmt = (float)($ai['discount_amount'] ?? 0);
                                                    $productName = null;
                                                    if ($productId) {
                                                        $found = $sale->items->firstWhere('product_id', $productId);
                                                        $productName = $found->product_name ?? null;
                                                    }
                                                @endphp
                                                <div class="flex items-center justify-between gap-3 text-xs py-1">
                                                    <span class="text-gray-500 truncate">{{ $productName ?: 'Produk ID: '.$productId }}</span>
                                                    <span class="font-black text-orange-500 whitespace-nowrap">- Rp {{ number_format($discAmt, 0, ',', '.') }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if(($plan['requires_free_item_selection'] ?? false) || (($plan['free_item_quota'] ?? 0) > 0))
                                    <div class="pt-3 border-t border-gray-100">
                                        <div class="flex items-center justify-between bg-cuan-green/5 border border-cuan-green/20 text-cuan-green px-4 py-3 rounded-xl">
                                            <span class="text-[10px] font-black uppercase tracking-widest">Hadiah Gratis</span>
                                            <span class="text-sm font-black">{{ (int)($plan['free_item_quota'] ?? 0) }} Item</span>
                                        </div>
                                        @php
                                            $freeItems = [];
                                            foreach ($appliedDiscounts as $ad) {
                                                if (($ad['type'] ?? '') === 'buy_x_get_y' && !empty($ad['free_items'])) {
                                                    $freeItems = array_merge($freeItems, $ad['free_items']);
                                                }
                                            }
                                        @endphp
                                        @if(!empty($freeItems))
                                            <div class="mt-2 space-y-1">
                                                @foreach($freeItems as $fi)
                                                    <div class="flex items-center justify-between text-xs text-cuan-green px-1">
                                                        <span>{{ $fi['product_name'] ?? 'Item' }}</span>
                                                        <span class="font-black">x{{ $fi['free_qty'] ?? 1 }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @else
                            <p class="text-sm text-gray-500 font-medium">
                                {{ $rawNotes ?: 'Tidak ada catatan.' }}
                            </p>
                        @endif

                        @if(!empty($sale->customer_notes))
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Catatan Pelanggan</p>
                                <p class="text-sm text-gray-600">{{ $sale->customer_notes }}</p>
                            </div>
                        @endif
                    </div>
                </x-card-container>

            </div>

            {{-- KANAN: Pembayaran + Item --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Pembayaran --}}
                <x-card-container>
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Pembayaran</h2>
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">
                            {{ $sale->payments->count() }} metode
                        </span>
                    </div>
                    <div class="px-6 py-2 divide-y divide-gray-100">
                        @forelse($sale->payments as $pay)
                            <div class="py-4 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-black text-gray-900">{{ strtoupper($pay->payment_method) }}</p>
                                    @if($pay->reference_number)
                                        <p class="text-[10px] font-bold text-gray-400 mt-0.5 truncate">Ref: {{ $pay->reference_number }}</p>
                                    @endif
                                </div>
                                <p class="font-black text-gray-900 whitespace-nowrap">
                                    Rp {{ number_format($pay->amount, 0, ',', '.') }}
                                </p>
                            </div>
                        @empty
                            <p class="py-4 text-sm text-gray-400">Belum ada data pembayaran.</p>
                        @endforelse
                    </div>
                </x-card-container>

                {{-- Item Penjualan --}}
                <x-card-container>
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Item Penjualan</h2>
                            <p class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-widest">
                                Rincian barang yang terjual pada transaksi ini.
                            </p>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">
                            Total: <span class="text-gray-900">{{ $sale->items->count() }}</span> item
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm min-w-[680px]">
                            <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4 text-left">Produk</th>
                                    <th class="px-6 py-4 text-right">Qty</th>
                                    <th class="px-6 py-4 text-right">Harga</th>
                                    <th class="px-6 py-4 text-right">Subtotal</th>
                                    <th class="px-6 py-4 text-right">HPP</th>
                                    <th class="px-6 py-4 text-right">Profit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach($sale->items as $item)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900 cursor-pointer hover:text-cuan-green transition-colors"
                                                 onclick="showProductDetail({{ json_encode([
                                                     'name' => $item->product_name,
                                                     'price' => number_format($item->unit_price, 0, ',', '.'),
                                                     'description' => $item->product->description ?? 'Tidak ada deskripsi produk.',
                                                     'image' => $item->product->image ? Storage::url($item->product->image) : null,
                                                     'category' => $item->product->category->name ?? 'Umum',
                                                     'unit' => $item->product->unit->name ?? 'pcs'
                                                 ]) }})">
                                                {{ $item->product_name }}
                                                <i class="fas fa-external-link-alt text-[10px] ml-1 opacity-40"></i>
                                            </div>
                                            @if(isset($item->product->unit->name))
                                                <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-0.5">
                                                    {{ $item->product->unit->name }}
                                                </div>
                                            @endif
                                            @if($item->notes)
                                                <div class="mt-2 p-3 bg-amber-50 border border-amber-100 rounded-xl text-[10px] text-gray-600 leading-relaxed italic whitespace-pre-line">
                                                    <div class="flex items-start gap-2">
                                                        <i class="fas fa-sticky-note text-amber-400 mt-1"></i>
                                                        <span>{{ $item->notes }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right font-bold text-gray-900 whitespace-nowrap">
                                            {{ number_format($item->quantity, 0, ',', '.') }}
                                            <span class="text-gray-400">{{ $item->product->unit->name ?? '' }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-right text-gray-600 whitespace-nowrap">
                                            Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-right font-black text-gray-900 whitespace-nowrap">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-gray-500 whitespace-nowrap">
                                            Rp {{ number_format($item->hpp, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-right font-black whitespace-nowrap {{ ($item->profit ?? 0) < 0 ? 'text-red-500' : 'text-cuan-green' }}">
                                            Rp {{ number_format($item->profit, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Footer: total + actions --}}
                    <div class="px-6 py-5 border-t border-gray-100 bg-gray-50/50">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Transaksi</p>
                                <p class="text-xl font-black text-cuan-green mt-1">
                                    Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @if($sale->status === 'completed')
                                    @can('unduh struk penjualan')
                                    <a href="{{ route('receipt.download', $sale->id) }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-2 rounded-xl bg-red-50 text-red-600 border border-red-100 px-4 py-2.5 text-sm font-black hover:bg-red-500 hover:text-white transition-all active:scale-95">
                                        <i class="fas fa-file-pdf text-xs"></i>
                                        <span>Export PDF</span>
                                    </a>
                                    @endcan

                                    @can('cetak struk penjualan')
                                    <a href="{{ route('receipt.print', $sale->id) }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-2 rounded-xl bg-orange-50 text-orange-600 border border-orange-100 px-4 py-2.5 text-sm font-black hover:bg-orange-500 hover:text-white transition-all active:scale-95">
                                        <i class="fas fa-print text-xs"></i>
                                        <span>Cetak Struk</span>
                                    </a>
                                    @endcan

                                    @can('preview struk')
                                    <a href="{{ route('receipt.preview', $sale->id) }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-2 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 px-4 py-2.5 text-sm font-black hover:bg-blue-500 hover:text-white transition-all active:scale-95">
                                        <i class="fas fa-eye text-xs"></i>
                                        <span>Preview Struk</span>
                                    </a>
                                    @endcan

                                    @can('lihat struk publik')
                                    <a href="{{ route('receipts.show', $sale->invoice_number) }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-2 rounded-xl bg-cuan-green/10 text-cuan-green border border-cuan-green/20 px-4 py-2.5 text-sm font-black hover:bg-cuan-green hover:text-white transition-all active:scale-95">
                                        <i class="fas fa-share-alt text-xs"></i>
                                        <span>Struk Publik</span>
                                    </a>
                                    @endcan
                                @endif

                                <a href="{{ url()->previous() }}"
                                   class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all active:scale-95">
                                    <i class="fas fa-arrow-left text-xs"></i>
                                    <span>Kembali</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </x-card-container>
            </div>
        </section>

    </div>
</main>
@endsection

@push('scripts')
<script>
    function showProductDetail(product) {
        let imageHtml = product.image
            ? `<img src="${product.image}" class="w-full h-48 object-cover rounded-2xl mb-4 shadow-sm">`
            : `<div class="w-full h-32 bg-gray-100 flex items-center justify-center rounded-2xl mb-4 text-gray-300">
                   <i class="fas fa-box text-5xl"></i>
               </div>`;

        Swal.fire({
            title: `<div class="text-left">
                        <span class="text-[10px] font-black uppercase tracking-widest text-cuan-green bg-cuan-green/10 px-2.5 py-1 rounded-lg mb-2 inline-block">${product.category}</span>
                        <br>
                        <span class="text-lg font-black text-gray-900">${product.name}</span>
                    </div>`,
            html: `
                <div class="text-left">
                    ${imageHtml}
                    <div class="mb-4">
                        <p class="text-2xl font-black text-gray-900">
                            <span class="text-sm font-bold text-gray-400">Rp</span> ${product.price}
                        </p>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">
                            Satuan: <span class="text-gray-700">${product.unit}</span>
                        </p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Deskripsi</p>
                        <p class="text-sm text-gray-600 leading-relaxed">${product.description}</p>
                    </div>
                </div>
            `,
            showConfirmButton: false,
            showCloseButton: true,
            width: '400px',
            padding: '1.5rem',
            customClass: {
                popup: 'rounded-[2rem] border-none shadow-2xl',
                title: 'text-left p-0 mb-4',
                htmlContainer: 'p-0 m-0'
            }
        });
    }

    @if(session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 3000,
                iconColor: '#658C58',
                customClass: {
                    popup: 'rounded-3xl border-none shadow-2xl',
                    title: 'font-black text-gray-900',
                }
            });
        });
    @endif

    @if(session('error'))
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: "{{ session('error') }}",
                confirmButtonColor: '#ef4444',
                customClass: {
                    popup: 'rounded-3xl border-none shadow-2xl',
                    title: 'font-black text-gray-900',
                }
            });
        });
    @endif
</script>
@endpush

@extends('layouts.app')

@section('title', 'Detail Penjualan ' . $sale->invoice_number)

@section('content')
<main class="flex-grow py-4 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold text-gray-900">Detail Penjualan</h1>
            <span class="text-xs font-mono bg-slate-100 px-2 py-1 rounded">
                {{ $sale->invoice_number }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="bg-white rounded-2xl shadow-sm p-4">
                <h2 class="text-sm font-semibold text-gray-700 mb-2">Informasi</h2>
                <dl class="text-xs text-gray-600 space-y-1">
                    <div class="flex justify-between">
                        <dt>Tanggal</dt>
                        <dd>{{ $sale->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>Pelanggan</dt>
                        <dd>{{ $sale->customer->name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>Kasir</dt>
                        <dd>{{ $sale->cashier->name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>Metode Bayar</dt>
                        <dd>{{ ucfirst($sale->payment_method) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>Status Pembayaran</dt>
                        <dd>{{ ucfirst($sale->payment_status) }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-4">
                <h2 class="text-sm font-semibold text-gray-700 mb-2">Ringkasan</h2>
                <dl class="text-xs text-gray-600 space-y-1">
                    <div class="flex justify-between">
                        <dt>Subtotal</dt>
                        <dd>Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>Diskon</dt>
                        <dd>- Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>PPN</dt>
                        <dd>Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between text-sm font-bold pt-1 border-t border-slate-200 mt-1">
                        <dt>Total</dt>
                        <dd>Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>Bayar</dt>
                        <dd>Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>Kembali</dt>
                        <dd>Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-4">
                <h2 class="text-sm font-semibold text-gray-700 mb-2">Pembayaran</h2>
                @forelse($sale->payments as $pay)
                    <div class="mb-2 text-xs text-gray-600 border-b border-slate-100 pb-1">
                        <div class="flex justify-between">
                            <span>{{ ucfirst($pay->payment_method) }}</span>
                            <span>Rp {{ number_format($pay->amount, 0, ',', '.') }}</span>
                        </div>
                        @if($pay->reference_number)
                            <div class="text-[11px] text-gray-400">
                                Ref: {{ $pay->reference_number }}
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-xs text-gray-400">Belum ada data pembayaran.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-4">
            <h2 class="text-sm font-semibold text-gray-700 mb-2">Item Penjualan</h2>
            <table class="min-w-full text-xs">
                <thead class="bg-slate-50 text-[11px] uppercase text-gray-500">
                    <tr>
                        <th class="px-3 py-1 text-left">Produk</th>
                        <th class="px-3 py-1 text-right">Qty</th>
                        <th class="px-3 py-1 text-right">Harga</th>
                        <th class="px-3 py-1 text-right">Sub</th>
                        <th class="px-3 py-1 text-right">HPP</th>
                        <th class="px-3 py-1 text-right">Profit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($sale->items as $item)
                        <tr>
                            <td class="px-3 py-1">
                                <div class="font-semibold text-gray-800">{{ $item->product_name }}</div>
                            </td>
                            <td class="px-3 py-1 text-right">{{ number_format($item->quantity, 0, ',', '.') }} {{$item->product->unit->name}}</td>
                            <td class="px-3 py-1 text-right">
                                Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-1 text-right">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-1 text-right">
                                Rp {{ number_format($item->hpp, 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-1 text-right">
                                Rp {{ number_format($item->profit, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</main>
@endsection

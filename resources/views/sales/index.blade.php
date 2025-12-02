@extends('layouts.app')

@section('title', 'Riwayat Penjualan')

@section('content')
<main class="flex-grow py-4 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">Riwayat Penjualan</h1>

        <div class="bg-white shadow-sm rounded-2xl overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-2 text-left">Invoice</th>
                        <th class="px-4 py-2 text-left">Tanggal</th>
                        <th class="px-4 py-2 text-left">Pelanggan</th>
                        <th class="px-4 py-2 text-right">Total</th>
                        <th class="px-4 py-2 text-center">Status</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sales as $sale)
                        <tr>
                            <td class="px-4 py-2 text-sm font-mono">{{ $sale->invoice_number }}</td>
                            <td class="px-4 py-2 text-sm">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-2 text-sm">{{ $sale->customer->name ?? '-' }}</td>
                            <td class="px-4 py-2 text-sm text-right">
                                Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-2 text-xs text-center">
                                <span class="inline-flex px-2 py-0.5 rounded-full
                                    @if($sale->status === 'completed') bg-emerald-50 text-emerald-700
                                    @elseif($sale->status === 'cancelled') bg-red-50 text-red-700
                                    @else bg-slate-50 text-slate-600 @endif">
                                    {{ ucfirst($sale->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-right text-xs">
                                <a href="{{ route('sales.show', $sale) }}"
                                   class="text-indigo-600 hover:text-indigo-800 font-semibold">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center text-sm text-gray-500">
                                Belum ada penjualan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $sales->links() }}
        </div>
    </div>
</main>
@endsection

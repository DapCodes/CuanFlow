@extends('admin.layouts.app')

@section('title', 'Detail Pembayaran')
@section('page-title', 'Detail Transaksi: ' . $payment->transaction_id)

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <a href="{{ route('admin.subscription-payments.index') }}" class="hover:text-emerald-600 transition-colors text-sm">Transaksi</a>
</li>
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Detail</span>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Payment Info Card -->
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="px-8 py-6 bg-gray-50/50 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Informasi Pembayaran</h2>
                <p class="text-sm text-gray-400 font-mono mt-1">#{{ $payment->transaction_id }}</p>
            </div>
            <span class="px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider border {{ $payment->status_badge }}">
                {{ $payment->status }}
            </span>
        </div>
        
        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-8 gap-x-12">
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-2">Pelanggan</label>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 font-bold">
                            {{ substr($payment->user->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">{{ $payment->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $payment->user->email }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-2">Produk / Tier</label>
                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <p class="text-sm font-bold text-gray-900">{{ $payment->tier->display_name }}</p>
                        <p class="text-xs text-gray-500">{{ $payment->plan->duration_months }} Bulan Masa Aktif</p>
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Nominal Pembayaran</label>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                </div>

                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Metode & Waktu</label>
                    <p class="text-sm font-bold text-gray-700">{{ $payment->payment_method ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-400">{{ $payment->paid_at?->format('d F Y, H:i') ?? 'Belum Lunas' }}</p>
                </div>
            </div>

            <!-- JSON Response -->
            @if($payment->payment_response)
            <div class="mt-10 pt-8 border-t border-gray-100">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-3">Midtrans Response / Admin Note</label>
                <div class="bg-gray-900 rounded-xl p-5 overflow-x-auto">
                    <pre class="text-emerald-400 text-[11px] font-mono leading-relaxed">{{ json_encode($payment->payment_response, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
            @endif
        </div>

        @if($payment->isPending())
        <div class="px-8 py-6 bg-amber-50 border-t border-amber-100 flex items-center justify-between">
            <div class="flex items-center gap-3 text-amber-600">
                <i class="fas fa-triangle-exclamation"></i>
                <p class="text-xs font-semibold">Transaksi ini masih pending. Setujui secara manual jika dana sudah masuk.</p>
            </div>
            <form action="{{ route('admin.subscription-payments.approve', $payment) }}" method="POST">
                @csrf
                <button type="submit" class="px-6 py-2.5 bg-gray-900 text-white text-xs font-bold rounded-xl hover:bg-emerald-600 transition-all shadow-lg">
                    Approve Manual
                </button>
            </form>
        </div>
        @endif
    </div>

    <div class="flex justify-center">
        <a href="{{ route('admin.subscription-payments.index') }}" class="text-sm font-bold text-gray-400 hover:text-gray-900 transition-colors flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            Kembali ke Daftar Transaksi
        </a>
    </div>
</div>
@endsection

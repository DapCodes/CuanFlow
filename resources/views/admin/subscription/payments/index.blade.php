@extends('admin.layouts.app')

@section('title', 'Transaksi Pembayaran')
@section('page-title', 'Riwayat Pembayaran Langganan')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Transaksi</span>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 shadow-sm shadow-indigo-100/50">
                <i class="fas fa-receipt text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Riwayat Transaksi</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium">Monitoring arus pendapatan dan status pembayaran Midtrans</p>
            </div>
        </div>
        
        <!-- Filter Tabs -->
        <div class="bg-white rounded-xl p-1 flex space-x-1 border border-gray-200">
            <a href="{{ route('admin.subscription-payments.index') }}" 
               class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition {{ !$status ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-500 hover:bg-gray-50' }}">
               Semua
            </a>
            <a href="{{ route('admin.subscription-payments.index', ['status' => 'success']) }}" 
               class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition {{ $status == 'success' ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-500 hover:bg-gray-50' }}">
               Berhasil
            </a>
            <a href="{{ route('admin.subscription-payments.index', ['status' => 'pending']) }}" 
               class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition {{ $status == 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'text-gray-500 hover:bg-gray-50' }}">
               Pending
            </a>
        </div>
    </div>
    
    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Transaction ID</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pengguna</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Plan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nominal</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="text-[11px] font-mono font-bold text-gray-900 border-b border-gray-100 w-fit">{{ $payment->transaction_id }}</p>
                            <p class="text-[10px] text-gray-400 mt-1">{{ $payment->created_at->format('d/m/Y H:i') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-gray-900">{{ $payment->user->name }}</p>
                            <p class="text-[10px] text-gray-400">{{ $payment->user->email }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-semibold text-gray-700">{{ $payment->tier->display_name }}</span>
                            <span class="block text-[10px] text-gray-400">{{ $payment->plan->duration_months }} Bulan</span>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-900">
                           Rp {{ number_format($payment->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-widest border {{ $payment->status_badge }}">
                                {{ $payment->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                             <a href="{{ route('admin.subscription-payments.show', $payment) }}" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-200 hover:bg-gray-900 hover:text-white hover:border-gray-900 text-gray-600 text-[11px] font-bold rounded-lg transition-all">
                                <i class="fas fa-search-dollar text-[10px]"></i>
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                             <div class="flex flex-col items-center gap-2">
                                <i class="fas fa-receipt text-4xl text-gray-200"></i>
                                <p class="font-medium">Belum ada transaksi</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($payments->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

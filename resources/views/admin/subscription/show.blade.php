@extends('admin.layouts.app')

@section('title', 'Detail Langganan')
@section('page-title', 'Detail Langganan Pelanggan')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <a href="{{ route('admin.subscription-users.index') }}" class="hover:text-emerald-600 transition-colors text-sm">Pelanggan</a>
</li>
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Detail</span>
</li>
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Stats -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="p-8 flex items-center gap-6">
                    <div class="w-20 h-20 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 text-3xl font-bold border-4 border-white shadow-sm">
                        {{ substr($subscription->user->name, 0, 1) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $subscription->user->name }}</h1>
                        <p class="text-gray-500">{{ $subscription->user->email }}</p>
                        <div class="flex items-center gap-4 mt-3">
                             <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $subscription->status == 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                {{ $subscription->status }}
                            </span>
                            <span class="text-xs text-gray-400 font-medium tracking-wide">MEMBER SINCE: {{ $subscription->user->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-3 border-t border-gray-100 bg-gray-50/50">
                    <div class="p-6 text-center border-r border-gray-100">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Paket Aktif</label>
                        <p class="text-sm font-bold text-gray-900">{{ $subscription->tier->display_name }}</p>
                    </div>
                    <div class="p-6 text-center border-r border-gray-100">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Masa Berlaku</label>
                        <p class="text-sm font-bold text-gray-900">{{ ($subscription->is_trial ? $subscription->trial_ends_at : $subscription->expires_at)?->format('d M Y') ?? 'Unlimited' }}</p>
                    </div>
                    <div class="p-6 text-center">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Sisa Waktu</label>
                        <p class="text-sm font-bold {{ $subscription->days_remaining < 7 ? 'text-red-600' : 'text-emerald-600' }}">{{ $subscription->days_remaining ?? '∞' }} Hari</p>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="px-8 py-6 bg-gray-50/50 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Riwayat Pembayaran</h2>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase">Inv No.</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase">Tanggal</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase">Nominal</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($subscription->payments as $payment)
                            <tr>
                                <td class="px-6 py-4 text-xs font-mono font-bold">{{ $payment->transaction_id }}</td>
                                <td class="px-6 py-4 text-xs">{{ $payment->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-xs font-bold">Rp {{ number_format($payment->amount, 0) }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $payment->status_badge }}">
                                        {{ $payment->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400 text-sm">Belum ada riwayat pembayaran.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Settings Sidebar -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-sm space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Kelola Status</h3>
                    <p class="text-xs text-gray-500 mt-1">Ubah status langganan secara manual.</p>
                </div>

                <form action="{{ route('admin.subscription-users.status', $subscription) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status Baru</label>
                        <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all text-sm font-medium">
                            @foreach(['active', 'expired', 'cancelled', 'trial', 'pending_verification'] as $st)
                                <option value="{{ $st }}" {{ $subscription->status == $st ? 'selected' : '' }}>{{ strtoupper($st) }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="space-y-1.5">
                         <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tanggal Berakhir</label>
                         <input type="date" name="expires_at" value="{{ $subscription->expires_at?->format('Y-m-d') }}"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all text-sm">
                    </div>

                    <button type="submit" class="w-full py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-emerald-600 transition-all shadow-lg flex items-center justify-center gap-2">
                        <i class="fas fa-save text-sm"></i>
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

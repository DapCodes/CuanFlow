@extends('admin.layouts.app')

@section('title', 'Transaksi Pembayaran')
@section('page-title', 'Financial Dashboard')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Transaksi</span>
</li>
@endsection

@section('content')
<div class="px-4 lg:px-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-file-invoice-dollar text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight uppercase">Riwayat Transaksi</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium italic">Monitoring arus pendapatan dan status pembayaran gateway otomatis</p>
            </div>
        </div>
        
        <!-- Filter Tabs -->
        <div class="bg-white rounded-2xl p-1.5 flex gap-1.5 border border-gray-200 shadow-sm">
            @php
                $tabClasses = "px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 active:scale-95 whitespace-nowrap";
            @endphp
            <a href="{{ route('admin.subscription-payments.index') }}" 
               class="{{ $tabClasses }} {{ !$status ? 'bg-gray-900 text-white shadow-md' : 'text-gray-400 hover:bg-gray-50 hover:text-gray-600' }}">
               Semua
            </a>
            <a href="{{ route('admin.subscription-payments.index', ['status' => 'success']) }}" 
               class="{{ $tabClasses }} {{ $status == 'success' ? 'bg-emerald-100 text-emerald-700 shadow-sm' : 'text-gray-400 hover:bg-gray-50 hover:text-gray-600' }}">
               Berhasil
            </a>
            <a href="{{ route('admin.subscription-payments.index', ['status' => 'pending']) }}" 
               class="{{ $tabClasses }} {{ $status == 'pending' ? 'bg-amber-100 text-amber-700 shadow-sm' : 'text-gray-400 hover:bg-gray-50 hover:text-gray-600' }}">
               Pending
            </a>
        </div>
    </div>

    {{-- RINGKASAN STATISTIK --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Revenue --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Total Pendapatan</p>
                    <p class="mt-1 text-2xl font-black text-gray-900 uppercase tracking-tighter">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100 shadow-sm">
                    <i class="fas fa-wallet text-gray-400 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Successful Payments --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Bayar Berhasil</p>
                    <p class="mt-1 text-2xl font-black text-emerald-600 uppercase tracking-tighter">{{ number_format($stats['successful_count']) }} Tx</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100 shadow-sm shadow-emerald-100/50">
                    <i class="fas fa-cash-register text-emerald-500 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Pending Payments --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Menunggu Verifikasi</p>
                    <p class="mt-1 text-2xl font-black text-amber-600 uppercase tracking-tighter">{{ number_format($stats['pending_count']) }} Tx</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center border border-amber-100 shadow-sm shadow-amber-100/50">
                    <i class="fas fa-clock text-amber-500 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Monthly Revenue --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Omzet Bulan Ini</p>
                    <p class="mt-1 text-2xl font-black text-blue-600 uppercase tracking-tighter">Rp {{ number_format($stats['monthly_revenue'], 0, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center border border-blue-100 shadow-sm shadow-blue-100/50">
                    <i class="fas fa-chart-line text-blue-500 text-lg"></i>
                </div>
            </div>
        </div>
    </section>

    {{-- KONTEN UTAMA: TOOLBAR + TABEL --}}
    <x-card-container class="!p-0 overflow-hidden border border-gray-200 shadow-sm bg-white rounded-xl">
        {{-- Toolbar: Search --}}
        <div class="border-b border-gray-200 px-4 md:px-6 py-5 bg-gray-50/50">
            <form action="{{ route('admin.subscription-payments.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:items-center md:justify-between gap-4">
                <input type="hidden" name="status" value="{{ $status }}">
                <div class="w-full md:max-w-xs">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2 block italic">Cari Transaksi / Pengguna</label>
                    <div class="relative group">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari data..."
                               class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all duration-300">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-emerald-500 transition-colors text-xs"></i>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition-all shadow-md shadow-gray-200 active:scale-95 group">
                        <i class="fas fa-search group-hover:rotate-12 transition-transform"></i>
                    </button>
                    @if(request()->anyFilled(['search']))
                        <a href="{{ route('admin.subscription-payments.index', ['status' => $status]) }}" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white border border-gray-200 text-gray-400 hover:bg-gray-50 hover:text-red-500 transition-all shadow-sm active:scale-95" title="Reset">
                            <i class="fas fa-redo-alt text-sm"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left">Kode Transaksi</th>
                        <th class="px-6 py-4 text-left whitespace-nowrap">Detail Langganan</th>
                        <th class="px-6 py-4 text-left">Nominal</th>
                        <th class="px-6 py-4 text-center">Status Pembayaran</th>
                        <th class="px-6 py-4 text-center font-black">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payments as $payment)
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

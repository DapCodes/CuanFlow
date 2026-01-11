@extends('admin.layouts.app')

@section('title', 'Manajemen Penarikan')

@section('breadcrumb')
<li class="flex items-center">
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900 font-medium">Penarikan</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-teal-50 text-teal-600 border border-teal-100">
                        <i class="fas fa-money-bill-transfer text-sm"></i>
                    </span>
                    <span>Manajemen Penarikan</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola dan proses permintaan penarikan saldo dari para pemilik outlet.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                <a href="{{ route('admin.withdrawals.tax-settings') }}" 
                   class="inline-flex items-center gap-2 rounded-lg bg-white border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:ring-offset-1 transition-all">
                    <i class="fas fa-cog text-gray-400"></i>
                    <span>Pengaturan Pajak</span>
                </a>
            </div>
        </section>

        {{-- RINGKASAN STATISTIK --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Menunggu</p>
                        <p class="mt-1 text-2xl font-semibold text-yellow-600">{{ number_format($stats['pending']) }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-yellow-50 flex items-center justify-center border border-yellow-100">
                        <i class="fas fa-clock text-yellow-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Disetujui</p>
                        <p class="mt-1 text-2xl font-semibold text-blue-600">{{ number_format($stats['approved']) }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center border border-blue-100">
                        <i class="fas fa-check-circle text-blue-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Sudah Dibayar</p>
                        <p class="mt-1 text-2xl font-semibold text-emerald-600">{{ number_format($stats['paid']) }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100">
                        <i class="fas fa-money-bill-wave text-emerald-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Pending</p>
                        <p class="mt-1 text-xl font-semibold text-teal-700">Rp {{ number_format($stats['total_pending_amount'], 0, ',', '.') }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-teal-50 flex items-center justify-center border border-teal-100">
                        <i class="fas fa-wallet text-teal-500 text-lg"></i>
                    </div>
                </div>
            </div>
        </section>

        {{-- KONTEN UTAMA: TOOLBAR + TABEL --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            {{-- Toolbar: Search & Filter --}}
            <div class="border-b border-gray-200 px-4 md:px-6 py-5 bg-gray-50/50">
                <form action="{{ route('admin.withdrawals.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:items-center md:justify-between gap-4">
                    <div class="w-full md:max-w-xs">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Cari Pengguna/Rekening</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, no rek..."
                                   class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-300 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-end gap-3 w-full md:w-auto">
                        <div class="w-full sm:w-36">
                            <label class="text-xs font-medium text-gray-500 mb-1 block">Status</label>
                            <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400">
                                <option value="">Semua</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Dibayar</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>

                        <div class="w-full sm:w-36">
                            <label class="text-xs font-medium text-gray-500 mb-1 block">Dari</label>
                            <input type="date" name="from" value="{{ request('from') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                        </div>

                        <div class="w-full sm:w-36">
                            <label class="text-xs font-medium text-gray-500 mb-1 block">Sampai</label>
                            <input type="date" name="to" value="{{ request('to') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-800 text-white hover:bg-gray-700 transition-all shadow-sm">
                                <i class="fas fa-search"></i>
                            </button>
                            @if(request()->anyFilled(['status', 'search', 'from', 'to']))
                                <a href="{{ route('admin.withdrawals.index') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-white border border-gray-300 text-gray-500 hover:bg-gray-50 transition-all shadow-sm" title="Reset">
                                    <i class="fas fa-undo"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-[11px] text-gray-500 uppercase font-bold border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4">ID / Tanggal</th>
                            <th class="px-6 py-4">Pengguna & Outlet</th>
                            <th class="px-6 py-4">Tujuan Transfer</th>
                            <th class="px-6 py-4">Nominal</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($withdrawals as $w)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-900 text-sm">#{{ $w->id }}</p>
                                <p class="text-[10px] text-gray-400 mt-0.5 uppercase tracking-tighter">{{ $w->created_at->isoFormat('D MMM Y, HH:mm') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $w->user->avatar_url }}" class="h-8 w-8 rounded-full border border-gray-100 object-cover shadow-sm">
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-gray-900 truncate leading-tight">{{ $w->user->name }}</p>
                                        <p class="text-[10px] text-teal-600 truncate font-medium">{{ $w->outlet->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="p-1.5 bg-gray-50 border border-gray-100 rounded text-gray-500">
                                        <i class="fas fa-university text-[10px]"></i>
                                    </div>
                                    <div class="text-sm">
                                        <p class="font-bold text-gray-800 leading-tight">{{ $w->payment_method }}</p>
                                        <p class="text-[10px] text-gray-500 font-mono tracking-tight">{{ $w->account_number }}</p>
                                    </div>
                                </div>
                                <p class="text-[9px] text-gray-400 truncate mt-1 italic">{{ $w->account_name }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-teal-700">Rp {{ number_format($w->net_amount, 0, ',', '.') }}</p>
                                <p class="text-[9px] text-gray-400 mt-0.5">Potong pajak: Rp {{ number_format($w->tax_amount, 0, ',', '.') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                {!! $w->status_badge !!}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.withdrawals.show', $w) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 text-gray-400 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition-all" 
                                   title="Lihat Detail">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-dashed border-gray-200">
                                        <i class="fas fa-inbox text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">Belum ada pengajuan penarikan</p>
                                    <p class="text-xs text-gray-400 mt-1 pb-4">Coba sesuaikan filter Anda</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($withdrawals->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $withdrawals->links() }}
            </div>
            @endif
        </section>
    </div>
</main>
@endsection

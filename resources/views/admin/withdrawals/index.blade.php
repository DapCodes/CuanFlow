@extends('admin.layouts.app')

@section('title', 'Manajemen Penarikan')

@section('breadcrumb')
<li class="flex items-center">
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900 font-medium">Penarikan</span>
</li>
@endsection

@section('content')
<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-4 text-yellow-600">
            <div class="p-3 bg-yellow-50 rounded-lg">
                <i class="fas fa-clock text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Menunggu</p>
                <p class="text-2xl font-bold">{{ number_format($stats['pending']) }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-4 text-blue-600">
            <div class="p-3 bg-blue-50 rounded-lg">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Disetujui</p>
                <p class="text-2xl font-bold">{{ number_format($stats['approved']) }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-4 text-green-600">
            <div class="p-3 bg-green-50 rounded-lg">
                <i class="fas fa-money-bill-wave text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Sudah Dibayar</p>
                <p class="text-2xl font-bold">{{ number_format($stats['paid']) }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-4 text-teal-600">
            <div class="p-3 bg-teal-50 rounded-lg">
                <i class="fas fa-wallet text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Target Bayar</p>
                <p class="text-xl font-bold">Rp {{ number_format($stats['total_pending_amount'], 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters & List -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="p-6 border-b border-gray-200 bg-gray-50/50">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h3 class="font-bold text-gray-800">Daftar Permintaan Penarikan</h3>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.withdrawals.tax-settings') }}" class="text-sm font-bold text-teal-600 hover:text-teal-700 bg-teal-50 px-4 py-2 rounded-lg border border-teal-100">
                    <i class="fas fa-cog mr-1"></i> Pengaturan Pajak
                </a>
            </div>
        </div>

        <!-- Filter Form -->
        <form action="{{ route('admin.withdrawals.index') }}" method="GET" class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label>
                <select name="status" class="w-full border rounded-lg p-2 text-sm focus:ring-cuan-dark">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Dibayar</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pencarian User/Rekening</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, email, atau no rek..." class="w-full border rounded-lg p-2 text-sm focus:ring-cuan-dark">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Dari Tanggal</label>
                <input type="date" name="from" value="{{ request('from') }}" class="w-full border rounded-lg p-2 text-sm">
            </div>
            <div class="flex items-end gap-2">
                <div class="flex-grow">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Sampai</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="w-full border rounded-lg p-2 text-sm">
                </div>
                <button type="submit" class="bg-gray-800 text-white p-2.5 rounded-lg hover:bg-gray-700 transition">
                    <i class="fas fa-search"></i>
                </button>
                @if(request()->anyFilled(['status', 'search', 'from', 'to']))
                <a href="{{ route('admin.withdrawals.index') }}" class="bg-gray-100 text-gray-500 p-2.5 rounded-lg hover:bg-gray-200 transition" title="Reset">
                    <i class="fas fa-undo"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase font-bold border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4">ID / Tanggal</th>
                    <th class="px-6 py-4">Pengguna & Outlet</th>
                    <th class="px-6 py-4">Tujuan Transfer</th>
                    <th class="px-6 py-4">Nominal Bersih</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($withdrawals as $w)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-bold text-gray-900 leading-none">#{{ $w->id }}</p>
                        <p class="text-[10px] text-gray-400 mt-1 uppercase">{{ $w->created_at->isoFormat('D MMM Y, HH:mm') }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ $w->user->avatar_url }}" class="h-8 w-8 rounded-full border">
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-gray-900 truncate leading-tight">{{ $w->user->name }}</p>
                                <p class="text-[10px] text-teal-600 truncate">{{ $w->outlet->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <p class="font-bold text-gray-900 leading-tight">{{ $w->payment_method }}</p>
                        <p class="text-xs text-gray-500 font-mono">{{ $w->account_number }}</p>
                        <p class="text-[10px] text-gray-400 truncate">{{ $w->account_name }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-bold text-teal-700">Rp {{ number_format($w->net_amount, 0, ',', '.') }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">Potong pajak: Rp {{ number_format($w->tax_amount, 0, ',', '.') }}</p>
                    </td>
                    <td class="px-6 py-4">
                        {!! $w->status_badge !!}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('admin.withdrawals.show', $w) }}" class="inline-flex items-center justify-center p-2 text-cuan-dark hover:bg-cuan-yellow/20 rounded-lg transition-colors" title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="h-16 w-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                <i class="fas fa-money-bill-transfer text-2xl text-gray-300"></i>
                            </div>
                            <p class="text-gray-500 font-medium">Belum ada pengajuan penarikan</p>
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
</div>
@endsection

@extends('layouts.app')

@section('title', $title . ' - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-300 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
    <a href="{{ route('expenses.index', ['type' => $type]) }}" class="text-gray-900 font-medium">{{ $title }}</a>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Alert Success --}}
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 flex items-start gap-3 text-sm animate-fade-in-down">
                <i class="fas fa-check-circle mt-0.5 text-green-500"></i>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        {{-- HEADER HALAMAN --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg {{ $type == 'income' ? 'bg-emerald-50 text-emerald-500 border border-emerald-100' : 'bg-red-50 text-red-500 border border-red-100' }}">
                        <i class="fas {{ $type == 'income' ? 'fa-wallet' : 'fa-money-bill-wave' }} text-sm"></i>
                    </span>
                    <span>Kelola {{ $title }}</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola dan pantau data {{ strtolower($title) }} operasional outlet dengan mudah.
                </p>
            </div>
            
            @php
                $createPermission = $type == 'income' ? 'buat pemasukan' : 'buat pengeluaran';
            @endphp
            @can($createPermission)
            <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                <a href="{{ route('expenses.create', ['type' => $type]) }}" 
                   class="inline-flex items-center gap-2 rounded-lg {{ $type == 'income' ? 'bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-400' : 'bg-red-600 hover:bg-red-700 focus:ring-red-400' }} px-4 py-2.5 text-sm font-semibold text-white focus:outline-none focus:ring-2 focus:ring-offset-1 transition-all">
                    <i class="fas fa-plus-circle text-sm"></i>
                    <span>Tambah {{ $title }}</span>
                </a>
            </div>
            @endcan
        </section>

        {{-- KONTEN UTAMA: TOOLBAR + TABEL --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            {{-- Toolbar: Search & Filter --}}
            <div class="border-b border-gray-200 px-4 md:px-6 py-4 space-y-3 md:space-y-0 md:flex md:items-center md:justify-between gap-4">
                <div class="w-full md:max-w-md">
                    <label class="text-xs font-medium text-gray-500 mb-1 block">Cari {{ strtolower($title) }}</label>
                    <div class="relative">
                        <!-- Search functionality could be implemented via JS or Backend. For now reusing the discount design -->
                         <!-- Adding a form for basic search if needed, or just keeping the input for JS filtering/future implementation -->
                         <form method="GET" action="{{ route('expenses.index') }}">
                            <input type="hidden" name="type" value="{{ $type }}">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari deskripsi atau nomor..."
                                class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                         </form>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 w-full md:w-auto">
                    <div class="w-full sm:w-48">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Filter Status</label>
                        <select onchange="window.location.href=this.value"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                            <option value="{{ route('expenses.index', ['type' => $type]) }}">Semua Status</option>
                            <option value="{{ route('expenses.index', ['type' => $type, 'status' => 'pending']) }}" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="{{ route('expenses.index', ['type' => $type, 'status' => 'approved']) }}" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                            <option value="{{ route('expenses.index', ['type' => $type, 'status' => 'rejected']) }}" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Tanggal & Nomor
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Kategori / Deskripsi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Nominal
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Oleh
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($expenses as $expense)
                        <tr class="hover:bg-gray-50 transition-colors">
                            {{-- Tanggal & Nomor --}}
                            <td class="px-6 py-3 whitespace-nowrap">
                                <div class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</div>
                                <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 font-mono mt-1">
                                    {{ $expense->expense_number }}
                                </span>
                            </td>

                            {{-- Kategori & Deskripsi --}}
                            <td class="px-6 py-3">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-lg flex-shrink-0 flex items-center justify-center text-xs border
                                        {{ $type == 'income' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-red-50 text-red-600 border-red-100' }}">
                                        <i class="fas {{ $type == 'income' ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $expense->category->name ?? 'Uncategorized' }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5 line-clamp-1" title="{{ $expense->description }}">{{ $expense->description }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Nominal --}}
                            <td class="px-6 py-3 whitespace-nowrap">
                                <span class="font-bold {{ $type == 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $type == 'income' ? '+' : '-' }} Rp {{ number_format(abs($expense->amount), 0, ',', '.') }}
                                </span>
                                <div class="text-[10px] text-gray-400 mt-1 uppercase tracking-wider font-semibold">Via {{ $expense->payment_method }}</div>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-3 whitespace-nowrap">
                                @if($expense->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                        Disetujui
                                    </span>
                                @elseif($expense->status === 'rejected')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>
                                        Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700 border border-yellow-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5"></span>
                                        Pending
                                    </span>
                                @endif
                            </td>

                            {{-- Oleh --}}
                            <td class="px-6 py-3 whitespace-nowrap">
                                <div class="text-xs">
                                    <span class="text-gray-900 font-medium">{{ $expense->creator->name ?? '-' }}</span>
                                    @if($expense->approvedBy)
                                        <div class="text-gray-400 mt-0.5 text-[10px]">
                                            {{ $expense->status == 'approved' ? 'Acc:' : 'Reject:' }} {{ $expense->approvedBy->name }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-3 whitespace-nowrap text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('expenses.show', $expense->id) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-gray-200 bg-white text-gray-600 hover:bg-gray-50"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>

                                    @if($expense->status === 'pending')
                                        @php
                                            $approvalPermission = $type == 'income' ? 'setujui pemasukan' : 'setujui pengeluaran';
                                        @endphp
                                        @can($approvalPermission)
                                            <form action="{{ route('expenses.approve', $expense->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-emerald-200 bg-emerald-50 text-emerald-600 hover:bg-emerald-100"
                                                    title="Setujui" onclick="return confirm('Setujui transaksi ini?')">
                                                    <i class="fas fa-check text-xs"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('expenses.reject', $expense->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-red-200 bg-red-50 text-red-600 hover:bg-red-100"
                                                    title="Tolak" onclick="return confirm('Tolak transaksi ini?')">
                                                    <i class="fas fa-times text-xs"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    @endif

                                    @php
                                        $editPermission = $type == 'income' ? 'edit pemasukan' : 'edit pengeluaran';
                                        $deletePermission = $type == 'income' ? 'hapus pemasukan' : 'hapus pengeluaran';
                                    @endphp

                                    @if($expense->status === 'pending' || auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin'))
                                        @can($editPermission)
                                        <a href="{{ route('expenses.edit', $expense->id) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-yellow-200 bg-yellow-50 text-yellow-600 hover:bg-yellow-100"
                                           title="Edit">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        @endcan

                                        @can($deletePermission)
                                        <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-red-200 bg-red-50 text-red-600 hover:bg-red-100"
                                                    title="Hapus">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center text-center">
                                    <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                        <i class="fas {{ $type == 'income' ? 'fa-wallet' : 'fa-money-bill-wave' }} text-3xl text-gray-300"></i>
                                    </div>
                                    <h3 class="text-base font-semibold text-gray-900 mb-1">Belum ada data</h3>
                                    <p class="text-sm text-gray-500 mb-4 max-w-sm">
                                        Belum ada {{ strtolower($title) }} yang tercatat saat ini.
                                    </p>
                                    @can($createPermission)
                                    <a href="{{ route('expenses.create', ['type' => $type]) }}"
                                       class="inline-flex items-center gap-2 rounded-lg {{ $type == 'income' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-red-600 hover:bg-red-700' }} px-4 py-2.5 text-sm font-semibold text-white">
                                        <i class="fas fa-plus-circle text-xs"></i>
                                        Tambah {{ $title }}
                                    </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($expenses->hasPages())
                <div class="px-4 md:px-6 py-3 border-t border-gray-200">
                    {{ $expenses->links() }}
                </div>
            @endif
        </section>

    </div>
</main>
@endsection

@extends('layouts.app')

@section('title', 'Keuangan - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Keuangan</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-4 md:py-6 px-3 md:px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-4 md:space-y-6">

        {{-- Alert / Notifikasi --}}
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-check-circle mt-0.5 text-green-500"></i>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        {{-- HEADER HALAMAN: seragam dengan Outlet/Diskon --}}
        <section
            class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-600 border border-blue-100">
                        <i class="fas fa-wallet text-sm"></i>
                    </span>
                    <span>Ringkasan Keuangan</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Pantau pendapatan dan pengeluaran outlet Anda dalam satu tampilan yang rapi dan konsisten.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                <a href="{{ route('raw-materials.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-purple-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-1 shadow-sm">
                    <i class="fas fa-arrow-down text-xs"></i>
                    <span>Tambah Pengeluaran</span>
                </a>
                <a href="{{ route('raw-materials.suppliers.create') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-green-600 border border-gray-200 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-1 shadow-sm">
                    <i class="fas fa-arrow-up text-xs"></i>
                    <span>Tambah Pemasukan</span>
                </a>
            </div>
        </section>

        {{-- SUMMARY CARDS --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
            {{-- Saldo Kas Total (All Time) --}}
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg md:rounded-xl p-4 md:p-6 shadow-lg text-white relative overflow-hidden sm:col-span-2 lg:row-span-2">
                <div class="absolute top-0 right-0 opacity-10">
                    <i class="fas fa-wallet text-6xl md:text-8xl"></i>
                </div>
                <div class="relative z-10 h-full flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2 md:mb-3">
                            <p class="text-xs md:text-sm font-semibold uppercase tracking-wide opacity-90">Saldo Kas Bersih</p>
                            <i class="fas fa-wallet text-xl md:text-2xl opacity-75"></i>
                        </div>
                        <p class="text-3xl md:text-5xl font-bold mb-1 md:mb-2">Rp {{ number_format($totalNetIncome, 0, ',', '.') }}</p>
                        <p class="text-xs md:text-sm opacity-80 mb-2 md:mb-4">Total pendapatan dikurangi pengeluaran</p>
                        <div class="flex items-center space-x-4 text-xs md:text-sm opacity-90">
                            <div>
                                <p class="opacity-75">Pendapatan</p>
                                <p class="font-semibold">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="opacity-75">Pengeluaran</p>
                                <p class="font-semibold">Rp {{ number_format($allTimeExpenses, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-auto pt-3 md:pt-4 border-t border-white/20">
                        <p class="text-xs opacity-75">
                            <i class="far fa-calendar mr-1"></i>
                            Update terakhir: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- PENDAPATAN --}}
            <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-lg md:rounded-xl p-3 md:p-5 shadow-lg text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 opacity-10">
                    <i class="fas fa-chart-line text-5xl md:text-7xl"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-semibold uppercase tracking-wide opacity-90 mb-1 md:mb-2">Pendapatan Tahun ini</p>
                    <p class="text-xl md:text-2xl font-bold mb-0.5 md:mb-1">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</p>
                    <p class="text-xs opacity-75">{{ \Carbon\Carbon::now()->format('Y') }}</p>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg md:rounded-xl p-3 md:p-5 shadow-lg text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 opacity-10">
                    <i class="fas fa-chart-line text-5xl md:text-7xl"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-semibold uppercase tracking-wide opacity-90 mb-1 md:mb-2">Pendapatan Bulan ini</p>
                    <p class="text-xl md:text-2xl font-bold mb-0.5 md:mb-1">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</p>
                    <p class="text-xs opacity-75">{{ \Carbon\Carbon::now()->format('F Y') }}</p>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-400 to-green-500 rounded-lg md:rounded-xl p-3 md:p-5 shadow-lg text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 opacity-10">
                    <i class="fas fa-chart-area text-5xl md:text-7xl"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-semibold uppercase tracking-wide opacity-90 mb-1 md:mb-2">Pendapatan Minggu ini</p>
                    <p class="text-xl md:text-2xl font-bold mb-0.5 md:mb-1">Rp {{ number_format($weeklyRevenue, 0, ',', '.') }}</p>
                    <p class="text-xs opacity-75">7 Hari Terakhir</p>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-300 to-green-400 rounded-lg md:rounded-xl p-3 md:p-5 shadow-lg text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 opacity-10">
                    <i class="fas fa-chart-bar text-5xl md:text-7xl"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-semibold uppercase tracking-wide opacity-90 mb-1 md:mb-2">Pendapatan Hari ini</p>
                    <p class="text-xl md:text-2xl font-bold mb-0.5 md:mb-1">Rp {{ number_format($dailyRevenue, 0, ',', '.') }}</p>
                    <p class="text-xs opacity-75">{{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}</p>
                </div>
            </div>

            {{-- PENGELUARAN --}}
            <div class="bg-gradient-to-br from-red-600 to-red-700 rounded-lg md:rounded-xl p-3 md:p-5 shadow-lg text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 opacity-10">
                    <i class="fas fa-minus-circle text-5xl md:text-7xl"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-semibold uppercase tracking-wide opacity-90 mb-1 md:mb-2">Pengeluaran Tahun ini</p>
                    <p class="text-xl md:text-2xl font-bold mb-0.5 md:mb-1">Rp {{ number_format($monthlyExpenses, 0, ',', '.') }}</p>
                    <p class="text-xs opacity-75">{{ \Carbon\Carbon::now()->format('Y') }}</p>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg md:rounded-xl p-3 md:p-5 shadow-lg text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 opacity-10">
                    <i class="fas fa-minus-circle text-5xl md:text-7xl"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-semibold uppercase tracking-wide opacity-90 mb-1 md:mb-2">Pengeluaran Bulan ini</p>
                    <p class="text-xl md:text-2xl font-bold mb-0.5 md:mb-1">Rp {{ number_format($monthlyExpenses, 0, ',', '.') }}</p>
                    <p class="text-xs opacity-75">{{ \Carbon\Carbon::now()->format('F Y') }}</p>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-400 to-red-500 rounded-lg md:rounded-xl p-3 md:p-5 shadow-lg text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 opacity-10">
                    <i class="fas fa-cash-register text-5xl md:text-7xl"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-semibold uppercase tracking-wide opacity-90 mb-1 md:mb-2">Pengeluaran Minggu ini</p>
                    <p class="text-xl md:text-2xl font-bold mb-0.5 md:mb-1">Rp {{ number_format($weeklyExpenses, 0, ',', '.') }}</p>
                    <p class="text-xs opacity-75">7 Hari Terakhir</p>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-300 to-red-400 rounded-lg md:rounded-xl p-3 md:p-5 shadow-lg text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 opacity-10">
                    <i class="fas fa-receipt text-5xl md:text-7xl"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-semibold uppercase tracking-wide opacity-90 mb-1 md:mb-2">Pengeluaran Hari ini</p>
                    <p class="text-xl md:text-2xl font-bold mb-0.5 md:mb-1">Rp {{ number_format($dailyExpenses, 0, ',', '.') }}</p>
                    <p class="text-xs opacity-75">{{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}</p>
                </div>
            </div>
        </section>

        {{-- MAIN CONTENT GRID: 2 kolom besar (penjualan/pengeluaran & chart) --}}
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 items-stretch">
            <div class="flex flex-col h-full space-y-4 md:space-y-6">
                {{-- Rincian Pendapatan --}}
                <div class="bg-white rounded-lg md:rounded-xl shadow-sm border border-gray-200 flex flex-col flex-1">
                    <div class="p-3 md:p-5 border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <h3 class="text-base md:text-lg font-bold text-gray-900">Rincian Pendapatan</h3>
                                <p class="text-xs md:text-sm text-gray-500 mt-1">Semua data pendapatan bulan ini</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <select id="yearFilter" class="px-2 md:px-3 py-1.5 md:py-2 border border-gray-300 rounded-lg text-xs md:text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                                        <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                                <select id="monthFilter" class="px-2 md:px-3 py-1.5 md:py-2 border border-gray-300 rounded-lg text-xs md:text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $month)
                                        <option value="{{ $i + 1 }}" {{ ($i + 1) == date('n') ? 'selected' : '' }}>{{ $month }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-3 md:p-5 flex-1 flex flex-col justify-between">
                        <div class="overflow-x-auto -mx-3 md:mx-0 w-full">
                            <div class="inline-block min-w-full align-middle">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="px-2 md:px-3 py-2 md:py-3 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                                            <th class="px-2 md:px-3 py-2 md:py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kasir</th>
                                            <th class="px-2 md:px-3 py-2 md:py-3 text-left text-xs font-semibold text-gray-600 uppercase hidden sm:table-cell">Metode</th>
                                            <th class="px-2 md:px-3 py-2 md:py-3 text-right text-xs font-semibold text-gray-600 uppercase">Total</th>
                                            <th class="px-2 md:px-3 py-2 md:py-3 text-left text-xs font-semibold text-gray-600 uppercase hidden md:table-cell">Tanggal</th>
                                            <th class="px-2 md:px-3 py-2 md:py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="salesTableBody" class="divide-y divide-gray-100">
                                        @forelse($salesList->take(5) as $index => $sale)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-2 md:px-3 py-2 md:py-3 text-xs md:text-sm text-gray-700">{{ $index + 1 }}</td>
                                                <td class="px-2 md:px-3 py-2 md:py-3 text-xs md:text-sm font-medium text-gray-900">
                                                    {{ $sale->cashier->name }}
                                                    <span class="block sm:hidden text-xs text-gray-500 mt-0.5">
                                                        {{ $sale->payment_method == 'cash' ? 'Tunai' : ucfirst($sale->payment_method) }}
                                                    </span>
                                                </td>
                                                <td class="px-2 md:px-3 py-2 md:py-3 text-xs md:text-sm text-gray-700 hidden sm:table-cell">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                        {{ $sale->payment_method == 'cash' ? 'bg-green-100 text-green-800' : '' }}
                                                        {{ $sale->payment_method == 'qris' ? 'bg-blue-100 text-blue-800' : '' }}
                                                        {{ $sale->payment_method == 'transfer' ? 'bg-purple-100 text-purple-800' : '' }}">
                                                        {{ $sale->payment_method == 'cash' ? 'Tunai' : ucfirst($sale->payment_method) }}
                                                    </span>
                                                </td>
                                                <td class="px-2 md:px-3 py-2 md:py-3 text-xs md:text-sm font-semibold text-green-600 text-right whitespace-nowrap">
                                                    Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                                                </td>
                                                <td class="px-2 md:px-3 py-2 md:py-3 text-xs md:text-sm text-gray-600 hidden md:table-cell">
                                                    {{ $sale->created_at->format('d-m-Y H:i') }}
                                                </td>
                                                <td class="px-2 md:px-3 py-2 md:py-3">
                                                    <div class="flex items-center justify-center gap-1 md:gap-2">
                                                        <a href="{{ route('sales.show', $sale->id) }}" class="w-7 h-7 md:w-8 md:h-8 bg-green-500 hover:bg-green-600 text-white rounded-md flex items-center justify-center transition-colors" title="Lihat Detail">
                                                            <i class="fas fa-eye text-xs"></i>
                                                        </a>
                                                        <a href="{{ route('receipt.preview', $sale->id) }}" class="w-7 h-7 md:w-8 md:h-8 bg-blue-500 hover:bg-blue-600 text-white rounded-md flex items-center justify-center transition-colors" title="Print">
                                                            <i class="fas fa-print text-xs"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="h-40 md:h-52 text-center text-gray-500 align-middle">
                                                    <div class="flex flex-col items-center justify-center h-full">
                                                        <i class="fas fa-inbox text-2xl md:text-3xl mb-2 block text-gray-300"></i>
                                                        <p class="text-xs md:text-sm">Belum ada data pendapatan</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        @if($salesList->count() > 0)
                            <div class="mt-3 md:mt-4 flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-gray-200 pt-3 md:pt-4">
                                <p class="text-xs md:text-sm text-gray-600">Menampilkan {{ min(5, $salesList->count()) }} dari {{ $salesList->count() }} transaksi</p>
                                <div class="flex items-center gap-1 md:gap-2">
                                    <span class="text-xs text-gray-500">Total: <span class="font-semibold text-green-600">Rp {{ number_format($salesList->sum('grand_total'), 0, ',', '.') }}</span></span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Rincian Pengeluaran --}}
                <div class="bg-white rounded-lg md:rounded-xl shadow-sm border border-gray-200 flex flex-col flex-1">
                    <div class="p-3 md:p-5 border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <h3 class="text-base md:text-lg font-bold text-gray-900">Rincian Pengeluaran</h3>
                                <p class="text-xs md:text-sm text-gray-500 mt-1">Top pengeluaran bulan ini</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <select id="expenseYearFilter" class="px-2 md:px-3 py-1.5 md:py-2 border border-gray-300 rounded-lg text-xs md:text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                                        <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                                <select id="expenseMonthFilter" class="px-2 md:px-3 py-1.5 md:py-2 border border-gray-300 rounded-lg text-xs md:text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $month)
                                        <option value="{{ $i + 1 }}" {{ ($i + 1) == date('n') ? 'selected' : '' }}>{{ $month }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-3 md:p-5 flex-1 flex flex-col justify-between">
                        <div class="overflow-x-auto -mx-3 md:mx-0 w-full">
                            <div class="inline-block min-w-full align-middle">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="px-2 md:px-3 py-2 md:py-3 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                                            <th class="px-2 md:px-3 py-2 md:py-3 text-left text-xs font-semibold text-gray-600 uppercase">Deskripsi</th>
                                            <th class="px-2 md:px-3 py-2 md:py-3 text-left text-xs font-semibold text-gray-600 uppercase hidden sm:table-cell">Kategori</th>
                                            <th class="px-2 md:px-3 py-2 md:py-3 text-right text-xs font-semibold text-gray-600 uppercase">Jumlah</th>
                                            <th class="px-2 md:px-3 py-2 md:py-3 text-left text-xs font-semibold text-gray-600 uppercase hidden md:table-cell">Tanggal</th>
                                            <th class="px-2 md:px-3 py-2 md:py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @forelse($expenses->take(5) as $index => $expense)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-2 md:px-3 py-2 md:py-3 text-xs md:text-sm text-gray-700">{{ $index + 1 }}</td>
                                                <td class="px-2 md:px-3 py-2 md:py-3 text-xs md:text-sm font-medium text-gray-900">
                                                    {{ Str::limit($expense->description, 30) }}
                                                    <span class="block sm:hidden text-xs text-gray-500 mt-0.5">
                                                        {{ $expense->category->name ?? 'Lainnya' }}
                                                    </span>
                                                </td>
                                                <td class="px-2 md:px-3 py-2 md:py-3 text-xs md:text-sm text-gray-700 hidden sm:table-cell">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ $expense->category->name ?? 'Lainnya' }}
                                                    </span>
                                                </td>
                                                <td class="px-2 md:px-3 py-2 md:py-3 text-xs md:text-sm font-semibold text-red-600 text-right whitespace-nowrap">
                                                    Rp {{ number_format($expense->amount, 0, ',', '.') }}
                                                </td>
                                                <td class="px-2 md:px-3 py-2 md:py-3 text-xs md:text-sm text-gray-600 hidden md:table-cell">
                                                    {{ \Carbon\Carbon::parse($expense->expense_date)->format('d-m-Y') }}
                                                </td>
                                                <td class="px-2 md:px-3 py-2 md:py-3">
                                                    <div class="flex items-center justify-center gap-1 md:gap-2">
                                                        <button class="w-7 h-7 md:w-8 md:h-8 bg-green-500 hover:bg-green-600 text-white rounded-md flex items-center justify-center transition-colors" title="Lihat Detail">
                                                            <i class="fas fa-eye text-xs"></i>
                                                        </button>
                                                        <button class="w-7 h-7 md:w-8 md:h-8 bg-red-500 hover:bg-red-600 text-white rounded-md flex items-center justify-center transition-colors" title="Hapus">
                                                            <i class="fas fa-trash text-xs"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="h-40 md:h-52 text-center text-gray-500 align-middle">
                                                    <div class="flex flex-col items-center justify-center h-full">
                                                        <i class="fas fa-inbox text-2xl md:text-3xl mb-2 block text-gray-300"></i>
                                                        <p class="text-xs md:text-sm">Belum ada data pengeluaran</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        @if($expenses->count() > 0)
                            <div class="mt-3 md:mt-4 flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-gray-200 pt-3 md:pt-4">
                                <p class="text-xs md:text-sm text-gray-600">Menampilkan {{ min(5, $expenses->count()) }} dari {{ $expenses->count() }} pengeluaran</p>
                                <div class="flex items-center gap-1 md:gap-2">
                                    <span class="text-xs text-gray-500">Total: <span class="font-semibold text-red-600">Rp {{ number_format($expenses->sum('amount'), 0, ',', '.') }}</span></span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Chart pendapatan & pengeluaran --}}
            <div class="flex flex-col h-full space-y-4 md:space-y-6">
                <div class="bg-white rounded-lg md:rounded-xl shadow-sm border border-gray-200 flex flex-col flex-1">
                    <div class="p-3 md:p-5 border-b border-gray-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div>
                            <h3 class="text-base md:text-lg font-bold text-gray-900">Distribusi Pendapatan</h3>
                            <p class="text-xs md:text-sm text-gray-500 mt-1">Berdasarkan produk</p>
                        </div>
                        <select id="revenueYearFilter" class="w-full sm:w-auto px-2 md:px-3 py-1.5 md:py-2 border border-gray-300 rounded-lg text-xs md:text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="week" selected>Minggu Ini</option>
                            <option value="month">Bulan Ini</option>
                            <option value="year">Tahun Ini</option>
                        </select>
                    </div>
                    
                    <div class="p-3 md:p-5 flex-1 flex flex-col justify-center">
                        <div class="h-48 md:h-64 flex items-center justify-center w-full">
                            <canvas id="revenueChart"></canvas>
                        </div>
                        <div id="revenueLegend" class="mt-4 md:mt-6 grid grid-cols-2 gap-2 md:gap-3"></div>
                    </div>
                </div>

                <div class="bg-white rounded-lg md:rounded-xl shadow-sm border border-gray-200 flex flex-col flex-1">
                    <div class="p-3 md:p-5 border-b border-gray-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div>
                            <h3 class="text-base md:text-lg font-bold text-gray-900">Pengeluaran Toko</h3>
                            <p class="text-xs md:text-sm text-gray-500 mt-1">Berdasarkan kategori</p>
                        </div>
                        <select id="expenseChartYearFilter" class="w-full sm:w-auto px-2 md:px-3 py-1.5 md:py-2 border border-gray-300 rounded-lg text-xs md:text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="week" selected>Minggu ini</option>
                            <option value="month">Bulan ini</option>
                            <option value="year">Tahun ini</option>
                        </select>
                    </div>
                    
                    <div class="p-3 md:p-5 flex-1 flex flex-col justify-center">
                        <div class="h-48 md:h-64 flex items-center justify-center w-full">
                            <canvas id="expenseChart"></canvas>
                        </div>
                        <div id="expenseLegend" class="mt-4 md:mt-6 grid grid-cols-2 gap-2 md:gap-3"></div>
                    </div>
                </div>
            </div>
        </section>

    </div>
</main>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let revenueChart = null;
    let expenseChart = null;

    const colors = [
        'rgb(248, 113, 113)',
        'rgb(244, 114, 182)',
        'rgb(167, 139, 250)',
        'rgb(96, 165, 250)',
        'rgb(250, 204, 21)',
        'rgb(251, 146, 60)',
        'rgb(134, 239, 172)',
        'rgb(251, 191, 36)',
        'rgb(147, 197, 253)',
        'rgb(196, 181, 253)'
    ];

    function updateLegend(elementId, labels, colors) {
        const legendElement = document.getElementById(elementId);
        if (!legendElement) return;

        legendElement.innerHTML = labels.map((label, index) => `
            <div class="flex items-center space-x-2">
                <div class="w-2.5 h-2.5 md:w-3 md:h-3 rounded-full flex-shrink-0" style="background-color: ${colors[index]}"></div>
                <span class="text-xs text-gray-600">${label}</span>
            </div>
        `).join('');
    }

    // Load Revenue Chart
    function loadRevenueChart(period = 'month') {
        fetch(`{{ route('finance.revenue-chart') }}?period=${period}`)
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById('revenueChart');
                if (!ctx) return;

                if (revenueChart) {
                    revenueChart.destroy();
                }

                if (data.labels.length === 0) {
                    ctx.parentElement.innerHTML = '<div class="text-center text-gray-500 py-8"><i class="fas fa-inbox text-3xl mb-2 block text-gray-300"></i><p class="text-sm">Belum ada data pendapatan</p></div>';
                    document.getElementById('revenueLegend').innerHTML = '';
                    return;
                }

                revenueChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.data,
                            backgroundColor: colors.slice(0, data.labels.length),
                            borderWidth: 0,
                            cutout: '65%'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = new Intl.NumberFormat('id-ID', {
                                            style: 'currency',
                                            currency: 'IDR',
                                            minimumFractionDigits: 0
                                        }).format(context.parsed);
                                        const percentage = data.percentages[context.dataIndex];
                                        const sold = data.totalSold[context.dataIndex];
                                        return [label, value, `${percentage}% (${sold} terjual)`];
                                    }
                                }
                            }
                        }
                    }
                });

                updateLegend('revenueLegend', data.labels, colors);
            })
            .catch(error => console.error('Error loading revenue chart:', error));
    }

    // Load Expense Chart
    function loadExpenseChart(period = 'month') {
        fetch(`{{ route('finance.expense-chart') }}?period=${period}`)
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById('expenseChart');
                if (!ctx) return;

                if (expenseChart) {
                    expenseChart.destroy();
                }

                if (data.labels.length === 0) {
                    ctx.parentElement.innerHTML = '<div class="text-center text-gray-500 py-8"><i class="fas fa-inbox text-3xl mb-2 block text-gray-300"></i><p class="text-sm">Belum ada data pengeluaran</p></div>';
                    document.getElementById('expenseLegend').innerHTML = '';
                    return;
                }

                expenseChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.data,
                            backgroundColor: colors.slice(0, data.labels.length),
                            borderWidth: 0,
                            cutout: '65%'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = new Intl.NumberFormat('id-ID', {
                                            style: 'currency',
                                            currency: 'IDR',
                                            minimumFractionDigits: 0
                                        }).format(context.parsed);
                                        const percentage = data.percentages[context.dataIndex];
                                        const count = data.counts[context.dataIndex];
                                        return [label, value, `${percentage}% (${count} transaksi)`];
                                    }
                                }
                            }
                        }
                    }
                });

                updateLegend('expenseLegend', data.labels, colors);
            })
            .catch(error => console.error('Error loading expense chart:', error));
    }

    // Event listeners
    document.getElementById('revenueYearFilter')?.addEventListener('change', function() {
        loadRevenueChart(this.value);
    });

    document.getElementById('expenseChartYearFilter')?.addEventListener('change', function() {
        loadExpenseChart(this.value);
    });

    // Initial load
    loadRevenueChart('month');
    loadExpenseChart('month');
});
</script>
@endpush
@endsection

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
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Alert / Notifikasi --}}
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
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-600 border border-blue-100">
                        <i class="fas fa-wallet text-sm"></i>
                    </span>
                    <span>Ringkasan Keuangan</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Pantau arus kas, pendapatan, dan pengeluaran outlet Anda secara real-time.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('finance.expense.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-50 text-red-600 border border-red-100 rounded-lg text-sm font-semibold hover:bg-red-100 transition-all">
                    <i class="fas fa-arrow-down text-xs"></i>
                    <span>Catat Pengeluaran</span>
                </a>
                <a href="{{ route('finance.income.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-lg text-sm font-semibold hover:bg-emerald-100 transition-all">
                    <i class="fas fa-arrow-up text-xs"></i>
                    <span>Catat Pemasukan</span>
                </a>
            </div>
        </section>

        {{-- MAIN SUMMARY CARDS & CAROUSEL --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            {{-- Carousel Saldo Kas Bersih (Kiri - 5 Kolom) --}}
            <div class="lg:col-span-5 h-full">
                <div class="relative bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden h-full">
                    {{-- Carousel Container --}}
                    <div id="balanceCarousel" class="relative h-full transition-transform duration-500 ease-in-out flex" style="width: 300%;">
                        
                        {{-- SLIDE 1: TOTAL SEMUA --}}
                        <div class="w-1/3 p-8 flex flex-col justify-between bg-gradient-to-br from-blue-600 to-blue-700 text-white relative h-full">
                            <div class="absolute top-0 right-0 opacity-10 p-4">
                                <i class="fas fa-globe text-9xl"></i>
                            </div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-6">
                                    <span class="px-3 py-1 rounded-full bg-white/20 text-[10px] font-bold uppercase tracking-wider">Semua Metode</span>
                                    <i class="fas fa-wallet text-2xl opacity-50"></i>
                                </div>
                                <h4 class="text-sm font-medium opacity-80 mb-2 uppercase tracking-wide">Saldo Kas Bersih</h4>
                                <div class="text-4xl md:text-5xl font-bold tracking-tight">
                                    <span class="text-2xl font-normal opacity-70 mr-1">Rp</span>{{ number_format($totalNetIncome, 0, ',', '.') }}
                                </div>
                                <p class="mt-4 text-sm opacity-80 leading-relaxed">
                                    Gabungan seluruh pendapatan dari Cash, QRIS, dan Transfer setalah dikurangi biaya pengeluaran.
                                </p>
                            </div>
                            <div class="relative z-10 mt-8 pt-6 border-t border-white/20 flex items-center justify-between">
                                <span class="text-xs opacity-60 italic">All-time record</span>
                                <div class="flex gap-2 text-xs">
                                    <div class="bg-white/10 px-2 py-1 rounded">R: Rp {{ number_format($totalRevenue/1000, 0) }}k</div>
                                    <div class="bg-white/10 px-2 py-1 rounded">E: Rp {{ number_format($allTimeExpenses/1000, 0) }}k</div>
                                </div>
                            </div>
                        </div>

                        {{-- SLIDE 2: TUNAI & QRIS --}}
                        <div class="w-1/3 p-8 flex flex-col justify-between bg-gradient-to-br from-emerald-600 to-emerald-700 text-white relative h-full">
                            <div class="absolute top-0 right-0 opacity-10 p-4">
                                <i class="fas fa-qrcode text-9xl"></i>
                            </div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-6">
                                    <span class="px-3 py-1 rounded-full bg-white/20 text-[10px] font-bold uppercase tracking-wider">Tunai & QRIS</span>
                                    <i class="fas fa-cash-register text-2xl opacity-50"></i>
                                </div>
                                <h4 class="text-sm font-medium opacity-80 mb-2 uppercase tracking-wide">Saldo Likuid</h4>
                                <div class="text-4xl md:text-5xl font-bold tracking-tight">
                                    <span class="text-2xl font-normal opacity-70 mr-1">Rp</span>{{ number_format($cashQrisNetIncome, 0, ',', '.') }}
                                </div>
                                <p class="mt-4 text-sm opacity-80 leading-relaxed">
                                    Total saldo dari pembayaran langsung (Tunai & QRIS) yang siap digunakan untuk operasional harian.
                                </p>
                            </div>
                            <div class="relative z-10 mt-8 pt-6 border-t border-white/20 flex items-center justify-between">
                                <span class="text-xs opacity-60 italic">Real-time liquid</span>
                                <div class="flex gap-1">
                                     <span class="w-2 h-2 rounded-full bg-white opacity-50"></span>
                                     <span class="w-2 h-2 rounded-full bg-white"></span>
                                     <span class="w-2 h-2 rounded-full bg-white opacity-50"></span>
                                </div>
                            </div>
                        </div>

                        {{-- SLIDE 3: TRANSFER --}}
                        <div class="w-1/3 p-8 flex flex-col justify-between bg-gradient-to-br from-indigo-600 to-indigo-700 text-white relative h-full">
                            <div class="absolute top-0 right-0 opacity-10 p-4">
                                <i class="fas fa-university text-9xl"></i>
                            </div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-6">
                                    <span class="px-3 py-1 rounded-full bg-white/20 text-[10px] font-bold uppercase tracking-wider">Transfer Bank</span>
                                    <i class="fas fa-exchange-alt text-2xl opacity-50"></i>
                                </div>
                                <h4 class="text-sm font-medium opacity-80 mb-2 uppercase tracking-wide">Saldo Rekening</h4>
                                <div class="text-4xl md:text-5xl font-bold tracking-tight">
                                    <span class="text-2xl font-normal opacity-70 mr-1">Rp</span>{{ number_format($transferNetIncome, 0, ',', '.') }}
                                </div>
                                <p class="mt-4 text-sm opacity-80 leading-relaxed">
                                    Saldo yang masuk melalui metode transfer bank. Biasanya memerlukan pengecekan mutasi berkala.
                                </p>
                            </div>
                            <div class="relative z-10 mt-8 pt-6 border-t border-white/20 flex items-center justify-between">
                                <span class="text-xs opacity-60 italic">Bank statement balance</span>
                                <div class="flex gap-1">
                                     <span class="w-2 h-2 rounded-full bg-white opacity-50"></span>
                                     <span class="w-2 h-2 rounded-full bg-white opacity-50"></span>
                                     <span class="w-2 h-2 rounded-full bg-white font-bold"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Navigation Controls --}}
                    <div class="absolute top-6 right-8 flex items-center gap-1.5 z-20">
                        <button onclick="moveCarousel(-1)" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all backdrop-blur-sm border border-white/10">
                            <i class="fas fa-chevron-left text-[10px]"></i>
                        </button>
                        <button onclick="moveCarousel(1)" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all backdrop-blur-sm border border-white/10">
                            <i class="fas fa-chevron-right text-[10px]"></i>
                        </button>
                    </div>

                    {{-- Indicators --}}
                    <div class="absolute top-6 left-8 flex items-center gap-1.5 z-20">
                        <div class="carousel-dot w-6 h-1.5 rounded-full bg-white transition-all duration-300"></div>
                        <div class="carousel-dot w-2 h-1.5 rounded-full bg-white/30 transition-all duration-300"></div>
                        <div class="carousel-dot w-2 h-1.5 rounded-full bg-white/30 transition-all duration-300"></div>
                    </div>
                </div>
            </div>

            {{-- Secondary Stats (Kanan - 7 Kolom) --}}
            <div class="lg:col-span-7 grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Income Stats --}}
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase text-gray-400 tracking-wider">Pendapatan Terakhir</span>
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center border border-emerald-100">
                            <i class="fas fa-arrow-up text-xs"></i>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] text-gray-500 mb-1 uppercase">Bulan Ini</p>
                            <p class="text-lg font-bold text-gray-900 leading-tight">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 mb-1 uppercase">Minggu Ini</p>
                            <p class="text-lg font-bold text-gray-800 leading-tight">Rp {{ number_format($weeklyRevenue, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="pt-3 border-t border-gray-50">
                         <p class="text-[10px] text-gray-400">Total kotor s/d hari ini: <span class="font-semibold text-gray-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span></p>
                    </div>
                </div>

                {{-- Expense Stats --}}
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase text-gray-400 tracking-wider">Pengeluaran Terakhir</span>
                        <div class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center border border-red-100">
                            <i class="fas fa-arrow-down text-xs"></i>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] text-gray-500 mb-1 uppercase">Bulan Ini</p>
                            <p class="text-lg font-bold text-gray-900 leading-tight">Rp {{ number_format($monthlyExpenses, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 mb-1 uppercase">Minggu Ini</p>
                            <p class="text-lg font-bold text-gray-800 leading-tight">Rp {{ number_format($weeklyExpenses, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="pt-3 border-t border-gray-50">
                         <p class="text-[10px] text-gray-400">Total biaya s/d hari ini: <span class="font-semibold text-gray-600">Rp {{ number_format($allTimeExpenses, 0, ',', '.') }}</span></p>
                    </div>
                </div>

                {{-- Daily Summary Card --}}
                <div class="md:col-span-2 bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400">
                                <i class="far fa-calendar-alt text-2xl"></i>
                            </div>
                            <div>
                                <h5 class="text-sm font-bold text-gray-900">Performa Hari Ini</h5>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-8 pr-4">
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-bold text-right">Revenue</p>
                                <p class="text-xl font-black text-emerald-600">Rp {{ number_format($dailyRevenue, 0, ',', '.') }}</p>
                            </div>
                            <div class="w-px h-10 bg-gray-100"></div>
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-bold text-right">Profit Bersih</p>
                                <p class="text-xl font-black text-blue-600">Rp {{ number_format($dailyNetIncome, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLES & CHARTS SECTION --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            
            {{-- Penjualan Terakhir (Kiri - 2 Kolom) --}}
            <div class="xl:col-span-2 space-y-6">
                {{-- Transaksi Terbaru --}}
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center text-xs">
                                <i class="fas fa-history"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Histori Penjualan</h3>
                                <p class="text-[11px] text-gray-500">Menampilkan transaksi bulan ini</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                             <select id="yearFilter" class="text-xs px-2.5 py-1.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-gray-50">
                                @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                                    <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                            <select id="monthFilter" class="text-xs px-2.5 py-1.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-gray-50">
                                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $month)
                                    <option value="{{ $i + 1 }}" {{ ($i + 1) == date('n') ? 'selected' : '' }}>{{ $month }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">No. Invoice</th>
                                    <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Kasir / Pelanggan</th>
                                    <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Metode</th>
                                    <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-right">Total Tagihan</th>
                                    <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($salesList->take(8) as $sale)
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="text-xs font-mono font-bold text-gray-700">{{ $sale->invoice_number }}</div>
                                        <div class="text-[10px] text-gray-400 mt-1">{{ $sale->created_at->format('d M, H:i') }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="text-xs font-semibold text-gray-900">{{ $sale->cashier->name }}</div>
                                        <div class="text-[10px] text-gray-500 flex items-center gap-1 mt-0.5">
                                            <i class="far fa-user opacity-60"></i> {{ $sale->customer->name ?? 'Guest' }}
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                            @if($sale->payment_method == 'cash') bg-emerald-50 text-emerald-600 border border-emerald-100
                                            @elseif($sale->payment_method == 'qris') bg-blue-50 text-blue-600 border border-blue-100
                                            @else bg-purple-50 text-purple-600 border border-purple-100 @endif">
                                            {{ $sale->payment_method }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="text-xs font-black text-gray-900">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <a href="{{ route('sales.show', $sale->id) }}" class="p-1.5 bg-white border border-gray-200 text-gray-400 hover:text-blue-500 hover:border-blue-100 rounded-lg shadow-sm transition-all">
                                                <i class="fas fa-eye text-[10px]"></i>
                                            </a>
                                            <a href="{{ route('receipt.preview', $sale->id) }}" class="p-1.5 bg-white border border-gray-200 text-gray-400 hover:text-emerald-500 hover:border-emerald-100 rounded-lg shadow-sm transition-all">
                                                <i class="fas fa-print text-[10px]"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-gray-400 italic">Belum ada data transaksi tercatat.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pengeluaran Terakhir --}}
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center text-xs">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Data Pengeluaran</h3>
                                <p class="text-[11px] text-gray-500">Menampilkan histori pengeluaran terakhir</p>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Keperluan / Kategori</th>
                                    <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Metode</th>
                                    <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-right">Biaya</th>
                                    <th class="px-5 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($expenses->take(10) as $expense)
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    <td class="px-5 py-4">
                                        <div class="text-xs font-semibold text-gray-900">{{ Str::limit($expense->description, 40) }}</div>
                                        <div class="flex items-center gap-2 mt-1">
                                            @if($expense->amount < 0)
                                                <span class="text-[9px] font-bold text-emerald-600 uppercase bg-emerald-50 border border-emerald-100 px-1.5 py-0.5 rounded">Pemasukan</span>
                                            @else
                                                <span class="text-[9px] font-bold text-gray-400 uppercase bg-gray-100 px-1.5 py-0.5 rounded">{{ $expense->category->name ?? 'Lainnya' }}</span>
                                            @endif
                                            <span class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="text-[10px] font-medium text-gray-600 bg-white border border-gray-200 px-2 py-0.5 rounded-full">
                                            {{ ucfirst($expense->payment_method) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        @if($expense->amount < 0)
                                            <div class="text-xs font-black text-emerald-600">+ Rp {{ number_format(abs($expense->amount), 0, ',', '.') }}</div>
                                        @else
                                            <div class="text-xs font-black text-red-600">- Rp {{ number_format($expense->amount, 0, ',', '.') }}</div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                         <div class="flex items-center justify-center gap-2">
                                            {{-- Edit --}}
                                            @php
                                                $editRoute = $expense->amount < 0 ? route('finance.income.edit', $expense->id) : route('finance.expense.edit', $expense->id);
                                            @endphp
                                            <a href="{{ $editRoute }}" class="p-1.5 text-gray-400 hover:text-emerald-500 transition-colors">
                                               <i class="fas fa-edit text-[10px]"></i>
                                            </a>

                                            {{-- Delete --}}
                                            <form action="{{ route('finance.destroy', $expense->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 transition-colors">
                                                   <i class="fas fa-trash text-[10px]"></i>
                                                </button>
                                            </form>
                                         </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-12 text-center text-gray-400 italic">Belum ada pengeluaran hari ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Charts (Kanan - 1 Kolom) --}}
            <div class="space-y-6">
                {{-- Chart Revenue --}}
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 overflow-hidden">
                    <div class="flex items-center justify-between mb-6">
                         <h3 class="text-sm font-bold text-gray-900 uppercase tracking-tight">Top Produk (Revenue)</h3>
                         <select id="revenueChartPeriod" class="text-[10px] font-bold border-none bg-gray-50 rounded-md focus:ring-0">
                            <option value="week">WEEKS</option>
                            <option value="month" selected>MONTHS</option>
                            <option value="year">YEARS</option>
                         </select>
                    </div>
                    <div class="h-56 flex items-center justify-center relative">
                        <canvas id="revenueChart"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-2">
                             <p class="text-[10px] text-gray-400 font-bold uppercase">Total</p>
                             <p class="text-xs font-black text-gray-700 mt-0.5">Pendapatan</p>
                        </div>
                    </div>
                    <div id="revenueLegend" class="mt-6 grid grid-cols-1 gap-2 border-t border-gray-50 pt-4"></div>
                </div>

                {{-- Chart Expense --}}
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 overflow-hidden">
                     <div class="flex items-center justify-between mb-6">
                         <h3 class="text-sm font-bold text-gray-900 uppercase tracking-tight">Beban Kategori</h3>
                         <select id="expenseChartPeriod" class="text-[10px] font-bold border-none bg-gray-50 rounded-md focus:ring-0">
                            <option value="week">WEEKS</option>
                            <option value="month" selected>MONTHS</option>
                            <option value="year">YEARS</option>
                         </select>
                    </div>
                    <div class="h-56 flex items-center justify-center">
                        <canvas id="expenseChart"></canvas>
                    </div>
                    <div id="expenseLegend" class="mt-6 grid grid-cols-1 gap-2 border-t border-gray-50 pt-4"></div>
                </div>
            </div>

        </div>

    </div>
</main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // CAROUSEL LOGIC
    let currentSlide = 0;
    const totalSlides = 3;
    const carouselEl = document.getElementById('balanceCarousel');
    const dots = document.querySelectorAll('.carousel-dot');

    function moveCarousel(direction) {
        currentSlide = (currentSlide + direction + totalSlides) % totalSlides;
        updateCarousel();
    }

    function updateCarousel() {
        const offset = -(currentSlide * (100 / 3));
        carouselEl.style.transform = `translateX(${offset}%)`;
        
        // Update dots
        dots.forEach((dot, idx) => {
            if (idx === currentSlide) {
                dot.classList.add('w-6', 'bg-white');
                dot.classList.remove('w-2', 'bg-white/30');
            } else {
                dot.classList.remove('w-6', 'bg-white');
                dot.classList.add('w-2', 'bg-white/30');
            }
        });
    }

    // Auto slide carousel every 8 seconds
    let carouselInterval = setInterval(() => moveCarousel(1), 8000);

    // Stop auto-slide on click
    document.getElementById('balanceCarousel').parentElement.addEventListener('mouseenter', () => clearInterval(carouselInterval));
    document.getElementById('balanceCarousel').parentElement.addEventListener('mouseleave', () => {
        carouselInterval = setInterval(() => moveCarousel(1), 8000);
    });

    // CHARTS LOGIC
    document.addEventListener('DOMContentLoaded', function() {
        let revenueChart = null;
        let expenseChart = null;

        const colors = [
            'rgb(59, 130, 246)', // Blue
            'rgb(16, 185, 129)', // Emerald
            'rgb(139, 92, 246)', // Violet
            'rgb(245, 158, 11)', // Amber
            'rgb(236, 72, 153)', // Pink
            'rgb(107, 114, 128)'  // Gray
        ];

        function createLegend(elementId, labels, data, colors, percentages, extraData = []) {
            const legendElement = document.getElementById(elementId);
            if (!legendElement) return;

            legendElement.innerHTML = labels.map((label, index) => `
                <div class="flex items-center justify-between group cursor-default">
                    <div class="flex items-center gap-2">
                        <div class="w-1.5 h-1.5 rounded-full" style="background-color: ${colors[index]}"></div>
                        <span class="text-[11px] font-semibold text-gray-600 group-hover:text-gray-900 transition-colors">${label}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold text-gray-400 tracking-tighter">${percentages[index]}%</span>
                        <span class="text-[11px] font-black text-gray-800">Rp ${new Intl.NumberFormat('id-ID').format(data[index])}</span>
                    </div>
                </div>
            `).join('');
        }

        function loadRevenueChart(period = 'month') {
            fetch(`{{ route('finance.revenue-chart') }}?period=${period}`)
                .then(res => res.json())
                .then(data => {
                    const ctx = document.getElementById('revenueChart');
                    if (!ctx) return;
                    if (revenueChart) revenueChart.destroy();

                    if (data.labels.length === 0) {
                        ctx.parentElement.innerHTML = '<div class="flex flex-col items-center justify-center h-full text-gray-300 italic text-xs"><i class="fas fa-chart-pie text-3xl mb-3 opacity-20"></i>No Data</div>';
                        return;
                    }

                    revenueChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                data: data.data,
                                backgroundColor: colors.slice(0, data.labels.length),
                                borderWidth: 4,
                                borderColor: '#ffffff',
                                hoverOffset: 4,
                                cutout: '75%'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#1f2937',
                                    titleFont: { size: 10, weight: 'bold' },
                                    bodyFont: { size: 10 },
                                    padding: 10,
                                    cornerRadius: 8,
                                    callbacks: {
                                        label: function(context) {
                                            const val = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(context.parsed);
                                            return ` ${val} (${data.percentages[context.dataIndex]}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });

                    createLegend('revenueLegend', data.labels, data.data, colors, data.percentages, data.totalSold);
                });
        }

        function loadExpenseChart(period = 'month') {
            fetch(`{{ route('finance.expense-chart') }}?period=${period}`)
                .then(res => res.json())
                .then(data => {
                    const ctx = document.getElementById('expenseChart');
                    if (!ctx) return;
                    if (expenseChart) expenseChart.destroy();

                    if (data.labels.length === 0) {
                        ctx.parentElement.innerHTML = '<div class="flex flex-col items-center justify-center h-full text-gray-300 italic text-xs"><i class="fas fa-receipt text-3xl mb-3 opacity-20"></i>No Data</div>';
                        return;
                    }

                    expenseChart = new Chart(ctx, {
                        type: 'polarArea',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                data: data.data,
                                backgroundColor: colors.map(c => c.replace('rgb', 'rgba').replace(')', ', 0.7)')),
                                borderJoinStyle: 'round'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: { r: { display: false } },
                            plugins: {
                                legend: { display: false }
                            }
                        }
                    });

                    createLegend('expenseLegend', data.labels, data.data, colors, data.percentages);
                });
        }

        // Listeners for filter changes
        document.getElementById('revenueChartPeriod').addEventListener('change', (e) => loadRevenueChart(e.target.value));
        document.getElementById('expenseChartPeriod').addEventListener('change', (e) => loadExpenseChart(e.target.value));

        // Initial Load
        loadRevenueChart('month');
        loadExpenseChart('month');
    });
</script>

<style>
    @keyframes fade-in-down {
        0% { opacity: 0; transform: translateY(-10px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-down { animation: fade-in-down 0.4s ease-out; }
    
    #balanceCarousel {
        will-change: transform;
    }
</style>
@endpush

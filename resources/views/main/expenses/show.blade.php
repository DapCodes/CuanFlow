@extends('layouts.app')

@section('title', 'Detail ' . ($expense->type == 'income' ? 'Pemasukan' : 'Pengeluaran') . ' - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-900 transition-colors">Dashboard</a>
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('expenses.index', ['type' => $expense->type]) }}" class="text-gray-400 hover:text-gray-900 transition-colors">{{ $expense->type == 'income' ? 'Pemasukan' : 'Pengeluaran' }}</a>
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Detail Transaksi</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900 leading-tight">
                    {{ $expense->type == 'income' ? 'Detail Pemasukan' : 'Detail Pengeluaran' }}
                </h1>
                <div class="mt-1 flex items-center gap-2">
                    <span class="text-[10px] font-black bg-gray-100 text-gray-500 px-2 py-0.5 rounded-md uppercase tracking-widest">{{ $expense->expense_number }}</span>
                    <span class="text-gray-300 font-bold">/</span>
                    <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">{{ \Carbon\Carbon::parse($expense->expense_date)->translatedFormat('d F Y') }}</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                 <a href="{{ route('expenses.index', ['type' => $expense->type]) }}" 
                   class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-600 font-black text-[10px] uppercase tracking-widest hover:bg-gray-50 transition-all active:scale-95 shadow-sm">
                    <span>Kembali</span>
                </a>
                
                @if($expense->status === 'approved')
                    <div class="hidden md:flex flex-col items-end">
                        <span class="inline-flex items-center text-[9px] font-black bg-cuan-green/10 text-cuan-green px-3 py-1.5 rounded-md uppercase tracking-widest">
                            Disetujui
                        </span>
                        @if($expense->approvedBy)
                            <p class="text-[8px] font-black text-gray-400 mt-1 uppercase tracking-widest italic">Acc: {{ $expense->approvedBy->name }}</p>
                        @endif
                    </div>
                @elseif($expense->status === 'rejected')
                     <div class="hidden md:flex flex-col items-end">
                        <span class="inline-flex items-center text-[9px] font-black bg-red-50 text-red-500 px-3 py-1.5 rounded-md uppercase tracking-widest">
                            Ditolak
                        </span>
                        @if($expense->approvedBy)
                            <p class="text-[8px] font-black text-gray-400 mt-1 uppercase tracking-widest italic">Oleh: {{ $expense->approvedBy->name }}</p>
                        @endif
                    </div>
                @else
                    <span class="inline-flex items-center text-[9px] font-black bg-amber-50 text-amber-500 px-3 py-1.5 rounded-md uppercase tracking-widest">
                        Pending Approval
                    </span>
                @endif
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Details --}}
            <div class="lg:col-span-2 space-y-6">
                <x-card-container>
                    <div class="px-8 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h3 class="text-[10px] font-black text-gray-900 uppercase tracking-widest">Informasi Transaksi</h3>
                        {{-- Mobile Status --}}
                         @if($expense->status === 'approved')
                            <span class="md:hidden inline-flex items-center text-[8px] font-black bg-cuan-green/10 text-cuan-green px-2 py-1 rounded uppercase tracking-widest">
                                Disetujui
                            </span>
                        @elseif($expense->status === 'rejected')
                             <span class="md:hidden inline-flex items-center text-[8px] font-black bg-red-50 text-red-500 px-2 py-1 rounded uppercase tracking-widest">
                                Ditolak
                            </span>
                        @endif
                    </div>
                    <div class="p-8 md:p-10 space-y-10">
                        
                        {{-- Nominal Hero --}}
                        <div class="flex flex-col items-center justify-center p-8 bg-gray-50 rounded-3xl border border-gray-100 border-dashed relative overflow-hidden group">
                            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:scale-110 transition-transform">
                                <i class="fas {{ $expense->type == 'income' ? 'fa-wallet' : 'fa-money-bill-wave' }} text-6xl"></i>
                            </div>
                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-3">Total Nominal</p>
                            <p class="text-4xl md:text-5xl font-black {{ $expense->type == 'income' ? 'text-cuan-green' : 'text-red-500' }}">
                                {{ $expense->type == 'income' ? '+' : '-' }} Rp {{ number_format(abs($expense->amount), 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <div class="space-y-3">
                                <h4 class="text-[10px] text-gray-400 font-black uppercase tracking-widest pl-1">Kategori</h4>
                                <div class="flex items-center gap-3 bg-gray-50/80 px-4 py-3 rounded-2xl border border-gray-100/50">
                                    <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-cuan-green">
                                        <i class="fas fa-tag text-xs"></i>
                                    </div>
                                    <span class="text-sm font-black text-gray-900 uppercase tracking-tight">{{ $expense->category->name ?? 'Uncategorized' }}</span>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <h4 class="text-[10px] text-gray-400 font-black uppercase tracking-widest pl-1">Metode Pembayaran</h4>
                                <div class="flex items-center gap-3 bg-gray-50/80 px-4 py-3 rounded-2xl border border-gray-100/50">
                                    <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-amber-500">
                                        <i class="fas fa-credit-card text-xs"></i>
                                    </div>
                                    <span class="text-sm font-black text-gray-900 uppercase tracking-tight">{{ $expense->payment_method }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <h4 class="text-[10px] text-gray-400 font-black uppercase tracking-widest pl-1">Deskripsi / Keperluan</h4>
                            <div class="bg-white rounded-3xl p-6 border-2 border-gray-50 leading-relaxed shadow-sm">
                                <p class="text-sm font-bold text-gray-700 italic">"{{ $expense->description }}"</p>
                            </div>
                        </div>

                        @if($expense->reference_number || $expense->notes)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 pt-8 border-t border-gray-100">
                            @if($expense->reference_number)
                            <div class="space-y-2">
                                <h4 class="text-[9px] text-gray-400 font-black uppercase tracking-widest">No. Referensi</h4>
                                <p class="text-xs font-black text-gray-900 tracking-wider">{{ $expense->reference_number }}</p>
                            </div>
                            @endif
                            @if($expense->notes)
                            <div class="space-y-2">
                                <h4 class="text-[9px] text-gray-400 font-black uppercase tracking-widest">Catatan</h4>
                                <p class="text-xs font-bold text-gray-500 italic">{{ $expense->notes }}</p>
                            </div>
                            @endif
                        </div>
                        @endif

                        <div class="flex flex-col sm:flex-row items-center justify-between gap-6 pt-10 border-t border-gray-100">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-gray-900 flex items-center justify-center text-white text-lg font-black shadow-lg shadow-gray-900/10">
                                    {{ substr($expense->creator->name ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest leading-none mb-1.5">Dibuat oleh</p>
                                    <p class="text-sm font-black text-gray-900 uppercase tracking-widest">{{ $expense->creator->name ?? 'Unknown' }}</p>
                                </div>
                            </div>
                            <div class="text-center sm:text-right bg-gray-50 px-4 py-2 rounded-xl border border-gray-100">
                                <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest mb-1">Waktu Transaksi</p>
                                <p class="text-xs font-black text-gray-900 uppercase tracking-widest">{{ $expense->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </x-card-container>
            </div>

            {{-- Sidebar (Evidence & Actions) --}}
            <div class="space-y-6">
                
                {{-- Approval Actions Card --}}
                @if($expense->status === 'pending')
                    @php
                        $approvalPermission = $expense->type == 'income' ? 'setujui pemasukan' : 'setujui pengeluaran';
                    @endphp
                    @can($approvalPermission)
                    <x-card-container class="!bg-gradient-to-br from-cuan-green/10 to-transparent border-cuan-green/10">
                        <div class="p-6 space-y-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-cuan-green text-white flex items-center justify-center shadow-lg shadow-cuan-green/20">
                                    <i class="fas fa-user-check text-xs"></i>
                                </div>
                                <div>
                                    <h4 class="text-[10px] font-black text-gray-900 uppercase tracking-widest leading-none mb-1">Persetujuan</h4>
                                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest leading-none">Menunggu Approval</p>
                                </div>
                            </div>

                            <p class="text-xs font-bold text-gray-500 leading-relaxed uppercase tracking-widest">Transaksi ini membutuhkan persetujuan Anda untuk diproses.</p>
                            
                            <div class="flex flex-col gap-3">
                                <form action="{{ route('expenses.approve', $expense->id) }}" method="POST" class="w-full">
                                    @csrf
                                    <button type="submit" class="w-full py-3.5 bg-black text-white font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-cuan-green transition-all active:scale-95 shadow-md shadow-gray-200">
                                        Setujui Sekarang
                                    </button>
                                </form>
                                <form action="{{ route('expenses.reject', $expense->id) }}" method="POST" class="w-full">
                                    @csrf
                                    <button type="submit" class="w-full py-3 bg-white border-2 border-red-50 text-red-500 font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-red-500 hover:text-white transition-all active:scale-95 shadow-sm shadow-red-50" onclick="return confirm('Tolak transaksi ini?')">
                                        Tolak Transaksi
                                    </button>
                                </form>
                            </div>
                        </div>
                    </x-card-container>
                    @endcan
                @endif

                {{-- Evidence Card --}}
                <x-card-container>
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                        <i class="fas fa-receipt text-gray-300 text-xs"></i>
                        <h3 class="text-[10px] font-black text-gray-900 uppercase tracking-widest">Bukti Transaksi</h3>
                    </div>
                    <div class="p-6">
                        @if($expense->receipt_image)
                            <div class="relative group cursor-pointer w-full overflow-hidden rounded-3xl" onclick="window.open('{{ asset('storage/' . $expense->receipt_image) }}', '_blank')">
                                <img src="{{ asset('storage/' . $expense->receipt_image) }}" alt="Receipt" 
                                    class="w-full h-auto object-cover rounded-3xl shadow-xl border-2 border-gray-50 transition-all group-hover:scale-105 duration-500">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-[10px] font-black uppercase tracking-widest">
                                    <i class="fas fa-search-plus mr-2"></i> Perbesar
                                </div>
                            </div>
                            <a href="{{ asset('storage/' . $expense->receipt_image) }}" download class="mt-4 w-full py-3 flex items-center justify-center gap-2 text-[10px] font-black text-gray-500 uppercase tracking-widest bg-gray-50 hover:bg-gray-100 rounded-xl transition-all border border-gray-200/50 active:scale-95">
                                <i class="fas fa-download text-xs"></i> Unduh Gambar
                            </a>
                        @else
                            <div class="w-full py-12 bg-gray-50 rounded-[2rem] border-2 border-dashed border-gray-200 flex flex-col items-center justify-center text-gray-400">
                                <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-4">
                                    <i class="fas fa-image text-gray-200 text-lg"></i>
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-widest">Tanpa Bukti</span>
                            </div>
                        @endif
                    </div>
                </x-card-container>
                
                {{-- Manage Actions Card --}}
                @php
                    $editPermission = $expense->type == 'income' ? 'edit pemasukan' : 'edit pengeluaran';
                    $deletePermission = $expense->type == 'income' ? 'hapus pemasukan' : 'hapus pengeluaran';
                @endphp
                
                @if($expense->status === 'pending' || auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin'))
                    @if(auth()->user()->can($editPermission) || auth()->user()->can($deletePermission))
                    <x-card-container class="p-6 space-y-4">
                         <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest pl-1">Kelola</h4>
                         <div class="flex flex-col gap-3">
                            @can($editPermission)
                            <a href="{{ route('expenses.edit', $expense->id) }}" class="w-full py-3 bg-amber-50 text-amber-500 font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-amber-500 hover:text-white transition-all text-center flex items-center justify-center gap-2 shadow-sm shadow-amber-50">
                                <span>Edit Data</span>
                            </a>
                            @endcan

                            @can($deletePermission)
                            <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="w-full" onsubmit="return confirm('Apakah Anda yakin?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full py-3 bg-red-50 text-red-500 font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-red-500 hover:text-white transition-all text-center flex items-center justify-center gap-2 shadow-sm shadow-red-50">
                                    <span>Hapus Data</span>
                                </button>
                            </form>
                            @endcan
                         </div>
                    </x-card-container>
                    @endif
                @endif

            </div>
        </div>

    </div>
</main>
@endsection

@extends('layouts.app')

@section('title', 'Detail ' . ($expense->type == 'income' ? 'Pemasukan' : 'Pengeluaran') . ' - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-500">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
    </a>
    <svg class="w-4 h-4 text-gray-300 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
    <a href="{{ route('expenses.index', ['type' => $expense->type]) }}" class="text-gray-400 hover:text-gray-500">{{ $expense->type == 'income' ? 'Pemasukan' : 'Pengeluaran' }}</a>
    <svg class="w-4 h-4 text-gray-300 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
    <span class="text-gray-900 font-medium">Detail Transaksi</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER CARD --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl {{ $expense->type == 'income' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-red-50 text-red-600 border border-red-100' }}">
                    <i class="fas {{ $expense->type == 'income' ? 'fa-wallet' : 'fa-money-bill-wave' }} text-xl"></i>
                </span>
                <div>
                    <h1 class="text-xl font-bold text-gray-900">{{ $expense->type == 'income' ? 'Detail Pemasukan' : 'Detail Pengeluaran' }}</h1>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-sm text-gray-500 font-mono bg-gray-100 px-2 py-0.5 rounded text-xs">{{ $expense->expense_number }}</span>
                        <span class="text-gray-300">|</span>
                        <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($expense->expense_date)->translatedFormat('l, d F Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                 <a href="{{ route('expenses.index', ['type' => $expense->type]) }}" 
                   class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-all">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
                
                @if($expense->status === 'approved')
                    <div class="hidden md:flex flex-col items-end">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                            <i class="fas fa-check-circle"></i> Disetujui
                        </span>
                        @if($expense->approvedBy)
                            <p class="text-[10px] text-gray-400 mt-1">Oleh: {{ $expense->approvedBy->name }}</p>
                        @endif
                    </div>
                @elseif($expense->status === 'rejected')
                     <div class="hidden md:flex flex-col items-end">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold bg-red-50 text-red-700 border border-red-100">
                            <i class="fas fa-times-circle"></i> Ditolak
                        </span>
                        @if($expense->approvedBy)
                            <p class="text-[10px] text-gray-400 mt-1">Oleh: {{ $expense->approvedBy->name }}</p>
                        @endif
                    </div>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold bg-yellow-50 text-yellow-700 border border-yellow-100">
                        <i class="fas fa-clock"></i> Pending Approval
                    </span>
                @endif
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Details --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 font-semibold text-gray-700 flex justify-between items-center">
                        <span>Informasi Transaksi</span>
                        {{-- Mobile Status --}}
                         @if($expense->status === 'approved')
                            <span class="md:hidden inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                <i class="fas fa-check-circle"></i> Disetujui
                            </span>
                        @elseif($expense->status === 'rejected')
                             <span class="md:hidden inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-100">
                                <i class="fas fa-times-circle"></i> Ditolak
                            </span>
                        @endif
                    </div>
                    <div class="p-6 md:p-8 space-y-8">
                        
                        {{-- Nominal Hero --}}
                        <div class="flex flex-col items-center justify-center p-6 bg-gray-50 rounded-xl border border-gray-100 border-dashed">
                            <p class="text-xs text-gray-500 uppercase tracking-wider font-bold mb-2">Total Nominal</p>
                            <p class="text-3xl md:text-4xl font-black {{ $expense->type == 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $expense->type == 'income' ? '+' : '-' }} Rp {{ number_format(abs($expense->amount), 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <h4 class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-2">Kategori</h4>
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                        <i class="fas fa-tag text-sm"></i>
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $expense->category->name ?? 'Uncategorized' }}</span>
                                </div>
                            </div>

                            <div>
                                <h4 class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-2">Metode Pembayaran</h4>
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                                        <i class="fas fa-credit-card text-sm"></i>
                                    </div>
                                    <span class="font-medium text-gray-900 capitalize">{{ $expense->payment_method }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-2">Deskripsi / Keperluan</h4>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                                <p class="text-gray-700 leading-relaxed">{{ $expense->description }}</p>
                            </div>
                        </div>

                        @if($expense->reference_number || $expense->notes)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t border-gray-100">
                            @if($expense->reference_number)
                            <div>
                                <h4 class="text-[10px] text-gray-400 uppercase tracking-wider font-bold mb-1">No. Referensi</h4>
                                <p class="text-sm font-mono text-gray-700">{{ $expense->reference_number }}</p>
                            </div>
                            @endif
                            @if($expense->notes)
                            <div>
                                <h4 class="text-[10px] text-gray-400 uppercase tracking-wider font-bold mb-1">Catatan</h4>
                                <p class="text-sm text-gray-600 italic">"{{ $expense->notes }}"</p>
                            </div>
                            @endif
                        </div>
                        @endif

                        <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-sm">
                                    {{ substr($expense->creator->name ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Dibuat oleh</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $expense->creator->name ?? 'Unknown' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">Waktu dibuat</p>
                                <p class="text-sm font-medium text-gray-900">{{ $expense->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar (Evidence & Actions) --}}
            <div class="space-y-6">
                
                {{-- Approval Actions Card --}}
                @if($expense->status === 'pending')
                    @php
                        $approvalPermission = $expense->type == 'income' ? 'setujui pemasukan' : 'setujui pengeluaran';
                    @endphp
                    @can($approvalPermission)
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-blue-100 font-semibold text-blue-800 flex items-center gap-2">
                            <i class="fas fa-user-check"></i>
                            Persetujuan Diperlukan
                        </div>
                        <div class="p-6 space-y-4">
                            <p class="text-sm text-blue-700">Transaksi ini membutuhkan persetujuan Anda untuk diproses.</p>
                            <div class="grid grid-cols-2 gap-3">
                                <form action="{{ route('expenses.reject', $expense->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full py-2.5 px-4 bg-white border border-red-200 text-red-600 rounded-lg hover:bg-red-50 hover:border-red-300 font-semibold transition-all flex items-center justify-center gap-2 shadow-sm" onclick="return confirm('Tolak transaksi ini?')">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
                                </form>
                                <form action="{{ route('expenses.approve', $expense->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 border border-emerald-600 text-white rounded-lg hover:bg-emerald-700 hover:border-emerald-700 font-semibold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2" onclick="return confirm('Setujui transaksi ini?')">
                                        <i class="fas fa-check"></i> Setujui
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endcan
                @endif

                {{-- Evidence Card --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-receipt text-gray-400"></i>
                        Bukti Transaksi
                    </div>
                    <div class="p-6 flex flex-col items-center justify-center">
                        @if($expense->receipt_image)
                            <div class="relative group cursor-pointer w-full" onclick="window.open('{{ asset('storage/' . $expense->receipt_image) }}', '_blank')">
                                <img src="{{ asset('storage/' . $expense->receipt_image) }}" alt="Receipt" 
                                    class="w-full max-h-64 object-cover rounded-lg shadow-sm border border-gray-200 transition-transform group-hover:scale-[1.02]">
                                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center text-white font-medium">
                                    <i class="fas fa-search-plus mr-2"></i> Lihat Penuh
                                </div>
                            </div>
                            <a href="{{ asset('storage/' . $expense->receipt_image) }}" download class="mt-4 w-full py-2 flex items-center justify-center gap-2 text-sm text-gray-600 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg transition-colors">
                                <i class="fas fa-download"></i> Unduh Gambar
                            </a>
                        @else
                            <div class="w-full h-40 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200 flex flex-col items-center justify-center text-gray-400">
                                <i class="fas fa-image text-3xl mb-2 opacity-50"></i>
                                <span class="text-sm">Tidak ada bukti gambar</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                {{-- Manage Actions Card --}}
                @php
                    $editPermission = $expense->type == 'income' ? 'edit pemasukan' : 'edit pengeluaran';
                    $deletePermission = $expense->type == 'income' ? 'hapus pemasukan' : 'hapus pengeluaran';
                @endphp
                
                @if($expense->status === 'pending' || auth()->user()->hasRole('owner') || auth()->user()->hasRole('admin'))
                    @if(auth()->user()->can($editPermission) || auth()->user()->can($deletePermission))
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                         <h4 class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-4">Kelola Data</h4>
                         <div class="flex flex-col gap-3">
                            @can($editPermission)
                            <a href="{{ route('expenses.edit', $expense->id) }}" class="w-full py-2.5 px-4 bg-white border border-yellow-300 text-yellow-700 rounded-lg hover:bg-yellow-50 font-semibold transition-colors text-center shadow-sm">
                                <i class="fas fa-edit mr-2"></i> Edit Data
                            </a>
                            @endcan

                            @can($deletePermission)
                            <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini secara permanen?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full py-2.5 px-4 bg-white border border-red-200 text-red-600 rounded-lg hover:bg-red-50 font-semibold transition-colors text-center shadow-sm">
                                    <i class="fas fa-trash-alt mr-2"></i> Hapus Data
                                </button>
                            </form>
                            @endcan
                         </div>
                    </div>
                    @endif
                @endif

            </div>
        </div>

    </div>
</main>
@endsection

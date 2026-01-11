@extends('layouts.app')

@section('title', 'Stock Opname - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Stock Opname</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-check-circle mt-0.5 text-green-500"></i>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-exclamation-circle mt-0.5 text-red-500"></i>
                <p class="text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        {{-- HEADER HALAMAN --}}
        <section
            class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100">
                        <i class="fas fa-clipboard-check text-sm"></i>
                    </span>
                    <span>Stock Opname</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Lakukan pencocokan stok fisik dan sistem secara berkala untuk menjaga akurasi inventaris.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                @can('buat stock opname')
                <a href="{{ route('stock-opname.create') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1 shadow-sm transition-all">
                    <i class="fas fa-plus text-sm"></i>
                    <span>Mulai Opname Baru</span>
                </a>
                @endcan
            </div>
        </section>

        {{-- RINGKASAN STATISTIK --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Opname</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div
                        class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100">
                        <i class="fas fa-list text-gray-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Selesai</p>
                        <p class="mt-1 text-2xl font-semibold text-emerald-600">{{ $stats['completed'] }}</p>
                    </div>
                    <div
                        class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100">
                        <i class="fas fa-check-double text-emerald-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Sedang Proses</p>
                        <p class="mt-1 text-2xl font-semibold text-yellow-600">{{ $stats['in_progress'] }}</p>
                    </div>
                    <div
                        class="w-10 h-10 rounded-lg bg-yellow-50 flex items-center justify-center border border-yellow-100">
                        <i class="fas fa-spinner text-yellow-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Draft</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-600">{{ $stats['draft'] }}</p>
                    </div>
                    <div
                        class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center border border-gray-200">
                        <i class="fas fa-file-alt text-gray-400 text-lg"></i>
                    </div>
                </div>
            </div>
        </section>

        {{-- KONTEN UTAMA: TOOLBAR + TABEL --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            {{-- Toolbar: Search & Filter --}}
            <form action="{{ route('stock-opname.index') }}" method="GET" class="border-b border-gray-200 px-4 md:px-6 py-4 space-y-3 md:space-y-0 md:flex md:items-center md:justify-between gap-4">
                <div class="w-full md:max-w-md">
                    <label class="text-xs font-medium text-gray-500 mb-1 block">Cari Opname</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nomor Stock Opname..."
                               class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-shadow">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 w-full md:w-auto">
                    <div class="w-full sm:w-40 md:w-44">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Status</label>
                        <select name="status" onchange="this.form.submit()"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Semua Status</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Sedang Proses</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                </div>
            </form>

            {{-- Tabel --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Nomor Opname</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Dibuat Oleh</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal Mulai</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Waktu Selesai</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($stockOpnames as $opname)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <span class="font-mono font-medium text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md text-xs border border-emerald-100">
                                        {{ $opname->opname_number }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-gray-700">
                                    @if($opname->type == 'product')
                                        Produk Jadi
                                    @elseif($opname->type == 'raw_material')
                                        Bahan Baku
                                    @else
                                        Semua
                                    @endif
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    @if($opname->status == 'completed')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            Selesai
                                        </span>
                                    @elseif($opname->status == 'in_progress')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Sedang Proses
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Draft
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-gray-600">
                                    {{ $opname->createdBy->name ?? '-' }}
                                </td>
                                <td class="px-6 py-3 text-gray-600">
                                    {{ $opname->started_at ? $opname->started_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-3 text-gray-600">
                                    {{ $opname->completed_at ? $opname->completed_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        @php
                                            $canEdit = auth()->user()->can('edit stock opname');
                                            $canView = auth()->user()->can('lihat stock opname');
                                            $isCompleted = $opname->status == 'completed';
                                        @endphp

                                        @if($isCompleted && $canView)
                                            <a href="{{ route('stock-opname.show', $opname->id) }}"
                                               class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-emerald-600 transition-colors"
                                               title="Lihat Detail">
                                                <i class="fas fa-eye text-xs"></i>
                                            </a>
                                        @elseif(!$isCompleted && $canEdit)
                                            <a href="{{ route('stock-opname.show', $opname->id) }}"
                                               class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-emerald-600 transition-colors"
                                               title="Lanjutkan Opname">
                                                <i class="fas fa-edit text-xs"></i>
                                            </a>
                                        @elseif(!$isCompleted && $canView)
                                             <a href="{{ route('stock-opname.show', $opname->id) }}"
                                               class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-emerald-600 transition-colors"
                                               title="Lihat Detail">
                                                <i class="fas fa-eye text-xs"></i>
                                            </a>
                                        @endif
                                        @if($opname->status != 'completed')
                                        @can('hapus stock opname')
                                        <form action="{{ route('stock-opname.destroy', $opname->id) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Yakin ingin menghapus sesi stock opname ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
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
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-center">
                                        <div
                                            class="w-20 h-20 rounded-full bg-emerald-50 flex items-center justify-center mb-4 border border-emerald-100">
                                            <i class="fas fa-clipboard-list text-3xl text-emerald-300"></i>
                                        </div>
                                        <h3 class="text-base font-semibold text-gray-900 mb-1">Belum ada riwayat Opname</h3>
                                        <p class="text-sm text-gray-500 mb-4 max-w-sm">
                                            Mulai lakukan stock opname untuk memastikan data stok di sistem sesuai dengan fisik di gudang.
                                        </p>
                                        <a href="{{ route('stock-opname.create') }}"
                                           class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 shadow-sm transition-all">
                                            <i class="fas fa-plus text-xs"></i>
                                            Mulai Stock Opname
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($stockOpnames->hasPages())
                <div class="px-4 md:px-6 py-3 border-t border-gray-200">
                    {{ $stockOpnames->links() }}
                </div>
            @endif
        </section>
    </div>
</main>
@endsection

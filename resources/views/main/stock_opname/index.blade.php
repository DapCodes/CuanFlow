@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Stock Opname - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Stock Opname</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Stock Opname
                </h1>
                <p class="mt-1 text-sm text-gray-500 font-medium">
                    Pencocokan stok fisik dan sistem secara berkala untuk akurasi inventaris.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @can('buat stock opname')
                <a href="{{ route('stock-opname.create') }}"
                   class="inline-flex items-center justify-center h-11 px-6 bg-cuan-green text-white rounded-xl text-sm font-black hover:bg-cuan-dark transition-all active:scale-95 shadow-lg shadow-cuan-green/20">
                    Sesi Opname Baru
                </a>
                @endcan
            </div>
        </section>

        {{-- RINGKASAN STATISTIK --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-card-container>
                <div class="p-5 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Opname</p>
                        <p class="mt-1 text-2xl font-black text-gray-900">{{ number_format($stats['total']) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center border border-gray-100">
                        <i class="fas fa-list text-gray-400"></i>
                    </div>
                </div>
            </x-card-container>

            <x-card-container>
                <div class="p-5 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Selesai</p>
                        <p class="mt-1 text-2xl font-black text-cuan-green">{{ number_format($stats['completed']) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-cuan-green/10 flex items-center justify-center border border-cuan-green/10">
                        <i class="fas fa-check-double text-cuan-green"></i>
                    </div>
                </div>
            </x-card-container>

            <x-card-container>
                <div class="p-5 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Sedang Proses</p>
                        <p class="mt-1 text-2xl font-black text-yellow-600">{{ number_format($stats['in_progress']) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-yellow-50 flex items-center justify-center border border-yellow-100">
                        <i class="fas fa-spinner text-yellow-500"></i>
                    </div>
                </div>
            </x-card-container>

            <x-card-container>
                <div class="p-5 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Draft</p>
                        <p class="mt-1 text-2xl font-black text-gray-600">{{ number_format($stats['draft']) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center border border-gray-200">
                        <i class="fas fa-file-alt text-gray-400"></i>
                    </div>
                </div>
            </x-card-container>
        </section>

        {{-- KONTEN UTAMA: TOOLBAR + TABEL --}}
        <x-card-container>
            {{-- Toolbar: Search & Filter --}}
            <form id="filter-form" action="{{ route('stock-opname.index') }}" method="GET" class="border-b border-gray-100 px-6 py-5 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div class="flex-grow max-w-lg space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Cari Opname</label>
                    <div class="relative">
                        <input type="text" name="search" id="search-input" value="{{ request('search') }}" placeholder="Nomor Stock Opname..."
                               class="w-full pl-11 pr-4 py-3 rounded-2xl border border-gray-200 text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/5 focus:border-cuan-green transition-all">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    </div>
                </div>

                <div class="w-full md:w-48 space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Status</label>
                    <div class="relative">
                        <select name="status" id="status-select"
                                class="w-full appearance-none rounded-2xl border border-gray-200 px-4 py-3 text-sm font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/5 focus:border-cuan-green transition-all bg-white">
                            <option value="">Semua Status</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Sedang Proses</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 text-[10px] pointer-events-none"></i>
                    </div>
                </div>
            </form>

            {{-- Tabel --}}
            <div id="table-container">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left">No. Opname</th>
                                <th class="px-6 py-4 text-left">Tipe</th>
                                <th class="px-6 py-4 text-left">Dibuat Oleh</th>
                                <th class="px-6 py-4 text-left text-center">Tanggal</th>
                                <th class="px-6 py-4 text-left">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($stockOpnames as $opname)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <span class="text-[10px] font-black font-mono text-gray-500 bg-gray-100 px-2 py-1 rounded-lg border border-gray-100 uppercase tracking-tighter">
                                            {{ $opname->opname_number }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        @if($opname->type == 'product')
                                            <span class="text-[11px] font-bold text-purple-600 bg-purple-50 px-2 py-0.5 rounded-lg border border-purple-100">Produk Jadi</span>
                                        @elseif($opname->type == 'raw_material')
                                             <span class="text-[11px] font-bold text-orange-600 bg-orange-50 px-2 py-0.5 rounded-lg border border-orange-100">Bahan Baku</span>
                                        @else
                                            <span class="text-[11px] font-bold text-gray-600 bg-gray-50 px-2 py-0.5 rounded-lg border border-gray-200">Semua</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="text-[11px] font-bold text-gray-900 capitalize">{{ $opname->createdBy->name ?? '-' }}</div>
                                        <div class="text-[9px] font-black uppercase tracking-widest text-gray-400 mt-0.5">Penanggung Jawab</div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-center">
                                        <div class="text-[11px] font-bold text-gray-900">
                                            {{ $opname->started_at ? $opname->started_at->format('d M Y') : $opname->created_at->format('d M Y') }}
                                        </div>
                                        <div class="text-[9px] font-black uppercase tracking-widest text-gray-400 mt-0.5">
                                            {{ $opname->started_at ? $opname->started_at->format('H:i') : 'Mulai' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        @php
                                            $statusClass = match($opname->status) {
                                                'completed' => 'bg-cuan-green/10 text-cuan-green border-cuan-green/10',
                                                'in_progress' => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                                                default => 'bg-gray-50 text-gray-400 border-gray-200'
                                            };
                                            $statusLabel = match($opname->status) {
                                                'completed' => 'Selesai',
                                                'in_progress' => 'Proses',
                                                default => 'Draft'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('stock-opname.show', $opname->id) }}" 
                                               class="w-9 h-9 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white transition-all active:scale-95 border border-blue-100"
                                               title="Lihat/Lanjutkan">
                                                <i class="fas {{ $opname->status == 'completed' ? 'fa-eye' : 'fa-play' }} text-xs"></i>
                                            </a>
                                            
                                            @if($opname->status != 'completed')
                                            @can('hapus stock opname')
                                            <form action="{{ route('stock-opname.destroy', $opname->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="confirm-delete w-9 h-9 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all active:scale-95 border border-red-100"
                                                        data-name="Sesi Opname #{{ $opname->opname_number }}"
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
                                    <td colspan="6" class="px-6 py-20 text-center">
                                        <div class="w-16 h-16 bg-gray-50 border border-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                            <i class="fas fa-clipboard-list text-gray-200 text-2xl"></i>
                                        </div>
                                        <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">Belum Ada Opname</h3>
                                        <p class="text-[11px] text-gray-500 font-bold uppercase tracking-widest mt-2 max-w-xs mx-auto">Riwayat stock opname akan tampil di sini setelah dibuat.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($stockOpnames->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $stockOpnames->links() }}
                    </div>
                @endif
            </div>
        </x-card-container>
    </div>
</main>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('filter-form');
        const searchInput = document.getElementById('search-input');
        const statusSelect = document.getElementById('status-select');
        const tableContainer = document.getElementById('table-container');

        let timeout = null;

        function fetchResults() {
            const url = new URL(form.action);
            const formData = new FormData(form);
            for (const [key, value] of formData.entries()) {
                if(value) url.searchParams.append(key, value);
            }

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTableContainer = doc.getElementById('table-container');
                if (newTableContainer) {
                    tableContainer.innerHTML = newTableContainer.innerHTML;
                }
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(timeout);
                timeout = setTimeout(fetchResults, 300);
            });
        }

        if (statusSelect) {
            statusSelect.addEventListener('change', fetchResults);
        }
    });
</script>
@endpush
@endsection

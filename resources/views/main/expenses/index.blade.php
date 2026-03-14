@extends('layouts.app')

@section('title', $title . ' - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-900 transition-colors">Dashboard</a>
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">{{ $title }}</span>
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
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900 leading-tight">
                    Kelola {{ $title }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 font-medium">
                    Kelola dan pantau data {{ strtolower($title) }} operasional outlet dengan mudah.
                </p>
            </div>
            
            @php
                $createPermission = $type == 'income' ? 'buat pemasukan' : 'buat pengeluaran';
            @endphp
            @can($createPermission)
            <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                <a href="{{ route('expenses.create', ['type' => $type]) }}" 
                   class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl {{ $type == 'income' ? 'bg-cuan-green hover:bg-cuan-dark' : 'bg-red-600 hover:bg-red-700' }} text-white font-black text-[10px] uppercase tracking-widest transition-all active:scale-95 shadow-lg {{ $type == 'income' ? 'shadow-cuan-green/20' : 'shadow-red-600/20' }}">
                    <span>Tambah {{ $title }}</span>
                </a>
            </div>
            @endcan
        </section>

        {{-- KONTEN UTAMA: TOOLBAR + TABEL --}}
        <x-card-container class="relative overflow-hidden" id="expenses-container">
            {{-- Loading Overlay --}}
            <div id="loading-overlay" class="absolute inset-0 bg-white/70 backdrop-blur-sm z-50 flex items-center justify-center hidden">
                <div class="flex flex-col items-center gap-4">
                    <div class="w-12 h-12 border-4 border-cuan-green/20 border-t-cuan-green rounded-full animate-spin"></div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest animate-pulse">Memuat Data...</span>
                </div>
            </div>
            {{-- Toolbar: Search & Filter --}}
            <div class="border-b border-gray-100 px-6 py-6 flex flex-col xl:flex-row xl:items-end justify-between gap-6">
                <div class="flex-grow max-w-xl">
                    <label for="searchInput" class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 block pl-1">Cari {{ strtolower($title) }}</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none text-gray-300 group-focus-within:text-cuan-green transition-colors">
                            <i class="fas fa-search text-xs"></i>
                        </div>
                        <input type="text" id="searchInput" value="{{ request('search') }}" placeholder="Cari deskripsi atau nomor..."
                            class="w-full pl-14 pr-6 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green focus:bg-white transition-all shadow-sm">
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-4">
                    {{-- Filter Kategori --}}
                    <div class="w-full sm:w-56">
                        <label for="categoryFilter" class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 block pl-1">Filter Kategori</label>
                        <select id="categoryFilter"
                                class="w-full px-5 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl text-sm font-bold text-gray-900 focus:outline-none focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green focus:bg-white transition-all appearance-none cursor-pointer">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Status --}}
                    <div class="w-full sm:w-56">
                        <label for="statusFilter" class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 block pl-1">Filter Status</label>
                        <select id="statusFilter"
                                class="w-full px-5 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl text-sm font-bold text-gray-900 focus:outline-none focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green focus:bg-white transition-all appearance-none cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Tanggal & Nomor
                            </th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Kategori / Deskripsi
                            </th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Nominal
                            </th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Status
                            </th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Oleh
                            </th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($expenses as $expense)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            {{-- Tanggal & Nomor --}}
                            <td class="px-8 py-4 whitespace-nowrap">
                                <p class="text-xs font-black text-gray-900 leading-none">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</p>
                                <p class="mt-1.5 inline-flex text-[9px] font-black bg-gray-100 text-gray-500 px-2 py-0.5 rounded-md uppercase tracking-widest">
                                    {{ $expense->expense_number }}
                                </p>
                            </td>

                            {{-- Kategori & Deskripsi --}}
                            <td class="px-8 py-4">
                                <p class="text-xs font-black text-gray-900 leading-tight">{{ $expense->category->name ?? 'Uncategorized' }}</p>
                                <p class="text-[10px] font-bold text-gray-400 mt-1 line-clamp-1 italic">{{ $expense->description }}</p>
                            </td>

                            {{-- Nominal --}}
                            <td class="px-8 py-4 whitespace-nowrap">
                                <p class="text-sm font-black {{ $type == 'income' ? 'text-cuan-green' : 'text-red-500' }}">
                                    {{ $type == 'income' ? '+' : '-' }} Rp {{ number_format(abs($expense->amount), 0, ',', '.') }}
                                </p>
                                <p class="text-[9px] font-black text-gray-400 mt-1 uppercase tracking-widest">Via {{ $expense->payment_method }}</p>
                            </td>

                            {{-- Status --}}
                            <td class="px-8 py-4 whitespace-nowrap">
                                @if($expense->status === 'approved')
                                    <span class="inline-flex items-center text-[9px] font-black bg-cuan-green/10 text-cuan-green px-2.5 py-1 rounded-md uppercase tracking-widest">
                                        Disetujui
                                    </span>
                                @elseif($expense->status === 'rejected')
                                    <span class="inline-flex items-center text-[9px] font-black bg-red-50 text-red-500 px-2.5 py-1 rounded-md uppercase tracking-widest">
                                        Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-[9px] font-black bg-amber-50 text-amber-500 px-2.5 py-1 rounded-md uppercase tracking-widest">
                                        Pending
                                    </span>
                                @endif
                            </td>

                            {{-- Oleh --}}
                            <td class="px-8 py-4 whitespace-nowrap">
                                <p class="text-[10px] font-black text-gray-900 uppercase tracking-widest">{{ $expense->creator->name ?? '-' }}</p>
                                @if($expense->approvedBy)
                                    <p class="text-[8px] font-bold text-gray-400 mt-1 uppercase tracking-widest italic">
                                        Acc: {{ $expense->approvedBy->name }}
                                    </p>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-8 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('expenses.show', $expense->id) }}"
                                       class="w-8 h-8 rounded-xl bg-gray-50 text-gray-400 hover:bg-cuan-green hover:text-white transition-all flex items-center justify-center"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>

                                    @if($expense->status === 'pending')
                                        @php
                                            $approvalPermission = $type == 'income' ? 'setujui pemasukan' : 'setujui pengeluaran';
                                        @endphp
                                        @can($approvalPermission)
                                            <form action="{{ route('expenses.approve', $expense->id) }}" method="POST" class="inline confirm-approve">
                                                @csrf
                                                <button type="submit" 
                                                    class="w-8 h-8 rounded-xl bg-cuan-green/10 text-cuan-green hover:bg-cuan-green hover:text-white transition-all flex items-center justify-center"
                                                    title="Setujui">
                                                    <i class="fas fa-check text-xs"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('expenses.reject', $expense->id) }}" method="POST" class="inline confirm-reject">
                                                @csrf
                                                <button type="submit" 
                                                    class="w-8 h-8 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center"
                                                    title="Tolak">
                                                    <i class="fas fa-times text-xs"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    @endif

                                    @php
                                        $editPermission = $type == 'income' ? 'edit pemasukan' : 'edit pengeluaran';
                                        $deletePermission = $type == 'income' ? 'hapus pemasukan' : 'hapus pengeluaran';
                                    @endphp

                                    @if($expense->status === 'pending' || auth()->user()->hasRole('owner'))
                                        @can($editPermission)
                                        <a href="{{ route('expenses.edit', $expense->id) }}"
                                           class="w-8 h-8 rounded-xl bg-amber-50 text-amber-500 hover:bg-amber-500 hover:text-white transition-all flex items-center justify-center"
                                           title="Edit">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        @endcan

                                        @can($deletePermission)
                                        <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="inline confirm-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="w-8 h-8 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center"
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
                            <td colspan="6" class="px-8 py-24 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-50 rounded-[2rem] flex items-center justify-center border border-dashed border-gray-200 mb-6">
                                        <i class="fas {{ $type == 'income' ? 'fa-wallet' : 'fa-money-bill-wave' }} text-3xl text-gray-200"></i>
                                    </div>
                                    <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest">Belum ada data {{ strtolower($title) }}</h3>
                                    <p class="text-[10px] font-bold text-gray-400 mt-2 max-w-sm mx-auto leading-relaxed uppercase tracking-widest">
                                        Silakan tambah data baru untuk mencatat pengeluaran operasional Anda.
                                    </p>
                                    @can($createPermission)
                                    <a href="{{ route('expenses.create', ['type' => $type]) }}"
                                       class="mt-8 inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-black text-white font-black text-[10px] uppercase tracking-widest hover:bg-cuan-green transition-all active:scale-95 shadow-lg shadow-gray-900/10">
                                        Tambah {{ $title }} Pertama
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
                <div class="px-8 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $expenses->links() }}
                </div>
            @endif
        </x-card-container>

    </div>
</main>
@push('scripts')
<script>
    const container = document.getElementById('expenses-container');
    const loadingOverlay = document.getElementById('loading-overlay');
    let debounceTimer;

    function toggleLoading(show) {
        if (loadingOverlay) {
            if (show) loadingOverlay.classList.remove('hidden');
            else loadingOverlay.classList.add('hidden');
        }
    }

    async function fetchData(url) {
        toggleLoading(true);
        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (!response.ok) throw new Error('Network response was not ok');
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContent = doc.getElementById('expenses-container');
            
            if (newContent && container) {
                container.innerHTML = newContent.innerHTML;
                bindEvents();
                window.history.pushState({}, '', url);
            }
        } catch (error) {
            console.error('Error fetching data:', error);
        } finally {
            toggleLoading(false);
        }
    }

    function triggerUpdate(urlObj = null) {
        const url = urlObj || new URL(window.location.href);
        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        const statusFilter = document.getElementById('statusFilter');

        // Maintain type
        url.searchParams.set('type', '{{ $type }}');

        if (searchInput && searchInput.value) {
            url.searchParams.set('search', searchInput.value);
        } else {
            url.searchParams.delete('search');
        }

        if (categoryFilter && categoryFilter.value) {
            url.searchParams.set('category_id', categoryFilter.value);
        } else {
            url.searchParams.delete('category_id');
        }

        if (statusFilter && statusFilter.value) {
            url.searchParams.set('status', statusFilter.value);
        } else {
            url.searchParams.delete('status');
        }

        if (!urlObj) {
            url.searchParams.delete('page');
        }

        fetchData(url.toString());
    }

    function bindEvents() {
        const currentSearchInput = document.getElementById('searchInput');
        if (currentSearchInput) {
            currentSearchInput.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => triggerUpdate(), 500);
            });
        }

        const currentCategoryFilter = document.getElementById('categoryFilter');
        if (currentCategoryFilter) {
            currentCategoryFilter.addEventListener('change', () => triggerUpdate());
        }

        const currentStatusFilter = document.getElementById('statusFilter');
        if (currentStatusFilter) {
            currentStatusFilter.addEventListener('change', () => triggerUpdate());
        }

        document.querySelectorAll('.pagination a, a.ajax-pagination').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                fetchData(link.getAttribute('href'));
            });
        });
    }

    document.addEventListener('DOMContentLoaded', bindEvents);
</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'Kelola Meja - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Kelola Meja</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Alert / Notifikasi --}}
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
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-amber-50 text-amber-600 border border-amber-100">
                        <i class="fas fa-chair text-sm"></i>
                    </span>
                    <span>Kelola Meja</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Atur meja outlet Anda untuk sistem penomoran saat pembayaran.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                @can('buat meja')
                <a href="{{ route('tables.create') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-1">
                    <i class="fas fa-plus-circle text-sm"></i>
                    <span>Tambah Meja</span>
                </a>
                @endcan
            </div>
        </section>

        {{-- RINGKASAN STATISTIK --}}
        <section class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Meja</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div
                        class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100">
                        <i class="fas fa-chair text-gray-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Tersedia</p>
                        <p class="mt-1 text-2xl font-semibold text-emerald-600">{{ $stats['available'] }}</p>
                    </div>
                    <div
                        class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100">
                        <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Terisi</p>
                        <p class="mt-1 text-2xl font-semibold text-red-600">{{ $stats['occupied'] }}</p>
                    </div>
                    <div
                        class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center border border-red-100">
                        <i class="fas fa-user text-red-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Dipesan</p>
                        <p class="mt-1 text-2xl font-semibold text-yellow-600">{{ $stats['reserved'] }}</p>
                    </div>
                    <div
                        class="w-10 h-10 rounded-lg bg-yellow-50 flex items-center justify-center border border-yellow-100">
                        <i class="fas fa-clock text-yellow-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Maintenance</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-600">{{ $stats['maintenance'] }}</p>
                    </div>
                    <div
                        class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100">
                        <i class="fas fa-wrench text-gray-500 text-lg"></i>
                    </div>
                </div>
            </div>
        </section>

        {{-- KONTEN UTAMA: TOOLBAR + GRID/TABEL --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            {{-- Toolbar: Search & Filter --}}
            <div class="border-b border-gray-200 px-4 md:px-6 py-4 space-y-3 md:space-y-0 md:flex md:items-center md:justify-between gap-4">
                <div class="w-full md:max-w-md">
                    <label class="text-xs font-medium text-gray-500 mb-1 block">Cari meja</label>
                    <div class="relative">
                        <input type="text" id="searchTable" placeholder="Cari berdasarkan nomor, nama, atau kode..."
                               class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 w-full md:w-auto">
                    <div class="w-full sm:w-40 md:w-44">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Status</label>
                        <select id="filterStatus"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400">
                            <option value="">Semua Status</option>
                            <option value="available">Tersedia</option>
                            <option value="occupied">Terisi</option>
                            <option value="reserved">Dipesan</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>

                    @if($locations->isNotEmpty())
                    <div class="w-full sm:w-40 md:w-44">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Lokasi</label>
                        <select id="filterLocation"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400">
                            <option value="">Semua Lokasi</option>
                            @foreach($locations as $location)
                                <option value="{{ $location }}">{{ $location }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Grid View --}}
            <div class="p-4 md:p-6">
                @if($tables->isEmpty())
                    <div class="flex flex-col items-center justify-center text-center py-16">
                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                            <i class="fas fa-chair text-3xl text-gray-300"></i>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900 mb-1">Belum ada meja</h3>
                        <p class="text-sm text-gray-500 mb-4 max-w-sm">
                            Mulai tambahkan meja untuk mengatur penomoran di outlet Anda.
                        </p>
                        <a href="{{ route('tables.create') }}"
                           class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-600">
                            <i class="fas fa-plus-circle text-xs"></i>
                            Tambah Meja
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4" id="tableGrid">
                        @foreach($tables as $table)
                            <div class="table-card rounded-xl p-4 relative group hover:shadow-lg transition-all duration-300
                                @if($table->status === 'available') bg-emerald-50 border-2 border-emerald-200
                                @elseif($table->status === 'occupied') bg-red-50 border-2 border-red-200
                                @elseif($table->status === 'reserved') bg-yellow-50 border-2 border-yellow-200
                                @else bg-gray-50 border-2 border-gray-200
                                @endif"
                                 data-name="{{ strtolower($table->name ?? '') }}"
                                 data-number="{{ strtolower($table->table_number) }}"
                                 data-code="{{ strtolower($table->code ?? '') }}"
                                 data-status="{{ $table->status }}"
                                 data-location="{{ strtolower($table->location ?? '') }}">
                                
                                {{-- Status Badge --}}
                                <div class="absolute top-2 right-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                                        @if($table->status === 'available') bg-emerald-100 text-emerald-700 border border-emerald-200
                                        @elseif($table->status === 'occupied') bg-red-100 text-red-700 border border-red-200
                                        @elseif($table->status === 'reserved') bg-yellow-100 text-yellow-700 border border-yellow-200
                                        @else bg-gray-100 text-gray-700 border border-gray-200
                                        @endif">
                                        {{ $table->getStatusLabel() }}
                                    </span>
                                </div>

                                {{-- Table Number --}}
                                <div class="text-center mb-3">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-2 border-2
                                        @if($table->status === 'available') bg-emerald-100 border-emerald-300
                                        @elseif($table->status === 'occupied') bg-red-100 border-red-300
                                        @elseif($table->status === 'reserved') bg-yellow-100 border-yellow-300
                                        @else bg-gray-100 border-gray-300
                                        @endif">
                                        <span class="text-lg font-bold
                                            @if($table->status === 'available') text-emerald-700
                                            @elseif($table->status === 'occupied') text-red-700
                                            @elseif($table->status === 'reserved') text-yellow-700
                                            @else text-gray-700
                                            @endif">{{ $table->table_number }}</span>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900 truncate">
                                        {{ $table->getDisplayName() }}
                                    </p>
                                    @if($table->location)
                                        <p class="text-xs text-gray-500 truncate">
                                            <i class="fas fa-map-marker-alt mr-1"></i>{{ $table->location }}
                                        </p>
                                    @endif
                                </div>

                                {{-- Capacity --}}
                                <div class="flex items-center justify-center gap-1 text-xs text-gray-600 mb-3">
                                    <i class="fas fa-users"></i>
                                    <span>{{ $table->capacity }} orang</span>
                                </div>

                                {{-- Quick Actions --}}
                                <div class="flex gap-2">
                                    @can('quick toggle meja')
                                    <button type="button" 
                                            onclick="quickToggleTable({{ $table->id }})"
                                            class="flex-1 px-2 py-1.5 rounded-lg text-xs font-medium transition-colors
                                                   {{ $table->isOccupied() 
                                                      ? 'bg-emerald-500 text-white hover:bg-emerald-600' 
                                                      : 'bg-red-500 text-white hover:bg-red-600' }}">
                                        <i class="fas fa-{{ $table->isOccupied() ? 'check' : 'user-plus' }} mr-1"></i>
                                        {{ $table->isOccupied() ? 'Kosongkan' : 'Isi' }}
                                    </button>
                                    @endcan
                                    
                                    @can('edit meja')
                                    <a href="{{ route('tables.edit', $table) }}"
                                       class="px-2 py-1.5 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 text-xs">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Pagination --}}
            @if($tables->hasPages())
                <div class="px-4 md:px-6 py-3 border-t border-gray-200">
                    {{ $tables->links() }}
                </div>
            @endif
        </section>
    </div>
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchTable');
    const filterStatus = document.getElementById('filterStatus');
    const filterLocation = document.getElementById('filterLocation');
    const tableCards = document.querySelectorAll('.table-card');

    function filterTables() {
        const searchTerm = (searchInput?.value || '').toLowerCase();
        const statusFilter = filterStatus?.value || '';
        const locationFilter = (filterLocation?.value || '').toLowerCase();

        tableCards.forEach(card => {
            const name = card.dataset.name || '';
            const number = card.dataset.number || '';
            const code = card.dataset.code || '';
            const status = card.dataset.status || '';
            const location = card.dataset.location || '';

            const matchesSearch = !searchTerm
                || name.includes(searchTerm)
                || number.includes(searchTerm)
                || code.includes(searchTerm);

            const matchesStatus = !statusFilter || status === statusFilter;
            const matchesLocation = !locationFilter || location.includes(locationFilter);

            card.style.display = (matchesSearch && matchesStatus && matchesLocation) ? '' : 'none';
        });
    }

    searchInput?.addEventListener('input', filterTables);
    filterStatus?.addEventListener('change', filterTables);
    filterLocation?.addEventListener('change', filterTables);
});

function quickToggleTable(tableId) {
    fetch(`/tables/${tableId}/quick-toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload page to reflect changes
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Gagal mengubah status meja');
    });
}
</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'Kelola Meja - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium">Kelola Meja</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-gray-900">
                    Kelola Meja
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Atur meja outlet Anda untuk sistem penomoran saat pembayaran.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                @can('buat meja')
                <a href="{{ route('tables.create') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-cuan-green px-4 py-2.5 text-sm font-semibold text-white hover:bg-cuan-dark transition-colors shadow-sm">
                    <i class="fas fa-plus text-xs"></i>
                    <span>Tambah Meja</span>
                </a>
                @endcan
            </div>
        </section>

        {{-- RINGKASAN STATISTIK --}}
        <section class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Total Meja</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Tersedia</p>
                <p class="mt-1 text-2xl font-bold text-emerald-600">{{ $stats['available'] }}</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Terisi</p>
                <p class="mt-1 text-2xl font-bold text-red-600">{{ $stats['occupied'] }}</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Dipesan</p>
                <p class="mt-1 text-2xl font-bold text-amber-500">{{ $stats['reserved'] }}</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Maintenance</p>
                <p class="mt-1 text-2xl font-bold text-gray-500">{{ $stats['maintenance'] }}</p>
            </div>
        </section>

        {{-- KONTEN UTAMA: TOOLBAR + GRID/TABEL --}}
        <x-card-container>
            {{-- Toolbar: Search & Filter --}}
            <div class="border-b border-gray-100 px-6 py-4 space-y-4 md:space-y-0 md:flex md:items-center md:justify-between gap-6 bg-gray-50/50">
                <div class="w-full md:max-w-md">
                    <div class="relative">
                        <input type="text" id="searchTable" placeholder="Cari nomor atau nama meja..."
                               class="w-full pl-4 pr-4 py-2.5 rounded-lg border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-cuan-green/20 focus:border-cuan-green transition-all">
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 w-full md:w-auto">
                    <select id="filterStatus"
                            class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-cuan-green/20 focus:border-cuan-green transition-all">
                        <option value="">Semua Status</option>
                        <option value="available">Tersedia</option>
                        <option value="occupied">Terisi</option>
                        <option value="reserved">Dipesan</option>
                        <option value="maintenance">Maintenance</option>
                    </select>

                    @if($locations->isNotEmpty())
                    <select id="filterLocation"
                            class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-cuan-green/20 focus:border-cuan-green transition-all">
                        <option value="">Semua Lokasi</option>
                        @foreach($locations as $location)
                            <option value="{{ $location }}">{{ $location }}</option>
                        @endforeach
                    </select>
                    @endif
                </div>
            </div>

            {{-- Grid View --}}
            <div class="p-6">
                @if($tables->isEmpty())
                    <div class="flex flex-col items-center justify-center text-center py-20">
                        <div class="w-20 h-20 rounded-full bg-gray-50 flex items-center justify-center mb-4 border border-gray-100">
                            <i class="fas fa-chair text-3xl text-gray-200"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Belum ada meja</h3>
                        <p class="text-sm text-gray-500 mb-6 max-w-sm">
                            Mulai tambahkan meja untuk mengatur penomoran di outlet Anda.
                        </p>
                        <a href="{{ route('tables.create') }}"
                           class="inline-flex items-center gap-2 rounded-lg bg-cuan-green px-6 py-3 text-sm font-bold text-white hover:bg-cuan-dark transition-all shadow-sm">
                            <i class="fas fa-plus text-xs"></i>
                            Tambah Meja Pertama
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6" id="tableGrid">
                        @foreach($tables as $table)
                            <div class="table-card rounded-2xl p-5 relative border transition-all duration-300 group
                                @if($table->status === 'available') bg-white border-gray-200 hover:border-emerald-300 hover:shadow-emerald-100/50
                                @elseif($table->status === 'occupied') bg-red-50/30 border-red-100 hover:border-red-200 hover:shadow-red-50/50
                                @elseif($table->status === 'reserved') bg-amber-50/30 border-amber-100 hover:border-amber-200 hover:shadow-amber-50/50
                                @else bg-gray-50/50 border-gray-200 hover:border-gray-300
                                @endif"
                                 data-name="{{ strtolower($table->name ?? '') }}"
                                 data-number="{{ strtolower($table->table_number) }}"
                                 data-code="{{ strtolower($table->code ?? '') }}"
                                 data-status="{{ $table->status }}"
                                 data-location="{{ strtolower($table->location ?? '') }}">
                                
                                {{-- Status Badge --}}
                                <div class="absolute top-3 right-3">
                                    <div class="w-2.5 h-2.5 rounded-full
                                        @if($table->status === 'available') bg-emerald-500
                                        @elseif($table->status === 'occupied') bg-red-500
                                        @elseif($table->status === 'reserved') bg-amber-500
                                        @else bg-gray-400
                                        @endif ring-4
                                        @if($table->status === 'available') ring-emerald-50
                                        @elseif($table->status === 'occupied') ring-red-50
                                        @elseif($table->status === 'reserved') ring-amber-50
                                        @else ring-gray-50
                                        @endif"></div>
                                </div>

                                {{-- Table Number & Info --}}
                                <div class="text-center">
                                    <span class="text-3xl font-black block mb-1
                                        @if($table->status === 'available') text-gray-900
                                        @elseif($table->status === 'occupied') text-red-700
                                        @elseif($table->status === 'reserved') text-amber-700
                                        @else text-gray-500
                                        @endif">{{ $table->table_number }}</span>
                                    
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-tighter truncate px-1">
                                        {{ $table->name ?: 'Meja ' . $table->table_number }}
                                    </h4>
                                    
                                    @if($table->location)
                                        <p class="text-[10px] text-gray-400 mt-1 truncate">
                                            {{ $table->location }}
                                        </p>
                                    @endif
                                </div>

                                {{-- Footer Info --}}
                                <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-center gap-2 text-[10px] font-bold text-gray-400">
                                    <i class="fas fa-users text-[8px]"></i>
                                    <span>CAP {{ $table->capacity }}</span>
                                </div>

                                {{-- Hover Actions --}}
                                <div class="absolute inset-0 bg-white/95 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center p-4 gap-2 z-10">
                                    @can('quick toggle meja')
                                    <button type="button" 
                                            onclick="quickToggleTable({{ $table->id }})"
                                            class="w-full py-2 rounded-lg text-xs font-bold transition-all
                                                   {{ $table->isOccupied() 
                                                      ? 'bg-emerald-500 text-white hover:bg-emerald-600 shadow-md shadow-emerald-200' 
                                                      : 'bg-red-500 text-white hover:bg-red-600 shadow-md shadow-red-200' }}">
                                        {{ $table->isOccupied() ? 'KOSONGKAN' : 'ISI MEJA' }}
                                    </button>
                                    @endcan
                                    
                                    @can('edit meja')
                                    <a href="{{ route('tables.edit', $table) }}"
                                       class="w-full py-2 rounded-lg bg-gray-900 text-white hover:bg-black text-xs font-bold text-center transition-all">
                                        EDIT
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
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $tables->links() }}
                </div>
            @endif
        </x-card-container>
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

    {{-- SweetAlert2 Notifications --}}
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '{{ session('success') }}',
            confirmButtonColor: '#658C58',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl px-6 py-2.5 font-bold'
            }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '{{ session('error') }}',
            confirmButtonColor: '#31694E',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl px-6 py-2.5 font-bold'
            }
        });
    @endif
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
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: 'Gagal mengubah status meja',
            confirmButtonColor: '#31694E'
        });
    });
}
</script>
@endpush
@endsection

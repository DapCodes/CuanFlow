@extends('layouts.app')

@section('title', 'Kelola Meja - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Kelola Meja</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-2xl md:text-2xl font-black text-gray-900 tracking-tight">
                    Kelola Meja
                </h1>
                <p class="mt-1 text-sm text-gray-500 font-medium">
                    Atur manajemen meja dan denah outlet Anda secara efisien.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @can('buat meja')
                <a href="{{ route('tables.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-5 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                    <span>Tambah Meja</span>
                </a>
                @endcan
            </div>
        </section>

        {{-- RINGKASAN STATISTIK --}}
        <section class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white border border-gray-100 rounded-[1.5rem] p-6 shadow-sm">
                <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2">Total</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-black text-gray-900">{{ number_format($stats['total'], 0, ',', '.') }}</span>
                    <span class="text-[10px] font-bold text-gray-400">Unit</span>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-[1.5rem] p-6 shadow-sm">
                <p class="text-[9px] font-black uppercase tracking-widest text-emerald-500 mb-2">Tersedia</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-black text-emerald-600">{{ number_format($stats['available'], 0, ',', '.') }}</span>
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-[1.5rem] p-6 shadow-sm">
                <p class="text-[9px] font-black uppercase tracking-widest text-red-500 mb-2">Terisi</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-black text-red-600">{{ number_format($stats['occupied'], 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-[1.5rem] p-6 shadow-sm">
                <p class="text-[9px] font-black uppercase tracking-widest text-amber-500 mb-2">Dipesan</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-black text-amber-600">{{ number_format($stats['reserved'], 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-[1.5rem] p-6 shadow-sm">
                <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2">Maintenance</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-black text-gray-400">{{ number_format($stats['maintenance'], 0, ',', '.') }}</span>
                </div>
            </div>
        </section>

        {{-- KONTEN UTAMA: TOOLBAR + GRID/TABEL --}}
        <x-card-container>
            {{-- Toolbar: Search & Filter --}}
            <div class="border-b border-gray-100 px-8 py-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gray-50/30">
                <div class="flex-1 max-w-md">
                    <input type="text" id="searchTable" placeholder="Cari nomor, nama, atau kode meja..."
                           class="w-full px-5 py-3 rounded-2xl border border-gray-200 text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all shadow-sm">
                </div>

                <div class="flex flex-wrap gap-3">
                    <select id="filterStatus"
                            class="px-5 py-3 rounded-2xl border border-gray-200 text-sm font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all bg-white shadow-sm">
                        <option value="">Semua Status</option>
                        <option value="available">Tersedia</option>
                        <option value="occupied">Terisi</option>
                        <option value="reserved">Dipesan</option>
                        <option value="maintenance">Maintenance</option>
                    </select>

                    @if($locations->isNotEmpty())
                    <select id="filterLocation"
                            class="px-5 py-3 rounded-2xl border border-gray-200 text-sm font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all bg-white shadow-sm">
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
                    <div class="flex flex-col items-center justify-center text-center py-24">
                        <div class="w-24 h-24 rounded-[2.5rem] bg-gray-50 flex items-center justify-center mb-6 border border-gray-200/50">
                            <i class="fas fa-chair text-4xl text-gray-200"></i>
                        </div>
                        <h3 class="text-xl font-black text-gray-900 mb-2">Belum ada meja</h3>
                        <p class="text-sm text-gray-400 mb-8 max-w-sm font-medium">
                            Meja Anda belum terdaftar. Mulai tambahkan meja baru untuk mengelola operasional outlet Anda.
                        </p>
                        @can('buat meja')
                        <a href="{{ route('tables.create') }}"
                           class="px-8 py-4 bg-cuan-green text-white rounded-2xl font-black text-sm hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95 leading-none">
                            Tambah Meja Pertama
                        </a>
                        @endcan
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6" id="tableGrid">
                        @foreach($tables as $table)
                            @php
                                $statusClasses = [
                                    'available' => [
                                        'bg' => 'bg-white',
                                        'border' => 'border-gray-200 group-hover:border-cuan-green/50',
                                        'shadow' => 'group-hover:shadow-cuan-green/10',
                                        'line' => 'bg-cuan-green',
                                        'number' => 'text-gray-900',
                                        'indicator' => 'bg-cuan-green ring-cuan-green/20'
                                    ],
                                    'occupied' => [
                                        'bg' => 'bg-red-50/40',
                                        'border' => 'border-red-100 group-hover:border-red-200',
                                        'shadow' => 'group-hover:shadow-red-50',
                                        'line' => 'bg-red-500',
                                        'number' => 'text-red-700',
                                        'indicator' => 'bg-red-500 ring-red-100'
                                    ],
                                    'reserved' => [
                                        'bg' => 'bg-amber-50/40',
                                        'border' => 'border-amber-100 group-hover:border-amber-200',
                                        'shadow' => 'group-hover:shadow-amber-50',
                                        'line' => 'bg-amber-500',
                                        'number' => 'text-amber-700',
                                        'indicator' => 'bg-amber-500 ring-amber-100'
                                    ],
                                    'maintenance' => [
                                        'bg' => 'bg-gray-50',
                                        'border' => 'border-gray-200 group-hover:border-gray-300',
                                        'shadow' => 'group-hover:shadow-gray-100',
                                        'line' => 'bg-gray-400',
                                        'number' => 'text-gray-500',
                                        'indicator' => 'bg-gray-400 ring-gray-200'
                                    ],
                                ];
                                $style = $statusClasses[$table->status] ?? $statusClasses['maintenance'];
                            @endphp
                            <div class="table-card rounded-[2rem] p-6 relative border {{ $style['bg'] }} {{ $style['border'] }} transition-all duration-500 group overflow-hidden"
                                 data-name="{{ strtolower($table->name ?? '') }}"
                                 data-number="{{ strtolower($table->table_number) }}"
                                 data-code="{{ strtolower($table->code ?? '') }}"
                                 data-status="{{ $table->status }}"
                                 data-location="{{ strtolower($table->location ?? '') }}">
                                
                                {{-- Top Accent Line --}}
                                <div class="absolute top-0 left-0 w-full h-1 {{ $style['line'] }} opacity-50"></div>

                                {{-- Status Indicator --}}
                                <div class="absolute top-4 right-4">
                                    <div class="w-3 h-3 rounded-full {{ $style['indicator'] }} ring-4 shadow-[0_0_10px_rgba(0,0,0,0.05)] {{ $table->status === 'available' ? 'animate-pulse' : '' }}"></div>
                                </div>

                                {{-- Table Info --}}
                                <div class="text-center mt-2">
                                    <span class="text-4xl font-black block mb-2 {{ $style['number'] }} tracking-tighter">{{ $table->table_number }}</span>
                                    
                                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest truncate px-2 mb-1">
                                        {{ $table->name ?: 'Unit ' . $table->table_number }}
                                    </h4>
                                    
                                    @if($table->location)
                                        <div class="inline-flex items-center px-2 py-0.5 rounded-full bg-gray-100 text-[8px] font-black text-gray-500 uppercase tracking-widest">
                                            {{ $table->location }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Footer --}}
                                <div class="mt-6 pt-5 border-t border-gray-100/50 flex items-center justify-between">
                                    <div class="flex items-center gap-1.5 overflow-hidden">
                                        <div class="w-1.5 h-1.5 rounded-full bg-gray-300 flex-shrink-0"></div>
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest truncate">{{ $table->code ?: 'NO CODE' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <i class="fas fa-users text-[8px] text-gray-300"></i>
                                        <span class="text-[9px] font-black text-gray-900">{{ $table->capacity }}</span>
                                    </div>
                                </div>

                                {{-- Action Overlay (Hidden by Default) --}}
                                <div class="absolute inset-0 bg-white/95 rounded-[2rem] opacity-0 group-hover:opacity-100 transition-all duration-300 flex flex-col items-center justify-center p-6 gap-3 z-10 scale-95 group-hover:scale-100">
                                    @can('quick toggle meja')
                                    <button type="button" 
                                            onclick="quickToggleTable({{ $table->id }}, '{{ $table->isOccupied() ? 'available' : 'occupied' }}')"
                                            class="w-full py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg active:scale-95
                                                   {{ $table->isOccupied() 
                                                      ? 'bg-cuan-green text-white hover:bg-cuan-dark shadow-cuan-green/20' 
                                                      : 'bg-red-500 text-white hover:bg-red-600 shadow-red-500/20' }}">
                                        {{ $table->isOccupied() ? 'Kosongkan' : 'Isi Meja' }}
                                    </button>
                                    @endcan
                                    
                                    @can('edit meja')
                                    <a href="{{ route('tables.edit', $table) }}"
                                       class="w-full py-3 rounded-xl bg-gray-900 text-white hover:bg-black text-[10px] font-black uppercase tracking-widest text-center transition-all shadow-lg shadow-gray-900/10 active:scale-95">
                                        Edit Data
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
            iconColor: '#658C58',
            customClass: {
                popup: 'rounded-[1.5rem] border-0 shadow-2xl',
                title: 'font-black tracking-tight',
                confirmButton: 'rounded-xl font-black uppercase text-xs tracking-widest px-6 py-3'
            }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: '{{ session('error') }}',
            confirmButtonColor: '#ef4444',
            customClass: {
                popup: 'rounded-[1.5rem] border-0 shadow-2xl',
                title: 'font-black tracking-tight',
                confirmButton: 'rounded-xl font-black uppercase text-xs tracking-widest px-6 py-3'
            }
        });
    @endif
});

function quickToggleTable(tableId, targetStatus) {
    Swal.fire({
        title: 'Ubah Status Meja?',
        text: `Konfirmasi untuk mengubah status meja menjadi ${targetStatus === 'available' ? 'Tersedia' : 'Terisi'}.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: targetStatus === 'available' ? '#658C58' : '#ef4444',
        cancelButtonColor: '#9ca3af',
        confirmButtonText: 'Ya, Lanjutkan',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-[1.5rem] border-0 shadow-2xl',
            title: 'font-black tracking-tight text-gray-900',
            confirmButton: 'rounded-xl font-black uppercase text-xs tracking-widest px-6 py-3',
            cancelButton: 'rounded-xl font-bold uppercase text-xs tracking-widest px-6 py-3'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

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
                } else {
                    throw new Error(data.message || 'Gagal mengubah status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'System Error',
                    text: error.message || 'Gagal menghubungi server',
                    confirmButtonColor: '#658C58'
                });
            });
        }
    });
}
</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'Informasi Outlet - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium">Informasi Outlet</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Informasi Outlet
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola daftar outlet, detail kontak, dan status operasional sistem Anda.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @can('buat outlet')
                <a href="{{ route('outlets.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-5 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                    <span>Tambah Outlet</span>
                </a>
                @endcan
            </div>
        </section>

        {{-- RINGKASAN STATISTIK --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-card-container>
                <div class="p-6">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Outlet</p>
                    <p class="mt-2 text-3xl font-black text-gray-900">{{ $outlets->total() }}</p>
                </div>
            </x-card-container>

            <x-card-container>
                <div class="p-6">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Outlet Aktif</p>
                    <p class="mt-2 text-3xl font-black text-cuan-green">{{ $outlets->where('is_active', true)->count() }}</p>
                </div>
            </x-card-container>

            <x-card-container>
                <div class="p-6">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Outlet Nonaktif</p>
                    <p class="mt-2 text-3xl font-black text-gray-400">{{ $outlets->where('is_active', false)->count() }}</p>
                </div>
            </x-card-container>

            <x-card-container>
                <div class="p-6">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Owner</p>
                    <p class="mt-2 text-3xl font-black text-orange-500">{{ $outlets->pluck('owner_id')->unique()->count() }}</p>
                </div>
            </x-card-container>
        </div>

        {{-- KONTEN UTAMA --}}
        <x-card-container>
            {{-- Toolbar: Search & Filter --}}
            <div class="px-6 py-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center gap-4">
                <div class="flex-1">
                    <input type="text" id="searchInput" placeholder="Cari nama outlet..."
                           class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all font-bold text-gray-700 shadow-sm placeholder:text-gray-400">
                </div>

                <div class="w-full md:w-48">
                    <select id="statusFilter"
                            class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all font-bold text-gray-700 shadow-sm">
                        <option value="">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left">Kode</th>
                            <th class="px-6 py-4 text-left">Outlet</th>
                            <th class="px-6 py-4 text-left">Alamat</th>
                            <th class="px-6 py-4 text-left">Kontak</th>
                            <th class="px-6 py-4 text-left">Owner</th>
                            <th class="px-6 py-4 text-left">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($outlets as $outlet)
                            <tr class="hover:bg-gray-50 transition-colors outlet-row {{ !$outlet->is_active ? 'bg-gray-50/50' : '' }}"
                                data-status="{{ $outlet->is_active ? 'active' : 'inactive' }}">
                                
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <span class="text-[10px] font-black font-mono text-gray-500 bg-gray-100 px-2 py-1 rounded-lg border border-gray-100 uppercase tracking-tighter">
                                        {{ $outlet->code }}
                                    </span>
                                </td>

                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="flex items-center gap-4 {{ !$outlet->is_active ? 'opacity-60 grayscale' : '' }}">
                                        @if($outlet->logo)
                                            <img src="{{ Storage::url($outlet->logo) }}" alt="{{ $outlet->name }}" class="w-12 h-12 rounded-xl object-cover border-2 border-white shadow-sm flex-shrink-0">
                                        @else
                                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-cuan-green to-cuan-dark flex items-center justify-center border-2 border-white shadow-sm flex-shrink-0">
                                                <span class="text-white font-black text-xs uppercase">{{ substr($outlet->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-bold text-gray-900 leading-tight outlet-name">{{ $outlet->name }}</div>
                                            <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">
                                                Sejak {{ $outlet->created_at->format('M Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-5">
                                    <div class="text-[11px] font-medium text-gray-600 max-w-xs leading-relaxed">
                                        {{ Str::limit($outlet->address, 50) }}
                                    </div>
                                    @if($outlet->latitude && $outlet->longitude)
                                        <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">
                                            GPS: {{ number_format($outlet->latitude, 4) }}, {{ number_format($outlet->longitude, 4) }}
                                        </div>
                                    @endif
                                </td>

                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="text-[11px] font-bold text-gray-900">
                                        {{ $outlet->phone ?? '-' }}
                                    </div>
                                    @if($outlet->email)
                                        <div class="text-[10px] font-medium text-gray-400 mt-0.5">
                                            {{ $outlet->email }}
                                        </div>
                                    @endif
                                </td>

                                <td class="px-6 py-5 whitespace-nowrap">
                                    @if($outlet->owner)
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-gray-100 border border-gray-100 flex items-center justify-center text-gray-500 font-black text-[10px]">
                                                {{ strtoupper(substr($outlet->owner->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="text-[11px] font-bold text-gray-900">{{ $outlet->owner->name }}</div>
                                                <div class="text-[9px] font-black uppercase tracking-widest text-gray-400">Owner</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic font-medium">No Owner</span>
                                    @endif
                                </td>

                                <td class="px-6 py-5 whitespace-nowrap">
                                    @if($outlet->is_active)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-cuan-green/10 text-cuan-green border border-cuan-green/10">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-50 text-gray-400 border border-gray-200">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-5 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        @can('lihat detail outlet')
                                        <a href="{{ route('outlets.show', $outlet->id) }}"
                                           class="w-9 h-9 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white transition-all active:scale-95 border border-blue-100"
                                           title="Detail">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('edit outlet')
                                        <a href="{{ route('outlets.edit', $outlet->id) }}"
                                           class="w-9 h-9 flex items-center justify-center rounded-xl bg-cuan-green/10 text-cuan-green hover:bg-cuan-green hover:text-white transition-all active:scale-95 border border-cuan-green/10"
                                           title="Edit">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        @endcan

                                        @if(auth()->user()->outlet_id !== $outlet->id)
                                            @can('hapus outlet')
                                            <form action="{{ route('outlets.destroy', $outlet->id) }}"
                                                  method="POST" class="inline confirm-delete" data-name="{{ $outlet->name }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all active:scale-95 border border-red-100"
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
                                <td colspan="7" class="px-6 py-20 text-center">
                                    <div class="w-16 h-16 bg-gray-50 border border-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                        <i class="fas fa-store-slash text-gray-200 text-2xl"></i>
                                    </div>
                                    <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">Belum Ada Outlet</h3>
                                    <p class="text-[11px] text-gray-500 font-bold uppercase tracking-widest mt-2 max-w-xs mx-auto">Daftar outlet Anda masih kosong.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($outlets->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $outlets->links() }}
                </div>
            @endif
        </x-card-container>
    </div>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    {{-- Session Notifications --}}
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 3000,
            iconColor: '#658C58',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black text-gray-900',
            }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: "{{ session('error') }}",
            confirmButtonColor: '#ef4444',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black text-gray-900',
            }
        });
    @endif

    {{-- AJAX Filter Logic --}}
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const tableContainer = document.querySelector('x-card-container'); // Assuming container has table
    let timeout = null;

    function refreshTable() {
        // Need to reconstruct URL with current filters
        const url = new URL(window.location.href);
        const search = searchInput.value;
        const status = statusFilter.value;

        if (search) url.searchParams.set('search', search);
        else url.searchParams.delete('search');

        if (status) url.searchParams.set('status', status);
        else url.searchParams.delete('status');

        // Target the specific section that contains the table for swapping
        const target = document.querySelector('table').closest('div').parentElement;
        target.style.opacity = '0.5';

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContent = doc.querySelector('table').closest('div').parentElement;
            
            if (newContent) {
                target.innerHTML = newContent.innerHTML;
                window.history.replaceState({}, '', url);
                // Re-init delete listeners as content replaced
                initDeleteListeners();
            }
        })
        .finally(() => { target.style.opacity = '1'; });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(refreshTable, 500);
        });
        searchInput.addEventListener('keypress', (e) => { if(e.key === 'Enter') e.preventDefault(); });
    }
    if (statusFilter) statusFilter.addEventListener('change', refreshTable);

    {{-- Delete Confirmation --}}
    function initDeleteListeners() {
        document.querySelectorAll('.confirm-delete').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const name = this.dataset.name;
                
                Swal.fire({
                    title: 'Hapus Outlet?',
                    text: `Apakah Anda yakin ingin menghapus "${name}"? Data terkait outlet akan ikut terhapus.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'rounded-[2rem] border-none shadow-2xl',
                        title: 'font-black text-gray-900',
                        confirmButton: 'rounded-xl px-6 py-3 font-bold text-sm',
                        cancelButton: 'rounded-xl px-6 py-3 font-bold text-sm'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    }

    initDeleteListeners();

    // Handle pagination clicks for AJAX too
    document.addEventListener('click', function(e) {
        const link = e.target.closest('.pagination a');
        if (link && document.querySelector('table').contains(e.target.closest('table'))) {
            e.preventDefault();
            const url = new URL(link.href);
            const target = document.querySelector('table').closest('div').parentElement;
            
            target.style.opacity = '0.5';
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.querySelector('table').closest('div').parentElement;
                if (newContent) {
                    target.innerHTML = newContent.innerHTML;
                    window.history.pushState({}, '', url);
                    initDeleteListeners();
                }
            })
            .finally(() => { target.style.opacity = '1'; });
        }
    });
});
</script>
@endpush

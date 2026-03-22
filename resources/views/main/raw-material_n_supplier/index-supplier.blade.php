@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Supplier - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Supplier</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">
        
        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Kelola Supplier
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Manajemen data supplier untuk pengadaan bahan baku operasional Anda.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('raw-materials.index') }}" 
                   class="inline-flex items-center gap-2 rounded-xl bg-white border border-gray-200 px-5 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all shadow-sm active:scale-95">
                    <span>Kelola Stok</span>
                </a>
                @can('buat supplier')
                <a href="{{ route('raw-materials.suppliers.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-5 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                    <span>Tambah Supplier</span>
                </a>
                @endcan
            </div>
        </section>

        {{-- RINGKASAN STATISTIK --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Supplier</p>
                <p class="mt-2 text-2xl font-black text-gray-900">{{ number_format($suppliers->total(), 0, ',', '.') }}</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Aktif</p>
                <p class="mt-2 text-2xl font-black text-cuan-green">
                    {{ number_format($total_active, 0, ',', '.') }}
                </p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Items Disuplai</p>
                <p class="mt-2 text-2xl font-black text-blue-600">
                    {{ number_format($total_items_supplied, 0, ',', '.') }} <span class="text-xs font-bold text-gray-400 uppercase">Items</span>
                </p>
            </div>
        </section>

        {{-- KONTEN UTAMA --}}
        <x-card-container>
            {{-- Toolbar: Search & Filter --}}
            <div class="px-6 py-5 border-b border-gray-100 bg-white flex flex-col md:flex-row md:items-center gap-4">
                <div class="flex-1 relative">
                    <input type="text" id="searchInput" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                           class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all font-bold shadow-sm">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                </div>

                <div class="w-full md:w-48">
                    <select id="statusFilter"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all bg-white font-bold shadow-sm">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>

            {{-- Table Container for AJAX --}}
            <div id="supplier-table-container">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left">Kode</th>
                                <th class="px-6 py-4 text-left">Supplier</th>
                                <th class="px-6 py-4 text-left">Kontak Person</th>
                                <th class="px-6 py-4 text-left">Telepon / WhatsApp</th>
                                <th class="px-6 py-4 text-left">Items</th>
                                <th class="px-6 py-4 text-left">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($suppliers as $supplier)
                                <tr class="hover:bg-gray-50 transition-colors supplier-row {{ !$supplier->is_active ? 'bg-gray-50/50' : '' }}">
                                    
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <span class="text-[10px] font-black font-mono text-gray-500 bg-gray-100 px-2 py-1 rounded-lg border border-gray-100 uppercase tracking-tighter">
                                            {{ $supplier->code }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-5">
                                        <div>
                                            <div class="font-bold text-gray-900 leading-tight">{{ $supplier->name }}</div>
                                            @if($supplier->email)
                                                <div class="text-[10px] font-bold text-gray-400 mt-1 lowercase">{{ $supplier->email }}</div>
                                            @endif
                                            @if($supplier->address)
                                                <div class="text-[9px] font-medium text-gray-400 mt-1 max-w-[200px] truncate">{{ $supplier->address }}</div>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-6 py-5">
                                        <div class="text-[11px] font-bold text-gray-900">{{ $supplier->contact_person ?: '-' }}</div>
                                    </td>

                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-1.5">
                                            <div class="text-[11px] font-bold text-gray-900">{{ $supplier->phone ?: '-' }}</div>
                                            @if($supplier->whatsapp_url)
                                                <a href="{{ $supplier->whatsapp_url }}" target="_blank" 
                                                   class="inline-flex items-center gap-1 w-fit bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full border border-emerald-100 hover:bg-emerald-100 transition-colors">
                                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                                </a>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-6 py-5">
                                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-[10px] font-black bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-widest">
                                            {{ $supplier->raw_materials_count + $supplier->products_count }} Items
                                        </span>
                                    </td>

                                    <td class="px-6 py-5 whitespace-nowrap">
                                        @if($supplier->is_active)
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
                                            @can('lihat detail supplier')
                                            <a href="{{ route('raw-materials.suppliers.show', $supplier) }}"
                                               class="w-9 h-9 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white transition-all active:scale-95 border border-blue-100"
                                               title="Detail">
                                                <i class="fas fa-eye text-xs"></i>
                                            </a>
                                            @endcan
                                            
                                            @can('edit supplier')
                                            <a href="{{ route('raw-materials.suppliers.edit', $supplier) }}"
                                               class="w-9 h-9 flex items-center justify-center rounded-xl bg-cuan-green/10 text-cuan-green hover:bg-cuan-green hover:text-white transition-all active:scale-95 border border-cuan-green/10"
                                               title="Edit">
                                                <i class="fas fa-edit text-xs"></i>
                                            </a>
                                            @endcan
                                            
                                            @can('hapus supplier')
                                            <form action="{{ route('raw-materials.suppliers.destroy', $supplier) }}"
                                                  method="POST" class="inline confirm-delete" data-name="{{ $supplier->name }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all active:scale-95 border border-red-100"
                                                        title="Hapus">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-20 text-center">
                                        <div class="w-16 h-16 bg-gray-50 border border-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                            <i class="fas fa-truck-loading text-gray-200 text-2xl"></i>
                                        </div>
                                        <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">Belum Ada Supplier</h3>
                                        <p class="text-[11px] text-gray-500 font-bold uppercase tracking-widest mt-2 max-w-xs mx-auto">Daftar supplier Anda masih kosong.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($suppliers->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $suppliers->links() }}
                    </div>
                @endif
            </div>
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
    let timeout = null;

    function refreshTable() {
        const url = new URL(window.location.href);
        const search = searchInput.value;
        const status = statusFilter.value;

        if (search) url.searchParams.set('search', search);
        else url.searchParams.delete('search');

        if (status) url.searchParams.set('status', status);
        else url.searchParams.delete('status');

        const target = document.getElementById('supplier-table-container');
        target.style.opacity = '0.5';

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContent = doc.getElementById('supplier-table-container');
            
            if (newContent) {
                target.innerHTML = newContent.innerHTML;
                window.history.replaceState({}, '', url);
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
                    title: 'Hapus Supplier?',
                    text: `Apakah Anda yakin ingin menghapus "${name}"? Tindakan ini tidak dapat dibatalkan.`,
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
        if (link && document.getElementById('supplier-table-container').contains(e.target.closest('#supplier-table-container'))) {
            e.preventDefault();
            const url = new URL(link.href);
            const target = document.getElementById('supplier-table-container');
            
            target.style.opacity = '0.5';
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.getElementById('supplier-table-container');
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
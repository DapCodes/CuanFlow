@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Kelola Diskon - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Kelola Diskon</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Kelola Diskon
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Atur promo dan diskon produk untuk meningkatkan penjualan outlet Anda.
                </p>
            </div>
            @can('buat diskon')
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('discounts.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-5 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                    <span>Tambah Diskon</span>
                </a>
            </div>
            @endcan
        </section>

        {{-- RINGKASAN STATISTIK --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Diskon</p>
                <p class="mt-2 text-2xl font-black text-gray-900">{{ number_format($stats['total'], 0, ',', '.') }}</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Aktif</p>
                <p class="mt-2 text-2xl font-black text-cuan-green">{{ number_format($stats['active'], 0, ',', '.') }}</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Kadaluarsa</p>
                <p class="mt-2 text-2xl font-black text-red-600">{{ number_format($stats['expired'], 0, ',', '.') }}</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Terpakai</p>
                <p class="mt-2 text-2xl font-black text-blue-600">{{ number_format($stats['used'], 0, ',', '.') }}</p>
            </div>
        </section>

        {{-- KONTEN UTAMA: TOOLBAR + TABEL --}}
        <x-card-container>
            {{-- Toolbar: Search & Filter --}}
            <div class="px-6 py-5 border-b border-gray-100 bg-white space-y-4 md:space-y-0 md:flex md:items-center md:gap-4">
                <div class="flex-1 relative">
                    <input type="text" id="searchDiscount" placeholder="Cari nama atau kode diskon..."
                           class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all font-bold">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                </div>

                <div class="flex flex-wrap gap-3">
                    <select id="filterType"
                            class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all bg-white">
                        <option value="">Semua Tipe</option>
                        <option value="percentage">Persentase</option>
                        <option value="fixed">Fixed</option>
                        <option value="buy_x_get_y">Buy X Get Y</option>
                    </select>

                    <select id="filterStatus"
                            class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all bg-white">
                        <option value="">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Tidak Aktif</option>
                        <option value="expired">Kadaluarsa</option>
                    </select>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left">Kode</th>
                            <th class="px-6 py-4 text-left">Nama Diskon</th>
                            <th class="px-6 py-4 text-left">Tipe</th>
                            <th class="px-6 py-4 text-left">Nilai</th>
                            <th class="px-6 py-4 text-left">Periode</th>
                            <th class="px-6 py-4 text-left">Penggunaan</th>
                            <th class="px-6 py-4 text-left">Voucher</th>
                            <th class="px-6 py-4 text-left">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white" id="discountTableBody">
                        @forelse($discounts as $discount)
                            @php
                                $isExpired = $discount->end_date && $discount->end_date->lt(now());
                                $isActive = $discount->is_active && !$isExpired;
                            @endphp
                            <tr class="discount-row hover:bg-gray-50 transition-colors"
                                data-name="{{ strtolower($discount->name) }}"
                                data-code="{{ strtolower($discount->code) }}"
                                data-type="{{ $discount->type }}"
                                data-status="{{ $isExpired ? 'expired' : ($discount->is_active ? 'active' : 'inactive') }}"
                                data-is-voucher="{{ $discount->is_voucher ? '1' : '0' }}">

                                {{-- Kode --}}
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-black font-mono text-gray-800 tracking-widest">
                                        {{ $discount->code }}
                                    </span>
                                </td>

                                {{-- Nama + Info Produk/Kategori --}}
                                <td class="px-6 py-5">
                                    <div class="font-bold text-gray-900 leading-tight">{{ $discount->name }}</div>
                                    <div class="mt-1 text-[10px] font-black uppercase tracking-widest text-gray-400 flex items-center gap-1.5">
                                        @if($discount->product)
                                            <span>{{ $discount->product->name }}</span>
                                        @elseif($discount->category)
                                            <span>{{ $discount->category->name }}</span>
                                        @else
                                            <span>Semua produk</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Tipe --}}
                                <td class="px-6 py-5 whitespace-nowrap">
                                    @if($discount->type === 'percentage')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-blue-50 text-blue-600 border border-blue-100">
                                            Persentase
                                        </span>
                                    @elseif($discount->type === 'fixed')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-cuan-green/10 text-cuan-green border border-cuan-green/20">
                                            Fixed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-purple-50 text-purple-600 border border-purple-100">
                                            Buy X Get Y
                                        </span>
                                    @endif
                                </td>

                                {{-- Nilai --}}
                                <td class="px-6 py-5 whitespace-nowrap">
                                    @if($discount->type === 'percentage')
                                        <span class="font-bold text-gray-900">{{ number_format($discount->value, 0) }}%</span>
                                    @elseif($discount->type === 'fixed')
                                        <span class="font-bold text-gray-900">Rp {{ number_format($discount->value, 0) }}</span>
                                    @else
                                        <span class="font-bold text-gray-900">Beli {{ $discount->buy_quantity }} Gratis {{ $discount->get_quantity }}</span>
                                    @endif
                                </td>

                                {{-- Periode --}}
                                <td class="px-6 py-5 whitespace-nowrap">
                                    @if($discount->start_date)
                                        <div class="text-[10px] font-black uppercase tracking-widest text-gray-400">
                                            Mulai <span class="text-gray-700">{{ $discount->start_date->format('d/m/Y') }}</span>
                                        </div>
                                    @endif
                                    @if($discount->end_date)
                                        <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">
                                            Sampai <span class="text-gray-700">{{ $discount->end_date->format('d/m/Y') }}</span>
                                        </div>
                                    @endif
                                    @if(!$discount->start_date && !$discount->end_date)
                                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-300">Tidak terbatas</span>
                                    @endif
                                </td>

                                {{-- Penggunaan --}}
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <span class="font-bold text-gray-900">{{ $discount->used_count }}</span>
                                    @if($discount->usage_limit)
                                        <span class="text-gray-400 text-[10px] font-black uppercase tracking-widest"> / {{ $discount->usage_limit }}</span>
                                    @else
                                        <span class="text-gray-300 text-[10px] font-black uppercase tracking-widest"> / ∞</span>
                                    @endif
                                </td>

                                {{-- Voucher --}}
                                <td class="px-6 py-5 whitespace-nowrap">
                                    @if($discount->is_voucher)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-indigo-50 text-indigo-600 border border-indigo-100">Ya</span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-50 text-gray-400 border border-gray-200">Tidak</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-5 whitespace-nowrap">
                                    @if($isExpired)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-red-50 text-red-500 border border-red-100">
                                            Kadaluarsa
                                        </span>
                                    @elseif($discount->is_active)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-cuan-green/10 text-cuan-green border border-cuan-green/10">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-50 text-gray-400 border border-gray-200">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-5 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('discounts.show', $discount->id) }}"
                                           class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 hover:bg-gray-100 transition-all active:scale-95"
                                           title="Detail">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>

                                        @can('edit diskon')
                                        <a href="{{ route('discounts.edit', $discount->id) }}"
                                           class="w-9 h-9 flex items-center justify-center rounded-xl bg-cuan-green/10 text-cuan-green hover:bg-cuan-green hover:text-white transition-all active:scale-95"
                                           title="Edit">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        @endcan

                                        @can('aktifkan nonaktifkan diskon')
                                        <form action="{{ route('discounts.toggle-status', $discount->id) }}" method="POST" class="inline confirm-toggle"
                                              data-name="{{ $discount->name }}"
                                              data-status="{{ $discount->is_active ? 'nonaktifkan' : 'aktifkan' }}">
                                            @csrf
                                            <button type="submit"
                                                    class="w-9 h-9 flex items-center justify-center rounded-xl transition-all active:scale-95
                                                    {{ $discount->is_active ? 'bg-gray-50 text-gray-400 hover:bg-gray-100' : 'bg-cuan-green/10 text-cuan-green hover:bg-cuan-green hover:text-white' }}"
                                                    title="{{ $discount->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <i class="fas fa-{{ $discount->is_active ? 'toggle-on' : 'toggle-off' }} text-xs"></i>
                                            </button>
                                        </form>
                                        @endcan

                                        @can('hapus diskon')
                                        <form action="{{ route('discounts.destroy', $discount->id) }}" method="POST" class="inline confirm-delete"
                                              data-name="{{ $discount->name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all active:scale-95"
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
                                <td colspan="9" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-center">
                                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                            <i class="fas fa-tags text-3xl text-gray-300"></i>
                                        </div>
                                        <h3 class="text-base font-semibold text-gray-900 mb-1">Belum ada diskon</h3>
                                        <p class="text-sm text-gray-500 mb-4 max-w-sm">
                                            Mulai buat diskon untuk menarik lebih banyak pelanggan dan meningkatkan penjualan.
                                        </p>
                                        @can('buat diskon')
                                        <a href="{{ route('discounts.create') }}"
                                           class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-6 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                                            <i class="fas fa-plus text-xs"></i>
                                            Tambah Diskon
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
            @if($discounts->hasPages())
                <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/30">
                    {{ $discounts->links() }}
                </div>
            @endif
        </x-card-container>
    </div>
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchDiscount');
    const filterType = document.getElementById('filterType');
    const filterStatus = document.getElementById('filterStatus');
    const discountRows = document.querySelectorAll('.discount-row');

    function filterDiscounts() {
        const searchTerm = (searchInput.value || '').toLowerCase();
        const typeFilter = filterType.value;
        const statusFilter = filterStatus.value;

        discountRows.forEach(row => {
            const name = row.dataset.name || '';
            const code = row.dataset.code || '';
            const type = row.dataset.type || '';
            const status = row.dataset.status || '';

            const matchesSearch = !searchTerm || name.includes(searchTerm) || code.includes(searchTerm);
            const matchesType = !typeFilter || type === typeFilter;
            const matchesStatus = !statusFilter || status === statusFilter;

            row.style.display = (matchesSearch && matchesType && matchesStatus) ? '' : 'none';
        });
    }

    if (searchInput) searchInput.addEventListener('input', filterDiscounts);
    if (filterType) filterType.addEventListener('change', filterDiscounts);
    if (filterStatus) filterStatus.addEventListener('change', filterDiscounts);

    // SweetAlert2 Notifications
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
                htmlContainer: 'text-sm font-medium text-gray-500'
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
                htmlContainer: 'text-sm font-medium text-gray-500'
            }
        });
    @endif

    // Confirm Delete
    document.querySelectorAll('.confirm-delete').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = this.dataset.name;

            Swal.fire({
                title: 'Hapus Diskon?',
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
                    htmlContainer: 'text-sm font-medium text-gray-500',
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

    // Confirm Toggle Status
    document.querySelectorAll('.confirm-toggle').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = this.dataset.name;
            const status = this.dataset.status;

            Swal.fire({
                title: `${status.charAt(0).toUpperCase() + status.slice(1)} Diskon?`,
                text: `Apakah Anda yakin ingin ${status} diskon "${name}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#658C58',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-[2rem] border-none shadow-2xl',
                    title: 'font-black text-gray-900',
                    htmlContainer: 'text-sm font-medium text-gray-500',
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
});
</script>
@endpush
@endsection

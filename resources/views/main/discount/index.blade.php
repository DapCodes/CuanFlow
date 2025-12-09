@extends('layouts.app')

@section('title', 'Kelola Diskon - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Kelola Diskon</span>
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

        {{-- HEADER HALAMAN (POLA UTAMA) --}}
        <section
            class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-500 border border-red-100">
                        <i class="fas fa-percent text-sm"></i>
                    </span>
                    <span>Kelola Diskon</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Atur promo dan diskon produk dengan tampilan sederhana, jelas, dan mudah dipahami untuk semua tim.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                <a href="{{ route('discounts.create') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-red-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-1">
                    <i class="fas fa-plus-circle text-sm"></i>
                    <span>Tambah Diskon</span>
                </a>
            </div>
        </section>

        {{-- RINGKASAN STATISTIK (BLOK YANG BISA DIJADIKAN POLA DI HALAMAN LAIN) --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Diskon</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div
                        class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100">
                        <i class="fas fa-tags text-gray-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Aktif</p>
                        <p class="mt-1 text-2xl font-semibold text-emerald-600">{{ $stats['active'] }}</p>
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
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Kadaluarsa</p>
                        <p class="mt-1 text-2xl font-semibold text-red-600">{{ $stats['expired'] }}</p>
                    </div>
                    <div
                        class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center border border-red-100">
                        <i class="fas fa-exclamation-triangle text-red-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Terpakai</p>
                        <p class="mt-1 text-2xl font-semibold text-blue-600">{{ $stats['used'] }}</p>
                    </div>
                    <div
                        class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center border border-blue-100">
                        <i class="fas fa-chart-line text-blue-500 text-lg"></i>
                    </div>
                </div>
            </div>
        </section>

        {{-- KONTEN UTAMA: TOOLBAR + TABEL (POLA UTAMA UNTUK DATA LIST) --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            {{-- Toolbar: Search & Filter --}}
            <div class="border-b border-gray-200 px-4 md:px-6 py-4 space-y-3 md:space-y-0 md:flex md:items-center md:justify-between gap-4">
                <div class="w-full md:max-w-md">
                    <label class="text-xs font-medium text-gray-500 mb-1 block">Cari diskon</label>
                    <div class="relative">
                        <input type="text" id="searchDiscount" placeholder="Cari berdasarkan nama atau kode..."
                               class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 w-full md:w-auto">
                    <div class="w-full sm:w-40 md:w-44">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Tipe Diskon</label>
                        <select id="filterType"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400">
                            <option value="">Semua Tipe</option>
                            <option value="percentage">Persentase</option>
                            <option value="fixed">Fixed</option>
                            <option value="buy_x_get_y">Buy X Get Y</option>
                        </select>
                    </div>

                    <div class="w-full sm:w-40 md:w-44">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Status</label>
                        <select id="filterStatus"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400">
                            <option value="">Semua Status</option>
                            <option value="active">Aktif</option>
                            <option value="inactive">Tidak Aktif</option>
                            <option value="expired">Kadaluarsa</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Kode
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Nama Diskon
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Tipe
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Nilai
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Periode
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Penggunaan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Status
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Aksi
                            </th>
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
                                data-status="{{ $isExpired ? 'expired' : ($discount->is_active ? 'active' : 'inactive') }}">
                                {{-- Kode --}}
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center rounded-md bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-800 font-mono">
                                        {{ $discount->code }}
                                    </span>
                                </td>

                                {{-- Nama + Info Produk/Kategori --}}
                                <td class="px-6 py-3">
                                    <div class="font-semibold text-gray-900">{{ $discount->name }}</div>
                                    <div class="mt-0.5 text-xs text-gray-500 flex items-center gap-1.5">
                                        @if($discount->product)
                                            <i class="fas fa-box text-[11px]"></i>
                                            <span>{{ $discount->product->name }}</span>
                                        @elseif($discount->category)
                                            <i class="fas fa-folder text-[11px]"></i>
                                            <span>{{ $discount->category->name }}</span>
                                        @else
                                            <span class="text-gray-400">Tanpa batasan produk</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Tipe --}}
                                <td class="px-6 py-3 whitespace-nowrap">
                                    @if($discount->type === 'percentage')
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                            <i class="fas fa-percent mr-1 text-[11px]"></i>
                                            Persentase
                                        </span>
                                    @elseif($discount->type === 'fixed')
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <i class="fas fa-money-bill mr-1 text-[11px]"></i>
                                            Fixed
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100">
                                            <i class="fas fa-gift mr-1 text-[11px]"></i>
                                            Buy X Get Y
                                        </span>
                                    @endif
                                </td>

                                {{-- Nilai --}}
                                <td class="px-6 py-3 whitespace-nowrap">
                                    @if($discount->type === 'percentage')
                                        <span class="text-sm font-semibold text-gray-900">
                                            {{ number_format($discount->value, 0) }}%
                                        </span>
                                    @elseif($discount->type === 'fixed')
                                        <span class="text-sm font-semibold text-gray-900">
                                            Rp {{ number_format($discount->value, 0) }}
                                        </span>
                                    @else
                                        <span class="text-sm font-semibold text-gray-900">
                                            Beli {{ $discount->buy_quantity }} Gratis {{ $discount->get_quantity }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Periode --}}
                                <td class="px-6 py-3 whitespace-nowrap text-xs text-gray-600">
                                    @if($discount->start_date)
                                        <div>
                                            <span class="text-gray-400 mr-1">Mulai</span>
                                            {{ $discount->start_date->format('d/m/Y') }}
                                        </div>
                                    @endif
                                    @if($discount->end_date)
                                        <div>
                                            <span class="text-gray-400 mr-1">Sampai</span>
                                            {{ $discount->end_date->format('d/m/Y') }}
                                        </div>
                                    @endif
                                    @if(!$discount->start_date && !$discount->end_date)
                                        <span class="text-gray-400">Tidak dibatasi tanggal</span>
                                    @endif
                                </td>

                                {{-- Penggunaan --}}
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <div class="text-sm">
                                        <span class="font-semibold text-gray-900">{{ $discount->used_count }}</span>
                                        @if($discount->usage_limit)
                                            <span class="text-gray-500">/ {{ $discount->usage_limit }}</span>
                                        @else
                                            <span class="text-gray-400">/ ∞</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-3 whitespace-nowrap">
                                    @if($isExpired)
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>
                                            Kadaluarsa
                                        </span>
                                    @elseif($discount->is_active)
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1.5"></span>
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-3 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('discounts.show', $discount->id) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-gray-200 bg-white text-gray-600 hover:bg-gray-50"
                                           title="Detail">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        <a href="{{ route('discounts.edit', $discount->id) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-yellow-200 bg-yellow-50 text-yellow-600 hover:bg-yellow-100"
                                           title="Edit">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <form action="{{ route('discounts.toggle-status', $discount->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md border bg-white text-gray-600 hover:bg-gray-50
                                                    {{ $discount->is_active ? 'text-green-600' : 'bg-gray-100 text-gray-600' }}"
                                                    title="{{ $discount->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <i class="fas fa-{{ $discount->is_active ? 'toggle-on' : 'toggle-off' }} text-xs"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('discounts.destroy', $discount->id) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus diskon ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-red-200 bg-red-50 text-red-600 hover:bg-red-100"
                                                    title="Hapus">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-center">
                                        <div
                                            class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                            <i class="fas fa-percent text-3xl text-gray-300"></i>
                                        </div>
                                        <h3 class="text-base font-semibold text-gray-900 mb-1">Belum ada diskon</h3>
                                        <p class="text-sm text-gray-500 mb-4 max-w-sm">
                                            Mulai buat diskon untuk menarik lebih banyak pelanggan dan meningkatkan penjualan.
                                        </p>
                                        <a href="{{ route('discounts.create') }}"
                                           class="inline-flex items-center gap-2 rounded-lg bg-red-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-600">
                                            <i class="fas fa-plus-circle text-xs"></i>
                                            Tambah Diskon
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($discounts->hasPages())
                <div class="px-4 md:px-6 py-3 border-t border-gray-200">
                    {{ $discounts->links() }}
                </div>
            @endif
        </section>
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

            const matchesSearch = !searchTerm
                || name.includes(searchTerm)
                || code.includes(searchTerm);

            const matchesType = !typeFilter || type === typeFilter;
            const matchesStatus = !statusFilter || status === statusFilter;

            row.style.display = (matchesSearch && matchesType && matchesStatus) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterDiscounts);
    filterType.addEventListener('change', filterDiscounts);
    filterStatus.addEventListener('change', filterDiscounts);
});
</script>
@endpush
@endsection

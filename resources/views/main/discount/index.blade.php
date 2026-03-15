@extends('layouts.app')

@section('title', 'Kelola Diskon - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-900 transition-colors">Dashboard</a>
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Kelola Diskon</span>
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
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-gray-100 pb-6 mb-6">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900 leading-tight">
                    Kelola Diskon
                </h1>
                <p class="mt-1 text-sm text-gray-500 font-medium">
                    Atur promo dan diskon produk dengan tampilan sederhana, jelas, dan mudah dipahami.
                </p>
            </div>
            @can('buat diskon')
            <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                <a href="{{ route('discounts.create') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-700 transition-all active:scale-95 shadow-lg shadow-red-600/20">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Diskon</span>
                </a>
            </div>
            @endcan
        </section>

        {{-- RINGKASAN STATISTIK --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-card-container class="px-6 py-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Diskon</p>
                        <p class="mt-1 text-2xl font-black text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-[1rem] bg-gray-50 flex items-center justify-center">
                        <i class="fas fa-tags text-gray-400 text-xl"></i>
                    </div>
                </div>
            </x-card-container>

            <x-card-container class="px-6 py-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Aktif</p>
                        <p class="mt-1 text-2xl font-black text-cuan-green">{{ $stats['active'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-[1rem] bg-cuan-green/10 flex items-center justify-center">
                        <i class="fas fa-check-circle text-cuan-green text-xl"></i>
                    </div>
                </div>
            </x-card-container>

            <x-card-container class="px-6 py-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Kadaluarsa</p>
                        <p class="mt-1 text-2xl font-black text-red-500">{{ $stats['expired'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-[1rem] bg-red-50 flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                    </div>
                </div>
            </x-card-container>

            <x-card-container class="px-6 py-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Terpakai</p>
                        <p class="mt-1 text-2xl font-black text-blue-500">{{ $stats['used'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-[1rem] bg-blue-50 flex items-center justify-center">
                        <i class="fas fa-chart-line text-blue-400 text-xl"></i>
                    </div>
                </div>
            </x-card-container>
        </section>

        {{-- KONTEN UTAMA: TOOLBAR + TABEL --}}
        <x-card-container class="p-0 overflow-hidden">
            {{-- Toolbar: Search & Filter --}}
            <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gray-50/50">
                <div class="w-full md:max-w-md relative">
                    <input type="text" id="searchDiscount" placeholder="Cari berdasarkan nama atau kode..."
                           class="w-full pl-10 pr-4 py-3 rounded-xl border-gray-200 border-2 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:border-red-400 focus:ring-0 transition-colors">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                </div>

                <div class="flex items-center gap-3">
                    <select id="filterType"
                            class="text-[10px] font-bold px-3 py-2.5 border-none bg-white rounded-lg focus:ring-2 focus:ring-red-400/20 uppercase tracking-widest text-gray-600 shadow-sm cursor-pointer border-gray-200 border">
                        <option value="">Semua Tipe</option>
                        <option value="percentage">Persentase</option>
                        <option value="fixed">Fixed</option>
                        <option value="buy_x_get_y">Buy X Get Y</option>
                    </select>

                    <select id="filterStatus"
                            class="text-[10px] font-bold px-3 py-2.5 border-none bg-white rounded-lg focus:ring-2 focus:ring-red-400/20 uppercase tracking-widest text-gray-600 shadow-sm cursor-pointer border-gray-200 border">
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
                    <thead class="bg-white border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Kode
                            </th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Nama Diskon
                            </th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Tipe
                            </th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Nilai
                            </th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Periode
                            </th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Penggunaan
                            </th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Voucher
                            </th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Status
                            </th>
                            <th class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest w-24">
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
                                data-status="{{ $isExpired ? 'expired' : ($discount->is_active ? 'active' : 'inactive') }}"
                                data-is-voucher="{{ $discount->is_voucher ? '1' : '0' }}">
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

                                    {{-- Voucher --}}
                                    <td class="px-6 py-3 whitespace-nowrap text-center">
                                        @if($discount->is_voucher)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">Ya</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200">Tidak</span>
                                        @endif
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
                                    <div class="inline-flex items-center justify-center gap-2">
                                        <a href="{{ route('discounts.show', $discount->id) }}"
                                           class="inline-flex items-center justify-center w-7 h-7 bg-white border border-gray-200 text-gray-500 hover:text-blue-600 hover:border-blue-300 hover:bg-blue-50 rounded-lg shadow-sm transition-all duration-200" title="Detail">
                                            <i class="fas fa-eye text-[10px]"></i>
                                        </a>
                                        @can('edit diskon')
                                        <a href="{{ route('discounts.edit', $discount->id) }}"
                                           class="inline-flex items-center justify-center w-7 h-7 bg-white border border-gray-200 text-gray-500 hover:text-amber-600 hover:border-amber-300 hover:bg-amber-50 rounded-lg shadow-sm transition-all duration-200" title="Edit">
                                            <i class="fas fa-edit text-[10px]"></i>
                                        </a>
                                        @endcan
                                        @can('aktifkan nonaktifkan diskon')
                                        <form action="{{ route('discounts.toggle-status', $discount->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center w-7 h-7 bg-white border border-gray-200 text-gray-500 hover:text-emerald-600 hover:border-emerald-300 hover:bg-emerald-50 rounded-lg shadow-sm transition-all duration-200 {{ $discount->is_active ? 'text-emerald-600' : 'text-gray-400' }}"
                                                    title="{{ $discount->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <i class="fas fa-{{ $discount->is_active ? 'toggle-on' : 'toggle-off' }} text-[10px]"></i>
                                            </button>
                                        </form>
                                        @endcan
                                        @can('hapus diskon')
                                        <form action="{{ route('discounts.destroy', $discount->id) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus diskon ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center w-7 h-7 bg-white border border-gray-200 text-gray-500 hover:text-red-600 hover:border-red-300 hover:bg-red-50 rounded-lg shadow-sm transition-all duration-200" title="Hapus">
                                                <i class="fas fa-trash text-[10px]"></i>
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
                                        <div
                                            class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                            <i class="fas fa-percent text-3xl text-gray-300"></i>
                                        </div>
                                        <h3 class="text-base font-semibold text-gray-900 mb-1">Belum ada diskon</h3>
                                        <p class="text-sm text-gray-500 mb-4 max-w-sm">
                                            Mulai buat diskon untuk menarik lebih banyak pelanggan dan meningkatkan penjualan.
                                        </p>
                                        @can('buat diskon')
                                        <a href="{{ route('discounts.create') }}"
                                           class="inline-flex items-center gap-2 rounded-lg bg-red-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-600">
                                            <i class="fas fa-plus-circle text-xs"></i>
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
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
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

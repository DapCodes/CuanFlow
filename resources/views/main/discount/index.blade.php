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
<main class="flex-grow py-8 px-4">
    <div class="max-w-7xl mx-auto">
        
        @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg" role="alert">
            <div class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                <div class="flex-1">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg" role="alert">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-red-500 mt-1 mr-3"></i>
                <div class="flex-1">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Main Card -->
        <x-card-container>
            <div class="bg-gradient-to-br from-red-400 to-pink-500 p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-white flex items-center">
                            <i class="fas fa-percent mr-3"></i>
                            Kelola Diskon
                        </h2>
                        <p class="text-sm text-red-50 mt-1">Atur promo dan diskon untuk produk Anda</p>
                    </div>
                    <a href="{{ route('discounts.create') }}" 
                       class="inline-flex items-center px-5 py-2.5 bg-white text-red-500 rounded-lg font-semibold hover:bg-red-50 transition-colors shadow-md">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Tambah Diskon
                    </a>
                </div>
            </div>

            <!-- Filter & Search -->
            <div class="p-6 bg-gray-50 border-b border-gray-200">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" id="searchDiscount" placeholder="Cari diskon..." 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                    <select id="filterType" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option value="">Semua Tipe</option>
                        <option value="percentage">Persentase</option>
                        <option value="fixed">Fixed</option>
                        <option value="buy_x_get_y">Buy X Get Y</option>
                    </select>
                    <select id="filterStatus" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option value="">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Tidak Aktif</option>
                        <option value="expired">Kadaluarsa</option>
                    </select>
                </div>
            </div>

            <!-- Discounts Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-barcode mr-1 text-gray-400"></i>
                                Kode
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-tag mr-1 text-gray-400"></i>
                                Nama Diskon
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-layer-group mr-1 text-gray-400"></i>
                                Tipe
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-dollar-sign mr-1 text-gray-400"></i>
                                Nilai
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-calendar mr-1 text-gray-400"></i>
                                Periode
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-chart-bar mr-1 text-gray-400"></i>
                                Penggunaan
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-toggle-on mr-1 text-gray-400"></i>
                                Status
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-cog mr-1 text-gray-400"></i>
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="discountTableBody">
                        @forelse($discounts as $discount)
                        @php
                            $isExpired = $discount->end_date && $discount->end_date->lt(now());
                            $isActive = $discount->is_active && !$isExpired;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors discount-row" 
                            data-name="{{ strtolower($discount->name) }}"
                            data-code="{{ strtolower($discount->code) }}"
                            data-type="{{ $discount->type }}"
                            data-status="{{ $isExpired ? 'expired' : ($discount->is_active ? 'active' : 'inactive') }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono font-semibold text-gray-900 bg-gray-100 px-2 py-1 rounded">
                                    {{ $discount->code }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $discount->name }}</div>
                                @if($discount->product)
                                <div class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-box mr-1"></i>
                                    {{ $discount->product->name }}
                                </div>
                                @elseif($discount->category)
                                <div class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-folder mr-1"></i>
                                    {{ $discount->category->name }}
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($discount->type === 'percentage')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-percent mr-1"></i>
                                    Persentase
                                </span>
                                @elseif($discount->type === 'fixed')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-money-bill mr-1"></i>
                                    Fixed
                                </span>
                                @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <i class="fas fa-gift mr-1"></i>
                                    Buy X Get Y
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($discount->type === 'percentage')
                                <span class="text-sm font-bold text-gray-900">{{ number_format($discount->value, 0) }}%</span>
                                @elseif($discount->type === 'fixed')
                                <span class="text-sm font-bold text-gray-900">Rp {{ number_format($discount->value, 0) }}</span>
                                @else
                                <span class="text-sm font-bold text-gray-900">
                                    Beli {{ $discount->buy_quantity }} Gratis {{ $discount->get_quantity }}
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-gray-600">
                                    @if($discount->start_date)
                                    <div><i class="fas fa-calendar-plus mr-1"></i>{{ $discount->start_date->format('d/m/Y') }}</div>
                                    @endif
                                    @if($discount->end_date)
                                    <div><i class="fas fa-calendar-minus mr-1"></i>{{ $discount->end_date->format('d/m/Y') }}</div>
                                    @endif
                                    @if(!$discount->start_date && !$discount->end_date)
                                    <span class="text-gray-400">Tidak terbatas</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <span class="font-semibold text-gray-900">{{ $discount->used_count }}</span>
                                    @if($discount->usage_limit)
                                    <span class="text-gray-500">/ {{ $discount->usage_limit }}</span>
                                    @else
                                    <span class="text-gray-400">/ ∞</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($isExpired)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                    Kadaluarsa
                                </span>
                                @elseif($discount->is_active)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                    Aktif
                                </span>
                                @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                    <span class="w-2 h-2 bg-gray-500 rounded-full mr-2"></span>
                                    Tidak Aktif
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('discounts.show', $discount->id) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors"  
                                       title="Detail">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="{{ route('discounts.edit', $discount->id) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"  
                                       title="Edit">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <form action="{{ route('discounts.toggle-status', $discount->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg {{ $discount->is_active ? 'bg-gray-100 text-gray-600 hover:bg-gray-200' : 'bg-green-100 text-green-600 hover:bg-green-200' }} transition-colors"  
                                                title="{{ $discount->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="fas fa-{{ $discount->is_active ? 'toggle-off' : 'toggle-on' }} text-sm"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('discounts.destroy', $discount->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus diskon ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition-colors"  
                                                title="Hapus">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-percent text-5xl text-gray-300"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Diskon</h3>
                                    <p class="text-sm text-gray-500 mb-6">Tambahkan diskon pertama Anda untuk menarik lebih banyak pelanggan</p>
                                    <a href="{{ route('discounts.create') }}" 
                                       class="inline-flex items-center px-5 py-2.5 bg-gradient-to-br from-red-400 to-pink-500 text-white rounded-lg font-semibold hover:from-red-500 hover:to-pink-600 transition-colors shadow-md">
                                        <i class="fas fa-plus-circle mr-2"></i>
                                        Tambah Diskon
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($discounts->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $discounts->links() }}
            </div>
            @endif

            <div class="bg-gradient-to-br from-yellow-50 to-orange-50 p-6 border-t border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Diskon</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
                            </div>
                            <div class="w-12 h-12 bg-gradient-to-br from-red-100 to-pink-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-tags text-red-500 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Aktif</p>
                                <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['active'] }}</p>
                            </div>
                            <div class="w-12 h-12 bg-gradient-to-br from-green-100 to-emerald-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-500 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Kadaluarsa</p>
                                <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['expired'] }}</p>
                            </div>
                            <div class="w-12 h-12 bg-gradient-to-br from-red-100 to-rose-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Terpakai</p>
                                <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['used'] }}</p>
                            </div>
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-line text-blue-500 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-card-container>

    </div>
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchDiscount');
    const filterType = document.getElementById('filterType');
    const filterStatus = document.getElementById('filterStatus');
    const discountRows = document.querySelectorAll('.discount-row');

    function filterDiscounts() {
        const searchTerm = searchInput.value.toLowerCase();
        const typeFilter = filterType.value;
        const statusFilter = filterStatus.value;

        discountRows.forEach(row => {
            const name = row.dataset.name;
            const code = row.dataset.code;
            const type = row.dataset.type;
            const status = row.dataset.status;

            const matchesSearch = name.includes(searchTerm) || code.includes(searchTerm);
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
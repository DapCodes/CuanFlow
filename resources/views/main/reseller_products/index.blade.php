@extends('layouts.app')

@section('title', 'Produk Reseller - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Produk Reseller</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Produk Reseller
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola stok produk yang Anda beli dari outlet pusat untuk dijual kembali.
                </p>
            </div>
        </section>

        {{-- RINGKASAN STATISTIK --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Produk</p>
                <p class="mt-2 text-2xl font-black text-gray-900">{{ number_format($stats['total'], 0, ',', '.') }}</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Menunggu (Pending)</p>
                <p class="mt-2 text-2xl font-black text-amber-500">{{ number_format($stats['pending'], 0, ',', '.') }}</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Diterima</p>
                <p class="mt-2 text-2xl font-black text-cuan-green">{{ number_format($stats['accepted'], 0, ',', '.') }}</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Estimasi Aset Stok</p>
                <p class="mt-2 text-2xl font-black text-blue-600">Rp {{ number_format($stats['stock_value'], 0, ',', '.') }}</p>
            </div>
        </section>

        {{-- KONTEN UTAMA: TOOLBAR + TABEL --}}
        <x-card-container>
            {{-- Toolbar: Search & Filter --}}
            <form action="{{ route('reseller-products.index') }}" method="GET" id="filterForm" class="px-6 py-5 border-b border-gray-100 bg-white space-y-4 md:space-y-0 md:flex md:items-center md:gap-4">
                <div class="flex-1 relative">
                    <input type="text" name="search" id="searchProduct" value="{{ request('search') }}" placeholder="Cari nama produk atau outlet sumber..."
                           class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all font-bold">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                </div>

                <div class="flex flex-wrap gap-3">
                    <select name="status" id="filterStatus"
                            class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all bg-white">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
            </form>

            {{-- Tabel --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left">Nama Produk</th>
                            <th class="px-6 py-4 text-left">Sumber Outlet</th>
                            <th class="px-6 py-4 text-left">Harga Beli</th>
                            <th class="px-6 py-4 text-left">Harga Jual</th>
                            <th class="px-6 py-4 text-left">Stok Saat Ini</th>
                            <th class="px-6 py-4 text-left">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white" id="productTableBody">
                        @forelse($products as $product)
                            <tr class="product-row hover:bg-gray-50 transition-colors">
                                
                                {{-- Produk --}}
                                <td class="px-6 py-5">
                                    <div class="font-bold text-gray-900 leading-tight">{{ $product->name }}</div>
                                    <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">
                                        ID: #{{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}
                                    </div>
                                </td>

                                {{-- Sumber --}}
                                <td class="px-6 py-5">
                                    <div class="text-xs font-bold text-gray-700">
                                        {{ $product->sourceOutlet->name ?? 'Outlet Sumber' }}
                                    </div>
                                </td>

                                {{-- Harga Beli --}}
                                <td class="px-6 py-5">
                                    <div class="text-sm font-bold text-gray-900">
                                        Rp {{ number_format($product->purchase_price, 0, ',', '.') }}
                                    </div>
                                </td>

                                {{-- Harga Jual --}}
                                <td class="px-6 py-5">
                                    @if($product->selling_price > 0)
                                        <div class="text-sm font-bold text-indigo-600">
                                            Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                        </div>
                                    @else
                                        <div class="text-[10px] font-black uppercase tracking-widest text-red-500">
                                            Belum Diatur
                                        </div>
                                    @endif
                                </td>

                                {{-- Stok --}}
                                <td class="px-6 py-5">
                                    <div class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-bold {{ $product->stock > 0 ? 'bg-indigo-50 text-indigo-700' : 'bg-red-50 text-red-700' }}">
                                        {{ number_format($product->stock, 2, ',', '.') }}
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-5 whitespace-nowrap">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-amber-50 text-amber-500 border-amber-100',
                                            'accepted' => 'bg-cuan-green/10 text-cuan-green border-cuan-green/10',
                                            'rejected' => 'bg-red-50 text-red-500 border-red-100',
                                        ];
                                        $class = $statusClasses[$product->status] ?? 'bg-gray-50 text-gray-400 border-gray-100';
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $class }}">
                                        {{ $product->status }}
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-5 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="inline-flex items-center gap-2">
                                        @if($product->status == 'pending')
                                            <form action="{{ route('reseller-products.update', $product->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="accepted">
                                                <button type="button"
                                                        onclick="openAcceptModal({{ $product->id }}, '{{ $product->name }}', {{ $product->purchase_price }})"
                                                        class="w-9 h-9 flex items-center justify-center rounded-xl bg-cuan-green/10 text-cuan-green hover:bg-cuan-green hover:text-white transition-all active:scale-95"
                                                        title="Terima">
                                                    <i class="fas fa-check text-xs"></i>
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('reseller-products.update', $product->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="rejected">
                                                <input type="hidden" name="selling_price" value="0">
                                                <button type="submit"
                                                        class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-100/50 text-red-600 hover:bg-red-500 hover:text-white transition-all active:scale-95"
                                                        title="Tolak">
                                                    <i class="fas fa-times text-xs"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Hapus (Soft Delete) --}}
                                        <form action="{{ route('reseller-products.destroy', $product->id) }}" method="POST" class="inline confirm-delete" data-name="{{ $product->name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 hover:bg-red-500 hover:text-white transition-all active:scale-95"
                                                    title="Hapus">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-center">
                                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                            <i class="fas fa-boxes text-3xl text-gray-300"></i>
                                        </div>
                                        <h3 class="text-base font-semibold text-gray-900 mb-1">Belum ada produk</h3>
                                        <p class="text-sm text-gray-500 mb-4 max-w-sm">
                                            Belum ada produk yang disinkronkan dari outlet pusat. Lakukan pembelian melalui POS pusat terlebih dahulu.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($products->hasPages())
                <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/30">
                    {{ $products->links() }}
                </div>
            @endif
        </x-card-container>
    </div>
</main>

{{-- MODAL TERIMA PRODUK --}}
<div id="acceptProductModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-900 opacity-75 backdrop-blur-sm"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
            <form id="acceptProductForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="accepted">
                <div class="bg-white px-8 pt-8 pb-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-black text-gray-900" id="modalTitle">
                            Terima Produk
                        </h3>
                        <button type="button" onclick="closeAcceptModal()" class="text-gray-400 hover:text-gray-500 bg-gray-50 p-2 rounded-xl transition-all">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Produk</label>
                            <p id="productName" class="font-bold text-gray-900"></p>
                            <div class="mt-2 text-xs flex items-center gap-2">
                                <span class="text-gray-400">Harga Beli:</span>
                                <span id="purchasePriceDisplay" class="font-black text-cuan-green"></span>
                            </div>
                        </div>

                        <div>
                            <label for="selling_price" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Tentukan Harga Jual (Rp)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-400">Rp</span>
                                <input type="number" name="selling_price" id="selling_price" required min="0"
                                       class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all font-bold text-gray-900">
                            </div>
                            <p class="mt-2 text-[10px] text-gray-400 italic">* Harga ini juga akan digunakan sebagai HPP produk di sistem Anda.</p>
                        </div>

                        <div>
                            <label for="expired_at" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Tanggal Kadaluarsa (Opsional)</label>
                            <input type="date" name="expired_at" id="expired_at"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all font-bold text-gray-900">
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-8 py-6 flex flex-col gap-3">
                    <button type="submit" class="w-full bg-cuan-green hover:bg-cuan-dark text-white font-black py-4 rounded-xl transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                        Konfirmasi & Terima
                    </button>
                    <button type="button" onclick="closeAcceptModal()" class="w-full bg-white border border-gray-200 text-gray-500 font-bold py-3 rounded-xl hover:bg-gray-50 transition-all">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openAcceptModal(id, name, purchasePrice) {
        const form = document.getElementById('acceptProductForm');
        const modal = document.getElementById('acceptProductModal');
        const nameText = document.getElementById('productName');
        const priceDisplay = document.getElementById('purchasePriceDisplay');
        const input = document.getElementById('selling_price');

        form.action = `/reseller-products/${id}`;
        nameText.innerText = name;
        priceDisplay.innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(purchasePrice);
        input.value = purchasePrice; // Default to purchase price

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeAcceptModal() {
        const modal = document.getElementById('acceptProductModal');
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Confirm Delete
        document.querySelectorAll('.confirm-delete').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const name = this.dataset.name;
                
                Swal.fire({
                    title: 'Hapus Produk?',
                    text: `Apakah Anda yakin ingin menghapus "${name}" dari daftar stok Anda?`,
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
    });
</script>
@endpush
@endsection

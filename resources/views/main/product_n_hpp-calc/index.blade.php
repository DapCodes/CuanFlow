@extends('layouts.app')

@section('title', 'Produk & Resep - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Produk & Resep</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Daftar Produk & Resep
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola produk dan resep dengan perhitungan HPP otomatis dan tampilan yang konsisten.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @can('buat produk')
                <a href="{{ route('products-hpp.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-5 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                    <span>Tambah Produk</span>
                </a>
                @endcan
            </div>
        </section>

        {{-- RINGKASAN STATISTIK --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Produk</p>
                <p class="mt-2 text-2xl font-black text-gray-900">{{ number_format($stats['total'], 0, ',', '.') }}</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Produk Aktif</p>
                <p class="mt-2 text-2xl font-black text-cuan-green">{{ number_format($stats['active'], 0, ',', '.') }}</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Rata-rata HPP</p>
                <p class="mt-2 text-2xl font-black text-gray-900">Rp {{ number_format($stats['avg_hpp'], 0, ',', '.') }}</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Avg Margin</p>
                <p class="mt-2 text-2xl font-black text-blue-600">{{ number_format($stats['avg_margin'], 1) }}%</p>
            </div>
        </section>

        {{-- KONTEN UTAMA: TOOLBAR + TABEL --}}
        <x-card-container>
            {{-- Toolbar: Search & Filter --}}
            <div class="px-6 py-5 border-b border-gray-100 bg-white space-y-4 md:space-y-0 md:flex md:items-center md:gap-4">
                <div class="flex-1 relative">
                    <input type="text" id="searchTerm" placeholder="Cari berdasarkan nama atau kode..."
                           class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all font-bold">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                </div>

                <div class="flex flex-wrap gap-3">
                    <select id="filterCategory"
                            class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all bg-white font-bold text-gray-600">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>

                    <select id="filterStatus"
                            class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all bg-white font-bold text-gray-600">
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
                            <th class="px-6 py-4 text-left">Produk</th>
                            <th class="px-6 py-4 text-left">Kategori</th>
                            <th class="px-6 py-4 text-left">HPP</th>
                            <th class="px-6 py-4 text-left">Harga Jual</th>
                            <th class="px-6 py-4 text-left">Margin</th>
                            <th class="px-6 py-4 text-left">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white" id="productTableBody">
                        @forelse($products as $product)
                            <tr class="product-row hover:bg-gray-50 transition-colors"
                                data-name="{{ strtolower($product->name) }}"
                                data-code="{{ strtolower($product->code) }}"
                                data-category="{{ $product->category_id }}"
                                data-status="{{ $product->is_active ? 'active' : 'inactive' }}">
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">
                                        {{ $product->code }}
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        @if($product->image)
                                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                                 class="w-14 h-14 flex-shrink-0 aspect-square rounded-2xl object-cover border-2 border-white shadow-lg shadow-gray-200/50 transition-transform hover:scale-110">
                                        @else
                                            <div class="w-14 h-14 flex-shrink-0 aspect-square rounded-2xl bg-gradient-to-br from-cuan-green to-cuan-dark flex items-center justify-center border-2 border-white shadow-lg shadow-cuan-green/20">
                                                <i class="fas fa-utensils text-white text-sm"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-bold text-gray-900 leading-tight">{{ $product->name }}</div>
                                            <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">
                                                {{ $product->unit->name ?? 'UNIT' }} • {{ $product->is_stock ? 'BISA STOK' : 'PESANAN' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    @if($product->category)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-blue-50 text-blue-600 border border-blue-100">
                                            {{ $product->category->name }}
                                        </span>
                                    @else
                                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-300">Kosong</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">
                                        Rp {{ number_format($product->hpp, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="text-sm font-bold text-cuan-green">
                                        Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    @php
                                        $margin = $product->margin_percent;
                                        $marginColor = $margin >= 30 ? 'text-cuan-green bg-cuan-green/10 border-cuan-green/20' : ($margin >= 15 ? 'text-amber-600 bg-amber-50 border-amber-100' : 'text-red-600 bg-red-50 border-red-100');
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $marginColor }}">
                                        {{ number_format($margin, 1) }}%
                                    </span>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <span class="status-badge inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border
                                                {{ $product->is_active ? 'bg-cuan-green/10 text-cuan-green border-cuan-green/10' : 'bg-gray-50 text-gray-400 border-gray-200' }}"
                                          data-status-badge
                                          data-product-id="{{ $product->id }}"
                                          data-active="{{ $product->is_active ? '1' : '0' }}">
                                        {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center gap-2">
                                        @can('generate barcode produk')
                                        <button type="button" 
                                                onclick="openBarcodeModal('{{ $product->id }}', '{{ $product->name }}', '{{ $product->barcode }}')"
                                                class="w-9 h-9 flex items-center justify-center rounded-xl bg-purple-50 text-purple-500 hover:bg-purple-500 hover:text-white transition-all active:scale-95"
                                                title="Barcode">
                                            <i class="fas fa-barcode text-xs"></i>
                                        </button>
                                        @endcan

                                        @can('lihat detail produk')
                                        <a href="{{ route('products-hpp.show', $product->id) }}"
                                           class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 hover:bg-gray-100 transition-all active:scale-95"
                                           title="Detail">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        @endcan

                                        @can('edit produk')
                                        <a href="{{ route('products-hpp.edit', $product->id) }}"
                                           class="w-9 h-9 flex items-center justify-center rounded-xl bg-cuan-green/10 text-cuan-green hover:bg-cuan-green hover:text-white transition-all active:scale-95"
                                           title="Edit">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        @endcan

                                        @can('aktifkan nonaktifkan produk')
                                        <form action="{{ route('products-hpp.toggle-status', $product->id) }}"
                                              method="POST"
                                              class="inline ajax-toggle-status"
                                              data-product-id="{{ $product->id }}">
                                            @csrf
                                            <button type="submit"
                                                    class="w-9 h-9 flex items-center justify-center rounded-xl transition-all active:scale-95
                                                        {{ $product->is_active ? 'bg-gray-50 text-gray-400 hover:bg-gray-100' : 'bg-cuan-green/10 text-cuan-green hover:bg-cuan-green hover:text-white' }}"
                                                    title="{{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <i class="fas fa-{{ $product->is_active ? 'toggle-on' : 'toggle-off' }} text-xs"></i>
                                            </button>
                                        </form>
                                        @endcan

                                        @can('hapus produk')
                                        <form action="{{ route('products-hpp.destroy', $product->id) }}"
                                              method="POST"
                                              class="inline confirm-delete"
                                              data-name="{{ $product->name }}">
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
                                <td colspan="8" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-center">
                                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                            <i class="fas fa-box-open text-3xl text-gray-300"></i>
                                        </div>
                                        <h3 class="text-base font-semibold text-gray-900 mb-1">Belum ada produk</h3>
                                        <p class="text-sm text-gray-500 mb-4 max-w-sm">
                                            Mulai dengan menambahkan produk pertama Anda untuk pengelolaan inventaris yang lebih baik.
                                        </p>
                                        @can('buat produk')
                                        <a href="{{ route('products-hpp.create') }}"
                                           class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-6 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                                            <i class="fas fa-plus-circle text-xs"></i>
                                            Tambah Produk
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
            @if($products->hasPages())
                <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/30">
                    {{ $products->links() }}
                </div>
            @endif
        </x-card-container>
    </div>

    {{-- Barcode Modal --}}
    <div id="barcodeModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeBarcodeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-purple-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-barcode text-purple-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Barcode Produk
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 mb-4" id="barcodeProductTitle">
                                    Name
                                </p>
                                
                                <div id="barcodeDisplayArea" class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 min-h-[150px]">
                                    <img id="barcodeImage" src="" alt="Barcode" class="max-w-full h-auto shadow-sm p-2 bg-white hidden">
                                    <p id="barcodeValue" class="mt-2 text-lg font-mono font-bold tracking-widest text-gray-800"></p>
                                    <p id="noBarcodeMsg" class="text-gray-400 italic hidden">Produk ini belum memiliki barcode.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    @can('unduh barcode produk')
                    <a id="downloadBarcodeBtn" href="#" target="_blank" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        <i class="fas fa-download mr-2"></i> Download
                    </a>
                    @endcan
                    {{-- Assuming we don't have a specific print barcode permission yet, but let's use cetak struk as a proxy or just let it be if they can see it --}}
                    <button type="button" onclick="printBarcode()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        <i class="fas fa-print mr-2"></i> Print
                    </button>
                    <button type="button" onclick="closeBarcodeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const searchInput = document.getElementById('searchTerm');
    const filterCategory = document.getElementById('filterCategory');
    const filterStatus = document.getElementById('filterStatus');
    const productRows = document.querySelectorAll('.product-row');

    function updateStatusBadge(productId, isActive) {
        const badge = document.querySelector(`[data-status-badge][data-product-id="${productId}"]`);
        if (!badge) return;

        badge.classList.remove('bg-cuan-green/10', 'text-cuan-green', 'border-cuan-green/10', 'bg-gray-50', 'text-gray-400', 'border-gray-200');

        if (isActive) {
            badge.classList.add('bg-cuan-green/10', 'text-cuan-green', 'border-cuan-green/10');
            badge.textContent = 'Aktif';
        } else {
            badge.classList.add('bg-gray-50', 'text-gray-400', 'border-gray-200');
            badge.textContent = 'Nonaktif';
        }

        badge.dataset.active = isActive ? '1' : '0';
    }

    // AJAX Toggle Status with Confirmation
    document.querySelectorAll('form.ajax-toggle-status').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            const icon = btn?.querySelector('i');
            const productId = this.getAttribute('data-product-id');
            const isActive = btn.classList.contains('text-gray-400'); // if it was inactive, it has gray color-ish logic (wait, let's check class)
            // Actually let's use the title or a data attribute for simpler logic
            const currentStatus = btn.title === 'Aktifkan' ? 'aktifkan' : 'nonaktifkan';
            
            Swal.fire({
                title: `${currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1)} Produk?`,
                text: `Apakah Anda yakin ingin ${currentStatus} produk ini?`,
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
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        btn?.setAttribute('disabled', 'disabled');
                        const res = await fetch(this.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            }
                        });

                        if (!res.ok) throw new Error(await res.text() || 'Request gagal');

                        const data = await res.json();
                        const newIsActive = !!data.is_active;

                        btn.classList.remove(
                            'bg-gray-50', 'text-gray-400', 'hover:bg-gray-100',
                            'bg-cuan-green/10', 'text-cuan-green', 'hover:bg-cuan-green', 'hover:text-white'
                        );

                        if (newIsActive) {
                            btn.classList.add('bg-gray-50', 'text-gray-400', 'hover:bg-gray-100');
                            btn.title = 'Nonaktifkan';
                        } else {
                            btn.classList.add('bg-cuan-green/10', 'text-cuan-green', 'hover:bg-cuan-green', 'hover:text-white');
                            btn.title = 'Aktifkan';
                        }

                        if (icon) {
                            icon.classList.remove('fa-toggle-on', 'fa-toggle-off');
                            icon.classList.add(newIsActive ? 'fa-toggle-on' : 'fa-toggle-off');
                        }

                        updateStatusBadge(productId, newIsActive);

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: `Status produk berhasil diperbarui.`,
                            showConfirmButton: false,
                            timer: 2000,
                            iconColor: '#658C58',
                            customClass: {
                                popup: 'rounded-3xl border-none shadow-2xl',
                                title: 'font-black text-gray-900',
                                htmlContainer: 'text-sm font-medium text-gray-500'
                            }
                        });

                    } catch (err) {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal mengubah status produk.',
                            confirmButtonColor: '#ef4444',
                            customClass: {
                                popup: 'rounded-3xl border-none shadow-2xl'
                            }
                        });
                    } finally {
                        btn?.removeAttribute('disabled');
                    }
                }
            });
        });
    });

    // Filtering logic
    function filterProducts() {
        const searchTerm = (searchInput.value || '').toLowerCase();
        const categoryFilter = filterCategory.value;
        const statusFilter = filterStatus.value;

        productRows.forEach(row => {
            const name = row.dataset.name || '';
            const code = row.dataset.code || '';
            const category = row.dataset.category || '';
            const status = row.dataset.status || '';

            const matchesSearch = !searchTerm || name.includes(searchTerm) || code.includes(searchTerm);
            const matchesCategory = !categoryFilter || category === categoryFilter;
            const matchesStatus = !statusFilter || status === statusFilter;

            row.style.display = (matchesSearch && matchesCategory && matchesStatus) ? '' : 'none';
        });
    }

    if (searchInput) searchInput.addEventListener('input', filterProducts);
    if (filterCategory) filterCategory.addEventListener('change', filterProducts);
    if (filterStatus) filterStatus.addEventListener('change', filterProducts);

    // Global SweetAlert2 notification handler
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
                title: 'Hapus Produk?',
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
});

// Barcode Modal Functions (Product specific)
function openBarcodeModal(id, name, barcode) {
    document.getElementById('barcodeProductTitle').textContent = name;
    const img = document.getElementById('barcodeImage');
    const val = document.getElementById('barcodeValue');
    const noMsg = document.getElementById('noBarcodeMsg');
    const dlBtn = document.getElementById('downloadBarcodeBtn');
    
    if (barcode) {
        const baseUrl = "{{ route('products-hpp.index') }}";
        const previewUrl = `${baseUrl}/${id}/barcode-preview`;
        const downloadUrl = `${baseUrl}/${id}/barcode-download`;

        img.src = previewUrl;
        img.classList.remove('hidden');
        val.textContent = barcode;
        val.classList.remove('hidden');
        noMsg.classList.add('hidden');
        
        dlBtn.href = downloadUrl;
        dlBtn.classList.remove('opacity-50', 'pointer-events-none');
    } else {
        img.src = "";
        img.classList.add('hidden');
        val.textContent = "";
        val.classList.add('hidden');
        noMsg.classList.remove('hidden');
        
        dlBtn.href = "#";
        dlBtn.classList.add('opacity-50', 'pointer-events-none');
    }
    
    document.getElementById('barcodeModal').classList.remove('hidden');
}

function closeBarcodeModal() {
    document.getElementById('barcodeModal').classList.add('hidden');
}

function printBarcode() {
    const content = document.getElementById('barcodeDisplayArea').innerHTML;
    const name = document.getElementById('barcodeProductTitle').textContent;
    const win = window.open('', '', 'height=600,width=800');
    
    win.document.write('<html><head><title>Print Barcode</title>');
    win.document.write('<style>');
    win.document.write('body { font-family: sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; margin: 0; }');
    win.document.write('.barcode-container { text-align: center; }');
    win.document.write('img { max-width: 100%; height: auto; }');
    win.document.write('p { margin-top: 10px; font-size: 24px; font-weight: bold; font-family: monospace; }');
    win.document.write('</style>');
    win.document.write('</head><body>');
    win.document.write('<h2>' + name + '</h2>');
    win.document.write('<div class="barcode-container">' + content + '</div>');
    win.document.write('</body></html>');
    
    win.document.close();
    win.focus();
    
    setTimeout(() => {
        win.print();
        win.close();
    }, 500);
}
</script>
@endpush

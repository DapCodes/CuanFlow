@extends('layouts.app')

@section('title', 'Mulai Stock Opname - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('stock-opname.index') }}" class="text-gray-500 hover:text-gray-700">Stock Opname</a>
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Baru</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100">
                        <i class="fas fa-plus-circle text-sm"></i>
                    </span>
                    <span>Mulai Stock Opname Baru</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Pilih tipe opname dan item yang ingin dicek. Sistem akan menyiapkan lembar kerja untuk Anda.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                <a href="{{ route('stock-opname.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-white border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 shadow-sm transition-all">
                    <i class="fas fa-arrow-left text-sm"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </section>

        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-exclamation-circle mt-0.5 text-red-500"></i>
                <p class="text-red-800">{{ session('error') }}</p>
            </div>
        @endif
        
        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm">
                <ul class="list-disc pl-5 text-red-800">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('stock-opname.store') }}" method="POST" class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-8">
            @csrf

            {{-- 1. Pilihan Tipe --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3 uppercase tracking-wide">1. Pilih Tipe Opname</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="relative flex items-start p-4 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 hover:border-emerald-500 transition-all group has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50">
                        <div class="flex items-center h-5">
                            <input type="radio" name="type" value="product" checked onchange="toggleItems('product')" class="focus:ring-emerald-500 h-4 w-4 text-emerald-600 border-gray-300">
                        </div>
                        <div class="ml-3 text-sm">
                            <span class="block font-medium text-gray-900 group-hover:text-emerald-700">Produk Jadi</span>
                            <span class="block text-gray-500">Opname stok produk penjualan.</span>
                        </div>
                    </label>
                    
                    <label class="relative flex items-start p-4 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 hover:border-emerald-500 transition-all group has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50">
                        <div class="flex items-center h-5">
                            <input type="radio" name="type" value="raw_material" onchange="toggleItems('raw_material')" class="focus:ring-emerald-500 h-4 w-4 text-emerald-600 border-gray-300">
                        </div>
                        <div class="ml-3 text-sm">
                            <span class="block font-medium text-gray-900 group-hover:text-emerald-700">Bahan Baku</span>
                            <span class="block text-gray-500">Opname stok bahan mentah.</span>
                        </div>
                    </label>

                    <label class="relative flex items-start p-4 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 hover:border-emerald-500 transition-all group has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50">
                        <div class="flex items-center h-5">
                            <input type="radio" name="type" value="all" onchange="toggleItems('all')" class="focus:ring-emerald-500 h-4 w-4 text-emerald-600 border-gray-300">
                        </div>
                        <div class="ml-3 text-sm">
                            <span class="block font-medium text-gray-900 group-hover:text-emerald-700">Semua Item</span>
                            <span class="block text-gray-500">Gabungan produk & bahan baku.</span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- 2. Daftar Item Selection --}}
            <div id="itemsSelectionContainer" class="border-t border-gray-100 pt-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                    <label class="block text-sm font-medium text-gray-700 uppercase tracking-wide">2. Pilih Item yang akan dihitung</label>
                    
                    <div class="flex flex-wrap items-center gap-3">
                        {{-- Kategori Filter --}}
                        <select id="categoryFilter" onchange="filterItems()" class="rounded-lg border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500 py-1.5 px-3">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" data-type="{{ $cat->type }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" onclick="selectAll()" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                            <i class="fas fa-check-double mr-1"></i> Pilih Semua
                        </button>
                        <button type="button" onclick="deselectAll()" class="text-sm text-gray-500 hover:text-gray-700">
                            Clear
                        </button>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden max-h-[500px] overflow-y-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-100 text-gray-600 uppercase font-semibold text-xs sticky top-0 z-10">
                            <tr>
                                <th class="px-4 py-3 w-10 text-center">
                                    <input type="checkbox" id="masterCheckbox" onchange="toggleAllCheckboxes(this)" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                </th>
                                <th class="px-4 py-3">Nama Item</th>
                                <th class="px-4 py-3">Kategori</th>
                                <th class="px-4 py-3">Kode / SKU</th>
                                <th class="px-4 py-3 text-right">Stok Saat Ini</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody" class="divide-y divide-gray-200 bg-white">
                            {{-- Products --}}
                            @foreach($products as $product)
                                <tr class="item-row hover:bg-gray-50 cursor-pointer" 
                                    data-type="product" 
                                    data-category="{{ $product->category_id }}" 
                                    onclick="toggleRow(this)">
                                    <td class="px-4 py-3 text-center">
                                        <input type="checkbox" name="items[]" value="product_{{ $product->id }}" class="item-checkbox rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 pointer-events-none"> {{-- Pointer events none to let row click handle it --}}
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $product->name }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $product->category->name ?? '-' }}</td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $product->code ?? '-' }}</td>
                                    <td class="px-4 py-3 text-right text-gray-600">{{ number_format($product->getStockQuantity(auth()->user()->outlet_id), 0) }}</td>
                                </tr>
                            @endforeach

                            {{-- Raw Materials --}}
                            @foreach($rawMaterials as $material)
                                <tr class="item-row hover:bg-gray-50 cursor-pointer" 
                                    data-type="raw_material" 
                                    data-category="{{ $material->category_id }}" 
                                    onclick="toggleRow(this)">
                                    <td class="px-4 py-3 text-center">
                                        <input type="checkbox" name="items[]" value="raw_material_{{ $material->id }}" class="item-checkbox rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 pointer-events-none">
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $material->name }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $material->category->name ?? '-' }}</td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $material->code ?? '-' }}</td>
                                    <td class="px-4 py-3 text-right text-gray-600">{{ number_format($material->getStockQuantity(auth()->user()->outlet_id), 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    {{-- Empty State --}}
                    <div id="emptyState" class="hidden p-8 text-center text-gray-500">
                        <i class="fas fa-search text-2xl mb-2 text-gray-300"></i>
                        <p>Tidak ada item yang sesuai dengan filter.</p>
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-500 flex justify-end items-center gap-1">
                    <span id="selectedCount">0</span> item terpilih
                </p>
            </div>

            {{-- 3. Notes --}}
            <div class="border-t border-gray-100 pt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1 uppercase tracking-wide">3. Catatan (Opsional)</label>
                <textarea id="notes" name="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="Contoh: Opname bulanan periode Januari 2025"></textarea>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end">
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-6 py-3 text-sm font-semibold text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1 shadow-md transition-all">
                    Buat Sesi Opname
                    <i class="fas fa-arrow-right text-xs"></i>
                </button>
            </div>
        </form>
    </div>
</main>

@push('scripts')
<script>
    function toggleRow(row) {
        const checkbox = row.querySelector('.item-checkbox');
        checkbox.checked = !checkbox.checked;
        updateSelectedCount();
    }

    // Stop propagation on checkbox directly to avoid double toggle
    document.querySelectorAll('.item-checkbox').forEach(cb => {
        cb.addEventListener('click', function(e) {
            e.stopPropagation();
            updateSelectedCount(); // manually update if clicked directly (though pointer-events-none prevents this on td, but just in case)
        });
    });

    function toggleItems(type) {
        // Filter Categories First
        const catOptions = document.querySelectorAll('#categoryFilter option');
        const currentCat = document.getElementById('categoryFilter').value;
        let isCurrentCatHidden = false;

        catOptions.forEach(opt => {
            if (opt.value === "") return; // Skip "Semua Kategori"
            const catType = opt.dataset.type;
            
            // Assuming categories have 'product' or 'raw_material' types matching our filter
            // If type is 'all', show all. Else show only matching.
            if (type === 'all' || catType === type) {
                opt.hidden = false;
                opt.disabled = false;
            } else {
                opt.hidden = true;
                opt.disabled = true;
                if (opt.value == currentCat) isCurrentCatHidden = true;
            }
        });
        
        if (isCurrentCatHidden) {
            document.getElementById('categoryFilter').value = "";
        }

        // Toggle Rows
        const rows = document.querySelectorAll('.item-row');
        rows.forEach(row => {
            const rowType = row.dataset.type;
            if (type === 'all' || rowType === type) {
                row.classList.remove('hidden-by-type');
                // Also respect category filter
                filterVerify(row);
            } else {
                row.classList.add('hidden-by-type');
                row.style.display = 'none'; // Ensure hidden
            }
        });
        
        filterItems(); // Re-apply current category filter
    }

    function filterItems() {
        const categoryId = document.getElementById('categoryFilter').value;
        const type = document.querySelector('input[name="type"]:checked').value;
        const rows = document.querySelectorAll('.item-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const rowType = row.dataset.type;
            const rowCat = row.dataset.category;
            
            // Check Type
            let typeMatch = (type === 'all' || rowType === type);
            // Check Category
            let catMatch = (!categoryId || rowCat == categoryId);
            
            if (typeMatch && catMatch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('emptyState').classList.toggle('hidden', visibleCount > 0);
    }
    
    // Auxiliary for toggleItems reuse
    function filterVerify(row) {
        // Just empty stub, real logic in filterItems which is called after toggleItems
    }

    function selectAll() {
        const visibleRows = Array.from(document.querySelectorAll('.item-row')).filter(row => row.style.display !== 'none');
        visibleRows.forEach(row => {
            row.querySelector('.item-checkbox').checked = true;
        });
        updateSelectedCount();
    }

    function deselectAll() {
        const visibleRows = Array.from(document.querySelectorAll('.item-row')).filter(row => row.style.display !== 'none');
        visibleRows.forEach(row => {
            row.querySelector('.item-checkbox').checked = false;
        });
        updateSelectedCount();
    }
    
    function toggleAllCheckboxes(masterCheckbox) {
        const isChecked = masterCheckbox.checked;
         const visibleRows = Array.from(document.querySelectorAll('.item-row')).filter(row => row.style.display !== 'none');
         visibleRows.forEach(row => {
            row.querySelector('.item-checkbox').checked = isChecked;
        });
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const count = document.querySelectorAll('.item-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = count;
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        toggleItems('product'); // Default
    });
</script>

<style>
    /* Custom utility not in tailwind by default if needed, but display none is handled inline by JS mostly */
    .hidden-by-type {
        display: none !important;
    }
</style>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'Mulai Stock Opname - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('stock-opname.index') }}" class="text-gray-600 hover:text-gray-900 font-medium">Stock Opname</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Baru</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Mulai Sesi Opname
                </h1>
                <p class="mt-1 text-sm text-gray-500 font-medium">
                    Sistem akan menyiapkan lembar kerja berdasarkan tipe item yang Anda pilih.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('stock-opname.index') }}"
                   class="inline-flex items-center justify-center h-11 px-6 bg-white text-gray-700 border border-gray-200 rounded-xl text-sm font-black hover:bg-gray-50 transition-all active:scale-95 shadow-sm">
                    Kembali
                </a>
            </div>
        </section>

        <form action="{{ route('stock-opname.store') }}" method="POST" class="space-y-6">
            @csrf

            <x-card-container>
                <div class="p-6 space-y-8">
                    {{-- 1. Pilihan Tipe --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 ml-1">1. Pilih Tipe Opname</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label class="relative flex items-start p-5 rounded-2xl border-2 border-gray-100 cursor-pointer hover:bg-gray-50 hover:border-cuan-green/30 transition-all group has-[:checked]:border-cuan-green has-[:checked]:bg-cuan-green/5">
                                <div class="flex items-center h-5">
                                    <input type="radio" name="type" value="product" checked onchange="toggleItems('product')" class="w-5 h-5 text-cuan-green border-gray-200 focus:ring-cuan-green/20">
                                </div>
                                <div class="ml-4">
                                    <span class="block text-sm font-black text-gray-900 group-hover:text-cuan-dark transition-colors">Produk Jadi</span>
                                    <span class="block text-[11px] font-bold text-gray-400 mt-0.5">Opname stok produk siap jual.</span>
                                </div>
                            </label>
                            
                            <label class="relative flex items-start p-5 rounded-2xl border-2 border-gray-100 cursor-pointer hover:bg-gray-50 hover:border-cuan-green/30 transition-all group has-[:checked]:border-cuan-green has-[:checked]:bg-cuan-green/5">
                                <div class="flex items-center h-5">
                                    <input type="radio" name="type" value="raw_material" onchange="toggleItems('raw_material')" class="w-5 h-5 text-cuan-green border-gray-200 focus:ring-cuan-green/20">
                                </div>
                                <div class="ml-4">
                                    <span class="block text-sm font-black text-gray-900 group-hover:text-cuan-dark transition-colors">Bahan Baku</span>
                                    <span class="block text-[11px] font-bold text-gray-400 mt-0.5">Opname stok bahan mentah.</span>
                                </div>
                            </label>

                            <label class="relative flex items-start p-5 rounded-2xl border-2 border-gray-100 cursor-pointer hover:bg-gray-50 hover:border-cuan-green/30 transition-all group has-[:checked]:border-cuan-green has-[:checked]:bg-cuan-green/5">
                                <div class="flex items-center h-5">
                                    <input type="radio" name="type" value="all" onchange="toggleItems('all')" class="w-5 h-5 text-cuan-green border-gray-200 focus:ring-cuan-green/20">
                                </div>
                                <div class="ml-4">
                                    <span class="block text-sm font-black text-gray-900 group-hover:text-cuan-dark transition-colors">Semua Item</span>
                                    <span class="block text-[11px] font-bold text-gray-400 mt-0.5">Gabungan produk & bahan baku.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- 2. Daftar Item Selection --}}
                    <div id="itemsSelectionContainer" class="space-y-4">
                        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">2. Pilih Item yang akan dihitung</label>
                            
                            <div class="flex flex-wrap items-center gap-3">
                                <div class="relative w-full sm:w-48">
                                    <select id="categoryFilter" onchange="filterItems()" class="w-full appearance-none rounded-xl border border-gray-200 pl-4 pr-10 py-2 text-[11px] font-black uppercase tracking-widest text-gray-900 focus:ring-4 focus:ring-cuan-green/5 focus:border-cuan-green transition-all bg-white">
                                        <option value="">Semua Kategori</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" data-type="{{ $cat->type }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 text-[8px] pointer-events-none"></i>
                                </div>
                                <button type="button" onclick="selectAll()" class="text-[10px] font-black text-cuan-green uppercase tracking-widest hover:underline px-2">
                                    Pilih Semua
                                </button>
                                <button type="button" onclick="deselectAll()" class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:underline px-2">
                                    Reset
                                </button>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-gray-100 overflow-hidden">
                            <div class="max-h-[400px] overflow-y-auto custom-scrollbar">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100 sticky top-0 z-10">
                                        <tr>
                                            <th class="px-6 py-4 w-12 text-center">
                                                <input type="checkbox" id="masterCheckbox" onchange="toggleAllCheckboxes(this)" class="w-4 h-4 text-cuan-green border-gray-200 rounded focus:ring-cuan-green/20">
                                            </th>
                                            <th class="px-6 py-4">Nama Item</th>
                                            <th class="px-6 py-4">Kategori</th>
                                            <th class="px-6 py-4">Kode/SKU</th>
                                            <th class="px-6 py-4 text-right">Stok Sistem</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTableBody" class="divide-y divide-gray-100 bg-white">
                                        {{-- Products --}}
                                        @foreach($products as $product)
                                            <tr class="item-row hover:bg-gray-50 transition-colors cursor-pointer" 
                                                data-type="product" 
                                                data-category="{{ $product->category_id }}" 
                                                onclick="toggleRow(this)">
                                                <td class="px-6 py-4 text-center">
                                                    <input type="checkbox" name="items[]" value="product_{{ $product->id }}" class="item-checkbox w-4 h-4 text-cuan-green border-gray-200 rounded focus:ring-cuan-green/20 pointer-events-none">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-[11px] font-bold text-gray-900 capitalize">{{ $product->name }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{ $product->category->name ?? '-' }}</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="text-[10px] font-black font-mono text-gray-500 bg-gray-100 px-2 py-0.5 rounded border border-gray-200">{{ $product->code ?? '-' }}</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                                    <div class="text-[11px] font-black text-gray-900">{{ number_format($product->getStockQuantity(auth()->user()->outlet_id), 0) }}</div>
                                                </td>
                                            </tr>
                                        @endforeach

                                        {{-- Raw Materials --}}
                                        @foreach($rawMaterials as $material)
                                            <tr class="item-row hover:bg-gray-50 transition-colors cursor-pointer" 
                                                data-type="raw_material" 
                                                data-category="{{ $material->category_id }}" 
                                                onclick="toggleRow(this)">
                                                <td class="px-6 py-4 text-center">
                                                    <input type="checkbox" name="items[]" value="raw_material_{{ $material->id }}" class="item-checkbox w-4 h-4 text-cuan-green border-gray-200 rounded focus:ring-cuan-green/20 pointer-events-none">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-[11px] font-bold text-gray-900 capitalize">{{ $material->name }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{ $material->category->name ?? '-' }}</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="text-[10px] font-black font-mono text-gray-500 bg-gray-100 px-2 py-0.5 rounded border border-gray-200">{{ $material->code ?? '-' }}</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                                    <div class="text-[11px] font-black text-gray-900">{{ number_format($material->getStockQuantity(auth()->user()->outlet_id), 0) }}</div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                
                                {{-- Empty State --}}
                                <div id="emptyState" class="hidden py-20 text-center">
                                    <i class="fas fa-search text-gray-200 text-3xl mb-4"></i>
                                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tidak Ada Item Ditemukan</h4>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end pr-1">
                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">
                                <span id="selectedCount" class="text-cuan-green">0</span> Item Terpilih
                            </span>
                        </div>
                    </div>

                    {{-- 3. Notes --}}
                    <div class="space-y-2">
                        <label for="notes" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">3. Catatan (Opsional)</label>
                        <textarea id="notes" name="notes" rows="3" class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/5 focus:border-cuan-green transition-all" placeholder="Contoh: Opname bulanan periode Maret 2026"></textarea>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="h-12 px-10 bg-cuan-green text-white rounded-xl text-sm font-black hover:bg-cuan-dark transition-all active:scale-95 shadow-lg shadow-cuan-green/20 group">
                            Buat Sesi Opname
                            <i class="fas fa-arrow-right ml-2 text-xs group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </div>
                </div>
            </x-card-container>
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

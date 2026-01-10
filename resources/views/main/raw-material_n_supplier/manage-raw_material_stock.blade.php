@extends('layouts.app')

@section('title', 'Kelola Stok - ' . $rawMaterial->name . ' - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('raw-materials.index') }}" class="text-gray-500 hover:text-red-600 transition-colors">Bahan Baku</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Kelola Stok</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Header Section --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-500 border border-red-100">
                        <i class="fas fa-boxes text-sm"></i>
                    </span>
                    <span>Kelola Stok Bahan Baku</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Atur stok masuk dan keluar untuk bahan baku <strong>{{ $rawMaterial->name }}</strong>.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('raw-materials.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-sm transition-all">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column: Info & Context --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Material Info Card --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-info-circle text-gray-400"></i> Detail Bahan Baku
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-4 mb-4">
                            @if($rawMaterial->image)
                                <img src="{{ Storage::url($rawMaterial->image) }}" alt="{{ $rawMaterial->name }}" class="h-16 w-16 rounded-lg object-cover border border-gray-200 shadow-sm">
                            @else
                                <div class="h-16 w-16 rounded-lg bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center border border-gray-200">
                                    <i class="fas fa-cube text-gray-400 text-2xl"></i>
                                </div>
                            @endif
                            <div>
                                <h4 class="font-bold text-gray-900">{{ $rawMaterial->name }}</h4>
                                <span class="text-xs font-mono bg-gray-100 text-gray-600 px-2 py-0.5 rounded border border-gray-200">{{ $rawMaterial->code }}</span>
                            </div>
                        </div>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between items-center py-2 border-b border-gray-50 text-gray-600">
                                <span>Kategori</span>
                                <span class="font-medium text-gray-900">{{ $rawMaterial->category->name ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-50 text-gray-600">
                                <span>Supplier</span>
                                <span class="font-medium text-gray-900">{{ $rawMaterial->supplier->name ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-50 text-gray-600">
                                <span>Min. Stok</span>
                                <span class="font-medium text-gray-900">{{ number_format($rawMaterial->min_stock) }} {{ $rawMaterial->unit->name }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 text-gray-600">
                                <span>Harga Beli (Ref)</span>
                                <span class="font-medium text-gray-900">Rp {{ number_format($rawMaterial->purchase_price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Current Stock Status --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
                    <div class="text-center">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 mb-1">Total Stok ({{ $rawMaterial->unit->name }})</p>
                        <h2 class="text-4xl font-bold {{ $currentStock <= $rawMaterial->min_stock ? 'text-red-500' : 'text-gray-900' }}">
                            {{ number_format($currentStock, 2) }}
                        </h2>
                    </div>
                </div>

                {{-- Status Breakdown --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="p-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-chart-pie"></i>
                            Rincian Kondisi Stok
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        {{-- Expired --}}
                        <div class="p-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">
                                    <i class="fas fa-calendar-times text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Kadaluarsa</p>
                                    <p class="text-lg font-bold text-red-600">{{ number_format($expiredQty, 2) }}</p>
                                </div>
                            </div>
                            <a href="{{ route('raw-materials.stock-show', $rawMaterial) }}" class="text-[10px] font-bold px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-100 hover:bg-red-100 transition-colors">DETAIL</a>
                        </div>

                        {{-- Expiring --}}
                        <div class="p-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-yellow-100 text-yellow-600 flex items-center justify-center">
                                    <i class="fas fa-hourglass-half text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Segera Kadaluarsa</p>
                                    <p class="text-lg font-bold text-yellow-600">{{ number_format($expiringQty, 2) }}</p>
                                </div>
                            </div>
                            <a href="{{ route('raw-materials.stock-show', $rawMaterial) }}" class="text-[10px] font-bold px-2 py-0.5 rounded bg-yellow-50 text-yellow-700 border border-yellow-100 hover:bg-yellow-100 transition-colors">DETAIL</a>
                        </div>

                        {{-- Valid --}}
                        <div class="p-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                    <i class="fas fa-check-circle text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Aman/Valid</p>
                                    <p class="text-lg font-bold text-emerald-600">{{ number_format($validQty, 2) }}</p>
                                </div>
                            </div>
                            <a href="{{ route('raw-materials.stock-show', $rawMaterial) }}" class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-100 hover:bg-emerald-100 transition-colors">DETAIL</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Form --}}
            <div class="lg:col-span-2">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-5">
                        <h3 class="font-semibold text-gray-900">Form Transaksi Stok</h3>
                    </div>
                    
                    <form action="{{ route('raw-materials.update-stock', $rawMaterial) }}" method="POST" id="stockForm" class="p-6 space-y-6">
                        @csrf
                        
                        {{-- Transaction Type --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Jenis Transaksi</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="type" value="add" class="peer sr-only" checked>
                                    <div class="p-4 border border-gray-200 rounded-xl transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:ring-1 peer-checked:ring-emerald-500 hover:border-emerald-200 bg-white">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                                    <i class="fas fa-plus"></i>
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-900">Tambah Stok</p>
                                                    <p class="text-xs text-gray-500">Stok masuk / Pembelian</p>
                                                </div>
                                            </div>
                                            <i class="fas fa-check-circle text-emerald-500 opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                        </div>
                                    </div>
                                </label>

                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="type" value="reduce" class="peer sr-only">
                                    <div class="p-4 border border-gray-200 rounded-xl transition-all peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:ring-1 peer-checked:ring-red-500 hover:border-red-200 bg-white">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">
                                                    <i class="fas fa-minus"></i>
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-900">Kurangi Stok</p>
                                                    <p class="text-xs text-gray-500">Stok keluar / Koreksi</p>
                                                </div>
                                            </div>
                                            <i class="fas fa-check-circle text-red-500 opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Quantity --}}
                            <div class="col-span-1">
                                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">
                                    Jumlah <span class="text-red-500">*</span>
                                </label>
                                <div class="relative rounded-lg shadow-sm">
                                    <input type="number" name="quantity" id="quantity" step="0.01" min="0.01" required
                                           value="{{ old('quantity') }}"
                                           class="block w-full rounded-lg border-gray-300 pl-4 pr-12 text-sm focus:border-red-500 focus:ring-red-500 py-2.5 shadow-sm"
                                           placeholder="0.00">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">{{ $rawMaterial->unit->name }}</span>
                                    </div>
                                </div>
                                @error('quantity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Unit Price (Only for Add) --}}
                            <div class="col-span-1 purchase-field">
                                <label for="unit_price" class="block text-sm font-medium text-gray-700 mb-1">
                                    Harga Satuan (Rp)
                                </label>
                                <div class="relative rounded-lg shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" name="unit_price" id="unit_price" step="0.01" min="0"
                                           value="{{ old('unit_price', $rawMaterial->purchase_price) }}"
                                           class="block w-full rounded-lg border-gray-300 pl-10 text-sm focus:border-red-500 focus:ring-red-500 py-2.5 shadow-sm">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Harga beli per unit untuk transaksi ini.</p>
                                @error('unit_price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Purchase Information Section (Only for Add) --}}
                        <div class="purchase-field space-y-4 pt-4 border-t border-gray-100">
                            <h4 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-file-invoice-dollar text-gray-400"></i> Informasi Pembelian & Pengeluaran
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Expense Category --}}
                                <div>
                                    <label for="expense_category_id" class="block text-sm font-medium text-gray-700 mb-1">
                                        Kategori Pengeluaran <span class="text-red-500">*</span>
                                    </label>
                                    <select name="expense_category_id" id="expense_category_id" 
                                            class="block w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500 py-2.5 shadow-sm">
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach($expenseCategories as $category)
                                            <option value="{{ $category->id }}" {{ old('expense_category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }} ({{ $category->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('expense_category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                {{-- Payment Method --}}
                                <div>
                                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">
                                        Metode Pembayaran <span class="text-red-500">*</span>
                                    </label>
                                    <select name="payment_method" id="payment_method" 
                                            class="block w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500 py-2.5 shadow-sm">
                                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                                        <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                        <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Kartu Debit/Kredit</option>
                                    </select>
                                    @error('payment_method') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Additional Info Fields --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100">
                             {{-- Batch Number --}}
                             <div class="purchase-field">
                                <label for="batch_number" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nomor Batch
                                </label>
                                <input type="text" name="batch_number" id="batch_number" 
                                       value="{{ old('batch_number') }}"
                                       class="block w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500 py-2.5 shadow-sm"
                                       placeholder="Opsional">
                            </div>

                            {{-- Expired Date --}}
                            <div class="purchase-field">
                                <label for="expired_at" class="block text-sm font-medium text-gray-700 mb-1">
                                    Tanggal Kadaluarsa
                                </label>
                                <input type="date" name="expired_at" id="expired_at" 
                                       value="{{ old('expired_at') }}"
                                       class="block w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500 py-2.5 shadow-sm">
                            </div>

                             {{-- Notes --}}
                             <div class="col-span-1 md:col-span-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                                <textarea name="notes" id="notes" rows="3" 
                                          class="block w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500 shadow-sm"
                                          placeholder="Tambahkan catatan jika perlu...">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="pt-6 border-t border-gray-200 flex items-center justify-end gap-3">
                            <a href="{{ route('raw-materials.index') }}" 
                               class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all">
                                Batal
                            </a>
                            <button type="submit" 
                                    class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-md transition-all">
                                Simpan Transaksi
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const purchaseFields = document.querySelectorAll('.purchase-field');
    const quantityInput = document.getElementById('quantity');

    function toggleFields() {
        const isAdd = document.querySelector('input[name="type"]:checked').value === 'add';
        
        purchaseFields.forEach(el => {
            if (isAdd) {
                el.classList.remove('hidden');
                const inputs = el.querySelectorAll('input, select');
                inputs.forEach(input => {
                    if (input.id === 'expense_category_id' || input.id === 'payment_method') {
                        input.setAttribute('required', 'required');
                    }
                });
            } else {
                el.classList.add('hidden');
                const inputs = el.querySelectorAll('input, select');
                inputs.forEach(input => {
                    input.removeAttribute('required');
                });
            }
        });
    }

    typeRadios.forEach(radio => {
        radio.addEventListener('change', toggleFields);
    });

    toggleFields();

    document.getElementById('stockForm').addEventListener('submit', function(e) {
        const type = document.querySelector('input[name="type"]:checked').value;
        const qty = parseFloat(quantityInput.value);
        const currentStock = {{ $currentStock }};

        if (type === 'reduce' && qty > currentStock) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Opps!',
                text: 'Jumlah pengurangan tidak boleh melebihi stok tersedia (' + currentStock + ')',
                confirmButtonColor: '#EF4444'
            });
        }
    });
});
</script>
@endpush
@endsection
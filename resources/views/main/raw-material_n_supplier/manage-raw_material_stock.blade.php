@extends('layouts.app')

@section('title', 'Kelola Stok - ' . $rawMaterial->name . ' - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('raw-materials.index') }}" class="text-gray-500 hover:text-orange-600 transition-colors">Stok Bahan Baku</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Kelola Stok</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4">
    <div class="max-w-7xl mx-auto">
        
        <x-card-container>
            <!-- Header -->
            <div class="bg-gradient-to-br from-orange-50 to-red-50 p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                            Kelola Stok Bahan Baku
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Tambah atau kurangi stok bahan baku</p>
                    </div>
                    <a href="{{ route('raw-materials.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition-all shadow-sm border border-gray-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>

            <!-- Material Info -->
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-center gap-4">
                    @if($rawMaterial->image)
                    <img src="{{ Storage::url($rawMaterial->image) }}" alt="{{ $rawMaterial->name }}" class="h-20 w-20 rounded-lg object-cover border-2 border-gray-200 shadow-sm">
                    @else
                    <div class="h-20 w-20 rounded-lg bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center shadow-sm">
                        <i class="fas fa-cube text-white text-3xl"></i>
                    </div>
                    @endif
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-900">{{ $rawMaterial->name }}</h3>
                        <div class="flex flex-wrap gap-3 mt-2">
                            <span class="text-sm text-gray-600">
                                <i class="fas fa-barcode mr-1 text-gray-400"></i>
                                <span class="font-mono font-semibold">{{ $rawMaterial->code }}</span>
                            </span>
                            <span class="text-sm text-gray-600">
                                <i class="fas fa-tag mr-1 text-gray-400"></i>
                                {{ $rawMaterial->category->name ?? '-' }}
                            </span>
                            @if($rawMaterial->supplier)
                            <span class="text-sm text-gray-600">
                                <i class="fas fa-truck mr-1 text-gray-400"></i>
                                {{ $rawMaterial->supplier->name }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Stock Info -->
            <div class="p-6 bg-gradient-to-r from-orange-50 to-red-50 border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold">Stok Saat Ini</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">
                                    {{ number_format($currentStock, 0) }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">{{ $rawMaterial->unit->name ?? 'unit' }}</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-warehouse text-blue-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold">Minimum Stok</p>
                                <p class="text-2xl font-bold text-yellow-600 mt-1">
                                    {{ number_format($rawMaterial->min_stock, 0) }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">{{ $rawMaterial->unit->name ?? 'unit' }}</p>
                            </div>
                            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold">Status Stok</p>
                                @if($currentStock <= 0)
                                <p class="text-base font-bold text-red-600 mt-1">Habis</p>
                                @elseif($currentStock <= $rawMaterial->min_stock)
                                <p class="text-base font-bold text-yellow-600 mt-1">Menipis</p>
                                @else
                                <p class="text-base font-bold text-green-600 mt-1">Aman</p>
                                @endif
                            </div>
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center
                                {{ $currentStock <= 0 ? 'bg-red-100' : ($currentStock <= $rawMaterial->min_stock ? 'bg-yellow-100' : 'bg-green-100') }}">
                                <i class="fas {{ $currentStock <= 0 ? 'fa-times-circle text-red-600' : ($currentStock <= $rawMaterial->min_stock ? 'fa-exclamation-triangle text-yellow-600' : 'fa-check-circle text-green-600') }} text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Management Form -->
            <div class="p-6">
                <form action="{{ route('raw-materials.update-stock', $rawMaterial) }}" method="POST" id="stockForm">
                    @csrf
                    
                    <!-- Type Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-exchange-alt mr-1 text-gray-400"></i>
                            Jenis Transaksi
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="add" class="peer sr-only" checked>
                                <div class="p-4 border-2 border-gray-200 rounded-lg transition-all peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-gray-300">
                                    <div class="flex items-center justify-center mb-2">
                                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center peer-checked:bg-green-200">
                                            <i class="fas fa-plus text-green-600 text-xl"></i>
                                        </div>
                                    </div>
                                    <p class="text-center font-semibold text-gray-900">Tambah Stok</p>
                                    <p class="text-center text-xs text-gray-500 mt-1">Menambah stok bahan baku</p>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="reduce" class="peer sr-only">
                                <div class="p-4 border-2 border-gray-200 rounded-lg transition-all peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-gray-300">
                                    <div class="flex items-center justify-center mb-2">
                                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center peer-checked:bg-red-200">
                                            <i class="fas fa-minus text-red-600 text-xl"></i>
                                        </div>
                                    </div>
                                    <p class="text-center font-semibold text-gray-900">Kurangi Stok</p>
                                    <p class="text-center text-xs text-gray-500 mt-1">Mengurangi stok bahan baku</p>
                                </div>
                            </label>
                        </div>
                        @error('type')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Quantity Input -->
                    <div class="mb-6">
                        <label for="quantity" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-sort-numeric-up mr-1 text-gray-400"></i>
                            Jumlah <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" 
                                name="quantity" 
                                id="quantity" 
                                step="0.01" 
                                min="0.01"
                                value="{{ old('quantity') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('quantity') border-red-500 @enderror" 
                                placeholder="Masukkan jumlah"
                                required>
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">
                                {{ $rawMaterial->unit->name ?? 'unit' }}
                            </span>
                        </div>
                        @error('quantity')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Stok tersedia saat ini: <span class="font-semibold">{{ number_format($currentStock, 2) }} {{ $rawMaterial->unit->name ?? 'unit' }}</span>
                        </p>
                    </div>

                    <!-- Batch Number (Optional for incoming stock) -->
                    <div class="mb-6" id="batchNumberField">
                        <label for="batch_number" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-barcode mr-1 text-gray-400"></i>
                            Nomor Batch
                        </label>
                        <input type="text" 
                            name="batch_number" 
                            id="batch_number" 
                            value="{{ old('batch_number') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('batch_number') border-red-500 @enderror" 
                            placeholder="Masukkan nomor batch (opsional)">
                        @error('batch_number')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            Nomor batch dari supplier untuk tracking
                        </p>
                    </div>

                    <!-- Expired Date (Optional for incoming stock) -->
                    <div class="mb-6" id="expiredDateField">
                        <label for="expired_at" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar-times mr-1 text-gray-400"></i>
                            Tanggal Kadaluarsa
                        </label>
                        <input type="date" 
                            name="expired_at" 
                            id="expired_at" 
                            value="{{ old('expired_at') }}"
                            min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('expired_at') border-red-500 @enderror">
                        @error('expired_at')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            Tanggal kadaluarsa bahan baku (jika ada)
                        </p>
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-sticky-note mr-1 text-gray-400"></i>
                            Catatan
                        </label>
                        <textarea name="notes" 
                                id="notes" 
                                rows="3" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('notes') border-red-500 @enderror" 
                                placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                        @error('notes')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit" 
                                class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-orange-400 to-red-500 text-white rounded-lg font-semibold hover:from-orange-500 hover:to-red-600 transition-all shadow-md hover:shadow-lg">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('raw-materials.index') }}" 
                        class="inline-flex items-center justify-center px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                            <i class="fas fa-times mr-2"></i>
                            Batal
                        </a>
                    </div>
                </form>
            </div>

        </x-card-container>

    </div>
</main>

@push('scripts')
<script>
    // Auto-select type based on URL parameter
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const typeParam = urlParams.get('type');
        
        if (typeParam === 'add' || typeParam === 'reduce') {
            const radio = document.querySelector(`input[name="type"][value="${typeParam}"]`);
            if (radio) {
                radio.checked = true;
                radio.dispatchEvent(new Event('change'));
            }
        }
    });

    // Toggle batch number and expired date fields based on type
    function toggleAdditionalFields() {
        const type = document.querySelector('input[name="type"]:checked').value;
        const batchField = document.getElementById('batchNumberField');
        const expiredField = document.getElementById('expiredDateField');
        
        if (type === 'add') {
            batchField.classList.remove('hidden');
            expiredField.classList.remove('hidden');
        } else {
            batchField.classList.add('hidden');
            expiredField.classList.add('hidden');
            document.getElementById('batch_number').value = '';
            document.getElementById('expired_at').value = '';
        }
    }

    // Update form styling based on selected type
    document.querySelectorAll('input[name="type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const quantityInput = document.getElementById('quantity');
            if (this.value === 'add') {
                quantityInput.classList.remove('border-red-300', 'focus:ring-red-500', 'focus:border-red-500');
                quantityInput.classList.add('focus:ring-orange-500', 'focus:border-orange-500');
            } else {
                quantityInput.classList.remove('focus:ring-orange-500', 'focus:border-orange-500');
                quantityInput.classList.add('border-red-300', 'focus:ring-red-500', 'focus:border-red-500');
            }
            
            // Toggle additional fields
            toggleAdditionalFields();
        });
    });

    // Initial toggle on page load
    toggleAdditionalFields();

    // Form validation for reduce type
    document.getElementById('stockForm').addEventListener('submit', function(e) {
        const type = document.querySelector('input[name="type"]:checked').value;
        const quantity = parseFloat(document.getElementById('quantity').value);
        const currentStock = {{ $currentStock }};

        if (type === 'reduce' && quantity > currentStock) {
            e.preventDefault();
            alert('Jumlah pengurangan tidak boleh melebihi stok tersedia!\n\nStok tersedia: ' + currentStock.toFixed(2) + ' {{ $rawMaterial->unit->name ?? "unit" }}');
        }
    });
</script>
@endpush
@endsection
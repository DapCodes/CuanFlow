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
                                    <p class="text-lg font-bold text-red-600">{{ number_format(collect($expiredStocks)->sum('quantity'), 2) }}</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-red-50 text-red-700 border border-red-100">{{ count($expiredStocks) }} Batch</span>
                        </div>

                        {{-- Expiring --}}
                        <div class="p-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-yellow-100 text-yellow-600 flex items-center justify-center">
                                    <i class="fas fa-hourglass-half text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Segera Kadaluarsa</p>
                                    <p class="text-lg font-bold text-yellow-600">{{ number_format(collect($expiringStocks)->sum('quantity'), 2) }}</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-yellow-50 text-yellow-700 border border-yellow-100">{{ count($expiringStocks) }} Batch</span>
                        </div>

                        {{-- Valid --}}
                        <div class="p-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                    <i class="fas fa-check-circle text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Aman/Valid</p>
                                    <p class="text-lg font-bold text-emerald-600">{{ number_format(collect($validStocks)->sum('quantity'), 2) }}</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-100">{{ count($validStocks) }} Batch</span>
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

                {{-- Batch Breakdown List --}}
                <div class="mt-8 space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-layer-group text-gray-400"></i>
                            Daftar Batch Bahan Baku
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Expired Batches --}}
                        <div class="space-y-4">
                            <div class="bg-red-50 px-4 py-3 border border-red-200 rounded-lg flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-red-800 flex items-center gap-2">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span>Kadaluarsa ({{ count($expiredStocks) }})</span>
                                </h3>
                                @if(count($expiredStocks) > 0)
                                <button onclick="openRemoveExpiredModal()" class="text-[10px] font-bold text-red-700 hover:text-red-900 uppercase">Hapus</button>
                                @endif
                            </div>
                            <div class="space-y-3">
                                @forelse($expiredStocks as $stock)
                                <div class="bg-white border border-red-100 rounded-xl p-4 shadow-sm relative overflow-hidden group">
                                    <div class="absolute top-0 right-0 p-2">
                                        <input type="checkbox" class="expired-checkbox w-3.5 h-3.5 text-red-600 border-gray-300 rounded focus:ring-red-500" value="{{ $stock['id'] }}">
                                    </div>
                                    <div class="flex flex-col gap-1">
                                        <span class="text-[10px] font-mono font-bold text-red-400">#{{ $stock['batch_number'] }}</span>
                                        <div class="flex items-center justify-between">
                                            <span class="text-xl font-black text-gray-900">{{ number_format($stock['quantity'], 2) }}</span>
                                            <span class="text-xs font-medium text-gray-500">{{ $rawMaterial->unit->abbreviation }}</span>
                                        </div>
                                        <div class="mt-2 flex items-center justify-between text-[10px]">
                                            <span class="text-gray-500">KADALUARSA:</span>
                                            <span class="font-bold text-red-600 uppercase">{{ $stock['expired_at']->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-8 bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl">
                                    <p class="text-xs text-gray-400">Tidak ada batch kadaluarsa</p>
                                </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Expiring Soon --}}
                        <div class="space-y-4">
                            <div class="bg-yellow-50 px-4 py-3 border border-yellow-200 rounded-lg">
                                <h3 class="text-sm font-semibold text-yellow-800 flex items-center gap-2">
                                    <i class="fas fa-hourglass-start"></i>
                                    <span>Segera Kadaluarsa ({{ count($expiringStocks) }})</span>
                                </h3>
                            </div>
                            <div class="space-y-3">
                                @forelse($expiringStocks as $stock)
                                <div class="bg-white border border-yellow-100 rounded-xl p-4 shadow-sm relative overflow-hidden">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-[10px] font-mono font-bold text-yellow-500">#{{ $stock['batch_number'] }}</span>
                                        <div class="flex items-center justify-between">
                                            <span class="text-xl font-black text-gray-900">{{ number_format($stock['quantity'], 2) }}</span>
                                            <span class="text-xs font-medium text-gray-500">{{ $rawMaterial->unit->abbreviation }}</span>
                                        </div>
                                        <div class="mt-2 flex items-center justify-between text-[10px]">
                                            <span class="text-gray-500">KADALUARSA:</span>
                                            <span class="font-bold text-yellow-600 uppercase">{{ $stock['expired_at']->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-8 bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl">
                                    <p class="text-xs text-gray-400">Tidak ada batch kritis</p>
                                </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Valid --}}
                        <div class="space-y-4">
                            <div class="bg-emerald-50 px-4 py-3 border border-emerald-200 rounded-lg">
                                <h3 class="text-sm font-semibold text-emerald-800 flex items-center gap-2">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Stok Aman/Valid ({{ count($validStocks) }})</span>
                                </h3>
                            </div>
                            <div class="space-y-3">
                                @forelse($validStocks as $stock)
                                <div class="bg-white border border-emerald-100 rounded-xl p-4 shadow-sm relative overflow-hidden">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-[10px] font-mono font-bold text-emerald-500">#{{ $stock['batch_number'] }}</span>
                                        <div class="flex items-center justify-between">
                                            <span class="text-xl font-black text-gray-900">{{ number_format($stock['quantity'], 2) }}</span>
                                            <span class="text-xs font-medium text-gray-500">{{ $rawMaterial->unit->abbreviation }}</span>
                                        </div>
                                        <div class="mt-2 flex items-center justify-between text-[10px]">
                                            <span class="text-gray-500">KADALUARSA:</span>
                                            <span class="font-bold text-emerald-600 uppercase">{{ $stock['expired_at'] ? $stock['expired_at']->diffForHumans() : 'Selamanya (Aman)' }}</span>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-8 bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl">
                                    <p class="text-xs text-gray-400">Tidak ada stok valid</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

{{-- Remove Expired Modal --}}
<div id="removeExpiredModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeRemoveExpiredModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-trash-alt text-red-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">Konfirmasi Penghapusan Stok</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Anda yakin ingin menghapus <span id="selectedCount" class="font-bold text-red-600">0</span> batch stok yang kadaluarsa? Tindakan ini tidak dapat dibatalkan dan akan mengurangi stok tersedia.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                <form id="removeExpiredForm" action="{{ route('raw-materials.remove-expired', $rawMaterial) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm">
                        Ya, Hapus Stok
                    </button>
                </form>
                <button type="button" onclick="closeRemoveExpiredModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const purchaseFields = document.querySelectorAll('.purchase-field');
    const quantityInput = document.getElementById('quantity');
    const unitPriceInput = document.getElementById('unit_price');
    const expenseCategorySelect = document.getElementById('expense_category_id');
    const paymentMethodSelect = document.getElementById('payment_method');

    function toggleFields() {
        const isAdd = document.querySelector('input[name="type"]:checked').value === 'add';
        
        purchaseFields.forEach(el => {
            if (isAdd) {
                el.classList.remove('hidden');
                // Enable required fields if adding
                const inputs = el.querySelectorAll('input, select');
                inputs.forEach(input => {
                    if (input.id === 'expense_category_id' || input.id === 'payment_method') {
                        input.setAttribute('required', 'required');
                    }
                });
            } else {
                el.classList.add('hidden');
                // Disable required to avoid validation error on client side
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

    // Initial check
    toggleFields();

    window.openRemoveExpiredModal = function() {
        const checkboxes = document.querySelectorAll('.expired-checkbox:checked');
        if (checkboxes.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Opps...',
                text: 'Pilih minimal satu batch untuk dihapus',
            });
            return;
        }
        
        const form = document.getElementById('removeExpiredForm');
        form.querySelectorAll('input[name="batch_ids[]"]').forEach(el => el.remove());
        
        checkboxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'batch_ids[]';
            input.value = checkbox.value;
            form.appendChild(input);
        });
        
        document.getElementById('selectedCount').textContent = checkboxes.length;
        document.getElementById('removeExpiredModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    window.closeRemoveExpiredModal = function() {
        document.getElementById('removeExpiredModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Validation for reduce
    document.getElementById('stockForm').addEventListener('submit', function(e) {
        const type = document.querySelector('input[name="type"]:checked').value;
        const qty = parseFloat(quantityInput.value);
        const currentStock = {{ $currentStock }};

        if (type === 'reduce' && qty > currentStock) {
            e.preventDefault();
            alert('Jumlah pengurangan tidak boleh melebihi stok tersedia (' + currentStock + ')');
        }
    });
});
</script>
@endpush
@endsection
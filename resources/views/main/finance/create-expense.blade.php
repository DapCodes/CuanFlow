@extends('layouts.app')

@section('title', 'Catat Pengeluaran - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('finance.index') }}" class="text-gray-500 hover:text-red-600 transition-colors">Keuangan</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Catat Pengeluaran</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">
        
        {{-- Header --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-600 border border-red-100">
                        <i class="fas fa-minus-circle text-sm"></i>
                    </span>
                    <span>Catat Pengeluaran Baru</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Rekam pengeluaran untuk operasional, gaji, atau kebutuhan outlet lainnya.
                </p>
            </div>
            <a href="{{ route('finance.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </section>

        {{-- Form --}}
        <form action="{{ route('finance.expense.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 md:p-8 space-y-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Amount --}}
                        <div class="md:col-span-2">
                            <label for="amount" class="block text-sm font-bold text-gray-700 mb-2">Jumlah Biaya (Rp) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 font-bold">Rp</span>
                                <input type="number" name="amount" id="amount" required step="0.01" min="0" value="{{ old('amount') }}"
                                    class="w-full pl-12 pr-4 py-4 bg-gray-50 border-gray-200 rounded-xl text-2xl font-black text-red-600 focus:ring-2 focus:ring-red-500 focus:border-red-500 placeholder-gray-300 transition-all"
                                    placeholder="0">
                            </div>
                            @error('amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Category --}}
                        <div>
                            <label for="expense_category_id" class="block text-sm font-bold text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                            <select name="expense_category_id" id="expense_category_id" required
                                class="w-full px-4 py-2.5 bg-gray-50 border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                <option value="" disabled selected>Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('expense_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('expense_category_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Date --}}
                        <div>
                            <label for="expense_date" class="block text-sm font-bold text-gray-700 mb-2">Tanggal Pengeluaran <span class="text-red-500">*</span></label>
                            <input type="date" name="expense_date" id="expense_date" required value="{{ old('expense_date', date('Y-m-d')) }}"
                                class="w-full px-4 py-2.5 bg-gray-50 border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            @error('expense_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Payment Method --}}
                        <div>
                            <label for="payment_method" class="block text-sm font-bold text-gray-700 mb-2">Metode Pembayaran <span class="text-red-500">*</span></label>
                            <select name="payment_method" id="payment_method" required
                                class="w-full px-4 py-2.5 bg-gray-50 border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Tunai (Kas Toko)</option>
                                <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Kartu Debit/Kredit</option>
                            </select>
                            @error('payment_method') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Description --}}
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Pengeluaran <span class="text-red-500">*</span></label>
                            <input type="text" name="description" id="description" required value="{{ old('description') }}"
                                class="w-full px-4 py-2.5 bg-gray-50 border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                placeholder="Contoh: Belanja bahan baku, Bayar Listrik period Jan 24">
                            @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Receipt Image --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Bukti Pembayaran (Foto/Scan)</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-200 border-dashed rounded-xl hover:border-red-400 transition-colors bg-gray-50 group">
                                <div class="space-y-1 text-center">
                                    <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-3 group-hover:text-red-500 transition-colors"></i>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="receipt_image" class="relative cursor-pointer bg-white rounded-md font-bold text-red-600 hover:text-red-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-red-500">
                                            <span>Unggah file</span>
                                            <input id="receipt_image" name="receipt_image" type="file" class="sr-only" accept="image/*">
                                        </label>
                                        <p class="pl-1">atau seret dan lepas</p>
                                    </div>
                                    <p class="text-[10px] text-gray-500">PNG, JPG, GIF s/d 2MB</p>
                                </div>
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-bold text-gray-700 mb-2">Catatan Internal</label>
                            <textarea name="notes" id="notes" rows="3"
                                class="w-full px-4 py-2.5 bg-gray-50 border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                placeholder="Keterangan tambahan jika diperlukan...">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                </div>
                
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('finance.index') }}" class="px-6 py-2.5 text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">Batal</a>
                    <button type="submit" class="px-8 py-2.5 bg-red-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-red-200 hover:bg-red-700 transition-all">
                        Simpan Pengeluaran
                    </button>
                </div>
            </div>
        </form>

    </div>
</main>
@endsection

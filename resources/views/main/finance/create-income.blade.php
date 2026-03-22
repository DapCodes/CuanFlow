@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Catat Pemasukan - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('finance.index') }}" class="text-gray-500 hover:text-emerald-600 transition-colors">Keuangan</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Catat Pemasukan</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">
        
        {{-- Header --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100">
                        <i class="fas fa-plus-circle text-sm"></i>
                    </span>
                    <span>Catat Pemasukan Lainnya</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Gunakan formulir ini untuk mencatat pemasukan di luar transaksi penjualan reguler.
                </p>
            </div>
            <a href="{{ route('finance.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </section>

        {{-- Form --}}
        <form action="{{ route('finance.income.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 md:p-8 space-y-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Amount --}}
                        <div class="md:col-span-2">
                            <label for="amount" class="block text-sm font-bold text-gray-700 mb-2">Jumlah Pemasukan (Rp) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 font-bold">Rp</span>
                                <input type="number" name="amount" id="amount" required step="0.01" min="0" value="{{ old('amount') }}"
                                    class="w-full pl-12 pr-4 py-4 bg-gray-50 border-gray-200 rounded-xl text-2xl font-black text-emerald-600 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 placeholder-gray-300 transition-all"
                                    placeholder="0">
                            </div>
                            @error('amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Date --}}
                        <div>
                            <label for="income_date" class="block text-sm font-bold text-gray-700 mb-2">Tanggal Terimal <span class="text-red-500">*</span></label>
                            <input type="date" name="income_date" id="income_date" required value="{{ old('income_date', date('Y-m-d')) }}"
                                class="w-full px-4 py-2.5 bg-gray-50 border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            @error('income_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Payment Method --}}
                        <div>
                            <label for="payment_method" class="block text-sm font-bold text-gray-700 mb-2">Metode Penerimaan <span class="text-red-500">*</span></label>
                            <select name="payment_method" id="payment_method" required
                                class="w-full px-4 py-2.5 bg-gray-50 border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                                <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Kartu Debit/Kredit</option>
                            </select>
                            @error('payment_method') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Description --}}
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Deskripsi / Judul <span class="text-red-500">*</span></label>
                            <input type="text" name="description" id="description" required value="{{ old('description') }}"
                                class="w-full px-4 py-2.5 bg-gray-50 border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                placeholder="Contoh: Pendapatan Bunga, Jual Aset, dll">
                            @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Reference Number --}}
                        <div>
                            <label for="reference_number" class="block text-sm font-bold text-gray-700 mb-2">Nomor Referensi (Opsional)</label>
                            <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number') }}"
                                class="w-full px-4 py-2.5 bg-gray-50 border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                placeholder="No. Resi / No. Bukti">
                        </div>

                        {{-- Notes --}}
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-bold text-gray-700 mb-2">Catatan Tambahan</label>
                            <textarea name="notes" id="notes" rows="3"
                                class="w-full px-4 py-2.5 bg-gray-50 border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                placeholder="Detail tambahan jika diperlukan...">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                </div>
                
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('finance.index') }}" class="px-6 py-2.5 text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">Batal</a>
                    <button type="submit" class="px-8 py-2.5 bg-emerald-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-200 hover:bg-emerald-700 transition-all">
                        Simpan Pemasukan
                    </button>
                </div>
            </div>
        </form>

    </div>
</main>
@endsection

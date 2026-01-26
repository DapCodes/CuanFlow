@extends('layouts.app')

@section('title', 'Edit ' . ($expense->type == 'income' ? 'Pemasukan' : 'Pengeluaran') . ' - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-300 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
    <a href="{{ route('expenses.index', ['type' => $expense->type]) }}" class="text-gray-400 hover:text-gray-500">{{ $expense->type == 'income' ? 'Pemasukan' : 'Pengeluaran' }}</a>
    <svg class="w-4 h-4 text-gray-300 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
    <span class="text-gray-900 font-medium">Edit Data</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg {{ $expense->type == 'income' ? 'bg-emerald-50 text-emerald-500 border border-emerald-100' : 'bg-red-50 text-red-500 border border-red-100' }}">
                        <i class="fas fa-edit text-sm"></i>
                    </span>
                    <span>Edit {{ $expense->type == 'income' ? 'Pemasukan' : 'Pengeluaran' }}</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Perbarui informasi transaksi yang sudah tercatat.
                </p>
            </div>
            <a href="{{ route('expenses.index', ['type' => $expense->type]) }}" 
               class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </section>

        {{-- FORM CARD --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <form action="{{ route('expenses.update', $expense->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="p-6 md:p-8 space-y-8">
                    
                    {{-- Section: Informasi Utama --}}
                    <div class="space-y-6">
                        <div class="border-b border-gray-100 pb-2">
                            <h3 class="text-lg font-medium text-gray-900">Informasi Utama</h3>
                            <p class="text-sm text-gray-500">Perbarui data utama transaksi.</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nominal -->
                            <div class="space-y-1">
                                <label for="amount" class="block text-sm font-medium text-gray-700">Nominal (Rp) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm font-semibold">Rp</span>
                                    </div>
                                    <input type="number" name="amount" id="amount" required min="0" step="0.01" value="{{ old('amount', abs($expense->amount)) }}" 
                                        class="pl-10 block w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-lg font-medium">
                                </div>
                                @error('amount') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Tanggal -->
                            <div class="space-y-1">
                                <label for="expense_date" class="block text-sm font-medium text-gray-700">Tanggal Transaksi <span class="text-red-500">*</span></label>
                                <input type="date" name="expense_date" id="expense_date" required value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" 
                                    class="block w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                @error('expense_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                             <!-- Kategori -->
                            <div class="space-y-1">
                                <label for="expense_category_id" class="block text-sm font-medium text-gray-700">Kategori <span class="text-red-500">*</span></label>
                                <select name="expense_category_id" id="expense_category_id" required 
                                    class="block w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('expense_category_id', $expense->expense_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('expense_category_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Metode Pembayaran -->
                            <div class="space-y-1">
                                <label for="payment_method" class="block text-sm font-medium text-gray-700">Metode Pembayaran <span class="text-red-500">*</span></label>
                                <select name="payment_method" id="payment_method" required 
                                    class="block w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                    <option value="cash" {{ old('payment_method', $expense->payment_method) == 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                                    <option value="transfer" {{ old('payment_method', $expense->payment_method) == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                    <option value="card" {{ old('payment_method', $expense->payment_method) == 'card' ? 'selected' : '' }}>Kartu Debit/Kredit</option>
                                </select>
                                @error('payment_method') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="space-y-1">
                            <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi / Keperluan <span class="text-red-500">*</span></label>
                            <input type="text" name="description" id="description" required value="{{ old('description', $expense->description) }}" maxlength="255"
                                class="block w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Section: Informasi Tambahan --}}
                    <div class="space-y-6 pt-4">
                         <div class="border-b border-gray-100 pb-2">
                            <h3 class="text-lg font-medium text-gray-900">Informasi Tambahan</h3>
                            <p class="text-sm text-gray-500">Opsional untuk detail lebih lengkap.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <label for="reference_number" class="block text-sm font-medium text-gray-700">Nomor Referensi</label>
                                <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number', $expense->reference_number) }}" 
                                    class="block w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div class="space-y-1">
                                <label for="notes" class="block text-sm font-medium text-gray-700">Catatan Lainnya</label>
                                <textarea name="notes" id="notes" rows="1" class="block w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">{{ old('notes', $expense->notes) }}</textarea>
                            </div>
                        </div>

                        <!-- Bukti -->
                        <div class="space-y-2">
                             <label class="block text-sm font-medium text-gray-700">Bukti Struk (Foto)</label>
                             
                             @if($expense->receipt_image)
                                <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <p class="text-xs text-gray-500 mb-2 font-medium">Gambar Saat Ini:</p>
                                    <img src="{{ asset('storage/' . $expense->receipt_image) }}" alt="Receipt" class="h-32 rounded-lg shadow-sm">
                                </div>
                             @endif

                             <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer" onclick="document.getElementById('receipt_image').click()">
                                <div class="space-y-1 text-center">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label for="receipt_image" class="relative cursor-pointer rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                            <span>Ganti file</span>
                                            <input id="receipt_image" name="receipt_image" type="file" class="sr-only" accept="image/*" onchange="previewImage(this)">
                                        </label>
                                        <p class="pl-1">atau drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        Biarkan kosong jika tidak ingin mengubah
                                    </p>
                                </div>
                            </div>
                            <div id="image-preview" class="hidden mt-4 text-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <p class="text-xs text-gray-500 mb-2">Preview Pengganti:</p>
                                <img id="preview-img" src="#" alt="Preview" class="max-h-64 rounded-lg mx-auto shadow-sm">
                                <button type="button" onclick="clearImage(); event.stopPropagation();" class="mt-3 text-sm text-red-600 hover:text-red-800 font-medium flex items-center justify-center gap-1 mx-auto">
                                    <i class="fas fa-trash-alt"></i> Batal Ganti
                                </button>
                            </div>
                            @error('receipt_image') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">
                    <a href="{{ route('expenses.index', ['type' => $expense->type]) }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-all">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white {{ $expense->type == 'income' ? 'bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500' : 'bg-red-600 hover:bg-red-700 focus:ring-red-500' }} rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </section>

    </div>
</main>
@endsection

@push('scripts')
<script>
    function previewImage(input) {
        const previewDiv = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewDiv.classList.remove('hidden');
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    function clearImage() {
        const input = document.getElementById('receipt_image');
        const previewDiv = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');
        
        input.value = '';
        previewImg.src = '#';
        previewDiv.classList.add('hidden');
    }
</script>
@endpush

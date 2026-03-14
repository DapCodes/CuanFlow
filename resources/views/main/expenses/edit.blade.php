@extends('layouts.app')

@section('title', 'Edit ' . ($expense->type == 'income' ? 'Pemasukan' : 'Pengeluaran') . ' - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-900 transition-colors">Dashboard</a>
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('expenses.index', ['type' => $expense->type]) }}" class="text-gray-400 hover:text-gray-900 transition-colors">{{ $expense->type == 'income' ? 'Pemasukan' : 'Pengeluaran' }}</a>
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Edit Data</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900 leading-tight">
                    Edit {{ $expense->type == 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 font-medium">
                    Perbarui informasi transaksi yang sudah tercatat.
                </p>
            </div>
            <a href="{{ route('expenses.index', ['type' => $expense->type]) }}" 
               class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-600 font-black text-[10px] uppercase tracking-widest hover:bg-gray-50 transition-all active:scale-95 shadow-sm">
                <span>Kembali</span>
            </a>
        </section>

        {{-- FORM CARD --}}
        <x-card-container>
            <form action="{{ route('expenses.update', $expense->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="p-8 md:p-10 space-y-10">
                    
                    {{-- Section: Informasi Utama --}}
                    <div class="space-y-8">
                        <div>
                            <h3 class="text-[10px] font-black text-gray-900 uppercase tracking-widest pl-1">Informasi Utama</h3>
                            <div class="mt-2 h-1 w-10 bg-cuan-green rounded-full"></div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Nominal -->
                            <div class="space-y-3">
                                <label for="amount" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Nominal (Rp) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-6 flex items-center pointer-events-none">
                                        <span class="text-gray-400 font-black text-sm">Rp</span>
                                    </div>
                                    <input type="number" name="amount" id="amount" required min="0" step="0.01" value="{{ old('amount', abs($expense->amount)) }}" 
                                        class="w-full pl-14 pr-6 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl text-2xl font-black text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green focus:bg-white transition-all outline-none">
                                </div>
                                @error('amount') <p class="text-[10px] text-red-500 font-bold uppercase tracking-tight mt-1 pl-2">{{ $message }}</p> @enderror
                            </div>

                            <!-- Tanggal -->
                            <div class="space-y-3">
                                <label for="expense_date" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Tanggal Transaksi <span class="text-red-500">*</span></label>
                                <input type="date" name="expense_date" id="expense_date" required value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" 
                                    class="w-full px-6 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green focus:bg-white transition-all outline-none cursor-pointer">
                                @error('expense_date') <p class="text-[10px] text-red-500 font-bold uppercase tracking-tight mt-1 pl-2">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                             <!-- Kategori -->
                            <div class="space-y-3">
                                <label for="expense_category_id" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Kategori <span class="text-red-500">*</span></label>
                                <select name="expense_category_id" id="expense_category_id" required 
                                    class="w-full px-6 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green focus:bg-white transition-all outline-none appearance-none cursor-pointer">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('expense_category_id', $expense->expense_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('expense_category_id') <p class="text-[10px] text-red-500 font-bold uppercase tracking-tight mt-1 pl-2">{{ $message }}</p> @enderror
                            </div>

                            <!-- Metode Pembayaran -->
                            <div class="space-y-3">
                                <label for="payment_method" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Metode Pembayaran <span class="text-red-500">*</span></label>
                                <select name="payment_method" id="payment_method" required 
                                    class="w-full px-6 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green focus:bg-white transition-all outline-none appearance-none cursor-pointer">
                                    <option value="cash" {{ old('payment_method', $expense->payment_method) == 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                                    <option value="transfer" {{ old('payment_method', $expense->payment_method) == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                    <option value="card" {{ old('payment_method', $expense->payment_method) == 'card' ? 'selected' : '' }}>Kartu Debit/Kredit</option>
                                </select>
                                @error('payment_method') <p class="text-[10px] text-red-500 font-bold uppercase tracking-tight mt-1 pl-2">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="space-y-3">
                            <label for="description" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Deskripsi / Keperluan <span class="text-red-500">*</span></label>
                            <textarea name="description" id="description" required maxlength="255" rows="3"
                                class="w-full px-6 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green focus:bg-white transition-all outline-none" placeholder="Contoh: Pembayaran listrik bulan ini...">{{ old('description', $expense->description) }}</textarea>
                            @error('description') <p class="text-[10px] text-red-500 font-bold uppercase tracking-tight mt-1 pl-2">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Section: Informasi Tambahan --}}
                    <div class="space-y-8 pt-4">
                        <div>
                            <h3 class="text-[10px] font-black text-gray-900 uppercase tracking-widest pl-1">Informasi Tambahan</h3>
                            <div class="mt-2 h-1 w-10 bg-amber-400 rounded-full"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label for="reference_number" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Nomor Referensi</label>
                                <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number', $expense->reference_number) }}" 
                                    class="w-full px-6 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green focus:bg-white transition-all outline-none">
                            </div>
                            
                            <div class="space-y-3">
                                <label for="notes" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Catatan Lainnya</label>
                                <input type="text" name="notes" id="notes" value="{{ old('notes', $expense->notes) }}" 
                                    class="w-full px-6 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green focus:bg-white transition-all outline-none">
                            </div>
                        </div>

                        <!-- Bukti -->
                        <div class="space-y-4">
                             <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Bukti Struk (Foto)</label>
                             
                             @if($expense->receipt_image)
                                <div class="mb-6 p-6 bg-gray-50 rounded-[2rem] border-2 border-gray-100 inline-block">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Gambar Saat Ini</p>
                                    <img src="{{ asset('storage/' . $expense->receipt_image) }}" alt="Receipt" class="h-40 rounded-2xl shadow-xl">
                                </div>
                             @endif

                             <div class="mt-1 flex justify-center px-6 pt-10 pb-10 border-2 border-dashed border-gray-200 rounded-[2rem] bg-gray-50 hover:bg-white hover:border-cuan-green transition-all cursor-pointer group" onclick="document.getElementById('receipt_image').click()">
                                <div class="space-y-1 text-center">
                                    <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-cloud-upload-alt text-2xl text-gray-300 group-hover:text-cuan-green transition-colors"></i>
                                    </div>
                                    <div class="flex text-xs text-gray-600 justify-center">
                                        <label for="receipt_image" class="relative cursor-pointer rounded-md font-black text-cuan-green uppercase tracking-widest focus-within:outline-none">
                                            <span>Ganti file</span>
                                            <input id="receipt_image" name="receipt_image" type="file" class="sr-only" accept="image/*" onchange="previewImage(this)">
                                        </label>
                                        <p class="pl-2 font-bold text-gray-400 uppercase tracking-widest">atau drag and drop</p>
                                    </div>
                                    <p class="text-[10px] font-bold text-gray-300 uppercase tracking-widest mt-2">
                                        Biarkan kosong jika tidak ingin mengubah
                                    </p>
                                </div>
                            </div>
                            <div id="image-preview" class="hidden mt-6 text-center p-6 bg-gray-50 rounded-[2rem] border-2 border-gray-100">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Preview Pengganti</p>
                                <img id="preview-img" src="#" alt="Preview" class="max-h-64 rounded-2xl mx-auto shadow-xl">
                                <button type="button" onclick="clearImage(); event.stopPropagation();" class="mt-4 px-6 py-2 rounded-xl bg-red-50 text-red-500 text-[10px] font-black uppercase tracking-widest hover:bg-red-500 hover:text-white transition-all mx-auto">
                                    Batal Ganti
                                </button>
                            </div>
                            @error('receipt_image') <p class="text-[10px] text-red-500 font-bold uppercase tracking-tight mt-1 pl-2">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100 flex items-center justify-end gap-4">
                    <a href="{{ route('expenses.index', ['type' => $expense->type]) }}" class="px-6 py-3 text-[10px] font-black text-gray-500 uppercase tracking-widest rounded-xl hover:bg-white transition-all active:scale-95">
                        Batal
                    </a>
                    <button type="submit" class="px-8 py-3 bg-black text-white font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-cuan-green transition-all active:scale-95 shadow-lg shadow-gray-900/10">
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </x-card-container>

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

@extends('layouts.app')

@section('title', 'Tambah Metode Pembayaran - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('outlet-payment-links.index') }}" class="text-gray-500 hover:text-gray-700">Metode Pembayaran QRIS</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Tambah Metode</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-pink-50 text-pink-500 border border-pink-100">
                        <i class="fas fa-plus-circle text-sm"></i>
                    </span>
                    <span>Tambah Metode Pembayaran Baru</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Tambahkan metode pembayaran QRIS untuk memudahkan transaksi di outlet Anda.
                </p>
            </div>
        </section>

        {{-- FORM CARD UTAMA --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            @if ($errors->any())
                <div class="mx-4 md:mx-6 mt-6 p-3 rounded bg-red-50 border border-red-200 text-sm text-red-700">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('outlet-payment-links.store') }}" method="POST" enctype="multipart/form-data" class="px-4 md:px-6 py-6 space-y-8">
                @csrf

                {{-- Pilih Metode Pembayaran --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <span>Pilih Metode Pembayaran</span>
                        </h3>
                    </div>
                    <div>
                        <label for="payment_method_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Metode Pembayaran <span class="text-red-500">*</span>
                        </label>
                        <select name="payment_method_id" id="payment_method_id" required
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-pink-400 @error('payment_method_id') border-red-500 @enderror">
                            <option value="">-- Pilih Metode Pembayaran --</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method->id }}" 
                                        {{ in_array($method->id, $usedMethodIds) ? 'disabled' : '' }}
                                        {{ old('payment_method_id') == $method->id ? 'selected' : '' }}>
                                    {{ $method->name }} {{ in_array($method->id, $usedMethodIds) ? '(Sudah digunakan)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('payment_method_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-xs text-gray-500">Pilih metode pembayaran yang ingin ditambahkan untuk outlet Anda</p>
                    </div>
                </div>

                {{-- Informasi Rekening --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <span>Informasi Rekening / Akun</span>
                        </h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Nomor Rekening / HP --}}
                        <div>
                            <label for="account_number" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Nomor Rekening / Nomor HP
                            </label>
                            <input type="text" 
                                   name="account_number" 
                                   id="account_number"
                                   value="{{ old('account_number') }}"
                                   placeholder="Contoh: 1234567890"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-pink-400 @error('account_number') border-red-500 @enderror">
                            @error('account_number')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1.5 text-xs text-gray-500">Isi nomor rekening untuk bank atau nomor HP untuk e-wallet</p>
                        </div>

                        {{-- Nama Pemilik --}}
                        <div>
                            <label for="account_name" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Nama Pemilik Rekening / Akun
                            </label>
                            <input type="text" 
                                   name="account_name" 
                                   id="account_name"
                                   value="{{ old('account_name') }}"
                                   placeholder="Contoh: PT. Toko Sejahtera"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-pink-400 @error('account_name') border-red-500 @enderror">
                            @error('account_name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Upload QR Code --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <span>Upload QR Code</span>
                        </h3>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            File QR Code <span class="text-gray-400">(Opsional)</span>
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-pink-400 transition-colors">
                            <input type="file" name="qr_image" id="qrImage" accept="image/*" 
                                   class="hidden" onchange="previewImage(this)">
                            <label for="qrImage" class="cursor-pointer">
                                <div id="imagePreview" class="mb-3 hidden">
                                    <img src="" alt="Preview" class="mx-auto max-h-48 rounded-lg border border-gray-200">
                                </div>
                                <div id="uploadPlaceholder">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                    <p class="text-sm font-medium text-gray-700 mb-1">Klik untuk upload QR Code</p>
                                    <p class="text-xs text-gray-500">Format: JPG, PNG (Max 2MB)</p>
                                </div>
                            </label>
                        </div>
                        @error('qr_image')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Catatan --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <span>Catatan Tambahan</span>
                        </h3>
                    </div>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Catatan <span class="text-gray-400">(Opsional)</span>
                        </label>
                        <textarea name="notes" 
                                  id="notes" 
                                  rows="3"
                                  placeholder="Tambahkan catatan atau instruksi khusus untuk metode pembayaran ini..."
                                  class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-pink-400 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Status Aktif --}}
                <div>
                    <div class="flex items-center gap-6">
                        <div class="flex items-center">
                            <input type="checkbox"
                                   name="is_active"
                                   id="is_active"
                                   value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="w-4 h-4 text-pink-600 border-gray-300 rounded focus:ring-pink-500">
                            <label for="is_active" class="ml-3 text-sm font-medium text-gray-700">
                                Aktifkan metode pembayaran ini
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-5 border-t border-gray-200">
                    <div class="flex flex-col md:flex-row md:justify-end gap-3">
                        <a href="{{ route('outlet-payment-links.index') }}"
                           class="w-full md:w-auto inline-flex items-center justify-center px-4 md:px-6 py-2.5 border border-gray-300 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-times mr-2 text-xs"></i>
                            <span>Batal</span>
                        </a>
                        <button type="submit"
                                class="w-full md:w-auto inline-flex items-center justify-center px-4 md:px-6 py-2.5 bg-pink-500 text-sm font-semibold text-white rounded-lg hover:bg-pink-600 shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-400 focus:ring-offset-1">
                            <i class="fas fa-save mr-2 text-xs"></i>
                            <span>Simpan Metode</span>
                        </button>
                    </div>
                </div>
            </form>
        </section>

    </div>
</main>

@push('scripts')
<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const placeholder = document.getElementById('uploadPlaceholder');
    const img = preview.querySelector('img');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            img.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };

        reader.readAsDataURL(input.files[0]);
    } else {
        preview.classList.add('hidden');
        placeholder.classList.remove('hidden');
    }
}
</script>
@endpush
@endsection
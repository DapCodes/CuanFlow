{{-- ============================================ --}}
{{-- FILE: outlet-payment-links/edit.blade.php --}}
{{-- ============================================ --}}
@extends('layouts.app')

@section('title', 'Edit Metode Pembayaran - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

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
    <span class="text-gray-900 font-medium">Edit Metode</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-yellow-50 text-yellow-500 border border-yellow-100">
                        <i class="fas fa-edit text-sm"></i>
                    </span>
                    <span>Edit Metode Pembayaran</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Perbarui informasi metode pembayaran {{ $outletPaymentLink->paymentMethod->name }}
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

            <form action="{{ route('outlet-payment-links.update', $outletPaymentLink->id) }}" method="POST" enctype="multipart/form-data" class="px-4 md:px-6 py-6 space-y-8">
                @csrf
                @method('PUT')

                {{-- Metode Pembayaran (Read Only) --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <span>Metode Pembayaran</span>
                        </h3>
                    </div>
                    <div class="p-4 bg-gradient-to-r from-pink-50 to-red-50 rounded-lg border border-pink-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg bg-white flex items-center justify-center border border-pink-200 shadow-sm">
                                <i class="fas fa-building text-pink-500 text-lg"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $outletPaymentLink->paymentMethod->name }}</p>
                                <p class="text-xs text-gray-600 mt-0.5">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Metode pembayaran tidak dapat diubah
                                </p>
                            </div>
                        </div>
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
                                   value="{{ old('account_number', $outletPaymentLink->account_number) }}"
                                   placeholder="Contoh: 1234567890"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-pink-400 @error('account_number') border-red-500 @enderror">
                            @error('account_number')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nama Pemilik --}}
                        <div>
                            <label for="account_name" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Nama Pemilik Rekening / Akun
                            </label>
                            <input type="text" 
                                   name="account_name" 
                                   id="account_name"
                                   value="{{ old('account_name', $outletPaymentLink->account_name) }}"
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
                    
                    @if($outletPaymentLink->qr_image)
                        <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">QR Code Saat Ini:</p>
                            <div class="inline-block">
                                <img src="{{ asset('storage/' . $outletPaymentLink->qr_image) }}" 
                                     alt="Current QR" 
                                     class="max-h-32 rounded-lg border border-gray-300 shadow-sm">
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Upload QR Code Baru <span class="text-gray-400">(Opsional)</span>
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
                                    <p class="text-sm font-medium text-gray-700 mb-1">Klik untuk upload QR Code baru</p>
                                    <p class="text-xs text-gray-500">Format: JPG, PNG (Max 2MB) • Kosongkan jika tidak ingin mengubah</p>
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
                                  class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-pink-400 @error('notes') border-red-500 @enderror">{{ old('notes', $outletPaymentLink->notes) }}</textarea>
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
                                   {{ old('is_active', $outletPaymentLink->is_active) ? 'checked' : '' }}
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
                            <span>Update Metode</span>
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
    }
}
</script>
@endpush
@endsection
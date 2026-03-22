@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Tambah Metode Pembayaran - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('outlet-payment-links.index') }}" class="text-gray-500 hover:text-cuan-green transition-colors">Metode Pembayaran QRIS</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Tambah Metode</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-2 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        <form action="{{ route('outlet-payment-links.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- HEADER HALAMAN --}}
            <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-xl md:text-2xl font-black text-gray-900">
                        Tambah Metode Pembayaran
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Lengkapi informasi metode pembayaran QRIS untuk outlet Anda.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('outlet-payment-links.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all active:scale-95">
                        <span>Batal</span>
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-5 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                        <span>Simpan Metode</span>
                    </button>
                </div>
            </section>



                {{-- Informasi Utama --}}
                <x-card-container>
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Informasi Utama</h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Pilih penyedia layanan pembayaran</p>
                    </div>
                    <div class="px-8 py-8">
                        <label for="payment_method_id" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                            Metode Pembayaran <span class="text-red-500">*</span>
                        </label>
                        <select name="payment_method_id" id="payment_method_id" required
                                class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all @error('payment_method_id') border-red-300 @enderror">
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
                    </div>
                </x-card-container>

                {{-- Informasi Rekening --}}
                <x-card-container>
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Informasi Akun</h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Detail identitas pembayaran</p>
                    </div>
                    <div class="px-8 py-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Nomor Rekening / HP --}}
                            <div>
                                <label for="account_number" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                    Nomor Rekening / Nomor HP
                                </label>
                                <input type="text" 
                                       name="account_number" 
                                       id="account_number"
                                       value="{{ old('account_number') }}"
                                       placeholder="Contoh: 1234567890"
                                       class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all @error('account_number') border-red-300 @enderror">
                                @error('account_number')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Nama Pemilik --}}
                            <div>
                                <label for="account_name" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                    Nama Pemilik
                                </label>
                                <input type="text" 
                                       name="account_name" 
                                       id="account_name"
                                       value="{{ old('account_name') }}"
                                       placeholder="Contoh: PT. Toko Sejahtera"
                                       class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all @error('account_name') border-red-300 @enderror">
                                @error('account_name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </x-card-container>

                {{-- File & Catatan --}}
                <x-card-container>
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">QR Code & Catatan</h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Lampiran visual dan keterangan tambahan</p>
                    </div>
                    <div class="px-8 py-8 space-y-8">
                        {{-- QR Code --}}
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4">File QR Code <span class="text-gray-400">(Opsional)</span></label>
                            <div class="border-2 border-dashed border-gray-200 rounded-[2rem] p-8 text-center hover:border-cuan-green hover:bg-cuan-green/5 transition-all group">
                                <input type="file" name="qr_image" id="qrImage" accept="image/*" 
                                       class="hidden" onchange="previewImage(this)">
                                <label for="qrImage" class="cursor-pointer block">
                                    <div id="imagePreview" class="mb-4 hidden">
                                        <img src="" alt="Preview" class="mx-auto max-h-48 rounded-2xl border border-gray-100 shadow-xl">
                                    </div>
                                    <div id="uploadPlaceholder">
                                        <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                            <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 group-hover:text-cuan-green"></i>
                                        </div>
                                        <p class="text-sm font-black text-gray-900 uppercase tracking-widest">Klik untuk upload QR Code</p>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Format: JPG, PNG (Max 2MB)</p>
                                    </div>
                                </label>
                            </div>
                            @error('qr_image')
                                <p class="mt-2 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Catatan --}}
                        <div>
                            <label for="notes" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                Catatan Tambahan <span class="text-gray-400">(Opsional)</span>
                            </label>
                            <textarea name="notes" 
                                      id="notes" 
                                      rows="4"
                                      placeholder="Tambahkan catatan atau instruksi khusus..."
                                      class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all @error('notes') border-red-300 @enderror">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-card-container>

                {{-- Status Aktif --}}
                <x-card-container>
                    <div class="px-8 py-8">
                        <label class="flex items-center gap-4 cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox" name="is_active" value="1" 
                                       id="is_active"
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cuan-green"></div>
                            </div>
                            <div>
                                <span class="text-xs font-black text-gray-900 uppercase tracking-widest">Aktifkan metode pembayaran</span>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Pelanggan dapat langsung melihat dan menggunakan metode ini saat checkout.</p>
                            </div>
                        </label>
                    </div>
                </x-card-container>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-3 pt-4 pb-8">
                    <a href="{{ route('outlet-payment-links.index') }}"
                       class="px-8 py-4 bg-white border border-gray-200 text-gray-600 rounded-2xl font-bold text-sm hover:bg-gray-50 transition-all active:scale-95">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-8 py-4 bg-cuan-green text-white rounded-2xl font-black text-sm hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                        Simpan Metode
                    </button>
                </div>
            </form>
        </section>

    </div>
</main>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Session Flash SweetAlert
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: "{{ session('success') }}",
            confirmButtonColor: '#658C58',
            iconColor: '#658C58',
            customClass: {
                popup: 'rounded-[1.5rem] border-0',
                title: 'font-black tracking-tight',
                confirmButton: 'rounded-xl font-black uppercase text-xs tracking-widest px-6 py-3'
            }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: "{{ session('error') }}",
            confirmButtonColor: '#ef4444',
            customClass: {
                popup: 'rounded-[1.5rem] border-0',
                title: 'font-black tracking-tight',
                confirmButton: 'rounded-xl font-black uppercase text-xs tracking-widest px-6 py-3'
            }
        });
    @endif
});

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
@extends('admin.layouts.app')

@section('title', 'Edit Metode Pembayaran')
@section('page-title', 'Edit Metode Pembayaran')

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <a href="{{ route('admin.payment-methods.index') }}" class="text-gray-500 hover:text-gray-700">Metode Pembayaran</a>
</li>
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <span class="text-gray-700">Edit</span>
</li>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <form action="{{ route('admin.payment-methods.update', $paymentMethod) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">Edit Metode: {{ $paymentMethod->name }}</h2>
            <a href="{{ route('admin.payment-methods.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </a>
        </div>
        
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Metode <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $paymentMethod->name) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green @error('name') border-red-300 @enderror">
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        Kode <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" id="code" value="{{ old('code', $paymentMethod->code) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green font-mono @error('code') border-red-300 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Kode unik untuk metode pembayaran (huruf kecil, tanpa spasi)</p>
                    @error('code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div>
                <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">Icon / Logo</label>
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden border-2 border-dashed border-gray-300" id="icon-preview">
                        @if($paymentMethod->icon)
                        <img src="{{ Storage::url($paymentMethod->icon) }}" alt="{{ $paymentMethod->name }}" class="w-12 h-12 object-contain">
                        @else
                        <i class="fas fa-credit-card text-gray-400 text-xl"></i>
                        @endif
                    </div>
                    <div class="flex-1">
                        <input type="file" name="icon" id="icon" accept="image/*"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green @error('icon') border-red-300 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, SVG, WebP. Maks: 2MB. Biarkan kosong jika tidak ingin mengubah.</p>
                        @error('icon')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="w-5 h-5 text-cuan-green border-gray-300 rounded"
                           {{ old('is_active', $paymentMethod->is_active) ? 'checked' : '' }}>
                    <span class="text-sm text-gray-700">Aktifkan metode pembayaran</span>
                </label>
            </div>
            
            <!-- Usage Info -->
            @if($paymentMethod->outletPaymentLinks->count() > 0)
            <div class="p-4 bg-amber-50 rounded-lg">
                <p class="text-xs font-medium text-amber-700 mb-2"><i class="fas fa-info-circle mr-1"></i> Metode ini digunakan oleh {{ $paymentMethod->outletPaymentLinks->count() }} outlet</p>
                <p class="text-xs text-amber-600">Menonaktifkan metode ini akan mempengaruhi outlet yang menggunakannya.</p>
            </div>
            @endif
        </div>
        
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.payment-methods.index') }}" 
               class="px-4 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-cuan-dark text-white font-semibold rounded-lg hover:bg-cuan-green">
                <i class="fas fa-save mr-2"></i>Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.getElementById('icon').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('icon-preview').innerHTML = `<img src="${event.target.result}" class="w-12 h-12 object-contain" alt="Preview">`;
        };
        reader.readAsDataURL(file);
    }
});

// Auto-lowercase code
document.getElementById('code').addEventListener('input', function() {
    this.value = this.value.toLowerCase().replace(/\s+/g, '');
});
</script>
@endpush
@endsection

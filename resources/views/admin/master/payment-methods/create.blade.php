@extends('admin.layouts.app')

@section('title', 'Tambah Metode Pembayaran')
@section('page-title', 'Tambah Metode Pembayaran')

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <a href="{{ route('admin.payment-methods.index') }}" class="text-gray-500 hover:text-gray-700">Metode Pembayaran</a>
</li>
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <span class="text-gray-700">Tambah</span>
</li>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <form action="{{ route('admin.payment-methods.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">Tambah Metode Pembayaran Baru</h2>
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
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green @error('name') border-red-300 @enderror"
                           placeholder="Contoh: Bank BCA">
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        Kode <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green font-mono @error('code') border-red-300 @enderror"
                           placeholder="bca">
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
                        <i class="fas fa-credit-card text-gray-400 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <input type="file" name="icon" id="icon" accept="image/*"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green @error('icon') border-red-300 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, SVG, WebP. Maks: 2MB</p>
                        @error('icon')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="w-5 h-5 text-cuan-green border-gray-300 rounded"
                           {{ old('is_active', true) ? 'checked' : '' }}>
                    <span class="text-sm text-gray-700">Aktifkan metode pembayaran</span>
                </label>
            </div>
            
            <!-- Common Payment Methods -->
            <div class="p-4 bg-blue-50 rounded-lg">
                <p class="text-xs font-medium text-blue-700 mb-3"><i class="fas fa-lightbulb mr-1"></i> Template Cepat:</p>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="setPaymentTemplate('Bank BCA', 'bca')" class="px-3 py-1.5 text-xs font-medium bg-white text-blue-700 rounded-full hover:bg-blue-100 transition-colors border border-blue-200">Bank BCA</button>
                    <button type="button" onclick="setPaymentTemplate('Bank BRI', 'bri')" class="px-3 py-1.5 text-xs font-medium bg-white text-blue-700 rounded-full hover:bg-blue-100 transition-colors border border-blue-200">Bank BRI</button>
                    <button type="button" onclick="setPaymentTemplate('Bank Mandiri', 'mandiri')" class="px-3 py-1.5 text-xs font-medium bg-white text-blue-700 rounded-full hover:bg-blue-100 transition-colors border border-blue-200">Bank Mandiri</button>
                    <button type="button" onclick="setPaymentTemplate('Bank BNI', 'bni')" class="px-3 py-1.5 text-xs font-medium bg-white text-blue-700 rounded-full hover:bg-blue-100 transition-colors border border-blue-200">Bank BNI</button>
                    <button type="button" onclick="setPaymentTemplate('GoPay', 'gopay')" class="px-3 py-1.5 text-xs font-medium bg-white text-blue-700 rounded-full hover:bg-blue-100 transition-colors border border-blue-200">GoPay</button>
                    <button type="button" onclick="setPaymentTemplate('OVO', 'ovo')" class="px-3 py-1.5 text-xs font-medium bg-white text-blue-700 rounded-full hover:bg-blue-100 transition-colors border border-blue-200">OVO</button>
                    <button type="button" onclick="setPaymentTemplate('Dana', 'dana')" class="px-3 py-1.5 text-xs font-medium bg-white text-blue-700 rounded-full hover:bg-blue-100 transition-colors border border-blue-200">Dana</button>
                    <button type="button" onclick="setPaymentTemplate('ShopeePay', 'shopeepay')" class="px-3 py-1.5 text-xs font-medium bg-white text-blue-700 rounded-full hover:bg-blue-100 transition-colors border border-blue-200">ShopeePay</button>
                    <button type="button" onclick="setPaymentTemplate('QRIS', 'qris')" class="px-3 py-1.5 text-xs font-medium bg-white text-blue-700 rounded-full hover:bg-blue-100 transition-colors border border-blue-200">QRIS</button>
                </div>
            </div>
        </div>
        
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.payment-methods.index') }}" 
               class="px-4 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-cuan-dark text-white font-semibold rounded-lg hover:bg-cuan-green">
                <i class="fas fa-save mr-2"></i>Simpan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function setPaymentTemplate(name, code) {
    document.getElementById('name').value = name;
    document.getElementById('code').value = code;
}

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

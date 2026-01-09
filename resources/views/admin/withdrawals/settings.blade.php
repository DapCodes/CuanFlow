@extends('admin.layouts.app')

@section('title', 'Pengaturan Pajak Penarikan')
@section('page-title', 'Pengaturan Pajak')

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <a href="{{ route('admin.withdrawals.index') }}" class="text-gray-500 hover:text-gray-700">Penarikan</a>
</li>
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <span class="text-gray-700">Pengaturan Pajak</span>
</li>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('admin.withdrawals.index') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 mb-6">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali ke Daftar Penarikan
    </a>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Pengaturan Pajak Penarikan</h2>
            <p class="text-sm text-gray-500 mt-1">Atur persentase pajak yang akan dipotong dari setiap penarikan</p>
        </div>

        <form action="{{ route('admin.withdrawals.tax-settings.update') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div>
                <label for="tax_percent" class="block text-sm font-medium text-gray-700 mb-2">
                    Persentase Pajak (%)
                </label>
                <div class="relative">
                    <input type="number" 
                           name="tax_percent" 
                           id="tax_percent" 
                           value="{{ old('tax_percent', $taxPercent) }}"
                           step="0.1"
                           min="0"
                           max="100"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cuan-green focus:border-cuan-green @error('tax_percent') border-red-300 @enderror">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">%</span>
                </div>
                @error('tax_percent')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Pajak ini akan dipotong secara otomatis dari jumlah penarikan user
                </p>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm font-medium text-gray-700 mb-2">Contoh Perhitungan:</p>
                <div class="text-sm text-gray-600 space-y-1">
                    <p>Jumlah Penarikan: Rp 1.000.000</p>
                    <p>Pajak (<span id="examplePercent">{{ $taxPercent }}</span>%): <span class="text-red-600" id="exampleTax">Rp {{ number_format(1000000 * $taxPercent / 100, 0, ',', '.') }}</span></p>
                    <p class="font-semibold">Total Diterima: <span class="text-teal-600" id="exampleNet">Rp {{ number_format(1000000 - (1000000 * $taxPercent / 100), 0, ',', '.') }}</span></p>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" 
                        class="px-6 py-2.5 bg-cuan-dark text-white font-semibold rounded-lg hover:bg-cuan-green transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('tax_percent').addEventListener('input', function() {
        const percent = parseFloat(this.value) || 0;
        const amount = 1000000;
        const tax = amount * (percent / 100);
        const net = amount - tax;
        
        document.getElementById('examplePercent').textContent = percent;
        document.getElementById('exampleTax').textContent = 'Rp ' + Math.round(tax).toLocaleString('id-ID');
        document.getElementById('exampleNet').textContent = 'Rp ' + Math.round(net).toLocaleString('id-ID');
    });
</script>
@endsection

@extends('layouts.app')

@section('title', 'Ajukan Penarikan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Back Button -->
        <a href="{{ route('withdraw.index') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 mb-6 group transition-colors">
            <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
            Kembali ke Riwayat
        </a>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <!-- Header -->
            <div class="bg-gradient-to-r from-teal-600 to-teal-500 px-8 py-6 text-white">
                <h1 class="text-2xl font-bold">Ajukan Penarikan</h1>
                <p class="text-teal-50 text-sm mt-1 opacity-90">Tarik saldo keuntungan Anda dengan aman dan cepat</p>
            </div>

            <div class="p-8">
                <!-- Balance Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                    <div class="bg-teal-50 rounded-xl p-5 border border-teal-100">
                        <p class="text-xs font-semibold text-teal-600 uppercase tracking-wider mb-1">Total Saldo Tersedia</p>
                        <p class="text-2xl font-bold text-teal-900 leading-none">Rp {{ number_format($actualBalance, 0, ',', '.') }}</p>
                        <p class="text-[10px] text-teal-500 mt-2 italic">* Sudah dikurangi penarikan pending</p>
                    </div>
                    <div class="bg-amber-50 rounded-xl p-5 border border-amber-100">
                        <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider mb-1">Pajak Penarikan</p>
                        <p class="text-2xl font-bold text-amber-900 leading-none">{{ $taxPercent }}%</p>
                        <p class="text-[10px] text-amber-500 mt-2">* Dipotong otomatis dari nominal penarikan</p>
                    </div>
                </div>

                <!-- Form -->
                <form action="{{ route('withdraw.store') }}" method="POST" class="space-y-6" id="withdrawForm">
                    @csrf

                    <!-- Amount -->
                    <div>
                        <label for="amount" class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Penarikan (IDR)</label>
                        <div class="relative group">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold group-focus-within:text-teal-600 transition-colors">Rp</span>
                            <input type="number" 
                                   name="amount" 
                                   id="amount" 
                                   class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 focus:bg-white transition-all outline-none font-bold text-lg @error('amount') border-red-500 @enderror" 
                                   placeholder="Min. 10.000"
                                   min="10000"
                                   max="{{ $actualBalance }}"
                                   value="{{ old('amount') }}"
                                   required>
                        </div>
                        @error('amount')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Method Select -->
                    <div>
                        <label for="payment_method_id" class="block text-sm font-semibold text-gray-700 mb-2">Metode Penarikan</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($paymentMethods as $pm)
                            <label class="relative cursor-pointer">
                                <input type="radio" name="payment_method_id" value="{{ $pm->id }}" class="peer sr-only" {{ old('payment_method_id') == $pm->id ? 'checked' : '' }} required>
                                <div class="p-3 border-2 rounded-xl border-gray-100 bg-gray-50 hover:bg-white peer-checked:border-teal-500 peer-checked:bg-teal-50 transition-all text-center">
                                    <p class="text-xs font-bold text-gray-700 peer-checked:text-teal-700">{{ $pm->name }}</p>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error('payment_method_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Account Number -->
                        <div>
                            <label for="account_number" class="block text-sm font-semibold text-gray-700 mb-2">Nomor Rekening / E-Wallet</label>
                            <input type="text" 
                                   name="account_number" 
                                   id="account_number" 
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 focus:bg-white transition-all outline-none @error('account_number') border-red-500 @enderror" 
                                   placeholder="Contoh: 08123456789 atau 12345678"
                                   value="{{ old('account_number') }}"
                                   required>
                            @error('account_number')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Account Name -->
                        <div>
                            <label for="account_name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Pemilik Rekening</label>
                            <input type="text" 
                                   name="account_name" 
                                   id="account_name" 
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 focus:bg-white transition-all outline-none @error('account_name') border-red-500 @enderror" 
                                   placeholder="Nama sesuai rekening"
                                   value="{{ old('account_name') }}"
                                   required>
                            @error('account_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Calculation Preview -->
                    <div id="calculationPreview" class="hidden bg-gray-50 rounded-xl p-5 border border-dashed border-gray-200 space-y-2">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Jumlah Pengajuan</span>
                            <span id="previewRawAmount">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-sm text-red-600">
                            <span>Pajak ({{ $taxPercent }}%)</span>
                            <span id="previewTaxAmount">- Rp 0</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold text-teal-600 pt-2 border-t border-gray-200">
                            <span>Total Diterima</span>
                            <span id="previewNetAmount">Rp 0</span>
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full py-4 bg-gradient-to-r from-teal-600 to-teal-500 text-white font-bold rounded-xl shadow-lg shadow-teal-500/20 hover:shadow-teal-500/40 hover:-translate-y-0.5 transition-all text-lg flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i>
                        Ajukan Penarikan Sekarang
                    </button>
                    
                    <p class="text-center text-[10px] text-gray-400">
                        Pastikan data rekening sudah benar. Kesalahan input data bukan tanggung jawab kami.
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const amountInput = document.getElementById('amount');
    const taxPercent = {{ $taxPercent }};
    const calculationPreview = document.getElementById('calculationPreview');
    const previewRawAmount = document.getElementById('previewRawAmount');
    const previewTaxAmount = document.getElementById('previewTaxAmount');
    const previewNetAmount = document.getElementById('previewNetAmount');

    const formatRupiah = (number) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);
    }

    amountInput.addEventListener('input', function() {
        const val = parseFloat(this.value) || 0;
        
        if (val >= 10000) {
            calculationPreview.classList.remove('hidden');
            const tax = val * (taxPercent / 100);
            const net = val - tax;

            previewRawAmount.textContent = formatRupiah(val);
            previewTaxAmount.textContent = '- ' + formatRupiah(tax);
            previewNetAmount.textContent = formatRupiah(net);
        } else {
            calculationPreview.classList.add('hidden');
        }
    });

    // Trigger on load if there's old value
    if (amountInput.value) {
        amountInput.dispatchEvent(new Event('input'));
    }
</script>
@endsection

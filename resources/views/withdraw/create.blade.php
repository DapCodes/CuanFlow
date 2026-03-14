@extends('layouts.app')

@section('title', 'Ajukan Penarikan')

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('withdraw.index') }}" class="text-gray-400 hover:text-gray-900 transition-colors">Riwayat Penarikan</a>
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('withdraw.confirm-password') }}" class="text-gray-400 hover:text-gray-900 transition-colors">Verifikasi</a>
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Ajukan Penarikan</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900 leading-tight">
                    Ajukan Penarikan
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Tarik saldo keuntungan Anda dengan aman dan cepat ke rekening tujuan.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                <a href="{{ route('withdraw.index') }}" 
                   class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-white border border-gray-200 text-gray-600 font-black text-[10px] uppercase tracking-widest hover:bg-gray-50 hover:text-gray-900 transition-all active:scale-95 shadow-sm">
                    <span>Riwayat Penarikan</span>
                </a>
            </div>
        </section>

        {{-- INFO SALDO & PAJAK --}}
        <section class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-card-container class="p-8">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Saldo Tersedia</p>
                <p class="mt-2 text-3xl font-black text-gray-900">Rp {{ number_format($actualBalance, 0, ',', '.') }}</p>
                <div class="mt-4 pt-4 border-t border-gray-100 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                    Sudah dikurangi pengajuan pending
                </div>
            </x-card-container>

            <x-card-container class="p-8">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Pajak Penarikan</p>
                <p class="mt-2 text-3xl font-black text-gray-900">{{ $taxPercent }}%</p>
                <div class="mt-4 pt-4 border-t border-gray-100 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                    Dipotong otomatis dari nominal penarikan
                </div>
            </x-card-container>
        </section>

        {{-- FORM PENARIKAN --}}
        {{-- FORM PENARIKAN --}}
        <x-card-container>
            <div class="p-6 md:p-8">
                <form action="{{ route('withdraw.store') }}" method="POST" class="space-y-8" id="withdrawForm">
                    @csrf

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                        {{-- Left Column: Input Amount --}}
                        <div class="lg:col-span-7 space-y-6">
                            <div>
                                <label for="amount" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-3">Jumlah Penarikan (IDR)</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                                        <span class="text-gray-400 font-black group-focus-within:text-cuan-green transition-colors text-lg">Rp</span>
                                    </div>
                                    <input type="number" 
                                           name="amount" 
                                           id="amount" 
                                           class="w-full pl-16 pr-6 py-5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green focus:bg-white transition-all outline-none font-black text-3xl text-gray-900 @error('amount') border-red-500 @enderror" 
                                           placeholder="10.000"
                                           min="10000"
                                           max="{{ $actualBalance }}"
                                           value="{{ old('amount') }}"
                                           required>
                                </div>
                                <p class="mt-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                    Minimal Penarikan: Rp 10.000
                                </p>
                                @error('amount')
                                    <p class="mt-2 text-xs font-bold text-red-500 flex items-center gap-1.5">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="space-y-4">
                                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-500">Metode Penarikan</label>
                                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($paymentMethods as $pm)
                                    <label class="relative cursor-pointer group h-full">
                                        <input type="radio" name="payment_method_id" value="{{ $pm->id }}" class="peer sr-only" {{ old('payment_method_id') == $pm->id ? 'checked' : '' }} required>
                                        <div class="h-full p-4 border-2 rounded-2xl border-gray-100 bg-gray-50 hover:bg-white peer-checked:border-cuan-green peer-checked:bg-cuan-green/5 transition-all text-center flex flex-col items-center justify-center gap-3">
                                            @if($pm->icon && Storage::disk('public')->exists($pm->icon))
                                                <img src="{{ Storage::url($pm->icon) }}" class="h-8 object-contain filter group-hover:brightness-110">
                                            @endif
                                            <p class="text-[10px] font-black text-gray-700 peer-checked:text-cuan-dark uppercase tracking-widest">{{ $pm->name }}</p>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                                @error('payment_method_id')
                                    <p class="mt-2 text-xs font-bold text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Right Column: Preview & Destination --}}
                        <div class="lg:col-span-5 space-y-6">
                            <div class="bg-gray-50 border border-gray-100 rounded-[2rem] p-6 space-y-5 shadow-inner">
                                <h3 class="text-[10px] font-black text-gray-900 uppercase tracking-widest">
                                    Rincian Penarikan
                               </h3>
                                
                                <div id="calculationPreview" class="space-y-4">
                                    <div class="flex justify-between items-center text-sm text-gray-500 font-bold">
                                        <span>Pengajuan</span>
                                        <span id="previewRawAmount" class="text-gray-900">Rp 0</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm text-gray-500 font-bold">
                                        <span>Potongan Pajak ({{ $taxPercent }}%)</span>
                                        <span id="previewTaxAmount" class="text-red-500">- Rp 0</span>
                                    </div>
                                    <div class="pt-4 border-t border-gray-200 border-dashed flex justify-between items-end">
                                        <span class="text-xs font-black uppercase tracking-widest text-gray-900">Total Diterima</span>
                                        <div class="text-right">
                                            <span id="previewNetAmount" class="block text-2xl font-black text-cuan-green leading-none">Rp 0</span>
                                            <span class="text-[10px] uppercase font-bold text-gray-400 mt-1 block">Estimasi: 1-2 hari kerja</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-5 pt-2">
                                <div>
                                    <label for="account_number" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Nomor Rekening / E-Wallet</label>
                                    <input type="text" 
                                           name="account_number" 
                                           id="account_number" 
                                           class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green focus:bg-white transition-all outline-none text-sm font-bold text-gray-900 @error('account_number') border-red-500 @enderror" 
                                           placeholder="Contoh: 1234567890"
                                           value="{{ old('account_number') }}"
                                           required>
                                </div>

                                <div>
                                    <label for="account_name" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Nama Pemilik</label>
                                    <input type="text" 
                                           name="account_name" 
                                           id="account_name" 
                                           class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green focus:bg-white transition-all outline-none text-sm font-bold text-gray-900 @error('account_name') border-red-500 @enderror" 
                                           placeholder="Sesuai buku tabungan"
                                           value="{{ old('account_name') }}"
                                           required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-8 border-t border-gray-100 flex flex-col items-center gap-4">
                        <button type="submit" 
                                class="w-full md:w-auto md:min-w-[400px] py-4 px-8 bg-black text-white font-black text-[10px] uppercase tracking-widest rounded-2xl hover:bg-cuan-green transition-all active:scale-95 flex items-center justify-center shadow-xl hover:shadow-cuan-green/30">
                            Konfirmasi & Ajukan Penarikan
                        </button>
                        <p class="text-[10px] font-bold text-gray-400 text-center max-w-sm uppercase tracking-widest leading-relaxed">
                            Dengan menekan tombol di atas, Anda menyetujui syarat & ketentuan penarikan CuanFlow.
                        </p>
                    </div>
                </form>
            </div>
        </x-card-container>
    </div>
</main>

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

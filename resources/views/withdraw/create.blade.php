@extends('layouts.app')

@section('title', 'Ajukan Penarikan')

@section('breadcrumb')
<li class="flex items-center">
    <a href="{{ route('withdraw.index') }}" class="text-gray-400 hover:text-gray-900 transition-colors">Riwayat Penarikan</a>
    <svg class="w-4 h-4 text-gray-400 mx-1.5" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('withdraw.confirm-password') }}" class="text-gray-400 hover:text-gray-900 transition-colors">Verifikasi</a>
    <svg class="w-4 h-4 text-gray-400 mx-1.5" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-bold">Ajukan Penarikan</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-teal-50 text-teal-600 border border-teal-100">
                        <i class="fas fa-wallet text-sm"></i>
                    </span>
                    <span>Ajukan Penarikan</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Tarik saldo keuntungan Anda dengan aman dan cepat ke rekening tujuan.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                <a href="{{ route('withdraw.index') }}" 
                   class="inline-flex items-center gap-2 rounded-lg bg-white border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 transition-all">
                    <i class="fas fa-history text-gray-400"></i>
                    <span>Riwayat Penarikan</span>
                </a>
            </div>
        </section>

        {{-- INFO SALDO & PAJAK --}}
        <section class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm relative overflow-hidden group">
                <div class="relative z-10 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-teal-50 flex items-center justify-center text-teal-600 border border-teal-100 group-hover:scale-110 transition-transform">
                        <i class="fas fa-money-bill-wave text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Saldo Tersedia</p>
                        <p class="mt-0.5 text-2xl font-bold text-gray-900">Rp {{ number_format($actualBalance, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-50 flex items-center gap-1.5 text-[10px] text-gray-400">
                    <i class="fas fa-info-circle text-teal-500/50"></i>
                    <span>Setelah dikurangi penarikan pernding</span>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm relative overflow-hidden group">
                <div class="relative z-10 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 border border-amber-100 group-hover:scale-110 transition-transform">
                        <i class="fas fa-percent text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Pajak Penarikan</p>
                        <p class="mt-0.5 text-2xl font-bold text-gray-900">{{ $taxPercent }}%</p>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-50 flex items-center gap-1.5 text-[10px] text-gray-400">
                    <i class="fas fa-info-circle text-amber-500/50"></i>
                    <span>Dipotong otomatis dari nominal penarikan</span>
                </div>
            </div>
        </section>

        {{-- FORM PENARIKAN --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 md:p-8">
                <form action="{{ route('withdraw.store') }}" method="POST" class="space-y-8" id="withdrawForm">
                    @csrf

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                        {{-- Left Column: Input Amount --}}
                        <div class="lg:col-span-7 space-y-6">
                            <div>
                                <label for="amount" class="block text-sm font-bold text-gray-700 mb-3">Jumlah Penarikan (IDR)</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-gray-400 font-bold group-focus-within:text-teal-600 transition-colors">Rp</span>
                                    </div>
                                    <input type="number" 
                                           name="amount" 
                                           id="amount" 
                                           class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 focus:bg-white transition-all outline-none font-bold text-2xl @error('amount') border-red-500 @enderror" 
                                           placeholder="Min. 10.000"
                                           min="10000"
                                           max="{{ $actualBalance }}"
                                           value="{{ old('amount') }}"
                                           required>
                                </div>
                                @error('amount')
                                    <p class="mt-2 text-sm text-red-600 flex items-center gap-1.5">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="space-y-4">
                                <label class="block text-sm font-bold text-gray-700">Metode Penarikan</label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    @foreach($paymentMethods as $pm)
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="payment_method_id" value="{{ $pm->id }}" class="peer sr-only" {{ old('payment_method_id') == $pm->id ? 'checked' : '' }} required>
                                        <div class="p-4 border-2 rounded-xl border-gray-100 bg-gray-50 hover:bg-white peer-checked:border-teal-500 peer-checked:bg-teal-50 peer-checked:shadow-sm transition-all text-center">
                                            @if($pm->icon && Storage::disk('public')->exists($pm->icon))
                                                <img src="{{ Storage::url($pm->icon) }}" class="h-8 mx-auto mb-2 object-contain filter group-hover:brightness-110">
                                            @else
                                                <div class="w-8 h-8 rounded-lg bg-gray-200 flex items-center justify-center mx-auto mb-2 group-hover:bg-teal-100 transition-colors">
                                                    <i class="fas fa-university text-gray-400 group-hover:text-teal-600 text-xs"></i>
                                                </div>
                                            @endif
                                            <p class="text-[11px] font-bold text-gray-700 peer-checked:text-teal-700 uppercase tracking-tight">{{ $pm->name }}</p>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                                @error('payment_method_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Right Column: Preview & Destination --}}
                        <div class="lg:col-span-5 space-y-6">
                            <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 space-y-4">
                                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                    <i class="fas fa-calculator text-teal-500"></i>
                                    Rincian Penarikan
                                </h3>
                                
                                <div id="calculationPreview" class="space-y-3">
                                    <div class="flex justify-between text-sm text-gray-500">
                                        <span>Pengajuan</span>
                                        <span id="previewRawAmount" class="font-medium text-gray-900">Rp 0</span>
                                    </div>
                                    <div class="flex justify-between text-sm text-gray-500">
                                        <span>Potongan Pajak ({{ $taxPercent }}%)</span>
                                        <span id="previewTaxAmount" class="font-medium text-red-500">- Rp 0</span>
                                    </div>
                                    <div class="pt-3 border-t border-gray-200 flex justify-between items-end">
                                        <span class="text-sm font-bold text-gray-800">Total Diterima</span>
                                        <div class="text-right">
                                            <span id="previewNetAmount" class="block text-xl font-black text-teal-600 leading-none">Rp 0</span>
                                            <span class="text-[9px] text-gray-400 italic">Estimasi waktu: 1-2 hari kerja</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4 pt-2">
                                <div>
                                    <label for="account_number" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Nomor Rekening / E-Wallet</label>
                                    <input type="text" 
                                           name="account_number" 
                                           id="account_number" 
                                           class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all outline-none text-sm font-medium @error('account_number') border-red-500 @enderror" 
                                           placeholder="Pencairan tujuan..."
                                           value="{{ old('account_number') }}"
                                           required>
                                </div>

                                <div>
                                    <label for="account_name" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Nama Pemilik</label>
                                    <input type="text" 
                                           name="account_name" 
                                           id="account_name" 
                                           class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all outline-none text-sm font-medium @error('account_name') border-red-500 @enderror" 
                                           placeholder="Nama sesuai buku tabungan"
                                           value="{{ old('account_name') }}"
                                           required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex flex-col items-center gap-4">
                        <button type="submit" 
                                class="w-full md:w-auto md:min-w-[300px] py-4 px-8 bg-gray-900 text-white font-bold rounded-xl shadow-xl shadow-gray-200 hover:bg-teal-600 hover:shadow-teal-500/30 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-3 group">
                            <span>Konfirmasi & Ajukan Penarikan</span>
                            <i class="fas fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
                        </button>
                        <p class="text-[10px] text-gray-400 text-center max-w-sm">
                            Dengan menekan tombol di atas, Anda menyetujui syarat dan ketentuan penarikan saldo di CuanFlow.
                        </p>
                    </div>
                </form>
            </div>
        </section>
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

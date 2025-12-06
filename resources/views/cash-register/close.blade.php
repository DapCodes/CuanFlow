@extends('layouts.app')

@section('title', 'Tutup Toko - CuanFlow')

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('pos.index') }}" class="text-gray-600 hover:text-indigo-600">Point of Sale</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Tutup Toko</span>
</li>
@endsection

@section('content')
<!-- Toast Container -->
<div id="toastContainer" class="fixed top-20 right-4 z-50 space-y-2"></div>

<x-card-container>
<main class="flex-grow py-8 px-4">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-100 to-purple-50 rounded-2xl p-8 mb-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                            <i class="fas fa-cash-register text-3xl text-black"></i>
                        </div>
                        <div class="text-black">
                            <h1 class="text-3xl font-bold">Tutup Toko</h1>
                            <p class="text-slate-600">Rekap Penjualan Hari Ini</p>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-slate-600 mb-1">Kasir: {{ auth()->user()->name }}</p>
                    <p class="text-sm text-slate-600">Dibuka: {{ $register->opened_at->format('d/m/Y H:i') }}</p>
                    <p class="text-xs text-slate-600 mt-1">ID: #{{ $register->id }}</p>
                </div>
            </div>
        </div>

        <!-- Ringkasan Penjualan -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Transaksi -->
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-receipt text-blue-600 text-xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-gray-900">{{ number_format($register->total_transactions, 0, ',', '.') }}</span>
                </div>
                <p class="text-sm font-semibold text-gray-700">Total Transaksi</p>
                <p class="text-xs text-gray-500 mt-1">transaksi selesai hari ini</p>
            </div>

            <!-- Total Penjualan -->
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-green-600 text-xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-green-600">Rp {{ number_format($register->total_sales, 0, ',', '.') }}</span>
                </div>
                <p class="text-sm font-semibold text-gray-700">Total Penjualan</p>
                <p class="text-xs text-gray-500 mt-1">omset hari ini</p>
            </div>

            <!-- Modal Awal -->
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-wallet text-purple-600 text-xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-purple-600">Rp {{ number_format($register->opening_amount, 0, ',', '.') }}</span>
                </div>
                <p class="text-sm font-semibold text-gray-700">Modal Awal</p>
                <p class="text-xs text-gray-500 mt-1">saldo awal kasir</p>
            </div>
        </div>

        <!-- Detail Metode Pembayaran -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-credit-card text-indigo-600"></i>
                Rincian Metode Pembayaran
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-5 border border-green-200">
                    <div class="flex items-center gap-3">
                        <div class="w-14 h-14 bg-green-500 rounded-lg flex items-center justify-center shadow-md">
                            <i class="fas fa-money-bill-wave text-white text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-green-700 font-medium">TUNAI</p>
                            <p class="text-2xl font-bold text-green-900">Rp {{ number_format($register->total_cash, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-5 border border-purple-200">
                    <div class="flex items-center gap-3">
                        <div class="w-14 h-14 bg-purple-500 rounded-lg flex items-center justify-center shadow-md">
                            <i class="fas fa-qrcode text-white text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-purple-700 font-medium">QRIS</p>
                            <p class="text-2xl font-bold text-purple-900">Rp {{ number_format($register->total_qris, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-5 border border-blue-200">
                    <div class="flex items-center gap-3">
                        <div class="w-14 h-14 bg-blue-500 rounded-lg flex items-center justify-center shadow-md">
                            <i class="fas fa-building-columns text-white text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-blue-700 font-medium">TRANSFER</p>
                            <p class="text-2xl font-bold text-blue-900">Rp {{ number_format($register->total_transfer, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Tutup Toko -->
        <div class="bg-white rounded-xl shadow-md p-8 mb-8">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <i class="fas fa-calculator text-indigo-600"></i>
                Perhitungan Kasir
            </h3>

            <form id="closeRegisterForm" class="space-y-6">
                @csrf
                
                <!-- Expected Amount Display -->
                <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 border-2 border-indigo-300 rounded-xl p-6">
                    <div class="flex items-center gap-4 mb-3">
                        <div class="w-14 h-14 bg-indigo-500 rounded-lg flex items-center justify-center shadow-md">
                            <i class="fas fa-calculator text-white text-2xl"></i>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-indigo-900 mb-1">
                                Saldo Tunai yang Diharapkan
                            </label>
                            <div class="text-4xl font-bold text-indigo-600">
                                Rp {{ number_format($register->expected_amount, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                    <div class="bg-white/50 rounded-lg p-3 mt-3">
                        <p class="text-sm text-gray-700">
                            <i class="fas fa-info-circle text-indigo-500 mr-2"></i>
                            <span class="font-medium">Perhitungan:</span> Modal Awal (Rp {{ number_format($register->opening_amount, 0, ',', '.') }}) + Penjualan Tunai (Rp {{ number_format($register->total_cash, 0, ',', '.') }})
                        </p>
                    </div>
                </div>

                <!-- Closing Amount Input -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-3">
                        <i class="fas fa-coins text-orange-500 mr-2"></i>
                        Jumlah Uang Aktual di Kasir <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-lg">Rp</span>
                        <input 
                            type="number" 
                            id="closingAmount" 
                            name="closing_amount"
                            class="w-full pl-14 pr-4 py-4 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-xl font-bold text-gray-900 transition-all"
                            placeholder="0"
                            required
                            min="0"
                            step="0.01"
                            onkeyup="calculateDifference()"
                            onchange="calculateDifference()"
                        >
                    </div>
                    <p class="text-sm text-gray-600 mt-2 flex items-center gap-2">
                        <i class="fas fa-hand-holding-usd text-gray-400"></i>
                        Hitung total uang fisik yang ada di kasir Anda saat ini
                    </p>
                </div>

                <!-- Difference Display -->
                <div id="differenceDisplay" class="hidden">
                    <div class="rounded-xl p-6 border-2 transition-all" id="differenceCard">
                        <div class="flex items-center gap-4 mb-2">
                            <div class="w-14 h-14 rounded-lg flex items-center justify-center shadow-md" id="differenceIcon">
                                <i class="text-white text-2xl" id="differenceIconElement"></i>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1" id="differenceLabel">
                                    Selisih
                                </label>
                                <div class="text-4xl font-bold" id="differenceAmount">Rp 0</div>
                            </div>
                        </div>
                        <p class="text-sm mt-3 font-medium" id="differenceNote"></p>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-3">
                        <i class="fas fa-sticky-note text-gray-500 mr-2"></i>
                        Catatan (Opsional)
                    </label>
                    <textarea 
                        name="notes"
                        id="notes"
                        rows="4"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                        placeholder="Tambahkan catatan atau keterangan jika diperlukan..."
                    ></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4 pt-6 border-t-2 border-gray-200">
                    <a href="{{ route('pos.index') }}" class="flex-1 px-6 py-4 border-2 border-gray-300 text-gray-700 rounded-xl font-bold hover:bg-gray-50 transition-all text-center flex items-center justify-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        Kembali ke POS
                    </a>
                    <button type="submit" id="submitBtn" class="flex-1 px-6 py-4 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl font-bold hover:from-red-600 hover:to-red-700 transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                        <i class="fas fa-door-closed"></i>
                        Tutup Toko
                    </button>
                </div>
            </form>
        </div>

        <!-- Recent Transactions -->
        @if($sales->count() > 0)
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-list text-indigo-600"></i>
                Transaksi Hari Ini ({{ $sales->count() }} transaksi)
            </h3>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Invoice</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Waktu</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Metode</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($sales as $sale)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $sale->invoice_number }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $sale->created_at->format('H:i:s') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 text-xs font-bold rounded-full 
                                    @if($sale->payment_method === 'cash') bg-green-100 text-green-700
                                    @elseif($sale->payment_method === 'qris') bg-purple-100 text-purple-700
                                    @else bg-blue-100 text-blue-700
                                    @endif">
                                    {{ strtoupper($sale->payment_method) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm font-bold text-right text-gray-900">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>
</main>
</x-card-container>

<!-- Modal Success -->
<div id="successModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8 transform transition-all">
        <div class="flex justify-center mb-6">
            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check-circle text-green-500 text-5xl"></i>
            </div>
        </div>
        
        <h3 class="text-3xl font-bold text-gray-900 text-center mb-3">Toko Berhasil Ditutup!</h3>
        <p class="text-gray-600 text-center mb-8">Terima kasih atas kerja keras Anda hari ini</p>
        
        <button onclick="window.location.href='{{ route('dashboard') }}'" class="w-full px-6 py-4 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl font-bold hover:from-indigo-600 hover:to-indigo-700 transition-all shadow-lg flex items-center justify-center gap-2">
            <i class="fas fa-home"></i>
            Kembali ke Dashboard
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>
const expectedAmount = {{ $register->expected_amount }};

function showToast(type, message) {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500',
        warning: 'bg-orange-500'
    };
    
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        info: 'fa-info-circle',
        warning: 'fa-exclamation-triangle'
    };
    
    toast.className = `${colors[type]} text-white px-6 py-4 rounded-xl shadow-lg flex items-center gap-3 min-w-[300px]`;
    toast.innerHTML = `<i class="fas ${icons[type]} text-xl"></i><span class="font-medium">${message}</span>`;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

function calculateDifference() {
    const closingAmountInput = document.getElementById('closingAmount');
    const closingAmount = parseFloat(closingAmountInput.value) || 0;
    const difference = closingAmount - expectedAmount;
    
    const display = document.getElementById('differenceDisplay');
    const card = document.getElementById('differenceCard');
    const icon = document.getElementById('differenceIcon');
    const iconElement = document.getElementById('differenceIconElement');
    const amountEl = document.getElementById('differenceAmount');
    const noteEl = document.getElementById('differenceNote');
    const labelEl = document.getElementById('differenceLabel');
    
    if (closingAmount > 0) {
        display.classList.remove('hidden');
        
        amountEl.textContent = 'Rp ' + Math.abs(difference).toLocaleString('id-ID');
        
        if (difference > 0) {
            // Surplus
            card.className = 'rounded-xl p-6 border-2 border-green-300 bg-gradient-to-br from-green-50 to-green-100 transition-all';
            icon.className = 'w-14 h-14 bg-green-500 rounded-lg flex items-center justify-center shadow-md';
            iconElement.className = 'fas fa-arrow-up text-white text-2xl';
            amountEl.className = 'text-4xl font-bold text-green-600';
            labelEl.className = 'block text-sm font-semibold mb-1 text-green-900';
            labelEl.textContent = 'Selisih Lebih (Surplus)';
            noteEl.className = 'text-sm mt-3 font-medium text-green-700 bg-white/50 rounded-lg p-3';
            noteEl.innerHTML = '<i class="fas fa-info-circle mr-2"></i>Uang di kasir lebih banyak dari yang diharapkan';
        } else if (difference < 0) {
            // Minus
            card.className = 'rounded-xl p-6 border-2 border-red-300 bg-gradient-to-br from-red-50 to-red-100 transition-all';
            icon.className = 'w-14 h-14 bg-red-500 rounded-lg flex items-center justify-center shadow-md';
            iconElement.className = 'fas fa-arrow-down text-white text-2xl';
            amountEl.className = 'text-4xl font-bold text-red-600';
            labelEl.className = 'block text-sm font-semibold mb-1 text-red-900';
            labelEl.textContent = 'Selisih Kurang (Minus)';
            noteEl.className = 'text-sm mt-3 font-medium text-red-700 bg-white/50 rounded-lg p-3';
            noteEl.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Uang di kasir kurang dari yang diharapkan';
        } else {
            // Pas
            card.className = 'rounded-xl p-6 border-2 border-blue-300 bg-gradient-to-br from-blue-50 to-blue-100 transition-all';
            icon.className = 'w-14 h-14 bg-blue-500 rounded-lg flex items-center justify-center shadow-md';
            iconElement.className = 'fas fa-check text-white text-2xl';
            amountEl.className = 'text-4xl font-bold text-blue-600';
            labelEl.className = 'block text-sm font-semibold mb-1 text-blue-900';
            labelEl.textContent = 'Tidak Ada Selisih';
            noteEl.className = 'text-sm mt-3 font-medium text-blue-700 bg-white/50 rounded-lg p-3';
            noteEl.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Sesuai! Uang di kasir pas dengan perhitungan';
        }
    } else {
        display.classList.add('hidden');
    }
}

document.getElementById('closeRegisterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const closingAmount = parseFloat(document.getElementById('closingAmount').value);
    const notes = document.getElementById('notes').value;
    const submitBtn = document.getElementById('submitBtn');
    
    if (!closingAmount || closingAmount < 0) {
        showToast('error', 'Mohon masukkan jumlah uang aktual yang valid');
        return;
    }
    
    // Confirm
    if (!confirm('Yakin ingin menutup toko? Tindakan ini tidak dapat dibatalkan.')) {
        return;
    }
    
    // Disable button
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
    
    fetch('{{ route("cash-register.process-close") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            closing_amount: closingAmount,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Toko berhasil ditutup!');
            setTimeout(() => {
                document.getElementById('successModal').classList.remove('hidden');
            }, 500);
        } else {
            showToast('error', data.message || 'Gagal menutup toko');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-door-closed mr-2"></i>Tutup Toko';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Terjadi kesalahan saat menutup toko');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-door-closed mr-2"></i>Tutup Toko';
    });
});

// Auto-focus on load
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('closingAmount').focus();
});
</script>
@endpush
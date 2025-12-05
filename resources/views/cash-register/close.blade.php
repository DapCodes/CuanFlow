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
<div id="toastContainer" class="fixed top-20 right-4 z-50 space-y-2"></div>

<main class="flex-grow py-8 px-4">
    <div class="max-w-5xl mx-auto">
        
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Tutup Toko</h1>
                <p class="text-gray-600">Rekap penjualan hari ini</p>
                <p class="text-sm text-gray-500 mt-2">
                    Dibuka: {{ $register->opened_at->format('d/m/Y H:i') }}
                </p>
            </div>

            <!-- Ringkasan Penjualan -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Total Transaksi -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-blue-600 text-sm font-semibold">Total Transaksi</span>
                        <i class="fas fa-receipt text-blue-400 text-2xl"></i>
                    </div>
                    <p class="text-3xl font-bold text-blue-900">{{ number_format($register->total_transactions, 0, ',', '.') }}</p>
                    <p class="text-xs text-blue-600 mt-1">transaksi hari ini</p>
                </div>

                <!-- Total Penjualan -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-green-600 text-sm font-semibold">Total Penjualan</span>
                        <i class="fas fa-chart-line text-green-400 text-2xl"></i>
                    </div>
                    <p class="text-3xl font-bold text-green-900">Rp {{ number_format($register->total_sales, 0, ',', '.') }}</p>
                    <p class="text-xs text-green-600 mt-1">omset hari ini</p>
                </div>

                <!-- Modal Awal -->
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-purple-600 text-sm font-semibold">Modal Awal</span>
                        <i class="fas fa-wallet text-purple-400 text-2xl"></i>
                    </div>
                    <p class="text-3xl font-bold text-purple-900">Rp {{ number_format($register->opening_amount, 0, ',', '.') }}</p>
                    <p class="text-xs text-purple-600 mt-1">saldo awal</p>
                </div>
            </div>

            <!-- Detail Metode Pembayaran -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white border-2 border-gray-200 rounded-xl p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tunai</p>
                            <p class="text-xl font-bold text-gray-900">Rp {{ number_format($register->total_cash, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border-2 border-gray-200 rounded-xl p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-qrcode text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">QRIS</p>
                            <p class="text-xl font-bold text-gray-900">Rp {{ number_format($register->total_qris, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border-2 border-gray-200 rounded-xl p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-building-columns text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Transfer</p>
                            <p class="text-xl font-bold text-gray-900">Rp {{ number_format($register->total_transfer, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Tutup Toko -->
            <form id="closeRegisterForm" class="space-y-6">
                @csrf
                
                <!-- Expected Amount (Read-only) -->
                <div class="bg-indigo-50 border-2 border-indigo-200 rounded-xl p-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calculator text-indigo-600 mr-2"></i>
                        Saldo yang Diharapkan (Expected Amount)
                    </label>
                    <div class="text-3xl font-bold text-indigo-600">
                        Rp {{ number_format($register->expected_amount, 0, ',', '.') }}
                    </div>
                    <p class="text-xs text-gray-600 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Modal awal + Total penjualan tunai = Rp {{ number_format($register->opening_amount, 0, ',', '.') }} + Rp {{ number_format($register->total_cash, 0, ',', '.') }}
                    </p>
                </div>

                <!-- Closing Amount Input -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-coins text-orange-600 mr-2"></i>
                        Jumlah Uang Aktual di Kasir <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="closingAmount" 
                        name="closing_amount"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent text-lg font-semibold"
                        placeholder="Masukkan jumlah uang aktual"
                        required
                        onkeyup="calculateDifference()"
                    >
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-hand-holding-usd mr-1"></i>
                        Hitung total uang fisik yang ada di kasir Anda
                    </p>
                </div>

                <!-- Difference Display -->
                <div id="differenceDisplay" class="hidden">
                    <div class="border-2 rounded-xl p-6" id="differenceCard">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-exchange-alt mr-2"></i>
                            Selisih (Difference)
                        </label>
                        <div class="text-3xl font-bold" id="differenceAmount">Rp 0</div>
                        <p class="text-sm mt-2" id="differenceNote"></p>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-sticky-note text-gray-600 mr-2"></i>
                        Catatan (Opsional)
                    </label>
                    <textarea 
                        name="notes"
                        id="notes"
                        rows="4"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        placeholder="Tambahkan catatan jika ada..."
                    ></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('pos.index') }}" class="flex-1 px-6 py-4 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-all text-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke POS
                    </a>
                    <button type="submit" class="flex-1 px-6 py-4 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl font-semibold hover:from-red-600 hover:to-red-700 transition-all shadow-lg hover:shadow-xl">
                        <i class="fas fa-door-closed mr-2"></i>
                        Tutup Toko
                    </button>
                </div>
            </form>

        </div>

        <!-- Recent Transactions -->
        @if($sales->count() > 0)
        <div class="bg-white rounded-2xl shadow-lg p-6 mt-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">
                <i class="fas fa-list text-indigo-600 mr-2"></i>
                Transaksi Hari Ini ({{ $sales->count() }} transaksi)
            </h3>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Invoice</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Waktu</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Metode</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($sales as $sale)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $sale->invoice_number }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $sale->created_at->format('H:i') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
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

<!-- Modal Success -->
<div id="successModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8">
        <div class="flex justify-center mb-6">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>
        
        <h3 class="text-2xl font-bold text-gray-900 text-center mb-2">Toko Berhasil Ditutup!</h3>
        <p class="text-gray-600 text-center mb-6">Terima kasih atas kerja keras Anda hari ini</p>
        
        <button onclick="window.location.href='{{ route('dashboard') }}'" class="w-full px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl font-semibold hover:from-indigo-600 hover:to-purple-700 transition-all shadow-lg">
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
    const colors = { success: 'bg-green-500', error: 'bg-red-500', info: 'bg-blue-500', warning: 'bg-orange-500' };
    toast.className = `${colors[type]} text-white px-4 py-3 rounded-lg shadow-lg text-sm`;
    toast.textContent = message;
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

function calculateDifference() {
    const closingAmount = parseFloat(document.getElementById('closingAmount').value) || 0;
    const difference = closingAmount - expectedAmount;
    
    const display = document.getElementById('differenceDisplay');
    const card = document.getElementById('differenceCard');
    const amountEl = document.getElementById('differenceAmount');
    const noteEl = document.getElementById('differenceNote');
    
    if (closingAmount > 0) {
        display.classList.remove('hidden');
        
        amountEl.textContent = 'Rp ' + Math.abs(difference).toLocaleString('id-ID');
        
        if (difference > 0) {
            card.className = 'border-2 border-green-200 bg-green-50 rounded-xl p-6';
            amountEl.className = 'text-3xl font-bold text-green-600';
            noteEl.className = 'text-sm mt-2 text-green-700';
            noteEl.innerHTML = '<i class="fas fa-arrow-up mr-2"></i>Uang lebih (surplus) dari yang diharapkan';
        } else if (difference < 0) {
            card.className = 'border-2 border-red-200 bg-red-50 rounded-xl p-6';
            amountEl.className = 'text-3xl font-bold text-red-600';
            noteEl.className = 'text-sm mt-2 text-red-700';
            noteEl.innerHTML = '<i class="fas fa-arrow-down mr-2"></i>Uang kurang (minus) dari yang diharapkan';
        } else {
            card.className = 'border-2 border-blue-200 bg-blue-50 rounded-xl p-6';
            amountEl.className = 'text-3xl font-bold text-blue-600';
            noteEl.className = 'text-sm mt-2 text-blue-700';
            noteEl.innerHTML = '<i class="fas fa-check mr-2"></i>Sesuai! Tidak ada selisih';
        }
    } else {
        display.classList.add('hidden');
    }
}

document.getElementById('closeRegisterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const closingAmount = parseFloat(document.getElementById('closingAmount').value);
    const notes = document.getElementById('notes').value;
    
    if (!closingAmount || closingAmount < 0) {
        showToast('error', 'Mohon masukkan jumlah uang aktual');
        return;
    }
    
    // Confirm
    if (!confirm('Yakin ingin menutup toko? Tindakan ini tidak dapat dibatalkan.')) {
        return;
    }
    
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
            document.getElementById('successModal').classList.remove('hidden');
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        showToast('error', 'Gagal menutup toko');
        console.error(error);
    });
});
</script>
@endpush
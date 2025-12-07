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

@push('styles')
<style>
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .stat-card {
            padding: 1rem !important;
        }
        .payment-card {
            padding: 0.75rem !important;
        }
    }
</style>
@endpush

@section('content')
<!-- Toast Container -->
<div id="toastContainer" class="fixed top-20 right-4 z-50 space-y-2"></div>

<main class="flex-grow py-8 px-4">
    <div class="max-w-7xl mx-auto space-y-6">
    
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-6 mb-6 text-white shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-cash-register text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">Tutup Toko</h1>
                    <p class="text-sm text-indigo-100">Rekap Penjualan Hari Ini</p>
                </div>
            </div>
            <div class="text-sm text-indigo-100 sm:text-right">
                <p class="font-medium">{{ auth()->user()->name }}</p>
                <p>{{ $register->opened_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Stats Grid - SIMPLIFIED -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <!-- Total Transaksi -->
        <div class="stat-card bg-white rounded-lg shadow-sm p-4 border border-gray-200">
            <p class="text-xs text-gray-500 font-medium mb-1">Total Transaksi</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($register->total_transactions, 0, ',', '.') }}</p>
        </div>

        <!-- Total Penjualan -->
        <div class="stat-card bg-white rounded-lg shadow-sm p-4 border border-gray-200">
            <p class="text-xs text-gray-500 font-medium mb-1">Total Penjualan</p>
            <p class="text-2xl font-bold text-green-600">Rp {{ number_format($register->total_sales, 0, ',', '.') }}</p>
        </div>

        <!-- Modal Awal -->
        <div class="stat-card bg-white rounded-lg shadow-sm p-4 border border-gray-200">
            <p class="text-xs text-gray-500 font-medium mb-1">Modal Awal</p>
            <p class="text-2xl font-bold text-purple-600">Rp {{ number_format($register->opening_amount, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="bg-white rounded-lg shadow p-5 mb-6">
        <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-credit-card text-indigo-600"></i>
            Metode Pembayaran
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="payment-card bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-money-bill-wave text-white"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-green-700 font-medium">TUNAI</p>
                        <p class="text-lg font-bold text-green-900 truncate">Rp {{ number_format($register->total_cash, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="payment-card bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-qrcode text-white"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-purple-700 font-medium">QRIS</p>
                        <p class="text-lg font-bold text-purple-900 truncate">Rp {{ number_format($register->total_qris, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="payment-card bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-building-columns text-white"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-blue-700 font-medium">TRANSFER</p>
                        <p class="text-lg font-bold text-blue-900 truncate">Rp {{ number_format($register->total_transfer, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Tutup Toko -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
            <i class="fas fa-calculator text-indigo-600"></i>
            Perhitungan Kasir
        </h3>

        <form id="closeRegisterForm" class="space-y-5">
            @csrf
            
            <!-- Expected Amount -->
            <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 border-2 border-indigo-200 rounded-lg p-4">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-indigo-500 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-calculator text-white"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <label class="block text-xs font-semibold text-indigo-900 mb-1">
                            Saldo yang Diharapkan
                        </label>
                        <div class="text-2xl sm:text-3xl font-bold text-indigo-600 truncate">
                            Rp {{ number_format($register->expected_amount, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
                <div class="bg-white/50 rounded-lg p-2 mt-2">
                    <p class="text-xs text-gray-700">
                        <i class="fas fa-info-circle text-indigo-500 mr-1"></i>
                        Modal (Rp {{ number_format($register->opening_amount, 0, ',', '.') }}) + Tunai (Rp {{ number_format($register->total_cash, 0, ',', '.') }})
                    </p>
                </div>
            </div>

            <!-- Closing Amount Input -->
            <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">
                    <i class="fas fa-coins text-orange-500 mr-1"></i>
                    Uang Aktual di Kasir <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">Rp</span>
                    <input 
                        type="number" 
                        id="closingAmount" 
                        name="closing_amount"
                        class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-lg font-bold text-gray-900"
                        placeholder="0"
                        required
                        min="0"
                        step="0.01"
                        onkeyup="calculateDifference()"
                        onchange="calculateDifference()"
                    >
                </div>
                <p class="text-xs text-gray-600 mt-1 flex items-center gap-1">
                    <i class="fas fa-hand-holding-usd text-gray-400"></i>
                    Hitung total uang fisik di kasir
                </p>
            </div>

            <!-- Difference Display -->
            <div id="differenceDisplay" class="hidden">
                <div class="rounded-lg p-4 border-2 transition-all" id="differenceCard">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" id="differenceIcon">
                            <i class="text-white" id="differenceIconElement"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <label class="block text-xs font-semibold mb-1" id="differenceLabel">Selisih</label>
                            <div class="text-2xl sm:text-3xl font-bold truncate" id="differenceAmount">Rp 0</div>
                        </div>
                    </div>
                    <p class="text-xs mt-2 font-medium" id="differenceNote"></p>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">
                    <i class="fas fa-sticky-note text-gray-500 mr-1"></i>
                    Catatan (Opsional)
                </label>
                <textarea 
                    name="notes"
                    id="notes"
                    rows="3"
                    class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="Tambahkan catatan jika diperlukan..."
                ></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t-2 border-gray-200">
                <a href="{{ route('pos.index') }}" class="flex-1 px-4 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-all text-center flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
                <button type="submit" id="submitBtn" class="flex-1 px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg font-semibold hover:from-red-600 hover:to-red-700 transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                    <i class="fas fa-door-closed"></i>
                    <span>Tutup Toko</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Recent Transactions -->
    @if($sales->count() > 0)
    <div class="bg-white rounded-lg shadow p-5">
        <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-list text-indigo-600"></i>
            Transaksi Hari Ini ({{ $sales->count() }})
        </h3>
        
        <div class="overflow-x-auto -mx-5 px-5">
            <table class="w-full min-w-[500px]">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase">Invoice</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase">Waktu</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase">Metode</th>
                        <th class="px-3 py-2 text-right text-xs font-bold text-gray-700 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($sales as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 text-sm font-semibold text-gray-900">{{ $sale->invoice_number }}</td>
                        <td class="px-3 py-2 text-sm text-gray-600">{{ $sale->created_at->format('H:i') }}</td>
                        <td class="px-3 py-2">
                            <span class="px-2 py-1 text-xs font-bold rounded-full 
                                @if($sale->payment_method === 'cash') bg-green-100 text-green-700
                                @elseif($sale->payment_method === 'qris') bg-purple-100 text-purple-700
                                @else bg-blue-100 text-blue-700
                                @endif">
                                {{ strtoupper($sale->payment_method) }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-sm font-bold text-right text-gray-900">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
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
<div id="successModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6">
        <div class="flex justify-center mb-4">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check-circle text-green-500 text-4xl"></i>
            </div>
        </div>
        
        <h3 class="text-2xl font-bold text-gray-900 text-center mb-2">Toko Berhasil Ditutup!</h3>
        <p class="text-gray-600 text-center mb-6 text-sm">Terima kasih atas kerja keras Anda hari ini</p>
        
        <button onclick="window.location.href='{{ route('dashboard') }}'" class="w-full px-4 py-3 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-lg font-semibold hover:from-indigo-600 hover:to-indigo-700 transition-all shadow-lg flex items-center justify-center gap-2">
            <i class="fas fa-home"></i>
            <span>Kembali ke Dashboard</span>
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
    
    toast.className = `${colors[type]} text-white px-4 py-3 rounded-lg shadow-lg flex items-center gap-2 min-w-[280px]`;
    toast.innerHTML = `<i class="fas ${icons[type]}"></i><span class="text-sm font-medium">${message}</span>`;
    
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
            card.className = 'rounded-lg p-4 border-2 border-green-300 bg-gradient-to-br from-green-50 to-green-100';
            icon.className = 'w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center flex-shrink-0';
            iconElement.className = 'fas fa-arrow-up text-white';
            amountEl.className = 'text-2xl sm:text-3xl font-bold text-green-600 truncate';
            labelEl.className = 'block text-xs font-semibold mb-1 text-green-900';
            labelEl.textContent = 'Selisih Lebih';
            noteEl.className = 'text-xs mt-2 font-medium text-green-700';
            noteEl.innerHTML = '<i class="fas fa-info-circle mr-1"></i>Uang di kasir lebih dari yang diharapkan';
        } else if (difference < 0) {
            // Minus
            card.className = 'rounded-lg p-4 border-2 border-red-300 bg-gradient-to-br from-red-50 to-red-100';
            icon.className = 'w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center flex-shrink-0';
            iconElement.className = 'fas fa-arrow-down text-white';
            amountEl.className = 'text-2xl sm:text-3xl font-bold text-red-600 truncate';
            labelEl.className = 'block text-xs font-semibold mb-1 text-red-900';
            labelEl.textContent = 'Selisih Kurang';
            noteEl.className = 'text-xs mt-2 font-medium text-red-700';
            noteEl.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i>Uang di kasir kurang dari yang diharapkan';
        } else {
            // Pas
            card.className = 'rounded-lg p-4 border-2 border-blue-300 bg-gradient-to-br from-blue-50 to-blue-100';
            icon.className = 'w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0';
            iconElement.className = 'fas fa-check text-white';
            amountEl.className = 'text-2xl sm:text-3xl font-bold text-blue-600 truncate';
            labelEl.className = 'block text-xs font-semibold mb-1 text-blue-900';
            labelEl.textContent = 'Tidak Ada Selisih';
            noteEl.className = 'text-xs mt-2 font-medium text-blue-700';
            noteEl.innerHTML = '<i class="fas fa-check-circle mr-1"></i>Sesuai! Uang di kasir pas dengan perhitungan';
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
        showToast('error', 'Mohon masukkan jumlah yang valid');
        return;
    }
    
    if (!confirm('Yakin ingin menutup toko? Tindakan ini tidak dapat dibatalkan.')) {
        return;
    }
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i><span>Memproses...</span>';
    
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
            submitBtn.innerHTML = '<i class="fas fa-door-closed mr-2"></i><span>Tutup Toko</span>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Terjadi kesalahan');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-door-closed mr-2"></i><span>Tutup Toko</span>';
    });
});

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('closingAmount').focus();
});
</script>
@endpush
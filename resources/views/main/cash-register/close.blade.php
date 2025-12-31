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
    @media (max-width: 768px) {
        .stat-card { padding: 0.9rem !important; }
        .payment-card { padding: 0.9rem !important; }
    }
</style>
@endpush

@section('content')
<div id="toastContainer" class="fixed top-20 right-4 z-50 space-y-2"></div>

<main class="flex-grow py-4 md:py-6 px-3 md:px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-4 md:space-y-6">

        {{-- Header: seragam seperti halaman index outlet --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100">
                        <i class="fas fa-cash-register text-sm"></i>
                    </span>
                    <span>Rekap Penjualan</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Hitung pengeluaran dan pendapatan toko Anda kali ini.
                </p>
            </div>
            <div class="text-sm text-gray-600 md:text-right">
                <p class="font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                <p class="text-gray-500">Dibuka: {{ $register->opened_at->format('d/m/Y H:i') }}</p>
            </div>
        </section>

        {{-- Ringkasan: compact --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 p-4 md:p-5 border-b border-indigo-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <p class="text-sm font-semibold text-indigo-900">Ringkasan Sesi</p>
                        <p class="text-xs text-indigo-700/80">Data otomatis dari transaksi pada sesi ini</p>
                    </div>
                    <div class="text-xs text-indigo-700/80">
                        <span class="font-medium text-indigo-900">Outlet:</span>
                        {{ auth()->user()->outlet->name ?? '-' }}
                    </div>
                </div>
            </div>

            <div class="p-4 md:p-5">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="stat-card bg-white rounded-lg border border-gray-200 p-3">
                        <p class="text-[11px] text-gray-500 font-medium">Transaksi</p>
                        <p class="text-xl font-bold text-gray-900">{{ number_format($register->total_transactions, 0, ',', '.') }}</p>
                    </div>

                    <div class="stat-card bg-white rounded-lg border border-gray-200 p-3">
                        <p class="text-[11px] text-gray-500 font-medium">Penjualan</p>
                        <p class="text-xl font-bold text-green-600">Rp {{ number_format($register->total_sales, 0, ',', '.') }}</p>
                    </div>

                    <div class="stat-card bg-white rounded-lg border border-gray-200 p-3">
                        <p class="text-[11px] text-gray-500 font-medium">Diskon</p>
                        <p class="text-xl font-bold text-orange-600">Rp {{ number_format($totalDiscount ?? 0, 0, ',', '.') }}</p>
                    </div>

                    <div class="stat-card bg-white rounded-lg border border-gray-200 p-3">
                        <p class="text-[11px] text-gray-500 font-medium">Modal Awal</p>
                        <p class="text-xl font-bold text-purple-600">Rp {{ number_format($register->opening_amount, 0, ',', '.') }}</p>
                    </div>
                </div>

                {{-- Metode Pembayaran: minimal icon --}}
                <div class="mt-4 md:mt-5">
                    <p class="text-sm font-semibold text-gray-900 mb-2">Metode Pembayaran</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="payment-card bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-3 border border-green-200">
                            <p class="text-[11px] text-green-700 font-semibold">Tunai</p>
                            <p class="text-lg font-bold text-green-900">Rp {{ number_format($register->total_cash, 0, ',', '.') }}</p>
                        </div>

                        <div class="payment-card bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-3 border border-purple-200">
                            <p class="text-[11px] text-purple-700 font-semibold">QRIS</p>
                            <p class="text-lg font-bold text-purple-900">Rp {{ number_format($register->total_qris, 0, ',', '.') }}</p>
                        </div>

                        <div class="payment-card bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-3 border border-blue-200">
                            <p class="text-[11px] text-blue-700 font-semibold">Transfer</p>
                            <p class="text-lg font-bold text-blue-900">Rp {{ number_format($register->total_transfer, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Perhitungan Kasir: lebih ringkas + mudah dimengerti --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 md:p-6">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2 mb-4">
                <div>
                    <h3 class="text-base md:text-lg font-bold text-gray-900">Cocokkan Uang Kas</h3>
                    <p class="text-sm text-gray-500">Masukkan uang fisik di laci kas. Sistem akan hitung selisihnya.</p>
                </div>
            </div>

            <form id="closeRegisterForm" class="space-y-4">
                @csrf

                {{-- Expected amount --}}
                <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 border border-indigo-200 rounded-lg p-4">
                    <p class="text-xs font-semibold text-indigo-900">Uang tunai yang seharusnya ada</p>
                    <p class="text-2xl font-bold text-indigo-600 mt-1">
                        Rp {{ number_format($register->expected_amount, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-700 mt-2">
                        Rumus: Modal awal (Rp {{ number_format($register->opening_amount, 0, ',', '.') }}) + Tunai masuk (Rp {{ number_format($register->total_cash, 0, ',', '.') }}).
                    </p>
                </div>

                {{-- Closing input --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Uang tunai di kas (hasil hitung fisik) <span class="text-red-500">*</span>
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
                    <p class="text-xs text-gray-500 mt-1">Contoh: jumlah uang kertas + koin di laci kas.</p>
                </div>

                {{-- Difference --}}
                <div id="differenceDisplay" class="hidden">
                    <div class="rounded-lg p-4 border-2 transition-all" id="differenceCard">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold mb-1" id="differenceLabel">Selisih</p>
                                <p class="text-2xl font-bold truncate" id="differenceAmount">Rp 0</p>
                            </div>
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" id="differenceIcon">
                                <i class="text-white" id="differenceIconElement"></i>
                            </div>
                        </div>
                        <p class="text-xs mt-2 font-medium" id="differenceNote"></p>
                    </div>
                </div>

                {{-- Checkbox daily report --}}
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <input
                            type="checkbox"
                            id="generateDailyReport"
                            name="generate_daily_report"
                            class="w-5 h-5 text-indigo-600 rounded focus:ring-2 focus:ring-indigo-500 mt-0.5"
                        >
                        <div class="flex-1">
                            <label for="generateDailyReport" class="block text-sm font-semibold text-gray-900 cursor-pointer">
                                Ini penutupan terakhir hari ini
                            </label>
                            <p class="text-xs text-gray-600 mt-1">
                                Jika dicentang, sistem akan buat laporan harian otomatis dan menandai transaksi hari ini sudah masuk laporan.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Catatan (opsional)</label>
                    <textarea
                        name="notes"
                        id="notes"
                        rows="3"
                        class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                        placeholder="Misal: ada selisih karena uang receh, atau alasan lainnya..."
                    ></textarea>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('pos.index') }}"
                       class="flex-1 px-4 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-all text-center">
                        Kembali
                    </a>
                    <button type="submit" id="submitBtn"
                            class="flex-1 px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg font-semibold hover:from-red-600 hover:to-red-700 transition-all shadow-lg hover:shadow-xl">
                        Tutup Sesi
                    </button>
                </div>
            </form>
        </div>

        {{-- Tabel transaksi: semua penjualan termasuk refunded --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
            <div class="flex items-center justify-between gap-3 mb-3">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Daftar Transaksi</h3>
                    <p class="text-sm text-gray-500">Menampilkan transaksi pada sesi ini, termasuk yang refund.</p>
                </div>
                <div class="text-sm text-gray-600 font-semibold">
                    Total: {{ $sales->count() }}
                </div>
            </div>

            @if($sales->count() > 0)
                <div class="overflow-x-auto -mx-5 px-5">
                    <table class="w-full min-w-[820px]">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase">Invoice</th>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase">Waktu</th>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase">Metode</th>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase">Status</th>
                                <th class="px-3 py-2 text-right text-xs font-bold text-gray-700 uppercase">Diskon</th>
                                <th class="px-3 py-2 text-right text-xs font-bold text-gray-700 uppercase">Total</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            @foreach($sales as $sale)
                                @php $isRefunded = $sale->status === 'refunded'; @endphp
                                <tr class="hover:bg-gray-50 {{ $isRefunded ? 'opacity-80' : '' }}">
                                    <td class="px-3 py-2 text-sm font-semibold {{ $isRefunded ? 'text-gray-500 line-through' : 'text-gray-900' }}">
                                        {{ $sale->invoice_number }}
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-600">
                                        {{ $sale->created_at->format('d/m H:i') }}
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="px-2 py-1 text-xs font-bold rounded-full
                                            @if($sale->payment_method === 'cash') bg-green-100 text-green-700
                                            @elseif($sale->payment_method === 'qris') bg-purple-100 text-purple-700
                                            @elseif($sale->payment_method === 'transfer') bg-blue-100 text-blue-700
                                            @else bg-gray-100 text-gray-700
                                            @endif">
                                            {{ strtoupper($sale->payment_method) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="px-2 py-1 text-xs font-bold rounded-full
                                            {{ $isRefunded ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                            {{ $isRefunded ? 'REFUNDED' : 'COMPLETED' }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-sm font-bold text-right text-orange-600 whitespace-nowrap">
                                        Rp {{ number_format($sale->discount_amount ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-2 text-sm font-bold text-right text-gray-900 whitespace-nowrap">
                                        Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-10 text-center text-gray-500">
                    <p class="text-sm">Belum ada transaksi pada sesi ini.</p>
                </div>
            @endif
        </div>

    </div>
</main>

{{-- Modal Success --}}
<div id="successModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6">
        <div class="text-center">
            <h3 class="text-xl font-bold text-gray-900">Sesi berhasil ditutup</h3>
            <p class="text-gray-600 mt-2 text-sm">Terima kasih, data sudah tersimpan.</p>
        </div>

        <button onclick="window.location.href='{{ route('dashboard') }}'"
                class="mt-6 w-full px-4 py-3 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-lg font-semibold hover:from-indigo-600 hover:to-indigo-700 transition-all shadow-lg">
            Kembali ke Dashboard
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>
const expectedAmount = {{ $register->expected_amount }};

// ====== JS kamu yang lama boleh dipakai, hanya aku sederhanakan text confirm ======

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
    }, 3500);
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
            card.className = 'rounded-lg p-4 border-2 border-green-300 bg-gradient-to-br from-green-50 to-green-100';
            icon.className = 'w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center flex-shrink-0';
            iconElement.className = 'fas fa-arrow-up text-white';
            amountEl.className = 'text-2xl font-bold text-green-600 truncate';
            labelEl.className = 'text-xs font-semibold text-green-900';
            labelEl.textContent = 'Selisih lebih';
            noteEl.className = 'text-xs mt-2 font-medium text-green-700';
            noteEl.textContent = 'Uang kas lebih banyak dari hitungan sistem.';
        } else if (difference < 0) {
            card.className = 'rounded-lg p-4 border-2 border-red-300 bg-gradient-to-br from-red-50 to-red-100';
            icon.className = 'w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center flex-shrink-0';
            iconElement.className = 'fas fa-arrow-down text-white';
            amountEl.className = 'text-2xl font-bold text-red-600 truncate';
            labelEl.className = 'text-xs font-semibold text-red-900';
            labelEl.textContent = 'Selisih kurang';
            noteEl.className = 'text-xs mt-2 font-medium text-red-700';
            noteEl.textContent = 'Uang kas lebih sedikit dari hitungan sistem.';
        } else {
            card.className = 'rounded-lg p-4 border-2 border-blue-300 bg-gradient-to-br from-blue-50 to-blue-100';
            icon.className = 'w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0';
            iconElement.className = 'fas fa-check text-white';
            amountEl.className = 'text-2xl font-bold text-blue-600 truncate';
            labelEl.className = 'text-xs font-semibold text-blue-900';
            labelEl.textContent = 'Pas';
            noteEl.className = 'text-xs mt-2 font-medium text-blue-700';
            noteEl.textContent = 'Uang kas sesuai dengan hitungan sistem.';
        }
    } else {
        display.classList.add('hidden');
    }
}

document.getElementById('closeRegisterForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const closingAmount = parseFloat(document.getElementById('closingAmount').value);
    const notes = document.getElementById('notes').value;
    const generateDailyReport = document.getElementById('generateDailyReport').checked;
    const submitBtn = document.getElementById('submitBtn');

    if (!closingAmount || closingAmount < 0) {
        showToast('error', 'Masukkan jumlah uang kas yang valid.');
        return;
    }

    let confirmMessage = 'Tutup sesi sekarang?';
    if (generateDailyReport) {
        confirmMessage = 'Tutup sesi dan buat laporan harian sekarang?';
    }

    if (!confirm(confirmMessage)) return;

    submitBtn.disabled = true;
    submitBtn.textContent = 'Memproses...';

    fetch('{{ route("cash-register.process-close") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            closing_amount: closingAmount,
            notes: notes,
            generate_daily_report: generateDailyReport
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            
            // Tampilkan modal success
            setTimeout(() => {
                document.getElementById('successModal').classList.remove('hidden');
            }, 500);
        } else {
            showToast('error', data.message || 'Gagal menutup sesi.');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Tutup Sesi';
        }
    })
    .catch(() => {
        showToast('error', 'Terjadi kesalahan.');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Tutup Sesi';
    });
});

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('closingAmount')?.focus();
});
</script>
@endpush

@extends('layouts.app')

@section('title', 'Tutup Toko - CuanFlow')

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('pos.index') }}" class="text-gray-500 hover:text-cuan-green transition-colors font-medium">Point of Sale</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-bold tracking-tight">Tutup Sesi Kasir</span>
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

        {{-- Header Halaman --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900 flex items-center gap-2">
                    Tutup Sesi & Rekap
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Selesaikan operasional kasir dan hitung pendapatan hari ini.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="px-4 py-2 bg-white border border-gray-200 rounded-xl shadow-sm text-right">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Kasir Aktif</p>
                    <p class="text-sm font-bold text-gray-900 uppercase tracking-tighter">{{ auth()->user()->name }}</p>
                </div>
                <div class="px-4 py-2 bg-white border border-gray-200 rounded-xl shadow-sm text-right">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Waktu Buka</p>
                    <p class="text-sm font-bold text-cuan-green">{{ $register->opened_at->format('H:i') }} <span class="text-[10px] text-gray-400">({{ $register->opened_at->format('d M') }})</span></p>
                </div>
            </div>
        </section>

        {{-- Ringkasan: compact --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="bg-gray-50 p-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-sm font-black text-gray-900 uppercase tracking-widest">Ringkasan Sesi</h2>
                    <p class="text-[10px] text-gray-500 font-medium">DATA OTOMATIS BERDASARKAN TRANSAKSI AKTIF</p>
                </div>
                <div class="text-[10px] font-black text-cuan-green bg-cuan-green/10 px-3 py-1 rounded-full uppercase tracking-widest">
                    {{ auth()->user()->outlet->name ?? '-' }}
                </div>
            </div>

            <div class="p-4 md:p-5">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="stat-card bg-white border border-gray-100 rounded-xl p-4 transition-all hover:border-cuan-green/30">
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Transaksi</p>
                        <p class="mt-2 text-2xl font-black text-gray-900">{{ number_format($register->total_transactions, 0, ',', '.') }}</p>
                    </div>

                    <div class="stat-card bg-white border border-gray-100 rounded-xl p-4 transition-all hover:border-cuan-green/30">
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Penjualan</p>
                        <p class="mt-2 text-2xl font-black text-cuan-green">Rp {{ number_format($register->total_sales, 0, ',', '.') }}</p>
                    </div>

                    <div class="stat-card bg-white border border-gray-100 rounded-xl p-4 transition-all hover:border-cuan-green/30">
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Potongan Diskon</p>
                        <p class="mt-2 text-2xl font-black text-orange-500">Rp {{ number_format($totalDiscount ?? 0, 0, ',', '.') }}</p>
                    </div>

                    <div class="stat-card bg-white border border-gray-100 rounded-xl p-4 transition-all hover:border-cuan-green/30">
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Modal Awal Kas</p>
                        <p class="mt-2 text-2xl font-black text-gray-700">Rp {{ number_format($register->opening_amount, 0, ',', '.') }}</p>
                    </div>
                </div>

                {{-- Metode Pembayaran --}}
                <div class="mt-6 border-t border-gray-50 pt-6">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4">Detail Per Metode Pembayaran</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 flex flex-col">
                            <span class="text-[10px] font-black text-cuan-green uppercase tracking-tighter">Pemasukan Tunai</span>
                            <span class="text-xl font-black text-gray-900 mt-1 uppercase">Rp {{ number_format($register->total_cash, 0, ',', '.') }}</span>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 flex flex-col">
                            <span class="text-[10px] font-black text-purple-600 uppercase tracking-tighter">Pembayaran QRIS</span>
                            <span class="text-xl font-black text-gray-900 mt-1 uppercase">Rp {{ number_format($register->total_qris, 0, ',', '.') }}</span>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 flex flex-col">
                            <span class="text-[10px] font-black text-blue-600 uppercase tracking-tighter">Transfer Bank</span>
                            <span class="text-xl font-black text-gray-900 mt-1 uppercase">Rp {{ number_format($register->total_transfer, 0, ',', '.') }}</span>
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
                <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 text-center">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Ekspektasi Uang Tunai di Kas</p>
                    <p class="text-4xl font-black text-cuan-green tracking-tighter uppercase">
                        Rp {{ number_format($register->expected_amount, 0, ',', '.') }}
                    </p>
                    <div class="mt-3 inline-flex items-center gap-2 text-[10px] font-bold text-gray-500 bg-white border border-gray-100 px-3 py-1 rounded-full uppercase tracking-tighter">
                        Rumus: Modal Awal + Tunai Masuk
                    </div>
                </div>

                {{-- Closing input --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                        Hasil Hitung Fisik (Kas Aktual) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative group">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-black text-xl">Rp</span>
                        <input
                            type="number"
                            id="closingAmount"
                            name="closing_amount"
                            class="w-full pl-14 pr-4 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green focus:bg-white text-3xl font-black text-gray-900 transition-all uppercase placeholder-gray-200"
                            placeholder="0"
                            required
                            min="0"
                            step="0.01"
                            onkeyup="calculateDifference()"
                            onchange="calculateDifference()"
                        >
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Contoh: jumlah uang kertas + koin di laci kas.</p>
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
                <div class="flex flex-col sm:flex-row gap-3 pt-6">
                    <a href="{{ route('pos.index') }}"
                       class="flex-1 px-4 py-4 border border-gray-200 text-gray-500 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-50 transition-all text-center">
                        Batalkan
                    </a>
                    <button type="submit" id="submitBtn"
                            class="flex-[2] px-4 py-4 bg-cuan-green text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-cuan-dark transition-all shadow-xl shadow-emerald-100 active:scale-95">
                        Konfirmasi & Tutup Sesi
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
                                <th class="px-3 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">No. Invoice</th>
                                <th class="px-3 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Waktu</th>
                                <th class="px-3 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Metode</th>
                                <th class="px-3 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                                <th class="px-3 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Diskon</th>
                                <th class="px-3 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Total</th>
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
                                    <td class="px-3 py-3">
                                        <span class="px-3 py-1 text-[10px] font-black rounded-full uppercase tracking-tighter
                                            {{ $isRefunded ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-cuan-green/10 text-cuan-green border border-cuan-green/20' }}">
                                            {{ $isRefunded ? 'Refunded' : 'Completed' }}
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
            <h3 class="text-xl font-black text-gray-900 uppercase tracking-tighter">Sesi Berhasil Ditutup</h3>
            <p class="text-gray-500 mt-2 text-sm">Terima kasih, data operasional hari ini telah tersimpan dengan aman.</p>
        </div>

        <button onclick="window.location.href='{{ route('dashboard') }}'"
                class="mt-8 w-full px-4 py-4 bg-cuan-green text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-cuan-dark transition-all shadow-xl shadow-emerald-100">
            Kembali ke Dashboard
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>
const expectedAmount = {{ $register->expected_amount }};

// ====== JS kamu yang lama boleh dipakai, hanya aku sederhanakan text confirm ======

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

function showToast(type, message) {
    Toast.fire({
        icon: type,
        title: message
    });
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

    const closingAmountInput = document.getElementById('closingAmount');
    const closingAmount = parseFloat(closingAmountInput.value);
    const notes = document.getElementById('notes').value;
    const generateDailyReport = document.getElementById('generateDailyReport').checked;
    const submitBtn = document.getElementById('submitBtn');

    if (isNaN(closingAmount) || closingAmount < 0) {
        showToast('error', 'Masukkan jumlah uang kas yang valid.');
        return;
    }

    let confirmTitle = 'Tutup sesi sekarang?';
    let confirmText = 'Pastikan semua uang kas telah dihitung dengan benar.';
    
    if (generateDailyReport) {
        confirmTitle = 'Tutup Sesi & Buat Laporan?';
        confirmText = 'Sistem akan menutup sesi kasir dan mengunci laporan harian untuk hari ini.';
    }

    Swal.fire({
        title: confirmTitle,
        text: confirmText,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Tutup Sesi',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            container: 'rounded-2xl',
            popup: 'rounded-2xl',
            confirmButton: 'rounded-xl font-bold px-6 py-3',
            cancelButton: 'rounded-xl font-bold px-6 py-3'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i>Memproses...';

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
                    submitBtn.textContent = 'Konfirmasi & Tutup Sesi';
                }
            })
            .catch(() => {
                showToast('error', 'Terjadi kesalahan sistem.');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Konfirmasi & Tutup Sesi';
            });
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('closingAmount')?.focus();
});
</script>
@endpush

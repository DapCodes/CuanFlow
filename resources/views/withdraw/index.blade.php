@extends('layouts.app')

@section('title', 'Riwayat Penarikan Saldo')


@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Riwayat Penarikan</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- ALERT NOTIFIKASI --}}
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-check-circle mt-0.5 text-green-500"></i>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-exclamation-circle mt-0.5 text-red-500"></i>
                <p class="text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        {{-- HEADER HALAMAN --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-teal-50 text-teal-600 border border-teal-100">
                        <i class="fas fa-history text-sm"></i>
                    </span>
                    <span>Riwayat Penarikan</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Pantau status pengajuan penarikan keuntungan Anda dari CuanFlow.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                <a href="{{ route('withdraw.confirm-password') }}" 
                   class="inline-flex items-center gap-2 rounded-lg bg-teal-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:ring-offset-1 transition-all shadow-sm">
                    <i class="fas fa-plus-circle text-sm"></i>
                    <span>Ajukan Penarikan Baru</span>
                </a>
            </div>
        </section>

        {{-- RINGKASAN STATISTIK --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Total Pengajuan</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($stats['total_count']) }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100">
                        <i class="fas fa-file-invoice-dollar text-gray-400 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Sedang Proses</p>
                        <p class="mt-1 text-2xl font-bold text-yellow-600">{{ number_format($stats['pending_count']) }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-yellow-50 flex items-center justify-center border border-yellow-100">
                        <i class="fas fa-spinner fa-spin text-yellow-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Dana Diterima</p>
                        <p class="mt-1 text-2xl font-bold text-emerald-600">Rp {{ number_format($stats['paid_total'], 0, ',', '.') }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100">
                        <i class="fas fa-check-double text-emerald-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Request</p>
                        <p class="mt-1 text-xl font-bold text-gray-900">Rp {{ number_format($stats['total_request'], 0, ',', '.') }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center border border-blue-100">
                        <i class="fas fa-chart-pie text-blue-500 text-lg"></i>
                    </div>
                </div>
            </div>
        </section>

        {{-- KONTEN UTAMA: TABEL --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-[11px] text-gray-500 uppercase font-bold border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4">ID / Tanggal</th>
                            <th class="px-6 py-4">Tujuan Penarikan</th>
                            <th class="px-6 py-4">Nominal Diterima</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($withdrawals as $w)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-900 leading-none">#{{ $w->id }}</p>
                                <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-tighter">{{ $w->created_at->isoFormat('D MMM Y, HH:mm') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="p-1.5 bg-gray-50 border border-gray-100 rounded text-gray-500">
                                        <i class="fas fa-university text-[10px]"></i>
                                    </div>
                                    <div class="text-sm">
                                        <p class="font-bold text-gray-800 leading-tight">{{ $w->payment_method }}</p>
                                        <p class="text-[10px] text-gray-500 font-mono tracking-tight">{{ $w->account_number }}</p>
                                    </div>
                                </div>
                                <p class="text-[9px] text-gray-400 truncate mt-1 italic">{{ $w->account_name }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-teal-600">Rp {{ number_format($w->net_amount, 0, ',', '.') }}</p>
                                <p class="text-[9px] text-gray-400 mt-0.5">Potong pajak: Rp {{ number_format($w->tax_amount, 0, ',', '.') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                {!! $w->status_badge !!}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button onclick="showDetails({{ $w->id }}, '{{ $w->status }}', '{{ addslashes($w->admin_note) }}', '{{ $w->proof_image_url }}')" 
                                        class="inline-flex items-center justify-center w-8 h-8 text-gray-400 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition-all"
                                        title="Lihat Detail">
                                    <i class="fas fa-info-circle text-base"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-dashed border-gray-200">
                                        <i class="fas fa-history text-2xl text-gray-300"></i>
                                    </div>
                                    <h3 class="text-gray-500 font-medium">Belum ada riwayat penarikan</h3>
                                    <p class="text-xs text-gray-400 mt-1 max-w-xs mx-auto">Saldo hasil penjualan Anda akan tercatat di sini setelah Anda melakukan pengajuan penarikan pertama.</p>
                                    <a href="{{ route('withdraw.confirm-password') }}" class="mt-6 px-5 py-2.5 bg-gray-900 text-white text-xs font-bold rounded-xl hover:bg-teal-600 transition-all shadow-lg hover:shadow-teal-500/20">
                                        Ajukan Penarikan Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($withdrawals->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $withdrawals->links() }}
            </div>
            @endif
        </section>
    </div>
</main>

{{-- MODAL DETAIL --}}
<div id="detailModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl transform scale-95 transition-all duration-300 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="font-black text-gray-900 tracking-tight flex items-center gap-2">
                <i class="fas fa-info-circle text-teal-500"></i>
                Detail Penarikan
            </h3>
            <button onclick="hideDetails()" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-white rounded-xl transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6">
            <div id="modalContent" class="space-y-6">
                <div id="adminNoteSection" class="hidden p-4 bg-amber-50 rounded-2xl border border-amber-100 shadow-inner">
                    <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-1">Catatan Admin:</p>
                    <p id="adminNoteText" class="text-sm text-amber-900 leading-relaxed font-medium"></p>
                </div>
                
                <div id="proofSection" class="hidden space-y-3">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Bukti Pengiriman:</p>
                    <div class="relative group cursor-zoom-in" onclick="window.open(document.getElementById('proofImage').src)">
                        <img id="proofImage" src="" alt="Bukti Transfer" class="w-full h-auto rounded-2xl border border-gray-100 shadow-sm transition-transform group-hover:scale-[1.02]">
                        <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-2xl">
                            <span class="px-3 py-1.5 bg-white/90 backdrop-blur rounded-lg text-[10px] font-bold text-gray-900 shadow-lg">Klik untuk Memperbesar</span>
                        </div>
                    </div>
                </div>
                
                <div id="noDetailText" class="text-center py-10">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-dashed border-gray-200">
                        <i class="fas fa-hourglass-half text-xl text-gray-300"></i>
                    </div>
                    <p class="text-sm text-gray-500 font-medium italic">Belum ada catatan atau bukti dari admin.</p>
                    <p class="text-[10px] text-gray-400 mt-1">Silakan tunggu proses peninjauan selesai.</p>
                </div>
            </div>
        </div>
        <div class="px-6 py-5 bg-gray-50 flex justify-end border-t border-gray-100">
            <button onclick="hideDetails()" class="px-8 py-3 bg-white border border-gray-200 text-gray-900 text-xs font-black rounded-xl hover:bg-gray-100 transition-all shadow-sm">Tutup</button>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('detailModal');
    const modalContent = modal.querySelector('.transform');
    const adminNoteSection = document.getElementById('adminNoteSection');
    const adminNoteText = document.getElementById('adminNoteText');
    const proofSection = document.getElementById('proofSection');
    const proofImage = document.getElementById('proofImage');
    const noDetailText = document.getElementById('noDetailText');

    function showDetails(id, status, adminNote, proofUrl) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent scroll
        setTimeout(() => {
            modal.classList.add('opacity-100');
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }, 10);

        let hasData = false;

        if (adminNote && adminNote !== 'null' && adminNote !== '') {
            adminNoteSection.classList.remove('hidden');
            adminNoteText.textContent = adminNote;
            hasData = true;
        } else {
            adminNoteSection.classList.add('hidden');
        }

        if (proofUrl && proofUrl !== '' && proofUrl !== 'null') {
            proofSection.classList.remove('hidden');
            proofImage.src = proofUrl;
            hasData = true;
        } else {
            proofSection.classList.add('hidden');
        }

        if (hasData) {
            noDetailText.classList.add('hidden');
        } else {
            noDetailText.classList.remove('hidden');
        }
    }

    function hideDetails() {
        modal.classList.remove('opacity-100');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');
        document.body.style.overflow = ''; // Restore scroll
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Close on backdrop click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) hideDetails();
    });
</script>
@endsection

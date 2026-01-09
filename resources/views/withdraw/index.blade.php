@extends('layouts.app')

@section('title', 'Riwayat Penarikan Saldo')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Riwayat Penarikan</h1>
                <p class="text-gray-500 mt-1">Pantau status pengajuan penarikan dana Anda</p>
            </div>
            <a href="{{ route('withdraw.confirm-password') }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl shadow-lg shadow-teal-500/20 transition-all active:scale-95 gap-2">
                <i class="fas fa-plus"></i>
                Ajukan Penarikan Baru
            </a>
        </div>

        <!-- Withdrawal History Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Tanggal & ID</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Tujuan & Rekening</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Nominal</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($withdrawals as $w)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-900 leading-none">#{{ $w->id }}</p>
                                <p class="text-[11px] text-gray-400 mt-1">{{ $w->created_at->isoFormat('D MMM Y, HH:mm') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-800 leading-tight">{{ $w->payment_method }}</p>
                                <p class="text-xs text-gray-500">{{ $w->account_number }}</p>
                                <p class="text-[10px] text-gray-400 truncate max-w-[150px]">{{ $w->account_name }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-teal-600">Rp {{ number_format($w->net_amount, 0, ',', '.') }}</p>
                                <p class="text-[10px] text-gray-400">Total: Rp {{ number_format($w->amount, 0, ',', '.') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                {!! $w->status_badge !!}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button onclick="showDetails({{ $w->id }}, '{{ $w->status }}', '{{ $w->admin_note }}', '{{ $w->proof_image_url }}')" 
                                        class="p-2 text-gray-400 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition-all"
                                        title="Lihat Detail">
                                    <i class="fas fa-info-circle text-lg"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="h-20 w-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-history text-3xl text-gray-300"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-600">Belum ada penarikan</h3>
                                    <p class="text-gray-400 text-sm mt-1">Saldo hasil penjualan Anda akan muncul di sini.</p>
                                    <a href="{{ route('withdraw.confirm-password') }}" class="mt-6 text-teal-600 font-bold hover:underline">
                                        Buat pengajuan pertama →
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($withdrawals->hasPages())
            <div class="px-6 py-4 border-t border-gray-50">
                {{ $withdrawals->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Simple Modal for Details -->
<div id="detailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl transform scale-95 transition-transform duration-300 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-900">Detail Penarikan</h3>
            <button onclick="hideDetails()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6">
            <div id="modalContent" class="space-y-4">
                <div id="adminNoteSection" class="hidden p-4 bg-amber-50 rounded-xl border border-amber-100">
                    <p class="text-xs font-bold text-amber-700 uppercase mb-1">Catatan Admin:</p>
                    <p id="adminNoteText" class="text-sm text-amber-800 italic"></p>
                </div>
                
                <div id="proofSection" class="hidden">
                    <p class="text-xs font-bold text-gray-500 uppercase mb-2">Bukti Pengiriman:</p>
                    <img id="proofImage" src="" alt="Bukti Transfer" class="w-full h-auto rounded-xl border border-gray-100 shadow-sm cursor-zoom-in" onclick="window.open(this.src)">
                    <p class="text-[10px] text-center text-gray-400 mt-2 italic">Klik gambar untuk memperbesar</p>
                </div>
                
                <div id="noDetailText" class="text-center py-4 text-gray-500 text-sm italic">
                    Belum ada catatan atau bukti dari admin. Silakan tunggu proses peninjauan.
                </div>
            </div>
        </div>
        <div class="px-6 py-4 bg-gray-50 flex justify-end">
            <button onclick="hideDetails()" class="px-5 py-2 bg-gray-200 text-gray-700 font-bold rounded-lg hover:bg-gray-300 transition-colors">Tutup</button>
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
        setTimeout(() => {
            modal.classList.add('opacity-100');
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }, 10);

        let hasData = false;

        if (adminNote && adminNote !== 'null') {
            adminNoteSection.classList.remove('hidden');
            adminNoteText.textContent = adminNote;
            hasData = true;
        } else {
            adminNoteSection.classList.add('hidden');
        }

        if (proofUrl && proofUrl !== '') {
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

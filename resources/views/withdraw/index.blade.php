@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Riwayat Penarikan Saldo')


@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Riwayat Penarikan</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Notifications will be handled by SweetAlert2 --}}

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900 leading-tight">
                    Riwayat Penarikan
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Pantau status pengajuan penarikan keuntungan Anda dari CuanFlow.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                <a href="{{ route('withdraw.confirm-password') }}" 
                   class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-cuan-green text-white font-black text-[10px] uppercase tracking-widest hover:bg-cuan-dark transition-all active:scale-95 shadow-lg shadow-cuan-green/20">
                    <span>Ajukan Penarikan Baru</span>
                </a>
            </div>
        </section>

        {{-- RINGKASAN STATISTIK --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
            <x-card-container class="px-6 py-6">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Pengajuan</p>
                <p class="mt-2 text-2xl font-black text-gray-900">{{ number_format($stats['total_count']) }}</p>
            </x-card-container>

            <x-card-container class="px-6 py-6">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Sedang Proses</p>
                <p class="mt-2 text-2xl font-black text-yellow-600">{{ number_format($stats['pending_count']) }}</p>
            </x-card-container>

            <x-card-container class="px-6 py-6">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Dana Diterima</p>
                <p class="mt-2 text-xl font-black text-cuan-green">Rp {{ number_format($stats['paid_total'], 0, ',', '.') }}</p>
            </x-card-container>

            <x-card-container class="px-6 py-6">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Request</p>
                <p class="mt-2 text-xl font-black text-blue-600">Rp {{ number_format($stats['total_request'], 0, ',', '.') }}</p>
            </x-card-container>
        </section>

        {{-- CONTENT TABS --}}
        <div x-data="{ activeTab: 'history' }">
            @if(isset($confirmations) && $confirmations->count() > 0)
            <div class="flex gap-4 mb-6 border-b border-gray-200">
                <button @click="activeTab = 'history'" 
                        :class="{ 'border-b-2 border-cuan-green text-cuan-dark font-black': activeTab === 'history', 'text-gray-400 hover:text-gray-600 font-bold': activeTab !== 'history' }"
                        class="pb-3 px-2 text-[10px] uppercase tracking-widest transition-colors">
                    Riwayat Saya
                </button>
                <button @click="activeTab = 'approvals'" 
                        :class="{ 'border-b-2 border-orange-500 text-orange-600 font-black': activeTab === 'approvals', 'text-gray-400 hover:text-gray-600 font-bold': activeTab !== 'approvals' }"
                        class="pb-3 px-2 text-[10px] uppercase tracking-widest transition-colors flex items-center gap-2">
                    Perlu Persetujuan
                    <span class="bg-red-500 text-white text-[9px] px-1.5 py-0.5 rounded-full shadow-sm">{{ $confirmations->count() }}</span>
                </button>
            </div>
            @endif

            {{-- TAB: RIWAYAT --}}
            <x-card-container x-show="activeTab === 'history'" style="display: block;">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">ID / Tanggal</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tujuan Penarikan</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nominal Diterima</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($withdrawals as $w)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-8 py-4">
                                    <p class="text-xs font-black text-gray-900 leading-none">#{{ $w->id }}</p>
                                    <p class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-widest">{{ $w->created_at->isoFormat('D MMM Y, HH:mm') }}</p>
                                </td>
                                <td class="px-8 py-4">
                                    <div>
                                        <p class="text-xs font-black text-gray-900 leading-tight">{{ $w->payment_method }}</p>
                                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-1">{{ $w->account_number }}</p>
                                        <p class="text-[9px] font-bold text-gray-400 truncate mt-1 italic">{{ $w->account_name }}</p>
                                    </div>
                                </td>
                                <td class="px-8 py-4">
                                    <p class="text-xs font-black text-cuan-green">Rp {{ number_format($w->net_amount, 0, ',', '.') }}</p>
                                    <p class="text-[10px] font-bold text-gray-400 mt-0.5 uppercase tracking-widest">Pajak: Rp {{ number_format($w->tax_amount, 0, ',', '.') }}</p>
                                </td>
                                <td class="px-8 py-4">
                                    {!! $w->status_badge !!}
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <button onclick="showDetails({{ $w->id }}, '{{ $w->status }}', '{{ addslashes($w->admin_note) }}', '{{ $w->proof_image_url }}')" 
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-gray-50 text-gray-500 hover:bg-cuan-green hover:text-white transition-all transform group-hover:scale-105"
                                            title="Lihat Detail">
                                        <i class="fas fa-info-circle text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-gray-50 rounded-[2rem] flex items-center justify-center border border-dashed border-gray-200 mb-6">
                                            <i class="fas fa-history text-3xl text-gray-300"></i>
                                        </div>
                                        <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest">Belum ada riwayat penarikan</h3>
                                        <p class="text-[10px] font-bold text-gray-400 mt-2 max-w-sm mx-auto leading-relaxed">Saldo hasil penjualan Anda akan tercatat di sini setelah Anda melakukan pengajuan penarikan pertama.</p>
                                        <a href="{{ route('withdraw.confirm-password') }}" class="mt-8 inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gray-900 text-white font-black text-[10px] uppercase tracking-widest hover:bg-black transition-all active:scale-95 shadow-lg shadow-gray-900/10">
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
                <div class="px-8 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $withdrawals->links() }}
                </div>
                @endif
            </x-card-container>

            {{-- TAB: PERSETUJUAN --}}
            @if(isset($confirmations) && $confirmations->count() > 0)
            <x-card-container x-show="activeTab === 'approvals'" style="display: none;">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-orange-50/50 border-b border-orange-100">
                            <tr>
                                <th class="px-8 py-4 text-[10px] font-black text-orange-800 uppercase tracking-widest">Tgl Pengajuan</th>
                                <th class="px-8 py-4 text-[10px] font-black text-orange-800 uppercase tracking-widest">Pengaju</th>
                                <th class="px-8 py-4 text-[10px] font-black text-orange-800 uppercase tracking-widest">Nominal</th>
                                <th class="px-8 py-4 text-[10px] font-black text-orange-800 uppercase tracking-widest text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                            @foreach($confirmations as $c)
                            <tr class="hover:bg-orange-50/30 transition-colors group">
                                <td class="px-8 py-4">
                                    <p class="text-xs font-black text-gray-900">{{ $c->created_at->format('d M Y') }}</p>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">{{ $c->created_at->format('H:i') }}</p>
                                </td>
                                <td class="px-8 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $c->user->avatar_url }}" class="h-10 w-10 rounded-xl border border-gray-100 shadow-sm">
                                        <div>
                                            <p class="text-xs font-black text-gray-900">{{ $c->user->name }}</p>
                                            <span class="inline-flex mt-1 uppercase tracking-widest px-2 py-0.5 rounded-md text-[8px] font-black bg-gray-100 text-gray-600">{{ $c->user->role }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-4">
                                    <p class="text-xs font-black text-orange-600">Rp {{ number_format($c->amount, 0, ',', '.') }}</p>
                                    <p class="text-[10px] font-bold text-gray-400 mt-0.5 uppercase tracking-widest">Net: Rp {{ number_format($c->net_amount, 0, ',', '.') }}</p>
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <button onclick="showApprovalModal({{ $c->id }}, {{ $c->amount }}, {{ $c->net_amount }}, '{{ $c->user->name }}', '{{ $c->payment_method }}', '{{ $c->account_number }}', '{{ $c->account_name }}')" 
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white transition-all transform group-hover:scale-105" title="Tinjau">
                                        <i class="fas fa-search text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card-container>
            @endif
        </div>
    </div>
</main>


{{-- MODAL DETAIL --}}
<div id="detailModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-[2rem] w-full max-w-md shadow-2xl transform scale-95 transition-all duration-300 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <div>
                <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">Detail Penarikan</h3>
            </div>
            <button onclick="hideDetails()" class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-100 text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all">
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
        <div class="px-8 py-6 bg-gray-50/50 flex justify-end border-t border-gray-100">
            <button onclick="hideDetails()" class="px-8 py-4 bg-white border border-gray-200 text-gray-600 text-sm font-black rounded-2xl hover:bg-gray-50 transition-all active:scale-95 text-center">Tutup</button>
        </div>
    </div>
</div>

{{-- MODAL APPROVAL (OWNER) --}}
<div id="approvalModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-[2rem] w-full max-w-md shadow-2xl transform scale-95 transition-all duration-300 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-orange-50/50">
            <div>
                <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">Tinjau Penarikan</h3>
            </div>
            <button onclick="hideApprovalModal()" class="w-8 h-8 flex items-center justify-center rounded-xl bg-orange-100/50 text-orange-400 hover:bg-red-50 hover:text-red-500 transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-8 space-y-6">
            {{-- Info Ringkas --}}
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 space-y-4">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Pengaju</p>
                        <p class="font-black text-gray-900 text-sm mt-1" id="appUser"></p>
                    </div>
                     <div class="text-right">
                        <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Total Penarikan</p>
                        <p class="font-black text-orange-500 text-lg mt-1" id="appAmount"></p>
                    </div>
                </div>
                <hr class="border-gray-200 dashed">
                <div>
                     <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest mb-2">Tujuan Transfer</p>
                     <p class="text-sm font-black text-gray-800"><span id="appPm"></span> - <span id="appAccNum"></span></p>
                     <p class="text-[10px] font-bold tracking-widest uppercase text-gray-500 mt-1" id="appAccName"></p>
                </div>
            </div>

            {{-- Action Forms --}}
            <div>
                <form id="approveForm" method="POST" class="mb-4 confirm-action">
                    @csrf
                    <button type="submit" data-action="setujui" 
                            class="w-full bg-cuan-green text-white font-black py-4 rounded-2xl hover:bg-cuan-dark transition-all active:scale-95 shadow-lg shadow-cuan-green/20 flex items-center justify-center gap-2 text-sm">
                        <i class="fas fa-check-circle"></i> Setujui Penarikan
                    </button>
                </form>

                <div class="relative flex py-2 items-center">
                    <div class="flex-grow border-t border-gray-200"></div>
                    <span class="flex-shrink-0 mx-4 text-gray-400 font-bold uppercase tracking-widest text-[10px]">ATAU</span>
                    <div class="flex-grow border-t border-gray-200"></div>
                </div>

                <form id="rejectForm" method="POST" class="mt-4 confirm-action">
                    @csrf
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Alasan Penolakan</label>
                    <div class="flex gap-3">
                        <input type="text" name="reason" required placeholder="Contoh: Saldo tidak cukup..." 
                               class="flex-1 rounded-2xl border-gray-200 bg-gray-50 text-sm font-bold placeholder:text-gray-400 focus:ring-4 focus:ring-red-500/10 focus:border-red-500 transition-all px-4">
                        <button type="submit" data-action="tolak"
                                class="bg-gray-100 text-red-500 font-black px-6 rounded-2xl hover:bg-red-50 hover:text-red-600 transition-all active:scale-95 border border-gray-200 shadow-sm text-sm">
                            Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Global SweetAlert2 notification handler
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 3000,
            iconColor: '#658C58',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black text-gray-900',
                htmlContainer: 'text-sm font-medium text-gray-500'
            }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: "{{ session('error') }}",
            confirmButtonColor: '#ef4444',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black text-gray-900',
                htmlContainer: 'text-sm font-medium text-gray-500'
            }
        });
    @endif

    // existing detail modal script
    const modal = document.getElementById('detailModal');
    const modalContent = modal.querySelector('.transform');
    const adminNoteSection = document.getElementById('adminNoteSection');
    const adminNoteText = document.getElementById('adminNoteText');
    const proofSection = document.getElementById('proofSection');
    const proofImage = document.getElementById('proofImage');
    const noDetailText = document.getElementById('noDetailText');

    function showDetails(id, status, adminNote, proofUrl) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; 
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
        document.body.style.overflow = '';
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    modal.addEventListener('click', (e) => {
        if (e.target === modal) hideDetails();
    });

    // Approval Modal Logic
    const appModal = document.getElementById('approvalModal');
    const appContent = appModal.querySelector('.transform');

    function showApprovalModal(id, amount, net, user, pm, accNum, accName) {
        // Set Data
        document.getElementById('appUser').textContent = user;
        document.getElementById('appAmount').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        document.getElementById('appPm').textContent = pm;
        document.getElementById('appAccNum').textContent = accNum;
        document.getElementById('appAccName').textContent = accName;

        // Set Action URLs
        document.getElementById('approveForm').action = `/withdraw/${id}/owner-approve`;
        document.getElementById('rejectForm').action = `/withdraw/${id}/owner-reject`;

        appModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            appModal.classList.add('opacity-100');
            appContent.classList.remove('scale-95');
            appContent.classList.add('scale-100');
        }, 10);
    }

    function hideApprovalModal() {
        appModal.classList.remove('opacity-100');
        appContent.classList.remove('scale-100');
        appContent.classList.add('scale-95');
        document.body.style.overflow = '';
        setTimeout(() => {
            appModal.classList.add('hidden');
        }, 300);
    }

    appModal.addEventListener('click', (e) => {
        if (e.target === appModal) hideApprovalModal();
    });

    // Sweetalert confirm actions
    document.querySelectorAll('.confirm-action').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const action = e.submitter.dataset.action;
            const isApprove = action === 'setujui';
            
            Swal.fire({
                title: isApprove ? 'Setujui Penarikan?' : 'Tolak Penarikan?',
                text: `Apakah Anda yakin ingin ${action} penarikan ini?`,
                icon: isApprove ? 'question' : 'warning',
                showCancelButton: true,
                confirmButtonColor: isApprove ? '#658C58' : '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: isApprove ? 'Ya, Setujui' : 'Ya, Tolak',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-[2rem] border-none shadow-2xl',
                    title: 'font-black text-gray-900',
                    htmlContainer: 'text-sm font-medium text-gray-500',
                    confirmButton: 'rounded-xl px-6 py-3 font-bold text-sm',
                    cancelButton: 'rounded-xl px-6 py-3 font-bold text-sm'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
</script>
@endsection

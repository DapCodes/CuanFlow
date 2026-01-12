@extends('admin.layouts.app')

@section('title', 'Detail Penarikan #' . $withdrawal->id)

@section('breadcrumb')
<li class="flex items-center">
    <span class="mx-2 text-gray-400">/</span>
    <a href="{{ route('admin.withdrawals.index') }}" class="hover:text-cuan-dark">Penarikan</a>
</li>
<li class="flex items-center">
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-900 font-medium">Detail</span>
</li>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: Primary Details -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Informasi Penarikan</h3>
                <div class="flex items-center gap-2">
                    @if($withdrawal->status == 'pending' && !$withdrawal->accepted_by_owner)
                        <span class="px-2.5 py-1 text-xs font-medium bg-orange-100 text-orange-700 rounded-full">Menunggu Owner</span>
                    @endif
                    {!! $withdrawal->status_badge !!}
                </div>
            </div>
            
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Pengguna</p>
                    <div class="flex items-center gap-3">
                        <img src="{{ $withdrawal->user->avatar_url }}" class="h-10 w-10 rounded-full border border-gray-200">
                        <div>
                            <p class="font-bold text-gray-900 leading-tight">{{ $withdrawal->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $withdrawal->user->email }}</p>
                        </div>
                    </div>
                </div>
                
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Outlet</p>
                    <p class="font-bold text-gray-900">{{ $withdrawal->outlet->name ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-500">ID Outlet: {{ $withdrawal->outlet_id ?? '-' }}</p>
                </div>

                <div class="md:col-span-2 bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-3">Tujuan Pengiriman</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-semibold">Bank/E-Wallet</p>
                            <p class="font-bold text-teal-700">{{ $withdrawal->payment_method }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-semibold">Nomor Rekening</p>
                            <div class="flex items-center gap-2">
                                <p class="font-bold text-gray-900" id="accountNumber">{{ $withdrawal->account_number }}</p>
                                <button onclick="copyToClipboard('{{ $withdrawal->account_number }}')" class="text-teal-600 hover:text-teal-700" title="Salin">
                                    <i class="fas fa-copy text-xs"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-semibold">Atas Nama</p>
                            <p class="font-bold text-gray-900">{{ $withdrawal->account_name }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Nominal Kotor</p>
                    <p class="text-lg font-bold text-gray-900">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</p>
                </div>
                
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Pajak ({{ $withdrawal->tax_percent }}%)</p>
                    <p class="text-lg font-bold text-red-600">Rp {{ number_format($withdrawal->tax_amount, 0, ',', '.') }}</p>
                </div>
                
                <div class="md:col-span-2 p-4 bg-teal-50 rounded-xl border border-teal-100 flex justify-between items-center">
                    <div>
                        <p class="text-xs text-teal-600 uppercase font-extrabold tracking-widest mb-1">NOMINAL BERSIH (YANG HARUS DITRANSFER)</p>
                        <p class="text-3xl font-black text-teal-900">Rp {{ number_format($withdrawal->net_amount, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($withdrawal->proof_image)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 font-bold text-gray-800">
                Bukti Transfer (Admin)
            </div>
            <div class="p-6">
                <img src="{{ $withdrawal->proof_image_url }}" alt="Bukti Transfer" class="max-w-full h-auto rounded-lg border shadow-sm">
            </div>
        </div>
        @endif
    </div>

    <!-- Right Column: Actions -->
    <div class="space-y-6">
        @if($withdrawal->isPending())
            
            {{-- Owner Approval Section --}}
            @if(!$withdrawal->accepted_by_owner)
                @if(auth()->user()->hasRole('owner') || auth()->user()->can('setujui penarikan'))
                <div class="bg-white rounded-xl shadow-sm border border-orange-200 p-6 space-y-4">
                    <div class="flex items-center gap-2 text-orange-600 mb-2">
                        <i class="fas fa-user-shield"></i>
                        <h4 class="font-bold">Persetujuan Owner</h4>
                    </div>
                    <p class="text-xs text-gray-500">
                        Penarikan ini memerlukan persetujuan dari Owner sebelum dapat diproses oleh Admin.
                    </p>
                    
                    <form action="{{ route('admin.withdrawals.approve-by-owner', $withdrawal) }}" method="POST">
                        @csrf
                        <button type="submit" onclick="return confirm('Setujui penarikan ini sebagai Owner?')" 
                                class="w-full bg-orange-600 text-white font-bold py-2 rounded-lg hover:bg-orange-700 transition mb-3">
                            <i class="fas fa-check-double mr-2"></i> Setujui (Owner)
                        </button>
                    </form>

                    <form action="{{ route('admin.withdrawals.reject-by-owner', $withdrawal) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Alasan Penolakan</label>
                            <textarea name="reason" required class="w-full border rounded-lg p-2 text-sm focus:ring-red-500" rows="2" placeholder="Contoh: Saldo tidak mencukupi..."></textarea>
                        </div>
                        <button type="submit" onclick="return confirm('Tolak penarikan ini?')"
                                class="w-full bg-white border border-red-500 text-red-500 font-bold py-2 rounded-lg hover:bg-red-50 transition">
                            <i class="fas fa-times mr-2"></i> Tolak (Owner)
                        </button>
                    </form>
                </div>
                @else
                <div class="bg-orange-50 rounded-xl border border-orange-200 p-6 text-center">
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3 text-orange-600">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h4 class="font-bold text-gray-800 mb-1">Menunggu Owner</h4>
                    <p class="text-xs text-gray-500">Penarikan ini sedang menunggu persetujuan dari Owner.</p>
                </div>
                @endif
            @endif

            {{-- Admin Approval Section (Only if accepted by owner) --}}
            @if($withdrawal->accepted_by_owner)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
            <h4 class="font-bold text-gray-800">Tindakan Persetujuan</h4>
            
            <form action="{{ route('admin.withdrawals.approve', $withdrawal) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Catatan (Optional)</label>
                    <textarea name="admin_note" class="w-full border rounded-lg p-2 text-sm focus:ring-cuan-dark" rows="3"></textarea>
                </div>
                <button type="submit" onclick="return confirm('Setujui penarikan ini? Status akan berubah menjadi DISENTUJUI.')" 
                        class="w-full bg-blue-600 text-white font-bold py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-check mr-2"></i> Setujui Pengajuan
                </button>
            </form>

            <hr>

            <form action="{{ route('admin.withdrawals.reject', $withdrawal) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Alasan Penolakan (Wajib)</label>
                    <textarea name="admin_note" required class="w-full border rounded-lg p-2 text-sm focus:ring-red-500" rows="3"></textarea>
                </div>
                <button type="submit" onclick="return confirm('Tolak penarikan ini? Alasan akan dikirim ke user.')"
                        class="w-full bg-red-500 text-white font-bold py-2 rounded-lg hover:bg-red-600 transition">
                    <i class="fas fa-times mr-2"></i> Tolak Pengajuan
                </button>
            </form>
        </div>
            @endif 
            {{-- End Admin Approval Section --}}

        @elseif($withdrawal->isApproved())
        <div class="bg-white rounded-xl shadow-md border-2 border-teal-500 p-6 space-y-4">
            <div class="flex items-center gap-2 text-teal-600 mb-2">
                <i class="fas fa-info-circle"></i>
                <h4 class="font-bold">Konfirmasi Pembayaran</h4>
            </div>
            <p class="text-xs text-gray-500">
                Silakan transfer dana sebesar <strong>Rp {{ number_format($withdrawal->net_amount, 0, ',', '.') }}</strong> ke rekening tujuan, kemudian unggah bukti transfer di bawah ini.
            </p>
            
            <form action="{{ route('admin.withdrawals.paid', $withdrawal) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Unggah Bukti Transfer</label>
                    <input type="file" name="proof_image" required class="w-full text-sm text-gray-500 border rounded-lg p-2 file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                    <p class="text-[10px] text-gray-400 mt-1">Format: JPG, PNG, JPEG. Maks 2MB.</p>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Catatan Tambahan (Optional)</label>
                    <textarea name="admin_note" class="w-full border rounded-lg p-2 text-sm focus:ring-cuan-dark" rows="2" placeholder="Contoh: Transfer via BRI Mobile">{{ old('admin_note', $withdrawal->admin_note) }}</textarea>
                </div>

                <button type="submit" class="w-full bg-teal-600 text-white font-black py-3 rounded-xl hover:bg-teal-700 transition shadow-lg shadow-teal-100">
                    <i class="fas fa-money-bill-wave mr-2"></i> Konfirmasi Sudah Bayar
                </button>
            </form>
        </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
            <h4 class="font-bold text-gray-800">Histori Proses</h4>
            <div class="space-y-4">
                <div>
                    <p class="text-[10px] text-gray-400 uppercase font-semibold">Tgl Diajukan</p>
                    <p class="text-xs text-gray-700 font-medium">{{ $withdrawal->created_at->format('d/m/Y H:i') }}</p>
                </div>
                @if($withdrawal->processed_at)
                <div>
                    <p class="text-[10px] text-gray-400 uppercase font-semibold">Tgl Diproses</p>
                    <p class="text-xs text-gray-700 font-medium">{{ $withdrawal->processed_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 uppercase font-semibold">Diproses Oleh</p>
                    <p class="text-xs text-gray-700 font-medium">{{ $withdrawal->processedBy->name ?? '-' }}</p>
                </div>
                @endif
                @if($withdrawal->admin_note)
                <div class="p-3 bg-gray-50 rounded-lg border">
                    <p class="text-[10px] text-gray-400 uppercase font-semibold mb-1">Catatan Admin</p>
                    <p class="text-xs text-gray-600 whitespace-pre-line">{{ $withdrawal->admin_note }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Nomor rekening disalin ke clipboard!');
        });
    }
</script>
@endsection

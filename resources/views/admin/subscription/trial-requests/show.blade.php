@extends('admin.layouts.app')

@section('title', 'Detail Permintaan Trial')
@section('page-title', 'Detail Verifikasi Trial: ' . $trialRequest->user->name)

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <a href="{{ route('admin.subscription-trial-requests.index') }}" class="hover:text-emerald-600 transition-colors text-sm">Permintaan Trial</a>
</li>
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Detail</span>
</li>
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="px-8 py-6 bg-gray-50/50 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-900">Informasi Usaha</h2>
                    <span class="px-3 py-1 text-xs font-bold rounded-full {{ $trialRequest->status_badge }}">
                        {{ $trialRequest->status_label }}
                    </span>
                </div>
                
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-4">
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Nama Outlet / Usaha</label>
                                <p class="text-lg font-bold text-gray-900">{{ $trialRequest->outlet_name }}</p>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Jenis Bisnis</label>
                                <span class="px-2 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-sm font-semibold">
                                    {{ $trialRequest->business_type ?? 'Tidak ditentukan' }}
                                </span>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Deskripsi Usaha</label>
                                <p class="text-gray-600 leading-relaxed">{{ $trialRequest->business_description }}</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Diajukan Oleh</label>
                                <p class="font-bold text-gray-900">{{ $trialRequest->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $trialRequest->user->email }}</p>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Tanggal Pengajuan</label>
                                <p class="font-semibold text-gray-700">{{ $trialRequest->created_at->format('d F Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Proof Photos -->
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="px-8 py-6 bg-gray-50/50 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Bukti Fisik Usaha</h2>
                </div>
                
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Foto Depan Usaha (Store Front)</label>
                            @if($trialRequest->photo_store_front_path)
                                <a href="{{ $trialRequest->photo_store_front_url }}" target="_blank" class="block group relative rounded-2xl overflow-hidden border border-gray-100 shadow-sm aspect-video">
                                    <img src="{{ $trialRequest->photo_store_front_url }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Store Front">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white font-bold gap-2">
                                        <i class="fas fa-magnifying-glass-plus"></i> Lihat Full
                                    </div>
                                </a>
                            @else
                                <div class="bg-gray-50 rounded-2xl border border-dashed border-gray-200 flex flex-col items-center justify-center py-12 text-gray-400">
                                    <i class="fas fa-image text-3xl mb-2"></i>
                                    <p class="text-xs">Tidak ada foto</p>
                                </div>
                            @endif
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Foto Produk / Stok</label>
                            @if($trialRequest->photo_products_path)
                                <a href="{{ $trialRequest->photo_products_url }}" target="_blank" class="block group relative rounded-2xl overflow-hidden border border-gray-100 shadow-sm aspect-video">
                                    <img src="{{ $trialRequest->photo_products_url }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Products">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white font-bold gap-2">
                                        <i class="fas fa-magnifying-glass-plus"></i> Lihat Full
                                    </div>
                                </a>
                            @else
                                <div class="bg-gray-50 rounded-2xl border border-dashed border-gray-200 flex flex-col items-center justify-center py-12 text-gray-400">
                                    <i class="fas fa-image text-3xl mb-2"></i>
                                    <p class="text-xs">Tidak ada foto</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Panel -->
        <div class="space-y-6">
            @if($trialRequest->isPending())
            <!-- Approval Form -->
            <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-sm space-y-6 sticky top-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Proses Verifikasi</h3>
                    <p class="text-xs text-gray-500 mt-1">Tinjau data di samping dan berikan keputusan.</p>
                </div>

                <!-- Approve Action -->
                <form action="{{ route('admin.subscription-trial-requests.approve', $trialRequest) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-gray-700">Catatan Admin (Opsional)</label>
                        <textarea name="notes" rows="2" class="w-full px-4 py-2 text-sm rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="Misal: Data valid, selamat mencoba trial..."></textarea>
                    </div>
                    <button type="submit" class="w-full py-3 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-100 flex items-center justify-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        Setujui Trial
                    </button>
                </form>

                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-100"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="bg-white px-2 text-gray-400">ATAU</span>
                    </div>
                </div>

                <!-- Reject Action -->
                <form action="{{ route('admin.subscription-trial-requests.reject', $trialRequest) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-gray-700">Alasan Penolakan <span class="text-red-500">*</span></label>
                        <textarea name="reason" rows="2" required class="w-full px-4 py-2 text-sm rounded-xl border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition-all" placeholder="Misal: Foto tidak jelas, data tidak valid..."></textarea>
                    </div>
                    <button type="submit" class="w-full py-3 bg-white text-red-600 border-2 border-red-500 font-bold rounded-xl hover:bg-red-50 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-times-circle"></i>
                        Tolak Permintaan
                    </button>
                </form>
            </div>
            @else
            <!-- Result Panel -->
            <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-sm space-y-6 lg:sticky lg:top-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Riwayat Verifikasi</h3>
                    <p class="text-xs text-gray-500 mt-1">Permintaan ini sudah diproses.</p>
                </div>

                <div class="space-y-4">
                    <div class="p-4 rounded-2xl {{ $trialRequest->status === 'approved' ? 'bg-emerald-50 border border-emerald-100' : 'bg-red-50 border border-red-100' }}">
                        <div class="flex items-start gap-3">
                            <i class="fas {{ $trialRequest->status === 'approved' ? 'fa-circle-check text-emerald-500' : 'fa-circle-xmark text-red-500' }} mt-1"></i>
                            <div>
                                <p class="text-sm font-bold {{ $trialRequest->status === 'approved' ? 'text-emerald-800' : 'text-red-800' }}">
                                    {{ $trialRequest->status_label }}
                                </p>
                                <p class="text-xs text-gray-500 mt-0.5">Oleh: {{ $trialRequest->reviewer->name ?? 'System' }}</p>
                                <p class="text-[10px] text-gray-400">{{ $trialRequest->reviewed_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    @if($trialRequest->admin_notes)
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Catatan / Alasan</label>
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 text-sm text-gray-600 italic leading-relaxed">
                            "{{ $trialRequest->admin_notes }}"
                        </div>
                    </div>
                    @endif
                </div>

                <a href="{{ route('admin.subscription-trial-requests.index') }}" class="w-full py-3 bg-gray-900 text-white text-center font-bold rounded-xl hover:bg-gray-800 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Daftar
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

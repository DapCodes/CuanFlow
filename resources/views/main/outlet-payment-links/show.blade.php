@extends('layouts.app')

@section('title', 'Detail Metode Pembayaran - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('outlet-payment-links.index') }}" class="text-gray-500 hover:text-cuan-green transition-colors">Metode Pembayaran QRIS</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Detail Metode</span>
</li>
@endsection

@section('content')

<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Detail Metode Pembayaran
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Informasi lengkap mengenai metode pembayaran yang terdaftar.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('outlet-payment-links.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all active:scale-95">
                    <!-- <i class="fas fa-arrow-left text-xs"></i> -->
                    <span>Kembali</span>
                </a>
                <a href="{{ route('outlet-payment-links.edit', $outletPaymentLink->id) }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-5 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                    <!-- <i class="fas fa-edit text-xs"></i> -->
                    <span>Edit Metode</span>
                </a>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- KOLOM KIRI: INFO UTAMA --}}
            <div class="lg:col-span-2 space-y-6">
                <x-card-container>
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Informasi Utama</h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Detail penyedia dan identitas akun</p>
                    </div>
                    <div class="px-8 py-8 space-y-8">
                        {{-- Metode & Outlet --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">Metode Pembayaran</label>
                                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-200">
                                    <div class="w-12 h-12 rounded-xl bg-cuan-green/10 flex items-center justify-center border border-cuan-green/20">
                                        <i class="fas fa-wallet text-cuan-green text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-gray-900 uppercase tracking-widest leading-none">{{ $outletPaymentLink->paymentMethod->name }}</p>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-2">Code: {{ $outletPaymentLink->paymentMethod->code }}</p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">Outlet Terdaftar</label>
                                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-200">
                                    <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center border border-gray-200">
                                        <i class="fas fa-store text-gray-400 text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-gray-900 uppercase tracking-widest leading-none">{{ $outletPaymentLink->outlet->name }}</p>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-2">Lokasi Outlet Utama</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Rekening & Pemilik --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4">
                            @if($outletPaymentLink->account_number)
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Nomor Rekening / HP</label>
                                <p class="text-lg font-mono font-black text-gray-900 tracking-tighter">{{ $outletPaymentLink->account_number }}</p>
                            </div>
                            @endif

                            @if($outletPaymentLink->account_name)
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Nama Pemilik Akun</label>
                                <p class="text-base font-bold text-gray-900">{{ $outletPaymentLink->account_name }}</p>
                            </div>
                            @endif
                        </div>

                        {{-- Catatan --}}
                        @if($outletPaymentLink->notes)
                        <div class="pt-4">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">Catatan Tambahan</label>
                            <div class="p-6 bg-gray-50 rounded-2xl border border-gray-200 border-l-4 border-l-cuan-green">
                                <p class="text-sm text-gray-600 font-medium leading-relaxed italic">"{{ $outletPaymentLink->notes }}"</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </x-card-container>

                {{-- Status & Log --}}
                <x-card-container>
                    <div class="px-8 py-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="flex items-center gap-4">
                            @if($outletPaymentLink->is_active)
                                <div class="w-12 h-12 rounded-full bg-cuan-green/10 flex items-center justify-center border-4 border-white shadow-sm ring-1 ring-cuan-green/20">
                                    <div class="w-2 h-2 rounded-full bg-cuan-green animate-pulse"></div>
                                </div>
                                <div>
                                    <span class="text-xs font-black text-gray-900 uppercase tracking-widest">Metode Aktif</span>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Dapat digunakan pelanggan</p>
                                </div>
                            @else
                                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center border-4 border-white shadow-sm ring-1 ring-gray-200">
                                    <div class="w-2 h-2 rounded-full bg-gray-400"></div>
                                </div>
                                <div>
                                    <span class="text-xs font-black text-gray-900 uppercase tracking-widest text-gray-400">Metode Nonaktif</span>
                                    <p class="text-[10px] text-gray-300 font-bold uppercase tracking-widest mt-1">Status saat ini terhenti</p>
                                </div>
                            @endif
                        </div>
                        <div class="grid grid-cols-2 gap-8 border-l border-gray-100 pl-8">
                            <div>
                                <span class="block text-[10px] font-black uppercase tracking-widest text-gray-300 mb-1">Dibuat</span>
                                <span class="text-[11px] font-bold text-gray-500 uppercase">{{ $outletPaymentLink->created_at->format('d M Y') }}</span>
                            </div>
                            <div>
                                <span class="block text-[10px] font-black uppercase tracking-widest text-gray-300 mb-1">Update</span>
                                <span class="text-[11px] font-bold text-gray-500 uppercase">{{ $outletPaymentLink->updated_at->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                </x-card-container>
            </div>

            {{-- KOLOM KANAN: QR CODE --}}
            <div class="space-y-6">
                <x-card-container>
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Visual QR Code</h2>
                    </div>
                    <div class="px-8 py-8 text-center">
                        @if($outletPaymentLink->qr_image)
                            <div class="inline-block relative group">
                                <img src="{{ asset('storage/' . $outletPaymentLink->qr_image) }}" 
                                     alt="QR Code" 
                                     class="max-h-64 rounded-3xl border-4 border-white shadow-2xl group-hover:scale-[1.05] transition-transform duration-500">
                            </div>
                        @else
                            <div class="py-12 flex flex-col items-center">
                                <div class="w-24 h-24 rounded-[2rem] bg-gray-50 flex items-center justify-center border border-dashed border-gray-200 mb-4">
                                    <i class="fas fa-qrcode text-3xl text-gray-300"></i>
                                </div>
                                <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Tidak Ada QR Code</p>
                            </div>
                        @endif
                    </div>
                    <div class="px-8 py-6 bg-gray-50/30 border-t border-gray-50">
                        @if($outletPaymentLink->qr_image)
                            <a href="{{ asset('storage/' . $outletPaymentLink->qr_image) }}" 
                               download="QR_{{ Str::slug($outletPaymentLink->paymentMethod->name) }}_{{ Str::slug($outletPaymentLink->account_name) }}.png"
                               class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-gray-900 text-white font-black text-[10px] uppercase tracking-widest hover:bg-black transition-all active:scale-95 shadow-lg shadow-gray-900/10">
                                <i class="fas fa-download text-xs"></i>
                                Unduh QR Code
                            </a>
                        @else
                            <button disabled 
                                    class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-gray-100 text-gray-400 font-black text-[10px] uppercase tracking-widest cursor-not-allowed border border-gray-200">
                                <i class="fas fa-download text-xs"></i>
                                Unduh QR Code
                            </button>
                        @endif
                    </div>
                </x-card-container>

                {{-- Hapus Area --}}
                <div class="p-8 rounded-[2rem] border-2 border-dashed border-red-100 bg-red-50/50">
                    <h3 class="text-sm font-black text-red-600 uppercase tracking-widest mb-2">Hapus Layanan?</h3>
                    <p class="text-[10px] font-bold text-red-400 uppercase tracking-widest leading-relaxed mb-6">Tindakan ini permanen. Pastikan Anda tidak lagi membutuhkan metode ini.</p>
                    <form action="{{ route('outlet-payment-links.destroy', $outletPaymentLink->id) }}" method="POST" id="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDelete()"
                                class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-red-500 text-white font-black text-[10px] uppercase tracking-widest hover:bg-red-600 transition-all active:scale-95 shadow-lg shadow-red-500/20">
                            <i class="fas fa-trash text-xs"></i>
                            Hapus Permanen
                        </button>
                    </form>
                </div>
            </div>
        </div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete() {
    Swal.fire({
        title: 'Hapus Metode?',
        text: 'Apakah Anda yakin ingin menghapus metode pembayaran ini secara permanen?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus Sekarang',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'rounded-[2rem] border-none shadow-2xl',
            title: 'font-black text-gray-900',
            htmlContainer: 'text-sm font-medium text-gray-500',
            confirmButton: 'rounded-xl px-6 py-3 font-black text-[10px] uppercase tracking-widest',
            cancelButton: 'rounded-xl px-6 py-3 font-black text-[10px] uppercase tracking-widest'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form').submit();
        }
    });
}
</script>
@endpush
@endsection
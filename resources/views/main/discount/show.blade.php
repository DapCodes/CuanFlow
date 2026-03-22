@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Detail Diskon - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('discounts.index') }}" class="text-gray-500 hover:text-cuan-green transition-colors">Kelola Diskon</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Detail Diskon</span>
</li>
@endsection

@section('content')
@php
    $isExpired = $discount->end_date && $discount->end_date->lt(now());
    $isActive = $discount->is_active && !$isExpired;
@endphp

<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER: judul + status + aksi --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-xl md:text-2xl font-black text-gray-900">
                        {{ $discount->name }}
                    </h1>
                    @if($isExpired)
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-red-50 text-red-500 border border-red-100">
                            Kadaluarsa
                        </span>
                    @elseif($isActive)
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-cuan-green/10 text-cuan-green border border-cuan-green/10">
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-50 text-gray-400 border border-gray-200">
                            Tidak Aktif
                        </span>
                    @endif
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    Kode:
                    <span class="font-mono font-bold text-gray-900 bg-gray-100 px-2 py-0.5 rounded-lg">
                        {{ $discount->code }}
                    </span>
                </p>
            </div>

            {{-- Aksi cepat --}}
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('discounts.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all active:scale-95">
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span>Kembali</span>
                </a>

                @can('edit diskon')
                <a href="{{ route('discounts.edit', $discount->id) }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-cuan-green/10 text-cuan-green border border-cuan-green/20 px-5 py-3 text-sm font-black hover:bg-cuan-green hover:text-white transition-all active:scale-95">
                    <i class="fas fa-edit text-xs"></i>
                    <span>Edit Diskon</span>
                </a>
                @endcan

                @can('aktifkan nonaktifkan diskon')
                <form action="{{ route('discounts.toggle-status', $discount->id) }}" method="POST" class="inline confirm-toggle"
                      data-name="{{ $discount->name }}"
                      data-status="{{ $discount->is_active ? 'nonaktifkan' : 'aktifkan' }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl px-5 py-3 text-sm font-black transition-all active:scale-95 shadow-lg
                            {{ $discount->is_active ? 'bg-gray-800 hover:bg-gray-900 text-white shadow-gray-800/20' : 'bg-cuan-green hover:bg-cuan-dark text-white shadow-cuan-green/20' }}">
                        <i class="fas fa-{{ $discount->is_active ? 'pause' : 'play' }} text-xs"></i>
                        <span>{{ $discount->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</span>
                    </button>
                </form>
                @endcan

                @can('hapus diskon')
                <form action="{{ route('discounts.destroy', $discount->id) }}" method="POST" class="inline confirm-delete"
                      data-name="{{ $discount->name }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all active:scale-95"
                            title="Hapus">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </form>
                @endcan
            </div>
        </section>

        {{-- KONTEN UTAMA: kiri detail, kanan statistik --}}
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- DETAIL DISKON --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Informasi Diskon --}}
                <x-card-container>
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Informasi Diskon</h2>
                    </div>
                    <div class="px-8 py-6">
                        <dl class="divide-y divide-gray-100 text-sm">
                            <div class="py-4 flex items-start justify-between gap-4">
                                <dt class="text-[10px] font-black uppercase tracking-widest text-gray-400 w-36">Kode</dt>
                                <dd class="flex-1 font-mono font-bold text-gray-900 bg-gray-100 px-2 py-0.5 rounded-lg text-xs inline-block">
                                    {{ $discount->code }}
                                </dd>
                            </div>

                            <div class="py-4 flex items-start justify-between gap-4">
                                <dt class="text-[10px] font-black uppercase tracking-widest text-gray-400 w-36">Tipe</dt>
                                <dd class="flex-1">
                                    @if($discount->type === 'percentage')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-blue-50 text-blue-600 border border-blue-100">
                                            Persentase
                                        </span>
                                    @elseif($discount->type === 'fixed')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-cuan-green/10 text-cuan-green border border-cuan-green/20">
                                            Fixed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-purple-50 text-purple-600 border border-purple-100">
                                            Buy X Get Y
                                        </span>
                                    @endif
                                </dd>
                            </div>

                            <div class="py-4 flex items-start justify-between gap-4">
                                <dt class="text-[10px] font-black uppercase tracking-widest text-gray-400 w-36">
                                    @if($discount->type === 'buy_x_get_y') Promo @else Nilai Diskon @endif
                                </dt>
                                <dd class="flex-1 font-bold text-gray-900">
                                    @if($discount->type === 'percentage')
                                        {{ number_format($discount->value, 0) }}%
                                    @elseif($discount->type === 'fixed')
                                        Rp {{ number_format($discount->value, 0) }}
                                    @else
                                        Beli {{ $discount->buy_quantity }} Gratis {{ $discount->get_quantity }}
                                    @endif
                                </dd>
                            </div>

                            @if($discount->type !== 'buy_x_get_y')
                                <div class="py-4 flex items-start justify-between gap-4">
                                    <dt class="text-[10px] font-black uppercase tracking-widest text-gray-400 w-36">Minimal Pembelian</dt>
                                    <dd class="flex-1 font-medium text-gray-900">
                                        {{ $discount->min_purchase > 0 ? 'Rp ' . number_format($discount->min_purchase, 0) : 'Tidak ada' }}
                                    </dd>
                                </div>

                                <div class="py-4 flex items-start justify-between gap-4">
                                    <dt class="text-[10px] font-black uppercase tracking-widest text-gray-400 w-36">Maksimal Diskon</dt>
                                    <dd class="flex-1 font-medium text-gray-900">
                                        {{ $discount->max_discount ? 'Rp ' . number_format($discount->max_discount, 0) : 'Tidak terbatas' }}
                                    </dd>
                                </div>
                            @endif

                            <div class="py-4 flex items-start justify-between gap-4">
                                <dt class="text-[10px] font-black uppercase tracking-widest text-gray-400 w-36">Berlaku Untuk</dt>
                                <dd class="flex-1 font-medium text-gray-900">
                                    @if($discount->product)
                                        Produk: {{ $discount->product->name }}
                                    @elseif($discount->category)
                                        Kategori: {{ $discount->category->name }}
                                    @else
                                        Semua produk
                                    @endif
                                </dd>
                            </div>

                            <div class="py-4 flex items-start justify-between gap-4">
                                <dt class="text-[10px] font-black uppercase tracking-widest text-gray-400 w-36">Tipe Kode</dt>
                                <dd class="flex-1">
                                    @if($discount->is_voucher)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-indigo-50 text-indigo-600 border border-indigo-100">
                                            Voucher
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-50 text-gray-400 border border-gray-200">
                                            Bukan Voucher
                                        </span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </x-card-container>

                {{-- Periode & Status Waktu --}}
                <x-card-container>
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Periode Berlaku</h2>
                    </div>
                    <div class="px-8 py-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Tanggal Mulai</p>
                                <p class="font-bold text-gray-900">
                                    {{ $discount->start_date ? $discount->start_date->format('d M Y, H:i') : 'Tidak ditentukan' }}
                                </p>
                            </div>
                            <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Tanggal Berakhir</p>
                                <p class="font-bold text-gray-900">
                                    {{ $discount->end_date ? $discount->end_date->format('d M Y, H:i') : 'Tidak terbatas' }}
                                </p>
                            </div>
                        </div>

                        @if($discount->start_date || $discount->end_date)
                            <div class="mt-4 rounded-2xl border border-blue-100 bg-blue-50 px-5 py-4">
                                <p class="text-[10px] font-black uppercase tracking-widest text-blue-600">
                                    @if($discount->end_date && $discount->end_date->isFuture())
                                        Diskon akan berakhir {{ $discount->end_date->diffForHumans() }}.
                                    @elseif($discount->end_date && $discount->end_date->isPast())
                                        Diskon telah berakhir {{ $discount->end_date->diffForHumans() }}.
                                    @elseif($discount->start_date && $discount->start_date->isFuture())
                                        Diskon akan mulai {{ $discount->start_date->diffForHumans() }}.
                                    @else
                                        Diskon sedang berjalan.
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </x-card-container>
            </div>

            {{-- SIDEBAR: Statistik & info --}}
            <div class="space-y-6">
                {{-- Statistik Penggunaan --}}
                <x-card-container>
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Statistik</h2>
                    </div>
                    <div class="px-6 py-6 space-y-4">
                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Total Digunakan</p>
                            <p class="text-2xl font-black text-gray-900">{{ $discount->used_count }}</p>
                        </div>

                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Batas Penggunaan</p>
                            <p class="text-2xl font-black text-gray-900">{{ $discount->usage_limit ?? '∞' }}</p>
                        </div>

                        @if($discount->usage_limit)
                            @php
                                $percentage = ($discount->used_count / $discount->usage_limit) * 100;
                                $percentage = min($percentage, 100);
                            @endphp
                            <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">Progress</p>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full bg-cuan-green transition-all"
                                         style="width: {{ $percentage }}%"></div>
                                </div>
                                <p class="mt-2 text-right text-[10px] font-black uppercase tracking-widest text-gray-400">
                                    {{ number_format($percentage, 1) }}%
                                </p>
                            </div>
                        @endif
                    </div>
                </x-card-container>

                {{-- Informasi Sistem --}}
                <x-card-container>
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Informasi Sistem</h2>
                    </div>
                    <div class="px-6 py-6">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Dibuat</span>
                                <span class="font-bold text-gray-900 text-sm">{{ $discount->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Terakhir Diubah</span>
                                <span class="font-bold text-gray-900 text-sm">{{ $discount->updated_at->format('d M Y') }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Status</span>
                                @if($isExpired)
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-red-50 text-red-500 border border-red-100">Kadaluarsa</span>
                                @elseif($isActive)
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-cuan-green/10 text-cuan-green border border-cuan-green/10">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-50 text-gray-400 border border-gray-200">Nonaktif</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </x-card-container>

                {{-- Copy kode diskon --}}
                <x-card-container>
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Salin Kode</h2>
                    </div>
                    <div class="px-6 py-6">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-4">
                            Bagikan kode ini ke pelanggan atau kasir.
                        </p>
                        <div class="flex gap-2">
                            <input type="text"
                                   id="discountCode"
                                   value="{{ $discount->code }}"
                                   readonly
                                   class="flex-1 px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-sm font-mono font-bold text-gray-900">
                            <button type="button"
                                    onclick="copyDiscountCode(event)"
                                    class="px-4 py-3 rounded-xl bg-cuan-green hover:bg-cuan-dark text-white text-sm font-black transition-all active:scale-95 shadow-lg shadow-cuan-green/20">
                                <i class="fas fa-copy text-xs"></i>
                            </button>
                        </div>
                    </div>
                </x-card-container>
            </div>
        </section>
    </div>
</main>

@push('scripts')
<script>
// SweetAlert2 Notifications
@if(session('success'))
    document.addEventListener('DOMContentLoaded', function() {
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
    });
@endif

@if(session('error'))
    document.addEventListener('DOMContentLoaded', function() {
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
    });
@endif

// Confirm Toggle
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.confirm-toggle').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = this.dataset.name;
            const status = this.dataset.status;

            Swal.fire({
                title: `${status.charAt(0).toUpperCase() + status.slice(1)} Diskon?`,
                text: `Apakah Anda yakin ingin ${status} diskon "${name}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#658C58',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-[2rem] border-none shadow-2xl',
                    title: 'font-black text-gray-900',
                    htmlContainer: 'text-sm font-medium text-gray-500',
                    confirmButton: 'rounded-xl px-6 py-3 font-bold text-sm',
                    cancelButton: 'rounded-xl px-6 py-3 font-bold text-sm'
                }
            }).then((result) => {
                if (result.isConfirmed) this.submit();
            });
        });
    });

    document.querySelectorAll('.confirm-delete').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = this.dataset.name;

            Swal.fire({
                title: 'Hapus Diskon?',
                text: `Apakah Anda yakin ingin menghapus "${name}"? Tindakan ini tidak dapat dibatalkan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-[2rem] border-none shadow-2xl',
                    title: 'font-black text-gray-900',
                    htmlContainer: 'text-sm font-medium text-gray-500',
                    confirmButton: 'rounded-xl px-6 py-3 font-bold text-sm',
                    cancelButton: 'rounded-xl px-6 py-3 font-bold text-sm'
                }
            }).then((result) => {
                if (result.isConfirmed) this.submit();
            });
        });
    });
});

function copyDiscountCode(e) {
    const codeInput = document.getElementById('discountCode');
    const code = codeInput.value;
    const button = e.currentTarget;

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(code).then(() => showCopyFeedback(button));
    } else {
        codeInput.select();
        document.execCommand('copy');
        showCopyFeedback(button);
    }
}

function showCopyFeedback(button) {
    const originalHtml = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check text-xs"></i>';
    button.classList.remove('bg-cuan-green', 'hover:bg-cuan-dark');
    button.classList.add('bg-gray-800');

    setTimeout(() => {
        button.innerHTML = originalHtml;
        button.classList.remove('bg-gray-800');
        button.classList.add('bg-cuan-green', 'hover:bg-cuan-dark');
    }, 2000);
}
</script>
@endpush
@endsection

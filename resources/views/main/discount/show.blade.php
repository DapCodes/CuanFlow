@extends('layouts.app')

@section('title', 'Detail Diskon - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('discounts.index') }}" class="text-gray-500 hover:text-gray-700">Kelola Diskon</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Detail Diskon</span>
</li>
@endsection

@section('content')
@php
    $isExpired = $discount->end_date && $discount->end_date->lt(now());
    $isActive = $discount->is_active && !$isExpired;
@endphp

<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Notifikasi sukses --}}
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-check-circle mt-0.5 text-green-500"></i>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        {{-- HEADER: judul + status + aksi (seragam) --}}
        <section
            class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-500 border border-red-100">
                        <i class="fas fa-tag text-sm"></i>
                    </span>
                    <div>
                        <h1 class="text-xl md:text-2xl font-semibold text-gray-900">
                            {{ $discount->name }}
                        </h1>
                        <p class="text-xs md:text-sm text-gray-500 mt-0.5">
                            Kode diskon:
                            <span class="font-mono font-semibold text-gray-800 bg-gray-100 px-1.5 py-0.5 rounded">
                                {{ $discount->code }}
                            </span>
                        </p>
                    </div>
                </div>

                {{-- Status pill --}}
                <div class="mt-2">
                    @if($isExpired)
                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>
                            Kadaluarsa
                        </span>
                    @elseif($isActive)
                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                            Aktif
                        </span>
                    @else
                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1.5"></span>
                            Tidak Aktif
                        </span>
                    @endif
                </div>
            </div>

            {{-- Aksi cepat (mobile full width) --}}
            <div class="flex flex-col md:flex-row gap-2 md:gap-3 w-full md:w-auto md:justify-end">
                <a href="{{ route('discounts.edit', $discount->id) }}"
                   class="w-full md:w-auto inline-flex items-center justify-center px-4 md:px-5 py-2.5 text-sm font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-edit mr-2 text-xs"></i>
                    <span>Edit</span>
                </a>

                <form action="{{ route('discounts.toggle-status', $discount->id) }}" method="POST" class="w-full md:w-auto">
                    @csrf
                    <button type="submit"
                            class="w-full md:w-auto inline-flex items-center justify-center px-4 md:px-5 py-2.5 text-sm font-semibold rounded-lg {{ $discount->is_active ? 'bg-gray-600 hover:bg-gray-700' : 'bg-emerald-500 hover:bg-emerald-600' }} text-white">
                        <i class="fas fa-{{ $discount->is_active ? 'pause' : 'play' }} mr-2 text-xs"></i>
                        <span>{{ $discount->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</span>
                    </button>
                </form>

                <form action="{{ route('discounts.destroy', $discount->id) }}" method="POST"
                      class="w-full md:w-auto"
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus diskon ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full md:w-auto inline-flex items-center justify-center px-4 md:px-5 py-2.5 text-sm font-semibold rounded-lg bg-red-500 hover:bg-red-600 text-white">
                        <i class="fas fa-trash mr-2 text-xs"></i>
                        <span>Hapus</span>
                    </button>
                </form>
            </div>
        </section>

        {{-- KONTEN UTAMA: kiri detail, kanan statistik --}}
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- DETAIL DISKON --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Informasi Diskon --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 md:p-6">
                    <h2 class="text-base md:text-lg font-semibold text-gray-900 mb-4">
                        Informasi Diskon
                    </h2>
                    <dl class="divide-y divide-gray-100 text-sm">
                        <div class="py-3 flex items-start justify-between gap-4">
                            <dt class="text-gray-500 w-32 md:w-40">Kode</dt>
                            <dd class="flex-1 font-mono font-semibold text-gray-900">
                                {{ $discount->code }}
                            </dd>
                        </div>

                        <div class="py-3 flex items-start justify-between gap-4">
                            <dt class="text-gray-500 w-32 md:w-40">Tipe</dt>
                            <dd class="flex-1">
                                @if($discount->type === 'percentage')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                        Persentase
                                    </span>
                                @elseif($discount->type === 'fixed')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        Fixed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100">
                                        Buy X Get Y
                                    </span>
                                @endif
                            </dd>
                        </div>

                        {{-- Nilai Diskon / Buy X Get Y --}}
                        <div class="py-3 flex items-start justify-between gap-4">
                            <dt class="text-gray-500 w-32 md:w-40">
                                @if($discount->type === 'percentage' || $discount->type === 'fixed')
                                    Nilai Diskon
                                @else
                                    Promo
                                @endif
                            </dt>
                            <dd class="flex-1 font-semibold text-gray-900">
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
                            <div class="py-3 flex items-start justify-between gap-4">
                                <dt class="text-gray-500 w-32 md:w-40">Minimal Pembelian</dt>
                                <dd class="flex-1 font-medium text-gray-900">
                                    {{ $discount->min_purchase > 0 ? 'Rp ' . number_format($discount->min_purchase, 0) : 'Tidak ada' }}
                                </dd>
                            </div>

                            <div class="py-3 flex items-start justify-between gap-4">
                                <dt class="text-gray-500 w-32 md:w-40">Maksimal Diskon</dt>
                                <dd class="flex-1 font-medium text-gray-900">
                                    {{ $discount->max_discount ? 'Rp ' . number_format($discount->max_discount, 0) : 'Tidak terbatas' }}
                                </dd>
                            </div>
                        @endif

                        {{-- Berlaku untuk --}}
                        <div class="py-3 flex items-start justify-between gap-4">
                            <dt class="text-gray-500 w-32 md:w-40">Berlaku Untuk</dt>
                            <dd class="flex-1">
                                @if($discount->product)
                                    <p class="text-sm font-semibold text-gray-900">
                                        Produk: {{ $discount->product->name }}
                                    </p>
                                @elseif($discount->category)
                                    <p class="text-sm font-semibold text-gray-900">
                                        Kategori: {{ $discount->category->name }}
                                    </p>
                                @else
                                    <p class="text-sm font-semibold text-gray-900">
                                        Semua produk
                                    </p>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                {{-- Periode & Status Waktu --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 md:p-6">
                    <h2 class="text-base md:text-lg font-semibold text-gray-900 mb-4">
                        Periode Berlaku
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                            <p class="text-xs text-gray-500 mb-1">Tanggal Mulai</p>
                            <p class="font-medium text-gray-900">
                                {{ $discount->start_date ? $discount->start_date->format('d M Y, H:i') : 'Tidak ditentukan' }}
                            </p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                            <p class="text-xs text-gray-500 mb-1">Tanggal Berakhir</p>
                            <p class="font-medium text-gray-900">
                                {{ $discount->end_date ? $discount->end_date->format('d M Y, H:i') : 'Tidak terbatas' }}
                            </p>
                        </div>
                    </div>

                    @if($discount->start_date || $discount->end_date)
                        <div class="mt-4 rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-xs text-blue-800">
                            @if($discount->end_date && $discount->end_date->isFuture())
                                Diskon akan berakhir {{ $discount->end_date->diffForHumans() }}.
                            @elseif($discount->end_date && $discount->end_date->isPast())
                                Diskon telah berakhir {{ $discount->end_date->diffForHumans() }}.
                            @elseif($discount->start_date && $discount->start_date->isFuture())
                                Diskon akan mulai {{ $discount->start_date->diffForHumans() }}.
                            @else
                                Diskon sedang berjalan.
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- SIDEBAR: Statistik & info --}}
            <div class="space-y-6">
                {{-- Statistik Penggunaan --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 md:p-6">
                    <h2 class="text-base md:text-lg font-semibold text-gray-900 mb-4">
                        Statistik Penggunaan
                    </h2>
                    <div class="space-y-4 text-sm">
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                            <p class="text-xs text-gray-500 mb-1">Total Digunakan</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $discount->used_count }}</p>
                        </div>

                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                            <p class="text-xs text-gray-500 mb-1">Batas Penggunaan</p>
                            <p class="text-xl font-semibold text-gray-900">
                                {{ $discount->usage_limit ?? '∞' }}
                            </p>
                        </div>

                        @if($discount->usage_limit)
                            @php
                                $percentage = ($discount->used_count / $discount->usage_limit) * 100;
                                $percentage = min($percentage, 100);
                            @endphp
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                <p class="text-xs text-gray-500 mb-2">Progress</p>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full bg-blue-500 transition-all"
                                         style="width: {{ $percentage }}%"></div>
                                </div>
                                <p class="mt-1 text-right text-xs text-gray-500">
                                    {{ number_format($percentage, 1) }}%
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Informasi Sistem --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 md:p-6 text-sm">
                    <h2 class="text-base md:text-lg font-semibold text-gray-900 mb-4">
                        Informasi Sistem
                    </h2>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-1.5 border-b border-gray-100">
                            <span class="text-gray-500">Dibuat</span>
                            <span class="font-medium text-gray-900">
                                {{ $discount->created_at->format('d M Y') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-1.5 border-b border-gray-100">
                            <span class="text-gray-500">Terakhir Diubah</span>
                            <span class="font-medium text-gray-900">
                                {{ $discount->updated_at->format('d M Y') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-1.5">
                            <span class="text-gray-500">Status</span>
                            <span class="font-medium text-gray-900">
                                @if($isExpired)
                                    Kadaluarsa
                                @elseif($isActive)
                                    Aktif
                                @else
                                    Tidak Aktif
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Copy kode diskon --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 md:p-6 text-sm">
                    <h2 class="text-base font-semibold text-gray-900 mb-3">
                        Copy Kode Diskon
                    </h2>
                    <p class="text-xs text-gray-500 mb-3">
                        Gunakan kode ini saat promosi di media sosial, poster, atau informasi ke kasir.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <input type="text"
                               id="discountCode"
                               value="{{ $discount->code }}"
                               readonly
                               class="flex-1 px-3 py-2.5 rounded-lg border border-gray-300 bg-gray-50 text-sm font-mono text-gray-900">
                        <button type="button"
                                onclick="copyDiscountCode(event)"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium">
                            <i class="fas fa-copy mr-2 text-xs"></i>
                            <span>Copy</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>

@push('scripts')
<script>
function copyDiscountCode(e) {
    const codeInput = document.getElementById('discountCode');
    const code = codeInput.value;
    const button = e.currentTarget;

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(code).then(() => showCopyFeedback(button));
    } else {
        // Fallback lama
        codeInput.select();
        document.execCommand('copy');
        showCopyFeedback(button);
    }
}

function showCopyFeedback(button) {
    const originalHtml = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check mr-2 text-xs"></i><span>Tersalin</span>';
    button.classList.remove('bg-purple-600', 'hover:bg-purple-700');
    button.classList.add('bg-green-600');

    setTimeout(() => {
        button.innerHTML = originalHtml;
        button.classList.remove('bg-green-600');
        button.classList.add('bg-purple-600', 'hover:bg-purple-700');
    }, 2000);
}
</script>
@endpush
@endsection

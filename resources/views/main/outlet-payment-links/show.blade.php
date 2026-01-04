@extends('layouts.app')

@section('title', 'Detail Metode Pembayaran - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('outlet-payment-links.index') }}" class="text-gray-500 hover:text-gray-700">Metode Pembayaran QRIS</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Detail</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-500 border border-blue-100">
                        <i class="fas fa-eye text-sm"></i>
                    </span>
                    <span>Detail Metode Pembayaran</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Informasi lengkap {{ $outletPaymentLink->paymentMethod->name }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                <a href="{{ route('outlet-payment-links.edit', $outletPaymentLink->id) }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-yellow-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-yellow-600">
                    <i class="fas fa-edit text-xs"></i>
                    <span>Edit</span>
                </a>
            </div>
        </section>

        {{-- DETAIL INFO --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            <div class="px-4 md:px-6 py-6 space-y-6">
                
                {{-- Metode Pembayaran --}}
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wide text-gray-500 mb-3">Metode Pembayaran</label>
                    <div class="flex items-center gap-3 p-4 bg-gradient-to-r from-pink-50 to-red-50 rounded-lg border border-pink-100">
                        <div class="w-12 h-12 rounded-lg bg-white flex items-center justify-center border border-pink-200 shadow-sm">
                            <i class="fas fa-building text-pink-500 text-lg"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $outletPaymentLink->paymentMethod->name }}</p>
                            <p class="text-xs text-gray-600 mt-0.5">{{ $outletPaymentLink->paymentMethod->code }}</p>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-200">

                {{-- Outlet --}}
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wide text-gray-500 mb-2">Outlet</label>
                    <p class="text-sm font-medium text-gray-900">{{ $outletPaymentLink->outlet->name }}</p>
                </div>

                {{-- Nomor Rekening / HP --}}
                @if($outletPaymentLink->account_number)
                    <div>
                        <label class="block text-xs font-medium uppercase tracking-wide text-gray-500 mb-2">Nomor Rekening / HP</label>
                        <p class="font-mono text-base font-semibold text-gray-900">{{ $outletPaymentLink->account_number }}</p>
                    </div>
                @endif

                {{-- Nama Pemilik --}}
                @if($outletPaymentLink->account_name)
                    <div>
                        <label class="block text-xs font-medium uppercase tracking-wide text-gray-500 mb-2">Nama Pemilik</label>
                        <p class="text-sm text-gray-900">{{ $outletPaymentLink->account_name }}</p>
                    </div>
                @endif

                {{-- QR Code --}}
                @if($outletPaymentLink->qr_image)
                    <div>
                        <label class="block text-xs font-medium uppercase tracking-wide text-gray-500 mb-3">QR Code</label>
                        <div class="inline-block p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <img src="{{ asset('storage/' . $outletPaymentLink->qr_image) }}" 
                                 alt="QR Code" 
                                 class="max-h-64 rounded-lg shadow-sm">
                        </div>
                    </div>
                @endif

                {{-- Catatan --}}
                @if($outletPaymentLink->notes)
                    <div>
                        <label class="block text-xs font-medium uppercase tracking-wide text-gray-500 mb-2">Catatan</label>
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $outletPaymentLink->notes }}</p>
                        </div>
                    </div>
                @endif

                <hr class="border-gray-200">

                {{-- Status --}}
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wide text-gray-500 mb-2">Status</label>
                    @if($outletPaymentLink->is_active)
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span>
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-gray-50 text-gray-700 border border-gray-200">
                            <span class="w-2 h-2 rounded-full bg-gray-400 mr-2"></span>
                            Tidak Aktif
                        </span>
                    @endif
                </div>

                {{-- Tanggal --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-medium uppercase tracking-wide text-gray-500 mb-2">Dibuat Pada</label>
                        <p class="text-sm text-gray-700">{{ $outletPaymentLink->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium uppercase tracking-wide text-gray-500 mb-2">Terakhir Diperbarui</label>
                        <p class="text-sm text-gray-700">{{ $outletPaymentLink->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Actions --}}
        <section class="flex flex-col md:flex-row items-center justify-between gap-3">
            <form action="{{ route('outlet-payment-links.destroy', $outletPaymentLink->id) }}" method="POST"
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus metode pembayaran ini?')"
                  class="w-full md:w-auto">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="w-full md:w-auto inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-100">
                    <i class="fas fa-trash text-xs"></i>
                    <span>Hapus Metode</span>
                </button>
            </form>

            <a href="{{ route('outlet-payment-links.index') }}"
               class="w-full md:w-auto inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                <i class="fas fa-arrow-left text-xs"></i>
                <span>Kembali</span>
            </a>
        </section>

    </div>
</main>
@endsection
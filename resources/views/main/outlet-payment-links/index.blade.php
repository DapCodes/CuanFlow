@extends('layouts.app')

@section('title', 'Metode Pembayaran QRIS - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Metode Pembayaran QRIS</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Alert / Notifikasi --}}
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
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-pink-50 text-pink-500 border border-pink-100">
                        <i class="fas fa-qrcode text-sm"></i>
                    </span>
                    <span>Metode Pembayaran QRIS</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola metode pembayaran QRIS untuk outlet Anda. Tambahkan rekening bank, e-wallet, atau QR code untuk memudahkan transaksi.
                </p>
            </div>
            @can('buat metode pembayaran')
            <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                <a href="{{ route('outlet-payment-links.create') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-pink-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:ring-offset-1">
                    <i class="fas fa-plus-circle text-sm"></i>
                    <span>Tambah Metode</span>
                </a>
            </div>
            @endcan
        </section>

        {{-- RINGKASAN STATISTIK --}}
        <section class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Metode</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100">
                        <i class="fas fa-wallet text-gray-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Aktif</p>
                        <p class="mt-1 text-2xl font-semibold text-emerald-600">{{ $stats['active'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100">
                        <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Tidak Aktif</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-600">{{ $stats['inactive'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100">
                        <i class="fas fa-times-circle text-gray-500 text-lg"></i>
                    </div>
                </div>
            </div>
        </section>

        {{-- KONTEN UTAMA: TABEL --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            {{-- Toolbar: Search --}}
            <div class="border-b border-gray-200 px-4 md:px-6 py-4">
                <div class="w-full md:max-w-md">
                    <label class="text-xs font-medium text-gray-500 mb-1 block">Cari metode pembayaran</label>
                    <div class="relative">
                        <input type="text" id="searchPayment" placeholder="Cari berdasarkan nama atau nomor rekening..."
                               class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-pink-400">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    </div>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Metode Pembayaran
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Nomor Rekening / HP
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Nama Pemilik
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                QR Code
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Status
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white" id="paymentTableBody">
                        @forelse($paymentLinks as $link)
                            <tr class="payment-row hover:bg-gray-50 transition-colors"
                                data-name="{{ strtolower($link->paymentMethod->name) }}"
                                data-account="{{ strtolower($link->account_number ?? '') }}">
                                {{-- Metode Pembayaran --}}
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-pink-50 to-red-50 flex items-center justify-center border border-pink-100">
                                            <i class="fas fa-building text-pink-500"></i>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $link->paymentMethod->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $link->paymentMethod->code }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Nomor Rekening --}}
                                <td class="px-6 py-3">
                                    @if($link->account_number)
                                        <span class="font-mono text-sm font-semibold text-gray-900">{{ $link->account_number }}</span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>

                                {{-- Nama Pemilik --}}
                                <td class="px-6 py-3">
                                    @if($link->account_name)
                                        <span class="text-sm text-gray-900">{{ $link->account_name }}</span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>

                                {{-- QR Code --}}
                                <td class="px-6 py-3 text-center">
                                    @if($link->qr_image)
                                        <button onclick="showQRModal('{{ asset('storage/' . $link->qr_image) }}', '{{ $link->paymentMethod->name }}')"
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-pink-50 text-pink-700 border border-pink-100 hover:bg-pink-100">
                                            <i class="fas fa-qrcode mr-1"></i>
                                            Lihat QR
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-3 whitespace-nowrap">
                                    @if($link->is_active)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1.5"></span>
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-3 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('outlet-payment-links.show', $link->id) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-gray-200 bg-white text-gray-600 hover:bg-gray-50"
                                           title="Detail">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        @can('edit metode pembayaran')
                                        <a href="{{ route('outlet-payment-links.edit', $link->id) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-yellow-200 bg-yellow-50 text-yellow-600 hover:bg-yellow-100"
                                           title="Edit">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        @endcan
                                        @can('aktifkan nonaktifkan metode pembayaran')
                                        <form action="{{ route('outlet-payment-links.toggle-status', $link->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md border bg-white hover:bg-gray-50
                                                    {{ $link->is_active ? 'text-green-600 border-green-200' : 'text-gray-600 border-gray-200' }}"
                                                    title="{{ $link->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <i class="fas fa-{{ $link->is_active ? 'toggle-on' : 'toggle-off' }} text-xs"></i>
                                            </button>
                                        </form>
                                        @endcan
                                        @can('hapus metode pembayaran')
                                        <form action="{{ route('outlet-payment-links.destroy', $link->id) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus metode pembayaran ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-red-200 bg-red-50 text-red-600 hover:bg-red-100"
                                                    title="Hapus">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-center">
                                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                            <i class="fas fa-qrcode text-3xl text-gray-300"></i>
                                        </div>
                                        <h3 class="text-base font-semibold text-gray-900 mb-1">Belum ada metode pembayaran</h3>
                                        <p class="text-sm text-gray-500 mb-4 max-w-sm">
                                            Tambahkan metode pembayaran untuk memudahkan pelanggan melakukan transaksi dengan QRIS.
                                        </p>
                                        @can('buat metode pembayaran')
                                        <a href="{{ route('outlet-payment-links.create') }}"
                                           class="inline-flex items-center gap-2 rounded-lg bg-pink-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-pink-600">
                                            <i class="fas fa-plus-circle text-xs"></i>
                                            Tambah Metode
                                        </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($paymentLinks->hasPages())
                <div class="px-4 md:px-6 py-3 border-t border-gray-200">
                    {{ $paymentLinks->links() }}
                </div>
            @endif
        </section>
    </div>
</main>

{{-- Modal untuk tampilkan QR Code --}}
<div id="qrModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="closeQRModal()"></div>
        <div class="relative bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
            <button onclick="closeQRModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h3 id="qrModalTitle" class="text-lg font-semibold text-gray-900 mb-4"></h3>
            <div class="flex justify-center">
                <img id="qrModalImage" src="" alt="QR Code" class="max-w-full h-auto rounded-lg border border-gray-200">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchPayment');
    const paymentRows = document.querySelectorAll('.payment-row');

    searchInput?.addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase();

        paymentRows.forEach(row => {
            const name = row.dataset.name || '';
            const account = row.dataset.account || '';

            const matches = name.includes(searchTerm) || account.includes(searchTerm);
            row.style.display = matches ? '' : 'none';
        });
    });
});

function showQRModal(imageUrl, title) {
    document.getElementById('qrModalImage').src = imageUrl;
    document.getElementById('qrModalTitle').textContent = title;
    document.getElementById('qrModal').classList.remove('hidden');
}

function closeQRModal() {
    document.getElementById('qrModal').classList.add('hidden');
}
</script>
@endpush
@endsection
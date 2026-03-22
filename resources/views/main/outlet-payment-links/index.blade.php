@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Metode Pembayaran QRIS - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Metode Pembayaran QRIS</span>
</li>
@endsection

@section('content')

<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Metode Pembayaran QRIS
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola metode pembayaran QRIS untuk outlet <span class="font-semibold text-cuan-green">{{ auth()->user()->outlet->name ?? 'CuanFlow' }}</span>.
                </p>
            </div>
            @can('buat metode pembayaran')
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('outlet-payment-links.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-5 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                    <span>Tambah Metode</span>
                </a>
            </div>
            @endcan
        </section>

        {{-- RINGKASAN STATISTIK --}}
        <section class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Metode</p>
                <p class="mt-2 text-2xl font-black text-gray-900">{{ $stats['total'] }}</p>
                <div class="mt-2 text-[10px] text-gray-400 font-black uppercase tracking-widest">Metode Terdaftar</div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Aktif</p>
                <p class="mt-2 text-2xl font-black text-cuan-green">{{ $stats['active'] }}</p>
                <div class="mt-2 text-[10px] text-cuan-green font-black uppercase tracking-widest">Siap Digunakan</div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Tidak Aktif</p>
                <p class="mt-2 text-2xl font-black text-gray-900">{{ $stats['inactive'] }}</p>
                <div class="mt-2 text-[10px] text-gray-400 font-black uppercase tracking-widest">Status Terhenti</div>
            </div>
        </section>

        {{-- KONTEN UTAMA: TOOLBAR + TABEL --}}
        <x-card-container>
            {{-- Toolbar: Search --}}
            <div class="px-6 py-5 border-b border-gray-100 bg-white">
                <div class="w-full md:max-w-md relative">
                    <input type="text" id="searchPayment" placeholder="Cari nama atau nomor rekening..."
                           class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all font-bold">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left">Metode Pembayaran</th>
                            <th class="px-6 py-4 text-left">Nomor Rekening / HP</th>
                            <th class="px-6 py-4 text-left">Nama Pemilik</th>
                            <th class="px-6 py-4 text-center">QR Code</th>
                            <th class="px-6 py-4 text-left">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white" id="paymentTableBody">
                        @forelse($paymentLinks as $link)
                            <tr class="payment-row hover:bg-gray-50 transition-colors"
                                data-name="{{ strtolower($link->paymentMethod->name) }}"
                                data-account="{{ strtolower($link->account_number ?? '') }}">
                                {{-- Metode Pembayaran --}}
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 flex-shrink-0 rounded-2xl bg-gradient-to-br from-cuan-green to-cuan-dark flex items-center justify-center border-2 border-white shadow-lg shadow-cuan-green/20">
                                            <i class="fas fa-wallet text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 leading-tight">{{ $link->paymentMethod->name }}</div>
                                            <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">
                                                Code: {{ $link->paymentMethod->code }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Nomor Rekening --}}
                                <td class="px-6 py-5">
                                    @if($link->account_number)
                                        <span class="font-mono text-sm font-bold text-gray-900">{{ $link->account_number }}</span>
                                    @else
                                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-300">-</span>
                                    @endif
                                </td>

                                {{-- Nama Pemilik --}}
                                <td class="px-6 py-5">
                                    @if($link->account_name)
                                        <span class="text-sm font-bold text-gray-900">{{ $link->account_name }}</span>
                                    @else
                                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-300">-</span>
                                    @endif
                                </td>

                                {{-- QR Code --}}
                                <td class="px-6 py-5 text-center">
                                    @if($link->qr_image)
                                        <button onclick="showQRModal('{{ asset('storage/' . $link->qr_image) }}', '{{ $link->paymentMethod->name }}')"
                                                class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-cuan-green/10 text-cuan-green border border-cuan-green/20 hover:bg-cuan-green hover:text-white transition-all">
                                            <i class="fas fa-qrcode mr-1.5 text-[10px]"></i>
                                            Lihat QR
                                        </button>
                                    @else
                                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-300">-</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-5 whitespace-nowrap">
                                    @if($link->is_active)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-cuan-green/10 text-cuan-green border border-cuan-green/10">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-50 text-gray-400 border border-gray-200">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-5 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('outlet-payment-links.show', $link->id) }}"
                                           class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 hover:bg-gray-100 transition-all active:scale-95"
                                           title="Detail">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>

                                        @can('edit metode pembayaran')
                                        <a href="{{ route('outlet-payment-links.edit', $link->id) }}"
                                           class="w-9 h-9 flex items-center justify-center rounded-xl bg-cuan-green/10 text-cuan-green hover:bg-cuan-green hover:text-white transition-all active:scale-95"
                                           title="Edit">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        @endcan

                                        @can('aktifkan nonaktifkan metode pembayaran')
                                        <form action="{{ route('outlet-payment-links.toggle-status', $link->id) }}" method="POST" class="inline confirm-toggle" data-name="{{ $link->paymentMethod->name }}" data-status="{{ $link->is_active ? 'nonaktifkan' : 'aktifkan' }}">
                                            @csrf
                                            <button type="submit"
                                                    class="w-9 h-9 flex items-center justify-center rounded-xl transition-all active:scale-95
                                                    {{ $link->is_active ? 'bg-gray-50 text-gray-400 hover:bg-gray-100' : 'bg-cuan-green/10 text-cuan-green hover:bg-cuan-green hover:text-white' }}"
                                                    title="{{ $link->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <i class="fas fa-{{ $link->is_active ? 'toggle-on' : 'toggle-off' }} text-xs"></i>
                                            </button>
                                        </form>
                                        @endcan

                                        @can('hapus metode pembayaran')
                                        <form action="{{ route('outlet-payment-links.destroy', $link->id) }}" method="POST" class="inline confirm-delete" data-name="{{ $link->paymentMethod->name }}">
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
                                               class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-6 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
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
                <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/30">
                    {{ $paymentLinks->links() }}
                </div>
            @endif
        </x-card-container>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    // Confirm Delete
    document.querySelectorAll('.confirm-delete').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = this.dataset.name;
            
            Swal.fire({
                title: 'Hapus Metode?',
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
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });

    // Confirm Toggle Status
    document.querySelectorAll('.confirm-toggle').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = this.dataset.name;
            const status = this.dataset.status;
            
            Swal.fire({
                title: `${status.charAt(0).toUpperCase() + status.slice(1)} Metode?`,
                text: `Apakah Anda yakin ingin ${status} metode "${name}"?`,
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
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
});

function showQRModal(imageUrl, title) {
    document.getElementById('qrModalImage').src = imageUrl;
    document.getElementById('qrModalTitle').textContent = title;
    document.getElementById('qrModal').classList.remove('hidden');
    // Using SweetAlert2 for nicer modal could be another option, but staying with current for now.
}

function closeQRModal() {
    document.getElementById('qrModal').classList.add('hidden');
}
</script>
@endpush
@endsection
@extends('layouts.app')

@section('title', 'Informasi Outlet - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Informasi Outlet</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Alert / Notifikasi --}}
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 flex items-start gap-3 text-sm">
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 flex items-start gap-3 text-sm">
                <p class="text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        {{-- HEADER HALAMAN (diseragamkan dengan halaman diskon, warna tetap kuning-oranye) --}}
        <section
            class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-yellow-100 to-orange-100 text-orange-500 border border-orange-100">
                        <i class="fas fa-store text-sm"></i>
                    </span>
                    <span>Informasi Outlet</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola daftar outlet, detail kontak, dan status operasional dengan tampilan yang rapi dan konsisten.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                @can('buat outlet')
                <a href="{{ route('outlets.create') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-br from-yellow-500 to-orange-500 px-4 py-2.5 text-sm font-semibold text-white hover:from-yellow-600 hover:to-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:ring-offset-1 shadow-sm">
                    <i class="fas fa-plus-circle text-sm"></i>
                    <span>Tambah Outlet</span>
                </a>
                @endcan
            </div>
        </section>

        {{-- RINGKASAN STATISTIK (layout mirip diskon, warna ala outlet) --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Outlet</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $outlets->total() }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-yellow-50 flex items-center justify-center border border-yellow-100">
                        <i class="fas fa-store text-yellow-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Outlet Aktif</p>
                        <p class="mt-1 text-2xl font-semibold text-green-600">{{ $outlets->where('is_active', true)->count() }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center border border-green-100">
                        <i class="fas fa-check-circle text-green-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Outlet Nonaktif</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-600">{{ $outlets->where('is_active', false)->count() }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100">
                        <i class="fas fa-times-circle text-gray-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Owner</p>
                        <p class="mt-1 text-2xl font-semibold text-orange-500">{{ $outlets->pluck('owner_id')->unique()->count() }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-orange-50 flex items-center justify-center border border-orange-100">
                        <i class="fas fa-user-tie text-orange-500 text-lg"></i>
                    </div>
                </div>
            </div>
        </section>

        {{-- KONTEN UTAMA: TOOLBAR + TABEL (layout mengikuti halaman diskon) --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            {{-- Toolbar: Search & Filter --}}
            <div class="border-b border-gray-200 px-4 md:px-6 py-4 space-y-3 md:space-y-0 md:flex md:items-center md:justify-between gap-4">
                <div class="w-full md:max-w-md">
                    <label class="text-xs font-medium text-gray-500 mb-1 block">Cari outlet</label>
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Cari berdasarkan nama outlet..."
                               class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400">
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 w-full md:w-auto">
                    <div class="w-full sm:w-40 md:w-44">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Status Outlet</label>
                        <select id="statusFilter"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400">
                            <option value="">Semua Status</option>
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Kode
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Outlet
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Alamat
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Kontak
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Owner
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Status
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($outlets as $outlet)
                            <tr class="hover:bg-gray-50 transition-colors outlet-row"
                                data-status="{{ $outlet->is_active ? 'active' : 'inactive' }}">
                                {{-- Kode --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-xs font-mono font-semibold text-gray-900 bg-gray-100 px-2 py-1 rounded">
                                        {{ $outlet->code }}
                                    </span>
                                </td>

                                {{-- Outlet + Tanggal --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($outlet->logo)
                                            <img src="{{ Storage::url($outlet->logo) }}" alt="{{ $outlet->name }}" class="h-12 w-12 rounded-lg object-cover mr-3 border-2 border-gray-200 shadow-sm">
                                        @else
                                            <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-yellow-500 to-orange-500 flex items-center justify-center mr-3 shadow-sm">
                                                <i class="fas fa-store text-white text-lg"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900 outlet-name">{{ $outlet->name }}</div>
                                            <div class="text-xs text-gray-500 flex items-center mt-1">
                                                {{ $outlet->created_at->format('d M Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Alamat --}}
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs">
                                        {{ Str::limit($outlet->address, 50) }}
                                    </div>
                                    @if($outlet->latitude && $outlet->longitude)
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ number_format($outlet->latitude, 6) }}, {{ number_format($outlet->longitude, 6) }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Kontak --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $outlet->phone ?? '-' }}
                                    </div>
                                    @if($outlet->email)
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $outlet->email }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Owner --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($outlet->owner)
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-gradient-to-br from-yellow-500 to-orange-500 flex items-center justify-center text-white font-semibold mr-2">
                                                {{ substr(auth()->user()->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $outlet->owner->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $outlet->owner->email }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($outlet->is_active)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-100">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200">
                                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        @can('lihat detail outlet')
                                        <a href="{{ route('outlets.show', $outlet->id) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-blue-200 bg-blue-50 text-blue-600 hover:bg-blue-100"
                                           title="Detail">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        @endcan
                                        @can('edit outlet')
                                        <a href="{{ route('outlets.edit', $outlet->id) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-yellow-200 bg-yellow-50 text-yellow-600 hover:bg-yellow-100"
                                           title="Edit">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        @endcan
                                        @if(auth()->user()->outlet_id === $outlet->id)
                                            <button type="button"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-gray-200 bg-gray-50 text-gray-400 cursor-not-allowed"
                                                    title="Outlet aktif tidak dapat dihapus"
                                                    disabled>
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        @else
                                            @can('hapus outlet')
                                            <form action="{{ route('outlets.destroy', $outlet->id) }}"
                                                  method="POST"
                                                  class="inline-block"
                                                  onsubmit="return confirm('Yakin ingin menghapus outlet {{ $outlet->name }}? Semua data terkait akan terhapus!')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-red-200 bg-red-50 text-red-600 hover:bg-red-100"
                                                        title="Hapus">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <span class="text-5xl text-gray-300 font-bold">!</span>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Outlet</h3>
                                        <p class="text-sm text-gray-500 mb-4">Mulai dengan menambahkan outlet pertama Anda</p>
                                        <a href="{{ route('outlets.create') }}"
                                           class="inline-flex items-center px-5 py-2.5 bg-gradient-to-br from-yellow-500 to-orange-500 text-white rounded-lg font-semibold hover:from-yellow-600 hover:to-orange-600 transition-colors">
                                            Tambah Outlet
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($outlets->hasPages())
                <div class="px-4 md:px-6 py-3 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="text-xs sm:text-sm text-gray-700">
                        Menampilkan 
                        <span class="font-semibold">{{ $outlets->firstItem() }}</span>
                        sampai
                        <span class="font-semibold">{{ $outlets->lastItem() }}</span>
                        dari
                        <span class="font-semibold">{{ $outlets->total() }}</span>
                        outlet
                    </div>
                    <div>
                        {{ $outlets->links() }}
                    </div>
                </div>
            @endif
        </section>
    </div>
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const rows = document.querySelectorAll('.outlet-row');

    function filterTable() {
        const searchTerm = (searchInput.value || '').toLowerCase();
        const statusValue = statusFilter.value;

        rows.forEach(row => {
            const nameEl = row.querySelector('.outlet-name');
            const name = nameEl ? nameEl.textContent.toLowerCase() : '';
            const status = row.dataset.status;

            const matchesSearch = !searchTerm || name.includes(searchTerm);
            const matchesStatus = !statusValue || status === statusValue;

            row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterTable);
    }
    if (statusFilter) {
        statusFilter.addEventListener('change', filterTable);
    }
});
</script>
@endpush
@endsection

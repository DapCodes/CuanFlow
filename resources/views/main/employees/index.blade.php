@extends('layouts.app')

@section('title', 'Kelola Pegawai - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Kelola Pegawai</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Alert / Notifikasi --}}
        @if(session('success'))
            <div class="rounded-lg border border-teal-200 bg-teal-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-check-circle mt-0.5 text-teal-500"></i>
                <p class="text-teal-800">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-exclamation-circle mt-0.5 text-red-500"></i>
                <p class="text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        @if(session('info'))
            <div class="rounded-lg border border-cyan-200 bg-cyan-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-info-circle mt-0.5 text-cyan-500"></i>
                <p class="text-cyan-800">{{ session('info') }}</p>
            </div>
        @endif

        {{-- HEADER HALAMAN --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-teal-50 text-teal-500 border border-teal-100">
                        <i class="fas fa-users text-sm"></i>
                    </span>
                    <span>Kelola Pegawai</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola data pegawai, hak akses, dan peran untuk operasional outlet Anda.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                <a href="{{ route('employees.create') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-teal-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:ring-offset-1">
                    <i class="fas fa-user-plus text-sm"></i>
                    <span>Tambah Pegawai</span>
                </a>
            </div>
        </section>

        {{-- RINGKASAN STATISTIK --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Pegawai</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100">
                        <i class="fas fa-users text-gray-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Aktif</p>
                        <p class="mt-1 text-2xl font-semibold text-teal-600">{{ $stats['active'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-teal-50 flex items-center justify-center border border-teal-100">
                        <i class="fas fa-user-check text-teal-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Tidak Aktif</p>
                        <p class="mt-1 text-2xl font-semibold text-red-600">{{ $stats['inactive'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center border border-red-100">
                        <i class="fas fa-user-times text-red-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Terverifikasi</p>
                        <p class="mt-1 text-2xl font-semibold text-cyan-600">{{ $stats['verified'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-cyan-50 flex items-center justify-center border border-cyan-100">
                        <i class="fas fa-check text-cyan-500 text-lg"></i>
                    </div>
                </div>
            </div>
        </section>

        {{-- KONTEN UTAMA: TOOLBAR + TABEL --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            {{-- Toolbar: Search & Filter --}}
            <div class="border-b border-gray-200 px-4 md:px-6 py-4 space-y-3 md:space-y-0 md:flex md:items-center md:justify-between gap-4">
                <div class="w-full md:max-w-md">
                    <label class="text-xs font-medium text-gray-500 mb-1 block">Cari pegawai</label>
                    <div class="relative">
                        <input type="text" id="searchEmployee" placeholder="Cari berdasarkan nama atau email..."
                               class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 w-full md:w-auto">
                    <div class="w-full sm:w-40 md:w-44">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Role</label>
                        <select id="filterRole"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400">
                            <option value="">Semua Role</option>
                            <option value="kasir">Kasir</option>
                            <option value="produksi">Produksi</option>
                            <option value="inventaris">Inventaris</option>
                        </select>
                    </div>

                    <div class="w-full sm:w-40 md:w-44">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Status</label>
                        <select id="filterStatus"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400">
                            <option value="">Semua Status</option>
                            <option value="active">Aktif</option>
                            <option value="inactive">Tidak Aktif</option>
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
                                Pegawai
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Kontak
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Role
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Permission
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Verifikasi
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white" id="employeeTableBody">
                        @forelse($employees as $employee)
                            <tr class="employee-row hover:bg-gray-50 transition-colors"
                                data-name="{{ strtolower($employee->name) }}"
                                data-email="{{ strtolower($employee->email) }}"
                                data-role="{{ $employee->roles->pluck('name')->join(',') }}"
                                data-status="{{ $employee->is_active ? 'active' : 'inactive' }}">
                                
                                {{-- Pegawai --}}
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        @if($employee->avatar)
                                            <img src="{{ Storage::url($employee->avatar) }}" alt="{{ $employee->name }}"
                                                 class="w-10 h-10 rounded-full object-cover border-2 border-gray-100">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-teal-400 to-cyan-500 flex items-center justify-center border-2 border-gray-100">
                                                <span class="text-white font-semibold text-sm">
                                                    {{ strtoupper(substr($employee->name, 0, 2)) }}
                                                </span>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $employee->name }}</div>
                                            <div class="text-xs text-gray-500">
                                                ID: #{{ str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kontak --}}
                                <td class="px-6 py-3">
                                    <div class="text-sm text-gray-900">{{ $employee->email }}</div>
                                    @if($employee->phone)
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            <i class="fas fa-phone text-[10px] mr-1"></i>
                                            {{ $employee->phone }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Role --}}
                                <td class="px-6 py-3">
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($employee->roles as $role)
                                            @php
                                                $roleColors = [
                                                    'kasir' => 'bg-cyan-50 text-cyan-700 border-cyan-100',
                                                    'produksi' => 'bg-teal-50 text-teal-700 border-teal-100',
                                                    'inventaris' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                                ];
                                                $colorClass = $roleColors[$role->name] ?? 'bg-gray-50 text-gray-700 border-gray-100';
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $colorClass }}">
                                                {{ ucfirst($role->name) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>

                                {{-- Permission --}}
                                <td class="px-6 py-3">
                                    @if($employee->permissions->count() > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($employee->permissions->take(2) as $permission)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                                    {{ str_replace('-', ' ', $permission->name) }}
                                                </span>
                                            @endforeach
                                            @if($employee->permissions->count() > 2)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                                    +{{ $employee->permissions->count() - 2 }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">Tidak ada</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-3 whitespace-nowrap">
                                    @if($employee->is_active)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-teal-50 text-teal-700 border border-teal-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-teal-500 mr-1.5"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1.5"></span>
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </td>

                                {{-- Verifikasi --}}
                                <td class="px-6 py-3 whitespace-nowrap">
                                    @if($employee->email_verified_at)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-cyan-50 text-cyan-700 border border-cyan-100">
                                            <i class="fas fa-check-circle mr-1 text-[10px]"></i>
                                            Verified
                                        </span>
                                    @else
                                        <div class="flex items-center gap-1">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">
                                                <i class="fas fa-clock mr-1 text-[10px]"></i>
                                                Pending
                                            </span>
                                            <form action="{{ route('employees.resend-verification', $employee->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        class="inline-flex items-center justify-center w-7 h-7 rounded-md border border-amber-200 bg-amber-50 text-amber-600 hover:bg-amber-100"
                                                        title="Kirim Ulang Verifikasi">
                                                    <i class="fas fa-paper-plane text-[10px]"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-3 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('employees.show', $employee->id) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-gray-200 bg-white text-gray-600 hover:bg-gray-50"
                                           title="Detail">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        <a href="{{ route('employees.edit', $employee->id) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-teal-200 bg-teal-50 text-teal-600 hover:bg-teal-100"
                                           title="Edit">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <form action="{{ route('employees.toggle-status', $employee->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md border bg-white hover:bg-gray-50
                                                    {{ $employee->is_active ? 'border-teal-200 text-teal-600' : 'border-gray-200 text-gray-600' }}"
                                                    title="{{ $employee->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <i class="fas fa-{{ $employee->is_active ? 'toggle-on' : 'toggle-off' }} text-xs"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus pegawai ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-red-200 bg-red-50 text-red-600 hover:bg-red-100"
                                                    title="Hapus">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-center">
                                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                            <i class="fas fa-users text-3xl text-gray-300"></i>
                                        </div>
                                        <h3 class="text-base font-semibold text-gray-900 mb-1">Belum ada pegawai</h3>
                                        <p class="text-sm text-gray-500 mb-4 max-w-sm">
                                            Tambahkan pegawai untuk memulai pengelolaan tim dan hak akses.
                                        </p>
                                        <a href="{{ route('employees.create') }}"
                                           class="inline-flex items-center gap-2 rounded-lg bg-teal-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-600">
                                            <i class="fas fa-user-plus text-xs"></i>
                                            Tambah Pegawai
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($employees->hasPages())
                <div class="px-4 md:px-6 py-3 border-t border-gray-200">
                    {{ $employees->links() }}
                </div>
            @endif
        </section>
    </div>
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchEmployee');
    const filterRole = document.getElementById('filterRole');
    const filterStatus = document.getElementById('filterStatus');
    const employeeRows = document.querySelectorAll('.employee-row');

    function filterEmployees() {
        const searchTerm = (searchInput.value || '').toLowerCase();
        const roleFilter = filterRole.value;
        const statusFilter = filterStatus.value;

        employeeRows.forEach(row => {
            const name = row.dataset.name || '';
            const email = row.dataset.email || '';
            const roles = row.dataset.role || '';
            const status = row.dataset.status || '';

            const matchesSearch = !searchTerm
                || name.includes(searchTerm)
                || email.includes(searchTerm);

            const matchesRole = !roleFilter || roles.includes(roleFilter);
            const matchesStatus = !statusFilter || status === statusFilter;

            row.style.display = (matchesSearch && matchesRole && matchesStatus) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterEmployees);
    filterRole.addEventListener('change', filterEmployees);
    filterStatus.addEventListener('change', filterEmployees);
});
</script>
@endpush
@endsection
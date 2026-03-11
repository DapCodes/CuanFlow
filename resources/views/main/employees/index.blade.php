@extends('layouts.app')

@section('title', 'Kelola Pegawai - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium">Kelola Pegawai</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

{{-- Notifications via SweetAlert2 will be handled by the script --}}

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Kelola Pegawai
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola data pegawai, hak akses, dan peran untuk operasional outlet Anda.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('employees.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-5 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                    <span>Tambah Pegawai</span>
                </a>
            </div>
        </section>

        {{-- RINGKASAN STATISTIK --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Pegawai</p>
                <p class="mt-2 text-2xl font-black text-gray-900">{{ number_format($stats['total'], 0, ',', '.') }}</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Aktif</p>
                <p class="mt-2 text-2xl font-black text-cuan-green">{{ number_format($stats['active'], 0, ',', '.') }}</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Tidak Aktif</p>
                <p class="mt-2 text-2xl font-black text-red-600">{{ number_format($stats['inactive'], 0, ',', '.') }}</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Terverifikasi</p>
                <p class="mt-2 text-2xl font-black text-blue-600">{{ number_format($stats['verified'], 0, ',', '.') }}</p>
            </div>
        </section>

        {{-- KONTEN UTAMA: TOOLBAR + TABEL --}}
        <x-card-container>
            {{-- Toolbar: Search & Filter --}}
            <form action="{{ route('employees.index') }}" method="GET" id="filterForm" class="px-6 py-5 border-b border-gray-100 bg-white space-y-4 md:space-y-0 md:flex md:items-center md:gap-4">
                <div class="flex-1">
                    <input type="text" name="search" id="searchEmployee" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all">
                </div>

                <div class="flex flex-wrap gap-3">
                    <select name="role" id="filterRole" onchange="this.form.submit()"
                            class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all">
                        <option value="">Semua Role</option>
                        @foreach($availableRoles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>

                    <select name="status" id="filterStatus" onchange="this.form.submit()"
                            class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
            </form>

            {{-- Tabel --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left">Pegawai</th>
                            <th class="px-6 py-4 text-left">Kontak</th>
                            <th class="px-6 py-4 text-left">Role</th>
                            <th class="px-6 py-4 text-left">Permission</th>
                            <th class="px-6 py-4 text-left">Status</th>
                            <th class="px-6 py-4 text-left">Verifikasi</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white" id="employeeTableBody">
                        @forelse($employees as $employee)
                            <tr class="employee-row hover:bg-gray-50 transition-colors"
                                data-name="{{ strtolower($employee->name) }}"
                                data-email="{{ strtolower($employee->email) }}"
                                data-role="{{ $employee->roles->pluck('name')->join(',') }}"
                                data-permissions="{{ $employee->getAllPermissions()->pluck('name')->join(',') }}"
                                data-status="{{ $employee->is_active ? 'active' : 'inactive' }}">
                                
                                {{-- Pegawai --}}
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        @if($employee->avatar)
                                            <img src="{{ Storage::url($employee->avatar) }}" alt="{{ $employee->name }}"
                                                 class="w-12 h-12 rounded-2xl object-cover border-2 border-white shadow-sm transition-transform hover:scale-110">
                                        @else
                                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-cuan-green to-cuan-dark flex items-center justify-center border-2 border-white shadow-sm">
                                                <span class="text-white font-black text-xs">
                                                    {{ strtoupper(substr($employee->name, 0, 2)) }}
                                                </span>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-bold text-gray-900 leading-tight">{{ $employee->name }}</div>
                                            <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">
                                                ID: #{{ str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kontak --}}
                                <td class="px-6 py-5">
                                    <div class="text-sm font-bold text-gray-900">{{ $employee->email }}</div>
                                    @if($employee->phone)
                                        <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">
                                            {{ $employee->phone }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Role --}}
                                <td class="px-6 py-5">
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($employee->roles as $role)
                                            @php
                                                $roleColors = [
                                                    'kasir' => 'bg-blue-50 text-blue-600 border-blue-100',
                                                    'produksi' => 'bg-cuan-green/10 text-cuan-green border-cuan-green/20',
                                                    'inventaris' => 'bg-amber-50 text-amber-600 border-amber-100',
                                                ];
                                                $colorClass = $roleColors[$role->name] ?? 'bg-gray-50 text-gray-500 border-gray-100';
                                            @endphp
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $colorClass }}">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>

                                {{-- Permission --}}
                                <td class="px-6 py-5">
                                    @php
                                        $allPermissions = $employee->getAllPermissions();
                                    @endphp
                                    @if($allPermissions->count() > 0)
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach($allPermissions->take(3) as $permission)
                                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest bg-gray-50 text-gray-400 border border-gray-100">
                                                    {{ str_replace('-', ' ', $permission->name) }}
                                                </span>
                                            @endforeach
                                            @if($allPermissions->count() > 3)
                                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest bg-gray-50 text-gray-300 border border-gray-100">
                                                    +{{ $allPermissions->count() - 3 }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-300">Kosong</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-5 whitespace-nowrap">
                                    @if($employee->is_active)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-cuan-green/10 text-cuan-green border border-cuan-green/10">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-50 text-gray-400 border border-gray-200">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                {{-- Verifikasi --}}
                                <td class="px-6 py-5 whitespace-nowrap">
                                    @if($employee->email_verified_at)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-blue-50 text-blue-500 border border-blue-100">
                                            Verified
                                        </span>
                                    @else
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-amber-50 text-amber-500 border border-amber-100">
                                                Pending
                                            </span>
                                            <form action="{{ route('employees.resend-verification', $employee->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        class="w-8 h-8 flex items-center justify-center rounded-xl border border-amber-200 bg-white text-amber-500 hover:bg-amber-50 transition-all active:scale-95"
                                                        title="Kirim Ulang Verifikasi">
                                                    <i class="fas fa-paper-plane text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-5 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="inline-flex items-center gap-2">
                                        @can('lihat detail pegawai')
                                        <a href="{{ route('employees.show', $employee->id) }}"
                                           class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 hover:bg-gray-100 transition-all active:scale-95"
                                           title="Detail">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('edit pegawai')
                                        <a href="{{ route('employees.edit', $employee->id) }}"
                                           class="w-9 h-9 flex items-center justify-center rounded-xl bg-cuan-green/10 text-cuan-green hover:bg-cuan-green hover:text-white transition-all active:scale-95"
                                           title="Edit">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        @endcan

                                        @can('aktifkan nonaktifkan pegawai')
                                        <form action="{{ route('employees.toggle-status', $employee->id) }}" method="POST" class="inline confirm-toggle" data-name="{{ $employee->name }}" data-status="{{ $employee->is_active ? 'nonaktifkan' : 'aktifkan' }}">
                                            @csrf
                                            <button type="submit"
                                                    class="w-9 h-9 flex items-center justify-center rounded-xl transition-all active:scale-95
                                                    {{ $employee->is_active ? 'bg-gray-50 text-gray-400 hover:bg-gray-100' : 'bg-cuan-green/10 text-cuan-green hover:bg-cuan-green hover:text-white' }}"
                                                    title="{{ $employee->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <i class="fas fa-{{ $employee->is_active ? 'toggle-on' : 'toggle-off' }} text-xs"></i>
                                            </button>
                                        </form>
                                        @endcan

                                        @can('hapus pegawai')
                                        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="inline confirm-delete" data-name="{{ $employee->name }}">
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
                                    <td colspan="7" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center text-center">
                                            <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                                <i class="fas fa-users text-3xl text-gray-300"></i>
                                            </div>
                                            @if(request('search') || request('role') || request('status'))
                                                <h3 class="text-base font-semibold text-gray-900 mb-1">Pencarian tidak ditemukan</h3>
                                                <p class="text-sm text-gray-500 mb-4 max-w-sm">
                                                    Tidak ada pegawai yang cocok dengan kriteria filter Anda. Silakan coba sesuaikan kata kunci atau filter Anda.
                                                </p>
                                                <a href="{{ route('employees.index') }}"
                                                   class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-6 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                                                    <i class="fas fa-undo text-xs"></i>
                                                    Reset Filter
                                                </a>
                                            @else
                                                <h3 class="text-base font-semibold text-gray-900 mb-1">Belum ada pegawai</h3>
                                                <p class="text-sm text-gray-500 mb-4 max-w-sm">
                                                    Tambahkan pegawai untuk memulai pengelolaan tim dan hak akses.
                                                </p>
                                                @can('buat pegawai')
                                                <a href="{{ route('employees.create') }}"
                                                   class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-6 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                                                    <i class="fas fa-user-plus text-xs"></i>
                                                    Tambah Pegawai
                                                </a>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($employees->hasPages())
                <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/30">
                    {{ $employees->links() }}
                </div>
            @endif
        </x-card-container>
    </div>
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchEmployee');
    const filterForm = document.getElementById('filterForm');
    let timeout = null;

    searchInput.addEventListener('keyup', function () {
        clearTimeout(timeout);
        timeout = setTimeout(function () {
            filterForm.submit();
        }, 700);
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
                title: 'Hapus Pegawai?',
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
                title: `${status.charAt(0).toUpperCase() + status.slice(1)} Pegawai?`,
                text: `Apakah Anda yakin ingin ${status} akun "${name}"?`,
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
</script>
@endpush
@endsection
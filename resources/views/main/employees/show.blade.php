@extends('layouts.app')

@section('title', 'Detail Pegawai - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('employees.index') }}" class="text-gray-500 hover:text-cuan-green transition-colors">Kelola Pegawai</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Detail Pegawai</span>
</li>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Session Flash SweetAlert
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: "{{ session('success') }}",
            confirmButtonColor: '#658C58',
            iconColor: '#658C58',
            customClass: {
                popup: 'rounded-[1.5rem] border-0',
                title: 'font-black tracking-tight',
                confirmButton: 'rounded-xl font-black uppercase text-xs tracking-widest px-6 py-3'
            }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: "{{ session('error') }}",
            confirmButtonColor: '#658C58',
            customClass: {
                popup: 'rounded-[1.5rem] border-0',
                title: 'font-black tracking-tight',
                confirmButton: 'rounded-xl font-black uppercase text-xs tracking-widest px-6 py-3'
            }
        });
    @endif

    // Handle Delete Confirmation
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const entity = this.dataset.entity || 'Data';
            const name = this.dataset.name || '';
            
            Swal.fire({
                title: 'Hapus ' + entity + '?',
                text: "Anda yakin ingin menghapus " + (name ? name : 'data ini') + "? Tindakan ini tidak dapat dibatalkan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-[1.5rem] border-0',
                    title: 'font-black tracking-tight text-gray-900',
                    confirmButton: 'rounded-xl font-black uppercase text-xs tracking-widest px-6 py-3',
                    cancelButton: 'rounded-xl font-bold uppercase text-xs tracking-widest px-6 py-3'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });

    // Handle Toggle Status Confirmation
    document.querySelectorAll('.toggle-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const action = this.dataset.status || 'ubah';
            const name = this.dataset.name || '';
            
            Swal.fire({
                title: 'Konfirmasi Akses',
                text: "Anda yakin ingin " + action + " akun " + name + "?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#658C58',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-[1.5rem] border-0',
                    title: 'font-black tracking-tight text-gray-900',
                    confirmButton: 'rounded-xl font-black uppercase text-xs tracking-widest px-6 py-3',
                    cancelButton: 'rounded-xl font-bold uppercase text-xs tracking-widest px-6 py-3'
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

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Header dengan Action Buttons --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="flex items-center gap-6">
                @if($employee->avatar)
                    <img src="{{ Storage::url($employee->avatar) }}" alt="{{ $employee->name }}"
                         class="w-20 h-20 rounded-[2rem] object-cover border-4 border-white shadow-xl">
                @else
                    <div class="w-20 h-20 rounded-[2rem] bg-gradient-to-br from-cuan-green to-cuan-dark flex items-center justify-center border-4 border-white shadow-xl">
                        <span class="text-white font-black text-2xl">
                            {{ strtoupper(substr($employee->name, 0, 2)) }}
                        </span>
                    </div>
                @endif
                <div>
                    <h1 class="text-2xl md:text-3xl font-black text-gray-900 tracking-tight">{{ $employee->name }}</h1>
                    <div class="flex items-center gap-3 mt-2">
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">ID: #{{ str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}</span>
                        @if($employee->is_active)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-cuan-green/10 text-cuan-green border border-cuan-green/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-cuan-green mr-2 animate-pulse"></span>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-100 text-gray-400 border border-gray-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-2"></span>
                                Tidak Aktif
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('employees.index') }}"
                   class="px-5 py-3 border border-gray-200 bg-white text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-50 transition-all active:scale-95">
                    Kembali
                </a>
                <a href="{{ route('employees.edit', $employee->id) }}"
                   class="px-5 py-3 bg-cuan-green text-white rounded-xl font-black text-sm hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                    Edit Data
                </a>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Kolom Kiri: Informasi Pribadi --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Informasi Kontak --}}
                <x-card-container>
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Informasi Kontak</h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Detail komunikasi pegawai</p>
                    </div>
                    <div class="px-8 py-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center border border-gray-100">
                                <i class="fas fa-envelope text-cuan-green text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Email</p>
                                <p class="text-sm font-bold text-gray-900">{{ $employee->email }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center border border-gray-100">
                                <i class="fas fa-phone text-blue-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">No. Telepon</p>
                                <p class="text-sm font-bold text-gray-900">{{ $employee->phone ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center border border-gray-100">
                                <i class="fas fa-store text-amber-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Outlet</p>
                                <p class="text-sm font-bold text-gray-900">{{ $employee->outlet->name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </x-card-container>

                {{-- Role & Permission --}}
                <x-card-container>
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Role & Hak Akses</h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Pengaturan wewenang sistem</p>
                    </div>
                    <div class="px-8 py-8 space-y-8">
                        {{-- Roles --}}
                        <div>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4">Role Pegawai</p>
                            <div class="flex flex-wrap gap-3">
                                @forelse($employee->roles as $role)
                                    <span class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest bg-cuan-green/10 text-cuan-green border border-cuan-green/20">
                                        {{ $role->name }}
                                    </span>
                                @empty
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest italic">Tidak ada role terpilih</span>
                                @endforelse
                            </div>
                        </div>

                        {{-- Permissions --}}
                        <div>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4">Hak Akses Aktif (Direct & Inherited)</p>
                            @if($employee->permissions->count() > 0)
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach($employee->permissions as $permission)
                                        <div class="flex items-center gap-3 px-4 py-3 rounded-[1.5rem] bg-gray-50/50 border border-gray-100">
                                            <div class="w-2 h-2 rounded-full bg-cuan-green shadow-[0_0_8px_rgba(101,140,88,0.5)]"></div>
                                            <span class="text-[10px] font-black text-gray-900 uppercase tracking-widest">{{ str_replace('-', ' ', $permission->name) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest italic">Tidak ada permission tambahan</span>
                            @endif
                        </div>
                    </div>
                </x-card-container>
            </div>

            {{-- Kolom Kanan: Info Tambahan --}}
            <div class="space-y-6">
                {{-- Status & Verifikasi --}}
                <x-card-container>
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Status Akun</h2>
                    </div>
                    <div class="px-8 py-8 space-y-6">
                        <div>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3">Status Aktif</p>
                            @if($employee->is_active)
                                <span class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest bg-cuan-green/10 text-cuan-green border border-cuan-green/20">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest bg-red-100 text-red-500 border border-red-200">
                                    Tidak Aktif
                                </span>
                            @endif
                        </div>

                        <div>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3">Verifikasi Email</p>
                            @if($employee->email_verified_at)
                                <span class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest bg-blue-50 text-blue-500 border border-blue-100">
                                    Terverifikasi
                                </span>
                            @else
                                <div class="space-y-4">
                                    <span class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest bg-amber-50 text-amber-500 border border-amber-100">
                                        Belum Verifikasi
                                    </span>
                                    <form action="{{ route('employees.resend-verification', $employee->id) }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit"
                                                class="w-full px-4 py-3 text-[10px] font-black uppercase bg-amber-500 text-white rounded-xl hover:bg-amber-600 active:scale-95 transition-all shadow-md shadow-amber-500/20">
                                            Kirim Ulang Email
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </x-card-container>

                {{-- Timeline --}}
                <x-card-container>
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Timeline</h2>
                    </div>
                    <div class="px-8 py-8 space-y-6">
                        <div class="flex items-center justify-between">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Bergabung</span>
                            <span class="text-xs font-bold text-gray-900">{{ $employee->created_at->format('d M Y') }}</span>
                        </div>

                        @if($employee->email_verified_at)
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Verifikasi</span>
                                <span class="text-xs font-bold text-gray-900">{{ $employee->email_verified_at->format('d M Y') }}</span>
                            </div>
                        @endif

                        @if($employee->last_login_at)
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Login Terakhir</span>
                                <span class="text-xs font-bold text-gray-900">{{ $employee->last_login_at->diffForHumans() }}</span>
                            </div>
                        @endif

                        <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Update Terakhir</span>
                            <span class="text-xs font-bold text-gray-900">{{ $employee->updated_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </x-card-container>

                {{-- Quick Actions --}}
                <x-card-container>
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Aksi Cepat</h2>
                    </div>
                    <div class="px-8 py-8 space-y-3">
                        <form action="{{ route('employees.toggle-status', $employee->id) }}" method="POST" class="w-full toggle-form"
                              data-entity="Pegawai" data-name="{{ $employee->name }}" data-status="{{ $employee->is_active ? 'nonaktifkan' : 'aktifkan' }}">
                            @csrf
                            <button type="submit"
                                    class="w-full px-5 py-4 text-[10px] font-black uppercase tracking-widest border {{ $employee->is_active ? 'border-red-100 bg-red-50 text-red-500 hover:bg-red-100' : 'border-cuan-green/20 bg-cuan-green/5 text-cuan-green hover:bg-cuan-green/10' }} rounded-2xl transition-all active:scale-95">
                                {{ $employee->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
                            </button>
                        </form>

                        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="w-full delete-form"
                              data-entity="Pegawai" data-name="{{ $employee->name }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full px-5 py-4 text-[10px] font-black uppercase tracking-widest bg-red-500 text-white rounded-2xl hover:bg-red-600 transition-all active:scale-95 shadow-lg shadow-red-500/20">
                                Hapus Pegawai
                            </button>
                        </form>
                    </div>
                </x-card-container>
            </div>
        </div>
    </div>
</main>
@endsection
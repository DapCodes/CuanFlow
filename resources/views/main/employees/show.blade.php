@extends('layouts.app')

@section('title', 'Detail Pegawai - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('employees.index') }}" class="text-gray-500 hover:text-gray-700">Kelola Pegawai</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Detail Pegawai</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Header dengan Action Buttons --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                @if($employee->avatar)
                    <img src="{{ Storage::url($employee->avatar) }}" alt="{{ $employee->name }}"
                         class="w-16 h-16 rounded-full object-cover border-2 border-teal-100">
                @else
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-teal-400 to-cyan-500 flex items-center justify-center border-2 border-teal-100">
                        <span class="text-white font-semibold text-xl">
                            {{ strtoupper(substr($employee->name, 0, 2)) }}
                        </span>
                    </div>
                @endif
                <div>
                    <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ $employee->name }}</h1>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs text-gray-500">ID: #{{ str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}</span>
                        @if($employee->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-teal-50 text-teal-700 border border-teal-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-teal-500 mr-1"></span>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1"></span>
                                Tidak Aktif
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('employees.edit', $employee->id) }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-teal-200 bg-teal-50 px-4 py-2 text-sm font-semibold text-teal-600 hover:bg-teal-100">
                    <i class="fas fa-edit text-xs"></i>
                    Edit
                </a>
                <a href="{{ route('employees.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-arrow-left text-xs"></i>
                    Kembali
                </a>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Kolom Kiri: Informasi Pribadi --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Informasi Kontak --}}
                <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-address-card text-teal-500"></i>
                            Informasi Kontak
                        </h2>
                    </div>
                    <div class="px-6 py-5 space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-cyan-50 flex items-center justify-center border border-cyan-100">
                                <i class="fas fa-envelope text-cyan-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 mb-0.5">Email</p>
                                <p class="text-sm font-medium text-gray-900">{{ $employee->email }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-teal-50 flex items-center justify-center border border-teal-100">
                                <i class="fas fa-phone text-teal-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 mb-0.5">No. Telepon</p>
                                <p class="text-sm font-medium text-gray-900">{{ $employee->phone ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100">
                                <i class="fas fa-store text-emerald-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 mb-0.5">Outlet</p>
                                <p class="text-sm font-medium text-gray-900">{{ $employee->outlet->name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Role & Permission --}}
                <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-shield-alt text-teal-500"></i>
                            Role & Hak Akses
                        </h2>
                    </div>
                    <div class="px-6 py-5 space-y-5">
                        {{-- Roles --}}
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-2">Role</p>
                            <div class="flex flex-wrap gap-2">
                                @forelse($employee->roles as $role)
                                    @php
                                        $roleColors = [
                                            'kasir' => 'bg-cyan-50 text-cyan-700 border-cyan-100',
                                            'produksi' => 'bg-teal-50 text-teal-700 border-teal-100',
                                            'inventaris' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                        ];
                                        $colorClass = $roleColors[$role->name] ?? 'bg-gray-50 text-gray-700 border-gray-100';
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold border {{ $colorClass }}">
                                        <i class="fas fa-user-tag mr-1.5 text-xs"></i>
                                        {{ ucfirst($role->name) }}
                                    </span>
                                @empty
                                    <span class="text-sm text-gray-400">Tidak ada role</span>
                                @endforelse
                            </div>
                        </div>

                        {{-- Permissions --}}
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-2">Permission</p>
                            @if($employee->permissions->count() > 0)
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($employee->permissions as $permission)
                                        <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-50 border border-gray-100">
                                            <i class="fas fa-check-circle text-teal-500 text-xs"></i>
                                            <span class="text-sm text-gray-700">{{ str_replace('-', ' ', ucfirst($permission->name)) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-sm text-gray-400">Tidak ada permission tambahan</span>
                            @endif
                        </div>
                    </div>
                </section>
            </div>

            {{-- Kolom Kanan: Info Tambahan --}}
            <div class="space-y-6">
                {{-- Status & Verifikasi --}}
                <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-info-circle text-teal-500"></i>
                            Status Akun
                        </h2>
                    </div>
                    <div class="px-6 py-5 space-y-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1.5">Status Aktif</p>
                            @if($employee->is_active)
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold bg-teal-50 text-teal-700 border border-teal-100">
                                    <i class="fas fa-check-circle mr-1.5"></i>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold bg-gray-50 text-gray-700 border border-gray-200">
                                    <i class="fas fa-times-circle mr-1.5"></i>
                                    Tidak Aktif
                                </span>
                            @endif
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 mb-1.5">Verifikasi Email</p>
                            @if($employee->email_verified_at)
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold bg-cyan-50 text-cyan-700 border border-cyan-100">
                                    <i class="fas fa-shield-check mr-1.5"></i>
                                    Terverifikasi
                                </span>
                            @else
                                <div class="space-y-2">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold bg-amber-50 text-amber-700 border border-amber-100">
                                        <i class="fas fa-clock mr-1.5"></i>
                                        Belum Verifikasi
                                    </span>
                                    <form action="{{ route('employees.resend-verification', $employee->id) }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit"
                                                class="w-full inline-flex items-center justify-center gap-2 rounded-lg border border-amber-200 bg-amber-50 text-amber-600 hover:bg-amber-100 px-3 py-2 text-xs font-semibold">
                                            <i class="fas fa-paper-plane text-[10px]"></i>
                                            Kirim Ulang Email
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </section>

                {{-- Timeline --}}
                <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-clock text-teal-500"></i>
                            Timeline
                        </h2>
                    </div>
                    <div class="px-6 py-5 space-y-4">
                        <div>
                            <p class="text-xs text-gray-500">Bergabung</p>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">
                                {{ $employee->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>

                        @if($employee->email_verified_at)
                            <div>
                                <p class="text-xs text-gray-500">Email Diverifikasi</p>
                                <p class="text-sm font-medium text-gray-900 mt-0.5">
                                    {{ $employee->email_verified_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                        @endif

                        @if($employee->last_login_at)
                            <div>
                                <p class="text-xs text-gray-500">Login Terakhir</p>
                                <p class="text-sm font-medium text-gray-900 mt-0.5">
                                    {{ $employee->last_login_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                        @endif

                        <div>
                            <p class="text-xs text-gray-500">Terakhir Diperbarui</p>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">
                                {{ $employee->updated_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>
                </section>

                {{-- Quick Actions --}}
                <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-900">Aksi Cepat</h2>
                    </div>
                    <div class="px-6 py-4 space-y-2">
                        <form action="{{ route('employees.toggle-status', $employee->id) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 rounded-lg border {{ $employee->is_active ? 'border-red-200 bg-red-50 text-red-600 hover:bg-red-100' : 'border-teal-200 bg-teal-50 text-teal-600 hover:bg-teal-100' }} px-4 py-2.5 text-sm font-semibold">
                                <i class="fas fa-{{ $employee->is_active ? 'times-circle' : 'check-circle' }} text-xs"></i>
                                {{ $employee->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
                            </button>
                        </form>

                        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="w-full"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus pegawai ini? Tindakan ini tidak dapat dibatalkan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2.5 text-sm font-semibold">
                                <i class="fas fa-trash text-xs"></i>
                                Hapus Pegawai
                            </button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
</main>
@endsection
@extends('layouts.app')

@section('title', 'Tambah Pegawai - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

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
    <span class="text-gray-900 font-medium">Tambah Pegawai</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-4xl mx-auto">
        
        <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Header --}}
            <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5">
                <div class="flex items-center gap-3 mb-1">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-teal-50 text-teal-500 border border-teal-100">
                        <i class="fas fa-user-plus text-sm"></i>
                    </span>
                    <h1 class="text-xl font-semibold text-gray-900">Tambah Pegawai Baru</h1>
                </div>
                <p class="text-sm text-gray-500 ml-12">
                    Lengkapi formulir di bawah untuk menambahkan pegawai baru ke outlet Anda.
                </p>
            </section>

            {{-- Informasi Pribadi --}}
            <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">Informasi Pribadi</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Data pribadi dan kontak pegawai</p>
                </div>
                <div class="px-6 py-5 space-y-5">
                    {{-- Avatar --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto Profil</label>
                        <div class="flex items-start gap-4">
                            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-teal-400 to-cyan-500 flex items-center justify-center border-2 border-gray-100" id="avatarPreview">
                                <i class="fas fa-user text-white text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <input type="file" name="avatar" id="avatar" accept="image/*"
                                       class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400">
                                <p class="mt-1 text-xs text-gray-500">PNG, JPG maksimal 2MB</p>
                                @error('avatar')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Nama --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 @error('name') border-red-300 @enderror"
                               placeholder="Masukkan nama lengkap">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 @error('email') border-red-300 @enderror"
                                   placeholder="email@example.com">
                            <p class="mt-1 text-xs text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                Email verifikasi akan dikirim otomatis setelah pegawai ditambahkan
                            </p>
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">
                                No. Telepon
                            </label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 @error('phone') border-red-300 @enderror"
                                   placeholder="08xxxxxxxxxx">
                            @error('phone')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Password --}}
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password" id="password" required
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 @error('password') border-red-300 @enderror"
                                   placeholder="Minimal 8 karakter">
                            @error('password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password Confirmation --}}
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Konfirmasi Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400"
                                   placeholder="Ulangi password">
                        </div>
                    </div>
                </div>
            </section>

            {{-- Role & Permission --}}
            <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">Role & Hak Akses</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Tentukan role dan permission untuk pegawai</p>
                </div>
                <div class="px-6 py-5 space-y-5">
                    {{-- Roles --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            @foreach($roles as $role)
                                @php
                                    $roleColors = [
                                        'kasir' => 'peer-checked:bg-cyan-50 peer-checked:border-cyan-400 peer-checked:text-cyan-700',
                                        'produksi' => 'peer-checked:bg-teal-50 peer-checked:border-teal-400 peer-checked:text-teal-700',
                                        'inventaris' => 'peer-checked:bg-emerald-50 peer-checked:border-emerald-400 peer-checked:text-emerald-700',
                                    ];
                                    $colorClass = $roleColors[$role->name] ?? 'peer-checked:bg-gray-50 peer-checked:border-gray-400';
                                @endphp
                                <label class="relative cursor-pointer">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="peer sr-only"
                                           {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}>
                                    <div class="block p-4 border-2 border-gray-200 rounded-lg hover:border-gray-300 {{ $colorClass }} transition-all">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-user-tag"></i>
                                            <span class="font-semibold">{{ ucfirst($role->name) }}</span>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('roles')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Permissions --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Permission Tambahan <span class="text-xs text-gray-500">(Opsional)</span>
                        </label>
                        <div class="border border-gray-200 rounded-lg p-4 max-h-64 overflow-y-auto">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($permissions as $permission)
                                    <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-colors">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                               class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500"
                                               {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                        <span class="text-sm text-gray-700">{{ str_replace('-', ' ', ucfirst($permission->name)) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        @error('permissions')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- Status --}}
            <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">Status Akun</h2>
                </div>
                <div class="px-6 py-5">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="w-5 h-5 text-teal-600 border-gray-300 rounded focus:ring-teal-500"
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <div>
                            <span class="text-sm font-medium text-gray-900">Aktifkan akun</span>
                            <p class="text-xs text-gray-500">Pegawai dapat login dan mengakses sistem</p>
                        </div>
                    </label>
                </div>
            </section>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('employees.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-times text-xs"></i>
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-teal-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:ring-offset-1">
                    <i class="fas fa-save text-xs"></i>
                    Simpan Pegawai
                </button>
            </div>
        </form>
    </div>
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatarPreview');

    avatarInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                avatarPreview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover rounded-full">`;
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush
@endsection
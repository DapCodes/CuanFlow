@extends('layouts.app')

@section('title', 'Edit Pegawai - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('employees.index') }}" class="text-gray-500 hover:text-cuan-green transition-colors">Kelola Pegawai</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Edit Pegawai</span>
</li>
@endsection

@push('styles')
<style>
    .role-card {
        transition: all 0.2s ease;
    }
    .role-card:hover {
        transform: translateY(-2px);
    }
    .role-card.selected {
        transform: scale(1.02);
    }
    .permission-item {
        transition: all 0.15s ease;
    }
    .permission-item:hover {
        background-color: #f8fafc;
    }
    .category-section {
        scroll-margin-top: 1rem;
    }
    .permission-checkbox:checked + .permission-label {
        background-color: #f0fdfa;
        border-color: #14b8a6;
    }
    .search-highlight {
        background-color: #fef08a;
        padding: 0 2px;
        border-radius: 2px;
    }
    .hidden-by-search {
        display: none !important;
    }
</style>
@endpush

@section('content')
<main class="flex-grow py-2 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">
        
        <form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Header --}}
            <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-xl md:text-2xl font-black text-gray-900">
                        Edit Pegawai
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Perbarui informasi pegawai {{ $employee->name }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('employees.index') }}"
                    class="px-5 py-3 border border-gray-200 bg-white text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-50 transition-all active:scale-95">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-5 py-3 bg-cuan-green text-white rounded-xl font-black text-sm hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                        Simpan Perubahan
                    </button>
                </div>
            </section>

            {{-- Informasi Pribadi --}}
            <x-card-container>
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Informasi Pribadi</h2>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Data pribadi dan kontak pegawai</p>
                </div>
                <div class="px-8 py-8 space-y-8">
                    {{-- Avatar --}}
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4">Foto Profil</label>
                        <div class="flex items-center gap-6">
                            <div class="w-24 h-24 rounded-3xl bg-gray-50 flex items-center justify-center border-2 border-dashed border-gray-200 overflow-hidden group hover:border-cuan-green transition-all" id="avatarPreview">
                                @if($employee->avatar)
                                    <img src="{{ Storage::url($employee->avatar) }}" alt="{{ $employee->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-cuan-green to-cuan-dark flex items-center justify-center">
                                        <span class="text-white font-black text-xl">{{ strtoupper(substr($employee->name, 0, 2)) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" name="avatar" id="avatar" accept="image/*"
                                       class="block w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-cuan-green/10 file:text-cuan-green hover:file:bg-cuan-green/20">
                                <p class="mt-2 text-[10px] text-gray-400 font-medium">Format: PNG, JPG (Maks. 2MB). Kosongkan jika tidak ingin mengubah.</p>
                                @error('avatar')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Nama --}}
                    <div>
                        <label for="name" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $employee->name) }}" required
                               class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all @error('name') border-red-300 @enderror"
                               placeholder="Masukkan nama lengkap">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email', $employee->email) }}" required
                                   class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all @error('email') border-red-300 @enderror"
                                   placeholder="email@example.com">
                            <p class="mt-2 text-[9px] text-amber-600 font-bold uppercase tracking-widest">
                                Jika email diubah, verifikasi akan direset.
                            </p>
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label for="phone" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                No. Telepon
                            </label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $employee->phone) }}"
                                   class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all @error('phone') border-red-300 @enderror"
                                   placeholder="08xxxxxxxxxx">
                            @error('phone')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Password --}}
                        <div>
                            <label for="password" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                Password Baru <span class="text-[9px] lowercase font-bold">(Kosongkan jika tidak berubah)</span>
                            </label>
                            <input type="password" name="password" id="password"
                                   class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all @error('password') border-red-300 @enderror"
                                   placeholder="Minimal 8 karakter">
                            @error('password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password Confirmation --}}
                        <div>
                            <label for="password_confirmation" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                Konfirmasi Password
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all"
                                   placeholder="Ulangi password">
                        </div>
                    </div>
                </div>
            </x-card-container>

            {{-- Role --}}
            <x-card-container>
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Pilih Role</h2>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Role menentukan set permission dasar pegawai.</p>
                </div>
                <div class="px-8 py-8">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        @php
                            $roleConfig = [
                                'supervisor' => [
                                    'icon' => 'fa-user-tie',
                                    'color' => 'indigo',
                                    'description' => 'Pengawas'
                                ],
                                'kasir' => [
                                    'icon' => 'fa-cash-register',
                                    'color' => 'blue',
                                    'description' => 'Akses Kasir'
                                ],
                                'produksi' => [
                                    'icon' => 'fa-flask',
                                    'color' => 'cuan-green',
                                    'description' => 'Produksi'
                                ],
                                'inventaris' => [
                                    'icon' => 'fa-boxes-stacked',
                                    'color' => 'amber',
                                    'description' => 'Kelola Stok'
                                ],
                                'supplier' => [
                                    'icon' => 'fa-truck-field',
                                    'color' => 'orange',
                                    'description' => 'Pemasok'
                                ],
                                'reseller' => [
                                    'icon' => 'fa-handshake',
                                    'color' => 'emerald',
                                    'description' => 'Reseller'
                                ],
                            ];
                        @endphp
                        
                        @foreach($roles as $role)
                            @php
                                $config = $roleConfig[$role->name] ?? [
                                    'icon' => 'fa-user-tag',
                                    'color' => 'gray',
                                    'description' => 'Role Pegawai'
                                ];
                                $isChecked = in_array($role->name, old('roles', $employeeRoles));
                            @endphp
                            <label class="role-card relative cursor-pointer group" data-role="{{ $role->name }}">
                                <input type="checkbox" name="roles[]" value="{{ $role->name }}" 
                                       class="peer sr-only role-checkbox"
                                       data-role-name="{{ $role->name }}"
                                       {{ $isChecked ? 'checked' : '' }}>
                                <div class="block p-5 border border-gray-100 rounded-[2rem] bg-gray-50/50
                                            peer-checked:border-cuan-green peer-checked:bg-cuan-green/5 
                                            hover:bg-white hover:shadow-xl hover:shadow-gray-200 transition-all duration-300">
                                    <div class="flex flex-col items-center text-center gap-3">
                                        <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                                            <i class="fas {{ $config['icon'] }} 
                                                {{ $config['color'] === 'cuan-green' ? 'text-cuan-green' : 'text-'.$config['color'].'-500' }} text-lg"></i>
                                        </div>
                                        <div>
                                            <span class="text-xs font-black text-gray-900 uppercase tracking-widest block">{{ $role->name }}</span>
                                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest hidden sm:block mt-1">{{ $config['description'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('roles')
                        <p class="mt-4 text-[10px] font-black uppercase text-red-500 tracking-widest">{{ $message }}</p>
                    @enderror
                </div>
            </x-card-container>

            {{-- Permissions --}}
            <x-card-container>
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Permission / Hak Akses</h2>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Sesuaikan hak akses spesifik untuk pegawai ini.</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            {{-- Search --}}
                            <input type="text" id="permissionSearch" placeholder="Cari hak akses..."
                                   class="px-4 py-2 text-xs font-bold bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all">
                            {{-- Toggle All --}}
                            <button type="button" id="toggleAllPermissions" 
                                    class="px-4 py-2 text-[10px] font-black uppercase bg-white border border-gray-200 text-gray-400 rounded-xl hover:bg-gray-50 active:scale-95 transition-all">
                                Pilih Semua
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="px-8 py-6 max-h-[500px] overflow-y-auto scrollbar-hide" id="permissionsContainer">
                    {{-- Stats Bar --}}
                    <div class="flex items-center justify-between mb-6 p-4 bg-cuan-green/5 border border-cuan-green/10 rounded-2xl">
                        <div class="flex items-center gap-6">
                            <span class="text-[10px] font-black uppercase tracking-widest text-cuan-green">
                                Terpilih: <span id="selectedCount" class="text-sm ml-1">0</span>
                            </span>
                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">
                                Total: <span class="text-sm ml-1 text-gray-900">{{ $permissionCategories->sum(fn($c) => $c->permissions->count()) }}</span>
                            </span>
                        </div>
                        <button type="button" id="clearAllPermissions" 
                                class="text-[10px] font-black uppercase tracking-widest text-red-400 hover:text-red-500 transition-colors">
                            Hapus Semua
                        </button>
                    </div>

                    {{-- Permission Categories --}}
                    <div class="space-y-4" id="permissionsList">
                        @foreach($permissionCategories as $category)
                            @if($category->permissions->count() > 0)
                                <div class="category-section border border-gray-100 rounded-[2rem] overflow-hidden" data-category="{{ $category->slug }}">
                                    {{-- Category Header --}}
                                    <div class="flex items-center justify-between px-6 py-4 bg-gray-50 cursor-pointer category-toggle hover:bg-gray-100 transition-colors"
                                         data-category-id="{{ $category->id }}">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shadow-sm">
                                                <i class="{{ $category->icon }} text-sm" style="color: {{ $category->color }};"></i>
                                            </div>
                                            <div>
                                                <span class="text-xs font-black text-gray-900 uppercase tracking-widest">{{ $category->name }}</span>
                                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-2">
                                                    (<span class="category-selected-count" data-category="{{ $category->id }}">0</span>/{{ $category->permissions->count() }})
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <button type="button" class="select-all-category text-[9px] font-black uppercase tracking-widest text-cuan-green hover:underline"
                                                    data-category="{{ $category->id }}">
                                                Pilih Semua
                                            </button>
                                            <i class="fas fa-chevron-down text-[10px] text-gray-300 transition-transform category-chevron"></i>
                                        </div>
                                    </div>
                                    
                                    {{-- Permissions Grid --}}
                                    <div class="p-6 bg-white border-t border-gray-100 category-content" data-category-id="{{ $category->id }}">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                            @foreach($category->permissions as $permission)
                                                <label class="permission-item relative flex items-center gap-3 p-4 rounded-2xl border border-gray-50 bg-gray-50/30 hover:bg-white hover:border-cuan-green/30 hover:shadow-sm cursor-pointer transition-all"
                                                       data-permission-name="{{ $permission->name }}">
                                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                           class="permission-checkbox peer sr-only"
                                                           data-category="{{ $category->id }}"
                                                           {{ in_array($permission->name, old('permissions', $employeeAllPermissions)) ? 'checked' : '' }}>
                                                    
                                                    <div class="w-5 h-5 rounded-md border-2 border-gray-200 flex items-center justify-center bg-white peer-checked:bg-cuan-green peer-checked:border-cuan-green transition-all">
                                                        <i class="fas fa-check text-[10px] text-white opacity-0 peer-checked:opacity-100"></i>
                                                    </div>

                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center gap-2 overflow-hidden">
                                                            <span class="text-xs font-black uppercase tracking-widest text-gray-900 block truncate group-hover:text-cuan-green">
                                                                {{ str_replace('-', ' ', $permission->name) }}
                                                            </span>
                                                            @if(in_array($permission->name, $employeeAllPermissions) && !in_array($permission->name, $employeeDirectPermissions))
                                                                <span class="flex-shrink-0 px-1.5 py-0.5 rounded-lg text-[7px] font-black uppercase tracking-widest bg-gray-100 text-gray-400">Role</span>
                                                            @elseif(in_array($permission->name, $employeeDirectPermissions))
                                                                <span class="flex-shrink-0 px-1.5 py-0.5 rounded-lg text-[7px] font-black uppercase tracking-widest bg-amber-50 text-amber-500">Custom</span>
                                                            @endif
                                                        </div>
                                                        @if($permission->description)
                                                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest block truncate mt-1">{{ $permission->description }}</span>
                                                        @endif
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </x-card-container>
        {{-- No Results --}}
                    <div id="noSearchResults" class="hidden py-8 text-center text-gray-500">
                        <i class="fas fa-search text-3xl mb-2 text-gray-300"></i>
                        <p>Tidak ada permission yang ditemukan</p>
                    </div>
                </div>
                
                @error('permissions')
                    <div class="px-6 pb-4">
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    </div>
                @enderror
            </section>

            {{-- Status --}}
            <x-card-container>
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Status Akun</h2>
                </div>
                <div class="px-8 py-8">
                    <label class="flex items-center gap-4 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" name="is_active" value="1" 
                                   {{ old('is_active', $employee->is_active) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cuan-green"></div>
                        </div>
                        <div>
                            <span class="text-xs font-black text-gray-900 uppercase tracking-widest">Aktifkan akun</span>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Pegawai dapat login dan mengakses sistem setelah verifikasi email.</p>
                        </div>
                    </label>
                </div>
            </x-card-container>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-4 pb-8">
                <a href="{{ route('employees.index') }}"
                   class="px-8 py-4 bg-white border border-gray-200 text-gray-600 rounded-2xl font-bold text-sm hover:bg-gray-50 transition-all active:scale-95">
                    Batal
                </a>
                <button type="submit"
                        class="px-8 py-4 bg-cuan-green text-white rounded-2xl font-black text-sm hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</main>

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

    // Role permissions mapping from server
    const rolePermissions = @json($rolePermissions);
    
    // Elements
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatarPreview');
    const roleCheckboxes = document.querySelectorAll('.role-checkbox');
    const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
    const searchInput = document.getElementById('permissionSearch');
    const toggleAllBtn = document.getElementById('toggleAllPermissions');
    const clearAllBtn = document.getElementById('clearAllPermissions');
    const selectedCountEl = document.getElementById('selectedCount');
    const permissionsList = document.getElementById('permissionsList');
    const noSearchResults = document.getElementById('noSearchResults');
    
    // Avatar preview
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
    
    // Role selection - auto check permissions
    roleCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const roleName = this.dataset.roleName;
            const permissions = rolePermissions[roleName] || [];
            
            if (this.checked) {
                // Check all permissions for this role
                permissions.forEach(permName => {
                    const permCheckbox = document.querySelector(`.permission-checkbox[value="${permName}"]`);
                    if (permCheckbox) {
                        permCheckbox.checked = true;
                    }
                });
            } else {
                // Get all checked roles
                const checkedRoles = Array.from(document.querySelectorAll('.role-checkbox:checked'))
                    .map(cb => cb.dataset.roleName);
                
                // Get all permissions that should remain checked
                const remainingPermissions = new Set();
                checkedRoles.forEach(role => {
                    (rolePermissions[role] || []).forEach(p => remainingPermissions.add(p));
                });
                
                // Uncheck permissions that are not in remaining roles
                permissions.forEach(permName => {
                    if (!remainingPermissions.has(permName)) {
                        const permCheckbox = document.querySelector(`.permission-checkbox[value="${permName}"]`);
                        if (permCheckbox) {
                            permCheckbox.checked = false;
                        }
                    }
                });
            }
            
            updateCounts();
        });
    });
    
    // Permission checkbox change
    permissionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateCounts);
    });
    
    // Update selected counts
    function updateCounts() {
        const total = document.querySelectorAll('.permission-checkbox:checked').length;
        selectedCountEl.textContent = total;
        
        // Update category counts
        document.querySelectorAll('.category-selected-count').forEach(el => {
            const categoryId = el.dataset.category;
            const count = document.querySelectorAll(`.permission-checkbox[data-category="${categoryId}"]:checked`).length;
            el.textContent = count;
        });
    }
    
    // Search functionality
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        let hasVisibleItems = false;
        
        document.querySelectorAll('.category-section').forEach(category => {
            let categoryHasVisible = false;
            
            category.querySelectorAll('.permission-item').forEach(item => {
                const permName = item.dataset.permissionName.toLowerCase();
                const isMatch = query === '' || permName.includes(query);
                
                if (isMatch) {
                    item.classList.remove('hidden-by-search');
                    categoryHasVisible = true;
                    hasVisibleItems = true;
                } else {
                    item.classList.add('hidden-by-search');
                }
            });
            
            // Show/hide entire category
            if (categoryHasVisible) {
                category.classList.remove('hidden-by-search');
            } else {
                category.classList.add('hidden-by-search');
            }
        });
        
        // Show/hide no results message
        if (hasVisibleItems || query === '') {
            noSearchResults.classList.add('hidden');
            permissionsList.classList.remove('hidden');
        } else {
            noSearchResults.classList.remove('hidden');
            permissionsList.classList.add('hidden');
        }
    });
    
    // Toggle all permissions
    let allSelected = false;
    toggleAllBtn.addEventListener('click', function() {
        allSelected = !allSelected;
        permissionCheckboxes.forEach(cb => {
            if (!cb.closest('.hidden-by-search')) {
                cb.checked = allSelected;
            }
        });
        this.innerHTML = allSelected 
            ? '<i class="fas fa-times mr-1"></i> Batalkan Semua'
            : '<i class="fas fa-check-double mr-1"></i> Pilih Semua';
        updateCounts();
    });
    
    // Clear all permissions
    clearAllBtn.addEventListener('click', function() {
        permissionCheckboxes.forEach(cb => cb.checked = false);
        roleCheckboxes.forEach(cb => cb.checked = false);
        allSelected = false;
        toggleAllBtn.innerHTML = '<i class="fas fa-check-double mr-1"></i> Pilih Semua';
        updateCounts();
    });
    
    // Select all in category
    document.querySelectorAll('.select-all-category').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const categoryId = this.dataset.category;
            const categoryCheckboxes = document.querySelectorAll(`.permission-checkbox[data-category="${categoryId}"]`);
            const allChecked = Array.from(categoryCheckboxes).every(cb => cb.checked);
            
            categoryCheckboxes.forEach(cb => cb.checked = !allChecked);
            this.textContent = allChecked ? 'Pilih Semua' : 'Batalkan';
            updateCounts();
        });
    });
    
    // Category toggle (collapse/expand)
    document.querySelectorAll('.category-toggle').forEach(toggle => {
        toggle.addEventListener('click', function() {
            const categoryId = this.dataset.categoryId;
            const content = document.querySelector(`.category-content[data-category-id="${categoryId}"]`);
            const chevron = this.querySelector('.category-chevron');
            
            content.classList.toggle('hidden');
            chevron.classList.toggle('rotate-180');
        });
    });
    
    // Initial count
    updateCounts();
});
</script>
@endpush
@endsection
@extends('layouts.app')

@section('title', 'Edit Pegawai - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

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
    <span class="text-gray-900 font-medium">Edit Pegawai</span>
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
            <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-teal-50 text-teal-500 border border-teal-100">
                            <i class="fas fa-users text-sm"></i>
                        </span>
                        <span>Edit Data Pegawai</span>
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Perbarui informasi pegawai {{ $employee->name }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                    <a href="{{ route('employees.index') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-teal-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:ring-offset-1">
                        <i class="fas fa-arrow-left text-sm"></i>
                        <span>Kembali</span>
                    </a>
                </div>
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
                            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-teal-400 to-cyan-500 flex items-center justify-center border-2 border-gray-100 overflow-hidden" id="avatarPreview">
                                @if($employee->avatar)
                                    <img src="{{ Storage::url($employee->avatar) }}" alt="{{ $employee->name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-white font-semibold text-lg">{{ strtoupper(substr($employee->name, 0, 2)) }}</span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" name="avatar" id="avatar" accept="image/*"
                                       class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400">
                                <p class="mt-1 text-xs text-gray-500">PNG, JPG maksimal 2MB. Kosongkan jika tidak ingin mengubah.</p>
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
                        <input type="text" name="name" id="name" value="{{ old('name', $employee->name) }}" required
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
                            <input type="email" name="email" id="email" value="{{ old('email', $employee->email) }}" required
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 @error('email') border-red-300 @enderror"
                                   placeholder="email@example.com">
                            <p class="mt-1 text-xs text-amber-600 flex items-start gap-1.5">
                                <i class="fas fa-exclamation-triangle mt-0.5"></i>
                                <span>Jika email diubah, verifikasi akan direset dan email verifikasi baru akan dikirim</span>
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
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $employee->phone) }}"
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
                                Password Baru <span class="text-xs text-gray-500">(Opsional)</span>
                            </label>
                            <input type="password" name="password" id="password"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 @error('password') border-red-300 @enderror"
                                   placeholder="Minimal 8 karakter">
                            <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengubah password</p>
                            @error('password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password Confirmation --}}
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Konfirmasi Password
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400"
                                   placeholder="Ulangi password">
                        </div>
                    </div>
                </div>
            </section>

            {{-- Role --}}
            <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">Pilih Role</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Pilih satu atau lebih role untuk pegawai. Permission akan otomatis terisi sesuai role.</p>
                </div>
                <div class="px-6 py-5">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @php
                            $roleConfig = [
                                'supervisor' => [
                                    'icon' => 'fa-user-tie',
                                    'color' => 'purple',
                                    'bgGradient' => 'from-purple-500 to-indigo-500',
                                    'description' => 'Pengawas operasional'
                                ],
                                'kasir' => [
                                    'icon' => 'fa-cash-register',
                                    'color' => 'cyan',
                                    'bgGradient' => 'from-cyan-500 to-blue-500',
                                    'description' => 'Akses kasir & transaksi'
                                ],
                                'produksi' => [
                                    'icon' => 'fa-flask',
                                    'color' => 'teal',
                                    'bgGradient' => 'from-teal-500 to-emerald-500',
                                    'description' => 'Kelola produksi'
                                ],
                                'inventaris' => [
                                    'icon' => 'fa-boxes-stacked',
                                    'color' => 'amber',
                                    'bgGradient' => 'from-amber-500 to-orange-500',
                                    'description' => 'Kelola stok & gudang'
                                ],
                                'supplier' => [
                                    'icon' => 'fa-truck-field',
                                    'color' => 'orange',
                                    'bgGradient' => 'from-orange-500 to-amber-600',
                                    'description' => 'Akses pemasok / vendor'
                                ],
                                'reseller' => [
                                    'icon' => 'fa-handshake',
                                    'color' => 'emerald',
                                    'bgGradient' => 'from-emerald-500 to-teal-600',
                                    'description' => 'Akses anggota reseller'
                                ],
                            ];
                        @endphp
                        
                        @foreach($roles as $role)
                            @php
                                $config = $roleConfig[$role->name] ?? [
                                    'icon' => 'fa-user-tag',
                                    'color' => 'gray',
                                    'bgGradient' => 'from-gray-500 to-gray-600',
                                    'description' => 'Role pegawai'
                                ];
                                $isChecked = in_array($role->name, old('roles', $employeeRoles));
                            @endphp
                            <label class="role-card relative cursor-pointer group" data-role="{{ $role->name }}">
                                <input type="checkbox" name="roles[]" value="{{ $role->name }}" 
                                       class="peer sr-only role-checkbox"
                                       data-role-name="{{ $role->name }}"
                                       {{ $isChecked ? 'checked' : '' }}>
                                <div class="block p-4 border-2 border-gray-200 rounded-xl 
                                            peer-checked:border-{{ $config['color'] }}-400 
                                            peer-checked:bg-{{ $config['color'] }}-50 
                                            hover:border-gray-300 transition-all">
                                    <div class="flex flex-col items-center text-center gap-2">
                                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $config['bgGradient'] }} flex items-center justify-center shadow-lg">
                                            <i class="fas {{ $config['icon'] }} text-white text-lg"></i>
                                        </div>
                                        <div>
                                            <span class="font-semibold text-gray-900 block">{{ ucfirst($role->name) }}</span>
                                            <span class="text-xs text-gray-500 hidden sm:block">{{ $config['description'] }}</span>
                                        </div>
                                    </div>
                                    {{-- Checkmark --}}
                                    <div class="absolute top-2 right-2 w-5 h-5 rounded-full border-2 border-gray-300 
                                                peer-checked:border-{{ $config['color'] }}-500 peer-checked:bg-{{ $config['color'] }}-500 
                                                flex items-center justify-center transition-all">
                                        <i class="fas fa-check text-white text-xs opacity-0 peer-checked:opacity-100"></i>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('roles')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            {{-- Permissions --}}
            <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h2 class="text-base font-semibold text-gray-900">Permission / Hak Akses</h2>
                            <p class="text-xs text-gray-500 mt-0.5">Centang permission untuk mengatur akses pegawai</p>
                        </div>
                        <div class="flex items-center gap-2">
                            {{-- Search --}}
                            <div class="relative">
                                <input type="text" id="permissionSearch" placeholder="Cari permission..."
                                       class="w-48 sm:w-64 pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            </div>
                            {{-- Toggle All --}}
                            <button type="button" id="toggleAllPermissions" 
                                    class="px-3 py-2 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                <i class="fas fa-check-double mr-1"></i>
                                Pilih Semua
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 max-h-[500px] overflow-y-auto" id="permissionsContainer">
                    {{-- Stats Bar --}}
                    <div class="flex items-center justify-between mb-4 p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-4 text-sm">
                            <span class="text-gray-600">
                                <i class="fas fa-check-circle text-teal-500 mr-1"></i>
                                Terpilih: <strong id="selectedCount">0</strong>
                            </span>
                            <span class="text-gray-600">
                                <i class="fas fa-list text-gray-400 mr-1"></i>
                                Total: <strong>{{ $permissionCategories->sum(fn($c) => $c->permissions->count()) }}</strong>
                            </span>
                        </div>
                        <button type="button" id="clearAllPermissions" 
                                class="text-xs text-red-500 hover:text-red-700 font-medium">
                            <i class="fas fa-times mr-1"></i>
                            Hapus Semua
                        </button>
                    </div>

                    {{-- Permission Categories --}}
                    <div class="space-y-4" id="permissionsList">
                        @foreach($permissionCategories as $category)
                            @if($category->permissions->count() > 0)
                                <div class="category-section border border-gray-200 rounded-lg overflow-hidden" data-category="{{ $category->slug }}">
                                    {{-- Category Header --}}
                                    <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-200 cursor-pointer category-toggle"
                                         data-category-id="{{ $category->id }}">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg flex items-center justify-center" 
                                                 style="background-color: {{ $category->color }}20;">
                                                <i class="{{ $category->icon }}" style="color: {{ $category->color }};"></i>
                                            </div>
                                            <div>
                                                <span class="font-semibold text-gray-900 text-sm">{{ $category->name }}</span>
                                                <span class="text-xs text-gray-500 ml-2">
                                                    (<span class="category-selected-count" data-category="{{ $category->id }}">0</span>/{{ $category->permissions->count() }})
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button type="button" class="select-all-category px-2 py-1 text-xs font-medium text-teal-600 hover:bg-teal-50 rounded transition-colors"
                                                    data-category="{{ $category->id }}">
                                                Pilih Semua
                                            </button>
                                            <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform category-chevron"></i>
                                        </div>
                                    </div>
                                    
                                    {{-- Permissions Grid --}}
                                    <div class="p-3 category-content" data-category-id="{{ $category->id }}">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                            @foreach($category->permissions as $permission)
                                                @php
                                                    // Cek apakah permission ini dimiliki (dari role atau direct)
                                                    $isFromRole = false;
                                                    foreach ($employeeRoles as $roleName) {
                                                        if (in_array($permission->name, $rolePermissions[$roleName] ?? [])) {
                                                            $isFromRole = true;
                                                            break;
                                                        }
                                                    }
                                                    $isDirect = in_array($permission->name, $employeeDirectPermissions);
                                                    $isChecked = $isFromRole || $isDirect || in_array($permission->name, old('permissions', []));
                                                @endphp
                                                <label class="permission-item flex items-center gap-2.5 p-2.5 rounded-lg border border-transparent hover:border-gray-200 cursor-pointer transition-all"
                                                       data-permission-name="{{ $permission->name }}">
                                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                           class="permission-checkbox w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500 focus:ring-offset-0"
                                                           data-category="{{ $category->id }}"
                                                           {{ $isChecked ? 'checked' : '' }}>
                                                    <div class="flex-1 min-w-0">
                                                        <span class="permission-label text-sm text-gray-700 block truncate">
                                                            {{ ucfirst($permission->name) }}
                                                        </span>
                                                        @if($permission->description)
                                                            <span class="text-xs text-gray-400 block truncate">{{ $permission->description }}</span>
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
            <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">Status Akun</h2>
                </div>
                <div class="px-6 py-5">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="w-5 h-5 text-teal-600 border-gray-300 rounded focus:ring-teal-500"
                               {{ old('is_active', $employee->is_active) ? 'checked' : '' }}>
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
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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
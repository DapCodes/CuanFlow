@extends('admin.layouts.app')

@section('title', 'Edit Role')
@section('page-title', 'Edit Role')

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <a href="{{ route('admin.roles.index') }}" class="text-gray-500 hover:text-gray-700">Roles</a>
</li>
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <span class="text-gray-700">Edit</span>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.roles.update', $role) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Edit Role: {{ ucfirst($role->name) }}</h2>
                <p class="text-sm text-gray-500 mt-1">Perbarui informasi dan permission role</p>
            </div>
            <a href="{{ route('admin.roles.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </a>
        </div>
        
        <!-- Form Card -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <!-- Role Name -->
            <div class="p-6 border-b border-gray-200">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Role <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="{{ old('name', $role->name) }}"
                       required
                       @if(in_array($role->name, ['admin', 'owner'])) readonly @endif
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green focus:border-cuan-green @error('name') border-red-300 @enderror @if(in_array($role->name, ['admin', 'owner'])) bg-gray-100 @endif"
                       placeholder="Contoh: manager">
                @if(in_array($role->name, ['admin', 'owner']))
                <p class="mt-2 text-sm text-amber-600">
                    <i class="fas fa-info-circle mr-1"></i>
                    Nama role sistem tidak dapat diubah
                </p>
                @endif
                @error('name')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Permissions -->
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Permissions</label>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ count($rolePermissions) }} dari {{ count($permissions) }} permission dipilih
                        </p>
                    </div>
                    <button type="button" 
                            onclick="toggleAllPermissions()"
                            class="text-sm text-cuan-dark hover:text-cuan-green font-medium">
                        Toggle Semua
                    </button>
                </div>
                
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 max-h-80 overflow-y-auto p-4 bg-gray-50 rounded-lg">
                    @foreach($permissions as $permission)
                    <label class="flex items-center gap-2 p-2 rounded hover:bg-white cursor-pointer">
                        <input type="checkbox" 
                               name="permissions[]" 
                               value="{{ $permission->name }}"
                               class="permission-checkbox w-4 h-4 text-cuan-green border-gray-300 rounded focus:ring-cuan-green"
                               {{ in_array($permission->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">{{ $permission->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.roles.index') }}" 
               class="px-4 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </a>
            @can('edit roles')
            <button type="submit" 
                    class="px-6 py-2.5 bg-cuan-dark text-white font-semibold rounded-lg hover:bg-cuan-green transition-colors">
                <i class="fas fa-save mr-2"></i>
                Simpan Perubahan
            </button>
            @else
            <button type="button" disabled
                    class="px-6 py-2.5 bg-gray-300 text-white font-semibold rounded-lg cursor-not-allowed">
                <i class="fas fa-lock mr-2"></i>
                Tidak Memiliki Izin
            </button>
            @endcan
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleAllPermissions() {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
}
</script>
@endpush
@endsection

@extends('admin.layouts.app')

@section('title', 'Kelola Permissions')
@section('page-title', 'Data Master - Permissions')

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <span class="text-gray-700">Permissions</span>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Kelola Permissions</h2>
            <p class="text-sm text-gray-500 mt-1">Total {{ $totalPermissions }} permission dalam {{ $permissionCategories->count() }} kategori</p>
        </div>
        @can('kelola permissions')
        <a href="{{ route('admin.permissions.create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-cuan-dark text-white font-semibold rounded-lg hover:bg-cuan-green transition-colors">
            <i class="fas fa-plus text-sm"></i>
            <span>Tambah Permission</span>
        </a>
        @endcan
    </div>
    
    <!-- Permission Categories -->
    <div class="space-y-4">
        @foreach($permissionCategories as $category)
        @if($category->permissions->count() > 0)
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden" x-data="{ open: true }">
            <!-- Category Header -->
            <button @click="open = !open" 
                    class="w-full flex items-center justify-between px-6 py-4 bg-gray-50 hover:bg-gray-100 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" 
                         style="background-color: {{ $category->color }}20;">
                        <i class="{{ $category->icon }}" style="color: {{ $category->color }};"></i>
                    </div>
                    <div class="text-left">
                        <p class="font-semibold text-gray-900">{{ $category->name }}</p>
                        <p class="text-xs text-gray-500">{{ $category->permissions->count() }} permissions</p>
                    </div>
                </div>
                <i class="fas fa-chevron-down text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>
            </button>
            
            <!-- Permissions List -->
            <div x-show="open" x-transition class="border-t border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-4">
                    @foreach($category->permissions as $permission)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ $permission->name }}</p>
                            @if($permission->description)
                            <p class="text-xs text-gray-500 truncate">{{ $permission->description }}</p>
                            @endif
                        </div>
                        @can('kelola permissions')
                        <div class="flex items-center gap-1 ml-2">
                            <a href="{{ route('admin.permissions.edit', $permission) }}" 
                               class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition-colors">
                                <i class="fas fa-edit text-sm"></i>
                            </a>
                            <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus permission ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded transition-colors">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                        @endcan
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        @endforeach
    </div>
</div>
@endsection

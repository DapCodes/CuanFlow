@extends('admin.layouts.app')

@section('title', 'Kelola Roles')
@section('page-title', 'Data Master - Roles')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Roles</span>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-user-shield text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Kelola Roles</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium">Pengaturan peran dan pembatasan hak akses sistem</p>
            </div>
        </div>
        @can('buat roles')
        <div>
            <a href="{{ route('admin.roles.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-emerald-600 transition-all duration-200 shadow-sm hover:shadow-emerald-200/50">
                <i class="fas fa-plus text-xs"></i>
                <span>Tambah Role Baru</span>
            </a>
        </div>
        @endcan
    </div>
    
    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Guard</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Permissions</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($roles as $role)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-cuan-olive to-cuan-green flex items-center justify-center">
                                    <i class="fas fa-user-shield text-white"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ ucfirst($role->name) }}</p>
                                    <p class="text-xs text-gray-500">ID: {{ $role->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">
                                {{ $role->guard_name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 text-sm font-semibold bg-cuan-yellow/30 text-cuan-dark rounded-full">
                                {{ $role->permissions_count }} permissions
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                @can('edit roles')
                                <a href="{{ route('admin.roles.edit', $role) }}" 
                                   class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @if(!in_array($role->name, ['admin', 'owner']))
                                @can('hapus roles')
                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" 
                                      onsubmit="return confirm('Yakin ingin menghapus role ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                                @else
                                <span class="p-2 text-gray-300 cursor-not-allowed" title="Role sistem tidak bisa dihapus">
                                    <i class="fas fa-lock"></i>
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                            <p>Belum ada role</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($roles->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $roles->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@extends('admin.layouts.app')

@section('title', 'Manajemen Role')
@section('page-title', 'Manajemen Role')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Roles</span>
</li>
@endsection

@section('content')
<div class="px-4 lg:px-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-user-shield text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight uppercase">Kelola Roles</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium italic">Pengaturan peran dan pembatasan hak akses sistem</p>
            </div>
        </div>
        @can('buat roles')
        <div class="flex-shrink-0">
            <a href="{{ route('admin.roles.create') }}" 
               class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 text-white text-sm font-black uppercase tracking-widest rounded-xl hover:bg-emerald-600 transition-all duration-300 shadow-md hover:shadow-emerald-200/50 active:scale-95">
                <i class="fas fa-plus text-[10px]"></i>
                <span>Tambah Role</span>
            </a>
        </div>
        @endcan
    </div>

    {{-- RINGKASAN STATISTIK --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Roles --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Total Role</p>
                    <p class="mt-1 text-2xl font-black text-gray-900">{{ number_format($stats['total_roles']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100">
                    <i class="fas fa-id-badge text-gray-400 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Total Permissions --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Total Perms</p>
                    <p class="mt-1 text-2xl font-black text-blue-600">{{ number_format($stats['total_permissions']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center border border-blue-100 shadow-sm shadow-blue-100/50">
                    <i class="fas fa-key text-blue-500 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Without Permissions --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">No Perms</p>
                    <p class="mt-1 text-2xl font-black text-red-600">{{ number_format($stats['roles_without_permissions']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center border border-red-100 shadow-sm shadow-red-100/50">
                    <i class="fas fa-exclamation-circle text-red-500 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Total Guards --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Web Guard</p>
                    <p class="mt-1 text-2xl font-black text-emerald-600">{{ number_format($stats['total_guards']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100 shadow-sm shadow-emerald-100/50">
                    <i class="fas fa-shield-alt text-emerald-500 text-lg"></i>
                </div>
            </div>
        </div>
    </section>

    {{-- KONTEN UTAMA: TOOLBAR + TABEL --}}
    <x-card-container class="!p-0 overflow-hidden border border-gray-200 shadow-sm bg-white rounded-xl">
        {{-- Toolbar: Search --}}
        <div class="border-b border-gray-200 px-4 md:px-6 py-5 bg-gray-50/50">
            <form action="{{ route('admin.roles.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:items-center md:justify-between gap-4">
                <div class="w-full md:max-w-xs">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2 block italic">Cari Nama Role / Guard</label>
                    <div class="relative group">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: Admin..."
                               class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all duration-300">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-emerald-500 transition-colors text-xs"></i>
                    </div>
                </div>

                <div class="flex flex-wrap items-end gap-3 w-full md:w-auto">
                    <div class="w-full sm:w-40">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2 block italic">Filter Guard</label>
                        <select name="guard" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition-all duration-300">
                            <option value="">Semua Guard</option>
                            <option value="web" {{ request('guard') == 'web' ? 'selected' : '' }}>Web</option>
                            <option value="api" {{ request('guard') == 'api' ? 'selected' : '' }}>API</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition-all shadow-md shadow-gray-200 active:scale-95 group">
                            <i class="fas fa-search group-hover:rotate-12 transition-transform"></i>
                        </button>
                        @if(request()->anyFilled(['search', 'guard']))
                            <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white border border-gray-200 text-gray-400 hover:bg-gray-50 hover:text-red-500 transition-all shadow-sm active:scale-95" title="Reset">
                                <i class="fas fa-redo-alt text-sm"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left">Role</th>
                        <th class="px-6 py-4 text-left">Guard</th>
                        <th class="px-6 py-4 text-center">Permissions</th>
                        <th class="px-6 py-4 text-center font-black">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($roles as $role)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 border border-emerald-100 shadow-sm">
                                    <i class="fas fa-user-shield text-xs"></i>
                                </div>
                                <div>
                                    <p class="font-black text-gray-900 leading-tight uppercase tracking-tight">{{ $role->name }}</p>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1 italic">DIBUAT: {{ $role->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-gray-100 text-gray-600 border border-gray-200">
                                {{ $role->guard_name }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-[11px] font-black text-gray-900">{{ number_format($role->permissions_count) }}</span>
                                <span class="text-[8px] font-black uppercase tracking-tightest text-gray-400">Permissions</span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center justify-center gap-2">
                                @can('edit roles')
                                <a href="{{ route('admin.roles.edit', $role) }}" 
                                   class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-600 hover:text-white shadow-sm transition-all active:scale-95 border border-blue-100" 
                                   title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                @endcan
                                
                                @if(!in_array($role->name, ['admin', 'owner']))
                                    @can('hapus roles')
                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" 
                                          onsubmit="return confirm('Yakin ingin menghapus role ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-600 hover:text-white shadow-sm transition-all active:scale-95 border border-red-100" 
                                                title="Hapus">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                    @endcan
                                @else
                                    <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-50 text-gray-300 border border-gray-100" title="Role Sistem">
                                        <i class="fas fa-lock text-xs"></i>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gray-50 border border-dashed border-gray-200 rounded-full flex items-center justify-center mb-6">
                                    <i class="fas fa-user-tag text-gray-200 text-3xl"></i>
                                </div>
                                <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">Role Tidak Ditemukan</h3>
                                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-widest mt-2 max-w-xs mx-auto italic">
                                    {{ request('search') ? 'Coba sesuaikan kata kunci pencarian Anda.' : 'Daftar role masih kosong.' }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($roles->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $roles->links() }}
        </div>
        @endif
    </x-card-container>
</div>
@endsection


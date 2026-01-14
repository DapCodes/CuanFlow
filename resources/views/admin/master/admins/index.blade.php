@extends('admin.layouts.app')

@section('title', 'Kelola Admin')
@section('page-title', 'Data Master - Admin')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Admins</span>
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
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Kelola Admin</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium">Manajemen pengguna dengan hak akses Administrator Sistem</p>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.admins.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-emerald-600 transition-all duration-200 shadow-sm hover:shadow-emerald-200/50">
                <i class="fas fa-plus text-xs"></i>
                <span>Tambah Admin Baru</span>
            </a>
        </div>
    </div>
    
    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Administrator</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Email & Verifikasi</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($admins as $admin)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold border border-emerald-200">
                                    {{ substr($admin->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">{{ $admin->name }}</p>
                                    <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">System Administrator</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-gray-700">{{ $admin->email }}</p>
                            <div class="flex items-center gap-1.5 mt-1">
                                <i class="fas fa-check-circle text-[10px] text-emerald-500"></i>
                                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Verified System Admin</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($admin->is_active)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-red-50 text-red-700 border border-red-100">
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.admins.edit', $admin) }}" 
                                   class="w-8 h-8 flex items-center justify-center text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                   title="Edit Admin">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                @if($admin->id !== auth()->id())
                                <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus admin ini? Tindakan ini tidak dapat dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Hapus Admin">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                                @else
                                <span class="w-8 h-8 flex items-center justify-center text-gray-300 cursor-not-allowed" title="Tidak dapat menghapus diri sendiri">
                                    <i class="fas fa-lock text-sm"></i>
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-user-shield text-4xl text-gray-200 mb-3"></i>
                                <p class="font-medium">Belum ada data admin</p>
                                <p class="text-xs text-gray-400 mt-1">Gunakan tombol tambah untuk menambah administrator baru</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($admins->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $admins->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

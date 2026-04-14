@extends('admin.layouts.app')

@section('title', 'Manajemen Karir')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Karir</span>
</li>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-briefcase text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Manajemen Karir</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium">Kelola informasi lowongan pekerjaan</p>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.careers.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 border border-transparent text-white text-sm font-semibold rounded-xl hover:bg-gray-800 transition-all duration-200 shadow-sm">
                <i class="fas fa-plus text-xs"></i>
                <span>Tambah Lowongan</span>
            </a>
        </div>
    </div>

    {{-- RINGKASAN STATISTIK --}}
    <section class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {{-- Total Lowongan --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Total Lowongan</p>
                <p class="mt-1 text-2xl font-black text-gray-900">{{ number_format($stats['total']) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100 shadow-sm">
                <i class="fas fa-briefcase text-gray-400 text-lg"></i>
            </div>
        </div>

        {{-- Dibuka / Aktif --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Aktif (Dibuka)</p>
                <p class="mt-1 text-2xl font-black text-emerald-600">{{ number_format($stats['active']) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-check-square text-emerald-500 text-lg"></i>
            </div>
        </div>

        {{-- Ditutup / Nonaktif --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Ditutup</p>
                <p class="mt-1 text-2xl font-black text-red-500">{{ number_format($stats['inactive']) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center border border-red-100 shadow-sm shadow-red-100/50">
                <i class="fas fa-times-circle text-red-500 text-lg"></i>
            </div>
        </div>
    </section>

    <x-card-container class="!p-0 border border-gray-200 shadow-sm overflow-hidden">
        {{-- Toolbar: Search & Filter --}}
        <div class="border-b border-gray-200 px-4 md:px-6 py-5 bg-gray-50/50">
            <form action="{{ route('admin.careers.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:items-center md:justify-between gap-4">
                <div class="w-full md:max-w-xs">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2 block italic">Cari Posisi / Lokasi</label>
                    <div class="relative group">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik posisi..."
                               class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all duration-300">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-emerald-500 transition-colors text-xs"></i>
                    </div>
                </div>

                <div class="flex flex-wrap items-end gap-3 w-full md:w-auto">
                    <div class="w-full sm:w-48">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2 block italic">Status Lowongan</label>
                        <select name="status" class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition-all duration-300">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif (Dibuka)</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif (Ditutup)</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition-all shadow-md shadow-gray-200 active:scale-95 group">
                            <i class="fas fa-search group-hover:rotate-12 transition-transform"></i>
                        </button>
                        @if(request()->anyFilled(['status', 'search']))
                            <a href="{{ route('admin.careers.index') }}" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white border border-gray-200 text-gray-400 hover:bg-gray-50 hover:text-red-500 transition-all shadow-sm active:scale-95" title="Reset">
                                <i class="fas fa-redo-alt text-sm"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-[11px] text-gray-500 uppercase font-bold border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4">Status & Tanggal</th>
                        <th class="px-6 py-4">Posisi</th>
                        <th class="px-6 py-4">Lokasi & Tipe</th>
                        <th class="px-6 py-4">Batas Akhir</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($careers as $career)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <form action="{{ route('admin.careers.toggle-status', $career) }}" method="POST" class="inline-block cursor-pointer mb-2">
                                @csrf
                                <button type="submit" class="focus:outline-none transition-all">
                                    <div class="relative w-10 h-5 bg-gray-200 rounded-full transition-colors duration-300 {{ $career->is_active ? 'bg-emerald-500' : 'bg-gray-300' }}">
                                        <div class="absolute top-[2px] left-[2px] bg-white w-4 h-4 rounded-full transition-transform duration-300 shadow-sm {{ $career->is_active ? 'translate-x-[20px]' : 'translate-x-0' }}"></div>
                                    </div>
                                </button>
                            </form>
                            <p class="text-[10px] text-gray-400 uppercase tracking-tighter">{{ $career->created_at->isoFormat('D MMM Y') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-gray-900 truncate">{{ $career->title }}</p>
                            <p class="text-xs text-gray-500 mt-1 truncate">{{ Str::limit($career->salary_range, 25) ?? 'Gaji dirahasiakan' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 mb-1">
                                <i class="fas fa-map-marker-alt text-gray-400 text-[11px]"></i>
                                <span class="text-xs font-semibold text-gray-700">{{ $career->location }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-clock text-gray-400 text-[11px]"></i>
                                <span class="text-[11px] text-gray-500 font-medium bg-gray-100 px-2 rounded">{{ $career->type }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($career->deadline)
                                <p class="text-xs font-bold {{ $career->deadline->isPast() ? 'text-red-500' : 'text-gray-900' }}">
                                    {{ $career->deadline->format('d M Y') }}
                                </p>
                            @else
                                <p class="text-xs text-gray-400 font-medium">Buka Terus</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.careers.edit', $career) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-all" 
                                   title="Edit Lowongan">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                
                                <form action="{{ route('admin.careers.destroy', $career) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lowongan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center justify-center w-8 h-8 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-all" 
                                            title="Hapus Lowongan">
                                        <i class="fas fa-trash-alt text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-dashed border-gray-200">
                                    <i class="fas fa-briefcase text-2xl text-gray-300"></i>
                                </div>
                                <p class="text-gray-500 font-medium">Belum ada lowongan pekerjaan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($careers->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $careers->links() }}
        </div>
        @endif
    </x-card-container>
</div>
@endsection

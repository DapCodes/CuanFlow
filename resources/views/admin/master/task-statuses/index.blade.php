@extends('admin.layouts.app')

@section('title', 'Status Tugas')
@section('page-title', 'Alur Kerja Kanban')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Status Tugas</span>
</li>
@endsection

@section('content')
<div class="px-4 lg:px-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-tasks text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight uppercase">Status Tugas</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium italic">Manajemen tahapan alur kerja untuk papan Kanban sistem</p>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.task-statuses.create') }}" 
               class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 text-white text-sm font-black uppercase tracking-widest rounded-xl hover:bg-emerald-600 transition-all duration-300 shadow-md hover:shadow-emerald-200/50 active:scale-95">
                <i class="fas fa-plus text-[10px]"></i>
                <span>Tambah Status</span>
            </a>
        </div>
    </div>

    {{-- RINGKASAN STATISTIK --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Statuses --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Total Status</p>
                    <p class="mt-1 text-2xl font-black text-gray-900">{{ number_format($stats['total_statuses']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100">
                    <i class="fas fa-list-ol text-gray-400 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Used Statuses --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Status Aktif</p>
                    <p class="mt-1 text-2xl font-black text-emerald-600">{{ number_format($stats['used_statuses']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100 shadow-sm shadow-emerald-100/50">
                    <i class="fas fa-stream text-emerald-500 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Empty Statuses --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Status Kosong</p>
                    <p class="mt-1 text-2xl font-black text-amber-600">{{ number_format($stats['empty_statuses']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center border border-amber-100 shadow-sm shadow-amber-100/50">
                    <i class="fas fa-folder-open text-amber-500 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Recent --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Baru (7 Hari)</p>
                    <p class="mt-1 text-2xl font-black text-blue-600">{{ number_format($stats['recent']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center border border-blue-100 shadow-sm shadow-blue-100/50">
                    <i class="fas fa-layer-group text-blue-500 text-lg"></i>
                </div>
            </div>
        </div>
    </section>

    {{-- KONTEN UTAMA: TOOLBAR + TABEL --}}
    <x-card-container class="!p-0 overflow-hidden border border-gray-200 shadow-sm bg-white rounded-xl">
        {{-- Toolbar: Search --}}
        <div class="border-b border-gray-200 px-4 md:px-6 py-5 bg-gray-50/50">
            <form action="{{ route('admin.task-statuses.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:items-center md:justify-between gap-4">
                <div class="w-full md:max-w-xs">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2 block italic">Cari Nama Status</label>
                    <div class="relative group">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: To Do / Done..."
                               class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all duration-300">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-emerald-500 transition-colors text-xs"></i>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition-all shadow-md shadow-gray-200 active:scale-95 group">
                        <i class="fas fa-search group-hover:rotate-12 transition-transform"></i>
                    </button>
                    @if(request()->anyFilled(['search']))
                        <a href="{{ route('admin.task-statuses.index') }}" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white border border-gray-200 text-gray-400 hover:bg-gray-50 hover:text-red-500 transition-all shadow-sm active:scale-95" title="Reset">
                            <i class="fas fa-redo-alt text-sm"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left">Nama Status</th>
                        <th class="px-6 py-4 text-left">Representasi Warna</th>
                        <th class="px-6 py-4 text-center">Urutan Tampilan</th>
                        <th class="px-6 py-4 text-center">Tugas Terkait</th>
                        <th class="px-6 py-4 text-center font-black">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($statuses as $status)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-11 h-11 rounded-xl flex items-center justify-center shadow-sm" style="background-color: {{ $status->color }}15; border: 1px solid {{ $status->color }}30;">
                                    <i class="fas fa-clipboard-list text-[10px]" style="color: {{ $status->color }}"></i>
                                </div>
                                <div>
                                    <p class="font-black text-gray-900 leading-tight uppercase tracking-tight">{{ $status->name }}</p>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-emerald-500 mt-1 italic">Stage Workflow</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <span class="w-5 h-5 rounded-lg shadow-sm border border-black/10" style="background-color: {{ $status->color }}"></span>
                                <span class="text-[11px] font-black font-mono text-gray-500 uppercase tracking-widest bg-gray-100 px-2 py-1 rounded-md">{{ $status->color }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-[11px] font-black text-gray-700 border border-gray-200">
                                {{ $status->order }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-[11px] font-black text-gray-900">{{ number_format($status->tasks_count ?? 0) }}</span>
                                <span class="text-[8px] font-black uppercase tracking-widest text-gray-400">Tasks</span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.task-statuses.edit', $status) }}" 
                                   class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-600 hover:text-white shadow-sm transition-all active:scale-95 border border-blue-100" 
                                   title="Edit Status">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                @if(($status->tasks_count ?? 0) == 0)
                                    <form action="{{ route('admin.task-statuses.destroy', $status) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus status ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-600 hover:text-white shadow-sm transition-all active:scale-95 border border-red-100" 
                                                title="Hapus Status">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                @else
                                    <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-50 text-gray-300 border border-gray-100 cursor-not-allowed" title="Status sedang digunakan">
                                        <i class="fas fa-lock text-xs"></i>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gray-50 border border-dashed border-gray-200 rounded-full flex items-center justify-center mb-6">
                                    <i class="fas fa-stream text-gray-200 text-3xl"></i>
                                </div>
                                <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">Status Tidak Ditemukan</h3>
                                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-widest mt-2 max-w-xs mx-auto italic">
                                    {{ request('search') ? 'Coba sesuaikan kata kunci pencarian Anda.' : 'Belum ada data status tersedia.' }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card-container>
</div>
@endsection


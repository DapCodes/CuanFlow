@extends('layouts.app')

@section('title', 'Manajemen Tugas - Table View - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Project Board</span>
</li>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" rel="stylesheet" />
@endpush

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Alert / Notifikasi --}}
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-check-circle mt-0.5 text-green-500"></i>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-exclamation-circle mt-0.5 text-red-500"></i>
                <p class="text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Database Tugas
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola ribuan tugas dengan mudah menggunakan <span class="font-semibold text-cuan-green">{{ auth()->user()->outlet->name ?? 'CuanFlow' }}</span> tampilan tabel.
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                {{-- View Switcher --}}
                <div class="inline-flex items-center p-1 bg-gray-50 border border-gray-200 rounded-lg">
                    <a href="{{ route('tasks.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-black text-gray-500 hover:text-gray-900 hover:bg-white/50 transition-all">
                        <i class="fa-solid fa-columns mr-1.5"></i>Board
                    </a>
                    <a href="{{ route('tasks.table') }}" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-black transition-all bg-cuan-green text-white shadow-sm shadow-cuan-green/20">
                        <i class="fa-solid fa-table mr-1.5"></i>Table
                    </a>
                    <a href="{{ route('tasks.calendar') }}" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-black text-gray-500 hover:text-gray-900 hover:bg-white/50 transition-all">
                        <i class="fa-solid fa-calendar mr-1.5"></i>Calendar
                    </a>
                </div>

                @can('tasks.create')
                <button onclick="openCreateModal()" class="inline-flex items-center gap-2 rounded-lg bg-cuan-green px-4 py-2.5 text-sm font-black text-white hover:bg-cuan-dark transition-all active:scale-95 shadow-sm">
                    <i class="fas fa-plus-circle text-sm"></i>
                    <span>Tugas Baru</span>
                </button>
                @endcan
            </div>
        </section>

        {{-- RINGKASAN STATISTIK --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Tugas</p>
                <p class="mt-2 text-2xl font-black text-gray-900">{{ $stats['total'] }}</p>
                <div class="mt-2 text-[10px] text-gray-500 font-black uppercase tracking-widest">Akumulasi Project</div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Menunggu</p>
                <p class="mt-2 text-2xl font-black text-amber-600">{{ $stats['pending'] }}</p>
                <div class="mt-2 text-[10px] text-amber-600 font-black uppercase tracking-widest">Status Pending</div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Berlangsung</p>
                <p class="mt-2 text-2xl font-black text-blue-600">{{ $stats['in_progress'] }}</p>
                <div class="mt-2 text-[10px] text-blue-600 font-black uppercase tracking-widest">Sedang Dikerjakan</div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Selesai</p>
                <p class="mt-2 text-2xl font-black text-cuan-green">{{ $stats['completed'] }}</p>
                <div class="mt-2 text-[10px] text-cuan-green font-black uppercase tracking-widest">Tugas Rampung</div>
            </div>
        </section>

        {{-- KONTEN UTAMA: TOOLBAR + TABEL (KONSISTEN) --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            {{-- Toolbar: Search & Filter --}}
            <div class="border-b border-gray-200 px-4 md:px-6 py-4 space-y-3 md:space-y-0 md:flex md:items-center md:justify-between gap-4">
                <div class="w-full md:max-w-md">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Cari tugas</label>
                    <div class="relative">
                        <input type="text" id="searchTask" placeholder="Cari berdasarkan judul tugas..."
                               class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-cuan-green/20 focus:border-cuan-green">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    </div>
                </div>

                <form action="{{ route('tasks.table') }}" method="GET" class="flex flex-wrap gap-3 w-full md:w-auto">
                    <div class="w-full sm:w-40 md:w-44">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Status</label>
                        <select name="status_id" onchange="this.form.submit()"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-cuan-green/20 focus:border-cuan-green">
                            <option value="">Semua Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}" {{ request('status_id') == $status->id ? 'selected' : '' }}>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-full sm:w-40 md:w-44">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Prioritas</label>
                        <select name="priority" onchange="this.form.submit()"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-cuan-green/20 focus:border-cuan-green">
                            <option value="">Semua Prioritas</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Tinggi</option>
                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Sedang</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Rendah</option>
                        </select>
                    </div>

                    <div class="w-full sm:w-40 md:w-44">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Assignee</label>
                        <select name="assignee" onchange="this.form.submit()"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-cuan-green/20 focus:border-cuan-green">
                            <option value="">Semua Assignee</option>
                            @foreach($assignableUsers as $user)
                                <option value="{{ $user->id }}" {{ request('assignee') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if(request()->hasAny(['priority', 'assignee', 'status_id']))
                    <div class="flex items-end">
                        <a href="{{ route('tasks.table') }}" class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 text-xs font-black">
                            <i class="fas fa-times-circle"></i>
                            Reset Filter
                        </a>
                    </div>
                    @endif
                </form>
            </div>

            {{-- Tabel --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50/50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Nama Tugas
                            </th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Status
                            </th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Prioritas
                            </th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Assignee
                            </th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Deadline
                            </th>
                            <th class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white" id="taskTableBody">
                        @forelse($tasks as $task)
                            <tr class="task-row hover:bg-gray-50 transition-colors cursor-pointer"
                                data-title="{{ strtolower($task->title) }}"
                                onclick="openDetailModal({{ $task->id }})">
                                
                                {{-- Nama Tugas + Labels --}}
                                <td class="px-6 py-3">
                                    <div class="flex items-start gap-3">
                                        <div class="w-2 h-2 rounded-full mt-1.5 shrink-0" 
                                             style="background-color: {{ $task->status->color }}"></div>
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $task->title }}</div>
                                            @if($task->description)
                                                <div class="mt-0.5 text-xs text-gray-500 line-clamp-1">
                                                    {{ $task->description }}
                                                </div>
                                            @endif
                                            @if($task->labels->isNotEmpty())
                                                <div class="flex flex-wrap gap-1.5 mt-2">
                                                    @foreach($task->labels as $label)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium text-white" 
                                                              style="background-color: {{ $label->color }}">
                                                            {{ $label->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                          style="background-color: {{ $task->status->color }}15; color: {{ $task->status->color }}">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5" 
                                              style="background-color: {{ $task->status->color }}"></span>
                                        {{ $task->status->name }}
                                    </span>
                                </td>

                                {{-- Prioritas --}}
                                <td class="px-6 py-3 whitespace-nowrap">
                                    @if($task->priority == 'high')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-100">
                                            <i class="fas fa-exclamation-circle mr-1 text-[10px]"></i>
                                            Tinggi
                                        </span>
                                    @elseif($task->priority == 'medium')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-100">
                                            <i class="fas fa-exclamation-triangle mr-1 text-[10px]"></i>
                                            Sedang
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <i class="fas fa-check-circle mr-1 text-[10px]"></i>
                                            Rendah
                                        </span>
                                    @endif
                                </td>

                                {{-- Assignee --}}
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <div class="flex -space-x-2">
                                        @foreach($task->assignees->take(3) as $assignee)
                                            <div class="w-7 h-7 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center text-xs font-semibold text-gray-600 shadow-sm" 
                                                 title="{{ $assignee->name }}">
                                                {{ substr($assignee->name, 0, 1) }}
                                            </div>
                                        @endforeach
                                        @if($task->assignees->count() > 3)
                                            <div class="w-7 h-7 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center text-xs font-semibold text-gray-600 shadow-sm"
                                                 title="{{ $task->assignees->count() - 3 }} lainnya">
                                                +{{ $task->assignees->count() - 3 }}
                                            </div>
                                        @endif
                                        @if($task->assignees->isEmpty())
                                            <div class="w-7 h-7 rounded-full border-2 border-gray-200 bg-gray-50 flex items-center justify-center" 
                                                 title="Belum ada assignee">
                                                <i class="fa-solid fa-user-plus text-[10px] text-gray-300"></i>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                {{-- Deadline --}}
                                <td class="px-6 py-3 whitespace-nowrap">
                                    @if($task->deadline)
                                        <div class="flex items-center gap-1.5 text-sm {{ $task->is_overdue ? 'text-red-500 font-semibold' : 'text-gray-600' }}">
                                            <i class="fa-regular fa-clock text-xs"></i>
                                            <span>{{ $task->deadline->format('d M Y, H:i') }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">Tidak ada deadline</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-3 whitespace-nowrap text-center" onclick="event.stopPropagation()">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button onclick="openDetailModal({{ $task->id }})"
                                               class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-gray-200 bg-white text-gray-600 hover:bg-gray-50"
                                               title="Detail">
                                            <i class="fas fa-eye text-xs"></i>
                                        </button>
                                        @can('tasks.update')
                                        <button onclick="openEditModal({{ $task->id }})"
                                               class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-yellow-200 bg-yellow-50 text-yellow-600 hover:bg-yellow-100"
                                               title="Edit">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                        @endcan
                                        @can('tasks.delete')
                                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus tugas ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-red-200 bg-red-50 text-red-600 hover:bg-red-100"
                                                    title="Hapus">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-center">
                                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                            <i class="fas fa-tasks text-3xl text-gray-300"></i>
                                        </div>
                                        <h3 class="text-base font-semibold text-gray-900 mb-1">Belum ada tugas</h3>
                                        <p class="text-sm text-gray-500 mb-4 max-w-sm">
                                            Mulai buat tugas baru untuk mengatur pekerjaan tim Anda dengan lebih baik.
                                        </p>
                                        @can('tasks.create')
                                        <button onclick="openCreateModal()"
                                               class="inline-flex items-center gap-2 rounded-lg bg-cuan-green px-4 py-2.5 text-sm font-black text-white hover:bg-cuan-dark transition-all">
                                            <i class="fas fa-plus-circle text-xs"></i>
                                            Tambah Tugas
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($tasks->hasPages())
                <div class="px-4 md:px-6 py-3 border-t border-gray-200">
                    {{ $tasks->links() }}
                </div>
            @endif
        </section>
    </div>
</main>

<!-- Modals -->
@include('tasks.partials.create-modal')
@include('tasks.partials.edit-modal')
@include('tasks.partials.detail-modal')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Search functionality
    const searchInput = document.getElementById('searchTask');
    const taskRows = document.querySelectorAll('.task-row');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            taskRows.forEach(row => {
                const title = row.dataset.title || '';
                const matchesSearch = !searchTerm || title.includes(searchTerm);
                
                row.style.display = matchesSearch ? '' : 'none';
            });
        });
    }
});
</script>
@endpush
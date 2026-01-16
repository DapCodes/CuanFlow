@extends('layouts.app')

@section('title', 'Manajemen Tugas - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('tasks.index') }}" class="text-gray-900 font-medium hover:text-purple-600 transition-colors">Manajemen Tugas</a>
</li>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" rel="stylesheet" />
<style>
    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f3f4f6;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }

    /* Kanban Column Styling */
    .kanban-column {
        min-height: 400px;
        background: #f9fafb;
        border-radius: 12px;
        padding: 16px;
    }
    
    /* Kanban Card Enhanced */
    .kanban-card {
        cursor: grab;
        transition: all 0.2s ease;
        background: white;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    
    .kanban-card:hover {
        border-color: #d1d5db;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transform: translateY(-2px);
    }
    
    .kanban-card:active {
        cursor: grabbing;
    }
    
    /* Sortable States */
    .sortable-ghost {
        opacity: 0.4;
        background: #e0e7ff;
        border: 2px dashed #818cf8;
    }
    
    .sortable-chosen {
        cursor: grabbing;
        transform: rotate(2deg);
    }
    
    .sortable-drag {
        opacity: 1;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        cursor: grabbing;
    }

    /* Drag Handle */
    .drag-handle {
        cursor: grab;
        opacity: 0;
        transition: opacity 0.2s;
    }
    
    .kanban-card:hover .drag-handle {
        opacity: 1;
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .kanban-board-container {
            flex-direction: column;
        }
        
        .kanban-column-wrapper {
            width: 100% !important;
            margin-bottom: 16px;
        }
        
        .kanban-column {
            min-height: 300px;
        }
    }
    
    /* Priority Border Colors */
    .priority-high { border-left-color: #ef4444; border-left-width: 3px; }
    .priority-medium { border-left-color: #f59e0b; border-left-width: 3px; }
    .priority-low { border-left-color: #10b981; border-left-width: 3px; }
</style>
@endpush

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-[1600px] mx-auto space-y-6">

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

        {{-- HEADER HALAMAN (KONSISTEN DENGAN DISCOUNT INDEX) --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-purple-50 text-purple-500 border border-purple-100">
                        <i class="fas fa-layer-group text-sm"></i>
                    </span>
                    <span>Project Board</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Visualisasikan alur kerja tim Anda. Geser tugas antar kolom untuk memperbarui progres secara instan.
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                {{-- View Switcher --}}
                <div class="inline-flex items-center p-1 bg-gray-50 border border-gray-200 rounded-lg">
                    <a href="{{ route('tasks.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold transition-all bg-white text-gray-900 shadow-sm border border-gray-100">
                        <i class="fa-solid fa-columns mr-1.5 text-purple-500"></i>Board
                    </a>
                    <a href="{{ route('tasks.table') }}" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-gray-500 hover:text-gray-900 hover:bg-white/50 transition-all">
                        <i class="fa-solid fa-table mr-1.5"></i>Table
                    </a>
                    <a href="{{ route('tasks.calendar') }}" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-gray-500 hover:text-gray-900 hover:bg-white/50 transition-all">
                        <i class="fa-solid fa-calendar mr-1.5"></i>Calendar
                    </a>
                </div>

                @can('tasks.create')
                <button onclick="openCreateModal()" class="inline-flex items-center gap-2 rounded-lg bg-purple-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-1">
                    <i class="fas fa-plus-circle text-sm"></i>
                    <span>Tugas Baru</span>
                </button>
                @endcan
            </div>
        </section>

        {{-- RINGKASAN STATISTIK (KONSISTEN DENGAN DISCOUNT INDEX) --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Tugas</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100">
                        <i class="fas fa-tasks text-gray-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Menunggu</p>
                        <p class="mt-1 text-2xl font-semibold text-amber-600">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center border border-amber-100">
                        <i class="fas fa-circle-dot text-amber-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Berlangsung</p>
                        <p class="mt-1 text-2xl font-semibold text-blue-600">{{ $stats['in_progress'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center border border-blue-100">
                        <i class="fas fa-spinner text-blue-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Selesai</p>
                        <p class="mt-1 text-2xl font-semibold text-emerald-600">{{ $stats['completed'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100">
                        <i class="fas fa-check-double text-emerald-500 text-lg"></i>
                    </div>
                </div>
            </div>
        </section>

        {{-- KONTEN UTAMA: FILTER BAR + KANBAN (KONSISTEN DENGAN DISCOUNT INDEX) --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            {{-- Filter Bar --}}
            <div class="border-b border-gray-200 px-4 md:px-6 py-4 space-y-3 md:space-y-0 md:flex md:items-center md:justify-between gap-4">
                <div class="w-full md:max-w-md">
                    <label class="text-xs font-medium text-gray-500 mb-1 block">Cari tugas</label>
                    <div class="relative">
                        <input type="text" id="searchTask" placeholder="Cari berdasarkan judul..."
                               class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-400">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    </div>
                </div>

                <form action="{{ route('tasks.index') }}" method="GET" class="flex flex-wrap gap-3 w-full md:w-auto">
                    <div class="w-full sm:w-40 md:w-44">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Prioritas</label>
                        <select name="priority" onchange="this.form.submit()"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-400">
                            <option value="">Semua Prioritas</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Tinggi</option>
                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Sedang</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Rendah</option>
                        </select>
                    </div>

                    <div class="w-full sm:w-40 md:w-44">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Assignee</label>
                        <select name="assignee" onchange="this.form.submit()"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-400">
                            <option value="">Semua Assignee</option>
                            @foreach($assignableUsers as $user)
                                <option value="{{ $user->id }}" {{ request('assignee') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-full sm:w-40 md:w-44">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Label</label>
                        <select name="label" onchange="this.form.submit()"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-400">
                            <option value="">Semua Label</option>
                            @foreach($labels as $label)
                                <option value="{{ $label->id }}" {{ request('label') == $label->id ? 'selected' : '' }}>
                                    {{ $label->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if(request()->hasAny(['priority', 'assignee', 'label']))
                    <div class="flex items-end">
                        <a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 text-xs font-semibold">
                            <i class="fas fa-times-circle"></i>
                            Reset Filter
                        </a>
                    </div>
                    @endif
                </form>
            </div>

            {{-- KANBAN BOARD --}}
            <div class="p-4 md:p-6">
                <div class="kanban-board-container flex gap-4 overflow-x-auto pb-4 custom-scrollbar">
                    @foreach($statuses as $status)
                    <div class="kanban-column-wrapper flex flex-col min-w-[320px] md:min-w-[340px]">
                        {{-- Column Header --}}
                        <div class="flex items-center justify-between mb-3 px-2">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $status->color }}"></span>
                                <h3 class="font-semibold text-gray-700 text-sm">{{ $status->name }}</h3>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    {{ $status->tasks->count() }}
                                </span>
                            </div>
                            @can('tasks.create')
                            <button onclick="openCreateModal({{ $status->id }})" 
                                    class="inline-flex items-center justify-center w-7 h-7 rounded-md hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors"
                                    title="Tambah tugas">
                                <i class="fa-solid fa-plus text-xs"></i>
                            </button>
                            @endcan
                        </div>

                        {{-- Column Content --}}
                        <div id="status-{{ $status->id }}" 
                             data-status-id="{{ $status->id }}"
                             class="kanban-column flex flex-col gap-3">
                            
                            @foreach($status->tasks as $task)
                            <div data-id="{{ $task->id }}" 
                                 onclick="openDetailModal({{ $task->id }})" 
                                 class="kanban-card rounded-lg p-4 priority-{{ $task->priority }}">
                                
                                {{-- Drag Handle --}}
                                <div class="flex items-start justify-between mb-3">
                                    <div class="drag-handle text-gray-300 hover:text-gray-400">
                                        <i class="fas fa-grip-vertical text-xs"></i>
                                    </div>
                                    
                                    {{-- Priority Badge --}}
                                    @if($task->priority == 'high')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-100">
                                            <i class="fas fa-exclamation-circle mr-1 text-[10px]"></i>
                                            Tinggi
                                        </span>
                                    @elseif($task->priority == 'medium')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-100">
                                            <i class="fas fa-exclamation-triangle mr-1 text-[10px]"></i>
                                            Sedang
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <i class="fas fa-check-circle mr-1 text-[10px]"></i>
                                            Rendah
                                        </span>
                                    @endif
                                </div>

                                {{-- Labels --}}
                                @if($task->labels->isNotEmpty())
                                <div class="flex flex-wrap gap-1.5 mb-3">
                                    @foreach($task->labels as $label)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium text-white" 
                                              style="background-color: {{ $label->color }}">
                                            {{ $label->name }}
                                        </span>
                                    @endforeach
                                </div>
                                @endif

                                {{-- Title --}}
                                <h4 class="text-sm font-semibold text-gray-900 mb-2 line-clamp-2 leading-snug">
                                    {{ $task->title }}
                                </h4>

                                {{-- Description --}}
                                @if($task->description)
                                <p class="text-xs text-gray-500 line-clamp-2 mb-3 leading-relaxed">
                                    {{ $task->description }}
                                </p>
                                @endif

                                {{-- Footer --}}
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    {{-- Deadline --}}
                                    @if($task->deadline)
                                    <div class="flex items-center gap-1.5 text-xs {{ $task->is_overdue ? 'text-red-500 font-semibold' : 'text-gray-500' }}">
                                        <i class="fa-regular fa-clock text-[11px]"></i>
                                        <span>{{ $task->deadline->format('d M Y') }}</span>
                                    </div>
                                    @else
                                    <div></div>
                                    @endif

                                    {{-- Assignees --}}
                                    <div class="flex -space-x-2">
                                        @foreach($task->assignees->take(3) as $assignee)
                                        <div class="w-7 h-7 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center text-xs font-semibold text-gray-600 shadow-sm" 
                                             title="{{ $assignee->name }}">
                                            {{ substr($assignee->name, 0, 1) }}
                                        </div>
                                        @endforeach
                                        @if($task->assignees->count() > 3)
                                        <div class="w-7 h-7 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center text-xs font-semibold text-gray-600 shadow-sm">
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
                                </div>
                            </div>
                            @endforeach
                            
                            {{-- Empty State --}}
                            <div class="empty-state-placeholder border-2 border-dashed border-gray-200 rounded-lg p-8 text-center {{ $status->tasks->isNotEmpty() ? 'hidden' : '' }}">
                                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-2">
                                    <i class="fas fa-inbox text-xl text-gray-300"></i>
                                </div>
                                <p class="text-xs font-medium text-gray-400">Belum ada tugas</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
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
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Sortable for each column
    const columns = document.querySelectorAll('.kanban-column');
    
    @can('tasks.update')
    columns.forEach(column => {
        new Sortable(column, {
            group: 'kanban',
            animation: 200,
            easing: "cubic-bezier(0.4, 0, 0.2, 1)",
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            handle: '.kanban-card',
            forceFallback: true,
            fallbackTolerance: 3,
            touchStartThreshold: 5,
            delay: 50,
            delayOnTouchOnly: true,
            
            onStart: function(evt) {
                evt.item.style.cursor = 'grabbing';
            },
            
            onEnd: function(evt) {
                evt.item.style.cursor = 'grab';
                
                const taskId = evt.item.dataset.id;
                const newStatusId = evt.to.dataset.statusId;
                
                if (evt.from !== evt.to) {
                    updateTaskStatus(taskId, newStatusId);
                    updateCounters(); // Immediate UI update
                }
            }
        });
    });
    @endcan

    // Search functionality
    const searchInput = document.getElementById('searchTask');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const cards = document.querySelectorAll('.kanban-card');
            
            cards.forEach(card => {
                const title = card.querySelector('h4').textContent.toLowerCase();
                const description = card.querySelector('p')?.textContent.toLowerCase() || '';
                
                if (title.includes(searchTerm) || description.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
});

function updateTaskStatus(taskId, statusId) {
    // Show loading state
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Memperbarui...',
            text: 'Sedang memproses perubahan status',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    fetch(`/tasks/${taskId}/update-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status_id: statusId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Show success toast
            if (typeof Swal !== 'undefined') {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: data.message || 'Status tugas berhasil diperbarui'
                });
            }
            
            // Update counter badges
            updateCounters();
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire('Error', data.message || 'Gagal memperbarui status', 'error').then(() => {
                    location.reload();
                });
            } else {
                alert(data.message || 'Gagal memperbarui status');
                location.reload();
            }
        }
    })
    .catch(err => {
        console.error('Error updating task status:', err);
        if (typeof Swal !== 'undefined') {
            Swal.fire('Error', 'Terjadi kesalahan saat memperbarui status', 'error').then(() => {
                location.reload();
            });
        } else {
            alert('Terjadi kesalahan saat memperbarui status');
            location.reload();
        }
    });
}

function updateCounters() {
    // Update counter badges for each column
    document.querySelectorAll('.kanban-column').forEach(column => {
        // Count only visible cards (important when filtering)
        // If sorting, all cards are present. If filtering, some are hidden.
        // For empty state logic "Belum ada tugas" (no tasks exist), we usually check ALL cards,
        // but if filtering hides all, user might want to see "No results".
        // However, generic "Belum ada tugas" implies empty column.
        // Let's check all cards for now to match 'physical' column state.
        
        const count = column.querySelectorAll('.kanban-card:not(.hidden)').length;
        // Search functionality hides using style="display: none", not class .hidden
        // Let's check style display too if needed. 
        // But the search script uses style.display = 'none'.
        // So checking :not([style*="display: none"]) might be tricky.
        
        // Let's stick to total count physically in the column for now as per requirement.
        const totalCards = column.querySelectorAll('.kanban-card').length;
        
        const badge = column.closest('.kanban-column-wrapper').querySelector('.bg-gray-100');
        if (badge) {
            badge.textContent = totalCards;
        }

        // Toggle Empty State
        const emptyState = column.querySelector('.empty-state-placeholder');
        if (emptyState) {
            if (totalCards === 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }
        }
    });
}
</script>
@endpush
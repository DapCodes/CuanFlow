@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Manajemen Tugas - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Project Board</span>
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

    /* Form Enhancements for Clear Borders */
    .choices__inner, 
    input[type="text"], 
    input[type="datetime-local"], 
    textarea, 
    select {
        border: 1px solid #d1d5db !important; /* border-gray-300 */
        border-radius: 0.5rem !important; /* rounded-lg */
        background-color: #ffffff !important;
        transition: all 0.2s ease !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
    }

    .choices__inner:hover,
    input:hover, 
    textarea:hover, 
    select:hover {
        border-color: #9ca3af !important; /* border-gray-400 */
    }

    .choices__inner:focus-within,
    input:focus, 
    textarea:focus, 
    select:focus {
        border-color: #00A884 !important; /* cuan-green */
        box-shadow: 0 0 0 4px rgba(0, 168, 132, 0.1) !important;
        outline: none !important;
    }

    /* Choices.js Specific Fixes */
    .choices__list--multiple .choices__item {
        background-color: #00A884 !important;
        border: 1px solid #00A884 !important;
        font-weight: 800 !important;
        font-size: 10px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        border-radius: 4px !important;
    }

    .choices__inner {
        padding: 4px 8px !important;
        min-height: 42px !important;
        display: flex !important;
        align-items: center !important;
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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

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
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-cuan-green/10 rounded-2xl flex items-center justify-center text-cuan-green shadow-sm shadow-cuan-green/10">
                    <i class="fas fa-tasks text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl md:text-2xl font-black text-gray-900 tracking-tight">Project Board</h1>
                    <p class="text-sm text-gray-500 mt-0.5 font-medium">Atur dan pantau progres tugas tim Anda</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex rounded-xl bg-white border border-gray-200 p-1 shadow-sm overflow-x-auto custom-scrollbar no-scrollbar">
                    <button @click="view = 'kanban'" 
                            :class="view === 'kanban' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-500 hover:bg-gray-50'"
                            class="px-4 py-1.5 rounded-lg text-xs font-black transition-all flex items-center gap-2">
                        <i class="fas fa-columns"></i>
                        <span>Board</span>
                    </button>
                    <button @click="view = 'table'" 
                            :class="view === 'table' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-500 hover:bg-gray-50'"
                            class="px-4 py-1.5 rounded-lg text-xs font-black transition-all flex items-center gap-2">
                        <i class="fas fa-table"></i>
                        <span>Table</span>
                    </button>
                    <button @click="view = 'calendar'" 
                            :class="view === 'calendar' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-500 hover:bg-gray-50'"
                            class="px-4 py-1.5 rounded-lg text-xs font-black transition-all flex items-center gap-2">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Calendar</span>
                    </button>
                </div>
                @can('tasks.create')
                <button @click="$dispatch('open-create-modal')" 
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-black text-white hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 active:scale-95">
                    <i class="fa-solid fa-circle-plus text-xs"></i>
                    <span>Tugas Baru</span>
                </button>
                @endcan
            </div>
        </section>

        {{-- STATS GRID --}}
        <section class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-5 shadow-sm">
                <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Total Tugas</p>
                <p class="mt-1 text-xl font-black text-gray-900 leading-none">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-5 shadow-sm">
                <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Sedang Berjalan</p>
                <p class="mt-1 text-xl font-black text-blue-600 leading-none">{{ $stats['in_progress'] }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-5 shadow-sm">
                <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Selesai</p>
                <p class="mt-1 text-xl font-black text-cuan-green leading-none">{{ $stats['completed'] }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-5 shadow-sm">
                <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Menunggu</p>
                <p class="mt-1 text-xl font-black text-amber-500 leading-none">{{ $stats['pending'] }}</p>
            </div>
        </section>

        {{-- KONTEN UTAMA: FILTER BAR + KANBAN --}}
        <x-card-container>
            {{-- Filter Bar --}}
            <div class="border-b border-gray-200 px-6 py-6 flex flex-col lg:flex-row lg:items-end gap-4">
                <div class="flex-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 block">Cari tugas</label>
                    <input type="text" id="searchTask" placeholder="Cari berdasarkan judul..."
                           class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all font-bold text-gray-700 shadow-sm placeholder:text-gray-400">
                </div>

                <form action="{{ route('tasks.index') }}" method="GET" class="flex flex-col sm:flex-row flex-wrap gap-3 w-full lg:w-auto">
                    <div class="w-full sm:w-40">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 block">Prioritas</label>
                        <select name="priority" onchange="this.form.submit()"
                                class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all font-bold text-gray-700 shadow-sm">
                            <option value="">Semua</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Tinggi</option>
                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Sedang</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Rendah</option>
                        </select>
                    </div>

                    <div class="w-full sm:w-44">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 block">Assignee</label>
                        <select name="assignee" onchange="this.form.submit()"
                                class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all font-bold text-gray-700 shadow-sm">
                            <option value="">Semua</option>
                            @foreach($assignableUsers as $user)
                                <option value="{{ $user->id }}" {{ request('assignee') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-full sm:w-40">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 block">Label</label>
                        <select name="label" onchange="this.form.submit()"
                                class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all font-bold text-gray-700 shadow-sm">
                            <option value="">Semua</option>
                            @foreach($labels as $label)
                                <option value="{{ $label->id }}" {{ request('label') == $label->id ? 'selected' : '' }}>
                                    {{ $label->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if(request()->hasAny(['priority', 'assignee', 'label']))
                    <div class="flex items-end">
                        <a href="{{ route('tasks.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 text-xs font-black transition-all">
                            <i class="fas fa-undo"></i>
                            Reset
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
                        <div class="flex items-center justify-between mb-3 px-2">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $status->color }}"></span>
                                <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-900">{{ $status->name }}</h3>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-gray-100 text-gray-600">
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
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-black bg-red-50 text-red-700 border border-red-100">
                                            <i class="fas fa-exclamation-circle mr-1 text-[10px]"></i>
                                            Tinggi
                                        </span>
                                    @elseif($task->priority == 'medium')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-black bg-amber-50 text-amber-700 border border-amber-100">
                                            <i class="fas fa-exclamation-triangle mr-1 text-[10px]"></i>
                                            Sedang
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-100">
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
                                <h4 class="text-sm font-black text-gray-900 mb-2 line-clamp-2 leading-snug">
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
                                    <div class="flex items-center gap-1.5 text-xs {{ $task->is_overdue ? 'text-red-500 font-black' : 'text-gray-500 font-black' }}">
                                        <i class="fa-regular fa-clock text-[11px]"></i>
                                        <span>{{ $task->deadline->format('d M Y') }}</span>
                                    </div>
                                    @else
                                    <div></div>
                                    @endif

                                    {{-- Assignees --}}
                                    <div class="flex -space-x-2">
                                        @foreach($task->assignees->take(3) as $assignee)
                                        <div class="w-7 h-7 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center text-xs font-black text-gray-600 shadow-sm" 
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
                                <p class="text-xs font-black text-gray-400">Belum ada tugas</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            </div>
        </x-card-container>
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
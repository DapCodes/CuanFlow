@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Manajemen Tugas - Calendar View - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Project Board</span>
</li>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" rel="stylesheet" />
<style>
    /* FullCalendar Custom Styling - Konsisten dengan Design System */
    .fc {
        font-family: inherit;
        --fc-border-color: #e5e7eb;
        --fc-button-bg-color: #ffffff;
        --fc-button-border-color: #d1d5db;
        --fc-button-hover-bg-color: #f3f4f6;
        --fc-button-hover-border-color: #9ca3af;
        --fc-button-active-bg-color: #e5e7eb;
        --fc-button-text-color: #374151;
        --fc-today-bg-color: rgba(0, 168, 132, 0.05);
    }
    
    .fc .fc-toolbar-title {
        font-size: 1.125rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #111827;
    }

    .fc .fc-button {
        font-weight: 500;
        text-transform: capitalize;
        border-radius: 0.5rem;
        padding: 0.5rem 0.875rem;
        font-size: 0.875rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
    }

    .fc .fc-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .fc .fc-button-primary:not(:disabled).fc-button-active, 
    .fc .fc-button-primary:not(:disabled):active {
        background-color: #00A884;
        color: white;
        border-color: #00A884;
    }
    
    .fc-event {
        cursor: pointer;
        padding: 0.25rem 0.375rem;
        border-radius: 0.375rem;
        font-weight: 500;
        font-size: 0.75rem;
        border: none !important;
        margin-bottom: 2px;
        transition: all 0.2s ease;
    }

    .fc-event:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        opacity: 0.9;
    }

    .fc-event-title {
        font-weight: 600;
    }

    .fc-daygrid-day-number {
        font-weight: 500;
        color: #6b7280;
        padding: 0.5rem !important;
        font-size: 0.875rem;
    }

    .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
        background-color: #00A884;
        color: white;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .fc-col-header-cell-cushion {
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 0.75rem 0 !important;
    }

    .fc-daygrid-day:hover {
        background-color: #f9fafb;
    }

    /* Badge untuk event tanpa deadline */
    .fc-event.no-deadline {
        border-left: 3px solid #9ca3af !important;
        background-color: #f3f4f6 !important;
        color: #6b7280 !important;
    }

    /* Badge untuk event overdue */
    .fc-event.overdue {
        border-left: 3px solid #ef4444 !important;
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
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

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: .8;
        }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .fc .fc-toolbar {
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .fc .fc-toolbar-chunk {
            display: flex;
            justify-content: center;
            width: 100%;
        }
        
        .fc-header-toolbar {
            margin-bottom: 1rem !important;
        }

        .fc .fc-toolbar-title {
            font-size: 1rem;
        }

        .fc .fc-button {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
        }

        .fc-event {
            font-size: 0.7rem;
            padding: 0.125rem 0.25rem;
        }

        .fc-daygrid-day-number {
            font-size: 0.75rem;
            padding: 0.25rem !important;
        }
    }

    /* Legend styles */
    .calendar-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1rem;
        padding: 1rem;
        background-color: #f9fafb;
        border-radius: 0.5rem;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
    }

    .legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 0.25rem;
    }
</style>
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
                    Timeline Kalender
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Pantau target deadline tim Anda dalam tampilan <span class="font-semibold text-cuan-green">{{ auth()->user()->outlet->name ?? 'CuanFlow' }}</span> kalender yang intuitif.
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                {{-- View Switcher --}}
                <div class="inline-flex items-center p-1 bg-gray-50 border border-gray-200 rounded-lg">
                    <a href="{{ route('tasks.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-black text-gray-500 hover:text-gray-900 hover:bg-white/50 transition-all">
                        <i class="fa-solid fa-columns mr-1.5"></i>Board
                    </a>
                    <a href="{{ route('tasks.table') }}" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-black text-gray-500 hover:text-gray-900 hover:bg-white/50 transition-all">
                        <i class="fa-solid fa-table mr-1.5"></i>Table
                    </a>
                    <a href="{{ route('tasks.calendar') }}" class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-black transition-all bg-cuan-green text-white shadow-sm shadow-cuan-green/20">
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
                <p class="mt-2 text-2xl font-black text-gray-900">{{ $stats['pending'] }}</p>
                <div class="mt-2 text-[10px] text-gray-400 font-black uppercase tracking-widest">Status Pending</div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Berlangsung</p>
                <p class="mt-2 text-2xl font-black text-gray-900">{{ $stats['in_progress'] }}</p>
                <div class="mt-2 text-[10px] text-gray-400 font-black uppercase tracking-widest">Sedang Dikerjakan</div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Selesai</p>
                <p class="mt-2 text-2xl font-black text-cuan-green">{{ $stats['completed'] }}</p>
                <div class="mt-2 text-[10px] text-cuan-green font-black uppercase tracking-widest">Tugas Rampung</div>
            </div>
        </section>

        {{-- KONTEN UTAMA: KALENDER (KONSISTEN) --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            {{-- Legend --}}
            <div class="border-b border-gray-200 px-4 md:px-6 py-4">
                <div class="calendar-legend">
                    <div class="legend-item">
                        <div class="legend-dot" style="background-color: #ef4444;"></div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">Overdue</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-dot" style="background-color: #00A884;"></div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">Dengan Deadline</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-dot bg-gray-300"></div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">Tanpa Deadline</span>
                    </div>
                </div>
            </div>

            {{-- Calendar Container --}}
            <div class="p-4 md:p-6">
                <div id='calendar'></div>
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
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        height: 'auto',
        aspectRatio: 1.8,
        
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        
        buttonText: {
            today: 'Hari Ini',
            month: 'Bulan',
            week: 'Minggu',
            day: 'Hari',
            list: 'List'
        },
        
        views: {
            listWeek: {
                buttonText: 'Agenda'
            }
        },
        
        // Fetch events from server
        events: '{{ route('tasks.calendar-data') }}',
        
        // Handle event click
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            
            // Extract task ID from event
            const taskId = info.event.id;
            
            // Open detail modal
            if (typeof openDetailModal === 'function') {
                openDetailModal(taskId);
            } else {
                console.error('openDetailModal function not found');
            }
        },
        
        // Customize event appearance
        eventDidMount: function(info) {
            const event = info.event;
            const el = info.el;
            
            // Add tooltip
            el.setAttribute('title', event.title);
            
            // Check if overdue
            if (event.extendedProps && event.extendedProps.isOverdue) {
                el.classList.add('overdue');
            }
            
            // Check if no deadline
            if (event.extendedProps && event.extendedProps.noDeadline) {
                el.classList.add('no-deadline');
            }
            
            // Apply custom background color with opacity
            if (event.backgroundColor) {
                const bgColor = event.backgroundColor;
                el.style.backgroundColor = bgColor + '20'; // 20% opacity
                el.style.borderLeft = `3px solid ${bgColor}`;
                el.style.color = bgColor;
            }
        },
        
        // Handle date click (create new task)
        dateClick: function(info) {
            @can('tasks.create')
            if (typeof openCreateModal === 'function') {
                // You can pass the clicked date to pre-fill deadline
                openCreateModal(null, info.dateStr);
            }
            @endcan
        },
        
        // Responsive toolbar
        windowResize: function(view) {
            if (window.innerWidth < 768) {
                calendar.setOption('headerToolbar', {
                    left: 'prev,next',
                    center: 'title',
                    right: 'today'
                });
            } else if (window.innerWidth < 1024) {
                calendar.setOption('headerToolbar', {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                });
            } else {
                calendar.setOption('headerToolbar', {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                });
            }
        },
        
        // Loading indicator
        loading: function(isLoading) {
            if (isLoading) {
                // Show loading state
                calendarEl.style.opacity = '0.5';
            } else {
                // Hide loading state
                calendarEl.style.opacity = '1';
            }
        },
        
        // Error handling
        eventSourceFailure: function(error) {
            console.error('Error loading calendar events:', error);
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memuat Data',
                    text: 'Terjadi kesalahan saat memuat data kalender. Silakan refresh halaman.',
                    confirmButtonColor: '#6366f1'
                });
            } else {
                alert('Gagal memuat data kalender. Silakan refresh halaman.');
            }
        }
    });
    
    // Initial responsive check
    if (window.innerWidth < 768) {
        calendar.setOption('headerToolbar', {
            left: 'prev,next',
            center: 'title',
            right: 'today'
        });
    } else if (window.innerWidth < 1024) {
        calendar.setOption('headerToolbar', {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek'
        });
    }
    
    // Render calendar
    calendar.render();

    // Fix for calendar layout issue on initial load
    window.addEventListener('load', () => {
        setTimeout(() => {
            calendar.updateSize();
        }, 500);
    });
    
    // Store calendar instance globally for potential external access
    window.taskCalendar = calendar;
});

// Refresh calendar after modal actions
function refreshCalendar() {
    if (window.taskCalendar) {
        window.taskCalendar.refetchEvents();
    }
}
</script>
@endpush
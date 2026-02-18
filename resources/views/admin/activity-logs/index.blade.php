@extends('admin.layouts.app')

@section('breadcrumb')
<i class="fas fa-chevron-right text-gray-300 mx-2 text-xs"></i>
<span class="text-emerald-600 font-medium text-sm">Activity Log</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header with Stats -->
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 flex-1">
            <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                        <i class="fas fa-calendar-day text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Hari Ini</p>
                        <p class="text-xl font-bold text-gray-900">{{ number_format($stats['today']) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
                        <i class="fas fa-chart-line text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Minggu Ini</p>
                        <p class="text-xl font-bold text-gray-900">{{ number_format($stats['this_week']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600">
                        <i class="fas fa-user-lock text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Top User</p>
                        @if($stats['top_user'] && $stats['top_user']->user)
                            <p class="text-sm font-bold text-gray-900 truncate max-w-[150px]">{{ $stats['top_user']->user->name }}</p>
                        @else
                            <p class="text-sm font-bold text-gray-400">-</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-start">
            <a href="{{ route('admin.security.activity-logs.archives') }}" 
               class="flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm">
                <i class="fas fa-box-archive text-emerald-500"></i>
                Lihat Arsip Backup
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4" x-data="{ showFilters: false }">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <form action="{{ route('admin.security.activity-logs.index') }}" method="GET" id="search-form">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari log aktivitas, IP, atau deskripsi..." 
                           class="w-full pl-10 pr-4 py-2 bg-gray-50 border-0 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 transition-shadow">
                    
                    @foreach(request()->except(['search', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                </form>
            </div>
            
            <button @click="showFilters = !showFilters" 
                    class="flex items-center gap-2 px-4 py-2 bg-gray-50 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100 transition-colors">
                <i class="fas fa-filter"></i>
                Filter Lanjutan
                <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="showFilters ? 'rotate-180' : ''"></i>
            </button>
        </div>

        <!-- Advanced Filters -->
        <div x-show="showFilters" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-1 md:grid-cols-4 gap-4"
             style="display: none;">
            
            <form id="filter-form" action="{{ route('admin.security.activity-logs.index') }}" method="GET" class="contents">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">User</label>
                    <select name="causer_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-gray-50 border-0 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500">
                        <option value="">Semua User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('causer_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Kategori Log</label>
                    <select name="log_name" onchange="this.form.submit()" class="w-full px-3 py-2 bg-gray-50 border-0 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500">
                        <option value="">Semua Kategori</option>
                        @foreach($logNames as $name)
                            <option value="{{ $name }}" {{ request('log_name') == $name ? 'selected' : '' }}>
                                {{ ucfirst($name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Event</label>
                    <select name="event" onchange="this.form.submit()" class="w-full px-3 py-2 bg-gray-50 border-0 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500">
                        <option value="">Semua Event</option>
                        <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Created</option>
                        <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Updated</option>
                        <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Waktu</label>
                    <div class="flex items-center gap-2">
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 bg-gray-50 border-0 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500">
                        <span class="text-gray-400">-</span>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 bg-gray-50 border-0 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500" onchange="this.form.submit()">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Event</th>
                        <th class="px-6 py-4">Deskripsi</th>
                        <th class="px-6 py-4">IP Address</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-gray-900 font-medium">{{ $log->created_at->format('d M Y') }}</div>
                            <div class="text-xs text-gray-400">{{ $log->created_at->format('H:i:s') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($log->causer)
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($log->causer->name) }}&color=7F9CF5&background=EBF4FF" 
                                         alt="{{ $log->causer->name }}" 
                                         class="w-8 h-8 rounded-full">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $log->causer->name }}</div>
                                        <div class="text-xs text-gray-400 capitalize">{{ $log->causer_type === 'App\Models\User' ? ($log->causer->roles->first()?->name ?? 'User') : 'System' }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-400 italic">System / Unknown</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php $badge = $log->event_badge; @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ match($badge['color']) {
                                    'emerald' => 'bg-emerald-100 text-emerald-800',
                                    'blue' => 'bg-blue-100 text-blue-800',
                                    'red' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                } }}">
                                {{ $badge['label'] }}
                            </span>
                            <span class="ml-2 text-xs text-gray-400 border border-gray-200 rounded px-1.5 py-0.5">{{ $log->log_name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-gray-900 truncate max-w-xs" title="{{ $log->description }}">
                                {{ $log->description }}
                            </p>
                            @if($log->subject_id)
                                <p class="text-xs text-gray-400 mt-0.5">
                                    Subject: {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                                </p>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500 font-mono text-xs">
                            {{ $log->ip_address ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.security.activity-logs.show', $log->id) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 transition-all">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center text-gray-300">
                                    <i class="fas fa-search text-2xl"></i>
                                </div>
                                <p>Tidak ada log aktivitas yang ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

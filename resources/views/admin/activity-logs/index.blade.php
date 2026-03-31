@extends('admin.layouts.app')

@section('breadcrumb')
<i class="fas fa-chevron-right text-gray-300 mx-2 text-xs"></i>
<span class="text-emerald-600 font-medium text-sm">Activity Log</span>
@endsection

@section('content')
<div class="space-y-8 animate-in fade-in duration-500">
    <!-- Header with Stats -->
    <div class="flex flex-col lg:flex-row gap-8 items-end">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 flex-1">
            <div class="group bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:shadow-blue-500/5 transition-all duration-300">
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-calendar-day text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.1em] mb-1">Hari Ini</p>
                        <p class="text-2xl font-black text-gray-900 leading-none">{{ number_format($stats['today']) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="group bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:shadow-purple-500/5 transition-all duration-300">
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-purple-50 flex items-center justify-center text-purple-600 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-clock-rotate-left text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.1em] mb-1">Minggu Ini</p>
                        <p class="text-2xl font-black text-gray-900 leading-none">{{ number_format($stats['this_week']) }}</p>
                    </div>
                </div>
            </div>

            <div class="group bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:shadow-orange-500/5 transition-all duration-300">
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-600 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-user-lock text-2xl"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.1em] mb-1">Top Contributor</p>
                        @if($stats['top_user'] && $stats['top_user']->user)
                            <p class="text-sm font-black text-gray-900 truncate" title="{{ $stats['top_user']->user->name }}">
                                {{ $stats['top_user']->user->name }}
                            </p>
                        @else
                            <p class="text-sm font-black text-gray-400">-</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-wrap items-center gap-4 lg:pb-2">
            <form action="{{ route('admin.security.activity-logs.backup') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin melakukan backup dan menghapus semua log saat ini? File backup akan tersedia di menu Arsip.')">
                @csrf
                <button type="submit" 
                        class="flex items-center gap-2.5 px-6 py-3 bg-emerald-600 text-white rounded-2xl text-sm font-bold hover:bg-emerald-700 hover:-translate-y-0.5 transition-all shadow-lg shadow-emerald-200">
                    <i class="fas fa-file-export opacity-80"></i>
                    Manual Backup & Purge
                </button>
            </form>
            <a href="{{ route('admin.security.activity-logs.archives') }}" 
               class="flex items-center gap-2.5 px-6 py-3 bg-white border border-gray-200 rounded-2xl text-sm font-bold text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm">
                <i class="fas fa-box-archive text-gray-400"></i>
                Arsip Backup
            </a>
        </div>
    </div>

    <!-- Main Container -->
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
        <!-- Toolbar & Filters -->
        <div class="p-6 border-b border-gray-100 bg-gray-50/30" x-data="{ showFilters: false }">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <!-- Search -->
                <div class="flex-1 max-w-xl relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <form action="{{ route('admin.security.activity-logs.index') }}" method="GET">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Cari log (ID, deskripsi, IP)..." 
                               class="w-full pl-12 pr-4 py-3 bg-white border-gray-200 rounded-2xl text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm">
                        
                        @foreach(request()->except(['search', 'page']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                    </form>
                </div>
                
                <div class="flex items-center gap-3">
                    <button @click="showFilters = !showFilters" 
                            class="flex items-center gap-2.5 px-5 py-3 bg-white border border-gray-200 rounded-2xl text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all shadow-sm"
                            :class="showFilters ? 'ring-2 ring-emerald-500/20 text-emerald-600 border-emerald-500/50' : ''">
                        <i class="fas fa-filter text-xs"></i>
                        Filter Lanjutan
                    </button>
                    <a href="{{ route('admin.security.activity-logs.index') }}" class="flex items-center justify-center w-11 h-11 bg-white border border-gray-200 rounded-2xl text-gray-400 hover:text-red-500 hover:border-red-100 transition-all shadow-sm">
                        <i class="fas fa-undo-alt"></i>
                    </a>
                </div>
            </div>

            <!-- Advanced Filters Drawer -->
            <div x-show="showFilters" 
                 x-collapse
                 class="mt-6 pt-6 border-t border-gray-100">
                
                <form id="filter-form" action="{{ route('admin.security.activity-logs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">User</label>
                        <select name="causer_id" onchange="this.form.submit()" class="w-full px-4 py-3 bg-white border-gray-200 rounded-2xl text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all">
                            <option value="">Semua User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('causer_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Namespace</label>
                        <select name="log_name" onchange="this.form.submit()" class="w-full px-4 py-3 bg-white border-gray-200 rounded-2xl text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all">
                            <option value="">Semua Kategori</option>
                            @foreach($logNames as $name)
                                <option value="{{ $name }}" {{ request('log_name') == $name ? 'selected' : '' }}>
                                    {{ ucfirst($name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Event</label>
                        <select name="event" onchange="this.form.submit()" class="w-full px-4 py-3 bg-white border-gray-200 rounded-2xl text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all">
                            <option value="">Semua Event</option>
                            <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Created</option>
                            <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Updated</option>
                            <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Rentang Waktu</label>
                        <div class="flex items-center gap-2">
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-3 bg-white border-gray-200 rounded-2xl text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all">
                            <span class="text-gray-300">-</span>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-3 bg-white border-gray-200 rounded-2xl text-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all" onchange="this.form.submit()">
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto overflow-y-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50/50 text-gray-400 font-bold uppercase tracking-widest text-[10px] border-b border-gray-100">
                    <tr>
                        <th class="px-8 py-5">Waktu</th>
                        <th class="px-8 py-5">User</th>
                        <th class="px-8 py-5">Event</th>
                        <th class="px-8 py-5">Deskripsi</th>
                        <th class="px-8 py-5">IP Address</th>
                        <th class="px-8 py-5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50/70 transition-all duration-200 group">
                        <td class="px-8 py-5 whitespace-nowrap">
                            <div class="text-gray-900 font-bold">{{ $log->created_at->format('d M Y') }}</div>
                            <div class="text-[11px] text-gray-400 font-medium">{{ $log->created_at->format('H:i:s') }}</div>
                        </td>
                        <td class="px-8 py-5 whitespace-nowrap">
                            @if($log->causer)
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($log->causer->name) }}&color=7F9CF5&background=EBF4FF" 
                                             alt="{{ $log->causer->name }}" 
                                             class="w-10 h-10 rounded-full border-2 border-white shadow-sm">
                                        <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></div>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $log->causer->name }}</div>
                                        <div class="text-[11px] text-gray-500 font-medium capitalize">{{ $log->causer_type === 'App\Models\User' ? ($log->causer->roles->first()?->name ?? 'User') : 'System' }}</div>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center gap-3 grayscale opacity-60">
                                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 border border-dashed border-gray-300">
                                        <i class="fas fa-robot"></i>
                                    </div>
                                    <span class="text-gray-400 italic font-medium">System Automated</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-8 py-5 whitespace-nowrap text-xs font-mono">
                            @php $badge = $log->event_badge; @endphp
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full font-bold uppercase tracking-wider
                                    {{ match($badge['color']) {
                                        'emerald' => 'bg-emerald-50 text-emerald-600',
                                        'blue' => 'bg-blue-50 text-blue-600',
                                        'red' => 'bg-red-50 text-red-600',
                                        default => 'bg-gray-50 text-gray-600'
                                    } }}">
                                    {{ $badge['label'] }}
                                </span>
                                <span class="px-2 py-0.5 text-[10px] bg-slate-100 text-slate-500 rounded font-bold">{{ $log->log_name }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <p class="text-gray-800 font-medium leading-relaxed max-w-md line-clamp-1 group-hover:line-clamp-none transition-all duration-300">
                                {{ $log->description }}
                            </p>
                            @if($log->subject_id)
                                <div class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-gray-100 rounded-md text-[10px] text-gray-500 mt-1.5 font-bold uppercase tracking-tight">
                                    <i class="fas fa-tag opacity-40"></i>
                                    {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                                </div>
                            @endif
                        </td>
                        <td class="px-8 py-5 whitespace-nowrap text-gray-500 font-mono text-[11px] font-bold">
                            {{ $log->ip_address ?? '::1' }}
                        </td>
                        <td class="px-8 py-5 text-center">
                            <a href="{{ route('admin.security.activity-logs.show', $log->id) }}" 
                               class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-white border border-gray-100 text-gray-400 hover:text-emerald-600 hover:border-emerald-100 hover:shadow-lg hover:shadow-emerald-500/10 transition-all">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center justify-center gap-4">
                                <div class="w-24 h-24 bg-gray-50 rounded-[2.5rem] flex items-center justify-center text-gray-200">
                                    <i class="fas fa-receipt text-4xl"></i>
                                </div>
                                <div>
                                    <p class="text-lg font-black text-gray-900">Belum ada aktivitas</p>
                                    <p class="text-sm text-gray-400">Jalankan aplikasi untuk mulai melihat riwayat perubahan data.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($logs->hasPages())
        <div class="px-8 py-6 border-t border-gray-50 bg-gray-50/20">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

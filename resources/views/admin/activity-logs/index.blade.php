@extends('admin.layouts.app')

@section('title', 'Activity Log')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Activity Log</span>
</li>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-list-ul text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Activity Log</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium">Pantau dan audit seluruh riwayat aktivitas sistem</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <form action="{{ route('admin.security.activity-logs.backup') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin melakukan backup dan menghapus semua log saat ini? File backup akan tersedia di menu Arsip.')">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition-all duration-200 shadow-sm shadow-emerald-200">
                    <i class="fas fa-file-export text-xs opacity-80"></i>
                    <span>Manual Backup</span>
                </button>
            </form>
            <a href="{{ route('admin.security.activity-logs.archives') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-all duration-200 shadow-sm">
                <i class="fas fa-box-archive text-xs text-gray-400"></i>
                <span>Arsip Log</span>
            </a>
        </div>
    </div>

    <!-- Statistik -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Log Hari Ini</p>
                    <p class="mt-1 text-2xl font-semibold text-blue-600">{{ number_format($stats['today']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center border border-blue-100">
                    <i class="fas fa-calendar-day text-blue-500 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Log Minggu Ini</p>
                    <p class="mt-1 text-2xl font-semibold text-purple-600">{{ number_format($stats['this_week']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center border border-purple-100">
                    <i class="fas fa-chart-line text-purple-500 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Log Aktif</p>
                    <p class="mt-1 text-2xl font-semibold text-emerald-600">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100">
                    <i class="fas fa-database text-emerald-500 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Top Contributor</p>
                    <p class="mt-1 text-sm font-bold text-gray-900 truncate max-w-[120px]">
                        {{ $stats['top_user']?->user?->name ?? '-' }}
                    </p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-orange-50 flex items-center justify-center border border-orange-100">
                    <i class="fas fa-user-shield text-orange-500 text-lg"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Konten Utama -->
    <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        {{-- Toolbar: Search & Filter --}}
        <div class="border-b border-gray-200 px-4 md:px-6 py-5 bg-gray-50/50">
            <form action="{{ route('admin.security.activity-logs.index') }}" method="GET" class="space-y-4 lg:space-y-0 lg:flex lg:items-end lg:justify-between gap-4">
                <div class="flex-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div class="col-span-1 md:col-span-2">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Cari Log / IP</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari deskripsi, IP..."
                                   class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-300 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-500 mb-1 block">User</label>
                        <select name="causer_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                            <option value="">Semua User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('causer_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Event</label>
                        <select name="event" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                            <option value="">Semua Event</option>
                            <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Created</option>
                            <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Updated</option>
                            <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Namespace</label>
                        <select name="log_name" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                            <option value="">Semua Kategori</option>
                            @foreach($logNames as $name)
                                <option value="{{ $name }}" {{ request('log_name') == $name ? 'selected' : '' }}>
                                    {{ ucfirst($name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-800 text-white hover:bg-gray-700 transition-all shadow-sm">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request()->anyFilled(['search', 'causer_id', 'event', 'log_name']))
                        <a href="{{ route('admin.security.activity-logs.index') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-white border border-gray-300 text-gray-500 hover:bg-gray-50 transition-all shadow-sm" title="Reset">
                            <i class="fas fa-undo text-xs"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-[11px] text-gray-500 uppercase font-bold border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4">Waktu / ID</th>
                        <th class="px-6 py-4">Pengguna</th>
                        <th class="px-6 py-4">Event & Kategori</th>
                        <th class="px-6 py-4">Deskripsi</th>
                        <th class="px-6 py-4">IP Address</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="font-bold text-gray-900 text-sm">#{{ $log->id }}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5 uppercase tracking-tighter">{{ $log->created_at->isoFormat('D MMM Y, HH:mm') }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($log->causer)
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($log->causer->name) }}&color=7F9CF5&background=EBF4FF" 
                                             class="h-8 w-8 rounded-full border border-gray-100 object-cover shadow-sm">
                                        <div class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full"></div>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-gray-900 truncate leading-tight">{{ $log->causer->name }}</p>
                                        <p class="text-[10px] text-emerald-600 truncate font-medium">{{ $log->causer->roles->first()?->name ?? 'User' }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center gap-3 opacity-50 grayscale">
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 border border-gray-200">
                                        <i class="fas fa-robot text-xs"></i>
                                    </div>
                                    <p class="text-xs font-medium text-gray-400 italic">System</p>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php $badge = $log->event_badge; @endphp
                            <div class="flex flex-col gap-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider w-fit
                                    {{ match($badge['color']) {
                                        'emerald' => 'bg-emerald-50 text-emerald-600',
                                        'blue' => 'bg-blue-50 text-blue-600',
                                        'red' => 'bg-red-50 text-red-600',
                                        default => 'bg-gray-50 text-gray-600'
                                    } }}">
                                    {{ $badge['label'] }}
                                </span>
                                <span class="text-[10px] text-gray-400 font-medium">@ {{ $log->log_name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-800 leading-tight max-w-xs line-clamp-1 hover:line-clamp-none transition-all duration-300" title="{{ $log->description }}">
                                {{ $log->description }}
                            </p>
                            @if($log->subject_id)
                                <p class="text-[9px] text-gray-400 mt-1 uppercase font-bold tracking-tighter">
                                    {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                                </p>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-mono text-[11px] text-gray-500">
                            {{ $log->ip_address ?? '::1' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.security.activity-logs.show', $log->id) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" 
                               title="Lihat Detail">
                                <i class="fas fa-eye text-sm"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-dashed border-gray-200">
                                    <i class="fas fa-receipt text-2xl text-gray-300"></i>
                                </div>
                                <p class="text-gray-500 font-medium">Tidak ada log ditemukan</p>
                                <p class="text-xs text-gray-400 mt-1">Coba sesuaikan filter pencarian Anda</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $logs->links() }}
        </div>
        @endif
    </section>
</div>
@endsection

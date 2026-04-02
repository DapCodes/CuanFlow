@extends('admin.layouts.app')

@section('title', 'System Error Log')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">System Error Log</span>
</li>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-red-50 rounded-2xl flex items-center justify-center text-red-600 shadow-sm shadow-red-100/50">
                <i class="fas fa-bug text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">System Error Log</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium">Pantau pesan error dan peringatan dari Laravel sistem</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <form action="{{ route('admin.security.error-logs.backup') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin melakukan backup dan menghapus semua log saat ini? File backup akan tersedia di menu Arsip.')">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition-all duration-200 shadow-sm shadow-emerald-200">
                    <i class="fas fa-file-export text-xs opacity-80"></i>
                    <span>Manual Backup</span>
                </button>
            </form>
            <a href="{{ route('admin.security.error-logs.archives') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-all duration-200 shadow-sm">
                <i class="fas fa-box-archive text-xs text-gray-400"></i>
                <span>Arsip Log</span>
            </a>
            <form action="{{ route('admin.security.error-logs.clear') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua error log secara permanen?');" class="ml-2">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center justify-center w-10 h-10 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 transition-all duration-200 shadow-sm" title="Bersihkan Logs Tanpa Backup">
                    <i class="fas fa-trash-can text-sm"></i>
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 dark:bg-gray-800 dark:text-emerald-400" role="alert">
            <span class="font-medium">Berhasil!</span> {{ session('success') }}
        </div>
    @endif

    <!-- Statistik -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Log</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100">
                    <i class="fas fa-database text-gray-500 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Errors</p>
                    <p class="mt-1 text-2xl font-semibold text-red-600">{{ number_format($stats['errors']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center border border-red-100">
                    <i class="fas fa-bug text-red-500 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Warnings</p>
                    <p class="mt-1 text-2xl font-semibold text-orange-600">{{ number_format($stats['warnings']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-orange-50 flex items-center justify-center border border-orange-100">
                    <i class="fas fa-exclamation-triangle text-orange-500 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Info</p>
                    <p class="mt-1 text-2xl font-semibold text-blue-600">{{ number_format($stats['info']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center border border-blue-100">
                    <i class="fas fa-info-circle text-blue-500 text-lg"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Konten Utama -->
    <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        {{-- Toolbar: Search & Filter --}}
        <div class="border-b border-gray-200 px-4 md:px-6 py-5 bg-gray-50/50">
            <form action="{{ route('admin.security.error-logs.index') }}" method="GET" class="space-y-4 lg:space-y-0 lg:flex lg:items-end lg:justify-between gap-4">
                <div class="flex-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="col-span-1 md:col-span-2">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Cari Keyword Error</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pesan error, warning..."
                                   class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-300 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Level Log</label>
                        <select name="level" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
                            <option value="">Semua Level</option>
                            <option value="ERROR" {{ request('level') == 'ERROR' ? 'selected' : '' }}>Error</option>
                            <option value="WARNING" {{ request('level') == 'WARNING' ? 'selected' : '' }}>Warning</option>
                            <option value="INFO" {{ request('level') == 'INFO' ? 'selected' : '' }}>Info</option>
                            <option value="DEBUG" {{ request('level') == 'DEBUG' ? 'selected' : '' }}>Debug</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-800 text-white hover:bg-gray-700 transition-all shadow-sm">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request()->anyFilled(['search', 'level']))
                        <a href="{{ route('admin.security.error-logs.index') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-white border border-gray-300 text-gray-500 hover:bg-gray-50 transition-all shadow-sm" title="Reset">
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
                        <th class="px-6 py-4 w-48">Waktu & Env</th>
                        <th class="px-6 py-4 w-32">Level</th>
                        <th class="px-6 py-4 w-auto">Pesan Error</th>
                        <th class="px-6 py-4 w-16 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($paginatedLogs as $index => $log)
                    <tr class="hover:bg-gray-50 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="font-bold text-gray-900 text-sm tracking-tight">{{ \Carbon\Carbon::parse($log['date'])->isoFormat('D MMM Y, HH:mm:ss') }}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5 uppercase tracking-wider font-semibold">{{ $log['env'] }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php $badge = $log['badge']; @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider
                                {{ match($badge['color']) {
                                    'emerald' => 'bg-emerald-50 text-emerald-600 border border-emerald-100',
                                    'blue' => 'bg-blue-50 text-blue-600 border border-blue-100',
                                    'red' => 'bg-red-50 text-red-600 border border-red-100',
                                    'orange' => 'bg-orange-50 text-orange-600 border border-orange-100',
                                    default => 'bg-gray-50 text-gray-600 border border-gray-200'
                                } }}">
                                @if($badge['color'] == 'red')
                                    <i class="fas fa-times-circle"></i>
                                @elseif($badge['color'] == 'orange')
                                    <i class="fas fa-exclamation-triangle"></i>
                                @elseif($badge['color'] == 'blue')
                                    <i class="fas fa-info-circle"></i>
                                @else
                                    <i class="fas fa-circle text-[8px]"></i>
                                @endif
                                {{ $badge['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 relative" x-data="{ expanded: false }">
                            <div class="relative">
                                <div class="font-mono text-[11px] leading-relaxed text-gray-700 bg-gray-50 rounded-lg p-3 border border-gray-200 break-words whitespace-pre-wrap overflow-hidden transition-all duration-300"
                                     :class="expanded ? 'max-h-[500px] overflow-y-auto' : 'max-h-[60px]' ">
                                    {{ $log['message'] }}
                                </div>
                                <button @click="expanded = !expanded" 
                                        class="absolute bottom-1 right-2 text-[10px] font-bold text-emerald-600 hover:text-emerald-700 bg-gray-50/90 px-2 py-0.5 rounded shadow-sm opacity-0 group-hover:opacity-100 transition-opacity"
                                        x-show="$el.previousElementSibling.scrollHeight > 60">
                                    <span x-text="expanded ? 'Tutup' : 'Lihat Semua'"></span>
                                </button>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="navigator.clipboard.writeText(`{{ addslashes($log['message']) }}`); alert('Copied to clipboard!');" 
                               class="inline-flex items-center justify-center w-8 h-8 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" 
                               title="Copy ke Clipboard">
                                <i class="fas fa-copy text-sm"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center mb-4 border border-emerald-100 shadow-sm shadow-emerald-100/50">
                                    <i class="fas fa-check-circle text-2xl text-emerald-500"></i>
                                </div>
                                <p class="text-gray-900 font-semibold text-lg">Semua Aman!</p>
                                <p class="text-sm text-gray-500 mt-1 max-w-sm">Tidak ada error log yang ditemukan. Sistem Anda berjalan dengan lancar saat ini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($paginatedLogs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $paginatedLogs->links() }}
        </div>
        @endif
    </section>
</div>

<!-- Alpine.js is required for the expanded view. Make sure it's included in app.layout, else the copy functionality handles most user needs. -->
@endsection

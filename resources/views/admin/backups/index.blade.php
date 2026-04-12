@extends('admin.layouts.app')

@section('title', 'Backup Manager')
@section('page-title', 'Backup Manager')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Backup Manager</span>
</li>
@endsection

@section('content')
<div class="px-4 lg:px-6 space-y-6 pb-20" x-data="{ showRunDropdown: false, deleteId: null }">

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- HEADER --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-shield-halved text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight uppercase">Backup Manager</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium italic">Kelola backup database & file ke Google Drive</p>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            {{-- Run Backup Dropdown --}}
            <div class="relative" @click.outside="showRunDropdown = false">
                <button @click="showRunDropdown = !showRunDropdown"
                        class="inline-flex items-center justify-center px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm shadow-emerald-200/50 active:scale-95">
                    <i class="fas fa-play mr-2"></i> Run Backup
                    <i class="fas fa-chevron-down ml-2 text-[10px] transition-transform" :class="showRunDropdown ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="showRunDropdown"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     style="display: none;"
                     class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden">
                    <div class="p-1.5">
                        <form action="{{ route('admin.backups.run') }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="full">
                            <button type="submit" @click="showRunDropdown = false"
                                    class="w-full flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 rounded-lg transition-colors">
                                <i class="fas fa-database text-emerald-500 w-5 text-center"></i>
                                <div class="text-left">
                                    <p class="font-semibold">Full Backup</p>
                                    <p class="text-[10px] text-gray-400">Database + Files</p>
                                </div>
                            </button>
                        </form>
                        <form action="{{ route('admin.backups.run') }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="database">
                            <button type="submit" @click="showRunDropdown = false"
                                    class="w-full flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-lg transition-colors">
                                <i class="fas fa-server text-blue-500 w-5 text-center"></i>
                                <div class="text-left">
                                    <p class="font-semibold">Database Only</p>
                                    <p class="text-[10px] text-gray-400">MySQL dump saja</p>
                                </div>
                            </button>
                        </form>
                        <form action="{{ route('admin.backups.run') }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="files">
                            <button type="submit" @click="showRunDropdown = false"
                                    class="w-full flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-700 rounded-lg transition-colors">
                                <i class="fas fa-folder text-amber-500 w-5 text-center"></i>
                                <div class="text-left">
                                    <p class="font-semibold">Files Only</p>
                                    <p class="text-[10px] text-gray-400">Storage files saja</p>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Clean Old Backups --}}
            <form action="{{ route('admin.backups.run') }}" method="POST"
                  onsubmit="return false;"
                  x-data
                  x-ref="cleanForm">
                @csrf
                <a href="javascript:void(0)"
                   onclick="if(confirm('Jalankan pembersihan backup lama sesuai kebijakan retensi?')) { window.location.href='#'; }"
                   class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-gray-200 text-gray-600 hover:text-red-600 hover:border-red-200 hover:bg-red-50 text-sm font-semibold rounded-xl transition-all shadow-sm">
                    <i class="fas fa-broom mr-2"></i> Clean Old
                </a>
            </form>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- GOOGLE DRIVE STATUS ALERT --}}
    {{-- ═══════════════════════════════════════════════ --}}
    @if(!$googleDriveConfigured)
    <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-xl shadow-sm">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-amber-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-amber-700 font-bold">Google Drive Belum Dikonfigurasi</p>
                <p class="text-xs text-amber-600 mt-1">Backup akan disimpan secara lokal. Konfigurasikan Service Account Google Drive dan set <code class="bg-amber-100 px-1 rounded">GOOGLE_DRIVE_FOLDER_ID</code> di file .env untuk mengaktifkan upload otomatis.</p>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- STATISTICS CARDS --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Backups --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Total Backup</p>
                    <p class="mt-1 text-2xl font-black text-gray-900">{{ number_format($totalBackups) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center border border-indigo-100 shadow-sm shadow-indigo-100/50">
                    <i class="fas fa-archive text-indigo-500 text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Last Successful Backup --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Backup Terakhir</p>
                    @if($latestBackup)
                        <p class="mt-1 text-lg font-black text-emerald-600">{{ $latestBackup->created_at->diffForHumans() }}</p>
                        <p class="text-[9px] text-gray-400">{{ $latestBackup->created_at->format('d M Y H:i') }}</p>
                    @else
                        <p class="mt-1 text-lg font-black text-gray-400">Belum ada</p>
                    @endif
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center border border-emerald-100 shadow-sm shadow-emerald-100/50">
                    <i class="fas fa-clock text-emerald-500 text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Total Size --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Total Ukuran</p>
                    <p class="mt-1 text-2xl font-black text-gray-900">
                        @php
                            $sizeUnits = ['B', 'KB', 'MB', 'GB'];
                            $sizeVal = $totalSize;
                            $sizeIdx = 0;
                            while ($sizeVal >= 1024 && $sizeIdx < count($sizeUnits) - 1) {
                                $sizeVal /= 1024;
                                $sizeIdx++;
                            }
                        @endphp
                        {{ round($sizeVal, 2) }} {{ $sizeUnits[$sizeIdx] }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center border border-blue-100 shadow-sm shadow-blue-100/50">
                    <i class="fas fa-hard-drive text-blue-500 text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Failed Backups --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Gagal</p>
                    <p class="mt-1 text-2xl font-black {{ $failedBackups > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ number_format($failedBackups) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl {{ $failedBackups > 0 ? 'bg-red-50 border-red-100 shadow-red-100/50' : 'bg-gray-50 border-gray-100' }} flex items-center justify-center border shadow-sm">
                    <i class="fas fa-times-circle {{ $failedBackups > 0 ? 'text-red-500' : 'text-gray-400' }} text-xl"></i>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- LATEST BACKUP HIGHLIGHT --}}
    {{-- ═══════════════════════════════════════════════ --}}
    @if($latestBackup)
    <div class="bg-gradient-to-r from-emerald-50 via-emerald-50/50 to-white border border-emerald-100 rounded-2xl p-5 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center shadow-sm shadow-emerald-200/50">
                    <i class="fas fa-check-circle text-emerald-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-600">Backup Terakhir Berhasil</p>
                    <p class="text-sm font-bold text-gray-900 mt-1 break-all">{{ $latestBackup->filename }}</p>
                    <div class="flex items-center gap-3 mt-1.5 flex-wrap">
                        <span class="text-[10px] font-semibold text-gray-500">
                            <i class="fas fa-calendar-alt text-gray-400 mr-1"></i>
                            {{ $latestBackup->created_at->format('d M Y, H:i') }}
                        </span>
                        <span class="text-[10px] font-semibold text-gray-500">
                            <i class="fas fa-weight-scale text-gray-400 mr-1"></i>
                            {{ $latestBackup->getSizeForHumans() }}
                        </span>
                        @if($latestBackup->is_encrypted)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[9px] font-bold bg-emerald-100 text-emerald-700 rounded-full">
                            <i class="fas fa-lock text-[8px]"></i> Terenkripsi
                        </span>
                        @endif
                        @if($latestBackup->disk === 'google')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[9px] font-bold bg-blue-100 text-blue-700 rounded-full">
                            <i class="fab fa-google-drive text-[8px]"></i> Google Drive
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.backups.download', $latestBackup) }}"
               class="inline-flex items-center justify-center px-4 py-2 bg-white border border-emerald-200 text-emerald-700 hover:bg-emerald-600 hover:text-white text-sm font-semibold rounded-xl transition-all shadow-sm active:scale-95">
                <i class="fas fa-download mr-2"></i> Download
            </a>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- BACKUP HISTORY TABLE --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="border-b border-gray-200 px-4 md:px-6 py-4 bg-gray-50/50">
            <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wide">
                <i class="fas fa-history text-gray-400 mr-2"></i>Riwayat Backup
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left">File</th>
                        <th class="px-6 py-4 text-left">Tipe</th>
                        <th class="px-6 py-4 text-left">Ukuran</th>
                        <th class="px-6 py-4 text-left">Status</th>
                        <th class="px-6 py-4 text-left">Disk</th>
                        <th class="px-6 py-4 text-left">Tanggal</th>
                        <th class="px-6 py-4 text-center font-black">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($backups as $backup)
                    <tr class="hover:bg-gray-50 transition-colors group">
                        {{-- Filename --}}
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg {{ $backup->status === 'completed' ? 'bg-emerald-50 text-emerald-500' : 'bg-red-50 text-red-500' }} flex items-center justify-center flex-shrink-0">
                                    <i class="fas {{ $backup->status === 'completed' ? 'fa-file-zipper' : 'fa-file-circle-xmark' }} text-sm"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-gray-900 text-xs truncate max-w-[200px]" title="{{ $backup->filename }}">
                                        {{ $backup->filename ?: 'unknown' }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        @if($backup->is_encrypted)
                                        <span class="inline-flex items-center gap-0.5 text-[9px] font-semibold text-amber-600">
                                            <i class="fas fa-lock text-[7px]"></i> AES-256
                                        </span>
                                        @endif
                                        @if($backup->checksum)
                                        <span class="text-[9px] font-mono text-gray-300" title="SHA-256: {{ $backup->checksum }}">
                                            {{ substr($backup->checksum, 0, 12) }}…
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Type --}}
                        <td class="px-6 py-5">
                            @php
                                $typeLabel = match($backup->type) {
                                    'full', 'gdrive_full' => ['Full', 'bg-indigo-50 text-indigo-600 border-indigo-100'],
                                    'database', 'gdrive_database' => ['Database', 'bg-blue-50 text-blue-600 border-blue-100'],
                                    'files', 'gdrive_files' => ['Files', 'bg-amber-50 text-amber-600 border-amber-100'],
                                    'activity_log' => ['Activity Log', 'bg-purple-50 text-purple-600 border-purple-100'],
                                    default => [$backup->type, 'bg-gray-50 text-gray-600 border-gray-100'],
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 text-[10px] font-bold rounded-lg border {{ $typeLabel[1] }}">
                                {{ $typeLabel[0] }}
                            </span>
                        </td>

                        {{-- Size --}}
                        <td class="px-6 py-5">
                            <span class="text-xs font-semibold text-gray-700">
                                {{ $backup->size > 0 ? $backup->getSizeForHumans() : '—' }}
                            </span>
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-5">
                            @if($backup->status === 'completed')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Success
                                </span>
                            @else
                                <div x-data="{ showError: false }" class="relative">
                                    <span @mouseenter="showError = true" @mouseleave="showError = false"
                                          class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-lg bg-red-50 text-red-700 border border-red-100 cursor-help">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                        Failed
                                    </span>
                                    @if($backup->error_message)
                                    <div x-show="showError"
                                         x-transition
                                         style="display: none;"
                                         class="absolute bottom-full left-0 mb-2 w-72 z-50">
                                        <div class="bg-gray-900 text-white text-[10px] rounded-xl p-3 shadow-xl">
                                            <p class="font-bold text-red-300 mb-1">Error Detail:</p>
                                            <p class="break-words leading-relaxed">{{ Str::limit($backup->error_message, 200) }}</p>
                                            <div class="absolute bottom-0 left-4 w-2 h-2 bg-gray-900 transform rotate-45 translate-y-1"></div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            @endif
                        </td>

                        {{-- Disk --}}
                        <td class="px-6 py-5">
                            @if($backup->disk === 'google')
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 text-[10px] font-bold rounded-lg bg-blue-50 text-blue-600 border border-blue-100">
                                    <i class="fab fa-google-drive text-[9px]"></i> Drive
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 text-[10px] font-bold rounded-lg bg-gray-50 text-gray-500 border border-gray-100">
                                    <i class="fas fa-server text-[9px]"></i> Local
                                </span>
                            @endif
                        </td>

                        {{-- Date --}}
                        <td class="px-6 py-5">
                            <p class="text-xs text-gray-600 font-medium">{{ $backup->created_at->diffForHumans() }}</p>
                            <p class="text-[9px] text-gray-400">{{ $backup->created_at->format('d M Y H:i:s') }}</p>
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                @if($backup->status === 'completed')
                                    {{-- Download --}}
                                    <a href="{{ route('admin.backups.download', $backup) }}"
                                       class="w-8 h-8 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-600 hover:text-white shadow-sm transition-all active:scale-95 border border-blue-100"
                                       title="Download">
                                        <i class="fas fa-download text-xs"></i>
                                    </a>
                                @else
                                    {{-- Retry --}}
                                    <form action="{{ route('admin.backups.retry', $backup) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="w-8 h-8 flex items-center justify-center rounded-xl bg-indigo-50 text-indigo-500 hover:bg-indigo-600 hover:text-white shadow-sm transition-all active:scale-95 border border-indigo-100"
                                                title="Retry Backup">
                                            <i class="fas fa-rotate-right text-xs"></i>
                                        </button>
                                    </form>
                                @endif

                                {{-- Delete --}}
                                <form action="{{ route('admin.backups.destroy', $backup) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Hapus backup ini? File di Google Drive juga akan dihapus.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-8 h-8 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-600 hover:text-white shadow-sm transition-all active:scale-95 border border-red-100"
                                            title="Hapus Backup">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gray-50 border border-dashed border-gray-200 rounded-full flex items-center justify-center mb-6">
                                    <i class="fas fa-shield-halved text-gray-200 text-3xl"></i>
                                </div>
                                <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">Belum Ada Backup</h3>
                                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-widest mt-2 max-w-xs mx-auto italic">
                                    Klik "Run Backup" untuk membuat backup pertama Anda.
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($backups->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $backups->links() }}
        </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- CONFIGURATION INFO --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="border-b border-gray-200 px-4 md:px-6 py-4 bg-gray-50/50">
            <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wide">
                <i class="fas fa-cog text-gray-400 mr-2"></i>Konfigurasi Backup
            </h2>
        </div>
        <div class="p-4 md:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
                        <i class="fas fa-clock text-indigo-500 text-xs"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Jadwal</p>
                        <p class="text-xs font-bold text-gray-800 capitalize">{{ config('cuanflow-backup.frequency', 'daily') }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                        <i class="fas fa-lock text-emerald-500 text-xs"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Enkripsi</p>
                        <p class="text-xs font-bold text-gray-800">{{ config('cuanflow-backup.encryption.enabled') ? 'AES-256-CBC' : 'Nonaktif' }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <i class="fab fa-google-drive text-blue-500 text-xs"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Google Drive</p>
                        <p class="text-xs font-bold {{ $googleDriveConfigured ? 'text-emerald-600' : 'text-amber-600' }}">
                            {{ $googleDriveConfigured ? 'Terhubung' : 'Belum dikonfigurasi' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                        <i class="fas fa-calendar-xmark text-amber-500 text-xs"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Retensi</p>
                        <p class="text-xs font-bold text-gray-800">{{ config('cuanflow-backup.retention.daily_backups', 7) }}d / {{ config('cuanflow-backup.retention.weekly_backups', 4) }}w</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

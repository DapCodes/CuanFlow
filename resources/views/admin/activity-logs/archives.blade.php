@extends('admin.layouts.app')

@section('breadcrumb')
<a href="{{ route('admin.security.activity-logs.index') }}" class="flex items-center hover:text-emerald-600 transition-colors">
    <i class="fas fa-chevron-right text-gray-300 mx-2 text-xs"></i>
    <span class="text-sm font-medium text-gray-500">Activity Log</span>
</a>
<i class="fas fa-chevron-right text-gray-300 mx-2 text-xs"></i>
<span class="text-emerald-600 font-medium text-sm">Arsip Backup</span>
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Arsip Activity Log</h1>
            <p class="text-gray-500 text-sm mt-2 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Koleksi file JSON berisi riwayat aktivitas yang telah dipindahkan dari database utama.
            </p>
        </div>
        <a href="{{ route('admin.security.activity-logs.index') }}" class="group flex items-center gap-2 px-6 py-3 bg-white border border-gray-200 rounded-2xl text-sm font-bold text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm">
            <i class="fas fa-arrow-left text-xs group-hover:-translate-x-1 transition-transform"></i>
            Kembali ke Log Aktif
        </a>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-blue-50/50 border border-blue-100 rounded-[2rem] p-6 relative overflow-hidden group">
            <i class="fas fa-database absolute -right-4 -top-4 text-6xl text-blue-500/10 group-hover:rotate-12 transition-transform"></i>
            <p class="text-[10px] font-black text-blue-600/60 uppercase tracking-widest mb-1">Total Arsip</p>
            <p class="text-3xl font-black text-blue-900">{{ $archives->total() }}</p>
        </div>
        <div class="bg-emerald-50/50 border border-emerald-100 rounded-[2rem] p-6 relative overflow-hidden group">
            <i class="fas fa-hdd absolute -right-4 -top-4 text-6xl text-emerald-500/10 group-hover:rotate-12 transition-transform"></i>
            <p class="text-[10px] font-black text-emerald-600/60 uppercase tracking-widest mb-1">Tipe Backup</p>
            <p class="text-3xl font-black text-emerald-900">JSON Format</p>
        </div>
        <div class="bg-amber-50/50 border border-amber-100 rounded-[2rem] p-6 relative overflow-hidden group">
            <i class="fas fa-shield-halved absolute -right-4 -top-4 text-6xl text-amber-500/10 group-hover:rotate-12 transition-transform"></i>
            <p class="text-[10px] font-black text-amber-600/60 uppercase tracking-widest mb-1">Masa Simpan</p>
            <p class="text-3xl font-black text-amber-900">Permanen</p>
        </div>
    </div>

    <!-- Archives Table -->
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50/50 text-gray-400 font-bold uppercase tracking-widest text-[10px] border-b border-gray-100">
                    <tr>
                        <th class="px-8 py-5">Informasi File</th>
                        <th class="px-8 py-5">Ukuran</th>
                        <th class="px-8 py-5">Dibuat Oleh</th>
                        <th class="px-8 py-5">Status</th>
                        <th class="px-8 py-5 text-right">Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($archives as $archive)
                    <tr class="hover:bg-gray-50/50 transition-all duration-200 group">
                        <td class="px-8 py-5 whitespace-nowrap">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 group-hover:bg-emerald-50 group-hover:text-emerald-500 transition-colors">
                                    <i class="fas fa-file-code"></i>
                                </div>
                                <div>
                                    <div class="text-gray-900 font-bold font-mono text-xs">{{ $archive->filename }}</div>
                                    <div class="text-[11px] text-gray-400 font-medium">{{ $archive->created_at->format('d M Y, H:i') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5 whitespace-nowrap">
                            <span class="px-3 py-1 bg-gray-100 rounded-full text-[10px] font-black text-gray-600 uppercase tracking-tight">
                                {{ $archive->getSizeForHumans() }}
                            </span>
                        </td>
                        <td class="px-8 py-5 whitespace-nowrap">
                            @if($archive->createdBy)
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-[10px] font-bold text-blue-600">
                                        {{ substr($archive->createdBy->name, 0, 1) }}
                                    </div>
                                    <span class="text-xs font-bold text-gray-700">{{ $archive->createdBy->name }}</span>
                                </div>
                            @else
                                <span class="text-xs font-medium text-gray-400 italic">System Auto</span>
                            @endif
                        </td>
                        <td class="px-8 py-5 whitespace-nowrap">
                            @if($archive->status === 'completed')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-800">
                                    <div class="w-1 h-1 rounded-full bg-emerald-500 animate-pulse"></div> Selesai
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle text-[8px]"></i> Gagal
                                </span>
                            @endif
                        </td>
                        <td class="px-8 py-5 text-right">
                            @if($archive->status === 'completed')
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.security.activity-logs.archives.view', $archive->id) }}" 
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-xl text-[11px] font-black uppercase tracking-wider hover:bg-blue-100 transition-all">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('admin.security.activity-logs.archives.download', $archive->id) }}" 
                                   class="inline-flex items-center justify-center w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-100 transition-all">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                            @else
                            <div class="text-xs text-red-400 font-medium italic" title="{{ $archive->error_message }}">
                                Terjadi kesalahan
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center justify-center gap-4">
                                <div class="w-24 h-24 bg-gray-50 rounded-[2.5rem] flex items-center justify-center text-gray-200">
                                    <i class="fas fa-box-open text-4xl"></i>
                                </div>
                                <div>
                                    <p class="text-lg font-black text-gray-900">Belum ada arsip</p>
                                    <p class="text-sm text-gray-400">Arsip akan muncul setelah Anda melakukan manual backup.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($archives->hasPages())
        <div class="px-8 py-6 border-t border-gray-50 bg-gray-50/20">
            {{ $archives->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

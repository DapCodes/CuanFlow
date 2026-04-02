@extends('admin.layouts.app')

@section('title', 'Detail Arsip JSON Error Log')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <a href="{{ route('admin.security.error-logs.index') }}" class="text-gray-500 hover:text-emerald-600 transition-colors text-sm font-medium">System Error Log</a>
</li>
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <a href="{{ route('admin.security.error-logs.archives') }}" class="text-gray-500 hover:text-emerald-600 transition-colors text-sm font-medium">Arsip</a>
</li>
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Lihat JSON</span>
</li>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 shadow-sm shadow-blue-100/50">
                <i class="fas fa-file-code text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Detail Isi Arsip Log</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium font-mono">{{ $backup->filename }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.security.error-logs.archives.download', $backup->id) }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition-all duration-200 shadow-sm shadow-emerald-200">
                <i class="fas fa-download text-xs"></i>
                <span>Download JSON</span>
            </a>
            <a href="{{ route('admin.security.error-logs.archives') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-all duration-200 shadow-sm">
                <i class="fas fa-arrow-left text-xs text-gray-400"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Records</p>
            <p class="text-xl font-bold text-gray-900">{{ number_format(count($json)) }} Entries</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Ukuran File</p>
            <p class="text-xl font-bold text-gray-900">{{ $backup->getSizeForHumans() }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Tanggal Backup</p>
            <p class="text-xl font-bold text-gray-900">{{ $backup->created_at->format('d M Y') }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Oleh</p>
            <p class="text-xl font-bold text-gray-900 truncate">{{ $backup->createdBy->name ?? 'System' }}</p>
        </div>
    </div>

    <!-- JSON Viewer -->
    <div class="bg-slate-900 rounded-2xl shadow-2xl overflow-hidden border border-slate-800">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800 bg-slate-900/50 backdrop-blur-xl">
            <div class="flex items-center gap-3">
                <div class="flex gap-1.5">
                    <div class="w-3 h-3 rounded-full bg-red-500/80"></div>
                    <div class="w-3 h-3 rounded-full bg-amber-500/80"></div>
                    <div class="w-3 h-3 rounded-full bg-emerald-500/80"></div>
                </div>
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">JSON Content Viewer</span>
            </div>
            <button onclick="copyJson()" class="flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-bold transition-all border border-slate-700">
                <i class="fas fa-copy"></i>
                <span id="copyText">Copy JSON</span>
            </button>
        </div>
        <div class="p-6 overflow-auto max-h-[600px] custom-scrollbar">
            <pre id="json-content" class="text-emerald-400 font-mono text-sm leading-relaxed">{{ json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Summary Backup</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Total Records</p>
                <p class="text-2xl font-black text-gray-900">{{ is_array($json) ? count($json) : 0 }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Storage Disk</p>
                <p class="text-2xl font-black text-gray-900 capitalize">{{ $backup->disk }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Created By</p>
                <p class="text-2xl font-black text-gray-900">{{ $backup->createdBy ? $backup->createdBy->name : 'System' }}</p>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(15, 23, 42, 1);
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(51, 65, 85, 1);
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(71, 85, 105, 1);
    }
</style>

<script>
    function copyJson() {
        const text = document.getElementById('json-content').innerText;
        navigator.clipboard.writeText(text).then(() => {
            alert('JSON copied to clipboard!');
        });
    }
</script>
@endsection

@extends('admin.layouts.app')

@section('breadcrumb')
<a href="{{ route('admin.security.activity-logs.index') }}" class="flex items-center hover:text-emerald-600 transition-colors">
    <i class="fas fa-chevron-right text-gray-300 mx-2 text-xs"></i>
    <span class="text-sm font-medium">Activity Log</span>
</a>
<a href="{{ route('admin.security.activity-logs.archives') }}" class="flex items-center hover:text-emerald-600 transition-colors">
    <i class="fas fa-chevron-right text-gray-300 mx-2 text-xs"></i>
    <span class="text-sm font-medium">Arsip Backup</span>
</a>
<i class="fas fa-chevron-right text-gray-300 mx-2 text-xs"></i>
<span class="text-emerald-600 font-medium text-sm">View JSON</span>
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Arsip JSON</h1>
            <p class="text-gray-500 text-sm mt-1">
                File: <span class="font-mono text-emerald-600">{{ $backup->filename }}</span> 
                ({{ $backup->getSizeForHumans() }}) • 
                Diarsipkan pada {{ $backup->created_at->format('d M Y, H:i') }}
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.security.activity-logs.archives.download', $backup->id) }}" 
               class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-sm font-medium hover:bg-emerald-700 transition-colors shadow-sm">
                <i class="fas fa-download mr-1.5"></i> Download File
            </a>
            <a href="{{ route('admin.security.activity-logs.archives') }}" 
               class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors shadow-sm">
                Kembali
            </a>
        </div>
    </div>

    <!-- JSON Viewer -->
    <div class="bg-slate-900 rounded-2xl border border-slate-800 shadow-xl overflow-hidden relative group">
        <div class="flex items-center justify-between px-4 py-3 bg-slate-800 border-b border-slate-700">
            <div class="flex items-center gap-2">
                <div class="flex gap-1.5">
                    <div class="w-3 h-3 rounded-full bg-red-500/80"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-500/80"></div>
                    <div class="w-3 h-3 rounded-full bg-green-500/80"></div>
                </div>
                <span class="text-xs font-mono text-slate-400 ml-2">activity_log_data.json</span>
            </div>
            <button onclick="copyJson()" class="text-slate-400 hover:text-white transition-colors text-xs flex items-center gap-1.5 bg-slate-700/50 px-2 py-1 rounded">
                <i class="fas fa-copy"></i> Copy JSON
            </button>
        </div>
        <div class="p-6 overflow-auto max-h-[70vh]">
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

<script>
    function copyJson() {
        const text = document.getElementById('json-content').innerText;
        navigator.clipboard.writeText(text).then(() => {
            alert('JSON copied to clipboard!');
        });
    }
</script>
@endsection

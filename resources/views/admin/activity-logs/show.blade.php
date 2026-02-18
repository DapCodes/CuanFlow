@extends('admin.layouts.app')

@section('breadcrumb')
<a href="{{ route('admin.security.activity-logs.index') }}" class="flex items-center hover:text-emerald-600 transition-colors">
    <i class="fas fa-chevron-right text-gray-300 mx-2 text-xs"></i>
    <span class="text-sm font-medium">Activity Log</span>
</a>
<i class="fas fa-chevron-right text-gray-300 mx-2 text-xs"></i>
<span class="text-emerald-600 font-medium text-sm">Detail #{{ $log->id }}</span>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Back Button -->
    <a href="{{ route('admin.security.activity-logs.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-900 transition-colors mb-2">
        <i class="fas fa-arrow-left"></i>
        <span class="text-sm font-medium">Kembali ke Daftar</span>
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content: Changes Diff -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900">Perubahan Data</h3>
                    <span class="text-xs font-mono text-gray-400 bg-white border border-gray-200 px-2 py-1 rounded">
                        {{ $log->event }}
                    </span>
                </div>
                
                @if($log->changes_list)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-3 w-1/3">Field</th>
                                    <th class="px-6 py-3 w-1/3">Sebelum</th>
                                    <th class="px-6 py-3 w-1/3">Sesudah</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($log->changes_list as $change)
                                <tr class="{{ $change['changed'] ? 'bg-yellow-50/30' : '' }}">
                                    <td class="px-6 py-4 font-mono text-gray-600">{{ $change['field'] }}</td>
                                    <td class="px-6 py-4 text-red-600 bg-red-50/30">
                                        @if(is_array($change['old']))
                                            <pre class="text-xs">{{ json_encode($change['old'], JSON_PRETTY_PRINT) }}</pre>
                                        @else
                                            {{ $change['old'] ?? '-' }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-emerald-600 bg-emerald-50/30 font-medium">
                                        @if(is_array($change['new']))
                                            <pre class="text-xs">{{ json_encode($change['new'], JSON_PRETTY_PRINT) }}</pre>
                                        @else
                                            {{ $change['new'] ?? '-' }}
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-info-circle text-2xl mb-2 text-gray-300"></i>
                        <p>Tidak ada perubahan atribut yang tercatat.</p>
                        <p class="text-xs mt-1">Aktivitas ini mungkin hanya log event tanpa perubahan data.</p>
                    </div>
                @endif
            </div>

            <!-- Subject Detail -->
            @if($log->subject)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">Subject Detail</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Type</p>
                        <p class="font-mono bg-gray-50 px-2 py-1 rounded inline-block">{{ $log->subject_type }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">ID</p>
                        <p class="font-mono">#{{ $log->subject_id }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Name / Title</p>
                        <p class="font-medium text-gray-900">{{ $log->subject_name }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- Meta Info -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">Metadata</h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Waktu</p>
                        <p class="font-medium text-gray-900">{{ $log->created_at->format('d M Y, H:i:s') }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $log->created_at->diffForHumans() }}</p>
                    </div>

                    <div class="pt-4 border-t border-gray-50">
                        <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">User (Causer)</p>
                        @if($log->causer)
                            <div class="flex items-center gap-3 mt-2">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($log->causer->name) }}&size=32" class="rounded-full w-8 h-8">
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $log->causer->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $log->causer->email ?? '-' }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-400 italic">System Auto</p>
                        @endif
                    </div>

                    @if($log->outlet)
                    <div class="pt-4 border-t border-gray-50">
                        <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Outlet Context</p>
                        <p class="font-medium text-gray-900">{{ $log->outlet->name }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Technical Info -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">Technical Details</h3>
                
                <div class="space-y-4 text-sm">
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">IP Address</p>
                        <p class="font-mono bg-gray-50 px-2 py-1 rounded inline-block text-xs">{{ $log->ip_address ?? 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">User Agent</p>
                        <p class="text-xs text-gray-600 leading-relaxed">{{ $log->user_agent ?? 'N/A' }}</p>
                    </div>

                    @if($log->url)
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">URL</p>
                        <p class="text-xs text-gray-600 break-all font-mono">{{ $log->url }}</p>
                    </div>
                    @endif

                    @if($log->batch_uuid)
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Batch UUID</p>
                        <p class="text-[10px] text-gray-400 font-mono">{{ $log->batch_uuid }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

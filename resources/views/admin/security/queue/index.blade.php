@extends('admin.layouts.app')

@section('title', 'Queue Monitoring')
@section('page-title', 'Queue Monitoring')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Queue Monitoring</span>
</li>
@endsection

@section('content')
<div class="px-4 lg:px-6 space-y-6 pb-20">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-network-wired text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight uppercase">Queue Monitoring</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium italic">Pantau proses background & pekerjaan tertunda</p>
            </div>
        </div>
        <div class="flex gap-2">
            @if($failedJobsCount > 0)
                <form action="{{ route('admin.security.queue.retry-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">
                        <i class="fas fa-rotate-right mr-2"></i> Retry All
                    </button>
                </form>
                <form action="{{ route('admin.security.queue.flush') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus SEMUA failed jobs?');">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">
                        <i class="fas fa-trash mr-2"></i> Flush
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- ALERTS --}}
    @if($failedJobsCount > 5)
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm cursor-default hover:bg-red-100 transition">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700 font-bold">Peringatan: Failed Job Menumpuk!</p>
                <p class="text-xs text-red-600 mt-1">Saat ini ada {{ $failedJobsCount }} jobs gagal. Jangan biarkan menumpuk, mohon segera Retry atau hapus (Flush).</p>
            </div>
        </div>
    </div>
    @endif

    @if($queueDelaySeconds > 60)
    <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-xl shadow-sm cursor-default hover:bg-amber-100 transition">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-clock text-amber-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-amber-700 font-bold">Peringatan: Terjadi Delay Antrean (Overload)</p>
                <p class="text-xs text-amber-600 mt-1">Waktu tunggu antrean terlama mencapai {{ number_format($queueDelaySeconds) }} detik. Pastikan Worker berjalan lancar atau pertimbangkan menambah jumlah Worker.</p>
            </div>
        </div>
    </div>
    @endif

    {{-- RINGKASAN STATISTIK --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Queue Pending --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Pending Jobs</p>
                    <p class="mt-1 text-2xl font-black text-gray-900">{{ number_format($pendingJobsCount) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center border border-gray-100 shadow-sm">
                    <i class="fas fa-hourglass-half text-gray-400 text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Queue Running --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Running Jobs</p>
                    <p class="mt-1 text-2xl font-black text-blue-600">{{ number_format($runningJobsCount) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center border border-blue-100 shadow-sm shadow-blue-100/50">
                    <i class="fas fa-spinner fa-spin text-blue-500 text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Failed Jobs --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Failed Jobs</p>
                    <p class="mt-1 text-2xl font-black {{ $failedJobsCount > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ number_format($failedJobsCount) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl {{ $failedJobsCount > 0 ? 'bg-red-50 border-red-100 shadow-red-100/50 text-red-500' : 'bg-gray-50 border-gray-100 text-gray-400' }} flex items-center justify-center border shadow-sm">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Worker Status --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Worker Status</p>
                    <p class="mt-1 text-xl font-black {{ $workerStatus == 'Active' ? 'text-emerald-600' : ($workerStatus == 'Inactive' ? 'text-amber-600' : 'text-gray-900') }}">
                        {{ $workerStatus }}
                    </p>
                    @if($workerCount > 0)
                        <span class="text-[10px] text-gray-400">({{ $workerCount }} processes)</span>
                    @endif
                </div>
                <div class="w-12 h-12 rounded-xl {{ $workerStatus == 'Active' ? 'bg-emerald-50 border-emerald-100 shadow-emerald-100/50 text-emerald-500' : 'bg-gray-50 border-gray-100 text-gray-400' }} flex items-center justify-center border shadow-sm">
                    <i class="fas fa-hard-hat text-xl"></i>
                </div>
            </div>
        </div>
    </section>

    {{-- KONTEN UTAMA: FAILED JOBS TABEL --}}
    <x-card-container class="!p-0 overflow-hidden border border-gray-200 shadow-sm mt-8">
        <div class="border-b border-gray-200 px-4 md:px-6 py-4 bg-gray-50/50">
            <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wide"><i class="fas fa-list text-ray-400 mr-2"></i> Failed Jobs History</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left">ID / Pelaksana</th>
                        <th class="px-6 py-4 text-left">Queue</th>
                        <th class="px-6 py-4 text-left">Exception</th>
                        <th class="px-6 py-4 text-left">Waktu Gagal</th>
                        <th class="px-6 py-4 text-center font-black">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($failedJobs as $job)
                    @php
                        $payload = json_decode($job->payload, true);
                        $jobName = $payload['displayName'] ?? 'Unknown Job';
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-5">
                            <p class="font-bold text-gray-900 break-all text-xs" style="word-break: break-all;">{{ Str::limit($job->uuid, 15) }}</p>
                            <p class="text-[10px] font-medium text-gray-400 mt-1 italic">{{ class_basename($jobName) }}</p>
                        </td>
                        <td class="px-6 py-5">
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-[10px] font-bold">{{ $job->queue }}</span>
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-[11px] text-red-600 font-medium" style="max-width: 300px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;" title="{{ $job->exception }}">
                                {{ explode("\n", $job->exception)[0] ?? 'Unknown error' }}
                            </p>
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($job->failed_at)->diffForHumans() }}</p>
                            <p class="text-[9px] text-gray-400">{{ \Carbon\Carbon::parse($job->failed_at)->format('d M Y H:i:s') }}</p>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <form action="{{ route('admin.security.queue.retry', $job->uuid) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-xl bg-indigo-50 text-indigo-500 hover:bg-indigo-600 hover:text-white shadow-sm transition-all active:scale-95 border border-indigo-100" title="Retry">
                                        <i class="fas fa-rotate-right text-xs"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.security.queue.destroy', $job->uuid) }}" method="POST" onsubmit="return confirm('Hapus permanently job ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-600 hover:text-white shadow-sm transition-all active:scale-95 border border-red-100" title="Delete">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gray-50 border border-dashed border-gray-200 rounded-full flex items-center justify-center mb-6">
                                    <i class="fas fa-check-circle text-gray-200 text-3xl"></i>
                                </div>
                                <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">Tidak Ada Failed Jobs</h3>
                                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-widest mt-2 max-w-xs mx-auto italic">
                                    Pekerjaan background sedang berjalan lancar.
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($failedJobs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $failedJobs->links() }}
        </div>
        @endif
    </x-card-container>

</div>
@endsection

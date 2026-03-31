@extends('admin.layouts.app')

@section('title', 'Arsip Log')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <a href="{{ route('admin.security.activity-logs.index') }}" class="text-gray-500 hover:text-emerald-600 transition-colors text-sm font-medium">Activity Log</a>
</li>
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Arsip Backup</span>
</li>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-box-archive text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Arsip Activity Log</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium">Koleksi riwayat log yang telah dipindahkan dari database utama</p>
            </div>
        </div>
        <a href="{{ route('admin.security.activity-logs.index') }}" 
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-all duration-200 shadow-sm">
            <i class="fas fa-arrow-left text-xs text-gray-400"></i>
            <span>Kembali ke Log Aktif</span>
        </a>
    </div>

    <!-- Statistik Singkat -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total File Arsip</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($archives->total()) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center border border-blue-100">
                    <i class="fas fa-file-invoice text-blue-500 text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Format Backup</p>
                    <p class="mt-1 text-2xl font-semibold text-emerald-600">JSON</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100">
                    <i class="fas fa-file-code text-emerald-500 text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 flex items-center gap-3 bg-blue-50/30 border-blue-100">
            <i class="fas fa-info-circle text-blue-500"></i>
            <p class="text-xs text-blue-700 leading-normal">Log yang telah diarsipkan dihapus dari database untuk menjaga performa aplikasi tetap cepat.</p>
        </div>
    </div>

    <!-- Tabel Arsip -->
    <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-[11px] text-gray-500 uppercase font-bold border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4">Informasi File</th>
                        <th class="px-6 py-4">Ukuran</th>
                        <th class="px-6 py-4">Dibuat Oleh</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($archives as $archive)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-bold text-gray-900 text-sm font-mono truncate max-w-xs">{{ $archive->filename }}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5 uppercase tracking-tighter">{{ $archive->created_at->isoFormat('D MMM Y, HH:mm') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-gray-50 border border-gray-100 rounded text-[10px] font-bold text-gray-600">
                                {{ $archive->getSizeForHumans() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($archive->createdBy)
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center text-[10px] font-bold text-emerald-600">
                                        {{ substr($archive->createdBy->name, 0, 1) }}
                                    </div>
                                    <span class="text-xs font-semibold text-gray-700">{{ $archive->createdBy->name }}</span>
                                </div>
                            @else
                                <span class="text-xs font-medium text-gray-400 italic">System</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($archive->status === 'completed')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-700">
                                    <i class="fas fa-check-circle text-[8px]"></i> Selesai
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-700">
                                    <i class="fas fa-times-circle text-[8px]"></i> Gagal
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                @if($archive->status === 'completed')
                                    <a href="{{ route('admin.security.activity-logs.archives.view', $archive->id) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 text-blue-500 hover:bg-blue-50 rounded-lg transition-all" 
                                       title="Lihat JSON">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="{{ route('admin.security.activity-logs.archives.download', $archive->id) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 text-emerald-500 hover:bg-emerald-50 rounded-lg transition-all" 
                                       title="Download File">
                                        <i class="fas fa-download text-sm"></i>
                                    </a>
                                @else
                                    <span class="text-[10px] text-gray-400 italic">No Action</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-dashed border-gray-200">
                                    <i class="fas fa-box-open text-2xl text-gray-300"></i>
                                </div>
                                <p class="text-gray-500 font-medium">Belum ada arsip log</p>
                                <p class="text-xs text-gray-400 mt-1">Lakukan backup manual untuk membuat arsip</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($archives->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $archives->links() }}
        </div>
        @endif
    </section>
</div>
@endsection

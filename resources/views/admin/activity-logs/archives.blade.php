@extends('admin.layouts.app')

@section('breadcrumb')
<a href="{{ route('admin.security.activity-logs.index') }}" class="flex items-center hover:text-emerald-600 transition-colors">
    <i class="fas fa-chevron-right text-gray-300 mx-2 text-xs"></i>
    <span class="text-sm font-medium">Activity Log</span>
</a>
<i class="fas fa-chevron-right text-gray-300 mx-2 text-xs"></i>
<span class="text-emerald-600 font-medium text-sm">Arsip Backup</span>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Arsip Backup Log</h1>
            <p class="text-gray-500 text-sm mt-1">Daftar file arsip log aktivitas yang telah dipindahkan dari database utama.</p>
        </div>
        <a href="{{ route('admin.security.activity-logs.index') }}" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors shadow-sm">
            Kembali ke Log Aktif
        </a>
    </div>

    <!-- Info Alert -->
    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 flex items-start gap-3">
        <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
        <div class="text-sm text-blue-800">
            <p class="font-bold mb-1">Bagaimana cara kerja arsip?</p>
            <p>Sistem secara otomatis mengarsipkan log aktivitas yang berumur lebih dari 30 hari ke dalam format file JSON. Log yang telah diarsipkan dihapus dari database untuk menjaga performa aplikasi tetap cepat.</p>
        </div>
    </div>

    <!-- Archives Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">Nama File</th>
                    <th class="px-6 py-4">Ukuran</th>
                    <th class="px-6 py-4">Tanggal Arsip</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($archives as $archive)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap font-mono text-gray-600">
                        {{ $archive->filename }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 bg-gray-100 rounded text-xs font-medium text-gray-600">
                            {{ $archive->getSizeForHumans() }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                        {{ $archive->created_at->format('d M Y, H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($archive->status === 'completed')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                <i class="fas fa-check-circle text-[10px]"></i> Selesai
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle text-[10px]"></i> Gagal
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($archive->status === 'completed')
                        <a href="{{ route('admin.security.activity-logs.archives.download', $archive->id) }}" 
                           class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-lg text-xs font-bold hover:bg-emerald-100 transition-colors">
                            <i class="fas fa-download"></i> Download
                        </a>
                        @else
                        <span class="text-gray-400 italic text-xs">Tidak tersedia</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                        <div class="flex flex-col items-center justify-center gap-3">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center text-gray-300">
                                <i class="fas fa-box-open text-2xl"></i>
                            </div>
                            <p>Belum ada arsip log yang tersedia.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($archives->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $archives->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

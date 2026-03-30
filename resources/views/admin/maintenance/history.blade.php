@extends('admin.layouts.app')

@section('title', 'Riwayat Maintenance')
@section('page-title', 'Riwayat Maintenance')

@section('content')
<div class="space-y-6">
    <!-- History Table -->
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-history text-amber-600 text-sm"></i>
                </div>
                <h3 class="font-bold text-gray-900">Log Maintenance</h3>
            </div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total: {{ $history->total() }} Record</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">Status</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">Judul & Alasan</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">Waktu</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($history as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-6 py-4 border-b border-gray-50">
                                @if($item->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase tracking-widest rounded-full border border-emerald-100">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                        Running
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-full border border-gray-100">
                                        Selesai
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 border-b border-gray-50">
                                <p class="text-sm font-bold text-gray-900">{{ $item->title }}</p>
                                <p class="text-[10px] text-gray-500 mt-1 italic">{{ $item->reason ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 border-b border-gray-50">
                                <div class="flex items-center gap-4">
                                    <div class="space-y-0.5">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase">Mulai</p>
                                        <p class="text-xs font-semibold text-gray-900">{{ $item->started_at->format('d/m/y H:i') }}</p>
                                    </div>
                                    <i class="fas fa-arrow-right text-[10px] text-gray-300"></i>
                                    <div class="space-y-0.5">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase">Selesai</p>
                                        <p class="text-xs font-semibold text-gray-900 italic">
                                            {{ $item->ended_at ? $item->ended_at->format('d/m/y H:i') : 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 border-b border-gray-50">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $item->admin->avatar_url }}" class="w-8 h-8 rounded-full border border-gray-100 object-cover" alt="">
                                    <div>
                                        <p class="text-xs font-bold text-gray-900">{{ $item->admin->name }}</p>
                                        <p class="text-[9px] text-gray-400">{{ $item->admin->email }}</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-300 italic">
                                <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-folder-open text-xl text-gray-200"></i>
                                </div>
                                Belum ada riwayat maintenance.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($history->hasPages())
            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-50">
                {{ $history->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

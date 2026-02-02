@extends('admin.layouts.app')

@section('title', 'Pelanggan Aktif')
@section('page-title', 'Manajemen Pelanggan & Langganan')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Pelanggan</span>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-users-viewfinder text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Daftar Pelanggan</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium">Lihat dan kelola status langganan seluruh pengguna</p>
            </div>
        </div>
        
        <!-- Filter Tabs -->
        <div class="bg-white rounded-xl p-1 flex space-x-1 border border-gray-200">
            <a href="{{ route('admin.subscription-users.index') }}" 
               class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition {{ !$status ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-500 hover:bg-gray-50' }}">
               Semua
            </a>
            <a href="{{ route('admin.subscription-users.index', ['status' => 'active']) }}" 
               class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition {{ $status == 'active' ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-500 hover:bg-gray-50' }}">
               Aktif
            </a>
            <a href="{{ route('admin.subscription-users.index', ['status' => 'trial']) }}" 
               class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition {{ $status == 'trial' ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-500 hover:bg-gray-50' }}">
               Trial
            </a>
            <a href="{{ route('admin.subscription-users.index', ['status' => 'expired']) }}" 
               class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition {{ $status == 'expired' ? 'bg-red-600 text-white shadow-sm' : 'text-gray-500 hover:bg-gray-50' }}">
               Expired
            </a>
        </div>
    </div>
    
    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pengguna</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tier</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Masa Aktif</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($subscriptions as $sub)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 font-bold overflow-hidden border border-gray-200">
                                    @if($sub->user->profile_photo_path)
                                        <img src="{{ Storage::url($sub->user->profile_photo_path) }}" class="w-full h-full object-cover">
                                    @else
                                        {{ substr($sub->user->name, 0, 1) }}
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 line-clamp-1">{{ $sub->user->name }}</p>
                                    <p class="text-[10px] text-gray-400 font-mono tracking-tight">{{ $sub->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-widest inline-block w-fit text-white {{ $sub->tier->badge_color }}">
                                    {{ $sub->tier->name }}
                                </span>
                                <span class="text-[10px] text-gray-400 mt-1">{{ $sub->plan->duration_months ?? '0' }} Bulan</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 text-xs font-medium text-gray-700">
                                    <i class="far fa-calendar-check text-gray-300"></i>
                                    <span>Lahir: {{ $sub->started_at->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs font-bold text-gray-900">
                                    <i class="far fa-calendar-times text-gray-300"></i>
                                    <span>Hingga: {{ ($sub->is_trial ? $sub->trial_ends_at : $sub->expires_at)?->format('d/m/Y') ?? 'Unlimited' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider 
                                @if($sub->status == 'active') bg-emerald-100 text-emerald-700 
                                @elseif($sub->status == 'trial') bg-indigo-100 text-indigo-700 
                                @elseif($sub->status == 'expired') bg-red-100 text-red-700 
                                @else bg-gray-100 text-gray-600 @endif">
                                {{ $sub->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.subscription-users.show', $sub) }}" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-indigo-600 hover:text-white text-gray-600 text-[11px] font-bold rounded-lg transition-all">
                                <i class="fas fa-eye text-[10px]"></i>
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fas fa-users-slash text-4xl text-gray-200"></i>
                                <p class="font-medium">Belum ada data pelanggan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($subscriptions->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/30">
            {{ $subscriptions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

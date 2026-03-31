@extends('admin.layouts.app')

@section('title', 'Daftar Pelanggan')
@section('page-title', 'Manajemen Billing')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Subscriptions</span>
</li>
@endsection

@section('content')
<div class="px-4 lg:px-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-users-viewfinder text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight uppercase">Daftar Pelanggan</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium italic">Monitor status langganan dan akses fitur seluruh pengguna sistem</p>
            </div>
        </div>
        
        <!-- Filter Tabs -->
        <div class="bg-white rounded-2xl p-1.5 flex gap-1.5 border border-gray-200 shadow-sm">
            @php
                $tabClasses = "px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 active:scale-95 whitespace-nowrap";
            @endphp
            <a href="{{ route('admin.subscription-users.index') }}" 
               class="{{ $tabClasses }} {{ !$status ? 'bg-gray-900 text-white shadow-md' : 'text-gray-400 hover:bg-gray-50 hover:text-gray-600' }}">
               Semua
            </a>
            <a href="{{ route('admin.subscription-users.index', ['status' => 'active']) }}" 
               class="{{ $tabClasses }} {{ $status == 'active' ? 'bg-emerald-100 text-emerald-700 shadow-sm' : 'text-gray-400 hover:bg-gray-50 hover:text-gray-600' }}">
               Aktif
            </a>
            <a href="{{ route('admin.subscription-users.index', ['status' => 'trial']) }}" 
               class="{{ $tabClasses }} {{ $status == 'trial' ? 'bg-indigo-100 text-indigo-700 shadow-sm' : 'text-gray-400 hover:bg-gray-50 hover:text-gray-600' }}">
               Trial
            </a>
            <a href="{{ route('admin.subscription-users.index', ['status' => 'expired']) }}" 
               class="{{ $tabClasses }} {{ $status == 'expired' ? 'bg-rose-100 text-rose-700 shadow-sm' : 'text-gray-400 hover:bg-gray-50 hover:text-gray-600' }}">
               Expired
            </a>
        </div>
    </div>

    {{-- RINGKASAN STATISTIK --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Users --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Total Pengguna</p>
                    <p class="mt-1 text-2xl font-black text-gray-900 uppercase tracking-tighter">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100 shadow-sm">
                    <i class="fas fa-users text-gray-400 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Active Subscriptions --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Pelanggan Aktif</p>
                    <p class="mt-1 text-2xl font-black text-emerald-600 uppercase tracking-tighter">{{ number_format($stats['active']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100 shadow-sm shadow-emerald-100/50">
                    <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Trial Subscriptions --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Masa Trial</p>
                    <p class="mt-1 text-2xl font-black text-indigo-600 uppercase tracking-tighter">{{ number_format($stats['trial']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center border border-indigo-100 shadow-sm shadow-indigo-100/50">
                    <i class="fas fa-flask text-indigo-500 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Expired Subscriptions --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Habis Masa Berlaku</p>
                    <p class="mt-1 text-2xl font-black text-rose-600 uppercase tracking-tighter">{{ number_format($stats['expired']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-rose-50 flex items-center justify-center border border-rose-100 shadow-sm shadow-rose-100/50">
                    <i class="fas fa-calendar-xmark text-rose-500 text-lg"></i>
                </div>
            </div>
        </div>
    </section>

    {{-- KONTEN UTAMA: TOOLBAR + TABEL --}}
    <x-card-container class="!p-0 overflow-hidden border border-gray-200 shadow-sm bg-white rounded-xl">
        {{-- Toolbar: Search --}}
        <div class="border-b border-gray-200 px-4 md:px-6 py-5 bg-gray-50/50">
            <form action="{{ route('admin.subscription-users.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:items-center md:justify-between gap-4">
                <input type="hidden" name="status" value="{{ $status }}">
                <div class="w-full md:max-w-xs">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2 block italic">Cari Nama / Email</label>
                    <div class="relative group">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pelanggan..."
                               class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all duration-300">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-emerald-500 transition-colors text-xs"></i>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition-all shadow-md shadow-gray-200 active:scale-95 group">
                        <i class="fas fa-search group-hover:rotate-12 transition-transform"></i>
                    </button>
                    @if(request()->anyFilled(['search']))
                        <a href="{{ route('admin.subscription-users.index', ['status' => $status]) }}" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white border border-gray-200 text-gray-400 hover:bg-gray-50 hover:text-red-500 transition-all shadow-sm active:scale-95" title="Reset">
                            <i class="fas fa-redo-alt text-sm"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left">Profil Pengguna</th>
                        <th class="px-6 py-4 text-left whitespace-nowrap">Paket Langganan</th>
                        <th class="px-6 py-4 text-left">Masa Berlaku</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center font-black">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($subscriptions as $sub)
                    <tr class="hover:bg-gray-50 transition-colors group">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-11 h-11 rounded-full bg-gray-900 border-2 border-gray-800 flex items-center justify-center text-emerald-400 font-black text-xs uppercase overflow-hidden shadow-lg group-hover:scale-105 transition-transform">
                                    @if($sub->user->profile_photo_path)
                                        <img src="{{ Storage::url($sub->user->profile_photo_path) }}" class="w-full h-full object-cover">
                                    @else
                                        {{ substr($sub->user->name, 0, 1) }}
                                    @endif
                                </div>
                                <div class="max-w-[200px]">
                                    <p class="font-black text-gray-900 leading-tight uppercase tracking-tight truncate">{{ $sub->user->name }}</p>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-emerald-500 mt-1 italic truncate font-mono">{{ $sub->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col gap-1.5">
                                <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest inline-block w-fit text-white {{ $sub->tier->badge_color ?? 'bg-gray-600' }} shadow-sm">
                                    {{ $sub->tier->display_name }}
                                </span>
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

@extends('admin.layouts.app')

@section('title', 'Riwayat Login')
@section('page-title', 'Keamanan - Riwayat Login')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-gray-500 text-sm">Keamanan</span>
</li>
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Riwayat Login</span>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-clock-rotate-left text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Riwayat Login</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium">Pantau aktivitas login pengguna di CuanFlow & JajanFlow</p>
            </div>
        </div>
        <a href="{{ route('admin.security.banned-ips.index') }}" 
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-xl hover:bg-red-700 transition-all duration-200 shadow-sm hover:shadow-red-200/50">
            <i class="fas fa-shield-halved text-xs"></i>
            <span>Kelola IP Terblokir</span>
        </a>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" action="{{ route('admin.security.login-histories.index') }}" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari nama user atau IP address..." 
                       class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
            </div>
            <select name="app_name" 
                    class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
                <option value="">Semua Aplikasi</option>
                <option value="CuanFlow" {{ request('app_name') === 'CuanFlow' ? 'selected' : '' }}>CuanFlow</option>
                <option value="JajanFlow" {{ request('app_name') === 'JajanFlow' ? 'selected' : '' }}>JajanFlow</option>
            </select>
            <button type="submit" 
                    class="px-5 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-emerald-600 transition-all duration-200">
                <i class="fas fa-filter mr-1.5"></i>Filter
            </button>
            @if(request('search') || request('app_name'))
            <a href="{{ route('admin.security.login-histories.index') }}" 
               class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200 text-center">
                <i class="fas fa-times mr-1.5"></i>Reset
            </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">User</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">IP Address</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aplikasi</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Waktu Login</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($histories as $history)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($history->user)
                                <img src="{{ $history->user->avatar_url }}" alt="{{ $history->user->name }}" 
                                     class="w-9 h-9 rounded-full object-cover border-2 border-gray-200">
                                <div>
                                    <p class="font-semibold text-gray-900 text-sm">{{ $history->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $history->user->email }}</p>
                                </div>
                                @else
                                <div class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center">
                                    <i class="fas fa-user-slash text-gray-400 text-xs"></i>
                                </div>
                                <span class="text-sm text-gray-400 italic">User dihapus</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <code class="text-sm bg-gray-100 px-2 py-1 rounded-lg text-gray-700 font-mono">{{ $history->ip_address }}</code>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($history->app_name === 'CuanFlow')
                            <span class="px-2.5 py-1 text-xs font-semibold bg-emerald-100 text-emerald-700 rounded-full">
                                <i class="fas fa-globe mr-1"></i>CuanFlow
                            </span>
                            @else
                            <span class="px-2.5 py-1 text-xs font-semibold bg-blue-100 text-blue-700 rounded-full">
                                <i class="fas fa-mobile-alt mr-1"></i>JajanFlow
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-700">{{ $history->created_at->format('d M Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $history->created_at->format('H:i:s') }}</p>
                        </td>
                        <td class="px-6 py-4 text-center" x-data="{ showModal: false }">
                            <button @click="showModal = true"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 text-xs font-semibold rounded-lg hover:bg-red-100 transition-all duration-200">
                                <i class="fas fa-ban"></i>
                                <span>Ban IP</span>
                            </button>

                            <!-- Ban Confirmation Modal -->
                            <div x-show="showModal" x-cloak
                                 class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0">
                                <div class="fixed inset-0 bg-gray-900/50" @click="showModal = false"></div>
                                <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 z-10"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95">
                                    <div class="text-center mb-5">
                                        <div class="w-14 h-14 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-shield-halved text-red-500 text-2xl"></i>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900">Blokir IP Address?</h3>
                                        <p class="text-sm text-gray-500 mt-1">IP <code class="bg-gray-100 px-1.5 py-0.5 rounded text-red-600 font-mono text-xs">{{ $history->ip_address }}</code> akan diblokir dari mengakses aplikasi.</p>
                                    </div>
                                    <form method="POST" action="{{ route('admin.security.banned-ips.store') }}">
                                        @csrf
                                        <input type="hidden" name="ip_address" value="{{ $history->ip_address }}">
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Alasan (opsional)</label>
                                            <textarea name="reason" rows="2" 
                                                      placeholder="Misalnya: Aktivitas mencurigakan..."
                                                      class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all"></textarea>
                                        </div>
                                        <div class="flex gap-3">
                                            <button type="button" @click="showModal = false" 
                                                    class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                                                Batal
                                            </button>
                                            <button type="submit" 
                                                    class="flex-1 px-4 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-xl hover:bg-red-700 transition-all duration-200">
                                                <i class="fas fa-ban mr-1.5"></i>Blokir IP
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-clock-rotate-left text-4xl text-gray-300 mb-3"></i>
                            <p>Belum ada riwayat login</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($histories->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $histories->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

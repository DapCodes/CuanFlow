@extends('admin.layouts.app')

@section('title', 'IP Terblokir')
@section('page-title', 'Keamanan - IP Terblokir')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-gray-500 text-sm">Keamanan</span>
</li>
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">IP Terblokir</span>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-red-50 rounded-2xl flex items-center justify-center text-red-600 shadow-sm shadow-red-100/50">
                <i class="fas fa-shield-halved text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">IP Terblokir</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium">Kelola daftar IP yang diblokir dari mengakses aplikasi</p>
            </div>
        </div>
        <a href="{{ route('admin.security.login-histories.index') }}" 
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-emerald-600 transition-all duration-200 shadow-sm hover:shadow-emerald-200/50">
            <i class="fas fa-clock-rotate-left text-xs"></i>
            <span>Riwayat Login</span>
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-ban text-red-500"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $bannedIps->total() }}</p>
                    <p class="text-xs text-gray-500 font-medium">Total IP Terblokir</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">IP Address</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Alasan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Diblokir Pada</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($bannedIps as $banned)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <code class="text-sm bg-red-50 px-2.5 py-1 rounded-lg text-red-700 font-mono font-semibold">{{ $banned->ip_address }}</code>
                        </td>
                        <td class="px-6 py-4">
                            @if($banned->reason)
                            <p class="text-sm text-gray-700">{{ $banned->reason }}</p>
                            @else
                            <span class="text-sm text-gray-400 italic">Tidak ada alasan</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-700">{{ $banned->created_at->format('d M Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $banned->created_at->format('H:i:s') }} · {{ $banned->created_at->diffForHumans() }}</p>
                        </td>
                        <td class="px-6 py-4 text-center" x-data="{ showUnban: false }">
                            <button @click="showUnban = true"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-600 text-xs font-semibold rounded-lg hover:bg-emerald-100 transition-all duration-200">
                                <i class="fas fa-unlock"></i>
                                <span>Unban</span>
                            </button>

                            <!-- Unban Confirmation Modal -->
                            <div x-show="showUnban" x-cloak
                                 class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0">
                                <div class="fixed inset-0 bg-gray-900/50" @click="showUnban = false"></div>
                                <div class="relative bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 z-10"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95">
                                    <div class="text-center mb-5">
                                        <div class="w-14 h-14 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-unlock text-emerald-500 text-2xl"></i>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900">Hapus Blokir IP?</h3>
                                        <p class="text-sm text-gray-500 mt-1">IP <code class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-700 font-mono text-xs">{{ $banned->ip_address }}</code> akan dapat mengakses aplikasi kembali.</p>
                                    </div>
                                    <div class="flex gap-3">
                                        <button type="button" @click="showUnban = false" 
                                                class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                                            Batal
                                        </button>
                                        <form method="POST" action="{{ route('admin.security.banned-ips.destroy', $banned) }}" class="flex-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-full px-4 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition-all duration-200">
                                                <i class="fas fa-unlock mr-1.5"></i>Unban
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-shield-halved text-4xl text-gray-300 mb-3"></i>
                            <p>Belum ada IP yang diblokir</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($bannedIps->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $bannedIps->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

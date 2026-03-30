@extends('admin.layouts.app')

@section('title', 'Maintenance & Online Users')
@section('page-title', 'Maintenance')

@section('content')
<div class="space-y-6">
    <!-- Maintenance Status Card -->
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
        <div class="p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center {{ $maintenance ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600' }}">
                        <i class="fas {{ $maintenance ? 'fa-triangle-exclamation' : 'fa-check-circle' }} text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Maintenance Mode</h2>
                        <p class="text-gray-500 text-sm">
                            Status saat ini: 
                            <span class="font-bold {{ $maintenance ? 'text-amber-600' : 'text-emerald-600' }}">
                                {{ $maintenance ? 'Aktif (Aplikasi Terkunci)' : 'Tidak Aktif (Aplikasi Normal)' }}
                            </span>
                        </p>
                    </div>
                </div>

                <button type="button" 
                        onclick="document.getElementById('maintenance-modal').classList.remove('hidden')"
                        class="px-6 py-3 rounded-xl font-bold transition-all flex items-center gap-2 {{ $maintenance ? 'bg-red-500 text-white hover:bg-red-600' : 'bg-emerald-500 text-white hover:bg-emerald-600' }}">
                    <i class="fas {{ $maintenance ? 'fa-power-off' : 'fa-hammer' }}"></i>
                    {{ $maintenance ? 'Matikan Maintenance' : 'Aktifkan Maintenance' }}
                </button>
            </div>

            @if($maintenance)
                <div class="mt-6 p-4 bg-amber-50 rounded-xl border border-amber-100">
                    <div class="flex gap-3">
                        <i class="fas fa-info-circle text-amber-500 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-bold text-amber-900">{{ $maintenance->title }}</p>
                            <p class="text-xs text-amber-700 mt-1">{{ $maintenance->reason ?? 'Tidak ada alasan spesifik.' }}</p>
                            <p class="text-[10px] text-amber-500 mt-2">Dimulai sejak: {{ $maintenance->started_at->format('d M Y, H:i') }} WIB oleh {{ $maintenance->admin->name }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Active Users Table -->
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users-viewfinder text-blue-600 text-sm"></i>
                </div>
                <h3 class="font-bold text-gray-900">User Sedang Online ({{ $activeUsers->count() }})</h3>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Realtime Data</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">User</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Role</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">IP Address</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Aktivitas Terakhir</th>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($activeUsers as $user)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <img src="{{ $user->avatar_url }}" class="w-10 h-10 rounded-full border-2 border-white shadow-sm object-cover" alt="">
                                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $user->name }}</p>
                                        <p class="text-[10px] text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-[10px] font-bold uppercase tracking-wider rounded-lg">
                                    {{ $user->roles->first()?->name ?? 'User' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <code class="text-[10px] bg-gray-50 px-2 py-1 rounded border border-gray-100 text-gray-600">
                                    {{ $user->ip_address ?? 'N/A' }}
                                </code>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-xs text-gray-600 font-medium">{{ $user->last_seen_at->diffForHumans() }}</p>
                                <p class="text-[9px] text-gray-400">{{ $user->last_seen_at->format('H:i:s') }}</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($user->session_id)
                                    <form action="{{ route('admin.maintenance.session.terminate', $user->session_id) }}" method="POST" onsubmit="return confirm('Yakin ingin memutus sesi user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Terminate Session">
                                            <i class="fas fa-sign-out-alt"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[10px] text-gray-400 italic">No Active Session</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-user-slash text-2xl text-gray-200"></i>
                                </div>
                                Tidak ada user yang sedang online saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Maintenance Modal -->
<div id="maintenance-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-[2rem] w-full max-w-lg shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600">
                            <i class="fas fa-hammer text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $maintenance ? 'Matikan Maintenance' : 'Aktifkan Maintenance' }}</h3>
                            <p class="text-gray-500 text-xs">Konfirmasi perubahan status sistem.</p>
                        </div>
                    </div>
                    <button onclick="document.getElementById('maintenance-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form action="{{ route('admin.maintenance.toggle') }}" method="POST" class="space-y-6">
                    @csrf
                    @if(!$maintenance)
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Judul Maintenance</label>
                            <input type="text" name="title" required placeholder="Contoh: Update Sistem Mingguan"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-100 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Alasan / Detail</label>
                            <textarea name="reason" rows="3" placeholder="Jelaskan detail maintenance..."
                                      class="w-full px-4 py-3 rounded-xl border border-gray-100 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none text-sm"></textarea>
                        </div>
                    @else
                        <div class="p-4 bg-red-50 rounded-2xl border border-red-100 text-center">
                            <i class="fas fa-exclamation-triangle text-red-500 text-3xl mb-3"></i>
                            <p class="text-sm text-red-900 font-bold">Anda akan mematikan mode maintenance.</p>
                            <p class="text-xs text-red-700 mt-1">Aplikasi akan kembali dapat diakses oleh semua pengguna.</p>
                        </div>
                    @endif

                    <div class="flex gap-3 pt-4">
                        <button type="button" onclick="document.getElementById('maintenance-modal').classList.add('hidden')"
                                class="flex-1 px-6 py-3 rounded-xl font-bold bg-gray-50 text-gray-500 hover:bg-gray-100 transition-all">
                            Batal
                        </button>
                        <button type="submit" 
                                class="flex-1 px-6 py-3 rounded-xl font-bold text-white transition-all {{ $maintenance ? 'bg-red-500 hover:bg-red-600 shadow-lg shadow-red-200' : 'bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-200' }}">
                            {{ $maintenance ? 'Ya, Matikan' : 'Ya, Aktifkan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

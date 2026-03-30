@extends('admin.layouts.app')

@section('title', 'Kirim Pengumuman')
@section('page-title', 'Broadcast Pengumuman')

@section('content')
<div class="space-y-6" x-data="{ target: 'all_owners' }">
    <!-- Broadcast Form -->
    <div class="bg-white rounded-3xl border border-gray-100 overflow-hidden shadow-sm shadow-emerald-100/20">
        <div class="px-8 py-6 border-b border-gray-50 flex items-center gap-4 bg-gray-50/30">
            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600">
                <i class="fas fa-paper-plane text-sm"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900">Buat Pengumuman Baru</h3>
                <p class="text-gray-500 text-xs">Pesan akan dikirim melalui antrian email (Queue).</p>
            </div>
        </div>

        <form action="{{ route('admin.maintenance.broadcast.send') }}" method="POST" class="p-8">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Tipe Pesan</label>
                        <select name="type" required class="w-full px-5 py-4 rounded-2xl border border-gray-100 focus:border-emerald-500 focus:ring-8 focus:ring-emerald-500/5 transition-all outline-none text-sm bg-gray-50/50">
                            <option value="maintenance_alert">Pemberitahuan Maintenance (Jadwal)</option>
                            <option value="custom_broadcast">Pesan Kustom / Broadcast Umum</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Subjek Email</label>
                        <input type="text" name="subject" required placeholder="Contoh: Pemberitahuan Pemeliharaan Sistem"
                               class="w-full px-5 py-4 rounded-2xl border border-gray-100 focus:border-emerald-500 focus:ring-8 focus:ring-emerald-500/5 transition-all outline-none text-sm bg-gray-50/50">
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Pilih Target Penerima</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative flex flex-col items-center justify-center p-6 border-2 rounded-3xl transition-all cursor-pointer hover:bg-emerald-50/30"
                                   :class="target === 'all_owners' ? 'border-emerald-500 bg-emerald-50/50 ring-4 ring-emerald-500/10 shadow-lg shadow-emerald-100/50' : 'border-gray-50 bg-white group hover:border-emerald-200'">
                                <input type="radio" name="target" value="all_owners" class="absolute inset-0 opacity-0 cursor-pointer" x-model="target">
                                <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600 mb-4 transition-transform group-hover:scale-110">
                                    <i class="fas fa-users-gear text-xl"></i>
                                </div>
                                <span class="text-sm font-black text-gray-900 group-hover:text-emerald-700">Semua Owner</span>
                                <span class="text-[10px] text-gray-400 mt-2 font-bold uppercase tracking-widest">{{ $owners->count() }} Users</span>
                            </label>
                            
                            <label class="relative flex flex-col items-center justify-center p-6 border-2 rounded-3xl transition-all cursor-pointer hover:bg-blue-50/30"
                                   :class="target === 'specific_users' ? 'border-blue-500 bg-blue-50/50 ring-4 ring-blue-500/10 shadow-lg shadow-blue-100/50' : 'border-gray-50 bg-white group hover:border-blue-200'">
                                <input type="radio" name="target" value="specific_users" class="absolute inset-0 opacity-0 cursor-pointer" x-model="target">
                                <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 mb-4 transition-transform group-hover:scale-110">
                                    <i class="fas fa-user-check text-xl"></i>
                                </div>
                                <span class="text-sm font-black text-gray-900 group-hover:text-blue-700">User Tertentu</span>
                                <span class="text-[10px] text-gray-400 mt-2 font-bold uppercase tracking-widest">Pilih Manual</span>
                            </label>
                        </div>
                    </div>
                    
                    <div x-show="target === 'specific_users'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Pilih User (Owner)</label>
                        <div class="max-h-[300px] overflow-y-auto pr-2 space-y-2 custom-scrollbar">
                            @foreach($owners as $owner)
                                <label class="flex items-center gap-3 p-3 rounded-2xl border border-gray-50 bg-gray-50/30 hover:bg-white hover:border-blue-200 transition-all cursor-pointer group">
                                    <input type="checkbox" name="user_ids[]" value="{{ $owner->id }}" class="w-5 h-5 rounded-lg border-gray-200 text-blue-600 focus:ring-blue-500/20">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $owner->avatar_url }}" class="w-10 h-10 rounded-full border-2 border-white shadow-sm object-cover" alt="">
                                        <div>
                                            <p class="text-sm font-bold text-gray-900 group-hover:text-blue-700">{{ $owner->name }}</p>
                                            <p class="text-[10px] text-gray-500">{{ $owner->email }}</p>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <div class="h-full flex flex-col">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Isi Pesan (Email)</label>
                        <textarea name="content" required placeholder="Tuliskan isi pengumuman anda di sini..."
                                  class="flex-1 w-full px-5 py-4 rounded-3xl border border-gray-100 focus:border-emerald-500 focus:ring-8 focus:ring-emerald-500/5 transition-all outline-none text-sm bg-gray-50/50 min-h-[400px]"></textarea>
                        
                        <button type="submit" class="mt-6 w-full py-5 rounded-2xl bg-emerald-500 text-white font-black uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-xl shadow-emerald-100 hover:shadow-emerald-200 transform hover:-translate-y-1">
                            <i class="fas fa-paper-plane mr-2 scale-110"></i> Kirim Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- History Header -->
    <div class="flex items-center gap-4 mt-12 mb-6">
        <div class="w-1 bg-emerald-500 h-8 rounded-full shadow-lg shadow-emerald-200"></div>
        <h3 class="text-xl font-black text-gray-900 tracking-tight">Riwayat Pengiriman</h3>
    </div>

    <!-- Broadcast History Table -->
    <div class="bg-white rounded-3xl border border-gray-100 overflow-hidden shadow-sm shadow-emerald-100/10">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/30">
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Waktu & Tipe</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Subjek</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Target</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Konten</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($broadcasts as $broadcast)
                        <tr class="hover:bg-gray-50/30 transition-colors group">
                            <td class="px-8 py-6">
                                <p class="text-xs font-bold text-gray-900">{{ $broadcast->created_at->format('d M Y') }}</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase mt-1">{{ $broadcast->created_at->format('H:i') }} WIB</p>
                                <span class="inline-block mt-2 px-2 py-0.5 {{ $broadcast->type === 'maintenance_alert' ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-blue-50 text-blue-600 border-blue-100' }} border rounded text-[8px] font-black uppercase tracking-widest">
                                    {{ $broadcast->type }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-sm font-black text-gray-900 leading-tight">{{ $broadcast->subject }}</p>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-2">
                                    <div class="w-1.5 h-1.5 bg-{{ $broadcast->target_role ? 'emerald' : 'blue' }}-500 rounded-full"></div>
                                    <p class="text-xs font-bold text-gray-700 capitalize">{{ $broadcast->target_role ? 'Semua Owner' : 'Spesifik User' }}</p>
                                </div>
                                <p class="text-[11px] text-gray-400 font-bold mt-1 tracking-wider">{{ $broadcast->total_recipients }} Penerima</p>
                            </td>
                            <td class="px-8 py-6 max-w-xs">
                                <p class="text-[11px] text-gray-500 line-clamp-2 leading-relaxed font-medium">
                                    {{ $broadcast->content }}
                                </p>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $broadcast->admin->avatar_url }}" class="w-10 h-10 rounded-2xl border-2 border-white shadow-md object-cover" alt="">
                                    <div>
                                        <p class="text-xs font-black text-gray-900">{{ $broadcast->admin->name }}</p>
                                        <p class="text-[9px] text-gray-400 font-bold tracking-tight">System Admin</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-16 text-center text-gray-300">
                                <div class="w-20 h-20 bg-gray-50 rounded-3xl flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-paper-plane text-3xl text-gray-200"></i>
                                </div>
                                <p class="text-sm font-black uppercase tracking-widest text-gray-400">Belum ada riwayat pengumuman.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($broadcasts->hasPages())
            <div class="px-8 py-6 bg-gray-50/30 border-t border-gray-50">
                {{ $broadcasts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

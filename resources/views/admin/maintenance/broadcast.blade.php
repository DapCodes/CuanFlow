@extends('admin.layouts.app')

@section('title', 'Kirim Pengumuman')
@section('page-title', 'Broadcast Pengumuman')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Pengumuman</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6" x-data="{ target: 'all_owners' }">

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900 leading-tight">
                    Broadcast Pengumuman
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kirim pengumuman ke owner. Pesan akan dikirim melalui antrian email (Queue).
                </p>
            </div>
        </section>

        {{-- RINGKASAN STATISTIK --}}
        <section class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6">
            <x-card-container class="px-6 py-6">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Owner</p>
                <p class="mt-2 text-2xl font-black text-gray-900">{{ $owners->count() }}</p>
            </x-card-container>

            <x-card-container class="px-6 py-6">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Broadcast</p>
                <p class="mt-2 text-2xl font-black text-gray-900">{{ $broadcasts->total() }}</p>
            </x-card-container>

            <x-card-container class="px-6 py-6">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Terakhir Dikirim</p>
                <p class="mt-2 text-sm font-black text-gray-900">
                    {{ $broadcasts->count() > 0 ? $broadcasts->first()->created_at->diffForHumans() : '-' }}
                </p>
            </x-card-container>
        </section>

        {{-- FORM BROADCAST --}}
        <x-card-container class="overflow-hidden">
            <div class="px-8 py-5 border-b border-gray-100 flex items-center gap-3 bg-gray-50/50">
                <div class="w-9 h-9 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600">
                    <i class="fas fa-paper-plane text-sm"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-gray-900">Buat Pengumuman Baru</h3>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">Isi form di bawah lalu kirim</p>
                </div>
            </div>

            <form action="{{ route('admin.maintenance.broadcast.send') }}" method="POST" class="p-8">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                    {{-- LEFT --}}
                    <div class="space-y-6">
                        {{-- Tipe --}}
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tipe Pesan</label>
                            <select name="type" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-sm font-bold text-gray-700 focus:border-cuan-green focus:ring-4 focus:ring-cuan-green/10 outline-none transition-all">
                                <option value="maintenance_alert">Pemberitahuan Maintenance</option>
                                <option value="custom_broadcast">Pesan Kustom / Broadcast Umum</option>
                            </select>
                        </div>

                        {{-- Subjek --}}
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Subjek Email</label>
                            <input type="text" name="subject" required
                                   placeholder="Contoh: Pemberitahuan Pemeliharaan Sistem"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-sm font-bold placeholder:text-gray-400 focus:border-cuan-green focus:ring-4 focus:ring-cuan-green/10 outline-none transition-all">
                        </div>

                        {{-- Target --}}
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Target Penerima</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="relative flex flex-col items-center justify-center p-5 border-2 rounded-2xl cursor-pointer transition-all"
                                       :class="target === 'all_owners' ? 'border-cuan-green bg-cuan-green/5 ring-4 ring-cuan-green/10' : 'border-gray-100 bg-white hover:border-gray-200'">
                                    <input type="radio" name="target" value="all_owners" class="absolute inset-0 opacity-0 cursor-pointer" x-model="target">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3 transition-colors"
                                         :class="target === 'all_owners' ? 'bg-cuan-green/10 text-cuan-green' : 'bg-gray-100 text-gray-400'">
                                        <i class="fas fa-users text-base"></i>
                                    </div>
                                    <span class="text-xs font-black text-gray-900">Semua Owner</span>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">{{ $owners->count() }} Users</span>
                                </label>

                                <label class="relative flex flex-col items-center justify-center p-5 border-2 rounded-2xl cursor-pointer transition-all"
                                       :class="target === 'specific_users' ? 'border-blue-500 bg-blue-50/50 ring-4 ring-blue-500/10' : 'border-gray-100 bg-white hover:border-gray-200'">
                                    <input type="radio" name="target" value="specific_users" class="absolute inset-0 opacity-0 cursor-pointer" x-model="target">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3 transition-colors"
                                         :class="target === 'specific_users' ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-400'">
                                        <i class="fas fa-user-check text-base"></i>
                                    </div>
                                    <span class="text-xs font-black text-gray-900">User Tertentu</span>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">Pilih Manual</span>
                                </label>
                            </div>
                        </div>

                        {{-- Pilih User --}}
                        <div x-show="target === 'specific_users'"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Pilih User</label>

                            {{-- Search box --}}
                            <div class="relative mb-2">
                                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 text-xs pointer-events-none"></i>
                                <input type="text" id="userSearch"
                                       placeholder="Cari nama atau email..."
                                       oninput="filterUsers(this.value)"
                                       class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-xs font-bold placeholder:text-gray-400 focus:border-blue-400 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all">
                            </div>

                            {{-- User list --}}
                            <div id="userList" class="max-h-[240px] overflow-y-auto space-y-1.5 pr-1">
                                @foreach($owners as $owner)
                                    <label data-name="{{ strtolower($owner->name) }}" data-email="{{ strtolower($owner->email) }}"
                                           class="user-item flex items-center gap-3 p-3 rounded-xl border border-gray-100 bg-gray-50/50 hover:bg-white hover:border-blue-200 transition-all cursor-pointer group">
                                        <input type="checkbox" name="user_ids[]" value="{{ $owner->id }}"
                                               class="w-4 h-4 rounded-md border-gray-200 text-blue-600 focus:ring-blue-500/20">
                                        <img src="{{ $owner->avatar_url }}" class="w-8 h-8 rounded-lg border border-gray-100 shadow-sm object-cover" alt="">
                                        <div class="min-w-0">
                                            <p class="text-xs font-black text-gray-900 group-hover:text-blue-700 truncate">{{ $owner->name }}</p>
                                            <p class="text-[9px] font-bold text-gray-400 tracking-wide truncate">{{ $owner->email }}</p>
                                        </div>
                                    </label>
                                @endforeach

                                {{-- Empty state saat search tidak ada hasil --}}
                                <div id="userSearchEmpty" class="hidden py-8 text-center">
                                    <i class="fas fa-user-slash text-xl text-gray-200 mb-2 block"></i>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">User tidak ditemukan</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT --}}
                    <div class="flex flex-col h-full">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Isi Pesan</label>
                        <textarea name="content" required
                                  placeholder="Tuliskan isi pengumuman Anda di sini..."
                                  class="flex-1 w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-sm font-medium placeholder:text-gray-400 focus:border-cuan-green focus:ring-4 focus:ring-cuan-green/10 outline-none transition-all resize-none min-h-[320px]"></textarea>

                        <button type="submit"
                                class="mt-4 w-full py-4 rounded-xl bg-cuan-green text-white font-black text-[10px] uppercase tracking-widest hover:bg-cuan-dark transition-all active:scale-95 shadow-lg shadow-cuan-green/20 flex items-center justify-center gap-2">
                            <i class="fas fa-paper-plane text-sm"></i>
                            Kirim Sekarang
                        </button>
                    </div>

                </div>
            </form>
        </x-card-container>

        {{-- RIWAYAT HEADER --}}
        <section class="flex items-center gap-3 pt-2">
            <div class="w-1 h-7 bg-cuan-green rounded-full"></div>
            <h3 class="text-lg font-black text-gray-900 tracking-tight">Riwayat Pengiriman</h3>
        </section>

        {{-- TABEL RIWAYAT --}}
        <x-card-container class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Waktu & Tipe</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Subjek</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Target</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Konten</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Admin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($broadcasts as $broadcast)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-8 py-4">
                                    <p class="text-xs font-black text-gray-900">{{ $broadcast->created_at->format('d M Y') }}</p>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">{{ $broadcast->created_at->format('H:i') }} WIB</p>
                                    <span class="inline-block mt-2 px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest border
                                        {{ $broadcast->type === 'maintenance_alert' ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-blue-50 text-blue-600 border-blue-100' }}">
                                        {{ $broadcast->type === 'maintenance_alert' ? 'Maintenance' : 'Custom' }}
                                    </span>
                                </td>
                                <td class="px-8 py-4">
                                    <p class="text-xs font-black text-gray-900 leading-snug">{{ $broadcast->subject }}</p>
                                </td>
                                <td class="px-8 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full {{ $broadcast->target_role ? 'bg-cuan-green' : 'bg-blue-500' }}"></div>
                                        <p class="text-xs font-bold text-gray-700">{{ $broadcast->target_role ? 'Semua Owner' : 'Spesifik User' }}</p>
                                    </div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">{{ $broadcast->total_recipients }} Penerima</p>
                                </td>
                                <td class="px-8 py-4 max-w-xs">
                                    <p class="text-[11px] text-gray-500 line-clamp-2 leading-relaxed font-medium">{{ $broadcast->content }}</p>
                                </td>
                                <td class="px-8 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $broadcast->admin->avatar_url }}" class="w-9 h-9 rounded-xl border border-gray-100 shadow-sm object-cover" alt="">
                                        <div>
                                            <p class="text-xs font-black text-gray-900">{{ $broadcast->admin->name }}</p>
                                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">System Admin</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-gray-50 rounded-[2rem] flex items-center justify-center border border-dashed border-gray-200 mb-6">
                                            <i class="fas fa-paper-plane text-3xl text-gray-300"></i>
                                        </div>
                                        <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest">Belum ada riwayat pengumuman</h3>
                                        <p class="text-[10px] font-bold text-gray-400 mt-2 max-w-sm mx-auto leading-relaxed">Buat pengumuman pertama Anda menggunakan form di atas.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($broadcasts->hasPages())
                <div class="px-8 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $broadcasts->links() }}
                </div>
            @endif
        </x-card-container>

    </div>
</main>

@push('scripts')
<script>
    function filterUsers(query) {
        const q = query.toLowerCase().trim();
        const items = document.querySelectorAll('#userList .user-item');
        const empty = document.getElementById('userSearchEmpty');
        let visible = 0;

        items.forEach(item => {
            const name = item.dataset.name || '';
            const email = item.dataset.email || '';
            const match = name.includes(q) || email.includes(q);
            item.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        empty.classList.toggle('hidden', visible > 0);
    }
</script>
@endpush

@endsection
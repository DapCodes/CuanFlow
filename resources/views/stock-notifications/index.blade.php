@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Pemberitahuan Stok - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Pemberitahuan Stok</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-[#f9fafb]" x-data="{ 
    activeFilter: '{{ $type }}'
}">
    <div class="max-w-6xl mx-auto space-y-8">
        
        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div class="animate-fade-in-down">
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Pemberitahuan Stok</h1>
                <p class="text-sm text-gray-500 font-medium mt-1">Pantau kondisi stok kritis dan kadaluarsa untuk menjaga kelancaran operasional.</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="markAllStockAsRead()" class="px-6 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl text-xs font-black uppercase tracking-wider hover:bg-gray-50 transition-all shadow-sm">
                    Tandai Semua Dibaca
                </button>
            </div>
        </div>

        {{-- Main Layout Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Navigation Sidebar --}}
            <aside class="lg:col-span-3 space-y-4">
                <nav class="hidden lg:flex flex-col gap-1.5 p-2 bg-white border border-gray-200 rounded-2xl shadow-sm">
                    <a href="{{ route('stock-notifications.index', ['type' => 'all']) }}" 
                       class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all font-bold text-sm text-left group {{ $type === 'all' ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}">
                        <i class="fas fa-list-ul text-lg opacity-40 group-hover:opacity-100 transition-opacity" :class="activeFilter === 'all' ? 'opacity-100' : ''"></i>
                        Semua
                    </a>
                    <a href="{{ route('stock-notifications.index', ['type' => 'unread']) }}" 
                       class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all font-bold text-sm text-left group {{ $type === 'unread' ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}">
                        <i class="fas fa-envelope-open text-lg opacity-40 group-hover:opacity-100 transition-opacity" :class="activeFilter === 'unread' ? 'opacity-100' : ''"></i>
                        Belum Dibaca
                    </a>
                    <a href="{{ route('stock-notifications.index', ['type' => 'product']) }}" 
                       class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all font-bold text-sm text-left group {{ $type === 'product' ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}">
                        <i class="fas fa-box text-lg opacity-40 group-hover:opacity-100 transition-opacity" :class="activeFilter === 'product' ? 'opacity-100' : ''"></i>
                        Produk
                    </a>
                    <a href="{{ route('stock-notifications.index', ['type' => 'stock']) }}" 
                       class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all font-bold text-sm text-left group {{ $type === 'stock' ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}">
                        <i class="fas fa-dolly text-lg opacity-40 group-hover:opacity-100 transition-opacity" :class="activeFilter === 'stock' ? 'opacity-100' : ''"></i>
                        Bahan Baku
                    </a>
                </nav>

                {{-- Mobile Navigation --}}
                <nav class="lg:hidden flex border border-gray-200 rounded-2xl bg-white p-1 overflow-x-auto no-scrollbar shadow-sm">
                    <a href="{{ route('stock-notifications.index', ['type' => 'all']) }}" class="flex-1 whitespace-nowrap px-4 py-3 rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center justify-center gap-2 {{ $type === 'all' ? 'bg-gray-100 text-gray-900 shadow-sm' : 'text-gray-500' }}">
                        <i class="fas fa-list-ul"></i> Semua
                    </a>
                    <a href="{{ route('stock-notifications.index', ['type' => 'unread']) }}" class="flex-1 whitespace-nowrap px-4 py-3 rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center justify-center gap-2 {{ $type === 'unread' ? 'bg-gray-100 text-gray-900 shadow-sm' : 'text-gray-500' }}">
                        <i class="fas fa-envelope-open"></i> Belum Dibaca
                    </a>
                    <a href="{{ route('stock-notifications.index', ['type' => 'product']) }}" class="flex-1 whitespace-nowrap px-4 py-3 rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center justify-center gap-2 {{ $type === 'product' ? 'bg-gray-100 text-gray-900 shadow-sm' : 'text-gray-500' }}">
                        <i class="fas fa-box"></i> Produk
                    </a>
                    <a href="{{ route('stock-notifications.index', ['type' => 'stock']) }}" class="flex-1 whitespace-nowrap px-4 py-3 rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center justify-center gap-2 {{ $type === 'stock' ? 'bg-gray-100 text-gray-900 shadow-sm' : 'text-gray-500' }}">
                        <i class="fas fa-dolly"></i> Stok
                    </a>
                </nav>
            </aside>

            {{-- Notifications List --}}
            <div class="lg:col-span-9 space-y-4" id="notifications-list">
                @forelse($notifications as $notification)
                <div class="notification-card bg-white border border-gray-200 rounded-2xl p-6 shadow-sm transition-all relative overflow-hidden animate-fade-in-up {{ $notification->is_read_by_me ? 'opacity-60 bg-gray-50/50 grayscale' : 'hover:shadow-md border-l-4' }}" 
                     id="notification-{{ $notification->id }}"
                     data-is-read="{{ $notification->is_read_by_me ? 'true' : 'false' }}">
                    <div class="flex flex-col sm:flex-row gap-6">
                        <div class="flex-shrink-0">
                            <div class="icon-container w-14 h-14 rounded-2xl flex items-center justify-center text-2xl 
                                {{ $notification->is_read_by_me 
                                    ? 'bg-gray-100 text-gray-400' 
                                    : (in_array($notification->type, ['out_of_stock', 'expired']) ? 'bg-red-50 text-red-500' : 'bg-orange-50 text-orange-500') 
                                }}">
                                <i class="fa-solid {{ in_array($notification->type, ['out_of_stock', 'expired']) ? 'fa-circle-xmark' : 'fa-triangle-exclamation' }}"></i>
                            </div>
                        </div>

                        {{-- Content Section --}}
                        <div class="flex-grow min-w-0">
                            <div class="flex justify-between items-start mb-1">
                                <h3 class="text-lg font-black text-gray-900 tracking-tight">{{ $notification->title }}</h3>
                                <span class="text-[10px] font-bold text-gray-400 bg-gray-100 px-2 py-1 rounded-lg uppercase tracking-wider">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 font-medium leading-relaxed mb-4">{{ $notification->message }}</p>
                            
                            <div class="flex flex-wrap items-center gap-4 pt-4 border-t border-gray-50">
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Sisa Stok:</span>
                                    <span class="text-sm font-bold text-gray-900">{{ number_format($notification->current_stock, 0) }}</span>
                                </div>
                                @if($notification->min_stock)
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Min Stok:</span>
                                    <span class="text-sm font-bold text-gray-900">{{ number_format($notification->min_stock, 0) }}</span>
                                </div>
                                @endif
                                
                                <div class="read-btn-container ml-auto">
                                    @unless($notification->is_read_by_me)
                                    <button onclick="readNotification({{ $notification->id }})" class="px-4 py-2 bg-gray-900 text-white rounded-xl text-[10px] font-black uppercase tracking-[0.1em] hover:bg-black transition-all active:scale-95">
                                        Tandai Sudah Dibaca
                                    </button>
                                    @endunless
                                </div>
                            </div>
                        </div>

                        {{-- Readers Avatars Section --}}
                        <div class="readers-container absolute top-4 right-4 flex items-center">
                            @php
                                $readers = $notification->readByUsers;
                            @endphp
                            @if($readers && $readers->count() > 0)
                            <div class="flex -space-x-2 overflow-hidden bg-white/80 backdrop-blur-sm p-1 rounded-full border border-gray-100 shadow-sm">
                                @foreach($readers->take(3) as $reader)
                                <img class="inline-block h-6 w-6 rounded-full ring-2 ring-white object-cover" 
                                     src="{{ $reader->avatar_url }}" 
                                     alt="{{ $reader->name }}" 
                                     title="Dibaca oleh {{ $reader->name }}">
                                @endforeach
                                @if($readers->count() > 3)
                                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-100 ring-2 ring-white">
                                    <span class="text-[8px] font-bold text-gray-500">+{{ $readers->count() - 3 }}</span>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white border border-gray-200 rounded-3xl p-12 text-center shadow-sm">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-box-open text-gray-300 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Semua Stok Berjalan Aman</h3>
                    <p class="text-sm text-gray-400 font-medium">Tidak ada pemberitahuan stok sesuai filter anda.</p>
                </div>
                @endforelse

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    function readNotification(id) {
        const card = document.getElementById(`notification-${id}`);
        const btn = card.querySelector('.read-btn-container button');
        
        // Visual feedback immediately
        if (btn) btn.classList.add('opacity-50', 'pointer-events-none');

        fetch(`/stock-notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update card appearance
                card.classList.add('opacity-60', 'bg-gray-50/50', 'grayscale');
                card.classList.remove('hover:shadow-md', 'border-l-4', 'border-l-orange-500', 'shadow-orange-100/20');
                
                const iconContainer = card.querySelector('.icon-container');
                if (iconContainer) {
                    iconContainer.classList.remove('bg-red-50', 'text-red-500', 'bg-orange-50', 'text-orange-500');
                    iconContainer.classList.add('bg-gray-100', 'text-gray-400');
                }

                if (btn) btn.remove();
                
                // Move card to the bottom of the current list (before pagination)
                // We'll move it to the very end of the #notifications-list
                const list = document.getElementById('notifications-list');
                const pagination = list.querySelector('.mt-8');
                if (pagination) {
                    list.insertBefore(card, pagination);
                } else {
                    list.appendChild(card);
                }

                // Optional: Update avatars without refresh could be complex without a fresh payload
                // For now, we just fade and move.
            } else {
                if (btn) btn.classList.remove('opacity-50', 'pointer-events-none');
            }
        })
        .catch(() => {
            if (btn) btn.classList.remove('opacity-50', 'pointer-events-none');
        });
    }

    function markAllStockAsRead() {
        if (!confirm('Tandai semua pemberitahuan sebagai dibaca?')) return;

        fetch('/stock-notifications/read-all', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }
</script>

<style>
    .animate-fade-in-down { animation: fade-in-down 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .animate-fade-in-up { animation: fade-in-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    
    @keyframes fade-in-down {
        0% { opacity: 0; transform: translateY(-20px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    @keyframes fade-in-up {
        0% { opacity: 0; transform: translateY(30px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endpush

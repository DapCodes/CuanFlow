@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Transfer Stok - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium">Transfer Stok</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Notifications via SweetAlert2 handled by app layout --}}

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Transfer Stok
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola perpindahan stok antar outlet dengan mudah, tercatat, dan akurat.
                </p>
            </div>
            @can('buat stock transfer')
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('stock-transfers.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-5 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                    <span>Transfer Baru</span>
                </a>
            </div>
            @endcan
        </section>

        {{-- RINGKASAN STATISTIK --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-card-container>
                <div class="p-6">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Draft Keluar</p>
                    <p class="mt-2 text-2xl font-black text-yellow-600">{{ number_format($stats['sent_pending']) }}</p>
                </div>
            </x-card-container>

            <x-card-container>
                <div class="p-6">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Terkirim Sukses</p>
                    <p class="mt-2 text-2xl font-black text-cuan-green">{{ number_format($stats['sent_completed']) }}</p>
                </div>
            </x-card-container>

            <x-card-container>
                <div class="p-6">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Masuk (Proses)</p>
                    <p class="mt-2 text-2xl font-black text-blue-600">{{ number_format($stats['received_pending']) }}</p>
                </div>
            </x-card-container>

            <x-card-container>
                <div class="p-6">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Diterima</p>
                    <p class="mt-2 text-2xl font-black text-emerald-600">{{ number_format($stats['received_completed']) }}</p>
                </div>
            </x-card-container>
        </section>

        {{-- KONTEN UTAMA --}}
        <x-card-container x-data="{ activeTab: 'outgoing' }">
            {{-- Tabs Header --}}
            <div class="flex border-b border-gray-100 bg-gray-50/30">
                <button 
                    @click="activeTab = 'outgoing'"
                    :class="activeTab === 'outgoing' ? 'text-cuan-green border-cuan-green bg-white' : 'text-gray-400 border-transparent hover:text-gray-600'"
                    class="flex-1 py-4 px-4 text-xs font-black uppercase tracking-widest border-b-2 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane text-[10px]"></i>
                    <span>Barang Keluar</span>
                </button>
                <button 
                    @click="activeTab = 'incoming'"
                    :class="activeTab === 'incoming' ? 'text-cuan-green border-cuan-green bg-white' : 'text-gray-400 border-transparent hover:text-gray-600'"
                    class="flex-1 py-4 px-4 text-xs font-black uppercase tracking-widest border-b-2 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-inbox text-[10px]"></i>
                    <span>Barang Masuk</span>
                </button>
            </div>

            {{-- Outgoing Content --}}
            <div x-show="activeTab === 'outgoing'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                @if($sentTransfers->isEmpty())
                <div class="text-center py-20">
                    <div class="w-16 h-16 bg-gray-50 border border-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-paper-plane text-gray-200 text-2xl"></i>
                    </div>
                    <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">Belum Ada Transfer Keluar</h3>
                    <p class="text-[11px] text-gray-500 font-bold uppercase tracking-widest mt-2 max-w-xs mx-auto">Anda belum mengirimkan stok ke outlet lain.</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left">No. Transfer</th>
                                <th class="px-6 py-4 text-left">Tujuan</th>
                                <th class="px-6 py-4 text-left">Tanggal</th>
                                <th class="px-6 py-4 text-left">Item</th>
                                <th class="px-6 py-4 text-left">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($sentTransfers as $trf)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <span class="text-[10px] font-black font-mono text-gray-500 bg-gray-100 px-2 py-1 rounded-lg border border-gray-100 uppercase tracking-tighter">
                                        {{ $trf->transfer_number }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-[10px] font-black border border-indigo-100">
                                            {{ substr($trf->toOutlet->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-[11px] font-bold text-gray-900 capitalize">{{ $trf->toOutlet->name }}</div>
                                            <div class="text-[9px] font-black uppercase tracking-widest text-gray-400">Outlet Tujuan</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="text-[11px] font-bold text-gray-900">
                                        {{ $trf->sent_at ? $trf->sent_at->format('d M Y') : $trf->created_at->format('d M Y') }}
                                    </div>
                                    <div class="text-[9px] font-black uppercase tracking-widest text-gray-400 mt-0.5">
                                        {{ $trf->sent_at ? $trf->sent_at->format('H:i') : 'Draft' }}
                                    </div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-[10px] font-black bg-gray-100 text-gray-600 border border-gray-200 uppercase tracking-tighter">
                                        {{ $trf->items->count() }} Jenis
                                    </span>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    @php
                                        $statusClass = match($trf->status) {
                                            'pending' => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                                            'in_transit' => 'bg-blue-50 text-blue-600 border-blue-100',
                                            'received' => 'bg-cuan-green/10 text-cuan-green border-cuan-green/10',
                                            'cancelled' => 'bg-red-50 text-red-600 border-red-100',
                                            default => 'bg-gray-50 text-gray-400 border-gray-200'
                                        };
                                        $statusLabel = match($trf->status) {
                                            'pending' => 'Draft',
                                            'in_transit' => 'Dikirim',
                                            'received' => 'Diterima',
                                            'cancelled' => 'Batal',
                                            default => ucfirst($trf->status)
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center">
                                        <a href="{{ route('stock-transfers.show', $trf->id) }}" 
                                           class="w-9 h-9 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white transition-all active:scale-95 border border-blue-100"
                                           title="Lihat Detail">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Pagination --}}
                @if($sentTransfers->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $sentTransfers->appends(['received_page' => request('received_page')])->links() }}
                </div>
                @endif
                @endif
            </div>

            {{-- Incoming Content --}}
            <div x-show="activeTab === 'incoming'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                @if($receivedTransfers->isEmpty())
                <div class="text-center py-20">
                    <div class="w-16 h-16 bg-gray-50 border border-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-inbox text-gray-200 text-2xl"></i>
                    </div>
                    <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">Belum Ada Transfer Masuk</h3>
                    <p class="text-[11px] text-gray-500 font-bold uppercase tracking-widest mt-2 max-w-xs mx-auto">Belum ada kiriman stok dari outlet lain.</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left">No. Transfer</th>
                                <th class="px-6 py-4 text-left">Dari</th>
                                <th class="px-6 py-4 text-left">Tanggal</th>
                                <th class="px-6 py-4 text-left">Item</th>
                                <th class="px-6 py-4 text-left">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($receivedTransfers as $trf)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <span class="text-[10px] font-black font-mono text-gray-500 bg-gray-100 px-2 py-1 rounded-lg border border-gray-100 uppercase tracking-tighter">
                                        {{ $trf->transfer_number }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-orange-50 text-orange-500 flex items-center justify-center text-[10px] font-black border border-orange-100">
                                            {{ substr($trf->fromOutlet->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-[11px] font-bold text-gray-900 capitalize">{{ $trf->fromOutlet->name }}</div>
                                            <div class="text-[9px] font-black uppercase tracking-widest text-gray-400">Outlet Asal</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="text-[11px] font-bold text-gray-900">
                                        {{ $trf->received_at ? $trf->received_at->format('d M Y') : 'Menunggu' }}
                                    </div>
                                    <div class="text-[9px] font-black uppercase tracking-widest text-gray-400 mt-0.5">
                                        {{ $trf->received_at ? $trf->received_at->format('H:i') : 'Masuk' }}
                                    </div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-[10px] font-black bg-gray-100 text-gray-600 border border-gray-200 uppercase tracking-tighter">
                                        {{ $trf->items->count() }} Jenis
                                    </span>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    @php
                                        $statusClass = match($trf->status) {
                                            'pending' => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                                            'in_transit' => 'bg-blue-50 text-blue-600 border-blue-100',
                                            'received' => 'bg-cuan-green/10 text-cuan-green border-cuan-green/10',
                                            'cancelled' => 'bg-red-50 text-red-600 border-red-100',
                                            default => 'bg-gray-50 text-gray-400 border-gray-200'
                                        };
                                        $statusLabel = match($trf->status) {
                                            'pending' => 'Draft',
                                            'in_transit' => 'Mendekat',
                                            'received' => 'Diterima',
                                            'cancelled' => 'Batal',
                                            default => ucfirst($trf->status)
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center">
                                        <a href="{{ route('stock-transfers.show', $trf->id) }}" 
                                           class="w-9 h-9 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white transition-all active:scale-95 border border-blue-100"
                                           title="Lihat Detail">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Pagination --}}
                @if($receivedTransfers->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $receivedTransfers->appends(['sent_page' => request('sent_page')])->links() }}
                </div>
                @endif
                @endif
            </div>
        </x-card-container>

    </div>
</main>
@endsection

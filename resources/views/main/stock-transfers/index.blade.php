@extends('layouts.app')

@section('title', 'Transfer Stok - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Transfer Stok</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Alert / Notifikasi --}}
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-check-circle mt-0.5 text-green-500"></i>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-exclamation-circle mt-0.5 text-red-500"></i>
                <p class="text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        {{-- HEADER HALAMAN --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-cyan-50 text-cyan-600 border border-cyan-100">
                        <i class="fas fa-truck-fast text-sm"></i>
                    </span>
                    <span>Transfer Stok</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola perpindahan stok antar outlet dengan mudah, tercatat, dan akurat.
                </p>
            </div>
            @can('buat stock transfer')
            <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                <a href="{{ route('stock-transfers.create') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-cyan-500 to-blue-500 px-4 py-2.5 text-sm font-semibold text-white hover:from-cyan-600 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:ring-offset-1 shadow-lg shadow-cyan-500/30 transition-all">
                    <i class="fas fa-plus-circle text-sm"></i>
                    <span>Transfer Baru</span>
                </a>
            </div>
            @endcan
        </section>

        {{-- RINGKASAN STATISTIK --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Pending Sent --}}
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Draft Keluar</p>
                        <p class="mt-1 text-2xl font-semibold text-yellow-600">{{ $stats['sent_pending'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-yellow-50 flex items-center justify-center border border-yellow-100">
                        <i class="fas fa-file-alt text-yellow-500 text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- Completed Sent --}}
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Terkirim Sukses</p>
                        <p class="mt-1 text-2xl font-semibold text-green-600">{{ $stats['sent_completed'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center border border-green-100">
                        <i class="fas fa-check-double text-green-500 text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- Incoming --}}
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Masuk (OTW)</p>
                        <p class="mt-1 text-2xl font-semibold text-blue-600">{{ $stats['received_pending'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center border border-blue-100">
                        <i class="fas fa-shipping-fast text-blue-500 text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- Received Completed --}}
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Diterima</p>
                        <p class="mt-1 text-2xl font-semibold text-emerald-600">{{ $stats['received_completed'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100">
                        <i class="fas fa-box-open text-emerald-500 text-lg"></i>
                    </div>
                </div>
            </div>
        </section>

        {{-- KONTEN UTAMA: TABEL --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden" x-data="{ activeTab: 'outgoing' }">
            
            {{-- Tabs Header --}}
            <div class="flex border-b border-gray-200 bg-gray-50/50">
                <button 
                    @click="activeTab = 'outgoing'"
                    :class="activeTab === 'outgoing' ? 'text-cyan-600 border-cyan-500 bg-white' : 'text-gray-500 border-transparent hover:text-gray-700 hover:bg-gray-100'"
                    class="flex-1 py-4 px-4 text-sm font-semibold border-b-2 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    <span>Barang Keluar (Sent)</span>
                </button>
                <button 
                    @click="activeTab = 'incoming'"
                    :class="activeTab === 'incoming' ? 'text-cyan-600 border-cyan-500 bg-white' : 'text-gray-500 border-transparent hover:text-gray-700 hover:bg-gray-100'"
                    class="flex-1 py-4 px-4 text-sm font-semibold border-b-2 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-inbox"></i>
                    <span>Barang Masuk (Incoming)</span>
                </button>
            </div>

            {{-- Outgoing Content --}}
            <div x-show="activeTab === 'outgoing'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                @if($sentTransfers->isEmpty())
                <div class="text-center py-16">
                    <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4 text-gray-300">
                        <i class="fas fa-paper-plane text-3xl"></i>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">Belum ada transfer keluar</h3>
                    <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">Anda belum mengirimkan stok ke outlet lain.</p>
                    @can('buat stock transfer')
                    <div class="mt-4">
                        <a href="{{ route('stock-transfers.create') }}" class="text-cyan-600 font-medium hover:text-cyan-700 text-sm">
                            + Buat Transfer Baru
                        </a>
                    </div>
                    @endcan
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500 uppercase tracking-wide font-semibold">
                            <tr>
                                <th class="px-6 py-3 text-left">No. Transfer</th>
                                <th class="px-6 py-3 text-left">Tujuan</th>
                                <th class="px-6 py-3 text-left">Tanggal</th>
                                <th class="px-6 py-3 text-left">Total Item</th>
                                <th class="px-6 py-3 text-left">Status</th>
                                <th class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($sentTransfers as $trf)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-3 font-mono font-semibold text-gray-700">
                                    {{ $trf->transfer_number }}
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold">
                                            {{ substr($trf->toOutlet->name, 0, 1) }}
                                        </div>
                                        <span class="font-medium text-gray-900">{{ $trf->toOutlet->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-gray-600">
                                    {{ $trf->sent_at ? $trf->sent_at->format('d/m/Y H:i') : ($trf->created_at->format('d/m/Y') . ' (Draft)') }}
                                </td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $trf->items->count() }} Jenis
                                    </span>
                                </td>
                                <td class="px-6 py-3">
                                    @php
                                        $statusClass = match($trf->status) {
                                            'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
                                            'in_transit' => 'bg-blue-50 text-blue-700 border-blue-100',
                                            'received' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                            'cancelled' => 'bg-red-50 text-red-700 border-red-100',
                                            default => 'bg-gray-50 text-gray-700 border-gray-100'
                                        };
                                        $statusLabel = match($trf->status) {
                                            'pending' => 'Draft',
                                            'in_transit' => 'Dikirim',
                                            'received' => 'Diterima',
                                            'cancelled' => 'Batal',
                                            default => ucfirst($trf->status)
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <a href="{{ route('stock-transfers.show', $trf->id) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-cyan-600 hover:border-cyan-200 transition-all"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $sentTransfers->appends(['received_page' => request('received_page')])->links() }}
                </div>
                @endif
            </div>

            {{-- Incoming Content --}}
            <div x-show="activeTab === 'incoming'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                @if($receivedTransfers->isEmpty())
                <div class="text-center py-16">
                    <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4 text-gray-300">
                        <i class="fas fa-inbox text-3xl"></i>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">Belum ada transfer masuk</h3>
                    <p class="text-sm text-gray-500 mt-1">Belum ada kiriman stok dari outlet lain.</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500 uppercase tracking-wide font-semibold">
                            <tr>
                                <th class="px-6 py-3 text-left">No. Transfer</th>
                                <th class="px-6 py-3 text-left">Dari</th>
                                <th class="px-6 py-3 text-left">Tanggal Terima</th>
                                <th class="px-6 py-3 text-left">Total Item</th>
                                <th class="px-6 py-3 text-left">Status</th>
                                <th class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($receivedTransfers as $trf)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-3 font-mono font-semibold text-gray-700">
                                    {{ $trf->transfer_number }}
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center text-xs font-bold">
                                            {{ substr($trf->fromOutlet->name, 0, 1) }}
                                        </div>
                                        <span class="font-medium text-gray-900">{{ $trf->fromOutlet->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-gray-600">
                                    @if($trf->received_at)
                                        {{ $trf->received_at->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-xs text-blue-500 font-medium bg-blue-50 px-2 py-0.5 rounded">Estimasi: Segera</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $trf->items->count() }} Jenis
                                    </span>
                                </td>
                                <td class="px-6 py-3">
                                    @php
                                        $statusClass = match($trf->status) {
                                            'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
                                            'in_transit' => 'bg-blue-50 text-blue-700 border-blue-100',
                                            'received' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                            'cancelled' => 'bg-red-50 text-red-700 border-red-100',
                                            default => 'bg-gray-50 text-gray-700 border-gray-100'
                                        };
                                         $statusLabel = match($trf->status) {
                                            'pending' => 'Menunggu Kirim',
                                            'in_transit' => 'OTW (Masuk)',
                                            'received' => 'Diterima',
                                            'cancelled' => 'Batal',
                                            default => ucfirst($trf->status)
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <a href="{{ route('stock-transfers.show', $trf->id) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-cyan-600 hover:border-cyan-200 transition-all"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $receivedTransfers->appends(['sent_page' => request('sent_page')])->links() }}
                </div>
                @endif
            </div>

        </section>

    </div>
</main>
@endsection

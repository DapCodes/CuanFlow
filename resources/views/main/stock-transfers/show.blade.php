@extends('layouts.app')

@section('title', 'Detail Transfer #' . $stockTransfer->transfer_number . ' - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('stock-transfers.index') }}" class="text-gray-500 hover:text-gray-700">Transfer Stok</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Detail #{{ $stockTransfer->transfer_number }}</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Notifikasi --}}
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-check-circle mt-0.5 text-green-500"></i>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-exclamation-triangle mt-0.5 text-red-500"></i>
                <p class="text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        {{-- HEADER --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="space-y-1">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-cyan-50 text-cyan-600 border border-cyan-100">
                        <i class="fas fa-box text-sm"></i>
                    </span>
                    <div>
                        <h1 class="text-xl md:text-2xl font-semibold text-gray-900">
                            Transfer #{{ $stockTransfer->transfer_number }}
                        </h1>
                        <p class="text-xs md:text-sm text-gray-500 mt-0.5">
                            Dibuat: {{ $stockTransfer->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                </div>

                {{-- Status Pills --}}
                <div class="mt-2 pl-1">
                     @php
                        $statusClass = match($stockTransfer->status) {
                            'pending' => 'bg-yellow-50 text-yellow-700 border border-yellow-100',
                            'in_transit' => 'bg-blue-50 text-blue-700 border border-blue-100',
                            'received' => 'bg-emerald-50 text-emerald-700 border border-emerald-100',
                            'cancelled' => 'bg-red-50 text-red-700 border border-red-100',
                            default => 'bg-gray-50 text-gray-700 border border-gray-200'
                        };
                        $statusIcon = match($stockTransfer->status) {
                            'pending' => 'clock',
                            'in_transit' => 'truck',
                            'received' => 'check-circle',
                            'cancelled' => 'ban',
                            default => 'circle'
                        };
                         $statusLabel = match($stockTransfer->status) {
                            'pending' => 'Pending (Draft)',
                            'in_transit' => 'Dalam Perjalanan',
                            'received' => 'Diterima (Selesai)',
                            'cancelled' => 'Dibatalkan',
                            default => ucfirst($stockTransfer->status)
                        };
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                        <i class="fas fa-{{ $statusIcon }} mr-1.5"></i>
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>

            {{-- Actions (Right Side) --}}
            <div class="flex flex-col md:flex-row gap-2 md:gap-3 w-full md:w-auto md:justify-end">
                {{-- Logic for Sender --}}
                @if(auth()->user()->outlet_id === $stockTransfer->from_outlet_id)
                    @if($stockTransfer->status === 'pending')
                         @can('batalkan stock transfer')
                        <form action="{{ route('stock-transfers.destroy', $stockTransfer->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan transfer ini?');" class="w-full md:w-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-lg border border-red-200 text-red-600 hover:bg-red-50 transition-colors">
                                <i class="fas fa-times mr-2"></i> Batalkan
                            </button>
                        </form>
                        @endcan
                        
                        @can('proses stock transfer')
                        <form action="{{ route('stock-transfers.send', $stockTransfer->id) }}" method="POST" onsubmit="return confirm('Kirim sekarang? Stok akan dikurangi dari gudang Anda.');" class="w-full md:w-auto">
                            @csrf
                            <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-500 text-sm font-semibold text-white rounded-lg hover:from-cyan-600 hover:to-blue-600 shadow-md transition-all">
                                <i class="fas fa-truck-fast mr-2"></i> Kirim Stok
                            </button>
                        </form>
                        @endcan
                    @endif
                @endif

                {{-- Logic for Receiver --}}
                @if(auth()->user()->outlet_id === $stockTransfer->to_outlet_id)
                    @if($stockTransfer->status === 'in_transit')
                         @can('terima stock transfer')
                        <form action="{{ route('stock-transfers.receive', $stockTransfer->id) }}" method="POST" onsubmit="return confirm('Pastikan barang fisik sudah diterima. Lanjutkan?');" class="w-full md:w-auto">
                            @csrf
                            <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-emerald-500 text-sm font-semibold text-white rounded-lg hover:bg-emerald-600 shadow-md transition-all">
                                <i class="fas fa-check-double mr-2"></i> Terima Barang
                            </button>
                        </form>
                        @endcan
                    @endif
                @endif
            </div>
        </section>

        {{-- MAIN CONTENT GRID --}}
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- LEFT COLUMN: ITEMS LIST (Wide) --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h2 class="text-base font-semibold text-gray-900">Daftar Barang Transfer</h2>
                        <span class="bg-white border border-gray-200 text-gray-600 px-2.5 py-1 rounded text-xs font-semibold">
                            Total: {{ $stockTransfer->items->count() }} Item
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-500 font-semibold border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-3 w-10">#</th>
                                    <th class="px-6 py-3">Nama Barang</th>
                                    <th class="px-6 py-3">Tipe</th>
                                    <th class="px-6 py-3 text-right">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($stockTransfer->items as $index => $item)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-6 py-3 text-gray-400">{{ $index + 1 }}</td>
                                    <td class="px-6 py-3 font-medium text-gray-800">
                                        {{ $item->stockable->name ?? 'Item Terhapus' }}
                                    </td>
                                    <td class="px-6 py-3">
                                        @if($item->stockable_type == 'App\Models\Product')
                                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-purple-50 text-purple-600 border border-purple-100">Produk</span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-orange-50 text-orange-600 border border-orange-100">Bahan Baku</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-right font-mono text-gray-700 font-semibold">
                                        {{ number_format($item->quantity, 0, ',', '.') }}
                                        <span class="text-xs text-gray-400 ml-1 font-sans font-normal">{{ $item->stockable->unit->name ?? 'Unit' }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($stockTransfer->notes)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-4">
                    <i class="fas fa-sticky-note text-amber-500 mt-1"></i>
                    <div>
                        <h4 class="text-sm font-semibold text-amber-800">Catatan Tambahan</h4>
                        <p class="text-sm text-amber-700 mt-1">"{{ $stockTransfer->notes }}"</p>
                    </div>
                </div>
                @endif
            </div>

            {{-- RIGHT COLUMN: INFO & TIMELINE --}}
            <div class="space-y-6">
                
                {{-- Route Info --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Rute Pengiriman</h2>
                    <div class="relative pl-4 border-l-2 border-gray-100 space-y-6">
                        {{-- From --}}
                        <div class="relative">
                            <span class="absolute -left-[21px] top-1 w-3 h-3 rounded-full bg-white border-2 border-orange-400"></span>
                            <p class="text-xs text-gray-500 uppercase font-semibold">DARI (PENGIRIM)</p>
                            <p class="text-sm font-bold text-gray-800 mt-1">{{ $stockTransfer->fromOutlet->name }}</p>
                            <p class="text-xs text-gray-400">{{ $stockTransfer->creator->name ?? '-' }} (Pembuat)</p>
                        </div>
                        {{-- To --}}
                        <div class="relative">
                            <span class="absolute -left-[21px] top-1 w-3 h-3 rounded-full bg-white border-2 border-indigo-500"></span>
                            <p class="text-xs text-gray-500 uppercase font-semibold">KE (PENERIMA)</p>
                            <p class="text-sm font-bold text-gray-800 mt-1">{{ $stockTransfer->toOutlet->name }}</p>
                            @if($stockTransfer->received_by)
                            <p class="text-xs text-gray-400">{{ $stockTransfer->receiver->name ?? '-' }} (Penerima)</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Status Timeline --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Riwayat Status</h2>
                    <div class="space-y-5">
                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 mt-1.5 ring-4 ring-emerald-50"></div>
                                <div class="w-0.5 h-full bg-gray-100 my-1"></div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Dibuat (Draft)</p>
                                <p class="text-xs text-gray-500">{{ $stockTransfer->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>

                        <div class="flex gap-3 {{ $stockTransfer->sent_at ? '' : 'opacity-50 grayscale' }}">
                            <div class="flex flex-col items-center">
                                <div class="w-2.5 h-2.5 rounded-full {{ $stockTransfer->sent_at ? 'bg-blue-500 ring-4 ring-blue-50' : 'bg-gray-300' }} mt-1.5"></div>
                                <div class="w-0.5 h-full bg-gray-100 my-1"></div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Dikirim</p>
                                <p class="text-xs text-gray-500">{{ $stockTransfer->sent_at ? $stockTransfer->sent_at->format('d M Y, H:i') : '-' }}</p>
                            </div>
                        </div>

                        <div class="flex gap-3 {{ $stockTransfer->received_at ? '' : 'opacity-50 grayscale' }}">
                            <div class="flex flex-col items-center">
                                <div class="w-2.5 h-2.5 rounded-full {{ $stockTransfer->received_at ? 'bg-green-500 ring-4 ring-green-50' : 'bg-gray-300' }} mt-1.5"></div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Diterima</p>
                                <p class="text-xs text-gray-500">{{ $stockTransfer->received_at ? $stockTransfer->received_at->format('d M Y, H:i') : '-' }}</p>
                            </div>
                        </div>

                        @if($stockTransfer->status === 'cancelled')
                        <div class="flex gap-3 mt-4 pt-4 border-t border-red-100">
                             <div class="flex flex-col items-center">
                                <div class="w-2.5 h-2.5 rounded-full bg-red-500 mt-1.5 ring-4 ring-red-50"></div>
                            </div>
                             <div>
                                <p class="text-sm font-medium text-red-600">Dibatalkan</p>
                                <p class="text-xs text-gray-500">{{ $stockTransfer->updated_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </section>

    </div>
</main>
@endsection

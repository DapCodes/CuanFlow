@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Detail Transfer #' . $stockTransfer->transfer_number . ' - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('stock-transfers.index') }}" class="text-gray-600 hover:text-gray-900 font-medium">Transfer Stok</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Detail #{{ $stockTransfer->transfer_number }}</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Notifications via SweetAlert2 handled by app layout --}}

        {{-- HEADER --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-xl md:text-2xl font-black text-gray-900">
                        Transfer #{{ $stockTransfer->transfer_number }}
                    </h1>
                    @php
                        $statusClass = match($stockTransfer->status) {
                            'pending' => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                            'in_transit' => 'bg-blue-50 text-blue-600 border-blue-100',
                            'received' => 'bg-cuan-green/10 text-cuan-green border-cuan-green/10',
                            'cancelled' => 'bg-red-50 text-red-600 border-red-100',
                             default => 'bg-gray-50 text-gray-400 border-gray-200'
                        };
                        $statusLabel = match($stockTransfer->status) {
                            'pending' => 'Pending (Draft)',
                            'in_transit' => 'Dalam Perjalanan',
                            'received' => 'Selesai',
                            'cancelled' => 'Dibatalkan',
                             default => ucfirst($stockTransfer->status)
                        };
                    @endphp
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-gray-500 font-medium capitalize">
                    Dibuat pada {{ $stockTransfer->created_at->format('d M Y, H:i') }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('stock-transfers.index') }}" class="inline-flex items-center justify-center h-11 px-6 bg-white text-gray-700 border border-gray-200 rounded-xl text-sm font-black hover:bg-gray-50 transition-all active:scale-95 shadow-sm">
                    Kembali
                </a>
                
                {{-- Actions --}}
                @if(auth()->user()->outlet_id === $stockTransfer->from_outlet_id)
                    @if($stockTransfer->status === 'pending')
                         @can('batalkan stock transfer')
                        <form action="{{ route('stock-transfers.destroy', $stockTransfer->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                class="confirm-delete h-11 px-6 bg-white text-red-600 border border-red-100 rounded-xl text-sm font-black hover:bg-red-50 transition-all active:scale-95 shadow-sm"
                                data-name="Transfer #{{ $stockTransfer->transfer_number }}">
                                Batalkan
                            </button>
                        </form>
                        @endcan
                        
                        @can('proses stock transfer')
                        <form action="{{ route('stock-transfers.send', $stockTransfer->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                class="confirm-toggle h-11 px-8 bg-cuan-green text-white rounded-xl text-sm font-black hover:bg-cuan-dark transition-all active:scale-95 shadow-lg shadow-cuan-green/20"
                                data-status="kirim"
                                data-name="barang transfer ini sekarang">
                                Kirim Sekarang
                            </button>
                        </form>
                        @endcan
                    @endif
                @endif

                @if(auth()->user()->outlet_id === $stockTransfer->to_outlet_id)
                    @if($stockTransfer->status === 'in_transit')
                         @can('terima stock transfer')
                        <form action="{{ route('stock-transfers.receive', $stockTransfer->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                class="confirm-toggle h-11 px-8 bg-blue-600 text-white rounded-xl text-sm font-black hover:bg-blue-700 transition-all active:scale-95 shadow-lg shadow-blue-500/20"
                                data-status="konfirmasi penerimaan"
                                data-name="barang ini">
                                Terima Barang
                            </button>
                        </form>
                        @endcan
                    @endif
                @endif
            </div>
        </section>

        {{-- MAIN CONTENT --}}
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Daftar Barang --}}
            <div class="lg:col-span-2 space-y-6">
                <x-card-container>
                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/30">
                        <h2 class="text-xs font-black text-gray-900 uppercase tracking-widest">Detail Barang</h2>
                        <span class="text-[10px] font-black uppercase tracking-widest text-cuan-green bg-cuan-green/10 px-3 py-1 rounded-full">
                            {{ $stockTransfer->items->count() }} Jenis
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4 text-left">Nama Barang</th>
                                    <th class="px-6 py-4 text-left">Tipe</th>
                                    <th class="px-6 py-4 text-right">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach($stockTransfer->items as $item)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-[11px] font-bold text-gray-900 capitalize">{{ $item->stockable->name ?? 'Item Terhapus' }}</div>
                                        <div class="text-[9px] font-black uppercase tracking-widest text-gray-400 mt-0.5">SKU: {{ $item->stockable->sku ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($item->stockable_type == 'App\Models\Product')
                                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest bg-purple-50 text-purple-500 border border-purple-100">Produk</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest bg-orange-50 text-orange-500 border border-orange-100">Bahan Baku</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-[11px] font-black text-gray-900">
                                            {{ number_format($item->quantity, 0, ',', '.') }}
                                            <span class="text-[9px] text-gray-400 uppercase tracking-widest ml-1">{{ $item->stockable->unit->name ?? 'Unit' }}</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-card-container>

                @if($stockTransfer->notes)
                <div class="bg-amber-50/50 border border-amber-100 rounded-2xl p-6">
                    <h4 class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-2 flex items-center gap-2">
                        <i class="fas fa-sticky-note text-xs"></i> Catatan Tambahan
                    </h4>
                    <p class="text-sm text-gray-600 font-medium italic leading-relaxed">"{{ $stockTransfer->notes }}"</p>
                </div>
                @endif
            </div>

            {{-- Info Pengiriman --}}
            <div class="space-y-6">
                {{-- Rute --}}
                <x-card-container>
                    <div class="p-6">
                        <h2 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-6">Informasi Rute</h2>
                        <div class="relative pl-6 border-l-2 border-gray-100 space-y-8">
                            {{-- From --}}
                            <div class="relative">
                                <span class="absolute -left-[31px] top-1 w-2.5 h-2.5 rounded-full bg-white border-2 border-orange-400"></span>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Dari (Pengirim)</p>
                                <p class="text-sm font-black text-gray-800 mt-1 capitalize">{{ $stockTransfer->fromOutlet->name }}</p>
                                <p class="text-[10px] text-gray-500 font-bold mt-0.5">{{ $stockTransfer->creator->name ?? '-' }} (Admin)</p>
                            </div>
                            {{-- To --}}
                            <div class="relative">
                                <span class="absolute -left-[31px] top-1 w-2.5 h-2.5 rounded-full bg-white border-2 border-indigo-500"></span>
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Ke (Penerima)</p>
                                <p class="text-sm font-black text-gray-800 mt-1 capitalize">{{ $stockTransfer->toOutlet->name }}</p>
                                @if($stockTransfer->received_by)
                                <p class="text-[10px] text-gray-500 font-bold mt-0.5">{{ $stockTransfer->receiver->name ?? '-' }} (Penerima)</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </x-card-container>

                {{-- Riwayat --}}
                <x-card-container>
                    <div class="p-6">
                        <h2 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-6">Riwayat Status</h2>
                        <div class="space-y-6">
                            <div class="flex items-center gap-4">
                                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-50"></div>
                                <div>
                                    <p class="text-[11px] font-black text-gray-900 uppercase tracking-tight">Dibuat (Draft)</p>
                                    <p class="text-[10px] text-gray-400 font-bold">{{ $stockTransfer->created_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 {{ $stockTransfer->sent_at ? '' : 'opacity-30' }}">
                                <div class="w-2.5 h-2.5 rounded-full {{ $stockTransfer->sent_at ? 'bg-blue-500 ring-4 ring-blue-50' : 'bg-gray-300' }}"></div>
                                <div>
                                    <p class="text-[11px] font-black text-gray-900 uppercase tracking-tight">Dikirim</p>
                                    <p class="text-[10px] text-gray-400 font-bold">{{ $stockTransfer->sent_at ? $stockTransfer->sent_at->format('d M Y, H:i') : 'Menunggu' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 {{ $stockTransfer->received_at ? '' : 'opacity-30' }}">
                                <div class="w-2.5 h-2.5 rounded-full {{ $stockTransfer->received_at ? 'bg-cuan-green ring-4 ring-cuan-green/10' : 'bg-gray-300' }}"></div>
                                <div>
                                    <p class="text-[11px] font-black text-gray-900 uppercase tracking-tight">Diterima</p>
                                    <p class="text-[10px] text-gray-400 font-bold">{{ $stockTransfer->received_at ? $stockTransfer->received_at->format('d M Y, H:i') : 'Menunggu' }}</p>
                                </div>
                            </div>

                            @if($stockTransfer->status === 'cancelled')
                            <div class="pt-4 mt-4 border-t border-red-50 flex items-center gap-4">
                                <div class="w-2.5 h-2.5 rounded-full bg-red-500 ring-4 ring-red-50"></div>
                                <div>
                                    <p class="text-[11px] font-black text-red-600 uppercase tracking-tight">Dibatalkan</p>
                                    <p class="text-[10px] text-gray-400 font-bold">{{ $stockTransfer->updated_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </x-card-container>
            </div>
        </section>

    </div>
</main>
@endsection

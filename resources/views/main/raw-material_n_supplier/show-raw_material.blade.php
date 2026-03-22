@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Detail Bahan Baku - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('raw-materials.index') }}" class="text-gray-500 hover:text-cuan-green transition-colors">Bahan Baku</a>
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Detail Produk</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">
        
        @php
            $stock = $rawMaterial->stocks->first();
            $currentStock = $stock ? $stock->quantity : 0;
            $isOutOfStock = $currentStock <= 0;
            $isLowStock = $currentStock <= $rawMaterial->min_stock && !$isOutOfStock;
        @endphp

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gray-50 border-2 border-white shadow-sm overflow-hidden flex-shrink-0 flex items-center justify-center">
                    @if($rawMaterial->image)
                        <img src="{{ Storage::url($rawMaterial->image) }}" class="w-full h-full object-cover">
                    @else
                        <i class="fas fa-cube text-gray-300 text-2xl"></i>
                    @endif
                </div>
                <div>
                    <h1 class="text-xl md:text-2xl font-black text-gray-900 leading-tight">
                        {{ $rawMaterial->name }}
                    </h1>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-[10px] font-black font-mono text-gray-400 bg-gray-100 px-2 py-1 rounded-lg border border-gray-100 uppercase tracking-tighter">{{ $rawMaterial->code }}</span>
                        @if($rawMaterial->barcode)
                            <span class="text-xs text-gray-300 font-bold">|</span>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $rawMaterial->barcode }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('raw-materials.index') }}" 
                   class="inline-flex items-center gap-2 rounded-xl bg-white border border-gray-200 px-5 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all shadow-sm active:scale-95">
                    <span>Kembali</span>
                </a>
                @can('edit bahan baku')
                <a href="{{ route('raw-materials.edit', $rawMaterial) }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-5 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                    <span>Edit Data</span>
                </a>
                @endcan
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Kolom Kiri --}}
            <div class="lg:col-span-2 space-y-6">
                
                <x-card-container title="Status Inventaris">
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div class="p-6 rounded-[2rem] bg-gray-50 border border-gray-100 text-center">
                                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-400">Tersedia</p>
                                <p class="text-4xl font-black mt-2 {{ $isOutOfStock ? 'text-red-600' : ($isLowStock ? 'text-orange-500' : 'text-emerald-600') }} tracking-tighter">
                                    {{ number_format($currentStock, 2) }}
                                </p>
                                <p class="text-[10px] font-bold text-gray-400 uppercase mt-2 tracking-widest">{{ $rawMaterial->unit->abbreviation }}</p>
                            </div>

                            <div class="p-6 rounded-[2rem] bg-white border border-gray-100 text-center">
                                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-400">Min. Stok</p>
                                <p class="text-4xl font-black mt-2 text-gray-900 tracking-tighter">
                                    {{ number_format($rawMaterial->min_stock, 2) }}
                                </p>
                                <p class="text-[10px] font-bold text-gray-400 uppercase mt-2 tracking-widest">{{ $rawMaterial->unit->abbreviation }}</p>
                            </div>

                            <div class="p-6 rounded-[2rem] bg-white border border-gray-100 text-center">
                                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-400">Kategori</p>
                                <div class="mt-2 min-h-[40px] flex items-center justify-center">
                                    <span class="px-4 py-2 bg-cuan-green/10 text-cuan-green rounded-xl text-[10px] font-black uppercase tracking-widest">
                                        {{ $rawMaterial->category->name ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        @if($currentStock > 0)
                        <div class="mt-8 space-y-3">
                            <div class="flex items-center justify-between text-[11px] font-black uppercase tracking-widest">
                                <span class="text-gray-400">Kapasitas Stok</span>
                                @php
                                    $percentage = ($currentStock / max($rawMaterial->min_stock * 2, 1)) * 100;
                                    $percentage = min($percentage, 100);
                                @endphp
                                <span class="{{ $isLowStock ? 'text-orange-500' : 'text-cuan-green' }}">{{ number_format($percentage, 0) }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden border border-gray-50">
                                <div class="h-full rounded-full transition-all duration-1000 {{ $isLowStock ? 'bg-orange-400 shadow-[0_0_15px_rgba(251,146,60,0.5)]' : 'bg-cuan-green shadow-[0_0_15px_rgba(101,140,88,0.5)]' }}" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        @endif
                    </div>
                </x-card-container>

                <x-card-container title="Detail Informasi">
                    <div class="p-8 space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Harga Beli Rata-rata</label>
                                <p class="text-2xl font-black text-gray-900 tracking-tight">Rp {{ number_format($rawMaterial->purchase_price, 0, ',', '.') }} <span class="text-[11px] font-bold text-gray-400">/ {{ $rawMaterial->unit->abbreviation }}</span></p>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Masa Simpan</label>
                                <p class="text-2xl font-black text-gray-900 tracking-tight">{{ $rawMaterial->shelf_life_days ?? 'N/A' }} <span class="text-[11px] font-bold text-gray-400">Hari</span></p>
                            </div>
                        </div>

                        <div class="pt-8 border-t border-gray-50">
                             <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Keterangan & Deskripsi</label>
                             <div class="mt-3 p-6 rounded-2xl bg-gray-50 text-sm font-bold text-gray-700 leading-relaxed border border-gray-100 italic">
                                 {!! nl2br(e($rawMaterial->description ?: 'Tidak ada deskripsi tambahan untuk bahan baku ini.')) !!}
                             </div>
                        </div>
                    </div>
                </x-card-container>

                <x-card-container title="Supplier Utama">
                    <div class="p-8">
                        @if($rawMaterial->supplier)
                        <div class="flex items-start gap-6">
                            <div class="w-16 h-16 rounded-2xl bg-cuan-green/10 text-cuan-green flex items-center justify-center text-2xl flex-shrink-0">
                                <i class="fas fa-truck-loading"></i>
                            </div>
                            <div class="flex-grow">
                                <h4 class="text-xl font-black text-gray-900 uppercase tracking-tight">{{ $rawMaterial->supplier->name }}</h4>
                                <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex items-center gap-2 text-sm font-bold text-gray-500">
                                        <i class="fas fa-phone-alt text-cuan-green"></i>
                                        <span>{{ $rawMaterial->supplier->phone ?: 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm font-bold text-gray-500">
                                        <i class="fas fa-user text-cuan-green"></i>
                                        <span>{{ $rawMaterial->supplier->contact_person ?: 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                            @can('lihat supplier')
                            <a href="{{ route('raw-materials.suppliers.show', $rawMaterial->supplier) }}" class="px-5 py-2.5 rounded-xl bg-gray-900 text-[10px] font-black uppercase text-white hover:bg-black transition-all">Profil</a>
                            @endcan
                        </div>
                        @else
                        <div class="text-center py-6 text-gray-400 text-xs font-bold uppercase tracking-widest italic flex flex-col items-center gap-2">
                             <i class="fas fa-info-circle text-lg opacity-30"></i>
                             Belum Ada Supplier Utama Terdaftar (Umum)
                        </div>
                        @endif
                    </div>
                </x-card-container>

            </div>

            {{-- Kolom Kanan --}}
            <div class="lg:col-span-1 space-y-6">
                
                <x-card-container title="Manajemen Cepat">
                    <div class="p-6 space-y-4">
                        @can('kelola stok bahan baku')
                        <a href="{{ route('raw-materials.manage-stock', $rawMaterial) }}" 
                           class="flex items-center justify-between w-full p-5 rounded-2xl bg-cuan-green text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 group">
                            <div class="flex items-center gap-4">
                                <i class="fas fa-dolly text-xl opacity-80"></i>
                                <span class="font-black uppercase tracking-widest text-xs">Kelola Stok</span>
                            </div>
                            <i class="fas fa-arrow-right text-[10px] transition-transform group-hover:translate-x-1"></i>
                        </a>

                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('raw-materials.manage-stock', $rawMaterial) }}?type=add" class="flex flex-col items-center justify-center p-4 rounded-2xl bg-white border border-gray-100 hover:border-cuan-green hover:text-cuan-green transition-all shadow-sm">
                                <i class="fas fa-plus-circle text-lg mb-2"></i>
                                <span class="text-[9px] font-black uppercase tracking-widest">Tambah</span>
                            </a>
                            <a href="{{ route('raw-materials.manage-stock', $rawMaterial) }}?type=reduce" class="flex flex-col items-center justify-center p-4 rounded-2xl bg-white border border-gray-100 hover:border-orange-500 hover:text-orange-500 transition-all shadow-sm">
                                <i class="fas fa-minus-circle text-lg mb-2"></i>
                                <span class="text-[9px] font-black uppercase tracking-widest">Kurangi</span>
                            </a>
                        </div>
                        @endcan

                        @can('lihat riwayat stok bahan baku')
                        <a href="{{ route('raw-materials.stock-history', $rawMaterial) }}" 
                           class="flex items-center gap-4 w-full p-4 rounded-2xl bg-white border border-gray-100 text-gray-500 hover:border-blue-500 hover:text-blue-500 transition-all font-bold text-xs uppercase tracking-widest">
                            <i class="fas fa-history text-lg"></i>
                            <span>Riwayat Mutasi</span>
                        </a>
                        @endcan
                    </div>
                </x-card-container>

                <div class="bg-gray-900 rounded-[2rem] p-8 text-white shadow-2xl relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-[9px] font-black uppercase tracking-widest opacity-50">Valuasi Stok</p>
                        <p class="text-3xl font-black mt-3 flex items-baseline gap-2 tracking-tighter">
                            <span class="text-xs opacity-50">RP</span>
                            {{ number_format($currentStock * $rawMaterial->purchase_price, 0, ',', '.') }}
                        </p>
                        <div class="mt-6 pt-6 border-t border-white/5 space-y-2">
                             <div class="flex justify-between text-[9px] font-black uppercase opacity-60">
                                 <span>Status Sistem</span>
                                 @if($rawMaterial->is_active)
                                    <span class="text-cuan-green">Online</span>
                                 @else
                                    <span class="text-red-500">Offline</span>
                                 @endif
                             </div>
                        </div>
                    </div>
                </div>

                {{-- DANGER ZONE --}}
                @can('hapus bahan baku')
                <div class="p-6 rounded-[2rem] bg-red-50/30 border border-red-50 text-center">
                    <button type="button" onclick="confirmDeleteRawMaterial()"
                            class="text-[10px] font-black text-red-400 uppercase tracking-widest hover:text-red-600 transition-colors">
                        Hapus Permanen
                    </button>
                    <form id="delete-raw-material-form" action="{{ route('raw-materials.destroy', $rawMaterial) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
                @endcan
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
window.confirmDeleteRawMaterial = function() {
    Swal.fire({
        title: 'Hapus Permanen?',
        text: "Seluruh riwayat stok untuk bahan ini akan ikut terhapus.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        customClass: {
            popup: 'rounded-[2rem] border-none shadow-2xl',
            title: 'font-black text-gray-900'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-raw-material-form').submit();
        }
    });
}
</script>
@endpush
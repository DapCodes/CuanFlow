@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Detail Supplier - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('raw-materials.suppliers') }}" class="text-gray-500 hover:text-cuan-green transition-colors">Supplier</a>
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Detail Supplier</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">
        
        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 rounded-3xl bg-cuan-green/10 flex items-center justify-center border-2 border-white shadow-xl text-cuan-green text-3xl">
                    <i class="fas fa-truck"></i>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-black text-gray-900 leading-tight">
                        {{ $supplier->name }}
                    </h1>
                    <div class="flex items-center gap-3 mt-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black font-mono bg-gray-100 text-gray-500 border border-gray-200 uppercase tracking-tighter">
                            {{ $supplier->code }}
                        </span>
                        @if($supplier->is_active)
                            <span class="inline-flex items-center px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-cuan-green/10 text-cuan-green border border-cuan-green/10 shadow-sm">
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-50 text-gray-400 border border-gray-200">
                                Nonaktif
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('raw-materials.suppliers') }}" 
                   class="inline-flex items-center gap-2 rounded-xl bg-white border border-gray-200 px-5 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all shadow-sm active:scale-95">
                    <span>Kembali</span>
                </a>
                @can('edit supplier')
                <a href="{{ route('raw-materials.suppliers.edit', $supplier) }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-6 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                    <i class="fas fa-edit text-xs"></i>
                    <span>Edit Supplier</span>
                </a>
                @endcan
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Kolom Kiri: Detail Informasi --}}
            <div class="lg:col-span-2 space-y-6">
                <x-card-container title="Informasi Kontak">
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Kontak Person</p>
                                <p class="text-sm font-black text-gray-900">{{ $supplier->contact_person ?: '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nomor Telepon / WA</p>
                                <div class="space-y-3">
                                    <p class="text-sm font-black text-gray-900">{{ $supplier->phone ?: '-' }}</p>
                                    @if($supplier->whatsapp_url)
                                        <a href="{{ $supplier->whatsapp_url }}" target="_blank"
                                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest border border-emerald-100 hover:bg-emerald-100 transition-all shadow-sm">
                                            <i class="fab fa-whatsapp text-sm"></i>
                                            WhatsApp Supplier
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Alamat Email</p>
                                <p class="text-sm font-black text-gray-900 lowercase">{{ $supplier->email ?: '-' }}</p>
                            </div>
                        </div>
                    </div>
                </x-card-container>

                <x-card-container title="Alamat & Catatan">
                    <div class="p-8 space-y-8">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Alamat Lengkap</p>
                            <div class="p-5 rounded-2xl bg-gray-50 border border-gray-100 italic text-sm text-gray-600 leading-relaxed shadow-inner">
                                {{ $supplier->address ?: 'Alamat belum diatur.' }}
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Catatan Internal</p>
                            <div class="p-5 rounded-2xl bg-gray-50 border border-gray-100 text-sm text-gray-600 font-medium">
                                "{{ $supplier->notes ?: 'Tidak ada catatan.' }}"
                            </div>
                        </div>
                    </div>
                </x-card-container>

                <x-card-container>
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white">
                        <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest">Bahan Baku yang Disuplai</h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black bg-gray-100 text-gray-500 border border-gray-200 uppercase tracking-widest">
                            {{ $supplier->rawMaterials->count() }} Items
                        </span>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($supplier->rawMaterials as $material)
                            <div class="group p-6 flex items-center justify-between hover:bg-gray-50 transition-all">
                                <div class="flex items-center gap-5">
                                    <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center border-2 border-white shadow-sm overflow-hidden flex-shrink-0 group-hover:scale-105 transition-transform">
                                        @if($material->image)
                                            <img src="{{ Storage::url($material->image) }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="fas fa-box text-gray-300 text-xl"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-gray-900 group-hover:text-cuan-green transition-colors">{{ $material->name }}</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[9px] font-black font-mono text-gray-400 uppercase tracking-tighter bg-gray-100 px-1.5 py-0.5 rounded border border-gray-100">
                                                {{ $material->code }}
                                            </span>
                                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                                                {{ $material->unit ? $material->unit->name : '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @can('lihat detail bahan baku')
                                <a href="{{ route('raw-materials.show', $material) }}"
                                   class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-400 hover:bg-cuan-green hover:text-white hover:border-cuan-green transition-all active:scale-95 shadow-sm group-hover:shadow-md">
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </a>
                                @endcan
                            </div>
                        @empty
                            <div class="p-12 text-center">
                                <i class="fas fa-boxes text-gray-200 text-3xl mb-4 block"></i>
                                <p class="text-xs font-black text-gray-400 uppercase tracking-widest italic">Belum ada bahan baku yang terhubung.</p>
                            </div>
                        @endforelse
                    </div>
                </x-card-container>
            </div>

            {{-- Kolom Kanan: Ringkasan --}}
            <div class="lg:col-span-1 space-y-6">
                <x-card-container title="Statistik & Sistem">
                    <div class="p-6">
                        <div class="bg-gray-50 rounded-3xl p-6 border border-gray-100 text-center shadow-inner">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Total Item Disuplai</p>
                            <p class="text-4xl font-black text-gray-900">{{ $supplier->raw_materials_count }}</p>
                            @can('buat bahan baku')
                            <a href="{{ route('raw-materials.create') }}?supplier_id={{ $supplier->id }}"
                               class="mt-6 w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-white border border-gray-200 py-3 text-[10px] font-black text-gray-600 uppercase tracking-widest hover:bg-gray-50 transition-all shadow-sm">
                                <i class="fas fa-plus"></i> Tambah Item
                            </a>
                            @endcan
                        </div>

                        <div class="mt-8 space-y-4">
                            <div class="flex items-center justify-between px-2">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Didaftarkan</span>
                                <span class="text-[11px] font-bold text-gray-900">{{ $supplier->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="flex items-center justify-between px-2">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pembaruan</span>
                                <span class="text-[11px] font-bold text-gray-900">{{ $supplier->updated_at->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                </x-card-container>

                @can('hapus supplier')
                <div class="p-6 rounded-3xl bg-red-50 border border-red-100 text-center">
                    <p class="text-[10px] font-black text-red-600 uppercase tracking-widest mb-3">Zona Berbahaya</p>
                    <p class="text-[10px] text-red-400 font-bold uppercase tracking-widest mb-4">Hapus data supplier? Pastikan tidak ada bahan aktif.</p>
                    <form action="{{ route('raw-materials.suppliers.destroy', $supplier) }}" method="POST" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDelete()"
                                class="w-full py-3 rounded-xl border border-red-200 text-[10px] font-black text-red-600 uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all active:scale-95">
                            <i class="fas fa-trash-alt mr-2"></i> Hapus Supplier
                        </button>
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
function confirmDelete() {
    Swal.fire({
        title: 'Hapus Supplier?',
        text: "Apakah Anda yakin ingin menghapus supplier ini? Tindakan ini tidak dapat dibatalkan.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'rounded-[2rem] border-none shadow-2xl',
            title: 'font-black text-gray-900',
            confirmButton: 'rounded-xl px-6 py-3 font-bold text-sm',
            cancelButton: 'rounded-xl px-6 py-3 font-bold text-sm'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm').submit();
        }
    });
}
</script>
@endpush
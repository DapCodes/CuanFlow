@extends('layouts.app')

@section('title', 'Detail Supplier - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('raw-materials.suppliers') }}" class="text-gray-500 hover:text-red-600 transition-colors">Supplier</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Detail</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">
        
        {{-- Header Section --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-lg bg-red-50 flex items-center justify-center border border-red-100 text-red-500 shadow-sm text-2xl">
                    <i class="fas fa-truck"></i>
                </div>
                <div>
                    <h1 class="text-xl md:text-2xl font-semibold text-gray-900">{{ $supplier->name }}</h1>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono font-medium bg-gray-100 text-gray-600 border border-gray-200">
                            {{ $supplier->code }}
                        </span>
                        @if($supplier->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-50 text-emerald-600 border border-emerald-100">
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-gray-100 text-gray-400 border border-gray-200">
                                Nonaktif
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('raw-materials.suppliers') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-sm transition-all">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
                @can('edit supplier')
                <a href="{{ route('raw-materials.suppliers.edit', $supplier) }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-sm transition-all">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
                @endcan
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Left Column: Information --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Contact Info Card --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                           <i class="fas fa-address-book text-gray-400"></i> Informasi Kontak
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase block mb-1.5">Kontak Person</label>
                                <p class="text-sm font-semibold text-gray-900">{{ $supplier->contact_person ?: '-' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase block mb-1.5">Nomor Telepon / WA</label>
                                <div class="flex flex-col gap-2">
                                    <p class="text-sm font-semibold text-gray-900">{{ $supplier->phone ?: '-' }}</p>
                                    @if($supplier->whatsapp_url)
                                        <a href="{{ $supplier->whatsapp_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 hover:bg-emerald-100 transition-colors w-fit">
                                            <i class="fab fa-whatsapp text-sm"></i>
                                            WhatsApp Supplier
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-xs font-medium text-gray-500 uppercase block mb-1.5">Alamat Email</label>
                                <p class="text-sm font-semibold text-gray-900">{{ $supplier->email ?: '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Address & Notes Card --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                           <i class="fas fa-map-marker-alt text-gray-400"></i> Alamat & Catatan
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase block mb-1.5">Alamat Lengkap</label>
                            <p class="text-sm text-gray-700 leading-relaxed">
                                {{ $supplier->address ?: 'Alamat belum diatur.' }}
                            </p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase block mb-1.5">Catatan Internal</label>
                            <p class="text-sm text-gray-600 italic">
                                "{{ $supplier->notes ?: 'Tidak ada catatan.' }}"
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Raw Materials from this Supplier --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                           <i class="fas fa-boxes text-gray-400"></i> Bahan Baku yang Disuplai
                        </h3>
                        <span class="text-xs font-bold bg-white px-2 py-0.5 border border-gray-200 rounded text-gray-600">
                            {{ $supplier->rawMaterials->count() }} Items
                        </span>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($supplier->rawMaterials as $material)
                        <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors group">
                            <div class="flex items-center gap-3">
                                @if($material->image)
                                    <img src="{{ Storage::url($material->image) }}" class="w-10 h-10 rounded-lg object-cover border border-gray-100 shadow-sm">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-200 text-gray-400">
                                        <i class="fas fa-cube text-xs"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 group-hover:text-red-600 transition-colors">{{ $material->name }}</p>
                                    <p class="text-[11px] text-gray-400 font-mono">{{ $material->code }}</p>
                                </div>
                            </div>
                            @can('lihat detail bahan baku')
                            <a href="{{ route('raw-materials.show', $material) }}" class="p-2 text-gray-400 hover:text-red-600 hover:bg-white rounded-lg transition-all border border-transparent hover:border-red-100 shadow-none hover:shadow-sm">
                                <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                            @endcan
                        </div>
                        @empty
                        <div class="p-8 text-center text-gray-400 italic text-sm">
                            Belum ada bahan baku yang terhubung dengan supplier ini.
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>

             {{-- Right Column: Side Info --}}
             <div class="lg:col-span-1 space-y-6">
                 
                 {{-- Stats Summary --}}
                 <div class="grid grid-cols-1 gap-4">
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Item Disuplai</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ $supplier->raw_materials_count }}</p>
                        <hr class="my-4 border-gray-100">
                        @can('buat bahan baku')
                        <a href="{{ route('raw-materials.create') }}?supplier_id={{ $supplier->id }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-50 text-red-700 rounded-lg text-xs font-bold hover:bg-red-100 transition-colors border border-red-100">
                            <i class="fas fa-plus-circle"></i> Item Baru untuk Supplier ini
                        </a>
                        @endcan
                    </div>
                 </div>

                 {{-- System Info --}}
                 <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="font-semibold text-gray-900">Informasi Sistem</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-500">Didaftarkan pada</span>
                            <span class="font-semibold text-gray-900">{{ $supplier->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-500">Terakhir Update</span>
                            <span class="font-semibold text-gray-900">{{ $supplier->updated_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                 </div>

                 {{-- Danger Zone --}}
                 @can('hapus supplier')
                 <div class="bg-red-50 border border-red-100 rounded-xl shadow-sm p-6 text-center">
                    <h4 class="text-sm font-semibold text-red-900 mb-2">Hapus Supplier</h4>
                    <p class="text-xs text-red-600 mb-4">Pastikan tidak ada bahan baku aktif yang menggunakan supplier ini sebelum menghapus.</p>
                    <form action="{{ route('raw-materials.suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-white border border-red-200 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors">
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
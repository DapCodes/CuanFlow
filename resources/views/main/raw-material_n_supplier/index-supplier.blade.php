@extends('layouts.app')

@section('title', 'Supplier - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Supplier</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">
        
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

        {{-- Header Section --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-500 border border-red-100">
                        <i class="fas fa-truck text-sm"></i>
                    </span>
                    <span>Kelola Supplier</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Manajemen data supplier untuk pengadaan bahan baku Anda.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('raw-materials.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-sm transition-all">
                    <i class="fas fa-boxes mr-2"></i>
                    Kelola Stok
                </a>
                <a href="{{ route('raw-materials.suppliers.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-sm transition-all">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Tambah Supplier
                </a>
            </div>
        </section>

        {{-- Stats Overview --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Supplier</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $suppliers->total() }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100">
                        <i class="fas fa-id-badge text-gray-400 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Supplier Aktif</p>
                        <p class="mt-1 text-2xl font-bold text-emerald-600">{{ $suppliers->where('is_active', true)->count() }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100">
                        <i class="fas fa-check-double text-emerald-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Bahan Baku Terkait</p>
                        <p class="mt-1 text-2xl font-bold text-red-600">{{ $suppliers->sum('raw_materials_count') }} Items</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center border border-red-100">
                        <i class="fas fa-cubes text-red-500 text-lg"></i>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            {{-- Filter & Search --}}
            <div class="p-5 border-b border-gray-200">
                <form method="GET" action="{{ route('raw-materials.suppliers') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    {{-- Search --}}
                    <div class="md:col-span-8 relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Cari supplier, kontak person, atau email..." 
                               class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all shadow-sm">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    </div>
                    
                    {{-- Filters --}}
                    <div class="md:col-span-2">
                         <select name="status" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 shadow-sm text-gray-600">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                    <div class="md:col-span-2 flex gap-2">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 shadow-sm transition-all">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                        <a href="{{ route('raw-materials.suppliers') }}" class="inline-flex items-center justify-center px-3 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all" title="Reset Filter">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-200 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3 w-16">Kode</th>
                            <th class="px-6 py-3">Supplier</th>
                            <th class="px-6 py-3">Kontak Person</th>
                            <th class="px-6 py-3">Telepon / WhatsApp</th>
                            <th class="px-6 py-3">Total Bahan</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($suppliers as $supplier)
                        <tr class="hover:bg-gray-50 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap align-top">
                                <span class="text-xs font-mono font-semibold text-gray-600 bg-gray-100 px-2 py-1 rounded border border-gray-200">
                                    {{ $supplier->code }}
                                </span>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <div class="font-medium text-gray-900 group-hover:text-red-600 transition-colors">{{ $supplier->name }}</div>
                                @if($supplier->email)
                                    <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-1.5">
                                        <i class="fas fa-envelope text-[10px]"></i> {{ $supplier->email }}
                                    </div>
                                @endif
                                @if($supplier->address)
                                    <div class="text-[11px] text-gray-400 mt-1 max-w-xs truncate" title="{{ $supplier->address }}">
                                        <i class="fas fa-map-marker-alt mr-1"></i> {{ $supplier->address }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 align-top">
                                <div class="text-gray-900 font-medium">{{ $supplier->contact_person ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 align-top">
                                @if($supplier->phone)
                                    <div class="flex flex-col gap-1.5">
                                        <div class="text-gray-900">{{ $supplier->phone }}</div>
                                        @if($supplier->whatsapp_url)
                                            <a href="{{ $supplier->whatsapp_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-2 py-1 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100 hover:bg-emerald-100 transition-colors w-fit">
                                                <i class="fab fa-whatsapp text-sm"></i>
                                                WhatsApp
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 align-top">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                    {{ $supplier->raw_materials_count }} Item
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap align-top">
                                @if($supplier->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Aktif
                                </span>
                                @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-50 text-gray-600 border border-gray-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1.5"></span> Nonaktif
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center align-top">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('raw-materials.suppliers.show', $supplier) }}" 
                                        class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                        title="Detail">
                                         <i class="fas fa-eye"></i>
                                     </a>
                                    <a href="{{ route('raw-materials.suppliers.edit', $supplier) }}" 
                                       class="p-1.5 text-gray-500 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-colors"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('raw-materials.suppliers.destroy', $supplier) }}" 
                                          method="POST" 
                                          class="inline-block" 
                                          onsubmit="return confirm('Yakin ingin menghapus supplier {{ $supplier->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" 
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 italic">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-truck text-3xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium">Belum ada data supplier.</p>
                                    <a href="{{ route('raw-materials.suppliers.create') }}" class="mt-4 inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 shadow-sm transition-all focus:ring-2 focus:ring-red-500">
                                        <i class="fas fa-plus mr-2"></i> Tambah Supplier Baru
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($suppliers->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $suppliers->links() }}
            </div>
            @endif
        </section>

    </div>
</main>
@endsection
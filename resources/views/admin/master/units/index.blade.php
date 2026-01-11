@extends('admin.layouts.app')

@section('title', 'Kelola Units')
@section('page-title', 'Data Master - Units')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Units</span>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-ruler text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Kelola Units</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium">Pengaturan satuan produk dan bahan baku</p>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.units.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-emerald-600 transition-all duration-200 shadow-sm hover:shadow-emerald-200/50">
                <i class="fas fa-plus text-xs"></i>
                <span>Tambah Unit Baru</span>
            </a>
        </div>
    </div>
    
    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Unit</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Singkatan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Konversi</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Digunakan</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($units as $unit)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center">
                                    <i class="fas fa-ruler text-teal-600"></i>
                                </div>
                                <p class="font-semibold text-gray-900">{{ $unit->name }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 text-sm font-medium bg-gray-100 text-gray-700 rounded">
                                {{ $unit->abbreviation }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            @if($unit->base_unit_id)
                            1 {{ $unit->abbreviation }} = {{ $unit->conversion_factor }} {{ $unit->baseUnit->abbreviation ?? '' }}
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm text-gray-600">
                                {{ $unit->raw_materials_count + $unit->products_count }} item
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($unit->is_active)
                            <span class="px-2.5 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Aktif</span>
                            @else
                            <span class="px-2.5 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.units.edit', $unit) }}" 
                                   class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($unit->raw_materials_count + $unit->products_count == 0)
                                <form action="{{ route('admin.units.destroy', $unit) }}" method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus unit ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @else
                                <span class="p-2 text-gray-300 cursor-not-allowed" title="Unit sedang digunakan">
                                    <i class="fas fa-lock"></i>
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-ruler text-4xl text-gray-300 mb-3"></i>
                            <p>Belum ada unit</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($units->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $units->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

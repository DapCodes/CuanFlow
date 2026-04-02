@extends('admin.layouts.app')

@section('title', 'Manajemen Feature Flags')
@section('page-title', 'Feature Flags')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Feature Flags</span>
</li>
@endsection

@section('content')
<div class="px-4 lg:px-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-toggle-on text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight uppercase">Feature Flags</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium italic">Aktif dan nonaktifkan fitur untuk seluruh pengguna</p>
            </div>
        </div>
    </div>

    {{-- KONTEN UTAMA: TABEL FITUR --}}
    <x-card-container class="!p-0 overflow-hidden border border-gray-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left">Fitur</th>
                        <th class="px-6 py-4 text-left">Code Name</th>
                        <th class="px-6 py-4 text-left">Kategori</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($features as $feature)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center text-teal-600 border border-teal-100 shadow-sm">
                                    <i class="ph-light ph-{{ $feature->icon ?? 'star' }} text-lg"></i>
                                </div>
                                <div>
                                    <p class="font-black text-gray-900 leading-tight tracking-tight">{{ $feature->display_name }}</p>
                                    <p class="text-[10px] font-medium text-gray-400 mt-0.5 italic">{{ $feature->description ?? 'Tidak ada deskripsi' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <code class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs font-bold">{{ $feature->name }}</code>
                        </td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-widest bg-emerald-50 text-emerald-600 border border-emerald-100">
                                {{ $feature->category ?? 'Lainnya' }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <form action="{{ route('admin.features.toggle', $feature) }}" method="POST">
                                @csrf
                                <button type="submit" class="focus:outline-none transition-transform active:scale-95 group">
                                    @if($feature->is_active)
                                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-green-50 text-green-600 border border-green-100 group-hover:bg-red-600 group-hover:text-white transition-all" title="Klik untuk menonaktifkan">Aktif</span>
                                    @else
                                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-red-50 text-red-600 border border-red-100 group-hover:bg-green-600 group-hover:text-white transition-all" title="Klik untuk mengaktifkan">Nonaktif</span>
                                    @endif
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gray-50 border border-dashed border-gray-200 rounded-full flex items-center justify-center mb-6">
                                    <i class="fas fa-list text-gray-200 text-3xl"></i>
                                </div>
                                <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">Tidak Ada Fitur</h3>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card-container>
</div>
@endsection

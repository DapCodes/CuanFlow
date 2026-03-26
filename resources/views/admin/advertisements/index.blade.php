@extends('admin.layouts.app')

@section('title', 'Manajemen Iklan')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Iklan & Banner</span>
</li>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-bullhorn text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Manajemen Iklan</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium">Kelola iklan dan banner aplikasi</p>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.advertisements.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 border border-transparent text-white text-sm font-semibold rounded-xl hover:bg-gray-800 transition-all duration-200 shadow-sm">
                <i class="fas fa-plus text-xs"></i>
                <span>Tambah Iklan</span>
            </a>
        </div>
    </div>

    {{-- KONTEN UTAMA: TOOLBAR + TABEL --}}
    <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-[11px] text-gray-500 uppercase font-bold border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4">ID / Tanggal</th>
                        <th class="px-6 py-4">Iklan</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Periode</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($advertisements as $ad)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-bold text-gray-900 text-sm">#{{ $ad->id }}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5 uppercase tracking-tighter">{{ $ad->created_at->isoFormat('D MMM Y, HH:mm') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <img src="{{ $ad->banner_url }}" class="h-12 w-20 rounded-md border border-gray-100 object-cover shadow-sm">
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-gray-900 truncate leading-tight">{{ $ad->title }}</p>
                                    @if($ad->url)
                                        <a href="{{ $ad->url }}" target="_blank" class="text-[10px] text-teal-600 truncate font-medium hover:underline">{{ Str::limit($ad->url, 30) }}</a>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 relative">
                            <form action="{{ route('admin.advertisements.toggle-status', $ad) }}" method="POST" class="inline-block relative z-10 cursor-pointer">
                                @csrf
                                <button type="submit" class="focus:outline-none transition-all">
                                    <div class="w-10 h-5 bg-gray-200 rounded-full peer relative transition-colors duration-300 {{ $ad->is_active ? 'bg-emerald-500' : 'bg-gray-300' }}">
                                        <div class="absolute top-[2px] left-[2px] bg-white w-4 h-4 rounded-full transition-transform duration-300 shadow-sm {{ $ad->is_active ? 'translate-x-[20px]' : 'translate-x-0' }}"></div>
                                    </div>
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-xs text-gray-600">
                                @if($ad->start_date)
                                    Mulai: <span class="font-medium text-gray-900">{{ $ad->start_date->format('d M Y') }}</span>
                                @else
                                    <span class="text-gray-400">Tanpa batas awal</span>
                                @endif
                            </p>
                            <p class="text-xs text-gray-600 mt-1">
                                @if($ad->end_date)
                                    Akhir: <span class="font-medium text-gray-900">{{ $ad->end_date->format('d M Y') }}</span>
                                @else
                                    <span class="text-gray-400">Tanpa batas akhir</span>
                                @endif
                            </p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.advertisements.edit', $ad) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-all" 
                                   title="Edit Iklan">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                
                                <form action="{{ route('admin.advertisements.destroy', $ad) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus iklan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center justify-center w-8 h-8 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-all" 
                                            title="Hapus Iklan">
                                        <i class="fas fa-trash-alt text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-dashed border-gray-200">
                                    <i class="fas fa-bullhorn text-2xl text-gray-300"></i>
                                </div>
                                <p class="text-gray-500 font-medium">Belum ada iklan ditambahkan</p>
                                <a href="{{ route('admin.advertisements.create') }}" class="text-xs text-emerald-600 font-medium mt-1 pb-4 hover:underline">Tambah Iklan Sekarang</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($advertisements->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $advertisements->links() }}
        </div>
        @endif
    </section>
</div>
@endsection

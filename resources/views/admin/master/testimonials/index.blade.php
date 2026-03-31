@extends('admin.layouts.app')

@section('title', 'Testimonial')
@section('page-title', 'Manajemen Sosmed')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Testimonial</span>
</li>
@endsection

@section('content')
<div class="px-4 lg:px-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-quote-left text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight uppercase">Testimonial</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium italic">Kelola testimoni pelanggan untuk ditampilkan di landing page</p>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.testimonials.create') }}" 
               class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 text-white text-sm font-black uppercase tracking-widest rounded-xl hover:bg-emerald-600 transition-all duration-300 shadow-md hover:shadow-emerald-200/50 active:scale-95">
                <i class="fas fa-plus text-[10px]"></i>
                <span>Tambah Testimoni</span>
            </a>
        </div>
    </div>

    {{-- RINGKASAN STATISTIK --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Testimonials --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Total Testimoni</p>
                    <p class="mt-1 text-2xl font-black text-gray-900">{{ number_format($stats['total_testimonials']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100">
                    <i class="fas fa-users text-gray-400 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Published --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Published</p>
                    <p class="mt-1 text-2xl font-black text-emerald-600">{{ number_format($stats['published']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100 shadow-sm shadow-emerald-100/50">
                    <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Draft --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Draft</p>
                    <p class="mt-1 text-2xl font-black text-amber-600">{{ number_format($stats['draft']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center border border-amber-100 shadow-sm shadow-amber-100/50">
                    <i class="fas fa-file-alt text-amber-500 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Avg Rating --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Rata-rata Rating</p>
                    <div class="flex items-center gap-2 mt-1">
                        <p class="text-2xl font-black text-blue-600">{{ $stats['avg_rating'] }}</p>
                        <div class="flex text-amber-400 text-[10px]">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center border border-blue-100 shadow-sm shadow-blue-100/50">
                    <i class="fas fa-star text-blue-500 text-lg"></i>
                </div>
            </div>
        </div>
    </section>

    {{-- KONTEN UTAMA: TOOLBAR + TABEL --}}
    <x-card-container class="!p-0 overflow-hidden border border-gray-200 shadow-sm bg-white rounded-xl">
        {{-- Toolbar: Search --}}
        <div class="border-b border-gray-200 px-4 md:px-6 py-5 bg-gray-50/50">
            <form action="{{ route('admin.testimonials.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:items-center md:justify-between gap-4">
                <div class="w-full md:max-w-xs">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2 block italic">Cari Nama / Konten</label>
                    <div class="relative group">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari testimoni..."
                               class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all duration-300">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-emerald-500 transition-colors text-xs"></i>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition-all shadow-md shadow-gray-200 active:scale-95 group">
                        <i class="fas fa-search group-hover:rotate-12 transition-transform"></i>
                    </button>
                    @if(request()->anyFilled(['search']))
                        <a href="{{ route('admin.testimonials.index') }}" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white border border-gray-200 text-gray-400 hover:bg-gray-50 hover:text-red-500 transition-all shadow-sm active:scale-95" title="Reset">
                            <i class="fas fa-redo-alt text-sm"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left">Nama & Peran</th>
                        <th class="px-6 py-4 text-left">Isi Testimoni</th>
                        <th class="px-6 py-4 text-center">Rating</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center font-black">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($testimonials as $testimonial)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                @if($testimonial->image)
                                    <div class="w-12 h-12 rounded-xl border border-gray-100 shadow-sm overflow-hidden p-0.5 bg-white group hover:border-emerald-200 transition-colors">
                                        <img src="{{ Storage::url($testimonial->image) }}" alt="{{ $testimonial->name }}" class="w-full h-full object-cover rounded-lg group-hover:scale-110 transition-transform duration-300">
                                    </div>
                                @else
                                    <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 font-black text-xs uppercase border border-emerald-100">
                                        {{ substr($testimonial->name, 0, 2) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-black text-gray-900 leading-tight uppercase tracking-tight">{{ $testimonial->name }}</p>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-emerald-500 mt-1 italic">{{ $testimonial->role ?? 'Pelanggan' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-[11px] font-medium text-gray-600 italic line-clamp-2 leading-relaxed">"{{ $testimonial->content }}"</p>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <div class="flex items-center justify-center text-yellow-400 gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="{{ $i <= $testimonial->rating ? 'fas' : 'far' }} fa-star text-[10px]"></i>
                                @endfor
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <form action="{{ route('admin.testimonials.toggle-status', $testimonial) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="focus:outline-none active:scale-95 transition-transform">
                                    @if($testimonial->is_published)
                                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-green-50 text-green-600 border border-green-100 hover:bg-green-100 transition-colors">Published</span>
                                    @else
                                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-gray-50 text-gray-500 border border-gray-200 hover:bg-gray-100 transition-colors">Draft</span>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.testimonials.edit', $testimonial) }}" 
                                   class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-600 hover:text-white shadow-sm transition-all active:scale-95 border border-blue-100" 
                                   title="Edit Testimoni">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('admin.testimonials.destroy', $testimonial) }}" method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus testimonial ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-10 h-10 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-600 hover:text-white shadow-sm transition-all active:scale-95 border border-red-100" 
                                            title="Hapus Testimoni">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gray-50 border border-dashed border-gray-200 rounded-full flex items-center justify-center mb-6">
                                    <i class="fas fa-quote-left text-gray-200 text-3xl"></i>
                                </div>
                                <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">Testimoni Tidak Ditemukan</h3>
                                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-widest mt-2 max-w-xs mx-auto italic">
                                    {{ request('search') ? 'Coba sesuaikan kata kunci pencarian Anda.' : 'Belum ada data testimoni tersedia.' }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($testimonials->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $testimonials->withQueryString()->links() }}
        </div>
        @endif
    </x-card-container>
</div>
@endsection


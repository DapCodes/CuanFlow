@extends('layouts.app')

@section('title', 'Kelola Testimoni - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Testimoni</span>
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

        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-500 border border-blue-100">
                        <i class="fas fa-comment-alt text-sm"></i>
                    </span>
                    <span>Testimoni Pelanggan</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola ulasan dan testimoni yang masuk dari halaman landing page Anda.
                </p>
            </div>
        </section>

        <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            @if($testimonials->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Pelanggan</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Rating & Ulasan</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($testimonials as $testimonial)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($testimonial->image)
                                                <img src="{{ Storage::url($testimonial->image) }}" class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 font-bold">
                                                    {{ strtoupper(substr($testimonial->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $testimonial->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $testimonial->role ?? 'Pelanggan' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        <div class="flex items-center text-yellow-400 text-xs mb-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $testimonial->rating ? '' : 'text-gray-300' }}"></i>
                                            @endfor
                                        </div>
                                        <p class="text-gray-600 line-clamp-2 max-w-sm">{{ $testimonial->content }}</p>
                                    </td>

                                    <td class="px-6 py-4 text-gray-500 text-xs">
                                        {{ $testimonial->created_at->format('d M Y, H:i') }}
                                    </td>

                                    <td class="px-6 py-4">
                                        @if($testimonial->is_published)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-100">
                                                Publik
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200">
                                                Draft / Hidden
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            @can('aktifkan nonaktifkan testimoni')
                                            <form action="{{ route('testimonials.toggle-status', $testimonial->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" 
                                                        class="w-8 h-8 flex items-center justify-center rounded-lg border transition-colors {{ $testimonial->is_published ? 'border-green-200 text-green-600 hover:bg-green-50' : 'border-gray-200 text-gray-400 hover:bg-gray-50' }}"
                                                        title="{{ $testimonial->is_published ? 'Sembunyikan' : 'Tampilkan Publik' }}">
                                                    <i class="fas {{ $testimonial->is_published ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                                </button>
                                            </form>
                                            @endcan

                                            @can('hapus testimoni')
                                            <form action="{{ route('testimonials.destroy', $testimonial->id) }}" method="POST" onsubmit="return confirm('Hapus testimoni ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg border border-red-200 text-red-600 hover:bg-red-50 transition-colors" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $testimonials->links() }}
                    </div>
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="far fa-comments text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Belum Ada Testimoni</h3>
                    <p class="text-gray-500 mt-1">Testimoni yang dikirim pelanggan akan muncul di sini.</p>
                </div>
            @endif
        </section>
    </div>
</main>
@endsection

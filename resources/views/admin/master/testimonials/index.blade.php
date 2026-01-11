@extends('admin.layouts.app')

@section('title', 'Manajemen Testimonial')
@section('page-title', 'Data Master - Testimonial')

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <span class="text-gray-700">Testimonial</span>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Manajemen Testimonial</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola testimoni pelanggan untuk ditampilkan di landing page</p>
        </div>
        <a href="{{ route('admin.testimonials.create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-cuan-dark text-white font-semibold rounded-lg hover:bg-cuan-green transition-colors">
            <i class="fas fa-plus text-sm"></i>
            <span>Tambah Testimonial</span>
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Nama & Peran</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Konten</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Rating</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($testimonials as $testimonial)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($testimonial->image)
                                    <img src="{{ Storage::url($testimonial->image) }}" alt="{{ $testimonial->name }}" class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 uppercase font-bold text-xs">
                                        {{ substr($testimonial->name, 0, 2) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $testimonial->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $testimonial->role ?? 'Pelanggan' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600 italic line-clamp-2">"{{ $testimonial->content }}"</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center text-yellow-400 gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="{{ $i <= $testimonial->rating ? 'fas' : 'far' }} fa-star text-xs"></i>
                                @endfor
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('admin.testimonials.toggle-status', $testimonial) }}" method="POST">
                                @csrf
                                <button type="submit" class="focus:outline-none">
                                    @if($testimonial->is_published)
                                        <span class="px-2.5 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full hover:bg-green-200 transition-colors">Published</span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors">Draft</span>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.testimonials.edit', $testimonial) }}" 
                                   class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.testimonials.destroy', $testimonial) }}" method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus testimonial ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-quote-left text-4xl text-gray-300 mb-3"></i>
                            <p>Belum ada testimonial</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($testimonials->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $testimonials->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

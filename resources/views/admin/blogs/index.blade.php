@extends('admin.layouts.app')

@section('title', 'Manajemen Blog')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Blog</span>
</li>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-newspaper text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Manajemen Blog</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium">Kelola artikel dan publikasi blog</p>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.blogs.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 border border-transparent text-white text-sm font-semibold rounded-xl hover:bg-gray-800 transition-all duration-200 shadow-sm">
                <i class="fas fa-plus text-xs"></i>
                <span>Tambah Artikel</span>
            </a>
        </div>
    </div>

    <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-[11px] text-gray-500 uppercase font-bold border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4">ID / Tanggal</th>
                        <th class="px-6 py-4">Artikel</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Views</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($blogs as $blog)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-bold text-gray-900 text-sm">#{{ $blog->id }}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5 uppercase tracking-tighter">{{ $blog->created_at->isoFormat('D MMM Y, HH:mm') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <img src="{{ $blog->thumbnail_url }}" class="h-12 w-20 rounded-md border border-gray-100 object-cover shadow-sm">
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-gray-900 truncate leading-tight">{{ $blog->title }}</p>
                                    <span class="inline-block mt-1 px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] font-bold uppercase rounded">{{ $blog->category ?? 'Uncategorized' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 relative">
                            <form action="{{ route('admin.blogs.toggle-status', $blog) }}" method="POST" class="inline-block relative z-10 cursor-pointer">
                                @csrf
                                <button type="submit" class="focus:outline-none transition-all">
                                    <div class="w-10 h-5 bg-gray-200 rounded-full peer relative transition-colors duration-300 {{ $blog->is_published ? 'bg-emerald-500' : 'bg-gray-300' }}">
                                        <div class="absolute top-[2px] left-[2px] bg-white w-4 h-4 rounded-full transition-transform duration-300 shadow-sm {{ $blog->is_published ? 'translate-x-[20px]' : 'translate-x-0' }}"></div>
                                    </div>
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-gray-900">{{ number_format($blog->views) }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.blogs.edit', $blog) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-all" 
                                   title="Edit Artikel">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                
                                <form action="{{ route('admin.blogs.destroy', $blog) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center justify-center w-8 h-8 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-all" 
                                            title="Hapus Artikel">
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
                                    <i class="fas fa-newspaper text-2xl text-gray-300"></i>
                                </div>
                                <p class="text-gray-500 font-medium">Belum ada artikel ditambahkan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($blogs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $blogs->links() }}
        </div>
        @endif
    </section>
</div>
@endsection

@extends('admin.layouts.app')

@section('title', 'Kategori Permission')
@section('page-title', 'Data Master - Kategori Permission')

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <span class="text-gray-700">Kategori Permission</span>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Kategori Permission</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola pengelompokan permission untuk role management</p>
        </div>
        <a href="{{ route('admin.permission-categories.create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-cuan-dark text-white font-semibold rounded-lg hover:bg-cuan-green transition-colors">
            <i class="fas fa-plus text-sm"></i>
            <span>Tambah Kategori</span>
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Nama Kategori</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Slug</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Jumlah Permission</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($categories as $category)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                                    <i class="fas fa-tags text-indigo-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $category->name }}</p>
                                    @if($category->description)
                                        <p class="text-xs text-gray-500 line-clamp-1">{{ $category->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 text-xs font-mono bg-gray-100 text-gray-600 rounded">
                                {{ $category->slug }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm font-medium text-gray-700">{{ $category->permissions_count }} permission</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.permission-categories.edit', $category) }}" 
                                   class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($category->permissions_count == 0)
                                    <form action="{{ route('admin.permission-categories.destroy', $category) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="p-2 text-gray-300 cursor-not-allowed" title="Kategori masih memiliki permission">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-layer-group text-4xl text-gray-300 mb-3"></i>
                            <p>Belum ada kategori permission</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($categories->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $categories->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

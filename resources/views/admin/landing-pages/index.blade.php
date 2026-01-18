@extends('admin.layouts.app')

@section('title', 'Landing Pages')

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right text-[8px] mx-2"></i>
    <span class="text-gray-600 font-medium">Landing Pages</span>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Landing Pages</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola landing page untuk aplikasi Flow</p>
        </div>
        <a href="{{ route('admin.landing-pages.create') }}" 
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all hover:shadow-md">
            <i class="fas fa-plus text-xs"></i>
            Buat Landing Page
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-globe text-emerald-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $landingPages->total() }}</p>
                    <p class="text-xs text-gray-500">Total Landing Pages</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $landingPages->where('is_active', true)->count() }}</p>
                    <p class="text-xs text-gray-500">Aktif</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-pause-circle text-gray-400"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $landingPages->where('is_active', false)->count() }}</p>
                    <p class="text-xs text-gray-500">Draft</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Landing Pages List -->
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        @if($landingPages->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Landing Page</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Sections</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Status</th>
                        <th class="text-right px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($landingPages as $page)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-lg font-bold shadow-sm" 
                                     style="background: linear-gradient(135deg, {{ $page->primary_color }}, {{ $page->secondary_color }})">
                                    {{ strtoupper(substr($page->title, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $page->title }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        <i class="fas fa-link mr-1"></i>/flow/{{ $page->slug }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-medium">
                                    {{ $page->active_sections_count }} aktif
                                </span>
                                <span class="text-gray-300">/</span>
                                <span class="text-xs text-gray-400">{{ $page->sections_count }} total</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 hidden sm:table-cell">
                            @if($page->is_active)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-50 text-green-700 text-xs font-medium">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 text-gray-500 text-xs font-medium">
                                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.landing-pages.preview', $page) }}" 
                                   target="_blank"
                                   class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                   title="Preview">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.landing-pages.sections.index', $page) }}" 
                                   class="p-2 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors"
                                   title="Kelola Sections">
                                    <i class="fas fa-layer-group"></i>
                                </a>
                                <a href="{{ route('admin.landing-pages.edit', $page) }}" 
                                   class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.landing-pages.toggle-status', $page) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                            title="{{ $page->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="fas {{ $page->is_active ? 'fa-toggle-on text-green-500' : 'fa-toggle-off' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.landing-pages.destroy', $page) }}" method="POST" class="inline" 
                                      onsubmit="return confirm('Yakin ingin menghapus landing page ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($landingPages->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $landingPages->links() }}
        </div>
        @endif
        @else
        <!-- Empty State -->
        <div class="py-16 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-globe text-3xl text-gray-300"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Landing Page</h3>
            <p class="text-sm text-gray-500 mb-6">Mulai buat landing page pertama untuk aplikasi Flow</p>
            <a href="{{ route('admin.landing-pages.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-colors">
                <i class="fas fa-plus text-xs"></i>
                Buat Landing Page
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

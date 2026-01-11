@extends('admin.layouts.app')

@section('title', 'Kelola FAQ')
@section('page-title', 'Data Master - FAQ')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">FAQ</span>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-question-circle text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Kelola FAQ</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium">Pengaturan pertanyaan umum dan panduan pengguna sistem</p>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.faqs.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-emerald-600 transition-all duration-200 shadow-sm hover:shadow-emerald-200/50">
                <i class="fas fa-plus text-xs"></i>
                <span>Tambah FAQ Baru</span>
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <form action="{{ route('admin.faqs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Cari FAQ</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pertanyaan..." 
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-cuan-green focus:border-cuan-green">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Outlet</label>
                <select name="outlet_id" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-cuan-green focus:border-cuan-green">
                    <option value="">Semua Outlet</option>
                    @foreach($outlets as $outlet)
                        <option value="{{ $outlet->id }}" {{ request('outlet_id') == $outlet->id ? 'selected' : '' }}>{{ $outlet->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Kategori</label>
                <select name="type" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-cuan-green focus:border-cuan-green">
                    <option value="">Semua Kategori</option>
                    @foreach(App\Models\Faq::getTypes() as $key => $label)
                        <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
            </div>
        </form>
    </div>
    
    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pertanyaan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Outlet</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($faqs as $faq)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="max-w-xs md:max-w-md">
                                <p class="font-semibold text-gray-900 truncate" title="{{ $faq->question }}">{{ $faq->question }}</p>
                                <p class="text-xs text-gray-500 mt-1 truncate">{{ Str::limit($faq->answer, 60) }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-medium text-gray-700">{{ $faq->outlet->name ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2.5 py-1 text-xs font-medium bg-blue-50 text-blue-700 rounded-full border border-blue-100">
                                {{ $faq->getTypeLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('admin.faqs.toggle-status', $faq) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center">
                                    @if($faq->is_active)
                                        <span class="px-2.5 py-1 text-xs font-medium bg-emerald-50 text-emerald-700 rounded-full border border-emerald-100">Aktif</span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-medium bg-gray-50 text-gray-600 rounded-full border border-gray-200">Nonaktif</span>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.faqs.show', $faq) }}" class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.faqs.edit', $faq) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus FAQ ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-question-circle text-4xl text-gray-300 mb-3"></i>
                            <p>Belum ada FAQ</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($faqs->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $faqs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

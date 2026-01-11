@extends('admin.layouts.app')

@section('title', 'Label Tugas')
@section('page-title', 'Data Master - Label Tugas')

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <span class="text-gray-700">Label Tugas</span>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Label Tugas</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola label untuk klasifikasi tugas</p>
        </div>
        <a href="{{ route('admin.task-labels.create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-cuan-dark text-white font-semibold rounded-lg hover:bg-cuan-green transition-colors">
            <i class="fas fa-plus text-sm"></i>
            <span>Tambah Label</span>
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Nama Label</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Warna</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Tugas Terkait</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($labels as $label)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-sm font-semibold rounded-full border" 
                                  style="color: {{ $label->color }}; background-color: {{ $label->color }}15; border-color: {{ $label->color }}">
                                {{ $label->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded-full" style="background-color: {{ $label->color }}"></span>
                                <span class="text-xs font-mono text-gray-600">{{ $label->color }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm text-gray-600">{{ $label->tasks_count }} tugas</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.task-labels.edit', $label) }}" 
                                   class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($label->tasks_count == 0)
                                    <form action="{{ route('admin.task-labels.destroy', $label) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus label ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="p-2 text-gray-300 cursor-not-allowed" title="Label masih digunakan">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-tags text-4xl text-gray-300 mb-3"></i>
                            <p>Belum ada label tugas</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($labels->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $labels->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@extends('admin.layouts.app')

@section('title', 'Status Tugas')
@section('page-title', 'Data Master - Status Tugas')

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <span class="text-gray-700">Status Tugas</span>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Status Tugas</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola status untuk alur kerja Kanban</p>
        </div>
        <a href="{{ route('admin.task-statuses.create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-cuan-dark text-white font-semibold rounded-lg hover:bg-cuan-green transition-colors">
            <i class="fas fa-plus text-sm"></i>
            <span>Tambah Status</span>
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Nama Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Warna</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Urutan</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Tugas Terkait</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($statuses as $status)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-semibold text-gray-900">{{ $status->name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded-full" style="background-color: {{ $status->color }}"></span>
                                <span class="text-xs font-mono text-gray-600">{{ $status->color }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm font-medium text-gray-700">{{ $status->order }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm text-gray-600">{{ $status->tasks_count ?? $status->tasks()->count() }} tugas</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.task-statuses.edit', $status) }}" 
                                   class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(($status->tasks_count ?? $status->tasks()->count()) == 0)
                                    <form action="{{ route('admin.task-statuses.destroy', $status) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus status ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="p-2 text-gray-300 cursor-not-allowed" title="Status masih digunakan">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-list-ul text-4xl text-gray-300 mb-3"></i>
                            <p>Belum ada status tugas</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('admin.layouts.app')

@section('title', 'Tambah Label Tugas')
@section('page-title', 'Tambah Label Tugas')

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <a href="{{ route('admin.task-labels.index') }}" class="text-gray-500 hover:text-gray-700">Label Tugas</a>
</li>
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <span class="text-gray-700">Tambah</span>
</li>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <form action="{{ route('admin.task-labels.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">Tambah Label Baru</h2>
            <a href="{{ route('admin.task-labels.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </a>
        </div>
        
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-5 shadow-sm">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Label <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green @error('name') border-red-300 @enderror"
                       placeholder="Contoh: Urgent">
                @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                    Warna <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-2">
                    <input type="color" name="color" id="color" value="{{ old('color', '#EF4444') }}" 
                           class="h-12 w-12 border border-gray-300 rounded-lg cursor-pointer">
                    <input type="text" id="color-text" value="{{ old('color', '#EF4444') }}"
                           class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green font-mono">
                </div>
            </div>
        </div>
        
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.task-labels.index') }}" 
               class="px-4 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-cuan-dark text-white font-semibold rounded-lg hover:bg-cuan-green shadow-md">
                <i class="fas fa-save mr-2"></i>Simpan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const colorInput = document.getElementById('color');
    const colorText = document.getElementById('color-text');
    
    colorInput.addEventListener('input', (e) => {
        colorText.value = e.target.value.toUpperCase();
    });
    
    colorText.addEventListener('input', (e) => {
        if(/^#[0-9A-F]{6}$/i.test(e.target.value)) {
            colorInput.value = e.target.value;
        }
    });
</script>
@endpush
@endsection

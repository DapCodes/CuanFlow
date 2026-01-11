@extends('admin.layouts.app')

@section('title', 'Edit Kategori')
@section('page-title', 'Edit Kategori')

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <a href="{{ route('admin.categories.index') }}" class="text-gray-500 hover:text-gray-700">Kategori</a>
</li>
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <span class="text-gray-700">Edit</span>
</li>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">Edit Kategori: {{ $category->name }}</h2>
            <a href="{{ route('admin.categories.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </a>
        </div>
        
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Kategori <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green @error('name') border-red-300 @enderror">
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe Kategori <span class="text-red-500">*</span>
                    </label>
                    <select name="type" id="type" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green @error('type') border-red-300 @enderror">
                        <option value="product" {{ old('type', $category->type) == 'product' ? 'selected' : '' }}>Produk</option>
                        <option value="raw_material" {{ old('type', $category->type) == 'raw_material' ? 'selected' : '' }}>Bahan Baku</option>
                    </select>
                    @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">Icon (Font Awesome)</label>
                    <input type="text" name="icon" id="icon" value="{{ old('icon', $category->icon) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green"
                           placeholder="fas fa-utensils">
                    <p class="mt-1 text-xs text-gray-500">Contoh: fas fa-utensils, fas fa-coffee, fas fa-box</p>
                </div>
                
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">Urutan</label>
                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $category->sort_order) }}"
                           min="0"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green">
                    <p class="mt-1 text-xs text-gray-500">Semakin kecil, semakin atas posisinya</p>
                </div>
            </div>
            
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" id="description" rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green @error('description') border-red-300 @enderror"
                          placeholder="Deskripsi singkat kategori (opsional)">{{ old('description', $category->description) }}</textarea>
                @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="w-5 h-5 text-cuan-green border-gray-300 rounded"
                           {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                    <span class="text-sm text-gray-700">Aktifkan kategori</span>
                </label>
            </div>
            
            <!-- Icon Preview -->
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-xs font-medium text-gray-500 mb-2">Preview Icon:</p>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg {{ $category->type == 'product' ? 'bg-purple-100' : 'bg-amber-100' }} flex items-center justify-center" id="icon-preview">
                        <i class="{{ $category->icon ?? 'fas fa-folder' }} {{ $category->type == 'product' ? 'text-purple-600' : 'text-amber-600' }} text-xl"></i>
                    </div>
                    <span class="text-sm text-gray-600" id="icon-name">{{ $category->icon ?? 'fas fa-folder' }}</span>
                </div>
            </div>
        </div>
        
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.categories.index') }}" 
               class="px-4 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-cuan-dark text-white font-semibold rounded-lg hover:bg-cuan-green">
                <i class="fas fa-save mr-2"></i>Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.getElementById('icon').addEventListener('input', function() {
    const iconClass = this.value || 'fas fa-folder';
    const type = document.getElementById('type').value;
    const colorClass = type === 'raw_material' ? 'text-amber-600' : 'text-purple-600';
    document.getElementById('icon-preview').innerHTML = `<i class="${iconClass} ${colorClass} text-xl"></i>`;
    document.getElementById('icon-name').textContent = iconClass;
});

document.getElementById('type').addEventListener('change', function() {
    const preview = document.getElementById('icon-preview');
    const icon = preview.querySelector('i');
    if (this.value === 'raw_material') {
        preview.className = 'w-12 h-12 rounded-lg bg-amber-100 flex items-center justify-center';
        icon.classList.remove('text-purple-600');
        icon.classList.add('text-amber-600');
    } else {
        preview.className = 'w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center';
        icon.classList.remove('text-amber-600');
        icon.classList.add('text-purple-600');
    }
});
</script>
@endpush
@endsection

@extends('admin.layouts.app')

@section('title', 'Tambah Testimonial')
@section('page-title', 'Tambah Testimonial')

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <a href="{{ route('admin.testimonials.index') }}" class="text-gray-500 hover:text-gray-700">Testimonial</a>
</li>
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <span class="text-gray-700">Tambah</span>
</li>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <form action="{{ route('admin.testimonials.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">Tambah Testimonial Baru</h2>
            <a href="{{ route('admin.testimonials.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </a>
        </div>
        
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-5 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Pengguna <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green @error('name') border-red-300 @enderror"
                           placeholder="Contoh: John Doe">
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Peran / Jabatan</label>
                    <input type="text" name="role" id="role" value="{{ old('role') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green"
                           placeholder="Contoh: Owner Bakso Mercon">
                </div>
            </div>

            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                    Konten Testimoni <span class="text-red-500">*</span>
                </label>
                <textarea name="content" id="content" rows="4" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green"
                          placeholder="Apa kata mereka tentang CuanFlow?">{{ old('content') }}</textarea>
                @error('content')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-center">
                <div>
                    <label for="rating" class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                    <select name="rating" id="rating" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green">
                        <option value="5" {{ old('rating') == 5 ? 'selected' : '' }}>⭐⭐⭐⭐⭐ (5 Bintang)</option>
                        <option value="4" {{ old('rating') == 4 ? 'selected' : '' }}>⭐⭐⭐⭐ (4 Bintang)</option>
                        <option value="3" {{ old('rating') == 3 ? 'selected' : '' }}>⭐⭐⭐ (3 Bintang)</option>
                        <option value="2" {{ old('rating') == 2 ? 'selected' : '' }}>⭐⭐ (2 Bintang)</option>
                        <option value="1" {{ old('rating') == 1 ? 'selected' : '' }}>⭐ (1 Bintang)</option>
                    </select>
                </div>

                <div>
                    <label class="flex items-center gap-3 cursor-pointer mt-6">
                        <input type="checkbox" name="is_published" value="1" class="w-5 h-5 text-cuan-green border-gray-300 rounded"
                               {{ old('is_published', true) ? 'checked' : '' }}>
                        <span class="text-sm font-medium text-gray-700">Terbitkan langsung</span>
                    </label>
                </div>
            </div>

            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Foto (Opsional)</label>
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden border-2 border-dashed border-gray-300" id="image-preview">
                        <i class="fas fa-camera text-gray-400 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <input type="file" name="image" id="image" accept="image/*"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green">
                        <p class="mt-1 text-xs text-gray-500">JPG, PNG, WebP. Maks 2MB</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.testimonials.index') }}" 
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
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('image-preview').innerHTML = `<img src="${event.target.result}" class="w-full h-full object-cover">`;
                document.getElementById('image-preview').classList.remove('border-dashed');
                document.getElementById('image-preview').classList.add('border-solid');
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
@endsection

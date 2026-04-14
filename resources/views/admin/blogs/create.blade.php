@extends('admin.layouts.app')

@section('title', 'Tambah Artikel Blog')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <a href="{{ route('admin.blogs.index') }}" class="text-gray-500 hover:text-emerald-600 font-medium text-sm transition-colors">Blog</a>
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Tambah</span>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Tambah Artikel Baru</h1>
            <p class="text-sm text-gray-500 mt-0.5">Buat postingan blog baru untuk dibagikan ke pengunjung.</p>
        </div>
        <a href="{{ route('admin.blogs.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        @csrf
        <div class="p-6 md:p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Judul Artikel <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                           placeholder="Masukkan judul artikel...">
                    @error('title') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Kategori <span class="text-red-500">*</span></label>
                    <input type="text" name="category" value="{{ old('category') }}" required
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                           placeholder="Ex: Bisnis & UMKM">
                    @error('category') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
                
                <div class="flex items-center pt-8">
                    <label class="flex items-center cursor-pointer">
                        <div class="relative">
                            <input type="checkbox" name="is_published" value="1" class="sr-only" {{ old('is_published') ? 'checked' : '' }}>
                            <div class="block w-14 h-8 bg-gray-200 rounded-full transition-colors duration-300"></div>
                            <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform duration-300 shadow-sm border border-gray-100"></div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-bold text-gray-900">Publikasikan</p>
                            <p class="text-xs text-gray-500 hidden md:block">Tampilkan langsung untuk umum</p>
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Thumbnail Banner <span class="text-red-500">*</span></label>
                <div class="border-2 border-dashed border-gray-300 rounded-2xl p-8 hover:bg-gray-50 transition-colors text-center relative group cursor-pointer" onclick="document.getElementById('thumbnail').click()">
                    <div id="image-preview" class="hidden absolute inset-0 w-full h-full p-2">
                        <img src="" class="w-full h-full object-cover rounded-xl shadow-sm">
                        <div class="absolute inset-0 bg-gray-900/40 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <p class="text-white font-medium text-sm">Klik untuk mengubah gambar</p>
                        </div>
                    </div>
                    <div id="image-placeholder" class="space-y-3">
                        <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto group-hover:scale-110 transition-transform">
                            <i class="fas fa-cloud-upload-alt text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-emerald-600">Klik untuk mengunggah</p>
                            <p class="text-xs text-gray-500 mt-1">JPEG, PNG, JPG, WEBP (Max: 3MB)</p>
                            <p class="text-xs text-emerald-500 font-medium mt-2"><i class="fas fa-compress-arrows-alt"></i> Otomatis dikompres & resize</p>
                        </div>
                    </div>
                    <input type="file" name="thumbnail" id="thumbnail" accept="image/*" class="hidden" required onchange="previewImage(event)">
                </div>
                @error('thumbnail') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-900 mb-2">Konten Artikel <span class="text-red-500">*</span></label>
                <textarea name="content" rows="10" required
                          class="w-full px-4 py-3 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all custom-scrollbar"
                          placeholder="Tuliskan isi artikel Anda di sini..."></textarea>
                @error('content') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex items-center justify-end gap-3 rounded-b-2xl">
            <a href="{{ route('admin.blogs.index') }}" class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 focus:ring-2 focus:ring-gray-200 transition-all">Batal</a>
            <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-emerald-600 border border-transparent rounded-xl hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500/50 transition-all shadow-sm">
                <i class="fas fa-save mr-2"></i> Simpan Artikel
            </button>
        </div>
    </form>
</div>

@push('styles')
<style>
    input:checked ~ .dot { transform: translateX(100%); border-color: white; }
    input:checked ~ .block { background-color: #10b981; }
</style>
@endpush

@push('scripts')
<script>
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('image-preview');
        const placeholder = document.getElementById('image-placeholder');
        const img = preview.querySelector('img');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection

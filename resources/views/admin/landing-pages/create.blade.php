@extends('admin.layouts.app')

@section('title', 'Buat Landing Page')

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right text-[8px] mx-2"></i>
    <a href="{{ route('admin.landing-pages.index') }}" class="hover:text-emerald-600 transition-colors">Landing Pages</a>
</li>
<li class="flex items-center">
    <i class="fas fa-chevron-right text-[8px] mx-2"></i>
    <span class="text-gray-600 font-medium">Buat Baru</span>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('admin.landing-pages.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-emerald-600 transition-colors mb-4">
            <i class="fas fa-arrow-left"></i>
            Kembali
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Buat Landing Page Baru</h1>
        <p class="text-sm text-gray-500 mt-1">Isi informasi dasar untuk landing page Flow</p>
    </div>

    <form action="{{ route('admin.landing-pages.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Basic Info -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-emerald-500"></i>
                Informasi Dasar
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Judul Landing Page *</label>
                    <input type="text" name="title" id="title" required
                           value="{{ old('title', 'Flow – All in One Business App') }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                           placeholder="Contoh: Flow – All in One Business App">
                    @error('title')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="tagline" class="block text-sm font-medium text-gray-700 mb-2">Tagline</label>
                    <input type="text" name="tagline" id="tagline"
                           value="{{ old('tagline', 'One ecosystem to run your business smarter') }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                           placeholder="Contoh: One ecosystem to run your business smarter">
                </div>

                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug URL</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-4 py-3 bg-gray-50 border border-r-0 border-gray-200 rounded-l-xl text-sm text-gray-500">/flow/</span>
                        <input type="text" name="slug" id="slug"
                               value="{{ old('slug') }}"
                               class="flex-1 px-4 py-3 border border-gray-200 rounded-r-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                               placeholder="flow (auto-generate jika kosong)">
                    </div>
                    <p class="mt-1 text-xs text-gray-400">Biarkan kosong untuk auto-generate dari judul</p>
                </div>

                <div>
                    <label for="font_family" class="block text-sm font-medium text-gray-700 mb-2">Font Family</label>
                    <select name="font_family" id="font_family"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                        @foreach(['Inter', 'Poppins', 'Plus Jakarta Sans', 'Roboto', 'Open Sans', 'Montserrat'] as $font)
                            <option value="{{ $font }}" {{ old('font_family', 'Inter') == $font ? 'selected' : '' }}>{{ $font }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Colors -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-palette text-emerald-500"></i>
                Skema Warna
            </h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label for="primary_color" class="block text-sm font-medium text-gray-700 mb-2">Warna Utama</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="primary_color" id="primary_color"
                               value="{{ old('primary_color', '#658C58') }}"
                               class="w-12 h-12 rounded-xl border-2 border-gray-200 cursor-pointer">
                        <input type="text" id="primary_color_text" 
                               value="{{ old('primary_color', '#658C58') }}"
                               class="flex-1 px-4 py-3 border border-gray-200 rounded-xl text-sm font-mono"
                               onchange="document.getElementById('primary_color').value = this.value">
                    </div>
                    <p class="mt-1 text-xs text-gray-400">cuan-green</p>
                </div>

                <div>
                    <label for="secondary_color" class="block text-sm font-medium text-gray-700 mb-2">Warna Sekunder</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="secondary_color" id="secondary_color"
                               value="{{ old('secondary_color', '#31694E') }}"
                               class="w-12 h-12 rounded-xl border-2 border-gray-200 cursor-pointer">
                        <input type="text" id="secondary_color_text" 
                               value="{{ old('secondary_color', '#31694E') }}"
                               class="flex-1 px-4 py-3 border border-gray-200 rounded-xl text-sm font-mono"
                               onchange="document.getElementById('secondary_color').value = this.value">
                    </div>
                    <p class="mt-1 text-xs text-gray-400">cuan-dark</p>
                </div>

                <div>
                    <label for="accent_color" class="block text-sm font-medium text-gray-700 mb-2">Warna Aksen</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="accent_color" id="accent_color"
                               value="{{ old('accent_color', '#F0E491') }}"
                               class="w-12 h-12 rounded-xl border-2 border-gray-200 cursor-pointer">
                        <input type="text" id="accent_color_text" 
                               value="{{ old('accent_color', '#F0E491') }}"
                               class="flex-1 px-4 py-3 border border-gray-200 rounded-xl text-sm font-mono"
                               onchange="document.getElementById('accent_color').value = this.value">
                    </div>
                    <p class="mt-1 text-xs text-gray-400">cuan-yellow</p>
                </div>
            </div>

            <!-- Color Preview -->
            <div class="mt-6 p-4 rounded-xl border border-gray-100 bg-gray-50/50">
                <p class="text-xs text-gray-500 mb-3">Preview Warna:</p>
                <div class="flex gap-3" id="colorPreview">
                    <div class="w-20 h-10 rounded-lg shadow-sm" style="background: #658C58"></div>
                    <div class="w-20 h-10 rounded-lg shadow-sm" style="background: #31694E"></div>
                    <div class="w-20 h-10 rounded-lg shadow-sm" style="background: #F0E491"></div>
                    <div class="w-20 h-10 rounded-lg shadow-sm" style="background: #BBC863"></div>
                </div>
            </div>
        </div>

        <!-- SEO -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-search text-emerald-500"></i>
                SEO & Meta
            </h2>
            
            <div class="space-y-4">
                <div>
                    <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                    <input type="text" name="meta_title" id="meta_title"
                           value="{{ old('meta_title') }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                           placeholder="Judul untuk SEO (opsional)">
                </div>

                <div>
                    <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                    <textarea name="meta_description" id="meta_description" rows="3"
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all resize-none"
                              placeholder="Deskripsi singkat untuk SEO (opsional)">{{ old('meta_description') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Branding -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-image text-emerald-500"></i>
                Branding
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-emerald-300 transition-colors bg-gray-50/30 relative">
                        <input type="file" name="logo" id="logo" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewLogo(this)">
                        <div id="logoPreview" class="hidden mb-3">
                            <img src="" alt="Logo" class="h-16 mx-auto">
                        </div>
                        <div id="logoPlaceholder">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-300 mb-2"></i>
                            <p class="text-sm text-gray-500">Klik atau drop logo di sini</p>
                            <p class="text-xs text-gray-400">PNG, JPG, SVG (max 2MB)</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="favicon" class="block text-sm font-medium text-gray-700 mb-2">Favicon</label>
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-emerald-300 transition-colors bg-gray-50/30 relative">
                        <input type="file" name="favicon" id="favicon" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewFavicon(this)">
                        <div id="faviconPreview" class="hidden mb-3">
                            <img src="" alt="Favicon" class="h-12 mx-auto">
                        </div>
                        <div id="faviconPlaceholder">
                            <i class="fas fa-star text-3xl text-gray-300 mb-2"></i>
                            <p class="text-sm text-gray-500">Klik atau drop favicon di sini</p>
                            <p class="text-xs text-gray-400">ICO, PNG (max 1MB)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('admin.landing-pages.index') }}" 
               class="px-6 py-3 border border-gray-200 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit" 
                    class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold shadow-sm transition-all hover:shadow-md">
                <i class="fas fa-save mr-2"></i>
                Buat Landing Page
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Sync color pickers with text inputs
    document.querySelectorAll('input[type="color"]').forEach(picker => {
        const textInput = document.getElementById(picker.id + '_text');
        picker.addEventListener('input', () => {
            textInput.value = picker.value;
            updateColorPreview();
        });
    });

    function updateColorPreview() {
        const primary = document.getElementById('primary_color').value;
        const secondary = document.getElementById('secondary_color').value;
        const accent = document.getElementById('accent_color').value;
        
        const preview = document.getElementById('colorPreview');
        preview.children[0].style.background = primary;
        preview.children[1].style.background = secondary;
        preview.children[2].style.background = accent;
    }

    function previewLogo(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('#logoPreview img').src = e.target.result;
                document.getElementById('logoPreview').classList.remove('hidden');
                document.getElementById('logoPlaceholder').classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewFavicon(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('#faviconPreview img').src = e.target.result;
                document.getElementById('faviconPreview').classList.remove('hidden');
                document.getElementById('faviconPlaceholder').classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection

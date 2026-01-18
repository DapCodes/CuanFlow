@extends('admin.layouts.app')

@section('title', 'Edit Landing Page')

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right text-[8px] mx-2"></i>
    <a href="{{ route('admin.landing-pages.index') }}" class="hover:text-emerald-600 transition-colors">Landing Pages</a>
</li>
<li class="flex items-center">
    <i class="fas fa-chevron-right text-[8px] mx-2"></i>
    <span class="text-gray-600 font-medium">Edit</span>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <a href="{{ route('admin.landing-pages.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-emerald-600 transition-colors mb-4">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Edit Landing Page</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $landingPage->title }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.landing-pages.sections.index', $landingPage) }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-200 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors">
                <i class="fas fa-layer-group text-purple-500"></i>
                Kelola Sections
            </a>
            <a href="{{ route('admin.landing-pages.preview', $landingPage) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-200 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors">
                <i class="fas fa-eye text-blue-500"></i>
                Preview
            </a>
        </div>
    </div>

    <form action="{{ route('admin.landing-pages.update', $landingPage) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Status Banner -->
        <div class="p-4 rounded-xl {{ $landingPage->is_active ? 'bg-green-50 border border-green-100' : 'bg-amber-50 border border-amber-100' }}">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i class="fas {{ $landingPage->is_active ? 'fa-check-circle text-green-500' : 'fa-exclamation-circle text-amber-500' }}"></i>
                    <div>
                        <p class="text-sm font-medium {{ $landingPage->is_active ? 'text-green-800' : 'text-amber-800' }}">
                            {{ $landingPage->is_active ? 'Landing page aktif' : 'Landing page dalam mode draft' }}
                        </p>
                        <p class="text-xs {{ $landingPage->is_active ? 'text-green-600' : 'text-amber-600' }}">
                            URL: <a href="{{ route('flow.show', $landingPage->slug) }}" target="_blank" class="underline">/flow/{{ $landingPage->slug }}</a>
                        </p>
                    </div>
                </div>
                <form action="{{ route('admin.landing-pages.toggle-status', $landingPage) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $landingPage->is_active ? 'bg-white text-green-700 hover:bg-green-100' : 'bg-amber-100 text-amber-700 hover:bg-amber-200' }}">
                        {{ $landingPage->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
            </div>
        </div>

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
                           value="{{ old('title', $landingPage->title) }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                    @error('title')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="tagline" class="block text-sm font-medium text-gray-700 mb-2">Tagline</label>
                    <input type="text" name="tagline" id="tagline"
                           value="{{ old('tagline', $landingPage->tagline) }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                </div>

                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug URL</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-4 py-3 bg-gray-50 border border-r-0 border-gray-200 rounded-l-xl text-sm text-gray-500">/flow/</span>
                        <input type="text" name="slug" id="slug"
                               value="{{ old('slug', $landingPage->slug) }}"
                               class="flex-1 px-4 py-3 border border-gray-200 rounded-r-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                    </div>
                </div>

                <div>
                    <label for="font_family" class="block text-sm font-medium text-gray-700 mb-2">Font Family</label>
                    <select name="font_family" id="font_family"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                        @foreach(['Inter', 'Poppins', 'Plus Jakarta Sans', 'Roboto', 'Open Sans', 'Montserrat'] as $font)
                            <option value="{{ $font }}" {{ old('font_family', $landingPage->font_family) == $font ? 'selected' : '' }}>{{ $font }}</option>
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
                               value="{{ old('primary_color', $landingPage->primary_color) }}"
                               class="w-12 h-12 rounded-xl border-2 border-gray-200 cursor-pointer">
                        <input type="text" id="primary_color_text" 
                               value="{{ old('primary_color', $landingPage->primary_color) }}"
                               class="flex-1 px-4 py-3 border border-gray-200 rounded-xl text-sm font-mono">
                    </div>
                </div>

                <div>
                    <label for="secondary_color" class="block text-sm font-medium text-gray-700 mb-2">Warna Sekunder</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="secondary_color" id="secondary_color"
                               value="{{ old('secondary_color', $landingPage->secondary_color) }}"
                               class="w-12 h-12 rounded-xl border-2 border-gray-200 cursor-pointer">
                        <input type="text" id="secondary_color_text" 
                               value="{{ old('secondary_color', $landingPage->secondary_color) }}"
                               class="flex-1 px-4 py-3 border border-gray-200 rounded-xl text-sm font-mono">
                    </div>
                </div>

                <div>
                    <label for="accent_color" class="block text-sm font-medium text-gray-700 mb-2">Warna Aksen</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="accent_color" id="accent_color"
                               value="{{ old('accent_color', $landingPage->accent_color) }}"
                               class="w-12 h-12 rounded-xl border-2 border-gray-200 cursor-pointer">
                        <input type="text" id="accent_color_text" 
                               value="{{ old('accent_color', $landingPage->accent_color) }}"
                               class="flex-1 px-4 py-3 border border-gray-200 rounded-xl text-sm font-mono">
                    </div>
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
                           value="{{ old('meta_title', $landingPage->meta_title) }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                </div>

                <div>
                    <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                    <textarea name="meta_description" id="meta_description" rows="3"
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all resize-none">{{ old('meta_description', $landingPage->meta_description) }}</textarea>
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-emerald-300 transition-colors bg-gray-50/30 relative">
                        <input type="file" name="logo" id="logo" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewLogo(this)">
                        <div id="logoContainer" class="mb-3">
                            @if($landingPage->logo)
                                <img src="{{ Storage::url($landingPage->logo) }}" alt="Logo" class="h-16 mx-auto mb-1" id="logoImg">
                                <p class="text-[10px] text-gray-400 uppercase font-black">Ganti Logo</p>
                            @else
                                <div id="logoPlaceholder">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-300 mb-2"></i>
                                    <p class="text-sm text-gray-500">Klik atau drop logo di sini</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Favicon</label>
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-emerald-300 transition-colors bg-gray-50/30 relative">
                        <input type="file" name="favicon" id="favicon" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewFavicon(this)">
                        <div id="faviconContainer" class="mb-3">
                            @if($landingPage->favicon)
                                <img src="{{ Storage::url($landingPage->favicon) }}" alt="Favicon" class="h-12 mx-auto mb-1" id="faviconImg">
                                <p class="text-[10px] text-gray-400 uppercase font-black">Ganti Favicon</p>
                            @else
                                <div id="faviconPlaceholder">
                                    <i class="fas fa-star text-3xl text-gray-300 mb-2"></i>
                                    <p class="text-sm text-gray-500">Klik atau drop favicon di sini</p>
                                </div>
                            @endif
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
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Sync color pickers
    document.querySelectorAll('input[type="color"]').forEach(picker => {
        const textInput = document.getElementById(picker.id + '_text');
        if (textInput) {
            picker.addEventListener('input', () => textInput.value = picker.value);
            textInput.addEventListener('input', () => picker.value = textInput.value);
        }
    });

    function previewLogo(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                let container = document.getElementById('logoContainer');
                container.innerHTML = `
                    <img src="${e.target.result}" class="h-16 mx-auto mb-1">
                    <p class="text-[10px] text-emerald-600 uppercase font-black">Logo Terpilih</p>
                `;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewFavicon(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                let container = document.getElementById('faviconContainer');
                container.innerHTML = `
                    <img src="${e.target.result}" class="h-12 mx-auto mb-1">
                    <p class="text-[10px] text-emerald-600 uppercase font-black">Favicon Terpilih</p>
                `;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection

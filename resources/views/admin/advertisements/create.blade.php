@extends('admin.layouts.app')

@section('title', 'Tambah Iklan')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <a href="{{ route('admin.advertisements.index') }}" class="text-gray-500 hover:text-emerald-600 text-sm transition-colors">Iklan & Banner</a>
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Tambah Baru</span>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-plus text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Tambah Iklan</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium">Buat iklan atau banner promo baru</p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.advertisements.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        @csrf
        
        <div class="p-6 md:p-8 space-y-6">
            <!-- Informasi Dasar -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-2">Informasi Dasar</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label for="title" class="text-sm font-medium text-gray-700">Judul Iklan <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="Ex: Promo Lebaran"
                               class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors">
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="url" class="text-sm font-medium text-gray-700">Tautan Tujuan (Opsional)</label>
                        <input type="url" name="url" id="url" value="{{ old('url') }}" placeholder="https://..."
                               class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors">
                        @error('url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="description" class="text-sm font-medium text-gray-700">Deskripsi Singkat (Opsional)</label>
                    <textarea name="description" id="description" rows="2" placeholder="Tuliskan deskripsi ringkas..."
                              class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors">{{ old('description') }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Media -->
            <div class="space-y-4 pt-4">
                <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-2">Media Banner</h3>
                
                <div class="space-y-1">
                    <label for="banner" class="text-sm font-medium text-gray-700">Gambar Banner <span class="text-red-500">*</span></label>
                    <input type="file" name="banner" id="banner" required accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer border border-gray-300 rounded-lg">
                    <p class="text-[11px] text-gray-500 mt-1">Format didukung: JPG, PNG, GIF. Maks: 2MB.</p>
                    @error('banner') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Konten Lengkap -->
            <div class="space-y-4 pt-4">
                <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-2">Detail Iklan</h3>
                
                <div class="space-y-1">
                    <label for="content" class="text-sm font-medium text-gray-700">Konten Penjelasan (Opsional)</label>
                    <textarea name="content" id="content" rows="4" placeholder="Tuliskan syarat dan ketentuan atau informasi lengkap..."
                              class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors">{{ old('content') }}</textarea>
                    @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Periode & Pengaturan -->
            <div class="space-y-4 pt-4">
                <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-2">Pengaturan Penayangan</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label for="start_date" class="text-sm font-medium text-gray-700">Mulai Tayang (Opsional)</label>
                        <input type="datetime-local" name="start_date" id="start_date" value="{{ old('start_date') }}"
                               class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors">
                        @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="end_date" class="text-sm font-medium text-gray-700">Akhir Tayang (Opsional)</label>
                        <input type="datetime-local" name="end_date" id="end_date" value="{{ old('end_date') }}"
                               class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors">
                        @error('end_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <div class="relative flex items-start">
                        <div class="flex h-6 items-center">
                            <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-600">
                        </div>
                        <div class="ml-3 text-sm leading-6">
                            <label for="is_active" class="font-medium text-gray-900">Aktifkan Iklan</label>
                            <p class="text-gray-500">Iklan akan langsung tayang jika masuk dalam periode aktif.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-gray-100">
            <a href="{{ route('admin.advertisements.index') }}" class="px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-200 rounded-xl transition-colors">Batal</a>
            <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-colors shadow-sm shadow-emerald-600/20">
                Simpan Iklan
            </button>
        </div>
    </form>
</div>
@endsection

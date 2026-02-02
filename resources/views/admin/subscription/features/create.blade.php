@extends('admin.layouts.app')

@section('title', 'Tambah Fitur')
@section('page-title', 'Tambah Fitur Langganan')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <a href="{{ route('admin.subscription-features.index') }}" class="hover:text-emerald-600 transition-colors text-sm">Fitur Langganan</a>
</li>
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Tambah Fitur</span>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="px-8 py-6 bg-gray-50/50 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Form Fitur Baru</h2>
            <p class="text-sm text-gray-500 mt-1">Tambahkan kapabilitas baru ke dalam sistem langganan</p>
        </div>

        <form action="{{ route('admin.subscription-features.store') }}" method="POST" class="p-8 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-gray-700">System Name (Unique Key) <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all"
                           placeholder="e.g. advance_analytics">
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Display Name -->
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-gray-700">Display Name <span class="text-red-500">*</span></label>
                    <input type="text" name="display_name" value="{{ old('display_name') }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all"
                           placeholder="e.g. Analisis Bisnis Lanjutan">
                    @error('display_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Category -->
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-gray-700">Kategori</label>
                    <select name="category" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Icon -->
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-gray-700">Icon (FontAwesome Class)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fas fa-icons text-sm"></i>
                        </span>
                        <input type="text" name="icon" value="{{ old('icon', 'fas fa-star') }}"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all"
                               placeholder="e.g. fas fa-chart-line">
                    </div>
                    @error('icon') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Route Name -->
                <div class="space-y-1.5 md:col-span-2">
                    <label class="text-sm font-semibold text-gray-700">Route Name (Optional Access Check)</label>
                    <input type="text" name="route_name" value="{{ old('route_name') }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all"
                           placeholder="e.g. reports.index">
                </div>

                <!-- Description -->
                <div class="space-y-1.5 md:col-span-2">
                    <label class="text-sm font-semibold text-gray-700">Deskripsi</label>
                    <textarea name="description" rows="3"
                              class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all"
                              placeholder="Penjelasan singkat mengenai fitur ini...">{{ old('description') }}</textarea>
                </div>

                <!-- Sort Order -->
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-gray-700">Urutan Tampilan <span class="text-red-500">*</span></label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
                </div>

                <!-- Status -->
                <div class="space-y-1.5 flex items-end">
                    <label class="flex items-center gap-3 cursor-pointer p-2.5 rounded-xl hover:bg-gray-50 transition-colors w-full border border-transparent hover:border-gray-100">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-5 h-5 text-emerald-600 rounded-lg focus:ring-emerald-500 border-gray-300">
                        <span class="text-sm font-semibold text-gray-700">Aktifkan Fitur Ini</span>
                    </label>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.subscription-features.index') }}" 
                   class="px-6 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-50 rounded-xl transition-all">
                    Batal
                </a>
                <button type="submit" 
                        class="px-8 py-2.5 bg-gray-900 text-white text-sm font-bold rounded-xl hover:bg-indigo-600 transition-all shadow-lg hover:shadow-indigo-100 transform hover:-translate-y-0.5">
                    Simpan Fitur
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

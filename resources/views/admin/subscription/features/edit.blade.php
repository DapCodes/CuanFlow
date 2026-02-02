@extends('admin.layouts.app')

@section('title', 'Edit Fitur')
@section('page-title', 'Edit Fitur: ' . $subscriptionFeature->display_name)

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <a href="{{ route('admin.subscription-features.index') }}" class="hover:text-emerald-600 transition-colors text-sm">Fitur Langganan</a>
</li>
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Edit Fitur</span>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="px-8 py-6 bg-gray-50/50 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Edit Data Fitur</h2>
                <p class="text-sm text-gray-500 mt-1">Perbarui konfigurasi fitur dan hak akses</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-500">
                <i class="{{ $subscriptionFeature->icon ?? 'fas fa-star' }} text-lg"></i>
            </div>
        </div>

        <form action="{{ route('admin.subscription-features.update', $subscriptionFeature) }}" method="POST" class="p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="space-y-1.5 opacity-60">
                    <label class="text-sm font-semibold text-gray-700">System Name (Unique Key)</label>
                    <input type="text" name="name" value="{{ old('name', $subscriptionFeature->name) }}" required readonly
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 cursor-not-allowed outline-none transition-all">
                    <p class="text-[10px] text-gray-400 mt-1 italic">Nama sistem tidak dapat diubah karena merupakan kunci referensi.</p>
                </div>

                <!-- Display Name -->
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-gray-700">Display Name <span class="text-red-500">*</span></label>
                    <input type="text" name="display_name" value="{{ old('display_name', $subscriptionFeature->display_name) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
                    @error('display_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Category -->
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-gray-700">Kategori</label>
                    <select name="category" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ old('category', $subscriptionFeature->category) == $category ? 'selected' : '' }}>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Icon -->
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-gray-700">Icon (FontAwesome Class)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="{{ old('icon', $subscriptionFeature->icon) }} text-sm"></i>
                        </span>
                        <input type="text" name="icon" value="{{ old('icon', $subscriptionFeature->icon) }}"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
                    </div>
                </div>

                <!-- Route Name -->
                <div class="space-y-1.5 md:col-span-2">
                    <label class="text-sm font-semibold text-gray-700">Route Name (Check Access)</label>
                    <input type="text" name="route_name" value="{{ old('route_name', $subscriptionFeature->route_name) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
                </div>

                <!-- Description -->
                <div class="space-y-1.5 md:col-span-2">
                    <label class="text-sm font-semibold text-gray-700">Deskripsi</label>
                    <textarea name="description" rows="3"
                              class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">{{ old('description', $subscriptionFeature->description) }}</textarea>
                </div>

                <!-- Sort Order -->
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-gray-700">Urutan Tampilan <span class="text-red-500">*</span></label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $subscriptionFeature->sort_order) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
                </div>

                <!-- Status -->
                <div class="space-y-1.5 flex items-end">
                    <label class="flex items-center gap-3 cursor-pointer p-2.5 rounded-xl hover:bg-gray-50 transition-colors w-full border border-transparent hover:border-gray-100">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $subscriptionFeature->is_active) ? 'checked' : '' }}
                               class="w-5 h-5 text-emerald-600 rounded-lg focus:ring-emerald-500 border-gray-300">
                        <span class="text-sm font-semibold text-gray-700">Status Aktif</span>
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
                    Update Fitur
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

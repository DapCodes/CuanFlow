@extends('admin.layouts.app')

@section('title', 'Tambah Lowongan Karir')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <a href="{{ route('admin.careers.index') }}" class="text-gray-500 hover:text-emerald-600 font-medium text-sm transition-colors">Karir</a>
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Tambah</span>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Tambah Lowongan Baru</h1>
            <p class="text-sm text-gray-500 mt-0.5">Buka posisi baru untuk perekrutan talenta ke Flow Ecosystem.</p>
        </div>
        <a href="{{ route('admin.careers.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="{{ route('admin.careers.store') }}" method="POST" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        @csrf
        <div class="p-6 md:p-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Posisi Pekerjaan <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                           placeholder="Ex: Senior Backend Developer">
                    @error('title') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Lokasi <span class="text-red-500">*</span></label>
                    <input type="text" name="location" value="{{ old('location') }}" required
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                           placeholder="Ex: Bandung / Remote">
                    @error('location') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Tipe Pekerjaan <span class="text-red-500">*</span></label>
                    <input type="text" name="type" value="{{ old('type') }}" required
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                           placeholder="Ex: Penuh Waktu">
                    @error('type') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Kisaran Gaji</label>
                    <input type="text" name="salary_range" value="{{ old('salary_range') }}" 
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                           placeholder="Ex: Rp 8.000.000 - Rp 15.000.000">
                    @error('salary_range') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Batas Akhir (Deadline)</label>
                    <input type="date" name="deadline" value="{{ old('deadline') }}" 
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                    @error('deadline') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-900 mb-2">Deskripsi Pekerjaan <span class="text-red-500">*</span></label>
                <textarea name="description" rows="5" required
                          class="w-full px-4 py-3 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all custom-scrollbar"
                          placeholder="Jelaskan peran dan tanggung jawab..."></textarea>
                @error('description') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-900 mb-2">Persyaratan (Requirements) <span class="text-red-500">*</span></label>
                <textarea name="requirements" rows="5" required
                          class="w-full px-4 py-3 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all custom-scrollbar"
                          placeholder="Sebutkan skill dan pengalaman yang dibutuhkan..."></textarea>
                @error('requirements') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center pt-2">
                <label class="flex items-center cursor-pointer">
                    <div class="relative">
                        <input type="checkbox" name="is_active" value="1" class="sr-only" {{ old('is_active', true) ? 'checked' : '' }}>
                        <div class="block w-14 h-8 bg-gray-200 rounded-full transition-colors duration-300"></div>
                        <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform duration-300 shadow-sm border border-gray-100"></div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-bold text-gray-900">Aktifkan Lowongan</p>
                        <p class="text-xs text-gray-500 hidden md:block">Tampilkan lowongan di halaman karir saat ini</p>
                    </div>
                </label>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex items-center justify-end gap-3 rounded-b-2xl">
            <a href="{{ route('admin.careers.index') }}" class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 focus:ring-2 focus:ring-gray-200 transition-all">Batal</a>
            <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-emerald-600 border border-transparent rounded-xl hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500/50 transition-all shadow-sm">
                <i class="fas fa-save mr-2"></i> Simpan Lowongan
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
@endsection

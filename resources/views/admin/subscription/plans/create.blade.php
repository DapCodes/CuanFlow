@extends('admin.layouts.app')

@section('title', 'Tambah Opsi Durasi')
@section('page-title', 'Tambah Opsi Durasi & Harga')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <a href="{{ route('admin.subscription-plans.index') }}" class="hover:text-emerald-600 transition-colors text-sm">Opsi Durasi</a>
</li>
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Tambah Opsi</span>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="px-8 py-6 bg-gray-50/50 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Form Opsi Baru</h2>
            <p class="text-sm text-gray-500 mt-1">Tentukan harga berdasarkan durasi untuk tier tertentu</p>
        </div>

        <form action="{{ route('admin.subscription-plans.store') }}" method="POST" class="p-8 space-y-6" x-data="{ isUnlimited: false }">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tier -->
                <div class="space-y-1.5 md:col-span-2">
                    <label class="text-sm font-semibold text-gray-700">Pilih Paket (Tier) <span class="text-red-500">*</span></label>
                    <select name="tier_id" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
                        <option value="">-- Pilih Tier --</option>
                        @foreach($tiers as $tier)
                            <option value="{{ $tier->id }}" {{ old('tier_id') == $tier->id ? 'selected' : '' }}>{{ $tier->display_name }} (Dasar: Rp {{ number_format($tier->price, 0) }})</option>
                        @endforeach
                    </select>
                    @error('tier_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Durasi -->
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-gray-700">Durasi (Bulan) <span class="text-red-500" x-show="!isUnlimited">*</span></label>
                    <input type="number" name="duration_months" value="{{ old('duration_months', 1) }}" :required="!isUnlimited" :disabled="isUnlimited" :class="isUnlimited ? 'bg-gray-50 cursor-not-allowed' : ''"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
                    @error('duration_months') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Price -->
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-gray-700">Harga Paket <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-bold">Rp</span>
                        <input type="number" name="price" value="{{ old('price', 0) }}" required
                               class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
                    </div>
                </div>

                <!-- Discount -->
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-gray-700">Diskon (%)</label>
                    <div class="relative">
                        <input type="number" name="discount_percentage" value="{{ old('discount_percentage', 0) }}" step="0.01" min="0" max="100"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-bold">%</span>
                    </div>
                </div>

                <!-- Flags -->
                <div class="space-y-3 flex flex-col justify-end">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_unlimited" value="1" x-model="isUnlimited"
                               class="w-5 h-5 text-indigo-600 rounded-lg focus:ring-indigo-500 border-gray-300">
                        <span class="text-sm font-semibold text-gray-700">Durasi Selamanya (Unlimited)</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked
                               class="w-5 h-5 text-emerald-600 rounded-lg focus:ring-emerald-500 border-gray-300">
                        <span class="text-sm font-semibold text-gray-700">Aktifkan Opsi Ini</span>
                    </label>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.subscription-plans.index') }}" 
                   class="px-6 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-50 rounded-xl transition-all">
                    Batal
                </a>
                <button type="submit" 
                        class="px-8 py-2.5 bg-gray-900 text-white text-sm font-bold rounded-xl hover:bg-indigo-600 transition-all shadow-lg">
                    Tambah Opsi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

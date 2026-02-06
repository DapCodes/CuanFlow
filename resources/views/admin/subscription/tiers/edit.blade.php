@extends('admin.layouts.app')

@section('title', 'Edit Paket')
@section('page-title', 'Edit Paket: ' . $subscriptionTier->display_name)

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <a href="{{ route('admin.subscription-tiers.index') }}" class="hover:text-emerald-600 transition-colors text-sm">Paket Berlangganan</a>
</li>
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Edit Paket</span>
</li>
@endsection

@section('content')
<form action="{{ route('admin.subscription-tiers.update', ['tier' => $subscriptionTier->id]) }}" method="POST" class="space-y-6 max-w-5xl mx-auto">
    @csrf
    @method('PUT')
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Settings -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="px-8 py-6 bg-gray-50/50 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Informasi Utama</h2>
                        <p class="text-sm text-gray-500 mt-1">Konfigurasi dasar tier langganan</p>
                    </div>
                    <div class="px-3 py-1 bg-white border border-gray-200 rounded-lg text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        ID: #{{ $subscriptionTier->id }}
                    </div>
                </div>
                
                <div class="p-8 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Nama Internal (Kode) <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $subscriptionTier->name) }}" required
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Nama Tampilan <span class="text-red-500">*</span></label>
                            <input type="text" name="display_name" value="{{ old('display_name', $subscriptionTier->display_name) }}" required
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-gray-700">Deskripsi</label>
                        <textarea name="description" rows="3"
                                  class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">{{ old('description', $subscriptionTier->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Harga Dasar <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-bold">Rp</span>
                                <input type="number" name="price" value="{{ old('price', $subscriptionTier->price) }}" required
                                       class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Max Outlets</label>
                            <input type="number" name="max_outlets" value="{{ old('max_outlets', $subscriptionTier->max_outlets) }}"
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all"
                                   placeholder="Kosongkan untuk unlimited">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Trial Days</label>
                            <input type="number" name="trial_duration_days" value="{{ old('trial_duration_days', $subscriptionTier->trial_duration_days) }}"
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Selection -->
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="px-8 py-6 bg-gray-50/50 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Fitur yang Disertakan</h2>
                    <p class="text-sm text-gray-500 mt-1">Pilih fitur-fitur yang akan didapatkan oleh pelanggan di tier ini</p>
                </div>
                
                <div class="p-8">
                    @foreach($features as $category => $categoryFeatures)
                        <div class="mb-8 last:mb-0">
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <span>{{ $category ?? 'General' }}</span>
                                <div class="flex-1 h-px bg-gray-100"></div>
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($categoryFeatures as $feature)
                                    <label class="group flex items-start gap-3 p-4 rounded-2xl border border-gray-100 hover:border-indigo-200 hover:bg-indigo-50/30 transition-all cursor-pointer @if(in_array($feature->id, $selectedFeatures)) bg-indigo-50/20 border-indigo-100 @endif">
                                        <div class="mt-0.5">
                                            <input type="checkbox" name="features[]" value="{{ $feature->id }}"
                                                   @if(in_array($feature->id, $selectedFeatures)) checked @endif
                                                   class="w-5 h-5 text-indigo-600 rounded-lg focus:ring-indigo-500 border-gray-300 transition-all">
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900 group-hover:text-indigo-700 transition-colors">{{ $feature->display_name }}</p>
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $feature->description }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm sticky top-6">
                <div class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-gray-700">Urutan Tampilan</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $subscriptionTier->sort_order) }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
                    </div>

                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ $subscriptionTier->is_active ? 'checked' : '' }}
                                   class="w-5 h-5 text-emerald-600 rounded-lg focus:ring-emerald-500 border-gray-300">
                            <span class="text-sm font-bold text-gray-700">Paket Aktif</span>
                        </label>
                    </div>

                    <div class="pt-4 flex flex-col gap-3">
                        <button type="submit" 
                                class="w-full py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-indigo-600 transition-all shadow-lg hover:shadow-indigo-100 flex items-center justify-center gap-2">
                            <i class="fas fa-check-circle text-sm"></i>
                            Update Paket
                        </button>
                        <a href="{{ route('admin.subscription-tiers.index') }}" 
                           class="w-full py-3 text-center text-sm font-semibold text-gray-600 hover:bg-gray-50 rounded-xl transition-all">
                            Batal
                        </a>
                    </div>
                </div>

                <div class="mt-6 border-t border-gray-100 pt-6 space-y-4">
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span>Ditambahkan pada:</span>
                        <span class="font-medium">{{ $subscriptionTier->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

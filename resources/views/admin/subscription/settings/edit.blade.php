@extends('admin.layouts.app')

@section('title', 'Pengaturan Langganan')
@section('page-title', 'Konfigurasi Sistem Langganan')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Pengaturan</span>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="px-8 py-6 bg-gray-50/50 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Pengaturan Global</h2>
            <p class="text-sm text-gray-500 mt-1">Konfigurasi kebijakan trial, grace period, dan sistem langganan</p>
        </div>

        <form action="{{ route('admin.subscription-settings.update') }}" method="POST" class="p-8 space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Trial Settings -->
                <div class="space-y-6">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2">Kebijakan Trial</h3>
                    
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Durasi Trial Default (Hari)</label>
                            <input type="number" name="trial_duration_days" value="{{ old('trial_duration_days', $settings->trial_duration_days) }}" required
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
                        </div>

                        <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl hover:bg-gray-50 transition-all border border-transparent hover:border-gray-100">
                            <input type="checkbox" name="enable_trial" value="1" {{ $settings->enable_trial ? 'checked' : '' }}
                                   class="w-5 h-5 text-indigo-600 rounded-lg focus:ring-indigo-500 border-gray-300">
                            <div>
                                <p class="text-sm font-bold text-gray-700">Aktifkan Fitur Trial</p>
                                <p class="text-[10px] text-gray-500">Izinkan pengguna baru mencoba sistem secara gratis</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl hover:bg-gray-50 transition-all border border-transparent hover:border-gray-100">
                            <input type="checkbox" name="require_trial_verification" value="1" {{ $settings->require_trial_verification ? 'checked' : '' }}
                                   class="w-5 h-5 text-amber-600 rounded-lg focus:ring-amber-500 border-gray-300">
                            <div>
                                <p class="text-sm font-bold text-gray-700">Wajib Verifikasi Trial</p>
                                <p class="text-[10px] text-gray-500">Admin harus menyetujui data usaha sebelum trial aktif</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Subscription Policies -->
                <div class="space-y-6">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2">Kebijakan Langganan</h3>
                    
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-gray-700">Grace Period (Hari)</label>
                            <input type="number" name="grace_period_days" value="{{ old('grace_period_days', $settings->grace_period_days) }}" required
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all">
                            <p class="text-[10px] text-gray-500 italic mt-1">Waktu tambahan saat langganan berakhir sebelum akses diputus.</p>
                        </div>

                        <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl hover:bg-gray-50 transition-all border border-transparent hover:border-gray-100">
                            <input type="checkbox" name="auto_renew_default" value="1" {{ $settings->auto_renew_default ? 'checked' : '' }}
                                   class="w-5 h-5 text-indigo-600 rounded-lg focus:ring-indigo-500 border-gray-300">
                            <div>
                                <p class="text-sm font-bold text-gray-700">Auto-Renew Default</p>
                                <p class="text-[10px] text-gray-500">Otomatis aktifkan perpanjangan untuk pelanggan baru</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2 text-amber-600 font-medium text-xs">
                    <i class="fas fa-triangle-exclamation"></i>
                    <span>Perubahan akan berdampak pada seluruh pengguna baru.</span>
                </div>
                <button type="submit" 
                        class="px-8 py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-indigo-600 transition-all shadow-lg hover:shadow-indigo-100 transform hover:-translate-y-0.5">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

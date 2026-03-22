@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Verifikasi Uji Coba Gratis')

@push('styles')
<style>
    body {
        background: radial-gradient(circle at top left, #F0E49133, #ffffff 40%), 
                    radial-gradient(circle at bottom right, #658C5815, #f8fafc 60%);
    }

    .form-input {
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        background-color: #ffffff;
    }
    .form-input:focus {
        transform: translateY(-1px);
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.06);
    }

    .animate-slide-up {
        animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    @keyframes slideUp {
        0% { opacity: 0; transform: translateY(30px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    
    .cuan-primary { color: #658C58; }
    .bg-cuan-primary { background-color: #658C58; }
    .border-cuan-primary { border-color: #658C58; }
    .focus-ring-cuan:focus { --tw-ring-color: rgba(101, 140, 88, 0.2); }
</style>
@endpush

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Decorative Background Elements -->
    <div class="absolute top-20 left-10 w-64 h-64 bg-yellow-100/20 rounded-full blur-3xl -z-10 animate-pulse"></div>
    <div class="absolute bottom-20 right-10 w-80 h-80 bg-emerald-100/10 rounded-full blur-3xl -z-10 animate-pulse" style="animation-delay: 1s;"></div>

    <div class="w-full max-w-4xl mx-auto space-y-8 animate-slide-up">
        <!-- Header Section -->
        <header class="text-center">
            <p class="inline-flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-emerald-800/70 bg-white/70 backdrop-blur px-3 py-1 rounded-full border border-emerald-800/10 shadow-sm">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Verifikasi Uji Coba Gratis
            </p>
            <h1 class="mt-4 text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight">
                Lengkapi Data Usaha Anda
            </h1>
            <p class="mt-2 text-gray-500 text-base sm:text-lg max-w-2xl mx-auto">
                Langkah terakhir untuk memulai masa percobaan gratis (Trial) selama 30 hari. Kami butuh data ini untuk memverifikasi keaslian usaha Anda.
            </p>
        </header>

        <div class="grid gap-8 lg:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)] items-start">
            <!-- Sidebar Info -->
            <aside class="hidden lg:flex flex-col gap-6">
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-800 to-emerald-900 text-white p-7 shadow-xl">
                    <div class="absolute -top-16 -right-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="relative z-10 space-y-5">
                        <div class="inline-flex items-center gap-2 bg-white/10 rounded-full px-3 py-1 text-xs border border-white/20 backdrop-blur">
                            <i class="fa-solid fa-shield-check"></i>
                            <span>Verifikasi Aman & Terpercaya</span>
                        </div>
                        <h2 class="text-xl font-semibold leading-snug">
                            Kenapa kami butuh data ini?
                        </h2>
                        <ul class="space-y-4">
                            <li class="flex gap-3 items-start">
                                <div class="mt-0.5 w-6 h-6 rounded-full bg-white/15 flex items-center justify-center text-[10px]">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <p class="text-xs text-emerald-50/90">Memastikan CuanFlow digunakan oleh bisnis yang nyata.</p>
                            </li>
                            <li class="flex gap-3 items-start">
                                <div class="mt-0.5 w-6 h-6 rounded-full bg-white/15 flex items-center justify-center text-[10px]">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <p class="text-xs text-emerald-50/90">Menghindari penyalahgunaan fitur gratis oleh bot/spam.</p>
                            </li>
                            <li class="flex gap-3 items-start">
                                <div class="mt-0.5 w-6 h-6 rounded-full bg-white/15 flex items-center justify-center text-[10px]">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <p class="text-xs text-emerald-50/90">Membantu tim kami memberikan dukungan yang relevan dengan jenis usaha Anda.</p>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="bg-white/80 backdrop-blur border border-emerald-100 rounded-2xl p-4 flex gap-3 text-xs text-emerald-800 shadow-sm">
                    <i class="fa-solid fa-lightbulb text-emerald-500 mt-0.5"></i>
                    <p><strong>Tips:</strong> Foto yang jelas mempercepat proses verifikasi. Pastikan nama usaha terlihat pada foto depan.</p>
                </div>
            </aside>

            <!-- Form Area -->
            <section class="bg-white/90 backdrop-blur rounded-3xl border border-gray-100 p-6 sm:p-8 shadow-xl">
                @if(isset($hasUsedTrialBefore) && $hasUsedTrialBefore)
                    <div class="py-12 text-center space-y-6">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-red-50 text-red-500 border border-red-100 shadow-sm">
                            <i class="fa-solid fa-circle-xmark text-4xl"></i>
                        </div>
                        
                        <div class="space-y-2">
                            <h2 class="text-2xl font-bold text-gray-900">Akses Dibatasi</h2>
                            <p class="text-gray-500 max-w-sm mx-auto">
                                Maaf, anda atau perangkat ini sudah pernah melakukan uji coba gratis (Trial) sebelumnya. Silakan pilih paket langganan untuk melanjutkan.
                            </p>
                        </div>

                        <div class="pt-4">
                            <a href="{{ route('subscription.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition-all shadow-md">
                                <i class="fa-solid fa-crown text-sm"></i>
                                Lihat Paket Langganan
                            </a>
                        </div>
                    </div>
                @elseif(auth()->user()->hasPendingTrialVerification())
                    <div class="py-12 text-center space-y-6">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-amber-50 text-amber-500 border border-amber-100 shadow-sm animate-bounce">
                            <i class="fa-solid fa-clock-rotate-left text-4xl"></i>
                        </div>
                        
                        <div class="space-y-2">
                            <h2 class="text-2xl font-bold text-gray-900">Data Anda Sedang Dikonfirmasi</h2>
                            <p class="text-gray-500 max-w-sm mx-auto">
                                Tim kami sedang meninjau data usaha Anda. Proses ini biasanya memakan waktu maksimal 1x24 jam.
                            </p>
                        </div>

                        <div class="pt-4">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all">
                                <i class="fa-solid fa-house-chimney text-sm"></i>
                                Kembali ke Dashboard
                            </a>
                        </div>
                        
                        <div class="mt-8 p-4 bg-emerald-50 rounded-2xl border border-emerald-100 text-left flex gap-3 mx-auto max-w-md">
                            <i class="fa-solid fa-circle-info text-emerald-500 mt-1"></i>
                            <p class="text-xs text-emerald-800 leading-relaxed">
                                <strong>Info:</strong> Kami akan mengirimkan notifikasi atau mengaktifkan fitur trial secara otomatis jika data Anda sudah disetujui.
                            </p>
                        </div>
                    </div>
                @else
                    <form action="{{ route('subscription.trial-verification.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <div class="space-y-5">
                            <!-- Nama Usaha & Jenis Bisnis -->
                            <div class="grid sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Usaha / Outlet <span class="text-red-500">*</span></label>
                                    <input type="text" name="outlet_name" value="{{ old('outlet_name', auth()->user()->outlet->name ?? '') }}" required
                                        class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none text-gray-800 placeholder-gray-400">
                                    @error('outlet_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jenis Bisnis <span class="text-red-500">*</span></label>
                                    <select name="business_type" required
                                        class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none text-gray-800 bg-white">
                                        <option value="">-- Pilih Jenis --</option>
                                        <option value="F&B" {{ old('business_type') == 'F&B' ? 'selected' : '' }}>Food & Beverage</option>
                                        <option value="Retail" {{ old('business_type') == 'Retail' ? 'selected' : '' }}>Retail</option>
                                        <option value="Service" {{ old('business_type') == 'Service' ? 'selected' : '' }}>Jasa / Service</option>
                                        <option value="Other" {{ old('business_type') == 'Other' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                    @error('business_type') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi Singkat Usaha <span class="text-red-500">*</span></label>
                                <textarea name="business_description" rows="3" required
                                    class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none text-gray-800 placeholder-gray-400"
                                    placeholder="Jelaskan secara singkat usaha yang Anda jalankan...">{{ old('business_description') }}</textarea>
                                @error('business_description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Foto Depan -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Foto Depan Usaha (Store Front) <span class="text-red-500">*</span></label>
                                <div class="flex items-start gap-4 p-4 bg-slate-50/80 rounded-2xl border border-dashed border-slate-200 hover:border-emerald-500/60 transition-colors group relative">
                                    <div id="storeFrontPlaceholder" class="w-24 h-24 bg-white rounded-2xl flex items-center justify-center border border-gray-200 shadow-sm group-hover:border-emerald-500/70 transition-all">
                                        <i class="fa-solid fa-store text-gray-300 text-3xl group-hover:text-emerald-500 transition-colors"></i>
                                    </div>
                                    <img id="storeFrontPreview" class="w-24 h-24 object-cover rounded-2xl shadow-sm hidden" alt="Preview">
                                    <input type="file" name="photo_store_front" id="storeFrontInput" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer" required>
                                    
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-700">Upload Foto Depan</p>
                                        <p class="text-[11px] text-gray-500 mt-1 leading-relaxed">Pastikan banner atau nama usaha terlihat jelas. Maksimal 5MB.</p>
                                        <button type="button" id="removeStoreFront" class="text-xs text-red-500 hover:text-red-700 mt-2 font-medium hidden">
                                            <i class="fa-solid fa-trash-can mr-1"></i> Ganti Foto
                                        </button>
                                    </div>
                                </div>
                                @error('photo_store_front') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Foto Produk -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Foto Produk / Stok <span class="text-red-500">*</span></label>
                                <div class="flex items-start gap-4 p-4 bg-slate-50/80 rounded-2xl border border-dashed border-slate-200 hover:border-emerald-500/60 transition-colors group relative">
                                    <div id="productsPlaceholder" class="w-24 h-24 bg-white rounded-2xl flex items-center justify-center border border-gray-200 shadow-sm group-hover:border-emerald-500/70 transition-all">
                                        <i class="fa-solid fa-box-open text-gray-300 text-3xl group-hover:text-emerald-500 transition-colors"></i>
                                    </div>
                                    <img id="productsPreview" class="w-24 h-24 object-cover rounded-2xl shadow-sm hidden" alt="Preview">
                                    <input type="file" name="photo_products" id="productsInput" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer" required>
                                    
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-700">Upload Foto Produk</p>
                                        <p class="text-[11px] text-gray-500 mt-1 leading-relaxed">Bisa berupa foto rak produk, gudang, atau barang jualan Anda. Maksimal 5MB.</p>
                                        <button type="button" id="removeProducts" class="text-xs text-red-500 hover:text-red-700 mt-2 font-medium hidden">
                                            <i class="fa-solid fa-trash-can mr-1"></i> Ganti Foto
                                        </button>
                                    </div>
                                </div>
                                @error('photo_products') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-100">
                            <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-emerald-800 text-white font-bold py-4 rounded-2xl hover:shadow-lg hover:shadow-emerald-200 transform hover:-translate-y-1 transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-paper-plane"></i>
                                Kirim Permintaan Verifikasi
                            </button>
                            <div class="mt-4 flex items-center justify-center gap-2 text-xs text-gray-400">
                                <i class="fa-solid fa-lock text-[10px]"></i>
                                <span>Data dienkripsi dan hanya untuk verifikasi admin</span>
                            </div>
                        </div>
                    </form>
                @endif
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setupImagePreview('storeFrontInput', 'storeFrontPreview', 'storeFrontPlaceholder', 'removeStoreFront');
        setupImagePreview('productsInput', 'productsPreview', 'productsPlaceholder', 'removeProducts');
    });

    function setupImagePreview(inputId, previewId, placeholderId, removeId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const placeholder = document.getElementById(placeholderId);
        const removeBtn = document.getElementById(removeId);

        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                    removeBtn.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        });

        removeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            input.value = '';
            preview.classList.add('hidden');
            placeholder.classList.remove('hidden');
            removeBtn.classList.add('hidden');
        });
    }
</script>
@endpush

@extends('admin.layouts.app')

@section('title', 'Pengaturan Profil')

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right text-[8px] mx-2"></i>
    <span class="text-gray-600 font-medium">Pengaturan Profil</span>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Pengaturan Profil</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola informasi pribadi dan keamanan akun admin Anda.</p>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="{ activeTab: '{{ session('status') === 'password-updated' || $errors->updatePassword->isNotEmpty() ? 'security' : 'profile' }}' }">
        <!-- Sidebar Navigation -->
        <div class="lg:col-span-1">
            <nav class="flex flex-col gap-2 p-2 bg-white border border-gray-100 rounded-2xl shadow-sm sticky top-24">
                <button @click="activeTab = 'profile'" 
                        :class="activeTab === 'profile' ? 'bg-emerald-50 text-emerald-700' : 'text-gray-500 hover:bg-gray-50'"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all font-semibold text-sm text-left">
                    <i class="fas fa-user-circle text-lg"></i>
                    Informasi Profil
                </button>
                <button @click="activeTab = 'security'" 
                        :class="activeTab === 'security' ? 'bg-emerald-50 text-emerald-700' : 'text-gray-500 hover:bg-gray-50'"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all font-semibold text-sm text-left">
                    <i class="fas fa-shield-alt text-lg"></i>
                    Keamanan
                </button>
            </nav>
        </div>

        <!-- Forms Container -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Profile Information Card -->
            <div x-show="activeTab === 'profile'" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden animate-fade-in">
                <div class="p-6 border-b border-gray-50">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-user text-emerald-500"></i>
                        Informasi Profil
                    </h2>
                </div>
                <div class="p-6 sm:p-8">
                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <!-- Avatar Section -->
                        <div class="flex flex-col sm:flex-row items-center gap-6">
                            <div class="relative group">
                                <div class="w-32 h-32 rounded-3xl border-4 border-white shadow-xl overflow-hidden bg-gray-50">
                                    <img id="avatar-preview" src="{{ $user->avatar_url }}" class="w-full h-full object-cover">
                                </div>
                                <label for="avatar" class="absolute -bottom-2 -right-2 w-10 h-10 bg-white border border-gray-100 rounded-xl shadow-lg flex items-center justify-center cursor-pointer hover:bg-emerald-600 hover:text-white transition-all group-hover:scale-110">
                                    <i class="fas fa-camera text-sm"></i>
                                    <input type="file" name="avatar" id="avatar" class="sr-only" accept="image/*" onchange="previewAvatar(this)">
                                </label>
                            </div>
                            <div class="text-center sm:text-left">
                                <h3 class="text-base font-bold text-gray-900">Foto Profil</h3>
                                <p class="text-xs text-gray-500 mt-1 max-w-[240px]">
                                    Gunakan foto resmi Anda. Format PNG atau JPG (Maks. 2MB).
                                </p>
                            </div>
                        </div>

                        <!-- Form Grid -->
                        <div class="grid grid-cols-1 gap-6">
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Nama Lengkap</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                        <i class="fas fa-user text-xs"></i>
                                    </span>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                           class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-100 rounded-xl text-sm font-semibold text-gray-900 focus:ring-4 focus:ring-emerald-500/5 focus:border-emerald-500 transition-all">
                                </div>
                                @error('name') <p class="text-[10px] text-red-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Alamat Email</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                        <i class="fas fa-envelope text-xs"></i>
                                    </span>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                           class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-100 rounded-xl text-sm font-semibold text-gray-900 focus:ring-4 focus:ring-emerald-500/5 focus:border-emerald-500 transition-all">
                                </div>
                                @error('email') <p class="text-[10px] text-red-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Nomor Telepon</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                        <i class="fas fa-phone text-xs"></i>
                                    </span>
                                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                           class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-100 rounded-xl text-sm font-semibold text-gray-900 focus:ring-4 focus:ring-emerald-500/5 focus:border-emerald-500 transition-all">
                                </div>
                                @error('phone') <p class="text-[10px] text-red-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="pt-4 flex items-center justify-between gap-4">
                            <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 bg-emerald-600 text-white text-xs font-bold uppercase tracking-wider rounded-xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-200">
                                <i class="fas fa-save"></i>
                                Simpan Perubahan
                            </button>
                            <span class="text-[10px] text-gray-400 font-medium">Terakhir diperbarui: {{ $user->updated_at->diffForHumans() }}</span>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Card -->
            <div x-show="activeTab === 'security'" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden animate-fade-in" x-cloak>
                <div class="p-6 border-b border-gray-50">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-shield-halved text-emerald-500"></i>
                        Keamanan & Password
                    </h2>
                </div>
                <div class="p-6 sm:p-8">
                    <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Password Saat Ini</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-lock text-xs"></i>
                                </span>
                                <input type="password" name="current_password" required
                                       class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-100 rounded-xl text-sm font-semibold text-gray-900 focus:ring-4 focus:ring-emerald-500/5 focus:border-emerald-500 transition-all">
                            </div>
                            @error('current_password', 'updatePassword') <p class="text-[10px] text-red-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Password Baru</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                        <i class="fas fa-key text-xs"></i>
                                    </span>
                                    <input type="password" name="password" required
                                           class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-100 rounded-xl text-sm font-semibold text-gray-900 focus:ring-4 focus:ring-emerald-500/5 focus:border-emerald-500 transition-all">
                                </div>
                                @error('password', 'updatePassword') <p class="text-[10px] text-red-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider ml-1">Konfirmasi Password</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                        <i class="fas fa-check-double text-xs"></i>
                                    </span>
                                    <input type="password" name="password_confirmation" required
                                           class="w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-100 rounded-xl text-sm font-semibold text-gray-900 focus:ring-4 focus:ring-emerald-500/5 focus:border-emerald-500 transition-all">
                                </div>
                            </div>
                        </div>

                        <div class="bg-amber-50 rounded-xl p-4 border border-amber-100 flex gap-3">
                            <i class="fas fa-lightbulb text-amber-500 mt-0.5"></i>
                            <div class="text-[11px] text-amber-800 font-medium leading-relaxed">
                                <strong>Tips:</strong> Gunakan minimal 8 karakter dengan kombinasi huruf besar, angka, dan simbol unik untuk keamanan akun yang lebih baik.
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 bg-gray-900 text-white text-xs font-bold uppercase tracking-wider rounded-xl hover:bg-black transition-all shadow-lg">
                                <i class="fas fa-key"></i>
                                Perbarui Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fade-in 0.4s ease-out forwards; }
    [x-cloak] { display: none !important; }
</style>
@endpush
@endsection

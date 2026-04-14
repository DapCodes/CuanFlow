@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Pengaturan Akun - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Pengaturan dan Akun</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-[#f9fafb] shadow-sm md:shadow-none" x-data="{ 
    activeTab: '{{ request('tab') ?? (session('status') === 'password-updated' || $errors->updatePassword->isNotEmpty() || request()->reset_token || old('token') ? 'security' : 'profile') }}',
    showResetModal: {{ (request()->reset_token || old('token')) && $errors->updatePassword->isEmpty() ? 'true' : 'false' }},
    appLayout: localStorage.getItem('app_layout') || 'grid',
    updateLayout(choice) {
        if (this.appLayout === choice) return;

        this.appLayout = choice;
        
        // Use Alpine Store for instant global switch
        if (window.Alpine && Alpine.store('app')) {
            Alpine.store('app').setLayout(choice);
        } else {
            // Fallback for direct storage if store isn't ready
            localStorage.setItem('app_layout', choice);
            document.cookie = 'app_layout=' + choice + ';path=/;max-age=' + (60*60*24*365);
        }
        
        // Dispatch event for any other listeners
        window.dispatchEvent(new CustomEvent('app-layout-changed', { detail: choice }));
    },
    switchTab(tab) {
        this.activeTab = tab;
        // Clean URL if any tab parameter exists
        if (window.location.search.includes('tab=')) {
            const url = new URL(window.location);
            url.searchParams.delete('tab');
            window.history.replaceState({}, '', url.pathname + (url.search ? url.search : ''));
        }
    }
}">
    <div class="max-w-6xl mx-auto space-y-8">
        
        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div class="animate-fade-in-down">
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Pengaturan dan Akun</h1>
                <p class="text-sm text-gray-500 font-medium mt-1">Kelola informasi pribadi, keamanan, dan preferensi akun Anda.</p>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if (session('status') === 'profile-updated')
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="px-4 py-3 bg-gray-900 text-white rounded-2xl shadow-lg flex items-center justify-between animate-fade-in-down border border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
                        <i class="fas fa-check text-xs"></i>
                    </div>
                    <span class="text-sm font-bold">Profil Berhasil Diperbarui</span>
                </div>
                <button @click="show = false" class="text-white/50 hover:text-white transition-colors"><i class="fas fa-times text-xs"></i></button>
            </div>
        @endif

        @if (session('status') === 'password-updated')
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="px-4 py-3 bg-emerald-600 text-white rounded-2xl shadow-lg flex items-center justify-between animate-fade-in-down border border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
                        <i class="fas fa-key text-xs"></i>
                    </div>
                    <span class="text-sm font-bold">Kata Sandi Berhasil Diperbarui</span>
                </div>
                <button @click="show = false" class="text-white/50 hover:text-white transition-colors"><i class="fas fa-times text-xs"></i></button>
            </div>
        @endif

        @if (session('status') && !in_array(session('status'), ['profile-updated', 'password-updated']))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="px-4 py-3 bg-[#1e293b] text-white rounded-2xl shadow-lg flex items-center justify-between animate-fade-in-down border border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
                        <i class="fas fa-paper-plane text-xs"></i>
                    </div>
                    <span class="text-sm font-bold">{{ session('status') }}</span>
                </div>
                <button @click="show = false" class="text-white/50 hover:text-white transition-colors"><i class="fas fa-times text-xs"></i></button>
            </div>
        @endif

        @if ($errors->updatePassword->isNotEmpty())
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="px-4 py-3 bg-red-600 text-white rounded-2xl shadow-lg flex items-center justify-between animate-fade-in-down border border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
                        <i class="fas fa-exclamation-circle text-xs"></i>
                    </div>
                    <span class="text-sm font-bold">Gagal Memperbarui Kata Sandi. Silakan periksa kembali form Anda.</span>
                </div>
                <button @click="show = false" class="text-white/50 hover:text-white transition-colors"><i class="fas fa-times text-xs"></i></button>
            </div>
        @endif

        {{-- Main Layout Subgrid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Navigation - Responsive Sidebar/Tabs --}}
            <aside class="lg:col-span-3 space-y-4">
                {{-- Desktop Sidebar --}}
                <nav class="hidden lg:flex flex-col gap-1.5 p-2 bg-white border border-gray-200 rounded-2xl shadow-sm">
                    <button @click="switchTab('profile')" :class="activeTab === 'profile' ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700'" class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all font-bold text-sm text-left group">
                        <i class="fas fa-user-circle text-lg opacity-40 group-hover:opacity-100 transition-opacity" :class="activeTab === 'profile' ? 'opacity-100' : ''"></i>
                        Informasi Profil
                    </button>
                    <button @click="switchTab('security')" :class="activeTab === 'security' ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700'" class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all font-bold text-sm text-left group">
                        <i class="fas fa-shield-alt text-lg opacity-40 group-hover:opacity-100 transition-opacity" :class="activeTab === 'security' ? 'opacity-100' : ''"></i>
                        Keamanan
                    </button>
                    <button @click="switchTab('appearance')" :class="activeTab === 'appearance' ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700'" class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all font-bold text-sm text-left group">
                        <i class="fas fa-display text-lg opacity-40 group-hover:opacity-100 transition-opacity" :class="activeTab === 'appearance' ? 'opacity-100' : ''"></i>
                        Tampilan Aplikasi
                    </button>
                    <div class="my-2 border-t border-gray-100 mx-2"></div>

                </nav>

                {{-- Mobile Horizontal Tabs --}}
                <nav class="lg:hidden flex border border-gray-200 rounded-2xl bg-white p-1 overflow-x-auto no-scrollbar scroll-smooth shadow-sm">
                    <button @click="switchTab('profile')" :class="activeTab === 'profile' ? 'bg-gray-100 text-gray-900 shadow-sm' : 'text-gray-500'" class="flex-1 whitespace-nowrap px-4 py-3 rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-user-circle"></i> Profil
                    </button>
                    <button @click="switchTab('security')" :class="activeTab === 'security' ? 'bg-gray-100 text-gray-900 shadow-sm' : 'text-gray-500'" class="flex-1 whitespace-nowrap px-4 py-3 rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-shield-alt"></i> Keamanan
                    </button>
                    <button @click="switchTab('appearance')" :class="activeTab === 'appearance' ? 'bg-gray-100 text-gray-900 shadow-sm' : 'text-gray-500'" class="flex-1 whitespace-nowrap px-4 py-3 rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-display"></i> Tampilan
                    </button>
                </nav>

                {{-- Help Card (Desktop Only) --}}
                <div class="hidden lg:block p-6 bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl shadow-xl text-white relative overflow-hidden group">
                    <div class="relative z-10">
                        <h4 class="text-sm font-bold mb-2">Butuh Bantuan?</h4>
                        <p class="text-[11px] text-gray-400 leading-relaxed font-medium mb-4">Jika Anda mengalami kendala saat mengatur akun, hubungi bantuan kami.</p>
                        <a href="{{ route('faqs.index') }}" class="inline-flex items-center text-[10px] font-black uppercase tracking-widest text-white/50 hover:text-white transition-colors">
                            Buka FAQ <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                    <i class="fas fa-question-circle absolute -bottom-4 -right-4 text-7xl text-white/5 opacity-20"></i>
                </div>
            </aside>

            {{-- Main Content Area --}}
            <div class="lg:col-span-9 space-y-6 min-h-[500px]">
                
                {{-- Profile Section --}}
                <section x-show="activeTab === 'profile'" class="animate-fade-in-up" x-cloak>
                    <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden divide-y divide-gray-100">
                        <div class="p-6 md:p-8 lg:p-10">
                            <div class="flex items-center gap-4 mb-10">
                                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-sm border border-blue-100/50">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-black text-gray-900">Profil</h2>
                                    <p class="text-xs text-gray-500 font-medium mt-0.5">Lindungi akun Anda dengan password yang kuat dan unik.</p>
                                </div>
                            </div>
                            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-10">
                                @csrf
                                @method('PATCH')

                                {{-- Avatar Management --}}
                                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-8">
                                    <div class="relative">
                                        <div class="w-32 h-32 md:w-40 md:h-40 rounded-3xl border-4 border-gray-50 shadow-2xl overflow-hidden bg-gray-100 rotate-1 group transition-transform hover:rotate-0 duration-500">
                                            <img id="avatar-preview" src="{{ auth()->user()->avatar_url }}" class="w-full h-full object-cover">
                                        </div>
                                        <label for="avatar" class="absolute -bottom-2 -right-2 w-12 h-12 bg-white border border-gray-200 rounded-2xl shadow-xl flex items-center justify-center cursor-pointer hover:bg-gray-900 hover:text-white hover:scale-110 transition-all text-gray-600 active:scale-95 duration-300">
                                            <i class="fas fa-camera text-sm"></i>
                                            <input type="file" name="avatar" id="avatar" class="sr-only" accept="image/*" onchange="previewAvatar(this)">
                                        </label>
                                    </div>
                                    <div class="flex-grow space-y-3 py-2 text-center sm:text-left">
                                        <h3 class="text-xl font-black text-gray-900 tracking-tight">Foto Profil</h3>
                                        <p class="text-sm text-gray-500 leading-relaxed font-medium max-w-md">
                                            Tingkatkan kredibilitas akun dengan foto yang jelas. Gunakan format PNG, JPG, atau GIF (Maks. 2MB).
                                        </p>
                                        <div class="flex flex-wrap justify-center sm:justify-start gap-2 pt-2">
                                            <span class="px-3 py-1 bg-gray-50 text-[10px] font-black uppercase text-gray-400 rounded-lg border border-gray-100">2048 x 2048px</span>
                                            <span class="px-3 py-1 bg-gray-50 text-[10px] font-black uppercase text-gray-400 rounded-lg border border-gray-100">Max 2MB</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Form Inputs Grid --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-10">
                                    {{-- Full Name --}}
                                    <div class="space-y-3">
                                        <label for="name" class="flex items-center text-[11px] font-black uppercase text-gray-400 tracking-[0.2em] gap-2 pl-1">
                                            <i class="fas fa-id-card opacity-50"></i> Nama Lengkap
                                        </label>
                                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                            class="w-full px-5 py-4 bg-[#f9fafb] border-gray-200 rounded-2xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-gray-900/5 focus:border-gray-900 focus:bg-white transition-all placeholder:text-gray-300">
                                        @error('name') <p class="text-[11px] text-red-500 font-bold mt-1.5 pl-1 italic">{{ $message }}</p> @enderror
                                    </div>

                                    {{-- Phone Number --}}
                                    <div class="space-y-3">
                                        <label for="phone" class="flex items-center text-[11px] font-black uppercase text-gray-400 tracking-[0.2em] gap-2 pl-1">
                                            <i class="fas fa-phone opacity-50"></i> Nomor WhatsApp
                                        </label>
                                        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                                            placeholder="Contoh: 08123456xxx"
                                            class="w-full px-5 py-4 bg-[#f9fafb] border-gray-200 rounded-2xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-gray-900/5 focus:border-gray-900 focus:bg-white transition-all placeholder:text-gray-300">
                                        @error('phone') <p class="text-[11px] text-red-500 font-bold mt-1.5 pl-1 italic">{{ $message }}</p> @enderror
                                    </div>

                                    {{-- Email Address --}}
                                    <div class="md:col-span-2 space-y-3">
                                        <label for="email" class="flex items-center text-[11px] font-black uppercase text-gray-400 tracking-[0.2em] gap-2 pl-1">
                                            <i class="fas fa-envelope opacity-50"></i> Alamat Email Perusahaan
                                        </label>
                                        <div class="relative group">
                                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                                class="w-full px-5 py-4 bg-[#f9fafb] border-gray-200 rounded-2xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-gray-900/5 focus:border-gray-900 focus:bg-white transition-all placeholder:text-gray-300">
                                            
                                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                                <div class="mt-4 flex items-center justify-between bg-emerald-50 text-emerald-900 rounded-2xl p-4 border border-emerald-100/50 animate-pulse">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-8 h-8 rounded-full bg-emerald-600/10 flex items-center justify-center">
                                                            <i class="fas fa-info-circle text-xs text-emerald-600"></i>
                                                        </div>
                                                        <p class="text-xs font-bold tracking-tight">Email Anda belum terverifikasi secara resmi.</p>
                                                    </div>
                                                    <button form="send-verification" class="text-xs font-black uppercase tracking-widest hover:text-emerald-600 transition-colors">Kirim Ulang Link</button>
                                                </div>
                                            @endif
                                        </div>
                                        @error('email') <p class="text-[11px] text-red-500 font-bold mt-1.5 pl-1 italic">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="pt-10 flex flex-col sm:flex-row items-center gap-10">
                                    @can('update profil')
                                    <button type="submit" class="w-full sm:w-auto px-10 py-4 bg-gray-900 text-white rounded-2xl shadow-2xl shadow-gray-200 hover:bg-black transition-all text-xs font-black uppercase tracking-[0.2em] active:scale-95 duration-200">
                                        Simpan Profil Baru
                                    </button>
                                    @endcan
                                    <span class="hidden sm:block text-[10px] text-gray-300 font-medium">Terakhir diperbarui: {{ $user->updated_at->diffForHumans() }}</span>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>

                {{-- Security Tab Content --}}
                <section x-show="activeTab === 'security'" class="animate-fade-in-up" x-cloak>
                    <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden">
                        <div class="p-6 md:p-8 lg:p-10">
                            <div class="flex items-center gap-4 mb-10">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-sm border border-emerald-100/50">
                                    <i class="fas fa-shield-halved"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-black text-gray-900">Keamanan & Password</h2>
                                    <p class="text-xs text-gray-500 font-medium mt-0.5">Lindungi akun Anda dengan password yang kuat dan unik.</p>
                                </div>
                            </div>

                            <form action="{{ route('password.update') }}" method="POST" class="space-y-8">
                                @csrf
                                @method('PUT')

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-10">
                                    {{-- Current Password --}}
                                    <div class="space-y-3">
                                        <label for="current_password" class="text-[11px] font-black uppercase text-gray-400 tracking-[0.2em] pl-1">Kata Sandi Saat Ini</label>
                                        <div class="relative group">
                                            <input type="password" name="current_password" id="current_password" required
                                                class="w-full px-5 py-4 bg-[#f9fafb] border-gray-200 rounded-2xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-emerald-500/5 focus:border-emerald-500 focus:bg-white transition-all">
                                            <button type="button" @click="$refs.currPass.type = $refs.currPass.type === 'password' ? 'text' : 'password'" class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-900 transition-colors">
                                                <i class="fas fa-eye-slash text-xs transition-opacity duration-300" x-ref="eyeIcon"></i>
                                            </button>
                                            <input x-ref="currPass" type="password" class="hidden"> {{-- Dummy for Alpine ref logic helper --}}
                                        </div>
                                        @error('current_password', 'updatePassword') <p class="text-[11px] text-red-500 font-bold mt-1.5 pl-1 italic">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="hidden md:block"></div> {{-- Spacer --}}

                                    {{-- New Password --}}
                                    <div class="space-y-3">
                                        <label for="password" class="text-[11px] font-black uppercase text-gray-400 tracking-[0.2em] pl-1">Kata Sandi Baru</label>
                                        <div class="relative">
                                            <input type="password" name="password" id="password" required
                                                class="w-full px-5 py-4 bg-[#f9fafb] border-gray-200 rounded-2xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-emerald-500/5 focus:border-emerald-500 focus:bg-white transition-all">
                                        </div>
                                        @error('password', 'updatePassword') <p class="text-[11px] text-red-500 font-bold mt-1.5 pl-1 italic">{{ $message }}</p> @enderror
                                    </div>

                                    {{-- Confirm Password --}}
                                    <div class="space-y-3">
                                        <label for="password_confirmation" class="text-[11px] font-black uppercase text-gray-400 tracking-[0.2em] pl-1">Konfirmasi Sandi Baru</label>
                                        <input type="password" name="password_confirmation" id="password_confirmation" required
                                            class="w-full px-5 py-4 bg-[#f9fafb] border-gray-200 rounded-2xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-emerald-500/5 focus:border-emerald-500 focus:bg-white transition-all">
                                    </div>

                                    {{-- Password Tips --}}
                                    <div class="md:col-span-2 bg-gray-50 rounded-2xl p-6 flex items-start gap-4 border border-gray-100">
                                        <div class="w-8 h-8 rounded-xl bg-white flex items-center justify-center text-gray-400 shadow-sm">
                                            <i class="fas fa-lightbulb text-xs"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-[11px] font-black uppercase tracking-wider text-gray-600 mb-1">Tips Keamanan Pintar</h4>
                                            <p class="text-xs text-gray-500 font-medium leading-relaxed">
                                                Gunakan minimal <span class="text-gray-900 font-bold">8 karakter</span>. Kombinasikan huruf besar, angka, dan simbol unik (seperti #, !, ?) agar akun Anda sulit ditembus oleh peretas.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-6">
                                    @can('update profil')
                                    <button type="submit" class="w-full sm:w-auto px-10 py-4 bg-emerald-600 text-white rounded-2xl shadow-2xl shadow-emerald-200 hover:bg-emerald-700 transition-all text-xs font-black uppercase tracking-[0.2em] active:scale-95">
                                        Perbarui Kata Sandi
                                    </button>
                                    @endcan
                                </div>
                            </form>

                            <div class="mt-6 flex justify-end">
                                @if (Route::has('password.email.authenticated'))
                                    <form method="POST" action="{{ route('password.email.authenticated') }}">
                                        @csrf
                                        <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                                        <button type="submit" class="text-[11px] font-black uppercase tracking-widest text-gray-400 hover:text-gray-900 transition-colors border-b border-transparent hover:border-gray-900">
                                            Lupa Kata Sandi Akun?
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Google Account Binding --}}
                    <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden mt-6">
                        <div class="p-6 md:p-8 lg:p-10">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center text-xl shadow-sm border border-red-100/50">
                                    <i class="fab fa-google"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-black text-gray-900">Tautan Akun Google</h2>
                                    <p class="text-xs text-gray-500 font-medium mt-0.5">Hubungkan akun Google untuk login yang lebih cepat dan aman.</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between p-5 bg-gray-50 rounded-2xl border border-gray-100">
                                <div class="flex items-center gap-4">
                                    <i class="fab fa-google text-2xl {{ auth()->user()->google_id ? 'text-red-500' : 'text-gray-400' }}"></i>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">Google</p>
                                        <p class="text-[11px] font-medium text-gray-500">
                                            {{ auth()->user()->google_id ? 'Terhubung' : 'Belum terhubung' }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-3">
                                    @if(auth()->user()->google_id)
                                        <form action="{{ route('auth.google.unlink') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-6 py-2.5 bg-white border border-red-200 text-red-600 rounded-xl hover:bg-red-50 transition-all text-[11px] font-black uppercase tracking-wider shadow-sm">
                                                Putuskan
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('auth.google') }}" class="px-6 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all text-[11px] font-black uppercase tracking-wider shadow-sm">
                                            Hubungkan
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                
                {{-- Appearance Tab Content --}}
                <section x-show="activeTab === 'appearance'" class="animate-fade-in-up" x-cloak
                    x-data="{
                        activePaletteId: {{ auth()->user()->color_palette_id ?? 'null' }},
                        saving: false,
                        saved: false,
                        appTheme: localStorage.getItem('theme_mode') || 'light',
                        
                        init() {
                            this.applyTheme(this.appTheme);
                        },

                        applyTheme(mode) {
                            if (mode === 'dark' || (mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                                document.documentElement.classList.add('dark');
                            } else {
                                document.documentElement.classList.remove('dark');
                            }
                        },

                        updateTheme(mode) {
                            if (this.appTheme === mode) return;
                            this.appTheme = mode;
                            localStorage.setItem('theme_mode', mode);
                            this.applyTheme(mode);
                        },
                        
                        selectPalette(id, palette) {
                            if (this.activePaletteId === id || this.saving) return;
                            this.activePaletteId = id;
                            this.saving = true;
                            this.saved = false;

                            fetch('{{ route('profile.color-palette.update') }}', {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({ color_palette_id: id }),
                            })
                            .then(r => r.json())
                            .then(data => {
                                if (data.success) {
                                    const base = data.palette.color_green;
                                    
                                    const mixHex = (hex, mixWith, weight) => {
                                        hex = hex.replace('#', ''); mixWith = mixWith.replace('#', '');
                                        const r = Math.round(parseInt(hex.substr(0, 2), 16) * (1 - weight) + parseInt(mixWith.substr(0, 2), 16) * weight);
                                        const g = Math.round(parseInt(hex.substr(2, 2), 16) * (1 - weight) + parseInt(mixWith.substr(2, 2), 16) * weight);
                                        const b = Math.round(parseInt(hex.substr(4, 2), 16) * (1 - weight) + parseInt(mixWith.substr(4, 2), 16) * weight);
                                        return '#' + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).padStart(6, '0');
                                    };
                                    
                                    const newGreenScale = {
                                        50: mixHex(base, '#ffffff', 0.95),
                                        100: mixHex(base, '#ffffff', 0.87),
                                        200: mixHex(base, '#ffffff', 0.75),
                                        300: mixHex(base, '#ffffff', 0.60),
                                        400: mixHex(base, '#ffffff', 0.40),
                                        500: base,
                                        600: mixHex(base, '#000000', 0.18),
                                        700: mixHex(base, '#000000', 0.34),
                                        800: mixHex(base, '#000000', 0.52),
                                        900: mixHex(base, '#000000', 0.68),
                                        950: mixHex(base, '#000000', 0.80),
                                    };

                                    const newPalette = {
                                        'cuan-yellow': data.palette.color_yellow,
                                        'cuan-olive':  data.palette.color_olive,
                                        'cuan-green':  data.palette.color_green,
                                        'cuan-dark':   data.palette.color_dark,
                                    };

                                    tailwind.config.theme.extend.colors = {
                                        ...tailwind.config.theme.extend.colors,
                                        ...newPalette,
                                        primary: { DEFAULT: data.palette.color_green, ...newGreenScale },
                                        green: newGreenScale,
                                        emerald: newGreenScale,
                                        lime: newGreenScale,
                                    };

                                    const oldScript = document.querySelector('script[src=\'https://cdn.tailwindcss.com\']');
                                    if (oldScript) {
                                        const newScript = document.createElement('script');
                                        newScript.src = 'https://cdn.tailwindcss.com';
                                        oldScript.parentNode.replaceChild(newScript, oldScript);
                                    }

                                    this.saving = false;
                                    this.saved = true;
                                    setTimeout(() => this.saved = false, 2500);
                                }
                            })
                            .catch(() => { this.saving = false; });
                        }
                    }">
                    <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden">
                        <div class="p-6 md:p-8 lg:p-10">
                            <div class="flex items-center gap-4 mb-10">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shadow-sm border border-indigo-100/50">
                                    <i class="fas fa-palette"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-black text-gray-900">Tampilan Aplikasi</h2>
                                    <p class="text-xs text-gray-500 font-medium mt-0.5">Pilih layout dan warna tema yang paling nyaman untuk Anda.</p>
                                </div>
                            </div>

                            {{-- Layout Selector --}}
                            <div class="mb-10">
                                <h3 class="text-[11px] font-black uppercase text-gray-400 tracking-[0.2em] mb-5 flex items-center gap-2">
                                    <i class="fas fa-layout opacity-50"></i> Layout Interface
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {{-- Layout Option 1: Grid (Default) --}}
                                    <div @click="updateLayout('grid')" 
                                         class="relative group cursor-pointer rounded-3xl border-2 transition-all p-4"
                                         :class="appLayout === 'grid' ? 'border-gray-900 bg-gray-50 shadow-xl' : 'border-gray-100 hover:border-gray-300'">
                                        
                                        <div class="aspect-video bg-gray-200 rounded-2xl mb-4 overflow-hidden relative shadow-inner">
                                            <!-- Grid Layout Mockup -->
                                            <div class="absolute inset-0 flex flex-col">
                                                <div class="h-4 bg-gray-400 w-full mb-1"></div> <!-- Top Nav -->
                                                <div class="flex-1 p-2 grid grid-cols-3 gap-1">
                                                    <div class="h-4 bg-gray-300 rounded"></div>
                                                    <div class="h-4 bg-gray-300 rounded"></div>
                                                    <div class="h-4 bg-gray-300 rounded"></div>
                                                    <div class="h-4 bg-gray-300 rounded"></div>
                                                    <div class="h-4 bg-gray-300 rounded"></div>
                                                </div>
                                            </div>
                                            <div x-show="appLayout === 'grid'" class="absolute inset-0 bg-gray-900/10 flex items-center justify-center">
                                                <div class="bg-white rounded-full w-10 h-10 flex items-center justify-center shadow-lg"><i class="fas fa-check text-gray-900"></i></div>
                                            </div>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <h3 class="text-sm font-black text-gray-900">Layout Grid (Bawaan)</h3>
                                                <p class="text-[10px] text-gray-500 font-medium italic mt-0.5">Navigasi atas, konten melebar.</p>
                                            </div>
                                            <span x-show="appLayout === 'grid'" class="text-[10px] font-black uppercase tracking-widest text-emerald-600 px-2 py-1 bg-emerald-50 rounded-lg">Aktif</span>
                                        </div>
                                    </div>

                                    {{-- Layout Option 2: Sidebar --}}
                                    <div @click="updateLayout('sidebar')" 
                                         class="relative group cursor-pointer rounded-3xl border-2 transition-all p-4"
                                         :class="appLayout === 'sidebar' ? 'border-gray-900 bg-gray-50 shadow-xl' : 'border-gray-100 hover:border-gray-300'">
                                        
                                        <div class="aspect-video bg-gray-200 rounded-2xl mb-4 overflow-hidden relative shadow-inner">
                                            <!-- Sidebar Layout Mockup -->
                                            <div class="absolute inset-0 flex">
                                                <div class="w-1/4 bg-gray-400 h-full"></div> <!-- Sidebar -->
                                                <div class="flex-1 p-2 space-y-1">
                                                    <div class="h-2 bg-gray-300 w-1/2 rounded mb-2"></div> <!-- Title -->
                                                    <div class="h-10 bg-gray-300 w-full rounded"></div>
                                                    <div class="h-10 bg-gray-300 w-full rounded"></div>
                                                </div>
                                            </div>
                                            <div x-show="appLayout === 'sidebar'" class="absolute inset-0 bg-gray-900/10 flex items-center justify-center">
                                                <div class="bg-white rounded-full w-10 h-10 flex items-center justify-center shadow-lg"><i class="fas fa-check text-gray-900"></i></div>
                                            </div>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <h3 class="text-sm font-black text-gray-900">Layout Sidebar</h3>
                                                <p class="text-[10px] text-gray-500 font-medium italic mt-0.5">Navigasi samping, akses cepat.</p>
                                            </div>
                                            <span x-show="appLayout === 'sidebar'" class="text-[10px] font-black uppercase tracking-widest text-emerald-600 px-2 py-1 bg-emerald-50 rounded-lg">Aktif</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Divider --}}
                            <div class="border-t border-gray-100 mb-10"></div>
                            
                            {{-- Theme Mode Selector --}}
                            <div class="mb-10">
                                <h3 class="text-[11px] font-black uppercase text-gray-400 tracking-[0.2em] mb-5 flex items-center gap-2">
                                    <i class="fas fa-moon opacity-50"></i> Tema Tampilan
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    {{-- Light --}}
                                    <div @click="updateTheme('light')" 
                                         class="relative group cursor-pointer rounded-3xl border-2 transition-all p-4"
                                         :class="appTheme === 'light' ? 'border-gray-900 bg-gray-50 shadow-xl' : 'border-gray-100 hover:border-gray-300'">
                                        <div class="aspect-video bg-white rounded-2xl mb-4 overflow-hidden relative shadow-inner border border-gray-100 flex items-center justify-center">
                                            <i class="fas fa-sun text-4xl text-amber-500 hover:rotate-45 transition-transform duration-500"></i>
                                            <div x-show="appTheme === 'light'" class="absolute inset-0 bg-gray-900/5 flex items-center justify-center">
                                                <div class="bg-white rounded-full w-10 h-10 flex items-center justify-center shadow-lg"><i class="fas fa-check text-gray-900"></i></div>
                                            </div>
                                        </div>
                                        <div class="flex justify-between items-center mt-2">
                                            <div>
                                                <h3 class="text-sm font-black text-gray-900">Terang</h3>
                                            </div>
                                            <span x-show="appTheme === 'light'" class="text-[10px] font-black uppercase tracking-widest text-emerald-600 px-2 py-1 bg-emerald-50 rounded-lg">Aktif</span>
                                        </div>
                                    </div>

                                    {{-- Dark --}}
                                    <div @click="updateTheme('dark')" 
                                         class="relative group cursor-pointer rounded-3xl border-2 transition-all p-4"
                                         :class="appTheme === 'dark' ? 'border-gray-900 bg-gray-50 shadow-xl' : 'border-gray-100 hover:border-gray-300'">
                                        <div class="aspect-video bg-[#0f172a] rounded-2xl mb-4 overflow-hidden relative shadow-inner flex items-center justify-center">
                                            <i class="fas fa-moon text-4xl text-blue-300 hover:-rotate-12 transition-transform duration-500"></i>
                                            <div x-show="appTheme === 'dark'" class="absolute inset-0 bg-white/5 flex items-center justify-center">
                                                <div class="bg-white rounded-full w-10 h-10 flex items-center justify-center shadow-lg"><i class="fas fa-check text-gray-900"></i></div>
                                            </div>
                                        </div>
                                        <div class="flex justify-between items-center mt-2">
                                            <div>
                                                <h3 class="text-sm font-black text-gray-900">Gelap</h3>
                                            </div>
                                            <span x-show="appTheme === 'dark'" class="text-[10px] font-black uppercase tracking-widest text-emerald-600 px-2 py-1 bg-emerald-50 rounded-lg">Aktif</span>
                                        </div>
                                    </div>

                                    {{-- System --}}
                                    <div @click="updateTheme('system')" 
                                         class="relative group cursor-pointer rounded-3xl border-2 transition-all p-4"
                                         :class="appTheme === 'system' ? 'border-gray-900 bg-gray-50 shadow-xl' : 'border-gray-100 hover:border-gray-300'">
                                        <div class="aspect-video bg-gradient-to-r from-gray-100 to-gray-800 rounded-2xl mb-4 overflow-hidden relative shadow-inner flex items-center justify-center">
                                            <i class="fas fa-desktop text-4xl text-gray-400 group-hover:scale-110 transition-transform duration-500"></i>
                                            <div x-show="appTheme === 'system'" class="absolute inset-0 bg-gray-900/10 flex items-center justify-center">
                                                <div class="bg-white rounded-full w-10 h-10 flex items-center justify-center shadow-lg"><i class="fas fa-check text-gray-900"></i></div>
                                            </div>
                                        </div>
                                        <div class="flex justify-between items-center mt-2">
                                            <div>
                                                <h3 class="text-sm font-black text-gray-900">Bawaan Sistem</h3>
                                            </div>
                                            <span x-show="appTheme === 'system'" class="text-[10px] font-black uppercase tracking-widest text-emerald-600 px-2 py-1 bg-emerald-50 rounded-lg">Aktif</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Divider --}}
                            <div class="border-t border-gray-100 mb-10"></div>

                            {{-- Color Palette Selector --}}
                            <div>
                                <div class="flex items-center justify-between mb-6">
                                    <div>
                                        <h3 class="text-[11px] font-black uppercase text-gray-400 tracking-[0.2em] flex items-center gap-2">
                                            <i class="fas fa-swatchbook opacity-50"></i> Tema Warna Aplikasi
                                        </h3>
                                        <p class="text-xs text-gray-500 font-medium mt-1">Pilih palet warna yang paling mencerminkan kepribadian bisnis Anda.</p>
                                    </div>
                                    {{-- Status badge --}}
                                    <div class="flex items-center gap-2 min-w-[80px] justify-end">
                                        <span x-show="saving" class="flex items-center gap-1.5 text-[10px] font-black text-gray-400 uppercase tracking-wider">
                                            <svg class="animate-spin h-3 w-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                                            Menyimpan
                                        </span>
                                        <span x-show="saved && !saving" class="flex items-center gap-1.5 text-[10px] font-black text-emerald-600 uppercase tracking-wider">
                                            <i class="fas fa-check-circle"></i> Tersimpan
                                        </span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4" id="palette-grid">
                                    @foreach($colorPalettes as $palette)
                                    <div
                                        @click="selectPalette({{ $palette->id }}, {{ json_encode($palette->toTailwindColors()) }})"
                                        :class="activePaletteId === {{ $palette->id }}
                                            ? 'ring-2 ring-gray-900 shadow-xl scale-[1.03]'
                                            : 'ring-1 ring-gray-100 hover:ring-gray-300 hover:shadow-md hover:scale-[1.02]'"
                                        class="relative rounded-2xl overflow-hidden cursor-pointer transition-all duration-200 group bg-white">

                                        {{-- Color swatch preview --}}
                                        <div class="w-full h-16 flex">
                                            <div class="flex-1" style="background:{{ $palette->color_dark }}"></div>
                                            <div class="flex-1" style="background:{{ $palette->color_green }}"></div>
                                            <div class="flex-1" style="background:{{ $palette->color_olive }}"></div>
                                            <div class="flex-1" style="background:{{ $palette->color_yellow }}"></div>
                                        </div>

                                        {{-- Palette info --}}
                                        <div class="p-3">
                                            <div class="flex items-center justify-between">
                                                <p class="text-[11px] font-black text-gray-800 leading-tight truncate pr-1">{{ $palette->name }}</p>
                                                <div x-show="activePaletteId === {{ $palette->id }}"
                                                    class="w-5 h-5 rounded-full bg-gray-900 flex items-center justify-center flex-shrink-0">
                                                    <i class="fas fa-check text-white" style="font-size:8px"></i>
                                                </div>
                                            </div>
                                            {{-- Mini color dots --}}
                                            <div class="flex gap-1 mt-2">
                                                <div class="w-3 h-3 rounded-full border border-white/50 shadow-sm" style="background:{{ $palette->color_dark }}"></div>
                                                <div class="w-3 h-3 rounded-full border border-white/50 shadow-sm" style="background:{{ $palette->color_green }}"></div>
                                                <div class="w-3 h-3 rounded-full border border-white/50 shadow-sm" style="background:{{ $palette->color_olive }}"></div>
                                                <div class="w-3 h-3 rounded-full border border-white/50 shadow-sm" style="background:{{ $palette->color_yellow }}"></div>
                                            </div>
                                        </div>

                                        {{-- Default badge --}}
                                        @if($palette->is_default)
                                        <div class="absolute top-2 left-2">
                                            <span class="text-[8px] font-black uppercase tracking-wider px-1.5 py-0.5 bg-black/50 text-white rounded-full backdrop-blur-sm">Bawaan</span>
                                        </div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>

                                 <p class="text-[10px] text-gray-400 font-medium mt-5 flex items-center gap-1.5 cursor-help" title="Warna akan diperbarui secara otomatis tanpa me-refresh halaman">
                                    <i class="fas fa-magic text-emerald-500"></i>
                                    Berkat fitur Live Preview, setiap warna akan segera diaplikasikan secara *real-time* ke seluruh aplikasi.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>


            </div>
        </div>
    </div>

    {{-- Reset Password Modal --}}
    <div x-show="showResetModal" 
        class="fixed inset-0 z-[100] overflow-y-auto" 
        x-transition:enter="transition ease-out duration-300" 
        x-transition:enter-start="opacity-0" 
        x-transition:enter-end="opacity-100" 
        x-transition:leave="transition ease-in duration-200" 
        x-transition:leave-start="opacity-100" 
        x-transition:leave-end="opacity-0"
        x-cloak>
        
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showResetModal = false">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full animate-scale-in">
                <div class="bg-white p-8 sm:p-10">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-sm border border-emerald-100/50">
                                <i class="fas fa-key"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900">Reset Kata Sandi</h3>
                                <p class="text-xs text-gray-500 font-medium">Buat kata sandi baru untuk akun Anda.</p>
                            </div>
                        </div>
                        <button @click="showResetModal = false" class="text-gray-400 hover:text-gray-900 transition-colors">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
                        @csrf
                        <input type="hidden" name="token" value="{{ request()->reset_token ?? old('token') }}">
                        <input type="hidden" name="email" value="{{ request()->email ?? old('email') }}">

                        <div class="space-y-2">
                            <label for="modal_password" class="text-[11px] font-black uppercase text-gray-400 tracking-[0.2em] pl-1">Kata Sandi Baru</label>
                            <input type="password" name="password" id="modal_password" required autofocus
                                class="w-full px-5 py-4 bg-[#f9fafb] border-gray-200 rounded-2xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-emerald-500/5 focus:border-emerald-500 focus:bg-white transition-all">
                            @error('password') <p class="text-[11px] text-red-500 font-bold mt-1 pl-1 italic">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="modal_password_confirmation" class="text-[11px] font-black uppercase text-gray-400 tracking-[0.2em] pl-1">Konfirmasi Kata Sandi</label>
                            <input type="password" name="password_confirmation" id="modal_password_confirmation" required
                                class="w-full px-5 py-4 bg-[#f9fafb] border-gray-200 rounded-2xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-emerald-500/5 focus:border-emerald-500 focus:bg-white transition-all">
                        </div>

                        <div class="pt-4 flex flex-col gap-3">
                            <button type="submit" class="w-full px-10 py-4 bg-gray-900 text-white rounded-2xl shadow-xl hover:bg-black transition-all text-xs font-black uppercase tracking-[0.2em] active:scale-95">
                                Atur Ulang Kata Sandi
                            </button>
                            <button type="button" @click="showResetModal = false" class="w-full px-10 py-4 bg-gray-50 text-gray-500 rounded-2xl hover:bg-gray-100 transition-all text-xs font-black uppercase tracking-[0.2em]">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>


@push('scripts')
<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const formData = new FormData();
            var reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('avatar-preview');
                preview.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Toggle Password Helpers for Alpine would be cleaner but JS works for now
    function togglePassword(fieldId) {
        const input = document.getElementById(fieldId);
        const icon = input.nextElementSibling.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        }
    }


</script>

<style>
    /* Premium Animations */
    @keyframes fade-in-up {
        0% { opacity: 0; transform: translateY(30px) scale(0.98); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }
    .animate-fade-in-up { animation: fade-in-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    
    @keyframes fade-in-down {
        0% { opacity: 0; transform: translateY(-20px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-down { animation: fade-in-down 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

    @keyframes scale-in {
        0% { opacity: 0; transform: scale(0.95); }
        100% { opacity: 1; transform: scale(1); }
    }
    .animate-scale-in { animation: scale-in 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

    /* Hide Scrollbar for tabs */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    [x-cloak] { display: none !important; }
</style>
@endpush
@endsection


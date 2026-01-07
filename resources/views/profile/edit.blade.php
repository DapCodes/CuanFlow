@extends('layouts.app')

@section('title', 'Pengaturan Akun - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Pengaturan Akun</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-[#f9fafb]" x-data="{ activeTab: 'profile' }">
    <div class="max-w-6xl mx-auto space-y-8">
        
        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div class="animate-fade-in-down">
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Pengaturan Akun</h1>
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

        {{-- Main Layout Subgrid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Navigation - Responsive Sidebar/Tabs --}}
            <aside class="lg:col-span-3 space-y-4">
                {{-- Desktop Sidebar --}}
                <nav class="hidden lg:flex flex-col gap-1.5 p-2 bg-white border border-gray-200 rounded-2xl shadow-sm">
                    <button @click="activeTab = 'profile'" :class="activeTab === 'profile' ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700'" class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all font-bold text-sm text-left group">
                        <i class="fas fa-user-circle text-lg opacity-40 group-hover:opacity-100 transition-opacity" :class="activeTab === 'profile' ? 'opacity-100' : ''"></i>
                        Informasi Profil
                    </button>
                    <button @click="activeTab = 'security'" :class="activeTab === 'security' ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700'" class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all font-bold text-sm text-left group">
                        <i class="fas fa-shield-alt text-lg opacity-40 group-hover:opacity-100 transition-opacity" :class="activeTab === 'security' ? 'opacity-100' : ''"></i>
                        Keamanan
                    </button>
                    <div class="my-2 border-t border-gray-100 mx-2"></div>
                    <button @click="activeTab = 'danger'" :class="activeTab === 'danger' ? 'bg-red-50 text-red-600' : 'text-gray-500 hover:bg-red-50 hover:text-red-600'" class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all font-bold text-sm text-left group">
                        <i class="fas fa-exclamation-triangle text-lg opacity-40 group-hover:opacity-100 transition-opacity" :class="activeTab === 'danger' ? 'opacity-100' : ''"></i>
                        Hapus Akun
                    </button>
                </nav>

                {{-- Mobile Horizontal Tabs --}}
                <nav class="lg:hidden flex border border-gray-200 rounded-2xl bg-white p-1 overflow-x-auto no-scrollbar scroll-smooth shadow-sm">
                    <button @click="activeTab = 'profile'" :class="activeTab === 'profile' ? 'bg-gray-100 text-gray-900 shadow-sm' : 'text-gray-500'" class="flex-1 whitespace-nowrap px-4 py-3 rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-user-circle"></i> Profil
                    </button>
                    <button @click="activeTab = 'security'" :class="activeTab === 'security' ? 'bg-gray-100 text-gray-900 shadow-sm' : 'text-gray-500'" class="flex-1 whitespace-nowrap px-4 py-3 rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-shield-alt"></i> Keamanan
                    </button>
                    <button @click="activeTab = 'danger'" :class="activeTab === 'danger' ? 'bg-red-50 text-red-600' : 'text-gray-500'" class="flex-1 whitespace-nowrap px-4 py-3 rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-trash"></i> Hapus
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
                                    <button type="submit" class="w-full sm:w-auto px-10 py-4 bg-gray-900 text-white rounded-2xl shadow-2xl shadow-gray-200 hover:bg-black transition-all text-xs font-black uppercase tracking-[0.2em] active:scale-95 duration-200">
                                        Simpan Profil Baru
                                    </button>
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
                                    <button type="submit" class="w-full sm:w-auto px-10 py-4 bg-emerald-600 text-white rounded-2xl shadow-2xl shadow-emerald-200 hover:bg-emerald-700 transition-all text-xs font-black uppercase tracking-[0.2em] active:scale-95">
                                        Perbarui Kata Sandi
                                    </button>
                                    
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-[11px] font-black uppercase tracking-widest text-gray-400 hover:text-gray-900 transition-colors border-b border-transparent hover:border-gray-900">
                                            Lupa Kata Sandi Akun?
                                        </a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </section>

                {{-- Danger Zone Content --}}
                <section x-show="activeTab === 'danger'" class="animate-fade-in-up" x-cloak>
                    <div class="bg-white border border-red-100 rounded-3xl shadow-sm overflow-hidden divide-y divide-red-50">
                        <div class="p-6 md:p-8 lg:p-10">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center text-xl shadow-sm border border-red-100/50">
                                    <i class="fas fa-skull"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-black text-gray-900">Penghapusan Akun</h2>
                                    <p class="text-xs text-gray-500 font-medium mt-0.5 italic">Hanya dilakukan jika Anda benar-benar yakin.</p>
                                </div>
                            </div>

                            <div class="bg-red-50 rounded-2xl p-6 border border-red-100/50 mb-10 flex gap-5">
                                <div class="hidden sm:flex flex-shrink-0 w-12 h-12 bg-white rounded-2xl items-center justify-center text-red-500 shadow-sm">
                                    <i class="fas fa-info-circle text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-red-900 mb-1">Semua data akan hilang selamanya</h4>
                                    <p class="text-xs text-red-700 leading-relaxed font-medium">
                                        Menghapus akun akan memusnahkan seluruh riwayat transaksi, data outlet, laporan keuangan, dan akses login selamanya. Tindakan ini tidak dapat dibatalkan melalui bantuan admin sekalipun.
                                    </p>
                                </div>
                            </div>

                            <button onclick="confirmAccountDeletion()" class="w-full sm:w-auto px-10 py-4 bg-red-600 text-white rounded-2xl shadow-2xl shadow-red-200 hover:bg-black transition-all text-xs font-black uppercase tracking-[0.2em] active:scale-95">
                                Hapus Akun Saya Permanen
                            </button>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>
</main>

{{-- Account Deletion Modal Overlay --}}
<div id="deletion-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" x-cloak>
    <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-md" onclick="closeDeletionModal()"></div>
    
    <div class="bg-white rounded-[2rem] overflow-hidden shadow-2xl transform transition-all w-full max-w-lg relative p-8 md:p-12 border border-white/20 animate-scale-in">
        <form action="{{ route('profile.destroy') }}" method="POST" class="space-y-8">
            @csrf
            @method('DELETE')
            
            <div class="flex flex-col items-center text-center">
                <div class="w-20 h-20 bg-red-50 text-red-600 rounded-[2rem] flex items-center justify-center text-3xl mb-6 shadow-xl border border-red-100">
                    <i class="fas fa-user-xmark"></i>
                </div>
                <h3 class="text-2xl font-black text-gray-900 mb-3 tracking-tight">Konfirmasi Akhir</h3>
                <p class="text-sm text-gray-500 font-medium leading-relaxed px-4">
                    Tindakan ini permanen. Silakan masukkan kata sandi keamanan Anda untuk melanjutkan penghapusan akun <span class="text-gray-900 font-bold tracking-tight">{{ $user->email }}</span>.
                </p>
            </div>
            
            <div class="space-y-3">
                <label class="text-[11px] font-black uppercase text-gray-400 tracking-widest pl-1">Masukkkan Kata Sandi</label>
                <input type="password" name="password" required placeholder="• • • • • • • • •"
                    class="w-full px-6 py-4 bg-[#f9fafb] border-gray-200 rounded-2xl text-center text-lg focus:ring-4 focus:ring-red-500/10 focus:border-red-600 transition-all font-bold tracking-widest">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <button type="button" onclick="closeDeletionModal()" class="py-4 text-xs font-black uppercase tracking-widest text-gray-400 hover:text-gray-900 transition-colors">Batal</button>
                <button type="submit" class="py-4 bg-red-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-xl shadow-red-200 hover:bg-black transition-all active:scale-95">Ya, Hapus Akun</button>
            </div>
        </form>
    </div>
</div>

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

    function confirmAccountDeletion() {
        const modal = document.getElementById('deletion-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDeletionModal() {
        const modal = document.getElementById('deletion-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
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


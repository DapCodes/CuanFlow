<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CuanFlow - Lengkapi Profil</title>
    
    <!-- Scripts & Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@700,500,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('assets/image/logo.svg') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary-green: #2D5A43;
            --accent-green: #D9E88E;
        }
        * { box-sizing: border-box; }
        body { 
            font-family: 'Satoshi', sans-serif; 
            overflow: hidden;
            height: 100vh;
            height: 100dvh;
        }

        .bg-login-green { background-color: var(--primary-green); }
        .text-accent { color: var(--accent-green); }

        /* SweetAlert Custom */
        .swal2-popup { font-family: 'Satoshi', sans-serif !important; border-radius: 12px !important; }
        .swal2-confirm {
            background-color: #1a1a1a !important;
            border-radius: 8px !important;
            padding: 10px 24px !important;
            font-weight: 600 !important;
        }

        .input-focus {
            transition: all 0.2s ease;
        }
        .input-focus:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 1px var(--primary-green);
        }

        /* Float Animation */
        @keyframes float {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(3deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }
        .float-shape { animation: float 6s ease-in-out infinite; }
        
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-white">

    <div class="flex h-screen overflow-hidden">
        
        <!-- Left Section: Google Welcome Info -->
        <div class="hidden lg:flex lg:w-1/2 bg-login-green text-white p-12 xl:p-20 flex-col justify-between relative overflow-hidden h-full">
            <!-- Background Decoration -->
            <div class="absolute top-10 right-[-5%] w-64 h-64 border border-white/10 rounded-3xl float-shape opacity-40"></div>
            <div class="absolute bottom-20 left-[-5%] w-48 h-48 border border-white/5 rounded-full float-shape opacity-30" style="animation-delay: -2s;"></div>
            
            <!-- Content Top -->
            <div class="relative z-10">
                <h1 class="text-5xl xl:text-6xl font-bold leading-tight tracking-tight mb-4">
                    Satu Langkah<br>
                    <span class="text-accent">Lagi!</span>
                </h1>
                <div class="inline-flex items-center gap-2 bg-white/10 px-4 py-2 rounded-full border border-white/20 backdrop-blur-sm">
                    <span class="w-2 h-2 bg-accent rounded-full animate-pulse"></span>
                    <span class="text-xs font-bold uppercase tracking-widest text-accent">Lengkapi Profil Anda</span>
                </div>
            </div>

            <!-- Main Content -->
            <div class="relative z-10 mb-20 max-w-md">
                <p class="text-lg xl:text-xl font-medium leading-relaxed opacity-80">
                    Sesaat lagi Anda akan bergabung dengan ekosistem <span class="text-accent font-bold">CuanFlow</span> menggunakan akun Google <span class="text-accent font-bold">{{ $googleUser['email'] }}</span>.
                </p>
                <div class="mt-8 flex items-center gap-4">
                     <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-accent backdrop-blur-md border border-white/20">
                         <i class="fas fa-user-check text-xl"></i>
                     </div>
                     <p class="text-sm font-bold opacity-60 italic">Profil Hampir Siap</p>
                </div>
            </div>

            <!-- Footer Left -->
            <div class="relative z-10 text-xs opacity-40">
                <p>&copy; 2025 CuanFlow. All rights reserved.</p>
            </div>
        </div>

        <!-- Right Section: Google Profile Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-12 xl:p-16 bg-white shrink-0 h-full overflow-y-auto no-scrollbar" x-data="{ loading: false }">
            <div class="w-full max-w-sm lg:max-w-md my-auto">
                
                <!-- Logo -->
                <div class="mb-8 text-center lg:text-left">
                    <img src="{{ asset('assets/image/full-logo.svg') }}" alt="CuanFlow" class="h-9 w-auto inline-block lg:block" />
                </div>

                <!-- Title Section -->
                <div class="mb-8 text-center lg:text-left">
                    <div class="flex items-center justify-center lg:justify-start gap-4 mb-4">
                         <div class="relative">
                            <img src="{{ $googleUser['google_avatar'] ?? asset('assets/image/default-avatar.png') }}" 
                                 alt="Avatar" 
                                 class="w-16 h-16 rounded-2xl border-2 border-gray-100 p-0.5 object-cover shadow-sm">
                            <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-white rounded-lg flex items-center justify-center border border-gray-100 shadow-sm">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                            </div>
                         </div>
                         <div class="text-left">
                             <h2 class="text-2xl font-bold text-gray-900 leading-tight">Halo, {{ explode(' ', $googleUser['name'] ?? 'User')[0] }}!</h2>
                             <p class="text-gray-500 text-sm">Sedikit data lagi untuk profil Anda.</p>
                         </div>
                    </div>
                </div>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-2xl">
                        <ul class="text-[10px] font-bold text-red-600 space-y-1 uppercase tracking-tight">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="complete-profile-form" method="POST" action="{{ route('auth.google.store') }}">
                    @csrf
                    <input type="hidden" name="email" value="{{ $googleUser['email'] ?? '' }}">
                    <input type="hidden" name="name" value="{{ $googleUser['name'] ?? '' }}">
                    <input type="hidden" name="google_id" value="{{ $googleUser['id'] ?? $googleUser['google_id'] ?? '' }}">
                    <input type="hidden" name="google_avatar" value="{{ $googleUser['google_avatar'] ?? '' }}">
                    
                    <div class="space-y-4 xl:space-y-5">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-500 px-1 uppercase tracking-wider">Nomor Telepon</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" required autofocus placeholder="08xxxxxxxxxx"
                                class="w-full px-4 py-3.5 text-sm font-medium text-gray-900 bg-gray-50/50 border border-gray-100 rounded-xl placeholder-gray-400 input-focus focus:outline-none" />
                        </div>

                        <div class="space-y-1.5" x-data="{ show: false }">
                            <label class="block text-xs font-bold text-gray-500 px-1 uppercase tracking-wider">Buat Kata Sandi</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="password" required placeholder="••••••••"
                                    class="w-full px-4 py-3.5 pr-12 text-sm font-medium text-gray-900 bg-gray-50/50 border border-gray-100 rounded-xl placeholder-gray-400 input-focus focus:outline-none" />
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-4 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-1.5" x-data="{ show: false }">
                            <label class="block text-xs font-bold text-gray-500 px-1 uppercase tracking-wider">Konfirmasi Kata Sandi</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="password_confirmation" required placeholder="••••••••"
                                    class="w-full px-4 py-3.5 pr-12 text-sm font-medium text-gray-900 bg-gray-50/50 border border-gray-100 rounded-xl placeholder-gray-400 input-focus focus:outline-none" />
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-4 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Terms -->
                        <div class="flex items-start gap-3 px-1">
                            <input type="checkbox" name="terms" id="terms" required checked
                                class="mt-1 w-4 h-4 text-gray-900 border-gray-200 rounded focus:ring-0 transition-all checked:bg-gray-900" />
                            <label for="terms" class="text-[10px] xl:text-xs text-gray-500 leading-relaxed font-medium">
                                Saya setuju dengan <a href="{{ route('legal.terms') }}" target="_blank" class="text-gray-900 font-bold underline">Syarat & Ketentuan</a> serta Kebijakan Privasi
                            </label>
                        </div>

                        <button
                            type="submit"
                            :disabled="loading"
                            @click="loading = true"
                            class="w-full py-4 bg-gray-900 text-white rounded-xl text-base font-bold hover:bg-black transition-all shadow-md active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        >
                            <template x-if="!loading">
                                <span>Selesaikan Pendaftaran</span>
                            </template>
                            <template x-if="loading">
                                <div class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Memproses...</span>
                                </div>
                            </template>
                        </button>
                    </div>
                </form>

                <!-- Footer (Only Mobile) -->
                <div class="lg:hidden mt-12 text-center">
                    <p class="text-[10px] font-bold text-gray-300 uppercase tracking-widest">&copy; 2025 CuanFlow. All rights reserved.</p>
                </div>

            </div>
        </div>
    </div>

    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        // SweetAlert config
        const Toast = Swal.mixin({
            buttonsStyling: false,
            heightAuto: false,
            customClass: { confirmButton: 'swal2-confirm' }
        });

        function showAlert(icon, title, text) {
            return Toast.fire({ icon, title, text, confirmButtonText: 'Mengerti' });
        }

        // Form Submission Validation
        document.getElementById('complete-profile-form').addEventListener('submit', function(e) {
            const phone = this.querySelector('input[name="phone"]').value;
            const password = this.querySelector('input[name="password"]').value;
            const confirm = this.querySelector('input[name="password_confirmation"]').value;
            
            if (!phone || !password || !confirm) {
                // Let browser default validation handle it
                return;
            }

            if (password !== confirm) {
                e.preventDefault();
                showAlert('error', 'Kata sandi tidak cocok', 'Konfirmasi kata sandi tidak sesuai.');
            }
        });

        // Laravel alerts
        @if ($errors->any())
            window.addEventListener('DOMContentLoaded', () => showAlert('error', 'Peringatan', '{{ $errors->first() }}'));
        @endif
    </script>
</body>
</html>

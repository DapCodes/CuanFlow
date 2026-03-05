<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CuanFlow - Masuk</title>
    
    <!-- Scripts & Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@700,500,400&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('assets/image/logo.svg') }}" type="image/x-icon">
    
    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <!-- SweetAlert2 -->
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
        
        /* Swiper Custom */
        .swiper-pagination-bullet { background: rgba(255, 255, 255, 0.5) !important; opacity: 1; }
        .swiper-pagination-bullet-active { background: white !important; }

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

        /* Hide scrollbar but keep functionality if needed elsewhere */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-white">

    <div class="flex flex-col lg:flex-row h-screen overflow-hidden">
        
        <!-- Left Section: Information & Testimonials -->
        <div class="hidden lg:flex lg:w-1/2 bg-login-green text-white p-12 xl:p-20 flex-col justify-between relative overflow-hidden h-full">
            <!-- Background Decoration -->
            <div class="absolute top-10 right-[-5%] w-64 h-64 border border-white/10 rounded-3xl float-shape opacity-40"></div>
            <div class="absolute bottom-20 left-[-5%] w-48 h-48 border border-white/5 rounded-full float-shape opacity-30" style="animation-delay: -2s;"></div>
            
            <!-- Content Top -->
            <div class="relative z-10">
                <h1 class="text-5xl xl:text-6xl font-bold leading-tight tracking-tight">
                    Hello<br>
                    <span class="text-accent">SobatCuan!</span>
                </h1>
            </div>

            <!-- Testimonial Section -->
            <div class="relative z-10 mb-10 max-w-md">
                <h3 class="text-lg font-medium mb-6 opacity-70 italic">apa kata mereka?</h3>
                
                <div class="swiper testimonial-slider overflow-visible">
                    <div class="swiper-wrapper">
                        <!-- Slide 1 -->
                        <div class="swiper-slide card-compact">
                            <div class="bg-white/10 backdrop-blur-lg border border-white/20 p-6 rounded-2xl">
                                <p class="text-base xl:text-lg leading-relaxed mb-4 italic text-gray-100">
                                    "CuanFlow sangat membantu saya dalam mengelola keuangan bisnis kecil saya. Sekarang semua jadi lebih teratur dan performa bisnis meningkat!"
                                </p>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center text-accent font-bold text-lg uppercase border border-white/20">B</div>
                                    <div>
                                        <h4 class="font-bold text-sm">Budi Pratama</h4>
                                        <p class="text-xs opacity-50">Owner Kedai Kopi</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Slide 2 -->
                        <div class="swiper-slide">
                            <div class="bg-white/10 backdrop-blur-lg border border-white/20 p-6 rounded-2xl">
                                <p class="text-base xl:text-lg leading-relaxed mb-4 italic text-gray-100">
                                    "Fitur-fiturnya lengkap dan sangat mudah dipahami. Rekomendasi banget buat sobat cuan yang mau bisnisnya makin profesional."
                                </p>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center text-accent font-bold text-lg uppercase border border-white/20">S</div>
                                    <div>
                                        <h4 class="font-bold text-sm">Siti Aminah</h4>
                                        <p class="text-xs opacity-50">Freelancer Designer</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Left -->
            <div class="relative z-10 text-xs opacity-40">
                <p>&copy; 2025 CuanFlow. All rights reserved.</p>
            </div>
        </div>

        <!-- Right Section: Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-12 xl:p-16 bg-white shrink-0 h-full overflow-y-auto no-scrollbar" x-data="{ loading: false }">
            <div class="w-full max-w-sm lg:max-w-md my-auto">
                
                <!-- Logo -->
                <div class="mb-8 text-center lg:text-left">
                    <img src="{{ asset('assets/image/full-logo.svg') }}" alt="CuanFlow" class="h-9 w-auto inline-block lg:block" />
                </div>

                <!-- Title Section -->
                <div class="mb-7 text-center lg:text-left">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang!</h2>
                    <p class="text-gray-500 text-sm xl:text-base">
                        Belum punya akun? 
                        <a href="{{ route('register') }}" class="text-gray-900 font-bold hover:underline">Buat akun sekarang!</a>
                        <span class="block text-xs opacity-75 mt-0.5 font-medium italic">Ini GRATIS, gunakan waktumu!</span>
                    </p>
                </div>

                @if ($lockoutSeconds > 0)
                    <!-- Lockout State -->
                    <div class="mb-6 p-6 bg-red-50 border border-red-100 rounded-2xl text-center">
                        <div class="w-14 h-14 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h4 class="text-base font-bold text-red-800 mb-1 leading-tight">Terlalu banyak percobaan</h4>
                        <p class="text-xs text-red-600 mb-5">Silakan coba lagi dalam beberapa saat:</p>
                        <div id="countdown" class="text-3xl font-bold text-gray-900 font-mono mb-6 tracking-tight">00:00:00</div>

                        <div class="space-y-2.5">
                            <a href="{{ route('auth.google') }}"
                               class="w-full flex items-center justify-center gap-3 py-3 border border-gray-200 rounded-xl text-xs font-bold text-gray-700 hover:bg-gray-50 transition-all shadow-sm">
                                <svg class="w-4 h-4" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                                Masuk dengan Google
                            </a>
                            <a href="https://wa.me/6281221049828" target="_blank"
                               class="w-full flex items-center justify-center gap-3 py-3 bg-emerald-500 text-white rounded-xl text-xs font-bold hover:bg-emerald-600 transition-all shadow-md">
                                Hubungi Admin
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Login Form -->
                    <form id="login-form" method="POST" action="{{ route('login') }}" class="space-y-4 xl:space-y-5">
                        @csrf

                        <!-- Email -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-500 px-1 uppercase tracking-wider">Alamat Email</label>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="email@example.com"
                                required
                                autofocus
                                class="w-full px-4 py-3.5 text-sm font-medium text-gray-900 bg-gray-50/50 border border-gray-100 rounded-xl placeholder-gray-400 input-focus focus:outline-none @error('email') border-red-500 @enderror"
                            />
                            @error('email')
                                <p class="text-red-500 text-[10px] font-bold mt-1 px-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="space-y-1.5" x-data="{ show: false }">
                            <label class="block text-xs font-bold text-gray-500 px-1 uppercase tracking-wider">Kata Sandi</label>
                            <div class="relative">
                                <input
                                    :type="show ? 'text' : 'password'"
                                    name="password"
                                    placeholder="••••••••"
                                    required
                                    class="w-full px-4 py-3.5 pr-12 text-sm font-medium text-gray-900 bg-gray-50/50 border border-gray-100 rounded-xl placeholder-gray-400 input-focus focus:outline-none @error('password') border-red-500 @enderror"
                                />
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-4 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-red-500 text-[10px] font-bold mt-1 px-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Remember & Options -->
                        <div class="flex items-center justify-between pb-1 px-1">
                            <label class="flex items-center cursor-pointer group">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    id="remember"
                                    class="w-4 h-4 text-gray-900 border-gray-200 rounded focus:ring-0 transition-all checked:bg-gray-900"
                                />
                                <span class="ml-2 text-xs font-semibold text-gray-400 group-hover:text-gray-900 transition-colors">Ingat Saya</span>
                            </label>
                            <a href="{{ route('password.request') }}" class="text-[11px] font-bold text-gray-400 hover:text-gray-900 transition-colors">Lupa Kata Sandi? <span class="underline">Klik Disini</span></a>
                        </div>

                        <!-- Submit -->
                        <button
                            type="submit"
                            :disabled="loading"
                            @click="loading = true"
                            class="w-full py-4 bg-gray-900 text-white rounded-xl text-base font-bold hover:bg-black transition-all shadow-md active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        >
                            <template x-if="!loading">
                                <span>Masuk</span>
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
                    </form>

                    <!-- Divider -->
                    <div class="flex items-center my-6">
                        <div class="flex-1 border-t border-gray-100"></div>
                        <span class="px-4 text-[10px] font-bold text-gray-300 uppercase tracking-widest">atau</span>
                        <div class="flex-1 border-t border-gray-100"></div>
                    </div>

                    <!-- Google Login -->
                    <a href="{{ route('auth.google') }}"
                       class="w-full flex items-center justify-center gap-3 py-3.5 border border-gray-100 rounded-xl text-gray-700 text-sm font-bold hover:bg-gray-50 hover:border-gray-200 transition-all shadow-sm">
                        <svg class="w-5 h-5" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                        Lanjutkan dengan Google
                    </a>
                @endif

                <!-- Footer (Only Mobile view has this at bottom, on Desktop it's in the left section) -->
                <div class="lg:hidden mt-12 text-center">
                    <p class="text-[10px] font-bold text-gray-300 uppercase tracking-widest">&copy; 2025 CuanFlow. All rights reserved.</p>
                </div>

            </div>
        </div>
    </div>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        // Swiper Initialization
        new Swiper(".testimonial-slider", {
            loop: true,
            autoplay: {
                delay: 4500,
                disableOnInteraction: false,
            },
            effect: "fade",
            fadeEffect: {
                crossFade: true
            },
        });

        // SweetAlert config
        const Toast = Swal.mixin({
            buttonsStyling: false,
            heightAuto: false,
            customClass: { confirmButton: 'swal2-confirm' }
        });

        function showAlert(icon, title, text) {
            return Toast.fire({ icon, title, text, confirmButtonText: 'Mengerti' });
        }

        // Form submit validation
        const loginForm = document.getElementById('login-form');
        if (loginForm) {
            loginForm.addEventListener('submit', (e) => {
                const email = document.querySelector('input[name="email"]').value;
                const password = document.querySelector('input[name="password"]').value;
                if (!email || !password) {
                    e.preventDefault();
                    showAlert('warning', 'Lengkapi form', 'Mohon isi email dan password terlebih dahulu.');
                }
            });
        }

        // Lockout countdown
        const countdownEl = document.getElementById('countdown');
        if (countdownEl) {
            let secondsLeft = {{ $lockoutSeconds }};
            function tick() {
                const h = Math.floor(secondsLeft / 3600);
                const m = Math.floor((secondsLeft % 3600) / 60);
                const s = secondsLeft % 60;
                countdownEl.textContent =
                    String(h).padStart(2,'0') + ':' +
                    String(m).padStart(2,'0') + ':' +
                    String(s).padStart(2,'0');
                if (secondsLeft <= 0) { clearInterval(timer); window.location.reload(); }
                secondsLeft--;
            }
            tick();
            const timer = setInterval(tick, 1000);
        }

        // Laravel alerts
        @if ($errors->any())
            window.addEventListener('DOMContentLoaded', () => {
                @if ($errors->has('email') || $errors->has('password'))
                    // Inline errors shown via Tailwind
                @else
                    showAlert('error', 'Login Gagal', '{{ $errors->first() }}');
                @endif
            });
        @endif
        @if (session('success'))
            window.addEventListener('DOMContentLoaded', () => showAlert('success', 'Berhasil!', '{{ session("success") }}'));
        @endif
        @if (session('error'))
            window.addEventListener('DOMContentLoaded', () => showAlert('error', 'Gagal!', '{{ session("error") }}'));
        @endif
        @if (session('error_google'))
            window.addEventListener('DOMContentLoaded', () => showAlert('warning', 'Akun Tidak Ditemukan', 'Email atau kata sandi tidak cocok.'));
        @endif
    </script>
</body>
</html>
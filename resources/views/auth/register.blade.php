<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CuanFlow - Daftar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@700,500,400&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('assets/image/logo.svg') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Satoshi', sans-serif;
        }
        
        /* Global Page Loader */
        .global-page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, visibility 0.2s ease;
            will-change: opacity, visibility;
            pointer-events: none;
        }
        
        .global-page-loader.active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }
        
        .global-loader-asterisk {
            width: 60px;
            height: 60px;
            animation: spin 1s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite;
            will-change: transform;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg) scale(1); }
            50% { transform: rotate(180deg) scale(1.15); }
            100% { transform: rotate(360deg) scale(1); }
        }
        
        .global-loader-dots {
            display: flex;
            gap: 6px;
            margin-top: 16px;
        }
        
        .global-loader-dot {
            width: 6px;
            height: 6px;
            background: #31694E;
            border-radius: 50%;
            animation: pulse 1.2s ease-in-out infinite;
            will-change: transform, opacity;
        }
        
        .global-loader-dot:nth-child(2) { animation-delay: 0.15s; }
        .global-loader-dot:nth-child(3) { animation-delay: 0.3s; }
        
        @keyframes pulse {
            0%, 100% { transform: scale(0.8); opacity: 0.5; }
            50% { transform: scale(1.2); opacity: 1; }
        }
        
        .global-loader-text {
            color: #31694E;
            font-size: 16px;
            font-weight: 600;
            margin-top: 12px;
            animation: fadeInOut 1.5s ease-in-out infinite;
        }
        
        @keyframes fadeInOut {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(50px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.8); }
            to { opacity: 1; transform: scale(1); }
        }
        
        @keyframes fadeOutLeft {
            from { opacity: 1; transform: translateX(0); }
            to { opacity: 0; transform: translateX(-50px); }
        }
        
        @keyframes fadeOutRight {
            from { opacity: 1; transform: translateX(0); }
            to { opacity: 0; transform: translateX(50px); }
        }
        
        .animate-fade-in-up { animation: fadeInUp 0.8s ease-out forwards; }
        .animate-fade-in-left { animation: fadeInLeft 0.8s ease-out forwards; }
        .animate-fade-in-right { animation: fadeInRight 0.8s ease-out forwards; }
        .animate-scale-in { animation: scaleIn 0.6s ease-out forwards; }
        
        .page-exit .left-section { animation: fadeOutLeft 0.6s ease-in forwards; }
        .page-exit .right-section { animation: fadeOutRight 0.6s ease-in forwards; }
        
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        
        .animate-on-load { opacity: 0; }
        
        /* Interactive Asterisk */
        #asterisk-icon {
            transition: transform 0.05s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            will-change: transform;
        }
        
        #asterisk-container { cursor: pointer; }
        
        /* Button & Input Styles */
        .btn-hover { transition: all 0.3s ease; }
        .btn-hover:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15); }
        .btn-hover:active { transform: translateY(0); }
        
        input { transition: all 0.3s ease; }
        input:focus { transform: translateY(-2px); }
        
        /* Custom SweetAlert2 */
        .swal2-popup {
            font-family: 'Satoshi', sans-serif !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
        }
        .swal2-title { color: #1f2937 !important; font-size: 24px !important; font-weight: 700 !important; }
        .swal2-html-container { color: #6b7280 !important; font-size: 14px !important; }
        .swal2-confirm {
            background-color: #31694E !important;
            color: #ffffff !important;
            border-radius: 8px !important;
            padding: 12px 32px !important;
            font-weight: 600 !important;
            font-size: 15px !important;
            box-shadow: 0 4px 12px rgba(49, 105, 78, 0.3) !important;
            transition: all 0.3s ease !important;
        }
        .swal2-confirm:hover {
            background-color: #658C58 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 16px rgba(49, 105, 78, 0.4) !important;
        }
        .swal2-icon.swal2-warning { border-color: #BBC863 !important; color: #BBC863 !important; }
        .swal2-icon.swal2-error { border-color: #ef4444 !important; color: #dc2626 !important; }
        .swal2-container { z-index: 10000 !important; }
        .swal2-backdrop-show { background: rgba(0, 0, 0, 0.6) !important; }
        
        /* Progress Steps */
        .step-dot { transition: all 0.3s ease; }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'cuan-yellow': '#F0E491',
                        'cuan-olive': '#BBC863',
                        'cuan-green': '#658C58',
                        'cuan-dark': '#31694E',
                    }
                }
            }
        }
    </script>
</head>
<body class="h-screen flex overflow-hidden">
    
    <!-- Global Page Loader -->
    <div id="global-page-loader" class="global-page-loader active">
        <svg class="global-loader-asterisk" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M40 10V70M10 40H70M20 20L60 60M60 20L20 60" stroke="#31694E" stroke-width="8" stroke-linecap="round"/>
        </svg>
        <div class="global-loader-dots">
            <div class="global-loader-dot"></div>
            <div class="global-loader-dot"></div>
            <div class="global-loader-dot"></div>
        </div>
        <p class="global-loader-text">Loading...</p>
    </div>
    
    <!-- Left Section - Green Background -->
    <div class="left-section animate-on-load animate-fade-in-left hidden lg:flex lg:w-1/2 bg-cuan-dark text-white px-12 py-10 flex-col justify-between relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0">
            <div class="absolute top-24 right-24 w-80 h-80 border-2 border-white opacity-10 rotate-12 rounded-3xl"></div>
            <div class="absolute top-40 right-12 w-64 h-64 border-2 border-white opacity-10 rotate-12 rounded-3xl"></div>
            <div class="absolute top-56 right-0 w-48 h-48 border-2 border-white opacity-10 rotate-12 rounded-3xl"></div>
        </div>
        
        <!-- Asterisk Icon -->
        <div class="relative z-10 animate-on-load animate-scale-in delay-200">
            <div id="asterisk-container" class="inline-block w-32 h-32 flex items-center justify-center">
                <svg id="asterisk-icon" width="70" height="70" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M40 10V70M10 40H70M20 20L60 60M60 20L20 60" stroke="#F0E491" stroke-width="8" stroke-linecap="round"/>
                </svg>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="relative z-10 mb-8 animate-on-load animate-fade-in-up delay-300">
            <h1 class="text-5xl font-bold mb-5 leading-tight">
                Bergabung<br/>Sekarang!
            </h1>
            <p class="text-lg max-w-md leading-relaxed text-cuan-yellow">
                Mulai perjalanan bisnismu dengan langkah yang tepat. Daftarkan dirimu dan rasakan kemudahan mengelola usaha bersama <i>CuanFlow</i>.
            </p>
        </div>
        
        <!-- Footer -->
        <p class="relative z-10 text-sm opacity-70 animate-on-load animate-fade-in-up delay-400">© 2025 CuanFlow. All rights reserved.</p>
    </div>
    
    <!-- Right Section - Register Form -->
    <div class="right-section animate-on-load animate-fade-in-right w-full lg:w-1/2 bg-white flex items-center justify-center px-8 py-10" x-data="registerWizard()">
        <div class="w-full max-w-md">
            
             <!-- Mobile Logo -->
             <div class="block lg:hidden mb-6 text-center animate-on-load animate-scale-in delay-100">
                <img src="{{ asset('assets/image/full-logo.svg') }}" alt="Logo" class="h-10 mx-auto"/>
            </div>

            <!-- Header
            <div class="hidden lg:block mb-8 animate-on-load animate-scale-in delay-100">
                <img src="{{ asset('assets/image/full-logo.svg') }}" alt="Logo" class="w-full max-w-[150px] h-auto"/>
            </div>
             -->
            <div class="mb-8">
                <h3 class="text-3xl font-bold text-gray-900 mb-2 animate-on-load animate-fade-in-up delay-200">Buat Akun</h3>
                
                <p class="text-gray-600 text-sm animate-on-load animate-fade-in-up delay-300">
                    Sudah punya akun? 
                    <a href="{{ route('login') }}" class="login-link text-gray-900 font-semibold underline hover:text-cuan-green transition-colors">Masuk disini.</a>
                </p>
            </div>

            <!-- Progress Indicator -->
            <div class="flex items-center mb-8 animate-on-load animate-fade-in-up delay-300">
                <div class="flex items-center w-full relative">
                    <!-- Step 1 -->
                    <div class="relative z-10">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300"
                             :class="step >= 1 ? 'bg-cuan-dark text-white ring-4 ring-cuan-dark/20' : 'bg-gray-100 text-gray-400'">
                            1
                        </div>
                        <div class="absolute -bottom-6 left-1/2 transform -translate-x-1/2 text-[10px] font-medium text-gray-500 whitespace-nowrap">Data Diri</div>
                    </div>
                    
                    <!-- Line 1-2 -->
                    <div class="flex-1 h-0.5 mx-2 bg-gray-100">
                        <div class="h-full bg-cuan-dark transition-all duration-500" :style="`width: ${step >= 2 ? '100%' : '0%'}`"></div>
                    </div>
                    
                    <!-- Step 2 -->
                    <div class="relative z-10">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300"
                             :class="step >= 2 ? 'bg-cuan-dark text-white ring-4 ring-cuan-dark/20' : 'bg-gray-100 text-gray-400'">
                            2
                        </div>
                         <div class="absolute -bottom-6 left-1/2 transform -translate-x-1/2 text-[10px] font-medium text-gray-500 whitespace-nowrap">Keamanan</div>
                    </div>
                </div>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg animate-fade-in-up">
                    <ul class="text-sm text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" x-ref="form" class="animate-on-load animate-fade-in-up delay-400">
                @csrf
                
                <!-- STEP 1: Info Personal -->
                <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <div class="space-y-5">
                        <!-- Nama -->
                        <div>
                            <input type="text" name="name" x-model="form.name" required
                                class="w-full px-0 py-2.5 text-gray-900 border-b-2 border-gray-300 focus:border-cuan-dark outline-none placeholder-gray-500 text-base"
                                placeholder="Nama Lengkap">
                        </div>
                        
                        <!-- Email -->
                        <div>
                             <input type="email" name="email" x-model="form.email" required
                                class="w-full px-0 py-2.5 text-gray-900 border-b-2 border-gray-300 focus:border-cuan-dark outline-none placeholder-gray-500 text-base"
                                placeholder="Email">
                        </div>

                        <!-- Phone -->
                         <div>
                                 <input type="tel" name="phone" x-model="form.phone" required
                                    class="w-full px-0 py-2.5 text-gray-900 border-b-2 border-gray-300 focus:border-cuan-dark outline-none placeholder-gray-500 text-base"
                                    placeholder="Nomor Telepon">
                        </div>

                        <div class="pt-4">
                            <button type="button" @click="nextStep()"
                                class="w-full py-3.5 bg-black text-white font-semibold rounded-lg hover:bg-gray-800 transition-colors text-base btn-hover flex items-center justify-center gap-2">
                                <span>Lanjutkan</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </button>
                        </div>
                    </div>

                     <div class="my-6 flex items-center gap-3">
                        <div class="h-px bg-gray-100 flex-1"></div>
                        <span class="text-[10px] text-gray-400 font-medium uppercase tracking-wider">Atau</span>
                        <div class="h-px bg-gray-100 flex-1"></div>
                    </div>
            
                    <a href="{{ route('auth.google') }}" class="google-link w-full flex items-center justify-center gap-3 py-3.5 bg-white border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all text-base btn-hover">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        Daftar dengan Google
                    </a>
                </div>

                <!-- STEP 2: Password & Security -->
                <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0" style="display: none;">
                    
                    <div class="space-y-5">
                        <!-- Password -->
                        <div x-data="{ show: false }">
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="password" x-model="form.password"
                                    class="w-full px-0 py-2.5 pr-8 text-gray-900 border-b-2 border-gray-300 focus:border-cuan-dark outline-none placeholder-gray-500 text-base"
                                    placeholder="Password">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div x-data="{ show: false }">
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="password_confirmation" x-model="form.password_confirmation"
                                    class="w-full px-0 py-2.5 pr-8 text-gray-900 border-b-2 border-gray-300 focus:border-cuan-dark outline-none placeholder-gray-500 text-base"
                                    placeholder="Konfirmasi Password">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>

                         <!-- Terms -->
                        <div class="flex items-start gap-3 pt-2">
                             <input type="checkbox" name="terms" id="terms" required checked
                                class="mt-1 h-4 w-4 text-cuan-dark border-gray-300 rounded focus:ring-cuan-dark">
                            <label for="terms" class="text-xs text-gray-500 leading-relaxed">
                                Saya setuju dengan <a href="{{ route('legal.terms') }}" target="_blank" class="font-semibold text-gray-900 hover:text-cuan-green underline">Syarat & Ketentuan</a> serta Kebijakan Privasi
                            </label>
                        </div>

                        <div class="flex gap-4 mt-6 pt-2">
                            <button type="button" @click="step = 1"
                                class="w-1/3 py-3.5 bg-gray-100 hover:bg-gray-200 text-gray-900 font-semibold rounded-lg transition-colors text-base btn-hover">
                                Kembali
                            </button>
                            <button type="button" @click="submitForm()"
                                class="w-2/3 py-3.5 bg-black text-white font-semibold rounded-lg hover:bg-gray-800 transition-colors text-base btn-hover shadow-lg">
                                Daftar Sekarang
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
    
    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
         // Custom SweetAlert2
         const Toast = Swal.mixin({
            customClass: {
                popup: 'swal2-popup',
                title: 'swal2-title',
                htmlContainer: 'swal2-html-container',
                confirmButton: 'swal2-confirm'
            },
            buttonsStyling: false,
            heightAuto: false,
            scrollbarPadding: false
        });

        function showAlert(icon, title, text) {
            return Toast.fire({ icon: icon, title: title, text: text, confirmButtonText: 'Mengerti' });
        }

        // Asterisk Animation
        const asteriskContainer = document.getElementById('asterisk-container');
        const asteriskIcon = document.getElementById('asterisk-icon');
        let isHovering = false;
        
        asteriskContainer.addEventListener('mouseenter', () => isHovering = true);
        asteriskContainer.addEventListener('mouseleave', () => {
            isHovering = false;
            asteriskIcon.style.transform = 'rotate(0deg)';
        });
        
        asteriskContainer.addEventListener('mousemove', (e) => {
            if (!isHovering) return;
            const rect = asteriskIcon.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;
            const angle = Math.atan2(e.clientY - centerY, e.clientX - centerX) * (180 / Math.PI);
            asteriskIcon.style.transform = `rotate(${angle + 90}deg)`;
        });

        // Page Loader
        const loader = document.getElementById('global-page-loader');
        let isNavigating = false;

        window.addEventListener('load', () => {
            setTimeout(() => {
                loader.classList.remove('active');
                document.body.classList.remove('overflow-hidden');
                document.body.classList.add('overflow-auto');
            }, 300);
        });

        function exitPage(url) {
            if (isNavigating) return;
            isNavigating = true;
            loader.classList.add('active');
            document.body.classList.remove('overflow-auto');
            document.body.classList.add('overflow-hidden');
            
            setTimeout(() => document.body.classList.add('page-exit'), 300);
            setTimeout(() => window.location.href = url, 800);
        }
        
        document.querySelectorAll('.login-link, .google-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                exitPage(link.getAttribute('href'));
            });
        });

        // Wizard Logic
        function registerWizard() {
            return {
                step: 1,
                form: {
                    name: '{{ old('name') }}',
                    email: '{{ old('email') }}',
                    phone: '{{ old('phone') }}',
                    password: '',
                    password_confirmation: ''
                },
                nextStep() {
                    if (!this.form.name || !this.form.email || !this.form.phone) {
                        showAlert('warning', 'Mohon Lengkapi Data', 'Silakan isi nama, email, dan nomor telepon.');
                        return;
                    }
                    this.step = 2;
                },
                submitForm() {
                     if (!this.form.password || !this.form.password_confirmation) {
                        showAlert('warning', 'Password Kosong', 'Silakan isi password dan konfirmasi password.');
                        return;
                    }
                    if (this.form.password !== this.form.password_confirmation) {
                        showAlert('error', 'Password Tidak Sama', 'Konfirmasi password tidak cocok.');
                        return;
                    }
                    
                    isNavigating = true;
                    loader.classList.add('active');
                    document.body.classList.remove('overflow-auto');
                    document.body.classList.add('overflow-hidden');
                    setTimeout(() => document.body.classList.add('page-exit'), 300);
                    
                    this.$refs.form.submit();
                }
            }
        }
    </script>
</body>
</html>
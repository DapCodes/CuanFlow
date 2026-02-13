<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CuanFlow - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@700,500,400&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('assets/image/logo.svg') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Satoshi', sans-serif;
        }
        
        /* Global Page Loader from app.blade.php */
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
        
        /* Page Load Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        /* Page Exit Animation */
        @keyframes fadeOutLeft {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(-50px);
            }
        }
        
        @keyframes fadeOutRight {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(50px);
            }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }
        
        .animate-fade-in-left {
            animation: fadeInLeft 0.8s ease-out forwards;
        }
        
        .animate-fade-in-right {
            animation: fadeInRight 0.8s ease-out forwards;
        }
        
        .animate-scale-in {
            animation: scaleIn 0.6s ease-out forwards;
        }
        
        .page-exit .left-section {
            animation: fadeOutLeft 0.6s ease-in forwards;
        }
        
        .page-exit .right-section {
            animation: fadeOutRight 0.6s ease-in forwards;
        }
        
        /* Stagger animation delays */
        .delay-100 {
            animation-delay: 0.1s;
        }
        
        .delay-200 {
            animation-delay: 0.2s;
        }
        
        .delay-300 {
            animation-delay: 0.3s;
        }
        
        .delay-400 {
            animation-delay: 0.4s;
        }
        
        /* Initial hidden state */
        .animate-on-load {
            opacity: 0;
        }
        
        /* Interactive Asterisk - Ultra Smooth */
        #asterisk-icon {
            transition: transform 0.05s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            will-change: transform;
        }
        
        #asterisk-container {
            cursor: pointer;
        }
        
        /* Smooth button hover */
        .btn-hover {
            transition: all 0.3s ease;
        }
        
        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
        
        .btn-hover:active {
            transform: translateY(0);
        }
        
        /* Input focus animation */
        input {
            transition: all 0.3s ease;
        }
        
        input:focus {
            transform: translateY(-2px);
        }
        
        /* Custom SweetAlert2 Styles */
               /* Custom SweetAlert2 Styles */
        .swal2-popup {
            font-family: 'Satoshi', sans-serif !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
        }
        
        .swal2-title {
            color: #1f2937 !important;
            font-size: 24px !important;
            font-weight: 700 !important;
        }
        
        .swal2-html-container {
            color: #6b7280 !important;
            font-size: 14px !important;
        }
        
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
        
        .swal2-confirm:focus {
            box-shadow: 0 0 0 3px rgba(49, 105, 78, 0.3) !important;
        }
        
        .swal2-icon.swal2-warning {
            border-color: #BBC863 !important;
            color: #BBC863 !important;
        }
        
        .swal2-icon.swal2-error {
            border-color: #ef4444 !important;
            color: #dc2626 !important;
        }
        
        /* Fix modal backdrop z-index */
        .swal2-container {
            z-index: 10000 !important;
        }
        
        .swal2-backdrop-show {
            background: rgba(0, 0, 0, 0.6) !important;
        }
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
        
        <!-- Asterisk Icon with Interactive Area -->
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
                Hello<br/>SobatCuan!
            </h1>
            <p class="text-lg max-w-md leading-relaxed text-cuan-yellow">
               Atur dan pantau perkembangan bisnismu tanpa ribet bersama <i>CuanFlow</i>. Semua proses menjadi lebih cepat, lebih teratur, dan pastinya lebih nyaman untuk dikerjakan kapan pun kamu butuh.
            </p>
        </div>
        
        <!-- Footer -->
        <p class="relative z-10 text-sm opacity-70 animate-on-load animate-fade-in-up delay-400">© 2025 CuanFlow. All rights reserved.</p>
    </div>
    
    <!-- Right Section - Login Form -->
    <div class="right-section animate-on-load animate-fade-in-right w-full lg:w-1/2 bg-white flex items-center justify-center px-8 py-10">
        <div class="w-full max-w-md">
            <!-- Header -->
            <div class="mb-6 animate-on-load animate-scale-in delay-100">
                <img 
                    src="{{ asset('assets/image/full-logo.svg') }}" 
                    alt="Logo"
                    class="w-full max-w-[180px] h-auto"
                />
            </div>
            
            <h3 class="text-3xl font-bold text-gray-900 mb-2 animate-on-load animate-fade-in-up delay-200">Selamat Datang!</h3>
            
            <p class="text-gray-600 mb-8 text-sm animate-on-load animate-fade-in-up delay-300">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="register-link text-gray-900 font-semibold underline hover:text-cuan-green transition-colors">Buat akun sekarang!.</a>
                <br/>Ini GRATIS, gunakan waktumu!
            </p>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg animate-fade-in-up">
                    <ul class="text-sm text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Success Messages -->
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg animate-fade-in-up">
                    <p class="text-sm text-green-600">{{ session('success') }}</p>
                </div>
            @endif
            
            @if ($lockoutSeconds > 0)
                <div id="lockout-container" class="space-y-6 animate-fade-in-up">
                    <div class="p-6 bg-red-50 border border-red-100 rounded-2xl text-center">
                        <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-lock text-2xl"></i>
                        </div>
                        <h4 class="text-lg font-bold text-red-900 mb-2">Batas login harian anda sudah habis</h4>
                        <p class="text-sm text-red-700 leading-relaxed">
                            Terlalu banyak percobaan masuk yang gagal. Untuk keamanan akun Anda, akses masuk dibatasi sementara.
                        </p>
                    </div>

                    <div class="text-center space-y-2">
                        <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Tersisa Waktu Tunggu</p>
                        <div id="countdown" class="text-4xl font-black text-gray-900 font-mono tracking-tighter">
                            00:00:00
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-3">
                        <a href="{{ route('auth.google') }}" 
                           class="google-link w-full flex items-center justify-center gap-3 py-3.5 bg-white border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all text-base btn-hover">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                            </svg>
                            Login dengan Google
                        </a>

                        <a href="https://wa.me/6281221049828" target="_blank"
                           class="w-full flex items-center justify-center gap-3 py-3.5 bg-emerald-500 text-white font-semibold rounded-lg hover:bg-emerald-600 transition-all text-base btn-hover">
                            <i class="fab fa-whatsapp text-xl"></i>
                            Hubungi Admin
                        </a>
                    </div>
                </div>
            @else
                <!-- Login Form -->
                <form id="login-form" method="POST" action="{{ route('login') }}" class="space-y-5 animate-on-load animate-fade-in-up delay-400">
                    @csrf
                    
                    <!-- Email Input -->
                    <div>
                        <input 
                            type="email" 
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="email@example.com"
                            required
                            autofocus
                            class="w-full px-0 py-2.5 text-gray-900 border-b-2 border-gray-300 focus:border-cuan-dark outline-none placeholder-gray-500 text-base @error('email') border-red-500 @enderror"
                        />
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Password Input -->
                    <div x-data="{ show: false }">
                        <div class="relative">
                            <input 
                                :type="show ? 'text' : 'password'" 
                                name="password"
                                placeholder="Password"
                                required
                                class="w-full px-0 py-2.5 pr-8 text-gray-900 border-b-2 border-gray-300 focus:border-cuan-dark outline-none placeholder-gray-500 text-base @error('password') border-red-500 @enderror"
                            />
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    name="remember" 
                                    id="remember"
                                    class="w-4 h-4 text-cuan-dark border-gray-300 rounded focus:ring-cuan-dark"
                                />
                                <label for="remember" class="ml-2 text-sm text-gray-600">Ingat Saya</label>
                            </div>

                            <p class="text-center text-gray-600 text-sm pt-2">
                                Lupa Password?
                                <a href="{{ route('password.request') }}" class="forgot-link text-gray-900 font-semibold underline hover:text-cuan-green transition-colors">Klik Disini</a>
                            </p>
                    </div>
                    
                    <!-- Login Button -->
                    <button 
                        type="submit"
                        class="w-full py-3.5 bg-black text-white font-semibold rounded-lg hover:bg-gray-800 transition-colors text-base mt-6 btn-hover"
                    >
                        Masuk
                    </button>

                </form>

                <!-- Divider -->
                <div class="flex items-center my-6 animate-on-load animate-fade-in-up delay-400">
                    <div class="flex-1 border-t border-gray-300"></div>
                    <span class="px-4 text-sm text-gray-500">atau</span>
                    <div class="flex-1 border-t border-gray-300"></div>
                </div>

                <!-- Google Sign In Button -->
                <a href="{{ route('auth.google') }}" 
                   class="google-link w-full flex items-center justify-center gap-3 py-3.5 bg-white border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all text-base btn-hover animate-on-load animate-fade-in-up delay-400">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    Lanjutkan dengan Google
                </a>
            @endif
        </div>
        </div>
    </div>
    
    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        // Custom SweetAlert2 Configuration
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

        // Show custom alert
        function showAlert(icon, title, text) {
            return Toast.fire({
                icon: icon,
                title: title,
                text: text,
                confirmButtonText: 'Mengerti'
            });
        }
        
        // Ultra Smooth Interactive Asterisk - Direct cursor following
        const asteriskContainer = document.getElementById('asterisk-container');
        const asteriskIcon = document.getElementById('asterisk-icon');
        let isHovering = false;
        
        asteriskContainer.addEventListener('mouseenter', () => {
            isHovering = true;
        });
        
        asteriskContainer.addEventListener('mouseleave', () => {
            isHovering = false;
            // Smooth return to 0 rotation
            asteriskIcon.style.transform = 'rotate(0deg)';
        });
        
        asteriskContainer.addEventListener('mousemove', (e) => {
            if (!isHovering) return;
            
            const rect = asteriskIcon.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;
            
            const deltaX = e.clientX - centerX;
            const deltaY = e.clientY - centerY;
            
            // Calculate angle in degrees - langsung apply tanpa interpolasi untuk ultra smooth
            const angle = Math.atan2(deltaY, deltaX) * (180 / Math.PI);
            const rotation = angle + 90; // +90 to align properly
            
            asteriskIcon.style.transform = `rotate(${rotation}deg)`;
        });
        
        // Page Loader Logic
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
            
            setTimeout(() => {
                document.body.classList.add('page-exit');
            }, 300);
            
            setTimeout(() => {
                window.location.href = url;
            }, 800);
        }
        
        // Handle navigation links with animation
        document.querySelectorAll('.register-link, .forgot-link, .google-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const url = link.getAttribute('href');
                exitPage(url);
            });
        });
        
        // Handle form submission with animation (safely check if form exists)
        const loginForm = document.getElementById('login-form');
        if (loginForm) {
            loginForm.addEventListener('submit', (e) => {
                if (isNavigating) {
                    e.preventDefault();
                    return;
                }
                
                const email = document.querySelector('input[name="email"]').value;
                const password = document.querySelector('input[name="password"]').value;
                
                if (!email || !password) {
                    e.preventDefault();
                    showAlert('warning', 'Oops!', 'Mohon lengkapi email dan password');
                    return;
                }
                
                isNavigating = true;
                loader.classList.add('active');
                document.body.classList.remove('overflow-auto');
                document.body.classList.add('overflow-hidden');
                
                setTimeout(() => {
                    document.body.classList.add('page-exit');
                }, 300);
            });
        }

        // Lockout Countdown Logic
        const countdownElement = document.getElementById('countdown');
        if (countdownElement) {
            let secondsLeft = {{ $lockoutSeconds }};
            
            function updateCountdown() {
                const hours = Math.floor(secondsLeft / 3600);
                const minutes = Math.floor((secondsLeft % 3600) / 60);
                const seconds = secondsLeft % 60;
                
                countdownElement.textContent = 
                    String(hours).padStart(2, '0') + ':' + 
                    String(minutes).padStart(2, '0') + ':' + 
                    String(seconds).padStart(2, '0');
                
                if (secondsLeft <= 0) {
                    clearInterval(timerInterval);
                    window.location.reload();
                }
                
                secondsLeft--;
            }
            
            updateCountdown();
            const timerInterval = setInterval(updateCountdown, 1000);
        }
        
        // Auto show alert if there are Laravel validation errors
        @if ($errors->any())
            window.addEventListener('DOMContentLoaded', function() {
                showAlert('error', 'Login Gagal', '{{ $errors->first() }}');
            });
        @endif
        
        // Auto show alert if there is success message
        @if (session('success'))
            window.addEventListener('DOMContentLoaded', function() {
                showAlert('success', 'Berhasil!', '{{ session('success') }}');
            });
        @endif

        // Auto show alert if there is error message
        @if (session('error'))
            window.addEventListener('DOMContentLoaded', function() {
                showAlert('error', 'Gagal!', '{{ session('error') }}');
            });
        @endif

        // Auto show alert if google account not found
        @if (session('error_google'))
            window.addEventListener('DOMContentLoaded', function() {
                showAlert('warning', 'Akun Tidak Ditemukan', '{{ session('error_google') }}');
            });
        @endif
    </script>
    
</body>
</html>
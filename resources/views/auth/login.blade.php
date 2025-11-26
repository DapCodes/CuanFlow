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
        
        /* Custom Loader */
        .loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #31694E;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        
        .loader-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        /* Spinning Asterisk Loader */
        .loader-asterisk {
            width: 80px;
            height: 80px;
            animation: spin 1.5s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite;
        }
        
        @keyframes spin {
            0% {
                transform: rotate(0deg) scale(1);
            }
            50% {
                transform: rotate(180deg) scale(1.2);
            }
            100% {
                transform: rotate(360deg) scale(1);
            }
        }
        
        /* Pulsing dots */
        .loader-dots {
            display: flex;
            gap: 8px;
            margin-top: 20px;
        }
        
        .loader-dot {
            width: 8px;
            height: 8px;
            background: #F0E491;
            border-radius: 50%;
            animation: pulse 1.4s ease-in-out infinite;
        }
        
        .loader-dot:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .loader-dot:nth-child(3) {
            animation-delay: 0.4s;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(0.8);
                opacity: 0.5;
            }
            50% {
                transform: scale(1.2);
                opacity: 1;
            }
        }
        
        /* Loading text */
        .loader-text {
            color: #F0E491;
            font-size: 18px;
            font-weight: 600;
            margin-top: 16px;
            animation: fadeInOut 2s ease-in-out infinite;
        }
        
        @keyframes fadeInOut {
            0%, 100% {
                opacity: 0.5;
            }
            50% {
                opacity: 1;
            }
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
    
    <!-- Custom Loader -->
    <div id="page-loader" class="loader-overlay">
        <svg class="loader-asterisk" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M40 10V70M10 40H70M20 20L60 60M60 20L20 60" stroke="#F0E491" stroke-width="8" stroke-linecap="round"/>
        </svg>
        <div class="loader-dots">
            <div class="loader-dot"></div>
            <div class="loader-dot"></div>
            <div class="loader-dot"></div>
        </div>
        <p class="loader-text">Loading...</p>
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
                Hello<br/>SobatCuan!👋
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
                <div>
                    <input 
                        type="password" 
                        name="password"
                        placeholder="Password"
                        required
                        class="w-full px-0 py-2.5 text-gray-900 border-b-2 border-gray-300 focus:border-cuan-dark outline-none placeholder-gray-500 text-base @error('password') border-red-500 @enderror"
                    />
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
        </div>
    </div>
    
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
        
        // Page Exit Animation Function
        function exitPage(url) {
            // Show loader first
            const loader = document.getElementById('page-loader');
            loader.classList.add('active');
            
            // Start page exit animation after a brief moment
            setTimeout(() => {
                document.body.classList.add('page-exit');
            }, 300);
            
            // Navigate after loader duration (1 second total)
            setTimeout(() => {
                window.location.href = url;
            }, 1000);
        }
        
        // Handle navigation links with animation
        document.querySelectorAll('.register-link, .forgot-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const url = e.target.getAttribute('href');
                exitPage(url);
            });
        });
        
        // Handle form submission with animation
        document.getElementById('login-form').addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Basic client-side validation
            const email = document.querySelector('input[name="email"]').value;
            const password = document.querySelector('input[name="password"]').value;
            
            if (!email || !password) {
                showAlert('warning', 'Oops!', 'Mohon lengkapi email dan password');
                return;
            }
            
            // Show loader
            const loader = document.getElementById('page-loader');
            loader.classList.add('active');
            
            // Start page exit animation
            setTimeout(() => {
                document.body.classList.add('page-exit');
            }, 300);
            
            // Submit form after loader
            setTimeout(() => {
                e.target.submit();
            }, 1000);
        });
        
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
    </script>
    
</body>
</html>
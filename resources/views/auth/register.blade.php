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
        
        /* Page Load Animations */
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
        
        /* Step Transition Animations */
        @keyframes slideOutLeft {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(-40px);
            }
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(40px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes slideOutRight {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(40px);
            }
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-40px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        /* Page Exit Animations */
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
        
        /* Progress Animation */
        @keyframes progressPulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }
        
        @keyframes lineGrow {
            from {
                width: 0%;
            }
            to {
                width: 100%;
            }
        }
        
        /* Initial States */
        .animate-on-load {
            opacity: 0;
        }
        
        /* Applied Animations */
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }
        
        .animate-fade-in-left {
            animation: fadeInLeft 0.6s ease-out forwards;
        }
        
        .animate-fade-in-right {
            animation: fadeInRight 0.6s ease-out forwards;
        }
        
        .animate-scale-in {
            animation: scaleIn 0.5s ease-out forwards;
        }
        
        .animate-slide-in-right {
            animation: slideInRight 0.5s ease-out forwards;
        }
        
        .animate-slide-in-left {
            animation: slideInLeft 0.5s ease-out forwards;
        }
        
        .animate-slide-out-left {
            animation: slideOutLeft 0.35s ease-out forwards;
        }
        
        .animate-slide-out-right {
            animation: slideOutRight 0.35s ease-out forwards;
        }
        
        .page-exit .left-section {
            animation: fadeOutLeft 0.4s ease-out forwards;
        }
        
        .page-exit .right-section {
            animation: fadeOutRight 0.4s ease-out forwards;
        }
        
        /* Delays */
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }
        .delay-600 { animation-delay: 0.6s; }
        
        /* Progress Step Styles */
        .progress-step {
            transition: all 0.3s ease-out;
        }
        
        .progress-step.active {
            background: #31694E;
            color: white;
        }
        
        .progress-step.completed {
            background: #BBC863;
            color: white;
            transform: scale(1);
        }
        
        .progress-line {
            transition: background-color 0.3s ease-out;
            position: relative;
            overflow: hidden;
        }
        
        .progress-line.active::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            background: #BBC863;
            animation: lineGrow 0.4s ease-out forwards;
        }
        
        /* Interactive Asterisk - Ultra Smooth */
        #asterisk-icon {
            transition: transform 0.08s ease-out;
            cursor: pointer;
            will-change: transform;
        }
        
        #asterisk-container {
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
        }
        
        /* Button Hover */
        .btn-hover {
            transition: all 0.3s ease-out;
        }
        
        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }
        
        .btn-hover:active {
            transform: translateY(0);
            transition: all 0.1s ease;
        }
        
        /* Input Focus */
        input, textarea, select {
            transition: all 0.3s ease-out;
        }
        
        input:focus, textarea:focus, select:focus {
            transform: translateY(-2px);
        }
        
        /* Form Field Animation */
        .form-field {
            opacity: 0;
            animation: fadeInUp 0.4s ease-out forwards;
        }
        
        .form-field:nth-child(1) { animation-delay: 0.05s; }
        .form-field:nth-child(2) { animation-delay: 0.1s; }
        .form-field:nth-child(3) { animation-delay: 0.15s; }
        .form-field:nth-child(4) { animation-delay: 0.2s; }
        .form-field:nth-child(5) { animation-delay: 0.25s; }
        .form-field:nth-child(6) { animation-delay: 0.3s; }
        
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
<body class="h-screen flex overflow-hidden bg-white">
    
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
    
    <!-- Left Section - Simple Background -->
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
                Bergabung dengan<br/>CuanFlow!
            </h1>
            <p class="text-lg max-w-md leading-relaxed text-cuan-yellow">
                Platform all-in-one untuk mengelola bisnis Anda dengan lebih efisien dan profesional. Mulai kelola bisnis dengan mudah hari ini.
            </p>
        </div>
        
        <!-- Footer -->
        <p class="relative z-10 text-sm opacity-70 animate-on-load animate-fade-in-up delay-400">© 2025 CuanFlow. All rights reserved.</p>
    </div>
    
    <!-- Right Section - Registration Form -->
    <div class="right-section animate-on-load animate-fade-in-right w-full lg:w-1/2 bg-white flex items-center justify-center px-6 sm:px-8 py-10 overflow-y-auto">
        <div class="w-full max-w-md">
            <!-- Header -->
            <div class="mb-6 animate-on-load animate-scale-in delay-100">
                <img 
                    src="{{ asset('assets/image/full-logo.svg') }}" 
                    alt="Logo"
                    class="w-full max-w-[180px] h-auto"
                />
            </div>
            
            <h3 class="text-3xl font-bold text-gray-900 mb-2 animate-on-load animate-fade-in-up delay-200">Daftar Sekarang</h3>
            
            <p class="text-gray-600 mb-8 text-sm animate-on-load animate-fade-in-up delay-300">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="login-link text-gray-900 font-semibold underline hover:text-cuan-green transition-colors">Login di sini</a>
            </p>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg animate-fade-in-up">
                    <ul class="text-sm text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
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

            <!-- Progress Indicator -->
            <div class="flex items-center justify-center mb-8 space-x-2 animate-on-load animate-fade-in-up delay-400">
                <div id="progress-1" class="progress-step active w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">
                    1
                </div>
                <div id="progress-line-1" class="progress-line w-12 h-1 bg-gray-200"></div>
                <div id="progress-2" class="progress-step w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold bg-gray-200 text-gray-500">
                    2
                </div>
                <div id="progress-line-2" class="progress-line w-12 h-1 bg-gray-200"></div>
                <div id="progress-3" class="progress-step w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold bg-gray-200 text-gray-500">
                    ✓
                </div>
            </div>
            
            <!-- Registration Form -->
            <form method="POST" action="{{ route('register') }}" class="space-y-5 animate-on-load animate-fade-in-up delay-500" id="registerForm">
                @csrf
                
                <!-- Step 1: Personal Information -->
                <div id="step1">
                    <p class="text-xs font-semibold text-gray-500 mb-4 uppercase tracking-wide form-field">
                        Informasi Pribadi
                    </p>
                    
                    <!-- Name Input -->
                    <div class="mb-5 form-field">
                        <input 
                            type="text" 
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Nama Lengkap"
                            required
                            autofocus
                            class="w-full px-0 py-2.5 text-gray-900 border-b-2 border-gray-300 focus:border-cuan-dark outline-none placeholder-gray-500 text-base @error('name') border-red-500 @enderror"
                        />
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Email Input -->
                    <div class="mb-5 form-field">
                        <input 
                            type="email" 
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Email"
                            required
                            class="w-full px-0 py-2.5 text-gray-900 border-b-2 border-gray-300 focus:border-cuan-dark outline-none placeholder-gray-500 text-base @error('email') border-red-500 @enderror"
                        />
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Phone Input -->
                    <div class="mb-5 form-field">
                        <input 
                            type="tel" 
                            name="phone"
                            value="{{ old('phone') }}"
                            placeholder="Nomor Telepon (08xxx)"
                            required
                            class="w-full px-0 py-2.5 text-gray-900 border-b-2 border-gray-300 focus:border-cuan-dark outline-none placeholder-gray-500 text-base @error('phone') border-red-500 @enderror"
                        />
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Next Button -->
                    <button 
                        type="button"
                        onclick="goToStep2()"
                        class="w-full py-3.5 bg-black text-white font-semibold rounded-lg hover:bg-gray-800 transition-colors text-base mt-6 btn-hover form-field"
                    >
                        Lanjutkan
                    </button>
                </div>

                <!-- Step 2: Password -->
                <div id="step2" class="hidden">
                    <button 
                        type="button"
                        onclick="goToStep1()"
                        class="text-sm text-gray-600 hover:text-cuan-dark mb-4 flex items-center transition-colors"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Kembali
                    </button>
                    
                    <p class="text-xs font-semibold text-gray-500 mb-4 uppercase tracking-wide form-field">
                        Keamanan Akun
                    </p>
                    
                    <!-- Password Input -->
                    <div class="mb-5 form-field">
                        <input 
                            type="password" 
                            name="password"
                            id="password"
                            placeholder="Password (Min. 8 karakter)"
                            required
                            class="w-full px-0 py-2.5 text-gray-900 border-b-2 border-gray-300 focus:border-cuan-dark outline-none placeholder-gray-500 text-base @error('password') border-red-500 @enderror"
                        />
                        <p class="text-xs text-gray-500 mt-1">Gunakan kombinasi huruf, angka, dan simbol</p>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Confirm Password Input -->
                    <div class="mb-5 form-field">
                        <input 
                            type="password" 
                            name="password_confirmation"
                            id="password_confirmation"
                            placeholder="Konfirmasi Password"
                            required
                            class="w-full px-0 py-2.5 text-gray-900 border-b-2 border-gray-300 focus:border-cuan-dark outline-none placeholder-gray-500 text-base"
                        />
                    </div>

                    <!-- Terms & Conditions -->
                    <div class="flex items-start mb-5 form-field">
                        <input 
                            type="checkbox" 
                            name="terms" 
                            id="terms"
                            value="1"
                            required
                            class="w-4 h-4 text-cuan-dark border-gray-300 rounded focus:ring-cuan-dark mt-1 flex-shrink-0 @error('terms') border-red-500 @enderror"
                        />
                        <label for="terms" class="ml-2 text-sm text-gray-600">
                            Saya menyetujui <a href="#" class="text-gray-900 font-semibold underline hover:text-cuan-green transition-colors">Syarat & Ketentuan</a>
                        </label>
                    </div>
                    @error('terms')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    
                    <!-- Next Button -->
                    <button 
                        type="button"
                        onclick="goToStep3()"
                        class="w-full py-3.5 bg-black text-white font-semibold rounded-lg hover:bg-gray-800 transition-colors text-base mt-6 btn-hover form-field"
                    >
                        Lanjutkan
                    </button>
                </div>

                <!-- Step 3: Success Message -->
                <div id="step3" class="hidden text-center">
                    <button 
                        type="button"
                        onclick="backToStep2()"
                        class="text-sm text-gray-600 hover:text-cuan-dark mb-6 flex items-center transition-colors"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Kembali
                    </button>

                    <!-- Success Icon -->
                    <div class="mb-6 form-field flex justify-center">
                        <div class="w-20 h-20 bg-cuan-green rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Success Message -->
                    <h3 class="text-2xl font-bold text-gray-900 mb-3 form-field">
                        Akun Berhasil Dibuat!
                    </h3>
                    <p class="text-gray-600 mb-8 form-field max-w-sm mx-auto">
                        Selamat! Akun Anda telah berhasil didaftarkan. Anda siap untuk memulai perjalanan mengelola bisnis dengan CuanFlow.
                    </p>

                    <!-- Action Button -->
                    <div class="form-field">
                        <button 
                            type="submit"
                            class="w-full py-3.5 bg-black text-white font-semibold rounded-lg hover:bg-gray-800 transition-colors text-base btn-hover"
                        >
                            Daftar & Buka Dasbor Sekarang
                        </button>
                    </div>
                </div>
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
            heightAuto: false, // Added
            scrollbarPadding: false // Added
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
            asteriskIcon.style.transform = 'rotate(0deg)';
        });
        
        asteriskContainer.addEventListener('mousemove', (e) => {
            if (!isHovering) return;
            
            const rect = asteriskIcon.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;
            
            const deltaX = e.clientX - centerX;
            const deltaY = e.clientY - centerY;
            
            // Calculate angle and apply smooth rotation
            const angle = Math.atan2(deltaY, deltaX) * (180 / Math.PI);
            const rotation = angle + 90;
            
            // Use requestAnimationFrame for ultra smooth animation
            requestAnimationFrame(() => {
                asteriskIcon.style.transform = `rotate(${rotation}deg)`;
            });
        });
        
        // Step Navigation with Animations
        function goToStep2() {
            // Validate step 1 fields
            const name = document.querySelector('input[name="name"]').value;
            const email = document.querySelector('input[name="email"]').value;
            const phone = document.querySelector('input[name="phone"]').value;
            
            if (!name || !email || !phone) {
                showAlert('warning', 'Oops!', 'Mohon lengkapi semua field yang wajib diisi');
                return;
            }
            
            // Animate step 1 out
            const step1 = document.getElementById('step1');
            step1.classList.add('animate-slide-out-left');
            
            setTimeout(() => {
                step1.classList.add('hidden');
                step1.classList.remove('animate-slide-out-left');
                
                // Update progress
                document.getElementById('progress-1').classList.remove('active');
                document.getElementById('progress-1').classList.add('completed');
                document.getElementById('progress-line-1').classList.add('active');
                
                setTimeout(() => {
                    document.getElementById('progress-2').classList.add('active');
                }, 200);
                
                // Show step 2 with animation
                const step2 = document.getElementById('step2');
                step2.classList.remove('hidden');
                step2.classList.add('animate-slide-in-right');
                
                // Re-apply form field animations
                setTimeout(() => {
                    step2.querySelectorAll('.form-field').forEach((field, index) => {
                        field.style.opacity = '0';
                        setTimeout(() => {
                            field.style.animation = 'fadeInUp 0.4s ease-out forwards';
                        }, index * 50);
                    });
                }, 100);
            }, 400);
        }
        
        function goToStep3() {
            // Validate step 2 fields
            const password = document.querySelector('input[name="password"]').value;
            const passwordConfirm = document.querySelector('input[name="password_confirmation"]').value;
            const terms = document.querySelector('input[name="terms"]').checked;
            
            if (!password || !passwordConfirm) {
                showAlert('warning', 'Oops!', 'Mohon lengkapi password dan konfirmasi password');
                return;
            }
            
            if (password.length < 8) {
                showAlert('warning', 'Password Terlalu Pendek', 'Password harus minimal 8 karakter');
                return;
            }
            
            if (password !== passwordConfirm) {
                showAlert('error', 'Password Tidak Cocok', 'Password dan konfirmasi password tidak sama');
                return;
            }
            
            if (!terms) {
                showAlert('warning', 'Syarat & Ketentuan', 'Mohon setujui syarat dan ketentuan untuk melanjutkan');
                return;
            }
            
            // Animate step 2 out
            const step2 = document.getElementById('step2');
            step2.classList.add('animate-slide-out-left');
            
            setTimeout(() => {
                step2.classList.add('hidden');
                step2.classList.remove('animate-slide-out-left');
                
                // Update progress - complete step 2
                document.getElementById('progress-2').classList.remove('active');
                document.getElementById('progress-2').classList.add('completed');
                document.getElementById('progress-line-2').classList.add('active');
                
                setTimeout(() => {
                    document.getElementById('progress-3').classList.add('active');
                }, 200);
                
                // Show step 3 with animation
                const step3 = document.getElementById('step3');
                step3.classList.remove('hidden');
                step3.classList.add('animate-slide-in-right');
                
                // Re-apply form field animations
                setTimeout(() => {
                    step3.querySelectorAll('.form-field').forEach((field, index) => {
                        field.style.opacity = '0';
                        setTimeout(() => {
                            field.style.animation = 'fadeInUp 0.4s ease-out forwards';
                        }, index * 50);
                    });
                }, 100);
            }, 400);
        }
        
        function goToStep1() {
            // Animate step 2 out
            const step2 = document.getElementById('step2');
            step2.classList.add('animate-slide-out-right');
            
            setTimeout(() => {
                step2.classList.add('hidden');
                step2.classList.remove('animate-slide-out-right');
                
                // Update progress
                document.getElementById('progress-2').classList.remove('active');
                document.getElementById('progress-1').classList.add('active');
                document.getElementById('progress-1').classList.remove('completed');
                document.getElementById('progress-line-1').classList.remove('active');
                
                // Show step 1 with animation
                const step1 = document.getElementById('step1');
                step1.classList.remove('hidden');
                step1.classList.add('animate-slide-in-left');
                
                // Re-apply form field animations
                setTimeout(() => {
                    step1.querySelectorAll('.form-field').forEach((field, index) => {
                        field.style.opacity = '0';
                        setTimeout(() => {
                            field.style.animation = 'fadeInUp 0.4s ease-out forwards';
                        }, index * 50);
                    });
                }, 100);
            }, 400);
        }
        
        function backToStep2() {
            // Animate step 3 out
            const step3 = document.getElementById('step3');
            step3.classList.add('animate-slide-out-right');
            
            setTimeout(() => {
                step3.classList.add('hidden');
                step3.classList.remove('animate-slide-out-right');
                
                // Update progress - back to step 2
                document.getElementById('progress-3').classList.remove('active');
                document.getElementById('progress-2').classList.add('active');
                document.getElementById('progress-2').classList.remove('completed');
                document.getElementById('progress-line-2').classList.remove('active');
                
                // Show step 2 with animation
                const step2 = document.getElementById('step2');
                step2.classList.remove('hidden');
                step2.classList.add('animate-slide-in-left');
                
                // Re-apply form field animations
                setTimeout(() => {
                    step2.querySelectorAll('.form-field').forEach((field, index) => {
                        field.style.opacity = '0';
                        setTimeout(() => {
                            field.style.animation = 'fadeInUp 0.4s ease-out forwards';
                        }, index * 50);
                    });
                }, 100);
            }, 400);
        }
        
        // Real-time password match validation
        document.getElementById('password_confirmation')?.addEventListener('input', function() {
            const password = document.getElementById('password').value;
            if (this.value && password !== this.value) {
                this.style.borderColor = '#ef4444';
            } else {
                this.style.borderColor = '';
            }
        });

        // Check if there are validation errors and auto-navigate to step 2
        @if($errors->has('password') || $errors->has('password_confirmation') || $errors->has('terms'))
            window.addEventListener('DOMContentLoaded', function() {
                goToStep2();
            });
        @endif
        
        // Page Exit Animation
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
        
        // Handle navigation with animation
        document.querySelector('.login-link')?.addEventListener('click', (e) => {
            e.preventDefault();
            const url = e.target.getAttribute('href');
            exitPage(url);
        });
        
        // Handle form submission with animation
        document.getElementById('registerForm').addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Show loader
            const loader = document.getElementById('page-loader');
            loader.classList.add('active');
            
            // Start page exit animation
            setTimeout(() => {
                document.body.classList.add('page-exit');
            }, 300);
            
            // Submit form after loader (1 second total)
            setTimeout(() => {
                e.target.submit();
            }, 1000);
        });
    </script>
</body>
</html>
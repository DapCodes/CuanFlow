<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CuanFlow - Lengkapi Profil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@700,500,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="shortcut icon" href="{{ asset('assets/image/logo.svg') }}" type="image/x-icon">
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
        }
        
        .global-page-loader.active {
            opacity: 1;
            visibility: visible;
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
                Satu Langkah<br/>Lagi!
            </h1>
            <p class="text-lg max-w-md leading-relaxed text-cuan-yellow">
                Lengkapi profil Anda untuk mulai menggunakan CuanFlow bersama akun Google <b>{{ $googleUser['email'] }}</b>.
            </p>
        </div>
        
        <!-- Footer -->
        <p class="relative z-10 text-sm opacity-70 animate-on-load animate-fade-in-up delay-400">© 2025 CuanFlow. All rights reserved.</p>
    </div>
    
    <!-- Right Section - Profile Form -->
    <div class="right-section animate-on-load animate-fade-in-right w-full lg:w-1/2 bg-white flex items-center justify-center px-8 py-10">
        <div class="w-full max-w-md">
            
             <!-- Mobile Logo
             <div class="block lg:hidden mb-6 text-center animate-on-load animate-scale-in delay-100">
                <img src="{{ asset('assets/image/full-logo.svg') }}" alt="Logo" class="h-10 mx-auto"/>
            </div> -->

            <!-- Header -->
            <!-- <div class="hidden lg:block mb-8 animate-on-load animate-scale-in delay-100">
                <img src="{{ asset('assets/image/full-logo.svg') }}" alt="Logo" class="w-full max-w-[150px] h-auto"/>
            </div> -->

            
            <div class="mb-8">
                <!-- Debug Info (Temporary)
                @if(config('app.debug'))
                    <div class="mb-4 p-2 bg-gray-100 text-xs rounded border border-gray-300 overflow-auto max-h-40">
                        <strong>Debug Info:</strong>
                        <pre>{{ json_encode($googleUser, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                @endif -->

                <div class="flex items-center gap-4 mb-4 animate-on-load animate-fade-in-up delay-200">
                     <img src="{{ $googleUser['google_avatar'] ?? asset('assets/image/default-avatar.png') }}" alt="Avatar" class="w-16 h-16 rounded-full border-2 border-cuan-dark p-1">
                     <div>
                         <h3 class="text-2xl font-bold text-gray-900">Halo, {{ explode(' ', $googleUser['name'] ?? 'User')[0] }}!</h3>
                         <p class="text-sm text-gray-500">Lengkapi data berikut ya.</p>
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

            <form id="complete-profile-form" method="POST" action="{{ route('auth.google.store') }}" enctype="multipart/form-data" class="space-y-6 animate-on-load animate-fade-in-up delay-400">
                @csrf
                <input type="hidden" name="email" value="{{ $googleUser['email'] ?? '' }}">
                <input type="hidden" name="name" value="{{ $googleUser['name'] ?? '' }}">
                <input type="hidden" name="google_id" value="{{ $googleUser['id'] ?? $googleUser['google_id'] ?? '' }}">
                <input type="hidden" name="google_avatar" value="{{ $googleUser['google_avatar'] ?? '' }}">
                
                <!-- Avatar Upload
                <div x-data="{ 
                    photoName: null, 
                    photoPreview: {{ json_encode($googleUser['google_avatar'] ?? null) }},
                    browserPhoto: false,
                    init() {
                       if (!this.photoPreview) {
                           this.browserPhoto = true; 
                       }
                    }
                }" class="col-span-6 sm:col-span-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Avatar</label>
                    <input type="file" name="avatar" class="hidden" x-ref="photo" x-on:change="
                            photoName = $refs.photo.files[0].name;
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                photoPreview = e.target.result;
                                browserPhoto = true; 
                            };
                            reader.readAsDataURL($refs.photo.files[0]);
                    ">

                    <div class="mt-2 flex items-center gap-4">
                        Current Profile Photo
                        <div x-show="!photoPreview" class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center text-gray-400">
                           <i class="fas fa-user text-3xl"></i>
                        </div>

                        New Profile Photo Preview
                        <div x-show="photoPreview" style="display: none;">
                            <span class="block w-20 h-20 rounded-full bg-cover bg-no-repeat bg-center border-2 border-cuan-dark"
                                  :style="'background-image: url(\'' + photoPreview + '\');'">
                            </span>
                        </div>

                        <button type="button" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-cuan-dark focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150" x-on:click.prevent="$refs.photo.click()">
                            Pilih Foto Baru
                        </button>
                         <p x-show="!photoPreview && !browserPhoto" class="text-xs text-red-500 mt-1">
                            Wajib upload jika tidak ada foto dari Google
                        </p>
                    </div>
                     <p class="text-xs text-gray-500 mt-2">Format: JPG, PNG. Maks: 2MB.</p>
                </div> -->

                 <!-- Phone Input -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required autofocus
                        class="w-full px-0 py-2.5 text-gray-900 border-b-2 border-gray-300 focus:border-cuan-dark outline-none placeholder-gray-500 text-base"
                        placeholder="Contoh: 08123456789">
                </div>
                
                <!-- Password Input -->
                <div x-data="{ show: false }">
                     <label class="block text-sm font-semibold text-gray-700 mb-2">Buat Password Baru</label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" name="password" required
                            class="w-full px-0 py-2.5 pr-8 text-gray-900 border-b-2 border-gray-300 focus:border-cuan-dark outline-none placeholder-gray-500 text-base"
                            placeholder="Minimal 8 karakter">
                         <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                            <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>
                
                 <!-- Confirm Password Input -->
                <div x-data="{ show: false }">
                     <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password</label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" name="password_confirmation" required
                            class="w-full px-0 py-2.5 pr-8 text-gray-900 border-b-2 border-gray-300 focus:border-cuan-dark outline-none placeholder-gray-500 text-base"
                            placeholder="Ulangi password">
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

                <div class="pt-2">
                    <button type="submit"
                        class="w-full py-3.5 bg-black text-white font-semibold rounded-lg hover:bg-gray-800 transition-colors text-base btn-hover flex items-center justify-center gap-2">
                        <span>Selesai & Masuk</span>
                        <i class="fas fa-check text-xs"></i>
                    </button>
                </div>

            </form>
        </div>
    </div>
    
    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
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

        window.addEventListener('load', () => setTimeout(() => loader.classList.remove('active'), 300));
        
        // Failsafe: Remove loader after 3 seconds anyway
        setTimeout(() => {
            if (loader.classList.contains('active')) {
                console.warn('Failsafe: Force removing loader');
                loader.classList.remove('active');
            }
        }, 3000);

        // Form Submission
        document.getElementById('complete-profile-form').addEventListener('submit', (e) => {
            if (isNavigating) {
                e.preventDefault();
                return;
            }
            
            // Basic validation
            const phone = document.querySelector('input[name="phone"]').value;
            const password = document.querySelector('input[name="password"]').value;
            const confirm = document.querySelector('input[name="password_confirmation"]').value;
            
             if (!phone || !password || !confirm) {
                 // Let browser default validation handle empty required fields first
                 return; 
            }
            
             if (password !== confirm) {
                 e.preventDefault();
                 alert('Password tidak sama!'); // Simple alert for fallback
                 return;
             }
            
            isNavigating = true;
            loader.classList.add('active');
            setTimeout(() => document.body.classList.add('page-exit'), 300);
        });
    </script>
</body>
</html>

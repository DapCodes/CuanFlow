<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk - CuanFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@700,500,400&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('assets/image/logo.svg') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Satoshi', sans-serif;
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(12deg); }
            50% { transform: translateY(-20px) rotate(12deg); }
        }
        
        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
        .animate-scale-in { animation: scaleIn 0.5s ease-out forwards; }
        
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        
        .animate-on-load { opacity: 0; }
        
        /* Background Decorations */
        .green-blur {
            background: radial-gradient(circle at top left, rgba(49, 105, 78, 0.15) 0%, transparent 70%);
            filter: blur(60px);
        }
        
        .bg-pattern {
            animation: float 10s ease-in-out infinite;
        }
        
        .bg-pattern:nth-child(2) {
            animation-delay: 2s;
            animation-duration: 12s;
        }
        
        /* Form Elements */
        .input-box {
            transition: all 0.3s ease;
        }
        
        .input-box:focus-within {
            transform: translateY(-2px);
            border-color: #31694E;
            box-shadow: 0 4px 12px rgba(49, 105, 78, 0.08);
        }
        
        .btn-primary {
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(49, 105, 78, 0.2);
        }
        
        .btn-google {
            transition: all 0.3s ease;
        }
        
        .btn-google:hover {
            transform: translateY(-2px);
            background-color: #f9fafb;
            border-color: #d1d5db;
        }

        /* Sweet Alert */
        .swal2-popup {
            font-family: 'Satoshi', sans-serif !important;
            border-radius: 12px !important;
        }
        
        .swal2-confirm {
            background-color: #31694E !important;
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
<body class="min-h-screen bg-white relative overflow-hidden flex items-center justify-center p-4">
    
    <!-- Decorative Background -->
    <div class="green-blur absolute -top-40 -left-40 w-96 h-96 pointer-events-none"></div>
    
    <div class="absolute bottom-0 right-0 w-full h-full pointer-events-none overflow-hidden">
        <div class="bg-pattern absolute bottom-10 right-10 w-48 h-48 border-2 border-cuan-dark opacity-10 rotate-12 rounded-3xl"></div>
        <div class="bg-pattern absolute bottom-32 right-32 w-32 h-32 border-2 border-cuan-green opacity-10 rotate-12 rounded-3xl"></div>
        <div class="bg-pattern absolute bottom-20 right-52 w-24 h-24 border-2 border-cuan-olive opacity-10 rotate-12 rounded-3xl"></div>
    </div>
    
    <!-- Login Card -->
    <div class="relative z-10 w-full max-w-[420px]">
        <div class="bg-white/80 backdrop-blur-xl border border-gray-100 rounded-3xl p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] animate-on-load animate-scale-in">
            
            <!-- Logo -->
            <div class="flex justify-center mb-6 animate-on-load animate-fade-in-up delay-100">
                <img src="{{ asset('assets/image/full-logo.svg') }}" alt="CuanFlow Logo" class="h-10">
            </div>
            
            <div class="text-center mb-8 animate-on-load animate-fade-in-up delay-100">
                <h1 class="text-2xl font-bold text-gray-900">Selamat Datang!</h1>
                <p class="text-sm text-gray-500 mt-1">Masuk untuk mengelola bisnis Anda</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-5 p-3.5 bg-red-50 border border-red-100 rounded-xl animate-fade-in-up">
                    <ul class="text-xs font-medium text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-5 p-3.5 bg-green-50 border border-green-100 rounded-xl animate-fade-in-up">
                    <div class="flex items-center gap-2 text-green-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}" class="space-y-5 animate-on-load animate-fade-in-up delay-200">
                @csrf
                
                <!-- Email -->
                <div>
                    <div class="relative input-box border border-gray-200 rounded-xl bg-gray-50/50 overflow-hidden group hover:border-gray-300">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-cuan-dark transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="block w-full pl-11 pr-4 py-3 bg-transparent text-gray-900 text-sm placeholder-gray-400 focus:outline-none"
                            placeholder="Email Address">
                    </div>
                </div>

                <!-- Password -->
                <div x-data="{ show: false }">
                    <div class="relative input-box border border-gray-200 rounded-xl bg-gray-50/50 overflow-hidden group hover:border-gray-300">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-cuan-dark transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input :type="show ? 'text' : 'password'" name="password" required
                            class="block w-full pl-11 pr-11 py-3 bg-transparent text-gray-900 text-sm placeholder-gray-400 focus:outline-none"
                            placeholder="Password">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                            <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="flex items-center justify-between text-xs sm:text-sm">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <div class="relative flex items-center">
                            <input type="checkbox" name="remember" class="peer h-4 w-4 rounded border-gray-300 text-cuan-dark focus:ring-cuan-dark cursor-pointer transition-colors">
                        </div>
                        <span class="text-gray-500 group-hover:text-gray-700 transition-colors">Ingat Saya</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="font-semibold text-cuan-dark hover:text-cuan-green transition-colors">Lupa Password?</a>
                </div>

                <button type="submit" class="btn-primary w-full py-3 px-4 bg-gray-900 hover:bg-black text-white text-sm font-bold rounded-xl shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2">
                    <span>Masuk sekarang</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </button>
            </form>
            
            <div class="my-6 flex items-center gap-3 animate-on-load animate-fade-in-up delay-300">
                <div class="h-px bg-gray-200 flex-1"></div>
                <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">Atau masuk dengan</span>
                <div class="h-px bg-gray-200 flex-1"></div>
            </div>
            
            <div class="animate-on-load animate-fade-in-up delay-300">
                <a href="{{ route('auth.google') }}" class="btn-google w-full flex items-center justify-center gap-3 py-3 px-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition-all group bg-white">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    <span class="text-sm font-semibold text-gray-600 group-hover:text-gray-800">Google</span>
                </a>
            </div>
            
            <p class="mt-8 text-center text-sm text-gray-500 animate-on-load animate-fade-in-up delay-300">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="font-bold text-gray-900 hover:text-cuan-green transition-colors">Daftar Gratis</a>
            </p>
        </div>
        
        <p class="text-center text-center text-xs text-gray-400 mt-6 animate-on-load animate-fade-in-up delay-300">
            &copy; {{ date('Y') }} CuanFlow. All rights reserved.
        </p>
    </div>

    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        // SweetAlert Mixin
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        // Flash Messages
        @if ($errors->any())
        Toast.fire({
            icon: 'error',
            title: 'Gagal Masuk',
            text: '{{ $errors->first() }}'
        });
        @endif
        
        @if (session('error_google'))
        Toast.fire({
            icon: 'error',
            title: 'Gagal Login Google',
            text: '{{ session('error_google') }}'
        });
        @endif
    </script>
</body>
</html>
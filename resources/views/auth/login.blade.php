<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CuanFlow - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@700,500,400&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Satoshi', sans-serif;
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
    
    <!-- Left Section - Green Background -->
    <div class="hidden lg:flex lg:w-1/2 bg-cuan-dark text-white px-12 py-10 flex-col justify-between relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0">
            <div class="absolute top-24 right-24 w-80 h-80 border-2 border-white opacity-10 rotate-12 rounded-3xl"></div>
            <div class="absolute top-40 right-12 w-64 h-64 border-2 border-white opacity-10 rotate-12 rounded-3xl"></div>
            <div class="absolute top-56 right-0 w-48 h-48 border-2 border-white opacity-10 rotate-12 rounded-3xl"></div>
        </div>
        
        <!-- Asterisk Icon -->
        <div class="relative z-10">
            <svg width="70" height="70" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M40 10V70M10 40H70M20 20L60 60M60 20L20 60" stroke="#F0E491" stroke-width="8" stroke-linecap="round"/>
            </svg>
        </div>
        
        <!-- Main Content -->
        <div class="relative z-10 mb-8">
            <h1 class="text-5xl font-bold mb-5 leading-tight">
                Hello<br/>SobatCuan!👋
            </h1>
            <p class="text-lg max-w-md leading-relaxed text-cuan-yellow">
               Atur dan pantau perkembangan bisnismu tanpa ribet bersama <i>CuanFlow</i>. Semua proses menjadi lebih cepat, lebih teratur, dan pastinya lebih nyaman untuk dikerjakan kapan pun kamu butuh.
            </p>
        </div>
        
        <!-- Footer -->
        <p class="relative z-10 text-sm opacity-70">© 2025 CuanFlow. All rights reserved.</p>
    </div>
    
    <!-- Right Section - Login Form -->
    <div class="w-full lg:w-1/2 bg-white flex items-center justify-center px-8 py-10">
        <div class="w-full max-w-md">
            <!-- Header -->
            <div class="mb-6">
                <h2 class="text-3xl font-bold text-gray-900">CuanFlow</h2>
            </div>
            
            <h3 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang!</h3>
            
            <p class="text-gray-600 mb-8 text-sm">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="text-gray-900 font-semibold underline hover:text-cuan-green">Buat akun sekarang!.</a>
                <br/>Ini GRATIS, gunakan waktumu!
            </p>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="text-sm text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Success Messages -->
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-600">{{ session('success') }}</p>
                </div>
            @endif
            
            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                
                <!-- Email Input -->
                <div>
                    <input 
                        type="email" 
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="youremail@example.com"
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
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="remember" 
                        id="remember"
                        class="w-4 h-4 text-cuan-dark border-gray-300 rounded focus:ring-cuan-dark"
                    />
                    <label for="remember" class="ml-2 text-sm text-gray-600">Ingat Saya</label>
                </div>
                
                <!-- Login Button -->
                <button 
                    type="submit"
                    class="w-full py-3.5 bg-black text-white font-semibold rounded-lg hover:bg-gray-800 transition-colors text-base mt-6"
                >
                    Login Now
                </button>
                
                <!-- Forgot Password -->
                <p class="text-center text-gray-600 text-sm pt-2">
                    Lupa Password?
                    <a href="{{ route('password.request') }}" class="text-gray-900 font-semibold underline hover:text-cuan-green">Klik Disini</a>
                </p>
            </form>
        </div>
    </div>
    
</body>
</html>
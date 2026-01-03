<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CuanFlow - Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@700,500,400&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('assets/image/logo.svg') }}" type="image/x-icon">
    <style>
        body {
            font-family: 'Satoshi', sans-serif;
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
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
        
        .animate-fade-in-up { animation: fadeInUp 0.8s ease-out forwards; }
        .animate-scale-in { animation: scaleIn 0.6s ease-out forwards; }
        .animate-float { animation: float 8s ease-in-out infinite; }
        
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }
        
        .animate-on-load { opacity: 0; }
        
        .btn-primary {
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(49, 105, 78, 0.2);
        }
        
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
        
        .bg-pattern:nth-child(3) {
            animation-delay: 4s;
            animation-duration: 14s;
        }
        
        input {
            transition: all 0.3s ease;
        }
        
        input:focus {
            transform: translateY(-2px);
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
<body class="min-h-screen bg-white relative overflow-hidden flex items-center justify-center">
    
    <div class="green-blur absolute -top-40 -left-40 w-96 h-96 pointer-events-none"></div>
    
    <div class="absolute bottom-0 right-0 w-full h-full pointer-events-none overflow-hidden">
        <div class="bg-pattern absolute bottom-10 right-10 w-48 h-48 sm:w-64 sm:h-64 border-2 border-cuan-dark opacity-10 rotate-12 rounded-3xl"></div>
        <div class="bg-pattern absolute bottom-32 right-32 w-32 h-32 sm:w-40 sm:h-40 border-2 border-cuan-green opacity-10 rotate-12 rounded-3xl"></div>
        <div class="bg-pattern absolute bottom-20 right-52 w-24 h-24 sm:w-32 sm:h-32 border-2 border-cuan-olive opacity-10 rotate-12 rounded-3xl"></div>
    </div>
    
    <div class="relative z-10 w-full max-w-md px-6 sm:px-8 py-12">
        
        <div class="mb-10 sm:mb-12 animate-on-load animate-scale-in flex justify-center animate-fade-in-up delay-100">
            <img 
                src="{{ asset('assets/image/full-logo.svg') }}" 
                alt="CuanFlow Logo"
                class="w-full max-w-[140px] sm:max-w-[160px] h-auto"
            />
        </div>

        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4 text-center animate-on-load animate-fade-in-up delay-200">
            Reset Password
        </h1>
        
        <p class="text-gray-600 text-sm sm:text-base leading-relaxed mb-8 text-center animate-on-load animate-fade-in-up delay-300">
            Masukkan email dan password baru Anda untuk mereset password akun CuanFlow Anda.
        </p>
        
        <form method="POST" action="{{ route('password.store') }}" class="space-y-5 animate-on-load animate-fade-in-up delay-400">
            @csrf
            
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            
            <div>
                <input 
                    type="email" 
                    name="email"
                    value="{{ old('email', $request->email) }}"
                    placeholder="email@example.com"
                    required
                    autofocus
                    autocomplete="username"
                    class="w-full px-0 py-3 text-gray-900 border-b-2 border-gray-300 focus:border-cuan-dark outline-none placeholder-gray-500 text-base @error('email') border-red-500 @enderror"
                />
                @error('email')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <input 
                    type="password" 
                    name="password"
                    placeholder="Password Baru"
                    required
                    autocomplete="new-password"
                    class="w-full px-0 py-3 text-gray-900 border-b-2 border-gray-300 focus:border-cuan-dark outline-none placeholder-gray-500 text-base @error('password') border-red-500 @enderror"
                />
                @error('password')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <input 
                    type="password" 
                    name="password_confirmation"
                    placeholder="Konfirmasi Password"
                    required
                    autocomplete="new-password"
                    class="w-full px-0 py-3 text-gray-900 border-b-2 border-gray-300 focus:border-cuan-dark outline-none placeholder-gray-500 text-base @error('password_confirmation') border-red-500 @enderror"
                />
                @error('password_confirmation')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>
            
            <button 
                type="submit"
                class="btn-primary w-full py-3.5 bg-cuan-dark text-white font-semibold rounded-lg text-base mt-6"
            >
                Reset Password
            </button>
        </form>
        
        <p class="text-gray-400 text-xs mt-12 text-center animate-on-load animate-fade-in-up delay-500">
            © 2025 CuanFlow. All rights reserved.
        </p>
        
    </div>
    
</body>
</html>
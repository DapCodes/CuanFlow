<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Admin - CuanFlow</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/image/logo.svg') }}" type="image/x-icon">
    
    <!-- Fonts -->
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@700,500,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
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
    
    <style>
        body { font-family: 'Satoshi', sans-serif; }

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

        .green-blur {
            background: radial-gradient(circle at top left, rgba(49, 105, 78, 0.1) 0%, transparent 70%);
            filter: blur(60px);
        }
        
        .bg-pattern {
            animation: float 10s ease-in-out infinite;
        }
        
        .bg-pattern:nth-child(2) {
            animation-delay: 2s;
            animation-duration: 12s;
        }

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
    </style>
</head>
<body class="antialiased bg-white min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    
    <!-- Background Decorations -->
    <div class="green-blur absolute -top-40 -left-40 w-96 h-96 pointer-events-none"></div>
    <div class="absolute bottom-0 right-0 w-full h-full pointer-events-none overflow-hidden">
        <div class="bg-pattern absolute bottom-10 right-10 w-48 h-48 border-2 border-cuan-dark opacity-5 rotate-12 rounded-3xl"></div>
        <div class="bg-pattern absolute bottom-32 right-32 w-32 h-32 border-2 border-cuan-green opacity-5 rotate-12 rounded-3xl"></div>
    </div>

    <div class="w-full max-w-md relative z-10">

        <!-- Login Card -->
        <div class="bg-white/80 backdrop-blur-sm border border-gray-100 rounded-3xl p-8 sm:p-10 shadow-[0_8px_30px_rgb(0,0,0,0.04)] animate-on-load animate-fade-in-up delay-100">
            <!-- Logo -->
            <div class="flex justify-center mb-3 animate-on-load animate-fade-in-up">
                <img src="{{ asset('assets/image/full-logo.svg') }}" alt="CuanFlow Logo" class="h-10">
            </div>
            <div class="mb-8 flex justify-center">
                <p class="text-sm text-gray-500 mt-1">Masuk ke dashboard admin</p>
            </div>
            
            <!-- Error Messages -->
            @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-2xl animate-on-load animate-fade-in-up">
                <div class="flex items-center gap-2 text-red-600">
                    <i class="fas fa-exclamation-circle"></i>
                    <span class="text-sm font-medium">{{ $errors->first() }}</span>
                </div>
            </div>
            @endif
            
            <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-6">
                @csrf
                
                <!-- Email -->
                <div class="animate-on-load animate-fade-in-up delay-200">
                    <label for="email" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 ml-1">Email</label>
                    <div class="relative input-box border border-gray-200 rounded-2xl overflow-hidden bg-gray-50/50">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                            <i class="far fa-envelope"></i>
                        </span>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email') }}"
                               required 
                               autofocus
                               class="w-full pl-11 pr-4 py-3.5 bg-transparent focus:outline-none text-gray-700 placeholder:text-gray-300"
                               placeholder="Nama pengguna atau email">
                    </div>
                </div>
                
                <!-- Password -->
                <div class="animate-on-load animate-fade-in-up delay-300" x-data="{ show: false }">
                    <label for="password" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 ml-1">Password</label>
                    <div class="relative input-box border border-gray-200 rounded-2xl overflow-hidden bg-gray-50/50">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                            <i class="far fa-lock"></i>
                        </span>
                        <input :type="show ? 'text' : 'password'" 
                               name="password" 
                               id="password" 
                               required
                               class="w-full pl-11 pr-12 py-3.5 bg-transparent focus:outline-none text-gray-700 placeholder:text-gray-300"
                               placeholder="••••••••">
                        <button type="button" 
                                @click="show = !show"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-cuan-dark transition-colors">
                            <i :class="show ? 'far fa-eye-slash' : 'far fa-eye'"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Remember Me -->
                <div class="flex items-center justify-between pb-2 animate-on-load animate-fade-in-up delay-300">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" 
                                   name="remember" 
                                   class="peer hidden">
                            <div class="w-5 h-5 border-2 border-gray-200 rounded-md peer-checked:bg-cuan-dark peer-checked:border-cuan-dark transition-all flex items-center justify-center">
                                <i class="fas fa-check text-[10px] text-white opacity-0 peer-checked:opacity-100"></i>
                            </div>
                        </div>
                        <span class="text-sm text-gray-500 group-hover:text-gray-700 transition-colors">Ingat saya</span>
                    </label>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" 
                        class="btn-primary w-full py-4 px-6 bg-cuan-dark text-white font-bold rounded-2xl shadow-lg shadow-cuan-dark/10 flex items-center justify-center gap-2 animate-on-load animate-fade-in-up delay-300">
                    <span>Masuk ke Dashboard</span>
                    <i class="fas fa-arrow-right text-sm"></i>
                </button>
            </form>
    
        </div>
        
        <!-- Footer -->
        <p class="text-center text-gray-300 text-xs mt-10 animate-on-load animate-fade-in-up delay-300">
            &copy; {{ date('Y') }} CuanFlow. All rights reserved.
        </p>
    </div>
    
    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>

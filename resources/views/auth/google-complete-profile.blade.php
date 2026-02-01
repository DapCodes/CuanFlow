<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lengkapi Profil | CuanFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@700,500,400&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('assets/image/logo.svg') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        
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
        
        /* Input focus animation */
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
<body class="min-h-screen bg-white relative overflow-x-hidden flex items-center justify-center py-12">
    
    <div class="green-blur absolute -top-40 -left-40 w-96 h-96 pointer-events-none"></div>
    
    <div class="absolute bottom-0 right-0 w-full h-full pointer-events-none overflow-hidden">
        <div class="bg-pattern absolute bottom-10 right-10 w-48 h-48 sm:w-64 sm:h-64 border-2 border-cuan-dark opacity-10 rotate-12 rounded-3xl"></div>
        <div class="bg-pattern absolute bottom-32 right-32 w-32 h-32 sm:w-40 sm:h-40 border-2 border-cuan-green opacity-10 rotate-12 rounded-3xl"></div>
        <div class="bg-pattern absolute bottom-20 right-52 w-24 h-24 sm:w-32 sm:h-32 border-2 border-cuan-olive opacity-10 rotate-12 rounded-3xl"></div>
    </div>
    
    <div class="relative z-10 w-full max-w-xl px-6 sm:px-8 text-center">
        
        <div class="mb-8 animate-on-load animate-scale-in flex justify-center animate-fade-in-up delay-100">
            <img 
                src="{{ asset('assets/image/full-logo.svg') }}" 
                alt="CuanFlow Logo"
                class="w-full max-w-[140px] sm:max-w-[160px] h-auto"
            />
        </div>
        
        <h2 class="text-3xl font-bold text-gray-900 mb-2 animate-on-load animate-fade-in-up delay-200">
            Satu Langkah Lagi! 🚀
        </h2>
        
        <p class="text-gray-600 text-sm leading-relaxed mb-8 animate-on-load animate-fade-in-up delay-300">
            Lengkapi profil Anda untuk mulai menggunakan CuanFlow bersama akun Google <b>{{ $googleUser['email'] }}</b>.
        </p>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-left animate-fade-in-up">
                <ul class="text-sm text-red-600 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-10 border border-gray-100 text-left animate-on-load animate-fade-in-up delay-400">
            <form action="{{ route('auth.google.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Profile Pic from Google -->
                <div class="flex items-center space-x-4 mb-6">
                    <img src="{{ $googleUser['google_avatar'] }}" alt="Avatar" class="w-16 h-16 rounded-full ring-4 ring-cuan-yellow">
                    <div>
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Email Terverifikasi</p>
                        <p class="text-sm font-bold text-gray-900">{{ $googleUser['email'] }}</p>
                    </div>
                </div>

                <!-- Name Input -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                    <input 
                        type="text" 
                        name="name"
                        value="{{ old('name', $googleUser['name']) }}"
                        placeholder="Nama Lengkap"
                        required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-cuan-dark outline-none text-gray-900"
                    />
                </div>
                
                <!-- Phone Input -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Telepon</label>
                    <input 
                        type="tel" 
                        name="phone"
                        value="{{ old('phone') }}"
                        placeholder="08xxxxxxxxxx"
                        required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-cuan-dark outline-none text-gray-900"
                    />
                </div>

                <!-- Password Input -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                        <input 
                            type="password" 
                            name="password"
                            placeholder="Min. 8 Karakter"
                            required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-cuan-dark outline-none text-gray-900"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Konfirmasi</label>
                        <input 
                            type="password" 
                            name="password_confirmation"
                            placeholder="Konfirmasi Password"
                            required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-cuan-dark outline-none text-gray-900"
                        />
                    </div>
                </div>
                
                <div class="pt-4">
                    <button 
                        type="submit"
                        class="btn-primary w-full py-4 bg-cuan-dark text-white font-bold rounded-xl text-lg flex items-center justify-center gap-2"
                    >
                        <span>Selesaikan Pendaftaran</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
        
        <p class="text-gray-400 text-xs mt-12 animate-on-load animate-fade-in-up delay-600">© 2025 CuanFlow. All rights reserved.</p>
    </div>
    
</body>
</html>

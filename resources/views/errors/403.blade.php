<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak | CuanFlow</title>
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
        
        .btn-secondary {
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            border-color: #31694E;
            color: #31694E;
            transform: translateY(-2px);
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
        
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            background: linear-gradient(135deg, #31694E 0%, #658C58 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
        }
        
        @media (max-width: 640px) {
            .error-code {
                font-size: 5rem;
            }
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
    
    <div class="relative z-10 w-full max-w-2xl px-6 sm:px-8 py-12 text-center">
        
        <div class="mb-8 animate-on-load animate-scale-in flex justify-center">
            <img 
                src="{{ asset('assets/image/full-logo.svg') }}" 
                alt="CuanFlow Logo"
                class="w-full max-w-[140px] sm:max-w-[160px] h-auto"
            />
        </div>
        
        <!-- <div class="mb-6 animate-on-load animate-scale-in delay-100">
            <h1 class="error-code">403</h1>
        </div> -->
        
        <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4 animate-on-load animate-fade-in-up delay-200">
            Akses Ditolak
        </h2>
        
        <p class="text-gray-600 text-sm sm:text-base leading-relaxed mb-10 max-w-lg mx-auto animate-on-load animate-fade-in-up delay-300">
            Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. Jika Anda yakin ini adalah kesalahan, silakan hubungi administrator sistem.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-3 justify-center animate-on-load animate-fade-in-up delay-400">
            <a 
                href="{{ route('dashboard') }}"
                class="btn-primary px-8 py-3.5 bg-cuan-dark text-white font-semibold rounded-lg text-base"
            >
                Kembali ke Dashboard
            </a>
            
            <a 
                href="#"
                class="btn-secondary px-8 py-3.5 bg-white text-gray-700 font-medium rounded-lg border-2 border-gray-200 text-base"
            >
                Hubungi Support
            </a>
        </div>
        
        <p class="text-gray-400 text-xs mt-16">© 2025 CuanFlow. All rights reserved.</p>
    </div>
    
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Sedang Maintenance - CuanFlow</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS (CDN for Error Page) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        'cuan-green': '#31694E',
                        'cuan-yellow': '#F0E491',
                    }
                }
            }
        }
    </script>
    
    <style>
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        
        .blob {
            position: absolute;
            width: 500px;
            height: 500px;
            background: linear-gradient(180deg, rgba(49, 105, 78, 0.1) 0%, rgba(240, 228, 145, 0.1) 100%);
            filter: blur(80px);
            border-radius: 50%;
            z-index: -1;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6 relative overflow-hidden font-sans">
    <div class="blob -top-20 -left-20 animate-pulse"></div>
    <div class="blob -bottom-20 -right-20 animate-pulse" style="animation-delay: 2s"></div>

    <div class="max-w-2xl w-full text-center space-y-12 relative z-10">
        <!-- Brand -->
        <div class="flex items-center justify-center gap-4 mb-20 animate-fade-in">
            <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-200">
                <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20Logo%20Only/6%20Crimson.svg" class="h-8 w-8 grayscale opacity-50" alt="Logo">
            </div>
            <span class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">CuanFlow</span>
        </div>

        <!-- Illustration -->
        <div class="relative inline-block animate-float">
            <div class="w-64 h-64 bg-white rounded-[3rem] shadow-2xl flex items-center justify-center relative z-10">
                <i class="fas fa-screwdriver-wrench text-8xl text-cuan-green"></i>
            </div>
            <div class="absolute -top-4 -right-4 w-20 h-20 bg-cuan-yellow rounded-2xl flex items-center justify-center shadow-xl rotate-12">
                <i class="fas fa-clock text-3xl text-cuan-green"></i>
            </div>
            <div class="absolute -bottom-6 -left-6 w-24 h-24 bg-cuan-green rounded-full flex items-center justify-center shadow-xl -rotate-12">
                <i class="fas fa-tools text-3xl text-cuan-yellow"></i>
            </div>
        </div>

        <!-- Text Content -->
        <div class="space-y-6">
            <h1 class="text-5xl font-black text-slate-900 tracking-tight leading-tight">
                Sistem Sedang <span class="text-cuan-green italic">Ditingkatkan.</span>
            </h1>
            <div class="max-w-md mx-auto">
                <p class="text-slate-500 font-medium text-lg leading-relaxed mb-6">
                    Kami sedang melakukan pemeliharaan rutin untuk memastikan sistem CuanFlow tetap optimal. Harap bersabar ya!
                </p>
                <div class="inline-flex items-center gap-3 px-6 py-3 bg-white border border-slate-200 rounded-full shadow-sm">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full animate-ping"></div>
                    <span class="text-sm font-bold text-slate-700">Estimasi Selesai: 30-60 Menit</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="pt-20 border-t border-slate-200">
            <p class="text-slate-400 text-sm font-bold uppercase tracking-widest">
                &copy; {{ date('Y') }} DIGITAL CUAN SOLUTIONS. SEMUA HAK DILINDUNGI.
            </p>
            <div class="flex items-center justify-center gap-6 mt-6">
                <a href="#" class="text-slate-400 hover:text-cuan-green transition-colors"><i class="fab fa-instagram text-xl"></i></a>
                <a href="#" class="text-slate-400 hover:text-cuan-green transition-colors"><i class="fab fa-whatsapp text-xl"></i></a>
                <a href="#" class="text-slate-400 hover:text-cuan-green transition-colors"><i class="fas fa-envelope text-xl"></i></a>
            </div>
        </div>
    </div>
</body>
</html>

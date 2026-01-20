<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syarat & Ketentuan | CuanFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@700,500,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link rel="shortcut icon" href="{{ asset('assets/image/logo.svg') }}" type="image/x-icon">
    <style>
        body {
            font-family: 'Satoshi', sans-serif;
            scroll-behavior: smooth;
        }
        
        .green-blur {
            background: radial-gradient(circle at top left, rgba(49, 105, 78, 0.15) 0%, transparent 70%);
            filter: blur(60px);
        }
        
        .bg-pattern {
            animation: float 10s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(12deg); }
            50% { transform: translateY(-20px) rotate(12deg); }
        }

        .prose h1 { font-size: 2.25rem; font-weight: 700; color: #111827; margin-bottom: 1.5rem; }
        .prose h2 { font-size: 1.5rem; font-weight: 600; color: #111827; margin-top: 2.5rem; margin-bottom: 1rem; }
        .prose p { color: #4B5563; line-height: 1.75; margin-bottom: 1.25rem; }
        .prose ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1.25rem; color: #4B5563; }
        .prose li { margin-bottom: 0.5rem; }
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
<body class="bg-white relative overflow-x-hidden min-h-screen">
    
    <div class="green-blur absolute -top-40 -left-40 w-96 h-96 pointer-events-none"></div>
    
    <div class="absolute bottom-0 right-0 w-full h-full pointer-events-none overflow-hidden">
        <div class="bg-pattern absolute bottom-10 right-10 w-48 h-48 border-2 border-cuan-dark opacity-5 rotate-12 rounded-3xl"></div>
        <div class="bg-pattern absolute bottom-32 right-32 w-32 h-32 border-2 border-cuan-green opacity-5 rotate-12 rounded-3xl"></div>
    </div>
    
    <header class="relative z-20 px-6 py-8 md:px-12 lg:px-24 flex justify-between items-center">
        <a href="{{ url('/') }}" data-aos="fade-down">
            <img src="{{ asset('assets/image/full-logo.svg') }}" alt="CuanFlow Logo" class="h-10">
        </a>
        <a href="{{ route('register') }}" class="text-sm font-semibold text-cuan-dark hover:text-cuan-green transition-colors" data-aos="fade-down" data-aos-delay="100">
            Daftar Sekarang
        </a>
    </header>

    <main class="relative z-10 max-w-4xl mx-auto px-6 py-12 md:py-20">
        <div class="mb-12" data-aos="fade-up">
            <nav class="flex mb-6 text-sm text-gray-500">
                <a href="{{ url('/') }}" class="hover:text-cuan-dark">Beranda</a>
                <span class="mx-2">/</span>
                <span class="text-cuan-dark font-medium">Syarat & Ketentuan</span>
            </nav>
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Syarat & Ketentuan</h1>
            <p class="text-gray-500 italic">Terakhir diperbarui: {{ $terms ? $terms->updated_at->format('d F Y') : '-' }}</p>
        </div>

        <div class="prose max-w-none bg-white/50 backdrop-blur-sm p-8 md:p-12 rounded-3xl border border-gray-100 shadow-xl shadow-gray-100/50" data-aos="fade-up" data-aos-delay="200">
            {!! $terms ? $terms->content : '<p class="text-center text-gray-500 py-20">Konten belum tersedia.</p>' !!}
        </div>

        <div class="mt-20 text-center" data-aos="fade-up">
            <p class="text-gray-600 mb-6">Punya pertanyaan lebih lanjut?</p>
            <a href="mailto:support@cuanflow.com" class="inline-flex items-center gap-2 px-8 py-4 bg-cuan-dark text-white font-bold rounded-2xl hover:bg-cuan-green hover:-translate-y-1 transition-all shadow-lg shadow-cuan-dark/20">
                Hubungi Kami
            </a>
        </div>
    </main>

    <footer class="relative z-10 py-12 px-6 text-center border-t border-gray-50 mt-20">
        <p class="text-gray-400 text-sm">© {{ date('Y') }} CuanFlow. All rights reserved.</p>
    </footer>

    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true,
            easing: 'ease-out-cubic'
        });
    </script>
</body>
</html>

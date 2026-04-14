<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $blog->title }} | CuanFlow Blog</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@700,500,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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

        .prose h1 { font-size: 2.25rem; font-weight: 700; color: #111827; margin-bottom: 1.5rem; line-height: 1.3; }
        .prose h2 { font-size: 1.5rem; font-weight: 600; color: #111827; margin-top: 2.5rem; margin-bottom: 1rem; line-height: 1.4; }
        .prose h3 { font-size: 1.25rem; font-weight: 600; color: #111827; margin-top: 2rem; margin-bottom: 1rem; }
        .prose p { color: #4B5563; line-height: 1.75; margin-bottom: 1.25rem; }
        .prose ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1.25rem; color: #4B5563; }
        .prose ol { list-style-type: decimal; padding-left: 1.5rem; margin-bottom: 1.25rem; color: #4B5563; }
        .prose li { margin-bottom: 0.5rem; }
        .prose a { color: #31694E; font-weight: 500; text-decoration: underline; text-decoration-color: rgba(49, 105, 78, 0.3); text-underline-offset: 4px; }
        .prose a:hover { text-decoration-color: #31694E; }
        .prose blockquote { border-left: 4px solid #F0E491; padding-left: 1rem; font-style: italic; color: #6B7280; margin: 1.5rem 0; }
        .prose img { border-radius: 1rem; margin: 2rem 0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); width: 100%; object-fit: cover; }
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
<body class="bg-gray-50/30 relative overflow-x-hidden min-h-screen">
    
    <div class="green-blur absolute -top-40 -left-40 w-96 h-96 pointer-events-none z-0"></div>
    
    <div class="absolute top-40 right-0 w-full h-full pointer-events-none overflow-hidden z-0">
        <div class="bg-pattern absolute top-20 right-10 w-48 h-48 border-2 border-cuan-dark opacity-5 rotate-12 rounded-3xl"></div>
    </div>
    
    <header class="relative z-20 px-6 py-8 md:px-12 lg:px-24 flex justify-between items-center bg-transparent">
        <a href="{{ url('/') }}" data-aos="fade-down">
            <img src="{{ asset('assets/image/full-logo.svg') }}" alt="CuanFlow Logo" class="h-10">
        </a>
        <a href="{{ route('blog') }}" class="text-sm font-semibold text-cuan-dark hover:text-cuan-green transition-colors" data-aos="fade-down" data-aos-delay="100">
            Kembali ke Blog
        </a>
    </header>

    <main class="relative z-10 max-w-4xl mx-auto px-6 py-8 md:py-16">
        <div class="mb-10 text-center" data-aos="fade-up">
            <nav class="flex justify-center mb-6 text-sm text-gray-500">
                <a href="{{ route('blog') }}" class="hover:text-cuan-dark transition-colors">Blog</a>
                <span class="mx-2">/</span>
                <span class="text-cuan-dark font-medium">{{ $blog->category ?? 'Artikel' }}</span>
            </nav>
            <h1 class="text-3xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight max-w-3xl mx-auto">{{ $blog->title }}</h1>
            
            <div class="flex items-center justify-center gap-4 text-sm text-gray-500 font-medium">
                <span class="flex items-center gap-1.5"><i class="far fa-calendar-alt"></i> {{ $blog->created_at->format('d M Y') }}</span>
                <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                <span class="flex items-center gap-1.5"><i class="far fa-eye"></i> {{ number_format($blog->views) }}x Dibaca</span>
            </div>
        </div>

        @if($blog->thumbnail)
            <div class="w-full aspect-[21/9] rounded-3xl overflow-hidden shadow-xl shadow-gray-200/50 mb-12" data-aos="fade-up" data-aos-delay="100">
                <img src="{{ $blog->thumbnail_url }}" alt="{{ $blog->title }}" class="w-full h-full object-cover">
            </div>
        @else
            <div class="w-full aspect-[21/9] rounded-3xl overflow-hidden shadow-xl shadow-gray-200/50 mb-12 bg-gradient-to-br from-emerald-50 to-gray-100 flex items-center justify-center" data-aos="fade-up" data-aos-delay="100">
                <img src="{{ asset('assets/image/full-logo.svg') }}" alt="Flow Logo" class="max-w-[200px] opacity-20 filter grayscale mix-blend-multiply">
            </div>
        @endif

        <div class="prose max-w-none bg-white p-8 md:p-14 md:px-20 rounded-[2.5rem] border border-gray-100 shadow-2xl shadow-gray-100/60 leading-relaxed text-lg" data-aos="fade-up" data-aos-delay="200">
            {!! $blog->content !!}
        </div>

        <div class="mt-20 text-center" data-aos="fade-up">
            <p class="text-gray-600 mb-6">Ingin tahu lebih banyak seputar wawasan bisnis lainnya?</p>
            <a href="{{ route('blog') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-cuan-dark text-white font-bold rounded-2xl hover:bg-cuan-green hover:-translate-y-1 transition-all shadow-lg shadow-cuan-dark/20">
                <i class="fas fa-arrow-left text-sm mr-1"></i> Telusuri Artikel Lain
            </a>
        </div>
    </main>

    <footer class="relative z-10 py-12 px-6 text-center border-t border-gray-200 mt-10">
        <p class="text-gray-500 font-medium text-sm">© {{ date('Y') }} Flow Ecosystem. Dibuat dengan <i class="fas fa-heart text-red-400 mx-1"></i> untuk UMKM Indonesia.</p>
    </footer>

    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 900,
            once: true,
            easing: 'ease-out-cubic'
        });
    </script>
</body>
</html>

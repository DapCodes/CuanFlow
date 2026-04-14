<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $career->title }} | Karir Flow Ecosystem</title>
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
            background: radial-gradient(circle at top right, rgba(49, 105, 78, 0.15) 0%, transparent 70%);
            filter: blur(60px);
        }

        .prose h1 { font-size: 2.25rem; font-weight: 700; color: #111827; margin-bottom: 1.5rem; }
        .prose h2 { font-size: 1.5rem; font-weight: 700; color: #111827; border-bottom: 2px solid #F3F4F6; padding-bottom: 0.75rem; margin-top: 2.5rem; margin-bottom: 1.5rem; }
        .prose p { color: #4B5563; line-height: 1.75; margin-bottom: 1.25rem; }
        .prose ul.req-list { list-style-type: none; padding-left: 0; margin-bottom: 1.5rem; }
        .prose ul.req-list li { position: relative; padding-left: 1.75rem; margin-bottom: 0.75rem; color: #4B5563; }
        .prose ul.req-list li::before { content: "\f058"; font-family: "Font Awesome 6 Free"; font-weight: 900; position: absolute; left: 0; top: 0.125rem; color: #BBC863; font-size: 1.1em; }
        
        /* General UL fallback since sometimes users use standard UL inside tinyMce */
        .prose ul:not(.req-list) { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1.25rem; color: #4B5563; }
        .prose ul:not(.req-list) li { margin-bottom: 0.5rem; }
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
<body class="bg-[#F8F9FA] relative overflow-x-hidden min-h-screen">
    
    <div class="green-blur absolute -top-40 -right-40 w-[600px] h-[600px] pointer-events-none z-0"></div>
    
    <header class="relative z-20 px-6 py-8 md:px-12 lg:px-24 flex justify-between items-center bg-transparent">
        <a href="{{ url('/') }}" data-aos="fade-down">
            <img src="{{ asset('assets/image/full-logo.svg') }}" alt="CuanFlow Logo" class="h-10">
        </a>
        <a href="{{ route('career') }}" class="text-sm font-semibold text-cuan-dark hover:text-cuan-green transition-colors" data-aos="fade-down" data-aos-delay="100">
            Lihat Semua Lowongan
        </a>
    </header>

    <main class="relative z-10 max-w-4xl mx-auto px-6 py-6 md:py-12">
        <div class="mb-8" data-aos="fade-up">
            <nav class="flex mb-8 text-sm text-gray-500 font-medium">
                <a href="{{ route('career') }}" class="hover:text-cuan-dark transition-colors"><i class="fas fa-arrow-left mr-2"></i> Karir</a>
                <span class="mx-3">/</span>
                <span class="text-gray-400">Detail Lowongan</span>
            </nav>
            
            <div class="bg-white p-8 md:p-12 rounded-[2rem] border border-gray-100 shadow-xl shadow-gray-200/40">
                <div class="flex flex-wrap gap-3 mb-6">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-cuan-dark text-xs font-bold rounded-lg uppercase tracking-wide">
                        <i class="fas fa-clock"></i> {{ $career->type }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-bold rounded-lg uppercase tracking-wide">
                        <i class="fas fa-map-marker-alt text-gray-400"></i> {{ $career->location }}
                    </span>
                </div>
                
                <h1 class="text-3xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight">{{ $career->title }}</h1>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-6 border-t border-gray-100">
                    @if($career->salary_range)
                    <div>
                        <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Kisaran Gaji</p>
                        <p class="text-gray-900 font-medium"><i class="fas fa-wallet text-cuan-olive mr-2"></i> {{ $career->salary_range }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Batas Lamaran</p>
                        <p class="text-gray-900 font-medium"><i class="fas fa-calendar-xmark text-red-400 mr-2"></i> {{ $career->deadline ? $career->deadline->format('d F Y') : 'Terbuka Secara Reguler' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="prose max-w-none bg-white p-8 md:p-12 rounded-[2rem] border border-gray-100 shadow-lg shadow-gray-100/50 leading-relaxed text-[1.05rem]" data-aos="fade-up" data-aos-delay="100">
            <h2>Tentang Peran Ini</h2>
            {!! $career->description !!}

            <h2>Persyaratan Peran</h2>
            <div class="req-lists-container">
                {!! str_replace('<ul>', '<ul class="req-list">', $career->requirements) !!}
            </div>
        </div>

        <div class="mt-16 bg-cuan-dark text-white p-10 md:p-14 rounded-[2.5rem] flex flex-col md:flex-row items-center justify-between gap-8 relative overflow-hidden shadow-2xl" data-aos="fade-up" data-aos-delay="200">
            <div class="absolute -right-20 -bottom-20 opacity-10">
                <i class="fas fa-rocket text-[15rem]"></i>
            </div>
            <div class="relative z-10 text-center md:text-left">
                <h3 class="text-2xl md:text-3xl font-bold mb-3">Siap Membangun Masa Depan?</h3>
                <p class="text-emerald-100/80 max-w-md m-0">Kirimkan resume CV terbaik Anda beserta tautan portofolio yang relevan dengan peran ini.</p>
            </div>
            <a href="mailto:careers@flowecosystem.com?subject=Lamaran: {{ urlencode($career->title) }}" class="relative z-10 shrink-0 inline-flex items-center gap-2 px-8 py-4 bg-cuan-yellow text-gray-900 font-bold rounded-2xl hover:bg-white hover:-translate-y-1 transition-all shadow-xl shadow-black/10">
                Kirim Email Lamaran <i class="fas fa-paper-plane ml-1"></i>
            </a>
        </div>
    </main>

    <footer class="relative z-10 py-10 px-6 text-center border-t border-gray-200 mt-10">
        <p class="text-gray-500 font-medium text-sm">© {{ date('Y') }} Flow Ecosystem. Kesempatan yang Sama (EEO).</p>
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

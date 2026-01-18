<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $landingPage->meta_title ?: $landingPage->title }}</title>
    <meta name="description" content="{{ $landingPage->meta_description ?: $landingPage->tagline }}">
    
    @if($landingPage->favicon)
    <link rel="icon" href="{{ Storage::url($landingPage->favicon) }}">
    @endif
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '{{ $landingPage->primary_color }}',
                        secondary: '{{ $landingPage->secondary_color }}',
                        accent: '{{ $landingPage->accent_color }}',
                    },
                    fontFamily: {
                        sans: ['{{ $landingPage->font_family }}', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Google Fonts -->
    @php
        $fonts = ['Inter', 'Poppins', 'Plus Jakarta Sans', 'Roboto', 'Open Sans', 'Montserrat'];
        $fontFamily = $landingPage->font_family;
    @endphp
    <link href="https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', $fontFamily) }}:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- GSAP + ScrollTrigger -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    
    <style>
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { 
            font-family: '{{ $landingPage->font_family }}', sans-serif;
            overflow-x: hidden;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: {{ $landingPage->primary_color }}; border-radius: 10px; }
        
        /* Animations */
        .fade-up { opacity: 0; transform: translateY(40px); }
        .fade-in { opacity: 0; }
        .scale-in { opacity: 0; transform: scale(0.9); }
        .slide-left { opacity: 0; transform: translateX(-60px); }
        .slide-right { opacity: 0; transform: translateX(60px); }
        
        /* Navbar */
        .navbar-blur {
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        
        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, {{ $landingPage->primary_color }}, {{ $landingPage->secondary_color }});
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Glass effect */
        .glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        /* Counter animation */
        .counter { display: inline-block; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    <!-- ========== NAVBAR ========== -->
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="#" class="flex items-center gap-3">
                    @if($landingPage->logo)
                        <img src="{{ Storage::url($landingPage->logo) }}" alt="{{ $landingPage->title }}" class="h-10">
                    @else
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold" 
                             style="background: linear-gradient(135deg, {{ $landingPage->primary_color }}, {{ $landingPage->secondary_color }})">
                            F
                        </div>
                    @endif
                    <span class="text-xl font-bold text-gray-900 nav-brand">Flow</span>
                </a>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center gap-8">
                    @if(isset($sections['features']))
                        <a href="#features" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors">Fitur</a>
                    @endif
                    @if(isset($sections['benefits']))
                        <a href="#benefits" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors">Keuntungan</a>
                    @endif
                    @if(isset($sections['testimonial']))
                        <a href="#testimonial" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors">Testimoni</a>
                    @endif
                    @if(isset($sections['faq']))
                        <a href="#faq" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors">FAQ</a>
                    @endif
                    <a href="#cta" class="px-5 py-2.5 text-sm font-semibold text-white rounded-full shadow-lg transition-all hover:shadow-xl hover:-translate-y-0.5"
                       style="background: linear-gradient(135deg, {{ $landingPage->primary_color }}, {{ $landingPage->secondary_color }})">
                        Mulai Sekarang
                    </a>
                </div>
                
                <!-- Mobile Menu Button -->
                <button class="md:hidden p-2 text-gray-600" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </nav>
    
    <!-- Mobile Menu -->
    <div id="mobileMenu" class="fixed inset-0 z-[100] hidden">
        <div class="fixed inset-0 bg-black/50" onclick="toggleMobileMenu()"></div>
        <div class="fixed right-0 top-0 h-full w-80 max-w-full bg-white shadow-2xl p-6">
            <div class="flex items-center justify-between mb-8">
                <span class="text-xl font-bold">Menu</span>
                <button onclick="toggleMobileMenu()" class="p-2 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="space-y-4">
                @if(isset($sections['features']))
                    <a href="#features" onclick="toggleMobileMenu()" class="block py-3 text-gray-600 border-b border-gray-100">Fitur</a>
                @endif
                @if(isset($sections['benefits']))
                    <a href="#benefits" onclick="toggleMobileMenu()" class="block py-3 text-gray-600 border-b border-gray-100">Keuntungan</a>
                @endif
                @if(isset($sections['testimonial']))
                    <a href="#testimonial" onclick="toggleMobileMenu()" class="block py-3 text-gray-600 border-b border-gray-100">Testimoni</a>
                @endif
                @if(isset($sections['faq']))
                    <a href="#faq" onclick="toggleMobileMenu()" class="block py-3 text-gray-600 border-b border-gray-100">FAQ</a>
                @endif
                <a href="#cta" onclick="toggleMobileMenu()" 
                   class="block w-full py-3 text-center text-white rounded-xl font-semibold mt-4"
                   style="background: linear-gradient(135deg, {{ $landingPage->primary_color }}, {{ $landingPage->secondary_color }})">
                    Mulai Sekarang
                </a>
            </div>
        </div>
    </div>

    <!-- ========== HERO SECTION ========== -->
    @if(isset($sections['hero']))
    @php $hero = $sections['hero']; @endphp
    <section id="hero" class="min-h-screen flex items-center relative overflow-hidden pt-20" 
             style="{{ $hero->background_style ?: 'background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%)' }}">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-20 right-20 w-96 h-96 rounded-full opacity-20"
                 style="background: {{ $landingPage->accent_color }}; filter: blur(100px);"></div>
            <div class="absolute bottom-20 left-20 w-80 h-80 rounded-full opacity-20"
                 style="background: {{ $landingPage->primary_color }}; filter: blur(100px);"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <p class="text-sm font-semibold uppercase tracking-widest mb-4 fade-up" style="color: {{ $landingPage->primary_color }}">
                    {{ $landingPage->tagline }}
                </p>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight mb-6 fade-up">
                    {{ $hero->title ?: $landingPage->title }}
                </h1>
                <p class="text-lg sm:text-xl text-gray-600 mb-10 max-w-2xl mx-auto fade-up">
                    {{ $hero->description ?: 'Kelola bisnis Anda dengan satu platform yang terintegrasi.' }}
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 fade-up">
                    <a href="#cta" class="px-8 py-4 text-white font-semibold rounded-full shadow-lg transition-all hover:shadow-xl hover:-translate-y-1 inline-flex items-center gap-2"
                       style="background: linear-gradient(135deg, {{ $landingPage->primary_color }}, {{ $landingPage->secondary_color }})">
                        <span>Mulai Gratis</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="#features" class="px-8 py-4 border-2 border-gray-200 text-gray-700 font-semibold rounded-full hover:border-gray-300 transition-colors inline-flex items-center gap-2">
                        <i class="fas fa-play-circle"></i>
                        <span>Lihat Demo</span>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Scroll indicator -->
        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 fade-up">
            <a href="#about" class="flex flex-col items-center gap-2 text-gray-400 hover:text-gray-600 transition-colors">
                <span class="text-xs uppercase tracking-widest">Scroll</span>
                <i class="fas fa-chevron-down animate-bounce"></i>
            </a>
        </div>
    </section>
    @endif

    <!-- ========== ABOUT SECTION ========== -->
    @if(isset($sections['about']))
    @php $about = $sections['about']; @endphp
    <section id="about" class="py-24 bg-white" style="{{ $about->background_style }}">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="slide-left">
                    <p class="text-sm font-semibold uppercase tracking-widest mb-4" style="color: {{ $landingPage->primary_color }}">
                        {{ $about->subtitle ?: 'Tentang Kami' }}
                    </p>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-6">
                        {{ $about->title ?: 'Mengapa Memilih Flow?' }}
                    </h2>
                    <div class="text-gray-600 leading-relaxed space-y-4">
                        {!! nl2br(e($about->description ?: 'Flow adalah solusi all-in-one untuk mengelola bisnis Anda dengan lebih efisien dan terorganisir.')) !!}
                    </div>
                </div>
                <div class="slide-right">
                    <div class="relative">
                        <div class="absolute -inset-4 rounded-3xl opacity-10" style="background: {{ $landingPage->primary_color }}"></div>
                        <div class="relative bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                            <div class="grid grid-cols-2 gap-6">
                                <div class="text-center p-4">
                                    <p class="text-3xl font-bold gradient-text">10K+</p>
                                    <p class="text-sm text-gray-500 mt-1">Pengguna</p>
                                </div>
                                <div class="text-center p-4">
                                    <p class="text-3xl font-bold gradient-text">99%</p>
                                    <p class="text-sm text-gray-500 mt-1">Kepuasan</p>
                                </div>
                                <div class="text-center p-4">
                                    <p class="text-3xl font-bold gradient-text">24/7</p>
                                    <p class="text-sm text-gray-500 mt-1">Support</p>
                                </div>
                                <div class="text-center p-4">
                                    <p class="text-3xl font-bold gradient-text">50+</p>
                                    <p class="text-sm text-gray-500 mt-1">Fitur</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- ========== FEATURES SECTION ========== -->
    @if(isset($sections['features']))
    @php $features = $sections['features']; @endphp
    <section id="features" class="py-24 bg-gray-50" style="{{ $features->background_style }}">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 fade-up">
                <p class="text-sm font-semibold uppercase tracking-widest mb-4" style="color: {{ $landingPage->primary_color }}">
                    {{ $features->subtitle ?: 'Fitur Unggulan' }}
                </p>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                    {{ $features->title ?: 'Semua yang Anda Butuhkan' }}
                </h2>
                <p class="text-gray-600">
                    {{ $features->description ?: 'Fitur lengkap untuk mendukung pertumbuhan bisnis Anda.' }}
                </p>
            </div>
            
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($features->activeItems as $index => $item)
                <div class="group bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 fade-up" style="transition-delay: {{ $index * 100 }}ms">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6 transition-colors"
                         style="background: {{ $landingPage->accent_color }}20">
                        <i class="{{ $item->icon ?: 'fas fa-star' }} text-2xl" style="color: {{ $landingPage->primary_color }}"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $item->title }}</h3>
                    <p class="text-gray-600 leading-relaxed">{{ $item->description }}</p>
                </div>
                @empty
                <div class="col-span-full text-center py-12 text-gray-400">
                    <i class="fas fa-puzzle-piece text-4xl mb-3"></i>
                    <p>Belum ada fitur yang ditambahkan</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>
    @endif

    <!-- ========== BENEFITS SECTION ========== -->
    @if(isset($sections['benefits']))
    @php $benefits = $sections['benefits']; @endphp
    <section id="benefits" class="py-24 bg-white" style="{{ $benefits->background_style }}">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 fade-up">
                <p class="text-sm font-semibold uppercase tracking-widest mb-4" style="color: {{ $landingPage->primary_color }}">
                    {{ $benefits->subtitle ?: 'Keuntungan' }}
                </p>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                    {{ $benefits->title ?: 'Manfaat Menggunakan Flow' }}
                </h2>
            </div>
            
            <div class="grid sm:grid-cols-2 gap-6">
                @forelse($benefits->activeItems as $index => $item)
                <div class="flex gap-4 p-6 rounded-2xl hover:bg-gray-50 transition-colors fade-up">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background: {{ $landingPage->primary_color }}">
                        <i class="{{ $item->icon ?: 'fas fa-check' }} text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $item->title }}</h3>
                        <p class="text-gray-600">{{ $item->description }}</p>
                    </div>
                </div>
                @empty
                @endforelse
            </div>
        </div>
    </section>
    @endif

    <!-- ========== STATISTICS SECTION ========== -->
    @if(isset($sections['statistics']))
    @php $stats = $sections['statistics']; @endphp
    <section id="statistics" class="py-24" style="background: linear-gradient(135deg, {{ $landingPage->secondary_color }}, {{ $landingPage->primary_color }})">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 fade-up">
                <h2 class="text-3xl font-bold text-white mb-2">{{ $stats->title ?: 'Dipercaya Ribuan Bisnis' }}</h2>
                <p class="text-white/70">{{ $stats->subtitle }}</p>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                @forelse($stats->activeItems as $item)
                <div class="text-center fade-up">
                    <p class="text-4xl lg:text-5xl font-bold text-white mb-2 counter" data-target="{{ $item->getExtraData('value', 100) }}">0</p>
                    <p class="text-white/70">{{ $item->title }}</p>
                </div>
                @empty
                <div class="text-center fade-up">
                    <p class="text-4xl lg:text-5xl font-bold text-white mb-2">10K+</p>
                    <p class="text-white/70">Pengguna Aktif</p>
                </div>
                <div class="text-center fade-up">
                    <p class="text-4xl lg:text-5xl font-bold text-white mb-2">500+</p>
                    <p class="text-white/70">Bisnis</p>
                </div>
                <div class="text-center fade-up">
                    <p class="text-4xl lg:text-5xl font-bold text-white mb-2">1M+</p>
                    <p class="text-white/70">Transaksi</p>
                </div>
                <div class="text-center fade-up">
                    <p class="text-4xl lg:text-5xl font-bold text-white mb-2">99%</p>
                    <p class="text-white/70">Uptime</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>
    @endif

    <!-- ========== TESTIMONIAL SECTION ========== -->
    @if(isset($sections['testimonial']))
    @php $testimonial = $sections['testimonial']; @endphp
    <section id="testimonial" class="py-24 bg-gray-50" style="{{ $testimonial->background_style }}">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 fade-up">
                <p class="text-sm font-semibold uppercase tracking-widest mb-4" style="color: {{ $landingPage->primary_color }}">
                    {{ $testimonial->subtitle ?: 'Testimonial' }}
                </p>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900">
                    {{ $testimonial->title ?: 'Apa Kata Mereka?' }}
                </h2>
            </div>
            
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($testimonial->activeItems as $index => $item)
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 fade-up">
                    <div class="flex items-center gap-1 mb-4">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star text-sm {{ $i <= $item->rating ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                        @endfor
                    </div>
                    <p class="text-gray-600 mb-6 leading-relaxed">"{{ $item->description }}"</p>
                    <div class="flex items-center gap-3">
                        @if($item->image)
                            <img src="{{ Storage::url($item->image) }}" alt="{{ $item->title }}" class="w-12 h-12 rounded-full object-cover">
                        @else
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold"
                                 style="background: {{ $landingPage->primary_color }}">
                                {{ strtoupper(substr($item->title, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <p class="font-semibold text-gray-900">{{ $item->title }}</p>
                            <p class="text-sm text-gray-500">{{ $item->role }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12 text-gray-400">
                    <i class="fas fa-quote-left text-4xl mb-3"></i>
                    <p>Belum ada testimonial</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>
    @endif

    <!-- ========== FAQ SECTION ========== -->
    @if(isset($sections['faq']))
    @php $faq = $sections['faq']; @endphp
    <section id="faq" class="py-24 bg-white" style="{{ $faq->background_style }}">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 fade-up">
                <p class="text-sm font-semibold uppercase tracking-widest mb-4" style="color: {{ $landingPage->primary_color }}">
                    {{ $faq->subtitle ?: 'FAQ' }}
                </p>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900">
                    {{ $faq->title ?: 'Pertanyaan Umum' }}
                </h2>
            </div>
            
            <div class="space-y-4" x-data="{ active: null }">
                @forelse($faq->activeItems as $index => $item)
                <div class="bg-gray-50 rounded-2xl overflow-hidden fade-up">
                    <button @click="active = active === {{ $index }} ? null : {{ $index }}"
                            class="w-full px-6 py-5 text-left flex items-center justify-between hover:bg-gray-100 transition-colors">
                        <span class="font-semibold text-gray-900">{{ $item->title }}</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform" :class="active === {{ $index }} ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="active === {{ $index }}" x-collapse class="px-6 pb-5">
                        <p class="text-gray-600 leading-relaxed">{{ $item->description }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-12 text-gray-400">
                    <i class="fas fa-question-circle text-4xl mb-3"></i>
                    <p>Belum ada FAQ</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>
    @endif

    <!-- ========== CTA SECTION ========== -->
    @if(isset($sections['cta']) || isset($cta))
    @php $ctaData = $cta ?? null; @endphp
    <section id="cta" class="py-24 relative overflow-hidden" 
             style="background: linear-gradient(135deg, {{ $landingPage->primary_color }}, {{ $landingPage->secondary_color }})">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-20 -right-20 w-96 h-96 rounded-full opacity-10" style="background: white"></div>
            <div class="absolute -bottom-20 -left-20 w-80 h-80 rounded-full opacity-10" style="background: white"></div>
        </div>
        
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-6 fade-up">
                {{ $ctaData->headline ?? 'Siap untuk Level Up Bisnis Anda?' }}
            </h2>
            <p class="text-xl text-white/80 mb-10 max-w-2xl mx-auto fade-up">
                {{ $ctaData->description ?? 'Mulai kelola bisnis Anda dengan lebih pintar menggunakan Flow.' }}
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 fade-up">
                <a href="{{ $ctaData->button_link ?? '#' }}" 
                   class="px-8 py-4 bg-white font-semibold rounded-full shadow-xl transition-all hover:shadow-2xl hover:-translate-y-1 inline-flex items-center gap-2"
                   style="color: {{ $landingPage->primary_color }}">
                    <span>{{ $ctaData->button_text ?? 'Mulai Sekarang' }}</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
                @if($ctaData && $ctaData->hasSecondaryButton())
                <a href="{{ $ctaData->secondary_button_link }}" 
                   class="px-8 py-4 border-2 border-white/30 text-white font-semibold rounded-full hover:bg-white/10 transition-colors">
                    {{ $ctaData->secondary_button_text }}
                </a>
                @endif
            </div>
        </div>
    </section>
    @endif

    <!-- ========== FOOTER ========== -->
    @if(isset($sections['footer']))
    @php $footer = $sections['footer']; @endphp
    <footer class="py-12 bg-gray-900" style="{{ $footer->background_style }}">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-3">
                    @if($landingPage->logo)
                        <img src="{{ Storage::url($landingPage->logo) }}" alt="{{ $landingPage->title }}" class="h-8 brightness-0 invert">
                    @else
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-sm font-bold" 
                             style="background: {{ $landingPage->primary_color }}">F</div>
                    @endif
                    <span class="text-white font-semibold">Flow</span>
                </div>
                
                <p class="text-gray-400 text-sm">
                    {{ $footer->description ?: '© ' . date('Y') . ' Flow. All rights reserved.' }}
                </p>
                
                <div class="flex items-center gap-4">
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700 transition-colors">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700 transition-colors">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700 transition-colors">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>
    @else
    <footer class="py-8 bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-gray-400 text-sm">© {{ date('Y') }} {{ $landingPage->title }}. All rights reserved.</p>
        </div>
    </footer>
    @endif

    <!-- AlpineJS for FAQ accordion -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        // Mobile menu toggle
        function toggleMobileMenu() {
            document.getElementById('mobileMenu').classList.toggle('hidden');
        }
        
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('bg-white/95', 'navbar-blur', 'shadow-sm');
            } else {
                navbar.classList.remove('bg-white/95', 'navbar-blur', 'shadow-sm');
            }
        });
        
        // GSAP Animations
        gsap.registerPlugin(ScrollTrigger);
        
        // Fade up animation
        gsap.utils.toArray('.fade-up').forEach(element => {
            gsap.fromTo(element, 
                { opacity: 0, y: 40 },
                {
                    opacity: 1,
                    y: 0,
                    duration: 0.8,
                    ease: 'power2.out',
                    scrollTrigger: {
                        trigger: element,
                        start: 'top 85%',
                        toggleActions: 'play none none none'
                    }
                }
            );
        });
        
        // Fade in animation
        gsap.utils.toArray('.fade-in').forEach(element => {
            gsap.fromTo(element,
                { opacity: 0 },
                {
                    opacity: 1,
                    duration: 1,
                    scrollTrigger: {
                        trigger: element,
                        start: 'top 85%'
                    }
                }
            );
        });
        
        // Slide left animation
        gsap.utils.toArray('.slide-left').forEach(element => {
            gsap.fromTo(element,
                { opacity: 0, x: -60 },
                {
                    opacity: 1,
                    x: 0,
                    duration: 1,
                    ease: 'power2.out',
                    scrollTrigger: {
                        trigger: element,
                        start: 'top 80%'
                    }
                }
            );
        });
        
        // Slide right animation
        gsap.utils.toArray('.slide-right').forEach(element => {
            gsap.fromTo(element,
                { opacity: 0, x: 60 },
                {
                    opacity: 1,
                    x: 0,
                    duration: 1,
                    ease: 'power2.out',
                    scrollTrigger: {
                        trigger: element,
                        start: 'top 80%'
                    }
                }
            );
        });
        
        // Scale in animation
        gsap.utils.toArray('.scale-in').forEach(element => {
            gsap.fromTo(element,
                { opacity: 0, scale: 0.9 },
                {
                    opacity: 1,
                    scale: 1,
                    duration: 0.8,
                    ease: 'back.out(1.7)',
                    scrollTrigger: {
                        trigger: element,
                        start: 'top 85%'
                    }
                }
            );
        });
        
        // Stagger animation for feature cards
        ScrollTrigger.batch('.grid > .fade-up', {
            onEnter: batch => gsap.to(batch, {
                opacity: 1,
                y: 0,
                stagger: 0.15,
                duration: 0.8,
                ease: 'power2.out'
            }),
            start: 'top 85%'
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $outlet->name }} - Official Store</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <style>
        * { box-sizing: border-box; }
        html {
            scroll-behavior: smooth;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }
        
        :root {
            --primary: {{ $landingPage->primary_color ?? '#4F46E5' }};
            --secondary: {{ $landingPage->secondary_color ?? '#1F2937' }};
        }

        .text-primary { color: var(--primary); }
        .bg-primary { background-color: var(--primary); }
        .border-primary { border-color: var(--primary); }
        .text-secondary { color: var(--secondary); }
        .bg-secondary { background-color: var(--secondary); }

        /* Hero Section */
        .hero-section {
            background-image: linear-gradient(135deg, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.4)), url('{{ $landingPage->hero_image ? Storage::url($landingPage->hero_image) : "https://images.unsplash.com/photo-1556740738-b6a63e27c4df?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: relative;
            min-height: 100vh;
        }

        /* Navbar Styles */
        .navbar {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .navbar.scrolled .nav-link { color: #374151 !important; }
        .navbar.scrolled .brand-name { color: #111827 !important; }

        /* Glass Card */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Mobile Menu */
        .mobile-menu {
            position: fixed;
            top: 0;
            right: 0;
            width: 100%;
            max-width: 320px;
            height: 100vh;
            background: white;
            z-index: 2000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            box-shadow: -10px 0 30px rgba(0, 0, 0, 0.1);
        }
        .mobile-menu.active { transform: translateX(0); }
        .mobile-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .mobile-overlay.active { opacity: 1; pointer-events: all; }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #F3F4F6; }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--secondary); }

        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .animate-float { animation: float 3s ease-in-out infinite; }
    </style>
</head>
<body class="bg-gray-50">

    <!-- ========== NAVBAR ========== -->
    <nav class="navbar fixed w-full z-50 px-4 py-3" id="navbar">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center">
                <!-- Brand -->
                <div class="flex items-center gap-3" data-aos="fade-right">
                    @if($outlet->logo)
                        <img src="{{ Storage::url($outlet->logo) }}" alt="Logo" class="h-10 w-10 rounded-full object-cover border-2 border-white/30">
                    @else
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr($outlet->name, 0, 1)) }}
                        </div>
                    @endif
                    <span class="brand-name text-xl font-bold text-white">{{ $outlet->name }}</span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center gap-8" data-aos="fade-left">
                    <a href="#home" class="nav-link text-white/90 hover:text-white font-medium text-sm transition">Home</a>
                    <a href="#about" class="nav-link text-white/90 hover:text-white font-medium text-sm transition">Tentang</a>
                    <a href="#products" class="nav-link text-white/90 hover:text-white font-medium text-sm transition">Produk</a>
                    <a href="#contact" class="nav-link text-white/90 hover:text-white font-medium text-sm transition">Kontak</a>
                </div>

                <!-- Mobile Hamburger -->
                <button type="button" class="md:hidden w-10 h-10 rounded-full bg-white/10 text-white flex items-center justify-center" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars text-lg"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-overlay" id="mobileOverlay" onclick="toggleMobileMenu()"></div>
    
    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <div class="p-6">
            <div class="flex justify-between items-center mb-8">
                <span class="text-xl font-bold text-gray-900">Menu</span>
                <button type="button" onclick="toggleMobileMenu()" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-times text-gray-600"></i>
                </button>
            </div>
            <nav class="space-y-4">
                <a href="#home" onclick="toggleMobileMenu()" class="block py-3 px-4 rounded-xl text-gray-700 hover:bg-gray-100 font-medium transition">
                    <i class="fas fa-home mr-3 text-indigo-500"></i> Home
                </a>
                <a href="#about" onclick="toggleMobileMenu()" class="block py-3 px-4 rounded-xl text-gray-700 hover:bg-gray-100 font-medium transition">
                    <i class="fas fa-info-circle mr-3 text-indigo-500"></i> Tentang
                </a>
                <a href="#products" onclick="toggleMobileMenu()" class="block py-3 px-4 rounded-xl text-gray-700 hover:bg-gray-100 font-medium transition">
                    <i class="fas fa-box mr-3 text-indigo-500"></i> Produk
                </a>
                <a href="#contact" onclick="toggleMobileMenu()" class="block py-3 px-4 rounded-xl text-gray-700 hover:bg-gray-100 font-medium transition">
                    <i class="fas fa-envelope mr-3 text-indigo-500"></i> Kontak
                </a>
            </nav>
        </div>
    </div>

    <!-- ========== HERO SECTION ========== -->
    <section id="home" class="hero-section flex items-center justify-center text-center text-white relative">
        <div class="relative z-10 max-w-4xl mx-auto px-6" data-aos="zoom-in" data-aos-duration="1000">
            <!-- Tagline -->
            @if($landingPage->tagline_text)
            <p class="text-lg md:text-xl font-medium text-white/80 uppercase tracking-widest mb-4">
                {{ $landingPage->tagline_text }}
            </p>
            @endif

            <!-- Hero Title -->
            <h1 class="text-4xl sm:text-5xl md:text-7xl font-extrabold mb-6 leading-tight">
                {{ $landingPage->hero_title }}
            </h1>
            
            <!-- Hero Subtitle -->
            <p class="text-lg md:text-2xl font-light text-white/90 mb-10">
                {{ $landingPage->hero_subtitle }}
            </p>
            
            <!-- CTA Buttons -->
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="#products" class="bg-white text-gray-900 px-8 py-4 rounded-full font-bold text-lg shadow-2xl hover:shadow-3xl transform hover:scale-105 transition-all duration-300">
                    {{ $landingPage->cta_button_text ?? 'Belanja Sekarang' }}
                </a>
                <a href="#about" class="border-2 border-white/30 text-white px-8 py-4 rounded-full font-semibold hover:bg-white/10 transition">
                    Pelajari Lebih
                </a>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 animate-bounce">
            <i class="fas fa-chevron-down text-white/50 text-2xl"></i>
        </div>
    </section>

    <!-- ========== STATS SECTION ========== -->
    <section class="py-16 bg-white relative -mt-16 mx-4 md:mx-auto max-w-6xl rounded-2xl shadow-2xl z-20 glass-card" data-aos="fade-up">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center divide-y md:divide-y-0 md:divide-x divide-gray-200">
            <div class="p-6 flex flex-col items-center justify-center">
                <div class="text-4xl font-bold text-primary mb-2">{{ $displaySales }}</div>
                <div class="text-gray-500 uppercase tracking-widest text-sm font-semibold">Total Penjualan</div>
            </div>
            <div class="p-6 flex flex-col items-center justify-center">
                <div class="text-4xl font-bold text-primary mb-2">{{ count($products) }}</div>
                <div class="text-gray-500 uppercase tracking-widest text-sm font-semibold">Produk Unggulan</div>
            </div>
            <div class="p-6 flex flex-col items-center justify-center">
                <div class="text-4xl font-bold text-primary mb-2">100%</div>
                <div class="text-gray-500 uppercase tracking-widest text-sm font-semibold">Kepuasan Pelanggan</div>
            </div>
        </div>
    </section>

    <!-- ========== ABOUT SECTION ========== -->
    <section id="about" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-16 items-center">
                <!-- Image -->
                <div class="relative" data-aos="fade-right">
                    <div class="absolute -inset-4 bg-gradient-to-r from-indigo-100 to-purple-100 rounded-3xl opacity-60 blur-2xl"></div>
                    <img src="{{ $landingPage->about_image ? Storage::url($landingPage->about_image) : 'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80' }}" 
                         alt="About" 
                         class="relative rounded-2xl shadow-2xl w-full object-cover">
                </div>

                <!-- Content -->
                <div class="space-y-8" data-aos="fade-left">
                    <div>
                        <span class="text-sm font-bold uppercase tracking-wider text-primary">Tentang Kami</span>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2">Kenali Lebih Dekat</h2>
                        <div class="w-20 h-1 bg-primary rounded-full mt-4"></div>
                    </div>
                    
                    <div class="text-gray-600 leading-relaxed prose">
                        {!! $landingPage->about_text ?? '<p>Deskripsi toko belum diisi.</p>' !!}
                    </div>

                    <!-- Vision & Mission -->
                    @if($landingPage->vision_text || $landingPage->mission_text)
                    <div class="grid gap-4">
                        @if($landingPage->vision_text)
                        <div class="flex items-start p-4 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl border border-indigo-100">
                            <div class="w-12 h-12 rounded-lg bg-primary flex items-center justify-center text-white shrink-0">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="ml-4">
                                <h4 class="font-bold text-gray-900">Visi</h4>
                                <p class="text-gray-600 text-sm mt-1">{{ $landingPage->vision_text }}</p>
                            </div>
                        </div>
                        @endif

                        @if($landingPage->mission_text)
                        <div class="flex items-start p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-gray-200">
                            <div class="w-12 h-12 rounded-lg bg-secondary flex items-center justify-center text-white shrink-0">
                                <i class="fas fa-rocket"></i>
                            </div>
                            <div class="ml-4">
                                <h4 class="font-bold text-gray-900">Misi</h4>
                                <p class="text-gray-600 text-sm mt-1">{{ $landingPage->mission_text }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- ========== PRODUCTS SECTION ========== -->
    <section id="products" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="text-sm font-bold uppercase tracking-wider text-primary">Produk Kami</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2">Produk Pilihan Kami</h2>
                <div class="w-20 h-1 bg-primary rounded-full mx-auto mt-4"></div>
            </div>

            <!-- Products Grid -->
            @if(count($products) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($products as $index => $product)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden transform hover:-translate-y-2 transition duration-300 border border-gray-100" 
                         data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                        <div class="h-64 bg-gray-100 overflow-hidden relative group">
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-300">
                                    <i class="fas fa-box text-5xl"></i>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition duration-300 flex items-center justify-center">
                                <button type="button" 
                                        onclick="showProductDetail({{ json_encode([
                                            'name' => $product->name,
                                            'price' => number_format($product->selling_price, 0, ',', '.'),
                                            'description' => $product->description ?? 'Tidak ada deskripsi produk.',
                                            'image' => $product->image ? Storage::url($product->image) : null,
                                            'category' => $product->category->name ?? 'Umum',
                                            'unit' => $product->unit->name ?? 'pcs'
                                        ]) }})"
                                        class="opacity-0 group-hover:opacity-100 bg-white text-gray-900 px-6 py-2 rounded-full font-bold transform translate-y-4 group-hover:translate-y-0 transition duration-300">
                                    Lihat Detail
                                </button>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $product->name }}</h3>
                            <p class="text-primary font-bold text-lg">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-16 text-gray-400">
                <i class="fas fa-box-open text-6xl mb-4"></i>
                <p>Belum ada produk unggulan yang dipilih.</p>
            </div>
            @endif
        </div>
    </section>

    </section>

    <!-- ========== TESTIMONIALS SECTION ========== -->
    @if(isset($testimonials) && count($testimonials) > 0)
    <section class="py-24 bg-gray-50 relative overflow-hidden">
        <div class="absolute inset-0 bg-white/50 pattern-grid-lg opacity-10"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="text-primary font-bold tracking-wider uppercase text-sm">Apa Kata Mereka</span>
                <h2 class="text-3xl md:text-5xl font-bold mt-2 text-gray-900">Ulasan Pelanggan</h2>
                <div class="w-24 h-1 bg-primary mx-auto mt-6 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($testimonials as $index => $testimonial)
                    <div class="bg-white rounded-3xl p-8 shadow-xl border border-gray-100 hover:shadow-2xl transition duration-300" 
                         data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                        
                        <div class="flex items-center gap-4 mb-6">
                            @if($testimonial->image)
                                <img src="{{ Storage::url($testimonial->image) }}" class="w-16 h-16 rounded-full object-cover border-4 border-gray-50 shadow-md">
                            @else
                                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-400 font-bold text-2xl shadow-md">
                                    {{ strtoupper(substr($testimonial->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <h4 class="font-bold text-gray-900 text-lg">{{ $testimonial->name }}</h4>
                                <p class="text-sm text-gray-500 font-medium">{{ $testimonial->role ?? 'Pelanggan' }}</p>
                            </div>
                        </div>

                        <div class="flex text-yellow-400 text-sm mb-4">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $testimonial->rating ? '' : 'text-gray-200' }}"></i>
                            @endfor
                        </div>

                        <p class="text-gray-600 leading-relaxed italic relative">
                            '{{ $testimonial->content }}'
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- ========== TESTIMONIAL FORM SECTION ========== -->
    <section class="py-24 bg-white relative">
        <div class="max-w-4xl mx-auto px-4 relative z-10">
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-[2.5rem] p-8 md:p-12 shadow-2xl text-white overflow-hidden relative">
                <!-- Decor -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                
                <div class="relative z-10">
                    <div class="text-center mb-10">
                        <h2 class="text-3xl font-bold mb-4">Bagikan Pengalaman Anda</h2>
                        <p class="text-gray-300">Kami sangat menghargai masukan Anda. Ceritakan pengalaman Anda berbelanja di sini!</p>
                    </div>

                    <form id="testimonialForm" class="space-y-6">
                        @csrf
                        <input type="hidden" name="outlet_id" value="{{ $outlet->id }}">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Nama Lengkap</label>
                                <input type="text" name="name" required class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" placeholder="John Doe">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Role / Pekerjaan (Opsional)</label>
                                <input type="text" name="role" class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" placeholder="Contoh: Food Vlogger">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Beri Rating</label>
                            <div class="flex gap-4 items-center bg-white/5 rounded-xl p-4 border border-white/10">
                                <div class="rating-stars flex flex-row-reverse justify-end gap-2">
                                    <input type="radio" name="rating" id="star5" value="5" class="hidden peer/5" checked>
                                    <label for="star5" class="cursor-pointer text-gray-600 peer-checked/5:text-yellow-400 hover:text-yellow-400 transition-colors text-2xl"><i class="fas fa-star"></i></label>
                                    
                                    <input type="radio" name="rating" id="star4" value="4" class="hidden peer/4">
                                    <label for="star4" class="cursor-pointer text-gray-600 peer-checked/4:text-yellow-400 peer-checked/5:text-yellow-400 hover:text-yellow-400 transition-colors text-2xl"><i class="fas fa-star"></i></label>
                                    
                                    <input type="radio" name="rating" id="star3" value="3" class="hidden peer/3">
                                    <label for="star3" class="cursor-pointer text-gray-600 peer-checked/3:text-yellow-400 peer-checked/4:text-yellow-400 peer-checked/5:text-yellow-400 hover:text-yellow-400 transition-colors text-2xl"><i class="fas fa-star"></i></label>
                                    
                                    <input type="radio" name="rating" id="star2" value="2" class="hidden peer/2">
                                    <label for="star2" class="cursor-pointer text-gray-600 peer-checked/2:text-yellow-400 peer-checked/3:text-yellow-400 peer-checked/4:text-yellow-400 peer-checked/5:text-yellow-400 hover:text-yellow-400 transition-colors text-2xl"><i class="fas fa-star"></i></label>
                                    
                                    <input type="radio" name="rating" id="star1" value="1" class="hidden peer/1">
                                    <label for="star1" class="cursor-pointer text-gray-600 peer-checked/1:text-yellow-400 peer-checked/2:text-yellow-400 peer-checked/3:text-yellow-400 peer-checked/4:text-yellow-400 peer-checked/5:text-yellow-400 hover:text-yellow-400 transition-colors text-2xl"><i class="fas fa-star"></i></label>
                                </div>
                                <span class="text-sm text-gray-400 ml-2">Pilih jumlah bintang</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Ulasan Anda</label>
                            <textarea name="content" rows="4" required class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition" placeholder="Bagikan pengalaman menarik Anda..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Foto (Opsional)</label>
                            <div class="relative">
                                <input type="file" name="image" id="testimonialImage" accept="image/*" class="hidden" onchange="previewTestimonialImage(this)">
                                <label for="testimonialImage" class="cursor-pointer w-full flex items-center justify-center gap-3 bg-white/5 border border-dashed border-white/30 rounded-xl p-4 text-gray-300 hover:bg-white/10 transition">
                                    <i class="fas fa-camera"></i>
                                    <span id="testimonialImageLabel">Upload Foto</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit" id="btnSubmitTestimonial" class="w-full bg-primary hover:bg-indigo-600 text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-primary/50 transition duration-300 transform hover:scale-[1.02]">
                            Kirim Ulasan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== CTA SECTION ========== -->
    <section class="py-24 relative overflow-hidden" style="background: linear-gradient(135deg, var(--primary), var(--secondary))">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-72 h-72 bg-white rounded-full -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full translate-x-1/2 translate-y-1/2"></div>
        </div>
        <div class="relative z-10 max-w-4xl mx-auto text-center px-4" data-aos="zoom-in">
            <h2 class="text-3xl md:text-5xl font-bold text-white mb-6">
                {{ $landingPage->cta_text ?? 'Siap Untuk Berbelanja?' }}
            </h2>
            <p class="text-white/80 text-lg mb-10">Hubungi kami sekarang dan dapatkan penawaran terbaik!</p>
            <div class="flex flex-wrap gap-4 justify-center">
                @if($landingPage->whatsapp_number)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $landingPage->whatsapp_number) }}" 
                   target="_blank"
                   class="bg-white text-gray-900 px-8 py-4 rounded-full font-bold hover:shadow-xl transform hover:scale-105 transition-all inline-flex items-center gap-2">
                    <i class="fab fa-whatsapp text-green-500 text-xl"></i>
                    {{ $landingPage->cta_button_text ?? 'Hubungi Kami' }}
                </a>
                @else
                <a href="#contact" class="bg-white text-gray-900 px-8 py-4 rounded-full font-bold hover:shadow-xl transform hover:scale-105 transition-all">
                    {{ $landingPage->cta_button_text ?? 'Hubungi Kami' }}
                </a>
                @endif
            </div>
        </div>
    </section>

    <!-- ========== FOOTER ========== -->
    <footer id="contact" class="bg-gray-900 text-white pt-20 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-12 mb-16">
                <!-- Brand -->
                <div data-aos="fade-up">
                    <div class="flex items-center gap-3 mb-6">
                        @if($outlet->logo)
                            <img src="{{ Storage::url($outlet->logo) }}" alt="Logo" class="h-12 w-12 rounded-full bg-white p-1 object-cover">
                        @else
                            <div class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl">
                                {{ strtoupper(substr($outlet->name, 0, 1)) }}
                            </div>
                        @endif
                        <span class="text-2xl font-bold">{{ $outlet->name }}</span>
                    </div>
                    <p class="text-gray-400 mb-6">{{ $outlet->address ?? 'Alamat belum diatur' }}</p>
                    
                    <!-- Social Links -->
                    <div class="flex gap-3">
                        @if($landingPage->social_media['instagram'] ?? false)
                        <a href="https://instagram.com/{{ $landingPage->social_media['instagram'] }}" target="_blank" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-pink-500 transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        @endif
                        @if($landingPage->social_media['tiktok'] ?? false)
                        <a href="https://tiktok.com/@{{ $landingPage->social_media['tiktok'] }}" target="_blank" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-gray-700 transition">
                            <i class="fab fa-tiktok"></i>
                        </a>
                        @endif
                        @if($landingPage->whatsapp_number ?? false)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $landingPage->whatsapp_number) }}" target="_blank" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-green-500 transition">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Contact Info -->
                <div data-aos="fade-up" data-aos-delay="100">
                    <h4 class="font-bold text-lg mb-6">Hubungi Kami</h4>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3 text-gray-400">
                            <i class="fas fa-phone w-5 text-center"></i>
                            <span>{{ $outlet->phone ?? '-' }}</span>
                        </li>
                        <li class="flex items-center gap-3 text-gray-400">
                            <i class="fas fa-envelope w-5 text-center"></i>
                            <span>{{ $outlet->email ?? '-' }}</span>
                        </li>
                        <li class="flex items-start gap-3 text-gray-400">
                            <i class="fas fa-map-marker-alt w-5 text-center mt-1"></i>
                            <span>{{ $outlet->address ?? '-' }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Map -->
                <div class="h-48 rounded-xl overflow-hidden shadow-lg bg-gray-800" data-aos="fade-up" data-aos-delay="200" id="map-container">
                    <div id="map" class="w-full h-full"></div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="border-t border-gray-800 pt-8 text-center text-gray-500 text-sm">
                {{ $landingPage->footer_text ?? '© ' . date('Y') . ' ' . $outlet->name . '. Powered by CuanFlow.' }}
            </div>
        </div>
    </footer>

    <!-- ========== PRODUCT DETAIL MODAL ========== -->
    <div id="productModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-black bg-opacity-50" aria-hidden="true" onclick="closeProductModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="relative">
                    <button onclick="closeProductModal()" class="absolute top-4 right-4 z-10 w-10 h-10 flex items-center justify-center bg-white/80 backdrop-blur rounded-full text-gray-600 hover:text-gray-900 shadow-md transition">
                        <i class="fas fa-times"></i>
                    </button>

                    <div class="flex flex-col md:flex-row">
                        <!-- Image Column -->
                        <div class="md:w-1/2 h-64 md:h-auto bg-gray-100">
                            <img id="modalProductImage" src="" alt="Product" class="w-full h-full object-cover">
                            <div id="modalImagePlaceholder" class="hidden w-full h-full flex items-center justify-center text-gray-300 bg-gray-100">
                                <i class="fas fa-box text-6xl"></i>
                            </div>
                        </div>

                        <!-- Info Column -->
                        <div class="md:w-1/2 p-8">
                            <div class="mb-2">
                                <span id="modalProductCategory" class="text-xs font-bold uppercase tracking-wider text-primary bg-primary/10 px-3 py-1 rounded-full">Kategori</span>
                            </div>
                            <h3 id="modalProductName" class="text-2xl font-bold text-gray-900 mb-4 leading-tight">Nama Produk</h3>
                            <div class="text-3xl font-black text-primary mb-6">
                                <span class="text-lg font-bold">Rp</span> <span id="modalProductPrice">0</span>
                            </div>
                            
                            <div class="mb-8">
                                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-2">Deskripsi Produk</h4>
                                <p id="modalProductDescription" class="text-gray-600 leading-relaxed text-sm">Deskripsi singkat produk.</p>
                            </div>

                            <div class="flex flex-col gap-3">
                                <div class="flex items-center gap-3 text-sm text-gray-500">
                                    <i class="fas fa-tag"></i>
                                    <span>Satuan: <span id="modalProductUnit" class="font-bold text-gray-900">-</span></span>
                                </div>
                                <div class="flex items-center gap-3 text-sm text-gray-500">
                                    <i class="fas fa-shield-alt"></i>
                                    <span>Original Store Quality</span>
                                </div>
                            </div>

                            <div class="mt-8 pt-8 border-t border-gray-100">
                                <a id="modalWhatsAppBtn" href="#" target="_blank" class="w-full flex items-center justify-center gap-3 bg-green-500 hover:bg-green-600 text-white font-bold py-4 px-6 rounded-2xl shadow-lg transition transform hover:scale-[1.02]">
                                    <i class="fab fa-whatsapp text-xl"></i>
                                    Pesan Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        // Init AOS
        AOS.init({
            once: true,
            offset: 100,
            duration: 800,
        });

        // Mobile Menu Toggle
        function toggleMobileMenu() {
            document.getElementById('mobileMenu').classList.toggle('active');
            document.getElementById('mobileOverlay').classList.toggle('active');
        }

        // Product Modal Functions
        function showProductDetail(product) {
            document.getElementById('modalProductName').innerText = product.name;
            document.getElementById('modalProductPrice').innerText = product.price;
            document.getElementById('modalProductDescription').innerText = product.description;
            document.getElementById('modalProductCategory').innerText = product.category;
            document.getElementById('modalProductUnit').innerText = product.unit;

            const image = document.getElementById('modalProductImage');
            const placeholder = document.getElementById('modalImagePlaceholder');
            
            if (product.image) {
                image.src = product.image;
                image.classList.remove('hidden');
                placeholder.classList.add('hidden');
            } else {
                image.classList.add('hidden');
                placeholder.classList.remove('hidden');
            }

            // Update WhatsApp link if number exists
            const waNumber = "{{ preg_replace('/[^0-9]/', '', $landingPage->whatsapp_number ?? '') }}";
            const waBtn = document.getElementById('modalWhatsAppBtn');
            if (waNumber) {
                const message = encodeURIComponent(`Halo, saya tertarik dengan produk ${product.name}. Bisakah saya mendapatkan info lebih lanjut?`);
                waBtn.href = `https://wa.me/${waNumber}?text=${message}`;
                waBtn.classList.remove('hidden');
            } else {
                waBtn.classList.add('hidden');
            }

            const modal = document.getElementById('productModal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeProductModal() {
            const modal = document.getElementById('productModal');
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Testimonial Form Handler
        function previewTestimonialImage(input) {
            if (input.files && input.files[0]) {
                const fileName = input.files[0].name;
                document.getElementById('testimonialImageLabel').innerText = fileName;
            }
        }

        document.getElementById('testimonialForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('btnSubmitTestimonial');
            const originalText = btn.innerText;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
            btn.disabled = true;

            const formData = new FormData(this);

            fetch('{{ route("testimonials.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Terima Kasih!',
                        text: data.message,
                        confirmButtonColor: '#4F46E5',
                        background: '#fff',
                        customClass: {
                            popup: 'rounded-3xl'
                        }
                    });
                    this.reset();
                    document.getElementById('testimonialImageLabel').innerText = 'Upload Foto';
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Gagal mengirim ulasan. Silakan coba lagi.',
                    confirmButtonColor: '#EF4444'
                });
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });

        // Navbar Scroll Effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Leaflet Map
        document.addEventListener('DOMContentLoaded', function() {
            var lat = {{ $outlet->latitude ?? -6.200000 }};
            var lng = {{ $outlet->longtitude ?? 106.816666 }};
            
            var map = L.map('map').setView([lat, lng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            L.marker([lat, lng]).addTo(map)
                .bindPopup('{{ $outlet->name }}')
                .openPopup();
        });
    </script>
</body>
</html>

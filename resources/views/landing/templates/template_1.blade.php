@php
    // Template 1: Modern Gradient & Glassmorphism (Enhanced Version)
    $headingFont = $landingPage->font_heading ?? 'Inter';
    $bodyFont = $landingPage->font_body ?? 'Inter';
@endphp

<style>
    /* Load Dynamic Fonts */
    @import url('https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', $headingFont) }}:wght@300;400;500;600;700;800;900&family={{ str_replace(' ', '+', $bodyFont) }}:wght@300;400;500;600;700;800;900&display=swap');

    .template-1-wrapper {
        font-family: '{{ $bodyFont }}', sans-serif !important;
        --heading-font: '{{ $headingFont }}', sans-serif;
        --body-font: '{{ $bodyFont }}', sans-serif;
    }

    .template-1-wrapper h1, 
    .template-1-wrapper h2, 
    .template-1-wrapper h3, 
    .template-1-wrapper h4, 
    .template-1-wrapper h5, 
    .template-1-wrapper h6,
    .template-1-wrapper .font-heading,
    .template-1-wrapper .brand-name {
        font-family: var(--heading-font) !important;
    }

    /* Enhanced Parallax & Hero Layout */
    .hero-section {
        background-image: none !important;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .hero-parallax-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 130%; /* More height for smoother travel */
        background-image: linear-gradient(135deg, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.4)), url('{{ $landingPage->hero_image ? Storage::url($landingPage->hero_image) : "https://images.unsplash.com/photo-1556740738-b6a63e27c4df?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" }}');
        background-size: cover;
        background-position: center;
        z-index: 0;
        will-change: transform;
        transition: background-image 0.5s ease;
    }
    .hero-content {
        position: relative;
        z-index: 10;
        width: 100%;
    }
    .scroll-indicator {
        position: absolute;
        bottom: 40px;
        left: 0;
        right: 0;
        margin: 0 auto;
        width: fit-content;
        z-index: 20;
    }

    /* Navbar Scrolled Adjustments for Template 1 */
    .navbar.scrolled .brand-name {
        color: #111827 !important;
    }
    .navbar.scrolled .nav-link {
        color: #374151 !important;
    }
    .navbar.scrolled .nav-link::after {
        background-color: #374151 !important;
    }
    .navbar.scrolled button.md\:hidden {
        color: #111827 !important;
        background-color: rgba(0, 0, 0, 0.05) !important;
    }
    .navbar.scrolled button.md\:hidden:hover {
        background-color: rgba(0, 0, 0, 0.1) !important;
    }
</style>

<div class="template-1-wrapper">

<!-- ========== NAVBAR ========== -->
<nav class="navbar fixed top-0 left-0 w-full z-50 px-4 py-4 backdrop-blur-md bg-black/40 border-b border-white/10 shadow-2xl transition-all duration-500 group" id="navbar">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center">
            <!-- Brand -->
            <div class="flex items-center gap-3 transition-transform duration-300 hover:scale-105" data-aos="fade-right">
                @if($outlet->logo)
                    <img src="{{ Storage::url($outlet->logo) }}" alt="Logo" class="h-10 w-10 rounded-full object-cover border-2 border-white/30 shadow-lg group-hover:border-white/60 transition-colors">
                @else
                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white font-bold shadow-lg group-hover:shadow-primary/50 transition-all">
                        {{ strtoupper(substr($outlet->name, 0, 1)) }}
                    </div>
                @endif
                <span class="brand-name text-xl font-bold text-white drop-shadow-lg group-hover:text-white transition-colors">{{ $outlet->name }}</span>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center gap-8" data-aos="fade-left">
                <a href="#home" class="nav-link relative text-white/90 hover:text-white font-medium text-sm transition-all py-1 after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-0 after:h-0.5 after:bg-white after:transition-all hover:after:w-full">Home</a>
                <a href="#about" class="nav-link relative text-white/90 hover:text-white font-medium text-sm transition-all py-1 after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-0 after:h-0.5 after:bg-white after:transition-all hover:after:w-full">Tentang</a>
                <a href="#products" class="nav-link relative text-white/90 hover:text-white font-medium text-sm transition-all py-1 after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-0 after:h-0.5 after:bg-white after:transition-all hover:after:w-full">Produk</a>
                <a href="#contact" class="nav-link relative text-white/90 hover:text-white font-medium text-sm transition-all py-1 after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-0 after:h-0.5 after:bg-white after:transition-all hover:after:w-full">Kontak</a>
            </div>

            <!-- Mobile Hamburger -->
            <button type="button" class="md:hidden w-10 h-10 rounded-full bg-white/10 backdrop-blur-sm text-white flex items-center justify-center hover:bg-white/20 transition-all active:scale-95" onclick="toggleMobileMenu()">
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
            <button type="button" onclick="toggleMobileMenu()" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition">
                <i class="fas fa-times text-gray-600"></i>
            </button>
        </div>
        <nav class="space-y-4">
            <a href="#home" onclick="toggleMobileMenu()" class="block py-3 px-4 rounded-xl text-gray-700 hover:bg-gradient-to-r hover:from-primary/10 hover:to-secondary/10 font-medium transition-all">
                <i class="fas fa-home mr-3 text-primary"></i> Home
            </a>
            <a href="#about" onclick="toggleMobileMenu()" class="block py-3 px-4 rounded-xl text-gray-700 hover:bg-gradient-to-r hover:from-primary/10 hover:to-secondary/10 font-medium transition-all">
                <i class="fas fa-info-circle mr-3 text-primary"></i> Tentang
            </a>
            <a href="#products" onclick="toggleMobileMenu()" class="block py-3 px-4 rounded-xl text-gray-700 hover:bg-gradient-to-r hover:from-primary/10 hover:to-secondary/10 font-medium transition-all">
                <i class="fas fa-box mr-3 text-primary"></i> Produk
            </a>
            <a href="#contact" onclick="toggleMobileMenu()" class="block py-3 px-4 rounded-xl text-gray-700 hover:bg-gradient-to-r hover:from-primary/10 hover:to-secondary/10 font-medium transition-all">
                <i class="fas fa-envelope mr-3 text-primary"></i> Kontak
            </a>
        </nav>
    </div>
</div>

<!-- ========== HERO SECTION ========== -->
<section id="home" class="hero-section text-center text-white relative min-h-screen">
    <!-- Parallax Background Layer -->
    <div class="hero-parallax-bg" id="heroParallaxBg"></div>

    <div class="hero-content pt-20">
        @if(isset($is_editor) && $is_editor)
        <!-- Upload Button with Better Design -->
        <div class="absolute top-20 right-4 md:right-10 z-[60]">
            <label for="hero_image" class="cursor-pointer inline-flex items-center gap-2 bg-white/90 backdrop-blur-md text-gray-800 px-4 md:px-6 py-3 rounded-2xl shadow-2xl font-semibold text-sm hover:bg-white transition-all transform hover:scale-105 hover:shadow-primary/50">
                <i class="fas fa-camera text-primary text-lg"></i>
                <span class="hidden sm:inline">Ganti Background</span>
                <span class="sm:hidden">Background</span>
            </label>
        </div>
        @endif
        <div class="max-w-5xl mx-auto px-4 sm:px-6" data-aos="zoom-in" data-aos-duration="1000">
        <!-- Tagline -->
        @if(isset($is_editor) && $is_editor)
            <div class="mb-6 group">
                <div class="relative inline-block w-full max-w-2xl">
                    <input type="text" name="tagline_text" value="{{ $landingPage->tagline_text }}" 
                          class="editable-field text-base sm:text-lg md:text-xl font-medium text-white/90 uppercase tracking-widest text-center w-full bg-white/10 backdrop-blur-sm border-2 border-white/30 rounded-xl px-4 py-2 focus:bg-white/20 focus:border-white/50 focus:outline-none transition-all placeholder-white/50"
                          placeholder="Tagline (Opsional)" data-sync="tagline_text">
                    <div class="absolute -bottom-1 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-white/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
            </div>
        @elseif($landingPage->tagline_text)
            <p class="text-base sm:text-lg md:text-xl font-medium text-white/90 uppercase tracking-widest mb-6 animate-fade-in">
                {{ $landingPage->tagline_text }}
            </p>
        @endif

        <!-- Hero Title -->
        <h1 class="text-3xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold mb-6 leading-tight">
            @if(isset($is_editor) && $is_editor)
                <div class="group relative inline-block w-full">
                    <textarea rows="2" 
                              class="editable-field w-full text-center bg-white/10 backdrop-blur-sm border-2 border-white/30 text-white resize-none rounded-xl px-4 py-3 focus:bg-white/20 focus:border-white/50 focus:outline-none transition-all placeholder-white/50 font-extrabold text-3xl sm:text-5xl md:text-6xl lg:text-7xl leading-tight"
                              placeholder="Judul Utama Hero" data-sync="hero_title"
                              oninput="autoResize(this)">{{ $landingPage->hero_title }}</textarea>
                    <div class="absolute -bottom-1 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-white/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
            @else
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-white to-white/80 drop-shadow-2xl">
                    {{ $landingPage->hero_title }}
                </span>
            @endif
        </h1>
        
        <!-- Hero Subtitle -->
        <div class="text-base sm:text-lg md:text-xl lg:text-2xl font-light text-white/90 mb-10 max-w-3xl mx-auto">
            @if(isset($is_editor) && $is_editor)
                <div class="group relative inline-block w-full">
                    <textarea rows="2" 
                              class="editable-field w-full text-center bg-white/10 backdrop-blur-sm border-2 border-white/30 text-white resize-none rounded-xl px-4 py-3 focus:bg-white/20 focus:border-white/50 focus:outline-none transition-all placeholder-white/50 text-base sm:text-lg md:text-xl lg:text-2xl leading-relaxed"
                              placeholder="Sub Judul Hero" data-sync="hero_subtitle"
                              oninput="autoResize(this)">{{ $landingPage->hero_subtitle }}</textarea>
                    <div class="absolute -bottom-1 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-white/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
            @else
                {{ $landingPage->hero_subtitle }}
            @endif
        </div>
        
        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            @if(isset($is_editor) && $is_editor)
                <div class="relative group w-full sm:w-auto">
                    <div class="bg-white text-gray-900 px-6 sm:px-8 py-4 rounded-full font-bold text-base sm:text-lg shadow-2xl flex items-center justify-center gap-2">
                        <i class="fas fa-shopping-bag text-primary"></i>
                        <input type="text" name="cta_button_text" value="{{ $landingPage->cta_button_text ?? 'Belanja Sekarang' }}" 
                               class="bg-transparent text-center w-full sm:w-40 border-b-2 border-transparent focus:border-primary outline-none text-gray-900 font-bold"
                               data-sync="cta_button_text" placeholder="Text Button">
                    </div>
                    <div class="absolute -inset-1 bg-gradient-to-r from-primary/50 to-secondary/50 rounded-full opacity-0 group-hover:opacity-20 blur transition-opacity"></div>
                </div>
            @else
                <a href="#products" class="w-full sm:w-auto bg-white text-gray-900 px-6 sm:px-8 py-4 rounded-full font-bold text-base sm:text-lg shadow-2xl hover:shadow-primary/50 transform hover:scale-105 transition-all duration-300 inline-flex items-center justify-center gap-2">
                    <i class="fas fa-shopping-bag"></i>
                    {{ $landingPage->cta_button_text ?? 'Belanja Sekarang' }}
                </a>
            @endif
            <a href="#about" class="w-full sm:w-auto border-2 border-white/40 backdrop-blur-sm bg-white/10 text-white px-6 sm:px-8 py-4 rounded-full font-semibold hover:bg-white/20 hover:border-white/60 transition-all duration-300 inline-flex items-center justify-center gap-2">
                <i class="fas fa-info-circle"></i>
                Pelajari Lebih
            </a>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <!-- <div class="absolute bottom-10 left-1/2 -translate-x-1/2 scroll-indicator animate-bounce">
        <i class="fas fa-chevron-down text-white/50 text-2xl drop-shadow-lg"></i>
    </div> -->
</section>

<!-- ========== STATS SECTION ========== -->
<section class="py-8 sm:py-12 md:py-16 bg-transparent relative -mt-20 sm:-mt-24 md:-mt-32 px-4 z-20" data-aos="fade-up">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white/95 backdrop-blur-xl rounded-2xl sm:rounded-3xl shadow-2xl border border-gray-100/50 overflow-hidden">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-0 text-center divide-y sm:divide-y-0 sm:divide-x divide-gray-200/50">
                <div class="p-6 sm:p-8 flex flex-col items-center justify-center hover:bg-gradient-to-br hover:from-primary/5 hover:to-secondary/5 transition-all duration-300 group">
                    <div class="text-3xl sm:text-4xl md:text-5xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-2 group-hover:scale-110 transition-transform">
                        {{ $displaySales }}
                    </div>
                    <div class="text-gray-500 uppercase tracking-widest text-xs sm:text-sm font-semibold">Total Penjualan</div>
                </div>
                <div class="p-6 sm:p-8 flex flex-col items-center justify-center hover:bg-gradient-to-br hover:from-primary/5 hover:to-secondary/5 transition-all duration-300 group">
                    <div class="text-3xl sm:text-4xl md:text-5xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-2 group-hover:scale-110 transition-transform">
                        {{ $outlet->products->count() }}
                    </div>
                    <div class="text-gray-500 uppercase tracking-widest text-xs sm:text-sm font-semibold">Produk Unggulan</div>
                </div>
                <div class="p-6 sm:p-8 flex flex-col items-center justify-center hover:bg-gradient-to-br hover:from-primary/5 hover:to-secondary/5 transition-all duration-300 group">
                    <div class="text-3xl sm:text-4xl md:text-5xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-2 group-hover:scale-110 transition-transform">
                        100%
                    </div>
                    <div class="text-gray-500 uppercase tracking-widest text-xs sm:text-sm font-semibold">Kepuasan Pelanggan</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== ABOUT SECTION ========== -->
<section id="about" class="py-16 sm:py-20 md:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-12 lg:gap-16 items-center">
            <!-- Image -->
            <div class="relative group" data-aos="fade-right">
                <div class="absolute -inset-4 bg-gradient-to-r from-primary/10 to-secondary/10 rounded-3xl opacity-60 blur-3xl group-hover:opacity-80 transition-opacity"></div>
                <img id="aboutImagePreview" src="{{ $landingPage->about_image ? Storage::url($landingPage->about_image) : 'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80' }}" 
                     alt="About" 
                     class="relative rounded-2xl shadow-2xl w-full object-cover aspect-square group-hover:scale-[1.02] transition-transform duration-500">
                @if(isset($is_editor) && $is_editor)
                <div class="absolute bottom-4 right-4 z-10">
                    <label for="about_image" class="cursor-pointer inline-flex items-center gap-2 bg-white/95 backdrop-blur-md text-gray-800 px-4 py-2.5 rounded-xl shadow-xl font-semibold text-xs sm:text-sm hover:bg-white transition-all transform hover:scale-105">
                        <i class="fas fa-camera text-primary"></i>
                        <span class="hidden sm:inline">Ganti Foto</span>
                    </label>
                </div>
                @endif
            </div>

            <!-- Content -->
            <div class="space-y-8" data-aos="fade-left">
                <div>
                    <span class="text-xs sm:text-sm font-bold uppercase tracking-wider text-primary">Tentang Kami</span>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mt-2">Kenali Lebih Dekat</h2>
                    <div class="w-20 h-1 bg-gradient-to-r from-primary to-secondary rounded-full mt-4"></div>
                </div>
                
                <div class="text-gray-600 leading-relaxed prose max-w-none">
                    @if(isset($is_editor) && $is_editor)
                        <div class="bg-gray-50 rounded-xl p-4 border-2 border-gray-200 hover:border-primary/50 transition-all">
                            <div id="aboutEditor" class="min-h-[150px] focus:outline-none">
                                {!! $landingPage->about_text !!}
                            </div>
                        </div>
                        <input type="hidden" name="about_text" id="aboutText" value="{{ $landingPage->about_text }}">
                        <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                            <i class="fas fa-info-circle"></i>
                            Klik untuk mengedit teks
                        </p>
                    @else
                        {!! $landingPage->about_text ?? '<p>Deskripsi toko belum diisi.</p>' !!}
                    @endif
                </div>

                <!-- Vision & Mission -->
                <div class="grid gap-4">
                    <div class="flex items-start p-4 sm:p-5 bg-gradient-to-br from-primary/5 to-secondary/5 rounded-xl sm:rounded-2xl border border-primary/10 hover:shadow-lg transition-all group">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white shrink-0 shadow-lg group-hover:scale-110 transition-transform">
                            <i class="fas fa-eye text-lg sm:text-xl"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <h4 class="font-bold text-gray-900 text-base sm:text-lg mb-2">Visi</h4>
                            @if(isset($is_editor) && $is_editor)
                                <textarea name="vision_text" rows="2" 
                                          class="editable-field w-full text-gray-600 text-sm resize-none border-2 border-primary/20 bg-white/70 backdrop-blur-sm p-3 rounded-lg focus:border-primary/50 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all placeholder-gray-400"
                                          placeholder="Tulis visi toko Anda..."
                                          data-sync="vision_text"
                                          oninput="autoResize(this)">{{ $landingPage->vision_text }}</textarea>
                            @else
                                <p class="text-gray-600 text-sm leading-relaxed">{{ $landingPage->vision_text }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-start p-4 sm:p-5 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl sm:rounded-2xl border border-gray-200 hover:shadow-lg transition-all group">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-gradient-to-br from-gray-700 to-gray-900 flex items-center justify-center text-white shrink-0 shadow-lg group-hover:scale-110 transition-transform">
                            <i class="fas fa-rocket text-lg sm:text-xl"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <h4 class="font-bold text-gray-900 text-base sm:text-lg mb-2">Misi</h4>
                            @if(isset($is_editor) && $is_editor)
                                <textarea name="mission_text" rows="2" 
                                          class="editable-field w-full text-gray-600 text-sm resize-none border-2 border-gray-300 bg-white/70 backdrop-blur-sm p-3 rounded-lg focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all placeholder-gray-400"
                                          placeholder="Tulis misi toko Anda..."
                                          data-sync="mission_text"
                                          oninput="autoResize(this)">{{ $landingPage->mission_text }}</textarea>
                            @else
                                <p class="text-gray-600 text-sm leading-relaxed">{{ $landingPage->mission_text }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== PRODUCTS SECTION ========== -->
<section id="products" class="py-16 sm:py-20 md:py-24 bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12 sm:mb-16" data-aos="fade-up">
            <span class="text-xs sm:text-sm font-bold uppercase tracking-wider text-primary">Produk Kami</span>
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mt-2">Produk Pilihan Kami</h2>
            <div class="w-20 h-1 bg-gradient-to-r from-primary to-secondary rounded-full mx-auto mt-4"></div>
        </div>

        <!-- Products Grid -->
        @if(count($products) > 0)
            @if(isset($is_editor) && $is_editor)
                <p class="text-center mb-8 text-gray-600 bg-primary/10 p-4 rounded-xl inline-block mx-auto border border-primary/20">
                    <i class="fas fa-info-circle text-primary"></i> Pilih produk unggulan untuk ditampilkan di carousel
                </p>
            @endif
            
            <div class="swiper products-carousel pb-12">
                <div class="swiper-wrapper">
                    @foreach($products as $index => $product)
                        <div class="swiper-slide h-auto">
                            @if(isset($is_editor) && $is_editor)
                                {{-- Editor Mode --}}
                                <label class="block bg-white rounded-2xl sm:rounded-3xl shadow-xl overflow-hidden cursor-pointer border-2 border-transparent hover:border-primary/50 transition-all duration-300 group h-full">
                                    <input type="checkbox" data-product-id="{{ $product->id }}" value="{{ $product->id }}" class="peer sr-only product-checkbox" {{ in_array($product->id, $landingPage->selected_product_ids ?? []) ? 'checked' : '' }}>
                                    <div class="h-56 sm:h-64 bg-gray-100 overflow-hidden relative ring-offset-2 peer-checked:ring-4 peer-checked:ring-primary">
                                        @if($product->image)
                                            <img src="{{ Storage::url($product->image) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-300"><i class="fas fa-box text-5xl"></i></div>
                                        @endif
                                        <div class="absolute top-3 right-3 bg-white w-8 h-8 rounded-full flex items-center justify-center shadow-lg opacity-0 peer-checked:opacity-100 transition-opacity">
                                            <i class="fas fa-check text-primary font-bold"></i>
                                        </div>
                                    </div>
                                    <div class="p-5 sm:p-6 peer-checked:bg-primary/5">
                                        <h3 class="text-lg font-bold text-gray-900 mb-2 truncate">{{ $product->name }}</h3>
                                        <p class="text-primary font-bold text-xl">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                                    </div>
                                </label>
                            @else
                                {{-- Public View --}}
                                <div class="bg-white rounded-2xl sm:rounded-3xl shadow-xl overflow-hidden transform hover:-translate-y-2 transition-all duration-300 border border-gray-100 group h-full">
                                    <div class="h-56 sm:h-64 bg-gray-100 overflow-hidden relative">
                                        @if($product->image)
                                            <img src="{{ Storage::url($product->image) }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-300"><i class="fas fa-box text-5xl"></i></div>
                                        @endif
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center">
                                            <button type="button" onclick="showProductDetail({{ json_encode(['name' => $product->name,'price' => number_format($product->selling_price, 0, ',', '.'),'description' => $product->description ?? '','image' => $product->image ? Storage::url($product->image) : null,'category' => $product->category->name ?? '','unit' => $product->unit->name ?? '']) }})"
                                                    class="bg-white text-gray-900 px-6 py-3 rounded-full font-bold shadow-xl hover:scale-105 transition-all">Lihat Detail</button>
                                        </div>
                                    </div>
                                    <div class="p-5 sm:p-6 text-center">
                                        <h3 class="text-lg font-bold text-gray-900 mb-2 truncate">{{ $product->name }}</h3>
                                        <p class="text-primary font-bold text-xl">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
                <div class="swiper-button-next !text-primary after:!text-xl bg-white/80 backdrop-blur w-10 h-10 rounded-full shadow-lg !hidden md:!flex"></div>
                <div class="swiper-button-prev !text-primary after:!text-xl bg-white/80 backdrop-blur w-10 h-10 rounded-full shadow-lg !hidden md:!flex"></div>
            </div>

            <!-- View All Button -->
            <div class="text-center mt-12" data-aos="fade-up">
                <button onclick="toggleAllProducts()" class="inline-flex items-center gap-3 bg-gradient-to-r from-primary to-secondary text-white px-10 py-4 rounded-2xl font-bold shadow-xl hover:shadow-primary/40 transform hover:-translate-y-1 transition-all active:scale-95 group">
                    <span>Lihat Semua Produk</span>
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </button>
            </div>
        @else
            <div class="text-center py-16 text-gray-400" data-aos="fade-up">
                <i class="fas fa-box-open text-5xl sm:text-6xl mb-4 opacity-50"></i>
                <p class="text-base sm:text-lg">Belum ada produk unggulan yang dipilih.</p>
            </div>
        @endif
    </div>
</section>

<!-- ========== TESTIMONIALS SECTION ========== -->
@if(isset($testimonials) && count($testimonials) > 0)
<section class="py-16 sm:py-20 md:py-24 bg-gradient-to-b from-white to-gray-50 relative overflow-hidden">
    <div class="absolute inset-0 bg-white/50 pattern-grid-lg opacity-10"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-12 sm:mb-16" data-aos="fade-up">
            <span class="text-primary font-bold tracking-wider uppercase text-xs sm:text-sm">Apa Kata Mereka</span>
            <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mt-2 text-gray-900">Ulasan Pelanggan</h2>
            <div class="w-24 h-1 bg-gradient-to-r from-primary to-secondary mx-auto mt-6 rounded-full"></div>
        </div>

        @if(isset($is_editor) && $is_editor)
            <p class="text-center mb-8 text-gray-600 bg-secondary/10 p-4 rounded-xl inline-block mx-auto border border-secondary/20">
                <i class="fas fa-info-circle text-secondary"></i> Pilih testimoni yang ingin ditampilkan
            </p>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            @foreach($testimonials as $index => $testimonial)
                @if(isset($is_editor) && $is_editor)
                    {{-- Editor Mode: Checkbox Selection --}}
                    <label class="bg-white rounded-2xl sm:rounded-3xl p-6 sm:p-8 shadow-xl border-2 border-transparent hover:border-secondary/30 cursor-pointer transition-all duration-300 group relative" 
                         data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                        <input type="checkbox" 
                               data-testimonial-id="{{ $testimonial->id }}" 
                               value="{{ $testimonial->id }}" 
                               class="peer sr-only testimonial-checkbox"
                               {{ in_array($testimonial->id, $landingPage->selected_testimonial_ids ?? []) ? 'checked' : '' }}>
                        
                        <div class="absolute top-4 right-4 bg-white w-8 h-8 rounded-full flex items-center justify-center shadow-lg opacity-0 peer-checked:opacity-100 transition-opacity border-2 border-secondary">
                            <i class="fas fa-check text-secondary font-bold text-sm"></i>
                        </div>

                        <div class="peer-checked:bg-secondary/5 -m-6 sm:-m-8 p-6 sm:p-8 rounded-2xl sm:rounded-3xl transition-colors">
                            <div class="flex items-center gap-4 mb-6">
                                @if($testimonial->image)
                                    <img src="{{ Storage::url($testimonial->image) }}" class="w-14 h-14 sm:w-16 sm:h-16 rounded-full object-cover border-4 border-white shadow-lg">
                                @else
                                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-gradient-to-br from-primary/10 to-secondary/10 flex items-center justify-center text-primary font-bold text-xl sm:text-2xl shadow-lg">
                                        {{ strtoupper(substr($testimonial->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <h4 class="font-bold text-gray-900 text-base sm:text-lg">{{ $testimonial->name }}</h4>
                                    <p class="text-xs sm:text-sm text-gray-500 font-medium">{{ $testimonial->role ?? 'Pelanggan' }}</p>
                                </div>
                            </div>

                            <div class="flex text-yellow-400 text-sm mb-4">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $testimonial->rating ? '' : 'text-gray-200' }}"></i>
                                @endfor
                            </div>

                            <p class="text-gray-600 text-sm sm:text-base leading-relaxed italic">
                                "{{ Str::limit($testimonial->content, 120) }}"
                            </p>
                        </div>
                    </label>
                @elseif(in_array($testimonial->id, $landingPage->selected_testimonial_ids ?? []))
                    {{-- Public View: Only show selected testimonials--}}
                    <div class="bg-white rounded-2xl sm:rounded-3xl p-6 sm:p-8 shadow-xl border border-gray-100 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group" 
                         data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                        
                        <div class="flex items-center gap-4 mb-6">
                            @if($testimonial->image)
                                <img src="{{ Storage::url($testimonial->image) }}" class="w-14 h-14 sm:w-16 sm:h-16 rounded-full object-cover border-4 border-gray-50 shadow-lg group-hover:scale-110 transition-transform">
                            @else
                                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-gradient-to-br from-primary/10 to-secondary/10 flex items-center justify-center text-primary font-bold text-xl sm:text-2xl shadow-lg group-hover:scale-110 transition-transform">
                                    {{ strtoupper(substr($testimonial->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <h4 class="font-bold text-gray-900 text-base sm:text-lg">{{ $testimonial->name }}</h4>
                                <p class="text-xs sm:text-sm text-gray-500 font-medium">{{ $testimonial->role ?? 'Pelanggan' }}</p>
                            </div>
                        </div>

                        <div class="flex text-yellow-400 text-sm mb-4">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $testimonial->rating ? '' : 'text-gray-200' }}"></i>
                            @endfor
                        </div>

                        <p class="text-gray-600 text-sm sm:text-base leading-relaxed italic relative">
                            "{{ $testimonial->content }}"
                        </p>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- ========== TESTIMONIAL FORM SECTION ========== -->
@if(!isset($is_editor) || !$is_editor)
<section class="py-16 sm:py-20 md:py-24 bg-white relative">
    <div class="max-w-4xl mx-auto px-4 relative z-10">
        <div class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 rounded-3xl sm:rounded-[2.5rem] p-6 sm:p-8 md:p-12 shadow-2xl text-white overflow-hidden relative">
            <!-- Decor -->
            <div class="absolute top-0 right-0 w-48 sm:w-64 h-48 sm:h-64 bg-white/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-48 sm:w-64 h-48 sm:h-64 bg-primary/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>
            
            <div class="relative z-10">
                <div class="text-center mb-8 sm:mb-10">
                    <h2 class="text-2xl sm:text-3xl font-bold mb-3 sm:mb-4">Bagikan Pengalaman Anda</h2>
                    <p class="text-gray-300 text-sm sm:text-base">Kami sangat menghargai masukan Anda. Ceritakan pengalaman Anda berbelanja di sini!</p>
                </div>

                <form id="testimonialForm" class="space-y-5 sm:space-y-6">
                    @csrf
                    <input type="hidden" name="outlet_id" value="{{ $outlet->id }}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Nama Lengkap <span class="text-red-400">*</span></label>
                            <input type="text" name="name" required class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition backdrop-blur-sm" placeholder="John Doe">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Role / Pekerjaan</label>
                            <input type="text" name="role" class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition backdrop-blur-sm" placeholder="Food Vlogger">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Beri Rating <span class="text-red-400">*</span></label>
                        <div class="flex gap-4 items-center bg-white/5 rounded-xl p-4 border border-white/10 backdrop-blur-sm">
                            <div class="rating-stars flex flex-row-reverse justify-end gap-2">
                                <input type="radio" name="rating" id="star5" value="5" class="hidden peer/5" checked>
                                <label for="star5" class="cursor-pointer text-gray-600 peer-checked/5:text-yellow-400 hover:text-yellow-400 transition-all text-2xl sm:text-3xl"><i class="fas fa-star"></i></label>
                                
                                <input type="radio" name="rating" id="star4" value="4" class="hidden peer/4">
                                <label for="star4" class="cursor-pointer text-gray-600 peer-checked/4:text-yellow-400 peer-checked/5:text-yellow-400 hover:text-yellow-400 transition-all text-2xl sm:text-3xl"><i class="fas fa-star"></i></label>
                                
                                <input type="radio" name="rating" id="star3" value="3" class="hidden peer/3">
                                <label for="star3" class="cursor-pointer text-gray-600 peer-checked/3:text-yellow-400 peer-checked/4:text-yellow-400 peer-checked/5:text-yellow-400 hover:text-yellow-400 transition-all text-2xl sm:text-3xl"><i class="fas fa-star"></i></label>
                                
                                <input type="radio" name="rating" id="star2" value="2" class="hidden peer/2">
                                <label for="star2" class="cursor-pointer text-gray-600 peer-checked/2:text-yellow-400 peer-checked/3:text-yellow-400 peer-checked/4:text-yellow-400 peer-checked/5:text-yellow-400 hover:text-yellow-400 transition-all text-2xl sm:text-3xl"><i class="fas fa-star"></i></label>
                                
                                <input type="radio" name="rating" id="star1" value="1" class="hidden peer/1">
                                <label for="star1" class="cursor-pointer text-gray-600 peer-checked/1:text-yellow-400 peer-checked/2:text-yellow-400 peer-checked/3:text-yellow-400 peer-checked/4:text-yellow-400 peer-checked/5:text-yellow-400 hover:text-yellow-400 transition-all text-2xl sm:text-3xl"><i class="fas fa-star"></i></label>
                            </div>
                            <span class="text-xs sm:text-sm text-gray-400 ml-2">Pilih jumlah bintang</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Ulasan Anda <span class="text-red-400">*</span></label>
                        <textarea name="content" rows="4" required class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition backdrop-blur-sm resize-none" placeholder="Bagikan pengalaman menarik Anda..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Foto</label>
                        <div class="relative">
                            <input type="file" name="image" id="testimonialImage" accept="image/*" class="hidden" onchange="previewTestimonialImage(this)">
                            <label for="testimonialImage" class="cursor-pointer w-full flex items-center justify-center gap-3 bg-white/5 border border-dashed border-white/30 rounded-xl p-4 text-gray-300 hover:bg-white/10 hover:border-white/50 transition-all group">
                                <i class="fas fa-camera text-xl group-hover:scale-110 transition-transform"></i>
                                <span id="testimonialImageLabel" class="text-sm sm:text-base">Upload Foto (Opsional)</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" id="btnSubmitTestimonial" class="w-full bg-gradient-to-r from-primary to-secondary hover:brightness-110 text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-primary/50 transition-all duration-300 transform hover:scale-[1.02] flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i>
                        Kirim Ulasan
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endif

<!-- ========== CTA SECTION ========== -->
<section class="py-16 sm:py-20 md:py-24 relative overflow-hidden" style="background: linear-gradient(135deg, var(--primary), var(--secondary))">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-64 sm:w-72 h-64 sm:h-72 bg-white rounded-full -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-80 sm:w-96 h-80 sm:h-96 bg-white rounded-full translate-x-1/2 translate-y-1/2"></div>
    </div>
    <div class="relative z-10 max-w-4xl mx-auto text-center px-4" data-aos="zoom-in">
        <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4 sm:mb-6">
            @if(isset($is_editor) && $is_editor)
                <div class="group relative inline-block w-full max-w-3xl mx-auto">
                    <textarea rows="1" 
                              class="editable-field text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-white bg-white/10 backdrop-blur-sm text-center w-full resize-none border-2 border-white/30 p-3 sm:p-4 rounded-xl focus:bg-white/20 focus:border-white/50 focus:outline-none transition-all placeholder-white/50 leading-tight"
                              data-sync="cta_text"
                              oninput="autoResize(this)"
                              placeholder="Siap Untuk Berbelanja?">{{ $landingPage->cta_text ?? 'Siap Untuk Berbelanja?' }}</textarea>
                    <div class="absolute -bottom-1 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-white/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
            @else
                {{ $landingPage->cta_text ?? 'Siap Untuk Berbelanja?' }}
            @endif
        </h2>
        <p class="text-white/90 text-base sm:text-lg md:text-xl mb-8 sm:mb-10">Hubungi kami sekarang dan dapatkan penawaran terbaik!</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            @if($landingPage->whatsapp_number)
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $landingPage->whatsapp_number) }}" 
               target="_blank"
               class="bg-white text-gray-900 px-6 sm:px-8 py-4 rounded-full font-bold text-base sm:text-lg hover:shadow-2xl transform hover:scale-105 transition-all inline-flex items-center justify-center gap-2 shadow-xl">
                <i class="fab fa-whatsapp text-green-500 text-xl"></i>
                {{ $landingPage->cta_button_text ?? 'Hubungi Kami' }}
            </a>
            @else
            <a href="#contact" class="bg-white text-gray-900 px-6 sm:px-8 py-4 rounded-full font-bold text-base sm:text-lg hover:shadow-2xl transform hover:scale-105 transition-all shadow-xl inline-flex items-center justify-center gap-2">
                <i class="fas fa-envelope"></i>
                {{ $landingPage->cta_button_text ?? 'Hubungi Kami' }}
            </a>
            @endif
        </div>
    </div>
</section>

<!-- ========== FOOTER ========== -->
<footer id="contact" class="bg-gray-900 text-white pt-16 sm:pt-20 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 sm:gap-12 mb-12 sm:mb-16">
            <!-- Brand -->
            <div data-aos="fade-up">
                <div class="flex items-center gap-3 mb-6">
                    @if($outlet->logo)
                        <img src="{{ Storage::url($outlet->logo) }}" alt="Logo" class="h-12 w-12 rounded-full bg-white p-1 object-cover shadow-lg">
                    @else
                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white font-bold text-xl shadow-lg">
                            {{ strtoupper(substr($outlet->name, 0, 1)) }}
                        </div>
                    @endif
                    <span class="text-xl sm:text-2xl font-bold">{{ $outlet->name }}</span>
                </div>
                <p class="text-gray-400 mb-6 text-sm sm:text-base">{{ $outlet->address ?? 'Alamat belum diatur' }}</p>
                
                <!-- Social Links -->
                <div class="flex gap-3">
                    @if($landingPage->social_media['instagram'] ?? false)
                    <a href="https://instagram.com/{{ $landingPage->social_media['instagram'] }}" target="_blank" class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/10 flex items-center justify-center hover:bg-gradient-to-br hover:from-primary hover:to-secondary transition-all transform hover:scale-110">
                        <i class="fab fa-instagram text-base sm:text-lg"></i>
                    </a>
                    @endif
                    @if($landingPage->social_media['tiktok'] ?? false)
                    <a href="https://tiktok.com/@{{ $landingPage->social_media['tiktok'] }}" target="_blank" class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/10 flex items-center justify-center hover:bg-gray-700 transition-all transform hover:scale-110">
                        <i class="fab fa-tiktok text-base sm:text-lg"></i>
                    </a>
                    @endif
                    @if($landingPage->whatsapp_number ?? false)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $landingPage->whatsapp_number) }}" target="_blank" class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/10 flex items-center justify-center hover:bg-green-500 transition-all transform hover:scale-110">
                        <i class="fab fa-whatsapp text-base sm:text-lg"></i>
                    </a>
                    @endif
                </div>
            </div>

            <!-- Contact Info -->
            <div data-aos="fade-up" data-aos-delay="100">
                <h4 class="font-bold text-base sm:text-lg mb-6">Hubungi Kami</h4>
                <ul class="space-y-4">
                    <li class="flex items-center gap-3 text-gray-400 text-sm sm:text-base hover:text-white transition-colors">
                        <i class="fas fa-phone w-5 text-center text-primary"></i>
                        <span>{{ $outlet->phone ?? '-' }}</span>
                    </li>
                    <li class="flex items-center gap-3 text-gray-400 text-sm sm:text-base hover:text-white transition-colors">
                        <i class="fas fa-envelope w-5 text-center text-primary"></i>
                        <span>{{ $outlet->email ?? '-' }}</span>
                    </li>
                    <li class="flex items-start gap-3 text-gray-400 text-sm sm:text-base hover:text-white transition-colors">
                        <i class="fas fa-map-marker-alt w-5 text-center mt-1 text-primary"></i>
                        <span>{{ $outlet->address ?? '-' }}</span>
                    </li>
                </ul>
            </div>

            <!-- Map -->
            <div class="h-48 sm:h-56 rounded-xl overflow-hidden shadow-2xl bg-gray-800 ring-2 ring-white/10" data-aos="fade-up" data-aos-delay="200" id="map-container">
                <div id="map" class="w-full h-full"></div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="border-t border-gray-800 pt-8 text-center text-gray-500 text-xs sm:text-sm">
            @if(isset($is_editor) && $is_editor)
                <div class="group relative inline-block w-full max-w-2xl mx-auto">
                    <textarea rows="1" 
                              class="editable-field text-xs sm:text-sm text-gray-400 bg-white/5 backdrop-blur-sm text-center w-full resize-none border-2 border-gray-700 p-3 rounded-lg focus:bg-white/10 focus:border-gray-600 focus:outline-none transition-all placeholder-gray-600"
                              placeholder="© 2024 Nama Outlet. All rights reserved."
                              data-sync="footer_text"
                              oninput="autoResize(this)">{{ $landingPage->footer_text ?? '© ' . date('Y') . ' ' . $outlet->name . '. All rights reserved.' }}</textarea>
                    <div class="absolute -bottom-1 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-gray-600 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
            @else
                {{ $landingPage->footer_text ?? '© ' . date('Y') . ' ' . $outlet->name . '. Powered by CuanFlow.' }}
            @endif
        </div>
    </div>
</footer>

</div>

<style>
    .nav-link.active {
        color: white !important;
    }
    .nav-link.active::after {
        width: 100% !important;
    }

    .mobile-menu {
        position: fixed;
        top: 0;
        right: -100%;
        width: 80%;
        max-width: 300px;
        height: 100vh;
        background: white;
        z-index: 100;
        transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: -10px 0 30px rgba(0,0,0,0.1);
    }
    .mobile-menu.active {
        right: 0;
    }
    .mobile-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        backdrop-blur: 4px;
        z-index: 90;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s;
    }
    .mobile-overlay.active {
        opacity: 1;
        visibility: visible;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.nav-link');

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (pageYOffset >= sectionTop - 150) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').includes(current)) {
                    link.classList.add('active');
                }
            });
            });
    });

    // Optimized Parallax Effect
    (function() {
        const parallaxBg = document.getElementById('heroParallaxBg');
        if (!parallaxBg) return;

        let ticking = false;

        function updateParallax() {
            const scrollY = window.pageYOffset;
            const viewportHeight = window.innerHeight;
            
            // Limit calculation to when hero is visible
            if (scrollY < viewportHeight * 1.5) {
                const speed = 0.35; // Fine-tuned speed for smoothness
                const yPos = -(scrollY * speed);
                parallaxBg.style.transform = `translate3d(0, ${yPos}px, 0)`;
            }
            ticking = false;
        }

        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(updateParallax);
                ticking = true;
            }
        }, { passive: true });

        // Initial positions
        updateParallax();
    })();
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        const overlay = document.getElementById('mobileOverlay');
        menu.classList.toggle('active');
        overlay.classList.toggle('active');
        document.body.classList.toggle('overflow-hidden');
    }

    // Font Sync Logic for Editor
    @if(isset($is_editor) && $is_editor)
    document.addEventListener('DOMContentLoaded', function() {
        const headingSelect = document.querySelector('select[name="font_heading"]');
        const bodySelect = document.querySelector('select[name="font_body"]');
        const wrapper = document.querySelector('.template-1-wrapper');

        function loadGoogleFont(fontName) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = `https://fonts.googleapis.com/css2?family=${fontName.replace(/ /g, '+')}:wght@300;400;500;600;700;800;900&display=swap`;
            document.head.appendChild(link);
        }

        if (headingSelect) {
            headingSelect.addEventListener('change', function() {
                const font = this.value;
                loadGoogleFont(font);
                if(wrapper) wrapper.style.setProperty('--heading-font', `'${font}', sans-serif`);
            });
        }

        if (bodySelect) {
            bodySelect.addEventListener('change', function() {
                const font = this.value;
                loadGoogleFont(font);
                if(wrapper) {
                    wrapper.style.setProperty('--body-font', `'${font}', sans-serif`);
                    wrapper.style.setProperty('font-family', `'${font}', sans-serif`, 'important');
                }
            });
        }
    });
    @endif
</script>
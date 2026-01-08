<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Landing Page - {{ $outlet->name }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Quill WYSIWYG Editor -->
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

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

        /* Hero Section */
        .hero-section {
            background-image: linear-gradient(135deg, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.4)), url('{{ $landingPage->hero_image ? Storage::url($landingPage->hero_image) : "https://images.unsplash.com/photo-1556740738-b6a63e27c4df?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: relative;
            min-height: 100vh;
            transition: background-image 0.5s ease;
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

        /* Floating Toolbar */
        .floating-toolbar {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            width: 340px;
            max-height: calc(100vh - 100px);
            overflow-y: auto;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .floating-toolbar.collapsed {
            transform: translateX(380px);
            opacity: 0;
        }

        .toolbar-trigger {
            position: fixed;
            top: 90px;
            right: 0;
            z-index: 999;
            background: var(--primary);
            color: white;
            padding: 12px 18px 12px 24px;
            border-radius: 50px 0 0 50px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            font-size: 14px;
        }
        
        .toolbar-trigger.hidden {
            transform: translateX(100%);
        }

        /* Editable Fields */
        .editable-field {
            border: 2px dashed transparent;
            padding: 12px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: text;
            background: rgba(255, 255, 255, 0.05);
        }
        .editable-field:hover {
            border-color: rgba(255, 255, 255, 0.4);
            background: rgba(255, 255, 255, 0.1);
        }
        .editable-field:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.2);
        }

        .editable-dark {
            border-color: rgba(0, 0, 0, 0.1);
            background: transparent;
        }
        .editable-dark:hover {
            border-color: var(--primary);
            background: rgba(79, 70, 229, 0.05);
        }
        .editable-dark:focus {
            background: white;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        /* Color Picker */
        .color-picker-wrapper {
            position: relative;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            overflow: hidden;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border: 3px solid white;
            transition: transform 0.2s;
        }
        .color-picker-wrapper:hover { transform: scale(1.1); }
        .color-picker-wrapper input[type="color"] {
            position: absolute;
            width: 200%;
            height: 200%;
            left: -50%;
            top: -50%;
            cursor: pointer;
        }

        /* Upload Button */
        .upload-btn {
            position: absolute;
            bottom: 30px;
            right: 30px;
            z-index: 50;
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

        /* Quill Editor Customization */
        .ql-container { font-family: 'Inter', sans-serif; }
        .ql-editor { min-height: 120px; padding: 16px; }
        .ql-toolbar { border-radius: 8px 8px 0 0 !important; background: #F9FAFB; }
        .ql-container { border-radius: 0 0 8px 8px !important; }

        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
        }

        /* Product Card */
        .product-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .product-card.selected {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(79, 70, 229, 0.3);
        }

        /* Section Edit Indicator */
        .section-editable {
            position: relative;
        }
        .section-editable::before {
            content: 'Klik untuk edit';
            position: absolute;
            top: -30px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--primary);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            opacity: 0;
            transition: opacity 0.2s;
            pointer-events: none;
        }
        .section-editable:hover::before { opacity: 1; }

        /* Smooth Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #F3F4F6; }
        ::-webkit-scrollbar-thumb { background: #D1D5DB; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #9CA3AF; }

        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .animate-float { animation: float 3s ease-in-out infinite; }
    </style>
</head>
<body class="bg-gray-50">

    <form action="{{ route('landing-pages.update', $outlet->id) }}" method="POST" enctype="multipart/form-data" id="editForm">
        @csrf
        @method('PUT')

        <!-- ========== FLOATING TOOLBAR ========== -->
        <div class="floating-toolbar" id="toolbar">
            <!-- Header -->
            <div class="sticky top-0 bg-white/95 backdrop-blur-xl px-5 py-4 border-b border-gray-100 flex justify-between items-center z-10">
                <div>
                    <h3 class="font-bold text-gray-900">Panel Pengaturan</h3>
                    <p class="text-xs text-gray-500">Kustomisasi landing page</p>
                </div>
                <button type="button" onclick="toggleToolbar()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition">
                    <i class="fas fa-times text-gray-500"></i>
                </button>
            </div>

            <div class="p-5 space-y-6">
                <!-- Theme Colors -->
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 block">Tema Warna</label>
                    <div class="flex items-center gap-6">
                        <div class="text-center">
                            <div class="color-picker-wrapper mx-auto mb-2" id="primaryColorPicker" style="background-color: {{ $landingPage->primary_color }}">
                                <input type="color" name="primary_color" value="{{ $landingPage->primary_color }}" onchange="updateColor(this.value, 'primary')">
                            </div>
                            <span class="text-xs text-gray-600 font-medium">Utama</span>
                        </div>
                        <div class="text-center">
                            <div class="color-picker-wrapper mx-auto mb-2" id="secondaryColorPicker" style="background-color: {{ $landingPage->secondary_color }}">
                                <input type="color" name="secondary_color" value="{{ $landingPage->secondary_color }}" onchange="updateColor(this.value, 'secondary')">
                            </div>
                            <span class="text-xs text-gray-600 font-medium">Aksen</span>
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 block">Media Sosial</label>
                    <div class="space-y-2">
                        <div class="flex items-center bg-gradient-to-r from-pink-50 to-purple-50 rounded-xl px-4 py-2.5 border border-pink-100 focus-within:ring-2 focus-within:ring-pink-500/30 transition">
                            <i class="fab fa-instagram text-pink-500 text-lg"></i>
                            <input type="text" name="social_media[instagram]" value="{{ $landingPage->social_media['instagram'] ?? '' }}" 
                                   placeholder="Username Instagram" 
                                   class="flex-1 bg-transparent border-0 ml-3 text-sm focus:outline-none">
                        </div>
                        <div class="flex items-center bg-gray-50 rounded-xl px-4 py-2.5 border border-gray-200 focus-within:ring-2 focus-within:ring-gray-500/30 transition">
                            <i class="fab fa-tiktok text-black text-lg"></i>
                            <input type="text" name="social_media[tiktok]" value="{{ $landingPage->social_media['tiktok'] ?? '' }}" 
                                   placeholder="Username TikTok" 
                                   class="flex-1 bg-transparent border-0 ml-3 text-sm focus:outline-none">
                        </div>
                        <div class="flex items-center bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl px-4 py-2.5 border border-green-100 focus-within:ring-2 focus-within:ring-green-500/30 transition">
                            <i class="fab fa-whatsapp text-green-500 text-lg"></i>
                            <input type="text" name="whatsapp_number" value="{{ $landingPage->whatsapp_number ?? '' }}" 
                                   placeholder="Nomor WhatsApp (08xxx)" 
                                   class="flex-1 bg-transparent border-0 ml-3 text-sm focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- CTA Button -->
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 block">Tombol CTA</label>
                    <input type="text" name="cta_button_text" value="{{ $landingPage->cta_button_text ?? 'Belanja Sekarang' }}" 
                           placeholder="Text tombol CTA" 
                           class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                </div>

                <!-- Actions -->
                <div class="space-y-3 pt-4 border-t border-gray-100">
                    <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-indigo-200 transform hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('landing-pages.index') }}" class="block w-full py-3 bg-white border border-gray-200 text-gray-600 rounded-xl font-medium text-sm text-center hover:bg-gray-50 transition">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
                    </a>
                    <a href="{{ route('landing-pages.show', [$outlet->id, Str::slug($outlet->name)]) }}" target="_blank" class="block w-full py-3 bg-gray-900 text-white rounded-xl font-medium text-sm text-center hover:bg-gray-800 transition">
                        <i class="fas fa-external-link-alt mr-2"></i> Lihat Preview
                    </a>
                </div>
            </div>
        </div>

        <!-- Toolbar Trigger Button -->
        <div class="toolbar-trigger hidden" id="toolbarTrigger" onclick="toggleToolbar()">
            <i class="fas fa-cog"></i>
            <span>Pengaturan</span>
        </div>

        <!-- ========== NAVBAR ========== -->
        <nav class="navbar fixed w-full z-50 px-4 py-3" id="navbar">
            <div class="max-w-7xl mx-auto">
                <div class="flex justify-between items-center">
                    <!-- Brand -->
                    <div class="flex items-center gap-3">
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
                    <div class="hidden md:flex items-center gap-8">
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
            <!-- Upload Button -->
            <div class="upload-btn">
                <label for="hero_image" class="cursor-pointer inline-flex items-center gap-2 bg-white text-gray-800 px-5 py-3 rounded-full shadow-xl font-semibold text-sm hover:bg-gray-100 transition transform hover:scale-105">
                    <i class="fas fa-camera text-indigo-600"></i>
                    <span>Ganti Background</span>
                </label>
                <input type="file" name="hero_image" id="hero_image" class="hidden" accept="image/*" onchange="previewImage(this)">
            </div>

            <div class="relative z-10 max-w-4xl mx-auto px-6">
                <!-- Tagline -->
                <div class="mb-4">
                    <input type="text" name="tagline_text" 
                           value="{{ $landingPage->tagline_text ?? 'Welcome to' }}" 
                           class="editable-field text-lg md:text-xl font-medium text-white/80 bg-transparent text-center w-full uppercase tracking-widest"
                           placeholder="Tagline...">
                </div>

                <!-- Hero Title -->
                <div class="mb-6 section-editable">
                    <textarea name="hero_title" rows="1"
                              class="editable-field text-4xl sm:text-5xl md:text-7xl font-extrabold text-white bg-transparent text-center w-full resize-none overflow-hidden leading-tight"
                              placeholder="Judul Utama"
                              oninput="autoResize(this)">{{ $landingPage->hero_title }}</textarea>
                </div>

                <!-- Hero Subtitle -->
                <div class="mb-10 section-editable">
                    <textarea name="hero_subtitle" rows="1"
                              class="editable-field text-lg md:text-2xl font-light text-white/90 bg-transparent text-center w-full resize-none overflow-hidden"
                              placeholder="Sub judul"
                              oninput="autoResize(this)">{{ $landingPage->hero_subtitle }}</textarea>
                </div>

                <!-- CTA Button Preview -->
                <div class="flex flex-wrap gap-4 justify-center">
                    <button type="button" class="cta-primary bg-white text-gray-900 px-8 py-4 rounded-full font-bold text-lg shadow-2xl hover:shadow-3xl transform hover:scale-105 transition-all duration-300">
                        {{ $landingPage->cta_button_text ?? 'Belanja Sekarang' }}
                    </button>
                    <a href="#about" class="border-2 border-white/30 text-white px-8 py-4 rounded-full font-semibold hover:bg-white/10 transition">
                        Pelajari Lebih
                    </a>
                </div>

                <!-- Scroll Indicator -->
                <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 animate-bounce">
                    <i class="fas fa-chevron-down text-white/50 text-2xl"></i>
                </div>
            </div>
        </section>

        <!-- ========== STATS SECTION ========== -->
        <section class="py-16 bg-white relative -mt-16 mx-4 md:mx-auto max-w-6xl rounded-2xl shadow-2xl z-20" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center divide-y md:divide-y-0 md:divide-x divide-gray-200">
                <div class="p-6 flex flex-col items-center justify-center">
                    <div class="text-4xl font-bold mb-2" style="color: var(--primary)">150+</div>
                    <div class="text-gray-500 uppercase tracking-widest text-sm font-semibold">Total Penjualan</div>
                </div>
                <div class="p-6 flex flex-col items-center justify-center">
                    <div class="text-4xl font-bold mb-2" style="color: var(--primary)">{{ count($products) }}</div>
                    <div class="text-gray-500 uppercase tracking-widest text-sm font-semibold">Produk Tersedia</div>
                </div>
                <div class="p-6 flex flex-col items-center justify-center">
                    <div class="text-4xl font-bold mb-2" style="color: var(--primary)">100%</div>
                    <div class="text-gray-500 uppercase tracking-widest text-sm font-semibold">Kepuasan Pelanggan</div>
                </div>
            </div>
        </section>

        <!-- ========== ABOUT SECTION ========== -->
        <section id="about" class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-2 gap-16 items-center">
                    <!-- Image -->
                    <div class="relative group">
                        <div class="absolute -inset-4 bg-gradient-to-r from-indigo-100 to-purple-100 rounded-3xl opacity-60 blur-2xl"></div>
                        <img id="aboutImagePreview" src="{{ $landingPage->about_image ? Storage::url($landingPage->about_image) : 'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80' }}" 
                             alt="About" 
                             class="relative rounded-2xl shadow-2xl w-full object-cover aspect-video md:aspect-square">
                        <div class="absolute bottom-4 right-4 z-10">
                            <label for="about_image" class="cursor-pointer inline-flex items-center gap-2 bg-white/90 backdrop-blur text-gray-800 px-4 py-2 rounded-full shadow-lg font-semibold text-xs hover:bg-white transition transform hover:scale-105">
                                <i class="fas fa-camera text-indigo-600"></i>
                                <span>Ganti Foto About</span>
                            </label>
                            <input type="file" name="about_image" id="about_image" class="hidden" accept="image/*" onchange="previewAboutImage(this)">
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="space-y-8">
                        <div>
                            <span class="text-sm font-bold uppercase tracking-wider" style="color: var(--primary)">Tentang Kami</span>
                            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2">Kenali Lebih Dekat</h2>
                            <div class="w-20 h-1 rounded-full mt-4" style="background: var(--primary)"></div>
                        </div>
                        
                        <!-- WYSIWYG About -->
                        <div>
                            <div id="aboutEditor" class="bg-gray-50 rounded-xl border border-gray-200"></div>
                            <input type="hidden" name="about_text" id="aboutText" value="{{ $landingPage->about_text }}">
                        </div>

                        <!-- Vision & Mission -->
                        <div class="grid gap-4">
                            <div class="flex items-start p-4 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl border border-indigo-100">
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center text-white shrink-0" style="background: var(--primary)">
                                    <i class="fas fa-eye"></i>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h4 class="font-bold text-gray-900">Visi</h4>
                                    <textarea name="vision_text" rows="2" 
                                              class="editable-dark w-full text-gray-600 text-sm resize-none mt-1"
                                              placeholder="Tulis visi..."
                                              oninput="autoResize(this)">{{ $landingPage->vision_text }}</textarea>
                                </div>
                            </div>
                            <div class="flex items-start p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-gray-200">
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center text-white shrink-0" style="background: var(--secondary)">
                                    <i class="fas fa-rocket"></i>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h4 class="font-bold text-gray-900">Misi</h4>
                                    <textarea name="mission_text" rows="2" 
                                              class="editable-dark w-full text-gray-600 text-sm resize-none mt-1"
                                              placeholder="Tulis misi..."
                                              oninput="autoResize(this)">{{ $landingPage->mission_text }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== PRODUCTS SECTION ========== -->
        <section id="products" class="py-24 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="text-center mb-16">
                    <span class="text-sm font-bold uppercase tracking-wider" style="color: var(--primary)">Produk Kami</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2">Pilih Produk Unggulan</h2>
                    <div class="w-20 h-1 rounded-full mx-auto mt-4" style="background: var(--primary)"></div>
                    <p class="mt-6 text-gray-500 bg-amber-50 inline-flex items-center gap-2 px-4 py-2 rounded-full border border-amber-200 text-sm">
                        <i class="fas fa-info-circle text-amber-500"></i>
                        Pilih maksimal 3 produk untuk ditampilkan di landing page
                    </p>
                </div>

                <!-- Products Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @forelse($products as $product)
                        <label class="relative cursor-pointer group">
                            <input type="checkbox" name="selected_product_ids[]" value="{{ $product->id }}" 
                                   class="peer sr-only product-checkbox"
                                   {{ in_array($product->id, $landingPage->selected_product_ids ?? []) ? 'checked' : '' }}>
                            
                            <div class="product-card bg-white rounded-2xl overflow-hidden border border-gray-100 peer-checked:border-indigo-500 peer-checked:selected peer-checked:ring-2 peer-checked:ring-indigo-500">
                                <div class="h-56 bg-gray-100 relative overflow-hidden">
                                    @if($product->image)
                                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                                            <i class="fas fa-image text-5xl"></i>
                                        </div>
                                    @endif
                                    
                                    <!-- Selection Badge -->
                                    <div class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white shadow-lg flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-all transform scale-50 peer-checked:scale-100">
                                        <i class="fas fa-check text-indigo-600"></i>
                                    </div>
                                </div>
                                <div class="p-5">
                                    <h3 class="font-bold text-gray-900 text-lg truncate">{{ $product->name }}</h3>
                                    <p class="font-bold text-lg mt-1" style="color: var(--primary)">
                                        Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </label>
                    @empty
                        <div class="col-span-3 text-center py-16 text-gray-400">
                            <i class="fas fa-box-open text-6xl mb-4"></i>
                            <p>Belum ada produk</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        </section>

        <!-- ========== TESTIMONIALS SELECTION ========== -->
        <section class="py-20 bg-gray-50 section-editable">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <span class="text-primary font-semibold tracking-wider uppercase text-sm">Apa Kata Mereka</span>
                    <h2 class="text-3xl md:text-5xl font-bold mt-2 text-gray-900">Testimoni Pilihan</h2>
                    <div class="w-24 h-1 bg-primary mx-auto mt-6 rounded-full"></div>
                    <p class="mt-4 text-gray-500 max-w-2xl mx-auto">
                        Pilih ulasan terbaik dari pelanggan Anda untuk ditampilkan di halaman utama.
                        <a href="{{ route('testimonials.index') }}" target="_blank" class="text-indigo-600 hover:underline font-semibold ml-1">
                            Kelola Testimoni <i class="fas fa-external-link-alt text-xs"></i>
                        </a>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @forelse($testimonials as $testimonial)
                        <label class="cursor-pointer group relative">
                            <input type="checkbox" name="selected_testimonial_ids[]" value="{{ $testimonial->id }}"
                                   class="hidden peer testimonial-checkbox"
                                   {{ in_array($testimonial->id, $landingPage->selected_testimonial_ids ?? []) ? 'checked' : '' }}>
                            
                            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 h-full transition-all duration-300 peer-checked:border-primary peer-checked:ring-2 peer-checked:ring-primary peer-checked:shadow-xl group-hover:shadow-md">
                                <div class="absolute top-4 right-4 text-gray-300 peer-checked:text-primary transition-colors">
                                    <i class="fas fa-check-circle text-2xl"></i>
                                </div>

                                <div class="flex items-center gap-4 mb-6">
                                    @if($testimonial->image)
                                        <img src="{{ Storage::url($testimonial->image) }}" class="w-14 h-14 rounded-full object-cover">
                                    @else
                                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 font-bold text-xl">
                                            {{ strtoupper(substr($testimonial->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <h4 class="font-bold text-gray-900">{{ $testimonial->name }}</h4>
                                        <p class="text-sm text-gray-500">{{ $testimonial->role ?? 'Pelanggan' }}</p>
                                    </div>
                                </div>

                                <div class="flex text-yellow-400 text-sm mb-4">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $testimonial->rating ? '' : 'text-gray-200' }}"></i>
                                    @endfor
                                </div>

                                <p class="text-gray-600 leading-relaxed italic">
                                    "{{ Str::limit($testimonial->content, 120) }}"
                                </p>
                            </div>
                        </label>
                    @empty
                        <div class="col-span-full text-center py-12 bg-white rounded-2xl border border-dashed border-gray-300">
                            <i class="far fa-comment-dots text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">Belum ada testimoni publik yang tersedia.</p>
                            <p class="text-sm text-gray-400 mt-1">Pastikan testimoni sudah disetujui/di-publish di menu Testimoni.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- ========== CTA SECTION ========== -->
        <section class="py-24 relative overflow-hidden" style="background: linear-gradient(135deg, var(--primary), var(--secondary))">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 left-0 w-72 h-72 bg-white rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full translate-x-1/2 translate-y-1/2"></div>
            </div>
            <div class="relative z-10 max-w-4xl mx-auto text-center px-4">
                <h2 class="text-3xl md:text-5xl font-bold text-white mb-6">
                    <textarea name="cta_text" rows="1" 
                              class="editable-field text-3xl md:text-5xl font-bold text-white bg-transparent text-center w-full resize-none"
                              placeholder="Text CTA..."
                              oninput="autoResize(this)">{{ $landingPage->cta_text ?? 'Siap Untuk Berbelanja?' }}</textarea>
                </h2>
                <p class="text-white/80 text-lg mb-10">Hubungi kami sekarang dan dapatkan penawaran terbaik!</p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <button type="button" class="bg-white text-gray-900 px-8 py-4 rounded-full font-bold hover:shadow-xl transform hover:scale-105 transition-all">
                        {{ $landingPage->cta_button_text ?? 'Hubungi Kami' }}
                    </button>
                </div>
            </div>
        </section>

        <!-- ========== CONTACT / FOOTER ========== -->
        <footer id="contact" class="bg-gray-900 text-white pt-20 pb-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-3 gap-12 mb-16">
                    <!-- Brand -->
                    <div>
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
                            <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-pink-500 transition">
                                <i class="fab fa-instagram"></i>
                            </a>
                            @endif
                            @if($landingPage->social_media['tiktok'] ?? false)
                            <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-gray-700 transition">
                                <i class="fab fa-tiktok"></i>
                            </a>
                            @endif
                            @if($landingPage->whatsapp_number ?? false)
                            <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-green-500 transition">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            @endif
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div>
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

                    <!-- Map Placeholder -->
                    <div class="h-48 bg-gray-800 rounded-xl flex items-center justify-center text-gray-600 border border-gray-700">
                        <div class="text-center">
                            <i class="fas fa-map-marker-alt text-4xl mb-2"></i>
                            <p class="text-sm">Google Maps</p>
                        </div>
                    </div>
                </div>

                <!-- Footer Bottom -->
                <div class="border-t border-gray-800 pt-8 text-center text-gray-500 text-sm">
                    <textarea name="footer_text" rows="1" 
                              class="editable-field text-sm text-gray-400 bg-transparent text-center w-full max-w-md mx-auto resize-none"
                              placeholder="© 2024 Nama Outlet. All rights reserved."
                              oninput="autoResize(this)">{{ $landingPage->footer_text ?? '© ' . date('Y') . ' ' . $outlet->name . '. All rights reserved.' }}</textarea>
                </div>
            </div>
        </footer>

    </form>

    <script>
        // ========== QUILL WYSIWYG EDITOR ==========
        const aboutQuill = new Quill('#aboutEditor', {
            theme: 'snow',
            placeholder: 'Tulis deskripsi tentang bisnis Anda disini...',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link'],
                    ['clean']
                ]
            }
        });

        // Load existing content
        aboutQuill.root.innerHTML = `{!! addslashes($landingPage->about_text ?? '') !!}`;

        // Sync content before form submit
        document.getElementById('editForm').addEventListener('submit', function() {
            document.getElementById('aboutText').value = aboutQuill.root.innerHTML;
        });

        // ========== AUTO RESIZE TEXTAREA ==========
        function autoResize(el) {
            el.style.height = 'auto';
            el.style.height = el.scrollHeight + 'px';
        }

        // Init on load
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('textarea').forEach(textarea => autoResize(textarea));
        });

        // ========== TOGGLE TOOLBAR ==========
        function toggleToolbar() {
            const toolbar = document.getElementById('toolbar');
            const trigger = document.getElementById('toolbarTrigger');
            toolbar.classList.toggle('collapsed');
            trigger.classList.toggle('hidden');
        }

        // ========== MOBILE MENU ==========
        function toggleMobileMenu() {
            document.getElementById('mobileMenu').classList.toggle('active');
            document.getElementById('mobileOverlay').classList.toggle('active');
        }

        // ========== COLOR UPDATES ==========
        function updateColor(value, type) {
            document.documentElement.style.setProperty(type === 'primary' ? '--primary' : '--secondary', value);
            document.getElementById(type + 'ColorPicker').style.backgroundColor = value;
        }

        // ========== PREVIEW BACKGROUND IMAGE ==========
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.hero-section').style.backgroundImage = 
                        `linear-gradient(135deg, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.4)), url('${e.target.result}')`;
                    
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Background berhasil diubah',
                        showConfirmButton: false,
                        timer: 2000
                    });
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // ========== PREVIEW ABOUT IMAGE ==========
        function previewAboutImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('aboutImagePreview').src = e.target.result;
                    
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Foto About berhasil diubah',
                        showConfirmButton: false,
                        timer: 2000
                    });
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // ========== PRODUCT SELECTION (MAX 3) ==========
        document.querySelectorAll('.product-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checked = document.querySelectorAll('.product-checkbox:checked');
                if (checked.length > 3) {
                    this.checked = false;
                    Swal.fire({
                        icon: 'warning',
                        title: 'Maksimal 3 Produk',
                        text: 'Anda hanya dapat memilih maksimal 3 produk unggulan.',
                        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary').trim(),
                    });
                }
                // Update selected styling
                this.closest('label').querySelector('.product-card').classList.toggle('selected', this.checked);
            });
        });

        // ========== NAVBAR SCROLL EFFECT ==========
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // ========== SUCCESS MESSAGE ==========
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil Disimpan!',
                text: '{{ session('success') }}',
                confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary').trim(),
                timer: 3000
            });
        @endif
    </script>
</body>
</html>

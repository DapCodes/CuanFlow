<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Landing Page - {{ $outlet->name }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: 'var(--primary)',
                        secondary: 'var(--secondary)',
                    }
                }
            }
        }
    </script>
    
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

        /* Scrollbar Hide */
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

        .collapsed-toolbar {
            transform: translate(-50%, calc(100% + 20px)) !important;
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
        
        <!-- Global Hidden File Inputs -->
        <input type="file" name="hero_image" id="hero_image" class="hidden" accept="image/*" onchange="previewImage(this)">
        <input type="file" name="about_image" id="about_image" class="hidden" accept="image/*" onchange="previewAboutImage(this)">

        <!-- ========== FLOATING EDITOR TOOLBAR ========== -->
        <div id="editorToolbar" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[100] w-[95%] max-w-lg transition-all duration-500 transform translate-y-0">
            <!-- Toolbar Toggle Button -->
            <button type="button" onclick="toggleToolbar()" class="absolute -top-12 left-1/2 -translate-x-1/2 bg-white shadow-xl rounded-full px-6 py-2 text-xs font-bold uppercase tracking-widest border border-gray-100 flex items-center gap-2 hover:bg-gray-50 transition active:scale-95 group hover:-translate-y-0.5">
                <span id="toolbarStatusText">Sembunyikan Panel</span>
                <i id="toolbarToggleIcon" class="fas fa-chevron-down group-hover:translate-y-0.5 transition-transform"></i>
            </button>

            <div class="bg-white/90 backdrop-blur-xl rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.3)] border border-white/20 p-2 overflow-hidden">
                <div id="toolbarContent" class="max-h-[70vh] overflow-y-auto px-4 py-6 space-y-8 scrollbar-hide">
                    <!-- Toolbar Header -->
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-8 bg-primary rounded-full"></div>
                            <h3 class="font-black text-xl tracking-tighter uppercase italic">Editor Panel</h3>
                        </div>
                        <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-6 py-2 rounded-full font-bold text-sm shadow-lg shadow-primary/30 transition active:scale-95">
                            <i class="fas fa-save mr-2"></i> Simpan
                        </button>
                    </div>
                <!-- Template Selection -->
                <div class="bg-primary/5 rounded-xl p-4 border border-primary/20">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 block">Layout Template</label>
                    <div class="grid grid-cols-2 gap-3">
                        @php
                            $templates = [
                                1 => ['name' => 'Modern', 'icon' => 'fa-bolt'],
                                2 => ['name' => 'Minimal', 'icon' => 'fa-align-left'],
                                3 => ['name' => 'Dark Bold', 'icon' => 'fa-moon'],
                                4 => ['name' => 'Playful', 'icon' => 'fa-shapes'],
                                5 => ['name' => 'Elegant', 'icon' => 'fa-gem'],
                            ];
                        @endphp
                        @foreach($templates as $id => $t)
                        <label class="cursor-pointer">
                            <input type="radio" name="template_id" value="{{ $id }}" class="peer sr-only" {{ ($landingPage->template_id ?? 1) == $id ? 'checked' : '' }}>
                            <div class="flex flex-col items-center justify-center p-3 rounded-lg bg-white border border-gray-200 text-gray-400 hover:border-primary/50 hover:text-primary peer-checked:bg-white peer-checked:border-primary peer-checked:text-primary peer-checked:shadow-md transition gap-2">
                                <i class="fas {{ $t['icon'] }} text-lg"></i>
                                <span class="text-xs font-bold">{{ $t['name'] }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Fonts Selection -->
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 block">Tipografi</label>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-[10px] uppercase text-gray-400 font-bold mb-1 block">Heading</label>
                            <select name="font_heading" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                                @foreach(['Inter', 'Poppins', 'Playfair Display', 'Montserrat', 'Oswald', 'Raleway', 'Merriweather', 'Roboto Slab'] as $font)
                                    <option value="{{ $font }}" {{ ($landingPage->font_heading ?? 'Inter') == $font ? 'selected' : '' }}>{{ $font }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] uppercase text-gray-400 font-bold mb-1 block">Body</label>
                            <select name="font_body" class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                                @foreach(['Inter', 'Roboto', 'Open Sans', 'Lato', 'Source Sans Pro', 'Merriweather', 'PT Serif'] as $font)
                                    <option value="{{ $font }}" {{ ($landingPage->font_body ?? 'Inter') == $font ? 'selected' : '' }}>{{ $font }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

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

                <!-- Hero & Footer Content -->
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 space-y-4">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Konten Teks</label>
                    
                    <div>
                        <label class="text-[10px] uppercase text-gray-400 font-bold mb-1 block">Tagline</label>
                        <input type="text" name="tagline_text" value="{{ $landingPage->tagline_text }}" 
                               placeholder="Tagline kecil (di atas judul)" data-sync="tagline_text"
                               class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                    </div>

                    <div>
                        <label class="text-[10px] uppercase text-gray-400 font-bold mb-1 block">Judul Utama (Hero)</label>
                        <textarea name="hero_title" rows="2" data-sync="hero_title"
                               class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary resize-none">{{ $landingPage->hero_title }}</textarea>
                    </div>

                    <div>
                        <label class="text-[10px] uppercase text-gray-400 font-bold mb-1 block">Sub Judul (Hero)</label>
                        <textarea name="hero_subtitle" rows="2" data-sync="hero_subtitle"
                               class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary resize-none">{{ $landingPage->hero_subtitle }}</textarea>
                    </div>

                    <div class="pt-2 border-t border-gray-200/50">
                        <label class="text-[10px] uppercase text-gray-400 font-bold mb-1 block">Teks Ajakan (CTA Section)</label>
                        <textarea name="cta_text" rows="2" data-sync="cta_text"
                               class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary resize-none"
                               placeholder="Contoh: Siap untuk Level Up?">{{ $landingPage->cta_text }}</textarea>
                    </div>

                    <div>
                        <label class="text-[10px] uppercase text-gray-400 font-bold mb-1 block">Teks Kaki (Footer)</label>
                        <textarea name="footer_text" rows="2" data-sync="footer_text"
                               class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary resize-none"
                               placeholder="Contoh: © 2024 Nama Bisnis">{{ $landingPage->footer_text }}</textarea>
                    </div>
                </div>

                <!-- Social Media -->
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 block">Media Sosial</label>
                    <div class="space-y-2">
                        <div class="flex items-center bg-primary/5 rounded-xl px-4 py-2.5 border border-primary/10 focus-within:ring-2 focus-within:ring-primary/30 transition">
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

                <!-- Product Selection -->
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 block">
                        <i class="fas fa-box text-primary mr-1"></i> Produk Unggulan (Max 3)
                    </label>
                    <div class="space-y-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                        @forelse($products as $product)
                        <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer transition group">
                            <input type="checkbox" 
                                   name="selected_product_ids[]" 
                                   value="{{ $product->id }}" 
                                   class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary master-product-checkbox"
                                   {{ in_array($product->id, $landingPage->selected_product_ids ?? []) ? 'checked' : '' }}>
                            <div class="w-8 h-8 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0">
                                @if($product->image)
                                    <img src="{{ Storage::url($product->image) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fas fa-box text-xs"></i></div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</p>
                                <p class="text-xs text-gray-500">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                            </div>
                        </label>
                        @empty
                        <p class="text-xs text-gray-400 text-center py-4">Belum ada produk di outlet ini.</p>
                        @endforelse
                    </div>
                    <p class="text-[10px] text-gray-400 mt-2"><i class="fas fa-info-circle"></i> Pilih maksimal 3 produk untuk ditampilkan.</p>
                </div>

                <!-- Testimonial Selection -->
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 block">
                        <i class="fas fa-comment-dots text-primary mr-1"></i> Testimoni
                    </label>
                    <div class="space-y-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                        @forelse($testimonials as $testimonial)
                        <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer transition group">
                            <input type="checkbox" 
                                   name="selected_testimonial_ids[]" 
                                   value="{{ $testimonial->id }}" 
                                   class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary master-testimonial-checkbox"
                                   {{ in_array($testimonial->id, $landingPage->selected_testimonial_ids ?? []) ? 'checked' : '' }}>
                            <div class="w-8 h-8 rounded-full bg-gray-100 overflow-hidden flex-shrink-0">
                                @if($testimonial->photo)
                                    <img src="{{ Storage::url($testimonial->photo) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fas fa-user text-xs"></i></div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $testimonial->name }}</p>
                                <div class="flex items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star text-[8px] {{ $i <= $testimonial->rating ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                                    @endfor
                                </div>
                            </div>
                        </label>
                        @empty
                        <p class="text-xs text-gray-400 text-center py-4">Belum ada testimoni yang dipublish.</p>
                        @endforelse
                    </div>
                </div>

                <!-- CTA Button -->
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 block">Tombol CTA</label>
                    <input type="text" name="cta_button_text" id="master_cta_button_text" value="{{ $landingPage->cta_button_text ?? 'Belanja Sekarang' }}" 
                           placeholder="Text tombol CTA" data-sync="cta_button_text"
                           class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                </div>

                <!-- Actions -->
                <div class="space-y-3 pt-4 border-t border-gray-100">
                    <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-primary to-secondary hover:brightness-110 text-white rounded-xl font-bold text-sm shadow-lg shadow-primary/20 transform hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
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
        </div>

        <!-- ========== TEMPLATE PREVIEW AREA ========== -->
        @foreach(range(1, 5) as $pt_id)
            <div id="template-view-{{ $pt_id }}" class="template-view {{ ($landingPage->template_id ?? 1) == $pt_id ? '' : 'hidden' }}">
                @include('landing.templates.template_' . $pt_id, [
                    'is_editor' => true,
                    'outlet' => $outlet,
                    'landingPage' => $landingPage,
                    'products' => $products,
                    'testimonials' => $testimonials,
                    'displaySales' => $displaySales
                ])
            </div>
        @endforeach

    </form>

    <script>
        // ========== TEMPLATE SWITCHING ==========
        function switchTemplate(templateId) {
            // Hide all
            document.querySelectorAll('.template-view').forEach(el => {
                el.classList.add('hidden');
                // Disable all inputs in hidden templates to prevent them from overwriting data in POST
                el.querySelectorAll('input, textarea, select').forEach(input => {
                    input.disabled = true;
                });
            });
            // Show selected
            const activeView = document.getElementById('template-view-' + templateId);
            if(activeView) {
                activeView.classList.remove('hidden');
                // Enable inputs in the active template
                activeView.querySelectorAll('input, textarea, select').forEach(input => {
                    input.disabled = false;
                });
            }
        }

        document.querySelectorAll('input[name="template_id"]').forEach(radio => {
            radio.addEventListener('change', function() {
                switchTemplate(this.value);
            });
        });

        // ========== DATA SYNCING ACROSS TEMPLATES ==========
        function syncInputs(source) {
            const name = source.getAttribute('name');
            const val = source.value;
            
            // Skip array inputs like selected_product_ids[]
            if (name && name.includes('[]')) return;

            // Sync by name
            if (name) {
                document.querySelectorAll(`[name="${name}"]`).forEach(el => {
                    if(el !== source) {
                        if (el.type === 'checkbox') {
                            el.checked = source.checked;
                        } else if (el.type === 'radio') {
                            if (el.value === val) {
                                el.checked = source.checked;
                            }
                        } else {
                            el.value = val;
                        }
                    }
                });
            }
            
            // Sync by data-sync attribute
            if(source.getAttribute('data-sync')) {
                 const syncKey = source.getAttribute('data-sync');
                 document.querySelectorAll(`[data-sync="${syncKey}"]`).forEach(el => {
                    if(el !== source) {
                        if (el.type === 'checkbox') {
                            el.checked = source.checked;
                        } else if (el.type === 'radio') {
                            if (el.value === val) {
                                el.checked = source.checked;
                            }
                        } else {
                            el.value = val;
                            if(el.tagName === 'TEXTAREA') autoResize(el);
                        }
                    }
                 });
            }
        }

        // Attach sync listeners
        document.addEventListener('input', function(e) {
            // Only sync text-related inputs via 'input' event
            const type = e.target.type;
            if (type === 'text' || type === 'textarea' || e.target.tagName === 'TEXTAREA') {
                if(e.target.hasAttribute('name') || e.target.hasAttribute('data-sync')) {
                    syncInputs(e.target);
                }
            }
        });

        // Use 'change' listener for checkboxes and radios
        document.addEventListener('change', function(e) {
            const type = e.target.type;
            if (type === 'checkbox' || type === 'radio' || e.target.tagName === 'SELECT') {
                if(e.target.hasAttribute('name') || e.target.hasAttribute('data-sync')) {
                    syncInputs(e.target);
                }
            }
        });

        // ========== QUILL WYSIWYG EDITOR ==========
        // Note: Quill needs unique ID. Since we are in a loop, we might have multiple #aboutEditor.
        // We need to initialize Quill for the CURRENTLY VISIBLE template only, or all of them?
        // Simpler: Just initialize all unique IDs if we can. 
        // Or update template logic to not duplicate IDs?
        // Templates use id="aboutEditor". This is bad for includes.
        // We should change templates to use class="about-editor-container" and unique IDs?
        // For now, let's assume specific ID per template or we fix it in JS.
        
       const quills = [];
       document.querySelectorAll('#aboutEditor').forEach((el, index) => {
           // We need to make IDs unique
           el.id = 'aboutEditor-' + (index + 1);
           const q = new Quill('#aboutEditor-' + (index + 1), {
                theme: 'snow',
                placeholder: 'Tulis deskripsi...',
                modules: { toolbar: [['bold', 'italic', 'underline'], [{ 'list': 'ordered'}, { 'list': 'bullet' }], ['clean']] }
           });
           // Load content
           q.root.innerHTML = `{!! addslashes($landingPage->about_text ?? '') !!}`;
           
           // Sync on change
           q.on('text-change', function() {
               const html = q.root.innerHTML;
               document.getElementById('aboutText').value = html; // Main hidden input
               // Update other quills
               quills.forEach(otherQ => {
                   if(otherQ !== q && otherQ.root.innerHTML !== html) {
                       otherQ.root.innerHTML = html;
                   }
               });
           });
           quills.push(q);
       });


        // ========== AUTO RESIZE TEXTAREA ==========
        function autoResize(el) {
            el.style.height = 'auto';
            el.style.height = el.scrollHeight + 'px';
        }

        // Init on load
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('textarea').forEach(textarea => autoResize(textarea));
            
            // Re-sync template radios and initial view state
            const initialTemplate = document.querySelector('input[name="template_id"]:checked');
            if(initialTemplate) {
                switchTemplate(initialTemplate.value);
            }
        });

        // ========== MOBILE MENU ==========
        function toggleMobileMenu() {
            // Target the visible one
            const visibleMenu = document.querySelector('.template-view:not(.hidden) .mobile-menu');
            const visibleOverlay = document.querySelector('.template-view:not(.hidden) .mobile-overlay');
            if(visibleMenu) visibleMenu.classList.toggle('active');
            if(visibleOverlay) visibleOverlay.classList.toggle('active');
        }

        // ========== COLOR UPDATES ==========
        function updateColor(value, type) {
            document.documentElement.style.setProperty(type === 'primary' ? '--primary' : '--secondary', value);
            document.getElementById(type + 'ColorPicker').style.backgroundColor = value;
        }

        // ========== PREVIEW BACKGROUND IMAGE ==========
        // Needs adjustment to target visible hero section
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Update all hero sections across templates
                    document.querySelectorAll('.hero-section').forEach(el => {
                         // Some templates use img tag, some bg image. 
                         // Template 1 uses specific CSS logic? No, template 1 in this file used .hero-section CSS.
                         // But we removed the CSS rule in head that targets .hero-section bg image?
                         // Wait, the dynamic style in head: .hero-section { background-image: ... }
                         // This targets ALL .hero-section classes.
                         // So updating the style tag or style attribute is best.
                         el.style.backgroundImage = `linear-gradient(135deg, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.4)), url('${e.target.result}')`;
                    });
                    
                    // Also update any img tags used in other layouts?
                    // Template 2 uses <img>.
                    document.querySelectorAll('img.hero-bg-img').forEach(img => img.src = e.target.result);
                    // I need to add class hero-bg-img to templates.

                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Background berhasil diubah', showConfirmButton: false, timer: 2000 });
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewAboutImage(input) {
             if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelectorAll('#aboutImagePreview').forEach(img => img.src = e.target.result);
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Foto About berhasil diubah', showConfirmButton: false, timer: 2000 });
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // ========== PRODUCT SELECTION (MAX 3) ==========
        // Master checkboxes are in the toolbar, preview checkboxes are in templates
        function setupProductSelection() {
            const masterCheckboxes = document.querySelectorAll('.master-product-checkbox');
            const templateCheckboxes = document.querySelectorAll('.product-checkbox');
            
            // Master checkbox change -> sync to templates and enforce limit
            masterCheckboxes.forEach(master => {
                master.addEventListener('change', function() {
                    const val = this.value;
                    const status = this.checked;
                    
                    // Count currently selected in master
                    const checkedCount = document.querySelectorAll('.master-product-checkbox:checked').length;
                    
                    if (checkedCount > 3) {
                        this.checked = false;
                        Swal.fire({
                            icon: 'warning',
                            title: 'Maksimal 3 Produk',
                            text: 'Anda hanya dapat memilih maksimal 3 produk unggulan.',
                            confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary').trim(),
                        });
                        return;
                    }
                    
                    // Sync to template checkboxes (for visual only)
                    templateCheckboxes.forEach(cb => {
                        if (cb.value === val) {
                            cb.checked = status;
                        }
                    });
                });
            });
            
            // Template checkbox change -> sync to master (if user clicks in template)
            templateCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    const val = this.value;
                    const status = this.checked;
                    
                    // Find master checkbox and trigger its change
                    const master = document.querySelector(`.master-product-checkbox[value="${val}"]`);
                    if (master) {
                        master.checked = status;
                        master.dispatchEvent(new Event('change'));
                    }
                });
            });
        }
        
        // ========== TESTIMONIAL SELECTION ==========
        function setupTestimonialSelection() {
            const masterCheckboxes = document.querySelectorAll('.master-testimonial-checkbox');
            const templateCheckboxes = document.querySelectorAll('.testimonial-checkbox');
            
            masterCheckboxes.forEach(master => {
                master.addEventListener('change', function() {
                    const val = this.value;
                    const status = this.checked;
                    
                    // Sync to template checkboxes
                    templateCheckboxes.forEach(cb => {
                        if (cb.value === val) {
                            cb.checked = status;
                        }
                    });
                });
            });
            
            templateCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    const val = this.value;
                    const status = this.checked;
                    
                    const master = document.querySelector(`.master-testimonial-checkbox[value="${val}"]`);
                    if (master) {
                        master.checked = status;
                    }
                });
            });
        }
        
        // Initialize on DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            setupProductSelection();
            setupTestimonialSelection();
        });

        // ========== NAVBAR SCROLL EFFECT ==========
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.template-view:not(.hidden) .navbar');
            if(navbar) {
                if (window.scrollY > 100) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
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
        // ========== TOOLBAR TOGGLE ==========
        function toggleToolbar() {
            const toolbar = document.getElementById('editorToolbar');
            const toggleIcon = document.getElementById('toolbarToggleIcon');
            const statusText = document.getElementById('toolbarStatusText');
            
            toolbar.classList.toggle('collapsed-toolbar');
            
            if (toolbar.classList.contains('collapsed-toolbar')) {
                toggleIcon.classList.replace('fa-chevron-down', 'fa-chevron-up');
                statusText.innerText = 'Buka Panel Editor';
            } else {
                toggleIcon.classList.replace('fa-chevron-up', 'fa-chevron-down');
                statusText.innerText = 'Sembunyikan Panel';
            }
        }
    </script>
</body>
</html>

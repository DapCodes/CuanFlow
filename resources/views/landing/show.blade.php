<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $outlet->name }} - Official Store</title>
    
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
    @php
        $headingFont = $landingPage->font_heading ?? 'Inter';
        $bodyFont = $landingPage->font_body ?? 'Inter';
        $fonts = array_unique([$headingFont, $bodyFont]);
        $fontQuery = implode('&family=', array_map(function($font) {
            return str_replace(' ', '+', $font) . ':wght@300;400;500;600;700;800;900';
        }, $fonts));
    @endphp
    <link href="https://fonts.googleapis.com/css2?family={{ $fontQuery }}&display=swap" rel="stylesheet">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <!-- Swiper.js for Carousels -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <style>
        * { box-sizing: border-box; }
        html {
            scroll-behavior: smooth;
        }

        section, header, footer {
            scroll-margin-top: 80px;
        }
        
        body {
            font-family: '{{ $bodyFont }}', sans-serif;
            overflow-x: hidden;
        }
        
        h1, h2, h3, h4, h5, h6, .font-heading {
            font-family: '{{ $headingFont }}', sans-serif;
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
        }

        .text-primary { color: var(--primary); }
        .bg-primary { background-color: var(--primary); }
        .border-primary { border-color: var(--primary); }
        .text-secondary { color: var(--secondary); }
        .bg-secondary { background-color: var(--secondary); }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #F3F4F6; }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--secondary); }

        /* Common Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .animate-float { animation: float 3s ease-in-out infinite; }

        /* Glass Card Utility */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        /* Navbar Utilities */
        .navbar {
            transition: all 0.3s ease;
        }
        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .navbar.scrolled .nav-link { color: #374151 !important; }
        .navbar.scrolled .brand-name { color: #111827 !important; }

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
    </style>
    
    <!-- Template Specific CSS Overrides -->
    @if($landingPage->template_id == 2)
        <style>
            .hero-section { min-height: 80vh; }
            .section-title-line { height: 4px; width: 60px; }
        </style>
    @elseif($landingPage->template_id == 3)
        <style>
           /* Dark Mode / Bold Extras if needed */
        </style>
    @endif
</head>
<body class="bg-gray-50 text-gray-800">

    @php
        $template = $landingPage->template_id ?? 1;
        // Fallback if file doesn't exist (safety)
        if (!view()->exists('landing.templates.template_' . $template)) {
            $template = 1;
        }
    @endphp

    @if(!$landingPage->is_active)
        <div class="fixed top-0 left-0 w-full z-[1000] bg-yellow-500 text-white py-2 shadow-lg flex items-center justify-center gap-3">
            <i class="fas fa-exclamation-triangle"></i>
            <span class="text-[10px] md:text-sm font-bold uppercase tracking-widest">Preview Mode: Halaman ini sedang nonaktif.</span>
            @can('edit landing page')
            <a href="{{ route('landing-pages.index') }}" class="px-3 py-1 bg-white text-yellow-600 rounded-full text-[9px] font-black uppercase hover:bg-gray-100 transition whitespace-nowrap">
                Aktifkan
            </a>
            @endcan
        </div>
        <style>
            .navbar, #navbar { top: 40px !important; }
            .mobile-menu, #mobileMenu { top: 40px !important; }
            body { padding-top: 40px; }
        </style>
    @endif

    @include('landing.templates.template_' . $template)

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

    <!-- ========== ALL PRODUCTS SECTION (THEMED OVERLAY) ========== -->
    <div id="allProductsOverlay" class="fixed inset-0 z-[80] hidden overflow-y-auto bg-gray-50/95 backdrop-blur-xl">
        <div class="min-h-screen px-4 py-12 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <!-- Header Overlay -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12" data-aos="fade-down">
                    <div>
                        <h2 class="text-4xl md:text-5xl font-black text-gray-900 tracking-tighter mb-2">Semua Koleksi</h2>
                        <div class="h-1.5 w-20 bg-primary rounded-full"></div>
                    </div>
                    <button onclick="toggleAllProducts()" class="self-start md:self-center flex items-center gap-2 bg-white border border-gray-200 text-gray-800 px-6 py-3 rounded-2xl shadow-sm hover:shadow-md transition-all active:scale-95">
                        <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                    </button>
                </div>

                <!-- Products Listing -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-16" data-aos="fade-up">
                    @foreach($allProducts as $product)
                        <div class="group bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 border border-gray-100 p-4 cursor-pointer"
                             onclick="showProductDetail({{ json_encode([
                                'name' => $product->name,
                                'price' => number_format($product->selling_price, 0, ',', '.'),
                                'description' => $product->description ?? 'Tidak ada deskripsi tersedia.',
                                'image' => $product->image ? Storage::url($product->image) : null,
                                'category' => $product->category->name ?? 'Umum',
                                'unit' => $product->unit->name ?? 'pcs'
                             ]) }})">
                            <div class="aspect-square rounded-2xl overflow-hidden mb-6 relative">
                                @if($product->image)
                                    <img src="{{ Storage::url($product->image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300 bg-gray-50">
                                        <i class="fas fa-box text-4xl"></i>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <span class="bg-white text-gray-900 px-6 py-2 rounded-full font-bold text-sm shadow-xl transform translate-y-4 group-hover:translate-y-0 transition-transform">Lihat Detail</span>
                                </div>
                            </div>
                            <div class="px-2">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-primary mb-2 block">{{ $product->category->name ?? 'Koleksi' }}</span>
                                <h3 class="font-bold text-lg text-gray-900 mb-1 truncate">{{ $product->name }}</h3>
                                <p class="text-primary font-black">
                                    <span class="text-xs">Rp</span> {{ number_format($product->selling_price, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Init AOS
        AOS.init({
            once: true,
            offset: 50,
            duration: 800,
        });

        // Mobile Menu Toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            const overlay = document.getElementById('mobileOverlay');
            if(menu) menu.classList.toggle('active');
            if(overlay) overlay.classList.toggle('active');
        }

        function toggleAllProducts() {
            const overlay = document.getElementById('allProductsOverlay');
            const isHidden = overlay.classList.contains('hidden');
            
            if (isHidden) {
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                overlay.scrollTop = 0;
            } else {
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }
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

        const testimonialForm = document.getElementById('testimonialForm');
        if(testimonialForm) {
            testimonialForm.addEventListener('submit', function(e) {
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
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    }
                })
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok) {
                        // Handle validation errors or other server errors
                        let errorMessage = data.message || 'Gagal mengirim ulasan.';
                        if (data.errors) {
                            errorMessage = Object.values(data.errors).flat().join('\n');
                        }
                        throw new Error(errorMessage);
                    }
                    return data;
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terima Kasih!',
                            text: data.message,
                            confirmButtonColor: '#4F46E5',
                            timer: 3000
                        });
                        this.reset();
                        document.getElementById('testimonialImageLabel').innerText = 'Upload Foto';
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: error.message || 'Gagal mengirim ulasan. Silakan coba lagi.',
                        confirmButtonColor: '#EF4444'
                    });
                })
                .finally(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
            });
        }

        // Navbar Scroll Effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (navbar) {
                if (window.scrollY > 100) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            }
        });

        // Leaflet Map logic inside template or here? 
        // Better to check if map-container exists first
        document.addEventListener('DOMContentLoaded', function() {
            if(document.getElementById('map')) {
                var lat = {{ $outlet->latitude ?? -6.200000 }};
                var lng = {{ $outlet->longitude ?? 106.816666 }};
                
                var map = L.map('map').setView([lat, lng], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                L.marker([lat, lng]).addTo(map)
                    .bindPopup('{{ $outlet->name }}')
                    .openPopup();
            }
        });
    </script>
</body>
</html>

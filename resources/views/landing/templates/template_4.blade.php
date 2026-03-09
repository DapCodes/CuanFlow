@php
    // Template 4: Playful / Creative (Rounded, Colorful)
    // Uses blobs and soft shadows
@endphp

<div class="bg-[#FFF8F0] text-gray-800 font-sans overflow-x-hidden">
    
    <!-- Navbar (Floating Pill) -->
    <div class="fixed top-6 left-0 right-0 z-50 flex justify-center px-4 transition-all duration-300" id="navbar">
        <nav class="bg-white/90 backdrop-blur-md shadow-lg rounded-full px-8 py-4 flex items-center gap-8 border border-gray-100 transition-all duration-300">
            <span class="font-black text-xl text-primary">{{ $outlet->name }}</span>
            <div class="hidden md:flex gap-6 font-bold text-sm text-gray-600">
                <a href="#home" class="hover:text-primary transition">Home</a>
                <a href="#about" class="hover:text-primary transition">About</a>
                <a href="#products" class="hover:text-primary transition">Shop</a>
                <a href="#testimonials" class="hover:text-primary transition">Reviews</a>
                <a href="#contact" class="hover:text-primary transition">Contact</a>
            </div>
             <button class="md:hidden" onclick="toggleMobileMenu()"><i class="fas fa-bars text-xl text-primary"></i></button>
        </nav>
    </div>
    <div class="mobile-overlay" id="mobileOverlay" onclick="toggleMobileMenu()"></div>
     <div class="mobile-menu p-8 bg-[#FFF8F0]" id="mobileMenu">
         <h2 class="font-bold text-2xl mb-6">Menu</h2>
         <a href="#home" onclick="toggleMobileMenu()" class="block py-2 font-bold text-lg">Home</a>
         <a href="#about" onclick="toggleMobileMenu()" class="block py-2 font-bold text-lg">About</a>
         <a href="#products" onclick="toggleMobileMenu()" class="block py-2 font-bold text-lg">Shop</a>
         <a href="#testimonials" onclick="toggleMobileMenu()" class="block py-2 font-bold text-lg">Reviews</a>
    </div>


    <!-- Hero (Blob Backgrounds) -->
    <header id="home" class="pt-40 pb-20 px-6 relative overflow-hidden min-h-screen flex items-center">
        <!-- Blobs -->
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-primary/20 rounded-full blur-3xl mix-blend-multiply filter animate-float"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-yellow-300/30 rounded-full blur-3xl mix-blend-multiply filter animation-delay-2000"></div>
        
        <div class="relative z-10 max-w-6xl mx-auto grid md:grid-cols-2 gap-12 items-center">
            <div class="text-center md:text-left">
                @if(isset($is_editor) && $is_editor)
                    <span class="inline-block bg-white shadow-sm border border-gray-100 rounded-full px-4 py-1 text-sm font-bold text-primary mb-6">
                        👋 <input type="text" value="{{ $landingPage->tagline_text }}" class="bg-transparent border-0 w-32 focus:ring-0" data-sync="tagline_text">
                    </span>
                    <textarea rows="2" class="w-full bg-transparent text-5xl md:text-7xl font-black mb-6 leading-tight text-gray-900 resize-none border-b-4 border-primary/20 focus:border-primary" data-sync="hero_title" oninput="autoResize(this)">{{ $landingPage->hero_title }}</textarea>
                    <textarea rows="2" class="w-full bg-transparent text-xl text-gray-600 mb-8 font-medium resize-none border-l-2 border-gray-200 pl-4" data-sync="hero_subtitle" oninput="autoResize(this)">{{ $landingPage->hero_subtitle }}</textarea>
                @else
                    <span class="inline-block bg-white shadow-sm border border-gray-100 rounded-full px-4 py-1 text-sm font-bold text-primary mb-6 animate-pulse">
                         👋 {{ $landingPage->tagline_text ?? 'Hello There!' }}
                    </span>
                    <h1 class="text-5xl md:text-7xl font-black mb-6 leading-tight text-gray-900">
                        {{ $landingPage->hero_title }}
                    </h1>
                    <p class="text-xl text-gray-600 mb-8 font-medium">
                        {{ $landingPage-> hero_subtitle }}
                    </p>
                @endif
                
                @if(isset($is_editor) && $is_editor)
                    <div class="inline-block bg-gray-900 text-white px-8 py-4 rounded-full font-bold shadow-lg opacity-80">
                        <input type="text" value="{{ $landingPage->cta_button_text ?? 'Start Shopping' }}" class="bg-transparent border-0 text-center w-40 focus:ring-0" data-sync="cta_button_text">
                    </div>
                @else
                    <a href="#products" class="inline-block bg-gray-900 text-white px-8 py-4 rounded-full font-bold shadow-lg hover:shadow-xl hover:-translate-y-1 transition transform">
                        {{ $landingPage->cta_button_text ?? 'Start Shopping' }}
                    </a>
                @endif
            </div>
            <div class="relative hero-section-bg">
                @if(isset($is_editor) && $is_editor)
                    <div class="absolute bottom-4 right-4 z-10">
                        <label for="hero_image" class="cursor-pointer bg-white px-4 py-3 rounded-full shadow-xl text-xs font-bold text-primary flex items-center gap-2 hover:scale-105 transition transform active:scale-95"><i class="fas fa-camera"></i> Change Image</label>
                    </div>
                @endif
                <div class="bg-white p-4 rounded-[3rem] shadow-2xl rotate-3 hover:rotate-0 transition duration-500">
                    <img src="{{ $landingPage->hero_image ? Storage::url($landingPage->hero_image) : 'https://images.unsplash.com/photo-1542291026-7eec264c27ff' }}" 
                         class="rounded-[2.5rem] w-full object-cover aspect-square hero-bg-img">
                </div>
            </div>
        </div>
    </header>

    <!-- Stats Pills -->
    <div class="py-12 px-6">
        <div class="max-w-4xl mx-auto flex flex-wrap justify-center gap-4">
            <div class="bg-white rounded-full px-8 py-4 shadow-lg border border-gray-100">
                <span class="font-black text-2xl text-primary">{{ $displaySales ?? '100+' }}</span>
                <span class="text-xs font-bold uppercase tracking-widest text-gray-500 ml-2">Happy Clients</span>
            </div>
            <div class="bg-white rounded-full px-8 py-4 shadow-lg border border-gray-100">
                <span class="font-black text-2xl text-primary">{{ $outlet->products->count() }}</span>
                <span class="text-xs font-bold uppercase tracking-widest text-gray-500 ml-2">Products</span>
            </div>
        </div>
    </div>

    <!-- About Section -->
    <section id="about" class="py-24 px-6">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-4xl font-black mb-12">Our Story ✨</h2>
            <div class="prose prose-lg mx-auto">
                @if(isset($is_editor) && $is_editor)
                    <div id="aboutEditor" class="bg-white p-6 rounded-3xl shadow-xl border border-gray-100 text-left">{!! $landingPage->about_text !!}</div>
                @else
                    {!! $landingPage->about_text ?? '<p>We create joy through quality products.</p>' !!}
                @endif
            </div>
        </div>
    </section>

    <!-- Products (Cards with rounded corners) -->
    <section id="products" class="py-24 px-6">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-4xl font-black text-center mb-16 relative inline-block left-1/2 transform -translate-x-1/2">
                Our Favorites
                <span class="absolute -bottom-2 -right-4 w-full h-3 bg-yellow-300 -z-10 rounded-full"></span>
            </h2>

            @if(isset($is_editor) && $is_editor)
                <p class="text-center mb-8 text-gray-600">
                    <i class="fas fa-check-circle text-primary"></i> Pilih produk unggulan untuk ditampilkan di carousel
                </p>
                @endif

            @if(count($products) > 0)
                <div class="swiper products-carousel pb-12">
                <div class="swiper-wrapper">
                    @foreach($products as $product)
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

                <div class="text-center mt-12">
                    <button onclick="toggleAllProducts()" class="bg-primary hover:bg-primary/90 text-white px-8 py-4 rounded-full font-bold shadow-lg transform active:scale-95 transition-all">
                        Lihat Semua Produk
                    </button>
                </div>
            @else
                <p class="text-center text-gray-500">No products available yet.</p>
            @endif
        </div>
    </section>

    <!-- Testimonials List -->
    @if(isset($testimonials) && count($testimonials) > 0)
    <section id="testimonials" class="py-24 px-6 bg-yellow-50/50">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-4xl font-black text-center mb-16">What Friends Say</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($testimonials as $testimonial)
                    @if(isset($is_editor) && $is_editor)
                        <label class="bg-white p-8 rounded-[2rem] shadow-lg border-2 border-transparent peer-checked:border-primary cursor-pointer relative">
                            <input type="checkbox" data-testimonial-id="{{ $testimonial->id }}" value="{{ $testimonial->id }}"
                                   class="hidden peer testimonial-checkbox"
                                   {{ in_array($testimonial->id, $landingPage->selected_testimonial_ids ?? []) ? 'checked' : '' }}>
                            <div class="flex text-yellow-400 mb-4">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $testimonial->rating ? '' : 'text-gray-200' }}"></i>
                                @endfor
                            </div>
                            <p class="text-gray-600 italic mb-6">"{{ Str::limit($testimonial->content, 100) }}"</p>
                            <div class="font-black text-primary text-sm">{{ $testimonial->name }}</div>
                        </label>
                    @elseif(in_array($testimonial->id, $landingPage->selected_testimonial_ids ?? []))
                        <div class="bg-white p-8 rounded-[2rem] shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
                            <div class="flex text-yellow-400 mb-4">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $testimonial->rating ? '' : 'text-gray-200' }}"></i>
                                @endfor
                            </div>
                            <p class="text-gray-600 italic mb-6 leading-relaxed">"{{ $testimonial->content }}"</p>
                            <div class="flex items-center gap-4">
                                @if($testimonial->image)
                                <img src="{{ Storage::url($testimonial->image) }}" class="w-10 h-10 rounded-full object-cover">
                                @endif
                                <div>
                                    <div class="font-black text-primary text-sm">{{ $testimonial->name }}</div>
                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $testimonial->role }}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Testimonial Form Section -->
    @if(!isset($is_editor) || !$is_editor)
    <section id="write-testimonial" class="py-24 px-6 bg-white">
        <div class="max-w-4xl mx-auto">
            <div class="bg-yellow-50 p-8 md:p-12 rounded-[2.5rem] shadow-xl border-4 border-white relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-primary/10 rounded-full blur-3xl"></div>
                
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-black mb-4">Wanna share joy? 🥳</h2>
                    <p class="text-gray-600">Tell everyone how much you love {{ $outlet->name }}!</p>
                </div>

                <form id="testimonialForm" class="space-y-6 relative z-10">
                    @csrf
                    <input type="hidden" name="outlet_id" value="{{ $outlet->id }}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-primary mb-2 ml-4">Nick Name</label>
                            <input type="text" name="name" required class="w-full bg-white border-none shadow-inner rounded-full px-6 py-4 text-sm focus:ring-2 focus:ring-primary" placeholder="Cool Cat">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-primary mb-2 ml-4">Who are you?</label>
                            <input type="text" name="role" class="w-full bg-white border-none shadow-inner rounded-full px-6 py-4 text-sm focus:ring-2 focus:ring-primary" placeholder="Pizza Lover">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-primary mb-2 ml-4">How many stars?</label>
                        <div class="flex items-center gap-4 bg-white px-6 py-4 rounded-full shadow-inner">
                            <div class="flex flex-row-reverse justify-end gap-2">
                                <input type="radio" name="rating" id="star5" value="5" class="hidden peer/5" checked>
                                <label for="star5" class="cursor-pointer text-gray-200 peer-checked/5:text-yellow-400 hover:text-yellow-400 transition-all text-2xl"><i class="fas fa-star"></i></label>
                                
                                <input type="radio" name="rating" id="star4" value="4" class="hidden peer/4">
                                <label for="star4" class="cursor-pointer text-gray-200 peer-checked/4:text-yellow-400 peer-checked/5:text-yellow-400 hover:text-yellow-400 transition-all text-2xl"><i class="fas fa-star"></i></label>
                                
                                <input type="radio" name="rating" id="star3" value="3" class="hidden peer/3">
                                <label for="star3" class="cursor-pointer text-gray-200 peer-checked/3:text-yellow-400 peer-checked/4:text-yellow-400 peer-checked/5:text-yellow-400 hover:text-yellow-400 transition-all text-2xl"><i class="fas fa-star"></i></label>
                                
                                <input type="radio" name="rating" id="star2" value="2" class="hidden peer/2">
                                <label for="star2" class="cursor-pointer text-gray-200 peer-checked/2:text-yellow-400 peer-checked/3:text-yellow-400 peer-checked/4:text-yellow-400 peer-checked/5:text-yellow-400 hover:text-yellow-400 transition-all text-2xl"><i class="fas fa-star"></i></label>
                                
                                <input type="radio" name="rating" id="star1" value="1" class="hidden peer/1">
                                <label for="star1" class="cursor-pointer text-gray-200 peer-checked/1:text-yellow-400 peer-checked/2:text-yellow-400 peer-checked/3:text-yellow-400 peer-checked/4:text-yellow-400 peer-checked/5:text-yellow-400 hover:text-yellow-400 transition-all text-2xl"><i class="fas fa-star"></i></label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-primary mb-2 ml-4">The tea ☕</label>
                        <textarea name="content" rows="4" required class="w-full bg-white border-none shadow-inner rounded-3xl px-6 py-4 text-sm focus:ring-2 focus:ring-primary resize-none" placeholder="Is it good? Is it great?"></textarea>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-primary mb-2 ml-4">Pic or it didn't happen!</label>
                        <input type="file" name="image" id="testimonialImage" accept="image/*" class="hidden" onchange="previewTestimonialImage(this)">
                        <label for="testimonialImage" class="cursor-pointer flex items-center justify-center gap-3 bg-white border-2 border-dashed border-primary/20 rounded-[2rem] p-8 text-primary/40 hover:bg-white hover:border-primary transition-all group">
                            <i class="fas fa-cloud-upload-alt text-3xl group-hover:scale-110 transition-transform"></i>
                            <span id="testimonialImageLabel" class="font-black uppercase tracking-[0.2em] text-[10px]">Add a Snap</span>
                        </label>
                    </div>

                    <div class="text-center">
                        <button type="submit" id="btnSubmitTestimonial" class="px-12 py-5 bg-gray-900 text-white font-black uppercase tracking-[0.2em] text-sm rounded-full shadow-xl hover:shadow-primary/30 hover:-translate-y-1 transition transform active:scale-95">
                            High Five! 🚀
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    @endif

    <!-- Footer -->
    <footer id="contact" class="bg-primary text-white py-16 px-6 rounded-t-[4rem] mt-12 mx-4 shadow-2xl">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row gap-16 items-start">
            <div class="flex-1 text-left">
                @if(isset($is_editor) && $is_editor)
                    <textarea name="cta_text" rows="1" class="w-full bg-transparent text-4xl md:text-6xl font-black mb-8 resize-none border-0 focus:ring-0 p-0 text-white placeholder-white/50" data-sync="cta_text">{{ $landingPage->cta_text ?? 'See You Soon!' }}</textarea>
                @else
                    <h2 class="text-4xl md:text-6xl font-black mb-8">{{ $landingPage->cta_text ?? 'See You Soon!' }}</h2>
                @endif
                
                <div class="flex gap-4 mb-12">
                     @if($landingPage->social_media['instagram'] ?? false) 
                        <a href="https://instagram.com/{{ $landingPage->social_media['instagram'] }}" target="_blank" class="bg-white text-primary w-12 h-12 rounded-full flex items-center justify-center text-xl hover:scale-110 transition shadow-lg">
                            <i class="fab fa-instagram"></i>
                        </a> 
                     @endif
                     @if($landingPage->social_media['tiktok'] ?? false) 
                        <a href="https://tiktok.com/@{{ $landingPage->social_media['tiktok'] }}" target="_blank" class="bg-white text-primary w-12 h-12 rounded-full flex items-center justify-center text-xl hover:scale-110 transition shadow-lg">
                            <i class="fab fa-tiktok"></i>
                        </a> 
                     @endif
                     @if($landingPage->whatsapp_number ?? false) 
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $landingPage->whatsapp_number) }}" target="_blank" class="bg-white text-green-500 w-12 h-12 rounded-full flex items-center justify-center text-xl hover:scale-110 transition shadow-lg">
                            <i class="fab fa-whatsapp"></i>
                        </a> 
                     @endif
                </div>

                <div class="pt-8 border-t border-white/20">
                    <h3 class="font-black text-2xl mb-4 text-white">{{ $outlet->name }}</h3>
                    <p class="text-white/70 mb-8 max-w-sm">{{ $outlet->address }}</p>
                    
                    @if(isset($is_editor) && $is_editor)
                        <textarea name="footer_text" class="w-full bg-transparent text-white/50 text-xs font-bold uppercase tracking-widest resize-none border-0 focus:ring-0 p-0" data-sync="footer_text">{{ $landingPage->footer_text ?? '© '.date('Y').' All rights reserved.' }}</textarea>
                    @else
                        <p class="text-white/50 text-xs font-bold uppercase tracking-widest">
                            {{ $landingPage->footer_text ?? '© '.date('Y').' '.$outlet->name.'. All rights reserved.' }}
                        </p>
                    @endif
                </div>
            </div>

            <!-- Map Footer -->
            <div class="w-full md:w-1/2">
                <div id="map" class="h-[350px] w-full bg-white/10 rounded-[3rem] overflow-hidden border-8 border-white/20 shadow-inner"></div>
            </div>
        </div>
    </footer>
</div>

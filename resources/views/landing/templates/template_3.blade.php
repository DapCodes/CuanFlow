@php
    // Template 3: Dark Mode / Bold Interactions
@endphp

<div class="bg-gray-900 text-white min-h-screen font-sans selection:bg-primary selection:text-white">
    
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 w-full z-50 px-8 py-4 backdrop-blur-md bg-black/40 border-b border-white/10 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <span class="font-black text-2xl tracking-tighter">{{ strtoupper($outlet->name) }}</span>
            <div class="hidden md:flex gap-8 text-sm font-bold uppercase tracking-widest">
                <a href="#home" class="hover:text-primary transition-colors">Home</a>
                <a href="#about" class="hover:text-primary transition-colors">About</a>
                <a href="#products" class="hover:text-primary transition-colors">Collections</a>
                <a href="#testimonials" class="hover:text-primary transition-colors">Reviews</a>
            </div>
            <button class="md:hidden text-white" onclick="toggleMobileMenu()"><i class="fas fa-bars text-xl"></i></button>
        </div>
    </nav>
    
    <!-- Mobile Menu Overlay -->
    <div class="mobile-overlay" id="mobileOverlay" onclick="toggleMobileMenu()"></div>
    <div class="mobile-menu bg-gray-900 text-white p-8" id="mobileMenu">
         <!-- Simple list -->
         <nav class="flex flex-col gap-6 text-2xl font-black uppercase">
            <a href="#home" onclick="toggleMobileMenu()">Home</a>
            <a href="#about" onclick="toggleMobileMenu()">About</a>
            <a href="#products" onclick="toggleMobileMenu()">Products</a>
            <a href="#testimonials" onclick="toggleMobileMenu()">Reviews</a>
         </nav>
    </div>

    <!-- Hero -->
    <header id="home" class="relative min-h-[90vh] flex items-center pt-20 overflow-hidden">
        <!-- Background Image with Heavy Overlay -->
        <div class="absolute inset-0 z-0 hero-section-bg">
             <img src="{{ $landingPage->hero_image ? Storage::url($landingPage->hero_image) : 'https://images.unsplash.com/photo-1550989460-0adf9ea622e2' }}" 
                  class="w-full h-full object-cover opacity-40 hero-bg-img">
             <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/50 to-transparent"></div>
             
             @if(isset($is_editor) && $is_editor)
             <!-- Editor Upload Trigger -->
             <div class="absolute bottom-8 right-8 z-20">
                <label for="hero_image" class="cursor-pointer inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded shadow-xl text-xs font-bold uppercase hover:bg-white hover:text-black transition transform active:scale-95">
                    <i class="fas fa-camera text-lg"></i> Ganti Background
                </label>
             </div>
            @endif
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 w-full grid md:grid-cols-2 gap-12 items-center">
            <div>
                <div class="inline-block px-3 py-1 border border-primary text-primary text-xs font-bold uppercase tracking-widest mb-6 rounded-full hover:bg-primary hover:text-white transition cursor-default">
                    @if(isset($is_editor) && $is_editor)
                        <input type="text" name="tagline_text" value="{{ $landingPage->tagline_text }}" class="bg-transparent border-0 text-center w-32 focus:ring-0 text-white placeholder-gray-500" placeholder="TAGLINE" data-sync="tagline_text">
                    @else
                        {{ $landingPage->tagline_text ?? 'New Arrival' }}
                    @endif
                </div>
                
                @if(isset($is_editor) && $is_editor)
                    <textarea name="hero_title" rows="2" 
                          class="editable-field w-full bg-transparent text-6xl md:text-8xl font-black leading-none mb-8 tracking-tighter text-white resize-none border-l-4 border-primary pl-4 focus:outline-none"
                          placeholder="HERO TITLE" data-sync="hero_title"
                          oninput="autoResize(this)">{{ $landingPage->hero_title }}</textarea>
                @else
                    <h1 class="text-6xl md:text-8xl font-black leading-none mb-8 tracking-tighter bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-500">
                        {{ $landingPage->hero_title }}
                    </h1>
                @endif
                
                @if(isset($is_editor) && $is_editor)
                    <textarea name="hero_subtitle" rows="3" 
                          class="editable-field w-full bg-transparent text-xl text-gray-400 mb-10 max-w-lg leading-relaxed resize-none border-l-2 border-gray-700 pl-6 focus:outline-none focus:border-primary"
                          placeholder="Hero subtitle goes here..." data-sync="hero_subtitle"
                          oninput="autoResize(this)">{{ $landingPage->hero_subtitle }}</textarea>
                @else
                    <p class="text-xl text-gray-400 mb-10 max-w-lg leading-relaxed border-l-2 border-primary pl-6">
                        {{ $landingPage->hero_subtitle }}
                    </p>
                @endif

                <div class="flex gap-4">
                     @if(isset($is_editor) && $is_editor)
                         <div class="relative inline-block">
                            <a href="#" class="bg-primary text-white px-10 py-5 font-bold uppercase tracking-wider clip-diagonal pointer-events-none opacity-80">
                                 <input type="text" value="{{ $landingPage->cta_button_text ?? 'Explore Now' }}" 
                                   class="bg-transparent text-center w-32 border-b border-white/30 focus:border-white outline-none text-white pointer-events-auto"
                                   data-sync="cta_button_text">
                            </a>
                         </div>
                     @else
                     <a href="#products" class="bg-primary hover:bg-white hover:text-gray-900 text-white px-10 py-5 font-bold uppercase tracking-wider transition-all duration-300 clip-diagonal">
                        {{ $landingPage->cta_button_text ?? 'Explore Now' }}
                     </a>
                     @endif
                </div>
            </div>
            <!-- Floating Element / Visual -->
            <div class="hidden md:block relative">
                 <div class="absolute top-0 right-0 w-64 h-64 bg-primary/20 rounded-full blur-[100px]"></div>
            </div>
        </div>
    </header>

    <!-- Marquee Stats -->
    <div class="bg-primary text-white py-4 overflow-hidden whitespace-nowrap border-y border-white/10">
        <div class="inline-block animate-marquee pl-10">
            <span class="text-lg font-bold uppercase tracking-widest mx-8">Verified Quality</span>
            <span class="text-lg font-bold uppercase tracking-widest mx-8">•</span>
            <span class="text-lg font-bold uppercase tracking-widest mx-8">{{ $outlet->products->count() }} Premium Items</span>
            <span class="text-lg font-bold uppercase tracking-widest mx-8">•</span>
            <span class="text-lg font-bold uppercase tracking-widest mx-8">{{ $displaySales ?? '100+' }} Satisfied Customers</span>
             <span class="text-lg font-bold uppercase tracking-widest mx-8">•</span>
        </div>
    </div>

    <!-- About Section -->
    <section id="about" class="py-24 px-6 bg-gray-900 relative">
        <div class="max-w-5xl mx-auto">
             <div class="grid md:grid-cols-2 gap-16">
                 <div>
                     <h2 class="text-4xl font-black mb-8 text-white">THE <span class="text-primary">STORY</span></h2>
                     <div class="prose prose-invert prose-lg text-gray-400">
                        @if(isset($is_editor) && $is_editor)
                            <div id="aboutEditor" class="bg-gray-800 p-4 border border-gray-700 rounded min-h-[200px] text-gray-300">
                                {!! $landingPage->about_text !!}
                            </div>
                        @else
                            {!! $landingPage->about_text ?? '<p>About us content...</p>' !!}
                        @endif
                     </div>
                 </div>
                 <div class="space-y-8">
                     <div class="bg-gray-800 p-8 border-l-4 border-primary">
                         <h3 class="text-primary font-bold uppercase tracking-widest mb-2">Vision</h3>
                         @if(isset($is_editor) && $is_editor)
                             <textarea name="vision_text" rows="3" class="w-full bg-transparent border-b border-gray-600 focus:border-primary text-gray-300 resize-none" data-sync="vision_text">{{ $landingPage->vision_text }}</textarea>
                         @else
                             <p class="text-gray-400">{{ $landingPage->vision_text }}</p>
                         @endif
                     </div>
                     <div class="bg-gray-800 p-8 border-l-4 border-white">
                         <h3 class="text-white font-bold uppercase tracking-widest mb-2">Mission</h3>
                         @if(isset($is_editor) && $is_editor)
                             <textarea name="mission_text" rows="3" class="w-full bg-transparent border-b border-gray-600 focus:border-white text-gray-300 resize-none" data-sync="mission_text">{{ $landingPage->mission_text }}</textarea>
                         @else
                             <p class="text-gray-400">{{ $landingPage->mission_text }}</p>
                         @endif
                     </div>
                 </div>
             </div>
        </div>
    </section>

    <!-- Products: Dark Cards -->
    <section id="products" class="py-24 px-6 bg-gray-900 border-t border-gray-800">
        <div class="max-w-7xl mx-auto">
             <div class="flex items-end justify-between mb-16">
                 <div>
                     <h2 class="text-4xl md:text-5xl font-black mb-2 text-white">Editor's Pick</h2>
                     <div class="h-1 w-20 bg-primary"></div>
                 </div>
             </div>
             
            @if(isset($is_editor) && $is_editor)
                <p class="text-center mb-10 text-gray-400 bg-white/5 p-4 rounded-2xl border border-white/10 text-sm">
                    <i class="fas fa-info-circle mr-2 text-primary"></i> Pilih produk unggulan untuk ditampilkan di carousel
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

            <div class="text-center mt-12" data-aos="fade-up">
                <button onclick="toggleAllProducts()" class="bg-primary hover:bg-primary/90 text-white px-8 py-4 rounded-full font-bold shadow-lg transform active:scale-95 transition-all">
                    Lihat Semua Produk
                </button>
            </div>
        @else
            <div class="text-center py-16 text-gray-500">
                <i class="fas fa-box-open text-5xl mb-4 opacity-50"></i>
                <p>Belum ada produk unggulan yang dipilih.</p>
            </div>
        @endif
        </div>
    </section>

    <!-- Testimonials -->
    <section id="testimonials" class="py-24 px-6 bg-black">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-4xl font-black mb-12 text-center text-white">REVIEWS</h2>
            <div class="grid md:grid-cols-3 gap-8">
                @foreach($testimonials as $testimonial)
                    @if(isset($is_editor) && $is_editor)
                        <label class="bg-gray-900 p-8 border border-gray-800 cursor-pointer relative hover:bg-gray-800 transition">
                            <input type="checkbox" data-testimonial-id="{{ $testimonial->id }}" value="{{ $testimonial->id }}"
                                   class="hidden peer testimonial-checkbox"
                                   {{ in_array($testimonial->id, $landingPage->selected_testimonial_ids ?? []) ? 'checked' : '' }}>
                            <div class="absolute top-4 right-4 text-gray-700 peer-checked:text-primary"><i class="fas fa-check-circle"></i></div>
                            <p class="text-gray-400 italic mb-4">"{{ Str::limit($testimonial->content, 100) }}"</p>
                            <div class="font-bold text-white text-sm uppercase tracking-widest">- {{ $testimonial->name }}</div>
                        </label>
                    @elseif(in_array($testimonial->id, $landingPage->selected_testimonial_ids ?? []))
                        <div class="bg-gray-900 p-8 border border-gray-800 hover:border-primary transition duration-300">
                             <div class="flex text-primary mb-4 text-xs">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $testimonial->rating ? '' : 'text-gray-800' }}"></i>
                                @endfor
                             </div>
                            <p class="text-gray-400 italic mb-6 leading-relaxed">"{{ $testimonial->content }}"</p>
                            <div class="flex items-center gap-4 border-t border-gray-800 pt-6">
                                <div class="font-bold text-white uppercase tracking-widest text-sm">{{ $testimonial->name }}</div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
    <!-- Testimonial Form Section -->
    @if(!isset($is_editor) || !$is_editor)
    <section id="write-testimonial" class="py-24 px-6 bg-gray-900 border-t border-white/5">
        <div class="max-w-4xl mx-auto">
            <div class="bg-black/50 backdrop-blur-xl p-8 md:p-12 border border-white/10 rounded-3xl">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-black mb-4 tracking-tighter">LEAVE A REVIEW</h2>
                    <p class="text-gray-400">Share your thoughts on {{ $outlet->name }} with the world.</p>
                </div>

                <form id="testimonialForm" class="space-y-6">
                    @csrf
                    <input type="hidden" name="outlet_id" value="{{ $outlet->id }}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-primary mb-2">Your Name</label>
                            <input type="text" name="name" required class="w-full bg-white/5 border-white/10 focus:border-primary focus:ring-0 rounded-xl p-3 text-white placeholder-gray-600" placeholder="John Doe">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-primary mb-2">Role / Business</label>
                            <input type="text" name="role" class="w-full bg-white/5 border-white/10 focus:border-primary focus:ring-0 rounded-xl p-3 text-white placeholder-gray-600" placeholder="CEO / Enthusiast">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-primary mb-2">Rating</label>
                        <div class="flex items-center gap-4 bg-white/5 p-4 rounded-xl border border-white/10">
                            <div class="flex flex-row-reverse justify-end gap-2">
                                <input type="radio" name="rating" id="star5" value="5" class="hidden peer/5" checked>
                                <label for="star5" class="cursor-pointer text-gray-700 peer-checked/5:text-yellow-400 hover:text-yellow-400 transition-all text-2xl"><i class="fas fa-star"></i></label>
                                
                                <input type="radio" name="rating" id="star4" value="4" class="hidden peer/4">
                                <label for="star4" class="cursor-pointer text-gray-700 peer-checked/4:text-yellow-400 peer-checked/5:text-yellow-400 hover:text-yellow-400 transition-all text-2xl"><i class="fas fa-star"></i></label>
                                
                                <input type="radio" name="rating" id="star3" value="3" class="hidden peer/3">
                                <label for="star3" class="cursor-pointer text-gray-700 peer-checked/3:text-yellow-400 peer-checked/4:text-yellow-400 peer-checked/5:text-yellow-400 hover:text-yellow-400 transition-all text-2xl"><i class="fas fa-star"></i></label>
                                
                                <input type="radio" name="rating" id="star2" value="2" class="hidden peer/2">
                                <label for="star2" class="cursor-pointer text-gray-700 peer-checked/2:text-yellow-400 peer-checked/3:text-yellow-400 peer-checked/4:text-yellow-400 peer-checked/5:text-yellow-400 hover:text-yellow-400 transition-all text-2xl"><i class="fas fa-star"></i></label>
                                
                                <input type="radio" name="rating" id="star1" value="1" class="hidden peer/1">
                                <label for="star1" class="cursor-pointer text-gray-700 peer-checked/1:text-yellow-400 peer-checked/2:text-yellow-400 peer-checked/3:text-yellow-400 peer-checked/4:text-yellow-400 peer-checked/5:text-yellow-400 hover:text-yellow-400 transition-all text-2xl"><i class="fas fa-star"></i></label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-primary mb-2">The Experience</label>
                        <textarea name="content" rows="4" required class="w-full bg-white/5 border-white/10 focus:border-primary focus:ring-0 rounded-xl p-3 text-white placeholder-gray-600 resize-none" placeholder="Tell us about it..."></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-primary mb-2">Visual Proof</label>
                        <input type="file" name="image" id="testimonialImage" accept="image/*" class="hidden" onchange="previewTestimonialImage(this)">
                        <label for="testimonialImage" class="cursor-pointer flex items-center justify-center gap-3 bg-white/5 border-2 border-dashed border-white/10 rounded-2xl p-6 text-gray-500 hover:bg-white/10 hover:border-primary transition-all group">
                            <i class="fas fa-camera text-2xl group-hover:scale-110 transition-transform"></i>
                            <span id="testimonialImageLabel" class="font-bold uppercase tracking-widest text-sm">Upload Photo</span>
                        </label>
                    </div>

                    <div class="text-center">
                        <button type="submit" id="btnSubmitTestimonial" class="w-full py-5 bg-primary text-white font-black uppercase tracking-[0.2em] text-sm hover:brightness-110 transition-all transform active:scale-[0.98] rounded-xl shadow-lg shadow-primary/20">
                            Post Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    @endif

    <!-- Footer -->
    <footer id="contact" class="bg-black py-20 px-6 border-t border-white/5">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-16 items-start">
            <div class="text-left">
                <div class="mb-12">
                    @if(isset($is_editor) && $is_editor)
                        <textarea name="cta_text" rows="2" class="w-full bg-transparent text-4xl md:text-6xl font-black text-white italic mb-8 resize-none border-0 focus:ring-0 p-0" data-sync="cta_text">{{ $landingPage->cta_text ?? 'READY TO LEVEL UP?' }}</textarea>
                    @else
                        <h2 class="text-4xl md:text-6xl font-black text-white italic mb-12">{{ $landingPage->cta_text ?? 'READY TO LEVEL UP?' }}</h2>
                    @endif
                    
                    <div class="flex gap-6 mb-12">
                         @if($landingPage->social_media['instagram'] ?? false) 
                            <a href="https://instagram.com/{{ $landingPage->social_media['instagram'] }}" target="_blank" class="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center text-xl hover:bg-primary hover:border-primary text-white transition transform hover:-translate-y-1">
                                <i class="fab fa-instagram"></i>
                            </a> 
                         @endif
                         @if($landingPage->social_media['tiktok'] ?? false) 
                            <a href="https://tiktok.com/@{{ $landingPage->social_media['tiktok'] }}" target="_blank" class="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center text-xl hover:bg-white hover:text-black hover:border-white text-white transition transform hover:-translate-y-1">
                                <i class="fab fa-tiktok"></i>
                            </a> 
                         @endif
                         @if($landingPage->whatsapp_number ?? false) 
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $landingPage->whatsapp_number) }}" target="_blank" class="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center text-xl hover:bg-green-500 hover:border-green-500 text-white transition transform hover:-translate-y-1">
                                <i class="fab fa-whatsapp"></i>
                            </a> 
                         @endif
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-2xl font-black text-white tracking-tighter">{{ strtoupper($outlet->name) }}</h3>
                    <p class="text-gray-500 max-sm">{{ $outlet->address }}</p>
                    
                    @if(isset($is_editor) && $is_editor)
                        <textarea name="footer_text" rows="1" class="w-full bg-transparent text-sm text-gray-600 resize-none border-0 focus:ring-0 p-0" data-sync="footer_text">{{ $landingPage->footer_text }}</textarea>
                    @else
                        <p class="text-sm text-gray-600">&copy; {{ date('Y') }} {{ $outlet->name }}. All rights reserved.</p>
                    @endif
                </div>
            </div>

            <!-- Map Footer -->
            <div class="w-full">
                <div id="map" class="h-[400px] w-full rounded-3xl overflow-hidden border border-white/10 shadow-2xl brightness-75 hover:brightness-100 transition duration-500"></div>
            </div>
        </div>
    </footer>
</div>

@php
    // Template 5: Elegant / Luxury (Serif, Gold accents, Centered)
@endphp

<div class="bg-stone-50 text-stone-800 font-serif">
    
    <!-- Navbar (Centered Logo) -->
    <nav class="fixed top-0 left-0 w-full z-50 py-6 px-6 flex flex-col items-center bg-stone-50/80 backdrop-blur-md border-b border-stone-200 transition-all duration-300" id="navbar">
        <span class="text-2xl tracking-[0.4em] uppercase font-light mb-4">{{ $outlet->name }}</span>
        <div class="hidden md:flex gap-12 text-[10px] font-sans font-bold uppercase tracking-[0.2em] text-stone-400">
            <a href="#home" class="hover:text-stone-900 transition active:text-stone-900">Home</a>
            <a href="#about" class="hover:text-stone-900 transition active:text-stone-900">Philosophy</a>
            <a href="#products" class="hover:text-stone-900 transition active:text-stone-900">Collection</a>
            <a href="#testimonials" class="hover:text-stone-900 transition active:text-stone-900">Testimonials</a>
            <a href="#contact" class="hover:text-stone-900 transition active:text-stone-900">Contact</a>
        </div>
         <button class="md:hidden absolute right-6 top-6" onclick="toggleMobileMenu()"><i class="fas fa-bars text-xl text-stone-500"></i></button>
    </nav>
    <div class="h-32"></div> <!-- Spacer for fixed navbar -->
    <div class="mobile-overlay" id="mobileOverlay" onclick="toggleMobileMenu()"></div>
     <div class="mobile-menu bg-stone-100 p-8" id="mobileMenu">
         <h2 class="font-serif text-2xl mb-6 italic">{{ $outlet->name }}</h2>
         <nav class="flex flex-col gap-4 font-sans text-sm font-bold uppercase tracking-widest">
            <a href="#home" onclick="toggleMobileMenu()">Home</a>
            <a href="#about" onclick="toggleMobileMenu()">Philosophy</a>
            <a href="#products" onclick="toggleMobileMenu()">Collection</a>
            <a href="#testimonials" onclick="toggleMobileMenu()">Testimonials</a>
            <a href="#contact" onclick="toggleMobileMenu()">Contact</a>
         </nav>
    </div>

    <!-- Hero -->
    <header id="home" class="py-24 px-6 text-center max-w-5xl mx-auto">
        @if(isset($is_editor) && $is_editor)
            <input type="text" value="{{ $landingPage->tagline_text }}" class="font-sans text-xs font-bold uppercase tracking-[0.3em] text-stone-400 mb-6 bg-transparent border-b border-stone-200 text-center w-full focus:border-stone-500" data-sync="tagline_text">
            <textarea rows="2" class="w-full bg-transparent text-6xl md:text-8xl mb-8 leading-tight font-light italic resize-none text-center border-0 focus:ring-0" data-sync="hero_title" oninput="autoResize(this)">{{ $landingPage->hero_title }}</textarea>
            <textarea rows="2" class="w-full bg-transparent font-sans text-stone-500 mb-12 leading-loose resize-none text-center border-l-2 border-stone-200 pl-4" data-sync="hero_subtitle" oninput="autoResize(this)">{{ $landingPage->hero_subtitle }}</textarea>
        @else
            <p class="font-sans text-xs font-bold uppercase tracking-[0.3em] text-stone-400 mb-6 animate-fade-in-up">
                {{ $landingPage->tagline_text ?? 'Est. 2024' }}
            </p>
            <h1 class="text-6xl md:text-8xl mb-8 leading-tight font-light italic">
                {{ $landingPage->hero_title }}
            </h1>
            <p class="font-sans text-stone-500 max-w-lg mx-auto leading-loose mb-12">
                {{ $landingPage->hero_subtitle }}
            </p>
        @endif
        
        <div class="relative w-full aspect-[16/9] overflow-hidden mb-12 hero-section-bg">
            @if(isset($is_editor) && $is_editor)
                <div class="absolute bottom-4 right-4 z-10">
                    <label for="hero_image" class="cursor-pointer bg-white/90 backdrop-blur px-6 py-3 rounded-full shadow-xl font-sans text-xs font-bold uppercase tracking-[0.2em] text-stone-800 hover:bg-stone-900 hover:text-white transition transform active:scale-95"><i class="fas fa-camera mr-2"></i> Change Image</label>
                </div>
            @endif
            <img src="{{ $landingPage->hero_image ? Storage::url($landingPage->hero_image) : 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d' }}" 
                 class="w-full h-full object-cover grayscale hover:grayscale-0 transition duration-1000 ease-in-out hero-bg-img">
        </div>

        @if(isset($is_editor) && $is_editor)
            <div class="inline-block border-b border-stone-800 pb-1">
                <input type="text" value="{{ $landingPage->cta_button_text ?? 'Discover Collection' }}" class="text-sm font-sans font-bold uppercase tracking-widest bg-transparent border-0 text-center w-48 focus:ring-0" data-sync="cta_button_text">
            </div>
        @else
            <a href="#products" class="inline-block border-b border-stone-800 pb-1 text-sm font-sans font-bold uppercase tracking-widest hover:text-stone-600 hover:border-stone-600 transition">
                {{ $landingPage->cta_button_text ?? 'Discover Collection' }}
            </a>
        @endif
    </header>

    <!-- Stats (Elegant) -->
    <div class="py-12 border-y border-stone-200">
        <div class="max-w-4xl mx-auto grid grid-cols-3 divide-x divide-stone-200 text-center">
            <div class="py-4">
                <span class="block text-3xl font-light italic mb-2">{{ $displaySales ?? '100+' }}</span>
                <span class="font-sans text-xs uppercase tracking-widest text-stone-500">Clients</span>
            </div>
            <div class="py-4">
                <span class="block text-3xl font-light italic mb-2">{{ $outlet->products->count() }}</span>
                <span class="font-sans text-xs uppercase tracking-widest text-stone-500">Items</span>
            </div>
            <div class="py-4">
                <span class="block text-3xl font-light italic mb-2">100%</span>
                <span class="font-sans text-xs uppercase tracking-widest text-stone-500">Excellence</span>
            </div>
        </div>
    </div>

    <!-- About -->
    <section id="about" class="py-24 px-6 bg-white">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-4xl font-light italic mb-12 text-stone-900">Our Philosophy</h2>
            <div class="prose prose-stone prose-lg mx-auto">
                @if(isset($is_editor) && $is_editor)
                    <div id="aboutEditor" class="bg-stone-50 p-6 border border-stone-200 rounded text-left">{!! $landingPage->about_text !!}</div>
                @else
                    {!! $landingPage->about_text ?? '<p>Timeless elegance meets modern craftsmanship.</p>' !!}
                @endif
            </div>

            <div class="grid md:grid-cols-2 gap-8 mt-16 text-left">
                <div class="border-l-2 border-stone-900 pl-6">
                    <h3 class="font-sans text-xs font-bold uppercase tracking-widest mb-4">Vision</h3>
                    @if(isset($is_editor) && $is_editor)
                        <textarea name="vision_text" rows="3" class="w-full bg-transparent border-b border-stone-200 text-stone-600 font-sans resize-none" data-sync="vision_text">{{ $landingPage->vision_text }}</textarea>
                    @else
                        <p class="text-stone-600 font-sans">{{ $landingPage->vision_text }}</p>
                    @endif
                </div>
                <div class="border-l-2 border-stone-300 pl-6">
                    <h3 class="font-sans text-xs font-bold uppercase tracking-widest mb-4">Mission</h3>
                    @if(isset($is_editor) && $is_editor)
                        <textarea name="mission_text" rows="3" class="w-full bg-transparent border-b border-stone-200 text-stone-600 font-sans resize-none" data-sync="mission_text">{{ $landingPage->mission_text }}</textarea>
                    @else
                        <p class="text-stone-600 font-sans">{{ $landingPage->mission_text }}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Products (Minimal Grid) -->
    <section id="products" class="py-24 px-6 bg-stone-50">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-4xl font-light italic text-center mb-16">Curated Collection</h2>

            @if(isset($is_editor) && $is_editor)
                <p class="text-center mb-8 text-gray-500 bg-gray-50 p-4 rounded-2xl border border-gray-200 text-sm">
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

                <div class="text-center mt-12">
                    <button onclick="toggleAllProducts()" class="bg-primary hover:bg-primary/90 text-white px-8 py-4 rounded-full font-bold shadow-lg transform active:scale-95 transition-all">
                        Lihat Semua Produk
                    </button>
                </div>
            @else
                <div class="text-center py-16 text-stone-400">
                    <i class="fas fa-box-open text-5xl mb-4 opacity-50"></i>
                    <p>Belum ada produk unggulan yang dipilih.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Testimonials -->
    <section id="testimonials" class="py-20 px-6 bg-white">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-4xl font-light italic text-center mb-16">Distinguished Voices</h2>
            <div class="grid md:grid-cols-3 gap-8">
                @foreach($testimonials as $testimonial)
                    @if(isset($is_editor) && $is_editor)
                        <label class="border border-stone-200 p-8 cursor-pointer hover:border-stone-900 transition">
                            <input type="checkbox" data-testimonial-id="{{ $testimonial->id }}" value="{{ $testimonial->id }}" class="hidden peer testimonial-checkbox" {{ in_array($testimonial->id, $landingPage->selected_testimonial_ids ?? []) ? 'checked' : '' }}>
                            <div class="peer-checked:bg-stone-50 p-2">
                                <p class="italic text-stone-600 mb-6 leading-relaxed">"{{ Str::limit($testimonial->content, 100) }}"</p>
                                <div class="font-sans text-xs font-bold uppercase tracking-widest text-stone-900">{{ $testimonial->name }}</div>
                            </div>
                        </label>
                    @elseif(in_array($testimonial->id, $landingPage->selected_testimonial_ids ?? []))
                        <div class="border border-stone-200 p-8">
                             <div class="flex text-yellow-600 mb-4 text-sm">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $testimonial->rating ? '' : 'text-stone-200' }}"></i>
                                @endfor
                             </div>
                            <p class="italic text-stone-600 mb-6 leading-relaxed">"{{ $testimonial->content }}"</p>
                            <div class="font-sans text-xs font-bold uppercase tracking-widest text-stone-900">{{ $testimonial->name }}</div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    <!-- Testimonial Form Section -->
    @if(!isset($is_editor) || !$is_editor)
    <section id="write-testimonial" class="py-32 px-6 bg-stone-100/50">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white p-12 md:p-20 shadow-sm border border-stone-200">
                <div class="text-center mb-16">
                    <span class="font-sans text-[10px] font-black uppercase tracking-[0.4em] text-stone-400 mb-6 block">Guest Book</span>
                    <h2 class="text-4xl font-light italic mb-6">Leave Your Mark</h2>
                    <div class="w-12 h-px bg-stone-300 mx-auto"></div>
                </div>

                <form id="testimonialForm" class="space-y-12">
                    @csrf
                    <input type="hidden" name="outlet_id" value="{{ $outlet->id }}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                        <div class="border-b border-stone-200">
                            <label class="block font-sans text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-2">Guest Name</label>
                            <input type="text" name="name" required class="w-full bg-transparent border-0 focus:ring-0 p-0 text-stone-900 font-serif italic text-xl placeholder-stone-200" placeholder="Madame de Pompadour">
                        </div>
                        <div class="border-b border-stone-200">
                            <label class="block font-sans text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-2">Designation</label>
                            <input type="text" name="role" class="w-full bg-transparent border-0 focus:ring-0 p-0 text-stone-900 font-serif italic text-xl placeholder-stone-200" placeholder="Art Collector">
                        </div>
                    </div>

                    <div class="border-b border-stone-200 pb-4">
                        <label class="block font-sans text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-4">Initial Impressions</label>
                        <div class="flex items-center gap-6">
                            <div class="flex flex-row-reverse justify-end gap-2">
                                <input type="radio" name="rating" id="star5" value="5" class="hidden peer/5" checked>
                                <label for="star5" class="cursor-pointer text-stone-200 peer-checked/5:text-stone-900 hover:text-stone-900 transition-all text-2xl"><i class="fas fa-star"></i></label>
                                
                                <input type="radio" name="rating" id="star4" value="4" class="hidden peer/4">
                                <label for="star4" class="cursor-pointer text-stone-200 peer-checked/4:text-stone-900 peer-checked/5:text-stone-900 hover:text-stone-900 transition-all text-2xl"><i class="fas fa-star"></i></label>
                                
                                <input type="radio" name="rating" id="star3" value="3" class="hidden peer/3">
                                <label for="star3" class="cursor-pointer text-stone-200 peer-checked/3:text-stone-900 peer-checked/4:text-stone-900 peer-checked/5:text-stone-900 hover:text-stone-900 transition-all text-2xl"><i class="fas fa-star"></i></label>
                                
                                <input type="radio" name="rating" id="star2" value="2" class="hidden peer/2">
                                <label for="star2" class="cursor-pointer text-stone-200 peer-checked/2:text-stone-900 peer-checked/3:text-stone-900 peer-checked/4:text-stone-900 peer-checked/5:text-stone-900 hover:text-stone-900 transition-all text-2xl"><i class="fas fa-star"></i></label>
                                
                                <input type="radio" name="rating" id="star1" value="1" class="hidden peer/1">
                                <label for="star1" class="cursor-pointer text-stone-200 peer-checked/1:text-stone-900 peer-checked/2:text-stone-900 peer-checked/3:text-stone-900 peer-checked/4:text-stone-900 peer-checked/5:text-stone-900 hover:text-stone-900 transition-all text-2xl"><i class="fas fa-star"></i></label>
                            </div>
                        </div>
                    </div>

                    <div class="border-b border-stone-200">
                        <label class="block font-sans text-[10px] font-bold uppercase tracking-widest text-stone-400 mb-2">Your Narrative</label>
                        <textarea name="content" rows="4" required class="w-full bg-transparent border-0 focus:ring-0 p-0 text-stone-600 font-serif italic text-lg leading-relaxed resize-none placeholder-stone-200" placeholder="An unforgettable evening..."></textarea>
                    </div>

                    <div>
                        <input type="file" name="image" id="testimonialImage" accept="image/*" class="hidden" onchange="previewTestimonialImage(this)">
                        <label for="testimonialImage" class="cursor-pointer flex flex-col items-center justify-center border border-stone-200 border-dashed p-12 hover:bg-stone-50 transition-colors group">
                            <i class="fas fa-camera text-xl text-stone-300 group-hover:text-stone-900 transition-colors mb-4"></i>
                            <span id="testimonialImageLabel" class="font-sans text-[10px] font-bold uppercase tracking-[0.2em] text-stone-400 group-hover:text-stone-900 transition-colors">Attach a Portrait</span>
                        </label>
                    </div>

                    <div class="text-center pt-8">
                        <button type="submit" id="btnSubmitTestimonial" class="px-20 py-6 border border-stone-900 text-stone-900 font-sans text-[10px] font-bold uppercase tracking-[0.4em] hover:bg-stone-900 hover:text-white transition-all transform active:scale-95">
                            Submit Entry
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    @endif

    <!-- Footer -->
    <footer id="contact" class="bg-stone-900 text-stone-400 pt-32 pb-16 px-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row gap-20 items-start">
            <div class="flex-1 text-left">
                @if(isset($is_editor) && $is_editor)
                    <textarea name="cta_text" rows="2" class="w-full bg-transparent text-left font-serif text-5xl text-white italic mb-12 resize-none border-0 focus:ring-0 p-0 placeholder-white/20" data-sync="cta_text">{{ $landingPage->cta_text ?? 'Elevate Your Experience' }}</textarea>
                @else
                    <span class="font-serif text-5xl text-white italic mb-16 block leading-tight">{{ $landingPage->cta_text ?? 'Elevate Your Experience' }}</span>
                @endif
                
                <div class="grid grid-cols-2 gap-12 mb-20">
                    <div>
                        <h4 class="font-sans text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-6">Connect</h4>
                        <div class="flex flex-col gap-4 font-sans text-xs tracking-widest">
                            @if($landingPage->social_media['instagram'] ?? false)
                            <a href="https://instagram.com/{{ $landingPage->social_media['instagram'] }}" target="_blank" class="hover:text-white transition text-stone-400">Instagram</a>
                            @endif
                            @if($landingPage->whatsapp_number)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $landingPage->whatsapp_number) }}" target="_blank" class="hover:text-white transition text-stone-400">WhatsApp</a>
                            @endif
                        </div>
                    </div>
                    <div>
                        <h4 class="font-sans text-[10px] font-bold uppercase tracking-widest text-stone-500 mb-6">Location</h4>
                        <p class="font-sans text-xs tracking-widest leading-loose text-stone-400">{{ $outlet->address }}</p>
                    </div>
                </div>

                <div class="pt-8 border-t border-stone-800 flex justify-between items-center">
                    <div>
                        <span class="font-serif text-2xl text-white block mb-2">{{ $outlet->name }}</span>
                        @if(isset($is_editor) && $is_editor)
                            <textarea name="footer_text" class="w-full bg-transparent text-left font-sans text-[10px] tracking-widest opacity-30 resize-none border-0 focus:ring-0 p-0" data-sync="footer_text">{{ $landingPage->footer_text ?? '© '.date('Y').'. crafted with care.' }}</textarea>
                        @else
                            <p class="font-sans text-[10px] tracking-widest opacity-30 uppercase">{{ $landingPage->footer_text ?? '© '.date('Y').'. crafted with care.' }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Map Footer -->
            <div class="w-full md:w-1/2">
                <div class="p-4 bg-stone-800 rounded-sm">
                    <div id="map" class="h-[450px] w-full grayscale contrast-125 opacity-70 hover:opacity-100 transition-opacity duration-500 rounded-lg"></div>
                </div>
            </div>
        </div>
    </footer>
</div>

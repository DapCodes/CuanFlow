@php
    // Template 2: Minimalist Split Layout
    // Hero: Left Text, Right Image (Full Height)
@endphp

<div class="font-sans antialiased text-gray-900">
    <!-- Navbar (Simplified) -->
    <nav class="fixed top-0 left-0 w-full z-50 px-6 py-4 flex justify-between items-center transition-all duration-300" id="navbar">
         <div class="flex items-center gap-3">
            @if($outlet->logo)
                <img src="{{ Storage::url($outlet->logo) }}" class="h-10 w-10 rounded-full object-cover">
            @else
                <div class="h-10 w-10 rounded-full bg-black text-white flex items-center justify-center font-bold">{{ substr($outlet->name, 0, 1) }}</div>
            @endif
            <span class="font-bold text-xl tracking-tight">{{ $outlet->name }}</span>
        </div>
        <div class="hidden md:flex gap-8 text-sm font-medium">
            <a href="#home" class="hover:text-primary transition">Home</a>
            <a href="#products" class="hover:text-primary transition">Shop</a>
            <a href="#about" class="hover:text-primary transition">Story</a>
            <a href="#testimonials" class="hover:text-primary transition">Reviews</a>
            <a href="#contact" class="hover:text-primary transition">Contact</a>
        </div>
         <button class="md:hidden" onclick="toggleMobileMenu()"><i class="fas fa-bars"></i></button>
    </nav>

    <!-- Mobile Menu (Reused ID) -->
    <div class="mobile-overlay" id="mobileOverlay" onclick="toggleMobileMenu()"></div>
    <div class="mobile-menu p-8 flex flex-col gap-6" id="mobileMenu">
        <div class="flex justify-between">
            <span class="font-bold text-xl">Menu</span>
            <button onclick="toggleMobileMenu()"><i class="fas fa-times"></i></button>
        </div>
        <a href="#home" onclick="toggleMobileMenu()" class="text-lg font-medium">Home</a>
        <a href="#products" onclick="toggleMobileMenu()" class="text-lg font-medium">Shop</a>
        <a href="#about" onclick="toggleMobileMenu()" class="text-lg font-medium">Story</a>
        <a href="#testimonials" onclick="toggleMobileMenu()" class="text-lg font-medium">Reviews</a>
        <a href="#contact" onclick="toggleMobileMenu()" class="text-lg font-medium">Contact</a>
    </div>

    <!-- Hero Section: Split -->
    <header id="home" class="relative min-h-screen flex flex-col md:flex-row">
        <!-- Text Side -->
        <div class="w-full md:w-1/2 flex items-center justify-center p-8 md:p-16 bg-white z-10 order-2 md:order-1">
            <div class="max-w-md w-full" data-aos="fade-up">
                @if(isset($is_editor) && $is_editor)
                    <input type="text" value="{{ $landingPage->tagline_text }}" 
                           class="editable-field text-sm font-bold uppercase tracking-widest text-gray-400 mb-4 w-full border-b border-gray-200 focus:outline-none focus:border-black" 
                           placeholder="TAGLINE" data-sync="tagline_text">
                    
                    <textarea rows="2" 
                              class="editable-field text-5xl md:text-7xl font-black leading-none mb-6 text-gray-900 tracking-tighter w-full resize-none border-l-4 border-gray-200 pl-4 focus:outline-none focus:border-black"
                              placeholder="Hero Title" data-sync="hero_title"
                              oninput="autoResize(this)">{{ $landingPage->hero_title }}</textarea>
                    
                    <textarea rows="3" 
                              class="editable-field text-xl text-gray-600 mb-8 font-light leading-relaxed w-full resize-none focus:outline-none bg-gray-50 p-2 rounded"
                              placeholder="Hero Subtitle" data-sync="hero_subtitle"
                              oninput="autoResize(this)">{{ $landingPage->hero_subtitle }}</textarea>
                @else
                    <p class="text-sm font-bold uppercase tracking-widest text-gray-400 mb-4">{{ $landingPage->tagline_text ?? 'Welcome' }}</p>
                    <h1 class="text-5xl md:text-7xl font-black leading-none mb-6 text-gray-900 tracking-tighter">
                        {{ $landingPage->hero_title }}
                    </h1>
                    <p class="text-xl text-gray-600 mb-8 font-light leading-relaxed">
                        {{ $landingPage->hero_subtitle }}
                    </p>
                @endif
                
                <div class="flex gap-4">
                    @if(isset($is_editor) && $is_editor)
                     <div class="relative">
                        <a href="#" class="bg-black text-white px-8 py-4 text-sm font-bold uppercase tracking-widest pointer-events-none opacity-80">
                             <input type="text" value="{{ $landingPage->cta_button_text ?? 'Shop Now' }}" 
                               class="bg-transparent text-center w-24 border-b border-gray-500 focus:border-white outline-none text-white pointer-events-auto"
                               data-sync="cta_button_text">
                        </a>
                     </div>
                    @else
                    <a href="#products" class="bg-black text-white px-8 py-4 text-sm font-bold uppercase tracking-widest hover:bg-gray-800 transition">
                        {{ $landingPage->cta_button_text ?? 'Shop Now' }}
                    </a>
                    @endif
                </div>
            </div>
        </div>
        <!-- Image Side -->
        <div class="w-full md:w-1/2 h-[50vh] md:h-screen bg-gray-100 relative order-1 md:order-2 hero-section-bg">
            @if(isset($is_editor) && $is_editor)
             <!-- Editor Upload Trigger -->
             <div class="absolute bottom-4 right-4 z-20">
                <label for="hero_image" class="cursor-pointer inline-flex items-center gap-2 bg-white text-gray-900 px-4 py-2 rounded shadow text-xs font-bold uppercase hover:bg-gray-100 transition-transform active:scale-95">
                    <i class="fas fa-camera"></i> Change Image
                </label>
             </div>
            @endif
            <img src="{{ $landingPage->hero_image ? Storage::url($landingPage->hero_image) : 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800&q=80' }}" 
                 class="absolute inset-0 w-full h-full object-cover hero-bg-img">
        </div>
    </header>

    <!-- Stats (Minimal) -->
    <section class="border-b border-gray-100 bg-white">
        <div class="grid grid-cols-3 divide-x divide-gray-100">
            <div class="py-12 text-center">
                <span class="block text-3xl font-black">{{ $displaySales ?? '150+' }}</span>
                <span class="text-xs uppercase tracking-widest text-gray-400">Sales</span>
            </div>
            <div class="py-12 text-center">
                <span class="block text-3xl font-black">{{ $outlet->products()->count() }}</span>
                <span class="text-xs uppercase tracking-widest text-gray-400">Products</span>
            </div>
            <div class="py-12 text-center">
                <span class="block text-3xl font-black">100%</span>
                <span class="text-xs uppercase tracking-widest text-gray-400">Happy</span>
            </div>
        </div>
    </section>

    <!-- About (Text Heavy, Clean) -->
    <section id="about" class="py-32 bg-gray-50 px-6">
        <div class="max-w-4xl mx-auto text-center">
            <span class="text-primary font-bold tracking-widest uppercase text-xs mb-4 block">Our Philosophy</span>
            <div class="mb-8">
                @if(isset($is_editor) && $is_editor)
                     <input type="text" value="{{ $landingPage->tagline_text }}"
                            class="editable-field text-3xl md:text-5xl font-serif italic text-center w-full bg-transparent border-0 focus:ring-0 placeholder-gray-300"
                            data-sync="tagline_text"> 
                @else
                    <h2 class="text-3xl md:text-5xl font-serif italic">"{{ $landingPage->tagline_text ?? 'Quality First' }}"</h2>
                @endif
            </div>
            
            <div class="prose prose-lg mx-auto text-gray-600 mb-12">
                 @if(isset($is_editor) && $is_editor)
                    <div id="aboutEditor" class="bg-white p-4 border border-gray-200 text-left rounded">
                        {!! $landingPage->about_text !!}
                    </div>
                 @else
                    {!! $landingPage->about_text ?? '<p>About us description...</p>' !!}
                 @endif
            </div>

            <!-- Vision Mission Grid -->
            <div class="grid md:grid-cols-2 gap-8 text-left mt-16 border-t border-gray-200 pt-16">
                 <div>
                    <h4 class="font-bold uppercase tracking-widest text-sm mb-4">Vision</h4>
                    @if(isset($is_editor) && $is_editor)
                        <textarea name="vision_text" rows="3" class="w-full bg-white border border-gray-200 p-3 rounded text-sm text-gray-600" data-sync="vision_text" oninput="autoResize(this)">{{ $landingPage->vision_text }}</textarea>
                    @else
                        <p class="text-gray-600">{{ $landingPage->vision_text }}</p>
                    @endif
                 </div>
                 <div>
                    <h4 class="font-bold uppercase tracking-widest text-sm mb-4">Mission</h4>
                    @if(isset($is_editor) && $is_editor)
                        <textarea name="mission_text" rows="3" class="w-full bg-white border border-gray-200 p-3 rounded text-sm text-gray-600" data-sync="mission_text" oninput="autoResize(this)">{{ $landingPage->mission_text }}</textarea>
                    @else
                        <p class="text-gray-600">{{ $landingPage->mission_text }}</p>
                    @endif
                 </div>
            </div>
        </div>
    </section>

    <!-- Products (Grid with minimal cards) -->
    <section id="products" class="py-32 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-end mb-16 border-b border-gray-200 pb-6">
                <h2 class="text-4xl font-bold">Selected Items</h2>
                <a href="#" class="hidden md:block text-sm font-bold uppercase tracking-widest hover:underline">View All</a>
            </div>

            @if(isset($is_editor) && $is_editor)
                <p class="mb-8 text-gray-500 bg-gray-100 p-4 rounded text-sm flex items-center gap-2">
                    <i class="fas fa-info-circle"></i> Select up to 3 products to display.
                </p>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-8 gap-y-12">
                @foreach($products as $product)
                    @if(isset($is_editor) && $is_editor)
                        <!-- Editor Mode: Checkbox Selection -->
                        <label class="group cursor-pointer relative">
                             <input type="checkbox" data-product-id="{{ $product->id }}" value="{{ $product->id }}" 
                                   class="peer sr-only product-checkbox"
                                   {{ in_array($product->id, $landingPage->selected_product_ids ?? []) ? 'checked' : '' }}>
                             
                             <div class="aspect-[3/4] bg-gray-100 mb-6 overflow-hidden relative ring-offset-2 peer-checked:ring-2 peer-checked:ring-black">
                                @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" class="w-full h-full object-cover">
                                @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300"><i class="fas fa-image text-3xl"></i></div>
                                @endif
                                <div class="absolute top-2 right-2 bg-black text-white w-6 h-6 flex items-center justify-center rounded-full opacity-0 peer-checked:opacity-100">
                                    <i class="fas fa-check text-xs"></i>
                                </div>
                             </div>
                             <h3 class="font-medium text-lg text-gray-900">{{ $product->name }}</h3>
                             <p class="text-gray-500 mt-1">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                        </label>
                    @else
                     <!-- View Mode -->
                     <div class="group cursor-pointer" onclick="showProductDetail({{ json_encode($product) }})">
                        <div class="aspect-[3/4] bg-gray-100 mb-6 overflow-hidden relative">
                            @if($product->image)
                            <img src="{{ Storage::url($product->image) }}" class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-500">
                            @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300"><i class="fas fa-image text-3xl"></i></div>
                            @endif
                            
                            <button type="button" 
                                    onclick="showProductDetail({{ json_encode([
                                        'name' => $product->name,
                                        'price' => 'Rp ' . number_format($product->selling_price, 0, ',', '.'),
                                        'description' => $product->description ?? 'No description available.',
                                        'image' => $product->image ? Storage::url($product->image) : null,
                                        'category' => $product->category->name ?? 'General',
                                        'unit' => $product->unit->name ?? 'pcs'
                                    ]) }})"
                                    class="absolute bottom-4 right-4 w-10 h-10 bg-white shadow-lg rounded-full flex items-center justify-center hover:bg-black hover:text-white transition z-10">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <h3 class="font-medium text-lg text-gray-900 group-hover:underline">{{ $product->name }}</h3>
                        <p class="text-gray-500 mt-1">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-24 bg-black text-white">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl md:text-5xl font-bold mb-16 text-center">Client Stories</h2>
            
            <div class="grid md:grid-cols-3 gap-8">
                @forelse($testimonials as $testimonial)
                    @if(isset($is_editor) && $is_editor)
                         <label class="block bg-gray-900 p-8 rounded border border-gray-800 cursor-pointer relative">
                             <input type="checkbox" data-testimonial-id="{{ $testimonial->id }}" value="{{ $testimonial->id }}"
                                   class="hidden peer testimonial-checkbox"
                                   {{ in_array($testimonial->id, $landingPage->selected_testimonial_ids ?? []) ? 'checked' : '' }}>
                             
                             <div class="absolute top-4 right-4 text-gray-700 peer-checked:text-white">
                                <i class="fas fa-check-circle"></i>
                             </div>

                             <div class="flex text-yellow-500 mb-4 text-xs">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $testimonial->rating ? '' : 'text-gray-700' }}"></i>
                                @endfor
                             </div>
                             <p class="text-gray-400 mb-6 italic leading-relaxed">"{{ Str::limit($testimonial->content, 100) }}"</p>
                             <div class="flex items-center gap-4">
                                @if($testimonial->image)
                                <img src="{{ Storage::url($testimonial->image) }}" class="w-10 h-10 rounded-full object-cover">
                                @else
                                <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center font-bold text-xs">{{ substr($testimonial->name, 0, 1) }}</div>
                                @endif
                                <span class="font-bold text-sm">{{ $testimonial->name }}</span>
                             </div>
                         </label>
                    @elseif(in_array($testimonial->id, $landingPage->selected_testimonial_ids ?? []))
                         <div class="bg-gray-900 p-8 rounded border border-gray-800">
                             <div class="flex text-yellow-500 mb-4 text-xs">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $testimonial->rating ? '' : 'text-gray-700' }}"></i>
                                @endfor
                             </div>
                             <p class="text-gray-400 mb-6 italic leading-relaxed">"{{ $testimonial->content }}"</p>
                             <div class="flex items-center gap-4">
                                @if($testimonial->image)
                                <img src="{{ Storage::url($testimonial->image) }}" class="w-10 h-10 rounded-full object-cover">
                                @else
                                <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center font-bold text-xs">{{ substr($testimonial->name, 0, 1) }}</div>
                                @endif
                                <div>
                                    <span class="font-bold text-sm block">{{ $testimonial->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $testimonial->role }}</span>
                                </div>
                             </div>
                         </div>
                    @endif
                @empty
                    <div class="col-span-3 text-center text-gray-500">No testimonials available.</div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Testimonial Form Section -->
    @if(!isset($is_editor) || !$is_editor)
    <section id="write-testimonial" class="py-24 bg-gray-50 px-6">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white p-8 md:p-12 shadow-sm border border-gray-100 rounded-2xl">
                <div class="text-center mb-12">
                    <h3 class="text-3xl font-bold mb-4">Share Your Experience</h3>
                    <p class="text-gray-500">How was your experience with {{ $outlet->name }}? We'd love to hear from you.</p>
                </div>

                <form id="testimonialForm" class="space-y-6">
                    @csrf
                    <input type="hidden" name="outlet_id" value="{{ $outlet->id }}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Full Name</label>
                            <input type="text" name="name" required class="w-full bg-gray-50 border-gray-200 focus:border-black focus:ring-0 rounded p-3 text-sm" placeholder="John Doe">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Role / Title</label>
                            <input type="text" name="role" class="w-full bg-gray-50 border-gray-200 focus:border-black focus:ring-0 rounded p-3 text-sm" placeholder="Coffee Enthusiast">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Rating</label>
                        <div class="flex items-center gap-4 bg-gray-50 p-3 rounded">
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
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Your Review</label>
                        <textarea name="content" rows="4" required class="w-full bg-gray-50 border-gray-200 focus:border-black focus:ring-0 rounded p-3 text-sm resize-none" placeholder="Write your honest review here..."></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Photo (Optional)</label>
                        <input type="file" name="image" id="testimonialImage" accept="image/*" class="hidden" onchange="previewTestimonialImage(this)">
                        <label for="testimonialImage" class="cursor-pointer flex items-center justify-center gap-3 bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl p-6 text-gray-400 hover:bg-gray-100 hover:border-gray-300 transition-all group">
                            <i class="fas fa-camera text-xl"></i>
                            <span id="testimonialImageLabel" class="text-sm font-medium">Upload Photo</span>
                        </label>
                    </div>

                    <div class="text-center">
                        <button type="submit" id="btnSubmitTestimonial" class="px-12 py-4 bg-black text-white font-bold uppercase tracking-widest text-sm hover:bg-gray-800 transition-all transform hover:scale-[1.02] active:scale-95">
                            Submit Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    @endif

    <!-- CTA & Footer -->
    <section id="contact" class="bg-white pt-24 pb-12 px-6 border-t border-gray-200">
        <div class="max-w-7xl mx-auto">
             <div class="text-center mb-24">
                <h2 class="text-4xl md:text-6xl font-black mb-8">
                     @if(isset($is_editor) && $is_editor)
                        <textarea rows="1" class="w-full text-center resize-none border-0 focus:ring-0 placeholder-gray-300 font-bold" placeholder="Ready to Order?" data-sync="cta_text" oninput="autoResize(this)">{{ $landingPage->cta_text }}</textarea>
                     @else
                        {{ $landingPage->cta_text ?? 'Ready to Order?' }}
                     @endif
                </h2>
                <div class="flex justify-center gap-4">
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $landingPage->whatsapp_number ?? '') }}" target="_blank" class="px-10 py-5 bg-black text-white font-bold text-lg hover:bg-gray-800 transition rounded-full flex items-center gap-2">
                         <i class="fab fa-whatsapp"></i> Chat on WhatsApp
                    </a>
                </div>
             </div>

             <div class="flex flex-col md:flex-row justify-between items-start border-t-4 border-black pt-12 gap-12">
                 <div class="flex-1">
                     <h4 class="font-bold text-2xl mb-4">{{ $outlet->name }}</h4>
                     <p class="text-gray-500 max-w-xs mb-8">{{ $outlet->address }}</p>
                     
                     <div class="flex gap-6 mb-8">
                          @if($landingPage->social_media['instagram'] ?? false) 
                            <a href="https://instagram.com/{{ $landingPage->social_media['instagram'] }}" target="_blank" class="text-xl hover:text-black text-gray-400 transition">
                                <i class="fab fa-instagram"></i>
                            </a> 
                          @endif
                          @if($landingPage->social_media['tiktok'] ?? false) 
                            <a href="https://tiktok.com/@{{ $landingPage->social_media['tiktok'] }}" target="_blank" class="text-xl hover:text-black text-gray-400 transition">
                                <i class="fab fa-tiktok"></i>
                            </a> 
                          @endif
                           @if($landingPage->whatsapp_number ?? false) 
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $landingPage->whatsapp_number) }}" target="_blank" class="text-xl hover:text-black text-gray-400 transition">
                                <i class="fab fa-whatsapp"></i>
                            </a> 
                          @endif
                     </div>

                     @if(isset($is_editor) && $is_editor)
                        <textarea rows="1" class="text-gray-400 text-sm resize-none border-0 focus:ring-0 bg-transparent p-0 w-full" placeholder="Copyright text" data-sync="footer_text">{{ $landingPage->footer_text }}</textarea>
                    @else
                        <p class="text-sm text-gray-400">{{ $landingPage->footer_text ?? '© '.date('Y').' All rights reserved.' }}</p>
                    @endif
                 </div>

                 <!-- Map Footer -->
                 <div class="w-full md:w-1/2">
                    <div id="map" class="h-64 w-full rounded-2xl shadow-inner bg-gray-100 overflow-hidden ring-1 ring-gray-100"></div>
                 </div>
             </div>
        </div>
    </section>
</div>

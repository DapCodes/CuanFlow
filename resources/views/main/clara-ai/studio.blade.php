@extends('layouts.app')

@section('title', 'Clara AI Studio')

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('clara-ai.index') }}" class="nav-link text-gray-500 hover:text-gray-700">Clara AI</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-bold tracking-tight">AI Studio</span>
</li>
@endsection

@push('styles')
<style>
    .studio-mode-btn {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .studio-mode-btn.active {
        background: linear-gradient(135deg, #658C58 0%, #31694E 100%);
        color: white;
        box-shadow: 0 10px 25px -5px rgba(101, 140, 88, 0.4);
        transform: translateX(4px);
    }
    .studio-mode-btn:not(.active):hover {
        background-color: #f0fdf4;
        transform: translateX(2px);
    }
    .tone-pill {
        transition: all 0.2s ease;
    }
    .tone-pill.active {
        background: #1f2937;
        color: white;
        box-shadow: 0 4px 12px rgba(31, 41, 55, 0.3);
    }
    .lang-toggle.active {
        background: #658C58;
        color: white;
    }
    .output-area {
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    .generate-btn {
        background: linear-gradient(135deg, #658C58 0%, #31694E 100%);
        transition: all 0.3s ease;
    }
    .generate-btn:hover:not(:disabled) {
        box-shadow: 0 12px 30px -8px rgba(101, 140, 88, 0.5);
        transform: translateY(-1px);
    }
    .generate-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .loading-pulse {
        animation: pulse-glow 1.5s ease-in-out infinite;
    }
    @keyframes pulse-glow {
        0%, 100% { opacity: 0.4; }
        50% { opacity: 1; }
    }
    .fade-in {
        animation: fadeIn 0.4s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .char-counter {
        transition: color 0.2s ease;
    }

    /* Scrollbar */
    .output-scroll::-webkit-scrollbar { width: 4px; }
    .output-scroll::-webkit-scrollbar-track { background: transparent; }
    .output-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
    .output-scroll::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

    /* Mobile sidebar */
    @media (max-width: 1023px) {
        .studio-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 40;
            height: 100vh;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        .studio-sidebar.open {
            transform: translateX(0);
        }
    }
</style>
@endpush

@section('content')
<div x-data="studioApp()" class="flex bg-gray-50" style="height: calc(100vh - 64px - 57px);">

    {{-- LEFT SIDE — Mode Selector Sidebar --}}
    <div class="studio-sidebar w-72 bg-white border-r border-gray-200 flex flex-col lg:relative"
         :class="{ 'open': sidebarOpen }">

        {{-- Sidebar Header --}}
        <div class="px-5 py-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex-shrink-0">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-cuan-green to-cuan-dark flex items-center justify-center shadow-lg shadow-emerald-100">
                        <i class="fas fa-wand-magic-sparkles text-white text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-gray-900 tracking-tight">AI STUDIO</h2>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pilih Mode</p>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden p-2 text-gray-400 hover:text-gray-600 rounded-lg">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        {{-- Mode List --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-2">

            {{-- Video Prompter --}}
            <button @click="selectMode('video_prompt')"
                class="studio-mode-btn w-full text-left px-4 py-4 rounded-2xl border border-gray-100"
                :class="{ 'active': mode === 'video_prompt' }">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg"
                         :class="mode === 'video_prompt' ? 'bg-white/20 text-white' : 'bg-purple-50 text-purple-600'">
                        🎬
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-tight"
                           :class="mode === 'video_prompt' ? 'text-white' : 'text-gray-800'">
                            Video Prompter
                        </p>
                        <p class="text-[10px] font-medium mt-0.5"
                           :class="mode === 'video_prompt' ? 'text-white/70' : 'text-gray-400'">
                            Runway, Sora, Pika, Kling
                        </p>
                    </div>
                </div>
            </button>

            {{-- Affiliate Script --}}
            <button @click="selectMode('affiliate_script')"
                class="studio-mode-btn w-full text-left px-4 py-4 rounded-2xl border border-gray-100"
                :class="{ 'active': mode === 'affiliate_script' }">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg"
                         :class="mode === 'affiliate_script' ? 'bg-white/20 text-white' : 'bg-amber-50 text-amber-600'">
                        💰
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-tight"
                           :class="mode === 'affiliate_script' ? 'text-white' : 'text-gray-800'">
                            Affiliate Script
                        </p>
                        <p class="text-[10px] font-medium mt-0.5"
                           :class="mode === 'affiliate_script' ? 'text-white/70' : 'text-gray-400'">
                            TikTok, IG, YouTube
                        </p>
                    </div>
                </div>
            </button>

            {{-- Ads Image Prompt --}}
            <button @click="selectMode('ads_image_prompt')"
                class="studio-mode-btn w-full text-left px-4 py-4 rounded-2xl border border-gray-100"
                :class="{ 'active': mode === 'ads_image_prompt' }">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg"
                         :class="mode === 'ads_image_prompt' ? 'bg-white/20 text-white' : 'bg-sky-50 text-sky-600'">
                        🖼️
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-tight"
                           :class="mode === 'ads_image_prompt' ? 'text-white' : 'text-gray-800'">
                            Ads Image Prompt
                        </p>
                        <p class="text-[10px] font-medium mt-0.5"
                           :class="mode === 'ads_image_prompt' ? 'text-white/70' : 'text-gray-400'">
                            Midjourney, DALL·E, SDXL
                        </p>
                    </div>
                </div>
            </button>
        </div>

        {{-- Back to Chat Link --}}
        <div class="p-4 border-t border-gray-100 flex-shrink-0">
            <a href="{{ route('clara-ai.index') }}"
                class="nav-link flex items-center justify-center gap-2 w-full px-4 py-3 bg-gray-50 hover:bg-gray-100 text-gray-600 rounded-xl text-[10px] font-black uppercase tracking-widest transition-colors">
                <i class="fas fa-comments"></i>
                Kembali ke Chat
            </a>
        </div>
    </div>

    {{-- Mobile sidebar overlay --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-30 lg:hidden"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;"></div>

    {{-- RIGHT SIDE — Input & Output --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Top Bar --}}
        <div class="bg-white border-b border-gray-200 px-4 sm:px-6 py-3 flex-shrink-0">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <h1 class="text-sm font-black text-gray-900 uppercase tracking-tight" x-text="modeTitle"></h1>
                        <p class="text-[10px] font-bold text-cuan-green uppercase tracking-widest" x-text="modeSubtitle"></p>
                    </div>
                </div>

                {{-- Tone & Language Controls --}}
                <div class="hidden sm:flex items-center gap-3">
                    {{-- Tone Selector --}}
                    <div class="flex items-center gap-1 bg-gray-100 rounded-xl p-1">
                        <button @click="tone = 'casual'" class="tone-pill px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider"
                                :class="{ 'active': tone === 'casual' }">Casual</button>
                        <button @click="tone = 'formal'" class="tone-pill px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider"
                                :class="{ 'active': tone === 'formal' }">Formal</button>
                        <button @click="tone = 'aggressive'" class="tone-pill px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider"
                                :class="{ 'active': tone === 'aggressive' }">Agresif</button>
                    </div>

                    {{-- Language Toggle --}}
                    <div class="flex items-center gap-1 bg-gray-100 rounded-xl p-1">
                        <button @click="language = 'id'" class="lang-toggle px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider"
                                :class="{ 'active': language === 'id' }">🇮🇩 ID</button>
                        <button @click="language = 'en'" class="lang-toggle px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider"
                                :class="{ 'active': language === 'en' }">🇺🇸 EN</button>
                    </div>
                </div>
            </div>

            {{-- Mobile Controls --}}
            <div class="sm:hidden flex items-center gap-2 mt-3">
                <div class="flex items-center gap-1 bg-gray-100 rounded-xl p-1 flex-1">
                    <button @click="tone = 'casual'" class="tone-pill flex-1 px-2 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-wider"
                            :class="{ 'active': tone === 'casual' }">Casual</button>
                    <button @click="tone = 'formal'" class="tone-pill flex-1 px-2 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-wider"
                            :class="{ 'active': tone === 'formal' }">Formal</button>
                    <button @click="tone = 'aggressive'" class="tone-pill flex-1 px-2 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-wider"
                            :class="{ 'active': tone === 'aggressive' }">Agresif</button>
                </div>
                <div class="flex items-center gap-1 bg-gray-100 rounded-xl p-1">
                    <button @click="language = 'id'" class="lang-toggle px-2.5 py-1.5 rounded-lg text-[9px] font-black uppercase"
                            :class="{ 'active': language === 'id' }">ID</button>
                    <button @click="language = 'en'" class="lang-toggle px-2.5 py-1.5 rounded-lg text-[9px] font-black uppercase"
                            :class="{ 'active': language === 'en' }">EN</button>
                </div>
            </div>
        </div>

        {{-- Content Area (split: input top, output bottom) --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- Output Panel --}}
            <div class="flex-1 overflow-y-auto output-scroll bg-gray-50 p-4 sm:p-6">
                <div class="max-w-4xl mx-auto">

                    {{-- Empty State --}}
                    <div x-show="!output && !loading && !error" class="flex items-center justify-center h-full min-h-[300px]">
                        <div class="text-center max-w-md fade-in">
                            <div class="w-20 h-20 mx-auto rounded-3xl bg-gradient-to-br from-cuan-green/10 to-cuan-dark/10 flex items-center justify-center mb-5">
                                <span class="text-4xl" x-text="modeEmoji"></span>
                            </div>
                            <h3 class="text-lg font-black text-gray-800 mb-2" x-text="modeTitle"></h3>
                            <p class="text-sm text-gray-500 leading-relaxed" x-text="modeDescription"></p>

                            {{-- Quick prompts --}}
                            <div class="mt-6 space-y-2">
                                <template x-for="(example, idx) in quickPrompts" :key="idx">
                                    <button @click="prompt = example; $refs.promptInput.focus()"
                                        class="w-full px-4 py-3 bg-white border border-gray-100 hover:border-cuan-green/50 rounded-xl text-[10px] font-bold uppercase tracking-wider text-gray-500 hover:text-cuan-green text-left transition-all hover:shadow-md group flex items-center justify-between">
                                        <span x-text="example"></span>
                                        <i class="fas fa-chevron-right text-gray-200 group-hover:text-cuan-green group-hover:translate-x-1 transition-all"></i>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Loading State --}}
                    <div x-show="loading" class="flex items-center justify-center h-full min-h-[300px]">
                        <div class="text-center fade-in">
                            <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-cuan-green to-cuan-dark flex items-center justify-center mb-4 loading-pulse">
                                <i class="fas fa-wand-magic-sparkles text-white text-xl"></i>
                            </div>
                            <p class="text-sm font-black text-gray-700 uppercase tracking-tight">Clara AI Generating...</p>
                            <p class="text-xs text-gray-400 mt-1.5">Ini mungkin memerlukan waktu 15-30 detik</p>
                            <div class="flex justify-center gap-1.5 mt-4">
                                <div class="w-2 h-2 bg-cuan-green rounded-full animate-bounce"></div>
                                <div class="w-2 h-2 bg-cuan-green rounded-full animate-bounce" style="animation-delay: 0.15s"></div>
                                <div class="w-2 h-2 bg-cuan-green rounded-full animate-bounce" style="animation-delay: 0.3s"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Error State --}}
                    <div x-show="error && !loading" class="fade-in">
                        <div class="bg-red-50 border border-red-200 rounded-2xl p-5">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-red-500 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-red-800">Gagal menghasilkan konten</p>
                                    <p class="text-xs text-red-600 mt-1" x-text="error"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Output Result --}}
                    <div x-show="output && !loading" class="fade-in">
                        {{-- Result header --}}
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-cuan-green to-cuan-dark flex items-center justify-center shadow-lg shadow-emerald-100">
                                    <img src="{{ asset('assets/image/clara-ai.png') }}" class="p-1" alt="Clara AI">
                                </div>
                                <div>
                                    <p class="text-xs font-black text-gray-800 uppercase tracking-tight">Hasil — <span x-text="modeTitle"></span></p>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                                        Produk digunakan: <span x-text="dataUsed?.products_count || 0"></span> |
                                        Outlet: <span x-text="dataUsed?.outlet_name || '-'"></span>
                                    </p>
                                </div>
                            </div>
                            <button @click="copyOutput()"
                                class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:border-cuan-green rounded-xl text-[10px] font-black uppercase tracking-wider text-gray-600 hover:text-cuan-green transition-all hover:shadow-md active:scale-95">
                                <i class="fas" :class="copied ? 'fa-check text-cuan-green' : 'fa-copy'"></i>
                                <span x-text="copied ? 'Tersalin!' : 'Salin'"></span>
                            </button>
                        </div>

                        {{-- The actual output --}}
                        <div class="bg-white border border-gray-100 rounded-2xl p-5 sm:p-6 shadow-sm border-l-4 border-l-cuan-green">
                            <div class="output-area text-sm text-gray-800 leading-relaxed" x-text="output"></div>
                        </div>

                        {{-- Data used badge --}}
                        <div x-show="dataUsed?.top_products?.length" class="mt-3 flex flex-wrap gap-2">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest py-1">Produk referensi:</span>
                            <template x-for="(product, idx) in (dataUsed?.top_products || [])" :key="idx">
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-[10px] font-bold" x-text="product"></span>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Input Area (Bottom) --}}
            <div class="bg-white border-t border-gray-200 flex-shrink-0 px-4 sm:px-6 py-4">
                <div class="max-w-4xl mx-auto">
                    <div class="flex items-end gap-3">
                        <div class="flex-1 relative">
                            <textarea x-ref="promptInput"
                                x-model="prompt"
                                @keydown.enter.ctrl="submitGenerate()"
                                @keydown.enter.meta="submitGenerate()"
                                placeholder="Deskripsikan apa yang ingin Anda buat..."
                                maxlength="2000"
                                rows="2"
                                class="w-full px-5 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green focus:bg-white text-sm transition-all shadow-sm resize-none"
                                :disabled="loading"></textarea>
                            <span class="absolute bottom-2 right-3 text-[10px] font-bold char-counter"
                                  :class="prompt.length > 1800 ? 'text-red-500' : 'text-gray-300'"
                                  x-text="prompt.length + '/2000'"></span>
                        </div>
                        <button @click="submitGenerate()"
                            :disabled="loading || !prompt.trim()"
                            class="generate-btn w-12 h-12 text-white rounded-2xl font-black flex items-center justify-center flex-shrink-0 active:scale-95">
                            <i class="fas" :class="loading ? 'fa-spinner fa-spin' : 'fa-bolt'"></i>
                        </button>
                    </div>
                    <p class="text-[10px] text-gray-400 text-center mt-2 font-medium">
                        Tekan <kbd class="px-1.5 py-0.5 bg-gray-100 rounded text-gray-500 text-[9px] font-bold">Ctrl+Enter</kbd> untuk generate · Clara AI dapat membuat kesalahan
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function studioApp() {
    return {
        mode: 'video_prompt',
        tone: 'casual',
        language: 'id',
        prompt: '',
        output: '',
        error: '',
        loading: false,
        copied: false,
        sidebarOpen: false,
        dataUsed: null,

        get modeTitle() {
            return {
                'video_prompt': 'Video Prompter',
                'affiliate_script': 'Affiliate Script Generator',
                'ads_image_prompt': 'Ads Image Prompt Generator',
            }[this.mode] || 'AI Studio';
        },

        get modeSubtitle() {
            return {
                'video_prompt': 'Prompt untuk AI Video Tools',
                'affiliate_script': 'Script Konversi Tinggi',
                'ads_image_prompt': 'Prompt untuk AI Image Tools',
            }[this.mode] || '';
        },

        get modeEmoji() {
            return { 'video_prompt': '🎬', 'affiliate_script': '💰', 'ads_image_prompt': '🖼️' }[this.mode] || '✨';
        },

        get modeDescription() {
            return {
                'video_prompt': 'Buat prompt sinematik yang detail untuk tools AI video seperti Runway, Sora, Pika, dan Kling. Termasuk breakdown scene, pergerakan kamera, pencahayaan, dan keywords.',
                'affiliate_script': 'Generate script affiliate yang high-converting dengan hook, problem statement, CTA, dan adaptasi platform untuk TikTok, Instagram, dan YouTube.',
                'ads_image_prompt': 'Buat prompt gambar iklan profesional untuk Midjourney, DALL·E, dan SDXL dengan komposisi visual, color grading, dan sudut pandang marketing.',
            }[this.mode] || '';
        },

        get quickPrompts() {
            return {
                'video_prompt': [
                    'Buat video promosi produk best seller kami',
                    'Video cinematic behind the scene proses pembuatan',
                    'Konten unboxing produk untuk social media',
                ],
                'affiliate_script': [
                    'Script jualan produk best seller kami',
                    'Script review produk untuk TikTok',
                    'Script promo diskon akhir bulan',
                ],
                'ads_image_prompt': [
                    'Gambar iklan produk unggulan untuk Instagram',
                    'Banner promo diskon 50%',
                    'Desain menu board premium dengan foto produk',
                ],
            }[this.mode] || [];
        },

        selectMode(newMode) {
            this.mode = newMode;
            this.output = '';
            this.error = '';
            this.dataUsed = null;
            this.sidebarOpen = false;
        },

        async submitGenerate() {
            if (this.loading || !this.prompt.trim()) return;

            this.loading = true;
            this.output = '';
            this.error = '';
            this.dataUsed = null;
            this.copied = false;

            try {
                const res = await fetch('{{ route("clara-ai.generate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        mode: this.mode,
                        prompt: this.prompt,
                        tone: this.tone,
                        language: this.language,
                    }),
                });

                const data = await res.json();

                if (data.success) {
                    this.output = data.result;
                    this.dataUsed = data.data_used || null;
                } else {
                    this.error = data.message || 'Terjadi kesalahan yang tidak terduga.';
                }
            } catch (err) {
                this.error = 'Koneksi gagal. Silakan periksa jaringan Anda dan coba lagi.';
                console.error('Studio generate error:', err);
            } finally {
                this.loading = false;
            }
        },

        async copyOutput() {
            if (!this.output) return;
            try {
                await navigator.clipboard.writeText(this.output);
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            } catch (err) {
                // Fallback
                const ta = document.createElement('textarea');
                ta.value = this.output;
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            }
        },
    };
}
</script>
@endpush
@endsection

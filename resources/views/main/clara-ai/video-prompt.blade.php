@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Video Prompt Generator')

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('clara-ai.index') }}" class="text-gray-400 hover:text-gray-900 transition-colors">Clara AI</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-bold tracking-tight">Video Prompt AI</span>
</li>
@endsection

@push('styles')
<style>
    .output-area { white-space: pre-wrap; word-wrap: break-word; word-break: break-word; }
    .tone-pill.active { background: #1f2937; color: white; }
    .lang-toggle.active { background-color: var(--cuan-green, #658C58); color: white; }
    .fade-in { animation: fadeIn 0.3s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
    
    /* Custom Scrollbar for history */
    .history-scroll::-webkit-scrollbar { width: 4px; }
    .history-scroll::-webkit-scrollbar-track { background: transparent; }
    .history-scroll::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 4px; }
    .history-scroll:hover::-webkit-scrollbar-thumb { background: #d1d5db; }
</style>
@endpush

@section('content')
<main x-data="videoPromptApp()" class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Header Section --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Video Prompt AI
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Rancang skenario video cinematic untuk model AI seperti Runway, Sora, dan Kling.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-0.5 bg-white border border-gray-200 rounded-lg p-0.5 shadow-sm">
                    <button @click="tone = 'casual'" class="tone-pill px-3 py-1.5 rounded-md text-xs font-bold uppercase" :class="{ 'active': tone === 'casual' }">Casual</button>
                    <button @click="tone = 'formal'" class="tone-pill px-3 py-1.5 rounded-md text-xs font-bold uppercase" :class="{ 'active': tone === 'formal' }">Formal</button>
                    <button @click="tone = 'aggressive'" class="tone-pill px-3 py-1.5 rounded-md text-xs font-bold uppercase" :class="{ 'active': tone === 'aggressive' }">Agresif</button>
                </div>
                <div class="flex items-center gap-0.5 bg-white border border-gray-200 rounded-lg p-0.5 shadow-sm">
                    <button @click="language = 'id'" class="lang-toggle px-3 py-1.5 rounded-md text-xs font-bold uppercase" :class="{ 'active': language === 'id' }">ID</button>
                    <button @click="language = 'en'" class="lang-toggle px-3 py-1.5 rounded-md text-xs font-bold uppercase" :class="{ 'active': language === 'en' }">EN</button>
                </div>
            </div>
        </section>

        {{-- Main Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            
            {{-- LEFT COLUMN: Generative Interface (Span 3) --}}
            <div class="lg:col-span-3 space-y-6">
                
                {{-- Input Area --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-black text-gray-600 uppercase tracking-widest">Skenario Video</span>
                        </div>
                        <span class="text-xs text-gray-400 font-medium" x-text="prompt.length + ' / 2000'"></span>
                    </div>
                    <textarea x-model="prompt" @keydown.ctrl.enter="submit()" @keydown.meta.enter="submit()"
                        placeholder="Deskripsikan video cinematic yang ingin dibuat. Contoh: Video slow-motion close-up tetesan saus pedas di atas takoyaki panas, suasana street food neon Jepang..."
                        maxlength="2000" rows="5"
                        class="w-full px-5 py-4 border-0 focus:ring-0 text-sm resize-none bg-white placeholder-gray-300"
                        :disabled="loading"></textarea>
                    
                    {{-- Quick Actions & Submit --}}
                    <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex flex-wrap items-center justify-between gap-4">
                        <div class="flex gap-2">
                            <template x-for="q in quickPrompts" :key="q">
                                <button @click="prompt = q" class="px-3 py-1.5 bg-white border border-gray-200 hover:border-cuan-green rounded-lg text-[10px] font-bold text-gray-500 hover:text-cuan-green transition-colors shadow-sm">
                                    <span x-text="q"></span>
                                </button>
                            </template>
                        </div>
                        <button @click="submit()" :disabled="loading || !prompt.trim()"
                            class="px-6 py-2.5 bg-cuan-green hover:bg-cuan-dark disabled:opacity-40 disabled:cursor-not-allowed text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-emerald-100 active:scale-95 flex items-center gap-2">
                            <span x-text="loading ? 'Generating...' : 'Generate Prompt'"></span>
                        </button>
                    </div>
                </div>

                {{-- Status / Info States --}}
                <div x-show="!output && !loading && !error && history.length === 0" class="bg-white border text-center border-gray-200 rounded-2xl px-6 py-12 shadow-sm fade-in">
                    <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-4">
                       <i class="fa-solid fa-video text-2xl text-gray-300"></i>
                    </div>
                    <h3 class="text-base font-black text-gray-800 tracking-tight">Belum ada prompt video</h3>
                    <p class="text-sm text-gray-400 mt-2 max-w-sm mx-auto">Tuliskan ide skenario video Anda di atas, Clara AI akan meracik prompt video berskala cinematic.</p>
                </div>

                <div x-show="loading" class="bg-white border text-center border-gray-200 rounded-2xl px-6 py-12 shadow-sm fade-in">
                    <div class="flex justify-center gap-2 mb-4">
                        <div class="w-3 h-3 bg-cuan-green rounded-full animate-bounce"></div>
                        <div class="w-3 h-3 bg-cuan-green rounded-full animate-bounce" style="animation-delay:0.15s"></div>
                        <div class="w-3 h-3 bg-cuan-green rounded-full animate-bounce" style="animation-delay:0.3s"></div>
                    </div>
                    <p class="text-sm font-bold text-gray-600">Menyusun komposisi kamera & lighting...</p>
                </div>

                <div x-show="error && !loading" class="bg-red-50 border border-red-200 rounded-2xl px-5 py-4 fade-in flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                    <p class="text-sm font-medium text-red-600" x-text="error"></p>
                </div>

                {{-- Output Result (Chat bubble style but wider) --}}
                <div x-show="output && !loading" class="fade-in">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-cuan-green flex items-center justify-center flex-shrink-0 shadow-lg shadow-emerald-100">
                            <img src="{{ asset('assets/image/clara-ai.png') }}" class="p-1" alt="Clara AI">
                        </div>
                        <div class="flex-1 space-y-3">
                            <div class="bg-white border border-gray-200 text-gray-800 rounded-2xl rounded-tl-none p-6 shadow-sm">
                                <p class="output-area text-sm leading-relaxed" x-text="output"></p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="copyOutput()"
                                    class="px-4 py-2 bg-white border border-gray-200 hover:border-cuan-green rounded-xl text-xs font-bold uppercase tracking-widest text-gray-500 hover:text-cuan-green transition-all shadow-sm flex items-center gap-2">
                                    <i class="fas" :class="copied ? 'fa-check text-cuan-green' : 'fa-copy'"></i>
                                    <span x-text="copied ? 'Tersalin' : 'Salin Text'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT COLUMN: History Sidebar (Span 1) --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden h-full flex flex-col max-h-[calc(100vh-12rem)] sticky top-6">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                        <h2 class="text-xs font-black text-gray-800 uppercase tracking-widest">History</h2>
                        <button @click="clearHistory" x-show="history.length > 0" class="text-[10px] text-red-500 hover:text-red-700 font-bold tracking-wider">Hapus Semua</button>
                    </div>
                    
                    <div class="flex-1 overflow-y-auto history-scroll p-4 space-y-3 bg-gray-50/30">
                        <template x-if="history.length === 0">
                            <div class="text-center py-8">
                                <i class="fa-solid fa-clock-rotate-left text-gray-300 text-2xl mb-2"></i>
                                <p class="text-xs text-gray-400 mt-2">Belum ada history</p>
                            </div>
                        </template>
                        
                        <template x-for="(item, index) in history" :key="item.id">
                            <button @click="loadHistory(item)" class="w-full text-left bg-white p-3 rounded-xl border border-gray-200 hover:border-cuan-green/50 hover:shadow-md transition-all group">
                                <div class="flex items-start justify-between mb-1">
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-1.5 py-0.5 rounded bg-gray-100 text-[8px] font-black uppercase tracking-wider text-gray-500" x-text="item.language"></span>
                                        <span class="px-1.5 py-0.5 rounded bg-amber-50 text-[8px] font-black uppercase tracking-wider text-amber-600" x-text="item.tone"></span>
                                    </div>
                                    <span class="text-[9px] text-gray-400 font-medium whitespace-nowrap" x-text="formatTime(item.timestamp)"></span>
                                </div>
                                <p class="text-xs text-gray-800 font-medium line-clamp-2 leading-snug mt-1.5" x-text="item.prompt"></p>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

@push('scripts')
<script>
function videoPromptApp() {
    return {
        prompt: '', output: '', error: '', loading: false, copied: false,
        tone: 'casual', language: 'id',
        history: [],
        storageKey: 'clara_video_prompt_history',
        quickPrompts: [
            'Produk best seller',
            'Behind the scene',
        ],
        init() {
            const saved = localStorage.getItem(this.storageKey);
            if(saved) {
                try { this.history = JSON.parse(saved); } catch(e) {}
            }
        },
        async submit() {
            if (this.loading || !this.prompt.trim()) return;
            this.loading = true; this.output = ''; this.error = ''; this.copied = false;
            try {
                const res = await fetch('{{ route("clara-ai.generate") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ mode: 'video_prompt', prompt: this.prompt, tone: this.tone, language: this.language })
                });
                const data = await res.json();
                if (data.success) { 
                    this.output = data.result; 
                    this.saveToHistory(this.prompt, this.output, this.tone, this.language);
                } else { 
                    this.error = data.message || 'Gagal menghasilkan konten.'; 
                }
            } catch (e) { this.error = 'Koneksi gagal. Coba lagi.'; } finally { this.loading = false; }
        },
        saveToHistory(prompt, output, tone, language) {
            const newItem = {
                id: Date.now(),
                timestamp: new Date().toISOString(),
                prompt: prompt,
                output: output,
                tone: tone,
                language: language
            };
            this.history.unshift(newItem);
            if(this.history.length > 20) this.history.pop();
            localStorage.setItem(this.storageKey, JSON.stringify(this.history));
        },
        loadHistory(item) {
            this.prompt = item.prompt;
            this.output = item.output;
            this.tone = item.tone;
            this.language = item.language;
            this.error = '';
            
            // Scroll to output
            window.scrollTo({ top: 300, behavior: 'smooth' });
        },
        clearHistory() {
            if(confirm('Hapus semua history video prompt?')) {
                this.history = [];
                localStorage.removeItem(this.storageKey);
            }
        },
        formatTime(isoString) {
            const date = new Date(isoString);
            return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' (' + date.toLocaleDateString('id-ID', {day: 'numeric', month: 'short'}) + ')';
        },
        async copyOutput() {
            if (!this.output) return;
            try { await navigator.clipboard.writeText(this.output); } catch { const t = document.createElement('textarea'); t.value = this.output; t.style.cssText = 'position:fixed;opacity:0'; document.body.appendChild(t); t.select(); document.execCommand('copy'); document.body.removeChild(t); }
            this.copied = true; setTimeout(() => this.copied = false, 2000);
        }
    };
}
</script>
@endpush
@endsection

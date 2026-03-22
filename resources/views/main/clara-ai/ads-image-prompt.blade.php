@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Ads Image Prompt Generator')

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('clara-ai.index') }}" class="text-gray-400 hover:text-gray-900 transition-colors">Clara AI</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-bold tracking-tight">Ads Image AI</span>
</li>
@endsection

@push('styles')
<style>
    .output-area { white-space: pre-wrap; word-wrap: break-word; word-break: break-word; }
    .tone-pill.active { background: #1f2937; color: white; }
    .lang-toggle.active { background-color: var(--cuan-green, #658C58); color: white; }
    .fade-in { animation: fadeIn 0.3s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
    .history-scroll::-webkit-scrollbar { width: 4px; }
    .history-scroll::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 4px; }
</style>
@endpush

@section('content')
<main x-data="imagePromptApp()" class="flex-grow py-8 px-4 bg-gray-50 h-full flex flex-col">
    <div class="max-w-7xl mx-auto space-y-6 w-full flex flex-col h-full relative">

        {{-- HEADER --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Ads Image AI
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Buat prompt gambar iklan profesional untuk Midjourney, DALL-E, dan Stable Diffusion.
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

        {{-- MAIN CONTENT AREA --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 flex-1 pb-24">
            
            {{-- Center Flow (Span 3) --}}
            <div class="lg:col-span-3 pb-8">
                
                {{-- Idle State (Cards) --}}
                <div x-show="!output && !loading && !error" class="fade-in">
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8 text-center mb-6">
                        <h2 class="text-lg font-black text-gray-800 uppercase tracking-tight">Prompt Gambar Profesional</h2>
                        <p class="text-sm text-gray-400 mt-2 max-w-md mx-auto">Clara AI akan menyusun prompt Midjourney/DALL-E lengkap dengan setting kamera dan visual.</p>
                    </div>

                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 px-2">Kumpulan Ide Cepat</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <template x-for="q in quickCards" :key="q.title">
                            <button @click="prompt = q.prompt" class="bg-white border text-left border-gray-200 hover:border-cuan-green/50 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all group overflow-hidden relative">
                                <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
                                    <i :class="q.icon" class="text-6xl"></i>
                                </div>
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-cuan-green mb-3 relative z-10">
                                    <i :class="q.icon" class="text-sm"></i>
                                </div>
                                <h3 class="text-xs font-black text-gray-800 uppercase tracking-wide mb-1 relative z-10" x-text="q.title"></h3>
                                <p class="text-[10px] text-gray-400 font-medium line-clamp-2 relative z-10" x-text="q.prompt"></p>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Loading State --}}
                <div x-show="loading" class="bg-white rounded-2xl border border-gray-200 shadow-sm p-12 text-center fade-in h-64 flex flex-col items-center justify-center">
                    <div class="relative w-16 h-16 mb-6">
                        <div class="absolute inset-0 border-4 border-gray-100 rounded-full"></div>
                        <div class="absolute inset-0 border-4 border-cuan-green rounded-full border-t-transparent animate-spin"></div>
                        <i class="fa-solid fa-paintbrush absolute inset-0 m-auto w-fit h-fit text-cuan-green"></i>
                    </div>
                    <p class="text-sm font-bold text-gray-800 uppercase tracking-widest">Melukis Prompt...</p>
                </div>

                {{-- Error State --}}
                <div x-show="error && !loading" class="bg-red-50 border border-red-200 rounded-2xl p-6 fade-in">
                    <div class="flex items-center gap-3 justify-center">
                        <i class="fa-solid fa-brake-warning text-red-500 text-2xl"></i>
                        <p class="text-sm font-bold text-red-700" x-text="error"></p>
                    </div>
                </div>

                {{-- Output State --}}
                <div x-show="output && !loading" class="fade-in">
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden border-t-4 border-t-cuan-green">
                        <div class="px-6 py-4 border-b border-gray-50 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gray-50/50">
                            <div>
                                <h3 class="text-xs font-black text-gray-800 uppercase tracking-widest">Prompt Siap Pakai</h3>
                                <p class="text-[10px] text-gray-400 font-medium">Salin dan gunakan di AI Generator pilihan Anda</p>
                            </div>
                            <button @click="copyOutput()"
                                class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold uppercase tracking-widest text-gray-600 hover:text-cuan-green hover:border-cuan-green transition-all shadow-sm">
                                <i class="fas mr-1.5" :class="copied ? 'fa-check text-cuan-green' : 'fa-copy'"></i>
                                <span x-text="copied ? 'Tersalin!' : 'Copy Prompt'"></span>
                            </button>
                        </div>
                        <div class="p-6">
                            <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 text-gray-300 relative group overflow-hidden">
                                <i class="fa-solid fa-quote-left absolute -top-2 -left-2 text-4xl text-gray-800/50"></i>
                                <p class="output-area text-sm leading-relaxed font-mono relative z-10" x-text="output"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column (Span 1) --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden h-full flex flex-col max-h-[calc(100vh-14rem)] sticky top-6">
                    <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                        <div class="flex items-center gap-2 text-gray-500">
                            <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                            <h2 class="text-[10px] font-black uppercase tracking-widest">Gallery History</h2>
                        </div>
                        <button @click="clearHistory" x-show="history.length > 0" class="text-[10px] text-red-500 hover:text-red-700 font-bold uppercase tracking-wider">Clear</button>
                    </div>
                    
                    <div class="flex-1 overflow-y-auto history-scroll p-3 space-y-2 bg-gray-50/30">
                        <template x-if="history.length === 0">
                            <div class="text-[10px] text-gray-400 text-center py-8">Belum ada history.</div>
                        </template>
                        
                        <template x-for="item in history" :key="item.id">
                            <button @click="loadHistory(item)" class="w-full text-left bg-white p-3 rounded-xl border border-gray-100 hover:border-cuan-green hover:shadow-md transition-all group overflow-hidden">
                                <div class="flex items-center justify-between mb-1.5 relative z-10">
                                    <span class="text-[8px] text-gray-400 font-bold" x-text="formatTime(item.timestamp)"></span>
                                    <div class="flex gap-1">
                                        <div class="w-1.5 h-1.5 rounded-full" :class="item.language === 'en' ? 'bg-blue-400' : 'bg-red-400'"></div>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-700 font-medium line-clamp-3 leading-tight relative z-10" x-text="item.prompt"></p>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

        </div>

        {{-- FLOATING PROMPT BAR --}}
        <div class="fixed bottom-0 left-0 right-0 z-40 bg-white/80 backdrop-blur-md border-t border-gray-200 shadow-[0_-10px_40px_-10px_rgba(0,0,0,0.1)] py-4 px-4">
            <div class="max-w-4xl mx-auto md:ml-64 transition-all pr-4"> {{-- Adjust for sidebar --}}
                <div class="relative flex items-end gap-3 bg-white p-2 rounded-3xl border border-gray-200 shadow-lg focus-within:border-emerald-300 focus-within:ring-4 focus-within:ring-emerald-50 transition-all">
                    
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-cuan-green to-emerald-400 flex items-center justify-center flex-shrink-0 shadow-inner">
                        <i class="fa-solid fa-paintbrush text-white text-lg"></i>
                    </div>
                    
                    <div class="flex-1 min-w-0 pb-1">
                        <textarea x-model="prompt" @keydown.ctrl.enter="submit()" @keydown.meta.enter="submit()"
                            placeholder="Deskripsikan ide visual Ads Anda di sini..."
                            maxlength="2000" rows="1" x-ref="promptInput"
                            @input="resizeTextarea"
                            class="w-full px-2 py-2 border-0 focus:ring-0 text-sm resize-none bg-transparent placeholder-gray-400"
                            style="min-height: 40px; max-height: 120px;"
                            :disabled="loading"></textarea>
                    </div>

                    <div class="flex-shrink-0 flex items-center pb-1 pr-1">
                         <button @click="submit()" :disabled="loading || !prompt.trim()"
                            class="w-12 h-12 flex items-center justify-center bg-gray-900 hover:bg-black disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed text-white rounded-2xl transition-all shadow-md active:scale-95 group">
                            <i class="fas" :class="loading ? 'fa-spinner fa-spin' : 'fa-paper-plane group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform'"></i>
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </div>
</main>

@push('scripts')
<script>
function imagePromptApp() {
    return {
        prompt: '', output: '', error: '', loading: false, copied: false,
        tone: 'casual', language: 'id',
        history: [],
        storageKey: 'clara_image_prompt_history',
        quickCards: [
            { icon: 'fa-solid fa-box-open', title: 'Product Display', prompt: 'Gambar produk di studio dengan pencahayaan dramatis dari samping, background minimalis.' },
            { icon: 'fa-solid fa-utensils', title: 'Food Detail', prompt: 'Foto makro makanan yang menggugah selera, fokus tajam pada tekstur makanan, uap panas terlihat.' },
            { icon: 'fa-solid fa-users', title: 'Lifestyle Ads', prompt: 'Orang menggunakan produk dengan bahagia di taman saat golden hour, warna hangat.' },
        ],
        init() {
            const saved = localStorage.getItem(this.storageKey);
            if(saved) {
                try { this.history = JSON.parse(saved); } catch(e) {}
            }
        },
        resizeTextarea() {
            const el = this.$refs.promptInput;
            el.style.height = 'auto';
            el.style.height = (el.scrollHeight) + 'px';
        },
        async submit() {
            if (this.loading || !this.prompt.trim()) return;
            this.loading = true; this.output = ''; this.error = ''; this.copied = false;
            
            // scroll page up slightly
            window.scrollTo({ top: 0, behavior: 'smooth' });

            try {
                const res = await fetch('{{ route("clara-ai.generate") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ mode: 'ads_image', prompt: this.prompt, tone: this.tone, language: this.language })
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
            const newItem = { id: Date.now(), timestamp: new Date().toISOString(), prompt, output, tone, language };
            this.history.unshift(newItem);
            if(this.history.length > 20) this.history.pop();
            localStorage.setItem(this.storageKey, JSON.stringify(this.history));
            
            // reset textarea
            setTimeout(() => this.resizeTextarea(), 10);
        },
        loadHistory(item) {
            this.prompt = item.prompt; this.output = item.output;
            this.tone = item.tone; this.language = item.language;
            this.error = '';
            setTimeout(() => this.resizeTextarea(), 10);
            window.scrollTo({ top: 100, behavior: 'smooth' });
        },
        clearHistory() {
            if(confirm('Hapus semua history?')) {
                this.history = []; localStorage.removeItem(this.storageKey);
            }
        },
        formatTime(iso) {
            const d = new Date(iso);
            return d.getDate() + '/' + (d.getMonth()+1) + ' ' + String(d.getHours()).padStart(2,'0') + ':' + String(d.getMinutes()).padStart(2,'0');
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

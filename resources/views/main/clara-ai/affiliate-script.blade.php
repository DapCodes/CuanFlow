@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Affiliate Script Generator')

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('clara-ai.index') }}" class="text-gray-400 hover:text-gray-900 transition-colors">Clara AI</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-bold tracking-tight">Script Generator AI</span>
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
<main x-data="scriptGenApp()" class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Script Generator AI
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Buat script konten affiliate yang high-converting untuk TikTok, Instagram, dan YouTube.
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

        {{-- WORKSPACE GRID --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- 1. Input Panel (Col 4) --}}
            <div class="lg:col-span-4 space-y-4">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col h-full min-h-[400px]">
                    <div class="px-5 py-3 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                        <p class="text-[10px] font-black text-gray-600 uppercase tracking-widest">Deskripsi Promosi</p>
                        <span class="text-[10px] text-gray-400 font-bold" x-text="prompt.length + '/2000'"></span>
                    </div>
                    
                    <textarea x-model="prompt" @keydown.ctrl.enter="submit()" @keydown.meta.enter="submit()"
                        placeholder="Mau jualan apa hari ini? Contoh: Script soft selling untuk es kopi susu aren, target anak senja..."
                        maxlength="2000"
                        class="flex-1 w-full px-5 py-4 border-0 focus:ring-0 text-sm resize-none bg-white placeholder-gray-300"
                        :disabled="loading"></textarea>
                        
                    <div class="p-4 border-t border-gray-100 bg-white">
                        <button @click="submit()" :disabled="loading || !prompt.trim()"
                            class="w-full py-3.5 bg-cuan-green hover:bg-cuan-dark disabled:opacity-40 disabled:cursor-not-allowed text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-emerald-100 active:scale-95 flex items-center justify-center gap-2">
                            <span x-text="loading ? 'Menulis...' : 'Generate Script'"></span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- 2. Output Panel (Col 5) --}}
            <div class="lg:col-span-5 relative min-h-[400px]">
                
                {{-- Empty State --}}
                <div x-show="!output && !loading && !error" class="absolute inset-0 bg-white rounded-2xl border border-gray-200 shadow-sm p-8 flex flex-col items-center justify-center text-center fade-in">
                    <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-file-lines text-2xl text-gray-300"></i>
                    </div>
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-tight mb-2">Kertas Masih Kosong</h3>
                    <p class="text-xs text-gray-400 max-w-xs">Isi deskripsi promosi di sebelah kiri. Clara AI akan membuatkan Hook & Call-To-Action yang mengkonversi penjualan.</p>
                </div>

                {{-- Loading State --}}
                <div x-show="loading" class="absolute inset-0 bg-white rounded-2xl border border-gray-200 shadow-sm p-8 flex flex-col items-center justify-center text-center fade-in z-10">
                    <i class="fa-solid fa-pen-nib text-3xl text-cuan-green animate-bounce mb-4"></i>
                    <p class="text-sm font-bold text-gray-800">Merangkai kata-kata viral...</p>
                </div>

                {{-- Error State --}}
                <div x-show="error && !loading" class="bg-red-50 border border-red-200 rounded-2xl p-6 fade-in mb-4">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-triangle-exclamation text-red-500 text-xl"></i>
                        <p class="text-sm font-bold text-red-700" x-text="error"></p>
                    </div>
                </div>

                {{-- Result --}}
                <div x-show="output && !loading" class="bg-white rounded-2xl border border-gray-200 shadow-sm h-full flex flex-col fade-in">
                    <div class="px-5 py-3 border-b border-gray-100 bg-emerald-50/30 flex items-center justify-between overflow-hidden relative">
                        <div class="flex items-center gap-2 relative z-10">
                            <p class="text-[10px] font-black text-gray-800 uppercase tracking-widest">Hasil Script Copywriting</p>
                        </div>
                        <button @click="copyOutput()"
                            class="relative z-10 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-[10px] font-bold uppercase tracking-wider text-gray-600 hover:text-cuan-green hover:border-cuan-green transition-all shadow-sm">
                            <i class="fas mr-1" :class="copied ? 'fa-check text-cuan-green' : 'fa-copy'"></i>
                            <span x-text="copied ? 'Tersalin!' : 'Salin Text'"></span>
                        </button>
                    </div>
                    <div class="p-5 flex-1 overflow-y-auto" style="max-height: calc(100vh - 300px);">
                        <p class="output-area text-sm text-gray-700 leading-relaxed" x-text="output"></p>
                    </div>
                </div>
            </div>

            {{-- 3. History Panel (Col 3) --}}
            <div class="lg:col-span-3">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col h-full max-h-[calc(100vh-12rem)] min-h-[400px]">
                    <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                        <div class="flex items-center gap-2 text-gray-500">
                            <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                            <h2 class="text-[10px] font-black uppercase tracking-widest">Riwayat Script</h2>
                        </div>
                        <button @click="clearHistory" x-show="history.length > 0" class="text-[10px] text-red-500 hover:text-red-700 font-bold uppercase tracking-wider">Hapus</button>
                    </div>
                    
                    <div class="flex-1 overflow-y-auto history-scroll p-3 space-y-2 bg-gray-50/30">
                        <template x-if="history.length === 0">
                            <div class="text-[10px] text-gray-400 text-center py-8">Belum ada history.</div>
                        </template>
                        
                        <template x-for="item in history" :key="item.id">
                            <button @click="loadHistory(item)" class="w-full text-left bg-white p-3 rounded-xl border border-gray-100 hover:border-cuan-green hover:shadow-md transition-all">
                                <span class="block text-[8px] text-gray-400 font-bold mb-1" x-text="formatTime(item.timestamp)"></span>
                                <p class="text-xs text-gray-700 font-medium line-clamp-3 leading-tight" x-text="item.prompt"></p>
                                <div class="flex items-center gap-1 mt-2">
                                    <span class="px-1.5 py-0.5 rounded bg-gray-100 text-[8px] font-black uppercase text-gray-500" x-text="item.language"></span>
                                    <span class="px-1.5 py-0.5 rounded bg-blue-50 text-[8px] font-black uppercase text-blue-600" x-text="item.tone"></span>
                                </div>
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
function scriptGenApp() {
    return {
        prompt: '', output: '', error: '', loading: false, copied: false,
        tone: 'casual', language: 'id',
        history: [],
        storageKey: 'clara_script_prompt_history',
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
                    body: JSON.stringify({ mode: 'affiliate_script', prompt: this.prompt, tone: this.tone, language: this.language })
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
        },
        loadHistory(item) {
            this.prompt = item.prompt; this.output = item.output;
            this.tone = item.tone; this.language = item.language;
            this.error = '';
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

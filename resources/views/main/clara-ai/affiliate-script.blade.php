@extends('layouts.app')

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
</style>
@endpush

@section('content')
{{-- LAYOUT: Side-by-side (input left, output right) on desktop, stacked on mobile --}}
<div x-data="scriptGenApp()" class="bg-gray-50 min-h-[calc(100vh-64px-57px)]">

    {{-- Header --}}
    <div class="bg-white border-b border-gray-200 flex-shrink-0">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-cuan-green flex items-center justify-center flex-shrink-0 shadow-lg shadow-emerald-100">
                        <i class="fa-solid fa-scroll text-white text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <h1 class="font-black text-gray-900 text-sm uppercase tracking-tighter">Script Generator AI</h1>
                        <p class="text-[10px] font-bold text-cuan-green uppercase tracking-widest">TikTok · Instagram · YouTube</p>
                    </div>
                </div>
                <div class="hidden sm:flex items-center gap-2">
                    <div class="flex items-center gap-0.5 bg-gray-100 rounded-lg p-0.5">
                        <button @click="tone = 'casual'" class="tone-pill px-2.5 py-1 rounded-md text-[10px] font-bold uppercase" :class="{ 'active': tone === 'casual' }">Casual</button>
                        <button @click="tone = 'formal'" class="tone-pill px-2.5 py-1 rounded-md text-[10px] font-bold uppercase" :class="{ 'active': tone === 'formal' }">Formal</button>
                        <button @click="tone = 'aggressive'" class="tone-pill px-2.5 py-1 rounded-md text-[10px] font-bold uppercase" :class="{ 'active': tone === 'aggressive' }">Agresif</button>
                    </div>
                    <div class="flex items-center gap-0.5 bg-gray-100 rounded-lg p-0.5">
                        <button @click="language = 'id'" class="lang-toggle px-2.5 py-1 rounded-md text-[10px] font-bold uppercase" :class="{ 'active': language === 'id' }">ID</button>
                        <button @click="language = 'en'" class="lang-toggle px-2.5 py-1 rounded-md text-[10px] font-bold uppercase" :class="{ 'active': language === 'en' }">EN</button>
                    </div>
                </div>
            </div>
            {{-- Mobile controls --}}
            <div class="sm:hidden flex items-center gap-2 mt-3">
                <div class="flex items-center gap-0.5 bg-gray-100 rounded-lg p-0.5 flex-1">
                    <button @click="tone = 'casual'" class="tone-pill flex-1 px-2 py-1 rounded-md text-[9px] font-bold uppercase" :class="{ 'active': tone === 'casual' }">Casual</button>
                    <button @click="tone = 'formal'" class="tone-pill flex-1 px-2 py-1 rounded-md text-[9px] font-bold uppercase" :class="{ 'active': tone === 'formal' }">Formal</button>
                    <button @click="tone = 'aggressive'" class="tone-pill flex-1 px-2 py-1 rounded-md text-[9px] font-bold uppercase" :class="{ 'active': tone === 'aggressive' }">Agresif</button>
                </div>
                <div class="flex items-center gap-0.5 bg-gray-100 rounded-lg p-0.5">
                    <button @click="language = 'id'" class="lang-toggle px-2 py-1 rounded-md text-[9px] font-bold uppercase" :class="{ 'active': language === 'id' }">ID</button>
                    <button @click="language = 'en'" class="lang-toggle px-2 py-1 rounded-md text-[9px] font-bold uppercase" :class="{ 'active': language === 'en' }">EN</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-6 space-y-4">

        {{-- Input Panel --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-50 flex items-center justify-between">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Deskripsi Script</p>
                <span class="text-[10px] text-gray-300 font-medium" x-text="prompt.length + ' / 2000'"></span>
            </div>
            <textarea x-model="prompt" @keydown.ctrl.enter="submit()" @keydown.meta.enter="submit()"
                placeholder="Deskripsikan script yang ingin dibuat. Contoh: Script jualan takoyaki pedas yang viral di TikTok..."
                maxlength="2000" rows="4"
                class="w-full px-5 py-4 border-0 focus:ring-0 text-sm resize-none bg-white placeholder-gray-300"
                :disabled="loading"></textarea>
            <div class="px-5 py-3 bg-gray-50/50 border-t border-gray-50">
                <button @click="submit()" :disabled="loading || !prompt.trim()"
                    class="w-full px-5 py-3 bg-cuan-green hover:bg-cuan-dark disabled:opacity-40 disabled:cursor-not-allowed text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-emerald-100 active:scale-95 flex items-center justify-center gap-2">
                    <i class="fas" :class="loading ? 'fa-circle-notch fa-spin' : 'fa-bolt'"></i>
                    <span x-text="loading ? 'Generating...' : 'Generate Script'"></span>
                </button>
            </div>
        </div>

        {{-- Quick Prompts --}}
        <div x-show="!output && !loading && !error" class="grid grid-cols-1 sm:grid-cols-2 gap-3 fade-in">
            <template x-for="q in quickPrompts" :key="q">
                <button @click="prompt = q"
                    class="text-left px-5 py-4 bg-white border border-gray-100 hover:border-cuan-green/30 hover:shadow-emerald-100 rounded-2xl text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-cuan-green transition-all shadow-sm hover:shadow-xl group flex items-center justify-between">
                    <span x-text="q"></span>
                    <i class="fas fa-chevron-right text-gray-200 group-hover:text-cuan-green group-hover:translate-x-1 transition-all"></i>
                </button>
            </template>
        </div>

        {{-- Loading --}}
        <div x-show="loading" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center fade-in">
            <div class="flex justify-center gap-1.5 mb-3">
                <div class="w-2 h-2 bg-cuan-green rounded-full animate-bounce"></div>
                <div class="w-2 h-2 bg-cuan-green rounded-full animate-bounce" style="animation-delay:0.15s"></div>
                <div class="w-2 h-2 bg-cuan-green rounded-full animate-bounce" style="animation-delay:0.3s"></div>
            </div>
            <p class="text-sm font-bold text-gray-600">Clara AI sedang membuat script...</p>
        </div>

        {{-- Error --}}
        <div x-show="error && !loading" class="fade-in">
            <div class="bg-red-50 border border-red-200 rounded-2xl px-4 py-2.5">
                <p class="text-sm text-red-600" x-text="error"></p>
            </div>
        </div>

        {{-- Output --}}
        <div x-show="output && !loading" class="fade-in">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-lg bg-cuan-green flex items-center justify-center">
                            <img src="{{ asset('assets/image/clara-ai.png') }}" class="p-0.5" alt="">
                        </div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Hasil Script</p>
                    </div>
                    <button @click="copyOutput()"
                        class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-gray-400 hover:text-cuan-green border border-gray-100 hover:border-cuan-green/30 rounded-lg transition-all">
                        <i class="fas mr-1" :class="copied ? 'fa-check text-cuan-green' : 'fa-copy'"></i>
                        <span x-text="copied ? 'Tersalin' : 'Salin'"></span>
                    </button>
                </div>
                <div class="px-5 py-4">
                    <p class="output-area text-sm text-gray-800 leading-relaxed" x-text="output"></p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function scriptGenApp() {
    return {
        prompt: '', output: '', error: '', loading: false, copied: false,
        tone: 'casual', language: 'id',
        quickPrompts: [
            'Script jualan produk best seller untuk TikTok',
            'Script review produk dengan gaya storytelling',
            'Script promo diskon akhir bulan yang mendesak',
        ],
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
                if (data.success) { this.output = data.result; } else { this.error = data.message || 'Gagal menghasilkan konten.'; }
            } catch (e) { this.error = 'Koneksi gagal. Coba lagi.'; } finally { this.loading = false; }
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

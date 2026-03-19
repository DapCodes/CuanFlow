@extends('layouts.app')

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
</style>
@endpush

@section('content')
{{-- LAYOUT: Full-width top input + bottom output (chat-like vertical flow) --}}
<div x-data="videoPromptApp()" class="bg-gray-50 min-h-[calc(100vh-64px-57px)] flex flex-col">

    {{-- Sticky Header --}}
    <div class="bg-white border-b border-gray-200 flex-shrink-0">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-cuan-green flex items-center justify-center flex-shrink-0 shadow-lg shadow-emerald-100">
                    <i class="fa-solid fa-film text-white text-sm"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <h1 class="font-black text-gray-900 text-sm uppercase tracking-tighter">Video Prompt Generator</h1>
                    <p class="text-[10px] font-bold text-cuan-green uppercase tracking-widest">Runway · Sora · Pika · Kling</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Scrollable Content --}}
    <div class="flex-1 overflow-y-auto">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 py-6 space-y-4">

            {{-- Controls Row --}}
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-0.5 bg-gray-100 rounded-lg p-0.5">
                        <button @click="tone = 'casual'" class="tone-pill px-2.5 py-1 rounded-md text-[10px] font-bold uppercase" :class="{ 'active': tone === 'casual' }">Casual</button>
                        <button @click="tone = 'formal'" class="tone-pill px-2.5 py-1 rounded-md text-[10px] font-bold uppercase" :class="{ 'active': tone === 'formal' }">Formal</button>
                        <button @click="tone = 'aggressive'" class="tone-pill px-2.5 py-1 rounded-md text-[10px] font-bold uppercase" :class="{ 'active': tone === 'aggressive' }">Agresif</button>
                    </div>
                </div>
                <div class="flex items-center gap-0.5 bg-gray-100 rounded-lg p-0.5">
                    <button @click="language = 'id'" class="lang-toggle px-2.5 py-1 rounded-md text-[10px] font-bold uppercase" :class="{ 'active': language === 'id' }">ID</button>
                    <button @click="language = 'en'" class="lang-toggle px-2.5 py-1 rounded-md text-[10px] font-bold uppercase" :class="{ 'active': language === 'en' }">EN</button>
                </div>
            </div>

            {{-- Input Card --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <textarea x-model="prompt" @keydown.ctrl.enter="submit()" @keydown.meta.enter="submit()"
                    placeholder="Deskripsikan video yang ingin dibuat. Contoh: Video promosi takoyaki premium dengan suasana street food Jepang yang cinematic..."
                    maxlength="2000" rows="4"
                    class="w-full px-5 py-4 border-0 focus:ring-0 text-sm resize-none bg-white placeholder-gray-300"
                    :disabled="loading"></textarea>
                <div class="px-5 py-3 bg-gray-50/50 border-t border-gray-50 flex items-center justify-between">
                    <span class="text-[10px] text-gray-300 font-medium" x-text="prompt.length + ' / 2000'"></span>
                    <button @click="submit()" :disabled="loading || !prompt.trim()"
                        class="px-5 py-2.5 bg-cuan-green hover:bg-cuan-dark disabled:opacity-40 disabled:cursor-not-allowed text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-emerald-100 active:scale-95 flex items-center gap-2">
                        <i class="fas" :class="loading ? 'fa-circle-notch fa-spin' : 'fa-bolt'"></i>
                        <span x-text="loading ? 'Generating...' : 'Generate Prompt'"></span>
                    </button>
                </div>
            </div>

            {{-- Quick Prompts (visible when idle) --}}
            <div x-show="!output && !loading && !error" class="space-y-2 fade-in">
                <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest">Coba salah satu</p>
                <template x-for="q in quickPrompts" :key="q">
                    <button @click="prompt = q"
                        class="w-full text-left px-5 py-4 bg-white border border-gray-100 hover:border-cuan-green/30 hover:shadow-emerald-100 rounded-2xl text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-cuan-green transition-all shadow-sm hover:shadow-xl group flex items-center justify-between">
                        <span x-text="q"></span>
                        <i class="fas fa-chevron-right text-gray-200 group-hover:text-cuan-green group-hover:translate-x-1 transition-all"></i>
                    </button>
                </template>
            </div>

            {{-- Loading --}}
            <div x-show="loading" class="fade-in">
                <div class="flex gap-4 mb-6">
                    <div class="w-9 h-9 rounded-xl bg-cuan-green flex items-center justify-center flex-shrink-0 shadow-xl shadow-emerald-50">
                        <i class="fa-solid fa-film text-white text-xs"></i>
                    </div>
                    <div class="bg-white border border-gray-100 rounded-2xl rounded-tl-none px-5 py-3.5 shadow-sm">
                        <div class="flex gap-1.5">
                            <div class="w-2 h-2 bg-cuan-green rounded-full animate-bounce"></div>
                            <div class="w-2 h-2 bg-cuan-green rounded-full animate-bounce" style="animation-delay:0.15s"></div>
                            <div class="w-2 h-2 bg-cuan-green rounded-full animate-bounce" style="animation-delay:0.3s"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Error --}}
            <div x-show="error && !loading" class="fade-in">
                <div class="bg-red-50 border border-red-200 rounded-2xl px-4 py-2.5">
                    <p class="text-sm text-red-600" x-text="error"></p>
                </div>
            </div>

            {{-- Output (chat-style bubble like the Clara AI chat) --}}
            <div x-show="output && !loading" class="fade-in">
                <div class="flex gap-4 mb-6">
                    <div class="w-9 h-9 rounded-xl bg-cuan-green flex items-center justify-center flex-shrink-0 shadow-xl shadow-emerald-50">
                        <img src="{{ asset('assets/image/clara-ai.png') }}" class="p-1" alt="">
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="bg-white border border-gray-100 text-gray-800 rounded-2xl rounded-tl-none px-5 py-3.5 shadow-sm break-words overflow-hidden">
                            <p class="output-area text-sm leading-relaxed" x-text="output"></p>
                        </div>
                        <div class="flex items-center gap-2 mt-2">
                            <button @click="copyOutput()"
                                class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-gray-400 hover:text-cuan-green transition-colors">
                                <i class="fas" :class="copied ? 'fa-check text-cuan-green' : 'fa-copy'"></i>
                                <span x-text="copied ? 'Tersalin' : 'Salin'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
function videoPromptApp() {
    return {
        prompt: '', output: '', error: '', loading: false, copied: false,
        tone: 'casual', language: 'id',
        quickPrompts: [
            'Buat video promosi produk best seller kami',
            'Video cinematic behind the scene proses pembuatan produk',
            'Konten unboxing produk baru untuk social media',
        ],
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

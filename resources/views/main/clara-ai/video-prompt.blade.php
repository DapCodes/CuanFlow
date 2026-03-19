@extends('layouts.app')

@section('title', 'Video Prompt Generator')

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-bold tracking-tight">Video Prompt Generator</span>
</li>
@endsection

@push('styles')
<style>
    .output-area { white-space: pre-wrap; word-wrap: break-word; }
    .generate-btn { background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); transition: all 0.3s ease; }
    .generate-btn:hover:not(:disabled) { box-shadow: 0 8px 24px -6px rgba(124, 58, 237, 0.4); transform: translateY(-1px); }
    .generate-btn:disabled { opacity: 0.5; cursor: not-allowed; }
    .tone-pill.active { background: #1f2937; color: white; }
    .lang-toggle.active { background: #7c3aed; color: white; }
    .fade-in { animation: fadeIn 0.3s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
    .output-scroll::-webkit-scrollbar { width: 4px; }
    .output-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
</style>
@endpush

@section('content')
<div x-data="generatorApp()" class="min-h-[calc(100vh-64px-57px)] bg-gray-50">

    {{-- Header --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-base font-black text-gray-900 tracking-tight">Video Prompt Generator</h1>
                    <p class="text-xs text-gray-400 font-medium mt-0.5">Buat prompt sinematik untuk Runway, Sora, Pika, Kling</p>
                </div>
                <div class="hidden sm:flex items-center gap-2">
                    <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-0.5">
                        <button @click="tone = 'casual'" class="tone-pill px-2.5 py-1 rounded-md text-[10px] font-bold uppercase" :class="{ 'active': tone === 'casual' }">Casual</button>
                        <button @click="tone = 'formal'" class="tone-pill px-2.5 py-1 rounded-md text-[10px] font-bold uppercase" :class="{ 'active': tone === 'formal' }">Formal</button>
                        <button @click="tone = 'aggressive'" class="tone-pill px-2.5 py-1 rounded-md text-[10px] font-bold uppercase" :class="{ 'active': tone === 'aggressive' }">Agresif</button>
                    </div>
                    <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-0.5">
                        <button @click="language = 'id'" class="lang-toggle px-2.5 py-1 rounded-md text-[10px] font-bold uppercase" :class="{ 'active': language === 'id' }">ID</button>
                        <button @click="language = 'en'" class="lang-toggle px-2.5 py-1 rounded-md text-[10px] font-bold uppercase" :class="{ 'active': language === 'en' }">EN</button>
                    </div>
                </div>
            </div>
            {{-- Mobile controls --}}
            <div class="sm:hidden flex items-center gap-2 mt-3">
                <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-0.5 flex-1">
                    <button @click="tone = 'casual'" class="tone-pill flex-1 px-2 py-1 rounded-md text-[9px] font-bold uppercase" :class="{ 'active': tone === 'casual' }">Casual</button>
                    <button @click="tone = 'formal'" class="tone-pill flex-1 px-2 py-1 rounded-md text-[9px] font-bold uppercase" :class="{ 'active': tone === 'formal' }">Formal</button>
                    <button @click="tone = 'aggressive'" class="tone-pill flex-1 px-2 py-1 rounded-md text-[9px] font-bold uppercase" :class="{ 'active': tone === 'aggressive' }">Agresif</button>
                </div>
                <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-0.5">
                    <button @click="language = 'id'" class="lang-toggle px-2 py-1 rounded-md text-[9px] font-bold uppercase" :class="{ 'active': language === 'id' }">ID</button>
                    <button @click="language = 'en'" class="lang-toggle px-2 py-1 rounded-md text-[9px] font-bold uppercase" :class="{ 'active': language === 'en' }">EN</button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-6 space-y-4">

        {{-- Input --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-5">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Deskripsikan video yang ingin dibuat</label>
            <textarea x-model="prompt" @keydown.ctrl.enter="submit()" @keydown.meta.enter="submit()"
                placeholder="Contoh: Buat video promosi takoyaki premium dengan suasana street food Jepang..."
                maxlength="2000" rows="3"
                class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 focus:bg-white text-sm resize-none transition-all"
                :disabled="loading"></textarea>
            <div class="flex items-center justify-between mt-3">
                <span class="text-[10px] text-gray-300 font-medium" x-text="prompt.length + '/2000'"></span>
                <button @click="submit()" :disabled="loading || !prompt.trim()"
                    class="generate-btn px-5 py-2.5 text-white rounded-xl text-xs font-bold uppercase tracking-wider flex items-center gap-2 active:scale-95">
                    <i class="fas" :class="loading ? 'fa-spinner fa-spin' : 'fa-bolt'"></i>
                    <span x-text="loading ? 'Generating...' : 'Generate'"></span>
                </button>
            </div>
        </div>

        {{-- Output --}}
        <div x-show="output || loading || error" class="fade-in">
            {{-- Loading --}}
            <div x-show="loading" class="bg-white rounded-2xl border border-gray-200 p-8 text-center">
                <div class="flex justify-center gap-1.5 mb-3">
                    <div class="w-2 h-2 bg-purple-500 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-purple-500 rounded-full animate-bounce" style="animation-delay:0.15s"></div>
                    <div class="w-2 h-2 bg-purple-500 rounded-full animate-bounce" style="animation-delay:0.3s"></div>
                </div>
                <p class="text-sm font-bold text-gray-600">Clara AI sedang membuat prompt video...</p>
                <p class="text-xs text-gray-400 mt-1">Sekitar 15-30 detik</p>
            </div>

            {{-- Error --}}
            <div x-show="error && !loading" class="bg-red-50 border border-red-200 rounded-2xl p-4">
                <p class="text-sm text-red-700 font-medium" x-text="error"></p>
            </div>

            {{-- Result --}}
            <div x-show="output && !loading">
                <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Hasil Generate</p>
                        <button @click="copyOutput()" class="px-3 py-1.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg text-[10px] font-bold uppercase text-gray-600 transition-colors active:scale-95">
                            <i class="fas" :class="copied ? 'fa-check text-green-600' : 'fa-copy'"></i>
                            <span x-text="copied ? 'Tersalin' : 'Salin'"></span>
                        </button>
                    </div>
                    <div class="output-area text-sm text-gray-800 leading-relaxed border-l-2 border-purple-300 pl-4" x-text="output"></div>
                </div>
            </div>
        </div>

        {{-- Quick Prompts (shown when no output) --}}
        <div x-show="!output && !loading && !error" class="space-y-2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Coba salah satu</p>
            <button @click="prompt = 'Buat video promosi produk best seller kami'; submit()"
                class="w-full text-left px-4 py-3 bg-white border border-gray-200 hover:border-purple-300 rounded-xl text-sm text-gray-600 hover:text-purple-700 transition-all">
                Buat video promosi produk best seller kami
            </button>
            <button @click="prompt = 'Video cinematic behind the scene proses pembuatan produk'; submit()"
                class="w-full text-left px-4 py-3 bg-white border border-gray-200 hover:border-purple-300 rounded-xl text-sm text-gray-600 hover:text-purple-700 transition-all">
                Video cinematic behind the scene proses pembuatan produk
            </button>
            <button @click="prompt = 'Konten unboxing produk baru untuk TikTok dan Instagram'; submit()"
                class="w-full text-left px-4 py-3 bg-white border border-gray-200 hover:border-purple-300 rounded-xl text-sm text-gray-600 hover:text-purple-700 transition-all">
                Konten unboxing produk baru untuk TikTok dan Instagram
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function generatorApp() {
    return {
        prompt: '', output: '', error: '', loading: false, copied: false,
        tone: 'casual', language: 'id',
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

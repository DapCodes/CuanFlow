@extends('layouts.app')

@section('title', 'Kalkulaba AI — Kalkulator Bisnis')

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('clara-ai.index') }}" class="text-gray-400 hover:text-gray-900 transition-colors">Clara AI</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-bold tracking-tight">Kalkulaba AI</span>
</li>
@endsection

@push('styles')
<style>
    .output-area { white-space: pre-wrap; word-wrap: break-word; word-break: break-word; }
    .mode-pill.active { background: #1f2937; color: white; }
    .lang-toggle.active { background-color: var(--cuan-green, #658C58); color: white; }
    .biz-pill.active { background-color: var(--cuan-green, #658C58); color: white; }
    .fade-in { animation: fadeIn 0.3s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
    .history-scroll::-webkit-scrollbar { width: 4px; }
    .history-scroll::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 4px; }

    .pricing-card { transition: all 0.2s ease; }
    .pricing-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px -5px rgba(0,0,0,0.1); }
    .pricing-low { border-top: 3px solid #3b82f6; }
    .pricing-competitive { border-top: 3px solid #658C58; }
    .pricing-exclusive { border-top: 3px solid #f59e0b; }

    .cost-input-group input { font-variant-numeric: tabular-nums; }
    .cost-toggle { cursor: pointer; user-select: none; }

    .insight-item { border-left: 3px solid var(--cuan-green, #658C58); }

    .ingredient-row:nth-child(even) { background: #f9fafb; }

    @keyframes pulseGlow {
        0%, 100% { box-shadow: 0 0 0 0 rgba(101, 140, 88, 0.3); }
        50% { box-shadow: 0 0 0 8px rgba(101, 140, 88, 0); }
    }
    .pulse-glow { animation: pulseGlow 2s ease-in-out infinite; }
</style>
@endpush

@section('content')
<main x-data="kalkulabaApp()" class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-cuan-green flex items-center justify-center flex-shrink-0 shadow-lg shadow-emerald-100">
                    <i class="fa-solid fa-calculator text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl md:text-2xl font-black text-gray-900 uppercase tracking-tighter">
                        Kalkulaba AI
                    </h1>
                    <p class="mt-1 text-sm text-cuan-green font-bold tracking-wider">
                        KALKULATOR BISNIS · COGS · PRICING · PROFIT
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                {{-- Advanced Mode Toggle --}}
                <div class="flex items-center gap-0.5 bg-white border border-gray-200 rounded-lg p-0.5 shadow-sm">
                    <button @click="advancedMode = ''" class="mode-pill px-3 py-1.5 rounded-md text-xs font-bold uppercase" :class="{ 'active': advancedMode === '' }">Standard</button>
                    <button @click="advancedMode = 'exclusive'" class="mode-pill px-3 py-1.5 rounded-md text-xs font-bold uppercase" :class="{ 'active': advancedMode === 'exclusive' }">Exclusive</button>
                    <button @click="advancedMode = 'efficiency'" class="mode-pill px-3 py-1.5 rounded-md text-xs font-bold uppercase" :class="{ 'active': advancedMode === 'efficiency' }">Efisiensi</button>
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
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">

                    {{-- Product Info --}}
                    <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                        <p class="text-[10px] font-black text-gray-600 uppercase tracking-widest">Informasi Produk</p>
                    </div>
                    <div class="p-4 space-y-3 border-b border-gray-100">
                        <input x-model="productName" type="text" placeholder="Nama Produk (contoh: Es Kopi Susu)"
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-1 focus:ring-cuan-green focus:border-cuan-green"
                            :disabled="loading">

                        <textarea x-model="productDescription" rows="2" placeholder="Deskripsi singkat produk..."
                            maxlength="500"
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm resize-none focus:ring-1 focus:ring-cuan-green focus:border-cuan-green"
                            :disabled="loading"></textarea>

                        <input x-model="imageUrl" type="url" placeholder="URL Gambar Produk (opsional)"
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-1 focus:ring-cuan-green focus:border-cuan-green"
                            :disabled="loading">

                        {{-- Business Type --}}
                        <div>
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Tipe Bisnis</p>
                            <div class="flex flex-wrap gap-1.5">
                                <template x-for="t in ['food','beverage','product','other']" :key="t">
                                    <button @click="businessType = t" class="biz-pill px-3 py-1.5 rounded-lg text-xs font-bold uppercase border border-gray-200 transition-all"
                                        :class="{ 'active': businessType === t }" x-text="t === 'food' ? '🍳 Makanan' : t === 'beverage' ? '🥤 Minuman' : t === 'product' ? '📦 Produk' : '🔧 Lainnya'">
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Cost Inputs (Collapsible) --}}
                    <div class="border-b border-gray-100">
                        <button @click="showCosts = !showCosts" class="cost-toggle w-full px-5 py-3 bg-gray-50 flex items-center justify-between">
                            <p class="text-[10px] font-black text-gray-600 uppercase tracking-widest">
                                <i class="fa-solid fa-coins mr-1"></i> Biaya Tambahan (Opsional)
                            </p>
                            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform" :class="{ 'rotate-180': showCosts }"></i>
                        </button>
                        <div x-show="showCosts" x-collapse class="p-4 space-y-2">
                            <template x-for="field in costFields" :key="field.key">
                                <div class="cost-input-group flex items-center gap-2">
                                    <label class="text-xs text-gray-500 w-24 flex-shrink-0" x-text="field.label"></label>
                                    <div class="flex-1 relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">Rp</span>
                                        <input type="number" min="0" x-model.number="costInput[field.key]" placeholder="0"
                                            class="w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg text-sm text-right focus:ring-1 focus:ring-cuan-green focus:border-cuan-green"
                                            :disabled="loading">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Target Profit --}}
                    <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                        <p class="text-[10px] font-black text-gray-600 uppercase tracking-widest">
                            <i class="fa-solid fa-bullseye mr-1"></i> Target Keuntungan
                        </p>
                    </div>
                    <div class="p-4">
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-400">Rp</span>
                            <input type="number" min="0" x-model.number="targetProfit" placeholder="1000000"
                                class="w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl text-sm font-bold text-right focus:ring-1 focus:ring-cuan-green focus:border-cuan-green"
                                :disabled="loading">
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1.5">Target profit yang ingin dicapai (per periode)</p>
                    </div>

                    {{-- Submit Button --}}
                    <div class="p-4 border-t border-gray-100 bg-white">
                        <button @click="submit()" :disabled="loading || !productName.trim()"
                            class="w-full py-3.5 bg-cuan-green hover:bg-cuan-dark disabled:opacity-40 disabled:cursor-not-allowed text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-emerald-100 active:scale-95 flex items-center justify-center gap-2"
                            :class="{ 'pulse-glow': !loading && productName.trim() }">
                            <i class="fas" :class="loading ? 'fa-circle-notch fa-spin' : 'fa-calculator'"></i>
                            <span x-text="loading ? 'Menghitung...' : 'Analisis Sekarang'"></span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- 2. Output Panel (Col 5) --}}
            <div class="lg:col-span-5 relative min-h-[400px]">

                {{-- Empty State --}}
                <div x-show="!parsedResult && !loading && !error" class="absolute inset-0 bg-white rounded-2xl border border-gray-200 shadow-sm p-8 flex flex-col items-center justify-center text-center fade-in">
                    <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-chart-pie text-2xl text-gray-300"></i>
                    </div>
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-tight mb-2">Belum Ada Analisis</h3>
                    <p class="text-xs text-gray-400 max-w-xs">Isi informasi produk di sebelah kiri. Kalkulaba AI akan menghitung HPP, strategi harga, dan prediksi keuntungan.</p>
                </div>

                {{-- Loading State --}}
                <div x-show="loading" class="absolute inset-0 bg-white rounded-2xl border border-gray-200 shadow-sm p-8 flex flex-col items-center justify-center text-center fade-in z-10">
                    <i class="fa-solid fa-calculator text-3xl text-cuan-green animate-bounce mb-4"></i>
                    <p class="text-sm font-bold text-gray-800">Menghitung biaya & strategi harga...</p>
                    <p class="text-xs text-gray-400 mt-1">AI sedang menganalisis produk kamu</p>
                </div>

                {{-- Error State --}}
                <div x-show="error && !loading" class="bg-red-50 border border-red-200 rounded-2xl p-6 fade-in mb-4">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-triangle-exclamation text-red-500 text-xl"></i>
                        <p class="text-sm font-bold text-red-700" x-text="error"></p>
                    </div>
                </div>

                {{-- Result Cards --}}
                <div x-show="parsedResult && !loading" class="space-y-4 fade-in">

                    {{-- Top bar --}}
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-5 py-3 bg-emerald-50/30 flex items-center justify-between border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-check-circle text-cuan-green"></i>
                                <p class="text-[10px] font-black text-gray-800 uppercase tracking-widest">Hasil Analisis Kalkulaba</p>
                            </div>
                            <button @click="copyRawJson()"
                                class="px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-[10px] font-bold uppercase tracking-wider text-gray-600 hover:text-cuan-green hover:border-cuan-green transition-all shadow-sm">
                                <i class="fas mr-1" :class="copied ? 'fa-check text-cuan-green' : 'fa-copy'"></i>
                                <span x-text="copied ? 'Tersalin!' : 'Salin JSON'"></span>
                            </button>
                        </div>

                        {{-- COGS Summary --}}
                        <div class="p-5">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                                    <i class="fa-solid fa-coins text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">HPP Per Unit</p>
                                    <p class="text-xl font-black text-gray-900" x-text="'Rp ' + formatNumber(parsedResult?.cost_analysis?.cogs_per_unit || 0)"></p>
                                </div>
                            </div>

                            {{-- Breakdown Table --}}
                            <div class="bg-gray-50 rounded-xl p-3" x-show="parsedResult?.cost_analysis?.breakdown">
                                <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Rincian Biaya</p>
                                <template x-for="(val, key) in (parsedResult?.cost_analysis?.breakdown || {})" :key="key">
                                    <div class="flex justify-between items-center py-1.5 border-b border-gray-100 last:border-0">
                                        <span class="text-xs text-gray-600 capitalize" x-text="costLabel(key)"></span>
                                        <span class="text-xs font-bold text-gray-800" x-text="'Rp ' + formatNumber(val)"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Pricing Cards --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <template x-for="tier in ['low', 'competitive', 'exclusive']" :key="tier">
                            <div class="pricing-card bg-white rounded-2xl border border-gray-200 shadow-sm p-4"
                                :class="'pricing-' + tier">
                                <div class="flex items-center gap-2 mb-3">
                                    <i class="text-sm" :class="tier === 'low' ? 'fa-solid fa-tag text-blue-500' : tier === 'competitive' ? 'fa-solid fa-balance-scale text-cuan-green' : 'fa-solid fa-crown text-amber-500'"></i>
                                    <p class="text-[10px] font-black uppercase tracking-widest"
                                        :class="tier === 'low' ? 'text-blue-600' : tier === 'competitive' ? 'text-cuan-green' : 'text-amber-600'"
                                        x-text="tier === 'low' ? 'Harga Hemat' : tier === 'competitive' ? 'Harga Pasar' : 'Harga Premium'"></p>
                                </div>
                                <p class="text-lg font-black text-gray-900 mb-2" x-text="'Rp ' + formatNumber(parsedResult?.pricing?.[tier]?.price || 0)"></p>
                                <div class="space-y-1.5 text-xs">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Profit/unit</span>
                                        <span class="font-bold text-cuan-green" x-text="'Rp ' + formatNumber(parsedResult?.pricing?.[tier]?.profit_per_unit || 0)"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Margin</span>
                                        <span class="font-bold" x-text="(parsedResult?.pricing?.[tier]?.margin || 0) + '%'"></span>
                                    </div>
                                    <div class="flex justify-between bg-gray-50 -mx-1 px-1 py-1 rounded">
                                        <span class="text-gray-500">Target unit</span>
                                        <span class="font-black text-gray-900" x-text="formatNumber(parsedResult?.pricing?.[tier]?.units_to_target || 0) + ' pcs'"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Recipe Section --}}
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden" x-show="parsedResult?.recipe?.ingredients?.length > 0">
                        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50 flex items-center gap-2">
                            <i class="fa-solid fa-utensils text-cuan-green text-sm"></i>
                            <p class="text-[10px] font-black text-gray-600 uppercase tracking-widest">Resep & Bahan</p>
                        </div>
                        <div class="p-4">
                            {{-- Ingredients --}}
                            <div class="mb-4">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Bahan-bahan</p>
                                <template x-for="(ing, idx) in (parsedResult?.recipe?.ingredients || [])" :key="idx">
                                    <div class="ingredient-row flex justify-between items-center py-2 px-2 rounded text-xs">
                                        <div class="flex items-center gap-2">
                                            <span class="w-5 h-5 rounded-full bg-cuan-green/10 text-cuan-green text-[10px] font-bold flex items-center justify-center" x-text="idx + 1"></span>
                                            <span class="text-gray-700 font-medium" x-text="ing.name"></span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="text-gray-500" x-text="ing.quantity"></span>
                                            <span class="font-bold text-gray-800 min-w-[70px] text-right" x-text="'Rp ' + formatNumber(ing.estimated_cost || 0)"></span>
                                        </div>
                                    </div>
                                </template>
                                <div class="flex justify-between items-center pt-2 mt-2 border-t border-gray-200 px-2">
                                    <span class="text-xs font-bold text-gray-600">Total Bahan</span>
                                    <span class="text-sm font-black text-cuan-green" x-text="'Rp ' + formatNumber(parsedResult?.recipe?.estimated_cost || 0)"></span>
                                </div>
                            </div>

                            {{-- Steps --}}
                            <div x-show="parsedResult?.recipe?.steps?.length > 0">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Langkah Pembuatan</p>
                                <template x-for="(step, idx) in (parsedResult?.recipe?.steps || [])" :key="idx">
                                    <div class="flex gap-2 mb-2 text-xs text-gray-600">
                                        <span class="flex-shrink-0 w-5 h-5 rounded-full bg-gray-100 text-gray-500 text-[10px] font-bold flex items-center justify-center" x-text="idx + 1"></span>
                                        <p x-text="step" class="leading-relaxed"></p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Insights --}}
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden" x-show="parsedResult?.insights?.length > 0">
                        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50 flex items-center gap-2">
                            <i class="fa-solid fa-lightbulb text-amber-500 text-sm"></i>
                            <p class="text-[10px] font-black text-gray-600 uppercase tracking-widest">Smart Insights</p>
                        </div>
                        <div class="p-4 space-y-2">
                            <template x-for="(insight, idx) in (parsedResult?.insights || [])" :key="idx">
                                <div class="insight-item pl-3 py-2 text-xs text-gray-700 leading-relaxed" x-text="insight"></div>
                            </template>
                        </div>
                    </div>

                    {{-- Raw Output (Collapsible) --}}
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <button @click="showRaw = !showRaw" class="w-full px-5 py-3 bg-gray-50 flex items-center justify-between">
                            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Raw AI Response</p>
                            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform" :class="{ 'rotate-180': showRaw }"></i>
                        </button>
                        <div x-show="showRaw" x-collapse class="p-4">
                            <pre class="output-area text-xs text-gray-600 bg-gray-50 rounded-xl p-3 overflow-x-auto max-h-60" x-text="rawOutput"></pre>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. History Panel (Col 3) --}}
            <div class="lg:col-span-3">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col h-full max-h-[calc(100vh-12rem)] min-h-[400px]">
                    <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                        <div class="flex items-center gap-2 text-gray-500">
                            <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                            <h2 class="text-[10px] font-black uppercase tracking-widest">Riwayat Analisis</h2>
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
                                <p class="text-xs text-gray-700 font-bold line-clamp-1" x-text="item.productName"></p>
                                <p class="text-[10px] text-gray-500 mt-0.5" x-text="'HPP: Rp ' + formatNumber(item.cogs || 0)"></p>
                                <div class="flex items-center gap-1 mt-2">
                                    <span class="px-1.5 py-0.5 rounded bg-gray-100 text-[8px] font-black uppercase text-gray-500" x-text="item.businessType"></span>
                                    <span class="px-1.5 py-0.5 rounded bg-blue-50 text-[8px] font-black uppercase text-blue-600" x-text="item.advancedMode || 'standard'"></span>
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
function kalkulabaApp() {
    return {
        // Input state
        productName: '',
        productDescription: '',
        imageUrl: '',
        businessType: 'food',
        costInput: { gas: 0, labor: 0, packaging: 0, rent: 0, utilities: 0, other: 0 },
        targetProfit: 1000000,
        advancedMode: '',
        language: 'id',
        showCosts: false,

        // Output state
        rawOutput: '',
        parsedResult: null,
        error: '',
        loading: false,
        copied: false,
        showRaw: false,

        // History
        history: [],
        storageKey: 'kalkulaba_analysis_history',

        costFields: [
            { key: 'gas', label: 'Gas / Listrik' },
            { key: 'labor', label: 'Tenaga Kerja' },
            { key: 'packaging', label: 'Packaging' },
            { key: 'rent', label: 'Sewa Tempat' },
            { key: 'utilities', label: 'Utilitas' },
            { key: 'other', label: 'Lain-lain' },
        ],

        init() {
            const saved = localStorage.getItem(this.storageKey);
            if (saved) {
                try { this.history = JSON.parse(saved); } catch(e) {}
            }
        },

        async submit() {
            if (this.loading || !this.productName.trim()) return;
            this.loading = true;
            this.rawOutput = '';
            this.parsedResult = null;
            this.error = '';
            this.copied = false;

            // Build the prompt from product info
            const prompt = `Analisis produk: ${this.productName}. ${this.productDescription}. Tipe bisnis: ${this.businessType}. Target profit: Rp ${this.targetProfit}.`;

            try {
                const res = await fetch('{{ route("clara-ai.generate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        mode: 'kalkulaba',
                        prompt: prompt,
                        language: this.language,
                        product_name: this.productName,
                        product_description: this.productDescription,
                        image_url: this.imageUrl,
                        business_type: this.businessType,
                        cost_input: this.costInput,
                        target_profit: this.targetProfit,
                        advanced_mode: this.advancedMode,
                    })
                });

                const data = await res.json();

                if (data.success) {
                    this.rawOutput = data.result;
                    this.parseResult(data.result);
                    if (this.parsedResult) {
                        this.saveToHistory();
                    }
                } else {
                    this.error = data.message || 'Gagal menganalisis produk.';
                }
            } catch (e) {
                this.error = 'Koneksi gagal. Coba lagi.';
            } finally {
                this.loading = false;
            }
        },

        parseResult(raw) {
            try {
                // Try to extract JSON from the response
                let jsonStr = raw.trim();

                // Remove markdown code blocks if present
                jsonStr = jsonStr.replace(/```json\s*/gi, '').replace(/```\s*/g, '');

                // Try to find JSON object
                const firstBrace = jsonStr.indexOf('{');
                const lastBrace = jsonStr.lastIndexOf('}');
                if (firstBrace !== -1 && lastBrace !== -1) {
                    jsonStr = jsonStr.substring(firstBrace, lastBrace + 1);
                }

                this.parsedResult = JSON.parse(jsonStr);
            } catch (e) {
                console.warn('Failed to parse JSON, showing raw output', e);
                // Fallback: show raw output
                this.parsedResult = null;
                this.error = 'AI response bukan format JSON yang valid. Lihat raw output di bawah.';
                this.showRaw = true;
            }
        },

        saveToHistory() {
            const newItem = {
                id: Date.now(),
                timestamp: new Date().toISOString(),
                productName: this.productName,
                businessType: this.businessType,
                advancedMode: this.advancedMode,
                cogs: this.parsedResult?.cost_analysis?.cogs_per_unit || 0,
                rawOutput: this.rawOutput,
                parsedResult: this.parsedResult,
                // Save inputs for restoration
                inputs: {
                    productName: this.productName,
                    productDescription: this.productDescription,
                    imageUrl: this.imageUrl,
                    businessType: this.businessType,
                    costInput: {...this.costInput},
                    targetProfit: this.targetProfit,
                    advancedMode: this.advancedMode,
                    language: this.language,
                }
            };
            this.history.unshift(newItem);
            if (this.history.length > 15) this.history.pop();
            localStorage.setItem(this.storageKey, JSON.stringify(this.history));
        },

        loadHistory(item) {
            this.rawOutput = item.rawOutput;
            this.parsedResult = item.parsedResult;
            this.error = '';

            // Restore inputs
            if (item.inputs) {
                this.productName = item.inputs.productName;
                this.productDescription = item.inputs.productDescription;
                this.imageUrl = item.inputs.imageUrl;
                this.businessType = item.inputs.businessType;
                this.costInput = item.inputs.costInput || { gas: 0, labor: 0, packaging: 0, rent: 0, utilities: 0, other: 0 };
                this.targetProfit = item.inputs.targetProfit;
                this.advancedMode = item.inputs.advancedMode;
                this.language = item.inputs.language;
            }
        },

        clearHistory() {
            if (confirm('Hapus semua history analisis?')) {
                this.history = [];
                localStorage.removeItem(this.storageKey);
            }
        },

        formatNumber(num) {
            if (!num && num !== 0) return '0';
            return Number(num).toLocaleString('id-ID');
        },

        formatTime(iso) {
            const d = new Date(iso);
            return d.getDate() + '/' + (d.getMonth() + 1) + ' ' + String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
        },

        costLabel(key) {
            const map = {
                ingredients: 'Bahan Baku',
                gas: 'Gas / Listrik',
                labor: 'Tenaga Kerja',
                packaging: 'Packaging',
                rent: 'Sewa Tempat',
                utilities: 'Utilitas',
                other: 'Lain-lain',
            };
            return map[key] || key;
        },

        async copyRawJson() {
            if (!this.rawOutput) return;
            try {
                await navigator.clipboard.writeText(this.rawOutput);
            } catch {
                const t = document.createElement('textarea');
                t.value = this.rawOutput;
                t.style.cssText = 'position:fixed;opacity:0';
                document.body.appendChild(t);
                t.select();
                document.execCommand('copy');
                document.body.removeChild(t);
            }
            this.copied = true;
            setTimeout(() => this.copied = false, 2000);
        }
    };
}
</script>
@endpush
@endsection

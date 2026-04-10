@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

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

    .insight-item { border-left: 3px solid var(--cuan-green, #658C58); }
    .ingredient-row:nth-child(even) { background: #f9fafb; }

    .dropzone { border: 2px dashed #d1d5db; transition: all 0.2s ease; }
    .dropzone.dragover { border-color: var(--cuan-green, #658C58); background: rgba(101, 140, 88, 0.05); }
    .dropzone:hover { border-color: #9ca3af; }

    @keyframes pulseGlow {
        0%, 100% { box-shadow: 0 0 0 0 rgba(101, 140, 88, 0.3); }
        50% { box-shadow: 0 0 0 8px rgba(101, 140, 88, 0); }
    }
    .pulse-glow { animation: pulseGlow 2s ease-in-out infinite; }

    .editable-input { background: transparent; border: 1px solid transparent; border-radius: 6px; padding: 2px 4px; transition: all 0.15s; }
    .editable-input:hover { border-color: #e5e7eb; }
    .editable-input:focus { border-color: var(--cuan-green, #658C58); background: white; outline: none; box-shadow: 0 0 0 2px rgba(101, 140, 88, 0.15); }
</style>
@endpush

@section('content')
<main x-data="kalkulabaApp()" class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Kalkulaba AI
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kalkulasi HPP, strategi harga, dan target profit UMKM secara otomatis.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
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

            {{-- ==================== 1. INPUT PANEL (Col 4) ==================== --}}
            <div class="lg:col-span-4 space-y-4">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">

                    {{-- Image Upload --}}
                    <div class="px-5 py-3 border-b border-gray-100 bg-gray-50" hidden>
                        <p class="text-[10px] font-black text-gray-600 uppercase tracking-widest">Foto Produk</p>
                    </div>
                    <div class="p-4 border-b border-gray-100" hidden>
                        <template x-if="!imagePreview">
                            <div class="dropzone rounded-xl p-6 text-center cursor-pointer"
                                :class="{ 'dragover': isDragging }"
                                @click="$refs.imageInput.click()"
                                @dragover.prevent="isDragging = true"
                                @dragleave.prevent="isDragging = false"
                                @drop.prevent="handleDrop($event)">
                                <i class="fa-solid fa-cloud-arrow-up text-2xl text-gray-300 mb-2"></i>
                                <p class="text-xs text-gray-500 font-medium">Klik atau drag & drop gambar produk</p>
                                <p class="text-[10px] text-gray-400 mt-1">JPG, PNG, WebP · Max 5MB</p>
                                <div x-show="uploading" class="mt-2">
                                    <i class="fa-solid fa-circle-notch fa-spin text-cuan-green"></i>
                                    <span class="text-xs text-cuan-green font-bold ml-1">Mengunggah...</span>
                                </div>
                            </div>
                        </template>
                        <template x-if="imagePreview">
                            <div class="relative rounded-xl overflow-hidden">
                                <img :src="imagePreview" class="w-full h-40 object-cover rounded-xl">
                                <button @click="removeImage()" class="absolute top-2 right-2 w-7 h-7 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs shadow-lg transition-colors">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                                <div class="absolute bottom-2 left-2 px-2 py-1 bg-black/50 backdrop-blur rounded-md text-[10px] text-white font-bold">
                                    <i class="fa-solid fa-check-circle text-green-400 mr-1"></i> Gambar siap dianalisis AI
                                </div>
                            </div>
                        </template>
                        <input type="file" x-ref="imageInput" @change="handleFileSelect($event)" accept="image/jpeg,image/png,image/webp" class="hidden">
                    </div>

                    {{-- Product Info --}}
                    <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                        <p class="text-[10px] font-black text-gray-600 uppercase tracking-widest">Informasi Produk</p>
                    </div>
                    <div class="p-4 space-y-3 border-b border-gray-100">
                        <input x-model="productName" type="text" placeholder="Nama Produk (contoh: Es Kopi Susu)"
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-1 focus:ring-cuan-green focus:border-cuan-green"
                            :disabled="loading">
                        <textarea x-model="productDescription" rows="2" placeholder="Deskripsi singkat produk..."
                            required
                            maxlength="500"
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm resize-none focus:ring-1 focus:ring-cuan-green focus:border-cuan-green"
                            :disabled="loading"></textarea>
                        <div>
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Tipe Bisnis</p>
                            <div class="flex flex-wrap gap-1.5">
                                <template x-for="t in ['food','beverage','product','other']" :key="t">
                                    <button @click="businessType = t" class="biz-pill px-3 py-1.5 rounded-lg text-xs font-bold uppercase border border-gray-200 transition-all"
                                        :class="{ 'active': businessType === t }" x-text="t === 'food' ? 'Makanan' : t === 'beverage' ? 'Minuman' : t === 'product' ? 'Produk' : 'Lainnya'">
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Dynamic Cost Inputs --}}
                    <div class="border-b border-gray-100" hidden>
                        <button @click="showCosts = !showCosts" class="w-full px-5 py-3 bg-gray-50 flex items-center justify-between cursor-pointer select-none">
                            <p class="text-[10px] font-black text-gray-600 uppercase tracking-widest">
                                Pengeluaran Tambahan
                                <span class="text-gray-400 font-normal normal-case" x-text="'(' + additionalCosts.length + ')'"></span>
                            </p>
                            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform" :class="{ 'rotate-180': showCosts }"></i>
                        </button>
                        <div x-show="showCosts" x-collapse class="p-4 space-y-2">
                            <template x-for="(cost, idx) in additionalCosts" :key="idx">
                                <div class="flex items-center gap-2 fade-in">
                                    <input type="text" x-model="additionalCosts[idx].label" placeholder="Nama biaya"
                                        class="flex-1 px-2.5 py-2 border border-gray-200 rounded-lg text-xs focus:ring-1 focus:ring-cuan-green focus:border-cuan-green" :disabled="loading">
                                    <div class="relative w-32">
                                        <span class="absolute left-2 top-1/2 -translate-y-1/2 text-[10px] text-gray-400">Rp</span>
                                        <input type="number" min="0" x-model.number="additionalCosts[idx].value" placeholder="0"
                                            class="w-full pl-7 pr-2 py-2 border border-gray-200 rounded-lg text-xs text-right focus:ring-1 focus:ring-cuan-green focus:border-cuan-green font-mono" :disabled="loading">
                                    </div>
                                    <button @click="removeCost(idx)" class="w-7 h-7 flex items-center justify-center rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </div>
                            </template>
                            <button @click="addCost()" class="w-full py-2 border border-dashed border-gray-300 rounded-lg text-xs text-gray-500 hover:text-cuan-green hover:border-cuan-green transition-colors flex items-center justify-center gap-1">
                                <i class="fa-solid fa-plus text-[10px]"></i> Tambah Biaya
                            </button>
                        </div>
                    </div>

                    {{-- Target Profit --}}
                    <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                        <p class="text-[10px] font-black text-gray-600 uppercase tracking-widest">
                            Target Keuntungan
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

            {{-- ==================== 2. OUTPUT PANEL (Col 5) ==================== --}}
            <div class="lg:col-span-5 relative min-h-[400px]">

                {{-- Empty State --}}
                <div x-show="!parsedResult && !loading && !error" class="absolute inset-0 bg-white rounded-2xl border border-gray-200 shadow-sm p-8 flex flex-col items-center justify-center text-center fade-in">
                    <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-chart-pie text-2xl text-gray-300"></i>
                    </div>
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-tight mb-2">Belum Ada Analisis</h3>
                    <p class="text-xs text-gray-400 max-w-xs">Upload foto atau isi informasi produk. Kalkulaba AI akan menghitung HPP, strategi harga, dan prediksi keuntungan.</p>
                </div>

                {{-- Loading State --}}
                <div x-show="loading" class="absolute inset-0 bg-white rounded-2xl border border-gray-200 shadow-sm p-8 flex flex-col items-center justify-center text-center fade-in z-10">
                    <i class="fa-solid fa-calculator text-3xl text-cuan-green animate-bounce mb-4"></i>
                    <p class="text-sm font-bold text-gray-800">Menghitung biaya & strategi harga...</p>
                    <p class="text-xs text-gray-400 mt-1" x-text="imageBase64 ? 'AI sedang menganalisis gambar & produk kamu' : 'AI sedang menganalisis produk kamu'"></p>
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

                    {{-- Top Bar --}}
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-5 py-3 bg-emerald-50/30 flex items-center justify-between border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <p class="text-[10px] font-black text-gray-800 uppercase tracking-widest">Hasil Analisis Kalkulaba</p>
                            </div>
                            <button @click="copyRawJson()" hidden
                                class="px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-[10px] font-bold uppercase tracking-wider text-gray-600 hover:text-cuan-green hover:border-cuan-green transition-all shadow-sm">
                                <i class="fas mr-1" :class="copied ? 'fa-check text-cuan-green' : 'fa-copy'"></i>
                                <span x-text="copied ? 'Tersalin!' : 'Salin JSON'"></span>
                            </button>
                        </div>

                        {{-- COGS Summary --}}
                        <div class="p-5">
                            <div class="flex items-center gap-3 mb-4">
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">HPP Per Unit</p>
                                    <p class="text-xl font-black text-gray-900" x-text="'Rp ' + formatNumber(computedCogs)"></p>
                                    <p x-show="recipeEdited" class="text-[10px] text-amber-600 font-bold">Dihitung dari resep yang diedit</p>
                                </div>
                            </div>

                            {{-- Breakdown Table --}}
                            <div class="bg-gray-50 rounded-xl p-3" x-show="cogsBreakdown.length > 0">
                                <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Rincian Biaya</p>
                                <template x-for="(item, idx) in cogsBreakdown" :key="idx">
                                    <div class="flex justify-between items-center py-1.5 border-b border-gray-100 last:border-0">
                                        <span class="text-xs text-gray-600" x-text="item.label"></span>
                                        <span class="text-xs font-bold text-gray-800" x-text="'Rp ' + formatNumber(item.value)"></span>
                                    </div>
                                </template>
                                <div class="flex justify-between items-center pt-2 mt-1 border-t-2 border-gray-300">
                                    <span class="text-xs font-black text-gray-700">Total HPP</span>
                                    <span class="text-sm font-black text-cuan-green" x-text="'Rp ' + formatNumber(computedCogs)"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pricing Cards --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <template x-for="tier in ['low', 'competitive', 'exclusive']" :key="tier">
                            <div class="pricing-card bg-white rounded-2xl border border-gray-200 shadow-sm p-4" :class="'pricing-' + tier">
                                <div class="flex items-center gap-2 mb-3">
                                    <i class="text-sm" :class="tier === 'low' ? 'fa-solid fa-tag text-blue-500' : tier === 'competitive' ? 'fa-solid fa-balance-scale text-cuan-green' : 'fa-solid fa-crown text-amber-500'"></i>
                                    <p class="text-[10px] font-black uppercase tracking-widest"
                                        :class="tier === 'low' ? 'text-blue-600' : tier === 'competitive' ? 'text-cuan-green' : 'text-amber-600'"
                                        x-text="tier === 'low' ? 'Harga Hemat' : tier === 'competitive' ? 'Harga Pasar' : 'Harga Premium'"></p>
                                </div>
                                <p class="text-lg font-black text-gray-900 mb-2" x-text="'Rp ' + formatNumber(computedPricing[tier]?.price || 0)"></p>
                                <div class="space-y-1.5 text-xs">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Profit/unit</span>
                                        <span class="font-bold text-cuan-green" x-text="'Rp ' + formatNumber(computedPricing[tier]?.profit || 0)"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Margin</span>
                                        <span class="font-bold" x-text="(computedPricing[tier]?.margin || 0) + '%'"></span>
                                    </div>
                                    <div class="flex justify-between bg-gray-50 -mx-1 px-1 py-1 rounded">
                                        <span class="text-gray-500">Target unit</span>
                                        <span class="font-black text-gray-900" x-text="formatNumber(computedPricing[tier]?.units || 0) + ' pcs'"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- EDITABLE Recipe Section --}}
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden" x-show="editableIngredients.length > 0">
                        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <p class="text-[10px] font-black text-gray-600 uppercase tracking-widest">Resep & Bahan</p>
                                <span x-show="recipeEdited" class="px-1.5 py-0.5 bg-amber-100 text-amber-700 text-[9px] font-bold rounded uppercase">Diedit</span>
                            </div>
                            <button @click="resetRecipe()" x-show="recipeEdited" class="text-[10px] text-gray-500 hover:text-cuan-green font-bold uppercase tracking-wider">
                                <i class="fa-solid fa-rotate-left mr-1"></i> Reset
                            </button>
                        </div>
                        <div class="p-4">
                            {{-- Editable Ingredients --}}
                            <div class="mb-4">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Bahan-bahan <span class="text-gray-300 normal-case">(klik untuk edit)</span></p>
                                <template x-for="(ing, idx) in editableIngredients" :key="idx">
                                    <div class="ingredient-row flex items-center gap-1 py-1.5 px-2 rounded text-xs group">
                                        <span class="w-5 h-5 rounded-full bg-cuan-green/10 text-cuan-green text-[10px] font-bold flex items-center justify-center flex-shrink-0" x-text="idx + 1"></span>
                                        <input type="text" x-model="editableIngredients[idx].name" @input="markRecipeEdited()" class="editable-input flex-1 text-xs text-gray-700 font-medium min-w-0">
                                        <input type="text" x-model="editableIngredients[idx].quantity" @input="markRecipeEdited()" class="editable-input w-20 text-xs text-gray-500 text-center">
                                        <div class="relative w-24">
                                            <span class="absolute left-1 top-1/2 -translate-y-1/2 text-[10px] text-gray-400">Rp</span>
                                            <input type="number" min="0" x-model.number="editableIngredients[idx].estimated_cost" @input="markRecipeEdited(); recalculate()" class="editable-input w-full pl-5 text-xs font-bold text-gray-800 text-right">
                                        </div>
                                        <button @click="removeIngredient(idx)" class="w-5 h-5 flex items-center justify-center text-red-300 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <i class="fa-solid fa-xmark text-[10px]"></i>
                                        </button>
                                    </div>
                                </template>
                                <button @click="addIngredient()" class="w-full py-1.5 mt-1 border border-dashed border-gray-200 rounded-lg text-[10px] text-gray-400 hover:text-cuan-green hover:border-cuan-green transition-colors flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-plus"></i> Tambah Bahan
                                </button>
                                <div class="flex justify-between items-center pt-2 mt-2 border-t border-gray-200 px-2">
                                    <span class="text-xs font-bold text-gray-600">Total Bahan</span>
                                    <span class="text-sm font-black text-cuan-green" x-text="'Rp ' + formatNumber(computedIngredientsCost)"></span>
                                </div>
                            </div>

                            {{-- Steps --}}
                            <div x-show="recipeSteps.length > 0">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Langkah Pembuatan</p>
                                <template x-for="(step, idx) in recipeSteps" :key="idx">
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
                            <p class="text-[10px] font-black text-gray-600 uppercase tracking-widest">Smart Insights</p>
                        </div>
                        <div class="p-4 space-y-2">
                            <template x-for="(insight, idx) in (parsedResult?.insights || [])" :key="idx">
                                <div class="insight-item pl-3 py-2 text-xs text-gray-700 leading-relaxed" x-text="insight"></div>
                            </template>
                        </div>
                    </div>

                    {{-- Raw Output (Collapsible) --}}
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden" hidden>
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

            {{-- ==================== 3. HISTORY PANEL (Col 3) ==================== --}}
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
                                <div class="flex items-center gap-2 mb-1">
                                    <template x-if="item.imagePreview">
                                        <img :src="item.imagePreview" class="w-8 h-8 rounded-lg object-cover flex-shrink-0">
                                    </template>
                                    <div class="min-w-0">
                                        <p class="text-xs text-gray-700 font-bold truncate" x-text="item.productName"></p>
                                        <p class="text-[10px] text-gray-500" x-text="'HPP: Rp ' + formatNumber(item.cogs || 0)"></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 mt-1.5">
                                    <span class="px-1.5 py-0.5 rounded bg-gray-100 text-[8px] font-black uppercase text-gray-500" x-text="item.businessType"></span>
                                    <span x-show="item.hasImage" class="px-1.5 py-0.5 rounded bg-purple-50 text-[8px] font-black uppercase text-purple-600">📷 foto</span>
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
        businessType: 'food',
        additionalCosts: [
            { label: 'Gas / Listrik', value: 0 },
            { label: 'Tenaga Kerja', value: 0 },
            { label: 'Packaging', value: 0 },
        ],
        targetProfit: 1000000,
        advancedMode: '',
        language: 'id',
        showCosts: false,

        // Image state
        imagePreview: null,
        imageBase64: '',
        imageUrl: '',
        uploading: false,
        isDragging: false,

        // Output state
        rawOutput: '',
        parsedResult: null,
        error: '',
        loading: false,
        copied: false,
        showRaw: false,

        // Editable recipe state
        editableIngredients: [],
        recipeSteps: [],
        originalIngredients: [],
        recipeEdited: false,

        // History
        history: [],
        storageKey: 'kalkulaba_analysis_history_v2',

        init() {
            const saved = localStorage.getItem(this.storageKey);
            if (saved) {
                try { this.history = JSON.parse(saved); } catch(e) {}
            }
        },

        // ===================== IMAGE HANDLING =====================
        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) this.uploadImage(file);
        },

        handleDrop(event) {
            this.isDragging = false;
            const file = event.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) this.uploadImage(file);
        },

        async uploadImage(file) {
            if (file.size > 5 * 1024 * 1024) {
                this.error = 'Ukuran gambar maksimal 5MB.';
                return;
            }

            // Local preview immediately using blob URL
            this.imagePreview = URL.createObjectURL(file);
            this.uploading = true;
            this.error = '';

            const formData = new FormData();
            formData.append('image', file);

            try {
                const res = await fetch('{{ route("clara-ai.kalkulaba.upload-image") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });
                const data = await res.json();
                if (data.success) {
                    // We keep using the local preview URL if server gives a 403, 
                    // but we store the real URL for persistence if needed
                    this.imageUrl = data.url;
                    this.imageBase64 = data.image_base64;
                    // If server URL is successful, we can optionally switch preview
                    // but local blob is safer and faster.
                } else {
                    this.error = data.message || 'Gagal mengunggah gambar.';
                    this.removeImage();
                }
            } catch (e) {
                this.error = 'Gagal mengunggah gambar. Coba lagi.';
                this.removeImage();
            } finally {
                this.uploading = false;
            }
        },

        removeImage() {
            this.imagePreview = null;
            this.imageBase64 = '';
            this.imageUrl = '';
            if (this.$refs.imageInput) this.$refs.imageInput.value = '';
        },

        // ===================== COST MANAGEMENT =====================
        addCost() {
            this.additionalCosts.push({ label: '', value: 0 });
        },

        removeCost(idx) {
            this.additionalCosts.splice(idx, 1);
        },

        // ===================== RECIPE EDITING =====================
        addIngredient() {
            this.editableIngredients.push({ name: '', quantity: '', estimated_cost: 0 });
            this.markRecipeEdited();
        },

        removeIngredient(idx) {
            this.editableIngredients.splice(idx, 1);
            this.markRecipeEdited();
            this.recalculate();
        },

        markRecipeEdited() {
            this.recipeEdited = true;
        },

        resetRecipe() {
            this.editableIngredients = JSON.parse(JSON.stringify(this.originalIngredients));
            this.recipeEdited = false;
            this.recalculate();
        },

        // ===================== COMPUTED VALUES =====================
        get computedIngredientsCost() {
            return this.editableIngredients.reduce((sum, ing) => sum + (Number(ing.estimated_cost) || 0), 0);
        },

        get cogsBreakdown() {
            if (!this.parsedResult?.cost_analysis?.breakdown) return [];

            const breakdown = this.parsedResult.cost_analysis.breakdown;
            let items = [];

            // Handle new array format
            if (Array.isArray(breakdown)) {
                items = breakdown.map(b => ({
                    label: b.label || 'Unknown',
                    value: b.value || 0,
                }));
            } else {
                // Handle old object format (backwards compatibility)
                for (const [key, val] of Object.entries(breakdown)) {
                    items.push({ label: this.costLabelMap(key), value: val });
                }
            }

            // Selalu sinkronkan 'Bahan Baku' dengan total resep jika ada data bahan baku
            if (this.editableIngredients.length > 0 && items.length > 0) {
                const bahanIdx = items.findIndex(i => 
                    i.label.toLowerCase().includes('bahan') || 
                    i.label.toLowerCase() === 'ingredients'
                );
                if (bahanIdx !== -1) {
                    items[bahanIdx].value = this.computedIngredientsCost;
                }
            }

            return items;
        },

        get computedCogs() {
            return this.cogsBreakdown.reduce((sum, item) => sum + (Number(item.value) || 0), 0);
        },

        get computedPricing() {
            if (!this.parsedResult?.pricing) return {};

            const cogs = this.computedCogs;
            const result = {};

            for (const tier of ['low', 'competitive', 'exclusive']) {
                const original = this.parsedResult.pricing[tier];
                if (!original) continue;

                // If recipe was edited, recalculate based on new COGS
                if (this.recipeEdited) {
                    const margin = original.margin || 0;
                    const price = Math.ceil(cogs * (1 + margin / 100));
                    const profit = price - cogs;
                    const units = profit > 0 ? Math.ceil(this.targetProfit / profit) : 0;

                    result[tier] = { price, profit, margin, units };
                } else {
                    result[tier] = {
                        price: original.price || 0,
                        profit: original.profit_per_unit || 0,
                        margin: original.margin || 0,
                        units: original.units_to_target || 0,
                    };
                }
            }

            return result;
        },

        recalculate() {
            // Force reactivity — Alpine handles this via getters
        },

        costLabelMap(key) {
            const map = { ingredients: 'Bahan Baku', gas: 'Gas / Listrik', labor: 'Tenaga Kerja', packaging: 'Packaging', rent: 'Sewa Tempat', utilities: 'Utilitas', other: 'Lain-lain' };
            return map[key] || key;
        },

        // ===================== SUBMIT =====================
        async submit() {
            if (this.loading || !this.productName.trim()) return;
            this.loading = true;
            this.rawOutput = '';
            this.parsedResult = null;
            this.editableIngredients = [];
            this.recipeSteps = [];
            this.originalIngredients = [];
            this.recipeEdited = false;
            this.error = '';
            this.copied = false;

            const prompt = `Analisis produk: ${this.productName}. ${this.productDescription}. Tipe bisnis: ${this.businessType}. Target profit: Rp ${this.targetProfit}.`;

            try {
                const res = await fetch('{{ route("clara-ai.generate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        mode: 'kalkulaba',
                        prompt: prompt,
                        language: this.language,
                        product_name: this.productName,
                        product_description: this.productDescription,
                        image_url: this.imageUrl,
                        image_base64: this.imageBase64,
                        business_type: this.businessType,
                        additional_costs: this.additionalCosts.filter(c => c.label && c.value > 0),
                        target_profit: this.targetProfit,
                        advanced_mode: this.advancedMode,
                    }),
                });

                const data = await res.json();
                if (data.success) {
                    this.rawOutput = data.result;
                    this.parseResult(data.result);
                    if (this.parsedResult) this.saveToHistory();
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
                let jsonStr = raw.trim();

                // 1. Remove markdown code blocks if any
                jsonStr = jsonStr.replace(/^```json\s*/im, '')
                                .replace(/```\s*$/i, '')
                                .trim();

                // 2. Extract only the content between the FIRST { and LAST }
                // If it's truncated and missing the last }, we still try to take from the first {
                const firstBrace = jsonStr.indexOf('{');
                if (firstBrace === -1) {
                    throw new Error("No JSON object found in response");
                }
                
                let lastBrace = jsonStr.lastIndexOf('}');
                
                // If truncated, we might need to repair it instead of just cutting to the last brace
                let candidate = jsonStr.substring(firstBrace, lastBrace !== -1 ? lastBrace + 1 : jsonStr.length);

                // 3. Robust Fixes
                
                // Fix unescaped newlines in strings
                candidate = candidate.replace(/\n/g, '\\n');
                
                // But wait, the above might break the actual JSON structure if we are not careful.
                // Re-doing: only fix newlines that are NOT between property/value separators
                // Actually, a simpler way is to try parsing, and if it fails, try to repair.

                try {
                    this.parsedResult = JSON.parse(this.cleanJSON(candidate));
                } catch (firstError) {
                    console.warn("First parse attempt failed, trying to repair JSON...", firstError);
                    const repaired = this.repairJson(candidate);
                    this.parsedResult = JSON.parse(repaired);
                }

                // Populate editable recipe
                if (this.parsedResult?.recipe?.ingredients) {
                    this.editableIngredients = JSON.parse(JSON.stringify(this.parsedResult.recipe.ingredients));
                    this.originalIngredients = JSON.parse(JSON.stringify(this.parsedResult.recipe.ingredients));
                }
                if (this.parsedResult?.recipe?.steps) {
                    this.recipeSteps = [...this.parsedResult.recipe.steps];
                }
            } catch (e) {
                console.error('Failed to parse JSON even after repair:', e, { content: raw });
                this.parsedResult = null;
                this.error = 'AI response bukan format JSON yang valid: ' + (e.message || 'Unknown error');
                this.showRaw = true;
            }
        },

        cleanJSON(str) {
            // Strip trailing commas
            str = str.replace(/,\s*([\]}])/g, '$1');
            // Remove any potential non-json filler if AI added it after the last brace
            return str;
        },

        repairJson(json) {
            // 1. Fix unescaped control characters in values (like newlines)
            // This is tricky without a full parser, but we can fix the most common: literal newlines inside double quotes
            let repaired = json.replace(/"([^"]*)"/g, (match, p1) => {
                return '"' + p1.replace(/\n/g, '\\n').replace(/\r/g, '\\r') + '"';
            });

            // 2. Fix truncated JSON by balancing braces and brackets
            let stack = [];
            for (let i = 0; i < repaired.length; i++) {
                let char = repaired[i];
                if (char === '{' || char === '[') {
                    stack.push(char === '{' ? '}' : ']');
                } else if (char === '}' || char === ']') {
                    if (stack.length > 0 && stack[stack.length - 1] === char) {
                        stack.pop();
                    }
                }
            }

            // Close everything left in the stack
            while (stack.length > 0) {
                let closing = stack.pop();
                repaired += closing;
            }

            return repaired;
        },

        // ===================== HISTORY =====================
        saveToHistory() {
            const newItem = {
                id: Date.now(),
                timestamp: new Date().toISOString(),
                productName: this.productName,
                businessType: this.businessType,
                advancedMode: this.advancedMode,
                hasImage: !!this.imageBase64,
                imagePreview: this.imagePreview,
                cogs: this.computedCogs,
                rawOutput: this.rawOutput,
                parsedResult: this.parsedResult,
                inputs: {
                    productName: this.productName,
                    productDescription: this.productDescription,
                    imagePreview: this.imagePreview,
                    imageUrl: this.imageUrl,
                    businessType: this.businessType,
                    additionalCosts: JSON.parse(JSON.stringify(this.additionalCosts)),
                    targetProfit: this.targetProfit,
                    advancedMode: this.advancedMode,
                    language: this.language,
                },
            };
            this.history.unshift(newItem);
            if (this.history.length > 15) this.history.pop();

            // Don't save base64 to localStorage (too large)
            const historyForStorage = this.history.map(h => {
                const copy = {...h};
                delete copy.imageBase64;
                return copy;
            });
            localStorage.setItem(this.storageKey, JSON.stringify(historyForStorage));
        },

        loadHistory(item) {
            this.rawOutput = item.rawOutput;
            this.parsedResult = item.parsedResult;
            this.error = '';
            this.recipeEdited = false;

            if (item.inputs) {
                this.productName = item.inputs.productName;
                this.productDescription = item.inputs.productDescription;
                this.imagePreview = item.inputs.imagePreview || null;
                this.imageUrl = item.inputs.imageUrl || '';
                this.imageBase64 = ''; // Not stored in history
                this.businessType = item.inputs.businessType;
                this.additionalCosts = item.inputs.additionalCosts || [{ label: 'Gas / Listrik', value: 0 }, { label: 'Tenaga Kerja', value: 0 }, { label: 'Packaging', value: 0 }];
                this.targetProfit = item.inputs.targetProfit;
                this.advancedMode = item.inputs.advancedMode;
                this.language = item.inputs.language;
            }

            // Populate editable recipe
            if (this.parsedResult?.recipe?.ingredients) {
                this.editableIngredients = JSON.parse(JSON.stringify(this.parsedResult.recipe.ingredients));
                this.originalIngredients = JSON.parse(JSON.stringify(this.parsedResult.recipe.ingredients));
            }
            if (this.parsedResult?.recipe?.steps) {
                this.recipeSteps = [...this.parsedResult.recipe.steps];
            }
        },

        clearHistory() {
            if (confirm('Hapus semua history analisis?')) {
                this.history = [];
                localStorage.removeItem(this.storageKey);
            }
        },

        // ===================== UTILITIES =====================
        formatNumber(num) {
            if (!num && num !== 0) return '0';
            return Number(Math.round(num)).toLocaleString('id-ID');
        },

        formatTime(iso) {
            const d = new Date(iso);
            return d.getDate() + '/' + (d.getMonth() + 1) + ' ' + String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
        },

        async copyRawJson() {
            if (!this.rawOutput) return;
            try { await navigator.clipboard.writeText(this.rawOutput); }
            catch { const t = document.createElement('textarea'); t.value = this.rawOutput; t.style.cssText = 'position:fixed;opacity:0'; document.body.appendChild(t); t.select(); document.execCommand('copy'); document.body.removeChild(t); }
            this.copied = true;
            setTimeout(() => this.copied = false, 2000);
        },
    };
}
</script>
@endpush
@endsection

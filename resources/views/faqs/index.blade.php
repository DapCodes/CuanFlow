@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Bantuan & FAQ - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Bantuan & FAQ</span>
</li>
@endsection

@push('styles')
<style>
    .faq-item {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .faq-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.05);
        border-color: #10b981 !important;
    }
    
    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease-in-out;
    }
    
    .faq-answer.active {
        max-height: 1000px;
    }
    
    .faq-icon {
        transition: transform 0.3s ease;
    }
    
    .faq-icon.active {
        transform: rotate(180deg);
    }

    .category-tag {
        transition: all 0.2s ease;
    }

    .category-tag:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .category-tag.active {
        transform: translateY(-1px);
    }
</style>
@endpush

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-6xl mx-auto space-y-8">

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Pusat Bantuan & FAQ
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Temukan jawaban cepat untuk pertanyaan operasional Anda.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-white border border-gray-200 rounded-xl px-4 py-2 shadow-sm flex items-center gap-3">
                    <span class="flex h-2 w-2 rounded-full bg-cuan-green animate-pulse"></span>
                    <span class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Support Online</span>
                </div>
            </div>
        </section>

        {{-- SEARCH BOX --}}
        <section class="max-w-3xl mx-auto">
            <div class="relative group">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-cuan-green transition-colors text-lg"></i>
                <input type="text" 
                       id="searchFaq" 
                       placeholder="Cari solusi atau pertanyaan Anda..." 
                       class="w-full pl-14 pr-4 py-5 bg-white border border-gray-200 rounded-2xl text-base font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all shadow-sm">
            </div>
            <p class="mt-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">
                <i class="fas fa-lightbulb text-cuan-green mr-2 scale-110"></i>
                Tips: Masukkan kata kunci seperti "Stok" atau "Laporan"
            </p>
        </section>

        <section class="max-w-5xl mx-auto overflow-x-auto no-scrollbar -mx-4 px-4 md:mx-0 md:px-0">
            <div class="flex flex-nowrap md:flex-wrap gap-2 md:justify-center pb-2 min-w-max md:min-w-0">
                <button onclick="filterByCategory('')" 
                        data-category=""
                        class="category-tag category-btn px-5 md:px-6 py-2 md:py-2.5 rounded-xl text-[9px] md:text-[10px] font-black uppercase tracking-widest border border-cuan-green bg-cuan-green text-white shadow-lg shadow-emerald-100 transition-all active">
                    Semua
                </button>
                
                @php
                    $categories = [
                        ['id' => 'general', 'label' => 'Umum', 'icon' => 'fa-info-circle'],
                        ['id' => 'pos', 'label' => 'Point of Sale', 'icon' => 'fa-cash-register'],
                        ['id' => 'product', 'label' => 'Produk & Stok', 'icon' => 'fa-box'],
                        ['id' => 'finance', 'label' => 'Keuangan', 'icon' => 'fa-wallet'],
                        ['id' => 'report', 'label' => 'Laporan', 'icon' => 'fa-file-invoice'],
                        ['id' => 'account', 'label' => 'Akun', 'icon' => 'fa-user-gear']
                    ];
                @endphp

                @foreach($categories as $cat)
                <button onclick="filterByCategory('{{ $cat['id'] }}')" 
                        data-category="{{ $cat['id'] }}"
                        class="category-tag category-btn px-5 md:px-6 py-2 md:py-2.5 rounded-xl text-[9px] md:text-[10px] font-black uppercase tracking-widest border border-gray-200 bg-white text-gray-400 hover:border-cuan-green hover:text-cuan-green transition-all shadow-sm">
                    <i class="fas {{ $cat['icon'] }} mr-2"></i>
                    {{ $cat['label'] }}
                </button>
                @endforeach
            </div>
        </section>

        {{-- FAQ LIST --}}
        <section class="max-w-4xl mx-auto">
            <div id="noResults" class="hidden text-center py-16">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-search text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak Ada Hasil</h3>
                <p class="text-gray-500">Coba gunakan kata kunci lain atau pilih kategori berbeda</p>
            </div>

            <div id="faqList" class="space-y-4">
                @forelse($faqs as $faq)
                    @if($faq->is_active || auth()->user()->can('aktifkan nonaktifkan faq'))
                        @php
                            $typeColors = [
                                'general' => ['badge' => 'bg-gray-100 text-gray-700 border-gray-200'],
                                'pos' => ['badge' => 'bg-blue-100 text-blue-700 border-blue-200'],
                                'product' => ['badge' => 'bg-purple-100 text-purple-700 border-purple-200'],
                                'finance' => ['badge' => 'bg-emerald-100 text-emerald-700 border-emerald-200'],
                                'report' => ['badge' => 'bg-orange-100 text-orange-700 border-orange-200'],
                                'account' => ['badge' => 'bg-teal-100 text-teal-700 border-teal-200'],
                            ];
                            $typeIcons = [
                                'general' => 'fa-info-circle',
                                'pos' => 'fa-cash-register',
                                'product' => 'fa-box',
                                'finance' => 'fa-wallet',
                                'report' => 'fa-file-invoice',
                                'account' => 'fa-user-gear',
                            ];
                            $colors = $typeColors[$faq->type] ?? $typeColors['general'];
                        @endphp
                        
                        <div class="faq-item faq-row bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm"
                             data-question="{{ strtolower($faq->question) }}"
                             data-answer="{{ strtolower(strip_tags($faq->answer)) }}"
                             data-type="{{ $faq->type }}">
                            
                            {{-- FAQ Header --}}
                            <div class="faq-header px-4 md:px-6 py-4 md:py-5" onclick="toggleFaq(this)">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-base md:text-lg font-black text-gray-900 mb-2.5 leading-snug tracking-tight break-words pr-2">
                                            {{ $faq->question }}
                                        </h3>
                                        <!-- detail button -->
                                        <div class="flex flex-wrap items-center gap-2 mt-auto">
                                            <a href="{{ route('faqs.show', $faq->id) }}" class="inline-flex items-center px-3 md:px-4 py-1.5 rounded-lg text-[9px] md:text-[10px] font-black uppercase tracking-widest border border-emerald-100 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors">
                                                <i class="fas fa-eye text-[8px] mr-2"></i>
                                                Detail
                                            </a>
                                            <span class="inline-flex items-center px-3 md:px-4 py-1.5 rounded-lg text-[9px] md:text-[10px] font-black uppercase tracking-widest border border-emerald-100 bg-emerald-50 text-emerald-600">
                                                <i class="fas {{ $typeIcons[$faq->type] ?? 'fa-question' }} text-[8px] mr-2"></i>
                                                {{ $faq->getTypeLabel() }}
                                            </span>
                                            @if($faq->priority === 'high')
                                                <span class="inline-flex items-center px-3 md:px-4 py-1.5 rounded-lg text-[9px] md:text-[10px] font-black uppercase tracking-widest border border-amber-100 bg-amber-50 text-amber-600">
                                                    <i class="fas fa-star text-[8px] mr-2"></i>
                                                    Penting
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-lg bg-gray-50 border border-gray-100">
                                        <i class="fas fa-chevron-down faq-icon text-gray-400 text-xs md:text-sm"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- FAQ Answer --}}
                            <div class="faq-answer bg-gray-50/50">
                                <div class="px-5 md:px-6 py-6 border-t border-gray-100">
                                    <div class="text-sm md:text-base text-gray-600 leading-relaxed font-medium space-y-3 mb-6">
                                        {!! nl2br(e($faq->answer)) !!}
                                    </div>

                                    {{-- Helpful Buttons --}}
                                    <div class="pt-5 border-t border-gray-200">
                                        <p class="text-sm font-medium text-gray-700 mb-3">Apakah jawaban ini membantu?</p>
                                        <div class="flex flex-wrap items-center gap-2 md:gap-3">
                                            @php
                                                $userVote = $faq->currentUserVote;
                                                $isHelpful = $userVote && $userVote->is_helpful;
                                                $isNotHelpful = $userVote && !$userVote->is_helpful;
                                            @endphp
                                            
                                            @can('tandai faq membantu')
                                            <button onclick="markHelpful({{ $faq->id }}, event, this)" 
                                                    id="btn-helpful-{{ $faq->id }}"
                                                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border-2 transition-all font-medium text-xs md:text-sm {{ $isHelpful ? 'bg-emerald-50 border-emerald-400 text-emerald-800' : 'border-emerald-200 bg-white text-emerald-700 hover:bg-emerald-50 hover:border-emerald-300' }}">
                                                <i class="fas fa-thumbs-up"></i>
                                                <span class="whitespace-nowrap">Ya, Membantu</span>
                                                <span class="px-2 py-0.5 rounded-md {{ $isHelpful ? 'bg-emerald-200 text-emerald-800' : 'bg-emerald-100 text-emerald-700' }} text-[10px] md:text-xs font-bold" id="helpful-{{ $faq->id }}">
                                                    {{ $faq->helpful_count }}
                                                </span>
                                            </button>
                                            @endcan

                                            @can('tandai faq tidak membantu')
                                            <button onclick="markNotHelpful({{ $faq->id }}, event, this)" 
                                                    id="btn-not-helpful-{{ $faq->id }}"
                                                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border-2 transition-all font-medium text-xs md:text-sm {{ $isNotHelpful ? 'bg-gray-100 border-gray-400 text-gray-800' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50 hover:border-gray-300' }}">
                                                <i class="fas fa-thumbs-down"></i>
                                                <span class="whitespace-nowrap">Tidak</span>
                                                <span class="px-2 py-0.5 rounded-md {{ $isNotHelpful ? 'bg-gray-300 text-gray-800' : 'bg-gray-100 text-gray-700' }} text-[10px] md:text-xs font-bold" id="not-helpful-{{ $faq->id }}">
                                                    {{ $faq->not_helpful_count }}
                                                </span>
                                            </button>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="text-center py-16">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-6">
                            <i class="fas fa-question-circle text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Pertanyaan</h3>
                        <p class="text-gray-500">Pertanyaan akan muncul di sini setelah ditambahkan</p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- HELP SECTION --}}
        <section class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6 pb-20">
            <div class="bg-gray-900 rounded-xl p-8 text-white relative overflow-hidden group">
                <div class="relative z-10 space-y-4">
                    <h4 class="text-xl font-black uppercase tracking-tighter">Butuh Respon Cepat?</h4>
                    <p class="text-xs text-gray-400 font-medium leading-relaxed">
                        Jika Anda tidak menemukan jawaban yang dicari, tim support kami siap membantu melalui WhatsApp.
                    </p>
                    <a href="https://wa.me/628123456789" target="_blank" class="inline-flex items-center text-[10px] font-black uppercase tracking-widest text-cuan-green hover:text-white transition-colors">
                        Hubungi WhatsApp <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
                <i class="fab fa-whatsapp absolute -bottom-6 -right-6 text-8xl text-white/5"></i>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-8 relative overflow-hidden group">
                <div class="relative z-10 space-y-4">
                    <h4 class="text-xl font-black uppercase tracking-tighter text-gray-900">Email Support</h4>
                    <p class="text-xs text-gray-500 font-medium leading-relaxed">
                        Untuk pertanyaan teknis yang lebih kompleks atau kerjasama, silakan kirimkan email kepada kami.
                    </p>
                    <a href="mailto:support@cuanflow.com" class="inline-flex items-center text-[10px] font-black uppercase tracking-widest text-cuan-green/60 hover:text-cuan-green transition-colors">
                        Kirim Email <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
                <i class="fas fa-envelope absolute -bottom-6 -right-6 text-8xl text-gray-50 group-hover:text-gray-100 transition-colors"></i>
            </div>
        </section>

    </div>
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchFaq');
    const faqRows = document.querySelectorAll('.faq-row');
    const noResults = document.getElementById('noResults');
    const faqList = document.getElementById('faqList');

    function filterFaqs() {
        const searchTerm = (searchInput.value || '').toLowerCase();
        const activeBtn = document.querySelector('.category-btn.active');
        const activeCategory = activeBtn ? activeBtn.dataset.category : '';
        
        let visibleCount = 0;

        faqRows.forEach(row => {
            const question = row.dataset.question || '';
            const answer = row.dataset.answer || '';
            const type = row.dataset.type || '';

            const matchesSearch = !searchTerm || question.includes(searchTerm) || answer.includes(searchTerm);
            const matchesCategory = !activeCategory || type === activeCategory;

            if (matchesSearch && matchesCategory) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (visibleCount === 0) {
            noResults.classList.remove('hidden');
            faqList.classList.add('hidden');
        } else {
            noResults.classList.add('hidden');
            faqList.classList.remove('hidden');
        }
    }

    searchInput.addEventListener('input', filterFaqs);
});

function filterByCategory(category) {
    const buttons = document.querySelectorAll('.category-btn');
    
    buttons.forEach(btn => {
        // Reset to base style
        btn.classList.remove('active', 'border-cuan-green', 'bg-cuan-green', 'text-white', 'shadow-lg', 'shadow-emerald-100');
        btn.classList.add('border-gray-200', 'bg-white', 'text-gray-400');

        // Apply active style
        if (btn.dataset.category === category) {
            btn.classList.remove('border-gray-200', 'bg-white', 'text-gray-400');
            btn.classList.add('active', 'border-cuan-green', 'bg-cuan-green', 'text-white', 'shadow-lg', 'shadow-emerald-100');
        }
    });

    const faqRows = document.querySelectorAll('.faq-row');
    const noResults = document.getElementById('noResults');
    const faqList = document.getElementById('faqList');
    let visibleCount = 0;

    faqRows.forEach(row => {
        const type = row.dataset.type || '';
        if (!category || type === category) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    if (visibleCount === 0) {
        noResults.classList.remove('hidden');
        faqList.classList.add('hidden');
    } else {
        noResults.classList.add('hidden');
        faqList.classList.remove('hidden');
    }
}

function toggleFaq(element) {
    const faqItem = element.closest('.faq-item');
    const answer = faqItem.querySelector('.faq-answer');
    const icon = element.querySelector('.faq-icon');
    
    const isActive = answer.classList.contains('active');
    
    document.querySelectorAll('.faq-answer.active').forEach(a => {
        a.classList.remove('active');
    });
    document.querySelectorAll('.faq-icon.active').forEach(i => {
        i.classList.remove('active');
    });
    
    if (!isActive) {
        answer.classList.add('active');
        icon.classList.add('active');
    }
}

function markHelpful(faqId, event, btn) {
    event.stopPropagation();
    
    fetch(`/faqs/${faqId}/helpful`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateVoteUI(faqId, data);
        }
    })
    .catch(error => console.error('Error:', error));
}

function markNotHelpful(faqId, event, btn) {
    event.stopPropagation();
    
    fetch(`/faqs/${faqId}/not-helpful`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateVoteUI(faqId, data);
        }
    })
    .catch(error => console.error('Error:', error));
}

function updateVoteUI(faqId, data) {
    // Update counts
    const helpfulCountEl = document.getElementById(`helpful-${faqId}`);
    const notHelpfulCountEl = document.getElementById(`not-helpful-${faqId}`);
    
    if (helpfulCountEl) helpfulCountEl.textContent = data.helpful_count;
    if (notHelpfulCountEl) notHelpfulCountEl.textContent = data.not_helpful_count;

    // Reset Buttons Class
    const helpfulBtn = document.getElementById(`btn-helpful-${faqId}`);
    const notHelpfulBtn = document.getElementById(`btn-not-helpful-${faqId}`);

    // Base Classes
    const baseHelpful = "inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 transition-all font-medium text-sm border-emerald-200 bg-white text-emerald-700 hover:bg-emerald-50 hover:border-emerald-300";
    const activeHelpful = "inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 transition-all font-medium text-sm bg-emerald-50 border-emerald-400 text-emerald-800";
    
    const baseNotHelpful = "inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 transition-all font-medium text-sm border-gray-200 bg-white text-gray-700 hover:bg-gray-50 hover:border-gray-300";
    const activeNotHelpful = "inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 transition-all font-medium text-sm bg-gray-100 border-gray-400 text-gray-800";

    // Count Badge Classes
    const baseHelpfulBadge = "px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700 text-xs font-bold";
    const activeHelpfulBadge = "px-2 py-0.5 rounded-md bg-emerald-200 text-emerald-800 text-xs font-bold";

    const baseNotHelpfulBadge = "px-2 py-0.5 rounded-md bg-gray-100 text-gray-700 text-xs font-bold";
    const activeNotHelpfulBadge = "px-2 py-0.5 rounded-md bg-gray-300 text-gray-800 text-xs font-bold";

    // Logic
    if (data.status === 'removed') {
        helpfulBtn.className = baseHelpful;
        notHelpfulBtn.className = baseNotHelpful;
        helpfulCountEl.className = baseHelpfulBadge;
        notHelpfulCountEl.className = baseNotHelpfulBadge;
    } else if (data.type === 'helpful') {
        helpfulBtn.className = activeHelpful;
        notHelpfulBtn.className = baseNotHelpful;
        helpfulCountEl.className = activeHelpfulBadge;
        notHelpfulCountEl.className = baseNotHelpfulBadge;
    } else if (data.type === 'not_helpful') {
        helpfulBtn.className = baseHelpful;
        notHelpfulBtn.className = activeNotHelpful;
        helpfulCountEl.className = baseHelpfulBadge;
        notHelpfulCountEl.className = activeNotHelpfulBadge;
    }

    // Animation
    if (data.type === 'helpful') {
        helpfulCountEl.classList.add('animate-bounce');
        setTimeout(() => helpfulCountEl.classList.remove('animate-bounce'), 500);
    } else if (data.type === 'not_helpful') {
        notHelpfulCountEl.classList.add('animate-bounce');
        setTimeout(() => notHelpfulCountEl.classList.remove('animate-bounce'), 500);
    }
}
</script>
@endpush
@endsection
@extends('layouts.app')

@section('title', 'Bantuan & FAQ - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Bantuan & FAQ</span>
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
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        border-color: #14b8a6 !important;
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
<main class="flex-grow py-6 md:py-10 px-4 bg-gradient-to-br from-gray-50 via-white to-teal-50/30">
    <div class="max-w-5xl mx-auto space-y-8">

        {{-- HEADER HERO --}}
        <section class="text-center space-y-4 py-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-teal-500 to-teal-600 shadow-lg shadow-teal-500/30 mb-4">
                <i class="fas fa-circle-question text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900">
                Pusat Bantuan CuanFlow
            </h1>
            <p class="text-base md:text-lg text-gray-600 max-w-2xl mx-auto">
                Temukan jawaban cepat untuk pertanyaan Anda. Pilih kategori atau cari langsung pertanyaan yang Anda butuhkan.
            </p>
        </section>

        {{-- SEARCH BOX --}}
        <section class="max-w-2xl mx-auto">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-5 pointer-events-none">
                    <i class="fas fa-search text-gray-400 text-lg"></i>
                </div>
                <input type="text" 
                       id="searchFaq" 
                       placeholder="Ketik pertanyaan Anda di sini..." 
                       class="w-full pl-14 pr-4 py-4 md:py-5 rounded-2xl border-2 border-gray-200 text-base md:text-lg text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-4 focus:ring-teal-400/30 focus:border-teal-400 shadow-sm transition-all">
            </div>
            <p class="mt-3 text-sm text-gray-500 text-center">
                <i class="fas fa-lightbulb text-yellow-500 mr-1"></i>
                Contoh: "Bagaimana cara menambah produk?" atau "Cara melihat laporan penjualan"
            </p>
        </section>

        <section class="max-w-5xl mx-auto">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 md:p-6">
                <div class="flex flex-wrap gap-2 justify-center">
                    <button onclick="filterByCategory('')" 
                            data-category=""
                            class="category-tag category-btn px-4 py-2 rounded-full text-sm font-semibold border-2 border-teal-500 bg-teal-500 text-white hover:bg-teal-600 hover:border-teal-600 transition-all active">
                        Semua
                    </button>
                    
                    <button onclick="filterByCategory('general')" 
                            data-category="general"
                            class="category-tag category-btn px-4 py-2 rounded-full text-sm font-semibold border-2 border-gray-300 bg-white text-gray-700 hover:border-gray-400 hover:bg-gray-50 transition-all">
                        <i class="fas fa-info-circle text-xs mr-1"></i>
                        Umum
                    </button>

                    <button onclick="filterByCategory('pos')" 
                            data-category="pos"
                            class="category-tag category-btn px-4 py-2 rounded-full text-sm font-semibold border-2 border-gray-300 bg-white text-gray-700 hover:border-blue-400 hover:bg-blue-50 hover:text-blue-700 transition-all">
                        <i class="fas fa-cash-register text-xs mr-1"></i>
                        Point of Sale
                    </button>

                    <button onclick="filterByCategory('product')" 
                            data-category="product"
                            class="category-tag category-btn px-4 py-2 rounded-full text-sm font-semibold border-2 border-gray-300 bg-white text-gray-700 hover:border-purple-400 hover:bg-purple-50 hover:text-purple-700 transition-all">
                        <i class="fas fa-box text-xs mr-1"></i>
                        Produk & Stok
                    </button>

                    <button onclick="filterByCategory('finance')" 
                            data-category="finance"
                            class="category-tag category-btn px-4 py-2 rounded-full text-sm font-semibold border-2 border-gray-300 bg-white text-gray-700 hover:border-emerald-400 hover:bg-emerald-50 hover:text-emerald-700 transition-all">
                        <i class="fas fa-wallet text-xs mr-1"></i>
                        Keuangan
                    </button>

                    <button onclick="filterByCategory('report')" 
                            data-category="report"
                            class="category-tag category-btn px-4 py-2 rounded-full text-sm font-semibold border-2 border-gray-300 bg-white text-gray-700 hover:border-orange-400 hover:bg-orange-50 hover:text-orange-700 transition-all">
                        <i class="fas fa-file-invoice text-xs mr-1"></i>
                        Laporan
                    </button>

                    <button onclick="filterByCategory('account')" 
                            data-category="account"
                            class="category-tag category-btn px-4 py-2 rounded-full text-sm font-semibold border-2 border-gray-300 bg-white text-gray-700 hover:border-teal-400 hover:bg-teal-50 hover:text-teal-700 transition-all">
                        <i class="fas fa-user-gear text-xs mr-1"></i>
                        Akun & Pengaturan
                    </button>
                </div>
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
                        
                        <div class="faq-item faq-row bg-white border-2 border-gray-200 rounded-2xl overflow-hidden shadow-sm"
                             data-question="{{ strtolower($faq->question) }}"
                             data-answer="{{ strtolower(strip_tags($faq->answer)) }}"
                             data-type="{{ $faq->type }}">
                            
                            {{-- FAQ Header --}}
                            <div class="faq-header px-5 md:px-6 py-4 md:py-5" onclick="toggleFaq(this)">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1">
                                        <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-2 leading-snug pr-8">
                                            {{ $faq->question }}
                                        </h3>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $colors['badge'] }}">
                                                <i class="fas {{ $typeIcons[$faq->type] ?? 'fa-question' }} text-[10px] mr-1.5"></i>
                                                {{ $faq->getTypeLabel() }}
                                            </span>
                                            @if($faq->priority === 'high')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border bg-red-100 text-red-700 border-red-200">
                                                    <i class="fas fa-star text-[10px] mr-1.5"></i>
                                                    Penting
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100">
                                        <i class="fas fa-chevron-down faq-icon text-gray-500 text-sm"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- FAQ Answer --}}
                            <div class="faq-answer bg-gradient-to-br from-gray-50 to-white">
                                <div class="px-5 md:px-6 py-5 border-t-2 border-gray-100">
                                    <div class="text-sm md:text-base text-gray-700 leading-relaxed space-y-3 mb-6">
                                        {!! nl2br(e($faq->answer)) !!}
                                    </div>

                                    {{-- Helpful Buttons --}}
                                    <div class="pt-5 border-t border-gray-200">
                                        <p class="text-sm font-medium text-gray-700 mb-3">Apakah jawaban ini membantu?</p>
                                        <div class="flex items-center gap-3">
                                            @can('tandai faq membantu')
                                            <button onclick="markHelpful({{ $faq->id }}, event)" 
                                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 border-emerald-200 bg-white text-emerald-700 hover:bg-emerald-50 hover:border-emerald-300 transition-all font-medium text-sm">
                                                <i class="fas fa-thumbs-up"></i>
                                                <span>Ya, Membantu</span>
                                                <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700 text-xs font-bold" id="helpful-{{ $faq->id }}">
                                                    {{ $faq->helpful_count }}
                                                </span>
                                            </button>
                                            @endcan
                                            @can('tandai faq tidak membantu')
                                            <button onclick="markNotHelpful({{ $faq->id }}, event)" 
                                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 border-gray-200 bg-white text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-all font-medium text-sm">
                                                <i class="fas fa-thumbs-down"></i>
                                                <span>Tidak</span>
                                                <span class="px-2 py-0.5 rounded-md bg-gray-100 text-gray-700 text-xs font-bold" id="not-helpful-{{ $faq->id }}">
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
        <section class="max-w-4xl mx-auto">
            <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl shadow-xl p-8 text-center text-white">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/20 backdrop-blur-sm mb-4">
                    <i class="fas fa-headset text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-3">Masih Butuh Bantuan?</h3>
                <p class="text-teal-50 mb-6 max-w-xl mx-auto">
                    Tim support kami siap membantu Anda. Hubungi kami untuk bantuan lebih lanjut.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="mailto:support@cuanflow.com" 
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-teal-600 hover:bg-teal-50 transition-all font-semibold shadow-lg">
                        <i class="fas fa-envelope"></i>
                        <span>Email Support</span>
                    </a>
                    <a href="https://wa.me/628123456789" 
                       target="_blank"
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white/10 backdrop-blur-sm text-white hover:bg-white/20 transition-all font-semibold border-2 border-white/30">
                        <i class="fab fa-whatsapp"></i>
                        <span>WhatsApp</span>
                    </a>
                </div>
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
    const categoryStyles = {
        '': { border: 'border-teal-500', bg: 'bg-teal-500', text: 'text-white', hover: 'hover:bg-teal-600 hover:border-teal-600' },
        'general': { border: 'border-gray-400', bg: 'bg-gray-50', text: 'text-gray-700', hover: 'hover:border-gray-400 hover:bg-gray-50' },
        'pos': { border: 'border-blue-400', bg: 'bg-blue-50', text: 'text-blue-700', hover: 'hover:border-blue-400 hover:bg-blue-50' },
        'product': { border: 'border-purple-400', bg: 'bg-purple-50', text: 'text-purple-700', hover: 'hover:border-purple-400 hover:bg-purple-50' },
        'finance': { border: 'border-emerald-400', bg: 'bg-emerald-50', text: 'text-emerald-700', hover: 'hover:border-emerald-400 hover:bg-emerald-50' },
        'report': { border: 'border-orange-400', bg: 'bg-orange-50', text: 'text-orange-700', hover: 'hover:border-orange-400 hover:bg-orange-50' },
        'account': { border: 'border-teal-400', bg: 'bg-teal-50', text: 'text-teal-700', hover: 'hover:border-teal-400 hover:bg-teal-50' }
    };

    buttons.forEach(btn => {
        btn.classList.remove('active', 'border-teal-500', 'bg-teal-500', 'text-white',
            'border-gray-400', 'bg-gray-50', 'text-gray-700',
            'border-blue-400', 'bg-blue-50', 'text-blue-700',
            'border-purple-400', 'bg-purple-50', 'text-purple-700',
            'border-emerald-400', 'bg-emerald-50', 'text-emerald-700',
            'border-orange-400', 'bg-orange-50', 'text-orange-700',
            'border-teal-400', 'bg-teal-50', 'text-teal-700');
        
        btn.classList.add('border-gray-300', 'bg-white', 'text-gray-700');

        if (btn.dataset.category === category) {
            const styles = categoryStyles[category];
            btn.classList.remove('border-gray-300', 'bg-white', 'text-gray-700');
            btn.classList.add('active', styles.border, styles.bg, styles.text);
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

function markHelpful(faqId, event) {
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
            const el = document.getElementById(`helpful-${faqId}`);
            el.textContent = data.helpful_count;
            el.classList.add('animate-bounce');
            setTimeout(() => el.classList.remove('animate-bounce'), 500);
        }
    })
    .catch(error => console.error('Error:', error));
}

function markNotHelpful(faqId, event) {
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
            const el = document.getElementById(`not-helpful-${faqId}`);
            el.textContent = data.not_helpful_count;
            el.classList.add('animate-bounce');
            setTimeout(() => el.classList.remove('animate-bounce'), 500);
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endpush
@endsection
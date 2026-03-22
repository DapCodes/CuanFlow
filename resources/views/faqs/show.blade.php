@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Detail FAQ - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('faqs.index') }}" class="text-gray-500 hover:text-gray-900 transition-colors">Bantuan & FAQ</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Detail FAQ</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-[#f9fafb]">
    <div class="max-w-4xl mx-auto space-y-8">
        
        {{-- Header Section --}}
        <section class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div class="animate-fade-in-down">
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Detail FAQ</h1>
                <p class="text-sm text-gray-500 font-medium mt-1">Melihat rincian pertanyaan dan jawaban FAQ.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('faqs.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-xs font-black uppercase tracking-widest text-gray-500 bg-white border border-gray-200 rounded-2xl hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </section>

        {{-- Content Card --}}
        <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden animate-fade-in-up">
            <div class="p-6 md:p-10 space-y-8">
                
                {{-- Question Area --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 bg-teal-100 text-teal-700 text-[10px] font-black uppercase tracking-widest rounded-full border border-teal-200">
                            {{ $faq->getTypeLabel() }}
                        </span>
                        @if($faq->priority === 'high')
                        <span class="px-3 py-1 bg-red-100 text-red-700 text-[10px] font-black uppercase tracking-widest rounded-full border border-red-200">
                            Penting
                        </span>
                        @endif
                        <span class="px-3 py-1 {{ $faq->is_active ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-gray-100 text-gray-700 border-gray-200' }} text-[10px] font-black uppercase tracking-widest rounded-full border">
                            {{ $faq->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <h2 class="text-3xl font-black text-gray-900 leading-tight">
                        {{ $faq->question }}
                    </h2>
                </div>

                {{-- Answer Area --}}
                <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100">
                    <h4 class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-400 mb-6 flex items-center gap-2">
                        <i class="fas fa-comment-dots opacity-50"></i> Jawaban
                    </h4>
                    <div class="text-base text-gray-700 leading-loose font-medium">
                        {!! nl2br(e($faq->answer)) !!}
                    </div>
                </div>

                {{-- Meta Info --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-4 border-t border-gray-100">
                    <div class="space-y-1">
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Dilihat</p>
                        <p class="text-sm font-bold text-gray-900">{{ number_format($faq->view_count) }} kali</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Membantu</p>
                        <p class="text-sm font-bold text-emerald-600">{{ number_format($faq->helpful_count) }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Tidak Membantu</p>
                        <p class="text-sm font-bold text-red-600">{{ number_format($faq->not_helpful_count) }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Dibuat Pada</p>
                        <p class="text-sm font-bold text-gray-900">{{ $faq->created_at->format('d M Y') }}</p>
                    </div>
                </div>

            </div>
        </div>

    </div>
</main>

<style>
    @keyframes fade-in-up {
        0% { opacity: 0; transform: translateY(30px) scale(0.98); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }
    .animate-fade-in-up { animation: fade-in-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    
    @keyframes fade-in-down {
        0% { opacity: 0; transform: translateY(-20px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-down { animation: fade-in-down 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
@endsection

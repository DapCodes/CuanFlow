@extends('layouts.app')

@section('title', $outletPolicy->title . ' - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('outlet-policies.index') }}" class="text-gray-500 hover:text-gray-900 transition-colors">Kebijakan Outlet</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Detail Kebijakan</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-12 px-4 bg-[#f9fafb]" x-data="{ readingProgress: 0 }" @scroll.window="readingProgress = (window.pageYOffset / (document.body.scrollHeight - window.innerHeight)) * 100">
    {{-- Reading Progress Bar --}}
    <div class="fixed top-0 left-0 h-1.5 bg-gray-900 z-[60] transition-all duration-100" :style="'width: ' + readingProgress + '%'"></div>

    <div class="max-w-4xl mx-auto space-y-10">
        
        {{-- Header Section --}}
        <section class="flex flex-col md:flex-row md:items-end justify-between gap-6 animate-fade-in-down">
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 bg-gray-900 text-[10px] font-black uppercase tracking-widest text-white rounded-lg">
                        {{ $outletPolicy->category ?? 'Umum' }}
                    </span>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                        Diperbarui {{ $outletPolicy->updated_at->diffForHumans() }}
                    </span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 tracking-tighter sm:text-5xl leading-tight">
                    {{ $outletPolicy->title }}
                </h1>
            </div>
            
            <div class="flex items-center gap-3">
                @can('edit kebijakan outlet')
                <a href="{{ route('outlet-policies.edit', $outletPolicy->id) }}" class="p-3 bg-white border border-gray-200 text-gray-900 rounded-2xl hover:bg-gray-50 transition-all shadow-sm">
                    <i class="fas fa-edit"></i>
                </a>
                @endcan
                <a href="{{ route('outlet-policies.index') }}" class="px-6 py-3 bg-white border border-gray-200 text-xs font-black uppercase tracking-widest text-gray-500 rounded-2xl hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </section>

        {{-- Main Document --}}
        <article class="bg-white border border-gray-200 rounded-[3rem] shadow-2xl shadow-gray-200/50 overflow-hidden animate-fade-in-up">
            <div class="p-8 md:p-16">
                
                {{-- Meta Info --}}
                <div class="flex flex-wrap items-center gap-8 pb-10 mb-10 border-b border-gray-50">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-900 border border-gray-100 font-black text-lg">
                            {{ substr($outletPolicy->creator->name ?? 'S', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-0.5">Disusun Oleh</p>
                            <p class="text-sm font-bold text-gray-900">{{ $outletPolicy->creator->name ?? 'Sistem' }}</p>
                        </div>
                    </div>

                    <div class="h-10 w-px bg-gray-100 hidden md:block"></div>

                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-400 border border-gray-100">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-0.5">Tanggal Terbit</p>
                            <p class="text-sm font-bold text-gray-900 text-nowrap">{{ $outletPolicy->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Content Body --}}
                <div class="prose prose-gray max-w-none">
                    <div class="text-gray-700 leading-[2] text-lg font-medium space-y-6 whitespace-pre-wrap">
                        {!! nl2br(e($outletPolicy->content)) !!}
                    </div>
                </div>

                {{-- Footer / Signature --}}
                <div class="mt-16 pt-10 border-t border-gray-100 bg-gray-50/50 -mx-8 -mb-8 md:-mx-16 md:-mb-16 p-8 md:p-16">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                        <div>
                            <h4 class="text-sm font-black text-gray-900 mb-1">Konfirmasi Kepatuhan</h4>
                            <p class="text-xs text-gray-500 font-medium">Pastikan seluruh tim telah membaca dan memahami kebijakan ini.</p>
                        </div>
                        <div class="flex items-center gap-2">
                             <span class="text-[10px] font-black uppercase text-gray-400 tracking-widest mr-4">Status: <span class="text-emerald-500 ml-1">Aktif & Berlaku</span></span>
                             <button onclick="window.print()" class="px-6 py-3 bg-gray-900 text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-xl shadow-gray-200 hover:bg-black transition-all">
                                 <i class="fas fa-print mr-2"></i> Cetak Dokumen
                             </button>
                        </div>
                    </div>
                </div>

            </div>
        </article>

        {{-- Help Section --}}
        <section class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-fade-in-up" style="animation-delay: 0.2s">
            <div class="bg-gray-900 rounded-[2rem] p-8 text-white relative overflow-hidden group">
                <div class="relative z-10 space-y-4">
                    <h4 class="text-xl font-bold">Butuh Penjelasan Lebih?</h4>
                    <p class="text-xs text-gray-400 font-medium leading-relaxed">
                        Jika ada bagian dari kebijakan ini yang kurang jelas, silakan tanyakan kepada manajer outlet atau pemilik.
                    </p>
                    <a href="#" class="inline-flex items-center text-[10px] font-black uppercase tracking-widest text-white/50 hover:text-white transition-colors">
                        Hubungi via WhatsApp <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
                <i class="fas fa-comment-dots absolute -bottom-6 -right-6 text-8xl text-white/5"></i>
            </div>

            <div class="bg-white border border-gray-200 rounded-[2rem] p-8 relative overflow-hidden group">
                <div class="relative z-10 space-y-4">
                    <h4 class="text-xl font-bold text-gray-900">FAQ Terkait</h4>
                    <p class="text-xs text-gray-500 font-medium leading-relaxed">
                        Mungkin pertanyaan Anda sudah terjawab di pusat bantuan kami.
                    </p>
                    <a href="{{ route('faqs.index') }}" class="inline-flex items-center text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-gray-900 transition-colors">
                        Pusat Bantuan <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
                <i class="fas fa-info-circle absolute -bottom-6 -right-6 text-8xl text-gray-50 group-hover:text-gray-100 transition-colors"></i>
            </div>
        </section>

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

    @media print {
        .fixed, footer, nav, aside, .animate-fade-in-down summary, a[href="{{ route('outlet-policies.index') }}"], .p-3.bg-white.border.border-gray-200 {
            display: none !important;
        }
        body { background: white !important; }
        .bg-[#f9fafb] { background: white !important; }
        .bg-white { border: none !important; shadow: none !important; }
        article { width: 100% !important; max-width: 100% !important; border: none !important; }
        .max-w-4xl { max-width: 100% !important; }
    }
</style>
@endsection

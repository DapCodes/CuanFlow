@extends('admin.layouts.app')

@section('title', 'Detail FAQ')
@section('page-title', 'Detail FAQ')

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <a href="{{ route('admin.faqs.index') }}" class="text-gray-500 hover:text-gray-700">FAQ</a>
</li>
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <span class="text-gray-700">Detail</span>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Detail FAQ</h2>
            <p class="text-sm text-gray-500 mt-1">Informasi lengkap mengenai pertanyaan FAQ</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.faqs.index') }}" 
               class="px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                Kembali
            </a>
            <a href="{{ route('admin.faqs.edit', $faq) }}" 
               class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-edit mr-2"></i> Edit FAQ
            </a>
        </div>
    </div>

    <!-- Main Info Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Details Card -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">{{ $faq->question }}</h3>
                    <div class="prose max-w-none text-gray-600 leading-relaxed">
                        {!! nl2br(e($faq->answer)) !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Meta Info Card -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="p-6 space-y-4">

                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Kategori</p>
                        <span class="px-2.5 py-1 text-xs font-medium bg-blue-50 text-blue-700 rounded-full border border-blue-100">
                            {{ $faq->getTypeLabel() }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Prioritas</p>
                        <span class="px-2.5 py-1 text-xs font-medium {{ $faq->priority === 'high' ? 'bg-red-50 text-red-700 border-red-100' : ($faq->priority === 'medium' ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-gray-50 text-gray-700 border-gray-100') }} rounded-full border">
                            {{ ucfirst($faq->priority) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Status</p>
                        <span class="px-2.5 py-1 text-xs font-medium {{ $faq->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-gray-50 text-gray-600 border-gray-100' }} rounded-full border">
                            {{ $faq->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Dilihat</p>
                        <p class="text-sm font-semibold text-gray-900">{{ number_format($faq->view_count) }} kali</p>
                    </div>
                </div>
                <div class="bg-gray-50 p-6 border-t border-gray-200 grid grid-cols-2 gap-4">
                    <div class="text-center">
                        <p class="text-lg font-bold text-emerald-600">{{ $faq->helpful_count }}</p>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Membantu</p>
                    </div>
                    <div class="text-center border-l border-gray-200">
                        <p class="text-lg font-bold text-red-600">{{ $faq->not_helpful_count }}</p>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tidak Membantu</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

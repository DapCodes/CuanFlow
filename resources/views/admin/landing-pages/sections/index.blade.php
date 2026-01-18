@extends('admin.layouts.app')

@section('title', 'Kelola Sections')

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right text-[8px] mx-2"></i>
    <a href="{{ route('admin.landing-pages.index') }}" class="hover:text-emerald-600 transition-colors">Landing Pages</a>
</li>
<li class="flex items-center">
    <i class="fas fa-chevron-right text-[8px] mx-2"></i>
    <span class="text-gray-600 font-medium">Sections</span>
</li>
@endsection

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <a href="{{ route('admin.landing-pages.edit', $landingPage) }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-emerald-600 transition-colors mb-4">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Edit
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Sections</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $landingPage->title }}</p>
        </div>
        <a href="{{ route('admin.landing-pages.preview', $landingPage) }}" target="_blank"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-colors">
            <i class="fas fa-eye"></i>
            Preview
        </a>
    </div>

    <!-- Info Card -->
    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6">
        <div class="flex items-start gap-3">
            <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
            <div>
                <p class="text-sm text-blue-800 font-medium">Tips mengelola sections</p>
                <p class="text-xs text-blue-600 mt-1">Drag & drop untuk mengatur urutan. Klik toggle untuk mengaktifkan/nonaktifkan section. Klik pada section untuk mengedit konten.</p>
            </div>
        </div>
    </div>

    <!-- Sections List -->
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden" id="sectionsContainer">
        <div class="divide-y divide-gray-50" id="sectionsList">
            @foreach($sections as $section)
            <div class="section-item p-4 hover:bg-gray-50/50 transition-colors" data-id="{{ $section->id }}">
                <div class="flex items-center gap-4">
                    <!-- Drag Handle -->
                    <div class="cursor-move text-gray-300 hover:text-gray-400 drag-handle">
                        <i class="fas fa-grip-vertical"></i>
                    </div>

                    <!-- Section Icon -->
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $section->is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-400' }}">
                        @switch($section->section_key)
                            @case('hero')
                                <i class="fas fa-mountain-sun"></i>
                                @break
                            @case('about')
                                <i class="fas fa-building"></i>
                                @break
                            @case('features')
                                <i class="fas fa-puzzle-piece"></i>
                                @break
                            @case('benefits')
                                <i class="fas fa-gift"></i>
                                @break
                            @case('app_preview')
                                <i class="fas fa-mobile-alt"></i>
                                @break
                            @case('statistics')
                                <i class="fas fa-chart-bar"></i>
                                @break
                            @case('testimonial')
                                <i class="fas fa-quote-left"></i>
                                @break
                            @case('pricing')
                                <i class="fas fa-tags"></i>
                                @break
                            @case('faq')
                                <i class="fas fa-question-circle"></i>
                                @break
                            @case('cta')
                                <i class="fas fa-bullhorn"></i>
                                @break
                            @case('footer')
                                <i class="fas fa-shoe-prints"></i>
                                @break
                            @default
                                <i class="fas fa-layer-group"></i>
                        @endswitch
                    </div>

                    <!-- Section Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="font-semibold text-gray-900">{{ $section->section_name }}</p>
                            <span class="text-xs text-gray-400 uppercase tracking-wider">{{ $section->section_key }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5 truncate">
                            {{ $section->title ?: 'Belum ada judul' }}
                            @if($section->hasItems())
                                • {{ $section->items->count() }} item
                            @endif
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2">
                        <!-- Toggle -->
                        <form action="{{ route('admin.landing-pages.sections.toggle', [$landingPage, $section]) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="p-2 rounded-lg transition-colors {{ $section->is_active ? 'text-emerald-500 hover:bg-emerald-50' : 'text-gray-400 hover:bg-gray-100' }}"
                                    title="{{ $section->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                <i class="fas {{ $section->is_active ? 'fa-toggle-on text-xl' : 'fa-toggle-off text-xl' }}"></i>
                            </button>
                        </form>

                        <!-- Edit -->
                        <a href="{{ route('admin.landing-pages.sections.edit', [$landingPage, $section]) }}" 
                           class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                           title="Edit Section">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Legend -->
    <div class="mt-6 p-4 bg-gray-50 rounded-xl">
        <p class="text-xs text-gray-500 font-medium mb-3">Keterangan Section:</p>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs text-gray-600">
            <div class="flex items-center gap-2">
                <i class="fas fa-mountain-sun text-emerald-500"></i>
                <span>Hero - Banner utama</span>
            </div>
            <div class="flex items-center gap-2">
                <i class="fas fa-puzzle-piece text-emerald-500"></i>
                <span>Features - Fitur aplikasi</span>
            </div>
            <div class="flex items-center gap-2">
                <i class="fas fa-quote-left text-emerald-500"></i>
                <span>Testimonial - Ulasan</span>
            </div>
            <div class="flex items-center gap-2">
                <i class="fas fa-question-circle text-emerald-500"></i>
                <span>FAQ - Pertanyaan umum</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    // Initialize Sortable for drag & drop
    const el = document.getElementById('sectionsList');
    const sortable = Sortable.create(el, {
        handle: '.drag-handle',
        animation: 150,
        ghostClass: 'bg-emerald-50',
        onEnd: function(evt) {
            // Get new order
            const items = el.querySelectorAll('.section-item');
            const order = Array.from(items).map(item => item.dataset.id);

            // Send to server
            fetch('{{ route('admin.landing-pages.sections.reorder', $landingPage) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order: order })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Optional: Show success toast
                }
            });
        }
    });
</script>
@endpush
@endsection

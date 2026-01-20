@extends('admin.layouts.app')

@section('title', 'Kelola Syarat & Ketentuan')

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right text-[8px] mx-2"></i>
    <span class="text-gray-600 font-medium">Syarat & Ketentuan</span>
</li>
@endsection

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Syarat & Ketentuan</h1>
            <p class="text-sm text-gray-500 mt-1">Sesuaikan isi halaman syarat dan ketentuan penggunaan layanan.</p>
        </div>
        <a href="{{ route('legal.terms') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
            <i class="fas fa-eye text-blue-500"></i>
            Lihat Halaman
        </a>
    </div>

    <form action="{{ route('admin.terms.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-3xl border border-gray-100 p-6 md:p-8 shadow-sm">
            <div class="mb-6">
                <label for="editor" class="block text-sm font-semibold text-gray-700 mb-2">Konten Syarat & Ketentuan</label>
                <div class="prose-editor">
                    <textarea name="content" id="editor">{{ old('content', $terms ? $terms->content : '') }}</textarea>
                </div>
                @error('content')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end pt-4 border-t border-gray-50">
                <button type="submit" class="px-8 py-3 bg-cuan-dark text-white font-bold rounded-xl hover:bg-cuan-green transition-all shadow-lg shadow-cuan-dark/10">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .ck-editor__editable {
        min-height: 500px;
        border-bottom-left-radius: 12px !important;
        border-bottom-right-radius: 12px !important;
    }
    .ck-toolbar {
        border-top-left-radius: 12px !important;
        border-top-right-radius: 12px !important;
        background-color: #F9FAFB !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#editor'), {
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|',
                'insertTable', 'undo', 'redo'
            ],
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' }
                ]
            }
        })
        .catch(error => {
            console.error(error);
        });
</script>
@endpush

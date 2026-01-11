@extends('admin.layouts.app')

@section('title', 'Tambah FAQ')
@section('page-title', 'Tambah FAQ Baru')

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <a href="{{ route('admin.faqs.index') }}" class="text-gray-500 hover:text-gray-700">FAQ</a>
</li>
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <span class="text-gray-700">Tambah</span>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.faqs.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Tambah FAQ Baru</h2>
                <p class="text-sm text-gray-500 mt-1">Buat FAQ baru untuk membantu pengguna</p>
            </div>
            <a href="{{ route('admin.faqs.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </a>
        </div>
        
        <!-- Form Card -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <div class="p-6 space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Outlet Selection -->
                    <div>
                        <label for="outlet_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih Outlet <span class="text-red-500">*</span>
                        </label>
                        <select name="outlet_id" id="outlet_id" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cuan-green focus:outline-none">
                            <option value="">-- Pilih Outlet --</option>
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id }}" {{ old('outlet_id') == $outlet->id ? 'selected' : '' }}>{{ $outlet->name }}</option>
                            @endforeach
                        </select>
                        @error('outlet_id') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <!-- Category Selection -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select name="type" id="type" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cuan-green focus:outline-none">
                            @foreach(App\Models\Faq::getTypes() as $key => $label)
                                <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Question -->
                <div>
                    <label for="question" class="block text-sm font-medium text-gray-700 mb-2">
                        Pertanyaan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="question" id="question" value="{{ old('question') }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cuan-green focus:outline-none"
                           placeholder="Masukkan pertanyaan...">
                    @error('question') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <!-- Answer -->
                <div>
                    <label for="answer" class="block text-sm font-medium text-gray-700 mb-2">
                        Jawaban <span class="text-red-500">*</span>
                    </label>
                    <textarea name="answer" id="answer" rows="6" required
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cuan-green focus:outline-none"
                              placeholder="Masukkan jawaban detail...">{{ old('answer') }}</textarea>
                    @error('answer') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Priority -->
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                            Prioritas <span class="text-red-500">*</span>
                        </label>
                        <select name="priority" id="priority" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cuan-green focus:outline-none">
                            @foreach(App\Models\Faq::getPriorities() as $key => $label)
                                <option value="{{ $key }}" {{ old('priority', 'medium') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Order -->
                    <div>
                        <label for="order" class="block text-sm font-medium text-gray-700 mb-2">
                            Urutan Tampil
                        </label>
                        <input type="number" name="order" id="order" value="{{ old('order', 0) }}" min="0"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cuan-green focus:outline-none">
                    </div>

                    <!-- Active Status -->
                    <div class="flex items-end pb-3">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="is_active" value="1" class="w-5 h-5 text-cuan-green border-gray-300 rounded focus:ring-cuan-green" {{ old('is_active', true) ? 'checked' : '' }}>
                            <span class="ml-3 text-sm font-semibold text-gray-700 group-hover:text-cuan-green transition-colors">Aktifkan Sekarang</span>
                        </label>
                    </div>
                </div>

            </div>
        </div>
        
        <!-- Actions -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.faqs.index') }}" 
               class="px-5 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit" 
                    class="px-8 py-2.5 bg-cuan-dark text-white font-semibold rounded-lg hover:bg-cuan-green transition-colors flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span>Simpan FAQ</span>
            </button>
        </div>
    </form>
</div>
@endsection

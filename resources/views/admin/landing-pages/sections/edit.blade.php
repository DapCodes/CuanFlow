@extends('admin.layouts.app')

@section('title', 'Edit Section')

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right text-[8px] mx-2"></i>
    <a href="{{ route('admin.landing-pages.index') }}" class="hover:text-emerald-600 transition-colors">Landing Pages</a>
</li>
<li class="flex items-center">
    <i class="fas fa-chevron-right text-[8px] mx-2"></i>
    <a href="{{ route('admin.landing-pages.sections.index', $landingPage) }}" class="hover:text-emerald-600 transition-colors">Sections</a>
</li>
<li class="flex items-center">
    <i class="fas fa-chevron-right text-[8px] mx-2"></i>
    <span class="text-gray-600 font-medium">{{ $section->section_name }}</span>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('admin.landing-pages.sections.index', $landingPage) }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-emerald-600 transition-colors mb-4">
            <i class="fas fa-arrow-left"></i>
            Kembali ke Sections
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Edit {{ $section->section_name }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $landingPage->title }}</p>
    </div>

    <form action="{{ route('admin.landing-pages.sections.update', [$landingPage, $section]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Section Content -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-edit text-emerald-500"></i>
                Konten Section
            </h2>
            
            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Judul Section</label>
                    <input type="text" name="title" id="title"
                           value="{{ old('title', $section->title) }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                           placeholder="Masukkan judul section">
                </div>

                <div>
                    <label for="subtitle" class="block text-sm font-medium text-gray-700 mb-2">Subtitle</label>
                    <input type="text" name="subtitle" id="subtitle"
                           value="{{ old('subtitle', $section->subtitle) }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                           placeholder="Masukkan subtitle (opsional)">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all resize-none"
                              placeholder="Masukkan deskripsi atau konten section">{{ old('description', $section->description) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Background Settings -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-fill-drip text-emerald-500"></i>
                Background
            </h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Background</label>
                    <div class="flex gap-3" x-data="{ type: '{{ $section->background_type }}' }">
                        <label class="flex-1">
                            <input type="radio" name="background_type" value="color" class="sr-only peer" x-model="type">
                            <div class="p-4 border-2 rounded-xl text-center cursor-pointer transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 border-gray-200 hover:border-gray-300">
                                <i class="fas fa-palette text-lg mb-1"></i>
                                <p class="text-sm font-medium">Warna</p>
                            </div>
                        </label>
                        <label class="flex-1">
                            <input type="radio" name="background_type" value="image" class="sr-only peer" x-model="type">
                            <div class="p-4 border-2 rounded-xl text-center cursor-pointer transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 border-gray-200 hover:border-gray-300">
                                <i class="fas fa-image text-lg mb-1"></i>
                                <p class="text-sm font-medium">Gambar</p>
                            </div>
                        </label>
                        <label class="flex-1">
                            <input type="radio" name="background_type" value="gradient" class="sr-only peer" x-model="type">
                            <div class="p-4 border-2 rounded-xl text-center cursor-pointer transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 border-gray-200 hover:border-gray-300">
                                <i class="fas fa-rainbow text-lg mb-1"></i>
                                <p class="text-sm font-medium">Gradient</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div x-show="type === 'color'" x-cloak>
                    <label for="background_value" class="block text-sm font-medium text-gray-700 mb-2">Warna Background</label>
                    <div class="flex items-center gap-3">
                        <input type="color" id="bg_color_picker" 
                               value="{{ $section->background_type === 'color' ? $section->background_value : '#ffffff' }}"
                               class="w-12 h-12 rounded-xl border-2 border-gray-200 cursor-pointer"
                               onchange="document.getElementById('background_value').value = this.value">
                        <input type="text" name="background_value" id="background_value"
                               value="{{ old('background_value', $section->background_value) }}"
                               class="flex-1 px-4 py-3 border border-gray-200 rounded-xl text-sm font-mono"
                               placeholder="#ffffff">
                    </div>
                </div>

                <div x-show="type === 'image'" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Background</label>
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-emerald-300 transition-colors">
                        <input type="file" name="background_image" id="background_image" accept="image/*" class="hidden">
                        <label for="background_image" class="cursor-pointer">
                            @if($section->background_type === 'image' && $section->background_value)
                                <img src="{{ Storage::url($section->background_value) }}" alt="Background" class="h-24 mx-auto mb-3 rounded-lg">
                                <p class="text-xs text-gray-500">Klik untuk ganti</p>
                            @else
                                <i class="fas fa-cloud-upload-alt text-3xl text-gray-300 mb-2"></i>
                                <p class="text-sm text-gray-500">Klik untuk upload gambar</p>
                            @endif
                        </label>
                    </div>
                </div>

                <div x-show="type === 'gradient'" x-cloak>
                    <label for="gradient_value" class="block text-sm font-medium text-gray-700 mb-2">CSS Gradient</label>
                    <input type="text" name="background_value" id="gradient_value"
                           value="{{ $section->background_type === 'gradient' ? $section->background_value : '' }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm font-mono"
                           placeholder="linear-gradient(135deg, #658C58, #31694E)">
                    <p class="mt-1 text-xs text-gray-400">Contoh: linear-gradient(135deg, #658C58, #31694E)</p>
                </div>
            </div>
        </div>

        <!-- Section Items -->
        @if($section->hasItems())
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-list text-emerald-500"></i>
                    Items ({{ $section->items->count() }})
                </h2>
                <button type="button" onclick="showAddItemModal()" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-700 text-sm font-medium rounded-lg hover:bg-emerald-100 transition-colors">
                    <i class="fas fa-plus"></i>
                    Tambah Item
                </button>
            </div>

            @if($section->items->count() > 0)
            <div class="space-y-3" id="itemsList">
                @foreach($section->items->sortBy('order') as $item)
                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
                    <div class="cursor-move text-gray-300 hover:text-gray-400">
                        <i class="fas fa-grip-vertical"></i>
                    </div>
                    @if($item->icon)
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm">
                            <i class="{{ $item->icon }} text-emerald-600"></i>
                        </div>
                    @elseif($item->image)
                        <img src="{{ Storage::url($item->image) }}" class="w-10 h-10 rounded-lg object-cover">
                    @else
                        <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-cube text-gray-400"></i>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 truncate">{{ $item->title ?: 'Tanpa judul' }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ Str::limit($item->description, 60) }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full {{ $item->is_active ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                        <form action="{{ route('admin.landing-pages.sections.items.destroy', [$landingPage, $section, $item]) }}" method="POST" class="inline"
                              onsubmit="return confirm('Hapus item ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-inbox text-2xl text-gray-300"></i>
                </div>
                <p class="text-sm text-gray-500">Belum ada item dalam section ini</p>
            </div>
            @endif
        </div>
        @endif

        <!-- Actions -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('admin.landing-pages.sections.index', $landingPage) }}" 
               class="px-6 py-3 border border-gray-200 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit" 
                    class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold shadow-sm transition-all hover:shadow-md">
                <i class="fas fa-save mr-2"></i>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<!-- Add Item Modal -->
<div id="addItemModal" class="fixed inset-0 z-50 hidden overflow-y-auto" x-data="{ open: false }">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black/50" onclick="closeAddItemModal()"></div>
        <div class="inline-block w-full max-w-lg p-6 my-8 text-left align-middle transition-all transform bg-white rounded-2xl shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Tambah Item Baru</h3>
                <button onclick="closeAddItemModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('admin.landing-pages.sections.items.store', [$landingPage, $section]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul *</label>
                        <input type="text" name="title" required
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" rows="3"
                                  class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Icon (FontAwesome class)</label>
                        <input type="text" name="icon" placeholder="fas fa-star"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gambar</label>
                        <input type="file" name="image" accept="image/*"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm">
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeAddItemModal()" 
                            class="px-4 py-2.5 border border-gray-200 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold">
                        Tambah Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showAddItemModal() {
        document.getElementById('addItemModal').classList.remove('hidden');
    }

    function closeAddItemModal() {
        document.getElementById('addItemModal').classList.add('hidden');
    }
</script>
@endpush
@endsection

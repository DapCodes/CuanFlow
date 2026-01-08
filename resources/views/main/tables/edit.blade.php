@extends('layouts.app')

@section('title', 'Edit Meja - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('tables.index') }}" class="text-gray-500 hover:text-gray-700">Kelola Meja</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Edit Meja {{ $table->table_number }}</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-3xl mx-auto space-y-6">

        {{-- HEADER --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-amber-50 text-amber-600 border border-amber-100">
                        <i class="fas fa-edit text-sm"></i>
                    </span>
                    <span>Edit Meja {{ $table->table_number }}</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Perbarui data meja outlet Anda.
                </p>
            </div>
            <form action="{{ route('tables.destroy', $table) }}" method="POST" 
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus meja ini?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-100 border border-red-200">
                    <i class="fas fa-trash text-sm"></i>
                    <span>Hapus Meja</span>
                </button>
            </form>
        </section>

        {{-- FORM --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            <form action="{{ route('tables.update', $table) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nomor Meja --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor Meja <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="table_number" value="{{ old('table_number', $table->table_number) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-colors @error('table_number') border-red-500 @enderror"
                               placeholder="Contoh: 1, A1, VIP-01" required>
                        @error('table_number')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama Meja --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Meja
                        </label>
                        <input type="text" name="name" value="{{ old('name', $table->name) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-colors"
                               placeholder="Contoh: Meja Teras, Meja VIP">
                    </div>

                    {{-- Kode --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Kode Meja
                        </label>
                        <input type="text" name="code" value="{{ old('code', $table->code) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-colors"
                               placeholder="Kode unik">
                    </div>

                    {{-- Kapasitas --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Kapasitas <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="capacity" value="{{ old('capacity', $table->capacity) }}" min="1" max="50"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-colors"
                               required>
                        @error('capacity')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Lokasi --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Lokasi
                        </label>
                        <input type="text" name="location" value="{{ old('location', $table->location) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-colors"
                               placeholder="Contoh: Indoor, Outdoor, Lantai 2">
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Status Saat Ini
                        </label>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium
                                @if($table->status === 'available') bg-emerald-50 text-emerald-700 border border-emerald-200
                                @elseif($table->status === 'occupied') bg-red-50 text-red-700 border border-red-200
                                @elseif($table->status === 'reserved') bg-yellow-50 text-yellow-700 border border-yellow-200
                                @else bg-gray-50 text-gray-700 border border-gray-200
                                @endif">
                                <span class="w-2 h-2 rounded-full mr-2
                                    @if($table->status === 'available') bg-emerald-500
                                    @elseif($table->status === 'occupied') bg-red-500
                                    @elseif($table->status === 'reserved') bg-yellow-500
                                    @else bg-gray-500
                                    @endif"></span>
                                {{ $table->getStatusLabel() }}
                            </span>
                            <p class="text-xs text-gray-500">Status diubah melalui halaman utama meja.</p>
                        </div>
                    </div>

                    {{-- Active Status --}}
                    <div class="md:col-span-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" 
                                   {{ old('is_active', $table->is_active) ? 'checked' : '' }}
                                   class="w-5 h-5 text-amber-500 rounded border-gray-300 focus:ring-amber-400">
                            <div>
                                <span class="text-sm font-medium text-gray-700">Aktif</span>
                                <p class="text-xs text-gray-500">Meja aktif akan muncul di daftar dan bisa digunakan.</p>
                            </div>
                        </label>
                    </div>

                    {{-- Catatan --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan
                        </label>
                        <textarea name="notes" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-colors"
                                  placeholder="Catatan tambahan tentang meja...">{{ old('notes', $table->notes) }}</textarea>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('tables.index') }}"
                       class="inline-flex items-center justify-center px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center px-6 py-2.5 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-all font-semibold text-sm sm:ml-auto">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </section>
    </div>
</main>
@endsection

@extends('layouts.app')

@section('title', 'Tambah Meja - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

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
    <span class="text-gray-900 font-medium">Tambah Meja</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-3xl mx-auto space-y-6">

        {{-- HEADER --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5">
            <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-amber-50 text-amber-600 border border-amber-100">
                    <i class="fas fa-plus text-sm"></i>
                </span>
                <span>Tambah Meja Baru</span>
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Isi data meja untuk ditambahkan ke sistem outlet Anda.
            </p>
        </section>

        {{-- FORM --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            <form action="{{ route('tables.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nomor Meja --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor Meja <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="table_number" value="{{ old('table_number') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-colors @error('table_number') border-red-500 @enderror"
                               placeholder="Contoh: 1, A1, VIP-01" required>
                        <p class="text-xs text-gray-500 mt-1">Nomor unik untuk identifikasi meja.</p>
                        @error('table_number')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama Meja --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Meja
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-colors"
                               placeholder="Contoh: Meja Teras, Meja VIP">
                        <p class="text-xs text-gray-500 mt-1">Opsional. Nama deskriptif untuk meja.</p>
                    </div>

                    {{-- Kode --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Kode Meja
                        </label>
                        <div class="flex gap-2">
                            <input type="text" name="code" id="codeInput" value="{{ old('code') }}"
                                   class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-colors"
                                   placeholder="Otomatis jika kosong">
                            <button type="button" id="generateCode"
                                    class="px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                <i class="fas fa-magic"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Kode unik untuk QR atau referensi.</p>
                    </div>

                    {{-- Kapasitas --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Kapasitas <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="capacity" value="{{ old('capacity', 4) }}" min="1" max="50"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-colors"
                               required>
                        <p class="text-xs text-gray-500 mt-1">Jumlah orang maksimal untuk meja ini.</p>
                        @error('capacity')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Lokasi --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Lokasi
                        </label>
                        <input type="text" name="location" value="{{ old('location') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-colors"
                               placeholder="Contoh: Indoor, Outdoor, Lantai 2, Teras">
                        <p class="text-xs text-gray-500 mt-1">Area atau zona di mana meja berada.</p>
                    </div>

                    {{-- Catatan --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan
                        </label>
                        <textarea name="notes" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-colors"
                                  placeholder="Catatan tambahan tentang meja...">{{ old('notes') }}</textarea>
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
                        Simpan Meja
                    </button>
                </div>
            </form>
        </section>
    </div>
</main>

@push('scripts')
<script>
document.getElementById('generateCode').addEventListener('click', function() {
    fetch('{{ route("tables.generate-code") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('codeInput').value = data.code;
        });
});
</script>
@endpush
@endsection

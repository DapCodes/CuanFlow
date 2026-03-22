@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Tambah Meja - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('tables.index') }}" class="text-gray-500 hover:text-gray-700">Kelola Meja</a>
</li>
<li class="flex items-center">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium">Tambah Meja</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-4xl mx-auto space-y-6">

        {{-- HEADER --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-gray-900">
                    Tambah Meja Baru
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Isi data meja untuk ditambahkan ke sistem outlet Anda.
                </p>
            </div>
        </section>

        {{-- FORM --}}
        <x-card-container>
            <form action="{{ route('tables.store') }}" method="POST" class="p-8 space-y-8">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Nomor Meja --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Nomor Meja <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="table_number" value="{{ old('table_number') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all @error('table_number') border-red-500 @enderror"
                               placeholder="Contoh: 1, A1, VIP-01" required>
                        <p class="text-xs text-gray-400 mt-2">Nomor unik untuk identifikasi meja.</p>
                        @error('table_number')
                            <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama Meja --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Nama Meja
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all"
                               placeholder="Contoh: Meja Teras, Meja VIP">
                        <p class="text-xs text-gray-400 mt-2">Opsional. Nama deskriptif untuk meja.</p>
                    </div>

                    {{-- Kode --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Kode Meja
                        </label>
                        <div class="flex gap-2">
                            <input type="text" name="code" id="codeInput" value="{{ old('code') }}"
                                   class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all"
                                   placeholder="Otomatis jika kosong">
                            <button type="button" id="generateCode"
                                    class="px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all active:scale-95">
                                <i class="fas fa-magic"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Kode unik untuk QR atau referensi.</p>
                    </div>

                    {{-- Kapasitas --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Kapasitas <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="capacity" value="{{ old('capacity', 4) }}" min="1" max="50"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all"
                               required>
                        <p class="text-xs text-gray-400 mt-2">Jumlah orang maksimal.</p>
                        @error('capacity')
                            <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Lokasi --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Lokasi
                        </label>
                        <input type="text" name="location" value="{{ old('location') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all"
                               placeholder="Contoh: Indoor, Outdoor, Lantai 2">
                        <p class="text-xs text-gray-400 mt-2">Area atau zona meja berada.</p>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Status Meja <span class="text-red-500">*</span>
                        </label>
                        <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all @error('status') border-red-500 @enderror" required>
                            @foreach(\App\Models\Table::getStatusOptions() as $value => $label)
                                <option value="{{ $value }}" {{ old('status', 'available') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Catatan --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Catatan
                        </label>
                        <textarea name="notes" rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all"
                                  placeholder="Catatan tambahan tentang meja...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-100">
                    <a href="{{ route('tables.index') }}"
                       class="inline-flex items-center justify-center px-6 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-600 bg-white hover:bg-gray-50 transition-all active:scale-95">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center px-8 py-3 bg-cuan-green text-white rounded-xl hover:bg-cuan-dark transition-all font-bold text-sm sm:ml-auto shadow-lg shadow-cuan-green/20 active:scale-95">
                        <i class="fas fa-save mr-2 text-xs"></i>
                        Simpan Meja
                    </button>
                </div>
            </form>
        </x-card-container>
    </div>
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('generateCode')?.addEventListener('click', function() {
        const btn = this;
        btn.classList.add('opacity-50');
        fetch('{{ route("tables.generate-code") }}')
            .then(response => response.json())
            .then(data => {
                document.getElementById('codeInput').value = data.code;
            })
            .finally(() => {
                btn.classList.remove('opacity-50');
            });
    });

    {{-- SweetAlert2 Notifications --}}
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '{{ session('success') }}',
            confirmButtonColor: '#658C58',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl px-6 py-2.5 font-bold'
            }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '{{ session('error') }}',
            confirmButtonColor: '#31694E',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl px-6 py-2.5 font-bold'
            }
        });
    @endif
});
</script>
@endpush
@endsection

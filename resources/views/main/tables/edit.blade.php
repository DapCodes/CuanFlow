@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Edit Meja - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('tables.index') }}" class="text-gray-500 hover:text-gray-700">Kelola Meja</a>
</li>
<li class="flex items-center">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium">Edit Meja {{ $table->table_number }}</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-4xl mx-auto space-y-6">

        {{-- HEADER --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-gray-900">
                    Edit Meja {{ $table->table_number }}
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Perbarui data meja outlet Anda.
                </p>
            </div>
            @can('hapus meja')
            <form action="{{ route('tables.destroy', $table) }}" method="POST" id="deleteForm">
                @csrf
                @method('DELETE')
                <button type="button" onclick="confirmDelete()"
                        class="inline-flex items-center gap-2 rounded-xl bg-white border border-red-200 px-5 py-2.5 text-sm font-bold text-red-600 hover:bg-red-50 transition-all active:scale-95 shadow-sm">
                    <i class="fas fa-trash text-xs"></i>
                    <span>Hapus Meja</span>
                </button>
            </form>
            @endcan
        </section>

        {{-- FORM --}}
        <x-card-container>
            <form action="{{ route('tables.update', $table) }}" method="POST" class="p-8 space-y-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Nomor Meja --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Nomor Meja <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="table_number" value="{{ old('table_number', $table->table_number) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all @error('table_number') border-red-500 @enderror"
                               placeholder="Contoh: 1, A1, VIP-01" required>
                        @error('table_number')
                            <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama Meja --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Nama Meja
                        </label>
                        <input type="text" name="name" value="{{ old('name', $table->name) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all"
                               placeholder="Contoh: Meja Teras, Meja VIP">
                    </div>

                    {{-- Kode --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Kode Meja
                        </label>
                        <input type="text" name="code" value="{{ old('code', $table->code) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all"
                               placeholder="Kode unik">
                    </div>

                    {{-- Kapasitas --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Kapasitas <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="capacity" value="{{ old('capacity', $table->capacity) }}" min="1" max="50"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all"
                               required>
                        @error('capacity')
                            <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Lokasi --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Lokasi
                        </label>
                        <input type="text" name="location" value="{{ old('location', $table->location) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all"
                               placeholder="Contoh: Indoor, Outdoor, Lantai 2">
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Status Meja <span class="text-red-500">*</span>
                        </label>
                        <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all @error('status') border-red-500 @enderror" required>
                            @foreach(\App\Models\Table::getStatusOptions() as $value => $label)
                                <option value="{{ $value }}" {{ old('status', $table->status) === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Active Status --}}
                    <div class="md:col-span-2 bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <label class="flex items-center gap-4 cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" name="is_active" value="1" 
                                       {{ old('is_active', $table->is_active) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cuan-green"></div>
                            </div>
                            <div>
                                <span class="text-sm font-bold text-gray-900">Aktif</span>
                                <p class="text-xs text-gray-500">Munculkan di daftar transaksi.</p>
                            </div>
                        </label>
                    </div>

                    {{-- Catatan --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Catatan
                        </label>
                        <textarea name="notes" rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all"
                                  placeholder="Catatan tambahan tentang meja...">{{ old('notes', $table->notes) }}</textarea>
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
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </x-card-container>
    </div>
</main>

@push('scripts')
<script>
function confirmDelete() {
    Swal.fire({
        title: 'Hapus Meja?',
        text: "Meja yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#31694E',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'rounded-xl px-4 py-2 font-bold',
            cancelButton: 'rounded-xl px-4 py-2 font-bold'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm').submit();
        }
    })
}

document.addEventListener('DOMContentLoaded', function() {
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

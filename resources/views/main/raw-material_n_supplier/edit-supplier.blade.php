@extends('layouts.app')

@section('title', 'Edit Supplier - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('raw-materials.suppliers') }}" class="text-gray-500 hover:text-cuan-green transition-colors">Supplier</a>
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Edit Supplier</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">
        
        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Edit Supplier
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Perbarui informasi untuk supplier: <span class="font-black text-gray-900">{{ $supplier->name }}</span>
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('raw-materials.suppliers') }}" 
                   class="inline-flex items-center gap-2 rounded-xl bg-white border border-gray-200 px-5 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all shadow-sm active:scale-95">
                    <span>Kembali</span>
                </a>
            </div>
        </section>

        {{-- FORM --}}
        <form action="{{ route('raw-materials.suppliers.update', $supplier) }}" method="POST" id="supplierForm">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Kolom Kiri: Informasi Utama --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    <x-card-container title="Informasi Supplier">
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="code" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Kode Supplier</label>
                                    <input type="text" name="code" id="code" value="{{ old('code', $supplier->code) }}" readonly
                                           class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold text-gray-500 shadow-sm cursor-not-allowed">
                                    <p class="mt-1.5 text-[10px] text-gray-400 font-medium italic">* Kode tidak dapat diubah</p>
                                </div>

                                <div>
                                    <label for="name" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Nama Supplier <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $supplier->name) }}" required
                                           class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all shadow-sm">
                                    @error('name') <p class="mt-1.5 text-[10px] text-red-500 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="contact_person" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Kontak Person</label>
                                    <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}"
                                           class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all shadow-sm">
                                    @error('contact_person') <p class="mt-1.5 text-[10px] text-red-500 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Telepon / WhatsApp</label>
                                    <input type="text" name="phone" id="phone" value="{{ old('phone', $supplier->phone) }}"
                                           class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all shadow-sm">
                                    @error('phone') <p class="mt-1.5 text-[10px] text-red-500 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="email" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Alamat Email</label>
                                    <input type="email" name="email" id="email" value="{{ old('email', $supplier->email) }}"
                                           class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all shadow-sm">
                                    @error('email') <p class="mt-1.5 text-[10px] text-red-500 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </x-card-container>

                    <x-card-container title="Alamat & Catatan">
                        <div class="p-6 space-y-6">
                            <div>
                                <label for="address" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Alamat Lengkap</label>
                                <textarea name="address" id="address" rows="3"
                                          class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all shadow-sm">{{ old('address', $supplier->address) }}</textarea>
                                @error('address') <p class="mt-1.5 text-[10px] text-red-500 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="notes" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Catatan Internal</label>
                                <textarea name="notes" id="notes" rows="2"
                                          class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all shadow-sm">{{ old('notes', $supplier->notes) }}</textarea>
                                @error('notes') <p class="mt-1.5 text-[10px] text-red-500 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </x-card-container>
                </div>

                {{-- Kolom Kanan: Status & Actions --}}
                <div class="lg:col-span-1 space-y-6">
                    <x-card-container title="Pengaturan">
                        <div class="p-6">
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-gray-50 border border-gray-100">
                                <div>
                                    <p class="text-sm font-black text-gray-900 uppercase tracking-widest">Status Aktif</p>
                                    <p class="text-[10px] font-bold text-gray-400 mt-1">Dapat dipilih di sistem</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cuan-green"></div>
                                </label>
                            </div>

                            <div class="mt-6 space-y-3">
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-cuan-green py-4 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                                    <i class="fas fa-save"></i>
                                    <span>Update Supplier</span>
                                </button>
                                <a href="{{ route('raw-materials.suppliers') }}"
                                   class="w-full inline-flex items-center justify-center py-4 text-sm font-bold text-gray-500 hover:text-gray-900 transition-all">
                                    Batal
                                </a>
                            </div>
                        </div>
                    </x-card-container>

                    {{-- Danger Zone --}}
                    <div class="p-6 rounded-3xl bg-red-50 border border-red-100 text-center">
                        <p class="text-[10px] font-black text-red-600 uppercase tracking-widest mb-3">Zona Berbahaya</p>
                        <p class="text-[10px] text-red-400 font-bold uppercase tracking-widest mb-4">Hapus data supplier ini? Tindakan ini permanen.</p>
                        <form action="{{ route('raw-materials.suppliers.destroy', $supplier) }}" method="POST" id="deleteForm">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmDelete()"
                                    class="w-full py-3 rounded-xl border border-red-200 text-[10px] font-black text-red-600 uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all active:scale-95">
                                <i class="fas fa-trash-alt mr-2"></i> Hapus Supplier
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>
@endsection

@push('scripts')
<script>
function confirmDelete() {
    Swal.fire({
        title: 'Hapus Supplier?',
        text: "Apakah Anda yakin ingin menghapus supplier ini? Tindakan ini tidak dapat dibatalkan.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'rounded-[2rem] border-none shadow-2xl',
            title: 'font-black text-gray-900',
            confirmButton: 'rounded-xl px-6 py-3 font-bold text-sm',
            cancelButton: 'rounded-xl px-6 py-3 font-bold text-sm'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm').submit();
        }
    });
}
</script>
@endpush
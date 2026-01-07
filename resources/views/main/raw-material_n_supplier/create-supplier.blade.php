@extends('layouts.app')

@section('title', 'Tambah Supplier - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('raw-materials.suppliers') }}" class="text-gray-500 hover:text-red-600 transition-colors">Supplier</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Tambah Baru</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">
        
        {{-- Header Section --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-500 border border-red-100">
                        <i class="fas fa-plus text-sm"></i>
                    </span>
                    <span>Tambah Supplier Baru</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Lengkapi formulir di bawah untuk mendaftarkan supplier baru.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('raw-materials.suppliers') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-sm transition-all">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </section>

        {{-- Form Container --}}
        <form action="{{ route('raw-materials.suppliers.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Left Column: Main Form --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- Basic Info --}}
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-info-circle text-gray-400"></i> Informasi Supplier
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Kode Supplier</label>
                                <input type="text" name="code" id="code" value="{{ old('code', $code) }}" readonly
                                    class="w-full rounded-lg border-gray-300 bg-gray-50 text-gray-500 text-sm shadow-sm py-2.5 cursor-not-allowed">
                                <p class="text-[10px] text-gray-400 mt-1 italic">* Kode otomatis dibuat oleh sistem</p>
                                @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Supplier <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: PT. Sumber Makmur"
                                    class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm shadow-sm py-2.5">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-1">Kontak Person</label>
                                <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person') }}" placeholder="Contoh: Ahmad"
                                    class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm shadow-sm py-2.5">
                                @error('contact_person') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon / WhatsApp</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="Contoh: 08123456789"
                                    class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm shadow-sm py-2.5">
                                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="supplier@example.com"
                                    class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm shadow-sm py-2.5">
                                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Address & Notes --}}
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-gray-400"></i> Detail Tambahan
                        </h3>
                        <div class="space-y-5">
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                                <textarea name="address" id="address" rows="3" placeholder="Masukkan alamat lengkap supplier..."
                                    class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm shadow-sm">{{ old('address') }}</textarea>
                                @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                                <textarea name="notes" id="notes" rows="2" placeholder="Catatan tambahan (opsional)..."
                                    class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm shadow-sm">{{ old('notes') }}</textarea>
                                @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Right Column: Status & Actions --}}
                <div class="lg:col-span-1 space-y-6">
                    
                    {{-- Status Card --}}
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                         <h3 class="text-sm font-semibold text-gray-900 mb-4 uppercase">Status</h3>
                         <div class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg bg-gray-50">
                             <div class="flex items-center h-5">
                                <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                    class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                             </div>
                             <div class="text-sm">
                                <label for="is_active" class="font-medium text-gray-700">Aktif</label>
                                <p class="text-xs text-gray-500">Supplier dapat dipilih dalam pengadaan bahan baku.</p>
                             </div>
                         </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="pt-4">
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-red-600 border border-transparent rounded-lg text-sm font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-lg transition-all">
                            <i class="fas fa-save mr-2"></i> Simpan Supplier
                        </button>
                        <a href="{{ route('raw-materials.suppliers') }}" class="mt-3 w-full inline-flex items-center justify-center px-4 py-3 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-all text-center">
                            Batal
                        </a>
                    </div>

                </div>
            </div>
        </form>

    </div>
</main>
@endsection
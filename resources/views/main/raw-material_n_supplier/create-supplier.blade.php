@extends('layouts.app')

@section('title', 'Tambah Supplier - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('raw-materials.suppliers') }}" class="text-gray-500 hover:text-orange-600 transition-colors">Supplier</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Tambah Supplier</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4">
    <div class="max-w-7xl mx-auto">
        
        <x-card-container>
            <!-- Header -->
            <div class="bg-gradient-to-br from-orange-50 to-red-50 p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                            Tambah Supplier Baru
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Isi formulir di bawah untuk menambahkan supplier</p>
                    </div>
                    <a href="{{ route('raw-materials.suppliers') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-all duration-200 shadow-md">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('raw-materials.suppliers.store') }}" method="POST" class="p-6">
                @csrf

                <div class="space-y-6">
                    <!-- Informasi Dasar -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-info-circle text-orange-500 mr-2"></i>
                            Informasi Dasar
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Code -->
                            <div>
                                <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Kode Supplier <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="code" 
                                       id="code" 
                                       value="{{ old('code', $code) }}"
                                       readonly
                                       class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg cursor-not-allowed @error('code') border-red-500 @enderror">
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Kode otomatis dibuat sistem
                                </p>
                                @error('code')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Nama Supplier <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       value="{{ old('name') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('name') border-red-500 @enderror" 
                                       required>
                                @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Contact Person -->
                            <div>
                                <label for="contact_person" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Kontak Person
                                </label>
                                <input type="text" 
                                       name="contact_person" 
                                       id="contact_person" 
                                       value="{{ old('contact_person') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('contact_person') border-red-500 @enderror">
                                @error('contact_person')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Telepon
                                </label>
                                <input type="text" 
                                       name="phone" 
                                       id="phone" 
                                       value="{{ old('phone') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('phone') border-red-500 @enderror">
                                @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="md:col-span-2">
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Email
                                </label>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       value="{{ old('email') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('email') border-red-500 @enderror">
                                @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Alamat & Catatan -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>
                            Alamat & Catatan
                        </h3>
                        
                        <div class="space-y-6">
                            <!-- Address -->
                            <div>
                                <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Alamat
                                </label>
                                <textarea name="address" 
                                          id="address" 
                                          rows="3"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                                @error('address')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Catatan
                                </label>
                                <textarea name="notes" 
                                          id="notes" 
                                          rows="3"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                                @error('notes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-toggle-on text-orange-500 mr-2"></i>
                            Status
                        </h3>
                        
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="is_active" 
                                   id="is_active" 
                                   value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500">
                            <label for="is_active" class="ml-2 text-sm font-medium text-gray-900">
                                Aktifkan supplier ini
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('raw-materials.suppliers') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </a>
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-gradient-to-r from-orange-400 to-red-500 text-white rounded-lg font-semibold hover:from-orange-500 hover:to-red-600 transition-all shadow-md">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Supplier
                    </button>
                </div>
            </form>
        </x-card-container>

    </div>
</main>
@endsection
@extends('layouts.app')

@section('title', 'Edit Diskon - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('discounts.index') }}" class="text-gray-500 hover:text-cuan-green transition-colors">Kelola Diskon</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('discounts.show', $discount->id) }}" class="text-gray-500 hover:text-cuan-green transition-colors">{{ $discount->name }}</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Edit</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-2 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">
        <form action="{{ route('discounts.update', $discount->id) }}" method="POST" class="space-y-6"
              x-data="{ isVoucher: {{ old('is_voucher', $discount->is_voucher) ? 'true' : 'false' }} }">
            @csrf
            @method('PUT')

            {{-- Header --}}
            <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-xl md:text-2xl font-black text-gray-900">
                        Edit Diskon
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Perbarui detail diskon <span class="font-bold text-gray-900">{{ $discount->name }}</span>.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('discounts.show', $discount->id) }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all active:scale-95">
                        <span>Batal</span>
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-5 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </section>

            {{-- Validasi Error --}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-100 rounded-xl px-6 py-4">
                    <p class="text-[10px] font-black uppercase tracking-widest text-red-500 mb-2">Terdapat Kesalahan</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="text-sm text-red-600">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Info penggunaan --}}
            @if($discount->used_count > 0)
                <div class="bg-blue-50 border border-blue-100 rounded-xl px-6 py-4">
                    <p class="text-[10px] font-black uppercase tracking-widest text-blue-500">
                        Diskon ini telah digunakan <strong>{{ $discount->used_count }} kali</strong>.
                        @if($discount->usage_limit)
                            Sisa {{ max($discount->usage_limit - $discount->used_count, 0) }} kali lagi.
                        @endif
                    </p>
                </div>
            @endif

            {{-- Informasi Dasar --}}
            <x-card-container>
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Informasi Dasar</h2>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Kode dan nama untuk identifikasi diskon</p>
                </div>
                <div class="px-8 py-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Kode Diskon --}}
                        <div>
                            <label for="code" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                Kode Diskon <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <input type="text"
                                       name="code"
                                       id="code"
                                       value="{{ old('code', $discount->code) }}"
                                       class="flex-1 px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all @error('code') border-red-300 @enderror"
                                       placeholder="DISC-XXXXX"
                                       required>
                                <button type="button"
                                        id="generateCodeBtn"
                                        class="px-4 py-4 bg-gray-800 text-white rounded-2xl text-sm font-black hover:bg-gray-900 transition-all active:scale-95">
                                    <i class="fas fa-sync-alt text-xs"></i>
                                </button>
                            </div>
                            @error('code')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nama Diskon --}}
                        <div>
                            <label for="name" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                Nama Diskon <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="{{ old('name', $discount->name) }}"
                                   class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all @error('name') border-red-300 @enderror"
                                   placeholder="Contoh: Diskon Akhir Tahun"
                                   required>
                            @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </x-card-container>

            {{-- Tipe Diskon --}}
            <x-card-container>
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Tipe Diskon <span class="text-red-500">*</span></h2>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Pilih mekanisme perhitungan diskon</p>
                </div>
                <div class="px-8 py-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Percentage --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="type" value="percentage" class="peer sr-only"
                                   {{ old('type', $discount->type) == 'percentage' ? 'checked' : '' }}>
                            <div class="block p-5 border border-gray-100 rounded-[2rem] bg-gray-50/50
                                        peer-checked:border-cuan-green peer-checked:bg-cuan-green/5
                                        hover:bg-white hover:shadow-xl hover:shadow-gray-200 transition-all duration-300">
                                <div class="flex flex-col items-center text-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                                        <i class="fas fa-percent text-blue-500 text-lg"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs font-black text-gray-900 uppercase tracking-widest block">Persentase</span>
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest hidden sm:block mt-1">Diskon dalam %</span>
                                    </div>
                                </div>
                            </div>
                        </label>

                        {{-- Fixed --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="type" value="fixed" class="peer sr-only"
                                   {{ old('type', $discount->type) == 'fixed' ? 'checked' : '' }}>
                            <div class="block p-5 border border-gray-100 rounded-[2rem] bg-gray-50/50
                                        peer-checked:border-cuan-green peer-checked:bg-cuan-green/5
                                        hover:bg-white hover:shadow-xl hover:shadow-gray-200 transition-all duration-300">
                                <div class="flex flex-col items-center text-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                                        <i class="fas fa-money-bill text-cuan-green text-lg"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs font-black text-gray-900 uppercase tracking-widest block">Fixed</span>
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest hidden sm:block mt-1">Nominal tetap (Rp)</span>
                                    </div>
                                </div>
                            </div>
                        </label>

                        {{-- Buy X Get Y --}}
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="type" value="buy_x_get_y" class="peer sr-only"
                                   {{ old('type', $discount->type) == 'buy_x_get_y' ? 'checked' : '' }}>
                            <div class="block p-5 border border-gray-100 rounded-[2rem] bg-gray-50/50
                                        peer-checked:border-cuan-green peer-checked:bg-cuan-green/5
                                        hover:bg-white hover:shadow-xl hover:shadow-gray-200 transition-all duration-300">
                                <div class="flex flex-col items-center text-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                                        <i class="fas fa-gift text-purple-500 text-lg"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs font-black text-gray-900 uppercase tracking-widest block">Buy X Get Y</span>
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest hidden sm:block mt-1">Beli dapat gratis</span>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </x-card-container>

            {{-- Detail Diskon (Dinamis) --}}
            <div id="percentageFixedCard" style="display:none;">
                <x-card-container>
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Detail Diskon</h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Nilai dan ketentuan diskon</p>
                    </div>
                    <div class="px-8 py-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="value" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                    <span id="valueLabel">Nilai Diskon</span> <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number"
                                           name="value"
                                           id="value"
                                           value="{{ old('value', $discount->value) }}"
                                           step="0.01"
                                           min="0"
                                           class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all @error('value') border-red-300 @enderror"
                                           placeholder="0">
                                    <span id="valueUnit" class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-gray-500 font-bold"></span>
                                </div>
                                @error('value')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="max_discount" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                    Maksimal Diskon <span class="text-gray-300">(Opsional)</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs text-gray-500 font-bold">Rp</span>
                                    <input type="number"
                                           name="max_discount"
                                           id="max_discount"
                                           value="{{ old('max_discount', $discount->max_discount) }}"
                                           step="1000"
                                           min="0"
                                           class="w-full pl-10 pr-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all"
                                           placeholder="0">
                                </div>
                                <p class="mt-2 text-[9px] text-gray-400 font-bold uppercase tracking-widest">Kosongkan jika tidak ada batasan.</p>
                            </div>

                            <div>
                                <label for="min_purchase" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                    Minimal Pembelian
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs text-gray-500 font-bold">Rp</span>
                                    <input type="number"
                                           name="min_purchase"
                                           id="min_purchase"
                                           value="{{ old('min_purchase', $discount->min_purchase) }}"
                                           step="1000"
                                           min="0"
                                           class="w-full pl-10 pr-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all"
                                           placeholder="0">
                                </div>
                            </div>

                            <div>
                                <label for="product_id" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                    Pilih Produk <span class="text-red-500">*</span>
                                </label>
                                <select name="product_id"
                                        id="product_id"
                                        required
                                        class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all @error('product_id') border-red-300 @enderror">
                                    <option value="">Pilih Produk</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ old('product_id', $discount->product_id) == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </x-card-container>
            </div>

            {{-- Buy X Get Y Fields --}}
            <div id="buyXGetYCard" style="display:none;">
                <x-card-container>
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Detail Promo</h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Aturan promo beli dapat gratis</p>
                    </div>
                    <div class="px-8 py-8">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="buy_quantity" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                    Beli <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       name="buy_quantity"
                                       id="buy_quantity"
                                       value="{{ old('buy_quantity', $discount->buy_quantity) }}"
                                       min="1"
                                       class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all @error('buy_quantity') border-red-300 @enderror"
                                       placeholder="1">
                                @error('buy_quantity')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="get_quantity" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                    Gratis <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       name="get_quantity"
                                       id="get_quantity"
                                       value="{{ old('get_quantity', $discount->get_quantity) }}"
                                       min="1"
                                       class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all @error('get_quantity') border-red-300 @enderror"
                                       placeholder="1">
                                @error('get_quantity')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="buy_product_id" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                    Produk <span class="text-red-500">*</span>
                                </label>
                                <select name="product_id"
                                        id="buy_product_id"
                                        class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all @error('product_id') border-red-300 @enderror">
                                    <option value="">Pilih Produk</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ old('product_id', $discount->product_id) == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Preview Promo --}}
                        <div class="mt-6 p-5 bg-cuan-green/5 border border-cuan-green/20 rounded-2xl">
                            <p class="text-[10px] font-black uppercase tracking-widest text-cuan-green">
                                Preview: <span id="promoPreview" class="text-sm normal-case tracking-normal font-bold text-gray-900 ml-1">Beli {{ $discount->buy_quantity ?? 1 }} Gratis {{ $discount->get_quantity ?? 1 }}</span>
                            </p>
                        </div>
                    </div>
                </x-card-container>
            </div>

            {{-- Periode & Batasan --}}
            <x-card-container>
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Periode & Batasan</h2>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Masa berlaku dan batas penggunaan diskon</p>
                </div>
                <div class="px-8 py-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="start_date" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                Tanggal Mulai <span class="text-gray-300">(Opsional)</span>
                            </label>
                            <input type="datetime-local"
                                   name="start_date"
                                   id="start_date"
                                   value="{{ old('start_date', $discount->start_date ? $discount->start_date->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all">
                        </div>

                        <div>
                            <label for="end_date" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                Tanggal Berakhir <span class="text-gray-300">(Opsional)</span>
                            </label>
                            <input type="datetime-local"
                                   name="end_date"
                                   id="end_date"
                                   value="{{ old('end_date', $discount->end_date ? $discount->end_date->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all @error('end_date') border-red-300 @enderror">
                            @error('end_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="usage_limit" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                                Batas Penggunaan <span class="text-gray-300">(Opsional)</span>
                            </label>
                            <input type="number"
                                   name="usage_limit"
                                   id="usage_limit"
                                   value="{{ old('usage_limit', $discount->usage_limit) }}"
                                   min="1"
                                   class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all"
                                   placeholder="Tidak terbatas">
                            <p class="mt-2 text-[9px] text-gray-400 font-bold uppercase tracking-widest">Kosongkan untuk tidak ada batasan.</p>
                        </div>
                    </div>
                </div>
            </x-card-container>

            {{-- Pengaturan Diskon --}}
            <x-card-container>
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Pengaturan Diskon</h2>
                </div>
                <div class="px-8 py-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        {{-- Toggle Voucher --}}
                        <label class="flex items-center gap-4 cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox"
                                       name="is_voucher"
                                       id="is_voucher"
                                       value="1"
                                       {{ old('is_voucher', $discount->is_voucher) ? 'checked' : '' }}
                                       @change="isVoucher = $event.target.checked"
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cuan-green"></div>
                            </div>
                            <div>
                                <span class="text-xs font-black text-gray-900 uppercase tracking-widest">Gunakan Voucher</span>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Jadikan sebagai kode kupon</p>
                            </div>
                        </label>

                        {{-- Toggle Public Voucher --}}
                        <label class="flex items-center gap-4 cursor-pointer group" x-show="isVoucher" x-cloak x-transition>
                            <div class="relative">
                                <input type="checkbox"
                                       name="is_public"
                                       id="is_public"
                                       value="1"
                                       {{ old('is_public', $discount->is_public) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                            </div>
                            <div>
                                <span class="text-xs font-black text-gray-900 uppercase tracking-widest">Voucher Publik</span>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Bisa diklaim semua pembeli</p>
                            </div>
                        </label>

                        {{-- Toggle Active --}}
                        <label class="flex items-center gap-4 cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox"
                                       name="is_active"
                                       id="is_active"
                                       value="1"
                                       {{ old('is_active', $discount->is_active) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cuan-green"></div>
                            </div>
                            <div>
                                <span class="text-xs font-black text-gray-900 uppercase tracking-widest">Aktifkan Diskon</span>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Tampilkan diskon di outlet</p>
                            </div>
                        </label>
                    </div>
                </div>
            </x-card-container>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-4 pb-8">
                <a href="{{ route('discounts.show', $discount->id) }}"
                   class="px-8 py-4 bg-white border border-gray-200 text-gray-600 rounded-2xl font-bold text-sm hover:bg-gray-50 transition-all active:scale-95">
                    Batal
                </a>
                <button type="submit"
                        class="px-8 py-4 bg-cuan-green text-white rounded-2xl font-black text-sm hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const percentageFixedCard = document.getElementById('percentageFixedCard');
    const buyXGetYCard = document.getElementById('buyXGetYCard');
    const valueLabel = document.getElementById('valueLabel');
    const valueUnit = document.getElementById('valueUnit');
    const valueInput = document.getElementById('value');
    const generateCodeBtn = document.getElementById('generateCodeBtn');
    const codeInput = document.getElementById('code');
    const buyQuantityInput = document.getElementById('buy_quantity');
    const getQuantityInput = document.getElementById('get_quantity');
    const promoPreview = document.getElementById('promoPreview');

    // Init
    updateFormFields();

    typeRadios.forEach(radio => {
        radio.addEventListener('change', updateFormFields);
    });

    function updateFormFields() {
        const checked = document.querySelector('input[name="type"]:checked');
        if (!checked) return;
        const selectedType = checked.value;

        percentageFixedCard.style.display = 'none';
        toggleFields(percentageFixedCard, false);
        buyXGetYCard.style.display = 'none';
        toggleFields(buyXGetYCard, false);

        if (selectedType === 'percentage') {
            percentageFixedCard.style.display = 'block';
            toggleFields(percentageFixedCard, true);
            valueLabel.textContent = 'Persentase Diskon';
            valueUnit.textContent = '%';
            valueInput.placeholder = '0 - 100';
            valueInput.max = '100';
        } else if (selectedType === 'fixed') {
            percentageFixedCard.style.display = 'block';
            toggleFields(percentageFixedCard, true);
            valueLabel.textContent = 'Nominal Diskon';
            valueUnit.textContent = 'Rp';
            valueInput.placeholder = '0';
            valueInput.removeAttribute('max');
        } else if (selectedType === 'buy_x_get_y') {
            buyXGetYCard.style.display = 'block';
            toggleFields(buyXGetYCard, true);
            updatePromoPreview();
        }
    }

    function toggleFields(container, isEnabled) {
        container.querySelectorAll('input, select, textarea').forEach(field => {
            field.disabled = !isEnabled;
        });
    }

    // Generate code (with confirmation)
    generateCodeBtn.addEventListener('click', function() {
        Swal.fire({
            title: 'Generate Kode Baru?',
            text: 'Kode lama akan diganti dengan kode baru.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#658C58',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Generate',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-[2rem] border-none shadow-2xl',
                title: 'font-black text-gray-900',
                confirmButton: 'rounded-xl px-6 py-3 font-bold text-sm',
                cancelButton: 'rounded-xl px-6 py-3 font-bold text-sm'
            }
        }).then((result) => {
            if (!result.isConfirmed) return;

            generateCodeBtn.disabled = true;
            generateCodeBtn.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i>';

            fetch('{{ route("discounts.generate-code") }}')
                .then(response => response.json())
                .then(data => {
                    codeInput.value = data.code;
                    generateCodeBtn.disabled = false;
                    generateCodeBtn.innerHTML = '<i class="fas fa-sync-alt text-xs"></i>';
                })
                .catch(error => {
                    console.error('Error:', error);
                    generateCodeBtn.disabled = false;
                    generateCodeBtn.innerHTML = '<i class="fas fa-sync-alt text-xs"></i>';
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal generate kode diskon.', confirmButtonColor: '#658C58' });
                });
        });
    });

    // Update promo preview
    function updatePromoPreview() {
        if (!buyQuantityInput || !getQuantityInput || !promoPreview) return;
        promoPreview.textContent = `Beli ${buyQuantityInput.value || 1} Gratis ${getQuantityInput.value || 1}`;
    }

    if (buyQuantityInput && getQuantityInput) {
        buyQuantityInput.addEventListener('input', updatePromoPreview);
        getQuantityInput.addEventListener('input', updatePromoPreview);
    }

    // Session Flash SweetAlert
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: "{{ session('success') }}",
            confirmButtonColor: '#658C58',
            iconColor: '#658C58',
            customClass: {
                popup: 'rounded-[1.5rem] border-0',
                title: 'font-black tracking-tight',
                confirmButton: 'rounded-xl font-black uppercase text-xs tracking-widest px-6 py-3'
            }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: "{{ session('error') }}",
            confirmButtonColor: '#658C58',
            customClass: {
                popup: 'rounded-[1.5rem] border-0',
                title: 'font-black tracking-tight',
                confirmButton: 'rounded-xl font-black uppercase text-xs tracking-widest px-6 py-3'
            }
        });
    @endif
});
</script>
@endpush
@endsection

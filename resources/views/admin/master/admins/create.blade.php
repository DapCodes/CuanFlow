@extends('admin.layouts.app')

@section('title', 'Tambah Admin')
@section('page-title', 'Tambah Administrator')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <a href="{{ route('admin.admins.index') }}" class="hover:text-emerald-600 transition-colors">Admins</a>
</li>
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Tambah Baru</span>
</li>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.admins.index') }}" 
           class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-500 hover:text-emerald-600 hover:border-emerald-100 hover:bg-emerald-50 transition-all">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Tambah Admin Baru</h1>
            <p class="text-sm text-gray-500 mt-0.5 font-medium">Buat akun administrator sistem baru dengan otorisasi penuh</p>
        </div>
    </div>

    <form action="{{ route('admin.admins.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 md:p-8 space-y-6">
                <div class="flex items-center gap-3 pb-4 border-b border-gray-50">
                    <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-sm">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 tracking-tight">Informasi Dasar</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="name" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                                <i class="fas fa-user-circle text-sm"></i>
                            </div>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:ring-4 focus:ring-emerald-500/5 focus:border-emerald-500 focus:bg-white transition-all placeholder:text-gray-300"
                                   placeholder="Masukkan nama lengkap admin">
                        </div>
                        @error('name')
                        <p class="text-[10px] font-bold text-red-500 mt-1 ml-1 uppercase tracking-wider italic">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="space-y-2">
                        <label for="email" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Alamat Email <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                                <i class="fas fa-envelope text-sm"></i>
                            </div>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                   class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:ring-4 focus:ring-emerald-500/5 focus:border-emerald-500 focus:bg-white transition-all placeholder:text-gray-300"
                                   placeholder="email@contoh.com">
                        </div>
                        @error('email')
                        <p class="text-[10px] font-bold text-red-500 mt-1 ml-1 uppercase tracking-wider italic">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="password" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Kata Sandi <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                                <i class="fas fa-lock text-sm"></i>
                            </div>
                            <input type="password" name="password" id="password" required
                                   class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:ring-4 focus:ring-emerald-500/5 focus:border-emerald-500 focus:bg-white transition-all"
                                   placeholder="Minimal 8 karakter">
                        </div>
                        @error('password')
                        <p class="text-[10px] font-bold text-red-500 mt-1 ml-1 uppercase tracking-wider italic">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="space-y-2">
                        <label for="password_confirmation" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Konfirmasi Sandi <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                                <i class="fas fa-shield-check text-sm"></i>
                            </div>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                   class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:ring-4 focus:ring-emerald-500/5 focus:border-emerald-500 focus:bg-white transition-all"
                                   placeholder="Ulangi kata sandi">
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <label class="flex items-center gap-3 cursor-pointer p-4 bg-emerald-50/50 border border-emerald-100 rounded-xl group hover:bg-emerald-50 transition-all">
                        <input type="checkbox" name="is_active" value="1" class="w-5 h-5 text-emerald-600 border-gray-300 rounded-lg focus:ring-emerald-500 shadow-sm"
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <div>
                            <span class="block text-sm font-bold text-emerald-900">Aktifkan Status Akun</span>
                            <span class="block text-[10px] text-emerald-600 font-medium tracking-tight">Akun akan dapat langsung masuk ke sistem setelah dibuat.</span>
                        </div>
                    </label>
                </div>
                
                <div class="bg-amber-50 border border-amber-100 rounded-2xl p-5 flex items-start gap-4">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-amber-500 shadow-sm">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-amber-900 uppercase tracking-widest mb-1 mt-1">Otoritas System Admin</h4>
                        <p class="text-xs text-amber-700 leading-relaxed font-medium">
                            Akun ini akan otomatis memiliki role <span class="font-bold underline">admin</span> dengan akses penuh ke seluruh fitur dan pengaturan sistem (Global Access). Dan email akan otomatis berstatus <span class="font-bold">Verified</span>.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest pl-1"><span class="text-red-500">*</span> Wajib diisi</span>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.admins.index') }}" 
                       class="px-5 py-2.5 text-xs font-bold text-gray-400 uppercase tracking-widest hover:text-gray-900 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-8 py-3 bg-gray-900 text-white text-xs font-black uppercase tracking-[0.2em] rounded-xl hover:bg-emerald-600 transition-all shadow-xl shadow-gray-200 hover:shadow-emerald-200/50 active:scale-95">
                        Simpan Admin
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

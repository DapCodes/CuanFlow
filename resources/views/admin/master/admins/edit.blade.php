@extends('admin.layouts.app')

@section('title', 'Edit Admin')
@section('page-title', 'Ubah Administrator')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <a href="{{ route('admin.admins.index') }}" class="hover:text-emerald-600 transition-colors">Admins</a>
</li>
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Edit Admin</span>
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
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Admin</h1>
            <p class="text-sm text-gray-500 mt-0.5 font-medium">Ubah informasi akun administrator sistem</p>
        </div>
    </div>

    <form action="{{ route('admin.admins.update', $admin) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 md:p-8 space-y-6">
                <div class="flex items-center gap-3 pb-4 border-b border-gray-50">
                    <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-sm">
                        <i class="fas fa-edit"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 tracking-tight">Perbarui Informasi</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="name" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                                <i class="fas fa-user-circle text-sm"></i>
                            </div>
                            <input type="text" name="name" id="name" value="{{ old('name', $admin->name) }}" required
                                   class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:ring-4 focus:ring-emerald-500/5 focus:border-emerald-500 focus:bg-white transition-all placeholder:text-gray-300">
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
                            <input type="email" name="email" id="email" value="{{ old('email', $admin->email) }}" required
                                   class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:ring-4 focus:ring-emerald-500/5 focus:border-emerald-500 focus:bg-white transition-all placeholder:text-gray-300">
                        </div>
                        @error('email')
                        <p class="text-[10px] font-bold text-red-500 mt-1 ml-1 uppercase tracking-wider italic">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 space-y-4">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-key text-gray-400"></i>
                        <h4 class="text-xs font-black uppercase tracking-[0.2em] text-gray-600">Keamanan (Opsional)</h4>
                    </div>
                    <p class="text-[11px] text-gray-400 font-bold mb-4">Kosongkan jika tidak ingin mengubah kata sandi.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="password" class="text-[11px] font-bold text-gray-500 pl-1">Kata Sandi Baru</label>
                            <input type="password" name="password" id="password"
                                   class="w-full px-5 py-3.5 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:ring-4 focus:ring-emerald-500/5 focus:border-emerald-500 transition-all">
                            @error('password')
                            <p class="text-[10px] font-bold text-red-500 mt-1 ml-1 uppercase tracking-wider italic">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="space-y-2">
                            <label for="password_confirmation" class="text-[11px] font-bold text-gray-500 pl-1">Konfirmasi Sandi Baru</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="w-full px-5 py-3.5 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 focus:ring-4 focus:ring-emerald-500/5 focus:border-emerald-500 transition-all">
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <label class="flex items-center gap-3 cursor-pointer p-4 bg-emerald-50/50 border border-emerald-100 rounded-xl group hover:bg-emerald-50 transition-all">
                        <input type="checkbox" name="is_active" value="1" class="w-5 h-5 text-emerald-600 border-gray-300 rounded-lg focus:ring-emerald-500 shadow-sm"
                               {{ old('is_active', $admin->is_active) ? 'checked' : '' }}>
                        <div>
                            <span class="block text-sm font-bold text-emerald-900">Aktifkan Status Akun</span>
                            <span class="block text-[10px] text-emerald-600 font-medium tracking-tight">Tentukan apakah admin ini diperbolehkan masuk ke sistem.</span>
                        </div>
                    </label>
                </div>
            </div>
            
            <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest pl-1">ID Admin: {{ $admin->id }}</span>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.admins.index') }}" 
                       class="px-5 py-2.5 text-xs font-bold text-gray-400 uppercase tracking-widest hover:text-gray-900 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-8 py-3 bg-gray-900 text-white text-xs font-black uppercase tracking-[0.2em] rounded-xl hover:bg-emerald-600 transition-all shadow-xl shadow-gray-200 hover:shadow-emerald-200/50 active:scale-95">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

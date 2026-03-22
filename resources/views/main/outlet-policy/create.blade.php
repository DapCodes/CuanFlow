@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Tambah Kebijakan - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('outlet-policies.index') }}" class="text-gray-500 hover:text-gray-900 transition-colors tracking-tight">Kebijakan Outlet</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Tambah Baru</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-4xl mx-auto space-y-8">
        
        {{-- Header Section --}}
        <section class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div class="animate-fade-in-down">
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Buat Kebijakan Baru</h1>
                <p class="text-sm text-gray-500 font-medium mt-1">Definisikan SOP atau aturan baru untuk outlet Anda.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('outlet-policies.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-xs font-black uppercase tracking-widest text-gray-400 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </section>

        {{-- Form Container --}}
        <form action="{{ route('outlet-policies.store') }}" method="POST" class="animate-fade-in-up">
            @csrf
            
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 md:p-12 space-y-8">
                    
                    {{-- Title --}}
                    <div class="space-y-3">
                        <label for="title" class="flex items-center text-[11px] font-black uppercase text-gray-400 tracking-[0.2em] gap-2 pl-1">
                            <i class="fas fa-heading opacity-50"></i> Judul Kebijakan 
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="Contoh: Prosedur Pembukaan Kasir"
                            class="w-full px-5 py-4 bg-gray-50 border-gray-100 rounded-xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green focus:bg-white transition-all placeholder:text-gray-300">
                        @error('title') <p class="text-[11px] text-red-500 font-bold mt-1.5 pl-1 italic">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Category --}}
                        <div class="space-y-3">
                            <label for="category" class="flex items-center text-[11px] font-black uppercase text-gray-400 tracking-[0.2em] gap-2 pl-1">
                                <i class="fas fa-tag opacity-50"></i> Kategori
                            </label>
                            <input type="text" name="category" id="category" value="{{ old('category') }}" placeholder="Contoh: Operasional, SDM, Keuangan"
                                class="w-full px-5 py-4 bg-gray-50 border-gray-100 rounded-xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green focus:bg-white transition-all placeholder:text-gray-300">
                            @error('category') <p class="text-[11px] text-red-500 font-bold mt-1.5 pl-1 italic">{{ $message }}</p> @enderror
                        </div>

                        {{-- Quick Hint --}}
                        <div class="bg-gray-50 rounded-2xl p-6 flex items-start gap-4 border border-gray-100">
                            <div class="w-8 h-8 rounded-lg bg-cuan-green text-white flex items-center justify-center shadow-lg shadow-emerald-100">
                                <i class="fas fa-lightbulb text-xs"></i>
                            </div>
                            <div>
                                <h4 class="text-[11px] font-black uppercase tracking-wider text-gray-600 mb-1">Tips Menarik</h4>
                                <p class="text-[10px] text-gray-400 font-medium leading-relaxed">
                                    Gunakan judul yang jelas dan spesifik agar karyawan mudah memahami inti dari kebijakan tersebut.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="space-y-3">
                        <label for="content" class="flex items-center text-[11px] font-black uppercase text-gray-400 tracking-[0.2em] gap-2 pl-1">
                            <i class="fas fa-file-lines opacity-50"></i> Isi Kebijakan (SOP)
                        </label>
                        <textarea name="content" id="content" rows="12" required placeholder="Tuliskan detail prosedur atau aturan di sini..."
                            class="w-full px-5 py-4 bg-gray-50 border-gray-100 rounded-xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green focus:bg-white transition-all placeholder:text-gray-300 resize-none">{{ old('content') }}</textarea>
                        @error('content') <p class="text-[11px] text-red-500 font-bold mt-1.5 pl-1 italic">{{ $message }}</p> @enderror
                    </div>

                    {{-- Action Buttons --}}
                    <div class="pt-6 flex flex-col sm:flex-row items-center gap-4 border-t border-gray-100">
                        <button type="submit" class="w-full sm:w-auto px-10 py-4 bg-cuan-green text-white rounded-xl shadow-lg shadow-emerald-100 hover:bg-cuan-dark transition-all text-xs font-black uppercase tracking-widest active:scale-95 duration-200">
                            Simpan Kebijakan
                        </button>
                        <a href="{{ route('outlet-policies.index') }}" class="w-full sm:w-auto text-center px-10 py-4 text-xs font-black uppercase tracking-[0.2em] text-gray-400 hover:text-gray-900 transition-colors">
                            Batal
                        </a>
                    </div>

                </div>
            </div>
        </form>

    </div>
</main>

<style>
    @keyframes fade-in-up {
        0% { opacity: 0; transform: translateY(30px) scale(0.98); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }
    .animate-fade-in-up { animation: fade-in-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    
    @keyframes fade-in-down {
        0% { opacity: 0; transform: translateY(-20px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-down { animation: fade-in-down 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
@endsection

@extends('layouts.app')

@section('title', 'Kebijakan Outlet - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Kebijakan Outlet</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-[#f9fafb]" x-data="{ searchQuery: '' }">
    <div class="max-w-6xl mx-auto space-y-8">
        
        {{-- Header Section --}}
        <section class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="animate-fade-in-down">
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Kebijakan & SOP</h1>
                <p class="text-sm text-gray-500 font-medium mt-1">Pusat dokumentasi aturan dan prosedur operasional outlet Anda.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative group min-w-[240px]">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-gray-900 transition-colors"></i>
                    <input type="text" x-model="searchQuery" placeholder="Cari kebijakan..." 
                        class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-gray-900/5 focus:border-gray-900 transition-all shadow-sm">
                </div>
                @can('buat kebijakan outlet')
                <a href="{{ route('outlet-policies.create') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 text-white rounded-2xl shadow-xl shadow-gray-200 hover:bg-black transition-all text-xs font-black uppercase tracking-[0.2em] active:scale-95">
                    <i class="fas fa-plus mr-2"></i>
                    Buat Baru
                </a>
                @endcan
            </div>
        </section>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="px-5 py-4 bg-gray-900 text-white rounded-2xl shadow-lg flex items-center justify-between animate-fade-in-down">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
                        <i class="fas fa-check text-xs"></i>
                    </div>
                    <span class="text-sm font-bold">{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-white/50 hover:text-white"><i class="fas fa-times text-xs"></i></button>
            </div>
        @endif

        {{-- Interactive Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 auto-rows-fr">
            @forelse($policies as $policy)
                <div class="group bg-white border border-gray-200 rounded-[2rem] p-8 shadow-sm hover:shadow-2xl hover:shadow-gray-200 transition-all duration-500 flex flex-col justify-between animate-fade-in-up"
                     x-show="searchQuery === '' || '{{ strtolower($policy->title) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($policy->category) }}'.includes(searchQuery.toLowerCase())">
                    
                    <div>
                        <div class="flex items-start justify-between mb-6">
                            <span class="px-4 py-1.5 bg-gray-50 text-[10px] font-black uppercase tracking-widest text-gray-400 rounded-full border border-gray-100 group-hover:bg-gray-900 group-hover:text-white group-hover:border-gray-900 transition-all duration-300">
                                {{ $policy->category ?? 'Umum' }}
                            </span>
                            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                @can('edit kebijakan outlet')
                                <a href="{{ route('outlet-policies.edit', $policy->id) }}" class="p-2 text-gray-400 hover:text-gray-900 transition-colors">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                @endcan
                                @can('hapus kebijakan outlet')
                                <form action="{{ route('outlet-policies.destroy', $policy->id) }}" method="POST" onsubmit="return confirm('Hapus kebijakan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </div>

                        <h3 class="text-xl font-black text-gray-900 mb-3 tracking-tight group-hover:text-gray-700 transition-colors">
                            {{ $policy->title }}
                        </h3>
                        
                        <p class="text-sm text-gray-500 leading-relaxed font-medium line-clamp-3 mb-6">
                            {{ Str::limit($policy->content, 150) }}
                        </p>
                    </div>

                    <div class="pt-6 border-t border-gray-50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 text-[10px] font-black">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black uppercase text-gray-900 tracking-wider">Disusun Oleh</span>
                                <span class="text-[10px] font-medium text-gray-400">{{ $policy->creator->name ?? 'Sistem' }}</span>
                            </div>
                        </div>
                        <a href="{{ route('outlet-policies.show', $policy->id) }}" class="w-10 h-10 rounded-2xl bg-gray-50 text-gray-900 flex items-center justify-center hover:bg-gray-900 hover:text-white transition-all group/btn">
                            <i class="fas fa-arrow-right text-xs group-hover/btn:translate-x-0.5 transition-transform"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 flex flex-col items-center justify-center bg-gray-50/50 rounded-[3rem] border-2 border-dashed border-gray-200">
                    <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center text-gray-200 text-4xl shadow-sm mb-6">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-2">Belum Ada Kebijakan</h3>
                    <p class="text-sm text-gray-500 font-medium max-w-xs text-center px-6">Mulailah dengan membuat kebijakan pertama Anda untuk menstandarisasi operasional outlet.</p>
                </div>
            @endforelse
        </div>

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

@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Kebijakan Outlet - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Kebijakan Outlet</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50" x-data="{ searchQuery: '' }">
    <div class="max-w-7xl mx-auto space-y-6">
        
        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Kebijakan & SOP
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Pusat dokumentasi aturan dan prosedur operasional outlet Anda.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @can('buat kebijakan outlet')
                <a href="{{ route('outlet-policies.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-5 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                    <i class="fas fa-plus"></i>
                    <span>Buat Baru</span>
                </a>
                @endcan
            </div>
        </section>

        {{-- RINGKASAN STATISTIK --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Kebijakan</p>
                <p class="mt-2 text-2xl font-black text-gray-900">{{ number_format($policies->count(), 0) }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Kategori Aktif</p>
                <p class="mt-2 text-2xl font-black text-cuan-green">{{ $policies->pluck('category')->unique()->count() }}</p>
            </div>
            @php
                $recentCount = $policies->where('created_at', '>=', now()->subDays(7))->count();
            @endphp
            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Baru (7 Hari)</p>
                <p class="mt-2 text-2xl font-black text-emerald-600">{{ $recentCount }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Status Sistem</p>
                <div class="mt-2 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-cuan-green animate-pulse"></span>
                    <span class="text-sm font-black text-gray-900 uppercase tracking-tighter">Terhubung</span>
                </div>
            </div>
        </section>

        {{-- SEARCH & FILTER --}}
        <section class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="relative group w-full md:w-96">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-cuan-green transition-colors"></i>
                <input type="text" x-model="searchQuery" placeholder="Cari kebijakan atau kategori..." 
                    class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all shadow-sm">
            </div>
            <div class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                <i class="fas fa-info-circle text-cuan-green"></i>
                <span>Klik Kartu Untuk Detail</span>
            </div>
        </section>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="px-5 py-4 bg-cuan-green text-white rounded-2xl shadow-xl shadow-emerald-100 flex items-center justify-between animate-fade-in-down">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
                        <i class="fas fa-check text-xs"></i>
                    </div>
                    <span class="text-xs font-black uppercase tracking-widest">{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-white/50 hover:text-white"><i class="fas fa-times text-xs"></i></button>
            </div>
        @endif

        {{-- Interactive Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 auto-rows-fr">
            @forelse($policies as $policy)
                <div class="group bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-xl hover:shadow-emerald-100/50 transition-all duration-300 flex flex-col justify-between animate-fade-in-up"
                     x-show="searchQuery === '' || '{{ strtolower($policy->title) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($policy->category) }}'.includes(searchQuery.toLowerCase())">
                    
                    <div>
                        <div class="flex items-start justify-between mb-4">
                            <span class="px-3 py-1 bg-emerald-50 text-[10px] font-black uppercase tracking-widest text-emerald-600 rounded-lg border border-emerald-100 group-hover:bg-cuan-green group-hover:text-white group-hover:border-cuan-green transition-all">
                                {{ $policy->category ?? 'Umum' }}
                            </span>
                            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                @can('edit kebijakan outlet')
                                <a href="{{ route('outlet-policies.edit', $policy->id) }}" class="p-2 text-gray-400 hover:text-gray-900 transition-colors">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                @endcan
                                @can('hapus kebijakan outlet')
                                <form action="{{ route('outlet-policies.destroy', $policy->id) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this)" class="p-2 text-gray-400 hover:text-red-600 transition-colors">
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
                        <a href="{{ route('outlet-policies.show', $policy->id) }}" class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 flex items-center justify-center hover:bg-cuan-green hover:text-white transition-all group/btn">
                            <i class="fas fa-arrow-right text-xs group-hover/btn:translate-x-0.5 transition-transform"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 flex flex-col items-center justify-center bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="w-20 h-20 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-300 text-3xl mb-6">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h3 class="text-lg font-black text-gray-900 mb-2 uppercase tracking-tighter">Belum Ada Kebijakan</h3>
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

@push('scripts')
<script>
    function confirmDelete(button) {
        const form = button.closest('form');
        Swal.fire({
            title: 'Hapus kebijakan ini?',
            text: 'Dokumentasi kebijakan ini akan dihapus secara permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                container: 'rounded-2xl',
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl font-bold px-6 py-3',
                cancelButton: 'rounded-xl font-bold px-6 py-3'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
</script>
@endpush

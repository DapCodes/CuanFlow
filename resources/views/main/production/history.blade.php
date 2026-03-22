@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Riwayat Produksi - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('production.index') }}" class="text-gray-500 hover:text-cuan-green transition-colors font-medium">Produksi</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Riwayat</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Riwayat Produksi
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Daftar semua siklus produksi yang telah direncanakan, selesai, maupun dibatalkan.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('production.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-5 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                    <i class="fas fa-plus text-xs"></i>
                    <span>Produksi Baru</span>
                </a>
            </div>
        </section>

        {{-- KONTEN UTAMA --}}
        <x-card-container>
            {{-- Toolbar: Filter --}}
            <div class="px-6 py-5 border-b border-gray-100 bg-white">
                <div class="flex flex-wrap items-center gap-3">
                     <a href="{{ route('production.history') }}" 
                        class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ !$status ? 'bg-gray-900 text-white shadow-lg shadow-gray-900/10' : 'bg-gray-50 text-gray-400 border border-gray-100' }}">
                        Semua Status
                     </a>
                     <a href="{{ route('production.history', ['status' => 'planned']) }}" 
                        class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $status === 'planned' ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/10' : 'bg-gray-50 text-gray-400 border border-gray-100' }}">
                        Direncanakan
                     </a>
                     <a href="{{ route('production.history', ['status' => 'completed']) }}" 
                        class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $status === 'completed' ? 'bg-cuan-green text-white shadow-lg shadow-cuan-green/10' : 'bg-gray-50 text-gray-400 border border-gray-100' }}">
                        Selesai
                     </a>
                     <a href="{{ route('production.history', ['status' => 'cancelled']) }}" 
                        class="px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $status === 'cancelled' ? 'bg-red-500 text-white shadow-lg shadow-red-500/10' : 'bg-gray-50 text-gray-400 border border-gray-100' }}">
                        Dibatalkan
                     </a>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="overflow-x-auto min-h-[400px]">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest border-b border-gray-100">
                        <tr>
                            <th class="px-8 py-4 text-left">Batch & Tanggal</th>
                            <th class="px-8 py-4 text-left">Produk</th>
                            <th class="px-8 py-4 text-right">Jumlah</th>
                            <th class="px-8 py-4 text-left">Oleh</th>
                            <th class="px-8 py-4 text-center">Status</th>
                            <th class="px-8 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 bg-white">
                        @forelse($productions as $p)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-8 py-5">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-gray-900 font-mono tracking-tighter group-hover:text-cuan-green transition-colors">#{{ $p->batch_number }}</span>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">{{ $p->created_at->format('d M Y, H:i') }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-gray-900">{{ $p->product->name }}</span>
                                    <span class="text-[9px] font-black uppercase text-gray-300 tracking-tighter mt-0.5">{{ $p->product->code }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-right whitespace-nowrap">
                                <span class="text-sm font-black text-gray-900">{{ number_format($p->actual_quantity > 0 ? $p->actual_quantity : $p->planned_quantity, 2) }}</span>
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">{{ $p->product->unit->name }}</span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 border border-gray-100">
                                        <i class="fas fa-user-circle text-[10px]"></i>
                                    </div>
                                    <span class="text-xs font-bold text-gray-600 truncate max-w-[120px]">{{ $p->createdBy->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-center">
                                @php
                                    $statusConfig = [
                                        'planned' => ['class' => 'bg-amber-50 text-amber-600 border-amber-100', 'text' => 'Direncanakan'],
                                        'in_progress' => ['class' => 'bg-blue-50 text-blue-600 border-blue-100', 'text' => 'Diproses'],
                                        'completed' => ['class' => 'bg-cuan-green/10 text-cuan-green border-cuan-green/20', 'text' => 'Selesai'],
                                        'cancelled' => ['class' => 'bg-red-50 text-red-500 border-red-100', 'text' => 'Batal'],
                                    ];
                                    $config = $statusConfig[$p->status] ?? $statusConfig['planned'];
                                @endphp
                                <span class="inline-flex items-center px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest border {{ $config['class'] }}">
                                    {{ $config['text'] }}
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('production.show', $p->id) }}"
                                       class="w-10 h-10 flex items-center justify-center rounded-2xl bg-gray-50 text-gray-400 hover:bg-cuan-green hover:text-white transition-all active:scale-95 shadow-sm border border-gray-100 group-hover:border-cuan-green/20"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-8 py-20 text-center">
                                <div class="max-w-xs mx-auto opacity-30">
                                    <i class="fas fa-history text-4xl text-gray-300 mb-4 block"></i>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Belum ada riwayat produksi.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($productions->hasPages())
            <div class="px-8 py-6 border-t border-gray-50 bg-gray-50/20">
                {{ $productions->links() }}
            </div>
            @endif
        </x-card-container>

    </div>
</main>
@endsection

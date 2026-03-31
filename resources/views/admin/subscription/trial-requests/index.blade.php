@extends('admin.layouts.app')

@section('title', 'Permintaan Trial')
@section('page-title', 'Verifikasi Bisnis')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Permintaan Trial</span>
</li>
@endsection

@section('content')
<div class="px-4 lg:px-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-id-card-clip text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight uppercase">Permintaan Trial</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium italic">Verifikasi pendaftaran akun bisnis dan aktivasi uji coba gratis</p>
            </div>
        </div>
        
        <!-- Filter Tabs -->
        <div class="bg-white rounded-2xl p-1.5 flex gap-1.5 border border-gray-200 shadow-sm">
            @php
                $tabClasses = "px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 active:scale-95 whitespace-nowrap";
            @endphp
            <a href="{{ route('admin.subscription-trial-requests.index', ['status' => 'pending']) }}" 
               class="{{ $tabClasses }} {{ $status == 'pending' ? 'bg-amber-100 text-amber-700 shadow-sm shadow-amber-100/50' : 'text-gray-400 hover:bg-gray-50 hover:text-gray-600' }}">
               Menunggu
            </a>
            <a href="{{ route('admin.subscription-trial-requests.index', ['status' => 'approved']) }}" 
               class="{{ $tabClasses }} {{ $status == 'approved' ? 'bg-emerald-100 text-emerald-700 shadow-sm shadow-emerald-100/50' : 'text-gray-400 hover:bg-gray-50 hover:text-gray-600' }}">
               Disetujui
            </a>
            <a href="{{ route('admin.subscription-trial-requests.index', ['status' => 'rejected']) }}" 
               class="{{ $tabClasses }} {{ $status == 'rejected' ? 'bg-rose-100 text-rose-700 shadow-sm shadow-rose-100/50' : 'text-gray-400 hover:bg-gray-50 hover:text-gray-600' }}">
               Ditolak
            </a>
        </div>
    </div>

    {{-- KONTEN UTAMA --}}
    <x-card-container class="!p-0 overflow-hidden border border-gray-200 shadow-sm bg-white rounded-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left">Pelanggan & Outlet</th>
                        <th class="px-6 py-4 text-left">Tipe Bisnis</th>
                        <th class="px-6 py-4 text-left">Tanggal</th>
                        <th class="px-6 py-4 text-left">Berkas</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($requests as $req)
                    <tr class="hover:bg-gray-50 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold">
                                    {{ substr($req->user->name ?? '?', 0, 1) }}
                                </div> {{-- Close icon wrapper --}}
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 leading-tight uppercase font-black uppercase tracking-tight">{{ $req->user->name ?? 'Unknown User' }}</div>
                                    <div class="text-xs text-gray-500 italic mt-0.5">{{ $req->outlet_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                            {{ $req->business_type ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-[10px] font-black uppercase tracking-widest text-emerald-500 italic">
                            {{ $req->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex space-x-2">
                                @if($req->photo_store_front_path)
                                    <a href="{{ Storage::url($req->photo_store_front_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 underline text-[10px] font-black italic">Depan</a>
                                @endif
                                @if($req->photo_products_path)
                                    <a href="{{ Storage::url($req->photo_products_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 underline text-[10px] font-black italic">Produk</a>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @php
                                $badgeStyle = match($req->status) {
                                    'approved' => 'bg-emerald-50 text-emerald-600 border-emerald-100 shadow-emerald-100/50',
                                    'rejected' => 'bg-rose-50 text-rose-600 border-rose-100 shadow-rose-100/50',
                                    'pending' => 'bg-amber-50 text-amber-600 border-amber-100 shadow-amber-100/50',
                                    default => 'bg-gray-50 text-gray-400 border-gray-200'
                                };
                            @endphp
                            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest {{ $badgeStyle }} border shadow-sm">
                                {{ strtoupper($req->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                             <a href="{{ route('admin.subscription-trial-requests.show', $req) }}" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-200 hover:bg-gray-900 hover:text-white hover:border-gray-900 text-gray-600 text-[11px] font-bold rounded-lg transition-all">
                                <i class="fas fa-search-dollar text-[10px]"></i>
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                             <div class="flex flex-col items-center gap-2">
                                <i class="fas fa-id-card-clip text-4xl text-gray-200"></i>
                                <p class="font-medium">Belum ada permintaan trial</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $requests->links() }}
        </div>
    </x-card-container>
</div>
@endsection

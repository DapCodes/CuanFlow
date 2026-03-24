@extends('admin.layouts.app')

@section('title', 'Manajemen Outlet')
@section('page-title', 'Manajemen Outlet')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Manajemen Outlet</span>
</li>
@endsection

@section('content')
<div class="px-4 lg:px-6 space-y-6">
    <!-- Header -->
    <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-store text-lg"></i>
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900 tracking-tight">Manajemen Outlet</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium">Monitoring seluruh cabang outlet Anda</p>
            </div>
        </div>
    </section>

    <x-card-container>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left">Outlet</th>
                        <th class="px-6 py-4 text-left">Owner</th>
                        <th class="px-6 py-4 text-center">Statistik</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($outlets as $outlet)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center text-teal-600 border border-teal-100 shadow-sm">
                                    <i class="fas fa-store text-xs"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 leading-tight">{{ $outlet->name }}</p>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">{{ Str::limit($outlet->address, 30) ?? 'No address' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-[11px] font-bold text-gray-900">{{ $outlet->owner->name ?? 'N/A' }}</div>
                            <div class="text-[10px] font-medium text-gray-400">{{ $outlet->owner->email ?? '' }}</div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex justify-center gap-4">
                                <div class="text-center" title="Penjualan">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Sales</p>
                                    <p class="text-[11px] font-bold text-gray-900">{{ number_format($outlet->sales_count) }}</p>
                                </div>
                                <div class="text-center" title="Produk">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Prod</p>
                                    <p class="text-[11px] font-bold text-gray-900">{{ number_format($outlet->products_count) }}</p>
                                </div>
                                <div class="text-center" title="Karyawan">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">Staff</p>
                                    <p class="text-[11px] font-bold text-gray-900">{{ number_format($outlet->users_count) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <form action="{{ route('admin.outlets.toggle-status', $outlet) }}" method="POST">
                                @csrf
                                <button type="submit" class="focus:outline-none transition-transform active:scale-95">
                                    @if($outlet->is_active)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-green-50 text-green-600 border border-green-100">Aktif</span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-red-50 text-red-600 border border-red-100">Nonaktif</span>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.outlets.show', $outlet) }}" 
                                   class="w-9 h-9 flex items-center justify-center rounded-xl bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white transition-all active:scale-95 border border-blue-100" title="Detail">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="w-16 h-16 bg-gray-50 border border-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-store-slash text-gray-200 text-2xl"></i>
                            </div>
                            <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">Belum Ada Outlet</h3>
                            <p class="text-[11px] text-gray-500 font-bold uppercase tracking-widest mt-2">Daftar outlet masih kosong.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($outlets->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $outlets->links() }}
        </div>
        @endif
    </x-card-container>
</div>
@endsection

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
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-store text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Manajemen Outlet</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium">Monitoring performa dan pengaturan seluruh cabang outlet</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Outlet</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Owner</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Statistik</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($outlets as $outlet)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center">
                                    <i class="fas fa-store text-teal-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $outlet->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $outlet->address ?? 'No address' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                <p class="text-gray-900 font-medium">{{ $outlet->owner->name ?? 'N/A' }}</p>
                                <p class="text-gray-500 text-xs">{{ $outlet->owner->email ?? '' }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-4 text-xs font-medium">
                                <div class="text-center" title="Penjualan">
                                    <p class="text-gray-500 uppercase">Sales</p>
                                    <p class="text-gray-900">{{ number_format($outlet->sales_count) }}</p>
                                </div>
                                <div class="text-center" title="Produk">
                                    <p class="text-gray-500 uppercase">Prod</p>
                                    <p class="text-gray-900">{{ number_format($outlet->products_count) }}</p>
                                </div>
                                <div class="text-center" title="Karyawan">
                                    <p class="text-gray-500 uppercase">Staff</p>
                                    <p class="text-gray-900">{{ number_format($outlet->users_count) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('admin.outlets.toggle-status', $outlet) }}" method="POST">
                                @csrf
                                <button type="submit" class="focus:outline-none">
                                    @if($outlet->is_active)
                                        <span class="px-2.5 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full hover:bg-green-200 transition-colors">Aktif</span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full hover:bg-red-200 transition-colors">Nonaktif</span>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.outlets.show', $outlet) }}" 
                                   class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-store-slash text-4xl text-gray-300 mb-3"></i>
                            <p>Belum ada outlet yang terdaftar</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($outlets->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $outlets->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

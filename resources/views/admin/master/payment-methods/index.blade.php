@extends('admin.layouts.app')

@section('title', 'Kelola Metode Pembayaran')
@section('page-title', 'Data Master - Metode Pembayaran')

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <span class="text-gray-700">Metode Pembayaran</span>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Kelola Metode Pembayaran</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola bank, e-wallet, dan metode pembayaran lainnya</p>
        </div>
        <a href="{{ route('admin.payment-methods.create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-cuan-dark text-white font-semibold rounded-lg hover:bg-cuan-green transition-colors">
            <i class="fas fa-plus text-sm"></i>
            <span>Tambah Metode</span>
        </a>
    </div>
    
    <!-- Search -->
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" action="{{ route('admin.payment-methods.index') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari metode pembayaran..."
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2.5 bg-cuan-dark text-white font-semibold rounded-lg hover:bg-cuan-green transition-colors">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search'))
                <a href="{{ route('admin.payment-methods.index') }}" class="px-4 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
    
    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Metode Pembayaran</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Kode</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Digunakan Outlet</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($paymentMethods as $method)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($method->icon)
                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden">
                                    <img src="{{ Storage::url($method->icon) }}" alt="{{ $method->name }}" class="w-8 h-8 object-contain">
                                </div>
                                @else
                                <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-credit-card text-blue-600 text-lg"></i>
                                </div>
                                @endif
                                <p class="font-semibold text-gray-900">{{ $method->name }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 text-sm font-mono font-medium bg-gray-100 text-gray-700 rounded">
                                {{ strtoupper($method->code) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm text-gray-600">{{ $method->outlet_payment_links_count }} outlet</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('admin.payment-methods.toggle-status', $method) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="focus:outline-none">
                                    @if($method->is_active)
                                    <span class="px-2.5 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full hover:bg-green-200 transition-colors cursor-pointer">Aktif</span>
                                    @else
                                    <span class="px-2.5 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full hover:bg-red-200 transition-colors cursor-pointer">Nonaktif</span>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.payment-methods.edit', $method) }}" 
                                   class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($method->outlet_payment_links_count == 0)
                                <form action="{{ route('admin.payment-methods.destroy', $method) }}" method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus metode pembayaran ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @else
                                <span class="p-2 text-gray-300 cursor-not-allowed" title="Metode pembayaran sedang digunakan">
                                    <i class="fas fa-lock"></i>
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-credit-card text-4xl text-gray-300 mb-3"></i>
                            <p>Belum ada metode pembayaran</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($paymentMethods->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $paymentMethods->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@extends('admin.layouts.app')

@section('title', 'Detail Metode Pembayaran')
@section('page-title', 'Detail Metode Pembayaran')

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <a href="{{ route('admin.payment-methods.index') }}" class="text-gray-500 hover:text-gray-700">Metode Pembayaran</a>
</li>
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <span class="text-gray-700">{{ $paymentMethod->name }}</span>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            @if($paymentMethod->icon)
            <div class="w-16 h-16 rounded-xl bg-gray-100 flex items-center justify-center overflow-hidden">
                <img src="{{ Storage::url($paymentMethod->icon) }}" alt="{{ $paymentMethod->name }}" class="w-12 h-12 object-contain">
            </div>
            @else
            <div class="w-16 h-16 rounded-xl bg-blue-100 flex items-center justify-center">
                <i class="fas fa-credit-card text-blue-600 text-2xl"></i>
            </div>
            @endif
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $paymentMethod->name }}</h2>
                <p class="text-sm text-gray-500 font-mono">Kode: {{ strtoupper($paymentMethod->code) }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.payment-methods.edit', $paymentMethod) }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-edit text-sm"></i>
                <span>Edit</span>
            </a>
            <a href="{{ route('admin.payment-methods.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left text-sm"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>
    
    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Status</p>
            @if($paymentMethod->is_active)
            <span class="px-2.5 py-1 text-sm font-medium bg-green-100 text-green-700 rounded-full">Aktif</span>
            @else
            <span class="px-2.5 py-1 text-sm font-medium bg-red-100 text-red-700 rounded-full">Nonaktif</span>
            @endif
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Outlet Menggunakan</p>
            <p class="text-lg font-bold text-gray-900">{{ $paymentMethod->outletPaymentLinks->count() }} outlet</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Dibuat Pada</p>
            <p class="text-sm font-medium text-gray-900">{{ $paymentMethod->created_at->format('d M Y, H:i') }}</p>
        </div>
    </div>
    
    <!-- Outlets Using This Payment Method -->
    @if($paymentMethod->outletPaymentLinks->count() > 0)
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Outlet yang Menggunakan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Outlet</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No. Rekening</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Atas Nama</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($paymentMethod->outletPaymentLinks as $link)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center">
                                    <i class="fas fa-store text-teal-600"></i>
                                </div>
                                <span class="font-medium text-gray-900">{{ $link->outlet->name ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-mono text-gray-700">{{ $link->account_number ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-700">{{ $link->account_name ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($link->is_active)
                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Aktif</span>
                            @else
                            <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Nonaktif</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-200 p-8 text-center">
        <i class="fas fa-store-slash text-4xl text-gray-300 mb-3"></i>
        <p class="text-gray-500">Belum ada outlet yang menggunakan metode pembayaran ini</p>
    </div>
    @endif
    
    <!-- Timestamps -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-500">Dibuat pada:</p>
                <p class="font-medium text-gray-900">{{ $paymentMethod->created_at->format('d M Y, H:i') }}</p>
            </div>
            <div>
                <p class="text-gray-500">Terakhir diperbarui:</p>
                <p class="font-medium text-gray-900">{{ $paymentMethod->updated_at->format('d M Y, H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

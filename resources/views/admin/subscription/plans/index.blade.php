@extends('admin.layouts.app')

@section('title', 'Durasi Langganan')
@section('page-title', 'Opsi Durasi & Harga Langganan')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Opsi Durasi</span>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 shadow-sm shadow-indigo-100/50">
                <i class="fas fa-calendar-days text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Opsi Durasi</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium">Atur paket harga berdasarkan durasi bulan untuk setiap tier</p>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.subscription-plans.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl hover:bg-indigo-600 transition-all duration-200 shadow-sm">
                <i class="fas fa-plus text-xs"></i>
                <span>Tambah Opsi Durasi</span>
            </a>
        </div>
    </div>
    
    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tier</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Durasi</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Harga</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Diskon</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($plans as $plan)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs">
                                    {{ substr($plan->tier->name, 0, 1) }}
                                </div>
                                <span class="font-bold text-gray-900">{{ $plan->tier->display_name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($plan->is_unlimited)
                                <span class="text-sm font-semibold text-gray-900">Selamanya (Unlimited)</span>
                            @else
                                <span class="text-sm font-semibold text-gray-900">{{ $plan->duration_months }} Bulan</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-gray-900">Rp {{ number_format($plan->price, 0, ',', '.') }}</p>
                            <p class="text-[10px] text-gray-400">Total bayar bersih</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($plan->discount_percentage > 0)
                                <span class="px-2 py-1 text-xs font-bold bg-amber-100 text-amber-700 rounded-lg">
                                    {{ (int)$plan->discount_percentage }}% Off
                                </span>
                            @else
                                <span class="text-gray-300 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($plan->is_active)
                                <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-700 rounded-lg">
                                    Aktif
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-700 rounded-lg">
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.subscription-plans.edit', ['plan' => $plan->id]) }}" 
                                   class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.subscription-plans.destroy', ['plan' => $plan->id]) }}" method="POST" 
                                      onsubmit="return confirm('Yakin ingin menghapus opsi durasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fas fa-calendar-xmark text-4xl text-gray-200"></i>
                                <p class="font-medium">Belum ada opsi durasi</p>
                                <a href="{{ route('admin.subscription-plans.create') }}" class="text-indigo-600 hover:underline text-sm">Buat sekarang</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

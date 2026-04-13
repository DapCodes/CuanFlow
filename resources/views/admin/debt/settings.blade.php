@extends('admin.layouts.app')

@section('title', 'Pengaturan Piutang')
@section('page-title', 'Konfigurasi Jatuh Tempo & Denda')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Pengaturan Piutang</span>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="px-8 py-6 bg-gray-50/50 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Pengaturan Jatuh Tempo</h2>
            <p class="text-sm text-gray-500 mt-1">Konfigurasi denda untuk pelanggan yang terlambat membayar piutang</p>
        </div>

        <form action="{{ route('admin.debt-settings.update') }}" method="POST" class="p-8 space-y-8">
            @csrf
            
            <div class="space-y-6">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2">Kebijakan Denda</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-gray-700">Persentase Denda (%)</label>
                        <div class="relative">
                            <input type="number" name="late_fee_percentage" value="{{ old('late_fee_percentage', $lateFeePercentage) }}" required step="0.01" min="0" max="100"
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">%</div>
                        </div>
                        <p class="text-[10px] text-gray-500 italic mt-1">Denda akan ditambahkan ke total tagihan jika melewati tanggal jatuh tempo.</p>
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2 text-amber-600 font-medium text-xs">
                    <i class="fas fa-triangle-exclamation"></i>
                    <span>Denda dihitung dari sisa utang yang belum terbayar.</span>
                </div>
                <button type="submit" 
                        class="px-8 py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-emerald-600 transition-all shadow-lg hover:shadow-emerald-100 transform hover:-translate-y-0.5">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

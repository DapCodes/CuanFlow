@extends('layouts.app')

@section('title', 'Detail Diskon - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('discounts.index') }}" class="text-gray-500 hover:text-gray-700">Kelola Diskon</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Detail Diskon</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4">
    <div class="max-w-7xl mx-auto">
        
        @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg" role="alert">
            <div class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                <div class="flex-1">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        <x-card-container>
            <!-- Header -->
            <div class="bg-gradient-to-br from-red-400 to-pink-500 p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-white flex items-center">
                            <i class="fas fa-tag mr-3"></i>
                            {{ $discount->name }}
                        </h2>
                        <p class="text-sm text-red-50 mt-1">Kode: {{ $discount->code }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        @php
                            $isExpired = $discount->end_date && $discount->end_date->lt(now());
                            $isActive = $discount->is_active && !$isExpired;
                        @endphp
                        @if($isExpired)
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Kadaluarsa
                        </span>
                        @elseif($discount->is_active)
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-2"></i>
                            Aktif
                        </span>
                        @else
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-gray-100 text-gray-800">
                            <i class="fas fa-pause-circle mr-2"></i>
                            Tidak Aktif
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-6">
                <!-- Quick Actions -->
                <div class="flex items-center justify-end gap-3 mb-6">
                    <a href="{{ route('discounts.edit', $discount->id) }}" 
                       class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors font-medium">
                        <i class="fas fa-edit mr-2"></i>
                        Edit
                    </a>
                    <form action="{{ route('discounts.toggle-status', $discount->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 {{ $discount->is_active ? 'bg-gray-500' : 'bg-green-500' }} text-white rounded-lg hover:{{ $discount->is_active ? 'bg-gray-600' : 'bg-green-600' }} transition-colors font-medium">
                            <i class="fas fa-{{ $discount->is_active ? 'pause' : 'play' }} mr-2"></i>
                            {{ $discount->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                    <form action="{{ route('discounts.destroy', $discount->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus diskon ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors font-medium">
                            <i class="fas fa-trash mr-2"></i>
                            Hapus
                        </button>
                    </form>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Info -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Tipe Diskon -->
                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-layer-group text-red-500 mr-2"></i>
                                Tipe Diskon
                            </h3>
                            <div class="space-y-4">
                                @if($discount->type === 'percentage')
                                <div class="flex items-center p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center mr-4">
                                        <i class="fas fa-percent text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">Persentase</h4>
                                        <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($discount->value, 0) }}%</p>
                                    </div>
                                </div>
                                @elseif($discount->type === 'fixed')
                                <div class="flex items-center p-4 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center mr-4">
                                        <i class="fas fa-money-bill text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">Fixed Amount</h4>
                                        <p class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($discount->value, 0) }}</p>
                                    </div>
                                </div>
                                @else
                                <div class="flex items-center p-4 bg-purple-50 border border-purple-200 rounded-lg">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center mr-4">
                                        <i class="fas fa-gift text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">Buy X Get Y</h4>
                                        <p class="text-2xl font-bold text-purple-600 mt-1">Beli {{ $discount->buy_quantity }} Gratis {{ $discount->get_quantity }}</p>
                                    </div>
                                </div>
                                @endif

                                <!-- Detail Info -->
                                <div class="grid grid-cols-2 gap-4 mt-4">
                                    @if($discount->type !== 'buy_x_get_y')
                                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                                        <p class="text-xs text-gray-600 mb-1">Minimal Pembelian</p>
                                        <p class="text-lg font-semibold text-gray-900">
                                            {{ $discount->min_purchase > 0 ? 'Rp ' . number_format($discount->min_purchase, 0) : 'Tidak ada' }}
                                        </p>
                                    </div>
                                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                                        <p class="text-xs text-gray-600 mb-1">Maksimal Diskon</p>
                                        <p class="text-lg font-semibold text-gray-900">
                                            {{ $discount->max_discount ? 'Rp ' . number_format($discount->max_discount, 0) : 'Tidak terbatas' }}
                                        </p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Applicable To -->
                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-bullseye text-red-500 mr-2"></i>
                                Berlaku Untuk
                            </h3>
                            <div class="space-y-3">
                                @if($discount->product)
                                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                                    <i class="fas fa-box text-blue-500 mr-3"></i>
                                    <div>
                                        <p class="text-xs text-gray-600">Produk Spesifik</p>
                                        <p class="text-sm font-semibold text-gray-900">{{ $discount->product->name }}</p>
                                    </div>
                                </div>
                                @elseif($discount->category)
                                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                                    <i class="fas fa-folder text-purple-500 mr-3"></i>
                                    <div>
                                        <p class="text-xs text-gray-600">Kategori</p>
                                        <p class="text-sm font-semibold text-gray-900">{{ $discount->category->name }}</p>
                                    </div>
                                </div>
                                @else
                                <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                                    <i class="fas fa-globe text-green-500 mr-3"></i>
                                    <div>
                                        <p class="text-xs text-gray-600">Jangkauan</p>
                                        <p class="text-sm font-semibold text-gray-900">Semua Produk</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Periode -->
                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-calendar-alt text-red-500 mr-2"></i>
                                Periode Aktif
                            </h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <p class="text-xs text-gray-600 mb-2 flex items-center">
                                        <i class="fas fa-calendar-plus text-green-500 mr-2"></i>
                                        Tanggal Mulai
                                    </p>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $discount->start_date ? $discount->start_date->format('d M Y, H:i') : 'Tidak ditentukan' }}
                                    </p>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <p class="text-xs text-gray-600 mb-2 flex items-center">
                                        <i class="fas fa-calendar-minus text-red-500 mr-2"></i>
                                        Tanggal Berakhir
                                    </p>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $discount->end_date ? $discount->end_date->format('d M Y, H:i') : 'Tidak terbatas' }}
                                    </p>
                                </div>
                            </div>
                            @if($discount->start_date || $discount->end_date)
                            <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <p class="text-xs text-blue-800">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    @if($discount->end_date && $discount->end_date->isFuture())
                                        Diskon akan berakhir dalam {{ $discount->end_date->diffForHumans() }}
                                    @elseif($discount->end_date && $discount->end_date->isPast())
                                        Diskon telah berakhir {{ $discount->end_date->diffForHumans() }}
                                    @elseif($discount->start_date && $discount->start_date->isFuture())
                                        Diskon akan dimulai {{ $discount->start_date->diffForHumans() }}
                                    @else
                                        Diskon sedang berjalan
                                    @endif
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Sidebar Stats -->
                    <div class="space-y-6">
                        <!-- Usage Stats -->
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 border border-blue-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                                Statistik Penggunaan
                            </h3>
                            <div class="space-y-4">
                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                    <p class="text-xs text-gray-600 mb-1">Total Digunakan</p>
                                    <p class="text-3xl font-bold text-blue-600">{{ $discount->used_count }}</p>
                                </div>
                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                    <p class="text-xs text-gray-600 mb-1">Batas Penggunaan</p>
                                    <p class="text-2xl font-bold text-gray-900">
                                        {{ $discount->usage_limit ?? '∞' }}
                                    </p>
                                </div>
                                @if($discount->usage_limit)
                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                    <p class="text-xs text-gray-600 mb-2">Progress</p>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        @php
                                            $percentage = ($discount->used_count / $discount->usage_limit) * 100;
                                            $percentage = min($percentage, 100);
                                        @endphp
                                        <div class="bg-blue-600 h-2.5 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-1 text-right">{{ number_format($percentage, 1) }}%</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Info Card -->
                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-info-circle text-red-500 mr-2"></i>
                                Informasi
                            </h3>
                            <div class="space-y-3 text-sm">
                                <div class="flex items-center justify-between py-2 border-b border-gray-200">
                                    <span class="text-gray-600">Dibuat</span>
                                    <span class="font-medium text-gray-900">{{ $discount->created_at->format('d M Y') }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-gray-200">
                                    <span class="text-gray-600">Terakhir Diubah</span>
                                    <span class="font-medium text-gray-900">{{ $discount->updated_at->format('d M Y') }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-gray-600">Kode Diskon</span>
                                    <span class="font-mono font-semibold text-gray-900 bg-gray-200 px-2 py-1 rounded">{{ $discount->code }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Copy -->
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-6 border border-purple-200">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-copy text-purple-600 mr-2"></i>
                                Copy Kode
                            </h3>
                            <div class="flex items-center gap-2">
                                <input type="text" 
                                       value="{{ $discount->code }}" 
                                       id="discountCode"
                                       readonly
                                       class="flex-1 px-3 py-2 bg-white border border-purple-300 rounded-lg text-sm font-mono">
                                <button onclick="copyDiscountCode()" 
                                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-card-container>

    </div>
</main>

@push('scripts')
<script>
function copyDiscountCode() {
    const codeInput = document.getElementById('discountCode');
    codeInput.select();
    document.execCommand('copy');
    
    // Show feedback
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check"></i>';
    button.classList.add('bg-green-600');
    button.classList.remove('bg-purple-600');
    
    setTimeout(() => {
        button.innerHTML = originalContent;
        button.classList.remove('bg-green-600');
        button.classList.add('bg-purple-600');
    }, 2000);
}
</script>
@endpush
@endsection
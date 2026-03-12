@extends('layouts.app')

@section('title', 'Kelola Stok - ' . $rawMaterial->name . ' - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('raw-materials.index') }}" class="text-gray-500 hover:text-cuan-green transition-colors">Bahan Baku</a>
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Kelola Stok</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Kelola Stok Bahan Baku
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Manajemen mutasi stok untuk <span class="text-cuan-green font-bold tracking-tight">{{ $rawMaterial->name }}</span>
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('raw-materials.index') }}" 
                   class="inline-flex items-center gap-2 rounded-xl bg-white border border-gray-200 px-5 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all shadow-sm active:scale-95">
                    <span>Kembali</span>
                </a>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Kolom Kiri: Ringkasan Bahan --}}
            <div class="lg:col-span-1 space-y-6">
                
                <x-card-container title="Detail Produk">
                    <div class="p-6">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-20 h-20 rounded-2xl bg-gray-50 border-2 border-white shadow-sm overflow-hidden flex-shrink-0 flex items-center justify-center">
                                @if($rawMaterial->image)
                                    <img src="{{ Storage::url($rawMaterial->image) }}" class="w-full h-full object-cover">
                                @else
                                    <i class="fas fa-cube text-gray-300 text-2xl"></i>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-black text-gray-900 tracking-tight leading-tight">{{ $rawMaterial->name }}</h4>
                                <span class="text-[10px] font-black font-mono text-gray-400 bg-gray-100 px-2 py-1 rounded-lg border border-gray-100 mt-2 inline-block uppercase tracking-tighter">{{ $rawMaterial->code }}</span>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between py-1 border-b border-gray-50">
                                <span class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Kategori</span>
                                <span class="text-xs font-bold text-gray-900">{{ $rawMaterial->category->name ?? '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between py-1 border-b border-gray-50">
                                <span class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Satuan</span>
                                <span class="text-xs font-bold text-gray-900">{{ $rawMaterial->unit->name }} ({{ $rawMaterial->unit->abbreviation }})</span>
                            </div>
                            <div class="flex items-center justify-between py-1 border-b border-gray-50">
                                <span class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Min. Stok</span>
                                <span class="text-xs font-bold text-gray-900">{{ number_format($rawMaterial->min_stock, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </x-card-container>

                <div class="bg-white border border-gray-200 rounded-[2rem] p-8 shadow-sm text-center">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Total Stok Saat Ini</p>
                    <div class="flex items-baseline justify-center gap-2">
                        <span class="text-5xl font-black {{ $currentStock <= $rawMaterial->min_stock ? 'text-red-600' : 'text-gray-900' }} tracking-tighter">{{ number_format($currentStock, 2) }}</span>
                        <span class="text-sm font-black text-gray-400 uppercase tracking-widest">{{ $rawMaterial->unit->abbreviation }}</span>
                    </div>
                </div>

                <x-card-container title="Breakdown Kondisi">
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-xs">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <span class="text-[10px] font-black uppercase text-emerald-700 tracking-widest">Aman</span>
                                </div>
                                <span class="text-sm font-black text-emerald-700 leading-none">{{ number_format($validQty, 2) }}</span>
                            </div>

                            <div class="p-4 rounded-2xl bg-yellow-50 border border-yellow-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-yellow-500 text-white flex items-center justify-center text-xs">
                                        <i class="fas fa-hourglass-half"></i>
                                    </div>
                                    <span class="text-[10px] font-black uppercase text-yellow-700 tracking-widest">Segera Kadal.</span>
                                </div>
                                <span class="text-sm font-black text-yellow-700 leading-none">{{ number_format($expiringQty, 2) }}</span>
                            </div>

                            <div class="p-4 rounded-2xl bg-red-50 border border-red-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-red-500 text-white flex items-center justify-center text-xs">
                                        <i class="fas fa-calendar-times"></i>
                                    </div>
                                    <span class="text-[10px] font-black uppercase text-red-700 tracking-widest">Kadaluarsa</span>
                                </div>
                                <span class="text-sm font-black text-red-700 leading-none">{{ number_format($expiredQty, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </x-card-container>
            </div>

            {{-- Kolom Kanan: Form Transaksi --}}
            <div class="lg:col-span-2">
                <x-card-container title="Catat Mutasi Stok">
                    <form action="{{ route('raw-materials.update-stock', $rawMaterial) }}" method="POST" id="stockForm" class="p-8 space-y-8">
                        @csrf
                        
                        {{-- Transaction Type --}}
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4">Pilih Jenis Transaksi</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="type" value="add" class="peer sr-only" checked>
                                    <div class="p-6 border-2 border-gray-100 rounded-2xl transition-all peer-checked:border-cuan-green peer-checked:bg-cuan-green/5 bg-white shadow-sm ring-cuan-green/10 peer-checked:ring-4">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl transition-colors peer-checked:bg-emerald-500 peer-checked:text-white">
                                                <i class="fas fa-plus"></i>
                                            </div>
                                            <div>
                                                <p class="font-black text-gray-900 uppercase tracking-widest text-xs">Tambah Stok</p>
                                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter mt-1">Stok Masuk / Pembelian</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="type" value="reduce" class="peer sr-only">
                                    <div class="p-6 border-2 border-gray-100 rounded-2xl transition-all peer-checked:border-red-500 peer-checked:bg-red-50 bg-white shadow-sm ring-red-100 peer-checked:ring-4">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-xl transition-colors peer-checked:bg-red-500 peer-checked:text-white">
                                                <i class="fas fa-minus"></i>
                                            </div>
                                            <div>
                                                <p class="font-black text-gray-900 uppercase tracking-widest text-xs">Kurangi Stok</p>
                                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter mt-1">Stok Keluar / Koreksi</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-50">
                            {{-- Quantity --}}
                            <div>
                                <label for="quantity" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Jumlah Mutasi <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="number" name="quantity" id="quantity" step="0.01" min="0.01" required value="{{ old('quantity') }}"
                                           class="w-full pl-6 pr-16 py-4 bg-white border border-gray-200 rounded-xl text-sm font-black text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all shadow-sm" placeholder="0.00">
                                    <span class="absolute right-6 top-1/2 -translate-y-1/2 text-[10px] font-black text-gray-400 uppercase">{{ $rawMaterial->unit->abbreviation }}</span>
                                </div>
                                @error('quantity') <p class="mt-1.5 text-[10px] text-red-500 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                            </div>

                            {{-- Price (Add Only) --}}
                            <div class="purchase-field">
                                <label for="unit_price" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Harga Beli Satuan</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-gray-400">RP</span>
                                    <input type="number" name="unit_price" id="unit_price" step="0.01" min="0" value="{{ old('unit_price', $rawMaterial->purchase_price) }}"
                                           class="w-full pl-10 pr-4 py-4 bg-white border border-gray-200 rounded-xl text-sm font-black text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all shadow-sm">
                                </div>
                                @error('unit_price') <p class="mt-1.5 text-[10px] text-red-500 font-bold uppercase tracking-widest">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Purchase Specifics --}}
                        <div class="purchase-field space-y-6 pt-6 border-t border-gray-50">
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Informasi Transaksi Keuangan</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="expense_category_id" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Kategori Pengeluaran <span class="text-red-500">*</span></label>
                                    <select name="expense_category_id" id="expense_category_id" class="select2 w-full">
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach($expenseCategories as $category)
                                            <option value="{{ $category->id }}" {{ old('expense_category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="payment_method" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Metode Bayar <span class="text-red-500">*</span></label>
                                    <select name="payment_method" id="payment_method" class="select2 w-full">
                                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                                        <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                        <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Kartu</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="purchase-field grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                             <div>
                                <label for="batch_number" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Nomor Batch</label>
                                <input type="text" name="batch_number" id="batch_number" value="{{ old('batch_number') }}"
                                       class="w-full px-5 py-4 bg-white border border-gray-200 rounded-xl text-sm font-black text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all shadow-sm uppercase placeholder:text-gray-300" placeholder="Opsional">
                            </div>

                            <div>
                                <label for="expired_at" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Tanggal Kadaluarsa</label>
                                <input type="date" name="expired_at" id="expired_at" value="{{ old('expired_at') }}"
                                       class="w-full px-5 py-3.5 bg-white border border-gray-200 rounded-xl text-sm font-black text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all shadow-sm">
                            </div>
                        </div>

                        <div>
                            <label for="notes" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Catatan Keterangan</label>
                            <textarea name="notes" id="notes" rows="3" 
                                      class="w-full px-5 py-4 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all shadow-sm" placeholder="Contoh: Pembelian supplier XYZ, Penyesuaian stok rusak, dll...">{{ old('notes') }}</textarea>
                        </div>

                        {{-- ACTIONS --}}
                        <div class="pt-8 flex flex-col md:flex-row items-center justify-end gap-3">
                            <a href="{{ route('raw-materials.index') }}" 
                               class="w-full md:w-auto px-8 py-4 text-sm font-bold text-gray-500 hover:text-gray-900 transition-all text-center">
                                Batalkan
                            </a>
                             @can('update stok bahan baku')
                            <button type="submit" 
                                    class="w-full md:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-cuan-green px-8 py-4 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                                <i class="fas fa-save shadow-sm"></i>
                                <span>Simpan Mutasi Stok</span>
                            </button>
                            @endcan
                        </div>
                    </form>
                </x-card-container>
            </div>
        </div>
    </div>
</main>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        border-radius: 0.75rem !important;
        border: 1px solid #e5e7eb !important;
        height: 54px !important;
        padding: 12px 10px 10px 10px !important;
        font-weight: 700;
        font-size: 0.875rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 52px !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2();

    const typeRadios = $('input[name="type"]');
    const purchaseFields = $('.purchase-field');

    function toggleFields() {
        const isAdd = $('input[name="type"]:checked').val() === 'add';
        if (isAdd) {
            purchaseFields.removeClass('hidden');
            $('#expense_category_id, #payment_method').prop('required', true);
        } else {
            purchaseFields.addClass('hidden');
            $('#expense_category_id, #payment_method').prop('required', false);
        }
    }

    typeRadios.on('change', toggleFields);
    toggleFields();

    $('#stockForm').on('submit', function(e) {
        const type = $('input[name="type"]:checked').val();
        const qty = parseFloat($('#quantity').val());
        const currentStock = {{ $currentStock }};

        if (type === 'reduce' && qty > currentStock) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Stok Terbatas',
                text: 'Jumlah pengurangan tidak boleh melebihi stok tersedia (' + currentStock + ')',
                confirmButtonColor: '#EF4444',
                customClass: {
                    popup: 'rounded-[1.5rem] border-none shadow-2xl',
                    title: 'font-black text-gray-900'
                }
            });
        }
    });
});
</script>
@endpush
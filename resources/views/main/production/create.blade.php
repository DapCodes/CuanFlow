@extends('layouts.app')

@section('title', 'Buat Produksi - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('production.index') }}" class="text-gray-500 hover:text-cuan-green transition-colors font-medium">Produksi</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Buat Produksi</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-4xl mx-auto space-y-6">

        {{-- HEADER HALAMAN (Matched employees/create) --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Siklus Produksi Baru
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Input detail produksi untuk menambah stok produk jadi secara manual.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('production.index') }}"
                   class="px-5 py-3 border border-gray-200 bg-white text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-50 transition-all active:scale-95">
                    Batalkan
                </a>
            </div>
        </section>

        {{-- MULTI-STEP FORM --}}
        <form action="{{ route('production.store') }}" method="POST" id="mainProductionForm" class="space-y-6">
            @csrf
            
            {{-- STEP INDICATOR --}}
            <div class="bg-white border border-gray-200 rounded-[1.5rem] px-8 py-6 flex items-center justify-between relative overflow-hidden">
                <div class="flex items-center gap-8 w-full relative z-10 overflow-x-auto no-scrollbar">
                    <div class="step-link flex items-center gap-3 group cursor-pointer active-step" data-step="1">
                        <div class="w-8 h-8 rounded-xl bg-cuan-green text-white flex items-center justify-center font-black text-xs transition-all shadow-lg shadow-cuan-green/20">1</div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-cuan-green">Pilih Produk</span>
                    </div>
                    <div class="h-px w-8 bg-gray-100 hidden md:block"></div>
                    <div class="step-link flex items-center gap-3 group cursor-pointer" data-step="2">
                        <div class="w-8 h-8 rounded-xl bg-gray-50 text-gray-400 flex items-center justify-center font-black text-xs transition-all border border-gray-100">2</div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 group-hover:text-gray-600">Material</span>
                    </div>
                    <div class="h-px w-8 bg-gray-100 hidden md:block"></div>
                    <div class="step-link flex items-center gap-3 group cursor-pointer" data-step="3">
                        <div class="w-8 h-8 rounded-xl bg-gray-50 text-gray-400 flex items-center justify-center font-black text-xs transition-all border border-gray-100">3</div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 group-hover:text-gray-600">Konfirmasi</span>
                    </div>
                </div>
            </div>

            {{-- STEP 1: PILIH PRODUK --}}
            <div class="form-step block" id="step-content-1">
                <x-card-container>
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Detail Produk</h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Pilih item hasil akhir produksi</p>
                    </div>
                    <div class="px-8 py-8 space-y-8">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">Produk Jadi</label>
                            <select name="product_id" id="product_id" required class="select2-modern w-full">
                                <option value="">Cari & Pilih Produk...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" 
                                        data-unit="{{ $product->unit->name ?? 'Pcs' }}"
                                        data-recipe="{{ $product->defaultRecipe ? 1 : 0 }}"
                                        {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                        [{{ $product->code }}] {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">Jumlah Rencana</label>
                                <div class="relative">
                                    <input type="number" name="planned_quantity" id="planned_quantity" step="0.01" required value="0"
                                        class="w-full pl-6 pr-16 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-lg font-black text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all">
                                    <span class="absolute right-6 top-1/2 -translate-y-1/2 text-[10px] font-black uppercase text-gray-300" id="unit-label">UNIT</span>
                                </div>
                            </div>
                             <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">Catatan Opsional</label>
                                <textarea name="notes" rows="1" class="w-full px-6 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all" placeholder="Contoh: Batch Pagi"></textarea>
                            </div>
                        </div>

                        <div id="recipe-warning" class="hidden p-5 bg-amber-50 border border-amber-100 rounded-[1.5rem] flex items-center gap-4 animate-fade-in shadow-sm">
                             <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-amber-500 shadow-sm border border-amber-100">
                                <i class="fas fa-exclamation-triangle"></i>
                             </div>
                             <div>
                                <p class="text-xs font-black text-amber-800 uppercase tracking-widest leading-none">Produk Tanpa Resep</p>
                                <p class="text-[9px] font-bold text-amber-600/80 uppercase tracking-widest mt-1">Produksi tanpa resep hanya akan menambah stok produk tanpa memotong bahan baku.</p>
                             </div>
                        </div>
                    </div>
                </x-card-container>
            </div>

            {{-- STEP 2: MATERIAL --}}
            <div class="form-step hidden" id="step-content-2">
                <x-card-container>
                     <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-black text-gray-900 uppercase tracking-widest">Kebutuhan Bahan Baku</h2>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Perkiraan konsumsi stok dapur</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[9px] font-black uppercase tracking-widest text-gray-300">Estimasi Biaya:</span>
                            <span class="text-sm font-black text-cuan-green" id="total-estimation">Rp 0</span>
                        </div>
                    </div>
                    <div class="overflow-x-auto min-h-[150px]">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest border-b border-gray-100">
                                <tr>
                                    <th class="px-8 py-4 text-left">Nama Bahan</th>
                                    <th class="px-8 py-4 text-right">Dibutuhkan</th>
                                    <th class="px-8 py-4 text-right">Stok Saat Ini</th>
                                    <th class="px-8 py-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody id="materials-tbody" class="divide-y divide-gray-50 bg-white">
                                {{-- Loaded via AJAX --}}
                            </tbody>
                        </table>
                    </div>
                </x-card-container>
            </div>

            {{-- STEP 3: KONFIRMASI --}}
            <div class="form-step hidden animate-fade-in" id="step-content-3">
                 <x-card-container class="p-10 text-center">
                    <div class="w-24 h-24 rounded-[2.5rem] bg-gray-50 border border-gray-100 flex items-center justify-center mx-auto mb-8 shadow-inner shadow-gray-200/50">
                        <i class="fas fa-clipboard-check text-gray-300 text-3xl"></i>
                    </div>
                    <h2 class="text-xl font-black text-gray-900 tracking-tight mb-2">Konfirmasi Mulai Produksi</h2>
                    <p class="text-sm font-bold text-gray-500 mb-10 max-w-sm mx-auto leading-relaxed">
                        Anda akan memulai rencana produksi <span id="conf-qty" class="text-gray-900 font-black">0</span> <span id="conf-unit" class="text-gray-900 font-black">UNIT</span> untuk produk <span id="conf-name" class="text-gray-900 font-black">---</span>.
                    </p>

                    <div class="grid grid-cols-2 gap-4 max-w-sm mx-auto p-2 bg-gray-50 border border-gray-100 rounded-[2rem]">
                         <div class="p-4 bg-white rounded-[1.5rem] shadow-sm">
                             <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Status Awal</p>
                             <span class="text-[10px] font-black uppercase text-cuan-green tracking-widest">Planned</span>
                         </div>
                         <div class="p-4 bg-white rounded-[1.5rem] shadow-sm">
                             <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Potong Stok</p>
                             <span class="text-[10px] font-black uppercase text-amber-500 tracking-widest" id="conf-auto">OTOMATIS</span>
                         </div>
                    </div>
                 </x-card-container>
            </div>

            {{-- NAVIGATION BUTTONS --}}
            <div class="flex items-center justify-between gap-4 pt-4">
                <button type="button" id="btn-prev" class="hidden px-8 py-4 border border-gray-200 bg-white text-gray-500 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-50 transition-all active:scale-95 shadow-sm">
                    Kembali
                </button>
                <div class="flex-1"></div>
                <button type="button" id="btn-next" class="px-10 py-4 bg-cuan-green text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
                    Lanjutkan
                </button>
                <button type="submit" id="btn-submit" class="hidden px-12 py-4 bg-cuan-dark text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-black transition-all shadow-lg shadow-gray-900/20 active:scale-95">
                    Mulai Rencana Produksi
                </button>
            </div>
        </form>

    </div>
</main>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 60px !important;
        background-color: #f9fafb !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 1rem !important;
        display: flex;
        align-items: center;
        padding-left: 10px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 58px !important;
    }
    .select2-dropdown {
        border: 1px solid #e5e7eb !important;
        border-radius: 1rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        padding: 8px;
    }
    .select2-search__field {
        border-radius: 0.5rem !important;
        padding: 8px 12px !important;
    }
     @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fadeInUp 0.4s ease-out; }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-modern').select2({ width: '100%', dropdownParent: $('body') });

        let currentStep = 1;
        const totalSteps = 3;

        function updateSteps() {
            $('.form-step').addClass('hidden');
            $('#step-content-' + currentStep).removeClass('hidden');

            $('.step-link').each(function() {
                const s = $(this).data('step');
                const dot = $(this).find('div');
                const text = $(this).find('span');
                
                if (s === currentStep) {
                    dot.removeClass('bg-gray-50 text-gray-400 border-gray-100').addClass('bg-cuan-green text-white shadow-lg shadow-cuan-green/20');
                    text.removeClass('text-gray-400 text-gray-600').addClass('text-cuan-green');
                } else if (s < currentStep) {
                    dot.removeClass('bg-gray-50 text-gray-400 border-gray-100 bg-cuan-green').addClass('bg-gray-900 text-white');
                    text.removeClass('text-cuan-green text-gray-400').addClass('text-gray-900');
                } else {
                    dot.removeClass('bg-cuan-green bg-gray-900 text-white shadow-lg').addClass('bg-gray-50 text-gray-400 border-gray-100');
                    text.removeClass('text-cuan-green text-gray-900').addClass('text-gray-400');
                }
            });

            if (currentStep === 1) {
                $('#btn-prev').addClass('hidden');
                $('#btn-next').removeClass('hidden');
                $('#btn-submit').addClass('hidden');
            } else if (currentStep === totalSteps) {
                $('#btn-prev').removeClass('hidden');
                $('#btn-next').addClass('hidden');
                $('#btn-submit').removeClass('hidden');
                fillConfirmation();
            } else {
                $('#btn-prev').removeClass('hidden');
                $('#btn-next').removeClass('hidden');
                $('#btn-submit').addClass('hidden');
            }
        }

        $('#btn-next').on('click', function() {
            if (currentStep === 1) {
                if (!$('#product_id').val() || parseFloat($('#planned_quantity').val()) <= 0) {
                    Swal.fire({ icon: 'error', title: 'Oops!', text: 'Pilih produk dan tentukan jumlah rencana.', customClass: { popup: 'rounded-[1.5rem]' } });
                    return;
                }
                loadMaterials();
            }
            if (currentStep < totalSteps) {
                currentStep++;
                updateSteps();
            }
        });

        $('#btn-prev').on('click', function() {
            if (currentStep > 1) {
                currentStep--;
                updateSteps();
            }
        });

        $('#product_id').on('change', function() {
            const data = $(this).find(':selected').data();
            $('#unit-label').text((data.unit || 'UNIT').toUpperCase());
            if (data.recipe === 0) $('#recipe-warning').removeClass('hidden');
            else $('#recipe-warning').addClass('hidden');
        });

        function loadMaterials() {
            const productId = $('#product_id').val();
            const qty = $('#planned_quantity').val();
            $('#materials-tbody').html('<tr><td colspan="4" class="px-8 py-10 text-center"><i class="fas fa-spinner fa-spin text-cuan-green"></i></td></tr>');

            $.get(`{{ url('production/recipe-details') }}/${productId}`)
                .done(function(res) {
                    if (res.materials && res.materials.length > 0) {
                        let html = '';
                        let totalEst = 0;
                        const mul = qty / (res.output_quantity || 1);
                        
                        res.materials.forEach(item => {
                            const req = item.required_quantity * mul;
                            const price = item.unit_price || 0;
                            totalEst += req * price;
                            const isSufficient = item.current_stock >= req;

                            html += `
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-8 py-5">
                                        <div class="text-sm font-black text-gray-900">${item.name}</div>
                                        <div class="text-[9px] font-black uppercase text-gray-300 font-mono tracking-tighter mt-1">RAW MATERIAL</div>
                                    </td>
                                    <td class="px-8 py-5 text-right whitespace-nowrap">
                                        <span class="text-sm font-black text-gray-900">${req.toFixed(2)}</span>
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">${item.unit}</span>
                                    </td>
                                    <td class="px-8 py-5 text-right whitespace-nowrap">
                                        <span class="text-sm font-bold ${isSufficient ? 'text-gray-900' : 'text-red-500'}">${parseFloat(item.current_stock).toFixed(2)}</span>
                                    </td>
                                    <td class="px-8 py-5 text-center align-middle">
                                        ${isSufficient 
                                            ? '<span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-cuan-green/10 text-cuan-green border border-cuan-green/20"><i class="fas fa-check text-[10px]"></i></span>' 
                                            : '<span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-red-50 text-red-500 border border-red-100"><i class="fas fa-exclamation text-[10px]"></i></span>'}
                                    </td>
                                </tr>
                            `;
                        });
                        $('#materials-tbody').html(html);
                        $('#total-estimation').text('Rp ' + totalEst.toLocaleString('id-ID'));
                        $('#conf-auto').text('OTOMATIS (Bahan Baku Bakal Dipotong)').removeClass('text-gray-400').addClass('text-amber-500');
                    } else {
                        $('#materials-tbody').html('<tr><td colspan="4" class="px-8 py-10 text-center text-gray-400 font-bold uppercase tracking-widest text-[9px]">Produk ini tidak memiliki resep terdaftar.</td></tr>');
                        $('#total-estimation').text('Rp 0');
                        $('#conf-auto').text('TIDAK (Tanpa Resep)').removeClass('text-amber-500').addClass('text-gray-400');
                    }
                });
        }

        function fillConfirmation() {
            $('#conf-qty').text($('#planned_quantity').val());
            $('#conf-unit').text($('#product_id').find(':selected').data('unit') || 'UNIT');
            const pName = $('#product_id').find(':selected').text().split('] ').pop();
            $('#conf-name').text(pName);
        }

        $('#mainProductionForm').on('submit', function() {
            $('#btn-submit').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...');
        });
    });
</script>
@endpush
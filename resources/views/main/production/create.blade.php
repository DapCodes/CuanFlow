@extends('layouts.app')

@section('title', 'Mulai Produksi - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <a href="{{ route('production.index') }}" class="text-gray-500 hover:text-cuan-green transition-colors font-medium">Produksi</a>
</li>
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-black tracking-tight">Mulai Produksi</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-5xl mx-auto">
        
        {{-- HEADER --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Produksi Baru
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Rencanakan batch produksi baru dengan memvalidasi bahan baku.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('production.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all active:scale-95">
                    <span>Batal</span>
                </a>
            </div>
        </section>

        {{-- STEP INDICATOR --}}
        <div class="mb-10 px-4">
            <div class="flex items-center justify-between relative">
                <div class="absolute top-1/2 left-0 w-full h-0.5 bg-gray-100 -translate-y-1/2 z-0"></div>
                
                @foreach(['Produk', 'Bahan Baku', 'Detail', 'Konfirmasi'] as $index => $step)
                <div class="relative z-10 flex flex-col items-center group">
                    <div id="circle-step-{{ $index + 1 }}" class="w-10 h-10 rounded-2xl flex items-center justify-center font-black text-xs transition-all duration-300 border-4 border-gray-50 {{ $index === 0 ? 'bg-cuan-green text-white shadow-lg shadow-cuan-green/20' : 'bg-white text-gray-300' }}">
                        {{ $index + 1 }}
                    </div>
                    <span id="label-step-{{ $index + 1 }}" class="mt-3 text-[10px] font-black uppercase tracking-widest {{ $index === 0 ? 'text-gray-900' : 'text-gray-400' }} transition-colors">
                        {{ $step }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- FORM --}}
        <form action="{{ route('production.store') }}" method="POST" id="production-form">
            @csrf
            
            <x-card-container id="step-container" class="overflow-hidden">
                
                <!-- STEP 1: PILIH PRODUK -->
                <div id="content-step-1" class="step-content p-8 md:p-12">
                    <div class="max-w-2xl mx-auto space-y-8">
                        <div class="text-center space-y-2">
                            <h2 class="text-xl font-black text-gray-900 tracking-tight">Pilih Produk</h2>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-widest">Tentukan produk yang akan Anda produksi hari ini</p>
                        </div>

                        <div class="space-y-4">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400">Daftar Produk Produksi</label>
                            <select name="product_id" id="product_id" required
                                class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-bold text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all select2-basic">
                                <option value="">Cari Produk...</option>
                                @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} ({{ $product->code }})
                                </option>
                                @endforeach
                            </select>
                            
                            <div id="recipe-preview" class="hidden mt-6 p-6 rounded-[2rem] bg-gray-50 border border-gray-100 flex items-center gap-6 animate-fade-in">
                                <div class="w-16 h-16 rounded-2xl bg-white border border-gray-100 flex items-center justify-center text-gray-400 shadow-sm flex-shrink-0">
                                    <i class="fas fa-utensils text-xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 id="recipe-name" class="font-black text-gray-900 text-base truncate"></h4>
                                    <div class="flex items-center gap-4 mt-1.5">
                                        <span class="text-[10px] font-black uppercase tracking-widest text-cuan-green">Output per batch: <span id="recipe-output"></span></span>
                                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 border-l border-gray-200 pl-4">Durasi: <span id="recipe-time">--</span> Menit</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: BAHAN BAKU -->
                <div id="content-step-2" class="step-content hidden p-8 md:p-12">
                    <div class="space-y-8">
                        <div class="text-center space-y-2">
                            <h2 class="text-xl font-black text-gray-900 tracking-tight">Kebutuhan Bahan Baku</h2>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-widest">Validasi ketersediaan bahan di dapur sebelum memulai</p>
                        </div>

                        <div class="overflow-x-auto rounded-[2rem] border border-gray-100 shadow-sm">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest border-b border-gray-100">
                                    <tr>
                                        <th class="px-6 py-4 text-left">Bahan Baku</th>
                                        <th class="px-6 py-4 text-center">Kebutuhan per Batch</th>
                                        <th class="px-6 py-4 text-right">Stok Tersedia</th>
                                        <th class="px-6 py-4 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="material-list-body" class="divide-y divide-gray-50">
                                    <!-- Dynamic -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: DETAIL -->
                <div id="content-step-3" class="step-content hidden p-8 md:p-12">
                     <div class="max-w-2xl mx-auto space-y-8">
                        <div class="text-center space-y-2">
                            <h2 class="text-xl font-black text-gray-900 tracking-tight">Detail Produksi</h2>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-widest">Tentukan jumlah produksi dan tambahkan catatan</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400">Jumlah Produksi</label>
                                <div class="relative">
                                    <input type="number" name="planned_quantity" id="planned_quantity" step="0.01" required
                                        class="w-full pl-6 pr-20 py-5 bg-gray-50 border border-gray-200 rounded-3xl text-2xl font-black text-gray-900 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all"
                                        placeholder="0.00">
                                    <span id="unit-label" class="absolute right-6 top-1/2 -translate-y-1/2 text-xs font-black uppercase tracking-widest text-gray-400">Pcs</span>
                                </div>
                                <div class="flex items-center justify-between px-2">
                                    <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">Kelipatan Batch:</span>
                                    <span id="batch-multiplier" class="text-[10px] font-black text-cuan-green">x 0.0</span>
                                </div>
                            </div>
                            
                            <div class="space-y-4">
                                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400">Catatan Produksi</label>
                                <textarea name="notes" rows="4" 
                                    class="w-full px-6 py-5 bg-gray-50 border border-gray-200 rounded-3xl text-sm font-bold text-gray-900 placeholder:text-gray-300 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all"
                                    placeholder="Instruksi khusus untuk batch ini..."></textarea>
                            </div>
                        </div>

                        <div class="p-8 rounded-[2rem] bg-gray-900 text-white space-y-6">
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-gray-400">Ringkasan Estimasi</h4>
                            <div class="grid grid-cols-2 gap-6 border-b border-gray-800 pb-6">
                                <div>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-500 mb-1">Total Kebutuhan Bahan</p>
                                    <p id="summary-material-count" class="text-base font-black">-- Item</p>
                                </div>
                                @if(Auth::user()->can('lihat hpp'))
                                <div>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-500 mb-1">Estimasi Biaya Bahan (HPP)</p>
                                    <p id="summary-cost" class="text-base font-black text-cuan-green">Rp --</p>
                                </div>
                                @endif
                            </div>
                            <p class="text-[9px] font-bold text-gray-500 italic">* Estimasi biaya didasarkan pada harga beli terakhir bahan baku.</p>
                        </div>
                    </div>
                </div>

                <!-- STEP 4: KONFIRMASI -->
                <div id="content-step-4" class="step-content hidden p-8 md:p-12">
                    <div class="max-w-2xl mx-auto space-y-8">
                        <div class="text-center space-y-2">
                            <h2 class="text-xl font-black text-gray-900 tracking-tight">Konfirmasi Batch</h2>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-widest">Tinjau kembali rencana produksi Anda</p>
                        </div>

                        <div class="bg-gray-50 rounded-[2.5rem] border border-gray-100 p-8 space-y-8 shadow-inner">
                            <div class="flex items-center justify-between border-b border-gray-200 pb-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center text-gray-900 border border-gray-100 shadow-sm">
                                        <i class="fas fa-boxes-stacked text-base"></i>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-0.5">Produk Utama</p>
                                        <h4 id="confirm-product" class="text-base font-black text-gray-900 leading-tight">--</h4>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-0.5">Target Produksi</p>
                                    <h4 id="confirm-qty" class="text-base font-black text-cuan-green leading-tight">--</h4>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2">Kelipatan Resep</p>
                                    <span id="confirm-multiplier" class="inline-flex items-center px-3 py-1.5 rounded-xl bg-white border border-gray-100 text-[10px] font-black text-gray-900 shadow-sm">
                                        --
                                    </span>
                                </div>
                                @if(Auth::user()->can('lihat hpp'))
                                <div class="text-right">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2">Total Estimasi Biaya</p>
                                    <span id="confirm-cost" class="text-base font-black text-gray-900">
                                        --
                                    </span>
                                </div>
                                @endif
                            </div>

                            <div class="pt-6 border-t border-gray-200">
                                <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-4">Catatan</p>
                                <p id="confirm-notes" class="text-xs font-bold text-gray-600 italic">-- tidak ada catatan --</p>
                            </div>
                        </div>

                         <div class="bg-amber-50 border border-amber-100 rounded-3xl p-6 flex gap-4">
                            <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
                            <div>
                                <h5 class="text-[10px] font-black uppercase tracking-widest text-amber-800 mb-1">Peringatan Stok</h5>
                                <p class="text-[10px] font-bold text-amber-600 leading-relaxed">Pastikan Anda telah memeriksa ketersediaan fisik bahan baku. Memulai produksi akan memotong stok bahan baku secara sistem.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FOOTER NAVIGATION --}}
                <div class="px-8 py-8 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                    <button type="button" id="prev-btn" class="hidden px-8 py-4 bg-white border border-gray-200 text-gray-400 rounded-2xl font-bold text-sm hover:bg-gray-100 active:scale-95 transition-all">
                        Kembali
                    </button>
                    <div class="flex-1"></div>
                    <button type="button" id="next-btn" class="px-8 py-4 bg-cuan-green text-white rounded-2xl font-black text-sm hover:bg-cuan-dark transition-all active:scale-95 shadow-lg shadow-cuan-green/20">
                        Lanjut ke Bahan Baku
                    </button>
                    <button type="submit" id="submit-btn" class="hidden px-8 py-4 bg-cuan-green text-white rounded-2xl font-black text-sm hover:bg-cuan-dark transition-all active:scale-95 shadow-lg shadow-cuan-green/20">
                        Konfirmasi & Mulai
                    </button>
                </div>

            </x-card-container>
        </form>
    </div>
</main>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentStep = 1;
        const totalSteps = 4;
        let recipeData = null;

        const productSelect = $('#product_id');
        const plannedQtyInput = document.getElementById('planned_quantity');
        const nextBtn = document.getElementById('next-btn');
        const prevBtn = document.getElementById('prev-btn');
        const submitBtn = document.getElementById('submit-btn');

        // Initialize Select2
        productSelect.select2({
            placeholder: 'Cari Produk...',
            width: '100%',
            theme: 'classic'
        });

        // Event for Product Selection
        productSelect.on('change', function() {
            const productId = this.value;
            if(!productId) {
                document.getElementById('recipe-preview').classList.add('hidden');
                return;
            }

            // Fetch Recipe Data
            fetch(`{{ url('production/get-recipe') }}/${productId}`)
                .then(r => r.json())
                .then(res => {
                    if(res.success) {
                        recipeData = res.recipe;
                        document.getElementById('recipe-preview').classList.remove('hidden');
                        document.getElementById('recipe-name').textContent = recipeData.name;
                        document.getElementById('recipe-output').textContent = `${recipeData.output_quantity} ${res.unit_name}`;
                        document.getElementById('recipe-time').textContent = recipeData.estimated_time_minutes || '--';
                        document.getElementById('unit-label').textContent = res.unit_name;
                        
                        // Prep Step 2 Table
                        updateMaterialTable(res.materials, res.unit_name);
                    } else {
                        Swal.fire('Gagal', res.message || 'Produk belum memiliki resep aktif.', 'error');
                        productSelect.val('').trigger('change');
                    }
                });
        });

        function updateMaterialTable(materials, productUnit) {
            const tbody = document.getElementById('material-list-body');
            tbody.innerHTML = '';
            
            materials.forEach(m => {
                const isSuff = m.is_sufficient;
                const row = `
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-5 whitespace-nowrap">
                            <div class="text-sm font-black text-gray-900">${m.name}</div>
                            <div class="text-[9px] font-black uppercase text-gray-300 font-mono tracking-tighter mt-1">#${m.code}</div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-center">
                            <span class="text-xs font-black text-gray-700">${parseFloat(m.amount).toFixed(2)} ${m.unit}</span>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-right">
                            <span class="text-xs font-black ${isSuff ? 'text-cuan-green' : 'text-red-500'}">${parseFloat(m.available).toFixed(2)}</span>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">${m.unit}</span>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-center">
                            <i class="fas ${isSuff ? 'fa-check-circle text-cuan-green' : 'fa-times-circle text-red-400'} text-base"></i>
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            });
        }

        // Stepper Logic
        nextBtn.addEventListener('click', () => {
            if(currentStep === 1 && !productSelect.val()) return alertStep('Pilih produk terlebih dahulu.');
            if(currentStep === 3 && !plannedQtyInput.value) return alertStep('Masukkan jumlah produksi.');
            
            if(currentStep < totalSteps) {
                goToStep(currentStep + 1);
            }
        });

        prevBtn.addEventListener('click', () => {
            if(currentStep > 1) {
                goToStep(currentStep - 1);
            }
        });

        function goToStep(step) {
            // Hide current
            document.getElementById(`content-step-${currentStep}`).classList.add('hidden');
            
            // Show new
            document.getElementById(`content-step-${step}`).classList.remove('hidden');
            
            // Update indicator
            document.getElementById(`circle-step-${currentStep}`).classList.remove('bg-cuan-green', 'text-white', 'shadow-lg', 'shadow-cuan-green/20');
            document.getElementById(`circle-step-${currentStep}`).classList.add('bg-white', 'text-gray-300');
            document.getElementById(`label-step-${currentStep}`).classList.remove('text-gray-900');
            document.getElementById(`label-step-${currentStep}`).classList.add('text-gray-400');

            document.getElementById(`circle-step-${step}`).classList.remove('bg-white', 'text-gray-300');
            document.getElementById(`circle-step-${step}`).classList.add('bg-cuan-green', 'text-white', 'shadow-lg', 'shadow-cuan-green/20');
            document.getElementById(`label-step-${step}`).classList.remove('text-gray-400');
            document.getElementById(`label-step-${step}`).classList.add('text-gray-900');

            currentStep = step;

            // Buttons visibility
            prevBtn.classList.toggle('hidden', currentStep === 1);
            nextBtn.classList.toggle('hidden', currentStep === totalSteps);
            submitBtn.classList.toggle('hidden', currentStep !== totalSteps);

            // Update label
            if(currentStep === 1) nextBtn.textContent = 'Lanjut ke Bahan Baku';
            if(currentStep === 2) nextBtn.textContent = 'Lanjut ke Detail';
            if(currentStep === 3) {
                nextBtn.textContent = 'Lanjut ke Konfirmasi';
                updateSummary();
            }
            if(currentStep === 4) prepareConfirmation();
        }

        function alertStep(msg) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: msg,
                confirmButtonColor: '#658C58',
                customClass: { popup: 'rounded-[3rem]' }
            });
        }

        function updateSummary() {
            const qty = parseFloat(plannedQtyInput.value) || 0;
            if(!recipeData) return;

            const multiplier = qty / recipeData.output_quantity;
            document.getElementById('batch-multiplier').textContent = `x ${multiplier.toFixed(2)}`;
            
            // Re-fetch dynamic summary data if needed or calculate here
            // For now just multiplier logic
            let totalCost = 0;
            let matCount = 0;
            
            if(recipeData.materials) {
                matCount = recipeData.materials.length;
                recipeData.materials.forEach(m => {
                    const costPerUnit = m.raw_material ? (parseFloat(m.raw_material.purchase_price) || 0) : 0;
                    totalCost += (parseFloat(m.amount) * multiplier) * costPerUnit;
                });
            }

            document.getElementById('summary-material-count').textContent = `${matCount} Item Bahan`;
            document.getElementById('summary-cost').textContent = `Rp ${new Intl.NumberFormat('id-ID').format(totalCost)}`;
        }

        function prepareConfirmation() {
            const qty = parseFloat(plannedQtyInput.value) || 0;
            const unit = document.getElementById('unit-label').textContent;
            
            document.getElementById('confirm-product').textContent = recipeData.name;
            document.getElementById('confirm-qty').textContent = `${qty} ${unit}`;
            document.getElementById('confirm-multiplier').textContent = document.getElementById('batch-multiplier').textContent;
            document.getElementById('confirm-cost').textContent = document.getElementById('summary-cost').textContent;
            
            const notes = document.querySelector('textarea[name="notes"]').value;
            document.getElementById('confirm-notes').textContent = notes || '-- tidak ada catatan --';
        }

        // Form Submission Validation
        document.getElementById('production-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Konfirmasi Akhir',
                text: 'Apakah Anda yakin ingin memulai produksi batch ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Mulai!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#658C58',
                cancelButtonColor: '#ef4444',
                customClass: {
                    popup: 'rounded-[3rem]',
                    confirmButton: 'rounded-xl px-6 py-3 font-black text-xs uppercase tracking-widest',
                    cancelButton: 'rounded-xl px-6 py-3 font-black text-xs uppercase tracking-widest'
                }
            }).then((res) => {
                if(res.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
</script>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fadeInUp 0.4s ease-out; }
    
    .select2-container--classic .select2-selection--single {
        height: 60px !important;
        padding-top: 15px !important;
        border-radius: 1.5rem !important;
        border: 1px solid #e5e7eb !important;
        background-color: #f9fafb !important;
        font-weight: 700 !important;
    }
    .select2-container--classic .select2-selection--single .select2-selection__arrow {
        top: 15px !important;
    }
    .no-scrollbar::-webkit-scrollbar { display: none; }
</style>
@endpush
@endsection
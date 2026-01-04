@extends('layouts.app')

@section('title', 'Buat Produksi Baru - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('production.index') }}" class="text-gray-500 hover:text-gray-700">Produksi</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Buat Produksi</span>
</li>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 46px;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0.625rem 1rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 30px;
        color: #374151;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px;
        right: 10px;
    }
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #3b82f6;
        outline: none;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }
</style>
@endpush

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-exclamation-circle mt-0.5 text-red-500"></i>
                <div class="flex-1">
                    <h3 class="font-semibold text-red-800 mb-2">Terjadi kesalahan!</h3>
                    <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @if(session('insufficient_materials'))
            <div class="rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-exclamation-triangle mt-0.5 text-yellow-500"></i>
                <div class="flex-1">
                    <h3 class="font-semibold text-yellow-800 mb-2">Stok Bahan Baku Tidak Mencukupi!</h3>
                    <ul class="list-disc list-inside text-sm text-yellow-700 space-y-1">
                        @foreach(session('insufficient_materials') as $material)
                        <li>
                            <strong>{{ $material['name'] }}</strong>: 
                            Dibutuhkan {{ number_format($material['required'], 2) }}, 
                            Tersedia {{ number_format($material['available'], 2) }}, 
                            Kurang {{ number_format($material['shortage'], 2) }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            <div class="border-b border-gray-200 px-6 py-5">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-500 border border-blue-100">
                                <i class="fas fa-plus-circle text-sm"></i>
                            </span>
                            <span>Buat Produksi Baru</span>
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Ikuti langkah-langkah untuk membuat rencana produksi baru
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('production.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-white border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-arrow-left text-sm"></i>
                            <span>Kembali</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-b border-gray-200 px-6 py-5 bg-gray-50">
                <div class="flex justify-between items-center relative">
                    <div class="absolute top-5 left-0 right-0 h-0.5 bg-gray-200" style="z-index: 0;">
                        <div id="progressLine" class="h-full bg-blue-500 transition-all duration-300" style="width: 0%;"></div>
                    </div>

                    <div class="flex-1 text-center step-indicator active relative z-10" data-step="1">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-2 shadow-md">
                            <i class="fas fa-box text-white text-sm"></i>
                        </div>
                        <p class="text-xs font-medium text-gray-600 hidden sm:block">Pilih Produk</p>
                    </div>

                    <div class="flex-1 text-center step-indicator relative z-10" data-step="2">
                        <div class="w-10 h-10 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-list-ul text-gray-400 text-sm"></i>
                        </div>
                        <p class="text-xs font-medium text-gray-400 hidden sm:block">Bahan Baku</p>
                    </div>

                    <div class="flex-1 text-center step-indicator relative z-10" data-step="3">
                        <div class="w-10 h-10 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-calculator text-gray-400 text-sm"></i>
                        </div>
                        <p class="text-xs font-medium text-gray-400 hidden sm:block">Detail</p>
                    </div>

                    <div class="flex-1 text-center step-indicator relative z-10" data-step="4">
                        <div class="w-10 h-10 bg-white border-2 border-gray-300 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-check-circle text-gray-400 text-sm"></i>
                        </div>
                        <p class="text-xs font-medium text-gray-400 hidden sm:block">Konfirmasi</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('production.store') }}" method="POST" id="productionForm">
                @csrf

                <div class="step-content p-6" id="step1">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-box text-blue-500"></i>
                            <span>Pilih Produk</span>
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Pilih produk yang akan diproduksi</p>
                    </div>
                    
                    <div class="max-w-2xl">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Produk <span class="text-red-500">*</span>
                        </label>
                        <select name="product_id" id="productSelect" class="select2-product w-full" required>
                            <option value="">- Pilih Produk -</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}" 
                                data-recipe-id="{{ $product->defaultRecipe?->id }}"
                                {{ old('product_id', $selectedProduct?->id) == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} ({{ $product->unit->name ?? '' }})
                            </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-2">Hanya produk yang memiliki resep yang dapat diproduksi</p>
                    </div>

                    <div id="productInfo" class="mt-6 hidden">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-5">
                            <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="fas fa-info-circle text-blue-500"></i>
                                Informasi Produk
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 mb-1">Nama Resep</p>
                                    <p class="text-sm font-semibold text-gray-900" id="recipeName">-</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 mb-1">Output Per Batch</p>
                                    <p class="text-sm font-semibold text-gray-900" id="outputQuantity">-</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 mb-1">Satuan Output</p>
                                    <p class="text-sm font-semibold text-gray-900" id="outputUnit">-</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="step-content p-6 hidden" id="step2">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-list-ul text-blue-500"></i>
                            <span>Bahan Baku yang Dibutuhkan</span>
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Periksa ketersediaan bahan baku untuk produksi</p>
                    </div>

                    <input type="hidden" name="recipe_id" id="recipeIdInput">
                    
                    <div id="materialsContainer" class="space-y-4">
                    </div>

                    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-start gap-2">
                            <i class="fas fa-info-circle text-yellow-600 mt-0.5 flex-shrink-0"></i>
                            <p class="text-sm text-yellow-800">
                                Pastikan semua bahan baku tersedia dalam jumlah yang cukup. 
                                Stok bahan baku akan otomatis berkurang setelah produksi dimulai.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="step-content p-6 hidden" id="step3">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-calculator text-blue-500"></i>
                            <span>Detail Produksi</span>
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Tentukan jumlah yang akan diproduksi</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Jumlah Produksi <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="planned_quantity" id="plannedQuantity" 
                                value="{{ old('planned_quantity', 1) }}" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                placeholder="1" required min="0.01">
                            <p class="text-xs text-gray-500 mt-1">Jumlah produk yang akan dihasilkan</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Multiplier
                            </label>
                            <input type="text" id="multiplierDisplay" readonly
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600" 
                                value="1x">
                            <p class="text-xs text-gray-500 mt-1">Pengali dari resep standar</p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan
                            </label>
                            <textarea name="notes" rows="3" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                placeholder="Catatan tambahan untuk produksi ini (opsional)">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <h4 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-clipboard-list text-blue-500"></i>
                            Kebutuhan Bahan Baku
                        </h4>
                        <div id="materialRequirements" class="space-y-3">
                        </div>
                    </div>
                </div>

                <div class="step-content p-6 hidden" id="step4">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-check-circle text-blue-500"></i>
                            <span>Konfirmasi Produksi</span>
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Periksa kembali detail produksi sebelum menyimpan</p>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <p class="text-xs font-medium text-gray-500 mb-1">Produk</p>
                                <p class="text-sm font-semibold text-gray-900" id="confirmProduct">-</p>
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <p class="text-xs font-medium text-gray-500 mb-1">Resep</p>
                                <p class="text-sm font-semibold text-gray-900" id="confirmRecipe">-</p>
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <p class="text-xs font-medium text-gray-500 mb-1">Jumlah Produksi</p>
                                <p class="text-xl font-bold text-green-600" id="confirmQuantity">-</p>
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <p class="text-xs font-medium text-gray-500 mb-1">Estimasi Biaya</p>
                                <p class="text-xl font-bold text-blue-600" id="confirmCost">Rp 0</p>
                            </div>
                        </div>

                        <div class="mt-6 bg-white rounded-lg p-4 border border-gray-200">
                            <h5 class="text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                <i class="fas fa-list text-blue-500"></i>
                                Bahan yang Akan Digunakan
                            </h5>
                            <div id="confirmMaterials" class="space-y-2">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-5 bg-gray-50 border-t border-gray-200 rounded-b-xl">
                    <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3">
                        <button type="button" id="prevBtn" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-semibold text-sm" style="display: none;">
                            <i class="fas fa-arrow-left"></i>
                            <span>Sebelumnya</span>
                        </button>
                        <div class="hidden sm:block"></div>
                        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                            <button type="button" id="nextBtn" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-semibold text-sm shadow-sm">
                                <span>Selanjutnya</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                            <button type="submit" id="submitBtn" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors font-semibold text-sm shadow-sm" style="display: none;">
                                <i class="fas fa-save"></i>
                                <span>Buat Produksi</span>
                            </button>
                        </div>
                    </div>
                </div>

            </form>

        </section>

    </div>
</main>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
let currentStep = 1;
const totalSteps = 4;
let recipeData = null;
let materialsData = [];
let outputQuantityPerBatch = 1;

document.addEventListener('DOMContentLoaded', function() {
    $('.select2-product').select2({
        theme: 'default',
        width: '100%',
        placeholder: '- Pilih Produk -'
    });
    
    showStep(currentStep);
    
    $('#productSelect').on('change', function() {
        const productId = $(this).val();
        const recipeId = $(this).find(':selected').data('recipe-id');
        
        if (productId && recipeId) {
            loadRecipeDetails(productId);
        } else {
            $('#productInfo').addClass('hidden');
        }
    });

    $('#plannedQuantity').on('input', function() {
        updateMultiplier();
        calculateMaterialRequirements();
    });
    
    $('#nextBtn').on('click', function() {
        if (validateStep(currentStep)) {
            currentStep++;
            showStep(currentStep);
        }
    });
    
    $('#prevBtn').on('click', function() {
        currentStep--;
        showStep(currentStep);
    });

    @if($selectedProduct && $recipe)
        loadRecipeDetails({{ $selectedProduct->id }});
    @endif
});

function loadRecipeDetails(productId) {
    fetch(`/production/api/recipe-details/${productId}`)
        .then(response => response.json())
        .then(data => {
            recipeData = data;
            materialsData = data.materials;
            outputQuantityPerBatch = data.output_quantity;
            
            $('#recipeIdInput').val(data.recipe_id);
            $('#recipeName').text(data.recipe_name);
            $('#outputQuantity').text(data.output_quantity);
            $('#outputUnit').text(data.output_unit);
            $('#productInfo').removeClass('hidden');
            
            displayMaterials();
            updateMultiplier();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal memuat data resep');
        });
}

function displayMaterials() {
    const container = $('#materialsContainer');
    container.empty();
    
    materialsData.forEach(material => {
        const isSufficient = material.is_sufficient;
        const statusClass = isSufficient ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200';
        const statusIcon = isSufficient ? 'fa-check-circle text-green-600' : 'fa-exclamation-circle text-red-600';
        
        const html = `
            <div class="border ${statusClass} rounded-lg p-4">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center border border-orange-100">
                                <i class="fas fa-box text-orange-500 text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-gray-900">${material.name}</h4>
                            </div>
                            <i class="fas ${statusIcon}"></i>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1">Dibutuhkan</p>
                                <p class="text-sm font-semibold text-gray-900">${material.required_quantity} ${material.unit}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1">Stok Saat Ini</p>
                                <p class="text-sm font-semibold ${isSufficient ? 'text-green-600' : 'text-red-600'}">
                                    ${material.current_stock} ${material.unit}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1">Harga Satuan</p>
                                <p class="text-sm font-semibold text-gray-900">Rp ${formatNumber(material.unit_price)}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.append(html);
    });
}

function updateMultiplier() {
    const plannedQty = parseFloat($('#plannedQuantity').val()) || 0;
    const multiplier = outputQuantityPerBatch > 0 ? (plannedQty / outputQuantityPerBatch) : 1;
    $('#multiplierDisplay').val(multiplier.toFixed(2) + 'x');
}

function calculateMaterialRequirements() {
    const plannedQty = parseFloat($('#plannedQuantity').val()) || 0;
    const multiplier = outputQuantityPerBatch > 0 ? (plannedQty / outputQuantityPerBatch) : 1;
    
    const container = $('#materialRequirements');
    container.empty();
    
    let totalCost = 0;
    
    materialsData.forEach(material => {
        const requiredQty = material.required_quantity * multiplier;
        const cost = requiredQty * material.unit_price;
        totalCost += cost;
        
        const isSufficient = material.current_stock >= requiredQty;
        const statusIcon = isSufficient ? 'fa-check-circle text-green-600' : 'fa-times-circle text-red-600';
        
        const html = `
            <div class="flex justify-between items-center py-2 border-b border-blue-100">
                <div class="flex items-center gap-2">
                    <i class="fas ${statusIcon} text-xs"></i>
                    <span class="text-sm font-medium text-gray-900">${material.name}</span>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-900">${requiredQty.toFixed(2)} ${material.unit}</p>
                    <p class="text-xs text-gray-600">Rp ${formatNumber(cost)}</p>
                </div>
            </div>
        `;
        container.append(html);
    });
    
    container.append(`
        <div class="flex justify-between items-center pt-3 mt-3 border-t-2 border-blue-300">
            <span class="text-sm font-bold text-gray-900">Total Estimasi Biaya:</span>
            <span class="text-lg font-bold text-green-600">Rp ${formatNumber(totalCost)}</span>
        </div>
    `);
    
    return totalCost;
}

function showConfirmation() {
    const productName = $('#productSelect option:selected').text();
    const recipeName = $('#recipeName').text();
    const quantity = $('#plannedQuantity').val();
    const unit = $('#outputUnit').text();
    const totalCost = calculateMaterialRequirements();
    
    $('#confirmProduct').text(productName);
    $('#confirmRecipe').text(recipeName);
    $('#confirmQuantity').text(`${quantity} ${unit}`);
    $('#confirmCost').text(`Rp ${formatNumber(totalCost)}`);
    
    const multiplier = outputQuantityPerBatch > 0 ? (parseFloat(quantity) / outputQuantityPerBatch) : 1;
    const confirmContainer = $('#confirmMaterials');
    confirmContainer.empty();
    
    materialsData.forEach(material => {
        const requiredQty = material.required_quantity * multiplier;
        const html = `
            <div class="flex justify-between items-center text-sm py-2 border-b border-gray-100">
                <span class="text-gray-700">${material.name}</span>
                <span class="font-semibold text-gray-900">${requiredQty.toFixed(2)} ${material.unit}</span>
            </div>
        `;
        confirmContainer.append(html);
    });
}

function validateStep(step) {
    switch(step) {
        case 1:
            if (!$('#productSelect').val()) {
                alert('Silakan pilih produk terlebih dahulu');
                return false;
            }
            if (!recipeData) {
                alert('Data resep belum dimuat. Silakan pilih produk yang valid.');
                return false;
            }
            return true;
            
        case 2:
            const plannedQty = parseFloat($('#plannedQuantity').val()) || 1;
            const multiplier = outputQuantityPerBatch > 0 ? (plannedQty / outputQuantityPerBatch) : 1;
            
            let hasInsufficientMaterial = false;
            materialsData.forEach(material => {
                const requiredQty = material.required_quantity * multiplier;
                if (material.current_stock < requiredQty) {
                    hasInsufficientMaterial = true;
                }
            });
            
            if (hasInsufficientMaterial) {
                if (!confirm('Ada bahan baku yang tidak mencukupi. Apakah Anda yakin ingin melanjutkan?')) {
                    return false;
                }
            }
            return true;
            
        case 3:
            const qty = parseFloat($('#plannedQuantity').val());
            if (!qty || qty <= 0) {
                alert('Jumlah produksi harus lebih dari 0');
                $('#plannedQuantity').focus();
                return false;
            }
            return true;
            
        default:
            return true;
    }
}

function showStep(step) {
    $('.step-content').addClass('hidden');
    $(`#step${step}`).removeClass('hidden');
    
    $('.step-indicator').each(function(index) {
        const stepNum = index + 1;
        const circle = $(this).find('div').first();
        const icon = circle.find('i');
        const label = $(this).find('p');
        
        if (stepNum < step) {
            circle.removeClass('bg-white border-2 border-gray-300').addClass('bg-blue-500 shadow-sm');
            icon.removeClass('text-gray-400').addClass('text-white');
            label.removeClass('text-gray-400').addClass('text-gray-600');
        } else if (stepNum === step) {
            circle.removeClass('bg-white border-2 border-gray-300').addClass('bg-blue-500 shadow-md ring-4 ring-blue-200');
            icon.removeClass('text-gray-400').addClass('text-white');
            label.removeClass('text-gray-400').addClass('text-blue-600 font-semibold');
        } else {
            circle.removeClass('bg-blue-500 shadow-md ring-4 ring-blue-200 shadow-sm').addClass('bg-white border-2 border-gray-300');
            icon.removeClass('text-white').addClass('text-gray-400');
            label.removeClass('text-blue-600 font-semibold text-gray-600').addClass('text-gray-400');
        }
    });
    
    const progressPercent = ((step - 1) / (totalSteps - 1)) * 100;
    $('#progressLine').css('width', progressPercent + '%');
    
    $('#prevBtn').toggle(step > 1);
    $('#nextBtn').toggle(step < totalSteps);
    $('#submitBtn').toggle(step === totalSteps);
    
    if (step === 4) {
        showConfirmation();
    }
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(Math.round(num));
}
</script>
@endpush
@endsection
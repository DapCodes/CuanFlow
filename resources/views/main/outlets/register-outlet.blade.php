@extends('layouts.app')

@section('title', 'Daftarkan Outlet - CuanFlow')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-cuan-yellow/20 via-cuan-olive/10 to-cuan-green/20 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-block p-3 bg-gradient-to-br from-cuan-olive to-cuan-green rounded-2xl mb-4">
                <i class="fa-solid fa-store text-4xl text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Daftarkan Outlet Anda</h1>
            <p class="text-gray-600">Lengkapi informasi outlet untuk mulai menggunakan CuanFlow</p>
        </div>

        <!-- Progress Steps -->
        <div class="mb-8">
            <div class="flex items-center justify-center">
                <div class="flex items-center space-x-4">
                    <!-- Step 1 -->
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-cuan-olive text-white font-semibold step-indicator" data-step="1">
                            1
                        </div>
                        <span class="ml-2 text-sm font-medium text-gray-700 step-label" data-step="1">Info Dasar</span>
                    </div>
                    
                    <div class="w-16 h-1 bg-gray-300 step-line" data-step="1"></div>
                    
                    <!-- Step 2 -->
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 text-gray-600 font-semibold step-indicator" data-step="2">
                            2
                        </div>
                        <span class="ml-2 text-sm font-medium text-gray-500 step-label" data-step="2">Kontak</span>
                    </div>
                    
                    <div class="w-16 h-1 bg-gray-300 step-line" data-step="2"></div>
                    
                    <!-- Step 3 -->
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 text-gray-600 font-semibold step-indicator" data-step="3">
                            3
                        </div>
                        <span class="ml-2 text-sm font-medium text-gray-500 step-label" data-step="3">Bisnis</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            
            @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
                <div class="flex items-center">
                    <i class="fa-solid fa-circle-exclamation mr-2"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
            @endif

            <form action="{{ route('outlets.register.store') }}" method="POST" enctype="multipart/form-data" id="outletForm">
                @csrf

                <!-- Step 1: Info Dasar -->
                <div class="step-content" data-step="1">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Informasi Dasar Outlet</h2>
                    
                    <div class="space-y-6">
                        <!-- Nama Outlet -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Outlet <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cuan-olive focus:border-transparent transition"
                                placeholder="Contoh: Kedai Kopi Kenangan">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Logo Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Logo Outlet (Opsional)
                            </label>
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <img id="logoPreview" class="h-20 w-20 object-cover rounded-lg border-2 border-gray-300 hidden" alt="Logo preview">
                                    <div id="logoPlaceholder" class="h-20 w-20 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center">
                                        <i class="fa-solid fa-image text-2xl text-gray-400"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <input type="file" name="logo" id="logoInput" accept="image/jpeg,image/png,image/jpg" 
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-cuan-yellow/30 file:text-cuan-dark hover:file:bg-cuan-yellow/50">
                                    <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG. Maksimal 2MB</p>
                                </div>
                            </div>
                            @error('logo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Alamat -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Alamat Lengkap <span class="text-red-500">*</span>
                            </label>
                            <textarea name="address" rows="3" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cuan-olive focus:border-transparent transition"
                                placeholder="Jl. Contoh No. 123, Kelurahan, Kecamatan, Kota">{{ old('address') }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Step 2: Kontak -->
                <div class="step-content hidden" data-step="2">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Informasi Kontak</h2>
                    
                    <div class="space-y-6">
                        <!-- Nomor Telepon -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor Telepon <span class="text-red-500">*</span>
                            </label>
                            <div class="flex">
                                <span class="inline-flex items-center px-4 bg-gray-100 border border-r-0 border-gray-300 rounded-l-lg text-gray-600">
                                    <i class="fa-solid fa-phone"></i>
                                </span>
                                <input type="tel" name="phone" value="{{ old('phone') }}" required
                                    class="flex-1 px-4 py-3 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-cuan-olive focus:border-transparent transition"
                                    placeholder="08123456789">
                            </div>
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email (Opsional)
                            </label>
                            <div class="flex">
                                <span class="inline-flex items-center px-4 bg-gray-100 border border-r-0 border-gray-300 rounded-l-lg text-gray-600">
                                    <i class="fa-solid fa-envelope"></i>
                                </span>
                                <input type="email" name="email" value="{{ old('email') }}"
                                    class="flex-1 px-4 py-3 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-cuan-olive focus:border-transparent transition"
                                    placeholder="outlet@contoh.com">
                            </div>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Step 3: Bisnis -->
                <div class="step-content hidden" data-step="3">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Informasi Bisnis</h2>
                    
                    <div class="space-y-6">
                        <!-- Jenis Bisnis -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Bisnis <span class="text-red-500">*</span>
                            </label>
                            <select name="business_type" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cuan-olive focus:border-transparent transition">
                                <option value="">-- Pilih Jenis Bisnis --</option>
                                <option value="F&B" {{ old('business_type') == 'F&B' ? 'selected' : '' }}>Food & Beverage</option>
                                <option value="Retail" {{ old('business_type') == 'Retail' ? 'selected' : '' }}>Retail</option>
                                <option value="Service" {{ old('business_type') == 'Service' ? 'selected' : '' }}>Jasa/Service</option>
                                <option value="Other" {{ old('business_type') == 'Other' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('business_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kategori Bisnis -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Kategori Bisnis <span class="text-red-500">*</span>
                            </label>
                            <select name="business_category" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cuan-olive focus:border-transparent transition">
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Restaurant" {{ old('business_category') == 'Restaurant' ? 'selected' : '' }}>Restoran</option>
                                <option value="Cafe" {{ old('business_category') == 'Cafe' ? 'selected' : '' }}>Kafe</option>
                                <option value="Bakery" {{ old('business_category') == 'Bakery' ? 'selected' : '' }}>Toko Roti</option>
                                <option value="Grocery" {{ old('business_category') == 'Grocery' ? 'selected' : '' }}>Toko Kelontong</option>
                                <option value="Fashion" {{ old('business_category') == 'Fashion' ? 'selected' : '' }}>Fashion</option>
                                <option value="Electronics" {{ old('business_category') == 'Electronics' ? 'selected' : '' }}>Elektronik</option>
                                <option value="Other" {{ old('business_category') == 'Other' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('business_category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Info Box -->
                        <div class="bg-cuan-yellow/20 border-l-4 border-cuan-olive p-4 rounded">
                            <div class="flex">
                                <i class="fa-solid fa-circle-info text-cuan-dark mt-1 mr-3"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-cuan-dark mb-1">Informasi Penting</h4>
                                    <p class="text-sm text-gray-700">
                                        Data ini akan digunakan untuk mengoptimalkan fitur-fitur CuanFlow sesuai dengan kebutuhan bisnis Anda.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="mt-8 flex justify-between items-center pt-6 border-t border-gray-200">
                    <button type="button" id="prevBtn" class="px-6 py-2.5 text-gray-600 hover:text-cuan-dark font-medium transition hidden">
                        <i class="fa-solid fa-arrow-left mr-2"></i>
                        Sebelumnya
                    </button>
                    
                    <div class="ml-auto flex space-x-3">
                        <button type="button" id="nextBtn" class="px-6 py-2.5 bg-gradient-to-r from-cuan-olive to-cuan-green hover:from-cuan-green hover:to-cuan-dark text-white font-semibold rounded-lg transition shadow-lg hover:shadow-xl">
                            Selanjutnya
                            <i class="fa-solid fa-arrow-right ml-2"></i>
                        </button>
                        
                        <button type="submit" id="submitBtn" class="hidden px-6 py-2.5 bg-gradient-to-r from-cuan-green to-cuan-dark hover:from-cuan-dark hover:to-cuan-green text-white font-semibold rounded-lg transition shadow-lg hover:shadow-xl">
                            <i class="fa-solid fa-check mr-2"></i>
                            Daftarkan Outlet
                        </button>
                    </div>
                </div>

            </form>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 3;
    
    const stepContents = document.querySelectorAll('.step-content');
    const stepIndicators = document.querySelectorAll('.step-indicator');
    const stepLabels = document.querySelectorAll('.step-label');
    const stepLines = document.querySelectorAll('.step-line');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    // Logo preview
    const logoInput = document.getElementById('logoInput');
    const logoPreview = document.getElementById('logoPreview');
    const logoPlaceholder = document.getElementById('logoPlaceholder');
    
    logoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                logoPreview.src = e.target.result;
                logoPreview.classList.remove('hidden');
                logoPlaceholder.classList.add('hidden');
            }
            reader.readAsDataURL(file);
        }
    });
    
    function showStep(step) {
        stepContents.forEach(content => {
            content.classList.add('hidden');
        });
        
        document.querySelector(`.step-content[data-step="${step}"]`).classList.remove('hidden');
        
        // Update indicators
        stepIndicators.forEach((indicator, index) => {
            const stepNum = index + 1;
            if (stepNum < step) {
                indicator.classList.remove('bg-gray-300', 'text-gray-600', 'bg-cuan-olive');
                indicator.classList.add('bg-cuan-green', 'text-white');
                indicator.innerHTML = '<i class="fa-solid fa-check"></i>';
            } else if (stepNum === step) {
                indicator.classList.remove('bg-gray-300', 'text-gray-600', 'bg-cuan-green');
                indicator.classList.add('bg-cuan-olive', 'text-white');
                indicator.textContent = stepNum;
            } else {
                indicator.classList.remove('bg-cuan-olive', 'bg-cuan-green', 'text-white');
                indicator.classList.add('bg-gray-300', 'text-gray-600');
                indicator.textContent = stepNum;
            }
        });
        
        // Update labels
        stepLabels.forEach((label, index) => {
            const stepNum = index + 1;
            if (stepNum <= step) {
                label.classList.remove('text-gray-500');
                label.classList.add('text-gray-700', 'font-medium');
            } else {
                label.classList.remove('text-gray-700', 'font-medium');
                label.classList.add('text-gray-500');
            }
        });
        
        // Update lines
        stepLines.forEach((line, index) => {
            const stepNum = index + 1;
            if (stepNum < step) {
                line.classList.remove('bg-gray-300');
                line.classList.add('bg-cuan-green');
            } else {
                line.classList.remove('bg-cuan-green');
                line.classList.add('bg-gray-300');
            }
        });
        
        // Update buttons
        if (step === 1) {
            prevBtn.classList.add('hidden');
        } else {
            prevBtn.classList.remove('hidden');
        }
        
        if (step === totalSteps) {
            nextBtn.classList.add('hidden');
            submitBtn.classList.remove('hidden');
        } else {
            nextBtn.classList.remove('hidden');
            submitBtn.classList.add('hidden');
        }
    }
    
    nextBtn.addEventListener('click', function() {
        if (currentStep < totalSteps) {
            currentStep++;
            showStep(currentStep);
        }
    });
    
    prevBtn.addEventListener('click', function() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });
    
    // Initialize
    showStep(currentStep);
});
</script>
@endsection
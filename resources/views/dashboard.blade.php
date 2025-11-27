@extends('layouts.app')

@section('title', 'Menu - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Menu</span>
</li>
@endsection

@push('styles')
<style>
    /* Modal Animation */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes modalSlideUp {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @keyframes modalExit {
        from {
            opacity: 1;
            transform: scale(1);
        }
        to {
            opacity: 0;
            transform: scale(0.95);
        }
    }

    .modal-backdrop {
        animation: fadeIn 0.3s ease-out;
    }

    .modal-content {
        animation: modalSlideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .modal-exit .modal-content {
        animation: modalExit 0.2s ease-in forwards;
    }

    /* Menu Card Animations */
    @keyframes menuCardEntry {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .menu-card {
        opacity: 0; /* Initial state for animation */
        animation: menuCardEntry 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid transparent;
    }

   .menu-card:hover {
        transform: none; 
        background-color: #ffffff;
        box-shadow: none; 
        border-color: transparent;
    }

    .menu-card .menu-icon {
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .menu-card:hover .menu-icon {
        transform: scale(1.05);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    /* Stagger delays for menu cards */
    .menu-card:nth-child(1) { animation-delay: 0.05s; }
    .menu-card:nth-child(2) { animation-delay: 0.1s; }
    .menu-card:nth-child(3) { animation-delay: 0.15s; }
    .menu-card:nth-child(4) { animation-delay: 0.2s; }
    .menu-card:nth-child(5) { animation-delay: 0.25s; }
    .menu-card:nth-child(6) { animation-delay: 0.3s; }
    .menu-card:nth-child(7) { animation-delay: 0.35s; }
    .menu-card:nth-child(8) { animation-delay: 0.4s; }
    .menu-card:nth-child(9) { animation-delay: 0.45s; }
    .menu-card:nth-child(10) { animation-delay: 0.5s; }
    .menu-card:nth-child(11) { animation-delay: 0.55s; }
    .menu-card:nth-child(12) { animation-delay: 0.6s; }
    .menu-card:nth-child(13) { animation-delay: 0.65s; }
    .menu-card:nth-child(14) { animation-delay: 0.7s; }

    /* Backdrop blur effect */
    .backdrop-blur-effect {
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
</style>
@endpush

@section('content')
<main class="flex-grow flex items-center justify-center py-8 px-4">
    <div class="w-full max-w-7xl">
        <div class="flex justify-center p-4">
            <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-5 gap-6 max-w-6xl w-full">

                <a href="#" class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300">
                    <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
                        <i class="fa-solid fa-cash-register text-4xl sm:text-5xl text-white"></i>
                    </div>
                    <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">Point of Sale</span>
                </a>

                <a href="#" class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300">
                    <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-green-400 to-blue-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
                        <i class="fa-solid fa-chart-line text-4xl sm:text-5xl text-white"></i>
                    </div>
                    <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">Dashboard & Statistik</span>
                </a>

                <a href="#" class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300">
                    <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
                        <i class="fa-solid fa-file-invoice text-4xl sm:text-5xl text-white"></i>
                    </div>
                    <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">Laporan Keseluruhan</span>
                </a>

                <a href="#" class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300">
                    <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-purple-400 to-pink-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
                        <i class="fa-solid fa-wallet text-4xl sm:text-5xl text-white"></i>
                    </div>
                    <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">Keuangan</span>
                </a>
                
                <a href="#" class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300">
                    <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-blue-400 to-blue-700 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
                        <i class="fa-solid fa-flask text-4xl sm:text-5xl text-white"></i>
                    </div>
                    <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">Produksi & Stok</span>
                </a>

                <a href="#" class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300">
                    <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-orange-400 to-red-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
                        <i class="fa-solid fa-boxes-stacked text-4xl sm:text-5xl text-white"></i>
                    </div>
                    <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">Bahan Baku</span>
                </a>

                <a href="{{ route('products-hpp.index') }}" class="menu-card nav-link group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300">
                    <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-yellow-400 to-green-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
                        <i class="fa-solid fa-utensils text-4xl sm:text-5xl text-white"></i>
                    </div>
                    <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">Produk & Resep</span>
                </a>

                <a href="{{ route('outlets.index') }}" class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300">
                    <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
                        <i class="fa-solid fa-store text-4xl sm:text-5xl text-white"></i>
                    </div>
                    <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">Informasi Outlet</span>
                </a>

                <a href="#" class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300">
                    <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-teal-400 to-cyan-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
                        <i class="fa-solid fa-users text-4xl sm:text-5xl text-white"></i>
                    </div>
                    <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">Pegawai & Hak Akses</span>
                </a>

                <a href="#" class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300">
                    <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-pink-400 to-red-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
                        <i class="fa-solid fa-tags text-4xl sm:text-5xl text-white"></i>
                    </div>
                    <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">Penjualan & Diskon</span>
                </a>
                
                <a href="#" class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300">
                    <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-gray-400 to-gray-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
                        <i class="fa-solid fa-clipboard-list text-4xl sm:text-5xl text-white"></i>
                    </div>
                    <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">Kebijakan Outlet</span>
                </a>

                <a href="#" class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300">
                    <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-gray-500 to-gray-700 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
                        <i class="fa-solid fa-user-gear text-4xl sm:text-5xl text-white"></i>
                    </div>
                    <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">Pengaturan Akun</span>
                </a>

                <a href="#" class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300">
                    <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-green-500 to-teal-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
                        <i class="fa-solid fa-circle-question text-4xl sm:text-5xl text-white"></i>
                    </div>
                    <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">Bantuan & FAQ</span>
                </a>

                <a href="#" class="menu-card group block text-center p-2 hover:bg-gray-50 rounded-lg transition-all duration-300">
                    <div class="menu-icon w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-xl transition-shadow">
                        <i class="fa-solid fa-robot text-4xl sm:text-5xl text-white"></i>
                    </div>
                    <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">AI Assisten</span>
                </a>

            </div>
        </div>
    </div>
</main>

<!-- Modal untuk user yang belum memiliki outlet -->
@if(auth()->check() && is_null(auth()->user()->outlet_id))
<div id="noOutletModal" class="modal-backdrop fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-effect"></div>
    
    <div class="modal-content relative bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 p-8 text-center transform">
        <div class="mb-6">
            <div class="mx-auto w-20 h-20 bg-gradient-to-br from-cuan-green to-cuan-olive rounded-full flex items-center justify-center mb-4">
                <i class="fa-solid fa-store text-4xl text-white"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-3">Outlet Belum Terdaftar</h2>
            <p class="text-gray-600 leading-relaxed">
                Anda belum mendaftarkan outlet. Daftarkan outlet Anda sekarang untuk menggunakan semua fitur yang tersedia di CuanFlow.
            </p>
        </div>
        
        <a href="{{ route('outlets.register.index') }}" id="registerOutletBtn" class="inline-block w-full bg-cuan-green hover:bg-cuan-dark text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
            <i class="fa-solid fa-plus-circle mr-2"></i>
            Daftarkan Outlet Sekarang
        </a>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('noOutletModal');
    const registerBtn = document.getElementById('registerOutletBtn');
    
    if (modal && registerBtn) {
        // Prevent clicking outside to close
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
        
        // Prevent ESC key from closing
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                e.preventDefault();
            }
        });
        
        // Handle register button with animation
        registerBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            
            // Start exit animation
            modal.classList.add('modal-exit');
            
            // Navigate after animation
            setTimeout(() => {
                // Show page loader
                const globalLoader = document.getElementById('global-page-loader');
                if (globalLoader) {
                    globalLoader.classList.add('active');
                }
                
                setTimeout(() => {
                    window.location.href = url;
                }, 500);
            }, 300);
        });
    }
});
</script>
@endpush
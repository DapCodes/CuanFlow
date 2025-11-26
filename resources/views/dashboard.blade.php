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

@section('content')
<main class="flex-grow flex items-center justify-center py-8 px-4">
    <div class="w-full max-w-7xl">

        <div class="flex justify-center p-4">
            <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-5 gap-6 max-w-6xl w-full">

                <a href="#" class="group block text-center p-2 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-md transition-shadow">
                        <i class="fa-solid fa-cash-register text-4xl sm:text-5xl text-white"></i>
                    </div>
                    <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">Point of Sale</span>
                </a>

                <a href="#" class="group block text-center p-2 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-green-400 to-blue-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-md transition-shadow">
                        <i class="fa-solid fa-chart-line text-4xl sm:text-5xl text-white"></i>
                    </div>
                    <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">Dashboard & Statistik</span>
                </a>

                <a href="#" class="group block text-center p-2 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-md transition-shadow">
                        <i class="fa-solid fa-file-invoice text-4xl sm:text-5xl text-white"></i>
                    </div>
                    <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">Laporan Keseluruhan</span>
                </a>

                <a href="#" class="group block text-center p-2 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-purple-400 to-pink-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-md transition-shadow">
                        <i class="fa-solid fa-wallet text-4xl sm:text-5xl text-white"></i>
                    </div>
                    <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">Keuangan</span>
                </a>
                
                <a href="#" class="group block text-center p-2 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-orange-400 to-red-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-md transition-shadow">
                        <i class="fa-solid fa-boxes-stacked text-4xl sm:text-5xl text-white"></i>
                    </div>
                    <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">Stok Bahan</span>
                </a>

                <a href="{{ route('products-hpp.index') }}" class="group block text-center p-2 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-yellow-400 to-green-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-md transition-shadow">
                        <i class="fa-solid fa-utensils text-4xl sm:text-5xl text-white"></i>
                    </div>
                    <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">Produk & Resep</span>
                </a>

                <a href="#" class="group block text-center p-2 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-md transition-shadow">
                        <i class="fa-solid fa-store text-4xl sm:text-5xl text-white"></i>
                    </div>
                    <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">Informasi Outlet</span>
                </a>

                <a href="#" class="group block text-center p-2 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-teal-400 to-cyan-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-md transition-shadow">
                        <i class="fa-solid fa-users text-4xl sm:text-5xl text-white"></i>
                    </div>
                    <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">Menejemen Pegawai</span>
                </a>

                <a href="#" class="group block text-center p-2 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-pink-400 to-red-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-md transition-shadow">
                        <i class="fa-solid fa-tags text-4xl sm:text-5xl text-white"></i>
                    </div>
                    <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">Penjualan & Diskon</span>
                </a>
                
                <a href="#" class="group block text-center p-2 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-gray-400 to-gray-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-md transition-shadow">
                        <i class="fa-solid fa-clipboard-list text-4xl sm:text-5xl text-white"></i>
                    </div>
                    <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">Kebijakan Outlet</span>
                </a>

                <a href="#" class="group block text-center p-2 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-gray-500 to-gray-700 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-md transition-shadow">
                        <i class="fa-solid fa-user-gear text-4xl sm:text-5xl text-white"></i>
                    </div>
                    <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">Pengaturan Akun</span>
                </a>

                <a href="#" class="group block text-center p-2 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-green-500 to-teal-500 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-md transition-shadow">
                        <i class="fa-solid fa-circle-question text-4xl sm:text-5xl text-white"></i>
                    </div>
                    <span class="inline-flex items-center h-10 text-xs sm:text-sm font-semibold text-gray-800 leading-snug">Bantuan & FAQ</span>
                </a>

                <a href="#" class="group block text-center p-2 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:shadow-md transition-shadow">
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
<div id="noOutletModal" class="fixed inset-0 z-50 flex items-center justify-center">
    <!-- Backdrop blur -->
    <div class="absolute inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm"></div>
    
    <!-- Modal content -->
    <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 p-8 text-center transform transition-all">
        <div class="mb-6">
            <div class="mx-auto w-20 h-20 bg-gradient-to-br from-cuan-green to-cuan-olive rounded-full flex items-center justify-center mb-4">
                <i class="fa-solid fa-store text-4xl text-white"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-3">Outlet Belum Terdaftar</h2>
            <p class="text-gray-600 leading-relaxed">
                Anda belum mendaftarkan outlet. Daftarkan outlet Anda sekarang untuk menggunakan semua fitur yang tersedia di CuanFlow.
            </p>
        </div>
        
        <a href="{{ route('outlets.register.index') }}" class="inline-block w-full bg-cuan-green hover:from-cuan-green hover:to-cuan-olive text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
            <i class="fa-solid fa-plus-circle mr-2"></i>
            Daftarkan Outlet Sekarang
        </a>
    </div>
</div>

<script>
    // Prevent modal from being closed
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('noOutletModal');
        if (modal) {
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
        }
    });
</script>
@endif
@endsection
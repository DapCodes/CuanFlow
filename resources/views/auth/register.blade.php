<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar - CuanFlow</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Fonts -->
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@700,500,400&display=swap" rel="stylesheet">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    {{-- favicon --}}
    <link rel="shortcut icon" href="{{ asset('assets/image/logo.svg') }}" type="image/x-icon">
    
    <!-- GSAP -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    
    <style>
        body {
            font-family: 'Satoshi', sans-serif;
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, #31694E 0%, #658C58 50%, #BBC863 100%);
        }
        
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .blob {
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            animation: blob 8s ease-in-out infinite;
        }
        
        @keyframes blob {
            0%, 100% { border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; }
            25% { border-radius: 58% 42% 75% 25% / 76% 46% 54% 24%; }
            50% { border-radius: 50% 50% 33% 67% / 55% 27% 73% 45%; }
            75% { border-radius: 33% 67% 58% 42% / 63% 68% 32% 37%; }
        }
        
        .pattern-dots {
            background-image: radial-gradient(circle, rgba(240, 228, 145, 0.3) 1px, transparent 1px);
            background-size: 20px 20px;
        }
        
        .text-gradient {
            background: linear-gradient(135deg, #F0E491 0%, #BBC863 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
    </style>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'cuan-yellow': '#F0E491',
                        'cuan-olive': '#BBC863',
                        'cuan-green': '#658C58',
                        'cuan-dark': '#31694E',
                    }
                }
            }
        }
    </script>
</head>
<body class="overflow-x-hidden">
    
    <!-- Navigation -->
    <nav class="fixed w-full top-0 z-50 bg-white/90 backdrop-blur-md shadow-sm" id="navbar">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <img class="logo w-8 h-8" src="{{ asset('assets/image/logo.svg') }}" alt="CuanFlow Logo">
                    <span class="text-2xl font-bold text-cuan-dark">CuanFlow</span>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#hero" class="text-gray-700 hover:text-cuan-dark transition">Beranda</a>
                    <a href="#features" class="text-gray-700 hover:text-cuan-dark transition">Fitur</a>
                    <a href="#benefits" class="text-gray-700 hover:text-cuan-dark transition">Keuntungan</a>
                    <a href="#register" class="px-6 py-2 bg-cuan-dark text-white rounded-lg hover:bg-cuan-green transition">Daftar Sekarang</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="hero" class="hero-gradient min-h-screen flex items-center justify-center relative overflow-hidden pt-20">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 pattern-dots opacity-30"></div>
        <div class="absolute top-20 left-10 w-64 h-64 bg-cuan-yellow opacity-20 blob"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-cuan-olive opacity-20 blob" style="animation-delay: -4s;"></div>
        
        <div class="container mx-auto px-6 relative z-10">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right" data-aos-duration="1000">
                    <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 leading-tight">
                        Kelola Bisnis<br/>
                        <span class="text-gradient">Lebih Mudah</span>
                    </h1>
                    <p class="text-xl text-white/90 mb-8 leading-relaxed">
                        Platform manajemen bisnis all-in-one untuk mengatur stok, penjualan, keuangan, dan operasional outlet Anda dengan lebih efisien.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="#register" class="px-8 py-4 bg-white text-cuan-dark font-semibold rounded-lg hover:bg-cuan-yellow transition shadow-lg">
                            Mulai Gratis
                        </a>
                        <a href="#features" class="px-8 py-4 bg-transparent border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-cuan-dark transition">
                            Pelajari Lebih Lanjut
                        </a>
                    </div>
                </div>
                
                <div class="relative" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                    <div class="floating">
                        <img src="/images/dashboard-preview.png" alt="Dashboard Preview" class="rounded-2xl shadow-2xl" onerror="this.src='https://placehold.co/600x400/31694E/FFFFFF?text=Dashboard+Preview'">
                    </div>
                    <!-- Floating Cards -->
                    <div class="absolute -top-10 -left-10 bg-white p-4 rounded-xl shadow-xl" data-aos="zoom-in" data-aos-delay="400">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Transaksi Hari Ini</p>
                                <p class="text-xl font-bold text-cuan-dark">+245</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="absolute -bottom-10 -right-10 bg-white p-4 rounded-xl shadow-xl" data-aos="zoom-in" data-aos-delay="600">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Pendapatan</p>
                                <p class="text-xl font-bold text-cuan-dark">Rp 45.2 Jt</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Fitur Lengkap untuk Bisnis Anda</h2>
                <p class="text-xl text-gray-600">Semua yang Anda butuhkan dalam satu platform</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg card-hover" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-16 h-16 bg-cuan-yellow rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-cuan-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Manajemen Stok</h3>
                    <p class="text-gray-600 leading-relaxed">Kelola stok bahan baku dan produk jadi dengan sistem tracking real-time dan notifikasi otomatis.</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg card-hover" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-16 h-16 bg-cuan-olive rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Point of Sale</h3>
                    <p class="text-gray-600 leading-relaxed">Sistem kasir yang cepat dan mudah dengan berbagai metode pembayaran dan cetak struk otomatis.</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg card-hover" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-16 h-16 bg-cuan-green rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Laporan & Analitik</h3>
                    <p class="text-gray-600 leading-relaxed">Dashboard analitik lengkap dengan grafik interaktif dan insight bisnis yang actionable.</p>
                </div>
                
                <!-- Feature 4 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg card-hover" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-16 h-16 bg-cuan-dark rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Keuangan</h3>
                    <p class="text-gray-600 leading-relaxed">Pantau arus kas, kelola pengeluaran, dan rekonsiliasi kasir dengan mudah dan akurat.</p>
                </div>
                
                <!-- Feature 5 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg card-hover" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-16 h-16 bg-yellow-400 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Multi Outlet</h3>
                    <p class="text-gray-600 leading-relaxed">Kelola berbagai cabang outlet dari satu dashboard dengan hak akses yang terkelola.</p>
                </div>
                
                <!-- Feature 6 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg card-hover" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-16 h-16 bg-purple-500 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">AI Assistant</h3>
                    <p class="text-gray-600 leading-relaxed">Asisten AI yang membantu analisis data dan memberikan rekomendasi bisnis yang cerdas.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section id="benefits" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right">
                    <img src="/images/benefits-illustration.png" alt="Benefits" class="rounded-2xl shadow-xl" onerror="this.src='https://placehold.co/600x400/BBC863/FFFFFF?text=Business+Growth'">
                </div>
                
                <div data-aos="fade-left">
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">Mengapa Memilih CuanFlow?</h2>
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Hemat Waktu & Biaya</h3>
                                <p class="text-gray-600">Otomatisasi proses bisnis menghemat waktu hingga 70% dan mengurangi biaya operasional.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Data Real-time</h3>
                                <p class="text-gray-600">Akses informasi bisnis kapan saja, dimana saja dengan sinkronisasi real-time.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Aman & Terpercaya</h3>
                                <p class="text-gray-600">Data bisnis Anda tersimpan aman dengan enkripsi tingkat enterprise.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Support 24/7</h3>
                                <p class="text-gray-600">Tim support kami siap membantu Anda kapan pun dibutuhkan.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-20 bg-cuan-dark">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-8 text-center">
                <div data-aos="fade-up" data-aos-delay="100">
                    <div class="text-5xl font-bold text-cuan-yellow mb-2" id="users-count">0</div>
                    <p class="text-white text-lg">Pengguna Aktif</p>
                </div>
                <div data-aos="fade-up" data-aos-delay="200">
                    <div class="text-5xl font-bold text-cuan-yellow mb-2" id="outlets-count">0</div>
                    <p class="text-white text-lg">Outlet Terdaftar</p>
                </div>
                <div data-aos="fade-up" data-aos-delay="300">
                    <div class="text-5xl font-bold text-cuan-yellow mb-2" id="transactions-count">0</div>
                    <p class="text-white text-lg">Transaksi/Hari</p>
                </div>
                <div data-aos="fade-up" data-aos-delay="400">
                    <div class="text-5xl font-bold text-cuan-yellow mb-2">4.9</div>
                    <p class="text-white text-lg">Rating Pengguna</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Registration Form Section -->
    <section id="register" class="py-20 bg-gradient-to-br from-gray-50 to-white">
        <div class="container mx-auto px-6">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-12" data-aos="fade-up">
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Mulai Perjalanan Anda</h2>
                    <p class="text-xl text-gray-600">Daftar sekarang dan dapatkan akses gratis selama 30 hari</p>
                </div>
                
                <div class="bg-white rounded-3xl shadow-2xl p-8 md:p-12" data-aos="fade-up" data-aos-delay="200">
                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="flex-1">
                                    <h3 class="text-sm font-medium text-red-800 mb-2">Terdapat beberapa kesalahan:</h3>
                                    <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('register') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Personal Information -->
                        <div class="space-y-6">
                            <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                                <span class="w-8 h-8 bg-cuan-yellow rounded-full flex items-center justify-center text-sm font-bold mr-3">1</span>
                                Informasi Pribadi
                            </h3>
                            
                            <div class="grid md:grid-cols-2 gap-6">
                                <!-- Name -->
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap *</label>
                                    <input 
                                        type="text" 
                                        id="name" 
                                        name="name" 
                                        value="{{ old('name') }}"
                                        required
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-cuan-dark focus:ring-2 focus:ring-cuan-dark/20 outline-none transition"
                                        placeholder="John Doe"
                                    >
                                </div>
                                
                                <!-- Phone -->
                                <div>
                                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon *</label>
                                    <input 
                                        type="tel" 
                                        id="phone" 
                                        name="phone" 
                                        value="{{ old('phone') }}"
                                        required
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-cuan-dark focus:ring-2 focus:ring-cuan-dark/20 outline-none transition"
                                        placeholder="08123456789"
                                    >
                                </div>
                            </div>
                            
                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    value="{{ old('email') }}"
                                    required
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-cuan-dark focus:ring-2 focus:ring-cuan-dark/20 outline-none transition"
                                    placeholder="email@example.com"
                                >
                            </div>
                            
                            <div class="grid md:grid-cols-2 gap-6">
                                <!-- Password -->
                                <div>
                                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password *</label>
                                    <input 
                                        type="password" 
                                        id="password" 
                                        name="password" 
                                        required
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-cuan-dark focus:ring-2 focus:ring-cuan-dark/20 outline-none transition"
                                        placeholder="Minimal 8 karakter"
                                    >
                                </div>
                                
                                <!-- Confirm Password -->
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password *</label>
                                    <input 
                                        type="password" 
                                        id="password_confirmation" 
                                        name="password_confirmation" 
                                        required
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-cuan-dark focus:ring-2 focus:ring-cuan-dark/20 outline-none transition"
                                        placeholder="Ulangi password"
                                    >
                                </div>
                            </div>
                        </div>
                        
                        <!-- Divider -->
                        <div class="border-t-2 border-gray-100 my-8"></div>
                        
                        <!-- Outlet Information -->
                        <div class="space-y-6">
                            <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                                <span class="w-8 h-8 bg-cuan-olive rounded-full flex items-center justify-center text-sm font-bold text-white mr-3">2</span>
                                Informasi Outlet
                            </h3>
                            
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-start">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-sm text-blue-800">Outlet pertama Anda akan dibuat otomatis, setelah anda mengisi formulir dibawah.</p>
                            </div>
                            
                            <!-- Outlet Name -->
                            <div>
                                <label for="outlet_name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Outlet *</label>
                                <input 
                                    type="text" 
                                    id="outlet_name" 
                                    name="outlet_name" 
                                    value="{{ old('outlet_name') }}"
                                    required
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-cuan-dark focus:ring-2 focus:ring-cuan-dark/20 outline-none transition"
                                    placeholder="Toko Saya - Cabang Utama"
                                >
                            </div>
                            
                            <!-- Outlet Address -->
                            <div>
                                <label for="outlet_address" class="block text-sm font-semibold text-gray-700 mb-2">Alamat Outlet *</label>
                                <textarea 
                                    id="outlet_address" 
                                    name="outlet_address" 
                                    rows="3"
                                    required
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-cuan-dark focus:ring-2 focus:ring-cuan-dark/20 outline-none transition resize-none"
                                    placeholder="Jl. Contoh No. 123, Kota, Provinsi"
                                >{{ old('outlet_address') }}</textarea>
                            </div>
                            
                            <div class="grid md:grid-cols-2 gap-6">
                                <!-- Outlet Phone -->
                                <div>
                                    <label for="outlet_phone" class="block text-sm font-semibold text-gray-700 mb-2">Telepon Outlet</label>
                                    <input 
                                        type="tel" 
                                        id="outlet_phone" 
                                        name="outlet_phone" 
                                        value="{{ old('outlet_phone') }}"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-cuan-dark focus:ring-2 focus:ring-cuan-dark/20 outline-none transition"
                                        placeholder="021-12345678"
                                    >
                                </div>
                                
                                <!-- Outlet Email -->
                                <div>
                                    <label for="outlet_email" class="block text-sm font-semibold text-gray-700 mb-2">Email Outlet</label>
                                    <input 
                                        type="email" 
                                        id="outlet_email" 
                                        name="outlet_email" 
                                        value="{{ old('outlet_email') }}"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-cuan-dark focus:ring-2 focus:ring-cuan-dark/20 outline-none transition"
                                        placeholder="outlet@example.com"
                                    >
                                </div>
                            </div>
                        </div>
                        
                        <!-- Terms and Conditions -->
                        <div class="flex items-start space-x-3 pt-4">
                            <input 
                                type="checkbox" 
                                id="terms" 
                                name="terms"
                                required
                                class="w-5 h-5 text-cuan-dark border-2 border-gray-300 rounded focus:ring-2 focus:ring-cuan-dark/20 mt-0.5"
                            >
                            <label for="terms" class="text-sm text-gray-600 leading-relaxed">
                                Saya menyetujui <a href="#" class="text-cuan-dark font-semibold hover:underline">Syarat & Ketentuan</a> dan 
                                <a href="#" class="text-cuan-dark font-semibold hover:underline">Kebijakan Privasi</a> CuanFlow
                            </label>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button 
                                type="submit"
                                class="w-full py-4 bg-cuan-dark text-white font-bold text-lg rounded-lg hover:bg-cuan-green transition-all transform hover:scale-105 shadow-lg hover:shadow-xl"
                            >
                                Daftar Sekarang - GRATIS
                            </button>
                        </div>
                        
                        <!-- Login Link -->
                        <p class="text-center text-gray-600 text-sm pt-4">
                            Sudah punya akun? 
                            <a href="{{ route('login') }}" class="text-cuan-dark font-semibold hover:underline">Login di sini</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Kata Mereka</h2>
                <p class="text-xl text-gray-600">Dipercaya oleh ribuan pemilik bisnis di Indonesia</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center mb-4">
                        <div class="flex text-yellow-400">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-6 leading-relaxed">"CuanFlow benar-benar memudahkan pengelolaan 3 outlet saya. Semua data terintegrasi dan laporan otomatis. Sangat recommended!"</p>
                    <div class="flex items-center">
                        <img src="/images/avatar-1.jpg" alt="User" class="w-12 h-12 rounded-full mr-3" onerror="this.src='https://ui-avatars.com/api/?name=Budi+Santoso&background=31694E&color=fff'">
                        <div>
                            <p class="font-bold text-gray-900">Budi Santoso</p>
                            <p class="text-sm text-gray-600">Owner Kopi Nusantara</p>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonial 2 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex items-center mb-4">
                        <div class="flex text-yellow-400">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-6 leading-relaxed">"Interface-nya user friendly banget! Karyawan langsung bisa pakai tanpa training lama. Fitur AI Assistant juga sangat membantu."</p>
                    <div class="flex items-center">
                        <img src="/images/avatar-2.jpg" alt="User" class="w-12 h-12 rounded-full mr-3" onerror="this.src='https://ui-avatars.com/api/?name=Siti+Rahmawati&background=BBC863&color=fff'">
                        <div>
                            <p class="font-bold text-gray-900">Siti Rahmawati</p>
                            <p class="text-sm text-gray-600">Owner Bakery Manis</p>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonial 3 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg" data-aos="fade-up" data-aos-delay="300">
                    <div class="flex items-center mb-4">
                        <div class="flex text-yellow-400">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-6 leading-relaxed">"ROI-nya cepat banget! Dalam 2 bulan operasional sudah lebih efisien 60%. Worth it untuk investasi bisnis jangka panjang."</p>
                    <div class="flex items-center">
                        <img src="/images/avatar-3.jpg" alt="User" class="w-12 h-12 rounded-full mr-3" onerror="this.src='https://ui-avatars.com/api/?name=Ahmad+Fauzi&background=658C58&color=fff'">
                        <div>
                            <p class="font-bold text-gray-900">Ahmad Fauzi</p>
                            <p class="text-sm text-gray-600">Owner Restoran Padang</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-cuan-dark to-cuan-green relative overflow-hidden">
        <div class="absolute inset-0 pattern-dots opacity-20"></div>
        <div class="container mx-auto px-6 relative z-10">
            <div class="max-w-3xl mx-auto text-center" data-aos="zoom-in">
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
                    Siap Tingkatkan Bisnis Anda?
                </h2>
                <p class="text-xl text-white/90 mb-8">
                    Bergabunglah dengan ribuan pemilik bisnis yang sudah merasakan manfaatnya
                </p>
                <a href="#register" class="inline-block px-10 py-4 bg-white text-cuan-dark font-bold text-lg rounded-lg hover:bg-cuan-yellow transition-all transform hover:scale-105 shadow-xl">
                    Daftar Gratis Sekarang
                </a>
                <p class="text-white/80 mt-4 text-sm">Tidak perlu kartu kredit • Setup dalam 5 menit</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <!-- Brand -->
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <svg width="40" height="40" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M40 10V70M10 40H70M20 20L60 60M60 20L20 60" stroke="#F0E491" stroke-width="6" stroke-linecap="round"/>
                        </svg>
                        <span class="text-2xl font-bold">CuanFlow</span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Platform manajemen bisnis terpercaya untuk mengembangkan usaha Anda.
                    </p>
                </div>
                
                <!-- Product -->
                <div>
                    <h3 class="font-bold text-lg mb-4">Produk</h3>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#" class="hover:text-cuan-yellow transition">Fitur</a></li>
                        <li><a href="#" class="hover:text-cuan-yellow transition">Harga</a></li>
                        <li><a href="#" class="hover:text-cuan-yellow transition">Demo</a></li>
                        <li><a href="#" class="hover:text-cuan-yellow transition">API</a></li>
                    </ul>
                </div>
                
                <!-- Company -->
                <div>
                    <h3 class="font-bold text-lg mb-4">Perusahaan</h3>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#" class="hover:text-cuan-yellow transition">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-cuan-yellow transition">Blog</a></li>
                        <li><a href="#" class="hover:text-cuan-yellow transition">Karir</a></li>
                        <li><a href="#" class="hover:text-cuan-yellow transition">Kontak</a></li>
                    </ul>
                </div>
                
                <!-- Support -->
                <div>
                    <h3 class="font-bold text-lg mb-4">Dukungan</h3>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#" class="hover:text-cuan-yellow transition">Help Center</a></li>
                        <li><a href="#" class="hover:text-cuan-yellow transition">Dokumentasi</a></li>
                        <li><a href="#" class="hover:text-cuan-yellow transition">Status</a></li>
                        <li><a href="#" class="hover:text-cuan-yellow transition">FAQ</a></li>
                    </ul>
                </div>
            </div>
            
            <!-- Bottom Footer -->
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm mb-4 md:mb-0">
                    © 2025 CuanFlow. All rights reserved.
                </p>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-cuan-yellow transition">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"></path></svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-cuan-yellow transition">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"></path></svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-cuan-yellow transition">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });
        
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('shadow-lg');
            } else {
                navbar.classList.remove('shadow-lg');
            }
        });
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // GSAP Animations
        gsap.registerPlugin(ScrollTrigger);
        
        // Animated counter for stats
        function animateCounter(id, start, end, duration) {
            const element = document.getElementById(id);
            if (!element) return;
            
            const range = end - start;
            const increment = end > start ? 1 : -1;
            const stepTime = Math.abs(Math.floor(duration / range));
            let current = start;
            
            const timer = setInterval(function() {
                current += increment;
                element.textContent = current.toLocaleString('id-ID') + '+';
                if (current === end) {
                    clearInterval(timer);
                }
            }, stepTime);
        }
        
        // Trigger counter animation when stats section is visible
        ScrollTrigger.create({
            trigger: '#users-count',
            start: 'top 80%',
            onEnter: () => {
                animateCounter('users-count', 0, 5000, 2000);
                animateCounter('outlets-count', 0, 1200, 2000);
                animateCounter('transactions-count', 0, 50000, 2000);
            },
            once: true
        });
        
        // Hero section animations
        gsap.from('#hero h1', {
            duration: 1,
            y: 50,
            opacity: 0,
            ease: 'power3.out'
        });
        
        gsap.from('#hero p', {
            duration: 1,
            y: 30,
            opacity: 0,
            delay: 0.3,
            ease: 'power3.out'
        });
        
        gsap.from('#hero .flex.flex-wrap', {
            duration: 1,
            y: 30,
            opacity: 0,
            delay: 0.6,
            ease: 'power3.out'
        });
        
        // Parallax effect for floating elements
        gsap.to('.floating', {
            y: -20,
            duration: 3,
            ease: 'power1.inOut',
            repeat: -1,
            yoyo: true
        });
        
        // Feature cards hover animation
        document.querySelectorAll('.card-hover').forEach(card => {
            card.addEventListener('mouseenter', function() {
                gsap.to(this, {
                    y: -10,
                    duration: 0.3,
                    ease: 'power2.out'
                });
            });
            
            card.addEventListener('mouseleave', function() {
                gsap.to(this, {
                    y: 0,
                    duration: 0.3,
                    ease: 'power2.out'
                });
            });
        });
        
        // Form validation enhancement
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const passwordConfirmation = document.getElementById('password_confirmation').value;
                
                if (password !== passwordConfirmation) {
                    e.preventDefault();
                    alert('Password dan Konfirmasi Password tidak sama!');
                    return false;
                }
                
                if (password.length < 8) {
                    e.preventDefault();
                    alert('Password minimal 8 karakter!');
                    return false;
                }
            });
            
            // Real-time password match validation
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');
            
            confirmPasswordInput.addEventListener('input', function() {
                if (this.value && passwordInput.value !== this.value) {
                    this.setCustomValidity('Password tidak sama');
                    this.classList.add('border-red-500');
                } else {
                    this.setCustomValidity('');
                    this.classList.remove('border-red-500');
                }
            });
        }
        
        // Blob animation
        gsap.to('.blob', {
            duration: 8,
            repeat: -1,
            yoyo: true,
            ease: 'sine.inOut',
            borderRadius: '50% 50% 33% 67% / 55% 27% 73% 45%'
        });
        
        // Scroll progress indicator
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrollPercentage = (scrollTop / scrollHeight) * 100;
            
            // Create progress bar if it doesn't exist
            let progressBar = document.getElementById('scroll-progress');
            if (!progressBar) {
                progressBar = document.createElement('div');
                progressBar.id = 'scroll-progress';
                progressBar.style.cssText = 'position: fixed; top: 0; left: 0; height: 3px; background: linear-gradient(to right, #F0E491, #BBC863); z-index: 9999; transition: width 0.1s ease;';
                document.body.appendChild(progressBar);
            }
            progressBar.style.width = scrollPercentage + '%';
        });
        
        // Add entrance animation to form inputs
        const formInputs = document.querySelectorAll('input, textarea');
        formInputs.forEach((input, index) => {
            gsap.from(input, {
                scrollTrigger: {
                    trigger: input,
                    start: 'top 90%',
                    once: true
                },
                x: -30,
                opacity: 0,
                duration: 0.5,
                delay: index * 0.05
            });
        });
    </script>
</body>
</html>
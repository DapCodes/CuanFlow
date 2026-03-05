<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CuanFlow - Daftar</title>
    
    <!-- Scripts & Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@700,500,400&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('assets/image/logo.svg') }}" type="image/x-icon">
    
    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary-green: #2D5A43;
            --accent-green: #D9E88E;
        }
        * { box-sizing: border-box; }
        body { 
            font-family: 'Satoshi', sans-serif; 
            overflow: hidden;
            height: 100vh;
            height: 100dvh;
        }

        .bg-login-green { background-color: var(--primary-green); }
        .text-accent { color: var(--accent-green); }
        
        /* Swiper Custom */
        .swiper-pagination-bullet { background: rgba(255, 255, 255, 0.5) !important; opacity: 1; }
        .swiper-pagination-bullet-active { background: white !important; }

        /* SweetAlert Custom */
        .swal2-popup { font-family: 'Satoshi', sans-serif !important; border-radius: 12px !important; }
        .swal2-confirm {
            background-color: #1a1a1a !important;
            border-radius: 8px !important;
            padding: 10px 24px !important;
            font-weight: 600 !important;
        }

        .input-focus {
            transition: all 0.2s ease;
        }
        .input-focus:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 1px var(--primary-green);
        }

        /* Float Animation */
        @keyframes float {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(3deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }
        .float-shape { animation: float 6s ease-in-out infinite; }

        /* Hide scrollbar but keep functionality if needed elsewhere */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Custom Smooth Marquee Layout */
        .feature-slider {
            width: 100%;
            overflow: visible;
            position: relative;
        }

        .marquee-wrapper {
            display: flex;
            gap: 16px;
            width: max-content;
            will-change: transform;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
            position: relative;
            box-shadow: inset 0 0 20px rgba(255, 255, 255, 0.01);
        }

        .feature-card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(600px circle at var(--x) var(--y), rgba(217, 232, 142, 0.08), transparent 40%);
            opacity: 0;
            transition: opacity 0.5s;
            pointer-events: none;
        }

        .feature-card:hover::after {
            opacity: 1;
        }

        .feature-card:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(217, 232, 142, 0.3);
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.4);
        }

        .feature-icon-wrapper {
            background: rgba(217, 232, 142, 0.05);
            border: 1px solid rgba(217, 232, 142, 0.1);
            transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
        }

        .feature-card:hover .feature-icon-wrapper {
            background: var(--accent-green);
            color: var(--primary-green);
            transform: rotate(-10deg) scale(1.1);
            box-shadow: 0 0 20px rgba(217, 232, 142, 0.2);
        }
    </style>
</head>
<body class="bg-white">

    <div class="flex flex-col lg:flex-row h-screen overflow-hidden">
        
        <!-- Left Section: Feature Info & Trial -->
        <div class="hidden lg:flex lg:w-1/2 bg-login-green text-white p-12 xl:p-20 flex-col justify-between relative overflow-hidden h-full">
            <!-- Background Decoration -->
            <div class="absolute top-10 right-[-5%] w-64 h-64 border border-white/10 rounded-3xl float-shape opacity-40"></div>
            <div class="absolute bottom-20 left-[-5%] w-48 h-48 border border-white/5 rounded-full float-shape opacity-30" style="animation-delay: -2s;"></div>
            
            <!-- Content Top -->
            <div class="relative z-10">
                <h1 class="text-5xl xl:text-6xl font-bold leading-tight tracking-tight mb-4">
                    Mulai<br>
                    <span class="text-accent">Kelola Bisnis!</span>
                </h1>
                <div class="inline-flex items-center gap-2 bg-white/10 px-4 py-2 rounded-full border border-white/20 backdrop-blur-sm">
                    <span class="w-2 h-2 bg-accent rounded-full animate-pulse"></span>
                    <span class="text-xs font-bold uppercase tracking-widest text-accent">Coba Gratis Sekarang!</span>
                </div>
            </div>

            <!-- Features Carousel (Custom Smooth Ticker) -->
            <div class="relative z-10 mb-10 w-full overflow-hidden" id="marquee-container">
                <p class="text-sm font-medium mb-6 opacity-70 italic px-1">Manfaat yang akan anda dapatkan:</p>
                
                <div class="feature-slider">
                    <div class="marquee-wrapper" id="marquee-wrapper">
                        <!-- Slide 1 (Kasir) -->
                        <div class="feature-card w-[260px] p-6 rounded-3xl flex flex-col gap-5">
                            <div class="feature-icon-wrapper w-14 h-14 rounded-2xl flex items-center justify-center text-accent">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-base mb-2">Kasir Digital (POS)</h4>
                                <p class="text-[11px] opacity-60 leading-relaxed">Eksekusi transaksi kilat dengan rekapitulasi penjualan otomatis dan real-time.</p>
                            </div>
                        </div>
                        <!-- Slide 2 (Stok) -->
                        <div class="feature-card w-[260px] p-6 rounded-3xl flex flex-col gap-5">
                            <div class="feature-icon-wrapper w-14 h-14 rounded-2xl flex items-center justify-center text-accent">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-base mb-2">Stok & Inventaris</h4>
                                <p class="text-[11px] opacity-60 leading-relaxed">Pantau aliran stok barang Anda secara presisi tanpa takut kehabisan barang.</p>
                            </div>
                        </div>
                        <!-- Slide 3 (Keuangan) -->
                        <div class="feature-card w-[260px] p-6 rounded-3xl flex flex-col gap-5">
                            <div class="feature-icon-wrapper w-14 h-14 rounded-2xl flex items-center justify-center text-accent">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-base mb-2">Laporan Keuangan</h4>
                                <p class="text-[11px] opacity-60 leading-relaxed">Dapatkan wawasan performa laba rugi instan untuk keputusan bisnis yang tepat.</p>
                            </div>
                        </div>
                        <!-- Slide 4 (Karyawan) -->
                        <div class="feature-card w-[260px] p-6 rounded-3xl flex flex-col gap-5">
                            <div class="feature-icon-wrapper w-14 h-14 rounded-2xl flex items-center justify-center text-accent">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-base mb-2">Manajemen Karyawan</h4>
                                <p class="text-[11px] opacity-60 leading-relaxed">Orkestrasi jadwal, absensi, dan hak akses tim Anda dalam satu dasbor cerdas.</p>
                            </div>
                        </div>
                        <!-- Slide 5 (Pembayaran) -->
                        <div class="feature-card w-[260px] p-6 rounded-3xl flex flex-col gap-5">
                            <div class="feature-icon-wrapper w-14 h-14 rounded-2xl flex items-center justify-center text-accent">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-base mb-2">Multi-Pembayaran</h4>
                                <p class="text-[11px] opacity-60 leading-relaxed">Terima segala bentuk pembayaran dari QRIS, Dompet Digital, hingga Tunai.</p>
                            </div>
                        </div>
                        <!-- Slide 6 (Cabang) -->
                        <div class="feature-card w-[260px] p-6 rounded-3xl flex flex-col gap-5">
                            <div class="feature-icon-wrapper w-14 h-14 rounded-2xl flex items-center justify-center text-accent">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-base mb-2">Manajemen Cabang</h4>
                                <p class="text-[11px] opacity-60 leading-relaxed">Kendali penuh seluruh outlet Anda dari manapun secara terintegrasi.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Left -->
            <div class="relative z-10 text-xs opacity-40">
                <p>&copy; 2025 CuanFlow. All rights reserved.</p>
            </div>
        </div>

        <!-- Right Section: Register Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-12 xl:p-16 bg-white shrink-0 h-full overflow-y-auto no-scrollbar">
            <div class="w-full max-w-sm lg:max-w-md my-auto" x-data="registerWizard()">
                
                <!-- Logo -->
                <div class="mb-8 text-center lg:text-left">
                    <img src="{{ asset('assets/image/full-logo.svg') }}" alt="CuanFlow" class="h-9 w-auto inline-block lg:block" />
                </div>

                <!-- Title Section -->
                <div class="mb-7 text-center lg:text-left">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Buat Akun Baru!</h2>
                    <p class="text-gray-500 text-sm xl:text-base">
                        Sudah punya akun? 
                        <a href="{{ route('login') }}" class="text-gray-900 font-bold hover:underline">Masuk disini</a>
                    </p>
                </div>

                <!-- Step Indicator -->
                <div class="flex items-center gap-3 mb-8">
                    <div class="flex items-center gap-2">
                        <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold transition-all duration-300"
                             :class="step >= 1 ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-400'">1</div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400" :class="step >= 1 && 'text-gray-900'">Data Diri</span>
                    </div>
                    <div class="flex-1 h-px bg-gray-100">
                        <div class="h-full bg-gray-900 transition-all duration-400" :style="`width: ${step >= 2 ? '100%' : '0%'}`"></div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold transition-all duration-300"
                             :class="step >= 2 ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-400'">2</div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400" :class="step >= 2 && 'text-gray-900'">Keamanan</span>
                    </div>
                </div>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-5 p-4 bg-red-50 border border-red-100 rounded-2xl">
                        <ul class="text-[10px] font-bold text-red-600 space-y-1 uppercase tracking-tight">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" x-ref="form">
                    @csrf

                    <!-- STEP 1: Data Diri -->
                    <div x-show="step === 1" class="space-y-4 xl:space-y-5"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-x-4"
                         x-transition:enter-end="opacity-100 translate-x-0">

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-500 px-1 uppercase tracking-wider">Nama Lengkap</label>
                            <input type="text" name="name" x-model="form.name" required placeholder="Nama kamu"
                                class="w-full px-4 py-3.5 text-sm font-medium text-gray-900 bg-gray-50/50 border border-gray-100 rounded-xl placeholder-gray-400 input-focus focus:outline-none" />
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-500 px-1 uppercase tracking-wider">Alamat Email</label>
                            <input type="email" name="email" x-model="form.email" required placeholder="email@example.com"
                                class="w-full px-4 py-3.5 text-sm font-medium text-gray-900 bg-gray-50/50 border border-gray-100 rounded-xl placeholder-gray-400 input-focus focus:outline-none" />
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-500 px-1 uppercase tracking-wider">Nomor Telepon</label>
                            <input type="tel" name="phone" x-model="form.phone" required placeholder="08xxxxxxxxxx"
                                class="w-full px-4 py-3.5 text-sm font-medium text-gray-900 bg-gray-50/50 border border-gray-100 rounded-xl placeholder-gray-400 input-focus focus:outline-none" />
                        </div>

                        <button type="button" @click="nextStep()"
                            class="w-full py-4 bg-gray-900 text-white rounded-xl text-base font-bold hover:bg-black transition-all shadow-md active:scale-[0.98]">
                            Lanjutkan
                        </button>

                        <div class="flex items-center my-6">
                            <div class="flex-1 border-t border-gray-100"></div>
                            <span class="px-4 text-[10px] font-bold text-gray-300 uppercase tracking-widest">atau</span>
                            <div class="flex-1 border-t border-gray-100"></div>
                        </div>

                        <!-- Google Login -->
                        <a href="{{ route('auth.google') }}"
                           class="w-full flex items-center justify-center gap-3 py-3.5 border border-gray-100 rounded-xl text-gray-700 text-sm font-bold hover:bg-gray-50 hover:border-gray-200 transition-all shadow-sm">
                            <svg class="w-5 h-5" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                            Daftar dengan Google
                        </a>
                    </div>

                    <!-- STEP 2: Password -->
                    <div x-show="step === 2" class="space-y-4 xl:space-y-5" style="display:none;"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-x-4"
                         x-transition:enter-end="opacity-100 translate-x-0">

                        <div class="space-y-1.5" x-data="{ show: false }">
                            <label class="block text-xs font-bold text-gray-500 px-1 uppercase tracking-wider">Kata Sandi</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="password" x-model="form.password" placeholder="••••••••"
                                    class="w-full px-4 py-3.5 pr-12 text-sm font-medium text-gray-900 bg-gray-50/50 border border-gray-100 rounded-xl placeholder-gray-400 input-focus focus:outline-none" />
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-4 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-1.5" x-data="{ show: false }">
                            <label class="block text-xs font-bold text-gray-500 px-1 uppercase tracking-wider">Konfirmasi Kata Sandi</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="password_confirmation" x-model="form.password_confirmation" placeholder="••••••••"
                                    class="w-full px-4 py-3.5 pr-12 text-sm font-medium text-gray-900 bg-gray-50/50 border border-gray-100 rounded-xl placeholder-gray-400 input-focus focus:outline-none" />
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-4 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Terms -->
                        <div class="flex items-start gap-3 px-1">
                            <input type="checkbox" name="terms" id="terms" required checked
                                class="mt-1 w-4 h-4 text-gray-900 border-gray-200 rounded focus:ring-0 transition-all checked:bg-gray-900" />
                            <label for="terms" class="text-[10px] xl:text-xs text-gray-500 leading-relaxed font-medium">
                                Saya setuju dengan <a href="{{ route('legal.terms') }}" target="_blank" class="text-gray-900 font-bold underline">Syarat & Ketentuan</a> serta Kebijakan Privasi
                            </label>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="step = 1"
                                class="w-1/3 py-4 bg-gray-50 text-gray-700 rounded-xl text-sm font-bold hover:bg-gray-100 transition-all border border-gray-100">
                                Kembali
                            </button>
                            <button type="button" @click="submitForm()"
                                :disabled="loading"
                                class="w-2/3 py-4 bg-gray-900 text-white rounded-xl text-base font-bold hover:bg-black transition-all shadow-md active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                 <template x-if="!loading">
                                     <span>Daftar</span>
                                 </template>
                                 <template x-if="loading">
                                     <div class="flex items-center gap-2">
                                         <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                             <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                             <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                         </svg>
                                         <span>Memproses...</span>
                                     </div>
                                 </template>
                             </button>
                        </div>
                    </div>
                </form>

                <!-- Footer (Only Mobile) -->
                <div class="lg:hidden mt-12 text-center">
                    <p class="text-[10px] font-bold text-gray-300 uppercase tracking-widest">&copy; 2025 CuanFlow. All rights reserved.</p>
                </div>

            </div>
        </div>
    </div>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        // Custom Smooth Ticker Logic with Deceleration (Glide Effect)
        function initSmoothMarquee() {
            const container = document.getElementById('marquee-container');
            const wrapper = document.getElementById('marquee-wrapper');
            if (!wrapper) return;
            
            // Clone items for seamless loop
            const initialItems = [...wrapper.children];
            initialItems.forEach(item => {
                const clone = item.cloneNode(true);
                wrapper.appendChild(clone);
            });

            let x = 0;
            let currentSpeed = 0.8;
            let targetSpeed = 0.8;
            let rafId = null;

            // Deceleration/Acceleration factor (the "Smoothness")
            const friction = 0.05; 

            function animate() {
                // Lerp speed for smooth transition
                currentSpeed += (targetSpeed - currentSpeed) * friction;
                
                x -= currentSpeed;
                
                // Reset position when half way
                const halfWidth = wrapper.scrollWidth / 2;
                if (Math.abs(x) >= halfWidth) {
                    x = 0;
                }
                
                wrapper.style.transform = `translateX(${x}px)`;
                rafId = requestAnimationFrame(animate);
            }

            container.addEventListener('mouseenter', () => { targetSpeed = 0; });
            container.addEventListener('mouseleave', () => { targetSpeed = 0.8; });

            // Spotlight Effect for cards
            wrapper.addEventListener('mousemove', (e) => {
                const cards = wrapper.querySelectorAll('.feature-card');
                cards.forEach(card => {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    card.style.setProperty('--x', `${x}px`);
                    card.style.setProperty('--y', `${y}px`);
                });
            });

            animate();
        }

        window.addEventListener('load', initSmoothMarquee);

        // SweetAlert config
        const Toast = Swal.mixin({
            buttonsStyling: false,
            heightAuto: false,
            customClass: { confirmButton: 'swal2-confirm' }
        });

        function showAlert(icon, title, text) {
            return Toast.fire({ icon, title, text, confirmButtonText: 'Mengerti' });
        }

        // Wizard Logic
        function registerWizard() {
            return {
                step: 1,
                loading: false,
                form: {
                    name: '{{ old('name') }}',
                    email: '{{ old('email') }}',
                    phone: '{{ old('phone') }}',
                    password: '',
                    password_confirmation: ''
                },
                nextStep() {
                    if (!this.form.name || !this.form.email || !this.form.phone) {
                        showAlert('warning', 'Data belum lengkap', 'Silakan isi nama, email, dan nomor telepon.');
                        return;
                    }
                    this.step = 2;
                },
                submitForm() {
                    if (!this.form.password || !this.form.password_confirmation) {
                        showAlert('warning', 'Kata sandi kosong', 'Silakan isi kata sandi dan konfirmasi kata sandi.');
                        return;
                    }
                    if (this.form.password !== this.form.password_confirmation) {
                        showAlert('error', 'Kata sandi tidak cocok', 'Konfirmasi kata sandi tidak sesuai.');
                        return;
                    }
                    this.loading = true;
                    this.$refs.form.submit();
                }
            }
        }

        // Laravel alerts
        @if ($errors->any())
            window.addEventListener('DOMContentLoaded', () => showAlert('error', 'Pendaftaran Gagal', '{{ $errors->first() }}'));
        @endif
        @if (session('success'))
            window.addEventListener('DOMContentLoaded', () => showAlert('success', 'Berhasil!', '{{ session("success") }}'));
        @endif
    </script>
</body>
</html>
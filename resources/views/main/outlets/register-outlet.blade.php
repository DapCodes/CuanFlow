<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftarkan Outlet - CuanFlow</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        'cuan-primary': '#658C58',
                        'cuan-dark': '#31694E',
                        'cuan-olive': '#BBC863',
                        'cuan-yellow': '#F0E491',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-out forwards',
                        'slide-up': 'slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                        'slide-in-right': 'slideInRight 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                        'slide-out-left': 'slideOutLeft 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        slideInRight: {
                            '0%': { opacity: '0', transform: 'translateX(50px)' },
                            '100%': { opacity: '1', transform: 'translateX(0)' },
                        },
                        slideOutLeft: {
                            '0%': { opacity: '1', transform: 'translateX(0)' },
                            '100%': { opacity: '0', transform: 'translateX(-50px)' },
                        }
                    },
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at top left, #F0E49133, #ffffff 40%), 
                        radial-gradient(circle at bottom right, #658C5815, #f8fafc 60%);
            color: #1f2937;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Form Input Styles */
        .form-input {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: #ffffff;
        }
        .form-input:focus {
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.06);
        }

        /* Map Container */
        #map {
            height: 400px;
            width: 100%;
            border-radius: 1rem;
            z-index: 10;
        }

        /* Step Transitions */
        .step-content {
            display: none;
            opacity: 0;
        }
        .step-content.active {
            display: block;
            opacity: 1;
            animation: slideInRight 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .step-content.exiting {
            display: block;
            animation: slideOutLeft 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Progress Bar Transition */
        .progress-bar-fill {
            transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .step-content[data-step="1"] {
            display: block !important;
            opacity: 1 !important;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <!-- Top Bar -->
    <div class="w-full px-6 py-4 flex justify-between items-center fixed top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100">
        <!-- <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-gradient-to-br from-cuan-primary to-cuan-dark rounded-lg flex items-center justify-center text-white font-bold shadow-sm shadow-cuan-dark/30">
                <i class="fa-solid fa-store text-sm"></i>
            </div>
            <span class="font-bold text-gray-800 tracking-tight">CuanFlow</span>
        </div> -->
        
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="group flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 hover:text-red-600 transition-colors rounded-lg hover:bg-red-50">
                <span>Keluar</span>
                <i class="fa-solid fa-right-from-bracket transition-transform group-hover:translate-x-1"></i>
            </button>
        </form>
    </div>

    <!-- Main Content -->
    <main class="flex-grow px-4 py-24 sm:px-6 lg:px-8 relative overflow-hidden">
        
        <!-- Decorative Background Elements -->
        <div class="absolute top-20 left-10 w-64 h-64 bg-cuan-yellow/20 rounded-full blur-3xl -z-10 animate-pulse"></div>
        <div class="absolute bottom-20 right-10 w-80 h-80 bg-cuan-primary/10 rounded-full blur-3xl -z-10 animate-pulse" style="animation-delay: 1s;"></div>

        <div class="w-full max-w-5xl mx-auto space-y-8 lg:space-y-10 animate-slide-up">
            
            <!-- Header Section -->
            <header class="text-center">
                <p class="inline-flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-cuan-dark/70 bg-white/70 backdrop-blur px-3 py-1 rounded-full border border-cuan-dark/10 shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-cuan-primary animate-pulse"></span>
                    Pendaftaran Outlet Baru
                </p>
                <h1 class="mt-4 text-3xl sm:text-4xl font-bold text-gray-900 tracking-tight">
                    Siapkan Outlet Anda
                </h1>
                <p class="mt-2 text-gray-500 text-base sm:text-lg">
                    Lengkapi informasi berikut. Hanya beberapa langkah singkat sebelum outlet siap berjualan.
                </p>
            </header>

            <!-- Progress Steps -->
            <section class="bg-white/70 backdrop-blur border border-gray-100 rounded-2xl px-4 sm:px-6 py-4 shadow-sm shadow-slate-200/60">
                <div class="flex justify-between items-center mb-4 sm:mb-3 sm:px-1">
                    <div class="flex flex-col items-center w-1/4 cursor-pointer group" onclick="if(currentStep > 1) goToStep(1)">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-semibold text-xs sm:text-sm transition-all duration-300 border-2 step-circle active shadow-sm group-hover:shadow-md bg-white">
                            1
                        </div>
                        <span class="text-[11px] sm:text-xs font-medium mt-2 text-gray-700">Info Dasar</span>
                    </div>
                    <div class="flex flex-col items-center w-1/4 cursor-pointer group" onclick="if(currentStep > 2) goToStep(2)">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-semibold text-xs sm:text-sm transition-all duration-300 border-2 step-circle bg-white">
                            2
                        </div>
                        <span class="text-[11px] sm:text-xs font-medium mt-2 text-gray-400">Kontak</span>
                    </div>
                    <div class="flex flex-col items-center w-1/4 cursor-pointer group" onclick="if(currentStep > 3) goToStep(3)">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-semibold text-xs sm:text-sm transition-all duration-300 border-2 step-circle bg-white">
                            3
                        </div>
                        <span class="text-[11px] sm:text-xs font-medium mt-2 text-gray-400">Lokasi</span>
                    </div>
                    <div class="flex flex-col items-center w-1/4 cursor-pointer group" onclick="if(currentStep > 4) goToStep(4)">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-semibold text-xs sm:text-sm transition-all duration-300 border-2 step-circle bg-white">
                            4
                        </div>
                        <span class="text-[11px] sm:text-xs font-medium mt-2 text-gray-400">Bisnis</span>
                    </div>
                </div>
                <!-- Progress Bar Line -->
                <div class="h-1.5 sm:h-2 w-full bg-slate-100 rounded-full overflow-hidden relative">
                    <div id="progressBarFill" class="h-full bg-gradient-to-r from-cuan-primary to-cuan-dark progress-bar-fill" style="width: 25%"></div>
                </div>
                <div class="mt-3 flex justify-between text-[11px] text-slate-500 px-1">
                    <span>Langkah <span id="currentStepLabel">1</span> dari 4</span>
                    <span class="hidden sm:inline">Isi data dengan tenang, bisa disimpan nanti di pengaturan.</span>
                </div>
            </section>

            <!-- Layout: Info Panel + Form -->
            <section class="grid gap-8 lg:gap-10 lg:grid-cols-[minmax(0,0.95fr)_minmax(0,1.25fr)] items-start">
                
                <!-- Info / Mood Panel -->
                <aside class="hidden lg:flex flex-col gap-6 pr-2">
                    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-cuan-primary via-cuan-dark to-emerald-700 text-white p-7 shadow-[0_24px_60px_rgba(15,23,42,0.55)]">
                        <div class="absolute -top-16 -right-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                        <div class="absolute -bottom-10 -left-10 w-52 h-52 bg-cuan-yellow/15 rounded-full blur-2xl"></div>
                        
                        <div class="relative z-10 space-y-5">
                            <div class="inline-flex items-center gap-2 bg-white/10 rounded-full px-3 py-1 text-xs border border-white/20 backdrop-blur">
                                <i class="fa-solid fa-bolt"></i>
                                <span>Proses cepat & tanpa ribet</span>
                            </div>

                            <h2 class="text-2xl font-semibold leading-snug">
                                Satu formulir, <br/> banyak pintu cuan terbuka.
                            </h2>

                            <p class="text-sm text-emerald-50 leading-relaxed">
                                Kami bantu atur outlet Anda dari awal: mulai dari data dasar, kontak, hingga lokasi tepat di peta.
                            </p>

                            <div class="space-y-4 pt-1">
                                <div class="flex gap-3 items-start">
                                    <div class="mt-0.5 w-7 h-7 rounded-full bg-white/15 flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-circle-check"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold">Desain bersahabat</p>
                                        <p class="text-xs text-emerald-50/80 mt-0.5">
                                            Form disusun per langkah agar tidak terasa menumpuk dan membingungkan.
                                        </p>
                                    </div>
                                </div>
                                <div class="flex gap-3 items-start">
                                    <div class="mt-0.5 w-7 h-7 rounded-full bg-white/15 flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold">Lokasi akurat di peta</p>
                                        <p class="text-xs text-emerald-50/80 mt-0.5">
                                            Cukup klik di peta atau gunakan lokasi saya, alamat akan terisi otomatis.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-white/15 flex items-center gap-3 text-xs text-emerald-50/80">
                                <i class="fa-solid fa-clock text-emerald-100"></i>
                                <p>Kurang dari 5 menit, outlet Anda sudah tercatat dan siap dikelola.</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3 text-xs text-slate-600 bg-white/80 border border-slate-100 rounded-2xl px-4 py-3 shadow-sm">
                        <div class="flex gap-2">
                            <i class="fa-solid fa-lightbulb text-cuan-primary pt-0.5"></i>
                            <p><span class="font-semibold">Tips:</span> Jika ragu, isi dulu yang wajib (<span class="text-red-500">*</span>). Detail lain bisa dilengkapi kapan saja.</p>
                        </div>
                        <div class="flex gap-2">
                            <i class="fa-solid fa-shield-halved text-cuan-primary pt-0.5"></i>
                            <p>Data Anda aman dan hanya digunakan untuk keperluan pengelolaan outlet.</p>
                        </div>
                    </div>
                </aside>

                <!-- Form Area (tanpa card besar, tapi tetap rapi) -->
                <section class="bg-white/90 backdrop-blur rounded-3xl border border-gray-100 px-4 sm:px-6 lg:px-8 py-6 sm:py-7 lg:py-8 shadow-[0_18px_40px_rgba(15,23,42,0.08)]">
                    
                    @if(session('error'))
                    <div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl flex items-center gap-3 animate-fade-in">
                        <i class="fa-solid fa-circle-exclamation text-xl"></i>
                        <span class="font-medium text-sm">{{ session('error') }}</span>
                    </div>
                    @endif

                    <form action="{{ route('outlets.register.store') }}" method="POST" enctype="multipart/form-data" id="outletForm" class="space-y-8">
                        @csrf

                        <!-- STEP 1: Info Dasar -->
                        <div class="step-content active" data-step="1">
                            <div class="mb-2 flex items-center gap-2 text-[11px] uppercase tracking-[0.18em] text-cuan-dark/80">
                                <span class="w-6 h-[1px] bg-cuan-dark/60 rounded-full"></span>
                                <span>Langkah 1 dari 4 • Info Dasar</span>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 mb-1 flex items-center gap-2">
                                <span class="inline-flex w-8 h-8 rounded-xl bg-cuan-primary/10 text-cuan-primary items-center justify-center">
                                    <i class="fa-solid fa-store"></i>
                                </span>
                                <span>Informasi Dasar Outlet</span>
                            </h2>
                            <p class="text-sm text-gray-500 mb-6">
                                Mulai dari nama outlet dan logo untuk tampilan yang lebih profesional.
                            </p>
                            
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Outlet <span class="text-red-500">*</span></label>
                                    <p class="text-[11px] text-gray-400 mb-2">Nama ini akan terlihat oleh pelanggan Anda.</p>
                                    <input type="text" name="name" value="{{ old('name') }}" required
                                        class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-cuan-primary focus:ring-2 focus:ring-cuan-primary/20 outline-none text-gray-800 placeholder-gray-400"
                                        placeholder="Contoh: Kopi Senja">
                                    @error('name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Logo Outlet (Opsional)</label>
                                    <p class="text-[11px] text-gray-400 mb-2">Bantu pelanggan mengenali brand Anda lebih cepat.</p>
                                    <div class="flex items-start gap-4 p-4 bg-slate-50/80 rounded-2xl border border-dashed border-slate-200 hover:border-cuan-primary/60 transition-colors">
                                        <div class="relative group cursor-pointer">
                                            <div id="logoPlaceholder" class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center border border-gray-200 shadow-sm group-hover:border-cuan-primary/70 transition-all">
                                                <i class="fa-solid fa-image text-gray-300 text-2xl group-hover:text-cuan-primary transition-colors"></i>
                                            </div>
                                            <img id="logoPreview" class="w-20 h-20 object-cover rounded-2xl shadow-sm hidden" alt="Preview">
                                            <input type="file" name="logo" id="logoInput" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-700">Upload Logo</p>
                                            <p class="text-xs text-gray-500 mt-1">Format JPG atau PNG, maksimal 2MB. Gunakan logo persegi agar tampilan lebih rapi.</p>
                                            <button type="button" id="removeLogo" class="text-xs text-red-500 hover:text-red-700 mt-2 font-medium hidden">
                                                Hapus Logo
                                            </button>
                                        </div>
                                    </div>
                                    @error('logo') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- STEP 2: Kontak -->
                        <div class="step-content" data-step="2">
                            <div class="mb-2 flex items-center gap-2 text-[11px] uppercase tracking-[0.18em] text-cuan-dark/80">
                                <span class="w-6 h-[1px] bg-cuan-dark/60 rounded-full"></span>
                                <span>Langkah 2 dari 4 • Kontak</span>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 mb-1 flex items-center gap-2">
                                <span class="inline-flex w-8 h-8 rounded-xl bg-cuan-primary/10 text-cuan-primary items-center justify-center">
                                    <i class="fa-solid fa-address-book"></i>
                                </span>
                                <span>Kontak Outlet</span>
                            </h2>
                            <p class="text-sm text-gray-500 mb-6">
                                Pastikan pelanggan dan tim internal mudah menghubungi outlet Anda.
                            </p>

                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nomor Telepon <span class="text-red-500">*</span></label>
                                    <p class="text-[11px] text-gray-400 mb-2">Gunakan nomor aktif yang bisa dihubungi untuk pemesanan atau konfirmasi.</p>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                            <i class="fa-solid fa-phone"></i>
                                        </span>
                                        <input type="tel" name="phone" value="{{ old('phone') }}" required
                                            class="form-input w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 focus:border-cuan-primary focus:ring-2 focus:ring-cuan-primary/20 outline-none text-gray-800"
                                            placeholder="08123456789">
                                    </div>
                                    @error('phone') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email (Opsional)</label>
                                    <p class="text-[11px] text-gray-400 mb-2">Cocok untuk mengirim laporan, invoice, dan pemberitahuan lainnya.</p>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                            <i class="fa-solid fa-envelope"></i>
                                        </span>
                                        <input type="email" name="email" value="{{ old('email') }}"
                                            class="form-input w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 focus:border-cuan-primary focus:ring-2 focus:ring-cuan-primary/20 outline-none text-gray-800"
                                            placeholder="outlet@email.com">
                                    </div>
                                    @error('email') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- STEP 3: Lokasi (Leaflet Map) -->
                        <div class="step-content" data-step="3">
                            <div class="mb-2 flex items-center gap-2 text-[11px] uppercase tracking-[0.18em] text-cuan-dark/80">
                                <span class="w-6 h-[1px] bg-cuan-dark/60 rounded-full"></span>
                                <span>Langkah 3 dari 4 • Lokasi</span>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 mb-1 flex items-center gap-2">
                                <span class="inline-flex w-8 h-8 rounded-xl bg-cuan-primary/10 text-cuan-primary items-center justify-center">
                                    <i class="fa-solid fa-map-location-dot"></i>
                                </span>
                                <span>Lokasi Outlet</span>
                            </h2>
                            <p class="text-sm text-gray-500 mb-6">
                                Tandai titik outlet Anda di peta agar pengiriman dan laporan lokasi lebih akurat.
                            </p>

                            <div class="space-y-4">
                                <!-- Search & Geolocate -->
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <div class="relative flex-grow">
                                        <input type="text" id="locationSearch" placeholder="Cari alamat, nama jalan, atau area..." 
                                            class="w-full pl-4 pr-10 py-2.5 rounded-xl border border-gray-200 focus:border-cuan-primary focus:ring-2 focus:ring-cuan-primary/20 outline-none text-sm bg-white/80">
                                        <button type="button" id="searchBtn" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-cuan-primary transition-colors">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </button>
                                    </div>
                                    <button type="button" id="geoBtn" class="px-4 py-2.5 bg-slate-900 text-white hover:bg-slate-800 rounded-xl transition-all text-sm font-medium flex items-center gap-2 shadow-sm hover:shadow-md">
                                        <i class="fa-solid fa-location-crosshairs text-xs"></i> 
                                        <span class="hidden sm:inline">Gunakan Lokasi Saya</span>
                                        <span class="sm:hidden">Lokasi Saya</span>
                                    </button>
                                </div>

                                <!-- Map Container -->
                                <div class="relative rounded-2xl overflow-hidden border border-gray-200 shadow-sm shadow-slate-200">
                                    <div id="map"></div>
                                    <div class="absolute bottom-4 left-4 right-4 bg-white/95 backdrop-blur-sm p-3 rounded-xl shadow-lg z-[500] text-xs sm:text-sm border border-slate-100/60">
                                        <p class="font-semibold text-gray-800 mb-1 flex items-center gap-1.5">
                                            <i class="fa-solid fa-thumbtack text-red-500"></i> 
                                            <span>Lokasi Terpilih</span>
                                        </p>
                                        <p id="addressDisplay" class="text-gray-600 line-clamp-2">
                                            Belum ada lokasi dipilih. Klik pada peta atau gunakan tombol lokasi.
                                        </p>
                                    </div>
                                </div>

                                <!-- Hidden Inputs -->
                                <input type="hidden" name="latitude" id="latInput">
                                <input type="hidden" name="longitude" id="lngInput">

                                <!-- Manual Address Textarea -->
                                <div class="pt-1">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Detail Alamat <span class="text-red-500">*</span></label>
                                    <p class="text-[11px] text-gray-400 mb-2">
                                        Tambahkan patokan seperti nama gedung, blok, atau keterangan lantai untuk memudahkan kurir.
                                    </p>
                                    <textarea name="address" id="addressInput" rows="2" required
                                        class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-cuan-primary focus:ring-2 focus:ring-cuan-primary/20 outline-none text-gray-800 text-sm"
                                        placeholder="Nama jalan, nomor gedung, patokan..."></textarea>
                                    @error('address') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- STEP 4: Bisnis -->
                        <div class="step-content" data-step="4">
                            <div class="mb-2 flex items-center gap-2 text-[11px] uppercase tracking-[0.18em] text-cuan-dark/80">
                                <span class="w-6 h-[1px] bg-cuan-dark/60 rounded-full"></span>
                                <span>Langkah 4 dari 4 • Detail Bisnis</span>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 mb-1 flex items-center gap-2">
                                <span class="inline-flex w-8 h-8 rounded-xl bg-cuan-primary/10 text-cuan-primary items-center justify-center">
                                    <i class="fa-solid fa-briefcase"></i>
                                </span>
                                <span>Detail Bisnis</span>
                            </h2>
                            <p class="text-sm text-gray-500 mb-6">
                                Data ini membantu kami menyiapkan laporan dan fitur yang sesuai untuk jenis usaha Anda.
                            </p>

                            <div class="space-y-6">
                                <div class="grid sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jenis Bisnis <span class="text-red-500">*</span></label>
                                        <p class="text-[11px] text-gray-400 mb-2">Pilih kategori besar dari jenis usaha yang Anda jalankan.</p>
                                        <select name="business_type" required
                                            class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-cuan-primary focus:ring-2 focus:ring-cuan-primary/20 outline-none text-gray-800 bg-white text-sm">
                                            <option value="">-- Pilih Jenis --</option>
                                            <option value="F&B">Food &amp; Beverage</option>
                                            <option value="Retail">Retail</option>
                                            <option value="Service">Jasa / Service</option>
                                            <option value="Other">Lainnya</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                                        <p class="text-[11px] text-gray-400 mb-2">Lebih spesifik, agar laporan penjualan lebih relevan.</p>
                                        <select name="business_category" required
                                            class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-cuan-primary focus:ring-2 focus:ring-cuan-primary/20 outline-none text-gray-800 bg-white text-sm">
                                            <option value="">-- Pilih Kategori --</option>
                                            <option value="Restaurant">Restoran</option>
                                            <option value="Cafe">Kafe</option>
                                            <option value="Bakery">Toko Roti</option>
                                            <option value="Grocery">Toko Kelontong</option>
                                            <option value="Fashion">Fashion</option>
                                            <option value="Electronics">Elektronik</option>
                                            <option value="Other">Lainnya</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="bg-blue-50/80 border border-blue-100 rounded-2xl p-4 flex gap-3 items-start text-sm">
                                    <div class="mt-0.5">
                                        <i class="fa-solid fa-circle-info text-blue-500"></i>
                                    </div>
                                    <p class="text-blue-800 leading-relaxed">
                                        Pastikan data yang Anda masukkan sudah benar. Jika ada perubahan di kemudian hari, Anda selalu dapat memperbarui informasi ini melalui menu <span class="font-semibold">Pengaturan Outlet</span>.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="mt-4 sm:mt-6 flex flex-col sm:flex-row sm:items-center gap-3 pt-5 border-t border-slate-100">
                            <button type="button" id="prevBtn" class="hidden px-4 sm:px-5 py-2.5 text-gray-500 font-medium hover:text-gray-800 transition-colors flex items-center gap-2 text-sm rounded-xl hover:bg-slate-100">
                                <i class="fa-solid fa-arrow-left text-xs"></i> 
                                <span>Sebelumnya</span>
                            </button>
                            
                            <div class="flex sm:ml-auto gap-3 justify-end">
                                <button type="button" id="nextBtn" class="px-6 sm:px-7 py-2.5 bg-cuan-primary hover:bg-cuan-dark text-white text-sm font-semibold rounded-xl shadow-[0_14px_30px_rgba(101,140,88,0.45)] hover:shadow-[0_18px_40px_rgba(101,140,88,0.55)] transition-all transform hover:-translate-y-[2px] active:translate-y-0 flex items-center gap-2">
                                    <span>Selanjutnya</span>
                                    <i class="fa-solid fa-arrow-right text-xs"></i>
                                </button>

                                <button type="submit" id="submitBtn" class="hidden px-6 sm:px-7 py-2.5 bg-gradient-to-r from-cuan-primary to-cuan-dark text-white text-sm font-semibold rounded-xl shadow-[0_16px_36px_rgba(49,105,78,0.60)] hover:shadow-[0_20px_46px_rgba(49,105,78,0.75)] transition-all transform hover:-translate-y-[2px] active:translate-y-0 flex items-center gap-2">
                                    <i class="fa-solid fa-check text-xs"></i>
                                    <span>Selesai &amp; Daftar</span>
                                </button>
                            </div>
                        </div>

                    </form>
                </section>
            </section>
        </div>
    </main>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        // --- Variables ---
        let currentStep = 1;
        const totalSteps = 4;
        let map, marker;
        
        // --- Elements ---
        const form = document.getElementById('outletForm');
        const nextBtn = document.getElementById('nextBtn');
        const prevBtn = document.getElementById('prevBtn');
        const submitBtn = document.getElementById('submitBtn');
        const progressBarFill = document.getElementById('progressBarFill');
        const stepCircles = document.querySelectorAll('.step-circle');
        const stepLabels = document.querySelectorAll('.step-circle + span');
        const currentStepLabel = document.getElementById('currentStepLabel');

        // --- Initialization ---
        document.addEventListener('DOMContentLoaded', () => {
            updateUI();
            
            // Logo Preview Logic
            const logoInput = document.getElementById('logoInput');
            const logoPreview = document.getElementById('logoPreview');
            const logoPlaceholder = document.getElementById('logoPlaceholder');
            const removeLogo = document.getElementById('removeLogo');

            logoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        logoPreview.src = e.target.result;
                        logoPreview.classList.remove('hidden');
                        logoPlaceholder.classList.add('hidden');
                        removeLogo.classList.remove('hidden');
                    }
                    reader.readAsDataURL(file);
                }
            });

            removeLogo.addEventListener('click', function() {
                logoInput.value = '';
                logoPreview.classList.add('hidden');
                logoPlaceholder.classList.remove('hidden');
                removeLogo.classList.add('hidden');
            });
        });

        // --- Navigation Logic ---
        nextBtn.addEventListener('click', () => {
            if (validateStep(currentStep)) {
                changeStep(currentStep + 1);
            }
        });

        prevBtn.addEventListener('click', () => {
            changeStep(currentStep - 1);
        });

        function goToStep(step) {
            // Only allow going back or to next immediate step if valid
            if (step < currentStep || (step === currentStep + 1 && validateStep(currentStep))) {
                changeStep(step);
            }
        }

        function changeStep(newStep) {
            if (newStep < 1 || newStep > totalSteps || newStep === currentStep) return;

            const currentContent = document.querySelector(`.step-content[data-step="${currentStep}"]`);
            const nextContent = document.querySelector(`.step-content[data-step="${newStep}"]`);

            // Animation Out
            currentContent.classList.add('exiting');
            
            setTimeout(() => {
                currentContent.classList.remove('active', 'exiting');
                nextContent.classList.add('active');
                
                currentStep = newStep;
                updateUI();

                // Initialize Map if entering Step 3
                if (currentStep === 3) {
                    setTimeout(initMap, 100);
                }
            }, 400); // Match animation duration
        }

        function updateUI() {
            // Progress Bar
            const percentage = ((currentStep - 1) / (totalSteps - 1)) * 100;
            progressBarFill.style.width = `${percentage}%`;

            // Step indicator text
            if (currentStepLabel) {
                currentStepLabel.textContent = currentStep;
            }

            // Step Circles
            stepCircles.forEach((circle, index) => {
                const stepNum = index + 1;
                if (stepNum <= currentStep) {
                    circle.classList.add('bg-cuan-primary', 'text-white', 'border-cuan-primary', 'shadow-md');
                    circle.classList.remove('text-gray-400', 'border-gray-300', 'bg-white');
                    if (stepLabels[index]) {
                        stepLabels[index].classList.add('text-cuan-primary');
                        stepLabels[index].classList.remove('text-gray-400', 'text-gray-700');
                    }
                } else {
                    circle.classList.remove('bg-cuan-primary', 'text-white', 'border-cuan-primary', 'shadow-md');
                    circle.classList.add('text-gray-400', 'border-gray-300', 'bg-white');
                    if (stepLabels[index]) {
                        stepLabels[index].classList.remove('text-cuan-primary');
                        stepLabels[index].classList.add('text-gray-400');
                    }
                }
            });

            // Buttons
            prevBtn.classList.toggle('hidden', currentStep === 1);
            if (currentStep === totalSteps) {
                nextBtn.classList.add('hidden');
                submitBtn.classList.remove('hidden');
            } else {
                nextBtn.classList.remove('hidden');
                submitBtn.classList.add('hidden');
            }
        }

        function validateStep(step) {
            const content = document.querySelector(`.step-content[data-step="${step}"]`);
            const inputs = content.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                    // Shake animation
                    (input.closest('.relative') || input).animate([
                        { transform: 'translateX(0)' },
                        { transform: 'translateX(-4px)' },
                        { transform: 'translateX(4px)' },
                        { transform: 'translateX(0)' }
                    ], { duration: 250 });
                    
                    input.addEventListener('input', () => {
                        input.classList.remove('border-red-500', 'ring-1', 'ring-red-500');
                    }, { once: true });
                }
            });

            if (step === 3) {
                const lat = document.getElementById('latInput').value;
                if (!lat) {
                    alert('Mohon pilih lokasi pada peta terlebih dahulu.');
                    isValid = false;
                }
            }

            return isValid;
        }

        // --- Map Logic (Leaflet) ---
        function initMap() {
            if (map) {
                setTimeout(() => {
                    map.invalidateSize();
                }, 100);
                return; // Already initialized
            }

            // Default: Indonesia Center
            map = L.map('map').setView([-2.5489, 118.0149], 5);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Click Event
            map.on('click', function(e) {
                setLocation(e.latlng.lat, e.latlng.lng);
            });

            // Search
            document.getElementById('searchBtn').addEventListener('click', searchLocation);
            document.getElementById('locationSearch').addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchLocation();
                }
            });

            // Geolocation
            document.getElementById('geoBtn').addEventListener('click', () => {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(position => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        map.setView([lat, lng], 16);
                        setLocation(lat, lng);
                    }, () => {
                        alert('Gagal mendapatkan lokasi. Pastikan izin lokasi sudah diaktifkan.');
                    });
                } else {
                    alert('Geolocation tidak didukung di browser ini.');
                }
            });
        }

        function setLocation(lat, lng) {
            if (marker) map.removeLayer(marker);
            
            marker = L.marker([lat, lng]).addTo(map);
            
            document.getElementById('latInput').value = lat;
            document.getElementById('lngInput').value = lng;

            // Reverse Geocoding (Nominatim)
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('addressDisplay').textContent = data.display_name || `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
                    // Auto-fill address textarea if empty
                    const addrInput = document.getElementById('addressInput');
                    if (!addrInput.value && data.display_name) {
                        addrInput.value = data.display_name;
                    }
                })
                .catch(() => {
                    document.getElementById('addressDisplay').textContent = `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
                });
        }

        function searchLocation() {
            const query = document.getElementById('locationSearch').value;
            if (!query) return;

            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.length > 0) {
                        const lat = parseFloat(data[0].lat);
                        const lng = parseFloat(data[0].lon);
                        map.setView([lat, lng], 16);
                        setLocation(lat, lng);
                    } else {
                        alert('Lokasi tidak ditemukan. Coba kata kunci lain.');
                    }
                })
                .catch(() => alert('Terjadi kesalahan saat mencari lokasi.'));
        }

    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar - CuanFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@700,500,400&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('assets/image/logo.svg') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            font-family: 'Satoshi', sans-serif;
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(12deg); }
            50% { transform: translateY(-20px) rotate(12deg); }
        }
        
        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
        .animate-scale-in { animation: scaleIn 0.5s ease-out forwards; }
        
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        
        .animate-on-load { opacity: 0; }
        
        /* Background Decorations */
        .green-blur {
            background: radial-gradient(circle at top left, rgba(49, 105, 78, 0.15) 0%, transparent 70%);
            filter: blur(60px);
        }
        
        .bg-pattern {
            animation: float 10s ease-in-out infinite;
        }
        
        .bg-pattern:nth-child(2) {
            animation-delay: 2s;
            animation-duration: 12s;
        }
        
        /* Form Elements */
        .input-box {
            transition: all 0.3s ease;
        }
        
        .input-box:focus-within {
            transform: translateY(-2px);
            border-color: #31694E;
            box-shadow: 0 4px 12px rgba(49, 105, 78, 0.08);
        }
        
        .btn-primary {
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(49, 105, 78, 0.2);
        }
        
        /* Progress Steps */
        .step-dot {
            transition: all 0.3s ease;
        }
        
        .step-line {
            transition: all 0.3s ease;
        }

        /* Checkbox & Radio */
        .form-checkbox:checked {
            background-color: #31694E;
            border-color: #31694E;
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
<body class="min-h-screen bg-white relative overflow-hidden flex items-center justify-center p-4">
    
    <!-- Decorative Background -->
    <div class="green-blur absolute -top-40 -left-40 w-96 h-96 pointer-events-none"></div>
    
    <div class="absolute bottom-0 right-0 w-full h-full pointer-events-none overflow-hidden">
        <div class="bg-pattern absolute bottom-10 right-10 w-48 h-48 border-2 border-cuan-dark opacity-10 rotate-12 rounded-3xl"></div>
        <div class="bg-pattern absolute bottom-32 right-32 w-32 h-32 border-2 border-cuan-green opacity-10 rotate-12 rounded-3xl"></div>
    </div>
    
    <!-- Register Card -->
    <div class="relative z-10 w-full max-w-[460px]" x-data="registerWizard()">
        <div class="bg-white/80 backdrop-blur-xl border border-gray-100 rounded-3xl p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] animate-on-load animate-scale-in">
            
            <!-- Logo -->
            <div class="flex justify-center mb-6 animate-on-load animate-fade-in-up delay-100">
                <img src="{{ asset('assets/image/full-logo.svg') }}" alt="CuanFlow Logo" class="h-9">
            </div>

            <!-- Progress Indicator -->
            <div class="flex items-center justify-center mb-8 px-8 animate-on-load animate-fade-in-up delay-100">
                <div class="flex items-center w-full relative">
                    <!-- Step 1 Dot -->
                    <div class="relative z-10">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300"
                             :class="step >= 1 ? 'bg-cuan-dark text-white shadow-lg shadow-cuan-dark/30' : 'bg-gray-200 text-gray-500'">
                            1
                        </div>
                    </div>
                    
                    <!-- Line 1-2 -->
                    <div class="flex-1 h-1 mx-2 rounded-full overflow-hidden bg-gray-100">
                        <div class="h-full bg-cuan-dark transition-all duration-500" :style="`width: ${step >= 2 ? '100%' : '0%'}`"></div>
                    </div>
                    
                    <!-- Step 2 Dot -->
                    <div class="relative z-10">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300"
                             :class="step >= 2 ? 'bg-cuan-dark text-white shadow-lg shadow-cuan-dark/30' : 'bg-gray-200 text-gray-500'">
                            2
                        </div>
                    </div>
                    
                    <!-- Line 2-3 -->
                    <div class="flex-1 h-1 mx-2 rounded-full overflow-hidden bg-gray-100">
                        <div class="h-full bg-cuan-dark transition-all duration-500" :style="`width: ${step >= 3 ? '100%' : '0%'}`"></div>
                    </div>
                    
                    <!-- Step 3 Dot -->
                    <div class="relative z-10">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300"
                             :class="step >= 3 ? 'bg-cuan-dark text-white shadow-lg shadow-cuan-dark/30' : 'bg-gray-200 text-gray-500'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                    </div>
                </div>
            </div>

             <!-- Error Messages -->
             @if ($errors->any())
                <div class="mb-5 p-3.5 bg-red-50 border border-red-100 rounded-xl animate-fade-in-up">
                    <ul class="text-xs font-medium text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form method="POST" action="{{ route('register') }}" x-ref="form" class="animate-on-load animate-fade-in-up delay-200">
                @csrf
                
                <!-- STEP 1: Info Personal -->
                <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Buat Akun Baru</h2>
                        <p class="text-sm text-gray-500 mt-1">Isi data diri Anda untuk memulai</p>
                    </div>

                    <div class="space-y-4">
                        <!-- Nama -->
                        <div class="relative input-box border border-gray-200 rounded-xl bg-gray-50/50 overflow-hidden group hover:border-gray-300">
                            <input type="text" name="name" x-model="form.name" required
                                class="block w-full px-4 py-3 bg-transparent text-gray-900 text-sm placeholder-gray-400 focus:outline-none"
                                placeholder="Nama Lengkap">
                        </div>

                        <!-- Email -->
                        <div class="relative input-box border border-gray-200 rounded-xl bg-gray-50/50 overflow-hidden group hover:border-gray-300">
                             <input type="email" name="email" x-model="form.email" required
                                class="block w-full px-4 py-3 bg-transparent text-gray-900 text-sm placeholder-gray-400 focus:outline-none"
                                placeholder="Email Address">
                        </div>

                        <!-- Phone -->
                         <div class="relative input-box border border-gray-200 rounded-xl bg-gray-50/50 overflow-hidden group hover:border-gray-300">
                             <input type="tel" name="phone" x-model="form.phone" required
                                class="block w-full px-4 py-3 bg-transparent text-gray-900 text-sm placeholder-gray-400 focus:outline-none"
                                placeholder="Nomor Telepon">
                        </div>

                        <button type="button" @click="nextStep()"
                            class="btn-primary w-full py-3 px-4 bg-gray-900 hover:bg-black text-white text-sm font-bold rounded-xl shadow-lg transition-all flex items-center justify-center gap-2 mt-2">
                            <span>Lanjutkan</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </button>
                    </div>

                     <div class="my-6 flex items-center gap-3">
                        <div class="h-px bg-gray-200 flex-1"></div>
                        <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">Atau</span>
                        <div class="h-px bg-gray-200 flex-1"></div>
                    </div>
            
                    <a href="{{ route('auth.google') }}" class="btn-google w-full flex items-center justify-center gap-3 py-3 px-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition-all group bg-white">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        <span class="text-sm font-semibold text-gray-600 group-hover:text-gray-800">Daftar dengan Google</span>
                    </a>
                </div>

                <!-- STEP 2: Password & Security -->
                <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0" style="display: none;">
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Amankan Akun</h2>
                        <p class="text-sm text-gray-500 mt-1">Buat password yang kuat</p>
                    </div>

                    <div class="space-y-4">
                        <!-- Password -->
                        <div x-data="{ show: false }">
                            <div class="relative input-box border border-gray-200 rounded-xl bg-gray-50/50 overflow-hidden group hover:border-gray-300">
                                <input :type="show ? 'text' : 'password'" name="password" x-model="form.password"
                                    class="block w-full px-4 py-3 bg-transparent text-gray-900 text-sm placeholder-gray-400 focus:outline-none"
                                    placeholder="Password (Min. 8 karakter)">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.206 3.84-4.8 6.745-9.01 6.985" /></svg>
                                    <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29" /></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="relative input-box border border-gray-200 rounded-xl bg-gray-50/50 overflow-hidden group hover:border-gray-300">
                             <input type="password" name="password_confirmation" x-model="form.password_confirmation"
                                class="block w-full px-4 py-3 bg-transparent text-gray-900 text-sm placeholder-gray-400 focus:outline-none"
                                placeholder="Konfirmasi Password">
                        </div>

                         <!-- Terms -->
                        <div class="flex items-start gap-2 pt-2">
                            <input type="checkbox" name="terms" id="terms" required checked
                                class="mt-1 h-4 w-4 rounded border-gray-300 text-cuan-dark focus:ring-cuan-dark cursor-pointer">
                            <label for="terms" class="text-xs text-gray-500 leading-snug">
                                Saya setuju dengan <a href="{{ route('legal.terms') }}" target="_blank" class="font-semibold text-gray-900 hover:text-cuan-green underline">Syarat & Ketentuan</a> dan Kebijakan Privasi
                            </label>
                        </div>

                        <div class="flex gap-3 mt-4">
                            <button type="button" @click="step = 1"
                                class="w-1/3 py-3 px-4 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-xl transition-all">
                                Kembali
                            </button>
                            <button type="button" @click="submitForm()"
                                class="w-2/3 py-3 px-4 bg-gray-900 hover:bg-black text-white text-sm font-bold rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                                <span>Daftar Sekarang</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: Loading / Success State (Managed via standard form submission mostly, but visualized here if needed) -->
                <!-- We stick to form submission for simplicity, but if errors occur page reloads to step 1. -->

            </form>
            
            <p class="mt-8 text-center text-sm text-gray-500 animate-on-load animate-fade-in-up delay-300">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="font-bold text-gray-900 hover:text-cuan-green transition-colors">Masuk</a>
            </p>
        </div>
        
        <p class="text-center text-xs text-gray-400 mt-6 animate-on-load animate-fade-in-up delay-300">
            &copy; {{ date('Y') }} CuanFlow. All rights reserved.
        </p>
    </div>

    <script>
        function registerWizard() {
            return {
                step: 1,
                form: {
                    name: '{{ old('name') }}',
                    email: '{{ old('email') }}',
                    phone: '{{ old('phone') }}',
                    password: '',
                    password_confirmation: ''
                },
                nextStep() {
                    if (!this.form.name || !this.form.email || !this.form.phone) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Mohon Lengkapi Data',
                            text: 'Silakan isi nama, email, dan nomor telepon.',
                            confirmButtonText: 'Oke',
                             customClass: {
                                popup: 'swal2-popup',
                                confirmButton: 'swal2-confirm'
                            },
                        });
                        return;
                    }
                    this.step = 2;
                },
                submitForm() {
                     if (!this.form.password || !this.form.password_confirmation) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Password Kosong',
                            text: 'Silakan isi password dan konfirmasi password.',
                            confirmButtonText: 'Oke',
                            customClass: {
                                popup: 'swal2-popup',
                                confirmButton: 'swal2-confirm'
                            },
                        });
                        return;
                    }
                    if (this.form.password !== this.form.password_confirmation) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Password Tidak Sama',
                            text: 'Konfirmasi password tidak cocok.',
                            confirmButtonText: 'Perbaiki',
                            customClass: {
                                popup: 'swal2-popup',
                                confirmButton: 'swal2-confirm'
                            },
                        });
                        return;
                    }
                    this.$refs.form.submit();
                }
            }
        }
    </script>
</body>
</html>
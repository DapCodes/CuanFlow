@extends('layouts.app')

@section('title', 'Verifikasi Password')

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-500 font-medium">Riwayat Penarikan</span>
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Verifikasi Akses</span>
</li>
@endsection

@section('content')
<main class="flex-grow flex items-center justify-center py-12 px-4 bg-gray-50">
    <div class="max-w-md w-full">
        {{-- CARD CONTAINER --}}
        <div class="bg-white border text-center border-gray-200 rounded-[2rem] shadow-xl overflow-hidden">
            {{-- TOP ACCENT --}}
            <div class="h-2 bg-gradient-to-r from-cuan-green to-cuan-dark"></div>
            
            <div class="p-8">
                <div class="text-center space-y-4 mb-8">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-[1.5rem] bg-cuan-green/10 text-cuan-green border border-cuan-green/20 shadow-inner">
                        <i class="fas fa-shield-halved text-3xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-gray-900 tracking-tight">Verifikasi Keamanan</h2>
                        <p class="mt-2 text-sm text-gray-500 font-medium">
                            Masukkan password akun Anda untuk mengonfirmasi akses penarikan saldo.
                        </p>
                    </div>
                </div>

                @if($isLocked)
                {{-- LOCKED STATE --}}
                <div class="bg-red-50 border border-red-100 rounded-2xl p-6 text-center animate-pulse">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm text-red-500">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h3 class="text-sm font-bold text-red-800 uppercase tracking-widest mb-1">Akses Terkunci</h3>
                    <p class="text-xs text-red-600/80 mb-4 font-medium">Terlalu banyak percobaan gagal.</p>
                    <div class="inline-block px-4 py-2 bg-white rounded-lg border border-red-200 text-2xl font-black text-red-600 font-mono tracking-tighter" id="countdown">
                        {{ gmdate('i:s', $remainingSeconds) }}
                    </div>
                    <p class="text-[10px] text-red-400 mt-3 italic font-medium">Silakan coba lagi setelah waktu habis</p>
                </div>

                <script>
                    let remaining = {{ $remainingSeconds }};
                    const countdownEl = document.getElementById('countdown');
                    
                    const interval = setInterval(() => {
                        remaining--;
                        if (remaining <= 0) {
                            clearInterval(interval);
                            window.location.reload();
                            return;
                        }
                        const minutes = Math.floor(remaining / 60);
                        const seconds = remaining % 60;
                        countdownEl.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                    }, 1000);
                </script>
                @else
                {{-- PASSWORD FORM --}}
                <form class="space-y-6" action="{{ route('withdraw.confirm-password.post') }}" method="POST">
                    @csrf
                    
                    <div class="space-y-4">
                        @if($attempts > 0)
                        <div class="bg-amber-50 border border-amber-100 rounded-xl p-3 flex items-start gap-3">
                            <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5 text-xs"></i>
                            <p class="text-[11px] text-amber-800 font-medium leading-tight">
                                Percobaan gagal: {{ $attempts }}/3. 
                                @if($attempts >= 2)
                                <span class="font-bold underline">Kesalahan berikutnya akan mengunci akun 5 menit.</span>
                                @endif
                            </p>
                        </div>
                        @endif

                        <div class="space-y-3 text-left">
                            <label for="password" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest pl-2">Password Konfirmasi</label>
                            <div class="relative">
                                <input type="password" 
                                       name="password" 
                                       id="password" 
                                       required
                                       autofocus
                                       class="w-full px-6 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green focus:bg-white transition-all outline-none font-bold text-sm @error('password') border-red-300 @enderror"
                                       placeholder="••••••••">
                                <button type="button" 
                                        onclick="togglePassword()"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center text-gray-400 hover:text-cuan-green hover:bg-gray-100/50 rounded-xl transition-all">
                                    <i class="fas fa-eye text-xs" id="toggleIcon"></i>
                                </button>
                            </div>
                            @error('password')
                            <p class="mt-2 text-[10px] uppercase tracking-widest pl-2 text-red-500 font-bold flex items-center gap-1.5">
                                <i class="fas fa-circle-exclamation"></i>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <button type="submit" 
                                class="w-full py-4 bg-black text-white font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-gray-200 hover:bg-cuan-green hover:-translate-y-0.5 active:scale-95 transition-all text-[10px] flex items-center justify-center gap-3">
                            <i class="fas fa-unlock text-[10px]"></i>
                            Buka Akses Penarikan
                        </button>
                    </div>
                </form>

                <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-xs font-bold text-gray-400 hover:text-gray-600 transition-colors group">
                        <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
                        Batalkan & Kembali
                    </a>
                </div>

                <script>
                    function togglePassword() {
                        const input = document.getElementById('password');
                        const icon = document.getElementById('toggleIcon');
                        
                        if (input.type === 'password') {
                            input.type = 'text';
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        } else {
                            input.type = 'password';
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        }
                    }
                </script>
                @endif
            </div>
        </div>
        
        <p class="mt-8 text-center text-[10px] text-gray-400 uppercase tracking-widest font-bold">
            Powered by CuanFlow Security System
        </p>
    </div>
</main>
@endsection

@extends('layouts.app')

@section('title', 'Verifikasi Password')

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Riwayat Penarikan</span>
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Verifikasi</span>
</li>
@endsection

@section('content')
<main class="flex-grow flex items-center justify-center py-12 px-4 bg-gray-50">
    <div class="max-w-md w-full">
        {{-- CARD CONTAINER --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-xl overflow-hidden">
            {{-- TOP ACCENT --}}
            <div class="h-2 bg-gradient-to-r from-teal-500 to-teal-600"></div>
            
            <div class="p-8">
                <div class="text-center space-y-4 mb-8">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-teal-50 text-teal-600 border border-teal-100 shadow-inner">
                        <i class="fas fa-shield-halved text-3xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 tracking-tight">Verifikasi Keamanan</h2>
                        <p class="mt-2 text-sm text-gray-500">
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

                        <div class="space-y-2">
                            <label for="password" class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Password Konfirmasi</label>
                            <div class="relative group">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-teal-500 transition-colors">
                                    <i class="fas fa-key"></i>
                                </span>
                                <input type="password" 
                                       name="password" 
                                       id="password" 
                                       required
                                       autofocus
                                       class="w-full pl-11 pr-12 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 focus:bg-white transition-all outline-none font-medium @error('password') border-red-300 @enderror"
                                       placeholder="••••••••">
                                <button type="button" 
                                        onclick="togglePassword()"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-white rounded-lg transition-all">
                                    <i class="fas fa-eye text-xs" id="toggleIcon"></i>
                                </button>
                            </div>
                            @error('password')
                            <p class="mt-2 text-[11px] text-red-600 font-medium flex items-center gap-1">
                                <i class="fas fa-circle-exclamation"></i>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <button type="submit" 
                                class="w-full py-4 bg-gray-900 text-white font-bold rounded-xl shadow-lg shadow-gray-200 hover:bg-teal-600 hover:shadow-teal-500/30 hover:-translate-y-0.5 transition-all text-sm flex items-center justify-center gap-3">
                            <i class="fas fa-unlock text-xs"></i>
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

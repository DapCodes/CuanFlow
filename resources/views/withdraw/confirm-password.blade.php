@extends('layouts.app')

@section('title', 'Verifikasi Password')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-16 w-16 bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl flex items-center justify-center">
                <i class="fas fa-lock text-white text-2xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Verifikasi Password
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Masukkan password Anda untuk melanjutkan ke halaman penarikan saldo
            </p>
        </div>

        @if($isLocked)
        <!-- Locked State -->
        <div class="bg-red-50 border border-red-200 rounded-xl p-6 text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-lock text-red-500 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-red-800 mb-2">Akun Terkunci</h3>
            <p class="text-red-600 mb-4">Terlalu banyak percobaan yang gagal.</p>
            <div class="text-2xl font-bold text-red-700" id="countdown">
                {{ gmdate('i:s', $remainingSeconds) }}
            </div>
            <p class="text-sm text-red-500 mt-2">Silakan coba lagi setelah waktu habis</p>
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
        <!-- Password Form -->
        <form class="mt-8 space-y-6" action="{{ route('withdraw.confirm-password.post') }}" method="POST">
            @csrf
            
            <div class="bg-white rounded-xl shadow-lg p-6 space-y-4">
                @if($attempts > 0)
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-3">
                    <p class="text-sm text-amber-700">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Percobaan gagal: {{ $attempts }}/3. 
                        @if($attempts >= 2)
                        <span class="font-semibold">Satu kesalahan lagi akan mengunci akun selama 5 menit.</span>
                        @endif
                    </p>
                </div>
                @endif

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <input type="password" 
                               name="password" 
                               id="password" 
                               required
                               autofocus
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('password') border-red-300 @enderror"
                               placeholder="Masukkan password Anda">
                        <button type="button" 
                                onclick="togglePassword()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" 
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all">
                    <i class="fas fa-check mr-2"></i>
                    Verifikasi
                </button>
            </div>
        </form>

        <div class="text-center">
            <a href="{{ route('sales.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left mr-1"></i>
                Kembali ke Penjualan
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
@endsection

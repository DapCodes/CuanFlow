<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Dibatasi - CuanFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <!-- Main Modal Container -->
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden relative">
        
        <!-- Decoration Header -->
        <div class="h-32 bg-gradient-to-br from-red-500 to-pink-600 flex items-center justify-center relative overflow-hidden">
            <div class="absolute inset-0 opacity-20">
                <i class="fas fa-lock text-9xl absolute -bottom-4 -right-4 transform rotate-12"></i>
                <i class="fas fa-shield-alt text-6xl absolute top-4 left-4 transform -rotate-12"></i>
            </div>
            <div class="relative z-10 bg-white/20 backdrop-blur-sm p-4 rounded-full shadow-lg">
                <i class="fas fa-lock text-4xl text-white"></i>
            </div>
        </div>

        <!-- Content -->
        <div class="p-8 text-center">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Akses Dibatasi</h2>
            
            @php
                $reason = session('employee_lock_reason', 'unknown');
                $message = '';
                $subMessage = '';

                switch($reason) {
                    case 'no_subscription':
                        $message = 'Outlet Belum Berlangganan';
                        $subMessage = 'Pemilik outlet ini belum memiliki langganan aktif. Silakan hubungi pemilik outlet untuk mengaktifkan layanan.';
                        break;
                    case 'expired':
                        $message = 'Langganan Outlet Berakhir';
                        $subMessage = 'Masa aktif langganan outlet ini telah habis. Silakan hubungi pemilik outlet untuk memperpanjang langganan.';
                        break;
                    case 'feature_unavailable':
                        $message = 'Fitur Tidak Tersedia';
                        $subMessage = 'Paket langganan outlet saat ini tidak mencakup Manajemen Karyawan. Hubungi pemilik untuk upgrade paket.';
                        break;
                    default:
                        $message = 'Masalah Langganan';
                        $subMessage = 'Terdapat masalah dengan status langganan outlet ini. Silakan hubungi pemilik outlet.';
                }
            @endphp

            <p class="text-lg font-semibold text-red-600 mb-3">{{ $message }}</p>
            <p class="text-gray-600 leading-relaxed mb-6">
                {{ $subMessage }}
            </p>

            <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-6">
                <div class="flex items-start space-x-3">
                    <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                    <div class="text-left text-sm text-blue-700">
                        <p class="font-medium">Info untuk Karyawan:</p>
                        <p>Anda tidak dapat mengakses dashboard sampai pemilik outlet menyelesaikan masalah langganan ini.</p>
                    </div>
                </div>
            </div>

            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 text-white font-medium py-3 px-6 rounded-xl transition-all duration-200 flex items-center justify-center space-x-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Keluar Aplikasi</span>
                </button>
            </form>
        </div>
        
        <!-- Footer Info -->
        <div class="bg-gray-50 p-4 text-center border-t border-gray-100">
            <p class="text-xs text-gray-400">
                &copy; {{ date('Y') }} CuanFlow. All rights reserved.
            </p>
        </div>
    </div>

    <!-- Background Animation (Optional) -->
    <div class="fixed inset-0 -z-10 pointer-events-none">
        <div class="absolute top-0 left-0 w-full h-full bg-gray-100 opacity-50"></div>
    </div>

</body>
</html>

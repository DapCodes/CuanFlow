<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Sedang Maintenance - CuanFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@700,500,400&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('assets/image/logo.svg') }}" type="image/x-icon">
    <style>
        body {
            font-family: 'Satoshi', sans-serif;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
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

        @keyframes pulseDot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.75); }
        }

        .animate-fade-in-up { animation: fadeInUp 0.8s ease-out forwards; }
        .animate-scale-in   { animation: scaleIn 0.6s ease-out forwards; }

        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }

        .animate-on-load { opacity: 0; }

        .btn-primary { transition: all 0.3s ease; }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(49, 105, 78, 0.2);
        }

        .green-blur {
            background: radial-gradient(circle at top left, rgba(49, 105, 78, 0.15) 0%, transparent 70%);
            filter: blur(60px);
        }

        .bg-pattern { animation: float 10s ease-in-out infinite; }
        .bg-pattern:nth-child(2) { animation-delay: 2s; animation-duration: 12s; }
        .bg-pattern:nth-child(3) { animation-delay: 4s; animation-duration: 14s; }

        .dot { animation: pulseDot 1.4s ease-in-out infinite; }
        .dot:nth-child(2) { animation-delay: 0.25s; }
        .dot:nth-child(3) { animation-delay: 0.5s; }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'cuan-yellow': '#F0E491',
                        'cuan-olive':  '#BBC863',
                        'cuan-green':  '#658C58',
                        'cuan-dark':   '#31694E',
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-white relative overflow-hidden flex items-center justify-center">

    <!-- Background blur -->
    <div class="green-blur absolute -top-40 -left-40 w-96 h-96 pointer-events-none"></div>

    <!-- Decorative floating shapes (same as 404) -->
    <div class="absolute bottom-0 right-0 w-full h-full pointer-events-none overflow-hidden">
        <div class="bg-pattern absolute bottom-10 right-10 w-48 h-48 sm:w-64 sm:h-64 border-2 border-cuan-dark opacity-10 rotate-12 rounded-3xl"></div>
        <div class="bg-pattern absolute bottom-32 right-32 w-32 h-32 sm:w-40 sm:h-40 border-2 border-cuan-green opacity-10 rotate-12 rounded-3xl"></div>
        <div class="bg-pattern absolute bottom-20 right-52 w-24 h-24 sm:w-32 sm:h-32 border-2 border-cuan-olive opacity-10 rotate-12 rounded-3xl"></div>
    </div>

    <div class="relative z-10 w-full max-w-2xl px-6 sm:px-8 py-12 text-center">

        <!-- Logo -->
        <div class="mb-10 animate-on-load animate-scale-in flex justify-center animate-fade-in-up delay-100">
            <img
                src="{{ asset('assets/image/full-logo.svg') }}"
                alt="CuanFlow Logo"
                class="w-full max-w-[140px] sm:max-w-[160px] h-auto"
            />
        </div>

        <!-- Icon -->
        <div class="mb-8 flex justify-center animate-on-load animate-fade-in-up delay-200">
            <div class="w-24 h-24 bg-cuan-dark/5 rounded-3xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-cuan-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l5.654-4.654m5.614-4.926.933-.933a3.75 3.75 0 0 1 5.304 5.304l-.933.933m-5.304-5.304-3.03 2.496c-.14.468-.382.89-.766 1.208" />
                </svg>
            </div>
        </div>

        <!-- Heading -->
        <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4 animate-on-load animate-fade-in-up delay-200">
            Sistem Sedang Maintenance
        </h2>

        <!-- Description -->
        <p class="text-gray-600 text-sm sm:text-base leading-relaxed mb-8 max-w-lg mx-auto animate-on-load animate-fade-in-up delay-300">
            Kami sedang melakukan pemeliharaan rutin untuk memastikan sistem CuanFlow tetap optimal dan berjalan dengan baik. Harap bersabar, kami segera kembali!
        </p>

        <!-- Status indicator -->
        <div class="inline-flex items-center gap-3 px-5 py-3 bg-gray-50 border border-gray-100 rounded-xl mb-10 animate-on-load animate-fade-in-up delay-300">
            <div class="flex items-center gap-1">
                <span class="dot w-1.5 h-1.5 bg-cuan-dark rounded-full inline-block"></span>
                <span class="dot w-1.5 h-1.5 bg-cuan-dark rounded-full inline-block"></span>
                <span class="dot w-1.5 h-1.5 bg-cuan-dark rounded-full inline-block"></span>
            </div>
        </div>

        <!-- CTA -->
        <div class="flex justify-center animate-on-load animate-fade-in-up delay-400">
            <a
                href="{{ url('/') }}"
                class="btn-primary px-8 py-3.5 bg-cuan-dark text-white font-semibold rounded-lg text-base"
            >
                Coba Lagi
            </a>
        </div>

        <p class="text-gray-400 text-xs mt-16">© {{ date('Y') }} CuanFlow. All rights reserved.</p>
    </div>

</body>
</html>
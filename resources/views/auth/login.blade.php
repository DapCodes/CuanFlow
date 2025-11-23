<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CuanFlow - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@700,500,400&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Satoshi', sans-serif;
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
<body class="h-screen flex overflow-hidden">
    
    <!-- Left Section - Green Background -->
    <div class="hidden lg:flex lg:w-1/2 bg-cuan-dark text-white px-12 py-10 flex-col justify-between relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0">
            <div class="absolute top-24 right-24 w-80 h-80 border-2 border-white opacity-10 rotate-12 rounded-3xl"></div>
            <div class="absolute top-40 right-12 w-64 h-64 border-2 border-white opacity-10 rotate-12 rounded-3xl"></div>
            <div class="absolute top-56 right-0 w-48 h-48 border-2 border-white opacity-10 rotate-12 rounded-3xl"></div>
        </div>
        
        <!-- Asterisk Icon -->
        <div class="relative z-10">
            <svg width="70" height="70" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M40 10V70M10 40H70M20 20L60 60M60 20L20 60" stroke="#F0E491" stroke-width="8" stroke-linecap="round"/>
            </svg>
        </div>
        
        <!-- Main Content -->
        <div class="relative z-10 mb-8">
            <h1 class="text-5xl font-bold mb-5 leading-tight">
                Hello<br/>SobatCuan!👋
            </h1>
            <p class="text-lg max-w-md leading-relaxed text-cuan-yellow">
               Atur dan pantau perkembangan bisnismu tanpa ribet bersama <i>CuanFlow</i>. Semua proses menjadi lebih cepat, lebih teratur, dan pastinya lebih nyaman untuk dikerjakan kapan pun kamu butuh.
            </p>
        </div>
        
        <!-- Footer -->
        <p class="relative z-10 text-sm opacity-70">© 2025 CuanFlow. All rights reserved.</p>
    </div>
    
    <!-- Right Section - Login Form -->
    <div class="w-full lg:w-1/2 bg-white flex items-center justify-center px-8 py-10">
        <div class="w-full max-w-md">
            <!-- Header -->
            <div class="mb-6">
                <h2 class="text-3xl font-bold text-gray-900">CuanFlow</h2>
            </div>
            
            <h3 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang!</h3>
            
            <p class="text-gray-600 mb-8 text-sm">
                Belum punya akun? 
                <a href="#" class="text-gray-900 font-semibold underline hover:text-cuan-green">Buat akun sekarang!.</a>
                <br/>Ini GRATIS, gunakan waktumu!
            </p>
            
            <!-- Login Form -->
            <form class="space-y-5">
                <!-- Email Input -->
                <div>
                    <input 
                        type="email" 
                        placeholder="youremail@example.com"
                        class="w-full px-0 py-2.5 text-gray-900 border-b-2 border-gray-300 focus:border-cuan-dark outline-none placeholder-gray-900 text-base"
                    />
                </div>
                
                <!-- Password Input -->
                <div>
                    <input 
                        type="password" 
                        placeholder="Password"
                        class="w-full px-0 py-2.5 text-gray-900 border-b-2 border-gray-300 focus:border-cuan-dark outline-none placeholder-gray-500 text-base"
                    />
                </div>
                
                <!-- Login Button -->
                <button 
                    type="submit"
                    class="w-full py-3.5 bg-black text-white font-semibold rounded-lg hover:bg-gray-800 transition-colors text-base mt-6"
                >
                    Login Now
                </button>
                
                <!-- Google Login Button -->
                {{-- <button 
                    type="button"
                    class="w-full py-3.5 bg-white border-2 border-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-center gap-3"
                >
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19.9895 10.1871C19.9895 9.36767 19.9214 8.76973 19.7742 8.14966H10.1992V11.848H15.8195C15.7062 12.7671 15.0943 14.1512 13.7346 15.0813L13.7155 15.2051L16.7429 17.4969L16.9527 17.5174C18.879 15.7789 19.9895 13.221 19.9895 10.1871Z" fill="#4285F4"/>
                        <path d="M10.1993 19.9313C12.9527 19.9313 15.2643 19.0454 16.9527 17.5174L13.7346 15.0813C12.8734 15.6682 11.7176 16.0779 10.1993 16.0779C7.50243 16.0779 5.21352 14.3395 4.39759 11.9366L4.27799 11.9465L1.13003 14.3273L1.08887 14.4391C2.76588 17.6945 6.21061 19.9313 10.1993 19.9313Z" fill="#34A853"/>
                        <path d="M4.39748 11.9366C4.18219 11.3166 4.05759 10.6521 4.05759 9.96565C4.05759 9.27909 4.18219 8.61473 4.38615 7.99466L4.38045 7.8626L1.19304 5.44366L1.08876 5.49214C0.397576 6.84305 0.000976562 8.36008 0.000976562 9.96565C0.000976562 11.5712 0.397576 13.0882 1.08876 14.4391L4.39748 11.9366Z" fill="#FBBC05"/>
                        <path d="M10.1993 3.85336C12.1142 3.85336 13.406 4.66168 14.1425 5.33717L17.0207 2.59107C15.253 0.985496 12.9527 0 10.1993 0C6.2106 0 2.76588 2.23672 1.08887 5.49214L4.38626 7.99466C5.21352 5.59183 7.50242 3.85336 10.1993 3.85336Z" fill="#EB4335"/>
                    </svg>
                    Login with Google
                </button> --}}
                
                <!-- Forgot Password -->
                <p class="text-center text-gray-600 text-sm pt-2">
                    Lupa Password?
                    <a href="#" class="text-gray-900 font-semibold underline hover:text-cuan-green">Klik Disini</a>
                </p>
            </form>
        </div>
    </div>
    
</body>
</html>
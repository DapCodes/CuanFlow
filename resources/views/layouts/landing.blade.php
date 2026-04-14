<!DOCTYPE html>
<html lang="id">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Flow — Ekosistem Digital Bisnis & Konsumen')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <link rel="shortcut icon" href="{{ asset('assets/image/logo.svg') }}" type="image/x-icon">
    
    {{-- Preload font to prevent delay/jeda --}}
    <link rel="preload" href="{{ asset('landingstatic/GreatVibes-Regular.ttf') }}" as="font" type="font/ttf" crossorigin>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    
    <style>
      @font-face {
        font-family: 'Great Vibes';
        src: url('{{ asset('landingstatic/GreatVibes-Regular.ttf') }}') format('truetype');
        font-weight: normal;
        font-style: normal;
        font-display: swap;
      }

      :root {
        --ink: #1a1a1a;
        --ink-2: #4a4a4a;
        --ink-3: #888;
        --paper: #f5f3ef;
        --paper-2: #edeae4;
        --white: #ffffff;
        --accent: #31694E;
        --accent-light: #e8f2ec;
        --accent-mid: #658C58;
        --navy: #1e2d3d;
        --yellow: #F0E491;
        --olive: #BBC863;
        --green: #658C58;
        --dark-green: #31694E;
        --serif: 'DM Serif Display', Georgia, serif;
        --sans: 'DM Sans', sans-serif;
        --cursive: 'Great Vibes', cursive;
        --glass: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(255, 255, 255, 0.3);
      }

      *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
      html { scroll-behavior: smooth; scroll-padding-top: 80px; overflow-x: hidden; }
      ::-webkit-scrollbar { width: 4px; }
      ::-webkit-scrollbar-track { background: var(--paper); }
      ::-webkit-scrollbar-thumb { background: var(--ink-3); border-radius: 2px; }
      body { font-family: var(--sans); background-color: var(--paper); color: var(--ink); overflow-x: hidden; font-size: 16px; line-height: 1.6; }

      /* NAVBAR */
      nav { position: fixed; top: 0; left: 0; right: 0; z-index: 900; height: 72px; display: flex; align-items: center; padding: 0 5%; transition: background 0.4s ease, box-shadow 0.4s ease; }
      nav.scrolled { background: var(--glass); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1); border-bottom: 1px solid var(--glass-border); }
      .scroll-progress { position: fixed; top: 0; left: 0; height: 3px; background: linear-gradient(90deg, var(--accent), var(--olive)); z-index: 1000; width: 0%; transition: width 0.1s ease-out; }
      .nav-inner { display: flex; align-items: center; justify-content: space-between; width: 100%; max-width: 1200px; margin: 0 auto; }
      .nav-logo { font-family: var(--serif); font-size: 1.75rem; color: var(--ink); text-decoration: none; letter-spacing: -0.02em; }
      .nav-links { display: flex; list-style: none; gap: 36px; align-items: center; }
      .nav-links a { text-decoration: none; color: var(--ink-2); font-size: 0.875rem; font-weight: 500; transition: all 0.3s ease; padding: 6px 12px; border-radius: 8px; }
      .nav-links a:hover, .nav-links a.active { color: var(--accent); background: var(--accent-light); }
      .nav-cta { background: var(--ink); color: var(--white); padding: 9px 22px; border-radius: 100px; text-decoration: none; font-size: 0.85rem; font-weight: 500; transition: all 0.25s; }
      .nav-cta:hover { background: var(--accent); transform: translateY(-1px); }

      /* FOOTER */
      footer { background: var(--ink); color: rgba(255,255,255,0.6); padding: 80px 5% 40px; border-top: 1px solid rgba(255,255,255,0.06); }
      .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 48px; max-width: 1200px; margin: 0 auto 60px; }
      .footer-brand .nav-logo { color: var(--white); display: block; margin-bottom: 16px; }
      .footer-brand p { font-size: 0.875rem; line-height: 1.65; font-weight: 300; max-width: 280px; color: rgba(255,255,255,0.4); margin-top: 0; }
      footer h5 { font-size: 0.8rem; font-weight: 500; color: var(--white); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 18px; font-family: var(--sans); }
      .footer-links { list-style: none; display: flex; flex-direction: column; gap: 10px; }
      .footer-links a { color: rgba(255,255,255,0.4); text-decoration: none; font-size: 0.875rem; font-weight: 300; transition: color 0.25s; }
      .footer-links a:hover { color: var(--accent-mid); }
      .footer-bottom { border-top: 1px solid rgba(255,255,255,0.06); padding-top: 28px; max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; color: rgba(255,255,255,0.3); flex-wrap: wrap; gap: 12px; }
      .footer-socials { display: flex; gap: 18px; }
      .footer-socials a { color: rgba(255,255,255,0.3); font-size: 0.8rem; text-decoration: none; transition: color 0.25s; }
      .footer-socials a:hover { color: var(--white); }

      @media (max-width: 991px) {
        .footer-grid { grid-template-columns: 1fr 1fr; gap: 40px; }
      }
      @media (max-width: 767px) {
        .nav-links { display: none; }
        .footer-grid { grid-template-columns: 1fr; gap: 40px; }
        .footer-bottom { flex-direction: column; gap: 20px; text-align: center; }
      }

      /* SHARED COMPONENTS */
      .section-header { text-align: center; margin-bottom: 60px; }
      .section-header h2 { font-family: var(--serif); font-size: clamp(2.2rem, 5vw, 3.5rem); color: var(--ink); line-height: 1.1; margin-bottom: 20px; }
      .section-header p { color: var(--ink-2); max-width: 600px; margin: 0 auto; font-size: 1.1rem; font-weight: 300; }
      
      .page-hero { padding: 180px 5% 160px; background: var(--paper-2); text-align: center; overflow: hidden; position: relative; }
      .page-hero h1 { font-family: var(--serif); font-size: clamp(2.8rem, 5vw, 4.2rem); line-height: 1.1; color: var(--ink); margin-bottom: 20px; }
      .page-hero h1 em { font-family: var(--cursive); font-style: normal; color: var(--accent); font-size: 1.15em; font-weight: 400; display: block; margin-top: 2px; }
      .page-hero::after { content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 120px; background: var(--paper); clip-path: ellipse(60% 100% at 50% 100%); }

      .container { max-width: 1200px; margin: 0 auto; padding: 0 5%; }
      .py-120 { padding: 120px 0; }
    </style>
    @stack('styles')
</head>
<body>
    <div id="scroll-progress" class="scroll-progress"></div>
    <nav id="nav">
        <div class="nav-inner">
            <a href="{{ route('welcome') }}" class="nav-logo">
                <img src="{{ asset('landingstatic/logo.png') }}" alt="Flow" style="height:32px; width:auto; display:block;">
            </a>
            <ul class="nav-links">
                <li><a href="{{ route('welcome') }}" class="{{ request()->routeIs('welcome') ? 'active' : '' }}">Beranda</a></li>
                <li><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">Tentang Kami</a></li>
                <li><a href="{{ route('blog') }}" class="{{ request()->routeIs('blog') ? 'active' : '' }}">Blog</a></li>
                <li><a href="{{ route('career') }}" class="{{ request()->routeIs('career') ? 'active' : '' }}">Karir</a></li>
                <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">Hubungi Kami</a></li>
            </ul>
            <a href="{{ route('login') }}" class="nav-cta">Mulai Sekarang</a>
        </div>
    </nav>

    @yield('content')

    <footer>
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="{{ route('welcome') }}" class="nav-logo">
                    <img src="{{ asset('landingstatic/logo.png') }}" alt="Flow" style="height:36px; width:auto; display:block; filter: brightness(0) invert(1);">
                </a>
                <p>Ekosistem digital terbaik yang menjembatani operasional bisnis yang kuat dan pengalaman konsumen yang mulus di seluruh Indonesia.</p>
            </div>
            <div>
                <h5>Produk</h5>
                <ul class="footer-links">
                    <li><a href="#">CuanFlow B2B</a></li>
                    <li><a href="#">JajanFlow B2C</a></li>
                    <li><a href="#">Flow Enterprise</a></li>
                    <li><a href="#">Harga</a></li>
                </ul>
            </div>
            <div>
                <h5>Perusahaan</h5>
                <ul class="footer-links">
                    <li><a href="{{ route('about') }}">Tentang Kami</a></li>
                    <li><a href="{{ route('career') }}">Karir</a></li>
                    <li><a href="{{ route('blog') }}">Blog</a></li>
                    <li><a href="{{ route('contact') }}">Hubungi Kami</a></li>
                </ul>
            </div>
            <div>
                <h5>Hukum</h5>
                <ul class="footer-links">
                    <li><a href="{{ route('legal.terms') }}">Kebijakan Privasi</a></li>
                    <li><a href="{{ route('legal.terms') }}">Syarat & Ketentuan</a></li>
                    <li><a href="{{ route('legal.terms') }}">Kebijakan Cookie</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; 2026 Flow Ecosystem. Hak cipta dilindungi undang-undang.</span>
            <div class="footer-socials">
                <a href="https://github.com/DapCodes" target="_blank">GitHub</a>
                <a href="https://instagram.com/d4pfft" target="_blank">Instagram</a>
                <a href="https://www.linkedin.com/in/daffa-ramadhann/" target="_blank">LinkedIn</a>
            </div>
        </div>
    </footer>

    <script>
        const nav = document.getElementById('nav');
        const scrollProgress = document.getElementById('scroll-progress');
        
        window.addEventListener('scroll', () => {
            nav.classList.toggle('scrolled', window.scrollY > 60);
            const totalHeight = document.documentElement.scrollHeight - window.innerHeight;
            const progress = (window.scrollY / totalHeight) * 100;
            scrollProgress.style.width = progress + '%';
        }, { passive: true });

        AOS.init({ once: true, easing: 'ease-out-cubic', duration: 800 });
    </script>
    @stack('scripts')
</body>
</html>

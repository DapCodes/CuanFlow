<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Flow — Digital Ecosystem Bridging Business & Consumer</title>
<meta name="description" content="Flow is the ultimate digital ecosystem bridging business operations and consumer experiences in Indonesia. Manage your business with CuanFlow or explore with JajanFlow.">
<meta name="keywords" content="Business Ecosystem, POS Indonesia, CuanFlow, JajanFlow, Business Operations, Consumer Platform">
<meta property="og:title" content="Flow — Business Ecosystem">
<meta property="og:description" content="Bridging powerful business operations and seamless consumer experiences.">
<meta property="og:image" content="{{ asset('landingstatic/logo.png') }}">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

<style>
  @font-face {
    font-family: 'Great Vibes';
    src: url('{{ asset('landingstatic/GreatVibes-Regular.ttf') }}') format('truetype');
    font-weight: normal;
    font-style: normal;
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

  html {
    scroll-behavior: smooth;
    scroll-padding-top: 80px;
    overflow-x: hidden;
  }

  ::-webkit-scrollbar { width: 4px; }
  ::-webkit-scrollbar-track { background: var(--paper); }
  ::-webkit-scrollbar-thumb { background: var(--ink-3); border-radius: 2px; }

  body {
    font-family: var(--sans);
    background-color: var(--paper);
    color: var(--ink);
    overflow-x: hidden;
    font-size: 16px;
    line-height: 1.6;
  }

  /* ── NAVBAR ── */
  nav {
    position: fixed;
    top: 0; left: 0; right: 0;
    z-index: 900;
    height: 72px;
    display: flex;
    align-items: center;
    padding: 0 5%;
    transition: background 0.4s ease, box-shadow 0.4s ease;
  }

  nav.scrolled {
    background: var(--glass);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    border-bottom: 1px solid var(--glass-border);
  }

  /* Scroll Progress */
  .scroll-progress {
    position: fixed;
    top: 0; left: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--accent), var(--olive));
    z-index: 1000;
    width: 0%;
    transition: width 0.1s ease-out;
  }

  .nav-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
  }

  .nav-logo {
    font-family: var(--serif);
    font-size: 1.75rem;
    color: var(--ink);
    text-decoration: none;
    letter-spacing: -0.02em;
  }

  .nav-links {
    display: flex;
    list-style: none;
    gap: 36px;
    align-items: center;
  }

  .nav-links a {
    text-decoration: none;
    color: var(--ink-2);
    font-size: 0.875rem;
    font-weight: 500;
    letter-spacing: 0.01em;
    transition: all 0.3s ease;
    padding: 6px 12px;
    border-radius: 8px;
  }

  .nav-links a:hover { 
    color: var(--accent); 
    background: var(--accent-light);
  }

  /* Dropdown */
  .nav-dd-parent { position: relative; }

  .nav-dd-trigger {
    display: flex;
    align-items: center;
    gap: 5px;
    color: var(--ink-2);
    font-size: 0.875rem;
    font-weight: 400;
    cursor: pointer;
    transition: color 0.25s;
    user-select: none;
  }

  .nav-dd-trigger:hover { color: var(--accent); }

  .nav-dd-trigger i { font-size: 10px; transition: transform 0.3s; }

  .nav-dd-parent:hover .nav-dd-trigger i { transform: rotate(180deg); }

  .nav-dropdown {
    position: absolute;
    top: calc(100% + 16px);
    left: 50%;
    transform: translateX(-50%) translateY(8px);
    min-width: 220px;
    background: var(--white);
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 14px;
    padding: 8px;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s, transform 0.3s, visibility 0.3s;
    box-shadow: 0 12px 32px rgba(0,0,0,0.08);
  }

  .nav-dd-parent:hover .nav-dropdown {
    opacity: 1;
    visibility: visible;
    transform: translateX(-50%) translateY(0);
  }

  .nav-dropdown a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 0.875rem;
    color: var(--ink);
    text-decoration: none;
    transition: background 0.2s;
  }

  .nav-dropdown a:hover { background: var(--paper); }

  .dd-icon-box {
    width: 32px; height: 32px;
    border-radius: 8px;
    background: var(--accent-light);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--accent);
    font-size: 13px;
    flex-shrink: 0;
  }

  .dd-label { display: flex; flex-direction: column; }
  .dd-label span:first-child { font-weight: 500; font-size: 0.875rem; color: var(--ink); }
  .dd-label span:last-child { font-size: 0.72rem; color: var(--ink-3); margin-top: 1px; }

  .nav-cta {
    background: var(--ink);
    color: var(--white);
    padding: 9px 22px;
    border-radius: 100px;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 500;
    transition: background 0.25s, transform 0.2s;
    letter-spacing: 0.01em;
  }

  .nav-cta:hover { background: var(--accent); transform: translateY(-1px); }

  /* Hamburger */
  .hamburger {
    display: none;
    flex-direction: column;
    gap: 5px;
    cursor: pointer;
    background: none;
    border: none;
    padding: 6px;
    z-index: 910;
  }

  .hamburger span {
    width: 22px;
    height: 2px;
    background: var(--ink);
    border-radius: 2px;
    transition: all 0.35s cubic-bezier(0.23,1,0.32,1);
    display: block;
  }

  .hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
  .hamburger.open span:nth-child(2) { opacity: 0; transform: scaleX(0); }
  .hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

  /* Mobile Drawer */
  .mob-overlay {
    position: fixed; inset: 0;
    background: rgba(26,26,26,0.45);
    backdrop-filter: blur(4px);
    z-index: 895;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.35s, visibility 0.35s;
  }
  .mob-overlay.open { opacity: 1; visibility: visible; }

  .mob-drawer {
    position: fixed;
    top: 0; right: 0;
    width: min(320px, 88vw);
    height: 100dvh;
    background: var(--glass);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-left: 1px solid var(--glass-border);
    z-index: 896;
    transform: translateX(100%);
    transition: transform 0.4s cubic-bezier(0.23,1,0.32,1);
    padding: 100px 28px 40px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 4px;
  }

  .mob-drawer.open { transform: translateX(0); }

  .mob-drawer a, .mob-dd-toggle {
    display: block;
    text-decoration: none;
    color: var(--ink);
    font-size: 1rem;
    font-weight: 400;
    padding: 12px 14px;
    border-radius: 10px;
    transition: background 0.2s;
    cursor: pointer;
    user-select: none;
  }

  .mob-dd-toggle {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .mob-drawer a:hover, .mob-dd-toggle:hover { background: var(--paper); }

  .mob-sub { overflow: hidden; max-height: 0; transition: max-height 0.35s ease; }

  .mob-sub a { padding-left: 28px; font-size: 0.9rem; color: var(--ink-2); }

  .mob-divider { height: 1px; background: rgba(0,0,0,0.07); margin: 10px 0; }

  .mob-cta {
    display: block;
    text-align: center;
    background: var(--ink);
    color: var(--white) !important;
    padding: 13px 22px;
    border-radius: 100px;
    font-weight: 500;
    margin-top: 14px;
  }

  .mob-cta:hover { background: var(--accent) !important; }

  /* ── HERO ── */
  .hero {
    padding: 170px 5% 80px;
    display: flex;
    align-items: center;
    position: relative;
    overflow: hidden;
  }

  @media (max-width: 768px) {
    .features-section { padding: 40px 0 60px; }
    .arc-orbit { height: 420px; }
    .feat-card { width: 190px !important; padding: 18px 15px; border-radius: 16px; }
    .feat-card-icon { width: 32px; height: 32px; font-size: 12px; margin-bottom: 8px; }
    .feat-card h4 { font-size: 0.8rem; }
    .feat-card p { font-size: 0.7rem; }
    .section-label { margin-bottom: 20px; }
  }

  .hero-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
    width: 100%;
  }

  .eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--yellow);
    color: var(--dark-green);
    padding: 6px 14px;
    border-radius: 100px;
    font-size: 0.8rem;
    font-weight: 500;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    margin-bottom: 24px;
  }

  .eyebrow i { font-size: 10px; }

  h1.display {
    font-family: var(--serif);
    font-size: clamp(2.8rem, 5vw, 4.2rem);
    line-height: 1.1;
    letter-spacing: -0.02em;
    color: var(--ink);
    margin-bottom: 20px;
  }

  h1.display em {
    font-family: var(--cursive);
    font-style: normal;
    color: var(--accent);
    font-size: 1.2em;
    font-weight: 400;
  }

  .hero-desc {
    font-size: 1rem;
    color: var(--ink-2);
    line-height: 1.7;
    max-width: 440px;
    margin-bottom: 36px;
    font-weight: 300;
  }

  .btn-group { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }

  .btn-primary {
    background: linear-gradient(135deg, var(--ink) 0%, #333 100%);
    color: var(--white);
    padding: 13px 28px;
    border-radius: 100px;
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.1), 0 4px 12px rgba(0,0,0,0.1);
  }

  .btn-primary:hover { 
    background: linear-gradient(135deg, var(--accent) 0%, var(--dark-green) 100%); 
    transform: translateY(-2px); 
    box-shadow: 0 12px 28px rgba(49,105,78,0.25); 
  }

  .btn-secondary {
    background: transparent;
    color: var(--ink);
    padding: 13px 28px;
    border-radius: 100px;
    font-size: 0.9rem;
    font-weight: 400;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 1.5px solid rgba(0,0,0,0.15);
    transition: border-color 0.25s, color 0.25s;
  }

  .btn-secondary:hover { border-color: var(--accent); color: var(--accent); }

  /* Hero visual */
  .hero-visual {
    position: relative;
  }

  .hero-card-main {
    background: var(--navy);
    border-radius: 24px;
    overflow: hidden;
    aspect-ratio: 4/3;
    position: relative;
    box-shadow: 0 32px 80px rgba(30,45,61,0.2);
  }

  .hero-card-main img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  /* Floating widgets */
  .float-w {
    position: absolute;
    background: var(--white);
    border-radius: 14px;
    padding: 14px 18px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 160px;
    animation: floatY 4s ease-in-out infinite;
  }

  .float-w:nth-child(2) { animation-delay: 1.4s; }
  .float-w:nth-child(3) { animation-delay: 2.8s; }

  @keyframes floatY {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
  }

  .float-w.fw-tl { top: -20px; left: -32px; }
  .float-w.fw-bl { bottom: 36px; left: -40px; }
  .float-w.fw-tr { top: 28px; right: -24px; }

  .fw-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    background: var(--yellow);
    color: var(--dark-green);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
  }

  .fw-text { display: flex; flex-direction: column; }
  .fw-text .fw-label { font-size: 0.7rem; color: var(--ink-3); font-weight: 400; }
  .fw-text .fw-val { font-size: 0.85rem; font-weight: 500; color: var(--ink); margin-top: 1px; }
  .fw-text .fw-val.green { color: var(--accent); }

  /* Avatar group */
  .avatar-widget {
    position: absolute;
    bottom: -24px;
    right: -16px;
    background: var(--white);
    border-radius: 14px;
    padding: 12px 16px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .avatar-stack { display: flex; }
  .av-img {
    width: 30px; height: 30px;
    border-radius: 50%;
    border: 2px solid var(--white);
    margin-left: -8px;
    background: var(--paper-2);
    object-fit: cover;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 500;
    color: var(--ink-2);
  }
  .av-img:first-child { margin-left: 0; }

  .avatar-widget span {
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--ink);
    white-space: nowrap;
  }

  /* ── FEATURES ARC SECTION ── */
  .features-section {
    padding: 80px 0 100px;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    overflow: hidden;
    background: var(--paper);
    position: relative;
  }

  .features-section::before {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 180px;
    background: var(--dark-green);
    clip-path: ellipse(60% 100% at 50% 100%);
    z-index: 0;
  }

  .section-label {
    text-align: center;
    margin-bottom: 30px;
    padding: 0 5%;
    position: relative;
    z-index: 1;
  }

  .section-label h2 {
    font-family: var(--serif);
    font-size: clamp(2rem, 4vw, 3rem);
    letter-spacing: -0.02em;
    margin-bottom: 8px;
    color: var(--ink);
  }

  .section-label p {
    color: var(--ink-2);
    font-size: 1rem;
    font-weight: 300;
    max-width: 600px;
    margin: 0 auto;
  }

  /* Arc orbit container */
  .arc-orbit {
    position: relative;
    width: 100%;
    height: 520px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: visible;
    z-index: 1;
  }

  /* The curved track — invisible SVG arc guides cards via JS */
  .arc-track-svg {
    position: absolute;
    top: 0; left: 50%;
    transform: translateX(-50%);
    width: 1400px;
    height: 460px;
    pointer-events: none;
    opacity: 0.10;
  }

  .arc-cards-wrapper {
    position: absolute;
    top: 0; left: 50%;
    width: 1400px;
    height: 460px;
    transform: translateX(-50%);
  }

  .feat-card {
    position: absolute;
    width: 240px;
    background: var(--white);
    border: 1px solid rgba(0,0,0,0.06);
    border-radius: 20px;
    padding: 24px 20px;
    cursor: pointer;
    transition: border-color 0.4s cubic-bezier(0.2, 1, 0.3, 1), 
                box-shadow 0.4s cubic-bezier(0.2, 1, 0.3, 1),
                background 0.3s ease;
    transform-origin: center center;
    will-change: transform, opacity;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
  }

  .feat-card:hover {
    border-color: var(--accent);
    box-shadow: 0 20px 45px rgba(49,105,78,0.12);
    z-index: 500 !important;
  }

  .feat-card-icon {
    width: 38px; height: 38px;
    background: var(--yellow);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--dark-green);
    font-size: 14px;
    margin-bottom: 12px;
  }

  .feat-card h4 {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--ink);
    margin-bottom: 5px;
  }

  .feat-card p {
    font-size: 0.75rem;
    color: var(--ink-3);
    line-height: 1.5;
    font-weight: 300;
  }

  /* Scroll-triggered number badges on cards */
  .feat-card .card-num {
    position: absolute;
    top: -10px; right: -10px;
    width: 24px; height: 24px;
    background: var(--olive);
    color: var(--white);
    border-radius: 50%;
    font-size: 0.68rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* ── WHY SECTION ── */
  .why-section {
    background: var(--navy);
    padding: 120px 5%;
    color: var(--white);
    position: relative;
    overflow: hidden;
  }

  .why-section::before {
    content: '';
    position: absolute;
    top: -40%;
    right: -15%;
    width: 600px;
    height: 600px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(44,95,63,0.15) 0%, transparent 70%);
    pointer-events: none;
  }

  .why-grid {
    display: grid;
    grid-template-columns: 1fr 1.1fr;
    gap: 80px;
    max-width: 1200px;
    margin: 0 auto;
    align-items: center;
    position: relative;
    z-index: 1;
  }

  .why-section h2 {
    font-family: var(--serif);
    font-size: clamp(2.2rem, 4vw, 3.4rem);
    color: var(--white);
    line-height: 1.1;
    letter-spacing: -0.02em;
    margin-bottom: 20px;
  }

  .why-section h2 em { 
    font-family: var(--cursive);
    font-style: normal;
    color: var(--yellow);
    font-size: 1.25em;
    font-weight: 400;
  }

  .why-desc {
    font-size: 0.95rem;
    color: rgba(255,255,255,0.55);
    line-height: 1.75;
    font-weight: 300;
    max-width: 380px;
  }

  /* Accordion */
  .accordion { display: flex; flex-direction: column; gap: 10px; }

  .acc-item {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 14px;
    overflow: hidden;
    transition: border-color 0.3s, background 0.3s;
  }

  .acc-item.open {
    background: rgba(240,228,145,0.06);
    border-color: rgba(187,200,99,0.5);
  }

  .acc-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 22px;
    cursor: pointer;
    user-select: none;
  }

  .acc-header-left {
    display: flex;
    align-items: center;
    gap: 14px;
    font-size: 0.95rem;
    font-weight: 400;
    color: var(--white);
  }

  .acc-num {
    font-family: var(--serif);
    font-size: 1.1rem;
    color: var(--yellow);
    min-width: 20px;
    font-style: italic;
  }

  .acc-toggle {
    width: 28px; height: 28px;
    border-radius: 50%;
    background: rgba(255,255,255,0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255,255,255,0.5);
    font-size: 11px;
    transition: background 0.3s, color 0.3s, transform 0.35s;
    flex-shrink: 0;
  }

  .acc-item.open .acc-toggle {
    background: var(--olive);
    color: var(--dark-green);
    transform: rotate(180deg);
  }

  .acc-body {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s cubic-bezier(0.23,1,0.32,1);
  }

  .acc-content {
    padding: 0 22px 20px 56px;
    font-size: 0.875rem;
    color: rgba(255,255,255,0.5);
    line-height: 1.7;
    font-weight: 300;
  }

  /* ── PRODUCT SECTIONS ── */
  .product-section {
    padding: 120px 5%;
    position: relative;
    overflow: hidden;
  }

  .product-section.cuanflow { background: var(--paper); }
  .product-section.jajanflow { background: var(--white); }

  .product-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    max-width: 1200px;
    margin: 0 auto;
    align-items: center;
  }

  .product-section.jajanflow .product-grid {
    direction: rtl;
  }
  .product-section.jajanflow .product-grid > * { direction: ltr; }

  .product-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 5px 14px;
    border-radius: 100px;
    font-size: 0.75rem;
    font-weight: 500;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    margin-bottom: 20px;
  }

  .badge-navy { background: var(--yellow); color: var(--dark-green); }
  .badge-green { background: var(--accent-light); color: var(--accent); }

  .product-section h2 {
    font-family: var(--serif);
    font-size: clamp(2rem, 3.5vw, 3rem);
    color: var(--ink);
    line-height: 1.1;
    letter-spacing: -0.02em;
    margin-bottom: 18px;
  }

  .product-section .prod-desc {
    font-size: 0.95rem;
    color: var(--ink-2);
    line-height: 1.75;
    font-weight: 300;
    margin-bottom: 32px;
    max-width: 420px;
  }

  .feat-list {
    list-style: none;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px 20px;
    margin-bottom: 36px;
  }

  .feat-list li {
    display: flex;
    align-items: center;
    gap: 9px;
    font-size: 0.875rem;
    color: var(--ink-2);
  }

  .feat-list li i {
    color: var(--accent);
    font-size: 12px;
    flex-shrink: 0;
  }

  /* Mockup frames */
  .desktop-mockup {
    background: var(--white);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 24px 64px rgba(0,0,0,0.12);
    border: 1px solid rgba(0,0,0,0.06);
  }

  .mockup-bar {
    height: 34px;
    background: #f8f8f8;
    border-bottom: 1px solid rgba(0,0,0,0.06);
    display: flex;
    align-items: center;
    padding: 0 14px;
    gap: 6px;
  }

  .mockup-bar .dot { width: 9px; height: 9px; border-radius: 50%; }
  .dot-r { background: #ff5f57; }
  .dot-y { background: #febc2e; }
  .dot-g { background: #28c840; }

  .url-bar {
    flex: 1;
    margin-left: 10px;
    height: 20px;
    background: #eeeeee;
    border-radius: 5px;
    display: flex;
    align-items: center;
    padding: 0 10px;
    font-size: 0.65rem;
    color: #999;
    font-family: var(--sans);
  }

  .mockup-body {
    min-height: 240px;
    background: #eef1f5;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    position: relative;
    overflow: hidden;
  }

  .mockup-body img { width: 100%; display: block; object-fit: cover; }

  /* Phone mockup */
  .phone-mockup {
    width: 240px;
    margin: 0 auto;
    background: #0f1115;
    border-radius: 38px;
    padding: 8px;
    box-shadow: 0 32px 80px rgba(0,0,0,0.25);
    position: relative;
    border: 1px solid rgba(255,255,255,0.05);
  }

  .phone-notch {
    width: 80px; height: 18px;
    background: #0f1115;
    border-radius: 0 0 14px 14px;
    position: absolute;
    top: 8px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 2;
  }

  .phone-screen {
    width: 100%;
    aspect-ratio: 9/19;
    background: #f0f4f0;
    border-radius: 30px;
    overflow: hidden;
    position: relative;
  }

  .phone-screen img { width: 100%; height: 100%; object-fit: cover; display: block; }

  .phone-screen .placeholder-phone {
    width: 100%;
    height: 100%;
    background: linear-gradient(180deg, #e8f0eb 0%, #d4e4d8 100%);
    display: flex;
    flex-direction: column;
    padding: 36px 16px 16px;
    gap: 8px;
  }

  .ph-p-bar {
    background: rgba(255,255,255,0.6);
    border-radius: 6px;
  }

  /* pill accent */
  .pill:hover { border-color: var(--olive); color: var(--dark-green); background: rgba(187,200,99,0.1); }
  .pill i { font-size: 12px; color: var(--green); }

  /* ── CTA SECTION ── */
  .cta-section {
    background: var(--ink);
    padding: 120px 5%;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .cta-section::before {
    content: '';
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    width: 800px; height: 800px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(44,95,63,0.12) 0%, transparent 70%);
    pointer-events: none;
  }

  .cta-section h2 {
    font-family: var(--serif);
    font-size: clamp(2.2rem, 4vw, 3.6rem);
    color: var(--white);
    line-height: 1.15;
    letter-spacing: -0.02em;
    margin-bottom: 16px;
    position: relative;
    z-index: 1;
  }

  .cta-section h2 em { 
    font-family: var(--cursive);
    font-style: normal;
    color: var(--yellow);
    font-size: 1.25em;
    font-weight: 400;
  }

  .cta-section p {
    font-size: 1rem;
    color: rgba(255,255,255,0.5);
    max-width: 480px;
    margin: 0 auto 40px;
    font-weight: 300;
    line-height: 1.7;
    position: relative;
    z-index: 1;
  }

  .cta-btns {
    display: flex;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
    position: relative;
    z-index: 1;
  }

  .cta-btn-white {
    background: var(--white);
    color: var(--ink);
    padding: 13px 30px;
    border-radius: 100px;
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 9px;
    transition: background 0.25s, transform 0.2s;
  }

  .cta-btn-white:hover { background: var(--paper); transform: translateY(-2px); }

  .cta-btn-outline {
    background: transparent;
    color: rgba(255,255,255,0.75);
    padding: 13px 30px;
    border-radius: 100px;
    font-size: 0.9rem;
    font-weight: 400;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 9px;
    border: 1.5px solid rgba(255,255,255,0.2);
    transition: border-color 0.25s, color 0.25s;
  }

  .cta-btn-outline:hover { border-color: rgba(255,255,255,0.5); color: var(--white); }

  /* ── FOOTER ── */
  footer {
    background: var(--ink);
    color: rgba(255,255,255,0.6);
    padding: 80px 5% 40px;
    border-top: 1px solid rgba(255,255,255,0.06);
  }

  .footer-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    gap: 48px;
    max-width: 1200px;
    margin: 0 auto 60px;
  }

  .footer-brand .nav-logo { color: var(--white); display: block; margin-bottom: 16px; }

  .footer-brand p {
    font-size: 0.875rem;
    line-height: 1.65;
    font-weight: 300;
    max-width: 280px;
    color: rgba(255,255,255,0.4);
  }

  footer h5 {
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--white);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 18px;
  }

  .footer-links { list-style: none; display: flex; flex-direction: column; gap: 10px; }

  .footer-links a {
    color: rgba(255,255,255,0.4);
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 300;
    transition: color 0.25s;
  }

  .footer-links a:hover { color: var(--accent-mid); }

  .footer-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 28px;
    border-top: 1px solid rgba(255,255,255,0.06);
    max-width: 1200px;
    margin: 0 auto;
    font-size: 0.8rem;
    flex-wrap: wrap;
    gap: 12px;
  }

  .footer-socials { display: flex; gap: 18px; }
  .footer-socials a { color: rgba(255,255,255,0.3); font-size: 0.8rem; text-decoration: none; transition: color 0.25s; }
  .footer-socials a:hover { color: var(--white); }

  /* ── PILLS ── */
  .pill-group { display: flex; flex-wrap: wrap; gap: 10px; }

  .pill {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: var(--paper);
    border: 1px solid rgba(0,0,0,0.08);
    padding: 9px 16px;
    border-radius: 100px;
    font-size: 0.83rem;
    color: var(--ink-2);
    transition: all 0.25s;
  }

  .pill:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-light); }
  .pill i { font-size: 12px; color: var(--accent); }

  /* ── RESPONSIVE ── */
  @media (max-width: 960px) {
    .hero-grid { grid-template-columns: 1fr; gap: 60px; }
    .float-w.fw-tr { right: -10px; }
    .float-w.fw-bl { left: -10px; }
    .float-w.fw-tl { left: -10px; }
    .why-grid { grid-template-columns: 1fr; gap: 48px; }
    .product-grid { grid-template-columns: 1fr; gap: 48px; direction: ltr !important; }
    .footer-grid { grid-template-columns: 1fr 1fr; }
    .feat-list { grid-template-columns: 1fr; }
    nav .nav-links, nav .nav-cta.dt-only { display: none; }
    .hamburger { display: flex; }
  }

  @media (max-width: 767px) {
    /* Arc orbit → responsive scroll arc on mobile */
    .features-section {
       overflow: hidden;
       padding-bottom: 60px;
    }
    .arc-orbit {
      height: 500px !important;
      overflow: hidden;
      width: 100vw;
      margin-left: calc(-50vw + 50%);
      position: relative;
    }
    .arc-track-svg { display: none; }
    .arc-cards-wrapper {
      position: absolute !important;
      left: 0 !important; top: 0 !important;
      transform: none !important;
      width: 100% !important;
      height: 100% !important;
      display: block !important;
    }
    .feat-card {
      position: absolute !important;
      width: 260px !important;
      min-width: 260px !important;
      opacity: 0; /* Default invisible before JS */
      margin: 0 !important;
    }
    .features-section::before { height: 120px; }
  }

  @media (max-width: 600px) {
    h1.display { font-size: 2.4rem; }
    .hero { padding: 100px 5% 60px; }
    .float-w { display: none; }
    .avatar-widget { position: static; margin-top: 20px; }
    .footer-grid { grid-template-columns: 1fr; }
    .footer-bottom { flex-direction: column; align-items: flex-start; }
    .product-section { padding: 80px 5%; }
    .why-section { padding: 80px 5%; }
    .cta-section { padding: 80px 5%; }
    .feat-card { width: 100%; }
  }
</style>
</head>
<body>
<div class="scroll-progress" id="scroll-progress"></div>

<!-- ── NAVBAR ── -->
<nav id="nav">
  <div class="nav-inner">
    <a href="#" class="nav-logo"><img src="{{ asset('landingstatic/logo.png') }}" alt="Flow" style="height:36px; width:auto; display:block;"></a>
    <ul class="nav-links">
      <li><a href="#">Beranda</a></li>
      <li><a href="#features">Fitur</a></li>
      <li><a href="#why">Mengapa Kami</a></li>
      <li class="nav-dd-parent">
        <span class="nav-dd-trigger">Produk <i class="fa-solid fa-chevron-down"></i></span>
        <div class="nav-dropdown">
          <a href="#cuanflow">
            <div class="dd-icon-box"><i class="fa-solid fa-chart-line"></i></div>
            <div class="dd-label"><span>CuanFlow</span><span>Manajemen Bisnis · B2B</span></div>
          </a>
          <a href="#jajanflow">
            <div class="dd-icon-box"><i class="fa-solid fa-location-dot"></i></div>
            <div class="dd-label"><span>JajanFlow</span><span>Platform Konsumen · B2C</span></div>
          </a>
        </div>
      </li>
      <li><a href="#ecosystem">Ekosistem</a></li>
    </ul>
    <a href="#" class="nav-cta dt-only">Mulai Sekarang</a>
    <button class="hamburger" id="ham" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

<!-- Mobile overlay & drawer -->
<div class="mob-overlay" id="mob-overlay"></div>
<div class="mob-drawer" id="mob-drawer">
  <a href="#">Beranda</a>
  <a href="#features">Fitur</a>
  <a href="#why">Mengapa Kami</a>
  <div class="mob-dd-toggle" id="mob-prod-toggle">
    Produk <i class="fa-solid fa-chevron-down" style="font-size:11px; color:var(--ink-3);"></i>
  </div>
  <div class="mob-sub" id="mob-prod-sub">
    <a href="#cuanflow">CuanFlow</a>
    <a href="#jajanflow">JajanFlow</a>
  </div>
  <a href="#ecosystem">Ekosistem</a>
  <div class="mob-divider"></div>
  <a href="#" class="mob-cta">Mulai Sekarang</a>
</div>

<!-- ── HERO ── -->
<section class="hero" id="hero">
  <div class="hero-grid">
    <!-- Content -->
    <div>
      <div class="eyebrow" data-aos="fade-up" data-aos-duration="600">
        <i class="fa-solid fa-circle" style="font-size:6px;"></i> Platform Ekosistem Bisnis
      </div>
      <h1 class="display" data-aos="fade-up" data-aos-delay="80" data-aos-duration="700">
        Scale Your<br><em>Success.</em>
      </h1>
      <p class="hero-desc" data-aos="fade-up" data-aos-delay="160" data-aos-duration="700">
        Platform terintegrasi penuh yang menyatukan operasional, pemasaran, dan keuangan — menghilangkan alur kerja yang terfragmentasi bagi UMKM di seluruh Indonesia.
      </p>
      <div class="btn-group" data-aos="fade-up" data-aos-delay="240" data-aos-duration="700">
        <a href="#cuanflow" class="btn-primary">
          <i class="fa-solid fa-arrow-right"></i> Jelajahi Platform
        </a>
        <a href="#why" class="btn-secondary">Pelajari Lebih Lanjut</a>
      </div>
    </div>

    <!-- Visual -->
    <div class="hero-visual" data-aos="fade-left" data-aos-delay="200" data-aos-duration="800">
      <div class="hero-card-main">
        <img src="{{ asset('landingstatic/foto1.png') }}" alt="Dashboard Overview">
      </div>

      <div class="float-w fw-tl">
        <div class="fw-icon"><i class="fa-solid fa-arrow-trend-up"></i></div>
        <div class="fw-text">
          <span class="fw-label">Efisiensi</span>
          <span class="fw-val green">Tumbuh +40%</span>
        </div>
      </div>
<br>      <div class="float-w fw-bl">
        <div class="fw-icon"><i class="fa-solid fa-bolt"></i></div>
        <div class="fw-text">
          <span class="fw-label">Proses</span>
          <span class="fw-val">Otomatis</span>
        </div>
      </div>
<br>      <div class="float-w fw-tr">
        <div class="fw-icon"><i class="fa-solid fa-bullseye"></i></div>
        <div class="fw-text">
          <span class="fw-label">Platform</span>
          <span class="fw-val">Semua-dalam-Satu</span>
        </div>
      </div>
<br>      <div class="avatar-widget">
        <div class="avatar-stack">
          <div class="av-img">AR</div>
          <div class="av-img">BK</div>
          <div class="av-img">CS</div>
          <div class="av-img">DM</div>
          <div class="av-img">+</div>
        </div>
        <span>500+ UMKM Bergabung</span>
      </div>
    </div>
  </div>
</section>

<!-- ── FEATURES ARC ── -->
<section class="features-section" id="features">
  <div class="section-label" data-aos="fade-up">
    <h2>Fitur Terintegrasi Premium</h2>
    <p>Fitur canggih yang dirancang untuk mengubah operasional bisnis Anda menjadi ekosistem digital berperforma tinggi.</p>
  </div>

  <div class="arc-orbit" id="arc-orbit">
    <svg class="arc-track-svg" viewBox="0 0 1400 460" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M 100,400 Q 700,20 1300,400" stroke="#31694E" stroke-width="2" fill="none" stroke-dasharray="8 6"/>
    </svg>
    <div class="arc-cards-wrapper" id="arc-cards-wrapper">
      <div class="feat-card" id="fc-0">
        <div class="card-num">1</div>
        <div class="feat-card-icon"><i class="fa-solid fa-cash-register"></i></div>
        <h4>Kasir Digital (POS)</h4>
        <p>Eksekusi transaksi kilat dengan rekapitulasi penjualan otomatis setiap hari.</p>
      </div>
      <div class="feat-card" id="fc-1">
        <div class="card-num">2</div>
        <div class="feat-card-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
        <h4>Stok & Inventaris</h4>
        <p>Pantau aliran stok barang secara presisi dan real-time tanpa khawatir kehabisan.</p>
      </div>
      <div class="feat-card" id="fc-2">
        <div class="card-num">3</div>
        <div class="feat-card-icon"><i class="fa-solid fa-chart-bar"></i></div>
        <h4>Laporan Keuangan</h4>
        <p>Wawasan performa laba rugi instan untuk pengambilan keputusan yang tepat.</p>
      </div>
      <div class="feat-card" id="fc-3">
        <div class="card-num">4</div>
        <div class="feat-card-icon"><i class="fa-solid fa-users"></i></div>
        <h4>Manajemen Karyawan</h4>
        <p>Orkestrasi jadwal, absensi, dan hak akses tim dalam satu dashboard terpadu.</p>
      </div>
      <div class="feat-card" id="fc-4">
        <div class="card-num">5</div>
        <div class="feat-card-icon"><i class="fa-solid fa-credit-card"></i></div>
        <h4>Multi-Pembayaran</h4>
        <p>Terima segala bentuk pembayaran dari QRIS hingga berbagai dompet digital.</p>
      </div>
      <div class="feat-card" id="fc-5">
        <div class="card-num">6</div>
        <div class="feat-card-icon"><i class="fa-solid fa-building"></i></div>
        <h4>Manajemen Cabang</h4>
        <p>Kendali penuh seluruh outlet Anda secara terintegrasi dari satu platform.</p>
      </div>
      <div class="feat-card" id="fc-6">
        <div class="card-num">7</div>
        <div class="feat-card-icon"><i class="fa-solid fa-robot"></i></div>
        <h4>AI Assistant (Clara)</h4>
        <p>Asisten cerdas yang menganalisis performa bisnis Anda secara otomatis.</p>
      </div>
      <div class="feat-card" id="fc-7">
        <div class="card-num">8</div>
        <div class="feat-card-icon"><i class="fa-solid fa-percent"></i></div>
        <h4>Promo & Diskon</h4>
        <p>Buat dan kelola kampanye promosi yang menarik pelanggan baru lebih efektif.</p>
      </div>
    </div>
  </div>
</section>

<!-- ── WHY SECTION ── -->
<section class="why-section" id="why">
  <div class="why-grid">
    <div>
      <h2 data-aos="fade-up">Mengapa Memilih<br><em>Flow?</em></h2>
      <p class="why-desc" data-aos="fade-up" data-aos-delay="80">
        Dirancang khusus untuk UMKM Indonesia — integrasi cerdas, harga terjangkau, dan antarmuka yang dapat digunakan seluruh tim Anda sejak hari pertama.
      </p>
    </div>

    <div class="accordion" data-aos="fade-up" data-aos-delay="120">
      <div class="acc-item open">
        <div class="acc-header">
          <div class="acc-header-left">
            <span class="acc-num">01</span>
            <span>35+ Fitur Utama</span>
          </div>
          <div class="acc-toggle"><i class="fa-solid fa-chevron-down"></i></div>
        </div>
        <div class="acc-body" style="max-height:120px;">
          <p class="acc-content">Dari manajemen transaksi, inventaris, laporan keuangan, hingga POS — semua yang Anda butuhkan dalam satu platform cerdas untuk operasional harian yang lancar.</p>
        </div>
      </div>

      <div class="acc-item">
        <div class="acc-header">
          <div class="acc-header-left">
            <span class="acc-num">02</span>
            <span>Terjangkau untuk UMKM</span>
          </div>
          <div class="acc-toggle"><i class="fa-solid fa-chevron-down"></i></div>
        </div>
        <div class="acc-body">
          <p class="acc-content">Harga transparan tanpa biaya tersembunyi — dirancang untuk mendukung dan menumbuhkan bisnis kecil hingga menengah Anda tanpa beban finansial.</p>
        </div>
      </div>

      <div class="acc-item">
        <div class="acc-header">
          <div class="acc-header-left">
            <span class="acc-num">03</span>
            <span>Antarmuka Ramah Pengguna</span>
          </div>
          <div class="acc-toggle"><i class="fa-solid fa-chevron-down"></i></div>
        </div>
        <div class="acc-body">
          <p class="acc-content">Desain visual yang bersih dan navigasi intuitif memudahkan siapa pun di tim Anda — bahkan bagi pengguna platform digital pemula.</p>
        </div>
      </div>

      <div class="acc-item">
        <div class="acc-header">
          <div class="acc-header-left">
            <span class="acc-num">04</span>
            <span>Wawasan Berbasis AI</span>
          </div>
          <div class="acc-toggle"><i class="fa-solid fa-chevron-down"></i></div>
        </div>
        <div class="acc-body">
          <p class="acc-content">Dapatkan analisis cerdas instan tentang performa bisnis Anda yang didukung oleh Clara, asisten AI internal kami yang belajar dari data Anda.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── CUANFLOW ── -->
<section class="product-section cuanflow" id="cuanflow">
  <div class="product-grid">
    <div data-aos="fade-right" data-aos-duration="800">
      <div class="product-badge badge-navy">
        <i class="fa-solid fa-briefcase"></i> Manajemen Bisnis
      </div>
      <h2>CuanFlow<br>Platform B2B</h2>
      <p class="prod-desc">Kelola seluruh operasional bisnis Anda dari satu dashboard cerdas. POS, stok, keuangan, hingga manajemen karyawan — semuanya terintegrasi sempurna.</p>
      <ul class="feat-list">
        <li><i class="fa-solid fa-check"></i> Kasir Digital (POS)</li>
        <li><i class="fa-solid fa-check"></i> Manajemen Stok</li>
        <li><i class="fa-solid fa-check"></i> Laporan Keuangan</li>
        <li><i class="fa-solid fa-check"></i> Multi Cabang</li>
        <li><i class="fa-solid fa-check"></i> Manajemen Karyawan</li>
        <li><i class="fa-solid fa-check"></i> AI Assistant (Clara)</li>
      </ul>
      <a href="#" class="btn-primary">
        <i class="fa-solid fa-arrow-right"></i> Coba CuanFlow
      </a>
    </div>

    <div data-aos="fade-left" data-aos-duration="800" data-aos-delay="100">
      <div class="desktop-mockup">
        <div class="mockup-bar">
          <div class="dot dot-r"></div>
          <div class="dot dot-y"></div>
          <div class="dot dot-g"></div>
          <div class="url-bar">cuanflow.app/dashboard</div>
        </div>
        <div class="mockup-body">
            <img src="{{ asset('landingstatic/mockup1.png') }}" alt="CuanFlow Dashboard">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── JAJANFLOW ── -->
<section class="product-section jajanflow" id="jajanflow">
  <div class="product-grid">
    <div data-aos="fade-right" data-aos-duration="800">
      <div class="phone-mockup">
        <div class="phone-notch"></div>
        <div class="phone-screen">
          <img src="{{ asset('landingstatic/mockup2.png') }}" alt="JajanFlow App" style="width:100%; height:100%; object-fit:cover; display:block;">
        </div>
      </div>
    </div>

    <div data-aos="fade-left" data-aos-duration="800" data-aos-delay="100">
      <div class="product-badge badge-green">
        <i class="fa-solid fa-mobile-screen"></i> Platform Konsumen
      </div>
      <h2>JajanFlow<br>Platform B2C</h2>
      <p class="prod-desc">Platform mobile yang menghubungkan konsumen dengan UMKM terdekat secara real-time. Temukan produk dan layanan langsung dari genggaman tangan.</p>
      <div class="pill-group" style="margin-bottom: 32px;">
        <div class="pill"><i class="fa-solid fa-map"></i> Peta Interaktif</div>
        <div class="pill"><i class="fa-solid fa-magnifying-glass"></i> Cari Outlet</div>
        <div class="pill"><i class="fa-solid fa-wallet"></i> Catat Cashflow</div>
        <div class="pill"><i class="fa-solid fa-shield-check"></i> Outlet Terpercaya</div>
      </div>
      <a href="#" class="btn-primary">
        <i class="fa-solid fa-download"></i> Unduh JajanFlow
      </a>
    </div>
  </div>
</section>

<!-- ── CTA ── -->
<section class="cta-section" id="ecosystem">
  <h2 data-aos="fade-up">Siap Bergabung dengan<br><em>Ekosistem Flow?</em></h2>
  <p data-aos="fade-up" data-aos-delay="80">Kelola bisnis lebih efisien dengan CuanFlow atau jelajahi kuliner terdekat lewat JajanFlow. Pilih solusi yang tepat untuk Anda.</p>
  <div class="cta-btns" data-aos="fade-up" data-aos-delay="160">
    <a href="#cuanflow" class="cta-btn-white">
      <i class="fa-solid fa-chart-line"></i> Coba CuanFlow
    </a>
    <a href="#jajanflow" class="cta-btn-outline">
      <i class="fa-solid fa-mobile-screen"></i> Unduh JajanFlow
    </a>
  </div>
</section>

<!-- ── FOOTER ── -->
<footer>
  <div class="footer-grid">
    <div class="footer-brand">
      <a href="#" class="nav-logo"><img src="{{ asset('landingstatic/logo.png') }}" alt="Flow" style="height:36px; width:auto; display:block; filter: brightness(0) invert(1);"></a>
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
        <li><a href="#">Tentang Kami</a></li>
        <li><a href="#">Karir</a></li>
        <li><a href="#">Blog</a></li>
        <li><a href="#">Hubungi Kami</a></li>
      </ul>
    </div>
    <div>
      <h5>Hukum</h5>
      <ul class="footer-links">
        <li><a href="#">Kebijakan Privasi</a></li>
        <li><a href="#">Syarat & Ketentuan</a></li>
        <li><a href="#">Kebijakan Cookie</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <span>&copy; 2026 Flow Ecosystem. Hak cipta dilindungi undang-undang.</span>
    <div class="footer-socials">
      <a href="#">Twitter</a>
      <a href="#">Instagram</a>
      <a href="#">LinkedIn</a>
    </div>
  </div>
</footer>

<script>
  // ── SCROLL TRACKING ──
  const nav = document.getElementById('nav');
  const scrollProgress = document.getElementById('scroll-progress');
  
  window.addEventListener('scroll', () => {
    // Nav shift
    nav.classList.toggle('scrolled', window.scrollY > 60);
    
    // Progress bar
    const totalHeight = document.documentElement.scrollHeight - window.innerHeight;
    const progress = (window.scrollY / totalHeight) * 100;
    scrollProgress.style.width = progress + '%';
  }, { passive: true });

  // ── MOBILE MENU ──
  const ham = document.getElementById('ham');
  const overlay = document.getElementById('mob-overlay');
  const drawer = document.getElementById('mob-drawer');
  const allMobLinks = drawer.querySelectorAll('a');

  function openDrawer() {
    ham.classList.add('open');
    overlay.classList.add('open');
    drawer.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeDrawer() {
    ham.classList.remove('open');
    overlay.classList.remove('open');
    drawer.classList.remove('open');
    document.body.style.overflow = '';
  }

  ham.addEventListener('click', () => drawer.classList.contains('open') ? closeDrawer() : openDrawer());
  overlay.addEventListener('click', closeDrawer);
  allMobLinks.forEach(a => a.addEventListener('click', closeDrawer));

  const mobProdToggle = document.getElementById('mob-prod-toggle');
  const mobProdSub = document.getElementById('mob-prod-sub');
  mobProdToggle.addEventListener('click', () => {
    const isOpen = mobProdSub.style.maxHeight;
    mobProdSub.style.maxHeight = isOpen ? null : mobProdSub.scrollHeight + 'px';
    mobProdToggle.querySelector('i').style.transform = isOpen ? '' : 'rotate(180deg)';
  });

  // ── ACCORDION ──
  document.querySelectorAll('.acc-header').forEach(header => {
    header.addEventListener('click', () => {
      const item = header.parentElement;
      const body = header.nextElementSibling;
      const isOpen = item.classList.contains('open');
      document.querySelectorAll('.acc-item').forEach(i => {
        i.classList.remove('open');
        i.querySelector('.acc-body').style.maxHeight = null;
      });
      if (!isOpen) {
        item.classList.add('open');
        body.style.maxHeight = body.scrollHeight + 'px';
      }
    });
  });

  // ── ARC CARD POSITIONING & INTERACTIVE SCROLL ──
  function getBezierPoint(t, curve) {
    const { p0, p1, p2 } = curve;
    const x = (1-t)*(1-t)*p0.x + 2*(1-t)*t*p1.x + t*t*p2.x;
    const y = (1-t)*(1-t)*p0.y + 2*(1-t)*t*p1.y + t*t*p2.y;
    const dx = 2*(1-t)*(p1.x-p0.x) + 2*t*(p2.x-p1.x);
    const dy = 2*(1-t)*(p1.y-p0.y) + 2*t*(p2.y-p1.y);
    const angle = Math.atan2(dy, dx) * 180 / Math.PI;
    return { x, y, angle };
  }

  function getCurve() {
     const w = window.innerWidth;
     const isMobile = w < 768;
     
     if (isMobile) {
       // Wide reach for mobile to ensure cards animate across the entire screen
       return {
         p0: { x: -w * 1.5, y: 240 },
         p1: { x: 0, y: -20 },
         p2: { x: w * 1.5, y: 240 }
       };
     } else {
       return {
         p0: { x: -w * 0.5, y: 220 },
         p1: { x: 0, y: -160 },
         p2: { x: w * 0.5, y: 220 }
       };
     }
  }

  // ── GSAP + AOS INIT ──
  document.addEventListener('DOMContentLoaded', () => {
    gsap.registerPlugin(ScrollTrigger);
    AOS.init({ once: true, easing: 'ease-out-cubic', offset: 60 });

    const cards = Array.from(document.querySelectorAll('.feat-card'));
    const isMobile = () => window.innerWidth < 768;

    const wrapper = document.getElementById('arc-cards-wrapper');
    wrapper.style.cssText = 'position: absolute; top: 0; left: 50%; width: 0; height: 0;';
    
    // Set base positioning styles
    cards.forEach(card => {
       card.style.position = 'absolute';
       card.style.margin = '0';
       card.style.transformOrigin = 'center center';
       card.style.willChange = 'transform, opacity';
    });

    const state = { progress: 0, hoverIndex: -1 };
    
    function renderCards(progress) {
       const curve = getCurve();
       const n = cards.length;
       const mobile = isMobile();
       
       // Increase Spread Factor for more gap between cards
       const spreadFactor = mobile ? 1.8 : 1.2;
       
       cards.forEach((card, i) => {
          const relativeOffset = i / (n - 1);
          // Movement formula tuned for better visual centering as you scroll
          const t = 0.5 + (relativeOffset - 0.5) * spreadFactor - (progress - 0.5) * 3.5;
          
          if (t > 1.8 || t < -0.8) {
             card.style.opacity = 0;
             card.style.pointerEvents = 'none';
             card.style.visibility = 'hidden';
          } else {
             const { x, y, angle } = getBezierPoint(t, curve);
             card.style.visibility = 'visible';
             
             const distCenter = Math.abs(t - 0.5);
             
             // Opacity Falloff (Focus)
             let op = 1;
             if (mobile) {
               op = Math.max(0, 1 - Math.pow(distCenter * 2.2, 1.2)); 
             } else {
               op = Math.max(0, 1 - Math.pow(distCenter * 1.8, 1.5));
             }
             
             // Perspective Scale
             const scaleFocus = mobile ? 1.12 : 1.15;
             const scaleFalloff = mobile ? 0.6 : 0.7;
             const scale = scaleFocus - Math.pow(distCenter, 1.1) * scaleFalloff;
             
             card.style.opacity = op;
             card.style.pointerEvents = op > 0.4 ? 'auto' : 'none';
             
             // Dynamic card width
             const cardWidth = mobile ? 190 : 240;
             card.style.left = `${x - (cardWidth/2)}px`;
             card.style.top = `${y}px`;
             
             const tilt = angle * (mobile ? 0.05 : 0.35);
             const isHovered = state.hoverIndex === i;
             const hoverScale = isHovered ? 1.06 : 1;
             
             card.style.transform = `rotate(${tilt}deg) scale(${scale * hoverScale})`;
             card.style.zIndex = isHovered ? 1000 : Math.round(scale * 100);
          }
       });
    }

    // Add Hover Listeners
    cards.forEach((card, i) => {
       card.addEventListener('mouseenter', () => {
          state.hoverIndex = i;
          renderCards(state.progress);
       });
       card.addEventListener('mouseleave', () => {
          state.hoverIndex = -1;
          renderCards(state.progress);
       });
    });

    renderCards(0);
    window.addEventListener('resize', () => renderCards(state.progress));

    // Pin the features section with buttery smooth scrub
    ScrollTrigger.create({
       trigger: '.features-section',
       pin: true,
       start: 'top top',
       end: '+=2500', 
       scrub: 1.2, // Premium weighted scrub for smoother feel
       onUpdate: (self) => {
          state.progress = self.progress;
          renderCards(self.progress);
       }
    });

    // ── HERO FLOAT WIDGETS ──
    gsap.from('.hero-visual .float-w', {
      y: 20, opacity: 0, duration: 0.8,
      stagger: 0.18, ease: 'power3.out', delay: 0.6
    });

    gsap.from('.avatar-widget', {
      scrollTrigger: { trigger: '.hero', start: 'top 60%' },
      scale: 0.85, opacity: 0, duration: 0.7, ease: 'back.out(1.7)', delay: 0.8
    });

    // ── PRODUCT MOCKUPS ──
    gsap.from('.product-section.cuanflow .desktop-mockup', {
      scrollTrigger: { trigger: '.product-section.cuanflow', start: 'top 75%' },
      y: 48, opacity: 0, duration: 1, ease: 'power3.out'
    });

    gsap.from('.product-section.jajanflow .phone-mockup', {
      scrollTrigger: { trigger: '.product-section.jajanflow', start: 'top 75%' },
      y: 48, opacity: 0, scale: 0.92, duration: 1, ease: 'back.out(1.4)'
    });
  });
</script>
</body>
</html>
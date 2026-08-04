<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SIPEG KALJ — Sistem Kepegawaian & Absensi Face Recognition</title>
    <meta name="description"
        content="Sistem Informasi Kepegawaian & Absensi Biometrik Face Recognition PT. Karya Agung Lestari Jaya dengan verifikasi lokasi GPS real-time.">

    <!-- PWA Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0056B3">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SIPEG KALJ">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192x192.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .glass-header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .hero-glow {
            background: radial-gradient(circle at 50% 30%, rgba(0, 86, 179, 0.12) 0%, rgba(200, 16, 46, 0.05) 45%, transparent 70%);
        }

        .scan-line {
            background: linear-gradient(180deg, rgba(16, 185, 129, 0) 0%, rgba(16, 185, 129, 0.8) 50%, rgba(16, 185, 129, 0) 100%);
            animation: scan 2.5s ease-in-out infinite alternate;
        }

        @keyframes scan {
            0% { top: 5%; }
            100% { top: 85%; }
        }

        .floating-badge {
            animation: float 4s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col" x-data="{ mobileMenuOpen: false, pwaPrompt: null, canInstallPwa: false }">

    <!-- Sticky Navigation Header -->
    <header class="sticky top-0 z-50 glass-header border-b border-slate-200/80 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">

                <!-- Logo & Brand -->
                <a href="{{ route('landing') }}" class="flex items-center gap-3 group">
                    <div class="w-11 h-11 bg-white rounded-2xl flex items-center justify-center shadow-md border border-slate-200/80 p-0.5 overflow-hidden group-hover:scale-105 transition-transform duration-200">
                        <img src="{{ asset('storage/logo.jpeg') }}" alt="Logo PT KALJ" class="w-full h-full object-cover rounded-xl">
                    </div>
                    <div>
                        <div class="text-slate-900 font-extrabold text-lg tracking-tight leading-none flex items-center gap-1.5">
                            SIPEG KALJ
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-[#0056B3]">PWA</span>
                        </div>
                        <div class="text-slate-400 text-xs font-medium mt-0.5 hidden sm:block">PT. Karya Agung Lestari Jaya</div>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                    <a href="#fitur" class="hover:text-[#0056B3] transition-colors">Fitur Utama</a>
                    <a href="#cara-kerja" class="hover:text-[#0056B3] transition-colors">Cara Kerja</a>
                    <a href="#pwa-app" class="hover:text-[#0056B3] transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        Aplikasi HP
                    </a>
                    <a href="#keunggulan" class="hover:text-[#0056B3] transition-colors">Keunggulan</a>
                </nav>

                <!-- Auth / CTA Actions -->
                <div class="hidden md:flex items-center gap-3">
                    <!-- Install PWA Button -->
                    <button id="btn-install-pwa-nav" x-show="canInstallPwa" @click="triggerPwaInstall()" style="display: none;"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-all flex items-center gap-1.5 shadow-sm active:scale-95">
                        <svg class="w-4 h-4 text-emerald-600 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Install App HP
                    </button>

                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary px-5 py-2.5 rounded-xl text-sm font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                            <span>Buka Dashboard</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary px-5 py-2.5 rounded-xl text-sm font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            <span>Masuk ke Sistem</span>
                        </a>
                    @endauth
                </div>

                <!-- Mobile Hamburger Toggle -->
                <div class="flex items-center gap-2 md:hidden">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-3 py-1.5 text-xs font-bold bg-[#0056B3] text-white rounded-lg">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="px-3 py-1.5 text-xs font-bold bg-[#0056B3] text-white rounded-lg">Masuk</a>
                    @endauth
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="p-2 text-slate-600 hover:text-slate-900 rounded-lg border border-slate-200">
                        <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu Drawer -->
        <div x-show="mobileMenuOpen" x-transition class="md:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-6 space-y-3 shadow-xl">
            <a href="#fitur" @click="mobileMenuOpen = false" class="block px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 rounded-lg">Fitur Utama</a>
            <a href="#cara-kerja" @click="mobileMenuOpen = false" class="block px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 rounded-lg">Cara Kerja</a>
            <a href="#pwa-app" @click="mobileMenuOpen = false" class="block px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 rounded-lg">Aplikasi HP (PWA)</a>
            <a href="#keunggulan" @click="mobileMenuOpen = false" class="block px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 rounded-lg">Keunggulan</a>

            <div class="pt-2 border-t border-slate-100 flex flex-col gap-2">
                <button id="btn-install-pwa-mobile" x-show="canInstallPwa" @click="triggerPwaInstall()" style="display: none;"
                    class="w-full py-2.5 text-center text-xs font-bold text-emerald-800 bg-emerald-100 rounded-xl flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Install Aplikasi SIPEG ke HP
                </button>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-primary w-full py-3 text-center rounded-xl text-sm font-bold">Buka Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary w-full py-3 text-center rounded-xl text-sm font-bold">Masuk ke Sistem</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">

        <!-- Hero Section -->
        <section class="relative overflow-hidden pt-12 pb-20 lg:pt-20 lg:pb-32 hero-glow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                    
                    <!-- Hero Left Content -->
                    <div class="lg:col-span-7 text-center lg:text-left space-y-6">
                        
                        <!-- AI Badge -->
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-sky-100/80 border border-sky-200 text-[#0056B3] text-xs font-extrabold tracking-wide uppercase">
                            <span class="w-2 h-2 rounded-full bg-[#0056B3] animate-ping"></span>
                            AI Face Recognition & GPS Geofencing
                        </div>

                        <!-- Main Title -->
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight leading-[1.15]">
                            Sistem Kepegawaian & Presensi Biometrik Wajah <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#0056B3] via-sky-600 to-[#C8102E]">Terintegrasi</span>
                        </h1>

                        <!-- Subtitle -->
                        <p class="text-base sm:text-lg text-slate-600 max-w-2xl mx-auto lg:mx-0 font-medium leading-relaxed">
                            Platform manajemen SDM PT. Karya Agung Lestari Jaya. Merekam absensi otomatis dengan deteksi kecerdasan buatan, verifikasi radius lokasi GPS, serta alur izin & cuti secara realtime.
                        </p>

                        <!-- Action Buttons -->
                        <div class="pt-2 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                            @auth
                                <a href="{{ route('dashboard') }}" class="btn-primary w-full sm:w-auto px-8 py-4 text-base font-bold rounded-2xl shadow-lg shadow-[#0056B3]/25 hover:shadow-xl transition-all flex items-center justify-center gap-3">
                                    <span>Buka Dashboard Karyawan</span>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn-primary w-full sm:w-auto px-8 py-4 text-base font-bold rounded-2xl shadow-lg shadow-[#0056B3]/25 hover:shadow-xl transition-all flex items-center justify-center gap-3">
                                    <span>Masuk ke Sistem Absensi</span>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                </a>
                            @endauth

                            <button id="btn-hero-pwa" @click="triggerPwaInstall()" class="btn-secondary w-full sm:w-auto px-6 py-4 text-base font-bold rounded-2xl border-slate-300 hover:border-slate-400 flex items-center justify-center gap-2 text-slate-700">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                <span>Install App Mobile</span>
                            </button>
                        </div>

                        <!-- Trust Highlights -->
                        <div id="keunggulan" class="pt-6 grid grid-cols-3 gap-4 border-t border-slate-200/80 text-left">
                            <div>
                                <div class="text-xl sm:text-2xl font-extrabold text-slate-900">&lt; 1 Detik</div>
                                <div class="text-xs font-semibold text-slate-500 mt-0.5">Kecepatan Scan Wajah</div>
                            </div>
                            <div>
                                <div class="text-xl sm:text-2xl font-extrabold text-[#0056B3]">100% Presisi</div>
                                <div class="text-xs font-semibold text-slate-500 mt-0.5">Verifikasi Lokasi GPS</div>
                            </div>
                            <div>
                                <div class="text-xl sm:text-2xl font-extrabold text-emerald-600">PWA Ready</div>
                                <div class="text-xs font-semibold text-slate-500 mt-0.5">Akses HP Tanpa Download</div>
                            </div>
                        </div>

                    </div>

                    <!-- Hero Right Visual Card Mockup -->
                    <div class="lg:col-span-5 relative">
                        <div class="relative mx-auto max-w-sm sm:max-w-md bg-slate-900 rounded-[2.5rem] p-4 shadow-2xl border-4 border-slate-800 ring-1 ring-white/10">
                            
                            <!-- Phone Notch Header -->
                            <div class="flex justify-between items-center px-4 py-2 text-white/80 text-xs font-semibold">
                                <span>08:15 WIB</span>
                                <div class="w-16 h-4 bg-slate-800 rounded-full flex items-center justify-center">
                                    <div class="w-2 h-2 rounded-full bg-slate-600"></div>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span>5G</span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                                </div>
                            </div>

                            <!-- Phone Screen Inner Container -->
                            <div class="bg-slate-950 rounded-[2rem] p-4 overflow-hidden relative border border-slate-800">
                                
                                <!-- Camera View Simulator -->
                                <div class="relative aspect-[3/4] bg-slate-900 rounded-2xl overflow-hidden flex flex-col justify-between p-4 border border-slate-800">
                                    
                                    <!-- Top Status Overlay -->
                                    <div class="flex justify-between items-center z-10">
                                        <div class="bg-black/60 backdrop-blur-md px-3 py-1 rounded-full border border-white/10 text-white text-[11px] font-bold flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                            Kamera Biometrik Aktif
                                        </div>
                                        <div class="bg-sky-500/20 backdrop-blur-md text-sky-300 px-2.5 py-1 rounded-full text-[10px] font-bold border border-sky-400/30">
                                            GPS: Valid (8m)
                                        </div>
                                    </div>

                                    <!-- Face Landmark Scanning Box Simulator -->
                                    <div class="absolute inset-x-8 top-16 bottom-16 border-2 border-emerald-400/80 rounded-3xl flex items-center justify-center z-0">
                                        <!-- Scan Line Animation -->
                                        <div class="absolute inset-x-0 h-16 scan-line rounded-3xl"></div>
                                        
                                        <div class="w-32 h-32 rounded-full border border-dashed border-emerald-300/50 flex items-center justify-center">
                                            <svg class="w-16 h-16 text-emerald-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                    </div>

                                    <!-- Bottom Verification Popup -->
                                    <div class="bg-slate-900/90 backdrop-blur-md border border-slate-700/80 rounded-xl p-3 z-10 shadow-lg">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </div>
                                            <div>
                                                <div class="text-white text-xs font-bold">Wajah Terdeteksi (99.8%)</div>
                                                <div class="text-slate-400 text-[10px]">Budi Santoso — PT. KALJ (Masuk)</div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <!-- Live Status Bar Below Screen -->
                                <div class="mt-3 flex items-center justify-between text-white text-xs px-1">
                                    <span class="text-slate-400 text-[11px]">SIPEG KALJ Mobile v2.0</span>
                                    <span class="text-emerald-400 font-bold flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Siap Presensi
                                    </span>
                                </div>

                            </div>
                        </div>

                        <!-- Floating Badges Around Hero Phone Card -->
                        <div class="hidden sm:block absolute -top-4 -left-6 bg-white p-3.5 rounded-2xl shadow-xl border border-slate-100 floating-badge z-20">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-sky-100 text-[#0056B3] flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-900">Geofencing Radius</div>
                                    <div class="text-[10px] font-semibold text-slate-500">Akurasi GPS Tinggi</div>
                                </div>
                            </div>
                        </div>

                        <div class="hidden sm:block absolute -bottom-6 -right-6 bg-white p-3.5 rounded-2xl shadow-xl border border-slate-100 floating-badge z-20" style="animation-delay: 2s;">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-900">Progressive Web App</div>
                                    <div class="text-[10px] font-semibold text-slate-500">Install Langsung di HP</div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </section>

        <!-- Fitur Utama Section -->
        <section id="fitur" class="py-20 bg-white border-y border-slate-200/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Section Header -->
                <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                    <h2 class="text-xs font-extrabold text-[#0056B3] uppercase tracking-widest">Fitur Unggulan</h2>
                    <p class="text-3xl font-extrabold text-slate-900 tracking-tight">Solusi Manajemen SDM & Absensi Terdepan</p>
                    <p class="text-slate-600 text-sm sm:text-base font-medium">Dirancang khusus untuk mendukung operasional PT. Karya Agung Lestari Jaya secara efisien, presisi, dan aman.</p>
                </div>

                <!-- Features Grid (6 Cards) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    
                    <!-- Card 1 -->
                    <div class="card group hover:-translate-y-1.5 transition-all duration-300">
                        <div class="w-12 h-12 rounded-2xl bg-sky-50 border border-sky-100 text-[#0056B3] flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Presensi Biometrik Wajah AI</h3>
                        <p class="text-slate-600 text-sm font-medium leading-relaxed">
                            Pencatatan kehadiran secara akurat menggunakan deteksi titik biometrik wajah langsung dari kamera HP atau komputer.
                        </p>
                    </div>

                    <!-- Card 2 -->
                    <div class="card group hover:-translate-y-1.5 transition-all duration-300">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Validasi Geolocation GPS</h3>
                        <p class="text-slate-600 text-sm font-medium leading-relaxed">
                            Mencegah kecurangan lokasi dengan pembatasan radius koordinat kantor / site pekerjaan secara akurat.
                        </p>
                    </div>

                    <!-- Card 3 -->
                    <div class="card group hover:-translate-y-1.5 transition-all duration-300">
                        <div class="w-12 h-12 rounded-2xl bg-purple-50 border border-purple-100 text-purple-600 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Pengajuan Cuti & Izin Digital</h3>
                        <p class="text-slate-600 text-sm font-medium leading-relaxed">
                            Karyawan dapat mengajukan izin atau cuti secara instan dengan unggah dokumen pendukung dan alur approval bertingkat.
                        </p>
                    </div>

                    <!-- Card 4 -->
                    <div class="card group hover:-translate-y-1.5 transition-all duration-300">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Laporan & Ekspor Realtime</h3>
                        <p class="text-slate-600 text-sm font-medium leading-relaxed">
                            Rekapitulasi kehadiran harian/bulanan otomatis dengan fitur cetak laporan berformat PDF dan spreadsheet Excel.
                        </p>
                    </div>

                    <!-- Card 5 -->
                    <div class="card group hover:-translate-y-1.5 transition-all duration-300">
                        <div class="w-12 h-12 rounded-2xl bg-red-50 border border-red-100 text-[#C8102E] flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Manajemen Multi-Role</h3>
                        <p class="text-slate-600 text-sm font-medium leading-relaxed">
                            Hak akses terstruktur untuk Super Admin, Admin HRD, Manajer Divisi, dan Karyawan sesuai dengan wewenang masing-masing.
                        </p>
                    </div>

                    <!-- Card 6 -->
                    <div class="card group hover:-translate-y-1.5 transition-all duration-300">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Aplikasi Mobile PWA</h3>
                        <p class="text-slate-600 text-sm font-medium leading-relaxed">
                            Dapat diinstall langsung di HP Android / iPhone tanpa melalui App Store dengan respon layar fullscreen bak aplikasi native.
                        </p>
                    </div>

                </div>

            </div>
        </section>

        <!-- Cara Kerja Section -->
        <section id="cara-kerja" class="py-20 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                    <h2 class="text-xs font-extrabold text-[#0056B3] uppercase tracking-widest">Alur Penggunaan</h2>
                    <p class="text-3xl font-extrabold text-slate-900 tracking-tight">Presensi Cepat dalam 3 Langkah</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                    
                    <!-- Step 1 -->
                    <div class="bg-white p-8 rounded-3xl border border-slate-200/80 text-center space-y-4 relative z-10 shadow-sm">
                        <div class="w-14 h-14 rounded-2xl bg-[#0056B3] text-white font-extrabold text-xl flex items-center justify-center mx-auto shadow-md shadow-[#0056B3]/30">
                            1
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">Buka App & Izinkan Akses</h3>
                        <p class="text-slate-600 text-sm font-medium leading-relaxed">
                            Buka SIPEG KALJ via browser HP atau aplikasi PWA yang terinstall. Aktifkan izin GPS dan Kamera.
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div class="bg-white p-8 rounded-3xl border border-slate-200/80 text-center space-y-4 relative z-10 shadow-sm">
                        <div class="w-14 h-14 rounded-2xl bg-sky-600 text-white font-extrabold text-xl flex items-center justify-center mx-auto shadow-md shadow-sky-600/30">
                            2
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">Scan Wajah Biometrik</h3>
                        <p class="text-slate-600 text-sm font-medium leading-relaxed">
                            Arahkan wajah ke bingkai kamera. Sistem AI akan memverifikasi identitas Anda dalam hitungan detik.
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div class="bg-white p-8 rounded-3xl border border-slate-200/80 text-center space-y-4 relative z-10 shadow-sm">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-600 text-white font-extrabold text-xl flex items-center justify-center mx-auto shadow-md shadow-emerald-600/30">
                            3
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">Presensi Terverifikasi</h3>
                        <p class="text-slate-600 text-sm font-medium leading-relaxed">
                            Lokasi GPS dan waktu presensi otomatis tercatat resmi di database perusahaan.
                        </p>
                    </div>

                </div>

            </div>
        </section>

        <!-- PWA Installation Banner Section -->
        <section id="pwa-app" class="py-20 bg-gradient-to-br from-[#0b1528] to-[#17243c] text-white relative overflow-hidden">
            <!-- Background Glow Bubbles -->
            <div class="absolute -top-32 -right-32 w-96 h-96 bg-[#0056B3]/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-[#C8102E]/20 rounded-full blur-3xl"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="bg-white/5 backdrop-blur-xl rounded-3xl p-8 sm:p-12 border border-white/10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    
                    <div class="lg:col-span-8 space-y-4 text-center lg:text-left">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-extrabold border border-emerald-500/30">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            Progressive Web App (PWA)
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Gunakan SIPEG KALJ Layaknya Aplikasi Native di Smartphone Anda</h2>
                        <p class="text-slate-300 text-sm sm:text-base font-medium max-w-2xl">
                            Nikmati kemudahan presensi harian dengan menambahkan aplikasi ini langsung ke Layar Utama (Home Screen) HP Anda. Tanpa proses unduh yang memakan memori besar.
                        </p>

                        <div class="pt-2 flex flex-wrap gap-4 justify-center lg:justify-start text-xs font-semibold text-slate-300">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Support Android & iOS
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Loading Kilat & Hemat Kuota
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Akses Cepat 1-Click
                            </span>
                        </div>
                    </div>

                    <div class="lg:col-span-4 flex justify-center lg:justify-end">
                        <button id="btn-banner-pwa" @click="triggerPwaInstall()"
                            class="w-full sm:w-auto px-8 py-4 bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-base rounded-2xl shadow-xl shadow-emerald-500/30 transition-all flex items-center justify-center gap-3 active:scale-95">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Install Ke HP Sekarang</span>
                        </button>
                    </div>

                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center p-0.5 overflow-hidden">
                        <img src="{{ asset('storage/logo.jpeg') }}" alt="Logo KALJ" class="w-full h-full object-cover rounded-lg">
                    </div>
                    <div>
                        <div class="text-white font-bold text-sm">SIPEG KALJ</div>
                        <div class="text-xs text-slate-400">PT. Karya Agung Lestari Jaya</div>
                    </div>
                </div>

                <div class="flex items-center gap-2 text-xs font-medium text-emerald-400 bg-emerald-500/10 px-3 py-1.5 rounded-full border border-emerald-500/20">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Sistem Biometrik Wajah Online
                </div>

                <p class="text-xs text-slate-500 text-center sm:text-right">
                    © {{ date('Y') }} PT. Karya Agung Lestari Jaya. All rights reserved.
                </p>

            </div>
        </div>
    </footer>

    <!-- Service Worker & PWA Install Script -->
    <script>
        // Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('PWA Service Worker registered:', reg.scope))
                    .catch(err => console.log('Service Worker registration failed:', err));
            });
        }

        // PWA Install Prompt Listener
        let deferredPrompt = null;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            
            // Notify Alpine component that PWA install is ready
            const body = document.querySelector('body');
            if (body && body._x_dataStack) {
                body._x_dataStack[0].canInstallPwa = true;
            }

            // Show install buttons if hidden
            document.querySelectorAll('#btn-install-pwa-nav, #btn-install-pwa-mobile').forEach(btn => {
                btn.style.display = 'inline-flex';
            });
        });

        function triggerPwaInstall() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('User accepted the PWA install prompt');
                    }
                    deferredPrompt = null;
                });
            } else {
                alert('Aplikasi SIPEG KALJ sudah terinstall atau dapat ditambahkan melalui menu browser Anda ("Tambahkan ke Layar Utama" / "Add to Home Screen").');
            }
        }
    </script>

</body>

</html>

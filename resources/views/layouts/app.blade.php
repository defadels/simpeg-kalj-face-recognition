<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — SIPEG KALJ</title>
    <meta name="description" content="Sistem Informasi Kepegawaian PT. Karya Agung Lestari Jaya">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Leaflet CSS (Load secara lokal setelah CSS Tailwind agar style tile image tidak tertimpa reset) -->
    <link rel="stylesheet" href="/css/leaflet.css" />

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full bg-slate-50 text-slate-800" x-data="{ mobileSidebarOpen: false }">
<div class="flex h-screen overflow-hidden">
    
    <!-- Sidebar for Desktop -->
    <aside class="hidden lg:flex sidebar w-66 flex-shrink-0 flex-col h-full overflow-y-auto">
        <!-- Logo -->
        <div class="flex items-center gap-3 px-6 py-6 border-b border-white/5">
            <div class="w-11 h-11 bg-white rounded-full flex items-center justify-center flex-shrink-0 shadow-sm border border-red-100 p-0.5 overflow-hidden">
                <img src="{{ asset('storage/logo.jpeg') }}" alt="Logo KALJ" class="w-full h-full object-cover">
            </div>
            <div>
                <div class="text-white font-extrabold text-sm tracking-tight leading-none">SIPEG KALJ</div>
                <div class="text-slate-400 text-[10px] mt-1 font-medium truncate max-w-[150px]">Karya Agung Lestari Jaya</div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-5 space-y-1">
            @php $user = auth()->user(); @endphp

            {{-- ADMIN --}}
            @if($user->isAdmin())
                <div class="sidebar-group-label">Administrasi</div>
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Dashboard Admin
                </a>
                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Kelola Pengguna
                </a>

                <div class="sidebar-group-label">Master Data</div>
                <a href="{{ route('admin.karyawan.index') }}" class="sidebar-link {{ request()->routeIs('admin.karyawan.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Data Karyawan
                </a>
                <a href="{{ route('admin.jabatan.index') }}" class="sidebar-link {{ request()->routeIs('admin.jabatan.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Jabatan
                </a>
                <a href="{{ route('admin.divisi.index') }}" class="sidebar-link {{ request()->routeIs('admin.divisi.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Divisi
                </a>

                <div class="sidebar-group-label">Kehadiran & Cuti</div>
                <a href="{{ route('admin.absensi.monitor') }}" class="sidebar-link {{ request()->routeIs('admin.absensi.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Monitor Absensi
                </a>
                <a href="{{ route('admin.cuti-izin.index') }}" class="sidebar-link {{ request()->routeIs('admin.cuti-izin.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Persetujuan Cuti
                </a>

                <div class="sidebar-group-label">Laporan & Sistem</div>
                <a href="{{ route('admin.laporan.index') }}" class="sidebar-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Laporan Kehadiran
                </a>
                <a href="{{ route('admin.konfigurasi.index') }}" class="sidebar-link {{ request()->routeIs('admin.konfigurasi.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Konfigurasi Sistem
                </a>
            @else
                <div class="sidebar-group-label">Karyawan</div>
                <a href="{{ route('karyawan.dashboard') }}" class="sidebar-link {{ request()->routeIs('karyawan.dashboard') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('karyawan.absensi.index') }}" class="sidebar-link {{ request()->routeIs('karyawan.absensi.index') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Absensi Wajah
                </a>
                <a href="{{ route('karyawan.absensi.riwayat') }}" class="sidebar-link {{ request()->routeIs('karyawan.absensi.riwayat') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Riwayat Absensi
                </a>
                <a href="{{ route('karyawan.cuti-izin.index') }}" class="sidebar-link {{ request()->routeIs('karyawan.cuti-izin.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Cuti & Izin
                </a>
            @endif
        </nav>

        <!-- User Profile Card -->
        <div class="p-4 border-t border-white/5 bg-[#070e1b]/50">
            <div class="flex items-center gap-3">
                <img src="{{ auth()->user()->avatar }}" class="w-10 h-10 rounded-xl object-cover ring-2 ring-white/10" alt="Avatar">
                <div class="flex-1 min-w-0">
                    <div class="text-white text-xs font-bold truncate leading-tight">{{ auth()->user()->nama }}</div>
                    <div class="text-slate-500 text-[10px] truncate mt-0.5 font-medium uppercase tracking-wider">{{ auth()->user()->role }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-red-600/10 hover:bg-red-600 text-red-400 hover:text-white rounded-xl text-xs font-semibold transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Mobile Navigation Drawer -->
    <div x-show="mobileSidebarOpen" class="fixed inset-0 z-50 flex lg:hidden" style="display: none;">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="mobileSidebarOpen = false"></div>
        
        <!-- Drawer content -->
        <aside class="relative sidebar w-64 flex flex-col h-full overflow-y-auto shadow-2xl">
            <!-- Close Button -->
            <button @click="mobileSidebarOpen = false" class="absolute top-4 right-4 text-slate-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <!-- Logo -->
            <div class="flex items-center gap-3 px-6 py-6 border-b border-white/5">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center flex-shrink-0 shadow-sm overflow-hidden">
                    <img src="{{ asset('storage/logo.jpeg') }}" alt="Logo KALJ" class="w-full h-full object-cover">
                </div>
                <div>
                    <div class="text-white font-extrabold text-sm leading-none">SIPEG KALJ</div>
                    <div class="text-slate-400 text-[10px] mt-1 font-medium">PT. KALJ</div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-5 space-y-1">
                @if($user->isAdmin())
                    <div class="sidebar-group-label">Administrasi</div>
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard Admin</a>
                    <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Kelola Pengguna</a>
                    <div class="sidebar-group-label">Master Data</div>
                    <a href="{{ route('admin.karyawan.index') }}" class="sidebar-link {{ request()->routeIs('admin.karyawan.*') ? 'active' : '' }}">Karyawan</a>
                    <a href="{{ route('admin.jabatan.index') }}" class="sidebar-link {{ request()->routeIs('admin.jabatan.*') ? 'active' : '' }}">Jabatan</a>
                    <a href="{{ route('admin.divisi.index') }}" class="sidebar-link {{ request()->routeIs('admin.divisi.*') ? 'active' : '' }}">Divisi</a>
                    <div class="sidebar-group-label">Kehadiran & Cuti</div>
                    <a href="{{ route('admin.absensi.monitor') }}" class="sidebar-link {{ request()->routeIs('admin.absensi.*') ? 'active' : '' }}">Monitor Absensi</a>
                    <a href="{{ route('admin.cuti-izin.index') }}" class="sidebar-link {{ request()->routeIs('admin.cuti-izin.*') ? 'active' : '' }}">Approval Cuti</a>
                    <div class="sidebar-group-label">Laporan & Sistem</div>
                    <a href="{{ route('admin.laporan.index') }}" class="sidebar-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">Laporan</a>
                    <a href="{{ route('admin.konfigurasi.index') }}" class="sidebar-link {{ request()->routeIs('admin.konfigurasi.*') ? 'active' : '' }}">Konfigurasi Sistem</a>
                @else
                    <div class="sidebar-group-label">Karyawan</div>
                    <a href="{{ route('karyawan.dashboard') }}" class="sidebar-link {{ request()->routeIs('karyawan.dashboard') ? 'active' : '' }}">Dashboard</a>
                    <a href="{{ route('karyawan.absensi.index') }}" class="sidebar-link {{ request()->routeIs('karyawan.absensi.index') ? 'active' : '' }}">Absensi</a>
                    <a href="{{ route('karyawan.absensi.riwayat') }}" class="sidebar-link {{ request()->routeIs('karyawan.absensi.riwayat') ? 'active' : '' }}">Riwayat</a>
                    <a href="{{ route('karyawan.cuti-izin.index') }}" class="sidebar-link {{ request()->routeIs('karyawan.cuti-izin.*') ? 'active' : '' }}">Cuti & Izin</a>
                @endif
            </nav>

            <!-- User Info (mobile) -->
            <div class="p-4 border-t border-white/5 bg-[#070e1b]/50">
                <div class="flex items-center gap-3">
                    <img src="{{ auth()->user()->avatar }}" class="w-9 h-9 rounded-xl object-cover" alt="Avatar">
                    <div class="flex-1 min-w-0">
                        <div class="text-white text-xs font-bold truncate leading-tight">{{ auth()->user()->nama }}</div>
                        <div class="text-slate-500 text-[9px] truncate font-medium uppercase tracking-wider">{{ auth()->user()->role }}</div>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <!-- Main Content wrapper -->
    <main class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Bar Header -->
        <header class="bg-white border-b border-slate-100 px-6 py-4 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3">
                <!-- Hamburger Button for Mobile -->
                <button @click="mobileSidebarOpen = true" class="lg:hidden p-1.5 text-slate-500 hover:text-slate-700 bg-slate-100 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h1 class="text-lg font-bold text-slate-800 tracking-tight">{{ $title ?? 'Dashboard' }}</h1>
                    @isset($breadcrumb)
                        <p class="text-xs font-medium text-slate-400 mt-0.5">{{ $breadcrumb }}</p>
                    @endisset
                </div>
            </div>
            
            <!-- Date/Time Display -->
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <div class="text-xs font-bold text-slate-600">{{ now()->isoFormat('dddd, D MMMM Y') }}</div>
                    <div class="text-[10px] font-medium text-slate-400 mt-0.5" x-data="{}" x-init="setInterval(() => $el.textContent = new Date().toLocaleTimeString('id-ID'), 1000)">
                        {{ now()->format('H:i:s') }}
                    </div>
                </div>
            </div>
        </header>

        <!-- Dynamic Content Body -->
        <div class="flex-1 overflow-y-auto p-6 bg-[#f8fafc]">
            {{-- Flash Alert Messages --}}
            @if(session('success'))
                <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3 shadow-sm" x-data="{ show: true }" x-show="show" x-transition>
                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    </div>
                    <p class="text-emerald-800 text-sm font-medium flex-1">{{ session('success') }}</p>
                    <button @click="show = false" class="text-emerald-400 hover:text-emerald-600"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></button>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-2xl flex items-center gap-3 shadow-sm" x-data="{ show: true }" x-show="show" x-transition>
                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    </div>
                    <p class="text-red-800 text-sm font-medium flex-1">{{ session('error') }}</p>
                    <button @click="show = false" class="text-red-400 hover:text-red-600"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></button>
                </div>
            @endif

            {{ $slot }}
        </div>
    </main>
</div>

<!-- Leaflet JS (Load secara lokal untuk mencegah pemblokiran CDN/Adblocker) -->
<script src="/js/leaflet.js"></script>
@stack('scripts')
</body>
</html>

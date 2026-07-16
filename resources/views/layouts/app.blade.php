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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
        :root {
            --primary: #0ea5e9;
            --primary-dark: #0284c7;
            --secondary: #0f766e;
            --sidebar-bg: #0c1a2e;
            --sidebar-hover: #1a2d47;
        }
        .sidebar { background: var(--sidebar-bg); }
        .sidebar-link { @apply flex items-center gap-3 px-4 py-2.5 rounded-lg text-slate-300 text-sm font-medium transition-all duration-200; }
        .sidebar-link:hover { background: var(--sidebar-hover); @apply text-white; }
        .sidebar-link.active { background: var(--primary); @apply text-white shadow-lg; }
        .sidebar-group-label { @apply text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 mb-2 mt-4; }
        .card { @apply bg-white rounded-2xl shadow-sm border border-slate-100 p-6; }
        .stat-card { @apply rounded-2xl p-6 text-white relative overflow-hidden; }
        .btn-primary { @apply bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 inline-flex items-center gap-2; }
        .btn-secondary { @apply bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 inline-flex items-center gap-2; }
        .btn-danger { @apply bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 inline-flex items-center gap-2; }
        .btn-success { @apply bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 inline-flex items-center gap-2; }
        .table-row { @apply border-b border-slate-50 hover:bg-slate-50 transition-colors; }
        .badge { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium; }
        .badge-green { @apply bg-emerald-100 text-emerald-800; }
        .badge-red { @apply bg-red-100 text-red-800; }
        .badge-yellow { @apply bg-amber-100 text-amber-800; }
        .badge-blue { @apply bg-sky-100 text-sky-800; }
        .badge-purple { @apply bg-purple-100 text-purple-800; }
        .badge-gray { @apply bg-slate-100 text-slate-700; }
        .form-input { @apply w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all; }
        .form-label { @apply block text-sm font-medium text-slate-700 mb-1; }
        .form-error { @apply text-red-500 text-xs mt-1; }
        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="h-full bg-slate-50 text-slate-900">
<div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <aside class="sidebar w-64 flex-shrink-0 flex flex-col h-full overflow-y-auto">
        <!-- Logo -->
        <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10">
            <div class="w-10 h-10 bg-sky-500 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4v13l-9 4-9-4V7z"/>
                </svg>
            </div>
            <div>
                <div class="text-white font-bold text-sm leading-tight">SIPEG KALJ</div>
                <div class="text-slate-400 text-xs">PT. Karya Agung Lestari Jaya</div>
            </div>
        </div>

        <!-- Nav -->
        <nav class="flex-1 px-3 py-4 space-y-0.5">
            @php $role = auth()->user()->role; @endphp

            {{-- SUPER ADMIN --}}
            @if($role === 'super_admin')
                <div class="sidebar-group-label">Administrasi</div>
                <a href="{{ route('super-admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('super-admin.users.index') }}" class="sidebar-link {{ request()->routeIs('super-admin.users.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Kelola Pengguna
                </a>
                <a href="{{ route('super-admin.konfigurasi.index') }}" class="sidebar-link {{ request()->routeIs('super-admin.konfigurasi.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Konfigurasi Sistem
                </a>
            @endif

            {{-- ADMIN HRD --}}
            @if($role === 'admin_hrd')
                <div class="sidebar-group-label">Dashboard</div>
                <a href="{{ route('admin-hrd.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin-hrd.dashboard') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="3" y="14" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="14" width="7" height="7" rx="1" stroke-width="2"/></svg>
                    Dashboard
                </a>
                <div class="sidebar-group-label">Master Data</div>
                <a href="{{ route('admin-hrd.karyawan.index') }}" class="sidebar-link {{ request()->routeIs('admin-hrd.karyawan.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Data Karyawan
                </a>
                <a href="{{ route('admin-hrd.jabatan.index') }}" class="sidebar-link {{ request()->routeIs('admin-hrd.jabatan.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Jabatan
                </a>
                <a href="{{ route('admin-hrd.divisi.index') }}" class="sidebar-link {{ request()->routeIs('admin-hrd.divisi.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Divisi
                </a>
                <div class="sidebar-group-label">Kehadiran</div>
                <a href="{{ route('admin-hrd.absensi.monitor') }}" class="sidebar-link {{ request()->routeIs('admin-hrd.absensi.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Monitor Absensi
                </a>
                <a href="{{ route('admin-hrd.cuti-izin.index') }}" class="sidebar-link {{ request()->routeIs('admin-hrd.cuti-izin.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Cuti & Izin
                </a>
                <div class="sidebar-group-label">Laporan</div>
                <a href="{{ route('admin-hrd.laporan.index') }}" class="sidebar-link {{ request()->routeIs('admin-hrd.laporan.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Laporan Kehadiran
                </a>
            @endif

            {{-- MANAJER --}}
            @if($role === 'manajer')
                <div class="sidebar-group-label">Dashboard</div>
                <a href="{{ route('manajer.dashboard') }}" class="sidebar-link {{ request()->routeIs('manajer.dashboard') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="3" y="14" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="14" width="7" height="7" rx="1" stroke-width="2"/></svg>
                    Dashboard
                </a>
                <div class="sidebar-group-label">Tim Saya</div>
                <a href="{{ route('manajer.absensi.monitor') }}" class="sidebar-link {{ request()->routeIs('manajer.absensi.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Monitor Absensi
                </a>
                <a href="{{ route('manajer.cuti-izin.index') }}" class="sidebar-link {{ request()->routeIs('manajer.cuti-izin.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Persetujuan Cuti/Izin
                </a>
                <a href="{{ route('manajer.laporan.index') }}" class="sidebar-link {{ request()->routeIs('manajer.laporan.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Laporan
                </a>
            @endif

            {{-- KARYAWAN (dan semua role) --}}
            @if($role === 'karyawan')
                <div class="sidebar-group-label">Saya</div>
                <a href="{{ route('karyawan.dashboard') }}" class="sidebar-link {{ request()->routeIs('karyawan.dashboard') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="3" y="14" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="14" width="7" height="7" rx="1" stroke-width="2"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('karyawan.absensi.index') }}" class="sidebar-link {{ request()->routeIs('karyawan.absensi.index') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Absensi
                </a>
                <a href="{{ route('karyawan.absensi.riwayat') }}" class="sidebar-link {{ request()->routeIs('karyawan.absensi.riwayat') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Riwayat Absensi
                </a>
                <a href="{{ route('karyawan.cuti-izin.index') }}" class="sidebar-link {{ request()->routeIs('karyawan.cuti-izin.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Cuti & Izin
                </a>
            @endif
        </nav>

        <!-- User Profile -->
        <div class="px-3 py-4 border-t border-white/10">
            <div class="flex items-center gap-3 px-3 py-2">
                <img src="{{ auth()->user()->avatar }}" class="w-9 h-9 rounded-lg object-cover" alt="Avatar">
                <div class="flex-1 min-w-0">
                    <div class="text-white text-sm font-medium truncate">{{ auth()->user()->nama }}</div>
                    <div class="text-slate-400 text-xs truncate">{{ str_replace('_', ' ', ucwords(auth()->user()->role, '_')) }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="sidebar-link w-full text-left text-red-400 hover:text-red-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Bar -->
        <header class="bg-white border-b border-slate-100 px-6 py-4 flex items-center justify-between flex-shrink-0">
            <div>
                <h1 class="text-lg font-semibold text-slate-900">{{ $title ?? 'Dashboard' }}</h1>
                @isset($breadcrumb)
                    <p class="text-xs text-slate-500 mt-0.5">{{ $breadcrumb }}</p>
                @endisset
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <div class="text-sm font-medium text-slate-700">{{ now()->isoFormat('dddd, D MMMM Y') }}</div>
                    <div class="text-xs text-slate-500" x-data="{}" x-init="setInterval(() => $el.textContent = new Date().toLocaleTimeString('id-ID'), 1000)">
                        {{ now()->format('H:i:s') }}
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="flex-1 overflow-y-auto p-6">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-3" x-data="{ show: true }" x-show="show" x-transition>
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <p class="text-emerald-700 text-sm font-medium flex-1">{{ session('success') }}</p>
                    <button @click="show = false" class="text-emerald-500 hover:text-emerald-700"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></button>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3" x-data="{ show: true }" x-show="show" x-transition>
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <p class="text-red-700 text-sm font-medium flex-1">{{ session('error') }}</p>
                    <button @click="show = false" class="text-red-500 hover:text-red-700"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></button>
                </div>
            @endif

            {{ $slot }}
        </div>
    </main>
</div>
@stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SIPEG KALJ</title>
    <meta name="description" content="Sistem Informasi Kepegawaian PT. Karya Agung Lestari Jaya">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full bg-slate-100 flex items-center justify-center p-4 min-h-screen">

    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <!-- Blue bubble -->
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-[#0056B3]/5 rounded-full blur-3xl"></div>
        <!-- Red bubble -->
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-[#C8102E]/5 rounded-full blur-3xl"></div>
    </div>

    <div class="w-full max-w-md relative z-10">
        <!-- Logo & Header -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-3xl mb-3 shadow-md border border-slate-150 p-2 overflow-hidden">
                <img src="{{ asset('storage/logo.jpeg') }}" alt="Logo KALJ" class="w-full h-full object-cover rounded-2xl">
            </div>
            <h1 class="text-slate-800 text-2xl font-extrabold tracking-tight">SIPEG KALJ</h1>
            <p class="text-slate-400 text-xs mt-1 font-medium">PT. Karya Agung Lestari Jaya — Medan, Indonesia</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-xl p-8">
            <div class="mb-6">
                <h2 class="text-lg font-bold text-slate-800">Masuk ke Akun</h2>
                <p class="text-slate-400 text-xs mt-1 font-medium">Masukkan email dan password untuk merekam kehadiran</p>
            </div>

            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl">
                    @foreach($errors->all() as $error)
                        <p class="text-red-700 text-xs font-semibold">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="form-label" for="email">Alamat Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="form-input" placeholder="nama@perusahaan.com">
                </div>

                <div>
                    <label class="form-label" for="password">Password</label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" id="password" name="password" required
                            class="form-input pr-12" placeholder="••••••••">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-slate-600">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-[#0056B3] border-slate-200 rounded focus:ring-[#0056B3]">
                        <span class="text-xs font-semibold text-slate-500">Ingat Saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full btn-primary justify-center py-3">
                    Masuk ke Sistem
                </button>
            </form>

            <!-- Demo accounts -->
            <div class="mt-6 pt-6 border-t border-slate-100">
                <p class="text-[10px] text-slate-400 text-center mb-3 font-bold uppercase tracking-widest">Akun Demo</p>
                <div class="grid grid-cols-2 gap-2 text-[11px]">
                    @foreach([
                        ['label' => 'Super Admin', 'email' => 'superadmin@sipeg.local', 'color' => 'bg-purple-50 text-purple-700 border-purple-100 hover:bg-purple-100'],
                        ['label' => 'Admin HRD', 'email' => 'adminhrd@sipeg.local', 'color' => 'bg-sky-50 text-sky-700 border-sky-100 hover:bg-sky-100'],
                        ['label' => 'Manajer', 'email' => 'manajer@sipeg.local', 'color' => 'bg-amber-50 text-amber-700 border-amber-100 hover:bg-amber-100'],
                        ['label' => 'Karyawan', 'email' => 'karyawan@sipeg.local', 'color' => 'bg-emerald-50 text-emerald-700 border-emerald-100 hover:bg-emerald-100'],
                    ] as $demo)
                        <button type="button" onclick="fillDemo('{{ $demo['email'] }}')"
                            class="border {{ $demo['color'] }} rounded-xl py-2 text-center font-bold transition-colors">
                            {{ $demo['label'] }}
                        </button>
                    @endforeach
                </div>
                <p class="text-[10px] text-slate-400 text-center mt-3 font-medium">Password semua akun: <code class="bg-slate-100 px-1 rounded font-semibold">password</code></p>
            </div>
        </div>

        <p class="text-center text-slate-400 text-[10px] mt-6 font-semibold">© {{ date('Y') }} PT. Karya Agung Lestari Jaya. All rights reserved.</p>
    </div>

    <script>
        function fillDemo(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password';
        }
    </script>
</body>
</html>

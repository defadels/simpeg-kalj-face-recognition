<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SIPEG KALJ</title>
    <meta name="description" content="Sistem Informasi Kepegawaian PT. Karya Agung Lestari Jaya">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .ocean-gradient {
            background: linear-gradient(135deg, #0c1a2e 0%, #0f3460 40%, #0ea5e9 100%);
        }
        .glass-card {
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(20px);
        }
        .wave {
            animation: wave 8s ease-in-out infinite;
        }
        @keyframes wave {
            0%, 100% { transform: translateY(0) scaleX(1); }
            50% { transform: translateY(-10px) scaleX(1.02); }
        }
        .float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body class="h-full ocean-gradient flex items-center justify-center p-4 min-h-screen">

    <!-- Decorative elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-sky-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-sky-400/10 rounded-full blur-3xl"></div>
        <!-- Fish/wave decorations -->
        <svg class="absolute bottom-0 left-0 w-full opacity-10 wave" viewBox="0 0 1440 320" fill="none">
            <path fill="#0ea5e9" fill-opacity="0.6" d="M0,192L48,176C96,160,192,128,288,138.7C384,149,480,203,576,208C672,213,768,171,864,160C960,149,1056,171,1152,165.3C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"/>
        </svg>
    </div>

    <div class="w-full max-w-md relative z-10">
        <!-- Logo & Header -->
        <div class="text-center mb-8 float">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur rounded-3xl mb-4 shadow-2xl">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4v13l-9 4-9-4V7z"/>
                </svg>
            </div>
            <h1 class="text-white text-3xl font-bold tracking-tight">SIPEG KALJ</h1>
            <p class="text-sky-200 text-sm mt-1">Sistem Informasi Kepegawaian</p>
            <p class="text-sky-300/70 text-xs mt-0.5">PT. Karya Agung Lestari Jaya</p>
        </div>

        <!-- Login Card -->
        <div class="glass-card rounded-3xl shadow-2xl p-8">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-slate-900">Masuk ke Akun Anda</h2>
                <p class="text-slate-500 text-sm mt-1">Gunakan email dan password yang telah didaftarkan</p>
            </div>

            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl">
                    @foreach($errors->all() as $error)
                        <p class="text-red-600 text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5" for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all bg-slate-50/50"
                        placeholder="nama@perusahaan.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5" for="password">Password</label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" id="password" name="password" required
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all bg-slate-50/50 pr-12"
                            placeholder="••••••••">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-slate-600">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-sky-500 border-slate-300 rounded focus:ring-sky-500">
                        <span class="text-sm text-slate-600">Ingat saya</span>
                    </label>
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-semibold py-3 rounded-xl transition-all duration-200 shadow-lg shadow-sky-500/30 hover:shadow-sky-500/40 hover:-translate-y-0.5 active:translate-y-0">
                    Masuk
                </button>
            </form>

            <!-- Demo accounts -->
            <div class="mt-6 pt-6 border-t border-slate-100">
                <p class="text-xs text-slate-400 text-center mb-3 font-medium uppercase tracking-wider">Akun Demo</p>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    @foreach([
                        ['label' => 'Super Admin', 'email' => 'superadmin@sipeg.local', 'color' => 'bg-purple-50 text-purple-700 border-purple-100'],
                        ['label' => 'Admin HRD', 'email' => 'adminhrd@sipeg.local', 'color' => 'bg-sky-50 text-sky-700 border-sky-100'],
                        ['label' => 'Manajer', 'email' => 'manajer@sipeg.local', 'color' => 'bg-amber-50 text-amber-700 border-amber-100'],
                        ['label' => 'Karyawan', 'email' => 'karyawan@sipeg.local', 'color' => 'bg-emerald-50 text-emerald-700 border-emerald-100'],
                    ] as $demo)
                        <button type="button" onclick="fillDemo('{{ $demo['email'] }}')"
                            class="border {{ $demo['color'] }} rounded-lg px-2 py-1.5 text-center font-medium hover:opacity-80 transition-opacity">
                            {{ $demo['label'] }}
                        </button>
                    @endforeach
                </div>
                <p class="text-xs text-slate-400 text-center mt-2">Password semua akun: <code class="bg-slate-100 px-1 rounded">password</code></p>
            </div>
        </div>

        <p class="text-center text-sky-300/50 text-xs mt-6">© {{ date('Y') }} PT. Karya Agung Lestari Jaya. All rights reserved.</p>
    </div>

    <script>
        function fillDemo(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password';
        }
    </script>
</body>
</html>

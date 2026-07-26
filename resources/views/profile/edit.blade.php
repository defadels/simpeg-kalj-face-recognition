<x-app-layout>
    <x-slot name="title">Pengaturan Profil</x-slot>
    <x-slot name="breadcrumb">Kelola data pribadi, foto profil, dan kata sandi akun Anda</x-slot>

    <div class="max-w-4xl space-y-6">

        @if (session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm font-medium">
                <div class="font-bold mb-1">Terjadi kesalahan:</div>
                <ul class="list-disc list-inside space-y-0.5 text-xs text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Card Header Info Ringkas -->
        <div class="card bg-gradient-to-br from-slate-900 via-slate-800 to-[#0056B3] text-white overflow-hidden relative">
            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-sky-500/10 rounded-full blur-2xl"></div>
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 relative z-10">
                <div class="relative group">
                    <img id="avatarPreview" src="{{ $user->avatar }}" class="w-24 h-24 rounded-2xl object-cover ring-4 ring-white/20 shadow-xl" alt="Foto Profil">
                </div>
                <div class="flex-1 text-center sm:text-left">
                    <h2 class="text-xl font-extrabold tracking-tight text-white">{{ $user->nama }}</h2>
                    <p class="text-sky-300 text-xs mt-0.5">{{ $user->email }}</p>
                    
                    @if($karyawan)
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mt-3">
                            <span class="px-2.5 py-1 rounded-lg bg-white/10 text-white text-xs font-semibold backdrop-blur-md">
                                ID: {{ $karyawan->nip }}
                            </span>
                            <span class="px-2.5 py-1 rounded-lg bg-sky-500/30 text-sky-100 text-xs font-semibold backdrop-blur-md">
                                {{ $karyawan->jabatan?->nama_jabatan ?? 'Tanpa Jabatan' }}
                            </span>
                            <span class="px-2.5 py-1 rounded-lg bg-emerald-500/30 text-emerald-100 text-xs font-semibold backdrop-blur-md">
                                Divisi {{ $karyawan->divisi?->nama_divisi ?? 'Umum' }}
                            </span>
                            <span class="px-2.5 py-1 rounded-lg bg-amber-500/30 text-amber-100 text-xs font-semibold backdrop-blur-md">
                                Saldo Cuti: {{ $karyawan->saldo_cuti }} hari
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Form Edit Data Profil -->
        <div class="card">
            <h3 class="font-bold text-slate-800 text-base mb-4 pb-3 border-b border-slate-100 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#0056B3]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Informasi Pribadi & Kontak
            </h3>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PATCH')

                <!-- File Upload Foto Profil -->
                <div>
                    <label class="form-label">Foto Profil Baru (Opsional)</label>
                    <input type="file" name="foto" accept="image/*" class="form-input text-xs" onchange="previewImage(event)">
                    <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, WEBP. Ukuran maks: 2MB.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" class="form-input" required>
                    </div>

                    <div>
                        <label class="form-label">Alamat Email *</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" required>
                    </div>
                </div>

                @if($karyawan)
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="form-label">Nomor Telepon / WhatsApp</label>
                            <input type="text" name="no_telp" value="{{ old('no_telp', $karyawan->no_telp) }}" class="form-input" placeholder="08xxxxxxxxxx">
                        </div>

                        <div>
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-input">
                                <option value="L" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $karyawan->tanggal_lahir?->format('Y-m-d')) }}" class="form-input">
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Alamat Tempat Tinggal</label>
                        <textarea name="alamat" rows="2" class="form-input" placeholder="Alamat lengkap lokasi domisili...">{{ old('alamat', $karyawan->alamat) }}</textarea>
                    </div>
                @endif

                <div class="pt-3 flex justify-end">
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Simpan Perubahan Profil
                    </button>
                </div>
            </form>
        </div>

        <!-- Form Ubah Password -->
        <div class="card">
            <h3 class="font-bold text-slate-800 text-base mb-4 pb-3 border-b border-slate-100 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#0056B3]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Keamanan & Ubah Kata Sandi
            </h3>

            <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                @method('put')

                <div>
                    <label class="form-label">Kata Sandi Saat Ini *</label>
                    <input type="password" name="current_password" class="form-input max-w-md" autocomplete="current-password">
                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-2xl">
                    <div>
                        <label class="form-label">Kata Sandi Baru *</label>
                        <input type="password" name="password" class="form-input" autocomplete="new-password">
                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
                    </div>

                    <div>
                        <label class="form-label">Konfirmasi Kata Sandi Baru *</label>
                        <input type="password" name="password_confirmation" class="form-input" autocomplete="new-password">
                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
                    </div>
                </div>

                <div class="pt-3 flex justify-end">
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Perbarui Kata Sandi
                    </button>
                </div>
            </form>
        </div>

    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('avatarPreview');
                output.src = reader.result;
            };
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }
    </script>
</x-app-layout>

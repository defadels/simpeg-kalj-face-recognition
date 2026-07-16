<x-app-layout>
    <x-slot name="title">Edit Pengguna</x-slot>

    <div class="max-w-2xl">
        <div class="card">
            <h3 class="font-semibold text-slate-800 mb-6">Edit Pengguna: {{ $user->nama }}</h3>
            <form method="POST" action="{{ route('super-admin.users.update', $user) }}" class="space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" class="form-input" required>
                        @error('nama')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" required>
                        @error('email')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Password Baru <span class="text-slate-400">(kosongkan jika tidak diubah)</span></label>
                        <input type="password" name="password" class="form-input">
                        @error('password')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Role *</label>
                        <select name="role" class="form-input" required>
                            <option value="karyawan" {{ old('role', $user->role) === 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                            <option value="manajer" {{ old('role', $user->role) === 'manajer' ? 'selected' : '' }}>Manajer</option>
                            <option value="admin_hrd" {{ old('role', $user->role) === 'admin_hrd' ? 'selected' : '' }}>Admin HRD</option>
                            <option value="super_admin" {{ old('role', $user->role) === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                        </select>
                    </div>
                    <div class="flex items-end pb-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }} class="w-4 h-4 rounded text-sky-500">
                            <span class="text-sm font-medium text-slate-700">Akun Aktif</span>
                        </label>
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-primary">Perbarui</button>
                    <a href="{{ route('super-admin.users.index') }}" class="btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

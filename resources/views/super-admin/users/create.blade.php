<x-app-layout>
    <x-slot name="title">Tambah Pengguna</x-slot>

    <div class="max-w-2xl">
        <div class="card">
            <h3 class="font-semibold text-slate-800 mb-6">Informasi Pengguna</h3>
            <form method="POST" action="{{ route('super-admin.users.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text" name="nama" value="{{ old('nama') }}" class="form-input" required>
                        @error('nama')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-input" required>
                        @error('email')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" class="form-input" required>
                        @error('password')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Konfirmasi Password *</label>
                        <input type="password" name="password_confirmation" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Role *</label>
                        <select name="role" class="form-input" required>
                            <option value="karyawan" {{ old('role') === 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                            <option value="manajer" {{ old('role') === 'manajer' ? 'selected' : '' }}>Manajer</option>
                            <option value="admin_hrd" {{ old('role') === 'admin_hrd' ? 'selected' : '' }}>Admin HRD</option>
                            <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                        </select>
                        @error('role')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex items-end pb-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded text-sky-500">
                            <span class="text-sm font-medium text-slate-700">Akun Aktif</span>
                        </label>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-primary">Simpan Pengguna</button>
                    <a href="{{ route('super-admin.users.index') }}" class="btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="title">Tambah Karyawan</x-slot>

    <div class="max-w-3xl">
        <div class="card">
            <h3 class="font-semibold text-slate-800 mb-5">Data Karyawan Baru</h3>
            <form method="POST" action="{{ route('admin-hrd.karyawan.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="form-label">ID Karyawan *</label><input type="text" name="nip" value="{{ old('nip', $nextIdKaryawan) }}" class="form-input font-mono font-bold bg-slate-50 text-slate-700" required placeholder="KALJ-0001">@error('nip')<p class="form-error">{{ $message }}</p>@enderror</div>
                    <div><label class="form-label">Nama Lengkap *</label><input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" class="form-input" required>@error('nama_lengkap')<p class="form-error">{{ $message }}</p>@enderror</div>
                    <div><label class="form-label">Email (untuk login) *</label><input type="email" name="email" value="{{ old('email') }}" class="form-input" required>@error('email')<p class="form-error">{{ $message }}</p>@enderror</div>
                    <div><label class="form-label">Password *</label><input type="password" name="password" class="form-input" required minlength="8">@error('password')<p class="form-error">{{ $message }}</p>@enderror</div>
                    <div><label class="form-label">Jabatan</label><select name="jabatan_id" class="form-input"><option value="">-- Pilih Jabatan --</option>@foreach($jabatan as $j)<option value="{{ $j->id }}" {{ old('jabatan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama_jabatan }}</option>@endforeach</select></div>
                    <div><label class="form-label">Divisi</label><select name="divisi_id" class="form-input"><option value="">-- Pilih Divisi --</option>@foreach($divisi as $d)<option value="{{ $d->id }}" {{ old('divisi_id') == $d->id ? 'selected' : '' }}>{{ $d->nama_divisi }}</option>@endforeach</select></div>
                    <div><label class="form-label">Role *</label><select name="role" class="form-input" required><option value="karyawan" {{ old('role') === 'karyawan' ? 'selected' : '' }}>Karyawan</option><option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option></select></div>
                    <div><label class="form-label">Jenis Kelamin *</label><select name="jenis_kelamin" class="form-input" required><option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option><option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option></select></div>
                    <div><label class="form-label">Tanggal Lahir</label><input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="form-input"></div>
                    <div><label class="form-label">Tanggal Masuk *</label><input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', now()->format('Y-m-d')) }}" class="form-input" required></div>
                    <div><label class="form-label">No. Telepon</label><input type="text" name="no_telp" value="{{ old('no_telp') }}" class="form-input" placeholder="08xx"></div>
                    <div><label class="form-label">Saldo Cuti (hari)</label><input type="number" name="saldo_cuti" value="{{ old('saldo_cuti', 12) }}" min="0" class="form-input" required></div>
                </div>
                <div><label class="form-label">Alamat</label><textarea name="alamat" rows="2" class="form-input">{{ old('alamat') }}</textarea></div>
                <div>
                    <label class="form-label">Foto Profil</label>
                    <input type="file" name="foto" accept="image/*" class="form-input">
                    <p class="text-xs text-slate-500 mt-1">JPG/PNG, maks. 2MB. Bisa di-update nanti via halaman edit.</p>
                </div>
                <div><label class="form-label">Status</label><select name="status" class="form-input"><option value="aktif">Aktif</option><option value="nonaktif">Nonaktif</option></select></div>
                <div class="flex gap-3">
                    <button type="submit" class="btn-primary">Simpan Karyawan</button>
                    <a href="{{ route('admin-hrd.karyawan.index') }}" class="btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

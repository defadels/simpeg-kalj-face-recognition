<x-app-layout>
    <x-slot name="title">Edit Karyawan — {{ $karyawan->nama_lengkap }}</x-slot>

    <div class="max-w-3xl">
        <div class="card">
            <h3 class="font-semibold text-slate-800 mb-5">Edit Data Karyawan</h3>
            <form method="POST" action="{{ route('admin-hrd.karyawan.update', $karyawan) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="form-label">NIP *</label><input type="text" name="nip" value="{{ old('nip', $karyawan->nip) }}" class="form-input" required>@error('nip')<p class="form-error text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
                    <div><label class="form-label">Nama Lengkap *</label><input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $karyawan->nama_lengkap) }}" class="form-input" required>@error('nama_lengkap')<p class="form-error text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
                    <div><label class="form-label">Email *</label><input type="email" name="email" value="{{ old('email', $karyawan->user?->email) }}" class="form-input" required>@error('email')<p class="form-error text-xs text-red-600 mt-1">{{ $message }}</p>@enderror</div>
                    <div>
                        <label class="form-label">Role Akun *</label>
                        <select name="role" class="form-input" required>
                            <option value="karyawan" {{ old('role', $karyawan->user?->role) === 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                            <option value="manajer" {{ old('role', $karyawan->user?->role) === 'manajer' ? 'selected' : '' }}>Manajer</option>
                        </select>
                        @error('role')<p class="form-error text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Password</label>
                        <input type="password" name="password" placeholder="Isi untuk ganti password" class="form-input">
                        <span class="text-[10px] text-slate-400">Minimal 8 karakter. Kosongkan jika tidak diganti.</span>
                        @error('password')<p class="form-error text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div><label class="form-label">Jabatan</label><select name="jabatan_id" class="form-input"><option value="">-- Pilih Jabatan --</option>@foreach($jabatan as $j)<option value="{{ $j->id }}" {{ old('jabatan_id', $karyawan->jabatan_id) == $j->id ? 'selected' : '' }}>{{ $j->nama_jabatan }}</option>@endforeach</select></div>
                    <div><label class="form-label">Divisi</label><select name="divisi_id" class="form-input"><option value="">-- Pilih Divisi --</option>@foreach($divisi as $d)<option value="{{ $d->id }}" {{ old('divisi_id', $karyawan->divisi_id) == $d->id ? 'selected' : '' }}>{{ $d->nama_divisi }}</option>@endforeach</select></div>
                    <div><label class="form-label">Jenis Kelamin *</label><select name="jenis_kelamin" class="form-input" required><option value="L" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option><option value="P" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option></select></div>
                    <div><label class="form-label">Tanggal Lahir</label><input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $karyawan->tanggal_lahir?->format('Y-m-d')) }}" class="form-input"></div>
                    <div><label class="form-label">Tanggal Masuk *</label><input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', $karyawan->tanggal_masuk->format('Y-m-d')) }}" class="form-input" required></div>
                    <div><label class="form-label">No. Telepon</label><input type="text" name="no_telp" value="{{ old('no_telp', $karyawan->no_telp) }}" class="form-input"></div>
                    <div><label class="form-label">Saldo Cuti (hari)</label><input type="number" name="saldo_cuti" value="{{ old('saldo_cuti', $karyawan->saldo_cuti) }}" min="0" class="form-input" required></div>
                    <div><label class="form-label">Status</label><select name="status" class="form-input"><option value="aktif" {{ $karyawan->status === 'aktif' ? 'selected' : '' }}>Aktif</option><option value="nonaktif" {{ $karyawan->status === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option></select></div>
                </div>
                <div><label class="form-label">Alamat</label><textarea name="alamat" rows="2" class="form-input">{{ old('alamat', $karyawan->alamat) }}</textarea></div>
                <div>
                    <label class="form-label">Ganti Foto</label>
                    @if($karyawan->foto)
                        <div class="flex items-center gap-3 mb-2">
                            <img src="{{ asset('storage/' . $karyawan->foto) }}" class="w-12 h-12 rounded-xl object-cover" alt="">
                            <span class="text-xs text-slate-500">Foto saat ini</span>
                        </div>
                    @endif
                    <input type="file" name="foto" accept="image/*" class="form-input">
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="btn-primary">Perbarui</button>
                    <a href="{{ route('admin-hrd.karyawan.index') }}" class="btn-secondary">Batal</a>
                    <a href="{{ route('admin-hrd.karyawan.face-enrollment', $karyawan) }}" class="btn-secondary text-purple-600">Update Wajah</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

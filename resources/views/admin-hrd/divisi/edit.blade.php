<x-app-layout>
    <x-slot name="title">Edit Divisi</x-slot>
    <div class="max-w-xl"><div class="card"><h3 class="font-semibold text-slate-800 mb-5">Edit Divisi</h3>
    <form method="POST" action="{{ route('admin-hrd.divisi.update', $divisi) }}" class="space-y-4">@csrf @method('PUT')
        <div><label class="form-label">Nama Divisi *</label><input type="text" name="nama_divisi" value="{{ old('nama_divisi', $divisi->nama_divisi) }}" class="form-input" required></div>
        <div><label class="form-label">Manajer</label><select name="manajer_id" class="form-input"><option value="">-- Pilih Manajer --</option>@foreach($karyawan as $k)<option value="{{ $k->id }}" {{ old('manajer_id', $divisi->manajer_id) == $k->id ? 'selected' : '' }}>{{ $k->nama_lengkap }}</option>@endforeach</select></div>
        <div class="grid grid-cols-2 gap-3">
            <div><label class="form-label">Jam Datang (Masuk) *</label><input type="time" name="jam_masuk" value="{{ old('jam_masuk', $divisi->jam_masuk ? substr($divisi->jam_masuk, 0, 5) : '08:00') }}" class="form-input" required></div>
            <div><label class="form-label">Jam Pulang (Keluar) *</label><input type="time" name="jam_keluar" value="{{ old('jam_keluar', $divisi->jam_keluar ? substr($divisi->jam_keluar, 0, 5) : '17:00') }}" class="form-input" required></div>
        </div>
        <div><label class="form-label">Toleransi Keterlambatan (Menit) *</label><input type="number" name="toleransi_menit" value="{{ old('toleransi_menit', $divisi->toleransi_menit ?? 15) }}" min="0" max="120" class="form-input" required></div>
        <div><label class="form-label">Deskripsi</label><textarea name="deskripsi" rows="2" class="form-input">{{ old('deskripsi', $divisi->deskripsi) }}</textarea></div>
        <div class="flex gap-3"><button type="submit" class="btn-primary">Perbarui</button><a href="{{ route('admin-hrd.divisi.index') }}" class="btn-secondary">Batal</a></div>
    </form></div></div>
</x-app-layout>

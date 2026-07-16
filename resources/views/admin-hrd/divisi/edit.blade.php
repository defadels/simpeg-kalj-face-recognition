<x-app-layout>
    <x-slot name="title">Edit Divisi</x-slot>
    <div class="max-w-xl"><div class="card"><h3 class="font-semibold text-slate-800 mb-5">Edit Divisi</h3>
    <form method="POST" action="{{ route('admin-hrd.divisi.update', $divisi) }}" class="space-y-4">@csrf @method('PUT')
        <div><label class="form-label">Nama Divisi *</label><input type="text" name="nama_divisi" value="{{ old('nama_divisi', $divisi->nama_divisi) }}" class="form-input" required></div>
        <div><label class="form-label">Manajer</label><select name="manajer_id" class="form-input"><option value="">-- Pilih Manajer --</option>@foreach($karyawan as $k)<option value="{{ $k->id }}" {{ old('manajer_id', $divisi->manajer_id) == $k->id ? 'selected' : '' }}>{{ $k->nama_lengkap }}</option>@endforeach</select></div>
        <div><label class="form-label">Deskripsi</label><textarea name="deskripsi" rows="2" class="form-input">{{ old('deskripsi', $divisi->deskripsi) }}</textarea></div>
        <div class="flex gap-3"><button type="submit" class="btn-primary">Perbarui</button><a href="{{ route('admin-hrd.divisi.index') }}" class="btn-secondary">Batal</a></div>
    </form></div></div>
</x-app-layout>

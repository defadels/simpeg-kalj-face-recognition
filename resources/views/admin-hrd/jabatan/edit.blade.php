<x-app-layout>
    <x-slot name="title">Edit Jabatan</x-slot>
    <div class="max-w-xl"><div class="card"><h3 class="font-semibold text-slate-800 mb-5">Edit Jabatan</h3>
    <form method="POST" action="{{ route('admin-hrd.jabatan.update', $jabatan) }}" class="space-y-4">@csrf @method('PUT')
        <div><label class="form-label">Nama Jabatan *</label><input type="text" name="nama_jabatan" value="{{ old('nama_jabatan', $jabatan->nama_jabatan) }}" class="form-input" required>@error('nama_jabatan')<p class="form-error">{{ $message }}</p>@enderror</div>
        <div><label class="form-label">Deskripsi</label><textarea name="deskripsi" rows="3" class="form-input">{{ old('deskripsi', $jabatan->deskripsi) }}</textarea></div>
        <div class="flex gap-3"><button type="submit" class="btn-primary">Perbarui</button><a href="{{ route('admin-hrd.jabatan.index') }}" class="btn-secondary">Batal</a></div>
    </form></div></div>
</x-app-layout>

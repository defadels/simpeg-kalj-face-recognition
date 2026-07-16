<x-app-layout>
    <x-slot name="title">Data Karyawan</x-slot>

    <div class="card">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-semibold text-slate-800">Daftar Karyawan</h3>
                <p class="text-slate-500 text-sm">Total {{ $karyawan->total() }} karyawan</p>
            </div>
            <a href="{{ route('admin-hrd.karyawan.create') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Karyawan
            </a>
        </div>

        <form method="GET" class="flex flex-wrap gap-3 mb-5">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / NIP..." class="form-input max-w-xs">
            <select name="divisi_id" class="form-input max-w-xs">
                <option value="">Semua Divisi</option>
                @foreach($divisi as $div)
                    <option value="{{ $div->id }}" {{ request('divisi_id') == $div->id ? 'selected' : '' }}>{{ $div->nama_divisi }}</option>
                @endforeach
            </select>
            <select name="status" class="form-input w-36">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <button type="submit" class="btn-primary">Filter</button>
            @if(request()->hasAny(['search','divisi_id','status']))
                <a href="{{ route('admin-hrd.karyawan.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="text-left py-3 px-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Karyawan</th>
                        <th class="text-left py-3 px-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">NIP</th>
                        <th class="text-left py-3 px-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Jabatan / Divisi</th>
                        <th class="text-left py-3 px-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Face</th>
                        <th class="text-left py-3 px-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Status</th>
                        <th class="text-right py-3 px-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($karyawan as $kar)
                        <tr class="table-row">
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $kar->foto_url }}" class="w-9 h-9 rounded-xl object-cover flex-shrink-0" alt="">
                                    <div>
                                        <div class="font-medium text-slate-800">{{ $kar->nama_lengkap }}</div>
                                        <div class="text-xs text-slate-500">{{ $kar->user?->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-3 font-mono text-xs text-slate-600">{{ $kar->nip }}</td>
                            <td class="py-3 px-3">
                                <div class="text-sm text-slate-700">{{ $kar->jabatan?->nama_jabatan ?? '-' }}</div>
                                <div class="text-xs text-slate-500">{{ $kar->divisi?->nama_divisi ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-3">
                                @if($kar->face_data)
                                    <span class="badge badge-green">✓ Terdaftar</span>
                                @else
                                    <span class="badge badge-red">✗ Belum</span>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                <span class="badge {{ $kar->status === 'aktif' ? 'badge-green' : 'badge-red' }}">{{ ucfirst($kar->status) }}</span>
                            </td>
                            <td class="py-3 px-3 text-right">
                                <div class="flex items-center justify-end gap-2 flex-wrap">
                                    <a href="{{ route('admin-hrd.karyawan.face-enrollment', $kar) }}" class="text-purple-600 hover:text-purple-800 font-medium text-xs">Face Enroll</a>
                                    <a href="{{ route('admin-hrd.karyawan.show', $kar) }}" class="text-sky-600 hover:text-sky-800 font-medium text-xs">Detail</a>
                                    <a href="{{ route('admin-hrd.karyawan.edit', $kar) }}" class="text-amber-600 hover:text-amber-800 font-medium text-xs">Edit</a>
                                    <form method="POST" action="{{ route('admin-hrd.karyawan.destroy', $kar) }}" class="inline" onsubmit="return confirm('Hapus karyawan ini? Data user terkait juga akan dihapus.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 font-medium text-xs">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-12 text-center text-slate-400">Tidak ada karyawan ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $karyawan->links() }}</div>
    </div>
</x-app-layout>

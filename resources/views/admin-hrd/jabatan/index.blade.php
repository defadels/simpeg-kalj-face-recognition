<x-app-layout>
    <x-slot name="title">Jabatan</x-slot>
    <div class="card">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-semibold text-slate-800">Daftar Jabatan</h3>
            <a href="{{ route('admin-hrd.jabatan.create') }}" class="btn-primary"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Tambah Jabatan</a>
        </div>
        <form method="GET" class="flex gap-3 mb-5"><input type="text" name="search" value="{{ request('search') }}" placeholder="Cari jabatan..." class="form-input max-w-xs"><button type="submit" class="btn-primary">Filter</button></form>
        <table class="w-full text-sm">
            <thead><tr class="border-b border-slate-100"><th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Jabatan</th><th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Deskripsi</th><th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Karyawan</th><th class="text-right py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Aksi</th></tr></thead>
            <tbody>
                @forelse($jabatan as $j)
                <tr class="table-row">
                    <td class="py-3 px-3 font-medium text-slate-800">{{ $j->nama_jabatan }}</td>
                    <td class="py-3 px-3 text-slate-500 text-xs">{{ $j->deskripsi ?: '-' }}</td>
                    <td class="py-3 px-3"><span class="badge badge-blue">{{ $j->karyawan_count }} orang</span></td>
                    <td class="py-3 px-3 text-right">
                        <a href="{{ route('admin-hrd.jabatan.edit', $j) }}" class="text-sky-600 text-xs font-medium mr-3">Edit</a>
                        <form method="POST" action="{{ route('admin-hrd.jabatan.destroy', $j) }}" class="inline" onsubmit="return confirm('Hapus jabatan ini?')">@csrf @method('DELETE')<button type="submit" class="text-red-500 text-xs font-medium">Hapus</button></form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="py-8 text-center text-slate-400">Belum ada jabatan</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $jabatan->links() }}</div>
    </div>
</x-app-layout>

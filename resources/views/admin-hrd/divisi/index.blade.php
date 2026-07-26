<x-app-layout>
    <x-slot name="title">Divisi</x-slot>
    <div class="card">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-semibold text-slate-800">Daftar Divisi</h3>
            <a href="{{ route('admin-hrd.divisi.create') }}" class="btn-primary"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Tambah Divisi</a>
        </div>
        <table class="w-full text-sm">
            <thead><tr class="border-b border-slate-100"><th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Divisi</th><th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Manajer</th><th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Jam Kerja Divisi</th><th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Karyawan</th><th class="text-right py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Aksi</th></tr></thead>
            <tbody>
                @forelse($divisi as $d)
                <tr class="table-row">
                    <td class="py-3 px-3"><div class="font-medium text-slate-800">{{ $d->nama_divisi }}</div><div class="text-xs text-slate-500">{{ $d->deskripsi }}</div></td>
                    <td class="py-3 px-3">{{ $d->manajer?->nama_lengkap ?? '-' }}</td>
                    <td class="py-3 px-3">
                        <div class="font-mono text-xs text-slate-700 font-semibold">{{ $d->jam_kerja_formatted }}</div>
                        <div class="text-[11px] text-slate-400">Toleransi: {{ $d->toleransi_menit ?? 15 }} menit</div>
                    </td>
                    <td class="py-3 px-3"><span class="badge badge-blue">{{ $d->karyawan_count }} orang</span></td>
                    <td class="py-3 px-3 text-right">
                        <a href="{{ route('admin-hrd.divisi.edit', $d) }}" class="text-sky-600 text-xs font-medium mr-3">Edit</a>
                        <form method="POST" action="{{ route('admin-hrd.divisi.destroy', $d) }}" class="inline" onsubmit="return confirm('Hapus divisi ini?')">@csrf @method('DELETE')<button type="submit" class="text-red-500 text-xs font-medium">Hapus</button></form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-8 text-center text-slate-400">Belum ada divisi</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $divisi->links() }}</div>
    </div>
</x-app-layout>

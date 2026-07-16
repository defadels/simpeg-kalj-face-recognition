<x-app-layout>
    <x-slot name="title">Cuti & Izin — Persetujuan</x-slot>

    <div class="card">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-semibold text-slate-800">Pengajuan Cuti & Izin</h3>
        </div>

        <form method="GET" class="flex gap-3 mb-5">
            <select name="status" class="form-input w-40">
                <option value="" {{ !request('status') ? 'selected' : '' }}>Pending</option>
                <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                <option value="" {{ request('status') === '' && request()->has('status') ? 'selected' : '' }}>Semua</option>
            </select>
            <button type="submit" class="btn-primary">Filter</button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Karyawan</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Jenis</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Periode</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Hari</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Status</th>
                        <th class="text-right py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cutiIzin as $row)
                        @php $badge = $row->status_badge; @endphp
                        <tr class="table-row">
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $row->karyawan->foto_url }}" class="w-7 h-7 rounded-lg" alt="">
                                    <div>
                                        <div class="font-medium">{{ $row->karyawan->nama_lengkap }}</div>
                                        <div class="text-xs text-slate-500">{{ $row->karyawan->divisi?->nama_divisi }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-3"><span class="badge {{ $row->jenis === 'cuti' ? 'badge-purple' : 'badge-blue' }}">{{ ucfirst($row->jenis) }}</span></td>
                            <td class="py-3 px-3 text-xs text-slate-600">{{ $row->tanggal_mulai->format('d M') }} — {{ $row->tanggal_selesai->format('d M Y') }}</td>
                            <td class="py-3 px-3 text-center font-bold text-slate-700">{{ $row->jumlah_hari }}</td>
                            <td class="py-3 px-3"><span class="badge badge-{{ $badge['color'] }}">{{ $badge['label'] }}</span></td>
                            <td class="py-3 px-3 text-right">
                                <a href="{{ route('approval.cuti-izin.show', $row) }}" class="text-sky-600 text-xs font-medium">Detail & Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-10 text-center text-slate-400">Tidak ada pengajuan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $cutiIzin->links() }}</div>
    </div>
</x-app-layout>

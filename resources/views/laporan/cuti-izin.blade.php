<x-app-layout>
    <x-slot name="title">Laporan Cuti & Izin</x-slot>
    <x-slot name="breadcrumb">Rekap pengajuan cuti dan izin karyawan</x-slot>

    <div class="space-y-5">
        <!-- Filter -->
        <div class="card">
            <h3 class="font-semibold text-slate-800 mb-4">Filter Laporan</h3>
            <form method="GET" class="flex flex-wrap gap-3">
                <div>
                    <label class="form-label text-xs">Periode Awal</label>
                    <input type="date" name="periode_awal" value="{{ $periodeAwal }}" class="form-input">
                </div>
                <div>
                    <label class="form-label text-xs">Periode Akhir</label>
                    <input type="date" name="periode_akhir" value="{{ $periodeAkhir }}" class="form-input">
                </div>
                <div>
                    <label class="form-label text-xs">Divisi</label>
                    <select name="divisi_id" class="form-input max-w-xs">
                        <option value="">Semua Divisi</option>
                        @foreach($divisi as $d)
                            <option value="{{ $d->id }}" {{ request('divisi_id') == $d->id ? 'selected' : '' }}>{{ $d->nama_divisi }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label text-xs">Status</label>
                    <select name="status" class="form-input w-40">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="btn-primary">Filter</button>
                </div>
            </form>
        </div>

        <!-- Data Table -->
        <div class="card">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Karyawan</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Jenis</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Periode</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Hari</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Alasan</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Status</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Diproses Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cutiIzin as $row)
                            @php $badge = $row->status_badge; @endphp
                            <tr class="table-row">
                                <td class="py-3 px-3">
                                    <div class="font-medium text-slate-800">{{ $row->karyawan->nama_lengkap }}</div>
                                    <div class="text-xs text-slate-500">{{ $row->karyawan->nip }}</div>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="badge {{ $row->jenis === 'cuti' ? 'badge-purple' : 'badge-blue' }}">{{ ucfirst($row->jenis) }}</span>
                                </td>
                                <td class="py-3 px-3 text-xs text-slate-600">
                                    {{ $row->tanggal_mulai->format('d/m/Y') }} — {{ $row->tanggal_selesai->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-3 font-bold text-slate-700">{{ $row->jumlah_hari }} hari</td>
                                <td class="py-3 px-3 text-xs text-slate-500 max-w-xs truncate" title="{{ $row->alasan }}">{{ $row->alasan }}</td>
                                <td class="py-3 px-3">
                                    <span class="badge badge-{{ $badge['color'] }}">{{ $badge['label'] }}</span>
                                </td>
                                <td class="py-3 px-3 text-xs">
                                    @if($row->prosesor)
                                        <div class="text-slate-700 font-medium">{{ $row->prosesor->nama }}</div>
                                        <div class="text-slate-400">{{ $row->tanggal_proses?->format('d/m/Y') }}</div>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-8 text-center text-slate-400">Tidak ada data pengajuan cuti/izin ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $cutiIzin->links() }}</div>
        </div>
    </div>
</x-app-layout>

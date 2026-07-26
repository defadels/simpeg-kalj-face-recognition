<x-app-layout>
    <x-slot name="title">Monitor Absensi</x-slot>

    <div class="card">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-semibold text-slate-800">Monitor Kehadiran</h3>
        </div>
        <form method="GET" class="flex flex-wrap gap-3 mb-5">
            <input type="date" name="tanggal" value="{{ request('tanggal', today()->format('Y-m-d')) }}" class="form-input">
            <select name="divisi_id" class="form-input max-w-xs">
                <option value="">Semua Divisi</option>
                @foreach($divisi as $d)
                    <option value="{{ $d->id }}" {{ request('divisi_id') == $d->id ? 'selected' : '' }}>{{ $d->nama_divisi }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary">Filter</button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Karyawan</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Divisi & Jadwal</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Masuk</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Keluar</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Jam Kerja</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Status</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Lokasi</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Wajah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absensi as $row)
                        @php $badge = $row->status_badge; @endphp
                        <tr class="table-row">
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $row->karyawan->foto_url }}" class="w-7 h-7 rounded-lg object-cover" alt="">
                                    <span class="font-medium">{{ $row->karyawan->nama_lengkap }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-3 text-xs">
                                <div class="font-semibold text-slate-700">{{ $row->karyawan->divisi?->nama_divisi ?? '-' }}</div>
                                @if($row->karyawan->divisi)
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $row->karyawan->divisi->jam_kerja_formatted }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-3 font-mono text-xs">{{ $row->waktu_masuk ? substr($row->waktu_masuk, 0, 5) : '-' }}</td>
                            <td class="py-3 px-3 font-mono text-xs">{{ $row->waktu_keluar ? substr($row->waktu_keluar, 0, 5) : '-' }}</td>
                            <td class="py-3 px-3 text-xs">{{ $row->jam_kerja ? $row->jam_kerja . 'j' : '-' }}</td>
                            <td class="py-3 px-3"><span class="badge badge-{{ $badge['color'] }}">{{ $badge['label'] }}</span></td>
                            <td class="py-3 px-3">
                                @if($row->status_lokasi)<span class="badge {{ $row->status_lokasi === 'valid' ? 'badge-green' : 'badge-red' }}">{{ ucfirst($row->status_lokasi) }}</span>
                                @else<span class="text-slate-400 text-xs">-</span>@endif
                            </td>
                            <td class="py-3 px-3">
                                @if($row->status_face)<span class="badge {{ $row->status_face === 'berhasil' ? 'badge-green' : 'badge-red' }}">{{ ucfirst($row->status_face) }}</span>
                                @else<span class="text-slate-400 text-xs">-</span>@endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="py-10 text-center text-slate-400">Tidak ada data absensi untuk tanggal tersebut.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $absensi->links() }}</div>
    </div>
</x-app-layout>

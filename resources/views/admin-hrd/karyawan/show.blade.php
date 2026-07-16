<x-app-layout>
    <x-slot name="title">Detail Karyawan — {{ $karyawan->nama_lengkap }}</x-slot>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
        <div class="xl:col-span-1 space-y-5">
            <div class="card text-center">
                <img src="{{ $karyawan->foto_url }}" class="w-24 h-24 rounded-2xl object-cover mx-auto mb-3" alt="">
                <h3 class="font-bold text-slate-800 text-lg">{{ $karyawan->nama_lengkap }}</h3>
                <p class="text-slate-500 text-sm">{{ $karyawan->nip }}</p>
                <div class="flex justify-center gap-2 mt-2">
                    <span class="badge {{ $karyawan->status === 'aktif' ? 'badge-green' : 'badge-red' }}">{{ ucfirst($karyawan->status) }}</span>
                    @if($karyawan->face_data)<span class="badge badge-purple">✓ Face Enrolled</span>@endif
                </div>
                <div class="mt-4 flex gap-2 justify-center">
                    <a href="{{ route('admin-hrd.karyawan.edit', $karyawan) }}" class="btn-secondary text-sm">Edit</a>
                    <a href="{{ route('admin-hrd.karyawan.face-enrollment', $karyawan) }}" class="btn-primary text-sm">Face Enroll</a>
                </div>
            </div>

            <div class="card">
                <h4 class="font-semibold text-slate-700 mb-3">Informasi</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-slate-500">Jabatan</span><span class="font-medium">{{ $karyawan->jabatan?->nama_jabatan ?? '-' }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Divisi</span><span class="font-medium">{{ $karyawan->divisi?->nama_divisi ?? '-' }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Gender</span><span class="font-medium">{{ $karyawan->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Tgl. Masuk</span><span class="font-medium">{{ $karyawan->tanggal_masuk->format('d M Y') }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Saldo Cuti</span><span class="font-bold text-emerald-600">{{ $karyawan->saldo_cuti }} hari</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">No. Telp</span><span class="font-medium">{{ $karyawan->no_telp ?? '-' }}</span></div>
                </div>
            </div>
        </div>

        <div class="xl:col-span-2 card">
            <h4 class="font-semibold text-slate-700 mb-4">Riwayat Absensi Terakhir</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="text-left py-2 px-3 text-xs font-semibold text-slate-500 uppercase">Tanggal</th>
                            <th class="text-left py-2 px-3 text-xs font-semibold text-slate-500 uppercase">Masuk</th>
                            <th class="text-left py-2 px-3 text-xs font-semibold text-slate-500 uppercase">Keluar</th>
                            <th class="text-left py-2 px-3 text-xs font-semibold text-slate-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absensiTerakhir as $row)
                            @php $badge = $row->status_badge; @endphp
                            <tr class="table-row">
                                <td class="py-2 px-3">{{ $row->tanggal->isoFormat('ddd, D MMM Y') }}</td>
                                <td class="py-2 px-3 font-mono">{{ $row->waktu_masuk ? substr($row->waktu_masuk, 0, 5) : '-' }}</td>
                                <td class="py-2 px-3 font-mono">{{ $row->waktu_keluar ? substr($row->waktu_keluar, 0, 5) : '-' }}</td>
                                <td class="py-2 px-3"><span class="badge badge-{{ $badge['color'] }}">{{ $badge['label'] }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-6 text-center text-slate-400">Belum ada riwayat</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', sans-serif; font-size: 9pt; color: #1e293b; }
        .header { background: #0c1a2e; color: white; padding: 20px; margin-bottom: 16px; }
        .header h1 { font-size: 16pt; font-weight: bold; }
        .header p { font-size: 9pt; opacity: 0.8; margin-top: 4px; }
        .info-box { display: flex; gap: 20px; margin-bottom: 16px; padding: 12px; background: #f8fafc; border-radius: 6px; }
        .info-item label { font-size: 8pt; color: #64748b; display: block; }
        .info-item span { font-weight: bold; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #0ea5e9; color: white; }
        thead th { padding: 7px 8px; text-align: left; font-size: 8pt; font-weight: 600; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { padding: 6px 8px; font-size: 8pt; border-bottom: 1px solid #f1f5f9; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 7pt; font-weight: 600; }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-yellow { background: #fef9c3; color: #854d0e; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-purple { background: #f3e8ff; color: #6b21a8; }
        .footer { margin-top: 20px; font-size: 8pt; color: #64748b; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        .summary { display: flex; gap: 10px; margin-bottom: 16px; }
        .summary-item { flex: 1; text-align: center; padding: 8px; border-radius: 4px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Kehadiran Karyawan</h1>
        <p>PT. Karya Agung Lestari Jaya — Sistem Informasi Kepegawaian (SIPEG)</p>
    </div>

    <div class="info-box">
        <div class="info-item"><label>Periode</label><span>{{ \Carbon\Carbon::parse($periodeAwal)->isoFormat('D MMMM Y') }} — {{ \Carbon\Carbon::parse($periodeAkhir)->isoFormat('D MMMM Y') }}</span></div>
        <div class="info-item"><label>Divisi</label><span>{{ $divisiNama }}</span></div>
        <div class="info-item"><label>Total Record</label><span>{{ $absensi->count() }}</span></div>
        <div class="info-item"><label>Dicetak</label><span>{{ now()->isoFormat('D MMM Y HH:mm') }}</span></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIP</th>
                <th>Nama Karyawan</th>
                <th>Divisi</th>
                <th>Jadwal Divisi</th>
                <th>Tanggal</th>
                <th>Masuk</th>
                <th>Keluar</th>
                <th>Jam Kerja</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($absensi as $i => $row)
                @php
                    $badge = match($row->status_kehadiran) {
                        'hadir' => 'badge-green',
                        'terlambat' => 'badge-yellow',
                        'alpha' => 'badge-red',
                        'cuti' => 'badge-purple',
                        'izin' => 'badge-blue',
                        default => '',
                    };
                    $label = match($row->status_kehadiran) {
                        'hadir' => 'Hadir',
                        'terlambat' => 'Terlambat',
                        'alpha' => 'Alpha',
                        'cuti' => 'Cuti',
                        'izin' => 'Izin',
                        default => '-',
                    };
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row->karyawan->nip }}</td>
                    <td><strong>{{ $row->karyawan->nama_lengkap }}</strong></td>
                    <td>{{ $row->karyawan->divisi?->nama_divisi ?? '-' }}</td>
                    <td>{{ $row->karyawan->divisi?->jam_kerja_formatted ?? '-' }}</td>
                    <td>{{ $row->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $row->waktu_masuk ? substr($row->waktu_masuk, 0, 5) : '-' }}</td>
                    <td>{{ $row->waktu_keluar ? substr($row->waktu_keluar, 0, 5) : '-' }}</td>
                    <td>{{ $row->jam_kerja ? $row->jam_kerja . ' j' : '-' }}</td>
                    <td><span class="badge {{ $badge }}">{{ $label }}</span></td>
                </tr>
            @empty
                <tr><td colspan="10" style="text-align:center; padding: 20px; color: #94a3b8;">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh SIPEG KALJ — PT. Karya Agung Lestari Jaya | {{ now()->isoFormat('D MMMM Y, HH:mm') }}
    </div>
</body>
</html>

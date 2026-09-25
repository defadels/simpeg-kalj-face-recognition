<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Transaksi Karyawan</title>
    <style>
        @page { margin: 13px 16px; }
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; color: #111; font-size: 7px; }
        .report-header { width: 100%; border-bottom: 2px solid #111; margin: 0 0 10px; padding: 4px 8px 10px; }
        .report-header td { vertical-align: bottom; }
        .report-title { font-size: 17px; font-weight: bold; }
        .report-period { font-size: 11px; text-align: right; white-space: nowrap; }
        .attendance { width: 100%; border-collapse: collapse; }
        .attendance th, .attendance td { border: 1px solid #111; }
        .attendance thead th { height: 27px; padding: 3px 1px; text-align: center; font-size: 8px; font-weight: bold; white-space: nowrap; }
        .attendance .employee { padding: 4px; text-align: left; font-size: 10px; font-weight: bold; overflow-wrap: break-word; }
        .attendance .day { text-align: center; padding: 2px 0; line-height: 1.1; font-size: 7px; font-weight: bold; vertical-align: middle; }
        .attendance .section td { padding: 4px; background: #909090; font-size: 10px; font-weight: normal; text-transform: uppercase; }
        .attendance .time-in, .attendance .time-out { display: block; white-space: nowrap; }
        .attendance .empty-record { font-size: 10px; font-weight: bold; }
        .page-break { page-break-after: always; }
        .no-data { padding: 12px; text-align: center; font-size: 9px; }
    </style>
</head>
<body>
    @forelse($karyawanPerDivisi as $namaDivisi => $karyawanDivisi)
        @php $divisiTerakhir = $loop->last; @endphp
        @foreach($tanggalPerHalaman as $tanggalHalaman)
            @php
                $halamanTerakhir = $divisiTerakhir && $loop->last;
                $lebarNama = 145;
                $lebarTanggal = max(18, floor((770 - $lebarNama) / max($tanggalHalaman->count(), 1)));
            @endphp
            <table class="report-header">
                <tr>
                    <td class="report-title">Data Transaksi Karyawan</td>
                    <td class="report-period">
                        Dari {{ \Carbon\Carbon::parse($periodeAwal)->format('d-m-Y') }} s/d {{ \Carbon\Carbon::parse($periodeAkhir)->format('d-m-Y') }}
                    </td>
                </tr>
            </table>

            <table class="attendance">
                <thead>
                    <tr>
                        <th class="employee" style="width: {{ $lebarNama }}px; text-align:center">Nama</th>
                        @foreach($tanggalHalaman as $tanggal)
                            <th class="day" style="width: {{ $lebarTanggal }}px">{{ $tanggal->format('d/m') }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr class="section"><td colspan="{{ $tanggalHalaman->count() + 1 }}">{{ $namaDivisi }}</td></tr>
                    @forelse($karyawanDivisi as $karyawan)
                        @php
                            $absensiPerTanggal = $karyawan->absensi->keyBy(
                                fn ($absensi) => $absensi->tanggal->format('Y-m-d')
                            );
                            $nipRingkas = preg_match('/(\d+)$/', $karyawan->nip, $cocok)
                                ? (int) $cocok[1]
                                : $karyawan->nip;
                        @endphp
                        <tr>
                            <td class="employee">{{ strtoupper($karyawan->nama_lengkap) }} ({{ $nipRingkas }})</td>
                            @foreach($tanggalHalaman as $tanggal)
                                @php $absensi = $absensiPerTanggal->get($tanggal->format('Y-m-d')); @endphp
                                <td class="day">
                                    @if($absensi)
                                        @if($absensi->waktu_masuk)
                                            <span class="time-in">{{ substr($absensi->waktu_masuk, 0, 5) }}</span>
                                            @if($absensi->waktu_keluar)
                                                <span class="time-out">{{ substr($absensi->waktu_keluar, 0, 5) }}</span>
                                            @endif
                                        @else
                                            <span class="empty-record">-</span>
                                        @endif
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="{{ $tanggalHalaman->count() + 1 }}" class="no-data">Tidak ada karyawan aktif pada divisi ini.</td></tr>
                    @endforelse
                </tbody>
            </table>

            @if(!$halamanTerakhir)
                <div class="page-break"></div>
            @endif
        @endforeach
    @empty
        <table class="report-header"><tr><td class="report-title">Data Transaksi Karyawan</td><td class="report-period">Tidak ada data karyawan aktif</td></tr></table>
    @endforelse
</body>
</html>

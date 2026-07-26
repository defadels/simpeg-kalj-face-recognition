<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AbsensiExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(
        private string $periodeAwal,
        private string $periodeAkhir,
        private ?int $divisiId = null
    ) {}

    public function query()
    {
        $query = Absensi::with(['karyawan.jabatan', 'karyawan.divisi'])
            ->whereBetween('tanggal', [$this->periodeAwal, $this->periodeAkhir]);

        if ($this->divisiId) {
            $query->whereHas('karyawan', fn($q) => $q->where('divisi_id', $this->divisiId));
        }

        return $query->orderBy('tanggal')->orderBy('karyawan_id');
    }

    public function headings(): array
    {
        return [
            'No',
            'ID Karyawan',
            'Nama Karyawan',
            'Divisi',
            'Jadwal Divisi',
            'Jabatan',
            'Tanggal',
            'Waktu Masuk',
            'Waktu Keluar',
            'Jam Kerja',
            'Status Kehadiran',
            'Status Lokasi',
            'Status Face',
            'Keterangan',
        ];
    }

    private int $no = 0;

    public function map($absensi): array
    {
        $this->no++;
        return [
            $this->no,
            $absensi->karyawan->nip,
            $absensi->karyawan->nama_lengkap,
            $absensi->karyawan->divisi?->nama_divisi ?? '-',
            $absensi->karyawan->divisi?->jam_kerja_formatted ?? '-',
            $absensi->karyawan->jabatan?->nama_jabatan ?? '-',
            $absensi->tanggal->format('d/m/Y'),
            $absensi->waktu_masuk ?? '-',
            $absensi->waktu_keluar ?? '-',
            $absensi->jam_kerja ? $absensi->jam_kerja . ' jam' : '-',
            ucfirst($absensi->status_kehadiran),
            ucfirst($absensi->status_lokasi ?? '-'),
            ucfirst($absensi->status_face ?? '-'),
            $absensi->keterangan ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF0EA5E9']],
            ],
        ];
    }

    public function title(): string
    {
        return 'Laporan Kehadiran';
    }
}

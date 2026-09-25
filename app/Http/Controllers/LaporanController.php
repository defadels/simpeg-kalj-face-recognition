<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\CutiIzin;
use App\Models\Divisi;
use App\Models\Karyawan;
use App\Models\Laporan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AbsensiExport;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $divisi = Divisi::all();
        $divisiId = $request->divisi_id;

        $periodeAwal = $request->periode_awal ?? now()->startOfMonth()->format('Y-m-d');
        $periodeAkhir = $request->periode_akhir ?? now()->endOfMonth()->format('Y-m-d');

        $query = Absensi::with(['karyawan.jabatan', 'karyawan.divisi'])
            ->whereBetween('tanggal', [$periodeAwal, $periodeAkhir]);

        if ($divisiId) {
            $query->whereHas('karyawan', fn($q) => $q->where('divisi_id', $divisiId));
        }

        $absensi = $query->orderBy('tanggal', 'desc')->orderBy('karyawan_id')->get();

        // Summary statistics
        $summary = [
            'total_hadir' => $absensi->whereIn('status_kehadiran', ['hadir'])->count(),
            'total_terlambat' => $absensi->where('status_kehadiran', 'terlambat')->count(),
            'total_alpha' => $absensi->where('status_kehadiran', 'alpha')->count(),
            'total_cuti' => $absensi->where('status_kehadiran', 'cuti')->count(),
            'total_izin' => $absensi->where('status_kehadiran', 'izin')->count(),
            'total_sakit' => $absensi->where('status_kehadiran', 'sakit')->count(),
        ];

        return view('laporan.index', compact('absensi', 'divisi', 'summary', 'periodeAwal', 'periodeAkhir', 'divisiId'));
    }

    public function exportPdf(Request $request)
    {
        $divisiId = $request->divisi_id;

        $periodeAwal = $request->periode_awal ?? now()->startOfMonth()->format('Y-m-d');
        $periodeAkhir = $request->periode_akhir ?? now()->endOfMonth()->format('Y-m-d');

        // Bentuk matriks: satu baris untuk satu karyawan dan satu kolom untuk
        // satu tanggal. Karyawan tanpa record tetap dicetak agar format
        // laporan sesuai daftar absensi harian.
        $tanggal = collect(CarbonPeriod::create($periodeAwal, $periodeAkhir))
            ->map(fn ($date) => $date->copy());

        $karyawan = Karyawan::with([
            'divisi',
            'absensi' => fn ($query) => $query
                ->whereBetween('tanggal', [$periodeAwal, $periodeAkhir])
                ->orderBy('tanggal'),
        ])
            ->where('status', 'aktif')
            ->when($divisiId, fn ($query) => $query->where('divisi_id', $divisiId))
            ->orderBy('divisi_id')
            ->orderBy('nama_lengkap')
            ->get();

        $karyawanPerDivisi = $karyawan->groupBy(
            fn (Karyawan $karyawan) => $karyawan->divisi?->nama_divisi ?? 'Tanpa Divisi'
        );
        $tanggalPerHalaman = $tanggal->chunk(31);
        $divisiNama = $divisiId ? Divisi::find($divisiId)?->nama_divisi : 'Semua Divisi';

        // Simpan record laporan
        Laporan::create([
            'jenis_laporan' => 'kehadiran',
            'periode_awal' => $periodeAwal,
            'periode_akhir' => $periodeAkhir,
            'divisi_id' => $divisiId,
            'dibuat_oleh' => auth()->id(),
        ]);

        $pdf = Pdf::loadView('laporan.pdf.absensi', compact(
            'karyawanPerDivisi',
            'tanggalPerHalaman',
            'periodeAwal',
            'periodeAkhir',
            'divisiNama'
        ))
            ->setPaper('a4', 'landscape');

        return $pdf->download("laporan-absensi-{$periodeAwal}-{$periodeAkhir}.pdf");
    }

    public function exportExcel(Request $request)
    {
        $divisiId = $request->divisi_id;

        $periodeAwal = $request->periode_awal ?? now()->startOfMonth()->format('Y-m-d');
        $periodeAkhir = $request->periode_akhir ?? now()->endOfMonth()->format('Y-m-d');

        Laporan::create([
            'jenis_laporan' => 'kehadiran',
            'periode_awal' => $periodeAwal,
            'periode_akhir' => $periodeAkhir,
            'divisi_id' => $divisiId,
            'dibuat_oleh' => auth()->id(),
        ]);

        return Excel::download(
            new AbsensiExport($periodeAwal, $periodeAkhir, $divisiId),
            "laporan-absensi-{$periodeAwal}-{$periodeAkhir}.xlsx"
        );
    }

    public function cutiIzinReport(Request $request)
    {
        $divisi = Divisi::all();
        $periodeAwal = $request->periode_awal ?? now()->startOfMonth()->format('Y-m-d');
        $periodeAkhir = $request->periode_akhir ?? now()->endOfMonth()->format('Y-m-d');

        $query = CutiIzin::with(['karyawan.jabatan', 'karyawan.divisi'])
            ->whereBetween('tanggal_mulai', [$periodeAwal, $periodeAkhir]);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->divisi_id) {
            $query->whereHas('karyawan', fn($q) => $q->where('divisi_id', $request->divisi_id));
        }

        $cutiIzin = $query->latest()->paginate(20)->withQueryString();
        return view('laporan.cuti-izin', compact('cutiIzin', 'divisi', 'periodeAwal', 'periodeAkhir'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\CutiIzin;
use Illuminate\Http\Request;

class KaryawanDashboardController extends Controller
{
    public function dashboard()
    {
        $karyawan = auth()->user()->karyawan;
        if (!$karyawan) {
            abort(403, 'Data karyawan tidak ditemukan.');
        }

        $absensiHariIni = Absensi::where('karyawan_id', $karyawan->id)
            ->where('tanggal', today())
            ->first();

        $absensiTerakhir = Absensi::where('karyawan_id', $karyawan->id)
            ->orderBy('tanggal', 'desc')
            ->take(7)
            ->get();

        $cutiPending = CutiIzin::where('karyawan_id', $karyawan->id)
            ->where('status', 'pending')
            ->count();

        // Statistik bulan ini
        $bulanIni = Absensi::where('karyawan_id', $karyawan->id)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->get();

        $statsbulan = [
            'hadir' => $bulanIni->whereIn('status_kehadiran', ['hadir'])->count(),
            'terlambat' => $bulanIni->where('status_kehadiran', 'terlambat')->count(),
            'alpha' => $bulanIni->where('status_kehadiran', 'alpha')->count(),
        ];

        return view('karyawan.dashboard', compact(
            'karyawan', 'absensiHariIni', 'absensiTerakhir',
            'cutiPending', 'statsbulan'
        ));
    }
}

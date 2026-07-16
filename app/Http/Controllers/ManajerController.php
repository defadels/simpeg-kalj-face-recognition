<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\CutiIzin;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class ManajerController extends Controller
{
    public function dashboard()
    {
        $manajer = auth()->user()->karyawan;
        $divisi = $manajer?->managedDivisi;
        $divisiId = $divisi?->id;

        $stats = [
            'total_tim' => $divisiId ? Karyawan::where('divisi_id', $divisiId)->where('status', 'aktif')->count() : 0,
            'hadir_hari_ini' => $divisiId ? Absensi::where('tanggal', today())
                ->whereHas('karyawan', fn($q) => $q->where('divisi_id', $divisiId))
                ->whereIn('status_kehadiran', ['hadir', 'terlambat'])->count() : 0,
            'cuti_pending' => CutiIzin::where('status', 'pending')
                ->when($divisiId, fn($q) => $q->whereHas('karyawan', fn($qq) => $qq->where('divisi_id', $divisiId)))
                ->count(),
        ];

        $absensiTim = Absensi::with(['karyawan'])
            ->where('tanggal', today())
            ->when($divisiId, fn($q) => $q->whereHas('karyawan', fn($qq) => $qq->where('divisi_id', $divisiId)))
            ->get();

        return view('manajer.dashboard', compact('stats', 'divisi', 'absensiTim'));
    }
}

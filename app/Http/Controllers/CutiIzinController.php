<?php

namespace App\Http\Controllers;

use App\Models\CutiIzin;
use App\Models\Karyawan;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CutiIzinController extends Controller
{
    /**
     * Halaman pengajuan cuti/izin (karyawan)
     */
    public function index(Request $request)
    {
        $karyawan = auth()->user()->karyawan;
        $query = CutiIzin::where('karyawan_id', $karyawan->id)->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $cutiIzin = $query->paginate(10)->withQueryString();
        return view('karyawan.cuti-izin.index', compact('cutiIzin', 'karyawan'));
    }

    public function create()
    {
        $karyawan = auth()->user()->karyawan;
        return view('karyawan.cuti-izin.create', compact('karyawan'));
    }

    public function store(Request $request)
    {
        // Untuk jenis 'sakit', izinkan tanggal mulai di masa lampau (retroaktif)
        $isSakit = $request->input('jenis') === 'sakit';
        $validated = $request->validate([
            'jenis' => 'required|in:cuti,izin,sakit',
            'tanggal_mulai' => $isSakit
                ? 'required|date'
                : 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|min:10',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $karyawan = auth()->user()->karyawan;

        // Hitung jumlah hari kerja
        $jumlahHari = $this->hitungHariKerja($validated['tanggal_mulai'], $validated['tanggal_selesai']);

        // Cek saldo cuti (hanya untuk jenis 'cuti')
        if ($validated['jenis'] === 'cuti' && $karyawan->saldo_cuti < $jumlahHari) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Saldo cuti tidak mencukupi. Saldo tersisa: {$karyawan->saldo_cuti} hari, dibutuhkan: {$jumlahHari} hari.");
        }

        // Upload lampiran jika ada
        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('cuti-izin/lampiran', 'public');
        }

        CutiIzin::create([
            'karyawan_id' => $karyawan->id,
            'jenis' => $validated['jenis'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'jumlah_hari' => $jumlahHari,
            'alasan' => $validated['alasan'],
            'lampiran' => $lampiranPath,
            'status' => 'pending',
        ]);

        return redirect()->route('karyawan.cuti-izin.index')
            ->with('success', 'Pengajuan cuti/izin berhasil dikirim dan menunggu persetujuan.');
    }

    /**
     * List approval (manajer & admin_hrd)
     */
    public function indexApproval(Request $request)
    {
        $query = CutiIzin::with(['karyawan.divisi', 'karyawan.jabatan']);

        if ($request->divisi_id) {
            $query->whereHas('karyawan', fn($q) => $q->where('divisi_id', $request->divisi_id));
        }

        if ($request->has('status') && $request->status !== '' && $request->status !== 'semua') {
            $query->where('status', $request->status);
        } elseif (!$request->has('status')) {
            $query->where('status', 'pending');
        }

        $cutiIzin = $query->latest()->paginate(15)->withQueryString();
        $divisiList = \App\Models\Divisi::all();

        return view('approval.cuti-izin.index', compact('cutiIzin', 'divisiList'));
    }

    public function showApproval(CutiIzin $cutiIzin)
    {
        $cutiIzin->load(['karyawan.jabatan', 'karyawan.divisi', 'prosesor']);
        return view('approval.cuti-izin.show', compact('cutiIzin'));
    }

    public function approve(Request $request, CutiIzin $cutiIzin)
    {
        $request->validate(['catatan_prosesor' => 'nullable|string']);

        if ($cutiIzin->status !== 'pending') {
            return redirect()->back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        $cutiIzin->update([
            'status' => 'disetujui',
            'diproses_oleh' => auth()->id(),
            'catatan_prosesor' => $request->catatan_prosesor,
            'tanggal_proses' => now(),
        ]);

        // Potong saldo cuti jika jenis 'cuti'
        if ($cutiIzin->jenis === 'cuti') {
            $cutiIzin->karyawan->decrement('saldo_cuti', $cutiIzin->jumlah_hari);
        }

        // Update status absensi menjadi 'cuti' atau 'izin' untuk hari-hari yang dicakup
        $this->updateAbsensiStatus($cutiIzin);

        $redirectRoute = auth()->user()->isAdmin() ? 'admin.cuti-izin.index' : 'approval.cuti-izin.index';

        return redirect()->route($redirectRoute)
            ->with('success', 'Pengajuan disetujui.');
    }

    public function reject(Request $request, CutiIzin $cutiIzin)
    {
        $request->validate(['catatan_prosesor' => 'required|string']);

        if ($cutiIzin->status !== 'pending') {
            return redirect()->back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        $cutiIzin->update([
            'status' => 'ditolak',
            'diproses_oleh' => auth()->id(),
            'catatan_prosesor' => $request->catatan_prosesor,
            'tanggal_proses' => now(),
        ]);

        $redirectRoute = auth()->user()->isAdmin() ? 'admin.cuti-izin.index' : 'approval.cuti-izin.index';

        return redirect()->route($redirectRoute)
            ->with('success', 'Pengajuan ditolak.');
    }

    private function hitungHariKerja(string $mulai, string $selesai): int
    {
        $start = \Carbon\Carbon::parse($mulai);
        $end = \Carbon\Carbon::parse($selesai);
        $hari = 0;
        $current = $start->copy();
        while ($current->lte($end)) {
            if (!$current->isWeekend()) {
                $hari++;
            }
            $current->addDay();
        }
        return $hari;
    }

    private function updateAbsensiStatus(CutiIzin $cutiIzin): void
    {
        $start = $cutiIzin->tanggal_mulai->copy();
        $end = $cutiIzin->tanggal_selesai->copy();
        // Mapping: cuti -> cuti, izin -> izin, sakit -> sakit
        $statusAbsensi = $cutiIzin->jenis;
        $keterangan = match($cutiIzin->jenis) {
            'cuti' => 'Cuti disetujui',
            'izin' => 'Izin disetujui',
            'sakit' => 'Sakit disetujui',
            default => ucfirst($cutiIzin->jenis) . ' disetujui',
        };

        $current = $start;
        while ($current->lte($end)) {
            if (!$current->isWeekend()) {
                Absensi::updateOrCreate(
                    ['karyawan_id' => $cutiIzin->karyawan_id, 'tanggal' => $current->format('Y-m-d')],
                    ['status_kehadiran' => $statusAbsensi, 'keterangan' => $keterangan]
                );
            }
            $current->addDay();
        }
    }
}

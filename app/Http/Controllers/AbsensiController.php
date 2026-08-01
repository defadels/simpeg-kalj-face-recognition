<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\KonfigurasiSistem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AbsensiController extends Controller
{
    /**
     * Tampilkan halaman absensi karyawan
     */
    public function index()
    {
        $karyawan = auth()->user()->karyawan;
        if (!$karyawan) {
            abort(403, 'Data karyawan tidak ditemukan.');
        }

        $absensiHariIni = Absensi::where('karyawan_id', $karyawan->id)
            ->where('tanggal', today())
            ->first();

        $konfigurasi = KonfigurasiSistem::getActive();
        $sudahMasuk = $absensiHariIni && $absensiHariIni->waktu_masuk;
        $sudahKeluar = $absensiHariIni && $absensiHariIni->waktu_keluar;

        return view('karyawan.absensi.index', compact(
            'karyawan', 'absensiHariIni', 'konfigurasi', 'sudahMasuk', 'sudahKeluar'
        ));
    }

    /**
     * STEP 1: Validasi lokasi via Haversine (AJAX)
     * Frontend kirim lat/lng → backend hitung jarak → return valid/invalid
     */
    public function checkLokasi(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $konfigurasi = KonfigurasiSistem::getActive();

        if (!$konfigurasi || !$konfigurasi->isKonfigured()) {
            return response()->json([
                'valid' => false,
                'message' => 'Konfigurasi lokasi kantor belum diatur. Hubungi administrator.',
                'jarak' => null,
            ], 422);
        }

        $jarak = $this->haversine(
            $request->latitude,
            $request->longitude,
            $konfigurasi->lat_kantor,
            $konfigurasi->lng_kantor
        );

        $valid = $jarak <= $konfigurasi->radius_meter;

        return response()->json([
            'valid' => $valid,
            'jarak' => round($jarak, 1),
            'radius' => $konfigurasi->radius_meter,
            'message' => $valid
                ? "Lokasi valid. Jarak: {$jarak}m dari kantor."
                : "Lokasi di luar radius. Jarak: {$jarak}m, radius: {$konfigurasi->radius_meter}m.",
            'lat_kantor' => $konfigurasi->lat_kantor,
            'lng_kantor' => $konfigurasi->lng_kantor,
        ]);
    }

    /**
     * STEP 2: Proses absensi masuk/keluar dengan face descriptor (AJAX)
     * Frontend kirim descriptor 128-d & snapshot foto → backend hitung Euclidean distance & simpan foto
     */
    public function prosesAbsensi(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'face_descriptor' => 'required|array|size:128',
            'face_descriptor.*' => 'required|numeric',
            'jenis' => 'required|in:masuk,keluar',
            'foto_absensi' => 'nullable|string',
        ]);

        $karyawan = auth()->user()->karyawan;
        if (!$karyawan) {
            return response()->json(['success' => false, 'message' => 'Data karyawan tidak ditemukan.'], 403);
        }

        // Validasi ulang lokasi di backend
        $konfigurasi = KonfigurasiSistem::getActive();
        if (!$konfigurasi || !$konfigurasi->isKonfigured()) {
            return response()->json(['success' => false, 'message' => 'Konfigurasi sistem belum lengkap.'], 422);
        }

        $jarak = $this->haversine(
            $request->latitude,
            $request->longitude,
            $konfigurasi->lat_kantor,
            $konfigurasi->lng_kantor
        );

        if ($jarak > $konfigurasi->radius_meter) {
            return response()->json([
                'success' => false,
                'message' => "Lokasi di luar radius kantor. Jarak: {$jarak}m.",
                'status_lokasi' => 'invalid',
            ], 422);
        }

        // Face matching
        $faceDescriptor = $karyawan->getFaceDescriptorArray();
        if (!$faceDescriptor) {
            return response()->json([
                'success' => false,
                'message' => 'Wajah Anda belum terdaftar. Hubungi admin HRD untuk face enrollment.',
            ], 422);
        }

        $distance = $this->euclideanDistance($request->face_descriptor, $faceDescriptor);
        $threshold = 0.60; // Toleransi jarak Euclidean 0.60 (Menerima kemiripan wajah >= 40%)

        if ($distance > $threshold) {
            return response()->json([
                'success' => false,
                'message' => "Verifikasi wajah gagal. Kemiripan wajah (" . round(max(0, (1 - $distance)) * 100, 1) . "%) kurang dari batas minimum 40%. Pastikan Anda sendiri yang melakukan absensi.",
                'distance' => round($distance, 4),
                'threshold' => $threshold,
                'status_face' => 'gagal',
            ], 422);
        }

        // Simpan snapshot foto absensi jika ada
        $fotoPath = null;
        if ($request->foto_absensi && preg_match('/^data:image\/(\w+);base64,/', $request->foto_absensi, $type)) {
            $imageData = substr($request->foto_absensi, strpos($request->foto_absensi, ',') + 1);
            $imageData = base64_decode($imageData);
            
            if ($imageData !== false) {
                $ext = strtolower($type[1]) ?: 'jpg';
                $dir = 'absensi';
                if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($dir)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory($dir);
                }
                $filename = "{$dir}/foto_{$request->jenis}_{$karyawan->id}_" . time() . ".{$ext}";
                \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $imageData);
                $fotoPath = $filename;
            }
        }

        // Simpan absensi
        $absensi = Absensi::firstOrNew([
            'karyawan_id' => $karyawan->id,
            'tanggal' => today(),
        ]);

        $sekarang = now()->format('H:i:s');

        if ($request->jenis === 'masuk') {
            if ($absensi->waktu_masuk) {
                return response()->json(['success' => false, 'message' => 'Anda sudah absen masuk hari ini.'], 422);
            }

            $absensi->waktu_masuk = $sekarang;
            $absensi->lat_masuk = $request->latitude;
            $absensi->lng_masuk = $request->longitude;
            $absensi->status_lokasi = 'valid';
            $absensi->status_face = 'berhasil';
            $absensi->face_distance_masuk = round($distance, 4);
            $absensi->ip_masuk = $request->ip();
            $absensi->user_agent_masuk = $request->userAgent();
            if ($fotoPath) {
                $absensi->foto_masuk = $fotoPath;
            }

            // Tentukan status kehadiran berdasarkan jam masuk divisi karyawan (atau default konfigurasi sistem)
            $divisi = $karyawan->divisi;
            $targetJamMasukStr = $divisi?->jam_masuk ?: $konfigurasi->jam_masuk;
            $toleransiMenit = $divisi?->toleransi_menit !== null ? $divisi->toleransi_menit : $konfigurasi->toleransi_menit;

            $jamMasuk = Carbon::parse(today()->format('Y-m-d') . ' ' . $targetJamMasukStr);
            $toleransi = (clone $jamMasuk)->addMinutes($toleransiMenit);
            $waktuMasuk = Carbon::parse(today()->format('Y-m-d') . ' ' . $sekarang);

            $absensi->status_kehadiran = $waktuMasuk->lte($toleransi) ? 'hadir' : 'terlambat';
            $absensi->save();

            return response()->json([
                'success' => true,
                'message' => 'Absen masuk berhasil dicatat pada ' . now()->format('H:i'),
                'status_kehadiran' => $absensi->status_kehadiran,
                'waktu' => now()->format('H:i:s'),
                'distance' => round($distance, 4),
            ]);

        } else { // keluar
            if (!$absensi->waktu_masuk) {
                return response()->json(['success' => false, 'message' => 'Anda belum absen masuk.'], 422);
            }
            if ($absensi->waktu_keluar) {
                return response()->json(['success' => false, 'message' => 'Anda sudah absen keluar hari ini.'], 422);
            }

            $absensi->waktu_keluar = $sekarang;
            $absensi->lat_keluar = $request->latitude;
            $absensi->lng_keluar = $request->longitude;
            $absensi->face_distance_keluar = round($distance, 4);
            $absensi->ip_keluar = $request->ip();
            $absensi->user_agent_keluar = $request->userAgent();
            if ($fotoPath) {
                $absensi->foto_keluar = $fotoPath;
            }

            // Hitung jam kerja
            $masuk = Carbon::parse(today()->format('Y-m-d') . ' ' . $absensi->waktu_masuk);
            $keluar = Carbon::parse(today()->format('Y-m-d') . ' ' . $sekarang);
            $absensi->jam_kerja = round($masuk->diffInMinutes($keluar) / 60, 2);
            $absensi->save();

            return response()->json([
                'success' => true,
                'message' => 'Absen keluar berhasil dicatat pada ' . now()->format('H:i'),
                'jam_kerja' => $absensi->jam_kerja,
                'waktu' => now()->format('H:i:s'),
                'distance' => round($distance, 4),
            ]);
        }
    }

    /**
     * Riwayat absensi karyawan (self)
     */
    public function riwayat(Request $request)
    {
        $karyawan = auth()->user()->karyawan;
        $query = Absensi::where('karyawan_id', $karyawan->id);

        if ($request->bulan) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        if ($request->tahun) {
            $query->whereYear('tanggal', $request->tahun);
        } else {
            $query->whereYear('tanggal', now()->year);
        }

        $absensi = $query->orderBy('tanggal', 'desc')->paginate(20)->withQueryString();

        return view('karyawan.absensi.riwayat', compact('absensi'));
    }

    /**
     * Monitor absensi (admin_hrd + manajer)
     */
    public function monitor(Request $request)
    {
        $query = Absensi::with(['karyawan.divisi', 'karyawan.jabatan']);

        if ($request->tanggal) {
            $query->where('tanggal', $request->tanggal);
        } else {
            $query->where('tanggal', today());
        }

        if ($request->divisi_id) {
            $query->whereHas('karyawan', fn($q) => $q->where('divisi_id', $request->divisi_id));
        }

        $absensi = $query->paginate(20)->withQueryString();
        $divisi = \App\Models\Divisi::all();

        return view('admin-hrd.absensi.monitor', compact('absensi', 'divisi'));
    }

    /**
     * Detail monitoring absensi (kecocokan wajah & aktivitas login karyawan)
     */
    public function showMonitorDetail(Absensi $absensi)
    {
        $absensi->load(['karyawan.user', 'karyawan.divisi', 'karyawan.jabatan']);

        $user = $absensi->karyawan?->user;
        $loginLogs = $user
            ? \App\Models\UserLoginLog::where('user_id', $user->id)
                ->orderBy('logged_at', 'desc')
                ->take(15)
                ->get()
            : collect([]);

        $konfigurasi = KonfigurasiSistem::getActive();

        // Hitung jarak Haversine ke kantor jika koordinat tersedia
        $jarakMasuk = null;
        if ($absensi->lat_masuk && $absensi->lng_masuk && $konfigurasi && $konfigurasi->isKonfigured()) {
            $jarakMasuk = round($this->haversine($absensi->lat_masuk, $absensi->lng_masuk, $konfigurasi->lat_kantor, $konfigurasi->lng_kantor), 1);
        }

        $jarakKeluar = null;
        if ($absensi->lat_keluar && $absensi->lng_keluar && $konfigurasi && $konfigurasi->isKonfigured()) {
            $jarakKeluar = round($this->haversine($absensi->lat_keluar, $absensi->lng_keluar, $konfigurasi->lat_kantor, $konfigurasi->lng_kantor), 1);
        }

        return view('admin-hrd.absensi.detail', compact('absensi', 'loginLogs', 'konfigurasi', 'jarakMasuk', 'jarakKeluar'));
    }

    /**
     * Detail absensi karyawan (self view)
     */
    public function showKaryawanDetail(Absensi $absensi)
    {
        $karyawan = auth()->user()->karyawan;
        if (!$karyawan || ($absensi->karyawan_id !== $karyawan->id && !auth()->user()->isAdmin())) {
            abort(403, 'Anda tidak memiliki akses ke data absensi ini.');
        }

        $absensi->load(['karyawan.divisi', 'karyawan.jabatan']);
        $konfigurasi = KonfigurasiSistem::getActive();

        $jarakMasuk = null;
        if ($absensi->lat_masuk && $absensi->lng_masuk && $konfigurasi && $konfigurasi->isKonfigured()) {
            $jarakMasuk = round($this->haversine($absensi->lat_masuk, $absensi->lng_masuk, $konfigurasi->lat_kantor, $konfigurasi->lng_kantor), 1);
        }

        $jarakKeluar = null;
        if ($absensi->lat_keluar && $absensi->lng_keluar && $konfigurasi && $konfigurasi->isKonfigured()) {
            $jarakKeluar = round($this->haversine($absensi->lat_keluar, $absensi->lng_keluar, $konfigurasi->lat_kantor, $konfigurasi->lng_kantor), 1);
        }

        return view('karyawan.absensi.detail', compact('absensi', 'konfigurasi', 'jarakMasuk', 'jarakKeluar'));
    }

    // ===================== HELPER METHODS =====================

    /**
     * Rumus Haversine — hitung jarak (meter) antara dua koordinat GPS
     */
    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // meter

        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLng = deg2rad($lng2 - $lng1);

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLng / 2) * sin($deltaLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Euclidean distance antara dua face descriptor 128-d
     * Di bawah threshold 0.6 = match
     */
    private function euclideanDistance(array $desc1, array $desc2): float
    {
        $sum = 0;
        foreach ($desc1 as $i => $val) {
            $diff = $val - ($desc2[$i] ?? 0);
            $sum += $diff * $diff;
        }
        return sqrt($sum);
    }
}

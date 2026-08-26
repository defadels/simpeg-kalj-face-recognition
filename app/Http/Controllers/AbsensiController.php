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
            'face_landmarks' => 'nullable|array',
            'face_detail' => 'nullable|array',
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

        // Calculate detailed component similarity (Mata, Alis, Hidung, Mulut, Rahang, Overall)
        $masterLandmarks = $karyawan->getFaceLandmarksArray();
        $faceDetail = $this->computeFaceDetailData(
            $request->input('face_landmarks', []),
            $masterLandmarks,
            $distance,
            $request->input('face_detail')
        );

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
            $absensi->face_detail_masuk = $faceDetail;
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
                'face_detail' => $faceDetail,
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
            $absensi->face_detail_keluar = $faceDetail;
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
                'face_detail' => $faceDetail,
            ]);
        }
    }

    /**
     * Hitung rincian kemiripan komponen wajah (Mata, Alis, Hidung, Mulut, Rahang)
     */
    private function computeFaceDetailData(array $currentLandmarks, ?array $masterLandmarks, float $distance, ?array $clientDetail = null): array
    {
        $overallSim = round(max(0, (1 - $distance)) * 100, 1);

        if ($clientDetail && isset($clientDetail['mata'])) {
            return [
                'overall' => $overallSim,
                'mata' => round(floatval($clientDetail['mata']), 1),
                'alis' => round(floatval($clientDetail['alis'] ?? $overallSim), 1),
                'hidung' => round(floatval($clientDetail['hidung'] ?? $overallSim), 1),
                'mulut' => round(floatval($clientDetail['mulut'] ?? $overallSim), 1),
                'rahang' => round(floatval($clientDetail['rahang'] ?? $overallSim), 1),
                'distance' => round($distance, 4),
            ];
        }

        if (!empty($currentLandmarks) && !empty($masterLandmarks)) {
            $groups = [
                'rahang' => range(0, 16),
                'alis'   => range(17, 26),
                'hidung' => range(27, 35),
                'mata'   => range(36, 47),
                'mulut'  => range(48, 67),
            ];

            $normCurrent = $this->normalizeLandmarks($currentLandmarks);
            $normMaster  = $this->normalizeLandmarks($masterLandmarks);

            $result = ['overall' => $overallSim, 'distance' => round($distance, 4)];
            foreach ($groups as $key => $indices) {
                $distSum = 0;
                $count = count($indices);
                foreach ($indices as $idx) {
                    $p1 = $normCurrent[$idx] ?? ['x' => 0, 'y' => 0];
                    $p2 = $normMaster[$idx] ?? ['x' => 0, 'y' => 0];
                    $dx = $p1['x'] - $p2['x'];
                    $dy = $p1['y'] - $p2['y'];
                    $distSum += sqrt($dx * $dx + $dy * $dy);
                }
                $avgDist = $count > 0 ? $distSum / $count : 0;
                $sim = max(5.0, min(99.9, (1 - ($avgDist / 0.28)) * 100));
                $result[$key] = round($sim, 1);
            }
            return $result;
        }

        return [
            'overall' => $overallSim,
            'mata' => min(99.9, max(0.0, round($overallSim + 1.2, 1))),
            'alis' => min(99.9, max(0.0, round($overallSim - 0.8, 1))),
            'hidung' => min(99.9, max(0.0, round($overallSim + 0.5, 1))),
            'mulut' => min(99.9, max(0.0, round($overallSim - 1.1, 1))),
            'rahang' => min(99.9, max(0.0, round($overallSim + 0.2, 1))),
            'distance' => round($distance, 4),
        ];
    }

    private function normalizeLandmarks(array $pts): array
    {
        $parsed = [];
        foreach ($pts as $pt) {
            $x = is_array($pt) ? ($pt['x'] ?? $pt[0] ?? 0) : ($pt->x ?? 0);
            $y = is_array($pt) ? ($pt['y'] ?? $pt[1] ?? 0) : ($pt->y ?? 0);
            $parsed[] = ['x' => floatval($x), 'y' => floatval($y)];
        }
        if (count($parsed) < 68) return $parsed;

        $lx = 0; $ly = 0;
        for ($i = 42; $i <= 47; $i++) { $lx += $parsed[$i]['x']; $ly += $parsed[$i]['y']; }
        $lx /= 6; $ly /= 6;

        $rx = 0; $ry = 0;
        for ($i = 36; $i <= 41; $i++) { $rx += $parsed[$i]['x']; $ry += $parsed[$i]['y']; }
        $rx /= 6; $ry /= 6;

        $eyeDist = sqrt(($rx - $lx)**2 + ($ry - $ly)**2);
        if ($eyeDist < 1) $eyeDist = 1;

        $cx = ($lx + $rx) / 2;
        $cy = ($ly + $ry) / 2;

        $normalized = [];
        foreach ($parsed as $p) {
            $normalized[] = [
                'x' => ($p['x'] - $cx) / $eyeDist,
                'y' => ($p['y'] - $cy) / $eyeDist,
            ];
        }
        return $normalized;
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

        if ($request->status_kehadiran) {
            $query->where('status_kehadiran', $request->status_kehadiran);
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

    /**
     * Halaman absensi manual (admin only)
     */
    public function absensiManualIndex(Request $request)
    {
        $tanggal = $request->tanggal ? \Carbon\Carbon::parse($request->tanggal)->toDateString() : today()->toDateString();

        $karyawanBelumAbsen = $this->getKaryawanBelumAbsen($tanggal);
        $divisi = \App\Models\Divisi::all();

        return view('admin.absensi.absensi-manual', compact('karyawanBelumAbsen', 'tanggal', 'divisi'));
    }

    /**
     * AJAX: Refresh daftar karyawan belum absen berdasarkan tanggal
     */
    public function karyawanBelumAbsen(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
        ]);

        $tanggal = \Carbon\Carbon::parse($request->tanggal)->toDateString();
        $karyawan = $this->getKaryawanBelumAbsen($tanggal);

        return response()->json([
            'success' => true,
            'data'    => $karyawan->map(fn($k) => [
                'id'          => $k->id,
                'nip'         => $k->nip,
                'nama'        => $k->nama_lengkap,
                'divisi'      => $k->divisi?->nama_divisi ?? '-',
                'jabatan'     => $k->jabatan?->nama_jabatan ?? '-',
                'foto_url'    => $k->foto_url,
                'jam_masuk'   => $k->divisi?->jam_masuk ?? null,
                'jam_keluar'  => $k->divisi?->jam_keluar ?? null,
            ]),
        ]);
    }

    /**
     * Simpan absensi manual (admin only) — tanpa face recognition
     */
    public function absensiManual(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id'      => 'required|exists:karyawan,id',
            'tanggal'          => 'required|date',
            'waktu_masuk'      => 'required|date_format:H:i',
            'waktu_keluar'     => 'nullable|date_format:H:i|after:waktu_masuk',
            'status_kehadiran' => 'required|in:hadir,terlambat,izin,sakit,cuti,alpha',
            'keterangan'       => 'nullable|string|max:500',
        ]);

        $tanggal = \Carbon\Carbon::parse($validated['tanggal'])->toDateString();

        // Cek apakah sudah ada record absensi di tanggal tersebut
        $existing = Absensi::where('karyawan_id', $validated['karyawan_id'])
            ->where('tanggal', $tanggal)
            ->first();

        if ($existing && $existing->waktu_masuk) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan sudah memiliki data absensi masuk di tanggal tersebut.',
            ], 422);
        }

        $keterangan = '[MANUAL] ' . ($validated['keterangan'] ?? 'Diisi oleh admin ' . auth()->user()->nama);

        // Hitung jam kerja jika waktu keluar ada
        $jamKerja = null;
        if (!empty($validated['waktu_keluar'])) {
            $masuk  = \Carbon\Carbon::parse($tanggal . ' ' . $validated['waktu_masuk'] . ':00');
            $keluar = \Carbon\Carbon::parse($tanggal . ' ' . $validated['waktu_keluar'] . ':00');
            $jamKerja = round($masuk->diffInMinutes($keluar) / 60, 2);
        }

        $absensi = $existing ?? new Absensi();
        $absensi->karyawan_id      = $validated['karyawan_id'];
        $absensi->tanggal          = $tanggal;
        $absensi->waktu_masuk      = $validated['waktu_masuk'] . ':00';
        $absensi->waktu_keluar     = !empty($validated['waktu_keluar']) ? $validated['waktu_keluar'] . ':00' : null;
        $absensi->status_kehadiran = $validated['status_kehadiran'];
        $absensi->jam_kerja        = $jamKerja;
        $absensi->keterangan       = $keterangan;
        // Field face/lokasi dibiarkan null (tidak perlu face recognition)
        $absensi->save();

        return response()->json([
            'success' => true,
            'message' => 'Absensi manual berhasil disimpan.',
        ]);
    }

    /**
     * Helper: Ambil karyawan aktif yang belum absen di tanggal tertentu (exclude cuti/izin disetujui)
     */
    private function getKaryawanBelumAbsen(string $tanggal): \Illuminate\Database\Eloquent\Collection
    {
        return Karyawan::where('status', 'aktif')
            ->whereDoesntHave('absensi', fn($q) => $q->where('tanggal', $tanggal)->whereNotNull('waktu_masuk'))
            ->whereDoesntHave('cutiIzin', fn($q) => $q
                ->where('status', 'disetujui')
                ->where('tanggal_mulai', '<=', $tanggal)
                ->where('tanggal_selesai', '>=', $tanggal)
            )
            ->with(['jabatan', 'divisi'])
            ->orderBy('nama_lengkap')
            ->get();
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

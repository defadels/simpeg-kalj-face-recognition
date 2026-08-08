# 🏢 SIMPEG KALJ — Sistem Informasi Manajemen Pegawai dengan Face Recognition

> Sistem absensi digital berbasis pengenalan wajah untuk instansi/perusahaan, dibangun dengan **Laravel 13** dan teknologi **AI Face Recognition** di browser.

---

## 📌 Apa Itu SIMPEG KALJ?

**SIMPEG KALJ** adalah aplikasi web untuk mengelola data kepegawaian sekaligus sistem absensi otomatis yang menggunakan **pengenalan wajah (face recognition)**. Karyawan cukup membuka kamera di browser, aplikasi akan langsung mengenali wajahnya, memverifikasi lokasi, lalu mencatat kehadiran secara otomatis — tanpa perlu kartu atau sidik jari.

---

## ✨ Fitur Utama

| Fitur | Penjelasan |
|---|---|
| 👤 **Absensi dengan Wajah** | Karyawan absen masuk/keluar menggunakan kamera dan pengenalan wajah AI |
| 📍 **Validasi Lokasi GPS** | Sistem hanya menerima absensi dalam radius tertentu dari kantor |
| 🗂️ **Manajemen Data Karyawan** | Tambah, ubah, hapus data karyawan lengkap dengan foto dan ID otomatis |
| 🏷️ **Jabatan & Divisi** | Kelola struktur organisasi: jabatan dan divisi beserta jam kerja masing-masing |
| 📅 **Pengajuan Cuti & Izin** | Karyawan bisa mengajukan cuti/izin, lalu disetujui/ditolak oleh admin |
| 📊 **Laporan Kehadiran** | Ekspor laporan absensi ke format **PDF** atau **Excel** |
| ⚙️ **Konfigurasi Sistem** | Admin bisa mengatur koordinat kantor, radius absensi, jam masuk/keluar, dll |
| 🔐 **Sistem Peran (Role)** | Hak akses berbeda untuk Admin, HRD, Manajer, dan Karyawan |
| 📝 **Log Login** | Rekam jejak setiap aktivitas login pengguna |

---

## 🛠️ Teknologi yang Digunakan

### Backend (Server)
| Teknologi | Versi | Fungsi |
|---|---|---|
| **Laravel** | 13.x | Framework PHP utama |
| **PHP** | 8.3+ | Bahasa pemrograman server |
| **Spatie Permission** | 8.x | Manajemen role & permission |
| **Laravel DomPDF** | 3.x | Export laporan ke PDF |
| **Maatwebsite Excel** | 3.x | Export laporan ke Excel |

### Frontend (Browser)
| Teknologi | Fungsi |
|---|---|
| **Blade** | Template engine HTML bawaan Laravel |
| **Tailwind CSS** | Framework CSS untuk tampilan |
| **face-api.js** | Library AI pengenalan wajah di browser |
| **Vite** | Build tool untuk assets JavaScript/CSS |

---

## 👥 Peran Pengguna (Role)

| Peran | Hak Akses |
|---|---|
| **Admin / Super Admin** | Akses penuh: kelola semua data, konfigurasi sistem, laporan |
| **Admin HRD** | Kelola karyawan, jabatan, divisi, approval cuti/izin, monitor absensi |
| **Manajer** | Lihat laporan, approval cuti/izin timnya, monitor absensi |
| **Karyawan** | Absensi, lihat riwayat absensi, pengajuan cuti/izin |

---

## 📂 Struktur Folder Penting

```
simpeg-kalj-face-recognition/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/        ← Otak aplikasi (logika bisnis)
│   │   └── Middleware/         ← Penjaga akses halaman
│   ├── Models/                 ← Representasi tabel database
│   └── Exports/                ← Kelas untuk export Excel
│
├── resources/views/            ← Tampilan halaman (HTML)
│   ├── karyawan/               ← Halaman untuk karyawan
│   ├── admin-hrd/              ← Halaman untuk admin HRD
│   ├── admin/                  ← Halaman untuk super admin
│   └── welcome.blade.php       ← Landing page
│
├── routes/
│   └── web.php                 ← Daftar semua URL/halaman
│
└── database/
    ├── migrations/             ← Rancangan struktur tabel database
    └── seeders/                ← Data awal/contoh
```

---

## 🔍 Penjelasan Teknikal Per File

---

### 📁 `app/Models/User.php`
**Fungsi:** Representasi tabel `users` — data akun login setiap pengguna.

```php
// Kolom yang bisa diisi
protected $fillable = ['nama', 'email', 'password', 'role', 'is_active'];

// Password otomatis di-hash saat disimpan
'password' => 'hashed',

// Satu User punya satu data Karyawan
public function karyawan()
{
    return $this->hasOne(Karyawan::class);
}

// Mengecek apakah user adalah admin
public function isAdmin(): bool
{
    return $this->role === 'admin';
}
```

**Penjelasan:** Model ini menyimpan data akun (email/password). Field `role` menentukan peran pengguna (`admin` atau `karyawan`). Field `is_active` digunakan untuk menonaktifkan akun tanpa menghapusnya. Metode `isAdmin()`, `isManajer()`, dll. adalah cara cepat untuk mengecek peran pengguna di mana saja dalam kode.

---

### 📁 `app/Models/Karyawan.php`
**Fungsi:** Representasi tabel `karyawan` — data lengkap pegawai termasuk data wajah.

```php
// Kolom penting yang menyimpan data wajah
'face_data',      // JSON berisi 128 angka unik pengenal wajah (face descriptor)
'face_landmarks', // JSON berisi 68 titik koordinat fitur wajah (mata, hidung, dll)
'foto_enrollment',// Path foto saat pendaftaran wajah

// Mengambil face descriptor sebagai array PHP
public function getFaceDescriptorArray(): ?array
{
    return json_decode($this->face_data, true);
}

// Generate ID karyawan otomatis: KALJ-0001, KALJ-0002, dst.
public static function generateNextNip(): string
{
    // Mencari NIP terakhir lalu menambah angkanya
    $lastKaryawan = static::where('nip', 'like', 'KALJ-%')->...->first();
    return 'KALJ-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
}

// Cek apakah karyawan sudah absen masuk hari ini
public function sudahAbsenMasukHariIni(): bool
{
    return $this->absensi()
        ->where('tanggal', today())
        ->whereNotNull('waktu_masuk')
        ->exists();
}
```

**Penjelasan:** Ini adalah model inti. Kolom `face_data` menyimpan 128 angka yang merupakan "sidik digital" wajah karyawan dalam format JSON. Kolom `face_landmarks` menyimpan koordinat 68 titik wajah (mata kiri, mata kanan, hidung, mulut, rahang) untuk analisis detail per-fitur.

---

### 📁 `app/Models/Absensi.php`
**Fungsi:** Representasi tabel `absensi` — setiap baris adalah satu rekaman kehadiran per hari per karyawan.

```php
// Kolom penting
'waktu_masuk', 'waktu_keluar',               // Jam absen masuk dan keluar
'lat_masuk', 'lng_masuk',                    // Koordinat GPS saat absen masuk
'face_distance_masuk', 'face_distance_keluar',// Nilai jarak Euclidean wajah
'foto_masuk', 'foto_keluar',                 // Snapshot foto saat absen
'ip_masuk', 'user_agent_masuk',              // Alamat IP & browser yang digunakan
'status_kehadiran',                          // 'hadir'/'terlambat'/'alpha'/'izin'/'cuti'

// Konversi jarak wajah ke persentase kemiripan
public function getSimilarityMasukAttribute(): ?float
{
    // Rumus: Similarity % = (1 - distance) * 100
    // Jarak 0.0 = 100% mirip, Jarak 1.0 = 0% mirip
    return round(max(0, (1 - $this->face_distance_masuk)) * 100, 1);
}

// Hitung total jam kerja (dari waktu masuk hingga keluar)
public function hitungJamKerja(): float
{
    $masuk  = Carbon::parse($this->tanggal . ' ' . $this->waktu_masuk);
    $keluar = Carbon::parse($this->tanggal . ' ' . $this->waktu_keluar);
    return round($masuk->diffInMinutes($keluar) / 60, 2);
}
```

**Penjelasan:** Setiap karyawan hanya boleh punya **satu baris per tanggal** (enforced oleh `unique(['karyawan_id', 'tanggal'])`). Satu baris ini diisi dua kali: pertama saat masuk, lalu diperbarui saat keluar. Kolom `face_distance_masuk` menyimpan seberapa "jauh" wajah yang terdeteksi dari wajah yang terdaftar — semakin kecil nilainya, semakin mirip.

---

### 📁 `app/Models/KonfigurasiSistem.php`
**Fungsi:** Menyimpan pengaturan global sistem (koordinat kantor, jam kerja, dll).

```php
// Kolom konfigurasi
'lat_kantor',       // Latitude (garis lintang) kantor
'lng_kantor',       // Longitude (garis bujur) kantor
'radius_meter',     // Radius (meter) yang dibolehkan untuk absensi
'jam_masuk',        // Jam masuk default, contoh: "08:00"
'jam_keluar',       // Jam keluar default, contoh: "17:00"
'toleransi_menit',  // Berapa menit boleh terlambat (misal: 15 menit)

// Ambil konfigurasi aktif (hanya ada 1 baris)
public static function getActive(): ?self
{
    return static::first();
}

// Cek apakah koordinat kantor sudah diisi
public function isKonfigured(): bool
{
    return $this->lat_kantor !== null && $this->lng_kantor !== null;
}
```

**Penjelasan:** Tabel ini hanya memiliki **satu baris data** yang berisi semua pengaturan. Setiap kali admin mengubah pengaturan, baris tersebut di-update, tidak dibuat baru. Metode `isKonfigured()` digunakan untuk memastikan admin sudah mengisi koordinat kantor sebelum sistem bisa digunakan.

---

### 📁 `app/Models/CutiIzin.php`
**Fungsi:** Menyimpan setiap pengajuan cuti atau izin karyawan.

```php
// Kolom penting
'jenis',             // 'cuti' atau 'izin'
'tanggal_mulai',     // Tanggal mulai cuti/izin
'tanggal_selesai',   // Tanggal selesai
'jumlah_hari',       // Total hari kerja (tidak termasuk Sabtu-Minggu)
'alasan',            // Alasan pengajuan
'lampiran',          // Path file lampiran (PDF/gambar)
'status',            // 'pending' / 'disetujui' / 'ditolak'
'diproses_oleh',     // ID admin/manajer yang memproses
'catatan_prosesor',  // Catatan dari admin saat approve/reject
```

**Penjelasan:** Saat karyawan mengajukan cuti, status langsung `pending`. Setelah admin menyetujui/menolak, status berubah dan kolom `diproses_oleh` diisi dengan ID admin yang memprosesnya. Ini membuat ada jejak audit yang jelas.

---

### 📁 `app/Models/UserLoginLog.php`
**Fungsi:** Mencatat setiap aktivitas login pengguna untuk keperluan audit.

```php
'ip_address', // Alamat IP saat login
'user_agent', // Informasi browser dan sistem operasi
'status',     // 'success' atau 'failed'
'logged_at',  // Waktu login

// Mengurai user_agent menjadi nama browser dan OS yang mudah dibaca
public function getDeviceFormattedAttribute(): string
{
    // Contoh output: "Google Chrome (Windows)"
    if (str_contains($ua, 'Chrome')) $browser = 'Google Chrome';
    if (str_contains($ua, 'Windows')) $os = 'Windows';
    return "{$browser} ({$os})";
}
```

**Penjelasan:** Setiap login dicatat otomatis. Admin bisa melihat dari perangkat mana saja karyawan pernah login, berguna untuk mendeteksi aktivitas mencurigakan.

---

### 📁 `app/Http/Controllers/AbsensiController.php`
**Fungsi:** Otak dari seluruh proses absensi — validasi lokasi, verifikasi wajah, dan penyimpanan data.

#### Method `checkLokasi()` — Validasi GPS (baris 41–77)
```php
public function checkLokasi(Request $request): JsonResponse
{
    // Menerima koordinat dari browser
    $jarak = $this->haversine(
        $request->latitude, $request->longitude,
        $konfigurasi->lat_kantor, $konfigurasi->lng_kantor
    );

    // Jika jarak <= radius → valid, jika tidak → tolak
    $valid = $jarak <= $konfigurasi->radius_meter;

    return response()->json(['valid' => $valid, 'jarak' => $jarak, ...]);
}
```
**Penjelasan:** Dipanggil via AJAX oleh browser. Browser mengirim koordinat GPS, server menghitung jarak ke kantor menggunakan rumus Haversine, lalu mengembalikan hasil valid/tidak.

---

#### Method `prosesAbsensi()` — Verifikasi Wajah & Simpan (baris 83–252)
```php
public function prosesAbsensi(Request $request): JsonResponse
{
    // 1. Validasi lokasi ulang di server (agar tidak bisa dimanipulasi)
    $jarak = $this->haversine(...);
    if ($jarak > $konfigurasi->radius_meter) return error;

    // 2. Bandingkan face descriptor dari browser vs database
    $faceDescriptor = $karyawan->getFaceDescriptorArray();  // dari DB
    $distance = $this->euclideanDistance(
        $request->face_descriptor, // dari browser (128 angka)
        $faceDescriptor            // dari database (128 angka tersimpan)
    );

    // 3. Jika jarak wajah > 0.60 → wajah tidak cocok → tolak
    if ($distance > 0.60) return error("Verifikasi wajah gagal...");

    // 4. Simpan foto snapshot sebagai file
    // ... decode base64 → simpan ke storage/absensi/

    // 5. Tentukan status: 'hadir' atau 'terlambat'
    $absensi->status_kehadiran = $waktuMasuk->lte($toleransi) ? 'hadir' : 'terlambat';
    $absensi->save();
}
```
**Penjelasan:** Ini adalah method paling kritis. Ada **4 lapisan validasi**: (1) cek lokasi GPS, (2) cek wajah terdaftar di DB, (3) hitung jarak Euclidean antar deskriptor, (4) putuskan status hadir/terlambat. Foto diambil dari kamera browser dalam format Base64 lalu di-decode dan disimpan sebagai file gambar.

---

#### Method `haversine()` — Rumus Jarak GPS (baris 455–471)
```php
private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
{
    $earthRadius = 6371000; // radius bumi dalam meter

    $deltaLat = deg2rad($lat2 - $lat1); // perbedaan latitude dalam radian
    $deltaLng = deg2rad($lng2 - $lng1); // perbedaan longitude dalam radian

    $a = sin($deltaLat/2) * sin($deltaLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($deltaLng/2) * sin($deltaLng/2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earthRadius * $c; // hasil dalam meter
}
```
**Penjelasan:** Koordinat GPS adalah sudut (derajat), bukan jarak lurus. Rumus Haversine mengkonversi koordinat menjadi jarak sesungguhnya di permukaan bumi yang melengkung. Tanpa rumus ini, hasil jarak akan tidak akurat.

---

#### Method `euclideanDistance()` — Pencocokan Wajah (baris 477–485)
```php
private function euclideanDistance(array $desc1, array $desc2): float
{
    $sum = 0;
    foreach ($desc1 as $i => $val) {
        $diff = $val - ($desc2[$i] ?? 0); // selisih tiap angka
        $sum += $diff * $diff;            // kuadratkan lalu jumlahkan
    }
    return sqrt($sum); // akar kuadrat dari total = "jarak"
}
```
**Penjelasan:** Face descriptor adalah array 128 angka desimal. Rumus ini menghitung "jarak" antara dua titik di ruang berdimensi-128. Semakin kecil hasilnya, semakin mirip dua wajah tersebut. Jika hasil < 0.60, wajah dianggap cocok.

---

#### Method `computeFaceDetailData()` — Analisis Per-Fitur Wajah (baris 257–312)
```php
private function computeFaceDetailData(...): array
{
    // Membagi 68 titik wajah ke dalam kelompok fitur
    $groups = [
        'rahang' => range(0, 16),  // titik 0-16 = garis rahang
        'alis'   => range(17, 26), // titik 17-26 = alis kiri & kanan
        'hidung' => range(27, 35), // titik 27-35 = batang hidung
        'mata'   => range(36, 47), // titik 36-47 = kedua mata
        'mulut'  => range(48, 67), // titik 48-67 = bibir & mulut
    ];

    // Untuk setiap fitur, hitung rata-rata jarak antar titik
    // lalu konversi ke persentase kemiripan
}
```
**Penjelasan:** Selain kemiripan keseluruhan, sistem juga menghitung kemiripan per-bagian wajah. 68 titik landmark wajah dibagi ke 5 kelompok, lalu setiap kelompok dibandingkan secara terpisah. Hasilnya ditampilkan di halaman detail absensi admin.

---

### 📁 `app/Http/Controllers/CutiIzinController.php`
**Fungsi:** Mengelola alur pengajuan, persetujuan, dan penolakan cuti/izin.

#### Method `store()` — Simpan Pengajuan Baru (baris 35–76)
```php
public function store(Request $request)
{
    // Hitung hari kerja (tidak termasuk Sabtu & Minggu)
    $jumlahHari = $this->hitungHariKerja($validated['tanggal_mulai'], $validated['tanggal_selesai']);

    // Cek apakah saldo cuti cukup
    if ($validated['jenis'] === 'cuti' && $karyawan->saldo_cuti < $jumlahHari) {
        return redirect()->back()->with('error', 'Saldo cuti tidak mencukupi...');
    }

    // Simpan ke database dengan status 'pending'
    CutiIzin::create([..., 'status' => 'pending']);
}
```

#### Method `approve()` — Setujui Pengajuan (baris 107–133)
```php
public function approve(Request $request, CutiIzin $cutiIzin)
{
    $cutiIzin->update(['status' => 'disetujui', 'diproses_oleh' => auth()->id(), ...]);

    // Potong saldo cuti karyawan
    if ($cutiIzin->jenis === 'cuti') {
        $cutiIzin->karyawan->decrement('saldo_cuti', $cutiIzin->jumlah_hari);
    }

    // Update status absensi hari-hari tersebut menjadi 'cuti'/'izin'
    $this->updateAbsensiStatus($cutiIzin);
}
```

#### Method `hitungHariKerja()` — Hitung Hari Kerja (baris 157–170)
```php
private function hitungHariKerja(string $mulai, string $selesai): int
{
    $current = Carbon::parse($mulai);
    while ($current->lte(Carbon::parse($selesai))) {
        if (!$current->isWeekend()) { // skip Sabtu & Minggu
            $hari++;
        }
        $current->addDay(); // maju satu hari
    }
    return $hari;
}
```
**Penjelasan:** Sistem menghitung hari kerja nyata, bukan hari kalender. Misal mengajukan cuti Senin–Jumat = 5 hari kerja. Sabtu & Minggu tidak dihitung sebagai hari cuti.

---

### 📁 `app/Http/Controllers/LaporanController.php`
**Fungsi:** Membuat laporan absensi dan mengekspornya ke PDF atau Excel.

#### Export PDF (baris 46–76)
```php
public function exportPdf(Request $request)
{
    // Ambil data absensi dalam rentang periode
    $absensi = Absensi::with(['karyawan.jabatan', 'karyawan.divisi'])
        ->whereBetween('tanggal', [$periodeAwal, $periodeAkhir])->get();

    // Catat aktivitas pembuatan laporan ke tabel 'laporan'
    Laporan::create(['jenis_laporan' => 'kehadiran', ...]);

    // Generate PDF dari view Blade, format kertas A4 landscape
    $pdf = Pdf::loadView('laporan.pdf.absensi', compact('absensi', ...))
        ->setPaper('a4', 'landscape');

    return $pdf->download("laporan-absensi-{$periodeAwal}-{$periodeAkhir}.pdf");
}
```

#### Export Excel (baris 78–97)
```php
public function exportExcel(Request $request)
{
    return Excel::download(
        new AbsensiExport($periodeAwal, $periodeAkhir, $divisiId),
        "laporan-absensi-{$periodeAwal}-{$periodeAkhir}.xlsx"
    );
}
```
**Penjelasan:** Export PDF menggunakan library DomPDF yang mengubah halaman HTML menjadi PDF. Export Excel menggunakan class `AbsensiExport` yang telah didefinisikan di folder `app/Exports/`.

---

### 📁 `app/Exports/AbsensiExport.php`
**Fungsi:** Mendefinisikan struktur file Excel untuk laporan absensi.

```php
// Header kolom Excel
public function headings(): array
{
    return ['No', 'ID Karyawan', 'Nama Karyawan', 'Divisi',
            'Jabatan', 'Tanggal', 'Waktu Masuk', 'Waktu Keluar',
            'Jam Kerja', 'Status Kehadiran', ...];
}

// Mengisi setiap baris data
public function map($absensi): array
{
    return [
        $absensi->karyawan->nip,
        $absensi->karyawan->nama_lengkap,
        $absensi->tanggal->format('d/m/Y'), // format tanggal Indonesia
        $absensi->jam_kerja . ' jam',
        ucfirst($absensi->status_kehadiran), // "hadir" → "Hadir"
    ];
}

// Style header: teks putih, background biru
public function styles(Worksheet $sheet): array
{
    return [1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                  'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF0EA5E9']]]];
}
```
**Penjelasan:** Class ini mengimplementasikan beberapa `interface` dari library Maatwebsite Excel. `FromQuery` = sumber data dari database, `WithHeadings` = baris header, `WithMapping` = cara memetakan tiap baris, `WithStyles` = tampilan visual, `ShouldAutoSize` = lebar kolom otomatis menyesuaikan konten.

---

### 📁 `app/Http/Controllers/AdminController.php`
**Fungsi:** Dashboard admin dan manajemen akun pengguna (User).

#### Dashboard (baris 17–44)
```php
public function dashboard()
{
    $stats = [
        'total_karyawan'  => Karyawan::where('status', 'aktif')->count(),
        'hadir_hari_ini'  => Absensi::where('tanggal', today())
                              ->whereIn('status_kehadiran', ['hadir', 'terlambat'])->count(),
        'cuti_pending'    => CutiIzin::where('status', 'pending')->count(),
        'alpha_hari_ini'  => Karyawan::aktif()->count() - Absensi::hari_ini()->count(),
    ];
}
```
**Penjelasan:** Saat admin membuka dashboard, PHP menjalankan **4 query database** secara langsung untuk mendapatkan statistik real-time: jumlah karyawan aktif, yang hadir hari ini, cuti yang menunggu, dan yang alpha (tidak hadir).

---

### 📁 `app/Http/Controllers/KonfigurasiSistemController.php`
**Fungsi:** Mengatur parameter sistem seperti koordinat kantor dan jam kerja.

```php
public function update(Request $request)
{
    // Validasi input: lat harus antara -90 dan 90, radius min 10m max 5km
    $validated = $request->validate([
        'lat_kantor'      => 'nullable|numeric|between:-90,90',
        'lng_kantor'      => 'nullable|numeric|between:-180,180',
        'radius_meter'    => 'required|integer|min:10|max:5000',
        'toleransi_menit' => 'required|integer|min:0|max:120',
    ]);

    // Update jika sudah ada, buat baru jika belum
    if ($konfigurasi) {
        $konfigurasi->update(array_merge($validated, ['updated_by' => auth()->id()]));
    } else {
        KonfigurasiSistem::create(...);
    }
}
```
**Penjelasan:** Pattern `updateOrCreate` digunakan di sini secara manual. Selalu hanya ada satu baris konfigurasi di tabel. Kolom `updated_by` mencatat siapa admin yang terakhir mengubah pengaturan.

---

### 📁 `app/Http/Middleware/CheckRole.php`
**Fungsi:** "Satpam" yang menjaga setiap halaman agar hanya diakses oleh peran yang berwenang.

```php
public function handle(Request $request, Closure $next, string ...$roles): Response
{
    // 1. Cek apakah sudah login
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    // 2. Cek apakah akun aktif
    if (!$user->is_active) {
        auth()->logout(); // paksa logout jika akun dinonaktifkan
        return redirect()->route('login')->withErrors(['email' => 'Akun dinonaktifkan.']);
    }

    // 3. Cek apakah role cocok
    $hasAccess = in_array($user->role, $roles) || ($user->isAdmin() && ...);

    if (!$hasAccess) {
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }

    return $next($request); // lanjut ke halaman jika semua lolos
}
```
**Penjelasan:** Middleware ini dijalankan **sebelum** setiap request masuk ke Controller. Ia memeriksa 3 hal: login?, akun aktif?, role benar?. Jika salah satu gagal, request langsung dihentikan.

---

### 📁 `routes/web.php`
**Fungsi:** Daftar semua URL aplikasi beserta siapa yang boleh mengaksesnya.

```php
// Semua URL dikelompokkan berdasarkan middleware (penjaga akses)

// Hanya Admin yang boleh akses URL dengan prefix /admin/...
Route::middleware(['auth', 'check.role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/karyawan', [AdminHrdController::class, 'indexKaryawan'])->name('admin.karyawan.index');
    Route::get('/karyawan/{karyawan}/face-enrollment', ...)->name('admin.karyawan.face-enrollment');
    // ... dan seterusnya
});

// Karyawan dan Admin boleh akses URL dengan prefix /karyawan/...
Route::middleware(['auth', 'check.role:karyawan,admin'])->prefix('karyawan')->group(function () {
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('karyawan.absensi.index');
    Route::post('/absensi/proses', [AbsensiController::class, 'prosesAbsensi']);
    // ...
});
```
**Penjelasan:** Pengelompokan route dengan `->group()` memungkinkan satu middleware diterapkan ke banyak URL sekaligus. Method HTTP `GET` untuk menampilkan halaman, `POST` untuk mengirim data baru, `PUT`/`PATCH` untuk mengubah data, `DELETE` untuk menghapus.

---

### 📁 `database/migrations/`
**Fungsi:** "Blueprint" (cetak biru) struktur tabel database. Dijalankan dengan `php artisan migrate`.

#### Contoh: `2024_01_01_000004_create_absensi_table.php`
```php
Schema::create('absensi', function (Blueprint $table) {
    $table->id();                         // kolom ID otomatis (primary key)
    $table->foreignId('karyawan_id')      // relasi ke tabel karyawan
          ->constrained('karyawan')
          ->onDelete('cascade');          // jika karyawan dihapus, absensinya ikut terhapus
    $table->date('tanggal');              // kolom tanggal
    $table->time('waktu_masuk')->nullable();   // nullable = boleh kosong
    $table->time('waktu_keluar')->nullable();
    $table->decimal('lat_masuk', 10, 7)->nullable(); // 10 digit, 7 angka di belakang koma
    $table->enum('status_kehadiran', ['hadir', 'terlambat', 'alpha', 'izin', 'cuti'])
          ->default('alpha');             // default = alpha jika tidak ada data
    $table->unique(['karyawan_id', 'tanggal']); // satu karyawan = satu baris per hari
});
```
**Penjelasan:** Migration adalah cara terstruktur mendefinisikan tabel database menggunakan kode PHP (bukan SQL mentah). `->nullable()` artinya kolom boleh kosong. `->cascade()` artinya jika data induk dihapus, data anaknya ikut terhapus otomatis. `->unique()` memastikan tidak ada duplikasi.

---

### 📁 `database/migrations/2026_08_01_000008_add_foto_and_distance_to_absensi_table.php`
**Fungsi:** Migrasi tambahan yang menambah kolom baru ke tabel yang sudah ada.

```php
// Menambah kolom baru ke tabel 'absensi' yang sudah ada
Schema::table('absensi', function (Blueprint $table) {
    $table->string('foto_masuk')->nullable()->after('lng_keluar');         // path foto
    $table->decimal('face_distance_masuk', 6, 4)->nullable();             // jarak wajah
    $table->string('ip_masuk', 45)->nullable();                           // alamat IP
    $table->text('user_agent_masuk')->nullable();                         // info browser
});
```
**Penjelasan:** Kolom-kolom ini ditambahkan kemudian (bukan sejak awal) sebagai peningkatan fitur. Dengan migrasi, penambahan kolom terdokumentasi dengan baik dan bisa di-rollback jika terjadi kesalahan.

---

## 🔒 Alur Keamanan Lengkap

```
Request Masuk dari Browser
        │
        ▼
[Middleware Auth] ──── belum login? ──► redirect ke /login
        │
        ▼
[Middleware CheckRole] ── role salah? ──► HTTP 403 Forbidden
        │                  akun nonaktif? ──► logout + redirect
        ▼
[Controller Method]
        │
        ▼ (untuk absensi)
[Validasi Lokasi GPS] ── di luar radius? ──► return JSON error
        │
        ▼
[Verifikasi Wajah] ── jarak Euclidean > 0.60? ──► return JSON error
        │
        ▼
[Simpan ke Database] ──► return JSON success
```

---

## 🗄️ Struktur Database (Entity Relationship)

```
users (1) ──────── (1) karyawan
                         │
                ┌────────┼────────┐
               (n)      (n)      (1)
             absensi  cuti_izin  divisi (n) ── (1) jabatan
```

- **users → karyawan**: 1 akun login = 1 data karyawan
- **karyawan → absensi**: 1 karyawan = banyak rekaman absensi
- **karyawan → cuti_izin**: 1 karyawan = banyak pengajuan cuti/izin
- **divisi → karyawan**: 1 divisi = banyak karyawan
- **jabatan → karyawan**: 1 jabatan = banyak karyawan

---

## 🚀 Cara Instalasi & Menjalankan

### Persyaratan Sistem
- PHP >= 8.3
- Composer
- Node.js & NPM
- MySQL / SQLite
- Web server (Apache/Nginx) atau Laragon

### Langkah Instalasi

**1. Clone atau download proyek**
```bash
git clone <url-repositori> simpeg-kalj-face-recognition
cd simpeg-kalj-face-recognition
```

**2. Install dependensi PHP**
```bash
composer install
```

**3. Salin file konfigurasi**
```bash
cp .env.example .env
```

**4. Generate Application Key**
```bash
php artisan key:generate
```

**5. Atur koneksi database di file `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simpeg_kalj
DB_USERNAME=root
DB_PASSWORD=
```

**6. Jalankan migrasi dan seeder database**
```bash
php artisan migrate
php artisan db:seed
```

**7. Buat symlink storage (untuk foto)**
```bash
php artisan storage:link
```

**8. Install dependensi JavaScript dan build assets**
```bash
npm install
npm run build
```

**9. Jalankan server**
```bash
php artisan serve
```

Atau gunakan shortcut (menjalankan semua sekaligus):
```bash
composer run dev
```

Akses aplikasi di: **http://localhost:8000**

> **Alternatif dengan Laragon:** Cukup taruh folder proyek di `C:\laragon\www\`, lalu akses `http://simpeg-kalj-face-recognition.test`

---

## 🔑 Akun Default (Setelah Seeder)

| Role | Email | Password |
|---|---|---|
| Admin | admin@example.com | password |
| Karyawan | karyawan@example.com | password |

> ⚠️ **Segera ganti password** setelah pertama kali login di production!

---

## 📊 Keterangan Status Kehadiran

| Status | Artinya |
|---|---|
| ✅ **Hadir** | Absen masuk tepat waktu |
| ⚠️ **Terlambat** | Absen masuk melebihi toleransi keterlambatan |
| ❌ **Alpha** | Tidak absen sama sekali |
| 🔵 **Izin** | Pengajuan izin disetujui |
| 🟣 **Cuti** | Pengajuan cuti disetujui |

---

*Dibuat dengan ❤️ menggunakan Laravel 13 & face-api.js*
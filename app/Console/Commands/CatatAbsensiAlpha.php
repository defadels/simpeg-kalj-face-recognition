<?php

namespace App\Console\Commands;

use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\KonfigurasiSistem;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CatatAbsensiAlpha extends Command
{
    /**
     * Signature command beserta opsi yang tersedia.
     *
     * Opsi:
     *   --tanggal=  : Tentukan tanggal manual (format Y-m-d). Default: hari ini.
     *   --force     : Paksa berjalan meskipun bukan hari kerja atau sebelum jam pulang.
     *   --dry-run   : Preview tanpa menyimpan ke database.
     */
    protected $signature = 'absensi:catat-alpha
                            {--tanggal= : Tanggal target (Y-m-d). Default: hari ini}
                            {--force    : Paksa jalan meski bukan hari kerja / sebelum jam pulang}
                            {--dry-run  : Preview saja, tidak menyimpan ke database}';

    protected $description = 'Catat absensi alpha secara otomatis untuk karyawan aktif yang tidak hadir pada hari kerja';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $isForced = $this->option('force');

        // 1. Tentukan tanggal target
        $tanggalStr = $this->option('tanggal');
        try {
            $tanggal = $tanggalStr
                ? Carbon::createFromFormat('Y-m-d', $tanggalStr)->startOfDay()
                : Carbon::today();
        } catch (\Exception $e) {
            $this->error("Format tanggal tidak valid: {$tanggalStr}. Gunakan Y-m-d.");
            return self::FAILURE;
        }

        $this->info("+---------------------------------------------------------+");
        $this->info("¦          CATAT ABSENSI ALPHA — SIMPEG KALJ              ¦");
        $this->info("+---------------------------------------------------------+");
        $this->line("  Tanggal target : {$tanggal->isoFormat('dddd, D MMMM Y')}");
        $this->line("  Mode           : " . ($isDryRun ? 'DRY RUN (tidak menyimpan)' : 'LIVE'));
        $this->newLine();

        // 2. Cek hari kerja (Senin-Jumat)
        if ($tanggal->isWeekend() && !$isForced) {
            $this->warn("  Hari ini adalah akhir pekan (" . $tanggal->isoFormat('dddd') . "). Command dihentikan.");
            $this->line("  Gunakan --force untuk memaksa berjalan di akhir pekan.");
            return self::SUCCESS;
        }

        // 3. Cek apakah sudah melewati jam pulang kantor (hanya jika tanggal = hari ini)
        $konfigurasi = KonfigurasiSistem::getActive();
        $jamKeluar   = $konfigurasi?->jam_keluar ?? '17:00:00';

        if ($tanggal->isToday() && !$isForced) {
            $batasWaktu = Carbon::today()->setTimeFromTimeString($jamKeluar);
            if (now()->lt($batasWaktu)) {
                $this->warn("  Hari kerja belum selesai. Jam pulang kantor: {$jamKeluar}.");
                $this->line("  Command ini sebaiknya dijalankan setelah jam pulang kantor.");
                $this->line("  Gunakan --force untuk memaksa berjalan sekarang.");
                return self::SUCCESS;
            }
        }

        // 4. Ambil karyawan aktif yang belum absen dan tidak sedang cuti/izin/sakit
        $karyawanTidakHadir = Karyawan::where('status', 'aktif')
            ->whereDoesntHave('absensi', fn ($q) => $q->where('tanggal', $tanggal->toDateString()))
            ->whereDoesntHave('cutiIzin', fn ($q) => $q
                ->where('status', 'disetujui')
                ->where('tanggal_mulai', '<=', $tanggal->toDateString())
                ->where('tanggal_selesai', '>=', $tanggal->toDateString())
            )
            ->with(['divisi', 'jabatan'])
            ->get();

        if ($karyawanTidakHadir->isEmpty()) {
            $this->info("  Semua karyawan aktif sudah hadir atau sedang cuti/izin/sakit.");
            $this->info("  Tidak ada record alpha yang perlu dibuat.");
            return self::SUCCESS;
        }

        // 5. Tampilkan tabel preview
        $this->line("  Ditemukan {$karyawanTidakHadir->count()} karyawan yang akan dicatat ALPHA:");
        $this->newLine();

        $this->table(
            ['NIP', 'Nama Karyawan', 'Divisi', 'Jabatan'],
            $karyawanTidakHadir->map(fn ($k) => [
                $k->nip,
                $k->nama_lengkap,
                $k->divisi?->nama_divisi ?? '-',
                $k->jabatan?->nama_jabatan ?? '-',
            ])->toArray()
        );

        // 6. Konfirmasi interaktif (jika bukan dry-run dan bukan dari scheduler)
        if (!$isDryRun && $this->input->isInteractive()) {
            if (!$this->confirm("  Simpan {$karyawanTidakHadir->count()} record alpha ke database?", true)) {
                $this->warn("  Dibatalkan oleh pengguna.");
                return self::SUCCESS;
            }
        }

        // 7. Proses pencatatan alpha
        $berhasil = 0;
        $gagal    = 0;

        $this->newLine();
        foreach ($karyawanTidakHadir as $karyawan) {
            try {
                if (!$isDryRun) {
                    Absensi::create([
                        'karyawan_id'      => $karyawan->id,
                        'tanggal'          => $tanggal->toDateString(),
                        'status_kehadiran' => 'alpha',
                        'keterangan'       => 'Dicatat otomatis oleh sistem — karyawan tidak melakukan absensi',
                    ]);
                }

                $tag = $isDryRun ? ' [DRY RUN]' : '';
                $this->line("  OK  {$karyawan->nama_lengkap} ({$karyawan->nip}){$tag}");
                $berhasil++;
            } catch (\Exception $e) {
                // Jika sudah ada record (race condition / double-run), skip saja
                if (str_contains($e->getMessage(), 'Duplicate') || str_contains($e->getMessage(), 'UNIQUE')) {
                    $this->line("  --  {$karyawan->nama_lengkap} ({$karyawan->nip}) — record sudah ada, dilewati.");
                } else {
                    $this->line("  !!  {$karyawan->nama_lengkap} ({$karyawan->nip}) — Error: {$e->getMessage()}");
                    $gagal++;
                }
            }
        }

        // 8. Ringkasan akhir
        $this->newLine();
        $this->info("  Selesai  : " . now()->isoFormat('D MMM Y, HH:mm:ss'));
        $this->info("  Berhasil : {$berhasil} record dicatat");

        if ($gagal > 0) {
            $this->error("  Gagal    : {$gagal} record (periksa log)");
        }

        if ($isDryRun) {
            $this->newLine();
            $this->warn("  [DRY RUN] Tidak ada data yang disimpan ke database.");
        }

        return $gagal > 0 ? self::FAILURE : self::SUCCESS;
    }
}

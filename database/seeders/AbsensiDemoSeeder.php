<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Karyawan;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;

class AbsensiDemoSeeder extends Seeder
{
    /**
     * Mengisi data contoh untuk memeriksa format laporan matriks absensi.
     *
     * Aman dijalankan berulang kali: data absensi yang sudah ada tidak
     * diubah atau ditimpa.
     */
    public function run(): void
    {
        $periode = CarbonPeriod::create('2026-09-01', '2026-09-24');
        $karyawanAktif = Karyawan::with('divisi')
            ->where('status', 'aktif')
            ->orderBy('id')
            ->get();

        if ($karyawanAktif->isEmpty()) {
            $this->command?->warn('Tidak ada karyawan aktif. Tambahkan data karyawan terlebih dahulu.');

            return;
        }

        $dibuat = 0;
        foreach ($karyawanAktif as $urutanKaryawan => $karyawan) {
            foreach ($periode as $tanggal) {
                // Laporan akan menampilkan akhir pekan sebagai kolom kosong.
                if ($tanggal->isWeekend()) {
                    continue;
                }

                $tanggalAbsensi = $tanggal->format('Y-m-d');
                $hariKe = $tanggal->day;

                // Beberapa record alpha/izin membuat simbol "-" terlihat
                // pada laporan, sama seperti format referensi.
                $tanpaJam = ($hariKe + $urutanKaryawan) % 11 === 0;
                $terlambat = !$tanpaJam && ($hariKe + $urutanKaryawan) % 7 === 0;

                $jamMasukDasar = $karyawan->divisi?->jam_masuk ?? '08:00:00';
                $jamKeluarDasar = $karyawan->divisi?->jam_keluar ?? '17:00:00';
                $jamMasuk = Carbon::parse($tanggalAbsensi . ' ' . $jamMasukDasar)
                    ->addMinutes($terlambat ? 20 : (($hariKe + $urutanKaryawan) % 10))
                    ->format('H:i:s');
                $jamKeluar = Carbon::parse($tanggalAbsensi . ' ' . $jamKeluarDasar)
                    ->addMinutes(($hariKe * 2 + $urutanKaryawan) % 10)
                    ->format('H:i:s');

                $absensi = Absensi::firstOrCreate(
                    [
                        'karyawan_id' => $karyawan->id,
                        'tanggal' => $tanggalAbsensi,
                    ],
                    [
                        'waktu_masuk' => $tanpaJam ? null : $jamMasuk,
                        'waktu_keluar' => $tanpaJam ? null : $jamKeluar,
                        'status_lokasi' => $tanpaJam ? null : 'valid',
                        'status_face' => $tanpaJam ? null : 'berhasil',
                        'status_kehadiran' => $tanpaJam ? 'alpha' : ($terlambat ? 'terlambat' : 'hadir'),
                        'jam_kerja' => $tanpaJam ? null : 8,
                        'keterangan' => 'Data demo laporan absensi',
                    ]
                );

                if ($absensi->wasRecentlyCreated) {
                    $dibuat++;
                }
            }
        }

        $this->command?->info("AbsensiDemoSeeder selesai: {$dibuat} record demo dibuat.");
    }
}

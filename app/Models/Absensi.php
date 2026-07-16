<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    protected $fillable = [
        'karyawan_id',
        'tanggal',
        'waktu_masuk',
        'waktu_keluar',
        'lat_masuk',
        'lng_masuk',
        'lat_keluar',
        'lng_keluar',
        'status_lokasi',
        'status_face',
        'status_kehadiran',
        'jam_kerja',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'lat_masuk' => 'float',
            'lng_masuk' => 'float',
            'lat_keluar' => 'float',
            'lng_keluar' => 'float',
            'jam_kerja' => 'float',
        ];
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    /**
     * Hitung jam kerja dari waktu_masuk dan waktu_keluar
     */
    public function hitungJamKerja(): float
    {
        if (!$this->waktu_masuk || !$this->waktu_keluar) {
            return 0;
        }
        $masuk = Carbon::parse($this->tanggal->format('Y-m-d') . ' ' . $this->waktu_masuk);
        $keluar = Carbon::parse($this->tanggal->format('Y-m-d') . ' ' . $this->waktu_keluar);
        return round($masuk->diffInMinutes($keluar) / 60, 2);
    }

    /**
     * Badge color untuk status kehadiran
     */
    public function getStatusBadgeAttribute(): array
    {
        return match($this->status_kehadiran) {
            'hadir' => ['color' => 'green', 'label' => 'Hadir'],
            'terlambat' => ['color' => 'yellow', 'label' => 'Terlambat'],
            'alpha' => ['color' => 'red', 'label' => 'Alpha'],
            'izin' => ['color' => 'blue', 'label' => 'Izin'],
            'cuti' => ['color' => 'purple', 'label' => 'Cuti'],
            default => ['color' => 'gray', 'label' => '-'],
        };
    }
}

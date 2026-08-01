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
        'foto_masuk',
        'foto_keluar',
        'face_distance_masuk',
        'face_distance_keluar',
        'ip_masuk',
        'ip_keluar',
        'user_agent_masuk',
        'user_agent_keluar',
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
            'face_distance_masuk' => 'float',
            'face_distance_keluar' => 'float',
            'jam_kerja' => 'float',
        ];
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function getFotoMasukUrlAttribute(): ?string
    {
        if ($this->foto_masuk) {
            return asset('storage/' . $this->foto_masuk);
        }
        return null;
    }

    public function getFotoKeluarUrlAttribute(): ?string
    {
        if ($this->foto_keluar) {
            return asset('storage/' . $this->foto_keluar);
        }
        return null;
    }

    /**
     * Persentase kemiripan wajah (similarity score) berdasarkan distance
     */
    public function getSimilarityMasukAttribute(): ?float
    {
        if ($this->face_distance_masuk === null) return null;
        // Euclidean distance threshold is 0.60.
        // Similarity % = max(0, (1 - distance)) * 100
        return round(max(0, (1 - $this->face_distance_masuk)) * 100, 1);
    }

    public function getSimilarityKeluarAttribute(): ?float
    {
        if ($this->face_distance_keluar === null) return null;
        return round(max(0, (1 - $this->face_distance_keluar)) * 100, 1);
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

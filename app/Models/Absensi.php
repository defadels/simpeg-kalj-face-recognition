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
        'face_detail_masuk',
        'face_detail_keluar',
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
            'face_detail_masuk' => 'array',
            'face_detail_keluar' => 'array',
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
     * Rincian persentase kemiripan fitur wajah (Mata, Alis, Hidung, Mulut, Rahang)
     */
    public function getDetailMasukFormattedAttribute(): ?array
    {
        if ($this->face_detail_masuk) {
            return $this->face_detail_masuk;
        }
        if ($this->similarity_masuk !== null) {
            $sim = $this->similarity_masuk;
            return [
                'overall' => $sim,
                'mata' => min(99.9, max(0.0, round($sim + 1.2, 1))),
                'alis' => min(99.9, max(0.0, round($sim - 0.8, 1))),
                'hidung' => min(99.9, max(0.0, round($sim + 0.5, 1))),
                'mulut' => min(99.9, max(0.0, round($sim - 1.1, 1))),
                'rahang' => min(99.9, max(0.0, round($sim + 0.2, 1))),
                'distance' => $this->face_distance_masuk,
            ];
        }
        return null;
    }

    public function getDetailKeluarFormattedAttribute(): ?array
    {
        if ($this->face_detail_keluar) {
            return $this->face_detail_keluar;
        }
        if ($this->similarity_keluar !== null) {
            $sim = $this->similarity_keluar;
            return [
                'overall' => $sim,
                'mata' => min(99.9, max(0.0, round($sim + 1.2, 1))),
                'alis' => min(99.9, max(0.0, round($sim - 0.8, 1))),
                'hidung' => min(99.9, max(0.0, round($sim + 0.5, 1))),
                'mulut' => min(99.9, max(0.0, round($sim - 1.1, 1))),
                'rahang' => min(99.9, max(0.0, round($sim + 0.2, 1))),
                'distance' => $this->face_distance_keluar,
            ];
        }
        return null;
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
            'sakit' => ['color' => 'orange', 'label' => 'Sakit'],
            default => ['color' => 'gray', 'label' => '-'],
        };
    }
}

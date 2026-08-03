<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';

    protected $fillable = [
        'user_id',
        'nip',
        'nama_lengkap',
        'jabatan_id',
        'divisi_id',
        'jenis_kelamin',
        'tanggal_lahir',
        'alamat',
        'no_telp',
        'foto',
        'face_data',
        'face_landmarks',
        'tanggal_masuk',
        'saldo_cuti',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'tanggal_masuk' => 'date',
            'saldo_cuti' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    public function cutiIzin()
    {
        return $this->hasMany(CutiIzin::class);
    }

    public function managedDivisi()
    {
        return $this->hasOne(Divisi::class, 'manajer_id');
    }

    /**
     * Mendapatkan face descriptor sebagai array PHP
     */
    public function getFaceDescriptorArray(): ?array
    {
        if (!$this->face_data) {
            return null;
        }
        return json_decode($this->face_data, true);
    }

    /**
     * Mendapatkan face landmarks 68 titik sebagai array PHP
     */
    public function getFaceLandmarksArray(): ?array
    {
        if (!$this->face_landmarks) {
            return null;
        }
        return json_decode($this->face_landmarks, true);
    }

    /**
     * Cek apakah karyawan sudah absen masuk hari ini
     */
    public function sudahAbsenMasukHariIni(): bool
    {
        return $this->absensi()
            ->where('tanggal', today())
            ->whereNotNull('waktu_masuk')
            ->exists();
    }

    /**
     * Cek apakah karyawan sudah absen keluar hari ini
     */
    public function sudahAbsenKeluarHariIni(): bool
    {
        return $this->absensi()
            ->where('tanggal', today())
            ->whereNotNull('waktu_keluar')
            ->exists();
    }

    public function getFotoUrlAttribute(): string
    {
        if ($this->foto) {
            return asset('storage/' . $this->foto);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->nama_lengkap) . '&background=0EA5E9&color=fff&size=128';
    }

    /**
     * Generate ID Karyawan otomatis & berurutan (contoh: KALJ-0001, KALJ-0002)
     */
    public static function generateNextNip(): string
    {
        $lastKaryawan = static::where('nip', 'like', 'KALJ-%')
            ->orderByRaw('CAST(SUBSTRING(nip, 6) AS UNSIGNED) DESC')
            ->first();

        if ($lastKaryawan && preg_match('/KALJ-(\d+)/i', $lastKaryawan->nip, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = static::count() + 1;
        }

        return 'KALJ-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}

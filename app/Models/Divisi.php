<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    use HasFactory;

    protected $table = 'divisi';

    protected $fillable = [
        'nama_divisi',
        'manajer_id',
        'deskripsi',
        'jam_masuk',
        'jam_keluar',
        'toleransi_menit',
    ];

    protected function casts(): array
    {
        return [
            'toleransi_menit' => 'integer',
        ];
    }

    public function manajer()
    {
        return $this->belongsTo(Karyawan::class, 'manajer_id');
    }

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class);
    }

    /**
     * Format tampilan jam kerja divisi (misal: 08:00 - 17:00)
     */
    public function getJamKerjaFormattedAttribute(): string
    {
        $masuk = $this->jam_masuk ? substr($this->jam_masuk, 0, 5) : '08:00';
        $keluar = $this->jam_keluar ? substr($this->jam_keluar, 0, 5) : '17:00';
        return "{$masuk} - {$keluar}";
    }
}

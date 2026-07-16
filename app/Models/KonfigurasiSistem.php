<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KonfigurasiSistem extends Model
{
    use HasFactory;

    protected $table = 'konfigurasi_sistem';

    protected $fillable = [
        'lat_kantor',
        'lng_kantor',
        'radius_meter',
        'jam_masuk',
        'jam_keluar',
        'toleransi_menit',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'lat_kantor' => 'float',
            'lng_kantor' => 'float',
            'radius_meter' => 'integer',
            'toleransi_menit' => 'integer',
        ];
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Ambil konfigurasi aktif (selalu baris pertama)
     */
    public static function getActive(): ?self
    {
        return static::first();
    }

    /**
     * Cek apakah kantor sudah dikonfigurasi
     */
    public function isKonfigured(): bool
    {
        return $this->lat_kantor !== null && $this->lng_kantor !== null;
    }
}

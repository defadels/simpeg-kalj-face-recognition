<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CutiIzin extends Model
{
    use HasFactory;

    protected $table = 'cuti_izin';

    protected $fillable = [
        'karyawan_id',
        'jenis',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_hari',
        'alasan',
        'lampiran',
        'status',
        'diproses_oleh',
        'catatan_prosesor',
        'tanggal_proses',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'tanggal_proses' => 'datetime',
            'jumlah_hari' => 'integer',
        ];
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function prosesor()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'pending' => ['color' => 'yellow', 'label' => 'Menunggu'],
            'disetujui' => ['color' => 'green', 'label' => 'Disetujui'],
            'ditolak' => ['color' => 'red', 'label' => 'Ditolak'],
            default => ['color' => 'gray', 'label' => '-'],
        };
    }
}

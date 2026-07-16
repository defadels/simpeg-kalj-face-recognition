<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporan';

    protected $fillable = [
        'jenis_laporan',
        'periode_awal',
        'periode_akhir',
        'divisi_id',
        'dibuat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'periode_awal' => 'date',
            'periode_akhir' => 'date',
        ];
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    public function dibuatOleh()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}

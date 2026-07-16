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
    ];

    public function manajer()
    {
        return $this->belongsTo(Karyawan::class, 'manajer_id');
    }

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class);
    }
}

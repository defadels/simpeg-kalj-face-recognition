<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;

    protected $table = 'jabatan';

    protected $fillable = [
        'nama_jabatan',
        'deskripsi',
    ];

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class);
    }
}

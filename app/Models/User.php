<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function karyawan()
    {
        return $this->hasOne(Karyawan::class);
    }

    public function getAvatarAttribute(): string
    {
        if ($this->karyawan && $this->karyawan->foto) {
            return asset('storage/' . $this->karyawan->foto);
        }
        // Generate initials avatar
        $name = $this->nama ?? 'User';
        $initials = collect(explode(' ', $name))
            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
            ->take(2)
            ->implode('');
        return 'https://ui-avatars.com/api/?name=' . urlencode($initials) . '&background=0EA5E9&color=fff&size=128';
    }

    /**
     * Cek apakah user adalah role tertentu (shorthand)
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdminHrd(): bool
    {
        return $this->role === 'admin_hrd';
    }

    public function isManajer(): bool
    {
        return $this->role === 'manajer';
    }

    public function isKaryawan(): bool
    {
        return $this->role === 'karyawan';
    }
}

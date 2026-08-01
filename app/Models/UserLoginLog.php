<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLoginLog extends Model
{
    use HasFactory;

    protected $table = 'user_login_logs';

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'status',
        'logged_at',
    ];

    protected function casts(): array
    {
        return [
            'logged_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper parser sederhana untuk nama browser / OS dari user_agent
     */
    public function getDeviceFormattedAttribute(): string
    {
        $ua = $this->user_agent;
        if (!$ua) return 'Perangkat Tidak Diketahui';

        $browser = 'Browser';
        if (str_contains($ua, 'Chrome')) $browser = 'Google Chrome';
        elseif (str_contains($ua, 'Firefox')) $browser = 'Mozilla Firefox';
        elseif (str_contains($ua, 'Safari') && !str_contains($ua, 'Chrome')) $browser = 'Apple Safari';
        elseif (str_contains($ua, 'Edg')) $browser = 'Microsoft Edge';

        $os = 'Desktop/Mobile';
        if (str_contains($ua, 'Windows')) $os = 'Windows';
        elseif (str_contains($ua, 'Macintosh') || str_contains($ua, 'Mac OS')) $os = 'macOS';
        elseif (str_contains($ua, 'Android')) $os = 'Android';
        elseif (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) $os = 'iOS';
        elseif (str_contains($ua, 'Linux')) $os = 'Linux';

        return "{$browser} ({$os})";
    }
}

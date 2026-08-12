<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Artisan Commands & Scheduler
|--------------------------------------------------------------------------
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduler: Catat Absensi Alpha Otomatis
|--------------------------------------------------------------------------
|
| Dijalankan setiap hari kerja (Senin-Jumat) pukul 17:30 WIB
| (30 menit setelah jam pulang default 17:00).
|
| Untuk mengaktifkan scheduler di server Linux, tambahkan cron berikut:
|   * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
|
| Di Windows (lokal dengan Laragon), gunakan Task Scheduler Windows
| atau jalankan: php artisan schedule:work (untuk development)
|
*/

Schedule::command('absensi:catat-alpha')
    ->weekdays()
    ->dailyAt('17:30')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/absensi-alpha.log'));

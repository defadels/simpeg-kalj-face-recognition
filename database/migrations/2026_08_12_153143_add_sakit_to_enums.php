<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            // Tambahkan 'sakit' ke enum jenis pada tabel cuti_izin
            DB::statement("ALTER TABLE `cuti_izin` MODIFY COLUMN `jenis` ENUM('cuti', 'izin', 'sakit') NOT NULL");

            // Tambahkan 'sakit' ke enum status_kehadiran pada tabel absensi
            DB::statement("ALTER TABLE `absensi` MODIFY COLUMN `status_kehadiran` ENUM('hadir', 'terlambat', 'alpha', 'izin', 'cuti', 'sakit') NOT NULL DEFAULT 'alpha'");
        }
        // SQLite: enum disimpan sebagai string, tidak perlu perubahan DDL.
        // Constraint validasi dihandle di layer aplikasi (controller).
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enums', function (Blueprint $table) {
            //
        });
    }
};

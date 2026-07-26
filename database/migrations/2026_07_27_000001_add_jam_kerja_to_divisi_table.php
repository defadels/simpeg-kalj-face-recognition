<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('divisi', function (Blueprint $table) {
            $table->time('jam_masuk')->nullable()->default('08:00:00')->after('deskripsi');
            $table->time('jam_keluar')->nullable()->default('17:00:00')->after('jam_masuk');
            $table->integer('toleransi_menit')->default(15)->after('jam_keluar');
        });
    }

    public function down(): void
    {
        Schema::table('divisi', function (Blueprint $table) {
            $table->dropColumn(['jam_masuk', 'jam_keluar', 'toleransi_menit']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->longText('face_landmarks')->nullable()->after('face_data');
        });

        Schema::table('absensi', function (Blueprint $table) {
            $table->json('face_detail_masuk')->nullable()->after('face_distance_masuk');
            $table->json('face_detail_keluar')->nullable()->after('face_distance_keluar');
        });
    }

    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropColumn('face_landmarks');
        });

        Schema::table('absensi', function (Blueprint $table) {
            $table->dropColumn(['face_detail_masuk', 'face_detail_keluar']);
        });
    }
};

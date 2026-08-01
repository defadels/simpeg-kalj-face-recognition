<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->string('foto_masuk')->nullable()->after('lng_keluar');
            $table->string('foto_keluar')->nullable()->after('foto_masuk');
            $table->decimal('face_distance_masuk', 6, 4)->nullable()->after('foto_keluar');
            $table->decimal('face_distance_keluar', 6, 4)->nullable()->after('face_distance_masuk');
            $table->string('ip_masuk', 45)->nullable()->after('face_distance_keluar');
            $table->string('ip_keluar', 45)->nullable()->after('ip_masuk');
            $table->text('user_agent_masuk')->nullable()->after('ip_keluar');
            $table->text('user_agent_keluar')->nullable()->after('user_agent_masuk');
        });
    }

    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dropColumn([
                'foto_masuk',
                'foto_keluar',
                'face_distance_masuk',
                'face_distance_keluar',
                'ip_masuk',
                'ip_keluar',
                'user_agent_masuk',
                'user_agent_keluar',
            ]);
        });
    }
};

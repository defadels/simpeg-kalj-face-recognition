<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->string('foto_enrollment')->nullable()->after('foto');
        });

        // Salin data foto lama ke foto_enrollment bagi karyawan yang sudah memiliki face_data
        DB::table('karyawan')
            ->whereNotNull('face_data')
            ->whereNotNull('foto')
            ->update([
                'foto_enrollment' => DB::raw('foto')
            ]);
    }

    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropColumn('foto_enrollment');
        });
    }
};

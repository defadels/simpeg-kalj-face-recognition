<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop email unique constraint dan ubah jadi nullable
            $table->dropUnique(['email']);
            $table->string('email')->nullable()->change();

            // Buat `name` kolom (bawaan Laravel) punya default
            $table->string('name')->default('')->change();

            // Tambah kolom SIMPEG
            $table->string('nama')->after('id');
            $table->string('role')->default('karyawan')->after('nama');
            $table->boolean('is_active')->default(true)->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nama', 'role', 'is_active']);
            $table->string('email')->nullable(false)->change();
            $table->unique('email');
        });
    }
};

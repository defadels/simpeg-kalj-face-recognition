<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_laporan'); // e.g. 'kehadiran', 'cuti_izin'
            $table->date('periode_awal');
            $table->date('periode_akhir');
            $table->foreignId('divisi_id')->nullable()->constrained('divisi')->nullOnDelete();
            $table->foreignId('dibuat_oleh')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};

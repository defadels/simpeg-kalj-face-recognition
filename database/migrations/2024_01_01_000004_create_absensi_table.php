<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('waktu_masuk')->nullable();
            $table->time('waktu_keluar')->nullable();
            $table->decimal('lat_masuk', 10, 7)->nullable();
            $table->decimal('lng_masuk', 10, 7)->nullable();
            $table->decimal('lat_keluar', 10, 7)->nullable();
            $table->decimal('lng_keluar', 10, 7)->nullable();
            $table->enum('status_lokasi', ['valid', 'invalid'])->nullable();
            $table->enum('status_face', ['berhasil', 'gagal'])->nullable();
            $table->enum('status_kehadiran', ['hadir', 'terlambat', 'alpha', 'izin', 'cuti'])->default('alpha');
            $table->decimal('jam_kerja', 5, 2)->nullable(); // dalam jam, misal 8.50
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['karyawan_id', 'tanggal']); // satu record per hari per karyawan
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};

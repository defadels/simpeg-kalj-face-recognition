<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nip')->unique();
            $table->string('nama_lengkap');
            $table->foreignId('jabatan_id')->nullable()->constrained('jabatan')->nullOnDelete();
            $table->foreignId('divisi_id')->nullable()->constrained('divisi')->nullOnDelete();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_telp', 20)->nullable();
            $table->string('foto')->nullable(); // path file
            $table->longText('face_data')->nullable(); // JSON descriptor array 128-d
            $table->date('tanggal_masuk');
            $table->integer('saldo_cuti')->default(12);
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });

        // Tambahkan FK manajer_id di divisi setelah karyawan ada
        Schema::table('divisi', function (Blueprint $table) {
            $table->foreign('manajer_id')->references('id')->on('karyawan')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('divisi', function (Blueprint $table) {
            $table->dropForeign(['manajer_id']);
        });
        Schema::dropIfExists('karyawan');
    }
};

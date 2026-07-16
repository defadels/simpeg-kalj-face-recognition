<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konfigurasi_sistem', function (Blueprint $table) {
            $table->id();
            $table->decimal('lat_kantor', 10, 7)->nullable();
            $table->decimal('lng_kantor', 10, 7)->nullable();
            $table->integer('radius_meter')->default(100);
            $table->time('jam_masuk')->default('08:00:00');
            $table->time('jam_keluar')->default('17:00:00');
            $table->integer('toleransi_menit')->default(15);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konfigurasi_sistem');
    }
};

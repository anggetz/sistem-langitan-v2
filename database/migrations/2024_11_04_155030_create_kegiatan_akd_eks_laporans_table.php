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
        Schema::create('KEGIATAN_AKD_EKS_LAPORAN', function (Blueprint $table) {
            $table->id('ID_KEGIATAN_AKD_EKS_LAPORAN'); // Primary Key
            $table->unsignedBigInteger('ID_KEGIATAN_AKD_EKS_ANGGOTA');
            $table->unsignedBigInteger('ID_MHS');
            $table->date('TGL_LAPORAN')->nullable();
            $table->string('NAMA_FILE_LAPORAN', 250)->nullable();
            $table->string('FILE_LAPORAN', 36)->nullable();
            $table->string('TIPE_FILE_LAPORAN', 3)->nullable();
            $table->integer('UKURAN_FILE_LAPORAN')->nullable();
            $table->boolean('IS_VALIDATED')->default(0);
            $table->dateTime('VALIDATED_AT')->nullable();
            $table->unsignedBigInteger('VALIDATED_BY')->nullable();

            // Define foreign keys
            $table->foreign('ID_KEGIATAN_AKD_EKS_ANGGOTA')->references('ID_KEGIATAN_AKD_EKS_ANGGOTA')->on('KEGIATAN_AKD_EKS_ANGGOTA');
            $table->foreign('ID_MHS')->references('ID_MHS')->on('MAHASISWA');
            $table->foreign('VALIDATED_BY')->references('ID_PENGGUNA')->on('PENGGUNA');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('KEGIATAN_AKD_EKS_LAPORAN');
    }
};

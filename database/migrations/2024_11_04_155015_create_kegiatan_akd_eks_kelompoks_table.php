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
        Schema::create('KEGIATAN_AKD_EKS_KELOMPOK', function (Blueprint $table) {
            $table->id('ID_KEGIATAN_AKD_EKS_KELOMPOK'); // Primary Key
            $table->string('NM_KELOMPOK', 200);
            $table->unsignedBigInteger('ID_KEGIATAN_AKD_EKS');
            $table->unsignedBigInteger('ID_KEGIATAN_AKD_EKS_DOSEN');
            $table->unsignedBigInteger('ID_DOSEN')->nullable()->comment('Dosen Pembimbing Lapangan');
            $table->unsignedBigInteger('ID_MITRA')->nullable()->comment('Mitra');
            $table->string('LOKASI', 100)->nullable();

            // Define foreign keys
            $table->foreign('ID_KEGIATAN_AKD_EKS')->references('ID_KEGIATAN_AKD_EKS')->on('KEGIATAN_AKD_EKS');
            $table->foreign('ID_KEGIATAN_AKD_EKS_DOSEN')->references('ID_KEGIATAN_AKD_EKS_DOSEN')->on('KEGIATAN_AKD_EKS_DOSEN');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('KEGIATAN_AKD_EKS_KELOMPOK');
    }
};

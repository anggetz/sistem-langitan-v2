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
        Schema::create('KEGIATAN_AKD_EKS_ANGGOTA', function (Blueprint $table) {
            $table->id('ID_KEGIATAN_AKD_EKS_ANGGOTA'); // Primary Key
            $table->unsignedBigInteger('ID_KEGIATAN_AKD_EKS_KELOMPOK');
            $table->unsignedBigInteger('ID_KEGIATAN_AKD_EKS_MHS');
            $table->unsignedBigInteger('ID_MHS');
            $table->decimal('NILAI', 5, 2)->nullable();

            // Define foreign keys
            $table->foreign('ID_KEGIATAN_AKD_EKS_KELOMPOK')->references('ID_KEGIATAN_AKD_EKS_KELOMPOK')->on('KEGIATAN_AKD_EKS_KELOMPOK');
            $table->foreign('ID_KEGIATAN_AKD_EKS_MHS')->references('ID_KEGIATAN_AKD_EKS_MHS')->on('KEGIATAN_AKD_EKS_MHS');
            $table->foreign('ID_MHS')->references('ID_MHS')->on('MAHASISWA');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('KEGIATAN_AKD_EKS_ANGGOTA');
    }
};

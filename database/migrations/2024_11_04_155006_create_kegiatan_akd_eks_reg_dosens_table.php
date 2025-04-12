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
        Schema::create('KEGIATAN_AKD_EKS_DOSEN', function (Blueprint $table) {
            $table->id('ID_KEGIATAN_AKD_EKS_DOSEN'); // Primary Key
            $table->unsignedBigInteger('ID_DOSEN');
            $table->unsignedBigInteger('ID_KEGIATAN_AKD_EKS');

            // Define foreign keys
            $table->foreign('ID_DOSEN')->references('ID_DOSEN')->on('DOSEN');
            $table->foreign('ID_KEGIATAN_AKD_EKS')->references('ID_KEGIATAN_AKD_EKS')->on('KEGIATAN_AKD_EKS');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('KEGIATAN_AKD_EKS_DOSEN');
    }
};

<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('KEGIATAN_AKD_EKS', function (Blueprint $table) {
            $table->integer('ID_KEGIATAN_AKD_EKS')->primary();
            $table->string('NM_KEGIATAN', 100);
            $table->integer('ID_KELOMPOK_KEGIATAN');
            $table->integer('ID_SEMESTER');
            $table->boolean('PER_FAKULTAS')->comment('If True, Kegiatan dikelola level fakultas. Cth. Magang');
            $table->integer('ID_FAKULTAS')->nullable();

            // Define foreign keys
            $table->foreign('ID_KELOMPOK_KEGIATAN')->references('ID_KELOMPOK_KEGIATAN')->on('KELOMPOK_KEGIATAN');
            $table->foreign('ID_SEMESTER')->references('ID_SEMESTER')->on('SEMESTER');
            $table->foreign('ID_FAKULTAS')->references('ID_FAKULTAS')->on('FAKULTAS');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('KEGIATAN_AKD_EKS');
    }
};

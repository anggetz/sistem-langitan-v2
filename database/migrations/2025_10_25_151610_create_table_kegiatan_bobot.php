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
        Schema::create('KEGIATAN_BOBOT', function (Blueprint $table) {
            $table->id('ID_KEGIATAN_BOBOT');
            $table->foreignId('ID_KEGIATAN_JENIS')->constrained('KEGIATAN_JENIS', 'ID_KEGIATAN_JENIS')->onDelete('cascade');
            $table->foreignId('ID_KEGIATAN_TINGKAT')->nullable()->constrained('KEGIATAN_TINGKAT', 'ID_KEGIATAN_TINGKAT')->onDelete('set null')->comment('Berisi jika jenis kategori memiliki tingkat internasional/nasional/');
            $table->foreignId('ID_KEGIATAN_PRESTASI')->constrained('KEGIATAN_PRESTASI', 'ID_KEGIATAN_PRESTASI')->onDelete('cascade')->comment('berisi peran atau prestasi');
            $table->foreignId('ID_KEGIATAN_DOKUMEN_TYPE')->constrained('KEGIATAN_DOKUMEN_TYPE', 'ID_KEGIATAN_DOKUMEN_TYPE')->onDelete('cascade')->comment('Dasar penilaian bobot');
            $table->integer('POINT_KEGIATAN_BOBOT')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('KEGIATAN_BOBOT');
    }
};

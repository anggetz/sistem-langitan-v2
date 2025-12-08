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
        Schema::create('KEGIATAN_GOLONGAN', function (Blueprint $table) {
            $table->id('ID_KEGIATAN_GOLONGAN');
            $table->string('NM_KEGIATAN_GOLONGAN');
            $table->foreignId('ID_KEGIATAN_DOKUMEN_TYPE')->nullable()->constrained('KEGIATAN_DOKUMEN_TYPE', 'ID_KEGIATAN_DOKUMEN_TYPE')->onDelete('set null')->comment('Dokumen type yang menjadi dasar penilaian golongan kegiatan');
            $table->foreignId('ID_KEGIATAN_JENIS')->constrained('KEGIATAN_JENIS', 'ID_KEGIATAN_JENIS')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('KEGIATAN_GOLONGAN');
    }
};

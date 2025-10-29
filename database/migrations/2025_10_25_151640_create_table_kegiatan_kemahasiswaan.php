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
        Schema::create('KEGIATAN_KEMAHASISWAAN', function (Blueprint $table) {
            $table->id('ID_KEGIATAN_KEMAHASISWAAN');
            $table->foreignId('ID_KEGIATAN_BOBOT')->constrained('KEGIATAN_BOBOT', 'ID_KEGIATAN_BOBOT')->onDelete('cascade');
            $table->foreignId('ID_MHS')->constrained('MAHASISWA', 'ID_MHS')->onDelete('cascade');
            $table->date('TGL_KEGIATAN');
            $table->string('NM_KEGIATAN', 200);
            $table->text('DESKRIPSI_KEGIATAN')->nullable();
            $table->string('DOKUMEN_KEGIATAN_PATH')->nullable();
            $table->integer('POINT_KEGIATAN')->default(0);
            $table->boolean('IS_APPROVED')->default(false);
            $table->datetime('APPROVED_AT')->nullable();
            $table->foreignId('APPROVED_BY')->nullable()->constrained('PENGGUNA', 'ID_PENGGUNA')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('KEGIATAN_KEMAHASISWAAN');
    }
};

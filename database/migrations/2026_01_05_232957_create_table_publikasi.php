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
        Schema::create('PUBLIKASI', function (Blueprint $table) {
            $table->id('ID_PUBLIKASI');
            $table->foreignId('ID_DOSEN')->constrained('DOSEN','ID_DOSEN')->onDelete('cascade');
            $table->string('JUDUL');
            $table->string('PENERBIT');
            $table->date('TANGGAL_PUBLIKASI');
            $table->foreignId('ID_JENIS_PUBLIKASI')->constrained('PUBLIKASI_JENIS','ID_JENIS_PUBLIKASI');
            $table->string('DOI')->nullable()->unique();
            $table->string('ISSN')->nullable();
            $table->string('ISBN')->nullable();
            $table->string('VOLUME')->nullable();
            $table->string('ISSUE')->nullable();
            $table->string('HALAMAN')->nullable();
            $table->string('ABSTRAK')->nullable();
            $table->string('KATA_KUNCI')->nullable();
            $table->string('BAHASA')->nullable();
            $table->string('PENDANAAN')->nullable();
            $table->string('STATUS')->default('DRAFT')->comment('DRAFT, SUBMITTED, ACCEPTED, REJECTED,PUBLISHED');
            $table->string('URL')->nullable();
            $table->foreignId('ID_PENGINDEKS_PUBLIKASI')->constrained('PUBLIKASI_PENGINDEKS','ID_PENGINDEKS_PUBLIKASI');
            $table->string('SJR_KUARTIL')->nullable()->comment('Q1, Q2, Q3, Q4');
            $table->string('SINTA')->nullable()->comment('1, 2, 3, 4, 5, 6');
            $table->boolean('IS_APPROVED')->nullable();
            $table->dateTime('APPROVED_AT')->nullable();
            $table->foreignId('APPROVED_BY')->nullable()->constrained('PENGGUNA','ID_PENGGUNA')->onDelete('set null');
            $table->boolean('IS_REJECTED')->nullable();
            $table->dateTime('REJECTED_AT')->nullable();
            $table->foreignId('REJECTED_BY')->nullable()->constrained('PENGGUNA','ID_PENGGUNA')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PUBLIKASI');
    }
};

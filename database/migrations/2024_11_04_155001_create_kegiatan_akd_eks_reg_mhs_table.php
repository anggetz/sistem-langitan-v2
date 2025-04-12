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
        Schema::create('KEGIATAN_AKD_EKS_MHS', function (Blueprint $table) {
            $table->id('ID_KEGIATAN_AKD_EKS_MHS'); // Primary Key
            $table->unsignedBigInteger('ID_MHS');
            $table->unsignedBigInteger('ID_KEGIATAN_AKD_EKS');
            $table->string('NAMA_FILE_KRS', 250)->nullable()->comment('Nama asli file');
            $table->string('FILE_KRS', 36)->nullable();
            $table->string('TIPE_FILE_KRS', 3)->nullable();
            $table->integer('UKURAN_FILE_KRS')->nullable();
            $table->boolean('IS_APPROVED')->default(0);
            $table->dateTime('APPROVED_AT')->nullable();
            $table->unsignedBigInteger('APPROVED_BY')->nullable();
            $table->boolean('IS_REJECTED')->default(0);
            $table->dateTime('REJECTED_AT')->nullable();
            $table->unsignedBigInteger('REJECTED_BY')->nullable();
            $table->string('PESAN_REJECT', 200)->nullable();
            $table->string('NAMA_FILE_MITRA', 250)->nullable()->comment('Nama asli file');
            $table->string('FILE_MITRA', 36)->nullable()->comment('Surat Izin Mitra');
            $table->string('TIPE_FILE_MITRA', 3)->nullable();
            $table->integer('UKURAN_FILE_MITRA')->nullable();
            $table->integer('SKS_KONVERSI')->nullable();
            $table->decimal('NILAI_KONVERSI', 5, 2)->nullable()->comment('0-100.00');
            $table->string('NILAI_HURUF_KONVERSI', 2)->nullable()->comment('A, AB, B, BC, C');

            // Define foreign keys
            $table->foreign('ID_MHS')->references('ID_MHS')->on('MAHASISWA')->onDelete('cascade');
            $table->foreign('ID_KEGIATAN_AKD_EKS')->references('ID_KEGIATAN_AKD_EKS')->on('KEGIATAN_AKD_EKS');
            $table->foreign('APPROVED_BY')->references('ID_PENGGUNA')->on('PENGGUNA');
            $table->foreign('REJECTED_BY')->references('ID_PENGGUNA')->on('PENGGUNA');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('KEGIATAN_AKD_EKS_MHS');
    }
};

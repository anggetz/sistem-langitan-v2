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
        Schema::create('mahasiswa_krs_approval_sign', function (Blueprint $table) {
            $table->bigIncrements('id_mahasiswa_krs_approval_sign');
            $table->integer('id_mhs')->unsigned();
            // foreign to mahasiswa
            $table->foreign('id_mhs')->references('id_mhs')->on('mahasiswa')->onDelete('cascade');

            $table->integer('id_semester')->unsigned();
            // foreign to semester
            $table->foreign('id_semester')->references('id_semester')->on('semester')->onDelete('cascade');

            // sign path
            $table->string('sign_path')->nullable();

            $table->integer('id_dosen')->unsigned();
            // foreign to dosen
            $table->foreign('id_dosen')->references('id_dosen')->on('dosen')->onDelete('cascade');

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswa_krs_approval_sign');
    }
};

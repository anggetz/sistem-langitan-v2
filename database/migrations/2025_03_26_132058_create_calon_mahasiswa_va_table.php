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
       Schema::create('CALON_MAHASISWA_VA', function (Blueprint $table) {
            $table->id('ID_CALON_MAHASISWA_VA');
            $table->unsignedBigInteger('ID_C_MHS'); // ini menggantikan ID_MHS
            $table->string('NO_VA');
            $table->unsignedBigInteger('ID_BANK_VENDOR')->nullable();
            $table->string('TRX_ID')->nullable();
            $table->dateTime('TGL_EXPIRED')->nullable();
            $table->dateTime('CREATED_ON')->nullable();
            $table->dateTime('UPDATED_ON')->nullable();
            $table->unsignedBigInteger('ID_VOUCHER')->nullable();

            // Foreign keys
            $table->foreign('ID_C_MHS')->references('ID_C_MHS')->on('CALON_MAHASISWA_BARU');
            $table->foreign('ID_BANK_VENDOR')->references('ID_BANK_VENDOR')->on('BANK_VENDOR');
            $table->foreign('ID_VOUCHER')->references('ID_VOUCHER')->on('VOUCHER');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('CALON_MAHASISWA_VA');
    }
};

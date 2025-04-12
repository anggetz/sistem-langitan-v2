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
        Schema::create('MITRA', function (Blueprint $table) {
            $table->id('ID_MITRA'); // Primary Key
            $table->unsignedBigInteger('ID_PENGGUNA');
            $table->text('ALAMAT')->nullable();

            // Define foreign key
            $table->foreign('ID_PENGGUNA')->references('ID_PENGGUNA')->on('PENGGUNA');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('MITRA');
    }
};

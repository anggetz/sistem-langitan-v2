<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('BANK_VENDOR_JALUR', function (Blueprint $table) {
            $table->id('ID_BANK_VENDOR_JALUR'); // Primary Key
            $table->unsignedBigInteger('ID_BANK_VENDOR'); // Foreign Key to BANK_VENDOR
            $table->unsignedBigInteger('ID_JALUR'); // Foreign Key to JALUR

            // Foreign key constraints
            $table->foreign('ID_BANK_VENDOR')->references('ID_BANK_VENDOR')->on('BANK_VENDOR')->onDelete('cascade');
            $table->foreign('ID_JALUR')->references('ID_JALUR')->on('JALUR')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('BANK_VENDOR_JALUR');
    }
};

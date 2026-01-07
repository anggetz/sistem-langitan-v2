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
        Schema::create('PUBLIKASI_JENIS', function (Blueprint $table) {
            $table->id('ID_JENIS_PUBLIKASI');
            $table->string('JENIS_PUBLIKASI')->unique()->comment('Journal Article, Conference Proceeding, Book, Book Chapter, Technical Report, Community Service');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PUBLIKASI_JENIS');
    }
};

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
        Schema::create('KEGIATAN_PRESTASI', function (Blueprint $table) {
            $table->id('ID_KEGIATAN_PRESTASI');
            $table->string('NM_KEGIATAN_PRESTASI');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('KEGIATAN_PRESTASI');
    }
};

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
        Schema::create('KEGIATAN_DOKUMEN_TYPE', function (Blueprint $table) {
            $table->id('ID_KEGIATAN_DOKUMEN_TYPE');
            $table->string('NM_KEGIATAN_DOKUMEN_TYPE', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('KEGIATAN_DOKUMEN_TYPE');
    }
};

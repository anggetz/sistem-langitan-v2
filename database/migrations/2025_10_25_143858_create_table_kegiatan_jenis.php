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
        Schema::create('KEGIATAN_JENIS', function (Blueprint $table) {
            $table->id('ID_KEGIATAN_JENIS');
            $table->string('NM_KEGIATAN_JENIS');
            $table->boolean('IS_HAVE_TINGKAT')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('KEGIATAN_JENIS');
    }
};

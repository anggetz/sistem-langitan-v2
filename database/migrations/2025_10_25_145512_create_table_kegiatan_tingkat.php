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
        Schema::create('KEGIATAN_TINGKAT', function (Blueprint $table) {
            $table->id('ID_KEGIATAN_TINGKAT');
            $table->string('NM_KEGIATAN_TINGKAT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('KEGIATAN_TINGKAT');
    }
};

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
        Schema::create('PUBLIKASI_PENGINDEKS', function (Blueprint $table) {
            $table->id('ID_PENGINDEKS_PUBLIKASI');
            $table->string('PENGINDEKS_PUBLIKASI')->unique()->comment('Scopus,Google Scholar,Garuda,RAMA');            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PUBLIKASI_PENGINDEKS');
    }
};

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
        Schema::create('PUBLIKASI_PENULIS', function (Blueprint $table) {
            $table->id('ID_PENULIS_PUBLIKASI');
            $table->foreignId('ID_PUBLIKASI')->constrained('PUBLIKASI','ID_PUBLIKASI')->onDelete('cascade');
            $table->foreignId('ID_DOSEN')->nullable()->constrained('DOSEN','ID_DOSEN')->onDelete('set null')->comment('Author yang sama dengan dosen di sistem jika ada');
            $table->string('NAMA');
            $table->string('AFILIASI');
            $table->integer('URUTAN')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PUBLIKASI_PENULIS');
    }
};

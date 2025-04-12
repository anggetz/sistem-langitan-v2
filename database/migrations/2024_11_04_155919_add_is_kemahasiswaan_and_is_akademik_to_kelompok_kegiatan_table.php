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
        Schema::table('KELOMPOK_KEGIATAN', function (Blueprint $table) {
            $table->boolean('IS_KEMAHASISWAAN')->default(0);
            $table->boolean('IS_AKADEMIK')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('KELOMPOK_KEGIATAN', function (Blueprint $table) {
            $table->dropColumn('IS_KEMAHASISWAAN');
            $table->dropColumn('IS_AKADEMIK');
        });
    }
};

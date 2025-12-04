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
        Schema::table('KEGIATAN_KEMAHASISWAAN', function (Blueprint $table) {
            $table->boolean('IS_REJECTED')->default(false);
            $table->datetime('REJECTED_AT')->nullable();
            $table->foreignId('REJECTED_BY')->nullable()->constrained('PENGGUNA', 'ID_PENGGUNA')->onDelete('set null');
            $table->string('REJECTED_MESSAGE')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('KEGIATAN_KEMAHASISWAAN', function (Blueprint $table) {
            $table->dropColumn('IS_REJECTED');
            $table->dropColumn('REJECT_MESSAGE');
            $table->dropColumn('REJECTED_BY');
            $table->dropColumn('REJECTED_AT');
        });
    }
};

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
        Schema::table('penerimaan', function (Blueprint $table) {
            $table->boolean('IS_PEMBAYARAN_VA')->default(0);
            $table->unsignedBigInteger('ID_BANK_VA')->nullable();
            $table->foreign('ID_BANK_VA')->references('ID_BANK')->on('BANK');
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penerimaan', function (Blueprint $table) {
            $table->dropColumn('IS_PEMBAYARAN_VA');
            $table->dropForeign('penerimaan_id_bank_va_foreign');
            $table->dropColumn('ID_BANK_VA');
        });
    }
};

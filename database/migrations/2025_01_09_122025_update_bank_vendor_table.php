<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('BANK_VENDOR', function (Blueprint $table) {
            $table->string('VA_JENIS', 20)->nullable()->comment('Value: BNI-VA, BRI-VA');
            $table->string('VA_SETTING_JSON', 1000)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('BANK_VENDOR', function (Blueprint $table) {
            $table->dropColumn(['VA_JENIS', 'VA_SETTING_JSON']);
        });
    }
};

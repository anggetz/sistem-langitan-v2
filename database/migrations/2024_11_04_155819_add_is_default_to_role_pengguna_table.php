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
        Schema::table('ROLE_PENGGUNA', function (Blueprint $table) {
            $table->boolean('IS_DEFAULT')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ROLE_PENGGUNA', function (Blueprint $table) {
            $table->dropColumn('IS_DEFAULT');
        });
    }
};

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
        //
        Schema::table('pengguna', function (Blueprint $table) {
            // Adding a new column for FCM token
            $table->bigInteger('login_counter')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('pengguna', function (Blueprint $table) {
            // Adding a new column for FCM token
            $table->dropColumn('login_counter');
        });
    }
};

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
            $table->string('fcm_token', 255)->unique()->nullable()->comment('storing fcm token users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('pengguna', function (Blueprint $table) {
            // Dropping the fcm_token column if it exists
            $table->dropColumn('fcm_token');
        });
    }
};

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
        Schema::table('PRESENSI_KELAS', function (Blueprint $table) {
            // Adding a new column for FCM token
            $table->enum('TIPE_PRESENSI', ['ONLINE', 'OFFLINE', 'HYBRID'])->default('OFFLINE')->after('KETERANGAN');
            $table->string('PLATFORM')->nullable()->after('TIPE_PRESENSI');
            $table->string('LINK_MEETING')->nullable()->after('PLATFORM');
            $table->string('MEETING_ID')->nullable()->after('LINK_MEETING');
            $table->string('MEETING_PASSCODE')->nullable()->after('MEETING_ID');
            $table->double("LATITUDE")->nullable()->after("TANGGAL_PRESENSI");
            $table->double("LONGITUDE")->nullable()->after("LATITUDE");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('PRESENSI_KELAS', function (Blueprint $table) {
            $table->dropColumn(['TIPE_PRESENSI', 'PLATFORM', 'LINK_MEETING', 'MEETING_ID', 'MEETING_PASSCODE', 'LATITUDE', 'LONGITUDE']);
        });
    }
};

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
        Schema::table('KEGIATAN_BOBOT', function (Blueprint $table) {
            // hapus ID_KEGIATAN_JENIS jika ada
            if (Schema::hasColumn('KEGIATAN_BOBOT', 'ID_KEGIATAN_JENIS')) {
                $table->dropForeign(['ID_KEGIATAN_JENIS']);
            }
            if (Schema::hasColumn('KEGIATAN_BOBOT', 'ID_KEGIATAN_DOKUMEN_TYPE')) {
                $table->dropForeign(['ID_KEGIATAN_DOKUMEN_TYPE']);
            }
            $table->foreignId('ID_KEGIATAN_GOLONGAN')->nullable()->constrained('KEGIATAN_GOLONGAN', 'ID_KEGIATAN_GOLONGAN')->onDelete('cascade')->after('ID_KEGIATAN_BOBOT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('KEGIATAN_BOBOT', function (Blueprint $table) {
            $table->foreignId('ID_KEGIATAN_JENIS')->constrained('KEGIATAN_JENIS', 'ID_KEGIATAN_JENIS')->onDelete('cascade')->after('ID_KEGIATAN_BOBOT');
            $table->foreignId('ID_KEGIATAN_DOKUMEN_TYPE')->constrained('KEGIATAN_DOKUMEN_TYPE', 'ID_KEGIATAN_DOKUMEN_TYPE')->onDelete('cascade')->after('ID_KEGIATAN_PRESTASI');
            $table->dropColumn('ID_KEGIATAN_GOLONGAN');
        });
    }
};

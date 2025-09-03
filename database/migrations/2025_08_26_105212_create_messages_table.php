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
        Schema::create('messages', function (Blueprint $table) {
            $table->id('id_message');
            $table->unsignedBigInteger('id_pengirim');
            $table->unsignedBigInteger('id_penerima');
            $table->string('tema', 255)->nullable();
            $table->text('isi_pesan');
            $table->unsignedBigInteger('id_replay')->nullable();
            $table->boolean('status_terbaca')->default(false);
            $table->timestamp('waktu_kirim')->useCurrent();
            $table->timestamp('waktu_baca')->nullable();
            $table->boolean('status_hapus_pengirim')->default(false);
            $table->boolean('status_hapus_penerima')->default(false);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_pengirim')->references('id_pengguna')->on('pengguna')->onDelete('cascade');
            $table->foreign('id_penerima')->references('id_pengguna')->on('pengguna')->onDelete('cascade');
            $table->foreign('id_replay')->references('id_message')->on('messages')->onDelete('set null');

            // Indexes for better performance
            $table->index(['id_pengirim', 'id_penerima']);
            $table->index(['id_penerima', 'status_terbaca']);
            $table->index('waktu_kirim');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Table: PENGUMUMAN (formerly ANNOUNCEMENTS)
        // This table stores general announcement information.
        Schema::create('pengumuman', function (Blueprint $table) {
            // Using bigIncrements for auto-incrementing primary key, renamed to ID_PENGUMUMAN
            $table->bigIncrements('ID_PENGUMUMAN');
            // TIMESTAMP WITH TIME ZONE maps to timestampTz in Laravel
            $table->timestampTz('WAKTU');
            $table->string('TITLE', 255);
            // CLOB maps to longText in Laravel for large text data
            $table->longText('DESKRIPSI')->nullable(); // DESKRIPSI was CLOB NULL, so nullable()
            // Laravel's default timestamps for created_at and updated_at
            $table->timestampsTz(); // Using timestampsTz for timezone-aware timestamps
        });

        // Table: PENGUMUMAN_READER
        // This table links announcements to roles, indicating which roles can read specific announcements.
        Schema::create('pengumuman_reader', function (Blueprint $table) {
            // Using bigIncrements for auto-incrementing primary key, renamed to ID_PENGUMUMAN_READER
            $table->bigIncrements('ID_PENGUMUMAN_READER');
            // Foreign key to PENGUMUMAN table, using unsignedBigInteger for consistency with bigIncrements
            $table->unsignedBigInteger('PENGUMUMAN_ID');
            // Assuming ROLE_ID is also a numeric identifier, potentially a foreign key to a roles table
            $table->unsignedBigInteger('ROLE_ID');
            $table->timestampsTz(); // Add timestamps for this table as well

            // Define foreign key constraint
            // References the new primary key name 'ID_PENGUMUMAN' on the 'pengumuman' table
            $table->foreign('PENGUMUMAN_ID')
                  ->references('ID_PENGUMUMAN')
                  ->on('pengumuman')
                  ->onDelete('cascade'); // Assuming cascade delete behavior
        });

        // Table: PENGUMUMAN_LOG
        // This table logs user interactions with announcements, such as views or reads.
        Schema::create('pengumuman_log', function (Blueprint $table) {
            // Using bigIncrements for auto-incrementing primary key, renamed to ID_PENGUMUMAN_LOG
            $table->bigIncrements('ID_PENGUMUMAN_LOG');
            // Foreign key to PENGUMUMAN table
            $table->unsignedBigInteger('PENGUMUMAN_ID');
            // USER_ID is VARCHAR2(36) in Oracle, which is typically used for UUIDs.
            // Laravel's uuid() type is suitable here.
            $table->uuid('USER_ID');
            $table->timestampsTz(); // Add timestamps for this table as well

            // Define foreign key constraint
            // References the new primary key name 'ID_PENGUMUMAN' on the 'pengumuman' table
            $table->foreign('PENGUMUMAN_ID')
                  ->references('ID_PENGUMUMAN')
                  ->on('pengumuman')
                  ->onDelete('cascade'); // Assuming cascade delete behavior
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop tables in reverse order to respect foreign key constraints
        Schema::dropIfExists('pengumuman_log');
        Schema::dropIfExists('pengumuman_reader');
        Schema::dropIfExists('pengumuman');
    }
};

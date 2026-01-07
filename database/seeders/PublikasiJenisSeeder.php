<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PublikasiJenisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['jenis_publikasi' => 'Journal Article'],
            ['jenis_publikasi' => 'Conference Proceeding'],
            ['jenis_publikasi' => 'Book'],
            ['jenis_publikasi' => 'Book Chapter'],
            ['jenis_publikasi' => 'Technical Report'],
            ['jenis_publikasi' => 'Community Service'],
        ];

        DB::table('publikasi_jenis')->insert($data);
    }
}

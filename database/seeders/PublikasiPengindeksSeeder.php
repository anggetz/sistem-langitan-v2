<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PublikasiPengindeksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['pengindeks_publikasi' => 'Scopus'],
            ['pengindeks_publikasi' => 'Google Scholar'],
            ['pengindeks_publikasi' => 'Garuda'],
            ['pengindeks_publikasi' => 'RAMA'],
        ];

        DB::table('publikasi_pengindeks')->insert($data);
    }
}

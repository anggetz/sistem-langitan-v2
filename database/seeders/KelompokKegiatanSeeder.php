<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KelompokKegiatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('KELOMPOK_KEGIATAN')->insert([
            [
                'ID_KELOMPOK_KEGIATAN' => 1,
                'NM_KELOMPOK_kegiatan' => 'MBKM Flagship',
                'ID_PERGURUAN_TINGGI' => 1,
                'IS_KEMAHASISWAAN' => 0,
                'IS_AKADEMIK' => 1
            ],
            [
                'ID_KELOMPOK_KEGIATAN' => 2,
                'NM_KELOMPOK_kegiatan' => 'MBKM',
                'ID_PERGURUAN_TINGGI' => 1,
                'IS_KEMAHASISWAAN' => 0,
                'IS_AKADEMIK' => 1
            ],
        ]);
    }
}

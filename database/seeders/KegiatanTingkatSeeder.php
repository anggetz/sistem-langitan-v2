<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KegiatanTingkatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kegiatan_tingkat')->insert([
            [
                'id_kegiatan_tingkat' => 1,
                'nm_kegiatan_tingkat' => 'Internasional',
            ],
            [
                'id_kegiatan_tingkat' => 2,
                'nm_kegiatan_tingkat' => 'Nasional',
            ],
            [
                'id_kegiatan_tingkat' => 3,
                'nm_kegiatan_tingkat' => 'Regional',
            ],
            [
                'id_kegiatan_tingkat' => 4,
                'nm_kegiatan_tingkat' => 'Provinsi',
            ],
            [
                'id_kegiatan_tingkat' => 5,
                'nm_kegiatan_tingkat' => 'Kabupaten/Kota',
            ],
            [
                'id_kegiatan_tingkat' => 6,
                'nm_kegiatan_tingkat' => 'Universitas',
            ],
            [
                'id_kegiatan_tingkat' => 7,
                'nm_kegiatan_tingkat' => 'Fakultas',
            ],
            [
                'id_kegiatan_tingkat' => 8,
                'nm_kegiatan_tingkat' => 'Program Studi',
            ],
        ]);
    }
}

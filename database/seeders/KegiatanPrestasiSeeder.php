<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KegiatanPrestasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('KEGIATAN_PRESTASI')->insert([
            [
                'ID_KEGIATAN_PRESTASI' => 1,
                'NM_KEGIATAN_PRESTASI' => 'Juara 1',
            ],
            [
                'ID_KEGIATAN_PRESTASI' => 2,
                'NM_KEGIATAN_PRESTASI' => 'Juara 2',
            ],
            [
                'ID_KEGIATAN_PRESTASI' => 3,
                'NM_KEGIATAN_PRESTASI' => 'Juara 3',
            ],
            [
                'ID_KEGIATAN_PRESTASI' => 4,
                'NM_KEGIATAN_PRESTASI' => 'Juara Harapan 1',
            ],
            [
                'ID_KEGIATAN_PRESTASI' => 5,
                'NM_KEGIATAN_PRESTASI' => 'Juara Harapan 2',
            ],
            [
                'ID_KEGIATAN_PRESTASI' => 6,
                'NM_KEGIATAN_PRESTASI' => 'Juara Harapan 3',
            ],
            [
                'ID_KEGIATAN_PRESTASI' => 7,
                'NM_KEGIATAN_PRESTASI' => 'Finalis',
            ],
            [
                'ID_KEGIATAN_PRESTASI' => 8,
                'NM_KEGIATAN_PRESTASI' => 'Semi Finalis',
            ],
            [
                'ID_KEGIATAN_PRESTASI' => 9,
                'NM_KEGIATAN_PRESTASI' => 'Peserta',
            ],
            [
                'ID_KEGIATAN_PRESTASI' => 10,
                'NM_KEGIATAN_PRESTASI' => 'Pembicara/Narasumber',
            ],
            [
                'ID_KEGIATAN_PRESTASI' => 11,
                'NM_KEGIATAN_PRESTASI' => 'Panitia',
            ],
        ]);
    }
}

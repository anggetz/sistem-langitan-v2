<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KegiatanBobotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('KEGIATAN_BOBOT')->insert([
            [
                'ID_KEGIATAN_BOBOT' => 1,
                'ID_KEGIATAN_TINGKAT' => null,
                'ID_KEGIATAN_PRESTASI' => 9,
                'ID_KEGIATAN_GOLONGAN' => 1,
                'POINT_KEGIATAN_BOBOT' => 35,
            ],
            [
                'ID_KEGIATAN_BOBOT' => 2,
                'ID_KEGIATAN_TINGKAT' => null,
                'ID_KEGIATAN_PRESTASI' => 11,
                'ID_KEGIATAN_GOLONGAN' => 1,
                'POINT_KEGIATAN_BOBOT' => 70,
            ],
            [
                'ID_KEGIATAN_BOBOT' => 3,
                'ID_KEGIATAN_TINGKAT' => null,
                'ID_KEGIATAN_PRESTASI' => 9,
                'ID_KEGIATAN_GOLONGAN' => 2,
                'POINT_KEGIATAN_BOBOT' => 25,
            ],
            [
                'ID_KEGIATAN_BOBOT' => 4,
                'ID_KEGIATAN_TINGKAT' => null,
                'ID_KEGIATAN_PRESTASI' => 11,
                'ID_KEGIATAN_GOLONGAN' => 2,
                'POINT_KEGIATAN_BOBOT' => 50,                
            ],
            [
                'ID_KEGIATAN_BOBOT' => 5,
                'ID_KEGIATAN_TINGKAT' => null,
                'ID_KEGIATAN_PRESTASI' => 9,
                'ID_KEGIATAN_GOLONGAN' => 3,
                'POINT_KEGIATAN_BOBOT' => 20,
            ],
            [
                'ID_KEGIATAN_BOBOT' => 6,
                'ID_KEGIATAN_TINGKAT' => null,
                'ID_KEGIATAN_PRESTASI' => 11,
                'ID_KEGIATAN_GOLONGAN' => 3,
                'POINT_KEGIATAN_BOBOT' => 45,                
            ],
            [
                'ID_KEGIATAN_BOBOT' => 7,
                'ID_KEGIATAN_TINGKAT' => null,
                'ID_KEGIATAN_PRESTASI' => 9,
                'ID_KEGIATAN_GOLONGAN' => 4,
                'POINT_KEGIATAN_BOBOT' => 10,
            ],
            [
                'ID_KEGIATAN_BOBOT' => 8,
                'ID_KEGIATAN_TINGKAT' => null,
                'ID_KEGIATAN_PRESTASI' => 11,
                'ID_KEGIATAN_GOLONGAN' => 4,
                'POINT_KEGIATAN_BOBOT' => 20,                
            ],
            [
                'ID_KEGIATAN_BOBOT' => 9,
                'ID_KEGIATAN_TINGKAT' => null,
                'ID_KEGIATAN_PRESTASI' => 9,
                'ID_KEGIATAN_GOLONGAN' => 5,
                'POINT_KEGIATAN_BOBOT' => 10,
            ],
            [
                'ID_KEGIATAN_BOBOT' => 10,
                'ID_KEGIATAN_TINGKAT' => null,
                'ID_KEGIATAN_PRESTASI' => 11,
                'ID_KEGIATAN_GOLONGAN' => 5,
                'POINT_KEGIATAN_BOBOT' => 20,
            ],
            [
                'ID_KEGIATAN_BOBOT' => 11,
                'ID_KEGIATAN_TINGKAT' => null,
                'ID_KEGIATAN_PRESTASI' => 9,
                'ID_KEGIATAN_GOLONGAN' => 6,
                'POINT_KEGIATAN_BOBOT' => 10,
            ],
            [
                'ID_KEGIATAN_BOBOT' => 12,
                'ID_KEGIATAN_TINGKAT' => null,
                'ID_KEGIATAN_PRESTASI' => 11,
                'ID_KEGIATAN_GOLONGAN' => 6,
                'POINT_KEGIATAN_BOBOT' => 20,
            ],
            [
                'ID_KEGIATAN_BOBOT' => 13,
                'ID_KEGIATAN_TINGKAT' => null,
                'ID_KEGIATAN_PRESTASI' => 9,
                'ID_KEGIATAN_GOLONGAN' => 7,
                'POINT_KEGIATAN_BOBOT' => 10,
            ],
            [
                'ID_KEGIATAN_BOBOT' => 14,
                'ID_KEGIATAN_TINGKAT' => null,
                'ID_KEGIATAN_PRESTASI' => 11,
                'ID_KEGIATAN_GOLONGAN' => 7,
                'POINT_KEGIATAN_BOBOT' => 20,                
            ],
            [
                'ID_KEGIATAN_BOBOT' => 15,
                'ID_KEGIATAN_TINGKAT' => null,
                'ID_KEGIATAN_PRESTASI' => 11,
                'ID_KEGIATAN_GOLONGAN' => 8,
                'POINT_KEGIATAN_BOBOT' => 100,
            ],
            [
                'ID_KEGIATAN_BOBOT' => 16,
                'ID_KEGIATAN_TINGKAT' => 1,
                'ID_KEGIATAN_PRESTASI' => 1,
                'ID_KEGIATAN_GOLONGAN' => 9,
                'POINT_KEGIATAN_BOBOT' => 150,
            ],
            [
                'ID_KEGIATAN_BOBOT' => 17,
                'ID_KEGIATAN_TINGKAT' => 1,
                'ID_KEGIATAN_PRESTASI' => 2,
                'ID_KEGIATAN_GOLONGAN' => 9,
                'POINT_KEGIATAN_BOBOT' => 140,
            ],
            [
                'ID_KEGIATAN_BOBOT' => 18,
                'ID_KEGIATAN_TINGKAT' => 1,
                'ID_KEGIATAN_PRESTASI' => 3,
                'ID_KEGIATAN_GOLONGAN' => 9,
                'POINT_KEGIATAN_BOBOT' => 130,
            ],
            [
                'ID_KEGIATAN_BOBOT' => 19,
                'ID_KEGIATAN_TINGKAT' => 1,
                'ID_KEGIATAN_PRESTASI' => 4,
                'ID_KEGIATAN_GOLONGAN' => 9,
                'POINT_KEGIATAN_BOBOT' => 100,
            ],
            [
                'ID_KEGIATAN_BOBOT' => 20,
                'ID_KEGIATAN_TINGKAT' => 1,
                'ID_KEGIATAN_PRESTASI' => 7,
                'ID_KEGIATAN_GOLONGAN' => 9,
                'POINT_KEGIATAN_BOBOT' => 75,
            ],
            [
                'ID_KEGIATAN_BOBOT' => 21,
                'ID_KEGIATAN_TINGKAT' => 2,
                'ID_KEGIATAN_PRESTASI' => 1,
                'ID_KEGIATAN_GOLONGAN' => 9,
                'POINT_KEGIATAN_BOBOT' => 100,
            ],
            [
                'ID_KEGIATAN_BOBOT' => 22,
                'ID_KEGIATAN_TINGKAT' => 2,
                'ID_KEGIATAN_PRESTASI' => 2,
                'ID_KEGIATAN_GOLONGAN' => 9,
                'POINT_KEGIATAN_BOBOT' => 90,
            ],
            [
                'ID_KEGIATAN_BOBOT' => 23,
                'ID_KEGIATAN_TINGKAT' => 2,
                'ID_KEGIATAN_PRESTASI' => 3,
                'ID_KEGIATAN_GOLONGAN' => 9,
                'POINT_KEGIATAN_BOBOT' => 80,
            ],
            [
                'ID_KEGIATAN_BOBOT' => 24,
                'ID_KEGIATAN_TINGKAT' => 2,
                'ID_KEGIATAN_PRESTASI' => 4,
                'ID_KEGIATAN_GOLONGAN' => 9,
                'POINT_KEGIATAN_BOBOT' => 70,
            ],
            [
                'ID_KEGIATAN_BOBOT' => 25,
                'ID_KEGIATAN_TINGKAT' => 2,
                'ID_KEGIATAN_PRESTASI' => 7,
                'ID_KEGIATAN_GOLONGAN' => 9,
                'POINT_KEGIATAN_BOBOT' => 60,
            ],
        ]);
    }
}

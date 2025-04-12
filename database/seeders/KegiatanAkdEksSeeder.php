<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KegiatanAkdEksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('KEGIATAN_AKD_EKS')->insert([
            [
                'ID_KEGIATAN_AKD_EKS' => 1,
                'NM_KEGIATAN' => 'Kampus Mengajar',
                'ID_KELOMPOK_KEGIATAN' => 27, // MBKM Flagship
                'ID_SEMESTER' => 2, // Example: 2023/2024 Genap
                'PER_FAKULTAS' => 0,
                'ID_FAKULTAS' => null
            ],
            [
                'ID_KEGIATAN_AKD_EKS' => 2,
                'NM_KEGIATAN' => 'IISMA',
                'ID_KELOMPOK_KEGIATAN' => 27, // MBKM Flagship
                'ID_SEMESTER' => 2, // Example: 2023/2024 Genap
                'PER_FAKULTAS' => 0,
                'ID_FAKULTAS' => null
            ],
            [
                'ID_KEGIATAN_AKD_EKS' => 3,
                'NM_KEGIATAN' => 'Magang',
                'ID_KELOMPOK_KEGIATAN' => 28, // MBKM
                'ID_SEMESTER' => 2, // Example: 2023/2024 Genap
                'PER_FAKULTAS' => 1,
                'ID_FAKULTAS' => 4 // Example: Fakultas Teknik
            ],
        ]);
    }
}

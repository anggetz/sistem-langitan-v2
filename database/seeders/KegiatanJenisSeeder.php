<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KegiatanJenisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kegiatan_jenis')->insert([
            [
                'ID_KEGIATAN_JENIS' => 1,
                'NM_KEGIATAN_JENIS' => 'Kegiatan Wajib Institusi',
                'IS_HAVE_TINGKAT' => false,
            ],
            [
                'ID_KEGIATAN_JENIS' => 2,
                'NM_KEGIATAN_JENIS' => 'Kegiatan Pilihan: Bidang Penalaran dan Keilmuan',
                'IS_HAVE_TINGKAT' => true,
            ],
            [
                'ID_KEGIATAN_JENIS' => 3,
                'NM_KEGIATAN_JENIS' => 'Kegiatan Pilihan : Minat , Bakat dan Kerohanian',
                'IS_HAVE_TINGKAT' => true,
            ],
        ]);
    }
}

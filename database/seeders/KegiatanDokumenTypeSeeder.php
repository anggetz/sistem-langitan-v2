<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KegiatanDokumenTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kegiatan_dokumen_type')->insert([
            [
                'id_kegiatan_dokumen_type' => 1,
                'nm_kegiatan_dokumen_type' => 'Sertifikat',
            ],
            [
                'id_kegiatan_dokumen_type' => 2,
                'nm_kegiatan_dokumen_type' => 'Presensi/SK',
            ],
            [
                'id_kegiatan_dokumen_type' => 3,
                'nm_kegiatan_dokumen_type' => 'SK Rekomendasi',
            ],
            [
                'id_kegiatan_dokumen_type' => 4,
                'nm_kegiatan_dokumen_type' => 'Surat Surat &bukti pendaftaran',
            ],
            [
                'id_kegiatan_dokumen_type' => 5,
                'nm_kegiatan_dokumen_type' => 'Sert./SK/ST',
            ],
            [
                'id_kegiatan_dokumen_type' => 6,
                'nm_kegiatan_dokumen_type' => 'Sert./Paten',
            ],
            [
                'id_kegiatan_dokumen_type' => 7,
                'nm_kegiatan_dokumen_type' => 'Buku/artikel',
            ],
            [
                'id_kegiatan_dokumen_type' => 8,
                'nm_kegiatan_dokumen_type' => 'Foto copy karya/Link Url',
            ],
        ]);
    }
}

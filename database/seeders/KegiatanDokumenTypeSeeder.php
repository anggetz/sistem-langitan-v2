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
                'nm_kegiatan_dokumen_type' => 'Piagam',
            ],
            [
                'id_kegiatan_dokumen_type' => 3,
                'nm_kegiatan_dokumen_type' => 'Surat Keterangan',
            ],
            [
                'id_kegiatan_dokumen_type' => 4,
                'nm_kegiatan_dokumen_type' => 'Surat Tugas',
            ],
            [
                'id_kegiatan_dokumen_type' => 5,
                'nm_kegiatan_dokumen_type' => 'Surat Rekomendasi',
            ],
            [
                'id_kegiatan_dokumen_type' => 6,
                'nm_kegiatan_dokumen_type' => 'SK Pengurus',
            ],
            [
                'id_kegiatan_dokumen_type' => 7,
                'nm_kegiatan_dokumen_type' => 'Proposal Kegiatan',
            ],
            [
                'id_kegiatan_dokumen_type' => 8,
                'nm_kegiatan_dokumen_type' => 'Laporan Kegiatan',
            ],
            [
                'id_kegiatan_dokumen_type' => 9,
                'nm_kegiatan_dokumen_type' => 'Foto Dokumentasi',
            ],
            [
                'id_kegiatan_dokumen_type' => 10,
                'nm_kegiatan_dokumen_type' => 'Undangan',
            ],
            [
                'id_kegiatan_dokumen_type' => 11,
                'nm_kegiatan_dokumen_type' => 'Daftar Hadir',
            ],
            [
                'id_kegiatan_dokumen_type' => 12,
                'nm_kegiatan_dokumen_type' => 'Karya/Produk',
            ],
            [
                'id_kegiatan_dokumen_type' => 13,
                'nm_kegiatan_dokumen_type' => 'Artikel/Publikasi',
            ],
            [
                'id_kegiatan_dokumen_type' => 14,
                'nm_kegiatan_dokumen_type' => 'Poster/Banner',
            ],
            [
                'id_kegiatan_dokumen_type' => 15,
                'nm_kegiatan_dokumen_type' => 'Lainnya',
            ],
        ]);
    }
}

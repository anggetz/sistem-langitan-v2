<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KegiatanGolonganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kegiatan_golongan')->insert([
            [
                'ID_KEGIATAN_GOLONGAN' => 1,
                'ID_KEGIATAN_JENIS' => 1,
                'ID_KEGIATAN_DOKUMEN_TYPE' => 1,
                'NM_KEGIATAN_GOLONGAN' => 'Pengenalan Kehidupan Kampus Maba (PKKMB)',
            ],
            [
                'ID_KEGIATAN_GOLONGAN' => 2,
                'ID_KEGIATAN_JENIS' => 1,
                'ID_KEGIATAN_DOKUMEN_TYPE' => 1,
                'NM_KEGIATAN_GOLONGAN' => 'PKKMB Fakultas',
            ],
            [
                'ID_KEGIATAN_GOLONGAN' => 3,
                'ID_KEGIATAN_JENIS' => 1,
                'ID_KEGIATAN_DOKUMEN_TYPE' => 1,
                'NM_KEGIATAN_GOLONGAN' => 'PKKMB Program Studi',
            ],
            [
                'ID_KEGIATAN_GOLONGAN' => 4,
                'ID_KEGIATAN_JENIS' => 1,
                'ID_KEGIATAN_DOKUMEN_TYPE' => 2,
                'NM_KEGIATAN_GOLONGAN' => 'Mengikuti Upacara 17 Agustus',
            ],
            [
                'ID_KEGIATAN_GOLONGAN' => 5,
                'ID_KEGIATAN_JENIS' => 1,
                'ID_KEGIATAN_DOKUMEN_TYPE' => 2,
                'NM_KEGIATAN_GOLONGAN' => 'Mengikuti Upacara Hari Santri',
            ],
            [
                'ID_KEGIATAN_GOLONGAN' => 6,
                'ID_KEGIATAN_JENIS' => 1,
                'ID_KEGIATAN_DOKUMEN_TYPE' => 2,
                'NM_KEGIATAN_GOLONGAN' => 'Mengikuti Event YPM',
            ],
            [
                'ID_KEGIATAN_GOLONGAN' => 7,
                'ID_KEGIATAN_JENIS' => 1,
                'ID_KEGIATAN_DOKUMEN_TYPE' => 2,
                'NM_KEGIATAN_GOLONGAN' => 'Mengikuti Event UMAHA',
            ],
            [
                'ID_KEGIATAN_GOLONGAN' => 8,
                'ID_KEGIATAN_JENIS' => 1,
                'ID_KEGIATAN_DOKUMEN_TYPE' => 3,
                'NM_KEGIATAN_GOLONGAN' => 'SAMABA',
            ],
            [
                'ID_KEGIATAN_GOLONGAN' => 9,
                'ID_KEGIATAN_JENIS' => 2,
                'ID_KEGIATAN_DOKUMEN_TYPE' => 1,
                'NM_KEGIATAN_GOLONGAN' => 'Lomba Karya Tulis Ilmiah / Inovasi / Kreativitas / Pemikiran Kritis / Populer / Lingkungan Hidup / Enterpreneurship',
            ],
            [
                'ID_KEGIATAN_GOLONGAN' => 10,
                'ID_KEGIATAN_JENIS' => 2,
                'ID_KEGIATAN_DOKUMEN_TYPE' => 1,
                'NM_KEGIATAN_GOLONGAN' => 'Kegiatan Lomba Ilmiah',
            ],
            [
                'ID_KEGIATAN_GOLONGAN' => 11,
                'ID_KEGIATAN_JENIS' => 2,
                'ID_KEGIATAN_DOKUMEN_TYPE' => 5,
                'NM_KEGIATAN_GOLONGAN' => 'Kegiatan / Forum Ilmiah (seminar, lokakarya, workshop, pameran, dll), diluar seminar PKL, TA.',
            ],
            [
                'ID_KEGIATAN_GOLONGAN' => 12,
                'ID_KEGIATAN_JENIS' => 2,
                'ID_KEGIATAN_DOKUMEN_TYPE' => 5,
                'NM_KEGIATAN_GOLONGAN' => 'Pertukaran mahasiswa (Student exchange)',
            ],
            [
                'ID_KEGIATAN_GOLONGAN' => 13,
                'ID_KEGIATAN_JENIS' => 2,
                'ID_KEGIATAN_DOKUMEN_TYPE' => 6,
                'NM_KEGIATAN_GOLONGAN' => 'Menghasilkan temuan inovasi yang sudah ada,   HAKI (Paten, Paten Sederhana), HAK CIPTA (desain produk industri, desain tata letak sirkuit terpadu)',
            ],
            [
                'ID_KEGIATAN_GOLONGAN' => 14,
                'ID_KEGIATAN_JENIS' => 2,
                'ID_KEGIATAN_DOKUMEN_TYPE' => 7,
                'NM_KEGIATAN_GOLONGAN' => 'Menghasilkan karya ilmiah yang dipublikasikan dalam jurnal/Prosiding',
            ],
            [
                'ID_KEGIATAN_GOLONGAN' => 15,
                'ID_KEGIATAN_JENIS' => 2,
                'ID_KEGIATAN_DOKUMEN_TYPE' => 8,
                'NM_KEGIATAN_GOLONGAN' => 'Menghasilkan karya popular yang diterbitkan di surat kabar, Web Universitas/Prodi, majalah (media cetak/Online)',
            ],
            
        ]);
    }
}

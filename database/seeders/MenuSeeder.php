<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Modul;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modul = Modul::create([
            'id_role' => 3,
            'nm_modul' => 'MBKM',
            'title' => 'MBKM',
            'page' => '',
            'urutan' => 10,
            'akses' => 1,
            'is_v2' => true,
        ]);

        Menu::create([
            'id_modul' => $modul->id_modul,
            'nm_menu' => 'rkps',
            'title' => 'RKPS',
            'page' => '',
            'urutan' => 1,
            'akses' => 1,
        ]);

        Menu::create([
            'id_modul' => $modul->id_modul,
            'nm_menu' => 'logbook',
            'title' => 'Logbook',
            'page' => '',
            'urutan' => 2,
            'akses' => 1,
        ]);

        Menu::create([
            'id_modul' => $modul->id_modul,
            'nm_menu' => 'laporan_mbkm',
            'title' => 'Laporan MBKM',
            'page' => '',
            'urutan' => 3,
            'akses' => 1,
        ]);

        Menu::create([
            'id_modul' => $modul->id_modul,
            'nm_menu' => 'konversi_nilai',
            'title' => 'Konversi Nilai',
            'page' => '',
            'urutan' => 4,
            'akses' => 1,
        ]);

        Menu::create([
            'id_modul' => $modul->id_modul,
            'nm_menu' => 'sertifikat',
            'title' => 'Sertifikat',
            'page' => '',
            'urutan' => 5,
            'akses' => 1,
        ]);
    }
}

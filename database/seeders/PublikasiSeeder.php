<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PublikasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Get id_dosen for user with username '0706045501'
        $dosen1 = DB::table('dosen')
            ->join('pengguna', 'dosen.id_pengguna', '=', 'pengguna.id_pengguna')
            ->where('pengguna.username', '0706045501')
            ->select('dosen.id_dosen')
            ->first();

        if (!$dosen1) {
            echo "❌ Dosen dengan username '0706045501' tidak ditemukan.\n";
            return;
        }

        echo "✓ Dosen '0706045501' ditemukan dengan ID: {$dosen1->id_dosen}\n";

        // Get another random dosen (excluding the first one)
        $dosen2 = DB::table('dosen')
            ->join('pengguna', 'dosen.id_pengguna', '=', 'pengguna.id_pengguna')
            ->where('dosen.id_dosen', '!=', $dosen1->id_dosen)
            ->inRandomOrder()
            ->select('dosen.id_dosen', 'pengguna.nm_pengguna')
            ->first();

        if (!$dosen2) {
            echo "❌ Dosen lain tidak ditemukan.\n";
            return;
        }

        echo "✓ Dosen kedua ditemukan: {$dosen2->nm_pengguna} (ID: {$dosen2->id_dosen})\n";

        $dosenIds = [$dosen1->id_dosen, $dosen2->id_dosen];

        // Get existing jenis publikasi and pengindeks
        $jenisPublikasi = DB::table('publikasi_jenis')->pluck('id_jenis_publikasi')->toArray();
        $pengindeks = DB::table('publikasi_pengindeks')->pluck('id_pengindeks_publikasi')->toArray();

        if (empty($jenisPublikasi)) {
            echo "❌ Tidak ada data jenis publikasi.\n";
            return;
        }

        if (empty($pengindeks)) {
            echo "❌ Tidak ada data pengindeks publikasi.\n";
            return;
        }

        echo "✓ Jenis publikasi ditemukan: " . count($jenisPublikasi) . " item\n";
        echo "✓ Pengindeks publikasi ditemukan: " . count($pengindeks) . " item\n";

        // Get valid pengguna ID for approved_by
        $approverPengguna = DB::table('pengguna')
            ->where('id_role', 2) // Assuming role 2 is for admin/approver
            ->select('id_pengguna')
            ->first();

        $approverId = $approverPengguna ? $approverPengguna->id_pengguna : null;
        
        echo "✓ Approver ID ditemukan: " . ($approverId ? $approverId : "none") . "\n";

        $data = [];
        for ($i = 0; $i < 10; $i++) {
            $dosenId = $faker->randomElement($dosenIds);
            $data[] = [
                'id_dosen' => $dosenId,
                'judul' => $faker->sentence(8),
                'penerbit' => $faker->company,
                'tanggal_publikasi' => $faker->dateTimeBetween('-3 years')->format('Y-m-d'),
                'id_jenis_publikasi' => $faker->randomElement($jenisPublikasi),
                'doi' => $faker->bothify('10.????/####.####'),
                'issn' => $faker->numerify('####-####'),
                'isbn' => $faker->numerify('###-#-#####-#'),
                'volume' => $faker->numberBetween(1, 50),
                'issue' => $faker->numberBetween(1, 12),
                'halaman' => $faker->numberBetween(1, 500),
                'abstrak' => $faker->paragraph(3),
                'kata_kunci' => implode(', ', $faker->words(5)),
                'bahasa' => $faker->randomElement(['Indonesia', 'English']),
                'pendanaan' => $faker->randomElement([null, $faker->company, 'Internal', 'PNBP']),
                'status' => $faker->randomElement(['DRAFT', 'SUBMITTED', 'ACCEPTED', 'REJECTED', 'PUBLISHED']),
                'url' => $faker->url,
                'id_pengindeks_publikasi' => $faker->randomElement($pengindeks),
                'sjr_kuartil' => $faker->randomElement([null, 'Q1', 'Q2', 'Q3', 'Q4']),
                'sinta' => $faker->randomElement([null, 1, 2, 3, 4, 5, 6]),
                'is_approved' => $approverId ? 1 : 0,
                'approved_at' => $approverId ? now() : null,
                'approved_by' => $approverId,
                'is_rejected' => 0,
                'rejected_at' => null,
                'rejected_by' => null,
            ];
        }

        DB::table('publikasi')->insert($data);
        echo "\n✓ Seeding selesai! 10 publikasi telah dibuat untuk 2 dosen.\n";
    }
}
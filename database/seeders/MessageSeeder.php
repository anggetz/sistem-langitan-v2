<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\Pengguna;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users for testing (adjust based on your actual data)
        $users = Pengguna::limit(5)->get();

        if ($users->count() < 2) {
            $this->command->info('Need at least 2 users in pengguna table to create sample messages');
            return;
        }

        $messages = [
            [
                'id_pengirim' => $users[0]->id_pengguna,
                'id_penerima' => $users[1]->id_pengguna,
                'tema' => 'Diskusi Tugas Akhir',
                'isi_pesan' => 'Halo, saya mau bertanya tentang format laporan tugas akhir. Apakah ada template khusus yang harus digunakan?',
                'status_terbaca' => true,
                'waktu_kirim' => now()->subDays(3),
                'waktu_baca' => now()->subDays(3)->addHours(2),
            ],
            [
                'id_pengirim' => $users[1]->id_pengguna,
                'id_penerima' => $users[0]->id_pengguna,
                'tema' => 'Re: Diskusi Tugas Akhir',
                'isi_pesan' => 'Halo! Ya, ada template khusus. Saya akan kirimkan filenya via email. Format harus sesuai dengan panduan yang sudah ditetapkan fakultas.',
                'status_terbaca' => true,
                'waktu_kirim' => now()->subDays(3)->addHours(3),
                'waktu_baca' => now()->subDays(3)->addHours(5),
            ],
            [
                'id_pengirim' => $users[0]->id_pengguna,
                'id_penerima' => $users[1]->id_pengguna,
                'tema' => 'Jadwal Bimbingan',
                'isi_pesan' => 'Pak, untuk jadwal bimbingan minggu depan bagaimana? Saya sudah selesai draft bab 1 dan 2.',
                'status_terbaca' => false,
                'waktu_kirim' => now()->subDays(1),
            ],
            [
                'id_pengirim' => $users[2]->id_pengguna,
                'id_penerima' => $users[0]->id_pengguna,
                'tema' => 'Informasi Seminar Proposal',
                'isi_pesan' => 'Selamat siang, saya dari bagian akademik. Ingin menginformasikan bahwa seminar proposal akan dilaksanakan bulan depan. Silakan persiapkan berkas-berkas yang diperlukan.',
                'status_terbaca' => false,
                'waktu_kirim' => now()->subHours(6),
            ]
        ];

        foreach ($messages as $messageData) {
            Message::create($messageData);
        }

        // Create some reply messages
        $originalMessage = Message::first();
        if ($originalMessage) {
            Message::create([
                'id_pengirim' => $originalMessage->id_penerima,
                'id_penerima' => $originalMessage->id_pengirim,
                'tema' => 'Re: ' . $originalMessage->tema,
                'isi_pesan' => 'Terima kasih banyak atas informasinya. Saya akan segera download template tersebut.',
                'id_replay' => $originalMessage->id_message,
                'status_terbaca' => true,
                'waktu_kirim' => now()->subDays(2),
                'waktu_baca' => now()->subDays(2)->addHours(1),
            ]);
        }

        // Create some conversation between different users
        if ($users->count() >= 3) {
            Message::create([
                'id_pengirim' => $users[1]->id_pengguna,
                'id_penerima' => $users[2]->id_pengguna,
                'tema' => 'Koordinasi Jadwal Kuliah',
                'isi_pesan' => 'Selamat pagi Bu, saya mau konfirmasi jadwal kuliah untuk minggu depan. Apakah ada perubahan?',
                'status_terbaca' => false,
                'waktu_kirim' => now()->subHours(12),
            ]);

            Message::create([
                'id_pengirim' => $users[2]->id_pengguna,
                'id_penerima' => $users[1]->id_pengguna,
                'tema' => 'Re: Koordinasi Jadwal Kuliah',
                'isi_pesan' => 'Pagi! Tidak ada perubahan jadwal. Semua berjalan sesuai rencana. Jangan lupa bawa materi yang sudah saya berikan minggu lalu.',
                'status_terbaca' => false,
                'waktu_kirim' => now()->subHours(10),
            ]);
        }

        $this->command->info('Sample messages created successfully!');
    }
}

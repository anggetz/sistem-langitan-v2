<?php

namespace App\Http\Controllers\Dosen\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;

class DosenJadwalController extends Controller
{
    public function __construct() {}

    public function Jadwal()
    {
        try {
            $user = \App\Models\Pengguna::with([
                'dosen.pengampuMk.kelas_mk' => function ($query) {
                    $query->whereHas('semester', function ($q) {
                        $q->where('status_aktif_semester', 'True');
                    });
                },
            ])->find(auth()->user()->id_pengguna);

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $dosen = $user->dosen;

            $jadwal = collect($dosen->pengampuMk->filter(function ($item) {
                return $item->kelas_mk
                    && $item->kelas_mk->semester
                    && $item->kelas_mk->semester->status_aktif_semester;
            })->map(function ($item) {
                $mataKuliah = $item->kelas_mk->mataKuliah;
                $jadwalKelas = $item->kelas_mk->jadwalKelas;
                $jadwalJam = $jadwalKelas->jadwalJam;
                $ruangan = $jadwalKelas->ruangan;
                $gedung = $ruangan->gedung;
                return [
                    'nama_mk' => $mataKuliah->nm_mata_kuliah ?? '-',
                    'hari' => $jadwalKelas->nama_hari,
                    'id_kelas_mk' => $item->kelas_mk->id_kelas_mk,
                    'id_jadwal_hari' => $jadwalKelas->id_jadwal_hari,
                    'jam_mulai' => $jadwalJam->jam_mulai . ":" . $jadwalJam->menit_mulai,
                    'jam_selesai' => $jadwalJam->jam_selesai . ":" . $jadwalJam->menit_selesai,
                    'jam_mulai_ord' => $jadwalJam->jam_mulai * 100 + $jadwalJam->menit_mulai,
                    'jam_selesai_ord' => $jadwalJam->jam_selesai * 100 + $jadwalJam->menit_selesai,
                    'ruangan' => $ruangan->nm_ruangan ?? '-',
                    'gedung' => $gedung->nm_gedung ?? '-',
                ];
            }))
                ->sortBy([
                    fn($a, $b) => $a['id_jadwal_hari'] <=> $b['id_jadwal_hari'],
                    fn($a, $b) => $a['jam_mulai_ord'] <=> $b['jam_selesai_ord'],
                ])
                ->groupBy('hari')
                ->map(function ($items) {
                    return $items->map(function ($item) {
                        return [
                            'nama_mk' => $item['nama_mk'],
                            'id_kelas_mk' => $item['id_kelas_mk'],
                            'ruangan' => $item['ruangan'],
                            'gedung' => $item['gedung'],
                            'jadwal' => [
                                'jam' => $item['jam_mulai'] . ' - ' . $item['jam_selesai'],
                            ],
                        ];
                    })->values();
                });

            return response()->json([
                'message' => Message::OK,
                'data' => $jadwal
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function JadwalHariIni()
    {
        try {
            $user = \App\Models\Pengguna::with([
                'dosen.pengampuMk.kelas_mk' => function ($query) {
                    $query->whereHas('semester', function ($q) {
                        $q->where('status_aktif_semester', 'True');
                    });

                    $query->whereHas('jadwalKelas', function ($q) {
                        $q->where('id_jadwal_hari', date('w') + 2);
                    });
                },
            ])->find(auth()->user()->id_pengguna);

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $dosen = $user->dosen;

            $jadwal = collect($dosen->pengampuMk->filter(function ($item) {
                return $item->kelas_mk
                    && $item->kelas_mk->semester
                    && $item->kelas_mk->semester->status_aktif_semester;
            })->map(function ($item) {
                $mataKuliah = $item->kelas_mk->mataKuliah;
                $jadwalKelas = $item->kelas_mk->jadwalKelas;
                $jadwalJam = $jadwalKelas->jadwalJam;
                $ruangan = $jadwalKelas->ruangan;
                $gedung = $ruangan->gedung;
                return [
                    'nama_mk' => $mataKuliah->nm_mata_kuliah ?? '-',
                    'id_kelas_mk' => $item->kelas_mk->id_kelas_mk,
                    'hari' => $jadwalKelas->nama_hari,
                    'id_jadwal_hari' => $jadwalKelas->id_jadwal_hari,
                    'jam_mulai' => $jadwalJam->jam_mulai . ":" . $jadwalJam->menit_mulai,
                    'jam_selesai' => $jadwalJam->jam_selesai . ":" . $jadwalJam->menit_selesai,
                    'jam_mulai_ord' => $jadwalJam->jam_mulai * 100 + $jadwalJam->menit_mulai,
                    'jam_selesai_ord' => $jadwalJam->jam_selesai * 100 + $jadwalJam->menit_selesai,
                    'ruangan' => $ruangan->nm_ruangan ?? '-',
                    'gedung' => $gedung->nm_gedung ?? '-',
                ];
            }))
                ->sortBy([
                    fn($a, $b) => $a['id_jadwal_hari'] <=> $b['id_jadwal_hari'],
                    fn($a, $b) => $a['jam_mulai_ord'] <=> $b['jam_selesai_ord'],
                ])
                ->groupBy('hari')
                ->map(function ($items) {
                    return $items->map(function ($item) {
                        return [
                            'id_kelas_mk' => $item['id_kelas_mk'],
                            'nama_mk' => $item['nama_mk'],
                            'ruangan' => $item['ruangan'],
                            'gedung' => $item['gedung'],
                            'jadwal' => [
                                'jam' => $item['jam_mulai'] . ' - ' . $item['jam_selesai'],
                            ],
                        ];
                    })->values();
                });

            return response()->json([
                'message' => Message::OK,
                'data' => $jadwal
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}

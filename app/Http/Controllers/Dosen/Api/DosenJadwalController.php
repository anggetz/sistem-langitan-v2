<?php

namespace App\Http\Controllers\Dosen\Api;

use App\Http\Controllers\Controller;
use App\Models\Gedung;
use App\Models\JadwalJam;
use App\Models\Message;
use App\Models\PengampuMk;
use App\Models\Ruangan;
use App\Models\Semester;
use Exception;
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

                $jadwallAll = [];
                foreach ($jadwalKelas as $jadwal) {
                    $jadwallAll[] = [
                        'id_jadwal_hari' => $jadwal->id_jadwal_hari,
                        'nama_hari' => $jadwal->nama_hari,
                        'jam_mulai' => $jadwal->jadwalJam->jam_mulai . ":" . $jadwal->jadwalJam->menit_mulai,
                        'jam_selesai' => $jadwal->jadwalJam->jam_selesai . ":" . $jadwal->jadwalJam->menit_selesai,
                        'jam_mulai_ord' => $jadwal->jadwalJam->jam_mulai * 100 +  $jadwal->jadwalJam->menit_mulai,
                        'jam_selesai_ord' =>  $jadwal->jadwalJam->jam_selesai * 100 +  $jadwal->menit_selesai,
                        'gedung' => $jadwal->ruangan->gedung->nm_gedung ?? '-',
                        'ruangan' => $jadwal->ruangan->nm_ruangan ?? '-',
                    ];
                }

                return [
                    'nama_mk' => $mataKuliah->nm_mata_kuliah ?? '-',
                    'id_kelas_mk' => $item->kelas_mk->id_kelas_mk ?? 0,
                    'jadwalAll' => $jadwallAll,
                ];
            }));

            $jadwalResponse = [];
            // make the jadwal as single array
            $jadwal = $jadwal->map(function ($item) use (&$jadwalResponse) {
                foreach ($item['jadwalAll'] as $jadwal) {
                    # code...
                    array_push($jadwalResponse, [
                        'nama_mk' => $item['nama_mk'],
                        'id_kelas_mk' => $item['id_kelas_mk'],
                        'id_jadwal_hari' => $jadwal['id_jadwal_hari'],
                        'hari' => $jadwal['nama_hari'],
                        'jam_mulai' => $jadwal['jam_mulai'],
                        'jam_selesai' => $jadwal['jam_selesai'],
                        'jam_mulai_ord' => $jadwal['jam_mulai_ord'],
                        'jam_selesai_ord' => $jadwal['jam_selesai_ord'],
                        'gedung' => $jadwal['gedung'],
                        'ruangan' => $jadwal['ruangan'],
                    ]);
                }
            });

            $jadwalDosen = collect($jadwalResponse)
                    ->sortBy([
                        fn($a, $b) => $a['id_jadwal_hari'] <=> $b['id_jadwal_hari'],
                        fn($a, $b) => $a['jam_mulai_ord'] <=> $b['jam_selesai_ord'],
                    ])
                    ->groupBy('hari')->toArray();

            return response()->json([
                'message' => Message::OK,
                'data' => count($jadwalDosen) > 0 ? $jadwalDosen : json_decode('{}'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
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
                        $q->where('id_jadwal_hari', date('w') + 1);
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
                // $jadwalJam = $jadwalKelas->jadwalJam;
                $jadwallAll = [];

                foreach ($jadwalKelas as $jadwal) {
                    $jadwallAll[] = [
                        'id_jadwal_hari' => $jadwal->id_jadwal_hari,
                        'nama_hari' => $jadwal->nama_hari,
                        'jam_mulai' => $jadwal->jadwalJam?->jam_mulai . ":" . $jadwal->jadwalJam?->menit_mulai,
                        'jam_selesai' => $jadwal->jadwalJam?->jam_selesai . ":" . $jadwal->jadwalJam?->menit_selesai,
                        'jam_mulai_ord' => $jadwal->jadwalJam?->jam_mulai * 100 +  $jadwal->jadwalJam?->menit_mulai,
                        'jam_selesai_ord' =>  $jadwal->jadwalJam?->jam_selesai * 100 +  $jadwal->menit_selesai,
                        'gedung' => $jadwal->ruangan?->gedung?->nm_gedung ?? '-',
                        'ruangan' => $jadwal->ruangan?->nm_ruangan ?? '-',
                    ];
                }
                return [
                    'nama_mk' => $mataKuliah->nm_mata_kuliah ?? '-',
                    'id_kelas_mk' => $item->kelas_mk->id_kelas_mk,
                    'jadwalAll' => $jadwallAll,
                ];
            }));

             $jadwalResponse = [];
            // make the jadwal as single array
            $jadwal = $jadwal->map(function ($item) use (&$jadwalResponse) {
                foreach ($item['jadwalAll'] as $jadwal) {
                    # code...
                    array_push($jadwalResponse, [
                        'nama_mk' => $item['nama_mk'],
                        'id_kelas_mk' => $item['id_kelas_mk'],
                        'id_jadwal_hari' => $jadwal['id_jadwal_hari'],
                        'hari' => $jadwal['nama_hari'],
                        'jam_mulai' => $jadwal['jam_mulai'],
                        'jam_selesai' => $jadwal['jam_selesai'],
                        'jam_mulai_ord' => $jadwal['jam_mulai_ord'],
                        'jam_selesai_ord' => $jadwal['jam_selesai_ord'],
                        'gedung' => $jadwal['gedung'],
                        'ruangan' => $jadwal['ruangan'],
                    ]);
                }
            });

            $jadwalDosenToday = collect($jadwalResponse)
                    ->sortBy([
                        fn($a, $b) => $a['id_jadwal_hari'] <=> $b['id_jadwal_hari'],
                        fn($a, $b) => $a['jam_mulai_ord'] <=> $b['jam_selesai_ord'],
                    ])
                    ->groupBy('hari')->toArray();

            return response()->json([
                'message' => Message::OK,
                'data' => count($jadwalDosenToday) > 0 ? $jadwalDosenToday : json_decode('{}'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() . ' ' . $e->getLine()
            ], 500);
        }
    }

    public function jadwalUjianUTS(Request $request)
    {
        try {

            $id_kelas_mk = $request->input('id_kelas_mk');
            $page = $request->input('page');
            $perPage = $request->input('per_page', 10);
            $offset = ($page - 1) * $perPage;

            $dosen = auth()->user()->dosen;

            $semAktif = Semester::aktif();
            $jadwal = PengampuMk::with([
                'jadwalUjian' => function ($q) {
                    $q->orderBy('tgl_ujian', 'asc')
                        ->orderBy('jam_mulai', 'asc');
                },
                'jadwalUjian.kelas:id_kelas_mk,id_mata_kuliah',
                'jadwalUjian.kelas.mataKuliah:id_mata_kuliah,kd_mata_kuliah,nm_mata_kuliah',
            ])
                ->where('id_dosen', $dosen->id_dosen)
                ->whereHas('jadwalUjian.kegiatan', function ($q) {
                    $q->where('kode_kegiatan', 'UTS');
                })
                ->whereHas('jadwalUjian', function ($q) use ($semAktif) {
                    $q->where('id_semester', $semAktif->id_semester);
                });

            if (!empty($id_kelas_mk)) {
                $jadwal->where('id_kelas_mk', $id_kelas_mk);
            }


            $total = $jadwal->count();

            $jadwal = $jadwal->select(['id_pengampu_mk', 'id_kelas_mk', 'id_dosen'])
                ->offset($offset)
                ->limit($perPage)
                ->get();

            return response()->json([
                'status' => Message::OK,
                'data' => $jadwal,
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() . ' ' . $e->getLine()
            ], 500);
        }
    }

    public function jadwalUjianUAS(Request $request)
    {
        try {

            $id_kelas_mk = $request->input('id_kelas_mk');
            $page = $request->input('page');
            $perPage = $request->input('per_page', 10);
            $offset = ($page - 1) * $perPage;

            $dosen = auth()->user()->dosen;

            $semAktif = Semester::aktif();
            $jadwal = PengampuMk::with([
                'jadwalUjian' => function ($q) {
                    $q->orderBy('tgl_ujian', 'asc')
                        ->orderBy('jam_mulai', 'asc');
                },
                'jadwalUjian.kelas:id_kelas_mk,id_mata_kuliah',
                'jadwalUjian.kelas.mataKuliah:id_mata_kuliah,kd_mata_kuliah,nm_mata_kuliah',
            ])
                ->where('id_dosen', $dosen->id_dosen)
                ->whereHas('jadwalUjian.kegiatan', function ($q) {
                    $q->where('kode_kegiatan', 'UAS');
                })
                ->whereHas('jadwalUjian', function ($q) use ($semAktif) {
                    $q->where('id_semester', $semAktif->id_semester);
                });

            if (!empty($id_kelas_mk)) {
                $jadwal->where('id_kelas_mk', $id_kelas_mk);
            }

            $total = $jadwal->count();

            $jadwal = $jadwal->select(['id_pengampu_mk', 'id_kelas_mk', 'id_dosen'])
                ->offset($offset)
                ->limit($perPage)
                ->get();

            return response()->json([
                'status' => Message::OK,
                'data' => $jadwal,
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() . ' ' . $e->getLine()
            ], 500);
        }
    }
}

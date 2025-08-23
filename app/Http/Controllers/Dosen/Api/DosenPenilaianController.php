<?php

namespace App\Http\Controllers\Dosen\Api;

use App\Http\Controllers\Controller;
use App\Models\KomponenMk;
use App\Models\Mahasiswa;
use App\Models\MahasiswaStatus;
use App\Models\Message;
use App\Models\NilaiMk;
use App\Models\PengambilanMk;
use App\Models\PeraturanNilai;
use App\Models\Semester;
use App\Services\Mahasiswa\AkademikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DosenPenilaianController extends Controller
{
    public function __construct() {}

    /*
    * if the kelas doesnt have any komponen then the code will automatically add default komponen based on .env configuration
    */
    public function getKomponenByIdKelasMk($id_kelas_mk)
    {
        try {
            // check if the kelas mk has komponen
            $builder = \App\Models\KomponenMk::where('id_kelas_mk', $id_kelas_mk);
            $totalKomponen = $builder->count();
            if (!$totalKomponen) {
                $komponens = explode(',', env('KOMPONEN_MK', 'Aktivitas Partisipatif,Hasil Proyek,Tugas,Quiz,UTS,UAS'));

                // Map data terlebih dahulu, kemudian batch insert
                $mappedKomponens = collect($komponens)->map(function ($nm_komponen_mk, $index) use ($id_kelas_mk) {
                    return [
                        'id_kelas_mk' => $id_kelas_mk,
                        'nm_komponen_mk' => trim($nm_komponen_mk), // trim untuk menghilangkan spasi
                        'persentase_komponen_mk' => 0,
                        'urutan_komponen_mk' => $index + 1
                    ];
                })->toArray();

                // Single batch insert setelah mapping
                KomponenMk::insert($mappedKomponens);
            }

            $data = $builder->orderBy('urutan_komponen_mk')->get()->toArray();

            //if found return data
            return response()->json([
                'message' => 'Get komponen successfully.',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed get komponen.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateKomponen(Request $request, $id_kelas_mk)
    {
        try {
            $validatedData = $request->validate([
                'komponens' => 'required|array',
                'komponens.*.id_komponen_mk' => 'required|integer',
                'komponens.*.nm_komponen_mk' => 'required|string|max:255',
                'komponens.*.persentase_komponen_mk' => 'required|numeric|min:0|max:100',
                'komponens.*.urutan_komponen_mk' => 'integer'
            ]);

            $updatedKomponens = [];

            $updatedKomponens = collect($validatedData['komponens']);

            $totalActual = KomponenMk::where('id_kelas_mk', $id_kelas_mk)->count();
            $totalUpdated = $updatedKomponens->count();
            $totalProsentase = $updatedKomponens->sum('persentase_komponen_mk');

            if ($totalActual != $totalUpdated) {
                return response()->json([
                    'message' => 'Jumlah komponen yang diberikan tidak sesuai dengan jumlah komponen yang ada.',
                    'error' => 'Jumlah komponen yang diberikan: ' . $totalUpdated . ', Jumlah komponen yang ada: ' . $totalActual
                ], 400);
            }

            if ($totalProsentase < 100) {
                return response()->json([
                    'message' => 'Total prosentase komponen harus 100%.',
                    'error' => 'Total prosentase yang diberikan: ' . $totalProsentase
                ], 400);
            }

            DB::beginTransaction();
            KomponenMk::upsert(
                $validatedData['komponens'],
                ['id_komponen_mk'],
                ['persentase_komponen_mk']
            );


            DB::commit();

            return response()->json([
                'status' => Message::OK,
                'message' => 'Update komponen success.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'An error occurred while updating components.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function triggerCalcIps(Request $request, $id_kelas_mk)
    {
        Artisan::queue('app:calculating-final-score', [
            '--id_kelas_mk' => $id_kelas_mk,
        ]);
    }

    public function calculatingNilaiAkhir(Request $request, $id_kelas_mk)
    {
        $limit = $request->get('perPage', 10);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $limit;

        // id mhs
        $id_mhs = $request->get('id_mhs', null);
        $idSemesterAktif = Semester::aktif()->id_semester;

        try {
            // get mhs
            $q = \App\Models\PengambilanMk::when($id_mhs, function ($query, $id_mhs) {
                return $query->where('pengambilan_mk.id_mhs', $id_mhs);
            })
                ->where([
                    'pengambilan_mk.id_kelas_mk' => $id_kelas_mk,
                    'pengambilan_mk.id_semester' => $idSemesterAktif
                ])
                ->join('mahasiswa', 'pengambilan_mk.id_mhs', '=', 'mahasiswa.id_mhs')
                ->join('pengguna', 'mahasiswa.id_pengguna', '=', 'pengguna.id_pengguna')
                ->join('program_studi', 'mahasiswa.id_program_studi', '=', 'program_studi.id_program_studi')
                ->select([
                    'pengambilan_mk.id_pengambilan_mk',
                    'pengambilan_mk.id_kelas_mk',
                    'pengguna.nm_pengguna',
                    'mahasiswa.nim_mhs',
                    'program_studi.id_jenjang'
                ])
                ->with([
                    'nilaiMk:id_pengambilan_mk,id_komponen_mk,besar_nilai_mk',
                    'kelasMk:id_kelas_mk',
                    'kelasMk.komponenMk' => function ($query) {
                        $query->orderBy('urutan_komponen_mk', 'asc');
                    }
                ]);

            $total = $q->count();

            if (!$total) {
                return response()->json([
                    'status' => Message::FAIL,
                    'message' => 'Tidak ada mahasiswa yang terdaftar di kelas ini.',
                ], 404);
            }


            $kamusNilai = PeraturanNilai::with('standardNilai:id_standar_nilai,nm_standar_nilai')->where('id_jenjang', 1)
                ->orderBy('nilai_min_peraturan_nilai', 'desc')
                ->get()
                ->pluck('standardNilai.nm_standar_nilai', 'nilai_min_peraturan_nilai');

            $data = $q->offset($offset)
                ->limit($limit)
                ->get()
                ->map(function ($item) use ($kamusNilai) {
                    $komponenMk = $item->kelasMk?->komponenMk?->filter(fn($record) => $record->persentase_komponen_mk > 0) ?? collect([]);
                    $komponen = $komponenMk?->pluck('persentase_komponen_mk', 'id_komponen_mk');
                    $namaKomponen = $komponenMk?->pluck('nm_komponen_mk', 'id_komponen_mk');
                    $nilai = $item->nilaiMk?->pluck('besar_nilai_mk', 'id_komponen_mk') ?? collect([]);

                    $totalNilai = $komponen->filter(fn($persen, $id) => $nilai->has($id))
                        ->map(fn($persen, $id) => ($nilai->get($id) * $persen) / 100)
                        ->sum();
                    // map komponen
                    $mapKomponen = $namaKomponen->map(function ($nama, $id) use ($nilai) {
                        return [
                            'nm_komponen_mk' => $nama,
                            'besar_nilai_mk' => $nilai->get($id) ?? 0
                        ];
                    });

                    $nilaiHuruf = $kamusNilai->filter(fn($g, $min) => $totalNilai >= $min)->first() ?? '';

                    return [
                        'nama_mhs' => $item->nm_pengguna,
                        'nim_mhs' => $item->nim_mhs,
                        'nilai_akhir' => floor($totalNilai * 100) / 100,
                        'nilai_huruf' => $nilaiHuruf,
                        'rincian' => $mapKomponen
                    ];
                });



            return response()->json([
                'status' => Message::OK,
                'message' => 'Perhitungan nilai akhir berhasil.',
                'data' => $data,
                'total' => $total,
                'per_page' => $limit,
                'page' => $page,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to approve KRS MK.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /*
    Method untuk menyimpan nilai tiap komponen

*/
    public function saveNilaiMk(Request $request)
    {
        $validatedData = $request->validate([
            'id_kelas_mk' => 'required|integer',
            'id_komponen_mk' => 'required|integer',
            'mahasiswa' => 'required|array',
            'mahasiswa.*.id_mhs' => 'required|integer',
            'mahasiswa.*.besar_nilai_mk' => 'required|numeric|min:0|max:100',
        ]);

        // check if in range penilaian
        $akademikService = new AkademikService();

        try {
            if (!$akademikService->ValidateInputNilaiScheduleByActiveSemester()) {
                return response()->json([
                    'message' => 'Penilaian tidak dapat dilakukan di luar jadwal penilaian.',
                    'error' => 'Penilaian tidak dapat dilakukan di luar jadwal penilaian.'
                ], 400);
            }

            // get komponen mk
            $komponenMk = \App\Models\KomponenMk::where([
                'id_komponen_mk' => $request->id_komponen_mk,
            ])->first();

            if (!$komponenMk) {
                return response()->json([
                    'message' => 'Komponen MK tidakSeme ditemukan.',
                    'error' => 'Komponen MK dengan id_kelas_mk: ' . $request->id_kelas_mk . ' dan id_komponen_mk: ' . $request->id_komponen_mk . ' tidak ditemukan.'
                ], 404);
            }

            $sentMhs = collect($validatedData['mahasiswa']);
            $semesterActive = Semester::aktif()->id_semester;

            // get pengambilan mk with id mhs and make the map by id_mhs
            $pengambilanMks = \App\Models\PengambilanMk::where([
                'id_kelas_mk' => $request->id_kelas_mk,
                'id_semester' => $semesterActive,
            ])->get()->pluck('id_pengambilan_mk', 'id_mhs');

            // set id pengambilan mk to each mahasiswa
            $dataNilai = $sentMhs->map(function ($nilaiMhs) use ($pengambilanMks, $komponenMk) {
                $id = $nilaiMhs['id_mhs'];
                if ($pengambilanMks->has($id)) {
                    $nilaiMhs['id_pengambilan_mk'] = $pengambilanMks->get($id);
                    $nilaiMhs['id_komponen_mk'] = $komponenMk->id_komponen_mk;
                    return $nilaiMhs;
                }
            });

            NilaiMk::upsert(
                $dataNilai->toArray(),
                ['id_mhs', 'id_pengambilan_mk', 'id_komponen_mk'],
                [
                    'besar_nilai_mk',
                ]
            );

            // also update the mahasiswa status
            // call the command calculate final score using queue
            Artisan::queue('app:calculating-final-score', [
                '--id_kelas_mk' => $request->id_kelas_mk,
            ]);

            return response()->json([
                'status' => Message::OK,
                'message' => 'Nilai MK berhasil disimpan.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to save Nilai MK.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getNilai(Request $request, $id_kelas_mk)
    {
        $limit = $request->get('perPage', 10);
        $page = $request->get('page', 1);
        $id_semester = $request->get('id_semester', null);
        $offset = ($page - 1) * $limit;

        // id mhs
        $id_mhs = $request->get('id_mhs', null);
        // id komponen
        $id_komponen_mk = $request->get('id_komponen_mk', null);

        try {

            $data = PengambilanMk::select([
                'pengambilan_mk.id_pengambilan_mk',
                'pengambilan_mk.id_mhs',
                'pengambilan_mk.id_kelas_mk',
                'pengambilan_mk.id_semester',
            ])->where('pengambilan_mk.id_kelas_mk', $id_kelas_mk)
                ->with([
                    'mahasiswa.pengguna:id_pengguna,gelar_depan,nm_pengguna,gelar_belakang',
                    'nilaiMk' => function ($q) {
                        if (!empty($id_komponen_mk)) {
                            $q->where('id_komponen_mk', '=', $id_komponen_mk);
                        }
                    },
                    'nilaiMk.komponenMk' => function ($q) {
                        if (!empty($id_komponen_mk)) {
                            $q->where('id_komponen_mk', '=', $id_komponen_mk);
                        }
                    }
                ])
                ->when($id_mhs, function ($query) use ($id_mhs) {
                    return $query->where('id_mhs', $id_mhs);
                })
                ->when($id_semester, function ($query) use ($id_semester) {
                    return $query->where('id_semester', $id_semester);
                }, function ($query) {
                    return $query->where('id_semester', Semester::aktif()->id_semester);
                });

            return response()->json([
                'status' => Message::OK,
                'message' => 'Get Nilai successfully.',
                'data' => $data->offset($offset)
                    ->limit($limit)
                    ->get()->map(function ($item) {
                        Log::info("hello");
                        return [
                            'id_mhs' => $item->id_mhs,
                            'id_nilai_mk' => count($item->nilaiMk) > 0 ? $item->nilaiMk[0]->id_nilai_mk : null,
                            'nama_mhs' => $item->mahasiswa?->pengguna?->nama_lengkap ?? '',
                            'nim_mhs' => $item->mahasiswa?->nim_mhs ?? '',
                            'nilai' => count($item->nilaiMk) > 0 ? (int)$item->nilaiMk[0]->besar_nilai_mk : 0,
                            'id_pengambilan_mk' => $item->id_pengambilan_mk,
                            'id_komponen_mk' => count($item->nilaiMk) > 0 && !empty($item->nilaiMk[0]->komponenMk) ? $item->nilaiMk[0]->komponenMk->id_komponen_mk : null,
                        ];
                    }),
                'total' => $data->count(),
                'per_page' => $limit,
                'page' => $page,
            ], 200);
        } catch (\Exception $e) {
            dd($e);
            return response()->json([
                'message' => 'Failed to get Nilai.',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),

            ], 500);
        }
    }
}

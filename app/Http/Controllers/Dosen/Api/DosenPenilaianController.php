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

            // $temporaryForSaving = [];

            // foreach ($komponens as $index => $komponenMk) {
            //     $ifFound = false;
            //     foreach ($dbKomponen as $item) {
            //         if ($item->nm_komponen_mk == $komponenMk) {
            //             $ifFound = true;
            //             break;
            //         }
            //     }

            //     if (!$ifFound) {
            //         $newKomponen = new KomponenMk();
            //         $newKomponen->id_kelas_mk = $id_kelas_mk;
            //         $newKomponen->nm_komponen_mk = $komponenMk;
            //         $newKomponen->persentase_komponen_mk = 0; // default value
            //         $newKomponen->urutan_komponen_mk = $index;
            //         $temporaryForSaving[] = $newKomponen->toArray();
            //     }
            // }

            // //saving the temporary komponen bulk insert
            // if (count($temporaryForSaving) > 0) {
            //     KomponenMk::insert($temporaryForSaving);
            // }

            // // merge the komponent from the database and the temporary komponen
            // array_push($temporaryForSaving, ...$dbKomponen->toArray());

            // return response()->json([
            //     'message' => 'Get komponen successfully.',
            //     'data' => $temporaryForSaving
            // ], 200);
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

            // $totalProsentase = 0;

            // if ($totalActual != count($validatedData['komponens'])) {
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

            // foreach ($validatedData['komponens'] as $komponenData) {
            //     $totalProsentase += floatval($komponenData['persentase_komponen_mk']);
            // }

            // if ($totalProsentase < 100) {
            //     DB::rollBack();
            //     return response()->json([
            //         'message' => 'Total prosentase komponen harus 100%.',
            //         'error' => 'Total prosentase yang diberikan: ' . $totalProsentase
            //     ], 400);
            // }

            // KomponenMk::upsert(
            //     $validatedData['komponens'],
            //     ['nm_komponen_mk'],
            //     [
            //         'nm_komponen_mk',
            //         'persentase_komponen_mk',
            //         'urutan_komponen_mk',
            //     ]
            // );

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
                    'kelasMk.komponenMk'
                ]);
            // ->with(['mahasiswa.pengguna:id_pengguna,gelar_depan,nm_pengguna,gelar_belakang', 'mahasiswa.programStudi:id_program_studi,id_jenjang']);            

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
                    $komponen = $item->kelasMk?->komponenMk->pluck('persentase_komponen_mk', 'id_komponen_mk');
                    $nilai = $item->nilaiMk?->pluck('besar_nilai_mk', 'id_komponen_mk');

                    $totalNilai = $nilai
                        ->filter(fn($nilai, $id) => $komponen->has($id))
                        ->map(fn($nilai, $id) => ($nilai * $komponen->get($id)) / 100)
                        ->sum();

                    $nilaiHuruf = $kamusNilai->filter(fn($g, $min) => $totalNilai >= $min)->first() ?? '';

                    return [
                        'nama_mhs' => $item->nm_pengguna,
                        'nim_mhs' => $item->nim_mhs,
                        'nilai_akhir' => floor($totalNilai * 100) / 100,
                        'nilai_huruf' => $nilaiHuruf
                    ];
                });



            // $komponenFetched = [];

            // $namaMhsMapped = $mhs->map(function ($item) use ($id_kelas_mk, $komponenFetched) {
            //     $komponen = KomponenMk::where('id_kelas_mk', $id_kelas_mk)->get();
            //     $nilaiAkhir = 0;

            //     $nilaiMks = NilaiMk::selectRaw("
            //         id_komponen_mk, SUM(besar_nilai_mk)/count(id_komponen_mk) as besar_nilai_mk
            //     ")->where([
            //         'id_pengambilan_mk' => $item->id_pengambilan_mk,
            //         'id_mhs' => $item->id_mhs,
            //     ])->groupBy('id_komponen_mk')->get();

            //     foreach ($nilaiMks as $nilaiMk) {
            //         if ($komponenFetched[$nilaiMk->id_komponen_mk] ?? null) {
            //             $komponen = $komponenFetched[$nilaiMk->id_komponen_mk];
            //         } else {
            //             $komponen = KomponenMk::find($nilaiMk->id_komponen_mk);
            //             $komponenFetched[$nilaiMk->id_komponen_mk] = $komponen;
            //         }
            //         if ($komponen) {
            //             $nilaiAkhir += $nilaiMk->besar_nilai_mk * ($komponen->persentase_komponen_mk / 100);
            //         }
            //     }

            //     $mahasiswa = $item->mahasiswa ?? new Mahasiswa();
            //     $pengguna = $mahasiswa->pengguna ?? new \App\Models\Pengguna();

            //     return [
            //         'nama_mhs' => $pengguna->nama_lengkap,
            //         'nim_mhs' => $mahasiswa->nim_mhs ?? '',
            //         'nilai_akhir' => floor($nilaiAkhir),
            //         'nilai_huruf' => static::nilaiHuruf(floor($nilaiAkhir), $mahasiswa?->programStudi?->id_jenjang)
            //     ];
            // });

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
            if (!$akademikService->validateKRSScheduleByActiveSemester()) {
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
                    'message' => 'Komponen MK tidak ditemukan.',
                    'error' => 'Komponen MK dengan id_kelas_mk: ' . $request->id_kelas_mk . ' dan nm_komponen_mk: ' . $request->nm_komponen_mk . ' tidak ditemukan.'
                ], 404);
            }

            $idsMhs = collect($validatedData['mahasiswa'])->pluck('id_mhs')->toArray();

            // get pengambilan mk with id mhs and make the map by id_mhs
            $pengambilanMks = \App\Models\PengambilanMk::where([
                'id_kelas_mk' => $request->id_kelas_mk,
            ])->whereIn('id_mhs', $idsMhs)->get()->keyBy('id_mhs');

            // set id pengambilan mk to each mahasiswa
            $mahasiswa = collect($request->mahasiswa)->map(function ($item) use ($pengambilanMks, $komponenMk) {
                if (isset($pengambilanMks[$item['id_mhs']])) {
                    $item['id_pengambilan_mk'] = $pengambilanMks[$item['id_mhs']]->id_pengambilan_mk;
                    $item['id_komponen_mk'] = $komponenMk->id_komponen_mk;

                    // calculating the grade by komponen
                } else {
                    return response()->json([
                        'message' => 'Mahasiswa dengan id_mhs: ' . $item['id_mhs'] . ' tidak terdaftar di kelas ini.',
                        'error' => 'Mahasiswa dengan id_mhs: ' . $item['id_mhs'] . ' tidak terdaftar di kelas ini.'
                    ], 404);
                }
                return $item;
            });

            // save mahasiswa using upsert
            // dd($mahasiswa->toArray());
            NilaiMk::upsert(
                $mahasiswa->toArray(),
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
        $nm_komponen_mk = $request->get('nm_komponen_mk', null);

        try {

            $data = PengambilanMk::where('id_kelas_mk', $id_kelas_mk)
                ->with(['mahasiswa.pengguna:id_pengguna,gelar_depan,nm_pengguna,gelar_belakang'])
                ->when($id_mhs, function ($query) use ($id_mhs) {
                    return $query->where('id_mhs', $id_mhs);
                })
                ->when($id_semester, function ($query) use ($id_semester) {
                    return $query->where('id_semester', $id_semester);
                }, function ($query) {
                    return $query->where('id_semester', Semester::aktif()->id_semester);
                });

            $nilaiMks = NilaiMk::whereIn('id_pengambilan_mk', $data->get()->map(function ($item) {
                return $item->id_pengambilan_mk;
            }))->with([
                'komponenMk'
            ])
                ->get();

            return response()->json([
                'status' => Message::OK,
                'message' => 'Get Nilai successfully.',
                'data' => $data->offset($offset)
                    ->limit($limit)
                    ->get()->map(function ($item) use ($nilaiMks, $nm_komponen_mk) {
                        $pengguna = $item->mahasiswa->pengguna ?? new \App\Models\Pengguna();
                        $qNilaiMk = $nilaiMks->where('id_pengambilan_mk', $item->id_pengambilan_mk)
                            ->where('id_mhs', $item->id_mhs);

                        if ($nm_komponen_mk) {
                            $nilaiMk = $qNilaiMk->where('komponenMk.nm_komponen_mk', $nm_komponen_mk);
                        }

                        $nilaiMk = $qNilaiMk->first() ?? null;

                        return [
                            'id_mhs' => $item->id_mhs,
                            'id_nilai_mk' => $item->id_nilai_mk,
                            'nama_mhs' => $pengguna->nama_lengkap,
                            'nim_mhs' => $item->mahasiswa->nim_mhs ?? '',
                            'nilai' => $nilaiMk ? (int)$nilaiMk->besar_nilai_mk : 0,
                            'id_pengambilan_mk' => $item->id_pengambilan_mk,
                            'id_komponen_mk' => $nilaiMk ? $nilaiMk->id_komponen_mk : null,
                        ];
                    }),
                'total' => $data->count(),
                'per_page' => $limit,
                'page' => $page,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to get Nilai.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

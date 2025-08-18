<?php

namespace App\Http\Controllers\Dosen\Api;

use App\Http\Controllers\Controller;
use App\Models\KomponenMk;
use App\Models\Mahasiswa;
use App\Models\MahasiswaStatus;
use App\Models\Message;
use App\Models\NilaiMk;
use App\Models\PengambilanMk;
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
            $dbKomponen = \App\Models\KomponenMk::where('id_kelas_mk', $id_kelas_mk)->get();

            $komponens = explode(',', env('KOMPONEN_MK', 'Aktivitas Partisipatif,Hasil Proyek,Tugas,Quiz,UTS,UAS'));
            $temporaryForSaving = [];

            foreach ($komponens as $index => $komponenMk) {
                $ifFound = false;
                foreach ($dbKomponen as $item) {
                    if ($item->nm_komponen_mk == $komponenMk) {
                        $ifFound = true;
                        break;
                    }
                }

                if (!$ifFound) {
                    $newKomponen = new KomponenMk();
                    $newKomponen->id_kelas_mk = $id_kelas_mk;
                    $newKomponen->nm_komponen_mk = $komponenMk;
                    $newKomponen->persentase_komponen_mk = 0; // default value
                    $newKomponen->urutan_komponen_mk = $index;
                    $temporaryForSaving[] = $newKomponen->toArray();
                }
            }

            //saving the temporary komponen bulk insert
            if (count($temporaryForSaving) > 0) {
                KomponenMk::insert($temporaryForSaving);
            }

            // merge the komponent from the database and the temporary komponen
            array_push($temporaryForSaving, ...$dbKomponen->toArray());

            return response()->json([
                'message' => 'Get komponen successfully.',
                'data' => $temporaryForSaving
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
                'komponens.*.nm_komponen_mk' => 'required|string|max:255',
                'komponens.*.persentase_komponen_mk' => 'required|numeric|min:0|max:100',
                'komponens.*.urutan_komponen_mk' => 'integer'
            ]);

            $updatedKomponens = [];

            $actualKomponens = KomponenMk::where('id_kelas_mk', $id_kelas_mk)->get();

            $totalProsentase = 0;

            if (count($actualKomponens) != count($validatedData['komponens'])) {
                return response()->json([
                    'message' => 'Jumlah komponen yang diberikan tidak sesuai dengan jumlah komponen yang ada.',
                    'error' => 'Jumlah komponen yang diberikan: ' . count($validatedData['komponens']) . ', Jumlah komponen yang ada: ' . count($actualKomponens)
                ], 400);
            }

            DB::beginTransaction();

            foreach ($validatedData['komponens'] as $komponenData) {
                $totalProsentase += $komponenData['persentase_komponen_mk'];
            }

            if ($totalProsentase != 100) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Total prosentase komponen harus 100%.',
                    'error' => 'Total prosentase yang diberikan: ' . $totalProsentase
                ], 400);
            }

            KomponenMk::upsert(
                $validatedData['komponens'],
                ['nm_komponen_mk'],
                [
                    'nm_komponen_mk',
                    'persentase_komponen_mk',
                    'urutan_komponen_mk',
                ]
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

    public function calculatingNilaiAkhir(Request $request, $id_kelas_mk)
    {
        $limit = $request->get('perPage', 10);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $limit;

        // id mhs
        $id_mhs = $request->get('id_mhs', null);

        try {
            // get mhs
            $q = \App\Models\PengambilanMk::where('id_kelas_mk', $id_kelas_mk)
                ->with(['mahasiswa.pengguna:id_pengguna,gelar_depan,nm_pengguna,gelar_belakang']);

            if ($id_mhs) {
                $q->where('id_mhs', $id_mhs);
            }

            $total = $q->count();

            $mhs = $q->offset($offset)
                ->limit($limit)
                ->get();


            if ($mhs->isEmpty()) {
                return response()->json([
                    'status' => Message::FAIL,
                    'message' => 'Tidak ada mahasiswa yang terdaftar di kelas ini.',
                ], 404);
            }

            $komponenFetched = [];

            $namaMhsMapped = $mhs->map(function ($item) use ($id_kelas_mk, $komponenFetched) {
                $komponen = KomponenMk::where('id_kelas_mk', $id_kelas_mk)->get();
                $nilaiAkhir = 0;

                $nilaiMks = NilaiMk::selectRaw("
                    id_komponen_mk, SUM(besar_nilai_mk)/count(id_komponen_mk) as besar_nilai_mk
                ")->where([
                    'id_pengambilan_mk' => $item->id_pengambilan_mk,
                    'id_mhs' => $item->id_mhs,
                ])->groupBy('id_komponen_mk')->get();

                foreach ($nilaiMks as $nilaiMk) {
                    if ($komponenFetched[$nilaiMk->id_komponen_mk] ?? null) {
                        $komponen = $komponenFetched[$nilaiMk->id_komponen_mk];
                    } else {
                        $komponen = KomponenMk::find($nilaiMk->id_komponen_mk);
                        $komponenFetched[$nilaiMk->id_komponen_mk] = $komponen;
                    }
                    if ($komponen) {
                        $nilaiAkhir += $nilaiMk->besar_nilai_mk * ($komponen->persentase_komponen_mk / 100);
                    }
                }

                $mahasiswa = $item->mahasiswa ?? new Mahasiswa();
                $pengguna = $mahasiswa->pengguna ?? new \App\Models\Pengguna();

                return [
                    'nama_mhs' => $pengguna->nama_lengkap,
                    'nim_mhs' => $mahasiswa->nim_mhs ?? '',
                    'nilai_akhir' => floor($nilaiAkhir)
                ];
            });

            return response()->json([
                'status' => Message::OK,
                'message' => 'Perhitungan nilai akhir berhasil.',
                'data' => $namaMhsMapped,
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
            'nm_komponen_mk' => 'required|string',
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
                'id_kelas_mk' => $request->id_kelas_mk,
                'nm_komponen_mk' => $request->nm_komponen_mk,
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
                ['id_mhs', 'id_pengambilan_mk','id_komponen_mk'],
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
            // $nilaiMks = NilaiMk::whereHas('pengambilanMk', function ($query) use ($id_kelas_mk, $id_mhs, $id_semester) {
            //     $query->where('id_kelas_mk', $id_kelas_mk);
            //     if ($id_mhs) {
            //         $query->where('id_mhs', $id_mhs);
            //     }

            //     if ($id_semester) {
            //         $query->where('id_semester', $id_semester);
            //     } else {
            //         $query->where('id_semester', Semester::aktif()->id_semester);
            //     }
            // })->with(['pengambilanMk.mahasiswa.pengguna:id_pengguna,gelar_depan,nm_pengguna,gelar_belakang']);

            // if ($nm_komponen_mk) {
            //     $nilaiMks->leftJoin('komponen_mk', function ($join) use ($nm_komponen_mk) {
            //         $join->on('nilai_mk.id_komponen_mk', '=', 'komponen_mk.id_komponen_mk');
            //     });
            //     $nilaiMks->where('komponen_mk.nm_komponen_mk', '=',$nm_komponen_mk);
            // }


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

            $nilaiMks = NilaiMk::whereIn('id_pengambilan_mk', $data->get()->map(function($item) {
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

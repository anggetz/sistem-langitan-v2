<?php

namespace App\Http\Controllers\Dosen\Api;

use App\Http\Controllers\Controller;
use App\Models\KomponenMk;
use App\Models\Mahasiswa;
use App\Models\Message;
use App\Models\NilaiMk;
use Illuminate\Http\Request;
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

            $komponens = explode(',', env('KOMPONEN_MK'));
            $ifNeedRefetch = false;

            foreach ($komponens as $index => $komponenMk) {
                $ifFound = false;
                foreach ($dbKomponen as $item) {
                    if ($item->nm_komponen_mk == $komponenMk) {
                        $ifFound = true;
                        break;
                    }
                }

                if (!$ifFound) {
                    // if not found then create new komponen
                    $ifNeedRefetch = true;
                    \App\Models\KomponenMk::create([
                        'id_kelas_mk' => $id_kelas_mk,
                        'nm_komponen_mk' => $komponenMk,
                        'persentase_komponen_mk' => 0, // default bobot is 0
                        'urutan_komponen_mk' => $index, // default bobot is 0
                    ]);
                }
            }

            // get the komponen again after adding default komponen
            if ($ifNeedRefetch) {
                $dbKomponen = \App\Models\KomponenMk::where('id_kelas_mk', $id_kelas_mk)->get();
            }

            return response()->json([
                'message' => 'Get komponen successfully.',
                'data' => $dbKomponen
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to approve KRS MK.',
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
                $komponen = KomponenMk::where([
                    'id_kelas_mk' => $id_kelas_mk,
                    'id_komponen_mk' => $komponenData['id_komponen_mk']
                ])->first();

                if (!$komponen) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Komponen tidak ditemukan.',
                        'error' => 'Komponen dengan ' . $komponenData['id_komponen_mk'] . ' tidak ditemukan untuk kelas ' . $id_kelas_mk
                    ], 404);
                    continue;
                }

                $komponen->nm_komponen_mk = $komponenData['nm_komponen_mk'];
                $komponen->persentase_komponen_mk = $komponenData['persentase_komponen_mk'];
                $komponen->urutan_komponen_mk = $komponenData['urutan_komponen_mk'] ?? 0;
                $totalProsentase += $komponen->persentase_komponen_mk;
                $komponen->save();

                $updatedKomponens[] = $komponen;
            }

            if ($totalProsentase != 100) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Total prosentase komponen harus 100%.',
                    'error' => 'Total prosentase yang diberikan: ' . $totalProsentase
                ], 400);
            }

            DB::commit();

            return response()->json([
                'status' => Message::OK,
                'message' => 'Update komponen success.',
                'updated_data' => $updatedKomponens,
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

        try {
            // get mhs
            $mhs = \App\Models\PengambilanMk::where('id_kelas_mk', $id_kelas_mk)
                ->with(['mahasiswa.pengguna:id_pengguna,gelar_depan,nm_pengguna,gelar_belakang'])
                ->offset($offset)
                ->limit($limit)
                // ->whereHas('mahasiswa', function ($query) {
                //     $query->where('id_mhs', 5929);
                // })
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
                    'nilai_akhir' => round($nilaiAkhir, 2)
                ];
            });

            return response()->json([
                'status' => Message::OK,
                'message' => 'Perhitungan nilai akhir berhasil.',
                'data' => $namaMhsMapped
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to approve KRS MK.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

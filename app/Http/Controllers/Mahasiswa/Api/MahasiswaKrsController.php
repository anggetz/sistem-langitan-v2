<?php

namespace App\Http\Controllers\Mahasiswa\Api;

use App\Models\Message;
use App\Models\Semester;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Mahasiswa\AkademikService;
use App\Services\Mahasiswa\KeuanganService;
use App\Http\Resources\Mahasiswa\JadwalKuliahResource;
use App\Models\JadwalKegiatanSemester;
use App\Models\Kegiatan;
use App\Models\KelasMk;
use App\Models\MahasiswaKrsApprovalSign;
use App\Models\PengambilanMk;
use App\Services\Mahasiswa\KrsService as MahasiswaKrsService;
use Exception;
use Illuminate\Support\Facades\DB;
use KrsService;

class MahasiswaKrsController extends Controller
{

    public function CheckKRSScheduleOnCurrentSemester(Request $request)
    {
        $detailKegiatan = null;

        // remove this after demo
        $kegiatan = Kegiatan::where('kode_kegiatan',  'KRS')
            ->where('id_perguruan_tinggi', 1)
            ->first();

        $detailKegiatan = $jadwalKegiatanSemester = JadwalKegiatanSemester::where('id_kegiatan', $kegiatan->id_kegiatan)
            ->where('id_semester', Semester::aktif()->id_semester)
            ->first();

        try {
            $isValid = (new MahasiswaKrsService())->ValidateKRSScheduleByActiveSemester();

            if ($isValid) {
                return response()->json([
                    'message' => 'KRS schedule is valid for the current semester.',
                    'data' => true,
                    'info' => $detailKegiatan,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'KRS schedule is not valid for the current semester.',
                    'data' => false,
                    'info' => $detailKegiatan,
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve class schedule.',
                'data' => false,
                'error' => $e->getMessage(),
                'info' => $detailKegiatan,
            ], 400);
        }
    }


    public function CheckTagihanValidation(Request $request)
    {

        try {
            $isValid = (new MahasiswaKrsService())->validateMahasiswaCanKrsByActiveSemesterAndPrevSemester(auth()->user()->mahasiswa->id_mhs);

            if ($isValid) {
                return response()->json([
                    'message' => 'Mahasiswa dapat melakukan KRS pada semester ini.',
                    'data' => true,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Mahasiswa tidak dapat melakukan KRS pada semester ini. Silakan periksa tagihan atau status KRS Anda.',
                    'data' => false,
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'gagal melakukan validasi tagihan.',
                'data' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function listMataKuliahByActiveSemesterAndProdi(Request $request)
    {
        try {
            // get program studi from authenticated user
            $id_program_studi = auth()->user()->mahasiswa->id_program_studi;

            $res = (new MahasiswaKrsService())->listMataKuliahByActiveSemesterAndProdi($id_program_studi);
            return response()->json($res, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve data.',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }


    public function takeCourse(Request $request)
    {
        // Implement logic to handle course registration

        try {
            $validatedData = $request->validate([
                'id_kelas_mks' => 'required|array',
            ]);

            // check if mahasiswa can register for KRS
            if (!(new MahasiswaKrsService())->validateMahasiswaCanKrsByActiveSemesterAndPrevSemester(auth()->user()->mahasiswa->id_mhs)) {
                return response()->json([
                    'message' => 'Anda tidak dapat melakukan KRS pada semester ini. Silakan periksa tagihan atau status KRS Anda.',
                ], 400);
            }

            // if any pengambilam mk approved then cannot add course
            $pengambilanMk = PengambilanMk::where('id_mhs', auth()->user()->mahasiswa->id_mhs)
                ->where('id_semester', Semester::aktif()->id_semester)
                ->where('status_apv_pengambilan_mk', 1) // approved
                ->first();

            if ($pengambilanMk) {
                return response()->json([
                    'message' => 'Anda tidak dapat mendaftar mata kuliah karena sudah ada pengambilan mata kuliah yang disetujui.',
                ], 400);
            }

            // validate if any data in mahasiswa krs approval sign cannot take course

            DB::beginTransaction();

            $result = (new MahasiswaKrsService())->takeCourse($request->id_kelas_mks, auth()->user()->mahasiswa->id_mhs);

            DB::commit();

            return response()->json([
                'message' => 'Berhasil mendaftar mata kuliah.',
                'data' => $result,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mendaftar mata kuliah.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // leave course
    public function leaveCourse(Request $request)
    {
        // Implement logic to handle course registration

        try {
            $validatedData = $request->validate([
                'id_kelas_mks' => 'required|array',
            ]);

            // check if mahasiswa can register for KRS
            if (!(new MahasiswaKrsService())->validateMahasiswaCanKrsByActiveSemesterAndPrevSemester(auth()->user()->mahasiswa->id_mhs)) {
                return response()->json([
                    'message' => 'Anda tidak dapat melakukan KRS pada semester ini. Silakan periksa tagihan atau status KRS Anda.',
                ], 400);
            }

            $result = (new MahasiswaKrsService())->leaveCourse($request->id_kelas_mks, auth()->user()->mahasiswa->id_mhs);

            return response()->json([
                'message' => 'Mata kuliah berhasil dilepas.',
                'data' => $result,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal melepas mata kuliah.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getHistoryKrs(Request $request)
    {
        try {
            $id_semester = $request->get('id_semester', null);

            if (empty($id_semester)) {
                throw new Exception('id_semester tidak boleh kosong');
            }

            $result = (new MahasiswaKrsService())->getHistoryKrsByIdMhs(auth()->user()->mahasiswa->id_mhs, $id_semester);

            return response()->json([
                'message' => 'Riwayat KRS berhasil didapat.',
                'data' => $result,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mendapatkan riwayat KRS',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function getLimitSksPerSemester(Request $request)
    {
        try {
            $semester = Semester::prevAktif();

            if (count($semester) > 1) {
                // check if inside semester has semester pendek get pendek instead
                $isExistsPendek = $semester->where('nm_semester', 'Pendek')->exists();

                if ($isExistsPendek) {
                    $semester = $semester->where('nm_semester', 'Pendek')->first();
                } else {
                     $semester = $semester->where('nm_semester', 'Ganjil')->first();
                }
            } else if (count($semester) > 0) {
                 $semester = $semester[0];
            } else {
               return response()->json([
                    'message' => 'Tidak ada semester aktif',
                    'status' => false,
                ], 400);
            }

            $id_semester = $semester->id_semester;

            $result = (new MahasiswaKrsService())->getLimitSksPerSemester(auth()->user()->mahasiswa->id_mhs, $id_semester);

            $countKreditSemster = (new MahasiswaKrsService())->countKreditSemester(auth()->user()->mahasiswa->id_mhs, $id_semester);

            return response()->json([
                'message' => 'Batas maksimal SKS',
                'data' => [
                    'beban_sks' => $result,
                    'kredit_semester' => $countKreditSemster,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mendapatkan maksimal sks',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

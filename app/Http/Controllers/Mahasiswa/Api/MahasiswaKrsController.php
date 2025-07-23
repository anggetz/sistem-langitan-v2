<?php
namespace App\Http\Controllers\Mahasiswa\Api;

use App\Models\Message;
use App\Models\Semester;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Mahasiswa\AkademikService;
use App\Services\Mahasiswa\KeuanganService;
use App\Http\Resources\Mahasiswa\JadwalKuliahResource;
use App\Services\Mahasiswa\KrsService as MahasiswaKrsService;
use Exception;
use KrsService;

class MahasiswaKrsController extends Controller
{

    public function CheckKRSScheduleOnCurrentSemester(Request $request)
    {
        try {
            $isValid = (new MahasiswaKrsService())->ValidateKRSScheduleByActiveSemester();
            if ($isValid) {
                return response()->json([
                    'message' => 'KRS schedule is valid for the current semester.'
                ], 200);
            } else {
                return response()->json([
                    'message' => 'KRS schedule is not valid for the current semester.'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve class schedule.',
                'error' => $e->getMessage()
            ], 500);
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

            $result = (new MahasiswaKrsService())->takeCourse($request->id_kelas_mks);

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

            $result = (new MahasiswaKrsService())->leaveCourse($request->id_kelas_mks);

            return response()->json([
                'message' => 'Mata kuliah berhasil dilepas.',
                'data' => $result,
            ], 201);
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
            $th_semester = $request->get('tahun_semester', null);

            if (empty($th_semester)) {
                throw new Exception('tahun_semester tidak boleh kosong');
            }

            $result = (new MahasiswaKrsService())->getHistoryKrsByIdMhs(auth()->user()->mahasiswa->id_mhs, $th_semester);

            return response()->json([
                'message' => 'Riwayat KRS berhasil didapat.',
                'data' => $result,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mendapatkan riwayat KRS',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}

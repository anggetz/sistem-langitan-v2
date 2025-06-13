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

            $mataKuliah = (new MahasiswaKrsService())->listMataKuliahByActiveSemesterAndProdi($id_program_studi);
            return response()->json([
                'message' => 'Successfully retrieved data.',
                'data' => $mataKuliah
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve data.',
                'error' => $e->getMessage()
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

            $result = (new MahasiswaKrsService())->takeCourse($request->id_kelas_mks);

            return response()->json([
                'message' => 'Course registered successfully.',
                'status' => $result,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to register course.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}

<?php

namespace App\Http\Controllers\Dosen\Api;

use App\Http\Controllers\Controller;
use App\Models\DosenWali;
use App\Models\Message;
use App\Models\Semester;
use App\Services\Mahasiswa\KrsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;

class DosenKrsController extends Controller
{
    public function __construct() {}

    public function approveKprsMk() {
        try {
            $validatedData = request()->validate([
                'id_pengambilan_mk_kprs' => 'required|array',
                'id_mhs' => 'required',
            ]);

            // get semester aktif
            $semesterAktif = Semester::aktif();
            if (!$semesterAktif) {
                throw new \Exception("No active semester found.");
            }

            // validation hes dosen wali or not
            $dosenWali = DosenWali::where('id_mhs', $validatedData['id_mhs'])
                ->where('id_dosen', auth()->user()->dosen->id_dosen)
                ->where('id_semester', $semesterAktif->id_semester)
                ->where('status_dosen_wali', 1)
                ->first();

            if (empty($dosenWali)) {
                return response()->json([
                    'message' => 'lecture not allowed approve this data',
                ], 401);
            }

            $result = (new KrsService())->approveKprsMk($validatedData['id_pengambilan_mk_kprs']);

            return response()->json([
                'message' => 'KRS MK approved successfully.',
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to approve KRS MK.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function listCourseApproval() {
        try {
            $result = (new KrsService())->listCourse(auth()->user()->dosen->id_dosen);

            return response()->json([
                'message' => 'Get data successfully.',
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to get data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

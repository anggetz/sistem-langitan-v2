<?php

namespace App\Http\Controllers\Dosen\Api;

use App\Http\Controllers\Controller;
use App\Models\DosenWali;
use App\Models\MahasiswaKrsApprovalSign;
use App\Models\Message;
use App\Models\Semester;
use App\Services\Mahasiswa\KrsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;

class DosenKrsController extends Controller
{
    public function __construct() {}

    public function approveKprsMk(Request $request) {
        // define the sign variable
        $validatedData = [];

        try {
            $validatedData = request()->validate([
                'id_pengambilan_mk_kprs' => 'required',
                'id_mhs' => 'required',
            ]);

            // upload the sign image if exists
            if ($request->hasFile('sign')) {
                $file = $request->file('sign');
                $fileName = 'signatures/' . time() . '_' . $file->getClientOriginalName();
                Storage::disk('public')->put($fileName, file_get_contents($file));
                $validatedData['sign'] = $fileName;
            } else {
                $validatedData['sign'] = null; // or handle the case where no file is uploaded
            }


            // save the sign path to mahasiswa krs apprval sign
            $mahasiswaKrsApprovalSign = MahasiswaKrsApprovalSign::updateOrCreate(
                [
                    'id_mhs' => $validatedData['id_mhs'],
                    'id_semester' => Semester::aktif()->id_semester,
                    'id_dosen' => auth()->user()->dosen->id_dosen,
                ],
                [
                    'sign_path' => $validatedData['sign'],
                ]
            );

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

            // split the id_pengambilan_mk_kprs into an array
            $validatedData['id_pengambilan_mk_kprs'] = is_array($validatedData['id_pengambilan_mk_kprs']) ? $validatedData['id_pengambilan_mk_kprs'] : explode(',', $validatedData['id_pengambilan_mk_kprs']);

            $result = (new KrsService())->approveKprsMk($validatedData['id_pengambilan_mk_kprs']);

            return response()->json([
                'message' => 'KRS MK approved successfully.',
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            // if error remove the uploaded sign image
            if (isset($validatedData['sign']) && $validatedData['sign']) {
                Storage::disk('public')->delete($validatedData['sign']);
            }

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

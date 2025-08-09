<?php

namespace App\Http\Controllers\Dosen\Api;

use App\Http\Controllers\Controller;
use App\Models\DosenWali;
use App\Models\Fakultas;
use App\Models\Mahasiswa;
use App\Models\MahasiswaKrsApprovalSign;
use App\Models\MahasiswaStatus;
use App\Models\Message;
use App\Models\PengambilanMkKprs;
use App\Models\Pengguna;
use App\Models\ProgramStudi;
use App\Models\Semester;
use App\Services\Mahasiswa\KrsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;

class DosenKrsController extends Controller
{
    public function __construct() {}

    // get list mahasiswa approve krs sign
    public function getApprovedStudent(Request $request) {
        try {
            // pagination parameter
            $limit = $request->get('perPage', 10);
            $page = $request->get('page', 1);
            $offset = ($page - 1) * $limit;


            $q = MahasiswaKrsApprovalSign::
                with([
                    'mahasiswa.pengguna',
                    'mahasiswa.programStudi.fakultas',
                    'mahasiswaStatus'
                ]);

            $total = $q->count();

            $data = $q->where('id_dosen', auth()->user()->dosen->id_dosen)
                ->limit($limit)
                ->offset($offset)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($krs, $key) {
                    $mhs = $krs->Mahasiswa ?? new Mahasiswa();
                    $pengguna = $mhs->Pengguna ?? new Pengguna();
                    $programStudi = $mhs->programStudi ?? new ProgramStudi();
                    $fakultas = $programStudi->fakultas ?? new Fakultas();
                    $mhsStatus = $krs->MahasiswaStatus ?? new MahasiswaStatus();


                    return [
                        'id' => $krs->id_mahasiswa_krs_approval_sign,
                        'id_mhs' => $mhs->id_mhs,
                        'nama_mahasiswa' => $pengguna->nama_lengkap,
                        'program_studi' => $programStudi->nm_program_studi,
                        'fakultas' => $fakultas->nm_fakultas,
                        'semester' => $krs->semester->nm_semester ?? 'N/A',
                        'ipk' => $mhsStatus->ipk ?? 0,
                        'limit_sks' => $krs->limit_sks,
                        'kredit_sks' => $krs->kredit_sks,
                        'is_approved' => empty($krs->sign_path) ? false : true,
                        'ips' => $mhsStatus->ips ?? 0,
                    ];
                });


            return response()->json([
                'message' => 'Get data approved krs successfull',
                'status' => Message::OK,
                'data' => $data,
                'total' => $total,
                'page' => $page,
                'per_page' => $limit
            ]);
        } catch (Exception $err) {
            return response()->json([
                'message' => 'Failed to approve KRS MK.',
                'error' => $err->getMessage()
            ], 500);
        }
    }

    public function detailApprovalMahasiswa(Request $request, $id_mhs, $id_semester) {
        try {

            $history = (new KrsService())->getHistoryKrsByIdMhs($id_mhs, $id_semester);

            return response()->json([
                'message' => 'Get data successfully.',
                'data' => $history
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'message' => 'Failed to approve KRS MK.',
                'error' => $err->getMessage()
            ], 500);
        }
    }

    public function approveKprsMk(Request $request) {
        // define the sign variable
        $validatedData = [];

        try {
            $validatedData = request()->validate([
                'file' => 'nullable|image|max:2048', // max 2MB
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

            DB::beginTransaction();

            // calculate limit sks for this semester
            $currentKreditSemester = (new KrsService())->countKreditSemester($validatedData['id_mhs'], Semester::aktif()->id_semester);
            $limitForCurrentSemester = (new KrsService())->getLimitSksPerSemester($validatedData['id_mhs'], Semester::aktif()->id_semester);

            // save the sign path to mahasiswa krs apprval sign
            $mahasiswaKrsApprovalSign = MahasiswaKrsApprovalSign::updateOrCreate(
                [
                    'id_mhs' => $validatedData['id_mhs'],
                    'id_semester' => Semester::aktif()->id_semester,
                    'id_dosen' => auth()->user()->dosen->id_dosen,
                    'limit_sks' => $limitForCurrentSemester,
                    'kredit_sks' => $currentKreditSemester
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
            // $validatedData['id_pengambilan_mk_kprs'] = is_array($validatedData['id_pengambilan_mk_kprs']) ? $validatedData['id_pengambilan_mk_kprs'] : explode(',', $validatedData['id_pengambilan_mk_kprs']);
            PengambilanMkKprs::
                where('id_mhs', $validatedData['id_mhs'])
                ->where('id_semester', $semesterAktif->id_semester)
                ->update([
                    'status_apv_pengambilan_mk' => 1
                ]);

            DB::commit();

            return response()->json([
                'message' => 'KRS MK approved successfully.',
                'data' => true,
            ], 200);
        } catch (\Exception $e) {
            // if error remove the uploaded sign image
            if (isset($validatedData['sign']) && $validatedData['sign']) {
                Storage::disk('public')->delete($validatedData['sign']);
            }

            DB::rollBack();

            return response()->json([
                'message' => 'Failed to approve KRS MK.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function listCourseApproval(Request $request) {
        try {
            $idmhs = $request->query('id_mhs');

            $result = (new KrsService())->listCourse(auth()->user()->dosen->id_dosen, $idmhs);

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

     public function listStudentNeedApproval(Request $request) {
        try {

            $result = (new KrsService())->listMahasiswaNeedApproval(auth()->user()->dosen->id_dosen);

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


    public function listMataKuliahByActiveSemesterAndProdi(Request $request)
    {
        try {
            // get program studi from dosen wali mhs
            $idsProgramStudi = DosenWali::where('id_dosen', auth()->user()->dosen->id_dosen)
                ->where('status_dosen_wali', 1)
                ->where('id_semester', Semester::aktif()->id_semester)
                ->with(['mahasiswa'])
                ->get()->pluck('mahasiswa.id_program_studi');

            if (is_null($idsProgramStudi)) {
                return response()->json([
                    'message' => 'No program studi found for the current semester.',
                ], 404);
            }

            $res = (new KrsService())->listMataKuliahByActiveSemesterAndProdi($idsProgramStudi);
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
}

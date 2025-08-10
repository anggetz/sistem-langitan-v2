<?php

namespace App\Http\Controllers\Dosen\Api;

use App\Http\Controllers\Controller;
use App\Models\DosenWali;
use App\Models\Fakultas;
use App\Models\KelasMk;
use App\Models\Mahasiswa;
use App\Models\MahasiswaKrsApprovalSign;
use App\Models\MahasiswaStatus;
use App\Models\Message;
use App\Models\PengambilanMk;
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
    public function getListKrs(Request $request)
    {
        try {
            // pagination parameter
            $limit = $request->get('perPage', 10);
            $page = $request->get('page', 1);
            $offset = ($page - 1) * $limit;


            $q = MahasiswaKrsApprovalSign::with([
                'mahasiswa.pengguna',
                'mahasiswa.programStudi.fakultas',
                'mahasiswaStatus'
            ]);

            $total = $q->count();

            // get dosen mahasiswa allowable
            $listMhs = DosenWali::where('id_dosen', auth()->user()->dosen->id_dosen)
                ->get()
                ->map(function ($item) {
                    return $item->id_mhs;
                });

            $data = $q
                ->where('id_semester', Semester::aktif()->id_semester)
                ->whereIn('id_mhs', $listMhs)
                ->limit($limit)
                ->offset($offset)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($krs, $key) {
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
                        'id_semester' => $krs->id_semester,
                        'ipk' => (float)$mhsStatus->ipk ?? 0,
                        'limit_sks' => $krs->limit_sks,
                        'kredit_sks' => $krs->kredit_sks,
                        'is_approved' => empty($krs->sign_path) ? false : true,
                        'ips' => (float)$mhsStatus->ips ?? 0,
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

    public function detailApprovalMahasiswa(Request $request, $id_mhs, $id_semester)
    {
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

    public function approveKprsMk(Request $request)
    {
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
                Storage::disk('local')->put($fileName, file_get_contents($file));
                $validatedData['sign'] = $fileName;
            } else {
                $validatedData['sign'] = null; // or handle the case where no file is uploaded
            }

            DB::beginTransaction();

            // calculate limit sks for this semester
            $currentKreditSemester = (new KrsService())->countKreditSemester($validatedData['id_mhs'], Semester::aktif()->id_semester);
            $limitForCurrentSemester = (new KrsService())->getLimitSksPerSemester($validatedData['id_mhs'], Semester::aktif()->id_semester);

            // save the sign path to mahasiswa krs apprval sign
            $mahasiswaKrsApprovalSign = MahasiswaKrsApprovalSign::upsert(
                [
                    'id_mhs' => $validatedData['id_mhs'],
                    'id_semester' => Semester::aktif()->id_semester,
                    'id_dosen' => auth()->user()->dosen->id_dosen,
                    'limit_sks' => $limitForCurrentSemester,
                    'kredit_sks' => $currentKreditSemester,
                    'sign_path' => $validatedData['sign'],
                ],
                [
                    'id_mhs',
                    'id_semester'
                ],
                [
                    'sign_path',
                    'id_dosen',
                    'limit_sks',
                    'kredit_sks',
                    'id_mhs',
                    'id_semester'
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
            PengambilanMkKprs::where('id_mhs', $validatedData['id_mhs'])
                ->where('id_semester', $semesterAktif->id_semester)
                ->update([
                    'status_apv_pengambilan_mk' => 1,
                    'status_pengambilan_mk' => 1,
                ]);

            $data = PengambilanMkKprs::where('id_mhs', $validatedData['id_mhs'])
                ->where('id_semester', $semesterAktif->id_semester)
                ->whereHas('kelasMk')
                ->get()
                ->map(function ($item) {
                    return [
                        'id_mhs' => $item->id_mhs,
                        'id_kelas_mk' => $item->id_kelas_mk,
                        'status_apv_pengambilan_mk' => $item->status_apv_pengambilan_mk,
                        'status_pengambilan_mk' => $item->status_pengambilan_mk,
                        'id_semester' => $item->id_semester,
                        'nilai_angka' => 0,
                        'nilai_huruf' => '-',
                        'status_hapus' => 0,
                        'created_on' => date('Y-m-d'),
                        'updated_on' => date('Y-m-d'),
                    ];
                })
                ->toArray();

            // insert to pengambilan mk
            PengambilanMk::upsert(
                $data,
                ['id_mhs', 'id_kelas_mk', 'id_semester'],
                [
                    'id_mhs',
                    'id_kelas_mk',
                    'status_apv_pengambilan_mk',
                    'status_pengambilan_mk',
                    'id_semester',
                    'nilai_angka',
                    'nilai_huruf',
                    'status_hapus',
                    'created_on',
                    'updated_on'
                ]
            );
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

    public function listCourseApproval(Request $request)
    {
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

    public function listStudentNeedApproval(Request $request)
    {
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
            $validatedData = $request->validate([
                // 'id_program_studi' => 'required|integer',
                'id_mhs' => 'required|integer',
            ]);

            $id_mhs = $validatedData['id_mhs'];

            $mahasiswa = Mahasiswa::find($id_mhs);
            if (empty($mahasiswa)) {
                return response()->json([
                    'message' => 'Mahasiswa tidak ditemukan'
                ], 404);
            }

            $res = (new KrsService())->listMataKuliahByActiveSemesterAndProdi($mahasiswa->id_program_studi, Semester::aktif()->id_semester);
            return response()->json($res, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function takeCourse(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id_kelas_mks' => 'required|array',
                'id_mhs' => 'required|integer',
            ]);

            // validate if any data in mahasiswa krs approval sign cannot take course
            $mahasiswaKrsApprovalSign = MahasiswaKrsApprovalSign::where('id_mhs', $validatedData['id_mhs'])
                ->where('id_semester', Semester::aktif()->id_semester)
                ->first();

            // take course using KrsService
            $result = (new KrsService())->takeCourse($validatedData['id_kelas_mks'], $validatedData['id_mhs'], 'Mahasiswa ini');

            $totalSks = KelasMk::whereIn('id_kelas_mk', $validatedData['id_kelas_mks'])
                ->where('id_semester', Semester::aktif()->id_semester)
                ->sum('kredit_semester');

            $mahasiswaKrsApprovalSign->limit_sks = $mahasiswaKrsApprovalSign->limit_sks + $totalSks;
            $mahasiswaKrsApprovalSign->save();

            return response()->json([
                'message' => 'Successfully taking for courses.',
                'data' => $result,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to taking for courses.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function leaveCourse(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id_kelas_mks' => 'required|array',
                'id_mhs' => 'required|integer',
            ]);

            // validate if any data in mahasiswa krs approval sign cannot take course
            $mahasiswaKrsApprovalSign = MahasiswaKrsApprovalSign::where('id_mhs', $validatedData['id_mhs'])
                ->where('id_semester', Semester::aktif()->id_semester)
                ->first();

            // take course using KrsService
            $result = (new KrsService())->leaveCourse($validatedData['id_kelas_mks'], $validatedData['id_mhs']);

            $totalSks = KelasMk::whereIn('id_kelas_mk', $validatedData['id_kelas_mks'])
                ->where('id_semester', Semester::aktif()->id_semester)
                ->sum('kredit_semester');

            if ($mahasiswaKrsApprovalSign->limit_sks - $totalSks < 0) {
                return response()->json([
                    'message' => 'SKS tidak boleh kurang dari 0',
                ], 400);
            }

            $mahasiswaKrsApprovalSign->limit_sks = $mahasiswaKrsApprovalSign->limit_sks - $totalSks;
            $mahasiswaKrsApprovalSign->save();

            return response()->json([
                'message' => 'Successfully leaving for courses.',
                'data' => $result,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to leaving for courses.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

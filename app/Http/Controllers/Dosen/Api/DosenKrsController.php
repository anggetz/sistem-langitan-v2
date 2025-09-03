<?php

namespace App\Http\Controllers\Dosen\Api;

use App\Http\Controllers\Controller;
use App\Models\DosenWali;
use App\Models\Fakultas;
use App\Models\JadwalKegiatanSemester;
use App\Models\Kegiatan;
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
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;

class DosenKrsController extends Controller
{
    public function __construct() {}

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
            $isValid = (new KrsService())->ValidateKRSScheduleByActiveSemester();

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

    // get list mahasiswa approve krs sign
    public function getListKrs(Request $request)
    {
        try {
            // pagination parameter
            $limit = $request->get('perPage', 10);
            $page = $request->get('page', 1);
            $offset = ($page - 1) * $limit;
            $idSemesterAktif = Semester::aktif()->id_semester;

            $query = DosenWali::join('mahasiswa', 'dosen_wali.id_mhs', '=', 'mahasiswa.id_mhs')
                ->join('pengguna', 'mahasiswa.id_pengguna', '=', 'pengguna.id_pengguna')
                ->join('program_studi', 'mahasiswa.id_program_studi', '=', 'program_studi.id_program_studi')
                ->join('fakultas', 'program_studi.id_fakultas', '=', 'fakultas.id_fakultas')
                ->join('jenjang', 'program_studi.id_jenjang', '=', 'jenjang.id_jenjang')
                ->join(DB::raw('(SELECT DISTINCT id_mhs FROM pengambilan_mk WHERE id_semester = ' . $idSemesterAktif . ') mahasiswa_daftar'), 'mahasiswa.id_mhs', '=', 'mahasiswa_daftar.id_mhs')
                ->leftJoin('mahasiswa_krs_approval_sign', function ($join) use ($idSemesterAktif) {
                    $join->on('mahasiswa.id_mhs', '=', 'mahasiswa_krs_approval_sign.id_mhs')
                        ->where('mahasiswa_krs_approval_sign.id_semester', $idSemesterAktif);
                })
                ->leftJoin('mahasiswa_status', function ($join) use ($idSemesterAktif) {
                    $join->on('mahasiswa.id_mhs', '=', 'mahasiswa_status.id_mhs')
                        ->where('mahasiswa_status.id_semester', $idSemesterAktif);
                })
                ->join('semester', 'dosen_wali.id_semester', '=', 'semester.id_semester')
                ->where('dosen_wali.id_dosen', auth()->user()->dosen?->id_dosen)
                ->where('dosen_wali.id_semester', $idSemesterAktif)
                ->select([
                    'mahasiswa_krs_approval_sign.id_mahasiswa_krs_approval_sign as id',
                    'mahasiswa.id_mhs',
                    'pengguna.nm_pengguna as nama_mahasiswa',
                    'program_studi.nm_program_studi as program_studi',
                    'fakultas.nm_fakultas as fakultas',
                    'jenjang.nm_jenjang as jenjang',
                    'mahasiswa.thn_angkatan_mhs as angkatan',
                    'semester.nm_semester as semester',
                    'semester.id_semester',
                    'mahasiswa_status.ipk',
                    'mahasiswa_status.ips',
                    'mahasiswa_krs_approval_sign.limit_sks',
                    'mahasiswa_krs_approval_sign.kredit_sks',
                    'mahasiswa_krs_approval_sign.sign_path'
                ]);

            $total = $query->count();

            $data = $query
                ->limit($limit)
                ->offset($offset)
                ->orderByRaw('CASE WHEN mahasiswa_krs_approval_sign.sign_path IS NULL THEN 0 ELSE 1 END DESC') // yang kosong dulu
                ->orderBy('pengguna.nm_pengguna', 'ASC') // lalu nama
                ->get()
                ->map(function ($item) {
                    unset($item['rn']);
                    $item['id'] = $item['id'] ?? $item['id_mhs'];
                    $item['ipk'] = (float) $item['ipk'] ?? 0;
                    $item['ips'] = (float) $item['ips'] ?? 0;
                    $item['limit_sks'] = (int) $item['limit_sks'] ?? 0;
                    $item['kredit_sks'] = (int) $item['kredit_sks'] ?? 0;
                    $item['semester'] = $item['semester'] ?? 'N/A';
                    $item['is_approved'] = !empty($item['sign_path']);
                    unset($item['sign_path']);
                    return $item;
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


            // insert to pengambilan mk
            PengambilanMk::where('id_mhs', $validatedData['id_mhs'])
                ->where('id_semester', $semesterAktif->id_semester)
                ->update([
                    'status_apv_pengambilan_mk' => 1, // approved
                    'updated_on' => Carbon::now(),
                    'updated_by' => auth()->user()->id_pengguna,
                ]);

            $mataKuliah = PengambilanMkKprs::where('id_mhs', $validatedData['id_mhs'])
                ->where('id_semester', $semesterAktif->id_semester)
                ->get();

            $sksData = PengambilanMk::where('id_mhs', $validatedData['id_mhs'])
                ->join('kelas_mk', 'pengambilan_mk.id_kelas_mk', '=', 'kelas_mk.id_kelas_mk')
                ->join('mata_kuliah', 'kelas_mk.id_mata_kuliah', '=', 'mata_kuliah.id_mata_kuliah')
                ->get(['mata_kuliah.id_mata_kuliah', 'mata_kuliah.kredit_semester']);

            // unique id_mata_kuliah
            $sksTotal = $sksData->unique('id_mata_kuliah')->whereNotIn('id_mata_kuliah', collect($mataKuliah)->map(function ($item) {
                return $item->id_mata_kuliah;
            }))->sum('kredit_semester');


            // get previous mahasiswa status
            $prevData = MahasiswaStatus::where('id_semester', '<', $semesterAktif->id_semester)
                ->orderBy('id_semester', 'DESC')
                ->first();


            MahasiswaStatus::upsert(
                [
                    'id_mhs' => $validatedData['id_mhs'],
                    'ips' => 0,
                    'ipk' => $prevData->ipk,
                    'sks_total' => $sksTotal + $currentKreditSemester,
                    'sks_semester' =>  $currentKreditSemester,
                    'id_semester' => $semesterAktif->id_semester,
                    'id_status_pengguna' => 1,
                    'created_on' => Carbon::now(),
                    'created_by' => auth()->user()->id_pengguna,
                    'updated_on' => Carbon::now(),
                    'updated_by' => auth()->user()->id_pengguna,
                ],
                [
                    'id_mhs',
                    'id_semester',
                ],
                [
                    'id_mhs',
                    'ips',
                    'ipk',
                    'sks_total',
                    'sks_semester',
                    'id_semester',
                    'id_status_pengguna',
                    'created_on',
                    'created_by',
                    'updated_on',
                    'updated_by'
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

            $res = (new KrsService())->listMataKuliahByActiveSemesterAndProdi($mahasiswa->id_program_studi, Semester::aktif()->id_semester, $mahasiswa->id_mhs);
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

            // if any pengambilam mk approved then cannot add course
            $pengambilanMk = PengambilanMk::where('id_mhs', $validatedData['id_mhs'])
                ->where('id_semester', Semester::aktif()->id_semester)
                ->where('status_apv_pengambilan_mk', 1) // approved
                ->first();

            if ($pengambilanMk) {
                return response()->json([
                    'message' => 'Anda tidak dapat mendaftar mata kuliah karena sudah ada pengambilan mata kuliah yang disetujui.',
                    'error' => 'Pengambilan MK sudah disetujui.'
                ], 400);
            }

            // take course using KrsService
            $result = (new KrsService())->takeCourse($validatedData['id_kelas_mks'], $validatedData['id_mhs'], 'Mahasiswa ini');

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


            // take course using KrsService
            $result = (new KrsService())->leaveCourse($validatedData['id_kelas_mks'], $validatedData['id_mhs']);

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

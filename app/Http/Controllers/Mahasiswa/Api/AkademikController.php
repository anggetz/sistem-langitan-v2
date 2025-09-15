<?php

namespace App\Http\Controllers\Mahasiswa\Api;

use App\Models\Message;
use App\Models\Kegiatan;
use App\Models\Semester;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalKegiatanSemester;
use App\Services\Mahasiswa\BerandaService;
use App\Http\Resources\Mahasiswa\JadwalKuliahResource;
use App\Models\Config;
use App\Models\ConfigPT;
use App\Models\PresensiMhs;
use App\Services\Mahasiswa\AkademikService;
use Exception;

class AkademikController extends Controller
{

    private $akademikService;
    public function __construct(AkademikService $akademikService)
    {
        $this->akademikService = $akademikService;
    }
    public function index() {}
    public function kalender()
    {
        $data = $this->akademikService->kalender();
        return response()->json([
            'status' => Message::OK,
            'data' => $data
        ], 200);
    }

    public function jadwalKuliah()
    {
        $data = $this->akademikService->jadwalKuliah();
        return response()->json([
            'status' => Message::OK,
            'data' => $data
        ], 200);
    }

    public function jadwalKuliahHariIni()
    {
        $data = $this->akademikService->jadwalKuliah(date('w') + 1);
        return response()->json([
            'status' => Message::OK,
            'data' => $data
        ], 200);
    }

    public function khsPerSemester(Request $request)
    {
        $semester = $request->get("semester");
        $data = $this->akademikService->khs($semester);
        return response()->json([
            'status' => Message::OK,
            'data' => $data
        ], 200);
    }

    public function khsPerMataKuliah($semester, $mk)
    {
        return response()->json([
            'status' => Message::OK,
            'data' => $mk
        ], 200);
    }

    public function khs(Request $request)
    {

        if ($request->has("semester") && $request->has("mata-kuliah")) {

            return response()->json([
                'status' => Message::OK,
                'data' => $request->get("mata-kuliah")
            ], 200);
        }

        if ($request->has("semester")) {
            $data = $this->akademikService->khs($request->get("semester"));
            return response()->json([
                'status' => Message::OK,
                'data' => $data
            ], 200);
        }

        $semester = auth()->user()->mahasiswa
            ->pengambilanMk()
            ->with("semester:id_semester,nm_semester")
            ->groupBy("id_semester")
            ->get(["id_semester"]);

        return response()->json([
            'status' => Message::OK,
            'data' => $semester
        ], 200);
    }

    public function historyNilai(Request $request)
    {
        $semester = $request->get("semester", null);

        $data = auth()->user()->mahasiswa;
        $history = $data->historyNilai()
            ->with(['semester:id_semester,nm_semester,thn_akademik_semester'])->orderBy('id_mhs_status', 'asc')
            ->orderBy('created_on', 'desc');
        if (!empty($semester)) {
            $history = $history->where('id_semester', $semester);
        }
        $historyData = $history->get(['id_mhs_status', 'ips', 'ipk', 'sks_semester', 'sks_total', 'id_semester']);
        $historyCount = $history->count();
        $sks_tempuh = 0;
        $ipk = 0;
        if ($historyCount) {
            // $lastSemester = $historyData[$historyCount - 1];
            $sks_tempuh = (int)$historyData[$historyCount - 1]->sks_total;
            $ipk = $historyData[$historyCount - 1]->ipk;
        }
        return response()->json([
            'status' => Message::OK,
            'data' => $data,
            // make 2 digit after comma
            'ipk' => number_format($ipk, 2),
            "sks_tempuh" => $sks_tempuh,
            "semester" => $historyCount,
            "history" => $historyData,
        ], 200);
    }

    public function jadwalUjian()
    {
        $mhs = auth()->user()->mahasiswa;
        $semAktif = Semester::aktif();
        $jadwalUTS = $mhs->jadwalUjian()->with([
            'kelas:id_kelas_mk,id_mata_kuliah',
            'kelas.mataKuliah:id_mata_kuliah,kd_mata_kuliah,nm_mata_kuliah',
        ])
            ->where('id_semester', $semAktif->id_semester)
            ->whereHas('kegiatan', function ($q) {
                $q->where('kode_kegiatan', 'UTS');
            })
            ->orderBy('tgl_ujian', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->select(['tgl_ujian', 'jam_mulai', 'jam_selesai', 'id_kelas_mk', 'id_kegiatan', 'id_mhs'])
            ->get();

        $jadwalUAS = $mhs->jadwalUjian()->with([
            'kelas:id_kelas_mk,id_mata_kuliah',
            'kelas.mataKuliah:id_mata_kuliah,kd_mata_kuliah,nm_mata_kuliah',
        ])
            ->where('id_semester', $semAktif->id_semester)
            ->whereHas('kegiatan', function ($q) {
                $q->where('kode_kegiatan', 'UAS');
            })
            ->orderBy('tgl_ujian', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->select(['tgl_ujian', 'jam_mulai', 'jam_selesai', 'id_kelas_mk', 'id_kegiatan', 'id_mhs'])
            ->get();
        return response()->json([
            'status' => Message::OK,
            'jadwalUTS' => $jadwalUTS,
            'jadwalUAS' => $jadwalUAS,
        ], 200);
    }

    public function listSemester()
    {
        $semester = auth()->user()->mahasiswa
            ->pengambilanMk()
            ->with("semester:id_semester,nm_semester,tahun_ajaran,status_aktif_semester")
            ->groupBy("id_semester")
            ->orderByDesc('id_semester')
            ->get(["id_semester"]);

        return response()->json([
            'status' => Message::OK,
            'data' => $semester
        ], 200);
    }

    public function rekapAbsensi(Request $request, $semester)
    {
        try {
            $data = auth()->user()->mahasiswa
                ->pengambilanMk()
                ->with([
                    'kelasMk.mataKuliah:id_mata_kuliah,kd_mata_kuliah,nm_mata_kuliah',
                    'kelasMk:id_kelas_mk,id_mata_kuliah',
                    'namaKelas:id_nama_kelas,nama_kelas'
                ])
                ->where('pengambilan_mk.id_semester', $semester)
                ->get(['id_kelas_mk', 'status_cekal', 'status_cekal_uts'])
                ->transform(function ($item, $key) {
                    $item->status_cekal = ($item->status_cekal == 2) ? 'Tidak Cekal UAS' : 'Kena Cekal UAS';
                    $item->status_cekal_utes = ($item->status_cekal_uts == 2) ? 'Tidak Cekal UTS' : 'Kena Cekal UTS';
                    $item->jumlah_pertemuan = $item->kelasMk->presensiKelas()->count();
                    $item->jumlah_kehadiran = $item->kelasMk->presensiMhs()->where('id_mhs', auth()->user()->mahasiswa->id_mhs)->count();
                    return $item;
                });

            return response()->json([
                'status' => Message::OK,
                'data' => $data,
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => 'Gagal mendapatkan data rekap mahasiswa',
                'error' => $err->getMessage()
            ], 500);
        }
    }

    // get semester active group by table pengambilan_mk
    public function getSemesterActive()
    {
        $data = auth()->user()->mahasiswa
            ->pengambilanMkKprs()
            ->with("semester:id_semester,nm_semester,tahun_ajaran,status_aktif_semester")
            ->groupBy("id_semester")
            ->orderByDesc('id_semester')
            ->get(["id_semester"]);

        return response()->json([
            'status' => Message::OK,
            'data' => $data
        ], 200);
    }

    public function isAllowAddMkKrs()
    {
        try {
            $configPt = ConfigPT::where('KD_CONFIG', 'IS_ALLOW_ADD_MK_KRS')
                ->where('id_perguruan_tinggi', env('APP_ID_PERGURUAN_TINGGI_DEFAULT', '1'))
                ->first();

            if (!$configPt) {
                // check if kd_config inside config table
               return response()->json([
                    'status' => Message::OK,
                    'message' => 'Config not found',
                    'data' => false
                ], 200);
            }
            return response()->json([
                'status' => Message::OK,
                'data' => $configPt->config_value == 'Y' ? true : false,
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => Message::FAIL,
                'message' => 'Gagal mendapatkan config pt',
                'error' => $err->getMessage()
            ], 500);
        }
    }
}

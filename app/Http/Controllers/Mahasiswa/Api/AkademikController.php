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
use App\Models\PresensiMhs;
use App\Services\Mahasiswa\AkademikService;

class AkademikController extends Controller
{

    private $akademikService;
    public function __construct(AkademikService $akademikService)
    {
        $this->akademikService=$akademikService;
    }
    public function index()
    {
    }
    public function kalender()
    {
        $data = $this->akademikService->kalender();
        return response()->json([
            'status' => Message::OK,
            'data' => $data
        ], 200);
    }

    public function jadwalKuliah(){
        $data = $this->akademikService->jadwalKuliah();
        return response()->json([
            'status' => Message::OK,
            'data' => $data
        ], 200);
    }

    public function khsPerSemester(Request $request){
        $semester= $request->get("semester");
        $data = $this->akademikService->khs($semester);
        return response()->json([
            'status'=>Message::OK,
            'data'=> $data
        ],200);
    }

    public function khsPerMataKuliah($semester, $mk){
        return response()->json([
            'status'=>Message::OK,
            'data'=>$mk
        ],200);

    }
    
    public function khs(Request $request){

        if($request->has("semester")&&$request->has("mata-kuliah")){

            return response()->json([
                'status'=>Message::OK,
                'data'=> $request->get("mata-kuliah")
            ],200);
        }
        
        if($request->has("semester")){
            $data = $this->akademikService->khs($request->get("semester"));
            return response()->json([
                'status'=>Message::OK,
                'data'=> $data
            ],200);
            
        }

        $semester=auth()->user()->mahasiswa
                    ->pengambilanMk()
                    ->with("semester:id_semester,nm_semester")
                    ->groupBy("id_semester")
                    ->get(["id_semester"]);

        return response()->json([
            'status'=>Message::OK,
            'data'=>$semester
        ],200);
    }

    public function historyNilai(){
        $data = auth()->user()->mahasiswa;
        $history = $data->historyNilai()->with(['semester:id_semester,nm_semester,thn_akademik_semester'])->orderBy('id_mhs_status','asc');
        $historyData = $history->get(['id_mhs_status','ips','ipk','sks_semester','sks_total','id_semester']);
        $historyCount = $history->count();
        $sks_tempuh = 0;
        if($historyCount){
            $lastSemester = $historyData[$historyCount-1];
            $sks_tempuh = $lastSemester->sks_total;
        }
        return response()->json([
            'status'=>Message::OK,
            'data'=>$data,
            "sks_tempuh" => $sks_tempuh,
            "semester"=>$historyCount,
            "history"=>$historyData,
        ],200);
    }

    public function jadwalUjian(){
        $mhs = auth()->user()->mahasiswa;
        $semAktif = Semester::aktif();
        $jadwalUTS = $mhs->jadwalUjian()->with([
            'kelas:id_kelas_mk,id_mata_kuliah',
            'kelas.mataKuliah:id_mata_kuliah,kd_mata_kuliah,nm_mata_kuliah',
            ])
            ->where('id_semester',$semAktif->id_semester)
            ->whereHas('kegiatan',function($q){
                $q->where('kode_kegiatan','UTS');
            })
            ->orderBy('tgl_ujian','asc')
            ->orderBy('jam_mulai','asc')
            ->select(['tgl_ujian','jam_mulai','jam_selesai','id_kelas_mk','id_kegiatan'])
            ->get();
        $jadwalUAS = $mhs->jadwal_ujian()->with([
            'kelas:id_kelas_mk,id_mata_kuliah',
            'kelas.mataKuliah:id_mata_kuliah,kd_mata_kuliah,nm_mata_kuliah',
            ])
            ->where('id_semester',$semAktif->id_semester)
            ->whereHas('kegiatan',function($q){
                $q->where('kode_kegiatan','UAS');
            })
            ->orderBy('tgl_ujian','asc')
            ->orderBy('jam_mulai','asc')
            ->select(['tgl_ujian','jam_mulai','jam_selesai','id_kelas_mk','id_kegiatan'])
            ->get();
        return response()->json([
            'status'=>Message::OK,
            'jadwalUTS'=>$jadwalUTS,
            'jadwalUAS'=>$jadwalUAS,
        ],200);
    }

    public function listSemester(){
        $semester=auth()->user()->mahasiswa
                    ->pengambilanMk()
                    ->with("semester:id_semester,nm_semester,tahun_ajaran,status_aktif_semester")
                    ->groupBy("id_semester")
                    ->orderByDesc('id_semester')
                    ->get(["id_semester"]);

        return response()->json([
            'status'=>Message::OK,
            'data'=>$semester
        ],200);

    }

    public function rekapAbsensi(Request $request,$semester){
        $data=auth()->user()->mahasiswa
                    ->pengambilanMk()
                    ->with(['kelasMk.mata_kuliah:id_mata_kuliah,kd_mata_kuliah,nm_mata_kuliah',
                    'kelasMk:id_kelas_mk,id_mata_kuliah',
                    'namaKelas:id_nama_kelas,nama_kelas'])
                    ->where('pengambilanMk.id_semester',$semester)
                    ->get(['id_kelas_mk','status_cekal','status_cekal_uts'])
                    ->transform(function($item,$key){
                        $item->status_cekal = ($item->status_cekal == 2) ? 'Tidak Cekal UAS' : 'Kena Cekal UAS';
                        $item->status_cekal_utes = ($item->status_cekal_uts == 2) ? 'Tidak Cekal UTS' : 'Kena Cekal UTS';
                        $item->jumlah_pertemuan = $item->kelasMk->presensiKelas()->count();
                        $item->jumlah_kehadiran = $item->kelasMk->presensiMhs()->where('id_mhs',auth()->user()->mahasiswa->id_mhs)->count();
                        return $item;
                    })
                    ;

        return response()->json([
            'status'=>Message::OK,
            'data'=>$data,
        ],200);
    }


}

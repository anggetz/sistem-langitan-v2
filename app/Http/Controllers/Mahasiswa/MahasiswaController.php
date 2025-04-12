<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateMahasiswaRequest;
use App\Models\Message;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class MahasiswaController extends Controller
{

    public function __construct()
    {
        // $this->authorizeResource(Mahasiswa::class);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mahasiswa = auth()->user()->mahasiswa()->with([
            'agama:agama.id_agama,agama.nm_agama',
            'pengguna:id_pengguna,nm_pengguna,tgl_lahir_pengguna,kelamin_pengguna,email_pengguna,email_alternate',
            'kota',
            'provinsi',
            'sumberBiaya',
            'asalSekolah:id_sekolah,nm_sekolah',
            'programStudi:id_program_studi,nm_program_studi,id_jenjang',
            'pendidikanIbu',
            'pekerjaanIbu:id_pekerjaan,nm_pekerjaan',
            'kotaIbu',
            'provinsiIbu',
            'pendidikanAyah',
            'pekerjaanAyah:id_pekerjaan,nm_pekerjaan',
            'kotaAyah',
            'provinsiAyah',
            'pendidikanWali',
            'pekerjaanWali:id_pekerjaan,nm_pekerjaan',
            'kotaWali',
            'provinsiWali',
            'transportasi',
            'programStudi.jenjang',
            'statusPengguna:id_status_pengguna,nm_status_pengguna,status_akademik',
            'kota',
            'provinsi',
            'dosenWali.dosen.pengguna:id_pengguna,nm_pengguna,gelar_depan,gelar_belakang',
            'dosenWali:id_dosen_wali,id_dosen,id_mhs',
            'dosenWali.dosen:id_dosen,id_pengguna'
        ])->first();
        switch($mahasiswa->kewarganegaraan){
            case 2 : $mahasiswa->kewarganegaraan = 'WNA'; break;
            case 3 : $mahasiswa->kewarganegaraan = 'WNI Keturunan'; break;
            case 1 :
            default :
            $mahasiswa->kewarganegaraan = 'WNI'; break;
        }
        $data=[
            "mahasiswa"=>$mahasiswa,
            "data_akademik"=>$mahasiswa->data_akademik,
            // "foto"=>$mahasiswa->foto,
            // "program_studi"=>ProgramStudi::select(["id_program_studi","nm_program_studi"])->get(),
            // "program_studi"=>$mahasiswa->program_studi,
         ];
        return response()->json([
                'message' => Message::OK,
                'data' => $data
            ],200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $request->only(["alamat_mhs"]);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Mahasiswa  $mahasiswa
     * @return \Illuminate\Http\Response
     */
    public function show(Mahasiswa $mahasiswa)
    {
        // return auth()->user()->mahasiswa;
        $dataMahasiswa= auth()->user()->mahasiswa
        ->with(["programStudi","kota","provinsi"])
        ->first();

        // $dataMahasiswa= Mahasiswa::with(["program_studi","kota","provinsi"])
        //                 ->where("id_pengguna",$id_pengguna)->first();
         $data=[
            "mahasiswa"=>$dataMahasiswa,
            "program_studi"=>ProgramStudi::select(["id_program_studi","nm_program_studi"])->get()
         ];
        return response()->json([
                'message' => Message::OK,
                'data' => $data
            ],200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Mahasiswa  $mahasiswa
     * @return \Illuminate\Http\Response
     */
    public function edit(Mahasiswa $mahasiswa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Mahasiswa  $mahasiswa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $hasil=auth()->user()->mahasiswa->update(
            $request->except(
                ["id_perguruan_tinggi","password_general","langitan_http_host","_method"]
            ));
        if($hasil)
            return response()->json([
                "status"=>Message::OK,
                "data"=>auth()->user()->mahasiswa
            ],201);
        else 
            return response()->json([
                "status"=>Message::FAIL,
                "data"=>$hasil
            ],401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Mahasiswa  $mahasiswa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Mahasiswa $mahasiswa)
    {
        //
    }
}

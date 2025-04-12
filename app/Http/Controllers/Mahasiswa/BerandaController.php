<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Models\Message;
use App\Models\Semester;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Mahasiswa\AkademikService;
use App\Services\Mahasiswa\KeuanganService;
use App\Http\Resources\Mahasiswa\JadwalKuliahResource;

class BerandaController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $jadwalKelas=(new AkademikService())->jadwalKuliah();
        // $kalender=(new AkademikService())->kalender();
        // $tagihan=(new KeuanganService())->tagihan();

        return response()->json([
            'message' => Message::OK,
            'data' =>[ 
                "semester" => Semester::aktif(),
                "jadwal_kelas"=>JadwalKuliahResource::collection($jadwalKelas),
                // "kalender"=>$kalender,
                // "tagihan"=>$tagihan
                ]
        ], 200);
    }
}

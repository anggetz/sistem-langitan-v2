<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Pengguna;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    public function me(Request $request)
    {
        $pengguna = Pengguna::with(['mahasiswa'])
            ->find($request->user("api")->id_pengguna);

        if($pengguna->mahasiswa){
            return response()->json([
                'message' => Message::OK,
                'data' => $pengguna,
                'data_akademik' => $pengguna->mahasiswa->data_akademik,
                'foto' => $pengguna->mahasiswa->foto,
            ],200);

        }
        return response()->json([
            'message' => Message::OK,
            'data' => $pengguna,
        ],200);
    }
}

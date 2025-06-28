<?php

namespace App\Http\Controllers\Rektor;

use App\Http\Controllers\Controller;
use App\Models\Akreditas;
use App\Models\Beasiswa;
use App\Models\Message;
use Exception;
use Illuminate\Http\Request;

class AkreditasController extends Controller
{

    public function Index(Request $request)
    {
        try {
            $limit = $request->get('perPage', 10);
            $page = $request->get('page', 1);
            $offset = ($page - 1) * $limit;

            $q = Akreditas::with([
                'ProgramStudi:id_program_studi,nm_program_studi,kode_program_studi,nm_singkat_prodi,no_sk,tgl_pendirian,gelar_inggris'
            ]);

            $total = $q->count();

            $dataAkreditas = $q->offset($offset)
                ->limit($limit)
                ->get();

            return response()->json([
                'status' => Message::OK,
                'data' => $dataAkreditas,
                'total' => $total,
                'page' => $page,
                'per_page' => $limit
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() . ' ' . $e->getLine()
            ], 500);
        }
    }
}

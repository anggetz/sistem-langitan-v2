<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Beasiswa;
use App\Models\BeasiswaHistory;
use App\Models\Message;
use Exception;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class BeasiswaController extends Controller
{

    public function Index(Request $request)
    {
        try {
            $id_group_beasiswa = $request->get('id_group_beasiswa');
            $limit = $request->get('perPage', 10);
            $page = $request->get('page', 1);
            $offset = ($page - 1) * $limit;

            $q = Beasiswa::with([
                'GroupBeasiswa'
            ]);

            if (!empty($id_group_beasiswa)) {
                $q = $q->where('id_group_beasiswa', $id_group_beasiswa);
            }

            $total = $q->count();

            $dataBeasiswa = $q->offset($offset)
                ->limit($limit)
                ->get();

            return response()->json([
                'status' => Message::OK,
                'data' => $dataBeasiswa,
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

    public function Detail(Request $request, $id)
    {
        try {

            $dataBeasiswa = Beasiswa::with([
                'GroupBeasiswa',
                'JenisBeasiswa',
                'PengumumanBeasiswa' => function ($q) {
                    $q->whereRaw('batas_akhir > SYSDATE');
                }
            ])
                ->where('id_beasiswa', $id)
                ->first();

            return response()->json([
                'status' => Message::OK,
                'data' => $dataBeasiswa,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() . ' ' . $e->getLine()
            ], 500);
        }
    }

    public function History(Request $request, $id)
    {
        try {
            $aktif = $request->get('aktif', 'all');
            $limit = $request->get('perPage', 10);
            $page = $request->get('page', 1);
            $offset = ($page - 1) * $limit;

            $dataBeasiswa = BeasiswaHistory::where('id_beasiswa', $id)
                ->with([
                    'Mahasiswa.pengguna:id_pengguna,nm_pengguna'
                ]);

            if ($aktif != 'all') {
                if ($aktif == 'Y') {
                    $dataBeasiswa->whereRaw(" SYSDATE BETWEEN tgl_mulai and tgl_selesai");
                    $dataBeasiswa->where("beasiswa_aktif", "1");
                } else {
                    $dataBeasiswa->whereRaw(" SYSDATE NOT BETWEEN tgl_mulai and tgl_selesai");
                    $dataBeasiswa->whereNot("beasiswa_aktif", "1");
                }
            }

            $total = $dataBeasiswa->count();

            $dataBeasiswa = $dataBeasiswa->limit($limit)
                ->offset($offset)
                ->get();

            return response()->json([
                'status' => Message::OK,
                'data' => $dataBeasiswa,
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

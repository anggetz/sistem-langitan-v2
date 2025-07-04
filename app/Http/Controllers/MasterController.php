<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasterController extends Controller
{
     public function GetRuangan(Request $request)
    {
        $request->validate([
            'id_ruangan' => [
                'nullable',
                'integer',
                Rule::exists('ruangan', 'id_ruangan')
            ]
        ]);

        $page = $request->input('page') ?? 1;
        $perPage = $request->input('per_page', 10);
        $offset = ($page - 1) * $perPage;


        $query = \App\Models\Ruangan::query();

        $total = $query->count();

        $data = $query->take($perPage)->offset($offset)->get();

        if ($request->has('id_ruangan')) {
            $query->where('id_ruangan', $request->input('id_ruangan'));
        }

        return response()->json([
            'status' => Message::OK,
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage
        ]);
    }
}

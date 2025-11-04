<?php

namespace App\Http\Controllers\Dosen\Api;

use App\Http\Controllers\Controller;
use App\Models\PublikasiJurnal;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DosenPublikasiController extends Controller
{
    public function __construct() {}

    public function Index(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 10);
            $offset = ($page - 1) * $perPage;

            $newQuery =  new PublikasiJurnal();

            $penelitian = $newQuery
                ->with('Authors')
                ->offset($offset)
                ->limit($perPage)
                ->get();

            $total = $newQuery->count();
            $penelitian = [
                'data' => $penelitian,
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
            ];

            return response()->json($penelitian, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error fetching data: ' . $e->getMessage()], 500);
        }
    }
}

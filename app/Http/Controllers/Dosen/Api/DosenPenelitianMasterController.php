<?php

namespace App\Http\Controllers\Dosen\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DosenPenelitianMasterController extends Controller
{
    public function __construct() {}

    public function GetSkim(Request $request)
    {
        $request->validate([
            'id_penelitian_skim' => [
                'nullable',
                'integer',
                Rule::exists('penelitian_skim', 'id_penelitian_skim')
            ]
        ]);

        $query = \App\Models\PenelitianSkim::query();

        if ($request->has('id_penelitian_skim')) {
            $query->where('id_penelitian_skim', $request->input('id_penelitian_skim'));
        }

        return response()->json($query->get());
    }

    public function GetBidang(Request $request)
    {
        $request->validate([
            'id_penelitian_bidang' => [
                'nullable',
                'integer',
                Rule::exists('penelitian_bidang', 'id_penelitian_bidang')
            ]
        ]);

        $query = \App\Models\PenelitianBidang::query();

        if ($request->has('id_penelitian_bidang')) {
            $query->where('id_penelitian_bidang', $request->input('id_penelitian_bidang'));
        }

        return response()->json($query->get());
    }


    public function GetBidangIlmu(Request $request)
    {
        $request->validate([
            'id_penelitian_bidang_ilmu' => [
                'nullable',
                'integer',
                Rule::exists('penelitian_bidang_ilmu', 'id_penelitian_bidang_ilmu')
            ]
        ]);

        $query = \App\Models\PenelitianBidangIlmu::query();

        if ($request->has('id_penelitian_bidang_ilmu')) {
            $query->where('id_penelitian_bidang_ilmu', $request->input('id_penelitian_bidang_ilmu'));
        }

        return response()->json($query->get());
    }

    public function GetSumberDana(Request $request)
    {
        $request->validate([
            'id_penelitian_sumber_dana' => [
                'nullable',
                'integer',
                Rule::exists('penelitian_sumber_dana', 'id_penelitian_sumber_dana')
            ]
        ]);

        $query = \App\Models\PenelitianSumberDana::query();

        if ($request->has('id_penelitian_sumber_dana')) {
            $query->where('id_penelitian_sumber_dana', $request->input('id_penelitian_sumber_dana'));
        }

        return response()->json($query->get());
    }
}
